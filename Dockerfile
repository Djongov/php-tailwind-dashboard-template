FROM php:8.3-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libssl-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf
COPY . /var/www/html/
#Delete existing .env file, if it exists
#RUN rm -f ".env"
COPY /config/default.conf /etc/apache2/sites-available/000-default.conf
COPY /config/php.ini /usr/local/etc/php/php.ini
RUN touch /var/log/php_errors.log
RUN chown www-data:www-data /var/log/php_errors.log
RUN chmod 644 /var/log/php_errors.log
RUN chown www-data:www-data /var/tmp
RUN chown www-data:www-data /var/www/html/.tools
RUN chown www-data:www-data /var/www/html/public/assets/images/profile
RUN chmod 755 /var/www/html
RUN chmod 1733 /var/tmp
RUN a2enmod rewrite
RUN a2enmod headers
RUN service apache2 restart
# Start Apache
#CMD ["/usr/sbin/httpd","-D","FOREGROUND"]