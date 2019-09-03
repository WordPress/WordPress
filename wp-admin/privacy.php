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
	<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
	<a href="privacy.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Privacy' ); ?></a>
</nav>

<div class="about-wrap-content">
	<p class="about-description"><?php _e( 'From time to time, your WordPress site may send data to WordPress.org &#8212; including, but not limited to &#8212; the version of WordPress you are using, and a list of installed plugins and themes.' ); ?></p>

	<p>
		<?php
		printf(
			/* translators: %s: https://wordpress.org/about/stats/ */
			__( 'This data is used to provide general enhancements to WordPress, which includes helping to protect your site by finding and automatically installing new updates. It is also used to calculate statistics, such as those shown on the <a href="%s">WordPress.org stats page</a>.' ),
			__( 'https://wordpress.org/about/stats/' )
		);
		?>
	</p>

	<p>
		<?php
		printf(
			/* translators: %s: https://wordpress.org/about/privacy/ */
			__( 'We take privacy and transparency very seriously. To learn more about what data we collect, and how we use it, please visit <a href="%s">WordPress.org/about/privacy</a>.' ),
			__( 'https://wordpress.org/about/privacy/' )
		);
		?>
	</p>
</div>

</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
