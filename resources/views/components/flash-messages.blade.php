@props([
    'dismissAfter' => 3000,
    'showValidationSummary' => false,
    'validationSummary' => 'There were some errors with your submission. Please check the form and try again.',
])

@php
    $messages = collect();

    if ($success = session('success')) {
        $messages->push(['type' => 'success', 'text' => $success]);
    }

    if ($error = session('error')) {
        $messages->push(['type' => 'error', 'text' => $error]);
    }

    if ($showValidationSummary && session()->has('errors') && $errors->any()) {
        $messages->push(['type' => 'error', 'text' => $validationSummary]);
    }
@endphp

@if ($messages->isNotEmpty())
    <div
        data-flash-container
        {{ $attributes->merge(['class' => 'fixed left-1/2 top-20 z-50 flex w-full max-w-lg -translate-x-1/2 flex-col items-center gap-2 px-4 pointer-events-none']) }}
    >
        @foreach ($messages as $message)
            <x-flash-message :type="$message['type']">
                {{ $message['text'] }}
            </x-flash-message>
        @endforeach
    </div>

    @once
        <script>
            (function () {
                if (window.__flashMessagesInitialized) {
                    return;
                }

                window.__flashMessagesInitialized = true;

                document.addEventListener('DOMContentLoaded', function () {
                    const dismissAfter = {{ (int) $dismissAfter }};

                    if (dismissAfter <= 0) {
                        return;
                    }

                    document.querySelectorAll('[data-flash-message]').forEach(function (message) {
                        setTimeout(function () {
                            message.classList.add('opacity-0');
                            setTimeout(function () {
                                const container = message.closest('[data-flash-container]');
                                message.remove();
                                if (container && !container.children.length) {
                                    container.remove();
                                }
                            }, 300);
                        }, dismissAfter);
                    });
                });
            })();
        </script>
    @endonce
@endif
