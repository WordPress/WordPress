<?php
/**
 * EventON Uninstall
 *
 * Uninstalling EventON deletes everything.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON/Uninstaller
 * @version     1.1
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();

global $wpdb, $wp_roles;

// Delete options
$wpdb->query("DELETE FROM $wpdb->options WHERE 
	option_name LIKE 'evcal_%' 
	OR option_name LIKE '_evo_%'
	OR option_name LIKE 'eventon_%';");