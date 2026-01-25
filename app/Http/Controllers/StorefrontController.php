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
     * Página inicial da loja (versão React)
     */
    public function indexReact(): View
    {
        $storeSettings = StoreSettings::current();

        $with = ['category'];
        if (Product::productImagesTableExists()) {
            $with[] = 'images';
        }

        $featured = Product::query()
            ->with($with)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

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

        $newest = Product::query()
            ->with($with)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Obter o tenant atual para gerar URLs corretas
        $tenant = app(\App\Models\Tenant::class);
        $tenantSlug = $tenant->slug;

        // Formatar dados para React com URLs corretas (incluindo o tenant para rotas de subdomínio)
        $formatProduct = function ($product) use ($tenantSlug) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price_cents' => $product->price_cents,
                'promo_price_cents' => $product->promo_price_cents,
                'primary_image_url' => $product->primary_image_url,
                'url' => route('storefront.product', ['tenant' => $tenantSlug, 'product' => $product->slug ?? $product->id]),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
            ];
        };

        $formatCategory = function ($category) use ($tenantSlug) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image_url' => $category->image_url,
                'url' => route('storefront.products', ['tenant' => $tenantSlug, 'category' => $category->slug ?? $category->name]),
            ];
        };

        $pageData = [
            'baseUrl' => url('/'),
            'productsUrl' => route('storefront.products', ['tenant' => $tenantSlug]),
            'logoUrl' => $storeSettings->logo_url ?? null,
            'banners' => array_values(array_filter([
                $storeSettings->banner_1_url ?? null,
                $storeSettings->banner_2_url ?? null,
                $storeSettings->banner_3_url ?? null,
            ])),
            'categories' => $categories->map($formatCategory)->values()->toArray(),
            'featured' => $featured->map($formatProduct)->values()->toArray(),
            'promos' => $promos->map($formatProduct)->values()->toArray(),
            'newest' => $newest->map($formatProduct)->values()->toArray(),
        ];

        return view('storefront.index-react', [
            'pageData' => $pageData,
        ]);
    }

    /**
     * Página de listagem de produtos com filtros (React)
     */
    public function products(Request $request): View
    {
        $storeSettings = StoreSettings::current();
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $tenant = app(\App\Models\Tenant::class);
        $tenantSlug = $tenant->slug;

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

        // Formatar dados para React
        $formatProduct = function ($product) use ($tenantSlug) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price_cents' => $product->price_cents,
                'promo_price_cents' => $product->promo_price_cents,
                'primary_image_url' => $product->primary_image_url,
                'url' => route('storefront.product', ['tenant' => $tenantSlug, 'product' => $product->slug ?? $product->id]),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
            ];
        };

        $formatCategory = function ($cat) use ($tenantSlug) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'products_count' => $cat->products_count ?? 0,
                'url' => route('storefront.products', ['tenant' => $tenantSlug, 'category' => $cat->slug ?? $cat->name]),
            ];
        };

        $pageData = [
            'baseUrl' => url('/'),
            'productsUrl' => route('storefront.products', ['tenant' => $tenantSlug]),
            'logoUrl' => $storeSettings->logo_url ?? null,
            'categories' => $categories->map($formatCategory)->values()->toArray(),
            'products' => $products->map($formatProduct)->values()->toArray(),
            'pagination' => [
                'total' => $products->total(),
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
                'perPage' => $products->perPage(),
            ],
            'filters' => [
                'q' => $search,
                'category' => $category,
                'min_rating' => (string) ($minRating ?? ''),
                'sort' => $sort,
            ],
            'selectedCategory' => $selectedCategory ? [
                'id' => $selectedCategory->id,
                'name' => $selectedCategory->name,
                'slug' => $selectedCategory->slug,
            ] : null,
        ];

        return view('storefront.products-react', [
            'pageData' => $pageData,
        ]);
    }

    /**
     * Página de detalhes do produto (React)
     */
    public function show(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }

        $storeSettings = StoreSettings::current();
        $tenant = app(\App\Models\Tenant::class);
        $tenantSlug = $tenant->slug;

        $relations = ['category'];
        if (Product::productImagesTableExists()) {
            $relations[] = 'images';
        }

        $product->load($relations);

        if (! Product::productImagesTableExists()) {
            $product->setRelation('images', collect());
        }

        // Produtos relacionados
        $related = Product::query()
            ->with($relations)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Categorias para navegação
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        // Formatar produto para React
        $formatProduct = function ($p) use ($tenantSlug) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'description' => $p->description,
                'price_cents' => $p->price_cents,
                'promo_price_cents' => $p->promo_price_cents,
                'primary_image_url' => $p->primary_image_url,
                'url' => route('storefront.product', ['tenant' => $tenantSlug, 'product' => $p->slug ?? $p->id]),
                'images' => $p->images->map(function ($img) {
                    return ['url' => $img->url ?? $img->image_url ?? ''];
                })->values()->toArray(),
                'category' => $p->category ? [
                    'id' => $p->category->id,
                    'name' => $p->category->name,
                    'slug' => $p->category->slug,
                    'url' => route('storefront.products', ['tenant' => $tenantSlug, 'category' => $p->category->slug ?? $p->category->name]),
                ] : null,
            ];
        };

        $formatCategory = function ($cat) use ($tenantSlug) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'url' => route('storefront.products', ['tenant' => $tenantSlug, 'category' => $cat->slug ?? $cat->name]),
            ];
        };

        $pageData = [
            'baseUrl' => url('/'),
            'productsUrl' => route('storefront.products', ['tenant' => $tenantSlug]),
            'logoUrl' => $storeSettings->logo_url ?? null,
            'categories' => $categories->map($formatCategory)->values()->toArray(),
            'product' => $formatProduct($product),
            'related' => $related->map($formatProduct)->values()->toArray(),
        ];

        return view('storefront.show-react', [
            'pageData' => $pageData,
        ]);
    }
}
