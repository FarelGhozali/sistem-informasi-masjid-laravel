<?php

namespace App\Filament\Bendahara\Resources;

use App\Filament\Bendahara\Resources\KategoriKeuanganResource\Pages;
use App\Filament\Bendahara\Resources\KategoriKeuanganResource\RelationManagers;
use App\Models\KategoriKeuangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriKeuanganResource extends Resource
{
    protected static ?string $model = KategoriKeuangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?string $modelLabel = 'Kategori Keuangan';

    protected static ?string $pluralModelLabel = 'Kategori Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->required()
                    ->placeholder('Contoh: Infaq Jumat, Operasional Listrik')
                    ->maxLength(255),
                Forms\Components\Select::make('jenis')
                    ->label('Jenis Aliran Kas')
                    ->options([
                        'pemasukan' => 'Pemasukan (Kas Masuk)',
                        'pengeluaran' => 'Pengeluaran (Kas Keluar)',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriKeuangans::route('/'),
            'create' => Pages\CreateKategoriKeuangan::route('/create'),
            'edit' => Pages\EditKategoriKeuangan::route('/{record}/edit'),
        ];
    }
}
