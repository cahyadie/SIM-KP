<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['perusahaan_id', 'mahasiswa_id', 'rating', 'komentar'];

    public function mahasiswa() {
        return $this->belongsTo(Mahasiswa::class);
    }
    
    public function perusahaan() {
        return $this->belongsTo(Perusahaan::class);
    }
}