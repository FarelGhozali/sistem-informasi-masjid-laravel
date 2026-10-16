<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        // 3. Agenda Kegiatan Masjid — kajian rutin, kerja sosial, dan acara
        //    tahunan dengan berbagai status. Detailnya di TakmirSeeder;
        //    dijalankan juga dengan `php artisan db:seed --class=TakmirSeeder`.
        $this->call(TakmirSeeder::class);
    }
}
