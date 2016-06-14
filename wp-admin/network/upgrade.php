<?php
/**
 * Multisite upgrade administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

require_once( ABSPATH . WPINC . '/http.php' );

$title = __( 'Upgrade Network' );
$parent_file = 'upgrade.php';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' =>
		'<p>' . __('Only use this screen once you have updated to a new version of WordPress through Updates/Available Updates (via the Network Administration navigation menu or the Toolbar). Clicking the Upgrade Network button will step through each site in the network, five at a time, and make sure any database updates are applied.') . '</p>' .
		'<p>' . __('If a version update to core has not happened, clicking this button won&#8217;t affect anything.') . '</p>' .
		'<p>' . __('If this process fails for any reason, users logging in to their sites will force the same update.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Network_Admin_Updates_Screen" target="_blank">Documentation on Upgrade Network</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

require_once( ABSPATH . 'wp-admin/admin-header.php' );

if ( ! current_user_can( 'manage_network' ) )
	wp_die( __( 'You do not have permission to access this page.' ), 403 );

echo '<div class="wrap">';
echo '<h1>' . __( 'Upgrade Network' ) . '</h1>';

$action = isset($_GET['action']) ? $_GET['action'] : 'show';

switch ( $action ) {
	case "upgrade":
		$n = ( isset($_GET['n']) ) ? intval($_GET['n']) : 0;

		if ( $n < 5 ) {
			/**
			 * @global string $wp_db_version
			 */
			global $wp_db_version;
			update_site_option( 'wpmu_upgrade_site', $wp_db_version );
		}

		$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' AND spam = '0' AND deleted = '0' AND archived = '0' ORDER BY registered DESC LIMIT {$n}, 5", ARRAY_A );
		if ( empty( $blogs ) ) {
			echo '<p>' . __( 'All done!' ) . '</p>';
			break;
		}
		echo "<ul>";
		foreach ( (array) $blogs as $details ) {
			switch_to_blog( $details['blog_id'] );
			$siteurl = site_url();
			$upgrade_url = admin_url( 'upgrade.php?step=upgrade_db' );
			restore_current_blog();

			echo "<li>$siteurl</li>";

			$response = wp_remote_get( $upgrade_url, array(
				'timeout'     => 120,
				'httpversion' => '1.1',
				'sslverify'   => false,
			) );
			if ( is_wp_error( $response ) ) {
				wp_die( sprintf(
					/* translators: 1: site url, 2: server error message */
					__( 'Warning! Problem updating %1$s. Your server may not be able to connect to sites running on it. Error message: %2$s' ),
					$siteurl,
					'<em>' . $response->get_error_message() . '</em>'
				) );
			}

			/**
			 * Fires after the Multisite DB upgrade for each site is complete.
			 *
			 * @since MU
			 *
			 * @param array|WP_Error $response The upgrade response array or WP_Error on failure.
			 */
			do_action( 'after_mu_upgrade', $response );
			/**
			 * Fires after each site has been upgraded.
			 *
			 * @since MU
			 *
			 * @param int $blog_id The Site ID.
			 */
			do_action( 'wpmu_upgrade_site', $details[ 'blog_id' ] );
		}
		echo "</ul>";
		?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="upgrade.php?action=upgrade&amp;n=<?php echo ($n + 5) ?>"><?php _e("Next Sites"); ?></a></p>
		<script type="text/javascript">
		<!--
		function nextpage() {
			location.href = "upgrade.php?action=upgrade&n=<?php echo ($n + 5) ?>";
		}
		setTimeout( "nextpage()", 250 );
		//-->
		</script><?php
	break;
	case 'show':
	default:
		if ( get_site_option( 'wpmu_upgrade_site' ) != $GLOBALS['wp_db_version'] ) :
		?>
		<h2><?php _e( 'Database Update Required' ); ?></h2>
		<p><?php _e( 'WordPress has been updated! Before we send you on your way, we need to individually upgrade the sites in your network.' ); ?></p>
		<?php endif; ?>

		<p><?php _e( 'The database update process may take a little while, so please be patient.' ); ?></p>
		<p><a class="button button-primary" href="upgrade.php?action=upgrade"><?php _e( 'Upgrade Network' ); ?></a></p>
		<?php
		/**
		 * Fires before the footer on the network upgrade screen.
		 *
		 * @since MU
		 */
		do_action( 'wpmu_upgrade_page' );
	break;
}
?>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
