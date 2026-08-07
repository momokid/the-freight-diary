<?php

namespace App\Agent\Llm;

/**
 * Transport for a single LLM call. Knows nothing about intents, playbooks
 * or confidence — those belong to the resolver that consumes this.
 *
 * Implementations never throw for expected failure. Timeouts, throttling
 * and malformed responses all come back as LlmResponse::fail().
 */
interface LlmAdapter
{
    /**
     * $options accepts 'temperature', 'max_tokens' and 'json' (bool).
     * Anything unrecognised is ignored, so a swapped provider that lacks
     * a knob degrades instead of breaking.
     */
    public function complete(string $systemPrompt, string $userPrompt, array $options = []): LlmResponse;

    /** False when no key or base URL is configured. Lets callers skip the round trip. */
    public function isConfigured(): bool;

    /** For logging and audit rows — e.g. 'glm-4.5-flash'. */
    public function model(): string;
}
