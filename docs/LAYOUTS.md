# Sistema de Layouts - Guia Completo

## Como Funciona

Este projeto usa **Blade Components** (Laravel 8+) em vez do sistema tradicional `@extends/@section`.

### Jeito Antigo (@extends)

```blade
{{-- layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    @yield('title')
</head>
<body>
    @include('partials.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('partials.footer')
</body>
</html>

{{-- pagina.blade.php --}}
@extends('layouts.app')

@section('title')
    Título da Página
@endsection

@section('content')
    <div class="container">
        Meu conteúdo aqui
    </div>
@endsection
```

### Jeito Novo (Components)

```blade
{{-- resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body>
    @if ($isTenantAdminArea)
        <x-tenant-admin.header />
        
        <div class="h-20"></div> <!-- Spacer para header fixo -->
        
        <main class="container">
            <x-ui.alerts />
            {{ $slot }}  <!-- Aqui vai o conteúdo -->
        </main>
    @endif
</body>
</html>

{{-- pagina.blade.php --}}
<x-layouts.app :title="'Minha Página'">
    <div class="meu-conteudo">
        Conteúdo aqui
    </div>
</x-layouts.app>
```

## Estrutura Atual do Projeto

### 1. Layout Principal
**Arquivo:** `resources/views/components/layouts/app.blade.php`

Este componente gerencia **TODAS** as páginas do sistema:
- Storefront (loja do cliente)
- Tenant Admin (painel administrativo)
- Platform (gerenciamento de tenants)

### 2. Headers

#### Admin Header
**Arquivo:** `resources/views/components/tenant-admin/header.blade.php`
- **Position:** `fixed top-0` (fixo no topo)
- **Height:** ~80px (h-20)
- **Z-index:** 50

**Problema Original:**
```blade
<!-- Header fixo -->
<header class="fixed top-0 w-full">...</header>

<!-- Main SEM spacer = conteúdo fica atrás do header -->
<main class="pt-8">
    Conteúdo escondido! ❌
</main>
```

**Solução Implementada:**
```blade
<!-- Header fixo -->
<x-tenant-admin.header />

<!-- Spacer = empurra o conteúdo para baixo -->
<div class="h-20"></div>

<!-- Main = agora fica visível -->
<main class="py-8">
    Conteúdo visível! ✅
</main>
```

### 3. Como Usar nas Suas Páginas

#### Exemplo Completo

```blade
<x-layouts.app :title="'Admin da Loja'" :subtitle="'Produtos'">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold">Lista de Produtos</h1>
        
        <div class="mt-6">
            {{-- Seu conteúdo aqui --}}
        </div>
    </div>
</x-layouts.app>
```

#### Props Disponíveis

- `title` - Título da página (meta tag)
- `subtitle` - Subtítulo (opcional)
- `full-width` - Remove max-width (para páginas full-width)
- `show-header` - true/false para mostrar o header

### 4. Anatomia do Layout Admin

```
┌─────────────────────────────────────┐
│  HEADER (fixed, h-20)               │ ← Position: fixed
├─────────────────────────────────────┤
│  SPACER (h-20)                      │ ← Compensa o header fixo
├─────────────────────────────────────┤
│                                     │
│  MAIN CONTENT AREA                  │ ← Conteúdo sempre visível
│  (max-w-7xl, px-4, py-8)            │
│                                     │
│  {{ $slot }}                        │ ← Seu conteúdo entra aqui
│                                     │
│                                     │
└─────────────────────────────────────┘
```

### 5. Estrutura de Diretórios

```
resources/views/
├── components/
│   ├── layouts/
│   │   └── app.blade.php ← Layout Master
│   ├── tenant-admin/
│   │   └── header.blade.php ← Header Admin
│   ├── storefront/
│   │   ├── header.blade.php ← Header Loja
│   │   └── footer.blade.php ← Footer Loja
│   └── ui/
│       └── alerts.blade.php ← Componente de alertas
├── tenant_admin/
│   ├── products/
│   │   ├── index.blade.php ← Usa <x-layouts.app>
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── categories/
│       └── index.blade.php
└── storefront/
    ├── index.blade.php
    └── show.blade.php
```

## Por que Components ao invés de @extends?

### Vantagens:

1. **Mais Moderno** - Padrão do Laravel 8+
2. **Mais Limpo** - Menos código boilerplate
3. **Type-safe** - Props tipadas
4. **Reutilizável** - Componentes podem ser nested
5. **Performance** - Compilação otimizada

### Exemplo Prático:

**Antigo (@extends):**
```blade
@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
    Conteúdo
@endsection

@push('scripts')
    <script>...</script>
@endpush
```

**Novo (Components):**
```blade
<x-layouts.app :title="'Produtos'">
    Conteúdo
    
    @push('scripts')
        <script>...</script>
    @endpush
</x-layouts.app>
```

## Troubleshooting

### Problema: Conteúdo ficando atrás do header

**Causa:** Header com `position: fixed` sem spacer

**Solução:**
```blade
<!-- Sempre adicione um spacer após headers fixos -->
<x-tenant-admin.header />
<div class="h-20"></div> <!-- Altura = altura do header -->
<main>...</main>
```

### Problema: Padding inconsistente

**Causa:** Mixing padding-top com spacer

**Solução:**
```blade
<!-- ❌ Errado -->
<div class="h-20"></div>
<main class="pt-32">...</main> <!-- Double padding! -->

<!-- ✅ Correto -->
<div class="h-20"></div>
<main class="py-8">...</main> <!-- Apenas padding vertical normal -->
```

## Referências

- [Blade Components - Laravel Docs](https://laravel.com/docs/blade#components)
- [Tailwind CSS - Spacing](https://tailwindcss.com/docs/padding)
- [CSS Position - MDN](https://developer.mozilla.org/en-US/docs/Web/CSS/position)
