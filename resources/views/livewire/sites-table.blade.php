<div x-data="table()" x-init="init()" class="px-4 py-5 sm:p-6">
  <div class="w-full overflow-x-auto">
    <div class="inline-block min-w-full overflow-hidden align-middle">
      <table id="sites-table">
        <thead class="text-xs font-medium tracking-wider text-left text-gray-600 uppercase rounded bg-gray-50">
          <tr>
            <th class="px-6 py-3">{{ __('Name') }}</th>
            <th class="px-6 py-3">{{ __('Owner') }}</th>
            <th class="relative px-6 py-3">
              <span class="sr-only">{{ __('Manage') }}</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white">
          @foreach ($sites as $site)
            <tr id="{{ $site->id }}">
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $site->name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
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
              <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                <a class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50" href="{{ route('site.show', $site) }}" title="{{ __('Manage') }}">
                  <i class="text-gray-400 fas fa-cog fa-fw"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <script>
    const table = () => {
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
