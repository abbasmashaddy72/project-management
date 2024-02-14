<?php

namespace App\Filament\Resources\InvoiceStatusResource\Pages;

use App\Filament\Resources\InvoiceStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceStatuses extends ListRecords
{
    protected static string $resource = InvoiceStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
