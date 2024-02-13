<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use Filament\Actions;
use App\Models\ContractType;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ContractTypeResource;

class EditContractType extends EditRecord
{
    protected static string $resource = ContractTypeResource::class;

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
            ContractType::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
