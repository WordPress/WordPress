<?php 
if (! $feed) {
    require('wp-blog-header.php');
}

header('Content-type: text/xml');

echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; 
?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<?php
$i = 0;
foreach ($posts as $post) { start_wp();
	if ($i < 1) {
		$i++;
?>
	<title><?php if (isset($_REQUEST["p"]) || isset($_REQUEST["name"])) { echo "Comments on: "; the_title_rss(); } else { bloginfo_rss("name"); echo " Comments"; } ?></title>
	<link><?php (isset($_REQUEST["p"]) || isset($_REQUEST["name"])) ? permalink_single_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<language><?php echo get_settings('rss_language'); ?></language>
	<pubDate><?php echo gmdate('r'); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php echo $wp_version ?></generator>

<?php 
		if (isset($_REQUEST["p"]) || isset($_REQUEST["name"])) {
			$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
			comment_author_url, comment_date, comment_content, comment_post_ID, 
			$tableposts.ID, $tableposts.post_password FROM $tablecomments 
			LEFT JOIN $tableposts ON comment_post_id = id WHERE comment_post_ID = '$id' 
			AND $tablecomments.comment_approved = '1' AND $tableposts.post_status = 'publish' 
			AND post_date < '".date("Y-m-d H:i:s")."' 
			ORDER BY comment_date LIMIT " . get_settings('posts_per_rss') );
		} else { // if no post id passed in, we'll just ue the last 10 comments.
			$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
			comment_author_url, comment_date, comment_content, comment_post_ID, 
			$tableposts.ID, $tableposts.post_password FROM $tablecomments 
			LEFT JOIN $tableposts ON comment_post_id = id WHERE $tableposts.post_status = 'publish' 
			AND $tablecomments.comment_approved = '1' AND post_date < '".date("Y-m-d H:i:s")."'  
			ORDER BY comment_date DESC LIMIT " . get_settings('posts_per_rss') );
		}
	// this line is WordPress' motor, do not delete it.
		if ($comments) {
			foreach ($comments as $comment) {
?>
	<item>
		<title>by: <?php comment_author_rss() ?></title>
		<link><?php comment_link_rss() ?></link>
		<pubDate><?php comment_time('r'); ?></pubDate>
		<guid isPermaLink="false"><?php comment_ID(); echo ":".$comment->comment_post_ID; ?>@<?php bloginfo_rss("url") ?></guid>
			<?php 
			if (!empty($comment->post_password) && $HTTP_COOKIE_VARS['wp-postpass'] != $comment->post_password) {
			?>
		<description>Protected Comments: Please enter your password to view comments.</description>
		<content:encoded><![CDATA[<?php echo get_the_password_form() ?>]]></content:encoded>
			<?php
			} else {
			?>
		<description><?php comment_text_rss() ?></description>
		<content:encoded><![CDATA[<?php comment_text() ?>]]></content:encoded>
			<?php 
			} // close check for password 
			?>
	</item>
<?php 
			}
		}
	}
}
?>
</channel>
</rss>
