FROM php:8.3-apache
RUN apt-get update \
  && apt-get install -y --no-install-recommends libpq-dev \
  && docker-php-ext-install pdo_pgsql \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*
ENV APACHE_DOCUMENT_ROOT=/var/www/html
COPY public/ /var/www/html/
COPY src/ /var/www/src/
RUN chown -R www-data:www-data /var/www/html /var/www/src
RUN printf '%s\n' '#!/bin/sh' 'set -eu' 'PORT="${PORT:-10000}"' \
  'sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf' \
  'sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf' \
  'php /var/www/src/migrate-demo.php' \
  'exec apache2-foreground' > /usr/local/bin/render-entrypoint \
  && chmod +x /usr/local/bin/render-entrypoint
EXPOSE 10000
CMD ["/usr/local/bin/render-entrypoint"]
