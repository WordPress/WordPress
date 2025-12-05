<?php
/**
** Special Mail Tags
** https://contactform7.com/special-mail-tags/
**/

add_filter( 'wpcf7_special_mail_tags', 'wpcf7_special_mail_tag', 10, 4 );

/**
 * Returns output string of a special mail-tag.
 *
 * @param string $output The string to be output.
 * @param string $name The tag name of the special mail-tag.
 * @param bool $html Whether the mail-tag is used in an HTML content.
 * @param WPCF7_MailTag $mail_tag An object representation of the mail-tag.
 * @return string Output of the given special mail-tag.
 */
function wpcf7_special_mail_tag( $output, $name, $html, $mail_tag = null ) {
	if ( ! $mail_tag instanceof WPCF7_MailTag ) {
		wpcf7_doing_it_wrong(
			sprintf( '%s()', __FUNCTION__ ),
			__( 'The fourth parameter ($mail_tag) must be an instance of the WPCF7_MailTag class.', 'contact-form-7' ),
			'5.2.2'
		);
	}

	$name = preg_replace( '/^wpcf7\./', '_', $name ); // for back-compat

	$submission = WPCF7_Submission::get_instance();

	if ( ! $submission ) {
		return $output;
	}

	if ( '_remote_ip' === $name ) {
		if ( $remote_ip = $submission->get_meta( 'remote_ip' ) ) {
			return $remote_ip;
		} else {
			return '';
		}
	}

	if ( '_user_agent' === $name ) {
		if ( $user_agent = $submission->get_meta( 'user_agent' ) ) {
			return $html ? esc_html( $user_agent ) : $user_agent;
		} else {
			return '';
		}
	}

	if ( '_url' === $name ) {
		if ( $url = $submission->get_meta( 'url' ) ) {
			return $url;
		} else {
			return '';
		}
	}

	if ( '_date' === $name or '_time' === $name ) {
		if ( $timestamp = $submission->get_meta( 'timestamp' ) ) {
			if ( '_date' === $name ) {
				return wp_date( get_option( 'date_format' ), $timestamp );
			}

			if ( '_time' === $name ) {
				return wp_date( get_option( 'time_format' ), $timestamp );
			}
		}

		return '';
	}

	if ( '_invalid_fields' === $name ) {
		return (string) count( $submission->get_invalid_fields() );
	}

	if ( '_contact_form_title' === $name ) {
		$contact_form = $submission->get_contact_form();

		return $html
			? esc_html( $contact_form->title() )
			: $contact_form->title();
	}

	return $output;
}


add_filter( 'wpcf7_special_mail_tags', 'wpcf7_post_related_smt', 10, 4 );

/**
 * Returns output string of a special mail-tag.
 *
 * @param string $output The string to be output.
 * @param string $name The tag name of the special mail-tag.
 * @param bool $html Whether the mail-tag is used in an HTML content.
 * @param WPCF7_MailTag $mail_tag An object representation of the mail-tag.
 * @return string Output of the given special mail-tag.
 */
function wpcf7_post_related_smt( $output, $name, $html, $mail_tag = null ) {
	if ( ! $mail_tag instanceof WPCF7_MailTag ) {
		wpcf7_doing_it_wrong(
			sprintf( '%s()', __FUNCTION__ ),
			__( 'The fourth parameter ($mail_tag) must be an instance of the WPCF7_MailTag class.', 'contact-form-7' ),
			'5.2.2'
		);
	}

	if ( ! str_starts_with( $name, '_post_' ) ) {
		return $output;
	}

	$submission = WPCF7_Submission::get_instance();

	if ( ! $submission ) {
		return $output;
	}

	$post_id = (int) $submission->get_meta( 'container_post_id' );

	if ( ! $post_id or ! $post = get_post( $post_id ) ) {
		return '';
	}

	if ( '_post_id' === $name ) {
		return (string) $post->ID;
	}

	if ( '_post_name' === $name ) {
		return $post->post_name;
	}

	if ( '_post_title' === $name ) {
		return $html ? esc_html( $post->post_title ) : $post->post_title;
	}

	if ( '_post_url' === $name ) {
		return get_permalink( $post->ID );
	}

	$user = new WP_User( $post->post_author );

	if ( '_post_author' === $name ) {
		return $user->display_name;
	}

	if ( '_post_author_email' === $name ) {
		return $user->user_email;
	}

	return $output;
}


add_filter( 'wpcf7_special_mail_tags', 'wpcf7_site_related_smt', 10, 4 );

/**
 * Returns output string of a special mail-tag.
 *
 * @param string $output The string to be output.
 * @param string $name The tag name of the special mail-tag.
 * @param bool $html Whether the mail-tag is used in an HTML content.
 * @param WPCF7_MailTag $mail_tag An object representation of the mail-tag.
 * @return string Output of the given special mail-tag.
 */
function wpcf7_site_related_smt( $output, $name, $html, $mail_tag = null ) {
	if ( ! $mail_tag instanceof WPCF7_MailTag ) {
		wpcf7_doing_it_wrong(
			sprintf( '%s()', __FUNCTION__ ),
			__( 'The fourth parameter ($mail_tag) must be an instance of the WPCF7_MailTag class.', 'contact-form-7' ),
			'5.2.2'
		);
	}

	$filter = $html ? 'display' : 'raw';

	if ( '_site_title' === $name ) {
		$output = get_bloginfo( 'name', $filter );

		if ( ! $html ) {
			$output = wp_specialchars_decode( $output, ENT_QUOTES );
		}

		return $output;
	}

	if ( '_site_description' === $name ) {
		$output = get_bloginfo( 'description', $filter );

		if ( ! $html ) {
			$output = wp_specialchars_decode( $output, ENT_QUOTES );
		}

		return $output;
	}

	if ( '_site_url' === $name ) {
		return get_bloginfo( 'url', $filter );
	}

	if ( '_site_domain' === $name ) {
		$url = get_bloginfo( 'url', $filter );
		$host = wp_parse_url( $url, PHP_URL_HOST );

		if ( null === $host ) {
			return '';
		}

		if ( str_starts_with( $host, 'www.' ) ) {
			$host = substr( $host, 4 );
		}

		return $host;
	}

	if ( '_site_admin_email' === $name ) {
		return get_bloginfo( 'admin_email', $filter );
	}

	return $output;
}


add_filter( 'wpcf7_special_mail_tags', 'wpcf7_user_related_smt', 10, 4 );

/**
 * Returns output string of a special mail-tag.
 *
 * @param string $output The string to be output.
 * @param string $name The tag name of the special mail-tag.
 * @param bool $html Whether the mail-tag is used in an HTML content.
 * @param WPCF7_MailTag $mail_tag An object representation of the mail-tag.
 * @return string Output of the given special mail-tag.
 */
function wpcf7_user_related_smt( $output, $name, $html, $mail_tag = null ) {
	if ( ! $mail_tag instanceof WPCF7_MailTag ) {
		wpcf7_doing_it_wrong(
			sprintf( '%s()', __FUNCTION__ ),
			__( 'The fourth parameter ($mail_tag) must be an instance of the WPCF7_MailTag class.', 'contact-form-7' ),
			'5.2.2'
		);
	}

	if ( ! str_starts_with( $name, '_user_' ) or '_user_agent' === $name ) {
		return $output;
	}

	$submission = WPCF7_Submission::get_instance();

	if ( ! $submission ) {
		return $output;
	}

	$user_id = (int) $submission->get_meta( 'current_user_id' );

	if ( ! $user_id ) {
		return '';
	}

	$primary_props = array( 'user_login', 'user_email', 'user_url' );
	$opt = ltrim( $name, '_' );
	$opt = in_array( $opt, $primary_props, true ) ? $opt : substr( $opt, 5 );

	$user = new WP_User( $user_id );

	if ( $user->has_prop( $opt ) ) {
		return (string) $user->get( $opt );
	}

	return '';
}
