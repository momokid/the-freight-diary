<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;

/**
 * Turn a list of rows into a reply. Read-only and deterministic — no LLM.
 *
 * The model is used to understand the question, never to restate the answer.
 * Counts and names are rendered directly from what the read step found.
 */
class ComposeListReplyStep implements AgentStep
{
    public static function key(): string
    {
        return 'reply.list';
    }

    public static function label(): string
    {
        return 'Compose list reply';
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
            'Rows' => [
                'type'        => 'array',
                'required'    => true,
                'description' => 'Rows produced by the list step.',
            ],
            'Filter' => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'Which list was produced, used to word the reply.',
            ],
        ];
    }

    public static function outputs(): array
    {
        return ['Reply', 'ReplyFacts', 'ReplyKind', 'ReplyRows', 'MoreUrl', 'MoreLabel'];
    }

    public function run(array $input, AgentContext $context): array
    {
        $rows     = $input['Rows'] ?? [];
        $filter   = $input['Filter'] ?? '';
        $total    = (int) $context->get('RowCount', count($rows));
        $shown    = count($rows);

        $lines = [$this->header($filter, $total)];

        foreach ($rows as $row) {
            $lines[] = $this->line($row);
        }

        $moreUrl = $context->get('MoreUrl');

        if ($context->get('Truncated')) {
            $lines[] = $moreUrl
                ? "Showing {$shown} of {$total}."
                : "Showing {$shown} of {$total}. Open the report for the full list.";
        }

        $facts = ['Total' => $total];

        if ($shown < $total) {
            $facts['Shown'] = $shown;
        }

        return [
            'Reply'      => implode("\n", array_filter($lines)),
            'ReplyFacts' => $facts,
            'ReplyKind'  => 'list',
            'ReplyRows'  => $this->tableRows($rows, $filter),
            'MoreUrl'    => $moreUrl,
            'MoreLabel'  => $moreUrl ? 'Open the stall monitor' : null,
        ];
    }

    /**
     * The same rows as the text reply, kept structured so the thread can put
     * them in a table. Next action is dropped for overdue — the stage is
     * already the reason the row is listed.
     */
    private function tableRows(array $rows, string $filter): array
    {

        if ($filter === 'outstanding') {
            return array_map(fn($row) => [
                'Consignee'    => $row['ConsigneeName'] ?? null,
                'Balance'      => (float) $row['Balance'],
                'Consignments' => $row['Consignments'] ?? null,
            ], $rows);
        }

        $showAction = $filter !== 'overdue';

        return array_map(fn($row) => [
            'BL'        => $row['BL'] ?? null,
            'Consignee' => $row['ConsigneeName'] ?? null,
            'Days'      => $row['Days'] ?? null,
            'Action'    => $showAction ? ($row['NextAction'] ?? null) : null,
            'ClaimedBy' => $row['ClaimedBy'] ?? null,
        ], $rows);
    }


    /** Each list gets its own sentence — not-yet-invoiced is not a problem. */
    private function header(string $filter, int $count): string
    {
        if ($count === 0) {
            return match ($filter) {
                'overdue'          => 'Nothing overdue.',
                'not_disbursed'    => 'Every arrived consignment has a disbursement raised.',
                'not_invoiced'     => 'Every disbursed consignment has been invoiced.',
                'unconfirmed_type' => 'Every consignment has its type confirmed.',
                'outstanding'      => 'No outstanding balances.',
                default            => 'Nothing to show.',
            };
        }

        if ($filter === 'outstanding') {
            return "{$count} " . $this->plural($count, 'client') . ' owing money';
        }

        $noun = $this->plural($count, 'consignment');

        return match ($filter) {
            'overdue'          => "{$count} {$noun} overdue",
            'not_disbursed'    => "{$count} arrived {$noun} with no disbursement raised",
            'not_invoiced'     => "{$count} disbursed {$noun} not yet invoiced",
            'unconfirmed_type' => "{$count} {$noun} with type not confirmed",
            default            => "{$count} {$noun}",
        };
    }

    /** BL — consignee — age, next action (whoever is on it). */
    private function line(array $row): string
    {
        if (isset($row['Balance'])) {
            return $this->balanceLine($row);
        }
        $parts = [$row['BL'] ?? '?'];

        if (! empty($row['ConsigneeName'])) {
            $parts[] = $row['ConsigneeName'];
        }

        $tail = [];

        if (isset($row['Days']) && $row['Days'] !== null) {
            $days   = (int) $row['Days'];
            $tail[] = $days . ' ' . $this->plural($days, 'day');
        }

        if (! empty($row['NextAction'])) {
            $tail[] = lcfirst($row['NextAction']);
        }

        $line = implode(' — ', $parts);

        if ($tail) {
            $line .= ' — ' . implode(', ', $tail);
        }

        if (! empty($row['ClaimedBy'])) {
            $line .= ' (' . $row['ClaimedBy'] . ')';
        }

        return $line;
    }

    /** Consignee — balance — how many consignments it spans. */
    private function balanceLine(array $row): string
    {
        $bits = [
            $row['ConsigneeName'] ?? 'Consignee not on file',
            'GH₵ ' . number_format((float) $row['Balance'], 2),
        ];

        $n = (int) ($row['Consignments'] ?? 0);

        if ($n > 0) {
            $bits[] = $n . ' ' . $this->plural($n, 'consignment');
        }

        return implode(' — ', $bits);
    }

    private function plural(int $n, string $word): string
    {
        return $n === 1 ? $word : $word . 's';
    }
}
