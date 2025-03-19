# Use the official PHP image as the base
FROM php:8.2-apache

# Set environment variables
ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# Install necessary system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Copy the project files to the container
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Ensure correct file permissions
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start Apache server in the foreground
CMD ["apache2-foreground"]
