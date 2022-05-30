<div class="w-full overflow-x-auto border bg-slate-50 rounded-xl">
  <div class="bg-gradient-to-b from-white to-slate-100">
    <div class="overflow-auto rounded-xl">
      <div class="overflow-hidden shadow-sm">
        <table x-data="sitesTable()" x-init="init()"  id="sites-table" class="w-full text-sm border-collapse table-auto">
          <thead>
            <tr>
              <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b text-slate-400">{{ __('ID') }}</th>
              <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Name') }}</th>
              <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Owner') }}</th>
              <th class="p-4 pt-0 pb-3 pr-8 font-medium text-left border-b text-slate-400">
                <span class="sr-only">{{ __('Actions') }}</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white">
            @foreach ($sites as $site)
              <tr id="{{ $site->id }}">
                <td class="p-4 pl-8 border-b border-slate-100 text-slate-500">{{ $site-> id }}</td>
                <td class="p-4 border-b border-slate-100 text-slate-500">{{ $site->name }}</td>
                <td class="p-4 border-b border-slate-100 text-slate-500">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-8 h-8">
                      <img class="object-cover w-8 h-8 rounded-full" src="{{ $site->user->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                    <div class="ml-4">
                      <span class="text-sm font-medium text-gray-900">{{ $site->user->name }}</span>
                      <p class="text-sm text-gray-600">{{ $site->user->email }}</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 pr-8 border-b border-slate-100 text-slate-500">
                  <a class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50" href="{{ route('site.show', $site) }}" title="{{ __('Manage') }}">
                    <i class="fa-solid fa-pencil fa-fw text-slate-400"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script>
    const sitesTable = () => {
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
