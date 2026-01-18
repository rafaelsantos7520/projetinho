<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedTenantProducts extends Command
{
    protected $signature = 'tenant:seed-products 
                            {tenant : Slug do tenant} 
                            {--count=50 : Quantidade de produtos a criar}';

    protected $description = 'Cria produtos de teste para um tenant específico (imagens usam placeholder local)';

    protected array $productNames = [
        'Vestido Floral Verão',
        'Calça Jeans Skinny',
        'Blusa Cropped',
        'Saia Midi Plissada',
        'Conjunto Moletom',
        'Blazer Oversized',
        'Shorts Alfaiataria',
        'Macacão Longo',
        'Regata Basic',
        'Cardigan Tricot',
        'Vestido Midi Elegante',
        'Calça Pantalona',
        'Top Renda',
        'Casaco Inverno',
        'Jardineira Jeans',
        'Camisa Social',
        'Saia Longa Estampada',
        'Body Canelado',
        'Jaqueta Couro',
        'Vestido Tubinho',
        'Calça Legging',
        'Blusa Manga Bufante',
        'Shorts Jeans',
        'Vestido Longo Festa',
        'Cropped Decote V',
        'Calça Cargo',
        'Camiseta Estampada',
        'Conjunto Alfaiataria',
        'Vestido Chemise',
        'Saia Jeans',
        'Blusa Viscose',
        'Calça Moletom',
        'Top Esportivo',
        'Vestido Canelado',
        'Bermuda Ciclista',
        'Camisa Xadrez',
        'Saia Couro',
        'Blusa Gola Alta',
        'Calça Jogger',
        'Vestido Babado',
        'Short Saia',
        'Casaco Teddy',
        'Macacão Curto',
        'Regata Alça Fina',
        'Calça Flare',
        'Blusa Ombro a Ombro',
        'Saia Godê',
        'Vestido Tricot',
        'Conjunto Cropped Saia',
        'Blazer Alfaiataria',
    ];

    protected array $colors = [
        'Preto', 'Branco', 'Vermelho', 'Azul', 'Verde',
        'Rosa', 'Amarelo', 'Laranja', 'Roxo', 'Bege',
        'Marrom', 'Cinza', 'Nude', 'Vinho', 'Azul Marinho',
    ];

    protected array $sizes = [
        'PP', 'P', 'M', 'G', 'GG', 'XG',
        '34', '36', '38', '40', '42', '44', '46',
    ];

    protected array $descriptions = [
        'Peça versátil e confortável, perfeita para o dia a dia. Confeccionada em tecido de alta qualidade que proporciona durabilidade e elegância.',
        'Design moderno e sofisticado que combina com diversas ocasiões. Ideal para quem busca estilo sem abrir mão do conforto.',
        'Modelo exclusivo da nossa coleção. Possui acabamento impecável e detalhes que fazem toda a diferença no visual.',
        'Tendência da estação! Peça indispensável para compor looks incríveis. Tecido leve e confortável.',
        'Corte impecável que valoriza a silhueta feminina. Produzida com materiais sustentáveis e de primeira linha.',
        'Clássico atemporal que não pode faltar no guarda-roupa. Combina perfeitamente com outras peças da coleção.',
        'Peça fashion com caimento perfeito. Ideal para looks casuais ou mais elaborados.',
        'Conforto e estilo unidos em uma única peça. Perfeita para usar durante todo o dia.',
    ];

    public function handle(): int
    {
        $tenantSlug = $this->argument('tenant');
        $count = (int) $this->option('count');

        // Buscar o tenant
        $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();

        if (!$tenant) {
            $this->error("Tenant '{$tenantSlug}' não encontrado!");
            return self::FAILURE;
        }

        // Configurar a conexão do tenant
        $this->info("Conectando ao tenant: {$tenant->slug}...");
        
        config(['database.connections.tenant.database' => $tenant->schema]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        // Bind do tenant para os models
        app()->instance(\App\Models\Tenant::class, $tenant);

        // Buscar ou criar categorias
        $categories = $this->getOrCreateCategories();

        if ($categories->isEmpty()) {
            $this->error('Não foi possível criar/encontrar categorias!');
            return self::FAILURE;
        }

        $this->info("Criando {$count} produtos...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $created = 0;
        for ($i = 0; $i < $count; $i++) {
            try {
                $this->createProduct($categories, $i);
                $created++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Erro ao criar produto: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$created} produtos criados com sucesso!");

        return self::SUCCESS;
    }

    protected function getOrCreateCategories()
    {
        $categoryNames = [
            'Vestidos' => 1,
            'Calças' => 2,
            'Blusas' => 3,
            'Saias' => 4,
            'Conjuntos' => 5,
            'Acessórios' => 6,
        ];

        $categories = collect();

        foreach ($categoryNames as $name => $order) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'is_active' => true,
                    'sort_order' => $order,
                ]
            );
            $categories->push($category);
        }

        return $categories;
    }

    protected function createProduct($categories, $index): Product
    {
        $category = $categories->random();
        $name = $this->productNames[$index % count($this->productNames)];
        
        // Adicionar variação ao nome para evitar duplicatas
        if ($index >= count($this->productNames)) {
            $variation = ceil(($index + 1) / count($this->productNames));
            $name .= " v{$variation}";
        }

        // Gerar preço aleatório entre R$ 39,90 e R$ 599,90
        $priceCents = rand(3990, 59990);
        
        // 30% de chance de ter promoção
        $promoPriceCents = null;
        if (rand(1, 100) <= 30) {
            $discount = rand(10, 40); // 10% a 40% de desconto
            $promoPriceCents = (int) ($priceCents * (100 - $discount) / 100);
        }

        // 20% de chance de ser destaque
        $isFeatured = rand(1, 100) <= 20;

        $product = Product::create([
            'name' => $name,
            'category_id' => $category->id,
            'description' => $this->descriptions[array_rand($this->descriptions)],
            'price_cents' => $priceCents,
            'promo_price_cents' => $promoPriceCents,
            'is_active' => true,
            'is_featured' => $isFeatured,
            'color' => $this->colors[array_rand($this->colors)],
            'size' => $this->sizes[array_rand($this->sizes)],
            'rating_avg' => rand(30, 50) / 10, // 3.0 a 5.0
            'rating_count' => rand(0, 150),
        ]);

        return $product;
    }
}
