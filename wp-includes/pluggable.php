<?php
/**
 * These functions can be replaced via plugins. If plugins do not redefine these
 * functions, then these will be used instead.
 *
 * @package WordPress
 */

if ( ! function_exists( 'wp_set_current_user' ) ) :
	/**
	 * Changes the current user by ID or name.
	 *
	 * Set $id to null and specify a name if you do not know a user's ID.
	 *
	 * Some WordPress functionality is based on the current user and not based on
	 * the signed in user. Therefore, it opens the ability to edit and perform
	 * actions on users who aren't signed in.
	 *
	 * @since 2.0.3
	 *
	 * @global WP_User $current_user The current user object which holds the user data.
	 *
	 * @param int|null $id   User ID.
	 * @param string   $name User's username.
	 * @return WP_User Current user User object.
	 */
	function wp_set_current_user( $id, $name = '' ) {
		global $current_user;

		// If `$id` matches the current user, there is nothing to do.
		if ( isset( $current_user )
		&& ( $current_user instanceof WP_User )
		&& ( $id == $current_user->ID )
		&& ( null !== $id )
		) {
			return $current_user;
		}

		$current_user = new WP_User( $id, $name );

		setup_userdata( $current_user->ID );

		/**
		 * Fires after the current user is set.
		 *
		 * @since 2.0.1
		 */
		do_action( 'set_current_user' );

		return $current_user;
	}
endif;

if ( ! function_exists( 'wp_get_current_user' ) ) :
	/**
	 * Retrieves the current user object.
	 *
	 * Will set the current user, if the current user is not set. The current user
	 * will be set to the logged-in person. If no user is logged-in, then it will
	 * set the current user to 0, which is invalid and won't have any permissions.
	 *
	 * @since 2.0.3
	 *
	 * @see _wp_get_current_user()
	 * @global WP_User $current_user Checks if the current user is set.
	 *
	 * @return WP_User Current WP_User instance.
	 */
	function wp_get_current_user() {
		return _wp_get_current_user();
	}
endif;

if ( ! function_exists( 'get_userdata' ) ) :
	/**
	 * Retrieves user info by user ID.
	 *
	 * @since 0.71
	 *
	 * @param int $user_id User ID
	 * @return WP_User|false WP_User object on success, false on failure.
	 */
	function get_userdata( $user_id ) {
		return get_user_by( 'id', $user_id );
	}
endif;

if ( ! function_exists( 'get_user_by' ) ) :
	/**
	 * Retrieves user info by a given field.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Added 'ID' as an alias of 'id' for the `$field` parameter.
	 *
	 * @global WP_User $current_user The current user object which holds the user data.
	 *
	 * @param string     $field The field to retrieve the user with. id | ID | slug | email | login.
	 * @param int|string $value A value for $field. A user ID, slug, email address, or login name.
	 * @return WP_User|false WP_User object on success, false on failure.
	 */
	function get_user_by( $field, $value ) {
		$userdata = WP_User::get_data_by( $field, $value );

		if ( ! $userdata ) {
			return false;
		}

		$user = new WP_User();
		$user->init( $userdata );

		return $user;
	}
endif;

if ( ! function_exists( 'cache_users' ) ) :
	/**
	 * Retrieves info for user lists to prevent multiple queries by get_userdata().
	 *
	 * @since 3.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int[] $user_ids User ID numbers list
	 */
	function cache_users( $user_ids ) {
		global $wpdb;

		update_meta_cache( 'user', $user_ids );

		$clean = _get_non_cached_ids( $user_ids, 'users' );

		if ( empty( $clean ) ) {
			return;
		}

		$list = implode( ',', $clean );

		$users = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE ID IN ($list)" );

		foreach ( $users as $user ) {
			update_user_caches( $user );
		}
	}
endif;

if ( ! function_exists( 'wp_mail' ) ) :
	/**
	 * Sends an email, similar to PHP's mail function.
	 *
	 * A true return value does not automatically mean that the user received the
	 * email successfully. It just only means that the method used was able to
	 * process the request without any errors.
	 *
	 * The default content type is `text/plain` which does not allow using HTML.
	 * However, you can set the content type of the email by using the
	 * {@see 'wp_mail_content_type'} filter.
	 *
	 * The default charset is based on the charset used on the blog. The charset can
	 * be set using the {@see 'wp_mail_charset'} filter.
	 *
	 * @since 1.2.1
	 * @since 5.5.0 is_email() is used for email validation,
	 *              instead of PHPMailer's default validator.
	 *
	 * @global PHPMailer\PHPMailer\PHPMailer $phpmailer
	 *
	 * @param string|string[] $to          Array or comma-separated list of email addresses to send message.
	 * @param string          $subject     Email subject.
	 * @param string          $message     Message contents.
	 * @param string|string[] $headers     Optional. Additional headers.
	 * @param string|string[] $attachments Optional. Paths to files to attach.
	 * @return bool Whether the email was sent successfully.
	 */
	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
		// Compact the input, apply the filters, and extract them back out.

		/**
		 * Filters the wp_mail() arguments.
		 *
		 * @since 2.2.0
		 *
		 * @param array $args {
		 *     Array of the `wp_mail()` arguments.
		 *
		 *     @type string|string[] $to          Array or comma-separated list of email addresses to send message.
		 *     @type string          $subject     Email subject.
		 *     @type string          $message     Message contents.
		 *     @type string|string[] $headers     Additional headers.
		 *     @type string|string[] $attachments Paths to files to attach.
		 * }
		 */
		$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

		/**
		 * Filters whether to preempt sending an email.
		 *
		 * Returning a non-null value will short-circuit {@see wp_mail()}, returning
		 * that value instead. A boolean return value should be used to indicate whether
		 * the email was successfully sent.
		 *
		 * @since 5.7.0
		 *
		 * @param null|bool $return Short-circuit return value.
		 * @param array     $atts {
		 *     Array of the `wp_mail()` arguments.
		 *
		 *     @type string|string[] $to          Array or comma-separated list of email addresses to send message.
		 *     @type string          $subject     Email subject.
		 *     @type string          $message     Message contents.
		 *     @type string|string[] $headers     Additional headers.
		 *     @type string|string[] $attachments Paths to files to attach.
		 * }
		 */
		$pre_wp_mail = apply_filters( 'pre_wp_mail', null, $atts );

		if ( null !== $pre_wp_mail ) {
			return $pre_wp_mail;
		}

		if ( isset( $atts['to'] ) ) {
			$to = $atts['to'];
		}

		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
		}

		if ( isset( $atts['subject'] ) ) {
			$subject = $atts['subject'];
		}

		if ( isset( $atts['message'] ) ) {
			$message = $atts['message'];
		}

		if ( isset( $atts['headers'] ) ) {
			$headers = $atts['headers'];
		}

		if ( isset( $atts['attachments'] ) ) {
			$attachments = $atts['attachments'];
		}

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}
		global $phpmailer;

		// (Re)create it, if it's gone missing.
		if ( ! ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$phpmailer = new PHPMailer\PHPMailer\PHPMailer( true );

			$phpmailer::$validator = static function ( $email ) {
				return (bool) is_email( $email );
			};
		}

		// Headers.
		$cc       = array();
		$bcc      = array();
		$reply_to = array();

		if ( empty( $headers ) ) {
			$headers = array();
		} else {
			if ( ! is_array( $headers ) ) {
				// Explode the headers out, so this function can take
				// both string headers and an array of headers.
				$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			} else {
				$tempheaders = $headers;
			}
			$headers = array();

			// If it's actually got contents.
			if ( ! empty( $tempheaders ) ) {
				// Iterate through the raw headers.
				foreach ( (array) $tempheaders as $header ) {
					if ( strpos( $header, ':' ) === false ) {
						if ( false !== stripos( $header, 'boundary=' ) ) {
							$parts    = preg_split( '/boundary=/i', trim( $header ) );
							$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
						}
						continue;
					}
					// Explode them out.
					list( $name, $content ) = explode( ':', trim( $header ), 2 );

					// Cleanup crew.
					$name    = trim( $name );
					$content = trim( $content );

					switch ( strtolower( $name ) ) {
						// Mainly for legacy -- process a "From:" header if it's there.
						case 'from':
							$bracket_pos = strpos( $content, '<' );
							if ( false !== $bracket_pos ) {
								// Text before the bracketed email is the "From" name.
								if ( $bracket_pos > 0 ) {
									$from_name = substr( $content, 0, $bracket_pos );
									$from_name = str_replace( '"', '', $from_name );
									$from_name = trim( $from_name );
								}

								$from_email = substr( $content, $bracket_pos + 1 );
								$from_email = str_replace( '>', '', $from_email );
								$from_email = trim( $from_email );

								// Avoid setting an empty $from_email.
							} elseif ( '' !== trim( $content ) ) {
								$from_email = trim( $content );
							}
							break;
						case 'content-type':
							if ( strpos( $content, ';' ) !== false ) {
								list( $type, $charset_content ) = explode( ';', $content );
								$content_type                   = trim( $type );
								if ( false !== stripos( $charset_content, 'charset=' ) ) {
									$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
								} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
									$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
									$charset  = '';
								}

								// Avoid setting an empty $content_type.
							} elseif ( '' !== trim( $content ) ) {
								$content_type = trim( $content );
							}
							break;
						case 'cc':
							$cc = array_merge( (array) $cc, explode( ',', $content ) );
							break;
						case 'bcc':
							$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
							break;
						case 'reply-to':
							$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
							break;
						default:
							// Add it to our grand headers array.
							$headers[ trim( $name ) ] = trim( $content );
							break;
					}
				}
			}
		}

		// Empty out the values that may be set.
		$phpmailer->clearAllRecipients();
		$phpmailer->clearAttachments();
		$phpmailer->clearCustomHeaders();
		$phpmailer->clearReplyTos();
		$phpmailer->Body    = '';
		$phpmailer->AltBody = '';

		// Set "From" name and email.

		// If we don't have a name from the input headers.
		if ( ! isset( $from_name ) ) {
			$from_name = 'WordPress';
		}

		/*
		 * If we don't have an email from the input headers, default to wordpress@$sitename
		 * Some hosts will block outgoing mail from this address if it doesn't exist,
		 * but there's no easy alternative. Defaulting to admin_email might appear to be
		 * another option, but some hosts may refuse to relay mail from an unknown domain.
		 * See https://core.trac.wordpress.org/ticket/5007.
		 */
		if ( ! isset( $from_email ) ) {
			// Get the site domain and get rid of www.
			$sitename   = wp_parse_url( network_home_url(), PHP_URL_HOST );
			$from_email = 'wordpress@';

			if ( null !== $sitename ) {
				if ( 'www.' === substr( $sitename, 0, 4 ) ) {
					$sitename = substr( $sitename, 4 );
				}

				$from_email .= $sitename;
			}
		}

		/**
		 * Filters the email address to send from.
		 *
		 * @since 2.2.0
		 *
		 * @param string $from_email Email address to send from.
		 */
		$from_email = apply_filters( 'wp_mail_from', $from_email );

		/**
		 * Filters the name to associate with the "from" email address.
		 *
		 * @since 2.3.0
		 *
		 * @param string $from_name Name associated with the "from" email address.
		 */
		$from_name = apply_filters( 'wp_mail_from_name', $from_name );

		try {
			$phpmailer->setFrom( $from_email, $from_name, false );
		} catch ( PHPMailer\PHPMailer\Exception $e ) {
			$mail_error_data                             = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
			$mail_error_data['phpmailer_exception_code'] = $e->getCode();

			/** This filter is documented in wp-includes/pluggable.php */
			do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );

			return false;
		}

		// Set mail's subject and body.
		$phpmailer->Subject = $subject;
		$phpmailer->Body    = $message;

		// Set destination addresses, using appropriate methods for handling addresses.
		$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

		foreach ( $address_headers as $address_header => $addresses ) {
			if ( empty( $addresses ) ) {
				continue;
			}

			foreach ( (array) $addresses as $address ) {
				try {
					// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
					$recipient_name = '';

					if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
						if ( count( $matches ) == 3 ) {
							$recipient_name = $matches[1];
							$address        = $matches[2];
						}
					}

					switch ( $address_header ) {
						case 'to':
							$phpmailer->addAddress( $address, $recipient_name );
							break;
						case 'cc':
							$phpmailer->addCc( $address, $recipient_name );
							break;
						case 'bcc':
							$phpmailer->addBcc( $address, $recipient_name );
							break;
						case 'reply_to':
							$phpmailer->addReplyTo( $address, $recipient_name );
							break;
					}
				} catch ( PHPMailer\PHPMailer\Exception $e ) {
					continue;
				}
			}
		}

		// Set to use PHP's mail().
		$phpmailer->isMail();

		// Set Content-Type and charset.

		// If we don't have a content-type from the input headers.
		if ( ! isset( $content_type ) ) {
			$content_type = 'text/plain';
		}

		/**
		 * Filters the wp_mail() content type.
		 *
		 * @since 2.3.0
		 *
		 * @param string $content_type Default wp_mail() content type.
		 */
		$content_type = apply_filters( 'wp_mail_content_type', $content_type );

		$phpmailer->ContentType = $content_type;

		// Set whether it's plaintext, depending on $content_type.
		if ( 'text/html' === $content_type ) {
			$phpmailer->isHTML( true );
		}

		// If we don't have a charset from the input headers.
		if ( ! isset( $charset ) ) {
			$charset = get_bloginfo( 'charset' );
		}

		/**
		 * Filters the default wp_mail() charset.
		 *
		 * @since 2.3.0
		 *
		 * @param string $charset Default email charset.
		 */
		$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

		// Set custom headers.
		if ( ! empty( $headers ) ) {
			foreach ( (array) $headers as $name => $content ) {
				// Only add custom headers not added automatically by PHPMailer.
				if ( ! in_array( $name, array( 'MIME-Version', 'X-Mailer' ), true ) ) {
					try {
						$phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
					} catch ( PHPMailer\PHPMailer\Exception $e ) {
						continue;
					}
				}
			}

			if ( false !== stripos( $content_type, 'multipart' ) && ! empty( $boundary ) ) {
				$phpmailer->addCustomHeader( sprintf( 'Content-Type: %s; boundary="%s"', $content_type, $boundary ) );
			}
		}

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $filename => $attachment ) {
				$filename = is_string( $filename ) ? $filename : '';

				try {
					$phpmailer->addAttachment( $attachment, $filename );
				} catch ( PHPMailer\PHPMailer\Exception $e ) {
					continue;
				}
			}
		}

		/**
		 * Fires after PHPMailer is initialized.
		 *
		 * @since 2.2.0
		 *
		 * @param PHPMailer $phpmailer The PHPMailer instance (passed by reference).
		 */
		do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );

		$mail_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );

		// Send!
		try {
			$send = $phpmailer->send();

			/**
			 * Fires after PHPMailer has successfully sent an email.
			 *
			 * The firing of this action does not necessarily mean that the recipient(s) received the
			 * email successfully. It only means that the `send` method above was able to
			 * process the request without any errors.
			 *
			 * @since 5.9.0
			 *
			 * @param array $mail_data {
			 *     An array containing the email recipient(s), subject, message, headers, and attachments.
			 *
			 *     @type string[] $to          Email addresses to send message.
			 *     @type string   $subject     Email subject.
			 *     @type string   $message     Message contents.
			 *     @type string[] $headers     Additional headers.
			 *     @type string[] $attachments Paths to files to attach.
			 * }
			 */
			do_action( 'wp_mail_succeeded', $mail_data );

			return $send;
		} catch ( PHPMailer\PHPMailer\Exception $e ) {
			$mail_data['phpmailer_exception_code'] = $e->getCode();

			/**
			 * Fires after a PHPMailer\PHPMailer\Exception is caught.
			 *
			 * @since 4.4.0
			 *
			 * @param WP_Error $error A WP_Error object with the PHPMailer\PHPMailer\Exception message, and an array
			 *                        containing the mail recipient, subject, message, headers, and attachments.
			 */
			do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_data ) );

			return false;
		}
	}
endif;

if ( ! function_exists( 'wp_authenticate' ) ) :
	/**
	 * Authenticates a user, confirming the login credentials are valid.
	 *
	 * @since 2.5.0
	 * @since 4.5.0 `$username` now accepts an email address.
	 *
	 * @param string $username User's username or email address.
	 * @param string $password User's password.
	 * @return WP_User|WP_Error WP_User object if the credentials are valid,
	 *                          otherwise WP_Error.
	 */
	function wp_authenticate( $username, $password ) {
		$username = sanitize_user( $username );
		$password = trim( $password );

		/**
		 * Filters whether a set of user login credentials are valid.
		 *
		 * A WP_User object is returned if the credentials authenticate a user.
		 * WP_Error or null otherwise.
		 *
		 * @since 2.8.0
		 * @since 4.5.0 `$username` now accepts an email address.
		 *
		 * @param null|WP_User|WP_Error $user     WP_User if the user is authenticated.
		 *                                        WP_Error or null otherwise.
		 * @param string                $username Username or email address.
		 * @param string                $password User password.
		 */
		$user = apply_filters( 'authenticate', null, $username, $password );

		if ( null == $user ) {
			// TODO: What should the error message be? (Or would these even happen?)
			// Only needed if all authentication handlers fail to return anything.
			$user = new WP_Error( 'authentication_failed', __( '<strong>Error:</strong> Invalid username, email address or incorrect password.' ) );
		}

		$ignore_codes = array( 'empty_username', 'empty_password' );

		if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes, true ) ) {
			$error = $user;

			/**
			 * Fires after a user login has failed.
			 *
			 * @since 2.5.0
			 * @since 4.5.0 The value of `$username` can now be an email address.
			 * @since 5.4.0 The `$error` parameter was added.
			 *
			 * @param string   $username Username or email address.
			 * @param WP_Error $error    A WP_Error object with the authentication failure details.
			 */
			do_action( 'wp_login_failed', $username, $error );
		}

		return $user;
	}
endif;

if ( ! function_exists( 'wp_logout' ) ) :
	/**
	 * Logs the current user out.
	 *
	 * @since 2.5.0
	 */
	function wp_logout() {
		$user_id = get_current_user_id();

		wp_destroy_current_session();
		wp_clear_auth_cookie();
		wp_set_current_user( 0 );

		/**
		 * Fires after a user is logged out.
		 *
		 * @since 1.5.0
		 * @since 5.5.0 Added the `$user_id` parameter.
		 *
		 * @param int $user_id ID of the user that was logged out.
		 */
		do_action( 'wp_logout', $user_id );
	}
endif;

if ( ! function_exists( 'wp_validate_auth_cookie' ) ) :
	/**
	 * Validates authentication cookie.
	 *
	 * The checks include making sure that the authentication cookie is set and
	 * pulling in the contents (if $cookie is not used).
	 *
	 * Makes sure the cookie is not expired. Verifies the hash in cookie is what is
	 * should be and compares the two.
	 *
	 * @since 2.5.0
	 *
	 * @global int $login_grace_period
	 *
	 * @param string $cookie Optional. If used, will validate contents instead of cookie's.
	 * @param string $scheme Optional. The cookie scheme to use: 'auth', 'secure_auth', or 'logged_in'.
	 * @return int|false User ID if valid cookie, false if invalid.
	 */
	function wp_validate_auth_cookie( $cookie = '', $scheme = '' ) {
		$cookie_elements = wp_parse_auth_cookie( $cookie, $scheme );
		if ( ! $cookie_elements ) {
			/**
			 * Fires if an authentication cookie is malformed.
			 *
			 * @since 2.7.0
			 *
			 * @param string $cookie Malformed auth cookie.
			 * @param string $scheme Authentication scheme. Values include 'auth', 'secure_auth',
			 *                       or 'logged_in'.
			 */
			do_action( 'auth_cookie_malformed', $cookie, $scheme );
			return false;
		}

		$scheme     = $cookie_elements['scheme'];
		$username   = $cookie_elements['username'];
		$hmac       = $cookie_elements['hmac'];
		$token      = $cookie_elements['token'];
		$expired    = $cookie_elements['expiration'];
		$expiration = $cookie_elements['expiration'];

		// Allow a grace period for POST and Ajax requests.
		if ( wp_doing_ajax() || 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$expired += HOUR_IN_SECONDS;
		}

		// Quick check to see if an honest cookie has expired.
		if ( $expired < time() ) {
			/**
			 * Fires once an authentication cookie has expired.
			 *
			 * @since 2.7.0
			 *
			 * @param string[] $cookie_elements {
			 *     Authentication cookie components. None of the components should be assumed
			 *     to be valid as they come directly from a client-provided cookie value.
			 *
			 *     @type string $username   User's username.
			 *     @type string $expiration The time the cookie expires as a UNIX timestamp.
			 *     @type string $token      User's session token used.
			 *     @type string $hmac       The security hash for the cookie.
			 *     @type string $scheme     The cookie scheme to use.
			 * }
			 */
			do_action( 'auth_cookie_expired', $cookie_elements );
			return false;
		}

		$user = get_user_by( 'login', $username );
		if ( ! $user ) {
			/**
			 * Fires if a bad username is entered in the user authentication process.
			 *
			 * @since 2.7.0
			 *
			 * @param string[] $cookie_elements {
			 *     Authentication cookie components. None of the components should be assumed
			 *     to be valid as they come directly from a client-provided cookie value.
			 *
			 *     @type string $username   User's username.
			 *     @type string $expiration The time the cookie expires as a UNIX timestamp.
			 *     @type string $token      User's session token used.
			 *     @type string $hmac       The security hash for the cookie.
			 *     @type string $scheme     The cookie scheme to use.
			 * }
			 */
			do_action( 'auth_cookie_bad_username', $cookie_elements );
			return false;
		}

		$pass_frag = substr( $user->user_pass, 8, 4 );

		$key = wp_hash( $username . '|' . $pass_frag . '|' . $expiration . '|' . $token, $scheme );

		// If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
		$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
		$hash = hash_hmac( $algo, $username . '|' . $expiration . '|' . $token, $key );

		if ( ! hash_equals( $hash, $hmac ) ) {
			/**
			 * Fires if a bad authentication cookie hash is encountered.
			 *
			 * @since 2.7.0
			 *
			 * @param string[] $cookie_elements {
			 *     Authentication cookie components. None of the components should be assumed
			 *     to be valid as they come directly from a client-provided cookie value.
			 *
			 *     @type string $username   User's username.
			 *     @type string $expiration The time the cookie expires as a UNIX timestamp.
			 *     @type string $token      User's session token used.
			 *     @type string $hmac       The security hash for the cookie.
			 *     @type string $scheme     The cookie scheme to use.
			 * }
			 */
			do_action( 'auth_cookie_bad_hash', $cookie_elements );
			return false;
		}

		$manager = WP_Session_Tokens::get_instance( $user->ID );
		if ( ! $manager->verify( $token ) ) {
			/**
			 * Fires if a bad session token is encountered.
			 *
			 * @since 4.0.0
			 *
			 * @param string[] $cookie_elements {
			 *     Authentication cookie components. None of the components should be assumed
			 *     to be valid as they come directly from a client-provided cookie value.
			 *
			 *     @type string $username   User's username.
			 *     @type string $expiration The time the cookie expires as a UNIX timestamp.
			 *     @type string $token      User's session token used.
			 *     @type string $hmac       The security hash for the cookie.
			 *     @type string $scheme     The cookie scheme to use.
			 * }
			 */
			do_action( 'auth_cookie_bad_session_token', $cookie_elements );
			return false;
		}

		// Ajax/POST grace period set above.
		if ( $expiration < time() ) {
			$GLOBALS['login_grace_period'] = 1;
		}

		/**
		 * Fires once an authentication cookie has been validated.
		 *
		 * @since 2.7.0
		 *
		 * @param string[] $cookie_elements {
		 *     Authentication cookie components.
		 *
		 *     @type string $username   User's username.
		 *     @type string $expiration The time the cookie expires as a UNIX timestamp.
		 *     @type string $token      User's session token used.
		 *     @type string $hmac       The security hash for the cookie.
		 *     @type string $scheme     The cookie scheme to use.
		 * }
		 * @param WP_User  $user            User object.
		 */
		do_action( 'auth_cookie_valid', $cookie_elements, $user );

		return $user->ID;
	}
endif;

if ( ! function_exists( 'wp_generate_auth_cookie' ) ) :
	/**
	 * Generates authentication cookie contents.
	 *
	 * @since 2.5.0
	 * @since 4.0.0 The `$token` parameter was added.
	 *
	 * @param int    $user_id    User ID.
	 * @param int    $expiration The time the cookie expires as a UNIX timestamp.
	 * @param string $scheme     Optional. The cookie scheme to use: 'auth', 'secure_auth', or 'logged_in'.
	 *                           Default 'auth'.
	 * @param string $token      User's session token to use for this cookie.
	 * @return string Authentication cookie contents. Empty string if user does not exist.
	 */
	function wp_generate_auth_cookie( $user_id, $expiration, $scheme = 'auth', $token = '' ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return '';
		}

		if ( ! $token ) {
			$manager = WP_Session_Tokens::get_instance( $user_id );
			$token   = $manager->create( $expiration );
		}

		$pass_frag = substr( $user->user_pass, 8, 4 );

		$key = wp_hash( $user->user_login . '|' . $pass_frag . '|' . $expiration . '|' . $token, $scheme );

		// If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
		$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
		$hash = hash_hmac( $algo, $user->user_login . '|' . $expiration . '|' . $token, $key );

		$cookie = $user->user_login . '|' . $expiration . '|' . $token . '|' . $hash;

		/**
		 * Filters the authentication cookie.
		 *
		 * @since 2.5.0
		 * @since 4.0.0 The `$token` parameter was added.
		 *
		 * @param string $cookie     Authentication cookie.
		 * @param int    $user_id    User ID.
		 * @param int    $expiration The time the cookie expires as a UNIX timestamp.
		 * @param string $scheme     Cookie scheme used. Accepts 'auth', 'secure_auth', or 'logged_in'.
		 * @param string $token      User's session token used.
		 */
		return apply_filters( 'auth_cookie', $cookie, $user_id, $expiration, $scheme, $token );
	}
endif;

if ( ! function_exists( 'wp_parse_auth_cookie' ) ) :
	/**
	 * Parses a cookie into its components.
	 *
	 * @since 2.7.0
	 * @since 4.0.0 The `$token` element was added to the return value.
	 *
	 * @param string $cookie Authentication cookie.
	 * @param string $scheme Optional. The cookie scheme to use: 'auth', 'secure_auth', or 'logged_in'.
	 * @return string[]|false {
	 *     Authentication cookie components. None of the components should be assumed
	 *     to be valid as they come directly from a client-provided cookie value. If
	 *     the cookie value is malformed, false is returned.
	 *
	 *     @type string $username   User's username.
	 *     @type string $expiration The time the cookie expires as a UNIX timestamp.
	 *     @type string $token      User's session token used.
	 *     @type string $hmac       The security hash for the cookie.
	 *     @type string $scheme     The cookie scheme to use.
	 * }
	 */
	function wp_parse_auth_cookie( $cookie = '', $scheme = '' ) {
		if ( empty( $cookie ) ) {
			switch ( $scheme ) {
				case 'auth':
					$cookie_name = AUTH_COOKIE;
					break;
				case 'secure_auth':
					$cookie_name = SECURE_AUTH_COOKIE;
					break;
				case 'logged_in':
					$cookie_name = LOGGED_IN_COOKIE;
					break;
				default:
					if ( is_ssl() ) {
						$cookie_name = SECURE_AUTH_COOKIE;
						$scheme      = 'secure_auth';
					} else {
						$cookie_name = AUTH_COOKIE;
						$scheme      = 'auth';
					}
			}

			if ( empty( $_COOKIE[ $cookie_name ] ) ) {
				return false;
			}
			$cookie = $_COOKIE[ $cookie_name ];
		}

		$cookie_elements = explode( '|', $cookie );
		if ( count( $cookie_elements ) !== 4 ) {
			return false;
		}

		list( $username, $expiration, $token, $hmac ) = $cookie_elements;

		return compact( 'username', 'expiration', 'token', 'hmac', 'scheme' );
	}
endif;

if ( ! function_exists( 'wp_set_auth_cookie' ) ) :
	/**
	 * Sets the authentication cookies based on user ID.
	 *
	 * The $remember parameter increases the time that the cookie will be kept. The
	 * default the cookie is kept without remembering is two days. When $remember is
	 * set, the cookies will be kept for 14 days or two weeks.
	 *
	 * @since 2.5.0
	 * @since 4.3.0 Added the `$token` parameter.
	 *
	 * @param int         $user_id  User ID.
	 * @param bool        $remember Whether to remember the user.
	 * @param bool|string $secure   Whether the auth cookie should only be sent over HTTPS. Default is an empty
	 *                              string which means the value of `is_ssl()` will be used.
	 * @param string      $token    Optional. User's session token to use for this cookie.
	 */
	function wp_set_auth_cookie( $user_id, $remember = false, $secure = '', $token = '' ) {
		if ( $remember ) {
			/**
			 * Filters the duration of the authentication cookie expiration period.
			 *
			 * @since 2.8.0
			 *
			 * @param int  $length   Duration of the expiration period in seconds.
			 * @param int  $user_id  User ID.
			 * @param bool $remember Whether to remember the user login. Default false.
			 */
			$expiration = time() + apply_filters( 'auth_cookie_expiration', 14 * DAY_IN_SECONDS, $user_id, $remember );

			/*
			 * Ensure the browser will continue to send the cookie after the expiration time is reached.
			 * Needed for the login grace period in wp_validate_auth_cookie().
			 */
			$expire = $expiration + ( 12 * HOUR_IN_SECONDS );
		} else {
			/** This filter is documented in wp-includes/pluggable.php */
			$expiration = time() + apply_filters( 'auth_cookie_expiration', 2 * DAY_IN_SECONDS, $user_id, $remember );
			$expire     = 0;
		}

		if ( '' === $secure ) {
			$secure = is_ssl();
		}

		// Front-end cookie is secure when the auth cookie is secure and the site's home URL uses HTTPS.
		$secure_logged_in_cookie = $secure && 'https' === parse_url( get_option( 'home' ), PHP_URL_SCHEME );

		/**
		 * Filters whether the auth cookie should only be sent over HTTPS.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $secure  Whether the cookie should only be sent over HTTPS.
		 * @param int  $user_id User ID.
		 */
		$secure = apply_filters( 'secure_auth_cookie', $secure, $user_id );

		/**
		 * Filters whether the logged in cookie should only be sent over HTTPS.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $secure_logged_in_cookie Whether the logged in cookie should only be sent over HTTPS.
		 * @param int  $user_id                 User ID.
		 * @param bool $secure                  Whether the auth cookie should only be sent over HTTPS.
		 */
		$secure_logged_in_cookie = apply_filters( 'secure_logged_in_cookie', $secure_logged_in_cookie, $user_id, $secure );

		if ( $secure ) {
			$auth_cookie_name = SECURE_AUTH_COOKIE;
			$scheme           = 'secure_auth';
		} else {
			$auth_cookie_name = AUTH_COOKIE;
			$scheme           = 'auth';
		}

		if ( '' === $token ) {
			$manager = WP_Session_Tokens::get_instance( $user_id );
			$token   = $manager->create( $expiration );
		}

		$auth_cookie      = wp_generate_auth_cookie( $user_id, $expiration, $scheme, $token );
		$logged_in_cookie = wp_generate_auth_cookie( $user_id, $expiration, 'logged_in', $token );

		/**
		 * Fires immediately before the authentication cookie is set.
		 *
		 * @since 2.5.0
		 * @since 4.9.0 The `$token` parameter was added.
		 *
		 * @param string $auth_cookie Authentication cookie value.
		 * @param int    $expire      The time the login grace period expires as a UNIX timestamp.
		 *                            Default is 12 hours past the cookie's expiration time.
		 * @param int    $expiration  The time when the authentication cookie expires as a UNIX timestamp.
		 *                            Default is 14 days from now.
		 * @param int    $user_id     User ID.
		 * @param string $scheme      Authentication scheme. Values include 'auth' or 'secure_auth'.
		 * @param string $token       User's session token to use for this cookie.
		 */
		do_action( 'set_auth_cookie', $auth_cookie, $expire, $expiration, $user_id, $scheme, $token );

		/**
		 * Fires immediately before the logged-in authentication cookie is set.
		 *
		 * @since 2.6.0
		 * @since 4.9.0 The `$token` parameter was added.
		 *
		 * @param string $logged_in_cookie The logged-in cookie value.
		 * @param int    $expire           The time the login grace period expires as a UNIX timestamp.
		 *                                 Default is 12 hours past the cookie's expiration time.
		 * @param int    $expiration       The time when the logged-in authentication cookie expires as a UNIX timestamp.
		 *                                 Default is 14 days from now.
		 * @param int    $user_id          User ID.
		 * @param string $scheme           Authentication scheme. Default 'logged_in'.
		 * @param string $token            User's session token to use for this cookie.
		 */
		do_action( 'set_logged_in_cookie', $logged_in_cookie, $expire, $expiration, $user_id, 'logged_in', $token );

		/**
		 * Allows preventing auth cookies from actually being sent to the client.
		 *
		 * @since 4.7.4
		 *
		 * @param bool $send Whether to send auth cookies to the client.
		 */
		if ( ! apply_filters( 'send_auth_cookies', true ) ) {
			return;
		}

		setcookie( $auth_cookie_name, $auth_cookie, $expire, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN, $secure, true );
		setcookie( $auth_cookie_name, $auth_cookie, $expire, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true );
		setcookie( LOGGED_IN_COOKIE, $logged_in_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure_logged_in_cookie, true );
		if ( COOKIEPATH != SITECOOKIEPATH ) {
			setcookie( LOGGED_IN_COOKIE, $logged_in_cookie, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure_logged_in_cookie, true );
		}
	}
endif;

if ( ! function_exists( 'wp_clear_auth_cookie' ) ) :
	/**
	 * Removes all of the cookies associated with authentication.
	 *
	 * @since 2.5.0
	 */
	function wp_clear_auth_cookie() {
		/**
		 * Fires just before the authentication cookies are cleared.
		 *
		 * @since 2.7.0
		 */
		do_action( 'clear_auth_cookie' );

		/** This filter is documented in wp-includes/pluggable.php */
		if ( ! apply_filters( 'send_auth_cookies', true ) ) {
			return;
		}

		// Auth cookies.
		setcookie( AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, ADMIN_COOKIE_PATH, COOKIE_DOMAIN );
		setcookie( SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, ADMIN_COOKIE_PATH, COOKIE_DOMAIN );
		setcookie( AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN );
		setcookie( SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN );
		setcookie( LOGGED_IN_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( LOGGED_IN_COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );

		// Settings cookies.
		setcookie( 'wp-settings-' . get_current_user_id(), ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH );
		setcookie( 'wp-settings-time-' . get_current_user_id(), ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH );

		// Old cookies.
		setcookie( AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );
		setcookie( SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );

		// Even older cookies.
		setcookie( USER_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( PASS_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( USER_COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );
		setcookie( PASS_COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );

		// Post password cookie.
		setcookie( 'wp-postpass_' . COOKIEHASH, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}
endif;

if ( ! function_exists( 'is_user_logged_in' ) ) :
	/**
	 * Determines whether the current visitor is a logged in user.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if user is logged in, false if not logged in.
	 */
	function is_user_logged_in() {
		$user = wp_get_current_user();

		return $user->exists();
	}
endif;

if ( ! function_exists( 'auth_redirect' ) ) :
	/**
	 * Checks if a user is logged in, if not it redirects them to the login page.
	 *
	 * When this code is called from a page, it checks to see if the user viewing the page is logged in.
	 * If the user is not logged in, they are redirected to the login page. The user is redirected
	 * in such a way that, upon logging in, they will be sent directly to the page they were originally
	 * trying to access.
	 *
	 * @since 1.5.0
	 */
	function auth_redirect() {
		$secure = ( is_ssl() || force_ssl_admin() );

		/**
		 * Filters whether to use a secure authentication redirect.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $secure Whether to use a secure authentication redirect. Default false.
		 */
		$secure = apply_filters( 'secure_auth_redirect', $secure );

		// If https is required and request is http, redirect.
		if ( $secure && ! is_ssl() && false !== strpos( $_SERVER['REQUEST_URI'], 'wp-admin' ) ) {
			if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
				wp_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
				exit;
			} else {
				wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
				exit;
			}
		}

		/**
		 * Filters the authentication redirect scheme.
		 *
		 * @since 2.9.0
		 *
		 * @param string $scheme Authentication redirect scheme. Default empty.
		 */
		$scheme = apply_filters( 'auth_redirect_scheme', '' );

		$user_id = wp_validate_auth_cookie( '', $scheme );
		if ( $user_id ) {
			/**
			 * Fires before the authentication redirect.
			 *
			 * @since 2.8.0
			 *
			 * @param int $user_id User ID.
			 */
			do_action( 'auth_redirect', $user_id );

			// If the user wants ssl but the session is not ssl, redirect.
			if ( ! $secure && get_user_option( 'use_ssl', $user_id ) && false !== strpos( $_SERVER['REQUEST_URI'], 'wp-admin' ) ) {
				if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
					wp_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
					exit;
				} else {
					wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
					exit;
				}
			}

			return; // The cookie is good, so we're done.
		}

		// The cookie is no good, so force login.
		nocache_headers();

		$redirect = ( strpos( $_SERVER['REQUEST_URI'], '/options.php' ) && wp_get_referer() ) ? wp_get_referer() : set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$login_url = wp_login_url( $redirect, true );

		wp_redirect( $login_url );
		exit;
	}
endif;

if ( ! function_exists( 'check_admin_referer' ) ) :
	/**
	 * Ensures intent by verifying that a user was referred from another admin page with the correct security nonce.
	 *
	 * This function ensures the user intends to perform a given action, which helps protect against clickjacking style
	 * attacks. It verifies intent, not authorization, therefore it does not verify the user's capabilities. This should
	 * be performed with `current_user_can()` or similar.
	 *
	 * If the nonce value is invalid, the function will exit with an "Are You Sure?" style message.
	 *
	 * @since 1.2.0
	 * @since 2.5.0 The `$query_arg` parameter was added.
	 *
	 * @param int|string $action    The nonce action.
	 * @param string     $query_arg Optional. Key to check for nonce in `$_REQUEST`. Default '_wpnonce'.
	 * @return int|false 1 if the nonce is valid and generated between 0-12 hours ago,
	 *                   2 if the nonce is valid and generated between 12-24 hours ago.
	 *                   False if the nonce is invalid.
	 */
	function check_admin_referer( $action = -1, $query_arg = '_wpnonce' ) {
		if ( -1 === $action ) {
			_doing_it_wrong( __FUNCTION__, __( 'You should specify an action to be verified by using the first parameter.' ), '3.2.0' );
		}

		$adminurl = strtolower( admin_url() );
		$referer  = strtolower( wp_get_referer() );
		$result   = isset( $_REQUEST[ $query_arg ] ) ? wp_verify_nonce( $_REQUEST[ $query_arg ], $action ) : false;

		/**
		 * Fires once the admin request has been validated or not.
		 *
		 * @since 1.5.1
		 *
		 * @param string    $action The nonce action.
		 * @param false|int $result False if the nonce is invalid, 1 if the nonce is valid and generated between
		 *                          0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
		 */
		do_action( 'check_admin_referer', $action, $result );

		if ( ! $result && ! ( -1 === $action && strpos( $referer, $adminurl ) === 0 ) ) {
			wp_nonce_ays( $action );
			die();
		}

		return $result;
	}
endif;

if ( ! function_exists( 'check_ajax_referer' ) ) :
	/**
	 * Verifies the Ajax request to prevent processing requests external of the blog.
	 *
	 * @since 2.0.3
	 *
	 * @param int|string   $action    Action nonce.
	 * @param false|string $query_arg Optional. Key to check for the nonce in `$_REQUEST` (since 2.5). If false,
	 *                                `$_REQUEST` values will be evaluated for '_ajax_nonce', and '_wpnonce'
	 *                                (in that order). Default false.
	 * @param bool         $stop      Optional. Whether to stop early when the nonce cannot be verified.
	 *                                Default true.
	 * @return int|false 1 if the nonce is valid and generated between 0-12 hours ago,
	 *                   2 if the nonce is valid and generated between 12-24 hours ago.
	 *                   False if the nonce is invalid.
	 */
	function check_ajax_referer( $action = -1, $query_arg = false, $stop = true ) {
		if ( -1 == $action ) {
			_doing_it_wrong( __FUNCTION__, __( 'You should specify an action to be verified by using the first parameter.' ), '4.7.0' );
		}

		$nonce = '';

		if ( $query_arg && isset( $_REQUEST[ $query_arg ] ) ) {
			$nonce = $_REQUEST[ $query_arg ];
		} elseif ( isset( $_REQUEST['_ajax_nonce'] ) ) {
			$nonce = $_REQUEST['_ajax_nonce'];
		} elseif ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
		}

		$result = wp_verify_nonce( $nonce, $action );

		/**
		 * Fires once the Ajax request has been validated or not.
		 *
		 * @since 2.1.0
		 *
		 * @param string    $action The Ajax nonce action.
		 * @param false|int $result False if the nonce is invalid, 1 if the nonce is valid and generated between
		 *                          0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
		 */
		do_action( 'check_ajax_referer', $action, $result );

		if ( $stop && false === $result ) {
			if ( wp_doing_ajax() ) {
				wp_die( -1, 403 );
			} else {
				die( '-1' );
			}
		}

		return $result;
	}
endif;

if ( ! function_exists( 'wp_redirect' ) ) :
	/**
	 * Redirects to another page.
	 *
	 * Note: wp_redirect() does not exit automatically, and should almost always be
	 * followed by a call to `exit;`:
	 *
	 *     wp_redirect( $url );
	 *     exit;
	 *
	 * Exiting can also be selectively manipulated by using wp_redirect() as a conditional
	 * in conjunction with the {@see 'wp_redirect'} and {@see 'wp_redirect_status'} filters:
	 *
	 *     if ( wp_redirect( $url ) ) {
	 *         exit;
	 *     }
	 *
	 * @since 1.5.1
	 * @since 5.1.0 The `$x_redirect_by` parameter was added.
	 * @since 5.4.0 On invalid status codes, wp_die() is called.
	 *
	 * @global bool $is_IIS
	 *
	 * @param string $location      The path or URL to redirect to.
	 * @param int    $status        Optional. HTTP response status code to use. Default '302' (Moved Temporarily).
	 * @param string $x_redirect_by Optional. The application doing the redirect. Default 'WordPress'.
	 * @return bool False if the redirect was canceled, true otherwise.
	 */
	function wp_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) {
		global $is_IIS;

		/**
		 * Filters the redirect location.
		 *
		 * @since 2.1.0
		 *
		 * @param string $location The path or URL to redirect to.
		 * @param int    $status   The HTTP response status code to use.
		 */
		$location = apply_filters( 'wp_redirect', $location, $status );

		/**
		 * Filters the redirect HTTP response status code to use.
		 *
		 * @since 2.3.0
		 *
		 * @param int    $status   The HTTP response status code to use.
		 * @param string $location The path or URL to redirect to.
		 */
		$status = apply_filters( 'wp_redirect_status', $status, $location );

		if ( ! $location ) {
			return false;
		}

		if ( $status < 300 || 399 < $status ) {
			wp_die( __( 'HTTP redirect status code must be a redirection code, 3xx.' ) );
		}

		$location = wp_sanitize_redirect( $location );

		if ( ! $is_IIS && 'cgi-fcgi' !== PHP_SAPI ) {
			status_header( $status ); // This causes problems on IIS and some FastCGI setups.
		}

		/**
		 * Filters the X-Redirect-By header.
		 *
		 * Allows applications to identify themselves when they're doing a redirect.
		 *
		 * @since 5.1.0
		 *
		 * @param string $x_redirect_by The application doing the redirect.
		 * @param int    $status        Status code to use.
		 * @param string $location      The path to redirect to.
		 */
		$x_redirect_by = apply_filters( 'x_redirect_by', $x_redirect_by, $status, $location );
		if ( is_string( $x_redirect_by ) ) {
			header( "X-Redirect-By: $x_redirect_by" );
		}

		header( "Location: $location", true, $status );

		return true;
	}
endif;

if ( ! function_exists( 'wp_sanitize_redirect' ) ) :
	/**
	 * Sanitizes a URL for use in a redirect.
	 *
	 * @since 2.3.0
	 *
	 * @param string $location The path to redirect to.
	 * @return string Redirect-sanitized URL.
	 */
	function wp_sanitize_redirect( $location ) {
		// Encode spaces.
		$location = str_replace( ' ', '%20', $location );

		$regex    = '/
		(
			(?: [\xC2-\xDF][\x80-\xBF]        # double-byte sequences   110xxxxx 10xxxxxx
			|   \xE0[\xA0-\xBF][\x80-\xBF]    # triple-byte sequences   1110xxxx 10xxxxxx * 2
			|   [\xE1-\xEC][\x80-\xBF]{2}
			|   \xED[\x80-\x9F][\x80-\xBF]
			|   [\xEE-\xEF][\x80-\xBF]{2}
			|   \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
			|   [\xF1-\xF3][\x80-\xBF]{3}
			|   \xF4[\x80-\x8F][\x80-\xBF]{2}
		){1,40}                              # ...one or more times
		)/x';
		$location = preg_replace_callback( $regex, '_wp_sanitize_utf8_in_redirect', $location );
		$location = preg_replace( '|[^a-z0-9-~+_.?#=&;,/:%!*\[\]()@]|i', '', $location );
		$location = wp_kses_no_null( $location );

		// Remove %0D and %0A from location.
		$strip = array( '%0d', '%0a', '%0D', '%0A' );
		return _deep_replace( $strip, $location );
	}

	/**
	 * URL encodes UTF-8 characters in a URL.
	 *
	 * @ignore
	 * @since 4.2.0
	 * @access private
	 *
	 * @see wp_sanitize_redirect()
	 *
	 * @param array $matches RegEx matches against the redirect location.
	 * @return string URL-encoded version of the first RegEx match.
	 */
	function _wp_sanitize_utf8_in_redirect( $matches ) {
		return urlencode( $matches[0] );
	}
endif;

if ( ! function_exists( 'wp_safe_redirect' ) ) :
	/**
	 * Performs a safe (local) redirect, using wp_redirect().
	 *
	 * Checks whether the $location is using an allowed host, if it has an absolute
	 * path. A plugin can therefore set or remove allowed host(s) to or from the
	 * list.
	 *
	 * If the host is not allowed, then the redirect defaults to wp-admin on the siteurl
	 * instead. This prevents malicious redirects which redirect to another host,
	 * but only used in a few places.
	 *
	 * Note: wp_safe_redirect() does not exit automatically, and should almost always be
	 * followed by a call to `exit;`:
	 *
	 *     wp_safe_redirect( $url );
	 *     exit;
	 *
	 * Exiting can also be selectively manipulated by using wp_safe_redirect() as a conditional
	 * in conjunction with the {@see 'wp_redirect'} and {@see 'wp_redirect_status'} filters:
	 *
	 *     if ( wp_safe_redirect( $url ) ) {
	 *         exit;
	 *     }
	 *
	 * @since 2.3.0
	 * @since 5.1.0 The return value from wp_redirect() is now passed on, and the `$x_redirect_by` parameter was added.
	 *
	 * @param string $location      The path or URL to redirect to.
	 * @param int    $status        Optional. HTTP response status code to use. Default '302' (Moved Temporarily).
	 * @param string $x_redirect_by Optional. The application doing the redirect. Default 'WordPress'.
	 * @return bool False if the redirect was canceled, true otherwise.
	 */
	function wp_safe_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) {

		// Need to look at the URL the way it will end up in wp_redirect().
		$location = wp_sanitize_redirect( $location );

		/**
		 * Filters the redirect fallback URL for when the provided redirect is not safe (local).
		 *
		 * @since 4.3.0
		 *
		 * @param string $fallback_url The fallback URL to use by default.
		 * @param int    $status       The HTTP response status code to use.
		 */
		$fallback_url = apply_filters( 'wp_safe_redirect_fallback', admin_url(), $status );

		$location = wp_validate_redirect( $location, $fallback_url );

		return wp_redirect( $location, $status, $x_redirect_by );
	}
endif;

if ( ! function_exists( 'wp_validate_redirect' ) ) :
	/**
	 * Validates a URL for use in a redirect.
	 *
	 * Checks whether the $location is using an allowed host, if it has an absolute
	 * path. A plugin can therefore set or remove allowed host(s) to or from the
	 * list.
	 *
	 * If the host is not allowed, then the redirect is to $fallback_url supplied.
	 *
	 * @since 2.8.1
	 *
	 * @param string $location     The redirect to validate.
	 * @param string $fallback_url The value to return if $location is not allowed.
	 * @return string Redirect-sanitized URL.
	 */
	function wp_validate_redirect( $location, $fallback_url = '' ) {
		$location = wp_sanitize_redirect( trim( $location, " \t\n\r\0\x08\x0B" ) );
		// Browsers will assume 'http' is your protocol, and will obey a redirect to a URL starting with '//'.
		if ( '//' === substr( $location, 0, 2 ) ) {
			$location = 'http:' . $location;
		}

		// In PHP 5 parse_url() may fail if the URL query part contains 'http://'.
		// See https://bugs.php.net/bug.php?id=38143
		$cut  = strpos( $location, '?' );
		$test = $cut ? substr( $location, 0, $cut ) : $location;

		$lp = parse_url( $test );

		// Give up if malformed URL.
		if ( false === $lp ) {
			return $fallback_url;
		}

		// Allow only 'http' and 'https' schemes. No 'data:', etc.
		if ( isset( $lp['scheme'] ) && ! ( 'http' === $lp['scheme'] || 'https' === $lp['scheme'] ) ) {
			return $fallback_url;
		}

		if ( ! isset( $lp['host'] ) && ! empty( $lp['path'] ) && '/' !== $lp['path'][0] ) {
			$path = '';
			if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
				$path = dirname( parse_url( 'http://placeholder' . $_SERVER['REQUEST_URI'], PHP_URL_PATH ) . '?' );
				$path = wp_normalize_path( $path );
			}
			$location = '/' . ltrim( $path . '/', '/' ) . $location;
		}

		// Reject if certain components are set but host is not.
		// This catches URLs like https:host.com for which parse_url() does not set the host field.
		if ( ! isset( $lp['host'] ) && ( isset( $lp['scheme'] ) || isset( $lp['user'] ) || isset( $lp['pass'] ) || isset( $lp['port'] ) ) ) {
			return $fallback_url;
		}

		// Reject malformed components parse_url() can return on odd inputs.
		foreach ( array( 'user', 'pass', 'host' ) as $component ) {
			if ( isset( $lp[ $component ] ) && strpbrk( $lp[ $component ], ':/?#@' ) ) {
				return $fallback_url;
			}
		}

		$wpp = parse_url( home_url() );

		/**
		 * Filters the list of allowed hosts to redirect to.
		 *
		 * @since 2.3.0
		 *
		 * @param string[] $hosts An array of allowed host names.
		 * @param string   $host  The host name of the redirect destination; empty string if not set.
		 */
		$allowed_hosts = (array) apply_filters( 'allowed_redirect_hosts', array( $wpp['host'] ), isset( $lp['host'] ) ? $lp['host'] : '' );

		if ( isset( $lp['host'] ) && ( ! in_array( $lp['host'], $allowed_hosts, true ) && strtolower( $wpp['host'] ) !== $lp['host'] ) ) {
			$location = $fallback_url;
		}

		return $location;
	}
endif;

if ( ! function_exists( 'wp_notify_postauthor' ) ) :
	/**
	 * Notifies an author (and/or others) of a comment/trackback/pingback on a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
	 * @param string         $deprecated Not used.
	 * @return bool True on completion. False if no email addresses were specified.
	 */
	function wp_notify_postauthor( $comment_id, $deprecated = null ) {
		if ( null !== $deprecated ) {
			_deprecated_argument( __FUNCTION__, '3.8.0' );
		}

		$comment = get_comment( $comment_id );
		if ( empty( $comment ) || empty( $comment->comment_post_ID ) ) {
			return false;
		}

		$post   = get_post( $comment->comment_post_ID );
		$author = get_userdata( $post->post_author );

		// Who to notify? By default, just the post author, but others can be added.
		$emails = array();
		if ( $author ) {
			$emails[] = $author->user_email;
		}

		/**
		 * Filters the list of email addresses to receive a comment notification.
		 *
		 * By default, only post authors are notified of comments. This filter allows
		 * others to be added.
		 *
		 * @since 3.7.0
		 *
		 * @param string[] $emails     An array of email addresses to receive a comment notification.
		 * @param string   $comment_id The comment ID as a numeric string.
		 */
		$emails = apply_filters( 'comment_notification_recipients', $emails, $comment->comment_ID );
		$emails = array_filter( $emails );

		// If there are no addresses to send the comment to, bail.
		if ( ! count( $emails ) ) {
			return false;
		}

		// Facilitate unsetting below without knowing the keys.
		$emails = array_flip( $emails );

		/**
		 * Filters whether to notify comment authors of their comments on their own posts.
		 *
		 * By default, comment authors aren't notified of their comments on their own
		 * posts. This filter allows you to override that.
		 *
		 * @since 3.8.0
		 *
		 * @param bool   $notify     Whether to notify the post author of their own comment.
		 *                           Default false.
		 * @param string $comment_id The comment ID as a numeric string.
		 */
		$notify_author = apply_filters( 'comment_notification_notify_author', false, $comment->comment_ID );

		// The comment was left by the author.
		if ( $author && ! $notify_author && $comment->user_id == $post->post_author ) {
			unset( $emails[ $author->user_email ] );
		}

		// The author moderated a comment on their own post.
		if ( $author && ! $notify_author && get_current_user_id() == $post->post_author ) {
			unset( $emails[ $author->user_email ] );
		}

		// The post author is no longer a member of the blog.
		if ( $author && ! $notify_author && ! user_can( $post->post_author, 'read_post', $post->ID ) ) {
			unset( $emails[ $author->user_email ] );
		}

		// If there's no email to send the comment to, bail, otherwise flip array back around for use below.
		if ( ! count( $emails ) ) {
			return false;
		} else {
			$emails = array_flip( $emails );
		}

		$switched_locale = switch_to_locale( get_locale() );

		$comment_author_domain = '';
		if ( WP_Http::is_ip_address( $comment->comment_author_IP ) ) {
			$comment_author_domain = gethostbyaddr( $comment->comment_author_IP );
		}

		// The blogname option is escaped with esc_html() on the way into the database in sanitize_option().
		// We want to reverse this for the plain text arena of emails.
		$blogname        = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$comment_content = wp_specialchars_decode( $comment->comment_content );

		switch ( $comment->comment_type ) {
			case 'trackback':
				/* translators: %s: Post title. */
				$notify_message = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "\r\n";
				/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
				$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				/* translators: %s: Trackback/pingback/comment author URL. */
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				/* translators: %s: Comment text. */
				$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
				$notify_message .= __( 'You can see all trackbacks on this post here:' ) . "\r\n";
				/* translators: Trackback notification email subject. 1: Site title, 2: Post title. */
				$subject = sprintf( __( '[%1$s] Trackback: "%2$s"' ), $blogname, $post->post_title );
				break;

			case 'pingback':
				/* translators: %s: Post title. */
				$notify_message = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "\r\n";
				/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
				$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				/* translators: %s: Trackback/pingback/comment author URL. */
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				/* translators: %s: Comment text. */
				$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
				$notify_message .= __( 'You can see all pingbacks on this post here:' ) . "\r\n";
				/* translators: Pingback notification email subject. 1: Site title, 2: Post title. */
				$subject = sprintf( __( '[%1$s] Pingback: "%2$s"' ), $blogname, $post->post_title );
				break;

			default: // Comments.
				/* translators: %s: Post title. */
				$notify_message = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "\r\n";
				/* translators: 1: Comment author's name, 2: Comment author's IP address, 3: Comment author's hostname. */
				$notify_message .= sprintf( __( 'Author: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				/* translators: %s: Comment author email. */
				$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "\r\n";
				/* translators: %s: Trackback/pingback/comment author URL. */
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";

				if ( $comment->comment_parent && user_can( $post->post_author, 'edit_comment', $comment->comment_parent ) ) {
					/* translators: Comment moderation. %s: Parent comment edit URL. */
					$notify_message .= sprintf( __( 'In reply to: %s' ), admin_url( "comment.php?action=editcomment&c={$comment->comment_parent}#wpbody-content" ) ) . "\r\n";
				}

				/* translators: %s: Comment text. */
				$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
				$notify_message .= __( 'You can see all comments on this post here:' ) . "\r\n";
				/* translators: Comment notification email subject. 1: Site title, 2: Post title. */
				$subject = sprintf( __( '[%1$s] Comment: "%2$s"' ), $blogname, $post->post_title );
				break;
		}

		$notify_message .= get_permalink( $comment->comment_post_ID ) . "#comments\r\n\r\n";
		/* translators: %s: Comment URL. */
		$notify_message .= sprintf( __( 'Permalink: %s' ), get_comment_link( $comment ) ) . "\r\n";

		if ( user_can( $post->post_author, 'edit_comment', $comment->comment_ID ) ) {
			if ( EMPTY_TRASH_DAYS ) {
				/* translators: Comment moderation. %s: Comment action URL. */
				$notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment->comment_ID}#wpbody-content" ) ) . "\r\n";
			} else {
				/* translators: Comment moderation. %s: Comment action URL. */
				$notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment->comment_ID}#wpbody-content" ) ) . "\r\n";
			}
			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment->comment_ID}#wpbody-content" ) ) . "\r\n";
		}

		$wp_email = 'wordpress@' . preg_replace( '#^www\.#', '', wp_parse_url( network_home_url(), PHP_URL_HOST ) );

		if ( '' === $comment->comment_author ) {
			$from = "From: \"$blogname\" <$wp_email>";
			if ( '' !== $comment->comment_author_email ) {
				$reply_to = "Reply-To: $comment->comment_author_email";
			}
		} else {
			$from = "From: \"$comment->comment_author\" <$wp_email>";
			if ( '' !== $comment->comment_author_email ) {
				$reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
			}
		}

		$message_headers = "$from\n"
		. 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . "\"\n";

		if ( isset( $reply_to ) ) {
			$message_headers .= $reply_to . "\n";
		}

		/**
		 * Filters the comment notification email text.
		 *
		 * @since 1.5.2
		 *
		 * @param string $notify_message The comment notification email text.
		 * @param string $comment_id     Comment ID as a numeric string.
		 */
		$notify_message = apply_filters( 'comment_notification_text', $notify_message, $comment->comment_ID );

		/**
		 * Filters the comment notification email subject.
		 *
		 * @since 1.5.2
		 *
		 * @param string $subject    The comment notification email subject.
		 * @param string $comment_id Comment ID as a numeric string.
		 */
		$subject = apply_filters( 'comment_notification_subject', $subject, $comment->comment_ID );

		/**
		 * Filters the comment notification email headers.
		 *
		 * @since 1.5.2
		 *
		 * @param string $message_headers Headers for the comment notification email.
		 * @param string $comment_id      Comment ID as a numeric string.
		 */
		$message_headers = apply_filters( 'comment_notification_headers', $message_headers, $comment->comment_ID );

		foreach ( $emails as $email ) {
			wp_mail( $email, wp_specialchars_decode( $subject ), $notify_message, $message_headers );
		}

		if ( $switched_locale ) {
			restore_previous_locale();
		}

		return true;
	}
endif;

if ( ! function_exists( 'wp_notify_moderator' ) ) :
	/**
	 * Notifies the moderator of the site about a new comment that is awaiting approval.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * Uses the {@see 'notify_moderator'} filter to determine whether the site moderator
	 * should be notified, overriding the site setting.
	 *
	 * @param int $comment_id Comment ID.
	 * @return true Always returns true.
	 */
	function wp_notify_moderator( $comment_id ) {
		global $wpdb;

		$maybe_notify = get_option( 'moderation_notify' );

		/**
		 * Filters whether to send the site moderator email notifications, overriding the site setting.
		 *
		 * @since 4.4.0
		 *
		 * @param bool $maybe_notify Whether to notify blog moderator.
		 * @param int  $comment_ID   The id of the comment for the notification.
		 */
		$maybe_notify = apply_filters( 'notify_moderator', $maybe_notify, $comment_id );

		if ( ! $maybe_notify ) {
			return true;
		}

		$comment = get_comment( $comment_id );
		$post    = get_post( $comment->comment_post_ID );
		$user    = get_userdata( $post->post_author );
		// Send to the administration and to the post author if the author can modify the comment.
		$emails = array( get_option( 'admin_email' ) );
		if ( $user && user_can( $user->ID, 'edit_comment', $comment_id ) && ! empty( $user->user_email ) ) {
			if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) ) {
				$emails[] = $user->user_email;
			}
		}

		$switched_locale = switch_to_locale( get_locale() );

		$comment_author_domain = '';
		if ( WP_Http::is_ip_address( $comment->comment_author_IP ) ) {
			$comment_author_domain = gethostbyaddr( $comment->comment_author_IP );
		}

		$comments_waiting = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'" );

		// The blogname option is escaped with esc_html() on the way into the database in sanitize_option().
		// We want to reverse this for the plain text arena of emails.
		$blogname        = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$comment_content = wp_specialchars_decode( $comment->comment_content );

		switch ( $comment->comment_type ) {
			case 'trackback':
				/* translators: %s: Post title. */
				$notify_message  = sprintf( __( 'A new trackback on the post "%s" is waiting for your approval' ), $post->post_title ) . "\r\n";
				$notify_message .= get_permalink( $comment->comment_post_ID ) . "\r\n\r\n";
				/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
				$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				/* translators: %s: Trackback/pingback/comment author URL. */
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				$notify_message .= __( 'Trackback excerpt: ' ) . "\r\n" . $comment_content . "\r\n\r\n";
				break;

			case 'pingback':
				/* translators: %s: Post title. */
				$notify_message  = sprintf( __( 'A new pingback on the post "%s" is waiting for your approval' ), $post->post_title ) . "\r\n";
				$notify_message .= get_permalink( $comment->comment_post_ID ) . "\r\n\r\n";
				/* translators: 1: Trackback/pingback website name, 2: Website IP address, 3: Website hostname. */
				$notify_message .= sprintf( __( 'Website: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				/* translators: %s: Trackback/pingback/comment author URL. */
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				$notify_message .= __( 'Pingback excerpt: ' ) . "\r\n" . $comment_content . "\r\n\r\n";
				break;

			default: // Comments.
				/* translators: %s: Post title. */
				$notify_message  = sprintf( __( 'A new comment on the post "%s" is waiting for your approval' ), $post->post_title ) . "\r\n";
				$notify_message .= get_permalink( $comment->comment_post_ID ) . "\r\n\r\n";
				/* translators: 1: Comment author's name, 2: Comment author's IP address, 3: Comment author's hostname. */
				$notify_message .= sprintf( __( 'Author: %1$s (IP address: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				/* translators: %s: Comment author email. */
				$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "\r\n";
				/* translators: %s: Trackback/pingback/comment author URL. */
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";

				if ( $comment->comment_parent ) {
					/* translators: Comment moderation. %s: Parent comment edit URL. */
					$notify_message .= sprintf( __( 'In reply to: %s' ), admin_url( "comment.php?action=editcomment&c={$comment->comment_parent}#wpbody-content" ) ) . "\r\n";
				}

				/* translators: %s: Comment text. */
				$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
				break;
		}

		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Approve it: %s' ), admin_url( "comment.php?action=approve&c={$comment_id}#wpbody-content" ) ) . "\r\n";

		if ( EMPTY_TRASH_DAYS ) {
			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment_id}#wpbody-content" ) ) . "\r\n";
		} else {
			/* translators: Comment moderation. %s: Comment action URL. */
			$notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment_id}#wpbody-content" ) ) . "\r\n";
		}

		/* translators: Comment moderation. %s: Comment action URL. */
		$notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment_id}#wpbody-content" ) ) . "\r\n";

		$notify_message .= sprintf(
			/* translators: Comment moderation. %s: Number of comments awaiting approval. */
			_n(
				'Currently %s comment is waiting for approval. Please visit the moderation panel:',
				'Currently %s comments are waiting for approval. Please visit the moderation panel:',
				$comments_waiting
			),
			number_format_i18n( $comments_waiting )
		) . "\r\n";
		$notify_message .= admin_url( 'edit-comments.php?comment_status=moderated#wpbody-content' ) . "\r\n";

		/* translators: Comment moderation notification email subject. 1: Site title, 2: Post title. */
		$subject         = sprintf( __( '[%1$s] Please moderate: "%2$s"' ), $blogname, $post->post_title );
		$message_headers = '';

		/**
		 * Filters the list of recipients for comment moderation emails.
		 *
		 * @since 3.7.0
		 *
		 * @param string[] $emails     List of email addresses to notify for comment moderation.
		 * @param int      $comment_id Comment ID.
		 */
		$emails = apply_filters( 'comment_moderation_recipients', $emails, $comment_id );

		/**
		 * Filters the comment moderation email text.
		 *
		 * @since 1.5.2
		 *
		 * @param string $notify_message Text of the comment moderation email.
		 * @param int    $comment_id     Comment ID.
		 */
		$notify_message = apply_filters( 'comment_moderation_text', $notify_message, $comment_id );

		/**
		 * Filters the comment moderation email subject.
		 *
		 * @since 1.5.2
		 *
		 * @param string $subject    Subject of the comment moderation email.
		 * @param int    $comment_id Comment ID.
		 */
		$subject = apply_filters( 'comment_moderation_subject', $subject, $comment_id );

		/**
		 * Filters the comment moderation email headers.
		 *
		 * @since 2.8.0
		 *
		 * @param string $message_headers Headers for the comment moderation email.
		 * @param int    $comment_id      Comment ID.
		 */
		$message_headers = apply_filters( 'comment_moderation_headers', $message_headers, $comment_id );

		foreach ( $emails as $email ) {
			wp_mail( $email, wp_specialchars_decode( $subject ), $notify_message, $message_headers );
		}

		if ( $switched_locale ) {
			restore_previous_locale();
		}

		return true;
	}
endif;

if ( ! function_exists( 'wp_password_change_notification' ) ) :
	/**
	 * Notifies the blog admin of a user changing password, normally via email.
	 *
	 * @since 2.7.0
	 *
	 * @param WP_User $user User object.
	 */
	function wp_password_change_notification( $user ) {
		// Send a copy of password change notification to the admin,
		// but check to see if it's the admin whose password we're changing, and skip this.
		if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) ) {
			/* translators: %s: User name. */
			$message = sprintf( __( 'Password changed for user: %s' ), $user->user_login ) . "\r\n";
			// The blogname option is escaped with esc_html() on the way into the database in sanitize_option().
			// We want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

			$wp_password_change_notification_email = array(
				'to'      => get_option( 'admin_email' ),
				/* translators: Password change notification email subject. %s: Site title. */
				'subject' => __( '[%s] Password Changed' ),
				'message' => $message,
				'headers' => '',
			);

			/**
			 * Filters the contents of the password change notification email sent to the site admin.
			 *
			 * @since 4.9.0
			 *
			 * @param array   $wp_password_change_notification_email {
			 *     Used to build wp_mail().
			 *
			 *     @type string $to      The intended recipient - site admin email address.
			 *     @type string $subject The subject of the email.
			 *     @type string $message The body of the email.
			 *     @type string $headers The headers of the email.
			 * }
			 * @param WP_User $user     User object for user whose password was changed.
			 * @param string  $blogname The site title.
			 */
			$wp_password_change_notification_email = apply_filters( 'wp_password_change_notification_email', $wp_password_change_notification_email, $user, $blogname );

			wp_mail(
				$wp_password_change_notification_email['to'],
				wp_specialchars_decode( sprintf( $wp_password_change_notification_email['subject'], $blogname ) ),
				$wp_password_change_notification_email['message'],
				$wp_password_change_notification_email['headers']
			);
		}
	}
endif;

if ( ! function_exists( 'wp_new_user_notification' ) ) :
	/**
	 * Emails login credentials to a newly-registered user.
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since 2.0.0
	 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
	 * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
	 * @since 4.6.0 The `$notify` parameter accepts 'user' for sending notification only to the user created.
	 *
	 * @param int    $user_id    User ID.
	 * @param null   $deprecated Not used (argument deprecated).
	 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
	 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
	 */
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		if ( null !== $deprecated ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}

		// Accepts only 'user', 'admin' , 'both' or default '' as $notify.
		if ( ! in_array( $notify, array( 'user', 'admin', 'both', '' ), true ) ) {
			return;
		}

		$user = get_userdata( $user_id );

		// The blogname option is escaped with esc_html() on the way into the database in sanitize_option().
		// We want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		/**
		 * Filters whether the admin is notified of a new user registration.
		 *
		 * @since 6.1.0
		 *
		 * @param bool    $send Whether to send the email. Default true.
		 * @param WP_User $user User object for new user.
		 */
		$send_notification_to_admin = apply_filters( 'wp_send_new_user_notification_to_admin', true, $user );

		if ( 'user' !== $notify && true === $send_notification_to_admin ) {
			$switched_locale = switch_to_locale( get_locale() );

			/* translators: %s: Site title. */
			$message = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			/* translators: %s: User login. */
			$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
			/* translators: %s: User email address. */
			$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

			$wp_new_user_notification_email_admin = array(
				'to'      => get_option( 'admin_email' ),
				/* translators: New user registration notification email subject. %s: Site title. */
				'subject' => __( '[%s] New User Registration' ),
				'message' => $message,
				'headers' => '',
			);

			/**
			 * Filters the contents of the new user notification email sent to the site admin.
			 *
			 * @since 4.9.0
			 *
			 * @param array   $wp_new_user_notification_email_admin {
			 *     Used to build wp_mail().
			 *
			 *     @type string $to      The intended recipient - site admin email address.
			 *     @type string $subject The subject of the email.
			 *     @type string $message The body of the email.
			 *     @type string $headers The headers of the email.
			 * }
			 * @param WP_User $user     User object for new user.
			 * @param string  $blogname The site title.
			 */
			$wp_new_user_notification_email_admin = apply_filters( 'wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user, $blogname );

			wp_mail(
				$wp_new_user_notification_email_admin['to'],
				wp_specialchars_decode( sprintf( $wp_new_user_notification_email_admin['subject'], $blogname ) ),
				$wp_new_user_notification_email_admin['message'],
				$wp_new_user_notification_email_admin['headers']
			);

			if ( $switched_locale ) {
				restore_previous_locale();
			}
		}

		/**
		 * Filters whether the user is notified of their new user registration.
		 *
		 * @since 6.1.0
		 *
		 * @param bool    $send Whether to send the email. Default true.
		 * @param WP_User $user User object for new user.
		 */
		$send_notification_to_user = apply_filters( 'wp_send_new_user_notification_to_user', true, $user );

		// `$deprecated` was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
		if ( 'admin' === $notify || true !== $send_notification_to_user || ( empty( $deprecated ) && empty( $notify ) ) ) {
			return;
		}

		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) {
			return;
		}

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		/* translators: %s: User login. */
		$message  = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
		$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) . "\r\n\r\n";

		$message .= wp_login_url() . "\r\n";

		$wp_new_user_notification_email = array(
			'to'      => $user->user_email,
			/* translators: Login details notification email subject. %s: Site title. */
			'subject' => __( '[%s] Login Details' ),
			'message' => $message,
			'headers' => '',
		);

		/**
		 * Filters the contents of the new user notification email sent to the new user.
		 *
		 * @since 4.9.0
		 *
		 * @param array   $wp_new_user_notification_email {
		 *     Used to build wp_mail().
		 *
		 *     @type string $to      The intended recipient - New user email address.
		 *     @type string $subject The subject of the email.
		 *     @type string $message The body of the email.
		 *     @type string $headers The headers of the email.
		 * }
		 * @param WP_User $user     User object for new user.
		 * @param string  $blogname The site title.
		 */
		$wp_new_user_notification_email = apply_filters( 'wp_new_user_notification_email', $wp_new_user_notification_email, $user, $blogname );

		wp_mail(
			$wp_new_user_notification_email['to'],
			wp_specialchars_decode( sprintf( $wp_new_user_notification_email['subject'], $blogname ) ),
			$wp_new_user_notification_email['message'],
			$wp_new_user_notification_email['headers']
		);

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}
endif;

if ( ! function_exists( 'wp_nonce_tick' ) ) :
	/**
	 * Returns the time-dependent variable for nonce creation.
	 *
	 * A nonce has a lifespan of two ticks. Nonces in their second tick may be
	 * updated, e.g. by autosave.
	 *
	 * @since 2.5.0
	 * @since 6.1.0 Added `$action` argument.
	 *
	 * @param string|int $action Optional. The nonce action. Default -1.
	 * @return float Float value rounded up to the next highest integer.
	 */
	function wp_nonce_tick( $action = -1 ) {
		/**
		 * Filters the lifespan of nonces in seconds.
		 *
		 * @since 2.5.0
		 * @since 6.1.0 Added `$action` argument to allow for more targeted filters.
		 *
		 * @param int        $lifespan Lifespan of nonces in seconds. Default 86,400 seconds, or one day.
		 * @param string|int $action   The nonce action, or -1 if none was provided.
		 */
		$nonce_life = apply_filters( 'nonce_life', DAY_IN_SECONDS, $action );

		return ceil( time() / ( $nonce_life / 2 ) );
	}
endif;

if ( ! function_exists( 'wp_verify_nonce' ) ) :
	/**
	 * Verifies that a correct security nonce was used with time limit.
	 *
	 * A nonce is valid for 24 hours (by default).
	 *
	 * @since 2.0.3
	 *
	 * @param string     $nonce  Nonce value that was used for verification, usually via a form field.
	 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
	 * @return int|false 1 if the nonce is valid and generated between 0-12 hours ago,
	 *                   2 if the nonce is valid and generated between 12-24 hours ago.
	 *                   False if the nonce is invalid.
	 */
	function wp_verify_nonce( $nonce, $action = -1 ) {
		$nonce = (string) $nonce;
		$user  = wp_get_current_user();
		$uid   = (int) $user->ID;
		if ( ! $uid ) {
			/**
			 * Filters whether the user who generated the nonce is logged out.
			 *
			 * @since 3.5.0
			 *
			 * @param int        $uid    ID of the nonce-owning user.
			 * @param string|int $action The nonce action, or -1 if none was provided.
			 */
			$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
		}

		if ( empty( $nonce ) ) {
			return false;
		}

		$token = wp_get_session_token();
		$i     = wp_nonce_tick( $action );

		// Nonce generated 0-12 hours ago.
		$expected = substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago.
		$expected = substr( wp_hash( ( $i - 1 ) . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			return 2;
		}

		/**
		 * Fires when nonce verification fails.
		 *
		 * @since 4.4.0
		 *
		 * @param string     $nonce  The invalid nonce.
		 * @param string|int $action The nonce action.
		 * @param WP_User    $user   The current user object.
		 * @param string     $token  The user's session token.
		 */
		do_action( 'wp_verify_nonce_failed', $nonce, $action, $user, $token );

		// Invalid nonce.
		return false;
	}
endif;

if ( ! function_exists( 'wp_create_nonce' ) ) :
	/**
	 * Creates a cryptographic token tied to a specific action, user, user session,
	 * and window of time.
	 *
	 * @since 2.0.3
	 * @since 4.0.0 Session tokens were integrated with nonce creation.
	 *
	 * @param string|int $action Scalar value to add context to the nonce.
	 * @return string The token.
	 */
	function wp_create_nonce( $action = -1 ) {
		$user = wp_get_current_user();
		$uid  = (int) $user->ID;
		if ( ! $uid ) {
			/** This filter is documented in wp-includes/pluggable.php */
			$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
		}

		$token = wp_get_session_token( $action );
		$i     = wp_nonce_tick( $action );

		return substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );
	}
endif;

if ( ! function_exists( 'wp_salt' ) ) :
	/**
	 * Returns a salt to add to hashes.
	 *
	 * Salts are created using secret keys. Secret keys are located in two places:
	 * in the database and in the wp-config.php file. The secret key in the database
	 * is randomly generated and will be appended to the secret keys in wp-config.php.
	 *
	 * The secret keys in wp-config.php should be updated to strong, random keys to maximize
	 * security. Below is an example of how the secret key constants are defined.
	 * Do not paste this example directly into wp-config.php. Instead, have a
	 * {@link https://api.wordpress.org/secret-key/1.1/salt/ secret key created} just
	 * for you.
	 *
	 *     define('AUTH_KEY',         ' Xakm<o xQy rw4EMsLKM-?!T+,PFF})H4lzcW57AF0U@N@< >M%G4Yt>f`z]MON');
	 *     define('SECURE_AUTH_KEY',  'LzJ}op]mr|6+![P}Ak:uNdJCJZd>(Hx.-Mh#Tz)pCIU#uGEnfFz|f ;;eU%/U^O~');
	 *     define('LOGGED_IN_KEY',    '|i|Ux`9<p-h$aFf(qnT:sDO:D1P^wZ$$/Ra@miTJi9G;ddp_<q}6H1)o|a +&JCM');
	 *     define('NONCE_KEY',        '%:R{[P|,s.KuMltH5}cI;/k<Gx~j!f0I)m_sIyu+&NJZ)-iO>z7X>QYR0Z_XnZ@|');
	 *     define('AUTH_SALT',        'eZyT)-Naw]F8CwA*VaW#q*|.)g@o}||wf~@C-YSt}(dh_r6EbI#A,y|nU2{B#JBW');
	 *     define('SECURE_AUTH_SALT', '!=oLUTXh,QW=H `}`L|9/^4-3 STz},T(w}W<I`.JjPi)<Bmf1v,HpGe}T1:Xt7n');
	 *     define('LOGGED_IN_SALT',   '+XSqHc;@Q*K_b|Z?NC[3H!!EONbh.n<+=uKR:>*c(u`g~EJBf#8u#R{mUEZrozmm');
	 *     define('NONCE_SALT',       'h`GXHhD>SLWVfg1(1(N{;.V!MoE(SfbA_ksP@&`+AycHcAV$+?@3q+rxV{%^VyKT');
	 *
	 * Salting passwords helps against tools which has stored hashed values of
	 * common dictionary strings. The added values makes it harder to crack.
	 *
	 * @since 2.5.0
	 *
	 * @link https://api.wordpress.org/secret-key/1.1/salt/ Create secrets for wp-config.php
	 *
	 * @param string $scheme Authentication scheme (auth, secure_auth, logged_in, nonce).
	 * @return string Salt value
	 */
	function wp_salt( $scheme = 'auth' ) {
		static $cached_salts = array();
		if ( isset( $cached_salts[ $scheme ] ) ) {
			/**
			 * Filters the WordPress salt.
			 *
			 * @since 2.5.0
			 *
			 * @param string $cached_salt Cached salt for the given scheme.
			 * @param string $scheme      Authentication scheme. Values include 'auth',
			 *                            'secure_auth', 'logged_in', and 'nonce'.
			 */
			return apply_filters( 'salt', $cached_salts[ $scheme ], $scheme );
		}

		static $duplicated_keys;
		if ( null === $duplicated_keys ) {
			$duplicated_keys = array(
				'put your unique phrase here'       => true,
				/*
				 * translators: This string should only be translated if wp-config-sample.php is localized.
				 * You can check the localized release package or
				 * https://i18n.svn.wordpress.org/<locale code>/branches/<wp version>/dist/wp-config-sample.php
				 */
				__( 'put your unique phrase here' ) => true,
			);
			foreach ( array( 'AUTH', 'SECURE_AUTH', 'LOGGED_IN', 'NONCE', 'SECRET' ) as $first ) {
				foreach ( array( 'KEY', 'SALT' ) as $second ) {
					if ( ! defined( "{$first}_{$second}" ) ) {
						continue;
					}
					$value                     = constant( "{$first}_{$second}" );
					$duplicated_keys[ $value ] = isset( $duplicated_keys[ $value ] );
				}
			}
		}

		$values = array(
			'key'  => '',
			'salt' => '',
		);
		if ( defined( 'SECRET_KEY' ) && SECRET_KEY && empty( $duplicated_keys[ SECRET_KEY ] ) ) {
			$values['key'] = SECRET_KEY;
		}
		if ( 'auth' === $scheme && defined( 'SECRET_SALT' ) && SECRET_SALT && empty( $duplicated_keys[ SECRET_SALT ] ) ) {
			$values['salt'] = SECRET_SALT;
		}

		if ( in_array( $scheme, array( 'auth', 'secure_auth', 'logged_in', 'nonce' ), true ) ) {
			foreach ( array( 'key', 'salt' ) as $type ) {
				$const = strtoupper( "{$scheme}_{$type}" );
				if ( defined( $const ) && constant( $const ) && empty( $duplicated_keys[ constant( $const ) ] ) ) {
					$values[ $type ] = constant( $const );
				} elseif ( ! $values[ $type ] ) {
					$values[ $type ] = get_site_option( "{$scheme}_{$type}" );
					if ( ! $values[ $type ] ) {
						$values[ $type ] = wp_generate_password( 64, true, true );
						update_site_option( "{$scheme}_{$type}", $values[ $type ] );
					}
				}
			}
		} else {
			if ( ! $values['key'] ) {
				$values['key'] = get_site_option( 'secret_key' );
				if ( ! $values['key'] ) {
					$values['key'] = wp_generate_password( 64, true, true );
					update_site_option( 'secret_key', $values['key'] );
				}
			}
			$values['salt'] = hash_hmac( 'md5', $scheme, $values['key'] );
		}

		$cached_salts[ $scheme ] = $values['key'] . $values['salt'];

		/** This filter is documented in wp-includes/pluggable.php */
		return apply_filters( 'salt', $cached_salts[ $scheme ], $scheme );
	}
endif;

if ( ! function_exists( 'wp_hash' ) ) :
	/**
	 * Gets hash of given string.
	 *
	 * @since 2.0.3
	 *
	 * @param string $data   Plain text to hash.
	 * @param string $scheme Authentication scheme (auth, secure_auth, logged_in, nonce).
	 * @return string Hash of $data.
	 */
	function wp_hash( $data, $scheme = 'auth' ) {
		$salt = wp_salt( $scheme );

		return hash_hmac( 'md5', $data, $salt );
	}
endif;

if ( ! function_exists( 'wp_hash_password' ) ) :
	/**
	 * Creates a hash (encrypt) of a plain text password.
	 *
	 * For integration with other applications, this function can be overwritten to
	 * instead use the other package password checking algorithm.
	 *
	 * @since 2.5.0
	 *
	 * @global PasswordHash $wp_hasher PHPass object
	 *
	 * @param string $password Plain text user password to hash.
	 * @return string The hash string of the password.
	 */
	function wp_hash_password( $password ) {
		global $wp_hasher;

		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			// By default, use the portable hash from phpass.
			$wp_hasher = new PasswordHash( 8, true );
		}

		return $wp_hasher->HashPassword( trim( $password ) );
	}
endif;

if ( ! function_exists( 'wp_check_password' ) ) :
	/**
	 * Checks the plaintext password against the encrypted Password.
	 *
	 * Maintains compatibility between old version and the new cookie authentication
	 * protocol using PHPass library. The $hash parameter is the encrypted password
	 * and the function compares the plain text password when encrypted similarly
	 * against the already encrypted password to see if they match.
	 *
	 * For integration with other applications, this function can be overwritten to
	 * instead use the other package password checking algorithm.
	 *
	 * @since 2.5.0
	 *
	 * @global PasswordHash $wp_hasher PHPass object used for checking the password
	 *                                 against the $hash + $password.
	 * @uses PasswordHash::CheckPassword
	 *
	 * @param string     $password Plaintext user's password.
	 * @param string     $hash     Hash of the user's password to check against.
	 * @param string|int $user_id  Optional. User ID.
	 * @return bool False, if the $password does not match the hashed password.
	 */
	function wp_check_password( $password, $hash, $user_id = '' ) {
		global $wp_hasher;

		// If the hash is still md5...
		if ( strlen( $hash ) <= 32 ) {
			$check = hash_equals( $hash, md5( $password ) );
			if ( $check && $user_id ) {
				// Rehash using new hash.
				wp_set_password( $password, $user_id );
				$hash = wp_hash_password( $password );
			}

			/**
			 * Filters whether the plaintext password matches the encrypted password.
			 *
			 * @since 2.5.0
			 *
			 * @param bool       $check    Whether the passwords match.
			 * @param string     $password The plaintext password.
			 * @param string     $hash     The hashed password.
			 * @param string|int $user_id  User ID. Can be empty.
			 */
			return apply_filters( 'check_password', $check, $password, $hash, $user_id );
		}

		// If the stored hash is longer than an MD5,
		// presume the new style phpass portable hash.
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			// By default, use the portable hash from phpass.
			$wp_hasher = new PasswordHash( 8, true );
		}

		$check = $wp_hasher->CheckPassword( $password, $hash );

		/** This filter is documented in wp-includes/pluggable.php */
		return apply_filters( 'check_password', $check, $password, $hash, $user_id );
	}
endif;

if ( ! function_exists( 'wp_generate_password' ) ) :
	/**
	 * Generates a random password drawn from the defined set of characters.
	 *
	 * Uses wp_rand() is used to create passwords with far less predictability
	 * than similar native PHP functions like `rand()` or `mt_rand()`.
	 *
	 * @since 2.5.0
	 *
	 * @param int  $length              Optional. The length of password to generate. Default 12.
	 * @param bool $special_chars       Optional. Whether to include standard special characters.
	 *                                  Default true.
	 * @param bool $extra_special_chars Optional. Whether to include other special characters.
	 *                                  Used when generating secret keys and salts. Default false.
	 * @return string The random password.
	 */
	function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ( $special_chars ) {
			$chars .= '!@#$%^&*()';
		}
		if ( $extra_special_chars ) {
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		}

		$password = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$password .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		/**
		 * Filters the randomly-generated password.
		 *
		 * @since 3.0.0
		 * @since 5.3.0 Added the `$length`, `$special_chars`, and `$extra_special_chars` parameters.
		 *
		 * @param string $password            The generated password.
		 * @param int    $length              The length of password to generate.
		 * @param bool   $special_chars       Whether to include standard special characters.
		 * @param bool   $extra_special_chars Whether to include other special characters.
		 */
		return apply_filters( 'random_password', $password, $length, $special_chars, $extra_special_chars );
	}
endif;

if ( ! function_exists( 'wp_rand' ) ) :
	/**
	 * Generates a random non-negative number.
	 *
	 * @since 2.6.2
	 * @since 4.4.0 Uses PHP7 random_int() or the random_compat library if available.
	 * @since 6.1.0 Returns zero instead of a random number if both `$min` and `$max` are zero.
	 *
	 * @global string $rnd_value
	 *
	 * @param int $min Optional. Lower limit for the generated number.
	 *                 Accepts positive integers or zero. Defaults to 0.
	 * @param int $max Optional. Upper limit for the generated number.
	 *                 Accepts positive integers. Defaults to 4294967295.
	 * @return int A random non-negative number between min and max.
	 */
	function wp_rand( $min = null, $max = null ) {
		global $rnd_value;

		// Some misconfigured 32-bit environments (Entropy PHP, for example)
		// truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
		$max_random_number = 3000000000 === 2147483647 ? (float) '4294967295' : 4294967295; // 4294967295 = 0xffffffff

		if ( null === $min ) {
			$min = 0;
		}

		if ( null === $max ) {
			$max = $max_random_number;
		}

		// We only handle ints, floats are truncated to their integer value.
		$min = (int) $min;
		$max = (int) $max;

		// Use PHP's CSPRNG, or a compatible method.
		static $use_random_int_functionality = true;
		if ( $use_random_int_functionality ) {
			try {
				// wp_rand() can accept arguments in either order, PHP cannot.
				$_max = max( $min, $max );
				$_min = min( $min, $max );
				$val  = random_int( $_min, $_max );
				if ( false !== $val ) {
					return absint( $val );
				} else {
					$use_random_int_functionality = false;
				}
			} catch ( Error $e ) {
				$use_random_int_functionality = false;
			} catch ( Exception $e ) {
				$use_random_int_functionality = false;
			}
		}

		// Reset $rnd_value after 14 uses.
		// 32 (md5) + 40 (sha1) + 40 (sha1) / 8 = 14 random numbers from $rnd_value.
		if ( strlen( $rnd_value ) < 8 ) {
			if ( defined( 'WP_SETUP_CONFIG' ) ) {
				static $seed = '';
			} else {
				$seed = get_transient( 'random_seed' );
			}
			$rnd_value  = md5( uniqid( microtime() . mt_rand(), true ) . $seed );
			$rnd_value .= sha1( $rnd_value );
			$rnd_value .= sha1( $rnd_value . $seed );
			$seed       = md5( $seed . $rnd_value );
			if ( ! defined( 'WP_SETUP_CONFIG' ) && ! defined( 'WP_INSTALLING' ) ) {
				set_transient( 'random_seed', $seed );
			}
		}

		// Take the first 8 digits for our value.
		$value = substr( $rnd_value, 0, 8 );

		// Strip the first eight, leaving the remainder for the next call to wp_rand().
		$rnd_value = substr( $rnd_value, 8 );

		$value = abs( hexdec( $value ) );

		// Reduce the value to be within the min - max range.
		$value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );

		return abs( (int) $value );
	}
endif;

if ( ! function_exists( 'wp_set_password' ) ) :
	/**
	 * Updates the user's password with a new encrypted one.
	 *
	 * For integration with other applications, this function can be overwritten to
	 * instead use the other package password checking algorithm.
	 *
	 * Please note: This function should be used sparingly and is really only meant for single-time
	 * application. Leveraging this improperly in a plugin or theme could result in an endless loop
	 * of password resets if precautions are not taken to ensure it does not execute on every page load.
	 *
	 * @since 2.5.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $password The plaintext new user password.
	 * @param int    $user_id  User ID.
	 */
	function wp_set_password( $password, $user_id ) {
		global $wpdb;

		$hash = wp_hash_password( $password );
		$wpdb->update(
			$wpdb->users,
			array(
				'user_pass'           => $hash,
				'user_activation_key' => '',
			),
			array( 'ID' => $user_id )
		);

		clean_user_cache( $user_id );

		/**
		 * Fires after the user password is set.
		 *
		 * @since 6.2.0
		 *
		 * @param string $password The plaintext password just set.
		 * @param int    $user_id  The ID of the user whose password was just set.
		 */
		do_action( 'wp_set_password', $password, $user_id );
	}
endif;

if ( ! function_exists( 'get_avatar' ) ) :
	/**
	 * Retrieves the avatar `<img>` tag for a user, email address, MD5 hash, comment, or post.
	 *
	 * @since 2.5.0
	 * @since 4.2.0 Optional `$args` parameter added.
	 *
	 * @param mixed  $id_or_email   The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
	 *                              user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param int    $size          Optional. Height and width of the avatar image file in pixels. Default 96.
	 * @param string $default_value Optional. URL for the default image or a default type. Accepts '404'
	 *                              (return a 404 instead of a default image), 'retro' (8bit), 'monsterid'
	 *                              (monster), 'wavatar' (cartoon face), 'indenticon' (the "quilt"),
	 *                              'mystery', 'mm', or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF),
	 *                              or 'gravatar_default' (the Gravatar logo). Default is the value of the
	 *                              'avatar_default' option, with a fallback of 'mystery'.
	 * @param string $alt           Optional. Alternative text to use in img tag. Default empty.
	 * @param array  $args {
	 *     Optional. Extra arguments to retrieve the avatar.
	 *
	 *     @type int          $height        Display height of the avatar in pixels. Defaults to $size.
	 *     @type int          $width         Display width of the avatar in pixels. Defaults to $size.
	 *     @type bool         $force_default Whether to always show the default image, never the Gravatar. Default false.
	 *     @type string       $rating        What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
	 *                                       judged in that order. Default is the value of the 'avatar_rating' option.
	 *     @type string       $scheme        URL scheme to use. See set_url_scheme() for accepted values.
	 *                                       Default null.
	 *     @type array|string $class         Array or string of additional classes to add to the img element.
	 *                                       Default null.
	 *     @type bool         $force_display Whether to always show the avatar - ignores the show_avatars option.
	 *                                       Default false.
	 *     @type string       $loading       Value for the `loading` attribute.
	 *                                       Default null.
	 *     @type string       $extra_attr    HTML attributes to insert in the IMG element. Is not sanitized. Default empty.
	 * }
	 * @return string|false `<img>` tag for the user's avatar. False on failure.
	 */
	function get_avatar( $id_or_email, $size = 96, $default_value = '', $alt = '', $args = null ) {
		$defaults = array(
			// get_avatar_data() args.
			'size'          => 96,
			'height'        => null,
			'width'         => null,
			'default'       => get_option( 'avatar_default', 'mystery' ),
			'force_default' => false,
			'rating'        => get_option( 'avatar_rating' ),
			'scheme'        => null,
			'alt'           => '',
			'class'         => null,
			'force_display' => false,
			'loading'       => null,
			'extra_attr'    => '',
			'decoding'      => 'async',
		);

		if ( wp_lazy_loading_enabled( 'img', 'get_avatar' ) ) {
			$defaults['loading'] = wp_get_loading_attr_default( 'get_avatar' );
		}

		if ( empty( $args ) ) {
			$args = array();
		}

		$args['size']    = (int) $size;
		$args['default'] = $default_value;
		$args['alt']     = $alt;

		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['height'] ) ) {
			$args['height'] = $args['size'];
		}
		if ( empty( $args['width'] ) ) {
			$args['width'] = $args['size'];
		}

		if ( is_object( $id_or_email ) && isset( $id_or_email->comment_ID ) ) {
			$id_or_email = get_comment( $id_or_email );
		}

		/**
		 * Allows the HTML for a user's avatar to be returned early.
		 *
		 * Returning a non-null value will effectively short-circuit get_avatar(), passing
		 * the value through the {@see 'get_avatar'} filter and returning early.
		 *
		 * @since 4.2.0
		 *
		 * @param string|null $avatar      HTML for the user's avatar. Default null.
		 * @param mixed       $id_or_email The avatar to retrieve. Accepts a user_id, Gravatar MD5 hash,
		 *                                 user email, WP_User object, WP_Post object, or WP_Comment object.
		 * @param array       $args        Arguments passed to get_avatar_url(), after processing.
		 */
		$avatar = apply_filters( 'pre_get_avatar', null, $id_or_email, $args );

		if ( ! is_null( $avatar ) ) {
			/** This filter is documented in wp-includes/pluggable.php */
			return apply_filters( 'get_avatar', $avatar, $id_or_email, $args['size'], $args['default'], $args['alt'], $args );
		}

		if ( ! $args['force_display'] && ! get_option( 'show_avatars' ) ) {
			return false;
		}

		$url2x = get_avatar_url( $id_or_email, array_merge( $args, array( 'size' => $args['size'] * 2 ) ) );

		$args = get_avatar_data( $id_or_email, $args );

		$url = $args['url'];

		if ( ! $url || is_wp_error( $url ) ) {
			return false;
		}

		$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

		if ( ! $args['found_avatar'] || $args['force_default'] ) {
			$class[] = 'avatar-default';
		}

		if ( $args['class'] ) {
			if ( is_array( $args['class'] ) ) {
				$class = array_merge( $class, $args['class'] );
			} else {
				$class[] = $args['class'];
			}
		}

		// Add `loading` and `decoding` attributes.
		$extra_attr = $args['extra_attr'];

		if ( in_array( $args['loading'], array( 'lazy', 'eager' ), true )
			&& ! preg_match( '/\bloading\s*=/', $extra_attr )
		) {
			if ( ! empty( $extra_attr ) ) {
				$extra_attr .= ' ';
			}

			$extra_attr .= "loading='{$args['loading']}'";
		}

		if ( in_array( $args['decoding'], array( 'async', 'sync', 'auto' ), true )
			&& ! preg_match( '/\bdecoding\s*=/', $extra_attr )
		) {
			if ( ! empty( $extra_attr ) ) {
				$extra_attr .= ' ';
			}

			$extra_attr .= "decoding='{$args['decoding']}'";
		}

		$avatar = sprintf(
			"<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
			esc_attr( $args['alt'] ),
			esc_url( $url ),
			esc_url( $url2x ) . ' 2x',
			esc_attr( implode( ' ', $class ) ),
			(int) $args['height'],
			(int) $args['width'],
			$extra_attr
		);

		/**
		 * Filters the HTML for a user's avatar.
		 *
		 * @since 2.5.0
		 * @since 4.2.0 The `$args` parameter was added.
		 *
		 * @param string $avatar        HTML for the user's avatar.
		 * @param mixed  $id_or_email   The avatar to retrieve. Accepts a user_id, Gravatar MD5 hash,
		 *                              user email, WP_User object, WP_Post object, or WP_Comment object.
		 * @param int    $size          Square avatar width and height in pixels to retrieve.
		 * @param string $default_value URL for the default image or a default type. Accepts '404', 'retro', 'monsterid',
		 *                              'wavatar', 'indenticon', 'mystery', 'mm', 'mysteryman', 'blank', or 'gravatar_default'.
		 * @param string $alt           Alternative text to use in the avatar image tag.
		 * @param array  $args          Arguments passed to get_avatar_data(), after processing.
		 */
		return apply_filters( 'get_avatar', $avatar, $id_or_email, $args['size'], $args['default'], $args['alt'], $args );
	}
endif;

if ( ! function_exists( 'wp_text_diff' ) ) :
	/**
	 * Displays a human readable HTML representation of the difference between two strings.
	 *
	 * The Diff is available for getting the changes between versions. The output is
	 * HTML, so the primary use is for displaying the changes. If the two strings
	 * are equivalent, then an empty string will be returned.
	 *
	 * @since 2.6.0
	 *
	 * @see wp_parse_args() Used to change defaults to user defined settings.
	 * @uses Text_Diff
	 * @uses WP_Text_Diff_Renderer_Table
	 *
	 * @param string       $left_string  "old" (left) version of string.
	 * @param string       $right_string "new" (right) version of string.
	 * @param string|array $args {
	 *     Associative array of options to pass to WP_Text_Diff_Renderer_Table().
	 *
	 *     @type string $title           Titles the diff in a manner compatible
	 *                                   with the output. Default empty.
	 *     @type string $title_left      Change the HTML to the left of the title.
	 *                                   Default empty.
	 *     @type string $title_right     Change the HTML to the right of the title.
	 *                                   Default empty.
	 *     @type bool   $show_split_view True for split view (two columns), false for
	 *                                   un-split view (single column). Default true.
	 * }
	 * @return string Empty string if strings are equivalent or HTML with differences.
	 */
	function wp_text_diff( $left_string, $right_string, $args = null ) {
		$defaults = array(
			'title'           => '',
			'title_left'      => '',
			'title_right'     => '',
			'show_split_view' => true,
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( ! class_exists( 'WP_Text_Diff_Renderer_Table', false ) ) {
			require ABSPATH . WPINC . '/wp-diff.php';
		}

		$left_string  = normalize_whitespace( $left_string );
		$right_string = normalize_whitespace( $right_string );

		$left_lines  = explode( "\n", $left_string );
		$right_lines = explode( "\n", $right_string );
		$text_diff   = new Text_Diff( $left_lines, $right_lines );
		$renderer    = new WP_Text_Diff_Renderer_Table( $args );
		$diff        = $renderer->render( $text_diff );

		if ( ! $diff ) {
			return '';
		}

		$is_split_view       = ! empty( $args['show_split_view'] );
		$is_split_view_class = $is_split_view ? ' is-split-view' : '';

		$r = "<table class='diff$is_split_view_class'>\n";

		if ( $args['title'] ) {
			$r .= "<caption class='diff-title'>$args[title]</caption>\n";
		}

		if ( $args['title_left'] || $args['title_right'] ) {
			$r .= '<thead>';
		}

		if ( $args['title_left'] || $args['title_right'] ) {
			$th_or_td_left  = empty( $args['title_left'] ) ? 'td' : 'th';
			$th_or_td_right = empty( $args['title_right'] ) ? 'td' : 'th';

			$r .= "<tr class='diff-sub-title'>\n";
			$r .= "\t<$th_or_td_left>$args[title_left]</$th_or_td_left>\n";
			if ( $is_split_view ) {
				$r .= "\t<$th_or_td_right>$args[title_right]</$th_or_td_right>\n";
			}
			$r .= "</tr>\n";
		}

		if ( $args['title_left'] || $args['title_right'] ) {
			$r .= "</thead>\n";
		}

		$r .= "<tbody>\n$diff\n</tbody>\n";
		$r .= '</table>';

		return $r;
	}
endif;
