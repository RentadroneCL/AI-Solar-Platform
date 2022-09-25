<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
      <x-jet-nav-link class="text-xl font-semibold leading-tight" href="{{ route('site.show', $inspection->site) }}">
        {{ $inspection->site->name }}
      </x-jet-nav-link>
        <small class="ml-4 text-xs text-gray-600 dark:text-slate-500">{{ $inspection->name }} {{ __($inspection->commissioning_date->toFormattedDateString()) }}</small>
      </h2>
  </x-slot>

  @if (Auth::user()->hasRole('administrator'))
    <div class="flex items-center p-4 text-base font-medium text-blue-200 bg-blue-500 shadow-sm">
      <div class="p-2 mr-2 bg-blue-400 border border-blue-400 rounded-md shadow-sm">
        <i class="fa-solid fa-user-ninja fa-fw"></i>
      </div>
      <p>
        {{ __('You are impersonating') }} <a class="font-semibold underline" href={{ route('user.edit', $inspection->site->user) }}>{{ $inspection->site->user->name }}</a>
      </p>
    </div>
  @endif

  <div x-data="{ tab: '#map' }">
    <nav class="bg-gray-200 border-b-2 border-gray-300 dark:bg-slate-800 dark:border-slate-600">
      <div class="px-4 max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-full md:h-16">
          <div class="flex flex-col md:flex-row">
            <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <a class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 cursor-pointer hover:text-gray-700 dark:hover:text-slate-400 hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-slate-400 focus:border-gray-300" :class="{'text-gray-700 dark:text-slate-400 border-blue-400 border-b-2 font-semibold': tab === '#overview'}" @click="tab = '#overview'">
                <i class="mr-2 fa-solid fa-chart-area fa-fw text-slate-500"></i> {{ __('Overview') }}
              </a>
            </div>

            <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <a class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 cursor-pointer hover:text-gray-700 dark:hover:text-slate-400 hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-slate-400 focus:border-gray-300" :class="{'text-gray-700 dark:text-slate-400 border-blue-400 border-b-2 font-semibold': tab === '#map'}" @click="tab = '#map'">
                <i class="mr-2 fa-solid fa-map-location-dot fa-fw text-slate-500"></i> {{ __('Map') }}
              </a>
            </div>

            <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <a class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 cursor-pointer hover:text-gray-700 dark:hover:text-slate-400 hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-slate-400 focus:border-gray-300" :class="{'text-gray-700 dark:text-slate-400 border-blue-400 border-b-2 font-semibold': tab === '#files'}" @click="tab = '#files'">
                <i class="mr-2 fa-solid fa-hard-drive fa-fw text-slate-500"></i> {{ __('Files') }}
              </a>
            </div>

            <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <a class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 cursor-pointer hover:text-gray-700 dark:hover:text-slate-400 hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-slate-400 focus:border-gray-300" :class="{'text-gray-700 dark:text-slate-400 border-blue-400 border-b-2 font-semibold': tab === '#reports'}" @click="tab = '#reports'">
                <i class="mr-2 fa-solid fa-file-pdf fa-fw text-slate-500"></i> {{ __('Reports') }}
              </a>
            </div>

            @if (Auth::user()->hasRole('administrator'))
              <div class="space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <a class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 cursor-pointer hover:text-gray-700 dark:hover:text-slate-400 hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-slate-400 focus:border-gray-300" :class="{'text-gray-700 dark:text-slate-400 border-blue-400 border-b-2 font-semibold': tab === '#settings'}" @click="tab = '#settings'">
                  <i class="mr-2 fa-solid fa-wrench fa-fw text-slate-500"></i> {{ __('Settings') }}
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </nav>

    <div class="max-h-full" x-show="tab === '#overview'" @cloak>
      <div class="py-10 mx-auto sm:px-6 lg:px-8">
        <livewire:overview :inspection="$inspection">
      </div>
    </div>

    <div x-show="tab === '#map'" @cloak>
      <div class="max-w-full px-6 mx-auto md:px-0">
        <livewire:map-viewer :model="$inspection" :files="$inspection->getMedia('orthomosaic-geojson')">
      </div>
    </div>

    <div x-show="tab === '#files'" @cloak>
      <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="px-4 py-5 bg-white shadow sm:px-0 sm:p-6 sm:rounded-lg">
          @if (Auth::user()->hasRole('administrator'))
            <div class="md:grid md:grid-cols-3 md:gap-6">
              <div class="px-4 md:col-span-1">
                <h3 class="text-lg font-medium text-gray-900">{{ __('File Management') }}</h3>
                <div class="mt-1 text-sm text-gray-600">
                  {{ __('Click the upload file button from the right toolbar and add your images from their destination folder, or directly drag them into the upload box.') }}
                </div>
              </div>
              <div class="px-4 mt-5 md:mt-0 md:col-span-2">
                <livewire:upload-files :inspection="$inspection">
                  <div class="max-w-xl mt-3 text-sm text-slate-600">
                    <p class="mb-1">{{ __('If you are uploading data to enable creation of an Orthomosaic, use Orthomosaic/GeoJSON as the file type.') }}</p>
                  </div>
                  <div class="max-w-xl mt-3 text-xs text-gray-600">
                    <p>{{ __('Limit each upload session to a maximum of 200 files.') }}</p>
                    <p>{{ __('The maximum file size session allowed is 900MB.') }}</p>
                    <p>{{ __('File Support: jpg, jpeg, tif, shp, pdf.') }}</p>
                  </div>
              </div>
            </div>
            <x-jet-section-border></x-jet-section-border>
          @endif
          <livewire:files-table :model="$inspection">
        </div>
      </div>
    </div>

    <div x-show="tab === '#reports'" @cloak>
      <div class="max-w-full mx-auto">
        <livewire:report-viewer :model="$inspection" :files="$inspection->getMedia('pdf')">
      </div>
    </div>

    @if (Auth::user()->hasRole('administrator'))
      <div x-show="tab === '#settings'" @cloak>
        <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
          <livewire:update-inspection-information-form :inspection="$inspection">

          <x-jet-section-border></x-jet-section-border>

          <livewire:delete-inspection-form :inspection="$inspection">
        </div>
      </div>
    @endif
  </div>
</x-app-layout>
