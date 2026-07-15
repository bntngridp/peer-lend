@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md px-4 py-20 sm:px-6 lg:px-8">
    
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-8">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Two-Factor Authentication</h2>
            <p class="text-xs text-gray-500 mt-2">Your account is secured with 2FA. Open your Google Authenticator app and input the code.</p>
        </div>

        <form action="{{ route('2fa.verify.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500 text-center mb-2">6-Digit OTP Code</label>
                <input type="text" name="code" id="code" required maxlength="6" autofocus
                       class="block w-full text-center tracking-[0.5em] font-mono text-lg rounded-xl border-gray-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                       placeholder="000000">
                
                @error('code')
                    <p class="mt-2 text-xs text-red-600 font-medium text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-colors">
                Confirm Code
            </button>
        </form>

    </div>

</div>
@endsection
