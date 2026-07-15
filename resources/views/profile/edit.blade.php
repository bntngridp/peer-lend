@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-8">
        
        <!-- Left Title info -->
        <div class="md:col-span-1">
            <h3 class="text-xl font-bold tracking-tight text-gray-900">Personal Information</h3>
            <p class="mt-2 text-sm text-gray-500">Update your account profile, contact phone, and professional details.</p>
        </div>

        <!-- Form container -->
        <div class="mt-5 md:col-span-2 md:mt-0">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 bg-white overflow-hidden">
                    <div class="space-y-6 px-4 py-6 sm:p-8">
                        
                        <!-- Avatar Section -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-3">Profile Photo</label>
                            <div class="flex items-center gap-6">
                                @if($user->profile && $user->profile->avatar_path)
                                    <img class="h-16 w-16 rounded-2xl object-cover ring-2 ring-indigo-600/10"
                                         src="{{ asset('storage/' . $user->profile->avatar_path) }}"
                                         alt="Avatar">
                                @else
                                    <div class="h-16 w-16 rounded-2xl bg-indigo-100 text-indigo-700 font-bold text-2xl flex items-center justify-center border border-indigo-200">
                                        {{ strtoupper(substr($user->profile->full_name ?? $user->email, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                                    <label for="avatar" class="cursor-pointer rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        Change photo
                                    </label>
                                    <p class="mt-2 text-xs text-gray-400">JPG or PNG. Max 2MB.</p>
                                </div>
                            </div>
                            @error('avatar')
                                <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Basic details grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Full Name</label>
                                <input type="text" name="full_name" id="full_name" required
                                       value="{{ old('full_name', $user->profile->full_name ?? '') }}"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('full_name') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('full_name')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Phone Number</label>
                                <input type="text" name="phone" id="phone" required
                                       value="{{ old('phone', $user->profile->phone ?? '') }}"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                                       placeholder="e.g. 08123456789">
                                @error('phone')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Residential Address</label>
                            <textarea name="address" id="address" rows="3"
                                      class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $user->profile->address ?? '') }}</textarea>
                        </div>

                        <!-- Location details grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="city" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">City</label>
                                <input type="text" name="city" id="city"
                                       value="{{ old('city', $user->profile->city ?? '') }}"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="province" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Province</label>
                                <input type="text" name="province" id="province"
                                       value="{{ old('province', $user->profile->province ?? '') }}"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Income & Profession (Optional but recommended for risk assessment) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
                            <div>
                                <label for="occupation" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Occupation</label>
                                <input type="text" name="occupation" id="occupation"
                                       value="{{ old('occupation', $user->profile->occupation ?? '') }}"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g. Software Engineer, Trader">
                            </div>

                            <div>
                                <label for="monthly_income" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Monthly Income (IDR)</label>
                                <input type="number" name="monthly_income" id="monthly_income"
                                       value="{{ old('monthly_income', $user->profile->monthly_income ?? '') }}"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g. 15000000">
                                @error('monthly_income')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-8 flex justify-end">
                        <button type="submit"
                                class="inline-flex justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:scale-[1.01] active:scale-[0.99] transition-all">
                            Save changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
