<div class="kanban-record" data-id="{{ $record['id'] }}">
    <button type="button" class="handle">
        <x-heroicon-o-arrows-pointing-out class="w-5 h-5" />
    </button>
    <div class="record-info">
        @if ($this->isMultiProject())
            <span class="text-sm text-gray-500 record-subtitle">
                {{ $record['project']->name }}
            </span>
        @endif
        <a href="{{ route('filament.admin.resources.tickets.view', ['record' => $record['id'], 'tenant' => \Filament\Facades\Filament::getTenant()->id]) }}"
            target="_blank" class="flex items-center space-x-2 record-title">
            <span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full code">
                {{ $record['code'] }}
            </span>
            <span class="text-lg font-semibold title">
                Badge: {{ $record['title'] }}
            </span>
        </a>
    </div>
    <div class="flex items-center justify-between mt-2 space-x-2 record-footer">
        <div class="flex items-center space-x-2 record-type-code">
            @php($epic = $record['epic'])
            @if ($epic && $epic != '')
                <div class="px-2 py-1 text-xs text-white bg-purple-500 rounded" title="{{ __('Epic') }}">
                    {{ $epic->name }}
                </div>
            @endif
            <x-ticket-priority :priority="$record['priority']" />
            <x-ticket-type :type="$record['type']" />
        </div>
        @if ($record['responsible'])
            <x-user-avatar :user="$record['responsible']" />
        @endif
    </div>
    @if ($record['relations']?->count())
        <div class="mt-2 record-relations">
            @foreach ($record['relations'] as $relation)
                <div class="flex items-center space-x-2">
                    <span class="type text-{{ config('system.tickets.relations.colors.' . $relation->type) }}-600">
                        {{ __(config('system.tickets.relations.list.' . $relation->type)) }}
                    </span>
                    <a target="_blank" class="relation"
                        href="{{ route('filament.resources.tickets.share', $relation->relation->code) }}">
                        {{ $relation->relation->code }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif
    @if ($record['totalLoggedHours'])
        <div class="flex items-center mt-2 space-x-1 record-logged-hours">
            <x-heroicon-o-clock class="w-4 h-4" />
            <span>{{ $record['totalLoggedHours'] }}</span>
        </div>
    @endif
</div>
