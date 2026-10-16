<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    /**
     * The public website cannot read the `poster` column: it holds a path on
     * the storage disk, and it cannot render RichEditor HTML with textContent.
     * Both endpoints therefore also carry a ready-to-use poster URL and a
     * plain-text rendering of the description.
     */
    protected $appends = [
        'poster_url',
        'deskripsi_teks',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster ? Storage::disk('public')->url($this->poster) : null;
    }

    public function getDeskripsiTeksAttribute(): string
    {
        $html = (string) $this->deskripsi;

        if ($html === '') {
            return '';
        }

        /* Block tags become line breaks before the tags are stripped, or
           "<p>Satu</p><p>Dua</p>" would come out as "SatuDua". */
        $text = preg_replace('#</(p|h[1-6]|li|blockquote|div)>#i', "\n", $html);
        $text = preg_replace('#<br\s*/?>#i', "\n", (string) $text);
        $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5);

        return trim(preg_replace('/\n{3,}/', "\n\n", trim($text)) ?? '');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
