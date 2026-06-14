<?php

namespace App\Services;

use App\Models\EtaAlertLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EtaAlertService
{
    public function __construct(private ArkeselService $arkesel) {}

    /**
     * Main entry point — called by the scheduled command.
     * Returns digest data for the internal email.
     */
    public function run(): array
    {
        if (!config('alerts.enabled')) {
            Log::info('[EtaAlertService] Alerts disabled — skipping.');
            return [];
        }

        $consignments = $this->getActiveConsignments();

        if ($consignments->isEmpty()) {
            return [];
        }

        $latestLogs = $this->getLatestSnapshots($consignments->pluck('BL')->toArray());

        $digest = ['arriving_today' => [], 'upcoming' => [], 'eta_changed' => []];

        foreach ($consignments as $consignment) {
            $etaDays = (int) $consignment->ETADays;

            // Populate digest from the full active set regardless of log state
            if ($etaDays === 0) {
                $digest['arriving_today'][] = $consignment;
            } elseif ($etaDays <= 3) {
                $digest['upcoming'][] = $consignment;
            }

            $latestSnapshot = $latestLogs->get($consignment->BL);

            // First time seeing this consignment — record baseline, no SMS
            if ($latestSnapshot === null) {
                $this->recordBaseline($consignment);
                continue;
            }

            // ETA change — current ETA differs from last recorded snapshot
            if ($latestSnapshot !== $consignment->ETA) {
                $this->sendEtaChange($consignment);
                $digest['eta_changed'][] = $consignment;
            }

            // Arrival SMS — fires only on the exact ETA date, never for overdue consignments
            if ($etaDays === 0) {
                $this->sendArrival($consignment);
            }
        }

        return $digest;
    }

    // -------------------------------------------------------------------------
    // Queries
    // -------------------------------------------------------------------------

    private function getActiveConsignments()
    {
        return DB::table('container_main as cm')
            ->join('consignee_main as co', 'co.ConsigneeID', '=', 'cm.ConsigneeID')
            ->select([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ConsigneeID',
                'cm.ETA',
                DB::raw('TO_DAYS(cm.ETA) - TO_DAYS(CURDATE()) AS ETADays'),
                'co.FullName',
                'co.TelNo',
            ])
            ->where('cm.Status', 1)
            ->where('cm.Ownership', 1)
            ->whereRaw('cm.ETA >= CURDATE()')
            ->where('co.AlertOptOut', 0)
            ->where('co.TelNo', '!=', '')
            ->addSelect(DB::raw(
                '(SELECT COUNT(*) FROM container_details cd 
      WHERE cd.ConsignmentID = cm.ConsignmentID) as ContainerCount'
            ))
            ->get();
    }

    private function getLatestSnapshots(array $bls): \Illuminate\Support\Collection
    {
        if (empty($bls)) {
            return collect();
        }

        // Correlated subquery — gets the ETASnapshot from the most recent
        // log row per BL, keyed by BL for O(1) lookup in the loop above
        return DB::table('eta_alert_log as eal')
            ->whereIn('eal.BL', $bls)
            ->whereRaw(
                'eal.SentAt = (SELECT MAX(eal2.SentAt) FROM eta_alert_log eal2 WHERE eal2.BL = eal.BL)'
            )
            ->pluck('ETASnapshot', 'BL');
    }

    // -------------------------------------------------------------------------
    // Alert senders
    // -------------------------------------------------------------------------

    private function sendEtaChange(object $consignment): void
    {
        $message = $this->buildEtaChangeMessage($consignment->FullName, $consignment->BL, $consignment->ETA);
        $result  = $this->arkesel->sendSms($consignment->TelNo, $message);

        $this->logAlert(
            $consignment,
            EtaAlertLog::TYPE_ETA_CHANGE,
            EtaAlertLog::CHANNEL_SMS,
            $consignment->TelNo,
            $consignment->ETA,
            $result,
            $message
        );
    }

    private function sendArrival(object $consignment): void
    {
        $message = $this->buildArrivalMessage($consignment->FullName, $consignment->BL);
        $result  = $this->arkesel->sendSms($consignment->TelNo, $message);

        $this->logAlert(
            $consignment,
            EtaAlertLog::TYPE_ARRIVAL,
            EtaAlertLog::CHANNEL_SMS,
            $consignment->TelNo,
            $consignment->ETA,
            $result,
            $message
        );
    }

    private function recordBaseline(object $consignment): void
    {
        $this->logAlert(
            $consignment,
            EtaAlertLog::TYPE_BASELINE,
            EtaAlertLog::CHANNEL_SYSTEM,
            null,
            $consignment->ETA,
            ['success' => true, 'ref' => null, 'error' => null],
            null
        );
    }

    // -------------------------------------------------------------------------
    // Logging
    // -------------------------------------------------------------------------

    private function logAlert(
        object  $consignment,
        string  $type,
        string  $channel,
        ?string $recipient,
        string  $etaSnapshot,
        array   $result,
        ?string $message,
    ): void {
        try {
            EtaAlertLog::create([
                'ConsignmentID' => $consignment->ConsignmentID,
                'BL'            => $consignment->BL,
                'ConsigneeID'   => $consignment->ConsigneeID,
                'AlertType'     => $type,
                'Channel'       => $channel,
                'Recipient'     => $recipient,
                'ETASnapshot'   => $etaSnapshot,
                'Status'        => match (true) {
                    $type === EtaAlertLog::TYPE_BASELINE => EtaAlertLog::STATUS_SEEN,
                    $result['success']                   => EtaAlertLog::STATUS_SENT,
                    default                              => EtaAlertLog::STATUS_FAILED,
                },
                'ProviderRef'   => $result['ref'],
                'Message'       => $message,
                'SentAt'        => now(),
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            // Unique index did its job — this exact alert was already sent/recorded
            Log::info('[EtaAlertService] Duplicate skipped', [
                'BL' => $consignment->BL,
                'type' => $type,
                'channel' => $channel,
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Message builders — edit here to change SMS wording
    // -------------------------------------------------------------------------

    private function buildArrivalMessage(string $fullName, string $bl): string
    {
        $name = mb_substr(trim($fullName), 0, 20);
        return "Dear {$name}, your consignment BL {$bl} is due to arrive at Tema Port today. Please prepare for clearance. - PSIL";
    }

    private function buildEtaChangeMessage(string $fullName, string $bl, string $newETA): string
    {
        $name = mb_substr(trim($fullName), 0, 20);
        $date = date('d M Y', strtotime($newETA));
        return "Dear {$name}, the arrival date for BL {$bl} has been updated to {$date}. Contact PSIL for details.";
    }
}
