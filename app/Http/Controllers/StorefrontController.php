<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $query = Product::query()->with(['category', 'images']);

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $likeOperator) {
                $q->where('name', $likeOperator, '%'.$search.'%')
                    ->orWhere('description', $likeOperator, '%'.$search.'%')
                    ->orWhereHas('category', function ($q) use ($search, $likeOperator) {
                        $q->where('name', $likeOperator, '%'.$search.'%');
                    });
            });
        }

        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
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
            ->with(['category', 'images'])
            ->where('is_featured', true)
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $promos = Product::query()
            ->with(['category', 'images'])
            ->whereNotNull('promo_price_cents')
            ->orderByRaw($driver === 'pgsql'
                ? '(price_cents - promo_price_cents) desc nulls last'
                : '(price_cents - promo_price_cents) desc'
            )
            ->limit(6)
            ->get();

        $categories = Category::query()
            ->whereHas('products')
            ->orderBy('name')
            ->get();

        return view('storefront.index', [
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

    public function show(Product $product): View
    {
        $product->load(['category', 'images']);

        $related = Product::query()
            ->with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $categories = Category::query()
            ->whereHas('products')
            ->orderBy('name')
            ->get();

        return view('storefront.show', [
            'product' => $product,
            'related' => $related,
            'categories' => $categories,
        ]);
    }
}
