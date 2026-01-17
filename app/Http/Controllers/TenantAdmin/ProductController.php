<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        // Alterado para carregar view dashboard se for a intenção,
        // mas aqui vamos carregar a lista de produtos com relacionamento
        $products = Product::with('category')->orderBy('id', 'desc')->get();
        $categories = Category::query()->orderBy('name')->get();

        // O usuário quer que essa pagina seja um dashboard simples.
        // Vamos passar também estatísticas
        $stats = [
            'total_products' => $products->count(),
            'total_categories' => $categories->count(),
            'total_value' => $products->sum('price_cents'),
        ];

        return view('tenant_admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('tenant_admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->convertMoneyInputs($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['nullable', 'file', 'image', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'promo_price_cents' => ['nullable', 'integer', 'min:0'],
            'compare_at_price_cents' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'rating_avg' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Informe o nome do produto.',
            'name.max' => 'O nome do produto deve ter no máximo :max caracteres.',
            'images.array' => 'As imagens enviadas são inválidas.',
            'images.max' => 'Máximo de :max imagens por produto.',
            'images.*.image' => 'Cada arquivo deve ser uma imagem válida.',
            'images.*.max' => 'Cada imagem pode ter no máximo 5MB.',
            'image_url.url' => 'A URL da imagem principal é inválida.',
            'category_id.exists' => 'A categoria selecionada é inválida.',
            'price_cents.required' => 'Informe o preço do produto.',
            'price_cents.integer' => 'O preço do produto é inválido.',
            'price_cents.min' => 'O preço do produto não pode ser negativo.',
            'promo_price_cents.integer' => 'O preço promocional é inválido.',
            'promo_price_cents.min' => 'O preço promocional não pode ser negativo.',
            'compare_at_price_cents.integer' => 'O preço \"de\" é inválido.',
            'compare_at_price_cents.min' => 'O preço \"de\" não pode ser negativo.',
        ]);

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $images = $request->file('images') ?? [];
        $images = is_array($images) ? $images : [$images];
        $images = array_filter($images, fn ($f) => $f instanceof UploadedFile);
        ksort($images);
        $images = array_values($images);

        unset($validated['images']);

        $product = Product::query()->create($validated);

        if (count($images) > 0) {
            $urls = $this->storeProductImages($request, $images);

            foreach ($urls as $i => $url) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image_url' => $url,
                    'sort_order' => $i,
                ]);
            }

            $product->update(['image_url' => $urls[0] ?? $product->image_url]);
        }

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()
            ->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : [])
            ->with('status', 'Produto criado.');
    }

    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = Category::query()->orderBy('name')->get();

        return view('tenant_admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->convertMoneyInputs($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'replace_images' => ['nullable', 'array'],
            'replace_images.*' => ['nullable', 'file', 'image', 'max:5120'],
            'add_images' => ['nullable', 'array', 'max:3'],
            'add_images.*' => ['nullable', 'file', 'image', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'primary_image_id' => ['nullable', 'integer'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'promo_price_cents' => ['nullable', 'integer', 'min:0'],
            'compare_at_price_cents' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'rating_avg' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
        ], [
            'name.required' => 'Informe o nome do produto.',
            'name.max' => 'O nome do produto deve ter no máximo :max caracteres.',
            'replace_images.array' => 'Imagens para substituir são inválidas.',
            'replace_images.*.image' => 'Cada arquivo deve ser uma imagem válida.',
            'replace_images.*.max' => 'Cada imagem pode ter no máximo 5MB.',
            'add_images.array' => 'Imagens adicionais são inválidas.',
            'add_images.max' => 'Máximo de :max imagens adicionais por vez.',
            'add_images.*.image' => 'Cada arquivo deve ser uma imagem válida.',
            'add_images.*.max' => 'Cada imagem pode ter no máximo 5MB.',
            'remove_images.array' => 'Imagens para remover são inválidas.',
            'remove_images.*.integer' => 'Imagem para remover inválida.',
            'primary_image_id.integer' => 'Imagem principal inválida.',
            'image_url.url' => 'A URL da imagem principal é inválida.',
            'category_id.exists' => 'A categoria selecionada é inválida.',
            'price_cents.required' => 'Informe o preço do produto.',
            'price_cents.integer' => 'O preço do produto é inválido.',
            'price_cents.min' => 'O preço do produto não pode ser negativo.',
            'promo_price_cents.integer' => 'O preço promocional é inválido.',
            'promo_price_cents.min' => 'O preço promocional não pode ser negativo.',
            'compare_at_price_cents.integer' => 'O preço \"de\" é inválido.',
            'compare_at_price_cents.min' => 'O preço \"de\" não pode ser negativo.',
        ]);

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $removeIds = $request->input('remove_images', []);
        $removeIds = is_array($removeIds) ? $removeIds : [];

        $primaryImageId = $request->input('primary_image_id');
        $primaryImageId = is_numeric($primaryImageId) ? (int) $primaryImageId : null;

        $replace = $request->file('replace_images') ?? [];
        $replace = is_array($replace) ? $replace : [$replace];
        $replace = array_filter($replace, fn ($f) => $f instanceof UploadedFile);

        $add = $request->file('add_images') ?? [];
        $add = is_array($add) ? $add : [$add];
        $add = array_filter($add, fn ($f) => $f instanceof UploadedFile);
        ksort($add);
        $add = array_values($add);

        unset($validated['replace_images'], $validated['add_images'], $validated['remove_images'], $validated['primary_image_id']);

        if (count($removeIds) > 0) {
            $toRemove = $product->images()->whereIn('id', $removeIds)->get();
            foreach ($toRemove as $img) {
                $this->deleteLocalImageIfApplicable($img->image_url);
                $img->delete();
            }
        }

        if (count($replace) > 0) {
            $existing = $product->images()->whereIn('id', array_map('intval', array_keys($replace)))->get()->keyBy('id');

            foreach ($replace as $id => $file) {
                $id = (int) $id;
                $img = $existing->get($id);
                if (! $img) {
                    throw ValidationException::withMessages([
                        'replace_images' => ['Imagem para substituir inválida.'],
                    ]);
                }

                $urls = $this->storeProductImages($request, [$file]);
                $newUrl = $urls[0] ?? null;
                if (is_string($newUrl) && $newUrl !== '') {
                    $this->deleteLocalImageIfApplicable($img->image_url);
                    $img->update(['image_url' => $newUrl]);
                }
            }
        }

        if ($primaryImageId !== null) {
            $exists = $product->images()->where('id', $primaryImageId)->exists();
            if (! $exists) {
                throw ValidationException::withMessages([
                    'primary_image_id' => ['Imagem principal inválida.'],
                ]);
            }
        }

        if (count($add) > 0) {
            $existingCount = (int) $product->images()->count();
            if ($existingCount + count($add) > 3) {
                throw ValidationException::withMessages([
                    'add_images' => ['Máximo 3 imagens por produto. Remova alguma antes de enviar novas.'],
                ]);
            }

            $urls = $this->storeProductImages($request, $add);
            $start = (int) $product->images()->max('sort_order');
            $start = $start < 0 ? 0 : $start + 1;

            foreach ($urls as $i => $url) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image_url' => $url,
                    'sort_order' => $start + $i,
                ]);
            }
        }

        if ($primaryImageId !== null) {
            $ids = $product->images()->orderBy('sort_order')->orderBy('id')->pluck('id')->all();
            $ids = array_values(array_filter($ids, fn ($id) => (int) $id !== $primaryImageId));
            array_unshift($ids, $primaryImageId);

            foreach ($ids as $index => $id) {
                ProductImage::query()->where('id', $id)->update(['sort_order' => $index]);
            }
        }

        $product->update($validated);

        $first = $product->images()->orderBy('sort_order')->orderBy('id')->value('image_url');
        if (is_string($first) && $first !== '' && $product->image_url !== $first) {
            $product->update(['image_url' => $first]);
        }

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()
            ->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : [])
            ->with('status', 'Produto atualizado.');
    }

    public function duplicate(Product $product): RedirectResponse
    {
        $copy = $product->replicate();
        $copy->name = $copy->name.' (Cópia)';
        $copy->save();

        $images = $product->images()->get();
        foreach ($images as $image) {
            ProductImage::query()->create([
                'product_id' => $copy->id,
                'image_url' => $image->image_url,
                'sort_order' => $image->sort_order,
            ]);
        }

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;
        $routeParams = ['product' => $copy->id];
        if ($tenant) {
            $routeParams['tenant'] = $tenant->slug;
        }

        return redirect()
            ->route('tenant_admin.products.edit', $routeParams)
            ->with('status', 'Produto duplicado. Ajuste as informações antes de salvar.');
    }

    private function convertMoneyInputs(Request $request): void
    {
        $fields = ['price', 'promo_price', 'compare_at_price'];

        foreach ($fields as $field) {
            $formatted = $request->input($field.'_formatted');
            if ($formatted !== null && $formatted !== '') {
                // Remove pontos de milhar (Ex: 1.000,00 -> 1000,00)
                $clean = str_replace('.', '', $formatted);
                // Troca vírgula decimal por ponto (Ex: 1000,00 -> 1000.00)
                $clean = str_replace(',', '.', $clean);

                // Converte para centavos (R$ 150,00 -> 15000)
                $cents = (int) round((float) $clean * 100);

                $request->merge([$field.'_cents' => $cents]);
            }
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images()->get() as $img) {
            $this->deleteLocalImageIfApplicable($img->image_url);
        }

        $this->deleteLocalImageIfApplicable($product->image_url);
        $product->delete();

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()
            ->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : [])
            ->with('status', 'Produto excluído.');
    }

    private function storeProductImages(Request $request, array $images): array
    {
        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;
        $tenantSlug = $tenant?->slug ?? 'default';
        $disk = Storage::disk($this->productImagesDisk());

        $urls = [];
        foreach ($images as $file) {
            $filePath = $disk->putFile(
                'tenants/'.$tenantSlug.'/products',
                $file,
                'public'
            );

            $urls[] = $disk->url($filePath);
        }

        return $urls;
    }

    private function deleteLocalImageIfApplicable(?string $imageUrl): void
    {
        if (! is_string($imageUrl) || $imageUrl === '') {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return;
        }

        $prefix = '/storage/';
        if (! str_starts_with($path, $prefix)) {
            return;
        }

        $relative = ltrim(substr($path, strlen($prefix)), '/');
        if ($relative === '') {
            return;
        }

        Storage::disk($this->productImagesDisk())->delete($relative);
    }

    private function productImagesDisk(): string
    {
        return (string) config('filesystems.product_images_disk', 'public');
    }
}
