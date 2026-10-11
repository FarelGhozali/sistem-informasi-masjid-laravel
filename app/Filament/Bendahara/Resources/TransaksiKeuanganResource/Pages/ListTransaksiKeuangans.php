<?php

namespace App\Filament\Bendahara\Resources\TransaksiKeuanganResource\Pages;

use App\Filament\Bendahara\Resources\TransaksiKeuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiKeuangans extends ListRecords
{
    protected static string $resource = TransaksiKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
