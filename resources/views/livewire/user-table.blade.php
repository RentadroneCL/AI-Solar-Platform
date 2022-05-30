<div class="w-full overflow-x-auto border bg-slate-50 rounded-xl">
  <div class="bg-gradient-to-b from-white to-slate-100">
    <div class="overflow-auto rounded-xl">
      <div class="overflow-hidden shadow-sm">
        <table x-data="userTable()" x-init="init()" id="users-table" class="w-full text-sm border-collapse table-auto">
          <thead class="text-xs font-medium tracking-wider text-left text-gray-600 uppercase rounded bg-gray-50">
            <tr>
              <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b text-slate-400">{{ __('ID') }}</th>
              <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Name') }}</th>
              <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Email verification') }}</th>
              <th class="p-4 pt-0 pb-3 pr-8 font-medium text-left border-b text-slate-400">
                <span class="sr-only">{{ __('Actions') }}</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white">
            @foreach ($users as $user)
              <tr id="{{ $user->id }}">
                <td class="p-4 pl-8 border-b border-slate-100 text-slate-500">{{ $user->id }}</td>
                <td class="p-4 border-b border-slate-100 text-slate-500">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-8 h-8">
                      <img class="object-cover w-8 h-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                    <div class="ml-4">
                      <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                      <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                  </div>
                </td>
                <td class="p-4 border-b border-slate-100 text-slate-500">
                  @if ($user->email_verified_at)
                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                      {{ __('Verified') }}
                    </span>
                  @else
                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-yellow-800 bg-yellow-100 rounded-full">
                      {{ __('Pending') }}
                    </span>
                  @endif
                </td>
                <td class="p-4 pr-8 border-b border-slate-100 text-slate-500">
                  <a class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-white border border-transparent rounded-md text-slate-500 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:bg-slate-50 active:bg-slate-50" href="{{ route('user.edit', $user) }}" title="{{ __('Edit') }}">
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
  <script>
    const userTable = () => {
      return {
        init() {
          this.render();
        },
        render() {
          return new DataTable(document.getElementById('users-table'), {
            fixedHeight: true,
          });
        }
      };
    };
  </script>
</div>
