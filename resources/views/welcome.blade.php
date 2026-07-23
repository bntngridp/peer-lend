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
            /* Dark Theme Variables (Default matching Stitch Dark Mockup) */
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
            --grid-line: rgba(59, 130, 246, 0.04);
            --code-bg: #040711;
            --code-text: #38bdf8;
            --nav-bg: rgba(9, 13, 22, 0.85);
            --nav-border: rgba(255, 255, 255, 0.08);
        }

        html.light {
            /* Light Theme Variables (Matching Stitch Light Mockup) */
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
            --grid-line: rgba(37, 99, 235, 0.05);
            --code-bg: #0f172a;
            --code-text: #38bdf8;
            --nav-bg: rgba(248, 250, 252, 0.85);
            --nav-border: #e2e8f0;
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
            background-size: 48px 48px;
            background-position: center center;
        }

        /* Ambient Glow Orb */
        .ambient-glow {
            position: fixed;
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(124, 58, 237, 0.08) 50%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            transition: opacity 0.3s ease;
        }

        /* ── Layout Wrap ── */
        .layout-wrap {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ── Header / Navbar ── */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--nav-border);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
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
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 20px;
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
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 14px var(--accent-glow);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: var(--accent-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--accent-glow);
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
            padding: 10px 20px;
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

        /* Theme Toggle Button */
        .theme-toggle-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            color: var(--text-sub);
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .theme-toggle-btn:hover {
            color: var(--text-main);
            border-color: var(--text-muted);
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
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 24px;
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
            font-size: clamp(36px, 5.5vw, 64px);
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 20px;
            color: var(--text-main);
        }

        .headline-gradient {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: clamp(16px, 2vw, 19px);
            color: var(--text-sub);
            max-width: 680px;
            line-height: 1.6;
            font-weight: 400;
            margin-bottom: 36px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        /* ── Stats Strip ── */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1px;
            background: var(--border-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            overflow: hidden;
            margin: 48px 0 80px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .stat-card {
            background: var(--bg-card);
            padding: 28px 24px;
            transition: background-color 0.2s ease;
        }
        .stat-card:hover {
            background: var(--bg-card-hover);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
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
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .section-sub {
            font-size: 16px;
            color: var(--text-sub);
        }

        /* ── Feature Cards Grid ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 90px;
        }

        .feature-box {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-box:hover {
            border-color: var(--accent-primary);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .feature-icon-wrapper {
            width: 48px;
            height: 48px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--accent-primary);
            font-size: 22px;
        }

        .feature-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .feature-body {
            font-size: 14px;
            color: var(--text-sub);
            line-height: 1.6;
        }

        /* ── Technical Showcase Grid (Stitch Light & Tech Style) ── */
        .tech-showcase-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 90px;
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
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .tech-card-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            margin-top: 16px;
            align-self: flex-start;
        }

        /* Progress Bar Graphic in Card */
        .yield-progress-wrap {
            margin-top: 24px;
            background: var(--border-card);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .yield-progress-bar {
            height: 100%;
            width: 78%;
            background: linear-gradient(90deg, #3b82f6 0%, #06b6d4 100%);
            border-radius: 4px;
        }

        /* Code Snippet Card */
        .code-block-preview {
            background: var(--code-bg);
            border: 1px solid var(--border-card);
            border-radius: 10px;
            padding: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--code-text);
            margin-top: 20px;
            overflow-x: auto;
            line-height: 1.5;
        }

        /* ── Footer ── */
        footer {
            border-top: 1px solid var(--border-color);
            padding: 40px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .footer-logo-img {
            height: 28px;
            width: auto;
            object-fit: contain;
        }

        .footer-copyright {
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .footer-link {
            font-size: 13px;
            color: var(--text-sub);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-link:hover {
            color: var(--text-main);
        }

        /* Responsive Breakpoints */
        @media (max-width: 640px) {
            .nav-menu {
                gap: 10px;
            }
            .nav-link {
                display: none;
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

    <!-- Grid Line Background & Ambient Lighting -->
    <div class="grid-background"></div>
    <div class="ambient-glow"></div>

    <!-- Header Navbar -->
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

                <!-- Dark/Light Theme Toggle -->
                <button class="theme-toggle-btn" id="theme-toggle" onclick="toggleTheme()" title="Switch Theme">
                    <span id="theme-icon">☀️</span>
                    <span id="theme-label" style="display: none;">Theme</span>
                </button>
            </div>
        </div>
    </header>

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

        <!-- Stats Strip (4 Columns Grid) -->
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

        <!-- Feature Cards (Grid 2x2) -->
        <div class="features-grid">
            <div class="feature-box">
                <div class="feature-icon-wrapper">⚙️</div>
                <h3 class="feature-name">Auto-Invest Engine</h3>
                <p class="feature-body">Algoritma alokasi dana otomatis berdasarkan profil risiko presisi tinggi dan batas alokasi sesuai toleransi Anda.</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon-wrapper">💳</div>
                <h3 class="feature-name">Payment Gateway</h3>
                <p class="feature-body">Integrasi langsung dengan bank tier-1 untuk settlement instan dan enkripsi Webhook verifikasi SHA512.</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon-wrapper">₿</div>
                <h3 class="feature-name">Crypto Collateral</h3>
                <p class="feature-body">Opsi pinjaman aset digital terenkripsi dengan smart contract dan pemantauan LTV real-time Oracle.</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon-wrapper">🛡️</div>
                <h3 class="feature-name">Enterprise Security</h3>
                <p class="feature-body">Arsitektur zero-trust dengan enkripsi AES-256, otentikasi Google 2FA, dan audit berkala.</p>
            </div>
        </div>

        <!-- Section 2: Technical Excellence Showcase (Stitch Tech Cards) -->
        <div class="tech-showcase-grid">
            <!-- Algorithmic Precision Card -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-wrapper" style="width: 40px; height: 40px; font-size: 18px;">⚡</div>
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
                    <div class="feature-icon-wrapper" style="width: 40px; height: 40px; font-size: 18px;">🔒</div>
                    <h3 class="feature-name" style="font-size: 20px;">Fortified Security</h3>
                    <p class="feature-body">Multi-signature consensus and real-time threat detection safeguard your capital at every layer.</p>
                </div>
                <div class="tech-card-badge">
                    ✓ Audited by Trail of Bits
                </div>
            </div>

            <!-- High Throughput Card -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-wrapper" style="width: 40px; height: 40px; font-size: 18px;">☁️</div>
                    <h3 class="feature-name" style="font-size: 20px;">High-Throughput</h3>
                    <p class="feature-body">Execute massive volume with sub-millisecond latency. Built for algorithmic trading systems and enterprise treasury management.</p>
                </div>
            </div>

            <!-- Seamless Integration Card with Code Snippet -->
            <div class="tech-card">
                <div>
                    <div class="feature-icon-wrapper" style="width: 40px; height: 40px; font-size: 18px;">🔌</div>
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

        <!-- Footer -->
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
            const themeIcon = document.getElementById('theme-icon');

            if (isLight) {
                htmlEl.classList.remove('dark');
                htmlEl.classList.add('light');
                logoHeader.src = lightLogo;
                logoFooter.src = lightLogo;
                themeIcon.textContent = '🌙';
                localStorage.setItem('lendflow_theme', 'light');
            } else {
                htmlEl.classList.remove('light');
                htmlEl.classList.add('dark');
                logoHeader.src = darkLogo;
                logoFooter.src = darkLogo;
                themeIcon.textContent = '☀️';
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
