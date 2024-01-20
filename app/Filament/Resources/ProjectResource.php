<?php

namespace App\Filament\Resources;

use App\Exports\ProjectHoursExport;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\ProjectFavorite;
use App\Models\ProjectStatus;
use App\Models\Ticket;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
        return __('Management');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Project name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('ticket_prefix')
                    ->label(__('Ticket prefix'))
                    ->maxLength(3)
                    ->unique(Project::class, column: 'ticket_prefix', ignoreRecord: true)
                    ->disabled(
                        fn ($record) => $record && $record->tickets()->count() != 0
                    )
                    ->required(),

                Forms\Components\Select::make('owner_id')
                    ->label(__('Project owner'))
                    ->searchable()
                    ->options(fn () => User::all()->pluck('name', 'id')->toArray())
                    ->default(fn () => auth()->user()->id)
                    ->required(),

                Forms\Components\Select::make('status_id')
                    ->label(__('Project status'))
                    ->searchable()
                    ->options(fn () => ProjectStatus::all()->pluck('name', 'id')->toArray())
                    ->default(fn () => ProjectStatus::where('is_default', true)->first()?->id)
                    ->required(),
            ])->columns(2),

            Forms\Components\RichEditor::make('description')
                ->label(__('Project description'))
                ->columnSpanFull(),

            Forms\Components\Select::make('type')
                ->label(__('Project type'))
                ->searchable()
                ->options([
                    'kanban' => __('Kanban'),
                    'scrum' => __('Scrum')
                ])
                ->reactive()
                ->default(fn () => 'kanban')
                ->helperText(function ($state) {
                    if ($state === 'kanban') {
                        return __('Display and move your project forward with issues on a powerful board.');
                    } elseif ($state === 'scrum') {
                        return __('Achieve your project goals with a board, backlog, and roadmap.');
                    }
                    return '';
                })
                ->required(),

            Forms\Components\Select::make('status_type')
                ->label(__('Statuses configuration'))
                ->helperText(
                    __('If custom type selected, you need to configure project specific statuses')
                )
                ->searchable()
                ->options([
                    'default' => __('Default'),
                    'custom' => __('Custom configuration')
                ])
                ->default(fn () => 'default')
                ->disabled(fn ($record) => $record && $record->tickets()->count())
                ->required(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cover')
                    ->label(__('Cover image'))
                    ->formatStateUsing(fn ($state) => new HtmlString('
                            <div style=\'background-image: url("' . $state . '")\'
                                 class="w-8 h-8 bg-center bg-no-repeat bg-cover"></div>
                        ')),

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

                Tables\Columns\TextColumn::make('type')
                    ->badge()
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

                Tables\Actions\Action::make('favorite')
                    ->label('')
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
                        Filament::notify('success', __('Project updated'));
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('kanban')
                        ->label(
                            fn ($record)
                            => ($record->type === 'scrum' ? __('Scrum board') : __('Kanban board'))
                        )
                        ->icon('heroicon-o-view-columns')
                        ->color('secondary')
                        ->url(function ($record) {
                            if ($record->type === 'scrum') {
                                return route('filament.admin.pages.scrum.{project}', ['project' => $record->id]);
                            } else {
                                return route('filament.admin.pages.kanban.{project}', ['project' => $record->id]);
                            }
                        }),
                ])->color('secondary'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SprintsRelationManager::class,
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