<?php

namespace App\Filament\Bendahara\Resources\TransaksiKeuanganResource\Pages;

use App\Filament\Bendahara\Resources\TransaksiKeuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiKeuangan extends EditRecord
{
    protected static string $resource = TransaksiKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
