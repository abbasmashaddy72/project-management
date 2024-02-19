<?php

namespace App\Filament\Resources\TicketStatusResource\Pages;

use App\Models\TicketStatus;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TicketStatusResource;

class CreateTicketStatus extends CreateRecord
{
    protected static string $resource = TicketStatusResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->is_default) {
            TicketStatus::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
