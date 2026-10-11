<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_view_settings_form(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/pengaturan');

        $response->assertOk();
        $response->assertSee('Pengaturan Situs');
        $response->assertSee('Nama Situs');
        $response->assertSee('Email Kontak');
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->admin())->put('/admin/pengaturan', [
            'site_name' => 'Belanja Hemat',
            'site_description' => 'Rekomendasi produk murah',
            'contact_email' => 'admin@example.com',
            'contact_phone' => '08123456789',
            'footer_text' => 'Terima kasih telah berkunjung',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'Belanja Hemat']);
        $this->assertDatabaseHas('settings', ['key' => 'contact_email', 'value' => 'admin@example.com']);
        $this->assertSame('Belanja Hemat', Setting::value('site_name'));
    }

    public function test_site_name_is_required(): void
    {
        $response = $this->actingAs($this->admin())->put('/admin/pengaturan', [
            'site_name' => '',
        ]);

        $response->assertSessionHasErrors('site_name');
        $this->assertDatabaseMissing('settings', ['key' => 'site_name']);
    }

    public function test_contact_email_must_be_a_valid_email(): void
    {
        $response = $this->actingAs($this->admin())->put('/admin/pengaturan', [
            'site_name' => 'Toko Online',
            'contact_email' => 'bukan-email',
        ]);

        $response->assertSessionHasErrors('contact_email');
    }

    public function test_site_name_reflects_on_public_pages(): void
    {
        Setting::put('site_name', 'Affiliate Mantap');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Affiliate Mantap');
    }

    public function test_updated_site_name_appears_after_saving(): void
    {
        $this->actingAs($this->admin())->put('/admin/pengaturan', [
            'site_name' => 'Nama Baru Situs',
        ]);

        $this->get('/')->assertSee('Nama Baru Situs');
    }

    public function test_setting_value_returns_default_when_missing(): void
    {
        $this->assertSame('fallback', Setting::value('tidak-ada', 'fallback'));
    }

    public function test_non_admin_user_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/pengaturan')->assertForbidden();
        $this->actingAs($user)->put('/admin/pengaturan', ['site_name' => 'X'])->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/pengaturan')->assertRedirect(route('admin.login'));
    }
}
