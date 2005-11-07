<?php

/* Note: these tags go anywhere in the template */

function get_header() {
	if ( file_exists( TEMPLATEPATH . '/header.php') )
		load_template( TEMPLATEPATH . '/header.php');
	else
		load_template( ABSPATH . 'wp-content/themes/default/header.php');
}


function get_footer() {
	if ( file_exists( TEMPLATEPATH . '/footer.php') )
		load_template( TEMPLATEPATH . '/footer.php');
	else
		load_template( ABSPATH . 'wp-content/themes/default/footer.php');
}


function get_sidebar() {
	if ( file_exists( TEMPLATEPATH . '/sidebar.php') )
		load_template( TEMPLATEPATH . '/sidebar.php');
	else
		load_template( ABSPATH . 'wp-content/themes/default/sidebar.php');
}


function wp_loginout() {
	global $user_ID;
	get_currentuserinfo();

	if ('' == $user_ID)
		$link = '<a href="' . get_settings('siteurl') . '/wp-login.php">' . __('Login') . '</a>';
	else
		$link = '<a href="' . get_settings('siteurl') . '/wp-login.php?action=logout">' . __('Logout') . '</a>';

	echo apply_filters('loginout', $link);
}


function wp_register( $before = '<li>', $after = '</li>' ) {
	global $user_ID;

	get_currentuserinfo();

	if ( '' == $user_ID && get_settings('users_can_register') )
		$link = $before . '<a href="' . get_settings('siteurl') . '/wp-register.php">' . __('Register') . '</a>' . $after;
	elseif ( '' == $user_ID && !get_settings('users_can_register') )
		$link = '';
	else
		$link = $before . '<a href="' . get_settings('siteurl') . '/wp-admin/">' . __('Site Admin') . '</a>' . $after;

	echo apply_filters('register', $link);
}


function wp_meta() {
	do_action('wp_meta');
}


function bloginfo($show='') {
	$info = get_bloginfo($show);
	if ( ! strstr($info, 'url') ) {
		$info = apply_filters('bloginfo', $info, $show);
		$info = convert_chars($info);
	}

	echo $info;
}


function get_bloginfo($show='') {

	switch($show) {
		case 'url' :
		case 'home' :
		case 'siteurl' :
			$output = get_settings('home');
			break;
		case 'wpurl' :
			$output = get_settings('siteurl');
			break;
		case 'description':
			$output = get_settings('blogdescription');
			break;
		case 'rdf_url':
			$output = get_feed_link('rdf');
			break;
		case 'rss_url':
			$output = get_feed_link('rss');
			break;
		case 'rss2_url':
			$output = get_feed_link('rss2');
			break;
		case 'atom_url':
			$output = get_feed_link('atom');
			break;
		case 'comments_rss2_url':
			$output = get_feed_link('comments_rss2');
			break;
		case 'pingback_url':
			$output = get_settings('siteurl') .'/xmlrpc.php';
			break;
		case 'stylesheet_url':
			$output = get_stylesheet_uri();
			break;
		case 'stylesheet_directory':
			$output = get_stylesheet_directory_uri();
			break;
		case 'template_directory':
		case 'template_url':
			$output = get_template_directory_uri();
			break;
		case 'admin_email':
			$output = get_settings('admin_email');
			break;
		case 'charset':
			$output = get_settings('blog_charset');
			if ('' == $output) $output = 'UTF-8';
			break;
		case 'html_type' :
			$output = get_option('html_type');
			break;
		case 'version':
			global $wp_version;
			$output = $wp_version;
			break;
		case 'name':
		default:
			$output = get_settings('blogname');
			break;
	}
	return $output;
}


function wp_title($sep = '&raquo;', $display = true) {
	global $wpdb;
	global $m, $year, $monthnum, $day, $category_name, $month, $posts;

	$cat = get_query_var('cat');
	$p = get_query_var('p');
	$name = get_query_var('name');
	$category_name = get_query_var('category_name');

	// If there's a category
	if ( !empty($cat) ) {
			// category exclusion
			if ( !stristr($cat,'-') )
				$title = get_the_category_by_ID($cat);
	}
	if ( !empty($category_name) ) {
		if ( stristr($category_name,'/') ) {
				$category_name = explode('/',$category_name);
				if ( $category_name[count($category_name)-1] )
					$category_name = $category_name[count($category_name)-1]; // no trailing slash
				else
					$category_name = $category_name[count($category_name)-2]; // there was a trailling slash
		}
		$title = $wpdb->get_var("SELECT cat_name FROM $wpdb->categories WHERE category_nicename = '$category_name'");
	}

	// If there's a month
	if ( !empty($m) ) {
		$my_year = substr($m, 0, 4);
		$my_month = $month[substr($m, 4, 2)];
		$title = "$my_year $sep $my_month";
	}

	if ( !empty($year) ) {
		$title = $year;
		if ( !empty($monthnum) )
			$title .= " $sep ".$month[zeroise($monthnum, 2)];
		if ( !empty($day) )
			$title .= " $sep ".zeroise($day, 2);
	}

	// If there is a post
	if ( is_single() || is_page() ) {
		$title = strip_tags($posts[0]->post_title);
		$title = apply_filters('single_post_title', $title);
	}

	// Send it out
	if ( $display && isset($title) )
		echo " $sep $title";
	elseif ( !$display && isset($title) )
		return " $sep $title";
}


function single_post_title($prefix = '', $display = true) {
	global $wpdb;
	$p = get_query_var('p');
	$name = get_query_var('name');

	if ( intval($p) || '' != $name ) {
		if ( !$p )
			$p = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$name'");
		$post = & get_post($p);
		$title = $post->post_title;
		$title = apply_filters('single_post_title', $title);
		if ( $display )
			echo $prefix.strip_tags($title);
		else
			return strip_tags($title);
	}
}


function single_cat_title($prefix = '', $display = true ) {
	$cat = intval( get_query_var('cat') );
	if ( !empty($cat) && !(strtoupper($cat) == 'ALL') ) {
		$my_cat_name = get_the_category_by_ID($cat);
		if ( !empty($my_cat_name) ) {
			if ( $display )
				echo $prefix.strip_tags($my_cat_name);
			else
				return strip_tags($my_cat_name);
		}
	}
}


function single_month_title($prefix = '', $display = true ) {
	global $m, $monthnum, $month, $year;
	if ( !empty($monthnum) && !empty($year) ) {
		$my_year = $year;
		$my_month = $month[str_pad($monthnum, 2, '0', STR_PAD_LEFT)];
	} elseif ( !empty($m) ) {
		$my_year = substr($m, 0, 4);
		$my_month = $month[substr($m, 4, 2)];
	}

	if ( !empty($my_month) && $display )
		echo $prefix . $my_month . $prefix . $my_year;
	else
		return $monthnum;
}


/* link navigation hack by Orien http://icecode.com/ */
function get_archives_link($url, $text, $format = 'html', $before = '', $after = '') {
	$text = wptexturize($text);
	$title_text = wp_specialchars($text, 1);

	if ('link' == $format)
		return "\t<link rel='archives' title='$title_text' href='$url' />\n";
	elseif ('option' == $format)
		return "\t<option value='$url'>$before $text $after</option>\n";
	elseif ('html' == $format)
		return "\t<li>$before<a href='$url' title='$title_text'>$text</a>$after</li>\n";
	else // custom
		return "\t$before<a href='$url' title='$title_text'>$text</a>$after\n";
}


function wp_get_archives($args = '') {
	parse_str($args, $r);
	if ( !isset($r['type']) )
		$r['type'] = '';
	if ( !isset($r['limit']) )
		$r['limit'] = '';
	if ( !isset($r['format']) )
		$r['format'] = 'html';
	if ( !isset($r['before']) )
		$r['before'] = '';
	if ( !isset($r['after']) )
		$r['after'] = '';
	if ( !isset($r['show_post_count']) )
		$r['show_post_count'] = false;

	get_archives($r['type'], $r['limit'], $r['format'], $r['before'], $r['after'], $r['show_post_count']);
}


function get_archives($type='', $limit='', $format='html', $before = '', $after = '', $show_post_count = false) {
	global $month, $wpdb;

	if ( '' == $type )
		$type = 'monthly';

	if ( '' != $limit ) {
		$limit = (int) $limit;
		$limit = ' LIMIT '.$limit;
	}
	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
	$archive_date_format_over_ride = 0;

	// options for daily archive (only if you over-ride the general date format)
	$archive_day_date_format = 'Y/m/d';

	// options for weekly archive (only if you over-ride the general date format)
	$archive_week_start_date_format = 'Y/m/d';
	$archive_week_end_date_format	= 'Y/m/d';

	if ( !$archive_date_format_over_ride ) {
		$archive_day_date_format = get_settings('date_format');
		$archive_week_start_date_format = get_settings('date_format');
		$archive_week_end_date_format = get_settings('date_format');
	}

	$add_hours = intval(get_settings('gmt_offset'));
	$add_minutes = intval(60 * (get_settings('gmt_offset') - $add_hours));

	$now = current_time('mysql');

	if ( 'monthly' == $type ) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC" . $limit);
		if ( $arcresults ) {
			$afterafter = $after;
			foreach ( $arcresults as $arcresult ) {
				$url	= get_month_link($arcresult->year,	$arcresult->month);
				if ( $show_post_count ) {
					$text = sprintf('%s %d', $month[zeroise($arcresult->month,2)], $arcresult->year);
					$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
				} else {
					$text = sprintf('%s %d', $month[zeroise($arcresult->month,2)], $arcresult->year);
				}
				echo get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ( 'daily' == $type ) {
		$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth` FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		if ( $arcresults ) {
			foreach ( $arcresults as $arcresult ) {
				$url	= get_day_link($arcresult->year, $arcresult->month, $arcresult->dayofmonth);
				$date = sprintf("%d-%02d-%02d 00:00:00", $arcresult->year, $arcresult->month, $arcresult->dayofmonth);
				$text = mysql2date($archive_day_date_format, $date);
				echo get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ( 'weekly' == $type ) {
		$start_of_week = get_settings('start_of_week');
		$arcresults = $wpdb->get_results("SELECT DISTINCT WEEK(post_date, $start_of_week) AS `week`, YEAR(post_date) AS yr, DATE_FORMAT(post_date, '%Y-%m-%d') AS yyyymmdd FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		$arc_w_last = '';
		if ( $arcresults ) {
				foreach ( $arcresults as $arcresult ) {
					if ( $arcresult->week != $arc_w_last ) {
						$arc_year = $arcresult->yr;
						$arc_w_last = $arcresult->week;
						$arc_week = get_weekstartend($arcresult->yyyymmdd, get_settings('start_of_week'));
						$arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
						$arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
						$url  = sprintf('%s/%s%sm%s%s%sw%s%d', get_settings('home'), '', '?', '=', $arc_year, '&amp;', '=', $arcresult->week);
						$text = $arc_week_start . $archive_week_separator . $arc_week_end;
						echo get_archives_link($url, $text, $format, $before, $after);
					}
				}
		}
	} elseif ( 'postbypost' == $type ) {
		$arcresults = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' ORDER BY post_date DESC" . $limit);
		if ( $arcresults ) {
			foreach ( $arcresults as $arcresult ) {
				if ( $arcresult->post_date != '0000-00-00 00:00:00' ) {
					$url  = get_permalink($arcresult);
					$arc_title = $arcresult->post_title;
					if ( $arc_title )
						$text = strip_tags($arc_title);
					else
						$text = $arcresult->ID;
					echo get_archives_link($url, $text, $format, $before, $after);
				}
			}
		}
	}
}


// Used in get_calendar
function calendar_week_mod($num) {
	$base = 7;
	return ($num - $base*floor($num/$base));
}


function get_calendar($daylength = 1) {
	global $wpdb, $m, $monthnum, $year, $timedifference, $month, $month_abbrev, $weekday, $weekday_initial, $weekday_abbrev, $posts;

	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if ( !$gotsome )
			return;
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_settings('start_of_week'));
	$add_hours = intval(get_settings('gmt_offset'));
	$add_minutes = intval(60 * (get_settings('gmt_offset') - $add_hours));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$calendar = substr($m, 0, 6);
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp') + get_settings('gmt_offset') * 3600);
		$thismonth = gmdate('m', current_time('timestamp') + get_settings('gmt_offset') * 3600);
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$thisyear-$thismonth-01'
		AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");
	$next = $wpdb->get_row("SELECT	DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date >	'$thisyear-$thismonth-01'
		AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
		AND post_status = 'publish' 
			ORDER	BY post_date ASC
			LIMIT 1");

	echo '<table id="wp-calendar">
	<caption>' . $month[zeroise($thismonth, 2)] . ' ' . date('Y', $unixmonth) . '</caption>
	<thead>
	<tr>';

	$day_abbrev = $weekday_initial;
	if ( $daylength > 1 )
		$day_abbrev = $weekday_abbrev;

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[]=$weekday[($wdcount+$week_begins)%7];
	}

	foreach ( $myweek as $wd ) {
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">" . $day_abbrev[$wd] . '</th>';
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ( $previous ) {
		echo "\n\t\t".'<td abbr="' . $month[zeroise($previous->month, 2)] . '" colspan="3" id="prev"><a href="' .
		get_month_link($previous->year, $previous->month) . '" title="' . sprintf(__('View posts for %1$s %2$s'), $month[zeroise($previous->month, 2)], date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $month_abbrev[$month[zeroise($previous->month, 2)]] . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		echo "\n\t\t".'<td abbr="' . $month[zeroise($next->month, 2)] . '" colspan="3" id="next"><a href="' .
		get_month_link($next->year, $next->month) . '" title="View posts for ' . $month[zeroise($next->month, 2)] . ' ' .
		date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year)) . '">' . $month_abbrev[$month[zeroise($next->month, 2)]] . ' &raquo;</a></td>';
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
		FROM $wpdb->posts WHERE MONTH(post_date) = $thismonth
		AND YEAR(post_date) = $thisyear
		AND post_status = 'publish'
		AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);
	if ( $dayswithposts ) {
		foreach ( $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}



	if ( strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') )
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts "
		."WHERE YEAR(post_date) = '$thisyear' "
		."AND MONTH(post_date) = '$thismonth' "
		."AND post_date < '".current_time('mysql')."' "
		."AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( $ak_post_titles as $ak_post_title ) {
				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = str_replace('"', '&quot;', wptexturize($ak_post_title->post_title));
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . str_replace('"', '&quot;', wptexturize($ak_post_title->post_title));
		}
	}


	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', (time() + (get_settings('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_settings('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_settings('gmt_offset') * 3600)) )
			echo '<td id="today">';
		else
			echo '<td>';

		if ( in_array($day, $daywithpost) ) // any posts today?
				echo '<a href="' . get_day_link($thisyear, $thismonth, $day) . "\" title=\"$ak_titles_for_day[$day]\">$day</a>";
		else
			echo $day;
		echo '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

	echo "\n\t</tr>\n\t</tbody>\n\t</table>";
}


function allowed_tags() {
	global $allowedtags;
	$allowed = '';
	foreach ( $allowedtags as $tag => $attributes ) {
		$allowed .= '<'.$tag;
		if ( 0 < count($attributes) ) {
			foreach ( $attributes as $attribute => $limits ) {
				$allowed .= ' '.$attribute.'=""';
			}
		}
		$allowed .= '> ';
	}
	return htmlentities($allowed);
}


/***** Date/Time tags *****/


function the_date_xml() {
	global $post;
	echo mysql2date('Y-m-d', $post->post_date);
	//echo ""+$post->post_date;
}


function the_date($d='', $before='', $after='', $echo = true) {
	global $id, $post, $day, $previousday, $newday;
	$the_date = '';
	if ( $day != $previousday ) {
		$the_date .= $before;
		if ( $d=='' )
			$the_date .= mysql2date(get_settings('date_format'), $post->post_date);
		else
			$the_date .= mysql2date($d, $post->post_date);
		$the_date .= $after;
		$previousday = $day;
	}
	$the_date = apply_filters('the_date', $the_date, $d, $before, $after);
	if ( $echo )
		echo $the_date;
	else
		return $the_date;
}


function the_time( $d = '' ) {
	echo apply_filters('the_time', get_the_time( $d ), $d);
}


function get_the_time( $d = '' ) {
	if ( '' == $d )
		$the_time = get_post_time(get_settings('time_format'));
	else
		$the_time = get_post_time($d);
	return apply_filters('get_the_time', $the_time, $d);
}


function get_post_time( $d = 'U', $gmt = false ) { // returns timestamp
	global $post;
	if ( $gmt )
		$time = $post->post_date_gmt;
	else
		$time = $post->post_date;

	$time = mysql2date($d, $time);
	return apply_filters('get_the_time', $time, $d, $gmt);
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
	if ( $day != $previousweekday ) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $weekday[mysql2date('w', $post->post_date)];
		$the_weekday_date .= $after;
		$previousweekday = $day;
	}
	$the_weekday_date = apply_filters('the_weekday_date', $the_weekday_date, $before, $after);
	echo $the_weekday_date;
}

function rsd_link() {
	echo '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="' . get_bloginfo('url') . "/xmlrpc.php?rsd\" />\n";
}

?>