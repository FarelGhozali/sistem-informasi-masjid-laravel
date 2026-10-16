<?php

namespace Database\Seeders;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TakmirSeeder extends Seeder
{
    /**
     * The agenda the /jadwal/ page is built to show: routine kajian, one-off
     * social work, an event running today, one that was called off, and work
     * that has already happened — so every status badge, the date grouping,
     * and the detail pages all have something real to render.
     *
     * Dates are relative to the day the seeder runs, which keeps the demo
     * looking current on any machine. Existing slugs are left untouched
     * (`firstOrCreate`), so a re-seed never overwrites an event the takmir
     * has edited in the panel.
     *
     * Days are counted in Jakarta time, not the server's timezone: the
     * agenda is a wall clock read by people in WIB, so "today" and the
     * opening hours of the running event must follow their calendar.
     */
    public function run(): void
    {
        $takmir = User::query()->where('role', 'takmir')->first()
            ?? User::query()->orderBy('id')->firstOrFail();

        $today = CarbonImmutable::now('Asia/Jakarta')->startOfDay();
        $created = 0;

        foreach ($this->kegiatanList($today) as $kegiatan) {
            $record = JadwalKegiatan::query()->firstOrCreate(
                ['slug' => $kegiatan['slug']],
                ['user_id' => $takmir->id] + $kegiatan,
            );

            if ($record->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->command?->info(sprintf(
            'TakmirSeeder: %d kegiatan baru, %d sudah ada (agenda %s – %s).',
            $created,
            count($this->kegiatanList($today)) - $created,
            $today->subDays(28)->toDateString(),
            $today->addDays(34)->toDateString(),
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function kegiatanList(CarbonImmutable $today): array
    {
        $date = fn (int $offset): string => $today->addDays($offset)->toDateString();
        /* Three of the names promise a weekday ("Sabtu Malam", "Ahad Subuh",
           "Malam Jumat"), so those rows are anchored to the coming
           occurrence of that day instead of an offset that would land the
           event on a mismatched weekday. */
        $next = fn (string $weekday): string => $today->next($weekday)->toDateString();

        $list = [
            [
                'nama_kegiatan' => 'Pengajian Rutin Sabtu Malam: Tafsir Juz Amma',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Hasan Basri, S.Pd.I.',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => $next('saturday'),
                'waktu_mulai' => '19:30:00',
                'tanggal_selesai' => $next('saturday'),
                'waktu_selesai' => '21:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Pengajian tafsir mingguan membahas surat-surat pendek Juz Amma dengan bahasa yang mudah diikuti jamaah awam. Terbuka untuk semua usia.</p>',
            ],
            [
                'nama_kegiatan' => 'Kajian Rutin Ahad Subuh: Menyelami Riyadhus Shalihin',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Dr. Muhammad Zaki, M.A.',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => $next('sunday'),
                'waktu_mulai' => '05:00:00',
                'tanggal_selesai' => $next('sunday'),
                'waktu_selesai' => '06:30:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Kajian rutin setiap pekan ba\'da Subuh bersama Ustadz Dr. Muhammad Zaki membahas kitab Riyadhus Shalihin karya Imam An-Nawawi. Disediakan sarapan bersama setelah kajian.</p>',
            ],
            [
                'nama_kegiatan' => 'Tahsin & Belajar Membaca Al-Qur\'an Dewasa',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Ahmad Fauzan, Al-Hafidz',
                'lokasi' => 'Serambi Kanan Masjid',
                'tanggal_mulai' => $date(4),
                'waktu_mulai' => '18:30:00',
                'tanggal_selesai' => $date(4),
                'waktu_selesai' => '20:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Program perbaikan bacaan makhraj huruf dan hukum tajwid untuk jamaah bapak-bapak dan remaja putra. Terbuka untuk umum tanpa dipungut biaya.</p>',
            ],
            [
                'nama_kegiatan' => 'Kajian Fiqih Wanita & Parenting Islami',
                'penceramah_atau_penanggung_jawab' => 'Ustadzah Nurul Hidayah, Lc.',
                'lokasi' => 'Ruang Utama Lantai 2 (Area Akhwat)',
                'tanggal_mulai' => $date(6),
                'waktu_mulai' => '09:00:00',
                'tanggal_selesai' => $date(6),
                'waktu_selesai' => '11:30:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Kajian khusus muslimah mendiskusikan panduan fiqih wanita sehari-hari dan kiat mendidik anak di era digital menurut tuntunan Rasulullah SAW.</p>',
            ],
            [
                'nama_kegiatan' => 'Khataman & Qiyamul Lail Bersama Malam Jumat',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Ahmad Fauzan, Al-Hafidz',
                'lokasi' => 'Ruang Utama Masjid',
                /* Early Friday is the tail of Thursday night, which is the
                   "malam Jumat" the name refers to. */
                'tanggal_mulai' => $next('friday'),
                'waktu_mulai' => '02:30:00',
                'tanggal_selesai' => $next('friday'),
                'waktu_selesai' => '05:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Qiyamul lail berjamaah dimalami dengan tadarus dan doa bersama, dilanjutkan shalat Subuh berjamaah. Jamaah diharapkan membawa perlengkapan shalat sendiri.</p>',
            ],
            [
                'nama_kegiatan' => 'Pelatihan Manajemen Masjid untuk Remaja',
                'penceramah_atau_penanggung_jawab' => 'Divisi Kaderisasi Takmir',
                'lokasi' => 'Aula Serba Guna Masjid',
                'tanggal_mulai' => $date(9),
                'waktu_mulai' => '08:00:00',
                'tanggal_selesai' => $date(9),
                'waktu_selesai' => '12:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Pelatihan kepemimpinan dan manajemen kegiatan masjid untuk remaja masjid. Materi meliputi penyusunan program kerja, administrasi, dan kepanitiaan acara.</p>',
            ],
            [
                'nama_kegiatan' => 'Mabit & Qiyamul Lail Remaja Masjid',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Hasan Basri, S.Pd.I.',
                'lokasi' => 'Aula & Ruang Utama Masjid',
                'tanggal_mulai' => $date(10),
                'waktu_mulai' => '21:00:00',
                'tanggal_selesai' => $date(11),
                'waktu_selesai' => '05:30:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Mabit (malam bina iman dan takwa) selama dua hari satu malam untuk remaja masjid: kajian, game islami, qiyamul lail, dan sarapan bersama.</p>',
            ],
            [
                'nama_kegiatan' => 'Pemeriksaan Kesehatan Gratis & Donor Darah',
                'penceramah_atau_penanggung_jawab' => 'Divisi Sosial Takmir & PMI',
                'lokasi' => 'Halaman Depan Masjid',
                'tanggal_mulai' => $date(13),
                'waktu_mulai' => '07:00:00',
                'tanggal_selesai' => $date(13),
                'waktu_selesai' => '12:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Bekerja sama dengan puskesmas setempat dan PMI: cek tensi, gula darah, serta donor darah. Terbuka untuk seluruh warga lingkungan masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Bazar Jumat Berkah & Pasar Murah',
                'penceramah_atau_penanggung_jawab' => 'Divisi Sosial Takmir',
                'lokasi' => 'Halaman Parkir Masjid',
                'tanggal_mulai' => $date(20),
                'waktu_mulai' => '07:00:00',
                'tanggal_selesai' => $date(20),
                'waktu_selesai' => '11:30:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Bazar sembako murah dan makanan ringan hasil usaha jamaah. Sebagian hasil penjualan disalurkan untuk kas sosial masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Lomba Adzan & Tahfidz Anak-Anak',
                'penceramah_atau_penanggung_jawab' => 'Divisi Pendidikan Takmir',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => $date(27),
                'waktu_mulai' => '08:00:00',
                'tanggal_selesai' => $date(27),
                'waktu_selesai' => '14:00:00',
                'status' => 'akan_datang',
                'deskripsi' => '<p>Lomba adzan, hafalan surat pendek, dan doa harian untuk anak usia TPQ dan sekolah dasar. Pendaftaran dibuka di kantor takmir seminggu sebelum acara.</p>',
            ],
            [
                'nama_kegiatan' => 'Kajian Umum: Adab Bertetangga dalam Islam',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Dr. Muhammad Zaki, M.A.',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => $date(0),
                /* A running event should be running when it is seeded: the
                   window brackets the moment this seeder executes, in WIB
                   like every other time the page prints. */
                'waktu_mulai' => CarbonImmutable::now('Asia/Jakarta')->subMinutes(30)->format('H:i:s'),
                'tanggal_selesai' => $date(0),
                'waktu_selesai' => CarbonImmutable::now('Asia/Jakarta')->addMinutes(90)->format('H:i:s'),
                'status' => 'berlangsung',
                'deskripsi' => '<p>Kajian umum tentang adab bertetangga dan menjaga kerukunan warga menurut ajaran Islam, menjelang pemilihan ketua RT lingkungan masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Pengajian Bulanan Ibu-Ibu',
                'penceramah_atau_penanggung_jawab' => 'Ustadzah Nurul Hidayah, Lc.',
                'lokasi' => 'Aula Serba Guna Masjid',
                /* Called off, and dated in the past: the page files cancelled
                   rows under "Sudah Berlalu", so a future date would sit
                   there contradicting the group heading. */
                'tanggal_mulai' => $date(-1),
                'waktu_mulai' => '09:00:00',
                'tanggal_selesai' => $date(-1),
                'waktu_selesai' => '11:00:00',
                'status' => 'dibatalkan',
                'deskripsi' => '<p>Dibatalkan karena pemateri berhalangan hadir. Pengajian akan dijadwalkan ulang dan diumumkan melalui papan pengumuman masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Santunan Bulanan Anak Yatim & Dhuafa',
                'penceramah_atau_penanggung_jawab' => 'Divisi Sosial Takmir',
                'lokasi' => 'Aula Serba Guna Masjid',
                'tanggal_mulai' => $date(-10),
                'waktu_mulai' => '10:00:00',
                'tanggal_selesai' => $date(-10),
                'waktu_selesai' => '12:00:00',
                'status' => 'selesai',
                'deskripsi' => '<p>Penyaluran santunan bulanan untuk 35 anak yatim dan dhuafa binaan masjid, dilanjutkan makan siang bersama dan pemberian paket perlengkapan sekolah.</p>',
            ],
            [
                'nama_kegiatan' => 'Kerja Bakti Akbar & Bersih-bersih Masjid',
                'penceramah_atau_penanggung_jawab' => 'Divisi Pemeliharaan Sarana Takmir',
                'lokasi' => 'Seluruh Area Masjid & Tempat Wudhu',
                'tanggal_mulai' => $date(-3),
                'waktu_mulai' => '07:30:00',
                'tanggal_selesai' => $date(-3),
                'waktu_selesai' => '11:00:00',
                'status' => 'selesai',
                'deskripsi' => '<p>Gotong royong bersama warga sekitar membersihkan karpet masjid, area wudhu, halaman, dan drainase lingkungan masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Peringatan Nuzulul Qur\'an & Santunan Yatim',
                'penceramah_atau_penanggung_jawab' => 'KH. Abdullah Munir',
                'lokasi' => 'Ruang Utama Masjid',
                'tanggal_mulai' => $date(-20),
                'waktu_mulai' => '19:30:00',
                'tanggal_selesai' => $date(-20),
                'waktu_selesai' => '22:00:00',
                'status' => 'selesai',
                'deskripsi' => '<p>Peringatan malam Nuzulul Qur\'an dirangkaikan dengan penyaluran santunan untuk 50 anak yatim dhuafa di lingkungan sekitar masjid.</p>',
            ],
            [
                'nama_kegiatan' => 'Pelatihan Tajhiz Mayit untuk Warga',
                'penceramah_atau_penanggung_jawab' => 'Ustadz Hasan Basri, S.Pd.I.',
                'lokasi' => 'Aula Serba Guna Masjid',
                'tanggal_mulai' => $date(-28),
                'waktu_mulai' => '08:00:00',
                'tanggal_selesai' => $date(-28),
                'waktu_selesai' => '11:30:00',
                'status' => 'selesai',
                'deskripsi' => '<p>Pelatihan tata cara memandikan, mengkafani, dan menyalatkan jenazah sesuai tuntunan syariat, diikuti perwakilan keluarga dari tiap RT.</p>',
            ],
        ];

        return array_map(
            fn (array $kegiatan): array => ['slug' => Str::slug($kegiatan['nama_kegiatan'])] + $kegiatan,
            $list,
        );
    }
}
