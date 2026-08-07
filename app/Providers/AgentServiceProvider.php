<?php

namespace App\Providers;

use App\Agent\Llm\ChatCompletionsAdapter;
use App\Agent\Llm\LlmAdapter;
use App\Agent\Llm\NullAdapter;
use Illuminate\Support\ServiceProvider;

/**
 * Agent container bindings. Deliberately separate from AppServiceProvider,
 * whose view composer is load-bearing for every page in the app.
 */
class AgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LlmAdapter::class, function () {

            $driver = config('agent.llm.driver', 'none');
            $conf   = config("agent.llm.drivers.{$driver}");

            // Unknown driver name — disable rather than guess.
            if (! is_array($conf) || empty($conf['adapter'])) {
                return new NullAdapter();
            }

            if ($conf['adapter'] === NullAdapter::class) {
                return new NullAdapter();
            }

            return new ChatCompletionsAdapter(
                (string) ($conf['base_url'] ?? ''),
                (string) ($conf['key'] ?? ''),
                (string) ($conf['model'] ?? ''),
                (int) ($conf['timeout'] ?? 10),
                (bool) ($conf['requires_key'] ?? true),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
