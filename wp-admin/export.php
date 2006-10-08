<?php
require_once ('admin.php');
$title = __('Export');
$parent_file = 'edit.php';

if ( isset( $_GET['download'] ) )
	export_wp();

require_once ('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Export'); ?></h2>
<div class="narrow">
<p><?php _e('When you click the button below WordPress will create a XML file for you to save to your computer.'); ?></p>
<p><?php _e('This format, which we call WordPress eXtended RSS or WXR, will contain your posts, comments, custom fields, and categories.'); ?></p>
<form action="" method="get">
<p class="submit"><input type="submit" name="submit" value="<?php _e('Download Export File'); ?> &raquo;" />
<input type="hidden" name="download" value="true" />
</p>
</form>
</div>
</div>

<?php

function export_wp() {
	global $wpdb, $posts, $post;
	$filename = 'wordpress.' . date('Y-m-d') . '.xml';
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=$filename");
header('Content-type: text/xml; charset=' . get_option('blog_charset'), true);
//$posts = query_posts('');
$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts ORDER BY post_date_gmt ASC");
?>
<!-- generator="wordpress/<?php bloginfo_rss('version') ?>" created="<?php echo date('Y-m-d H:m'); ?>"-->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.0/"
>

<channel>
	<title><?php bloginfo_rss('name'); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php bloginfo_rss('version'); ?></generator>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('rss2_head'); ?>
	<?php if ($posts) { foreach ($posts as $post) { start_wp(); ?>
<item>
<title><?php the_title_rss() ?></title>
<link><?php permalink_single_rss() ?></link>
<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
<dc:creator><?php the_author() ?></dc:creator>
<?php the_category_rss() ?>

<guid isPermaLink="false"><?php the_guid(); ?></guid>
<description></description>
<content:encoded><![CDATA[<?php echo $post->post_content ?>]]></content:encoded>
<wp:post_date><?php echo $post->post_date; ?></wp:post_date>
<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
<wp:ping_status><?php echo $post->ping_status; ?></wp:ping_status>
<wp:post_name><?php echo $post->post_name; ?></wp:post_name>
<wp:status><?php echo $post->post_status; ?></wp:status>
<wp:post_parent><?php echo $post->post_parent; ?></wp:post_parent>
<wp:post_type><?php echo $post->post_type; ?></wp:post_type>
<?php
$postmeta = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = $post->ID"); 
if ( $postmeta ) {
?>
<?php foreach( $postmeta as $meta ) { ?>
<wp:postmeta>
<wp:meta_key><?php echo $meta->meta_key; ?></wp:meta_key>
<wp:meta_value><?Php echo $meta->meta_value; ?></wp:meta_value>
</wp:postmeta>
<?php } ?>
<?php } ?>
<?php
$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID"); 
if ( $comments ) { foreach ( $comments as $c ) { ?>
<wp:comment>
<wp:comment_author><?php echo $c->comment_author; ?></wp:comment_author>
<wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
<wp:comment_author_url><?php echo $c->comment_author_url; ?></wp:comment_author_url>
<wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
<wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
<wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
<wp:comment_content><?php echo $c->comment_content; ?></wp:comment_content>
<wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
<wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
<wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
</wp:comment>
<?php } } ?>
	</item>
<?php } } ?>
</channel>
</rss>
<?php
	die();
}

include ('admin-footer.php');
?>