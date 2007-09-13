<?php

// On which page are we ?
if ( is_admin() ) {
	// wp-admin pages are checked more carefully
	preg_match('#/wp-admin/?(.*?)$#i', $PHP_SELF, $self_matches);
	$pagenow = $self_matches[1];
	$pagenow = preg_replace('#\?.*?$#', '', $pagenow);
	if ( '' === $pagenow || 'index' === $pagenow || 'index.php' === $pagenow ) {
		$pagenow = 'index.php';
	} else {
		preg_match('#(.*?)(/|$)#', $pagenow, $self_matches);
		$pagenow = strtolower($self_matches[1]);
		if ( '.php' !== substr($pagenow, -4, 4) )
			$pagenow .= '.php'; // for Options +Multiviews: /wp-admin/themes/index.php (themes.php is queried)
	}
} else {
	if ( preg_match('#([^/]+\.php)([?/].*?)?$#i', $PHP_SELF, $self_matches) )
		$pagenow = strtolower($self_matches[1]);
	else
		$pagenow = 'index.php';
}

// Simple browser detection
$is_lynx = $is_gecko = $is_winIE = $is_macIE = $is_opera = $is_NS4 = false;

if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx') !== false) {
	$is_lynx = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false) {
	$is_gecko = true;
} elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Win') !== false) {
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

?>