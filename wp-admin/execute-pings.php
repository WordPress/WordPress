<?php

require_once('../wp-config.php');

// Do pingbacks
while ($ping = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme' LIMIT 1")) {
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$ping->ID} AND meta_key = '_pingme';");
	pingback($ping->post_content, $ping->ID);
}

// Do Enclosures
while ($enclosure = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme' LIMIT 1")) {
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$enclosure->ID} AND meta_key = '_encloseme';");
	do_enclose($enclosure->post_content, $enclosure->ID);
}

// Do Trackbacks
$trackbacks = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE CHAR_LENGTH(TRIM(to_ping)) > 7 AND post_status != 'draft'");
if ( is_array($trackbacks) ) {
	foreach ( $trackbacks as $trackback ) {
		do_trackbacks($trackback->ID);
	}
}

?>
