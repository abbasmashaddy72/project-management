<div class="kanban-record" data-id="{{ $record['id'] }}">
    <button type="button" class="handle">
        <x-heroicon-o-arrows-pointing-out class="w-5 h-5" />
    </button>
    <div class="space-y-2 record-info">
        <span class="record-subtitle">{{ $record['title'] }}</span>
        <span class="code">{{ $record['code'] }}</span>
        <span class="title">{{ $record['title'] }}</span>
    </div>
    <div class="flex items-center justify-between space-x-2 record-footer">
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
