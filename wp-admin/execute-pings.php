<?php
require_once('../wp-config.php');

register_shutdown_function('execute_all_pings');
//execute_all_pings();

function execute_all_pings() {
	global $wpdb;
	// Do pingbacks
	while ($ping = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$ping->ID} AND meta_key = '_pingme';");
		pingback($ping->post_content, $ping->ID);
		echo "Pingback: $ping->post_title : $ping->ID<br/>";
	}
	// Do Enclosures
	while ($enclosure = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$enclosure->ID} AND meta_key = '_encloseme';");
		do_enclose($enclosure->post_content, $enclosure->ID);
		echo "Enclosure: $enclosure->post_title : $enclosure->ID<br/>";
	}
	// Do Trackbacks
	while ($trackback = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE TRIM(to_ping) != '' AND post_status != 'draft' LIMIT 1")) {
		echo "Trackback : $trackback->ID<br/>";
		do_trackbacks($trackback->ID);
	}
}

_e('Done.');

?>
