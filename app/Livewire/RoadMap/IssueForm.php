<?php

namespace App\Livewire\RoadMap;

use Filament\Forms;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Project;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\TicketType;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class IssueForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Project|null $project = null;
    public array $epics;
    public array $sprints;
    public ?array $data = [];

    public function mount()
    {
        $this->initProject($this->project?->id);
        if ($this->project?->status_type === 'custom') {
            $defaultStatus = TicketStatus::where('project_id', $this->project->id)
                ->where('is_default', true)
                ->first()
                ?->id;
        } else {
            $defaultStatus = TicketStatus::whereNull('project_id')
                ->where('is_default', true)
                ->first()
                ?->id;
        }
        $this->form->fill([
            'project_id' => $this->project?->id ?? null,
            'owner_id' => auth()->user()->id,
            'status_id' => $defaultStatus,
            'type_id' => TicketType::where('is_default', true)->first()?->id,
            'priority_id' => TicketPriority::where('is_default', true)->first()?->id
        ]);
    }

    public function render()
    {
        return view('livewire.road-map.issue-form');
    }

    public function initProject($projectId): void
    {
        if ($projectId) {
            $this->project = Project::where('id', $projectId)->first();
        } else {
            $this->project = null;
        }
        $this->epics = $this->project ? $this->project->epics->pluck('name', 'id')->toArray() : [];
        $this->sprints = $this->project ? $this->project->sprints->pluck('name', 'id')->toArray() : [];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make()->schema([
                Forms\Components\Select::make('project_id')
                    ->label(__('Project'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->options(
                        fn () => Project::where('owner_id', auth()->user()->id)
                            ->orWhereHas('users', function ($query) {
                                return $query->where('users.id', auth()->user()->id);
                            })->pluck('name', 'id')->toArray()
                    )
                    ->afterStateUpdated(fn (Get $get) => $this->initProject($get('project_id')))
                    ->required(),

                Forms\Components\Select::make('epic_id')
                    ->label(__('Epic'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->visible(fn () => $this->project && $this->project->type !== 'scrum')
                    ->options(fn () => $this->epics),

                Forms\Components\Select::make('sprint_id')
                    ->label(__('Sprint'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->visible(fn () => $this->project && $this->project->type === 'scrum')
                    ->columnSpan(2)
                    ->options(fn () => $this->sprints),

                Forms\Components\TextInput::make('name')
                    ->label(__('Ticket name'))
                    ->required(),

                Forms\Components\Select::make('owner_id')
                    ->label(__('Ticket owner'))
                    ->searchable()
                    ->preload()
                    ->options(fn () => User::all()->pluck('name', 'id')->toArray())
                    ->default(fn () => auth()->user()->id)
                    ->required(),

                Forms\Components\Select::make('responsible_id')
                    ->label(__('Ticket responsible'))
                    ->searchable()
                    ->preload()
                    ->options(fn () => User::all()->pluck('name', 'id')->toArray()),

                Forms\Components\Select::make('status_id')
                    ->label(__('Ticket status'))
                    ->searchable()
                    ->preload()
                    ->options(function ($get) {
                        if ($this->project?->status_type === 'custom') {
                            return TicketStatus::where('project_id', $this->project->id)
                                ->get()
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        return TicketStatus::whereNull('project_id')
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required(),

                Forms\Components\Select::make('type_id')
                    ->label(__('Ticket type'))
                    ->searchable()
                    ->preload()
                    ->options(fn () => TicketType::all()->pluck('name', 'id')->toArray())
                    ->required(),

                Forms\Components\Select::make('priority_id')
                    ->label(__('Ticket priority'))
                    ->searchable()
                    ->preload()
                    ->options(fn () => TicketPriority::all()->pluck('name', 'id')->toArray())
                    ->required(),

                Forms\Components\TextInput::make('estimation')
                    ->label(__('Estimation time'))
                    ->numeric(),
            ])->columns(4),

            Forms\Components\RichEditor::make('content')
                ->label(__('Ticket content'))
                ->required()
                ->columnSpanFull(),
        ])->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        Ticket::create($data);
        Notification::make()
            ->title('Ticket successfully saved')
            ->success()
            ->send();
        $this->cancel(true);
    }

    public function cancel($refresh = false): void
    {
        $this->emit('closeTicketDialog', $refresh);
    }
}
