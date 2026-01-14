@props([
    'id',
    'title',
    'description' => null,
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" class="w-full max-w-md rounded-3xl bg-white border border-slate-200 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div id="{{ $id }}-title" class="text-lg font-semibold">{{ $title }}</div>
                    @if ($description)
                        <div class="text-sm text-slate-600 mt-1">{{ $description }}</div>
                    @endif
                </div>
                <button type="button" class="text-sm px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50" data-close-modal="{{ $id }}">
                    Fechar
                </button>
            </div>

            <div class="mt-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
