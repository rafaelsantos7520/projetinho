#!/bin/sh

# Sair imediatamente se um comando falhar
set -e

echo "Iniciando Entrypoint..."

# Cache de configuração e rotas para performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Rodar migrações do banco principal (Landlord)
echo "Rodando migrações do Landlord..."
php artisan migrate --force

# Iniciar o Nginx em background
nginx -g "daemon off;" &

# Iniciar o PHP-FPM
echo "Iniciando PHP-FPM..."
php-fpm
