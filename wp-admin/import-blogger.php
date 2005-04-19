<?php

$wpvarstoreset = array('action');
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
require_once('../wp-config.php');
require('upgrade-functions.php');
header( 'Content-Type: text/html; charset=utf-8' );
switch ($action) {

case "step1":
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<title>Blogger to WordPress - Converting...</title>
	<link rel="stylesheet" href="wp-admin.css" type="text/css">
</head>
<body>
<div class="wrap">
<h1>Blogger to <img src="../wp-images/wpminilogo.png" width="50" height="50" border="0" alt="WordPress" align="top" /></h1>
<p>The importer is running...</p>
<ul>
	<li>Importing posts and users
		<ul><?php

	for($bgy=1999; $bgy<=(date('Y')); $bgy++) {
		for($bgm=1; $bgm<13; $bgm++) {

		$bgmm = zeroise($bgm,2);
	
		$archivefile = "../$bgy"."_"."$bgmm"."_01_wordpress.php";
		
		if (file_exists($archivefile)) {

			$f = fopen($archivefile,"r");
			$archive = fread($f, filesize($archivefile));
			fclose($f);
			echo "<li>$bgy/$bgmm ";

			$posts = explode('<wordpresspost>', $archive);

			for ($i = 1; $i < count($posts); $i = $i + 1) {

			$postinfo = explode('|||', $posts[$i]);
			$post_date = $postinfo[0];
			$post_content = $postinfo[2];
			// Don't try to re-use the original numbers
			// because the new, longer numbers are too
			// big to handle as ints.
			//$post_number = $postinfo[3];
			$post_title = $postinfo[4];

			$post_author = trim(addslashes($postinfo[1]));
			// we'll check the author is registered already
			$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login = '$post_author'");
			if (!$user) { // seems s/he's not, so let's register
				$user_ip = '127.0.0.1';
				$user_domain = 'localhost';
				$user_browser = 'server';
				$user_joindate = '1979-06-06 00:41:00'; // that's my birthdate (gmt+1) - I could choose any other date. You could change the date too. Just remember the year must be >=1970 or the world would just randomly fall on your head (everything might look fine, and then blam! major headache!)
				$user_login = addslashes($post_author);
				$pass1 = addslashes('password');
				$user_nickname = addslashes($post_author);
				$user_email = addslashes('user@wordpress.org');
				$user_url = addslashes('');
				$user_joindate = addslashes($user_joindate);
				$result = $wpdb->query("
				INSERT INTO $wpdb->users (
					user_login,
					user_pass,
					user_nickname,
					user_email,
					user_url,
					user_ip,
					user_domain,
					user_browser,
					user_registered,
					user_level,
					user_idmode
				) VALUES (
					'$user_login',
					'$pass1',
					'$user_nickname',
					'$user_email',
					'$user_url',
					'$user_ip',
					'$user_domain',
					'$user_browser',
					'$user_joindate',
					'1',
					'nickname'
				)");

				echo ": Registered user <strong>$user_login</strong>";
			}

			$post_author_ID = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$post_author'");

			$post_date = explode(' ', $post_date);
			$post_date_Ymd = explode('/', $post_date[0]);
			$postyear = $post_date_Ymd[2];
			$postmonth = zeroise($post_date_Ymd[0], 2);
			$postday = zeroise($post_date_Ymd[1], 2);
			$post_date_His = explode(':', $post_date[1]);
			$posthour = zeroise($post_date_His[0], 2);
			$postminute = zeroise($post_date_His[1], 2);
			$postsecond = zeroise($post_date_His[2], 2);

			if (($post_date[2] == 'PM') && ($posthour != '12'))
				$posthour = $posthour + 12;
			else if (($post_date[2] == 'AM') && ($posthour == '12'))
				$posthour = '00';

			$post_date = "$postyear-$postmonth-$postday $posthour:$postminute:$postsecond";

			$post_content = addslashes($post_content);
			$post_content = str_replace('<br>', '<br />', $post_content); // the XHTML touch... ;)
			
			$post_title = addslashes($post_title);
			
			// Quick-n-dirty check for dups:
			$dupcheck = $wpdb->get_results("SELECT ID,post_date,post_title FROM $wpdb->posts WHERE post_date='$post_date' AND post_title='$post_title' LIMIT 1",ARRAY_A);
			if ($dupcheck[0]['ID']) {
				print "<br />\nSkipping duplicate post, ID = '" . $dupcheck[0]['ID'] . "'<br />\n";
				print "Timestamp: " . $post_date . "<br />\n";
				print "Post Title: '" . stripslashes($post_title) . "'<br />\n";
				continue;
			}

			$result = $wpdb->query("
			INSERT INTO $wpdb->posts 
				(post_author,post_date,post_content,post_title,post_category)
			VALUES 
				('$post_author_ID','$post_date','$post_content','$post_title','1')
			");


			} echo '... <strong>Done</strong></li>';
			
		}}
	}

	upgrade_all();
	?>
</ul>
<strong>Done</strong>
</li>
</ul>
<p>&nbsp;</p>
<p>Completed Blogger to WordPress import!</p>
<p>Now you can go and <a href="../wp-login.php">log in</a>, have fun!</p>
</div>
</body>
</html>
	<?php
	break;

default:

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<title>Blogger to WordPress Import Utility</title>
	<link rel="stylesheet" href="wp-admin.css" type="text/css">
</head>

<body>
<div class="wrap">
<h1>Blogger to <img src="../wp-images/wpminilogo.png" width="50" height="50" border="0" alt="WordPress" align="top" /></h1>
<p>This is a basic Blogger to WordPress import script.</p>
<p>What it does:</p>
<ul>
	<li>Parses your archives to retrieve your blogger posts.</li>
	<li>Adds an author whenever it sees a new nickname, all authors are imported at level 1, with a default profile and the password 'password'</li>
</ul>
<p>What it does not:</p>
<ul>
	<li>It sucks at making coffee.</li>
	<li>It always forgets to call back.</li>
</ul>

<h2>First step: Install WordPress</h2>
<p>Install the WordPress blog as explained in the <a href="../readme.html">read me</a>, then immediately come back here.</p>

<h3>Second step: let's play with Blogger</h3>
<p>Log into your Blogger account.<br />
Go to the Settings, and make Blogger publish your files in the directory where your WordPress resides. Change the Date/Time format to be mm/dd/yyyy hh:mm:ss AM/PM (the first choice in the dropdown menu). In Archives: set the frequency to 'monthly' and the archive filename to 'wordpress.php' (without the quotes), set the ftp archive path to make Blogger publish the archives in your WordPress directory. Click 'save changes'.<br />
Go to the Templates. Replace your existing template with this line (copy and paste):
<blockquote>&lt;Blogger>&lt;wordpresspost>&lt;$BlogItemDateTime$>|||&lt;$BlogItemAuthorNickname$>|||&lt;$BlogItemBody$>|||&lt;$BlogItemNumber$>|||&lt;$BlogItemSubject$>&lt;/Blogger></blockquote>
Go to the Archives, and click 'republish all'.<br />
Check in your FTP that you've got the archive files published. They should look like this example: <code>2001_10_01_wordpress.php</code>. If they aren't there, redo the republish process.</p>
<p>You're done with the hard part. :)</p>

<form name="stepOne" method="get">
<input type="hidden" name="action" value="step1" />
<h3>Third step: w00t, let's click OK:</h3>
<p>When you're ready, click OK to start importing: <input type="submit" name="submit" value="OK" /><br /><br />
<i>Note: the script might take some time, like 1 second for 100 entries
imported. DO NOT STOP IT or else you won't have a complete import.</i></p>
</form>
</div>
</body>
</html>
	<?php
	break;

}

?>
