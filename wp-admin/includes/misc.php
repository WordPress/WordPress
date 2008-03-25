<?php

function got_mod_rewrite() {
	$got_rewrite = apache_mod_loaded('mod_rewrite', true);
	return apply_filters('got_rewrite', $got_rewrite);
}

// Returns an array of strings from a file (.htaccess ) from between BEGIN
// and END markers.
function extract_from_markers( $filename, $marker ) {
	$result = array ();

	if (!file_exists( $filename ) ) {
		return $result;
	}

	if ( $markerdata = explode( "\n", implode( '', file( $filename ) ) ));
	{
		$state = false;
		foreach ( $markerdata as $markerline ) {
			if (strpos($markerline, '# END ' . $marker) !== false)
				$state = false;
			if ( $state )
				$result[] = $markerline;
			if (strpos($markerline, '# BEGIN ' . $marker) !== false)
				$state = true;
		}
	}

	return $result;
}

// Inserts an array of strings into a file (.htaccess ), placing it between
// BEGIN and END markers.  Replaces existing marked info.  Retains surrounding
// data.  Creates file if none exists.
// Returns true on write success, false on failure.
function insert_with_markers( $filename, $marker, $insertion ) {
	if (!file_exists( $filename ) || is_writeable( $filename ) ) {
		if (!file_exists( $filename ) ) {
			$markerdata = '';
		} else {
			$markerdata = explode( "\n", implode( '', file( $filename ) ) );
		}

		$f = fopen( $filename, 'w' );
		$foundit = false;
		if ( $markerdata ) {
			$state = true;
			foreach ( $markerdata as $n => $markerline ) {
				if (strpos($markerline, '# BEGIN ' . $marker) !== false)
					$state = false;
				if ( $state ) {
					if ( $n + 1 < count( $markerdata ) )
						fwrite( $f, "{$markerline}\n" );
					else
						fwrite( $f, "{$markerline}" );
				}
				if (strpos($markerline, '# END ' . $marker) !== false) {
					fwrite( $f, "# BEGIN {$marker}\n" );
					if ( is_array( $insertion ))
						foreach ( $insertion as $insertline )
							fwrite( $f, "{$insertline}\n" );
					fwrite( $f, "# END {$marker}\n" );
					$state = true;
					$foundit = true;
				}
			}
		}
		if (!$foundit) {
			fwrite( $f, "# BEGIN {$marker}\n" );
			foreach ( $insertion as $insertline )
				fwrite( $f, "{$insertline}\n" );
			fwrite( $f, "# END {$marker}\n" );
		}
		fclose( $f );
		return true;
	} else {
		return false;
	}
}

/**
 * Updates the htaccess file with the current rules if it is writable.
 *
 * Always writes to the file if it exists and is writable to ensure that we blank out old rules.
 */

function save_mod_rewrite_rules() {
	global $wp_rewrite;

	$home_path = get_home_path();
	$htaccess_file = $home_path.'.htaccess';

	// If the file doesn't already exists check for write access to the directory and whether of not we have some rules.
	// else check for write access to the file.
	if ((!file_exists($htaccess_file) && is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
		if ( got_mod_rewrite() ) {
			$rules = explode( "\n", $wp_rewrite->mod_rewrite_rules() );
			return insert_with_markers( $htaccess_file, 'WordPress', $rules );
		}
	}

	return false;
}

function update_recently_edited( $file ) {
	$oldfiles = (array ) get_option( 'recently_edited' );
	if ( $oldfiles ) {
		$oldfiles = array_reverse( $oldfiles );
		$oldfiles[] = $file;
		$oldfiles = array_reverse( $oldfiles );
		$oldfiles = array_unique( $oldfiles );
		if ( 5 < count( $oldfiles ))
			array_pop( $oldfiles );
	} else {
		$oldfiles[] = $file;
	}
	update_option( 'recently_edited', $oldfiles );
}

// If siteurl or home changed, flush rewrite rules.
function update_home_siteurl( $old_value, $value ) {
	global $wp_rewrite;

	if ( defined( "WP_INSTALLING" ) )
		return;

	// If home changed, write rewrite rules to new location.
	$wp_rewrite->flush_rules();
}

add_action( 'update_option_home', 'update_home_siteurl', 10, 2 );
add_action( 'update_option_siteurl', 'update_home_siteurl', 10, 2 );

function url_shorten( $url ) {
	$short_url = str_replace( 'http://', '', stripslashes( $url ));
	$short_url = str_replace( 'www.', '', $short_url );
	if ('/' == substr( $short_url, -1 ))
		$short_url = substr( $short_url, 0, -1 );
	if ( strlen( $short_url ) > 35 )
		$short_url = substr( $short_url, 0, 32 ).'...';
	return $short_url;
}

function wp_reset_vars( $vars ) {
	for ( $i=0; $i<count( $vars ); $i += 1 ) {
		$var = $vars[$i];
		global $$var;

		if (!isset( $$var ) ) {
			if ( empty( $_POST["$var"] ) ) {
				if ( empty( $_GET["$var"] ) )
					$$var = '';
				else
					$$var = $_GET["$var"];
			} else {
				$$var = $_POST["$var"];
			}
		}
	}
}

?>
