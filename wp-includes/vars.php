<?php

/* This file sets various arrays and variables for use in WordPress */
require(ABSPATH . 'wp-includes/version.php');

# Translation of invalid Unicode references range to valid range
$wp_htmltranswinuni = array(
	'&#128;' => '&#8364;', // the Euro sign
	'&#129;' => '',
	'&#130;' => '&#8218;', // these are Windows CP1252 specific characters
	'&#131;' => '&#402;',  // they would look weird on non-Windows browsers
	'&#132;' => '&#8222;',
	'&#133;' => '&#8230;',
	'&#134;' => '&#8224;',
	'&#135;' => '&#8225;',
	'&#136;' => '&#710;',
	'&#137;' => '&#8240;',
	'&#138;' => '&#352;',
	'&#139;' => '&#8249;',
	'&#140;' => '&#338;',
	'&#141;' => '',
	'&#142;' => '&#382;',
	'&#143;' => '',
	'&#144;' => '',
	'&#145;' => '&#8216;',
	'&#146;' => '&#8217;',
	'&#147;' => '&#8220;',
	'&#148;' => '&#8221;',
	'&#149;' => '&#8226;',
	'&#150;' => '&#8211;',
	'&#151;' => '&#8212;',
	'&#152;' => '&#732;',
	'&#153;' => '&#8482;',
	'&#154;' => '&#353;',
	'&#155;' => '&#8250;',
	'&#156;' => '&#339;',
	'&#157;' => '',
	'&#158;' => '',
	'&#159;' => '&#376;'
);

// On which page are we ?
$PHP_SELF = $_SERVER['PHP_SELF'];
$pagenow = explode('/', $PHP_SELF);
$pagenow = trim($pagenow[(sizeof($pagenow)-1)]);
$pagenow = explode('?', $pagenow);
$pagenow = $pagenow[0];
if (($querystring_start == '/') && ($pagenow != 'post.php')) {
	$pagenow = get_settings('siteurl') . '/' . get_settings('blogfilename');
}

// Simple browser detection
$is_lynx = 0; $is_gecko = 0; $is_winIE = 0; $is_macIE = 0; $is_opera = 0; $is_NS4 = 0;
if (!isset($HTTP_USER_AGENT)) {
	$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
}
if (preg_match('/Lynx/', $HTTP_USER_AGENT)) {
	$is_lynx = 1;
} elseif (preg_match('/Gecko/', $HTTP_USER_AGENT)) {
	$is_gecko = 1;
} elseif ((preg_match('/MSIE/', $HTTP_USER_AGENT)) && (preg_match('/Win/', $HTTP_USER_AGENT))) {
	$is_winIE = 1;
} elseif ((preg_match('/MSIE/', $HTTP_USER_AGENT)) && (preg_match('/Mac/', $HTTP_USER_AGENT))) {
	$is_macIE = 1;
} elseif (preg_match('/Opera/', $HTTP_USER_AGENT)) {
	$is_opera = 1;
} elseif ((preg_match('/Nav/', $HTTP_USER_AGENT) ) || (preg_match('/Mozilla\/4\./', $HTTP_USER_AGENT))) {
	$is_NS4 = 1;
}
$is_IE    = (($is_macIE) || ($is_winIE));

// browser-specific javascript corrections
$wp_macIE_correction['in'] = array(
	'/\%uFFD4/', '/\%uFFD5/', '/\%uFFD2/', '/\%uFFD3/',
	'/\%uFFA5/', '/\%uFFD0/', '/\%uFFD1/', '/\%uFFBD/',
	'/\%uFF83%uFFC0/', '/\%uFF83%uFFC1/', '/\%uFF83%uFFC6/', '/\%uFF83%uFFC9/',
	'/\%uFFB9/', '/\%uFF81%uFF8C/', '/\%uFF81%uFF8D/', '/\%uFF81%uFFDA/',
	'/\%uFFDB/'
);
$wp_macIE_correction['out'] = array(
	'&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;',
	'&bull;', '&ndash;', '&mdash;', '&Omega;',
	'&beta;', '&gamma;', '&theta;', '&lambda;',
	'&pi;', '&prime;', '&Prime;', '&ang;',
	'&euro;'
);
$wp_gecko_correction['in'] = array(
	'/\‘/', '/\’/', '/\“/', '/\”/',
	'/\•/', '/\–/', '/\—/', '/\Ω/',
	'/\β/', '/\γ/', '/\θ/', '/\λ/',
	'/\π/', '/\′/', '/\″/', '/\�/',
	'/\€/', '/\ /'
);
$wp_gecko_correction['out'] = array(
	'&8216;', '&rsquo;', '&ldquo;', '&rdquo;',
	'&bull;', '&ndash;', '&mdash;', '&Omega;',
	'&beta;', '&gamma;', '&theta;', '&lambda;',
	'&pi;', '&prime;', '&Prime;', '&ang;',
	'&euro;', '&#8201;'
);

// Server detection
$is_apache = strstr($_SERVER['SERVER_SOFTWARE'], 'Apache') ? 1 : 0;
$is_IIS = strstr($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') ? 1 : 0;

// if the config file does not provide the smilies array, let's define it here
if (!isset($wpsmiliestrans)) {
    $wpsmiliestrans = array(
        ' :)'        => 'icon_smile.gif',
        ' :D'        => 'icon_biggrin.gif',
        ' :-D'       => 'icon_biggrin.gif',
        ':grin:'    => 'icon_biggrin.gif',
        ' :)'        => 'icon_smile.gif',
        ' :-)'       => 'icon_smile.gif',
        ':smile:'   => 'icon_smile.gif',
        ' :('        => 'icon_sad.gif',
        ' :-('       => 'icon_sad.gif',
        ':sad:'     => 'icon_sad.gif',
        ' :o'        => 'icon_surprised.gif',
        ' :-o'       => 'icon_surprised.gif',
        ':eek:'     => 'icon_surprised.gif',
        ' 8O'        => 'icon_eek.gif',
        ' 8-O'       => 'icon_eek.gif',
        ':shock:'   => 'icon_eek.gif',
        ' :?'        => 'icon_confused.gif',
        ' :-?'       => 'icon_confused.gif',
        ' :???:'     => 'icon_confused.gif',
        ' 8)'        => 'icon_cool.gif',
        ' 8-)'       => 'icon_cool.gif',
        ':cool:'    => 'icon_cool.gif',
        ':lol:'     => 'icon_lol.gif',
        ' :x'        => 'icon_mad.gif',
        ' :-x'       => 'icon_mad.gif',
        ':mad:'     => 'icon_mad.gif',
        ' :P'        => 'icon_razz.gif',
        ' :-P'       => 'icon_razz.gif',
        ':razz:'    => 'icon_razz.gif',
        ':oops:'    => 'icon_redface.gif',
        ':cry:'     => 'icon_cry.gif',
        ':evil:'    => 'icon_evil.gif',
        ':twisted:' => 'icon_twisted.gif',
        ':roll:'    => 'icon_rolleyes.gif',
        ':wink:'    => 'icon_wink.gif',
        ' ;)'        => 'icon_wink.gif',
        ' ;-)'       => 'icon_wink.gif',
        ':!:'       => 'icon_exclaim.gif',
        ':?:'       => 'icon_question.gif',
        ':idea:'    => 'icon_idea.gif',
        ':arrow:'   => 'icon_arrow.gif',
        ' :|'        => 'icon_neutral.gif',
        ' :-|'       => 'icon_neutral.gif',
        ':neutral:' => 'icon_neutral.gif',
        ':mrgreen:' => 'icon_mrgreen.gif',
    );
}

// sorts the smilies' array
if (!function_exists('smiliescmp')) {
	function smiliescmp ($a, $b) {
	   if (strlen($a) == strlen($b)) {
		  return strcmp($a, $b);
	   }
	   return (strlen($a) > strlen($b)) ? -1 : 1;
	}
}
uksort($wpsmiliestrans, 'smiliescmp');

// generates smilies' search & replace arrays
foreach($wpsmiliestrans as $smiley => $img) {
	$wp_smiliessearch[] = $smiley;
	$smiley_masked = str_replace(' ', '', $smiley);
	$wp_smiliesreplace[] = " <img src='" . get_settings('siteurl') . "/wp-images/smilies/$img' alt='$smiley_masked' />";
}

// Path for cookies
define('COOKIEPATH', preg_replace('|http://[^/]+|i', '', get_settings('home') . '/' ) );

// Some default filters
add_filter('category_description', 'wptexturize');
add_filter('list_cats', 'wptexturize');
add_filter('comment_author', 'wptexturize');
add_filter('comment_text', 'wptexturize');
add_filter('single_post_title', 'wptexturize');
add_filter('the_title', 'wptexturize');
add_filter('the_content', 'wptexturize');
add_filter('the_excerpt', 'wptexturize');
add_action('wp_head', 'doGeoUrlHeader');
?>