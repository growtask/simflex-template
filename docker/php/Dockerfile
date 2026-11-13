FROM php:8.4-apache-bookworm

RUN apt-get update

# Системные пакеты
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

# Расширения PHP и модули Apache
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath intl zip
RUN docker-php-ext-configure gd --with-webp --with-jpeg && docker-php-ext-install gd zip
RUN a2enmod rewrite

# Рабочие настройки PHP: ошибки не выводятся в страницу, а пишутся в журнал контейнера
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Без заголовка X-Powered-By
RUN printf 'expose_php = Off\n' > $PHP_INI_DIR/conf.d/security.ini
# Без версии Apache (заголовок Server, страницы ошибок)
RUN sed -ri -e 's/^ServerTokens .*/ServerTokens Prod/' -e 's/^ServerSignature .*/ServerSignature Off/' \
    /etc/apache2/conf-available/security.conf

# Apache без root: хостинг-панель запускает контейнер от UID клиента, служебные каталоги Apache
# доступны на запись любому пользователю
RUN chmod 1777 /var/run/apache2 /var/lock/apache2 /var/log/apache2

# Корень сайта — public/: из веба доступны только index.php и статика
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Команда sf из любого каталога (без ./)
RUN printf '#!/bin/sh\nexec php /var/www/html/sf "$@"\n' > /usr/local/bin/sf && chmod +x /usr/local/bin/sf

# Composer: docker compose exec app composer install
RUN curl https://getcomposer.org/download/latest-stable/composer.phar > /usr/local/bin/composer && chmod +x /usr/local/bin/composer
RUN mkdir /.composer && chmod -R a+rwX /.composer
