<?php
/**
 * The Akismet integration module
 *
 * @link https://akismet.com/development/api/
 */

wpcf7_include_module_file( 'akismet/service.php' );


add_action(
	'wpcf7_init',
	'wpcf7_akismet_register_service',
	30, 0
);

/**
 * Registers the Akismet service.
 */
function wpcf7_akismet_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_service( 'akismet',
		WPCF7_Akismet::get_instance()
	);
}


add_filter( 'wpcf7_spam', 'wpcf7_akismet', 10, 2 );

function wpcf7_akismet( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	if ( ! wpcf7_akismet_is_available() ) {
		return false;
	}

	if ( ! $params = wpcf7_akismet_submitted_params() ) {
		return false;
	}

	$comment = array(
		'comment_type' => 'contact-form',
		'comment_author' => $params['author'],
		'comment_author_email' => $params['author_email'],
		'comment_author_url' => $params['author_url'],
		'comment_content' => $params['content'],
		'blog' => home_url(),
		'blog_lang' => get_locale(),
		'blog_charset' => get_option( 'blog_charset' ),
		'user_ip' => $submission->get_meta( 'remote_ip' ),
		'user_agent' => $submission->get_meta( 'user_agent' ),
		'referrer' => wpcf7_superglobal_server( 'HTTP_REFERER' ),
	);

	$datetime = date_create_immutable(
		'@' . $submission->get_meta( 'timestamp' )
	);

	if ( $datetime ) {
		$comment['comment_date_gmt'] = $datetime->format( DATE_ATOM );
	}

	if ( $permalink = get_permalink() ) {
		$comment['permalink'] = $permalink;
	}

	$server_vars = array_diff_key(
		$_SERVER,
		array_flip( array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' ) )
	);

	$comment = array_merge( $comment, $server_vars );

	$comment = apply_filters( 'wpcf7_akismet_parameters', $comment );

	if ( wpcf7_akismet_comment_check( $comment ) ) {
		$spam = true;

		$submission->add_spam_log( array(
			'agent' => 'akismet',
			'reason' => __( 'Akismet returns a spam response.', 'contact-form-7' ),
		) );
	} else {
		$spam = false;
	}

	return $spam;
}


/**
 * Returns true if Akismet is active and has a valid API key.
 */
function wpcf7_akismet_is_available() {
	if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) {
		return (bool) Akismet::get_api_key();
	}

	return false;
}


/**
 * Returns an array of parameters based on the current form submission.
 * Returns false if Akismet is not active on the contact form.
 */
function wpcf7_akismet_submitted_params() {
	$akismet_tags = array_filter(
		wpcf7_scan_form_tags(),
		static function ( $tag ) {
			$akismet_option = $tag->get_option( 'akismet',
				'(author|author_email|author_url)',
				true
			);

			return (bool) $akismet_option;
		}
	);

	if ( ! $akismet_tags ) { // Akismet is not active on this contact form.
		return false;
	}

	$params = array(
		'author' => '',
		'author_email' => '',
		'author_url' => '',
		'content' => '',
	);

	foreach ( (array) $_POST as $key => $val ) {
		if ( str_starts_with( $key, '_wpcf7' ) or '_wpnonce' === $key ) {
			continue;
		}

		$vals = array_filter(
			wpcf7_array_flatten( $val ),
			static function ( $val ) {
				return '' !== trim( $val );
			}
		);

		if ( empty( $vals ) ) {
			continue;
		}

		if ( $tags = wpcf7_scan_form_tags( array( 'name' => $key ) ) ) {
			$tag = $tags[0];

			$akismet_option = $tag->get_option( 'akismet',
				'(author|author_email|author_url)',
				true
			);

			if ( 'author' === $akismet_option ) {
				$params['author'] = sprintf(
					'%s %s',
					$params['author'],
					implode( ' ', $vals )
				);

				continue;
			}

			if (
				'author_email' === $akismet_option and
				'' === $params['author_email']
			) {
				$params['author_email'] = $vals[0];
				continue;
			}

			if (
				'author_url' === $akismet_option and
				'' === $params['author_url']
			) {
				$params['author_url'] = $vals[0];
				continue;
			}

			$vals = array_filter(
				$vals,
				static function ( $val ) use ( $tag ) {
					if (
						wpcf7_form_tag_supports( $tag->type, 'selectable-values' ) and
						in_array( $val, $tag->labels, true )
					) {
						return false;
					} else {
						return true;
					}
				}
			);
		}

		if ( $vals ) {
			$params['content'] .= "\n\n" . implode( ', ', $vals );
		}
	}

	$params = array_map( 'trim', $params );

	return $params;
}


/**
 * Sends data to Akismet.
 *
 * @param array $comment Submission and environment data.
 * @return bool True if Akismet called it spam, or false otherwise.
 */
function wpcf7_akismet_comment_check( $comment ) {
	$spam = false;
	$query_string = wpcf7_build_query( $comment );

	if ( is_callable( array( 'Akismet', 'http_post' ) ) ) {
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		return $spam;
	}

	if ( 'true' === $response[1] ) {
		$spam = true;
	}

	if ( $submission = WPCF7_Submission::get_instance() ) {
		$submission->push( 'akismet', array(
			'comment' => $comment,
			'spam' => $spam,
		) );
	}

	return apply_filters( 'wpcf7_akismet_comment_check', $spam, $comment );
}


add_filter( 'wpcf7_posted_data', 'wpcf7_akismet_posted_data', 10, 1 );

/**
 * Removes Akismet-related properties from the posted data.
 *
 * This does not affect the $_POST variable itself.
 *
 * @link https://plugins.trac.wordpress.org/browser/akismet/tags/5.0/_inc/akismet-frontend.js
 */
function wpcf7_akismet_posted_data( $posted_data ) {
	if ( wpcf7_akismet_is_available() ) {
		$posted_data = array_diff_key(
			$posted_data,
			array(
				'ak_bib' => '',
				'ak_bfs' => '',
				'ak_bkpc' => '',
				'ak_bkp' => '',
				'ak_bmc' => '',
				'ak_bmcc' => '',
				'ak_bmk' => '',
				'ak_bck' => '',
				'ak_bmmc' => '',
				'ak_btmc' => '',
				'ak_bsc' => '',
				'ak_bte' => '',
				'ak_btec' => '',
				'ak_bmm' => '',
			)
		);
	}

	return $posted_data;
}


add_filter(
	'wpcf7_default_template',
	'wpcf7_akismet_default_template',
	10, 2
);

function wpcf7_akismet_default_template( $template, $prop ) {
	if ( ! wpcf7_akismet_is_available() ) {
		return $template;
	}

	if ( 'form' === $prop ) {
		$template = str_replace(
			array(
				'[text* your-name ',
				'[email* your-email ',
			),
			array(
				'[text* your-name akismet:author ',
				'[email* your-email akismet:author_email ',
			),
			$template
		);

		$privacy_notice = sprintf( '%s %s',
			__( 'This form uses Akismet to reduce spam.', 'contact-form-7' ),
			wpcf7_link(
				'https://akismet.com/privacy/',
				__( 'Learn how your data is processed.', 'contact-form-7' ),
				array(
					'target' => '_blank',
					'rel' => 'nofollow noopener',
				)
			)
		);

		$template .= "\n\n" . $privacy_notice;
	}

	return $template;
}
