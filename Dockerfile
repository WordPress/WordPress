
# ===== Stage 2: final runtime image =====
FROM wordpress:6.5-php8.2-apache

# Replace the whole WordPress app
# (this overwrites core + wp-content + everything)
COPY . /var/www/html/

# Permissions: WordPress runs as www-data inside container
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \;

