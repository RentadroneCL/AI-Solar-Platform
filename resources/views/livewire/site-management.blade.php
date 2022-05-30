<div>
  <div class="px-4 py-5 bg-white shadow sm:p-6 sm:rounded-lg">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="px-4 md:col-span-1">
        <div class="text-lg font-semibold text-slate-900">{{ __('Site Management') }}</div>
        <div class="mt-1 text-sm text-slate-600">
          {{ __('Register new sites or find the site and click on it to continue to the inspection page.') }}
        </div>
      </div>
      <div class="px-4 mt-5 md:mt-0 md:col-span-2">
        @if (Auth::user()->hasRole('administrator'))
          <x-jet-button wire:click="$toggle('confirmingSiteCreation')">
            <i class="mr-2 text-blue-300 fa-solid fa-solar-panel fa-fw"></i> {{ __('Create site') }}
          </x-jet-button>
        @endif
      </div>
    </div>
    <x-jet-section-border></x-jet-section-border>
    <livewire:sites-table :sites="$sites">
  </div>

  {{-- Create site modal --}}
  <x-jet-dialog-modal wire:model="confirmingSiteCreation">
    <x-slot name="title">{{ __('New site') }}</x-slot>

    <x-slot name="content">
      <form wire:submit.prevent="store">
        <div class="col-span-6 mb-4 sm:col-span-4">
          <x-jet-label for="user_id" value="{{ __('Owner') }}" />

          @if (is_null($owner))
            <div class="inline-flex w-full">
              <x-jet-input wire:keyup="search" type="text" class="w-full mt-1 border-r-0 rounded-none rounded-l-md" wire:model.defer="query" placeholder="{{ __('Search user...') }}" />
              <div class="p-2 mt-1 border-l shadow-sm bg-slate-200 rounded-r-md">
                <i class="fa-solid fa-magnifying-glass text-slate-500 fa-fw"></i>
              </div>
            </div>
          @endif

          @if (!is_null($users) && $users->isNotEmpty())
            <ul class="mx-1 mb-4 list-none border border-t-0 divide-y shadow-sm bg-slate-50 rounded-b-md">
              @foreach ($users as $user)
                <li wire:click="$emit('selectedOwner', {{ $user->id }})" class="flex items-center justify-start px-4 py-2 cursor-pointer hover:bg-slate-100">
                  <div class="flex-shrink-0 w-8 h-8">
                    <img class="object-cover w-8 h-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                  </div>
                  <div class="ml-4">
                    <span class="text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                    <p class="text-xs text-slate-600">{{ $user->email }}</p>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif

          <div wire:loading class="inline-flex items-center justify-start w-full text-sm text-slate-600">
            <i class="mr-2 fa-solid fa-spinner fa-fw fa-spin text-slate-500"></i> {{ __('Searching...') }}
          </div>

          <x-jet-input-error for="state.user_id" class="mt-2" />

          @if (is_null($owner))
            <div class="p-4 mt-2 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-md shadow-sm">
              <div class="inline-flex items-center justify-start">
                <i class="p-2 mr-4 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i>
                <p class="text-base">
                  {{ __('If the user does not appear in the list. You can create a new one in the ') }} <a class="font-semibold underline hover:text-blue-300" href="{{ route('user.index') }}">{{ __('user admin section.') }}</a>
                </p>
              </div>
            </div>
          @endif

          @if (!is_null($owner))
            <div class="inline-flex items-center justify-start w-full p-4 my-2 border rounded-md shadow bg-slate-50 border-slate-200">
              <div class="flex-shrink-0 w-8 h-8">
                <img class="object-cover w-8 h-8 rounded-full" src="{{ $owner->profile_photo_url }}" alt="{{ $owner->name }}" />
              </div>
              <div class="ml-4">
                <span class="text-sm font-semibold text-slate-900">{{ $owner->name }}</span>
                <p class="text-xs text-slate-600">{{ $owner->email }}</p>
              </div>
              <x-jet-secondary-button wire:click="discardSelection" class="px-1 py-1 ml-auto rounded-full">
                <i class="fa-solid fa-xmark fa-fw text-slate-500"></i>
              </x-jet-secondary-button>
            </div>
          @endif
        </div>

        <div class="col-span-6 mb-4 sm:col-span-4">
          <x-jet-label for="name" value="{{ __('Site name, as it will appear on the report') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.defer="state.name" autocomplete="name" />
          <x-jet-input-error for="state.name" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4 sm:col-span-4">
          <x-jet-label for="address" value="{{ __('Site address, as it will appear on the report') }}" />
          <x-jet-input id="address" type="text" class="block w-full mt-1" wire:model.defer="state.address" autocomplete="address" />
          <x-jet-input-error for="state.address" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4 sm:col-span-4">
          <x-jet-label for="latitude" value="{{ __('Latitude, to at least 5 decimal places') }}" />
          <x-jet-input id="latitude" type="text" class="block w-full mt-1" wire:model.defer="state.latitude" />
          <x-jet-input-error for="state.latitude" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4 sm:col-span-4">
          <x-jet-label for="longitude" value="{{ __('Longitude, to at least 5 decimal places') }}" />
          <x-jet-input id="longitude" type="text" class="block w-full mt-1" wire:model.defer="state.longitude" />
          <x-jet-input-error for="state.longitude" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4 sm:col-span-4">
          <x-jet-label for="commissioning_date" value="{{ __('Commissioning date') }}" />
          <x-jet-input id="commissioning_date" type="date" class="block w-full mt-1" wire:model.defer="state.commissioning_date" />
          <x-jet-input-error for="state.commissioning_date" class="mt-2" />
        </div>
      </form>
    </x-slot>

    <x-slot name="footer">
      <div class="inline-flex items-center">
        <x-jet-secondary-button wire:click="$toggle('confirmingSiteCreation')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="store" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 fa-solid fa-solar-panel fa-fw"></i> {{ __('Create') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>
</div>
