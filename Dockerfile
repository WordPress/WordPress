FROM wordpress:6.8.3-php8.3-apache

# Install WP-CLI and required tools
RUN apt-get update && apt-get install -y \
    less \
    mariadb-client \
    && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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
