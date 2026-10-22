# ==============================================================
# SkyBook - Render.com Docker Deployment Container
# Base Image: Official PHP 8.2 with Apache Web Server
# ==============================================================

FROM php:8.2-apache

# Install PDO MySQL & MySQLi extensions required by SkyBook
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache rewrite engine
RUN a2enmod rewrite

# Copy codebase into Apache web directory
COPY . /var/www/html/

# Set correct permissions
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to listen on $PORT assigned by Render (defaults to 80)
RUN sed -i 's/80/${PORT:-80}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Expose standard port
EXPOSE 80

# Launch Apache in foreground
CMD ["apache2-foreground"]
