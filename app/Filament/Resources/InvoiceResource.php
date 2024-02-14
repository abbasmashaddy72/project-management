<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\InvoiceStatus;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('Project Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Fieldset::make('Invoice Details')->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->required()
                            ->default(fn () => Invoice::generateInvoiceNumber()),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required(),
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'name'),
                        Forms\Components\TextInput::make('vat')
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                            ->reactive()
                            ->numeric(),
                        Forms\Components\TextInput::make('subtotal')
                            ->readonly()
                            ->numeric(),
                        Forms\Components\TextInput::make('total')
                            ->readonly()
                            ->numeric(),
                        Forms\Components\Select::make('currency')
                            ->searchable()
                            ->required()
                            ->default('USD')
                            ->options(config('main.currencies')),
                        Forms\Components\Textarea::make('summary')
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpan(1),
                    Forms\Components\Fieldset::make('Dates')->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required(),
                        Forms\Components\DatePicker::make('issued_on'),
                        Forms\Components\DatePicker::make('due_on'),
                        Forms\Components\DatePicker::make('paid_on'),
                        Forms\Components\DatePicker::make('cancelled_on'),
                        Forms\Components\DatePicker::make('reminded_on'),
                        Forms\Components\Select::make('status_id')
                            ->label(__('Invoice status'))
                            ->searchable()
                            ->options(fn () => InvoiceStatus::all()->pluck('name', 'id')->toArray())
                            ->default(fn () => InvoiceStatus::where('is_default', true)->first()?->id)
                            ->required(),
                    ])->columns(2)->columnSpan(1),
                ])->columns(2),
                Forms\Components\Fieldset::make('Invoice Items')->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->reorderableWithButtons()
                        ->reorderable()
                        ->live()
                        ->schema([
                            Forms\Components\Toggle::make('is_vat')
                                ->reactive(),
                            Forms\Components\TextInput::make('vat')
                                ->default(0)
                                ->readonly(fn (Get $get) => !$get('is_vat'))
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemsAmount($get, $set))
                                ->numeric(),
                            Forms\Components\Select::make('currency')
                                ->searchable()
                                ->required()
                                ->default('USD')
                                ->options(config('main.currencies')),
                            Forms\Components\TextInput::make('unit_price')
                                ->reactive()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemsAmount($get, $set))
                                ->numeric(),
                            Forms\Components\TextInput::make('quantity')
                                ->reactive()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemsAmount($get, $set))
                                ->default(1)
                                ->numeric(),
                            Forms\Components\TextInput::make('amount')
                                ->numeric()
                                ->readonly()
                                ->required(),
                            Forms\Components\Select::make('sprint_id')
                                ->relationship('sprint', 'name'),
                            Forms\Components\Textarea::make('description')
                                ->columnSpanFull()
                                ->required(),
                        ])->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateTotals($get, $set);
                        })->deleteAction(
                            fn (Forms\Components\Actions\Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                        )->columnSpanFull()->columns(7),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('summary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issued_on')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_on')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_on')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cancelled_on')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reminded_on')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function calculateItemsAmount(Get $get, Set $set)
    {
        $unitPrice = $get('unit_price');
        $quantity = $get('quantity');
        $vatPercentage = $get('vat');

        // Check if any of the input values is empty
        if ($unitPrice === '' || $quantity === '' || $vatPercentage === '') {
            $set('amount', 0);
            return;
        }

        $amountWithoutVAT = $unitPrice * $quantity;

        // Check if VAT is not zero
        if ($vatPercentage !== 0) {
            $vatAmount = ($vatPercentage / 100) * $amountWithoutVAT;
            $totalAmount = $amountWithoutVAT + $vatAmount;
        } else {
            $totalAmount = $amountWithoutVAT;
        }

        $set('amount', $totalAmount);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        // Retrieve all selected items and remove empty rows
        $selectedItems = collect($get('items'))->filter(fn ($item) => !empty($item['amount']));

        // Calculate subtotal based on the selected items and quantities
        $subtotal = $selectedItems->sum('amount');

        // Calculate total including VAT
        $total = $subtotal;

        if ($get('vat') ?? 0 != 0) {
            $total += $subtotal * ($get('vat') / 100);
        }

        // Update the state with the new values
        $set('total', $total);
        $set('subtotal', $subtotal);
    }
}
