FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install additional tools
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
