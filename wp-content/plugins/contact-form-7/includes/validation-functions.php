<?php

/**
 * Checks whether a string is a valid NAME token.
 *
 * ID and NAME tokens must begin with a letter ([A-Za-z])
 * and may be followed by any number of letters, digits ([0-9]),
 * hyphens ("-"), underscores ("_"), colons (":"), and periods (".").
 *
 * @link http://www.w3.org/TR/html401/types.html#h-6.2
 *
 * @return bool True if it is a valid name, false if not.
 */
function wpcf7_is_name( $text ) {
	return preg_match( '/^[A-Za-z][-A-Za-z0-9_:.]*$/', $text );
}


/**
 * Checks whether the given text is a well-formed email address.
 */
function wpcf7_is_email( $text ) {
	$result = is_email( $text );
	return apply_filters( 'wpcf7_is_email', $result, $text );
}


/**
 * Checks whether the given text is a well-formed URL.
 */
function wpcf7_is_url( $text ) {
	$scheme = wp_parse_url( $text, PHP_URL_SCHEME );
	$result = $scheme && in_array( $scheme, wp_allowed_protocols(), true );
	return apply_filters( 'wpcf7_is_url', $result, $text );
}


/**
 * Checks whether the given text is a well-formed telephone number.
 */
function wpcf7_is_tel( $text ) {
	$text = preg_replace( '/[#*].*$/', '', $text ); // Remove extension
	$text = preg_replace( '%[()/.*#\s-]+%', '', $text );

	$is_international = (
		str_starts_with( $text, '+' ) ||
		str_starts_with( $text, '00' )
	);

	if ( $is_international ) {
		$text = '+' . preg_replace( '/^[+0]+/', '', $text );
	}

	$result = true;

	if ( ! preg_match( '/^[+]?[0-9]+$/', $text ) ) {
		$result = false;
	}

	if ( ! ( 5 < strlen( $text ) and strlen( $text ) < 16 ) ) {
		$result = false;
	}

	return apply_filters( 'wpcf7_is_tel', $result, $text );
}


/**
 * Checks whether the given text is a well-formed number.
 *
 * @link https://html.spec.whatwg.org/multipage/input.html#number-state-(type=number)
 */
function wpcf7_is_number( $text ) {
	$result = false;

	$patterns = array(
		'/^[-]?[0-9]+(?:[eE][+-]?[0-9]+)?$/',
		'/^[-]?(?:[0-9]+)?[.][0-9]+(?:[eE][+-]?[0-9]+)?$/',
	);

	foreach ( $patterns as $pattern ) {
		if ( preg_match( $pattern, $text ) ) {
			$result = true;
			break;
		}
	}

	return apply_filters( 'wpcf7_is_number', $result, $text );
}


/**
 * Checks whether the given text is a valid date.
 *
 * @link https://html.spec.whatwg.org/multipage/input.html#date-state-(type=date)
 */
function wpcf7_is_date( $text ) {
	$result = preg_match(
		'/^([0-9]{4,})-([0-9]{2})-([0-9]{2})$/',
		$text,
		$matches
	);

	if ( $result ) {
		$result = checkdate( $matches[2], $matches[3], $matches[1] );
	}

	return apply_filters( 'wpcf7_is_date', $result, $text );
}


/**
 * Checks whether the given text is a valid time.
 *
 * @link https://html.spec.whatwg.org/multipage/input.html#time-state-(type=time)
 */
function wpcf7_is_time( $text ) {
	$result = preg_match(
		'/^([0-9]{2})\:([0-9]{2})(?:\:([0-9]{2}))?$/',
		$text,
		$matches
	);

	if ( $result ) {
		$hour = (int) $matches[1];
		$minute = (int) $matches[2];
		$second = empty( $matches[3] ) ? 0 : (int) $matches[3];

		$result = (
			0 <= $hour && $hour <= 23 &&
			0 <= $minute && $minute <= 59 &&
			0 <= $second && $second <= 59
		);
	}

	return apply_filters( 'wpcf7_is_time', $result, $text );
}


/**
 * Checks whether the given text is a well-formed mailbox list.
 *
 * @param string|array $mailbox_list The subject to be checked.
 *                     Comma-separated string or an array of mailboxes.
 * @return array|bool Array of email addresses if all items are well-formed
 *                    mailbox, false if not.
 */
function wpcf7_is_mailbox_list( $mailbox_list ) {
	if ( ! is_array( $mailbox_list ) ) {
		$mailbox_text = (string) $mailbox_list;

		$mailbox_text = preg_replace(
			'/\\\\(?:\"|\')/',
			'esc-quote',
			$mailbox_text
		);

		$mailbox_text = preg_replace(
			'/(?:\".*?\"|\'.*?\')/',
			'quoted-string',
			$mailbox_text
		);

		$mailbox_list = explode( ',', $mailbox_text );
	}

	$addresses = array();

	foreach ( $mailbox_list as $mailbox ) {
		if ( ! is_string( $mailbox ) ) {
			return false;
		}

		$mailbox = trim( $mailbox );

		if ( '' === $mailbox ) {
			continue;
		}

		if ( preg_match( '/<(.+)>$/', $mailbox, $matches ) ) {
			$addr_spec = $matches[1];
		} else {
			$addr_spec = $mailbox;
		}

		if ( ! wpcf7_is_email( $addr_spec ) ) {
			return false;
		}

		$addresses[] = $addr_spec;
	}

	return $addresses;
}


/**
 * Checks whether an email address belongs to a domain.
 *
 * @param string $email A mailbox or a comma-separated list of mailboxes.
 * @param string $domain Internet domain name.
 * @return bool True if all of the email addresses belong to the domain,
 *              false if not.
 */
function wpcf7_is_email_in_domain( $email, $domain ) {
	$email_list = wpcf7_is_mailbox_list( $email );

	if ( false === $email_list ) {
		return false;
	}

	$domain = strtolower( $domain );

	foreach ( $email_list as $email ) {
		$email_domain = substr( $email, strrpos( $email, '@' ) + 1 );
		$email_domain = strtolower( $email_domain );
		$domain_parts = explode( '.', $domain );

		do {
			$site_domain = implode( '.', $domain_parts );

			if ( $site_domain === $email_domain ) {
				continue 2;
			}

			array_shift( $domain_parts );
		} while ( $domain_parts );

		return false;
	}

	return true;
}


/**
 * Checks whether an email address belongs to the site domain.
 */
function wpcf7_is_email_in_site_domain( $email ) {
	if ( wpcf7_is_localhost() ) {
		return true;
	}

	$homes = array(
		home_url(),
		network_home_url(),
	);

	$homes = array_unique( $homes );

	foreach ( $homes as $home ) {
		$sitename = wp_parse_url( $home, PHP_URL_HOST );

		if ( WP_Http::is_ip_address( $sitename ) ) {
			return true;
		}

		if ( wpcf7_is_email_in_domain( $email, $sitename ) ) {
			return true;
		}
	}

	return false;
}


/**
 * Verifies that a given file path is under the directories that WordPress
 * manages for user contents.
 *
 * Returns false if the file at the given path does not exist yet.
 *
 * @param string $path A file path.
 * @return bool True if the path is under the content directories,
 *              false otherwise.
 */
function wpcf7_is_file_path_in_content_dir( $path ) {
	if ( ! is_string( $path ) or '' === $path ) {
		return false;
	}

	$callback = static function ( $path, $dir ) {
		if ( $real_path = realpath( $path ) ) {
			$path = $real_path;
		} else {
			return false;
		}

		if ( $real_dir = realpath( $dir ) ) {
			$dir = trailingslashit( $real_dir );
		} else {
			return false;
		}

		return str_starts_with(
			wp_normalize_path( $path ),
			wp_normalize_path( $dir )
		);
	};

	if (
		call_user_func( $callback, $path, WP_CONTENT_DIR )
	) {
		return true;
	}

	if (
		defined( 'UPLOADS' ) and
		call_user_func( $callback, $path, ABSPATH . UPLOADS )
	) {
		return true;
	}

	if (
		defined( 'WP_TEMP_DIR' ) and
		call_user_func( $callback, $path, WP_TEMP_DIR )
	) {
		return true;
	}

	return false;
}
