<?php

namespace App\Filament\Bendahara\Resources;

use App\Filament\Bendahara\Resources\TransaksiKeuanganResource\Pages;
use App\Filament\Bendahara\Resources\TransaksiKeuanganResource\RelationManagers;
use App\Models\TransaksiKeuangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiKeuanganResource extends Resource
{
    protected static ?string $model = TransaksiKeuangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?string $modelLabel = 'Transaksi Kas';

    protected static ?string $pluralModelLabel = 'Transaksi Kas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Kas')
                            ->options([
                                'pemasukan' => 'Pemasukan (Kas Masuk)',
                                'pengeluaran' => 'Pengeluaran (Kas Keluar)',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('kategori_keuangan_id', null)),
                        Forms\Components\Select::make('kategori_keuangan_id')
                            ->label('Kategori')
                            ->relationship(
                                name: 'kategori',
                                titleAttribute: 'nama',
                                modifyQueryUsing: fn (Builder $query, callable $get) => 
                                    $get('jenis') ? $query->where('jenis', $get('jenis')) : $query
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul / Perihal')
                            ->required()
                            ->placeholder('Contoh: Infaq Jumat 15 Okt, Beli Karpet Baru')
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nominal')
                            ->label('Nominal (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('bukti')
                            ->label('Foto Kwitansi / Bukti Pembayaran')
                            ->image()
                            ->directory('bukti-keuangan')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul / Keterangan')
                    ->searchable()
                    ->description(fn (TransaksiKeuangan $record): ?string => $record->kategori?->nama),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('bukti')
                    ->label('Bukti')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis Transaksi')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
                Tables\Filters\SelectFilter::make('kategori_keuangan_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListTransaksiKeuangans::route('/'),
            'create' => Pages\CreateTransaksiKeuangan::route('/create'),
            'edit' => Pages\EditTransaksiKeuangan::route('/{record}/edit'),
        ];
    }
}
