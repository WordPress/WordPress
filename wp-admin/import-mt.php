<?php
define('MTEXPORT', '');
// enter the relative path of the import.txt file containing the mt entries. If the file is called import.txt and it is /wp-admin, then this line
//should be define('MTEXPORT', 'import.txt');

if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. You must install WordPress before you import any entries.");
require('../wp-config.php');
require ('upgrade-functions.php');
$step = $_GET['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Import from Movable Type</title>
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
	#authors li 	{
		padding:3px;
		border: 1px solid #ccc;
		width: 40%;
		margin-bottom:2px;
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
<p>The importer is smart enough not to import duplicates, so you can run this multiple times without worry if&#8212;for whatever reason&#8212;it doesn't finish. If you get an <strong>out of memory</strong> error try splitting up the import file into pieces. </p>
<?php
	break;
	
	case 1:
if ('' != MTEXPORT && !file_exists(MTEXPORT)) die("The file you specified does not seem to exist. Please check the path you've given.");
if ('' == MTEXPORT) die("You must edit the MTEXPORT line as described on the <a href='import-mt.php'>previous page</a> to continue.");
// Bring in the data
set_magic_quotes_runtime(0);
$importdata = file(MTEXPORT); // Read the file into an array
$importdata = implode('', $importdata); // squish it
$importdata = preg_replace("/(\r\n|\n|\r)/", "\n", $importdata);
$importdata = preg_replace("/--------\nAUTHOR/", "--MT-ENTRY--\nAUTHOR", $importdata);
$authors = array();
$temp = array();
$posts = explode("--MT-ENTRY--", $importdata);
unset( $importdata ); // Free up memory

function users_form($n) {
	global $wpdb, $testing;
	$users = $wpdb->get_results("SELECT * FROM $wpdb->users ORDER BY ID");
	?><select name="userselect[<?php echo $n; ?>]">
	<option value="#NONE#">- Select -</option>
	<?php foreach($users as $user) {
		echo '<option value="'.$user->user_login.'">'.$user->user_login.'</option>';
		} ?>
	</select>
<?php }

$i = -1;
foreach ($posts as $post) { 
	if ('' != trim($post)) {
		++$i;
		unset($post_categories);
		preg_match("|AUTHOR:(.*)|", $post, $thematch);
		$thematch = trim($thematch[1]);
		array_push($temp,"$thematch"); //store the extracted author names in a temporary array
		}
	}//end of foreach
//we need to find unique values of author names, while preserving the order, so this function emulates the unique_value(); php function, without the sorting.
$authors[0] = array_shift($temp); 
$y = count($temp) + 1;
for ($x = 1; $x < $y; $x++) {
	$next = array_shift($temp);
	if (!(in_array($next,$authors))) array_push($authors, "$next");
	}
//by this point, we have all unique authors in the array $authors
?><p><?php _e('To make it easier for you to edit and save the imported posts and drafts, you may want to change the name of the author of the posts. For example, you may want to import all the entries as <code>admin</code>s entries.'); ?></p>
<p><?php _e('Below, you can see the names of the authors of the MovableType posts in <i>italics</i>. For each of these names, you can either pick an author in your WordPress installation from the menu, or enter a name for the author in the textbox.'); ?></p>
<p><?php _e('If a new user is created by WordPress, the password will be set, by default, to "changeme". Quite suggestive, eh? ;)'); ?></p>
	<?php
	echo '<ol id="authors">';
	echo '<form action="?step=2" method="post">';
	$j = -1;
	foreach ($authors as $author) {
	++$j;
	echo '<li><i>'.$author.'</i><br />'.'<input type="text" value="'.$author.'" name="'.'user[]'.'" maxlength="30">';
	users_form($j);
	echo '</li>';
	}
	echo '<input type="submit" value="Submit">'.'<br/>';
	echo '</form>';
	echo '</ol>';
	
	flush();

	break;
	
	case 2:
	$newauthornames = array();
	$formnames = array();
	$selectnames = array();
	$mtnames = array();
	foreach($_POST['user'] as $key => $line) { 
	$newname = trim(stripslashes($line)); 
	if ($newname == '') $newname = 'left_blank';//passing author names from step 1 to step 2 is accomplished by using POST. left_blank denotes an empty entry in the form.
	array_push($formnames,"$newname");
	}// $formnames is the array with the form entered names
	foreach ($_POST['userselect'] as $user => $key) {
	$selected = trim(stripslashes($key));
	array_push($selectnames,"$selected");
	}
	$count = count($formnames);
	for ($i = 0; $i < $count; $i++) {
	if ( $selectnames[$i] != '#NONE#') {//if no name was selected from the select menu, use the name entered in the form
	array_push($newauthornames,"$selectnames[$i]");
	} 
	else {
	array_push($newauthornames,"$formnames[$i]");
	}
	}

	$j = -1;
	//function to check the authorname and do the mapping
	function checkauthor($author) {
	global $wpdb, $mtnames, $newauthornames, $j;//mtnames is an array with the names in the mt import file
	$md5pass = md5(changeme);
	if (!(in_array($author, $mtnames))) { //a new mt author name is found
		++$j;
		$mtnames[$j] = $author; //add that new mt author name to an array 
		$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$newauthornames[$j]'"); //check if the new author name defined by the user is a pre-existing wp user
		if (!$user_id) { //banging my head against the desk now. 
			if ($newauthornames[$j] == 'left_blank') { //check if the user does not want to change the authorname
				$wpdb->query("INSERT INTO $wpdb->users (user_level, user_login, user_pass, user_nickname) VALUES ('1', '$author', '$md5pass', '$author')"); // if user does not want to change, insert the authorname $author
				$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$author'");
				$newauthornames[$j] = $author; //now we have a name, in the place of left_blank.
			} else {
			$wpdb->query("INSERT INTO $wpdb->users (user_level, user_login, user_pass, user_nickname) VALUES ('1', '$newauthornames[$j]', '$md5pass', '$newauthornames[$j]')"); //if not left_blank, insert the user specified name
			$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$newauthornames[$j]'");
			}
		} else return $user_id; // return pre-existing wp username if it exists
    } else {
    $key = array_search($author, $mtnames); //find the array key for $author in the $mtnames array
    $user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$newauthornames[$key]'");//use that key to get the value of the author's name from $newauthornames
	}
	return $user_id;
}//function checkauthor ends here

	//bring in the posts now
set_magic_quotes_runtime(0);
$importdata = file(MTEXPORT); // Read the file into an array
$importdata = implode('', $importdata); // squish it
$importdata = preg_replace("/(\r\n|\n|\r)/", "\n", $importdata);
$importdata = preg_replace("/--------\nAUTHOR/", "--MT-ENTRY--\nAUTHOR", $importdata);
$authors = array();
$temp = array();
$posts = explode("--MT-ENTRY--", $importdata);
unset( $importdata ); // Free up memory

$i = -1;
echo "<ol>";
foreach ($posts as $post) { if ('' != trim($post)) {
	++$i;
	unset($post_categories);
	echo "<li>Processing post... ";

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
                $post_author = $value;
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
				$post_date_gmt = get_gmt_from_date("$post_date");
				break;
			default:
//				echo "\n$key: $value";
				break;
        } // end switch
	} // End foreach

	// Let's check to see if it's in already
	if ($wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$post_title' AND post_date = '$post_date'")) {
		echo "Post already imported.";
	} else {
		$post_author = checkauthor($post_author);//just so that if a post already exists, new users are not created by checkauthor
	    $wpdb->query("INSERT INTO $wpdb->posts (
			post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,  post_status, comment_status, ping_status, post_name, post_modified, post_modified_gmt)
			VALUES 
			('$post_author', '$post_date', '$post_date_gmt', '$post_content', '$post_title', '$excerpt', '$post_status', '$comment_status', '$ping_status', '$post_name','$post_date', '$post_date_gmt')");
		$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$post_title' AND post_date = '$post_date'");
		if (0 != count($post_categories)) {
			foreach ($post_categories as $post_category) {
			// See if the category exists yet
			$cat_id = $wpdb->get_var("SELECT cat_ID from $wpdb->categories WHERE cat_name = '$post_category'");
			if (!$cat_id && '' != trim($post_category)) {
				$cat_nicename = sanitize_title($post_category);
				$wpdb->query("INSERT INTO $wpdb->categories (cat_name, category_nicename) VALUES ('$post_category', '$cat_nicename')");
				$cat_id = $wpdb->get_var("SELECT cat_ID from $wpdb->categories WHERE cat_name = '$post_category'");
			}
			if ('' == trim($post_category)) $cat_id = 1;
			// Double check it's not there already
			$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_id AND category_id = $cat_id");

			 if (!$exists) { 
				$wpdb->query("
				INSERT INTO $wpdb->post2cat
				(post_id, category_id)
				VALUES
				($post_id, $cat_id)
				");
			}
		} // end category loop
		} else {
			$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_id AND category_id = 1");
			if (!$exists) $wpdb->query("INSERT INTO $wpdb->post2cat (post_id, category_id) VALUES ($post_id, 1) ");
		}
		echo " Post imported successfully...";
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
			if (!$wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_date = '$comment_date' AND comment_content = '$comment_content'")) {
				$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved)
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
			
			$comment_content = "<strong>$ping_title</strong>\n\n$comment_content";
      
			// Check if it's already there
			if (!$wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_date = '$comment_date' AND comment_content = '$comment_content'")) {
				$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved, comment_type)
				VALUES
				($post_id, '$comment_author', '$comment_email', '$comment_url', '$comment_ip', '$comment_date', '$comment_content', '1', 'trackback')");
				echo " Comment added.";
			}

		}
		}
	}
	echo "</li>";
	flush();

} }
upgrade_all();
?>
</ol>
<h3>All done. <a href="../">Have fun!</a></h3>
<?php
	break;
}
?> 
</body>
</html>
