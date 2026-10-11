<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReviewTest extends TestCase
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

    public function test_admin_can_open_review_page(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/review');

        $response->assertOk();
        $response->assertSee('Review Produk');
        $response->assertSee('Rata-rata Rating');
        $response->assertSee('Distribusi Rating');
    }

    public function test_review_page_does_not_error_with_empty_database(): void
    {
        $this->assertDatabaseCount('products', 0);

        $response = $this->actingAs($this->admin())->get('/admin/review');

        $response->assertOk();
        $response->assertSee('Tidak ada produk ditemukan');
    }

    public function test_review_summary_uses_real_product_values(): void
    {
        $category = Category::factory()->create(['name' => 'Fashion']);

        $best = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produk Rating Tinggi',
            'rating' => 4.90,
            'sold_count' => 120,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produk Biasa',
            'rating' => 3.10,
            'sold_count' => 10,
        ]);

        $response = $this->actingAs($this->admin())->get('/admin/review');

        $response->assertOk();
        $response->assertSee('Produk Rating Tinggi');
        $response->assertSee('Fashion');
        $response->assertViewHas('bestRated', fn ($product) => $product->id === $best->id);
        $response->assertViewHas('averageRating', 4.0);
    }

    public function test_review_can_search_by_name(): void
    {
        Product::factory()->create(['name' => 'Headphone Keren', 'rating' => 5.00, 'sold_count' => 100]);
        Product::factory()->create(['name' => 'Sepatu Lari', 'rating' => 1.00, 'sold_count' => 1]);

        $response = $this->actingAs($this->admin())->get('/admin/review?q=Headphone');

        $response->assertOk();
        $response->assertSee('Headphone Keren');
        $response->assertDontSee('Sepatu Lari');
    }

    public function test_review_can_filter_by_category(): void
    {
        $elektronik = Category::factory()->create(['name' => 'Elektronik']);
        $fashion = Category::factory()->create(['name' => 'Fashion']);

        Product::factory()->create(['category_id' => $elektronik->id, 'name' => 'Gadget Murah', 'rating' => 5.00, 'sold_count' => 100]);
        Product::factory()->create(['category_id' => $fashion->id, 'name' => 'Baju Bagus', 'rating' => 1.00, 'sold_count' => 1]);

        $response = $this->actingAs($this->admin())->get('/admin/review?category='.$elektronik->id);

        $response->assertOk();
        $response->assertSee('Gadget Murah');
        $response->assertDontSee('Baju Bagus');
    }

    public function test_review_can_sort_by_sold_count(): void
    {
        Product::factory()->create(['name' => 'Produk Sedikit', 'rating' => 1.00, 'sold_count' => 1]);
        Product::factory()->create(['name' => 'Produk Banyak', 'rating' => 5.00, 'sold_count' => 999]);

        $response = $this->actingAs($this->admin())->get('/admin/review?sort=sold_desc');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Banyak', 'Produk Sedikit']);
        $response->assertViewHas('sort', 'sold_desc');
    }

    public function test_invalid_sort_falls_back_to_default(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/review?sort=palsu');

        $response->assertOk();
        $response->assertViewHas('sort', 'rating_desc');
    }

    public function test_non_admin_user_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/review')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/review')->assertRedirect(route('admin.login'));
    }
}
