<?php

// $Id$
// b2 Calendar
//
// Contributed work by:
// Alex King
// http://www.alexking.org
//
// Mike Little for his bug fixes
// http://zed1.com/b2/
//
// Fred Cooper for querystring bugfix
// http://frcooper.com/journal/
//
// b2 is copyright (c) 2001, 2002, 2003 by Michel Valdrighi - m@tidakada.com
//
// Contributed portions copyright (c) various authors with permission
//

// original arrow hack by Alex King
$ak_use_arrows = 1;  // set to 0 to hide the arrows
$ak_use_tooltip_titles = 1;  // set to 0 to hide the tooltip titles

/* customize these as you wish */

$calendarmonthdisplay = 1;	// set this to 0 if you don't want to display the month name
$calendarmonthformat = 'F Y';
$calendarmonthstart = '<caption class="b2calendarmonth">';
$calendarmonthend = '</caption>';

$calendartablestart = '<table class="b2calendartable" summary="Monthly calendar with links to each day\'s posts">';
$calendartableend = '</table>';

$calendarrowstart = '<tr class="b2calendarrow">';
$calendarrowend = '</tr>';

$calendarheaderdisplay = 1;	// set this to 0 if you don't want to display the "Mon Tue Wed..." header
$calendarheadercellstart = '<th class="b2calendarheadercell" abbr="$abbr">';	// please leave $abbr there !
$calendarheadercellend = '</th>';
$calendarheaderabbrlength = 1;	// length of the shortened weekday

$calendarcellstart = '<td class="b2calendarcell">';
$calendarcellend = '</td>';

$calendaremptycellstart = '<td class="b2calendaremptycell">';
$calendaremptycellend = '</td>';

$calendaremptycellcontent = '&nbsp;';

/* stop customizing (unless you really know what you're doing) */


require('b2config.php');
require_once($abspath.$b2inc.'/b2template.functions.php');
require_once($abspath.$b2inc.'/b2functions.php');
require_once($abspath.$b2inc.'/b2vars.php');
require_once($curpath.$b2inc.'/wp-db.php');

if (isset($calendar) && ($calendar != '')) {
	$thisyear = substr($calendar,0,4);
	$thismonth = substr($calendar,4,2);
} else {
	if (isset($m) && ($m != '')) {
		$calendar = substr($m,0,6);
		$thisyear = substr($m,0,4);
		if (strlen($m) < 6) {
			$thismonth = '01';
		} else {
			$thismonth = substr($m,4,2);
		}
	} else {
		$thisyear = date('Y', time()+($time_difference * 3600));
		$thismonth = date('m', time()+($time_difference * 3600));
	}
}

$archive_link_m = $siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal;

// original arrow hack by Alex King
$ak_previous_year = $thisyear;
$ak_next_year = $thisyear;
$ak_previous_month = $thismonth - 1;
$ak_next_month = $thismonth + 1;
if ($ak_previous_month == 0) {
    $ak_previous_month = 12;
    --$ak_previous_year;
}
if ($ak_next_month == 13) {
    $ak_next_month = 1;
    ++$ak_next_year;
}

$ak_first_post = $wpdb->get_row("SELECT MONTH(MIN(post_date)) AS min_month, YEAR(MIN(post_date)) AS min_year FROM $tableposts");
// using text links by default
$ak_previous_month_dim = '<span>&lt;</span>&nbsp;&nbsp;';
$ak_previous_month_active = '<a href="'.$archive_link_m.$ak_previous_year.zeroise($ak_previous_month,2).'" style="text-decoration: none;">&lt;</a>&nbsp;&nbsp;';
$ak_next_month_dim = '&nbsp;&nbsp;<span>&gt;</span>';
$ak_next_month_active = '&nbsp;&nbsp;<a href="'.$archive_link_m.$ak_next_year.zeroise($ak_next_month,2).'" style="text-decoration: none;">&gt;</a>';
if ($ak_use_arrows == 1) {
    if (mktime(0,0,0,$ak_previous_month,1,$ak_previous_year) < mktime(0,0,0,$ak_first_post->min_month,1,$ak_first_post->min_year)) {
        $ak_previous_month_link = $ak_previous_month_dim;
	} else {
        $ak_previous_month_link = $ak_previous_month_active;
	}
	
	if (mktime(0,0,0,$ak_next_month,1,$ak_next_year) > mktime()) {
		$ak_next_month_link = $ak_next_month_dim;
	} else {
		$ak_next_month_link = $ak_next_month_active;
	}
} else {
	$ak_previous_month_link = "";
	$ak_next_month_link = "";
}

$end_of_week = (($start_of_week + 7) % 7);

$calendarmonthwithpost = 0;
while($calendarmonthwithpost == 0) {
	$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) AS dom FROM $tableposts WHERE MONTH(post_date) = '$thismonth' AND YEAR(post_date) = '$thisyear' ORDER BY post_date DESC";
	$querycount++;
    $arc_results = $wpdb->get_results($arc_sql);
	if ($wpdb->num_rows > 0) {
        $daysinmonthwithposts = '-';
		foreach ($arc_results as $arc_row) {
			$daysinmonthwithposts .= $arc_row->dom.'-';
		}
		$calendarmonthwithpost = 1;
	} elseif ($calendar != '') {
		$daysinmonthwithposts = '';
		$calendarmonthwithpost = 1;
	} else {
		$thismonth = zeroise(intval($thismonth)-1,2);
		if ($thismonth == '00') {
			$thismonth = '12';
			$thisyear = ''.(intval($thisyear)-1);
		}
	}
}

$daysinmonth = intval(date('t', mktime(0,0,0,$thismonth,1,$thisyear)));
$datestartofmonth = $thisyear.'-'.$thismonth.'-01';
$dateendofmonth = $thisyear.'-'.$thismonth.'-'.$daysinmonth;

// caution: offset bug inside
$calendarblah = get_weekstartend($datestartofmonth, $start_of_week);
if (mysql2date('w', $datestartofmonth) == $start_of_week) {
	$calendarfirst = $calendarblah['start']+1+3600;	//	adjust for daylight savings time
} else {
	$calendarfirst = $calendarblah['end']-604799+3600;	//	adjust for daylight savings time
}

$calendarblah = get_weekstartend($dateendofmonth, $end_of_week);
if (mysql2date('w', $dateendofmonth) == $end_of_week) {
	$calendarlast = $calendarblah['start']+1;
} else {
	$calendarlast = $calendarblah['end']+10000;
}

$beforethismonth = zeroise(intval($thismonth)-1,2);
$afterthismonth = zeroise(intval($thismonth)-1,2);

// here the offset bug is corrected
if ((intval(date('d', $calendarfirst)) > 1) && (intval(date('m', $calendarfirst)) == intval($thismonth))) {
	$calendarfirst = $calendarfirst - 604800;
}


// displays everything

echo $calendartablestart."\n";

if ($calendarmonthdisplay) {
	echo $calendarmonthstart;
	echo $ak_previous_month_link;
	echo date_i18n($calendarmonthformat, mktime(0, 0, 0, $thismonth, 1, $thisyear));
	echo $ak_next_month_link;
	echo $calendarmonthend."\n";
}

if ($calendarheaderdisplay) {
	echo $calendarrowstart."\n";

	for ($i = $start_of_week; $i<($start_of_week+7); $i = $i + 1) {
		echo str_replace('$abbr', $weekday[($i % 7)], $calendarheadercellstart);
		echo ucwords(substr($weekday[($i % 7)], 0, $calendarheaderabbrlength));
		echo $calendarheadercellend;
	}

	echo $calendarrowend."\n";
}

echo $calendarrowstart."\n";

$newrow = 0;
$j = 0;
$k = 1;

// original tooltip hack by Alex King
if ($ak_use_tooltip_titles == 1) {
	$ak_days_result = $wpdb->get_results("SELECT post_title, post_date FROM $tableposts WHERE YEAR(post_date) = '$thisyear' AND MONTH(post_date) = '$thismonth'");

	$ak_day_title_array = array();
	foreach($ak_days_result as $ak_temp) {
		$ak_day_title_array[] = $ak_temp;
	}
	if (strstr($HTTP_SERVER_VARS["HTTP_USER_AGENT"], "MSIE")) {
		$ak_title_separator = "\n";
		$ak_trim = 1;
	}
	else {
		$ak_title_separator = ", ";
		$ak_trim = 2;
	}
}


for($i = $calendarfirst; $i<($calendarlast+86400); $i = $i + 86400) {
	if ($newrow == 1) {
		if ($k > $daysinmonth) {
			break;
		}
		echo $calendarrowend."\n";
		if (($i+86400) < ($calendarlast+86400)) {
			echo $calendarrowstart."\n";
		}
		$newrow = 0;
	}
	if (date('m',$i) != $thismonth) {
		echo $calendaremptycellstart;
		echo $calendaremptycellcontent;
		echo $calendaremptycellend;
	} else {
		$k = $k + 1;
		echo $calendarcellstart;
		$calendarblah = '-'.date('j',$i).'-';
		$calendarthereisapost = ereg($calendarblah, $daysinmonthwithposts);
		$calendartoday = (date('Ymd',$i) == date('Ymd', (time() + ($time_difference * 3600))));

		if ($calendarthereisapost) {
			// original tooltip hack by Alex King 
			if ($ak_use_tooltip_titles == 1) { // check to see if we want to show the tooltip titles
				$ak_day_titles = "";
				foreach($ak_day_title_array as $post) {
					if (substr($post->post_date, 8, 2) == date('d',$i)) {
						$ak_day_titles = $ak_day_titles.stripslashes($post->post_title).$ak_title_separator;
					}
				}
				$ak_day_titles = substr($ak_day_titles, 0, strlen($ak_day_titles) - $ak_trim);
				echo '<a href="'.$siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal.$thisyear.$thismonth.date('d',$i).'" class="b2calendarlinkpost" title="'.$ak_day_titles.'">';
			}
			else {
				echo '<a href="'.$siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal.$thisyear.$thismonth.date('d',$i).'" class="b2calendarlinkpost">'; 
			}
		}
		if ($calendartoday) {
			echo '<span class="b2calendartoday">';
		}
		echo date('j',$i);
		if ($calendartoday) {
			echo '</span>';
		}
		if ($calendarthereisapost) {
			echo '</a>';
		}
		echo $calendarcellend."\n";
	}
	$j = $j + 1;
	if ($j == 7) {
		$j = 0;
		$newrow = 1;
	}
}

echo $calendarrowend."\n";
echo $calendartableend;

?>
