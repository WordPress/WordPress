<?php /* Don't remove these lines, they call the b2 function files ! */

/* $Id$ */

require_once( 'Smarty.class.php' );
$wpsmarty = new Smarty;
$wpsmarty->template_dir = './wp-blogs/main/templates';
$wpsmarty->compile_dir  = './wp-blogs/main/templates_c';
$wpsmarty->cache_dir    = './wp-blogs/main/smartycache';
$wpsmarty->plugin_dir    = './wp-plugins';
require_once( 'wp-include/class-smarty.php' );
$blog = 1;
require_once('wp-blog-header.php');
// not on by default: require_once(ABSPATH.'wp-links/links.weblogs.com.php');

define( 'NODISPLAY', false );

$wpsmarty->assign( 'siteurl', $siteurl );
$wpsmarty->assign( 'b2_version', $wp_version );

if($posts) 
{ 
	foreach ($posts as $post) 
	{ 
		start_wp(); 
		$content .= $wpsmarty->fetch( 'post.html' );
		ob_start();
		include(ABSPATH . 'wp-comments.php');
		$txt = ob_get_contents();
		ob_end_clean();
		$content .= $txt;
	}
}
else
{
	$content = 'No posts made';
}

$wpsmarty->assign( 'content', $content );
$wpsmarty->display( 'top.html' );

$wpsmarty->display( 'end.html' );

?>
