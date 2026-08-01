<!-- Multi-step Stepper + Form Layout with Real File Upload & Webcam Photo Capture -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" 
     x-data="{ 
         currentStep: 2,
         ktpPreview: null,
         ktpFileName: null,
         ktpFileSize: null,
         selfiePreview: null,
         selfieFileName: null,
         selfieFileSize: null,
         dragOverKtp: false,
         dragOverSelfie: false,
         webcamOpen: false,
         webcamTarget: 'ktp',
         webcamStream: null,
         webcamError: null,

         handleKtpSelect(e) {
             const file = e.target.files[0];
             if (file) {
                 this.setKtpFile(file);
             }
         },
         handleSelfieSelect(e) {
             const file = e.target.files[0];
             if (file) {
                 this.setSelfieFile(file);
             }
         },
         setKtpFile(file) {
             this.ktpFileName = file.name;
             this.ktpFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
             if (file.type && file.type.startsWith('image/')) {
                 this.ktpPreview = URL.createObjectURL(file);
             } else {
                 this.ktpPreview = 'pdf';
             }
         },
         setSelfieFile(file) {
             this.selfieFileName = file.name;
             this.selfieFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
             if (file.type && file.type.startsWith('image/')) {
                 this.selfiePreview = URL.createObjectURL(file);
             }
         },
         async startWebcam(target) {
             this.webcamTarget = target;
             this.webcamError = null;
             this.webcamOpen = true;
             this.$nextTick(async () => {
                 try {
                     const stream = await navigator.mediaDevices.getUserMedia({ 
                         video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } } 
                     });
                     this.webcamStream = stream;
                     if (this.$refs.videoElem) {
                         this.$refs.videoElem.srcObject = stream;
                     }
                 } catch (err) {
                     console.error('Webcam access error:', err);
                     this.webcamError = 'Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin akses webcam di browser Anda.';
                 }
             });
         },
         snapPhoto() {
             const video = this.$refs.videoElem;
             const canvas = this.$refs.canvasElem;
             if (!video || !canvas) return;

             canvas.width = video.videoWidth || 640;
             canvas.height = video.videoHeight || 480;
             const ctx = canvas.getContext('2d');
             ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

             canvas.toBlob((blob) => {
                 if (!blob) return;
                 const fileName = this.webcamTarget === 'ktp' ? 'ktp_webcam_capture.jpg' : 'selfie_webcam_capture.jpg';
                 const file = new File([blob], fileName, { type: 'image/jpeg' });

                 const dataTransfer = new DataTransfer();
                 dataTransfer.items.add(file);

                 if (this.webcamTarget === 'ktp') {
                     const input = document.getElementById('ktp');
                     input.files = dataTransfer.files;
                     this.setKtpFile(file);
                 } else {
                     const input = document.getElementById('selfie');
                     input.files = dataTransfer.files;
                     this.setSelfieFile(file);
                 }

                 this.stopWebcam();
             }, 'image/jpeg', 0.95);
         },
         stopWebcam() {
             if (this.webcamStream) {
                 this.webcamStream.getTracks().forEach(track => track.stop());
                 this.webcamStream = null;
             }
             this.webcamOpen = false;
         }
     }">
    
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
                    ✓
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
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 block">Identity Card</span>
                    <span class="text-[10px] text-slate-400 font-medium">Upload KTP / ID Document</span>
                </div>
            </div>

            <!-- Step 3: Selfie Match -->
            <div class="flex items-start gap-3" :class="selfiePreview ? '' : 'opacity-60'">
                <div class="h-7 w-7 rounded-full font-bold flex items-center justify-center text-xs border"
                     :class="selfiePreview ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-slate-100 text-slate-500 border-slate-200'">
                    <span x-text="selfiePreview ? '✓' : '3'">3</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Selfie Match</span>
                    <span class="text-[10px] text-slate-400 font-medium">Facial verification photo</span>
                </div>
            </div>

            <!-- Step 4: Proof of Address -->
            <div class="flex items-start gap-3 opacity-60">
                <div class="h-7 w-7 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-xs border border-slate-200">
                    4
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Proof of Address</span>
                    <span class="text-[10px] text-slate-400 font-medium">Utility bill or statement</span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center gap-1.5">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0112 2.714z" />
            </svg>
            <span>End-to-end 256-bit encrypted verification</span>
        </div>
    </div>

    <!-- Right Main Form Body (Spans 8 Cols) -->
    <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
        
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Upload Identity Card (KTP) &amp; Selfie</h3>
            <p class="text-xs text-slate-500 mt-1 font-medium">Please provide clear photos of your government-issued ID and a selfie holding the document.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Drag & Drop Zone (Spans 8 Cols) -->
            <div class="md:col-span-8 space-y-5">
                
                <!-- 1. KTP Dropzone Card -->
                <div class="border-2 border-dashed rounded-2xl p-5 text-center transition-all bg-slate-50/50"
                     :class="dragOverKtp ? 'border-emerald-600 bg-emerald-50/40' : 'border-slate-200 hover:border-emerald-600'"
                     x-on:dragover.prevent="dragOverKtp = true"
                     x-on:dragleave.prevent="dragOverKtp = false"
                     x-on:drop.prevent="
                         dragOverKtp = false;
                         const files = $event.dataTransfer.files;
                         if (files.length > 0) {
                             $refs.ktpInput.files = files;
                             setKtpFile(files[0]);
                         }
                     ">
                    
                    <div class="mx-auto h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center mb-3 border border-emerald-200/60 shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zM6.75 15a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" />
                        </svg>
                    </div>

                    <h4 class="text-xs font-bold text-slate-900">Upload KTP / Identity Document</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Drag &amp; Drop file here, or browse from device.</p>
                    <p class="text-[10px] text-slate-400 font-medium">Supported: JPG, PNG, PDF. Max size: 5 MB.</p>
                    
                    <!-- File Name Indicator if uploaded -->
                    <template x-if="ktpFileName">
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-300">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span class="truncate max-w-[180px]" x-text="ktpFileName"></span>
                            <span class="text-[10px] opacity-75" x-text="ktpFileSize"></span>
                        </div>
                    </template>

                    <div class="flex flex-wrap items-center justify-center gap-3 mt-4">
                        <label for="ktp" class="cursor-pointer py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <span>Browse Files</span>
                            <input id="ktp" x-ref="ktpInput" name="ktp" type="file" class="sr-only" required accept="image/*,application/pdf" @change="handleKtpSelect">
                        </label>
                        <button type="button" @click="startWebcam('ktp')" 
                                class="py-2 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="h-4 w-4 text-emerald-700 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                            </svg>
                            <span>Take Photo</span>
                        </button>
                    </div>
                </div>
                @error('ktp')
                    <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                @enderror

                <!-- 2. Selfie Dropzone Card -->
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Selfie Holding KTP</label>
                            <p class="text-[11px] text-slate-500">Take a clear photo holding your KTP next to your face.</p>
                        </div>
                        <button type="button" @click="startWebcam('selfie')" 
                                class="py-1.5 px-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-xs font-bold hover:bg-emerald-100 border border-emerald-200 transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="h-4 w-4 text-emerald-700 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            </svg>
                            <span>Use Camera</span>
                        </button>
                    </div>

                    <input id="selfie" x-ref="selfieInput" name="selfie" type="file" required accept="image/*" @change="handleSelfieSelect"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 cursor-pointer">
                    
                    <template x-if="selfieFileName">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-300">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span class="truncate max-w-[180px]" x-text="selfieFileName"></span>
                            <span class="text-[10px] opacity-75" x-text="selfieFileSize"></span>
                        </div>
                    </template>
                </div>
                @error('selfie')
                    <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                @enderror

            </div>

            <!-- Right Guidelines & Document Preview (Spans 4 Cols) -->
            <div class="md:col-span-4 space-y-4">
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Document Preview</span>
                        <template x-if="ktpPreview || selfiePreview">
                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800">Verified Preview</span>
                        </template>
                    </div>

                    <!-- KTP Image Preview Box -->
                    <div class="h-36 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 text-xs font-medium overflow-hidden relative shadow-inner">
                        <template x-if="ktpPreview && ktpPreview !== 'pdf'">
                            <img :src="ktpPreview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="ktpPreview === 'pdf'">
                            <div class="text-center p-3 text-emerald-700 dark:text-emerald-400 font-bold text-xs">
                                <svg class="h-8 w-8 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span>PDF Document Loaded</span>
                            </div>
                        </template>
                        <template x-if="!ktpPreview">
                            <div class="text-center space-y-1">
                                <svg class="h-7 w-7 mx-auto text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="text-[11px]">KTP Image Preview</span>
                            </div>
                        </template>
                    </div>

                    <!-- Selfie Image Preview Box if available -->
                    <template x-if="selfiePreview">
                        <div class="space-y-1 pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Selfie Preview</span>
                            <div class="h-28 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shadow-inner">
                                <img :src="selfiePreview" class="h-full w-full object-cover">
                            </div>
                        </div>
                    </template>

                    <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-700 text-[11px]">
                        <span class="font-bold text-slate-700 dark:text-slate-300 block">Guidelines:</span>
                        <div class="flex items-center gap-1.5 text-emerald-800 dark:text-emerald-400 font-medium">
                            <span>✓</span> All 4 corners must be visible
                        </div>
                        <div class="flex items-center gap-1.5 text-emerald-800 dark:text-emerald-400 font-medium">
                            <span>✓</span> Ensure good lighting, no glare
                        </div>
                        <div class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-medium">
                            <span>✕</span> No blurred or hidden details
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Navigation Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('dashboard') }}" class="py-2.5 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                &larr; Back to Dashboard
            </a>
            <button type="submit" id="btn_submit_kyc" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs inline-flex items-center gap-1 cursor-pointer">
                Continue &rarr;
            </button>
        </div>

    </div>

    <!-- ─── Live Webcam Photo Capture Modal ────────────────────────────────────── -->
    <div x-show="webcamOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4" 
         style="display: none;">
        
        <div @click.away="stopWebcam()" 
             class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 text-center">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100" 
                    x-text="webcamTarget === 'ktp' ? 'Take Photo of KTP Document' : 'Take Selfie Holding KTP'"></h3>
                <button type="button" @click="stopWebcam()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    ✕
                </button>
            </div>

            <!-- Error Banner if camera fails -->
            <template x-if="webcamError">
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold" x-text="webcamError"></div>
            </template>

            <!-- Video Stream Container -->
            <div class="relative rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 aspect-video flex items-center justify-center">
                <video x-ref="videoElem" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas x-ref="canvasElem" class="hidden"></canvas>
                
                <!-- Alignment Guide Frame -->
                <div class="absolute inset-4 border-2 border-dashed border-emerald-400/70 rounded-xl pointer-events-none flex items-center justify-center">
                    <span class="text-[10px] font-bold text-emerald-400 bg-slate-950/80 px-3 py-1 rounded-full uppercase tracking-wider">Posisikan Dokumen Di Dalam Bingkai</span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" @click="stopWebcam()" 
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="snapPhoto()" id="btn_snap_photo"
                        class="flex-1 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    </svg>
                    <span>Ambil Foto (Snap)</span>
                </button>
            </div>
        </div>
    </div>

</div>
