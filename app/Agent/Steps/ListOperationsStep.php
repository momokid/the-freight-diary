<?php

namespace App\Agent\Steps;

/** Operational backlog: what is late, and what was never classified. */
class ListOperationsStep extends ListConsignmentsStep
{
    public static function key(): string
    {
        return 'consignment.list.operations';
    }

    public static function label(): string
    {
        return 'List operational backlog';
    }

    public static function permission(): ?string
    {
        return 'OperationsReport';
    }

    public static function filters(): array
    {
        return ['overdue', 'unconfirmed_type'];
    }
}
