<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Masuk' }} — Sistem Monitoring Anggrek</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=boska@400,500&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500&family=Geist+Mono:wght@400&display=swap">
    <link rel="stylesheet" href="/css/landing.css">
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body class="auth">
<div class="auth-grid">
    <aside class="auth-aside">
        <a class="wordmark" href="/">Sistem Monitoring Anggrek</a>
        <p class="auth-claim">Siram ketika media tanam kering.</p>
        <img class="auth-orchid" src="/images/anggrek.png" alt="" aria-hidden="true" decoding="async" width="2000" height="2000">
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            {{ $slot }}
        </div>
        <a class="auth-back" href="/">Kembali ke halaman utama</a>
    </main>
</div>

<script>
document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(btn.dataset.togglePassword);
        var shown = input.type === 'text';
        input.type = shown ? 'password' : 'text';
        btn.textContent = shown ? 'Tampilkan' : 'Sembunyikan';
        btn.setAttribute('aria-pressed', String(!shown));
    });
});
</script>
</body>
</html>
