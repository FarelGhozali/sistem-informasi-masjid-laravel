<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    protected $table = 'jadwal_kegiatans';

    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'slug',
        'penceramah_atau_penanggung_jawab',
        'lokasi',
        'tanggal_mulai',
        'waktu_mulai',
        'tanggal_selesai',
        'waktu_selesai',
        'status',
        'deskripsi',
        'poster',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
