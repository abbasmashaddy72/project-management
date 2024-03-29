<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\TicketStatus;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class Kanban extends KanbanBoard
{
    // Custom
    public Project|null $project = null;
    public $users = [];
    public $types = [];
    public $priorities = [];
    public $includeNotAffectedTickets = false;
    public bool $ticket = false;

    // Slug
    public static ?string $slug = 'kanban/{project?}';
    public static bool $shouldRegisterNavigation = false;

    // Package
    protected static string $recordTitleAttribute = 'name';
    protected static string $recordStatusAttribute = 'status_id';
    protected static string $model = Ticket::class;
    public bool $disableEditModal = true;

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
        $query = Ticket::query()->ordered();
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

        return $query->get()->map(function (Ticket $item) {
            return $item;
        });
    }

    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Ticket::find($recordId)->update(['status_id' => $status]);
        Ticket::setNewOrder($toOrderedIds);
        Notification::make()
            ->success()
            ->title(__('Status Updated'))
            ->send();
    }

    public function onSortChanged(int $recordId, string $status, array $orderedIds): void
    {
        Ticket::setNewOrder($orderedIds);
        Notification::make()
            ->success()
            ->title(__('Tickets Sorted'))
            ->send();
    }

    public function getHeader(): ?View
    {
        return view('filament.kanban.kanban-header');
    }
}
