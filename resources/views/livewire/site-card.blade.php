<div class="mt-5 md:mt-0 md:col-span-1">
  <div class="h-full bg-white rounded-lg shadow dark:bg-slate-800 ring-1 ring-slate-900/5">
    <div id="map-container-{{ $site->id }}" class="w-full rounded-t-lg h-52 ol-map-site-card"></div>
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-400">{{ $site->name }}</h3>
      <div class="h-auto max-w-xl mt-3 mb-4 text-sm text-gray-600 dark:text-slate-500">
        <p class="inline-flex items-center mb-2 font-semibold text-slate-700 dark:text-slate-500">
          <i class="mr-2 fa-solid fa-list-check fa-fw text-slate-500"></i> {{ __('Most recent inspection') }}
        </p>
        @if ($site->inspections->isNotEmpty())
          <ul class="list-none">
            @foreach ($site->inspections()->select('id', 'name')->latest()->get()->take(5) as $inspection)
              <li>
                <x-jet-nav-link href="{{ route('inspection.show', $inspection->id) }}">{{ $inspection->name }}</x-jet-nav-link>
              </li>
            @endforeach
          </ul>
        @else
          <p>{{ __('There\'s no inspection yet!') }}</p>
        @endif
      </div>
      <div class="flex justify-end mt-auto">
        <a href="{{ route('site.show', $site) }}" class="inline-flex items-center justify-center px-4 py-2 mt-auto text-sm font-semibold tracking-widest transition duration-150 ease-in-out bg-white border-2 rounded-lg shadow-sm dark:bg-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 border-slate-200 dark:border-slate-600 text-slate-900 hover:text-gray-800 dark:hover:text-slate-400 focus:outline-none focus:border-slate-200 dark:focus:border-indigo-500 focus:shadow-outline-blue dark:active:text-slate-400 active:text-slate-800 active:bg-slate-50 dark:active:bg-slate-600">
          <i class="mr-2 fa-solid fa-solar-panel fa-fw text-slate-500 dark:text-slate-400"></i> {{ __('Manage site') }}
        </a>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('livewire:load', () => {
      new ol.Map({
        target: 'map-container-{{ $site->id }}',
        layers: [
          new TileLayer({
            source: new XYZ({
              url: 'https://mt{0-3}.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}' // 'https://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}'
            })
          }),
        ],
        view: new ol.View({
          center: fromLonLat([{{ $site->longitude }}, {{ $site->latitude }}]),
          zoom: 18,
        }),
        controls: [],
        interactions: [],
      });
    });
  </script>
</div>
