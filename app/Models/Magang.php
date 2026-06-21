<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magang extends Model
{
    protected $appends = ['status_kegiatan'];

    // Casting agar tanggal otomatis jadi object Carbon (mudah diformat)
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        // Tambahan cast agar jadwal otomatis terbaca lengkap dengan jamnya
        'jadwal_opsi_1' => 'datetime',
        'jadwal_opsi_2' => 'datetime',
        'jadwal_opsi_3' => 'datetime',
        'jadwal_terpilih' => 'datetime',
    ];

    protected $fillable = [
        'mahasiswa_id',
        'perusahaan_id',
        'dosen_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_gaji',
        'tema_magang',
        'status_skp',
        'status_validasi',
        'file_surat_kaprodi',
        'file_seminar',
        'nilai_seminar',
        'keterangan_revisi',
        'ruangan_skp',
        'surat_selesai_magang',
        'file_nilai_lapangan',

        // --- KOLOM BARU UNTUK FITUR JADWAL SKP ---
        'status_jadwal_skp',
        'jadwal_opsi_1',
        'jadwal_opsi_2',
        'jadwal_opsi_3',
        'jadwal_terpilih',
        'keterangan_tolak_jadwal'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function getStatusKegiatanAttribute()
    {
        $now = now()->startOfDay();
        $selesai = $this->tanggal_selesai ? \Carbon\Carbon::parse($this->tanggal_selesai)->startOfDay() : null;

        if ($this->status_skp === 'sudah') {
            return 'selesai';
        }
        if ($this->status_jadwal_skp === 'disetujui' || $this->status_jadwal_skp === 'menunggu') {
            return 'skp';
        }
        if ($selesai && $now->greaterThan($selesai)) {
            return 'selesai';
        }
        return 'magang';
    }
}