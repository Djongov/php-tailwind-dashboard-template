FROM php:8.3-apache

# Install system dependencies, PHP extensions, and Composer
RUN apt-get update && apt-get install -y \
    curl \
    libicu-dev \
    libssl-dev \
    unzip \
    && docker-php-ext-configure intl \
    # Let's install ssh as well
    && apt-get install -y openssh-server \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

# Copy application code and configuration files
COPY . /var/www/html/
COPY /config/default.conf /etc/apache2/sites-available/000-default.conf
COPY /config/php.ini /usr/local/etc/php/php.ini
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Modify the specific file in the vendor directory and set permissions
RUN if [ -f vendor/erusev/parsedown/Parsedown.php ]; then \
        sed -i "s/\$class = 'language-'.\$language;/\$class = 'language-'.\$language . ' c0py';/g" vendor/erusev/parsedown/Parsedown.php; \
    else \
        echo "File vendor/erusev/parsedown/Parsedown.php not found"; \
    fi \
    && touch /var/log/php_errors.log \
    && chown www-data:www-data /var/log/php_errors.log \
    && chmod 644 /var/log/php_errors.log \
    && chown www-data:www-data /var/tmp \
    && chown www-data:www-data /var/www/html/.tools \
    && chown www-data:www-data /var/www/html/public/assets/images/profile \
    && chmod 755 /var/www/html \
    && chmod 1733 /var/tmp \
    && a2enmod rewrite \
    && a2enmod headers \
    && service apache2 restart \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Set the entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose ports (for documentation purposes)
EXPOSE 22
EXPOSE 80
