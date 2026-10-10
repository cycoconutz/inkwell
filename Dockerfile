FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libzip-dev \
    unzip \
    default-mysql-client \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Apache rewrite + headers
RUN a2enmod rewrite headers

WORKDIR /var/www/html

COPY . /var/www/html/

# Install dependencies if composer.json present (zero deps expected)
RUN if [ -f composer.json ]; then \
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
        composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader; \
    fi

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
