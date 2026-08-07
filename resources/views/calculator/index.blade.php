@if(Auth::check())
    @extends('layouts.app')

    @section('content')
    <div class="max-w-7xl mx-auto w-full space-y-8">
        <!-- Header Banner -->
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 uppercase tracking-wider">
                {{ __('Calculator') }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Calculator') }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                {{ __('Real-time Loan Simulation') }}
            </p>
        </div>

        <!-- 2-Column Calculator Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Input Form (Spans 5 Cols) -->
            <div class="lg:col-span-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">{{ __('Calculator') }}</h3>

                <form id="calculatorForm" class="space-y-5">
                    @csrf

                    <!-- Amount Range & Number Input -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">{{ __('Loan Amount') }}</label>
                            <span id="amountDisplay" class="text-sm font-extrabold text-emerald-700">Rp 10.000.000</span>
                        </div>
                        <input type="range" id="amountRange" min="{{ $minAmount }}" max="{{ $maxAmount }}" step="500000" value="10000000"
                               class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-emerald-700 bg-slate-200">
                        <div class="flex justify-between text-[10px] text-slate-400 font-bold mt-1">
                            <span>Rp {{ number_format($minAmount, 0, ',', '.') }}</span>
                            <span>Rp {{ number_format($maxAmount, 0, ',', '.') }}</span>
                        </div>
                        <input type="number" id="amountInput" value="10000000" min="{{ $minAmount }}" max="{{ $maxAmount }}" step="any"
                               class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-emerald-600">
                    </div>

                    <!-- Duration Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Tenor (Months)') }}</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach([3, 6, 12, 24] as $month)
                            <button type="button" data-duration="{{ $month }}"
                                    class="duration-btn rounded-xl border py-2.5 text-xs font-bold transition-all
                                        {{ $month === 12 ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                {{ $month }} {{ __('Mo') }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="durationInput" value="12">
                    </div>

                    <!-- Risk Grade Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Credit Risk Grade') }}</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['A', 'B', 'C', 'D'] as $grade)
                            <button type="button" data-grade="{{ $grade }}"
                                    class="grade-btn rounded-xl border py-2.5 text-xs font-bold transition-all
                                        {{ $grade === 'A' ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                Grade {{ $grade }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="gradeInput" value="A">
                        <p id="gradeDescription" class="mt-2 text-[11px] text-slate-500 font-medium">
                            {{ __('Grade A: Interest rate 8–10% / yr. Very low risk profile.') }}
                        </p>
                    </div>

                    <!-- Calculate Button -->
                    <button type="submit" id="calculateBtn"
                            class="w-full py-3 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        <span id="btnText">{{ __('Calculate Loan') }} &rarr;</span>
                    </button>
                </form>
            </div>

            <!-- Right Results Panel (Spans 7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <div id="resultsPanel" class="space-y-6">
                    <!-- Summary Stats Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-2xl bg-emerald-700 text-white p-4 shadow-xs">
                            <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider block">{{ __('MONTHLY INSTALLMENT') }}</span>
                            <span id="res_monthly" class="text-lg font-black text-white mt-1 block truncate">Rp {{ __n('916.667') }}</span>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">{{ __('TOTAL REPAYMENT') }}</span>
                            <span id="res_total" class="text-base font-extrabold text-slate-900 dark:text-slate-100 mt-1 block truncate">Rp {{ __n('11.000.000') }}</span>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">{{ __('TOTAL INTEREST') }}</span>
                            <span id="res_interest" class="text-base font-extrabold text-emerald-700 dark:text-emerald-400 mt-1 block truncate">Rp {{ __n('1.000.000') }}</span>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">{{ __('PROVISION FEE') }} (1%)</span>
                            <span id="res_fee" class="text-base font-extrabold text-slate-900 dark:text-slate-100 mt-1 block truncate">Rp {{ __n('100.000') }}</span>
                        </div>
                    </div>

                    <!-- Amortization Schedule Table Card -->
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('Amortization Schedule') }}</h3>
                            <span id="res_duration_badge" class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ __n('12') }} {{ __('Months') }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-950/60 border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                        <th class="py-3 px-4">{{ __('MO') }}</th>
                                        <th class="py-3 px-4 text-right">{{ __('TOTAL INSTALLMENT') }}</th>
                                        <th class="py-3 px-4 text-right">{{ __('PRINCIPAL') }}</th>
                                        <th class="py-3 px-4 text-right">{{ __('INTEREST') }}</th>
                                        <th class="py-3 px-4 text-right">{{ __('REMAINING PRINCIPAL') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="scheduleBody" class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                                    {{-- Rendered dynamically via JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JavaScript Calculation Engine -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const amountRange   = document.getElementById('amountRange');
        const amountInput   = document.getElementById('amountInput');
        const amountDisplay = document.getElementById('amountDisplay');
        const durationInput = document.getElementById('durationInput');
        const gradeInput    = document.getElementById('gradeInput');
        const gradeDesc     = document.getElementById('gradeDescription');
        const currentLocale = '{{ app()->getLocale() }}';

        function convertToArabicNumerals(str) {
            if (currentLocale !== 'ar') return str;
            const westernDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            const arabicDigits  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return String(str).replace(/[0-9]/g, d => arabicDigits[d]);
        }

        const gradeDescriptions = {
            A: "{{ __('Grade A: Interest rate 8–10% / yr. Very low risk profile.') }}",
            B: "{{ __('Grade B: Interest rate 11–14% / yr. Medium risk profile.') }}",
            C: "{{ __('Grade C: Interest rate 15–18% / yr. High risk profile.') }}",
            D: "{{ __('Grade D: Interest rate 19–24% / yr. Very high risk profile.') }}",
        };

        function formatRupiah(num) {
            return convertToArabicNumerals('Rp ' + Math.floor(num).toLocaleString('id-ID'));
        }

        amountRange.addEventListener('input', () => {
            amountInput.value = amountRange.value;
            amountDisplay.textContent = formatRupiah(amountRange.value);
        });

        amountInput.addEventListener('input', () => {
            amountRange.value = amountInput.value;
            amountDisplay.textContent = formatRupiah(amountInput.value);
        });

        document.querySelectorAll('.duration-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.duration-btn').forEach(b => {
                    b.className = 'duration-btn rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 py-2.5 text-xs font-bold transition-all';
                });
                btn.className = 'duration-btn rounded-xl border border-emerald-700 bg-emerald-700 text-white shadow-xs py-2.5 text-xs font-bold transition-all';
                durationInput.value = btn.dataset.duration;
            });
        });

        document.querySelectorAll('.grade-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const g = btn.dataset.grade;
                gradeInput.value = g;
                gradeDesc.textContent = gradeDescriptions[g];

                document.querySelectorAll('.grade-btn').forEach(b => {
                    b.className = 'grade-btn rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 py-2.5 text-xs font-bold transition-all';
                });
                btn.className = 'grade-btn rounded-xl border border-emerald-700 bg-emerald-700 text-white shadow-xs py-2.5 text-xs font-bold transition-all';
            });
        });

        document.getElementById('calculatorForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btnText = document.getElementById('btnText');
            btnText.textContent = "{{ __('Calculating...') }}";

            try {
                const response = await fetch('{{ route("calculator.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        amount:     amountInput.value,
                        duration:   durationInput.value,
                        risk_grade: gradeInput.value,
                    }),
                });

                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Terjadi kesalahan');

                document.getElementById('res_monthly').textContent   = convertToArabicNumerals(data.monthly_payment);
                document.getElementById('res_total').textContent     = convertToArabicNumerals(data.total_payment);
                document.getElementById('res_interest').textContent  = convertToArabicNumerals(data.total_interest);
                document.getElementById('res_fee').textContent       = convertToArabicNumerals(data.origination_fee || data.provision_fee);
                document.getElementById('res_duration_badge').textContent = convertToArabicNumerals(durationInput.value) + " {{ __('Months') }}";

                const tbody = document.getElementById('scheduleBody');
                tbody.innerHTML = '';
                data.schedule.forEach((row) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50/80 transition-colors';
                    tr.innerHTML = `
                        <td class="py-3 px-4 font-bold text-slate-900">${convertToArabicNumerals(row.month)}</td>
                        <td class="py-3 px-4 text-right font-extrabold text-slate-900">${convertToArabicNumerals(row.payment || row.installment)}</td>
                        <td class="py-3 px-4 text-right font-semibold text-slate-700">${convertToArabicNumerals(row.principal)}</td>
                        <td class="py-3 px-4 text-right font-semibold text-emerald-700">${convertToArabicNumerals(row.interest)}</td>
                        <td class="py-3 px-4 text-right font-semibold text-slate-400">${convertToArabicNumerals(row.remaining)}</td>
                    `;
                    tbody.appendChild(tr);
                });

            } catch (err) {
                console.error(err);
            } finally {
                btnText.textContent = '{{ __('Calculate Loan') }} →';
            }
        });

        // Trigger initial calculation
        document.getElementById('calculatorForm').dispatchEvent(new Event('submit'));
    });
    </script>
    @endsection
@else
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Kalkulator Pinjaman | LendFlow</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/persegi-nobg.png') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            emerald: { 700: '#15803D', 800: '#166534', 900: '#14532D' }
                        },
                        fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                    }
                }
            }
        </script>
    </head>
    <body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between">

        <!-- Standalone Top Header Navigation (For Public Visitors) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="h-8 w-8 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-base shadow-xs">L</span>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight">LendFlow</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-600">
                    <a href="{{ route('home') }}#borrowers" class="hover:text-emerald-700 uppercase tracking-wider">Borrowers</a>
                    <a href="{{ route('home') }}#lenders" class="hover:text-emerald-700 uppercase tracking-wider">Lenders</a>
                    <a href="{{ route('home') }}#features" class="hover:text-emerald-700 uppercase tracking-wider">Features</a>
                    <a href="{{ route('calculator.index') }}" class="text-emerald-700 uppercase tracking-wider border-b-2 border-emerald-700 pb-1">Calculator</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3 py-2">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Create Account &rarr;
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            
            <!-- Header Banner -->
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    Real-time Loan Simulation
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Kalkulator Pinjaman Institusional</h1>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Simulasikan estimasi cicilan bulanan, total bunga, dan rincian jadwal amortisasi secara real-time.
                </p>
            </div>

            <!-- 2-Column Calculator Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Input Form (Spans 5 Cols) -->
                <div class="lg:col-span-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Parameter Pinjaman</h3>

                    <form id="calculatorFormPublic" class="space-y-5">
                        @csrf

                        <!-- Amount Range & Number Input -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jumlah Pinjaman</label>
                                <span id="amountDisplayPublic" class="text-sm font-extrabold text-emerald-700">Rp 10.000.000</span>
                            </div>
                            <input type="range" id="amountRangePublic" min="{{ $minAmount }}" max="{{ $maxAmount }}" step="500000" value="10000000"
                                   class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-emerald-700 bg-slate-200">
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold mt-1">
                                <span>Rp {{ number_format($minAmount, 0, ',', '.') }}</span>
                                <span>Rp {{ number_format($maxAmount, 0, ',', '.') }}</span>
                            </div>
                            <input type="number" id="amountInputPublic" value="10000000" min="{{ $minAmount }}" max="{{ $maxAmount }}"
                                   class="mt-2 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                        </div>

                        <!-- Duration Selection -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tenor (Bulan)</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach([3, 6, 12, 24] as $month)
                                <button type="button" data-duration="{{ $month }}"
                                        class="duration-btn-public rounded-xl border py-2.5 text-xs font-bold transition-all
                                            {{ $month === 12 ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                                    {{ $month }} Bln
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="durationInputPublic" value="12">
                        </div>

                        <!-- Risk Grade Selection -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Credit Risk Grade</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['A', 'B', 'C', 'D'] as $grade)
                                <button type="button" data-grade="{{ $grade }}"
                                        class="grade-btn-public rounded-xl border py-2.5 text-xs font-bold transition-all
                                            {{ $grade === 'A' ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                                    Grade {{ $grade }}
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="gradeInputPublic" value="A">
                            <p id="gradeDescriptionPublic" class="mt-2 text-[11px] text-slate-500 font-medium">
                                Grade A: Suku bunga 8–10% / thn. Profil risiko sangat rendah.
                            </p>
                        </div>

                        <!-- Calculate Button -->
                        <button type="submit" id="calculateBtnPublic"
                                class="w-full py-3 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                            <span id="btnTextPublic">Hitung Simpul Pinjaman &rarr;</span>
                        </button>
                    </form>
                </div>

                <!-- Right Results Panel (Spans 7 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <div id="resultsPanelPublic" class="space-y-6">
                        <!-- Summary Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="rounded-2xl bg-emerald-700 text-white p-4 shadow-xs">
                                <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider block">CICILAN / BULAN</span>
                                <span id="res_monthly_public" class="text-lg font-black text-white mt-1 block truncate">Rp 916.667</span>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL BAYAR</span>
                                <span id="res_total_public" class="text-base font-extrabold text-slate-900 mt-1 block truncate">Rp 11.000.000</span>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL BUNGA</span>
                                <span id="res_interest_public" class="text-base font-extrabold text-emerald-700 mt-1 block truncate">Rp 1.000.000</span>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PROVISI (1%)</span>
                                <span id="res_fee_public" class="text-base font-extrabold text-slate-900 mt-1 block truncate">Rp 100.000</span>
                            </div>
                        </div>

                        <!-- Amortization Schedule Table Card -->
                        <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Jadwal Amortisasi Angsuran</h3>
                                <span id="res_duration_badge_public" class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">12 Bulan</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="py-3 px-4">BLN</th>
                                            <th class="py-3 px-4 text-right">TOTAL ANGSURAN</th>
                                            <th class="py-3 px-4 text-right">POKOK</th>
                                            <th class="py-3 px-4 text-right">BUNGA</th>
                                            <th class="py-3 px-4 text-right">SISA POKOK</th>
                                        </tr>
                                    </thead>
                                    <tbody id="scheduleBodyPublic" class="divide-y divide-slate-100 font-medium text-slate-700">
                                        {{-- Rendered dynamically via JavaScript --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs font-medium text-slate-500">
                <p>&copy; {{ date('Y') }} LendFlow Financial Systems. All rights reserved. Institutional Grade P2P Lending.</p>
            </div>
        </footer>

        <!-- JavaScript Calculation Engine -->
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const amountRange  = document.getElementById('amountRangePublic');
            const amountInput  = document.getElementById('amountInputPublic');
            const amountDisplay = document.getElementById('amountDisplayPublic');
            const durationInput = document.getElementById('durationInputPublic');
            const gradeInput    = document.getElementById('gradeInputPublic');
            const gradeDesc     = document.getElementById('gradeDescriptionPublic');

            const gradeDescriptions = {
                A: 'Grade A: Suku bunga 8–10% / thn. Profil risiko sangat rendah.',
                B: 'Grade B: Suku bunga 11–14% / thn. Profil risiko sedang.',
                C: 'Grade C: Suku bunga 15–18% / thn. Profil risiko tinggi.',
                D: 'Grade D: Suku bunga 19–24% / thn. Profil risiko sangat tinggi.',
            };

            function formatRupiah(num) {
                return 'Rp ' + Math.floor(num).toLocaleString('id-ID');
            }

            amountRange.addEventListener('input', () => {
                amountInput.value = amountRange.value;
                amountDisplay.textContent = formatRupiah(amountRange.value);
            });

            amountInput.addEventListener('input', () => {
                amountRange.value = amountInput.value;
                amountDisplay.textContent = formatRupiah(amountInput.value);
            });

            document.querySelectorAll('.duration-btn-public').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.duration-btn-public').forEach(b => {
                        b.className = 'duration-btn-public rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 py-2.5 text-xs font-bold transition-all';
                    });
                    btn.className = 'duration-btn-public rounded-xl border border-emerald-700 bg-emerald-700 text-white shadow-xs py-2.5 text-xs font-bold transition-all';
                    durationInput.value = btn.dataset.duration;
                });
            });

            document.querySelectorAll('.grade-btn-public').forEach(btn => {
                btn.addEventListener('click', () => {
                    const g = btn.dataset.grade;
                    gradeInput.value = g;
                    gradeDesc.textContent = gradeDescriptions[g];

                    document.querySelectorAll('.grade-btn-public').forEach(b => {
                        b.className = 'grade-btn-public rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 py-2.5 text-xs font-bold transition-all';
                    });
                    btn.className = 'grade-btn-public rounded-xl border border-emerald-700 bg-emerald-700 text-white shadow-xs py-2.5 text-xs font-bold transition-all';
                });
            });

            document.getElementById('calculatorFormPublic').addEventListener('submit', async (e) => {
                e.preventDefault();

                const btnText = document.getElementById('btnTextPublic');
                btnText.textContent = 'Menghitung...';

                try {
                    const response = await fetch('{{ route("calculator.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            amount:     amountInput.value,
                            duration:   durationInput.value,
                            risk_grade: gradeInput.value,
                        }),
                    });

                    const data = await response.json();
                    if (!data.success) throw new Error(data.error || 'Terjadi kesalahan');

                    document.getElementById('res_monthly_public').textContent   = data.monthly_payment;
                    document.getElementById('res_total_public').textContent     = data.total_payment;
                    document.getElementById('res_interest_public').textContent  = data.total_interest;
                    document.getElementById('res_fee_public').textContent       = data.origination_fee;
                    document.getElementById('res_duration_badge_public').textContent = data.duration + ' Bulan';

                    const tbody = document.getElementById('scheduleBodyPublic');
                    tbody.innerHTML = '';
                    data.schedule.forEach((row) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-50/80 transition-colors';
                        tr.innerHTML = `
                            <td class="py-3 px-4 font-bold text-slate-900">${row.month}</td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">${row.payment}</td>
                            <td class="py-3 px-4 text-right text-slate-700">${row.principal}</td>
                            <td class="py-3 px-4 text-right text-slate-600">${row.interest}</td>
                            <td class="py-3 px-4 text-right text-slate-500 font-mono">${row.remaining}</td>
                        `;
                        tbody.appendChild(tr);
                    });

                } catch (err) {
                    console.error(err);
                } finally {
                    btnText.textContent = 'Hitung Simpul Pinjaman →';
                }
            });

            // Trigger initial calculation
            document.getElementById('calculatorFormPublic').dispatchEvent(new Event('submit'));
        });
        </script>
    </body>
    </html>
@endif
