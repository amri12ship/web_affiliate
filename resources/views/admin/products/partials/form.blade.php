@php($editing = $product->exists)
@php($existingImages = $editing ? $product->images : collect())

@if ($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200" role="alert">
        <p class="font-medium">Periksa kembali data yang Anda isi.</p>
        <ul class="mt-1 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}"
    enctype="multipart/form-data"
    class="space-y-8"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Produk <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('name') border-red-400 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori <span class="text-red-500">*</span></label>
                <select
                    id="category_id"
                    name="category_id"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('category_id') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                >
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('description') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="price" class="mb-1.5 block text-sm font-medium text-slate-700">Harga <span class="text-red-500">*</span></label>
                    <input
                        id="price"
                        name="price"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('price', $product->price) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('price') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                    >
                    @error('price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rating" class="mb-1.5 block text-sm font-medium text-slate-700">Rating (0-5)</label>
                    <input
                        id="rating"
                        name="rating"
                        type="number"
                        step="0.01"
                        min="0"
                        max="5"
                        value="{{ old('rating', $product->rating) }}"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('rating') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                    >
                    @error('rating')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sold_count" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah Terjual</label>
                    <input
                        id="sold_count"
                        name="sold_count"
                        type="number"
                        min="0"
                        value="{{ old('sold_count', $product->sold_count) }}"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('sold_count') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                    >
                    @error('sold_count')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="affiliate_url" class="mb-1.5 block text-sm font-medium text-slate-700">Affiliate URL <span class="text-red-500">*</span></label>
                <input
                    id="affiliate_url"
                    name="affiliate_url"
                    type="url"
                    value="{{ old('affiliate_url', $product->affiliate_url) }}"
                    placeholder="https://shopee.co.id/..."
                    required
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('affiliate_url') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                >
                @error('affiliate_url')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="space-y-5">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('status') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                >
                    <option value="active" @selected(old('status', $product->status ?: 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Inactive</option>
                    <option value="draft" @selected(old('status', $product->status) === 'draft')>Draft</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-3 rounded-xl border border-slate-200 p-4">
                <p class="text-sm font-medium text-slate-700">Label</p>
                <label class="flex items-center gap-2.5 text-sm text-slate-600">
                    <input type="checkbox" name="is_trending" value="1" @checked(old('is_trending', $product->is_trending)) class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    Trending
                </label>
                <label class="flex items-center gap-2.5 text-sm text-slate-600">
                    <input type="checkbox" name="is_viral" value="1" @checked(old('is_viral', $product->is_viral)) class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    Viral
                </label>
                <label class="flex items-center gap-2.5 text-sm text-slate-600">
                    <input type="checkbox" name="is_recommended" value="1" @checked(old('is_recommended', $product->is_recommended)) class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    Recommended
                </label>
            </div>
        </div>
    </div>

    <!-- Photos -->
    <div class="rounded-2xl border border-slate-200 p-5">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Foto Produk</h3>
                <p class="text-xs text-slate-500">Maksimal 3 foto. Foto 1 menjadi foto utama. JPG, JPEG, PNG, WEBP, maks 2 MB.</p>
            </div>
        </div>

        @error('images')
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-2.5 text-sm text-red-700 ring-1 ring-red-200">{{ $message }}</div>
        @enderror
        @error('images.*')
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-2.5 text-sm text-red-700 ring-1 ring-red-200">{{ $message }}</div>
        @enderror

        @if ($existingImages->isNotEmpty())
            <div class="mb-5">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Foto Tersimpan</p>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($existingImages as $image)
                        <label class="group relative block cursor-pointer overflow-hidden rounded-xl border border-slate-200">
                            <img src="{{ $image->url() }}" alt="Foto produk" class="h-32 w-full object-cover">
                            @if ($image->sort_order === 1)
                                <span class="absolute left-2 top-2 rounded-full bg-orange-600 px-2 py-0.5 text-[10px] font-semibold uppercase text-white">Utama</span>
                            @endif
                            <span class="absolute inset-x-0 bottom-0 flex items-center gap-2 bg-slate-900/70 px-2.5 py-2 text-xs text-white">
                                <input
                                    type="checkbox"
                                    name="delete_images[]"
                                    value="{{ $image->id }}"
                                    class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500"
                                >
                                Hapus foto
                            </label>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @for ($slot = 1; $slot <= 3; $slot++)
                <div class="rounded-xl border border-dashed border-slate-300 p-3">
                    <p class="mb-2 text-xs font-medium text-slate-600">Foto {{ $slot }}{{ $slot === 1 ? ' (Utama)' : '' }}</p>
                    <div class="mb-2 flex h-28 items-center justify-center overflow-hidden rounded-lg bg-slate-50">
                        <img data-preview="{{ $slot }}" src="" alt="Preview {{ $slot }}" class="hidden h-full w-full object-cover">
                        <span data-placeholder="{{ $slot }}" class="text-xs text-slate-400">Belum dipilih</span>
                    </div>
                    <input
                        type="file"
                        name="images[]"
                        data-slot="{{ $slot }}"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="block w-full cursor-pointer rounded-lg border border-slate-300 text-xs text-slate-600 file:mr-2 file:cursor-pointer file:border-0 file:bg-slate-100 file:px-2.5 file:py-1.5 file:text-xs file:font-medium file:text-slate-700 hover:file:bg-slate-200"
                    >
                    <button type="button" data-clear="{{ $slot }}" class="mt-2 hidden w-full rounded-lg border border-slate-300 px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">Hapus pilihan</button>
                </div>
            @endfor
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">
            {{ $editing ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
    </div>
</form>

<script>
    (function () {
        document.querySelectorAll('input[type="file"][data-slot]').forEach(function (input) {
            var slot = input.dataset.slot;
            var preview = document.querySelector('[data-preview="' + slot + '"]');
            var placeholder = document.querySelector('[data-placeholder="' + slot + '"]');
            var clear = document.querySelector('[data-clear="' + slot + '"]');

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (! file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    clear.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });

            clear && clear.addEventListener('click', function () {
                input.value = '';
                preview.src = '';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                clear.classList.add('hidden');
            });
        });
    })();
</script>
