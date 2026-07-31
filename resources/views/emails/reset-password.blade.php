<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi LendFlow</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-text-size-adjust: 100%;
        }
        .container {
            max-width: 560px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
        }
        .header {
            background-color: #0f172a;
            padding: 32px 40px;
            text-align: center;
            border-bottom: 3px solid #15803d;
        }
        .logo-badge {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: #15803d;
            color: #ffffff;
            font-weight: 900;
            font-size: 18px;
            line-height: 36px;
            border-radius: 10px;
            text-align: center;
            vertical-align: middle;
            margin-right: 8px;
        }
        .logo-text {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            vertical-align: middle;
        }
        .content {
            padding: 40px;
        }
        .title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background-color: #15803d;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.25);
        }
        .url-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            word-break: break-all;
            font-size: 12px;
            color: #64748b;
            margin-top: 24px;
        }
        .security-notice {
            background-color: #f0fdf4;
            border-left: 4px solid #15803d;
            padding: 12px 16px;
            font-size: 12px;
            color: #166534;
            border-radius: 4px;
            margin-top: 24px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 40px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Official LendFlow Logo -->
        <div class="header">
            @if(isset($message) && file_exists(public_path('images/persegi-panjang-drak-mode.png')))
                <img src="{{ $message->embed(public_path('images/persegi-panjang-drak-mode.png')) }}" alt="LendFlow Logo" style="max-height: 48px; width: auto; display: inline-block; vertical-align: middle;">
            @else
                <span class="logo-badge">L</span>
                <span class="logo-text">LendFlow</span>
            @endif
            <div style="font-size: 10px; color: #86efac; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 10px;">
                Institutional Grade P2P Lending Platform
            </div>
        </div>

        <!-- Body Content -->
        <div class="content">
            <h1 class="title">Halo, {{ $user->profile->full_name ?? 'Pengguna LendFlow' }} 👋</h1>
            <p class="text">
                Kami menerima permintaan untuk mereset kata sandi akun LendFlow Anda. Klik tombol di bawah ini untuk membuat kata sandi baru secara langsung dan aman:
            </p>

            <!-- Direct Reset Link Button -->
            <div class="btn-container">
                <a href="{{ $url }}" target="_blank" class="btn">
                    Reset Password Sekarang &rarr;
                </a>
            </div>

            <!-- Expiration & Security Notice -->
            <div class="security-notice">
                🔒 Tautan reset kata sandi ini akan kadaluwarsa dalam <strong>{{ $count }} menit</strong>. Jika Anda tidak meminta perubahan ini, Anda dapat mengabaikan email ini dan akun Anda akan tetap aman.
            </div>

            <!-- Fallback URL Text -->
            <div class="url-box">
                <strong>Tombol tidak dapat diklik?</strong> Salin dan tempel tautan berikut ke browser Anda:<br>
                <a href="{{ $url }}" style="color: #15803d; text-decoration: underline;">{{ $url }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; 2026 LendFlow Inc. Platform Layanan Keuangan P2P Terintegrasi.<br>
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
