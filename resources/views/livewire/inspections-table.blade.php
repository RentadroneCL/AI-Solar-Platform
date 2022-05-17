<div>
  @if ($inspections->isEmpty())
    <div class="flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed rounded-lg border-slate-200">
      <i class="mb-4 fa-solid fa-solar-panel fa-fw fa-2x text-slate-500"></i>
      <p class="font-semibold text-slate-900">{{ __('There are no inspections for this site.') }}</p>
      <div class="mt-3 text-sm text-gray-600">{{ __('Go ahead and create a new one!') }}</div>
    </div>
  @else
    <div x-data="inspectionsTable()" x-init="init()" class="w-full overflow-x-auto border bg-slate-50 rounded-xl">
      <div class="bg-gradient-to-b from-white to-slate-100">
        <div class="overflow-auto rounded-xl">
          <div class="overflow-hidden shadow-sm">
            <table id="inspections-table" class="w-full text-sm border-collapse table-auto">
              <thead>
                <tr>
                  <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b text-slate-400">{{ __('ID') }}</th>
                  <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Name') }}</th>
                  <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Commissioning Date') }}</th>
                  <th class="p-4 pt-0 pb-3 pr-8 font-medium text-left border-b text-slate-400">
                    <span class="sr-only">{{ __('Manage') }}</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white">
                @foreach ($inspections as $inspection)
                  <tr id="{{ $inspection->id }}">
                    <td class="p-4 pl-8 border-b border-slate-100 text-slate-500">{{ $inspection->id }}</td>
                    <td class="p-4 border-b border-slate-100 text-slate-500">{{ $inspection->name }}</td>
                    <td class="p-4 border-b border-slate-100 text-slate-500">{{ __($inspection->commissioning_date->toFormattedDateString()) }}</td>
                    <td class="p-4 pr-8 border-b border-slate-100 text-slate-500">
                      <a class="px-2 py-2 ml-auto mr-1 text-sm bg-transparent border-transparent rounded-lg active:bg-slate-50 active:text-slate-600 text-slate-500 focus:outline-none hover:bg-slate-50 hover:text-slate-600" href="{{ route('inspection.show', $inspection) }}" title="{{ __('Manage') }}">
                        <i class="fa-solid fa-eye fa-fw"></i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif

  <script>
    const inspectionsTable = () => {
      return {
        init() {
          this.render();
        },
        render() {
          return new DataTable(document.getElementById('inspections-table'), {
            fixedHeight: true,
          });
        }
      };
    };
  </script>
</div>
