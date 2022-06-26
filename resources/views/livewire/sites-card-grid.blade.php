<div class="flex flex-col w-full">
  <div class="inline-flex w-full max-w-xl mx-auto">
    <x-jet-input wire:keyup="search" type="search" class="w-full mt-1 bg-white border-r-0 rounded-none rounded-l-full dark:border-slate-600 dark:bg-slate-600/25 text-slate-500 dark:text-slate-400" wire:model.defer="query" placeholder="{{ __('Search site...') }}" />
    <div class="p-2 mt-1 border rounded-r-full shadow-sm dark:border-slate-600 bg-slate-200 dark:bg-slate-800/25">
      <i class="fa-solid fa-magnifying-glass text-slate-500 dark:text-slate-400 fa-fw"></i>
    </div>
  </div>

  <div wire:loading class="inline-flex items-center w-auto mx-auto mt-2 mb-4 text-sm text-slate-600 dark:text-slate-400">
    <i class="mr-2 fa-solid fa-spinner fa-fw fa-spin text-slate-500 dark:text-slate-400"></i> {{ __('Searching...') }}
  </div>

  @if (!is_null($sites))
    <div class="my-5 md:grid md:grid-cols-3 md:gap-6">
      @forelse ($sites as $site)
        <livewire:site-card :site="$site" :wire:key="$site->id">
      @empty
        <div class="flex flex-col items-center justify-center col-span-3 px-6 py-12 border-2 border-dashed rounded-lg border-slate-200 dark:border-slate-600">
          <i class="mb-5 fa-solid fa-solar-panel fa-fw fa-5x text-slate-500 dark:text-slate-400"></i>
          <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-400">{{ __('No sites') }}</h2>
          <p class="mt-1 text-slate-600 dark:text-slate-500">{{ __('Register new sites') }}</p>
        </div>
      @endforelse
    </div>
  @endif
</div>
