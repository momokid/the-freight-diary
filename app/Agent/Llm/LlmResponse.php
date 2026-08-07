<?php

namespace App\Agent\Llm;

/**
 * The outcome of one LLM call.
 *
 * Failure is a normal result here, not an exception. An instruction the model
 * cannot resolve is an ordinary outcome in this system, and forcing callers to
 * wrap every call in a try/catch invites someone to swallow it wrongly.
 */
final class LlmResponse
{
    private function __construct(
        public readonly bool $ok,
        public readonly string $text,
        public readonly ?string $error,
        public readonly ?int $httpStatus,
        public readonly int $latencyMs,
    ) {}

    public static function ok(string $text, int $latencyMs, ?int $httpStatus = 200): self
    {
        return new self(true, $text, null, $httpStatus, $latencyMs);
    }

    /** $httpStatus is null when no response arrived — timeout or connection failure. */
    public static function fail(string $error, int $latencyMs, ?int $httpStatus = null): self
    {
        return new self(false, '', $error, $httpStatus, $latencyMs);
    }

    /** Provider is rate limiting. Worth backing off rather than retrying. */
    public function isThrottled(): bool
    {
        return $this->httpStatus === 429;
    }
}
