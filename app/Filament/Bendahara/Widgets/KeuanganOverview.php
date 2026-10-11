<?php

namespace App\Filament\Bendahara\Widgets;

use App\Models\TransaksiKeuangan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KeuanganOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPemasukan = (float) TransaksiKeuangan::where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (float) TransaksiKeuangan::where('jenis', 'pengeluaran')->sum('nominal');
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        return [
            Stat::make('Total Kas Masuk', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->description('Total seluruh pemasukan kas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Kas Keluar', 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'))
                ->description('Total pengeluaran & operasional')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Saldo Kas Masjid', 'Rp ' . number_format($saldoKas, 0, ',', '.'))
                ->description($saldoKas >= 0 ? 'Surplus / Saldo aman' : 'Defisit kas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($saldoKas >= 0 ? 'primary' : 'danger'),
        ];
    }
}
