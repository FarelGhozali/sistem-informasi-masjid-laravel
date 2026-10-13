<?php

namespace Database\Seeders;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna
        $admin = User::firstOrCreate(
            ['email' => 'admin@masjid.com'],
            [
                'name' => 'Super Admin Masjid',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara@masjid.com'],
            [
                'name' => 'Ustadz Usman (Bendahara)',
                'password' => Hash::make('password123'),
                'role' => 'bendahara',
            ]
        );

        $takmir = User::firstOrCreate(
            ['email' => 'takmir@masjid.com'],
            [
                'name' => 'Ustadz Farhan (Takmir Masjid)',
                'password' => Hash::make('password123'),
                'role' => 'takmir',
            ]
        );

        // 2. Kategori & Riwayat Keuangan — dua tahun buku, ratusan transaksi.
        //    Detailnya di KeuanganSeeder; dijalankan juga dengan
        //    `php artisan db:seed --class=KeuanganSeeder`.
        $this->call(KeuanganSeeder::class);

        // 3. Jadwal Kegiatan Realistis
        $kegiatanList = [
            [
                'nama_kegiatan' => 'Kajian Rutin Ahad Subuh: Menyelami Riyadhus Shalihin',
                'slug' => Str::slug('Kajian Rutin Ahad Subuh: Menyelami Riyadhus Shalihin'),
                'penceramah_atau_penanggung_jawab' => 'Ustadz Dr. Muhammad Zaki, M.A.',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => now()->addDays(2)->toDateString(),
                'waktu_mulai' => '05:00:00',
                'tanggal_selesai' => now()->addDays(2)->toDateString(),
                'waktu_selesai' => '06:30:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Kajian rutin setiap pekan ba\'da Subuh bersama Ustadz Dr. Muhammad Zaki membahas kitab Riyadhus Shalihin karya Imam An-Nawawi. Disediakan sarapan bersama setelah kajian.</p>',
            ],
            [
                'nama_kegiatan' => 'Tahsin & Belajar Membaca Al-Qur\'an Dewasa',
                'slug' => Str::slug('Tahsin & Belajar Membaca Al-Qur\'an Dewasa'),
                'penceramah_atau_penanggung_jawab' => 'Ustadz Ahmad Fauzan, Al-Hafidz',
                'lokasi' => 'Serambi Kanan Masjid',
                'tanggal_mulai' => now()->addDays(4)->toDateString(),
                'waktu_mulai' => '18:30:00',
                'tanggal_selesai' => now()->addDays(4)->toDateString(),
                'waktu_selesai' => '20:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Program perbaikan bacaan makhraj huruf dan hukum tajwid untuk jamaah bapak-bapak dan remaja putra. Terbuka untuk umum tanpa dipungut biaya.</p>',
            ],
            [
                'nama_kegiatan' => 'Kajian Fiqih Wanita & Parenting Islami',
                'slug' => Str::slug('Kajian Fiqih Wanita & Parenting Islami'),
                'penceramah_atau_penanggung_jawab' => 'Ustadzah Nurul Hidayah, Lc.',
                'lokasi' => 'Ruang Utama Lantai 2 (Area Akhwat)',
                'tanggal_mulai' => now()->addDays(6)->toDateString(),
                'waktu_mulai' => '09:00:00',
                'tanggal_selesai' => now()->addDays(6)->toDateString(),
                'waktu_selesai' => '11:30:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Kajian khusus muslimah mendiskusikan panduan fiqih wanita sehari-hari dan kiat mendidik anak di era digital menurut tuntunan Rasulullah SAW.</p>',
            ],
            [
                'nama_kegiatan' => 'Kerja Bakti Akbar & Bersih-bersih Masjid',
                'slug' => Str::slug('Kerja Bakti Akbar & Bersih-bersih Masjid'),
                'penceramah_atau_penanggung_jawab' => 'Divisi Pemeliharaan Sarana Takmir',
                'lokasi' => 'Seluruh Area Masjid & Tempat Wudhu',
                'tanggal_mulai' => now()->subDays(3)->toDateString(),
                'waktu_mulai' => '07:30:00',
                'tanggal_selesai' => now()->subDays(3)->toDateString(),
                'waktu_selesai' => '11:00:00',
                'status' => 'selesai',
                'deskripsi' => '<p>Gotong royong bersama warga sekitar membersihkan karpet masjid, area wudhu, halaman, dan drainase lingkungan masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Peringatan Nuzulul Qur\'an & Santunan Yatim',
                'slug' => Str::slug('Peringatan Nuzulul Qur\'an & Santunan Yatim'),
                'penceramah_atau_penanggung_jawab' => 'KH. Abdullah Munir',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => now()->subDays(20)->toDateString(),
                'waktu_mulai' => '19:30:00',
                'tanggal_selesai' => now()->subDays(20)->toDateString(),
                'waktu_selesai' => '22:00:00',
                'status' => 'selesai',
                'deskripsi' => '<p>Peringatan malam Nuzulul Qur\'an dirangkaikan dengan penyaluran santunan untuk 50 anak yatim dhuafa di lingkungan sekitar masjid.</p>',
            ],
        ];

        foreach ($kegiatanList as $keg) {
            JadwalKegiatan::firstOrCreate(
                ['slug' => $keg['slug']],
                [
                    'user_id' => $takmir->id,
                    'nama_kegiatan' => $keg['nama_kegiatan'],
                    'penceramah_atau_penanggung_jawab' => $keg['penceramah_atau_penanggung_jawab'],
                    'lokasi' => $keg['lokasi'],
                    'tanggal_mulai' => $keg['tanggal_mulai'],
                    'waktu_mulai' => $keg['waktu_mulai'],
                    'tanggal_selesai' => $keg['tanggal_selesai'],
                    'waktu_selesai' => $keg['waktu_selesai'],
                    'status' => $keg['status'],
                    'deskripsi' => $keg['deskripsi'],
                ]
            );
        }
    }
}
