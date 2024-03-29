<?php

namespace App\Filament\Pages;

use App\Models\Team;
use Filament\Facades\Filament;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TeamSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Team';
    protected static string $view = 'filament.pages.team-settings';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 3;

    public Team $team;

    public ?array $data = [];

    public function mount(): void
    {
        $this->team = Filament::getTenant();
        $this->form->fill([
            'name' => $this->team->name,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Team Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->autofocus()
                            ->required(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->team);
    }

    public function create()
    {
        $this->team->update($this->form->getState());
        Notification::make()
            ->title('Team updated')
            ->body('Your team information has been updated.')
            ->send();
    }
}
