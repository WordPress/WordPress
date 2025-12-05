<?php

// We plan to gradually remove all of the disabled lint rules below.
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
// phpcs:disable WordPress.Security.NonceVerification.Missing
// phpcs:disable Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure

class Akismet {
	const API_HOST                          = 'rest.akismet.com';
	const API_PORT                          = 80;
	const MAX_DELAY_BEFORE_MODERATION_EMAIL = 86400; // One day in seconds
	const ALERT_CODE_COMMERCIAL             = 30001;

	public static $limit_notices = array(
		10501 => 'FIRST_MONTH_OVER_LIMIT',
		10502 => 'SECOND_MONTH_OVER_LIMIT',
		10504 => 'THIRD_MONTH_APPROACHING_LIMIT',
		10508 => 'THIRD_MONTH_OVER_LIMIT',
		10516 => 'FOUR_PLUS_MONTHS_OVER_LIMIT',
	);

	private static $last_comment                                = '';
	private static $initiated                                   = false;
	private static $last_comment_result                         = null;
	private static $comment_as_submitted_allowed_keys           = array(
		'blog'                 => '',
		'blog_charset'         => '',
		'blog_lang'            => '',
		'blog_ua'              => '',
		'comment_agent'        => '',
		'comment_author'       => '',
		'comment_author_IP'    => '',
		'comment_author_email' => '',
		'comment_author_url'   => '',
		'comment_content'      => '',
		'comment_date_gmt'     => '',
		'comment_tags'         => '',
		'comment_type'         => '',
		'guid'                 => '',
		'is_test'              => '',
		'permalink'            => '',
		'reporter'             => '',
		'site_domain'          => '',
		'submit_referer'       => '',
		'submit_uri'           => '',
		'user_ID'              => '',
		'user_agent'           => '',
		'user_id'              => '',
		'user_ip'              => '',
	);

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

		add_action( 'wp_insert_comment', array( 'Akismet', 'auto_check_update_meta' ), 10, 2 );
		add_action( 'wp_insert_comment', array( 'Akismet', 'schedule_email_fallback' ), 10, 2 );
		add_action( 'wp_insert_comment', array( 'Akismet', 'schedule_approval_fallback' ), 10, 2 );

		add_filter( 'preprocess_comment', array( 'Akismet', 'auto_check_comment' ), 1 );
		add_filter( 'rest_pre_insert_comment', array( 'Akismet', 'rest_auto_check_comment' ), 1 );

		add_action( 'comment_form', array( 'Akismet', 'load_form_js' ) );
		add_action( 'do_shortcode_tag', array( 'Akismet', 'load_form_js_via_filter' ), 10, 4 );

		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments_meta' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_orphaned_commentmeta' ) );
		add_action( 'akismet_schedule_cron_recheck', array( 'Akismet', 'cron_recheck' ) );

		add_action( 'akismet_email_fallback', array( 'Akismet', 'email_fallback' ), 10, 3 );
		add_action( 'akismet_approval_fallback', array( 'Akismet', 'approval_fallback' ), 10, 3 );

		add_action( 'comment_form', array( 'Akismet', 'add_comment_nonce' ), 1 );
		add_action( 'comment_form', array( 'Akismet', 'output_custom_form_fields' ) );
		add_filter( 'script_loader_tag', array( 'Akismet', 'set_form_js_async' ), 10, 3 );

		add_filter( 'notify_moderator', array( 'Akismet', 'disable_emails_if_unreachable' ), 1000, 2 );
		add_filter( 'notify_post_author', array( 'Akismet', 'disable_emails_if_unreachable' ), 1000, 2 );

		add_filter( 'pre_comment_approved', array( 'Akismet', 'last_comment_status' ), 10, 2 );

		add_action( 'transition_comment_status', array( 'Akismet', 'transition_comment_status' ), 10, 3 );

		// Run this early in the pingback call, before doing a remote fetch of the source uri
		add_action( 'xmlrpc_call', array( 'Akismet', 'pre_check_pingback' ), 10, 3 );

		// Jetpack compatibility
		add_filter( 'jetpack_options_whitelist', array( 'Akismet', 'add_to_jetpack_options_whitelist' ) );
		add_filter( 'jetpack_contact_form_html', array( 'Akismet', 'inject_custom_form_fields' ) );
		add_filter( 'jetpack_contact_form_akismet_values', array( 'Akismet', 'prepare_custom_form_values' ) );

		// Gravity Forms
		add_filter( 'gform_get_form_filter', array( 'Akismet', 'inject_custom_form_fields' ) );
		add_filter( 'gform_akismet_fields', array( 'Akismet', 'prepare_custom_form_values' ) );

		// Contact Form 7
		add_filter( 'wpcf7_form_elements', array( 'Akismet', 'append_custom_form_fields' ) );
		add_filter( 'wpcf7_akismet_parameters', array( 'Akismet', 'prepare_custom_form_values' ) );

		// Formidable Forms
		add_filter( 'frm_filter_final_form', array( 'Akismet', 'inject_custom_form_fields' ) );
		add_filter( 'frm_akismet_values', array( 'Akismet', 'prepare_custom_form_values' ) );

		// Fluent Forms
		/*
		 * The Fluent Forms  hook names were updated in version 5.0.0. The last version that supported
		 * the original hook names was 4.3.25, and version 4.3.25 was tested up to WordPress version 6.1.
		 *
		 * The legacy hooks are fired before the new hooks. See
		 * https://github.com/fluentform/fluentform/commit/cc45341afcae400f217470a7bbfb15efdd80454f
		 *
		 * The legacy Fluent Forms hooks will be removed when Akismet no longer supports WordPress version 6.1.
		 * This will provide compatibility with previous versions of Fluent Forms for a reasonable amount of time.
		 */
		add_filter( 'fluentform_form_element_start', array( 'Akismet', 'output_custom_form_fields' ) );
		add_filter( 'fluentform_akismet_fields', array( 'Akismet', 'prepare_custom_form_values' ), 10, 2 );
		// Current Fluent Form hooks.
		add_filter( 'fluentform/form_element_start', array( 'Akismet', 'output_custom_form_fields' ) );
		add_filter( 'fluentform/akismet_fields', array( 'Akismet', 'prepare_custom_form_values' ), 10, 2 );

		add_action( 'update_option_wordpress_api_key', array( 'Akismet', 'updated_option' ), 10, 2 );
		add_action( 'add_option_wordpress_api_key', array( 'Akismet', 'added_option' ), 10, 2 );

		add_action( 'comment_form_after', array( 'Akismet', 'display_comment_form_privacy_notice' ) );
	}

	public static function get_api_key() {
		return apply_filters( 'akismet_get_api_key', defined( 'WPCOM_API_KEY' ) ? constant( 'WPCOM_API_KEY' ) : get_option( 'wordpress_api_key' ) );
	}

	/**
	 * Exchange the API key for a token that can only be used to access stats pages.
	 *
	 * @return string
	 */
	public static function get_access_token() {
		static $access_token = null;

		if ( is_null( $access_token ) ) {
			$request_args = array( 'api_key' => self::get_api_key() );

			$request_args = apply_filters( 'akismet_request_args', $request_args, 'token' );

			$response = self::http_post( self::build_query( $request_args ), 'token' );

			$access_token = $response[1];
		}

		return $access_token;
	}

	public static function check_key_status( $key, $ip = null ) {
		$request_args = array(
			'key'  => $key,
			'blog' => get_option( 'home' ),
		);

		$request_args = apply_filters( 'akismet_request_args', $request_args, 'verify-key' );

		return self::http_post( self::build_query( $request_args ), 'verify-key', $ip );
	}

	public static function verify_key( $key, $ip = null ) {
		// Shortcut for obviously invalid keys.
		if ( strlen( $key ) != 12 ) {
			return 'invalid';
		}

		$response = self::check_key_status( $key, $ip );

		if ( $response[1] != 'valid' && $response[1] != 'invalid' ) {
			return 'failed';
		}

		return $response[1];
	}

	public static function deactivate_key( $key ) {
		$request_args = array(
			'key'  => $key,
			'blog' => get_option( 'home' ),
		);

		$request_args = apply_filters( 'akismet_request_args', $request_args, 'deactivate' );

		$response = self::http_post( self::build_query( $request_args ), 'deactivate' );

		if ( $response[1] != 'deactivated' ) {
			return 'failed';
		}

		return $response[1];
	}

	/**
	 * Add the akismet option to the Jetpack options management whitelist.
	 *
	 * @param array $options The list of whitelisted option names.
	 * @return array The updated whitelist
	 */
	public static function add_to_jetpack_options_whitelist( $options ) {
		$options[] = 'wordpress_api_key';
		return $options;
	}

	/**
	 * When the akismet option is updated, run the registration call.
	 *
	 * This should only be run when the option is updated from the Jetpack/WP.com
	 * API call, and only if the new key is different than the old key.
	 *
	 * @param mixed $old_value   The old option value.
	 * @param mixed $value       The new option value.
	 */
	public static function updated_option( $old_value, $value ) {
		// Not an API call
		if ( ! class_exists( 'WPCOM_JSON_API_Update_Option_Endpoint' ) ) {
			return;
		}
		// Only run the registration if the old key is different.
		if ( $old_value !== $value ) {
			self::verify_key( $value );
		}
	}

	/**
	 * Treat the creation of an API key the same as updating the API key to a new value.
	 *
	 * @param mixed $option_name   Will always be "wordpress_api_key", until something else hooks in here.
	 * @param mixed $value         The option value.
	 */
	public static function added_option( $option_name, $value ) {
		if ( 'wordpress_api_key' === $option_name ) {
			return self::updated_option( '', $value );
		}
	}

	public static function rest_auto_check_comment( $commentdata ) {
		return self::auto_check_comment( $commentdata, 'rest_api' );
	}

	/**
	 * Check a comment for spam.
	 *
	 * @param array  $commentdata
	 * @param string $context What kind of request triggered this comment check? Possible values are 'default', 'rest_api', and 'xml-rpc'.
	 * @return array|WP_Error Either the $commentdata array with additional entries related to its spam status
	 *                        or a WP_Error, if it's a REST API request and the comment should be discarded.
	 */
	public static function auto_check_comment( $commentdata, $context = 'default' ) {
		// If no key is configured, then there's no point in doing any of this.
		if ( ! self::get_api_key() ) {
			return $commentdata;
		}

		if ( ! isset( $commentdata['comment_meta'] ) ) {
			$commentdata['comment_meta'] = array();
		}

		self::$last_comment_result = null;

		// Skip the Akismet check if the comment matches the Disallowed Keys list.
		if ( function_exists( 'wp_check_comment_disallowed_list' ) ) {
			$comment_author       = isset( $commentdata['comment_author'] ) ? $commentdata['comment_author'] : '';
			$comment_author_email = isset( $commentdata['comment_author_email'] ) ? $commentdata['comment_author_email'] : '';
			$comment_author_url   = isset( $commentdata['comment_author_url'] ) ? $commentdata['comment_author_url'] : '';
			$comment_content      = isset( $commentdata['comment_content'] ) ? $commentdata['comment_content'] : '';
			$comment_author_ip    = isset( $commentdata['comment_author_IP'] ) ? $commentdata['comment_author_IP'] : '';
			$comment_agent        = isset( $commentdata['comment_agent'] ) ? $commentdata['comment_agent'] : '';

			if ( wp_check_comment_disallowed_list( $comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_ip, $comment_agent ) ) {
				$commentdata['akismet_result'] = 'skipped';
				$commentdata['comment_meta']['akismet_result'] = 'skipped';

				$commentdata['akismet_skipped_microtime'] = microtime( true );
				$commentdata['comment_meta']['akismet_skipped_microtime'] = $commentdata['akismet_skipped_microtime'];

				self::set_last_comment( $commentdata );

				return $commentdata;
			}
		}

		$comment = $commentdata;

		$comment['user_ip']      = self::get_ip_address();
		$comment['user_agent']   = self::get_user_agent();
		$comment['referrer']     = self::get_referer();
		$comment['blog']         = get_option( 'home' );
		$comment['blog_lang']    = get_locale();
		$comment['blog_charset'] = get_option( 'blog_charset' );
		$comment['permalink']    = get_permalink( $comment['comment_post_ID'] );

		if ( ! empty( $comment['user_ID'] ) ) {
			$comment['user_role'] = self::get_user_roles( $comment['user_ID'] );
		}

		/** See filter documentation in init_hooks(). */
		$akismet_nonce_option             = apply_filters( 'akismet_comment_nonce', get_option( 'akismet_comment_nonce' ) );
		$comment['akismet_comment_nonce'] = 'inactive';
		if ( $akismet_nonce_option == 'true' || $akismet_nonce_option == '' ) {
			$comment['akismet_comment_nonce'] = 'failed';
			if ( isset( $_POST['akismet_comment_nonce'] ) && wp_verify_nonce( $_POST['akismet_comment_nonce'], 'akismet_comment_nonce_' . $comment['comment_post_ID'] ) ) {
				$comment['akismet_comment_nonce'] = 'passed';
			}

			// comment reply in wp-admin
			if ( isset( $_POST['_ajax_nonce-replyto-comment'] ) && check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' ) ) {
				$comment['akismet_comment_nonce'] = 'passed';
			}
		}

		if ( self::is_test_mode() ) {
			$comment['is_test'] = 'true';
		}

		foreach ( $_POST as $key => $value ) {
			if ( is_string( $value ) ) {
				$comment[ "POST_{$key}" ] = $value;
			}
		}

		foreach ( $_SERVER as $key => $value ) {
			if ( ! is_string( $value ) ) {
				continue;
			}

			if ( preg_match( '/^HTTP_COOKIE/', $key ) ) {
				continue;
			}

			// Send any potentially useful $_SERVER vars, but avoid sending junk we don't need.
			if ( preg_match( '/^(HTTP_|REMOTE_ADDR|REQUEST_URI|DOCUMENT_URI)/', $key ) ) {
				$comment[ "$key" ] = $value;
			}
		}

		$post = get_post( $comment['comment_post_ID'] );

		if ( ! is_null( $post ) ) {
			// $post can technically be null, although in the past, it's always been an indicator of another plugin interfering.
			$comment['comment_post_modified_gmt'] = $post->post_modified_gmt;

			// Tags and categories are important context in which to consider the comment.
			$comment['comment_context'] = array();

			$tag_names = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );

			if ( $tag_names && ! is_wp_error( $tag_names ) ) {
				foreach ( $tag_names as $tag_name ) {
					$comment['comment_context'][] = $tag_name;
				}
			}

			$category_names = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );

			if ( $category_names && ! is_wp_error( $category_names ) ) {
				foreach ( $category_names as $category_name ) {
					$comment['comment_context'][] = $category_name;
				}
			}
		}

		// Set the webhook callback URL. The Akismet servers may make a request to this URL
		// if a comment's spam status changes.
		$comment['callback'] = get_rest_url( null, 'akismet/v1/webhook' );

		/**
		 * Filter the data that is used to generate the request body for the API call.
		 *
		 * @since 5.3.1
		 *
		 * @param array $comment An array of request data.
		 * @param string $endpoint The API endpoint being requested.
		 */
		$comment = apply_filters( 'akismet_request_args', $comment, 'comment-check' );

		$response = self::http_post( self::build_query( $comment ), 'comment-check' );

		do_action( 'akismet_comment_check_response', $response );

		$commentdata['comment_as_submitted'] = array_intersect_key( $comment, self::$comment_as_submitted_allowed_keys );

		// Also include any form fields we inject into the comment form, like ak_js
		foreach ( $_POST as $key => $value ) {
			if ( is_string( $value ) && strpos( $key, 'ak_' ) === 0 ) {
				$commentdata['comment_as_submitted'][ 'POST_' . $key ] = $value;
			}
		}

		$commentdata['akismet_result'] = $response[1];

		if ( 'true' === $response[1] || 'false' === $response[1] ) {
			$commentdata['comment_meta']['akismet_result'] = $response[1];
		} else {
			$commentdata['comment_meta']['akismet_error'] = time();
		}

		if ( isset( $response[0]['x-akismet-pro-tip'] ) ) {
			$commentdata['akismet_pro_tip'] = $response[0]['x-akismet-pro-tip'];
			$commentdata['comment_meta']['akismet_pro_tip'] = $response[0]['x-akismet-pro-tip'];
		}

		if ( isset( $response[0]['x-akismet-guid'] ) ) {
			$commentdata['akismet_guid'] = $response[0]['x-akismet-guid'];
			$commentdata['comment_meta']['akismet_guid'] = $response[0]['x-akismet-guid'];

			if ( 'false' === $response[1] ) {
				// If Akismet has indicated that there is more processing to be done before this comment
				// can be fully classified, delay moderation emails until that processing is complete.
				if ( isset( $response[0]['X-akismet-recheck-after'] ) ) {
					// Prevent this comment from reaching Active status (keep in Pending) until
					// it's finished being checked.
					$commentdata['comment_approved'] = '0';
					self::$last_comment_result = '0';

					// Indicate that we should schedule a fallback so that if the site never receives a
					// followup from Akismet, the emails will still be sent. We don't schedule it here
					// because we don't yet have the comment ID. Add an extra minute to ensure that the
					// fallback email isn't sent while the recheck or webhook call is happening.
					$delay = $response[0]['X-akismet-recheck-after'] * 2;

					$commentdata['comment_meta']['akismet_schedule_approval_fallback'] = $delay;

					// If this commentmeta is present, we'll prevent the moderation email from sending once.
					$commentdata['comment_meta']['akismet_delay_moderation_email'] = true;

					self::log( 'Delaying moderation email for comment from ' . $commentdata['comment_author'] . ' for ' . $delay . ' seconds' );

					$commentdata['comment_meta']['akismet_schedule_email_fallback'] = $delay;
				}
			}
		}

		$commentdata['comment_meta']['akismet_as_submitted'] = $commentdata['comment_as_submitted'];

		if ( isset( $response[0]['x-akismet-error'] ) ) {
			// An error occurred that we anticipated (like a suspended key) and want the user to act on.
			// Send to moderation.
			self::$last_comment_result = '0';
		} elseif ( 'true' == $response[1] ) {
			// akismet_spam_count will be incremented later by comment_is_spam()
			self::$last_comment_result = 'spam';

			$discard = ( isset( $commentdata['akismet_pro_tip'] ) && $commentdata['akismet_pro_tip'] === 'discard' && self::allow_discard() );

			do_action( 'akismet_spam_caught', $discard );

			if ( $discard ) {
				// The spam is obvious, so we're bailing out early.
				// akismet_result_spam() won't be called so bump the counter here
				if ( $incr = apply_filters( 'akismet_spam_count_incr', 1 ) ) {
					update_option( 'akismet_spam_count', get_option( 'akismet_spam_count' ) + $incr );
				}

				if ( 'rest_api' === $context ) {
					return new WP_Error( 'akismet_rest_comment_discarded', __( 'Comment discarded.', 'akismet' ) );
				} elseif ( 'xml-rpc' === $context ) {
					// If this is a pingback that we're pre-checking, the discard behavior is the same as the normal spam response behavior.
					return $commentdata;
				} else {
					// Redirect back to the previous page, or failing that, the post permalink, or failing that, the homepage of the blog.
					$redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ( $post ? get_permalink( $post ) : home_url() );
					wp_safe_redirect( esc_url_raw( $redirect_to ) );
					die();
				}
			} elseif ( 'rest_api' === $context ) {
				// The way the REST API structures its calls, we can set the comment_approved value right away.
				$commentdata['comment_approved'] = 'spam';
			}
		}

		// if the response is neither true nor false, hold the comment for moderation and schedule a recheck
		if ( 'true' != $response[1] && 'false' != $response[1] ) {
			if ( ! current_user_can( 'moderate_comments' ) ) {
				// Comment status should be moderated
				self::$last_comment_result = '0';
			}

			$commentdata['comment_meta']['akismet_delay_moderation_email'] = true;

			if ( ! wp_next_scheduled( 'akismet_schedule_cron_recheck' ) ) {
				wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
				do_action( 'akismet_scheduled_recheck', 'invalid-response-' . $response[1] );
			}
		}

		// Delete old comments daily
		if ( ! wp_next_scheduled( 'akismet_scheduled_delete' ) ) {
			wp_schedule_event( time(), 'daily', 'akismet_scheduled_delete' );
		}

		self::set_last_comment( $commentdata );
		self::fix_scheduled_recheck();

		return $commentdata;
	}

	public static function get_last_comment() {
		return self::$last_comment;
	}

	public static function set_last_comment( $comment ) {
		if ( is_null( $comment ) ) {
			// This never happens in our code.
			self::$last_comment = null;
		} else {
			// We filter it here so that it matches the filtered comment data that we'll have to compare against later.
			// wp_filter_comment expects comment_author_IP
			self::$last_comment = wp_filter_comment(
				array_merge(
					array( 'comment_author_IP' => self::get_ip_address() ),
					$comment
				)
			);
		}
	}

	// this fires on wp_insert_comment.  we can't update comment_meta when auto_check_comment() runs
	// because we don't know the comment ID at that point.
	public static function auto_check_update_meta( $id, $comment ) {
		// wp_insert_comment() might be called in other contexts, so make sure this is the same comment
		// as was checked by auto_check_comment
		if ( is_object( $comment ) && ! empty( self::$last_comment ) && is_array( self::$last_comment ) ) {
			if ( self::matches_last_comment_by_id( $id ) ) {
				// normal result: true or false
				if ( isset( self::$last_comment['akismet_result'] ) && self::$last_comment['akismet_result'] == 'true' ) {
					self::update_comment_history( $comment->comment_ID, '', 'check-spam' );
					if ( $comment->comment_approved != 'spam' ) {
						self::update_comment_history(
							$comment->comment_ID,
							'',
							'status-changed-' . $comment->comment_approved
						);
					}
				} elseif ( isset( self::$last_comment['akismet_result'] ) && self::$last_comment['akismet_result'] == 'false' ) {
					if ( get_comment_meta( $comment->comment_ID, 'akismet_schedule_approval_fallback', true ) ) {
						self::update_comment_history( $comment->comment_ID, '', 'check-ham-pending' );
					} else {
						self::update_comment_history( $comment->comment_ID, '', 'check-ham' );
					}

					// Status could be spam or trash, depending on the WP version and whether this change applies:
					// https://core.trac.wordpress.org/changeset/34726
					if ( $comment->comment_approved == 'spam' || $comment->comment_approved == 'trash' ) {
						if ( function_exists( 'wp_check_comment_disallowed_list' ) ) {
							if ( wp_check_comment_disallowed_list( $comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content, $comment->comment_author_IP, $comment->comment_agent ) ) {
								self::update_comment_history( $comment->comment_ID, '', 'wp-disallowed' );
							} else {
								self::update_comment_history( $comment->comment_ID, '', 'status-changed-' . $comment->comment_approved );
							}
						} else {
							self::update_comment_history( $comment->comment_ID, '', 'status-changed-' . $comment->comment_approved );
						}
					}
				} elseif ( isset( self::$last_comment['akismet_result'] ) && 'skipped' == self::$last_comment['akismet_result'] ) {
					// The comment wasn't sent to Akismet because it matched the disallowed comment keys.
					self::update_comment_history( $comment->comment_ID, '', 'wp-disallowed' );
					self::update_comment_history( $comment->comment_ID, '', 'akismet-skipped-disallowed' );
				} else if ( ! isset( self::$last_comment['akismet_result'] ) ) {
					// Add a generic skipped history item.
					self::update_comment_history( $comment->comment_ID, '', 'akismet-skipped' );
				} else {
					// abnormal result: error
					self::update_comment_history(
						$comment->comment_ID,
						'',
						'check-error',
						array( 'response' => substr( self::$last_comment['akismet_result'], 0, 50 ) )
					);
				}
			}
		}
	}

	/**
	 * After the comment has been inserted, we have access to the comment ID. Now, we can
	 * schedule the fallback moderation/notification emails using the comment ID instead
	 * of relying on a lookup of the GUID in the commentmeta table.
	 *
	 * @param int $id The comment ID.
	 * @param object $comment The comment object.
	 */
	public static function schedule_email_fallback( $id, $comment ) {
		self::log( 'Checking whether to schedule_email_fallback for comment #' . $id );

		// If the moderation/notification emails for this comment were delayed
		$email_delay = get_comment_meta( $id, 'akismet_schedule_email_fallback', true );

		if ( $email_delay ) {
			delete_comment_meta( $id, 'akismet_schedule_email_fallback' );

			wp_schedule_single_event( time() + $email_delay, 'akismet_email_fallback', array( $id ) );

			self::log( 'Scheduled email fallback for ' . ( time() + $email_delay ) . ' for comment #' . $id );
		} else {
			self::log( 'No need to schedule_email_fallback for comment #' . $id );
		}
	}

	/**
	 * Send out the notification emails if they were previously delayed while waiting
	 * for a recheck or webhook.
	 *
	 * @param int $comment_ID The comment ID.
	 */
	public static function email_fallback( $comment_id ) {
		self::log( 'In email fallback for comment #' . $comment_id );

		if ( get_comment_meta( $comment_id, 'akismet_delayed_moderation_email', true ) ) {
			self::log( 'Triggering notification emails for comment #' . $comment_id . '. They will be sent if comment is not spam.' );

			delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
			wp_new_comment_notify_moderator( $comment_id );
			wp_new_comment_notify_postauthor( $comment_id );
		} else {
			self::log( 'No need to send fallback email for comment #' . $comment_id );
		}

		delete_comment_meta( $comment_id, 'akismet_delay_moderation_email' );
	}

	/**
	 * After the comment has been inserted, we have access to the comment ID. Now, we can
	 * schedule the fallback moderation/notification emails using the comment ID instead
	 * of relying on a lookup of the GUID in the commentmeta table.
	 *
	 * @param int $id The comment ID.
	 * @param object $comment The comment object.
	 */
	public static function schedule_approval_fallback( $id, $comment ) {
		self::log( 'Checking whether to schedule_approval_fallback for comment #' . $id );

		// If the moderation/notification emails for this comment were delayed
		$approval_delay = get_comment_meta( $id, 'akismet_schedule_approval_fallback', true );

		if ( $approval_delay ) {
			delete_comment_meta( $id, 'akismet_schedule_approval_fallback' );

			wp_schedule_single_event( time() + $approval_delay, 'akismet_approval_fallback', array( $id ) );

			self::log( 'Scheduled approval fallback for ' . ( time() + $approval_delay ) . ' for comment #' . $id );
		} else {
			self::log( 'No need to schedule_approval_fallback for comment #' . $id );
		}
	}

	/**
	 * If no other process has approved or spammed this comment since it was put in pending, approve it.
	 *
	 * @param int $comment_ID The comment ID.
	 */
	public static function approval_fallback( $comment_id ) {
		self::log( 'In approval fallback for comment #' . $comment_id );

		if ( wp_get_comment_status( $comment_id ) == 'unapproved' ) {
			if ( self::last_comment_status_change_came_from_akismet( $comment_id ) ) {
				$comment = get_comment( $comment_id );

				if ( ! $comment ) {
					self::log( 'Comment #' . $comment_id . ' no longer exists.' );
				} else if ( check_comment( $comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content, $comment->comment_author_IP, $comment->comment_agent, $comment->comment_type ) ) {
					self::log( 'Approving comment #' . $comment_id );

					wp_set_comment_status( $comment_id, 1 );
				} else {
					self::log( 'Not approving comment #' . $comment_id . ' because it does not pass check_comment()' );
				}

				self::update_comment_history( $comment->comment_ID, '', 'check-ham' );
			} else {
				self::log( 'No need to fallback approve comment #' . $comment_id . ' because it was not last modified by Akismet.' );

				$history = self::get_comment_history( $comment_id );

				if ( ! empty( $history ) ) {
					$most_recent_history_event = $history[0];

					error_log( 'Comment history: ' . print_r( $history, true ) );
				}
			}
		} else {
			self::log( 'No need to fallback approve comment #' . $comment_id . ' because it is not pending.' );
		}
	}

	public static function delete_old_comments() {
		global $wpdb;

		/**
		 * Determines how many comments will be deleted in each batch.
		 *
		 * @param int The default, as defined by AKISMET_DELETE_LIMIT.
		 */
		$delete_limit = apply_filters( 'akismet_delete_comment_limit', defined( 'AKISMET_DELETE_LIMIT' ) ? AKISMET_DELETE_LIMIT : 10000 );
		$delete_limit = max( 1, intval( $delete_limit ) );

		/**
		 * Determines how many days a comment will be left in the Spam queue before being deleted.
		 *
		 * @param int The default number of days.
		 */
		$delete_interval = apply_filters( 'akismet_delete_comment_interval', 15 );
		$delete_interval = max( 1, intval( $delete_interval ) );

		while ( $comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT comment_id FROM {$wpdb->comments} WHERE DATE_SUB(NOW(), INTERVAL %d DAY) > comment_date_gmt AND comment_approved = 'spam' LIMIT %d", $delete_interval, $delete_limit ) ) ) {
			if ( empty( $comment_ids ) ) {
				return;
			}

			$wpdb->queries = array();

			$comments = array();

			foreach ( $comment_ids as $comment_id ) {
				$comments[ $comment_id ] = get_comment( $comment_id );

				do_action( 'delete_comment', $comment_id, $comments[ $comment_id ] );
				do_action( 'akismet_batch_delete_count', __FUNCTION__ );
			}

			// Prepared as strings since comment_id is an unsigned BIGINT, and using %d will constrain the value to the maximum signed BIGINT.
			$format_string = implode( ', ', array_fill( 0, is_countable( $comment_ids ) ? count( $comment_ids ) : 0, '%s' ) );

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->comments} WHERE comment_id IN ( " . $format_string . ' )', $comment_ids ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->commentmeta} WHERE comment_id IN ( " . $format_string . ' )', $comment_ids ) );

			foreach ( $comment_ids as $comment_id ) {
				do_action( 'deleted_comment', $comment_id, $comments[ $comment_id ] );
				unset( $comments[ $comment_id ] );
			}

			clean_comment_cache( $comment_ids );
			do_action( 'akismet_delete_comment_batch', is_countable( $comment_ids ) ? count( $comment_ids ) : 0 );
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand( 1, 5000 ) == 11 ), $wpdb->comments ) ) { // lucky number
			$wpdb->query( "OPTIMIZE TABLE {$wpdb->comments}" );
		}
	}

	public static function delete_old_comments_meta() {
		global $wpdb;

		$interval = apply_filters( 'akismet_delete_commentmeta_interval', 15 );

		// enforce a minimum of 1 day
		$interval = absint( $interval );
		if ( $interval < 1 ) {
			$interval = 1;
		}

		// akismet_as_submitted meta values are large, so expire them
		// after $interval days regardless of the comment status
		while ( $comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT m.comment_id FROM {$wpdb->commentmeta} as m INNER JOIN {$wpdb->comments} as c USING(comment_id) WHERE m.meta_key = 'akismet_as_submitted' AND DATE_SUB(NOW(), INTERVAL %d DAY) > c.comment_date_gmt LIMIT 10000", $interval ) ) ) {
			if ( empty( $comment_ids ) ) {
				return;
			}

			$wpdb->queries = array();

			foreach ( $comment_ids as $comment_id ) {
				delete_comment_meta( $comment_id, 'akismet_as_submitted' );
				do_action( 'akismet_batch_delete_count', __FUNCTION__ );
			}

			do_action( 'akismet_delete_commentmeta_batch', is_countable( $comment_ids ) ? count( $comment_ids ) : 0 );
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand( 1, 5000 ) == 11 ), $wpdb->commentmeta ) ) { // lucky number
			$wpdb->query( "OPTIMIZE TABLE {$wpdb->commentmeta}" );
		}
	}

	// Clear out comments meta that no longer have corresponding comments in the database
	public static function delete_orphaned_commentmeta() {
		global $wpdb;

		$last_meta_id  = 0;
		$start_time    = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime( true );
		$max_exec_time = max( ini_get( 'max_execution_time' ) - 5, 3 );

		while ( $commentmeta_results = $wpdb->get_results( $wpdb->prepare( "SELECT m.meta_id, m.comment_id, m.meta_key FROM {$wpdb->commentmeta} as m LEFT JOIN {$wpdb->comments} as c USING(comment_id) WHERE c.comment_id IS NULL AND m.meta_id > %d ORDER BY m.meta_id LIMIT 1000", $last_meta_id ) ) ) {
			if ( empty( $commentmeta_results ) ) {
				return;
			}

			$wpdb->queries = array();

			$commentmeta_deleted = 0;

			foreach ( $commentmeta_results as $commentmeta ) {
				if ( 'akismet_' == substr( $commentmeta->meta_key, 0, 8 ) ) {
					delete_comment_meta( $commentmeta->comment_id, $commentmeta->meta_key );
					do_action( 'akismet_batch_delete_count', __FUNCTION__ );
					++$commentmeta_deleted;
				}

				$last_meta_id = $commentmeta->meta_id;
			}

			do_action( 'akismet_delete_commentmeta_batch', $commentmeta_deleted );

			// If we're getting close to max_execution_time, quit for this round.
			if ( microtime( true ) - $start_time > $max_exec_time ) {
				return;
			}
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand( 1, 5000 ) == 11 ), $wpdb->commentmeta ) ) { // lucky number
			$wpdb->query( "OPTIMIZE TABLE {$wpdb->commentmeta}" );
		}
	}

	// how many approved comments does this author have?
	public static function get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url ) {
		global $wpdb;

		/**
		 * Which comment types should be ignored when counting a user's approved comments?
		 *
		 * Some plugins add entries to the comments table that are not actual
		 * comments that could have been checked by Akismet. Allow these comments
		 * to be excluded from the "approved comment count" query in order to
		 * avoid artificially inflating the approved comment count.
		 *
		 * @param array $comment_types An array of comment types that won't be considered
		 *                             when counting a user's approved comments.
		 *
		 * @since 4.2.2
		 */
		$excluded_comment_types = apply_filters( 'akismet_excluded_comment_types', array() );

		$comment_type_where = '';

		if ( is_array( $excluded_comment_types ) && ! empty( $excluded_comment_types ) ) {
			$excluded_comment_types = array_unique( $excluded_comment_types );

			foreach ( $excluded_comment_types as $excluded_comment_type ) {
				$comment_type_where .= $wpdb->prepare( ' AND comment_type <> %s ', $excluded_comment_type );
			}
		}

		if ( ! empty( $user_id ) ) {
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d AND comment_approved = 1" . $comment_type_where, $user_id ) );
		}

		if ( ! empty( $comment_author_email ) ) {
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_author_email = %s AND comment_author = %s AND comment_author_url = %s AND comment_approved = 1" . $comment_type_where, $comment_author_email, $comment_author, $comment_author_url ) );
		}

		return 0;
	}

	/**
	 * Get the full comment history for a given comment, as an array in reverse chronological order.
	 * Each entry will have an 'event', a 'time', and possible a 'message' member (if the entry is old enough).
	 * Some entries will also have a 'user' or 'meta' member.
	 *
	 * @param int $comment_id The relevant comment ID.
	 * @return array|bool An array of history events, or false if there is no history.
	 */
	public static function get_comment_history( $comment_id ) {
		$history = get_comment_meta( $comment_id, 'akismet_history', false );
		if ( empty( $history ) || empty( $history[0] ) ) {
			return false;
		}

		/*
		// To see all variants when testing.
		$history[] = array( 'time' => 445856401, 'message' => 'Old versions of Akismet stored the message as a literal string in the commentmeta.', 'event' => null );
		$history[] = array( 'time' => 445856402, 'event' => 'recheck-spam' );
		$history[] = array( 'time' => 445856403, 'event' => 'check-spam' );
		$history[] = array( 'time' => 445856404, 'event' => 'recheck-ham' );
		$history[] = array( 'time' => 445856405, 'event' => 'check-ham' );
		$history[] = array( 'time' => 445856405, 'event' => 'check-ham-pending' );
		$history[] = array( 'time' => 445856406, 'event' => 'wp-blacklisted' );
		$history[] = array( 'time' => 445856406, 'event' => 'wp-disallowed' );
		$history[] = array( 'time' => 445856407, 'event' => 'report-spam' );
		$history[] = array( 'time' => 445856408, 'event' => 'report-spam', 'user' => 'sam' );
		$history[] = array( 'message' => 'sam reported this comment as spam (hardcoded message).', 'time' => 445856400, 'event' => 'report-spam', 'user' => 'sam' );
		$history[] = array( 'time' => 445856409, 'event' => 'report-ham', 'user' => 'sam' );
		$history[] = array( 'message' => 'sam reported this comment as ham (hardcoded message).', 'time' => 445856400, 'event' => 'report-ham', 'user' => 'sam' ); //
		$history[] = array( 'time' => 445856410, 'event' => 'cron-retry-spam' );
		$history[] = array( 'time' => 445856411, 'event' => 'cron-retry-ham' );
		$history[] = array( 'time' => 445856412, 'event' => 'check-error' ); //
		$history[] = array( 'time' => 445856413, 'event' => 'check-error', 'meta' => array( 'response' => 'The server was taking a nap.' ) );
		$history[] = array( 'time' => 445856414, 'event' => 'recheck-error' ); // Should not generate a message.
		$history[] = array( 'time' => 445856415, 'event' => 'recheck-error', 'meta' => array( 'response' => 'The server was taking a nap.' ) );
		$history[] = array( 'time' => 445856416, 'event' => 'status-changedtrash' );
		$history[] = array( 'time' => 445856417, 'event' => 'status-changedspam' );
		$history[] = array( 'time' => 445856418, 'event' => 'status-changedhold' );
		$history[] = array( 'time' => 445856419, 'event' => 'status-changedapprove' );
		$history[] = array( 'time' => 445856420, 'event' => 'status-changed-trash' );
		$history[] = array( 'time' => 445856421, 'event' => 'status-changed-spam' );
		$history[] = array( 'time' => 445856422, 'event' => 'status-changed-hold' );
		$history[] = array( 'time' => 445856423, 'event' => 'status-changed-approve' );
		$history[] = array( 'time' => 445856424, 'event' => 'status-trash', 'user' => 'sam' );
		$history[] = array( 'time' => 445856425, 'event' => 'status-spam', 'user' => 'sam' );
		$history[] = array( 'time' => 445856426, 'event' => 'status-hold', 'user' => 'sam' );
		$history[] = array( 'time' => 445856427, 'event' => 'status-approve', 'user' => 'sam' );
		$history[] = array( 'time' => 445856427, 'event' => 'webhook-spam' );
		$history[] = array( 'time' => 445856427, 'event' => 'webhook-ham' );
		$history[] = array( 'time' => 445856427, 'event' => 'webhook-spam-noaction' );
		$history[] = array( 'time' => 445856427, 'event' => 'webhook-ham-noaction' );
		*/

		usort( $history, array( 'Akismet', '_cmp_time' ) );
		return $history;
	}

	/**
	 * Log an event for a given comment, storing it in comment_meta.
	 *
	 * @param int    $comment_id The ID of the relevant comment.
	 * @param string $message The string description of the event. No longer used.
	 * @param string $event The event code.
	 * @param array  $meta Metadata about the history entry. e.g., the user that reported or changed the status of a given comment.
	 */
	public static function update_comment_history( $comment_id, $message, $event = null, $meta = null ) {
		global $current_user;

		$user = '';

		$event = array(
			'time'  => self::_get_microtime(),
			'event' => $event,
		);

		if ( is_object( $current_user ) && isset( $current_user->user_login ) ) {
			$event['user'] = $current_user->user_login;
		}

		if ( ! empty( $meta ) ) {
			$event['meta'] = $meta;
		}

		// $unique = false so as to allow multiple values per comment
		$r = add_comment_meta( $comment_id, 'akismet_history', $event, false );
	}

	public static function check_db_comment( $id, $recheck_reason = 'recheck_queue' ) {
		global $wpdb;

		if ( ! self::get_api_key() ) {
			return new WP_Error( 'akismet-not-configured', __( 'Akismet is not configured. Please enter an API key.', 'akismet' ) );
		}

		$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $id ), ARRAY_A );

		if ( ! $c ) {
			return new WP_Error( 'invalid-comment-id', __( 'Comment not found.', 'akismet' ) );
		}

		$c['user_ip']        = $c['comment_author_IP'];
		$c['user_agent']     = $c['comment_agent'];
		$c['referrer']       = '';
		$c['blog']           = get_option( 'home' );
		$c['blog_lang']      = get_locale();
		$c['blog_charset']   = get_option( 'blog_charset' );
		$c['permalink']      = get_permalink( $c['comment_post_ID'] );
		$c['recheck_reason'] = $recheck_reason;

		$c['user_role'] = '';
		if ( ! empty( $c['user_ID'] ) ) {
			$c['user_role'] = self::get_user_roles( $c['user_ID'] );
		}

		if ( self::is_test_mode() ) {
			$c['is_test'] = 'true';
		}

		$c = apply_filters( 'akismet_request_args', $c, 'comment-check' );

		$response = self::http_post( self::build_query( $c ), 'comment-check' );

		if ( ! empty( $response[1] ) ) {
			return $response[1];
		}

		return false;
	}

	public static function recheck_comment( $id, $recheck_reason = 'recheck_queue' ) {
		add_comment_meta( $id, 'akismet_rechecking', true );

		$api_response = self::check_db_comment( $id, $recheck_reason );

		if ( is_wp_error( $api_response ) ) {
			// Invalid comment ID.
		} elseif ( 'true' === $api_response ) {
			wp_set_comment_status( $id, 'spam' );
			update_comment_meta( $id, 'akismet_result', 'true' );
			delete_comment_meta( $id, 'akismet_error' );
			delete_comment_meta( $id, 'akismet_delay_moderation_email' );
			delete_comment_meta( $id, 'akismet_delayed_moderation_email' );
			delete_comment_meta( $id, 'akismet_schedule_approval_fallback' );
			delete_comment_meta( $id, 'akismet_schedule_email_fallback' );
			self::update_comment_history( $id, '', 'recheck-spam' );
		} elseif ( 'false' === $api_response ) {
			update_comment_meta( $id, 'akismet_result', 'false' );
			delete_comment_meta( $id, 'akismet_error' );
			delete_comment_meta( $id, 'akismet_delay_moderation_email' );
			delete_comment_meta( $id, 'akismet_delayed_moderation_email' );
			delete_comment_meta( $id, 'akismet_schedule_approval_fallback' );
			delete_comment_meta( $id, 'akismet_schedule_email_fallback' );
			self::update_comment_history( $id, '', 'recheck-ham' );
		} else {
			// abnormal result: error
			update_comment_meta( $id, 'akismet_result', 'error' );
			self::update_comment_history(
				$id,
				'',
				'recheck-error',
				array( 'response' => substr( $api_response, 0, 50 ) )
			);
		}

		delete_comment_meta( $id, 'akismet_rechecking' );

		return $api_response;
	}

	public static function transition_comment_status( $new_status, $old_status, $comment ) {

		if ( $new_status == $old_status ) {
			return;
		}

		if ( 'spam' === $new_status || 'spam' === $old_status ) {
			// Clear the cache of the "X comments in your spam queue" count on the dashboard.
			wp_cache_delete( 'akismet_spam_count', 'widget' );
		}

		// we don't need to record a history item for deleted comments
		if ( $new_status == 'delete' ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $comment->comment_post_ID ) && ! current_user_can( 'moderate_comments' ) ) {
			return;
		}

		if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING == true ) {
			return;
		}

		// if this is present, it means the status has been changed by a re-check, not an explicit user action
		if ( get_comment_meta( $comment->comment_ID, 'akismet_rechecking' ) ) {
			return;
		}

		if ( function_exists( 'getallheaders' ) ) {
			$request_headers = getallheaders();

			foreach ( $request_headers as $header => $value ) {
				if ( strtolower( $header ) == 'x-akismet-webhook' ) {
					// This change is due to a webhook request.
					return;
				}
			}
		}

		// Assumption alert:
		// We want to submit comments to Akismet only when a moderator explicitly spams or approves it - not if the status
		// is changed automatically by another plugin.  Unfortunately WordPress doesn't provide an unambiguous way to
		// determine why the transition_comment_status action was triggered.  And there are several different ways by which
		// to spam and unspam comments: bulk actions, ajax, links in moderation emails, the dashboard, and perhaps others.
		// We'll assume that this is an explicit user action if certain POST/GET variables exist.
		if (
			// status=spam: Marking as spam via the REST API or...
			// status=unspam: I'm not sure. Maybe this used to be used instead of status=approved? Or the UI for removing from spam but not approving has been since removed?...
			// status=approved: Unspamming via the REST API (Calypso) or...
			( isset( $_POST['status'] ) && in_array( $_POST['status'], array( 'spam', 'unspam', 'approved' ) ) )
			// spam=1: Clicking "Spam" underneath a comment in wp-admin and allowing the AJAX request to happen.
			|| ( isset( $_POST['spam'] ) && (int) $_POST['spam'] == 1 )
			// unspam=1: Clicking "Not Spam" underneath a comment in wp-admin and allowing the AJAX request to happen. Or, clicking "Undo" after marking something as spam.
			|| ( isset( $_POST['unspam'] ) && (int) $_POST['unspam'] == 1 )
			// comment_status=spam/unspam: It's unclear where this is happening.
			|| ( isset( $_POST['comment_status'] ) && in_array( $_POST['comment_status'], array( 'spam', 'unspam' ) ) )
			// action=spam: Choosing "Mark as Spam" from the Bulk Actions dropdown in wp-admin (or the "Spam it" link in notification emails).
			// action=unspam: Choosing "Not Spam" from the Bulk Actions dropdown in wp-admin.
			// action=spamcomment: Following the "Spam" link below a comment in wp-admin (not allowing AJAX request to happen).
			// action=unspamcomment: Following the "Not Spam" link below a comment in wp-admin (not allowing AJAX request to happen).
			|| ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'spam', 'unspam', 'spamcomment', 'unspamcomment' ) ) )
			// action=editedcomment: Editing a comment via wp-admin (and possibly changing its status).
			|| ( isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'editedcomment' ) ) )
			// for=jetpack: Moderation via the WordPress app, Calypso, anything powered by the Jetpack connection.
			|| ( isset( $_GET['for'] ) && ( 'jetpack' == $_GET['for'] ) && ( ! defined( 'IS_WPCOM' ) || ! IS_WPCOM ) )
			// Certain WordPress.com API requests
			|| ( defined( 'REST_API_REQUEST' ) && REST_API_REQUEST )
			// WordPress.org REST API requests
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		) {
			if ( $new_status == 'spam' && ( $old_status == 'approved' || $old_status == 'unapproved' || ! $old_status ) ) {
				return self::submit_spam_comment( $comment->comment_ID );
			} elseif ( $old_status == 'spam' && ( $new_status == 'approved' || $new_status == 'unapproved' ) ) {
				return self::submit_nonspam_comment( $comment->comment_ID );
			}
		}

		self::update_comment_history( $comment->comment_ID, '', 'status-' . $new_status );
	}

	public static function submit_spam_comment( $comment_id ) {
		global $wpdb, $current_user, $current_site;

		$comment_id = (int) $comment_id;

		$comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $comment_id ), ARRAY_A );

		if ( ! $comment ) {
			// it was deleted
			return;
		}

		if ( 'spam' != $comment['comment_approved'] ) {
			return;
		}

		self::update_comment_history( $comment_id, '', 'report-spam' );

		// If the user hasn't configured Akismet, there's nothing else to do at this point.
		if ( ! self::get_api_key() ) {
			return;
		}

		// use the original version stored in comment_meta if available
		$as_submitted = self::sanitize_comment_as_submitted( get_comment_meta( $comment_id, 'akismet_as_submitted', true ) );

		if ( $as_submitted && is_array( $as_submitted ) && isset( $as_submitted['comment_content'] ) ) {
			$comment = array_merge( $comment, $as_submitted );
		}

		$comment['blog']         = get_option( 'home' );
		$comment['blog_lang']    = get_locale();
		$comment['blog_charset'] = get_option( 'blog_charset' );
		$comment['permalink']    = get_permalink( $comment['comment_post_ID'] );

		if ( is_object( $current_user ) ) {
			$comment['reporter'] = $current_user->user_login;
		}

		if ( is_object( $current_site ) ) {
			$comment['site_domain'] = $current_site->domain;
		}

		$comment['user_role'] = '';
		if ( ! empty( $comment['user_ID'] ) ) {
			$comment['user_role'] = self::get_user_roles( $comment['user_ID'] );
		}

		if ( self::is_test_mode() ) {
			$comment['is_test'] = 'true';
		}

		$post = get_post( $comment['comment_post_ID'] );

		if ( ! is_null( $post ) ) {
			$comment['comment_post_modified_gmt'] = $post->post_modified_gmt;
		}

		$comment['comment_check_response'] = self::last_comment_check_response( $comment_id );

		$comment = apply_filters( 'akismet_request_args', $comment, 'submit-spam' );

		$response = self::http_post( self::build_query( $comment ), 'submit-spam' );

		update_comment_meta( $comment_id, 'akismet_user_result', 'true' );

		if ( $comment['reporter'] ) {
			update_comment_meta( $comment_id, 'akismet_user', $comment['reporter'] );
		}

		do_action( 'akismet_submit_spam_comment', $comment_id, $response[1] );
	}

	public static function submit_nonspam_comment( $comment_id ) {
		global $wpdb, $current_user, $current_site;

		$comment_id = (int) $comment_id;

		$comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $comment_id ), ARRAY_A );

		if ( ! $comment ) {
			// it was deleted
			return;
		}

		self::update_comment_history( $comment_id, '', 'report-ham' );

		// If the user hasn't configured Akismet, there's nothing else to do at this point.
		if ( ! self::get_api_key() ) {
			return;
		}

		// use the original version stored in comment_meta if available
		$as_submitted = self::sanitize_comment_as_submitted( get_comment_meta( $comment_id, 'akismet_as_submitted', true ) );

		if ( $as_submitted && is_array( $as_submitted ) && isset( $as_submitted['comment_content'] ) ) {
			$comment = array_merge( $comment, $as_submitted );
		}

		$comment['blog']         = get_option( 'home' );
		$comment['blog_lang']    = get_locale();
		$comment['blog_charset'] = get_option( 'blog_charset' );
		$comment['permalink']    = get_permalink( $comment['comment_post_ID'] );
		$comment['user_role']    = '';

		if ( is_object( $current_user ) ) {
			$comment['reporter'] = $current_user->user_login;
		}

		if ( is_object( $current_site ) ) {
			$comment['site_domain'] = $current_site->domain;
		}

		if ( ! empty( $comment['user_ID'] ) ) {
			$comment['user_role'] = self::get_user_roles( $comment['user_ID'] );
		}

		if ( self::is_test_mode() ) {
			$comment['is_test'] = 'true';
		}

		$post = get_post( $comment['comment_post_ID'] );

		if ( ! is_null( $post ) ) {
			$comment['comment_post_modified_gmt'] = $post->post_modified_gmt;
		}

		$comment['comment_check_response'] = self::last_comment_check_response( $comment_id );

		$comment = apply_filters( 'akismet_request_args', $comment, 'submit-ham' );

		$response = self::http_post( self::build_query( $comment ), 'submit-ham' );

		update_comment_meta( $comment_id, 'akismet_user_result', 'false' );

		if ( $comment['reporter'] ) {
			update_comment_meta( $comment_id, 'akismet_user', $comment['reporter'] );
		}

		do_action( 'akismet_submit_nonspam_comment', $comment_id, $response[1] );
	}

	public static function cron_recheck() {
		global $wpdb;

		$api_key = self::get_api_key();

		$status = self::verify_key( $api_key );
		if ( get_option( 'akismet_alert_code' ) || $status == 'invalid' ) {
			// since there is currently a problem with the key, reschedule a check for 6 hours hence
			wp_schedule_single_event( time() + 21600, 'akismet_schedule_cron_recheck' );
			do_action( 'akismet_scheduled_recheck', 'key-problem-' . get_option( 'akismet_alert_code' ) . '-' . $status );
			return false;
		}

		delete_option( 'akismet_available_servers' );

		$comment_errors = $wpdb->get_col( "SELECT comment_id FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'	LIMIT 100" );

		foreach ( (array) $comment_errors as $comment_id ) {
			// if the comment no longer exists, or is too old, remove the meta entry from the queue to avoid getting stuck
			$comment = get_comment( $comment_id );

			if (
				! $comment // Comment has been deleted
				|| strtotime( $comment->comment_date_gmt ) < strtotime( '-15 days' ) // Comment is too old.
				|| $comment->comment_approved !== '0' // Comment is no longer in the Pending queue
				) {
				delete_comment_meta( $comment_id, 'akismet_error' );
				delete_comment_meta( $comment_id, 'akismet_delay_moderation_email' );
				delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
				delete_comment_meta( $comment_id, 'akismet_schedule_approval_fallback' );
				delete_comment_meta( $comment_id, 'akismet_schedule_email_fallback' );
				continue;
			}

			add_comment_meta( $comment_id, 'akismet_rechecking', true );
			$status = self::check_db_comment( $comment_id, 'retry' );

			$event = '';
			if ( $status == 'true' ) {
				$event = 'cron-retry-spam';
			} elseif ( $status == 'false' ) {
				$event = 'cron-retry-ham';
			}

			// If we got back a legit response then update the comment history
			// other wise just bail now and try again later.  No point in
			// re-trying all the comments once we hit one failure.
			if ( ! empty( $event ) ) {
				delete_comment_meta( $comment_id, 'akismet_error' );
				self::update_comment_history( $comment_id, '', $event );
				update_comment_meta( $comment_id, 'akismet_result', $status );
				// make sure the comment status is still pending.  if it isn't, that means the user has already moved it elsewhere.
				$comment = get_comment( $comment_id );
				if ( $comment && 'unapproved' == wp_get_comment_status( $comment_id ) ) {
					if ( $status == 'true' ) {
						wp_spam_comment( $comment_id );
					} elseif ( $status == 'false' ) {
						// comment is good, but it's still in the pending queue.  depending on the moderation settings
						// we may need to change it to approved.
						if ( check_comment( $comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content, $comment->comment_author_IP, $comment->comment_agent, $comment->comment_type ) ) {
							wp_set_comment_status( $comment_id, 1 );
						} elseif ( get_comment_meta( $comment_id, 'akismet_delayed_moderation_email', true ) ) {
							wp_new_comment_notify_moderator( $comment_id );
							wp_new_comment_notify_postauthor( $comment_id );
						}
					}
				}

				delete_comment_meta( $comment_id, 'akismet_delay_moderation_email' );
				delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
			} else {
				// If this comment has been pending moderation for longer than MAX_DELAY_BEFORE_MODERATION_EMAIL,
				// send a moderation email now.
				if ( ( intval( gmdate( 'U' ) ) - strtotime( $comment->comment_date_gmt ) ) < self::MAX_DELAY_BEFORE_MODERATION_EMAIL ) {
					delete_comment_meta( $comment_id, 'akismet_delay_moderation_email' );
					delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );

					wp_new_comment_notify_moderator( $comment_id );
					wp_new_comment_notify_postauthor( $comment_id );
				}

				delete_comment_meta( $comment_id, 'akismet_rechecking' );
				wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
				do_action( 'akismet_scheduled_recheck', 'check-db-comment-' . $status );

				return;
			}

			delete_comment_meta( $comment_id, 'akismet_rechecking' );
		}

		$remaining = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'" );

		if ( $remaining && ! wp_next_scheduled( 'akismet_schedule_cron_recheck' ) ) {
			wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
			do_action( 'akismet_scheduled_recheck', 'remaining' );
		}
	}

	public static function fix_scheduled_recheck() {
		$future_check = wp_next_scheduled( 'akismet_schedule_cron_recheck' );
		if ( ! $future_check ) {
			return;
		}

		if ( get_option( 'akismet_alert_code' ) > 0 ) {
			return;
		}

		$check_range = time() + 1200;
		if ( $future_check > $check_range ) {
			wp_clear_scheduled_hook( 'akismet_schedule_cron_recheck' );
			wp_schedule_single_event( time() + 300, 'akismet_schedule_cron_recheck' );
			do_action( 'akismet_scheduled_recheck', 'fix-scheduled-recheck' );
		}
	}

	public static function add_comment_nonce( $post_id ) {
		/**
		 * To disable the Akismet comment nonce, add a filter for the 'akismet_comment_nonce' tag
		 * and return any string value that is not 'true' or '' (empty string).
		 *
		 * Don't return boolean false, because that implies that the 'akismet_comment_nonce' option
		 * has not been set and that Akismet should just choose the default behavior for that
		 * situation.
		 */

		if ( ! self::get_api_key() ) {
			return;
		}

		$akismet_comment_nonce_option = apply_filters( 'akismet_comment_nonce', get_option( 'akismet_comment_nonce' ) );

		if ( $akismet_comment_nonce_option == 'true' || $akismet_comment_nonce_option == '' ) {
			echo '<p style="display: none;">';
			wp_nonce_field( 'akismet_comment_nonce_' . $post_id, 'akismet_comment_nonce', false );
			echo '</p>';
		}
	}

	public static function is_test_mode() {
		return defined( 'AKISMET_TEST_MODE' ) && AKISMET_TEST_MODE;
	}

	public static function allow_discard() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}
		if ( is_user_logged_in() ) {
			return false;
		}

		return ( get_option( 'akismet_strictness' ) === '1' );
	}

	public static function get_ip_address() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
	}

	/**
	 * Using the unique values that we assign, do we consider these two comments
	 * to be the same instance of a comment?
	 *
	 * The only fields that matter in $comment1 and $comment2 are akismet_guid and akismet_skipped_microtime.
	 * We set both of these during the comment-check call, and if the comment has been saved to the DB,
	 * we save them as comment meta and add them back into the comment array before comparing the comments.
	 *
	 * @param mixed $comment1 A comment object or array.
	 * @param mixed $comment2 A comment object or array.
	 * @return bool Whether the two comments should be treated as the same comment.
	 */
	private static function comments_match( $comment1, $comment2 ) {
		$comment1 = (array) $comment1;
		$comment2 = (array) $comment2;

		if ( ! empty( $comment1['akismet_guid'] ) && ! empty( $comment2['akismet_guid'] ) ) {
			// If the comment got sent to the API and got a response, it will have a GUID.

			return ( $comment1['akismet_guid'] == $comment2['akismet_guid'] );
		} else if ( ! empty( $comment1['akismet_skipped_microtime'] ) && ! empty( $comment2['akismet_skipped_microtime'] ) ) {
			// It won't have a GUID if it didn't get sent to the API because it matched the disallowed list,
			// but it should have a microtimestamp to use here for matching against the comment DB entry it matches.
			return ( strval( $comment1['akismet_skipped_microtime'] ) == strval( $comment2['akismet_skipped_microtime'] ) );
		}

		return false;
	}

	/**
	 * Does the supplied comment match the details of the one most recently stored in self::$last_comment?
	 *
	 * @param array $comment
	 * @return bool Whether the comment supplied as an argument is a match for the one we have stored in $last_comment.
	 */
	public static function matches_last_comment( $comment ) {
		if ( ! self::$last_comment ) {
			return false;
		}

		return self::comments_match( $comment, self::$last_comment );
	}

	/**
	 * Because of the order of operations, we don't always know the comment ID of the comment that we're checking,
	 * so we have to be able to match the comment we cached locally with the comment from the DB.
	 *
	 * @param int $comment_id
	 * @return bool Whether the comment represented by $comment_id is a match for the one we have stored in $last_comment.
	 */
	public static function matches_last_comment_by_id( $comment_id ) {
		return self::matches_last_comment( self::get_fields_for_comment_matching( $comment_id ) );
	}

	/**
	 * Given a comment ID, retrieve the values that we use for matching comments together.
	 *
	 * @param int $comment_id
	 * @return array An array containing akismet_guid and akismet_skipped_microtime. Either or both may be falsy, but we hope that at least one is a string.
	 */
	public static function get_fields_for_comment_matching( $comment_id ) {
		return array(
			'akismet_guid' => get_comment_meta( $comment_id, 'akismet_guid', true ),
			'akismet_skipped_microtime' => get_comment_meta( $comment_id, 'akismet_skipped_microtime', true ),
		);
	}

	private static function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}

	private static function get_referer() {
		return isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
	}

	// return a comma-separated list of role names for the given user
	public static function get_user_roles( $user_id ) {
		$comment_user = null;
		$roles        = false;

		if ( ! class_exists( 'WP_User' ) ) {
			return false;
		}

		if ( $user_id > 0 ) {
			$comment_user = new WP_User( $user_id );
			if ( isset( $comment_user->roles ) ) {
				$roles = implode( ',', $comment_user->roles );
			}
		}

		if ( is_multisite() && is_super_admin( $user_id ) ) {
			if ( empty( $roles ) ) {
				$roles = 'super_admin';
			} else {
				$comment_user->roles[] = 'super_admin';
				$roles                 = implode( ',', $comment_user->roles );
			}
		}

		return $roles;
	}

	// filter handler used to return a spam result to pre_comment_approved
	public static function last_comment_status( $approved, $comment ) {
		if ( is_null( self::$last_comment_result ) ) {
			// We didn't have reason to store the result of the last check.
			return $approved;
		}

		// Only do this if it's the correct comment.
		if ( ! self::matches_last_comment( $comment ) ) {
			self::log( "comment_is_spam mismatched comment, returning unaltered $approved" );
			return $approved;
		}

		if ( 'trash' === $approved ) {
			// If the last comment we checked has had its approval set to 'trash',
			// then it failed the comment blacklist check. Let that blacklist override
			// the spam check, since users have the (valid) expectation that when
			// they fill out their blacklists, comments that match it will always
			// end up in the trash.
			return $approved;
		}

		// bump the counter here instead of when the filter is added to reduce the possibility of overcounting
		if ( $incr = apply_filters( 'akismet_spam_count_incr', 1 ) ) {
			update_option( 'akismet_spam_count', get_option( 'akismet_spam_count' ) + $incr );
		}

		return self::$last_comment_result;
	}

	/**
	 * If Akismet is temporarily unreachable, we don't want to "spam" the blogger or post author
	 * with emails for comments that will be automatically cleared or spammed on the next retry.
	 *
	 * @param bool $maybe_notify Whether the notification email will be sent.
	 * @param int   $comment_id The ID of the relevant comment.
	 * @return bool Whether the notification email should still be sent.
	 */
	public static function disable_emails_if_unreachable( $maybe_notify, $comment_id ) {
		if ( $maybe_notify ) {
			if ( get_comment_meta( $comment_id, 'akismet_delay_moderation_email', true ) ) {
				self::log( 'Disabling notification email for comment #' . $comment_id );

				update_comment_meta( $comment_id, 'akismet_delayed_moderation_email', true );
				delete_comment_meta( $comment_id, 'akismet_delay_moderation_email' );

				// If we want to prevent the email from sending another time, we'll have to reset
				// the akismet_delay_moderation_email commentmeta.

				return false;
			}
		}

		return $maybe_notify;
	}

	public static function _cmp_time( $a, $b ) {
		return $a['time'] > $b['time'] ? -1 : 1;
	}

	public static function _get_microtime() {
		$mtime = explode( ' ', microtime() );
		return $mtime[1] + $mtime[0];
	}

	/**
	 * Make a POST request to the Akismet API.
	 *
	 * @param string $request The body of the request.
	 * @param string $path The path for the request.
	 * @param string $ip The specific IP address to hit.
	 * @return array A two-member array consisting of the headers and the response body, both empty in the case of a failure.
	 */
	public static function http_post( $request, $path, $ip = null ) {

		$akismet_ua = sprintf( 'WordPress/%s | Akismet/%s', $GLOBALS['wp_version'], constant( 'AKISMET_VERSION' ) );
		$akismet_ua = apply_filters( 'akismet_ua', $akismet_ua );

		$host    = self::API_HOST;
		$api_key = self::get_api_key();

		if ( $api_key ) {
			$request = add_query_arg( 'api_key', $api_key, $request );
		}

		$http_host = $host;
		// use a specific IP if provided
		// needed by Akismet_Admin::check_server_connectivity()
		if ( $ip && long2ip( ip2long( $ip ) ) ) {
			$http_host = $ip;
		}

		$http_args = array(
			'body'        => $request,
			'headers'     => array(
				'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
				'Host'         => $host,
				'User-Agent'   => $akismet_ua,
			),
			'httpversion' => '1.0',
			'timeout'     => 15,
		);

		$akismet_url = $http_akismet_url = "http://{$http_host}/1.1/{$path}";

		/**
		 * Try SSL first; if that fails, try without it and don't try it again for a while.
		 */

		$ssl = $ssl_failed = false;

		// Check if SSL requests were disabled fewer than X hours ago.
		$ssl_disabled = get_option( 'akismet_ssl_disabled' );

		if ( $ssl_disabled && $ssl_disabled < ( time() - 60 * 60 * 24 ) ) { // 24 hours
			$ssl_disabled = false;
			delete_option( 'akismet_ssl_disabled' );
		} elseif ( $ssl_disabled ) {
			do_action( 'akismet_ssl_disabled' );
		}

		if ( ! $ssl_disabled && ( $ssl = wp_http_supports( array( 'ssl' ) ) ) ) {
			$akismet_url = set_url_scheme( $akismet_url, 'https' );

			do_action( 'akismet_https_request_pre' );
		}

		$response = wp_remote_post( $akismet_url, $http_args );

		self::log( compact( 'akismet_url', 'http_args', 'response' ) );

		if ( $ssl && is_wp_error( $response ) ) {
			do_action( 'akismet_https_request_failure', $response );

			// Intermittent connection problems may cause the first HTTPS
			// request to fail and subsequent HTTP requests to succeed randomly.
			// Retry the HTTPS request once before disabling SSL for a time.
			$response = wp_remote_post( $akismet_url, $http_args );

			self::log( compact( 'akismet_url', 'http_args', 'response' ) );

			if ( is_wp_error( $response ) ) {
				$ssl_failed = true;

				do_action( 'akismet_https_request_failure', $response );

				do_action( 'akismet_http_request_pre' );

				// Try the request again without SSL.
				$response = wp_remote_post( $http_akismet_url, $http_args );

				self::log( compact( 'http_akismet_url', 'http_args', 'response' ) );
			}
		}

		if ( is_wp_error( $response ) ) {
			do_action( 'akismet_request_failure', $response );

			return array( '', '' );
		}

		if ( $ssl_failed ) {
			// The request failed when using SSL but succeeded without it. Disable SSL for future requests.
			update_option( 'akismet_ssl_disabled', time() );

			do_action( 'akismet_https_disabled' );
		}

		$simplified_response = array( $response['headers'], $response['body'] );

		$alert_code_check_paths = array(
			'verify-key',
			'comment-check',
			'get-stats',
		);

		if ( in_array( $path, $alert_code_check_paths ) ) {
			self::update_alert( $simplified_response );
		}

		return $simplified_response;
	}

	// given a response from an API call like check_key_status(), update the alert code options if an alert is present.
	public static function update_alert( $response ) {
		$alert_option_prefix = 'akismet_alert_';
		$alert_header_prefix = 'x-akismet-alert-';
		$alert_header_names  = array(
			'code',
			'msg',
			'api-calls',
			'usage-limit',
			'upgrade-plan',
			'upgrade-url',
			'upgrade-type',
			'upgrade-via-support',
		);

		foreach ( $alert_header_names as $alert_header_name ) {
			$value = null;
			if ( isset( $response[0][ $alert_header_prefix . $alert_header_name ] ) ) {
				$value = $response[0][ $alert_header_prefix . $alert_header_name ];
			}

			$option_name = $alert_option_prefix . str_replace( '-', '_', $alert_header_name );
			if ( $value != get_option( $option_name ) ) {
				if ( ! $value ) {
					delete_option( $option_name );
				} else {
					update_option( $option_name, $value );
				}
			}
		}
	}

	/**
	 * Mark akismet-frontend.js as deferred. Because nothing depends on it, it can run at any time
	 * after it's loaded, and the browser won't have to wait for it to load to continue
	 * parsing the rest of the page.
	 */
	public static function set_form_js_async( $tag, $handle, $src ) {
		if ( 'akismet-frontend' !== $handle ) {
			return $tag;
		}

		return preg_replace( '/^<script /i', '<script defer ', $tag );
	}

	public static function get_akismet_form_fields() {
		$fields = '';

		$prefix = 'ak_';

		// Contact Form 7 uses _wpcf7 as a prefix to know which fields to exclude from comment_content.
		if ( 'wpcf7_form_elements' === current_filter() ) {
			$prefix = '_wpcf7_ak_';
		}

		$fields .= '<p style="display: none !important;" class="akismet-fields-container" data-prefix="' . esc_attr( $prefix ) . '">';
		$fields .= '<label>&#916;<textarea name="' . $prefix . 'hp_textarea" cols="45" rows="8" maxlength="100"></textarea></label>';

		if ( ! function_exists( 'amp_is_request' ) || ! amp_is_request() ) {
			// Keep track of how many ak_js fields are in this page so that we don't re-use
			// the same ID.
			static $field_count = 0;

			++$field_count;

			$fields .= '<input type="hidden" id="ak_js_' . $field_count . '" name="' . $prefix . 'js" value="' . mt_rand( 0, 250 ) . '"/>';
			$fields .= '<script>document.getElementById( "ak_js_' . $field_count . '" ).setAttribute( "value", ( new Date() ).getTime() );</script>';
		}

		$fields .= '</p>';

		return $fields;
	}

	public static function output_custom_form_fields( $post_id ) {
		if ( 'fluentform/form_element_start' === current_filter() && did_action( 'fluentform_form_element_start' ) ) {
			// Already did this via the legacy filter.
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput
		echo self::get_akismet_form_fields();
	}

	public static function inject_custom_form_fields( $html ) {
		$html = str_replace( '</form>', self::get_akismet_form_fields() . '</form>', $html );

		return $html;
	}

	public static function append_custom_form_fields( $html ) {
		$html .= self::get_akismet_form_fields();

		return $html;
	}

	/**
	 * Ensure that any Akismet-added form fields are included in the comment-check call.
	 *
	 * @param array $form
	 * @param array $data Some plugins will supply the POST data via the filter, since they don't
	 *                    read it directly from $_POST.
	 * @return array $form
	 */
	public static function prepare_custom_form_values( $form, $data = null ) {
		if ( 'fluentform/akismet_fields' === current_filter() && did_filter( 'fluentform_akismet_fields' ) ) {
			// Already updated the form fields via the legacy filter.
			return $form;
		}

		if ( is_null( $data ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$data = $_POST;
		}

		$prefix = 'ak_';

		// Contact Form 7 uses _wpcf7 as a prefix to know which fields to exclude from comment_content.
		if ( 'wpcf7_akismet_parameters' === current_filter() ) {
			$prefix = '_wpcf7_ak_';
		}

		foreach ( $data as $key => $val ) {
			if ( 0 === strpos( $key, $prefix ) ) {
				$form[ 'POST_ak_' . substr( $key, strlen( $prefix ) ) ] = $val;
			}
		}

		return $form;
	}

	private static function bail_on_activation( $message, $deactivate = true ) {
		?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
</head>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
		<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$akismet = plugin_basename( AKISMET__PLUGIN_DIR . 'akismet.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $akismet ) {
					$plugins[ $i ] = false;
					$update        = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

	public static function view( $name, array $args = array() ) {
		$args = apply_filters( 'akismet_view_arguments', $args, $name );

		foreach ( $args as $key => $val ) {
			$$key = $val;
		}

		$file = AKISMET__PLUGIN_DIR . 'views/' . basename( $name ) . '.php';

		if ( file_exists( $file ) ) {
			include $file;
		}
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 *
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], AKISMET__MINIMUM_WP_VERSION, '<' ) ) {
			$message = '<strong>' .
				/* translators: 1: Current Akismet version number, 2: Minimum WordPress version number required. */
				sprintf( esc_html__( 'Akismet %1$s requires WordPress %2$s or higher.', 'akismet' ), AKISMET_VERSION, AKISMET__MINIMUM_WP_VERSION ) . '</strong> ' .
				/* translators: 1: WordPress documentation URL, 2: Akismet download URL. */
				sprintf( __( 'Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'akismet' ), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/plugins/akismet' );

			self::bail_on_activation( $message );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
			add_option( 'Activated_Akismet', true );
		}
	}

	/**
	 * Removes all connection options
	 *
	 * @static
	 */
	public static function plugin_deactivation() {
		self::deactivate_key( self::get_api_key() );

		// Remove any scheduled cron jobs.
		$akismet_cron_events = array(
			'akismet_schedule_cron_recheck',
			'akismet_scheduled_delete',
		);

		foreach ( $akismet_cron_events as $akismet_cron_event ) {
			$timestamp = wp_next_scheduled( $akismet_cron_event );

			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $akismet_cron_event );
			}
		}
	}

	/**
	 * Essentially a copy of WP's build_query but one that doesn't expect pre-urlencoded values.
	 *
	 * @param array $args An array of key => value pairs
	 * @return string A string ready for use as a URL query string.
	 */
	public static function build_query( $args ) {
		return _http_build_query( $args, '', '&' );
	}

	/**
	 * Log debugging info to the error log.
	 *
	 * Enabled when WP_DEBUG_LOG is enabled (and WP_DEBUG, since according to
	 * core, "WP_DEBUG_DISPLAY and WP_DEBUG_LOG perform no function unless
	 * WP_DEBUG is true), but can be disabled via the akismet_debug_log filter.
	 *
	 * @param mixed $akismet_debug The data to log.
	 */
	public static function log( $akismet_debug ) {
		if ( apply_filters( 'akismet_debug_log', defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && defined( 'AKISMET_DEBUG' ) && AKISMET_DEBUG ) ) {
			error_log( print_r( compact( 'akismet_debug' ), true ) );
		}
	}

	/**
	 * Check pingbacks for spam before they're saved to the DB.
	 *
	 * @param string           $method The XML-RPC method that was called.
	 * @param array            $args This and the $server arg are marked as optional since plugins might still be
	 *                               calling do_action( 'xmlrpc_action', [...] ) without the arguments that were added in WP 5.7.
	 * @param wp_xmlrpc_server $server
	 */
	public static function pre_check_pingback( $method, $args = array(), $server = null ) {
		if ( $method !== 'pingback.ping' ) {
			return;
		}

		/*
		 * $args looks like this:
		 *
		 * Array
		 * (
		 *     [0] => http://www.example.net/?p=1 // Site that created the pingback.
		 *     [1] => https://www.example.com/?p=2 // Post being pingback'd on this site.
		 * )
		 */

		if ( ! is_null( $server ) && ! empty( $args[1] ) ) {
			$is_multicall    = false;
			$multicall_count = 0;

			if ( 'system.multicall' === $server->message->methodName ) {
				$is_multicall    = true;
				$multicall_count = is_countable( $server->message->params ) ? count( $server->message->params ) : 0;
			}

			$post_id = url_to_postid( $args[1] );

			// If pingbacks aren't open on this post, we'll still check whether this request is part of a potential DDOS,
			// but indicate to the server that pingbacks are indeed closed so we don't include this request in the user's stats,
			// since the user has already done their part by disabling pingbacks.
			$pingbacks_closed = false;

			$post = get_post( $post_id );

			if ( ! $post || ! pings_open( $post ) ) {
				$pingbacks_closed = true;
			}

			$comment = array(
				'comment_author_url'      => $args[0],
				'comment_post_ID'         => $post_id,
				'comment_author'          => '',
				'comment_author_email'    => '',
				'comment_content'         => '',
				'comment_type'            => 'pingback',
				'akismet_pre_check'       => '1',
				'comment_pingback_target' => $args[1],
				'pingbacks_closed'        => $pingbacks_closed ? '1' : '0',
				'is_multicall'            => $is_multicall,
				'multicall_count'         => $multicall_count,
			);

			$comment = self::auto_check_comment( $comment, 'xml-rpc' );

			if ( isset( $comment['akismet_result'] ) && 'true' == $comment['akismet_result'] ) {
				// Sad: tightly coupled with the IXR classes. Unfortunately the action provides no context and no way to return anything.
				$server->error( new IXR_Error( 0, 'Invalid discovery target' ) );

				// Also note that if this was part of a multicall, a spam result will prevent the subsequent calls from being executed.
				// This is probably fine, but it raises the bar for what should be acceptable as a false positive.
			}
		}
	}

	/**
	 * Ensure that we are loading expected scalar values from akismet_as_submitted commentmeta.
	 *
	 * @param mixed $meta_value
	 * @return mixed
	 */
	private static function sanitize_comment_as_submitted( $meta_value ) {
		if ( empty( $meta_value ) ) {
			return $meta_value;
		}

		$meta_value = (array) $meta_value;

		foreach ( $meta_value as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				unset( $meta_value[ $key ] );
			} else {
				// These can change, so they're not explicitly listed in comment_as_submitted_allowed_keys.
				if ( strpos( $key, 'POST_ak_' ) === 0 ) {
					continue;
				}

				if ( ! isset( self::$comment_as_submitted_allowed_keys[ $key ] ) ) {
					unset( $meta_value[ $key ] );
				}
			}
		}

		return $meta_value;
	}

	public static function predefined_api_key() {
		if ( defined( 'WPCOM_API_KEY' ) ) {
			return true;
		}

		return apply_filters( 'akismet_predefined_api_key', false );
	}

	/**
	 * Controls the display of a privacy related notice underneath the comment
	 * form using the `akismet_comment_form_privacy_notice` option and filter
	 * respectively.
	 *
	 * Default is to not display the notice, leaving the choice to site admins,
	 * or integrators.
	 */
	public static function display_comment_form_privacy_notice() {
		if ( 'display' !== apply_filters( 'akismet_comment_form_privacy_notice', get_option( 'akismet_comment_form_privacy_notice', 'hide' ) ) ) {
			return;
		}

		echo apply_filters(
			'akismet_comment_form_privacy_notice_markup',
			'<p class="akismet_comment_form_privacy_notice">' .
				wp_kses(
					sprintf(
						/* translators: %s: Akismet privacy URL */
						__( 'This site uses Akismet to reduce spam. <a href="%s" target="_blank" rel="nofollow noopener">Learn how your comment data is processed.</a>', 'akismet' ),
						'https://akismet.com/privacy/'
					),
					array(
						'a' => array(
							'href' => array(),
							'target' => array(),
							'rel' => array(),
						),
					)
				) .
			'</p>'
		);
	}

	public static function load_form_js() {
		if (
			! is_admin()
			&& ( ! function_exists( 'amp_is_request' ) || ! amp_is_request() )
			&& self::get_api_key()
			) {
			wp_register_script( 'akismet-frontend', plugin_dir_url( __FILE__ ) . '_inc/akismet-frontend.js', array(), filemtime( plugin_dir_path( __FILE__ ) . '_inc/akismet-frontend.js' ), true );
			wp_enqueue_script( 'akismet-frontend' );
		}
	}

	/**
	 * Add the form JavaScript when we detect that a supported form shortcode is being parsed.
	 */
	public static function load_form_js_via_filter( $return_value, $tag, $attr, $m ) {
		if ( in_array( $tag, array( 'contact-form', 'gravityform', 'contact-form-7', 'formidable', 'fluentform' ) ) ) {
			self::load_form_js();
		}

		return $return_value;
	}

	/**
	 * Was the last entry in the comment history created by Akismet?
	 *
	 * @param int $comment_id The ID of the comment.
	 * @return bool
	 */
	public static function last_comment_status_change_came_from_akismet( $comment_id ) {
		$history = self::get_comment_history( $comment_id );

		if ( empty( $history ) ) {
			return false;
		}

		$most_recent_history_event = $history[0];

		if ( ! isset( $most_recent_history_event['event'] ) ) {
			return false;
		}

		$akismet_history_events = array(
			'check-error',
			'cron-retry-ham',
			'cron-retry-spam',
			'check-ham',
			'check-ham-pending',
			'check-spam',
			'recheck-error',
			'recheck-ham',
			'recheck-spam',
			'webhook-ham',
			'webhook-spam',
		);

		if ( in_array( $most_recent_history_event['event'], $akismet_history_events ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check the comment history to find out what the most recent comment-check
	 * response said about this comment.
	 *
	 * This value is then included in submit-ham and submit-spam requests to allow
	 * us to know whether the comment is actually a missed spam/ham or if it's
	 * just being reclassified after either never being checked or being mistakenly
	 * marked as ham/spam.
	 *
	 * @param int $comment_id The comment ID.
	 * @return string 'true', 'false', or an empty string if we don't have a record
	 *                of comment-check being called.
	 */
	public static function last_comment_check_response( $comment_id ) {
		$history = self::get_comment_history( $comment_id );

		if ( $history ) {
			$history = array_reverse( $history );

			foreach ( $history as $akismet_history_entry ) {
				// We've always been consistent in how history entries are formatted
				// but comment_meta is writable by everyone, so don't assume that all
				// entries contain the expected parts.

				if ( ! is_array( $akismet_history_entry ) ) {
					continue;
				}

				if ( ! isset( $akismet_history_entry['event'] ) ) {
					continue;
				}

				if ( in_array(
					$akismet_history_entry['event'],
					array(
						'recheck-spam',
						'check-spam',
						'cron-retry-spam',
						'webhook-spam',
						'webhook-spam-noaction',
					),
					true
				) ) {
					return 'true';
				} elseif ( in_array(
					$akismet_history_entry['event'],
					array(
						'recheck-ham',
						'check-ham',
						'cron-retry-ham',
						'webhook-ham',
						'webhook-ham-noaction',
					),
					true
				) ) {
					return 'false';
				}
			}
		}

		return '';
	}
}
