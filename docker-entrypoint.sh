#!/bin/bash
set -e

# Start Apache in the background using the official WordPress entrypoint
# The official WordPress image uses /usr/local/bin/docker-entrypoint.sh
/usr/local/bin/docker-entrypoint.sh apache2-foreground &
APACHE_PID=$!

# Wait for WordPress files to be in place
echo "Waiting for WordPress files..."
sleep 5

# Install WP-CLI if not present
if ! command -v wp &> /dev/null; then
    echo "Installing WP-CLI..."
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
    chmod +x wp-cli.phar
    mv wp-cli.phar /usr/local/bin/wp
fi

# Wait for database to be ready (with timeout)
echo "Waiting for database connection..."
DB_WAIT_TIMEOUT=180  # 3 minutes
DB_WAIT_COUNT=0
until wp db check --allow-root --path=/var/www/html 2>/dev/null; do
    if [ $DB_WAIT_COUNT -ge $DB_WAIT_TIMEOUT ]; then
        echo "ERROR: Database connection timeout after ${DB_WAIT_TIMEOUT} seconds"
        echo "Please check database configuration and network connectivity"
        exit 1
    fi
    echo "Database not ready, waiting... (${DB_WAIT_COUNT}/${DB_WAIT_TIMEOUT}s)"
    sleep 3
    DB_WAIT_COUNT=$((DB_WAIT_COUNT + 3))
done

echo "Database is ready!"

# Wait for Redis to be ready (if Redis host is configured)
if [ -n "${WP_REDIS_HOST}" ]; then
    echo "Waiting for Redis connection at ${WP_REDIS_HOST}..."
    REDIS_WAIT_TIMEOUT=60  # 1 minute
    REDIS_WAIT_COUNT=0
    until timeout 2 bash -c "echo > /dev/tcp/${WP_REDIS_HOST}/6379" 2>/dev/null; do
        if [ $REDIS_WAIT_COUNT -ge $REDIS_WAIT_TIMEOUT ]; then
            echo "WARNING: Redis connection timeout after ${REDIS_WAIT_TIMEOUT} seconds"
            echo "Continuing without Redis object cache..."
            break
        fi
        echo "Redis not ready, waiting... (${REDIS_WAIT_COUNT}/${REDIS_WAIT_TIMEOUT}s)"
        sleep 3
        REDIS_WAIT_COUNT=$((REDIS_WAIT_COUNT + 3))
    done
    if [ $REDIS_WAIT_COUNT -lt $REDIS_WAIT_TIMEOUT ]; then
        echo "Redis is ready!"
    fi
fi

# Check if WordPress is already installed
if ! wp core is-installed --allow-root --path=/var/www/html 2>/dev/null; then
    echo "WordPress not installed. Starting installation..."

    # Get configuration from environment variables with defaults
    WP_URL="${WORDPRESS_URL:-http://localhost}"
    WP_TITLE="${WORDPRESS_TITLE:-My WordPress Site}"
    WP_ADMIN_USER="${WORDPRESS_ADMIN_USER:-admin}"
    WP_ADMIN_PASSWORD="${WORDPRESS_ADMIN_PASSWORD:-changeme}"
    WP_ADMIN_EMAIL="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}"

    echo "Installing WordPress..."
    echo "  URL: $WP_URL"
    echo "  Title: $WP_TITLE"
    echo "  Admin: $WP_ADMIN_USER"

    # Install WordPress
    echo "Running wp core install..."
    if wp core install \
        --url="$WP_URL" \
        --title="$WP_TITLE" \
        --admin_user="$WP_ADMIN_USER" \
        --admin_password="$WP_ADMIN_PASSWORD" \
        --admin_email="$WP_ADMIN_EMAIL" \
        --skip-email \
        --allow-root \
        --path=/var/www/html; then
        echo "WordPress installed successfully!"
    else
        echo "ERROR: WordPress installation failed"
        echo "URL: $WP_URL"
        echo "Please check environment variables and logs"
        exit 1
    fi

    # Verify installation succeeded
    if ! wp core is-installed --allow-root --path=/var/www/html 2>/dev/null; then
        echo "ERROR: WordPress installation verification failed"
        echo "wp core install command succeeded but WordPress is not detected as installed"
        exit 1
    fi
    echo "WordPress installation verified!"

    # Activate pre-installed plugins
    echo "Activating plugins..."
    wp plugin activate --all --allow-root --path=/var/www/html || echo "Some plugins couldn't be activated"

    # Set permalink structure
    echo "Setting permalink structure..."
    wp rewrite structure '/%postname%/' --allow-root --path=/var/www/html

    # Flush rewrite rules
    wp rewrite flush --allow-root --path=/var/www/html

    echo "WordPress configuration complete!"
else
    echo "WordPress is already installed."
fi

# Configure Redis Object Cache (if Redis host is configured)
if [ -n "${WP_REDIS_HOST}" ]; then
    echo "Configuring Redis Object Cache..."

    # Add Redis configuration to wp-config.php if not already present
    if ! grep -q "WP_REDIS_HOST" /var/www/html/wp-config.php 2>/dev/null; then
        # Find the line with "That's all, stop editing!" and insert before it
        sed -i "/That's all, stop editing/i\\
\\
/* Redis Object Cache Configuration */\\
define('WP_REDIS_HOST', '${WP_REDIS_HOST}');\\
define('WP_REDIS_PORT', ${WP_REDIS_PORT:-6379});\\
define('WP_REDIS_DATABASE', ${WP_REDIS_DATABASE:-1});\\
define('WP_REDIS_PREFIX', '${WP_REDIS_PREFIX:-wp}');\\
define('WP_CACHE', true);\\
" /var/www/html/wp-config.php

        echo "Redis configuration added to wp-config.php"
    else
        echo "Redis configuration already present in wp-config.php"
    fi
fi

# Keep the container running by waiting for Apache
wait $APACHE_PID
