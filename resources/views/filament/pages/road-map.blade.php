<x-filament::page>

    <x-filament::card>

        <div class="flex-col hidden w-full gap-5 lg:flex md:hidden sm:hidden">
            <div class="flex items-center justify-between w-full">
                <form wire:submit.prevent="filter" class="flex items-center gap-2 min-w-[16rem]">
                    {{ $this->form }}
                    <button type="submit" class="px-3 py-2 text-white rounded bg-primary-500 hover:bg-primary-600">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" wire:loading.remove />
                        <div wire:loading.flex>
                            <div class="w-4 h-4 lds-dual-ring"></div>
                        </div>
                    </button>
                </form>
                <div class="flex items-center gap-2">
                    <button wire:click="createEpic" wire:loading.attr="disabled"
                        class="flex items-center gap-2 px-3 py-1 text-white rounded bg-primary-500 hover:bg-primary-600">
                        <x-heroicon-o-plus class="w-4 h-4" /> {{ __('Epic') }}
                    </button>
                    <button wire:click="createTicket" wire:loading.attr="disabled"
                        class="flex items-center gap-2 px-3 py-1 text-white rounded bg-success-500 hover:bg-success-600">
                        <x-heroicon-o-plus class="w-4 h-4" /> {{ __('Ticket') }}
                    </button>
                </div>
            </div>

            <div wire:init="filter" class="relative w-full gantt" id="gantt-chart" wire:ignore></div>
        </div>

        <div
            class="flex flex-col items-center justify-center w-full gap-2 font-medium text-center text-gray-500 2xl:hidden xl:hidden lg:hidden md:flex sm:flex">
            <x-heroicon-o-face-frown class="w-10 h-10" />
            <span>{{ __('Road Map chart is only available on large screen') }}</span>
        </div>
    </x-filament::card>

    @if ($epic)
        <!-- Epic modal -->
        <div class="dialog-container">
            <div class="dialog dialog-lg">
                <div class="dialog-header">
                    {{ __($epic && $epic->id ? 'Update Epic' : 'Create Epic') }}
                </div>
                <div class="dialog-content">
                    @livewire('road-map.epic-form', ['epic' => $epic])
                </div>
            </div>
        </div>
    @endif

    @if ($ticket)
        <!-- Epic modal -->
        <div class="dialog-container">
            <div class="dialog dialog-xl">
                <div class="dialog-header">
                    {{ __('Create ticket') }}
                </div>
                <div class="dialog-content">
                    @livewire('road-map.issue-form', ['project' => $project])
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('css/jsgantt.css') }}" />
        <script src="{{ asset('js/jsgantt.js') }}"></script>

        <script>
            const g = new JSGantt.GanttChart(document.getElementById('gantt-chart'), 'week');
            // Set settings
            g.setOptions({
                vCaptionType: 'Complete',
                vDayColWidth: 26,
                vWeekColWidth: 52,
                vMonthColWidth: 52,
                vDateTaskDisplayFormat: 'day dd month yyyy',
                vDayMajorDateDisplayFormat: 'mon yyyy - Week ww',
                vWeekMinorDateDisplayFormat: 'dd mon',
                vLang: '{{ config('app.locale') }}',
                vShowTaskInfoLink: 1,
                vShowEndWeekDate: 0,
                vUseSingleCell: 10000,
                vFormatArr: ['Day', 'Week', 'Month'],
                vEvents: {
                    taskname: (task) => {
                        const data = task.getAllData();
                        const meta = data.pDataObject.meta;
                        const tenant = '{{ \Filament\Facades\Filament::getTenant()->id }}';
                        const slug = meta.id;
                        const url = `/${tenant}/tickets/share/${slug}`;

                        if (meta.epic) {
                            Livewire.emit('updateEpic', meta.id);
                        } else {
                            window.open(url, '_blank');
                        }
                    }
                }
            });
            // Customize gantt chart
            g.setShowDur(false); // Hide duration from columns
            g.setUseToolTip(false); // Remove tooltip on object hover
            // Draw gantt chart
            g.Draw();

            window.addEventListener('projectChanged', (e) => {
                g.ClearTasks();
                JSGantt.parseJSON(e.detail[0].url, g);

                // Corrected indices for split
                const minDate = e.detail[0].start_date.split('-');
                const maxDate = e.detail[0].end_date.split('-');
                const scrollToDate = e.detail[0].scroll_to.split('-');

                // Use numerical indices for accessing array values
                g.setMinDate(new Date(minDate[0], (+minDate[1]) - 1, minDate[2]));
                g.setMaxDate(new Date(maxDate[0], (+maxDate[1]) - 1, maxDate[2]));
                g.setScrollTo(new Date(scrollToDate[0], (+scrollToDate[1]) - 1, scrollToDate[2]));
                g.Draw();
            });
        </script>
    @endpush





</x-filament::page>
