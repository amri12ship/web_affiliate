<?php

namespace Tests\Feature;

use App\Models\AffiliateClick;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStatisticsTest extends TestCase
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

    public function test_admin_can_open_statistics_page(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/statistik');

        $response->assertOk();
        $response->assertSee('Statistik');
        $response->assertSee('Klik 30 Hari Terakhir');
        $response->assertSee('Klik per Kategori');
    }

    public function test_statistics_page_does_not_error_with_empty_database(): void
    {
        $this->assertDatabaseCount('products', 0);

        $response = $this->actingAs($this->admin())->get('/admin/statistik');

        $response->assertOk();
        $response->assertViewHas('totalClicks', 0);
        $response->assertSee('Belum ada data klik affiliate.');
    }

    public function test_statistics_counts_clicks_and_ranks_products(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create(['name' => 'Elektronik']);

        $top = Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Teratas']);
        $other = Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Lain']);

        AffiliateClick::factory()->count(7)->for($top)->create();
        AffiliateClick::factory()->count(2)->for($other)->create();

        $response = $this->actingAs($admin)->get('/admin/statistik');

        $response->assertOk();
        $response->assertViewHas('totalClicks', 9);
        $response->assertSee('Produk Teratas');
        $response->assertSee('Elektronik');
        $response->assertViewHas('topProducts', fn ($products) => $products->first()->name === 'Produk Teratas'
            && (int) $products->first()->affiliate_clicks_count === 7);
        $response->assertViewHas('categoryStats', fn ($stats) => (int) $stats->first()->clicks === 9);
    }

    public function test_statistics_click_periods_use_correct_ranges(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create();

        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));

        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-10-15 09:00:00')]);
        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-10-13 09:00:00')]);
        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-10-05 09:00:00')]);
        AffiliateClick::factory()->for($product)->create(['clicked_at' => Carbon::parse('2026-09-20 09:00:00')]);

        $response = $this->actingAs($admin)->get('/admin/statistik');

        $response->assertOk();
        $response->assertViewHas('clicksToday', 1);
        $response->assertViewHas('clicksThisWeek', 2);
        $response->assertViewHas('clicksThisMonth', 3);

        $this->travelBack();
    }

    public function test_daily_clicks_series_has_thirty_days(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/statistik');

        $response->assertViewHas('dailyClicks', fn ($daily) => $daily->count() === 30);
    }

    public function test_non_admin_user_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/statistik')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/statistik')->assertRedirect(route('admin.login'));
    }
}
