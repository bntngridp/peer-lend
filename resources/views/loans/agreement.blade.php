<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjanjian Kontrak Pinjaman - Peer-Lend</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Cinzel', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white;
                color: black;
                font-size: 12px;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased py-10 px-4">

    <!-- ── Action Bar (Floating No-Print) ────────────────────────────────────── -->
    <div class="mx-auto max-w-4xl no-print mb-6 flex justify-between items-center bg-white rounded-2xl shadow p-4 border border-gray-200">
        <div>
            <h1 class="text-sm font-semibold text-gray-900">Perjanjian Kontrak Pinjaman</h1>
            <p class="text-xs text-gray-500">ID Dokumen: #{{ $loan->id }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.history.back();" class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                ← Kembali
            </button>
            <button onclick="window.print();" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 shadow shadow-indigo-600/10 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096a42.414 42.414 0 00-10.56 0m10.56 0L17.66 18m0 0a2.25 2.25 0 01-2.24 2.156H8.58A2.25 2.25 0 016.34 18m11.32 0h.008v.008h-.008V18zm-.008-6h.008v.008h-.008V12zm-9 6h.008v.008H8.58V18zm0-6h.008v.008H8.58V12z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- ── Document Page ─────────────────────────────────────────────────────── -->
    <div class="mx-auto max-w-4xl bg-white shadow-2xl rounded-2xl p-10 md:p-16 border border-gray-200/50 print:border-none print:shadow-none print:rounded-none relative overflow-hidden">
        
        <!-- Watermark / Seal Decorator -->
        <div class="absolute top-10 right-10 h-28 w-28 opacity-10 flex items-center justify-center border-4 border-indigo-700 rounded-full font-serif font-bold text-indigo-700 text-center uppercase tracking-widest text-xs rotate-12">
            Peer-Lend<br>OFFICIAL
        </div>

        <!-- Document Header (Kop Surat) -->
        <div class="text-center border-b-2 border-double border-gray-300 pb-6 mb-8">
            <h2 class="font-serif text-3xl font-bold tracking-wider text-gray-900 uppercase">Perjanjian Pinjam Meminjam Uang</h2>
            <p class="text-xs tracking-widest text-indigo-600 font-semibold uppercase mt-1">Platform Peer-to-Peer Lending Peer-Lend</p>
            <p class="text-xs text-gray-400 mt-0.5">Surat Perjanjian Elektronik berlandaskan Hukum Perdata Republik Indonesia</p>
        </div>

        <p class="text-justify text-sm leading-relaxed mb-6">
            Pada hari ini, tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini menyatakan sepakat untuk mengikatkan diri dalam Perjanjian Pinjam Meminjam Uang berbasis teknologi informasi (P2P Lending) melalui platform **Peer-Lend**, dengan syarat-syarat dan ketentuan sebagai berikut:
        </p>

        <!-- Pihak Terlibat -->
        <div class="space-y-6 mb-8 text-sm">
            <div class="border-l-4 border-indigo-500 pl-4 py-1">
                <h3 class="font-bold text-gray-900 uppercase tracking-wide text-xs">Pihak Pertama (Penerima Pinjaman / Borrower)</h3>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <span class="text-gray-500">Nama Lengkap</span>
                    <span class="col-span-2 font-semibold text-gray-800">: {{ $loan->borrower->profile->full_name ?? '—' }}</span>
                    <span class="text-gray-500">Email Terdaftar</span>
                    <span class="col-span-2 text-gray-800">: {{ $loan->borrower->email }}</span>
                    <span class="text-gray-500">No. Handphone</span>
                    <span class="col-span-2 text-gray-800">: {{ $loan->borrower->profile->phone ?? '—' }}</span>
                </div>
            </div>

            <div class="border-l-4 border-purple-500 pl-4 py-1">
                <h3 class="font-bold text-gray-900 uppercase tracking-wide text-xs">Pihak Kedua (Para Pemberi Pinjaman / Lenders)</h3>
                <div class="mt-2 space-y-2">
                    @forelse($funders as $fund)
                        <div class="grid grid-cols-3 gap-2 py-1 border-b border-gray-50 last:border-0">
                            <span class="text-gray-500">{{ $fund->lender->profile->full_name ?? $fund->lender->email }}</span>
                            <span class="col-span-2 font-semibold text-gray-800 text-right">Rp {{ number_format($fund->amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Belum ada pendana terdaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Rincian Pinjaman -->
        <div class="bg-gray-50 rounded-2xl border border-gray-200/60 p-6 mb-8 text-sm">
            <h3 class="font-bold text-gray-900 uppercase tracking-wide text-xs mb-4">Rincian Pokok & Ketentuan Finansial</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-xs text-gray-400 block uppercase font-medium">Pokok Pinjaman</span>
                    <span class="font-bold text-lg text-indigo-600">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block uppercase font-medium">Bunga Tahunan (APR)</span>
                    <span class="font-bold text-lg text-gray-800">{{ $loan->interest_rate }}% / Tahun</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block uppercase font-medium">Tenor Pinjaman</span>
                    <span class="font-bold text-sm text-gray-800">{{ $loan->duration }} Bulan</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block uppercase font-medium">Status Kontrak</span>
                    <span class="font-bold text-xs uppercase rounded bg-indigo-100 text-indigo-700 px-2 py-0.5 inline-block mt-0.5">{{ $loan->status }}</span>
                </div>
                <div class="col-span-2 border-t border-gray-100 pt-3">
                    <span class="text-xs text-gray-400 block uppercase font-medium">Tujuan Pinjaman (Loan Purpose)</span>
                    <span class="font-semibold text-sm text-gray-800">{{ $loan->purpose }}</span>
                </div>
                
                @if($loan->collateral_currency_id)
                <div class="col-span-2 border-t border-gray-200 pt-3">
                    <span class="text-xs text-gray-400 block uppercase font-medium">Kolateral Crypto Aset</span>
                    <span class="font-semibold text-sm text-gray-800">
                        {{ number_format($loan->collateral_amount, 8) }} {{ $loan->collateralCurrency->code }}
                    </span>
                    <span class="text-xs text-gray-500 block mt-0.5 italic">
                        (Ekivalen LTV Awal 50%, Harga Likuidasi Rp {{ number_format($loan->liquidation_price, 0, ',', '.') }})
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Jadwal Amortisasi -->
        <div class="mb-8">
            <h3 class="font-bold text-gray-900 uppercase tracking-wide text-xs mb-4">Jadwal Angsuran Bulanan</h3>
            <table class="w-full text-xs text-left border border-gray-150 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="py-2 px-3 font-semibold text-center">Bulan Ke-</th>
                        <th class="py-2 px-3 font-semibold text-right">Pokok Angsuran</th>
                        <th class="py-2 px-3 font-semibold text-right">Bunga Angsuran</th>
                        <th class="py-2 px-3 font-semibold text-right">Total Tagihan</th>
                        <th class="py-2 px-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // Flat Calculation mid-point simulation for UI if installments not yet fully generated in test
                        $duration = $loan->duration;
                        $monthlyPrincipal = bcdiv((string)$loan->amount, (string)$duration, 2);
                        $monthlyInterest = bcdiv(bcmul((string)$loan->amount, bcdiv((string)$loan->interest_rate, '1200', 6), 4), '1', 2);
                        $monthlyTotal = bcadd($monthlyPrincipal, $monthlyInterest, 2);
                    @endphp
                    @for($i = 1; $i <= $duration; $i++)
                    <tr>
                        <td class="py-2 px-3 text-center">{{ $i }}</td>
                        <td class="py-2 px-3 text-right text-gray-600">Rp {{ number_format((float)$monthlyPrincipal, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right text-gray-600">Rp {{ number_format((float)$monthlyInterest, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right font-semibold text-indigo-600">Rp {{ number_format((float)$monthlyTotal, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-center">
                            <span class="inline-block text-[10px] uppercase font-bold text-gray-400">UNPAID</span>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="page-break"></div>

        <!-- Ketentuan Legal & Ketentuan Umum -->
        <div class="space-y-4 text-xs text-justify leading-relaxed mb-12 border-t border-gray-200 pt-6">
            <h3 class="font-bold text-gray-900 uppercase tracking-wide text-center mb-2">Syarat & Ketentuan Perjanjian</h3>
            
            <p><strong>Pasal 1: Payout & Denda Keterlambatan</strong></p>
            <p>Penerima Pinjaman wajib melakukan pelunasan tagihan selambat-lambatnya pada tanggal jatuh tempo yang ditentukan. Keterlambatan pembayaran cicilan dikenakan denda harian sebesar <strong>0.1%</strong> dari nilai pokok cicilan tertunggak yang dihitung per hari sejak keterlambatan terjadi.</p>

            <p><strong>Pasal 2: Agunan Crypto & Margin Call</strong></p>
            <p>Jika pinjaman ini dijamin dengan agunan crypto, platform Peer-Lend berhak penuh untuk melakukan likuidasi otomatis terhadap kolateral tersebut tanpa memerlukan persetujuan tambahan jika rasio LTV (Loan-to-Value) saat ini menyentuh atau melampaui batas likuidasi <strong>80%</strong> akibat fluktuasi harga pasar.</p>

            <p><strong>Pasal 3: Legalitas Hukum</strong></p>
            <p>Perjanjian ini mengikat secara hukum sejak disetujui secara elektronik melalui integrasi tanda tangan digital platform. Segala sengketa hukum yang timbul akan diselesaikan secara arbitrase atau sesuai pengadilan domisili wilayah Republik Indonesia.</p>
        </div>

        <!-- Tanda Tangan Elektronik -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-10">
            <div>
                <p class="text-gray-400 uppercase tracking-wider mb-1">Pihak Pertama (Borrower)</p>
                <div class="h-20 flex items-center justify-center border border-dashed border-gray-200 rounded-xl bg-gray-50 my-2">
                    <span class="text-gray-400 font-medium italic select-none">TERTANDA SECARA ELEKTRONIK</span>
                </div>
                <p class="font-bold text-gray-800">{{ $loan->borrower->profile->full_name ?? '—' }}</p>
                <p class="text-gray-400">{{ $loan->borrower->email }}</p>
            </div>
            <div>
                <p class="text-gray-400 uppercase tracking-wider mb-1">Platform Fasilitator</p>
                <div class="h-20 flex items-center justify-center border border-dashed border-gray-200 rounded-xl bg-indigo-50/30 my-2 relative">
                    <span class="text-indigo-600/60 font-serif font-bold text-sm tracking-wider rotate-6">PEER-LEND SECURED</span>
                </div>
                <p class="font-bold text-gray-800">Admin Peer-Lend</p>
                <p class="text-gray-400">verification@peer-lend.com</p>
            </div>
        </div>

    </div>

</body>
</html>
