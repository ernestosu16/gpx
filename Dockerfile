FROM dap/php:8.0-apache

COPY [".env", "composer.json", "composer.lock", "symfony.lock", "proyect-init.sh", "/app/"]

COPY bin /app/bin
COPY config /app/config
COPY docker /app/docker
COPY migrations /app/migrations
COPY public /app/public
COPY src /app/src
COPY translations /app/translations
COPY templates /app/templates

# Instalando paquete
RUN apt-get -y update && \
    apt-get install -y libicu-dev libxml2-dev libsodium-dev

# Configurando extenciones
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

RUN docker-php-ext-install pcntl bcmath  pdo_mysql sockets sodium soap zip
# APACHE
COPY ./docker/app/conf/apache /etc/apache2
# PHP
COPY ./docker/app/conf/php/php.ini.dist /usr/local/etc/php/php.ini

# Instalando paquetes necesarios
RUN composer install --no-scripts

# Publicando el proyecto
RUN ln -s /app /var/www/app

# Configure cron jobs, and ensure crontab-file permissions
COPY docker/app/conf/cron.d/crontab /etc/cron.d/app-cron
RUN chmod 0644 /etc/cron.d/app-cron && \
    crontab /etc/cron.d/app-cron && \
    touch /var/log/cron.log

# Apache mod
RUN a2enmod ssl

WORKDIR /app
ENTRYPOINT ["/app/docker/app/docker-entrypoint.sh"]

EXPOSE 80
EXPOSE 443
RUN mkdir -p /var/log/supervisor
COPY ./docker/app/conf/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
