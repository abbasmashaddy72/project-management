<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Board extends Page implements HasForms
{
    public static ?string $navigationIcon = 'heroicon-o-view-columns';

    public static string $view = 'filament.pages.board';

    public static ?string $slug = 'board';

    public static ?int $navigationSort = 4;

    public function getSubheading(): string|Htmlable|null
    {
        return __("In this section you can choose one of your projects to show it's Scrum or Kanban board");
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function getNavigationLabel(): string
    {
        return __('Board');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public function getFormSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Grid::make()
                        ->columns(1)
                        ->schema([
                            Select::make('project')
                                ->label(__('Project'))
                                ->required()
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(fn () => $this->search())
                                ->helperText(__("Choose a project to show it's board"))
                                ->options(fn () => Project::where('owner_id', auth()->user()->id)
                                    ->orWhereHas('users', function ($query) {
                                        return $query->where('users.id', auth()->user()->id);
                                    })->pluck('name', 'id')->toArray()),
                        ]),
                ]),
        ];
    }

    public function search(): void
    {
        $data = $this->form->getState();
        $project = Project::find($data['project']);
        if ($project->type === "scrum") {
            $this->redirect(route('filament.pages.scrum/{project}', ['project' => $project]));
        } else {
            $this->redirect(route('filament.pages.kanban/{project}', ['project' => $project]));
        }
    }
}
