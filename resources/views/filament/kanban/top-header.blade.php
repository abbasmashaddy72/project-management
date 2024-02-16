<div class="flex flex-col w-full gap-1">
    <a href="{{ route('filament.admin.pages.board', ['tenant' => \Filament\Facades\Filament::getTenant()->id]) }}"
        class="text-xs font-medium text-primary-500 hover:underline">
        {{ __('Back to board') }}
    </a>
    <div class="flex flex-col gap-1">
        <span>{{ __('Kanban') }}
            @if ($this->project)
                - {{ $this->project->name }}
        </span>
    @else
        </span>
        <span class="text-xs text-gray-400">
            {{ __('Only default statuses are listed when no projects selected') }}
        </span>
        @endif
    </div>
</div>
