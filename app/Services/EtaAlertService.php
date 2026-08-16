<?php

namespace App\Services;

use App\Models\EtaAlertLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EtaAlertService
{
    /** How far ahead the internal digest looks. */
    private const UPCOMING_DAYS = 3;

    public function __construct(private ArkeselService $arkesel) {}

    /** Fetched once per run — a run can send many messages. */
    private ?string $companyName = null;

    private function companyName(): string
    {
        return $this->companyName ??= CompanyService::institution()?->InstName ?? '';
    }

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

        $digest = ['arriving_today' => [], 'upcoming' => [], 'eta_changed' => []];

        // The internal digest covers every arriving consignment. LCLs carry no
        // consignee on container_main, so a join would drop them silently.
        foreach ($this->getDigestConsignments() as $row) {
            $etaDays = (int) $row->ETADays;

            if ($etaDays === 0) {
                $digest['arriving_today'][] = $row;
            } elseif ($etaDays <= self::UPCOMING_DAYS) {
                $digest['upcoming'][] = $row;
            }
        }

        // SMS is a separate set: it needs a phone number and honours opt-out.
        $consignments = $this->getSmsConsignments();

        if ($consignments->isEmpty()) {
            return $digest;
        }

        $latestLogs = $this->getLatestSnapshots($consignments->pluck('BL')->toArray());

        foreach ($consignments as $consignment) {
            if ((int) $consignment->ETADays === 0) {
                $this->queueArrival($consignment);
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
        }

        return $digest;
    }

    /**
     * Everything arriving, whoever it belongs to. No consignee join: an LCL
     * carries ConsigneeID 0 because its consignees sit on the house BLs.
     */
    private function getDigestConsignments()
    {
        return DB::table('container_main as cm')
            ->leftJoin('ship_carrier as sc', 'sc.CarrierID', '=', 'cm.CarrierID')
            ->where('cm.Status', 1)
            ->where('cm.Ownership', 1)
            ->whereRaw('cm.ETA BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)', [self::UPCOMING_DAYS])
            ->orderBy('cm.ETA')
            ->get([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'sc.CarrierName',
                DB::raw('TO_DAYS(cm.ETA) - TO_DAYS(CURDATE()) AS ETADays'),
                DB::raw('(SELECT COUNT(*) FROM container_details cd
                          WHERE cd.ConsignmentID = cm.ConsignmentID) as ContainerCount'),
            ]);
    }

    /** Only what can actually be texted — a phone number and no opt-out. */
    private function getSmsConsignments()
    {
        return DB::table('container_main as cm')
            ->join('consignee_main as co', 'co.ConsigneeID', '=', 'cm.ConsigneeID')
            ->where('cm.Status', 1)
            ->where('cm.Ownership', 1)
            ->whereRaw('cm.ETA >= CURDATE()')
            ->where('co.AlertOptOut', 0)
            ->where('co.TelNo', '!=', '')
            ->get([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ConsigneeID',
                'cm.ETA',
                'co.FullName',
                'co.TelNo',
                DB::raw('TO_DAYS(cm.ETA) - TO_DAYS(CURDATE()) AS ETADays'),
                DB::raw('(SELECT COUNT(*) FROM container_details cd
                          WHERE cd.ConsignmentID = cm.ConsignmentID) as ContainerCount'),
            ]);
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

    private function queueArrival(object $consignment): void
    {
        $already = DB::table('arrival_sms_queue')
            ->where('BL', $consignment->BL)
            ->where('QueueDate', now()->toDateString())
            ->exists();

        if ($already) {
            return;
        }

        $message = $this->buildArrivalMessage($consignment->FullName, $consignment->BL);

        DB::table('arrival_sms_queue')->insert([
            'ConsignmentID' => $consignment->ConsignmentID,
            'BL'            => $consignment->BL,
            'ConsigneeID'   => $consignment->ConsigneeID,
            'ConsigneeName' => $consignment->FullName,
            'Phone'         => $consignment->TelNo,
            'ETA'           => $consignment->ETA,
            'ContainerCount' => (int) $consignment->ContainerCount,
            'Message'       => $message,
            'Status'        => 0,
            'SentBy'        => null,
            'SentAt'        => null,
            'QueueDate'     => now()->toDateString(),
        ]);
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

    // Message builders — edit here to change SMS wording

    private function buildArrivalMessage(string $fullName, string $bl): string
    {
        $name    = mb_substr(trim($fullName), 0, 20);
        $company = $this->companyName();
        $signoff = $company === '' ? '' : " - {$company}";

        return "Dear {$name}, your consignment BL {$bl} is due to arrive at Tema Port today. Please prepare for clearance.{$signoff}";
    }

    private function buildEtaChangeMessage(string $fullName, string $bl, string $newETA): string
    {
        $name    = mb_substr(trim($fullName), 0, 20);
        $date    = date('d M Y', strtotime($newETA));
        $company = $this->companyName();
        $contact = $company === '' ? 'Contact us for details.' : "Contact {$company} for details.";

        return "Dear {$name}, the arrival date for BL {$bl} has been updated to {$date}. {$contact}";
    }
}
