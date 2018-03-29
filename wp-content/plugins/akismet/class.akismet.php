<?php

class Akismet {
	const API_HOST = 'rest.akismet.com';
	const API_PORT = 80;
	const MAX_DELAY_BEFORE_MODERATION_EMAIL = 86400; // One day in seconds

	private static $last_comment = '';
	private static $initiated = false;
	private static $prevent_moderation_email_for_these_comments = array();
	private static $last_comment_result = null;
	private static $comment_as_submitted_allowed_keys = array( 'blog' => '', 'blog_charset' => '', 'blog_lang' => '', 'blog_ua' => '', 'comment_agent' => '', 'comment_author' => '', 'comment_author_IP' => '', 'comment_author_email' => '', 'comment_author_url' => '', 'comment_content' => '', 'comment_date_gmt' => '', 'comment_tags' => '', 'comment_type' => '', 'guid' => '', 'is_test' => '', 'permalink' => '', 'reporter' => '', 'site_domain' => '', 'submit_referer' => '', 'submit_uri' => '', 'user_ID' => '', 'user_agent' => '', 'user_id' => '', 'user_ip' => '' );
	private static $is_rest_api_call = false;
	
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
		add_filter( 'preprocess_comment', array( 'Akismet', 'auto_check_comment' ), 1 );
		add_filter( 'rest_pre_insert_comment', array( 'Akismet', 'rest_auto_check_comment' ), 1 );

		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments_meta' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_orphaned_commentmeta' ) );
		add_action( 'akismet_schedule_cron_recheck', array( 'Akismet', 'cron_recheck' ) );

		add_action( 'comment_form',  array( 'Akismet',  'add_comment_nonce' ), 1 );

		add_action( 'admin_head-edit-comments.php', array( 'Akismet', 'load_form_js' ) );
		add_action( 'comment_form', array( 'Akismet', 'load_form_js' ) );
		add_action( 'comment_form', array( 'Akismet', 'inject_ak_js' ) );
		add_filter( 'script_loader_tag', array( 'Akismet', 'set_form_js_async' ), 10, 3 );

		add_filter( 'comment_moderation_recipients', array( 'Akismet', 'disable_moderation_emails_if_unreachable' ), 1000, 2 );
		add_filter( 'pre_comment_approved', array( 'Akismet', 'last_comment_status' ), 10, 2 );
		
		add_action( 'transition_comment_status', array( 'Akismet', 'transition_comment_status' ), 10, 3 );

		// Run this early in the pingback call, before doing a remote fetch of the source uri
		add_action( 'xmlrpc_call', array( 'Akismet', 'pre_check_pingback' ) );
		
		// Jetpack compatibility
		add_filter( 'jetpack_options_whitelist', array( 'Akismet', 'add_to_jetpack_options_whitelist' ) );
		add_action( 'update_option_wordpress_api_key', array( 'Akismet', 'updated_option' ), 10, 2 );
	}

	public static function get_api_key() {
		return apply_filters( 'akismet_get_api_key', defined('WPCOM_API_KEY') ? constant('WPCOM_API_KEY') : get_option('wordpress_api_key') );
	}

	public static function check_key_status( $key, $ip = null ) {
		return self::http_post( Akismet::build_query( array( 'key' => $key, 'blog' => get_option( 'home' ) ) ), 'verify-key', $ip );
	}

	public static function verify_key( $key, $ip = null ) {
		$response = self::check_key_status( $key, $ip );

		if ( $response[1] != 'valid' && $response[1] != 'invalid' )
			return 'failed';

		return $response[1];
	}

	public static function deactivate_key( $key ) {
		$response = self::http_post( Akismet::build_query( array( 'key' => $key, 'blog' => get_option( 'home' ) ) ), 'deactivate' );

		if ( $response[1] != 'deactivated' )
			return 'failed';

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
	 * @param mixed  $old_value   The old option value.
	 * @param mixed  $value       The new option value.
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
	
	public static function rest_auto_check_comment( $commentdata ) {
		self::$is_rest_api_call = true;
		
		return self::auto_check_comment( $commentdata );
	}

	public static function auto_check_comment( $commentdata ) {
		self::$last_comment_result = null;

		$comment = $commentdata;

		$comment['user_ip']      = self::get_ip_address();
		$comment['user_agent']   = self::get_user_agent();
		$comment['referrer']     = self::get_referer();
		$comment['blog']         = get_option( 'home' );
		$comment['blog_lang']    = get_locale();
		$comment['blog_charset'] = get_option('blog_charset');
		$comment['permalink']    = get_permalink( $comment['comment_post_ID'] );

		if ( ! empty( $comment['user_ID'] ) ) {
			$comment['user_role'] = Akismet::get_user_roles( $comment['user_ID'] );
		}

		/** See filter documentation in init_hooks(). */
		$akismet_nonce_option = apply_filters( 'akismet_comment_nonce', get_option( 'akismet_comment_nonce' ) );
		$comment['akismet_comment_nonce'] = 'inactive';
		if ( $akismet_nonce_option == 'true' || $akismet_nonce_option == '' ) {
			$comment['akismet_comment_nonce'] = 'failed';
			if ( isset( $_POST['akismet_comment_nonce'] ) && wp_verify_nonce( $_POST['akismet_comment_nonce'], 'akismet_comment_nonce_' . $comment['comment_post_ID'] ) )
				$comment['akismet_comment_nonce'] = 'passed';

			// comment reply in wp-admin
			if ( isset( $_POST['_ajax_nonce-replyto-comment'] ) && check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' ) )
				$comment['akismet_comment_nonce'] = 'passed';

		}

		if ( self::is_test_mode() )
			$comment['is_test'] = 'true';

		foreach( $_POST as $key => $value ) {
			if ( is_string( $value ) )
				$comment["POST_{$key}"] = $value;
		}

		foreach ( $_SERVER as $key => $value ) {
			if ( ! is_string( $value ) ) {
				continue;
			}

			if ( preg_match( "/^HTTP_COOKIE/", $key ) ) {
				continue;
			}

			// Send any potentially useful $_SERVER vars, but avoid sending junk we don't need.
			if ( preg_match( "/^(HTTP_|REMOTE_ADDR|REQUEST_URI|DOCUMENT_URI)/", $key ) ) {
				$comment[ "$key" ] = $value;
			}
		}

		$post = get_post( $comment['comment_post_ID'] );

		if ( ! is_null( $post ) ) {
			// $post can technically be null, although in the past, it's always been an indicator of another plugin interfering.
			$comment[ 'comment_post_modified_gmt' ] = $post->post_modified_gmt;
		}

		$response = self::http_post( Akismet::build_query( $comment ), 'comment-check' );

		do_action( 'akismet_comment_check_response', $response );

		$commentdata['comment_as_submitted'] = array_intersect_key( $comment, self::$comment_as_submitted_allowed_keys );
		$commentdata['akismet_result']       = $response[1];

		if ( isset( $response[0]['x-akismet-pro-tip'] ) )
	        $commentdata['akismet_pro_tip'] = $response[0]['x-akismet-pro-tip'];

		if ( isset( $response[0]['x-akismet-error'] ) ) {
			// An error occurred that we anticipated (like a suspended key) and want the user to act on.
			// Send to moderation.
			self::$last_comment_result = '0';
		}
		else if ( 'true' == $response[1] ) {
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

				if ( self::$is_rest_api_call ) {
					return new WP_Error( 'akismet_rest_comment_discarded', __( 'Comment discarded.', 'akismet' ) );
				}
				else {
					// Redirect back to the previous page, or failing that, the post permalink, or failing that, the homepage of the blog.
					$redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ( $post ? get_permalink( $post ) : home_url() );
					wp_safe_redirect( esc_url_raw( $redirect_to ) );
					die();
				}
			}
			else if ( self::$is_rest_api_call ) {
				// The way the REST API structures its calls, we can set the comment_approved value right away.
				$commentdata['comment_approved'] = 'spam';
			}
		}
		
		// if the response is neither true nor false, hold the comment for moderation and schedule a recheck
		if ( 'true' != $response[1] && 'false' != $response[1] ) {
			if ( !current_user_can('moderate_comments') ) {
				// Comment status should be moderated
				self::$last_comment_result = '0';
			}

			if ( ! wp_next_scheduled( 'akismet_schedule_cron_recheck' ) ) {
				wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
				do_action( 'akismet_scheduled_recheck', 'invalid-response-' . $response[1] );
			}

			self::$prevent_moderation_email_for_these_comments[] = $commentdata;
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
			self::$last_comment = null;
		}
		else {
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
		if ( is_object( $comment ) && !empty( self::$last_comment ) && is_array( self::$last_comment ) ) {
			if ( self::matches_last_comment( $comment ) ) {
					
					load_plugin_textdomain( 'akismet' );
					
					// normal result: true or false
					if ( self::$last_comment['akismet_result'] == 'true' ) {
						update_comment_meta( $comment->comment_ID, 'akismet_result', 'true' );
						self::update_comment_history( $comment->comment_ID, '', 'check-spam' );
						if ( $comment->comment_approved != 'spam' )
							self::update_comment_history(
								$comment->comment_ID,
								'',
								'status-changed-'.$comment->comment_approved
							);
					}
					elseif ( self::$last_comment['akismet_result'] == 'false' ) {
						update_comment_meta( $comment->comment_ID, 'akismet_result', 'false' );
						self::update_comment_history( $comment->comment_ID, '', 'check-ham' );
						// Status could be spam or trash, depending on the WP version and whether this change applies:
						// https://core.trac.wordpress.org/changeset/34726
						if ( $comment->comment_approved == 'spam' || $comment->comment_approved == 'trash' ) {
							if ( wp_blacklist_check($comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content, $comment->comment_author_IP, $comment->comment_agent) )
								self::update_comment_history( $comment->comment_ID, '', 'wp-blacklisted' );
							else
								self::update_comment_history( $comment->comment_ID, '', 'status-changed-'.$comment->comment_approved );
						}
					} // abnormal result: error
					else {
						update_comment_meta( $comment->comment_ID, 'akismet_error', time() );
						self::update_comment_history(
							$comment->comment_ID,
							'',
							'check-error',
							array( 'response' => substr( self::$last_comment['akismet_result'], 0, 50 ) )
						);
					}

					// record the complete original data as submitted for checking
					if ( isset( self::$last_comment['comment_as_submitted'] ) )
						update_comment_meta( $comment->comment_ID, 'akismet_as_submitted', self::$last_comment['comment_as_submitted'] );

					if ( isset( self::$last_comment['akismet_pro_tip'] ) )
						update_comment_meta( $comment->comment_ID, 'akismet_pro_tip', self::$last_comment['akismet_pro_tip'] );
			}
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
			if ( empty( $comment_ids ) )
				return;

			$wpdb->queries = array();

			foreach ( $comment_ids as $comment_id ) {
				do_action( 'delete_comment', $comment_id );
				do_action( 'akismet_batch_delete_count', __FUNCTION__ );
			}

			// Prepared as strings since comment_id is an unsigned BIGINT, and using %d will constrain the value to the maximum signed BIGINT.
			$format_string = implode( ", ", array_fill( 0, count( $comment_ids ), '%s' ) );

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->comments} WHERE comment_id IN ( " . $format_string . " )", $comment_ids ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->commentmeta} WHERE comment_id IN ( " . $format_string . " )", $comment_ids ) );

			clean_comment_cache( $comment_ids );
			do_action( 'akismet_delete_comment_batch', count( $comment_ids ) );
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand(1, 5000) == 11), $wpdb->comments ) ) // lucky number
			$wpdb->query("OPTIMIZE TABLE {$wpdb->comments}");
	}

	public static function delete_old_comments_meta() {
		global $wpdb;

		$interval = apply_filters( 'akismet_delete_commentmeta_interval', 15 );

		# enforce a minimum of 1 day
		$interval = absint( $interval );
		if ( $interval < 1 )
			$interval = 1;

		// akismet_as_submitted meta values are large, so expire them
		// after $interval days regardless of the comment status
		while ( $comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT m.comment_id FROM {$wpdb->commentmeta} as m INNER JOIN {$wpdb->comments} as c USING(comment_id) WHERE m.meta_key = 'akismet_as_submitted' AND DATE_SUB(NOW(), INTERVAL %d DAY) > c.comment_date_gmt LIMIT 10000", $interval ) ) ) {
			if ( empty( $comment_ids ) )
				return;

			$wpdb->queries = array();

			foreach ( $comment_ids as $comment_id ) {
				delete_comment_meta( $comment_id, 'akismet_as_submitted' );
				do_action( 'akismet_batch_delete_count', __FUNCTION__ );
			}

			do_action( 'akismet_delete_commentmeta_batch', count( $comment_ids ) );
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand(1, 5000) == 11), $wpdb->commentmeta ) ) // lucky number
			$wpdb->query("OPTIMIZE TABLE {$wpdb->commentmeta}");
	}

	// Clear out comments meta that no longer have corresponding comments in the database
	public static function delete_orphaned_commentmeta() {
		global $wpdb;

		$last_meta_id = 0;
		$start_time = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime( true );
		$max_exec_time = max( ini_get('max_execution_time') - 5, 3 );

		while ( $commentmeta_results = $wpdb->get_results( $wpdb->prepare( "SELECT m.meta_id, m.comment_id, m.meta_key FROM {$wpdb->commentmeta} as m LEFT JOIN {$wpdb->comments} as c USING(comment_id) WHERE c.comment_id IS NULL AND m.meta_id > %d ORDER BY m.meta_id LIMIT 1000", $last_meta_id ) ) ) {
			if ( empty( $commentmeta_results ) )
				return;

			$wpdb->queries = array();

			$commentmeta_deleted = 0;

			foreach ( $commentmeta_results as $commentmeta ) {
				if ( 'akismet_' == substr( $commentmeta->meta_key, 0, 8 ) ) {
					delete_comment_meta( $commentmeta->comment_id, $commentmeta->meta_key );
					do_action( 'akismet_batch_delete_count', __FUNCTION__ );
					$commentmeta_deleted++;
				}

				$last_meta_id = $commentmeta->meta_id;
			}

			do_action( 'akismet_delete_commentmeta_batch', $commentmeta_deleted );

			// If we're getting close to max_execution_time, quit for this round.
			if ( microtime(true) - $start_time > $max_exec_time )
				return;
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand(1, 5000) == 11), $wpdb->commentmeta ) ) // lucky number
			$wpdb->query("OPTIMIZE TABLE {$wpdb->commentmeta}");
	}

	// how many approved comments does this author have?
	public static function get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url ) {
		global $wpdb;

		if ( !empty( $user_id ) )
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d AND comment_approved = 1", $user_id ) );

		if ( !empty( $comment_author_email ) )
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_author_email = %s AND comment_author = %s AND comment_author_url = %s AND comment_approved = 1", $comment_author_email, $comment_author, $comment_author_url ) );

		return 0;
	}

	// get the full comment history for a given comment, as an array in reverse chronological order
	public static function get_comment_history( $comment_id ) {
		$history = get_comment_meta( $comment_id, 'akismet_history', false );
		usort( $history, array( 'Akismet', '_cmp_time' ) );
		return $history;
	}

	/**
	 * Log an event for a given comment, storing it in comment_meta.
	 *
	 * @param int $comment_id The ID of the relevant comment.
	 * @param string $message The string description of the event. No longer used.
	 * @param string $event The event code.
	 * @param array $meta Metadata about the history entry. e.g., the user that reported or changed the status of a given comment.
	 */
	public static function update_comment_history( $comment_id, $message, $event=null, $meta=null ) {
		global $current_user;

		$user = '';

		$event = array(
			'time'    => self::_get_microtime(),
			'event'   => $event,
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

		$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $id ), ARRAY_A );
		
		if ( ! $c ) {
			return new WP_Error( 'invalid-comment-id', __( 'Comment not found.', 'akismet' ) );
		}

		$c['user_ip']        = $c['comment_author_IP'];
		$c['user_agent']     = $c['comment_agent'];
		$c['referrer']       = '';
		$c['blog']           = get_option( 'home' );
		$c['blog_lang']      = get_locale();
		$c['blog_charset']   = get_option('blog_charset');
		$c['permalink']      = get_permalink($c['comment_post_ID']);
		$c['recheck_reason'] = $recheck_reason;

		$c['user_role'] = '';
		if ( ! empty( $c['user_ID'] ) ) {
			$c['user_role'] = Akismet::get_user_roles( $c['user_ID'] );
		}

		if ( self::is_test_mode() )
			$c['is_test'] = 'true';

		$response = self::http_post( Akismet::build_query( $c ), 'comment-check' );

		if ( ! empty( $response[1] ) ) {
			return $response[1];
		}

		return false;
	}
	
	public static function recheck_comment( $id, $recheck_reason = 'recheck_queue' ) {
		add_comment_meta( $id, 'akismet_rechecking', true );
		
		$api_response = self::check_db_comment( $id, $recheck_reason );

		delete_comment_meta( $id, 'akismet_rechecking' );

		if ( is_wp_error( $api_response ) ) {
			// Invalid comment ID.
		}
		else if ( 'true' === $api_response ) {
			wp_set_comment_status( $id, 'spam' );
			update_comment_meta( $id, 'akismet_result', 'true' );
			delete_comment_meta( $id, 'akismet_error' );
			delete_comment_meta( $id, 'akismet_delayed_moderation_email' );
			Akismet::update_comment_history( $id, '', 'recheck-spam' );
		}
		elseif ( 'false' === $api_response ) {
			update_comment_meta( $id, 'akismet_result', 'false' );
			delete_comment_meta( $id, 'akismet_error' );
			delete_comment_meta( $id, 'akismet_delayed_moderation_email' );
			Akismet::update_comment_history( $id, '', 'recheck-ham' );
		}
		else {
			// abnormal result: error
			update_comment_meta( $id, 'akismet_result', 'error' );
			Akismet::update_comment_history(
				$id,
				'',
				'recheck-error',
				array( 'response' => substr( $api_response, 0, 50 ) )
			);
		}

		return $api_response;
	}

	public static function transition_comment_status( $new_status, $old_status, $comment ) {
		
		if ( $new_status == $old_status )
			return;

		# we don't need to record a history item for deleted comments
		if ( $new_status == 'delete' )
			return;
		
		if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) && !current_user_can( 'moderate_comments' ) )
			return;

		if ( defined('WP_IMPORTING') && WP_IMPORTING == true )
			return;
			
		// if this is present, it means the status has been changed by a re-check, not an explicit user action
		if ( get_comment_meta( $comment->comment_ID, 'akismet_rechecking' ) )
			return;
		
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
			 ( isset( $_POST['status'] ) && in_array( $_POST['status'], array( 'spam', 'unspam', 'approved', ) ) )
			 // spam=1: Clicking "Spam" underneath a comment in wp-admin and allowing the AJAX request to happen.
			 || ( isset( $_POST['spam'] ) && (int) $_POST['spam'] == 1 )
			 // unspam=1: Clicking "Not Spam" underneath a comment in wp-admin and allowing the AJAX request to happen. Or, clicking "Undo" after marking something as spam.
			 || ( isset( $_POST['unspam'] ) && (int) $_POST['unspam'] == 1 )
			 // comment_status=spam/unspam: It's unclear where this is happening.
			 || ( isset( $_POST['comment_status'] )  && in_array( $_POST['comment_status'], array( 'spam', 'unspam' ) ) )
			 // action=spam: Choosing "Mark as Spam" from the Bulk Actions dropdown in wp-admin (or the "Spam it" link in notification emails).
			 // action=unspam: Choosing "Not Spam" from the Bulk Actions dropdown in wp-admin.
			 // action=spamcomment: Following the "Spam" link below a comment in wp-admin (not allowing AJAX request to happen).
			 // action=unspamcomment: Following the "Not Spam" link below a comment in wp-admin (not allowing AJAX request to happen).
			 || ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'spam', 'unspam', 'spamcomment', 'unspamcomment', ) ) )
			 // action=editedcomment: Editing a comment via wp-admin (and possibly changing its status).
			 || ( isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'editedcomment' ) ) )
			 // for=jetpack: Moderation via the WordPress app, Calypso, anything powered by the Jetpack connection.
			 || ( isset( $_GET['for'] ) && ( 'jetpack' == $_GET['for'] ) && ( ! defined( 'IS_WPCOM' ) || ! IS_WPCOM ) ) 
			 // Certain WordPress.com API requests
			 || ( defined( 'REST_API_REQUEST' ) && REST_API_REQUEST )
			 // WordPress.org REST API requests
			 || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		 ) {
			if ( $new_status == 'spam' && ( $old_status == 'approved' || $old_status == 'unapproved' || !$old_status ) ) {
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

		$comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $comment_id ) );

		if ( !$comment ) // it was deleted
			return;

		if ( 'spam' != $comment->comment_approved )
			return;

		// use the original version stored in comment_meta if available
		$as_submitted = self::sanitize_comment_as_submitted( get_comment_meta( $comment_id, 'akismet_as_submitted', true ) );

		if ( $as_submitted && is_array( $as_submitted ) && isset( $as_submitted['comment_content'] ) )
			$comment = (object) array_merge( (array)$comment, $as_submitted );

		$comment->blog         = get_option( 'home' );
		$comment->blog_lang    = get_locale();
		$comment->blog_charset = get_option('blog_charset');
		$comment->permalink    = get_permalink($comment->comment_post_ID);

		if ( is_object($current_user) )
			$comment->reporter = $current_user->user_login;

		if ( is_object($current_site) )
			$comment->site_domain = $current_site->domain;

		$comment->user_role = '';
		if ( ! empty( $comment->user_ID ) ) {
			$comment->user_role = Akismet::get_user_roles( $comment->user_ID );
		}

		if ( self::is_test_mode() )
			$comment->is_test = 'true';

		$post = get_post( $comment->comment_post_ID );

		if ( ! is_null( $post ) ) {
			$comment->comment_post_modified_gmt = $post->post_modified_gmt;
		}

		$response = Akismet::http_post( Akismet::build_query( $comment ), 'submit-spam' );
		if ( $comment->reporter ) {
			self::update_comment_history( $comment_id, '', 'report-spam' );
			update_comment_meta( $comment_id, 'akismet_user_result', 'true' );
			update_comment_meta( $comment_id, 'akismet_user', $comment->reporter );
		}

		do_action('akismet_submit_spam_comment', $comment_id, $response[1]);
	}

	public static function submit_nonspam_comment( $comment_id ) {
		global $wpdb, $current_user, $current_site;

		$comment_id = (int) $comment_id;

		$comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $comment_id ) );
		if ( !$comment ) // it was deleted
			return;

		// use the original version stored in comment_meta if available
		$as_submitted = self::sanitize_comment_as_submitted( get_comment_meta( $comment_id, 'akismet_as_submitted', true ) );

		if ( $as_submitted && is_array($as_submitted) && isset($as_submitted['comment_content']) )
			$comment = (object) array_merge( (array)$comment, $as_submitted );

		$comment->blog         = get_option( 'home' );
		$comment->blog_lang    = get_locale();
		$comment->blog_charset = get_option('blog_charset');
		$comment->permalink    = get_permalink( $comment->comment_post_ID );
		$comment->user_role    = '';

		if ( is_object($current_user) )
			$comment->reporter = $current_user->user_login;

		if ( is_object($current_site) )
			$comment->site_domain = $current_site->domain;

		if ( ! empty( $comment->user_ID ) ) {
			$comment->user_role = Akismet::get_user_roles( $comment->user_ID );
		}

		if ( Akismet::is_test_mode() )
			$comment->is_test = 'true';

		$post = get_post( $comment->comment_post_ID );

		if ( ! is_null( $post ) ) {
			$comment->comment_post_modified_gmt = $post->post_modified_gmt;
		}

		$response = self::http_post( Akismet::build_query( $comment ), 'submit-ham' );
		if ( $comment->reporter ) {
			self::update_comment_history( $comment_id, '', 'report-ham' );
			update_comment_meta( $comment_id, 'akismet_user_result', 'false' );
			update_comment_meta( $comment_id, 'akismet_user', $comment->reporter );
		}

		do_action('akismet_submit_nonspam_comment', $comment_id, $response[1]);
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

		delete_option('akismet_available_servers');

		$comment_errors = $wpdb->get_col( "SELECT comment_id FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'	LIMIT 100" );
		
		load_plugin_textdomain( 'akismet' );

		foreach ( (array) $comment_errors as $comment_id ) {
			// if the comment no longer exists, or is too old, remove the meta entry from the queue to avoid getting stuck
			$comment = get_comment( $comment_id );

			if (
				! $comment // Comment has been deleted
				|| strtotime( $comment->comment_date_gmt ) < strtotime( "-15 days" ) // Comment is too old.
				|| $comment->comment_approved !== "0" // Comment is no longer in the Pending queue
				) {
				echo "Deleting";
				delete_comment_meta( $comment_id, 'akismet_error' );
				delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
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
			if ( !empty( $event ) ) {
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
						if ( check_comment($comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content, $comment->comment_author_IP, $comment->comment_agent, $comment->comment_type) )
							wp_set_comment_status( $comment_id, 1 );
						else if ( get_comment_meta( $comment_id, 'akismet_delayed_moderation_email', true ) )
							wp_notify_moderator( $comment_id );
					}
				}
				
				delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
			} else {
				// If this comment has been pending moderation for longer than MAX_DELAY_BEFORE_MODERATION_EMAIL,
				// send a moderation email now.
				if ( ( intval( gmdate( 'U' ) ) - strtotime( $comment->comment_date_gmt ) ) < self::MAX_DELAY_BEFORE_MODERATION_EMAIL ) {
					delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
					wp_notify_moderator( $comment_id );
				}

				delete_comment_meta( $comment_id, 'akismet_rechecking' );
				wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
				do_action( 'akismet_scheduled_recheck', 'check-db-comment-' . $status );
				return;
			}
			delete_comment_meta( $comment_id, 'akismet_rechecking' );
		}

		$remaining = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'" );
		if ( $remaining && !wp_next_scheduled('akismet_schedule_cron_recheck') ) {
			wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
			do_action( 'akismet_scheduled_recheck', 'remaining' );
		}
	}

	public static function fix_scheduled_recheck() {
		$future_check = wp_next_scheduled( 'akismet_schedule_cron_recheck' );
		if ( !$future_check ) {
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
		$akismet_comment_nonce_option = apply_filters( 'akismet_comment_nonce', get_option( 'akismet_comment_nonce' ) );

		if ( $akismet_comment_nonce_option == 'true' || $akismet_comment_nonce_option == '' ) {
			echo '<p style="display: none;">';
			wp_nonce_field( 'akismet_comment_nonce_' . $post_id, 'akismet_comment_nonce', FALSE );
			echo '</p>';
		}
	}

	public static function is_test_mode() {
		return defined('AKISMET_TEST_MODE') && AKISMET_TEST_MODE;
	}
	
	public static function allow_discard() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return false;
		if ( is_user_logged_in() )
			return false;
	
		return ( get_option( 'akismet_strictness' ) === '1'  );
	}

	public static function get_ip_address() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
	}
	
	/**
	 * Do these two comments, without checking the comment_ID, "match"?
	 *
	 * @param mixed $comment1 A comment object or array.
	 * @param mixed $comment2 A comment object or array.
	 * @return bool Whether the two comments should be treated as the same comment.
	 */
	private static function comments_match( $comment1, $comment2 ) {
		$comment1 = (array) $comment1;
		$comment2 = (array) $comment2;

		// Set default values for these strings that we check in order to simplify
		// the checks and avoid PHP warnings.
		if ( ! isset( $comment1['comment_author'] ) ) {
			$comment1['comment_author'] = '';
		}

		if ( ! isset( $comment2['comment_author'] ) ) {
			$comment2['comment_author'] = '';
		}

		if ( ! isset( $comment1['comment_author_email'] ) ) {
			$comment1['comment_author_email'] = '';
		}

		if ( ! isset( $comment2['comment_author_email'] ) ) {
			$comment2['comment_author_email'] = '';
		}

		$comments_match = (
			   isset( $comment1['comment_post_ID'], $comment2['comment_post_ID'] )
			&& intval( $comment1['comment_post_ID'] ) == intval( $comment2['comment_post_ID'] )
			&& (
				// The comment author length max is 255 characters, limited by the TINYTEXT column type.
				// If the comment author includes multibyte characters right around the 255-byte mark, they
				// may be stripped when the author is saved in the DB, so a 300+ char author may turn into
				// a 253-char author when it's saved, not 255 exactly.  The longest possible character is
				// theoretically 6 bytes, so we'll only look at the first 248 bytes to be safe.
				substr( $comment1['comment_author'], 0, 248 ) == substr( $comment2['comment_author'], 0, 248 )
				|| substr( stripslashes( $comment1['comment_author'] ), 0, 248 ) == substr( $comment2['comment_author'], 0, 248 )
				|| substr( $comment1['comment_author'], 0, 248 ) == substr( stripslashes( $comment2['comment_author'] ), 0, 248 )
				// Certain long comment author names will be truncated to nothing, depending on their encoding.
				|| ( ! $comment1['comment_author'] && strlen( $comment2['comment_author'] ) > 248 )
				|| ( ! $comment2['comment_author'] && strlen( $comment1['comment_author'] ) > 248 )
				)
			&& (
				// The email max length is 100 characters, limited by the VARCHAR(100) column type.
				// Same argument as above for only looking at the first 93 characters.
				substr( $comment1['comment_author_email'], 0, 93 ) == substr( $comment2['comment_author_email'], 0, 93 )
				|| substr( stripslashes( $comment1['comment_author_email'] ), 0, 93 ) == substr( $comment2['comment_author_email'], 0, 93 )
				|| substr( $comment1['comment_author_email'], 0, 93 ) == substr( stripslashes( $comment2['comment_author_email'] ), 0, 93 )
				// Very long emails can be truncated and then stripped if the [0:100] substring isn't a valid address.
				|| ( ! $comment1['comment_author_email'] && strlen( $comment2['comment_author_email'] ) > 100 )
				|| ( ! $comment2['comment_author_email'] && strlen( $comment1['comment_author_email'] ) > 100 )
			)
		);

		return $comments_match;
	}
	
	// Does the supplied comment match the details of the one most recently stored in self::$last_comment?
	public static function matches_last_comment( $comment ) {
		return self::comments_match( self::$last_comment, $comment );
	}

	private static function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}

	private static function get_referer() {
		return isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
	}

	// return a comma-separated list of role names for the given user
	public static function get_user_roles( $user_id ) {
		$roles = false;

		if ( !class_exists('WP_User') )
			return false;

		if ( $user_id > 0 ) {
			$comment_user = new WP_User( $user_id );
			if ( isset( $comment_user->roles ) )
				$roles = join( ',', $comment_user->roles );
		}

		if ( is_multisite() && is_super_admin( $user_id ) ) {
			if ( empty( $roles ) ) {
				$roles = 'super_admin';
			} else {
				$comment_user->roles[] = 'super_admin';
				$roles = join( ',', $comment_user->roles );
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

		// Only do this if it's the correct comment
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
		if ( $incr = apply_filters('akismet_spam_count_incr', 1) )
			update_option( 'akismet_spam_count', get_option('akismet_spam_count') + $incr );

		return self::$last_comment_result;
	}
	
	/**
	 * If Akismet is temporarily unreachable, we don't want to "spam" the blogger with
	 * moderation emails for comments that will be automatically cleared or spammed on
	 * the next retry.
	 *
	 * For comments that will be rechecked later, empty the list of email addresses that
	 * the moderation email would be sent to.
	 *
	 * @param array $emails An array of email addresses that the moderation email will be sent to.
	 * @param int $comment_id The ID of the relevant comment.
	 * @return array An array of email addresses that the moderation email will be sent to.
	 */
	public static function disable_moderation_emails_if_unreachable( $emails, $comment_id ) {
		if ( ! empty( self::$prevent_moderation_email_for_these_comments ) && ! empty( $emails ) ) {
			$comment = get_comment( $comment_id );

			foreach ( self::$prevent_moderation_email_for_these_comments as $possible_match ) {
				if ( self::comments_match( $possible_match, $comment ) ) {
					update_comment_meta( $comment_id, 'akismet_delayed_moderation_email', true );
					return array();
				}
			}
		}

		return $emails;
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
	public static function http_post( $request, $path, $ip=null ) {

		$akismet_ua = sprintf( 'WordPress/%s | Akismet/%s', $GLOBALS['wp_version'], constant( 'AKISMET_VERSION' ) );
		$akismet_ua = apply_filters( 'akismet_ua', $akismet_ua );

		$content_length = strlen( $request );

		$api_key   = self::get_api_key();
		$host      = self::API_HOST;

		if ( !empty( $api_key ) )
			$host = $api_key.'.'.$host;

		$http_host = $host;
		// use a specific IP if provided
		// needed by Akismet_Admin::check_server_connectivity()
		if ( $ip && long2ip( ip2long( $ip ) ) ) {
			$http_host = $ip;
		}

		$http_args = array(
			'body' => $request,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
				'Host' => $host,
				'User-Agent' => $akismet_ua,
			),
			'httpversion' => '1.0',
			'timeout' => 15
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
		}
		else if ( $ssl_disabled ) {
			do_action( 'akismet_ssl_disabled' );
		}

		if ( ! $ssl_disabled && ( $ssl = wp_http_supports( array( 'ssl' ) ) ) ) {
			$akismet_url = set_url_scheme( $akismet_url, 'https' );

			do_action( 'akismet_https_request_pre' );
		}

		$response = wp_remote_post( $akismet_url, $http_args );

		Akismet::log( compact( 'akismet_url', 'http_args', 'response' ) );

		if ( $ssl && is_wp_error( $response ) ) {
			do_action( 'akismet_https_request_failure', $response );

			// Intermittent connection problems may cause the first HTTPS
			// request to fail and subsequent HTTP requests to succeed randomly.
			// Retry the HTTPS request once before disabling SSL for a time.
			$response = wp_remote_post( $akismet_url, $http_args );
			
			Akismet::log( compact( 'akismet_url', 'http_args', 'response' ) );

			if ( is_wp_error( $response ) ) {
				$ssl_failed = true;

				do_action( 'akismet_https_request_failure', $response );

				do_action( 'akismet_http_request_pre' );

				// Try the request again without SSL.
				$response = wp_remote_post( $http_akismet_url, $http_args );

				Akismet::log( compact( 'http_akismet_url', 'http_args', 'response' ) );
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
		
		self::update_alert( $simplified_response );

		return $simplified_response;
	}

	// given a response from an API call like check_key_status(), update the alert code options if an alert is present.
	public static function update_alert( $response ) {
		$code = $msg = null;
		if ( isset( $response[0]['x-akismet-alert-code'] ) ) {
			$code = $response[0]['x-akismet-alert-code'];
			$msg  = $response[0]['x-akismet-alert-msg'];
		}

		// only call update_option() if the value has changed
		if ( $code != get_option( 'akismet_alert_code' ) ) {
			if ( ! $code ) {
				delete_option( 'akismet_alert_code' );
				delete_option( 'akismet_alert_msg' );
			}
			else {
				update_option( 'akismet_alert_code', $code );
				update_option( 'akismet_alert_msg', $msg );
			}
		}
	}

	public static function load_form_js() {
		wp_register_script( 'akismet-form', plugin_dir_url( __FILE__ ) . '_inc/form.js', array(), AKISMET_VERSION, true );
		wp_enqueue_script( 'akismet-form' );
	}
	
	/**
	 * Mark form.js as async. Because nothing depends on it, it can run at any time
	 * after it's loaded, and the browser won't have to wait for it to load to continue
	 * parsing the rest of the page.
	 */
	public static function set_form_js_async( $tag, $handle, $src ) {
		if ( 'akismet-form' !== $handle ) {
			return $tag;
		}
		
		return preg_replace( '/^<script /i', '<script async="async" ', $tag );
	}
	
	public static function inject_ak_js( $fields ) {
		echo '<p style="display: none;">';
		echo '<input type="hidden" id="ak_js" name="ak_js" value="' . mt_rand( 0, 250 ) . '"/>';
		echo '</p>';
	}

	private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
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
					$plugins[$i] = false;
					$update = true;
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
		
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
		
		load_plugin_textdomain( 'akismet' );

		$file = AKISMET__PLUGIN_DIR . 'views/'. $name . '.php';

		include( $file );
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], AKISMET__MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'akismet' );
			
			$message = '<strong>'.sprintf(esc_html__( 'Akismet %s requires WordPress %s or higher.' , 'akismet'), AKISMET_VERSION, AKISMET__MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'akismet'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/akismet/download/');

			Akismet::bail_on_activation( $message );
		}
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
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

	public static function pre_check_pingback( $method ) {
		if ( $method !== 'pingback.ping' )
			return;

		global $wp_xmlrpc_server;
	
		if ( !is_object( $wp_xmlrpc_server ) )
			return false;
	
		// Lame: tightly coupled with the IXR class.
		$args = $wp_xmlrpc_server->message->params;
	
		if ( !empty( $args[1] ) ) {
			$post_id = url_to_postid( $args[1] );

			// If this gets through the pre-check, make sure we properly identify the outbound request as a pingback verification
			Akismet::pingback_forwarded_for( null, $args[0] );
			add_filter( 'http_request_args', array( 'Akismet', 'pingback_forwarded_for' ), 10, 2 );

			$comment = array(
				'comment_author_url' => $args[0],
				'comment_post_ID' => $post_id,
				'comment_author' => '',
				'comment_author_email' => '',
				'comment_content' => '',
				'comment_type' => 'pingback',
				'akismet_pre_check' => '1',
				'comment_pingback_target' => $args[1],
			);

			$comment = Akismet::auto_check_comment( $comment );

			if ( isset( $comment['akismet_result'] ) && 'true' == $comment['akismet_result'] ) {
				// Lame: tightly coupled with the IXR classes. Unfortunately the action provides no context and no way to return anything.
				$wp_xmlrpc_server->error( new IXR_Error( 0, 'Invalid discovery target' ) );
			}
		}
	}
	
	public static function pingback_forwarded_for( $r, $url ) {
		static $urls = array();
	
		// Call this with $r == null to prime the callback to add headers on a specific URL
		if ( is_null( $r ) && !in_array( $url, $urls ) ) {
			$urls[] = $url;
		}

		// Add X-Pingback-Forwarded-For header, but only for requests to a specific URL (the apparent pingback source)
		if ( is_array( $r ) && is_array( $r['headers'] ) && !isset( $r['headers']['X-Pingback-Forwarded-For'] ) && in_array( $url, $urls ) ) {
			$remote_ip = preg_replace( '/[^a-fx0-9:.,]/i', '', $_SERVER['REMOTE_ADDR'] );
		
			// Note: this assumes REMOTE_ADDR is correct, and it may not be if a reverse proxy or CDN is in use
			$r['headers']['X-Pingback-Forwarded-For'] = $remote_ip;

			// Also identify the request as a pingback verification in the UA string so it appears in logs
			$r['user-agent'] .= '; verifying pingback from ' . $remote_ip;
		}

		return $r;
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
			if ( ! isset( self::$comment_as_submitted_allowed_keys[$key] ) || ! is_scalar( $value ) ) {
				unset( $meta_value[$key] );
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
}
