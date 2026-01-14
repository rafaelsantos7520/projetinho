<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Requisitos (Local)

- PHP 8.2+ com extensões PDO (obrigatório) e o driver do seu banco:
  - SQLite (padrão): `pdo_sqlite` + `sqlite3`
  - MySQL: `pdo_mysql`
- Node.js + npm (para build dos assets)

## Erro: "could not find driver" (SQLite)

Esse erro indica que o PHP está sem o driver PDO do SQLite habilitado. Como o projeto vem com `DB_CONNECTION=sqlite` e usa sessão/cache/filas no banco, a aplicação tenta acessar o arquivo `database/database.sqlite` e falha.

Verifique se o driver existe:

```bash
php -m | egrep -i 'pdo|sqlite'
```

Exemplos de correção:

```bash
sudo apt-get update
sudo apt-get install -y php8.2-sqlite3
```

Alternativa: mude `DB_CONNECTION` no `.env` para `mysql` e configure as credenciais do MySQL.

## Supabase (Postgres)

Você pode usar a URL do Supabase, mas o essencial no Laravel é: habilitar `pdo_pgsql` no PHP e configurar `DB_CONNECTION=pgsql` com SSL.

- Confirme o driver no PHP:

```bash
php -m | egrep -i 'pdo|pgsql'
```

- Em Debian/Ubuntu:

```bash
sudo apt-get update
sudo apt-get install -y php8.2-pgsql
```

- Se estiver usando `php artisan serve`, pare e suba de novo. Se estiver em nginx/apache com PHP-FPM, reinicie o serviço correspondente.

- Exemplo de `.env` (não comite credenciais):

```dotenv
DB_CONNECTION=pgsql
DB_HOST=your-project.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-password
DB_SSLMODE=require
```

Se estiver com `SESSION_DRIVER=database`, garanta que a tabela `sessions` existe:

```bash
php artisan migrate
```

## Usar um schema dedicado (Postgres)

Por padrão, o Postgres usa o schema `public`, então as migrations criam as tabelas lá. Se você quer isolar o ambiente de teste (ex.: `app_test`), faça:

1) Criar o schema no Supabase (SQL Editor):

```sql
create schema if not exists app_test;
```

2) Definir o schema no `.env`:

```dotenv
DB_SCHEMA=app_test
```

3) Limpar cache e rodar migrations (as tabelas vão para `app_test`):

```bash
php artisan config:clear
php artisan migrate
```

## Multi-tenancy por schema (Postgres)

Estratégia:

- **Landlord (public)**: guarda o cadastro de tenants (lojas) na tabela `tenants`.
- **Tenant (um schema por loja)**: guarda as tabelas da aplicação (users, sessions, catálogo, etc).

### Como funciona no request

O middleware [InitializeTenancy](file:///home/rafaeldev/projetos/projetinho/app/Http/Middleware/InitializeTenancy.php) resolve o tenant por:

- Domínio cadastrado no tenant (campo `domain`), ou
- Subdomínio (`loja1.seudominio.com`) quando `TENANCY_BASE_DOMAIN` está definido.

Em ambiente local, você também pode habilitar:

- Header `X-Tenant` (valor = `slug`)
- Query string `?tenant=<slug>`

Quando encontra, ele executa `SET search_path TO "<schema_do_tenant>", public`.

### Configuração recomendada (subdomínio)

No `.env`:

```dotenv
TENANCY_BASE_DOMAIN=seudominio.com
TENANCY_ADMIN_SUBDOMAIN_PREFIX=admin
TENANCY_ALLOW_HEADER_AND_QUERY=false
```

URLs:

- Catálogo: `https://loja-abc.seudominio.com`
- Admin: `https://admin.loja-abc.seudominio.com` (ou use `/admin` no mesmo host)

### Desenvolvimento local

Opção A (recomendada no dev): habilite query/header no `.env`:

```dotenv
TENANCY_ALLOW_HEADER_AND_QUERY=true
```

E acesse:

- `http://localhost:8000/?tenant=loja-abc`
- ou envie `X-Tenant: loja-abc`

Opção B (simular subdomínio): adicione no `/etc/hosts`:

```text
127.0.0.1 loja-abc.localtest
127.0.0.1 admin.loja-abc.localtest
```

E no `.env`:

```dotenv
TENANCY_BASE_DOMAIN=localtest
```

### Como provisionar uma loja

1) Rode as migrations do landlord (cria `tenants` no schema público):

```bash
php artisan migrate
```

2) Crie um tenant (isso cria o schema e roda as migrations do tenant):

```bash
php artisan tenants:create loja-abc
```

As migrations do tenant ficam em `database/migrations/tenant`.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
