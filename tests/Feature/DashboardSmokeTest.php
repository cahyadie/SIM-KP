<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }

    public function test_kaprodi_dashboard_renders(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'kaprodi']))
            ->get('/kaprodi/dashboard')
            ->assertStatus(200);
    }

    public function test_dosen_dashboard_renders(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'dosen']))
            ->get('/dosen/dashboard')
            ->assertStatus(200);
    }
}
