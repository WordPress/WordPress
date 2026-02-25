# PRD — WordPress 7.0

## Product Requirements Document

**Version :** 7.0-beta1 (build 61735)
**Révision DB :** 61696
**Date :** Février 2026
**Statut :** En développement actif depuis 2003
**Document compagnon :** [ARCHITECTURE.md](ARCHITECTURE.md) (architecture technique)

---

## Table des matières

1. [Vision produit](#1-vision-produit)
2. [Contexte et historique](#2-contexte-et-historique)
3. [Utilisateurs cibles et personas](#3-utilisateurs-cibles-et-personas)
4. [Objectifs produit](#4-objectifs-produit)
5. [Exigences fonctionnelles](#5-exigences-fonctionnelles)
6. [Exigences non-fonctionnelles](#6-exigences-non-fonctionnelles)
7. [Contraintes et dépendances](#7-contraintes-et-dépendances)
8. [Métriques de succès](#8-métriques-de-succès)

---

## 1. Vision produit

### 1.1 Énoncé de vision

WordPress est un **système de gestion de contenu (CMS) open source** dont la mission fondamentale est de **démocratiser la publication sur le web**. Il vise à offrir à tout utilisateur — du blogueur individuel à l'entreprise multinationale — la capacité de créer, gérer et publier du contenu web sans connaissance technique préalable, tout en fournissant aux développeurs une plateforme extensible et robuste.

### 1.2 Proposition de valeur

| Dimension | Proposition |
|-----------|-------------|
| **Simplicité** | Installation en 5 minutes, interface d'administration intuitive |
| **Flexibilité** | Du blog personnel au site e-commerce, du portail média à l'application web |
| **Extensibilité** | Architecture de hooks (actions/filtres) permettant la modification sans toucher au cœur |
| **Liberté** | Licence GPLv2+, pas de vendor lock-in, données exportables |
| **Communauté** | Écosystème de 60 000+ plugins, 10 000+ thèmes, traductions en 200+ langues |

### 1.3 Principes directeurs

1. **Rétrocompatibilité** — Ne jamais casser le web existant
2. **Décisions, pas options** — Privilégier les choix intelligents par défaut aux configurations complexes
3. **Accessibilité** — L'interface doit être utilisable par tous, sans exception
4. **Simplicité d'usage** — 80% des utilisateurs ne devraient jamais avoir besoin de code
5. **Code propre** — Lisible, bien documenté, testé

---

## 2. Contexte et historique

### 2.1 Origine

WordPress est né en 2003 comme un fork de **b2/cafelog**, un outil de blogging PHP/MySQL. Il a évolué d'un simple moteur de blog vers un CMS complet, puis vers une plateforme applicative à part entière.

### 2.2 Jalons architecturaux majeurs

| Version | Année | Contribution architecturale |
|---------|-------|----------------------------|
| 1.0 | 2004 | Fondations : boucle de requête, système de template |
| 1.5 | 2005 | Système de thèmes et pages statiques |
| 2.0 | 2005 | Système de plugins (hooks), rôles & capacités |
| 2.1 | 2007 | Autosave, révisions |
| 2.7 | 2008 | Refonte complète de l'admin |
| 3.0 | 2010 | Custom post types, custom taxonomies, multisite |
| 3.4 | 2012 | Customizer (personnalisation en temps réel) |
| 4.4 | 2015 | REST API (phase 1 — infrastructure) |
| 4.7 | 2016 | REST API (phase 2 — endpoints de contenu) |
| 5.0 | 2018 | Éditeur de blocs (Gutenberg) |
| 5.8 | 2021 | Widgets basés sur les blocs |
| 5.9 | 2022 | Full Site Editing (FSE) |
| 6.3 | 2023 | Patterns synchronisés, Command Palette |
| 6.5 | 2024 | Interactivity API, Font Library |
| 7.0 | 2026 | Abilities API, AI client intégré, collaboration |

### 2.3 Positionnement marché

WordPress propulse **~43% du web mondial** (toutes versions confondues), ce qui en fait le CMS le plus déployé de l'histoire du logiciel. Son écosystème économique est estimé à plusieurs milliards de dollars.

---

## 3. Utilisateurs cibles et personas

### 3.1 Persona 1 — Le Créateur de contenu

| Attribut | Description |
|----------|-------------|
| **Profil** | Blogueur, journaliste, rédacteur web |
| **Compétences techniques** | Faibles à modérées |
| **Besoins** | Écrire, formater, publier du contenu multimédia rapidement |
| **Frustrations** | Interfaces complexes, perte de contenu, SEO difficile |
| **Objectifs** | Publier régulièrement, fidéliser une audience |

**User Stories :**
- *En tant que créateur de contenu, je veux rédiger un article avec des blocs visuels afin de produire un contenu riche sans coder.*
- *En tant que créateur de contenu, je veux planifier la publication d'un article afin de contrôler mon calendrier éditorial.*
- *En tant que créateur de contenu, je veux réviser les versions précédentes d'un article afin de revenir en arrière si nécessaire.*

### 3.2 Persona 2 — L'Administrateur de site

| Attribut | Description |
|----------|-------------|
| **Profil** | Propriétaire de site, responsable éditorial |
| **Compétences techniques** | Modérées |
| **Besoins** | Gérer les utilisateurs, configurer le site, modérer les commentaires |
| **Frustrations** | Mises à jour qui cassent le site, spam, sécurité |
| **Objectifs** | Maintenir un site fonctionnel, sécurisé et à jour |

**User Stories :**
- *En tant qu'administrateur, je veux gérer les rôles et permissions des utilisateurs afin de contrôler l'accès au contenu.*
- *En tant qu'administrateur, je veux recevoir des notifications de mise à jour afin de maintenir le site sécurisé.*
- *En tant qu'administrateur, je veux vérifier la santé de mon site afin de diagnostiquer les problèmes de performance.*

### 3.3 Persona 3 — Le Développeur de thèmes/plugins

| Attribut | Description |
|----------|-------------|
| **Profil** | Développeur PHP/JS, freelance ou agence |
| **Compétences techniques** | Élevées |
| **Besoins** | APIs stables, hooks extensibles, documentation claire |
| **Frustrations** | God classes, couplage fort, manque de typage |
| **Objectifs** | Créer des extensions robustes, maintenables et performantes |

**User Stories :**
- *En tant que développeur, je veux enregistrer un custom post type avec l'API REST exposée afin de créer des types de contenu spécifiques.*
- *En tant que développeur, je veux utiliser les hooks (actions/filtres) afin de modifier le comportement du cœur sans le modifier.*
- *En tant que développeur, je veux créer des blocs Gutenberg personnalisés afin d'offrir de nouveaux outils d'édition.*

### 3.4 Persona 4 — L'Intégrateur / Agence

| Attribut | Description |
|----------|-------------|
| **Profil** | Agence web, freelance, intégrateur |
| **Compétences techniques** | Modérées à élevées |
| **Besoins** | Personnalisation visuelle, multisite, performance |
| **Frustrations** | Limites du customizer, fragmentation des plugins |
| **Objectifs** | Livrer des sites sur mesure rapidement avec un coût de maintenance faible |

**User Stories :**
- *En tant qu'intégrateur, je veux utiliser le Full Site Editing afin de créer des templates sans toucher au PHP.*
- *En tant qu'intégrateur, je veux déployer un réseau multisite afin de gérer plusieurs sites clients depuis une seule installation.*

### 3.5 Persona 5 — Le Visiteur / Lecteur

| Attribut | Description |
|----------|-------------|
| **Profil** | Utilisateur final naviguant sur un site WordPress |
| **Compétences techniques** | Aucune requise |
| **Besoins** | Contenu accessible, rapide, navigable, responsive |
| **Frustrations** | Sites lents, non accessibles, envahis de publicités |
| **Objectifs** | Trouver l'information recherchée rapidement |

---

## 4. Objectifs produit

### 4.1 Objectifs fonctionnels

| ID | Objectif | Priorité |
|----|----------|----------|
| OF-01 | Gestion complète du cycle de vie du contenu (CRUD, révisions, corbeille, planification) | P0 |
| OF-02 | Édition visuelle par blocs (Gutenberg) avec prévisualisation temps réel | P0 |
| OF-03 | Système de thèmes avec Full Site Editing | P0 |
| OF-04 | Système de plugins extensible par hooks | P0 |
| OF-05 | Gestion des utilisateurs avec rôles et capacités granulaires | P0 |
| OF-06 | API REST complète pour toutes les ressources | P0 |
| OF-07 | Gestion des médias (images, vidéo, audio, documents) | P0 |
| OF-08 | Système de commentaires avec modération | P1 |
| OF-09 | Support multisite (réseau de sites) | P1 |
| OF-10 | Outils de conformité RGPD (export/effacement données personnelles) | P1 |
| OF-11 | Système de mises à jour automatiques | P1 |
| OF-12 | Import/Export de contenu | P2 |
| OF-13 | XML-RPC pour la rétrocompatibilité | P2 |
| OF-14 | Interactivity API pour les interactions frontend | P1 |
| OF-15 | Abilities API pour les capacités de contenu | P2 |
| OF-16 | Client AI intégré | P2 |

### 4.2 Objectifs non-fonctionnels

| ID | Objectif | Cible |
|----|----------|-------|
| ONF-01 | Temps d'installation | < 5 minutes |
| ONF-02 | Time To First Byte (TTFB) | < 200ms (page en cache) |
| ONF-03 | Support navigateurs | 2 dernières versions majeures |
| ONF-04 | Accessibilité | WCAG 2.1 niveau AA |
| ONF-05 | Langues supportées | 200+ via système de traduction |
| ONF-06 | Disponibilité | 99.9% (dépend de l'hébergeur) |
| ONF-07 | Compatibilité PHP | 7.4+ (recommandé 8.3+) |
| ONF-08 | Compatibilité MySQL | 5.5.5+ (recommandé 8.0+ / MariaDB 10.6+) |

---

## 5. Exigences fonctionnelles

### 5.1 Gestion de contenu (CMS Core)

#### 5.1.1 Types de contenu

WordPress implémente un système de **custom post types** permettant la création de types de contenu arbitraires. Les types natifs sont :

| Type | Slug | Hiérarchique | Public | Description |
|------|------|:------------:|:------:|-------------|
| Article | `post` | Non | Oui | Contenu chronologique (blog) |
| Page | `page` | Oui | Oui | Contenu statique hiérarchique |
| Pièce jointe | `attachment` | Non | Oui | Fichiers média liés à un contenu |
| Révision | `revision` | Non | Non | Versions historiques d'un contenu |
| Menu de navigation | `nav_menu_item` | Non | Non | Éléments de menu |
| Bloc réutilisable | `wp_block` | Non | Non | Patterns de blocs synchronisés |
| Template | `wp_template` | Non | Non | Templates Full Site Editing |
| Partie de template | `wp_template_part` | Non | Non | Composants de template (header, footer) |
| Style global | `wp_global_styles` | Non | Non | Configuration JSON des styles du thème |
| Navigation | `wp_navigation` | Non | Non | Menus de navigation basés sur les blocs |
| Famille de polices | `wp_font_family` | Non | Non | Gestion des polices typographiques |
| Face de police | `wp_font_face` | Non | Non | Variantes de polices |

#### 5.1.2 Statuts de contenu

| Statut | Description | Visible publiquement |
|--------|-------------|:--------------------:|
| `publish` | Publié et visible | Oui |
| `future` | Publication planifiée | Non (jusqu'à la date) |
| `draft` | Brouillon en cours | Non |
| `pending` | En attente de relecture | Non |
| `private` | Privé (visible uniquement par les auteurs et admins) | Restreint |
| `trash` | Dans la corbeille (suppression réversible) | Non |
| `auto-draft` | Brouillon automatique (post-new.php) | Non |
| `inherit` | Hérite du parent (révisions, pièces jointes) | Dépend du parent |

#### 5.1.3 Taxonomies

| Taxonomie | Slug | Hiérarchique | Associée à |
|-----------|------|:------------:|------------|
| Catégorie | `category` | Oui | `post` |
| Étiquette | `post_tag` | Non | `post` |
| Menu de navigation | `nav_menu` | Non | `nav_menu_item` |
| Format d'article | `post_format` | Non | `post` |
| Catégorie de lien | `link_category` | Non | `link` |
| Pattern de bloc | `wp_pattern_category` | Non | `wp_block` |

Les développeurs peuvent enregistrer des taxonomies personnalisées via `register_taxonomy()`.

#### 5.1.4 Métadonnées

Chaque type de contenu possède une table de métadonnées associée (Entity-Attribute-Value) :

- **Post meta** (`wp_postmeta`) — Champs personnalisés des contenus
- **User meta** (`wp_usermeta`) — Préférences et données utilisateur
- **Term meta** (`wp_termmeta`) — Métadonnées des termes de taxonomie
- **Comment meta** (`wp_commentmeta`) — Métadonnées des commentaires

> Voir [ARCHITECTURE.md §4](ARCHITECTURE.md#4-modèle-de-données) pour le schéma de base de données complet et le pattern EAV.

#### 5.1.5 Boucle de requête (The Loop)

Le cœur du rendu WordPress repose sur la classe `WP_Query` (5 113 lignes, 68 méthodes) :

1. **Parsing d'URL** — `WP::parse_request()` convertit l'URL en variables de requête
2. **Construction SQL** — `WP_Query::get_posts()` génère et exécute la requête SQL
3. **The Loop** — Itération sur les résultats avec `have_posts()` / `the_post()`
4. **Template loading** — `template-loader.php` charge le template approprié selon la hiérarchie

**Variables de requête publiques** : `m`, `p`, `posts`, `w`, `cat`, `s`, `page`, `paged`, `author`, `name`, `pagename`, `year`, `monthnum`, `day`, `tag`, `feed`, etc.

### 5.2 Éditeur de blocs (Gutenberg)

#### 5.2.1 Architecture des blocs

L'éditeur est composé de **90+ blocs natifs** organisés en catégories :

**Blocs de texte :**
`paragraph`, `heading`, `list`, `list-item`, `quote`, `pullquote`, `verse`, `preformatted`, `code`, `details`

**Blocs de média :**
`image`, `gallery`, `video`, `audio`, `file`, `cover`, `media-text`

**Blocs de design/layout :**
`group`, `columns`, `column`, `row`, `stack`, `spacer`, `separator`, `buttons`, `button`

**Blocs de widgets :**
`archives`, `calendar`, `categories`, `latest-comments`, `latest-posts`, `rss`, `search`, `tag-cloud`, `social-links`, `social-link`

**Blocs de thème :**
`site-title`, `site-tagline`, `site-logo`, `navigation`, `navigation-link`, `navigation-submenu`, `page-list`, `loginout`

**Blocs de boucle de requête :**
`query`, `post-template`, `query-pagination`, `query-pagination-next`, `query-pagination-previous`, `query-pagination-numbers`, `query-no-results`, `query-title`, `query-total`

**Blocs de contenu de post :**
`post-title`, `post-content`, `post-excerpt`, `post-featured-image`, `post-author`, `post-author-name`, `post-author-biography`, `post-date`, `post-terms`, `post-navigation-link`, `read-more`

**Blocs de commentaires :**
`comments`, `comment-template`, `comment-author-name`, `comment-content`, `comment-date`, `comment-edit-link`, `comment-reply-link`, `comments-pagination`

**Blocs de template :**
`template-part`, `pattern`

**Blocs spéciaux :**
`shortcode`, `custom-html`, `embed`, `legacy-widget`, `widget-group`, `block` (réutilisable), `missing`, `freeform` (classic editor)

#### 5.2.2 Cycle de vie d'un bloc

1. **Enregistrement** — `register_block_type()` via fichier `block.json`
2. **Parsing** — `WP_Block_Parser` analyse le HTML sérialisé avec des délimiteurs `<!-- wp:blockname -->...<!-- /wp:blockname -->`
3. **Rendu serveur** — Callback `render_callback` pour les blocs dynamiques
4. **Rendu client** — React pour l'éditeur, HTML statique pour le frontend
5. **Validation** — Schéma d'attributs JSON validé à la sauvegarde

#### 5.2.3 Block Supports

Système déclaratif de fonctionnalités par bloc (`block-supports/`) :

- `align` — Alignement (wide, full, left, right, center)
- `anchor` — ID HTML personnalisé
- `background` — Image/couleur d'arrière-plan
- `border` — Bordures (radius, color, width, style)
- `color` — Couleurs (text, background, gradient, link)
- `custom-class-name` — Classes CSS additionnelles
- `dimensions` — Dimensions (minHeight, aspectRatio)
- `duotone` — Filtre bicolore sur les images
- `layout` — Type de mise en page (flex, grid, flow, constrained)
- `position` — Position CSS (sticky, fixed)
- `shadow` — Ombres portées
- `spacing` — Marges et paddings
- `typography` — Typographie (fontSize, fontFamily, fontStyle, fontWeight, lineHeight, letterSpacing, textDecoration, textTransform, writingMode)

### 5.3 Full Site Editing (FSE)

#### 5.3.1 Composants

| Composant | Description |
|-----------|-------------|
| **Templates** | Mise en page complète d'une page (post type `wp_template`) |
| **Template Parts** | Composants réutilisables : header, footer, sidebar (`wp_template_part`) |
| **Global Styles** | Configuration JSON centralisée des couleurs, typographies, spacings (`wp_global_styles`) |
| **Navigation Blocks** | Menus de navigation basés sur les blocs |
| **Patterns** | Compositions de blocs réutilisables (synchronisés ou non) |

#### 5.3.2 Theme.json

Fichier de configuration central pour les thèmes bloc :

```
{
  "version": 3,
  "settings": {
    "color": { "palette": [...], "gradients": [...] },
    "typography": { "fontFamilies": [...], "fontSizes": [...] },
    "spacing": { "units": ["px", "em", "rem", "%", "vw", "vh"] },
    "layout": { "contentSize": "800px", "wideSize": "1200px" }
  },
  "styles": {
    "color": { "background": "...", "text": "..." },
    "typography": { "fontFamily": "...", "fontSize": "..." },
    "elements": { "link": { ... }, "heading": { ... } },
    "blocks": { "core/paragraph": { ... } }
  },
  "templateParts": [...],
  "customTemplates": [...]
}
```

Classes de traitement : `WP_Theme_JSON`, `WP_Theme_JSON_Resolver`, `WP_Theme_JSON_Schema`.

### 5.4 Système de thèmes

#### 5.4.1 Hiérarchie de templates

La résolution de template suit une cascade prédéfinie (du plus spécifique au plus générique) :

```
Page d'accueil      : front-page.php → home.php → index.php
Article unique      : single-{post_type}-{slug}.php → single-{post_type}.php → single.php → singular.php → index.php
Page               : page-{slug}.php → page-{id}.php → page.php → singular.php → index.php
Archive catégorie  : category-{slug}.php → category-{id}.php → category.php → archive.php → index.php
Archive auteur     : author-{nicename}.php → author-{id}.php → author.php → archive.php → index.php
Recherche          : search.php → index.php
Erreur 404         : 404.php → index.php
```

#### 5.4.2 Fonctionnalités de thème

Déclarées via `add_theme_support()` :

- `title-tag` — Gestion automatique du `<title>`
- `post-thumbnails` — Images à la une
- `custom-header` — Image d'en-tête personnalisée
- `custom-background` — Arrière-plan personnalisable
- `custom-logo` — Logo du site
- `automatic-feed-links` — Liens RSS automatiques
- `html5` — Balisage HTML5 (search-form, comment-form, comment-list, gallery, caption, script, style)
- `editor-styles` — Styles dans l'éditeur
- `responsive-embeds` — Embeds responsive
- `wp-block-styles` — Styles de blocs par défaut
- `align-wide` — Alignements larges/pleine largeur
- `editor-color-palette` — Palette de couleurs personnalisée
- `editor-font-sizes` — Tailles de police personnalisées
- `block-templates` — Templates de blocs

### 5.5 Administration

#### 5.5.1 Pages d'administration

| Section | Pages | Fonctionnalité |
|---------|-------|----------------|
| **Tableau de bord** | `index.php` | Vue d'ensemble, widgets d'activité, santé du site |
| **Articles** | `edit.php`, `post-new.php`, `post.php`, `edit-tags.php` | CRUD articles, catégories, étiquettes |
| **Médias** | `upload.php`, `media-new.php`, `media.php` | Bibliothèque, upload, édition d'images |
| **Pages** | `edit.php?post_type=page`, `post-new.php?post_type=page` | CRUD pages statiques |
| **Commentaires** | `edit-comments.php`, `comment.php` | Modération, approbation, spam |
| **Apparence** | `themes.php`, `customize.php`, `widgets.php`, `nav-menus.php` | Thèmes, customizer, widgets, menus |
| **Extensions** | `plugins.php`, `plugin-install.php`, `plugin-editor.php` | Installation, activation, édition |
| **Utilisateurs** | `users.php`, `user-new.php`, `user-edit.php`, `profile.php` | Gestion des comptes et rôles |
| **Outils** | `tools.php`, `import.php`, `export.php`, `site-health.php` | Import/export, santé, RGPD |
| **Réglages** | `options-general.php`, `options-writing.php`, `options-reading.php`, `options-discussion.php`, `options-media.php`, `options-permalink.php`, `options-privacy.php` | Configuration complète du site |
| **Mises à jour** | `update-core.php` | Mise à jour cœur, plugins, thèmes, traductions |

#### 5.5.2 Settings API

Système déclaratif de gestion des réglages :

1. `register_setting($group, $name, $args)` — Enregistre un réglage
2. `add_settings_section($id, $title, $callback, $page)` — Crée une section
3. `add_settings_field($id, $title, $callback, $page, $section)` — Ajoute un champ
4. `do_settings_sections($page)` — Rend toutes les sections d'une page
5. `settings_fields($group)` — Rend les champs nonce et action

#### 5.5.3 List Tables (WP_List_Table)

Framework de tableaux de données pour l'admin avec :
- Pagination configurable
- Tri par colonnes
- Filtrage et recherche
- Actions groupées (bulk actions)
- Actions en ligne (quick edit, inline edit)
- Rendu AJAX pour les mises à jour partielles

Classes spécialisées : `WP_Posts_List_Table`, `WP_Comments_List_Table`, `WP_Users_List_Table`, `WP_Plugins_List_Table`, `WP_Themes_List_Table`, `WP_Media_List_Table`, `WP_Terms_List_Table`.

### 5.6 Gestion des médias

#### 5.6.1 Types supportés

| Catégorie | Extensions |
|-----------|-----------|
| **Images** | JPEG, PNG, GIF, WebP, AVIF, ICO, BMP, HEIC |
| **Vidéos** | MP4, MOV, WMV, AVI, FLV, OGG, WebM, 3GP |
| **Audio** | MP3, OGG, WAV, FLAC, M4A, WMA |
| **Documents** | PDF, DOC(X), PPT(X), XLS(X), ODT, ODS, ODP |
| **Archives** | ZIP, GZ, RAR, TAR |

#### 5.6.2 Traitement d'images

- **Sous-tailles automatiques** : thumbnail (150x150), medium (300x300), medium_large (768x0), large (1024x1024)
- **Éditeur d'images** : recadrage, rotation, retournement, redimensionnement
- **Moteurs** : GD Library (défaut), Imagick (si disponible)
- **Métadonnées** : EXIF/IPTC extraits automatiquement
- **Optimisation** : Compression JPEG configurable, conversion WebP/AVIF
- **Traitement côté client** : Support du traitement d'images dans le navigateur

#### 5.6.3 Stockage

Les fichiers sont organisés dans `wp-content/uploads/` par année/mois :
```
wp-content/uploads/
├── 2026/
│   ├── 01/
│   │   ├── photo.jpg
│   │   ├── photo-150x150.jpg
│   │   ├── photo-300x200.jpg
│   │   └── photo-1024x683.jpg
│   └── 02/
└── ...
```

### 5.7 Gestion des utilisateurs

#### 5.7.1 Rôles par défaut

| Rôle | Capacités clés |
|------|----------------|
| **Super Admin** | Toutes (multisite uniquement) — gestion réseau |
| **Administrateur** | Toutes capacités du site — gestion complète |
| **Éditeur** | Publier/modifier tous les contenus, modérer commentaires |
| **Auteur** | Publier/modifier ses propres contenus, uploader des médias |
| **Contributeur** | Écrire des brouillons, soumettre à relecture |
| **Abonné** | Lire le contenu, gérer son profil |

#### 5.7.2 Système de capacités

Architecture RBAC (Role-Based Access Control) avec :

- **Capacités primitives** : Actions directement vérifiables (`edit_posts`, `manage_options`, `install_plugins`)
- **Meta-capacités** : Mappées dynamiquement via `map_meta_cap()` (`edit_post`, `delete_page`, `read_post`)
- **Vérification** : `current_user_can($capability, ...$args)`, `user_can($user, $capability)`
- **Extensible** : `add_cap()`, `remove_cap()` sur les rôles, capacités custom pour les CPT

#### 5.7.3 Authentification

| Méthode | Contexte | Mécanisme |
|---------|----------|-----------|
| **Cookie** | Interface web | Hash salé avec clés secrètes, session PHP |
| **Application Passwords** | REST API / XML-RPC | Mot de passe spécifique par application, Basic Auth |
| **Nonce** | CSRF protection | Jeton lié à l'action, l'utilisateur et le temps (12h) |
| **OAuth** | Applications tierces | `authorize-application.php` |

> Voir [ARCHITECTURE.md §6](ARCHITECTURE.md#6-sécurité--implémentation) pour les détails d'implémentation de la sécurité.

### 5.8 Commentaires

- Commentaires natifs avec threading (réponses imbriquées)
- Modération : approbation manuelle, liste noire, liste de modération
- Anti-spam : hooks pour intégration (Akismet, etc.)
- Statuts : approuvé, en attente, spam, corbeille
- Pingbacks et trackbacks (legacy)
- Commentaires sur tous les types de contenu supportant `'supports' => ['comments']`

### 5.9 Multisite

Le mode multisite transforme une installation WordPress unique en réseau de sites :

- **Activation** : Constantes `WP_ALLOW_MULTISITE` / `MULTISITE`
- **Mapping** : Par sous-domaine (`site1.example.com`) ou sous-répertoire (`example.com/site1`)
- **Administration réseau** : Interface dédiée pour le super admin
- **Partage** : Tables `wp_users` et `wp_usermeta` globales, tables de contenu par site (`wp_2_posts`, `wp_3_posts`, etc.)
- **Switch de contexte** : `switch_to_blog($blog_id)` / `restore_current_blog()`
- **Gestion** : Activation/désactivation de plugins/thèmes au niveau réseau

### 5.10 Outils de conformité RGPD

| Fonctionnalité | Description |
|----------------|-------------|
| **Export de données personnelles** | Génère un fichier ZIP avec toutes les données d'un utilisateur |
| **Effacement de données personnelles** | Anonymise ou supprime les données sur demande |
| **Politique de confidentialité** | Assistant de création avec suggestions par plugin |
| **Consentement** | Hooks pour les plugins de gestion du consentement |
| **Requêtes utilisateurs** | Custom post type `user_request` pour le suivi |
| **Confirmation par email** | Double opt-in pour les demandes d'export/effacement |

### 5.11 Système de mises à jour

- **Détection** : Vérification périodique auprès de `api.wordpress.org`
- **Types** : Cœur (majeur/mineur), plugins, thèmes, traductions
- **Auto-updates** : Activables par type (mineures de sécurité activées par défaut)
- **Processus** : Téléchargement → Vérification → Backup → Extraction → Activation → Nettoyage
- **Rollback** : En cas d'échec, restauration automatique
- **Filesystem** : Support FTP, FTPS, SSH2, accès direct

### 5.12 Santé du site (Site Health)

Outil de diagnostic intégré avec deux onglets :

**Status** — Tests actifs :
- Version WordPress / PHP / MySQL
- Extensions HTTPS / SSL
- Tâches planifiées (cron)
- Mises à jour automatiques
- Communication avec WordPress.org
- Requêtes REST API et loopback
- Dépendances des plugins
- Autorisations fichiers

**Info** — Données de débogage exportables :
- Configuration WordPress, serveur, base de données
- Constantes actives, chemins de fichiers
- Thème et plugins actifs, versions
- Bibliothèques média disponibles

---

## 6. Exigences non-fonctionnelles

### 6.1 Sécurité

Le produit doit garantir la protection contre les vecteurs d'attaque suivants :

| Vecteur | Exigence |
|---------|----------|
| **SQL Injection** | Toute requête utilisant des données utilisateur doit être préparée |
| **XSS** | Toute sortie HTML doit être échappée selon son contexte |
| **CSRF** | Toute action modifiant des données doit être protégée par un nonce |
| **Upload malveillant** | Tout fichier uploadé doit être validé par type MIME |
| **SSRF** | Toute requête HTTP sortante vers une URL utilisateur doit être validée |
| **Brute force** | L'authentification doit supporter le rate limiting (via hooks) |

> Voir [ARCHITECTURE.md §6](ARCHITECTURE.md#6-sécurité--implémentation) pour les fonctions et mécanismes d'implémentation.

### 6.2 Performance

| Métrique | Cible | Condition |
|----------|-------|-----------|
| TTFB | < 200ms | Page en cache, hébergement performant |
| LCP (Largest Contentful Paint) | < 2.5s | Image optimisée, CDN |
| Requêtes SQL par page | < 30 | Configuration par défaut, peu de plugins |
| Mémoire PHP | < 40MB | Configuration par défaut |
| Temps de chargement admin | < 1s | Réseau local ou hébergement rapide |

> Voir [ARCHITECTURE.md §7](ARCHITECTURE.md#7-performance--stratégies) pour les stratégies de cache et d'optimisation.

### 6.3 Internationalisation et accessibilité

| Exigence | Cible |
|----------|-------|
| Langues supportées | 200+ via système de traduction PO/MO/PHP |
| Direction de texte | Support natif LTR et RTL |
| Accessibilité | WCAG 2.1 niveau AA pour toute l'interface d'administration |
| Navigation clavier | 100% de l'admin navigable au clavier |
| Screen readers | Compatibilité totale (ARIA roles, labels, live regions) |

> Voir [ARCHITECTURE.md §8](ARCHITECTURE.md#8-internationalisation--infrastructure) pour l'infrastructure technique i18n et l'implémentation a11y.

---

## 7. Contraintes et dépendances

### 7.1 Contraintes de rétrocompatibilité

WordPress maintient une politique de rétrocompatibilité extrêmement stricte :

- **Aucune fonction publique ne peut être supprimée** — uniquement dépréciée (avec `_deprecated_function()`)
- **Les hooks existants ne peuvent pas changer de signature** — seuls de nouveaux peuvent être ajoutés
- **Les tables de base de données ne peuvent pas perdre de colonnes** — uniquement en gagner
- **Le format de sérialisation des blocs doit rester parsable** par toutes les versions >= 5.0
- **Les URLs de l'API REST ne peuvent pas changer** une fois publiées
- **Les fonctions pluggables doivent conserver leur interface**

### 7.2 Dépendances externes

- **api.wordpress.org** — Mise à jour, répertoire de plugins/thèmes, vérification de version
- **downloads.wordpress.org** — Téléchargement des mises à jour
- **s.w.org** — Assets statiques (patterns, screenshots)
- **Gravatar (gravatar.com)** — Avatars utilisateurs

> Voir [ARCHITECTURE.md §11](ARCHITECTURE.md#11-dépendances-tierces-intégrées) pour les bibliothèques tierces intégrées au cœur et [§12](ARCHITECTURE.md#12-risques-architecturaux) pour les risques architecturaux identifiés.

---

## 8. Métriques de succès

### 8.1 KPIs techniques

| Métrique | Objectif | Mesure |
|----------|----------|--------|
| Score Lighthouse Performance | > 90 | Audit automatisé thème par défaut |
| Couverture de tests | > 80% | PHPUnit + E2E |
| Conformité WCAG 2.1 AA | 100% | Audit axe-core sur l'admin |
| Vulnérabilités critiques ouvertes | 0 | Programme de bug bounty HackerOne |
| Temps de release patch sécurité | < 72h | Depuis la découverte au déploiement |
| Compatibilité plugins (top 200) | > 95% | Tests de non-régression |

### 8.2 KPIs produit

| Métrique | Indicateur |
|----------|-----------|
| Adoption | Part de marché web (actuellement ~43%) |
| Écosystème | Nombre de plugins/thèmes actifs sur wordpress.org |
| Communauté | Contributeurs actifs par release |
| Satisfaction | Note moyenne des reviews de la version |
| Performance d'installation | Temps médian d'installation < 5 minutes |
| Utilisation du block editor | % de posts créés avec Gutenberg vs Classic |

### 8.3 KPIs de qualité

| Métrique | Cible |
|----------|-------|
| Tickets Trac ouverts (critiques) | < 50 |
| Temps de résolution bugs critiques | < 7 jours |
| Régressions par release | < 5 |
| Taux de rollback après mise à jour auto | < 0.1% |

---

## Annexes

### A. Glossaire

| Terme | Définition |
|-------|-----------|
| **Hook** | Point d'ancrage permettant d'exécuter du code (action) ou de transformer une valeur (filtre) |
| **Action** | Hook déclenché à un moment précis du cycle d'exécution |
| **Filter** | Hook transformant une valeur avant son utilisation |
| **CPT** | Custom Post Type — type de contenu personnalisé |
| **FSE** | Full Site Editing — édition complète du site par blocs |
| **Gutenberg** | Nom de code de l'éditeur de blocs |
| **Nonce** | Number used once — jeton anti-CSRF |
| **Transient** | Données temporaires avec expiration |
| **Drop-in** | Fichier PHP qui remplace un composant core s'il existe dans wp-content/ |
| **Pluggable** | Fonction core remplaçable par un plugin (définie dans pluggable.php) |
| **Slug** | Identifiant URL-safe d'un contenu |
| **Meta** | Donnée clé-valeur associée à un objet (post, user, term, comment) |
| **Capability** | Permission atomique vérifiable sur un utilisateur |
| **Role** | Collection nommée de capabilities |
| **Shortcode** | Macro entre crochets `[name]` remplacée par du contenu dynamique |
| **Widget** | Composant affichable dans une zone de sidebar |
| **REST** | Representational State Transfer — architecture d'API HTTP |
| **WXR** | WordPress eXtended RSS — format d'export XML |
| **EAV** | Entity-Attribute-Value — pattern de stockage flexible |
| **RBAC** | Role-Based Access Control — contrôle d'accès par rôles |

### B. Références

- [WordPress Developer Resources](https://developer.wordpress.org/)
- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)
- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress Core Trac](https://core.trac.wordpress.org/)
- [Theme.json Reference](https://developer.wordpress.org/themes/global-settings-and-styles/)

### C. Documents compagnons

- **[ARCHITECTURE.md](ARCHITECTURE.md)** — Architecture technique, modèle de données, spécifications API, implémentation sécurité/performance, infrastructure de déploiement

---

*Ce PRD est un document vivant qui reflète l'état de WordPress 7.0-beta1. Il doit être mis à jour à chaque release majeure.*
