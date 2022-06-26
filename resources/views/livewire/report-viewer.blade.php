<div>
  @if ($files->count())
    <div x-data="{ report: '{{ Storage::temporaryUrl($files->first()->getPath(), Carbon::now()->addMinutes(60)) }}' }" class="bg-white">
      <div class="md:grid md:grid-cols-3 md:gap-0">
        <div class="md:col-span-1 md:border-r md:border-gray-200">
          <div class="px-4 py-5">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Report Viewer') }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ __('Records') }} {{ $files->count() }}</p>
          </div>
          <div class="border-gray-200">
            <ul>
              @foreach ($files as $file)
                <li @click="report = '{{ Storage::temporaryUrl($file->getPath(), Carbon::now()->addMinutes(60)) }}'" class="block py-2 pl-3 pr-4 text-base font-medium text-gray-600 transition duration-150 ease-in-out border-l-4 border-transparent cursor-pointer hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300">{{ $file->name }}</li>
              @endforeach
            </ul>
          </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
          <object x-bind:data="report" type="application/pdf" class="w-full h-screen">
            <p class="px-4 py-5 mb-4 text-sm text-gray-600 hover:text-gray-700 hover:underline">
              {{ __('To view this file please enable JavaScript, and consider upgrading to a recent version of your web browser.') }}
            </p>
            <x-button-link x-bind:href="report">
              {{ __('Download') }}
            </x-button-link>
          </object>
        </div>
      </div>
    </div>
  @else
    <div class="py-10 mx-auto sm:px-6 lg:px-8">
      <div class="flex flex-col items-center justify-center px-6 py-12 border-2 border-dashed rounded-lg border-slate-200 dark:border-slate-600">
        <i class="mb-4 fa-solid fa-file-pdf fa-fw fa-3x text-slate-400"></i>
        <p class="font-semibold text-slate-900 dark:text-slate-400">{{ __('The report files are NOT uploaded yet.') }}</p>
        <div class="mt-3 text-sm text-gray-600 dark:text-slate-500">
          {{ __('Go to the Files management tab and upload all the data related to this inspection.') }}
        </div>
      </div>
    </div>
  @endif
</div>
