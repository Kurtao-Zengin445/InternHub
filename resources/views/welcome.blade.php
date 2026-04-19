<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternHub - Sistem Manajemen Magang</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('internhub-favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        :root {
            --ink: #f8fafc;
            --white: #ffffff;
            --blue: #1a56db;
            --blue-dk: #1340a8;
            --amber: #f59e0b;
            --slate: #94a3b8;
            --slate-soft: #64748b;
            --border: rgba(148, 163, 184, .18);
            --bg-soft: rgba(15, 23, 42, .72);
            --bg-accent: rgba(37, 99, 235, .16);
            --surface: rgba(15, 23, 42, .82);
            --surface-strong: #0b1120;
            --surface-soft: rgba(255, 255, 255, .05);
            --font-display: 'Monserrat', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif
        }

        html {
            scroll-behavior: smooth
        }

        body {
            font-family: var(--font-body);
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, .22), transparent 28%),
                radial-gradient(circle at bottom left, rgba(245, 158, 11, .12), transparent 26%),
                #020617;
            overflow-x: hidden
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 14px 5vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(2, 6, 23, .76);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(148, 163, 184, .14);
            transition: box-shadow .3s
        }

        nav.scrolled {
            box-shadow: 0 12px 36px rgba(0, 0, 0, .28)
        }

        .nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-display);
            color: var(--ink);
            text-decoration: none;
        }

        .nav-brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .08);
            color: #dbeafe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-brand-mark svg {
            width: 22px;
            height: 22px;
        }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .nav-brand-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -.4px;
            color: var(--white);
        }

        .nav-brand-title span {
            color: var(--blue)
        }

        .nav-brand-subtitle {
            font-family: var(--font-body);
            font-size: 9px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: .02em;
            margin-top: 2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .nav-link-item {
            font-size: 14px;
            font-weight: 500;
            color: #cbd5e1;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all .15s
        }

        .nav-link-item:hover {
            color: var(--white);
            background: rgba(255, 255, 255, .06)
        }

        .btn-nav,
        .btn-nav-outline,
        .btn-primary-lg,
        .btn-ghost,
        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all .2s
        }

        .btn-nav {
            font-size: 14px;
            color: var(--white);
            background: var(--bg-soft);
            padding: 9px 20px
        }

        .btn-nav:hover,
        .btn-primary-lg:hover {
            background: var(--blue-dk);
            transform: translateY(-1px)
        }

        .btn-nav-outline {
            font-size: 14px;
            color: #dbeafe;
            border: 1px solid rgba(191, 219, 254, .28);
            padding: 9px 18px;
            background: var(--bg-soft);
        }

        .btn-nav-outline:hover {
            background: rgba(59, 130, 246, .16)
        }

        .hero {
            min-height: 84vh;
            display: flex;
            align-items: center;
            padding: 120px 5vw 80px;
            position: relative;
            overflow: hidden
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 70% 40%, rgba(26, 86, 219, .16) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(245, 158, 11, .10) 0%, transparent 70%);
            pointer-events: none
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%)
        }

        .hero-content {
            position: relative;
            max-width: 640px;
            animation: fadeUp .8s ease both
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 99, 235, .16);
            color: #93c5fd;
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 28px;
            border: 1px solid rgba(147, 197, 253, .24)
        }

        .hero-tag .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--blue);
            animation: pulse 2s ease infinite
        }

        h1 {
            font-family: var(--font-display);
            font-size: clamp(50px, 6vw, 75px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 24px
        }

        h1 .accent {
            color: var(--blue);
            position: relative;
            display: inline-block
        }

        h1 .accent::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--amber);
            opacity: .5;
            border-radius: 3px;
            z-index: -1
        }

        .hero-desc {
            font-size: 18px;
            color: #cbd5e1;
            line-height: 1.75;
            margin-bottom: 36px;
            max-width: 560px
        }

        .hero-cta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap
        }

        .btn-primary-lg {
            font-size: 15px;
            color: var(--white);
            background: var(--blue);
            padding: 13px 26px;
            box-shadow: 0 4px 14px rgba(26, 86, 219, .3)
        }

        .btn-ghost {
            font-size: 15px;
            color: var(--white);
            padding: 13px 26px;
            border: 1.5px solid rgba(148, 163, 184, .22);
            background: rgba(255, 255, 255, .04)
        }

        .btn-ghost:hover {
            border-color: rgba(148, 163, 184, .36);
            background: rgba(255, 255, 255, .08)
        }

        .hero-visual {
            position: absolute;
            right: 4vw;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 16px;
            animation: fadeLeft .9s ease both .2s
        }

        .float-card {
            background: rgba(15, 23, 42, .82);
            border: 1px solid rgba(148, 163, 184, .16);
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 16px 42px rgba(0, 0, 0, .24);
            min-width: 260px;
            display: flex;
            align-items: center;
            gap: 14px
        }

        .fc-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0
        }

        .fc-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--white)
        }

        .fc-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px
        }

        .fc-stat {
            margin-left: auto;
            font-size: 22px;
            font-weight: 800;
            font-family: var(--font-display);
            color: var(--blue)
        }

        .stats-bar {
            background: rgba(15, 23, 42, .94);
            padding: 36px 5vw;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 32px
        }

        .stat-item {
            text-align: center
        }

        .stat-number {
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 800;
            color: var(--white);
            line-height: 1
        }

        .stat-number span {
            color: var(--amber)
        }

        .stat-desc {
            font-size: 13px;
            color: var(--slate-soft);
            margin-top: 6px
        }

        .section,
        .roles-section,
        .flow-section,
        .cta-section {
            padding: 96px 5vw
        }

        .roles-section {
            background: rgba(15, 23, 42, .62)
        }

        .section-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: 12px
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(28px, 4vw, 44px);
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 16px
        }

        .section-desc {
            font-size: 16px;
            color: #cbd5e1;
            line-height: 1.7;
            max-width: 560px;
            margin-bottom: 56px
        }

        .features-grid,
        .roles-grid {
            display: grid;
            gap: 20px
        }

        .features-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))
        }

        .roles-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            margin-top: 48px
        }

        .feature-card,
        .role-card {
            background: rgba(15, 23, 42, .78);
            border: 1.5px solid rgba(148, 163, 184, .16);
            border-radius: 18px;
            transition: all .25s
        }

        .feature-card {
            padding: 28px;
            position: relative;
            overflow: hidden
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--blue);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s ease
        }

        .feature-card:hover,
        .role-card:hover {
            border-color: rgba(147, 197, 253, .28);
            box-shadow: 0 18px 46px rgba(15, 23, 42, .28);
            transform: translateY(-4px)
        }

        .feature-card:hover::before {
            transform: scaleX(1)
        }

        .feature-icon,
        .role-emoji {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 18px
        }

        .feature-icon {
            display: flex
        }

        .role-card {
            padding: 28px 24px;
            text-align: center
        }

        .role-emoji {
            background: rgba(37, 99, 235, .16);
            color: #93c5fd
        }

        .feature-title,
        .role-name,
        .step-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--white)
        }

        .feature-title,
        .role-name {
            margin-bottom: 8px
        }

        .feature-desc,
        .role-desc,
        .step-desc {
            font-size: 13.5px;
            color: #cbd5e1;
            line-height: 1.65
        }

        .flow-steps {
            display: flex;
            gap: 0;
            margin-top: 56px;
            position: relative
        }

        .flow-steps::before {
            content: '';
            position: absolute;
            top: 28px;
            left: calc(28px + 5%);
            right: calc(28px + 5%);
            height: 2px;
            background: linear-gradient(90deg, var(--blue), var(--amber));
            z-index: 0
        }

        .flow-step {
            flex: 1;
            text-align: center;
            padding: 0 12px;
            position: relative;
            z-index: 1
        }

        .step-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--blue);
            color: var(--white);
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 14px rgba(26, 86, 219, .35)
        }

        .step-title {
            margin-bottom: 6px
        }

        .cta-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            text-align: center;
            position: relative;
            overflow: hidden
        }

        .cta-section::before,
        .cta-section::after {
            content: '';
            position: absolute;
            border-radius: 50%
        }

        .cta-section::before {
            top: -60px;
            right: -60px;
            width: 300px;
            height: 300px;
            background: rgba(245, 158, 11, .08)
        }

        .cta-section::after {
            bottom: -80px;
            left: -80px;
            width: 400px;
            height: 400px;
            background: rgba(26, 86, 219, .15)
        }

        .cta-title {
            font-family: var(--font-display);
            font-size: clamp(28px, 4vw, 48px);
            font-weight: 800;
            color: var(--white);
            letter-spacing: -1px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1
        }

        .cta-desc {
            font-size: 16px;
            color: var(--slate-soft);
            margin-bottom: 36px;
            position: relative;
            z-index: 1
        }

        .cta-btn {
            font-size: 15px;
            font-weight: 700;
            background: var(--amber);
            color: var(--ink);
            padding: 14px 30px;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 20px rgba(245, 158, 11, .4)
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            background: #fbbf24;
            box-shadow: 0 8px 28px rgba(245, 158, 11, .5)
        }

        footer {
            background: rgba(2, 6, 23, .96);
            color: #94a3b8;
            text-align: center;
            padding: 28px 5vw;
            font-size: 13px
        }

        footer .brand {
            color: var(--white);
            font-weight: 600
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes fadeLeft {
            from {
                opacity: 0;
                transform: translate(30px, -50%)
            }

            to {
                opacity: 1;
                transform: translate(0, -50%)
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-8px)
            }
        }

        .float-card:nth-child(1) {
            animation: floatY 4s ease-in-out infinite
        }

        .float-card:nth-child(2) {
            animation: floatY 4s ease-in-out infinite .8s
        }

        .float-card:nth-child(3) {
            animation: floatY 4s ease-in-out infinite 1.6s
        }

        #menuToggle {
            display: none;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer
        }

        @media (max-width:900px) {
            .hero-visual {
                display: none
            }

            .flow-steps {
                flex-direction: column;
                gap: 32px
            }

            .flow-steps::before {
                display: none
            }
        }

        @media (max-width:768px) {
            nav {
                padding: 14px 20px
            }

            .hero {
                padding: 90px 20px 50px;
                text-align: center
            }

            .hero-content {
                max-width: 100%
            }

            h1 {
                font-size: 34px;
                letter-spacing: -1px
            }

            .hero-desc {
                font-size: 15px;
                margin: 0 auto 24px
            }

            .hero-cta {
                justify-content: center
            }

            .stats-bar {
                flex-direction: column;
                gap: 20px
            }

            .section,
            .roles-section,
            .flow-section,
            .cta-section {
                padding: 70px 20px
            }

            .section-title {
                font-size: 26px
            }

            .flow-step {
                text-align: left
            }

            .step-circle {
                margin: 0 0 10px
            }

            #menuToggle {
                display: block;
                margin-left: auto
            }

            .nav-links {
                display: none;
            }

            .nav-links.active {
                display: flex;
                position: absolute;
                top: 70px;
                right: 20px;
                flex-direction: column;
                background: rgba(6, 21, 85, 0.96);
                padding: 15px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
                width: 220px
            }
        }
    </style>
</head>

<body>
    <nav id="navbar">
        <a href="/" class="nav-brand">
            <span class="nav-brand-mark">
                <x-application-logo class="text-blue-700" />
            </span>
            <span class="nav-brand-text">
                <span class="nav-brand-title">Intern<span>Hub</span></span>
                <span class="nav-brand-subtitle">Sistem Manajemen Magang</span>
            </span>
        </a>
        <button id="menuToggle" aria-label="Buka menu">
            <i class="bi bi-list" style="color: white;"></i>
        </button>
        <div class="nav-links">
            <a href="#fitur" class="nav-link-item">Fitur</a>
            <a href="#alur" class="nav-link-item">Alur</a>
            <a href="#pengguna" class="nav-link-item">Pengguna</a>
            <a href="{{ route('register') }}" class="btn-nav-outline">
                <i class="bi bi-person-plus"></i> Daftar
            </a>
            <a href="{{ route('login') }}" class="btn-nav">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>

        <div class="hero-content">
            <div class="hero-tag">
                <span class="dot"></span>
                Sistem Manajemen Magang Digital
            </div>

            <h1>
                Kelola Magang
                Lebih <span class="accent">Cerdas</span>
                dan Terstruktur
            </h1>

            <p class="hero-desc">
                InternHub adalah aplikasi berbasis web yang dirancang
                untuk membantu sekolah dalam mengelola program magang siswa secara digital.
                Sistem ini mengintegrasikan seluruh proses mulai dari pendaftaran magang,
                pencatatan kegiatan harian, hingga evaluasi siswa dalam satu platform.
                Dengan adanya sistem ini, proses administrasi menjadi lebih cepat,
                        transparan, dan efisien dibandingkan metode manual yang menggunakan kertas.
            </p>

            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn-primary-lg">
                    <i class="bi bi-person-plus-fill"></i>
                    Daftar Akun
                </a>
                <a href="{{ route('login') }}" class="btn-primary-lg">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                    Masuk ke Sistem
                </a>
                <a href="#fitur" class="btn-ghost">
                    Lihat Fitur <i class="bi bi-arrow-down"></i>
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="float-card">
                <div class="fc-icon" style="background:#eff6ff;color:#1a56db">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fc-title">Siswa Terdaftar</div>
                    <div class="fc-sub">Peserta aktif saat ini</div>
                </div>
                <div class="fc-stat">248</div>
            </div>

            <div class="float-card">
                <div class="fc-icon" style="background:#fef3c7;color:#92400e">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="fc-title">Laporan Diverifikasi</div>
                    <div class="fc-sub">Pembaruan hari ini</div>
                </div>
                <div class="fc-stat" style="color:#f59e0b">34</div>
            </div>

            <div class="float-card">
                <div class="fc-icon" style="background:#d1fae5;color:#065f46">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <div class="fc-title">Program Selesai</div>
                    <div class="fc-sub">Rekap bulan ini</div>
                </div>
                <div class="fc-stat" style="color:#10b981">19</div>
            </div>
        </div>
    </section>

    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-number">50<span>+</span></div>
            <div class="stat-desc">Sekolah Mitra</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">120<span>+</span></div>
            <div class="stat-desc">Perusahaan Partner</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">1.2<span>K+</span></div>
            <div class="stat-desc">Siswa Magang</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">98<span>%</span></div>
            <div class="stat-desc">Tingkat Kepuasan</div>
        </div>
    </div>

    <section class="section" id="fitur">
        <div class="section-label">Fitur Unggulan</div>
        <div class="section-title">Semua yang Dibutuhkan, Dalam Satu Platform</div>
        <p class="section-desc">
            Dirancang khusus untuk kebutuhan program magang SMK, mulai dari proses seleksi,
            pelaporan harian, verifikasi dokumen, hingga penilaian akhir.
        </p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background:#eff6ff;color:#1a56db">
                    <i class="bi bi-send-fill"></i>
                </div>
                <div class="feature-title">Pendaftaran Digital</div>
                <p class="feature-desc">
                    Siswa melamar program magang secara online, sementara perusahaan dapat menyeleksi peserta dengan lebih cepat.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:#fef3c7;color:#92400e">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="feature-title">Monitoring Harian</div>
                <p class="feature-desc">
                    Aktivitas harian siswa tercatat dengan rapi dan dapat diverifikasi langsung oleh pembimbing.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:#d1fae5;color:#065f46">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div class="feature-title">Presensi Digital</div>
                <p class="feature-desc">
                    Kehadiran check-in dan check-out terdokumentasi dengan baik untuk memudahkan pemantauan.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:#ede9fe;color:#4c1d95">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div class="feature-title">Penilaian Terstruktur</div>
                <p class="feature-desc">
                    Evaluasi dari sekolah dan perusahaan dihitung otomatis dengan bobot yang sudah ditentukan.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:#fee2e2;color:#991b1b">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="feature-title">Manajemen Dokumen</div>
                <p class="feature-desc">
                    Semua dokumen penting, mulai dari surat pengantar sampai sertifikat, tersimpan dalam alur verifikasi yang jelas.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:#fef9c3;color:#854d0e">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div class="feature-title">Notifikasi Otomatis</div>
                <p class="feature-desc">
                    Pengingat dan pembaruan penting dikirim otomatis agar tidak ada proses yang terlewat.
                </p>
            </div>
        </div>
    </section>

    <section class="flow-section" id="alur">
        <div class="section-label">Alur Penggunaan</div>
        <div class="section-title">Dari Pendaftaran hingga Sertifikat</div>

        <div class="flow-steps">
            <div class="flow-step">
                <div class="step-circle">1</div>
                <div class="step-title">Daftar dan Login</div>
                <p class="step-desc">Akun dibuat dan pengguna masuk ke sistem sesuai perannya.</p>
            </div>
            <div class="flow-step">
                <div class="step-circle">2</div>
                <div class="step-title">Program Dibuka</div>
                <p class="step-desc">Perusahaan membuka lowongan magang beserta kuota dan persyaratannya.</p>
            </div>
            <div class="flow-step">
                <div class="step-circle">3</div>
                <div class="step-title">Seleksi Berjalan</div>
                <p class="step-desc">Siswa melamar, lalu perusahaan melakukan seleksi peserta magang.</p>
            </div>
            <div class="flow-step">
                <div class="step-circle">4</div>
                <div class="step-title">Magang Dipantau</div>
                <p class="step-desc">Presensi, laporan harian, dan dokumen dipantau selama kegiatan berlangsung.</p>
            </div>
            <div class="flow-step">
                <div class="step-circle">5</div>
                <div class="step-title">Evaluasi Akhir</div>
                <p class="step-desc">Nilai akhir, verifikasi dokumen, dan penerbitan sertifikat dikelola dalam satu alur.</p>
            </div>
        </div>
    </section>

    <section class="roles-section" id="pengguna">
        <div class="section-label">Untuk Siapa</div>
        <div class="section-title">Dirancang untuk Semua Pemangku Kepentingan</div>

        <div class="roles-grid">
            <div class="role-card">
                <span class="role-emoji"><i class="bi bi-mortarboard-fill"></i></span>
                <div class="role-name">Siswa</div>
                <p class="role-desc">Melamar magang, mengisi laporan harian, mencatat presensi, dan melihat evaluasi.</p>
            </div>
            <div class="role-card">
                <span class="role-emoji"><i class="bi bi-building"></i></span>
                <div class="role-name">Sekolah</div>
                <p class="role-desc">Memantau perkembangan siswa dan menjaga koordinasi dengan perusahaan mitra.</p>
            </div>
            <div class="role-card">
                <span class="role-emoji"><i class="bi bi-person-badge-fill"></i></span>
                <div class="role-name">Pembimbing</div>
                <p class="role-desc">Memverifikasi laporan, memberikan umpan balik, dan melakukan penilaian akhir.</p>
            </div>
            <div class="role-card">
                <span class="role-emoji"><i class="bi bi-briefcase-fill"></i></span>
                <div class="role-name">Perusahaan</div>
                <p class="role-desc">Membuka program, menyeleksi pelamar, dan memantau performa peserta magang.</p>
            </div>
            <!-- <div class="role-card">
                <span class="role-emoji"><i class="bi bi-gear-fill"></i></span>
                <div class="role-name">Admin</div>
                <p class="role-desc">Mengelola pengguna, data sekolah, perusahaan, serta konfigurasi sistem secara menyeluruh.</p>
            </div> -->
        </div>
    </section>

    <section class="cta-section">
        <h2 class="cta-title">Siap Menggunakan InternHub?</h2>
        <p class="cta-desc">Daftar sebagai siswa atau masuk ke sistem jika Anda sudah memiliki akun.</p>
        <div style="position:relative;z-index:1;display:flex;justify-content:center;gap:12px;flex-wrap:wrap">
            <a href="{{ route('register') }}" class="cta-btn">
                <i class="bi bi-person-plus-fill"></i>
                Daftar Sekarang
            </a>
            <a href="{{ route('login') }}" class="btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.25);background:rgba(255,255,255,.06)">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk ke Sistem
            </a>
        </div>
    </section>

    <footer>
        <span class="brand">InternHub</span> -
        Sistem Manajemen Magang &copy; {{ date('Y') }}
    </footer>

    <script>
        window.addEventListener('scroll', () => {
            document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 20);
        });

        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', (event) => {
                event.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        document.getElementById('menuToggle').addEventListener('click', () => {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>

</html>
