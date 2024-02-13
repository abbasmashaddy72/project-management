<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Actions;
use App\Models\Activity;
use Filament\Forms\Form;
use App\Models\TicketHour;
use App\Models\TicketComment;
use App\Models\TicketSubscriber;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\TicketResource;
use Filament\Notifications\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;

class ViewTicket extends ViewRecord implements HasForms
{
    use InteractsWithForms {
        getFormStatePath as public;
    }

    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.tickets.view';

    public string $tab = 'comments';

    protected $listeners = ['doDeleteComment'];

    public $selectedCommentId;

    public function mount($record): void
    {
        parent::mount($record);
        $this->form->fill();
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('toggleSubscribe')
                ->label(
                    fn () => $this->record->subscribers()->where('users.id', auth()->user()->id)->count() ?
                        __('Unsubscribe')
                        : __('Subscribe')
                )
                ->color(
                    fn () => $this->record->subscribers()->where('users.id', auth()->user()->id)->count() ?
                        'danger'
                        : 'success'
                )
                ->icon('heroicon-o-bell')
                ->button()
                ->action(function () {
                    if (
                        $sub = TicketSubscriber::where('user_id', auth()->user()->id)
                        ->where('ticket_id', $this->record->id)
                        ->first()
                    ) {
                        $sub->delete();
                        Notification::make()
                            ->title(__('You unsubscribed from the ticket'))
                            ->success()
                            ->send();
                    } else {
                        TicketSubscriber::create([
                            'user_id' => auth()->user()->id,
                            'ticket_id' => $this->record->id
                        ]);
                        Notification::make()
                            ->title(__('You subscribed to the ticket'))
                            ->success()
                            ->send();
                    }
                    $this->record->refresh();
                }),
            Actions\Action::make('share')
                ->label(__('Share'))
                ->color('secondary')
                ->button()
                ->icon('heroicon-o-share')
                ->action(function () {
                    $url = route('filament.resources.tickets.share', ['ticket' => $this->record->id, 'tenant' => \Filament\Facades\Filament::getTenant()->id]);
                    $this->dispatch('shareTicket', ['url' => $url]);
                }),
            Actions\EditAction::make(),
            Actions\Action::make('logHours')
                ->label(__('Log time'))
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->modalWidth('sm')
                ->modalHeading(__('Log worked time'))
                ->modalDescription(__('Use the following form to add your worked time in this ticket.'))
                ->modalSubmitActionLabel(__('Log'))
                ->visible(fn () => in_array(
                    auth()->user()->id,
                    [$this->record->owner_id, $this->record->responsible_id]
                ))
                ->form([
                    TextInput::make('time')
                        ->label(__('Time to log'))
                        ->numeric()
                        ->required(),
                    Select::make('activity_id')
                        ->label(__('Activity'))
                        ->searchable()
                        ->reactive()
                        ->options(function ($get, $set) {
                            return Activity::all()->pluck('name', 'id')->toArray();
                        }),
                    Textarea::make('comment')
                        ->label(__('Comment'))
                        ->rows(3),
                ])
                ->action(function (Collection $records, array $data): void {
                    $value = $data['time'];
                    $comment = $data['comment'];
                    TicketHour::create([
                        'team_id' => \Filament\Facades\Filament::getTenant()->id,
                        'ticket_id' => $this->record->id,
                        'activity_id' => $data['activity_id'],
                        'user_id' => auth()->user()->id,
                        'value' => $value,
                        'comment' => $comment
                    ]);
                    $this->record->refresh();
                    Notification::make()
                        ->title(__('Time logged into ticket'))
                        ->success()
                        ->send();
                }),
            Actions\ActionGroup::make([])
                ->visible(fn () => (in_array(
                    auth()->user()->id,
                    [$this->record->owner_id, $this->record->responsible_id]
                )) || (
                    $this->record->watchers->where('id', auth()->user()->id)->count()
                    && $this->record->hours()->count()
                ))
                ->color('secondary'),
        ];
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('comment')
                    ->disableLabel()
                    ->placeholder(__('Type a new comment'))
                    ->required()
            ])->statePath('data');
    }

    public function submitComment(): void
    {
        $data = $this->form->getState();
        if ($this->selectedCommentId) {
            TicketComment::where('id', $this->selectedCommentId)
                ->update([
                    'content' => $data['comment']
                ]);
        } else {
            TicketComment::create([
                'user_id' => auth()->user()->id,
                'ticket_id' => $this->record->id,
                'content' => $data['comment']
            ]);
        }
        $this->record->refresh();
        $this->cancelEditComment();
        Notification::make()
            ->title(__('Comment saved'))
            ->success()
            ->send();
    }

    public function isAdministrator(): bool
    {
        return $this->record
            ->project
            ->users()
            ->where('users.id', auth()->user()->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'super_admin');
            })
            ->count() != 0;
    }

    public function editComment(int $commentId): void
    {
        $this->form->fill([
            'comment' => $this->record->comments->where('id', $commentId)->first()?->content
        ]);
        $this->selectedCommentId = $commentId;
    }

    public function deleteComment(int $commentId): void
    {
        Notification::make()
            ->warning()
            ->title(__('Delete confirmation'))
            ->body(__('Are you sure you want to delete this comment?'))
            ->actions([
                Action::make('confirm')
                    ->label(__('Confirm'))
                    ->color('danger')
                    ->button()
                    ->close()
                    ->emit('doDeleteComment', compact('commentId')),
                Action::make('cancel')
                    ->label(__('Cancel'))
                    ->close()
            ])
            ->persistent()
            ->send();
    }

    public function doDeleteComment(int $commentId): void
    {
        TicketComment::where('id', $commentId)->delete();
        $this->record->refresh();
        Notification::make()
            ->title(__('Comment deleted'))
            ->success()
            ->send();
    }

    public function cancelEditComment(): void
    {
        $this->form->fill();
        $this->selectedCommentId = null;
    }
}
