<div class="flex flex-col items-start space-y-4">
    <a href="{{ route('filament.admin.pages.board', ['tenant' => \Filament\Facades\Filament::getTenant()->id]) }}"
        class="text-sm font-medium transition-colors text-primary-500 hover:underline">
        {{ __('Back to Board') }}
    </a>
    <h1 class="text-3xl font-bold text-gray-800 transition-colors dark:text-white">
        {{ __('Scrum') }} - {{ $this->project->name }}
    </h1>
</div>

<div class="flex flex-col w-full gap-4">
    <div class="flex items-center w-full gap-4">
        <span class="px-2 py-1 text-base font-semibold text-white rounded-lg bg-primary-500">
            {{ $this->project->currentSprint->name }}
        </span>
        <span class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-bold">{{ __('Starts at') }}:</span>
            {{ $this->project->currentSprint->started_at->format(__('D, M d, Y')) }}
            | <span class="font-bold">{{ __('Ends at') }}:</span>
            {{ $this->project->currentSprint->ends_at->format(__('D, M d, Y')) }}
            @if ($this->project->currentSprint->remaining)
                | <span class="font-bold">{{ __('Remaining') }}:</span>
                {{ $this->project->currentSprint->remaining }} {{ __('days') }}
            @endif
        </span>
    </div>

    @if ($this->project->nextSprint)
        <span class="text-sm font-medium text-primary-500 dark:text-primary-400">
            <span class="font-bold">{{ __('Next sprint') }}:</span>
            {{ $this->project->nextSprint->name }}
            - <span class="font-bold">{{ __('Starts at') }}:</span>
            {{ $this->project->nextSprint->starts_at->format(__('D, M d, Y')) }}
            ({{ __('in') }} {{ $this->project->nextSprint->starts_at->diffForHumans() }})
        </span>
    @endif
</div>
