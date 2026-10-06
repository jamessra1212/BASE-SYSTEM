<?php

namespace Tests\Feature\Core;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProfileSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Terminating sessions only works with the database session driver
        config(['session.driver' => 'database']);
    }

    protected function otherDeviceSession(User $user): string
    {
        DB::table('sessions')->insert([
            'id' => 'other-device-session',
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        return 'other-device-session';
    }

    public function test_terminating_a_session_also_cancels_remember_me_on_other_devices(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        $otherSession = $this->otherDeviceSession($user);

        $this->actingAs($user)
            ->post(route('app.main.profile'), ['action_type' => 'terminate_session', 'session_id' => $otherSession])
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('sessions', ['id' => $otherSession]);
        $this->assertNotSame('old-remember-token', $user->fresh()->remember_token);
    }

    public function test_the_terminating_device_keeps_its_remember_cookie(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        $this->otherDeviceSession($user);
        $recaller = Auth::guard()->getRecallerName();

        $response = $this->actingAs($user)
            ->withCookie($recaller, $user->id.'|old-remember-token|'.$user->password)
            ->post(route('app.main.profile'), ['action_type' => 'terminate_session']);

        $response->assertOk()->assertCookie($recaller);

        $newToken = $user->fresh()->remember_token;
        $this->assertNotSame('old-remember-token', $newToken);
        $this->assertStringContainsString('|'.$newToken.'|', $response->getCookie($recaller)->getValue());
        $this->assertAuthenticatedAs($user);
    }
}
