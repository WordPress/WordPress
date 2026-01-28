# ===== Stage 1: build "app filesystem" =====
FROM alpine:3.20 AS build

WORKDIR /src

# Copy the whole project (excluding .dockerignore items)
COPY . .

# Safety: NEVER ship wp-config.php inside the image
RUN rm -f wp-config.php

# ===== Stage 2: final runtime image =====
FROM wordpress:6.5-php8.2-apache

# Replace the whole WordPress app
# (this overwrites core + wp-content + everything)
COPY --from=build /src/ /var/www/html/

# Permissions: WordPress runs as www-data inside container
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \;

