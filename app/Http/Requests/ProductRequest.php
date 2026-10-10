<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => $this->generateUniqueSlug((string) $this->input('name')),
            ]);
        }

        $this->merge([
            'is_trending' => $this->boolean('is_trending'),
            'is_viral' => $this->boolean('is_viral'),
            'is_recommended' => $this->boolean('is_recommended'),
        ]);

        $images = array_values(array_filter(
            $this->file('images', []),
            fn ($file) => $file instanceof UploadedFile && $file->isValid(),
        ));

        $this->files->set('images', $images);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($this->route('product')),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'sold_count' => ['nullable', 'integer', 'min:0'],
            'affiliate_url' => ['required', 'url', 'max:2048'],
            'status' => ['required', Rule::in([Product::STATUS_ACTIVE, Product::STATUS_INACTIVE, Product::STATUS_DRAFT])],
            'is_trending' => ['boolean'],
            'is_viral' => ['boolean'],
            'is_recommended' => ['boolean'],
            'images' => ['nullable', 'array', 'max:'.Product::MAX_IMAGES],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $existingCount = 0;
            $product = $this->route('product');

            if ($product instanceof Product) {
                $existingCount = $product->images()->count();
            }

            $deletedCount = count($this->input('delete_images', []));
            $newCount = count($this->file('images', []));

            $remaining = Product::MAX_IMAGES - $existingCount + $deletedCount;

            if ($newCount > $remaining) {
                $validator->errors()->add(
                    'images',
                    'Setiap produk maksimal '.Product::MAX_IMAGES.' foto. Tersisa '.max($remaining, 0).' slot untuk foto baru.',
                );
            }
        });
    }

    /**
     * Build a slug that is unique against the products table.
     */
    protected function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $index = 1;

        $ignoreId = optional($this->route('product'))->getKey();

        while (Product::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$index++;
        }

        return $slug;
    }
}
