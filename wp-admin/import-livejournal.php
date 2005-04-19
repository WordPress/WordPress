<?php
define('XMLFILE', '');
// Example:
// define('XMLFILE', '/home/example/public_html/rss.xml');
// or if it's in the same directory as import-rss.php
// define('XMLFILE', 'rss.xml');

$post_author = 1; // Author to import posts as author ID
$timezone_offset = 0; // GMT offset of posts your importing


$add_hours = intval($timezone_offset);
$add_minutes = intval(60 * ($timezone_offset - $add_hours));

if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. You must install WordPress before you import any entries.");
require('../wp-config.php');

$step = $_GET['step'];
if (!$step) $step = 0;
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Import from RSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
<p>Howdy! This importer allows you to extract posts from a LiveJournal XML export file. To get started you must edit the following line in this file (<code>import-livejournal.php</code>) </p>
<p><code>define('XMLFILE', '');</code></p>
<p>You want to define where the XML file we'll be working with is, for example: </p>
<p><code>define('XMLFILE', '2002-04.xml');</code></p>
<p>You have to do this manually for security reasons.</p>
<p>If you've done that and you&#8217;re all ready, <a href="import-livejournal.php?step=1">let's go</a>!</p>
<?php
	break;
	
	case 1:
if ('' != XMLFILE && !file_exists(XMLFILE)) die("The file you specified does not seem to exist. Please check the path you've given.");
if ('' == XMLFILE) die("You must edit the XMLFILE line as described on the <a href='import-rss.php'>previous page</a> to continue.");

// Bring in the data
set_magic_quotes_runtime(0);
$datalines = file(XMLFILE); // Read the file into an array
$importdata = implode('', $datalines); // squish it
$importdata = str_replace(array("\r\n", "\r"), "\n", $importdata);

preg_match_all('|<entry>(.*?)</entry>|is', $importdata, $posts);
$posts = $posts[1];

echo '<ol>';
foreach ($posts as $post) :
$title = $date = $categories = $content = $post_id =  '';
echo "<li>Importing post... ";

preg_match('|<subject>(.*?)</subject>|is', $post, $title);
$title = addslashes( trim($title[1]) );
$post_name = sanitize_title($title);

preg_match('|<eventtime>(.*?)</eventtime>|is', $post, $date);
$date = strtotime($date[1]);

$post_date = date('Y-m-d H:i:s', $date);


preg_match('|<event>(.*?)</event>|is', $post, $content);
$content = str_replace( array('<![CDATA[', ']]>'), '', addslashes( trim($content[1]) ) );

// Now lets put it in the DB
if ($wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$title' AND post_date = '$post_date'")) :
	echo 'Post already imported';
else : 
	
	$wpdb->query("INSERT INTO $wpdb->posts 
		(post_author, post_date, post_date_gmt, post_content, post_title,post_status, comment_status, ping_status, post_name)
		VALUES 
		('$post_author', '$post_date', DATE_ADD('$post_date', INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE), '$content', '$title', 'publish', '$comment_status', '$ping_status', '$post_name')");
	$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$title' AND post_date = '$post_date'");
	if (!$post_id) die("couldn't get post ID");
	$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_id AND category_id = 1");
	if (!$exists) $wpdb->query("INSERT INTO $wpdb->post2cat (post_id, category_id) VALUES ($post_id, 1) ");
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