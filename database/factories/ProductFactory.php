<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->sentence(),
            'image' => null,
            'price' => fake()->numberBetween(10000, 5000000),
            'rating' => fake()->randomFloat(2, 3, 5),
            'sold_count' => fake()->numberBetween(0, 10000),
            'affiliate_url' => 'https://shopee.co.id/product/'.fake()->numberBetween(100000, 999999),
            'is_trending' => fake()->boolean(30),
            'is_viral' => fake()->boolean(20),
            'is_recommended' => fake()->boolean(25),
            'clicks' => fake()->numberBetween(0, 500),
            'status' => fake()->randomElement(['active', 'inactive', 'draft']),
        ];
    }
}
