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

settings_errors();

?>