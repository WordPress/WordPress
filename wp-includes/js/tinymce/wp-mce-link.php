<?php

require_once '../../../wp-load.php';
include './wp-mce-link-includes.php';

header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_bloginfo('charset'); ?>" />
<title><?php _e('Insert/edit link') ?></title>
<script type="text/javascript">
//<![CDATA[
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>',
	wpLinkL10n = {
		untitled : '<?php _e('Untitled'); ?>',
		noMatchesFound : '<?php _e('No matches found.'); ?>'
	};
//]]>
</script>
<script type="text/javascript" src="tiny_mce_popup.js?ver=3223"></script>
<?php
wp_print_scripts( array('jquery', 'jquery-ui-widget') );
?>
<?php
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
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
html, body {
	background: #f1f1f1;
}

a:link, a:visited {
	color: #21759B;
}

select {
	height: 2em;
}

#link-options,
#link-advanced-options {
	padding: 5px;
	border-bottom: 1px solid #dfdfdf;
}

#link-type {
	width: 140px;
}

.link-panel {
	padding: 5px 5px 0;
	display: none;
}
	.link-panel-active {
		display: block;
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
		padding: 5px;
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
		.link-search-wrapper img.waiting {
			margin: 8px 1px 0 4px;
			float: left;
			display: none;
		}

#open-in-new-tab {
	padding-left: 87px;
}
	#open-in-new-tab span {
		width: auto;
		margin-left: 10px;
		font-size: 11px;
	}
	
.query-results {
	border: #dfdfdf solid;
	border-width: 1px 0;
	margin: 5px 0;
	background: #fff;
	height: 220px;
	overflow: auto;
}
	.query-results li {
		margin-bottom: 0;
		border-bottom: 1px solid #dfdfdf;
		color: #555;
		padding: 4px 6px;
		cursor: pointer;
	}
	.query-results li:hover {
		background: #EAF2FA;
		color: #333;
	}
	.query-results li.selected {
		background: #f1f1f1;
		font-weight: bold;
		color: #333;
	}
.item-info {
	text-transform: uppercase;
	color: #aaa;
	font-weight: bold;
	font-size: 11px;
	float: right;
}
#search-results {
	display: none;
}
	
.wp-results-pagelinks {
	padding:4px 0;
	margin:0 auto;
	text-align:center;
}
	.wp-results-pagelinks-top {
		border-bottom: 1px solid #dfdfdf;
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
<?php


?>
<body id="post-body">
<div id="link-selector">
	<div id="link-options">
		<label for="url-field">
			<span><?php _e('URL:'); ?></span><input id="url-field" type="text" />
		</label>
		<label for="link-title-field">
			<span><?php _e('Description:'); ?></span><input id="link-title-field" type="text" />
		</label>
		<label for="link-target-checkbox" id="open-in-new-tab">
			<input type="checkbox" id="link-target-checkbox" /><span><?php _e('Open in new tab'); ?></span>
		</label>
	</div>
	<div id="search-panel">
		<label for="search-field" class="link-search-wrapper">
			<span><?php _e('Search:'); ?></span>
			<input type="text" id="search-field" class="link-search-field" />
			<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
		</label>
		
		<div id="search-results" class="query-results">
			<div class="wp-results-pagelinks wp-results-pagelinks-top"></div>
			<ul>
				<li class="wp-results-loading unselectable"><em><?php _e('Loading...'); ?></em></li>
			</ul>
			<div class="wp-results-pagelinks wp-results-pagelinks-bottom"></div>
		</div>
		
		<?php $most_recent = wp_link_query(); ?>
		<div id="most-recent-results" class="query-results">
			<div class="wp-results-pagelinks wp-results-pagelinks-top">
				<?php echo $most_recent['pages']['page_links']; ?>
			</div>
			<ul>
				<?php foreach ( $most_recent['results'] as $item ): ?>
					<li>
						<input type="hidden" class="item-permalink" value="<?php echo esc_url( $item['permalink'] ); ?>" />
						<span class="item-title"><?php echo $item['title']; ?></span>
						<span class="item-info"><?php echo esc_html( $item['info'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="wp-results-pagelinks wp-results-pagelinks-bottom">
				<?php echo $most_recent['pages']['page_links']; ?>
			</div>
		</div>
	</div>
</div>
<div class="submitbox">
	<div id="wp-cancel">
		<a class="submitdelete deletion"><?php _e('Cancel'); ?></a>
	</div>
	<div id="wp-update">
		<a class="button-primary"><?php _e('Update'); ?></a>
	</div>
</div>
</body>
</html>
