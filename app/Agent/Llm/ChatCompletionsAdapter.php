<?php

namespace App\Agent\Llm;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to any provider exposing an OpenAI-style /chat/completions endpoint —
 * GLM, Ollama, Groq. Config supplies the base URL, key and model, so a swap
 * is an .env change rather than a new class.
 */
class ChatCompletionsAdapter implements LlmAdapter
{
    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private string $model,
        private int $timeout = 10,
        private bool $requiresKey = true,
    ) {}

    public function isConfigured(): bool
    {
        return $this->baseUrl !== ''
            && ($this->apiKey !== '' || ! $this->requiresKey);
    }

    public function model(): string
    {
        return $this->model;
    }

    public function complete(string $systemPrompt, string $userPrompt, array $options = []): LlmResponse
    {
        if (! $this->isConfigured()) {
            return LlmResponse::fail('LLM adapter not configured.', 0);
        }

        $payload = [
            'model'       => $this->model,
            'temperature' => $options['temperature'] ?? 0,
            'max_tokens'  => $options['max_tokens'] ?? 300,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPrompt],
            ],
        ];

        if (! empty($options['json'])) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        // Reasoning models otherwise burn the budget thinking. Providers that
        // don't recognise this ignore it.
        if (! empty($options['thinking'])) {
            $payload['thinking'] = ['type' => 'enabled'];
        } else {
            $payload['thinking'] = ['type' => 'disabled'];
        }

        $started = microtime(true);

        try {
            $headers = ['Content-Type' => 'application/json'];

            if ($this->apiKey !== '') {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $response = Http::withHeaders($headers)
                ->connectTimeout(3)
                ->timeout($this->timeout)
                ->post(rtrim($this->baseUrl, '/') . '/chat/completions', $payload);
        } catch (ConnectionException $e) {
            // Timeout or unreachable host — no response ever arrived.
            Log::warning('[Llm] Connection failed: ' . $e->getMessage());
            return LlmResponse::fail('Connection failed or timed out.', $this->elapsed($started));
        } catch (\Throwable $e) {
            report($e);
            return LlmResponse::fail('Request failed.', $this->elapsed($started));
        }

        $ms = $this->elapsed($started);

        if ($response->failed()) {
            Log::warning('[Llm] HTTP ' . $response->status() . ': ' . mb_substr($response->body(), 0, 500));
            return LlmResponse::fail('Provider returned ' . $response->status() . '.', $ms, $response->status());
        }

        // Some providers return application errors inside an HTTP 200.
        if ($response->json('success') === false || $response->json('error')) {
            $msg = $response->json('msg')
                ?? $response->json('error.message')
                ?? 'Provider reported an error.';

            Log::warning('[Llm] Error envelope in 200: ' . $msg);
            return LlmResponse::fail((string) $msg, $ms, $response->status());
        }

        $text = $response->json('choices.0.message.content');

        if (! is_string($text) || trim($text) === '') {
            $reason = $response->json('choices.0.finish_reason');

            // Reasoning models can spend the whole budget thinking.
            $why = $reason === 'length'
                ? 'Response truncated before any content — raise max_tokens.'
                : 'Empty response from provider.';

            Log::warning('[Llm] ' . $why);
            return LlmResponse::fail($why, $ms, $response->status());
        }

        return LlmResponse::ok(trim($text), $ms, $response->status());
    }

    private function elapsed(float $started): int
    {
        return (int) round((microtime(true) - $started) * 1000);
    }
}
