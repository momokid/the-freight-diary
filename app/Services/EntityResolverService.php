<?php

namespace App\Services;

use App\Models\UserAuth;
use Illuminate\Support\Facades\DB;

class EntityResolverService
{
    /** Rows returned per group. */
    private const LIMIT = 5;

    private const GATES = [
        'bl'          => 'EditData',
        'hbl'         => 'EditData',
        'container'   => 'EditData',
        'receipt'     => 'EditData',
        'consignee'   => null,
        'declaration' => null,
    ];

    public function resolve(string $q, UserAuth $userAuth): array
    {
        $shape  = $this->classify($q);
        $groups = [];

        foreach (array_keys(self::GATES) as $key) {
            if (! $this->allowed($key, $userAuth)) {
                continue;
            }

            if (! in_array($key, $shape, true)) {
                continue;
            }

            $group = $this->{'resolve' . ucfirst($key)}($q);

            if ($group !== null && count($group['items']) > 0) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    // ── Permission + input shape ────────────────────────────────────────────

    private function allowed(string $key, UserAuth $userAuth): bool
    {
        $permission = self::GATES[$key];

        return $permission === null || $userAuth->hasPermission($permission);
    }

    /**
     * Decide which groups the input could plausibly match, so a typical keystroke fires two queries instead of six.
     */
    private function classify(string $q): array
    {
        // Contains a space — only names have spaces
        if (str_contains($q, ' ')) {
            return ['consignee'];
        }

        // Container/BL format: 4 letters then digits (MSCU4421889)
        if (preg_match('/^[A-Z]{4}\d+$/', $q)) {
            return ['bl', 'hbl', 'container'];
        }

        // Digits only — could be any reference number
        if (preg_match('/^\d+$/', $q)) {
            return ['bl', 'hbl', 'container', 'receipt', 'declaration'];
        }

        // Letters only — a name or a carrier prefix
        if (preg_match('/^[A-Z]+$/', $q)) {
            return ['bl', 'hbl', 'container', 'consignee'];
        }

        // Mixed — try everything
        return array_keys(self::GATES);
    }

    // ── Resolvers ───────────────────────────────────────────────────────────

    /** Main BL — mirrors EditConsignmentController::searchBL, plus Status <> 9. */
    private function resolveBl(string $q): array
    {
        $rows = DB::table('container_main as cm')
            ->join('shipper_main as s', 'cm.ShipperID', '=', 's.ShipperID')
            ->where('cm.BL', 'like', "%{$q}%")
            ->where('cm.Status', '<>', 9)
            ->orderByDesc('cm.ConsignmentID')
            ->limit(self::LIMIT)
            ->get(['cm.ConsignmentID', 'cm.BL', 'cm.VesselName', 'cm.Status', 's.ShipperName']);

        return [
            'key'   => 'bl',
            'label' => 'Consignment',
            'icon'  => 'box',
            'items' => $rows->map(fn($r) => [
                'title' => $r->BL,
                'meta'  => trim($this->statusLabel($r->Status) . ' — ' . ($r->ShipperName ?: 'No shipper') .
                    ($r->VesselName ? ' — ' . $r->VesselName : '')),
                'mono'  => true,
                'url'   => route('edit-data.consignment.index', ['bl' => $r->BL]),
            ])->all(),
        ];
    }

    /** House BL + consignee name — mirrors EditConsignmentController::searchHBL. */
    private function resolveHbl(string $q): array
    {
        $rows = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->where(function ($query) use ($q) {
                $query->where('mb.HouseBL', 'like', "%{$q}%")
                    ->orWhere('c.FullName', 'like', "%{$q}%");
            })
            ->where('mb.Status', 1)
            ->orderByDesc('mb.ConsignmentID')
            ->limit(self::LIMIT)
            ->get(['mb.MainBL', 'mb.HouseBL', 'mb.ItemType', 'c.FullName']);

        return [
            'key'   => 'hbl',
            'label' => 'House BL',
            'icon'  => 'doc',
            'items' => $rows->map(fn($r) => [
                'title' => $r->HouseBL,
                'meta'  => $r->FullName . ($r->ItemType ? ' — ' . $r->ItemType : ''),
                'mono'  => true,
                'url'   => route('edit-data.consignment.index', [
                    'bl'  => $r->MainBL,
                    'hbl' => $r->HouseBL,
                ]),
            ])->all(),
        ];
    }

    /** Container number. */
    private function resolveContainer(string $q): array
    {
        $rows = DB::table('container_details as cd')
            ->join('container_main as cm', 'cd.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('cd.ContainerNo', 'like', "%{$q}%")
            ->where('cd.Status', '<>', 9)
            ->where('cm.Status', '<>', 9)
            ->orderByDesc('cd.ConsignmentID')
            ->limit(self::LIMIT)
            ->get(['cd.ContainerNo', 'cm.BL', 'cm.Status']);

        return [
            'key'   => 'container',
            'label' => 'Container',
            'icon'  => 'box',
            'items' => $rows->map(fn($r) => [
                'title' => $r->ContainerNo,
                'meta'  => 'BL ' . $r->BL . ' — ' . $this->statusLabel($r->Status),
                'mono'  => true,
                'url'   => route('edit-data.consignment.index', ['bl' => $r->BL]),
            ])->all(),
        ];
    }

    /** Consignee name. */
    private function resolveConsignee(string $q): array
    {
        $rows = DB::table('consignee_main')
            ->where('FullName', 'like', "%{$q}%")
            ->orderBy('FullName')
            ->limit(self::LIMIT)
            ->get(['ConsigneeID', 'FullName']);

        return [
            'key'   => 'consignee',
            'label' => 'Consignee',
            'icon'  => 'user',
            'items' => $rows->map(fn($r) => [
                'title' => $r->FullName,
                'meta'  => 'Consignee profile',
                'mono'  => false,
                'url'   => route('master-data.consignees.show', $r->ConsigneeID),
            ])->all(),
        ];
    }

    /** Receipt number. */
    private function resolveReceipt(string $q): array
    {
        $rows = DB::table('receipt_main')
            ->where('ReceiptNo', 'like', "%{$q}%")
            ->orderByDesc('Date')
            ->limit(self::LIMIT)
            ->get(['ReceiptNo', 'Date']);

        return [
            'key'   => 'receipt',
            'label' => 'Receipt',
            'icon'  => 'receipt',
            'items' => $rows->map(fn($r) => [
                'title' => $r->ReceiptNo,
                'meta'  => $r->Date ? date('d M Y', strtotime($r->Date)) : '',
                'mono'  => true,
                'url'   => route('edit-data.reverse-transaction.index', ['receipt' => $r->ReceiptNo]),
            ])->all(),
        ];
    }

    /** Declaration number — report route keys on ReceiptNo, not DeclarationNo. */
    private function resolveDeclaration(string $q): array
    {
        $rows = DB::table('declaration_main')
            ->where('DeclarationNo', 'like', "%{$q}%")
            ->where('Status', 1)
            ->orderByDesc('DeclarationID')
            ->limit(self::LIMIT)
            ->get(['DeclarationNo', 'BL', 'ReceiptNo', 'Date']);

        return [
            'key'   => 'declaration',
            'label' => 'Declaration',
            'icon'  => 'doc',
            'items' => $rows->filter(fn($r) => ! empty($r->ReceiptNo))->map(fn($r) => [
                'title' => $r->DeclarationNo,
                'meta'  => 'BL ' . $r->BL . ($r->Date ? ' — ' . date('d M Y', strtotime($r->Date)) : ''),
                'mono'  => true,
                'url'   => route('declaration.report', $r->ReceiptNo),
            ])->values()->all(),
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function statusLabel($status): string
    {
        return match ((int) $status) {
            0 => 'Cleared',
            1 => 'Not Arrived',
            2 => 'Pending',
            3 => 'Gated Out',
            default => 'Unknown',
        };
    }
}
