<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use App\Helpers\KanbanScrumHelper;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Support\Htmlable;

class Scrum extends Page implements HasForms
{
    use  KanbanScrumHelper;

    public static ?string $navigationIcon = 'heroicon-o-view-columns';

    public static ?string $slug = 'scrum/{project}';

    public static string $view = 'filament.pages.scrum';

    public static bool $shouldRegisterNavigation = false;

    public $listeners = [
        'recordUpdated',
        'closeTicketDialog'
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        if ($this->project->type !== 'scrum') {
            $this->redirect(route('filament.admin.pages.kanban.{project}', ['project' => $project, 'tenant' => \Filament\Facades\Filament::getTenant()->id]));
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
            Action::make('manage-sprints')
                ->button()
                ->visible(fn () => $this->project->currentSprint)
                ->label(__('Manage sprints'))
                ->color('primary')
                ->url(route('filament.resources.projects.edit', $this->project)),

            Action::make('refresh')
                ->button()
                ->visible(fn () => $this->project->currentSprint)
                ->label(__('Refresh'))
                ->color('secondary')
                ->action(function () {
                    $this->getRecords();
                    Filament::notify('success', __('Kanban board updated'));
                }),
        ];
    }

    public function getHeading(): string|Htmlable
    {
        return $this->scrumHeading();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->scrumSubHeading();
    }

    public function getFormSchema(): array
    {
        return $this->formSchema();
    }
}
