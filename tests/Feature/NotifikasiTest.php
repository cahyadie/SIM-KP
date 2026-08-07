<?php

namespace Tests\Feature;

use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use App\Models\User;
use App\Notifications\MagangNotification;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatDosen(): User
    {
        return User::factory()->create(['role' => 'dosen']);
    }

    private function buatMahasiswa(): User
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => '12345678',
            'angkatan' => '2022',
            'prodi' => 'Teknik Informatika',
            'no_hp' => '081234567890',
        ]);

        return $user;
    }

    private function buatMagang(array $overrides = []): Magang
    {
        $dosen = $this->buatDosen();
        $mahasiswa = $this->buatMahasiswa();

        $perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Contoh',
            'alamat' => 'Jl. Contoh No. 1',
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

    public function test_pendaftaran_magang_mengirim_notifikasi_ke_dosen()
    {
        $dosen = $this->buatDosen();
        $mahasiswa = $this->buatMahasiswa();

        $response = $this->actingAs($mahasiswa)->post('/mahasiswa/daftar', [
            'dosen_id' => $dosen->id,
            'nama_perusahaan' => 'PT Baru',
            'alamat' => 'Jl. Baru',
            'latitude' => '-6.2',
            'longitude' => '106.8',
            'kategori_industri' => 'Teknologi',
            'tanggal_mulai' => now()->addDays(1)->toDateString(),
            'tanggal_selesai' => now()->addDays(60)->toDateString(),
            'status_gaji' => 'unpaid',
            'tema_magang' => 'Mobile',
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $dosen->id,
            'type' => MagangNotification::class,
        ]);
    }

    public function test_pengisian_logbook_mengirim_notifikasi_ke_dosen()
    {
        $magang = $this->buatMagang();

        $this->actingAs($magang->mahasiswa->user)->post('/mahasiswa/magang/'.$magang->id.'/logbook', [
            'minggu_ke' => 1,
            'tgl_mulai' => $magang->tanggal_mulai->toDateString(),
            'tgl_selesai' => $magang->tanggal_mulai->copy()->addDays(6)->toDateString(),
            'log' => [
                ['hari' => 'Senin', 'kegiatan' => 'Ngoding', 'permasalahan' => '-', 'solusi' => '-'],
            ],
        ])->assertRedirect(route('mahasiswa.logbook.index', $magang->id));

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $magang->dosen_id,
            'type' => MagangNotification::class,
        ]);

        $notif = $magang->dosen->notifications()->first();
        $this->assertSame('logbook', $notif->data['jenis']);
    }

    public function test_pengajuan_jadwal_mengirim_notifikasi_ke_dosen()
    {
        $magang = $this->buatMagang();

        $dates = collect(range(1, 7))
            ->map(fn ($i) => now()->addDays($i)->format('Y-m-d\TH:i'));

        $response = $this->actingAs($magang->mahasiswa->user)->post('/mahasiswa/seminar/ajukan-jadwal', [
            'jadwal_opsi_1' => $dates[0],
            'jadwal_opsi_2' => $dates[1],
            'jadwal_opsi_3' => $dates[2],
            'jadwal_opsi_4' => $dates[3],
            'jadwal_opsi_5' => $dates[4],
            'jadwal_opsi_6' => $dates[5],
            'jadwal_opsi_7' => $dates[6],
            'surat_selesai_magang' => UploadedFile::fake()->create('surat.pdf', 100),
        ]);

        $response->assertRedirect()->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $magang->dosen_id,
            'type' => MagangNotification::class,
        ]);

        $notif = $magang->dosen->notifications()->first();
        $this->assertSame('ajukan_jadwal', $notif->data['jenis']);
    }

    public function test_penyelesaian_seminar_mengirim_notifikasi_ke_dosen()
    {
        $magang = $this->buatMagang();

        $this->actingAs($magang->mahasiswa->user)->post('/mahasiswa/seminar', [
            'nilai_seminar' => 'A',
            'file_seminar' => UploadedFile::fake()->create('laporan.pdf', 100),
        ])->assertRedirect();

        $magang->refresh();
        $this->assertSame('sudah', $magang->status_skp);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $magang->dosen_id,
            'type' => MagangNotification::class,
        ]);

        $notif = $magang->dosen->notifications()->first();
        $this->assertSame('selesai_seminar', $notif->data['jenis']);
    }

    public function test_kirim_selesai_magang_tidak_duplikat()
    {
        $magang = $this->buatMagang(['tanggal_selesai' => now()->subDay()->toDateString()]);

        $jumlah = NotifikasiService::kirimSelesaiMagang();
        $this->assertSame(1, $jumlah);

        $jumlah2 = NotifikasiService::kirimSelesaiMagang();
        $this->assertSame(0, $jumlah2);

        $this->assertSame(1, $magang->dosen->notifications()->count());
        $this->assertSame('selesai_magang', $magang->dosen->notifications()->first()->data['jenis']);
    }

    public function test_magang_yang_belum_selesai_tidak_dapat_notifikasi_selesai()
    {
        $magang = $this->buatMagang(['tanggal_selesai' => now()->addDays(10)->toDateString()]);

        NotifikasiService::kirimSelesaiMagang();

        $this->assertDatabaseCount('notifications', 0);
        $this->assertSame(0, $magang->dosen->notifications()->count());
    }

    public function test_membuka_notifikasi_menandai_dibaca_dan_redirect()
    {
        $magang = $this->buatMagang();
        $dosen = $magang->dosen;

        $dosen->notify(new MagangNotification(
            'logbook',
            'Pesan uji',
            route('dosen.bimbingan.logbook', $magang->id),
            'bi-bell',
            $magang->id,
        ));

        $notif = $dosen->notifications()->first();
        $this->assertNull($notif->read_at);

        $this->actingAs($dosen)
            ->get('/dosen/notifikasi/'.$notif->id.'/go')
            ->assertRedirect(route('dosen.bimbingan.logbook', $magang->id));

        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_read_all_menandai_semua_dibaca()
    {
        $magang = $this->buatMagang();
        $dosen = $magang->dosen;

        foreach (['mulai_magang', 'logbook'] as $jenis) {
            $dosen->notify(new MagangNotification($jenis, 'Pesan '.$jenis, route('dosen.dashboard'), 'bi-bell', $magang->id));
        }

        $this->assertSame(2, $dosen->unreadNotifications()->count());

        $this->actingAs($dosen)->post('/dosen/notifikasi/read-all')->assertRedirect();

        $this->assertSame(0, $dosen->fresh()->unreadNotifications()->count());
    }

    public function test_halaman_notifikasi_render()
    {
        $magang = $this->buatMagang();
        $dosen = $magang->dosen;

        $dosen->notify(new MagangNotification('logbook', 'Pesan uji', route('dosen.dashboard'), 'bi-bell', $magang->id));

        $this->actingAs($dosen)->get('/dosen/notifikasi')->assertStatus(200);
    }
}
