<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class ValidateAccount extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;

    public function mount()
    {
        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.validate-account');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->confirmed()
                    ->label(__('Account password'))
                    ->placeholder(__('Choose your account password')),

                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->label(__('Password confirmation'))
                    ->placeholder(__('Confirm your chosen password')),
            ]);
    }

    public function validateAccount(): void
    {
        $data = $this->form->getState();
        $this->user->creation_token = null;
        $this->user->password = bcrypt($data['password']);
        $this->user->email_verified_at = now();
        $this->user->save();
        auth()->login($this->user);
        Notification::make()
            ->title(__('Account verified'))
            ->success()
            ->send();
        redirect()->to(route('filament.admin.pages.dashboard'));
    }
}
