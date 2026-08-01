@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 py-6" x-data="{ setupMethod: 'qr', copied: false }">
    
    <!-- Top Header Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Two-Factor Authentication (2FA) Setup</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Tingkatkan keamanan akun LendFlow Anda dengan mengaktifkan Authenticator App (Google Authenticator, Authy, atau Microsoft Authenticator).</p>
        </div>
        <a href="{{ route('profile.edit', ['tab' => 'security']) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Profil
        </a>
    </div>

    <!-- Main Card Container -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-8">
        
        <!-- Step 1: Choose Setup Method & Get Credentials -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center">1</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Langkah 1: Hubungkan Aplikasi Authenticator</h3>
                </div>
                
                <!-- Toggle Setup Method Tabs -->
                <div class="flex bg-slate-100 p-1 rounded-xl text-xs font-bold text-slate-600">
                    <button type="button" @click="setupMethod = 'qr'" 
                            :class="setupMethod === 'qr' ? 'bg-white text-emerald-800 shadow-xs rounded-lg px-3 py-1' : 'px-3 py-1 hover:text-slate-900'">
                        📷 Scan QR Code
                    </button>
                    <button type="button" @click="setupMethod = 'key'" 
                            :class="setupMethod === 'key' ? 'bg-white text-emerald-800 shadow-xs rounded-lg px-3 py-1' : 'px-3 py-1 hover:text-slate-900'">
                        🔑 Kode Kunci Manual
                    </button>
                </div>
            </div>

            <!-- Method 1: QR Code Scan -->
            <div x-show="setupMethod === 'qr'" class="flex flex-col sm:flex-row items-center gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs shrink-0">
                    <img src="{{ $qrUrl }}" alt="2FA QR Code" class="h-44 w-44 rounded-xl">
                </div>
                <div class="space-y-3 text-center sm:text-left">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800">
                        Metode Utama (Scan Kamera)
                    </span>
                    <h4 class="text-xs font-bold text-slate-900">Pindai QR Code Menggunakan HP Anda</h4>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        Buka aplikasi <strong>Google Authenticator</strong>, <strong>Authy</strong>, atau <strong>Microsoft Authenticator</strong> di HP Anda, lalu pilih <em>Add Account / Scan QR Code</em>.
                    </p>
                </div>
            </div>

            <!-- Method 2: Manual Secret Key Code -->
            <div x-show="setupMethod === 'key'" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4" style="display: none;">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800">
                            Metode Alternatif (Manual Key)
                        </span>
                        <h4 class="text-xs font-bold text-slate-900 mt-1">Masukkan Kode Kunci Secara Manual</h4>
                    </div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Jika kamera HP Anda tidak bisa memindai QR Code, buka aplikasi Authenticator, pilih <em>Enter Setup Key Manually</em>, lalu masukkan kode rahasia di bawah ini:
                </p>

                <!-- Copyable Secret Key Box -->
                <div class="flex items-center gap-3">
                    <div class="flex-1 font-mono text-sm font-black bg-white border border-slate-200 px-4 py-3 rounded-xl text-emerald-800 tracking-wider shadow-xs select-all">
                        {{ $secret }}
                    </div>
                    <button type="button" 
                            @click="
                                navigator.clipboard.writeText('{{ $secret }}');
                                copied = true;
                                setTimeout(() => copied = false, 2500);
                            "
                            class="px-4 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition-colors shadow-xs shrink-0 flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.375v-6.375a1.125 1.125 0 00-1.125-1.125H15.75"/></svg>
                        <span x-text="copied ? 'Tercopy! ✨' : 'Copy Key'">Copy Key</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Verification Code Input -->
        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center gap-2 mb-4">
                <span class="h-6 w-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center">2</span>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Langkah 2: Verifikasi Kode 6 Angka</h3>
            </div>

            <form action="{{ route('2fa.enable') }}" method="POST" class="space-y-5 max-w-md mx-auto text-center">
                @csrf

                <div>
                    <label for="code" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Kode OTP dari Aplikasi Authenticator</label>
                    <input type="text" name="code" id="code" required maxlength="6" autofocus
                           class="w-full text-center tracking-[0.6em] font-mono text-2xl font-bold rounded-2xl border {{ $errors->has('code') ? 'border-rose-500 bg-rose-50/50 text-rose-900' : 'border-slate-200 text-slate-900' }} px-4 py-3.5 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs"
                           placeholder="000000">
                    
                    @error('code')
                        <p class="mt-2 text-xs text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" id="btn_submit_2fa"
                        class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                    Aktifkan 2FA Sekarang &rarr;
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
