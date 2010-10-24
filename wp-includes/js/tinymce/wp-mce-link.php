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
<script type="text/javascript" src="plugins/wplink/js/wplink.js?ver=20101023"></script>
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

#link-header,
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
	width: 220px;
}
	.wp-tab-panel label input[type="text"] {
		float: left;
		width: 200px;
	}

label span {
	display: inline-block;
	width: 80px;
	text-align: right;
	padding-right: 5px;
}
	.wp-tab-panel label span {
		width: auto;
		text-align: left;
		float: left;
		margin-top: 3px;
	}
	.link-search-wrapper {
		padding: 5px;
		border-bottom: solid 1px #dfdfdf;
		display: block;
		overflow: hidden;
	}
		.link-search-wrapper img.waiting {
			margin: 4px 1px 0 4px;
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

.submitbox {
	padding: 5px;
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
.wp-tab-active,
.wp-tab-panel {
	background: #fff;
}
	.wp-tab-panel {
		height: 160px;
		padding: 0;
	}
.wp-tab-panel li {
	margin-bottom: 0;
	border-bottom: 1px solid #dfdfdf;
	color: #555;
	padding: 4px 6px;
	cursor: pointer;
}
	.wp-tab-panel li:hover {
		background: #EAF2FA;
		color: #333;
	}
	.wp-tab-panel li.selected {
		background: #f1f1f1;
		font-weight: bold;
		color: #333;
	}
.wp-tab-panel-pagelinks {
	display: none;
	padding:4px 0;
	margin:0 auto;
	text-align:center;
}
	.wp-tab-panel-pagelinks-top {
		border-bottom: 1px solid #dfdfdf;
	}
</style>
</head>
<?php

$pts = get_post_types( array( 'public' => true ), 'objects' );
$queries = array(
	array( 'preset' => 'all', 'label' => __('View All') ),
	array( 'preset' => 'recent', 'label' => __('Most Recent') ),
	array( 'preset' => 'search', 'label' => __('Search') )
);

?>
<body id="post-body">
<div id="link-header">
	<label for="link-type">
		<span><strong><?php _e('Link Type:'); ?></strong>
		</span><select id="link-type">
			<option id="link-option-id-custom" class="link-custom"><?php _e('External Link'); ?></option>
		<?php
		foreach ( $pts as $pt ) {
			echo "<option id='link-option-id-pt-$pt->name' class='link-option-pt'>";
			echo $pt->labels->singular_name . '</option>';
		} ?>
		</select>
	</label>
</div>
<div id="link-selector">
	<?php
	wp_link_panel_custom();
	foreach( $pts as $pt )
		wp_link_panel_structure('pt', $pt->name, $queries);
	?>
	<div id="link-options">
		<label for="link-title-field">
			<span><?php _e('Description:'); ?></span><input id="link-title-field" type="text" />
		</label>
		<label for="link-target-checkbox" id="open-in-new-tab">
			<input type="checkbox" id="link-target-checkbox" /><span><?php _e('Open in new tab'); ?></span>
		</label>
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
