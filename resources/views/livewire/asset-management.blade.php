<div>
  <div x-data="{ tab: '#types' }" class="flex flex-col w-full py-2">
    <div class="flex flex-col items-center justify-start space-x-2 border-b dark:border-slate-700 border-slate-100 md:flex-row">
      <div class="flex flex-col items-center justify-center w-full p-2" :class="{'border-b-2 border-blue-500': tab === '#types'}">
        <button @click="tab = '#types'" :class="{'dark:text-slate-200 text-slate-700 font-bold': tab === '#types'}" class="w-full px-4 py-2 font-medium rounded-lg hover:shadow-sm hover:shadow-slate-200 dark:hover:shadow-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600/25 dark:text-slate-500 text-slate-600">
          <i class="mr-2 fa-solid fa-gear fa-fw text-slate-400"></i> {{ __('Equipment types') }}
        </button>
      </div>
      <div class="flex flex-col items-center justify-center w-full p-2" :class="{'border-b-2 border-blue-500': tab === '#equipments'}">
        <button @click="tab = '#equipments'" :class="{'dark:text-slate-200 text-slate-700 font-bold': tab === '#equipments'}" class="w-full px-4 py-2 font-medium rounded-lg hover:shadow-sm hover:shadow-slate-200 dark:hover:shadow-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600/25 dark:text-slate-500 text-slate-600">
          <i class="mr-2 fa-solid fa-toolbox fa-fw text-slate-400"></i>  {{ __('Equipments') }}
        </button>
      </div>
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
</div>
