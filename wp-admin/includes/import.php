<?php
/**
 * WordPress Administration Importer API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Retrieve list of importers.
 *
 * @since 2.0.0
 *
 * @return array
 */
function get_importers() {
	global $wp_importers;
	if ( is_array( $wp_importers ) ) {
		uasort( $wp_importers, '_usort_by_first_member' );
	}
	return $wp_importers;
}

/**
 * Sorts a multidimensional array by first member of each top level member
 *
 * Used by uasort() as a callback, should not be used directly.
 *
 * @since 2.9.0
 * @access private
 *
 * @param array $a
 * @param array $b
 * @return int
 */
function _usort_by_first_member( $a, $b ) {
	return strnatcasecmp( $a[0], $b[0] );
}

/**
 * Register importer for WordPress.
 *
 * @since 2.0.0
 *
 * @param string $id Importer tag. Used to uniquely identify importer.
 * @param string $name Importer name and title.
 * @param string $description Importer description.
 * @param callback $callback Callback to run.
 * @return WP_Error Returns WP_Error when $callback is WP_Error.
 */
function register_importer( $id, $name, $description, $callback ) {
	global $wp_importers;
	if ( is_wp_error( $callback ) )
		return $callback;
	$wp_importers[$id] = array ( $name, $description, $callback );
}

/**
 * Cleanup importer.
 *
 * Removes attachment based on ID.
 *
 * @since 2.0.0
 *
 * @param string $id Importer ID.
 */
function wp_import_cleanup( $id ) {
	wp_delete_attachment( $id );
}

/**
 * Handle importer uploading and add attachment.
 *
 * @since 2.0.0
 *
 * @return array Uploaded file's details on success, error message on failure
 */
function wp_import_handle_upload() {
	if ( !isset($_FILES['import']) ) {
		$file['error'] = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.' );
		return $file;
	}

	$overrides = array( 'test_form' => false, 'test_type' => false );
	$_FILES['import']['name'] .= '.txt';
	$file = wp_handle_upload( $_FILES['import'], $overrides );

	if ( isset( $file['error'] ) )
		return $file;

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$filename = basename( $file );

	// Construct the object array
	$object = array( 'post_title' => $filename,
		'post_content' => $url,
		'post_mime_type' => $type,
		'guid' => $url,
		'context' => 'import',
		'post_status' => 'private'
	);

	// Save the data
	$id = wp_insert_attachment( $object, $file );

	/*
	 * Schedule a cleanup for one day from now in case of failed
	 * import or missing wp_import_cleanup() call.
	 */
	wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

	return array( 'file' => $file, 'id' => $id );
}

/**
 * Returns a list from WordPress.org of popular importer plugins.
 *
 * @since 3.5.0
 *
 * @return array Importers with metadata for each.
 */
function wp_get_popular_importers() {
	include( ABSPATH . WPINC . '/version.php' ); // include an unmodified $wp_version

	$locale = get_locale();
	$popular_importers = get_site_transient( 'popular_importers_' . $locale );

	if ( ! $popular_importers ) {
		$url = add_query_arg( 'locale', get_locale(), 'http://api.wordpress.org/core/importers/1.1/' );
		$options = array( 'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url() );
		$response = wp_remote_get( $url, $options );
		$popular_importers = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_array( $popular_importers ) )
			set_site_transient( 'popular_importers_' . $locale, $popular_importers, 2 * DAY_IN_SECONDS );
		else
			$popular_importers = false;
	}

	if ( is_array( $popular_importers ) ) {
		// If the data was received as translated, return it as-is.
		if ( $popular_importers['translated'] )
			return $popular_importers['importers'];

		foreach ( $popular_importers['importers'] as &$importer ) {
			$importer['description'] = translate( $importer['description'] );
			if ( $importer['name'] != 'WordPress' )
				$importer['name'] = translate( $importer['name'] );
		}
		return $popular_importers['importers'];
	}

	return array(
		// slug => name, description, plugin slug, and register_importer() slug
		'blogger' => array(
			'name' => __( 'Blogger' ),
			'description' => __( 'Install the Blogger importer to import posts, comments, and users from a Blogger blog.' ),
			'plugin-slug' => 'blogger-importer',
			'importer-id' => 'blogger',
		),
		'wpcat2tag' => array(
			'name' => __( 'Categories and Tags Converter' ),
			'description' => __( 'Install the category/tag converter to convert existing categories to tags or tags to categories, selectively.' ),
			'plugin-slug' => 'wpcat2tag-importer',
			'importer-id' => 'wp-cat2tag',
		),
		'livejournal' => array(
			'name' => __( 'LiveJournal' ),
			'description' => __( 'Install the LiveJournal importer to import posts from LiveJournal using their API.' ),
			'plugin-slug' => 'livejournal-importer',
			'importer-id' => 'livejournal',
		),
		'movabletype' => array(
			'name' => __( 'Movable Type and TypePad' ),
			'description' => __( 'Install the Movable Type importer to import posts and comments from a Movable Type or TypePad blog.' ),
			'plugin-slug' => 'movabletype-importer',
			'importer-id' => 'mt',
		),
		'opml' => array(
			'name' => __( 'Blogroll' ),
			'description' => __( 'Install the blogroll importer to import links in OPML format.' ),
			'plugin-slug' => 'opml-importer',
			'importer-id' => 'opml',
		),
		'rss' => array(
			'name' => __( 'RSS' ),
			'description' => __( 'Install the RSS importer to import posts from an RSS feed.' ),
			'plugin-slug' => 'rss-importer',
			'importer-id' => 'rss',
		),
		'tumblr' => array(
			'name' => __( 'Tumblr' ),
			'description' => __( 'Install the Tumblr importer to import posts &amp; media from Tumblr using their API.' ),
			'plugin-slug' => 'tumblr-importer',
			'importer-id' => 'tumblr',
		),
		'wordpress' => array(
			'name' => 'WordPress',
			'description' => __( 'Install the WordPress importer to import posts, pages, comments, custom fields, categories, and tags from a WordPress export file.' ),
			'plugin-slug' => 'wordpress-importer',
			'importer-id' => 'wordpress',
		),
	);
}
