<?php
define('MTEXPORT', '');


if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. You must install WordPress before you import any entries.");
require('../wp-config.php');

$step = $_GET['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Import from Movable Type</title>
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
<h1 id="logo"><a href="http://wordpress.org">WordPress</a></h1> 
<?php
switch($step) {

	case 0:
?> 
<p>Howdy! We&#8217;re about to begin the process to import all of your Movable Type entries into WordPress. Before we get started, you need to edit this file (<code>import-mt.php</code>) and change one line so we know where to find your MT export file. To make this easy put the import file into the <code>wp-admin</code> directory. Look for the line that says:</p>
<p><code>define('MTEXPORT', '');</code></p>
<p>and change it to</p>
<p><code>define('MTEXPORT', 'import.txt');</code></p>
<p>You have to do this manually for security reasons.</p>
<p>If you've done that and you&#8217;re all ready, <a href="import-mt.php?step=1">let's go</a>! Remember that the import process may take a minute or so if you have a large number of entries and comments. Think of all the rebuilding time you'll be saving once it's done. :)</p>
<p>On our test system, importing a blog of 1189 entries and about a thousand comments took 18 seconds. </p>
<p>The importer is smart enough not to import duplicates, so you can run this multiple times without worry if&#8212;for whatever reason&#8212;it doesn't finish. </p>
<?php
	break;
	
	case 1:
if ('' != MTEXPORT && !file_exists(MTEXPORT)) die("The file you specified does not seem to exist. Please check the path you've given.");
if ('' == MTEXPORT) die("You must edit the MTEXPORT line as described on the <a href='import-mt.php'>previous page</a> to continue.");

function checkauthor($author) {
	global $wpdb, $tableusers;
	// Checks if an author exists, creates it if it doesn't, and returns author_id
	$user_id = $wpdb->get_var("SELECT ID FROM $tableusers WHERE user_login = '$author'");
	if (!$user_id) {
		$wpdb->query("INSERT INTO $tableusers (user_login, user_pass, user_nickname) VALUES ('$author', 'changeme', '$author')");
		$user_id = $wpdb->get_var("SELECT ID FROM $tableusers WHERE user_login = '$author'");
		echo "<strong>Created user '$author'</strong>. ";
	}
	return $user_id;
}

// Bring in the data
set_magic_quotes_runtime(0);
$datalines = file(MTEXPORT); // Read the file into an array
$importdata = implode('', $datalines); // squish it
$importdata = preg_replace("/(\r\n|\n|\r)/", "\n", $importdata);

$posts = explode("--------", $importdata);
$i = -1;
echo "<ol>";
foreach ($posts as $post) { if ('' != trim($post)) {
	++$i;
	unset($post_categories);
	echo "<li>Importing post... ";

	// Take the pings out first
	preg_match("|(-----\n\nPING:.*)|s", $post, $pings);
	$post = preg_replace("|(-----\n\nPING:.*)|s", '', $post);

	// Then take the comments out
	preg_match("|(-----\nCOMMENT:.*)|s", $post, $comments);
	$post = preg_replace("|(-----\nCOMMENT:.*)|s", '', $post);
	
	// We ignore the keywords
	$post = preg_replace("|(-----\nKEYWORDS:.*)|s", '', $post);
	
	// We want the excerpt
	preg_match("|-----\nEXCERPT:(.*)|s", $post, $excerpt);
	$excerpt = addslashes(trim($excerpt[1]));
	$post = preg_replace("|(-----\nEXCERPT:.*)|s", '', $post);
	
	// We're going to put extended body into main body with a more tag
	preg_match("|-----\nEXTENDED BODY:(.*)|s", $post, $extended);
	$extended = trim($extended[1]);
	if ('' != $extended) $extended = "\n<!--more-->\n$extended";
	$post = preg_replace("|(-----\nEXTENDED BODY:.*)|s", '', $post);
	
	// Now for the main body
	preg_match("|-----\nBODY:(.*)|s", $post, $body);
	$body = trim($body[1]);
	$post_content = addslashes($body . $extended);
	$post = preg_replace("|(-----\nBODY:.*)|s", '', $post);
	
	// Grab the metadata from what's left
	$metadata = explode("\n", $post);
	foreach ($metadata as $line) {
		preg_match("/^(.*?):(.*)/", $line, $token);
		$key = trim($token[1]);
		$value = trim($token[2]);
		// Now we decide what it is and what to do with it
        switch($key) {
			case '':
				break;
            case 'AUTHOR':
                $post_author = checkauthor($value);
                break;
            case 'TITLE':
                $post_title = addslashes($value);
				echo '<i>'.stripslashes($post_title).'</i>... ';
				$post_name = sanitize_title($post_title);
                break;
            case 'STATUS':
                // "publish" and "draft" enumeration items match up; no change required
                $post_status = $value;
				if (empty($post_status)) $post_status = 'publish';
                break;
            case 'ALLOW COMMENTS':
                $post_allow_comments = $value;
                if ($post_allow_comments == 1) {
                    $comment_status = 'open';
                } else {
                    $comment_status = 'closed';
                }
                break;
            case 'CONVERT BREAKS':
                $post_convert_breaks = $value;
                break;
            case 'ALLOW PINGS':
                $post_allow_pings = trim($meta[2][0]);
                if ($post_allow_pings == 1) {
                    $post_allow_pings = 'open';
                } else {
                    $post_allow_pings = 'closed';
                }
                break;
            case 'PRIMARY CATEGORY':
				$post_categories[] = addslashes($value);
                break;
            case 'CATEGORY':    
				$post_categories[] = addslashes($value);
                break;
			case 'DATE':
				$post_date = strtotime($value);
				$post_date = date('Y-m-d H:i:s', $post_date);
				break;
			default:
//				echo "\n$key: $value";
				break;
        } // end switch
	} // End foreach

	// Let's check to see if it's in already
	if ($wpdb->get_var("SELECT ID FROM $tableposts WHERE post_title = '$post_title' AND post_date = '$post_date'")) {
		echo "Post already imported.";
	} else {
	    $wpdb->query("INSERT INTO $tableposts (
			post_author, post_date, post_content, post_title, post_excerpt,  post_status, comment_status, ping_status, post_name)
			VALUES 
			('$post_author', '$post_date', '$post_content', '$post_title', '$excerpt', '$post_status', '$comment_status', '$ping_status', '$post_name')");
		$post_id = $wpdb->get_var("SELECT ID FROM $tableposts WHERE post_title = '$post_title' AND post_date = '$post_date'");
		if (0 != count($post_categories)) {
			foreach ($post_categories as $post_category) {
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
		} // end category loop
		} else {
			$exists = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post_id AND category_id = 1");
			if (!$exists) $wpdb->query("INSERT INTO $tablepost2cat (post_id, category_id) VALUES ($post_id, 1) ");
		}
		// Now for comments
		$comments = explode("-----\nCOMMENT:", $comments[0]);
		foreach ($comments as $comment) {
		if ('' != trim($comment)) {
			// Author
			preg_match("|AUTHOR:(.*)|", $comment, $comment_author);
			$comment_author = addslashes(trim($comment_author[1]));
			$comment = preg_replace('|(\n?AUTHOR:.*)|', '', $comment);

			preg_match("|EMAIL:(.*)|", $comment, $comment_email);
			$comment_email = addslashes(trim($comment_email[1]));
			$comment = preg_replace('|(\n?EMAIL:.*)|', '', $comment);

			preg_match("|IP:(.*)|", $comment, $comment_ip);
			$comment_ip = trim($comment_ip[1]);
			$comment = preg_replace('|(\n?IP:.*)|', '', $comment);

			preg_match("|URL:(.*)|", $comment, $comment_url);
			$comment_url = addslashes(trim($comment_url[1]));
			$comment = preg_replace('|(\n?URL:.*)|', '', $comment);

			preg_match("|DATE:(.*)|", $comment, $comment_date);
			$comment_date = trim($comment_date[1]);
			$comment_date = date('Y-m-d H:i:s', strtotime($comment_date));
			$comment = preg_replace('|(\n?DATE:.*)|', '', $comment);

			$comment_content = addslashes(trim($comment));
			$comment_content = str_replace('-----', '', $comment_content);

			// Check if it's already there
			if (!$wpdb->get_row("SELECT * FROM $tablecomments WHERE comment_date = '$comment_date' AND comment_content = '$comment_content'")) {
				$wpdb->query("INSERT INTO $tablecomments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved)
				VALUES
				($post_id, '$comment_author', '$comment_email', '$comment_url', '$comment_ip', '$comment_date', '$comment_content', '1')");
				echo " Comment added.";
			}
		}
		}

		// Finally the pings
		// fix the double newline on the first one
		$pings[0] = str_replace("-----\n\n", "-----\n", $pings[0]);
		$pings = explode("-----\nPING:", $pings[0]);
		foreach ($pings as $ping) {
		if ('' != trim($ping)) {
			// 'Author'
			preg_match("|BLOG NAME:(.*)|", $ping, $comment_author);
			$comment_author = addslashes(trim($comment_author[1]));
			$ping = preg_replace('|(\n?BLOG NAME:.*)|', '', $ping);

			$comment_email = '';

			preg_match("|IP:(.*)|", $ping, $comment_ip);
			$comment_ip = trim($comment_ip[1]);
			$ping = preg_replace('|(\n?IP:.*)|', '', $ping);

			preg_match("|URL:(.*)|", $ping, $comment_url);
			$comment_url = addslashes(trim($comment_url[1]));
			$ping = preg_replace('|(\n?URL:.*)|', '', $ping);

			preg_match("|DATE:(.*)|", $ping, $comment_date);
			$comment_date = trim($comment_date[1]);
			$comment_date = date('Y-m-d H:i:s', strtotime($comment_date));
			$ping = preg_replace('|(\n?DATE:.*)|', '', $ping);
      
 			preg_match("|TITLE:(.*)|", $ping, $ping_title);
			$ping_title = addslashes(trim($ping_title[1]));
			$ping = preg_replace('|(\n?TITLE:.*)|', '', $ping);

			$comment_content = addslashes(trim($ping));
			$comment_content = str_replace('-----', '', $comment_content);
			
			$comment_content = "<trackback /><strong>$ping_title</strong>\n$comment_content";
      
			// Check if it's already there
			if (!$wpdb->get_row("SELECT * FROM $tablecomments WHERE comment_date = '$comment_date' AND comment_content = '$comment_content'")) {
				$wpdb->query("INSERT INTO $tablecomments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved)
				VALUES
				($post_id, '$comment_author', '$comment_email', '$comment_url', '$comment_ip', '$comment_date', '$comment_content', '1')");
				echo " Comment added.";
			}

		}
		}
	}
	echo "</li>";
	flush();
	// n
} }

?>
</ol>
<h3>All done. <a href="../">Have fun!</a></h3>
<?php
	break;
}
?> 
</body>
</html>