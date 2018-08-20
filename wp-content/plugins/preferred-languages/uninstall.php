<?php
/**
 * Plugin uninstall routine.
 *
 * Removes all options and meta data added by the plugin.
 *
 * @package PreferredLanguages
 */

defined( 'WP_UNINSTALL_PLUGIN' ) or die;

delete_option( 'preferred_languages' );
delete_metadata( 'user', null, 'preferred_languages', '', true );
