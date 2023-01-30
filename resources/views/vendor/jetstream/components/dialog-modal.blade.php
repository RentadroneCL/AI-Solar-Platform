@props(['id' => null, 'maxWidth' => null])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4 dark:border-slate-600 dark:bg-slate-800">
        <div class="text-lg font-semibold text-slate-900 dark:text-slate-200">
            {{ $title }}
        </div>

        <div class="mt-4 text-slate-800 dark:text-slate-400">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 text-right bg-slate-100 dark:bg-slate-900/95">
        {{ $footer }}
    </div>
</x-jet-modal>
