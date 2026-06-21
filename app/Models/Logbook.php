<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $fillable = [
        'magang_id',
        'minggu_ke',
        'tgl_mulai',
        'tgl_selesai',
        'isi_logbook', // Berisi array data Senin-Sabtu
        'komentar_dosen',
        'status_acc',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'isi_logbook' => 'array', // Otomatis ubah JSON jadi Array
    ];

    public function magang()
    {
        return $this->belongsTo(Magang::class);
    }
}