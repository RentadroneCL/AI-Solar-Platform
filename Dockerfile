FROM php:fpm

COPY --from=composer:latest /usr/bin/composer /urs/bin/composer

LABEL maintainer="Alvaro Farias - alvaro.farias@rentadrone.cl"

WORKDIR /var/app/current

COPY . .

ENV DB_DATABASE=development
ENV DB_USERNAME=homestead
ENV DB_PASSWORD=secret

RUN apt update && apt install -y git curl zip unzip \
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

RUN curl -#LO https://github.com/atkrad/wait4x/releases/latest/download/wait4x-linux-amd64.tar.gz \
    && tar --one-top-level -xvf wait4x-linux-amd64.tar.gz \
    && cp ./wait4x-linux-amd64/wait4x /usr/local/bin/wait4x

COPY /services/php-fpm/solar.ini /usr/local/etc/php/conf.d

RUN groupmod -o -g 1000 www-data && \
    usermod -o -u 1000 -g www-data www-data

RUN COMPOSER_MEMORY_LIMIT=-1 /urs/bin/composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data storage \
    && chmod 644 .env \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan view:clear

CMD ['wait4x mysql ${DB_USERNAME}:${DB_PASSWORD}@tcp(mysql:3306)/${DB_DATABASE}?tls=skip-verify -- php artisan migrate --force']

CMD ["php-fpm"]

EXPOSE 9000
