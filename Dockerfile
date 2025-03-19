# Use an official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Set working directory in the container
WORKDIR /var/www/html

# Copy the project files to the container
COPY . /var/www/html

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mysqli

# Expose port 80 for the web server
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
