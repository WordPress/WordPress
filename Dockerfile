FROM wordpress:6.8.3-php8.3-apache

# Install PHP Redis extension and WP-CLI
RUN apt-get update && apt-get install -y \
    less \
    mariadb-client \
    unzip \
    && pecl install redis-6.0.2 \
    && docker-php-ext-enable redis \
    && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP session handler for Redis
RUN echo "session.save_handler = redis" >> /usr/local/etc/php/conf.d/redis-session.ini \
    && echo "session.save_path = \"tcp://redis:6379?database=0\"" >> /usr/local/etc/php/conf.d/redis-session.ini \
    && echo "session.gc_maxlifetime = 86400" >> /usr/local/etc/php/conf.d/redis-session.ini \
    && echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/redis-session.ini \
    && echo "session.cookie_secure = 1" >> /usr/local/etc/php/conf.d/redis-session.ini \
    && echo "session.cookie_samesite = \"Lax\"" >> /usr/local/etc/php/conf.d/redis-session.ini

# Install Redis Object Cache plugin (core dependency for stateless architecture)
RUN curl -L https://downloads.wordpress.org/plugin/redis-cache.2.7.0.zip -o /tmp/redis-cache.zip \
    && unzip /tmp/redis-cache.zip -d /usr/src/wordpress/wp-content/plugins/ \
    && rm /tmp/redis-cache.zip \
    && chown -R www-data:www-data /usr/src/wordpress/wp-content/plugins/redis-cache

# Copy pre-installed plugins
COPY wp-content/plugins/ /usr/src/wordpress/wp-content/plugins/

# Copy themes
COPY wp-content/themes/ /usr/src/wordpress/wp-content/themes/

# Copy must-use plugins (if any)
# COPY wp-content/mu-plugins/ /usr/src/wordpress/wp-content/mu-plugins/

# Set correct permissions
RUN chown -R www-data:www-data /usr/src/wordpress/wp-content

# Enable Apache modules
RUN a2enmod rewrite expires headers

# Copy custom entrypoint
COPY docker-entrypoint.sh /usr/local/bin/custom-entrypoint.sh
RUN chmod +x /usr/local/bin/custom-entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
  CMD curl -f http://localhost/wp-login.php || exit 1

EXPOSE 80

# Use custom entrypoint
ENTRYPOINT ["custom-entrypoint.sh"]
