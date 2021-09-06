FROM dap/php:8.0-apache

# Install symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# APACHE y PHP
COPY ./conf/apache/ /etc/apache2
COPY ./conf/php/php.ini /usr/local/etc/php/php.ini

# Publicando el proyecto
RUN ln -s /app /var/www/app

EXPOSE 80
RUN mkdir -p /var/log/supervisor
COPY ./conf/supervisord.conf /etc/supervisor/supervisord.conf
CMD ["supervisord"]