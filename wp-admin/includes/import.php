<?php

function get_importers() {
	global $wp_importers;
	if ( is_array($wp_importers) )
		uasort($wp_importers, create_function('$a, $b', 'return strcmp($a[0], $b[0]);'));
	return $wp_importers;
}

function register_importer( $id, $name, $description, $callback ) {
	global $wp_importers;
	if ( is_wp_error( $callback ) )
		return $callback;
	$wp_importers[$id] = array ( $name, $description, $callback );
}

function wp_import_cleanup( $id ) {
	wp_delete_attachment( $id );
}

function wp_import_handle_upload() {
	$overrides = array( 'test_form' => false, 'test_type' => false );
	$_FILES['import']['name'] .= '.import';
	$file = wp_handle_upload( $_FILES['import'], $overrides );

	if ( isset( $file['error'] ) )
		return $file;

	$url = $file['url'];
	$type = $file['type'];
	$file = addslashes( $file['file'] );
	$filename = basename( $file );

	// Construct the object array
	$object = array( 'post_title' => $filename,
		'post_content' => $url,
		'post_mime_type' => $type,
		'guid' => $url
	);

	// Save the data
	$id = wp_insert_attachment( $object, $file );

	return array( 'file' => $file, 'id' => $id );
}

?>
