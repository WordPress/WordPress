<?php

// *** b2 Archive file

require_once('b2config.php');
require_once($abspath.$b2inc.'/b2functions.php');

dbconnect();

// this is what will separate your archive links
// this is what will separate dates on weekly archive links
$archive_week_separator = '&#8211;';


// archive link url
$archive_link_m = $siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal;	# monthly archive
$archive_link_w = $siteurl.'/'.$blogfilename.$querystring_start.'w'.$querystring_equal;	# weekly archive
$archive_link_p = $siteurl.'/'.$blogfilename.$querystring_start.'p'.$querystring_equal;	# post-by-post archive


// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
$archive_date_format_over_ride = 0;


// options for daily archive (only if you over-ride the general date format)
$archive_day_date_format = 'Y/m/d';

// options for weekly archive (only if you over-ride the general date format)
$archive_week_start_date_format = 'Y/m/d';
$archive_week_end_date_format   = 'Y/m/d';


// --- //


$dateformat=get_settings('date_format');
$time_difference=get_settings('time_difference');

if (!$archive_date_format_over_ride) {
	$archive_day_date_format = $dateformat;
	$archive_week_start_date_format = $dateformat;
	$archive_week_end_date_format   = $dateformat;
}

if (basename($HTTP_SERVER_VARS['SCRIPT_FILENAME']) == 'b2archives.php')
	include ('blog.header.php');

if (!isset($querycount)) {
	$querycount = 0;
}

$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));

if ($archive_mode == 'monthly') {
	$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date) FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC";
	$querycount++;
	$arc_result=mysql_query($arc_sql) or die($arc_sql.'<br />'.mysql_error());
	while($arc_row = mysql_fetch_array($arc_result)) {
		$arc_year  = $arc_row['YEAR(post_date)'];
		$arc_month = $arc_row['MONTH(post_date)'];
		echo "<li><a href=\"$archive_link_m$arc_year".zeroise($arc_month,2).'">';
		echo $month[zeroise($arc_month,2)].' '.$arc_year;
		echo "</a></li>\n";
	}
} elseif ($archive_mode == 'daily') {
	$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC";
	$querycount++;
	$arc_result=mysql_query($arc_sql) or die($arc_sql.'<br />'.mysql_error());
	while($arc_row = mysql_fetch_array($arc_result)) {
		$arc_year  = $arc_row['YEAR(post_date)'];
		$arc_month = $arc_row['MONTH(post_date)'];
		$arc_dayofmonth = $arc_row['DAYOFMONTH(post_date)'];
		echo "<li><a href=\"$archive_link_m$arc_year".zeroise($arc_month,2).zeroise($arc_dayofmonth,2).'">';
		echo mysql2date($archive_day_date_format, $arc_year.'-'.zeroise($arc_month,2).'-'.zeroise($arc_dayofmonth,2).' 00:00:00');
		echo "</a></li>\n";
	}
} elseif ($archive_mode == 'weekly') {
	if (!isset($start_of_week)) {
		$start_of_week = 1;
	}
	$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date), WEEK(post_date) FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC";
	$querycount++;
	$arc_result=mysql_query($arc_sql) or die($arc_sql.'<br />'.mysql_error());
	$arc_w_last = '';
	while($arc_row = mysql_fetch_array($arc_result)) {
		$arc_year = $arc_row['YEAR(post_date)'];
		$arc_w = $arc_row['WEEK(post_date)'];
		if ($arc_w != $arc_w_last) {
			$arc_w_last = $arc_w;
			$arc_ymd = $arc_year.'-'.zeroise($arc_row['MONTH(post_date)'],2).'-' .zeroise($arc_row['DAYOFMONTH(post_date)'],2);
			$arc_week = get_weekstartend($arc_ymd, $start_of_week);
			$arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
			$arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
			echo "<li><a href=\"$siteurl/".$blogfilename."?m=$arc_year&amp;w=$arc_w\">";
			echo $arc_week_start.$archive_week_separator.$arc_week_end;
			echo "</a></li>\n";
		}
	}
} elseif ($archive_mode == 'postbypost') {
	$requestarc = " SELECT ID,post_date,post_title FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC";
	$querycount++;
	$resultarc = mysql_query($requestarc);
	while($row=mysql_fetch_object($resultarc)) {
		if ($row->post_date != '0000-00-00 00:00:00') {
			echo "<li><a href=\"$archive_link_p".$row->ID.'">';
			$arc_title = stripslashes($row->post_title);
			if ($arc_title) {
				echo strip_tags($arc_title);
			} else {
				echo $row->ID;
			}
			echo "</a></li>\n";
		}
	}
}

# echo $querycount."<br />\n";
# timer_stop(1,8);
?>