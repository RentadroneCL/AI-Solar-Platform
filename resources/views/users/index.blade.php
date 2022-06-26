<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-slate-400">
      {{ __('Users') }} <small class="text-sm text-gray-600 dark:text-slate-500">{{ __('- Enables admins to control user access and on-board and off-board users to and from IT resources.') }}</small>
    </h2>
  </x-slot>

  <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
    @if (session('status'))
      <div class="p-4 mb-4 text-sm font-medium text-green-600 bg-green-100 border-2 border-green-300 rounded-md shadow-sm">
        {{ session('status') }}
      </div>
    @endif

    <livewire:user-management :users="$users">
  </div>
</x-app-layout>
