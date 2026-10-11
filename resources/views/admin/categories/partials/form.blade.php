@php($editing = $category->exists)

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
    action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    enctype="multipart/form-data"
    class="space-y-6"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Kategori <span class="text-red-500">*</span></label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $category->name) }}"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">Slug <span class="text-red-500">*</span></label>
                <input
                    id="slug"
                    name="slug"
                    type="text"
                    value="{{ old('slug', $category->slug) }}"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 font-mono text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('slug') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                >
                <p class="mt-1 text-xs text-slate-500">Otomatis dibuat dari nama. Akan ditambah angka jika sudah dipakai.</p>
                @error('slug')
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
                >{{ old('description', $category->description) }}</textarea>
                @error('description')
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
                    <option value="active" @selected(old('status', $category->status ?: 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $category->status) === 'inactive')>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="image" class="mb-1.5 block text-sm font-medium text-slate-700">Gambar</label>
                <div class="mb-3 flex h-32 w-full items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50">
                    <img
                        id="image-preview"
                        src="{{ $category->imageUrl() ?? '' }}"
                        alt="Preview"
                        class="h-full w-full object-cover {{ $category->image ? '' : 'hidden' }}"
                    >
                    <span id="image-placeholder" class="text-xs text-slate-400 {{ $category->image ? 'hidden' : '' }}">Belum ada gambar</span>
                </div>
                <input
                    id="image"
                    name="image"
                    type="file"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="block w-full cursor-pointer rounded-lg border border-slate-300 text-sm text-slate-600 file:mr-3 file:cursor-pointer file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200 @error('image') border-red-400 @enderror"
                >
                <p class="mt-1 text-xs text-slate-500">JPG, JPEG, PNG, atau WEBP. Maks 2 MB.</p>
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <a
            href="{{ route('admin.categories.index') }}"
            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
        >
            Batal
        </a>
        <button
            type="submit"
            class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700"
        >
            {{ $editing ? 'Simpan Perubahan' : 'Simpan Kategori' }}
        </button>
    </div>
</form>

<script>
    (function () {
        var nameInput = document.getElementById('name');
        var slugInput = document.getElementById('slug');
        var imageInput = document.getElementById('image');
        var preview = document.getElementById('image-preview');
        var placeholder = document.getElementById('image-placeholder');
        var editing = @json($editing);

        function slugify(value) {
            return value.toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        if (nameInput && slugInput) {
            nameInput.addEventListener('input', function () {
                if (editing && slugInput.dataset.touched) return;
                slugInput.value = slugify(nameInput.value);
            });
            slugInput.addEventListener('input', function () {
                slugInput.dataset.touched = '1';
            });
        }

        if (imageInput && preview) {
            imageInput.addEventListener('change', function () {
                var file = imageInput.files && imageInput.files[0];
                if (! file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder && placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
    })();
</script>
