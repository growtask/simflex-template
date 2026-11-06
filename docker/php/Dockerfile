FROM php:8.4-apache-bookworm

RUN apt-get update

# Установить системные зависимости
RUN apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    vim \
    --no-install-recommends

# Установить необходимые модули
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath intl zip
RUN docker-php-ext-configure gd --with-webp --with-jpeg && docker-php-ext-install gd zip
RUN a2enmod rewrite

# Скрыть заголовок X-Powered-By
RUN printf 'expose_php = Off\n' > $PHP_INI_DIR/conf.d/security.ini
# Скрыть версию Apache (заголовок Server, страницы ошибок)
RUN sed -ri -e 's/^ServerTokens .*/ServerTokens Prod/' -e 's/^ServerSignature .*/ServerSignature Off/' \
    /etc/apache2/conf-available/security.conf

# Корневая директория документа — public/: только index.php и статические файлы доступны из веба
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Создать исполняемый скрипт sf для запуска команды sf без префикса ./
RUN printf '#!/bin/sh\nexec php /var/www/html/sf "$@"\n' > /usr/local/bin/sf && chmod +x /usr/local/bin/sf

# Установить composer. Использование: docker-compose exec app composer install
RUN curl https://getcomposer.org/download/latest-stable/composer.phar > /usr/local/bin/composer && chmod +x /usr/local/bin/composer
RUN mkdir /.composer && chmod -R a+rwX /.composer
