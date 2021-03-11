<?php
/**
 * Privacy administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

$title = __( 'Privacy' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-image">
			<img alt="<?php _e( 'Code is Poetry' ); ?>" src="<?php echo admin_url( 'images/about-badge.svg' ); ?>" />
		</div>

		<div class="about__header-container">
			<div class="about__header-title">
				<p>
					<?php _e( 'WordPress' ); ?>
					<?php echo $display_version; ?>
				</p>
			</div>

			<div class="about__header-text">
				<?php _e( 'Jazz up your stories in an editor that’s cleaner, crisper, and does more to get out of your way.' ); ?>
			</div>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Privacy' ); ?></a>
		</nav>
	</div>

	<div class="about__section">
		<div class="column">
			<h1><?php _e( 'Privacy' ); ?></h1>

			<p><img class="privacy-image" src="<?php echo esc_url( admin_url( 'images/privacy.png' ) ); ?>" alt="" /></p>

			<p><?php _e( 'From time to time, your WordPress site may send data to WordPress.org &#8212; including, but not limited to &#8212; the version of WordPress you are using, and a list of installed plugins and themes.' ); ?></p>

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
					__( 'We take privacy and transparency very seriously. To learn more about what data we collect, and how we use it, please visit <a href="%s">our Privacy Policy</a>.' ),
					__( 'https://wordpress.org/about/privacy/' )
				);
				?>
			</p>
		</div>
	</div>

</div>
<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
