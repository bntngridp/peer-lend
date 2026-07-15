<div class="space-y-6">
    
    <!-- KTP File Upload -->
    <div>
        <label for="ktp" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">ID Card (KTP) Scan / Photo</label>
        <p class="text-[11px] text-gray-500 mb-2">Upload a clear photo or scan of your KTP card. All details must be readable.</p>
        <div class="mt-1 flex justify-center rounded-xl border border-dashed border-gray-300 px-6 py-6 hover:border-indigo-500 transition-colors">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600 justify-center">
                    <label for="ktp" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                        <span>Upload a file</span>
                        <input id="ktp" name="ktp" type="file" class="sr-only" required accept="image/*,application/pdf">
                    </label>
                </div>
                <p class="text-xs text-gray-400">PNG, JPG, PDF up to 5MB</p>
            </div>
        </div>
        @error('ktp')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Selfie File Upload -->
    <div>
        <label for="selfie" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Selfie Holding KTP</label>
        <p class="text-[11px] text-gray-500 mb-2">Take a photo holding your KTP card close to your face. Ensure your face is not obscured.</p>
        <div class="mt-1 flex justify-center rounded-xl border border-dashed border-gray-300 px-6 py-6 hover:border-indigo-500 transition-colors">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <div class="flex text-sm text-gray-600 justify-center">
                    <label for="selfie" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                        <span>Upload a file</span>
                        <input id="selfie" name="selfie" type="file" class="sr-only" required accept="image/*">
                    </label>
                </div>
                <p class="text-xs text-gray-400">PNG, JPG up to 5MB</p>
            </div>
        </div>
        @error('selfie')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- NPWP File Upload -->
    <div>
        <label for="npwp" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Tax Identification Card (NPWP) - Optional</label>
        <p class="text-[11px] text-gray-500 mb-2">Providing your NPWP card can help expedite loan approvals and higher funding limits.</p>
        <div class="mt-1 flex justify-center rounded-xl border border-dashed border-gray-300 px-6 py-6 hover:border-indigo-500 transition-colors">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600 justify-center">
                    <label for="npwp" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                        <span>Upload a file</span>
                        <input id="npwp" name="npwp" type="file" class="sr-only" accept="image/*,application/pdf">
                    </label>
                </div>
                <p class="text-xs text-gray-400">PNG, JPG, PDF up to 5MB</p>
            </div>
        </div>
        @error('npwp')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="pt-4">
        <button type="submit"
                class="flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:scale-[1.01] active:scale-[0.99] transition-all">
            Submit for verification
        </button>
    </div>

</div>
