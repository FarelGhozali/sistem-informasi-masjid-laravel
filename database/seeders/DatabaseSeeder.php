<?php

namespace Database\Seeders;

use App\Models\JadwalKegiatan;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
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

        // 2. Kategori Keuangan
        $kategoriList = [
            // Pemasukan
            ['nama' => 'Kotak Infaq Jumat', 'jenis' => 'pemasukan', 'deskripsi' => 'Hasil perolehan infaq dan sedekah saat sholat Jumat'],
            ['nama' => 'Infaq Harian / Subuh', 'jenis' => 'pemasukan', 'deskripsi' => 'Kotak infaq harian dan pengajian'],
            ['nama' => 'Donasi Renovasi Masjid', 'jenis' => 'pemasukan', 'deskripsi' => 'Sumbangan khusus renovasi dan pembangunan sarana masjid'],
            ['nama' => 'Zakat, Infaq & Sedekah (ZIS)', 'jenis' => 'pemasukan', 'deskripsi' => 'Penerimaan dana zakat mal/fitrah dan fidyah'],
            ['nama' => 'Sponsor / Donatur Kegiatan', 'jenis' => 'pemasukan', 'deskripsi' => 'Dana sponsor kajian atau peringatan hari besar Islam'],

            // Pengeluaran
            ['nama' => 'Listrik, Air & Internet', 'jenis' => 'pengeluaran', 'deskripsi' => 'Tagihan rutin PLN, PDAM, dan WiFi masjid'],
            ['nama' => 'Honor Imam, Muadzin & Marbot', 'jenis' => 'pengeluaran', 'deskripsi' => 'Kompensasi bulanan petugas masjid'],
            ['nama' => 'Operasional Kajian & Tabligh Akbar', 'jenis' => 'pengeluaran', 'deskripsi' => 'Bisyarah pemateri, konsumsi jamaah, dan sound system'],
            ['nama' => 'Kebersihan & Pemeliharaan Sarana', 'jenis' => 'pengeluaran', 'deskripsi' => 'Peralatan kebersihan, perawatan AC, dan perbaikan sound'],
            ['nama' => 'Santunan Yatim & Dhuafa', 'jenis' => 'pengeluaran', 'deskripsi' => 'Penyaluran dana sosial bagi masyarakat sekitar'],
        ];

        $kategoriMap = [];
        foreach ($kategoriList as $item) {
            $cat = KategoriKeuangan::firstOrCreate(
                ['nama' => $item['nama']],
                [
                    'jenis' => $item['jenis'],
                    'deskripsi' => $item['deskripsi'],
                ]
            );
            $kategoriMap[$item['nama']] = $cat->id;
        }

        // 3. Transaksi Keuangan Realistis
        $transaksiList = [
            [
                'kategori' => 'Kotak Infaq Jumat',
                'jenis' => 'pemasukan',
                'judul' => 'Perolehan Infaq Jumat Pekan ke-1',
                'nominal' => 3850000,
                'tanggal' => now()->subDays(14)->toDateString(),
                'keterangan' => 'Dihitung bersama 2 orang saksi jamaah',
            ],
            [
                'kategori' => 'Donasi Renovasi Masjid',
                'jenis' => 'pemasukan',
                'judul' => 'Wakaf Karpet & Sound dari H. Sulaiman',
                'nominal' => 5000000,
                'tanggal' => now()->subDays(10)->toDateString(),
                'keterangan' => 'Transfer via rekening BSI Masjid',
            ],
            [
                'kategori' => 'Kotak Infaq Jumat',
                'jenis' => 'pemasukan',
                'judul' => 'Perolehan Infaq Jumat Pekan ke-2',
                'nominal' => 4120000,
                'tanggal' => now()->subDays(7)->toDateString(),
                'keterangan' => 'Total infaq sholat Jumat',
            ],
            [
                'kategori' => 'Infaq Harian / Subuh',
                'jenis' => 'pemasukan',
                'judul' => 'Infaq Kuliah Subuh Ahad Pagi',
                'nominal' => 950000,
                'tanggal' => now()->subDays(6)->toDateString(),
                'keterangan' => 'Kotak keliling kajian rutin ahad',
            ],
            [
                'kategori' => 'Listrik, Air & Internet',
                'jenis' => 'pengeluaran',
                'judul' => 'Pembayaran Tagihan Listrik PLN Bulan Ini',
                'nominal' => 1250000,
                'tanggal' => now()->subDays(8)->toDateString(),
                'keterangan' => 'Pemakaian AC dan penerangan masjid',
            ],
            [
                'kategori' => 'Kebersihan & Pemeliharaan Sarana',
                'jenis' => 'pengeluaran',
                'judul' => 'Service & Cuci AC 6 Unit',
                'nominal' => 750000,
                'tanggal' => now()->subDays(5)->toDateString(),
                'keterangan' => 'Service berkala teknisi AC',
            ],
            [
                'kategori' => 'Operasional Kajian & Tabligh Akbar',
                'jenis' => 'pengeluaran',
                'judul' => 'Bisyarah Ustadz & Snack Jamaah Kajian Ahad',
                'nominal' => 850000,
                'tanggal' => now()->subDays(4)->toDateString(),
                'keterangan' => 'Kajian tafsir tematik',
            ],
            [
                'kategori' => 'Honor Imam, Muadzin & Marbot',
                'jenis' => 'pengeluaran',
                'judul' => 'Honor Marbot dan Petugas Kebersihan',
                'nominal' => 2000000,
                'tanggal' => now()->subDays(2)->toDateString(),
                'keterangan' => 'Gaji marbot masjid bulan berjalan',
            ],
        ];

        foreach ($transaksiList as $trx) {
            TransaksiKeuangan::firstOrCreate(
                [
                    'judul' => $trx['judul'],
                    'tanggal' => $trx['tanggal'],
                ],
                [
                    'kategori_keuangan_id' => $kategoriMap[$trx['kategori']] ?? null,
                    'user_id' => $bendahara->id,
                    'jenis' => $trx['jenis'],
                    'nominal' => $trx['nominal'],
                    'keterangan' => $trx['keterangan'],
                ]
            );
        }

        // 4. Jadwal Kegiatan Realistis
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
