FROM dap/php:8.0-apache

# Instalando paquete
RUN apt-get -y update && \
    apt-get install -y libicu-dev

# Configurando extenciones
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# APACHE y PHP
COPY ./docker/app/conf/apache /etc/apache2
COPY ./docker/app/conf/php/php.ini.dist /usr/local/etc/php/php.ini

# Publicando el proyecto
RUN ln -s /app /var/www/app

EXPOSE 80
RUN mkdir -p /var/log/supervisor
COPY ./docker/app/conf/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["supervisord"]
