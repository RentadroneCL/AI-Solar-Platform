<div>
  @if ($files)
    <div class="h-full bg-white dark:bg-slate-800">
      <div class="flex flex-col md:flex-row">
        <div class="w-full md:w-1/5 md:border-r md:border-gray-200 md:dark:border-slate-600">
          <div class="px-4 py-5">
            <h3 class="text-lg font-medium text-slate-900 dark:text-slate-400">{{ __('Map Viewer') }}</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-500">{{ __('This view allows you to navigate a geospatial web map of your solar report.') }}</p>
          </div>

          <div class="flex flex-col items-center justify-center p-4" x-data="{ open: true }">
            <button @click="open = !open" class="inline-flex items-center w-full px-3 py-2 text-sm font-semibold leading-4 transition duration-150 ease-in-out bg-white border border-transparent rounded-md text-slate-500 dark:text-slate-400 dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 hover:text-slate-700 dark:hover:text-slate-500 focus:outline-none focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600">
              <i class="mr-2 fa-solid fa-layer-group fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Layers') }} <i class="ml-auto text-slate-400 fas fa-fw" :class="{ 'fa-chevron-down': !open, 'fa-chevron-up': open }"></i>
            </button>

            <div class="w-full transition-all duration-700" x-show="open">
              <div class="overflow-y-auto overscroll-auto h-96">
                <div class="w-full">
                  {{-- Geotiff layers --}}
                  @if (count($files['geotiff']))
                    @foreach ($files['geotiff'] as $file)
                      <div x-data="layer()"  class="flex items-center justify-start w-full px-1 py-2 my-1">
                        <label class="flex items-center w-full cursor-pointer" title="{{ $file->name }}">
                          <!-- toggle -->
                          <div class="relative">
                            <!-- input -->
                            <input id="{{ $file->id }}" type="checkbox" class="sr-only" @click="activate({ type: 'geotiff', id: {{ $file->id }}, url: '{{ Storage::temporaryUrl($file->getPath(), Carbon::now()->addMinutes(60)) }}' })" id="{{ $file->id }}" />
                            <!-- line -->
                            <div class="w-10 h-4 rounded-full shadow-inner bg-slate-400"></div>
                            <!-- dot -->
                            <div class="absolute w-6 h-6 transition rounded-full shadow bg-slate-50 dark:bg-slate-600 dot -left-1 -top-1"></div>
                          </div>
                          <!-- label -->
                          <div class="ml-3 font-medium text-slate-700 dark:text-slate-500">
                            {{ Str::limit($file->name, 30, '...') }}
                          </div>
                          <i id="spinner-{{ $file->id }}" class="hidden ml-auto text-blue-600 dark:text-slate-500 fas fa-sync-alt fa-fw fa-spin" title="{{ __('Fetching data') }}"></i>
                        </label>
                      </div>
                    @endforeach
                  @endif

                  {{-- GeoJSON Layers --}}
                  @if (count($files['geojson']))
                    @foreach ($files['geojson'] as $file)
                      <div x-data="layer()"  class="flex items-center justify-start w-full px-1 py-2 my-1">
                        <label class="flex items-center w-full cursor-pointer" title="{{ $file->name }}">
                          <!-- toggle -->
                          <div class="relative">
                            <!-- input -->
                            <input id="{{ $file->id }}" type="checkbox" class="sr-only" @click="activate({ type: 'geojson', id: {{ $file->id }}, url: '{{ Storage::temporaryUrl($file->getPath(), Carbon::now()->addMinutes(60)) }}' })" id="{{ $file->id }}" />
                            <!-- line -->
                            <div class="w-10 h-4 rounded-full shadow-inner bg-slate-400"></div>
                            <!-- dot -->
                            <div class="absolute w-6 h-6 transition rounded-full shadow bg-slate-50 dark:bg-slate-600 dot -left-1 -top-1"></div>
                          </div>
                          <!-- label -->
                          <div class="ml-3 font-medium text-slate-700 dark:text-slate-500">
                            {{ Str::limit($file->name, 30, '...') }}
                          </div>
                        </label>
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-col items-center justify-center p-4" x-data="{ open: false }">
            <button @click="open = !open" class="inline-flex items-center w-full px-3 py-2 text-sm font-semibold leading-4 transition duration-150 ease-in-out bg-white border border-transparent rounded-md text-slate-500 dark:text-slate-400 dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 hover:text-slate-700 dark:hover:text-slate-500 focus:outline-none focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600">
              <i class="mr-2 fa-solid fa-rectangle-list fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Legend') }} <i class="ml-auto text-gray-400 fas fa-fw" :class="{ 'fa-chevron-down': !open, 'fa-chevron-up': open }"></i>
            </button>

            <div class="transition-all duration-700" x-show="open">
              <ul class="list-none text-slate-700 dark:text-slate-500">
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  1. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(255, 255, 0);"></div> An Affected Cell or Connection
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  2. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(255, 255, 0);"></div> 2 to 4 Cells Affected
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  3. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(255, 255, 0);"></div> 5 or more Cells Affected
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  4. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(3, 175, 255);"></div> Bypass Diode
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  5. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(229, 0, 3);"></div> Disconnected / Deactivated
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  6. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(229, 0, 3);"></div> Connections or Others
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  7. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(255, 127, 0);"></div> Soiling / Dirty
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  8. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(255, 127, 0);"></div> Damaged Tracker
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  9. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(255, 127, 0);"></div> Shadowing
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  10. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(12, 56, 112);"></div> Missing Panel
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  11. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(229, 0, 3);"></div> Disconnected / Deactivated String
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  12. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(229, 0, 3);"></div> Disconnected / Deactivated Zone
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  13. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(229, 0, 3);"></div> Hot Spot Single
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  14. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(229, 0, 3);"></div> Hot Spot Multi
                </li>
                <li class="inline-flex items-center w-full px-3 py-2 text-sm">
                  15. <div class="w-4 h-4 mx-2 rounded-full" style="background-color:rgb(3, 175, 255);"></div> Bypass Diode Multi
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div id="ol-map-container" class="w-full m-5 rounded h-96 md:h-screen md:w-4/5 md:m-0 md:rounded-none md:rounded-r-lg"></div>
      </div>

      <div
        x-init="overlay = false"
        x-data="{
          overlay: false,
          panelInfo: false,
          anomalyInfo: true,
          imageInfo: false,
          annotations: false,
        }"
        id="slide-over"
        class="fixed"
        :class="{ 'hidden': !overlay }"
        aria-labelledby="slide-over-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="absolute">
          <!--
            Background overlay, show/hide based on slide-over state.

            Entering: "ease-in-out duration-500"
              From: "opacity-0"
              To: "opacity-100"
            Leaving: "ease-in-out duration-500"
              From: "opacity-100"
              To: "opacity-0"
          -->
          <!-- <div class="absolute inset-0 transition-opacity bg-opacity-75 bg-slate-500" aria-hidden="true"></div> -->
          <div class="fixed inset-y-0 right-0 flex w-1/3 pl-10">
            <!--
              Slide-over panel, show/hide based on slide-over state.

              Entering: "transform transition ease-in-out duration-500 sm:duration-700"
                From: "translate-x-full"
                To: "translate-x-0"
              Leaving: "transform transition ease-in-out duration-500 sm:duration-700"
                From: "translate-x-0"
                To: "translate-x-full"
            -->
            <div
              class="relative w-screen"
              x-transition:enter="transition ease duration-300"
              x-transition:enter-start="transform translate-x-0"
              x-transition:enter-end="transform translate-x-64"
              x-transition:leave="transition ease-in duration-300"
              x-transition:leave-start="transform opacity-100"
              x-transition:leave-end="transform opacity-0"
            >
              <!--
                Close button, show/hide based on slide-over state.

                Entering: "ease-in-out duration-500"
                  From: "opacity-0"
                  To: "opacity-100"
                Leaving: "ease-in-out duration-500"
                  From: "opacity-100"
                  To: "opacity-0"
              -->
              <div class="flex flex-col h-full py-16 overflow-y-scroll bg-white shadow-xl dark:bg-slate-800">
                <div class="inline-flex items-center justify-between px-4 py-3 sm:px-6">
                  <h2 class="inline-flex items-center justify-start text-lg font-bold text-slate-700 dark:text-slate-400" id="slide-over-title">
                    <i class="mr-2 text-blue-500 fa-solid fa-solar-panel fa-fw"></i> <span id="panel" class="uppercase"></span>
                  </h2>
                  <button @click="overlay = !overlay" type="button" class="p-1 transition duration-150 bg-transparent bg-opacity-25 border-2 border-transparent border-opacity-25 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 dark:text-slate-400 text-slate-600 ease-in-ou hover:bg-opacity-75 hover:border-opacity-50">
                    <i class="fas fa-times fa-fw"></i>
                  </button>
                </div>

                <div class="relative flex-1 px-4 sm:px-6">
                  <div class="absolute inset-0 px-4 sm:px-6">
                    <div class="h-full" aria-hidden="true">
                      <table class="w-full table-auto" :class="{ 'mb-2': panelInfo }">
                        <button type="button" @click="anomalyInfo = !anomalyInfo" class="inline-flex items-center justify-start w-full px-4 py-2 my-2 font-semibold transition duration-150 ease-in-out border-transparent rounded-md focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 dark:hover:text-slate-300 dark:active:text-slate-400 dark:focus:text-slate-400 focus:outline-none" :class="{ 'text-slate-600': !anomalyInfo, 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700': anomalyInfo }">
                          <i class="mr-2 fa-solid fa-temperature-low fa-fw"></i> {{ __('Thermal Anomaly') }} <i class="ml-auto fas fa-fw" :class="{ 'fa-chevron-down': !anomalyInfo, 'fa-chevron-up': anomalyInfo }"></i>
                        </button>
                        <tbody x-show="anomalyInfo">
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Code') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="fail-code"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Type') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="fail-type"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Severity') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="severity-level"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Max Temperature') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="max-temperature"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Mean Temperature') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="mean-temperature"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Reference Temperature') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="ref-temperature"></td>
                          </tr>
                        </tbody>
                      </table>

                      <table class="w-full table-auto" :class="{ 'mb-2': panelInfo }">
                        <button type="button" @click="panelInfo = !panelInfo" class="inline-flex items-center justify-start w-full px-4 py-2 my-2 font-semibold transition duration-150 ease-in-out border-transparent rounded-md focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 dark:hover:text-slate-300 dark:active:text-slate-400 dark:focus:text-slate-400 focus:outline-none" :class="{ 'text-slate-600': !panelInfo, 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700': panelInfo }">
                          <i class="mr-2 fa-solid fa-location-arrow fa-fw"></i> {{ __('Location') }} <i class="ml-auto fas fa-fw" :class="{ 'fa-chevron-down': !panelInfo, 'fa-chevron-up': panelInfo }"></i>
                        </button>
                        <tbody x-show="panelInfo">
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Zone') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="zone"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Sub Zone') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="sub-zone"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('String') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="string"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Module') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="module"></td>
                          </tr>
                          <tr>
                            <th class="p-4 font-medium text-left border-b text-slate-400 border-slate-100 dark:border-slate-600">{{ __('Serial Number') }}</th>
                            <td class="p-4 border-b text-slate-500 border-slate-100 dark:border-slate-600" id="serial-number"></td>
                          </tr>
                        </tbody>
                      </table>

                      <div class="flex flex-col justify-start w-full">
                        <button type="button" @click="imageInfo = !imageInfo" class="inline-flex items-center justify-start w-full px-4 py-2 my-2 font-semibold transition duration-150 ease-in-out border-transparent rounded-md focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 dark:hover:text-slate-300 dark:active:text-slate-400 dark:focus:text-slate-400 focus:outline-none" :class="{ 'text-slate-600': !imageInfo, 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700': imageInfo }">
                          <i class="mr-2 fa-solid fa-image fa-fw"></i> {{ __('Image File') }} <i class="ml-auto fas fa-fw" :class="{ 'fa-chevron-down': !imageInfo, 'fa-chevron-up': imageInfo }"></i>
                        </button>
                        <div class="block w-full px-4 py-2" x-show="imageInfo">
                          <h2 id="img-filename" class="font-bold text-left text-slate-700 dark:text-slate-400">N/A</h2>
                          <p id="img-size" class="w-full mt-1 text-sm font-semibold uppercase text-slate-600 dark:text-slate-500">N/A</p>
                          <img id="img-file" class="w-full mt-2 mb-4 rounded-lg shadow h-80" src="" alt="">
                        </div>
                      </div>

                      <div class="flex flex-col justify-start w-full">
                        <button type="button" @click="annotations = !annotations" class="inline-flex items-center justify-start w-full px-4 py-2 my-2 font-semibold transition duration-150 ease-in-out border-transparent rounded-md focus:bg-slate-50 dark:focus:bg-slate-600 active:bg-slate-50 dark:active:bg-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 dark:hover:text-slate-300 dark:active:text-slate-400 dark:focus:text-slate-400 focus:outline-none" :class="{ 'text-slate-600': !annotations, 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700': annotations }">
                          <i class="mr-2 fa-solid fa-note-sticky fa-fw"></i> {{ __('Annotations') }} <i class="ml-auto fas fa-fw" :class="{ 'fa-chevron-down': !annotations, 'fa-chevron-up': annotations }"></i>
                        </button>
                        <div class="block w-full px-4 py-2" x-show="annotations">
                          <livewire:annotations :model="$model">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="px-4 py-5 bg-white shadow sm:p-6 sm:rounded-lg">
      <h3 class="text-lg font-medium text-gray-900">{{ __('The files are NOT uploaded yet.') }}</h3>
      <div class="max-w-xl mt-3 text-sm text-gray-600">
        <p>{{ __('Go to the Files management tab and upload all the data related to this inspection.') }}</p>
      </div>
    </div>
  @endif

  <script>
    const layer = () => {
      return {
        checked: false,
        init() {

        },
        activate(payload = {}) {
          this.checked = ! this.checked;

          switch (payload.type) {
            case 'geotiff':
              Livewire.emit('handle-geotiff', payload);
              break;
            case 'geojson':
              Livewire.emit('handle-geojson', payload);
              break;
            default:
              break;
          }
        }
      };
    };

    document.addEventListener('livewire:load', () => {
      // Base layer group.
      const baseLayerGroup = new LayerGroup({
        layers: [
          new TileLayer({
            source: new XYZ({
              url: 'https://mt{0-3}.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}',
            }),
            title: 'Hybrid',
            visible: true,
            maxZoom: 20,
          }),
          new TileLayer({
            source: new XYZ({
              url: 'https://mt{0-3}.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
            }),
            title: 'Terrain',
            visible: false,
            maxZoom: 20,
          }),
          new TileLayer({
            source: new XYZ({
              url: 'https://mt{0-3}.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
            }),
            title: 'Satelite',
            visible: false,
            maxZoom: 20,
          }),
        ],
      });

      // Map controls
      const fullScreenControl = new FullScreen();
      const overViewMapControl = new OverviewMap({
        collapsed: true,
        layers: [baseLayerGroup],
      });

      // Map creation.
      const map = new ol.Map({
        target: 'ol-map-container',
        layers: [
          baseLayerGroup,
        ],
        view: new ol.View({
          center: fromLonLat([{{ $model->site->longitude }}, {{ $model->site->latitude }}], 'EPSG:4326'),
          zoom: 18,
          maxZoom: 20,
          projection: 'EPSG:4326',
        }),
        controls: defaults({ attribution: false }).extend([
          fullScreenControl,
          /* overViewMapControl, */
        ]),
      });

      // Sync map cache.
      syncMap(map);

      /**
      * Get the type of failure.
      *
      * @param {number} code - layer key.
      */
      const getFailType = (code = null) => {
        if (!code) {
          return 'N/A';
        }

        // Available fail types.
        const failTypes = {
          1: "{{ __('AN AFFECTED CELL OR CONNECTION') }}",
          2: "{{ __('2 TO 4 CELLS AFFECTED') }}",
          3: "{{ __('5 OR MORE CELLS AFFECTED') }}",
          4: "{{ __('BYPASS DIODE') }}",
          5: "{{ __('DISCONNECTED / DEACTIVATED SINGLE PANEL') }}",
          6: "{{ __('CONNECTIONS OR OTHERS') }}",
          7: "{{ __('SOILING / DIRTY') }}",
          8: "{{ __('DAMAGED TRACKER') }}",
          9: "{{ __('SHADOWING') }}",
          10: "{{ __('MISSING PANEL') }}",
          11: "{{ __('DISCONNECTED / DEACTIVATED STRING') }}",
          12: "{{ __('DISCONNECTED / DEACTIVATED ZONE') }}",
          13: "{{ __('HOT SPOT SINGLE') }}",
          14: "{{ __('HOT SPOT MULTI') }}",
          15: "{{ __('BYPASS DIODE MULTI') }}",
        };

        // subtract 1 to match the type, because the return value is an array.
        return Object.values(failTypes)[--code] ?? 'N/A';
      };

      /**
      * Get the severity level.
      *
      * @param {number} value - layer key.
      */
      const getSeverityLevel = (value = null) => {
        if (!value) {
          return 'N/A';
        }

        const severityLevels = {
          1: "{{ __('Low / Minor') }}",
          2: "{{ __('Middle / Major') }}",
          3: "{{ __('High / Critical') }}",
          4: "{{ __('Indeterminate') }}",
        };

        return severityLevels[value] ?? 'N/A';
      };

      /**
      * Retrieve the ir image file if it exists.
      *
      * @param {value} filename - filename.
      */
      const getImage = async (value = null) => {
        // placeholder image.
        const notFound = {
          file_name: 'N/A',
          file_url: `https://via.placeholder.com/512`,
          name: 'N/A',
          size: 'N/A',
        };

        if (value === 'undefined' || value === null) {
          return notFound;
        }

        return axios.post("{{ route('retrieve-image') }}", { filename: value, model_id: "{{ $model->id }}" })
          .then(response => {
            if (response.data.status === 'succeeded') {
              return {...response.data.data};
            }

            return notFound;
          })
          .catch(error => notFound);
      };

      // Slide over panel elements initial state.
      const slideOverElement = document.getElementById('slide-over');

      let featurePanel       = document.getElementById('panel');
      let featureSerial      = document.getElementById('serial-number');
      let featureZone        = document.getElementById('zone');
      let featureSubZone     = document.getElementById('sub-zone');
      let featureString      = document.getElementById('string');
      let featureModule      = document.getElementById('module');
      let featureFailCode    = document.getElementById('fail-code');
      let featureFailType    = document.getElementById('fail-type');
      let featureSeverity    = document.getElementById('severity-level');
      let featureTempMax     = document.getElementById('max-temperature');
      let featureTempMean    = document.getElementById('mean-temperature');
      let featureTempRef     = document.getElementById('ref-temperature');
      let featureImgFilename = document.getElementById('img-filename');
      let featureImgSize     = document.getElementById('img-size');
      let featureImgFile     = document.getElementById('img-file');

      const resetFeatures = () => {
        featurePanel.innerHTML       = 'N/A';
        featureSerial.innerHTML      = 'N/A';
        featureZone.innerHTML        = 'N/A';
        featureSubZone.innerHTML     = 'N/A';
        featureString.innerHTML      = 'N/A';
        featureModule.innerHTML      = 'N/A';
        featureFailCode.innerHTML    = 'N/A';
        featureFailType.innerHTML    = 'N/A';
        featureSeverity.innerHTML    = 'N/A';
        featureTempMax.innerHTML     = 'N/A';
        featureTempMean.innerHTML    = 'N/A';
        featureTempRef.innerHTML     = 'N/A';
        featureImgFilename.innerHTML = 'N/A';
        featureImgSize.innerHTML     = 'N/A';
        featureImgFile.innerHTML     = 'N/A';
      };

      // Vector slide over panel information.
      map.on('click', (evt) => {
        // Reset values.
        resetFeatures();

        map.forEachFeatureAtPixel(evt.pixel, async (feature, layer) => {
          const clickedFeaturePanel    = feature.get('panel') ?? 'N/A';
          const clickedFeatureSerial   = feature.get('serial') ?? 'N/A';
          const clickedFeatureZone     = feature.get('zone') ?? 'N/A';
          const clickedFeatureSubZone  = feature.get('subZone') ?? 'N/A';
          const clickedFeatureString   = feature.get('string') ?? 'N/A';
          const clickedFeatureModule   = feature.get('module') ?? clickedFeaturePanel;
          const clickedFeatureFailCode = feature.get('failCode') ?? 'N/A';
          const clickedFeatureFailType = feature.get('failCode') ?? 'N/A';
          const clickedFeatureSeverity = feature.get('severity') ?? 'N/A';
          const clickedFeatureTempMax  = feature.get('tempMax') ?? 0;
          const clickedFeatureTempMean = feature.get('tempMean') ?? 0;
          const clickedFeatureTempRef  = feature.get('tempRef') ?? 0;
          const clickedFeatureFilename = feature.get('filename') ?? null;

          // Set values.
          featurePanel.innerHTML    = clickedFeaturePanel;
          featureSerial.innerHTML   = clickedFeatureSerial;
          featureZone.innerHTML     = clickedFeatureZone;
          featureSubZone.innerHTML  = clickedFeatureSubZone;
          featureString.innerHTML   = clickedFeatureString;
          featureModule.innerHTML   = clickedFeatureModule;
          featureFailCode.innerHTML = clickedFeatureFailCode;
          featureFailType.innerHTML = getFailType(clickedFeatureFailType);
          featureSeverity.innerHTML = getSeverityLevel(clickedFeatureSeverity);
          featureTempMax.innerHTML  = `${clickedFeatureTempMax.toFixed(2)} °C`;
          featureTempMean.innerHTML = `${clickedFeatureTempMean.toFixed(2)} °C`;
          featureTempRef.innerHTML  = `${clickedFeatureTempRef.toFixed(2)} °C`;

          await getImage(clickedFeatureFilename).then((img) => {
            featureImgFilename.innerHTML = clickedFeatureFilename;
            featureImgSize.innerHTML = img.size;
            featureImgFile.setAttribute('src', img.file_url);
            featureImgFile.setAttribute('alt', clickedFeatureFilename);
          });

          slideOverElement.classList.remove('hidden');

          // Check if a panel exists in the database.
          const formFillData = {
            panel_id: clickedFeaturePanel,
            panel_serial: clickedFeatureSerial,
            panel_zone: clickedFeatureZone,
            panel_sub_zone: clickedFeatureSubZone,
            panel_string: clickedFeatureString,
          };

          Livewire.emit('checkingForPanelExistence', formFillData);

          // fulfill form data
          const panelSyncForm = document.getElementById('panel-sync-form');

          if (document.body.contains(panelSyncForm)) {
            Livewire.on('panelExist', evt => console.log(evt));

            const panelSyncFormElements = panelSyncForm.elements;

            const setFormValues = () => {
              panelSyncFormElements['panel_id'].value       = formFillData.panel_id;
              panelSyncFormElements['panel_serial'].value   = formFillData.panel_serial;
              panelSyncFormElements['panel_zone'].value     = formFillData.panel_zone;
              panelSyncFormElements['panel_sub_zone'].value = formFillData.panel_sub_zone;
              panelSyncFormElements['panel_string'].value   = formFillData.panel_string;
            };

            setFormValues();
          }

          Livewire.emit('featureAtPixel', feature);
        });
      });

      // Styling of vector features.
      const stylesForVectorFeatures = (feature) => {
        // Properties.
        let failCodeProperty = feature.get('failCode');

        // Fail types colors - styles for polygons.
        const affectedCellOrConnectionFillStyle = new Fill({
          color: 'rgba(255, 255, 0, 0.3)',
        });

        const twoFourCellsAffectedFillStyle = new Fill({
          color: 'rgba(255, 255, 0, 0.3)',
        });

        const fiveOrMoreCellsAffectedFillStyle = new Fill({
          color: 'rgba(255, 255, 0, 0.3)',
        });

        const bypassDiodeFillStyle = new Fill({
          color: 'rgba(3, 175, 255, 0.3)',
        });

        const disconnectedDeactivatedFillStyle = new Fill({
          color: 'rgba(229, 0, 3, 0.3)',
        });

        const connectionsOrOthersFillStyle = new Fill({
          color: 'rgba(229, 0, 3, 0.3)',
        });

        const soilingDirtyFillStyle = new Fill({
          color: 'rgba(255, 127, 0, 0.3)',
        });

        const damagedTrackerFillStyle = new Fill({
          color: 'rgba(255, 127, 0, 0.3)',
        });

        const shadowingFillStyle = new Fill({
          color: 'rgba(255, 127, 0, 0.3)',
        });

        const missingPanelFillStyle = new Fill({
          color: 'rgba(12, 56, 112, 0.1)',
        });

        const hotSpotSingleFillStyle = new Fill({
          color: 'rgba(229, 0, 3, 0.3)',
        });

        const hotSpotMultiFillStyle = new Fill({
          color: 'rgba(229, 0, 3, 0.3)',
        });

        const bypassDiodeMultiFillStyle = new Fill({
          color: 'rgba(3, 175, 255, 0.3)',
        });

        const defaultFillStyle = new Fill({
          color: 'rgba(0, 60, 136, 0.1)',
        });

        let featureTextLabel = new TextStyle({
          text: failCodeProperty.toString(),
          scale: 1.5,
          fill: new Fill({
            color: 'rgb(31 41 55)',
          }),
        });

        switch (failCodeProperty) {
          case 1:
            return feature.setStyle(new Style({
              fill: affectedCellOrConnectionFillStyle,
              stroke: new Stroke({
                color: [255, 255, 0, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 2:
            return feature.setStyle(new Style({
              fill: twoFourCellsAffectedFillStyle,
              stroke: new Stroke({
                color: [255, 255, 0, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 3:
            return feature.setStyle(new Style({
              fill: fiveOrMoreCellsAffectedFillStyle,
              stroke: new Stroke({
                color: [255, 255, 0, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 4:
            return feature.setStyle(new Style({
              fill: bypassDiodeFillStyle,
              stroke: new Stroke({
                color: [3, 175, 255, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 5:
            return feature.setStyle(new Style({
              fill: disconnectedDeactivatedFillStyle,
              stroke: new Stroke({
                color: [229, 0, 3, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 6:
            return feature.setStyle(new Style({
              fill: connectionsOrOthersFillStyle,
              stroke: new Stroke({
                color: [229, 0, 3, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 7:
            return feature.setStyle(new Style({
              fill: soilingDirtyFillStyle,
              stroke: new Stroke({
                color: [255, 127, 0, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 8:
            return feature.setStyle(new Style({
              fill: damagedTrackerFillStyle,
              stroke: new Stroke({
                color: [255, 127, 0, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 9:
            return feature.setStyle(new Style({
              fill: shadowingFillStyle,
              stroke: new Stroke({
                color: [255, 127, 0, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 10:
            return feature.setStyle(new Style({
              fill: missingPanelFillStyle,
              stroke: new Stroke({
                color: [12, 56, 112, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 11:
            return feature.setStyle(new Style({
              fill: disconnectedDeactivatedFillStyle,
              stroke: new Stroke({
                color: [229, 0, 3, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 12:
            return feature.setStyle(new Style({
              fill: disconnectedDeactivatedFillStyle,
              stroke: new Stroke({
                color: [229, 0, 3, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 13:
            return feature.setStyle(new Style({
              fill: hotSpotSingleFillStyle,
              stroke: new Stroke({
                color: [229, 0, 3, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 14:
            return feature.setStyle(new Style({
              fill: hotSpotMultiFillStyle,
              stroke: new Stroke({
                color: [229, 0, 3, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          case 15:
            return feature.setStyle(new Style({
              fill: bypassDiodeMultiFillStyle,
              stroke: new Stroke({
                color: [3, 175, 255, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;

          default:
            return feature.setStyle(new Style({
              fill: defaultFillStyle,
              stroke: new Stroke({
                color: [0, 60, 136, 0.3],
                width: 1.25,
              }),
              text: featureTextLabel,
            }));
            break;
        }
      };

      // layers collection.
      let store = [];
      window.store = store;

      /**
      * Find the layer in the layers array with the given id.
      *
      * @param {number} id - layer key.
      */
      const getLayerById = (id = null) => store.find(layer => layer.id === id);

      // Events Listeners
      Livewire.on('handle-geojson', payload => {
        const checkbox = document.getElementById(payload.id);

        let data = {...payload};

        if (!getLayerById(data.id)) {
          data.layer = new VectorLayer({
            source: new Vector({
              format: new GeoJSON(),
              url: data.url,
            }),
            style: stylesForVectorFeatures,
            maxZoom: 20,
          });

          store.push(data);
          map.addLayer(data.layer);
          checkbox.setAttribute('checked');
        } else {
          checkbox.hasAttribute('checked')
            ? map.removeLayer(getLayerById(data.id).layer)
            : map.addLayer(getLayerById(data.id).layer);

          checkbox.toggleAttribute('checked');
        }
      });

      Livewire.on('handle-geotiff', async (payload) => {
        const checkbox = document.getElementById(payload.id);

        const spinner = document.getElementById(`spinner-${payload.id}`);

        let data = {...payload};

        if (!getLayerById(data.id)) {
          spinner.classList.remove('hidden');
          // const pool = new Pool();
          const tiff = await fromUrl(data.url);
          const image = await tiff.getImage();
          const bbox = image.getBoundingBox();
          const width = image.getWidth();
          const height = image.getHeight();
          const rgb = await image.readRGB({ width, height });

          // Copy the rgb data from the geotiff to a canvas and then create a data url.
          const canvas = document.createElement('canvas');
          canvas.width = width;
          canvas.height = height;

          const ctx = canvas.getContext('2d');
          const imgData = ctx.getImageData(0, 0, width, height);
          const rgba = imgData.data;

          let j = 0;
          for (let i = 0; i < rgb.length; i += 3) {
            rgba[j] = rgb[i];
            rgba[j + 1] = rgb[i + 1];
            rgba[j + 2] = rgb[i + 2];
            rgba[j + 3] = 255;
            j += 4;
          }

          ctx.putImageData(imgData, 0, 0);

          data.layer = new ImageLayer({
            source: new ImageStatic({
              url: canvas.toDataURL(),
              projection: 'EPSG:4326',
              imageExtent: bbox,
            }),
            visible: true,
            maxZoom: 20,
            opacity: 0.9,
          });

          store.push(data);
          map.addLayer(data.layer);
          document.getElementById(`spinner-${payload.id}`).classList.add('hidden');
          checkbox.setAttribute('checked');
        } else {
          checkbox.hasAttribute('checked')
            ? map.removeLayer(getLayerById(data.id).layer)
            : map.addLayer(getLayerById(data.id).layer);

          checkbox.toggleAttribute('checked');
        }
      });
    });
  </script>
</div>
