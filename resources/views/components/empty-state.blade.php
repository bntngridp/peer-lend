@props([
    'title' => 'No records found',
    'subtitle' => 'Once new activity or transactions occur, they will appear here.',
    'actionUrl' => null,
    'actionLabel' => null,
])

<div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-xs space-y-4">
    <div class="mx-auto h-16 w-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 font-extrabold text-xl">
        LF
    </div>
    
    <div class="space-y-1">
        <h3 class="text-sm font-extrabold text-slate-900">{{ $title }}</h3>
        <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto leading-relaxed">{{ $subtitle }}</p>
    </div>

    @if($actionUrl && $actionLabel)
        <div class="pt-2">
            <a href="{{ $actionUrl }}" class="inline-flex items-center justify-center py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                {{ $actionLabel }} &rarr;
            </a>
        </div>
    @endif
</div>
