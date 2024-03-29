<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\ContractType;
use App\Models\ProjectStatus;
use Filament\Facades\Filament;
use App\Models\ProjectFavorite;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Projects');
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
            Forms\Components\Grid::make()->schema([
                Forms\Components\Fieldset::make('Project Details')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Project name'))
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('ticket_prefix', Str::limit(Str::slug($state), 3, ''))),

                    Forms\Components\TextInput::make('ticket_prefix')
                        ->label(__('Ticket prefix'))
                        ->unique(Project::class, column: 'ticket_prefix', ignoreRecord: true)
                        ->disabled(fn ($record) => $record && $record->tickets()->count() != 0)
                        ->required(),

                    Forms\Components\Select::make('owner_id')
                        ->label(__('Project owner'))
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

                    Forms\Components\Select::make('client_id')
                        ->label(__('Project Client'))
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
                ])->columnSpan(2)->columns(2),

                Forms\Components\Fieldset::make('Status Details')->schema([
                    Forms\Components\Select::make('type')
                        ->label(__('Project type'))
                        ->searchable()
                        ->preload()
                        ->options([
                            'kanban' => __('Kanban'),
                            'scrum' => __('Scrum')
                        ])
                        ->reactive()
                        ->default(fn () => 'kanban')
                        ->helperText(function ($state) {
                            if ($state === 'kanban') {
                                return __('Streamline your project progress by managing powerful board issues.');
                            } elseif ($state === 'scrum') {
                                return __('Reach project goals with a board, backlog, and roadmap.');
                            }
                            return '';
                        })
                        ->required(),

                    Forms\Components\Select::make('status_type')
                        ->label(__('Statuses configuration'))
                        ->helperText(
                            __('Configure project-specific statuses when choosing a custom type.')
                        )
                        ->searchable()
                        ->preload()
                        ->options([
                            'default' => __('Default'),
                            'custom' => __('Custom configuration')
                        ])
                        ->default(fn () => 'default')
                        ->disabled(fn ($record) => $record && $record->tickets()->count())
                        ->required(),

                    Forms\Components\Select::make('status_id')
                        ->label(__('Project status'))
                        ->searchable()
                        ->preload()
                        ->options(fn () => ProjectStatus::all()->pluck('name', 'id')->toArray())
                        ->default(fn () => ProjectStatus::where('is_default', true)->first()?->id)
                        ->required(),
                ])->columnSpan(2)->columns(2),

                Forms\Components\Fieldset::make('Contract Details')->schema([
                    Forms\Components\Select::make('contract_type')
                        ->label(__('Contract Type'))
                        ->searchable()
                        ->preload()
                        ->options(fn () => ContractType::all()->pluck('name', 'id')->toArray())
                        ->default(fn () => ContractType::where('is_default', true)->first()?->id)
                        ->required(),

                    Forms\Components\TextInput::make('amount')
                        ->label(__('Amount'))
                        ->required(),
                ])->columnSpan(1)->columns(1),

                Forms\Components\Fieldset::make('Status Details')->schema([
                    Forms\Components\RichEditor::make('description')
                        ->label(__('Project description'))
                        ->columnSpanFull(),
                ])->columnSpan(3)->columns(1),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Project name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('Project owner'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status.name')
                    ->label(__('Project status'))
                    ->formatStateUsing(fn ($record) => new HtmlString('
                            <div class="flex items-center gap-2">
                                <span class="relative flex w-6 h-6 rounded-md filament-tables-color-column"
                                    style="background-color: ' . $record->status->color . '"></span>
                                <span>' . $record->status->name . '</span>
                            </div>
                        '))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('users.name')
                    ->badge()
                    ->label(__('Affected users'))
                    ->limit(2),

                Tables\Columns\TextColumn::make('contract.name')
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->money(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => ucwords($state))
                    ->colors([
                        'secondary' => 'kanban',
                        'warning' => 'scrum',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label(__('Owner'))
                    ->multiple()
                    ->options(fn () => User::all()->pluck('name', 'id')->toArray()),

                Tables\Filters\SelectFilter::make('status_id')
                    ->label(__('Status'))
                    ->multiple()
                    ->options(fn () => ProjectStatus::all()->pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('kanban')
                    ->label(
                        fn ($record)
                        => ($record->type === 'scrum' ? __('Scrum board') : __('Kanban board'))
                    )
                    ->icon('heroicon-o-view-columns')
                    ->color('secondary')
                    ->url(function ($record) {
                        if ($record->type === 'scrum') {
                            return route('filament.admin.pages.scrum.{project?}', ['project' => $record->id, 'tenant' => \Filament\Facades\Filament::getTenant()->id]);
                        }
                        return route('filament.admin.pages.kanban.{project?}', ['project' => $record->id, 'tenant' => \Filament\Facades\Filament::getTenant()->id]);
                    }),

                Tables\Actions\Action::make('favorite')
                    ->hiddenLabel()
                    ->icon('heroicon-o-star')
                    ->color(fn ($record) => auth()->user()->favoriteProjects()
                        ->where('projects.id', $record->id)->count() ? 'success' : 'default')
                    ->action(function ($record) {
                        $projectId = $record->id;
                        $projectFavorite = ProjectFavorite::where('project_id', $projectId)
                            ->where('user_id', auth()->user()->id)
                            ->first();
                        if ($projectFavorite) {
                            $projectFavorite->delete();
                        } else {
                            ProjectFavorite::create([
                                'project_id' => $projectId,
                                'user_id' => auth()->user()->id
                            ]);
                        }
                        Notification::make()
                            ->title(__('Project updated'))
                            ->success()
                            ->send();
                    }),

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
            RelationManagers\SprintsRelationManager::class,
            RelationManagers\TicketsRelationManager::class,
            RelationManagers\UsersRelationManager::class,
            RelationManagers\StatusesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
