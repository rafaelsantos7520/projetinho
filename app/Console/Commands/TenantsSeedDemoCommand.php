<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tenant;
use App\Tenancy\TenantSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TenantsSeedDemoCommand extends Command
{
    protected $signature = 'tenants:seed-demo
        {--tenants= : Slugs separados por vírgula (default: todos)}
        {--products=10 : Quantidade mínima de produtos por tenant}
        {--categories=6 : Quantidade mínima de categorias por tenant}';

    protected $description = 'Popula tenants com categorias e produtos de demonstração.';

    public function handle(TenantSchemaManager $schemaManager): int
    {
        $tenantOption = $this->option('tenants');
        $slugs = [];
        if (is_string($tenantOption) && trim($tenantOption) !== '') {
            $slugs = array_values(array_filter(array_map('trim', explode(',', $tenantOption))));
        }

        $productsTarget = max(0, (int) $this->option('products'));
        $categoriesTarget = max(0, (int) $this->option('categories'));

        $query = Tenant::query()->orderBy('slug');
        if (count($slugs) > 0) {
            $query->whereIn('slug', $slugs);
        }

        $tenants = $query->get();
        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant encontrado.');

            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->line('Seed tenant: '.$tenant->slug);
            $schemaManager->setSearchPath($tenant->schema, true);

            $this->seedCategories($tenant->slug, $categoriesTarget);
            $this->seedProducts($tenant->slug, $productsTarget);
        }

        $this->info('Seed concluído.');

        return self::SUCCESS;
    }

    private function seedCategories(string $tenantSlug, int $target): void
    {
        $base = [
            'Camisetas',
            'Vestidos',
            'Calçados',
            'Acessórios',
            'Jeans',
            'Promoções',
            'Infantil',
            'Fitness',
        ];

        $existing = (int) Category::query()->count();
        $needed = max(0, $target - $existing);

        if ($needed === 0) {
            return;
        }

        $names = array_slice($base, 0, min(count($base), $needed));

        while (count($names) < $needed) {
            $names[] = 'Categoria '.Str::upper(Str::random(4));
        }

        foreach ($names as $name) {
            $slug = Str::slug($name);
            Category::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'image_url' => 'https://picsum.photos/seed/'.urlencode($tenantSlug.'-'.$slug).'/200/200',
                ]
            );
        }
    }

    private function seedProducts(string $tenantSlug, int $target): void
    {
        $existing = (int) Product::query()->count();
        $needed = max(0, $target - $existing);

        if ($needed === 0) {
            return;
        }

        $categories = Category::query()->inRandomOrder()->limit(max(1, min(20, $target)))->get();
        if ($categories->isEmpty()) {
            return;
        }

        for ($i = 0; $i < $needed; $i++) {
            $category = $categories->random();

            $price = random_int(1990, 39990);
            $hasPromo = random_int(0, 100) < 45;
            $promo = $hasPromo ? max(990, $price - random_int(300, 4000)) : null;
            $compare = $hasPromo ? $price + random_int(500, 8000) : $price;

            $name = $category->name.' '.Str::upper(Str::random(3)).'-'.random_int(10, 99);

            $product = Product::query()->create([
                'name' => $name,
                'category_id' => $category->id,
                'is_featured' => random_int(0, 100) < 30,
                'price_cents' => $price,
                'promo_price_cents' => $promo,
                'compare_at_price_cents' => $compare,
                'description' => 'Produto de demonstração para '.$tenantSlug.'.',
                'rating_avg' => random_int(35, 50) / 10,
                'rating_count' => random_int(0, 500),
            ]);

            $urls = [];
            for ($img = 1; $img <= 3; $img++) {
                $urls[] = 'https://picsum.photos/seed/'.urlencode($tenantSlug.'-'.$product->id.'-'.$img).'/800/800';
            }

            foreach ($urls as $idx => $url) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image_url' => $url,
                    'sort_order' => $idx,
                ]);
            }

            $product->update(['image_url' => $urls[0]]);
        }
    }
}

