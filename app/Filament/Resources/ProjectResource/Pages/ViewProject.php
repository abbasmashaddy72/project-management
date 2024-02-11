<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kanban')
                ->label(
                    fn ()
                    => ($this->record->type === 'scrum' ? __('Scrum board') : __('Kanban board'))
                )
                ->icon('heroicon-o-view-columns')
                ->color('secondary')
                ->url(function () {
                    if ($this->record->type === 'scrum') {
                        return route('filament.admin.pages.scrum.{project}', ['project' => $this->record->id, 'tenant' => \Filament\Facades\Filament::getTenant()->id]);
                    }
                    return route('filament.admin.pages.kanban.{project}', ['project' => $this->record->id, 'tenant' => \Filament\Facades\Filament::getTenant()->id]);
                }),

            Actions\EditAction::make(),
        ];
    }
}
