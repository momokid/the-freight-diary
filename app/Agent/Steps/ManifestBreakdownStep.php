<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;
use Illuminate\Support\Facades\DB;


class ManifestBreakdownStep implements AgentStep
{
    private const MAX_ENTRIES = 40;

    public static function key(): string
    {
        return 'manifest.read';
    }

    public static function label(): string
    {
        return 'Read manifest breakdown';
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
            'ConsignmentID' => [
                'type'        => 'int',
                'required'    => true,
                'description' => 'Internal consignment identifier, normally resolved from a reference by an earlier step.',
            ],
            'BL'            => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'The Main BL whose house BL breakdown should be read.',
            ],
        ];
    }

    public static function outputs(): array
    {
        return [
            'Entries',
            'EntryCount',
            'LineCount',
            'TotalPackages',
            'TotalWeight',
            'IsBrokenDown',
            'Truncated',
        ];
    }

    public function run(array $input, AgentContext $context): array
    {
        $id = (int) $input['ConsignmentID'];
        $bl = (string) $input['BL'];

        $rows = DB::table('manifestation_breakdown as mb')
            ->leftJoin('consignee_main as co', 'mb.ConsigneeID', '=', 'co.ConsigneeID')
            ->where('mb.ConsignmentID', $id)
            ->where('mb.MainBL', $bl)
            ->where('mb.Status', 1)
            ->orderBy('mb.HouseBL')
            ->get([
                'mb.HouseBL',
                'mb.ContainerNo',
                'mb.ItemType',
                'mb.Package',
                'mb.Unit',
                'mb.Weight',
                'co.FullName as ConsigneeName',
            ]);

        $entries = [];

        foreach ($rows as $row) {
            $key = $row->HouseBL;

            if (! isset($entries[$key])) {
                $entries[$key] = [
                    'HouseBL'       => $row->HouseBL,
                    'ConsigneeName' => $row->ConsigneeName,
                    'ItemType'      => $row->ItemType,
                    'Packages'      => 0,
                    'Unit'          => $row->Unit,
                    'Weight'        => 0.0,
                    'Containers'    => [],
                ];
            }

            $entries[$key]['Packages'] += (int) $row->Package;
            $entries[$key]['Weight']   += (float) $row->Weight;

            if ($row->ContainerNo !== '' && ! in_array($row->ContainerNo, $entries[$key]['Containers'], true)) {
                $entries[$key]['Containers'][] = $row->ContainerNo;
            }
        }

        $entries   = array_values($entries);
        $truncated = count($entries) > self::MAX_ENTRIES;

        return [
            'Entries'       => $truncated ? array_slice($entries, 0, self::MAX_ENTRIES) : $entries,
            'EntryCount'    => count($entries),
            'LineCount'     => $rows->count(),
            'TotalPackages' => array_sum(array_column($entries, 'Packages')),
            'TotalWeight'   => round(array_sum(array_column($entries, 'Weight')), 2),
            'IsBrokenDown'  => count($entries) > 0,
            'Truncated'     => $truncated,
        ];
    }
}
