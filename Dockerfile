# 1. Use base PHP with FPM
FROM php:8.1-fpm AS builder

# 2. Install prerequisites for pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 3. Enable PDO and PDO_PGSQL
RUN docker-php-ext-install pdo pdo_pgsql

# 4. Set working directory
WORKDIR /app

# 5. First copy composer files
COPY composer.json composer.lock ./

# 6. Install with production flags
RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-dev

# 7. Now copy rest of application
COPY . .

# 8. Provide command to start your application
CMD php artisan serve --host=0.0.0.0 --port=8000
