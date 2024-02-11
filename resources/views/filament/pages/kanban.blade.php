<x-filament::page>

    <div class="w-full mx-auto" x-data="{ open: false }">
        <details class="w-full duration-300 bg-white" x-bind:class="{ 'open:bg-gray-200': open }">
            <summary @click="open = !open"
                class="relative w-full px-5 py-3 text-base text-gray-500 cursor-pointer bg-inherit">
                {{ __('Filters') }}
            </summary>
            <div class="px-5 py-3 bg-white">
                <form>
                    {{ $this->form }}
                </form>
            </div>
        </details>
    </div>

    <div class="kanban-container">

        @foreach ($this->getStatuses() as $index => $status)
            <div id="status-records-{{ $status['id'] }}" class="kanban-record">
                @include('partials.kanban.status')
            </div>
        @endforeach

    </div>

    @push('scripts')
        <script src="{{ asset('js/Sortable.js') }}"></script>
        <script>
            (() => {
                document.querySelectorAll('.kanban-record').forEach((record, index) => {
                    Sortable.create(record, {
                        group: {
                            name: `status-${index}`,
                            pull: true,
                            put: true
                        },
                        handle: '.handle',
                        animation: 100,
                        onEnd: function(evt) {
                            this.$emit('recordUpdated', {
                                id: +evt.clone.dataset.id,
                                newIndex: +evt.newIndex,
                                newStatus: +evt.to.dataset.status
                            });
                        }.bind(this), // Bind the Livewire component instance to the function
                    });
                });
            })();
        </script>
    @endpush


</x-filament::page>
