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
			$output = $siteurl.'/b2rdf.php';
			break;
		case "rss_url":
			$output = $siteurl.'/b2rss.php';
			break;
		case "rss2_url":
			$output = $siteurl.'/b2rss2.php';
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
	return($output);
}

function single_post_title($prefix = '', $display = 1) {
	global $p;
	if (intval($p)) {
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

function single_cat_title($prefix = '', $display = 1 ) {
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

function single_month_title($prefix = '', $display = 1 ) {
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

function get_archives($type, $limit='') {
	global $tableposts, $dateformat, $time_difference, $siteurl, $blogfilename, $querystring_start, $querystring_equal, $month;
	// weekly and daily are *broken*
	dbconnect();
	if ('' != $limit) {
		$limit = (int) $limit;
		$limit= " LIMIT $limit";
	}
	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';
	
	
	// archive link url
	$archive_link_m = $siteurl.'/'.$blogfilename.$querystring_start.'m'.$querystring_equal;	# monthly archive
	$archive_link_w = $siteurl.'/'.$blogfilename.$querystring_start.'w'.$querystring_equal;	# weekly archive
	$archive_link_p = $siteurl.'/'.$blogfilename.$querystring_start.'p'.$querystring_equal;	# post-by-post archive
	
	
	$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));
	
	if ($type == 'monthly') {
		$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date) FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC" . $limit;
		$querycount++;
		$arc_result=mysql_query($arc_sql) or die($arc_sql.'<br />'.mysql_error());
		while($arc_row = mysql_fetch_array($arc_result)) {
			$arc_year  = $arc_row['YEAR(post_date)'];
			$arc_month = $arc_row['MONTH(post_date)'];
			echo "<li><a href=\"$archive_link_m$arc_year".zeroise($arc_month,2).'">';
			echo $month[zeroise($arc_month,2)].' '.$arc_year;
			echo "</a></li>\n";
		}
	} elseif ($type == 'daily') {
		$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC" . $limit;
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
	} elseif ($type == 'weekly') {
		if (!isset($start_of_week)) {
			$start_of_week = 1;
		}
		$arc_sql = "SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date), WEEK(post_date) FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC" . $limit;
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
	} elseif ($type == 'postbypost') {
		$requestarc = " SELECT ID,post_date,post_title FROM $tableposts WHERE post_date < '$now' AND post_category > 0 ORDER BY post_date DESC" . $limit;
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
}
/***** // About-the-blog tags *****/




/***** Date/Time tags *****/

function the_date($d='', $before='', $after='', $echo = 1) {
	global $id, $postdata, $day, $previousday,$dateformat,$newday;
	$the_date = '';
	if ($day != $previousday) {
		$the_date .= $before;
		if ($d=='') {
			$the_date .= mysql2date($dateformat, $postdata['Date']);
		} else {
			$the_date .= mysql2date($d, $postdata['Date']);
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

function the_time($d='', $echo = 1) {
	global $id,$postdata,$timeformat;
	if ($d=='') {
		$the_time = mysql2date($timeformat, $postdata['Date']);
	} else {
		$the_time = mysql2date($d, $postdata['Date']);
	}
	$the_time = apply_filters('the_time', $the_time);
	if ($echo) {
		echo $the_time;
	} else {
		return $the_time;
	}
}

function the_weekday() {
	global $weekday,$id,$postdata;
	$the_weekday = $weekday[mysql2date('w', $postdata['Date'])];
	$the_weekday = apply_filters('the_weekday', $the_weekday);
	echo $the_weekday;
}

function the_weekday_date($before='',$after='') {
	global $weekday,$id,$postdata,$day,$previousweekday;
	$the_weekday_date = '';
	if ($day != $previousweekday) {
		$the_weekday_date .= $before;
		$the_weekday_date .= $weekday[mysql2date('w', $postdata['Date'])];
		$the_weekday_date .= $after;
		$previousweekday = $day;
	}
	$the_weekday_date = apply_filters('the_weekday_date', $the_weekday_date);
	echo $the_weekday_date;
}

/***** // Date/Time tags *****/




/***** Author tags *****/

function the_author() {
	global $id,$authordata;
	$i = $authordata['user_idmode'];
	if ($i == 'nickname')	echo $authordata['user_nickname'];
	if ($i == 'login')	echo $authordata['user_login'];
	if ($i == 'firstname')	echo $authordata['user_firstname'];
	if ($i == 'lastname')	echo $authordata['user_lastname'];
	if ($i == 'namefl')	echo $authordata['user_firstname'].' '.$authordata['user_lastname'];
	if ($i == 'namelf')	echo $authordata['user_lastname'].' '.$authordata['user_firstname'];
	if (!$i) echo $authordata['user_nickname'];
}

function the_author_login() {
	global $id,$authordata;	echo $authordata['user_login'];
}

function the_author_firstname() {
	global $id,$authordata;	echo $authordata['user_firstname'];
}

function the_author_lastname() {
	global $id,$authordata;	echo $authordata['user_lastname'];
}

function the_author_nickname() {
	global $id,$authordata;	echo $authordata['user_nickname'];
}

function the_author_ID() {
	global $id,$authordata;	echo $authordata['ID'];
}

function the_author_email() {
	global $id,$authordata;	echo antispambot($authordata['user_email']);
}

function the_author_url() {
	global $id,$authordata;	echo $authordata['user_url'];
}

function the_author_icq() {
	global $id,$authordata;	echo $authordata['user_icq'];
}

function the_author_aim() {
	global $id,$authordata;	echo str_replace(' ', '+', $authordata['user_aim']);
}

function the_author_yim() {
	global $id,$authordata;	echo $authordata['user_yim'];
}

function the_author_msn() {
	global $id,$authordata;	echo $authordata['user_msn'];
}

function the_author_posts() {
	global $id,$postdata;	$posts=get_usernumposts($postdata['Author_ID']);	echo $posts;
}

/***** // Author tags *****/




/***** Post tags *****/

function the_ID() {
	global $id;
	echo $id;
}

function the_title($before='',$after='') {
	$title = get_the_title();
	$title = convert_bbcode($title);
	$title = convert_gmcode($title);
	$title = convert_smilies($title);
	$title = apply_filters('the_title', $title);
	if ($title) {
		echo convert_chars($before.$title.$after, 'html');
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
	$title = apply_filters('the_title_unicode', $title);
	if (trim($title)) {
		echo convert_chars($before.$title.$after, 'unicode');
	}
}
function get_the_title() {
	global $id,$postdata;
	$output = stripslashes($postdata['Title']);
	$output = apply_filters('the_title', $output);
	return($output);
}

function the_content($more_link_text='(more...)', $stripteaser=0, $more_file='') {
	$content = get_the_content($more_link_text,$stripteaser,$more_file);
	$content = convert_bbcode($content);
	$content = convert_gmcode($content);
	$content = convert_smilies($content);
	$content = convert_chars($content, 'html');
	$content = apply_filters('the_content', $content);
	echo $content;
}
function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	$content = get_the_content($more_link_text,$stripteaser,$more_file);
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
	$content = get_the_content($more_link_text,$stripteaser,$more_file);
	$content = convert_bbcode($content);
	$content = convert_gmcode($content);
	$content = convert_smilies($content);
	$content = convert_chars($content, 'unicode');
	$content = apply_filters('the_content_unicode', $content);
	echo $content;
}
function get_the_content($more_link_text='(more...)', $stripteaser=0, $more_file='') {
	global $id,$postdata,$more,$c,$withcomments,$page,$pages,$multipage,$numpages;
	global $HTTP_SERVER_VARS, $preview;
	global $querystring_start, $querystring_equal, $querystring_separator;
    global $pagenow;
	$output = '';
	if ($more_file != '') {
		$file=$more_file;
	} else {
		$file=$pagenow; //$HTTP_SERVER_VARS['PHP_SELF'];
	}
	$content=$pages[$page-1];
	$content=explode('<!--more-->', $content);
	if ((preg_match('/<!--noteaser-->/', $postdata['Content']) && ((!$multipage) || ($page==1))))
		$stripteaser=1;
	$teaser=$content[0];
	if (($more) && ($stripteaser))
		$teaser='';
	$output .= $teaser;
	if (count($content)>1) {
		if ($more) {
			$output .= '<a name="more'.$id.'"></a>'.$content[1];
		} else {
			$output .= ' <a href="'.$file.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'more'.$querystring_equal.'1#more'.$id.'">'.$more_link_text.'</a>';
		}
	}
	if ($preview) { // preview fix for javascript bug with foreign languages
		$output =  preg_replace('/\%u([0-9A-F]{4,4})/e',  "'&#'.base_convert('\\1',16,10).';'", $output);
	}
	return($output);
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
	$excerpt = get_the_excerpt();
	$excerpt = convert_bbcode($excerpt);
	$excerpt = convert_gmcode($excerpt);
	$excerpt = convert_chars($excerpt, 'unicode');
	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$excerpt = htmlspecialchars($excerpt);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$excerpt = make_url_footnote($excerpt);
	} elseif ($encode_html == 2) {
		$excerpt = strip_tags($excerpt);
	}
	if ($cut) {
		$blah = explode(' ', $excerpt);
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
		$excerpt = $excerpt;
	}
	echo $excerpt;
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

function get_the_excerpt() {
	global $id,$postdata;
	global $HTTP_SERVER_VARS, $preview;
	$output = '';
	$output = $postdata['Excerpt'];
    //if we haven't got an excerpt, make one in the style of the rss ones
    if ($output == '') {
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
	global $id,$page,$numpages,$multipage,$more;
	global $pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($more_file != '') {
		$file = $more_file;
	} else {
		$file = $pagenow;
	}
	if (($multipage)) { // && ($more)) {
		if ($next_or_number=='number') {
			echo $before;
			for ($i = 1; $i < ($numpages+1); $i = $i + 1) {
				$j=str_replace('%',"$i",$pagelink);
				echo " ";
				if (($i != $page) || ((!$more) && ($page==1)))
					echo '<a href="'.$file.$querystring_start.'p'.$querystring_equal.$id.
					$querystring_separator.'more'.$querystring_equal.'1'.
					$querystring_separator.'page'.$querystring_equal.$i.'">';
				echo $j;
				if (($i != $page) || ((!$more) && ($page==1)))
					echo '</a>';
			}
			echo $after;
		} else {
			if ($more) {
				echo $before;
				$i=$page-1;
				if ($i && $more)
					echo ' <a href="'.$file.$querystring_start.'p'.$querystring_equal.$id.
					$querystring_separator.'more'.$querystring_equal.'1'.
					$querystring_separator.'page'.$querystring_equal.$i.'">'.
					$previouspagelink.'</a>';
				$i=$page+1;
				if ($i<=$numpages && $more)
					echo ' <a href="'.$file.$querystring_start.'p'.$querystring_equal.$id.
					$querystring_separator.'more'.$querystring_equal.'1'.
					$querystring_separator.'page'.$querystring_equal.$i.'">'.
					$nextpagelink.'</a>';
				echo $after;
			}
		}
	}
}


function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {
	global $tableposts, $id, $postdata, $siteurl, $blogfilename, $querycount;
	global $p, $posts, $posts_per_page, $s;
	global $querystring_start, $querystring_equal, $querystring_separator;

	if(($p) || ($posts_per_page==1)) {
		
		$current_post_date = $postdata['Date'];
		$current_category = $postdata['Category'];

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

		$limitprev--;
		$sql = "SELECT ID,post_title FROM $tableposts WHERE post_date < '$current_post_date' AND post_category > 0 $sqlcat $sql_exclude_cats ORDER BY post_date DESC LIMIT $limitprev,1";

		$query = @mysql_query($sql);
		$querycount++;
		if (($query) && (mysql_num_rows($query))) {
			$p_info = mysql_fetch_object($query);
			$p_title = $p_info->post_title;
			$p_id = $p_info->ID;
			$string = '<a href="'.$blogfilename.$querystring_start.'p'.$querystring_equal.$p_id.$querystring_separator.'more'.$querystring_equal.'1'.$querystring_separator.'c'.$querystring_equal.'1">'.$previous;
			if (!($title!='yes')) {
				$string .= wptexturize(stripslashes($p_title));
			}
			$string .= '</a>';
			$format = str_replace('%',$string,$format);
			echo $format;
		}
	}
}

function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
	global $tableposts, $p, $posts, $id, $postdata, $siteurl, $blogfilename, $querycount;
	global $time_difference;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if(($p) || ($posts==1)) {
		
		$current_post_date = $postdata['Date'];
		$current_category = $postdata['Category'];

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
		$sql = "SELECT ID,post_title FROM $tableposts WHERE post_date > '$current_post_date' AND post_date < '$now' AND post_category > 0 $sqlcat $sql_exclude_cats ORDER BY post_date ASC LIMIT $limitnext,1";

		$query = @mysql_query($sql);
		$querycount++;
		if (($query) && (mysql_num_rows($query))) {
			$p_info = mysql_fetch_object($query);
			$p_title = $p_info->post_title;
			$p_id = $p_info->ID;
			$string = '<a href="'.$blogfilename.$querystring_start.'p'.$querystring_equal.$p_id.$querystring_separator.'more'.$querystring_equal.'1'.$querystring_separator.'c'.$querystring_equal.'1">'.$next;
			if ($title=='yes') {
				$string .= wptexturize(stripslashes($p_title));
			}
			$string .= '</a>';
			$format = str_replace('%',$string,$format);
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
			echo  $pagenow.$querystring_start.
				($qstr == '' ? '' : $qstr.$querystring_separator) .
				'paged'.$querystring_equal.$nextpage;
		}
	}
}

function next_posts_link($label='Next Page >>', $max_page=0) {
	global $p, $paged, $result, $request, $posts_per_page, $what_to_show;
	if ($what_to_show == 'paged') {
		if (!$max_page) {
			$nxt_request = $request;
			if ($pos = strpos(strtoupper($request), 'LIMIT')) {
				$nxt_request = substr($request, 0, $pos);
			}
			$nxt_result = mysql_query($nxt_request);
			$numposts = mysql_num_rows($nxt_result);
			$max_page = ceil($numposts / $posts_per_page);
		}
		if (!$paged) $paged = 1;
		$nextpage = intval($paged) + 1;
		if (empty($p) && (empty($paged) || $nextpage <= $max_page)) {
			echo '<a href="';
			echo next_posts($max_page);
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
		echo  $pagenow.$querystring_start.
			($qstr == '' ? '' : $qstr.$querystring_separator) .
			'paged'.$querystring_equal.$nextpage;
	}
} 

function previous_posts_link($label='<< Previous Page') {
	global $p, $paged, $what_to_show;
	if (empty($p)  && ($paged > 1) && ($what_to_show == 'paged')) {
		echo '<a href="';
		echo previous_posts();
		echo '">'.  htmlspecialchars($label) .'</a>';
	}
}

function posts_nav_link($sep=' :: ', $prelabel='<< Previous Page', $nxtlabel='Next Page >>') {
	global $p, $what_to_show, $request, $posts_per_page;
	if (empty($p) && ($what_to_show == 'paged')) {
		$nxt_request = $request;
		if ($pos = strpos(strtoupper($request), 'LIMIT')) {
			$nxt_request = substr($request, 0, $pos);
		}
		$nxt_result = mysql_query($nxt_request);
		$numposts = mysql_num_rows($nxt_result);
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

function the_category() {
	$category = get_the_category();
	$category = apply_filters('the_category', $category);
	echo convert_chars($category, 'html');
}
function the_category_rss() {
	echo convert_chars(strip_tags(get_the_category()), 'xml');
}
function the_category_unicode() {
	$category = get_the_category();
	$category = apply_filters('the_category_unicode', $category);
	echo convert_chars($category, 'unicode');
}
function get_the_category() {
	global $id,$postdata,$tablecategories,$querycount,$cache_categories,$use_cache;
	$cat_ID = $postdata['Category'];
	if ((empty($cache_categories[$cat_ID])) OR (!$use_cache)) {
		$query="SELECT cat_name FROM $tablecategories WHERE cat_ID = '$cat_ID'";
		$result=mysql_query($query);
		$querycount++;
		$myrow = mysql_fetch_array($result);
		$cat_name = $myrow[0];
		$cache_categories[$cat_ID] = $cat_name;
	} else {
		$cat_name = $cache_categories[$cat_ID];
	}
	return(stripslashes($cat_name));
}

function get_the_category_by_ID($cat_ID) {
	global $id,$tablecategories,$querycount,$cache_categories,$use_cache;
	if ((!$cache_categories[$cat_ID]) OR (!$use_cache)) {
		$query="SELECT cat_name FROM $tablecategories WHERE cat_ID = '$cat_ID'";
		$result=mysql_query($query);
		$querycount++;
		$myrow = mysql_fetch_array($result);
		$cat_name = $myrow[0];
		$cache_categories[$cat_ID] = $cat_name;
	} else {
		$cat_name = $cache_categories[$cat_ID];
	}
	return(stripslashes($cat_name));
}

function the_category_ID() {
	global $id,$postdata;	echo $postdata['Category'];
}

function the_category_head($before='',$after='') {
	global $id, $postdata, $currentcat, $previouscat,$dateformat,$newday;
	$currentcat = $postdata['Category'];
	if ($currentcat != $previouscat) {
		echo $before;
		echo get_the_category_by_ID($currentcat);
		echo $after;
		$previouscat = $currentcat;
	}
}

// out of the b2 loop
function dropdown_cats($optionall = 1, $all = 'All') {
	global $cat, $tablecategories, $querycount;
	$query="SELECT * FROM $tablecategories";
	$result=mysql_query($query);
	$querycount++;
	echo "<select name=\"cat\" class=\"postform\">\n";
	if (intval($optionall) == 1) {
		echo "\t<option value=\"all\">$all</option>\n";
	}
	while($row = mysql_fetch_object($result)) {
		echo "\t<option value=\"".$row->cat_ID."\"";
		if ($row->cat_ID == $cat)
			echo ' selected="selected"';
		echo '>'.stripslashes($row->cat_name)."</option>\n";
	}
	echo "</select>\n";
}

// out of the b2 loop
function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = 'blah', $list=true) {
	global $tablecategories, $querycount;
	global $pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	$file = ($file == 'blah') ? $pagenow : $file;
	$sort_column = 'cat_'.$sort_column;
	$query="SELECT * FROM $tablecategories WHERE cat_ID > 0 ORDER BY $sort_column $sort_order";
	$result=mysql_query($query);
	$querycount++;
	if (intval($optionall) == 1) {
		$all = apply_filters('list_cats', $all);
		if ($list) echo "\n\t<li><a href=\"".$file.$querystring_start.'cat'.$querystring_equal.'all">'.$all."</a></li>";
		else echo "\t<a href=\"".$file.$querystring_start.'cat'.$querystring_equal.'all">'.$all."</a><br />\n";
	}
	while($row = mysql_fetch_object($result)) {
		$cat_name = $row->cat_name;
		$cat_name = apply_filters('list_cats', $cat_name);
		if ($list) {
			echo "\n\t<li><a href=\"".$file.$querystring_start.'cat'.$querystring_equal.$row->cat_ID.'">';
			echo stripslashes($cat_name)."</a></li>";
		} else {
			echo "\t<a href=\"".$file.$querystring_start.'cat'.$querystring_equal.$row->cat_ID.'">';
			echo stripslashes($cat_name)."</a><br />\n";
		}
	}
}

/***** // Category tags *****/




/***** <Link> tags *****/



/***** // <Link> tags *****/




/***** Comment tags *****/

// generic comments/trackbacks/pingbacks numbering
function generic_ctp_number($post_id, $mode = 'comments') {
	global $postdata, $tablecomments, $querycount, $cache_ctp_number, $use_cache;
	if (!isset($cache_ctp_number[$post_id]) || (!$use_cache)) {
		$post_id = intval($post_id);
		$query = "SELECT * FROM $tablecomments WHERE comment_post_ID = $post_id";
		$result = mysql_query($query) or die('SQL query: '.$query.'<br />MySQL Error: '.mysql_error());
		$querycount++;
		$ctp_number = array();
		while($row = mysql_fetch_object($result)) {
			if (substr($row->comment_content, 0, 13) == '<trackback />') {
				$ctp_number['trackbacks']++;
			} elseif (substr($row->comment_content, 0, 12) == '<pingback />') {
				$ctp_number['pingbacks']++;
			} else {
				$ctp_number['comments']++;
			}
			$ctp_number['ctp']++;
		}
		$cache_ctp_number[$post_id] = $ctp_number;
	} else {
		$ctp_number = $cache_ctp_number[$post_id];
	}
	if (($mode != 'comments') && ($mode != 'trackbacks') && ($mode != 'pingbacks') && ($mode != 'ctp')) {
		$mode = 'ctp';
	}
	return $ctp_number[$mode];
}

function comments_number($zero='no comment', $one='1 comment', $more='% comments') {
	// original hack by dodo@regretless.com
	global $id,$postdata,$tablecomments,$c,$querycount,$cache_commentsnumber,$use_cache;
	$number = generic_ctp_number($id, 'comments');
	if ($number == 0) {
		$blah = $zero;
	} elseif ($number == 1) {
		$blah = $one;
	} elseif ($number  > 1) {
		$n = $number;
		$more=str_replace('%', $n, $more);
		$blah = $more;
	}
	echo $blah;
}

function comments_link($file='',$echo=true) {
	global $id,$pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($file == '')	$file = $pagenow;
	if ($file == '/')	$file = '';
	if (!$echo) return $file.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1#comments';
	else echo $file.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1#comments';
}

function comments_popup_script($width=400, $height=400, $file='b2commentspopup.php', $trackbackfile='b2trackbackpopup.php', $pingbackfile='b2pingbackspopup.php') {
	global $b2commentspopupfile, $b2trackbackpopupfile, $b2pingbackpopupfile, $b2commentsjavascript;
	$b2commentspopupfile = $file;
	$b2trackbackpopupfile = $trackbackfile;
	$b2pingbackpopupfile = $pingbackfile;
	$b2commentsjavascript = 1;
	$javascript = "<script language=\"javascript\" type=\"text/javascript\">\n<!--\nfunction b2open (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n//-->\n</script>\n";
	echo $javascript;
}

function comments_popup_link($zero='no comment', $one='1 comment', $more='% comments', $CSSclass='') {
	global $id, $b2commentspopupfile, $b2commentsjavascript;
	global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
	echo '<a href="'.$siteurl.'/';
	if ($b2commentsjavascript) {
		echo $b2commentspopupfile.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1';
		echo '" onclick="b2open(this.href); return false"';
	} else {
		// if comments_popup_script() is not in the template, display simple comment link
		comments_link();
		echo '"';
	}
	if (!empty($CSSclass)) {
		echo ' class="'.$CSSclass.'"';
	}
	echo '>';
	comments_number($zero, $one, $more);
	echo '</a>';
}

function comment_ID() {
	global $commentdata;	echo $commentdata['comment_ID'];
}

function comment_author() {
	global $commentdata;	echo stripslashes($commentdata['comment_author']);
}

function comment_author_email() {
	global $commentdata;	echo antispambot(stripslashes($commentdata['comment_author_email']));
}

function comment_author_url() {
	global $commentdata;
	$url = trim(stripslashes($commentdata['comment_author_url']));
	$url = (!stristr($url, '://')) ? 'http://'.$url : $url;
	// convert & into &amp;
	$url = preg_replace('#&([^amp\;])#is', '&amp;$1', $url);
	if ($url != 'http://url') {
		echo $url;
	}
}

function comment_author_email_link($linktext='', $before='', $after='') {
	global $commentdata;
	$email=$commentdata['comment_author_email'];
	if ((!empty($email)) && ($email != '@')) {
		$display = ($linktext != '') ? $linktext : antispambot(stripslashes($email));
		echo $before;
		echo '<a href="mailto:'.antispambot(stripslashes($email)).'">'.$display.'</a>';
		echo $after;
	}
}

function comment_author_url_link($linktext='', $before='', $after='') {
	global $commentdata;
	$url = trim(stripslashes($commentdata['comment_author_url']));
	$url = preg_replace('#&([^amp\;])#is', '&amp;$1', $url);
	$url = (!stristr($url, '://')) ? 'http://'.$url : $url;
	if ((!empty($url)) && ($url != 'http://') && ($url != 'http://url')) {
		$display = ($linktext != '') ? $linktext : stripslashes($url);
		echo $before;
		echo '<a href="'.stripslashes($url).'" target="_blank">'.$display.'</a>';
		echo $after;
	}
}

function comment_author_IP() {
	global $commentdata;	echo stripslashes($commentdata['comment_author_IP']);
}

function comment_text() {
	global $commentdata;
	$comment = stripslashes($commentdata['comment_content']);
	$comment = str_replace('<trackback />', '', $comment);
	$comment = str_replace('<pingback />', '', $comment);
	$comment = convert_chars($comment);
	$comment = convert_bbcode($comment);
	$comment = convert_gmcode($comment);
	$comment = convert_smilies($comment);
	$comment = make_clickable($comment);
	$comment = balanceTags($comment);
	$comment = apply_filters('comment_text', $comment);
	echo $comment;
}

function comment_date($d='') {
	global $commentdata,$dateformat;
	if ($d == '') {
		echo mysql2date($dateformat, $commentdata['comment_date']);
	} else {
		echo mysql2date($d, $commentdata['comment_date']);
	}
}

function comment_time($d='') {
	global $commentdata,$timeformat;
	if ($d == '') {
		echo mysql2date($timeformat, $commentdata['comment_date']);
	} else {
		echo mysql2date($d, $commentdata['comment_date']);
	}
}

/***** // Comment tags *****/



/***** TrackBack tags *****/

function trackback_url($display = 1) {
	global $siteurl, $id;
	$tb_url = $siteurl.'/b2trackback.php/'.$id;
	if ($display) {
		echo $tb_url;
	} else {
		return $tb_url;
	}
}

function trackback_number($zero='no trackback', $one='1 trackback', $more='% trackbacks') {
	global $id, $tablecomments, $tb, $querycount, $cache_trackbacknumber, $use_cache;
	$number = generic_ctp_number($id, 'trackbacks');
	if ($number == 0) {
		$blah = $zero;
	} elseif ($number == 1) {
		$blah = $one;
	} elseif ($number  > 1) {
		$n = $number;
		$more=str_replace('%', $n, $more);
		$blah = $more;
	}
	echo $blah;
}

function trackback_link($file='') {
	global $id,$pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($file == '')	$file = $pagenow;
	if ($file == '/')	$file = '';
	echo $file.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'tb'.$querystring_equal.'1#trackback';
}

function trackback_popup_link($zero='no trackback', $one='1 trackback', $more='% trackbacks', $CSSclass='') {
	global $id, $b2trackbackpopupfile, $b2commentsjavascript;
	global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
	echo '<a href="'.$siteurl.'/';
	if ($b2commentsjavascript) {
		echo $b2trackbackpopupfile.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'tb'.$querystring_equal.'1';
		echo '" onclick="b2open(this.href); return false"';
	} else {
		// if comments_popup_script() is not in the template, display simple comment link
		trackback_link();
		echo '"';
	}
	if (!empty($CSSclass)) {
		echo ' class="'.$CSSclass.'"';
	}
	echo '>';
	trackback_number($zero, $one, $more);
	echo '</a>';
}

function trackback_rdf($timezone=0) {
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
		echo '    dc:title="'.addslashes(get_the_title()).'"'."\n";
		echo '    trackback:ping="'.trackback_url(0).'"'." />\n";
		echo '</rdf:RDF>';
	}
}

/***** // TrackBack tags *****/



/***** PingBack tags *****/

function pingback_number($zero='no pingback', $one='1 pingback', $more='% pingbacks') {
	global $id, $tablecomments, $tb, $querycount, $cache_pingbacknumber, $use_cache;
	$number = generic_ctp_number($id, 'pingbacks');
	if ($number == 0) {
		$blah = $zero;
	} elseif ($number == 1) {
		$blah = $one;
	} elseif ($number  > 1) {
		$n = $number;
		$more=str_replace('%', $n, $more);
		$blah = $more;
	}
	echo $blah;
}

function pingback_link($file='') {
	global $id,$pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($file == '')	$file = $pagenow;
	if ($file == '/')	$file = '';
	echo $file.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'pb'.$querystring_equal.'1#pingbacks';
}

function pingback_popup_link($zero='no pingback', $one='1 pingback', $more='% pingbacks', $CSSclass='') {
	global $id, $b2pingbackpopupfile, $b2commentsjavascript;
	global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
	echo '<a href="'.$siteurl.'/';
	if ($b2commentsjavascript) {
		echo $b2pingbackpopupfile.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'pb'.$querystring_equal.'1';
		echo '" onclick="b2open(this.href); return false"';
	} else {
		// if comments_popup_script() is not in the template, display simple comment link
		pingback_link();
		echo '"';
	}
	if (!empty($CSSclass)) {
		echo ' class="'.$CSSclass.'"';
	}
	echo '>';
	pingback_number($zero, $one, $more);
	echo '</a>';
}



/***** // PingBack tags *****/



/***** Permalink tags *****/

function permalink_anchor($mode = 'id') {
	global $id, $postdata;
	switch(strtolower($mode)) {
		case 'title':
			$title = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $postdata['Title']);
			echo '<a name="'.$title.'"></a>';
			break;
		case 'id':
		default:
			echo '<a name="'.$id.'"></a>';
			break;
	}
}

function permalink_link($file='', $mode = 'id') {
	global $id, $postdata, $pagenow, $cacheweekly;
	global $querystring_start, $querystring_equal, $querystring_separator;
	$file = ($file=='') ? $pagenow : $file;
	switch(strtolower($mode)) {
		case 'title':
			$title = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $postdata['Title']);
			$anchor = $title;
			break;
		case 'id':
		default:
			$anchor = $id;
			break;
	}
	$archive_mode = get_settings('archive_mode');
	switch($archive_mode) {
		case 'daily':
			echo $file.$querystring_start.'m'.$querystring_equal.substr($postdata['Date'],0,4).substr($postdata['Date'],5,2).substr($postdata['Date'],8,2).'#'.$anchor;
			break;
		case 'monthly':
			echo $file.$querystring_start.'m'.$querystring_equal.substr($postdata['Date'],0,4).substr($postdata['Date'],5,2).'#'.$anchor;
			break;
		case 'weekly':
			if((!isset($cacheweekly)) || (empty($cacheweekly[$postdata['Date']]))) {
				$sql = "SELECT WEEK('".$postdata['Date']."')";
				$result = mysql_query($sql);
				$row = mysql_fetch_row($result);
				$cacheweekly[$postdata['Date']] = $row[0];
			}
			echo $file.$querystring_start.'m'.$querystring_equal.substr($postdata['Date'],0,4).$querystring_separator.'w'.$querystring_equal.$cacheweekly[$postdata['Date']].'#'.$anchor;
			break;
		case 'postbypost':
			echo $file.$querystring_start.'p'.$querystring_equal.$id;
			break;
	}
}

function permalink_single($file='') {
	global $id,$postdata,$pagenow;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if ($file=='')
		$file=$pagenow;
	echo $file.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'more'.$querystring_equal.'1'.$querystring_separator.'c'.$querystring_equal.'1';
}

function permalink_single_rss($file='b2rss.xml') {
	global $id,$postdata,$pagenow,$siteurl,$blogfilename;
	global $querystring_start, $querystring_equal, $querystring_separator;
		echo $siteurl.'/'.$blogfilename.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1';
}

/***** // Permalink tags *****/




// @@@ These aren't template tags, do not edit them

function start_b2() {
	global $row, $id, $postdata, $authordata, $day, $preview, $page, $pages, $multipage, $more, $numpages;
	global $preview_userid,$preview_date,$preview_content,$preview_title,$preview_category,$preview_notify,$preview_make_clickable,$preview_autobr;
	global $pagenow;
	global $HTTP_GET_VARS;
	if (!$preview) {
		$id = $row->ID;
		$postdata=get_postdata2($id);
	} else {
		$id = 0;
		$postdata = array (
			'ID' => 0, 
			'Author_ID' => $HTTP_GET_VARS['preview_userid'],
			'Date' => $HTTP_GET_VARS['preview_date'],
			'Content' => $HTTP_GET_VARS['preview_content'],
			'Excerpt' => $HTTP_GET_VARS['preview_excerpt'],
			'Title' => $HTTP_GET_VARS['preview_title'],
			'Category' => $HTTP_GET_VARS['preview_category'],
			'Notify' => 1,
			'Clickable' => 1,
			'Karma' => 0 // this isn't used yet
			);
		if (!empty($HTTP_GET_VARS['preview_autobr'])) {
			$postdata['Content'] = autobrize($postdata['Content']);
		}
	}
	$authordata = get_userdata($postdata['Author_ID']);
	$day = mysql2date('d.m.y',$postdata['Date']);
	$currentmonth = mysql2date('m',$postdata['Date']);
	$numpages=1;
	if (!$page)
		$page=1;
	if (isset($p))
		$more=1;
	$content = $postdata['Content'];
	if (preg_match('/<!--nextpage-->/', $postdata['Content'])) {
		if ($page > 1)
			$more=1;
		$multipage=1;
		$content=stripslashes($postdata['Content']);
		$content = str_replace("\n<!--nextpage-->\n", '<!--nextpage-->', $content);
		$content = str_replace("\n<!--nextpage-->", '<!--nextpage-->', $content);
		$content = str_replace("<!--nextpage-->\n", '<!--nextpage-->', $content);
		$pages=explode('<!--nextpage-->', $content);
		$numpages=count($pages);
	} else {
		$pages[0]=stripslashes($postdata['Content']);
		$multipage=0;
	}
	return true;
}

function is_new_day() {
	global $day, $previousday;
	if ($day != $previousday) {
		return(1);
	} else {
		return(0);
	}
}

function apply_filters($tag, $string) {
	global $b2_filter;
	if (isset($b2_filter['all'])) {
		$b2_filter['all'] = (is_string($b2_filter['all'])) ? array($b2_filter['all']) : $b2_filter['all'];
		$b2_filter[$tag] = array_merge($b2_filter['all'], $b2_filter[$tag]);
		$b2_filter[$tag] = array_unique($b2_filter[$tag]);
	}
	if (isset($b2_filter[$tag])) {
		$b2_filter[$tags] = (is_string($b2_filter[$tag])) ? array($b2_filter[$tag]) : $b2_filter[$tag];
		$functions = $b2_filter[$tag];
		foreach($functions as $function) {
			$string = $function($string);
		}
	}
	return $string;
}

function add_filter($tag, $function_to_add) {
	global $b2_filter;
	if (isset($b2_filter[$tag])) {
		$functions = $b2_filter[$tag];
		if (is_array($functions)) {
			foreach($functions as $function) {
				$new_functions[] = $function;
			}
		} elseif (is_string($functions)) {
			$new_functions[] = $functions;
		}
/* this is commented out because it just makes PHP die silently
   for no apparent reason
		if (is_array($function_to_add)) {
			foreach($function_to_add as $function) {
				if (!in_array($function, $b2_filter[$tag])) {
					$new_functions[] = $function;
				}
			}
		} else */if (is_string($function_to_add)) {
			if (!@in_array($function_to_add, $b2_filter[$tag])) {
				$new_functions[] = $function_to_add;
			}
		}
		$b2_filter[$tag] = $new_functions;
	} else {
		$b2_filter[$tag] = array($function_to_add);
	}
	return true;
}

?>
