@props([
    'field',
])

@error($field)
    <p {{ $attributes->merge(['class' => 'mt-1 text-xs text-red-500']) }}>
        {{ $message }}
    </p>
@enderror
