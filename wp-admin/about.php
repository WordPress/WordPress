<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

wp_enqueue_script( 'about' );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<div class="about-text"><?php echo str_replace( '3.7', $display_version, __( 'Thank you for updating to WordPress 3.7! You might not notice a thing, and we&#8217;re okay with that.' ) ); ?></div>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper">
	<a href="about.php" class="nav-tab nav-tab-active">
		<?php _e( 'What&#8217;s New' ); ?>
	</a><a href="credits.php" class="nav-tab">
		<?php _e( 'Credits' ); ?>
	</a><a href="freedoms.php" class="nav-tab">
		<?php _e( 'Freedoms' ); ?>
	</a>
</h2>

<div class="changelog point-releases">
	<h3><?php echo _n( 'Maintenance and Security Release', 'Maintenance and Security Releases', 6 ); ?></h3>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 8 ), '3.7.6' ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.7.6' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 8 ), '3.7.5', number_format_i18n( 8 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.7.5' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.', 5 ), '3.7.4', number_format_i18n( 5 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.7.4' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
		'<strong>Version %1$s</strong> addressed %2$s bugs.', 2 ), '3.7.3', number_format_i18n( 2 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.7.3' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 9 ), '3.7.2', number_format_i18n( 9 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.7.2' ); ?>
 	</p>
	<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
		'<strong>Version %1$s</strong> addressed %2$s bugs.', 11 ), '3.7.1', number_format_i18n( 11 ) ); ?>
		<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'http://codex.wordpress.org/Version_3.7.1' ); ?>
 	</p>
</div>

<div class="changelog">
	<h3><?php _e( 'Background Updates' ); ?></h3>

	<div class="feature-section col three-col about-updates">
		<div class="col-1">
			<h4><?php _e( 'Updates While You Sleep' ); ?></h4>
			<p><?php _e( 'With WordPress 3.7, you don&#8217;t have to lift a finger to apply maintenance and security updates. Most sites are now able to automatically apply these updates in the background, though some configurations may not allow it.' ); ?></p>
		</div>
		<div class="col-2">
			<img alt="" src="<?php echo admin_url( 'images/about-updates-2x.png' ); ?>" />
		</div>
		<div class="col-3 last-feature">
			<h4><?php _e( 'More Reliable Than Ever' ); ?></h4>
			<p><?php _e( 'The update process has been made even more reliable and secure, with dozens of new checks and safeguards.' ); ?></p>
			<p><?php _e( 'You&#8217;ll still need to click &#8220;Update Now&#8221; once WordPress 3.8 is released, but we&#8217;ve never had more confidence in that beautiful blue button.' ); ?></p>
		</div>
		<?php
		if ( current_user_can( 'update_core' ) ) {
			$future_minor_update = (object) array(
				'current'       => $wp_version . '.1.next.minor',
				'version'       => $wp_version . '.1.next.minor',
				'php_version'   => $required_php_version,
				'mysql_version' => $required_mysql_version,
			);
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			$updater = new WP_Automatic_Updater;
			$can_auto_update = wp_http_supports( array( 'ssl' ) ) && $updater->should_update( 'core', $future_minor_update, ABSPATH );

			if ( $can_auto_update ) {
				echo '<p class="about-auto-update cool">' . __( 'This site <strong>is</strong> able to apply these updates automatically. Cool!' ). '</p>';

			// If the updater is disabled entirely, don't show them anything.
			} elseif ( ! $updater->is_disabled() ) {
				echo '<p class="about-auto-update">';
				// If this is is filtered to false, they won't get emails, so don't claim we will.
				// Assumption: If the user can update core, they can see what the admin email is.

				/** This filter is documented in wp-admin/includes/class-wp-upgrader.php */
				if ( apply_filters( 'send_core_update_notification_email', true, $future_minor_update ) ) {
					printf( __( 'This site <strong>is not</strong> able to apply these updates automatically. But we&#8217;ll email %s when there is a new security release.' ), esc_html( get_site_option( 'admin_email' ) ) );
				} else {
					_e( 'This site <strong>is not</strong> able to apply these updates automatically.' );
				}
				echo '</p>';
			}
		}
		?>
	</div>
</div>

<div class="changelog about-passwords">
	<h3><?php _e( 'Create Stronger Passwords' ); ?></h3>

	<div class="feature-section col two-col">
		<div>
			<p><?php _e( 'Your password is your site&#8217;s first line of defense. It&#8217;s best to create passwords that are complex, long, and unique. To that end, our password meter has been updated in WordPress 3.7 to recognize common mistakes that can weaken your password: dates, names, keyboard patterns (123456789), and even pop culture references.' ); ?></p>
			<p><strong><?php _e( 'Try it out on the right.' ); ?></strong></p>
		</div>
		<div class="last-feature about-password-meter">
			<input type="password" id="pass" size="25" value="" />
			<p id="pass-strength-result" ><?php _e( 'Strength indicator' ); ?></p>
			<?php printf( __( 'Getting the urge to <a href="%s">change your password</a>?' ), esc_url( self_admin_url( 'profile.php' ) ) ); ?>
		</div>
	</div>
</div>

<div class="changelog">
	<div class="feature-section col two-col">
		<div>
			<h3><?php _e( 'Improved Search Results' ); ?></h3>
			<p><img alt="" src="<?php echo admin_url( 'images/about-search-2x.png' ); ?>" /><?php _e( 'Search results are now ordered by how well the search query matches a post, instead of ordered only by date. For example, when your search terms match a post title, that result will be pushed to the top.' ); ?></p>
		</div>
		<div class="last-feature">
			<h3><?php _e( 'Better Global Support' ); ?></h3>
			<p><img alt="" src="<?php echo admin_url( 'images/about-globe-2x.png' ); ?>" /><?php _e( 'Localized versions of WordPress will receive faster and more complete translations. WordPress 3.7 adds support for automatically installing the right language files and keeping them up to date.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section col three-col">
		<div>
			<h4><?php _e( 'More Background Updates (Experimental)' ); ?></h4>
			<p><?php _e( 'Want WordPress to always update automatically, even for major feature releases? Want to always keep a certain plugin up to date in the background? WordPress 3.7 comes with fine-grained update controls for developers and systems administrators.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Advanced Date Queries' ); ?></h4>
			<p><?php _e( 'Developers can now query for posts within a date range, or that are older than or newer than a specific point in time. Or get really fancy: all posts written on Friday afternoons? Not&nbsp;a&nbsp;problem.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Multisite Improvements' ); ?></h4>
			<p><?php _e( '<code>wp_get_sites()</code> allows developers to easily get an array of all the sites on your network without resorting to a direct database query &mdash; just one of many improvements to multisite in WordPress 3.7.' ); ?></p>
		</div>
</div>

<div class="return-to-dashboard">
	<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
	<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>"><?php
		is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' );
	?></a> |
	<?php endif; ?>
	<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php
		is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
</div>

</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

_n_noop( 'Maintenance Release', 'Maintenance Releases' );
_n_noop( 'Security Release', 'Security Releases' );
_n_noop( 'Maintenance and Security Release', 'Maintenance and Security Releases' );

/* translators: 1: WordPress version number. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue.',
         '<strong>Version %1$s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

__( 'For more information, see <a href="%s">the release notes</a>.' );
