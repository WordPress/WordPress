<?php 

// Contributed by Alex King
// http://www.alexking.org/software/b2/

/* These first lines are the first part of a WordPress template.
		   In every template you do, you got to copy them before the CafeLog 'loop' */
$blog=1; // enter your blog's ID
header('Content-type: text/xml');
require('wp-blog-header.php');

if (!isset($rss_language)) { $rss_language = 'en'; }
echo "<?xml version=\"1.0\"?".">"; 
?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="2.0" 
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<?php
$i = 0;
foreach ($posts as $post) { start_wp();
	if ($i < 1) {
		$i++;
?>
	<title><?php if (isset($_REQUEST["p"])) { echo "Comments on: "; the_title_rss(); } else { bloginfo_rss("name"); echo " Comments"; } ?></title>
	<link><?php isset($_REQUEST["p"]) ? permalink_single_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<dc:language><?php echo $rss_language ?></dc:language>
	<dc:creator><?php echo antispambot($admin_email) ?></dc:creator>
	<dc:rights>Copyright <?php echo mysql2date('Y', get_lastpostdate()); ?></dc:rights>
	<dc:date><?php echo gmdate('Y-m-d\TH:i:s'); ?></dc:date>
	<admin:generatorAgent rdf:resource="http://wordpress.org/?v=<?php echo $wp_version ?>"/>
	<admin:errorReportsTo rdf:resource="mailto:<?php echo antispambot($admin_email) ?>"/>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>

<?php 
		if (isset($_REQUEST["p"])) {
			$comments = $wpdb->get_results("SELECT comment_ID,
												   comment_author,
												   comment_author_email,
												   comment_author_url,
												   comment_date,
												   comment_content,
												   comment_post_ID,
												   $tableposts.ID,
												   $tableposts.post_password
											FROM $tablecomments 
											LEFT JOIN $tableposts ON comment_post_id = id
											WHERE comment_post_ID = '$id'
											AND $tablecomments.comment_approved = '1'
											AND $tableposts.post_status = 'publish'
											AND post_date < '".date("Y-m-d H:i:s")."' 
											ORDER BY comment_date 
											LIMIT $posts_per_rss");
		}
		else { // if no post id passed in, we'll just ue the last 10 comments.
			$comments = $wpdb->get_results("SELECT comment_ID,
												   comment_author,
												   comment_author_email,
												   comment_author_url,
												   comment_date,
												   comment_content,
												   comment_post_ID,
												   $tableposts.ID,
												   $tableposts.post_password
											FROM $tablecomments 
											LEFT JOIN $tableposts ON comment_post_id = id
											WHERE $tableposts.post_status = 'publish'
											AND $tablecomments.comment_approved = '1'
											AND post_date < '".date("Y-m-d H:i:s")."' 
											ORDER BY comment_date DESC
											LIMIT $posts_per_rss");
		}
	// this line is WordPress' motor, do not delete it.
		if ($comments) {
			foreach ($comments as $comment) {
?>
	<item rdf:about="<?php permalink_comments_rss() ?>">
		<title>by: <?php comment_author_rss() ?></title>
		<link><?php comment_link_rss() ?></link>
		<dc:date><?php comment_time('Y-m-d\TH:i:s'); ?></dc:date>
		<guid isPermaLink="false"><?php comment_ID(); echo ":".$comment->comment_post_ID; ?>@<?php bloginfo_rss("url") ?></guid>
			<?php 
			if (!empty($comment->post_password) && $HTTP_COOKIE_VARS['wp-postpass'] != $comment->post_password) {
			?>
		<description>Protected Comments: Please enter your password to view comments.</description>
		<content:encoded><![CDATA[<?php echo get_the_password_form() ?>]]></content:encoded>
			<?php
			}
			else {
			?>
		<description><?php comment_text_rss() ?></description>
		<content:encoded><![CDATA[<?php comment_text() ?>]]></content:encoded>
			<?php } // close check for password ?>
	</item>
<?php 
			}
		}
	}
}
?>
</channel>
</rss>