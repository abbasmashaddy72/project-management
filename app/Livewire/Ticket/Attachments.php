<?php

namespace App\Livewire\Ticket;

use App\Models\Ticket;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Livewire\WithFileUploads;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attachments extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Ticket $ticket;

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): BelongsToMany => $this->ticket->medias())
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    // ->simpleLightbox(fn (Model $record) => asset($record->path))
                    ->searchable(),

                TextColumn::make('size')
                    ->label(__('Size'))
                    ->sortable()
                    ->formatStateUsing(fn (Model $record): string => self::bytesToHuman($record->size))
                    ->searchable(),

                TextColumn::make('ext')
                    ->label(__('EXT'))
                    ->formatStateUsing(fn (Model $record): string => strtoupper($record->ext))
                    ->sortable()
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('download')
                    ->url(fn (Model $record): string => asset($record->path))
                    ->icon('heroicon-m-cloud-arrow-down')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.ticket.attachments');
    }
}
