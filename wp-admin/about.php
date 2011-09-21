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
		.return-to-dashboard {
			margin: 16px 0 0;
			font-size: 14px;
		}
		.return-to-dashboard a {
			text-decoration: none;
		}
		.wrap h2 {
			margin-top: 0.6em;
			font-size: 3.6em;
			text-shadow: 1px 1px 3px rgba( 0, 0, 0, 0.2 );
		}
		.about-text {
			font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
			font-weight: normal;
			font-size: 1.8em;
			color: #777;
			margin: 1.4em 0;
			line-height: 1.4em;
			max-width: 600px;
		}
		.changelog {
			font-size: 14px;
			padding-bottom: 10px;
		}
		.changelog h3 {
			font-size: 18px;
		}
		.changelog li {
			list-style-type: disc;
			margin-left: 3em;
		}
	</style>
<?php }

include( './admin-header.php' );
?>
<div class="wrap">

<div class="return-to-dashboard">
	<a href="<?php echo admin_url(); ?>"><?php _e('&larr; Return to dashboard'); ?></a>
</div>
<h2><?php _e( 'Welcome to WordPress 3.3!' ); ?></h2>

<div class="about-text">WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</div>

<div class="changelog">
	<h3><?php _e('For Users'); ?></h3>
	<ul>
		<li>Media uploader</li>
		<li>New-user experience</li>
		<li>Improved admin bar</li>
		<li>Responsive admin</li>
	</ul>
</div>

<div class="changelog">
	<h3><?php _e('For Developers'); ?></h3>
	<ul>
		<li>Meta API improvements</li>
		<li>Language packs</li>
		<li>Permalink performance</li>
		<li>Nav menus performance</li>
	</ul>
</div>


</div>
<?php

include( './admin-footer.php' );
