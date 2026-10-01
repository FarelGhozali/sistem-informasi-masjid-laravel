@extends('layouts.app')

@section('title', 'Beranda - Sistem Informasi Masjid')

@section('header_title', 'Sistem Informasi Masjid')
@section('header_subtitle', 'Platform informasi masjid terintegrasi dan modern')

@section('content')
    <!-- Intro -->
    <article id="intro">
        <h2 class="major">Selamat Datang</h2>
        <span class="image main"><img src="{{ asset('images/pic01.jpg') }}" alt="Masjid" /></span>
        <p>Assalamu'alaikum Warahmatullahi Wabarakatuh. Selamat datang di Sistem Informasi Masjid, platform terintegrasi untuk mengelola dan menyebarkan informasi masjid dengan mudah dan efisien.</p>
        <p>Website ini dirancang untuk memudahkan pengurus masjid dan jamaah dalam mengakses jadwal shalat, kegiatan, pengumuman, dan informasi keuangan masjid secara transparan.</p>
    </article>

    <!-- About -->
    <article id="about">
        <h2 class="major">Tentang Kami</h2>
        <span class="image main"><img src="{{ asset('images/pic03.jpg') }}" alt="Tentang Masjid" /></span>
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