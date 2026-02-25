# ARCHITECTURE — WordPress 7.0

## Document d'Architecture Technique

**Version :** 7.0-beta1 (build 61735)
**Révision DB :** 61696
**Date :** Février 2026
**Document compagnon :** [PRD.md](PRD.md) (exigences produit)

---

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Séquence de bootstrap](#2-séquence-de-bootstrap)
3. [Composants core](#3-composants-core)
4. [Modèle de données](#4-modèle-de-données)
5. [Spécifications API](#5-spécifications-api)
6. [Sécurité — implémentation](#6-sécurité--implémentation)
7. [Performance — stratégies](#7-performance--stratégies)
8. [Internationalisation — infrastructure](#8-internationalisation--infrastructure)
9. [Extensibilité et écosystème](#9-extensibilité-et-écosystème)
10. [Infrastructure et déploiement](#10-infrastructure-et-déploiement)
11. [Dépendances tierces intégrées](#11-dépendances-tierces-intégrées)
12. [Risques architecturaux](#12-risques-architecturaux)

---

## 1. Vue d'ensemble

```
┌─────────────────────────────────────────────────────────────────────┐
│                         VISITEUR / CLIENT                          │
│                    (Navigateur, App mobile, API)                   │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                    ┌──────────▼──────────┐
                    │   Serveur Web       │
                    │ (Apache / Nginx)    │
                    └──────────┬──────────┘
                               │
          ┌────────────────────┼────────────────────┐
          │                    │                    │
   ┌──────▼──────┐    ┌───────▼───────┐    ┌──────▼──────┐
   │ index.php   │    │ wp-login.php  │    │ xmlrpc.php  │
   │ (Frontend)  │    │ (Auth)        │    │ (Legacy API)│
   └──────┬──────┘    └───────┬───────┘    └──────┬──────┘
          │                    │                    │
          └────────────────────┼────────────────────┘
                               │
                    ┌──────────▼──────────┐
                    │   wp-load.php       │
                    │   (Bootstrap)       │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  wp-config.php      │
                    │  (Configuration)    │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  wp-settings.php    │
                    │  (Initialisation)   │
                    └──────────┬──────────┘
                               │
     ┌─────────────────────────┼─────────────────────────┐
     │                         │                         │
┌────▼────┐            ┌───────▼───────┐          ┌─────▼─────┐
│ Hooks   │            │  Core APIs    │          │  Plugins  │
│ System  │◄──────────►│  (wp-includes)│◄────────►│ & Themes  │
│         │            │               │          │           │
└────┬────┘            └───────┬───────┘          └─────┬─────┘
     │                         │                        │
     └─────────────────────────┼────────────────────────┘
                               │
                    ┌──────────▼──────────┐
                    │      wpdb           │
                    │  (Database Layer)   │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │   MySQL / MariaDB   │
                    └─────────────────────┘
```

---

## 2. Séquence de bootstrap (wp-settings.php)

| Phase | Fichiers chargés | Responsabilité |
|-------|-----------------|----------------|
| 1 | `version.php`, `compat.php`, `load.php` | Version, compatibilité PHP, fonctions de chargement |
| 2 | Classes d'exception, fatal error handler | Gestion d'erreurs et mode récupération |
| 3 | `plugin.php`, `class-wp-hook.php` | **Système de hooks** (fondation de tout le reste) |
| 4 | `default-constants.php` | Constantes (WP_CONTENT_DIR, WP_PLUGIN_DIR, etc.) |
| 5 | Classes User, Role, Capabilities | Authentification et autorisation |
| 6 | `class-wp-query.php` | Moteur de requête |
| 7 | `theme.php`, `template.php` | Système de thèmes et templates |
| 8 | `post.php`, `taxonomy.php` | Système de contenu |
| 9 | `formatting.php`, `functions.php` | Utilitaires et fonctions globales |
| 10 | Chargement des plugins (mu-plugins, plugins actifs) | Extensions utilisateur |
| 11 | `default-filters.php` | Hooks par défaut du cœur |
| 12 | `ms-settings.php` (si MULTISITE) | Initialisation multisite |

---

## 3. Composants core

### 3.1 Système de hooks (Event Bus)

Le pattern architectural fondamental de WordPress. **Tout** passe par les hooks.

#### Actions (événements)

```php
// Enregistrement
add_action('save_post', 'my_function', $priority, $accepted_args);

// Déclenchement
do_action('save_post', $post_id, $post, $update);
```

#### Filtres (transformations)

```php
// Enregistrement
add_filter('the_content', 'my_filter', $priority, $accepted_args);

// Application
$content = apply_filters('the_content', $raw_content);
```

**Caractéristiques :**
- Globaux `$wp_filter`, `$wp_actions`, `$wp_current_filter`
- Classe `WP_Hook` pour le stockage et l'exécution
- Priorité numérique (défaut 10, plus bas = plus tôt)
- Le fichier `post.php` à lui seul contient **111 hooks**
- Couplage score 10/10 — toutes les parties du système en dépendent

### 3.2 Couche d'abstraction base de données (wpdb)

Classe singleton `wpdb` (4 146 lignes, 72 méthodes) — point d'accès unique à la base de données :

**Opérations clés :**
- `$wpdb->query($sql)` — Requête SQL brute
- `$wpdb->get_results($sql)` — Résultats multiples
- `$wpdb->get_row($sql)` — Résultat unique
- `$wpdb->get_var($sql)` — Valeur scalaire
- `$wpdb->insert($table, $data)` — Insertion sécurisée
- `$wpdb->update($table, $data, $where)` — Mise à jour sécurisée
- `$wpdb->delete($table, $where)` — Suppression sécurisée
- `$wpdb->prepare($sql, ...$args)` — Requêtes préparées (protection injection SQL)

**Caractéristiques :**
- Préfixe de table configurable (`$table_prefix`)
- Support multisite (préfixes par blog)
- Cache de requêtes intégré
- Gestion des charsets et collations
- Logging et débogage conditionnel (`SAVEQUERIES`)

### 3.3 Système de cache

#### Cache d'objets (WP_Object_Cache)

Cache en mémoire par défaut, remplaçable par un drop-in (`wp-content/object-cache.php`) pour Redis, Memcached, etc.

```php
wp_cache_set($key, $data, $group, $expire);
$data = wp_cache_get($key, $group);
wp_cache_delete($key, $group);
wp_cache_flush();
```

Supporte les opérations batch : `wp_cache_get_multiple()`, `wp_cache_set_multiple()`, `wp_cache_delete_multiple()`.

#### Transients

Cache persistant avec expiration, stocké dans `wp_options` :

```php
set_transient($key, $value, $expiration);
$value = get_transient($key);
delete_transient($key);
```

Automatiquement migré vers le cache objet si un backend persistant est disponible.

### 3.4 HTTP Client

Classe `WP_Http` avec abstraction de transport :

- **Transports** : cURL (prioritaire), PHP Streams (fallback)
- **API** : `wp_remote_get()`, `wp_remote_post()`, `wp_remote_request()`
- **Safe API** : `wp_safe_remote_get()` (validation SSRF — bloque IPs privées)
- **Cookies** : Gestion automatique (`WP_Http_Cookie`)
- **Compression** : Support gzip natif
- **Timeouts** : Configurables par requête
- **SSL** : Vérification par défaut avec bundle de certificats intégré

### 3.5 Cron (Tâches planifiées)

Pseudo-cron déclenché par les visites :

```php
wp_schedule_event($timestamp, $recurrence, $hook, $args);
wp_schedule_single_event($timestamp, $hook, $args);
wp_clear_scheduled_hook($hook, $args);
```

- Déclenché via `wp-cron.php` à chaque chargement de page (ou via cron système)
- Récurrences natives : `hourly`, `twicedaily`, `daily`, `weekly`
- Extensible via filtre `cron_schedules`

### 3.6 Système de réécriture d'URL

Classe `WP_Rewrite` — gestion des permaliens :

- **Structures** : `/%year%/%monthnum%/%day%/%postname%/`, `/%postname%/`, etc.
- **Tags** : `%year%`, `%monthnum%`, `%postname%`, `%category%`, `%author%`, `%tag%`
- **Extra rules** : Ajoutables via `add_rewrite_rule()`, `add_rewrite_tag()`
- **Flush** : `flush_rewrite_rules()` pour régénérer les règles
- **Canonicalisation** : Redirection automatique vers l'URL canonique

---

## 4. Modèle de données

### 4.1 Schéma de base de données

#### Tables par site (blog-specific)

```
┌───────────────────┐       ┌───────────────────┐
│    wp_posts        │       │   wp_postmeta     │
├───────────────────┤       ├───────────────────┤
│ ID (PK)           │──────►│ meta_id (PK)      │
│ post_author (FK)  │       │ post_id (FK)      │
│ post_date         │       │ meta_key           │
│ post_date_gmt     │       │ meta_value         │
│ post_content      │       └───────────────────┘
│ post_title        │
│ post_excerpt      │       ┌───────────────────┐
│ post_status       │       │  wp_comments      │
│ comment_status    │       ├───────────────────┤
│ ping_status       │──────►│ comment_ID (PK)   │
│ post_password     │       │ comment_post_ID   │
│ post_name         │       │ comment_author    │
│ to_ping           │       │ comment_author    │
│ pinged            │       │   _email          │
│ post_modified     │       │ comment_author_url│
│ post_modified_gmt │       │ comment_author_IP │
│ post_content      │       │ comment_date      │
│   _filtered       │       │ comment_date_gmt  │
│ post_parent (self)│       │ comment_content   │
│ guid              │       │ comment_karma     │
│ menu_order        │       │ comment_approved  │
│ post_type         │       │ comment_agent     │
│ post_mime_type    │       │ comment_type      │
│ comment_count     │       │ comment_parent    │
└───────────────────┘       │ user_id (FK)      │
                            └───────┬───────────┘
                                    │
                            ┌───────▼───────────┐
                            │ wp_commentmeta    │
                            ├───────────────────┤
                            │ meta_id (PK)      │
                            │ comment_id (FK)   │
                            │ meta_key          │
                            │ meta_value        │
                            └───────────────────┘

┌───────────────────┐       ┌────────────────────┐      ┌──────────────────┐
│    wp_terms        │       │ wp_term_taxonomy   │      │wp_term_relation- │
├───────────────────┤       ├────────────────────┤      │     ships        │
│ term_id (PK)      │──────►│ term_taxonomy_id   │◄─────├──────────────────┤
│ name              │       │   (PK)             │      │ object_id (FK    │
│ slug              │       │ term_id (FK)       │      │   → wp_posts.ID) │
│ term_group        │       │ taxonomy           │      │ term_taxonomy_id │
└───────┬───────────┘       │ description        │      │   (FK)           │
        │                   │ parent             │      │ term_order       │
┌───────▼───────────┐       │ count              │      └──────────────────┘
│   wp_termmeta     │       └────────────────────┘
├───────────────────┤
│ meta_id (PK)      │       ┌───────────────────┐
│ term_id (FK)      │       │    wp_options      │
│ meta_key          │       ├───────────────────┤
│ meta_value        │       │ option_id (PK)    │
└───────────────────┘       │ option_name (UQ)  │
                            │ option_value      │
                            │ autoload          │
                            └───────────────────┘

┌───────────────────┐
│    wp_links        │
├───────────────────┤
│ link_id (PK)      │
│ link_url          │
│ link_name         │
│ link_image        │
│ link_target       │
│ link_description  │
│ link_visible      │
│ link_owner (FK)   │
│ link_rating       │
│ link_updated      │
│ link_rel          │
│ link_notes        │
│ link_rss          │
└───────────────────┘
```

#### Tables globales

```
┌───────────────────┐       ┌───────────────────┐
│    wp_users        │       │   wp_usermeta     │
├───────────────────┤       ├───────────────────┤
│ ID (PK)           │──────►│ umeta_id (PK)     │
│ user_login        │       │ user_id (FK)      │
│ user_pass         │       │ meta_key          │
│ user_nicename     │       │ meta_value        │
│ user_email        │       └───────────────────┘
│ user_url          │
│ user_registered   │
│ user_activation   │
│   _key            │
│ user_status       │
│ display_name      │
└───────────────────┘
```

#### Tables multisite additionnelles

```
┌───────────────────┐    ┌───────────────────┐    ┌───────────────────┐
│    wp_blogs        │    │    wp_site         │    │   wp_sitemeta     │
├───────────────────┤    ├───────────────────┤    ├───────────────────┤
│ blog_id (PK)      │    │ id (PK)           │    │ meta_id (PK)      │
│ site_id (FK)      │───►│ domain            │◄───│ site_id (FK)      │
│ domain            │    │ path              │    │ meta_key          │
│ path              │    └───────────────────┘    │ meta_value        │
│ registered        │                             └───────────────────┘
│ last_updated      │    ┌───────────────────┐
│ public            │    │   wp_blogmeta     │
│ archived          │    ├───────────────────┤
│ mature            │    │ meta_id (PK)      │
│ spam              │    │ blog_id (FK)      │
│ deleted           │    │ meta_key          │
│ lang_id           │    │ meta_value        │
└───────────────────┘    └───────────────────┘

┌───────────────────┐    ┌────────────────────┐
│  wp_signups       │    │ wp_registration_log│
├───────────────────┤    ├────────────────────┤
│ signup_id (PK)    │    │ ID (PK)           │
│ domain            │    │ email             │
│ path              │    │ IP                │
│ title             │    │ blog_id           │
│ user_login        │    │ date_registered   │
│ user_email        │    └────────────────────┘
│ registered        │
│ activated         │
│ active            │
│ activation_key    │
│ meta              │
└───────────────────┘
```

### 4.2 Pattern EAV (Entity-Attribute-Value)

Toutes les tables `*meta` suivent le pattern EAV permettant l'extension sans migration de schéma :

- **Avantage** : Flexibilité totale — tout plugin peut stocker des données sans ALTER TABLE
- **Inconvénient** : Performances dégradées sur les requêtes complexes (JOINs multiples)
- **Mitigation** : Cache objet, `update_meta_cache()` pour le pre-loading batch

### 4.3 Charset et collation

- **Défaut** : `utf8mb4` avec collation `utf8mb4_unicode_520_ci`
- **Longueur d'index max** : 191 caractères (utf8mb4 = 4 octets/caractère, limite InnoDB de 767 octets)
- **Rétrocompatibilité** : Support `utf8` (3 octets) pour les anciennes installations

---

## 5. Spécifications API

### 5.1 REST API (v2)

#### 5.1.1 Principes

- **Base URL** : `/wp-json/wp/v2/`
- **Namespaces** : `wp/v2` (cœur), extensible par les plugins
- **Formats** : JSON uniquement
- **Authentification** : Cookie + nonce (web), Application Passwords (externe), OAuth (applications tierces)
- **Pagination** : Headers `X-WP-Total`, `X-WP-TotalPages`, paramètres `per_page`, `page`
- **Embedding** : Paramètre `_embed` pour inclure les ressources liées (HATEOAS)
- **Batch** : Endpoint `/batch/v1` pour les requêtes groupées
- **Découverte** : Header `Link: <url>; rel="https://api.w.org/"` dans les réponses HTML

#### 5.1.2 Endpoints

| Ressource | Endpoint | Méthodes | Description |
|-----------|----------|----------|-------------|
| **Posts** | `/wp/v2/posts` | GET, POST | Articles |
| **Posts (single)** | `/wp/v2/posts/{id}` | GET, PUT, PATCH, DELETE | Article unique |
| **Post revisions** | `/wp/v2/posts/{id}/revisions` | GET | Révisions d'un article |
| **Post autosaves** | `/wp/v2/posts/{id}/autosaves` | GET, POST | Sauvegardes auto |
| **Pages** | `/wp/v2/pages` | GET, POST | Pages statiques |
| **Media** | `/wp/v2/media` | GET, POST | Bibliothèque de médias |
| **Media edit** | `/wp/v2/media/{id}/edit` | POST | Édition d'image |
| **Media sideload** | `/wp/v2/media/{id}/sideload` | POST | Sideload média |
| **Media post-process** | `/wp/v2/media/{id}/post-process` | POST | Post-traitement |
| **Comments** | `/wp/v2/comments` | GET, POST | Commentaires |
| **Categories** | `/wp/v2/categories` | GET, POST | Catégories |
| **Tags** | `/wp/v2/tags` | GET, POST | Étiquettes |
| **Users** | `/wp/v2/users` | GET, POST | Utilisateurs |
| **Users (me)** | `/wp/v2/users/me` | GET, PUT, PATCH, DELETE | Utilisateur courant |
| **Application Passwords** | `/wp/v2/users/{id}/application-passwords` | GET, POST, DELETE | Mots de passe app |
| **Taxonomies** | `/wp/v2/taxonomies` | GET | Métadonnées taxonomies |
| **Post Types** | `/wp/v2/types` | GET | Métadonnées types de contenu |
| **Post Statuses** | `/wp/v2/statuses` | GET | Statuts disponibles |
| **Settings** | `/wp/v2/settings` | GET, PUT, PATCH | Réglages du site |
| **Themes** | `/wp/v2/themes` | GET | Thèmes installés |
| **Plugins** | `/wp/v2/plugins` | GET, POST | Plugins installés |
| **Search** | `/wp/v2/search` | GET | Recherche unifiée |
| **Block Types** | `/wp/v2/block-types` | GET | Types de blocs |
| **Blocks** | `/wp/v2/blocks` | GET, POST | Blocs réutilisables |
| **Block Patterns** | `/wp/v2/block-patterns/patterns` | GET | Patterns de blocs |
| **Block Pattern Categories** | `/wp/v2/block-patterns/categories` | GET | Catégories de patterns |
| **Block Directory** | `/wp/v2/block-directory/search` | GET | Répertoire de blocs |
| **Block Renderer** | `/wp/v2/block-renderer/{name}` | POST | Rendu serveur |
| **Templates** | `/wp/v2/templates` | GET, POST | Templates FSE |
| **Template Parts** | `/wp/v2/template-parts` | GET, POST | Parties de templates |
| **Global Styles** | `/wp/v2/global-styles` | GET, PUT | Styles globaux |
| **Font Families** | `/wp/v2/font-families` | GET, POST | Familles de polices |
| **Font Faces** | `/wp/v2/font-families/{id}/font-faces` | GET, POST | Faces de polices |
| **Font Collections** | `/wp/v2/font-collections` | GET | Collections de polices |
| **Menus** | `/wp/v2/menus` | GET, POST | Menus de navigation |
| **Menu Items** | `/wp/v2/menu-items` | GET, POST | Éléments de menu |
| **Menu Locations** | `/wp/v2/menu-locations` | GET | Emplacements de menu |
| **Navigation Fallback** | `/wp/v2/navigation/fallback` | GET | Navigation par défaut |
| **Sidebars** | `/wp/v2/sidebars` | GET, PUT | Zones de widgets |
| **Widgets** | `/wp/v2/widgets` | GET, POST | Widgets |
| **Widget Types** | `/wp/v2/widget-types` | GET | Types de widgets |
| **Site Health** | `/wp/v2/site-health` | GET | Santé du site |
| **URL Details** | `/wp/v2/url-details` | GET | Métadonnées d'URL (embeds) |
| **Edit Site Export** | `/wp/v2/edit-site/export` | GET | Export du site |
| **Icons** | `/wp/v2/icons` | GET | Icônes disponibles |

#### 5.1.3 Schema de réponse (exemple : Post)

```json
{
  "id": 123,
  "date": "2026-02-25T10:30:00",
  "date_gmt": "2026-02-25T09:30:00",
  "guid": { "rendered": "https://example.com/?p=123" },
  "modified": "2026-02-25T12:00:00",
  "modified_gmt": "2026-02-25T11:00:00",
  "slug": "mon-article",
  "status": "publish",
  "type": "post",
  "link": "https://example.com/2026/02/25/mon-article/",
  "title": { "rendered": "Mon article" },
  "content": { "rendered": "<p>Contenu...</p>", "protected": false },
  "excerpt": { "rendered": "<p>Extrait...</p>", "protected": false },
  "author": 1,
  "featured_media": 456,
  "comment_status": "open",
  "ping_status": "open",
  "sticky": false,
  "template": "",
  "format": "standard",
  "meta": { "custom_field": "value" },
  "categories": [1, 5],
  "tags": [10, 20],
  "_links": {
    "self": [{ "href": "https://example.com/wp-json/wp/v2/posts/123" }],
    "author": [{ "embeddable": true, "href": "https://example.com/wp-json/wp/v2/users/1" }],
    "replies": [{ "embeddable": true, "href": "https://example.com/wp-json/wp/v2/comments?post=123" }]
  }
}
```

### 5.2 XML-RPC (Legacy)

- **Endpoint** : `/xmlrpc.php`
- **Statut** : Maintenu pour rétrocompatibilité, REST API recommandé
- **APIs** : WordPress API, Blogger API, MetaWeblog API, MovableType API, Pingback
- **Désactivable** : Filtre `xmlrpc_enabled`
- **Méthodes notables** : `wp.newPost`, `wp.editPost`, `wp.deletePost`, `wp.getPosts`, `wp.uploadFile`, `wp.getUsers`, etc.

### 5.3 Admin AJAX

- **Endpoint** : `/wp-admin/admin-ajax.php`
- **Hooks** : `wp_ajax_{action}` (authentifié), `wp_ajax_nopriv_{action}` (non authentifié)
- **Paramètre** : `action` (obligatoire dans la requête)
- **Statut** : Toujours utilisé pour l'admin, progressivement remplacé par REST API pour les nouvelles fonctionnalités

---

## 6. Sécurité — implémentation

### 6.1 Protection contre les injections

| Vecteur | Protection | Implémentation |
|---------|-----------|----------------|
| **SQL Injection** | Requêtes préparées | `$wpdb->prepare()` avec placeholders `%s`, `%d`, `%f` |
| **XSS** | Échappement en sortie | `esc_html()`, `esc_attr()`, `esc_url()`, `esc_js()`, `wp_kses()` |
| **CSRF** | Nonces | `wp_create_nonce()`, `wp_verify_nonce()`, `check_admin_referer()` |
| **File Upload** | Validation MIME | `wp_check_filetype()`, `wp_check_filetype_and_ext()` |
| **SSRF** | Validation d'URL | `wp_http_validate_url()` — bloque IPs privées et locales |
| **Path Traversal** | Sanitisation de chemins | `sanitize_file_name()`, `validate_file()` |

### 6.2 Sanitisation des entrées

| Fonction | Usage |
|----------|-------|
| `sanitize_text_field()` | Texte générique |
| `sanitize_email()` | Adresses email |
| `sanitize_url()` | URLs |
| `sanitize_title()` | Titres (slugs) |
| `sanitize_file_name()` | Noms de fichiers |
| `sanitize_html_class()` | Classes CSS |
| `sanitize_key()` | Clés (lowercase alphanumeric + tirets) |
| `wp_kses_post()` | HTML autorisé dans les posts |
| `wp_kses()` | HTML avec whitelist personnalisée |
| `absint()` | Entiers positifs |
| `intval()` | Entiers |

### 6.3 Hachage des mots de passe

- **Algorithme** : bcrypt via `wp_hash_password()` (PHPass compatible, migration progressive)
- **Vérification** : `wp_check_password()`
- **Salage** : 8 clés secrètes dans `wp-config.php` : `AUTH_KEY`, `SECURE_AUTH_KEY`, `LOGGED_IN_KEY`, `NONCE_KEY`, `AUTH_SALT`, `SECURE_AUTH_SALT`, `LOGGED_IN_SALT`, `NONCE_SALT`
- **Fonctions pluggables** : Remplaçables via `pluggable.php`

### 6.4 Sécurité des fichiers

- `index.php` vide dans chaque répertoire (empêche le listing)
- `.htaccess` pour bloquer l'accès direct à `wp-includes/`
- Permissions recommandées : répertoires 755, fichiers 644, `wp-config.php` 400

### 6.5 Headers de sécurité

- `X-Content-Type-Options: nosniff` (via `send_nosniff_header()`)
- `X-Frame-Options` (via `send_frame_options_header()`)
- Support HTTPS natif avec redirection automatique

### 6.6 Mode récupération

En cas d'erreur fatale causée par un plugin ou thème :
1. WordPress capture l'erreur (`WP_Fatal_Error_Handler`)
2. Envoie un email à l'admin avec un lien de récupération
3. Le mode récupération désactive le plugin/thème fautif
4. L'admin peut accéder à l'admin pour corriger le problème

---

## 7. Performance — stratégies

### 7.1 Stratégies de cache

| Niveau | Mécanisme | Portée |
|--------|-----------|--------|
| **Cache objet** | `WP_Object_Cache` | Requête (ou persistant via drop-in Redis/Memcached) |
| **Transients** | `set_transient()` | Persistant avec expiration (stocké en DB ou cache objet) |
| **Cache de page** | Drop-in `advanced-cache.php` | Page complète (Varnish, WP Super Cache, etc.) |
| **Cache de requête** | `WP_Query` interne | Résultats de requêtes SQL |
| **Cache d'options** | `wp_load_alloptions()` | Options autoloaded en une seule requête |
| **Cache de métadonnées** | `update_meta_cache()` | Pre-loading batch des meta |

### 7.2 Optimisations des assets

- **Script/Style loader** (`script-loader.php`, 4 139 lignes) — gestion des dépendances JS/CSS
- **Concatenation** : Support de la concaténation des scripts admin (`/wp-admin/load-scripts.php`)
- **Minification** : Assets distribués en versions `.min.js` / `.min.css`
- **Versioning** : Cache-busting par numéro de version WordPress
- **Lazy loading** : `loading="lazy"` automatique sur les images et iframes
- **Async/Defer** : Support des attributs `async` et `defer` sur les scripts

### 7.3 Optimisations base de données

- **Autoload** : Seules les options avec `autoload=yes` sont chargées au boot
- **Index** : Index composites optimisés (ex : `type_status_date` sur `wp_posts`)
- **Batch queries** : `update_post_caches()` pré-charge termes, meta, et auteurs en batch
- **Lazy loading** des termes et métadonnées

---

## 8. Internationalisation — infrastructure

### 8.1 Système de traduction

- **Fonctions** : `__()`, `_e()`, `_n()` (pluriel), `_x()` (contexte), `_nx()` (contexte + pluriel)
- **Échappement** : `esc_html__()`, `esc_attr__()`, `esc_html_e()`, `esc_attr_e()`
- **Domaines** : Chaque plugin/thème possède son domaine de traduction
- **Formats** : PO (éditable), MO (binaire compilé), PHP (tableaux)
- **Chargement** : `load_textdomain()`, `load_plugin_textdomain()`, `load_theme_textdomain()`

### 8.2 Infrastructure technique

- Classe `WP_Translation_Controller` — orchestrateur i18n
- Support MO (Machine Object) et PHP pour les fichiers de traduction
- Gestion des formes plurielles complexes (`Plural-Forms` header)
- Locale par utilisateur (`get_user_locale()`) et par site (`get_locale()`)
- 200+ langues disponibles via WordPress.org

### 8.3 Localisation (l10n)

- Formats de date/heure localisés
- Formats numériques
- Direction de texte (LTR/RTL) — support natif CSS et JavaScript
- Polices Google Noto pour couverture Unicode maximale

### 8.4 Accessibilité — implémentation

- **Rôles ARIA** : Navigation, contenus, formulaires
- **Labels** : Tous les champs de formulaire sont labellisés
- **Contraste** : Ratio minimum 4.5:1 (texte), 3:1 (grandes tailles)
- **Navigation clavier** : Tout l'admin est navigable au clavier
- **Screen readers** : Texte `screen-reader-text` pour contexte invisible
- **Skip links** : Liens d'accès rapide au contenu
- **Focus management** : États de focus visibles et gérés
- **Live regions** : ARIA live pour les mises à jour dynamiques
- **RTL** : Support complet des langues droite-à-gauche

---

## 9. Extensibilité et écosystème

### 9.1 Architecture d'extensibilité

```
┌─────────────────────────────────────────┐
│              WordPress Core              │
│                                         │
│  ┌──────────────┐  ┌─────────────────┐ │
│  │   Hooks      │  │ Pluggable       │ │
│  │ (Actions &   │  │ Functions       │ │
│  │  Filters)    │  │ (pluggable.php) │ │
│  └──────┬───────┘  └───────┬─────────┘ │
│         │                  │           │
│  ┌──────▼──────────────────▼─────────┐ │
│  │        Extension Points           │ │
│  ├───────────────────────────────────┤ │
│  │ • Custom Post Types              │ │
│  │ • Custom Taxonomies              │ │
│  │ • Custom Block Types             │ │
│  │ • REST API Endpoints             │ │
│  │ • Admin Pages & Menus            │ │
│  │ • Widget Types                   │ │
│  │ • Shortcodes                     │ │
│  │ • Cron Jobs                      │ │
│  │ • Rewrite Rules                  │ │
│  │ • Image Sizes                    │ │
│  │ • Customizer Controls            │ │
│  │ • Block Supports                 │ │
│  │ • Block Patterns                 │ │
│  │ • Block Bindings                 │ │
│  │ • Font Collections               │ │
│  └───────────────────────────────────┘ │
└─────────────┬───────────────────────────┘
              │
    ┌─────────▼─────────┐
    │    wp-content/     │
    ├────────────────────┤
    │ ├── mu-plugins/   │  ← Must-use plugins (toujours actifs)
    │ ├── plugins/      │  ← Plugins installables
    │ ├── themes/       │  ← Thèmes (parent + enfant)
    │ ├── uploads/      │  ← Médias uploadés
    │ ├── languages/    │  ← Traductions
    │ ├── upgrade/      │  ← Fichiers temporaires de mise à jour
    │ └── drop-ins:     │
    │     ├── db.php               ← Remplacement wpdb
    │     ├── object-cache.php     ← Cache objet persistant
    │     ├── advanced-cache.php   ← Cache de page
    │     ├── maintenance.php      ← Page de maintenance
    │     ├── sunrise.php          ← Bootstrap multisite
    │     └── blog-deleted.php     ← Page site supprimé
    └────────────────────┘
```

### 9.2 Types d'extensions

| Type | Emplacement | Chargement | Cas d'usage |
|------|-------------|-----------|-------------|
| **Must-Use Plugin** | `wp-content/mu-plugins/` | Automatique, non désactivable | Configurations critiques, security hardening |
| **Plugin** | `wp-content/plugins/` | Manuel (activation admin) | Fonctionnalités additionnelles |
| **Thème parent** | `wp-content/themes/` | Un seul actif | Design et structure du frontend |
| **Thème enfant** | `wp-content/themes/` | Hérite du parent | Personnalisation sans modifier le parent |
| **Drop-in** | `wp-content/` | Automatique (si le fichier existe) | Remplacement de composants core |

### 9.3 Registres globaux

| Registre | Classe/Global | Fonction d'enregistrement |
|----------|---------------|---------------------------|
| Post Types | `$wp_post_types` | `register_post_type()` |
| Taxonomies | `$wp_taxonomies` | `register_taxonomy()` |
| Block Types | `WP_Block_Type_Registry` | `register_block_type()` |
| Block Patterns | `WP_Block_Patterns_Registry` | `register_block_pattern()` |
| Block Bindings | `WP_Block_Bindings_Registry` | `register_block_bindings_source()` |
| Widgets | `WP_Widget_Factory` | `register_widget()` |
| Shortcodes | `$shortcode_tags` | `add_shortcode()` |
| Sidebars | `$wp_registered_sidebars` | `register_sidebar()` |
| Scripts | `WP_Scripts` | `wp_register_script()` |
| Styles | `WP_Styles` | `wp_register_style()` |
| Rewrites | `WP_Rewrite` | `add_rewrite_rule()` |
| Settings | Settings API | `register_setting()` |
| REST Routes | `WP_REST_Server` | `register_rest_route()` |
| Image Sizes | `$_wp_additional_image_sizes` | `add_image_size()` |
| Nav Menus | `$_wp_registered_nav_menus` | `register_nav_menus()` |
| Font Collections | `WP_Font_Collections` | `wp_register_font_collection()` |

### 9.4 Thèmes distribués

WordPress 7.0 inclut les thèmes par défaut suivants :

| Thème | Type | Année |
|-------|------|-------|
| Twenty Twenty-Five | Bloc (FSE) | 2025 |
| Twenty Twenty-Four | Bloc (FSE) | 2024 |
| Twenty Twenty-Three | Bloc (FSE) | 2023 |
| Twenty Twenty-Two | Bloc (FSE) | 2022 |
| Twenty Twenty-One | Classique | 2021 |
| Twenty Twenty | Classique | 2020 |
| Twenty Nineteen | Classique | 2019 |
| Twenty Seventeen | Classique | 2017 |
| Twenty Sixteen | Classique | 2016 |
| Twenty Fifteen | Classique | 2015 |

---

## 10. Infrastructure et déploiement

### 10.1 Prérequis serveur

| Composant | Minimum | Recommandé |
|-----------|---------|------------|
| **PHP** | 7.4 | 8.3+ |
| **MySQL** | 5.5.5 | 8.0+ |
| **MariaDB** | 10.0 | 10.6+ |
| **Extensions PHP requises** | `json`, `hash` | + `mysqli`, `openssl`, `curl`, `mbstring`, `xml`, `gd`/`imagick`, `zip`, `intl`, `sodium` |
| **Serveur web** | Apache 2.4+ ou Nginx 1.18+ | Avec `mod_rewrite` (Apache) |
| **HTTPS** | Recommandé | Obligatoire pour les fonctionnalités de sécurité avancées |
| **Mémoire PHP** | 64MB | 256MB+ |

### 10.2 Structure de fichiers

```
wordpress/
├── index.php                  ← Point d'entrée frontend
├── wp-load.php                ← Bootstrap
├── wp-blog-header.php         ← Charge environnement + template
├── wp-config.php              ← Configuration (non versionné)
├── wp-config-sample.php       ← Template de configuration
├── wp-settings.php            ← Initialisation complète
├── wp-login.php               ← Authentification
├── wp-cron.php                ← Pseudo-cron
├── wp-comments-post.php       ← Soumission de commentaires
├── wp-mail.php                ← Traitement des emails
├── wp-signup.php              ← Inscription multisite
├── wp-activate.php            ← Activation de compte
├── wp-trackback.php           ← Réception de trackbacks
├── wp-links-opml.php          ← Export OPML des liens
├── xmlrpc.php                 ← API XML-RPC
├── wp-admin/                  ← Interface d'administration
│   ├── admin.php              ← Bootstrap admin
│   ├── admin-ajax.php         ← Endpoint AJAX
│   ├── includes/              ← Librairies admin (106 fichiers)
│   ├── css/                   ← Styles admin (146 fichiers)
│   ├── js/                    ← Scripts admin (111 fichiers)
│   ├── images/                ← Images UI (79 fichiers)
│   ├── network/               ← Admin réseau multisite
│   └── user/                  ← Pages utilisateur multisite
├── wp-includes/               ← Cœur WordPress (~3 800 fichiers)
│   ├── blocks/                ← 801 fichiers — blocs Gutenberg
│   ├── css/                   ← 840 fichiers — styles
│   ├── js/                    ← 705 fichiers — scripts
│   ├── build/                 ← 106 fichiers — artefacts compilés
│   ├── rest-api/              ← 57 fichiers — infrastructure REST
│   ├── customize/             ← 36 fichiers — Customizer
│   ├── widgets/               ← 20 fichiers — widgets natifs
│   ├── block-supports/        ← 22 fichiers — supports de blocs
│   ├── html-api/              ← 14 fichiers — parsing HTML5
│   ├── sitemaps/              ← 9 fichiers — sitemaps XML
│   ├── fonts/                 ← 10 fichiers — gestion polices
│   ├── interactivity-api/     ← 3 fichiers — interactivité frontend
│   ├── block-bindings/        ← 4 fichiers — liaisons de blocs
│   ├── abilities-api/         ← 4 fichiers — API de capacités
│   ├── ai-client/             ← 6 fichiers — client AI
│   ├── collaboration/         ← 3 fichiers — collaboration
│   ├── sodium_compat/         ← 108 fichiers — cryptographie
│   ├── Requests/              ← 65 fichiers — bibliothèque HTTP
│   ├── SimplePie/             ← 82 fichiers — parsing de flux
│   ├── PHPMailer/             ← 7 fichiers — envoi d'emails
│   ├── IXR/                   ← 10 fichiers — XML-RPC
│   ├── pomo/                  ← 6 fichiers — traductions PO/MO
│   ├── Text/                  ← 8 fichiers — diff de texte
│   ├── l10n/                  ← 5 fichiers — localisation
│   ├── ID3/                   ← 18 fichiers — métadonnées audio
│   └── [~250 fichiers PHP racine]
└── wp-content/                ← Contenu utilisateur
    ├── plugins/               ← Extensions installées
    ├── themes/                ← Thèmes installés
    ├── uploads/               ← Fichiers média (runtime)
    ├── languages/             ← Fichiers de traduction
    ├── upgrade/               ← Temporaire mises à jour
    └── index.php              ← Protection listing
```

### 10.3 Configuration (wp-config.php)

**Obligatoire :**
```php
define('DB_NAME',     'wordpress');
define('DB_USER',     'root');
define('DB_PASSWORD', '');
define('DB_HOST',     'localhost');
define('DB_CHARSET',  'utf8mb4');
define('DB_COLLATE',  '');
$table_prefix = 'wp_';
```

**Clés de sécurité (obligatoire) :**
```php
define('AUTH_KEY',         'unique-phrase');
define('SECURE_AUTH_KEY',  'unique-phrase');
define('LOGGED_IN_KEY',    'unique-phrase');
define('NONCE_KEY',        'unique-phrase');
define('AUTH_SALT',        'unique-phrase');
define('SECURE_AUTH_SALT', 'unique-phrase');
define('LOGGED_IN_SALT',   'unique-phrase');
define('NONCE_SALT',       'unique-phrase');
```

**Optionnel (debug, multisite, paths, etc.) :**
```php
define('WP_DEBUG',             false);
define('WP_DEBUG_LOG',         false);
define('WP_DEBUG_DISPLAY',     false);
define('SCRIPT_DEBUG',         false);
define('WP_ENVIRONMENT_TYPE',  'production');  // local, development, staging, production
define('MULTISITE',            false);
define('WP_ALLOW_MULTISITE',   false);
define('WP_CONTENT_DIR',       '/path/to/wp-content');
define('WP_CONTENT_URL',       'https://example.com/wp-content');
define('WP_MEMORY_LIMIT',      '256M');
define('WP_MAX_MEMORY_LIMIT',  '512M');
define('DISALLOW_FILE_EDIT',   true);
define('DISALLOW_FILE_MODS',   true);
define('AUTOMATIC_UPDATER_DISABLED', false);
define('WP_AUTO_UPDATE_CORE',  'minor');
```

---

## 11. Dépendances tierces intégrées

| Bibliothèque | Fichiers | Usage |
|--------------|----------|-------|
| **PHPMailer** | `PHPMailer/` (7 fichiers) | Envoi d'emails SMTP |
| **SimplePie** | `SimplePie/` (82 fichiers) | Parsing de flux RSS/Atom |
| **Requests** | `Requests/` (65 fichiers) | Couche HTTP (transport cURL/streams) |
| **sodium_compat** | `sodium_compat/` (108 fichiers) | Cryptographie (polyfill libsodium) |
| **PHPass** | `class-phpass.php` | Hachage de mots de passe (legacy) |
| **PO/MO** | `pomo/` (6 fichiers) | Parsing de fichiers de traduction |
| **getID3** | `ID3/` (18 fichiers) | Métadonnées audio/vidéo |
| **IXR** | `IXR/` (10 fichiers) | Client/Serveur XML-RPC |
| **TinyMCE** | intégré aux JS | Éditeur classique (legacy) |
| **React** | intégré au build | Éditeur de blocs (Gutenberg) |

---

## 12. Risques architecturaux

| Risque | Sévérité | Description |
|--------|----------|-------------|
| **God Classes** | Haute | `WP_Query` (5 113 lignes), `wpdb` (4 146 lignes), `WP_Customize_Manager` (6 162 lignes) concentrent trop de responsabilités |
| **God Files** | Haute | `functions.php` (9 255 lignes, 214 fonctions), `post.php` (8 697 lignes) |
| **Couplage fort** | Haute | `get_option()` utilisé dans 75 fichiers, `wp_parse_args()` dans 59 fichiers |
| **Dépendances circulaires** | Moyenne | Hook system ↔ tous les modules ↔ options ↔ cache |
| **Pattern EAV** | Moyenne | Tables `*meta` peuvent devenir un goulet d'étranglement de performance |
| **Globales** | Moyenne | Usage extensif de variables globales (`$wpdb`, `$wp_query`, `$post`, etc.) |
| **Absence de typage** | Basse | Pas de types stricts PHP, PHPDoc comme seule documentation de types |

---

*Ce document d'architecture est le compagnon technique du [PRD.md](PRD.md). Il doit être mis à jour à chaque release majeure.*
