<?php
/**
 * Defines the Gears manifest file for Google Gears offline storage.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
error_reporting(0);

/** Set ABSPATH for execution */
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
define( 'WPINC', 'wp-includes' );

require(ABSPATH . '/wp-admin/includes/manifest.php');

$man_version = '';

$files = get_manifest($man_version);

$man_version = md5($man_version);

header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
header( 'Pragma: no-cache' );
header( 'Content-Type: text/cache-manifest; charset=UTF-8' ); 
?>
CACHE MANIFEST 
# 
# Version: <?php echo $man_version; ?>_20080710 
# 
<?php
foreach ( $files as $file ) {
	// If version is not set, just output the file
	if ( !isset($file[1]) )
		echo $file[0] . "\n";
	// If ver is set but ignoreQuery is not, output file with ver tacked on
	else
		echo $file[0] . '?ver=' . $file[1] . "\n";
}

