FROM php:8.3-apache

# Instalar dependencias del sistema y de PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    sqlite3

# Limpiar cache de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# Configurar DocumentRoot de Apache a la carpeta public de Laravel
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la app
COPY . .

# Crear archivo .env usando el de ejemplo y configurar base de datos SQLite y APP_KEY
RUN cp .env.example .env
RUN sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
RUN sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=\/var\/www\/html\/database\/database.sqlite/' .env || echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env
RUN touch database/database.sqlite
RUN chown -R www-data:www-data /var/www/html/database

# Instalar dependencias de PHP y Node
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Generar llave, ejecutar migraciones (seeding opcional) y optimizar para producción
RUN php artisan key:generate --force
RUN php artisan migrate --force --seed
RUN php artisan optimize

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
