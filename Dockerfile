FROM wordpress:6.8.3-php8.3-apache

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

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
  CMD curl -f http://localhost/wp-login.php || exit 1

EXPOSE 80
