<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;
use App\Services\ConsignmentService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Turn whatever the user referred to into one specific consignment.
 *
 * Accepts a Main BL, a container number, or a House BL. Read-only.
 * Ambiguity is a failure in this version — the run halts and the user is
 * shown the candidates. A proper clarification loop comes later.
 */
class ResolveConsignmentStep implements AgentStep
{
    public function __construct(
        private ConsignmentService $consignments
    ) {}

    public static function key(): string
    {
        return 'consignment.resolve';
    }

    public static function label(): string
    {
        return 'Resolve consignment';
    }

    public static function permission(): ?string
    {
        return null; // read-only
    }

    public static function isWrite(): bool
    {
        return false;
    }

    public static function inputs(): array
    {
        return [
            'Reference' => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'A Main BL, House BL or container number identifying the consignment.',
            ],
        ];
    }

    public static function outputs(): array
    {
        return [
            'ConsignmentID',
            'BL',
            'ConsignmentType',
            'ConsignmentTypeLabel',
            'MatchedOn',
        ];
    }

    public function run(array $input, AgentContext $context): array
    {
        $ref = strtoupper(trim($input['Reference'] ?? ''));

        if ($ref === '') {
            throw new RuntimeException('No BL or container number was given.');
        }

        $match = $this->exactMainBl($ref)
            ?? $this->exactContainer($ref)
            ?? $this->exactHouseBl($ref)
            ?? $this->partialMainBl($ref);

        if ($match === null) {
            throw new RuntimeException("Nothing found for {$ref}.");
        }

        $type = $this->consignments->resolveType($match->ConsignmentID, $match->BL);

        return [
            'ConsignmentID'        => $match->ConsignmentID,
            'BL'                   => $match->BL,
            'ConsignmentType'      => $type,
            'ConsignmentTypeLabel' => $this->consignments->typeLabel($type),
            'MatchedOn'            => $match->MatchedOn,
        ];
    }

    // ── Lookups, most specific first ────────────────────────────────────────

    private function exactMainBl(string $ref): ?object
    {
        $row = DB::table('container_main')
            ->where('BL', $ref)
            ->where('Status', '<>', 9)
            ->first(['ConsignmentID', 'BL']);

        return $row ? $this->tag($row, 'Main BL') : null;
    }

    private function exactContainer(string $ref): ?object
    {
        $row = DB::table('container_details as cd')
            ->join('container_main as cm', function ($j) {
                $j->on('cd.ConsignmentID', '=', 'cm.ConsignmentID')
                    ->on('cd.BL', '=', 'cm.BL');
            })
            ->where('cd.ContainerNo', $ref)
            ->where('cd.Status', '<>', 9)
            ->where('cm.Status', '<>', 9)
            ->first(['cm.ConsignmentID', 'cm.BL']);

        return $row ? $this->tag($row, 'Container number') : null;
    }

    private function exactHouseBl(string $ref): ?object
    {
        $row = DB::table('manifestation_breakdown as mb')
            ->join('container_main as cm', function ($j) {
                $j->on('mb.ConsignmentID', '=', 'cm.ConsignmentID')
                    ->on('mb.MainBL', '=', 'cm.BL');
            })
            ->where('mb.HouseBL', $ref)
            ->where('mb.Status', 1)
            ->where('cm.Status', '<>', 9)
            ->first(['cm.ConsignmentID', 'cm.BL']);

        return $row ? $this->tag($row, 'House BL') : null;
    }

    /** Last resort — only accepted when it identifies exactly one consignment. */
    private function partialMainBl(string $ref): ?object
    {
        $rows = DB::table('container_main')
            ->where('BL', 'like', "%{$ref}%")
            ->where('Status', '<>', 9)
            ->orderByDesc('ConsignmentID')
            ->limit(6)
            ->get(['ConsignmentID', 'BL']);

        if ($rows->isEmpty()) {
            return null;
        }

        if ($rows->count() > 1) {
            $list = $rows->pluck('BL')->implode(', ');
            throw new RuntimeException("\"{$ref}\" matches several consignments: {$list}. Please be more specific.");
        }

        return $this->tag($rows->first(), 'Partial BL');
    }

    private function tag(object $row, string $how): object
    {
        $row->MatchedOn = $how;
        return $row;
    }
}
