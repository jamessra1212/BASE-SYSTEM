<?php

namespace Tests\Feature\Core;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'menu.manage-users-destroy']);
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Staff']);
        Role::create(['name' => 'User Manager'])->givePermissionTo('manage users', 'menu.manage-users-destroy');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('User Manager');
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'fname' => 'Juan',
            'lname' => 'Dela Cruz',
            'username' => 'jdelacruz',
            'email' => 'juan@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
            'categories' => 1,
            'role' => 'Staff',
        ], $overrides);
    }

    public function test_creating_a_user_inserts_exactly_one_record_and_succeeds(): void
    {
        $this->actingAs($this->manager)
            ->post(route('core.users.store'), $this->payload())
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertSame(1, User::where('username', 'jdelacruz')->count());
        $this->assertTrue(User::where('username', 'jdelacruz')->first()->hasRole('Staff'));
    }

    public function test_updating_a_user_without_password_keeps_the_existing_one(): void
    {
        $user = User::factory()->create();
        $oldHash = $user->password;

        $this->actingAs($this->manager)
            ->post(route('core.users.store'), $this->payload([
                'id' => $user->id,
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertOk();

        $this->assertSame($oldHash, $user->fresh()->password);
        $this->assertSame('jdelacruz', $user->fresh()->username);
    }

    public function test_short_passwords_are_rejected(): void
    {
        $this->actingAs($this->manager)
            ->post(route('core.users.store'), $this->payload(['password' => 'abcd', 'password_confirmation' => 'abcd']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_non_super_admin_cannot_grant_super_admin_role(): void
    {
        $this->actingAs($this->manager)
            ->post(route('core.users.store'), $this->payload(['role' => 'Super Admin']))
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['username' => 'jdelacruz']);
    }

    public function test_non_super_admin_cannot_touch_a_super_admin_account(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $this->actingAs($this->manager)
            ->post(route('core.users.upass'), ['id' => $admin->id, 'password' => 'new-password', 'password_confirmation' => 'new-password'])
            ->assertForbidden();

        $this->actingAs($this->manager)
            ->post(route('core.users.ustat'), ['id' => $admin->id, 'is_activated' => 0])
            ->assertForbidden();

        $this->actingAs($this->manager)
            ->delete(route('core.users.destroy'), ['id' => $admin->id])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_activated);
    }

    public function test_super_admin_can_grant_super_admin_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $this->actingAs($admin)
            ->post(route('core.users.store'), $this->payload(['role' => 'Super Admin']))
            ->assertOk();

        $this->assertTrue(User::where('username', 'jdelacruz')->first()->isSuperAdmin());
    }

    public function test_avatar_upload_is_stored_and_served_safely(): void
    {
        $this->actingAs($this->manager)
            ->post(route('core.users.store'), $this->payload([
                'avatar' => UploadedFile::fake()->image('me.png', 64, 64),
            ]))
            ->assertOk();

        $user = User::where('username', 'jdelacruz')->first();
        $this->assertNotEmpty($user->avatar_data);
        $this->assertArrayNotHasKey('avatar_data', $user->toArray());

        $this->actingAs($this->manager)
            ->get($user->avatarUrl())
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_svg_avatars_are_rejected(): void
    {
        $svg = UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $this->actingAs($this->manager)
            ->post(route('core.users.store'), $this->payload(['avatar' => $svg]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('avatar');
    }

    public function test_deactivated_user_is_signed_out_on_next_request(): void
    {
        $user = User::factory()->deactivated()->create();

        $this->actingAs($user)
            ->get(route('app.main.home'))
            ->assertRedirect(route('auth.login'));

        $this->assertGuest();
    }
}
