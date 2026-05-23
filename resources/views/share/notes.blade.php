<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $share->title ?: "Kur'an Not Paylaşımı" }} — Tadabbur</title>
    <meta name="description"
        content="{{ $share->user->name ?? 'Bir araştırmacı' }} tarafından paylaşılan Kur'an araştırma notları.">
    <meta property="og:title" content="{{ $share->title ?: "Kur'an Not Paylaşımı" }}">
    <meta property="og:description"
        content="{{ $share->user->name ?? 'Bir araştırmacı' }} tarafından paylaşılan Kur'an araştırma notları · Tadabbur">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Cairo:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        /* ── Reset & tokens ─────────────────────────────────────────────── */
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
            --gold-light: #F5E6C8;
            --gold-pale: #fdf8ee;
            --cream: #faf7f0;
            --cream2: #f3ede0;
            --sand: #e8dfc8;
            --text-dark: #2c2416;
            --text-mid: #5a4a2e;
            --text-light: #8a7a60;
            --border: rgba(184, 134, 11, .15);
            --border-mid: rgba(184, 134, 11, .25);
            --border-strong: rgba(184, 134, 11, .35);
            --shadow-sm: 0 1px 4px rgba(44, 36, 22, .07);
            --shadow-md: 0 4px 16px rgba(44, 36, 22, .1);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--cream);
            color: var(--text-dark);
            font-family: 'Lora', Georgia, serif;
            font-size: 15px;
            line-height: 1.7;
            min-height: 100vh;
        }

        /* ── Top Banner ─────────────────────────────────────────────────── */
        .topbanner {
            background: linear-gradient(135deg, #1a6b5a 0%, #0f4a3d 100%);
            position: relative;
            overflow: hidden;
            padding: 0 1.5rem;
        }

        .topbanner::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 360px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.05)' stroke-width='1'%3E%3Crect x='15' y='15' width='70' height='70' rx='2' transform='rotate(45 50 50)'/%3E%3Crect x='27' y='27' width='46' height='46' rx='2' transform='rotate(45 50 50)'/%3E%3Ccircle cx='50' cy='50' r='22'/%3E%3Ccircle cx='50' cy='50' r='12'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .topbanner-inner {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 14px 0;
            position: relative;
            z-index: 1;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-emblem {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--gold-mid), #a07010);
            border: 1px solid rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-emblem svg {
            width: 19px;
            height: 19px;
        }

        .brand-name {
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            letter-spacing: .2px;
        }

        .brand-sub {
            font-family: 'Cairo', sans-serif;
            font-size: 9.5px;
            color: rgba(255, 255, 255, .45);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .share-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Cairo', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, .75);
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 999px;
            padding: 5px 13px;
            letter-spacing: .3px;
        }

        .share-badge i {
            font-size: 13px;
            color: var(--gold-mid);
        }

        /* ── Hero ───────────────────────────────────────────────────────── */
        .hero {
            background: linear-gradient(180deg, #0f4a3d 0%, var(--cream) 100%);
            padding: 2.5rem 1.5rem 0;
        }

        .hero-inner {
            max-width: 860px;
            margin: 0 auto;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-mid);
        }

        .hero-eyebrow {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1rem;
        }

        .hero-eyebrow-chip {
            font-family: 'Cairo', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--gold-mid);
            background: rgba(212, 168, 67, .12);
            border: 1px solid rgba(212, 168, 67, .3);
            border-radius: 999px;
            padding: 3px 10px;
        }

        .hero-title {
            font-family: 'Cairo', sans-serif;
            font-size: clamp(22px, 4vw, 32px);
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.25;
            margin-bottom: 1.1rem;
        }

        .hero-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            color: var(--text-light);
        }

        .meta-item i {
            font-size: 14px;
            color: var(--teal-mid);
        }

        .meta-item strong {
            color: var(--text-mid);
            font-weight: 600;
        }

        .expiry-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'Cairo', sans-serif;
            font-size: 11px;
            color: #92400e;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 999px;
            padding: 2px 9px;
        }

        @if(!empty($payload['range']))
            .range-pill {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                font-family: 'Cairo', sans-serif;
                font-size: 12px;
                color: var(--teal-dark);
                background: var(--teal-light);
                border: 1px solid rgba(45, 155, 132, .25);
                border-radius: 10px;
                padding: 5px 12px;
                margin-top: .75rem;
            }

        @endif

        /* ── Stats bar ──────────────────────────────────────────────────── */
        .stats-bar {
            max-width: 860px;
            margin: 0 auto;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .stat-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            border: 1px solid var(--border-mid);
            border-radius: 10px;
            padding: 7px 14px;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            color: var(--text-mid);
            box-shadow: var(--shadow-sm);
        }

        .stat-pill i {
            font-size: 15px;
            color: var(--teal-mid);
        }

        .stat-pill strong {
            color: var(--teal-dark);
            font-weight: 700;
        }

        /* ── Main content ───────────────────────────────────────────────── */
        .content {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 1.5rem 4rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* ── Sura block ─────────────────────────────────────────────────── */
        .sura-block {
            background: #fff;
            border: 1px solid var(--border-mid);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .sura-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 1.1rem 1.5rem;
            background: linear-gradient(135deg, var(--teal-dark) 0%, #0f4a3d 100%);
            position: relative;
            overflow: hidden;
        }

        .sura-header::after {
            content: '';
            position: absolute;
            right: -10px;
            top: -10px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, .04);
            border-radius: 50%;
        }

        .sura-num {
            width: 42px;
            height: 42px;
            background: rgba(212, 168, 67, .2);
            border: 1px solid rgba(212, 168, 67, .35);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--gold-mid);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .sura-title-group {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .sura-name {
            font-family: 'Cairo', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .sura-subtitle {
            font-family: 'Cairo', sans-serif;
            font-size: 11.5px;
            color: rgba(255, 255, 255, .5);
            margin-top: 1px;
        }

        .sura-count-chip {
            font-family: 'Cairo', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, .7);
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 999px;
            padding: 3px 10px;
            position: relative;
            z-index: 1;
            white-space: nowrap;
        }

        .sura-body {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* ── Aya block ──────────────────────────────────────────────────── */
        .aya-block {
            border-top: 1px solid var(--border);
            padding: 1.4rem 1.5rem;
        }

        .aya-block:first-child {
            border-top: none;
        }

        .aya-ref-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .9rem;
        }

        .aya-ref {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Cairo', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: var(--teal-dark);
            background: var(--teal-light);
            border: 1px solid rgba(45, 155, 132, .2);
            border-radius: 999px;
            padding: 3px 11px;
        }

        .aya-ref i {
            font-size: 12px;
        }

        .aya-note-count {
            font-family: 'Cairo', sans-serif;
            font-size: 11px;
            color: var(--text-light);
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 26px;
            line-height: 2.15;
            direction: rtl;
            text-align: right;
            color: var(--text-dark);
            background: var(--gold-pale);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.1rem;
        }

        /* ── Note card ──────────────────────────────────────────────────── */
        .notes-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .note-card {
            border-radius: 12px;
            border: 1px solid var(--border-mid);
            overflow: hidden;
            background: #fff;
            box-shadow: var(--shadow-sm);
        }

        .note-card-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: .65rem 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--cream);
        }

        .note-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-family: 'Cairo', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            border-radius: 999px;
            padding: 2px 9px;
        }

        .note-type-badge.note {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .note-type-badge.footnote {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .note-type-badge.research {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .note-title {
            font-family: 'Cairo', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-dark);
            flex: 1;
        }

        .note-card-body {
            padding: 1rem;
        }

        .word-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--teal-light);
            border: 1px solid rgba(45, 155, 132, .25);
            border-radius: 10px;
            padding: 7px 12px;
            margin-bottom: .85rem;
        }

        .word-chip-label {
            font-family: 'Cairo', sans-serif;
            font-size: 10.5px;
            color: var(--text-light);
            white-space: nowrap;
        }

        .word-chip-arabic {
            font-family: 'Amiri', serif;
            font-size: 22px;
            color: var(--teal-dark);
            direction: rtl;
            line-height: 1;
        }

        .note-content {
            font-size: 14.5px;
            line-height: 1.75;
            color: var(--text-dark);
            white-space: pre-wrap;
        }

        .note-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: .75rem;
            padding-top: .75rem;
            border-top: 1px solid var(--border);
        }

        .note-tag {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-family: 'Cairo', sans-serif;
            font-size: 11.5px;
            color: var(--teal-dark);
            background: var(--teal-light);
            border: 1px solid rgba(45, 155, 132, .25);
            border-radius: 999px;
            padding: 2px 10px;
        }

        /* ── Footer ─────────────────────────────────────────────────────── */
        .footer {
            background: #fff;
            border-top: 1px solid var(--border-mid);
            padding: 1.5rem;
            text-align: center;
        }

        .footer-inner {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
        }

        .footer-logo {
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            color: var(--text-light);
        }

        .footer-logo strong {
            color: var(--teal-dark);
        }

        .footer-ayah {
            font-family: 'Amiri', serif;
            font-size: 17px;
            color: var(--text-light);
            direction: rtl;
            margin-top: 4px;
        }

        .footer-ayah-tr {
            font-size: 12px;
            color: var(--text-light);
            font-style: italic;
        }

        /* ── Watermark separator ────────────────────────────────────────── */
        .ornament {
            text-align: center;
            color: var(--gold-mid);
            font-size: 20px;
            letter-spacing: 8px;
            opacity: .45;
            margin: .25rem 0;
            user-select: none;
        }

        /* ── Print ──────────────────────────────────────────────────────── */
        @media print {

            .topbanner,
            .share-badge,
            .stats-bar {
                display: none;
            }

            body {
                background: #fff;
                font-size: 12px;
            }

            .sura-header {
                background: #1a6b5a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .hero {
                background: #fff;
                padding-top: 0;
            }

            .sura-block {
                box-shadow: none;
                border: 1px solid #ccc;
                break-inside: avoid;
            }

            .arabic-text {
                background: #fafafa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 640px) {
            .hero-meta-row {
                gap: .75rem;
            }

            .topbanner-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: .5rem;
            }

            .sura-header {
                flex-wrap: wrap;
            }

            .sura-count-chip {
                order: 3;
            }

            .arabic-text {
                font-size: 22px;
            }

            .content {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
</head>

<body>

    {{-- ── Top banner ─────────────────────────────────────────────────── --}}
    <div class="topbanner">
        <div class="topbanner-inner">
            <a href="#" class="brand">
                <div class="brand-emblem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polygon
                            points="12,2.5 14.6,6.7 19.5,4.5 17.3,9.4 21.5,12 17.3,14.6 19.5,19.5 14.6,17.3 12,21.5 9.4,17.3 4.5,19.5 6.7,14.6 2.5,12 6.7,9.4 4.5,4.5 9.4,6.7" />
                    </svg>
                </div>
                <div>
                    <div class="brand-name">Suhuf</div>
                    <div class="brand-sub">Tadabbur</div>
                </div>
            </a>
            <span class="share-badge">
                <i class="ti ti-share"></i> Paylaşılan Araştırma Notları
            </span>
        </div>
    </div>

    {{-- ── Hero ───────────────────────────────────────────────────────── --}}
    <div class="hero">
        <div class="hero-inner">
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-chip"><i class="ti ti-notes"
                        style="font-size:10px;margin-right:3px;"></i>Araştırma Notları</span>
            </div>

            <h1 class="hero-title">{{ $share->title ?: "Kur'an Not Paylaşımı" }}</h1>

            <div class="hero-meta-row">
                <div class="meta-item">
                    <i class="ti ti-user-circle"></i>
                    <span><strong>{{ $share->user->name ?? 'Araştırmacı' }}</strong> tarafından paylaşıldı</span>
                </div>
                <div class="meta-item">
                    <i class="ti ti-calendar"></i>
                    <span>{{ $share->created_at->locale('tr')->isoFormat('D MMMM Y') }}</span>
                </div>
                @if($share->expires_at)
                    <span class="expiry-chip">
                        <i class="ti ti-clock" style="font-size:11px;"></i>
                        {{ $share->expires_at->locale('tr')->isoFormat('D MMMM Y') }}'e kadar geçerli
                    </span>
                @endif
            </div>

            @if(!empty($payload['range']))
                <div style="margin-top:.9rem;">
                    <span class="range-pill"
                        style="display:inline-flex;align-items:center;gap:7px;font-family:'Cairo',sans-serif;font-size:12px;color:var(--teal-dark);background:var(--teal-light);border:1px solid rgba(45,155,132,.25);border-radius:10px;padding:5px 12px;">
                        <i class="ti ti-list" style="font-size:13px;"></i>
                        Aralık: <strong>{{ $payload['range']['start'] ?? '' }}</strong>
                        <i class="ti ti-arrow-right" style="font-size:11px;opacity:.5;"></i>
                        <strong>{{ $payload['range']['end'] ?? '' }}</strong>
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Stats bar ───────────────────────────────────────────────────── --}}
    @php
        $totalSuras = count($payload['grouped'] ?? []);
        $totalAyas = collect($payload['grouped'] ?? [])->sum(fn($s) => count($s['ayas'] ?? []));
        $totalNotes = collect($payload['grouped'] ?? [])
            ->flatMap(fn($s) => collect($s['ayas'] ?? [])->flatMap(fn($a) => $a['notes'] ?? []))
            ->count();
      @endphp
    <div class="stats-bar">
        <div class="stat-pill">
            <i class="ti ti-book-2"></i>
            <strong>{{ $totalSuras }}</strong> Sure
        </div>
        <div class="stat-pill">
            <i class="ti ti-list-numbers"></i>
            <strong>{{ $totalAyas }}</strong> Ayet
        </div>
        <div class="stat-pill">
            <i class="ti ti-notes"></i>
            <strong>{{ $totalNotes }}</strong> Not
        </div>
    </div>

    {{-- ── Sura blocks ─────────────────────────────────────────────────── --}}
    <div class="content">
        @foreach(($payload['grouped'] ?? []) as $sura => $suraData)
            @php
                $suraAyaCount = count($suraData['ayas'] ?? []);
                $suraNoteCount = collect($suraData['ayas'] ?? [])
                    ->sum(fn($a) => count($a['notes'] ?? []));
              @endphp

            <div class="sura-block">

                {{-- Sure başlığı --}}
                <div class="sura-header">
                    <div class="sura-num">{{ $sura }}</div>
                    <div class="sura-title-group">
                        <div class="sura-name">{{ $suraData['name'] ?? ('Sure ' . $sura) }}</div>
                        <div class="sura-subtitle">{{ $suraAyaCount }} ayet · {{ $suraNoteCount }} not</div>
                    </div>
                    <span class="sura-count-chip">
                        <i class="ti ti-notes" style="font-size:11px;margin-right:3px;"></i>{{ $suraNoteCount }} not
                    </span>
                </div>

                {{-- Ayetler --}}
                <div class="sura-body">
                    @foreach(($suraData['ayas'] ?? []) as $aya => $ayaData)
                        <div class="aya-block">

                            {{-- Ayet ref + sayaç --}}
                            <div class="aya-ref-row">
                                <span class="aya-ref">
                                    <i class="ti ti-bookmark"></i>
                                    {{ $sura }}:{{ $aya }}
                                </span>
                                @php $noteCount = count($ayaData['notes'] ?? []); @endphp
                                @if($noteCount > 0)
                                    <span class="aya-note-count">{{ $noteCount }} not</span>
                                @endif
                            </div>

                            {{-- Arapça metin --}}
                            @if(!empty($ayaData['arabic']))
                                <div class="arabic-text">{{ $ayaData['arabic'] }}</div>
                            @endif

                            {{-- Notlar --}}
                            @if(!empty($ayaData['notes']))
                                <div class="notes-list">
                                    @foreach($ayaData['notes'] as $note)
                                        @php
                                            $type = $note['type'] ?? 'note';
                                            $typeLabels = ['note' => 'Not', 'footnote' => 'Dipnot', 'research' => 'Araştırma'];
                                            $typeIcons = ['note' => 'ti-notes', 'footnote' => 'ti-bookmark-filled', 'research' => 'ti-microscope'];
                                            $typeLabel = $note['type_label'] ?? ($typeLabels[$type] ?? 'Not');
                                            $typeIcon = $typeIcons[$type] ?? 'ti-notes';
                                          @endphp
                                        <div class="note-card">
                                            <div class="note-card-head">
                                                <span class="note-type-badge {{ $type }}">
                                                    <i class="ti {{ $typeIcon }}" style="font-size:10px;"></i>
                                                    {{ $typeLabel }}
                                                </span>
                                                @if(!empty($note['title']))
                                                    <span class="note-title">{{ $note['title'] }}</span>
                                                @endif
                                            </div>
                                            <div class="note-card-body">
                                                {{-- İlgili kelime --}}
                                                @if(!empty($note['word_text']))
                                                    <div class="word-chip">
                                                        <span class="word-chip-label"><i class="ti ti-cursor-text"
                                                                style="font-size:11px;margin-right:2px;"></i>İlgili kelime:</span>
                                                        <span class="word-chip-arabic">{{ $note['word_text'] }}</span>
                                                    </div>
                                                @endif

                                                {{-- Not içeriği --}}
                                                <div class="note-content">{{ $note['content'] }}</div>

                                                {{-- Etiketler --}}
                                                @if(!empty($note['tags']))
                                                    <div class="note-tags">
                                                        @foreach($note['tags'] as $tag)
                                                            <span class="note-tag"><i class="ti ti-tag"
                                                                    style="font-size:10px;"></i>{{ $tag }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>

            </div>

            {{-- Sureler arası süsleme --}}
            @if(!$loop->last)
                <div class="ornament">✦ ✦ ✦</div>
            @endif

        @endforeach
    </div>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-logo">
                Bu paylaşım <strong>Suhuf · Tadabbur</strong> aracılığıyla oluşturulmuştur.
            </div>
            <div class="footer-ayah">أَفَلَا يَتَدَبَّرُونَ الْقُرْآنَ</div>
            <div class="footer-ayah-tr">"Hâlâ Kur'an üzerinde düşünmüyorlar mı?" — Nisa 4:82</div>
        </div>
    </div>

</body>

</html>