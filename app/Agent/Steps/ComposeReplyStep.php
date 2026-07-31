<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;
use App\Services\ConsignmentService;

/**
 * Turn what earlier steps found into a reply. Read-only, and deliberately
 * deterministic — no LLM.
 *
 * The model is used to understand the question, never to restate the answer.
 * Facts read from the database are rendered directly, so the agent cannot
 * misreport a status, a date or a count.
 */
class ComposeReplyStep implements AgentStep
{
    public static function key(): string
    {
        return 'reply.compose';
    }

    public static function label(): string
    {
        return 'Compose reply';
    }

    public static function permission(): ?string
    {
        return null;
    }

    public static function isWrite(): bool
    {
        return false;
    }

    public static function inputs(): array
    {
        return [
            'BL'     => ['type' => 'string', 'required' => true],
            'Status' => ['type' => 'int',    'required' => true],
        ];
    }

    public static function outputs(): array
    {
        return ['Reply', 'ReplyFacts', 'NextAction', 'IsDelayed'];
    }

    public function run(array $input, AgentContext $context): array
    {
        $b = $context->all();   // everything earlier steps produced

        $next      = $this->nextAction($b);
        $facts     = $this->facts($b);
        $isDelayed = $this->isDelayed($b);

        $lines = [];
        $lines[] = trim(($b['ConsigneeName'] ?? 'Unknown consignee') . ' — ' . $b['BL']);
        $lines[] = $this->statusLine($b);
        $lines[] = $next;

        return [
            'Reply'      => implode("\n", array_filter($lines)),
            'ReplyFacts' => $facts,
            'NextAction' => $next,
            'IsDelayed'  => $isDelayed,
        ];
    }

    /** Type, status and timing on one line, so they cannot contradict each other. */
    private function statusLine(array $b): string
    {
        $type = $b['ConsignmentTypeLabel'] ?? '';
        $days = $b['DaysSinceEta'] ?? null;

        if (! empty($b['StatusDisagrees'])) {
            return "{$type} — ETA passed {$days} " . $this->plural($days, 'day') .
                ' ago, status not yet updated from Not Arrived';
        }

        if (empty($b['HasArrived'])) {
            return $days === null
                ? "{$type} — no ETA recorded"
                : "{$type} — due in " . abs($days) . ' ' . $this->plural(abs($days), 'day');
        }

        return "{$type} — {$b['StatusLabel']}, arrived " . $this->ago($days);
    }

    /**
     * Where the consignment sits in the workflow, and what is owed next.
     * Mirrors: register → ETA → arrive → manifest (LCL) → disburse →
     * gate out → return.
     */
    private function nextAction(array $b): string
    {
        $days       = $b['DaysSinceEta'] ?? null;
        $arrived    = $b['HasArrived'] ?? false;
        $type       = $b['ConsignmentType'] ?? null;
        $breakdown  = $b['BreakdownCount'] ?? 0;
        $stamps     = $b['DisbursementStamps'] ?? [];
        $containers = $b['ContainerCount'] ?? 0;
        $gatedOut   = $b['GatedOutCount'] ?? 0;
        $returned   = $b['ReturnedCount'] ?? 0;

        if (! $arrived) {
            return $days === null
                ? 'No ETA recorded — set one so arrival can be tracked.'
                : 'Awaiting arrival — ETA in ' . abs($days) . ' ' . $this->plural(abs($days), 'day') . '.';
        }

        if ($type === ConsignmentService::TYPE_LCL_PENDING) {
            return 'Manifest breakdown not yet done.';
        }

        if (empty($stamps)) {
            return ($breakdown > 0 ? "{$breakdown} house " . $this->plural($breakdown, 'BL') . ' manifested — ' : '') .
                'no disbursement raised yet.';
        }

        if ($containers > 0 && $gatedOut < $containers) {
            $waiting = $containers - $gatedOut;
            return 'Disbursement raised (' . implode(', ', $stamps) . ') — ' .
                $waiting . ' of ' . $containers . ' ' . $this->plural($containers, 'container') . ' still to gate out.';
        }

        if ($containers > 0 && $returned < $containers) {
            $out = $containers - $returned;
            return $out . ' of ' . $containers . ' ' . $this->plural($containers, 'container') .
                ' gated out and not yet returned.';
        }

        return 'All containers gated out and returned — nothing outstanding.';
    }

    /** Flag anything sitting longer than the configured tolerance. */
    private function isDelayed(array $b): bool
    {
        if (empty($b['HasArrived'])) {
            return false;
        }

        $days = $b['DaysSinceEta'] ?? 0;
        $t    = config('agent.thresholds');

        if (($b['ConsignmentType'] ?? null) === ConsignmentService::TYPE_LCL_PENDING) {
            return $days > $t['arrival_to_manifest'];
        }

        if (empty($b['DisbursementStamps'])) {
            return $days > ($t['arrival_to_manifest'] + $t['manifest_to_disbursement']);
        }

        $containers = $b['ContainerCount'] ?? 0;

        if ($containers > 0 && ($b['GatedOutCount'] ?? 0) < $containers) {
            return $days > ($t['arrival_to_manifest'] + $t['manifest_to_disbursement'] + $t['disbursement_to_gateout']);
        }

        if ($containers > 0 && ($b['ReturnedCount'] ?? 0) < $containers) {
            return $days > array_sum($t);
        }

        return false;
    }

    /** Structured pairs for the thread view to render as a table. */
    private function facts(array $b): array
    {
        $facts = [
            'BL'        => $b['BL'] ?? null,
            'Consignee' => $b['ConsigneeName'] ?? null,
            'Type'      => $b['ConsignmentTypeLabel'] ?? null,
            'Status'    => $b['StatusLabel'] ?? null,
            'Carrier'   => $b['CarrierName'] ?? null,
            'Vessel'    => trim(($b['VesselName'] ?? '') . ' ' . ($b['VoyageNo'] ?? '')) ?: null,
            'ETA'       => $b['ETA'] ?? null,
        ];

        if (($b['ContainerCount'] ?? 0) > 0) {
            $facts['Containers'] = $b['ContainerCount'] .
                ' (' . $b['GatedOutCount'] . ' gated out, ' . $b['ReturnedCount'] . ' returned)';
        }

        if (($b['BreakdownCount'] ?? 0) > 0) {
            $facts['House BLs'] = $b['BreakdownCount'];
        }

        if (! empty($b['DisbursementStamps'])) {
            $facts['Disbursement'] = implode(', ', $b['DisbursementStamps']);
        }

        if (! empty($b['MatchedOn'])) {
            $facts['Matched on'] = $b['MatchedOn'];
        }

        return array_filter($facts, fn($v) => $v !== null && $v !== '');
    }

    private function ago(?int $days): string
    {
        if ($days === null) return 'recently';
        if ($days === 0)    return 'today';
        return $days . ' ' . $this->plural($days, 'day') . ' ago';
    }

    private function plural(int $n, string $word): string
    {
        return $n === 1 ? $word : $word . 's';
    }
}
