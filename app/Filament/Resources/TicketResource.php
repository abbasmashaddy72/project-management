<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Epic;
use App\Models\User;
use Filament\Tables;
use App\Models\Ticket;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\TicketType;
use Filament\Tables\Table;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketRelation;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TicketResource\Pages;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Tickets');
    }

    public static function getPluralLabel(): ?string
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Project Management');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Fieldset::make('Project Details')->schema([
                    Forms\Components\Select::make('project_id')
                        ->label(__('Project'))
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function ($get, $set) {
                            $project = Project::where('id', $get('project_id'))->first();
                            if ($project?->status_type === 'custom') {
                                $set(
                                    'status_id',
                                    TicketStatus::where('project_id', $project->id)
                                        ->where('is_default', true)
                                        ->first()
                                        ?->id
                                );
                            } else {
                                $set(
                                    'status_id',
                                    TicketStatus::whereNull('project_id')
                                        ->where('is_default', true)
                                        ->first()
                                        ?->id
                                );
                            }
                        })
                        ->options(
                            fn () => Project::where('owner_id', auth()->user()->id)
                                ->orWhereHas('users', function ($query) {
                                    return $query->where('users.id', auth()->user()->id);
                                })->pluck('name', 'id')->toArray()
                        )
                        ->default(fn () => request()->get('project'))
                        ->required(),
                    Forms\Components\Select::make('epic_id')
                        ->label(__('Epic'))
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->options(function ($get, $set) {
                            return Epic::where('project_id', $get('project_id'))->pluck('name', 'id')->toArray();
                        }),
                ]),

                Forms\Components\Fieldset::make('Attachments')->schema([
                    CuratorPicker::make('medias')
                        ->hiddenLabel()
                        ->relationship('medias', 'id')
                        ->helperText(__('Here you can attach all files needed for this ticket'))
                        ->multiple()
                        ->tenantAware()
                        ->listDisplay(),
                ]),

                Forms\Components\Fieldset::make('Relations')->schema([
                    Forms\Components\Repeater::make('relations')
                        ->itemLabel(function (array $state) {
                            $ticketRelation = TicketRelation::find($state['id'] ?? 0);
                            if (!$ticketRelation) {
                                return null;
                            }
                            return __(config('system.tickets.relations.list.' . $ticketRelation->type))
                                . ' '
                                . $ticketRelation->relation->name
                                . ' (' . $ticketRelation->relation->code . ')';
                        })
                        ->hiddenLabel()
                        ->relationship()
                        ->collapsible()
                        ->collapsed()
                        ->defaultItems(0)
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label(__('Relation type'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options(config('system.tickets.relations.list'))
                                ->default(fn () => config('system.tickets.relations.default')),

                            Forms\Components\Select::make('relation_id')
                                ->label(__('Related ticket'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options(function ($livewire) {
                                    $query = Ticket::query();
                                    if ($livewire instanceof EditRecord && $livewire->record) {
                                        $query->where('id', '<>', $livewire->record->id);
                                    }
                                    return $query->get()->pluck('name', 'id')->toArray();
                                }),
                        ])->columns(2)
                        ->columnSpanFull(),
                ]),
            ]),
            Forms\Components\Group::make()->schema([
                Forms\Components\Fieldset::make('Ticket Details')->schema([
                    Forms\Components\TextInput::make('code')
                        ->label(__('Ticket code'))
                        ->visible(fn ($livewire) => !($livewire instanceof CreateRecord))
                        ->disabled(),

                    Forms\Components\TextInput::make('name')
                        ->label(__('Ticket name'))
                        ->required(),

                    Forms\Components\Fieldset::make('Responsible')->schema([
                        Forms\Components\Select::make('owner_id')
                            ->label(__('Ticket owner'))
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $teamId = Filament::getTenant()->id;

                                // Assuming you have a relationship between Team and User
                                $users = User::whereHas('teams', function ($query) use ($teamId) {
                                    $query->where('team_id', $teamId);
                                })->pluck('name', 'id')->toArray();

                                return $users;
                            })
                            ->default(fn () => auth()->user()->id)
                            ->required(),

                        Forms\Components\Select::make('responsible_id')
                            ->label(__('Ticket responsible'))
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $teamId = Filament::getTenant()->id;

                                // Assuming you have a relationship between Team and User
                                $users = User::whereHas('teams', function ($query) use ($teamId) {
                                    $query->where('team_id', $teamId);
                                })->pluck('name', 'id')->toArray();

                                return $users;
                            }),
                    ]),
                    Forms\Components\Fieldset::make('Status')->schema([
                        Forms\Components\Select::make('status_id')
                            ->label(__('Ticket status'))
                            ->searchable()
                            ->preload()
                            ->options(function ($get) {
                                $project = Project::where('id', $get('project_id'))->first();
                                if ($project?->status_type === 'custom') {
                                    return TicketStatus::where('project_id', $project->id)
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }
                                return TicketStatus::whereNull('project_id')
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->default(function ($get) {
                                $project = Project::where('id', $get('project_id'))->first();
                                if ($project?->status_type === 'custom') {
                                    return TicketStatus::where('project_id', $project->id)
                                        ->where('is_default', true)
                                        ->first()
                                        ?->id;
                                }
                                return TicketStatus::whereNull('project_id')
                                    ->where('is_default', true)
                                    ->first()
                                    ?->id;
                            })
                            ->required(),

                        Forms\Components\Select::make('type_id')
                            ->label(__('Ticket type'))
                            ->searchable()
                            ->preload()
                            ->options(fn () => TicketType::all()->pluck('name', 'id')->toArray())
                            ->default(fn () => TicketType::where('is_default', true)->first()?->id)
                            ->required(),

                        Forms\Components\Select::make('priority_id')
                            ->label(__('Ticket priority'))
                            ->searchable()
                            ->preload()
                            ->options(fn () => TicketPriority::all()->pluck('name', 'id')->toArray())
                            ->default(fn () => TicketPriority::where('is_default', true)->first()?->id)
                            ->required(),

                        Forms\Components\TextInput::make('estimation')
                            ->label(__('Estimation time'))
                            ->numeric(),
                    ]),
                ]),
            ]),

            Forms\Components\RichEditor::make('content')
                ->label(__('Ticket content'))
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function tableColumns(bool $withProject = true): array
    {
        $columns = [];
        if ($withProject) {
            $columns[] = Tables\Columns\TextColumn::make('project.name')
                ->label(__('Project'))
                ->sortable()
                ->searchable();
        }
        $columns = array_merge($columns, [
            Tables\Columns\TextColumn::make('name')
                ->label(__('Ticket name'))
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('owner.name')
                ->label(__('Owner'))
                ->sortable()
                ->formatStateUsing(fn ($record) => view('components.user-avatar', ['user' => $record->owner]))
                ->searchable(),

            Tables\Columns\TextColumn::make('responsible.name')
                ->label(__('Responsible'))
                ->sortable()
                ->formatStateUsing(fn ($record) => view('components.user-avatar', ['user' => $record->responsible]))
                ->searchable(),

            Tables\Columns\TextColumn::make('status.name')
                ->label(__('Status'))
                ->formatStateUsing(fn ($record) => new HtmlString('
                            <div class="flex items-center gap-2 mt-1">
                                <span class="relative flex w-6 h-6 rounded-md filament-tables-color-column"
                                    style="background-color: ' . $record->status->color . '"></span>
                                <span>' . $record->status->name . '</span>
                            </div>
                        '))
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('type.name')
                ->label(__('Type'))
                ->formatStateUsing(
                    fn ($record) => view('partials.filament.resources.ticket-type', ['state' => $record->type])
                )
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('priority.name')
                ->label(__('Priority'))
                ->formatStateUsing(fn ($record) => new HtmlString('
                            <div class="flex items-center gap-2 mt-1">
                                <span class="relative flex w-6 h-6 rounded-md filament-tables-color-column"
                                    style="background-color: ' . $record->priority->color . '"></span>
                                <span>' . $record->priority->name . '</span>
                            </div>
                        '))
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label(__('Created at'))
                ->dateTime()
                ->sortable()
                ->searchable(),
        ]);
        return $columns;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::tableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('Project'))
                    ->multiple()
                    ->options(fn () => Project::where('owner_id', auth()->user()->id)
                        ->orWhereHas('users', function ($query) {
                            return $query->where('users.id', auth()->user()->id);
                        })->pluck('name', 'id')->toArray()),

                Tables\Filters\SelectFilter::make('owner_id')
                    ->label(__('Owner'))
                    ->multiple()
                    ->options(function () {
                        $teamId = Filament::getTenant()->id;

                        // Assuming you have a relationship between Team and User
                        $users = User::whereHas('teams', function ($query) use ($teamId) {
                            $query->where('team_id', $teamId);
                        })->pluck('name', 'id')->toArray();

                        return $users;
                    }),

                Tables\Filters\SelectFilter::make('responsible_id')
                    ->label(__('Responsible'))
                    ->multiple()
                    ->options(function () {
                        $teamId = Filament::getTenant()->id;

                        // Assuming you have a relationship between Team and User
                        $users = User::whereHas('teams', function ($query) use ($teamId) {
                            $query->where('team_id', $teamId);
                        })->pluck('name', 'id')->toArray();

                        return $users;
                    }),

                Tables\Filters\SelectFilter::make('status_id')
                    ->label(__('Status'))
                    ->multiple()
                    ->options(fn () => TicketStatus::all()->pluck('name', 'id')->toArray()),

                Tables\Filters\SelectFilter::make('type_id')
                    ->label(__('Type'))
                    ->multiple()
                    ->options(fn () => TicketType::all()->pluck('name', 'id')->toArray()),

                Tables\Filters\SelectFilter::make('priority_id')
                    ->label(__('Priority'))
                    ->multiple()
                    ->options(fn () => TicketPriority::all()->pluck('name', 'id')->toArray()),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
