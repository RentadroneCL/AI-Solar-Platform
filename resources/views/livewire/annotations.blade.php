<div>
  <div class="flex flex-col">
    <button wire:click="$toggle('displayAnnotationCreationForm')" class="inline-flex items-center justify-center w-auto px-3 py-2 ml-auto text-sm font-semibold tracking-widest transition duration-150 ease-in-out bg-transparent border border-transparent rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-200 hover:text-gray-800 dark:hover:text-slate-400 focus:outline-none focus:border-slate-200 dark:focus:border-indigo-500 focus:shadow-outline-blue active:text-slate-800 dark:active:text-slate-400 active:bg-slate-50 dark:active:bg-slate-600" title="{{ __('Annotate') }}">
      <i class="mr-2 fa-solid fa-plus fa-fw"></i> {{ __('Annotate') }}
    </button>

    <!-- Annotations list. -->
    <div class="flex flex-col justify-start py-3 border-b">
      <div class="inline-flex justify-start">
        <img class="object-cover w-8 h-8 mb-auto mr-4 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }} avatar's">
        <div class="max-w-xl text-slate-600 dark:text-slate-400">Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse doloribus laudantium fugit aliquid eius velit quam, maiores nobis molestias incidunt repellat non dolor! Assumenda tenetur, possimus deserunt quam perferendis nesciunt!</div>
      </div>
      <div class="flex justify-end">
        <div class="inline-flex items-center justify-between">
          <i class="mr-4 text-gray-400 fas fa-edit"></i>
          <i class="text-red-400 fas fa-trash"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Create equipment type modal -->
  <x-jet-dialog-modal wire:model="displayAnnotationCreationForm" maxWidth="lg">
    <x-slot name="title" class="font-medium text-slate-700 dark:text-slate-400">
      <i class="mr-2 fa-regular fa-note-sticky fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Inspection Annotation') }}
    </x-slot>
    <x-slot name="content">
      <div class="grid h-full grid-cols-2 gap-2 border-2 divide-x-2 rounded-md shadow-sm divide-slate-100 dark:divide-slate-600 border-slate-100 dark:border-slate-600">
        <div class="flex flex-col justify-start col-span-2 px-3 py-4 md:col-span-1">
          <div class="inline-flex items-center w-full mb-2">
            <img class="object-cover w-8 h-8 mr-2 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }} avatar's">
            <h3 class="font-semibold text-slate-600 dark:text-slate-400">{{ Auth::user()->name }}</h3>
          </div>
          <form>
            <x-jet-input id="custom_properties['title']" type="text" class="block w-full mt-1 mb-4" wire:model.lazy="state.custom_properties.title" placeholder="{{ __('Title') }}" />
            <x-jet-input-error for="state.custom_properties.title" class="my-2" />
            <textarea wire:model='state.content' class="w-full border-gray-300 rounded-md shadow-sm dark:bg-slate-600/25 dark:text-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4" placeholder="{{ __("Leave a comment") }}"></textarea>
            <x-jet-input-error for="state.content" class="mt-2" />
          </form>
        </div>
        <div class="flex flex-col justify-start col-span-2 px-3 py-4 space-y-6 md:col-span-1">
          <div class="flex flex-row items-center justify-between">
            <label for="assignees" class="w-1/2 text-sm font-bold text-slate-600 dark:text-slate-500">
              <i class="mr-2 fa-solid fa-people-group fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Assignees') }}
            </label>
            <button class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold tracking-widest transition duration-150 ease-in-out bg-transparent border border-transparent rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-200 hover:text-gray-800 dark:hover:text-slate-400 focus:outline-none focus:border-slate-200 dark:focus:border-indigo-500 focus:shadow-outline-blue active:text-slate-800 dark:active:text-slate-400 active:bg-slate-50 dark:active:bg-slate-600" wire:click="$toggle('displaySuggestions')">
              <i class="mr-2 fa-solid fa-plus fa-fw text-slate-400"></i> {{ __('Add assignees') }}
            </button>
          </div>
          <div x-data>
            <div x-show="$wire.displaySuggestions" class="flex flex-col w-full">
              <div class="grid h-full grid-rows-2 grid-flow-row gap-2 border rounded-lg shadow-sm z-[150] border-slate-100 dark:border-slate-600">
                <div class="row-span-2">
                  @if ($currentTeamIsEmpty)
                    <div class="flex flex-col w-full p-4 text-sm text-blue-200 bg-blue-500 border border-blue-400 rounded-md shadow-sm">
                      <div class="inline-flex items-center justify-start text-sm font-semibold">
                        <i class="mr-2 text-blue-300 fa-solid fa-circle-info fa-fw"></i> {{ __('No team suggestion') }}
                      </div>
                      <div class="flex-col max-w-md mt-2 space-y-1 text-xs">
                        <div>{{ __('There are no users added to your team, you can add users by mail, if them doesn\'t have an account yet an invitation to join will send.') }}</div>
                        <div>{{ __('You can configure your team in the link below.') }}</div>
                      </div>
                      <a class="inline-flex items-center justify-center w-1/2 px-3 py-2 mt-3 text-xs font-medium text-center text-blue-200 bg-blue-400 border border-blue-300 rounded-md shadow-sm hover:text-blue-100 hover:bg-blue-300" href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                        <i class="mr-2 fa-solid fa-people-group fa-fw"></i> {{ __('Configure team.') }}
                      </a>
                    </div>
                  @else
                    @if (count($suggestions))
                      <div class="row-span-2 border-t border-b dark:border-slate-600 border-slate-100">
                        <h3 class="px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-500">{{ __('Suggestions') }}</h3>
                      </div>
                    @endif

                    @foreach ($suggestions as $suggestion)
                      <div class="inline-flex items-center w-full px-6 py-2 bg-transparent rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-600">
                        <x-jet-checkbox wire:click='toggleAssigned({{ $suggestion->id }})' class="mr-4 dark:bg-slate-500 dark:border-slate-600"></x-jet-checkbox>
                        <div class="inline-flex items-center justify-start">
                          <img class="object-cover w-5 h-5 mr-2 rounded-full shadow-inner" src="{{ $suggestion->profile_photo_url }}" alt="{{ $suggestion->name }} avatar's">
                          <h3 class="inline-flex items-center justify-start my-auto text-sm font-medium text-slate-700 dark:text-slate-400">
                            {{ $suggestion->name }} <small class="ml-2 text-xs text-slate-600 dark:text-slate-400">{{ $suggestion->email }}</small>
                          </h3>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
            <div x-show="$wire.displayAssignees" class="flex flex-col w-full mt-2 border rounded-lg border-slate-100 dark:border-slate-600">
              <div class="row-span-2 border-b dark:border-slate-600 border-slate-100">
                <h3 class="px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-500">{{ __('Assigned') }}</h3>
              </div>
              @foreach ($assignees as $assigned)
                <div class="inline-flex items-center w-full px-6 py-2 bg-transparent rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-600">
                  <div class="inline-flex items-center justify-start">
                    <img class="object-cover w-5 h-5 mr-2 rounded-full shadow-inner" src="{{ $assigned['profile_photo_url'] }}" alt="{{ $assigned['name'] }} avatar's">
                    <h3 class="inline-flex items-center justify-start my-auto text-sm font-medium text-slate-700 dark:text-slate-400">
                      {{ $assigned['name'] }} <small class="ml-2 text-xs text-slate-600 dark:text-slate-400">{{ $assigned['email'] }}</small>
                    </h3>
                  </div>
                  <button wire:click='removeAssigned({{ $assigned['id'] }})' class="inline-flex items-center justify-center p-1 ml-auto text-xs font-semibold tracking-widest transition duration-150 ease-in-out bg-transparent border border-transparent rounded-full hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-200 hover:text-gray-800 dark:hover:text-slate-400 focus:outline-none focus:border-slate-200 dark:focus:border-indigo-500 focus:shadow-outline-blue active:text-slate-800 dark:active:text-slate-400 active:bg-slate-50 dark:active:bg-slate-600">
                    <i class="fa-solid fa-xmark fa-fw text-slate-400"></i>
                  </button>
                </div>
              @endforeach
            </div>
            <x-jet-input-error for="state.custom_properties.assignees" class="mt-2" />
          </div>
          <div class="flex flex-row items-center justify-between">
            <label for="assignees" class="w-auto text-sm font-bold text-slate-600 dark:text-slate-500">
              <i class="mr-2 fa-solid fa-calendar fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Commissioning at') }}
            </label>
            <div class="flex flex-col justify-end w-auto">
              <x-jet-input id="commissioning_at" type="date" class="block w-auto" wire:model.defer="state.custom_properties.commissioning_at" />
              <x-jet-input-error for="state.custom_properties.commissioning_at" class="mt-2" />
            </div>
          </div>
          <div class="flex flex-row items-center justify-between">
            <label for="assignees" class="w-1/2 text-sm font-bold text-slate-600 dark:text-slate-500">
              <i class="mr-2 fa-solid fa-triangle-exclamation fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Priority') }}
            </label>
            <div class="flex flex-col justify-end w-full">
              <select wire:model.lazy="state.custom_properties.priority" id="type" name="type" required class="w-full border-gray-300 rounded-md shadow-sm dark:bg-slate-600/25 dark:text-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <option class="dark:bg-slate-600 dark:text-slate-400" value="">{{ __('Choose an option') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Low')->snake() }}">{{ __('Low') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Medium')->snake() }}">{{ __('Medium') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('High')->snake() }}">{{ __('High') }}</option>
              </select>
              <x-jet-input-error for="state.custom_properties.priority" class="mt-2" />
            </div>
          </div>
          <div class="flex flex-row items-center justify-between">
            <label for="assignees" class="w-1/2 text-sm font-bold text-slate-600 dark:text-slate-500">
              <i class="mr-2 fa-solid fa-list-check fa-fw text-slate-400 dark:text-slate-500"></i> {{ __('Status') }}
            </label>
            <div class="flex flex-col justify-end w-full">
              <select wire:model.lazy="state.custom_properties.status" id="type" name="type" required class="w-full border-gray-300 rounded-md shadow-sm dark:bg-slate-600/25 dark:text-slate-200 dark:border-slate-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <option class="dark:bg-slate-600 dark:text-slate-400" value="">{{ __('Choose an option') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('To Do')->snake() }}">{{ __('To Do') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Pending')->snake() }}">{{ __('Pending') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('In Progress')->snake() }}">{{ __('In Progress') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Delayed')->snake() }}">{{ __('Delayed') }}</option>
                <option class="dark:bg-slate-600 dark:text-slate-400" value="{{ Str::of('Done')->snake() }}">{{ __('Done') }}</option>
              </select>
              <x-jet-input-error for="state.custom_properties.status" class="mt-2" />
            </div>
          </div>
        </div>
      </div>
    </x-slot>
    <x-slot name="footer">
      <x-jet-secondary-button wire:click="$toggle('displayAnnotationCreationForm')" wire:loading.attr="disabled">
        {{ __('Cancel') }}
      </x-jet-secondary-button>
      <x-jet-button wire:click='store' class="ml-2" wire:loading.attr="disabled">
        <i class="mr-2 text-blue-300 fa-solid fa-floppy-disk fa-fw"></i> {{ __('Save') }}
      </x-jet-button>
    </x-slot>
  </x-jet-dialog-modal>
</div>
