<div class="flex flex-col items-start space-y-2">
    <a href="{{ route('filament.admin.pages.board', ['tenant' => \Filament\Facades\Filament::getTenant()->id]) }}"
        class="text-sm font-medium text-primary-500 hover:underline">
        {{ __('Back to Board') }}
    </a>
    <h1 class="text-2xl font-semibold">
        {{ __('Scrum') }} - {{ $this->project->name }}
    </h1>
</div>
