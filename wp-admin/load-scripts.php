<?php
/**
 * Formerly concatenated the core scripts named in the `load` query parameter.
 *
 * Scripts are no longer concatenated, so this file now only responds with 410 Gone, for code
 * that still links to it directly. WordPress is not loaded, so that requests from bots and stale
 * links cost next to nothing and do not trigger deprecation notices.
 *
 * @package WordPress
 * @subpackage Administration
 * @deprecated 7.2.0 Scripts are no longer concatenated.
 */

http_response_code( 410 );
header( 'Content-Type: text/plain; charset=UTF-8' );
echo 'Scripts are no longer concatenated.';
exit;
