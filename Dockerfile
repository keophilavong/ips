# Use official PHP Apache image
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PostgreSQL client
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite and mod_env (for environment variables)
RUN a2enmod rewrite env

# Configure PHP for file uploads
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Configure Apache to pass environment variables to PHP
RUN echo "PassEnv DB_HOST DB_PORT DB_USER DB_PASS DB_NAME" >> /etc/apache2/conf-available/docker-env.conf \
    && a2enconf docker-env

# Set proper permissions for upload directories
RUN mkdir -p /var/www/html/files /var/www/html/uploads/activities \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/files \
    && chmod -R 755 /var/www/html/uploads

# Copy application files
COPY . /var/www/html/

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

