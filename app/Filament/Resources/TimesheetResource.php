<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Activity;
use Filament\Forms\Form;
use App\Models\TicketHour;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Contracts\TimesheetService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\TimesheetResource\Pages;

class TimesheetResource extends Resource
{
    protected static ?string $model = TicketHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Timesheet');
    }

    public static function getPluralLabel(): ?string
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Select::make('activity_id')
                            ->label(__('Activity'))
                            ->searchable()
                            ->reactive()
                            ->options(function ($get, $set) {
                                return Activity::all()->pluck('name', 'id')->toArray();
                            }),
                        TextInput::make('value')
                            ->label(__('Time to log'))
                            ->numeric()
                            ->required(),

                        Textarea::make('comment')
                            ->label(__('Comment'))
                            ->rows(3),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.project.name')
                    ->label(__('Project Name'))
                    ->sortable()
                    ->description(fn (TicketHour $record): ?string => $record->comment)
                    ->searchable(),

                Tables\Columns\TextColumn::make('activity.name')
                    ->label(__('Activity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('ticket.name')
                    ->label(__('Ticket'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(__('Hours'))
                    ->sortable()
                    ->summarize(Sum::make())
                    ->formatStateUsing(fn (TicketHour $record): string => (new TimesheetService())->decimalToTime($record->value))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Owner'))
                    ->sortable()
                    ->formatStateUsing(fn ($record) => view('components.user-avatar', ['user' => $record->user]))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime('l d F Y')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimesheet::route('/'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
