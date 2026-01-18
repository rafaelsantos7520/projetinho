<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    /**
     * Página inicial da loja - Vitrine com destaques
     * Sem filtros, apenas produtos e categorias em destaque
     */
    public function index(): View
    {
        $storeSettings = StoreSettings::current();

        $with = ['category'];
        if (Product::productImagesTableExists()) {
            $with[] = 'images';
        }

        // Produtos em destaque (featured)
        $featured = Product::query()
            ->with($with)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        // Produtos em promoção
        $driver = DB::connection()->getDriverName();
        $promos = Product::query()
            ->with($with)
            ->whereNotNull('promo_price_cents')
            ->where('is_active', true)
            ->orderByRaw($driver === 'pgsql'
                ? '(price_cents - promo_price_cents) desc nulls last'
                : '(price_cents - promo_price_cents) desc'
            )
            ->limit(6)
            ->get();

        // Últimos produtos adicionados (novidades)
        $newest = Product::query()
            ->with($with)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        // Categorias ativas com produtos
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('storefront.index', [
            'storeSettings' => $storeSettings,
            'featured' => $featured,
            'promos' => $promos,
            'newest' => $newest,
            'categories' => $categories,
        ]);
    }

    /**
     * Página de listagem de produtos com filtros
     * Usada para busca, filtro por categoria, etc.
     */
    public function products(Request $request): View
    {
        $storeSettings = StoreSettings::current();
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $with = ['category'];
        if (Product::productImagesTableExists()) {
            $with[] = 'images';
        }

        $query = Product::query()->with($with)->where('is_active', true);

        // Filtro de busca
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $likeOperator) {
                $q->where('name', $likeOperator, '%'.$search.'%')
                    ->orWhere('description', $likeOperator, '%'.$search.'%')
                    ->orWhereHas('category', function ($q) use ($search, $likeOperator) {
                        $q->where('name', $likeOperator, '%'.$search.'%')->where('is_active', true);
                    });
            });
        }

        // Filtro por categoria (por slug ou nome)
        $category = trim((string) $request->query('category', ''));
        $selectedCategory = null;
        if ($category !== '') {
            $selectedCategory = Category::query()
                ->where('is_active', true)
                ->where(function ($q) use ($category) {
                    $q->where('slug', $category)->orWhere('name', $category);
                })
                ->first();

            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        // Filtro por avaliação mínima
        $minRating = $request->query('min_rating');
        if ($minRating !== null && $minRating !== '') {
            $query->where('rating_avg', '>=', (float) $minRating);
        }

        // Filtro por faixa de preço
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price_cents', '>=', (int) $minPrice * 100);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price_cents', '<=', (int) $maxPrice * 100);
        }

        // Ordenação
        $sort = (string) $request->query('sort', 'newest');
        $sortMap = [
            'newest' => ['id', 'desc'],
            'price_asc' => ['price_cents', 'asc'],
            'price_desc' => ['price_cents', 'desc'],
            'rating_desc' => ['rating_avg', 'desc'],
            'name_asc' => ['name', 'asc'],
        ];

        [$col, $dir] = $sortMap[$sort] ?? $sortMap['newest'];
        $query->orderBy($col, $dir)->orderBy('id', 'desc');

        // Paginação
        $products = $query->paginate(24)->withQueryString();

        // Categorias para filtro
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('storefront.products', [
            'storeSettings' => $storeSettings,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'filters' => [
                'q' => $search,
                'category' => $category,
                'min_rating' => (string) ($minRating ?? ''),
                'min_price' => (string) ($minPrice ?? ''),
                'max_price' => (string) ($maxPrice ?? ''),
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Página de detalhes do produto
     */
    public function show(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }

        $storeSettings = StoreSettings::current();

        $relations = ['category'];
        if (Product::productImagesTableExists()) {
            $relations[] = 'images';
        }

        // Load relations
        $product->load($relations);

        // Se não tem a tabela de imagens, define uma coleção vazia
        if (! Product::productImagesTableExists()) {
            $product->setRelation('images', collect());
        }

        // Produtos relacionados (mesma categoria)
        $related = Product::query()
            ->with($relations)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Categorias para o header/navegação
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('storefront.show', [
            'storeSettings' => $storeSettings,
            'product' => $product,
            'related' => $related,
            'categories' => $categories,
        ]);
    }
}
