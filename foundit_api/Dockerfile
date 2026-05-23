FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy existing application directory contents
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js and build frontend assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build \
    && rm -rf node_modules

# Create storage link
RUN php artisan storage:link || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Copy seed images to a backup location (volume may override storage/app/public)
RUN cp -r /var/www/html/database/seeders/images /var/www/html/database/seeders/images_backup 2>/dev/null || true

# Start: migrate, seed images if empty, then serve
CMD sleep 10 \
    && php artisan migrate --force \
    && php artisan storage:link 2>/dev/null || true \
    && if [ ! -f /var/www/html/storage/app/public/items/item_1_iphone.jpg ]; then \
        echo "Seeding images to storage..." \
        && mkdir -p /var/www/html/storage/app/public/items /var/www/html/storage/app/public/photos/profiles \
        && cp /var/www/html/database/seeders/images_backup/items/*.jpg /var/www/html/storage/app/public/items/ 2>/dev/null \
        && cp /var/www/html/database/seeders/images_backup/profiles/*.jpg /var/www/html/storage/app/public/photos/profiles/ 2>/dev/null \
        && chown -R www-data:www-data /var/www/html/storage/app/public; \
    fi \
    && apache2-foreground
