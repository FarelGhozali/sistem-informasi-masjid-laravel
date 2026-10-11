<?php

namespace App\Filament\Takmir\Widgets;

use App\Models\JadwalKegiatan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KegiatanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $akanDatang = JadwalKegiatan::where('status', 'akan_datang')->count();
        $berlangsung = JadwalKegiatan::where('status', 'berlangsung')->count();
        $selesai = JadwalKegiatan::where('status', 'selesai')->count();
        $totalKegiatan = JadwalKegiatan::count();

        return [
            Stat::make('Agenda Akan Datang', $akanDatang)
                ->description('Kegiatan mendatang')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Sedang Berlangsung', $berlangsung)
                ->description('Sedang berjalan hari ini')
                ->descriptionIcon('heroicon-m-play')
                ->color('warning'),
            Stat::make('Total Kegiatan Masjid', $totalKegiatan)
                ->description("{$selesai} kegiatan telah selesai")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
