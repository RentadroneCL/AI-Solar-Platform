<div>
  @if ($equipmentTypes === 0)
    <div class="flex flex-col items-center justify-center px-6 py-12 mt-4 border-2 border-dashed rounded-lg border-slate-200 dark:border-slate-600">
      <i class="mb-4 text-yellow-400 fa-solid fa-circle-exclamation fa-fw fa-2x"></i>
      <p class="text-base font-medium text-slate-900 dark:text-slate-400">
        {{ __('Before loading your data, you need to populate the ') }} <code class="p-1 text-sm font-semibold text-yellow-600 bg-yellow-200 rounded-md shadow-sm">{{ __('equipment types module.') }}</code>
      </p>
    </div>
  @else
    <div class="flex flex-col mx-4 mb-5 md:mx-0 md:flex-row">
      <x-jet-button class="w-full mb-4 md:w-auto md:mb-0" wire:click="$toggle('displayEquipmentCreationForm')">
        <i class="mr-2 text-blue-300 fa-solid fa-circle-plus fa-fw"></i> {{ __('New equipment') }}
      </x-jet-button>

      <x-jet-secondary-button class="mb-4 md:ml-auto md:mb-0" wire:click="$toggle('showImportModal')">
        <i class="mr-2 fa-solid fa-cloud-arrow-up fa-fw text-slate-500"></i> {{ __('Import csv') }}
      </x-jet-secondary-button>

      @if (!$equipments->isEmpty())
        <x-jet-secondary-button wire:click="export" class="w-full md:ml-2 md:w-auto">
          <i class="mr-2 fa-solid fa-download fa-fw text-slate-500"></i> {{ __('Export csv') }}
        </x-jet-secondary-button>
      @endif
    </div>

    @if ($equipments->isEmpty())
      <div class="flex flex-col items-center justify-center px-6 py-12 mt-4 border-2 border-dashed rounded-lg border-slate-200 dark:border-slate-600">
        <i class="mb-4 fa-solid fa-database fa-fw fa-2x text-slate-500 dark:text-slate-400"></i>
        <p class="font-semibold text-slate-900 dark:text-slate-500">{{ __('Load new data') }}</p>
      </div>
    @else
      <x-jet-action-message class="p-4 my-2 border-2 rounded-lg shadow-sm bg-rose-500 border-rose-400" on="error">
        <p class="inline-flex items-center text-base text-rose-200">
          <i class="p-1 mr-2 border rounded-md shadow-sm text-rose-200 bg-rose-400 border-rose-400 fa-solid fa-exclamation fa-fw"></i> {{ __('An unexpected error has occurred') }}
        </p>
      </x-jet-action-message>

      <livewire:equipment-table :site="$site">
    @endif
  @endif

  <!-- Create equipment type modal -->
  <x-jet-dialog-modal wire:model="displayEquipmentCreationForm">
    <x-slot name="title">{{ __('New equipment') }}</x-slot>

    <x-slot name="content">
      <div class="p-4 mb-4 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm">
        <div class="inline-flex items-center">
          <i class="mr-2 fa-solid fa-circle-info fa-fw fa-2x"></i>
          <div class="flex flex-col text-sm">
            <p class="max-w-md mb-1">
              {{ __('We recommend using a well-defined convention for naming assets like a combination of the type of asset and location in zone or sub-zone, For example: ') }}
              `<code class="text-xs font-semibold text-white">Panel-01A-C</code>`
            </p>
          </div>
        </div>
      </div>

      <form wire:submit.prevent="store">
        <div class="col-span-6 mb-4">
          <x-jet-label for="equipment_type_id" value="{{ __('Equipment type') }}" />

          @if (is_null($equipmentType))
            <div class="inline-flex w-full">
              <x-jet-input wire:keyup="search" type="text" class="w-full mt-1 border-r-0 rounded-none rounded-l-md" wire:model.defer="query" placeholder="{{ __('Search type...') }}" />
              <div class="p-2 mt-1 border-l shadow-sm bg-slate-200 rounded-r-md">
                <i class="fa-solid fa-magnifying-glass text-slate-500 fa-fw"></i>
              </div>
            </div>
            @if ($equipmentTypes instanceof \Illuminate\Database\Eloquent\Collection && $equipmentTypes->isNotEmpty())
              <ul class="mx-1 mb-4 list-none border border-t-0 divide-y shadow-sm bg-slate-50 rounded-b-md">
                @foreach ($equipmentTypes as $type)
                  <li wire:click="$emit('selectedEquipmentType', {{ $type->id }})" class="flex items-center justify-start px-4 py-2 cursor-pointer hover:bg-slate-100">
                    <div class="">
                      <span class="text-sm font-semibold text-slate-900">{{ $type->name }}</span>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif

            <div wire:loading class="inline-flex items-center justify-start w-full mt-2 text-sm text-slate-600">
              <i class="mr-2 fa-solid fa-spinner fa-fw fa-spin text-slate-500"></i> {{ __('Searching...') }}
            </div>

            <div class="p-4 text-slate-500">
              <div class="inline-flex items-center justify-start">
                <p class="text-sm">
                  {{ __('If the equipment type does not appear in the list. You can create a new one in the ') }} <span class="font-semibold">{{ __('Equipment type section.') }}</span>
                </p>
              </div>
            </div>
          @endif

          @if (!is_null($equipmentType))
            <div class="inline-flex items-center justify-start w-full p-4 my-2 border rounded-md shadow bg-slate-50 border-slate-200">
              <div class="ml-4">
                <span class="text-sm font-semibold text-slate-900">{{ $equipmentType->name }}</span>
              </div>
              <x-jet-secondary-button wire:click="discardSelection" class="px-1 py-1 ml-auto rounded-full">
                <i class="fa-solid fa-xmark fa-fw text-slate-500"></i>
              </x-jet-secondary-button>
            </div>
          @endif
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Name') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.name" autocomplete="name" />
          <x-jet-input-error for="state.name" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="brand" value="{{ __('Brand') }}" />
          <x-jet-input id="brand" type="text" class="block w-full mt-1" wire:model.lazy="state.brand" autocomplete="brand" />
          <x-jet-input-error for="state.brand" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="model" value="{{ __('Model') }}" />
          <x-jet-input id="model" type="text" class="block w-full mt-1" wire:model.lazy="state.model" autocomplete="model" />
          <x-jet-input-error for="state.model" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="serial" value="{{ __('Serial') }}" />
          <x-jet-input id="serial" type="text" class="block w-full mt-1" wire:model.lazy="state.serial" autocomplete="serial" />
          <x-jet-input-error for="state.serial" class="mt-2" />
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
        <x-jet-secondary-button wire:click="$toggle('displayEquipmentCreationForm')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="store" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 fa-solid fa-circle-plus fa-fw"></i> {{ __('Create') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>

  <!-- Import CSV data modal -->
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
      <div class="inline-flex w-full my-4">
        <div class="inline-flex items-center justify-center w-1/6 px-4 py-3 font-semibold tracking-widest transition bg-white border-2 border-r rounded-l-lg shadow-sm cursor-pointer text-slate-900 border-slate-200 hover:text-slate-800 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-slate-800 active:bg-slate-50 disabled:opacity-25">
          <i class="mr-2 cursor-pointer fas fa-folder-open fa-fw fa-lg text-slate-500"></i>
          <input wire:model="file" id="file" name="file" class="absolute w-1/6 opacity-0 cursor-pointer pin-x pin-y" type="file" accept="text/csv">
        </div>
        <input wire:model="filename" class="w-5/6 px-4 py-3 text-gray-600 border-2 border-l rounded-r-lg shadow-sm bg-slate-50 border-slate-200 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="text" readonly disabled>
      </div>
      <x-jet-input-error for="file" class="mt-2" />
      <div wire:loading class="inline-flex items-center justify-start w-full mt-2 text-sm text-slate-600">
        <i class="mr-2 fa-solid fa-spinner fa-fw fa-spin text-slate-400"></i> {{ __('Loading...') }}
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

  <!-- Edit equipment Confirmation Modal -->
  <x-jet-dialog-modal wire:model="confirmingEquipmentEdition">
    <x-slot name="title">{{ __('Edit equipment') }}</x-slot>
    <x-slot name="content">
      <div class="p-4 mb-4 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm">
        <div class="inline-flex items-center">
          <i class="mr-2 fa-solid fa-circle-info fa-fw fa-2x"></i>
          <div class="flex flex-col text-sm">
            <p class="max-w-md mb-1">
              {{ __('We recommend using a well-defined convention for naming assets like a combination of the type of asset and location in zone or sub-zone, For example: ') }}
              `<code class="text-xs font-semibold text-white">Panel-01A-C</code>`
            </p>
          </div>
        </div>
      </div>
      <form wire:submit.prevent="update">
        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Name') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.name" autocomplete="name" required />
          <x-jet-input-error for="state.name" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Brand') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.brand" autocomplete="brand" required />
          <x-jet-input-error for="state.brand" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Model') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.model" autocomplete="model" required />
          <x-jet-input-error for="state.model" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Serial') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.serial" autocomplete="serial" required />
          <x-jet-input-error for="state.serial" class="mt-2" />
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
                <x-jet-label for="custom_properties[{{$value['key']}}][key]" value="{{ __('Feature') }}" />
                <x-jet-input id="custom_properties[{{$value['key']}}][key]" type="text" class="block w-full mt-1" wire:model.lazy="state.custom_properties.{{$key}}.key" />
                <x-jet-input-error for="state.custom_properties.{{$key}}.key" class="mt-2" />
              </div>
              <div class="col-span-1">
                <x-jet-label for="custom_properties[{{$value['value']}}][value]" value="{{ __('Value') }}" />
                <x-jet-input id="custom_properties[{{$value['value']}}[value]" type="text" class="block w-full mt-1" wire:model.lazy="state.custom_properties.{{$key}}.value" />
                <x-jet-input-error for="state.custom_properties.{{$key}}.value" class="mt-2" />
              </div>
              <div class="col-span-1 mt-auto mb-1">
                <x-jet-danger-button class="px-2 py-2" title="{{ __('Remove') }}" wire:click='removeCustomPropertyInput({{$key}})'>
                  <i class="fa-solid fa-trash fa-fw text-rose-300"></i>
                </x-jet-danger-button>
              </div>
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
        <x-jet-secondary-button wire:click="$toggle('confirmingEquipmentEdition')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 fa-solid fa-floppy-disk fa-fw"></i> {{ __('Update') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>

  <!-- Delete equipment confirmation modal -->
  <x-jet-dialog-modal wire:model="confirmingEquipmentDeletion">
    <x-slot name="title">{{ __('Delete equipment') }}</x-slot>
    <x-slot name="content">
      {{ __('Are you sure you want to delete this equipment type? Once this resource is deleted, all of its related data will be permanently deleted. Please enter your password to confirm you would like to permanently delete the resource.') }}

      <div class="mt-4" x-data="{}" x-on:confirming-delete-equipment.window="setTimeout(() => $refs.password.focus(), 250)">
        <x-jet-input type="password" class="block w-3/4 mt-1"
          placeholder="{{ __('Password') }}"
          x-ref="password"
          wire:model.defer="password"
          wire:keydown.enter="destroy" />

        <x-jet-input-error for="password" class="mt-2" />
      </div>
    </x-slot>

    <x-slot name="footer">
      <x-jet-secondary-button wire:click="$toggle('confirmingEquipmentDeletion')" wire:loading.attr="disabled">
        {{ __('Nevermind') }}
      </x-jet-secondary-button>

      <x-jet-danger-button class="ml-2" wire:click="destroy" wire:loading.attr="disabled">
        {{ __('Delete equipment') }}
      </x-jet-danger-button>
    </x-slot>
  </x-jet-dialog-modal>
</div>
