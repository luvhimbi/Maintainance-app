FROM php:8.1-fpm AS builder

# Install prerequisites
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and enable gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql

# Set working directory
WORKDIR /app

# First copy composer files
COPY composer.json composer.lock ./
# Install with production flags
RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-dev

# Now copy rest of application
COPY . .

CMD php artisan serve --host=0.0.0.0 --port=8000

