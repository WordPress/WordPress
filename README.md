# ScaleGroup WordPress Template

Custom WordPress installation with pre-installed plugins for all customer sites.

## Pre-installed Plugins

- **Matomo** - Self-hosted analytics
- **Contact Form 7** - Contact forms
- **Yoast SEO** - SEO optimization
- **Akismet** - Spam protection

## Docker Image

This repository automatically builds a Docker image with WordPress and pre-installed plugins:

```
ghcr.io/scalegroup-aps/wordpress:latest
```

### Usage in Kubernetes

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: wordpress
spec:
  template:
    spec:
      containers:
        - name: wordpress
          image: ghcr.io/scalegroup-aps/wordpress:latest
          env:
            - name: WORDPRESS_DB_HOST
              value: mariadb
            - name: WORDPRESS_DB_USER
              valueFrom:
                secretKeyRef:
                  name: wordpress-secrets
                  key: db-user
            - name: WORDPRESS_DB_PASSWORD
              valueFrom:
                secretKeyRef:
                  name: wordpress-secrets
                  key: db-password
          volumeMounts:
            - name: wordpress-storage
              mountPath: /var/www/html/wp-content/uploads
```

## Building Locally

```bash
docker build -t ghcr.io/scalegroup-aps/wordpress:latest .
docker push ghcr.io/scalegroup-aps/wordpress:latest
```

## Updating Plugins

1. Update plugins via WordPress admin or manually in `wp-content/plugins/`
2. Commit and push changes
3. GitHub Actions automatically builds and pushes new image
4. Update customer deployments to pull latest image

## CI/CD

GitHub Actions automatically builds and publishes the Docker image on:
- Push to `main` branch
- Changes to `wp-content/plugins/`, `wp-content/themes/`, or `Dockerfile`
- Manual workflow dispatch

## Environment Variables

Required environment variables:
- `WORDPRESS_DB_HOST` - Database hostname
- `WORDPRESS_DB_USER` - Database user
- `WORDPRESS_DB_PASSWORD` - Database password
- `WORDPRESS_DB_NAME` - Database name (default: wordpress)
- `WORDPRESS_TABLE_PREFIX` - Table prefix (default: wp_)
- `WORDPRESS_DEBUG` - Debug mode (default: false)
