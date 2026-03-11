FROM php:8.2-apache

# Install mga kailangan ng Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# I-enable ang Apache mod_rewrite para gumana ang mga routes mo
RUN a2enmod rewrite

# I-set ang working directory
WORKDIR /var/www/html

# Kopyahin ang buong project code papunta sa server
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel Dependencies (vendor folder)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ituro ang server sa "public" folder ng Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Ayusin ang folder permissions para sa storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buksan ang port 80
EXPOSE 80
