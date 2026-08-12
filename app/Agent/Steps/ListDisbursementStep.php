<?php

namespace App\Agent\Steps;

/** Arrived consignments with no disbursement raised against them. */
class ListDisbursementStep extends ListConsignmentsStep
{
    public static function key(): string
    {
        return 'consignment.list.disbursement';
    }

    public static function label(): string
    {
        return 'List undisbursed consignments';
    }

    public static function permission(): ?string
    {
        return 'DisbursementReport';
    }

    public static function filters(): array
    {
        return ['not_disbursed'];
    }
}
