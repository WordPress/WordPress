<?php
/**
 * Multisite upgrade administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

require_once ABSPATH . WPINC . '/http.php';

/**
 * @global int $wp_db_version WordPress database version.
 */
global $wp_db_version;

// Used in the HTML title tag.
$title       = __( 'Upgrade Network' );
$parent_file = 'upgrade.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
			'<p>' . __( 'Only use this screen once you have updated to a new version of WordPress through Updates/Available Updates (via the Network Administration navigation menu or the Toolbar). Clicking the Upgrade Network button will step through each site in the network, five at a time, and make sure any database updates are applied.' ) . '</p>' .
			'<p>' . __( 'If a version update to core has not happened, clicking this button will not affect anything.' ) . '</p>' .
			'<p>' . __( 'If this process fails for any reason, users logging in to their sites will force the same update.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://developer.wordpress.org/advanced-administration/multisite/admin/#network-admin-updates-screen">Documentation on Upgrade Network</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

require_once ABSPATH . 'wp-admin/admin-header.php';

if ( ! current_user_can( 'upgrade_network' ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

echo '<div class="wrap">';
echo '<h1>' . __( 'Upgrade Network' ) . '</h1>';

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'show';

switch ( $action ) {
	case 'upgrade':
		$n = ( isset( $_GET['n'] ) ) ? (int) $_GET['n'] : 0;

		if ( $n < 5 ) {
			update_site_option( 'wpmu_upgrade_site', $wp_db_version );
		}

		$site_ids = get_sites(
			array(
				'spam'                   => 0,
				'deleted'                => 0,
				'archived'               => 0,
				'network_id'             => get_current_network_id(),
				'number'                 => 5,
				'offset'                 => $n,
				'fields'                 => 'ids',
				'order'                  => 'DESC',
				'orderby'                => 'id',
				'update_site_meta_cache' => false,
			)
		);
		if ( empty( $site_ids ) ) {
			echo '<p>' . __( 'All done!' ) . '</p>';
			break;
		}
		echo '<ul>';
		foreach ( (array) $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			$siteurl     = site_url();
			$upgrade_url = admin_url( 'upgrade.php?step=upgrade_db' );
			restore_current_blog();

			echo "<li>$siteurl</li>";

			$response = wp_remote_get(
				$upgrade_url,
				array(
					'timeout'     => 120,
					'httpversion' => '1.1',
					'sslverify'   => false,
				)
			);

			if ( is_wp_error( $response ) ) {
				wp_die(
					sprintf(
						/* translators: 1: Site URL, 2: Server error message. */
						__( 'Warning! Problem updating %1$s. Your server may not be able to connect to sites running on it. Error message: %2$s' ),
						$siteurl,
						'<em>' . $response->get_error_message() . '</em>'
					)
				);
			}

			/**
			 * Fires after the Multisite DB upgrade for each site is complete.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param array $response The upgrade response array.
			 */
			do_action( 'after_mu_upgrade', $response );

			/**
			 * Fires after each site has been upgraded.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $site_id The Site ID.
			 */
			do_action( 'wpmu_upgrade_site', $site_id );
		}
		echo '</ul>';
		?><p><?php _e( 'If your browser does not start loading the next page automatically, click this link:' ); ?> <a class="button" href="upgrade.php?action=upgrade&amp;n=<?php echo ( $n + 5 ); ?>"><?php _e( 'Next Sites' ); ?></a></p>
		<script type="text/javascript">
		<!--
		function nextpage() {
			location.href = "upgrade.php?action=upgrade&n=<?php echo ( $n + 5 ); ?>";
		}
		setTimeout( "nextpage()", 250 );
		//-->
		</script>
		<?php
		break;
	case 'show':
	default:
		if ( (int) get_site_option( 'wpmu_upgrade_site' ) !== $wp_db_version ) :
			?>
		<h2><?php _e( 'Database Update Required' ); ?></h2>
		<p><?php _e( 'WordPress has been updated! Next and final step is to individually upgrade the sites in your network.' ); ?></p>
		<?php endif; ?>

		<p><?php _e( 'The database update process may take a little while, so please be patient.' ); ?></p>
		<p><a class="button button-primary" href="upgrade.php?action=upgrade"><?php _e( 'Upgrade Network' ); ?></a></p>
		<?php
		/**
		 * Fires before the footer on the network upgrade screen.
		 *
		 * @since MU (3.0.0)
		 */
		do_action( 'wpmu_upgrade_page' );
		break;
}
?>
</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
