<?php
require_once('../wp-config.php');
require_once('admin-functions.php');
require_once('admin-db.php');

if ( !current_user_can('manage_categories') )
	die('-1');

function get_out_now() { exit; }

add_action('shutdown', 'get_out_now', -1);

$names = explode(',', rawurldecode($_GET['ajaxnewcat']) );
$ids   = array();

foreach ($names as $cat_name) {
	$cat_name = trim( $cat_name );

	if ( !$category_nicename = sanitize_title($cat_name) )
		continue;
	if ( $already = category_exists($cat_name) ) {
		$ids[] = (string) $already;
		continue;
	}

	$new_cat_id = wp_create_category($cat_name);

	$ids[] = (string) $new_cat_id;
}

$return = join(',', $ids);

die( (string) $return );

?>