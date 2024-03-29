<?php

namespace App\Filament\Resources\ExceptionLogResource\Pages;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ExceptionLogResource;

class ListExceptionLogGroups extends ListRecords
{
    protected static string $resource = ExceptionLogResource::class;

    protected function getTableFiltersFormColumns(): int
    {
        return 1;
    }

    protected function getTableFiltersFormWidth(): string
    {
        return '7xl';
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Model $record): string => route(
            'filament.admin.resources.exceptions.show',
            ['record' => $record, 'tenant' => \Filament\Facades\Filament::getTenant()->id]
        );
    }
}
