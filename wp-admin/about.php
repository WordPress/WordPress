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
$parent_file = 'index.php';

add_action( 'admin_head', '_wp_about_add_css' );
function _wp_about_add_css() { ?>
	<style type="text/css">
		.about-wrap {
			position: relative;
			margin: 44px 40px 0 20px;
		}
		.about-wrap h1 {
			margin: 0.6em 200px 0 0;
			line-height: 1.2em;
			font-size: 3.6em;
			font-weight: 200;

			color: #333;
			text-shadow: 1px 1px 1px white;
		}
		.about-text {
			font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
			font-weight: normal;
			font-size: 24px;
			color: #777;
			margin: 1em 200px 1.4em 0;
			line-height: 1.6em;
			min-height: 60px;
		}
		.about-wrap h2.nav-tab-wrapper {
			padding-left: 6px;
		}
		.wp-badge {
			padding-top: 142px;
			height: 37px;
			width: 160px;

			position: absolute;
			top: 0;
			right: 0;

			color: #fff;
			font-weight: bold;
			font-size: 14px;
			text-shadow: 0 -1px 0 #0c3d57;
			text-align: center;

			border: 1px solid #2B5173;
			-webkit-border-radius: 3px;
			border-radius: 3px;

			-moz-box-shadow: inset 0 0 0 1px #5F8CA8;
			-webkit-box-shadow: inset 0 0 0 1px #5F8CA8;
			box-shadow: inset 0 0 0 1px #5F8CA8;

			background-color: #378aac;
			background-repeat: no-repeat;
			background-position: center 20px;
			background-image: url(images/wp-badge.png);
			background-image: url(images/wp-badge.png), -ms-linear-gradient(top, #378aac, #165d84); /* IE10 */
			background-image: url(images/wp-badge.png), -moz-linear-gradient(top, #378aac, #165d84); /* Firefox */
			background-image: url(images/wp-badge.png), -o-linear-gradient(top, #378aac, #165d84); /* Opera */
			background-image: url(images/wp-badge.png), -webkit-gradient(linear, left top, left bottom, from(#378aac), to(#165d84)); /* old Webkit */
			background-image: url(images/wp-badge.png), -webkit-linear-gradient(top, #378aac, #165d84); /* new Webkit */
			background-image: url(images/wp-badge.png), linear-gradient(top, #378aac, #165d84); /* proposed W3C Markup */
		}

		.about-wrap h2 .nav-tab {
			color: #21759B;
			padding: 4px 10px 6px;
			margin: 0 3px -1px 0;
			font-size: 18px;
		}
		.about-wrap h2 .nav-tab:hover {
			color: #d54e21;
		}

		.about-wrap h2 .nav-tab-active,
		.about-wrap h2 .nav-tab-active:hover {
			color: #333;
		}
		.about-wrap h2 .nav-tab-active {
			font-weight: bold;
			text-shadow: 1px 1px 1px white;
			color: #464646;
			padding-top: 3px;
		}

		.about-wrap h3 {
			font-size: 24px;
			color: #333;
			text-shadow: 1px 1px 1px white;
			margin-bottom: 0.4em;
			padding-top: 20px;
		}

		.about-wrap .changelog {
			font-size: 14px;
			padding-bottom: 10px;
			overflow: hidden;
		}
		.about-wrap .changelog li {
			list-style-type: disc;
			margin-left: 3em;
		}

		.about-wrap .feature-section .left-feature,
		.about-wrap .feature-section img,
		.about-wrap .feature-section .right-feature {
			float: left;
		}

		.about-wrap .feature-section {
			min-height: 100px;
			overflow: hidden;
		}

		.about-wrap .feature-section.angled-right {
			margin-top: -35px;
			padding-top: 15px;
		}
		.about-wrap .feature-section.angled-right img {
			margin-top: 0;
		}
		.about-wrap .feature-section.angled-right .left-feature {
			margin-top: 15px;
		}

		.about-wrap .feature-section h4 {
			color: #464646;
			margin-bottom: 0.6em;
		}
		.about-wrap .feature-section p {
			line-height: 1.6em;
			margin-top: 0.6em;
		}

		.about-wrap .feature-section .left-feature,
		.about-wrap .feature-section .right-feature {
			width: 40%;
		}
		.about-wrap .feature-section .left-feature {
			margin-right: 10%;
		}
		.about-wrap .feature-section .right-feature {
			margin-left: 9%;
		}
		.about-wrap .feature-section.angled-left .left-feature,
		.about-wrap .feature-section.angled-right .left-feature {
			margin-right: 5%;
		}
		.about-wrap .feature-section.angled-left .right-feature,
		.about-wrap .feature-section.angled-right .right-feature {
			margin-left: 5%;
		}
		.about-wrap .feature-section img {
			width: 19%;
			height: 130px;
			background: #f9f9f9;
			margin-top: 15px;

			border: 1px solid #dfdfdf;
			-webkit-border-radius: 3px;
			border-radius: 3px;

			-moz-box-shadow: 0 0 6px rgba( 0, 0, 0, 0.3 );
			-webkit-box-shadow: 0 0 6px rgba( 0, 0, 0, 0.3 );
			box-shadow: 0 0 6px rgba( 0, 0, 0, 0.3 );
		}
		.about-wrap .feature-section.angled-left .left-feature {
			width: 30%;
		}
		.about-wrap .feature-section.angled-left .right-feature {
			width: 40%;
		}
		.about-wrap .feature-section.angled-right .left-feature {
			width: 40%;
		}
		.about-wrap .feature-section.angled-right .right-feature {
			width: 30%;
		}

		.about-wrap .return-to-dashboard {
			margin: 16px 0 0;
			font-size: 14px;
			font-weight: bold;
		}
		.about-wrap .return-to-dashboard a {
			text-decoration: none;
		}
	</style>
<?php }

include( './admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php _e( 'Welcome to WordPress 3.3!' ); ?></h1>

<div class="about-text"><?php _e( 'WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.' ); ?></div>

<div class="wp-badge"><?php _e( 'Version 3.3' ); ?></div>

<h2 class="nav-tab-wrapper">
	<a href="about.php" class="nav-tab nav-tab-active">
		<?php _e( 'What&#8217;s New in 3.3' ); ?>
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
			<h4><?php _e( 'Drag-and-Drop Media Uploader' ); ?></h4>
			<p><?php _e( 'Add your media by simply dragging and dropping files from your computer into the new WordPress media uploader.' ); ?></p>
		</div>
		<img class="placeholder" />
		<div class="right-feature">
			<h4><?php _e( 'A Responsive Admin' ); ?></h4>
			<p><?php _e( 'The WordPress admin now responds and adjusts to more devices and screen resolutions for a better native experience.' ); ?></p>
		</div>
	</div>
	<div class="feature-section angled-right">
		<div class="left-feature">
			<h4><?php _e( 'New-user Experience' ); ?></h4>
			<p><?php _e( 'New users get a helping hand. Updates come with this handy summary of what&#8217;s new in this version of WordPress.' ); ?></p>
		</div>
		<img class="placeholder" />
		<div class="right-feature">
			<h4><?php _e( 'A New and Improved Admin Bar' ); ?></h4>
			<p><?php _e( 'Get to the most useful areas of your dashboard form anywhere on your site quicker and easier than ever with a better organized admin bar.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e('For Developers'); ?></h3>

	<div class="feature-section">
		<div class="left-feature">
			<h4><?php _e( 'Performance Enhancements' ); ?></h4>
			<p><?php _e( 'Add your media by simply dragging and dropping files from your computer into the new WordPress media uploader.' ); ?></p>
		</div>
		<div class="right-feature">
			<h4><?php _e( 'API: Settings Improvements' ); ?></h4>
			<p><?php _e( 'The WordPress admin now responds and adjusts to more devices and screen resolutions for a better native experience.' ); ?></p>
		</div>
	</div>
	<div class="feature-section">
		<div class="left-feature">
			<h4><?php _e( 'More Efficient Updates and Upgrades' ); ?></h4>
			<p><?php _e( 'New users get a helping hand. Updates come with this handy summary of what&#8217;s new in this version of WordPress.' ); ?></p>
		</div>
		<div class="right-feature">
			<h4><?php _e( 'API: Meta Improvements' ); ?></h4>
			<p><?php _e( 'Get to the most useful areas of your dashboard form anywhere on your site quicker and easier than ever with a better organized admin bar.' ); ?></p>
		</div>
	</div>
</div>

<div class="return-to-dashboard">
	<a href="<?php echo admin_url(); ?>"><?php _e('Go to the Dashboard'); ?></a>
</div>

</div>
<?php

include( './admin-footer.php' );
