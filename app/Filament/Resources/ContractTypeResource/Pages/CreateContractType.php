<?php

namespace App\Filament\Resources\ContractTypeResource\Pages;

use App\Models\ContractType;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ContractTypeResource;

class CreateContractType extends CreateRecord
{
    protected static string $resource = ContractTypeResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->is_default) {
            ContractType::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
