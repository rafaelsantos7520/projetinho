@props([
    'as' => 'div',
])

<{{ $as }} {{ $attributes->merge(['class' => 'rounded-3xl bg-white border border-slate-200 p-6']) }}>
    {{ $slot }}
</{{ $as }}>
