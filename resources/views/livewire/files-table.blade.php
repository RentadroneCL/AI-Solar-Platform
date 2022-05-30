<div wire:ignore>
  <div class="px-4 py-5 sm:p-6">
    @if (! Auth::user()->hasRole('administrator'))
      <h3 class="mb-4 text-lg font-medium text-slate-900">{{ __('File Management') }}</h3>
    @endif
    <div class="w-full overflow-x-auto border bg-slate-50 rounded-xl">
      <div class="bg-gradient-to-b from-white to-slate-100">
        <div class="overflow-auto rounded-xl">
          <div class="overflow-hidden shadow-sm">
            <table x-data="fileTable()" x-init="init()" id="files-table" class="w-full text-sm border-collapse table-auto">
              <thead>
                <tr>
                  <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b text-slate-400">{{ __('ID') }}</th>
                  <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Filename') }}</th>
                  <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('Collection') }}</th>
                  <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('File Size') }}</th>
                  <th class="p-4 pt-0 pb-3 font-medium text-left border-b text-slate-400">{{ __('File Type') }}</th>
                  <th class="p-4 pt-0 pb-3 pr-8 font-medium text-left border-b text-slate-400">
                    <span class="sr-only">{{ __('Actions') }}</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white">
                @foreach ($files as $file)
                  <tr id="{{ $file->id }}">
                    <td class="p-4 pl-8 border-b border-slate-100 text-slate-500">{{ $file->id }}</td>
                    <td class="p-4 border-b border-slate-100 text-slate-500">
                      {{ $file->file_name }}
                    </td>
                    <td class="p-4 border-b border-slate-100 text-slate-500">
                      {{ $file->collection_name }}
                    </td>
                    <td class="p-4 border-b border-slate-100 text-slate-500">
                      {{ $file->human_readable_size }}
                    </td>
                    <td class="p-4 border-b border-slate-100 text-slate-500">
                      {{ explode('/', $file->mime_type)[1] }}
                    </td>
                    <td class="p-4 pr-8 border-b border-slate-100 text-slate-500">
                      <a href="{!! Storage::temporaryUrl($file->getPath(), Carbon::now()->addMinutes(60)) !!}" download target="_blank" class="inline-flex items-center p-2 mr-2 text-base font-normal tracking-widest text-gray-400 uppercase transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md cursor-pointer hover:text-gray-500 hover:bg-gray-200 active:bg-gray-200 focus:outline-none focus:border-transparent focus:shadow-outline-gray disabled:opacity-25">
                        <i class="fas fa-download fa-fw"></i>
                      </a>
                      @if (Auth::user()->hasRole('administrator'))
                        <button x-on:click='$wire.confirmMediaDeletion({{ $file->id }})' class="inline-flex items-center p-2 text-base font-normal tracking-widest text-gray-400 uppercase transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-500 hover:bg-gray-200 active:bg-gray-200 focus:outline-none focus:border-transparent focus:shadow-outline-gray disabled:opacity-25">
                          <i class="text-red-400 fas fa-trash fa-fw"></i>
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete file Confirmation Modal -->
  <x-jet-dialog-modal wire:model="confirmingMediaDeletion">
    <x-slot name="title">{{ __('Delete Media') }}</x-slot>

    <x-slot name="content">
      {{ __('Are you sure you want to delete this file? Once this file is deleted, all of its resources and data will be permanently deleted.') }}
    </x-slot>

    <x-slot name="footer">
      <x-jet-secondary-button wire:click="$toggle('confirmingMediaDeletion')" wire:loading.attr="disabled">
        {{ __('Nevermind') }}
      </x-jet-secondary-button>

      <x-jet-danger-button class="ml-2" wire:click="destroy()" wire:loading.attr="disabled">
        {{ __('Delete Media') }}
      </x-jet-danger-button>
    </x-slot>
  </x-jet-dialog-modal>

  {{-- Alpine JS --}}
  <script>
    const fileTable = () => {
      return {
        init() {
          this.render();
        },
        render() {
          return new DataTable(document.getElementById('files-table'));
        },
        async saveToDisk(url = null, filename = 'download', mimeType = null) {
          const response = await fetch(url);
          const data = await response.blob();
          const file = new File([data], filename, {type: mimeType});
          saveAs(file);
        }
      };
    };
  </script>

  {{-- Event Listeners --}}
  @push('scripts')
    <script>
      const bytesToHumanReadableSize = (bytes) => {
        const units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        let index;

        for (index = 0; Number(bytes) < 1024; index++) {
          bytes /= 1024;
        }

        return `${new Intl.NumberFormat(options = { maximumSignificantDigits: 2, style: 'unit', unit: 'megabyte' }).format(Math.round(bytes))} ${units[index]}`;
      };

      /* Complete file upload */
      Livewire.on('complete-files-upload', (payload) => {
        const table = document.getElementById('files-table');
        const tbody = table.lastElementChild;

        payload.files.map(item => {
          let tr = document.createElement('tr');
          const tdStyles = ['px-6', 'py-4', 'whitespace-nowrap'];

          const fileNameTd = document.createElement('td');
          fileNameTd.classList.add(...tdStyles);
          fileNameTd.appendChild(document.createTextNode(item.name));
          tr.appendChild(fileNameTd);

          const collectionTd = document.createElement('td');
          collectionTd.classList.add(...tdStyles);
          collectionTd.appendChild(document.createTextNode(payload.collection_name));
          tr.appendChild(collectionTd);

          const fileSizeTd = document.createElement('td');
          fileSizeTd.classList.add(...tdStyles);
          fileSizeTd.appendChild(document.createTextNode(bytesToHumanReadableSize(item.size)));
          tr.appendChild(fileSizeTd);

          const fileTypeTd = document.createElement('td');
          fileTypeTd.classList.add(...tdStyles);
          fileTypeTd.appendChild(document.createTextNode(item.type.split('/')[1]));
          tr.appendChild(fileTypeTd);

          const actionsTd = document.createElement('td');
          actionsTd.classList.add(...tdStyles);

          const linkStyles = ['inline-flex', 'items-center', 'p-2', 'font-semibold', 'text-gray-600', 'hover:underline', 'focus:outline-none', 'transition', 'ease-in-out', 'duration-150'];
          const reloadLink = document.createElement('a');
          reloadLink.classList.add(...linkStyles);
          reloadLink.setAttribute('href', window.location.href)
          reloadLink.appendChild(document.createTextNode('Reload'));
          actionsTd.appendChild(reloadLink);
          tr.appendChild(actionsTd);

          tbody.appendChild(tr);
        });
      });

      /* Deleted file */
      Livewire.on('deleted-media', (payload) => {
        const tr = document.querySelector(`tr[id="${payload}"]`);
        tr.parentNode.removeChild(tr);
      });
    </script>
  @endpush
</div>
