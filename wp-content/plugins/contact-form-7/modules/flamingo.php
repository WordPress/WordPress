<?php
/**
** Module for Flamingo plugin.
** http://wordpress.org/extend/plugins/flamingo/
**/

add_action( 'wpcf7_submit', 'wpcf7_flamingo_submit', 10, 2 );

function wpcf7_flamingo_submit( $contact_form, $result ) {
	if (
		! class_exists( 'Flamingo_Contact' ) or
		! class_exists( 'Flamingo_Inbound_Message' )
	) {
		return;
	}

	if ( $contact_form->in_demo_mode() ) {
		return;
	}

	$cases = (array) apply_filters( 'wpcf7_flamingo_submit_if',
		array( 'spam', 'mail_sent', 'mail_failed' )
	);

	if (
		empty( $result['status'] ) or
		! in_array( $result['status'], $cases, true )
	) {
		return;
	}

	$submission = WPCF7_Submission::get_instance();

	if ( ! $submission or ! $posted_data = $submission->get_posted_data() ) {
		return;
	}

	if ( $submission->get_meta( 'do_not_store' ) ) {
		return;
	}

	// Exclude do-not-store form-tag values.
	$posted_data = array_filter(
		$posted_data,
		static function ( $name ) use ( $contact_form ) {
			return ! $contact_form->scan_form_tags( array(
				'name' => $name,
				'feature' => 'do-not-store',
			) );
		},
		ARRAY_FILTER_USE_KEY
	);

	$email = wpcf7_flamingo_get_value( 'email', $contact_form );
	$name = wpcf7_flamingo_get_value( 'name', $contact_form );
	$subject = wpcf7_flamingo_get_value( 'subject', $contact_form );

	$meta = array();

	$special_mail_tags = array( 'serial_number', 'remote_ip',
		'user_agent', 'url', 'date', 'time', 'post_id', 'post_name',
		'post_title', 'post_url', 'post_author', 'post_author_email',
		'site_title', 'site_description', 'site_url', 'site_admin_email',
		'user_login', 'user_email', 'user_display_name',
	);

	foreach ( $special_mail_tags as $smt ) {
		$tagname = sprintf( '_%s', $smt );

		$mail_tag = new WPCF7_MailTag(
			sprintf( '[%s]', $tagname ),
			$tagname,
			''
		);

		$meta[$smt] = apply_filters( 'wpcf7_special_mail_tags', null,
			$tagname, false, $mail_tag
		);
	}

	$timestamp = $submission->get_meta( 'timestamp' );

	if ( $timestamp and $datetime = date_create( '@' . $timestamp ) ) {
		$datetime->setTimezone( wp_timezone() );
		$last_contacted = $datetime->format( 'Y-m-d H:i:s' );
	} else {
		$last_contacted = '0000-00-00 00:00:00';
	}

	if ( 'mail_sent' === $result['status'] ) {
		$flamingo_contact = Flamingo_Contact::add( array(
			'email' => $email,
			'name' => $name,
			'last_contacted' => $last_contacted,
		) );
	}

	$post_meta = get_post_meta( $contact_form->id(), '_flamingo', true );

	$channel_id = isset( $post_meta['channel'] )
		? (int) $post_meta['channel']
		: wpcf7_flamingo_add_channel(
				$contact_form->name(),
				$contact_form->title()
			);

	if ( $channel_id ) {
		if (
			! isset( $post_meta['channel'] ) or
			$post_meta['channel'] !== $channel_id
		) {
			$post_meta = empty( $post_meta ) ? array() : (array) $post_meta;
			$post_meta = array_merge( $post_meta, array(
				'channel' => $channel_id,
			) );

			update_post_meta( $contact_form->id(), '_flamingo', $post_meta );
		}

		$channel = get_term( $channel_id,
			Flamingo_Inbound_Message::channel_taxonomy
		);

		if ( ! $channel or is_wp_error( $channel ) ) {
			$channel = 'contact-form-7';
		} else {
			$channel = $channel->slug;
		}
	} else {
		$channel = 'contact-form-7';
	}

	$args = array(
		'channel' => $channel,
		'status' => $submission->get_status(),
		'subject' => $subject,
		'from' => trim( sprintf( '%s <%s>', $name, $email ) ),
		'from_name' => $name,
		'from_email' => $email,
		'fields' => $posted_data,
		'meta' => $meta,
		'akismet' => $submission->pull( 'akismet' ),
		'spam' => ( 'spam' === $result['status'] ),
		'consent' => $submission->collect_consent(),
		'timestamp' => $timestamp,
		'posted_data_hash' => $submission->get_posted_data_hash(),
	);

	if ( $args['spam'] ) {
		$args['spam_log'] = $submission->get_spam_log();
	}

	$args['recaptcha'] = $submission->pull( 'recaptcha' );

	$args = apply_filters( 'wpcf7_flamingo_inbound_message_parameters', $args );

	$flamingo_inbound = Flamingo_Inbound_Message::add( $args );

	if ( empty( $flamingo_contact ) ) {
		$flamingo_contact_id = 0;
	} elseif ( method_exists( $flamingo_contact, 'id' ) ) {
		$flamingo_contact_id = $flamingo_contact->id();
	} else {
		$flamingo_contact_id = $flamingo_contact->id;
	}

	if ( empty( $flamingo_inbound ) ) {
		$flamingo_inbound_id = 0;
	} elseif ( method_exists( $flamingo_inbound, 'id' ) ) {
		$flamingo_inbound_id = $flamingo_inbound->id();
	} else {
		$flamingo_inbound_id = $flamingo_inbound->id;
	}

	$result += array(
		'flamingo_contact_id' => absint( $flamingo_contact_id ),
		'flamingo_inbound_id' => absint( $flamingo_inbound_id ),
	);

	do_action( 'wpcf7_after_flamingo', $result );
}

function wpcf7_flamingo_get_value( $field, $contact_form ) {
	if ( empty( $field ) or empty( $contact_form ) ) {
		return false;
	}

	$value = '';

	if ( in_array( $field, array( 'email', 'name', 'subject' ), true ) ) {
		$template = $contact_form->pref( 'flamingo_' . $field );

		if ( null === $template ) {
			$template = sprintf( '[your-%s]', $field );
		} else {
			$template = trim( wpcf7_strip_quote( $template ) );
		}

		$value = wpcf7_mail_replace_tags( $template );
	}

	$value = apply_filters( 'wpcf7_flamingo_get_value', $value,
		$field, $contact_form
	);

	return $value;
}

function wpcf7_flamingo_add_channel( $slug, $name = '' ) {
	if ( ! class_exists( 'Flamingo_Inbound_Message' ) ) {
		return false;
	}

	$parent = term_exists( 'contact-form-7',
		Flamingo_Inbound_Message::channel_taxonomy
	);

	if ( ! $parent ) {
		$parent = wp_insert_term( __( 'Contact Form 7', 'contact-form-7' ),
			Flamingo_Inbound_Message::channel_taxonomy,
			array( 'slug' => 'contact-form-7' )
		);

		if ( is_wp_error( $parent ) ) {
			return false;
		}
	}

	$parent = (int) $parent['term_id'];

	if (
		! is_taxonomy_hierarchical( Flamingo_Inbound_Message::channel_taxonomy )
	) {
		// backward compat for Flamingo 1.0.4 and lower
		return $parent;
	}

	if ( empty( $name ) ) {
		$name = $slug;
	}

	$channel = term_exists( $slug,
		Flamingo_Inbound_Message::channel_taxonomy,
		$parent
	);

	if ( ! $channel ) {
		$channel = wp_insert_term( $name,
			Flamingo_Inbound_Message::channel_taxonomy,
			array( 'slug' => $slug, 'parent' => $parent )
		);

		if ( is_wp_error( $channel ) ) {
			return false;
		}
	}

	return (int) $channel['term_id'];
}

add_action( 'wpcf7_after_update', 'wpcf7_flamingo_update_channel', 10, 1 );

function wpcf7_flamingo_update_channel( $contact_form ) {
	if ( ! class_exists( 'Flamingo_Inbound_Message' ) ) {
		return false;
	}

	$post_meta = get_post_meta( $contact_form->id(), '_flamingo', true );

	$channel = isset( $post_meta['channel'] )
		? get_term( $post_meta['channel'],
				Flamingo_Inbound_Message::channel_taxonomy
			)
		: get_term_by( 'slug', $contact_form->name(),
				Flamingo_Inbound_Message::channel_taxonomy
			);

	if ( ! $channel or is_wp_error( $channel ) ) {
		return;
	}

	if ( $channel->name !== wp_unslash( $contact_form->title() ) ) {
		wp_update_term( $channel->term_id,
			Flamingo_Inbound_Message::channel_taxonomy,
			array(
				'name' => $contact_form->title(),
				'slug' => $contact_form->name(),
				'parent' => $channel->parent,
			)
		);
	}
}


add_filter( 'wpcf7_special_mail_tags', 'wpcf7_flamingo_serial_number', 10, 4 );

/**
 * Returns output string of a special mail-tag.
 *
 * @param string $output The string to be output.
 * @param string $name The tag name of the special mail-tag.
 * @param bool $html Whether the mail-tag is used in an HTML content.
 * @param WPCF7_MailTag $mail_tag An object representation of the mail-tag.
 * @return string Output of the given special mail-tag.
 */
function wpcf7_flamingo_serial_number( $output, $name, $html, $mail_tag = null ) {
	if ( ! $mail_tag instanceof WPCF7_MailTag ) {
		wpcf7_doing_it_wrong(
			sprintf( '%s()', __FUNCTION__ ),
			__( 'The fourth parameter ($mail_tag) must be an instance of the WPCF7_MailTag class.', 'contact-form-7' ),
			'5.2.2'
		);
	}

	if ( '_serial_number' !== $name ) {
		return $output;
	}

	if (
		! class_exists( 'Flamingo_Inbound_Message' ) or
		! method_exists( 'Flamingo_Inbound_Message', 'count' )
	) {
		return $output;
	}

	if ( ! $contact_form = WPCF7_ContactForm::get_current() ) {
		return $output;
	}

	$serial_number = 0;

	$post_meta = get_post_meta( $contact_form->id(), '_flamingo', true );

	$channel_id = isset( $post_meta['channel'] )
		? (int) $post_meta['channel']
		: wpcf7_flamingo_add_channel(
				$contact_form->name(), $contact_form->title()
			);

	if ( $channel_id ) {
		$serial_number = 1 + Flamingo_Inbound_Message::count( array(
			'channel_id' => $channel_id,
		) );
	}

	return (string) $serial_number;
}
