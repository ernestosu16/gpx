FROM php:8.1-apache AS builder
LABEL maintainer="Ernesto Suárez Ramírez <ernestosr@ecc.cu>"

ENV TZ "America/Havana"
ENV APACHE_RUN_USER  "www-data"
ENV APACHE_RUN_GROUP "www-data"
ENV APACHE_LOG_DIR   "/var/log/apache2"
ENV APACHE_PID_FILE  "/var/run/apache2/apache2.pid"
ENV APACHE_RUN_DIR   "/var/run/apache2"
ENV APACHE_LOCK_DIR  "/var/lock/apache2"
ENV APACHE_LOG_DIR   "/var/log/apache2"

# Instalando paquetes necesarios para la aplicación
COPY requirements.system /requirements.system
RUN apt-get update &&  cat /requirements.system | xargs apt install -y && apt-get clean

# Activando modulos del apache
RUN a2enmod rewrite ssl

# Configurando extenciones
RUN docker-php-ext-configure intl && \
    docker-php-ext-configure zip  && \
    docker-php-ext-configure opcache --enable-opcache  && \
    docker-php-ext-install intl opcache zip pcntl zip soap pdo pdo_mysql bcmath sockets sodium

# Instalando rabbitmq
RUN pecl install amqp \
    && docker-php-ext-enable amqp

# Instalando composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer -V

# Instalando symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony && \
    symfony check:requirements

# Configurando APP
ENV APP_DIR "/app"
COPY docker/conf/apache /etc/apache2
RUN mkdir /app && ln -s ${APP_DIR} /var/www/app

WORKDIR /app
EXPOSE 80

ENTRYPOINT ["docker-entrypoint"]
RUN mkdir -p /var/log/supervisor
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["supervisord","-c","/etc/supervisor/supervisord.conf"]

FROM builder AS developer

## Activando el xdebug
RUN pecl install xdebug && \
    echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

COPY docker/conf/php/php.ini /usr/local/etc/php/php.ini
RUN ln -s ${APP_DIR}/docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint