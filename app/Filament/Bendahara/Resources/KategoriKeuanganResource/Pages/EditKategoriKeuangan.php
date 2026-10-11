<?php

namespace App\Filament\Bendahara\Resources\KategoriKeuanganResource\Pages;

use App\Filament\Bendahara\Resources\KategoriKeuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriKeuangan extends EditRecord
{
    protected static string $resource = KategoriKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
