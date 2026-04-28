@if (!empty($attributeOptions) && count($attributeOptions))
    <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($attributeOptions as $attributeOption)
            <li class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 hover:border-slate-300">
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500 truncate">
                        {{ $attributeOption->attribute->name ?? 'Attribute' }}
                    </p>
                    <p class="text-sm font-medium text-slate-900 truncate">
                        {{ $attributeOption->name }}
                    </p>
                </div>
                <button type="button" 
                        aria-label="Remove"
                        class="remove-attribute-option-button shrink-0 inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-400 hover:bg-red-50 hover:text-red-600"
                        data-attribute-option-id="{{ $attributeOption->id }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 01-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </li>
        @endforeach
    </ul>
@else
    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
        <p class="text-sm font-medium text-slate-700">No attributes attached yet</p>
        <p class="text-xs text-slate-500 mt-0.5">Use the form above to assign an attribute option.</p>
    </div>
@endif