<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Login Admin');
    }

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@affiliate.test',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@affiliate.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_with_wrong_credentials_is_rejected(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@affiliate.test',
            'password' => Hash::make('password'),
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@affiliate.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_non_admin_user_is_forbidden_from_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_password_is_not_stored_in_plaintext(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@affiliate.test')->firstOrFail();

        $this->assertNotSame('password', $admin->password);
        $this->assertTrue(Hash::check('password', $admin->password));
    }
}
