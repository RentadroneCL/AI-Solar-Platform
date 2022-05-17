<div>
  <div x-data="{ tab: '#types' }" class="flex flex-col w-full py-2">
    <div class="flex flex-col items-center justify-start md:flex-row">
      <button @click="tab = '#types'" :class="{'bg-slate-100 text-slate-900 font-semibold': tab === '#types'}" class="w-full px-4 py-2 mx-2 mb-4 font-medium rounded-md shadow-sm md:mb-0 md:w-auto md:mr-2 md:ml-0 bg-slate-50 text-slate-600">
        <i class="mr-2 fa-solid fa-screwdriver-wrench fa-fw"></i> {{ __('Equipment types') }}
      </button>
      <button @click="tab = '#equipments'" :class="{'bg-slate-100 text-slate-900 font-semibold': tab === '#equipments'}" class="w-full px-4 py-2 mx-2 mb-4 font-medium rounded-md shadow-sm md:w-auto md:mb-0 md:mx-2 bg-slate-50 text-slate-600">
        <i class="mr-2 fa-solid fa-warehouse fa-fw"></i>  {{ __('Equipments') }}
      </button>
    </div>

    <div class="w-full mt-4" x-show="tab === '#types'" @cloak>
      <div class="p-4 mb-4 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm">
        <div class="inline-flex items-center">
          <i class="mr-2 fa-solid fa-circle-info fa-fw fa-2x"></i>
          <p class="max-w-md text-sm">
            {{ __('Define all the types of assets that the facilities have, before assigning them in bulk in the next tab.') }}
          </p>
        </div>
      </div>
      <livewire:equipment-type-management :site="$site">
    </div>

    <div class="w-full mt-4" x-show="tab === '#equipments'" @cloak>
      <div class="p-4 mb-4 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm">
        <div class="inline-flex items-center">
          <i class="mr-2 fa-solid fa-circle-info fa-fw fa-2x"></i>
          <p class="max-w-md text-sm">
            {{ __('In this section, you can keep track of the pieces of equipment and inventory vital to the day-to-day operation of their business.') }}
          </p>
        </div>
      </div>
      <livewire:equipment-management :site="$site">
    </div>
  </div>

  {{-- <x-jet-action-message class="p-4 my-2 bg-blue-500 border-2 border-blue-400 rounded-lg shadow-sm" on="imported">
    <p class="inline-flex items-center text-base text-blue-200">
      <i class="p-1 mr-2 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i> {{ __('The files were imported successfully') }}
    </p>
  </x-jet-action-message>

  <x-jet-action-message class="p-4 my-2 border-2 rounded-lg shadow-sm bg-rose-500 border-rose-400" on="error">
    <p class="inline-flex items-center text-base text-rose-200">
      <i class="p-1 mr-2 border rounded-md shadow-sm text-rose-200 bg-rose-400 border-rose-400 fa-solid fa-exclamation fa-fw"></i> {{ __('An unexpected error has occurred') }}
    </p>
  </x-jet-action-message> --}}

  {{-- <livewire:panel-table :panels="$panels"> --}}

  {{-- Import CSV data modal --}}
{{--   <x-jet-dialog-modal wire:model="showImportModal">
    <x-slot name="title">{{ __('Upload CSV file') }}</x-slot>

    <x-slot name="content">
      <p class="text-base text-slate-900">
        {{ __('You can choose a resource to import them into and match up headings from the CSV to the appropriate fields of the resource.') }}
      </p>
      <div class="hidden mt-4 md:block">
        <h3 class="font-semibold text-slate-900">{{ __('CSV example') }}</h3>
        <p class="text-slate-700">
          {{ __('We will use the following file ') }} <code class="p-1 font-semibold text-blue-600 bg-blue-200 rounded-md shadow-sm">file.csv</code> {{ __(' containing the following data:') }}
          <div class="w-full p-4 my-4 text-xs text-indigo-200 bg-indigo-600 border-2 border-indigo-400 rounded-md shadow-sm">
            <code class="flex m-0 text-left">
              panel_id,panel_serial,panel_zone,panel_sub_zone,panel_string
              80,N/A,20,20B,17
              63,01060630000,20,N/A,01
              75,01060750000,20,20B,17
              51,03024510000,20,03,024
            </code>
          </div>
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
  </x-jet-dialog-modal> --}}
</div>
