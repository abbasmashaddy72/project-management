<div class="flex flex-col w-full gap-5" wire:key='attachments'>
    <x-filament-panels::form wire:submit="upload">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-2" wire:loading.prop="disabled">
            {{ __('Upload') }}
        </x-filament::button>
    </x-filament-panels::form>

    <x-filament-actions::modals />
    <x-curator::modals.modal />
</div>
