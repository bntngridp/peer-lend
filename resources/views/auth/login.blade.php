@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- LendFlow Card Container with Soft Light Gray Border -->
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-xs group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            <h2 class="text-base font-bold text-slate-900">Sign in to your institutional account</h2>
            <p class="text-xs font-medium text-slate-400 mt-0.5">Institutional Grade P2P Lending</p>
        </div>

        @if (session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form class="space-y-5" action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Institutional Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Institutional Email</label>
                <div class="relative">
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('email') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                           placeholder="user@institution.com">
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password with Flex Embedded Eye Toggle -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Forgot Password?
                    </a>
                </div>
                <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white overflow-hidden px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200 @error('password') border-rose-300 @enderror">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="w-full bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none p-0 focus:ring-0"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePass('password', this)" class="ml-2 text-slate-400 hover:text-emerald-700 focus:outline-none transition-colors shrink-0 p-1 rounded-lg hover:bg-slate-100" title="Toggle password visibility">
                        <!-- Eye Open -->
                        <svg class="eye-open w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Off -->
                        <svg class="eye-closed w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between py-1">
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input id="remember" name="remember" type="checkbox" class="sr-only peer">
                    <div class="w-4 h-4 rounded border border-slate-300 peer-checked:bg-emerald-700 peer-checked:border-emerald-700 flex items-center justify-center transition-all">
                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="ml-2 text-xs font-semibold text-slate-600">Remember this device for 30 days</span>
                </label>
            </div>

            <!-- Primary Submit Button -->
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs">
                Sign In &rarr;
            </button>

            <!-- Social Divider -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-3 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Or continue with</span>
                </div>
            </div>

            <!-- Sign in with Google Button -->
            <a href="{{ route('auth.google') }}"
               class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center justify-center gap-2.5 transition-all shadow-xs hover:border-slate-300">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Sign in with Google</span>
            </a>

            <!-- Footer link to Register -->
            <div class="text-center pt-2">
                <p class="text-xs text-slate-500 font-medium">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Create Account</a>
                </p>
            </div>
        </form>

        <!-- Security Restriction Notice Box -->
        <div class="mt-8 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500 leading-relaxed font-medium">
            <div class="flex items-start gap-2">
                <span>Access is restricted to authorized personnel only. All activities are monitored and logged. By signing in, you agree to LendFlow's 
                <button type="button" onclick="openLegalModal('terms')" class="font-bold text-slate-700 underline bg-transparent border-none p-0 inline cursor-pointer">Terms of Service</button> 
                and 
                <button type="button" onclick="openLegalModal('privacy')" class="font-bold text-slate-700 underline bg-transparent border-none p-0 inline cursor-pointer">Privacy Policy</button>.</span>
            </div>
        </div>

    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- MODAL OVERLAY: TERMS & CONDITIONS -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div id="modal-terms" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
            <div class="flex items-center gap-2">
                <span class="h-6 w-6 rounded-lg bg-emerald-700 text-white font-black flex items-center justify-center text-xs">L</span>
                <h3 class="text-sm font-bold text-slate-900">Terms &amp; Conditions — LendFlow</h3>
            </div>
            <button type="button" onclick="closeLegalModal('terms')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-200/60 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto max-h-[60vh] text-xs text-slate-600 space-y-4 leading-relaxed">
            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">1. Pendahuluan &amp; Ketentuan Umum</h4>
                <p>Selamat datang di LendFlow. Dengan mendaftar dan menggunakan platform Peer-to-Peer (P2P) Lending LendFlow, Anda menyatakan menyetujui seluruh Syarat dan Ketentuan ini. Harap membaca dokumen ini dengan saksama sebelum melanjutkan proses pendaftaran.</p>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">2. Peran Pengguna (Borrower &amp; Lender)</h4>
                <ul class="list-disc pl-4 space-y-1">
                    <li><strong>Borrower (Peminjam):</strong> Peminjam wajib memberikan data KYC (Kartu Tanda Penduduk, NPWP, Bukti Pendapatan) yang asli dan valid. Peminjam bertanggung jawab penuh atas pembayaran pokok dan bunga cicilan sesuai jadwal penagihan.</li>
                    <li><strong>Lender (Pemberi Pinjaman):</strong> Pemberi pinjaman menyadari bahwa aktivitas pendanaan memiliki risiko bisnis. LendFlow memfasilitasi penilaian kredit (credit scoring) dan dana jaminan/collateral untuk meminimalkan risiko pendana.</li>
                </ul>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">3. Verifikasi Identitas (KYC &amp; AML)</h4>
                <p>Seluruh pengguna wajib melalui prosedur Know Your Customer (KYC) dan Anti-Money Laundering (AML) sesuai regulasi yang berlaku. Penggunaan akun oleh pihak ketiga tidak diizinkan.</p>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">4. Pembayaran &amp; Dompet Digital</h4>
                <p>Seluruh transaksi keuangan diproses melalui Virtual Account dan dompet digital IDR terenkripsi. Pengguna dilarang melakukan aktivitas pencucian uang atau transaksi manipulatif.</p>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">5. Perubahan &amp; Ketentuan Hukum</h4>
                <p>LendFlow berhak memperbarui Syarat &amp; Ketentuan ini sewaktu-waktu. Perubahan akan diberitahukan melalui email atau pengumuman di aplikasi.</p>
            </section>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">Terakhir diperbarui: Juli 2026</span>
            <button type="button" onclick="closeLegalModal('terms')" class="px-4 py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-all shadow-xs">
                Tutup / Close
            </button>
        </div>

    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- MODAL OVERLAY: PRIVACY POLICY -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div id="modal-privacy" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
            <div class="flex items-center gap-2">
                <span class="h-6 w-6 rounded-lg bg-emerald-700 text-white font-black flex items-center justify-center text-xs">L</span>
                <h3 class="text-sm font-bold text-slate-900">Privacy Policy — LendFlow</h3>
            </div>
            <button type="button" onclick="closeLegalModal('privacy')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-200/60 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto max-h-[60vh] text-xs text-slate-600 space-y-4 leading-relaxed">
            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">1. Pengumpulan Informasi Pribadi</h4>
                <p>LendFlow mengumpulkan informasi pribadi seperti Nama Lengkap, Alamat Email, Nomor Telepon, Dokumen Identitas (KTP/NPWP), dan data transaksi keuangan semata-mata untuk keperluan verifikasi akun, penilaian kelayakan pinjaman (credit scoring), dan keamanan layanan.</p>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">2. Keamanan &amp; Enkripsi Data</h4>
                <p>Seluruh data sensitif dan kata sandi Anda dilindungi menggunakan enkripsi standar industri (AES-256 dan bcrypt). Kami menerapkan protokol akses ketat dan arsitektur dua faktor (2FA) untuk mencegah akses tanpa izin.</p>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">3. Pengungkapan Kepada Pihak Ketiga</h4>
                <p>LendFlow tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga mana pun. Data hanya dibagikan kepada mitra penyedia jasa keuangan resmi (seperti Payment Gateway Midtrans) atau atas instruksi penegak hukum yang sah.</p>
            </section>

            <section>
                <h4 class="text-xs font-bold text-slate-900 mb-1">4. Hak Pengguna &amp; Penghapusan Akun</h4>
                <p>Pengguna berhak memperbarui data profil dan mengajukan permohonan penutupan akun sesuai dengan kebijakan retensi data yang berlaku.</p>
            </section>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">Terakhir diperbarui: Juli 2026</span>
            <button type="button" onclick="closeLegalModal('privacy')" class="px-4 py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-all shadow-xs">
                Tutup / Close
            </button>
        </div>

    </div>
</div>

<script>
function openLegalModal(type, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const modal = document.getElementById('modal-' + type);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    return false;
}

function closeLegalModal(type) {
    const modal = document.getElementById('modal-' + type);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('modal') === 'terms') {
        openLegalModal('terms');
    } else if (urlParams.get('modal') === 'privacy') {
        openLegalModal('privacy');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLegalModal('terms');
        closeLegalModal('privacy');
    }
});

function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    
    const eyeOpen = btn.querySelector('.eye-open');
    const eyeClosed = btn.querySelector('.eye-closed');
    if (eyeOpen && eyeClosed) {
        if (isPassword) {
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
}
</script>
@endsection
