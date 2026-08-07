<?php

namespace App\Agent\Llm;

/**
 * The off switch. Set AGENT_LLM_DRIVER=none and Layer 3 stops calling out
 * without a deploy — useful when a provider is down or throttling and you
 * are on shared hosting with no quick way to patch.
 *
 * Unresolved instructions behave exactly as they did before Layer 3 existed.
 */
class NullAdapter implements LlmAdapter
{
    public function isConfigured(): bool
    {
        return false;
    }

    public function model(): string
    {
        return 'none';
    }

    public function complete(string $systemPrompt, string $userPrompt, array $options = []): LlmResponse
    {
        return LlmResponse::fail('LLM disabled.', 0);
    }
}
