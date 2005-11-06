<?php
require_once('admin.php');

if ( ! current_user_can('edit_posts') )
	die ("Cheatin' uh?");

echo '/* No Styles Here */';
register_shutdown_function('execute_all_pings');
//execute_all_pings();

function execute_all_pings() {
	global $wpdb;
	// Do pingbacks
	if($pings = $wpdb->get_results("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme';")) {
		foreach($pings as $ping) {
			pingback($ping->post_content, $ping->ID);
			//echo "Pingback: $ping->post_title : $ping->ID<br/>";
			$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$ping->ID} AND meta_key = '_pingme';");
		}
	}
	// Do Enclosures
	if($enclosures = $wpdb->get_results("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme';")) {
		foreach($enclosures as $enclosure) {
			do_enclose($enclosure->post_content, $enclosure->ID);
			//echo "Enclosure: $enclosure->post_title : $enclosure->ID<br/>";
			$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$enclosure->ID} AND meta_key = '_encloseme';");
		}
	}
	// Do Trackbacks
	if($trackbacks = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE TRIM(to_ping) != '' AND post_status != 'draft'")) {
		foreach($trackbacks as $trackback) {
			//echo "trackback : $trackback->ID<br/>";
			do_trackbacks($trackback->ID);
		}
	}
}
?>
