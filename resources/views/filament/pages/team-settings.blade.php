<x-filament::page>
    <div x-data="{ activeTab: 'detailsForm' }" class="space-y-6">
        <x-filament::tabs label="Content tabs" contained>
            <x-filament::tabs.item icon="heroicon-o-document-text" alpine-active="activeTab === 'detailsForm'"
                x-on:click="activeTab = 'detailsForm'">
                {{ __('Details') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-o-users" alpine-active="activeTab === 'members'"
                x-on:click="activeTab = 'members'">
                {{ __('Members') }}
            </x-filament::tabs.item>
        </x-filament::tabs>

        <form x-ref="detailsForm" :class="activeTab === 'detailsForm' || 'hidden'" wire:submit="create"
            class="space-y-6">
            {{ $this->form }}
            <x-filament::button type="submit">
                {{ __('Save') }}
            </x-filament::button>
        </form>

        <div x-ref="members" :class="activeTab === 'members' || 'hidden'">
            <livewire:team-members />
        </div>
    </div>
</x-filament::page>
