FROM php:fpm

COPY --from=composer:latest /usr/bin/composer /urs/bin/composer

LABEL maintainer="Alvaro Farias - alvaro.farias@rentadrone.cl"

WORKDIR /var/app/current

COPY . .

RUN apt update && apt install -y git curl zip unzip nano \
    imagemagick bash ca-certificates gdal-bin default-mysql-client \
    libgdal-dev zlib1g-dev libpng-dev libzip-dev libbz2-dev libonig-dev libxml2-dev \
    && docker-php-ext-install \
    gd \
    exif \
    mbstring \
    bz2 \
    opcache \
    pcntl \
    bcmath \
    mysqli \
    pdo \
    pdo_mysql

RUN export CPLUS_INCLUDE_PATH=/usr/include/gdal
RUN export C_INCLUDE_PATH=/usr/include/gdal

COPY /services/php-fpm/laravel.ini /usr/local/etc/php/conf.d

# RUN groupmod -o -g 82 www-data && \
#    usermod -o -u 82 -g www-data www-data

RUN COMPOSER_MEMORY_LIMIT=-1 /urs/bin/composer install --no-dev --no-interaction --optimize-autoloader

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN chown -R www-data:www-data storage
RUN chown -R www-data:www-data bootstrap

RUN chmod 644 .env \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

CMD ["php-fpm"]

EXPOSE 9000
