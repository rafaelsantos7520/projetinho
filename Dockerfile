# Estágio de Build (Node.js)
FROM node:20-alpine AS build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Estágio Final (PHP + Nginx)
FROM php:8.2-fpm-alpine

# Instalar dependências do sistema necessárias para compilar extensões PHP
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    curl-dev \
    zip \
    unzip \
    git \
    mysql-client \
    icu-dev \
    zlib-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    linux-headers

# Configurar e instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . .

# Copiar os assets compilados do estágio de build
COPY --from=build-stage /app/public/build ./public/build

# Instalar dependências do Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Configurar Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Script de inicialização
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expor a porta que o Render espera (80)
EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
