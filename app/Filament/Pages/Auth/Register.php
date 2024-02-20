<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        PhoneInput::make('contact_no')
                            ->required()
                            ->validateFor(lenient: true),
                        Forms\Components\TextInput::make('address')
                            ->required(),
                        Forms\Components\TextInput::make('zipcode')
                            ->required(),
                        Country::make('country')
                            ->searchable()
                            ->required(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}
