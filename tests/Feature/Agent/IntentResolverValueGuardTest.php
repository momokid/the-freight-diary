<?php

namespace Tests\Feature\Agent;

use App\Agent\Intent\IntentResolver;
use App\Agent\Intent\ResolverPrompt;
use App\Agent\Llm\LlmAdapter;
use App\Agent\Llm\LlmResponse;
use Carbon\Carbon;
use Tests\TestCase;

class IntentResolverValueGuardTest extends TestCase
{
    private const CATALOGUE = [[
        'key'    => 'register.fcl',
        'title'  => 'Register FCL consignment',
        'params' => [
            'Reference'      => ['type' => 'string',  'required' => true],
            'ConsigneeName'  => ['type' => 'string',  'required' => true],
            'ETA'            => ['type' => 'date',    'required' => true],
            'ContainerCount' => ['type' => 'integer', 'required' => true],
            'ContainerSize'  => ['type' => 'string',  'required' => true],
        ],
    ]];

    private function resolve(string $instruction, array $reply): array
    {
        $resolver = new IntentResolver(
            new FakeLlmAdapter(json_encode($reply)),
            new ResolverPrompt(),
        );

        return $resolver->resolve($instruction, self::CATALOGUE);
    }

    private function reply(array $params, ?string $reference = 'MSCU4421889'): array
    {
        return [
            'playbook'   => 'register.fcl',
            'confidence' => 0.9,
            'reference'  => $reference,
            'params'     => $params,
        ];
    }

    public function test_verbatim_string_is_kept(): void
    {
        $result = $this->resolve(
            'register MSCU4421889 for Douglas Frimpong',
            $this->reply(['ConsigneeName' => 'Douglas Frimpong'])
        );

        $this->assertSame('Douglas Frimpong', $result['params']['ConsigneeName']);
    }

    public function test_invented_string_is_dropped(): void
    {
        $result = $this->resolve(
            'register MSCU4421889 for Douglas Frimpong',
            $this->reply(['ConsigneeName' => 'Douglas Frimpong Ltd'])
        );

        $this->assertArrayNotHasKey('ConsigneeName', $result['params']);
    }

    public function test_date_may_be_reformatted(): void
    {
        $expected = Carbon::parse('24 June')->toDateString();

        $result = $this->resolve(
            'register MSCU4421889 eta 24 June',
            $this->reply(['ETA' => $expected])
        );

        $this->assertSame($expected, $result['params']['ETA']);
    }

    public function test_slash_date_is_read_day_first(): void
    {
        $result = $this->resolve(
            'register MSCU4421889 eta 03/07/2026',
            $this->reply(['ETA' => '2026-07-03'])
        );

        $this->assertSame('2026-07-03', $result['params']['ETA']);
    }

    public function test_month_first_reading_is_rejected(): void
    {
        $result = $this->resolve(
            'register MSCU4421889 eta 03/07/2026',
            $this->reply(['ETA' => '2026-03-07'])
        );

        $this->assertArrayNotHasKey('ETA', $result['params']);
    }

    public function test_date_absent_from_instruction_is_dropped(): void
    {
        $result = $this->resolve(
            'register MSCU4421889',
            $this->reply(['ETA' => '2026-07-03'])
        );

        $this->assertArrayNotHasKey('ETA', $result['params']);
    }

    public function test_spelled_number_evidences_a_digit(): void
    {
        $result = $this->resolve(
            'register MSCU4421889, two 40ft containers',
            $this->reply(['ContainerCount' => 2])
        );

        $this->assertSame(2, $result['params']['ContainerCount']);
    }

    public function test_container_size_is_not_evidence_for_a_count(): void
    {
        $result = $this->resolve(
            'register MSCU4421889, two 20ft containers',
            $this->reply(['ContainerCount' => 20])
        );

        $this->assertArrayNotHasKey('ContainerCount', $result['params']);
    }

    public function test_reference_survives_and_undeclared_params_do_not(): void
    {
        $result = $this->resolve(
            'register MSCU4421889 for Douglas Frimpong',
            $this->reply(['Nonsense' => 'Douglas'])
        );

        $this->assertSame('MSCU4421889', $result['params']['Reference']);
        $this->assertArrayNotHasKey('Nonsense', $result['params']);
    }

    public function test_missing_required_reference_fails_resolution(): void
    {
        $result = $this->resolve(
            'register a consignment',
            $this->reply([], null)
        );

        $this->assertSame(IntentResolver::NONE, $result['outcome']);
    }
}

class FakeLlmAdapter implements LlmAdapter
{
    public function __construct(private string $text) {}

    public function complete(string $systemPrompt, string $userPrompt, array $options = []): LlmResponse
    {
        return LlmResponse::ok($this->text, 1);
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function model(): string
    {
        return 'fake';
    }
}
