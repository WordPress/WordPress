<?php

/* Note: these tags go anywhere in the template */

function get_header() {
	do_action( 'get_header' );
	if ( file_exists( TEMPLATEPATH . '/header.php') )
		load_template( TEMPLATEPATH . '/header.php');
	else
		load_template( ABSPATH . 'wp-content/themes/default/header.php');
}


function get_footer() {
	do_action( 'get_footer' );
	if ( file_exists( TEMPLATEPATH . '/footer.php') )
		load_template( TEMPLATEPATH . '/footer.php');
	else
		load_template( ABSPATH . 'wp-content/themes/default/footer.php');
}


function get_sidebar( $name = null ) {
	do_action( 'get_sidebar' );
	if ( isset($name) && file_exists( TEMPLATEPATH . "/sidebar-{$name}.php") )
		load_template( TEMPLATEPATH . "/sidebar-{$name}.php");
	elseif ( file_exists( TEMPLATEPATH . '/sidebar.php') )
		load_template( TEMPLATEPATH . '/sidebar.php');
	else
		load_template( ABSPATH . 'wp-content/themes/default/sidebar.php');
}


function wp_loginout() {
	if ( ! is_user_logged_in() )
		$link = '<a href="' . get_option('siteurl') . '/wp-login.php">' . __('Log in') . '</a>';
	else
		$link = '<a href="' . get_option('siteurl') . '/wp-login.php?action=logout">' . __('Log out') . '</a>';

	echo apply_filters('loginout', $link);
}


function wp_register( $before = '<li>', $after = '</li>' ) {

	if ( ! is_user_logged_in() ) {
		if ( get_option('users_can_register') )
			$link = $before . '<a href="' . get_option('siteurl') . '/wp-login.php?action=register">' . __('Register') . '</a>' . $after;
		else
			$link = '';
	} else {
		$link = $before . '<a href="' . get_option('siteurl') . '/wp-admin/">' . __('Site Admin') . '</a>' . $after;
	}

	echo apply_filters('register', $link);
}


function wp_meta() {
	do_action('wp_meta');
}


function bloginfo($show='') {
	echo get_bloginfo($show, 'display');
}

/**
 * Note: some of these values are DEPRECATED. Meaning they could be
 * taken out at any time and shouldn't be relied upon. Options
 * without "// DEPRECATED" are the preferred and recommended ways
 * to get the information.
 */
function get_bloginfo($show = '', $filter = 'raw') {

	switch($show) {
		case 'url' :
		case 'home' : // DEPRECATED
		case 'siteurl' : // DEPRECATED
			$output = get_option('home');
			break;
		case 'wpurl' :
			$output = get_option('siteurl');
			break;
		case 'description':
			$output = get_option('blogdescription');
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
		case 'comments_atom_url':
			$output = get_feed_link('comments_atom');
			break;
		case 'comments_rss2_url':
			$output = get_feed_link('comments_rss2');
			break;
		case 'pingback_url':
			$output = get_option('siteurl') .'/xmlrpc.php';
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
			$output = get_option('admin_email');
			break;
		case 'charset':
			$output = get_option('blog_charset');
			if ('' == $output) $output = 'UTF-8';
			break;
		case 'html_type' :
			$output = get_option('html_type');
			break;
		case 'version':
			global $wp_version;
			$output = $wp_version;
			break;
		case 'language':
			$output = get_locale();
			$output = str_replace('_', '-', $output);
			break;
		case 'text_direction':
			global $wp_locale;
			$output = $wp_locale->text_direction;
			break;
		case 'name':
		default:
			$output = get_option('blogname');
			break;
	}

	$url = true;
	if (strpos($show, 'url') === false &&
		strpos($show, 'directory') === false &&
		strpos($show, 'home') === false)
		$url = false;

	if ( 'display' == $filter ) {
		if ( $url )
			$output = apply_filters('bloginfo_url', $output, $show);
		else
			$output = apply_filters('bloginfo', $output, $show);
	}

	return $output;
}


function wp_title($sep = '&raquo;', $display = true, $seplocation = '') {
	global $wpdb, $wp_locale, $wp_query;

	$cat = get_query_var('cat');
	$tag = get_query_var('tag_id');
	$category_name = get_query_var('category_name');
	$author = get_query_var('author');
	$author_name = get_query_var('author_name');
	$m = get_query_var('m');
	$year = get_query_var('year');
	$monthnum = get_query_var('monthnum');
	$day = get_query_var('day');
	$title = '';

	// If there's a category
	if ( !empty($cat) ) {
			// category exclusion
			if ( !stristr($cat,'-') )
				$title = apply_filters('single_cat_title', get_the_category_by_ID($cat));
	} elseif ( !empty($category_name) ) {
		if ( stristr($category_name,'/') ) {
				$category_name = explode('/',$category_name);
				if ( $category_name[count($category_name)-1] )
					$category_name = $category_name[count($category_name)-1]; // no trailing slash
				else
					$category_name = $category_name[count($category_name)-2]; // there was a trailling slash
		}
		$cat = get_term_by('slug', $category_name, 'category', OBJECT, 'display');
		if ( $cat )
			$title = apply_filters('single_cat_title', $cat->name);
	}

	if ( !empty($tag) ) {
		$tag = get_term($tag, 'post_tag', OBJECT, 'display');
		if ( is_wp_error( $tag ) )
			return $tag;
		if ( ! empty($tag->name) )
			$title = apply_filters('single_tag_title', $tag->name);
	}

	// If there's an author
	if ( !empty($author) ) {
		$title = get_userdata($author);
		$title = $title->display_name;
	}
	if ( !empty($author_name) ) {
		// We do a direct query here because we don't cache by nicename.
		$title = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM $wpdb->users WHERE user_nicename = %s", $author_name));
	}

	// If there's a month
	if ( !empty($m) ) {
		$my_year = substr($m, 0, 4);
		$my_month = $wp_locale->get_month(substr($m, 4, 2));
		$my_day = intval(substr($m, 6, 2));
		$title = "$my_year" . ($my_month ? "$sep $my_month" : "") . ($my_day ? "$sep $my_day" : "");
	}

	if ( !empty($year) ) {
		$title = $year;
		if ( !empty($monthnum) )
			$title .= " $sep " . $wp_locale->get_month($monthnum);
		if ( !empty($day) )
			$title .= " $sep " . zeroise($day, 2);
	}

	// If there is a post
	if ( is_single() || is_page() ) {
		$post = $wp_query->get_queried_object();
		$title = strip_tags( apply_filters( 'single_post_title', $post->post_title ) );
	}

	$prefix = '';
	if ( !empty($title) )
		$prefix = " $sep ";

 	// Determines position of the separator
	if ( 'right' == $seplocation )
		$title = $title . $prefix;
	else
		$title = $prefix . $title;

	$title = apply_filters('wp_title', $title, $sep);

	// Send it out
	if ( $display )
		echo $title;
	else
		return $title;

}


function single_post_title($prefix = '', $display = true) {
	global $wpdb;
	$p = get_query_var('p');
	$name = get_query_var('name');

	if ( intval($p) || '' != $name ) {
		if ( !$p )
			$p = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s", $name));
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
		$my_cat_name = apply_filters('single_cat_title', get_the_category_by_ID($cat));
		if ( !empty($my_cat_name) ) {
			if ( $display )
				echo $prefix.strip_tags($my_cat_name);
			else
				return strip_tags($my_cat_name);
		}
	} else if ( is_tag() ) {
		return single_tag_title($prefix, $display);
	}
}


function single_tag_title($prefix = '', $display = true ) {
	if ( !is_tag() )
		return;

	$tag_id = intval( get_query_var('tag_id') );

	if ( !empty($tag_id) ) {
		$my_tag = &get_term($tag_id, 'post_tag', OBJECT, 'display');
		if ( is_wp_error( $my_tag ) )
			return false;
		$my_tag_name = apply_filters('single_tag_title', $my_tag->name);
		if ( !empty($my_tag_name) ) {
			if ( $display )
				echo $prefix . $my_tag_name;
			else
				return $my_tag_name;
		}
	}
}


function single_month_title($prefix = '', $display = true ) {
	global $wp_locale;

	$m = get_query_var('m');
	$year = get_query_var('year');
	$monthnum = get_query_var('monthnum');

	if ( !empty($monthnum) && !empty($year) ) {
		$my_year = $year;
		$my_month = $wp_locale->get_month($monthnum);
	} elseif ( !empty($m) ) {
		$my_year = substr($m, 0, 4);
		$my_month = $wp_locale->get_month(substr($m, 4, 2));
	}

	if ( empty($my_month) )
		return false;

	$result = $prefix . $my_month . $prefix . $my_year;

	if ( !$display )
		return $result;
	echo $result;
}


/* link navigation hack by Orien http://icecode.com/ */
function get_archives_link($url, $text, $format = 'html', $before = '', $after = '') {
	$text = wptexturize($text);
	$title_text = attribute_escape($text);
	$url = clean_url($url);

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
	global $wpdb, $wp_locale;

	$defaults = array(
		'type' => 'monthly', 'limit' => '',
		'format' => 'html', 'before' => '',
		'after' => '', 'show_post_count' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( '' == $type )
		$type = 'monthly';

	if ( '' != $limit ) {
		$limit = absint($limit);
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
		$archive_day_date_format = get_option('date_format');
		$archive_week_start_date_format = get_option('date_format');
		$archive_week_end_date_format = get_option('date_format');
	}

	//filters
	$where = apply_filters('getarchives_where', "WHERE post_type = 'post' AND post_status = 'publish'", $r );
	$join = apply_filters('getarchives_join', "", $r);

	if ( 'monthly' == $type ) {
		$query = "SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_add( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ( $arcresults ) {
			$afterafter = $after;
			foreach ( $arcresults as $arcresult ) {
				$url	= get_month_link($arcresult->year,	$arcresult->month);
				$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month($arcresult->month), $arcresult->year);
				if ( $show_post_count )
					$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
				echo get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ('yearly' == $type) {
		$query = "SELECT DISTINCT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_add( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ($arcresults) {
			$afterafter = $after;
			foreach ($arcresults as $arcresult) {
				$url = get_year_link($arcresult->year);
				$text = sprintf('%d', $arcresult->year);
				if ($show_post_count)
					$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
				echo get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ( 'daily' == $type ) {
		$query = "SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_add( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ( $arcresults ) {
			$afterafter = $after;
			foreach ( $arcresults as $arcresult ) {
				$url	= get_day_link($arcresult->year, $arcresult->month, $arcresult->dayofmonth);
				$date = sprintf('%1$d-%2$02d-%3$02d 00:00:00', $arcresult->year, $arcresult->month, $arcresult->dayofmonth);
				$text = mysql2date($archive_day_date_format, $date);
				if ($show_post_count)
					$after = '&nbsp;('.$arcresult->posts.')'.$afterafter;
				echo get_archives_link($url, $text, $format, $before, $after);
			}
		}
	} elseif ( 'weekly' == $type ) {
		$start_of_week = get_option('start_of_week');
		$query = "SELECT DISTINCT WEEK(post_date, $start_of_week) AS `week`, YEAR(post_date) AS yr, DATE_FORMAT(post_date, '%Y-%m-%d') AS yyyymmdd, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY WEEK(post_date, $start_of_week), YEAR(post_date) ORDER BY post_date DESC $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_add( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		$arc_w_last = '';
		$afterafter = $after;
		if ( $arcresults ) {
				foreach ( $arcresults as $arcresult ) {
					if ( $arcresult->week != $arc_w_last ) {
						$arc_year = $arcresult->yr;
						$arc_w_last = $arcresult->week;
						$arc_week = get_weekstartend($arcresult->yyyymmdd, get_option('start_of_week'));
						$arc_week_start = date_i18n($archive_week_start_date_format, $arc_week['start']);
						$arc_week_end = date_i18n($archive_week_end_date_format, $arc_week['end']);
						$url  = sprintf('%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', get_option('home'), '', '?', '=', $arc_year, '&amp;', '=', $arcresult->week);
						$text = $arc_week_start . $archive_week_separator . $arc_week_end;
						if ($show_post_count)
							$after = '&nbsp;('.$arcresult->posts.')'.$afterafter;
						echo get_archives_link($url, $text, $format, $before, $after);
					}
				}
		}
	} elseif ( ( 'postbypost' == $type ) || ('alpha' == $type) ) {
		('alpha' == $type) ? $orderby = "post_title ASC " : $orderby = "post_date DESC ";
		$query = "SELECT * FROM $wpdb->posts $join $where ORDER BY $orderby $limit";
		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_add( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}
		if ( $arcresults ) {
			foreach ( $arcresults as $arcresult ) {
				if ( $arcresult->post_date != '0000-00-00 00:00:00' ) {
					$url  = get_permalink($arcresult);
					$arc_title = $arcresult->post_title;
					if ( $arc_title )
						$text = strip_tags(apply_filters('the_title', $arc_title));
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


function get_calendar($initial = true) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

	$key = md5( $m . $monthnum . $year );
	if ( $cache = wp_cache_get( 'get_calendar', 'calendar' ) ) {
		if ( isset( $cache[ $key ] ) ) {
			echo $cache[ $key ];
			return;
		}
	}

	ob_start();
	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if ( !$gotsome )
			return;
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

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
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp'));
		$thismonth = gmdate('m', current_time('timestamp'));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$thisyear-$thismonth-01'
		AND post_type = 'post' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");
	$next = $wpdb->get_row("SELECT	DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date >	'$thisyear-$thismonth-01'
		AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
		AND post_type = 'post' AND post_status = 'publish'
			ORDER	BY post_date ASC
			LIMIT 1");

	echo '<table id="wp-calendar" summary="' . __('Calendar') . '">
	<caption>' . sprintf(_c('%1$s %2$s|Used as a calendar caption'), $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ( $previous ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($previous->month) . '" colspan="3" id="prev"><a href="' .
		get_month_link($previous->year, $previous->month) . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month),
			date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($next->month) . '" colspan="3" id="next"><a href="' .
		get_month_link($next->year, $next->month) . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month),
			date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
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
		FROM $wpdb->posts WHERE MONTH(post_date) = '$thismonth'
		AND YEAR(post_date) = '$thisyear'
		AND post_type = 'post' AND post_status = 'publish'
		AND post_date < '" . current_time('mysql') . '\'', ARRAY_N);
	if ( $dayswithposts ) {
		foreach ( $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts "
		."WHERE YEAR(post_date) = '$thisyear' "
		."AND MONTH(post_date) = '$thismonth' "
		."AND post_date < '".current_time('mysql')."' "
		."AND post_type = 'post' AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( $ak_post_titles as $ak_post_title ) {

				$post_title = apply_filters( "the_title", $ak_post_title->post_title );
				$post_title = str_replace('"', '&quot;', wptexturize( $post_title ));

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
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

		if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
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

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	$cache[ $key ] = $output;
	wp_cache_set( 'get_calendar', $cache, 'calendar' );
}

function delete_get_calendar_cache() {
	wp_cache_delete( 'get_calendar', 'calendar' );
}
add_action( 'save_post', 'delete_get_calendar_cache' );
add_action( 'delete_post', 'delete_get_calendar_cache' );
add_action( 'update_option_start_of_week', 'delete_get_calendar_cache' );
add_action( 'update_option_gmt_offset', 'delete_get_calendar_cache' );
add_action( 'update_option_start_of_week', 'delete_get_calendar_cache' );


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
	global $post, $day, $previousday;
	$the_date = '';
	if ( $day != $previousday ) {
		$the_date .= $before;
		if ( $d=='' )
			$the_date .= mysql2date(get_option('date_format'), $post->post_date);
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


function the_modified_date($d = '') {
	echo apply_filters('the_modified_date', get_the_modified_date($d), $d);
}


function get_the_modified_date($d = '') {
	if ( '' == $d )
		$the_time = get_post_modified_time(get_option('date_format'));
	else
		$the_time = get_post_modified_time($d);
	return apply_filters('get_the_modified_date', $the_time, $d);
}


function the_time( $d = '' ) {
	echo apply_filters('the_time', get_the_time( $d ), $d);
}


function get_the_time( $d = '' ) {
	if ( '' == $d )
		$the_time = get_post_time(get_option('time_format'));
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


function the_modified_time($d = '') {
	echo apply_filters('the_modified_time', get_the_modified_time($d), $d);
}


function get_the_modified_time($d = '') {
	if ( '' == $d )
		$the_time = get_post_modified_time(get_option('time_format'));
	else
		$the_time = get_post_modified_time($d);
	return apply_filters('get_the_modified_time', $the_time, $d);
}


function get_post_modified_time( $d = 'U', $gmt = false ) { // returns timestamp
	global $post;

	if ( $gmt )
		$time = $post->post_modified_gmt;
	else
		$time = $post->post_modified;
	$time = mysql2date($d, $time);

	return apply_filters('get_the_modified_time', $time, $d, $gmt);
}


function the_weekday() {
	global $wp_locale, $post;
	$the_weekday = $wp_locale->get_weekday(mysql2date('w', $post->post_date));
	$the_weekday = apply_filters('the_weekday', $the_weekday);
	echo $the_weekday;
}


function the_weekday_date($before='',$after='') {
	global $wp_locale, $post, $day, $previousweekday;
	$the_weekday_date = '';
	if ( $day != $previousweekday ) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $wp_locale->get_weekday(mysql2date('w', $post->post_date));
		$the_weekday_date .= $after;
		$previousweekday = $day;
	}
	$the_weekday_date = apply_filters('the_weekday_date', $the_weekday_date, $before, $after);
	echo $the_weekday_date;
}

function wp_head() {
	do_action('wp_head');
}

function wp_footer() {
	do_action('wp_footer');
}

function rsd_link() {
	echo '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="' . get_bloginfo('wpurl') . "/xmlrpc.php?rsd\" />\n";
}

function wlwmanifest_link() {
	echo '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="'
		. get_bloginfo('wpurl') . '/wp-includes/wlwmanifest.xml" /> ' . "\n";
}

function noindex() {
	// If the blog is not public, tell robots to go away.
	if ( '0' == get_option('blog_public') )
		echo "<meta name='robots' content='noindex,nofollow' />\n";
}

function rich_edit_exists() {
	global $wp_rich_edit_exists;
	if ( !isset($wp_rich_edit_exists) )
		$wp_rich_edit_exists = file_exists(ABSPATH . WPINC . '/js/tinymce/tiny_mce.js');
	return $wp_rich_edit_exists;
}

function user_can_richedit() {
	global $wp_rich_edit, $pagenow;

	if ( !isset( $wp_rich_edit) ) {
		if ( get_user_option( 'rich_editing' ) == 'true' &&
			( ( preg_match( '!AppleWebKit/(\d+)!', $_SERVER['HTTP_USER_AGENT'], $match ) && intval($match[1]) >= 420 ) ||
				!preg_match( '!opera[ /][2-8]|konqueror|safari!i', $_SERVER['HTTP_USER_AGENT'] ) )
				&& 'comment.php' != $pagenow ) {
			$wp_rich_edit = true;
		} else {
			$wp_rich_edit = false;
		}
	}

	return apply_filters('user_can_richedit', $wp_rich_edit);
}

function wp_default_editor() {
	$r = user_can_richedit() ? 'tinymce' : 'html'; // defaults
	if ( $user = wp_get_current_user() ) { // look for cookie
		if ( isset($_COOKIE['wordpress_editor_' . $user->ID]) && in_array($_COOKIE['wordpress_editor_' . $user->ID], array('tinymce', 'html', 'test') ) )
			$r = $_COOKIE['wordpress_editor_' . $user->ID];
	}
	return apply_filters( 'wp_default_editor', $r ); // filter
}

function the_editor($content, $id = 'content', $prev_id = 'title', $media_buttons = true, $tab_index = 2) {
	$rows = get_option('default_post_edit_rows');
	if (($rows < 3) || ($rows > 100))
		$rows = 12;

	$rows = "rows='$rows'";	?>
	<div id="editor-toolbar">
	<?php if ( user_can_richedit() ) {
		$wp_default_editor = wp_default_editor(); ?>
		<div class="zerosize"><input accesskey="e" type="button" onclick="switchEditors.go('<?php echo $id; ?>')" /></div>
		<?php if ( 'tinymce' == $wp_default_editor ) {
			add_filter('the_editor_content', 'wp_richedit_pre'); ?>
			<a id="edButtonHTML" onclick="switchEditors.go('<?php echo $id; ?>');"><?php _e('HTML'); ?></a>
			<a id="edButtonPreview" class="active"><?php _e('Visual'); ?></a>
		<?php } elseif ( 'html' == $wp_default_editor ) {
			add_filter('the_editor_content', 'wp_htmledit_pre'); ?>
			<a id="edButtonHTML" class="active"><?php _e('HTML'); ?></a>
			<a id="edButtonPreview" onclick="switchEditors.go('<?php echo $id; ?>');"><?php _e('Visual'); ?></a>
		<?php }
	}

	if ( $media_buttons ) { ?>
		<div id="media-buttons" class="hide-if-no-js">
		<?php do_action( 'media_buttons' ); ?>
		</div>
	<?php } ?>
	</div>

	<div id="quicktags">
	<?php wp_print_scripts( 'quicktags' ); ?>
	<script type="text/javascript">edToolbar()</script>
	</div>

    <?php if ( 'html' != $wp_default_editor ) : ?>
    <script type="text/javascript">
    // <![CDATA[
        if ( typeof tinyMCE != "undefined" )
            document.getElementById("quicktags").style.display="none";
    // ]]>
    </script>
    <?php endif; // 'html' != $wp_default_editor

	$the_editor = apply_filters('the_editor', "<div id='editorcontainer'><textarea class='' $rows cols='40' name='$id' tabindex='$tab_index' id='$id'>%s</textarea></div>\n");
	$the_editor_content = apply_filters('the_editor_content', $content);

	printf($the_editor, $the_editor_content);

	?>
    <script type="text/javascript">
    // <![CDATA[
    edCanvas = document.getElementById('<?php echo $id; ?>');
    <?php if ( $prev_id && user_can_richedit() ) : ?>
    // If tinyMCE is defined.
    if ( typeof tinyMCE != 'undefined' ) {
    // This code is meant to allow tabbing from Title to Post (TinyMCE).
        document.getElementById('<?php echo $prev_id; ?>').onkeydown = function (e) {
            e = e || window.event;
            if (e.keyCode == 9 && !e.shiftKey && !e.controlKey && !e.altKey) {
                if ( tinyMCE.activeEditor ) {
                    if ( (jQuery("#post_ID").val() < 1) && (jQuery("#title").val().length > 0) ) { autosave(); }
                    e = null;
                    if ( tinyMCE.activeEditor.isHidden() ) return true;
                    tinyMCE.activeEditor.focus();
                    return false;
                }
                return true;
            }
        }
    }
    <?php endif; ?>
    // ]]>
    </script>
    <?php
}

function get_search_query() {
	return apply_filters( 'get_search_query', stripslashes( get_query_var( 's' ) ) );
}

function the_search_query() {
	echo attribute_escape( apply_filters( 'the_search_query', get_search_query() ) );
}

function language_attributes($doctype = 'html') {
	$attributes = array();
	$output = '';

	if ( $dir = get_bloginfo('text_direction') )
		$attributes[] = "dir=\"$dir\"";

	if ( $lang = get_bloginfo('language') ) {
		if ( get_option('html_type') == 'text/html' || $doctype == 'xhtml' )
			$attributes[] = "lang=\"$lang\"";

		if ( get_option('html_type') != 'text/html' || $doctype == 'xhtml' )
			$attributes[] = "xml:lang=\"$lang\"";
	}

	$output = implode(' ', $attributes);
	$output = apply_filters('language_attributes', $output);
	echo $output;
}

function paginate_links( $args = '' ) {
	$defaults = array(
		'base' => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
		'total' => 1,
		'current' => 0,
		'show_all' => false,
		'prev_next' => true,
		'prev_text' => __('&laquo; Previous'),
		'next_text' => __('Next &raquo;'),
		'end_size' => 1, // How many numbers on either end including the end
		'mid_size' => 2, // How many numbers to either side of current not including current
		'type' => 'plain',
		'add_args' => false // array of query args to aadd
	);

	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);

	// Who knows what else people pass in $args
	$total    = (int) $total;
	if ( $total < 2 )
		return;
	$current  = (int) $current;
	$end_size = 0  < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
	$mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
	$add_args = is_array($add_args) ? $add_args : false;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	if ( $prev_next && $current && 1 < $current ) :
		$link = str_replace('%_%', 2 == $current ? '' : $format, $base);
		$link = str_replace('%#%', $current - 1, $link);
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$page_links[] = "<a class='prev page-numbers' href='" . clean_url($link) . "'>$prev_text</a>";
	endif;
	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = "<span class='page-numbers current'>$n</span>";
			$dots = true;
		else :
			if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace('%_%', 1 == $n ? '' : $format, $base);
				$link = str_replace('%#%', $n, $link);
				if ( $add_args )
					$link = add_query_arg( $add_args, $link );
				$page_links[] = "<a class='page-numbers' href='" . clean_url($link) . "'>$n</a>";
				$dots = true;
			elseif ( $dots && !$show_all ) :
				$page_links[] = "<span class='page-numbers dots'>...</span>";
				$dots = false;
			endif;
		endif;
	endfor;
	if ( $prev_next && $current && ( $current < $total || -1 == $total ) ) :
		$link = str_replace('%_%', $format, $base);
		$link = str_replace('%#%', $current + 1, $link);
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$page_links[] = "<a class='next page-numbers' href='" . clean_url($link) . "'>$next_text</a>";
	endif;
	switch ( $type ) :
		case 'array' :
			return $page_links;
			break;
		case 'list' :
			$r .= "<ul class='page-numbers'>\n\t<li>";
			$r .= join("</li>\n\t<li>", $page_links);
			$r .= "</li>\n</ul>\n";
			break;
		default :
			$r = join("\n", $page_links);
			break;
	endswitch;
	return $r;
}

function wp_admin_css_color($key, $name, $url, $colors = array()) {
	global $_wp_admin_css_colors;

	if ( !isset($_wp_admin_css_colors) )
		$_wp_admin_css_colors = array();

	$_wp_admin_css_colors[$key] = (object) array('name' => $name, 'url' => $url, 'colors' => $colors);
}

function wp_admin_css_uri( $file = 'wp-admin' ) {
	if ( defined('WP_INSTALLING') ) {
		$_file = "./$file.css";
	} else {
		if ( 'css/colors' == $file || 'css/colors-rtl' == $file ) {
			global $_wp_admin_css_colors;
			$color = get_user_option('admin_color');
			if ( empty($color) || !isset($_wp_admin_css_colors[$color]) )
				$color = 'fresh';
			$color = $_wp_admin_css_colors[$color];
			$_file = $color->url;
			$_file = ('css/colors-rtl' == $file) ? str_replace('.css','-rtl.css',$_file) : $_file;
		} else {
			$_file = get_option( 'siteurl' ) . "/wp-admin/$file.css";
		}
	}
	$_file = add_query_arg( 'version', get_bloginfo( 'version' ),  $_file );

	return apply_filters( 'wp_admin_css_uri', $_file, $file );
}

function wp_admin_css( $file = 'wp-admin' ) {

	echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . wp_admin_css_uri( $file ) . "' type='text/css' />\n", $file );
	if ( 'rtl' == get_bloginfo( 'text_direction' ) ) {
		$rtl = ( 'wp-admin' == $file ) ? 'rtl' : "$file-rtl";
		echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . wp_admin_css_uri( $rtl ) . "' type='text/css' />\n", $rtl );
	}
}

/**
 * Outputs the XHTML generator that is generated on the wp_head hook.
 */
function wp_generator()
{
	the_generator( apply_filters( 'wp_generator_type', 'xhtml' ) );
}

/**
 * Outputs the generator XML or Comment for RSS, ATOM, etc.
 * @param {String} $type The type of generator to return.
 */
function the_generator ( $type ) {
	echo apply_filters('the_generator',get_the_generator($type),$type) . "\n";
}

/**
 * Creates the generator XML or Comment for RSS, ATOM, etc.
 * @param {String} $type The type of generator to return.
 */
function get_the_generator ( $type ) {
	switch ($type) {
		case 'html':
			$gen = '<meta name="generator" content="WordPress ' . get_bloginfo( 'version' ) . '">' . "\n";
			break;
		case 'xhtml':
			$gen = '<meta name="generator" content="WordPress ' . get_bloginfo( 'version' ) . '" />' . "\n";
			break;
		case 'atom':
			$gen = '<generator uri="http://wordpress.org/" version="' . get_bloginfo_rss( 'version' ) . '">WordPress</generator>';
			break;
		case 'rss2':
			$gen = '<generator>http://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '</generator>';
			break;
		case 'rdf':
			$gen = '<admin:generatorAgent rdf:resource="http://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '" />';
			break;
		case 'comment':
			$gen = '<!-- generator="WordPress/' . get_bloginfo( 'version' ) . '" -->';
			break;
		case 'export':
			$gen = '<!-- generator="WordPress/' . get_bloginfo_rss('version') . '" created="'. date('Y-m-d H:i') . '"-->';
			break;
	}
	return apply_filters( "get_the_generator_{$type}", $gen, $type );
}
?>
