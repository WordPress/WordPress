<?php

/* new and improved ! now with more querystring stuff ! */

if (!isset($querystring_start)) {
	$querystring_start = '?';
	$querystring_equal = '=';
	$querystring_separator = '&amp;';
}

/* template functions... */


// @@@ These are template tags, you can edit them if you know what you're doing...



/***** About-the-blog tags *****/
/* Note: these tags go anywhere in the template */

function bloginfo($show='') {
	$info = get_bloginfo($show);
	$info = convert_bbcode($info);
	$info = convert_gmcode($info);
	$info = convert_smilies($info);
	$info = apply_filters('bloginfo', $info);
	echo convert_chars($info, 'html');
}

function bloginfo_rss($show='') {
	$info = strip_tags(get_bloginfo($show));
	echo convert_chars($info, 'unicode');
}

function bloginfo_unicode($show='') {
	$info = get_bloginfo($show);
	echo convert_chars($info, 'unicode');
}

function get_bloginfo($show='') {
	global $siteurl, $blogfilename, $blogname, $blogdescription, $siteurl, $admin_email;
	switch($show) {
		case "url":
			$output = $siteurl."/".$blogfilename;
			break;
		case "description":
			$output = $blogdescription;
			break;
		case "rdf_url":
			$output = $siteurl.'/wp-rdf.php';
			break;
		case "rss_url":
			$output = $siteurl.'/wp-rss.php';
			break;
		case "rss2_url":
			$output = $siteurl.'/wp-rss2.php';
			break;
		case "atom_url":
			$output = $siteurl.'/wp-atom.php';
			break;		
		case "comments_rss2_url":
			$output = $siteurl.'/wp-commentsrss2.php';
			break;
		case "pingback_url":
			$output = $siteurl.'/xmlrpc.php';
			break;
		case "admin_email":
			$output = $admin_email;
			break;
		case "name":
		default:
			$output = $blogname;
			break;
	}
	return $output;
}

function wp_title($sep = '&raquo;', $display = true) {
	global $wpdb, $tableposts, $tablecategories;
	global $year, $monthnum, $day, $cat, $p, $name;

	// If there's a category
	if(!empty($cat)) {
		$title = stripslashes(get_the_category_by_ID($cat));
	}
	if (!empty($category_name)) {
		$title = stripslashes($wpdb->get_var("SELECT cat_name FROM $tablecategories WHERE category_nicename = '$category_name'"));
	}

	// If there's a month
	if(!empty($m)) {
		$my_year = substr($m, 0, 4);
		$my_month = $month[substr($m, 4, 2)];
		$title = "$my_year $sep $my_month";

	}
	if (!empty($year)) {
		$title = $year;
		if (!empty($monthnum)) {
			$title .= " $sep $monthnum";
		}
		if (!empty($day)) {
			$title .= " $sep $day";
		}
	}

	// If there's a post
	if (intval($p) || '' != $name) {
		if (!$p) {
		if ($year != '') {
			$year = '' . intval($year);
			$where .= ' AND YEAR(post_date)=' . $year;
		}
		
		if ($monthnum != '') {
			$monthnum = '' . intval($monthnum);
			$where .= ' AND MONTH(post_date)=' . $monthnum;
		}
		
		if ($day != '') {
			$hay = '' . intval($day);
			$where .= ' AND DAYOFMONTH(post_date)=' . $day;
		}
			$p = $wpdb->get_var("SELECT ID FROM $tableposts WHERE post_name = '$name' $where");
		}
		$post_data = get_postdata($p);
		$title = strip_tags(stripslashes($post_data['Title']));
		$title = apply_filters('single_post_title', $title);
	}

	// Send it out
	if ($display && isset($title)) {
		echo " $sep $title";
	} elseif (!$display && isset($title)) {
		return " $sep $title";
	}
}

function single_post_title($prefix = '', $display = true) {
	global $p, $name, $wpdb, $tableposts;
	if (intval($p) || '' != $name) {
		if (!$p) {
			$p = $wpdb->get_var("SELECT ID FROM $tableposts WHERE post_name = '$name'");
		}
		$post_data = get_postdata($p);
		$title = $post_data['Title'];
		$title = apply_filters('single_post_title', $title);
		if ($display) {
			echo $prefix.strip_tags(stripslashes($title));
		} else {
			return strip_tags(stripslashes($title));
		}
	}
}

function single_cat_title($prefix = '', $display = true ) {
	global $cat;
	if(!empty($cat) && !(strtoupper($cat) == 'ALL')) {
		$my_cat_name = get_the_category_by_ID($cat);
		if(!empty($my_cat_name)) {
			if ($display)
				echo $prefix.strip_tags(stripslashes($my_cat_name));
			else
				return strip_tags(stripslashes($my_cat_name));
		}
	}
}

function single_month_title($prefix = '', $display = true ) {
	global $m, $month;
	if(!empty($m)) {
		$my_year = substr($m,0,4);
		$my_month = $month[substr($m,4,2)];
		if ($display)
			echo $prefix.$my_month.$prefix.$my_year;
		else
			return $m;
	}
}

/* link navigation hack by Orien http://icecode.com/ */
function get_archives_link($url, $text, $format = "html", $before = "", $after = "") {
	if ('link' == $format) {
		return "\t".'<link rel="archives" title="'.$text.'" href="'.$url.'" />'."\n";
	} else if ('option' == $format) {
		return '<option value="'.$url.'">'.$text.'</option>'."\n";
	} else if ('html' == $format) {
		return "\t".'<li><a href="'.$url.'" title="'.$text.'">'.$text.'</a>'.$after.'</li>'."\n";
	} else { // custom
		return "\t".$before.'<a href="'.$url.'" title="'.$text.'">'.$text.'</a>'.$after."\n";
	}
}

function get_archives($type='', $limit='', $format='html', $before = "", $after = "", $show_post_count = false) {
	global $tableposts, $dateformat, $time_difference, $siteurl, $blogfilename;
    global $querystring_start, $querystring_equal, $querystring_separator, $month, $wpdb, $start_of_week;

    if ('' == $type) {
        $type = get_settings('archive_mode');
    }

	if ('' != $limit) {
        $limit = (int) $limit;
		$limit = " LIMIT $limit";
	}
	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// archive link url
	$archive_link_m = $siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal;	# monthly archive;
	$archive_link_w = $siteurl.'/'.$blogfilename.$querystring_start.'w'.$querystring_equal;	# weekly archive;
	$archive_link_p = $siteurl.'/'.$blogfilename.$querystring_start.'p'.$querystring_equal;	# post-by-post archive;

    // over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
    $archive_date_format_over_ride = 0;

    // options for daily archive (only if you over-ride the general date format)
    $archive_day_date_format = 'Y/m/d';

    // options for weekly archive (only if you over-ride the general date format)
    $archive_week_start_date_format = 'Y/m/d';
    $archive_week_end_date_format   = 'Y/m/d';

    if (!$archive_date_format_over_ride) {
        $archive_day_date_format = $dateformat;
        $archive_week_start_date_format = $dateformat;
        $archive_week_end_date_format   = $dateformat;
    }

	$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));

	if ('monthly' == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $tableposts WHERE post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC" . $limit);
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                $url  = get_month_link($arcresult->year,   $arcresult->month);
                if ($show_post_count) {
                    $text = sprintf("%s %d", $month[zeroise($arcresult->month,2)], $arcresult->year);
                    $after = "&nbsp;($arcresult->posts)";
                } else {
                    $text = sprintf("%s %d", $month[zeroise($arcresult->month,2)], $arcresult->year);
                }
                echo get_archives_link($url, $text, $format, $before, $after);
            }
        }
	} elseif ('daily' == $type) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth` FROM $tableposts WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                $url  = get_day_link($arcresult->year, $arcresult->month, $arcresult->dayofmonth);
                $date = sprintf("%d-%02d-%02d 00:00:00", $arcresult->year, $arcresult->month, $arcresult->dayofmonth);
                $text = mysql2date($archive_day_date_format, $date);
                echo get_archives_link($url, $text, $format, $before, $after);
            }
        }
	} elseif ('weekly' == $type) {
		if (!isset($start_of_week)) {
			$start_of_week = 1;
		}
		$arcresults = $wpdb->get_results("SELECT DISTINCT WEEK(post_date, $start_of_week) AS `week`, YEAR(post_date) AS yr, DATE_FORMAT(post_date, '%Y-%m-%d') AS yyyymmdd FROM $tableposts WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		$arc_w_last = '';
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                if ($arcresult->week != $arc_w_last) {
                    $arc_year = $arcresult->yr;
                    $arc_w_last = $arcresult->week;
                    $arc_week = get_weekstartend($arcresult->yyyymmdd, $start_of_week);
                    $arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
                    $arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
                    $url  = sprintf("%s/%s%sm%s%s%sw%s%d", $siteurl, $blogfilename, $querystring_start,
                                    $querystring_equal, $arc_year, $querystring_separator,
                                    $querystring_equal, $arcresult->week);
                    $text = $arc_week_start . $archive_week_separator . $arc_week_end;
                    echo get_archives_link($url, $text, $format, $before, $after);
                }
            }
        }
	} elseif ('postbypost' == $type) {
		$arcresults = $wpdb->get_results("SELECT ID, post_date, post_title FROM $tableposts WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
        if ($arcresults) {
            foreach ($arcresults as $arcresult) {
                if ($arcresult->post_date != '0000-00-00 00:00:00') {
                    $url  = get_permalink($arcresult->ID);
                    $arc_title = stripslashes($arcresult->post_title);
                    if ($arc_title) {
                        $text = strip_tags($arc_title);
                    } else {
                        $text = $arcresult->ID;
                    }
                    echo get_archives_link($url, $text, $format, $before, $after);
                }
            }
        }
	}
}

function get_calendar($daylength = 1) {
	global $wpdb, $HTTP_GET_VARS, $m, $monthnum, $year, $timedifference, $month, $weekday, $tableposts, $posts;

    // Quick check. If we have no posts at all, abort!
    if (!$posts) {
        $gotsome = $wpdb->get_var("SELECT ID from $tableposts WHERE post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
        if (!$gotsome)
            return;
    }

	if (isset($HTTP_GET_VARS['w'])) {
		$w = ''.intval($HTTP_GET_VARS['w']);
	}
	$time_difference = get_settings('time_difference');

	// Let's figure out when we are
	if (!empty($monthnum) && !empty($year)) {
		$thismonth = ''.intval($monthnum);
		$thisyear = ''.intval($year);
	} elseif (!empty($w)) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif (!empty($m)) {
		$calendar = substr($m, 0, 6);
		$thisyear = ''.intval(substr($m, 0, 4));
		if (strlen($m) < 6) {
			$thismonth = '01';
		} else {
			$thismonth = ''.intval(substr($m, 4, 2));
		}
	} else {
		$thisyear = intval(date('Y', time()+($time_difference * 3600)));
		$thismonth = intval(date('m', time()+($time_difference * 3600)));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT DISTINCT MONTH( post_date ) AS month, YEAR( post_date ) AS year
			FROM $tableposts
			WHERE post_date < '$thisyear-$thismonth-01'
			AND post_status = 'publish'
							  ORDER BY post_date DESC
							  LIMIT 1");
	$next = $wpdb->get_row("SELECT  DISTINCT MONTH( post_date ) AS month, YEAR( post_date ) AS year
			FROM $tableposts
			WHERE post_date >  '$thisyear-$thismonth-01'
			AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
			AND post_status = 'publish'
							  ORDER  BY post_date ASC
							  LIMIT 1");

	echo '<table id="wp-calendar">
	<caption>' . $month[zeroise($thismonth, 2)] . ' ' . date('Y', $unixmonth) . '</caption>
	<thead>
	<tr>';
	foreach ($weekday as $wd) {
		echo "\n\t\t<th abbr='$wd' scope='col' title='$wd'>" . substr($wd, 0, $daylength) . '</th>';
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ($previous) {
		echo "\n\t\t".'<td abbr="' . $month[zeroise($previous->month, 2)] . '" colspan="3" id="prev"><a href="' .
				get_month_link($previous->year, $previous->month) . '" title="View posts for ' . $month[zeroise($previous->month, 2)] . ' ' .
				date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)) . '">&laquo; ' . substr($month[zeroise($previous->month, 2)], 0, 3) . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ($next) {
		echo "\n\t\t".'<td abbr="' . $month[zeroise($next->month, 2)] . '" colspan="3" id="next"><a href="' .
				get_month_link($next->year, $next->month) . '" title="View posts for ' . $month[zeroise($next->month, 2)] . ' ' .
				date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year)) . '">' . substr($month[zeroise($next->month, 2)], 0, 3) . ' &raquo;</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	echo '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
			FROM $tableposts WHERE MONTH(post_date) = $thismonth
			AND YEAR(post_date) = $thisyear
			AND post_status = 'publish'
			AND post_date < '" . date("Y-m-d H:i:s", (time() + ($time_difference * 3600)))."'", ARRAY_N);
	if ($dayswithposts) {
		foreach ($dayswithposts as $daywith) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}



	if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") ||
		  strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), "camino")) {
		$ak_title_separator = "\n";
	} else {
		$ak_title_separator = ", ";
	}

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
										 ."FROM $tableposts "
										 ."WHERE YEAR(post_date) = '$thisyear' "
										 ."AND MONTH(post_date) = '$thismonth' "
										 ."AND post_date < '".date("Y-m-d H:i:s", (time() + ($time_difference * 3600)))."' "
										 ."AND post_status = 'publish'"
										);
	if ($ak_post_titles) {
		foreach ($ak_post_titles as $ak_post_title) {
			$ak_titles_for_day["$ak_post_title->dom"] = '';
			if (empty($ak_titles_for_day["$ak_post_title->dom"])) { // first one
				$ak_titles_for_day["$ak_post_title->dom"] .= htmlspecialchars(stripslashes($ak_post_title->post_title));
			} else {
				$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . htmlspecialchars(stripslashes($ak_post_title->post_title));
			}
		}
	}


	// See how much we should pad in the beginning
	$pad = intval(date('w', $unixmonth));
	if (0 != $pad) echo "\n\t\t<td colspan='$pad' class='pad'>&nbsp;</td>";

	$daysinmonth = intval(date('t', $unixmonth));
	for ($day = 1; $day <= $daysinmonth; ++$day) {
		if (isset($newrow) && $newrow)
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ($day == date('j', (time() + ($time_difference * 3600))) && $thismonth == date('m', time()+($time_difference * 3600)))
			echo '<td id="today">';
		else
			echo "<td>";

		if (in_array($day, $daywithpost)) { // any posts today?
			echo '<a href="' . get_day_link($thisyear, $thismonth, $day) . "\" title=\"$ak_titles_for_day[$day]\">$day</a>";
		} else {
			echo $day;
		}
		echo '</td>';

		if (6 == date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear)))
			$newrow = true;
	}

	$pad = 7 - date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear));
	if ($pad != 0 && $pad != 7)
		echo "\n\t\t<td class='pad' colspan='$pad'>&nbsp;</td>";

	echo "\n\t</tr>\n\t</tbody>\n\t</table>";
}

/***** // About-the-blog tags *****/


/***** Links *****/
function get_permalink($id=false) {
	global $post, $wpdb, $tableposts;
	global $siteurl, $blogfilename, $querystring_start, $querystring_equal;

	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%postname%',
		'%post_id%'
	);
	if (!$id) {
		if ('' != get_settings('permalink_structure')) {
			$unixtime = strtotime($post->post_date);
			$rewritereplace = array(
				date('Y', $unixtime),
				date('n', $unixtime),
				date('j', $unixtime),
				$post->post_name,
				$post->ID
			);
			return $siteurl . str_replace($rewritecode, $rewritereplace, get_settings('permalink_structure'));
		} else { // if they're not using the fancy permalink option
			return $siteurl . '/' . $blogfilename.$querystring_start.'p'.$querystring_equal.$post->ID;
		}
	} else { // if an ID is given
		$idpost = $wpdb->get_row("SELECT post_date, post_name FROM $tableposts WHERE ID = $id");
		if ('' != get_settings('permalink_structure')) {
			$unixtime = strtotime($idpost->post_date);
			$rewritereplace = array(
				date('Y', $unixtime),
				date('n', $unixtime),
				date('j', $unixtime),
				$idpost->post_name,
				$id
			);
			return $siteurl . str_replace($rewritecode, $rewritereplace, get_settings('permalink_structure'));
		} else {
			return $siteurl . '/' . $blogfilename.$querystring_start.'p'.$querystring_equal.$id;
		}
	}
}

function get_month_link($year, $month) {
	global $siteurl, $blogfilename, $querystring_start, $querystring_equal;
	if (!$year) $year = date('Y', time()+($time_difference * 3600));
	if (!$month) $month = date('m', time()+($time_difference * 3600));
	if ('' != get_settings('permalink_structure')) {
		$off = strpos(get_settings('permalink_structure'), '%monthnum%');
		$offset = $off + 11;
		$monthlink = substr(get_settings('permalink_structure'), 0, $offset);
		if ('/' != substr($monthlink, -1)) $monthlink = substr($monthlink, 0, -1);
		$monthlink = str_replace('%year%', $year, $monthlink);
		$monthlink = str_replace('%monthnum%', intval($month), $monthlink);
		$monthlink = str_replace('%post_id%', '', $monthlink);
		return $siteurl . $monthlink;
	} else {
		return $siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal.$year.zeroise($month, 2);
	}
}

function get_day_link($year, $month, $day) {
	global $siteurl, $blogfilename, $querystring_start, $querystring_equal;
	if (!$year) $year = date('Y', time()+($time_difference * 3600));
	if (!$month) $month = date('m', time()+($time_difference * 3600));
	if (!$day) $day = date('j', time()+($time_difference * 3600));
	if ('' != get_settings('permalink_structure')) {
		$off = strpos(get_settings('permalink_structure'), '%day%');
		$offset = $off + 6;
		$daylink = substr(get_settings('permalink_structure'), 0, $offset);
		if ('/' != substr($daylink, -1)) $daylink = substr($daylink, 0, -1);
		$daylink = str_replace('%year%', $year, $daylink);
		$daylink = str_replace('%monthnum%', intval($month), $daylink);
		$daylink = str_replace('%day%', intval($day), $daylink);
		$daylink = str_replace('%post_id%', '', $daylink);
		return $siteurl . $daylink;
	} else {
		return $siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal.$year.zeroise($month, 2).zeroise($day, 2);
	}
}

function edit_post_link($link = 'Edit This', $before = '', $after = '') {
	global $user_level, $post, $siteurl;

	get_currentuserinfo();

	if ($user_level > 0) {
		$authordata = get_userdata($post->post_author);
		if ($user_level < $authordata->user_level) {
			return;
		}
	} else {
		return;
	}

	$location = "$siteurl/wp-admin/post.php?action=edit&amp;post=$post->ID";
	echo "$before <a href='$location'>$link</a> $after";
}

function edit_comment_link($link = 'Edit This', $before = '', $after = '') {
	global $user_level, $post, $comment, $siteurl;

	get_currentuserinfo();

	if ($user_level > 0) {
		$authordata = get_userdata($post->post_author);
		if ($user_level < $authordata->user_level) {
			return;
		}
	} else {
		return;
	}

	$location = "$siteurl/wp-admin/post.php?action=editcomment&amp;comment=$comment->comment_ID";
	echo "$before <a href='$location'>$link</a> $after";
}

/***** Date/Time tags *****/

function the_date_xml() {
    global $post;
    echo mysql2date("Y-m-d",$post->post_date);
    //echo ""+$post->post_date;
}

function the_date($d='', $before='', $after='', $echo = true) {
	global $id, $post, $day, $previousday, $dateformat, $newday;
	$the_date = '';
	if ($day != $previousday) {
		$the_date .= $before;
		if ($d=='') {
			$the_date .= mysql2date($dateformat, $post->post_date);
		} else {
			$the_date .= mysql2date($d, $post->post_date);
		}
		$the_date .= $after;
		$previousday = $day;
	}
	$the_date = apply_filters('the_date', $the_date);
	if ($echo) {
		echo $the_date;
	} else {
		return $the_date;
	}
}

function the_time($d='', $echo = true) {
	global $id, $post, $timeformat;
	if ($d=='') {
		$the_time = mysql2date($timeformat, $post->post_date);
	} else {
		$the_time = mysql2date($d, $post->post_date);
	}
	$the_time = apply_filters('the_time', $the_time);
	if ($echo) {
		echo $the_time;
	} else {
		return $the_time;
	}
}

function the_weekday() {
	global $weekday, $id, $post;
	$the_weekday = $weekday[mysql2date('w', $post->post_date)];
	$the_weekday = apply_filters('the_weekday', $the_weekday);
	echo $the_weekday;
}

function the_weekday_date($before='',$after='') {
	global $weekday, $id, $post, $day, $previousweekday;
	$the_weekday_date = '';
	if ($day != $previousweekday) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $weekday[mysql2date('w', $post->post_date)];
		$the_weekday_date .= $after;
		$previousweekday = $day;
	}
	$the_weekday_date = apply_filters('the_weekday_date', $the_weekday_date);
	echo $the_weekday_date;
}

/***** // Date/Time tags *****/

/**** // Geo Tags ****/
function get_Lat() {
    global $post;
    return $post->post_lat;
}

function get_Lon() {
    global $post;
    return $post->post_lon;
}

function print_Lat() {
    if(get_settings('use_geo_positions')) {
        if(get_Lat() > 0) {
            echo "".get_Lat()."N";
        } else {
            echo "".get_Lat()."S";
        }
    }
}

function print_Lon() {
    global $id, $postdata;
    if(get_settings('use_geo_positions')) {
        if(get_Lon() < 0) {
            $temp = get_Lon() * -1;
            echo "".$temp."W";
        } else {
            echo "".get_Lon()."E";
        }
    }
}

function print_PopUpScript() {
    echo "
<script type='text/javascript'>
<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->
function formHandler(form) {
  var URL = form.site.options[form.site.selectedIndex].value;
  if(URL != \".\") {
    popup = window.open(URL,\"MenuPopup\");
  }
}
</script> ";
}

function print_UrlPopNav() {
    $sites = array(
                   array('http://www.acme.com/mapper/?lat='.get_Lat().'&amp;long='.get_Lon().'&amp;scale=11&amp;theme=Image&amp;width=3&amp;height=2&amp;dot=Yes',
                         'Acme Mapper'),
                   array('http://geourl.org/near/?lat='.get_Lat().'&amp;lon='.get_Lon().'&amp;dist=500',
                         'GeoURLs near here'),
                   array('http://www.geocaching.com/seek/nearest.aspx?origin_lat='.get_Lat().'&amp;origin_long='.get_Lon().'&amp;dist=5',
                         'Geocaches Near Nere'),
                   array('http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude='.get_Lat().'&amp;longitude='.get_Lon(),
                         'Mapquest map of this spot'),
                   array('http://www.sidebit.com/ProjectGeoURLMap.php?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'SideBit URL Map of this spot'),
                   array('http://confluence.org/confluence.php?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'Confluence.org near here'),
                   array('http://www.topozone.com/map.asp?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'Topozone near here'),
                   array('http://www.findu.com/cgi-bin/near.cgi?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'FindU near here'),
                   array('http://mapserver.maptech.com/api/espn/index.cfm?lat='.get_Lat().'&amp;lon='.get_Lon().'&amp;scale=100000&amp;zoom=50&amp;type=1&amp;icon=0&amp;&amp;scriptfile=http://mapserver.maptech.com/api/espn/index.cfm',
                         'Maptech near here')
                  );
    echo '<form action=""><div>
<select name="site" size="1" onchange="formHandler(this.form);" >'."\n";
    echo '<option value=".">Sites referencing '.get_Lat().' x '.get_Lon()."</option>\n";
    foreach($sites as $site) {
        echo "\t".'<option value="'.$site[0].'">'.$site[1]."</option>\n";
    }
    echo '</select></div>
</form>'."\n";
}

function longitude_invalid() {
    if (get_Lon() == null) return true;
    if (get_Lon() > 360) return true;
    if (get_Lon() < -360) return true;
}

function print_AcmeMap_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.acme.com/mapper/?lat=".get_Lat()."&amp;long=".get_Lon()."&amp;scale=11&amp;theme=Image&amp;width=3&amp;height=2&amp;dot=Yes";
}

function print_GeoURL_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://geourl.org/near/?lat=".get_Lat()."&amp;lon=".get_Lon()."&amp;dist=500";
}

function print_GeoCache_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.geocaching.com/seek/nearest.aspx?origin_lat=".get_Lat()."&amp;origin_long=".get_Lon()."&amp;dist=5";
}

function print_MapQuest_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude=".get_Lat()."&amp;longitude=".get_Lon();
}

function print_SideBit_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.sidebit.com/ProjectGeoURLMap.php?lat=".get_Lat()."&amp;lon=".get_Lon();
}

function print_DegreeConfluence_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://confluence.org/confluence.php?lat=".get_Lat()."&amp;lon=".get_Lon();
}



/***** Author tags *****/

function the_author() {
	global $id, $authordata;
	$i = $authordata->user_idmode;
	if ($i == 'nickname')	echo $authordata->user_nickname;
	if ($i == 'login')	echo $authordata->user_login;
	if ($i == 'firstname')	echo $authordata->user_firstname;
	if ($i == 'lastname')	echo $authordata->user_lastname;
	if ($i == 'namefl')	echo $authordata->user_firstname.' '.$authordata->user_lastname;
	if ($i == 'namelf')	echo $authordata->user_lastname.' '.$authordata->user_firstname;
	if (!$i) echo $authordata->user_nickname;
}
function the_author_description() {
	global $authordata;
	echo $authordata->user_description;
}
function the_author_login() {
	global $id,$authordata;	echo $authordata->user_login;
}

function the_author_firstname() {
	global $id,$authordata;	echo $authordata->user_firstname;
}

function the_author_lastname() {
	global $id,$authordata;	echo $authordata->user_lastname;
}

function the_author_nickname() {
	global $id,$authordata;	echo $authordata->user_nickname;
}

function the_author_ID() {
	global $id,$authordata;	echo $authordata->ID;
}

function the_author_email() {
	global $id,$authordata;	echo antispambot($authordata->user_email);
}

function the_author_url() {
	global $id,$authordata;	echo $authordata->user_url;
}

function the_author_icq() {
	global $id,$authordata;	echo $authordata->user_icq;
}

function the_author_aim() {
	global $id,$authordata;	echo str_replace(' ', '+', $authordata->user_aim);
}

function the_author_yim() {
	global $id,$authordata;	echo $authordata->user_yim;
}

function the_author_msn() {
	global $id,$authordata;	echo $authordata->user_msn;
}

function the_author_posts() {
	global $id,$postdata;	$posts=get_usernumposts($post->post_author);	echo $posts;
}

/***** // Author tags *****/




/***** Post tags *****/

function get_the_password_form() {
	$output = "<form action='" . get_settings('siteurl') . "/wp-pass.php' method='post'>
	<p>This post is password protected. To view it please enter your password below:</p>
	<p><label>Password: <input name='post_password' type='text' size='20' /></label> <input type='submit' name='Submit' value='Submit' /></p>
	</form>
	";
	return $output;
}

function the_ID() {
	global $id;
	echo $id;
}

function the_title($before='', $after='', $echo=true) {
	$title = get_the_title();
	$title = convert_bbcode($title);
	$title = convert_gmcode($title);
	$title = convert_smilies($title);
	if ($title) {
		$title = convert_chars($before.$title.$after);
		$title = apply_filters('the_title', $title);
        if ($echo)
            echo $title;
        else
            return $title;
	}
}
function the_title_rss() {
	$title = get_the_title();
	$title = convert_bbcode($title);
	$title = convert_gmcode($title);
	$title = strip_tags($title);
	if (trim($title)) {
		echo convert_chars($title, 'unicode');
	}
}
function the_title_unicode($before='',$after='') {
	$title = get_the_title();
	$title = convert_bbcode($title);
	$title = convert_gmcode($title);
	if ($title) {
		$title = convert_chars($before.$title.$after);
		$title = apply_filters('the_title_unicode', $title);
		echo $title;
	}
}
function get_the_title() {
	global $id, $post;
	$output = stripslashes($post->post_title);
	if (!empty($post->post_password)) { // if there's a password
		$output = 'Protected: ' . $output;
	}
	return $output;
}

function the_content($more_link_text='(more...)', $stripteaser=0, $more_file='') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = convert_bbcode($content);
	$content = convert_gmcode($content);
	$content = convert_smilies($content);
	$content = convert_chars($content, 'html');
	$content = apply_filters('the_content', $content);
	echo $content;
}

function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = convert_bbcode($content);
	$content = convert_gmcode($content);
	$content = convert_chars($content, 'unicode');
	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$content = htmlspecialchars($content);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$content = make_url_footnote($content);
	} elseif ($encode_html == 2) {
		$content = strip_tags($content);
	}
	if ($cut) {
		$blah = explode(' ', $content);
		if (count($blah) > $cut) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$content = $excerpt;
	}
	echo $content;
}

function the_content_unicode($more_link_text='(more...)', $stripteaser=0, $more_file='') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = convert_bbcode($content);
	$content = convert_gmcode($content);
	$content = convert_smilies($content);
	$content = convert_chars($content, 'unicode');
	$content = apply_filters('the_content_unicode', $content);
	echo $content;
}

function get_the_content($more_link_text='(more...)', $stripteaser=0, $more_file='') {
	global $id, $post, $more, $c, $withcomments, $page, $pages, $multipage, $numpages;
	global $HTTP_SERVER_VARS, $HTTP_COOKIE_VARS, $preview, $cookiehash;
	global $querystring_start, $querystring_equal, $querystring_separator;
    global $pagenow;
	$output = '';

	if (!empty($post->post_password)) { // if there's a password
		if ($HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
			$output = get_the_password_form();
			return $output;
		}
	}

	if ($more_file != '') {
		$file = $more_file;
	} else {
		$file = $pagenow; //$HTTP_SERVER_VARS['PHP_SELF'];
	}
	$content = $pages[$page-1];
	$content = explode('<!--more-->', $content);
	if ((preg_match('/<!--noteaser-->/', $post->post_content) && ((!$multipage) || ($page==1))))
		$stripteaser = 1;
	$teaser = $content[0];
	if (($more) && ($stripteaser))
		$teaser = '';
	$output .= $teaser;
	if (count($content)>1) {
		if ($more) {
			$output .= '<a id="more-'.$id.'"></a>'.$content[1];
		} else {
			$output .= " <a href='". get_permalink() . "#more-$id'>$more_link_text</a>";
		}
	}
	if ($preview) { // preview fix for javascript bug with foreign languages
		$output =  preg_replace('/\%u([0-9A-F]{4,4})/e',  "'&#'.base_convert('\\1',16,10).';'", $output);
	}
	return $output;
}

function the_excerpt() {
	$excerpt = get_the_excerpt();
	$excerpt = convert_bbcode($excerpt);
	$excerpt = convert_gmcode($excerpt);
	$excerpt = convert_smilies($excerpt);
	$excerpt = convert_chars($excerpt, 'html');
	$excerpt = apply_filters('the_excerpt', $excerpt);
	echo $excerpt;
}

function the_excerpt_rss($cut = 0, $encode_html = 0) {
	$output = get_the_excerpt(true);
	$output = convert_bbcode($output);
	$output = convert_gmcode($output);
	$output = convert_chars($output, 'unicode');
	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$output = htmlspecialchars($output);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$output = make_url_footnote($output);
	} elseif ($encode_html == 2) {
		$output = strip_tags($output);
	}
	if ($cut) {
        $excerpt = '';
		$blah = explode(' ', $output);
		if (count($blah) > $cut) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$output = $excerpt;
	}
	echo $output;
}

function the_excerpt_unicode() {
	$excerpt = get_the_excerpt();
	$excerpt = convert_bbcode($excerpt);
	$excerpt = convert_gmcode($excerpt);
	$excerpt = convert_smilies($excerpt);
	$excerpt = convert_chars($excerpt, 'unicode');
	$excerpt = apply_filters('the_excerpt_unicode', $excerpt);
	echo $excerpt;
}

function get_the_excerpt($fakeit = false) {
	global $id, $post;
	global $HTTP_SERVER_VARS, $HTTP_COOKIE_VARS, $preview, $cookiehash;
	$output = '';
	$output = stripslashes($post->post_excerpt);
	if (!empty($post->post_password)) { // if there's a password
		if ($HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
			$output = "There is no excerpt because this is a protected post.";
			return $output;
		}
	}
    //if we haven't got an excerpt, make one in the style of the rss ones
    if (($output == '') && $fakeit) {
        $output = get_the_content();
        $output = strip_tags($output);
        $blah = explode(' ', $output);
        $excerpt_length = 120;
        if (count($blah) > $excerpt_length) {
			$k = $excerpt_length;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
        $excerpt = '';
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$output = $excerpt;
    } // end if no excerpt
	if ($preview) { // preview fix for javascript bug with foreign languages
		$output =  preg_replace('/\%u([0-9A-F]{4,4})/e',  "'&#'.base_convert('\\1',16,10).';'", $output);
	}
	return $output;
}


function link_pages($before='<br />', $after='<br />', $next_or_number='number', $nextpagelink='next page', $previouspagelink='previous page', $pagelink='%', $more_file='') {
	global $id, $page, $numpages, $multipage, $more;
	global $pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($more_file != '') {
		$file = $more_file;
	} else {
		$file = $pagenow;
	}
	if (($multipage)) {
		if ($next_or_number=='number') {
			echo $before;
			for ($i = 1; $i < ($numpages+1); $i = $i + 1) {
				$j=str_replace('%',"$i",$pagelink);
				echo " ";
				if (($i != $page) || ((!$more) && ($page==1))) {
				if ('' == get_settings('permalink_structure')) {
					echo '<a href="'.get_permalink().$querystring_separator.'page'.$querystring_equal.$i.'">';
				} else {
					echo '<a href="'.get_permalink().$i.'/">';
				}
				}
				echo $j;
				if (($i != $page) || ((!$more) && ($page==1)))
					echo '</a>';
			}
			echo $after;
		} else {
			if ($more) {
				echo $before;
				$i=$page-1;
				if ($i && $more) {
				if ('' == get_settings('permalink_structure')) {
					echo '<a href="'.get_permalink().$querystring_separator.'page'.$querystring_equal.$i.'">';
				} else {
					echo '<a href="'.get_permalink().$i.'/">';
				}
				}
				$i=$page+1;
				if ($i<=$numpages && $more) {
				if ('' == get_settings('permalink_structure')) {
					echo '<a href="'.get_permalink().$querystring_separator.'page'.$querystring_equal.$i.'">';
				} else {
					echo '<a href="'.get_permalink().$i.'/">';
				}
				}
				echo $after;
			}
		}
	}
}


function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {
	global $tableposts, $id, $post, $siteurl, $blogfilename, $wpdb;
	global $p, $posts, $posts_per_page, $s, $single;
	global $querystring_start, $querystring_equal, $querystring_separator;

	if(($p) || ($posts_per_page == 1) || 1 == $single) {

		$current_post_date = $post->post_date;
		$current_category = $post->post_category;

		$sqlcat = '';
		if ($in_same_cat != 'no') {
			$sqlcat = " AND post_category = '$current_category' ";
		}

		$sql_exclude_cats = '';
		if (!empty($excluded_categories)) {
			$blah = explode('and', $excluded_categories);
			foreach($blah as $category) {
				$category = intval($category);
				$sql_exclude_cats .= " AND post_category != $category";
			}
		}

		$limitprev--;
		$lastpost = @$wpdb->get_row("SELECT ID, post_title FROM $tableposts WHERE post_date < '$current_post_date' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date DESC LIMIT $limitprev, 1");
		if ($lastpost) {
			$string = '<a href="'.get_permalink($lastpost->ID).'">'.$previous;
			if ($title == 'yes') {
                $string .= wptexturize(stripslashes($lastpost->post_title));
            }
			$string .= '</a>';
			$format = str_replace('%', $string, $format);
			echo $format;
		}
	}
}

function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
	global $tableposts, $p, $posts, $id, $post, $siteurl, $blogfilename, $wpdb;
	global $time_difference, $single;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if(($p) || ($posts==1) || 1 == $single) {

		$current_post_date = $post->post_date;
		$current_category = $post->post_category;

		$sqlcat = '';
		if ($in_same_cat != 'no') {
			$sqlcat = " AND post_category='$current_category' ";
		}

		$sql_exclude_cats = '';
		if (!empty($excluded_categories)) {
			$blah = explode('and', $excluded_categories);
			foreach($blah as $category) {
				$category = intval($category);
				$sql_exclude_cats .= " AND post_category != $category";
			}
		}

		$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));

		$limitnext--;

		$nextpost = @$wpdb->get_row("SELECT ID,post_title FROM $tableposts WHERE post_date > '$current_post_date' AND post_date < '$now' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date ASC LIMIT $limitnext,1");
		if ($nextpost) {
			$string = '<a href="'.get_permalink($nextpost->ID).'">'.$next;
			if ($title=='yes') {
				$string .= wptexturize(stripslashes($nextpost->post_title));
			}
			$string .= '</a>';
			$format = str_replace('%', $string, $format);
			echo $format;
		}
	}
}

function next_posts($max_page = 0) { // original by cfactor at cooltux.org
	global $HTTP_SERVER_VARS, $siteurl, $blogfilename, $p, $paged, $what_to_show, $pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if (empty($p) && ($what_to_show == 'paged')) {
		$qstr = $HTTP_SERVER_VARS['QUERY_STRING'];
		if (!empty($qstr)) {
			$qstr = preg_replace("/&paged=\d{0,}/","",$qstr);
			$qstr = preg_replace("/paged=\d{0,}/","",$qstr);
		} elseif (stristr($HTTP_SERVER_VARS['REQUEST_URI'], $HTTP_SERVER_VARS['SCRIPT_NAME'] )) {
			if ('' != $qstr = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], '',
											$HTTP_SERVER_VARS['REQUEST_URI']) ) {
				$qstr = preg_replace("/^\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}/", "", $qstr);
				$qstr = preg_replace("/\/$/", "", $qstr);
			}
		}
		if (!$paged) $paged = 1;
		$nextpage = intval($paged) + 1;
		if (!$max_page || $max_page >= $nextpage) {
			echo  $siteurl.'/'.$pagenow.$querystring_start.
				($qstr == '' ? '' : $qstr.$querystring_separator) .
				'paged'.$querystring_equal.$nextpage;
		}
	}
}

function next_posts_link($label='Next Page &raquo;', $max_page=0) {
	global $p, $paged, $result, $request, $posts_per_page, $what_to_show, $wpdb;
	if ($what_to_show == 'paged') {
		if (!$max_page) {
			$nxt_request = $request;
            //if the query includes a limit clause, call it again without that
            //limit clause!
			if ($pos = strpos(strtoupper($request), 'LIMIT')) {
				$nxt_request = substr($request, 0, $pos);
			}
			$nxt_result = $wpdb->query($nxt_request);
			$numposts = $wpdb->num_rows;
			$max_page = ceil($numposts / $posts_per_page);
		}
		if (!$paged)
            $paged = 1;
		$nextpage = intval($paged) + 1;
		if (empty($p) && (empty($paged) || $nextpage <= $max_page)) {
			echo '<a href="';
			next_posts($max_page);
			echo '">'. htmlspecialchars($label) .'</a>';
		}
	}
}


function previous_posts() { // original by cfactor at cooltux.org
	global $HTTP_SERVER_VARS, $siteurl, $blogfilename, $p, $paged, $what_to_show, $pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if (empty($p) && ($what_to_show == 'paged')) {
		$qstr = $HTTP_SERVER_VARS['QUERY_STRING'];
		if (!empty($qstr)) {
			$qstr = preg_replace("/&paged=\d{0,}/","",$qstr);
			$qstr = preg_replace("/paged=\d{0,}/","",$qstr);
		} elseif (stristr($HTTP_SERVER_VARS['REQUEST_URI'], $HTTP_SERVER_VARS['SCRIPT_NAME'] )) {
			if ('' != $qstr = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], '',
											$HTTP_SERVER_VARS['REQUEST_URI']) ) {
				$qstr = preg_replace("/^\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}\//", "", $qstr);
				$qstr = preg_replace("/paged\/\d{0,}/", "", $qstr);
				$qstr = preg_replace("/\/$/", "", $qstr);
			}
		}
		$nextpage = intval($paged) - 1;
		if ($nextpage < 1) $nextpage = 1;
		echo  $siteurl.'/'.$pagenow.$querystring_start.
			($qstr == '' ? '' : $qstr.$querystring_separator) .
			'paged'.$querystring_equal.$nextpage;
	}
}

function previous_posts_link($label='&laquo; Previous Page') {
	global $p, $paged, $what_to_show;
	if (empty($p)  && ($paged > 1) && ($what_to_show == 'paged')) {
		echo '<a href="';
		previous_posts();
		echo '">'.  htmlspecialchars($label) .'</a>';
	}
}

function posts_nav_link($sep=' :: ', $prelabel='<< Previous Page', $nxtlabel='Next Page >>') {
	global $p, $what_to_show, $request, $posts_per_page, $wpdb;
	if (empty($p) && ($what_to_show == 'paged')) {
		$nxt_request = $request;
		if ($pos = strpos(strtoupper($request), 'LIMIT')) {
			$nxt_request = substr($request, 0, $pos);
		}
        $nxt_result = $wpdb->query($nxt_request);
        $numposts = $wpdb->num_rows;
		$max_page = ceil($numposts / $posts_per_page);
		if ($max_page > 1) {
			previous_posts_link($prelabel);
			echo htmlspecialchars($sep);
			next_posts_link($nxtlabel, $max_page);
		}
	}
}

/***** // Post tags *****/




/***** Category tags *****/

function get_the_category() {
	global $post, $tablecategories, $tablepost2cat, $wpdb, $category_cache;
	if ($category_cache[$post->ID]) {
		return $category_cache[$post->ID];
	} else {
		$categories = $wpdb->get_results("
			SELECT category_id, cat_name, category_nicename, category_description 
			FROM  $tablecategories, $tablepost2cat 
			WHERE $tablepost2cat.category_id = cat_ID AND $tablepost2cat.post_id = $post->ID
			");
	
		return $categories;
	}
}

function get_category_link($echo = false, $category_id, $category_nicename) {
	global $wpdb, $tablecategories, $post, $querystring_start, $querystring_equal, $siteurl, $blogfilename;
	$cat_ID = $category_id;
	$permalink_structure = get_settings('permalink_structure');
	
	if ('' == $permalink_structure) {
		$file = "$siteurl/$blogfilename";
		$link = $file.$querystring_start.'cat'.$querystring_equal.$cat_ID;
	} else {
		if ('' == $category_nicename) $category_nicename = $wpdb->get_var("SELECT category_nicename FROM $tablecategories WHERE cat_ID = $category_id");
		// Get any static stuff from the front
		$front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
		$link = $siteurl . $front . 'category/' . $category_nicename . '/';
	}

	if ($echo) echo $link;
	return $link;
}

function the_category($seperator = '') {
	$categories = get_the_category();
	if ('' == $seperator) {
		echo '<ul class="post-categories">';
		foreach ($categories as $category) {
			$category->cat_name = stripslashes($category->cat_name);
			echo "\n\t<li><a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>$category->cat_name</a></li>";
		}
		echo '</ul>';
	} else {
		$i = 0;
		foreach ($categories as $category) {
			$category->cat_name = stripslashes($category->cat_name);
			if (0 < $i) echo $seperator . ' ';
			echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>$category->cat_name</a>";
			++$i;
		}
	}
}

function the_category_rss($type = 'rss') {
	$categories = get_the_category();
	foreach ($categories as $category) {
		$category->cat_name = stripslashes(convert_chars($category->cat_name));
		if ('rdf' == $type) {
			echo "\n<dc:subject>$category->cat_name</dc:subject>";
		} else {
			echo "\n<category>$category->cat_name</category>";
		}
	}

}
function the_category_unicode() {
	$category = get_the_category();
	$category = apply_filters('the_category_unicode', $category);
	echo convert_chars($category, 'unicode');
}



function get_the_category_by_ID($cat_ID) {
	global $tablecategories, $cache_categories, $use_cache, $wpdb;
	if ((!$cache_categories[$cat_ID]) OR (!$use_cache)) {
		$cat_name = $wpdb->get_var("SELECT cat_name FROM $tablecategories WHERE cat_ID = '$cat_ID'");
		$cache_categories[$cat_ID] = $cat_name;
	} else {
		$cat_name = $cache_categories[$cat_ID];
	}
	return(stripslashes($cat_name));
}

function the_category_ID($echo=true) {
	global $post;
    if ($echo)
        echo $post->post_category;
    else
        return $post->post_category;
}

function the_category_head($before='', $after='') {
	global $post, $currentcat, $previouscat, $dateformat, $newday;
	$currentcat = $post->post_category;
	if ($currentcat != $previouscat) {
		echo $before;
		echo get_the_category_by_ID($currentcat);
		echo $after;
		$previouscat = $currentcat;
	}
}

function category_description($category = 0) {
	global $cat, $wpdb, $tablecategories;
	if (!$category) $category = $cat;
	$category_description = $wpdb->get_var("SELECT category_description FROM $tablecategories WHERE cat_ID = $category");
	$category_description = apply_filters('category_description', $category_description);
	return $category_description;
}

// out of the b2 loop
function dropdown_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc',
                       $optiondates = 0, $optioncount = 0, $hide_empty = 1) {
    global $cat, $tablecategories, $tableposts, $wpdb;
    $sort_column = 'cat_'.$sort_column;

    $query  = " SELECT cat_ID, cat_name,";
    $query .= "  COUNT($tableposts.ID) AS cat_count,";
    $query .= "  DAYOFMONTH(MAX(post_date)) AS lastday, MONTH(MAX(post_date)) AS lastmonth";
    $query .= " FROM $tablecategories LEFT JOIN $tableposts ON cat_ID = post_category";
    $query .= " WHERE cat_ID > 0 ";
    $query .= " GROUP BY post_category ";
    if (intval($hide_empty) == 1) {
        $query .= " HAVING cat_count > 0";
    }
    $query .= " ORDER BY $sort_column $sort_order, post_date DESC";

	$categories = $wpdb->get_results($query);
	echo "<select name='cat' class='postform'>\n";
	if (intval($optionall) == 1) {
		$all = apply_filters('list_cats', $all);
		echo "\t<option value='all'>$all</option>\n";
	}
	if ($categories) {
		foreach ($categories as $category) {
			$cat_name = apply_filters('list_cats', $category->cat_name);
			echo "\t<option value=\"".$category->cat_ID."\"";
			if ($category->cat_ID == $cat)
				echo ' selected="selected"';
			echo '>'.stripslashes($cat_name);
	        if (intval($optioncount) == 1) {
	            echo '&nbsp;&nbsp;('.$category->cat_count.')';
	        }
	        if (intval($optiondates) == 1) {
	            echo '&nbsp;&nbsp;'.$category->lastday.'/'.$category->lastmonth;
	        }
	        echo "</option>\n";
		}
	}
	echo "</select>\n";
}

// out of the b2 loop
function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc',
                   $file = 'blah', $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1) {
	global $tablecategories, $tableposts, $tablepost2cat, $wpdb;
	global $pagenow, $siteurl, $blogfilename;
	global $querystring_start, $querystring_equal, $querystring_separator;
    if (($file == 'blah') || ($file == '')) {
        $file = "$siteurl/$blogfilename";
    }
	$sort_column = 'cat_'.$sort_column;

    $query  = "
		SELECT cat_ID, cat_name, category_nicename,
		COUNT($tablepost2cat.post_id) AS cat_count,
		DAYOFMONTH(MAX(post_date)) AS lastday, MONTH(MAX(post_date)) AS lastmonth
		FROM $tablecategories LEFT JOIN $tablepost2cat ON (cat_ID = category_id)
		LEFT JOIN $tableposts ON (ID = post_id)
		WHERE cat_ID > 0 
		GROUP BY category_id
		";
    if (intval($hide_empty) == 1) {
        $query .= " HAVING cat_count > 0";
    }
    $query .= " ORDER BY $sort_column $sort_order, post_date DESC";

	$categories = $wpdb->get_results($query);
	if (!$categories) {
		if ($list) {
			$before = '<li>';
			$after = '</li>';
		}
		echo $before . "No categories" . $after . "\n";
		return;
	}
	if (intval($optionall) == 1) {
		$all = apply_filters('list_cats', $all);
        $link = "<a href=\"".$file.$querystring_start.'cat'.$querystring_equal.'all">'.$all."</a>";
		if ($list) echo "\n\t<li>$link</li>";
		else echo "\t$link<br />\n";
	}

	foreach ($categories as $category) {
		$cat_name = apply_filters('list_cats', $category->cat_name);
        $link = '<a href="'.get_category_link(0, $category->cat_ID, $category->category_nicename).'" title="View all posts filed under ' . $category->cat_name . '">';
        $link .= stripslashes($cat_name).'</a>';
        if (intval($optioncount) == 1) {
            $link .= '&nbsp;('.$category->cat_count.')';
        }
        if (intval($optiondates) == 1) {
            $link .= '&nbsp;'.$category->lastday.'/'.$category->lastmonth;
        }
		if ($list) {
			echo "\t<li>$link</li>\n";
		} else {
			echo "\t$link<br />\n";
		}
	}
}

/***** // Category tags *****/




/***** <Link> tags *****/



/***** // <Link> tags *****/




/***** Comment tags *****/

// generic comments/trackbacks/pingbacks numbering

function comments_number($zero='No Comments', $one='1 Comment', $more='% Comments', $number='') {
	global $id, $comment, $tablecomments, $wpdb;
	if ('' == $number) $number = $wpdb->get_var("SELECT COUNT(*) FROM $tablecomments WHERE comment_post_ID = $id AND comment_approved = '1'");
	if ($number == 0) {
		$blah = $zero;
	} elseif ($number == 1) {
		$blah = $one;
	} elseif ($number  > 1) {
		$blah = str_replace('%', $number, $more);
	}
	echo $blah;
}

function comments_link($file='', $echo=true) {
	global $id, $pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($file == '')	$file = $pagenow;
	if ($file == '/')	$file = '';
	if (!$echo) return get_permalink() . '#comments';
	else echo get_permalink() . '#comments';
}

function comments_popup_script($width=400, $height=400, $file='wp-comments-popup.php') {
	global $wpcommentspopupfile, $wptrackbackpopupfile, $wppingbackpopupfile, $wpcommentsjavascript;
	$wpcommentspopupfile = $file;
	$wpcommentsjavascript = 1;
	$javascript = "<script type='text/javascript'>\nfunction wpopen (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n</script>\n";
	echo $javascript;
}

function comments_popup_link($zero='No Comments', $one='1 Comment', $more='% Comments', $CSSclass='', $none='Comments Off') {
	global $id, $wpcommentspopupfile, $wpcommentsjavascript, $post, $wpdb, $tablecomments, $HTTP_COOKIE_VARS, $cookiehash;
	global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
	$number = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $tablecomments WHERE comment_post_ID = $id AND comment_approved = '1';");
	if (0 == $number && 'closed' == $post->comment_status && 'closed' == $post->ping_status) {
		echo $none;
		return;
	} else {
        if (!empty($post->post_password)) { // if there's a password
            if ($HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
                echo("Enter your password to view comments");
                return;
            }
        }
        echo '<a href="';
        if ($wpcommentsjavascript) {
            echo $siteurl.'/'.$wpcommentspopupfile.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1';
            //echo get_permalink();
            echo '" onclick="wpopen(this.href); return false"';
        } else {
            // if comments_popup_script() is not in the template, display simple comment link
            comments_link();
            echo '"';
        }
        if (!empty($CSSclass)) {
            echo ' class="'.$CSSclass.'"';
        }
        echo '>';
        comments_number($zero, $one, $more, $number);
        echo '</a>';
    }
}

function comment_ID() {
	global $comment;
	echo $comment->comment_ID;
}

function comment_author() {
	global $comment;
	$author = stripslashes(stripslashes($comment->comment_author));
	$author = apply_filters('comment_auther', $author);
	if (!empty($author)) {
		echo htmlspecialchars($comment->comment_author);
	}
	else {
		echo "Anonymous";
	}
}

function comment_author_email() {
	global $comment;
	$email = stripslashes(stripslashes($comment->comment_author_email));
	
	echo antispambot(stripslashes($comment->comment_author_email));
}

function comment_author_link() {
	global $comment;
	$url = trim(stripslashes($comment->comment_author_url));
	$email = stripslashes($comment->comment_author_email);
	$author = htmlspecialchars(stripslashes($comment->comment_author));
	if (empty($author)) {
		$author = "Anonymous";
	}

	$url = str_replace('http://url', '', $url);
	$url = preg_replace('|[^a-z0-9-_.?#=&;,/:]|i', '', $url);
	if (empty($url) && empty($email)) {
		echo $author;
		return;
		}
	echo '<a href="';
	if ($url) {
		$url = str_replace(';//', '://', $url);
		$url = (!strstr($url, '://')) ? 'http://'.$url : $url;
		$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
		echo $url;
	} else {
		echo 'mailto:'.antispambot($email);
	}
	echo '" rel="external">' . $author . '</a>';
}

function comment_type($commenttxt = 'Comment', $trackbacktxt = 'Trackback', $pingbacktxt = 'Pingback') {
	global $comment;
	if (preg_match('|<trackback />|', $comment->comment_content)) echo $trackbacktxt;
	elseif (preg_match('|<pingback />|', $comment->comment_content)) echo $pingbacktxt;
	else echo $commenttxt;
}

function comment_author_url() {
	global $comment;
	$url = trim(stripslashes($comment->comment_author_url));
	$url = str_replace(';//', '://', $url);
	$url = (!strstr($url, '://')) ? 'http://'.$url : $url;
	// convert & into &amp;
	$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
	$url = preg_replace('|[^a-z0-9-_.,/:]|i', '', $url);
	if ($url != 'http://url') {
		echo $url;
	}
}

function comment_author_email_link($linktext='', $before='', $after='') {
	global $comment;
	$email = $comment->comment_author_email;
	if ((!empty($email)) && ($email != '@')) {
		$display = ($linktext != '') ? $linktext : antispambot(stripslashes($email));
		echo $before;
		echo '<a href="mailto:'.antispambot(stripslashes($email)).'">'.$display.'</a>';
		echo $after;
	}
}

function comment_author_url_link($linktext='', $before='', $after='') {
	global $comment;
	$url = trim(stripslashes($comment->comment_author_url));
	$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
	$url = (!stristr($url, '://')) ? 'http://'.$url : $url;
	$url = preg_replace('|[^a-z0-9-_.,/:]|i', '', $url);
	if ((!empty($url)) && ($url != 'http://') && ($url != 'http://url')) {
		$display = ($linktext != '') ? $linktext : stripslashes($url);
		echo $before;
		echo '<a href="'.stripslashes($url).'" rel="external">'.$display.'</a>';
		echo $after;
	}
}

function comment_author_IP() {
	global $comment;
	echo stripslashes($comment->comment_author_IP);
}

function comment_text() {
	global $comment;
	$comment_text = stripslashes($comment->comment_content);
	$comment_text = str_replace('<trackback />', '', $comment_text);
	$comment_text = str_replace('<pingback />', '', $comment_text);
	$comment_text = convert_chars($comment_text);
	$comment_text = convert_bbcode($comment_text);
	$comment_text = convert_gmcode($comment_text);
	$comment_text = make_clickable($comment_text);
	$comment_text = balanceTags($comment_text,1);
	$comment_text = apply_filters('comment_text', $comment_text);
	$comment_text = convert_smilies($comment_text);
	echo $comment_text;
}

function comment_date($d='') {
	global $comment, $dateformat;
	if ($d == '') {
		echo mysql2date($dateformat, $comment->comment_date);
	} else {
		echo mysql2date($d, $comment->comment_date);
	}
}

function comment_time($d='') {
	global $comment, $timeformat;
	if ($d == '') {
		echo mysql2date($timeformat, $comment->comment_date);
	} else {
		echo mysql2date($d, $comment->comment_date);
	}
}

function comments_rss_link($link_text='Comments RSS', $commentsrssfilename = 'wp-commentsrss2.php') {
	global $id;
	global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
	$url = $siteurl.'/'.$commentsrssfilename.$querystring_start.'p'.$querystring_equal.$id;
	$url = '<a href="'.$url.'">'.$link_text.'</a>';
	echo $url;
}

function comment_author_rss() {
	global $comment;
	if (!empty($comment->comment_author)) {
		echo htmlspecialchars(strip_tags(stripslashes($comment->comment_author)));
	}
	else {
		echo "Anonymous";
	}
}

function comment_text_rss() {
	global $comment;
	$comment_text = stripslashes($comment->comment_content);
	$comment_text = str_replace('<trackback />', '', $comment_text);
	$comment_text = str_replace('<pingback />', '', $comment_text);
	$comment_text = convert_chars($comment_text);
	$comment_text = convert_bbcode($comment_text);
	$comment_text = convert_gmcode($comment_text);
	$comment_text = convert_smilies($comment_text);
	$comment_text = apply_filters('comment_text', $comment_text);
	$comment_text = strip_tags($comment_text);
	$comment_text = htmlspecialchars($comment_text);
	echo $comment_text;
}

function comment_link_rss() {
	global $comment;
	echo get_permalink($comment->comment_post_ID).'#comments';
}

function permalink_comments_rss() {
	global $comment;
	echo get_permalink($comment->comment_post_ID);
}

/***** // Comment tags *****/



/***** TrackBack tags *****/

function trackback_url($display = true) {
	global $siteurl, $id;
	$tb_url = $siteurl.'/wp-trackback.php/'.$id;
	if ($display) {
		echo $tb_url;
	} else {
		return $tb_url;
	}
}


function trackback_rdf($timezone = 0) {
	global $siteurl, $id, $HTTP_SERVER_VARS;
	if (!stristr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], 'W3C_Validator')) {
		echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" '."\n";
		echo '    xmlns:dc="http://purl.org/dc/elements/1.1/"'."\n";
		echo '    xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">'."\n";
		echo '<rdf:Description'."\n";
		echo '    rdf:about="';
		permalink_single();
		echo '"'."\n";
		echo '    dc:identifier="';
		permalink_single();
		echo '"'."\n";
		echo '    dc:title="'.str_replace('--', '&#x2d;&#x2d;', addslashes(strip_tags(get_the_title()))).'"'."\n";
		echo '    trackback:ping="'.trackback_url(0).'"'." />\n";
		echo '</rdf:RDF>';
	}
}

/***** // TrackBack tags *****/


/***** Permalink tags *****/
function permalink_anchor($mode = 'id') {
	global $id, $post;
	switch(strtolower($mode)) {
		case 'title':
			$title = sanitize_title($post->post_title) . '-' . $id;
			echo '<a id="'.$title.'"></a>';
			break;
		case 'id':
		default:
			echo '<a id="post-'.$id.'"></a>';
			break;
	}
}

function permalink_link($file='', $mode = 'id') {
	global $post, $pagenow, $cacheweekly, $wpdb;
	$file = ($file=='') ? $pagenow : $file;
	switch(strtolower($mode)) {
		case 'title':
			$title = sanitize_title($post->post_title) . '-' . $post->ID;
			$anchor = $title;
			break;
		case 'id':
		default:
			$anchor = $id;
			break;
	}
	echo get_permalink();
}

function permalink_single($file = '') {
	echo get_permalink();
}

function permalink_single_rss($file = '') {
	global $siteurl;
	echo get_permalink();
}

/***** // Permalink tags *****/

function allowed_tags() {
	global $allowedtags;
	foreach($allowedtags as $tag => $attributes) {
		$allowed .= "<$tag";
		if (0 < count($attributes)) {
			foreach ($attributes as $attribute => $limits) {
				$allowed .= " $attribute=\"\"";
			}
		}
		$allowed .= "> ";
	}
	return htmlentities($allowed);
}

?>