<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_lowongan_page_redirects_when_guest(): void
    {
        $this->get('/lowongan')->assertRedirect('/login');
    }
}
