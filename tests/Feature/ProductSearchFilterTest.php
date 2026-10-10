<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function activeProduct(array $attributes = []): Product
    {
        return Product::factory()->create(array_merge(['status' => 'active'], $attributes));
    }

    private function cardCount(string $content): int
    {
        return substr_count($content, 'data-testid="product-card"');
    }

    public function test_catalog_can_be_rendered_without_filters(): void
    {
        $this->activeProduct();

        $this->get('/produk')->assertOk()->assertSee('Semua Produk');
    }

    public function test_search_matches_product_name(): void
    {
        $this->activeProduct(['name' => 'Sepatu Olahraga']);
        $this->activeProduct(['name' => 'Tas Sekolah']);

        $response = $this->get('/produk?search=sepatu');

        $response->assertOk();
        $response->assertSee('Sepatu Olahraga');
        $response->assertDontSee('Tas Sekolah');
    }

    public function test_search_matches_product_description(): void
    {
        $this->activeProduct(['name' => 'Produk Andalan', 'description' => 'Cocok dipakai saat hujan deras.']);
        $this->activeProduct(['name' => 'Produk Biasa', 'description' => 'Ringan dan mudah dibawa.']);

        $response = $this->get('/produk?search=hujan');

        $response->assertOk();
        $response->assertSee('Produk Andalan');
        $response->assertDontSee('Produk Biasa');
    }

    public function test_search_does_not_return_inactive_products(): void
    {
        $this->activeProduct(['name' => 'Sepatu Aktif']);
        Product::factory()->create(['name' => 'Sepatu Nonaktif', 'status' => 'inactive']);

        $response = $this->get('/produk?search=sepatu');

        $response->assertSee('Sepatu Aktif');
        $response->assertDontSee('Sepatu Nonaktif');
    }

    public function test_filter_by_category(): void
    {
        $category = Category::factory()->create();
        $this->activeProduct(['name' => 'Produk Kategori A', 'category_id' => $category->id]);
        $this->activeProduct(['name' => 'Produk Kategori B']);

        $response = $this->get('/produk?category='.$category->id);

        $response->assertOk();
        $response->assertSee('Produk Kategori A');
        $response->assertDontSee('Produk Kategori B');
    }

    public function test_filter_by_min_price(): void
    {
        $this->activeProduct(['name' => 'Produk Murah', 'price' => 50000]);
        $this->activeProduct(['name' => 'Produk Mahal', 'price' => 500000]);

        $response = $this->get('/produk?min_price=100000');

        $response->assertOk();
        $response->assertSee('Produk Mahal');
        $response->assertDontSee('Produk Murah');
    }

    public function test_filter_by_max_price(): void
    {
        $this->activeProduct(['name' => 'Produk Murah', 'price' => 50000]);
        $this->activeProduct(['name' => 'Produk Mahal', 'price' => 500000]);

        $response = $this->get('/produk?max_price=100000');

        $response->assertOk();
        $response->assertSee('Produk Murah');
        $response->assertDontSee('Produk Mahal');
    }

    public function test_filter_by_price_range(): void
    {
        $this->activeProduct(['name' => 'Produk Terlalu Murah', 'price' => 10000]);
        $this->activeProduct(['name' => 'Produk Pas', 'price' => 150000]);
        $this->activeProduct(['name' => 'Produk Terlalu Mahal', 'price' => 900000]);

        $response = $this->get('/produk?min_price=100000&max_price=200000');

        $response->assertOk();
        $response->assertSee('Produk Pas');
        $response->assertDontSee('Produk Terlalu Murah');
        $response->assertDontSee('Produk Terlalu Mahal');
    }

    public function test_filter_by_min_rating(): void
    {
        $this->activeProduct(['name' => 'Produk Rating Bagus', 'rating' => 4.8]);
        $this->activeProduct(['name' => 'Produk Rating Jelek', 'rating' => 2.5]);

        $response = $this->get('/produk?min_rating=4');

        $response->assertOk();
        $response->assertSee('Produk Rating Bagus');
        $response->assertDontSee('Produk Rating Jelek');
    }

    public function test_combined_search_and_filter(): void
    {
        $category = Category::factory()->create();
        $this->activeProduct(['name' => 'Sepatu Mahal Sekali', 'price' => 800000, 'category_id' => $category->id]);
        $this->activeProduct(['name' => 'Sepatu Murah', 'price' => 90000, 'category_id' => $category->id]);
        $this->activeProduct(['name' => 'Tas Mahal', 'price' => 800000, 'category_id' => $category->id]);

        $response = $this->get('/produk?search=sepatu&category='.$category->id.'&min_price=500000');

        $response->assertOk();
        $response->assertSee('Sepatu Mahal Sekali');
        $response->assertDontSee('Sepatu Murah');
        $response->assertDontSee('Tas Mahal');
    }

    public function test_sort_by_cheapest(): void
    {
        $this->activeProduct(['name' => 'Produk Harga Tengah', 'price' => 300000]);
        $this->activeProduct(['name' => 'Produk Harga Murah', 'price' => 100000]);
        $this->activeProduct(['name' => 'Produk Harga Mahal', 'price' => 900000]);

        $response = $this->get('/produk?sort=cheapest');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Harga Murah', 'Produk Harga Tengah', 'Produk Harga Mahal']);
    }

    public function test_sort_by_expensive(): void
    {
        $this->activeProduct(['name' => 'Produk Harga Tengah', 'price' => 300000]);
        $this->activeProduct(['name' => 'Produk Harga Mahal', 'price' => 900000]);
        $this->activeProduct(['name' => 'Produk Harga Murah', 'price' => 100000]);

        $response = $this->get('/produk?sort=expensive');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Harga Mahal', 'Produk Harga Tengah', 'Produk Harga Murah']);
    }

    public function test_sort_by_rating(): void
    {
        $this->activeProduct(['name' => 'Produk Rating Sedang', 'rating' => 3.7]);
        $this->activeProduct(['name' => 'Produk Rating Tertinggi', 'price' => 1, 'rating' => 4.9]);
        $this->activeProduct(['name' => 'Produk Rating Rendah', 'rating' => 2.5]);

        $response = $this->get('/produk?sort=rating');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Rating Tertinggi', 'Produk Rating Sedang', 'Produk Rating Rendah']);
    }

    public function test_sort_by_popular(): void
    {
        $this->activeProduct(['name' => 'Produk Cukup Populer', 'sold_count' => 500]);
        $this->activeProduct(['name' => 'Produk Sangat Populer', 'sold_count' => 9000]);
        $this->activeProduct(['name' => 'Produk Kurang Populer', 'sold_count' => 10]);

        $response = $this->get('/produk?sort=popular');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Sangat Populer', 'Produk Cukup Populer', 'Produk Kurang Populer']);
    }

    public function test_invalid_sort_falls_back_to_newest(): void
    {
        $old = $this->activeProduct(['name' => 'Produk Lama']);
        $old->forceFill(['created_at' => now()->subDay()])->save();

        $new = $this->activeProduct(['name' => 'Produk Baru']);
        $new->forceFill(['created_at' => now()])->save();

        $response = $this->get('/produk?sort=drop_table');

        $response->assertOk();
        $response->assertSeeInOrder(['Produk Baru', 'Produk Lama']);
    }

    public function test_catalog_is_paginated(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->activeProduct(['name' => 'Produk Paginasi '.$i]);
        }

        $firstPage = $this->get('/produk');
        $firstPage->assertOk();
        $this->assertSame(12, $this->cardCount($firstPage->getContent()), 'Halaman pertama harus menampilkan 12 produk.');

        $secondPage = $this->get('/produk?page=2');
        $secondPage->assertOk();
        $this->assertSame(3, $this->cardCount($secondPage->getContent()), 'Halaman kedua harus menampilkan 3 produk.');
    }

    public function test_pagination_preserves_query_string(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->activeProduct(['name' => 'Produk Cari '.$i]);
        }

        $response = $this->get('/produk?search=Produk&page=1');

        $response->assertOk();
        $response->assertSee('search=Produk', false);
    }

    public function test_empty_state_shows_message_and_reset_button(): void
    {
        $this->activeProduct(['name' => 'Produk Ada']);

        $response = $this->get('/produk?search=barangyangtidakada');

        $response->assertOk();
        $response->assertSee('Produk yang kamu cari belum ditemukan.');
        $response->assertSee('Lihat Semua Produk');
        $this->assertSame(0, $this->cardCount($response->getContent()));
    }
}
