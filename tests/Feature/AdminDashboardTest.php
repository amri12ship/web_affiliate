<?php

namespace Tests\Feature;

use App\Models\AffiliateClick;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Total Produk');
        $response->assertSee('Total Kategori');
        $response->assertSee('Total Klik Affiliate');
        $response->assertSee('Produk Trending');
        $response->assertSee('Recent Products');
    }

    public function test_non_admin_user_is_rejected_from_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_guest_is_rejected_from_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_does_not_error_when_database_is_empty(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('categories', 0);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Belum ada produk');
    }

    public function test_dashboard_shows_real_counts_and_recent_products(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Elektronik']);

        $headphone = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Headphone Nirkabel',
            'price' => 250000,
        ]);

        $keyboard = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Keyboard Mekanik',
            'price' => 500000,
        ]);

        AffiliateClick::factory()->count(60)->for($headphone)->create();
        AffiliateClick::factory()->count(100)->for($keyboard)->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Headphone Nirkabel');
        $response->assertSee('Keyboard Mekanik');
        $response->assertSee('Elektronik');
        $response->assertSee('Rp 250.000');
        $response->assertSee('Total Klik Affiliate');
        $response->assertSee('Produk Paling Banyak Diklik');
        $response->assertViewHas('totalClicks', 160);
    }

    public function test_dashboard_orders_top_products_by_click_count(): void
    {
        $admin = User::factory()->admin()->create();

        $moreClicked = Product::factory()->create(['name' => 'Produk Paling Diklik']);
        $lessClicked = Product::factory()->create(['name' => 'Produk Sedikit Diklik']);

        AffiliateClick::factory()->count(8)->for($moreClicked)->create();
        AffiliateClick::factory()->count(3)->for($lessClicked)->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Paling Diklik', 'Produk Sedikit Diklik']);
        $response->assertViewHas('topProducts', function ($topProducts) {
            return $topProducts->first()->name === 'Produk Paling Diklik'
                && (int) $topProducts->first()->affiliate_clicks_count === 8;
        });
    }

    public function test_dashboard_click_statistics_use_correct_periods(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));

        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-10-15 09:00:00')]);
        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-10-13 09:00:00')]);
        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-10-05 09:00:00')]);
        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-09-20 09:00:00')]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertViewHas('totalClicks', 4);
        $response->assertViewHas('clicksToday', 1);
        $response->assertViewHas('clicksThisWeek', 2);
        $response->assertViewHas('clicksThisMonth', 3);

        $this->travelBack();
    }
}
