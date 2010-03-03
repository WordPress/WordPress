<?php
/**
 * Network installation functions.
 *
 * @since 3.0.0
 *
 * @package WordPress
 * @subpackage Multisite
 */

/**
 * Gets base domain of network.
 *
 * @since 3.0.0
 */
function get_clean_basedomain() {
	global $wpdb;

	$existing_domain = network_domain_check();
	if ( $existing_domain )
		return $existing_domain;

	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	if ( strpos( $domain, '/' ) )
		$domain = substr( $domain, 0, strpos( $domain, '/' ) );
	return $domain;
}

/**
 * Checks for existing network data/tables.
 *
 * @since 3.0.0
 */
function network_domain_check() {
	global $wpdb;

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		return $wpdb->get_var( "SELECT domain FROM $wpdb->site ORDER BY id ASC LIMIT 1" );

	return false;
}

/**
 * Prints summary of server statistics in preparation for setting up a network.
 *
 * @since 3.0.0
 */
function filestats( $err ) {
?>
	<h2><?php esc_html_e( 'Server Summary' ); ?></h2>
	<p><?php _e( 'If you post a message to the WordPress support forum at <a target="_blank" href="http://wordpress.org/support/">http://wordpress.org/support/</a> then copy and paste the following information into your message:' ); ?></p>
	<blockquote style="background: #eee; border: 1px solid #333; padding: 5px;">
	<br /><strong><?php printf( __( 'ERROR: %s' ), $err ); ?></strong><br />
<?php
	clearstatcache();
	$files = array( 'htaccess.dist', '.htaccess' );

	$indent = '&nbsp;&nbsp;&nbsp;&nbsp;';
	foreach ( (array) $files as $val ) {
		$stats = @stat( $val );
		if ( $stats ) {
?>
			<h2><?php echo esc_html( $val ); ?></h2>
			<?php echo $indent . sprintf( __( 'uid/gid: %1$s/%2$s' ), $stats['uid'], $stats['gid'] ); ?><br />
			<?php echo $indent . sprintf( __( 'size: %s' ), $stats['size'] ); ?><br/>
			<?php echo $indent . sprintf( __( 'perms: %s' ), substr( sprintf( '%o', fileperms( $val ) ), -4 ) ); ?><br/>
			<?php echo $indent . sprintf( __( 'readable: %s' ), is_readable( $val ) ? __( 'yes' ) : __( 'no' ) ); ?><br/>
			<?php echo $indent . sprintf( __( 'writeable: %s' ), is_writeable( $val ) ? __( 'yes' ) : __( 'no' ) ); ?><br/>
<?php
		} elseif ( ! file_exists( $val ) ) {
?>
			<h2><?php echo esc_html( $val ); ?></h2>
			<?php echo $indent . sprintf( __( 'FILE NOT FOUND: %s' ), $val ); ?><br/>
<?php
		}
	}
	echo "</blockquote>";
}
