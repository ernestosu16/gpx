FROM dap/php:8.0-apache
# Instalando paquete
RUN apt-get -y update && \
    apt-get install -y libicu-dev

# Configurando extenciones
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

COPY [".env", "composer.json", "composer.lock", "symfony.lock", "proyect-init.sh", "/app/"]

COPY bin /app/bin
COPY config /app/config
COPY docker /app/docker
COPY migrations /app/migrations
COPY public /app/public
COPY src /app/src
COPY translations /app/translations
COPY templates /app/templates

RUN mkdir -p /app/var/cache; \
    mkdir -p /app/var/log;\
    touch /app/var/log/dev.log; \
    touch /app/var/log/prod.log; \
    chown -R www-data:www-data var/cache var/log;

# APACHE
COPY ./docker/app/conf/apache /etc/apache2
# PHP
COPY ./docker/app/conf/php/php.ini.dist /usr/local/etc/php/php.ini

# Instalando paquetes necesarios
RUN composer install --no-scripts

# Publicando el proyecto
RUN ln -s /app /var/www/app

EXPOSE 80
RUN mkdir -p /var/log/supervisor
COPY ./docker/app/conf/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["supervisord"]
