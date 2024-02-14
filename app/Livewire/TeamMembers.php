<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\TeamInvitation;
use Filament\Facades\Filament;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Pages\Concerns\InteractsWithHeaderActions;

class TeamMembers extends Component implements HasActions, HasTable, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithHeaderActions;
    use InteractsWithTable;

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('invite')
                ->color('primary')
                ->size('lg')
                ->form(fn (Form $form) => $form
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->autofocus()
                            ->required(),
                    ]))
                ->requiresConfirmation()
                ->modalHeading('Invite Team Member')
                ->modalDescription('Enter the email address of the person you would like to invite to your team.')
                ->modalSubmitActionLabel('Send Invitation')
                ->action(fn (array $data) => $this->invite($data)),
        ];
    }

    private function invite($data)
    {
        if ($data['email']) {
            // Check if the user with the given email already exists
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                // User already exists, attach them to the team if not already attached
                $user->teams()->syncWithoutDetaching(Filament::getTenant()->id);

                Notification::make()
                    ->title('User Already Existed and Attached to the Team')
                    ->success()
                    ->send();
            } else {
                // User doesn't exist, create a TeamInvitation record
                TeamInvitation::create([
                    'team_id' => Filament::getTenant()->id,
                    'email' => $data['email'],
                ]);
            }
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->whereHas('teams', function ($query) {
                    $query->where('team_id', Filament::getTenant()->id);
                })
            )
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->formatStateUsing(function ($state) {
                        return $state->diffForHumans();
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.team-members');
    }
}
