<?php

use App\Models\JadwalKegiatan;
use App\Models\TransaksiKeuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes untuk Frontend Astro & Publik
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Transparansi Keuangan (Publik untuk Website Masjid)
    Route::get('/keuangan/ringkasan', function () {
        $totalPemasukan = (float) TransaksiKeuangan::where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (float) TransaksiKeuangan::where('jenis', 'pengeluaran')->sum('nominal');
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo_kas' => $saldoKas,
                'total_pemasukan_formatted' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.'),
                'total_pengeluaran_formatted' => 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'),
                'saldo_kas_formatted' => 'Rp ' . number_format($saldoKas, 0, ',', '.'),
            ],
        ]);
    });

    Route::get('/keuangan/transaksi', function (Request $request) {
        $query = TransaksiKeuangan::with('kategori:id,nama,jenis');

        if ($request->has('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        $transaksi = $query->orderBy('tanggal', 'desc')->paginate($request->query('limit', 15));

        return response()->json([
            'status' => 'success',
            'data' => $transaksi,
        ]);
    });

    // 2. Jadwal Kegiatan Masjid (Publik untuk Website Astro)
    Route::get('/kegiatan', function (Request $request) {
        $query = JadwalKegiatan::query();

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $kegiatan = $query->orderBy('tanggal_mulai', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->paginate($request->query('limit', 10));

        return response()->json([
            'status' => 'success',
            'data' => $kegiatan,
        ]);
    });

    Route::get('/kegiatan/{slug}', function ($slug) {
        $kegiatan = JadwalKegiatan::where('slug', $slug)->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $kegiatan,
        ]);
    });
});
