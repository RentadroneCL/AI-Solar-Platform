<div>
  <x-jet-action-section>
    <x-slot name="title">
      {{ __('Site Management') }}
    </x-slot>

    <x-slot name="description">
      {{ __('Register new sites or find the site and click on it to continue to the inspection page.') }}
    </x-slot>

    <x-slot name="content">
      <div class="mb-5">
        <livewire:new-site-dialog-modal-form :user="$user">
      </div>

      <div class="w-full overflow-x-auto border bg-slate-50 rounded-xl">
        <div class="bg-gradient-to-b from-white to-slate-100">
          <div class="overflow-auto rounded-xl">
            <div class="overflow-hidden shadow-sm">
              <table x-data="siteTable()" x-init="init()" id="sites-table" class="w-full text-sm border-collapse table-auto">
                <thead class="text-xs font-medium tracking-wider text-left text-gray-600 uppercase rounded bg-gray-50">
                  <tr>
                    <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b text-slate-400">{{ __('ID') }}</th>
                    <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Name') }}</th>
                    <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Commissioning date') }}</th>
                    <th class="p-4 pt-0 pb-3 pr-8 font-medium text-left border-b text-slate-400">
                      <span class="sr-only">{{ __('Actions') }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white">
                  @foreach ($sites as $site)
                    <tr id="{{ $site->id }}">
                      <td class="p-4 pl-8 border-b border-slate-100 text-slate-500">{{ $site->id }}</td>
                      <td class="p-4 border-b border-slate-100 text-slate-500">{{ $site->name }}</td>
                      <td class="p-4 border-b border-slate-100 text-slate-500">{{ $site->commissioning_date->toDateString() }}</td>
                      <td class="p-4 pr-8 border-b border-slate-100 text-slate-500">
                        <a class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-white border border-transparent rounded-md text-slate-500 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:bg-slate-50 active:bg-slate-50" href="{{ route('site.show', $site) }}" title="{{ __('Manage') }}">
                          <i class="text-slate-400 fa-solid fa-pencil fa-fw"></i>
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
    </x-slot>
  </x-jet-action-section>

  <script>
    const siteTable = () => {
      return {
        init() {
          this.render();
        },
        render() {
          return new DataTable(document.getElementById('sites-table'), {
            fixedHeight: true,
          });
        }
      };
    };
  </script>
</div>
