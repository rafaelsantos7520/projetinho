# MySQL (multi-tenancy por banco)

## Resumo

- **Landlord**: um banco único (ex.: `projetinho_landlord`) com as tabelas `tenants`, `platform_users` e `sessions`.
- **Tenant**: **um banco por tenant** (ex.: `tenant_loja_abc`) com as tabelas `users`, `products`, `categories`, etc.
- O campo `tenants.schema` armazena o **nome do banco do tenant**.

## Variáveis de ambiente (exemplo)

```dotenv
DB_CONNECTION=tenant

DB_TENANT_DRIVER=mysql
DB_TENANT_HOST=127.0.0.1
DB_TENANT_PORT=3306
DB_TENANT_DATABASE=projetinho_landlord
DB_TENANT_USERNAME=root
DB_TENANT_PASSWORD=root

DB_LANDLORD_DRIVER=mysql
DB_LANDLORD_HOST=127.0.0.1
DB_LANDLORD_PORT=3306
DB_LANDLORD_DATABASE=projetinho_landlord
DB_LANDLORD_USERNAME=root
DB_LANDLORD_PASSWORD=root

TENANCY_TENANT_CONNECTION=tenant
TENANCY_FALLBACK_DATABASE=projetinho_landlord
```

## Subir MySQL local (sem Docker)

```bash
./scripts/mysql-local-start.sh
./scripts/mysql-local-bootstrap.sh
```

## Subir o schema landlord

```bash
php artisan migrate
```

## Habilitar uploads públicos

```bash
php artisan storage:link
```

## Criar admin da plataforma

```bash
php artisan platform:make-admin admin@site.com --name="Admin"
```

## Criar tenant e rodar migrations do tenant

```bash
php artisan tenants:create loja-abc
```

## Popular com dados de exemplo (opcional)

```bash
php artisan tenants:seed-demo --tenants=elianamodas,lojarafa,loja-abc --products=10 --categories=6
```

Se o seu usuário do MySQL **não puder criar banco** automaticamente, crie o banco do tenant manualmente e depois rode:

```bash
php artisan tenants:create loja-abc --no-migrate
php artisan tenants:migrate --tenant=loja-abc --force
```

## Criar usuário dentro do tenant

```bash
php artisan tenants:make-user loja-abc user@loja.com --name="Admin" --password="SenhaForte"
```
