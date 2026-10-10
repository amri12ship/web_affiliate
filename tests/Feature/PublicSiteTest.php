<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_homepage_can_be_rendered_without_login(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Temukan Produk Menarik');
        $response->assertSee('Rekomendasi produk pilihan dengan harga menarik.');
        $response->assertSee('Lihat Produk Trending');
    }

    public function test_homepage_does_not_error_when_database_is_empty(): void
    {
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('categories', 0);

        $this->get('/')->assertOk();
    }

    public function test_homepage_shows_all_required_sections_and_navbar(): void
    {
        $response = $this->get('/');

        $response->assertSee('Kategori');
        $response->assertSee('Produk Trending');
        $response->assertSee('Produk Viral');
        $response->assertSee('Produk Rekomendasi');
        $response->assertSee('Produk Terbaru');

        foreach (['Home', 'Kategori', 'Trending', 'Promo', 'Review'] as $menu) {
            $response->assertSee($menu);
        }

        $response->assertSee(route('products.index'));
    }

    public function test_homepage_shows_only_active_categories(): void
    {
        Category::factory()->create(['name' => 'Elektronik Aktif', 'status' => 'active']);
        Category::factory()->create(['name' => 'Kategori Sembunyi', 'status' => 'inactive']);

        $response = $this->get('/');

        $response->assertSee('Elektronik Aktif');
        $response->assertDontSee('Kategori Sembunyi');
    }

    public function test_homepage_shows_trending_viral_and_recommended_products(): void
    {
        Product::factory()->create(['name' => 'Produk Trending A', 'status' => 'active', 'is_trending' => true, 'is_viral' => false, 'is_recommended' => false]);
        Product::factory()->create(['name' => 'Produk Viral B', 'status' => 'active', 'is_trending' => false, 'is_viral' => true, 'is_recommended' => false]);
        Product::factory()->create(['name' => 'Produk Rekomendasi C', 'status' => 'active', 'is_trending' => false, 'is_viral' => false, 'is_recommended' => true]);

        $response = $this->get('/');

        $response->assertSee('Produk Trending A');
        $response->assertSee('Produk Viral B');
        $response->assertSee('Produk Rekomendasi C');
    }

    public function test_inactive_products_are_not_shown(): void
    {
        Product::factory()->create(['name' => 'Produk Aktif Tampil', 'status' => 'active']);
        Product::factory()->create(['name' => 'Produk Nonaktif Sembunyi', 'status' => 'inactive']);

        $response = $this->get('/');

        $response->assertSee('Produk Aktif Tampil');
        $response->assertDontSee('Produk Nonaktif Sembunyi');
    }

    public function test_product_card_shows_price_rating_sold_and_button(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produk Kartu Uji',
            'price' => 250000,
            'rating' => 4.7,
            'sold_count' => 1500,
            'status' => 'active',
        ]);

        $response = $this->get('/');

        $response->assertSee('Produk Kartu Uji');
        $response->assertSee('250.000');
        $response->assertSee('4.7');
        $response->assertSee('1,500');
        $response->assertSee('Lihat Produk');
        $response->assertSee(route('affiliate.go', ['product' => $product->slug]), false);
        $response->assertDontSee($product->affiliate_url);
    }

    public function test_card_badges_are_shown_for_flagged_products(): void
    {
        Product::factory()->create([
            'name' => 'Produk Badge Uji',
            'status' => 'active',
            'is_trending' => true,
            'is_viral' => true,
            'is_recommended' => true,
        ]);

        $response = $this->get('/');

        $response->assertSee('Trending');
        $response->assertSee('Viral');
        $response->assertSee('Recommended');
    }

    public function test_affiliate_button_uses_safe_blank_target(): void
    {
        Product::factory()->create(['name' => 'Produk Link Aman', 'status' => 'active']);

        $response = $this->get('/');

        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer nofollow sponsored"', false);
    }

    public function test_catalog_page_can_be_rendered(): void
    {
        Product::factory()->create(['name' => 'Produk Katalog', 'status' => 'active']);

        $response = $this->get('/produk');

        $response->assertOk();
        $response->assertSee('Semua Produk');
        $response->assertSee('Produk Katalog');
    }

    public function test_catalog_search_filters_products(): void
    {
        Product::factory()->create(['name' => 'Sepatu Olahraga', 'status' => 'active']);
        Product::factory()->create(['name' => 'Tas Sekolah', 'status' => 'active']);

        $response = $this->get('/produk?search=sepatu');

        $response->assertOk();
        $response->assertSee('Sepatu Olahraga');
        $response->assertDontSee('Tas Sekolah');
    }

    public function test_catalog_filter_by_category(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Produk Kategori A', 'category_id' => $category->id, 'status' => 'active']);
        Product::factory()->create(['name' => 'Produk Kategori B', 'status' => 'active']);

        $response = $this->get('/produk?category='.$category->id);

        $response->assertOk();
        $response->assertSee('Produk Kategori A');
        $response->assertDontSee('Produk Kategori B');
    }

    public function test_product_image_is_shown_on_card(): void
    {
        $product = Product::factory()->create(['name' => 'Produk Berfoto', 'status' => 'active']);
        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
            'image_path' => 'products/uji.jpg',
            'sort_order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertSee($image->image_path, false);
    }
}
