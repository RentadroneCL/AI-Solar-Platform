<div>
  @if ($panels->isEmpty())
    <div class="max-w-xl text-sm text-gray-600">
      <div class="inline-flex items-center justify-start text-lg font-medium text-gray-900">
        <i class="mr-2 text-gray-400 fa-solid fa-table-columns fa-fw"></i> {{ __('There\'s no panel syncing information.') }}
      </div>
      <div class="mt-3 text-sm text-gray-600">{{ __('The panels are synced manually when you report a failure from the map overview active layers.') }}</div>
    </div>
  @else
    <div class="w-full overflow-x-auto">
      <div class="inline-block min-w-full overflow-hidden align-middle">
        <table x-data="panels()" x-init="init()" id="panels-table">
          <thead class="text-xs font-medium tracking-wider text-left text-gray-600 uppercase rounded bg-gray-50">
            <tr class="border-b">
              <th class="px-6 py-3">{{ __('Panel ID') }}</th>
              <th class="px-6 py-3">{{ __('Serial') }}</th>
              <th class="px-6 py-3">{{ __('Zone') }}</th>
              <th class="px-6 py-3">{{ __('Sub Zone') }}</th>
              <th class="px-6 py-3">{{ __('String') }}</th>
              {{-- <th class="relative px-6 py-3 rounded-tr">
                <span class="sr-only">{{ __('Manage') }}</span>
              </th> --}}
            </tr>
          </thead>
          <tbody class="bg-white">
            @foreach ($panels as $panel)
              <tr id="{{ $panel->id }}">
                <td class="px-6 py-4 whitespace-nowrap">{{ $panel->panel_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $panel->panel_serial }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $panel->panel_zone }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $panel->panel_sub_zone }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $panel->panel_string }}</td>
                {{-- <td class="px-6 py-4 whitespace-nowrap">
                  <a class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50" href="#!" title="{{ __('Manage') }}">
                    <i class="text-gray-400 fas fa-cog fa-fw"></i>
                  </a>
                </td> --}}
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

  <script>
    const panels = () => {
      return {
        init() {
          this.render();
        },
        render() {
          return new DataTable(document.getElementById('panels-table'), {
            fixedHeight: true,
          });
        }
      };
    };
  </script>
</div>
