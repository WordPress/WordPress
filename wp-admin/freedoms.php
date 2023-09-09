<?php
/**
 * Your Rights administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// This file was used to also display the Privacy tab on the About screen from 4.9.6 until 5.3.0.
if ( isset( $_GET['privacy-notice'] ) ) {
	wp_redirect( admin_url( 'privacy.php' ), 301 );
	exit;
}

// Used in the HTML title tag.
$title = __( 'Freedoms' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

<<<<<<< main
require_once ABSPATH . 'wp-admin/admin-header.php';
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-title">
			<h1>
				<?php _e( 'The Four Freedoms' ); ?>
			</h1>
		</div>

		<div class="about__header-text">
			<?php _e( 'WordPress is free and open source software' ); ?>
		</div>
	</div>

	<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
		<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
		<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
		<a href="freedoms.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Freedoms' ); ?></a>
		<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		<a href="contribute.php" class="nav-tab"><?php _e( 'Get Involved' ); ?></a>
	</nav>

	<div class="about__section is-feature">
		<p class="about-description">
		<?php
		printf(
			/* translators: %s: https://wordpress.org/about/license/ */
			__( 'WordPress comes with some awesome, worldview-changing rights courtesy of its <a href="%s">license</a>, the GPL.' ),
			__( 'https://wordpress.org/about/license/' )
		);
		?>
		</p>
	</div>

	<div class="about__section has-2-columns">
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-1.svg?ver=6.3' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( 'The 1st Freedom' ); ?></h2>
			<p><?php _e( 'To run the program for any purpose.' ); ?></p>
		</div>
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-2.svg?ver=6.3' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( 'The 2nd Freedom' ); ?></h2>
			<p><?php _e( 'To study how the program works and change it to make it do what you wish.' ); ?></p>
		</div>
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-3.svg?ver=6.3' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( 'The 3rd Freedom' ); ?></h2>
			<p><?php _e( 'To redistribute.' ); ?></p>
		</div>
		<div class="column aligncenter">
			<img class="freedom-image" src="<?php echo esc_url( admin_url( 'images/freedom-4.svg?ver=6.3' ) ); ?>" alt="" />
			<h2 class="is-smaller-heading"><?php _e( 'The 4th Freedom' ); ?></h2>
=======
include( ABSPATH . 'wp-admin/admin-header.php' );

$is_privacy_notice = isset( $_GET['privacy-notice'] );

?>
<div class="wrap about-wrap full-width-layout">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s introduces a robust new content creation experience.' ), $display_version ); ?></p>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper wp-clearfix">
	<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
	<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
	<a href="freedoms.php" class="nav-tab<?php if ( ! $is_privacy_notice ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Freedoms' ); ?></a>
	<a href="freedoms.php?privacy-notice" class="nav-tab<?php if ( $is_privacy_notice ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Privacy' ); ?></a>
</h2>

<?php if ( $is_privacy_notice ) : ?>

<div class="about-wrap-content">
	<p class="about-description"><?php _e( 'From time to time, your WordPress site may send data to WordPress.org &#8212; including, but not limited to &#8212; the version of WordPress you are using, and a list of installed plugins and themes.' ); ?></p>

	<p><?php printf( __( 'This data is used to provide general enhancements to WordPress, which includes helping to protect your site by finding and automatically installing new updates. It is also used to calculate statistics, such as those shown on the <a href="%s">WordPress.org stats page</a>.' ), 'https://wordpress.org/about/stats/' ); ?></p>

	<p><?php printf( __( 'We take privacy and transparency very seriously. To learn more about what data we collect, and how we use it, please visit <a href="%s">WordPress.org/about/privacy</a>.' ), 'https://wordpress.org/about/privacy/' ); ?></p>
</div>

<?php else : ?>
<div class="about-wrap-content">
	<div class="feature-section one-col">
		<div class="col">
			<h2><?php _e( 'Freedoms' ); ?></h2>
			<p class="about-description"><?php printf( __( 'WordPress is Free and open source software, built by a distributed community of mostly volunteer developers from around the world. WordPress comes with some awesome, worldview-changing rights courtesy of its <a href="%s">license</a>, the GPL.' ), 'https://wordpress.org/about/license/' ); ?></p>
		</div>
	</div>

	<div class="feature-section four-col">
		<div class="col">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 1st Freedom' ); ?></h3>
			<p><?php _e( 'To run the program for any purpose.' ); ?></p>
		</div>
		<div class="col">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 2nd Freedom' ); ?></h3>
			<p><?php _e( 'To study how the program works and change it to make it do what you wish.' ); ?></p>
		</div>
		<div class="col">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 3rd Freedom' ); ?></h3>
			<p><?php _e( 'To redistribute.' ); ?></p>
		</div>
		<div class="col">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 4th Freedom' ); ?></h3>
>>>>>>> upstream/5.0-branch
			<p><?php _e( 'To distribute copies of your modified versions to others.' ); ?></p>
		</div>
	</div>

<<<<<<< main
	<div class="about__section has-1-column">
		<div class="column">
			<p>
			<?php
			printf(
				/* translators: %s: https://wordpressfoundation.org/trademark-policy/ */
				__( 'WordPress grows when people like you tell their friends about it, and the thousands of businesses and services that are built on and around WordPress share that fact with their users. We are flattered every time someone spreads the good word, just make sure to <a href="%s">check out our trademark guidelines</a> first.' ),
				'https://wordpressfoundation.org/trademark-policy/'
			);
			?>
			</p>

			<p>
			<?php
			$plugins_url = current_user_can( 'activate_plugins' ) ? admin_url( 'plugins.php' ) : __( 'https://wordpress.org/plugins/' );
			$themes_url  = current_user_can( 'switch_themes' ) ? admin_url( 'themes.php' ) : __( 'https://wordpress.org/themes/' );
			printf(
				/* translators: 1: URL to Plugins screen, 2: URL to Themes screen, 3: https://wordpress.org/about/license/ */
				__( 'Every plugin and theme in WordPress.org&#8217;s directory is 100%% GPL or a similarly free and compatible license, so you can feel safe finding <a href="%1$s">plugins</a> and <a href="%2$s">themes</a> there. If you get a plugin or theme from another source, make sure to <a href="%3$s">ask them if it&#8217;s GPL</a> first. If they do not respect the WordPress license, it is not recommended to use them.' ),
				$plugins_url,
				$themes_url,
				__( 'https://wordpress.org/about/license/' )
			);
			?>
			</p>
		</div>
	</div>
=======
	<div class="feature-section one-col">
		<div class="col">
			<p><?php printf( __( 'WordPress grows when people like you tell their friends about it, and the thousands of businesses and services that are built on and around WordPress share that fact with their users. We&#8217;re flattered every time someone spreads the good word, just make sure to <a href="%s">check out our trademark guidelines</a> first.' ), 'https://wordpressfoundation.org/trademark-policy/' ); ?></p>

			<p><?php

			$plugins_url = current_user_can( 'activate_plugins' ) ? admin_url( 'plugins.php' ) : __( 'https://wordpress.org/plugins/' );
			$themes_url = current_user_can( 'switch_themes' ) ? admin_url( 'themes.php' ) : __( 'https://wordpress.org/themes/' );

			printf( __( 'Every plugin and theme in WordPress.org&#8217;s directory is 100%% GPL or a similarly free and compatible license, so you can feel safe finding <a href="%1$s">plugins</a> and <a href="%2$s">themes</a> there. If you get a plugin or theme from another source, make sure to <a href="%3$s">ask them if it&#8217;s GPL</a> first. If they don&#8217;t respect the WordPress license, we don&#8217;t recommend them.' ), $plugins_url, $themes_url, 'https://wordpress.org/about/license/' ); ?></p>

			<p><?php _e( 'Don&#8217;t you wish all software came with these freedoms? So do we! For more information, check out the <a href="https://www.fsf.org/">Free Software Foundation</a>.' ); ?></p>
		</div>
	</div>
</div>
>>>>>>> upstream/5.0-branch

<?php endif; ?>
</div>
<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
