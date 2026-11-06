FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Set recommended PHP settings
RUN echo "file_uploads = On\n" \
         "upload_max_filesize = 64M\n" \
         "post_max_size = 64M\n" \
         "max_execution_time = 300\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80
