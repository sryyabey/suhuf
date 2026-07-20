<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suhuf — {{ __('Tadabbur') }}</title>
    <meta name="description" content="{{ __('hero_desc') }}">
    <link rel="icon" type="image/webp" href="/logo.webp">

    {{-- Preconnect: DNS + TLS kurulumu önceden başlasın --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    {{-- Google Fonts: render-blocking değil, async yükle --}}
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400;0,600;1,400&display=swap"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400;0,600;1,400&display=swap">
    </noscript>

    {{-- Tabler Icons: render-blocking değil, async yükle (sürüm sabitlendi) --}}
    <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.0/tabler-icons.min.css"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.0/tabler-icons.min.css">
    </noscript>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --teal-dark: #1a6b5a;
            --teal-mid: #2d9b84;
            --teal-light: #e0f4f0;
            --gold: #B8860B;
            --gold-mid: #D4A843;
            --gold-light: #fdf8ee;
            --gold-pale: #fef9ec;
            --cream: #faf8f3;
            --cream2: #f3f0e8;
            --text-dark: #1a1a18;
            --text-mid: #4a4a45;
            --text-light: #8a8a82;
            --border: #e2ddd4;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Cairo', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* ── NAVBAR ─────────────────────────────────────────────── */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 48px;
            height: 64px;
            background: rgba(250, 248, 243, .94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-logo-text {
            font-size: 20px;
            font-weight: 800;
            color: var(--teal-dark);
            letter-spacing: -.3px;
        }

        .nav-logo-sub {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 400;
            margin-top: -2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
        }

        .nav-link-ghost {
            color: var(--text-mid);
        }

        .nav-link-ghost:hover {
            background: var(--cream2);
            color: var(--teal-dark);
        }

        .nav-link-solid {
            background: var(--teal-dark);
            color: #fff;
            border: 1.5px solid var(--teal-dark);
        }

        .nav-link-solid:hover {
            background: #145246;
            border-color: #145246;
        }

        .nav-lang {
            display: flex;
            align-items: center;
            gap: 2px;
            background: var(--cream2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 3px;
        }

        .nav-lang-btn {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 5px;
            text-decoration: none;
            transition: all .15s;
            color: var(--text-light);
        }

        .nav-lang-btn.active {
            background: var(--teal-dark);
            color: #fff;
        }

        .nav-lang-btn:not(.active):hover {
            color: var(--teal-dark);
        }

        /* ── HERO ────────────────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 64px;
            padding: 100px 72px 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 70% 60% at 20% 50%, rgba(26, 107, 90, .06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 20%, rgba(212, 168, 67, .05) 0%, transparent 50%),
                radial-gradient(ellipse 40% 30% at 80% 90%, rgba(26, 107, 90, .04) 0%, transparent 50%);
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: .03;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%231a6b5a'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-left {
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: var(--teal-light);
            border: 1px solid rgba(45, 155, 132, .25);
            font-size: 12px;
            font-weight: 700;
            color: var(--teal-dark);
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: clamp(40px, 5vw, 64px);
            font-weight: 800;
            line-height: 1.08;
            color: var(--text-dark);
            letter-spacing: -2px;
            margin-bottom: 8px;
        }

        .hero-title span {
            color: var(--teal-dark);
        }

        .hero-subtitle {
            font-family: 'Lora', Georgia, 'Times New Roman', serif;
            font-size: clamp(15px, 1.5vw, 18px);
            color: var(--text-mid);
            font-style: italic;
            margin-bottom: 20px;
        }

        .hero-desc {
            font-size: 15px;
            line-height: 1.8;
            color: var(--text-mid);
            margin-bottom: 36px;
            max-width: 460px;
        }

        .hero-cta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            border-radius: 11px;
            font-family: 'Cairo', system-ui, sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all .18s;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--teal-dark);
            color: #fff;
            border-color: var(--teal-dark);
        }

        .btn-primary:hover {
            background: #145246;
            border-color: #145246;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(26, 107, 90, .28);
        }

        .btn-outline {
            background: #fff;
            color: var(--teal-dark);
            border-color: var(--border);
        }

        .btn-outline:hover {
            border-color: var(--teal-mid);
            background: var(--teal-light);
            transform: translateY(-1px);
        }

        .hero-trust {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--text-light);
        }

        .hero-trust-dots {
            display: flex;
            gap: -4px;
        }

        .hero-trust-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal-dark), var(--teal-mid));
            border: 2px solid #fff;
            margin-left: -6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #fff;
            font-weight: 700;
        }

        .hero-trust-dot:first-child {
            margin-left: 0;
        }

        /* ── HERO MOCK UI ────────────────────────────────────────── */
        .hero-right {
            position: relative;
            z-index: 1;
        }

        .mock-browser {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, .14), 0 4px 16px rgba(0, 0, 0, .06);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .mock-browser-bar {
            background: var(--cream2);
            border-bottom: 1px solid var(--border);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mock-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .mock-dot-r {
            background: #fc5c65;
        }

        .mock-dot-y {
            background: #fed330;
        }

        .mock-dot-g {
            background: #26de81;
        }

        .mock-url {
            flex: 1;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 11px;
            color: var(--text-light);
            margin: 0 8px;
        }

        /* Mock mushaf page inside browser */
        .mock-mushaf {
            background: linear-gradient(180deg, #fdfaf3 0%, #f9f4e8 50%, #fdfaf3 100%);
            padding: 20px 24px;
            border: 4px solid transparent;
            border-image: linear-gradient(#fdfaf3, #f9f4e8) 1;
            position: relative;
        }

        .mock-mushaf-header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(184, 134, 11, .2);
            margin-bottom: 16px;
        }

        .mock-page-num {
            font-family: 'Amiri', Georgia, serif;
            font-size: 13px;
            color: #8a6522;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .mock-page-num::before,
        .mock-page-num::after {
            content: '❧';
            opacity: .5;
            color: #b89647;
        }

        .mock-page-range {
            font-size: 10px;
            color: var(--text-light);
            margin-top: 2px;
        }

        .mock-text {
            direction: rtl;
            font-family: 'Amiri', Georgia, serif;
            font-size: 18px;
            line-height: 2.5;
            color: #1a1208;
            text-align: justify;
        }

        .mock-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid rgba(184, 134, 11, .5);
            color: #8a6522;
            font-size: 9px;
            font-weight: 700;
            margin: 0 3px;
            vertical-align: middle;
            background: rgba(212, 168, 67, .1);
        }

        .mock-sura-card {
            text-align: center;
            margin: 14px 0;
            padding: 10px 16px;
            border: 1px solid rgba(184, 134, 11, .35);
            border-radius: 3px;
            background: linear-gradient(180deg, #fffaf0 0%, #f5ead5 100%);
        }

        .mock-sura-name {
            font-family: 'Amiri', Georgia, serif;
            font-size: 16px;
            color: #5a3e0e;
            display: block;
        }

        .mock-sura-stats {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-top: 6px;
        }

        .mock-sura-stat {
            text-align: center;
        }

        .mock-sura-stat-val {
            font-size: 13px;
            font-weight: 700;
            color: #5a3e0e;
            display: block;
            line-height: 1;
        }

        .mock-sura-stat-lbl {
            font-size: 9px;
            color: #a07830;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .mock-sura-stat-div {
            width: 1px;
            background: rgba(184, 134, 11, .25);
            align-self: center;
            height: 20px;
        }

        .mock-note-badge {
            display: inline-flex;
            vertical-align: super;
            background: #1a6b5a;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 999px;
            margin-left: 2px;
            line-height: 1.4;
            font-family: 'Cairo', system-ui, sans-serif;
        }

        .mock-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 12px 0 4px;
            border-top: 1px solid rgba(184, 134, 11, .18);
            margin-top: 14px;
        }

        .mock-nav-btn {
            font-size: 10px;
            font-family: 'Cairo', system-ui, sans-serif;
            color: #7a5a1e;
            border: 1px solid rgba(184, 134, 11, .35);
            border-radius: 4px;
            padding: 4px 10px;
            background: rgba(253, 250, 243, .8);
        }

        .mock-nav-num {
            font-family: 'Amiri', serif;
            font-size: 12px;
            color: #8a6522;
            border: 1px solid rgba(184, 134, 11, .25);
            border-radius: 4px;
            padding: 3px 10px;
        }

        /* Floating feature chips on mock */
        .mock-chip {
            position: absolute;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dark);
            white-space: nowrap;
        }

        .mock-chip-icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .mock-chip-icon.teal {
            background: var(--teal-light);
            color: var(--teal-dark);
        }

        .mock-chip-icon.gold {
            background: var(--gold-pale);
            color: var(--gold);
        }

        .mock-chip-1 {
            top: -14px;
            right: 30px;
        }

        .mock-chip-2 {
            bottom: 20px;
            left: -20px;
        }

        /* ── STATS ────────────────────────────────────────────────── */
        .stats {
            display: flex;
            justify-content: center;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: #fff;
        }

        .stat-item {
            flex: 1;
            max-width: 220px;
            text-align: center;
            padding: 32px 24px;
            border-right: 1px solid var(--border);
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-num {
            font-size: 32px;
            font-weight: 800;
            color: var(--teal-dark);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        /* ── READING EXPERIENCE SHOWCASE ─────────────────────────── */
        .showcase {
            padding: 96px 72px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .showcase-header {
            text-align: center;
            margin-bottom: 64px;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--teal-dark);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: clamp(26px, 3.5vw, 38px);
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -1px;
            margin-bottom: 14px;
            line-height: 1.15;
        }

        .section-desc {
            font-size: 15px;
            color: var(--text-mid);
            line-height: 1.8;
            max-width: 480px;
            margin: 0 auto;
        }

        .reading-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .rf-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            transition: all .2s;
        }

        .rf-card:hover {
            border-color: rgba(26, 107, 90, .3);
            box-shadow: 0 6px 24px rgba(26, 107, 90, .08);
            transform: translateY(-2px);
        }

        .rf-card.highlight {
            background: linear-gradient(135deg, var(--teal-dark) 0%, #145246 100%);
            border-color: transparent;
            color: #fff;
            grid-column: span 2;
            flex-direction: row;
            align-items: center;
            gap: 32px;
        }

        .rf-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .rf-icon.teal {
            background: var(--teal-light);
            color: var(--teal-dark);
        }

        .rf-icon.gold {
            background: var(--gold-pale);
            color: var(--gold);
        }

        .rf-icon.purple {
            background: #ede9fe;
            color: #6d28d9;
        }

        .rf-icon.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .rf-icon.white {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }

        .rf-body {
            flex: 1;
        }

        .rf-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .rf-desc {
            font-size: 13px;
            color: var(--text-mid);
            line-height: 1.7;
        }

        .rf-card.highlight .rf-title {
            color: #fff;
            font-size: 18px;
        }

        .rf-card.highlight .rf-desc {
            color: rgba(255, 255, 255, .75);
            font-size: 14px;
        }

        .rf-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            color: rgba(255, 255, 255, .9);
            border: 1px solid rgba(255, 255, 255, .2);
            margin-top: 8px;
        }

        /* ── FEATURES GRID ────────────────────────────────────────── */
        .features-section {
            background: #fff;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .features-inner {
            padding: 80px 72px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 48px;
        }

        .feature-card {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            transition: all .2s;
        }

        .feature-card:hover {
            border-color: rgba(45, 155, 132, .3);
            box-shadow: 0 6px 24px rgba(45, 155, 132, .08);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
        }

        .feature-icon.teal {
            background: var(--teal-light);
            color: var(--teal-dark);
        }

        .feature-icon.gold {
            background: var(--gold-pale);
            color: var(--gold);
        }

        .feature-icon.purple {
            background: #ede9fe;
            color: #6d28d9;
        }

        .feature-icon.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .feature-icon.rose {
            background: #fff1f2;
            color: #e11d48;
        }

        .feature-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .feature-desc {
            font-size: 13px;
            color: var(--text-mid);
            line-height: 1.7;
        }

        /* ── HOW IT WORKS ─────────────────────────────────────────── */
        .how-bg {
            border-top: 1px solid var(--border);
        }

        .how-inner {
            padding: 80px 72px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .steps {
            display: flex;
            gap: 0;
            margin-top: 48px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 27px;
            left: 28px;
            right: 28px;
            height: 2px;
            background: linear-gradient(90deg, var(--teal-dark), var(--teal-mid));
            opacity: .18;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 0 20px;
        }

        .step-num {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal-dark), var(--teal-mid));
            color: #fff;
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 4px 16px rgba(26, 107, 90, .3);
        }

        .step-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 7px;
        }

        .step-desc {
            font-size: 13px;
            color: var(--text-mid);
            line-height: 1.7;
        }

        /* ── CTA BANNER ──────────────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, var(--teal-dark) 0%, #0f4a3d 100%);
            padding: 80px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
        }

        .cta-arabic {
            font-family: 'Amiri', Georgia, serif;
            font-size: clamp(20px, 3vw, 28px);
            color: rgba(255, 255, 255, .5);
            direction: rtl;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .cta-title {
            font-size: clamp(28px, 4vw, 44px);
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 14px;
            line-height: 1.15;
            position: relative;
        }

        .cta-desc {
            font-size: 16px;
            color: rgba(255, 255, 255, .75);
            line-height: 1.8;
            max-width: 460px;
            margin: 0 auto 36px;
        }

        .cta-btns {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-white {
            background: #fff;
            color: var(--teal-dark);
            border-color: #fff;
        }

        .btn-white:hover {
            background: var(--cream);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
        }

        .btn-ghost-white {
            background: transparent;
            color: #fff;
            border-color: rgba(255, 255, 255, .35);
        }

        .btn-ghost-white:hover {
            background: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .6);
        }

        /* ── FOOTER ──────────────────────────────────────────────── */
        .footer {
            background: var(--text-dark);
            color: rgba(255, 255, 255, .5);
            padding: 48px 72px 32px;
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            flex-wrap: wrap;
            margin-bottom: 32px;
        }

        .footer-brand {}

        .footer-logo {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .footer-tagline {
            font-family: 'Lora', Georgia, serif;
            font-style: italic;
            font-size: 13px;
        }

        .footer-links-group {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-links-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: rgba(255, 255, 255, .35);
            margin-bottom: 4px;
        }

        .footer-link {
            color: rgba(255, 255, 255, .5);
            text-decoration: none;
            font-size: 13px;
            transition: color .15s;
        }

        .footer-link:hover {
            color: rgba(255, 255, 255, .9);
        }

        .footer-bottom {
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding-top: 24px;
            text-align: center;
        }

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 100px 40px 60px;
                gap: 48px;
                text-align: center;
            }

            .hero-desc {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-cta {
                justify-content: center;
            }

            .hero-trust {
                justify-content: center;
            }

            .hero-right {
                max-width: 540px;
                margin: 0 auto;
            }

            .showcase {
                padding: 72px 40px;
            }

            .features-inner {
                padding: 72px 40px;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .how-inner {
                padding: 72px 40px;
            }

            .footer {
                padding: 48px 40px 32px;
            }

            .mock-chip-1 {
                display: none;
            }

            .mock-chip-2 {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .nav {
                padding: 0 20px;
            }

            .nav-link-ghost {
                display: none;
            }

            .hero {
                padding: 90px 20px 48px;
            }

            .showcase {
                padding: 56px 20px;
            }

            .reading-features {
                grid-template-columns: 1fr;
            }

            .rf-card.highlight {
                grid-column: span 1;
                flex-direction: column;
            }

            .features-inner {
                padding: 56px 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .how-inner {
                padding: 56px 20px;
            }

            .steps {
                flex-direction: column;
                gap: 28px;
            }

            .steps::before {
                display: none;
            }

            .stats {
                flex-wrap: wrap;
            }

            .stat-item {
                min-width: 50%;
                border-bottom: 1px solid var(--border);
            }

            .cta-section {
                padding: 60px 20px;
            }

            .footer {
                padding: 40px 20px 24px;
            }

            .footer-inner {
                flex-direction: column;
                gap: 28px;
            }

            .footer-links-group {
                gap: 24px;
            }
        }

        @media (max-width: 480px) {
            .hero-cta {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }

            .cta-btns {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="nav">
        <a href="{{ route('home') }}" class="nav-logo">
            <img src="/logo.webp" alt="Suhuf"
                style="width:38px;height:38px;border-radius:10px;object-fit:cover;display:block;flex-shrink:0;">
            <div>
                <div class="nav-logo-text">Suhuf</div>
                <div class="nav-logo-sub"
                    style="font-family:'Amiri',Georgia,serif;font-style:normal;letter-spacing:0;font-size:12px;direction:rtl;">
                    أَفَلَا يَتَدَبَّرُونَ الْقُرْآنَ</div>
            </div>
        </a>
        <div class="nav-links">
            <a href="#features" class="nav-link nav-link-ghost">{{ __('Features') }}</a>
            <a href="#how-it-works" class="nav-link nav-link-ghost">{{ __('How It Works?') }}</a>
            <div class="nav-lang">
                <a href="{{ route('locale.switch', 'tr') }}"
                    class="nav-lang-btn {{ app()->getLocale() === 'tr' ? 'active' : '' }}">TR</a>
                <a href="{{ route('locale.switch', 'en') }}"
                    class="nav-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            </div>
            <a href="{{ route('login') }}" class="nav-link nav-link-ghost">{{ __('Sign In') }}</a>
            <a href="{{ route('register') }}" class="nav-link nav-link-solid">{{ __('Start for Free') }}</a>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-pattern"></div>

        <div class="hero-left">
            <div class="hero-badge">
                <i class="ti ti-sparkles" style="font-size:13px;"></i>
                {{ __('Quran Study Platform') }}
            </div>
            <h1 class="hero-title">{!! __('hero_title') !!}</h1>
            <p class="hero-subtitle">{{ __('hero_subtitle') }}</p>
            <p class="hero-desc">{{ __('hero_desc') }}</p>

            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="ti ti-user-plus"></i> {{ __('Create Free Account') }}
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="ti ti-login"></i> {{ __('Sign In') }}
                </a>
            </div>

            <div class="hero-trust">
                <div class="hero-trust-dots">
                    <div class="hero-trust-dot">أ</div>
                    <div class="hero-trust-dot">ب</div>
                    <div class="hero-trust-dot">ت</div>
                </div>
                <span>{{ __('Quran text & word-by-word analysis') }}</span>
            </div>
        </div>

        {{-- Mock Mushaf UI --}}
        <div class="hero-right">
            <div style="position:relative;">
                <div class="mock-chip mock-chip-1">
                    <div class="mock-chip-icon teal"><i class="ti ti-maximize"></i></div>
                    {{ __('Fullscreen mode') }}
                </div>
                <div class="mock-chip mock-chip-2">
                    <div class="mock-chip-icon gold"><i class="ti ti-bookmark"></i></div>
                    {{ __('Bookmarks') }}
                </div>

                <div class="mock-browser">
                    <div class="mock-browser-bar">
                        <span class="mock-dot mock-dot-r"></span>
                        <span class="mock-dot mock-dot-y"></span>
                        <span class="mock-dot mock-dot-g"></span>
                        <span class="mock-url">suhuf.live/quran-reading</span>
                    </div>
                    <div class="mock-mushaf">
                        <div class="mock-mushaf-header">
                            <div class="mock-page-num">{{ __('page_ref', ['number' => 534]) }}</div>
                            <div class="mock-page-range">El-Vakıa 56:1 — El-Vakıa 56:40</div>
                        </div>

                        <div class="mock-sura-card">
                            <span class="mock-sura-name">الواقعة</span>
                            <div
                                style="font-size:10px; color:#a07830; margin:2px 0 6px; font-family:'Cairo',sans-serif;">
                                56. El-Vakıa</div>
                            <div class="mock-sura-stats">
                                <div class="mock-sura-stat">
                                    <span class="mock-sura-stat-val">96</span>
                                    <span class="mock-sura-stat-lbl">{{ __('Verse') }}</span>
                                </div>
                                <div class="mock-sura-stat-div"></div>
                                <div class="mock-sura-stat">
                                    <span class="mock-sura-stat-val">379</span>
                                    <span class="mock-sura-stat-lbl">{{ __('Word') }}</span>
                                </div>
                                <div class="mock-sura-stat-div"></div>
                                <div class="mock-sura-stat">
                                    <span class="mock-sura-stat-val">1.709</span>
                                    <span class="mock-sura-stat-lbl">{{ __('Letter') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mock-text">
                            إِذَا وَقَعَتِ الْوَاقِعَةُ <span class="mock-marker">١</span>
                            لَيْسَ لِوَقْعَتِهَا كَاذِبَةٌ <span class="mock-marker">٢</span>
                            <span class="mock-note-badge"><i class="ti ti-notes" style="font-size:7px;"></i> 2</span>
                            خَافِضَةٌ رَّافِعَةٌ <span class="mock-marker">٣</span>
                            إِذَا رُجَّتِ الْأَرْضُ رَجًّا <span class="mock-marker">٤</span>
                        </div>

                        <div class="mock-nav">
                            <span class="mock-nav-btn">→ {{ __('Next Page') }}</span>
                            <span class="mock-nav-num">534</span>
                            <span class="mock-nav-btn">{{ __('Previous Page') }} ←</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- STATS --}}
    <div class="stats">
        <div class="stat-item">
            <div class="stat-num">114</div>
            <div class="stat-label">{{ __('Sura') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">6.236</div>
            <div class="stat-label">{{ __('Verse') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">77.430</div>
            <div class="stat-label">{{ __('Word Analysis') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">604</div>
            <div class="stat-label">{{ __('Page') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">∞</div>
            <div class="stat-label">{{ __('Personal Note') }}</div>
        </div>
    </div>

    {{-- READING EXPERIENCE SHOWCASE --}}
    <div id="features">
        <div class="showcase">
            <div class="showcase-header">
                <div class="section-label"><i class="ti ti-book-2" style="font-size:13px;"></i>
                    {{ __('Quran Reading') }}</div>
                <h2 class="section-title">{{ __('Authentic Mushaf Experience') }}</h2>
                <p class="section-desc">{{ __('reading_showcase_desc') }}</p>
            </div>

            <div class="reading-features">
                {{-- Highlight card --}}
                <div class="rf-card highlight">
                    <div class="rf-icon white" style="width:56px;height:56px;font-size:26px;border-radius:14px;">
                        <i class="ti ti-book-2"></i>
                    </div>
                    <div class="rf-body">
                        <div class="rf-title">{{ __('Flowing Mushaf Layout') }}</div>
                        <div class="rf-desc">{{ __('mushaf_layout_desc') }}</div>
                        <span class="rf-tag"><i class="ti ti-sparkles" style="font-size:10px;"></i>
                            {{ __('New Design') }}</span>
                    </div>
                </div>

                <div class="rf-card">
                    <div class="rf-icon gold"><i class="ti ti-info-circle"></i></div>
                    <div class="rf-body">
                        <div class="rf-title">{{ __('Sura Info Card') }}</div>
                        <div class="rf-desc">{{ __('sura_info_desc') }}</div>
                    </div>
                </div>

                <div class="rf-card">
                    <div class="rf-icon teal"><i class="ti ti-search"></i></div>
                    <div class="rf-body">
                        <div class="rf-title">{{ __('Searchable Sura Selector') }}</div>
                        <div class="rf-desc">{{ __('sura_search_desc') }}</div>
                    </div>
                </div>

                <div class="rf-card">
                    <div class="rf-icon purple"><i class="ti ti-bookmark"></i></div>
                    <div class="rf-body">
                        <div class="rf-title">{{ __('Page Bookmarks') }}</div>
                        <div class="rf-desc">{{ __('bookmark_desc') }}</div>
                    </div>
                </div>

                <div class="rf-card">
                    <div class="rf-icon blue"><i class="ti ti-maximize"></i></div>
                    <div class="rf-body">
                        <div class="rf-title">{{ __('Fullscreen Reading') }}</div>
                        <div class="rf-desc">{{ __('fullscreen_desc') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALL FEATURES --}}
    <div class="features-section">
        <div class="features-inner">
            <div class="section-label"><i class="ti ti-layout-grid" style="font-size:13px;"></i> {{ __('Features') }}
            </div>
            <h2 class="section-title">{{ __('Everything you need in one place') }}</h2>
            <p class="section-desc" style="max-width:440px;">{{ __('features_desc') }}</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon teal"><i class="ti ti-book"></i></div>
                    <div class="feature-title">{{ __('Quran Text & Translation') }}</div>
                    <div class="feature-desc">{{ __('feature_quran_text_desc') }}</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gold"><i class="ti ti-letters"></i></div>
                    <div class="feature-title">{{ __('Word by Word Analysis') }}</div>
                    <div class="feature-desc">{{ __('feature_word_analysis_desc') }}</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple"><i class="ti ti-notes"></i></div>
                    <div class="feature-title">{{ __('Personal Note System') }}</div>
                    <div class="feature-desc">{{ __('feature_notes_desc') }}</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon blue"><i class="ti ti-share"></i></div>
                    <div class="feature-title">{{ __('Note Sharing') }}</div>
                    <div class="feature-desc">{{ __('feature_sharing_desc') }}</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon teal"><i class="ti ti-chart-bar"></i></div>
                    <div class="feature-title">{{ __('Study Statistics') }}</div>
                    <div class="feature-desc">{{ __('feature_statistics_desc') }}</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon rose"><i class="ti ti-search"></i></div>
                    <div class="feature-title">{{ __('Global Search') }}</div>
                    <div class="feature-desc">{{ __('feature_search_desc') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- HOW IT WORKS --}}
    <div id="how-it-works" class="how-bg">
        <div class="how-inner">
            <div class="section-label"><i class="ti ti-route" style="font-size:13px;"></i> {{ __('How It Works?') }}
            </div>
            <h2 class="section-title">{{ __('Start in 3 steps') }}</h2>
            <p class="section-desc" style="max-width:400px;">{{ __('how_desc') }}</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-title">{{ __('step1_title') }}</div>
                    <div class="step-desc">{{ __('step1_desc') }}</div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-title">{{ __('step2_title') }}</div>
                    <div class="step-desc">{{ __('step2_desc') }}</div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-title">{{ __('step3_title') }}</div>
                    <div class="step-desc">{{ __('step3_desc') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <section class="cta-section">
        <p class="cta-arabic">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
        <h2 class="cta-title">{{ __('cta_title') }}</h2>
        <p class="cta-desc">{{ __('cta_desc') }}</p>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn btn-white">
                <i class="ti ti-user-plus"></i> {{ __('Create Free Account') }}
            </a>
            <a href="{{ route('login') }}" class="btn btn-ghost-white">
                <i class="ti ti-login"></i> {{ __('Sign In') }}
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">Suhuf</div>
                <div class="footer-tagline">{{ __('footer_tagline') }}</div>
            </div>
            <div class="footer-links-group">
                <div class="footer-links">
                    <div class="footer-links-title">{{ __('Platform') }}</div>
                    <a href="{{ route('register') }}" class="footer-link">{{ __('Register') }}</a>
                    <a href="{{ route('login') }}" class="footer-link">{{ __('Sign In') }}</a>
                </div>
                <div class="footer-links">
                    <div class="footer-links-title">{{ __('Discover') }}</div>
                    <a href="#features" class="footer-link">{{ __('Features') }}</a>
                    <a href="#how-it-works" class="footer-link">{{ __('How It Works?') }}</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            © {{ date('Y') }} Suhuf · {{ __('All rights reserved.') }}
        </div>
    </footer>

</body>

</html>