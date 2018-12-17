<?php
/**
 * Your Rights administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'Freedoms' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );

$is_privacy_notice = isset( $_GET['privacy-notice'] );

if ( $is_privacy_notice ) {
	$freedoms_class = '';
	$privacy_class  = ' nav-tab-active';
} else {
	$freedoms_class = ' nav-tab-active';
	$privacy_class  = '';
}

?>
<div class="wrap about-wrap full-width-layout">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s introduces a robust new content creation experience.' ), $display_version ); ?></p>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper wp-clearfix">
	<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
	<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
	<a href="freedoms.php" class="nav-tab<?php echo $freedoms_class; ?>"><?php _e( 'Freedoms' ); ?></a>
	<a href="freedoms.php?privacy-notice" class="nav-tab<?php echo $privacy_class; ?>"><?php _e( 'Privacy' ); ?></a>
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
			<p><?php _e( 'To distribute copies of your modified versions to others.' ); ?></p>
		</div>
	</div>

	<div class="feature-section one-col">
		<div class="col">
			<p><?php printf( __( 'WordPress grows when people like you tell their friends about it, and the thousands of businesses and services that are built on and around WordPress share that fact with their users. We&#8217;re flattered every time someone spreads the good word, just make sure to <a href="%s">check out our trademark guidelines</a> first.' ), 'https://wordpressfoundation.org/trademark-policy/' ); ?></p>

			<p>
			<?php
				$plugins_url = current_user_can( 'activate_plugins' ) ? admin_url( 'plugins.php' ) : __( 'https://wordpress.org/plugins/' );
				$themes_url  = current_user_can( 'switch_themes' ) ? admin_url( 'themes.php' ) : __( 'https://wordpress.org/themes/' );

				printf( __( 'Every plugin and theme in WordPress.org&#8217;s directory is 100%% GPL or a similarly free and compatible license, so you can feel safe finding <a href="%1$s">plugins</a> and <a href="%2$s">themes</a> there. If you get a plugin or theme from another source, make sure to <a href="%3$s">ask them if it&#8217;s GPL</a> first. If they don&#8217;t respect the WordPress license, we don&#8217;t recommend them.' ), $plugins_url, $themes_url, 'https://wordpress.org/about/license/' );
			?>
			</p>

			<p><?php _e( 'Don&#8217;t you wish all software came with these freedoms? So do we! For more information, check out the <a href="https://www.fsf.org/">Free Software Foundation</a>.' ); ?></p>
		</div>
	</div>
</div>

<?php endif; ?>
</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
