# Use image which contains apache with php
FROM php:8.2-apache
# Install packages needed to install php extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libicu-dev \
        zip \
        unzip \
    && rm -rf /var/lib/apt/lists/*
# Install PHP extensions
RUN docker-php-ext-install zip intl mysqli pdo pdo_mysql opcache
# Install XDEBUG
RUN pecl install xdebug
# Enable XDEBUG
RUN docker-php-ext-enable xdebug
# Install composer command
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
# Suppress Apache startup warning about missing global ServerName.
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername
# Set umask to 0000 (newly created files will have 777 permissions)
RUN echo "umask 0000" >> /root/.bashrc
