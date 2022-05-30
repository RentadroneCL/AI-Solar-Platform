<div>
  <div class="flex flex-col w-full mb-5 md:flex-row">
    <x-jet-button class="w-full mb-4 md:w-auto md:mb-0" wire:click="$toggle('displayEquipmentTypeCreationForm')">
      <i class="mr-2 text-blue-300 fa-solid fa-circle-plus fa-fw"></i> {{ __('Create new type') }}
    </x-jet-button>

    {{-- <x-jet-secondary-button class="w-full mb-4 ml-auto md:mb-0 md:w-auto" wire:click="$toggle('showImportModal')">
      <i class="mr-2 fa-solid fa-cloud-arrow-up fa-fw text-slate-500"></i> {{ __('Import CSV') }}
    </x-jet-secondary-button>

    @if (!$equipmentTypes->isEmpty())
      <x-jet-secondary-button class="w-full md:ml-2 md:w-auto" wire:click="export">
        <i class="mr-2 fa-solid fa-download fa-fw text-slate-500"></i> {{ __('Export CSV') }}
      </x-jet-secondary-button>
    @endif --}}
  </div>

  @if ($equipmentTypes->isEmpty())
    <div class="flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed rounded-lg border-slate-200">
      <i class="mb-4 fa-solid fa-database fa-fw fa-2x text-slate-500"></i>
      <p class="font-semibold text-slate-900">{{ __('Load new data') }}</p>
    </div>
  @else
    <x-jet-action-message class="inline-flex items-center justify-start mb-2" on="stored-equipment-type">
      <i class="p-1 mr-2 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i> {{ __('Saved!') }}
    </x-jet-action-message>

    <x-jet-action-message class="inline-flex items-center justify-start mb-2" on="updated">
      <i class="p-1 mr-2 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i> {{ __('Updated record!') }}
    </x-jet-action-message>

    <x-jet-action-message class="inline-flex items-center justify-start mb-2" on="deleted-equipment-type">
      <i class="p-1 mr-2 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i> {{ __('Deleted record!') }}
    </x-jet-action-message>

    <livewire:equipment-type-table :site="$site" :equipmentTypes="$equipmentTypes">
  @endif

  {{-- Create equipment type modal --}}
  <x-jet-dialog-modal wire:model="displayEquipmentTypeCreationForm">
    <x-slot name="title">{{ __('New type') }}</x-slot>
    <x-slot name="content">
      <div class="p-4 mb-4 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm">
        <div class="inline-flex items-center">
          <i class="mr-2 fa-solid fa-circle-info fa-fw fa-2x"></i>
          <div class="flex flex-col text-sm">
            <p class="max-w-md mb-1">
              {{-- {{ __('We recommend using a well-defined convention for naming assets like a combination of the type of asset and location in zone or sub-zone, For example: ') }} --}}
              {{ __('We recommend using a well-defined convention for naming assets like a combination of the type of asset and brand or model, For example: ') }}
              `<code class="text-xs font-semibold text-white">Panel</code>` {{ __('or') }} `<code class="text-xs font-semibold text-white">"{{ __('Panel Brand') }}" - "{{ __('Panel Model') }}"</code>`
            </p>
          </div>
        </div>
      </div>
      <form wire:submit.prevent="store">
        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Name') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.name" autocomplete="name" required />
          <x-jet-input-error for="state.name" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="quantity" value="{{ __('Quantity') }}" />
          <x-jet-input id="quantity" type="number" class="block w-full mt-1" wire:model.lazy="state.quantity" autocomplete="quantity" required />
          <x-jet-input-error for="state.quantity" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <div class="p-4 mb-4 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm">
            <div class="inline-flex items-center">
              <i class="mr-2 fa-solid fa-circle-info fa-fw fa-2x"></i>
              <div class="flex flex-col text-sm">
                <p class="max-w-md mb-1">
                  {{ __('You can also conditionally apply technical specifications for a type of asset using the feature/value convention using the inputs below. For example, ') }} `<code class="text-xs font-semibold text-white">feature:material</code>`, `<code class="text-xs font-semibold text-white">value:monocrystalline</code>`
                </p>
              </div>
            </div>
          </div>
          <x-jet-label class="mb-4" for="custom_properties[][]" value="{{ __('Properties') }}" />
          @foreach ($state['custom_properties'] as $key => $value)
            <div class="grid grid-cols-3 gap-4 mb-2">
              <div class="col-span-1">
                <x-jet-label for="custom_properties[{{$key}}][key]" value="{{ __('Feature') }}" />
                <x-jet-input id="custom_properties[{{$key}}][key]" type="text" class="block w-full mt-1" wire:model.lazy="state.custom_properties.{{$key}}.key" />
                <x-jet-input-error for="state.custom_properties.{{$key}}.key" class="mt-2" />
              </div>
              <div class="col-span-1">
                <x-jet-label for="custom_properties[{{$key}}][value]" value="{{ __('Value') }}" />
                <x-jet-input id="custom_properties[{{$key}}[value]" type="text" class="block w-full mt-1" wire:model.lazy="state.custom_properties.{{$key}}.value" />
                <x-jet-input-error for="state.custom_properties.{{$key}}.value" class="mt-2" />
              </div>
              @if ($key > 0)
                <div class="col-span-1 mt-auto mb-1">
                  <x-jet-danger-button class="px-2 py-2" title="{{ __('Remove') }}" wire:click='removeCustomPropertyInput({{$key}})'>
                    <i class="fa-solid fa-trash fa-fw text-rose-300"></i>
                  </x-jet-danger-button>
                </div>
              @endif
            </div>
          @endforeach
          <x-jet-secondary-button class="mt-5" wire:click='addCustomPropertyInput'>
            <i class="mr-2 fa-solid fa-plus fa-fw"></i> {{ __('Add properties') }}
          </x-jet-secondary-button>
        </div>
      </form>
    </x-slot>

    <x-slot name="footer">
      <div class="inline-flex items-center">
        <x-jet-secondary-button wire:click="$toggle('displayEquipmentTypeCreationForm')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="store" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 fa-solid fa-circle-plus fa-fw"></i> {{ __('Create') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>

  {{-- Import CSV data modal --}}
  <x-jet-dialog-modal wire:model="showImportModal">
    <x-slot name="title">{{ __('Upload CSV file') }}</x-slot>

    <x-slot name="content">
      <p class="text-base text-slate-900">
        {{ __('You can choose a resource to import them into and match up headings from the CSV to the appropriate fields of the resource.') }}
      </p>
      <div class="hidden mt-4 md:block">
        <h3 class="font-semibold text-slate-900">{{ __('CSV example') }}</h3>
        <p class="text-slate-700">
          {{ __('We will use the following file ') }} <code class="p-1 font-semibold text-blue-600 bg-blue-200 rounded-md shadow-sm">file.csv</code> {{ __(' containing the following data:') }}
        </p>
      </div>

      @error('file') <span class="p-4 my-3 border-2 rounded-md shadow-sm bg-rose-500 text-rose-200 border-rose-400">{{ $message }}</span> @enderror

      <div class="inline-flex w-full my-4">
        <div class="inline-flex items-center justify-center w-1/6 px-4 py-3 font-semibold tracking-widest transition bg-white border-2 border-r rounded-l-lg shadow-sm cursor-pointer text-slate-900 border-slate-200 hover:text-slate-800 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-slate-800 active:bg-slate-50 disabled:opacity-25">
          <i class="mr-2 cursor-pointer fas fa-folder-open fa-fw fa-lg text-slate-500"></i>
          <input wire:model="file" id="file" name="file" class="absolute w-1/6 opacity-0 cursor-pointer pin-x pin-y" type="file" accept="text/csv">
        </div>
        <input wire:model="filename" class="w-5/6 px-4 py-3 text-gray-600 border-2 border-l rounded-r-lg shadow-sm bg-slate-50 border-slate-200 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="text" readonly disabled>
      </div>
    </x-slot>

    <x-slot name="footer">
      <div class="inline-flex items-center">
        <x-jet-secondary-button wire:click="$toggle('showImportModal')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="import" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 fa-solid fa-cloud-arrow-up fa-fw"></i> {{ __('Upload file') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>
</div>
