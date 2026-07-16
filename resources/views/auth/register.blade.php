<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register') }} — Suhuf</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Amiri&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal-dark:  #2d9b84;
            --teal-mid:   #3ab89e;
            --teal-light: #e8f7f4;
            --cream:      #faf8f3;
            --cream2:     #f3f0e8;
            --text-dark:  #1a1a18;
            --text-mid:   #4a4a45;
            --text-light: #8a8a82;
            --border:     #e2ddd4;
            --border-focus: #2d9b84;
            --error-bg:   #fff2f2;
            --error-border: #fca5a5;
            --error-text: #991b1b;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 70% 50% at 30% 20%, rgba(45,155,132,.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 80%, rgba(196,152,42,.05) 0%, transparent 60%);
        }

        .page-wrap {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
        }

        /* Logo */
        .logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; justify-content: center;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: linear-gradient(135deg, var(--teal-dark), var(--teal-mid));
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            box-shadow: 0 4px 16px rgba(45,155,132,.3);
        }
        .logo-text { font-size: 24px; font-weight: 800; color: var(--teal-dark); letter-spacing: -.5px; }
        .logo-sub  { font-size: 12px; color: var(--text-light); display: block; margin-top: -2px; }

        /* Kart */
        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 4px 32px rgba(0,0,0,.06);
        }

        .card-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 28px;
        }
        .card-title {
            font-size: 22px; font-weight: 800;
            color: var(--text-dark); letter-spacing: -.4px;
            margin-bottom: 4px;
        }
        .card-sub {
            font-size: 14px; color: var(--text-light);
            line-height: 1.6;
        }

        /* Language switcher */
        .lang-switcher {
            display: flex; align-items: center; gap: 2px;
            background: var(--cream2); border: 1px solid var(--border);
            border-radius: 8px; padding: 3px; flex-shrink: 0;
        }
        .lang-btn {
            font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700;
            padding: 3px 8px; border-radius: 5px; text-decoration: none;
            transition: all .15s; color: var(--text-light);
        }
        .lang-btn.active { background: var(--teal-dark); color: #fff; }
        .lang-btn:not(.active):hover { color: var(--teal-dark); }

        /* Hata kutusu */
        .errors {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }
        .errors-title {
            font-size: 12px; font-weight: 700; color: var(--error-text);
            text-transform: uppercase; letter-spacing: .5px;
            display: flex; align-items: center; gap: 6px;
            margin-bottom: 6px;
        }
        .errors li {
            font-size: 13px; color: var(--error-text);
            list-style: none; padding: 2px 0;
            padding-left: 16px; position: relative;
        }
        .errors li::before {
            content: '·'; position: absolute; left: 4px; font-weight: 700;
        }

        /* Form elemanları */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 700; color: var(--text-mid);
            margin-bottom: 7px;
        }
        .form-label i { font-size: 14px; color: var(--text-light); }

        .input-wrap { position: relative; }
        .form-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(45,155,132,.12);
        }
        .form-input.is-error { border-color: var(--error-border); }
        .form-input::placeholder { color: var(--text-light); }

        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            font-size: 16px; color: var(--text-light); pointer-events: none;
        }

        .input-hint {
            font-size: 11px; color: var(--text-light);
            margin-top: 5px; padding-left: 2px;
        }

        /* Şifre göster/gizle */
        .input-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            font-size: 16px; color: var(--text-light);
            padding: 4px; transition: color .15s;
        }
        .input-toggle:hover { color: var(--teal-dark); }
        .form-input.has-toggle { padding-right: 40px; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, var(--teal-dark), var(--teal-mid));
            color: #fff; border: none; border-radius: 12px;
            font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all .18s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 8px;
            box-shadow: 0 4px 16px rgba(45,155,132,.25);
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(45,155,132,.35);
        }
        .btn-submit:active { transform: translateY(0); }

        /* Ayırıcı */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0; color: var(--text-light); font-size: 12px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* Giriş linki */
        .login-link {
            text-align: center; font-size: 14px; color: var(--text-mid);
        }
        .login-link a {
            color: var(--teal-dark); font-weight: 700; text-decoration: none;
        }
        .login-link a:hover { text-decoration: underline; }

        /* Gizlilik notu */
        .privacy-note {
            display: flex; align-items: flex-start; gap: 8px;
            margin-top: 20px; padding: 12px 14px;
            background: var(--teal-light); border-radius: 10px;
            font-size: 12px; color: var(--text-mid); line-height: 1.6;
        }
        .privacy-note i { color: var(--teal-dark); font-size: 15px; flex-shrink: 0; margin-top: 1px; }

        /* Ana sayfa linki */
        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 5px;
            margin-top: 24px; font-size: 13px; color: var(--text-light);
            text-decoration: none; transition: color .15s;
        }
        .back-link:hover { color: var(--teal-dark); }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- Logo -->
    <a href="{{ route('home') }}" class="logo">
        <div class="logo-icon"><i class="ti ti-book-2"></i></div>
        <div>
            <div class="logo-text">Suhuf</div>
            <span class="logo-sub">{{ __('Tadabbur') }}</span>
        </div>
    </a>

    <!-- Kart -->
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="card-title">{{ __('Create an account') }}</h1>
                <p class="card-sub">{{ __('Free, ad-free. Start your Quran study now.') }}</p>
            </div>
            <div class="lang-switcher">
                <a href="{{ route('locale.switch', 'tr') }}" class="lang-btn {{ app()->getLocale()==='tr' ? 'active' : '' }}">TR</a>
                <a href="{{ route('locale.switch', 'en') }}" class="lang-btn {{ app()->getLocale()==='en' ? 'active' : '' }}">EN</a>
            </div>
        </div>

        {{-- Hata mesajları --}}
        @if ($errors->any())
            <div class="errors">
                <div class="errors-title">
                    <i class="ti ti-alert-circle"></i>
                    {{ __('Please fix the following') }}
                </div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            {{-- Ad Soyad --}}
            <div class="form-group">
                <label class="form-label" for="name">
                    <i class="ti ti-user"></i> {{ __('Full Name') }}
                </label>
                <div class="input-wrap">
                    <i class="ti ti-user input-icon"></i>
                    <input
                        id="name" name="name" type="text"
                        class="form-input @error('name') is-error @enderror"
                        value="{{ old('name') }}"
                        placeholder="{{ __('Enter your name') }}"
                        autocomplete="name"
                        required
                    >
                </div>
            </div>

            {{-- E-posta --}}
            <div class="form-group">
                <label class="form-label" for="email">
                    <i class="ti ti-mail"></i> {{ __('Email') }}
                </label>
                <div class="input-wrap">
                    <i class="ti ti-mail input-icon"></i>
                    <input
                        id="email" name="email" type="email"
                        class="form-input @error('email') is-error @enderror"
                        value="{{ old('email') }}"
                        placeholder="{{ __('email_placeholder') }}"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="invite_code">
                    <i class="ti ti-ticket"></i> {{ __('Invitation Code') }}
                </label>
                <div class="input-wrap">
                    <i class="ti ti-ticket input-icon"></i>
                    <input
                        id="invite_code" name="invite_code" type="text"
                        class="form-input @error('invite_code') is-error @enderror"
                        value="{{ old('invite_code', $inviteCode ?? '') }}"
                        placeholder="{{ __('Enter your invitation code') }}"
                        autocomplete="off"
                        required
                    >
                </div>
                <div class="input-hint">{{ __('You need a valid invitation code from an existing user.') }}</div>
            </div>

            {{-- Şifre --}}
            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="ti ti-lock"></i> {{ __('Password') }}
                </label>
                <div class="input-wrap">
                    <i class="ti ti-lock input-icon"></i>
                    <input
                        id="password" name="password" type="password"
                        class="form-input has-toggle @error('password') is-error @enderror"
                        placeholder="{{ __('At least 8 characters') }}"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="input-toggle" onclick="togglePass('password', this)" tabindex="-1">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
                <div class="input-hint">{{ __('At least 8 characters') }}</div>
            </div>

            {{-- Şifre Tekrar --}}
            <div class="form-group">
                <label class="form-label" for="password_confirmation">
                    <i class="ti ti-lock-check"></i> {{ __('Confirm Password') }}
                </label>
                <div class="input-wrap">
                    <i class="ti ti-lock-check input-icon"></i>
                    <input
                        id="password_confirmation" name="password_confirmation" type="password"
                        class="form-input has-toggle"
                        placeholder="{{ __('Re-enter your password') }}"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="input-toggle" onclick="togglePass('password_confirmation', this)" tabindex="-1">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="ti ti-user-plus"></i>
                {{ __('Create Account') }}
            </button>
        </form>

        <div class="divider">{{ __('or') }}</div>

        <div class="login-link">
            {{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
        </div>

        <div class="privacy-note">
            <i class="ti ti-shield-check"></i>
            {{ __('privacy_note') }}
        </div>
    </div>

    <a href="{{ route('home') }}" class="back-link">
        <i class="ti ti-arrow-left" style="font-size:14px;"></i>
        {{ __('Back to home') }}
    </a>

</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ti ti-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'ti ti-eye';
    }
}
</script>

</body>
</html>
