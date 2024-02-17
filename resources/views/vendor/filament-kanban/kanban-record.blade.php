<div id="{{ $record->getKey() }}" wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="px-4 py-2 font-medium text-gray-600 bg-white rounded-lg record dark:bg-gray-700 cursor-grab dark:text-gray-200"
    @if ($record->just_updated) x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        " @endif>
    {{ $record->{static::$recordTitleAttribute} }}
</div>
