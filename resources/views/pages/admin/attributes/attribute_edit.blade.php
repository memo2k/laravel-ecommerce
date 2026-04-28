@extends('pages.admin.layout')

@section('title', 'Edit attribute – ShopDemo Admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                {{ $attribute->exists ? 'Edit attribute' : 'Add attribute' }}
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Update attribute details and manage its selectable options.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.attribute.save') }}" class="space-y-6 text-sm">
        @csrf

        <input type="hidden" name="id" value="{{ $attribute->id ?? '' }}">

        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Basic information
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Name
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ $attribute->name ?? old('name') }}"
                           class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">
                        Description
                    </label>
                    <textarea rows="4"
                              name="description"
                              class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500">{{ $attribute->description ?? old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <header class="flex items-start justify-between gap-4 px-5 py-4 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Options
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Values customers can pick for this attribute (e.g. Small, Medium, Large).
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                        {{ $attribute->attributeOptions->count() ?? 0 }} {{ Str::plural('option', $attribute->attributeOptions->count() ?? 0) }}
                    </span>
                    <button type="button"
                            id="add-option-button"
                            class="inline-flex items-center justify-center gap-1.5 rounded-md px-3 py-2 text-sm font-medium text-white shadow-sm"
                            style="background-color: var(--color-accent);">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 4a.75.75 0 01.75.75v4.5h4.5a.75.75 0 010 1.5h-4.5v4.5a.75.75 0 01-1.5 0v-4.5h-4.5a.75.75 0 010-1.5h4.5v-4.5A.75.75 0 0110 4z"/>
                        </svg>
                        Add option
                    </button>
                </div>
            </header>

            <div class="px-3 py-3">
                <div class="max-h-[360px] overflow-y-auto pr-1 divide-y divide-slate-100" id="attribute-options">
                    @forelse ($attribute->attributeOptions ?? [] as $index => $option)
                        <div class="option-row flex items-center gap-2 py-2 px-2 transition-colors">
                            <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">

                            <input type="text"
                                   name="options[{{ $index }}][name]"
                                   value="{{ $option->name }}"
                                   placeholder="Name (e.g. Small)"
                                   class="option-input w-1/3 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:line-through">

                            <input type="text"
                                   name="options[{{ $index }}][description]"
                                   value="{{ $option->description }}"
                                   placeholder="Description (optional)"
                                   class="option-input flex-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 placeholder-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:line-through">

                            <button type="button"
                                    title="Remove"
                                    class="remove-option-button shrink-0 rounded-md border border-slate-200 bg-white p-1.5 text-slate-500 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.5H3.75a.75.75 0 000 1.5h.382l.91 11.37A2.75 2.75 0 007.785 19h4.43a2.75 2.75 0 002.742-1.88l.91-11.37h.383a.75.75 0 000-1.5H14v-.5A2.75 2.75 0 0011.25 1h-2.5zM7.5 3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.5h-5v-.5z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <button type="button"
                                    title="Undo"
                                    class="undo-option-button hidden shrink-0 rounded-md border border-red-200 bg-white px-2 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                                Undo
                            </button>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center" id="no-options-placeholder">
                            <p class="text-sm font-medium text-slate-700">No options yet</p>
                            <p class="text-xs text-slate-500 mt-1">Add options such as Small, Medium, Large.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="px-5 py-2 bg-slate-50 border-t border-slate-200">
                <p class="text-xs text-slate-500">Removed options are deleted after saving.</p>
            </div>
        </section>

        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-900">
                Actions
            </h2>
            <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm"
                    style="background-color: var(--color-accent);">
                Save attribute
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let optionIndex = {{ ($attribute->attributeOptions ?? collect())->count() }};

            $('#add-option-button').on('click', function() {
                $('#no-options-placeholder').remove();

                const rowHtml = `
                    <div class="option-row flex items-center gap-2 py-2 px-2 transition-colors" data-new="1" style="background-color: #ecfdf5;">
                        <input type="hidden" name="options[${optionIndex}][id]" value="">

                        <input type="text"
                               name="options[${optionIndex}][name]"
                               placeholder="Name (e.g. Small)"
                               class="option-input w-1/3 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:line-through">

                        <input type="text"
                               name="options[${optionIndex}][description]"
                               placeholder="Description (optional)"
                               class="option-input flex-1 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 placeholder-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:line-through">

                        <button type="button"
                                title="Remove"
                                class="remove-option-button shrink-0 rounded-md border border-slate-200 bg-white p-1.5 text-slate-500 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.5H3.75a.75.75 0 000 1.5h.382l.91 11.37A2.75 2.75 0 007.785 19h4.43a2.75 2.75 0 002.742-1.88l.91-11.37h.383a.75.75 0 000-1.5H14v-.5A2.75 2.75 0 0011.25 1h-2.5zM7.5 3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.5h-5v-.5z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <button type="button"
                                title="Undo"
                                class="undo-option-button hidden shrink-0 rounded-md border border-red-200 bg-white px-2 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                            Undo
                        </button>
                    </div>
                `;

                const $list = $('#attribute-options');
                $list.prepend(rowHtml);
                $list.scrollTop(0);
                optionIndex++;
            });

            $('#attribute-options').on('click', '.remove-option-button', function() {
                const row = $(this).closest('.option-row');
                const isNew = row.attr('data-new') === '1';

                if (isNew) {
                    row.remove();
                    return;
                }

                row.css({
                    'background-color': '#fef2f2'
                });
                row.find('input').prop('disabled', true);
                row.find('.remove-option-button').addClass('hidden');
                row.find('.undo-option-button').removeClass('hidden');
            });

            $('#attribute-options').on('click', '.undo-option-button', function() {
                const row = $(this).closest('.option-row');
                row.css({
                    'background-color': ''
                });

                row.find('input').prop('disabled', false);
                row.find('.undo-option-button').addClass('hidden');
                row.find('.remove-option-button').removeClass('hidden');
            });
        });
    </script>
@endsection
