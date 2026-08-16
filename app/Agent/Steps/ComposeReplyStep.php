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
    /** Above this, the header counts consignees instead of naming them. */
    private const MAX_NAMED_CONSIGNEES = 4;

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
            'BL'     => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'The Main BL the reply is about.',
            ],
            'Status' => [
                'type'        => 'int',
                'required'    => true,
                'description' => 'Stored consignment status code, produced by an earlier read step.',
            ],
        ];
    }

    public static function outputs(): array
    {
        return ['Reply', 'ReplyFacts', 'ReplyKind', 'NextAction', 'IsDelayed'];
    }

    public function run(array $input, AgentContext $context): array
    {
        $b = $context->all();   // everything earlier steps produced

        $lines = [];
        $lines[] = $this->headerLine($b);
        $lines[] = $this->statusLine($b);
        $lines[] = $b['NextAction'] ?? '';

        return [
            'Reply'      => implode("\n", array_filter($lines)),
            'ReplyFacts' => $this->facts($b),
            'ReplyKind'  => 'consignment',
        ];
    }

    /**
     * An LCL has no single consignee — they sit per house BL. Naming one would
     * be wrong, and "Unknown consignee" is misleading when there is simply
     * more than one.
     */
    private function headerLine(array $b): string
    {
        $bl      = $b['BL'] ?? '';
        $entries = $b['Entries'] ?? [];
        $count   = (int) ($b['EntryCount'] ?? 0);

        if ($count === 0) {
            return empty($b['ConsigneeName'])
                ? $bl
                : trim($b['ConsigneeName'] . ' — ' . $bl);
        }

        $names = array_values(array_filter(array_column($entries, 'ConsigneeName')));
        $label = $count . ' ' . $this->plural($count, 'consignee');

        // Only name them when every entry has one — a short list with a silent
        // gap in it reads as complete when it is not.
        if ($count <= self::MAX_NAMED_CONSIGNEES && count($names) === $count) {
            return $bl . ' — ' . $label . ': ' . implode(', ', $names);
        }

        return $bl . ' — ' . $label . ' (ask for the breakdown to list them)';
    }

    /**
     * Arrival is derived from the ETA. The stored Status is only what somebody
     * last typed and is often never updated, so it never answers the question
     * on its own — where the two disagree, the derived answer wins and the
     * stale record is noted in the facts table instead.
     */
    private function statusLine(array $b): string
    {
        $type = $this->shortType($b);
        $days = $b['DaysSinceEta'] ?? null;

        if ($days === null) {
            return "{$type} — no ETA recorded";
        }

        if (empty($b['HasArrived'])) {
            return "{$type} — due in " . abs($days) . ' ' . $this->plural(abs($days), 'day');
        }

        if (! empty($b['StatusDisagrees'])) {
            return "{$type} — arrived " . $this->ago($days);
        }

        return "{$type} — {$b['StatusLabel']}, arrived " . $this->ago($days);
    }

    /** Short form for mid-sentence use. The full phrase belongs in the facts table. */
    private function shortType(array $b): string
    {
        return match ($b['ConsignmentType'] ?? null) {
            ConsignmentService::TYPE_LCL,
            ConsignmentService::TYPE_LCL_PENDING     => 'LCL',
            ConsignmentService::TYPE_FCL             => 'FCL',
            ConsignmentService::TYPE_UNCONFIRMED_LCL,
            ConsignmentService::TYPE_UNCONFIRMED_FCL => 'Type unconfirmed',
            default                                  => 'Consignment',
        };
    }

    /** Structured pairs for the thread view to render as a table. */
    private function facts(array $b): array
    {
        $houseBls = $this->houseBlCount($b);

        $facts = [
            'BL'        => $b['BL'] ?? null,
            // On an LCL the consignees are in the header line, not here.
            'Consignee' => $houseBls > 0 ? null : ($b['ConsigneeName'] ?? null),
            'Type' => $b['ConsignmentTypeLabel'] ?? null,
            'Status' => empty($b['StatusDisagrees'])
                ? ($b['StatusLabel'] ?? null)
                : 'Arrived but not updated',
            'Carrier'   => $b['CarrierName'] ?? null,
            'Vessel'    => trim(($b['VesselName'] ?? '') . ' ' . ($b['VoyageNo'] ?? '')) ?: null,
            'ETA'       => $this->date($b['ETA'] ?? null),
            'Registered' => $this->date($b['RegisteredOn'] ?? null),

        ];

        if (($b['ContainerCount'] ?? 0) > 0) {
            $facts['Containers'] = $b['ContainerCount'] .
                ' (' . $b['GatedOutCount'] . ' gated out, ' . $b['ReturnedCount'] . ' returned)';
        }

        if ($houseBls > 0) {
            $facts['House BLs'] = $houseBls;
        }

        if (! empty($b['DisbursementStamps'])) {
            $facts['Disbursement'] = implode(', ', $b['DisbursementStamps']);
        }



        return array_filter($facts, fn($v) => $v !== null && $v !== '');
    }

    /**
     * Distinct house BLs. EntryCount comes from manifest.read and is already
     * grouped; BreakdownCount counts table rows, which runs higher whenever an
     * entry is split across containers. Prefer the former where present.
     */
    private function houseBlCount(array $b): int
    {
        return (int) ($b['EntryCount'] ?? $b['BreakdownCount'] ?? 0);
    }

    private function ago(?int $days): string
    {
        if ($days === null) return 'recently';
        if ($days === 0)    return 'today';
        return $days . ' ' . $this->plural($days, 'day') . ' ago';
    }

    /** Stored dates are ISO. Nobody reads them that way. */
    private function date(?string $value): ?string
    {
        if (empty($value) || str_starts_with($value, '0000')) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('j M Y');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function plural(int $n, string $word): string
    {
        return $n === 1 ? $word : $word . 's';
    }
}
