<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Create an active product with a fixed slug.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function createProduct(array $attributes = []): Product
    {
        return Product::factory()->create(array_merge([
            'slug' => 'produk-detail',
            'status' => 'active',
        ], $attributes));
    }

    /**
     * Attach images to a product with the given number of photos.
     */
    private function attachImages(Product $product, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            ProductImage::factory()->create([
                'product_id' => $product->id,
                'image_path' => 'products/foto'.$i.'.jpg',
                'sort_order' => $i,
            ]);
        }
    }

    public function test_detail_page_can_be_rendered_by_slug(): void
    {
        $product = $this->createProduct(['name' => 'Produk Detail Uji']);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertOk();
        $response->assertSee('Produk Detail Uji');
    }

    public function test_unknown_slug_returns_not_found(): void
    {
        $this->get('/produk/tidak-ada')->assertNotFound();
    }

    public function test_inactive_product_returns_not_found(): void
    {
        $product = $this->createProduct(['slug' => 'produk-nonaktif', 'status' => 'inactive']);

        $this->get('/produk/'.$product->slug)->assertNotFound();
    }

    public function test_detail_shows_product_information(): void
    {
        $category = Category::factory()->create(['name' => 'Elektronik']);
        $product = $this->createProduct([
            'name' => 'Kipas Angin Portabel',
            'category_id' => $category->id,
            'description' => 'Kipas angin portabel dengan baterai tahan lama untuk cuaca panas.',
            'price' => 250000,
            'rating' => 4.8,
            'sold_count' => 1500,
        ]);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertOk();
        $response->assertSee('Kipas Angin Portabel');
        $response->assertSee('Rp 250.000');
        $response->assertSee('4.8');
        $response->assertSee('1,500 terjual');
        $response->assertSee('Kipas angin portabel dengan baterai tahan lama');
        $response->assertSee('Elektronik');
    }

    public function test_detail_shows_trending_and_viral_badges(): void
    {
        $product = $this->createProduct([
            'is_trending' => true,
            'is_viral' => true,
            'is_recommended' => false,
        ]);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertSee('Trending');
        $response->assertSee('Viral');
    }

    public function test_detail_shows_single_photo_without_thumbnails(): void
    {
        $product = $this->createProduct();
        $this->attachImages($product, 1);

        $response = $this->get('/produk/'.$product->slug);
        $content = $response->getContent();

        $response->assertOk();
        $response->assertSee('product-main-image', false);
        $response->assertSee('products/foto1.jpg', false);
        $this->assertSame(0, substr_count($content, 'Tampilkan foto'), 'Satu foto seharusnya tidak menampilkan thumbnail.');
    }

    public function test_detail_shows_two_photos_with_two_thumbnails(): void
    {
        $product = $this->createProduct();
        $this->attachImages($product, 2);

        $response = $this->get('/produk/'.$product->slug);
        $content = $response->getContent();

        $response->assertSee('products/foto1.jpg', false);
        $response->assertSee('products/foto2.jpg', false);
        $this->assertSame(2, substr_count($content, 'Tampilkan foto'));
    }

    public function test_detail_shows_three_photos_with_three_thumbnails(): void
    {
        $product = $this->createProduct();
        $this->attachImages($product, 3);

        $response = $this->get('/produk/'.$product->slug);
        $content = $response->getContent();

        $response->assertSee('products/foto1.jpg', false);
        $response->assertSee('products/foto2.jpg', false);
        $response->assertSee('products/foto3.jpg', false);
        $this->assertSame(3, substr_count($content, 'Tampilkan foto'), 'Tiga foto seharusnya menampilkan tiga thumbnail.');
    }

    public function test_first_photo_is_used_as_main_image(): void
    {
        $product = $this->createProduct();
        $this->attachImages($product, 3);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertSeeInOrder(['product-main-image', 'products/foto1.jpg'], false);
    }

    public function test_affiliate_button_uses_tracking_route_and_opens_new_tab(): void
    {
        $product = $this->createProduct([
            'affiliate_url' => 'https://shopee.co.id/product/123456',
        ]);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertSee('Beli di Shopee');
        $response->assertSee(route('affiliate.go', ['product' => $product->slug]), false);
        $response->assertDontSee('https://shopee.co.id/product/123456');
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer nofollow sponsored"', false);
    }

    public function test_related_products_from_same_category_are_shown(): void
    {
        $category = Category::factory()->create();
        $product = $this->createProduct(['category_id' => $category->id, 'name' => 'Produk Utama']);
        $related = Product::factory()->create([
            'name' => 'Produk Serumpun',
            'category_id' => $category->id,
            'status' => 'active',
        ]);
        Product::factory()->create(['name' => 'Produk Beda Kategori', 'status' => 'active']);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertOk();
        $response->assertSee('Produk Terkait');
        $response->assertSee($related->name);
        $response->assertDontSee('Produk Beda Kategori');
    }

    public function test_related_section_hidden_when_no_other_product_in_category(): void
    {
        $product = $this->createProduct();

        $response = $this->get('/produk/'.$product->slug);

        $response->assertOk();
        $response->assertDontSee('Produk Terkait');
    }

    public function test_related_products_exclude_the_current_product(): void
    {
        $category = Category::factory()->create();
        $product = $this->createProduct(['name' => 'Produk Utama', 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Produk Satu Kategori', 'category_id' => $category->id, 'status' => 'active']);

        $content = $this->get('/produk/'.$product->slug)->getContent();

        $selfUrl = route('products.show', ['product' => $product->slug]);
        $this->assertSame(2, substr_count($content, $selfUrl), 'URL produk sendiri hanya boleh muncul pada canonical dan og:url.');
    }

    public function test_detail_includes_seo_metadata(): void
    {
        $product = $this->createProduct([
            'name' => 'Produk SEO Uji',
            'description' => 'Deskripsi panjang untuk keperluan meta description pada halaman detail produk.',
        ]);
        $this->attachImages($product, 1);

        $response = $this->get('/produk/'.$product->slug);

        $response->assertSee('<title>Produk SEO Uji', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('Deskripsi panjang untuk keperluan meta description', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('products/foto1.jpg', false);
        $response->assertSee(route('products.show', ['product' => $product->slug]), false);
    }
}
