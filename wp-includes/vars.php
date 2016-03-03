<?php
/**
 * Creates common globals for the rest of WordPress
 *
 * Sets $pagenow global which is the current page. Checks
 * for the browser to set which one is currently being used.
 *
 * Detects which user environment WordPress is being used on.
 * Only attempts to check for Apache, Nginx and IIS -- three web
 * servers with known pretty permalink capability.
 *
 * Note: Though Nginx is detected, WordPress does not currently
 * generate rewrite rules for it. See https://codex.wordpress.org/Nginx
 *
 * @package WordPress
 */

global $pagenow,
	$is_lynx, $is_gecko, $is_winIE, $is_macIE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_IE, $is_edge,
	$is_apache, $is_IIS, $is_iis7, $is_nginx;

// On which page are we ?
if ( is_admin() ) {
	// wp-admin pages are checked more carefully
	if ( is_network_admin() )
		preg_match('#/wp-admin/network/?(.*?)$#i', $_SERVER['PHP_SELF'], $self_matches);
	elseif ( is_user_admin() )
		preg_match('#/wp-admin/user/?(.*?)$#i', $_SERVER['PHP_SELF'], $self_matches);
	else
		preg_match('#/wp-admin/?(.*?)$#i', $_SERVER['PHP_SELF'], $self_matches);
	$pagenow = $self_matches[1];
	$pagenow = trim($pagenow, '/');
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
	if ( preg_match('#([^/]+\.php)([?/].*?)?$#i', $_SERVER['PHP_SELF'], $self_matches) )
		$pagenow = strtolower($self_matches[1]);
	else
		$pagenow = 'index.php';
}
unset($self_matches);

// Simple browser detection
$is_lynx = $is_gecko = $is_winIE = $is_macIE = $is_opera = $is_NS4 = $is_safari = $is_chrome = $is_iphone = $is_edge = false;

if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx') !== false ) {
		$is_lynx = true;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Edge' ) !== false ) {
		$is_edge = true;
	} elseif ( stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false ) {
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'chromeframe' ) !== false ) {
			$is_admin = is_admin();
			/**
			 * Filter whether Google Chrome Frame should be used, if available.
			 *
			 * @since 3.2.0
			 *
			 * @param bool $is_admin Whether to use the Google Chrome Frame. Default is the value of is_admin().
			 */
			if ( $is_chrome = apply_filters( 'use_google_chrome_frame', $is_admin ) )
				header( 'X-UA-Compatible: chrome=1' );
			$is_winIE = ! $is_chrome;
		} else {
			$is_chrome = true;
		}
	} elseif ( stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false ) {
		$is_safari = true;
	} elseif ( ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false ) && strpos($_SERVER['HTTP_USER_AGENT'], 'Win') !== false ) {
		$is_winIE = true;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false ) {
		$is_macIE = true;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false ) {
		$is_gecko = true;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false ) {
		$is_opera = true;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Nav') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/4.') !== false ) {
		$is_NS4 = true;
	}
}

if ( $is_safari && stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') !== false )
	$is_iphone = true;

$is_IE = ( $is_macIE || $is_winIE );

// Server detection

/**
 * Whether the server software is Apache or something else
 * @global bool $is_apache
 */
$is_apache = (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false || strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);

/**
 * Whether the server software is Nginx or something else
 * @global bool $is_nginx
 */
$is_nginx = (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);

/**
 * Whether the server software is IIS or something else
 * @global bool $is_IIS
 */
$is_IIS = !$is_apache && (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false || strpos($_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer') !== false);

/**
 * Whether the server software is IIS 7.X or greater
 * @global bool $is_iis7
 */
$is_iis7 = $is_IIS && intval( substr( $_SERVER['SERVER_SOFTWARE'], strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/' ) + 14 ) ) >= 7;

/**
 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
 *
 * @staticvar bool $is_mobile
 *
 * @return bool
 */
function wp_is_mobile() {
	static $is_mobile = null;

	if ( isset( $is_mobile ) ) {
		return $is_mobile;
	}

	if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
		$is_mobile = false;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
			$is_mobile = true;
	} else {
		$is_mobile = false;
	}

	return $is_mobile;
}
