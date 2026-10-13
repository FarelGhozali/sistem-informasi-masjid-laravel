<?php

namespace Database\Seeders;

use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeuanganSeeder extends Seeder
{
    /**
     * How many months of bookkeeping to write, ending with the current one.
     * Two years is what makes the /keuangan/ page show its full shape: a
     * month-by-month chart, a category breakdown with real shares, and enough
     * rows for pagination, search and CSV export to have something to chew on.
     */
    private const MONTHS = 24;

    /** Month names, so "Honor ... Maret 2026" needs no intl extension. */
    private const MONTHS_ID = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Two years of books for the finance page.
     *
     * This seeder walks the last 24 months and books the transactions a masjid
     * of this size actually has: one infaq entry for every Friday, harian
     * infaq through the month, the monthly bills and honor, per-kajian
     * operations, santunan, and the occasional wakaf or sponsor — a few
     * hundred rows in total.
     *
     * Running it replaces every row in `transaksi_keuangans` and touches
     * nothing else. `mt_srand` is fixed, so two runs on the same day produce
     * the same books; only the calendar moves. That keeps totals comparable
     * between runs and between machines.
     */
    public function run(): void
    {
        mt_srand(20261017);

        $kategori = $this->seedKategori();
        $bendahara = User::query()->where('role', 'bendahara')->first()
            ?? User::query()->orderBy('id')->firstOrFail();

        $today = CarbonImmutable::now()->startOfDay();
        $firstMonth = $today->startOfMonth()->subMonthsNoOverflow(self::MONTHS - 1);
        $lastMonth = $today->startOfMonth();

        $rows = [];
        for ($month = $firstMonth; $month->lessThanOrEqualTo($lastMonth); $month = $month->addMonthNoOverflow()) {
            $this->bookMonth($month, $today, $kategori, $bendahara->id, $rows);
        }

        /* One transaction for the whole replacement, so a failure halfway
           leaves the books as they were rather than half-rewritten. */
        DB::transaction(function () use ($rows) {
            TransaksiKeuangan::query()->delete();
            foreach (array_chunk($rows, 200) as $chunk) {
                TransaksiKeuangan::query()->insert($chunk);
            }
        });

        $this->command?->info(sprintf(
            'KeuanganSeeder: %d transaksi pada %d bulan (%s – %s).',
            count($rows),
            self::MONTHS,
            $firstMonth->toDateString(),
            $today->toDateString(),
        ));
    }

    /**
     * The category list the admin panel and the website already know. Same
     * names as before, so a database seeded earlier keeps its categories
     * instead of growing a second "Kotak Infaq Jumat".
     *
     * @return array<string, int> nama => id
     */
    private function seedKategori(): array
    {
        $list = [
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

        $map = [];
        foreach ($list as $item) {
            $kategori = KategoriKeuangan::query()->firstOrCreate(
                ['nama' => $item['nama']],
                [
                    'jenis' => $item['jenis'],
                    'deskripsi' => $item['deskripsi'],
                ]
            );
            $map[$item['nama']] = $kategori->id;
        }

        return $map;
    }

    /**
     * Books one month. Every date lands on or before $today, so the current,
     * still-running month is not made more complete than it is.
     *
     * @param  array<string, int>  $kategori  nama => id
     * @param  list<array<string, mixed>>  $rows
     */
    private function bookMonth(
        CarbonImmutable $month,
        CarbonImmutable $today,
        array $kategori,
        int $userId,
        array &$rows
    ): void {
        $lastDay = $month->endOfMonth()->isAfter($today) ? $today : $month->endOfMonth();
        if ($lastDay->lessThan($month)) {
            return; // A future month: nothing booked yet.
        }

        $monthName = self::MONTHS_ID[$month->month].' '.$month->year;

        /* --- Pemasukan ---------------------------------------------------- */

        // Infaq Jumat, one entry for every Friday: the steadiest income line.
        $friday = $month->isFriday() ? $month : $month->next(CarbonInterface::FRIDAY);
        $week = 1;
        while ($friday->lessThanOrEqualTo($lastDay)) {
            $this->add($rows, $friday, $userId, $kategori['Kotak Infaq Jumat'], 'pemasukan',
                'Perolehan Infaq Jumat Pekan ke-'.$week,
                mt_rand(2200, 4600) * 1000,
                self::pick([
                    'Dihitung bersama 2 orang saksi jamaah',
                    'Hasil kotak infaq setelah sholat Jumat',
                    'Disaksikan petugas takmir dan jamaah',
                ]));
            $week++;
            $friday = $friday->addWeek();
        }

        // Infaq harian, kuliah subuh and the evening kajian box.
        foreach (self::pickMany([
            'Infaq Kuliah Subuh Ahad Pagi',
            'Infaq Kotak Depan Masjid',
            'Infaq Kajian Ba\'da Maghrib',
            'Infaq Jamaah Sholat Dhuha',
            'Infaq Kotak Belakang & Tempat Wudhu',
        ], mt_rand(3, 5)) as $judul) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Infaq Harian / Subuh'], 'pemasukan',
                $judul,
                mt_rand(300, 950) * 1000,
                'Kotak keliling dan kotak tetap masjid');
        }

        // Donasi dan wakaf: not every month, but the big ones when they come.
        if (mt_rand(1, 100) <= 28) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Donasi Renovasi Masjid'], 'pemasukan',
                self::pick([
                    'Wakaf Pembangunan Menara Masjid',
                    'Sumbangan Renovasi Tempat Wudhu',
                    'Wakaf Karpet & Sound System',
                    'Donasi Renovasi Ruang Utama',
                    'Wakaf Pembelian Rak Al-Qur\'an',
                ]),
                mt_rand(1000, 7500) * 1000,
                self::pick([
                    'Transfer via rekening BSI Masjid',
                    'Diserahkan langsung kepada bendahara',
                    'Donasi dari jamaah perantauan',
                ]));
        }

        // ZIS: a few times a year, and the largest single receipts.
        if (mt_rand(1, 100) <= 22) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Zakat, Infaq & Sedekah (ZIS)'], 'pemasukan',
                self::pick([
                    'Penerimaan Zakat Mal Jamaah',
                    'Penerimaan Zakat Fitrah & Fidyah',
                    'Penerimaan Infaq Sedekah Khusus',
                ]),
                mt_rand(2500, 9000) * 1000,
                'Disalurkan melalui program ZIS masjid');
        }

        // Sponsor kegiatan, mostly for the big events.
        if (mt_rand(1, 100) <= 16) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Sponsor / Donatur Kegiatan'], 'pemasukan',
                self::pick([
                    'Sponsor Tabligh Akbar & Peringatan Hari Besar',
                    'Donatur Kajian Rutin Bulanan',
                    'Sponsor Lomba & Festival Anak Sholeh',
                ]),
                mt_rand(1500, 4000) * 1000,
                'Dana kemitraan kegiatan masjid');
        }

        /* --- Pengeluaran -------------------------------------------------- */

        // Honor petugas, at the start of the month.
        $this->add($rows, $this->dayIn($month, $lastDay, 1, 5), $userId, $kategori['Honor Imam, Muadzin & Marbot'], 'pengeluaran',
            'Honor Imam, Muadzin & Marbot '.$monthName,
            mt_rand(3200, 4500) * 1000,
            'Kompensasi bulanan petugas masjid');

        // Listrik, air and internet, spread through the middle of the month.
        $this->add($rows, $this->dayIn($month, $lastDay, 8, 18), $userId, $kategori['Listrik, Air & Internet'], 'pengeluaran',
            'Pembayaran Tagihan Listrik PLN '.$monthName,
            mt_rand(950, 1650) * 1000,
            'Pemakaian AC dan penerangan masjid');

        if (mt_rand(1, 100) <= 70) {
            $this->add($rows, $this->dayIn($month, $lastDay, 8, 22), $userId, $kategori['Listrik, Air & Internet'], 'pengeluaran',
                'Tagihan PDAM & WiFi Masjid '.$monthName,
                mt_rand(450, 900) * 1000,
                'Air wudhu dan internet masjid');
        }

        // Operasional kajian: bisyarah, konsumsi, sound.
        foreach (self::pickMany([
            'Bisyarah Ustadz & Snack Jamaah Kajian Ahad',
            'Konsumsi Kajian Fiqih Wanita',
            'Bisyarah Pemateri Kajian Tafsir Tematik',
            'Sewa Sound System Tabligh Akbar',
            'Konsumsi Pelatihan Tahsin Remaja',
        ], mt_rand(3, 4)) as $judul) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Operasional Kajian & Tabligh Akbar'], 'pengeluaran',
                $judul,
                mt_rand(500, 1500) * 1000,
                'Operasional kajian rutin masjid');
        }

        // Kebersihan dan pemeliharaan sarana.
        foreach (self::pickMany([
            'Service & Cuci AC Masjid',
            'Pembelian Alat Kebersihan & Disinfektan',
            'Perawatan Karpet & Wastafel Masjid',
            'Perbaikan Lampu dan Kipas Ruang Utama',
            'Pemeliharaan Taman & Drainase Masjid',
        ], mt_rand(1, 2)) as $judul) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Kebersihan & Pemeliharaan Sarana'], 'pengeluaran',
                $judul,
                mt_rand(350, 1450) * 1000,
                'Perawatan sarana masjid');
        }

        // Belanja sarana besar: rare, but it is what the saldo is for.
        if (mt_rand(1, 100) <= 18) {
            $this->add($rows, $this->randomDay($month, $lastDay), $userId, $kategori['Kebersihan & Pemeliharaan Sarana'], 'pengeluaran',
                self::pick([
                    'Pengecatan & Perbaikan Bangunan Masjid',
                    'Pengadaan Al-Qur\'an dan Buku Kajian',
                    'Pembelian Genset Cadangan Listrik',
                    'Penggantian Karpet Ruang Utama',
                ]),
                mt_rand(1800, 5200) * 1000,
                'Belanja sarana prasarana masjid');
        }

        // Santunan bulanan untuk yatim dan dhuafa sekitar.
        $this->add($rows, $this->dayIn($month, $lastDay, 10, 25), $userId, $kategori['Santunan Yatim & Dhuafa'], 'pengeluaran',
            'Santunan Yatim & Dhuafa '.$monthName,
            mt_rand(900, 2400) * 1000,
            'Dana sosial untuk warga sekitar masjid');
    }

    /**
     * Appends one transaction to the batch, with timestamps on the same day —
     * the treasurer records a day, and the page reads updated_at for its
     * "pembukuan terakhir" line.
     *
     * @param  list<array<string, mixed>>  $rows
     */
    private function add(
        array &$rows,
        CarbonImmutable $tanggal,
        int $userId,
        ?int $kategoriId,
        string $jenis,
        string $judul,
        int $nominal,
        string $keterangan
    ): void {
        $stamp = $tanggal->setTime(mt_rand(8, 20), mt_rand(0, 59))->toDateTimeString();

        $rows[] = [
            'kategori_keuangan_id' => $kategoriId,
            'user_id' => $userId,
            'jenis' => $jenis,
            'judul' => $judul,
            'nominal' => $nominal,
            'tanggal' => $tanggal->toDateString(),
            'keterangan' => $keterangan,
            'bukti' => null,
            'created_at' => $stamp,
            'updated_at' => $stamp,
        ];
    }

    /** A random day inside the month, never after $lastDay. */
    private function randomDay(CarbonImmutable $month, CarbonImmutable $lastDay): CarbonImmutable
    {
        return $month->addDays(mt_rand(0, (int) $month->diffInDays($lastDay)));
    }

    /** A random day between two days of the month, clamped to $lastDay. */
    private function dayIn(CarbonImmutable $month, CarbonImmutable $lastDay, int $from, int $to): CarbonImmutable
    {
        $max = min($to, $lastDay->day);

        return $month->setDay(mt_rand($from, max($from, $max)));
    }

    /**
     * @param  list<string>  $items
     */
    private static function pick(array $items): string
    {
        return $items[mt_rand(0, count($items) - 1)];
    }

    /**
     * @param  list<string>  $items
     * @return list<string>
     */
    private static function pickMany(array $items, int $count): array
    {
        shuffle($items);

        return array_slice($items, 0, min($count, count($items)));
    }
}
