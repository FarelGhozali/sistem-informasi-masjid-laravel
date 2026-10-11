<?php

namespace App\Filament\Takmir\Resources\JadwalKegiatanResource\Pages;

use App\Filament\Takmir\Resources\JadwalKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalKegiatans extends ListRecords
{
    protected static string $resource = JadwalKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
