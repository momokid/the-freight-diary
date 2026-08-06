<?php

namespace App\Services;

use App\Models\ManifestBreakdown;
use App\Models\ManifestTemp;
use Illuminate\Support\Facades\DB;

class ManifestValidator
{
    private const ITEM_TYPES = ['GOODS', 'VEHICLE', 'MOTORBIKE'];
    private const UNITS      = ['LOT', 'PLT', 'PKG', 'UNIT'];

    public function validateBatch(
        array $rows,
        int $consignmentId,
        string $mainBl,
        string $username
    ): array {

        $mainBl = strtoupper(trim($mainBl));

        $totalWeight  = $this->containerWeight($consignmentId);
        $stagedWeight = (float) ManifestTemp::where('Username', $username)
            ->where('MainBL', $mainBl)
            ->sum('Weight');

        $remaining = round($totalWeight - $stagedWeight, 3);

        $seenHbl    = [];
        $batchTotal = 0.0;
        $checked    = [];
        $valid      = true;

        foreach ($rows as $i => $row) {
            $errors = [];

            $hbl         = strtoupper(trim((string) ($row['HouseBL'] ?? '')));
            $containerNo = strtoupper(trim((string) ($row['ContainerNo'] ?? '')));
            $description = trim((string) ($row['Description'] ?? ''));
            $itemType    = strtoupper(trim((string) ($row['ItemType'] ?? '')));
            $unit        = strtoupper(trim((string) ($row['Unit'] ?? '')));
            $vin         = strtoupper(trim((string) ($row['VIN'] ?? '')));
            $otherInfo   = trim((string) ($row['OtherInfo'] ?? ''));
            $weight      = $row['Weight'] ?? null;
            $package     = $row['Package'] ?? null;
            $consignee   = $row['CosigneeID'] ?? null;
            $notify      = $row['Cosignee2_ID'] ?? $consignee;

            // ── Required fields ──
            if ($hbl === '')         $errors['HouseBL']     = 'House BL is required.';
            if ($containerNo === '') $errors['ContainerNo'] = 'Container is required.';
            if ($description === '') $errors['Description'] = 'Description is required.';

            if (! in_array($itemType, self::ITEM_TYPES, true)) {
                $errors['ItemType'] = 'Select an item type.';
            }

            if (! in_array($unit, self::UNITS, true)) {
                $errors['Unit'] = 'Select a unit.';
            }

            if (! $this->consigneeExists($consignee)) {
                $errors['CosigneeID'] = 'Select a consignee.';
            }

            if (! $this->consigneeExists($notify)) {
                $errors['Cosignee2_ID'] = 'Select a notify party.';
            }

            // ── Weight ──
            if (! is_numeric($weight) || (float) $weight < 0.001) {
                $errors['Weight'] = 'Weight is required.';
            } else {
                $batchTotal += (float) $weight;
            }

            // ── Package ──
            if (! is_numeric($package) || (int) $package < 1) {
                $errors['Package'] = 'Package must be at least 1.';
            } elseif ((int) $package > 1 && $otherInfo === '' && $itemType !== 'MOTORBIKE') {
                $errors['OtherInfo'] = 'Other information is required when package is more than 1.';
            }

            // ── VIN ──
            if ($itemType === 'GOODS' && $vin !== '') {
                $errors['VIN'] = 'VIN is only for VEHICLE item type.';
            }

            if ($itemType === 'VEHICLE' && $vin === '') {
                $errors['VIN'] = 'VIN is required for VEHICLE item type.';
            }

            // ── House BL uniqueness ──
            if ($hbl !== '') {
                if (isset($seenHbl[$hbl])) {
                    $errors['HouseBL'] = 'Duplicated in this batch.';
                } elseif (ManifestTemp::where('HouseBL', $hbl)->exists()) {
                    $errors['HouseBL'] = 'Already staged.';
                } elseif (ManifestBreakdown::where('HouseBL', $hbl)->exists()) {
                    $errors['HouseBL'] = 'Already registered.';
                }

                $seenHbl[$hbl] = true;
            }

            if ($errors) {
                $valid = false;
            }

            $checked[] = [
                'index'  => $i,
                'row'    => [
                    'HouseBL'      => $hbl,
                    'ContainerNo'  => $containerNo,
                    'CosigneeID'   => $consignee,
                    'Cosignee2_ID' => $notify,
                    'Description'  => $description,
                    'ItemType'     => $itemType,
                    'VIN'          => $vin,
                    'OtherInfo'    => $otherInfo,
                    'Weight'       => is_numeric($weight) ? (float) $weight : null,
                    'Package'      => is_numeric($package) ? (int) $package : null,
                    'Unit'         => $unit,
                ],
                'errors' => $errors,
            ];
        }

        // ── Batch weight against remaining capacity ──
        $batchTotal = round($batchTotal, 3);
        $overBy     = round($batchTotal - $remaining, 3);

        if ($overBy > 0) {
            $valid = false;
        }

        return [
            'valid'   => $valid,
            'rows'    => $checked,
            'summary' => [
                'total'      => round($totalWeight, 3),
                'staged'     => round($stagedWeight, 3),
                'remaining'  => $remaining,
                'batch'      => $batchTotal,
                'overBy'     => $overBy > 0 ? $overBy : 0,
                'message'    => $overBy > 0
                    ? 'Total weight exceeds remaining capacity by ' . number_format($overBy, 3) . ' KG.'
                    : null,
            ],
        ];
    }

    /** Container capacity for this consignment. */
    public function containerWeight(int $consignmentId): float
    {
        return (float) DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->where('Status', 1)
            ->sum('Weight');
    }

    private function consigneeExists($id): bool
    {
        if (! is_numeric($id) || (int) $id < 1) {
            return false;
        }

        return DB::table('consignee_main')
            ->where('ConsigneeID', (int) $id)
            ->exists();
    }
}
