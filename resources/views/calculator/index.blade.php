@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">

    {{-- ── Hero Section ─────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-700 pt-16 pb-24">
        {{-- Background decorative blobs --}}
        <div class="absolute -top-10 -left-10 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-96 w-96 rounded-full bg-indigo-400/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 px-4 py-1.5 mb-6 text-sm font-medium text-white">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Simulasi Pinjaman Real-time
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight">
                Kalkulator Pinjaman
                <span class="block text-indigo-200 mt-1">Peer-Lend</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-indigo-100 text-lg leading-relaxed">
                Simulasikan cicilan bulanan, total pembayaran, dan jadwal amortisasi secara instan — sebelum mengajukan pinjaman.
            </p>

            {{-- Stat Pills --}}
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                @foreach(['A' => ['8–10%', 'Risiko Rendah', 'text-emerald-300'], 'B' => ['11–14%', 'Risiko Sedang', 'text-yellow-300'], 'C' => ['15–18%', 'Risiko Tinggi', 'text-orange-300'], 'D' => ['19–24%', 'Risiko Sangat Tinggi', 'text-red-300']] as $grade => $info)
                <div class="flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 px-4 py-2">
                    <span class="font-bold text-white text-sm">Grade {{ $grade }}</span>
                    <span class="text-white/40">·</span>
                    <span class="text-sm {{ $info[2] }}">{{ $info[0] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Main Calculator Section ──────────────────────────────────────────── --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 -mt-12 pb-20">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

            {{-- ── Left Panel: Input Form ────────────────────────────────────── --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-white shadow-xl shadow-indigo-900/5 border border-gray-100 overflow-hidden sticky top-24">
                    <div class="border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                        <h2 class="text-base font-semibold text-gray-900">Parameter Pinjaman</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Isi detail pinjaman di bawah ini</p>
                    </div>

                    <form id="calculatorForm" class="p-6 space-y-5">
                        @csrf

                        {{-- Loan Amount --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Jumlah Pinjaman
                                <span id="amountDisplay" class="ml-2 font-bold text-indigo-600">Rp 10.000.000</span>
                            </label>
                            <input
                                type="range"
                                id="amountRange"
                                min="{{ $minAmount }}"
                                max="{{ $maxAmount }}"
                                step="500000"
                                value="10000000"
                                class="w-full h-2 rounded-lg appearance-none cursor-pointer"
                                style="accent-color: #4f46e5;"
                            >
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>Rp {{ number_format($minAmount, 0, ',', '.') }}</span>
                                <span>Rp {{ number_format($maxAmount, 0, ',', '.') }}</span>
                            </div>
                            <input type="number" id="amountInput" value="10000000" min="{{ $minAmount }}" max="{{ $maxAmount }}"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                                placeholder="Masukkan jumlah...">
                        </div>

                        {{-- Duration --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tenor (Bulan)</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach([3, 6, 12, 24] as $month)
                                <button type="button"
                                    data-duration="{{ $month }}"
                                    class="duration-btn rounded-xl border-2 py-2.5 text-sm font-semibold transition-all
                                        {{ $month === 12 ? 'border-indigo-500 bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-300 hover:text-indigo-600' }}">
                                    {{ $month }}
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="durationInput" value="12">
                        </div>

                        {{-- Risk Grade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Risk Grade</label>
                            <div class="grid grid-cols-4 gap-2">
                                @php
                                    $gradeColors = [
                                        'A' => 'emerald', 'B' => 'yellow', 'C' => 'orange', 'D' => 'red'
                                    ];
                                @endphp
                                @foreach(['A', 'B', 'C', 'D'] as $grade)
                                @php $color = $gradeColors[$grade]; @endphp
                                <button type="button"
                                    data-grade="{{ $grade }}"
                                    class="grade-btn rounded-xl border-2 py-2.5 text-sm font-bold transition-all
                                        {{ $grade === 'A' ? "border-{$color}-500 bg-{$color}-600 text-white shadow-md" : "border-gray-200 bg-white text-gray-600 hover:border-{$color}-300 hover:text-{$color}-600" }}">
                                    {{ $grade }}
                                </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="gradeInput" value="A">
                            <p id="gradeDescription" class="mt-2 text-xs text-gray-500">
                                Grade A: Suku bunga 8–10% per tahun. Risiko rendah, profil kredit sangat baik.
                            </p>
                        </div>

                        {{-- Calculate Button --}}
                        <button type="submit" id="calculateBtn"
                            class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 hover:from-indigo-700 hover:to-purple-700 hover:shadow-indigo-600/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96" />
                            </svg>
                            <span id="btnText">Hitung Cicilan</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Right Panel: Results ─────────────────────────────────────── --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Placeholder (before calculation) --}}
                <div id="placeholder" class="rounded-2xl bg-white shadow-xl shadow-indigo-900/5 border border-gray-100 p-10 flex flex-col items-center justify-center text-center min-h-[300px]">
                    <div class="h-16 w-16 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Mulai Simulasi</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-xs">Pilih jumlah pinjaman, tenor, dan risk grade, lalu klik <strong>Hitung Cicilan</strong>.</p>
                </div>

                {{-- Results Panel (hidden initially) --}}
                <div id="resultsPanel" class="hidden space-y-6">

                    {{-- Summary Cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-4 text-white shadow-lg shadow-indigo-600/20">
                            <p class="text-xs font-medium text-indigo-200">Cicilan / Bulan</p>
                            <p id="res_monthly" class="mt-1 text-xl font-bold truncate">—</p>
                        </div>
                        <div class="rounded-2xl bg-white border border-gray-100 shadow-md p-4">
                            <p class="text-xs font-medium text-gray-500">Total Bayar</p>
                            <p id="res_total" class="mt-1 text-lg font-bold text-gray-900 truncate">—</p>
                        </div>
                        <div class="rounded-2xl bg-white border border-gray-100 shadow-md p-4">
                            <p class="text-xs font-medium text-gray-500">Total Bunga</p>
                            <p id="res_interest" class="mt-1 text-lg font-bold text-rose-600 truncate">—</p>
                        </div>
                        <div class="rounded-2xl bg-white border border-gray-100 shadow-md p-4">
                            <p class="text-xs font-medium text-gray-500">Biaya Awal (1%)</p>
                            <p id="res_fee" class="mt-1 text-lg font-bold text-amber-600 truncate">—</p>
                        </div>
                    </div>

                    {{-- Grade & Rate Info --}}
                    <div class="rounded-2xl bg-white border border-gray-100 shadow-md p-5 flex flex-wrap items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Risk Grade</p>
                            <span id="res_grade" class="mt-1 inline-flex items-center rounded-lg px-3 py-1 text-sm font-bold bg-emerald-100 text-emerald-700">A</span>
                        </div>
                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                        <div>
                            <p class="text-xs text-gray-500">Range Suku Bunga</p>
                            <p id="res_rate_range" class="mt-1 text-sm font-semibold text-gray-900">—</p>
                        </div>
                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                        <div>
                            <p class="text-xs text-gray-500">Suku Bunga Digunakan</p>
                            <p id="res_annual_rate" class="mt-1 text-sm font-semibold text-indigo-600">—</p>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                                Ajukan Sekarang →
                            </a>
                        </div>
                    </div>

                    {{-- Amortization Table --}}
                    <div class="rounded-2xl bg-white border border-gray-100 shadow-md overflow-hidden">
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Jadwal Amortisasi</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Rincian cicilan per bulan</p>
                            </div>
                            <span id="res_duration_badge" class="rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium px-3 py-1">— bulan</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50/30">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Bulan</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Cicilan</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pokok</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Bunga</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Sisa</th>
                                    </tr>
                                </thead>
                                <tbody id="scheduleBody" class="divide-y divide-gray-50">
                                    {{-- Filled dynamically --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- Error Panel --}}
                <div id="errorPanel" class="hidden rounded-2xl bg-red-50 border border-red-100 p-6 text-center">
                    <p class="text-sm text-red-600 font-medium" id="errorMsg">Terjadi kesalahan. Silakan coba lagi.</p>
                </div>

            </div>{{-- end right panel --}}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Grade descriptions ────────────────────────────────────────────────────
    const gradeDescriptions = {
        A: 'Grade A: Suku bunga 8–10% per tahun. Risiko rendah, profil kredit sangat baik.',
        B: 'Grade B: Suku bunga 11–14% per tahun. Risiko sedang, profil kredit baik.',
        C: 'Grade C: Suku bunga 15–18% per tahun. Risiko tinggi, profil kredit cukup.',
        D: 'Grade D: Suku bunga 19–24% per tahun. Risiko sangat tinggi, profil kredit perlu perbaikan.',
    };

    const gradeColors = {
        A: { bg: 'bg-emerald-100', text: 'text-emerald-700' },
        B: { bg: 'bg-yellow-100', text: 'text-yellow-700' },
        C: { bg: 'bg-orange-100', text: 'text-orange-700' },
        D: { bg: 'bg-red-100', text: 'text-red-700' },
    };

    const gradeBtnColors = {
        A: { active: 'border-emerald-500 bg-emerald-600 text-white shadow-md', inactive: 'border-gray-200 bg-white text-gray-600' },
        B: { active: 'border-yellow-500 bg-yellow-500 text-white shadow-md', inactive: 'border-gray-200 bg-white text-gray-600' },
        C: { active: 'border-orange-500 bg-orange-600 text-white shadow-md', inactive: 'border-gray-200 bg-white text-gray-600' },
        D: { active: 'border-red-500 bg-red-600 text-white shadow-md', inactive: 'border-gray-200 bg-white text-gray-600' },
    };

    // ── Elements ──────────────────────────────────────────────────────────────
    const amountRange  = document.getElementById('amountRange');
    const amountInput  = document.getElementById('amountInput');
    const amountDisplay = document.getElementById('amountDisplay');
    const durationInput = document.getElementById('durationInput');
    const gradeInput    = document.getElementById('gradeInput');
    const gradeDesc     = document.getElementById('gradeDescription');

    // ── Sync range <-> number input ───────────────────────────────────────────
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

    // ── Duration buttons ─────────────────────────────────────────────────────
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.duration-btn').forEach(b => {
                b.className = b.className
                    .replace('border-indigo-500 bg-indigo-600 text-white shadow-md shadow-indigo-600/20', '')
                    .trim();
                b.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
            });
            btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
            btn.classList.add('border-indigo-500', 'bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-600/20');
            durationInput.value = btn.dataset.duration;
        });
    });

    // ── Grade buttons ─────────────────────────────────────────────────────────
    document.querySelectorAll('.grade-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const g = btn.dataset.grade;
            gradeInput.value = g;
            gradeDesc.textContent = gradeDescriptions[g];

            document.querySelectorAll('.grade-btn').forEach(b => {
                const bg = b.dataset.grade;
                b.className = b.className
                    .replace(gradeBtnColors[bg].active, '')
                    .trim();
                b.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
            });
            btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
            const colors = gradeBtnColors[g].active.split(' ');
            btn.classList.add(...colors);
        });
    });

    // ── Form submit ───────────────────────────────────────────────────────────
    document.getElementById('calculatorForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = document.getElementById('calculateBtn');
        const btnText = document.getElementById('btnText');
        btnText.textContent = 'Menghitung…';
        btn.disabled = true;

        document.getElementById('errorPanel').classList.add('hidden');

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

            if (!data.success) {
                throw new Error(data.error || 'Terjadi kesalahan pada server.');
            }

            // ── Populate results ──────────────────────────────────────────────
            document.getElementById('placeholder').classList.add('hidden');
            document.getElementById('resultsPanel').classList.remove('hidden');

            document.getElementById('res_monthly').textContent   = data.monthly_payment;
            document.getElementById('res_total').textContent     = data.total_payment;
            document.getElementById('res_interest').textContent  = data.total_interest;
            document.getElementById('res_fee').textContent       = data.origination_fee;
            document.getElementById('res_rate_range').textContent = data.rate_range;
            document.getElementById('res_annual_rate').textContent = data.annual_rate + '% / tahun (midpoint)';
            document.getElementById('res_duration_badge').textContent = data.duration + ' bulan';

            // Grade badge
            const gradeEl = document.getElementById('res_grade');
            gradeEl.textContent = 'Grade ' + data.grade;
            const gc = gradeColors[data.grade];
            gradeEl.className = `mt-1 inline-flex items-center rounded-lg px-3 py-1 text-sm font-bold ${gc.bg} ${gc.text}`;

            // Amortization schedule
            const tbody = document.getElementById('scheduleBody');
            tbody.innerHTML = '';
            data.schedule.forEach((row, i) => {
                const tr = document.createElement('tr');
                tr.className = i % 2 === 0 ? 'hover:bg-gray-50 transition-colors' : 'bg-gray-50/40 hover:bg-gray-100/40 transition-colors';
                tr.innerHTML = `
                    <td class="py-3 px-4 font-medium text-gray-700">${row.month}</td>
                    <td class="py-3 px-4 text-right font-semibold text-indigo-600">${row.payment}</td>
                    <td class="py-3 px-4 text-right text-gray-700">${row.principal}</td>
                    <td class="py-3 px-4 text-right text-rose-500">${row.interest}</td>
                    <td class="py-3 px-4 text-right text-gray-500">${row.remaining}</td>
                `;
                tbody.appendChild(tr);
            });

            // Scroll to results
            document.getElementById('resultsPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });

        } catch (err) {
            document.getElementById('errorPanel').classList.remove('hidden');
            document.getElementById('errorMsg').textContent = err.message;
        } finally {
            btnText.textContent = 'Hitung Cicilan';
            btn.disabled = false;
        }
    });

    // ── Auto-calculate on load with defaults ──────────────────────────────────
    document.getElementById('calculatorForm').dispatchEvent(new Event('submit'));
});
</script>
@endsection
