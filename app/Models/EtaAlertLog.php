<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtaAlertLog extends Model
{
    protected $table = 'eta_alert_log';

    // SentAt is managed by the application, not Eloquent
    public $timestamps = false;

    protected $fillable = [
        'ConsignmentID',
        'BL',
        'ConsigneeID',
        'AlertType',
        'Channel',
        'Recipient',
        'ETASnapshot',
        'Status',
        'ProviderRef',
        'Message',
        'SentAt',
    ];

    protected $casts = [
        'ETASnapshot' => 'date',
        'SentAt'      => 'datetime',
    ];

    // AlertType constants — used in EtaAlertService, avoids magic strings
    const TYPE_BASELINE   = 'BASELINE';
    const TYPE_ETA_CHANGE = 'ETA_CHANGE';
    const TYPE_ARRIVAL    = 'ARRIVAL';

    // Channel constants
    const CHANNEL_SMS    = 'SMS';
    const CHANNEL_SYSTEM = 'SYSTEM'; // used for BASELINE rows (no message sent)

    // Status constants
    const STATUS_SENT   = 'SENT';
    const STATUS_FAILED = 'FAILED';
    const STATUS_SEEN   = 'SEEN';   // used for BASELINE rows
}
