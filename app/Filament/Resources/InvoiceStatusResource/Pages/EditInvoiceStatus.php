<?php

namespace App\Filament\Resources\InvoiceStatusResource\Pages;

use Filament\Actions;
use App\Models\InvoiceStatus;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\InvoiceStatusResource;

class EditInvoiceStatus extends EditRecord
{
    protected static string $resource = InvoiceStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->is_default) {
            InvoiceStatus::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
