<div class="w-full overflow-x-auto border bg-slate-50 rounded-xl">
  <div class="bg-gradient-to-b from-white to-slate-100">
    <div class="overflow-auto rounded-xl">
      <div class="overflow-hidden shadow-sm">
        <table x-data="equipmentTypeTable()" x-init="init()" id="equipment-type-table" class="w-full text-sm border-collapse table-auto">
          <thead>
            <tr>
              <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b text-slate-400">{{ __('ID') }}</th>
              <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Name') }}</th>
              <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Quantity') }}</th>
              <th class="p-4 pt-0 pb-3 pr-8 font-medium text-left border-b text-slate-400">
                <span class="sr-only">{{ __('Actions') }}</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white">
            @foreach ($equipmentTypes as $equipmentType)
              <tr data-id="{{ $equipmentType->id }}">
                <td class="p-4 pl-8 border-b border-slate-100 text-slate-500">{{ $equipmentType->id }}</td>
                <td class="p-4 border-b border-slate-100 text-slate-500">{{ $equipmentType->name }}</td>
                <td class="p-4 border-b border-slate-100 text-slate-500">{{ $equipmentType->quantity }}</td>
                <td class="p-4 pr-8 border-b border-slate-100 text-slate-500">
                  <div class="inline-flex items-center w-full">
                    <button x-on:click="$wire.emit('editEquipmentType', {{ $equipmentType->id }})" class="px-3 py-2 ml-auto mr-1 text-sm bg-transparent border-transparent rounded-lg active:bg-slate-50 active:text-slate-600 text-slate-500 focus:outline-none hover:bg-slate-50 hover:text-slate-600">
                      <i class="text-slate-400 fa-solid fa-pencil fa-fw"></i>
                    </button>

                    <button x-on:click="$wire.emit('deleteEquipmentType', {{ $equipmentType->id }})" class="px-3 py-2 mr-4 text-sm bg-transparent border-transparent rounded-lg active:bg-slate-50 active:text-red-500 focus:outline-none hover:bg-slate-50 text-rose-400 hover:text-rose-500">
                      <i class="fa-solid fa-trash fa-fw"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit equipment type Confirmation Modal -->
  <x-jet-dialog-modal wire:model="confirmingEquipmentTypeEdition">
    <x-slot name="title">{{ __('Edit type') }}</x-slot>
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
      <form wire:submit.prevent="update">
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
        <x-jet-secondary-button wire:click="$toggle('confirmingEquipmentTypeEdition')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 fa-solid fa-floppy-disk fa-fw"></i> {{ __('Update') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>

  <!-- Delete equipment type Confirmation Modal -->
  <x-jet-dialog-modal wire:model="confirmingEquipmentTypeDeletion">
    <x-slot name="title">{{ __('Delete equipment type') }}</x-slot>
    <x-slot name="content">
      {{ __('Are you sure you want to delete this equipment type? Once this resource is deleted, all of its related data will be permanently deleted. Please enter your password to confirm you would like to permanently delete the resource.') }}

      <div class="mt-4" x-data="{}" x-on:confirming-delete-equipment-type.window="setTimeout(() => $refs.password.focus(), 250)">
        <x-jet-input type="password" class="block w-3/4 mt-1"
          placeholder="{{ __('Password') }}"
          x-ref="password"
          wire:model.defer="password"
          wire:keydown.enter="destroy" />

        <x-jet-input-error for="password" class="mt-2" />
      </div>
    </x-slot>

    <x-slot name="footer">
      <x-jet-secondary-button wire:click="$toggle('confirmingEquipmentTypeDeletion')" wire:loading.attr="disabled">
        {{ __('Nevermind') }}
      </x-jet-secondary-button>

      <x-jet-danger-button class="ml-2" wire:click="destroy" wire:loading.attr="disabled">
        {{ __('Delete equipment type') }}
      </x-jet-danger-button>
    </x-slot>
  </x-jet-dialog-modal>

  <script>
    const equipmentTypeTable = () => {
      return {
        init() {
          this.render();
        },
        render() {
          window.dataTableEquipmentType = new DataTable(document.getElementById('equipment-type-table'), {
            fixedHeight: true,
          });
        }
      };
    };

    window.addEventListener('equipment-type-content-change', event => {
      window.dataTableEquipmentType = new DataTable(document.getElementById('equipment-type-table'), {
        fixedHeight: true,
      });
    });
  </script>
</div>
