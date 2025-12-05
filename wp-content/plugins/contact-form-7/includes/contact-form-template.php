<?php

class WPCF7_ContactFormTemplate {

	public static function get_default( $prop = 'form' ) {
		if ( 'form' === $prop ) {
			$template = self::form();
		} elseif ( 'mail' === $prop ) {
			$template = self::mail();
		} elseif ( 'mail_2' === $prop ) {
			$template = self::mail_2();
		} elseif ( 'messages' === $prop ) {
			$template = self::messages();
		} else {
			$template = null;
		}

		return apply_filters( 'wpcf7_default_template', $template, $prop );
	}

	public static function form() {
		$template = sprintf(
			'
<label> %2$s
    [text* your-name autocomplete:name] </label>

<label> %3$s
    [email* your-email autocomplete:email] </label>

<label> %4$s
    [text* your-subject] </label>

<label> %5$s %1$s
    [textarea your-message] </label>

[submit "%6$s"]',
			__( '(optional)', 'contact-form-7' ),
			__( 'Your name', 'contact-form-7' ),
			__( 'Your email', 'contact-form-7' ),
			__( 'Subject', 'contact-form-7' ),
			__( 'Your message', 'contact-form-7' ),
			__( 'Submit', 'contact-form-7' )
		);

		return trim( $template );
	}

	public static function mail() {
		$template = array(
			'subject' => sprintf(
				/* translators: 1: blog name, 2: [your-subject] */
				_x( '%1$s "%2$s"', 'mail subject', 'contact-form-7' ),
				'[_site_title]',
				'[your-subject]'
			),
			'sender' => sprintf(
				'%s <%s>',
				'[_site_title]',
				self::from_email()
			),
			'body' =>
				sprintf(
					/* translators: %s: [your-name] [your-email] */
					__( 'From: %s', 'contact-form-7' ),
					'[your-name] [your-email]'
				) . "\n"
				. sprintf(
					/* translators: %s: [your-subject] */
					__( 'Subject: %s', 'contact-form-7' ),
					'[your-subject]'
				) . "\n\n"
				. __( 'Message Body:', 'contact-form-7' )
				. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				. sprintf(
					/* translators: 1: blog name, 2: blog URL */
					__( 'This is a notification that a contact form was submitted on your website (%1$s %2$s).', 'contact-form-7' ),
					'[_site_title]',
					'[_site_url]'
				),
			'recipient' => '[_site_admin_email]',
			'additional_headers' => 'Reply-To: [your-email]',
			'attachments' => '',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		return $template;
	}

	public static function mail_2() {
		$template = array(
			'active' => false,
			'subject' => sprintf(
				/* translators: 1: blog name, 2: [your-subject] */
				_x( '%1$s "%2$s"', 'mail subject', 'contact-form-7' ),
				'[_site_title]',
				'[your-subject]'
			),
			'sender' => sprintf(
				'%s <%s>',
				'[_site_title]',
				self::from_email()
			),
			'body' =>
				__( 'Message Body:', 'contact-form-7' )
				. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				. sprintf(
					/* translators: 1: blog name, 2: blog URL */
					__( 'This email is a receipt for your contact form submission on our website (%1$s %2$s) in which your email address was used. If that was not you, please ignore this message.', 'contact-form-7' ),
					'[_site_title]',
					'[_site_url]'
				),
			'recipient' => '[your-email]',
			'additional_headers' => sprintf(
				'Reply-To: %s',
				'[_site_admin_email]'
			),
			'attachments' => '',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		return $template;
	}

	public static function from_email() {
		$admin_email = get_option( 'admin_email' );

		if ( wpcf7_is_localhost() ) {
			return $admin_email;
		}

		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
		$sitename = strtolower( $sitename );

		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		if ( strpbrk( $admin_email, '@' ) === '@' . $sitename ) {
			return $admin_email;
		}

		return 'wordpress@' . $sitename;
	}

	public static function messages() {
		$messages = array();

		foreach ( wpcf7_messages() as $key => $arr ) {
			$messages[$key] = $arr['default'];
		}

		return $messages;
	}
}

function wpcf7_messages() {
	$messages = array(
		'mail_sent_ok' => array(
			'description' => __( 'Sender&#8217;s message was sent successfully', 'contact-form-7' ),
			'default' => __( 'Thank you for your message. It has been sent.', 'contact-form-7' ),
		),

		'mail_sent_ng' => array(
			'description' => __( 'Sender&#8217;s message failed to send', 'contact-form-7' ),
			'default' => __( 'There was an error trying to send your message. Please try again later.', 'contact-form-7' ),
		),

		'validation_error' => array(
			'description' => __( 'Validation errors occurred', 'contact-form-7' ),
			'default' => __( 'One or more fields have an error. Please check and try again.', 'contact-form-7' ),
		),

		'spam' => array(
			'description' => __( 'Submission was referred to as spam', 'contact-form-7' ),
			'default' => __( 'There was an error trying to send your message. Please try again later.', 'contact-form-7' ),
		),

		'accept_terms' => array(
			'description' => __( 'There are terms that the sender must accept', 'contact-form-7' ),
			'default' => __( 'You must accept the terms and conditions before sending your message.', 'contact-form-7' ),
		),

		'invalid_required' => array(
			'description' => __( 'There is a field that the sender must fill in', 'contact-form-7' ),
			'default' => __( 'Please fill out this field.', 'contact-form-7' ),
		),

		'invalid_too_long' => array(
			'description' => __( 'There is a field with input that is longer than the maximum allowed length', 'contact-form-7' ),
			'default' => __( 'This field has a too long input.', 'contact-form-7' ),
		),

		'invalid_too_short' => array(
			'description' => __( 'There is a field with input that is shorter than the minimum allowed length', 'contact-form-7' ),
			'default' => __( 'This field has a too short input.', 'contact-form-7' ),
		),
	);

	return apply_filters( 'wpcf7_messages', $messages );
}
