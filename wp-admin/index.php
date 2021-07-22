<?php
/**
 * Dashboard Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once __DIR__ . '/admin.php';

/** Load WordPress dashboard API */
require_once ABSPATH . 'wp-admin/includes/dashboard.php';

wp_dashboard_setup();

wp_enqueue_script( 'dashboard' );

if ( current_user_can( 'install_plugins' ) ) {
	wp_enqueue_script( 'plugin-install' );
	wp_enqueue_script( 'updates' );
}
if ( current_user_can( 'upload_files' ) ) {
	wp_enqueue_script( 'media-upload' );
}
add_thickbox();

if ( wp_is_mobile() ) {
	wp_enqueue_script( 'jquery-touch-punch' );
}

// Used in the HTML title tag.
$title       = __( 'Dashboard' );
$parent_file = 'index.php';

$help = '<p>' . __( 'Welcome to your WordPress Dashboard! This is the screen you will see when you log in to your site, and gives you access to all the site management features of WordPress. You can get help for any screen by clicking the Help tab above the screen title.' ) . '</p>';

$screen = get_current_screen();

$screen->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => $help,
	)
);

// Help tabs.

$help  = '<p>' . __( 'The left-hand navigation menu provides links to all of the WordPress administration screens, with submenu items displayed on hover. You can minimize this menu to a narrow icon strip by clicking on the Collapse Menu arrow at the bottom.' ) . '</p>';
$help .= '<p>' . __( 'Links in the Toolbar at the top of the screen connect your dashboard and the front end of your site, and provide access to your profile and helpful WordPress information.' ) . '</p>';

$screen->add_help_tab(
	array(
		'id'      => 'help-navigation',
		'title'   => __( 'Navigation' ),
		'content' => $help,
	)
);

$help  = '<p>' . __( 'You can use the following controls to arrange your Dashboard screen to suit your workflow. This is true on most other administration screens as well.' ) . '</p>';
$help .= '<p>' . __( '<strong>Screen Options</strong> &mdash; Use the Screen Options tab to choose which Dashboard boxes to show.' ) . '</p>';
$help .= '<p>' . __( '<strong>Drag and Drop</strong> &mdash; To rearrange the boxes, drag and drop by clicking on the title bar of the selected box and releasing when you see a gray dotted-line rectangle appear in the location you want to place the box.' ) . '</p>';
$help .= '<p>' . __( '<strong>Box Controls</strong> &mdash; Click the title bar of the box to expand or collapse it. Some boxes added by plugins may have configurable content, and will show a &#8220;Configure&#8221; link in the title bar if you hover over it.' ) . '</p>';

$screen->add_help_tab(
	array(
		'id'      => 'help-layout',
		'title'   => __( 'Layout' ),
		'content' => $help,
	)
);

$help = '<p>' . __( 'The boxes on your Dashboard screen are:' ) . '</p>';

if ( current_user_can( 'edit_theme_options' ) ) {
	$help .= '<p>' . __( '<strong>Welcome</strong> &mdash; Shows links for some of the most common tasks when setting up a new site.' ) . '</p>';
}

if ( current_user_can( 'view_site_health_checks' ) ) {
	$help .= '<p>' . __( '<strong>Site Health Status</strong> &mdash; Informs you of any potential issues that should be addressed to improve the performance or security of your website.' ) . '</p>';
}

if ( current_user_can( 'edit_posts' ) ) {
	$help .= '<p>' . __( '<strong>At a Glance</strong> &mdash; Displays a summary of the content on your site and identifies which theme and version of WordPress you are using.' ) . '</p>';
}

$help .= '<p>' . __( '<strong>Activity</strong> &mdash; Shows the upcoming scheduled posts, recently published posts, and the most recent comments on your posts and allows you to moderate them.' ) . '</p>';

if ( is_blog_admin() && current_user_can( 'edit_posts' ) ) {
	$help .= '<p>' . __( "<strong>Quick Draft</strong> &mdash; Allows you to create a new post and save it as a draft. Also displays links to the 3 most recent draft posts you've started." ) . '</p>';
}

$help .= '<p>' . sprintf(
	/* translators: %s: WordPress Planet URL. */
	__( '<strong>WordPress Events and News</strong> &mdash; Upcoming events near you as well as the latest news from the official WordPress project and the <a href="%s">WordPress Planet</a>.' ),
	__( 'https://planet.wordpress.org/' )
) . '</p>';

$screen->add_help_tab(
	array(
		'id'      => 'help-content',
		'title'   => __( 'Content' ),
		'content' => $help,
	)
);

unset( $help );

$screen->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/dashboard-screen/">Documentation on Dashboard</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap">
	<h1><?php echo esc_html( $title ); ?></h1>

	<?php
	if ( ! empty( $_GET['admin_email_remind_later'] ) ) :
		/** This filter is documented in wp-login.php */
		$remind_interval = (int) apply_filters( 'admin_email_remind_interval', 3 * DAY_IN_SECONDS );
		$postponed_time  = get_option( 'admin_email_lifespan' );

		/*
		 * Calculate how many seconds it's been since the reminder was postponed.
		 * This allows us to not show it if the query arg is set, but visited due to caches, bookmarks or similar.
		 */
		$time_passed = time() - ( $postponed_time - $remind_interval );

		// Only show the dashboard notice if it's been less than a minute since the message was postponed.
		if ( $time_passed < MINUTE_IN_SECONDS ) :
			?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: Human-readable time interval. */
					__( 'The admin email verification page will reappear after %s.' ),
					human_time_diff( time() + $remind_interval )
				);
				?>
			</p>
		</div>
		<?php endif; ?>
	<?php endif; ?>

<?php
if ( has_action( 'welcome_panel' ) && current_user_can( 'edit_theme_options' ) ) :
	$classes = 'welcome-panel';

	$option = (int) get_user_meta( get_current_user_id(), 'show_welcome_panel', true );
	// 0 = hide, 1 = toggled to show or single site creator, 2 = multisite site owner.
	$hide = ( 0 === $option || ( 2 === $option && wp_get_current_user()->user_email !== get_option( 'admin_email' ) ) );
	if ( $hide ) {
		$classes .= ' hidden';
	}
	?>

	<div id="welcome-panel" class="<?php echo esc_attr( $classes ); ?>">
		<?php wp_nonce_field( 'welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
		<a class="welcome-panel-close" href="<?php echo esc_url( admin_url( '?welcome=0' ) ); ?>" aria-label="<?php esc_attr_e( 'Dismiss the welcome panel' ); ?>"><?php _e( 'Dismiss' ); ?></a>
		<?php
		/**
		 * Add content to the welcome panel on the admin dashboard.
		 *
		 * To remove the default welcome panel, use remove_action():
		 *
		 *     remove_action( 'welcome_panel', 'wp_welcome_panel' );
		 *
		 * @since 3.5.0
		 */
		do_action( 'welcome_panel' );
		?>
	</div>
<?php endif; ?>

	<div id="dashboard-widgets-wrap">
	<?php wp_dashboard(); ?>
	</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php
wp_print_community_events_templates();

require_once ABSPATH . 'wp-admin/admin-footer.php';
