<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ConsignmentService
{
    // Consignment type — single source of truth for the LCL/FCL rule
    public const TYPE_FCL         = 'FCL';
    public const TYPE_LCL         = 'LCL';
    public const TYPE_LCL_PENDING = 'LCL_AWAITING_BREAKDOWN';

    // Evaluation order: terminal Status flags first (0=Cleared, 3=Gated Out),
    // then ETA-driven logic for everything still active.
    // Aliases default to cm (container_main), da (disbursement_analysis)
    public function prioritySql(
        string $cm = 'cm',
        string $da = 'da'
    ): string {
        return "
            CASE
                WHEN {$cm}.Status = 0 THEN 7
                WHEN {$cm}.Status = 3 THEN 4
                WHEN DATEDIFF(CURDATE(), {$cm}.ETA) < 0 THEN 3
                WHEN DATEDIFF(CURDATE(), {$cm}.ETA) = 0 THEN 6
                WHEN DATEDIFF(CURDATE(), {$cm}.ETA) > 0
                    AND EXISTS (
                        SELECT 1 FROM disbursement_analysis {$da}
                        WHERE {$da}.BL = {$cm}.BL
                    ) THEN 1
                WHEN DATEDIFF(CURDATE(), {$cm}.ETA) BETWEEN 1 AND 2
                    AND NOT EXISTS (
                        SELECT 1 FROM disbursement_analysis {$da}
                        WHERE {$da}.BL = {$cm}.BL
                    ) THEN 5
                WHEN DATEDIFF(CURDATE(), {$cm}.ETA) >= 3
                    AND NOT EXISTS (
                        SELECT 1 FROM disbursement_analysis {$da}
                        WHERE {$da}.BL = {$cm}.BL
                    ) THEN 2
                ELSE 3
            END";
    }

    // Maps a priority integer to its label and colours.
    // Use this wherever consignment-level badge rendering happens in PHP.
    public function priorityBadge(int $priority): array
    {
        return match ($priority) {
            1 => ['label' => 'Gate Out',              'class' => 'badge-green',  'color' => '#15803d', 'bg' => '#dcfce7'],
            2 => ['label' => 'Pending Disbursement',  'class' => 'badge-red',    'color' => '#b91c1c', 'bg' => '#fee2e2'],
            3 => ['label' => 'Not Arrived',            'class' => 'badge-blue',   'color' => '#1d4ed8', 'bg' => '#dbeafe'],
            4 => ['label' => 'Gated Out',              'class' => 'badge-amber',  'color' => '#92400e', 'bg' => '#fef3c7'],
            5 => ['label' => 'In-Harbor',              'class' => 'badge-purple', 'color' => '#7e22ce', 'bg' => '#f3e8ff'],
            6 => ['label' => 'Arrived',                'class' => 'badge-teal',   'color' => '#0f766e', 'bg' => '#ccfbf1'],
            7 => ['label' => 'Cleared',                'class' => 'badge-gray',   'color' => '#374151', 'bg' => '#f3f4f6'],
            default => ['label' => 'Not Arrived',      'class' => 'badge-blue',   'color' => '#1d4ed8', 'bg' => '#dbeafe'],
        };
    }

    // Container-level status labels — separate scope from consignment Priority.
    // Use this wherever an individual container_details row is rendered.
    public function containerStatusBadge(int $status): array
    {
        return match ($status) {
            4 => ['label' => 'Returned',        'color' => '#15803d', 'bg' => '#dcfce7'],
            3 => ['label' => 'Awaiting Return',  'color' => '#7e22ce', 'bg' => '#f3e8ff'],
            default => ['label' => '—',          'color' => '#6b7280', 'bg' => '#f3f4f6'],
        };
    }

    // The JS equivalent of priorityBadge — embed once in a Blade layout
    // so client-side rendering uses the same mapping.
    public function priorityBadgeJs(): string
    {
        return "
            window.ConsignmentPriority = {
                badge(priority) {
                    const map = {
                        1: { label: 'Gate Out',             bg: '#dcfce7', color: '#15803d' },
                        2: { label: 'Pending Disbursement', bg: '#fee2e2', color: '#b91c1c' },
                        3: { label: 'Not Arrived',          bg: '#dbeafe', color: '#1d4ed8' },
                        4: { label: 'Gated Out',            bg: '#fef3c7', color: '#92400e' },
                        5: { label: 'In-Harbor',             bg: '#f3e8ff', color: '#7e22ce' },
                        6: { label: 'Arrived',              bg: '#ccfbf1', color: '#0f766e' },
                        7: { label: 'Cleared',               bg: '#f3f4f6', color: '#374151' },
                    };
                    return map[priority] ?? map[3];
                },
                containerBadge(status) {
                    const map = {
                        4: { label: 'Returned',        bg: '#dcfce7', color: '#15803d' },
                        3: { label: 'Awaiting Return', bg: '#f3e8ff', color: '#7e22ce' },
                    };
                    return map[status] ?? { label: '—', bg: '#f3f4f6', color: '#6b7280' };
                },
                // ETA input should be locked once ETA has passed or consignment has progressed
                etaLocked(priority) {
                    return priority !== 3 && priority !== 6;
                }
            };";
    }

    /**
     * Resolve whether a consignment is LCL, FCL, or LCL awaiting breakdown.
     *
     * CmdtTypeID = 1 is necessary but not sufficient for LCL — the presence of
     * manifest breakdown rows is what confirms it. An LCL that has been
     * registered but not yet broken down is a real and expected state, not an
     * error: the agent flags it, the user decides whether breakdown is needed.
     *
     * This is the only place the rule should exist.
     */
    public function resolveType(int $consignmentId, string $bl): string
    {
        $cmdtTypeId = DB::table('container_main')
            ->where('ConsignmentID', $consignmentId)
            ->where('BL', $bl)
            ->value('CmdtTypeID');

        if ($cmdtTypeId === null) {
            throw new \RuntimeException("Consignment not found: {$bl}");
        }

        if ((int) $cmdtTypeId !== 1) {
            return self::TYPE_FCL;
        }

        $hasBreakdown = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $consignmentId)
            ->where('MainBL', $bl)
            ->where('Status', 1)
            ->exists();

        return $hasBreakdown ? self::TYPE_LCL : self::TYPE_LCL_PENDING;
    }

    /** Display label for a resolved type. */
    public function typeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_LCL         => 'LCL',
            self::TYPE_LCL_PENDING => 'LCL — awaiting breakdown',
            default                => 'FCL',
        };
    }
}
