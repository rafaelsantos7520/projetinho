# Deploy (revisão)

## Pré-requisitos
- PHP 8.2+
- Banco MySQL
- Variáveis de ambiente configuradas (`.env`)

## Passos mínimos
1. Rodar migrations do landlord:

```bash
php artisan migrate
```

2. Criar symlink para uploads públicos:

```bash
php artisan storage:link
```

3. Criar um admin da plataforma (para usar `/platform`):

```bash
php artisan platform:make-admin admin@site.com --name="Admin"
```

4. Criar uma loja (self-serve):
- Acesse `GET /start` e complete o formulário.

5. Rodar migrations dos tenants existentes quando houver novos campos:

```bash
php artisan tenants:migrate --force
```

## Observações para MySQL (Hostinger)
- Estratégia: **um banco por tenant** (o campo `tenants.schema` guarda o nome do banco do tenant).
- Crie um banco “landlord” (ex.: `projetinho_landlord`) e aponte `DB_LANDLORD_DATABASE` para ele.
- Para criar novos tenants automaticamente, o usuário do banco precisa permissão para `CREATE DATABASE`; se não tiver, crie o banco do tenant no painel e depois rode `php artisan tenants:create --no-migrate` e `php artisan tenants:migrate --tenant=<slug> --force`.

Exemplo de `.env` (não comite credenciais):

```dotenv
DB_CONNECTION=tenant

DB_TENANT_DRIVER=mysql
DB_TENANT_HOST=localhost
DB_TENANT_PORT=3306
DB_TENANT_DATABASE=projetinho_landlord
DB_TENANT_USERNAME=seu_usuario
DB_TENANT_PASSWORD=sua_senha

DB_LANDLORD_DRIVER=mysql
DB_LANDLORD_HOST=localhost
DB_LANDLORD_PORT=3306
DB_LANDLORD_DATABASE=projetinho_landlord
DB_LANDLORD_USERNAME=seu_usuario
DB_LANDLORD_PASSWORD=sua_senha

TENANCY_TENANT_CONNECTION=tenant
TENANCY_FALLBACK_DATABASE=projetinho_landlord
```

## URLs úteis
- Cadastro de loja: `/start`
- Login do admin da plataforma: `/platform/login`
- Lista de lojas (plataforma): `/platform/tenants`
- Catálogo (dev): `/?tenant=loja-abc`
- Admin da loja (dev): `/admin/login?tenant=loja-abc`
