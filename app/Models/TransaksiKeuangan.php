<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKeuangan extends Model
{
    protected $table = 'transaksi_keuangans';

    protected $fillable = [
        'kategori_keuangan_id',
        'user_id',
        'jenis',
        'judul',
        'nominal',
        'tanggal',
        'keterangan',
        'bukti',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKeuangan::class, 'kategori_keuangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
