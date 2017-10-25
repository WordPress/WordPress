<?php
/**
 * Privacy administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'Privacy' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s helps you get your site set up the way you want it.' ), $display_version ); ?></p>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper wp-clearfix">
	<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
	<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
	<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
	<a href="privacy.php" class="nav-tab nav-tab-active"><?php _e( 'Privacy' ); ?></a>
</h2>

<p class="about-description"><?php _e( 'From time to time, your WordPress site may send data to WordPress.org &#8212; including, but not limited to &#8212; the version of WordPress you are using, and a list of installed plugins and themes.' ); ?></p>

<p><?php printf( __( 'This data helps WordPress to protect your site by finding and automatically installing new updates, as well as other general enhancements. The data is also used to calculate statistics, such as those shown on the <a href="%s">WordPress.org stats page</a>.' ), 'https://wordpress.org/about/stats/' ); ?></p>

<p><?php printf( __( 'We take privacy and transparency very seriously. To learn more about what data we collect, and how we use it, please visit <a href="%s">WordPress.org/about/privacy</a>.' ), 'https://wordpress.org/about/privacy/' ); ?></p>

</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
