@extends('layouts.app')

@section('content')
<div class="flex min-h-[80vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50/50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-gray-900">Reset your password</h2>
        <p class="mt-2 text-sm text-gray-600">
            Remembered your credentials?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                Sign in instead
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white px-6 py-8 shadow-xl shadow-gray-200/50 rounded-2xl border border-gray-100 sm:px-10">
            <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Email Address</label>
                    <p class="text-[11px] text-gray-500 mb-2">We will email you a link to reset your password.</p>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        Send reset link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
