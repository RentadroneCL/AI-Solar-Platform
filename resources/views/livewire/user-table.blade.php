<div class="w-full overflow-x-auto" x-data="table()" x-init="init()">
  <div class="inline-block min-w-full overflow-hidden align-middle">
    <table id="users-table" class="min-w-full divide-y divide-gray-200 rounded">
      <thead class="text-xs font-medium tracking-wider text-left text-gray-600 uppercase rounded bg-gray-50">
        <tr>
          <th class="px-6 py-3 rounded-tl">{{ __('Name') }}</th>
          <th class="px-6 py-3">{{ __('Email verification') }}</th>
          <th class="px-6 py-3">{{ __('Onboarding proccess') }}</th>
          <th class="relative px-6 py-3 rounded-tr">
            <span class="sr-only">{{ __('Edit') }}</span>
          </th>
        </tr>
        </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($users as $user)
              <tr id="{{ $user->id }}">
                <td class="px-6 py-4 whitespace-nowrap">
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
                <td class="px-6 py-4 whitespace-nowrap">
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
                <td class="px-6 py-4 whitespace-nowrap">
                  @if (! is_null($user->onboarding) && $user->onboarding->completed_at)
                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                      {{ __('Completed') }}
                    </span>
                  @else
                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-yellow-800 bg-yellow-100 rounded-full">
                      {{ __('Pending') }}
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                  <a class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50" href="{{ route('user.edit', $user) }}" title="{{ __('Edit') }}">
                    <i class="text-gray-400 far fa-edit"></i>
                  </a>
                </td>
              </tr>
            @endforeach
        </tbody>
    </table>
  </div>

  <script>
    const table = () => {
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
