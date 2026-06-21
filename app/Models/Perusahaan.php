<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    // Sesuaikan field ini dengan kolom yang ada di tabel perusahaans milikmu
    protected $fillable = [
        'nama_perusahaan',
        'kategori_industri',
        'alamat',
        'latitude',
        'longitude',
        // tambahkan kolom lain jika ada
    ];

    // Accessor: Membuat atribut bayangan 'rata_rata_rating'
    // Membaca dari hasil withAvg() di controller, atau menghitung manual jika tidak ada
    public function getRataRataRatingAttribute()
    {
        // Jika sudah dihitung oleh withAvg('reviews', 'rating') di controller
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return (float) ($this->attributes['reviews_avg_rating'] ?? 0);
        }

        // Fallback: Jika dipanggil tanpa withAvg, hitung rata-rata secara manual
        return (float) ($this->reviews()->avg('rating') ?? 0);
    }

    // Relasi ke Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relasi ke Magang
    public function magangs()
    {
        return $this->hasMany(Magang::class);
    }
}