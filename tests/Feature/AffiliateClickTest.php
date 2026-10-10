<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateClickTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function activeProduct(array $attributes = []): Product
    {
        return Product::factory()->create(array_merge([
            'status' => 'active',
            'clicks' => 0,
            'affiliate_url' => 'https://shopee.co.id/product/987654',
        ], $attributes));
    }

    public function test_click_is_recorded_and_redirects_to_affiliate_url(): void
    {
        $product = $this->activeProduct();

        $response = $this->get('/go/'.$product->slug);

        $response->assertRedirect('https://shopee.co.id/product/987654');

        $this->assertDatabaseHas('affiliate_clicks', [
            'product_id' => $product->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertDatabaseCount('affiliate_clicks', 1);
        $this->assertSame(1, $product->fresh()->clicks);
    }

    public function test_click_records_ip_address_and_user_agent(): void
    {
        $product = $this->activeProduct();

        $this->withHeaders(['User-Agent' => 'AffiliateBot/1.0'])->get('/go/'.$product->slug);

        $this->assertDatabaseHas('affiliate_clicks', [
            'product_id' => $product->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'AffiliateBot/1.0',
        ]);
    }

    public function test_click_records_timestamp(): void
    {
        $product = $this->activeProduct();

        $this->get('/go/'.$product->slug);

        $click = $product->affiliateClicks()->first();

        $this->assertNotNull($click);
        $this->assertNotNull($click->clicked_at);
    }

    public function test_inactive_product_is_rejected(): void
    {
        $product = $this->activeProduct(['status' => 'inactive']);

        $this->get('/go/'.$product->slug)->assertNotFound();

        $this->assertDatabaseCount('affiliate_clicks', 0);
    }

    public function test_draft_product_is_rejected(): void
    {
        $product = $this->activeProduct(['status' => 'draft']);

        $this->get('/go/'.$product->slug)->assertNotFound();

        $this->assertDatabaseCount('affiliate_clicks', 0);
    }

    public function test_unknown_product_returns_not_found(): void
    {
        $this->get('/go/produk-tidak-ada')->assertNotFound();

        $this->assertDatabaseCount('affiliate_clicks', 0);
    }

    public function test_product_without_affiliate_url_is_rejected(): void
    {
        $product = $this->activeProduct(['affiliate_url' => null]);

        $this->get('/go/'.$product->slug)->assertNotFound();

        $this->assertDatabaseCount('affiliate_clicks', 0);
    }

    public function test_affiliate_url_is_not_exposed_on_public_pages(): void
    {
        $product = $this->activeProduct();

        $this->get('/produk/'.$product->slug)->assertDontSee($product->affiliate_url);
        $this->get('/produk')->assertDontSee($product->affiliate_url);
    }

    public function test_multiple_clicks_are_all_recorded(): void
    {
        $product = $this->activeProduct();

        $this->get('/go/'.$product->slug);
        $this->get('/go/'.$product->slug);
        $this->get('/go/'.$product->slug);

        $this->assertDatabaseCount('affiliate_clicks', 3);
        $this->assertSame(3, $product->fresh()->clicks);
    }
}
