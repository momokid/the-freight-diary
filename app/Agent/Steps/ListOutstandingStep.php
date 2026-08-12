<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;
use Illuminate\Support\Facades\DB;

/**
 * Clients with money owing.
 *
 * Company-wide rather than branch-scoped: student_fee carries no BranchID, and
 * a debt is owed to the company rather than to a branch.
 *
 * Only authorised rows count. Dr is charged, Cr is paid — the column names are
 * inherited from an older schema and do not describe freight.
 */
class ListOutstandingStep implements AgentStep
{
    private const CAP = 20;

    public static function key(): string
    {
        return 'client.list.outstanding';
    }

    public static function label(): string
    {
        return 'List outstanding balances';
    }

    public static function permission(): ?string
    {
        return 'ClientReport';
    }

    public static function isWrite(): bool
    {
        return false;
    }

    public static function inputs(): array
    {
        return [];
    }

    public static function outputs(): array
    {
        return ['Rows', 'RowCount', 'Truncated', 'Filter'];
    }

    public function run(array $input, AgentContext $context): array
    {
        $rows = DB::table('student_fee as sf')
            ->leftJoin('consignee_main as co', 'sf.StudentID', '=', 'co.ConsigneeID')
            ->where('sf.Status', 1)
            ->groupBy('sf.StudentID', 'co.FullName')
            ->havingRaw('SUM(sf.Dr) - SUM(sf.Cr) > 0')
            ->orderByRaw('SUM(sf.Dr) - SUM(sf.Cr) DESC')
            ->get([
                'sf.StudentID as ConsigneeID',
                'co.FullName as ConsigneeName',
                DB::raw('ROUND(SUM(sf.Dr) - SUM(sf.Cr), 2) as Balance'),
                DB::raw('COUNT(DISTINCT sf.SubClassID) as Consignments'),
            ])
            ->map(fn($r) => [
                'ConsigneeName' => $r->ConsigneeName ?: 'Consignee not on file',
                'Balance'       => (float) $r->Balance,
                'Consignments'  => (int) $r->Consignments,
            ])
            ->all();

        return [
            'Rows'      => array_slice($rows, 0, self::CAP),
            'RowCount'  => count($rows),
            'Truncated' => count($rows) > self::CAP,
            'Filter'    => 'outstanding',
        ];
    }
}
