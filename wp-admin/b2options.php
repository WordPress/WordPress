<?php
$title = "Options";

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
} 

if (!get_magic_quotes_gpc()) {
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$b2varstoreset = array('action','standalone');
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

switch($action) {

case "update":

	$standalone = 1;
	include ("./b2header.php");

	$newposts_per_page=addslashes($HTTP_POST_VARS["newposts_per_page"]);
	$newwhat_to_show=addslashes($HTTP_POST_VARS["newwhat_to_show"]);
	$newarchive_mode=addslashes($HTTP_POST_VARS["newarchive_mode"]);
	$newtime_difference=addslashes($HTTP_POST_VARS["newtime_difference"]);
	//no longer use this? $newautobr=addslashes($HTTP_POST_VARS["newautobr"]);
    $newautobr = 0;
	$newtime_format=addslashes($HTTP_POST_VARS["newtime_format"]);
	$newdate_format=addslashes($HTTP_POST_VARS["newdate_format"]);
	
	$query = "UPDATE $tablesettings SET posts_per_page=$newposts_per_page, what_to_show='$newwhat_to_show', archive_mode='$newarchive_mode', time_difference=$newtime_difference, AutoBR=$newautobr, time_format='$newtime_format', date_format='$newdate_format' WHERE ID = 1";
	$result = mysql_query($query);
	if ($result==false) {
		$oops = "<b>ERROR</b>: couldn't update the options... please contact the <a href=\"mailto:$admin_email\">webmaster</a> !<br />$query<br />".mysql_errno().": ".mysql_error();
		die ($oops);
	}
	
	header ("Location: b2options.php");

break;

default:

	$standalone=0;
	include ("./b2header.php");
	if ($user_level <= 3) {
		die("You have no right to edit the options for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}
	?>
	
			<form name="form" action="b2options.php" method="post">
			<input type="hidden" name="action" value="update" />
	
<div class="wrap">
			
  <table width="550" cellpadding="5" cellspacing="0">
    <tr height="40"> 
      <td width="150" height="40">Show:</td>
      <td width="350"><input type="text" name="newposts_per_page" value="<?php echo get_settings("posts_per_page") ?>" size="3"> 
        <select name="newwhat_to_show">
          <option value="days" <?php
				$i = $what_to_show;
				if ($i == "days")
				echo " selected";
				?>>days</option>
          <option value="posts" <?php
				if ($i == "posts")
				echo " selected";
				?>>posts</option>
          <option value="paged" <?php
				if ($i == "paged")
				echo " selected";
				?>>posts paged</option>
        </select> </td>
    </tr>
    <tr height="40"> 
      <td height="40">Archive mode:</td>
      <td><select name="newarchive_mode">
          <?php $i = $archive_mode; ?>
          <option value="daily"<?php
				if ($i == "daily")
				echo " selected";
				?>>daily</option>
          <option value="weekly"<?php
				if ($i == "weekly")
				echo " selected";
				?>>weekly</option>
          <option value="monthly"<?php
				if ($i == "monthly")
				echo " selected";
				?>>monthly</option>
          <option value="postbypost"<?php
				if ($i == "postbypost")
				echo " selected";
				?>>post by post</option>
        </select> </tr>
    <tr height="40"> 
      <td height="40">Time difference:</td>
      <td><input type="text" name="newtime_difference" value="<?php echo $time_difference ?>" size="2"> 
        <i> if you're not on the timezone of your server</i> </td>
    </tr>
    <tr height="40"> 
      <td height="40">Date format:</td>
      <td><input type="text" name="newdate_format" value="<?php echo $date_format ?>" size="10"> 
        <i> (<a href="#dateformat">note</a>)</i> </td>
    </tr>
    <tr height="40"> 
      <td height="40">Time format:</td>
      <td><input type="text" name="newtime_format" value="<?php echo $time_format ?>" size="10"> 
        <i> (<a href="#dateformat">note</a>)</i> </td>
    </tr>
    <tr height="40"> 
      <td height="40">&nbsp;</td>
      <td> <input type="submit" name="submit" value="Update" class="search"> </td>
    </tr>
  </table>

</div>
	
		</form>

<div class="wrap">
<h2 id="dateformat">
About Date & Time formats:
</h2>
<p> You can format the date & time in many ways, using the PHP syntax.<br />
  As quoted from the PHP manual, here are the letters you can use:<br />
</p>
<blockquote>
		The following characters are recognized in the format string:<br />
		a - "am" or "pm"<br />
		A - "AM" or "PM"<br />
		B - Swatch Internet time<br />
		d - day of the month, 2 digits with leading zeros; i.e. "01" to "31"<br />
		D - day of the week, textual, 3 letters; i.e. "Fri"<br />
		F - month, textual, long; i.e. "January"<br />
		g - hour, 12-hour format without leading zeros; i.e. "1" to "12"<br />
		G - hour, 24-hour format without leading zeros; i.e. "0" to "23"<br />
		h - hour, 12-hour format; i.e. "01" to "12"<br />
		H - hour, 24-hour format; i.e. "00" to "23"<br />
		i - minutes; i.e. "00" to "59"<br />
		I (capital i) - "1" if Daylight Savings Time, "0" otherwise.<br />
		j - day of the month without leading zeros; i.e. "1" to "31"<br />
		l (lowercase 'L') - day of the week, textual, long; i.e. "Friday"<br />
		L - boolean for whether it is a leap year; i.e. "0" or "1"<br />
		m - month; i.e. "01" to "12"<br />
		M - month, textual, 3 letters; i.e. "Jan"<br />
		n - month without leading zeros; i.e. "1" to "12"<br />
		r - RFC 822 formatted date; i.e. "Thu, 21 Dec 2000 16:01:07 +0200" (added in PHP 4.0.4)<br />
		s - seconds; i.e. "00" to "59"<br />
		S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd"<br />
		t - number of days in the given month; i.e. "28" to "31"<br />
		T - Timezone setting of this machine; i.e. "MDT"<br />
		U - seconds since the epoch<br />
		w - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)<br />
		Y - year, 4 digits; i.e. "1999"<br />
		y - year, 2 digits; i.e. "99"<br />
		z - day of the year; i.e. "0" to "365"<br />
		Z - timezone offset in seconds (i.e. "-43200" to "43200"). The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.<br />
		<br />
		Unrecognized characters in the format string will be printed as-is.
		</blockquote>
		
<p>For more information and examples, check the PHP manual on <a href="http://www.php.net/manual/en/function.date.php">this 
  page</a>.</p>
  </div>
<?php

break;
}

include("b2footer.php") ?>