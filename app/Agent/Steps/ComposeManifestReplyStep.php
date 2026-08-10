<?php

namespace App\Agent\Steps;

use App\Services\ConsignmentService;
use App\Agent\AgentContext;

/**
 * Render the house BL breakdown as a reply. Read-only and deterministic —
 * no LLM. The model is used to understand the question, never to restate
 * the answer.
 *
 * Empty is not an error, and it is not one situation. FCL has no breakdown
 * by definition; LCL with none has simply not been broken down yet. Saying
 * "no entries" for both would hide a real gap in the workflow.
 */
class ComposeManifestReplyStep implements AgentStep
{
    public static function key(): string
    {
        return 'reply.manifest';
    }

    public static function label(): string
    {
        return 'Compose manifest reply';
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
            'BL'         => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'The Main BL the reply is about.',
            ],
            'EntryCount' => [
                'type'        => 'int',
                'required'    => true,
                'description' => 'Number of house BLs found, produced by an earlier manifest read step.',
            ],
        ];
    }

    public static function outputs(): array
    {
        return ['Reply', 'ReplyFacts'];
    }

    public function run(array $input, AgentContext $context): array
    {
        $b = $context->all();

        $entries = $b['Entries'] ?? [];
        $count   = (int) ($b['EntryCount'] ?? 0);
        $type    = $b['ConsignmentType'] ?? null;

        $lines   = [];
        $lines[] = $this->headerLine($b);

        if ($count === 0) {
            $lines[] = match ($type) {
                ConsignmentService::TYPE_LCL,
                ConsignmentService::TYPE_LCL_PENDING     => 'Manifest not broken down yet — no house BLs entered.',
                ConsignmentService::TYPE_UNCONFIRMED_LCL,
                ConsignmentService::TYPE_UNCONFIRMED_FCL => 'Cargo type not confirmed — set it to know whether a breakdown is due.',
                default                                  => 'FCL — no house BL breakdown applies.',
            };
            return [
                'Reply'        => implode("\n", $lines),
                'ReplyFacts'   => $this->facts($b, 0),
            ];
        }

        $lines[] = $this->headline($b, $count);
        $lines[] = '';

        foreach ($entries as $entry) {
            $lines[] = $this->entryLine($entry);
        }

        if (! empty($b['Truncated'])) {
            $lines[] = '';
            $lines[] = 'Showing ' . count($entries) . ' of ' . $count . '.';
        }

        return [
            'Reply'        => implode("\n", $lines),
            'ReplyFacts'   => $this->facts($b, $count),
        ];
    }

    /** Totals sentence. Packages and weight are omitted when not recorded. */
    private function headline(array $b, int $count): string
    {
        $parts   = [];
        $parts[] = $count . ' house ' . $this->plural($count, 'BL');

        if (! empty($b['TotalPackages'])) {
            $parts[] = $b['TotalPackages'] . ' ' . $this->plural((int) $b['TotalPackages'], 'package');
        }

        if (! empty($b['TotalWeight'])) {
            $parts[] = $this->number($b['TotalWeight']) . ' kg';
        }

        return implode(', ', $parts);
    }

    /**
     * An LCL has no single consignee — they sit per house BL. Naming none is
     * better than naming the wrong one, so the BL stands alone.
     */
    private function headerLine(array $b): string
    {
        $bl   = $b['BL'] ?? '';
        $type = $b['ConsignmentType'] ?? null;

        $isLcl = in_array($type, [
            ConsignmentService::TYPE_LCL,
            ConsignmentService::TYPE_LCL_PENDING,
        ], true);

        if ($isLcl || empty($b['ConsigneeName'])) {
            return $bl;
        }

        return trim($b['ConsigneeName'] . ' — ' . $bl);
    }

    private function entryLine(array $entry): string
    {
        $bits   = [];
        $bits[] = $entry['HouseBL'];
        $bits[] = $entry['ConsigneeName'] ?: 'consignee not on file';

        if (! empty($entry['ItemType'])) {
            $bits[] = $entry['ItemType'];
        }

        if (! empty($entry['Packages'])) {
            $bits[] = $entry['Packages'] . ' ' . ($entry['Unit'] ?: $this->plural((int) $entry['Packages'], 'package'));
        }

        if (! empty($entry['Weight'])) {
            $bits[] = $this->number($entry['Weight']) . ' kg';
        }

        if (count($entry['Containers'] ?? []) > 1) {
            $bits[] = 'split across ' . implode(' and ', $entry['Containers']);
        }

        return '• ' . implode(' — ', $bits);
    }


    private function facts(array $b, int $count): array
    {
        $facts = [
            'BL'   => $b['BL'] ?? null,
            'Type' => $b['ConsignmentTypeLabel'] ?? null,
        ];

        if ($count === 0) {
            return array_filter($facts, fn($v) => $v !== null && $v !== '');
        }

        $facts['House BLs'] = $count;

        $lineCount = (int) ($b['LineCount'] ?? 0);

        if ($lineCount > $count) {
            $facts['Breakdown rows'] = $lineCount;
        }

        if (! empty($b['TotalPackages'])) {
            $facts['Packages'] = $b['TotalPackages'];
        }

        if (! empty($b['TotalWeight'])) {
            $facts['Weight'] = $this->number($b['TotalWeight']) . ' kg';
        }

        return array_filter($facts, fn($v) => $v !== null && $v !== '');
    }

    private function number(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2), '0'), '.');
    }

    private function plural(int $n, string $word): string
    {
        return $n === 1 ? $word : $word . 's';
    }
}
