<?php

namespace App\Filament\Resources\TicketStatusResource\Pages;

use Filament\Actions;
use App\Models\TicketStatus;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TicketStatusResource;

class EditTicketStatus extends EditRecord
{
    protected static string $resource = TicketStatusResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->is_default) {
            TicketStatus::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
