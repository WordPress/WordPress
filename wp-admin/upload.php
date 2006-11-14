<?php
require_once('admin.php');

@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

wp_reset_vars(array('action', 'tab', 'from_tab', 'style', 'post_id', 'ID', 'paged', 'post_title', 'post_content', 'delete'));

require_once('upload-functions.php');
if ( !$tab )
	$tab = 'browse-all';

do_action( "upload_files_$tab" );

$pid = 0;
if ( $post_id < 0 )
	$pid = $post_id;
elseif ( get_post( $post_id ) )
	$pid = $post_id;
$wp_upload_tabs = array();
$all_atts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment'");
$post_atts = 0;

if ( $pid ) {
	// 0 => tab display name, 1 => required cap, 2 => function that produces tab content, 3 => total number objects OR array(total, objects per page), 4 => add_query_args
	$wp_upload_tabs['upload'] = array(__('Upload'), 'upload_files', 'wp_upload_tab_upload', 0);
	if ( $all_atts && $post_atts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '$post_id'") )
		$wp_upload_tabs['browse'] = array(__('Browse'), 'upload_files', "wp_upload_tab_browse", $action ? 0 : $post_atts);
	if ( $post_atts < $all_atts )
		$wp_upload_tabs['browse-all'] = array(__('Browse All'), 'upload_files', 'wp_upload_tab_browse', $action ? 0 : $all_atts);
} else
	$wp_upload_tabs['browse-all'] = array(__('Browse All'), 'upload_files', 'wp_upload_tab_browse', $action ? 0 : $all_atts);

	$wp_upload_tabs = array_merge($wp_upload_tabs, apply_filters( 'wp_upload_tabs', array() ));

if ( !is_callable($wp_upload_tabs[$tab][2]) ) {
	$to_tab = isset($wp_upload_tabs['upload']) ? 'upload' : 'browse-all';
	wp_redirect( add_query_arg( 'tab', $to_tab ) );
	exit;
}

foreach ( $wp_upload_tabs as $t => $tab_array ) {
	if ( !current_user_can( $tab_array[1] ) ) {
		unset($wp_upload_tabs[$t]);
		if ( $tab == $t )
			wp_die(__("You are not allowed to be here"));
	}
}

if ( 'inline' == $style ) : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; WordPress</title>
<link rel="stylesheet" href="<?php echo get_option('siteurl') ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<?php if ( ('rtl' == $wp_locale->text_direction) ) : ?>
<link rel="stylesheet" href="<?php echo get_option('siteurl') ?>/wp-admin/rtl.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<?php endif; ?> 
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<?php do_action('admin_print_scripts'); wp_upload_admin_head(); ?>
</head>
<body>
<?php
else :
	add_action( 'admin_head', 'wp_upload_admin_head' );
	include_once('admin-header.php');
	echo "<div class='wrap'>";
endif;

echo "<ul id='upload-menu'>\n";
foreach ( $wp_upload_tabs as $t => $tab_array ) { // We've already done the current_user_can check
	$href = add_query_arg( array('tab' => $t, 'ID' => '', 'action' => '', 'paged' => '') );
	if ( isset($tab_array[4]) && is_array($tab_array[4]) )
		add_query_arg( $tab_array[4], $href );
	$_href = wp_specialchars( $href, 1 );
	$page_links = '';
	$class = 'upload-tab alignleft';
	if ( $tab == $t ) {
		$class .= ' current';
		if ( $tab_array[3] ) {
			if ( is_array($tab_array[3]) ) {
				$total = $tab_array[3][0];
				$per = $tab_array[3][1];
			} else {
				$total = $tab_array[3];
				$per = 10;
			}
			$page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'total' => ceil($total / $per),
				'current' => $paged ? $paged : 1,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;'
			));
			if ( $page_links )
				$page_links = "<span id='current-tab-nav'>: $page_links</span>";
		}
	}

	echo "\t<li class='$class'><a href='$_href' class='upload-tab-link' title='{$tab_array[0]}'>{$tab_array[0]}</a>$page_links</li>\n";
}
unset($t, $tab_array, $href, $_href, $page_links, $total, $per, $class);
echo "</ul>\n\n";

echo "<div id='upload-content' class='$tab'>\n";

call_user_func( $wp_upload_tabs[$tab][2] );

echo "</div>\n";

if ( 'inline' != $style ) :
	echo "<div class='clear'></div></div>";
	include_once('admin-footer.php');
else : ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>

</body>
</html>
<?php endif; ?>
