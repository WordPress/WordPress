<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( './admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s!' ), $display_version ); ?></h1>

<div class="about-text"><?php _e( 'WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.' ); ?></div>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper">
	<a href="about.php" class="nav-tab nav-tab-active">
		<?php printf( __( 'What&#8217;s New in %s' ), $display_version ); ?>
	</a><a href="credits.php" class="nav-tab">
		<?php _e( 'Credits' ); ?>
	</a><a href="freedoms.php" class="nav-tab">
		<?php _e( 'Freedoms' ); ?>
	</a>
</h2>

<div class="changelog">
	<h3><?php _e('For Users'); ?></h3>

	<div class="feature-section angled-left">
		<div class="left-feature">
			<h4><?php echo ( 'Drag-and-Drop Media Uploader' ); ?></h4>
			<p><?php echo ( 'Add your media by simply dragging and dropping files from your computer into the new WordPress media uploader.' ); ?></p>
		</div>
		<img class="placeholder" />
		<div class="right-feature">
			<h4><?php echo ( 'A Responsive Admin' ); ?></h4>
			<p><?php echo ( 'The WordPress admin now responds and adjusts to more devices and screen resolutions for a better native experience.' ); ?></p>
		</div>
	</div>
	<div class="feature-section angled-right">
		<div class="left-feature">
			<h4><?php echo ( 'New-user Experience' ); ?></h4>
			<p><?php echo ( 'New users get a helping hand. Updates come with this handy summary of what&#8217;s new in this version of WordPress.' ); ?></p>
		</div>
		<img class="placeholder" />
		<div class="right-feature">
			<h4><?php echo ( 'A New and Improved Admin Bar' ); ?></h4>
			<p><?php echo ( 'Get to the most useful areas of your dashboard from anywhere on your site quicker and easier than ever with a better organized admin bar.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e('Under the Hood'); ?></h3>

	<div class="feature-section">
		<div class="left-feature">
			<h4><?php echo ( 'Flexible Permalinks' ); ?></h4>
			<p><?php echo ( 'You have more freedom when choosing a post permalink structure. Skip the date information or add a category slug with no performance penalty!' ); ?></p>
		</div>
		<div class="right-feature">
			<h4><?php echo ( 'WP_Screen API' ); ?></h4>
			<p><?php echo ( 'WordPress admin screens have a nice new API that gives you the ability to create screens with help documentation, settings, and more.' ); ?></p>
		</div>
	</div>
</div>

<div class="return-to-dashboard">
	<a href="<?php echo admin_url(); ?>"><?php _e('Go to the Dashboard'); ?></a>
</div>

</div>
<?php

include( './admin-footer.php' );
