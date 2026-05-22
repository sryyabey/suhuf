<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kayıt Ol</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f6f6f6; margin:0; }
    .wrap { max-width:420px; margin:48px auto; background:#fff; border:1px solid #ddd; border-radius:10px; padding:22px; }
    h1 { margin:0 0 16px; font-size:22px; }
    label { display:block; font-size:13px; margin:10px 0 6px; }
    input { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; font-size:14px; }
    button { margin-top:14px; width:100%; padding:10px; border:0; border-radius:8px; background:#1a6b5a; color:#fff; font-size:14px; cursor:pointer; }
    .err { background:#ffe9e9; border:1px solid #ffbdbd; color:#7a1f1f; padding:10px; border-radius:8px; margin-bottom:12px; font-size:13px; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Kayıt Ol</h1>

    @if ($errors->any())
      <div class="err">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
      @csrf
      <label for="name">Ad Soyad</label>
      <input id="name" name="name" type="text" value="{{ old('name') }}" required>

      <label for="email">E-posta</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required>

      <label for="password">Şifre</label>
      <input id="password" name="password" type="password" required>

      <label for="password_confirmation">Şifre Tekrar</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required>

      <button type="submit">Hesap Oluştur</button>
    </form>
  </div>
</body>
</html>
