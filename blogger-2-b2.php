<?php // rename this to blogger-2-b2.php

$b2varstoreset = array('action');
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

	require("b2config.php");
	require("$b2inc/b2functions.php");
	require("$b2inc/b2vars.php");
	
?>
<html>
<head>
<title>blogger 2 b2 - converting...</title>
<link rel="stylesheet" href="b2.css" type="text/css">
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
<p><font face="times new roman" style="font-size: 39px;">blogger 2 <img src="b2-img/b2minilogo.png" width="50" height="50" border="0" alt="b2" align="top" /></font></p>
<p>The importer is running...</p>
<ul>
<?php

	$connexion = mysql_connect($server,$loginsql,$passsql) or die ("Oops, MySQL connection error ! Couldn't connect to $server with the username $loginsql");  
	$bdd = mysql_select_db($dbname,$connexion) or die ("Oops, can't find any database named $dbname here !"); 

?>
<li>importing posts and users<br /><ul><?php

	for($bgy=1999; $bgy<=(date("Y")); $bgy++) {
		for($bgm=1; $bgm<13; $bgm++) {

		$bgmm=zeroise($bgm,2);
	
		$archivefile = "$bgy"."_"."$bgmm"."_01_cafelog.php";
		
		if (file_exists($archivefile)) {

			$f=fopen($archivefile,"r");
			$archive=fread($f,filesize($archivefile));
			fclose($f);
			echo "<li>$bgy/$bgmm ";

			$posts = explode("<cafelogpost>",$archive);

			for ($i = 1; $i < (count($posts)+1); $i = $i + 1) {

			$postinfo=explode("|||",$posts[$i]);
			$post_date=$postinfo[0];
			$post_content=$postinfo[2];
			$post_number=$postinfo[3];

			$post_author=trim(addslashes($postinfo[1]));
			// we'll check the author is registered already
			$sql = "SELECT * FROM $tableusers WHERE user_login = '$post_author'";
			$result = mysql_query($sql);
			if (!mysql_num_rows($result)) { // seems s/he's not, so let's register
				$user_ip="127.0.0.1";
				$user_domain="localhost";
				$user_browser="server";
				$user_joindate="1979-06-06 00:41:00"; // that's my birthdate (gmt+1) - I could choose any other date. You could change the date too. Just remember the year must be >=1970 or the world would just randomly fall on your head (everything might look fine, and then blam! major headache!)
				$user_login=addslashes($post_author);
				$pass1=addslashes("password");
				$user_nickname=addslashes($post_author);
				$user_email=addslashes("user@cafelog.com");
				$user_url=addslashes("");
				$user_joindate=addslashes($user_joindate);
				$query = "INSERT INTO $tableusers (user_login,user_pass,user_nickname,user_email,user_url,user_ip,user_domain,user_browser,dateYMDhour,user_level,user_idmode) VALUES ('$user_login','$pass1','$user_nickname','$user_email','$user_url','$user_ip','$user_domain','$user_browser','$user_joindate','1','nickname')";
				$result = mysql_query($query);
				if ($result==false) {
					die ("<b>ERROR</b>: couldn't register an user... please contact the <a href=\"mailto:$admin_email\">webmaster</a> !");
				}
				echo ": registered user <i>$user_login</i>";
			}

			$sql = "SELECT * FROM $tableusers WHERE user_login = '$post_author'";
			$result = mysql_query($sql);
			$myrow = mysql_fetch_array($result);
			$post_author_ID=$myrow[0];

			$post_date = explode(" ",$post_date);
			$post_date_Ymd = explode("/", $post_date[0]);
			$postyear=$post_date_Ymd[2];
			$postmonth=zeroise($post_date_Ymd[0],2);
			$postday=zeroise($post_date_Ymd[1],2);
			$post_date_His = explode(":", $post_date[1]);
			$posthour=zeroise($post_date_His[0],2);
			$postminute=zeroise($post_date_His[1],2);
			$postsecond=zeroise($post_date_His[2],2);

			if (($post_date[2]=="PM") && ($posthour!="12"))
				$posthour=$posthour+12;

			$post_date="$postyear-$postmonth-$postday $posthour:$postminute:$postsecond";

			$post_content=addslashes($post_content);
			$post_content=str_replace("<br>","<br />",$post_content); // the XHTML touch... ;)

			$post_title="";

			$query = "INSERT INTO $tableposts (ID, post_author,post_date,post_content,post_title,post_category) VALUES ('$post_number','$post_author_ID','$post_date','$post_content','$post_title','1')";
			$result = mysql_query($query) or die(mysql_error());

			if (!$result)
				die ("Error in posting... contact the <a href=\"mailto:$admin_email\">webmaster</a>");
			

			} echo "... <b>Done</b></li>";
			
		}}
	}

	/* we've still got a bug that adds some empty posts with the date 0000-00-00 00:00:00
	   here's the bugfix: */
	$query="DELETE FROM $tableposts WHERE post_date=\"0000-00-00 00:00:00\"";
	$result = mysql_query($query) or die(mysql_error());


	?>
</ul><b>Done</b></li></ul>
<p>&nbsp;</p>
<p>Completed Blogger 2 b2 import !</p>
<p>Now you can go and <a href="b2login.php">log in</a>, have fun !</p>
	<?php
	break;






default:

	?><html>
<head>
<title>blogger 2 b2 importer utility</title>
<link rel="stylesheet" href="b2.css" type="text/css">
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
<p><font face="times new roman" style="font-size: 39px;">blogger 2 <img src="b2-img/b2minilogo.png" width="50" height="50" border="0" alt="b2" align="top" /></font></p>
<p>This is a basic Blogger to b2 import script.</p>
<p>What it does:</p>
<ul>
<li>parses your archives to retrieve your blogger posts</li>
<li>adds an author whenever it sees a new nickname, all authors are imported at level 1, with a default profile and the password 'password'</li>
</ul>
<p>What it does not:</p>
<ul>
<li>it sucks at making coffee, and is not toilet-trained yet</li>
</ul>
<p>&nbsp;</p>

<h3>First step: install b2</h3>
<p>Install the b2 blog as explained in the <a href="readme.html" target="_blank">ReadMe</a>, then immediately come back here.</p>

<h3>Second step: let's play with Blogger</h3>
<p>Log into your Blogger account.<br />
Go to the Settings, and make Blogger publish your files in the directory where your b2 resides. Change the Date/Time format to be mm/dd/yyyy hh:mm:ss AM/PM (the first choice in the dropdown menu). In Archives: set the frequency to 'monthly' and the archive filename to 'cafelog.php' (without the quotes), set the ftp archive path to make Blogger publish the archives in your b2 directory. Click 'save changes'.<br />
Go to the Templates. Replace your existing template with this line (copy and paste):
<blockquote>&lt;Blogger>&lt;cafelogpost>&lt;$BlogItemDateTime$>|||&lt;$BlogItemAuthorNickname$>|||&lt;$BlogItemBody$>|||&lt;$BlogItemNumber$>&lt;/Blogger></blockquote>
Go to the Archives, and click 'republish all'.<br />
Check in your FTP that you've got the archive files published. They should look like this example: <i>2001_10_01_cafelog.php</i>. If they aren't there, redo the republish process.<br /><br />You're done with the hard part. :)</p>

<form name="stepOne" method="get">
<input type="hidden" name="action" value="step1" />
<h3>Third step: w00t, let's click OK:</h3>
<p>When you're ready, click OK to start importing: <input type="submit" name="submit" value="OK" class="search" /><br /><br />
<i>Note: the script might take some time, like 1 second for 100 entries imported. DO NOT STOP IT or else you won't have a complete import, and running the script again might just make you have double posts.</i></p>
</form>

</body>
</html>
	<?php
	break;

}

?>