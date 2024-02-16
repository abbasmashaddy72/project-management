<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\TicketStatus;
use App\Helpers\KanbanScrumHelper;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use InvadersXX\FilamentKanbanBoard\Pages\FilamentKanbanBoard;

class Kanban extends FilamentKanbanBoard
{
    public Project|null $project = null;

    public $users = [];
    public $types = [];
    public $priorities = [];
    public $includeNotAffectedTickets = false;

    public bool $ticket = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static ?string $slug = 'kanban/{project?}';

    public bool $sortable = true;
    public bool $sortableBetweenStatuses = true;

    public bool $recordClickEnabled = true;

    protected static string $view = 'filament-kanban-board::kanban-board';
    public string $kanbanBoardView = 'filament-kanban-board::kanban-header';
    public string $kanbanView = 'filament-kanban-board::kanban';
    public string $kanbanHeaderView = 'filament-kanban-board::kanban-header';
    public string $kanbanFooterView = 'filament-kanban-board::kanban-footer';
    public string $recordView = 'filament-kanban-board::record';
    public string $recordContentView = 'filament-kanban-board::record-content';
    public string $sortableView = 'filament-kanban-board::sortable';

    public function statuses(): Collection
    {
        $query = TicketStatus::query();
        if ($this->project && $this->project->status_type === 'custom') {
            $query->where('project_id', $this->project->id);
        } else {
            $query->whereNull('project_id');
        }
        return $query->orderBy('order')
            ->get()
            ->map(function ($item) {
                $query = Ticket::query();
                if ($this->project) {
                    $query->where('project_id', $this->project->id);
                }
                $query->where('status_id', $item->id);
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'color' => $item->color,
                    'size' => $query->count(),
                    'add_ticket' => $item->is_default
                ];
            });
    }

    public function records(): Collection
    {
        $this->project = Project::findOrFail(request('id'));

        $query = Ticket::query();
        if ($this->project->type === 'scrum') {
            $query->where('sprint_id', $this->project->currentSprint->id);
        }
        $query->with(['project', 'owner', 'responsible', 'status', 'type', 'priority', 'epic']);
        $query->where('project_id', $this->project->id);
        if (sizeof($this->users)) {
            $query->where(function ($query) {
                return $query->whereIn('owner_id', $this->users)
                    ->orWhereIn('responsible_id', $this->users);
            });
        }
        if (sizeof($this->types)) {
            $query->whereIn('type_id', $this->types);
        }
        if (sizeof($this->priorities)) {
            $query->whereIn('priority_id', $this->priorities);
        }
        if ($this->includeNotAffectedTickets) {
            $query->whereNull('responsible_id');
        }
        $query->where(function ($query) {
            return $query->where('owner_id', auth()->user()->id)
                ->orWhere('responsible_id', auth()->user()->id)
                ->orWhereHas('project', function ($query) {
                    return $query->where('owner_id', auth()->user()->id)
                        ->orWhereHas('users', function ($query) {
                            return $query->where('users.id', auth()->user()->id);
                        });
                });
        });
        return $query->get()
            ->map(fn (Ticket $item) => [
                'id' => $item->id,
                'code' => $item->code,
                'title' => $item->name,
                'owner' => $item->owner,
                'type' => $item->type,
                'responsible' => $item->responsible,
                'project' => $item->project,
                'status' => $item->status->id,
                'priority' => $item->priority,
                'epic' => $item->epic,
                'relations' => $item->relations,
                'totalLoggedHours' => $item->totalLoggedSeconds ? $item->totalLoggedHours : null
            ]);
    }

    protected function styles(): array
    {
        return [
            'wrapper' => 'w-full h-full flex space-x-4 overflow-x-auto',
            'kanbanWrapper' => 'h-full flex-1',
            'kanban' => 'border border-gray-150 flex flex-col h-full rounded',
            'kanbanHeader' => 'px-3 py-3 font-bold text-xs w-full border-b border-gray-150',
            'kanbanFooter' => '',
            'kanbanRecords' => 'space-y-4 p-3 flex-1 overflow-y-auto w-64',
            'record' => 'bg-white dark:bg-gray-800 p-4 border border-gray-150 rounded cursor-pointer w-62 hover:bg-gray-50 hover:shadow-lg',
            'recordContent' => 'w-full',
        ];
    }

    public function onStatusSorted($recordId, $statusId, $orderedIds): void
    {
        Ticket::setNewOrder($orderedIds);
        Notification::make()
            ->success()
            ->title(__('Tickets Sorted'))
            ->send();
    }

    public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds): void
    {
        Ticket::find($recordId)->update(['status_id' => $statusId]);
        Ticket::setNewOrder($toOrderedIds);
        Notification::make()
            ->success()
            ->title(__('Status Updated'))
            ->send();
    }

    public function getHeader(): ?View
    {
        return view('filament.kanban.top-header');
    }
}
