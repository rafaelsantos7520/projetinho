@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php($base = 'inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-slate-900/20 disabled:opacity-50 disabled:cursor-not-allowed')
@php($variants = [
    'primary' => 'bg-slate-900 text-white hover:bg-slate-800',
    'secondary' => 'bg-white border border-slate-200 hover:bg-slate-50',
    'danger' => 'bg-white border border-red-200 text-red-700 hover:bg-red-50',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base.' '.($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</button>
