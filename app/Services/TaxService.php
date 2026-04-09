<?php

namespace App\Services;

use App\Models\TaxComponent;

class TaxService
{
    /**
     * Calculate tax breakdown for a given amount.
     * The calculation is driven entirely by the tax_components table.
     * No hardcoded rates — add/remove/modify components in the DB.
     *
     * Example output:
     * [
     *   'base'      => 1000.00,
     *   'lines'     => [
     *     ['name' => 'GetFund', 'label' => '...', 'rate' => 2.50, 'base' => 1000.00, 'tax' => 25.00],
     *     ['name' => 'NHIL',    'label' => '...', 'rate' => 2.50, 'base' => 1000.00, 'tax' => 25.00],
     *     ['name' => 'VAT',     'label' => '...', 'rate' => 15.00,'base' => 1050.00, 'tax' => 157.50],
     *   ],
     *   'total_tax' => 207.50,
     *   'total'     => 1207.50,
     * ]
     */
    public static function calculate(float $amount, bool $taxable = true): array
    {
        if (!$taxable) {
            return [
                'base'      => $amount,
                'lines'     => [],
                'total_tax' => 0.00,
                'total'     => $amount,
            ];
        }

        $components = TaxComponent::active()->ordered()->get();
        $subtotal   = $amount;
        $lines      = [];

        foreach ($components as $component) {
            // 'base'     = always calculated on the original amount
            // 'subtotal' = calculated on the running subtotal (amount + all previous taxes)
            $base = $component->applies_on === 'subtotal' ? $subtotal : $amount;
            $tax  = round((float) $base * ((float) $component->rate / 100), 2);

            $lines[] = [
                'name'  => $component->name,
                'label' => $component->label,
                'rate'  => (float) $component->rate,
                'base'  => round($base, 2),
                'tax'   => $tax,
            ];

            $subtotal = round($subtotal + $tax, 2);
        }

        return [
            'base'      => round($amount, 2),
            'lines'     => $lines,
            'total_tax' => round($subtotal - $amount, 2),
            'total'     => round($subtotal, 2),
        ];
    }

    /**
     * Get tax components as JSON for use in JavaScript.
     * Used to mirror server-side calculation in the browser.
     */
    public static function componentsForJS(): array
    {
        return TaxComponent::active()->ordered()->get(['name', 'label', 'rate', 'applies_on'])->toArray();
    }

    /**
     * Get a named component's current rate.
     * e.g. TaxService::rate('VAT') → 15.00
     */
    public static function rate(string $name): float
    {
        $component = TaxComponent::active()
            ->where('name', $name)
            ->first();

        return $component ? (float) $component->rate : 0.00;
    }
}
