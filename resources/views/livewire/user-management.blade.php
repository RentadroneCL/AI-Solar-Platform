<div class="flex flex-col justify-start px-4 py-5 bg-white shadow dark:bg-slate-800 sm:p-6 sm:rounded-lg ring-1 ring-slate-900/5">
  <x-jet-button wire:click="$toggle('confirmingUserCreation')" class="w-auto mx-4 mb-4 md:mx-0 md:ml-auto">
    <i class="mr-2 text-blue-300 dark:text-white fa-solid fa-user-plus fa-fw"></i> {{ __('Create user') }}
  </x-jet-button>

  <livewire:user-table>

  {{-- Create user modal --}}
  <x-jet-dialog-modal wire:model="confirmingUserCreation">
    <x-slot name="title">{{ __('New user') }}</x-slot>

    <x-slot name="content">
      <form wire:submit.prevent="store">
        <div class="col-span-6 mb-4">
          <x-jet-label for="name" value="{{ __('Name') }}" />
          <x-jet-input id="name" type="text" class="block w-full mt-1" wire:model.lazy="state.name" autocomplete="name" required />
          <x-jet-input-error for="state.name" class="mt-2" />
        </div>

        <div class="col-span-6 mb-4">
          <x-jet-label for="email" value="{{ __('E-mail') }}" />
          <x-jet-input id="email" type="email" class="block w-full mt-1" wire:model.lazy="state.email" autocomplete="email" required />
          <x-jet-input-error for="state.email" class="mt-2" />
        </div>

        <div class="flex flex-col col-span-6 mb-4">
          <x-jet-label for="password" value="{{ __('Password') }}" />
          <div class="inline-flex justify-between w-full">
            <x-jet-input id="password" type="password" class="block w-full mt-1" wire:model.lazy="state.password" required />
            <x-jet-secondary-button
              x-show="$wire.get('confirmingRandomPassword')"
              wire:click="$emit('copied')"
              @click="navigator.clipboard.writeText($wire.get('state.password'));"
              class="p-3 ml-4"
              title="{{ __('Copy to clipboard') }}"
            >
              <i class="fa-solid fa-clipboard fa-lg fa-fw text-slate-500"></i>
            </x-jet-secondary-button>
          </div>
          <x-jet-action-message class="inline-flex items-center justify-start mt-2" on="copied">
            <i class="p-1 mr-2 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i> {{ __('Copied!') }}
          </x-jet-action-message>
          <x-jet-input-error for="state.password" class="mt-2" />
        </div>

        <x-jet-secondary-button wire:click="randomPassword" class="mb-4">
          <i class="mr-2 fa-solid fa-key fa-fw text-slate-500"></i> {{ __('Generate random password') }}
        </x-jet-secondary-button>

        <div class="col-span-6 mb-4">
          <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
          <x-jet-input id="password_confirmation" type="password" class="block w-full mt-1" wire:model.lazy="state.password_confirmation" required />
          <x-jet-input-error for="state.password_confirmation" class="mt-2" />
        </div>

        <div class="px-6 py-4 mt-5 text-blue-200 bg-blue-500 border-2 border-blue-400 rounded-md shadow-sm">
          <div class="inline-flex items-center justify-start">
            <i class="p-2 mr-4 text-blue-200 bg-blue-400 border border-blue-400 rounded-md shadow-sm fa-solid fa-info fa-fw"></i>
            <p class="text-base">{{ __('If you generate a random password, copy the password to a safe place so you can use it later.') }}</p>
          </div>
        </div>
      </form>
    </x-slot>

    <x-slot name="footer">
      <div class="inline-flex items-center">
        <x-jet-secondary-button wire:click="$toggle('confirmingUserCreation')" wire:loading.attr="disabled">
          {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="store" wire:loading.attr="disabled">
          <i class="mr-2 text-blue-300 dark:text-white fa-solid fa-user-plus fa-fw"></i> {{ __('Create') }}
        </x-jet-button>
      </div>
    </x-slot>
  </x-jet-dialog-modal>
</div>
