<?php

declare(strict_types=1);

namespace App\Livewire\Timesheet;

use Filament\Tables;
use App\Models\Ticket;
use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class TimeLogged extends Component implements HasTable
{
    use InteractsWithTable;

    public Ticket $ticket;

    protected function getFormModel(): Model|string|null
    {
        return $this->ticket;
    }

    protected function getTableQuery(): Builder
    {
        return $this->ticket->hours()->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label(__('Owner'))
                ->sortable()
                ->formatStateUsing(fn ($record) => view('components.user-avatar', ['user' => $record->user]))
                ->searchable(),

            Tables\Columns\TextColumn::make('value')
                ->label(__('Hours'))
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('comment')
                ->label(__('Comment'))
                ->limit(50)
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('activity.name')
                ->label(__('Activity'))
                ->sortable(),

            Tables\Columns\TextColumn::make('ticket.name')
                ->label(__('Ticket'))
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label(__('Created at'))
                ->dateTime()
                ->sortable()
                ->searchable(),
        ];
    }
}
