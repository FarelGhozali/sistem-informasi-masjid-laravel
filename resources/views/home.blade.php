@extends('layouts.app')

@section('title', 'Beranda - Sistem Informasi Masjid')

@section('header')
    <!-- Header -->
    <header id="header">
        <div class="logo">
            <span class="icon fa-mosque"></span>
        </div>
        <div class="content">
            <div class="inner">
                <h1>Sistem Informasi Masjid</h1>
                <p>Platform informasi masjid terintegrasi dan modern</p>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#intro">Intro</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#contact">Kontak</a></li>
            </ul>
        </nav>
    </header>
@endsection

@section('content')
    <!-- Intro -->
    <article id="intro">
        <h2 class="major">Selamat Datang</h2>
        <span class="image main"><img src="https://images.unsplash.com/photo-1587474260584-136574528ed5?w=1200&auto=format&fit=crop" alt="Masjid Agung" /></span>
        <p>Assalamu'alaikum Warahmatullahi Wabarakatuh. Selamat datang di Sistem Informasi Masjid, platform terintegrasi untuk mengelola dan menyebarkan informasi masjid dengan mudah dan efisien.</p>
        <p>Website ini dirancang untuk memudahkan pengurus masjid dan jamaah dalam mengakses jadwal shalat, kegiatan, pengumuman, dan informasi keuangan masjid secara transparan.</p>
    </article>

    <!-- About -->
    <article id="about">
        <h2 class="major">Tentang Kami</h2>
        <span class="image main"><img src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=1200&auto=format&fit=crop" alt="Arsitektur Masjid" /></span>
        <p>Sistem Informasi Masjid adalah platform digital yang dikembangkan untuk mendukung pengelolaan masjid di era modern. Kami percaya bahwa teknologi dapat menjadi alat yang powerful untuk memperkuat kehidupan keagamaan dan komunitas.</p>
        <p>Fitur utama kami meliputi:</p>
        <ul>
            <li>Jadwal Shalat Otomatis</li>
            <li>Manajemen Kegiatan & Acara</li>
            <li>Pengumuman & Berita Masjid</li>
            <li>Laporan Keuangan Transparan</li>
            <li>Data Jamaah & Donatur</li>
        </ul>
    </article>

    <!-- Contact -->
    <article id="contact">
        <h2 class="major">Kontak Kami</h2>
        <form method="post" action="#">
            @csrf
            <div class="fields">
                <div class="field half">
                    <label for="name">Nama</label>
                    <input type="text" name="name" id="name" />
                </div>
                <div class="field half">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" />
                </div>
                <div class="field">
                    <label for="message">Pesan</label>
                    <textarea name="message" id="message" rows="4"></textarea>
                </div>
            </div>
            <ul class="actions">
                <li><input type="submit" value="Kirim Pesan" class="primary" /></li>
                <li><input type="reset" value="Reset" /></li>
            </ul>
        </form>
        <ul class="icons">
            <li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
            <li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
            <li><a href="#" class="icon brands fa-youtube"><span class="label">YouTube</span></a></li>
            <li><a href="#" class="icon brands fa-whatsapp"><span class="label">WhatsApp</span></a></li>
        </ul>
    </article>
@endsection

@section('footer')
    <!-- Footer -->
    <footer id="footer">
        <p class="copyright">&copy; {{ date('Y') }} Sistem Informasi Masjid. Design: <a href="https://html5up.net">HTML5 UP</a>.</p>
    </footer>
@endsection