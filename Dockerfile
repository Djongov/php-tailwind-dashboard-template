FROM php:8.3-apache


# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libssl-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql mysqli

# Clean up to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf
COPY . /var/www/html/
#Delete existing .env file, if it exists
RUN rm -f ".env"
COPY /config/default.conf /etc/apache2/sites-available/000-default.conf
ARG phpIniPath=/usr/local/etc/php/php.ini
#RUN cp /usr/local/etc/php/php.ini-production $phpIniPath
COPY /config/php.ini $phpIniPath
RUN touch /var/log/php_errors.log
RUN chown www-data:www-data /var/log/php_errors.log
RUN chmod 644 /var/log/php_errors.log
RUN chown www-data:www-data /var/tmp
RUN chmod 1733 /var/tmp
RUN a2enmod rewrite
RUN a2enmod headers
RUN service apache2 restart
# Start Apache
#CMD ["/usr/sbin/httpd","-D","FOREGROUND"]