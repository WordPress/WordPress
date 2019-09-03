<?php
/**
 * Your Rights administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

// This file was used to also display the Privacy tab on the About screen from 4.9.6 until 5.3.0.
if ( isset( $_GET['privacy-notice'] ) ) {
	wp_redirect( admin_url( 'privacy.php' ), 301 );
	exit;
}

$title = __( 'Freedoms' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );

?>
<div class="wrap about-wrap full-width-layout">

<h1>
	<?php
	printf(
		/* translators: %s: The current WordPress version number. */
		__( 'Welcome to WordPress&nbsp;%s' ),
		$display_version
	);
	?>
</h1>

<p class="about-text">
	<?php
	printf(
		/* translators: %s: The current WordPress version number. */
		__( 'Congratulations on updating to WordPress %s! This update makes it easier than ever to fix your site if something goes wrong.' ),
		$display_version
	);
	?>
</p>

<div class="wp-badge">
	<?php
	printf(
		/* translators: %s: The current WordPress version number. */
		__( 'Version %s' ),
		$display_version
	);
	?>
</div>

<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
	<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
	<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
	<a href="freedoms.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Freedoms' ); ?></a>
	<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
</nav>

<div class="about-wrap-content">
	<div class="feature-section has-1-columns">
		<h2><?php _e( 'Freedoms' ); ?></h2>
		<p class="about-description">
		<?php
		printf(
			/* translators: %s: https://wordpress.org/about/license/ */
			__( 'WordPress is Free and open source software, built by a distributed community of mostly volunteer developers from around the world. WordPress comes with some awesome, worldview-changing rights courtesy of its <a href="%s">license</a>, the GPL.' ),
			__( 'https://wordpress.org/about/license/' )
		);
		?>
		</p>
	</div>

	<div class="feature-section has-4-columns is-fullwidth">
		<div class="column">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 1st Freedom' ); ?></h3>
			<p><?php _e( 'To run the program for any purpose.' ); ?></p>
		</div>
		<div class="column">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 2nd Freedom' ); ?></h3>
			<p><?php _e( 'To study how the program works and change it to make it do what you wish.' ); ?></p>
		</div>
		<div class="column">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 3rd Freedom' ); ?></h3>
			<p><?php _e( 'To redistribute.' ); ?></p>
		</div>
		<div class="column">
			<div class="freedoms-image"></div>
			<h3><?php _e( 'The 4th Freedom' ); ?></h3>
			<p><?php _e( 'To distribute copies of your modified versions to others.' ); ?></p>
		</div>
	</div>

	<div class="feature-section has-1-columns">
		<p>
		<?php
		printf(
			/* translators: %s: https://wordpressfoundation.org/trademark-policy/ */
			__( 'WordPress grows when people like you tell their friends about it, and the thousands of businesses and services that are built on and around WordPress share that fact with their users. We&#8217;re flattered every time someone spreads the good word, just make sure to <a href="%s">check out our trademark guidelines</a> first.' ),
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
			__( 'Every plugin and theme in WordPress.org&#8217;s directory is 100%% GPL or a similarly free and compatible license, so you can feel safe finding <a href="%1$s">plugins</a> and <a href="%2$s">themes</a> there. If you get a plugin or theme from another source, make sure to <a href="%3$s">ask them if it&#8217;s GPL</a> first. If they don&#8217;t respect the WordPress license, we don&#8217;t recommend them.' ),
			$plugins_url,
			$themes_url,
			__( 'https://wordpress.org/about/license/' )
		);
		?>
		</p>

		<p><?php _e( 'Don&#8217;t you wish all software came with these freedoms? So do we! For more information, check out the <a href="https://www.fsf.org/">Free Software Foundation</a>.' ); ?></p>
	</div>
</div>

</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
