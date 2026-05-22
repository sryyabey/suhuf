<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', "Kur'an Araştırma Merkezi")</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Cairo:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  @livewireStyles

  @stack('head')

  <style>
    :root {
      --gold: #B8860B;
      --gold-light: #F5E6C8;
      --gold-mid: #D4A843;
      --gold-pale: #fdf8ee;
      --teal-dark: #1a6b5a;
      --teal-mid: #2d9b84;
      --teal-light: #e0f4f0;
      --cream: #faf7f0;
      --cream2: #f3ede0;
      --sand: #e8dfc8;
      --text-dark: #2c2416;
      --text-mid: #5a4a2e;
      --text-light: #8a7a60;
      --border: rgba(184,134,11,0.15);
      --border-strong: rgba(184,134,11,0.3);
      --sidebar-w: 230px;
      --topbar-h: 64px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      height: 100%;
      overflow: hidden;
    }

    body {
      background: var(--cream);
      font-family: 'Lora', Georgia, serif;
      color: var(--text-dark);
      font-size: 14px;
    }

    .app {
      display: grid;
      grid-template-columns: var(--sidebar-w) 1fr;
      grid-template-rows: var(--topbar-h) 1fr;
      height: 100vh;
    }

    .topbar {
      grid-column: 1 / -1;
      background: var(--teal-dark);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.5rem;
      position: relative;
      overflow: hidden;
      z-index: 10;
    }

    .topbar::after {
      content: '';
      position: absolute;
      right: 0; top: 0; bottom: 0;
      width: 400px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.055)' stroke-width='1'%3E%3Crect x='20' y='20' width='80' height='80' rx='2' transform='rotate(45 60 60)'/%3E%3Crect x='32' y='32' width='56' height='56' rx='2' transform='rotate(45 60 60)'/%3E%3Crect x='44' y='44' width='32' height='32' rx='2' transform='rotate(45 60 60)'/%3E%3Ccircle cx='60' cy='60' r='28'/%3E%3Ccircle cx='60' cy='60' r='16'/%3E%3Ccircle cx='60' cy='60' r='5'/%3E%3Cline x1='60' y1='0' x2='60' y2='120'/%3E%3Cline x1='0' y1='60' x2='120' y2='60'/%3E%3Cline x1='0' y1='0' x2='120' y2='120'/%3E%3Cline x1='120' y1='0' x2='0' y2='120'/%3E%3C/g%3E%3C/svg%3E");
      pointer-events: none;
    }

    .topbar-logo { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
    .logo-emblem { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--gold-mid), #a07010); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.15); }
    .logo-emblem svg { width: 22px; height: 22px; }
    .logo-text { font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 600; color: #fff; letter-spacing: 0.2px; }
    .logo-sub { font-family: 'Cairo', sans-serif; font-size: 10px; color: rgba(255,255,255,0.5); letter-spacing: 1.5px; text-transform: uppercase; }
    .topbar-right { display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1; }

    .search-box { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; padding: 7px 14px; width: 240px; }
    .search-box i { color: rgba(255,255,255,0.45); font-size: 15px; }
    .search-box input { background: none; border: none; outline: none; color: #fff; font-family: 'Cairo', sans-serif; font-size: 13px; width: 100%; }
    .search-box input::placeholder { color: rgba(255,255,255,0.35); }

    .topbar-icon-btn { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.65); font-size: 17px; }
    .notif-dot { position: relative; }
    .notif-dot::after { content: ''; position: absolute; top: 6px; right: 6px; width: 6px; height: 6px; background: var(--gold-mid); border-radius: 50%; border: 1.5px solid var(--teal-dark); }

    .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--gold-mid); color: #fff; font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.2); }

    .sidebar { background: #fff; border-right: 1px solid var(--border-strong); display: flex; flex-direction: column; overflow: hidden; position: relative; }
    .sidebar-pattern { position: absolute; bottom: 0; left: 0; right: 0; height: 180px; opacity: 0.07; pointer-events: none; }
    .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1.25rem 0 0.75rem; }
    .nav-section { margin-bottom: 0.5rem; }
    .nav-section-label { font-family: 'Cairo', sans-serif; font-size: 9.5px; font-weight: 600; color: var(--text-light); letter-spacing: 1.4px; text-transform: uppercase; padding: 0.5rem 1.25rem 0.3rem; }

    .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 1.25rem; font-family: 'Cairo', sans-serif; font-size: 13.5px; font-weight: 400; color: var(--text-mid); border-left: 3px solid transparent; transition: all 0.15s; user-select: none; text-decoration: none; }
    .nav-item:hover { background: var(--cream2); color: var(--teal-dark); border-left-color: var(--gold-mid); }
    .nav-item.active { background: var(--teal-light); color: var(--teal-dark); border-left-color: var(--teal-mid); font-weight: 600; }
    .nav-item i { font-size: 17px; flex-shrink: 0; }

    .sidebar-divider { height: 1px; background: var(--border); margin: 0.5rem 1.25rem; }
    .sidebar-footer { padding: 0.75rem 1.25rem 1rem; border-top: 1px solid var(--border); }
    .user-card { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 10px; background: var(--cream); border: 1px solid var(--border); }
    .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--teal-dark); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; font-family: 'Cairo', sans-serif; }
    .user-name { font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-dark); }
    .user-role { font-family: 'Cairo', sans-serif; font-size: 10px; color: var(--text-light); }

    .main { background: var(--cream); overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem; }

    @media (max-width: 1024px) {
      html, body { overflow: auto; }
      .app { grid-template-columns: 1fr; grid-template-rows: var(--topbar-h) auto auto; min-height: 100vh; height: auto; }
      .sidebar { order: 2; }
      .main { order: 3; }
      .search-box { width: 160px; }
    }
  </style>
</head>
<body>
@php
  $user = auth()->user();
  $name = $user?->name ?? 'Kullanıcı';
  $initials = collect(explode(' ', trim($name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
  $initials = mb_strtoupper($initials !== '' ? $initials : 'U');
@endphp
<div class="app">
  <header class="topbar">
    <div class="topbar-logo">
      <div class="logo-emblem">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="12,2.5 14.6,6.7 19.5,4.5 17.3,9.4 21.5,12 17.3,14.6 19.5,19.5 14.6,17.3 12,21.5 9.4,17.3 4.5,19.5 6.7,14.6 2.5,12 6.7,9.4 4.5,4.5 9.4,6.7"/>
        </svg>
      </div>
      <div>
        <div class="logo-text">Kur'an Araştırma Merkezi</div>
        <div class="logo-sub">Kuran &amp; Tefsir Platformu</div>
      </div>
    </div>
    <div class="topbar-right">
      @livewire('global-search')
      <div class="topbar-icon-btn notif-dot" title="Bildirimler"><i class="ti ti-bell" aria-hidden="true"></i></div>
      <a class="topbar-icon-btn" href="{{ route('user.settings') }}" title="Ayarlar" style="text-decoration:none;"><i class="ti ti-settings" aria-hidden="true"></i></a>
      <div class="avatar" title="Profil">{{ $initials }}</div>
    </div>
  </header>

  <nav class="sidebar" aria-label="Ana gezinti">
    <div class="sidebar-scroll">
      <div class="nav-section">
        <div class="nav-section-label">Ana Menu</div>
        <a class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="ti ti-layout-dashboard" aria-hidden="true"></i> Genel Bakis</a>
        <a class="nav-item {{ request()->routeIs('user.quran-read') ? 'active' : '' }}" href="{{ route('user.quran-read') }}"><i class="ti ti-book-2" aria-hidden="true"></i> Kur'an Okuma</a>
        <a class="nav-item {{ request()->routeIs('user.quran-text') ? 'active' : '' }}" href="{{ route('user.quran-text') }}"><i class="ti ti-book" aria-hidden="true"></i> Kur'an Metni</a>
        <a class="nav-item {{ request()->routeIs('user.quran-notes-range') ? 'active' : '' }}" href="{{ route('user.quran-notes-range') }}"><i class="ti ti-notebook" aria-hidden="true"></i> Not Araştırması</a>
        <a class="nav-item {{ request()->routeIs('user.settings') ? 'active' : '' }}" href="{{ route('user.settings') }}"><i class="ti ti-settings" aria-hidden="true"></i> Ayarlar</a>
      </div>
    </div>

    <div class="sidebar-footer">
      <div class="user-card">
        <div class="user-avatar">{{ $initials }}</div>
        <div>
          <div class="user-name">{{ $name }}</div>
          <div class="user-role">Arastirmaci · User</div>
        </div>
      </div>
    </div>

    <svg class="sidebar-pattern" viewBox="0 0 230 180" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <g fill="none" stroke="#1a6b5a" stroke-width="1">
        <polygon points="115,10 200,55 200,145 115,190 30,145 30,55"/>
        <polygon points="115,28 182,63 182,137 115,172 48,137 48,63"/>
        <polygon points="115,46 164,71 164,129 115,154 66,129 66,71"/>
        <circle cx="115" cy="100" r="38"/>
        <circle cx="115" cy="100" r="22"/>
        <circle cx="115" cy="100" r="8"/>
      </g>
    </svg>
  </nav>

  <main class="main">
    @yield('content')
  </main>
</div>

@stack('scripts')
@livewireScripts
<x-impersonate::banner style="auto" />
</body>
</html>
