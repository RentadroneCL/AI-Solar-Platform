<div class="flex flex-col justify-start">
  <x-jet-action-message class="inline-flex items-center justify-start my-2" on="saved">
    <i class="p-1 mr-2 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i> {{ __('Updated data!') }}
  </x-jet-action-message>

  <x-jet-action-message class="p-4 my-2 border-2 rounded-lg shadow-sm bg-rose-500 border-rose-400" on="error">
    <p class="inline-flex items-center text-base text-rose-200">
      <i class="p-1 mr-2 border rounded-md shadow-sm text-rose-200 bg-rose-400 border-rose-400 fa-solid fa-exclamation fa-fw"></i> {{ __('An unexpected error has occurred') }}
    </p>
  </x-jet-action-message>

  @if ($site->equipmentTypes->isEmpty())
    <div class="flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed rounded-lg border-slate-200 dark:border-slate-600">
      <i class="mb-4 fa-solid fa-screwdriver-wrench fa-fw fa-3x text-slate-400"></i>
      <p class="font-semibold text-slate-900 dark:text-slate-400">{{ __('No equipment types registered') }}</p>
      <div class="mt-3 text-sm text-gray-600 dark:text-slate-500">
        {{ __('In order to dump data, at least the equipment type ') }} <code class="p-1 text-sm font-semibold text-blue-600 bg-blue-200 rounded-md shadow-sm">{{ __('panel') }}</code> {{ __(' or similar must be defined in the equipment types section.') }}
      </div>
      <x-button-link href="{{ route('site.show', $site) }}" class="w-full mt-4 md:w-auto">
        <i class="mr-2 text-blue-300 dark:text-white fa-solid fa-wrench fa-fw"></i>  {{ __('Create equipment type') }}
      </x-button-link>
    </div>
  @else
    @if (count($dataset))
      {{-- Update inspection data --}}
      <div class="flex flex-col items-center justify-center w-full md:flex-row md:justify-end md:mb-4">
        <x-jet-secondary-button wire:click="$toggle('showImportModal')" class="w-full mb-2 md:mb-0 md:w-auto md:mr-4 md:ml-auto">
          <i class="mr-2 fa-solid fa-cloud-arrow-up fa-fw text-slate-400"></i> {{ __('Import csv') }}
        </x-jet-secondary-button>
        <x-jet-secondary-button wire:click="export" class="w-full mb-2 md:mb-0 md:w-auto">
          <i class="mr-2 fa-solid fa-download fa-fw text-slate-400"></i> {{ __('Download csv') }}
        </x-jet-secondary-button>
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="flex flex-col h-full col-span-4 px-4 py-5 bg-white shadow-md dark:bg-slate-800 sm:p-6 sm:rounded-lg md:col-span-2">
          <h3 class="flex w-full text-xl font-bold text-slate-900 dark:text-slate-200">
            {{ $inspection->name }} <small class="ml-auto text-sm text-slate-600">{{ $inspection->commissioning_date->toDateString() }}</small>
          </h3>
          <p class="max-w-md mt-2 text-sm font-medium text-slate-700 dark:text-slate-400">{{ __('The data presented in the following table count the number of panels affected in the plant, by type of failure, showing subtotals for each category.') }}</p>
          <div class="flex flex-col mt-auto">
            <h3 class="text-xl font-bold text-slate-700 dark:text-slate-400">{{ __('Detected anomalies') }}</h3>
            <span class="mt-2 text-3xl font-bold md:text-5xl text-slate-900 dark:text-slate-200">
              {{ collect($inspection->getCustomProperty('data'))->filter(fn($item) => (int) $item['severity'] !== 4)->count() }}
            </span>
          </div>
        </div>

        <div class="col-span-4 px-4 py-5 bg-white shadow-md dark:bg-slate-800 sm:p-6 sm:rounded-lg md:col-span-2">
          <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ __('Anomalies by severity') }}</h3>
          <div class="flex flex-col items-center justify-center w-full mt-4 md:justify-between md:flex-row">
            <div class="relative w-full">
              <canvas id="anomalies-by-severity"></canvas>
            </div>
            <div class="flex flex-col justify-start w-full px-4 mt-2 md:w-1/2 md:mt-0">
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-900 rounded-full"></span>  {{ __('High / Critical') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-600 rounded-full"></span> {{ __('Middle / Major') }}
              </div>
              <div class="inline-flex items-center justify-start text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-300 rounded-full"></span> {{ __('Low / Minor') }}
              </div>
            </div>
          </div>

          @push('scripts')
            <script>
              document.addEventListener('livewire:load', () => {
                const data = {
                  labels: [
                    '{{ __('High / Critical') }}',
                    '{{ __('Middle / Major') }}',
                    '{{ __('Low / Minor') }}'
                  ],
                  datasets: [{
                    label: '{{ __('Anomalies by severity') }}',
                    data: [
                      {{ $dataset->filter(fn($item) => (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['severity'] === 1)->count() }}
                    ],
                    color: 'rgb(51 65 85)',
                    backgroundColor: [
                      'rgb(30 58 138)',
                      'rgb(37 99 235)',
                      'rgb(147 197 253)'
                    ],
                    hoverBackgroundColor: 'rgb(219 234 254)',
                    hoverOffset: 4
                  }]
                };

                const config = {
                  type: 'doughnut',
                  data: data,
                };

                const chart = new Chart(
                  document.getElementById('anomalies-by-severity'),
                  config
                );
              });
            </script>
          @endpush
        </div>

        <div class="col-span-4 px-4 py-5 bg-white shadow-md dark:bg-slate-800 sm:p-6 sm:rounded-lg">
          <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ __('Anomalies by type') }}</h3>
          <div class="flex flex-col items-center justify-center w-full mt-4 md:justify-between md:flex-row">
            <div class="relative w-full h-auto">
              <canvas id="anomalies-by-type"></canvas>
            </div>
            <div class="flex flex-col justify-start w-full px-4 mt-2 md:w-1/2 md:mt-0">
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 rounded-full bg-blue-50"></span>  {{ __('An affected cell or connection') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-100 rounded-full"></span> {{ __('2 to 4 cells affected') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-200 rounded-full"></span> {{ __('5 or more cells affected') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-300 rounded-full"></span> {{ __('Bypass diode') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-400 rounded-full"></span> {{ __('Disconnected / Deactivated single panel') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-500 rounded-full"></span> {{ __('Connections or others') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-600 rounded-full"></span> {{ __('Soiling / dirty') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-700 rounded-full"></span> {{ __('Damaged tracker') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-800 rounded-full"></span> {{ __('Shadowing') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-900 rounded-full"></span> {{ __('Missing panel') }}
              </div>
              <div class="inline-flex items-center justify-start mb-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-900 rounded-full"></span> {{ __('Disconnected / Deactivated string') }}
              </div>
              <div class="inline-flex items-center justify-start text-sm font-semibold text-slate-600 dark:text-slate-400">
                <span class="p-2 mr-2 bg-blue-900 rounded-full"></span> {{ __('Disconnected / Deactivated zone') }}
              </div>
            </div>
          </div>

          @push('scripts')
            <script>
              document.addEventListener('livewire:load', () => {
                const data = {
                  labels: [
                    '{{ __('An affected cell or connection') }}',
                    '{{ __('2 to 4 cells affected') }}',
                    '{{ __('5 or more cells affected') }}',
                    '{{ __('Bypass diode') }}',
                    '{{ __('Disconnected / Deactivated single panel') }}',
                    '{{ __('Connections or others') }}',
                    '{{ __('Soiling / Dirty') }}',
                    '{{ __('Damaged tracker') }}',
                    '{{ __('Shadowing') }}',
                    '{{ __('Missing paneL') }}',
                    '{{ __('Disconnected / Deactivated string') }}',
                    '{{ __('Disconnected / Deactivated zone') }}'
                  ],
                  datasets: [{
                    label: '{{ __('Anomalies by type') }}',
                    data: [
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 5)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 6)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 7)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 8)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 9)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 10)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 11)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 12)->count() }},
                    ],
                    color: 'rgb(51 65 85)',
                    backgroundColor: [
                      'rgb(239 246 255)',
                      'rgb(219 234 254)',
                      'rgb(191 219 254)',
                      'rgb(147 197 253)',
                      'rgb(96 165 250)',
                      'rgb(59 130 246)',
                      'rgb(37 99 235)',
                      'rgb(29 78 216)',
                      'rgb(30 64 175)',
                      'rgb(30 58 138)',
                      'rgb(30 58 138)',
                      'rgb(30 58 138)'
                    ],
                    hoverBackgroundColor: 'rgb(219 234 254)',
                    hoverOffset: 4
                  }]
                };

                const config = {
                  type: 'doughnut',
                  data: data,
                };

                const chart = new Chart(
                  document.getElementById('anomalies-by-type'),
                  config
                );
              });
            </script>
          @endpush
        </div>

        <div class="col-span-4 px-4 py-5 bg-white shadow-md sm:p-6 sm:rounded-lg dark:bg-slate-800">
          <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ __('# of failures by severity') }}</h3>
          <div class="flex flex-col items-center justify-center w-full mt-4 md:justify-between md:flex-row">
            <div class="relative w-full h-auto">
              <canvas id="anomalies-grouped-by-severity"></canvas>
            </div>
          </div>

          @push('scripts')
            <script>
              document.addEventListener('livewire:load', () => {
                const data = {
                  labels: [
                    '{{ __('An affected cell or connection') }}',
                    '{{ __('2 to 4 cells affected') }}',
                    '{{ __('5 or more cells affected') }}',
                    '{{ __('Bypass diode') }}',
                    '{{ __('Disconnected / Deactivated single panel') }}',
                    '{{ __('Connections or others') }}',
                    '{{ __('Soiling / Dirty') }}',
                    '{{ __('Damaged tracker') }}',
                    '{{ __('Shadowing') }}',
                  ],
                  datasets: [
                    {
                    label: '{{ __('High / Critical') }}',
                    data: [
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 1 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 2 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 3 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 4 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 5 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 6 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 7 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 8 && (int) $item['severity'] === 3)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 9 && (int) $item['severity'] === 3)->count() }},
                    ],
                    color: 'rgb(51 65 85)',
                    backgroundColor: 'rgb(30 58 138)',
                    borderColor: 'rgb(30 58 138)',
                    hoverBackgroundColor: 'rgb(219 234 254)',
                    borderWidth: 1
                  },
                  {
                    label: '{{ __('Middle / Major') }}',
                    data: [
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 1 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 2 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 3 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 4 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 5 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 6 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 7 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 8 && (int) $item['severity'] === 2)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 9 && (int) $item['severity'] === 2)->count() }},
                    ],
                    color: 'rgb(51 65 85)',
                    backgroundColor: 'rgb(37 99 235)',
                    borderColor: 'rgb(37 99 235)',
                    hoverBackgroundColor: 'rgb(219 234 254)',
                    borderWidth: 1
                  },
                  {
                    label: '{{ __('Low / Minor') }}',
                    data: [
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 1 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 2 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 3 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 4 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 5 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 6 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 7 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 8 && (int) $item['severity'] === 1)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 9 && (int) $item['severity'] === 1)->count() }},
                    ],
                    color: 'rgb(51 65 85)',
                    backgroundColor: 'rgb(147 197 253)',
                    borderColor: 'rgb(147 197 253)',
                    hoverBackgroundColor: 'rgb(219 234 254)',
                    borderWidth: 1
                  },
                ]
                };

                const config = {
                  type: 'bar',
                  data: data,
                  options: {
                    scales: {
                      xAxes: [{
                        beginAtZero: true,
                        stacked: true,
                      }],
                      yAxes: [{
                        beginAtZero: true,
                        stacked: true
                      }]
                    },
                    plugins: {
                      legend: {
                        labels: {
                          font: {
                            size: 10
                          }
                        }
                      }
                    }
                  }
                };

                const chart = new Chart(
                  document.getElementById('anomalies-grouped-by-severity'),
                  config
                );
              });
            </script>
          @endpush
        </div>

        <div class="col-span-4 px-4 py-5 bg-white shadow-md dark:bg-slate-800 sm:p-6 sm:rounded-lg">
          <div class="flex flex-col items-center justify-center w-full h-full md:justify-between md:flex-row">
            <div class="flex flex-col justify-start w-full h-full md:w-1/2">
              <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ __('# of confirmed faults') }}</h3>
              <p class="max-w-md mt-2 text-sm font-medium text-slate-700 dark:text-slate-400">
                {{ __('The existence of panels that are under observation due to faults previously detected by the client was confirmed.') }}
              </p>
              <div class="flex flex-col mt-auto">
                <h3 class="text-xl font-bold text-slate-700 dark:text-slate-400">{{ __('Confirmed anomalies') }}</h3>
                <span class="mt-2 text-3xl font-bold md:text-5xl text-slate-900 dark:text-slate-200">
                  {{ collect($inspection->getCustomProperty('data'))->filter(fn($item) => (int) $item['severity'] === 4)->count() }}
                </span>
              </div>
            </div>
            <div class="relative w-full h-auto">
              <canvas id="confirmed-faults"></canvas>
            </div>
          </div>

          @push('scripts')
            <script>
              document.addEventListener('livewire:load', () => {
                const data = {
                  labels: [
                    '{{ __('An affected cell or connection') }}',
                    '{{ __('2 to 4 cells affected') }}',
                    '{{ __('5 or more cells affected') }}',
                    '{{ __('Bypass diode') }}',
                    '{{ __('Disconnected / Deactivated single panel') }}',
                    '{{ __('Connections or others') }}',
                    '{{ __('Soiling / Dirty') }}',
                    '{{ __('Damaged tracker') }}',
                    '{{ __('Shadowing') }}',
                    '{{ __('Missing paneL') }}',
                    '{{ __('Disconnected / Deactivated string') }}',
                    '{{ __('Disconnected / Deactivated zone') }}'
                  ],
                  datasets: [{
                    label: '{{ __('Anomalies by type') }}',
                    data: [
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 1 && (int) $item['severity'] === 4 && (int) $item['severity'] === 4 )->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 2 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 3 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 4 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 5 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 6 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 7 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 8 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 9 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 10 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 11 && (int) $item['severity'] === 4)->count() }},
                      {{ $dataset->filter(fn($item) => (int) $item['failCode'] === 12 && (int) $item['severity'] === 4)->count() }},
                    ],
                    color: 'rgb(51 65 85)',
                    backgroundColor: [
                      'rgb(239 246 255)',
                      'rgb(219 234 254)',
                      'rgb(191 219 254)',
                      'rgb(147 197 253)',
                      'rgb(96 165 250)',
                      'rgb(59 130 246)',
                      'rgb(37 99 235)',
                      'rgb(29 78 216)',
                      'rgb(30 64 175)',
                      'rgb(30 58 138)',
                      'rgb(30 58 138)',
                      'rgb(30 58 138)'
                    ],
                    hoverBackgroundColor: 'rgb(219 234 254)',
                    hoverOffset: 4
                  }]
                };

                const config = {
                  type: 'bar',
                  data: data,
                  options: {
                    scales: {
                      xAxes: [{
                        beginAtZero: true,
                        stacked: true,
                      }],
                      yAxes: [{
                        beginAtZero: true,
                        stacked: true
                      }]
                    },
                    plugins: {
                      legend: {
                        labels: {
                          font: {
                            size: 10
                          }
                        }
                      }
                    }
                  }
                };

                const chart = new Chart(
                  document.getElementById('confirmed-faults'),
                  config
                );
              });
            </script>
          @endpush
        </div>
      </div>
    @else
      <div class="flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed rounded-lg border-slate-200 dark:border-slate-600">
        <i class="mb-4 fa-solid fa-database fa-fw fa-3x text-slate-400"></i>
        <p class="font-semibold text-slate-900 dark:text-slate-400">{{ __('No inspection data') }}</p>
        <div class="mt-3 text-sm text-gray-600 dark:text-slate-500">{{ __('Get started by uploading the inspection data.') }}</div>
        <x-jet-secondary-button wire:click="$toggle('showImportModal')" class="w-full mt-4 md:w-auto">
          <i class="mr-2 fa-solid fa-cloud-arrow-up fa-fw text-slate-400"></i>  {{ __('Import csv') }}
        </x-jet-secondary-button>
      </div>
    @endif
  @endif

  <!-- Import CSV data modal -->
  <x-jet-dialog-modal wire:model="showImportModal">
    <x-slot name="title">{{ __('Import csv file') }}</x-slot>
    <x-slot name="content">
      <p class="text-base text-slate-900 dark:text-slate-400">
        {{ __('You can choose a resource to import them into and match up headings from the CSV to the appropriate fields of the resource.') }}
      </p>
      <div class="hidden mt-4 md:block">
        <h3 class="font-semibold text-slate-900 dark:text-slate-400">{{ __('CSV example file') }}</h3>
        <p class="text-slate-700 dark:text-slate-500">
          {{ __('We will use the following file ') }} <a class="cursor-pointer" href="{{ asset('examples/inspection_data_example.csv') }}" id="csv_example" target="_blank"><code class="p-1 text-sm font-semibold text-blue-600 bg-blue-200 rounded-md shadow-sm">file.csv</code></a>
        </p>
      </div>
      <div class="inline-flex w-full my-4">
        <div class="inline-flex items-center justify-center w-1/6 px-4 py-3 font-semibold tracking-widest transition bg-white border-2 border-r rounded-l-lg shadow-sm cursor-pointer dark:bg-slate-600/25 text-slate-900 dark:text-slate-400 border-slate-200 dark:border-slate-600 hover:text-slate-800 dark:hover:text-slate-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-slate-800 dark:active:text-slate-300 active:bg-slate-50 dark:active:bg-slate-700 disabled:opacity-25">
          <i class="mr-2 cursor-pointer fas fa-folder-open fa-fw fa-lg text-slate-500 dark:text-slate-400"></i>
          <input wire:model="file" id="file" name="file" class="absolute w-1/6 opacity-0 cursor-pointer pin-x pin-y" type="file" accept="text/csv">
        </div>
        <input wire:model="filename" class="w-5/6 px-4 py-3 text-gray-600 border-2 border-l rounded-r-lg shadow-sm dark:text-slate-400 bg-slate-50 dark:bg-slate-600/25 border-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="text" readonly disabled>
      </div>
      <x-jet-input-error for="file" class="mt-2" />
      <div wire:loading class="inline-flex items-center justify-start w-full mt-2 text-sm text-slate-600 dark:text-slate-400">
        <i class="mr-2 fa-solid fa-spinner fa-fw fa-spin text-slate-400"></i> {{ __('Loading...') }}
      </div>
    </x-slot>
    <x-slot name="footer">
      <div class="inline-flex items-center">
        <x-jet-secondary-button wire:click="$toggle('showImportModal')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>
        <x-jet-button class="ml-2" wire:click="import" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 dark:text-white fa-solid fa-cloud-arrow-up fa-fw"></i> {{ __('Upload file') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>
</div>
