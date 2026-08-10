<?php

namespace Tests\Feature;

use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapAktifTest extends TestCase
{
    use RefreshDatabase;

    private function buatDosen(): User
    {
        return User::factory()->create(['role' => 'dosen']);
    }

    private function buatMahasiswa(string $nim): User
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa '.$nim]);
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $nim,
            'angkatan' => '2022',
            'prodi' => 'Teknik Informatika',
            'no_hp' => '0812'.$nim,
        ]);

        return $user;
    }

    private function buatMagang(array $overrides = []): Magang
    {
        $dosen = $this->buatDosen();
        $mahasiswa = $this->buatMahasiswa((string) random_int(10000000, 99999999));

        $perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Aktif',
            'alamat' => 'Jl. Contoh No. 1',
            'latitude' => '-6.2',
            'longitude' => '106.8',
        ]);

        return Magang::create(array_merge([
            'mahasiswa_id' => $mahasiswa->mahasiswa->id,
            'perusahaan_id' => $perusahaan->id,
            'dosen_id' => $dosen->id,
            'tanggal_mulai' => now()->subDays(30)->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
            'status_gaji' => 'unpaid',
            'status_skp' => 'belum',
            'status_validasi' => 'diterima',
            'tema_magang' => 'Backend',
        ], $overrides));
    }

    public function test_admin_map_hanya_menampilkan_magang_yang_masih_aktif()
    {
        $aktif = $this->buatMagang();
        $selesai = $this->buatMagang(['tanggal_selesai' => now()->subDay()->toDateString()]);

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();

        $namaTampil = collect($response->viewData('lokasi_magang'))->pluck('nama_mhs')->flatten();

        $this->assertTrue($namaTampil->contains($aktif->mahasiswa->user->name));
        $this->assertFalse($namaTampil->contains($selesai->mahasiswa->user->name));
    }

    public function test_dosen_map_hanya_menampilkan_magang_yang_masih_aktif()
    {
        $dosen = $this->buatDosen();
        $aktif = $this->buatMagang(['dosen_id' => $dosen->id]);
        $selesai = $this->buatMagang(['dosen_id' => $dosen->id, 'tanggal_selesai' => now()->subDay()->toDateString()]);

        $response = $this->actingAs($dosen)->get('/dosen/dashboard');

        $response->assertOk();

        $namaTampil = collect($response->viewData('marker_locations'))->pluck('nama_mhs')->flatten();

        $this->assertTrue($namaTampil->contains($aktif->mahasiswa->user->name));
        $this->assertFalse($namaTampil->contains($selesai->mahasiswa->user->name));
    }
}
