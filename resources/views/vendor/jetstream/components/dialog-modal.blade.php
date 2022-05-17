@props(['id' => null, 'maxWidth' => null])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4">
        <div class="text-lg font-semibold text-slate-900">
            {{ $title }}
        </div>

        <div class="mt-4 text-slate-800">
            {{ $content }}
        </div>
    </div>

    <div class="px-6 py-4 text-right bg-slate-100">
        {{ $footer }}
    </div>
</x-jet-modal>
