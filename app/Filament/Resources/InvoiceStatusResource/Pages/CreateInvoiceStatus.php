<?php

namespace App\Filament\Resources\InvoiceStatusResource\Pages;

use App\Models\InvoiceStatus;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InvoiceStatusResource;

class CreateInvoiceStatus extends CreateRecord
{
    protected static string $resource = InvoiceStatusResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->is_default) {
            InvoiceStatus::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
