#!/bin/sh

# Sair imediatamente se um comando falhar
set -e

echo "Iniciando Entrypoint..."

# Cache de configuração e rotas para performance
# No deploy inicial, se não houver banco, o config:cache pode falhar.
# Vamos rodar apenas se o arquivo .env existir ou variáveis estiverem prontas.
php artisan config:clear
php artisan view:clear

# Rodar migrações do banco principal (Landlord)
# Adicionamos um pequeno try/catch ou timeout para não travar o deploy se o banco demorar
echo "Tentando rodar migrações do Landlord..."
php artisan migrate --force || echo "Aviso: Migrações falharam ou banco inacessível, continuando inicialização..."

# Iniciar o PHP-FPM em background
echo "Iniciando PHP-FPM..."
php-fpm -D

# Iniciar o Nginx em foreground (mantém o container vivo)
echo "Iniciando Nginx..."
nginx -g "daemon off;"
