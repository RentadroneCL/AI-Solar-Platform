<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Users') }}</h2>
  </x-slot>

  <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
    @if (session('status'))
      <div class="p-4 mb-4 text-sm font-medium text-green-600 bg-green-100 border-2 border-green-300 rounded-md shadow-sm">
        {{ session('status') }}
      </div>
    @endif

    <x-jet-action-section>
      <x-slot name="title">{{ __('User Management') }}</x-slot>
      <x-slot name="description">
        {{ __('Enables admins to control user access and on-board and off-board users to and from IT resources. ') }}
      </x-slot>
      <x-slot name="content">
        <livewire:user-management :users="$users">
      </x-slot>
    </x-jet-action-section>
  </div>
</x-app-layout>
