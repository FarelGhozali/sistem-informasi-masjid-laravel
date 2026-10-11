<?php

namespace App\Filament\Takmir\Resources;

use App\Filament\Takmir\Resources\JadwalKegiatanResource\Pages;
use App\Filament\Takmir\Resources\JadwalKegiatanResource\RelationManagers;
use App\Models\JadwalKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JadwalKegiatanResource extends Resource
{
    protected static ?string $model = JadwalKegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Kegiatan Masjid';

    protected static ?string $modelLabel = 'Jadwal Kegiatan';

    protected static ?string $pluralModelLabel = 'Jadwal Kegiatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kegiatan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_kegiatan')
                            ->label('Nama Kegiatan')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('penceramah_atau_penanggung_jawab')
                            ->label('Penceramah / PJ Kegiatan')
                            ->placeholder('Contoh: Ustadz Ahmad, Lc.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->required()
                            ->maxLength(255)
                            ->default('Ruang Utama Masjid'),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->default(now())
                            ->required(),
                        Forms\Components\TimePicker::make('waktu_mulai')
                            ->label('Waktu Mulai')
                            ->seconds(false)
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai (Opsional)'),
                        Forms\Components\TimePicker::make('waktu_selesai')
                            ->label('Waktu Selesai (Opsional)')
                            ->seconds(false),
                        Forms\Components\Select::make('status')
                            ->label('Status Kegiatan')
                            ->options([
                                'akan_datang' => 'Akan Datang',
                                'berlangsung' => 'Sedang Berlangsung',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('akan_datang')
                            ->required(),
                        Forms\Components\FileUpload::make('poster')
                            ->label('Brosur / Poster Kegiatan')
                            ->image()
                            ->directory('poster-kegiatan')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi / Detail Acara')
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
                Tables\Columns\ImageColumn::make('poster')
                    ->label('Poster')
                    ->square()
                    ->disk('public'),
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->description(fn (JadwalKegiatan $record): ?string => $record->penceramah_atau_penanggung_jawab ? 'PJ/Pemateri: ' . $record->penceramah_atau_penanggung_jawab : null),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal & Waktu')
                    ->formatStateUsing(fn (JadwalKegiatan $record): string => $record->tanggal_mulai?->format('d M Y') . ' (' . substr($record->waktu_mulai, 0, 5) . ' WIB)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'akan_datang' => 'info',
                        'berlangsung' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'akan_datang' => 'Akan Datang',
                        'berlangsung' => 'Sedang Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst($state),
                    }),
            ])
            ->defaultSort('tanggal_mulai', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'akan_datang' => 'Akan Datang',
                        'berlangsung' => 'Sedang Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
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
            'index' => Pages\ListJadwalKegiatans::route('/'),
            'create' => Pages\CreateJadwalKegiatan::route('/create'),
            'edit' => Pages\EditJadwalKegiatan::route('/{record}/edit'),
        ];
    }
}
