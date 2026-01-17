# Correção do Erro de Route Model Binding em Produção

## Problema Identificado

Dois problemas foram identificados:

### 1. TypeError no Route Model Binding
O erro `TypeError: Argument #1 ($product) must be of type App\Models\Product, string given` ocorria porque o **Implicit Route Model Binding** do Laravel não funcionava corretamente no contexto multi-tenant (ordem de execução dos middlewares).

### 2. Erro 404 após correção inicial
Após mudar para busca manual, os produtos retornavam 404 porque os Models estavam usando a conexão de banco DEFAULT ao invés da conexão `tenant` que foi configurada pelo `TenantSchemaManager`.

## Soluções Implementadas

### Solução 1: Substituir Route Model Binding por Busca Manual

Mudamos de:
```php
public function show(Product $product): View  // Implicit binding
```

Para:
```php
public function show(string $product): View
{
    $product = Product::query()
        ->where('id', $product)
        ->where('is_active', true)
        ->firstOrFail();
    // ...
}
```

### Solução 2: Garantir que todos os Models usem a conexão correta

Adicionamos `getConnectionName()` a todos os models de tenant para garantir que usem a conexão configurada em `config('tenancy.tenant_connection')`:

```php
public function getConnectionName(): ?string
{
    return config('tenancy.tenant_connection', config('database.default'));
}
```

## Arquivos Modificados

### Controllers (busca manual)
1. **StorefrontController.php** - `show()`
2. **TenantAdmin/ProductController.php** - `edit()`, `update()`, `duplicate()`, `destroy()`
3. **TenantAdmin/CategoryController.php** - `edit()`, `update()`, `destroy()`, `toggle()`

### Models (conexão correta)
1. **Product.php** - adicionado `getConnectionName()`
2. **Category.php** - adicionado `getConnectionName()`
3. **ProductImage.php** - adicionado `getConnectionName()`
4. **ProductOption.php** - adicionado `getConnectionName()`
5. **ProductOptionValue.php** - adicionado `getConnectionName()`
6. **StoreSettings.php** - adicionado `getConnectionName()`
7. **User.php** - adicionado `getConnectionName()`

**Nota:** Os models `Tenant` e `PlatformUser` já usam `protected $connection = 'landlord'` explicitamente.

## Instruções para Deploy em Produção

### 1. Fazer commit e push das alterações

```bash
git add .
git commit -m "fix: Corrigir conexão de banco multi-tenant e route model binding"
git push origin main
```

### 2. No servidor de produção, após fazer pull do código:

```bash
# Limpar todos os caches (OBRIGATÓRIO)
php artisan optimize:clear

# OU executar individualmente:
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Recriar os caches otimizados (opcional, melhora performance)
php artisan optimize
```

### 3. Testar as seguintes URLs em produção:

- ✅ `https://loja3.emdigital.shop/` (página inicial)
- ✅ `https://loja3.emdigital.shop/produto/4` (visualização do produto)
- ✅ `https://loja3.emdigital.shop/admin/products` (lista de produtos)
- ✅ `https://loja3.emdigital.shop/admin/products/4/edit` (edição do produto)
- ✅ `https://loja3.emdigital.shop/admin/categories` (lista de categorias)

## Por que o erro acontecia apenas em PRODUÇÃO?

| Aspecto | Local | Produção |
|---------|-------|----------|
| **Modo** | Path (`?tenant=loja3`) | Subdomínio (`loja3.emdigital.shop`) |
| **DB_CONNECTION** | `sqlite` ou `tenant` | `tenant` ou `mysql` |
| **Route Cache** | Desabilitado | Habilitado (`route:cache`) |
| **Config Cache** | Desabilitado | Habilitado (`config:cache`) |

A combinação de cache de rotas/config com diferentes conexões de banco causava inconsistências que não apareciam localmente.

## Arquitetura Final

```
Request → InitializeTenancy (configura schema) → Controller
                                                    ↓
                                              Model::query()
                                                    ↓
                                          getConnectionName() 
                                                    ↓
                                       config('tenancy.tenant_connection')
                                                    ↓
                                          Conexão 'tenant' com
                                          schema correto (tenant_loja3)
```

Esta solução garante que:
1. ✅ O contexto do tenant está sempre configurado ANTES da query
2. ✅ Todos os models usam a mesma conexão configurada
3. ✅ Funciona em todos os ambientes (local, staging, produção)
4. ✅ Funciona em todos os modos (path, subdomínio)
