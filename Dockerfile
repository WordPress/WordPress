FROM wordpress:latest
COPY . /var/www/html
COPY uploads.ini /usr/local/etc/php/conf.d/
