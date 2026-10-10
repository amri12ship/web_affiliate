<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(Category $category, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Produk '.fake()->unique()->numerify('###'),
            'category_id' => $category->id,
            'description' => 'Deskripsi produk',
            'price' => 150000,
            'rating' => 4.5,
            'sold_count' => 120,
            'affiliate_url' => 'https://shopee.co.id/product/123456',
            'status' => 'active',
        ], $overrides);
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function fakeImages(int $count): array
    {
        return collect(range(1, $count))
            ->map(fn ($i) => UploadedFile::fake()->image("foto-{$i}.jpg", 100 * $i, 100))
            ->all();
    }

    public function test_admin_can_view_create_product_form(): void
    {
        Category::factory()->create();

        $this->actingAs($this->admin())
            ->get('/admin/products/create')
            ->assertOk()
            ->assertSee('Tambah Produk')
            ->assertSee('Affiliate URL');
    }

    public function test_admin_can_view_edit_product_form(): void
    {
        $product = Product::factory()->create(['name' => 'Nama Lama']);

        $this->actingAs($this->admin())
            ->get('/admin/products/'.$product->id.'/edit')
            ->assertOk()
            ->assertSee('Nama Lama');
    }

    public function test_admin_can_create_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Sepatu Lari',
            'is_trending' => 1,
        ]));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Sepatu Lari',
            'slug' => 'sepatu-lari',
            'is_trending' => true,
        ]);
    }

    public function test_admin_can_upload_one_photo(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Satu Foto',
            'images' => $this->fakeImages(1),
        ]))->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseCount('product_images', 1);
    }

    public function test_admin_can_upload_two_photos(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Dua Foto',
            'images' => $this->fakeImages(2),
        ]));

        $this->assertDatabaseCount('product_images', 2);
    }

    public function test_admin_can_upload_three_photos(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Tiga Foto',
            'images' => $this->fakeImages(3),
        ]));

        $this->assertDatabaseCount('product_images', 3);
    }

    public function test_uploading_four_photos_is_rejected(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Empat Foto',
            'images' => $this->fakeImages(4),
        ]));

        $response->assertSessionHasErrors('images');
        $this->assertDatabaseCount('product_images', 0);
    }

    public function test_non_image_file_is_rejected(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk File Salah',
            'images' => [UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf')],
        ]));

        $response->assertSessionHasErrors('images.0');
        $this->assertDatabaseCount('product_images', 0);
    }

    public function test_file_larger_than_two_megabytes_is_rejected(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Foto Besar',
            'images' => [UploadedFile::fake()->image('besar.jpg')->size(3000)],
        ]));

        $response->assertSessionHasErrors('images.0');
        $this->assertDatabaseCount('product_images', 0);
    }

    public function test_photo_is_stored_in_storage_and_path_saved_in_database(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Simpan Foto',
            'images' => $this->fakeImages(1),
        ]));

        $image = ProductImage::firstOrFail();

        $this->assertStringStartsWith('products/', $image->image_path);
        $this->assertDatabaseHas('product_images', ['image_path' => $image->image_path]);
        Storage::disk('public')->assertExists($image->image_path);
    }

    public function test_first_photo_becomes_primary_photo(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Foto Utama',
            'images' => $this->fakeImages(3),
        ]));

        $product = Product::where('name', 'Produk Foto Utama')->firstOrFail();

        $this->assertSame(3, $product->images()->count());
        $this->assertSame([1, 2, 3], $product->images()->orderBy('sort_order')->pluck('sort_order')->all());
        $this->assertSame($product->images()->orderBy('id')->first()->id, $product->primaryImage->id);
        $this->assertSame(1, $product->primaryImage->sort_order);
    }

    public function test_admin_can_edit_product(): void
    {
        $product = Product::factory()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($this->admin())->put('/admin/products/'.$product->id, $this->payload(
            $product->category,
            ['name' => 'Nama Baru', 'price' => 99000, 'is_viral' => 1],
        ));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nama Baru',
            'is_viral' => true,
        ]);
    }

    public function test_admin_can_delete_a_photo(): void
    {
        $category = Category::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Hapus Foto',
            'images' => $this->fakeImages(2),
        ]));

        $product = Product::where('name', 'Produk Hapus Foto')->firstOrFail();
        $image = $product->images()->orderBy('id')->first();
        $path = $image->image_path;

        $this->actingAs($admin)->put('/admin/products/'.$product->id, $this->payload($category, [
            'name' => 'Produk Hapus Foto',
            'delete_images' => [$image->id],
        ]));

        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame(1, $product->fresh()->images()->count());
    }

    public function test_admin_can_replace_a_photo(): void
    {
        $category = Category::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Ganti Foto',
            'images' => $this->fakeImages(1),
        ]));

        $product = Product::where('name', 'Produk Ganti Foto')->firstOrFail();
        $oldImage = $product->images()->first();
        $oldPath = $oldImage->image_path;

        $this->actingAs($admin)->put('/admin/products/'.$product->id, $this->payload($category, [
            'name' => 'Produk Ganti Foto',
            'delete_images' => [$oldImage->id],
            'images' => $this->fakeImages(1),
        ]));

        $product->refresh();

        $this->assertSame(1, $product->images()->count());
        $this->assertNotSame($oldPath, $product->images()->first()->image_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_total_photos_cannot_exceed_three_on_update(): void
    {
        $category = Category::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/products', $this->payload($category, [
            'name' => 'Produk Batas Tiga',
            'images' => $this->fakeImages(3),
        ]));

        $product = Product::where('name', 'Produk Batas Tiga')->firstOrFail();

        $response = $this->actingAs($admin)->put('/admin/products/'.$product->id, $this->payload($category, [
            'name' => 'Produk Batas Tiga',
            'images' => $this->fakeImages(1),
        ]));

        $response->assertSessionHasErrors('images');
        $this->assertSame(3, $product->fresh()->images()->count());
    }

    public function test_affiliate_url_must_be_valid(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post('/admin/products', $this->payload($category, [
            'name' => 'Produk URL Salah',
            'affiliate_url' => 'bukan-url',
        ]));

        $response->assertSessionHasErrors('affiliate_url');
    }

    public function test_non_admin_cannot_manage_products(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create();

        $this->actingAs($user)->get('/admin/products')->assertForbidden();
        $this->actingAs($user)->get('/admin/products/create')->assertForbidden();
        $this->actingAs($user)->post('/admin/products', [])->assertForbidden();
        $this->actingAs($user)->get('/admin/products/'.$product->id.'/edit')->assertForbidden();
        $this->actingAs($user)->put('/admin/products/'.$product->id, [])->assertForbidden();
        $this->actingAs($user)->delete('/admin/products/'.$product->id)->assertForbidden();
    }

    public function test_guest_is_redirected_from_products(): void
    {
        $this->get('/admin/products')->assertRedirect(route('admin.login'));
    }
}
