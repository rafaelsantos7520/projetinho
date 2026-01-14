<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'image_url' => ['nullable', 'url', 'max:2048'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'promo_price_cents' => ['nullable', 'integer', 'min:0'],
            'compare_at_price_cents' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'rating_avg' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        Product::query()->create($validated);

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()
            ->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : [])
            ->with('status', 'Produto criado.');
    }

    public function edit(Product $product): View
    {
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
            'image_url' => ['nullable', 'url', 'max:2048'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'promo_price_cents' => ['nullable', 'integer', 'min:0'],
            'compare_at_price_cents' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'rating_avg' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $product->update($validated);

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()
            ->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : [])
            ->with('status', 'Produto atualizado.');
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
        $product->delete();

        $tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;

        return redirect()
            ->route('tenant_admin.products.index', $tenant ? ['tenant' => $tenant->slug] : [])
            ->with('status', 'Produto excluído.');
    }
}
