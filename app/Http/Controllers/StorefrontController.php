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
    public function index(Request $request): View
    {
        $storeSettings = StoreSettings::current();
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $with = ['category'];
        if (Product::productImagesTableExists()) {
            $with[] = 'images';
        }

        $query = Product::query()->with($with)->where('is_active', true);

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

        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category)->where('is_active', true);
            });
        }

        $minRating = $request->query('min_rating');
        if ($minRating !== null && $minRating !== '') {
            $query->where('rating_avg', '>=', (float) $minRating);
        }

        $sort = (string) $request->query('sort', 'newest');
        $sortMap = [
            'newest' => ['id', 'desc'],
            'price_asc' => ['price_cents', 'asc'],
            'price_desc' => ['price_cents', 'desc'],
            'rating_desc' => ['rating_avg', 'desc'],
        ];

        [$col, $dir] = $sortMap[$sort] ?? $sortMap['newest'];
        $query->orderBy($col, $dir)->orderBy('id', 'desc');

        $products = $query->limit(60)->get();

        $featured = Product::query()
            ->with($with)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

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
            'products' => $products,
            'featured' => $featured,
            'promos' => $promos,
            'categories' => $categories,
            'filters' => [
                'q' => $search,
                'category' => $category,
                'min_rating' => (string) ($minRating ?? ''),
                'sort' => $sort,
            ],
        ]);
    }

    public function show(string $productId): View
    {
        $storeSettings = StoreSettings::current();

        // Carregar categorias uma única vez para reutilizar
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        $relations = [];
        if (Product::productImagesTableExists()) {
            $relations[] = 'images';
        }

        // Buscar o produto manualmente dentro do contexto do tenant
        $product = Product::query()
            ->with($relations)
            ->where('id', $productId)
            ->where('is_active', true)
            ->firstOrFail();

        // Definir a categoria do cache carregado
        if ($categories->has($product->category_id)) {
            $product->setRelation('category', $categories->get($product->category_id));
        }

        // Se não tem a tabela de imagens, define uma coleção vazia
        if (! Product::productImagesTableExists()) {
            $product->setRelation('images', collect());
        }

        $related = Product::query()
            ->with($relations)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Definir categorias dos produtos relacionados do cache
        foreach ($related as $relatedProduct) {
            if ($categories->has($relatedProduct->category_id)) {
                $relatedProduct->setRelation('category', $categories->get($relatedProduct->category_id));
            }
        }

        return view('storefront.show', [
            'storeSettings' => $storeSettings,
            'product' => $product,
            'related' => $related,
            'categories' => $categories->values(),
        ]);
    }
}
