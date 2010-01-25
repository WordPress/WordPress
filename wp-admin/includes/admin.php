<?php
/**
 * Includes all of the WordPress Administration API files.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Bookmark Administration API */
require_once(ABSPATH . 'wp-admin/includes/bookmark.php');

/** WordPress Comment Administration API */
require_once(ABSPATH . 'wp-admin/includes/comment.php');

/** WordPress Administration File API */
require_once(ABSPATH . 'wp-admin/includes/file.php');

/** WordPress Image Administration API */
require_once(ABSPATH . 'wp-admin/includes/image.php');

/** WordPress Media Administration API */
require_once(ABSPATH . 'wp-admin/includes/media.php');

/** WordPress Import Administration API */
require_once(ABSPATH . 'wp-admin/includes/import.php');

/** WordPress Misc Administration API */
require_once(ABSPATH . 'wp-admin/includes/misc.php');

/** WordPress Plugin Administration API */
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

/** WordPress Post Administration API */
require_once(ABSPATH . 'wp-admin/includes/post.php');

/** WordPress Taxonomy Administration API */
require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');

/** WordPress Template Administration API */
require_once(ABSPATH . 'wp-admin/includes/template.php');

/** WordPress Theme Administration API */
require_once(ABSPATH . 'wp-admin/includes/theme.php');

/** WordPress User Administration API */
require_once(ABSPATH . 'wp-admin/includes/user.php');

/** WordPress Update Administration API */
require_once(ABSPATH . 'wp-admin/includes/update.php');

/** WordPress Registration API */
require_once(ABSPATH . WPINC . '/registration.php');

/** WordPress Deprecated Administration API */
require_once(ABSPATH . 'wp-admin/includes/deprecated.php');

/** WordPress Multi-Site support API */
if ( is_multisite() )
	require_once(ABSPATH . 'wp-admin/includes/ms.php');

?>
