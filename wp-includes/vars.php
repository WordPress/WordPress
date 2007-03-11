<?php

// On which page are we ?
if ( preg_match('#([^/]+\.php)$#', $PHP_SELF, $self_matches) ) {
	$pagenow = $self_matches[1];
} elseif ( strpos($PHP_SELF, '?') !== false ) {
	$pagenow = explode('/', $PHP_SELF);
	$pagenow = trim($pagenow[(sizeof($pagenow)-1)]);
	$pagenow = explode('?', $pagenow);
	$pagenow = $pagenow[0];
} else {
	$pagenow = 'index.php';
}

// Simple browser detection
$is_lynx = $is_gecko = $is_winIE = $is_macIE = $is_opera = $is_NS4 = false;

if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx') !== false) {
	$is_lynx = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false) {
	$is_gecko = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Win')) {
	$is_winIE = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false) {
	$is_macIE = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) {
	$is_opera = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Nav') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/4.') !== false) {
	$is_NS4 = true;
}

$is_IE = ( $is_macIE || $is_winIE );

// Server detection
$is_apache = ((strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) || (strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false)) ? true : false;
$is_IIS = (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) ? true : false;

// if the config file does not provide the smilies array, let's define it here
if (!isset($wpsmiliestrans)) {
	$wpsmiliestrans = array(
	':mrgreen:' => 'icon_mrgreen.gif',
	':neutral:' => 'icon_neutral.gif',
	':twisted:' => 'icon_twisted.gif',
	  ':arrow:' => 'icon_arrow.gif',
	  ':shock:' => 'icon_eek.gif',
	  ':smile:' => 'icon_smile.gif',
	    ':???:' => 'icon_confused.gif',
	   ':cool:' => 'icon_cool.gif',
	   ':evil:' => 'icon_evil.gif',
	   ':grin:' => 'icon_biggrin.gif',
	   ':idea:' => 'icon_idea.gif',
	   ':oops:' => 'icon_redface.gif',
	   ':razz:' => 'icon_razz.gif',
	   ':roll:' => 'icon_rolleyes.gif',
	   ':wink:' => 'icon_wink.gif',
	    ':cry:' => 'icon_cry.gif',
	    ':eek:' => 'icon_surprised.gif',
	    ':lol:' => 'icon_lol.gif',
	    ':mad:' => 'icon_mad.gif',
	    ':sad:' => 'icon_sad.gif',
	      '8-)' => 'icon_cool.gif',
	      '8-O' => 'icon_eek.gif',
	      ':-(' => 'icon_sad.gif',
	      ':-)' => 'icon_smile.gif',
	      ':-?' => 'icon_confused.gif',
	      ':-D' => 'icon_biggrin.gif',
	      ':-P' => 'icon_razz.gif',
	      ':-o' => 'icon_surprised.gif',
	      ':-x' => 'icon_mad.gif',
	      ':-|' => 'icon_neutral.gif',
	      ';-)' => 'icon_wink.gif',
	       '8)' => 'icon_cool.gif',
	       '8O' => 'icon_eek.gif',
	       ':(' => 'icon_sad.gif',
	       ':)' => 'icon_smile.gif',
	       ':?' => 'icon_confused.gif',
	       ':D' => 'icon_biggrin.gif',
	       ':P' => 'icon_razz.gif',
	       ':o' => 'icon_surprised.gif',
	       ':x' => 'icon_mad.gif',
	       ':|' => 'icon_neutral.gif',
	       ';)' => 'icon_wink.gif',
	      ':!:' => 'icon_exclaim.gif',
	      ':?:' => 'icon_question.gif',
	);
}

// generates smilies' search & replace arrays
foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
	$wp_smiliessearch[] = '/(\s|^)'.preg_quote($smiley, '/').'(\s|$)/';
	$smiley_masked = htmlspecialchars(trim($smiley), ENT_QUOTES);
	$wp_smiliesreplace[] = " <img src='" . get_option('siteurl') . "/wp-includes/images/smilies/$img' alt='$smiley_masked' class='wp-smiley' /> ";
}

?>