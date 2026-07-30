@extends('layouts.app')

@section('content')
<div class="space-y-6">
    
    <!-- Top Header Bar with Filter Form -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Marketplace</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Browse institutional-grade P2P loan opportunities</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('marketplace.index') }}" class="flex flex-wrap items-center gap-2.5">
            <!-- Search Bar -->
            <div class="relative min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search marketplace..." 
                       class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-xs font-medium text-slate-700 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs">
                <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
            </div>

            <!-- Filter: Risk Grade -->
            <select name="risk_grade" onchange="this.form.submit()" 
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:border-emerald-600 shadow-xs">
                <option value="">Risk Grade (All)</option>
                @foreach(['A', 'B', 'C', 'D'] as $g)
                    <option value="{{ $g }}" {{ request('risk_grade') === $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endforeach
            </select>

            <!-- Filter: Term -->
            <select name="term" onchange="this.form.submit()" 
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:border-emerald-600 shadow-xs">
                <option value="">Term (All)</option>
                <option value="6" {{ request('term') == '6' ? 'selected' : '' }}>6 Months</option>
                <option value="12" {{ request('term') == '12' ? 'selected' : '' }}>12 Months</option>
                <option value="18" {{ request('term') == '18' ? 'selected' : '' }}>18 Months</option>
                <option value="24" {{ request('term') == '24' ? 'selected' : '' }}>24 Months</option>
                <option value="36" {{ request('term') == '36' ? 'selected' : '' }}>36 Months</option>
            </select>

            <!-- Sort By -->
            <select name="sort" onchange="this.form.submit()" 
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:border-emerald-600 shadow-xs">
                <option value="">Sort: Latest</option>
                <option value="interest_desc" {{ request('sort') === 'interest_desc' ? 'selected' : '' }}>Interest Rate (High to Low)</option>
                <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>Amount (High to Low)</option>
            </select>

            @if(request()->anyFilled(['search', 'risk_grade', 'term', 'sort']))
                <a href="{{ route('marketplace.index') }}" class="rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table Container Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        
        <!-- Table Sub-header Bar -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <span class="text-xs font-bold text-slate-500">
                Showing 1-{{ $loans->count() }} of {{ $loans->total() }} opportunities
            </span>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sort by:</span>
                <span class="text-xs font-bold text-slate-800">
                    @if(request('sort') === 'interest_desc') Interest Rate (High to Low)
                    @elseif(request('sort') === 'amount_desc') Loan Amount (High to Low)
                    @else Latest Listings
                    @endif
                </span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">BORROWER</th>
                        <th class="py-3.5 px-6">LOAN AMOUNT</th>
                        <th class="py-3.5 px-6">INTEREST RATE</th>
                        <th class="py-3.5 px-6">TERM</th>
                        <th class="py-3.5 px-6">RISK GRADE</th>
                        <th class="py-3.5 px-6">FUNDING PROGRESS</th>
                        <th class="py-3.5 px-6 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Borrower -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-emerald-700 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                    {{ strtoupper(substr($loan->purpose ?: ($loan->borrower?->profile?->full_name ?? 'L'), 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs line-clamp-1">
                                        {{ $loan->purpose ?: ($loan->borrower?->profile?->full_name ?? 'Institutional Borrower') }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium block">
                                        ID: LN-{{ substr($loan->id, 0, 6) }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Loan Amount -->
                        <td class="py-4 px-6 font-extrabold text-slate-900 text-sm">
                            Rp {{ number_format($loan->amount, 0, ',', '.') }}
                        </td>

                        <!-- Interest Rate -->
                        <td class="py-4 px-6">
                            <span class="text-emerald-700 font-extrabold text-sm">{{ $loan->interest_rate }}%</span>
                        </td>

                        <!-- Term -->
                        <td class="py-4 px-6 text-slate-600 font-semibold">
                            {{ $loan->duration }} months
                        </td>

                        <!-- Risk Grade -->
                        <td class="py-4 px-6">
                            @if($loan->risk_grade === 'A')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    A
                                </span>
                            @elseif($loan->risk_grade === 'B')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                                    B
                                </span>
                            @elseif($loan->risk_grade === 'C')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                    C
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                    D
                                </span>
                            @endif
                        </td>

                        <!-- Funding Progress -->
                        <td class="py-4 px-6 min-w-[200px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">{{ (int)$loan->funded_percentage }}%</span>
                                    <span class="text-slate-400 font-semibold">Rp {{ number_format($loan->fundings()->sum('amount'), 0, ',', '.') }} rem</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('marketplace.show', $loan->id) }}" 
                               class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                                Invest &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <!-- Mock Data Rows matching Stitch Screenshot if DB is empty -->
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-emerald-700 text-white font-black flex items-center justify-center text-xs">A</div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Acme Corp</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">ID: LN-9842</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-slate-900 text-sm">Rp 250.000.000</td>
                        <td class="py-4 px-6"><span class="text-emerald-700 font-extrabold text-sm">12.5%</span></td>
                        <td class="py-4 px-6 text-slate-600 font-semibold">24 months</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">A</span></td>
                        <td class="py-4 px-6 min-w-[200px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">75%</span>
                                    <span class="text-slate-400 font-semibold">Rp 187.500.000 rem</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: 75%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="#" class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">Invest &rarr;</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-blue-600 text-white font-black flex items-center justify-center text-xs">G</div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Global Logistics Ltd</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">ID: LN-9102</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-slate-900 text-sm">Rp 1.200.000.000</td>
                        <td class="py-4 px-6"><span class="text-emerald-700 font-extrabold text-sm">9.8%</span></td>
                        <td class="py-4 px-6 text-slate-600 font-semibold">36 months</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">B</span></td>
                        <td class="py-4 px-6 min-w-[200px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">40%</span>
                                    <span class="text-slate-400 font-semibold">Rp 720.000.000 rem</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: 40%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="#" class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">Invest &rarr;</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-amber-500 text-white font-black flex items-center justify-center text-xs">S</div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Solaris Energy</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">ID: LN-7731</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-slate-900 text-sm">Rp 500.000.000</td>
                        <td class="py-4 px-6"><span class="text-emerald-700 font-extrabold text-sm">11.2%</span></td>
                        <td class="py-4 px-6 text-slate-600 font-semibold">12 months</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">B</span></td>
                        <td class="py-4 px-6 min-w-[200px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">90%</span>
                                    <span class="text-slate-400 font-semibold">Rp 50.000.000 rem</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: 90%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="#" class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">Invest &rarr;</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-slate-800 text-white font-black flex items-center justify-center text-xs">T</div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">TechNova Solutions</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">ID: LN-8004</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-slate-900 text-sm">Rp 150.000.000</td>
                        <td class="py-4 px-6"><span class="text-emerald-700 font-extrabold text-sm">14.5%</span></td>
                        <td class="py-4 px-6 text-slate-600 font-semibold">18 months</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">C</span></td>
                        <td class="py-4 px-6 min-w-[200px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">15%</span>
                                    <span class="text-slate-400 font-semibold">Rp 127.500.000 rem</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="#" class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">Invest &rarr;</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Pagination links -->
    @if($loans->hasPages())
        <div class="mt-6">
            {{ $loans->links() }}
        </div>
    @endif

</div>
@endsection
