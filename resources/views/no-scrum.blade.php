<div class="flex flex-col w-full">
    <span class="text-lg font-medium text-gray-500">
        {{ __('No active sprint for this project!') }}
    </span>
    <span class="text-sm text-gray-500">
        {{ __("Click the below button to manage project's sprints") }}
    </span>
    <a href="{{ route('filament.admin.resources.projects.view', ['record' => $project, 'tenant' => \Filament\Facades\Filament::getTenant()->id]) }}"
        class="px-3 py-2 mt-3 text-white rounded bg-primary-500 hover:bg-primary-600 w-fit">
        {{ __('Manage sprints') }}
    </a>
    <span class="text-sm text-gray-500">
        {{ __('If you think a sprint should be started, please contact an administrator') }}
    </span>
</div>
