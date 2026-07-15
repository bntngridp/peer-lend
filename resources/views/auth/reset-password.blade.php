@extends('layouts.app')

@section('content')
<div class="flex min-h-[80vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50/50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-gray-900">Set new password</h2>
        <p class="mt-2 text-sm text-gray-600">Please choose a strong password to secure your account.</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white px-6 py-8 shadow-xl shadow-gray-200/50 rounded-2xl border border-gray-100 sm:px-10">
            <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Email Address</label>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $email) }}"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">New Password</label>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Confirm New Password</label>
                    <div class="mt-1.5 relative rounded-xl shadow-sm">
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        Update password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
