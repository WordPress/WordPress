<?php
/**
 * WordPress Options Header.
 *
 * Resets variables: 'action', 'standalone', and 'option_group_id'. Displays
 * updated message, if updated variable is part of the URL query.
 *
 * @package WordPress
 * @subpackage Administration
 */

wp_reset_vars(array('action', 'standalone', 'option_group_id'));

if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
	// For backwards compat with plugins that don't use the Settings API and just set updated=1 in the redirect
	add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
}

settings_errors();

?>