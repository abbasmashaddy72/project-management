<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use App\Helpers\KanbanScrumHelper;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class Kanban extends Page implements HasForms
{
    use  KanbanScrumHelper;

    public static ?string $navigationIcon = 'heroicon-o-view-columns';

    public static ?string $slug = 'kanban/{project}';

    public static string $view = 'filament.pages.kanban';

    public static bool $shouldRegisterNavigation = false;

    public $listeners = [
        'recordUpdated',
        'closeTicketDialog'
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        if ($this->project->type === 'scrum') {
            $this->redirect(route('filament.admin.pages.scrum/{project}', ['project' => $project]));
        } elseif (
            $this->project->owner_id != auth()->user()->id
            &&
            !$this->project->users->where('id', auth()->user()->id)->count()
        ) {
            abort(403);
        }
        $this->form->fill();
    }

    public function getActions(): array
    {
        return [
            Action::make('refresh')
                ->button()
                ->label(__('Refresh'))
                ->color('secondary')
                ->action(function () {
                    $this->getRecords();
                    Notification::make()
                        ->title(__('Kanban board updated'))
                        ->success()
                        ->send();
                })
        ];
    }

    public function getHeading(): string|Htmlable
    {
        return $this->kanbanHeading();
    }

    public function getFormSchema(): array
    {
        return $this->formSchema();
    }
}
