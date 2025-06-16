# 1. Use base PHP with Composer
FROM php:8.1-fpm AS builder

# 2. Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# 3. Configure and enable gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql

# 4. Set working directory
WORKDIR /app

# 5. Copy files
COPY composer.json composer.lock ./
# 6. Install composer dependencies
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# 7. Now copy the rest of the application
COPY . .

# 8. Run your application (depending on your framework)
CMD php artisan serve --host=0.0.0.0 --port=8000
