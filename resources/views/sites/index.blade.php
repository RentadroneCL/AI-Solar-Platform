<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-slate-400">
      {{ __('Sites') }} <small class="text-sm text-gray-600 dark:text-slate-500">{{ __('- Register new sites or find the site and click on it to continue to the inspection page.') }}</small>
    </h2>
  </x-slot>
  <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <livewire:site-management>
  </div>
</x-app-layout>
