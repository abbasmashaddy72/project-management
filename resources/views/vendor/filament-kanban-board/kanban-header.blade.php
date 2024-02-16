{{-- Injected variables $status, $styles --}}
<div class="{{ $styles['kanbanHeader'] }}" style="color: {{ $status['color'] }};">
    {{ $status['title'] }}
</div>
