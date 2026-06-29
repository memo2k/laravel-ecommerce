@php
    // Default 3-color palette for the public site (60/30/10)
    $primaryColor = $primaryColor ?? '#1d4ed8';   // 60% - main brand / header
    $secondaryColor = $secondaryColor ?? '#f3f4f6'; // 30% - surfaces / cards
    $accentColor = $accentColor ?? '#f97316';    // 10% - buttons / highlights
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopDemo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body
    class="min-h-screen flex flex-col bg-slate-50 text-slate-900"
    style="
        --color-primary: {{ $primaryColor }};
        --color-secondary: {{ $secondaryColor }};
        --color-accent: {{ $accentColor }};
    "
>
    @include('pages.components.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('pages.components.footer')

    <div
        id="toast_container"
        class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center gap-2 px-4 sm:inset-x-auto sm:right-4 sm:items-end"
        aria-live="polite"
        aria-atomic="true"
    ></div>

    @yield('scripts')
    @stack('scripts')

    <script>
        function showToast(message, type) {
            type = type || 'success';

            var styles = {
                success: 'border-emerald-200 bg-emerald-50 text-emerald-900',
                error: 'border-red-200 bg-red-50 text-red-900',
            };

            var $toast = $(
                '<div role="alert" class="pointer-events-auto flex w-full max-w-sm translate-y-2 items-start gap-3 rounded-lg border px-4 py-3 text-sm opacity-0 shadow-lg transition-all duration-300 sm:w-auto ' +
                (styles[type] || styles.success) +
                '">' +
                '<span class="flex-1">' + $('<div>').text(message).html() + '</span>' +
                '<button type="button" class="shrink-0 leading-none text-current/60 hover:text-current" aria-label="Dismiss">&times;</button>' +
                '</div>'
            );

            $('#toast_container').append($toast);

            requestAnimationFrame(function() {
                $toast.removeClass('translate-y-2 opacity-0');
            });

            function dismissToast() {
                $toast.addClass('translate-y-2 opacity-0');
                setTimeout(function() {
                    $toast.remove();
                }, 300);
            }

            $toast.find('button').on('click', dismissToast);
            setTimeout(dismissToast, 3500);
        }

        function showCartPreviewPanel() {
            if (window.matchMedia('(max-width: 639px)').matches) {
                return;
            }

            var $panel = $('#cart_preview_panel');
            if (!$panel.length) {
                return;
            }
            $('#cart_preview').attr('data-cart-preview-open', '1');
            $panel.removeClass('invisible opacity-0 translate-y-1').addClass('visible opacity-100 translate-y-0');
        }

        $(document).on('mouseenter', '#cart_preview', function() {
            var $wrap = $(this);
            if ($wrap.attr('data-cart-preview-open') !== '1') {
                return;
            }
            $wrap.removeAttr('data-cart-preview-open');
            $('#cart_preview_panel')
                .removeClass('visible opacity-100 translate-y-0')
                .addClass('invisible opacity-0 translate-y-1');
        });

        $(document).on('click', '.remove-product-btn', function() {
            var productId = $(this).data('product-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: '{{ route('cart.remove-product') }}',
                type: 'POST',
                data: { product_id: productId, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $('#cart_products').html(response.cartProductsContent);
                    $('#cart_preview').html(response.cartPreviewContent);

                    if (response.message) {
                        showToast(response.message, 'success');
                    }
                },
                error: function() {
                    showToast('Could not remove product from cart. Please try again.', 'error');
                }
            });
        });

        $(document).on('click', '.update-cart-quantity-btn', function() {
            var productId = $(this).data('product-id');
            var action = $(this).data('action');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: '{{ route('cart.update-quantity') }}',
                type: 'POST',
                data: { product_id: productId, action: action, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $('#cart_products').html(response.cartProductsContent);
                    $('#cart_preview').html(response.cartPreviewContent);

                    if (response.message) {
                        showToast(response.message, 'success');
                    }
                },
                error: function() {
                    showToast('Could not update cart quantity. Please try again.', 'error');
                }
            });
        });

        $(document).on('click', '.add-to-cart', function() {
            var productId = $(this).data('product-id');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: '{{ route('cart.add-to-cart') }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productId,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if (response.htmlContent) {
                        $('#cart_preview').html(response.htmlContent);
                        showCartPreviewPanel();
                    }

                    if (response.message) {
                        showToast(response.message, 'success');
                    }
                },
                error: function() {
                    showToast('Could not add product to cart. Please try again.', 'error');
                }
            });
        });
    </script>
</body>
</html>

