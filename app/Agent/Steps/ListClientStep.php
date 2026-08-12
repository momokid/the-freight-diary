<?php

namespace App\Agent\Steps;

/** Disbursed consignments with nothing billed yet. Billing is on request. */
class ListClientStep extends ListConsignmentsStep
{
    public static function key(): string
    {
        return 'consignment.list.client';
    }

    public static function label(): string
    {
        return 'List uninvoiced consignments';
    }

    public static function permission(): ?string
    {
        return 'ClientReport';
    }

    public static function filters(): array
    {
        return ['not_invoiced'];
    }
}
