<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Monitoring Anggrek — Penyiraman Otomatis</title>
    <meta name="description" content="Sistem monitoring dan penyiraman otomatis tanaman anggrek: sensor mengukur suhu, kelembapan, cahaya, dan media tanam; pompa menyiram secara otomatis; seluruh pembacaan tercatat.">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=boska@400,500&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500&family=Geist+Mono:wght@400&display=swap">
    <link rel="stylesheet" href="/css/landing.css">
</head>
<body>
<header class="nav">
    <div class="container nav-in">
        <a class="wordmark" href="/">Sistem Monitoring Anggrek</a>
        <nav class="nav-links" aria-label="Utama">
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="#riwayat">Riwayat</a>
            <a class="nav-login" href="{{ route('login') }}">Masuk</a>
            <a class="btn btn-accent" href="{{ route('register') }}">Daftar</a>
        </nav>
    </div>
</header>

<main>
    <section class="hero container">
        <div class="grid12">
            <div class="hero-text">
                <h1>Sistem Monitoring Penyiraman Otomatis Tanaman Anggrek</h1>
                <p class="lead">Sensor mengukur kondisi area tanam anggrek, pompa menyiram ketika media tanam mengering, dan setiap pembacaan tercatat.</p>
                <div class="actions">
                    <a class="btn btn-accent" href="{{ route('register') }}">Daftar</a>
                    <a class="btn btn-ghost" href="{{ route('login') }}">Masuk</a>
                </div>
            </div>
            <figure class="hero-fact hero-photo">
                <img src="/images/anggrek.png" width="2000" height="2000" alt="Ilustrasi empat bunga anggrek Phalaenopsis pada satu tangkai." decoding="async">
            </figure>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>Parameter yang Dipantau</h2>
                <p class="lead">Enam parameter yang menentukan apakah anggrek dapat berbunga atau hanya bertahan hidup.</p>
            </div>
            <div class="rows">
                <div class="row">
                    <span class="row-idx">01</span>
                    <h3>Suhu dan Kelembapan Udara</h3>
                    <p>Diukur di sekitar tanaman, bukan di luar ruang tanam, agar angka mewakili udara yang benar-benar diterima anggrek.</p>
                </div>
                <div class="row">
                    <span class="row-idx">02</span>
                    <h3>Cahaya</h3>
                    <p>Intensitas cahaya dalam lux, untuk mengetahui apakah naungan masih memadai atau terlalu gelap.</p>
                </div>
                <div class="row">
                    <span class="row-idx">03</span>
                    <h3>Kelembapan Media Tanam</h3>
                    <p>Dasar keputusan penyiraman: pompa hanya dinyalakan apabila media tanam benar-benar kering.</p>
                </div>
                <div class="row">
                    <span class="row-idx">04</span>
                    <h3>pH Media</h3>
                    <p>Anggrek tidak toleran terhadap media yang terlalu masam, sehingga pH turut diukur pada setiap pembacaan.</p>
                </div>
                <div class="row">
                    <span class="row-idx">05</span>
                    <h3>Nitrogen, Fosfor, Kalium</h3>
                    <p>Kandungan hara media, yang menjadi penentu kapan pupuk perlu ditambahkan.</p>
                </div>
                <div class="row">
                    <span class="row-idx">06</span>
                    <h3>Status Pompa</h3>
                    <p>Kondisi menyala atau mati beserta waktu penyiraman terakhir, agar tidak terjadi penyiraman ganda.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="cara-kerja">
        <div class="container">
            <div class="section-head">
                <h2>Cara Kerja</h2>
                <p class="lead">Empat tahapan, dari pembacaan sensor hingga air dialirkan.</p>
            </div>
            <div class="rows">
                <div class="row">
                    <span class="row-idx">01</span>
                    <h3>Sensor Mengukur</h3>
                    <p>Perangkat ESP32 pada area tanam mengukur seluruh parameter secara berkala tanpa pemeriksaan manual.</p>
                </div>
                <div class="row">
                    <span class="row-idx">02</span>
                    <h3>Data Dikirim</h3>
                    <p>Hasil pengukuran dikirim ke server melalui jaringan sehingga dapat dipantau dari mana saja.</p>
                </div>
                <div class="row">
                    <span class="row-idx">03</span>
                    <h3>Dibandingkan dengan Batas</h3>
                    <p>Setiap jenis anggrek memiliki rentang ideal tersendiri; pembacaan di luar rentang langsung ditandai.</p>
                </div>
                <div class="row">
                    <span class="row-idx">04</span>
                    <h3>Pompa Menyiram</h3>
                    <p>Apabila media tanam kering, pompa menyala dalam durasi singkat lalu berhenti otomatis, dengan jeda agar media tidak tergenang.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="riwayat">
        <div class="container">
            <div class="section-head">
                <h2>Riwayat</h2>
                <p class="lead">Data yang telah lewat tetap tersimpan, sehingga pola musiman dapat diamati.</p>
            </div>
            <div class="rows">
                <div class="row">
                    <span class="row-idx">01</span>
                    <h3>Tersimpan Setiap Pembacaan</h3>
                    <p>Setiap pembacaan tersimpan lengkap dalam riwayat, bukan hanya nilai terakhir yang tampil di layar.</p>
                </div>
                <div class="row">
                    <span class="row-idx">02</span>
                    <h3>Dapat Dilihat per Tanggal</h3>
                    <p>Tanggal tertentu dapat dipilih untuk melihat kapan suhu meningkat, kapan pompa menyala, dan seberapa sering.</p>
                </div>
                <div class="row">
                    <span class="row-idx">03</span>
                    <h3>Menjadi Dasar Keputusan</h3>
                    <p>Peninjauan data sebelumnya membantu memutuskan pemindahan naungan, perubahan jadwal penyiraman, atau penggantian media.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="close">
        <div class="container close-in">
            <div>
                <h2>Mulai dari Satu Area Tanam</h2>
                <p>Buat akun, isi batas anggrek, dan hubungkan sensor.</p>
            </div>
            <div class="actions">
                <a class="btn btn-accent" href="{{ route('register') }}">Daftar</a>
                <a class="btn btn-ghost" href="{{ route('login') }}">Masuk</a>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container footer-in">
        <div class="colophon">
            <span class="wordmark">Sistem Monitoring Anggrek</span>
            <span>Monitoring dan penyiraman otomatis. ESP32, Firebase, Laravel.</span>
        </div>
        <nav class="footer-links" aria-label="Tautan">
            <a href="{{ route('register') }}">Daftar</a>
            <a href="{{ route('login') }}">Masuk</a>
            <a href="#riwayat">Riwayat</a>
        </nav>
    </div>
</footer>
</body>
</html>
