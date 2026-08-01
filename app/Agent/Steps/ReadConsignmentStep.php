<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Load the current state of one consignment. Read-only.
 *
 * Arrival is derived, not recorded: an ETA in the past that nobody has moved
 * means the vessel has landed. Where that disagrees with the stored Status,
 * both are returned so the reply can say so rather than silently picking one.
 */
class ReadConsignmentStep implements AgentStep
{
    public static function key(): string
    {
        return 'consignment.read';
    }

    public static function label(): string
    {
        return 'Read consignment detail';
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
            'ConsignmentID' => ['type' => 'int',    'required' => true],
            'BL'            => ['type' => 'string', 'required' => true],
        ];
    }

    public static function outputs(): array
    {
        return [
            'Status',
            'StatusLabel',
            'RegisteredOn',
            'ETA',
            'DaysSinceEta',
            'HasArrived',
            'StatusDisagrees',
            'VesselName',
            'VoyageNo',
            'ConsigneeName',
            'CarrierName',
            'ContainerCount',
            'GatedOutCount',
            'ReturnedCount',
            'BreakdownCount',
            'DisbursementStamps',
        ];
    }

    public function run(array $input, AgentContext $context): array
    {
        $id = (int) $input['ConsignmentID'];
        $bl = (string) $input['BL'];

        $row = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->where('cm.ConsignmentID', $id)
            ->where('cm.BL', $bl)
            ->first([
                'cm.Status',
                'cm.ETA',
                'cm.Date',
                'cm.VesselName',
                'cm.VoyageNo',
                'co.FullName as ConsigneeName',
                'sc.CarrierName',
            ]);

        if (! $row) {
            throw new RuntimeException("Consignment not found: {$bl}");
        }

        // ── Containers ──
        $containers = DB::table('container_details')
            ->where('ConsignmentID', $id)
            ->where('BL', $bl)
            ->where('Status', '<>', 9)
            ->get(['GateOutDate', 'ReturnDate']);

        // ── Manifest ──
        $breakdownCount = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $id)
            ->where('MainBL', $bl)
            ->where('Status', 1)
            ->count();

        // ── Disbursement — which stages have been raised ──
        $stamps = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('Status', '<>', 9)
            ->distinct()
            ->pluck('Stamp')
            ->filter()
            ->values()
            ->all();

        // ── Derived arrival ──
        $eta          = $row->ETA ? Carbon::parse($row->ETA)->startOfDay() : null;
        $today        = Carbon::now()->startOfDay();
        $daysSinceEta = $eta ? (int) round($today->diffInDays($eta, false) * -1) : null;
        $hasArrived   = $eta ? $eta->lessThanOrEqualTo($today) : false;

        // Stored status still says Not Arrived, but the ETA has passed
        $statusDisagrees = ($hasArrived && (int) $row->Status === 1);

        return [
            'Status'             => (int) $row->Status,
            'StatusLabel'        => $this->statusLabel((int) $row->Status),
            'RegisteredOn'       => $row->Date,
            'ETA'                => $row->ETA,
            'DaysSinceEta'       => $daysSinceEta,
            'HasArrived'         => $hasArrived,
            'StatusDisagrees'    => $statusDisagrees,
            'VesselName'         => $row->VesselName ?: null,
            'VoyageNo'           => $row->VoyageNo ?: null,
            'ConsigneeName'      => $row->ConsigneeName ?: null,
            'CarrierName'        => $row->CarrierName ?: null,
            'ContainerCount'     => $containers->count(),
            'GatedOutCount'      => $containers->whereNotNull('GateOutDate')->count(),
            'ReturnedCount'      => $containers->whereNotNull('ReturnDate')->count(),
            'BreakdownCount'     => $breakdownCount,
            'DisbursementStamps' => $stamps,
        ];
    }

    private function statusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Cleared',
            1 => 'Not Arrived',
            2 => 'Pending',
            3 => 'Gated Out',
            9 => 'Deleted',
            default => 'Unknown',
        };
    }
}
