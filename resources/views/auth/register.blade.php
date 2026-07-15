@extends('layouts.app')

@section('content')
<div class="flex min-h-[85vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50/50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-gray-900">Create a new account</h2>
        <p class="mt-2 text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                Sign in instead
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg">
        <div class="bg-white px-6 py-8 shadow-xl shadow-gray-200/50 rounded-2xl border border-gray-100 sm:px-10">
            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Role Selection Toggle Cards -->
                <div x-data="{ selectedRole: '{{ old('role', 'borrower') }}' }">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">I want to register as a:</label>
                    <input type="hidden" name="role" :value="selectedRole">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Borrower Card -->
                        <div @click="selectedRole = 'borrower'"
                             :class="selectedRole === 'borrower' ? 'border-indigo-600 bg-indigo-50/30 ring-2 ring-indigo-600/20' : 'border-gray-200 hover:border-gray-300'"
                             class="flex flex-col items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 select-none text-center">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 mb-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Borrower</span>
                            <span class="text-[11px] text-gray-500 mt-1">I want to apply for a loan</span>
                        </div>

                        <!-- Lender Card -->
                        <div @click="selectedRole = 'lender'"
                             :class="selectedRole === 'lender' ? 'border-indigo-600 bg-indigo-50/30 ring-2 ring-indigo-600/20' : 'border-gray-200 hover:border-gray-300'"
                             class="flex flex-col items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 select-none text-center">
                            <div class="h-10 w-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 mb-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h.007v.008H3.75V4.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 9h.007v.008H3.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 3.75h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM7.5 12c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-3.75C8.004 18.375 7.5 17.871 7.5 17.25V12zm9.75-3c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v10.125c0 .621-.504 1.125-1.125 1.125h-2.25c-.621 0-1.125-.504-1.125-1.125V9z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Lender (Investor)</span>
                            <span class="text-[11px] text-gray-500 mt-1">I want to fund loans</span>
                        </div>
                    </div>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Full Name</label>
                    <div class="mt-1.5">
                        <input id="full_name" name="full_name" type="text" required value="{{ old('full_name') }}"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('full_name') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="John Doe">
                    </div>
                    @error('full_name')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Email Address</label>
                    <div class="mt-1.5">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Phone Number</label>
                    <div class="mt-1.5">
                        <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                               class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="081234567890">
                    </div>
                    @error('phone')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password and Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Password</label>
                        <div class="mt-1.5">
                            <input id="password" name="password" type="password" required
                                   class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                                   placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Confirm Password</label>
                        <div class="mt-1.5">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
