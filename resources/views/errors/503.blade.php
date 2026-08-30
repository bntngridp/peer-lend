<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance | LendFlow</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 700: '#15803D', 800: '#166534' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-6 antialiased">
    
    <div class="max-w-xl w-full text-center space-y-8">
        
        <!-- Logo -->
        <div class="flex items-center justify-center gap-2">
            <span class="h-10 w-10 rounded-2xl bg-emerald-700 text-white font-black text-xl flex items-center justify-center shadow-xs">LF</span>
            <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
        </div>

        <!-- Main Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-xs space-y-6">
            
            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                <span>System Maintenance</span>
            </div>

            <div class="space-y-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    We're temporarily down for scheduled maintenance
                </h1>
                <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-lg mx-auto">
                    Our engineering team is currently performing essential system upgrades to improve performance and reliability for the LendFlow enterprise platform. We apologize for the interruption to your workflow.
                </p>
            </div>

            <!-- Maintenance Specs Box -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold pt-2">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Estimated Completion</span>
                    <span class="text-sm font-extrabold text-slate-900 mt-1 block">14:00 UTC</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status Updates</span>
                    <span class="text-sm font-extrabold text-emerald-700 mt-1 block">status.lendflow.com</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="https://status.lendflow.com" target="_blank" class="w-full sm:w-auto py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Check Status Now
                </a>
                <a href="mailto:support@lendflow.com" class="w-full sm:w-auto py-2.5 px-6 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors shadow-xs">
                    Contact Urgent Support
                </a>
            </div>

        </div>

        <p class="text-[10px] text-slate-400 font-medium">&copy; 2026 LendFlow Financial Systems. All rights reserved. • API Status: Operational</p>

    </div>

</body>
</html>
