<?php
/**
 * My Sites dashboard.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once __DIR__ . '/admin.php';

if ( ! is_multisite() ) {
	wp_die( __( 'Multisite support is not enabled.' ) );
}

if ( ! current_user_can( 'read' ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ) );
}

$action = isset( $_POST['action'] ) ? $_POST['action'] : 'splash';

$blogs = get_blogs_of_user( $current_user->ID );

$updated = false;
if ( 'updateblogsettings' === $action && isset( $_POST['primary_blog'] ) ) {
	check_admin_referer( 'update-my-sites' );

	$blog = get_site( (int) $_POST['primary_blog'] );
	if ( $blog && isset( $blog->domain ) ) {
		update_user_meta( $current_user->ID, 'primary_blog', (int) $_POST['primary_blog'] );
		$updated = true;
	} else {
		wp_die( __( 'The primary site you chose does not exist.' ) );
	}
}

// Used in the HTML title tag.
$title       = __( 'My Sites' );
$parent_file = 'index.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
			'<p>' . __( 'This screen shows an individual user all of their sites in this network, and also allows that user to set a primary site. They can use the links under each site to visit either the front end or the dashboard for that site.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Dashboard_My_Sites_Screen">Documentation on My Sites</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

require_once ABSPATH . 'wp-admin/admin-header.php';

if ( $updated ) {
	wp_admin_notice(
		'<strong>' . __( 'Settings saved.' ) . '</strong>',
		array(
			'type'        => 'success',
			'dismissible' => true,
			'id'          => 'message',
		)
	);
}
?>

<div class="wrap">
<h1 class="wp-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( in_array( get_site_option( 'registration' ), array( 'all', 'blog' ), true ) ) {
	/** This filter is documented in wp-login.php */
	$sign_up_url = apply_filters( 'wp_signup_location', network_site_url( 'wp-signup.php' ) );
	printf( ' <a href="%s" class="page-title-action">%s</a>', esc_url( $sign_up_url ), esc_html__( 'Add New Site' ) );
}

if ( empty( $blogs ) ) :
	wp_admin_notice(
		'<strong>' . __( 'You must be a member of at least one site to use this page.' ) . '</strong>',
		array(
			'type'        => 'error',
			'dismissible' => true,
		)
	);
	?>
	<?php
else :
	?>

<hr class="wp-header-end">

<form id="myblogs" method="post">
	<?php
	choose_primary_blog();
	/**
	 * Fires before the sites list on the My Sites screen.
	 *
	 * @since 3.0.0
	 */
	do_action( 'myblogs_allblogs_options' );
	?>
	<br clear="all" />
	<ul class="my-sites striped">
	<?php
	/**
	 * Filters the settings HTML markup in the Global Settings section on the My Sites screen.
	 *
	 * By default, the Global Settings section is hidden. Passing a non-empty
	 * string to this filter will enable the section, and allow new settings
	 * to be added, either globally or for specific sites.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $settings_html The settings HTML markup. Default empty.
	 * @param string $context       Context of the setting (global or site-specific). Default 'global'.
	 */
	$settings_html = apply_filters( 'myblogs_options', '', 'global' );

	if ( $settings_html ) {
		echo '<h3>' . __( 'Global Settings' ) . '</h3>';
		echo $settings_html;
	}

	reset( $blogs );

	foreach ( $blogs as $user_blog ) {
		switch_to_blog( $user_blog->userblog_id );

		echo '<li>';
		echo "<h3>{$user_blog->blogname}</h3>";

		$actions = "<a href='" . esc_url( home_url() ) . "'>" . __( 'Visit' ) . '</a>';

		if ( current_user_can( 'read' ) ) {
			$actions .= " | <a href='" . esc_url( admin_url() ) . "'>" . __( 'Dashboard' ) . '</a>';
		}

		/**
		 * Filters the row links displayed for each site on the My Sites screen.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string $actions   The HTML site link markup.
		 * @param object $user_blog An object containing the site data.
		 */
		$actions = apply_filters( 'myblogs_blog_actions', $actions, $user_blog );

		echo "<p class='my-sites-actions'>" . $actions . '</p>';

		/** This filter is documented in wp-admin/my-sites.php */
		echo apply_filters( 'myblogs_options', '', $user_blog );

		echo '</li>';

		restore_current_blog();
	}
	?>
	</ul>
	<?php
	if ( count( $blogs ) > 1 || has_action( 'myblogs_allblogs_options' ) || has_filter( 'myblogs_options' ) ) {
		?>
		<input type="hidden" name="action" value="updateblogsettings" />
		<?php
		wp_nonce_field( 'update-my-sites' );
		submit_button();
	}
	?>
	</form>
<?php endif; ?>
	</div>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
