# Use the official PHP image
FROM php:7.4-apache

# Enable Apache modules and install PHP extensions
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli

# Set the working directory
WORKDIR /var/www/html

# Copy your PHP files into the container
COPY api/ /var/www/html/

# Expose port 80 for Apache
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
