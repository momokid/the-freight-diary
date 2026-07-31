<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Step registry
    |---------------------------------------------------------------------------
    | The complete list of operations the agent can perform. Explicit by design:
    | nothing becomes executable by being placed in a folder. Admins compose
    | playbooks from these keys — they cannot author new ones.
    */

    'steps' => [
        'consignment.resolve' => \App\Agent\Steps\ResolveConsignmentStep::class,
        'consignment.read'    => \App\Agent\Steps\ReadConsignmentStep::class,
        'reply.compose'       => \App\Agent\Steps\ComposeReplyStep::class,
    ],

    /*
    |---------------------------------------------------------------------------
    | Task vocabulary
    |---------------------------------------------------------------------------
    | Every playbook must declare one of these as its TaskType, so admin-composed
    | tasks land in an existing reporting bucket instead of inventing their own.
    */

    'tasks' => [
        'lookup.status'               => 'Status lookup',
        'manifest.breakdown'          => 'Manifest breakdown',
        'consignment.register.fcl'    => 'BL setup (FCL)',
        'consignment.register.lcl'    => 'BL setup (LCL)',
        'consignment.edit'            => 'Edit consignment',
        'disbursement.analysis'       => 'Disbursement analysis',
        'invoice.hbl'                 => 'House BL invoice',
        'invoice.service'             => 'Other service invoice',
        'invoice.nonmanifest'         => 'Non-manifest invoice',
        'waybill.create'              => 'Customer waybill',
        'declaration.process'         => 'Process declaration',
        'accounting.transaction'      => 'Accounting transaction',
        'receipt.generate'            => 'Receipt generation',
    ],

    /*
    |---------------------------------------------------------------------------
    | Stage thresholds (days)
    |---------------------------------------------------------------------------
    | Placeholders. Arrival is derived: ETA in the past and unchanged means the
    | consignment has landed. Each figure is the tolerated gap before a stage is
    | flagged as stalled. Move these to system_settings when the monitor is built
    | so they can be tuned without a deploy.
    */

    'thresholds' => [
        'arrival_to_manifest'      => 2,
        'manifest_to_disbursement' => 2,
        'disbursement_to_gateout'  => 3,
        'gateout_to_return'        => 5,
    ],

];
