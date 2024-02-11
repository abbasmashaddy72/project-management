<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TicketPriority;
use Filament\Resources\Resource;
use App\Filament\Resources\TicketPriorityResource\Pages;

class TicketPriorityResource extends Resource
{
    protected static ?string $model = TicketPriority::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?int $navigationSort = 4;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationLabel(): string
    {
        return __('Ticket priorities');
    }

    public static function getPluralLabel(): ?string
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Referential');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Priority name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\ColorPicker::make('color')
                            ->label(__('Priority color'))
                            ->required(),

                        Forms\Components\Checkbox::make('is_default')
                            ->label(__('Default priority'))
                            ->helperText(
                                __('If checked, this priority will be automatically affected to new tickets')
                            ),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('Priority color'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Priority name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default priority'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
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
            'index' => Pages\ListTicketPriorities::route('/'),
            'create' => Pages\CreateTicketPriority::route('/create'),
            'view' => Pages\ViewTicketPriority::route('/{record}'),
            'edit' => Pages\EditTicketPriority::route('/{record}/edit'),
        ];
    }
}
