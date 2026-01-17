<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('tenant_admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('tenant_admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image_url'] = $this->storeCategoryImage($request);
        }

        unset($validated['image']);

        Category::create($validated);

        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', 'Categoria criada com sucesso!');
    }

    public function edit(string $categoryId)
    {
        $category = Category::query()->where('id', $categoryId)->firstOrFail();

        return view('tenant_admin.categories.edit', compact('category'));
    }

    public function update(Request $request, string $categoryId)
    {
        // Buscar a categoria dentro do contexto do tenant
        $category = Category::query()->where('id', $categoryId)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->boolean('remove_image')) {
            $this->deleteLocalImageIfApplicable($category->image_url);
            $validated['image_url'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteLocalImageIfApplicable($category->image_url);
            $validated['image_url'] = $this->storeCategoryImage($request);
        }

        unset($validated['image'], $validated['remove_image']);

        $category->update($validated);

        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', 'Categoria atualizada com sucesso!');
    }

    public function destroy(string $categoryId)
    {
        $category = Category::query()->where('id', $categoryId)->firstOrFail();

        $this->deleteLocalImageIfApplicable($category->image_url);
        $category->delete();

        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', 'Categoria removida com sucesso!');
    }

    public function toggle(string $categoryId)
    {
        $category = Category::query()->where('id', $categoryId)->firstOrFail();

        // Validação: não pode desativar se tem produtos ativos
        if ($category->is_active) {
            $activeProductCount = $category->products()->where('is_active', true)->count();
            
            if ($activeProductCount > 0) {
                return redirect()->back()
                    ->with('error', "Não é possível desativar \"{$category->name}\" porque existem {$activeProductCount} produto(s) ativo(s) nesta categoria. Desative os produtos primeiro.");
            }
        }

        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'ativada' : 'desativada';
        
        return redirect()->route('tenant_admin.categories.index', ['tenant' => app(Tenant::class)->slug])
            ->with('status', "Categoria \"{$category->name}\" {$status} com sucesso!");
    }

    private function storeCategoryImage(Request $request): string
    {
        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;
        $tenantSlug = $tenant?->slug ?? 'default';

        $filePath = $request->file('image')->storePublicly(
            'tenants/'.$tenantSlug.'/categories',
            'public'
        );

        return Storage::disk('public')->url($filePath);
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

        Storage::disk('public')->delete($relative);
    }
}
