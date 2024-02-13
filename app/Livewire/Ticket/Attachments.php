<?php

namespace App\Livewire\Ticket;

use App\Models\Ticket;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Livewire\WithFileUploads;

class Attachments extends Component implements HasForms
{
    use InteractsWithForms, WithFileUploads;

    public Ticket $ticket;

    protected $listeners = [
        'filesUploaded'
    ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.ticket.attachments');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CuratorPicker::make('attachments')
                    ->helperText(__('Here you can attach all files needed for this ticket'))
                    ->multiple()
                    ->tenantAware()
                    ->listDisplay()
            ])
            ->statePath('data');
    }

    public function upload(): void
    {
        $this->ticket->update($this->form->getState());

        $this->emit('filesUploaded');
    }

    public function filesUploaded(): void
    {
        $this->ticket->refresh();
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title(__('Ticket attachments saved'))
            ->success()
            ->send();
    }
}
