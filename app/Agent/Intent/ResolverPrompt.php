<?php

namespace App\Agent\Intent;

/**
 * Builds the Layer 3 prompt.
 *
 * The catalogue is emitted before the instruction so the stable portion of the
 * prompt sits at the front — that is what prompt caching can reuse as the
 * playbook list grows. Only playbook titles, descriptions and parameter names
 * leave the building. No steps, no gates, no permission names, no schema.
 */
class ResolverPrompt
{
    public function system(array $catalogue): string
    {
        $lines = [];

        $lines[] = 'You classify instructions for a freight forwarding and customs clearing system.';
        $lines[] = '';
        $lines[] = 'Choose which task the user is asking for, from this list and no other:';
        $lines[] = '';

        foreach ($catalogue as $entry) {
            $lines[] = $this->describe($entry);
        }

        $lines[] = '';
        $lines[] = 'Rules:';
        $lines[] = '- Choose only a key from the list above. Never invent a key.';
        $lines[] = '- Copy parameter values exactly as they appear in the instruction. Never correct, complete or invent them.';
        $lines[] = '- A reference is a bill of lading or container number the user typed. If none is present, return null.';
        $lines[] = '- Dates, amounts and quantities are not references.';
        $lines[] = '- If unsure between two tasks, choose the closer one and give a low confidence.';
        $lines[] = '- If the instruction matches nothing on the list, return null for playbook.';
        $lines[] = '';
        $lines[] = 'Reply with JSON only. No prose, no markdown fences.';
        $lines[] = '{"playbook": string|null, "confidence": number between 0 and 1, "reference": string|null, "params": object, "alternates": [{"playbook": string, "confidence": number}]}';

        return implode("\n", $lines);
    }

    public function user(string $instruction): string
    {
        return "Instruction:\n" . $instruction;
    }

    private function describe(array $entry): string
    {
        $out = "- {$entry['key']}: {$entry['title']}";

        if (! empty($entry['description'])) {
            $out .= ' — ' . $entry['description'];
        }

        if (! empty($entry['params'])) {
            $params = [];

            foreach ($entry['params'] as $name => $spec) {
                $params[] = $name
                    . ' (' . $spec['type'] . ($spec['required'] ? ', required' : ', optional') . ')'
                    . (! empty($spec['description']) ? ': ' . $spec['description'] : '');
            }

            $out .= "\n    parameters: " . implode('; ', $params);
        }

        if (! empty($entry['examples'])) {
            $out .= "\n    example phrasings: " . implode(' | ', $entry['examples']);
        }

        return $out;
    }
}
