@php($record = $this->record)
<x-filament::page>

    <a href="{{ route('filament.admin.pages.kanban.{project}', ['project' => $record->project->id, 'tenant' => \Filament\Facades\Filament::getTenant()->id]) }}"
        class="flex items-center gap-1 text-sm font-semibold text-gray-600 hover:text-gray-700">
        <x-heroicon-o-arrow-left class="w-4 h-4" /> {{ __('Back to kanban board') }}
    </a>


    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        <x-filament::card class="flex flex-col w-full gap-5 md:w-2/3">
            <div class="flex flex-col w-full gap-4 p-4">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-2 text-sm font-medium text-primary-500">
                        <x-heroicon-o-ticket class="w-4 h-4" />
                        {{ $record->code }}
                    </span>
                    <span class="text-sm font-light text-gray-400">|</span>
                    <span class="flex items-center gap-2 text-sm text-gray-500">
                        {{ $record->project->name }}
                    </span>
                </div>
                <h2 class="text-2xl font-semibold text-gray-700">
                    {{ $record->name }}
                </h2>
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center px-2 py-1 text-xs text-center text-white rounded"
                        style="background-color: {{ $record->status->color }};">
                        {{ $record->status->name }}
                    </div>
                    <div class="flex items-center justify-center px-2 py-1 text-xs text-center text-white rounded"
                        style="background-color: {{ $record->priority->color }};">
                        {{ $record->priority->name }}
                    </div>
                    <div class="flex items-center justify-center px-2 py-1 text-xs text-center text-white rounded"
                        style="background-color: {{ $record->type->color }};">
                        <x-icon class="h-3 text-white" name="{{ $record->type->icon }}" />
                        <span class="ml-2">
                            {{ $record->type->name }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-col w-full gap-4 pt-5">
                    <span class="text-lg font-medium text-gray-500">
                        {{ __('Content') }}
                    </span>
                    <div class="prose">
                        {!! $record->content !!}
                    </div>
                </div>
            </div>

        </x-filament::card>

        <x-filament::card class="flex flex-col w-full md:w-1/3">
            <div class="flex flex-col w-full gap-1" wire:ignore>
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Owner') }}
                </span>
                <div class="flex items-center w-full gap-1 text-gray-500">
                    <x-user-avatar :user="$record->owner" />
                    {{ $record->owner->name }}
                </div>
            </div>

            <div class="flex flex-col w-full gap-1 pt-3" wire:ignore>
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Responsible') }}
                </span>
                <div class="flex items-center w-full gap-1 text-gray-500">
                    @if ($record->responsible)
                        <x-user-avatar :user="$record->responsible" />
                    @endif
                    {{ $record->responsible?->name ?? '-' }}
                </div>
            </div>

            @if ($record->project->type === 'scrum')
                <div class="flex flex-col w-full gap-1 pt-3">
                    <span class="text-sm font-semibold text-gray-600">
                        {{ __('Sprint') }}
                    </span>
                    <div class="flex flex-col justify-center w-full gap-1 text-gray-500">
                        @if ($record->sprint)
                            {{ $record->sprint->name }}
                            <span class="text-xs text-gray-400">
                                {{ __('Starts at:') }} {{ $record->sprint->starts_at->format(__('Y-m-d')) }} -
                                {{ __('Ends at:') }} {{ $record->sprint->ends_at->format(__('Y-m-d')) }}
                            </span>
                        @else
                            -
                        @endif
                    </div>
                </div>
            @else
                <div class="flex flex-col w-full gap-1 pt-3">
                    <span class="text-sm font-semibold text-gray-600">
                        {{ __('Epic') }}
                    </span>
                    <div class="flex items-center w-full gap-1 text-gray-500">
                        @if ($record->epic)
                            {{ $record->epic->name }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex flex-col w-full gap-1 pt-3">
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Estimation') }}
                </span>
                <div class="flex items-center w-full gap-1 text-gray-500">
                    @if ($record->estimation)
                        {{ $record->estimationForHumans }}
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="flex flex-col w-full gap-1 pt-3">
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Total time logged') }}
                </span>
                @if ($record->hours()->count())
                    @if ($record->estimation)
                        <div class="flex justify-between mb-1">
                            <span
                                class="text-base font-medium
                                         text-{{ $record->estimationProgress > 100 ? 'danger' : 'primary' }}-700
                                         dark:text-white">
                                {{ $record->totalLoggedHours }}
                            </span>
                            <span
                                class="text-sm font-medium
                                         text-{{ $record->estimationProgress > 100 ? 'danger' : 'primary' }}-700
                                         dark:text-white">
                                {{ round($record->estimationProgress) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-{{ $record->estimationProgress > 100 ? 'danger' : 'primary' }}-600
                                        h-2.5 rounded-full"
                                style="width: {{ $record->estimationProgress > 100 ? 100 : $record->estimationProgress }}%">
                            </div>
                        </div>
                    @else
                        <div class="flex items-center w-full gap-1 text-gray-500">
                            {{ $record->totalLoggedHours }}
                        </div>
                    @endif
                @else
                    -
                @endif
            </div>

            <div class="flex flex-col w-full gap-1 pt-3">
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Subscribers') }}
                </span>
                <div class="flex items-center w-full gap-1 text-gray-500">
                    @if ($record->subscribers->count())
                        @foreach ($record->subscribers as $subscriber)
                            <x-user-avatar :user="$subscriber" />
                        @endforeach
                    @else
                        {{ '-' }}
                    @endif
                </div>
            </div>

            <div class="flex flex-col w-full gap-1 pt-3">
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Creation date') }}
                </span>
                <div class="w-full text-gray-500">
                    {{ $record->created_at->format(__('Y-m-d g:i A')) }}
                    <span class="text-xs text-gray-400">
                        ({{ $record->created_at->diffForHumans() }})
                    </span>
                </div>
            </div>

            <div class="flex flex-col w-full gap-1 pt-3">
                <span class="text-sm font-semibold text-gray-600">
                    {{ __('Last update') }}
                </span>
                <div class="w-full text-gray-500">
                    {{ $record->updated_at->format(__('Y-m-d g:i A')) }}
                    <span class="text-xs text-gray-400">
                        ({{ $record->updated_at->diffForHumans() }})
                    </span>
                </div>
            </div>

            @if ($record->relations->count())
                <div class="flex flex-col w-full gap-1 pt-3">
                    <span class="text-sm font-semibold text-gray-600">
                        {{ __('Ticket relations') }}
                    </span>
                    <div class="w-full text-gray-500">
                        @foreach ($record->relations as $relation)
                            <div class="flex items-center w-full gap-1 text-xs">
                                <span
                                    class="rounded px-2 py-1 text-white
                                             bg-{{ config('system.tickets.relations.colors.' . $relation->type) }}-600">
                                    {{ __(config('system.tickets.relations.list.' . $relation->type)) }}
                                </span>
                                <a target="_blank" class="font-medium hover:underline"
                                    href="{{ route('filament.resources.tickets.share', $relation->relation->code) }}">
                                    {{ $relation->relation->code }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-filament::card>

    </div>

    <div class="w-full">
        <x-filament::card>
            <ul
                class="flex flex-wrap mb-4 text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
                <li class="me-2" wire:loading='selectTab' wire:target='selectTab'>
                    <div role="status">
                        <svg aria-hidden="true"
                            class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </li>
                <li class="me-2">
                    <button wire:click="selectTab('comments')" aria-current="page"
                        class="inline-block p-4 rounded-t-lg @if ($tab === 'comments') text-primary-600 bg-gray-100 active dark:bg-gray-800 dark:text-primary-500 @else hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 @endif">
                        {{ __('Comments') }}
                    </button>
                </li>
                <li class="me-2">
                    <button wire:click="selectTab('activities')"
                        class="inline-block p-4 rounded-t-lg @if ($tab === 'activities') text-primary-600 bg-gray-100 active dark:bg-gray-800 dark:text-primary-500 @else hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 @endif">
                        {{ __('Activities') }}
                    </button>
                </li>
                <li class="me-2">
                    <button wire:click="selectTab('time')"
                        class="inline-block p-4 rounded-t-lg @if ($tab === 'time') text-primary-600 bg-gray-100 active dark:bg-gray-800 dark:text-primary-500 @else hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 @endif">
                        {{ __('Time logged') }}
                    </button>
                </li>
                <li class="me-2">
                    <button wire:click="selectTab('attachments')"
                        class="inline-block p-4 rounded-t-lg @if ($tab === 'attachments') text-primary-600 bg-gray-100 active dark:bg-gray-800 dark:text-primary-500 @else hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300 @endif">
                        {{ __('Attachments') }}
                    </button>
                </li>
            </ul>
            @if ($tab === 'comments')
                <form wire:submit.prevent="submitComment" class="pb-5">
                    {{ $this->form }}
                    <button type="submit"
                        class="px-3 py-2 mt-3 text-white rounded bg-primary-500 hover:bg-primary-600">
                        {{ __($selectedCommentId ? 'Edit comment' : 'Add comment') }}
                    </button>
                    @if ($selectedCommentId)
                        <button type="button" wire:click="cancelEditComment"
                            class="px-3 py-2 mt-3 text-white rounded bg-warning-500 hover:bg-warning-600">
                            {{ __('Cancel') }}
                        </button>
                    @endif
                </form>
                @foreach ($record->comments->sortByDesc('created_at') as $comment)
                    <div
                        class="w-full flex flex-col gap-2 @if (!$loop->last) pb-5 mb-5 border-b border-gray-200 @endif ticket-comment">
                        <div class="flex justify-between w-full">
                            <span class="flex items-center gap-1 text-sm text-gray-500">
                                <span class="flex items-center gap-1 font-medium">
                                    <x-user-avatar :user="$comment->user" />
                                    {{ $comment->user->name }}
                                </span>
                                <span class="px-2 text-gray-400">|</span>
                                {{ $comment->created_at->format('Y-m-d g:i A') }}
                                ({{ $comment->created_at->diffForHumans() }})
                            </span>
                            @if ($this->isAdministrator() || $comment->user_id === auth()->user()->id)
                                <div class="flex items-center gap-2 actions">
                                    <button type="button" wire:click="editComment({{ $comment->id }})"
                                        class="text-xs text-primary-500 hover:text-primary-600 hover:underline">
                                        {{ __('Edit') }}
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                        class="text-xs text-danger-500 hover:text-danger-600 hover:underline">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="w-full prose">
                            {!! $comment->content !!}
                        </div>
                    </div>
                @endforeach
            @endif
            @if ($tab === 'activities')
                <div class="flex flex-col w-full pt-5">
                    @if ($record->activities->count())
                        @foreach ($record->activities->sortByDesc('created_at') as $activity)
                            <div
                                class="w-full flex flex-col gap-2 @if (!$loop->last) pb-5 mb-5 border-b border-gray-200 @endif">
                                <span class="flex items-center gap-1 text-sm text-gray-500">
                                    <span class="flex items-center gap-1 font-medium">
                                        <x-user-avatar :user="$activity->user" />
                                        {{ $activity->user->name }}
                                    </span>
                                    <span class="px-2 text-gray-400">|</span>
                                    {{ $activity->created_at->format('Y-m-d g:i A') }}
                                    ({{ $activity->created_at->diffForHumans() }})
                                </span>
                                <div class="flex items-center w-full gap-10">
                                    <span class="text-gray-400">{{ $activity->oldStatus->name }}</span>
                                    <x-heroicon-o-arrow-right class="w-6 h-6" />
                                    <span style="color: {{ $activity->newStatus->color }}">
                                        {{ $activity->newStatus->name }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="text-lg font-medium text-gray-500">
                            {{ __('No activities yet!') }}
                        </span>
                    @endif
                </div>
            @endif
            @if ($tab === 'time')
                <livewire:timesheet.time-logged :ticket="$record" />
            @endif
            @if ($tab === 'attachments')
                <livewire:ticket.attachments :ticket="$record" />
            @endif
        </x-filament::card>
    </div>

</x-filament::page>

@push('scripts')
    <script>
        window.addEventListener('shareTicket', async (e) => {
            const text = e.detail[0].url; // Access the URL from the array

            // Create a temporary input element
            const tempInput = document.createElement('input');
            tempInput.setAttribute('value', text);
            document.body.appendChild(tempInput);

            // Select the input field
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices

            try {
                // Copy the text to the clipboard
                await document.execCommand('copy');
                new FilamentNotification()
                    .success()
                    .title('{{ __('Url copied to clipboard') }}')
                    .send();
            } catch (err) {
                console.error('Unable to copy to clipboard', err);
            } finally {
                // Remove the temporary input element
                document.body.removeChild(tempInput);
            }
        });
    </script>
@endpush
