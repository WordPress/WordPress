<?php

// 1. save as gm-2-b2.php
// 2. upload on your server in the directory where your b2 files are
// 3. load in the browser from there

require_once('wp-config.php');
require_once($abspath.$b2inc.'/functions.php');

$b2varstoreset = array('action', 'gmpath', 'archivespath');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}


switch ($action) {

case "step1":

	function gm2autobr($string) { // transforms GM's |*| into b2's <br />\n
		$string = str_replace("|*|","<br />\n",$string);
		return($string);
	}

	if (!chdir($archivespath))
		alert_error("Wrong path, $archivespath\ndoesn't exist\non the server");

	if (!chdir($gmpath))
		alert_error("Wrong path, $gmpath\ndoesn't exist\non the server");
?>
<html>
<head>
<title>GM 2 b2 - converting...</title>
<link rel="stylesheet" href="wp-admin/b2.css" type="text/css">
<style type="text/css">
<!--
<?php
if (!preg_match("/Nav/",$HTTP_USER_AGENT)) {
?>
textarea,input,select {
	background-color: #f0f0f0;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php
}
?>
-->
</style>
</head>
<body style="margin: 20px;">
<p><font face="times new roman" style="font-size: 39px;">gm 2 <img src="wp-images/wpminilogo.png" width="50" height="50" border="0" alt="WP" align="top" /></font></p>
<p>The importer is running...</p>
<ul>
<li>importing users... <ul><?php

	chdir($gmpath);
	$userbase = file("gm-authors.cgi");

	$connexion = mysql_connect($server,$loginsql,$passsql) or die ("Oops, MySQL connection error ! Couldn't connect to $server with the username $loginsql");  
	$bdd = mysql_select_db($dbname,$connexion) or die ("Oops, can't find any database named $dbname here !"); 

	foreach($userbase as $user) {
		$userdata=explode("|", $user);

		$user_ip="127.0.0.1";
		$user_domain="localhost";
		$user_browser="server";

		$s=$userdata[4];
		$user_joindate=substr($s,6,4)."-".substr($s,0,2)."-".substr($s,3,2)." 00:00:00";

		$user_login=addslashes($userdata[0]);
		$pass1=addslashes($userdata[1]);
		$user_nickname=addslashes($userdata[0]);
		$user_email=addslashes($userdata[2]);
		$user_url=addslashes($userdata[3]);
		$user_joindate=addslashes($user_joindate);

		$query = "INSERT INTO $tableusers (user_login,user_pass,user_nickname,user_email,user_url,user_ip,user_domain,user_browser,dateYMDhour,user_level,user_idmode) VALUES ('$user_login','$pass1','$user_nickname','$user_email','$user_url','$user_ip','$user_domain','$user_browser','$user_joindate','1','nickname')";
		$result = mysql_query($query);
		if ($result==false) {
			die ("<b>ERROR</b>: couldn't register an user... please contact the <a href=\"mailto:$admin_email\">webmaster</a> !");
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
			$sql = "SELECT * FROM $tableusers WHERE user_login = '$post_author'";
			$result = mysql_query($sql);
			if (!mysql_num_rows($result)) { // if deleted from GM, we register the author as a level 0 user in b2
				$user_ip="127.0.0.1";
				$user_domain="localhost";
				$user_browser="server";
				$user_joindate="1979-06-06 00:41:00";
				$user_login=addslashes($post_author);
				$pass1=addslashes("password");
				$user_nickname=addslashes($post_author);
				$user_email=addslashes("user@deleted.com");
				$user_url=addslashes("");
				$user_joindate=addslashes($user_joindate);
				$query = "INSERT INTO $tableusers (user_login,user_pass,user_nickname,user_email,user_url,user_ip,user_domain,user_browser,dateYMDhour,user_level,user_idmode) VALUES ('$user_login','$pass1','$user_nickname','$user_email','$user_url','$user_ip','$user_domain','$user_browser','$user_joindate','0','nickname')";
				$result = mysql_query($query);
				if ($result==false) {
					die ("<b>ERROR</b>: couldn't register an user... please contact the <a href=\"mailto:$admin_email\">webmaster</a> !");
				}
				echo ": registered deleted user <i>$user_login</i> at level 0 ";
			}

			$sql = "SELECT * FROM $tableusers WHERE user_login = '$post_author'";
			$result = mysql_query($sql);
			$myrow = mysql_fetch_array($result);
			$post_author_ID=$myrow[0];

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

			$post_category="1";

			$post_content=$postmaincontent;
			if (strlen($postmorecontent)>3)
				$post_content .= "<!--more--><br /><br />".$postmorecontent;
			$post_content=addslashes($post_content);

			$post_karma=$postinfo[12];

			
			$query = "INSERT INTO $tableposts (post_author,post_date,post_content,post_title,post_category,post_karma) VALUES ('$post_author_ID','$post_date','$post_content','$post_title','1','$post_karma')";
			$result = mysql_query($query) or die(mysql_error());

			if (!$result)
				die ("Error in posting... contact the <a href=\"mailto:$admin_email\">webmaster</a>");
			
			$sql2 = "SELECT * FROM $tableposts WHERE 1=1 ORDER BY ID DESC LIMIT 1";
			$result2 = mysql_query($sql2);
			$myrow2 = mysql_fetch_array($result2);
			$post_ID=$myrow2[0];


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

					$sql3 = "INSERT INTO $tablecomments (comment_post_ID,comment_author,comment_author_email,comment_author_url,comment_author_IP,comment_date,comment_content) VALUES ('$comment_post_ID','$comment_author','$comment_author_email','$comment_author_url','$comment_author_IP','$comment_date','$comment_content')";
					$result3 = mysql_query($sql3);
					if (!$result3)
						die ("There is an error with the database, it can't store your comment...<br>Contact the <a href=\"mailto:$admin_email\">webmaster</a>");
				}
				$comments=$c-4;
				echo ": imported $comments comment";
				if ($comments>1)
					echo "s";
			}
			echo "... <b>Done</b></li>";
		}
	} ?>
</ul><b>Done</b></li></ul>
<p>&nbsp;</p>
<p>Completed GM 2 b2 import !</p>
<p>Now you can go and <a href="wp-login.php">log in</a>, have fun !</p>
	<?php
	break;






default:

	?><html>
<head>
<title>GM 2 b2 importer utility</title>
<link rel="stylesheet" href="wp-admin/b2.css" type="text/css">
<style type="text/css">
<!--
<?php
if (!preg_match("/Nav/",$HTTP_USER_AGENT)) {
?>
textarea,input,select {
	background-color: #f0f0f0;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php
}
?>
-->
</style>
</head>
<body style="margin: 20px;">
<p><font face="times new roman" style="font-size: 39px;">gm 2 <img src="wp-images/wpminilogo.png" width="50" height="50" border="0" alt="WP" align="top" /></font></p>
<p>This is a basic GreyMatter to b2 import script.</p>
<p>What it does:</p>
<ul>
<li>parses gm-authors.cgi to import authors: everyone is imported at level 1</li>
<li>parses the entries cgi files to import posts, comments, and karma on posts (although karma is not used on b2 yet)<br />if authors are found not to be in gm-authors.cgi, imports them at level 0</li>
</ul>
<p>What it does not:</p>
<ul>
<li>parse gm-counter.cgi (what's the use of that file ?), gm-banlist.cgi, gm-cplog.cgi (you can make a CP log hack if you really feel like it, but I question the need of a CP log)</li>
<li>import gm-templates. you'll start with the basic template b2.php</li>
<li>doesn't keep entries on top</li>
</ul>
<p>&nbsp;</p>

<h3>First step: install b2</h3>
<p>Install the b2 blog as explained in the <a href="readme.html" target="_blank">ReadMe</a>, then immediately come back here.</p>

<form name="stepOne" method="get">
<input type="hidden" name="action" value="step1" />
<h3>Second step: GreyMatter details:</h3>
<p><table cellpadding="0">
<tr>
<td>Path to GM files:</td>
<td><input type="text" style="width:300px" name="gmpath" value="/home/my/site/cgi-bin/greymatter/" /></td>
</tr>
<tr>
<td>Path to GM entries:</td>
<td><input type="text" style="width:300px" name="archivespath" value="/home/my/site/cgi-bin/greymatter/archives/" /></td>
</tr>
<tr>
<td colspan="2"><br />This importer will search for files 00000001.cgi to 000-whatever.cgi,<br />so you need to enter the number of the last GM post here.<br />(if you don't know that number, just log into your FTP and look it out<br />in the entries' folder)</td>
</tr>
<tr>
<td>Last entry's number:</td>
<td><input type="text" name="lastentry" value="00000001" /></td>
</tr>
</table>
</p>
<p>When you're ready, click OK to start importing: <input type="submit" name="submit" value="OK" class="search" /></p>
</form>

</body>
</html>
	<?php
	break;

}

?>