<?php exit;
/**
 * Autoptimize's magic 404 handler.
 *
 * Configure your webserver to have requests for files that are no longer in
 * /wp-content/cache/autoptimize/ to redirect to this file. AO's .htaccess file
 * will have a "Errordocument:" directive to automatically do this.
 *
 * This file has simple logic to redirect to the "fallback" files that are
 * created automatically by AO to avoid visitors seeing broken pages or
 * Googlebot getting utterly confused.
 *
 * Error logging is off by default (don't want to flood your php errorlog, but
 * can be enabled by this code snippet:
 *
 * add_filter( 'autoptimize_filter_cache_fallback_log_errors', '__return_true' );
 *
 * Warning: the fallback files might not apply to all pages, so this is a just
 * a temporary solution, you really should clear any page cache to avoid requests
 * to files that don't exist in AO's cache.
 */

$original_request = strtok( $_SERVER['REQUEST_URI'], '?' );

if ( strpos( $original_request, 'uucss/uucss-' ) !== false ) {
    $original_request = preg_replace( '/uucss\/uucss-[a-z0-9]{32}-/', 'css/', $original_request  );
}

$fallback_target  = preg_replace( '/(.*)_(?:[a-z0-9]{32})\.(js|css)$/', '${1}_fallback.${2}', $original_request );
$ao_cache_dir     = '<!--ao-cache-dir-->';
$js_or_css        = pathinfo( $original_request, PATHINFO_EXTENSION );

// add multisite logic.
$multisite = false;
if ( true === $multisite ) {
    preg_match( '#\/([0-9]{1,5})\/(?:js|css)\/[a-z0-9]*_fallback\.(?:js|css)$#', $fallback_target, $child_site_id );
    $ao_root_cache_dir = preg_replace( '#[0-9]*\/$#', '', $ao_cache_dir );
    $ao_cache_dir      = $ao_root_cache_dir . $child_site_id[1] . '/';
}

$fallback_path = $ao_cache_dir . $js_or_css . '/<!--ao-cachefile-prefix-->fallback.' . $js_or_css;

if ( $original_request !== $fallback_target && file_exists( $fallback_path ) ) {
    // error_log( 'Autoptimize file ' . $original_request . ' not found, using fallback instead.' );
    header( 'HTTP/1.1 301 Moved Permanently' );
    header( 'Location: ' . $fallback_target );
} else {
    // error_log( 'Autoptimize file ' . $original_request . ' not found, sending 410 gone response.' );
    header( 'HTTP/1.1 410 Gone' );
}

exit();
