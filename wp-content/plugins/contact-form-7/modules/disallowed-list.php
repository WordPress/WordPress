<?php

add_filter( 'wpcf7_spam', 'wpcf7_disallowed_list', 10, 2 );

function wpcf7_disallowed_list( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	$target = wpcf7_array_flatten( $submission->get_posted_data() );
	$target[] = $submission->get_meta( 'remote_ip' );
	$target[] = $submission->get_meta( 'user_agent' );
	$target = implode( "\n", $target );

	$word = wpcf7_check_disallowed_list( $target );

	$word = wpcf7_apply_filters_deprecated(
		'wpcf7_submission_is_blacklisted',
		array( $word, $submission ),
		'5.3',
		'wpcf7_submission_has_disallowed_words'
	);

	$word = apply_filters(
		'wpcf7_submission_has_disallowed_words',
		$word,
		$submission
	);

	if ( $word ) {
		if ( is_bool( $word ) ) {
			$reason = __( 'Disallowed words are used.', 'contact-form-7' );
		} else {
			$reason = sprintf(
				/* translators: %s: comma separated list of disallowed words */
				__( 'Disallowed words (%s) are used.', 'contact-form-7' ),
				implode( ', ', (array) $word )
			);
		}

		$submission->add_spam_log( array(
			'agent' => 'disallowed_list',
			'reason' => $reason,
		) );
	}

	$spam = (bool) $word;

	return $spam;
}

function wpcf7_check_disallowed_list( $target ) {
	$mod_keys = get_option( 'disallowed_keys' );

	if ( is_scalar( $mod_keys ) ) {
		$mod_keys = trim( $mod_keys );
	} else {
		$mod_keys = '';
	}

	if ( '' === $mod_keys ) {
		return false;
	}

	foreach ( explode( "\n", $mod_keys ) as $word ) {
		$word = trim( $word );
		$length = strlen( $word );

		if ( $length < 2 or 256 < $length ) {
			continue;
		}

		$pattern = sprintf( '#%s#i', preg_quote( $word, '#' ) );

		if ( preg_match( $pattern, $target ) ) {
			return $word;
		}
	}

	return false;
}

function wpcf7_blacklist_check( $target ) {
	wpcf7_deprecated_function(
		__FUNCTION__,
		'5.3',
		'wpcf7_check_disallowed_list'
	);

	return wpcf7_check_disallowed_list( $target );
}
