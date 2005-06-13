<?php
if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. You must install WordPress before you import any entries.");

require_once('../wp-config.php');
require('upgrade-functions.php');

$wpvarstoreset = array('action', 'gmpath', 'archivespath', 'lastentry');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

header( 'Content-Type: text/html; charset=utf-8' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Import from GreyMatter</title>
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
switch ($action) {

case "step1":

	function gm2autobr($string) { // transforms GM's |*| into wp's <br />\n
		$string = str_replace("|*|","<br />\n",$string);
		return($string);
	}

	if (!@chdir($archivespath))
		die("Wrong path, $archivespath\ndoesn't exist\non the server");

	if (!@chdir($gmpath))
		die("Wrong path, $gmpath\ndoesn't exist\non the server");
?>

<p>The importer is running...</p>
<ul>
<li>importing users... <ul><?php

	chdir($gmpath);
	$userbase = file("gm-authors.cgi");

	foreach($userbase as $user) {
		$userdata=explode("|", $user);

		$s=$userdata[4];
		$user_joindate=substr($s,6,4)."-".substr($s,0,2)."-".substr($s,3,2)." 00:00:00";

		$user_login=addslashes($userdata[0]);
		$pass1=addslashes($userdata[1]);
		$user_nickname=addslashes($userdata[0]);
		$user_email=addslashes($userdata[2]);
		$user_url=addslashes($userdata[3]);
		$user_joindate=addslashes($user_joindate);

		$loginthere = $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE user_login = '$user_login'");
		if ($loginthere) {
			echo "<li>user <i>$user_login</i>... <b>Already exists</b></li>";
			continue;
		}

		$query = "INSERT INTO $wpdb->users (user_login,user_pass,user_email,user_url,user_registered,user_level) VALUES ('$user_login','$pass1','$user_email','$user_url','$user_joindate','1')";
		$result = $wpdb->query($query);
		if ($result==false) {
			die ("<strong>ERROR</strong>: couldn't register an user!");
		}
		echo "<li>user <i>$user_login</i>... <b>Done</b></li>";

	}

?></ul><b>Done</b></li>
<li>importing posts, comments, and karma...<br /><ul><?php

	chdir($archivespath);
	
	for($i = 0; $i <= $lastentry; $i = $i + 1) {
		
		$entryfile = "";
		
		if ($i<10000000) {
			$entryfile .= "0";
			if ($i<1000000) {
				$entryfile .= "0";
				if ($i<100000) {
					$entryfile .= "0";
					if ($i<10000) {
						$entryfile .= "0";
						if ($i<1000) {
							$entryfile .= "0";
							if ($i<100) {
								$entryfile .= "0";
								if ($i<10) {
									$entryfile .= "0";
		}}}}}}}

		$entryfile .= "$i";

		if (is_file($entryfile.".cgi")) {

			$entry=file($entryfile.".cgi");
			echo "<li>entry # $entryfile ";
			$postinfo=explode("|",$entry[0]);
			$postmaincontent=gm2autobr($entry[2]);
			$postmorecontent=gm2autobr($entry[3]);

			$post_author=trim(addslashes($postinfo[1]));
			// we'll check the author is registered, or if it's a deleted author
			$sql = "SELECT * FROM $wpdb->users WHERE user_login = '$post_author'";
			$result = $wpdb->query($sql);
			if (! $result) { // if deleted from GM, we register the author as a level 0 user in wp
				$user_joindate="1979-06-06 00:41:00";
				$user_login=addslashes($post_author);
				$pass1=addslashes("password");
				$user_nickname=addslashes($post_author);
				$user_email=addslashes("user@deleted.com");
				$user_url=addslashes("");
				$user_joindate=addslashes($user_joindate);
				$query = "INSERT INTO $wpdb->users (user_login,user_pass,user_email,user_url,user_registered,user_level) VALUES ('$user_login','$pass1','$user_email','$user_url','$user_joindate','0')";
				$result = $wpdb->query($query);
				if ($result==false) {
					die ("<strong>ERROR</strong>: couldn't register an user!");
				}
				echo ": registered deleted user <i>$user_login</i> at level 0 ";
			}

			$sql = "SELECT ID FROM $wpdb->users WHERE user_login = '$post_author'";
			$post_author_ID = $wpdb->get_var($sql);

			$post_title=gm2autobr($postinfo[2]);
			$post_title=addslashes($post_title);

			$postyear=$postinfo[6];
			$postmonth=zeroise($postinfo[4],2);
			$postday=zeroise($postinfo[5],2);
			$posthour=zeroise($postinfo[7],2);
			$postminute=zeroise($postinfo[8],2);
			$postsecond=zeroise($postinfo[9],2);

			if (($postinfo[10]=="PM") && ($posthour!="12"))
				$posthour=$posthour+12;

			$post_date="$postyear-$postmonth-$postday $posthour:$postminute:$postsecond";

			$post_content=$postmaincontent;
			if (strlen($postmorecontent)>3)
				$post_content .= "<!--more--><br /><br />".$postmorecontent;
			$post_content=addslashes($post_content);

			$post_karma=$postinfo[12];

			$query = "INSERT INTO $wpdb->posts (post_author,post_date,post_content,post_title) VALUES ('$post_author_ID','$post_date','$post_content','$post_title')";
			$result = $wpdb->query($query);

			if (!$result)
				die ("Error in posting...");
			
			$query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 1";
			$post_ID = $wpdb->get_var($query);

			// Grab a default category.
			$post_category = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories LIMIT 1");

			// Update the post2cat table.
			$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_ID AND category_id = $post_category");
			  
			if (!$exists) {
			  $wpdb->query("
					INSERT INTO $wpdb->post2cat
					(post_id, category_id)
					VALUES
					($post_ID, $post_category)
					");
			}

			$c=count($entry);
			if ($c>4) {
				for ($j=4;$j<$c;$j++) {
					$entry[$j]=gm2autobr($entry[$j]);
					$commentinfo=explode("|",$entry[$j]);
					$comment_post_ID=$post_ID;
					$comment_author=addslashes($commentinfo[0]);
					$comment_author_email=addslashes($commentinfo[2]);
					$comment_author_url=addslashes($commentinfo[3]);
					$comment_author_IP=addslashes($commentinfo[1]);

					$commentyear=$commentinfo[7];
					$commentmonth=zeroise($commentinfo[5],2);
					$commentday=zeroise($commentinfo[6],2);
					$commenthour=zeroise($commentinfo[8],2);
					$commentminute=zeroise($commentinfo[9],2);
					$commentsecond=zeroise($commentinfo[10],2);
					if (($commentinfo[11]=="PM") && ($commenthour!="12"))
						$commenthour=$commenthour+12;
					$comment_date="$commentyear-$commentmonth-$commentday $commenthour:$commentminute:$commentsecond";

					$comment_content=addslashes($commentinfo[12]);

					$sql3 = "INSERT INTO $wpdb->comments (comment_post_ID,comment_author,comment_author_email,comment_author_url,comment_author_IP,comment_date,comment_content) VALUES ('$comment_post_ID','$comment_author','$comment_author_email','$comment_author_url','$comment_author_IP','$comment_date','$comment_content')";
					$result3 = $wpdb->query($sql3);
					if (!$result3)
						die ("There is an error with the database, it can't store your comment..");
				}
				$comments=$c-4;
				echo ": imported $comments comment";
				if ($comments>1)
					echo "s";
			}
			echo "... <b>Done</b></li>";
		}
	} 
	upgrade_all();
	?>
</ul><b>Done</b></li></ul>
<p>&nbsp;</p>
<p>Completed GM 2 WordPress import !</p>
<p>Now you can go and <a href="wp-login.php">log in</a>, have fun !</p>
	<?php
	break;

default:
?>

<p>This is a basic GreyMatter to WordPress import script.</p>
<p>What it does:</p>
<ul>
<li>parses gm-authors.cgi to import authors: everyone is imported at level 1</li>
<li>parses the entries cgi files to import posts, comments, and karma on posts (although karma is not used on WordPress); if authors are found not to be in gm-authors.cgi, imports them at level 0</li>
</ul>
<p>What it does not:</p>
<ul>
<li>parse gm-counter.cgi (what's the use of that file ?), gm-banlist.cgi, gm-cplog.cgi (you can make a CP log hack if you really feel like it, but I question the need of a CP log)</li>
<li>import gm-templates. you'll start with the basic template wp.php</li>
<li>doesn't keep entries on top</li>
</ul>

<h3>First step: Install WordPress</h3>
<p>Install the WordPress blog as explained in the <a href="../readme.html" target="_blank">ReadMe</a>, then immediately come back here.</p>

<form name="stepOne" method="get">
<input type="hidden" name="action" value="step1" />
<h3>Second step: Provide GreyMatter details</h3>
<table cellpadding="0">
<tr>
<td>Path to GM files:</td>
<td><input type="text" style="width:300px" name="gmpath" value="/home/my/site/cgi-bin/greymatter/" /></td>
</tr>
<tr>
<td>Path to GM entries:</td>
<td><input type="text" style="width:300px" name="archivespath" value="/home/my/site/cgi-bin/greymatter/archives/" /></td>
</tr>
</table>

<p>This importer will search for files 00000001.cgi to 000-whatever.cgi, so you need to enter the number of the last GM post here. (If you don't know that number, just log into your FTP and look it up in the entries' folder)</p>

<table>
<tr>
<td>Last entry's number:</td>
<td><input type="text" name="lastentry" value="00000001" /></td>
</tr>
</table>

<p>When you're ready, click OK to start importing: <input type="submit" name="submit" value="OK" class="search" /></p>
</form>

</body>
</html>
	<?php
	break;
}

?>