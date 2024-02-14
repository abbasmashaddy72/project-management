<?php

namespace App\Filament\Resources\InvoiceStatusResource\Pages;

use App\Filament\Resources\InvoiceStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoiceStatus extends ViewRecord
{
    protected static string $resource = InvoiceStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
