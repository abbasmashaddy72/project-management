<?php

namespace App\Filament\Resources;

use Exception;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use App\Contracts\SiteVigilanceSite;
use App\Repositories\SiteRepository;
use App\Repositories\UptimeCheckRepository;
use App\Repositories\SslCertificateCheckRepository;
use App\Filament\Resources\SiteResource\Pages\EditSite;
use App\Filament\Resources\SiteResource\Pages\ListSites;
use App\Filament\Resources\SiteResource\Pages\CreateSite;

class SiteResource extends Resource
{
    protected static ?string $slug = 'sites';

    protected static ?string $modelLabel = 'Site';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Site Vigilance';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @throws Exception
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('url')
                    ->unique(ignorable: fn (?SiteVigilanceSite $record): ?SiteVigilanceSite => $record)
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('max_request_duration_ms')
                    ->when(UptimeCheckRepository::isEnabled())
                    ->label('Max. request duration')
                    ->default(1000)
                    ->numeric()
                    ->minValue(0)
                    ->suffix('ms')
                    ->required(),
                Fieldset::make('Check Enabled')
                    ->when(UptimeCheckRepository::isEnabled() || SslCertificateCheckRepository::isEnabled())
                    ->schema([
                        Toggle::make('uptime_check_enabled')
                            ->when(UptimeCheckRepository::isEnabled())
                            ->label('Uptime check'),
                        Toggle::make('ssl_certificate_check_enabled')
                            ->when(SslCertificateCheckRepository::isEnabled())
                            ->label('SSL certificate check'),
                    ]),
                Fieldset::make('Server Monitoring')
                    ->schema([
                        Toggle::make('server_monitoring_notification_enabled')
                            ->label('Alert Notification Enabled')
                            ->inline(false)
                            ->columnSpan('full'),
                        TextInput::make('cpu_limit')
                            ->label('CPU load is above (%)')
                            ->numeric()
                            ->default(80)
                            ->minValue(1)
                            ->maxValue(100),
                        TextInput::make('ram_limit')
                            ->label('Memory usage is above (%)')
                            ->numeric()
                            ->default(80)
                            ->minValue(1)
                            ->maxValue(100),
                        TextInput::make('disk_limit')
                            ->label('Disk usage is above (%)')
                            ->numeric()
                            ->default(80)
                            ->minValue(1)
                            ->maxValue(100),
                    ])
                    ->columns(3),
                Fieldset::make('API Token')
                    ->schema([
                        Toggle::make('api_token_enabled')
                            ->reactive()
                            ->columnSpan('full')
                            ->label('Enabled')
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $token = Str::random(60);
                                    $set('api_token', $token);
                                    $set('api_token_input', $token);
                                } else {
                                    $set('api_token', null);
                                    $set('api_token_input', null);
                                }
                            }),
                        Hidden::make('api_token'),
                        TextInput::make('api_token_input')
                            ->disabled()
                            ->columnSpan('full')
                            ->hiddenLabel()
                            ->suffixAction(
                                Action::make('regenerate')
                                    ->disabled(function (CreateSite|EditSite $livewire) {
                                        return !$livewire->data['api_token_enabled'];
                                    })
                                    ->icon('heroicon-s-arrow-path')
                                    ->action(
                                        function (CreateSite|EditSite $livewire) {
                                            if ($livewire->data['api_token_enabled']) {
                                                $token = Str::random(60);
                                                $livewire->data['api_token'] = $token;
                                                $livewire->data['api_token_input'] = $token;
                                            }
                                        }
                                    )
                                    ->requiresConfirmation()
                            ),
                    ]),

                Toggle::make('down_for_maintenance')
                    ->columnSpan('full')
                    ->label('Down for maintenance'),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModel(): string
    {
        return SiteRepository::resolveModelClass();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }
}
