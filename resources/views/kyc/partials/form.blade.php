<!-- Multi-step Stepper + Form Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" x-data="{ currentStep: 2 }">
    
    <!-- Left Sidebar Stepper -->
    <div class="lg:col-span-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-sm font-bold text-slate-900">Identity Verification</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Complete your KYC to access institutional features.</p>
        </div>

        <!-- Steps List -->
        <div class="space-y-4">
            <!-- Step 1: Basic Info -->
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs border border-emerald-200">
                    
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-900 block">Basic Info</span>
                    <span class="text-[10px] text-slate-400 font-medium">Personal details completed</span>
                </div>
            </div>

            <!-- Step 2: Identity Card (Active) -->
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                    2
                </div>
                <div>
                    <span class="text-xs font-bold text-emerald-700 block">Identity Card</span>
                    <span class="text-[10px] text-slate-400 font-medium">Upload KTP / ID Document</span>
                </div>
            </div>

            <!-- Step 3: Selfie Match -->
            <div class="flex items-start gap-3 opacity-60">
                <div class="h-7 w-7 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-xs border border-slate-200">
                    3
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-700 block">Selfie Match</span>
                    <span class="text-[10px] text-slate-400 font-medium">Facial verification photo</span>
                </div>
            </div>

            <!-- Step 4: Proof of Address -->
            <div class="flex items-start gap-3 opacity-60">
                <div class="h-7 w-7 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-xs border border-slate-200">
                    4
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-700 block">Proof of Address</span>
                    <span class="text-[10px] text-slate-400 font-medium">Utility bill or statement</span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400 flex items-center gap-1">
            <span></span> End-to-end encrypted verification
        </div>
    </div>

    <!-- Right Main Form Body (Spans 8 Cols) -->
    <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
        
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Upload Identity Card (KTP)</h3>
            <p class="text-xs text-slate-500 mt-1 font-medium">Please provide a clear photo of your valid government-issued identification card.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Drag & Drop Zone (Spans 8 Cols) -->
            <div class="md:col-span-8 space-y-4">
                
                <!-- KTP Dropzone Card -->
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-600 transition-colors bg-slate-50/50">
                    <div class="text-4xl mb-2"></div>
                    <h4 class="text-xs font-bold text-slate-900">Drag &amp; Drop your file here</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Supported formats: JPG, PNG, PDF. Max size: 10 MB.</p>
                    
                    <div class="flex flex-wrap items-center justify-center gap-3 mt-4">
                        <label for="ktp" class="cursor-pointer py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs inline-flex items-center gap-1.5">
                            <span></span> Browse Files
                            <input id="ktp" name="ktp" type="file" class="sr-only" required accept="image/*,application/pdf">
                        </label>
                        <button type="button" class="py-2 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs inline-flex items-center gap-1.5">
                            <span></span> Take Photo
                        </button>
                    </div>
                </div>
                @error('ktp')
                    <p class="text-xs text-rose-600 font-bold">{{ $message }}</p>
                @enderror

                <!-- Selfie Dropzone Card -->
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/30">
                    <label class="block text-xs font-bold text-slate-800 mb-1">Selfie Holding KTP</label>
                    <p class="text-[11px] text-slate-500 mb-2">Take a clear photo holding your KTP next to your face.</p>
                    <input id="selfie" name="selfie" type="file" required accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">
                </div>
                @error('selfie')
                    <p class="text-xs text-rose-600 font-bold">{{ $message }}</p>
                @enderror

            </div>

            <!-- Right Guidelines & Document Preview (Spans 4 Cols) -->
            <div class="md:col-span-4 space-y-4">
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50 space-y-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Document Preview</span>
                    <div class="h-28 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs font-medium">
                        ️ Image Preview
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-200 text-[11px]">
                        <span class="font-bold text-slate-700 block">Guidelines:</span>
                        <div class="flex items-center gap-1.5 text-emerald-800 font-medium">
                            <span></span> All 4 corners must be visible
                        </div>
                        <div class="flex items-center gap-1.5 text-emerald-800 font-medium">
                            <span></span> Ensure good lighting, no glare
                        </div>
                        <div class="flex items-center gap-1.5 text-rose-600 font-medium">
                            <span></span> No blurred or hidden details
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Navigation Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <a href="{{ route('dashboard') }}" class="py-2.5 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                &larr; Back to Dashboard
            </a>
            <button type="submit" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs inline-flex items-center gap-1">
                Continue &rarr;
            </button>
        </div>

    </div>

</div>
