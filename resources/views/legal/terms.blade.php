@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-2xl">
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Header & Back Button -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                &larr; Kembali ke Pendaftaran / Back to Register
            </a>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Syarat &amp; Ketentuan</span>
        </div>

        <div class="mb-6">
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Terms &amp; Conditions</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">LendFlow Peer-to-Peer Lending Platform Agreement</p>
        </div>

        <!-- Terms Content -->
        <div class="prose prose-slate prose-xs max-w-none text-xs text-slate-600 space-y-4 leading-relaxed font-normal">
            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">1. Pendahuluan &amp; Ketentuan Umum</h3>
                <p>Selamat datang di LendFlow. Dengan mendaftar dan menggunakan platform Peer-to-Peer (P2P) Lending LendFlow, Anda menyatakan menyetujui seluruh Syarat dan Ketentuan ini. Harap membaca dokumen ini dengan saksama sebelum melanjutkan proses pendaftaran.</p>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">2. Peran Pengguna (Borrower &amp; Lender)</h3>
                <ul class="list-disc pl-4 space-y-1">
                    <li><strong>Borrower (Peminjam):</strong> Peminjam wajib memberikan data KYC (Kartu Tanda Penduduk, NPWP, Bukti Pendapatan) yang asli dan valid. Peminjam bertanggung jawab penuh atas pembayaran pokok dan bunga cicilan sesuai jadwal penagihan.</li>
                    <li><strong>Lender (Pemberi Pinjaman):</strong> Pemberi pinjaman menyadari bahwa aktivitas pendanaan memiliki risiko bisnis. LendFlow memfasilitasi penilaian kredit (credit scoring) dan dana jaminan/collateral untuk meminimalkan risiko pendana.</li>
                </ul>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">3. Verifikasi Identitas (KYC &amp; AML)</h3>
                <p>Seluruh pengguna wajib melalui prosedur Know Your Customer (KYC) dan Anti-Money Laundering (AML) sesuai regulasi yang berlaku. Penggunaan akun oleh pihak ketiga tidak diizinkan.</p>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">4. Pembayaran &amp; Dompet Digital</h3>
                <p>Seluruh transaksi keuangan diproses melalui Virtual Account dan dompet digital IDR terenkripsi. Pengguna dilarang melakukan aktivitas pencucian uang atau transaksi manipulatif.</p>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">5. Perubahan &amp; Ketentuan Hukum</h3>
                <p>LendFlow berhak memperbarui Syarat &amp; Ketentuan ini sewaktu-waktu. Perubahan akan diberitahukan melalui email atau pengumuman di aplikasi.</p>
            </section>
        </div>

        <!-- Bottom Action Button -->
        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('register') }}"
               class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-all shadow-xs inline-flex items-center gap-1.5">
                &larr; Kembali ke Pendaftaran
            </a>
            <span class="text-[11px] text-slate-400 font-medium">Terakhir diperbarui: Juli 2026</span>
        </div>

    </div>
</div>
@endsection
