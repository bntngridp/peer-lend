@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-2xl">
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Header & Back Button -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                &larr; Kembali ke Pendaftaran / Back to Register
            </a>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kebijakan Privasi</span>
        </div>

        <div class="mb-6">
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Privacy Policy</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">LendFlow Data Protection &amp; Privacy Standard</p>
        </div>

        <!-- Privacy Policy Content -->
        <div class="prose prose-slate prose-xs max-w-none text-xs text-slate-600 space-y-4 leading-relaxed font-normal">
            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">1. Pengumpulan Informasi Pribadi</h3>
                <p>LendFlow mengumpulkan informasi pribadi seperti Nama Lengkap, Alamat Email, Nomor Telepon, Dokumen Identitas (KTP/NPWP), dan data transaksi keuangan semata-mata untuk keperluan verifikasi akun, penilaian kelayakan pinjaman (credit scoring), dan keamanan layanan.</p>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">2. Keamanan &amp; Enkripsi Data</h3>
                <p>Seluruh data sensitif dan kata sandi Anda dilindungi menggunakan enkripsi standar industri (AES-256 dan bcrypt). Kami menerapkan protokol akses ketat dan arsitektur dua faktor (2FA) untuk mencegah akses tanpa izin.</p>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">3. Pengungkapan Kepada Pihak Ketiga</h3>
                <p>LendFlow tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga mana pun. Data hanya dibagikan kepada mitra penyedia jasa keuangan resmi (seperti Payment Gateway Midtrans) atau atas instruksi penegak hukum yang sah.</p>
            </section>

            <section>
                <h3 class="text-sm font-bold text-slate-900 mb-1">4. Hak Pengguna &amp; Penghapusan Akun</h3>
                <p>Pengguna berhak memperbarui data profil dan mengajukan permohonan penutupan akun sesuai dengan kebijakan retensi data yang berlaku.</p>
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
