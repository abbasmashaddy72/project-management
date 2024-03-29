<x-filament::section>
    <x-slot name="heading">
        {{ __('Member Invitation') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Invite a new member to the team to collaborate with you.') }}
    </x-slot>

    <x-slot name="headerEnd">
        <x-filament-actions::actions :actions="$this->getCachedHeaderActions()" class="shrink-0" />
    </x-slot>

    <div x-data="{ activeTab: 'teamMembers' }" class="space-y-6">
        <x-filament::tabs label="Content tabs" contained>
            <x-filament::tabs.item alpine-active="activeTab === 'teamMembers'" x-on:click="activeTab = 'teamMembers'">
                {{ __('Members List') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item alpine-active="activeTab === 'invitations'" x-on:click="activeTab = 'invitations'">
                {{ __('Invitations') }}
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div x-show="activeTab === 'teamMembers'">
            {{ $this->table }}
        </div>

        <div x-show="activeTab === 'invitations'">
            <livewire:team-invitation />
        </div>
    </div>
</x-filament::section>
