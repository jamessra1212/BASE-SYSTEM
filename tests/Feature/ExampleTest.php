<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('auth.login'));
    }

    public function test_login_page_renders(): void
    {
        $this->get(route('auth.login'))->assertOk();
    }
}
