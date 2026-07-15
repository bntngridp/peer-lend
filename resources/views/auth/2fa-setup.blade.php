@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md px-4 py-16 sm:px-6 lg:px-8">
    
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-8">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Setup Two-Factor Authentication</h2>
            <p class="text-xs text-gray-500 mt-2">Enhance your peer-lend wallet security by activating Google Authenticator TOTP.</p>
        </div>

        <div class="space-y-6">
            
            <!-- Step 1: Scan QR Code -->
            <div class="border-b border-gray-100 pb-5 text-center">
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 mb-4">Step 1: Scan QR Code</span>
                <div class="flex justify-center bg-gray-50 p-4 rounded-xl border border-gray-100 mb-3">
                    <img src="{{ $qrUrl }}" alt="2FA QR Code" class="h-44 w-44">
                </div>
                <p class="text-xs text-gray-500 leading-relaxed px-4">Open Google Authenticator, Authy, or compatible app on your mobile device and scan this QR code.</p>
            </div>

            <!-- Step 2: Secret Key backup -->
            <div class="border-b border-gray-100 pb-5 text-center">
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 mb-2">Manual Key (Optional)</span>
                <div class="font-mono text-sm font-bold bg-gray-50 border border-gray-100 px-3 py-2 rounded-xl inline-block text-gray-700 select-all">
                    {{ $secret }}
                </div>
                <p class="text-[10px] text-gray-400 mt-1.5">If you cannot scan, manually add this secret key inside your auth app.</p>
            </div>

            <!-- Step 3: Verify & Submit -->
            <form action="{{ route('2fa.enable') }}" method="POST" class="space-y-4">
                @csrf
                <div class="text-center">
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 mb-3">Step 2: Enter Verification Code</span>
                    
                    <label for="code" class="sr-only">Authenticator Code</label>
                    <input type="text" name="code" id="code" required maxlength="6" autofocus
                           class="block w-full text-center tracking-[0.5em] font-mono text-lg rounded-xl border-gray-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                           placeholder="000000">
                    
                    @error('code')
                        <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-colors">
                    Activate 2FA
                </button>
            </form>

        </div>

    </div>

</div>
@endsection
