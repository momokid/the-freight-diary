<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ETA Alert System
    |--------------------------------------------------------------------------
    | Master switch. Set ALERT_ETA_ENABLED=false in .env to suspend all alerts
    | instantly without touching any code.
    */

    'enabled' => env('ALERT_ETA_ENABLED', true),

    /*
    | Internal daily digest — comma-separated emails in .env.
    | Example: ALERT_DIGEST_EMAILS=ops@psil.com,manager@psil.com
    */
    'digest_emails' => array_filter(
        array_map('trim', explode(',', env('ALERT_DIGEST_EMAILS', '')))
    ),

    /*
    | Consignee alert channels — both fire for ARRIVAL and ETA_CHANGE events.
    */
    'consignee_channels' => ['sms'],

];
