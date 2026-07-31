<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | LendFlow</title>
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
    
    <div class="max-w-md w-full text-center space-y-6">
        
        <!-- 404 Illustration Circle -->
        <div class="mx-auto h-28 w-28 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 font-extrabold text-3xl shadow-xs">
            404
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Page Not Found</h1>
            <p class="text-xs font-medium text-slate-500 leading-relaxed max-w-sm mx-auto">
                The page you're looking for doesn't exist or has been moved. Please verify the URL, or return to the main dashboard to continue your session.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="/dashboard" class="w-full sm:w-auto py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                Return to Dashboard &rarr;
            </a>
            <a href="mailto:support@lendflow.com" class="w-full sm:w-auto py-2.5 px-6 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors shadow-xs">
                Contact Support
            </a>
        </div>

        <p class="text-[10px] font-mono text-slate-400 pt-4">Error Reference: ERR_404_NOT_FOUND_SECURE</p>

    </div>

</body>
</html>
