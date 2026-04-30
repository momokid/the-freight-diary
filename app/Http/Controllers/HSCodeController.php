<?php

namespace App\Http\Controllers;

use App\Services\HSCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HSCodeController extends Controller
{
    public function __construct(private HSCodeService $hsCodeService) {}

    // ════════════════════════════════════════════════════════════════════════
    // PREDICT — AJAX endpoint
    // Called from the manifest form (LCL) and container details form (FCL)
    // POST /hs-code/predict
    // ════════════════════════════════════════════════════════════════════════

    public function predict(Request $request)
    {
        $request->validate([
            'description' => ['required', 'string', 'min:3', 'max:500'],
            'item_type' => ['nullable', 'string', 'max:100'],
            'consignment_id' => ['nullable', 'integer'],
            'bl' => ['nullable', 'string', 'max:50'],
            'house_bl' => ['nullable', 'string', 'max:50'],
            'source_type' => ['nullable', 'in:FCL,LCL'],
            'cif_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $candidates = $this->hsCodeService->predict(
            $request->description,
            $request->item_type
        );

        if (empty($candidates)) {
            return response()->json([
                'success' => false,
                'message' => 'No matching HS codes found. Try a more specific description.',
                'candidates' => [],
                'debug' => app()->environment('local') ? [
                    'description' => $request->description,
                    'hs_count' => DB::table('hs_codes')->where('IsActive', 1)->count(),
                ] : null,
            ]);
        }

        // If CIF value provided, calculate duty for each candidate
        if ($request->filled('cif_value') && $request->cif_value > 0) {
            foreach ($candidates as &$c) {
                $c['DutyBreakdown'] = $this->hsCodeService->calculateDuty(
                    $c['HSCode'],
                    (float) $request->cif_value
                );
            }
            unset($c);
        }

        return response()->json([
            'success' => true,
            'candidates' => $candidates,
            'description' => $request->description,
            'source' => $candidates[0]['Source'] ?? 'rules',
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // ACCEPT — Save the accepted HS code + log to duty_predictions
    // POST /hs-code/accept
    // ════════════════════════════════════════════════════════════════════════

    public function accept(Request $request)
    {
        $request->validate([
            'hs_code' => ['required', 'string', 'exists:hs_codes,HSCode'],
            'source_type' => ['required', 'in:FCL,LCL'],
            'consignment_id' => ['required', 'integer'],
            'bl' => ['required', 'string', 'max:50'],
            'house_bl' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:500'],
            'predicted_hs_code' => ['nullable', 'string'],
            'all_candidates' => ['nullable', 'array'],
            'was_recommended' => ['nullable', 'boolean'],
            'container_no' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $hsRecord = DB::table('hs_codes')
            ->where('HSCode', $request->hs_code)
            ->first();

        if (! $hsRecord) {
            return response()->json([
                'success' => false,
                'message' => 'HS Code not found.',
            ], 404);
        }

        DB::beginTransaction();

        try {
            // ── Update the source record ──────────────────────────────────
            if ($request->source_type === 'LCL') {
                DB::table('manifestation_breakdown')
                    ->where('ConsignmentID', $request->consignment_id)
                    ->where('HouseBL', $request->house_bl)
                    ->update([
                        'HSCode' => $hsRecord->HSCode,
                        'HSDescription' => $hsRecord->HeadingDesc,
                        'EstimatedDutyRate' => $hsRecord->ImportDutyRate,
                        'HSRecommendedBy' => $user->ID,
                    ]);
            } else {
                DB::table('container_details')
                    ->where('ConsignmentID', $request->consignment_id)
                    ->where('ContainerNo', $request->container_no)
                    ->update([
                        'HSCode' => $hsRecord->HSCode,
                        'HSDescription' => $hsRecord->HeadingDesc,
                        'EstimatedDutyRate' => $hsRecord->ImportDutyRate,
                        'HSRecommendedBy' => $user->ID,
                    ]);
            }

            // ── Log to duty_predictions ───────────────────────────────────
            $predictedCode = $request->predicted_hs_code ?? $hsRecord->HSCode;
            $predictedHs = DB::table('hs_codes')
                ->where('HSCode', $predictedCode)
                ->first();

            DB::table('duty_predictions')->insert([
                'SourceType' => $request->source_type,
                'ConsignmentID' => $request->consignment_id,
                'BL' => strtoupper($request->bl),
                'HouseBL' => $request->house_bl
                    ? strtoupper($request->house_bl) : null,
                'ItemDescription' => $request->description,
                'PredictedHSCode' => $predictedCode,
                'PredictedHSDesc' => $predictedHs?->HeadingDesc ?? '—',
                'PredictedDutyRate' => $predictedHs?->ImportDutyRate ?? 0,
                'Confidence' => 0,
                'PredictionSource' => 'system',
                'AllCandidates' => $request->all_candidates
                    ? json_encode($request->all_candidates) : null,
                'AcceptedHSCode' => $hsRecord->HSCode,
                'AcceptedHSDesc' => $hsRecord->HeadingDesc,
                'AcceptedDutyRate' => $hsRecord->ImportDutyRate,
                'WasPredictionAccepted' => $request->was_recommended ?? false,
                'AcceptedBy' => $user->ID,
                'AcceptedAt' => now(),
                'Username' => $user->ID,
                'BranchID' => $user->BranchID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'HS Code saved successfully.',
                'hs_code' => $hsRecord->HSCode,
                'description' => $hsRecord->HeadingDesc,
                'duty_rate' => $hsRecord->ImportDutyRate,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save HS Code. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // CALCULATE DUTY — AJAX
    // POST /hs-code/calculate-duty
    // ════════════════════════════════════════════════════════════════════════

    public function calculateDuty(Request $request)
    {
        $request->validate([
            'hs_code' => ['required', 'string', 'exists:hs_codes,HSCode'],
            'cif_value' => ['required', 'numeric', 'min:0'],
        ]);

        $breakdown = $this->hsCodeService->calculateDuty(
            $request->hs_code,
            (float) $request->cif_value
        );

        if (empty($breakdown)) {
            return response()->json([
                'success' => false,
                'message' => 'HS Code not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'breakdown' => $breakdown,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // SEARCH — AJAX typeahead for HS code lookup
    // GET /hs-code/search?q=vehicles
    // ════════════════════════════════════════════════════════════════════════

    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $q = $request->q;

        $results = DB::table('hs_codes')
            ->where('IsActive', 1)
            ->where(function ($query) use ($q) {
                $query->where('HSCode', 'like', $q.'%')
                    ->orWhere('HeadingDesc', 'like', '%'.$q.'%')
                    ->orWhere('Keywords', 'like', '%'.$q.'%')
                    ->orWhere('ChapterDesc', 'like', '%'.$q.'%');
            })
            ->orderByRaw('CASE WHEN HSCode LIKE ? THEN 0 ELSE 1 END', [$q.'%'])
            ->orderBy('ImportDutyRate', 'asc')
            ->limit(10)
            ->get([
                'HSCode', 'HeadingDesc', 'Chapter',
                'ChapterDesc', 'ImportDutyRate',
            ]);

        return response()->json($results);
    }

    // ════════════════════════════════════════════════════════════════════════
    // HISTORY — Past predictions for a BL/HBL
    // GET /hs-code/history?bl=MSKU123&house_bl=HBL001
    // ════════════════════════════════════════════════════════════════════════

    public function history(Request $request)
    {
        $request->validate([
            'bl' => ['required', 'string', 'max:50'],
            'house_bl' => ['nullable', 'string', 'max:50'],
        ]);

        $query = DB::table('duty_predictions')
            ->where('BL', strtoupper($request->bl))
            ->where('BranchID', Auth::user()->BranchID)
            ->orderBy('created_at', 'desc');

        if ($request->filled('house_bl')) {
            $query->where('HouseBL', strtoupper($request->house_bl));
        }

        $predictions = $query->limit(10)->get();

        return response()->json([
            'success' => true,
            'predictions' => $predictions,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // HS CODE ADVISOR
    // ════════════════════════════════════════════════════════════════════════

    // ── Load consignment data for the advisor modal ──────────────────────────
    // GET /hs-code/load-consignment?bl=MSKU123
    public function loadConsignment(Request $request)
    {
        $request->validate([
            'bl' => ['required', 'string', 'max:50'],
        ]);

        $bl = strtoupper(trim($request->bl));
        $user = Auth::user();

        // ── Core consignment ─────────────────────────────────────────────────
        $consignment = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->where('cm.BL', $bl)
            ->where('cm.BranchID', $user->BranchID)
            ->where('cm.Status', '!=', 9)
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.ETA',
                'cm.Status',
                'cm.CmdtTypeID',
                'co.FullName as ConsigneeName',
                'sc.CarrierName',
            ])
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found for BL: '.$bl,
            ], 404);
        }

        // ── FCL containers with item details ─────────────────────────────────
        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->whereNotNull('ItemDetails')
            ->where('ItemDetails', '!=', '')
            ->get([
                'ContainerNo',
                'ContainerSize',
                'ItemDetails',
                'HSCode',
                'HSDescription',
                'EstimatedDutyRate',
            ]);

        // ── LCL HBL entries with descriptions ────────────────────────────────
        $hblEntries = DB::table('manifestation_breakdown as mb')
            ->leftJoin('consignee_main as co', 'mb.ConsigneeID', '=', 'co.ConsigneeID')
            ->where('mb.ConsignmentID', $consignment->ConsignmentID)
            ->where('mb.Status', '!=', 9)
            ->whereNotNull('mb.Description')
            ->where('mb.Description', '!=', '')
            ->get([
                'mb.ConsignmentID',
                'mb.HouseBL',
                'mb.Description',
                'mb.ItemType',
                'mb.HSCode',
                'mb.HSDescription',
                'mb.EstimatedDutyRate',
                'co.FullName as ConsigneeName',
            ]);

        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'containers' => $containers,
            'hblEntries' => $hblEntries,
            'type' => $consignment->CmdtTypeID == 1 ? 'LCL' : 'FCL',
            'totalItems' => $containers->count() + $hblEntries->count(),
            'itemSummary' => $consignment->CmdtTypeID == 1
    ? $hblEntries->map(fn ($h) => ($h->HouseBL ?? '—').': '.\Illuminate\Support\Str::limit($h->Description ?? '', 40)
    )->join(' | ')
    : $containers->map(fn ($c) => ($c->ContainerNo ?? '—').': '.\Illuminate\Support\Str::limit($c->ItemDetails ?? '', 40)
    )->join(' | '),
        ]);
    }

    // ── Accept all recommended codes in one shot ─────────────────────────────
    // POST /hs-code/accept-all
    public function acceptAll(Request $request)
    {
        $request->validate([
            'consignment_id' => ['required', 'integer'],
            'bl' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:FCL,LCL'],
            'items' => ['required', 'array'],
            'items.*.container_no' => ['required', 'integer'],
            'items.*.hs_code' => ['required', 'string', 'exists:hs_codes,HSCode'],
            'items.*.house_bl' => ['nullable', 'string'],
            'items.*.description' => ['required', 'string'],
            'items.*.was_recommended' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();
        $results = [];
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($request->items as $item) {
                $hsRecord = DB::table('hs_codes')
                    ->where('HSCode', $item['hs_code'])
                    ->first();

                if (! $hsRecord) {
                    continue;
                }

                if ($request->type === 'LCL') {
                    DB::table('manifestation_breakdown')
                        ->where('ConsignmentID', $request->consignment_id)
                        ->where('HouseBL', $item['house_bl'])
                        ->update([
                            'HSCode' => $hsRecord->HSCode,
                            'HSDescription' => $hsRecord->HeadingDesc,
                            'EstimatedDutyRate' => $hsRecord->ImportDutyRate,
                            'HSRecommendedBy' => $user->ID,
                        ]);
                } else {
                    DB::table('container_details')
                        ->where('ConsignmentID', $request->consignment_id)
                        ->where('ContainerNo', $item['container_no'])
                        ->update([
                            'HSCode' => $hsRecord->HSCode,
                            'HSDescription' => $hsRecord->HeadingDesc,
                            'EstimatedDutyRate' => $hsRecord->ImportDutyRate,
                            'HSRecommendedBy' => $user->ID,
                        ]);
                }

                // Log to duty_predictions
                DB::table('duty_predictions')->insert([
                    'SourceType' => $request->type,
                    'ConsignmentID' => $request->consignment_id,
                    'BL' => strtoupper($request->bl),
                    'HouseBL' => isset($item['house_bl'])
                        ? strtoupper($item['house_bl']) : null,
                    'ItemDescription' => $item['description'],
                    'PredictedHSCode' => $item['hs_code'],
                    'PredictedHSDesc' => $hsRecord->HeadingDesc,
                    'PredictedDutyRate' => $hsRecord->ImportDutyRate,
                    'Confidence' => 0,
                    'PredictionSource' => 'advisor',
                    'AcceptedHSCode' => $item['hs_code'],
                    'AcceptedHSDesc' => $hsRecord->HeadingDesc,
                    'AcceptedDutyRate' => $hsRecord->ImportDutyRate,
                    'WasPredictionAccepted' => $item['was_recommended'] ?? true,
                    'AcceptedBy' => $user->ID,
                    'AcceptedAt' => now(),
                    'Username' => $user->ID,
                    'BranchID' => $user->BranchID,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $results[] = [
                    'id' => $item['id'],
                    'hs_code' => $hsRecord->HSCode,
                    'desc' => $hsRecord->HeadingDesc,
                    'duty' => $hsRecord->ImportDutyRate,
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($results).' HS codes saved successfully.',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save HS codes.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ── Print report view ─────────────────────────────────────────────────────
    // GET /hs-code/print-report?bl=MSKU123&cif=10000
    public function printReport(Request $request)
    {
        $request->validate([
            'bl' => ['required', 'string', 'max:50'],
            'cif_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $bl = strtoupper(trim($request->bl));
        $user = Auth::user();

        // Load consignment
        $consignment = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('pol as p', 'cm.POL_ID', '=', 'p.POL_ID')
            ->where('cm.BL', $bl)
            ->where('cm.BranchID', $user->BranchID)
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.ETA',
                'cm.VesselName',
                'cm.VoyageNo',
                'cm.Status',
                'cm.CmdtTypeID',
                'co.FullName as ConsigneeName',
                'co.TelNo as ConsigneeTel',
                'sc.CarrierName',
                'p.POL_Name',
            ])
            ->first();

        if (! $consignment) {
            abort(404, 'Consignment not found.');
        }

        // Load accepted HS predictions for this BL
        $predictions = DB::table('duty_predictions as dp')
            ->leftJoin('hs_codes as hs', 'dp.AcceptedHSCode', '=', 'hs.HSCode')
            ->where('dp.BL', $bl)
            ->where('dp.BranchID', $user->BranchID)
            ->whereNotNull('dp.AcceptedHSCode')
            ->orderBy('dp.created_at', 'desc')
            ->get([
                'dp.SourceType',
                'dp.HouseBL',
                'dp.ItemDescription',
                'dp.AcceptedHSCode',
                'dp.AcceptedHSDesc',
                'dp.AcceptedDutyRate',
                'dp.WasPredictionAccepted',
                'dp.AcceptedBy',
                'dp.AcceptedAt',
                'dp.AllCandidates',
                'dp.Justification',
                'hs.Notes',
                'hs.Exclusions',
            ]);

        // Calculate duty if CIF provided
        $cifValue = (float) ($request->cif_value ?? 0);
        $dutyResults = [];

        if ($cifValue > 0) {
            foreach ($predictions as $pred) {
                if ($pred->AcceptedHSCode) {
                    $dutyResults[$pred->AcceptedHSCode] =
                        $this->hsCodeService->calculateDuty($pred->AcceptedHSCode, $cifValue);
                }
            }
        }

        // Savings calculation — compare accepted vs highest candidate
        $totalAcceptedDuty = 0;
        $totalHighestDuty = 0;

        if ($cifValue > 0) {
            foreach ($predictions as $pred) {
                $accepted = $dutyResults[$pred->AcceptedHSCode] ?? null;
                if ($accepted) {
                    $totalAcceptedDuty += $accepted['TotalDuty'];

                    // Find highest duty candidate from AllCandidates
                    $candidates = json_decode($pred->AllCandidates ?? '[]', true);
                    if (! empty($candidates)) {
                        $maxRate = collect($candidates)->max('ImportDutyRate') ?? $pred->AcceptedDutyRate;
                        $highestBreakdown = $this->hsCodeService->calculateDuty(
                            collect($candidates)->sortByDesc('ImportDutyRate')->first()['HSCode'] ?? $pred->AcceptedHSCode,
                            $cifValue
                        );
                        $totalHighestDuty += $highestBreakdown['TotalDuty'] ?? 0;
                    } else {
                        $totalHighestDuty += $accepted['TotalDuty'];
                    }
                }
            }
        }

        $potentialSavings = max(0, round($totalHighestDuty - $totalAcceptedDuty, 2));

        return view('partials.hs-advisor-print', compact(
            'consignment', 'predictions', 'dutyResults',
            'cifValue', 'totalAcceptedDuty', 'totalHighestDuty',
            'potentialSavings'
        ));
    }
}
