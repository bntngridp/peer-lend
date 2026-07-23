<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LendFlow — Platform P2P Lending generasi baru. Infrastruktur keuangan kelas institusi untuk mengoptimalkan likuiditas dan imbal hasil Anda.">
    <title>LendFlow — Platform P2P Lending Generasi Baru</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/persegi-nobg.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Dark Theme Variables */
            --bg-body: #090d16;
            --bg-card: #0f172a;
            --bg-card-hover: #1e293b;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-card: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-sub: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #3b82f6;
            --accent-primary-hover: #2563eb;
            --accent-glow: rgba(59, 130, 246, 0.35);
            --badge-bg: rgba(59, 130, 246, 0.12);
            --badge-border: rgba(59, 130, 246, 0.3);
            --badge-text: #60a5fa;
            --grid-line: rgba(59, 130, 246, 0.05);
            --code-bg: #040711;
            --code-text: #38bdf8;
            --nav-bg: rgba(9, 13, 22, 0.85);
            --nav-border: rgba(255, 255, 255, 0.08);
            --orb-opacity: 0.22;
        }

        html.light {
            /* Light Theme Variables */
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --border-color: #e2e8f0;
            --border-card: #e2e8f0;
            --text-main: #0f172a;
            --text-sub: #475569;
            --text-muted: #64748b;
            --accent-primary: #2563eb;
            --accent-primary-hover: #1d4ed8;
            --accent-glow: rgba(37, 99, 235, 0.2);
            --badge-bg: #eff6ff;
            --badge-border: #bfdbfe;
            --badge-text: #1d4ed8;
            --grid-line: rgba(37, 99, 235, 0.06);
            --code-bg: #0f172a;
            --code-text: #38bdf8;
            --nav-bg: rgba(248, 250, 252, 0.85);
            --nav-border: #e2e8f0;
            --orb-opacity: 0.15;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ── Grid Line Background Pattern ── */
        .grid-background {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image: 
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 56px 56px;
            background-position: center center;
        }

        /* Ambient Glow Animated Orbs (Identical in both Light and Dark mode) */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: var(--orb-opacity);
            pointer-events: none;
            z-index: 0;
            animation: orb-float 14s ease-in-out infinite alternate;
        }
        .orb-1 {
            top: -200px;
            left: 20%;
            width: 700px;
            height: 500px;
            background: radial-gradient(circle, #3b82f6 0%, #8b5cf6 50%, transparent 70%);
        }
        .orb-2 {
            bottom: -150px;
            right: 15%;
            width: 600px;
            height: 450px;
            background: radial-gradient(circle, #06b6d4 0%, #ec4899 60%, transparent 70%);
            animation-delay: -7s;
        }

        @keyframes orb-float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-40px) scale(1.06); }
            100% { transform: translateY(20px) scale(0.96); }
        }

        /* ── Full Width Layout Wrap (Edge to Edge) ── */
        .layout-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 0 5%;
        }

        /* ── Header / Navbar (Full Width) ── */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            width: 100%;
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--nav-border);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .nav-container {
            width: 100%;
            padding: 16px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .nav-logo-link:hover {
            opacity: 0.9;
        }

        .nav-logo-img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-link {
            color: var(--text-sub);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
            padding: 6px 12px;
            border-radius: 8px;
        }
        .nav-link:hover {
            color: var(--text-main);
        }

        /* Buttons */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: var(--accent-primary);
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            padding: 11px 22px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 16px var(--accent-glow);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: var(--accent-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 22px var(--accent-glow);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--bg-card);
            color: var(--text-main);
            font-size: 14px;
            font-weight: 500;
            padding: 11px 22px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid var(--border-card);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: var(--bg-card-hover);
            border-color: var(--text-muted);
            transform: translateY(-1px);
        }

        /* Theme Toggle Button (Pure SVG Icon) */
        .theme-toggle-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            color: var(--text-sub);
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            transition: all 0.2s ease;
        }
        .theme-toggle-btn:hover {
            color: var(--text-main);
            border-color: var(--text-muted);
            transform: scale(1.05);
        }
        .theme-toggle-btn svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* ── Hero Section ── */
        .hero-section {
            padding: 80px 0 60px;
            text-align: left;
        }

        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--badge-bg);
            border: 1px solid var(--badge-border);
            color: var(--badge-text);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        .pill-badge-dot {
            width: 6px;
            height: 6px;
            background-color: var(--badge-text);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--badge-text);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .hero-headline {
            font-size: clamp(40px, 6vw, 72px);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -0.035em;
            margin-bottom: 24px;
            color: var(--text-main);
        }

        /* Synchronized Gradient Text for both Dark and Light mode */
        .headline-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: clamp(16px, 2.2vw, 20px);
            color: var(--text-sub);
            max-width: 760px;
            line-height: 1.6;
            font-weight: 400;
            margin-bottom: 40px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        /* ── Full Width Stats Strip (4 Columns Across) ── */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            overflow: hidden;
            margin: 48px 0 90px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        @media (max-width: 768px) {
            .stats-strip {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: var(--bg-card);
            padding: 32px 28px;
            transition: background-color 0.2s ease;
        }
        .stat-card:hover {
            background: var(--bg-card-hover);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 900;
            color: var(--text-main);
            letter-spacing: -0.03em;
            margin-bottom: 4px;
        }

        .stat-desc {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        /* ── Section Header ── */
        .section-header {
            margin-bottom: 44px;
        }

        .section-title {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .section-sub {
            font-size: 17px;
            color: var(--text-sub);
        }

        /* ── Features Grid (Full Width 4 Columns) ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 100px;
        }

        @media (max-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 640px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        .feature-box {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 32px 28px;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .feature-box:hover {
            border-color: var(--accent-primary);
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }

        .feature-icon-box {
            width: 48px;
            height: 48px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            color: var(--accent-primary);
        }
        .feature-icon-box svg {
            width: 24px;
            height: 24px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .feature-name {
            font-size: 19px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 12px;
            letter-spacing: -0.01em;
        }

        .feature-body {
            font-size: 14px;
            color: var(--text-sub);
            line-height: 1.6;
        }

        /* ── Technical Showcase Grid ── */
        .tech-showcase-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 100px;
        }

        @media (max-width: 868px) {
            .tech-showcase-grid {
                grid-template-columns: 1fr;
            }
        }

        .tech-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .tech-card-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            margin-top: 20px;
            align-self: flex-start;
        }
        .tech-card-badge svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
        }

        /* Progress Bar Graphic in Card */
        .yield-progress-wrap {
            margin-top: 28px;
            background: var(--border-card);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .yield-progress-bar {
            height: 100%;
            width: 82%;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
            border-radius: 4px;
        }

        /* Code Snippet Card */
        .code-block-preview {
            background: var(--code-bg);
            border: 1px solid var(--border-card);
            border-radius: 12px;
            padding: 18px 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--code-text);
            margin-top: 24px;
            overflow-x: auto;
            line-height: 1.6;
        }

        /* ── Full Width Footer ── */
        footer {
            border-top: 1px solid var(--border-color);
            padding: 48px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            margin-top: 40px;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .footer-logo-img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .footer-copyright {
            font-size: 14px;
            color: var(--text-muted);
        }

        .footer-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .footer-link {
            font-size: 14px;
            color: var(--text-sub);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-link:hover {
            color: var(--text-main);
        }

        @media (max-width: 640px) {
            .layout-wrap, .nav-container {
                padding-left: 20px;
                padding-right: 20px;
            }
            .hero-section {
                padding: 50px 0 40px;
            }
            footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- Grid Line Background Pattern -->
    <div class="grid-background"></div>

    <!-- Ambient Glowing Gradient Orbs (Synchronized for both Dark and Light mode) -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>

    <!-- Header Navbar (Full Width) -->
    <header>
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-logo-link" id="nav-brand-logo">
                <img id="logo-img-header" src="{{ asset('images/persegi-panjang-drak-mode.png') }}" alt="LendFlow Logo" class="nav-logo-img">
            </a>

            <div class="nav-menu">
                <a href="{{ route('calculator.index') }}" class="nav-link" id="nav-simulator">Simulator</a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary" id="nav-dashboard">
                        Dashboard →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link" id="nav-signin">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-primary" id="nav-getstarted">
                        Get Started →
                    </a>
                @endauth

                <!-- Theme Toggle Button (Clean SVG Vector) -->
                <button class="theme-toggle-btn" id="theme-toggle" onclick="toggleTheme()" title="Switch Theme" aria-label="Switch Theme">
                    <!-- Sun Icon (shown in dark mode) -->
                    <svg id="icon-sun" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <!-- Moon Icon (shown in light mode) -->
                    <svg id="icon-moon" viewBox="0 0 24 24" style="display: none;">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Full Width Content Wrapper -->
    <div class="layout-wrap">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="pill-badge">
                <div class="pill-badge-dot"></div>
                Platform P2P Lending Generasi Baru
            </div>

            <h1 class="hero-headline">
                Dana Mengalir,<br>
                <span class="headline-gradient">Peluang Tumbuh</span>
            </h1>

            <p class="hero-subtitle">
                Infrastruktur keuangan kelas institusi untuk mengoptimalkan likuiditas dan imbal hasil Anda. Aman, transparan, dan berkinerja tinggi.
            </p>

            <div class="hero-cta-group">
                @guest
                    <a href="{{ route('register') }}" class="btn-primary" style="padding: 14px 28px; font-size: 15px;" id="hero-btn-register">
                        Mulai Sekarang — Gratis →
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary" style="padding: 14px 24px; font-size: 15px;" id="hero-btn-login">
                        Sudah punya akun? Masuk
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn-primary" style="padding: 14px 28px; font-size: 15px;" id="hero-btn-dashboard">
                        Ke Dashboard Utamamu →
                    </a>
                @endauth
            </div>
        </section>

        <!-- Full Width Stats Strip (4 Columns Across) -->
        <div class="stats-strip">
            <div class="stat-card">
                <div class="stat-number">IDR 0</div>
                <div class="stat-desc">Biaya Tersembunyi</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0%</div>
                <div class="stat-desc">Gagal Bayar T90</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">52</div>
                <div class="stat-desc">Mitra Institusi</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">13</div>
                <div class="stat-desc">Lapis Keamanan</div>
            </div>
        </div>

        <!-- Section: Core Infrastructure -->
        <div class="section-header">
            <h2 class="section-title">Infrastruktur Inti</h2>
            <p class="section-sub">Dibangun untuk presisi dan skalabilitas tinggi.</p>
        </div>

        <!-- Feature Cards (Full Width 4 Columns Grid) -->
        <div class="features-grid">
            <!-- Auto-Invest Engine -->
            <div class="feature-box">
                <div class="feature-icon-box">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <h3 class="feature-name">Auto-Invest Engine</h3>
                <p class="feature-body">Algoritma alokasi dana otomatis berdasarkan profil risiko presisi tinggi dan batas alokasi sesuai toleransi Anda.</p>
            </div>

            <!-- Payment Gateway -->
            <div class="feature-box">
                <div class="feature-icon-box">
                    <svg viewBox="0 0 24 24">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <h3 class="feature-name">Payment Gateway</h3>
                <p class="feature-body">Integrasi langsung dengan bank tier-1 untuk settlement instan dan enkripsi Webhook verifikasi SHA512.</p>
            </div>

            <!-- Crypto Collateral -->
            <div class="feature-box">
                <div class="feature-icon-box">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h3 class="feature-name">Crypto Collateral</h3>
                <p class="feature-body">Opsi pinjaman aset digital terenkripsi dengan smart contract dan pemantauan LTV real-time Oracle.</p>
            </div>

            <!-- Enterprise Security -->
            <div class="feature-box">
                <div class="feature-icon-box">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <h3 class="feature-name">Enterprise Security</h3>
                <p class="feature-body">Arsitektur zero-trust dengan enkripsi AES-256, otentikasi Google 2FA, dan audit berkala.</p>
            </div>
        </div>

        <!-- Technical Showcase Grid (2 Columns Full Width) -->
        <div class="tech-showcase-grid">
            <!-- Algorithmic Precision Card -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-box" style="width: 42px; height: 42px; margin-bottom: 18px;">
                        <svg viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <h3 class="feature-name" style="font-size: 20px;">Algorithmic Precision</h3>
                    <p class="feature-body">Leverage our proprietary routing engine to optimize yield across decentralized and institutional liquidity pools with minimal slippage.</p>
                </div>
                <div>
                    <div class="yield-progress-wrap">
                        <div class="yield-progress-bar"></div>
                    </div>
                </div>
            </div>

            <!-- Fortified Security Card -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-box" style="width: 42px; height: 42px; margin-bottom: 18px;">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-name" style="font-size: 20px;">Fortified Security</h3>
                    <p class="feature-body">Multi-signature consensus and real-time threat detection safeguard your capital at every layer.</p>
                </div>
                <div class="tech-card-badge">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Audited by Trail of Bits
                </div>
            </div>

            <!-- High Throughput Card -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-box" style="width: 42px; height: 42px; margin-bottom: 18px;">
                        <svg viewBox="0 0 24 24">
                            <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                            <line x1="6" y1="6" x2="6.01" y2="6"></line>
                            <line x1="6" y1="18" x2="6.01" y2="18"></line>
                        </svg>
                    </div>
                    <h3 class="feature-name" style="font-size: 20px;">High-Throughput</h3>
                    <p class="feature-body">Execute massive volume with sub-millisecond latency. Built for algorithmic trading systems and enterprise treasury management.</p>
                </div>
            </div>

            <!-- Seamless Integration Card with Code Snippet -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-box" style="width: 42px; height: 42px; margin-bottom: 18px;">
                        <svg viewBox="0 0 24 24">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-name" style="font-size: 20px;">Seamless Integration</h3>
                    <p class="feature-body">Connect your existing treasury systems via our robust REST API and WebSocket events.</p>
                </div>
                <div class="code-block-preview">
<span style="color: #60a5fa;">POST</span> /v1/allocations
{
  <span style="color: #f472b6;">"strategy"</span>: <span style="color: #a7f3d0;">"conservative"</span>,
  <span style="color: #f472b6;">"amount"</span>: <span style="color: #fde047;">5000000</span>,
  <span style="color: #f472b6;">"currency"</span>: <span style="color: #a7f3d0;">"IDR"</span>
}
                </div>
            </div>
        </div>

        <!-- Full Width Footer -->
        <footer>
            <div class="footer-left">
                <img id="logo-img-footer" src="{{ asset('images/persegi-panjang-drak-mode.png') }}" alt="LendFlow Logo" class="footer-logo-img">
                <span class="footer-copyright">© {{ date('Y') }} LendFlow. All rights reserved.</span>
            </div>
            <div class="footer-links">
                <a href="{{ route('calculator.index') }}" class="footer-link">Simulator</a>
                <a href="{{ route('api.docs') }}" class="footer-link">API Docs</a>
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Security</a>
            </div>
        </footer>
    </div>

    <!-- Theme Switcher JavaScript -->
    <script>
        const darkLogo = "{{ asset('images/persegi-panjang-drak-mode.png') }}";
        const lightLogo = "{{ asset('images/persegi-panjang-liegt-mode.png') }}";

        function applyTheme(isLight) {
            const htmlEl = document.documentElement;
            const logoHeader = document.getElementById('logo-img-header');
            const logoFooter = document.getElementById('logo-img-footer');
            const iconSun = document.getElementById('icon-sun');
            const iconMoon = document.getElementById('icon-moon');

            if (isLight) {
                htmlEl.classList.remove('dark');
                htmlEl.classList.add('light');
                logoHeader.src = lightLogo;
                logoFooter.src = lightLogo;
                iconSun.style.display = 'none';
                iconMoon.style.display = 'block';
                localStorage.setItem('lendflow_theme', 'light');
            } else {
                htmlEl.classList.remove('light');
                htmlEl.classList.add('dark');
                logoHeader.src = darkLogo;
                logoFooter.src = darkLogo;
                iconSun.style.display = 'block';
                iconMoon.style.display = 'none';
                localStorage.setItem('lendflow_theme', 'dark');
            }
        }

        function toggleTheme() {
            const isLight = document.documentElement.classList.contains('light');
            applyTheme(!isLight);
        }

        // Initialize Theme from localStorage
        (function initTheme() {
            const savedTheme = localStorage.getItem('lendflow_theme');
            if (savedTheme === 'light') {
                applyTheme(true);
            } else {
                applyTheme(false);
            }
        })();
    </script>
</body>
</html>
