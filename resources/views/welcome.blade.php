<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LendFlow — Platform P2P Lending terpercaya. Investasikan dana Anda atau ajukan pinjaman dengan agunan crypto secara transparan dan aman.">
    <title>LendFlow — Smarter P2P Lending</title>
    <link rel="icon" type="image/png" href="{{ asset('images/persegi-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #050811;
            --bg-secondary: #0c1120;
            --accent-blue: #4f8ef7;
            --accent-cyan: #06d6f7;
            --accent-purple: #a855f7;
            --accent-green: #10d98a;
            --text-primary: #f0f4ff;
            --text-muted: #8899bb;
            --glass-bg: rgba(255,255,255,0.04);
            --glass-border: rgba(255,255,255,0.09);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Animated Background ── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.18;
            animation: orb-float 12s ease-in-out infinite;
        }
        .bg-orb-1 {
            width: 700px; height: 700px;
            background: radial-gradient(circle, #4f8ef7 0%, transparent 70%);
            top: -200px; left: -150px;
            animation-delay: 0s;
        }
        .bg-orb-2 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, #a855f7 0%, transparent 70%);
            bottom: -100px; right: -100px;
            animation-delay: -4s;
        }
        .bg-orb-3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #06d6f7 0%, transparent 70%);
            top: 40%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -8s;
        }
        @keyframes orb-float {
            0%, 100% { transform: translateY(0px) scale(1); }
            33% { transform: translateY(-30px) scale(1.05); }
            66% { transform: translateY(20px) scale(0.97); }
        }

        /* Grid lines bg */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(79,142,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,142,247,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Layout ── */
        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 40px;
            border-bottom: 1px solid var(--glass-border);
            backdrop-filter: blur(16px);
            background: rgba(5,8,17,0.6);
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 16px;
            color: #fff;
        }
        .nav-logo-text {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-links { display: flex; align-items: center; gap: 12px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-ghost {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
        }
        .btn-ghost:hover {
            background: var(--glass-bg);
            border-color: rgba(79,142,247,0.4);
            color: var(--accent-blue);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), #3b7de8);
            color: #fff;
            box-shadow: 0 4px 20px rgba(79,142,247,0.35);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(79,142,247,0.5);
        }
        .btn-outline {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid rgba(79,142,247,0.5);
        }
        .btn-outline:hover {
            background: rgba(79,142,247,0.1);
            border-color: var(--accent-blue);
        }
        .btn-lg {
            padding: 16px 36px;
            font-size: 16px;
            border-radius: 14px;
        }

        /* ── Hero Section ── */
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 24px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(79,142,247,0.12);
            border: 1px solid rgba(79,142,247,0.25);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            color: var(--accent-blue);
            margin-bottom: 32px;
            animation: fade-in-up 0.6s ease both;
        }
        .hero-badge-dot {
            width: 7px; height: 7px;
            background: var(--accent-blue);
            border-radius: 50%;
            animation: pulse-dot 2s ease infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        h1 {
            font-size: clamp(42px, 7vw, 80px);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -2px;
            margin-bottom: 24px;
            animation: fade-in-up 0.6s ease 0.1s both;
        }
        .h1-gradient {
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--accent-cyan) 50%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-sub {
            font-size: clamp(16px, 2.5vw, 20px);
            color: var(--text-muted);
            max-width: 560px;
            line-height: 1.7;
            margin-bottom: 48px;
            animation: fade-in-up 0.6s ease 0.2s both;
        }
        .hero-cta {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fade-in-up 0.6s ease 0.3s both;
        }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Stats Strip ── */
        .stats-strip {
            display: flex;
            justify-content: center;
            gap: 48px;
            padding: 40px 24px;
            border-top: 1px solid var(--glass-border);
            flex-wrap: wrap;
            animation: fade-in-up 0.6s ease 0.4s both;
        }
        .stat-item { text-align: center; }
        .stat-value {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ── Feature Cards ── */
        .features {
            padding: 60px 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }
        .feature-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 28px;
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
        }
        .feature-card:hover {
            border-color: rgba(79,142,247,0.3);
            background: rgba(79,142,247,0.06);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(79,142,247,0.12);
        }
        .feature-icon {
            font-size: 28px;
            margin-bottom: 14px;
        }
        .feature-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .feature-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ── Footer ── */
        footer {
            text-align: center;
            padding: 28px;
            border-top: 1px solid var(--glass-border);
            color: var(--text-muted);
            font-size: 13px;
        }
        footer a { color: var(--accent-blue); text-decoration: none; }
        footer a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            nav { padding: 16px 20px; }
            .features { padding: 40px 20px; }
            .stats-strip { gap: 28px; }
            h1 { letter-spacing: -1px; }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-canvas">
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-orb bg-orb-3"></div>
    </div>
    <div class="bg-grid"></div>

    <div class="page-wrap">
        <!-- Navbar -->
        <nav>
            <a href="{{ route('home') }}" class="nav-logo">
                <img src="{{ asset('images/persegi-panjang-drak-mode.png') }}" alt="LendFlow Logo" style="height: 38px; width: auto; object-fit: contain;">
            </a>
            <div class="nav-links">
                <a href="{{ route('calculator.index') }}" class="btn btn-ghost" id="nav-calculator">
                    🧮 Simulator
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary" id="nav-dashboard">
                        Dashboard →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost" id="nav-login">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary" id="nav-register">
                        Get Started →
                    </a>
                @endauth
            </div>
        </nav>

        <!-- Hero -->
        <main class="hero">
            <div class="hero-badge">
                <div class="hero-badge-dot"></div>
                Platform P2P Lending Generasi Baru
            </div>

            <h1>
                Dana Mengalir,<br>
                <span class="h1-gradient">Peluang Tumbuh</span>
            </h1>

            <p class="hero-sub">
                LendFlow menghubungkan Peminjam dan Pemberi Dana dalam satu ekosistem
                yang transparan, aman, dan didukung teknologi agunan crypto canggih.
            </p>

            @guest
            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg" id="hero-register">
                    🚀 Mulai Sekarang — Gratis
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline btn-lg" id="hero-login">
                    Sudah punya akun? Masuk
                </a>
            </div>
            @else
            <div class="hero-cta">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg" id="hero-dashboard">
                    Ke Dashboard →
                </a>
            </div>
            @endguest
        </main>

        <!-- Stats Strip -->
        <div class="stats-strip">
            <div class="stat-item">
                <div class="stat-value">IDR 0</div>
                <div class="stat-label">Total Pinjaman Disalurkan</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">0%</div>
                <div class="stat-label">Tingkat Default</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">52</div>
                <div class="stat-label">Test Suite Passed ✅</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">13</div>
                <div class="stat-label">Fase Pengembangan</div>
            </div>
        </div>

        <!-- Feature Cards -->
        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <div class="feature-title">Auto-Invest Engine</div>
                <div class="feature-desc">Dana Anda bekerja otomatis. Tentukan kriteria grade risiko dan batas alokasi — mesin kami yang mencocokkan.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <div class="feature-title">Payment Gateway</div>
                <div class="feature-desc">Top-up saldo via Midtrans Snap. Webhook terverifikasi SHA512 untuk keamanan transaksi maksimal.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <div class="feature-title">Crypto Collateral</div>
                <div class="feature-desc">BTC, ETH, USDT sebagai agunan dengan monitoring LTV real-time dan likuidasi otomatis di 80%.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <div class="feature-title">Enterprise Security</div>
                <div class="feature-desc">2FA Google Authenticator, KYC terverifikasi, dan RBAC (Admin/Borrower/Lender) melindungi setiap akun.</div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>© {{ date('Y') }} <strong>LendFlow</strong> — Built with ❤️ using Laravel 11 &amp; PHP 8.3 &nbsp;·&nbsp;
                <a href="{{ route('calculator.index') }}">Loan Simulator</a> &nbsp;·&nbsp;
                <a href="{{ route('api.docs') }}">API Docs</a>
            </p>
        </footer>
    </div>
</body>
</html>
