FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libzip-dev libpq-dev libicu-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) zip pdo_pgsql intl \
    && pecl install redis && docker-php-ext-enable redis

RUN apt-get update && \
    apt-get install -y wget && \
    wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get remove -y wget && \
    apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install -y symfony-cli

WORKDIR /app/

COPY . .
