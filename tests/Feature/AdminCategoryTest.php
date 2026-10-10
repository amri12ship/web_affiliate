<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
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

    public function test_admin_can_view_categories_index(): void
    {
        Category::factory()->create(['name' => 'Elektronik']);

        $response = $this->actingAs($this->admin())->get('/admin/categories');

        $response->assertOk();
        $response->assertSee('Elektronik');
    }

    public function test_admin_can_create_category_with_automatic_slug(): void
    {
        $response = $this->actingAs($this->admin())->post('/admin/categories', [
            'name' => 'Fashion Pria',
            'description' => 'Pakaian pria',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Fashion Pria',
            'slug' => 'fashion-pria',
            'status' => 'active',
        ]);
    }

    public function test_automatic_slug_stays_unique_for_same_name(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Elektronik',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Elektronik',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('categories', ['slug' => 'elektronik']);
        $this->assertDatabaseHas('categories', ['slug' => 'elektronik-1']);
    }

    public function test_name_is_required(): void
    {
        $response = $this->actingAs($this->admin())->post('/admin/categories', [
            'name' => '',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_slug_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        $response = $this->actingAs($this->admin())->post('/admin/categories', [
            'name' => 'Elektronik Lain',
            'slug' => 'elektronik',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_image_must_be_valid_image_type(): void
    {
        $response = $this->actingAs($this->admin())->post('/admin/categories', [
            'name' => 'Gadget',
            'status' => 'active',
            'image' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_admin_can_store_category_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/categories', [
            'name' => 'Gadget',
            'status' => 'active',
            'image' => UploadedFile::fake()->image('gadget.jpg', 600, 400),
        ]);

        $category = Category::where('slug', 'gadget')->firstOrFail();

        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Lama', 'slug' => 'lama']);

        $response = $this->actingAs($this->admin())->put('/admin/categories/'.$category->id, [
            'name' => 'Baru',
            'slug' => 'lama',
            'description' => 'Deskripsi baru',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Baru',
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->delete('/admin/categories/'.$category->id);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $category = Category::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->admin())->patch('/admin/categories/'.$category->id.'/toggle');

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'status' => 'inactive']);
    }

    public function test_non_admin_user_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/categories')->assertForbidden();
        $this->actingAs($user)->post('/admin/categories', ['name' => 'X', 'status' => 'active'])->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/categories')->assertRedirect(route('admin.login'));
    }
}
