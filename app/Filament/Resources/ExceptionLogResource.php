<?php

namespace App\Filament\Resources;

use Exception;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\ExceptionLogStatus;
use App\Filament\Tables\Columns\ExceptionColumn;
use App\Repositories\ExceptionLogGroupRepository;
use App\Filament\Resources\ExceptionLogResource\Pages\SiteExceptionLogs;
use App\Filament\Resources\ExceptionLogResource\Pages\ListExceptionLogGroups;

class ExceptionLogResource extends Resource
{
    protected static ?string $slug = 'exceptions';

    protected static ?string $modelLabel = 'Recent Exceptions';

    protected static ?string $navigationLabel = 'Exceptions';

    protected static ?string $navigationGroup = 'Site Vigilance';

    protected static ?int $navigationSort = 2;

    public static ?string $statusFilter;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ExceptionColumn::make('exceptions'),
                TextColumn::make('Events')
                    ->getStateUsing(function (Model $record) {
                        if (self::$statusFilter === null) {
                            return $record->exceptionLogs()->count();
                        }

                        return $record->exceptionLogs()
                            ->where('status', self::$statusFilter)
                            ->count();
                    }),
                TextColumn::make('first_seen')->dateTime()->sortable(),
                TextColumn::make('last_seen')->dateTime()->sortable(),
            ])
            ->defaultSort('last_seen', 'desc')
            ->filters([
                SelectFilter::make('sites')
                    ->relationship('site', 'name'),
                SelectFilter::make('status')
                    ->default(ExceptionLogStatus::UNRESOLVED->value)
                    ->options([
                        ExceptionLogStatus::UNRESOLVED->value => 'Unresolved',
                        ExceptionLogStatus::RESOLVED->value => 'Resolved',
                        ExceptionLogStatus::IGNORED->value => 'Ignored',
                        ExceptionLogStatus::REVIEWED->value => 'Reviewed',
                    ])->query(function (Builder $query, array $data): Builder {
                        if (!$data['value']) {
                            self::$statusFilter = null;

                            return $query;
                        }
                        self::$statusFilter = $data['value'];

                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, $value): Builder => $query->whereRelation('exceptionLogs', 'status', $value)
                            );
                    }),
            ], layout: FiltersLayout::AboveContent);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModel(): string
    {
        return ExceptionLogGroupRepository::resolveModelClass();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExceptionLogGroups::route('/'),
            'show' => SiteExceptionLogs::route('/show/{record}'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ExceptionLogGroupRepository::isEnabled();
    }
}
