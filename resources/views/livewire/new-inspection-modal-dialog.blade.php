<div class="mx-4 mb-4 md:mx-0">
  <x-jet-button class="w-full md:w-auto" wire:click="$toggle('confirmingInspectionCreation')" wire:loading.attr="disabled">
    <i class="mr-2 text-blue-300 dark:text-white fa-solid fa-solar-panel fa-fw"></i> {{ __('New Inspection') }}
  </x-jet-button>
  <!-- Create new inspection Modal -->
  <x-jet-dialog-modal wire:model="confirmingInspectionCreation">
    <x-slot name="title">
      {{ __('New Inspection for this site') }}
    </x-slot>
    <x-slot name="content">
      <div class="flex flex-col items-center justify-start w-full space-y-2">
        <div class="w-full">
          <x-jet-label for="name" value="{{ __('Name, as it will appear on the report') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.defer="state.name" autocomplete="name" />
          <x-jet-input-error for="state.name" class="mt-2" />
        </div>
        <div class="w-full">
          <x-jet-label for="commissioning_date" value="{{ __('Commissioning date') }}" />
          <x-jet-input id="commissioning_date" type="date" class="block w-full mt-1" wire:model.defer="state.commissioning_date" />
          <x-jet-input-error for="state.commissioning_date" class="mt-2" />
        </div>
      </div>
    </x-slot>
    <x-slot name="footer">
      <x-jet-secondary-button wire:click="$toggle('confirmingInspectionCreation')" wire:loading.attr="disabled">
        {{ __('Neverminds') }}
      </x-jet-secondary-button>
      <x-jet-button class="ml-2" wire:click="store" wire:loading.attr="disabled">
        {{ __('Create Inspection') }}
      </x-jet-button>
    </x-slot>
  </x-jet-dialog-modal>
</div>
