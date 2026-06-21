<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    // Tambahkan 'no_hp' di sini
    protected $fillable = ['user_id', 'nim', 'angkatan', 'prodi', 'no_hp'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function magangs() {
        return $this->hasMany(Magang::class);
    }
}