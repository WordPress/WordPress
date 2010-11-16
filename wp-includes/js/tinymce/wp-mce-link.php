<?php

require_once '../../../wp-load.php';
include './wp-mce-link-includes.php';

header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_bloginfo('charset'); ?>" />
<title><?php _e( 'Insert/edit link' ); ?></title>
<script type="text/javascript">
//<![CDATA[
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>',
	wpLinkL10n = {
		untitled : '<?php _e('Untitled'); ?>',
		noMatchesFound : '<?php _e( 'No matches found.' ); ?>'
	};
//]]>
</script>
<script type="text/javascript" src="tiny_mce_popup.js?ver=3223"></script>
<?php
wp_print_scripts( array( 'jquery', 'jquery-ui-widget' ) );
$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';
$src = "plugins/wplink/js/wplink$suffix.js?ver=20101115";
?>
<script type="text/javascript" src="<?php echo $src; ?>"></script>
<?php
wp_admin_css( 'global', true );
wp_admin_css( 'wp-admin', true );
register_admin_color_schemes();
wp_admin_css( 'colors', true );
?>
<style>
html {
	background: #f1f1f1;
}
a:link, a:visited {
	color: #21759b;
}
p.howto {
	margin: 3px;
}
#link-options {
	padding: 10px 0 14px;
	border-bottom: 1px solid #dfdfdf;
	margin: 0 6px 14px;
}
label input[type="text"] {
	width: 360px;
	margin-top: 5px;
}
label span {
	display: inline-block;
	width: 80px;
	text-align: right;
	padding-right: 5px;
}
.link-search-wrapper {
	margin: 5px 5px 9px;
	display: block;
	overflow: hidden;
}
.link-search-wrapper span {
	float: left;
	margin-top: 6px;
}
.link-search-wrapper input[type="text"] {
	float: left;
	width: 220px;
}
img.waiting {
	margin: 8px 1px 0 4px;
	float: left;
	display: none;
}
#open-in-new-tab {
	display: inline-block;
	padding: 3px 0 0;
	margin: 0 0 0 87px;
}
#open-in-new-tab span {
	width: auto;
	margin-left: 6px;
	font-size: 11px;
}
.query-results {
	border: 1px #dfdfdf solid;
	margin: 0 5px 5px;
	background: #fff;
	height: 185px;
	overflow: auto;
}
.query-results li {
	margin-bottom: 0;
	border-bottom: 1px solid #f1f1f1;
	color: #555;
	padding: 4px 6px;
	cursor: pointer;
}
.query-results li:hover {
	background: #eaf2fa;
	color: #333;
}
.query-results li.unselectable:hover {
	background: #fff;
	cursor: auto;
	color: #555;
}
.query-results li.unselectable {
	border-bottom: 1px solid #dfdfdf;
}
.query-results li.selected {
	background: #f1f1f1;
	color: #333;
}
.query-results li.selected .item-title {
	font-weight: bold;
}
.item-info {
	text-transform: uppercase;
	color: #aaa;
	font-size: 11px;
	float: right;
}
#search-results {
	display: none;
}
.submitbox {
	padding: 5px 5px 0;
	font-size: 11px;
	overflow: auto;
	height: 29px;
}
#wp-cancel {
	line-height: 25px;
	float: left;
}
#wp-update {
	line-height: 23px;
	float: right;
}
#wp-update a {
	display: inline-block;
}
</style>
</head>
<body id="post-body">
<div id="link-selector">
	<div id="link-options">
		<p class="howto"><?php _e( 'Enter the destination URL:' ); ?></p>
		<label for="url-field">
			<span><?php _e( 'URL' ); ?></span><input id="url-field" type="text" />
		</label>
		<label for="link-title-field">
			<span><?php _e( 'Title' ); ?></span><input id="link-title-field" type="text" />
		</label>
		<label for="link-target-checkbox" id="open-in-new-tab">
			<input type="checkbox" id="link-target-checkbox" /><span><?php _e( 'Open in new tab' ); ?></span>
		</label>
	</div>
	<div id="search-panel">
		<div class="link-search-wrapper">
			<p class="howto"><?php _e( 'Or, link to existing site content:' ); ?></p>
			<label for="search-field">
				<span><?php _e( 'Search' ); ?></span>
				<input type="text" id="search-field" class="link-search-field" />
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</label>
		</div>
		<div id="search-results" class="query-results">
			<ul>
				<li class="wp-results-loading unselectable"><em><?php _e( 'Loading...' ); ?></em></li>
			</ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
		<?php $most_recent = wp_link_query(); ?>
		<div id="most-recent-results" class="query-results">
			<ul>
				<li class="unselectable"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></li>
				<?php foreach ( $most_recent['results'] as $item ) : ?>
					<li>
						<input type="hidden" class="item-permalink" value="<?php echo esc_url( $item['permalink'] ); ?>" />
						<span class="item-title"><?php echo $item['title']; ?></span>
						<span class="item-info"><?php echo esc_html( $item['info'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
	</div>
</div>
<div class="submitbox">
	<div id="wp-cancel">
		<a class="submitdelete deletion"><?php _e( 'Cancel' ); ?></a>
	</div>
	<div id="wp-update">
		<a class="button-primary"><?php _e( 'Update' ); ?></a>
	</div>
</div>
</body>
</html>