<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Dashboard') }}</h2>
  </x-slot>

  <div class="px-6 py-10 mx-auto max-w-7xl lg:px-8">
    <livewire:sites-card-grid>
  </div>
</x-app-layout>
