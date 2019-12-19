FROM php:7.2-apache

RUN a2enmod ssl
RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql

ENV PATH "$PATH:./vendor/bin"

#------------------------------------------------
# Add Xdebug
#------------------------------------------------
RUN pecl install xdebug-2.6.1 && docker-php-ext-enable xdebug

RUN mkdir /usr/local/etc/php/conf.d2/
RUN touch /usr/local/etc/php/conf.d2/php.ini
RUN ln -s /usr/local/etc/php/conf.d2/php.ini /usr/local/etc/php/conf.d/php.ini

#------------------------------------------------
# Install dependencies
#------------------------------------------------
RUN apt-get update --fix-missing
RUN apt-get install -y \
    # SSH agent
    openssh-server openssh-client \
    # Composer dependencies
    git \
    zip \
    unzip \
    # Dependencies for Laravel Dusk
    libnss3 chromium \
    # SOAP dependencies
    libxml2-dev \
    # IMAGICK dependencies
    libmagickwand-dev \
    # GD dependencies
    libpng-dev \
    --no-install-recommends libicu-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pcntl

#------------------------------------------------
# Add Composer
#------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


#------------------------------------------------
# Clean up
#------------------------------------------------
RUN apt-get clean && apt-get autoclean
RUN rm -rf /var/lib/apt/lists/*