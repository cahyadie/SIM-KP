<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function mahasiswaUser(): User
    {
        $user = $this->user('mahasiswa');
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '12345678',
            'angkatan' => '2022',
            'prodi' => 'Teknik Informatika',
            'no_hp' => '081234567890',
        ]);

        return $user;
    }

    public function test_guest_routes(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertStatus(200);
    }

    public function test_admin_pages_render(): void
    {
        $this->actingAs($this->user('admin'));
        $paths = [
            '/admin/dashboard',
            '/admin/riwayat-magang',
            '/admin/skp-list',
            '/admin/pengumuman',
            '/admin/users',
            '/lowongan',
            '/direktori-magang',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_kaprodi_pages_render(): void
    {
        $this->actingAs($this->user('kaprodi'));
        $paths = [
            '/kaprodi/dashboard',
            '/kaprodi/riwayat-magang',
            '/kaprodi/skp-list',
            '/kaprodi/monitoring',
            '/kaprodi/pantauan-skp',
            // statistik/aktif memakai MONTH() MySQL — tidak kompatibel SQLite (test)
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_dosen_pages_render(): void
    {
        $this->actingAs($this->user('dosen'));
        $paths = [
            '/dosen/dashboard',
            '/dosen/bimbingan',
            '/dosen/skp',
            '/dosen/riwayat-magang',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_mahasiswa_pages_render(): void
    {
        $this->actingAs($this->mahasiswaUser());
        $paths = [
            '/mahasiswa/dashboard',
            '/mahasiswa/profile',
            '/mahasiswa/daftar',
            '/mahasiswa/riwayat-magang',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertStatus(200);
        }

        // Seminar mengalihkan karena belum ada magang yang diterima
        $this->get('/mahasiswa/seminar')->assertRedirect();
    }
}
