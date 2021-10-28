FROM dap/php:8.0-apache

# Instalando paquete
RUN apt-get -y update && \
    apt-get install -y libicu-dev libxml2-dev

# Configurando extenciones
RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache soap

# Install xdebug
RUN pecl install xdebug \
    && echo 'zend_extension=xdebug.so' > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install symfony
#RUN curl -sS https://get.symfony.com/cli/installer | bash && \
#    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# APACHE y PHP
COPY ./conf/apache /etc/apache2
COPY ./conf/php/php.ini /usr/local/etc/php/php.ini

# Publicando el proyecto
RUN ln -s /app /var/www/app

# Configure cron jobs, and ensure crontab-file permissions
COPY conf/cron.d/crontab /etc/cron.d/app-cron
RUN chmod 0644 /etc/cron.d/app-cron && \
    crontab /etc/cron.d/app-cron && \
    touch /var/log/cron.log

EXPOSE 80
RUN mkdir -p /var/log/supervisor
COPY ./conf/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
