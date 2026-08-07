<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Magang extends Model
{
    protected $appends = ['status_kegiatan'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jadwal_opsi_1' => 'datetime',
        'jadwal_opsi_2' => 'datetime',
        'jadwal_opsi_3' => 'datetime',
        'jadwal_opsi_4' => 'datetime',
        'jadwal_opsi_5' => 'datetime',
        'jadwal_opsi_6' => 'datetime',
        'jadwal_opsi_7' => 'datetime',
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
        'status_jadwal_skp',
        'jadwal_opsi_1',
        'jadwal_opsi_2',
        'jadwal_opsi_3',
        'jadwal_opsi_4',
        'jadwal_opsi_5',
        'jadwal_opsi_6',
        'jadwal_opsi_7',
        'jadwal_terpilih',
        'keterangan_tolak_jadwal',
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

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function scopeDiterima(Builder $query): Builder
    {
        return $query->where('status_validasi', 'diterima');
    }

    public function scopeSkpBelum(Builder $query): Builder
    {
        return $query->where('status_skp', 'belum');
    }

    public function scopeSkpSudah(Builder $query): Builder
    {
        return $query->where('status_skp', 'sudah');
    }

    public function scopeGajiPaid(Builder $query): Builder
    {
        return $query->where('status_gaji', 'paid');
    }

    public function scopeGajiUnpaid(Builder $query): Builder
    {
        return $query->where('status_gaji', 'unpaid');
    }

    public function scopeSedangBerlangsung(Builder $query): Builder
    {
        return $query->diterima()->skpBelum()
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now());
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->diterima()->skpBelum()
            ->whereDate('tanggal_selesai', '>=', now());
    }

    public function scopeMenungguSkp(Builder $query): Builder
    {
        return $query->diterima()->skpBelum()
            ->whereDate('tanggal_selesai', '<', now());
    }

    public function scopeLulusSkp(Builder $query): Builder
    {
        return $query->diterima()->skpSudah();
    }

    public function scopeBimbingan(Builder $query, int $dosenId): Builder
    {
        return $query->where('dosen_id', $dosenId);
    }

    public function getStatusKegiatanAttribute(): string
    {
        if ($this->status_skp === 'sudah') {
            return 'selesai';
        }

        if (in_array($this->status_jadwal_skp, ['disetujui', 'menunggu'])) {
            return 'skp';
        }

        if ($this->tanggal_selesai && $this->tanggal_selesai->startOfDay()->isPast()) {
            return 'selesai';
        }

        return 'magang';
    }
}
