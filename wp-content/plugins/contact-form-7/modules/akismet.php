<?php
/**
** Akismet Filter
** Akismet API: http://akismet.com/development/api/
**/

add_filter( 'wpcf7_spam', 'wpcf7_akismet' );

function wpcf7_akismet( $spam ) {
	if ( $spam )
		return $spam;

	if ( ! wpcf7_akismet_is_available() ) {
		return false;
	}

	if ( ! $params = wpcf7_akismet_submitted_params() )
		return false;

	$c = array();

	if ( ! empty( $params['author'] ) )
		$c['comment_author'] = $params['author'];

	if ( ! empty( $params['author_email'] ) )
		$c['comment_author_email'] = $params['author_email'];

	if ( ! empty( $params['author_url'] ) )
		$c['comment_author_url'] = $params['author_url'];

	if ( ! empty( $params['content'] ) )
		$c['comment_content'] = $params['content'];

	$c['blog'] = get_option( 'home' );
	$c['blog_lang'] = get_locale();
	$c['blog_charset'] = get_option( 'blog_charset' );
	$c['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
	$c['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$c['referrer'] = $_SERVER['HTTP_REFERER'];

	// http://blog.akismet.com/2012/06/19/pro-tip-tell-us-your-comment_type/
	$c['comment_type'] = 'contact-form';

	if ( $permalink = get_permalink() )
		$c['permalink'] = $permalink;

	$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );

	foreach ( $_SERVER as $key => $value ) {
		if ( ! in_array( $key, (array) $ignore ) )
			$c["$key"] = $value;
	}

	return wpcf7_akismet_comment_check( $c );
}

function wpcf7_akismet_is_available() {
	if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) { // Akismet v3.0+
		return (bool) Akismet::get_api_key();
	}

	if ( function_exists( 'akismet_get_key' ) ) {
		return (bool) akismet_get_key();
	}

	return false;
}

function wpcf7_akismet_submitted_params() {
	$params = array(
		'author' => '',
		'author_email' => '',
		'author_url' => '' );

	$content = '';

	$fes = wpcf7_scan_shortcode();

	foreach ( $fes as $fe ) {
		if ( ! isset( $fe['name'] ) || ! isset( $_POST[$fe['name']] ) )
			continue;

		$value = $_POST[$fe['name']];

		if ( is_array( $value ) )
			$value = implode( ', ', wpcf7_array_flatten( $value ) );

		$value = trim( $value );

		$options = (array) $fe['options'];

		if ( preg_grep( '%^akismet:author$%', $options ) ) {
			$params['author'] = trim( $params['author'] . ' ' . $value );

		} elseif ( preg_grep( '%^akismet:author_email$%', $options ) ) {
			if ( '' == $params['author_email'] )
				$params['author_email'] = $value;

		} elseif ( preg_grep( '%^akismet:author_url$%', $options ) ) {
			if ( '' == $params['author_url'] )
				$params['author_url'] = $value;
		}

		$content = trim( $content . "\n\n" . $value );
	}

	$params = array_filter( $params );

	if ( ! $params )
		return false;

	$params['content'] = $content;

	return $params;
}

function wpcf7_akismet_comment_check( $comment ) {
	global $akismet_api_host, $akismet_api_port;

	$spam = false;
	$query_string = '';

	foreach ( $comment as $key => $data ) {
		$query_string .= $key . '=' . urlencode( wp_unslash( (string) $data ) ) . '&';
	}

	if ( is_callable( array( 'Akismet', 'http_post' ) ) ) { // Akismet v3.0+
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		$response = akismet_http_post( $query_string, $akismet_api_host,
			'/1.1/comment-check', $akismet_api_port );
	}

	if ( 'true' == $response[1] ) {
		$spam = true;
	}

	if ( $submission = WPCF7_Submission::get_instance() ) {
		$submission->akismet = array( 'comment' => $comment, 'spam' => $spam );
	}

	return apply_filters( 'wpcf7_akismet_comment_check', $spam, $comment );
}

?>