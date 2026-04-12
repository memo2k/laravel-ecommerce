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

    @yield('scripts')

    <script>
        $(document).on('click', '.remove-product-btn', function() {
            var productId = $(this).data('product-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: '{{ route('remove-from-cart') }}',
                type: 'POST',
                data: { product_id: productId, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $('#cart_preview').html(response.htmlContent);
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
                url: '{{ route('update-cart-quantity') }}',
                type: 'POST',
                data: { product_id: productId, action: action, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $('#cart_preview').html(response.htmlContent);
                }
            });
        });
    </script>
</body>
</html>

