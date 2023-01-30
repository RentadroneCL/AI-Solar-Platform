<div
  x-data="{
    open: $wire.entangle('showDropdown').defer,
  }"
>
  <div
    @click="open = true"
    wire:loading.attr="disabled"
    @submit.prevent
    class="inline-flex items-center justify-start w-full px-4 py-2 my-2 text-base font-medium transition duration-150 ease-in-out border-transparent rounded-md cursor-pointer focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 dark:hover:text-slate-300 dark:active:text-slate-400 dark:focus:text-slate-400 focus:outline-none"
    :class="{ 'text-slate-600': !open, 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700': open }"
  >
    <span>{{ __('Show More...') }}</span> <i class="ml-auto fa-solid fa-fw" :class="{'fa-chevron-right': open, 'fa-chevron-down': !open}"></i>
  </div>
  <div
    x-show="open"
    @click.outside="open = false"
    class="mt-1 overflow-hidden bg-white shadow dark:bg-slate-600/90 sm:rounded-lg"
  >
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-slate-200">{{ __('Feature Information') }}</h3>
      <p class="max-w-2xl mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Anomaly details and location.') }}</p>
    </div>
    <div class="border-t border-slate-200 dark:border-slate-400">
      <dl>
        <div class="flex flex-col justify-start bg-slate-50 dark:bg-slate-600/90">
          <h4 class="inline-flex items-center justify-start px-4 py-3 my-1 font-medium leading-3 sm:px-6 text-slate-900 dark:text-slate-200">
            <i class="mr-2 fa-solid fa-location-arrow fa-fw"></i> {{ __('Location') }}
          </h4>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Zone') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['zone'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-500/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Sub Zone') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['subZone'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('String') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['string'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-500/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Module') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['module'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Serial') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['serial'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-500/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Asset Latitude') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['latEquip'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Asset Longitude') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['longEquip'] ?? 'N/A' }}</dd>
          </div>
          <h4 class="inline-flex items-center justify-start px-4 py-3 my-1 font-medium leading-3 sm:px-6 text-slate-900 dark:text-slate-200">
            <i class="mr-2 fa-solid fa-temperature-half fa-fw"></i> {{ __('Thermal Anomaly') }}
          </h4>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Type') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['failCode'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-500/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Severity') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ $feature['severity'] ?? 'N/A' }}</dd>
          </div>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Max Temp') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ number_format($feature['tempMax'] ?? 0, 2) ?? 'N/A' }} °C</dd>
          </div>
          <div class="py-2 text-sm bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-500/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Mean Temp') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ number_format($feature['tempMean'] ?? 0, 2) ?? 'N/A' }} °C</dd>
          </div>
          <div class="py-2 text-sm sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-600/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Reference Temp') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ number_format($feature['tempRef'] ?? 0, 2) ?? 'N/A' }} °C</dd>
          </div>
          <div class="py-2 text-sm bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-slate-500/90">
            <dt class="font-medium text-slate-500 dark:text-slate-300">{{ __('Reflected Temp') }}</dt>
            <dd class="mt-1 text-slate-900 dark:text-slate-400 sm:col-span-2 sm:mt-0">{{ number_format($feature['tempReflx'] ?? 0, 2) ?? 'N/A' }} °C</dd>
          </div>
          <!-- Media file -->
          <div
            x-data="{
              show: $wire.entangle('hasMediaFile').defer,
            }"
            x-show="show"
            class="py-2 sm:px-6 dark:bg-slate-600/90"
          >
            <h4 class="inline-flex items-center justify-start pt-3 pb-1 my-1 font-medium leading-3 text-slate-900 dark:text-slate-200">
              <i class="mr-2 fa-solid fa-image fa-fw"></i> {{ $feature['img']['name'] ?? ''}}
            </h4>
            <p class="w-full mt-1 text-sm font-semibold uppercase text-slate-600 dark:text-slate-500">{{ $feature['img']['size'] ?? '' }}</p>
            <img src="{{ $feature['img']['file_url'] ?? '!#' }}" alt="{{ $feature['img']['file_name'] ?? '' }}" class="w-full mt-2 mb-4 rounded-lg shadow h-80">
          </div>
        </div>
      </dl>
    </div>
  </div>
</div>
