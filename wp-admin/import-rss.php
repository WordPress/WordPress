<?php
define('RSSFILE', '');
// Example:
// define('RSSFILE', '/home/example/public_html/rss.xml');
// or if it's in the same directory as import-rss.php
// define('RSSFILE', 'rss.xml');

$post_author = 1; // Author to import posts as author ID
$timezone_offset = 0; // GMT offset of posts your importing

function unhtmlentities($string) { // From php.net for < 4.3 compat
   $trans_tbl = get_html_translation_table(HTML_ENTITIES);
   $trans_tbl = array_flip($trans_tbl);
   return strtr($string, $trans_tbl);
}

$add_hours = intval($timezone_offset);
$add_minutes = intval(60 * ($timezone_offset - $add_hours));

if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. You must install WordPress before you import any entries.");
require('../wp-config.php');

$step = $_GET['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Import from RSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style media="screen" type="text/css">
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 20%;
		margin-right: 20%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/logo.png);
		background-repeat: no-repeat;
		height: 60px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		text-decoration: none;
		text-indent: -100em;
		height: 60px;
	}
	p {
		line-height: 140%;
	}
	</style>
</head><body> 
<h1 id="logo"><a href="http://wordpress.org/">WordPress</a></h1> 
<?php
switch($step) {

	case 0:
?> 
<p>Howdy! This importer allows you to extract posts from any RSS 2.0 file into your blog. This is useful if you want to import your posts from a system that is not handled by a custom import tool. To get started you must edit the following line in this file (<code>import-rss.php</code>) </p>
<p><code>define('RSSFILE', '');</code></p>
<p>You want to define where the RSS file we'll be working with is, for example: </p>
<p><code>define('RSSFILE', 'rss.xml');</code></p>
<p>You have to do this manually for security reasons. When you're done reload this page and we'll take you to the next step.</p>
<?php if ('' != RSSFILE) : ?>
<h2 style="text-align: right;"><a href="import-rss.php?step=1">Begin RSS Import &raquo;</a></h2>
<?php endif; ?>
<?php
	break;

	case 1:

// Bring in the data
set_magic_quotes_runtime(0);
$datalines = file(RSSFILE); // Read the file into an array
$importdata = implode('', $datalines); // squish it
$importdata = str_replace(array("\r\n", "\r"), "\n", $importdata);

preg_match_all('|<item>(.*?)</item>|is', $importdata, $posts);
$posts = $posts[1];

echo '<ol>';
foreach ($posts as $post) :
$title = $date = $categories = $content = $post_id =  '';
echo "<li>Importing post... ";

preg_match('|<title>(.*?)</title>|is', $post, $title);
$title = addslashes( trim($title[1]) );
$post_name = sanitize_title($title);

preg_match('|<pubdate>(.*?)</pubdate>|is', $post, $date);
$date = strtotime($date[1]);

if (!$date) : // if we don't already have something from pubDate
	preg_match('|<dc:date>(.*?)</dc:date>|is', $post, $date);
	$date = preg_replace('|(-[0-9:]+)$|', '', $date[1]);
	$date = strtotime($date);
endif;

$post_date = gmdate('Y-m-d H:i:s', $date);

preg_match_all('|<category>(.*?)</category>|is', $post, $categories);
$categories = $categories[1];

if (!$categories) :
	preg_match_all('|<dc:subject>(.*?)</dc:subject>|is', $post, $categories);
	$categories = $categories[1];
endif;

preg_match('|<content:encoded>(.*?)</content:encoded>|is', $post, $content);
$content = str_replace( array('<![CDATA[', ']]>'), '', addslashes( trim($content[1]) ) );

if (!$content) : // This is for feeds that put content in description
	preg_match('|<description>(.*?)</description>|is', $post, $content);
	$content = $wpdb->escape( unhtmlentities( trim($content[1]) ) );
endif;

// Clean up content
$content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $content);
$content = str_replace('<br>', '<br />', $content);
$content = str_replace('<hr>', '<hr />', $content);

// This can mess up on posts with no titles, but checking content is much slower
// So we do it as a last resort
if ('' == $title) : 
	$dupe = $wpdb->get_var("SELECT ID FROM $tableposts WHERE post_content = '$content' AND post_date = '$post_date'");
else :
	$dupe = $wpdb->get_var("SELECT ID FROM $tableposts WHERE post_title = '$title' AND post_date = '$post_date'");
endif;

// Now lets put it in the DB
if ($dupe) :
	echo 'Post already imported';
else : 
	
	$wpdb->query("INSERT INTO $tableposts 
		(post_author, post_date, post_date_gmt, post_content, post_title,post_status, comment_status, ping_status, post_name)
		VALUES 
		('$post_author', '$post_date', DATE_ADD('$post_date', INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE), '$content', '$title', 'publish', '$comment_status', '$ping_status', '$post_name')");
	$post_id = $wpdb->get_var("SELECT ID FROM $tableposts WHERE post_title = '$title' AND post_date = '$post_date'");
	if (!$post_id) die("couldn't get post ID");
	if (0 != count($categories)) :
		foreach ($categories as $post_category) :
		$post_category = unhtmlentities($post_category);
		// See if the category exists yet
		$cat_id = $wpdb->get_var("SELECT cat_ID from $tablecategories WHERE cat_name = '$post_category'");
		if (!$cat_id && '' != trim($post_category)) {
			$cat_nicename = sanitize_title($post_category);
			$wpdb->query("INSERT INTO $tablecategories (cat_name, category_nicename) VALUES ('$post_category', '$cat_nicename')");
			$cat_id = $wpdb->get_var("SELECT cat_ID from $tablecategories WHERE cat_name = '$post_category'");
		}
		if ('' == trim($post_category)) $cat_id = 1;
		// Double check it's not there already
		$exists = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post_id AND category_id = $cat_id");
	
		 if (!$exists) { 
			$wpdb->query("
			INSERT INTO $tablepost2cat
			(post_id, category_id)
			VALUES
			($post_id, $cat_id)
			");
			}
	endforeach;
	else:
		$exists = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post_id AND category_id = 1");
		if (!$exists) $wpdb->query("INSERT INTO $tablepost2cat (post_id, category_id) VALUES ($post_id, 1) ");
	endif;
	echo 'Done!</li>';
endif;


endforeach;
?>
</ol>

<h3>All done. <a href="../">Have fun!</a></h3>
<?php
	break;
}
?> 
</body>
</html>