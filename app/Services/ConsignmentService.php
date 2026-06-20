<?php

namespace App\Services;

class ConsignmentService
{
    // Returns the SQL CASE block for consignment priority.
    // ETA drives everything — status and gate/return dates are secondary.
    // Aliases default to cm (container_main), cd (container_details), da (disbursement_analysis)
    public function prioritySql(
        string $cm = 'cm',
        string $cd = 'cd',
        string $da = 'da'
    ): string {
        return "
            CASE
                WHEN {$cm}.ETA > CURDATE() THEN 3
                WHEN {$cm}.ETA = CURDATE() THEN 6
                WHEN {$cm}.ETA < CURDATE()
                    AND NOT EXISTS (
                        SELECT 1 FROM container_details {$cd}
                        WHERE {$cd}.ConsignmentID = {$cm}.ConsignmentID
                          AND {$cd}.GateOutDate IS NOT NULL
                    )
                    AND EXISTS (
                        SELECT 1 FROM disbursement_analysis {$da}
                        WHERE {$da}.BL = {$cm}.BL AND {$da}.Stamp = 'IN-HARBOR'
                    ) THEN 1
                WHEN {$cm}.ETA < CURDATE()
                    AND NOT EXISTS (
                        SELECT 1 FROM container_details {$cd}
                        WHERE {$cd}.ConsignmentID = {$cm}.ConsignmentID
                          AND {$cd}.GateOutDate IS NOT NULL
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM disbursement_analysis {$da}
                        WHERE {$da}.BL = {$cm}.BL AND {$da}.Stamp = 'IN-HARBOR'
                    ) THEN 2
                WHEN {$cm}.ETA < CURDATE()
                    AND EXISTS (
                        SELECT 1 FROM container_details {$cd}
                        WHERE {$cd}.ConsignmentID = {$cm}.ConsignmentID
                          AND {$cd}.GateOutDate IS NOT NULL
                          AND {$cd}.ReturnDate IS NULL
                    ) THEN 4
                ELSE 5
            END";
    }

    // Maps a priority integer to its label and colours.
    // Use this wherever badge rendering happens in PHP.
    public function priorityBadge(int $priority): array
    {
        return match ($priority) {
            1 => ['label' => 'Gate-Out Ready',      'class' => 'badge-green',  'color' => '#15803d', 'bg' => '#dcfce7'],
            2 => ['label' => 'Pending Disbursement', 'class' => 'badge-red',    'color' => '#b91c1c', 'bg' => '#fee2e2'],
            3 => ['label' => 'Not Arrived',          'class' => 'badge-blue',   'color' => '#1d4ed8', 'bg' => '#dbeafe'],
            4 => ['label' => 'Gated Out',            'class' => 'badge-amber',  'color' => '#92400e', 'bg' => '#fef3c7'],
            5 => ['label' => 'Awaiting Return',      'class' => 'badge-purple', 'color' => '#7e22ce', 'bg' => '#f3e8ff'],
            6 => ['label' => 'Arriving Today',       'class' => 'badge-teal',   'color' => '#0f766e', 'bg' => '#ccfbf1'],
            default => ['label' => 'Not Arrived',    'class' => 'badge-blue',   'color' => '#1d4ed8', 'bg' => '#dbeafe'],
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
                        1: { label: 'Gate-Out Ready',       bg: '#dcfce7', color: '#15803d' },
                        2: { label: 'Pending Disbursement', bg: '#fee2e2', color: '#b91c1c' },
                        3: { label: 'Not Arrived',          bg: '#dbeafe', color: '#1d4ed8' },
                        4: { label: 'Gated Out',            bg: '#fef3c7', color: '#92400e' },
                        5: { label: 'Awaiting Return',      bg: '#f3e8ff', color: '#7e22ce' },
                        6: { label: 'Arriving Today',       bg: '#ccfbf1', color: '#0f766e' },
                    };
                    return map[priority] ?? map[3];
                },
                // ETA input should be locked once ETA has passed
                etaLocked(priority) {
                    return priority !== 3 && priority !== 6;
                }
            };";
    }
}
