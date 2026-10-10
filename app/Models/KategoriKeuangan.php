<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKeuangan extends Model
{
    protected $table = 'kategori_keuangans';

    protected $fillable = [
        'nama',
        'jenis',
        'deskripsi',
    ];

    public function transaksiKeuangans()
    {
        return $this->hasMany(TransaksiKeuangan::class);
    }
}
