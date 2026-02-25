# WordPress wp-includes/ — Analyse des dépendances et God Classes

> **Date** : 25 février 2026
> **Scope** : Analyse statique de `wp-includes/` (29+ fichiers, 1 500+ fonctions)
> **Livrable** : Ce document + `WordPress_wp-includes_Analysis.pptx`

---

## 1. Carte des dépendances — Top 10 fichiers les plus couplés

```
                        +---------------------+
                        |    plugin.php        |  Score couplage: 10/10
                        |  (Hook System)       |  <- TOUT fichier en dépend
                        | add_filter/do_action |
                        +---------+-----------+
                                  |
              +-------------------+-------------------+
              |                   |                   |
              v                   v                   v
   +------------------+ +-----------------+ +-----------------+
   |  functions.php   | |   post.php      | |  formatting.php |
   |  214 fonctions   | |  145 fonctions  | |  120 fonctions  |
   |  292 KB          | |  301 KB         | |  353 KB         |
   |  Score: 8/10     | |  Score: 9/10    | |  Score: 7/10    |
   |  (11 refs)       | |  (29 refs)      | |  (20 refs)      |
   +-------+----------+ +--+------+-------+ +--------+--------+
           |               |      |                   |
           v               v      |                   |
   +------------------+    |      |    +--------------v--------+
   |   option.php     |<---+      |    |  general-template.php |
   |  get_option()    |           |    |  94 fonctions, 176 KB |
   |  75 fichiers     |           |    |  Score: 6/10          |
   |  l'utilisent     |           |    +-----------------------+
   +------------------+           |
                                  v
        +-----------------+  +-----------------+  +------------------+
        |  taxonomy.php   |  |    user.php     |  |    media.php     |
        |  80 fonctions   |  |  78 fonctions   |  |  99 fonctions    |
        |  Score: 5/10    |  |  Score: 6/10    |  |  Score: 6/10     |
        |  (12 refs)      |  |  (9 refs)       |  |  (15 refs)       |
        +--------+--------+  +--------+--------+  +--------+---------+
                 |                    |                     |
                 +------------+------+                     |
                              v                            |
                    +------------------+                   |
                    |    meta.php      |<------------------+
                    |  Score: 5/10     |
                    |  (7 refs)        |
                    +------------------+

        ===================================================
         Couche base de données (couplage global)
        +--------------------------------------------------+
        |            class-wpdb.php                        |
        |  72 méthodes, 4 146 lignes, Score: 10/10         |
        |  -> TOUT accès BDD passe par $wpdb               |
        +--------------------------------------------------+
```

### Résumé du couplage (Top 10)

| # | Fichier | Fonctions | Taille | Réf. par N fichiers | Score |
|---|---------|-----------|--------|---------------------|-------|
| 1 | `plugin.php` | Hook system | — | **tous** | **10/10** |
| 2 | `class-wpdb.php` | 72 méthodes | 120 KB | **tous** (BDD) | **10/10** |
| 3 | `post.php` | 145 | 301 KB | 29 | **9/10** |
| 4 | `functions.php` | 214 | 292 KB | 11 | **8/10** |
| 5 | `formatting.php` | 120 | 353 KB | 20 | **7/10** |
| 6 | `media.php` | 99 | 228 KB | 15 | **6/10** |
| 7 | `general-template.php` | 94 | 176 KB | 9 | **6/10** |
| 8 | `user.php` | 78 | 180 KB | 9 | **6/10** |
| 9 | `taxonomy.php` | 80 | 179 KB | 12 | **5/10** |
| 10 | `meta.php` | ~30 | 67 KB | 7 | **5/10** |

---

## 2. God Classes identifiées

### 2.1 WP_Query (`class-wp-query.php`) — God Class principale

- **5 113 lignes**, 68 méthodes, 165 KB
- Cumule **~15 responsabilités** :
  1. Parsing d'URL et interprétation de requête
  2. Construction SQL bas niveau
  3. Gestion de boucle posts (`next_post`, `the_post`, `have_posts`, `rewind_posts`)
  4. Gestion de boucle commentaires (`next_comment`, `the_comment`, `have_comments`)
  5. Détection d'archives (`is_archive`, `is_category`, `is_tag`, `is_author`…)
  6. Logique conditionnelle de templates (`is_single`, `is_page`, `is_singular`, `is_404`…)
  7. Tri et ordonnancement (`parse_orderby`, `parse_order`)
  8. Requêtes taxonomie (`parse_tax_query`)
  9. Requêtes meta (`WP_Meta_Query`, `WP_Tax_Query`, `WP_Date_Query`)
  10. Gestion du cache de résultats

**Verdict** : Violation massive du principe de responsabilité unique (SRP).

### 2.2 wpdb (`class-wpdb.php`) — Le monopole BDD

- **4 146 lignes**, 72 méthodes, 120 KB
- Responsabilités concentrées :
  - Connexion et abstraction BDD
  - Échappement et sanitisation des requêtes
  - Gestion des préfixes de table
  - Support multi-types de BDD
  - Couche de mise en cache
  - Exécution de requêtes
  - Formatage des résultats
  - Introspection de schéma

**Verdict** : Impossible à refactorer sans tout casser — point de passage obligatoire de toute opération BDD.

### 2.3 WP_Customize_Manager (`class-wp-customize-manager.php`) — Le monolithe UI

- **6 162 lignes** (le plus gros fichier classe), **117 méthodes**, 205 KB
- Responsabilités :
  1. UI de personnalisation du thème
  2. Enregistrement et gestion des contrôles
  3. Gestion des panneaux et sections
  4. Enregistrement des settings
  5. Gestion JavaScript/CSS
  6. Gestion du preview
  7. Logique de sauvegarde/validation
  8. Vérification des capabilities utilisateur

### 2.4 functions.php — God File procédural

- **9 255 lignes**, **214 fonctions**, 292 KB
- Catégories mélangées : dates, sanitisation, compat PHP, erreurs, URLs, tableaux, mémoire, plugins…
- Dépendance implicite dans tout le codebase ("fourre-tout").

### 2.5 post.php — Le fichier le plus couplé

- **8 697 lignes**, 145 fonctions, 301 KB, **111 appels de hooks** (record)
- 29 fichiers en dépendent directement
- Mélange CRUD, révisions, attachments, post types, taxonomie, métadonnées

---

## 3. Couplage fonctionnel — Fonctions les plus utilisées

| Fonction | Fichier source | Utilisée dans N fichiers |
|----------|---------------|--------------------------|
| `get_option()` | option.php | 75 fichiers |
| `wp_parse_args()` | functions.php | 59 fichiers |
| `get_post()` | post.php | 44 fichiers |
| `add_filter()` | plugin.php | 40+ fichiers |
| `wp_insert_post()` | post.php | 11 fichiers |
| `sanitize_text_field()` | formatting.php | 9 fichiers |

---

## 4. Dépendances circulaires détectées

### Boucle 1 — Hook System
Tous les fichiers → `add_filter()`/`do_action()` (plugin.php) → `default-filters.php` enregistre des hooks vers des fonctions de multiples modules → co-dépendance implicite généralisée.

### Boucle 2 — Formatting
`formatting.php` chargé très tôt (ligne 19 du bootstrap), appelé par `post.php`, `user.php`, `media.php` qui sont eux-mêmes référencés par `formatting.php`.

### Boucle 3 — Option/Meta
`option.php` ↔ `post.php` / `user.php` / `taxonomy.php` via les invalidations de cache dans `default-filters.php`.

---

## 5. Ordre de chargement (wp-settings.php)

| Étape | Fichier | Rôle |
|-------|---------|------|
| 1 | `version.php` | Constantes de version |
| 2 | `compat-utf8.php`, `compat.php` | Couche de compatibilité |
| 3 | `load.php` | Fonctions d'initialisation (58 KB) |
| 4 | `plugin.php` | Système de hooks — **CRITIQUE** |
| 5 | `formatting.php` | Formatage texte (353 KB) |
| 6 | `meta.php` | Fonctions métadonnées |
| 7 | `functions.php` | Fonctions utilitaires (292 KB) |
| 8 | `default-filters.php` | Enregistrement des hooks par défaut |
| 9 | Capabilities | `class-wp-roles.php`, `class-wp-role.php`, `class-wp-user.php` |
| 10 | `class-wp-query.php` | Moteur de requêtes (5 113 lignes) |
| 11 | `query.php`, `theme.php` | Fonctions de requête et thème |
| 12 | `post.php`, `user.php`, `taxonomy.php`… | Modules métier |

---

## 6. Recommandations de refactoring

### Priorité 1 — Découper WP_Query
- Extraire la construction SQL dans un `QueryBuilder` dédié
- Extraire la gestion de boucle dans un `LoopManager`
- Extraire les conditionals (`is_single`, `is_page`…) dans un `TemplateResolver`

### Priorité 2 — Découper post.php
- Séparer CRUD posts, révisions, attachments, types de post en fichiers distincts
- Regrouper les 111 hooks en groupes logiques
- Créer `post-crud.php`, `post-types.php`, `revisions.php`

### Priorité 3 — Découper functions.php
- Regrouper les 214 fonctions par domaine (dates, arrays, URLs, sanitisation…)
- Créer des modules spécialisés

### Priorité 4 — Abstraire wpdb
- Introduire un pattern Repository pour découpler l'accès données
- Séparer connexion, sanitisation et exécution
- Permettre des implémentations alternatives

---

## 7. Conclusion

- L'architecture WordPress repose sur **3 piliers fortement couplés** : `plugin.php`, `wpdb`, `post.php`
- **5 God Classes/Files majeurs** concentrent une complexité disproportionnée
- Le système de hooks agit comme un **bus d'événements implicite** rendant les dépendances difficiles à tracer statiquement
- Les dépendances circulaires via le système de hooks créent un réseau de couplage complexe
- Le couplage fort est un héritage historique de WordPress (lancé en 2003) — il explique à la fois la robustesse du CMS et la difficulté à le moderniser
