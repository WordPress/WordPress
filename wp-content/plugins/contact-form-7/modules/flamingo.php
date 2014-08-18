<?php
/**
** Module for Flamingo plugin.
** http://wordpress.org/extend/plugins/flamingo/
**/

add_action( 'wpcf7_submit', 'wpcf7_flamingo_submit', 10, 2 );

function wpcf7_flamingo_submit( $contactform, $result ) {
	$cases = (array) apply_filters( 'wpcf7_flamingo_submit_if',
		array( 'spam', 'mail_sent', 'mail_failed' ) );

	if ( empty( $result['status'] )
	|| ! in_array( $result['status'], $cases ) ) {
		return;
	}

	if ( ! class_exists( 'Flamingo_Contact' )
	|| ! class_exists( 'Flamingo_Inbound_Message' ) ) {
		return;
	}

	$submission = WPCF7_Submission::get_instance();

	if ( ! $submission || ! $posted_data = $submission->get_posted_data() ) {
		return;
	}

	$fields_senseless = $contactform->form_scan_shortcode(
		array( 'type' => array( 'captchar', 'quiz', 'acceptance' ) ) );

	$exclude_names = array();

	foreach ( $fields_senseless as $tag ) {
		$exclude_names[] = $tag['name'];
	}

	foreach ( $posted_data as $key => $value ) {
		if ( '_' == substr( $key, 0, 1 ) || in_array( $key, $exclude_names ) ) {
			unset( $posted_data[$key] );
		}
	}

	$email = wpcf7_flamingo_get_value( 'email', $contactform );
	$name = wpcf7_flamingo_get_value( 'name', $contactform );
	$subject = wpcf7_flamingo_get_value( 'subject', $contactform );

	$meta = array();

	$special_mail_tags = array( 'remote_ip', 'user_agent', 'url',
		'date', 'time', 'post_id', 'post_name', 'post_title', 'post_url',
		'post_author', 'post_author_email' );

	foreach ( $special_mail_tags as $smt ) {
		$meta[$smt] = apply_filters( 'wpcf7_special_mail_tags',
			'', '_' . $smt, false );
	}

	$akismet = isset( $submission->akismet ) ? (array) $submission->akismet : null;

	Flamingo_Contact::add( array(
		'email' => $email,
		'name' => $name ) );

	$channel_id = wpcf7_flamingo_add_channel(
		$contactform->name(), $contactform->title() );

	if ( $channel_id ) {
		$channel = get_term( $channel_id,
			Flamingo_Inbound_Message::channel_taxonomy );

		if ( ! $channel || is_wp_error( $channel ) ) {
			$channel = 'contact-form-7';
		} else {
			$channel = $channel->slug;
		}
	} else {
		$channel = 'contact-form-7';
	}

	$args = array(
		'channel' => $channel,
		'subject' => $subject,
		'from' => trim( sprintf( '%s <%s>', $name, $email ) ),
		'from_name' => $name,
		'from_email' => $email,
		'fields' => $posted_data,
		'meta' => $meta,
		'akismet' => $akismet,
		'spam' => ( 'spam' == $result['status'] ) );

	Flamingo_Inbound_Message::add( $args );
}

function wpcf7_flamingo_get_value( $field, $contactform ) {
	if ( empty( $field ) || empty( $contactform ) ) {
		return false;
	}

	$value = '';

	if ( in_array( $field, array( 'email', 'name', 'subject' ) ) ) {
		$templates = $contactform->additional_setting( 'flamingo_' . $field );

		if ( empty( $templates[0] ) ) {
			$template = sprintf( '[your-%s]', $field );
		} else {
			$template = trim( wpcf7_strip_quote( $templates[0] ) );
		}

		$value = wpcf7_mail_replace_tags( $template );
	}

	$value = apply_filters( 'wpcf7_flamingo_get_value', $value,
		$field, $contactform );

	return $value;
}

function wpcf7_flamingo_add_channel( $slug, $name = '' ) {
	if ( ! class_exists( 'Flamingo_Inbound_Message' ) )
		return false;

	$parent = term_exists( 'contact-form-7',
		Flamingo_Inbound_Message::channel_taxonomy );

	if ( ! $parent ) {
		$parent = wp_insert_term( __( 'Contact Form 7', 'contact-form-7' ),
			Flamingo_Inbound_Message::channel_taxonomy,
			array( 'slug' => 'contact-form-7' ) );

		if ( is_wp_error( $parent ) ) {
			return false;
		}
	}

	$parent = (int) $parent['term_id'];

	if ( ! is_taxonomy_hierarchical( Flamingo_Inbound_Message::channel_taxonomy ) ) {
		// backward compat for Flamingo 1.0.4 and lower
		return $parent;
	}

	if ( empty( $name ) ) {
		$name = $slug;
	}

	$channel = term_exists( $slug,
		Flamingo_Inbound_Message::channel_taxonomy,
		$parent );

	if ( ! $channel ) {
		$channel = wp_insert_term( $name,
			Flamingo_Inbound_Message::channel_taxonomy,
			array( 'slug' => $slug, 'parent' => $parent ) );

		if ( is_wp_error( $channel ) ) {
			return false;
		}
	}

	return (int) $channel['term_id'];
}

?>