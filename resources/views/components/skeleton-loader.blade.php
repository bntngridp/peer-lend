@props([
    'type' => 'card', // 'card', 'table', 'dashboard'
])

@if($type === 'table')
    <div class="animate-pulse space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="h-4 w-48 bg-slate-200 rounded-md"></div>
        <div class="space-y-3 pt-4">
            <div class="h-8 w-full bg-slate-100 rounded-xl"></div>
            <div class="h-8 w-full bg-slate-100 rounded-xl"></div>
            <div class="h-8 w-full bg-slate-100 rounded-xl"></div>
        </div>
    </div>
@elseif($type === 'dashboard')
    <div class="animate-pulse space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="h-24 bg-slate-200 rounded-2xl"></div>
            <div class="h-24 bg-slate-200 rounded-2xl"></div>
            <div class="h-24 bg-slate-200 rounded-2xl"></div>
            <div class="h-24 bg-slate-200 rounded-2xl"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 h-72 bg-slate-200 rounded-2xl"></div>
            <div class="h-72 bg-slate-200 rounded-2xl"></div>
        </div>
    </div>
@else
    <div class="animate-pulse p-6 rounded-2xl border border-slate-200 bg-white space-y-4">
        <div class="h-4 w-1/3 bg-slate-200 rounded-md"></div>
        <div class="h-10 w-2/3 bg-slate-200 rounded-xl"></div>
        <div class="h-4 w-full bg-slate-100 rounded-md"></div>
    </div>
@endif
