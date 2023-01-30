<div class="static flex flex-col justify-start px-4 py-5 bg-white shadow dark:bg-slate-800 sm:p-6 sm:rounded-lg ring-1 ring-slate-900/5">
  <livewire:inspection-annotation-table :inspection="$inspection">
  <!-- Overlay Pane -->
  <div
    x-data="{
      show: $wire.entangle('displayOverlayPane').defer,
    }"
    x-show="show"
    x-transition
    class="absolute z-[110] inset-y-0 right-0 w-1/3 h-auto px-4 py-16 overflow-y-scroll bg-white border border-l-2 border-slate-200 dark:border-slate-600 dark:bg-slate-800"
  >
    <div class="inline-flex items-center justify-between w-full px-4 py-3 sm:px-6">
      <h2 class="inline-flex items-center justify-start text-lg font-bold text-slate-700 dark:text-slate-400">{{ $inspection->name }}</h2>
      <button
        wire:click="$toggle('displayOverlayPane')"
        wire:loading.attr="disabled"
        class="p-1 transition duration-150 bg-transparent bg-opacity-25 border-2 border-transparent border-opacity-25 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 dark:text-slate-400 text-slate-600 ease-in-ou hover:bg-opacity-75 hover:border-opacity-50"
      >
        <i class="fa-solid fa-xmark fa-fw"></i>
      </button>
    </div>
    <div class="px-4 py-3">
      <form wire:submit.prevent="update" class="grid grid-cols-6 gap-4">
        <div class="col-span-6 mb-4">
          <x-jet-label for="state.custom_properties.commissioning_at" value="{{ __('Commissioning date') }}" />
          <x-jet-input id="state.custom_properties.commissioning_at" type="date" class="block w-auto mt-1" wire:model.lazy="state.custom_properties.commissioning_at" />
          <x-jet-input-error for="state.custom_properties.commissioning_at" class="mt-2" />
        </div>
        <div class="col-span-6 mb-4">
          <x-jet-label for="state.custom_properties.title" value="{{ __('Title') }}" />
          <x-jet-input id="title" type="text" class="block w-full mt-1" wire:model.lazy="state.custom_properties.title" placeholder="{{ __('Title') }}" autocomplete="title" required />
          <x-jet-input-error for="state.custom_properties.title" class="mt-2" />
        </div>
        <div class="col-span-6 mb-4">
          <x-jet-label for="state.content" value="{{ __('Content') }}" />
          <textarea wire:model.lazy="state.content" class="w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-slate-600/25 dark:text-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4" placeholder="{{ __('Leave a comment') }}"></textarea>
          <x-jet-input-error for="state.content" class="mt-2" />
        </div>
        <!-- Feature dropdown -->
        <div class="col-span-6 mb-4">
          <livewire:features>
        </div>
        <div class="col-span-6 mb-4">
          <x-jet-label for="state.custom_properties.assignees" value="{{ __('Team Members') }}" />
          <div class="inline-flex items-center justify-start mt-1 space-x-3">
            @forelse ($state['custom_properties']['assignees'] as $assigned)
              <div class="relative mt-1">
                <div
                  wire:key="assigned-{{ $assigned['id'] }}"
                  wire:click="removeAssigned({{ $assigned['id'] }})"
                  class="absolute top-0 right-0 p-1 -mt-2 -mr-2 text-xs border rounded-full shadow-sm cursor-pointer bg-slate-50 border-slate-100 dark:border-slate-200 text-rose-500 hover:bg-slate-100 dark:bg-slate-200 dark:hover-bg-slate-300"
                >
                  <i class="fa-solid fa-xmark fa-fw"></i>
                </div>
                <img class="object-cover border border-transparent rounded-full shadow-inner w-11 h-11 hover:border-2 hover:border-slate-200 dark:hover:border-slate-300" src="{{ $assigned['profile_photo_url'] }}" alt="{{ $assigned['name'] }} avatar's" title="{{ $assigned['email'] }}">
              </div>
            @empty
              <div class="max-w-sm px-3 py-2 rounded-md shadow-sm bg-slate-50 dark:bg-slate-600">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-200">{{ __('Add team members') }}</h3>
                <p class="text-xs text-slate-700 dark:text-slate-400">{{ __("You haven't more suggestion, consider to add more member to the current team") }}</p>
              </div>
            @endforelse
            <div class="static">
              <div
                wire:click="$toggle('displaySuggestions')"
                wire:loading.attr="disabled"
                x-on:click.stop
                class="inline-flex items-center justify-center p-1 border-2 border-dashed rounded-full shadow-sm cursor-pointer w-11 h-11 border-slate-200 hover:border-slate-300 dark:border-slate-300 dark:hover:border-slate-400 text-slate-400 hover:text-slate-400 dark:text-slate-500 dark:hover:text-slate-400"
                title="{{ __('Add user') }}"
              >
                <i class="fa-solid fa-plus fa-fw"></i>
              </div>
              <!-- Suggestion lists -->
              <div
                x-data="{
                  show: $wire.entangle('displaySuggestions').defer,
                }"
                x-on:click.away="show = false"
                x-on:close.stop="show = false"
                class="absolute z-[120] w-max h-auto mt-1 focus:outline-none shadow-lg bg-white rounded-md"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="menu-button"
                tabindex="-1"
              >
                <div
                  x-show="show"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="transform opacity-0 scale-95"
                  x-transition:enter-end="transform opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="transform opacity-100 scale-100"
                  x-transition:leave-end="transform opacity-0 scale-95"
                  class="px-2 py-3 bg-white rounded-md shadow-md dark:bg-slate-900"
                  role="none"
                >
                  <x-jet-label for="query" value="{{ __('Assigned to') }}" />
                  {{-- <x-jet-input id="query" type="text" class="block w-full mt-1" wire:model.lazy="query" placeholder="{{ __('Search...') }}" />
                  <x-jet-input-error for="query" class="mt-2" /> --}}
                  <div class="flex flex-col items-center justify-start mt-2 border divide-y rounded-md dark:divide-slate-600 dark:border-slate-600">
                    @forelse ($suggestions as $suggestion)
                      <div
                        x-on:click="show = false"
                        x-on:click.stop
                        wire:key="suggestion-{{ $suggestion['id'] }}"
                        wire:click="addAssigned({{ $suggestion['id'] }})"
                        class="inline-flex items-center justify-start w-full p-2 bg-white cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-500 dark:bg-slate-800"
                      >
                        <img class="object-cover w-8 h-8 mr-4 border border-transparent rounded-full shadow-inner hover:border-2 hover:border-slate-200 dark:hover:border-slate-300" src="{{ $suggestion['profile_photo_url'] }}" alt="{{ $suggestion['name'] }} avatar's" title="{{ $suggestion['email'] }}">
                        <div class="flex flex-col justify-start w-full text-left">
                          <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-200">{{ $suggestion['name'] }}</h3>
                          <small class="mt-1 text-xs text-slate-700 dark:text-slate-300">{{ $suggestion['email'] }}</small>
                        </div>
                      </div>
                    @empty
                      <div class="max-w-sm px-3 py-2 rounded-md shadow-sm bg-slate-50 dark:bg-slate-600">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-200">{{ __("You haven't more suggestion") }}</h3>
                        <p class="text-xs text-slate-700 dark:text-slate-400">{{ __("consider to add more member to the current team") }}</p>
                      </div>
                    @endforelse
                  </div>
                </div>
              </div>
            </div>
            <x-jet-input-error for="state.custom_properties.assignees" class="mt-2" />
          </div>
        </div>
        <div class="col-span-6 mb-4">
          <x-jet-label for="state.custom_properties.priority" value="{{ __('Priority') }}" />
          <select wire:model.lazy="state.custom_properties.priority" id="type" name="type" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-slate-600/25 dark:text-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <option class="dark:bg-slate-600 dark:text-slate-400" value="">{{ __('Choose an option') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Low')->snake() }}">{{ __('Low') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Medium')->snake() }}">{{ __('Medium') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('High')->snake() }}">{{ __('High') }}</option>
          </select>
          <x-jet-input-error for="state.custom_properties.priority" class="mt-2" />
        </div>
        <div class="col-span-6 mb-4">
          <x-jet-label for="state.custom_properties.status" value="{{ __('Status') }}" />
          <select wire:model.lazy="state.custom_properties.status" id="type" name="type" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-slate-600/25 dark:text-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <option class="dark:bg-slate-600 dark:text-slate-400" value="">{{ __('Choose an option') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('To Do')->snake() }}">{{ __('To Do') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Pending')->snake() }}">{{ __('Pending') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('In Progress')->snake() }}">{{ __('In Progress') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Delayed')->snake() }}">{{ __('Delayed') }}</option>
            <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Done')->snake() }}">{{ __('Done') }}</option>
          </select>
          <x-jet-input-error for="state.custom_properties.status" class="mt-2" />
        </div>
        <div class="col-span-6 mt-4">
          <div class="inline-flex items-center justify-end w-full space-x-4">
            <x-jet-secondary-button wire:click="$toggle('displayOverlayPane')" wire:loading.attr="disabled">{{ __('Cancel') }}</x-jet-button>
            <x-jet-button wire:click='update' wire:loading.attr="disabled">{{ __('Update') }}</x-jet-button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Confirmation Modal -->
  <x-jet-dialog-modal wire:model="confirmingAnnotationDeletion">
    <x-slot name="title">{{ __('Delete Annotation') }}</x-slot>
    <x-slot name="content">
      {{ __('Are you sure you want to delete this annotation? Once this resource is deleted, all of its related data will be permanently deleted. Please enter your password to confirm you would like to permanently delete the resource.') }}
      <div class="mt-4" x-data="{}" x-on:confirming-delete-annotation.window="setTimeout(() => $refs.password.focus(), 250)">
        <x-jet-input type="password" class="block w-3/4 mt-1"
          placeholder="{{ __('Password') }}"
          x-ref="password"
          wire:model.defer="password"
          wire:keydown.enter="destroy" />
        <x-jet-input-error for="password" class="mt-2" />
      </div>
    </x-slot>
    <x-slot name="footer">
      <x-jet-secondary-button wire:click="$toggle('confirmingAnnotationDeletion')" wire:loading.attr="disabled">
        {{ __('Nevermind') }}
      </x-jet-secondary-button>
      <x-jet-danger-button class="ml-2" wire:click="destroy" wire:loading.attr="disabled">
        {{ __('Delete annotation') }}
      </x-jet-danger-button>
    </x-slot>
  </x-jet-dialog-modal>
</div>
