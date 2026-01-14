# Deploy (revisão)

## Pré-requisitos
- PHP 8.2+
- Banco Postgres
- Variáveis de ambiente configuradas (`.env`)

## Passos mínimos
1. Rodar migrations do landlord:

```bash
php artisan migrate
```

2. Criar um admin da plataforma (para usar `/platform`):

```bash
php artisan platform:make-admin admin@site.com --name="Admin"
```

3. Criar uma loja (self-serve):
- Acesse `GET /start` e complete o formulário.

4. Rodar migrations dos tenants existentes quando houver novos campos:

```bash
php artisan tenants:migrate --force
```

## URLs úteis
- Cadastro de loja: `/start`
- Login do admin da plataforma: `/platform/login`
- Lista de lojas (plataforma): `/platform/tenants`
- Catálogo (dev): `/?tenant=loja-abc`
- Admin da loja (dev): `/admin/login?tenant=loja-abc`

