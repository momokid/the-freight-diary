<?php

return [

    'steps' => [
        'consignment.resolve' => \App\Agent\Steps\ResolveConsignmentStep::class,
        'consignment.read'    => \App\Agent\Steps\ReadConsignmentStep::class,
        'manifest.read'       => \App\Agent\Steps\ManifestBreakdownStep::class,
        'reply.compose'       => \App\Agent\Steps\ComposeReplyStep::class,
        'reply.manifest'      => \App\Agent\Steps\ComposeManifestReplyStep::class,
    ],

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

    'thresholds' => [
        'arrival_to_manifest'      => 2,
        'manifest_to_disbursement' => 2,
        'disbursement_to_gateout'  => 3,
        'gateout_to_return'        => 5,
    ],

    /*
    |---------------------------------------------------------------------------
    | LLM adapter
    |---------------------------------------------------------------------------
    | Layer 3 of intent routing. Set AGENT_LLM_DRIVER to swap providers — any
    | endpoint speaking the OpenAI /chat/completions shape needs only an entry
    | here, no new class. Set it to 'none' to disable Layer 3 without a deploy.
    */

    'llm' => [

        'driver' => env('AGENT_LLM_DRIVER', 'none'),

        /*
        | Below this, Layer 3 stops and offers ranked suggestions instead of
        | running. Raise it after watching real traffic — it is deliberately
        | config so it moves without a deploy.
        */
        'confidence_floor' => (float) env('AGENT_CONFIDENCE_FLOOR', 0.70),

        'drivers' => [

            'glm' => [
                'adapter'      => \App\Agent\Llm\ChatCompletionsAdapter::class,
                'base_url'     => env('GLM_BASE_URL', 'https://api.z.ai/api/paas/v4'),
                'key'          => env('GLM_API_KEY', ''),
                'model'        => env('GLM_MODEL', 'glm-4.5-flash'),
                'timeout'      => (int) env('GLM_TIMEOUT', 10),
                'requires_key' => true,
            ],

            // Local dev. No key by design.
            'ollama' => [
                'adapter'      => \App\Agent\Llm\ChatCompletionsAdapter::class,
                'base_url'     => env('OLLAMA_CHAT_URL', 'http://127.0.0.1:11434/v1'),
                'key'          => env('OLLAMA_API_KEY', ''),
                'model'        => env('OLLAMA_CHAT_MODEL', 'llama3.1'),
                'timeout'      => (int) env('OLLAMA_TIMEOUT', 30),
                'requires_key' => false,
            ],

            'claude' => [
                'adapter'      => \App\Agent\Llm\ChatCompletionsAdapter::class,
                'base_url'     => env('CLAUDE_CHAT_URL', 'https://api.anthropic.com/v1'),
                'key'          => env('ANTHROPIC_API_KEY', ''),
                'model'        => env('AGENT_CLAUDE_MODEL', 'claude-haiku-4-5'),
                'timeout'      => (int) env('AGENT_CLAUDE_TIMEOUT', 15),
                'requires_key' => true,
            ],

            'none' => [
                'adapter' => \App\Agent\Llm\NullAdapter::class,
            ],
        ],
    ],
];
