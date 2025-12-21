# WordPress with Redis - Docker Image

Custom WordPress Docker image with Redis support for stateless, horizontally-scalable deployments.

## Features

- ✅ **PHP Redis Extension** - Native Redis support for sessions
- ✅ **Redis Object Cache** - 80-90% DB query reduction
- ✅ **WP-CLI** - Command-line WordPress management
- ✅ **Auto-activation** - Plugins activate automatically on startup
- ✅ **Session Security** - HTTPOnly, Secure, SameSite cookies
- ✅ **Horizontal Scaling** - Stateless architecture for multiple pods

## Pre-installed Plugins

- **Elementor** - Page builder
- **Contact Form 7** - Contact forms
- **Yoast SEO** - SEO optimization
- **Akismet** - Spam protection
- **Matomo** - Self-hosted analytics

## Docker Image

**Registry:** `registry.digitalocean.com/scaleweb/wordpress`

### Build Image

```bash
# Using build script (recommended)
./build-image.sh

# Or manually
docker build -t registry.digitalocean.com/scaleweb/wordpress:latest .
docker build -t registry.digitalocean.com/scaleweb/wordpress:$(git rev-parse --short HEAD) .

# Push to registry
docker push registry.digitalocean.com/scaleweb/wordpress:latest
docker push registry.digitalocean.com/scaleweb/wordpress:$(git rev-parse --short HEAD)
```

## Local Testing

```bash
# Start local environment with Redis
docker-compose up

# Access WordPress
open http://localhost:8080

# Default credentials:
# Username: admin
# Password: admin123

# Stop when done
docker-compose down
```

### Verify Redis Connection

```bash
# Exec into WordPress container
docker exec -it wordpress_app bash

# Check PHP Redis extension
php -r "var_dump(extension_loaded('redis'));"
# Expected: bool(true)

# Check Redis connectivity
redis-cli -h redis ping
# Expected: PONG

# Check session handler
php -i | grep session.save
# Expected: session.save_handler => redis
```

## Environment Variables

### Required

| Variable | Description | Example |
|----------|-------------|---------|
| `WORDPRESS_DB_HOST` | MariaDB hostname | `mariadb:3306` |
| `WORDPRESS_DB_NAME` | Database name | `wordpress` |
| `WORDPRESS_DB_USER` | Database user | `wordpress` |
| `WORDPRESS_DB_PASSWORD` | Database password | `secret123` |

### Redis Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `WP_REDIS_HOST` | - | Redis hostname (e.g., `redis`) |
| `WP_REDIS_PORT` | `6379` | Redis port |
| `WP_REDIS_DATABASE` | `1` | Redis database for object cache |
| `WP_REDIS_PREFIX` | `wp` | Cache key prefix |
| `WP_CACHE` | `true` | Enable object caching |

### WordPress Auto-Install

| Variable | Default | Description |
|----------|---------|-------------|
| `WORDPRESS_URL` | `http://localhost` | Site URL |
| `WORDPRESS_TITLE` | `My WordPress Site` | Site title |
| `WORDPRESS_ADMIN_USER` | `admin` | Admin username |
| `WORDPRESS_ADMIN_PASSWORD` | `changeme` | Admin password |
| `WORDPRESS_ADMIN_EMAIL` | `admin@example.com` | Admin email |

## Redis Database Allocation

- **DB 0**: PHP Sessions (24-hour TTL)
- **DB 1**: WordPress Object Cache (variable TTL)
- **DB 2-15**: Reserved for future use

## Kubernetes Deployment

This image is designed for Kubernetes deployment with:
- Argo Rollouts for canary deployments
- Traefik for traffic routing
- Shared PVC for wp-content uploads
- Redis for sessions and cache

**Kubernetes manifests:** See `infra/customer-stack/wordpress/` in CRM repository

```yaml
apiVersion: argoproj.io/v1alpha1
kind: Rollout
metadata:
  name: wordpress
spec:
  replicas: 2
  template:
    spec:
      containers:
        - name: wordpress
          image: registry.digitalocean.com/scaleweb/wordpress:1.0.0
          env:
            - name: WP_REDIS_HOST
              value: "redis"
            - name: WP_REDIS_DATABASE
              value: "1"
            - name: WP_REDIS_PREFIX
              value: "customer-acme"
          volumeMounts:
            - name: wordpress-data
              mountPath: /var/www/html/wp-content
              subPath: wp-content
```

## Stateless Architecture

### What's Persistent (Shared Storage)

- ✅ `wp-content/uploads/` - Media files (PVC)
- ✅ `wp-content/plugins/` - Installed plugins (PVC)
- ✅ `wp-content/themes/` - Installed themes (PVC)
- ✅ Database - WordPress content (MariaDB)
- ✅ Sessions - User sessions (Redis DB 0)
- ✅ Cache - Object cache (Redis DB 1)

### What's Ephemeral (Container)

- ❌ `wp-admin/` - WordPress core (from image)
- ❌ `wp-includes/` - WordPress core (from image)
- ❌ `wp-config.php` - Generated on startup

## Updating Plugins

### Method 1: Via WordPress Admin (Runtime)
1. Log in to WordPress admin
2. Navigate to Plugins > Add New
3. Install and activate plugin
4. Files saved to PVC, available to all pods immediately

### Method 2: Via Docker Image (Build-time)
1. Update plugins in `wp-content/plugins/`
2. Rebuild image: `./build-image.sh`
3. Push to registry
4. Update Kubernetes rollout to use new image

## Updating WordPress Core

1. Update `Dockerfile` with new WordPress version:
   ```dockerfile
   FROM wordpress:6.8.4-php8.3-apache
   ```

2. Rebuild image:
   ```bash
   ./build-image.sh
   ```

3. Update Kubernetes rollout to use new image version

## Monitoring

### Redis Cache Status

```bash
# In WordPress pod
wp redis status --allow-root --path=/var/www/html

# Expected output:
# Status: Connected
# Redis: 7.x.x
# Client: PhpRedis (6.0.2)
```

### Cache Hit Ratio

Navigate to WordPress Admin:
- Tools > Redis
- Target: >85% hit ratio

## Performance

With Redis enabled:
- 🚀 50-60% faster page loads
- 📉 80-90% reduction in database queries
- ⚡ Instant session persistence across pods
- 🔄 Horizontal scaling without session loss

## Architecture

```
┌─────────────────────┐
│  WordPress Pods     │
│  (Stateless)        │
│                     │
│  ┌───────────────┐  │
│  │ PHP-FPM       │  │ Sessions ─────┐
│  │ Apache        │  │               │
│  └───────────────┘  │               ▼
│                     │         ┌──────────┐
│  ┌───────────────┐  │         │  Redis   │
│  │ wp-content/   │◄─┼────────►│          │
│  │ (PVC mount)   │  │  Cache  │  DB 0: Sessions
│  └───────────────┘  │         │  DB 1: Cache
└─────────────────────┘         └──────────┘
         │
         ▼
   ┌──────────┐
   │ MariaDB  │
   │          │
   └──────────┘
```

## Troubleshooting

### Redis Not Connected

```bash
# Check Redis pod
kubectl get pods -l app=redis

# Test connectivity
kubectl exec -it wordpress-pod -- redis-cli -h redis ping
```

### Sessions Not Persisting

```bash
# Check PHP configuration
kubectl exec -it wordpress-pod -- php -i | grep session.save

# Should show:
# session.save_handler => redis
# session.save_path => tcp://redis:6379?database=0
```

### Object Cache Not Working

```bash
# Enable Redis Object Cache
kubectl exec -it wordpress-pod -- \
  wp redis enable --force --allow-root --path=/var/www/html

# Check status
kubectl exec -it wordpress-pod -- \
  wp redis status --allow-root --path=/var/www/html
```

## Directory Structure

```
C:\Users\jonas\Work\WordPress\
├── Dockerfile                # Image definition with Redis support
├── docker-entrypoint.sh      # Startup script with auto-config
├── build-image.sh            # Build automation
├── docker-compose.yml        # Local testing with Redis
├── wp-admin/                 # WordPress core
├── wp-includes/              # WordPress core
└── wp-content/               # Customizations
    ├── plugins/              # Pre-installed plugins
    ├── themes/               # Pre-installed themes
    └── uploads/              # (Excluded from image)
```

## Related Documentation

- **Kubernetes Deployment**: See `infra/customer-stack/wordpress/DEPLOYMENT.md` in CRM repository
- **Plugin Management**: See `infra/customer-stack/wordpress/PLUGINS-AND-PERSISTENCE.md`
- **Architecture Diagrams**: See `infra/customer-stack/wordpress/ARCHITECTURE.md`
- **Directory Structure**: See `infra/customer-stack/wordpress/DIRECTORY-STRUCTURE.md`
