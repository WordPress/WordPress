# Audit de Securite - `wp-includes/pluggable.php`

**Date** : 2026-02-25
**Fichier audite** : `wp-includes/pluggable.php` (~3 450 lignes)
**Version WordPress** : trunk (commit `95b934782d`)
**Auditeur** : Claude Code (Opus 4.6)

---

## 1. Contexte

Le fichier `pluggable.php` contient les fonctions de securite les plus critiques de WordPress. Elles couvrent :

- **Authentification** : `wp_authenticate`, `wp_validate_auth_cookie`, `wp_set_auth_cookie`
- **Hashing de mots de passe** : `wp_hash_password`, `wp_check_password`, `wp_set_password`
- **Gestion de sessions** : `wp_login`, `wp_logout`, `wp_clear_auth_cookie`
- **Protection CSRF** : `wp_create_nonce`, `wp_verify_nonce`, `check_admin_referer`, `check_ajax_referer`
- **Redirections** : `wp_redirect`, `wp_safe_redirect`, `wp_sanitize_redirect`, `wp_validate_redirect`
- **Envoi d'emails** : `wp_mail`, `wp_notify_postauthor`, `wp_notify_moderator`
- **Generation aleatoire** : `wp_rand`, `wp_generate_password`, `wp_salt`
- **Divers** : `get_avatar`, `wp_text_diff`, `cache_users`

Toutes ces fonctions sont **pluggable** : elles sont enveloppees dans un `if ( ! function_exists( '...' ) )` et peuvent etre entierement remplacees par un plugin.

---

## 2. Synthese des constats

| Severite | Nombre | Resume |
|----------|--------|--------|
| Critique | 0 | Aucune vulnerabilite directement exploitable sans plugin malveillant |
| Elevee | 3 | XSS potentiel dans `get_avatar`, `wp_text_diff` ; acceptation MD5 |
| Moyenne | 4 | Cookies sans `SameSite`, Host header injection, filtres sensibles, architecture pluggable |
| Faible | 4 | Fallback PRNG, HMAC-MD5 par defaut, trimming password, fragment hash court |
| Positive | 13 | Bonnes pratiques identifiees |

---

## 3. Constats detailles

### 3.1 Severite ELEVEE

#### SEC-01 : XSS dans `get_avatar()` via `extra_attr`

- **Lignes** : 3188, 3319-3327
- **Fonction** : `get_avatar()`
- **CVSS estimee** : 6.1 (Medium-High)

**Description** :
Le parametre `extra_attr` est documente comme "Is not sanitized" et est injecte directement dans le tag HTML `<img>` :

```php
// Ligne 3188 (documentation) :
// @type string $extra_attr HTML attributes to insert in the IMG element. Is not sanitized.

// Ligne 3319-3327 :
$avatar = sprintf(
    "<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
    esc_attr( $args['alt'] ),
    esc_url( $url ),
    esc_url( $url2x ) . ' 2x',
    esc_attr( implode( ' ', $class ) ),
    (int) $args['height'],
    (int) $args['width'],
    $extra_attr   // <-- AUCUN ECHAPPEMENT
);
```

Tous les autres parametres sont correctement echappes (`esc_attr`, `esc_url`, cast `(int)`), mais `$extra_attr` est insere tel quel. Si un plugin ou un theme passe des donnees utilisateur non filtrees dans ce parametre, un XSS stocke est possible.

**Recommandation** : Appliquer `esc_attr()` sur chaque attribut ou au minimum documenter plus fortement le risque et ajouter une sanitization de base (strip des `<script>`, `onerror`, etc.).

---

#### SEC-02 : XSS dans `wp_text_diff()`

- **Lignes** : 3419, 3431, 3433
- **Fonction** : `wp_text_diff()`
- **CVSS estimee** : 5.4 (Medium)

**Description** :
Les parametres `title`, `title_left` et `title_right` sont injectes directement dans le HTML sans echappement :

```php
// Ligne 3419 :
$r .= "<caption class='diff-title'>$args[title]</caption>\n";

// Ligne 3431 :
$r .= "\t<$th_or_td_left>$args[title_left]</$th_or_td_left>\n";

// Ligne 3433 :
$r .= "\t<$th_or_td_right>$args[title_right]</$th_or_td_right>\n";
```

Si un appelant transmet des donnees utilisateur (ex : titre de revision, nom d'auteur) sans les echapper au prealable, un XSS est possible.

**Recommandation** : Echapper avec `esc_html()` les trois valeurs avant insertion dans le HTML.

---

#### SEC-03 : Acceptation de mots de passe haches en MD5 brut

- **Lignes** : 2847-2849
- **Fonction** : `wp_check_password()`
- **CVSS estimee** : 5.9 (Medium)

**Description** :
Les hash de mots de passe de 32 caracteres ou moins sont consideres comme du MD5 brut et verifies via :

```php
if ( strlen( $hash ) <= 32 ) {
    $check = hash_equals( $hash, md5( $password ) );
}
```

MD5 sans sel est vulnerable aux :
- Rainbow tables
- Attaques par force brute (vitesse ~10 milliards de hash/sec sur GPU moderne)
- Collisions connues

Si des hash MD5 subsistent en base de donnees (installations tres anciennes jamais mises a jour, ou migrations incompletes), ces comptes sont exposes.

**Facteurs attenuants** :
- `wp_password_needs_rehash()` (L.2886) detecte et planifie le rehash
- Les installations modernes utilisent bcrypt par defaut depuis WP 6.8

**Recommandation** :
- Forcer le rehash au prochain login via un hook sur `authenticate`
- Envisager un script de migration pour identifier et alerter sur les hash MD5 restants
- A terme, supprimer le support MD5

---

### 3.2 Severite MOYENNE

#### SEC-04 : Cookies d'authentification sans attribut `SameSite`

- **Lignes** : 1193-1197
- **Fonction** : `wp_set_auth_cookie()`

**Description** :
Tous les appels `setcookie()` utilisent l'ancienne signature a 7 parametres :

```php
setcookie( $auth_cookie_name, $auth_cookie, $expire, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN, $secure, true );
setcookie( $auth_cookie_name, $auth_cookie, $expire, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true );
setcookie( LOGGED_IN_COOKIE, $logged_in_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure_logged_in_cookie, true );
```

L'attribut `SameSite` n'est pas defini explicitement. Les navigateurs modernes appliquent `Lax` par defaut, mais :
- Les anciens navigateurs n'appliquent aucune restriction
- `SameSite=Lax` ne protege pas les requetes POST cross-origin (couvertes par les nonces WP)
- L'absence d'explicitation rend le comportement dependant du navigateur

**Point positif** : Le flag `HttpOnly` (dernier parametre `true`) est correctement defini.

**Recommandation** : Migrer vers la signature tableau de `setcookie()` (PHP 7.3+) avec `SameSite => 'Lax'` explicite :

```php
setcookie( $name, $value, [
    'expires'  => $expire,
    'path'     => COOKIEPATH,
    'domain'   => COOKIE_DOMAIN,
    'secure'   => $secure,
    'httponly'  => true,
    'samesite' => 'Lax',
]);
```

---

#### SEC-05 : Utilisation de `$_SERVER['HTTP_HOST']` dans les redirections

- **Lignes** : 1298, 1329, 1343
- **Fonction** : `auth_redirect()`

**Description** :
La valeur `$_SERVER['HTTP_HOST']` est controlee par le client (en-tete `Host` de la requete HTTP). Elle est utilisee pour construire des URL de redirection :

```php
// Ligne 1298 :
wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

// Ligne 1343 :
$redirect = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
```

**Facteurs attenuants** :
- `wp_redirect()` appelle `wp_sanitize_redirect()` qui filtre les caracteres dangereux
- La plupart des serveurs web (Apache, Nginx) valident l'en-tete `Host`

**Risque residuel** : Host header injection dans des configurations serveur permissives (virtual hosts mal configures), pouvant mener a du cache poisoning ou des liens de reset de mot de passe falsifies.

**Recommandation** : Utiliser `site_url()` ou `home_url()` au lieu de `$_SERVER['HTTP_HOST']`.

---

#### SEC-06 : Filtres exposant des donnees sensibles

- **Lignes** : 706, 2882, 2985, 988
- **Fonctions** : `wp_authenticate`, `wp_check_password`, `wp_generate_password`, `wp_generate_auth_cookie`

**Description** :
Plusieurs filtres transmettent des donnees sensibles en clair aux plugins abonnes :

| Filtre | Donnee exposee | Ligne |
|--------|---------------|-------|
| `authenticate` | Mot de passe en clair | 706 |
| `check_password` | Mot de passe en clair + hash | 2882 |
| `random_password` | Mot de passe genere | 2985 |
| `auth_cookie` | Cookie d'authentification complet | 988 |
| `salt` | Salt cryptographique | 2593, 2693 |

Un plugin malveillant ou compromis peut :
- Logger tous les mots de passe en clair
- Exfiltrer les cookies d'authentification
- Affaiblir la generation de mots de passe aleatoires

**Recommandation** :
- Documenter ces filtres comme "security-sensitive" dans la documentation officielle
- Auditer les plugins qui se hookent sur ces filtres
- Envisager a terme de reduire l'exposition (ex : ne pas passer le mot de passe en clair dans `check_password`)

---

#### SEC-07 : Architecture pluggable comme surface d'attaque

- **Lignes** : tout le fichier
- **Pattern** : `if ( ! function_exists( 'nom_fonction' ) ) :`

**Description** :
L'ensemble du fichier repose sur le pattern pluggable. Un seul plugin peut remplacer completement :

| Fonction remplacable | Risque si mal implementee |
|---------------------|--------------------------|
| `wp_hash_password` | Hash faible, mots de passe compromis |
| `wp_check_password` | Bypass d'authentification |
| `wp_validate_auth_cookie` | Bypass de session |
| `wp_verify_nonce` | Bypass CSRF |
| `wp_generate_password` | Mots de passe previsibles |
| `wp_redirect` | Open redirect |
| `wp_mail` | Interception d'emails, spam |

Il n'existe aucun mecanisme de verification de l'integrite des fonctions remplacees (pas de signature, pas de whitelist).

**Recommandation** :
- Maintenir un inventaire des fonctions pluggable remplacees (audit regulier)
- Envisager un mecanisme de logging quand une fonction pluggable est remplacee (ex : action `_doing_it_wrong` ou warning admin)

---

### 3.3 Severite FAIBLE

#### SEC-08 : Fallback PRNG faible dans `wp_rand()`

- **Lignes** : 3050-3063
- **Fonction** : `wp_rand()`

**Description** :
Si `random_int()` echoue, le fallback utilise des sources d'entropie faibles :

```php
$rnd_value  = md5( uniqid( microtime() . mt_rand(), true ) . $seed );
$rnd_value .= sha1( $rnd_value );
$rnd_value .= sha1( $rnd_value . $seed );
$seed       = md5( $seed . $rnd_value );
```

`mt_rand()` est un PRNG deterministe (Mersenne Twister), `microtime()` et `uniqid()` sont previsibles.

**Facteurs attenuants** : Ce chemin n'est jamais emprunte sur PHP 7+ avec une installation correcte.

**Recommandation** : Ajouter un logging d'erreur si le fallback est declenche, car cela indique un probleme d'environnement serveur.

---

#### SEC-09 : `wp_hash()` utilise HMAC-MD5 par defaut

- **Lignes** : 2715
- **Fonction** : `wp_hash()`

**Description** :

```php
function wp_hash( $data, $scheme = 'auth', $algo = 'md5' ) {
    $salt = wp_salt( $scheme );
    return hash_hmac( $algo, $data, $salt );
}
```

L'algorithme par defaut est MD5 dans un contexte HMAC. HMAC-MD5 ne souffre pas des memes faiblesses que MD5 brut (collisions, pre-image), mais SHA-256 offrirait une marge de securite superieure.

**Note** : Cette fonction est utilisee pour la derivation de cles de cookies et de nonces, pas pour le hashing de mots de passe.

**Recommandation** : Migrer vers SHA-256 par defaut a la prochaine version majeure (`$algo = 'sha256'`).

---

#### SEC-10 : Trimming silencieux des mots de passe

- **Lignes** : 690, 2756, 2809
- **Fonctions** : `wp_authenticate()`, `wp_hash_password()`

**Description** :

```php
// wp_authenticate(), L.690 :
$password = trim( $password );

// wp_hash_password(), L.2756 (via wp_hasher legacy) :
return $wp_hasher->HashPassword( trim( $password ) );

// wp_hash_password(), L.2809 (bcrypt path) :
$password_to_hash = base64_encode( hash_hmac( 'sha384', trim( $password ), 'wp-sha384', true ) );
```

Les espaces en debut et fin de mot de passe sont silencieusement supprimes. Un utilisateur choisissant `"  my password  "` verra son mot de passe reduit a `"my password"`.

**Recommandation** : Comportement intentionnel et de longue date dans WordPress. Le changer casserait la compatibilite. Documenter ce comportement pour les utilisateurs.

---

#### SEC-11 : Fragment de hash court pour la cle de cookie (4 caracteres)

- **Lignes** : 857, 860, 964, 967
- **Fonctions** : `wp_validate_auth_cookie()`, `wp_generate_auth_cookie()`

**Description** :

```php
$pass_frag = substr( $user->user_pass, 8, 4 );   // phpass/bcrypt
$pass_frag = substr( $user->user_pass, -4 );      // autres
```

Le fragment de mot de passe utilise dans la derivation de la cle de cookie ne fait que 4 caracteres. Ce fragment sert a invalider les cookies quand le mot de passe change.

**Facteurs attenuants** : Le fragment est combine avec le username, l'expiration et le token de session via HMAC, ce qui rend l'exploitation improbable.

---

## 4. Bonnes pratiques identifiees

| # | Aspect | Detail | Lignes |
|---|--------|--------|--------|
| 1 | **Hashing bcrypt** | Mots de passe haches avec bcrypt par defaut (depuis WP 6.8) | 2785 |
| 2 | **Pre-hash SHA-384** | Gestion des mots de passe > 72 bytes (limite bcrypt) via HMAC-SHA384 | 2809 |
| 3 | **Limite 4096 chars** | Prevention DoS sur bcrypt avec des mots de passe tres longs | 2759 |
| 4 | **`hash_equals()`** | Comparaison timing-safe sur tous les hash (cookies, nonces, passwords) | 867, 2495, 2501, 2849 |
| 5 | **Cookies HttpOnly** | Flag `true` sur tous les `setcookie()` | 1193-1197 |
| 6 | **HMAC-SHA256 cookies** | Cookie d'authentification signe avec `hash_hmac('sha256', ...)` | 865, 972 |
| 7 | **Session tokens** | Validation via `WP_Session_Tokens::verify()` | 889 |
| 8 | **Nonces lies session** | Nonces lies a user ID + session token + tick temporel | 2494, 2544 |
| 9 | **Sanitization redirects** | `wp_sanitize_redirect()` supprime CRLF, null bytes, caracteres non autorises | 1553-1576 |
| 10 | **Open redirect protection** | `wp_validate_redirect()` valide les hosts autorises, schema http/https uniquement | 1665-1732 |
| 11 | **`#[\SensitiveParameter]`** | Attribut PHP 8.2 sur les parametres mots de passe (exclus des stack traces) | 686, 2750, 2840, 3100 |
| 12 | **Message d'erreur generique** | "Invalid username, email address or incorrect password" (pas d'enumeration) | 713 |
| 13 | **Validation status redirect** | Les codes de redirection doivent etre 3xx, sinon `wp_die()` | 2512-2513 |

---

## 5. Matrice des fonctions et de leurs protections

| Fonction | Sanitization entree | Echappement sortie | CSRF | Auth | Crypto |
|----------|--------------------|--------------------|------|------|--------|
| `wp_mail()` | `is_email()`, PHPMailer validation | N/A (email) | N/A | N/A | N/A |
| `wp_authenticate()` | `sanitize_user()` | N/A | N/A | Fonction elle-meme | bcrypt |
| `wp_validate_auth_cookie()` | Parse cookie, verifie format | N/A | N/A | HMAC-SHA256 + session token | `hash_equals()` |
| `wp_set_auth_cookie()` | Cast int, `is_ssl()` | N/A | N/A | Session token | HMAC-SHA256 |
| `wp_redirect()` | `wp_sanitize_redirect()` | Strip CRLF, null bytes | N/A | N/A | N/A |
| `wp_safe_redirect()` | `wp_validate_redirect()` | Whitelist hosts | N/A | N/A | N/A |
| `check_admin_referer()` | N/A | N/A | `wp_verify_nonce()` | N/A | HMAC |
| `check_ajax_referer()` | N/A | N/A | `wp_verify_nonce()` | N/A | HMAC |
| `wp_hash_password()` | Limite 4096 chars | N/A | N/A | N/A | bcrypt + SHA-384 |
| `wp_check_password()` | Limite 4096 chars | N/A | N/A | N/A | bcrypt / phpass / MD5 |
| `get_avatar()` | `esc_attr()`, `esc_url()`, `(int)` | **`extra_attr` non echappe** | N/A | N/A | N/A |
| `wp_text_diff()` | N/A | **`title*` non echappes** | N/A | N/A | N/A |
| `cache_users()` | `_get_non_cached_ids()` (int cast) | N/A | N/A | N/A | N/A |

---

## 6. Recommandations priorisees

| Priorite | Action | Constats lies |
|----------|--------|--------------|
| **P1** | Echapper `extra_attr` dans `get_avatar()` ou ajouter une sanitization minimale | SEC-01 |
| **P1** | Echapper `title`, `title_left`, `title_right` dans `wp_text_diff()` avec `esc_html()` | SEC-02 |
| **P2** | Ajouter `SameSite=Lax` explicite sur les cookies d'authentification | SEC-04 |
| **P2** | Remplacer `$_SERVER['HTTP_HOST']` par `site_url()` / `home_url()` dans `auth_redirect()` | SEC-05 |
| **P2** | Identifier et forcer le rehash des mots de passe MD5 restants en base | SEC-03 |
| **P3** | Migrer `wp_hash()` vers SHA-256 par defaut | SEC-09 |
| **P3** | Logger un warning si le fallback PRNG est declenche dans `wp_rand()` | SEC-08 |
| **P3** | Documenter les filtres security-sensitive (`check_password`, `auth_cookie`, `salt`) | SEC-06 |
| **P3** | Ajouter un mecanisme de detection des fonctions pluggable remplacees | SEC-07 |

---

## 7. Perimetre et limites

- **Couvert** : Analyse statique du code de `pluggable.php` uniquement (fonctions, filtres, patterns cryptographiques, gestion des entrees/sorties)
- **Non couvert** :
  - Analyse dynamique (tests de penetration)
  - Interaction avec les autres fichiers core de WordPress
  - Plugins tiers remplacant les fonctions pluggable
  - Configuration serveur (TLS, headers HTTP, virtualhost)
  - Base de donnees (etat reel des hash stockes)
- **Methodologie** : Revue de code manuelle assistee, sans execution. Classification basee sur OWASP Top 10 2021 et bonnes pratiques NIST.
