<?php /* Don't remove these lines, they call the b2 function files ! */

/* $Id$ */

require_once( 'Smarty.class.php' );
$smarty = new Smarty;
$smarty->template_dir = './wp-blogs/main/templates';
$smarty->compile_dir  = './wp-blogs/main/templates_c';
$smarty->cache_dir    = './wp-blogs/main/smartycache';
$smarty->plugin_dir    = './wp-plugins';
require_once( 'b2-include/smarty.inc.php' );
$blog = 1;
require_once('blog.header.php');
require_once($abspath.'wp-links/links.php');
// not on by default: require_once($abspath.'wp-links/links.weblogs.com.php');

define( 'NODISPLAY', false );

$smarty->assign( 'siteurl', $siteurl );
$smarty->assign( 'b2_version', $b2_version );

if($posts) 
{ 
	foreach ($posts as $post) 
	{ 
		start_b2(); 
		$content .= $smarty->fetch( 'post.html' );
		ob_start();
		include($abspath . 'b2comments.php');
		$txt = ob_get_contents();
		ob_end_clean();
		$content .= $txt;
	}
}
else
{
	$content = 'No posts made';
}

$smarty->assign( 'content', $content );
$smarty->display( 'top.html' );

$smarty->display( 'end.html' );

?>
