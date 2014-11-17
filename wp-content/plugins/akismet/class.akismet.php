<?php

class Akismet {
	const API_HOST = 'rest.akismet.com';
	const API_PORT = 80;
	const MAX_DELAY_BEFORE_MODERATION_EMAIL = 86400; // One day in seconds

	private static $last_comment = '';
	private static $initiated = false;
	private static $prevent_moderation_email_for_these_comments = array();
	private static $last_comment_result = null;
	
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
		add_action( 'preprocess_comment', array( 'Akismet', 'auto_check_comment' ), 1 );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments_meta' ) );
		add_action( 'akismet_schedule_cron_recheck', array( 'Akismet', 'cron_recheck' ) );

		$akismet_comment_nonce_option = apply_filters( 'akismet_comment_nonce', get_option( 'akismet_comment_nonce' ) );

		if ( $akismet_comment_nonce_option == 'true' || $akismet_comment_nonce_option == '' )
			add_action( 'comment_form',  array( 'Akismet',  'add_comment_nonce' ), 1 );

		add_action( 'admin_head-edit-comments.php', array( 'Akismet', 'load_form_js' ) );
		add_action( 'comment_form', array( 'Akismet', 'load_form_js' ) );
		add_action( 'comment_form', array( 'Akismet', 'inject_ak_js' ) );

		add_filter( 'comment_moderation_recipients', array( 'Akismet', 'disable_moderation_emails_if_unreachable' ), 1000, 2 );
		add_filter( 'pre_comment_approved', array( 'Akismet', 'last_comment_status' ), 10, 2 );
		
		add_action( 'transition_comment_status', array( 'Akismet', 'transition_comment_status' ), 10, 3 );

		if ( '3.0.5' == $GLOBALS['wp_version'] ) {
			remove_filter( 'comment_text', 'wp_kses_data' );
			if ( is_admin() )
				add_filter( 'comment_text', 'wp_kses_post' );
		}
	}

	public static function get_api_key() {
		return apply_filters( 'akismet_get_api_key', defined('WPCOM_API_KEY') ? constant('WPCOM_API_KEY') : get_option('wordpress_api_key') );
	}

	public static function check_key_status( $key, $ip = null ) {
		return self::http_post( Akismet::build_query( array( 'key' => $key, 'blog' => get_option('home') ) ), 'verify-key', $ip );
	}

	public static function verify_key( $key, $ip = null ) {
		$response = self::check_key_status( $key, $ip );

		if ( $response[1] != 'valid' && $response[1] != 'invalid' )
			return 'failed';

		self::update_alert( $response );

		return $response[1];
	}

	public static function auto_check_comment( $commentdata ) {
		self::$last_comment_result = null;

		$comment = $commentdata;

		$comment['user_ip']      = self::get_ip_address();
		$comment['user_agent']   = self::get_user_agent();
		$comment['referrer']     = self::get_referer();
		$comment['blog']         = get_option('home');
		$comment['blog_lang']    = get_locale();
		$comment['blog_charset'] = get_option('blog_charset');
		$comment['permalink']    = get_permalink( $comment['comment_post_ID'] );

		if ( !empty( $comment['user_ID'] ) )
			$comment['user_role'] = Akismet::get_user_roles( $comment['user_ID'] );

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

		$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );

		foreach ( $_SERVER as $key => $value ) {
			if ( !in_array( $key, $ignore ) && is_string($value) )
				$comment["$key"] = $value;
			else
				$comment["$key"] = '';
		}

		$post = get_post( $comment['comment_post_ID'] );
		$comment[ 'comment_post_modified_gmt' ] = $post->post_modified_gmt;

		$response = self::http_post( Akismet::build_query( $comment ), 'comment-check' );

		do_action( 'akismet_comment_check_response', $response );

		self::update_alert( $response );

		$commentdata['comment_as_submitted'] = array_intersect_key( $comment, array( 'blog' => '', 'blog_charset' => '', 'blog_lang' => '', 'blog_ua' => '', 'comment_agent' => '', 'comment_author' => '', 'comment_author_IP' => '', 'comment_author_email' => '', 'comment_author_url' => '', 'comment_content' => '', 'comment_date_gmt' => '', 'comment_tags' => '', 'comment_type' => '', 'guid' => '', 'is_test' => '', 'permalink' => '', 'reporter' => '', 'site_domain' => '', 'submit_referer' => '', 'submit_uri' => '', 'user_ID' => '', 'user_agent' => '', 'user_id' => '', 'user_ip' => '' ) );
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
				// akismet_result_spam() won't be called so bump the counter here
				if ( $incr = apply_filters('akismet_spam_count_incr', 1) )
					update_option( 'akismet_spam_count', get_option('akismet_spam_count') + $incr );
				$redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : get_permalink( $post );
				wp_safe_redirect( esc_url_raw( $redirect_to ) );
				die();
			}
		}
		
		// if the response is neither true nor false, hold the comment for moderation and schedule a recheck
		if ( 'true' != $response[1] && 'false' != $response[1] ) {
			if ( !current_user_can('moderate_comments') ) {
				// Comment status should be moderated
				self::$last_comment_result = '0';
			}
			if ( function_exists('wp_next_scheduled') && function_exists('wp_schedule_single_event') ) {
				if ( !wp_next_scheduled( 'akismet_schedule_cron_recheck' ) ) {
					wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
				}
			}

			self::$prevent_moderation_email_for_these_comments[] = $commentdata;
		}

		if ( function_exists('wp_next_scheduled') && function_exists('wp_schedule_event') ) {
			// WP 2.1+: delete old comments daily
			if ( !wp_next_scheduled( 'akismet_scheduled_delete' ) )
				wp_schedule_event( time(), 'daily', 'akismet_scheduled_delete' );
		}
		elseif ( (mt_rand(1, 10) == 3) ) {
			// WP 2.0: run this one time in ten
			self::delete_old_comments();
		}
		
		self::set_last_comment( $commentdata );
		self::fix_scheduled_recheck();

		return self::$last_comment;
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

		// failsafe for old WP versions
		if ( !function_exists('add_comment_meta') )
			return false;

		if ( !isset( self::$last_comment['comment_author_email'] ) )
			self::$last_comment['comment_author_email'] = '';

		// wp_insert_comment() might be called in other contexts, so make sure this is the same comment
		// as was checked by auto_check_comment
		if ( is_object( $comment ) && !empty( self::$last_comment ) && is_array( self::$last_comment ) ) {
			if ( self::matches_last_comment( $comment ) ) {
					
					load_plugin_textdomain( 'akismet' );
					
					// normal result: true or false
					if ( self::$last_comment['akismet_result'] == 'true' ) {
						update_comment_meta( $comment->comment_ID, 'akismet_result', 'true' );
						self::update_comment_history( $comment->comment_ID, __('Akismet caught this comment as spam', 'akismet'), 'check-spam' );
						if ( $comment->comment_approved != 'spam' )
							self::update_comment_history( $comment->comment_ID, sprintf( __('Comment status was changed to %s', 'akismet'), $comment->comment_approved), 'status-changed'.$comment->comment_approved );
					}
					elseif ( self::$last_comment['akismet_result'] == 'false' ) {
						update_comment_meta( $comment->comment_ID, 'akismet_result', 'false' );
						self::update_comment_history( $comment->comment_ID, __('Akismet cleared this comment', 'akismet'), 'check-ham' );
						if ( $comment->comment_approved == 'spam' ) {
							if ( wp_blacklist_check($comment->comment_author, $comment->comment_author_email, $comment->comment_author_url, $comment->comment_content, $comment->comment_author_IP, $comment->comment_agent) )
								self::update_comment_history( $comment->comment_ID, __('Comment was caught by wp_blacklist_check', 'akismet'), 'wp-blacklisted' );
							else
								self::update_comment_history( $comment->comment_ID, sprintf( __('Comment status was changed to %s', 'akismet'), $comment->comment_approved), 'status-changed-'.$comment->comment_approved );
						}
					} // abnormal result: error
					else {
						update_comment_meta( $comment->comment_ID, 'akismet_error', time() );
						self::update_comment_history( $comment->comment_ID, sprintf( __('Akismet was unable to check this comment (response: %s), will automatically retry again later.', 'akismet'), substr(self::$last_comment['akismet_result'], 0, 50)), 'check-error' );
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

		while( $comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT comment_id FROM {$wpdb->comments} WHERE DATE_SUB(NOW(), INTERVAL 15 DAY) > comment_date_gmt AND comment_approved = 'spam' LIMIT %d", defined( 'AKISMET_DELETE_LIMIT' ) ? AKISMET_DELETE_LIMIT : 10000 ) ) ) {
			if ( empty( $comment_ids ) )
				return;

			$wpdb->queries = array();

			do_action( 'delete_comment', $comment_ids );

			$comma_comment_ids = implode( ', ', array_map('intval', $comment_ids) );

			$wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_id IN ( $comma_comment_ids )");
			$wpdb->query("DELETE FROM {$wpdb->commentmeta} WHERE comment_id IN ( $comma_comment_ids )");

			clean_comment_cache( $comment_ids );
		}

		if ( apply_filters( 'akismet_optimize_table', ( mt_rand(1, 5000) == 11), $wpdb->comments ) ) // lucky number
			$wpdb->query("OPTIMIZE TABLE {$wpdb->comments}");
	}

	public static function delete_old_comments_meta() {
		global $wpdb;

		$interval = apply_filters( 'akismet_delete_commentmeta_interval', 15 );

		# enfore a minimum of 1 day
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
			}
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

		// failsafe for old WP versions
		if ( !function_exists('add_comment_meta') )
			return false;

		$history = get_comment_meta( $comment_id, 'akismet_history', false );
		usort( $history, array( 'Akismet', '_cmp_time' ) );
		return $history;
	}

	// log an event for a given comment, storing it in comment_meta
	public static function update_comment_history( $comment_id, $message, $event=null ) {
		global $current_user;

		// failsafe for old WP versions
		if ( !function_exists('add_comment_meta') )
			return false;

		$user = '';
		if ( is_object( $current_user ) && isset( $current_user->user_login ) )
			$user = $current_user->user_login;

		$event = array(
			'time'    => self::_get_microtime(),
			'message' => $message,
			'event'   => $event,
			'user'    => $user,
		);

		// $unique = false so as to allow multiple values per comment
		$r = add_comment_meta( $comment_id, 'akismet_history', $event, false );
	}

	public static function check_db_comment( $id, $recheck_reason = 'recheck_queue' ) {
		global $wpdb;

		$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $id ), ARRAY_A );
		if ( !$c )
			return;

		$c['user_ip']        = $c['comment_author_IP'];
		$c['user_agent']     = $c['comment_agent'];
		$c['referrer']       = '';
		$c['blog']           = get_option('home');
		$c['blog_lang']      = get_locale();
		$c['blog_charset']   = get_option('blog_charset');
		$c['permalink']      = get_permalink($c['comment_post_ID']);
		$c['recheck_reason'] = $recheck_reason;

		if ( self::is_test_mode() )
			$c['is_test'] = 'true';

		$response = self::http_post( Akismet::build_query( $c ), 'comment-check' );

		return ( is_array( $response ) && ! empty( $response[1] ) ) ? $response[1] : false;
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
		
		global $current_user;
		$reporter = '';
		if ( is_object( $current_user ) )
			$reporter = $current_user->user_login;

		// Assumption alert:
		// We want to submit comments to Akismet only when a moderator explicitly spams or approves it - not if the status
		// is changed automatically by another plugin.  Unfortunately WordPress doesn't provide an unambiguous way to
		// determine why the transition_comment_status action was triggered.  And there are several different ways by which
		// to spam and unspam comments: bulk actions, ajax, links in moderation emails, the dashboard, and perhaps others.
		// We'll assume that this is an explicit user action if certain POST/GET variables exist.
		if ( ( isset( $_POST['status'] ) && in_array( $_POST['status'], array( 'spam', 'unspam' ) ) ) ||
			 ( isset( $_POST['spam'] )   && (int) $_POST['spam'] == 1 ) ||
			 ( isset( $_POST['unspam'] ) && (int) $_POST['unspam'] == 1 ) ||
			 ( isset( $_POST['comment_status'] )  && in_array( $_POST['comment_status'], array( 'spam', 'unspam' ) ) ) ||
			 ( isset( $_GET['action'] )  && in_array( $_GET['action'], array( 'spam', 'unspam' ) ) ) ||
			 ( isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'editedcomment' ) ) )
		 ) {
			if ( $new_status == 'spam' && ( $old_status == 'approved' || $old_status == 'unapproved' || !$old_status ) ) {
				return self::submit_spam_comment( $comment->comment_ID );
			} elseif ( $old_status == 'spam' && ( $new_status == 'approved' || $new_status == 'unapproved' ) ) {
				return self::submit_nonspam_comment( $comment->comment_ID );
			}
		}

		self::update_comment_history( $comment->comment_ID, sprintf( __('%1$s changed the comment status to %2$s', 'akismet'), $reporter, $new_status ), 'status-' . $new_status );
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
		$as_submitted = get_comment_meta( $comment_id, 'akismet_as_submitted', true);

		if ( $as_submitted && is_array( $as_submitted ) && isset( $as_submitted['comment_content'] ) )
			$comment = (object) array_merge( (array)$comment, $as_submitted );

		$comment->blog         = get_bloginfo('url');
		$comment->blog_lang    = get_locale();
		$comment->blog_charset = get_option('blog_charset');
		$comment->permalink    = get_permalink($comment->comment_post_ID);

		if ( is_object($current_user) )
			$comment->reporter = $current_user->user_login;

		if ( is_object($current_site) )
			$comment->site_domain = $current_site->domain;

		$comment->user_role = '';
		if ( isset( $comment->user_ID ) )
			$comment->user_role = Akismet::get_user_roles( $comment->user_ID );

		if ( self::is_test_mode() )
			$comment->is_test = 'true';

		$post = get_post( $comment->comment_post_ID );
		$comment->comment_post_modified_gmt = $post->post_modified_gmt;

		$response = Akismet::http_post( Akismet::build_query( $comment ), 'submit-spam' );
		if ( $comment->reporter ) {
			self::update_comment_history( $comment_id, sprintf( __('%s reported this comment as spam', 'akismet'), $comment->reporter ), 'report-spam' );
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
		$as_submitted = get_comment_meta( $comment_id, 'akismet_as_submitted', true);

		if ( $as_submitted && is_array($as_submitted) && isset($as_submitted['comment_content']) )
			$comment = (object) array_merge( (array)$comment, $as_submitted );

		$comment->blog         = get_bloginfo('url');
		$comment->blog_lang    = get_locale();
		$comment->blog_charset = get_option('blog_charset');
		$comment->permalink    = get_permalink( $comment->comment_post_ID );
		$comment->user_role    = '';

		if ( is_object($current_user) )
			$comment->reporter = $current_user->user_login;

		if ( is_object($current_site) )
			$comment->site_domain = $current_site->domain;

		if ( isset( $comment->user_ID ) )
			$comment->user_role = Akismet::get_user_roles($comment->user_ID);

		if ( Akismet::is_test_mode() )
			$comment->is_test = 'true';

		$post = get_post( $comment->comment_post_ID );
		$comment->comment_post_modified_gmt = $post->post_modified_gmt;

		$response = self::http_post( Akismet::build_query( $comment ), 'submit-ham' );
		if ( $comment->reporter ) {
			self::update_comment_history( $comment_id, sprintf( __('%s reported this comment as not spam', 'akismet'), $comment->reporter ), 'report-ham' );
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
			return false;
		}

		delete_option('akismet_available_servers');

		$comment_errors = $wpdb->get_col( "SELECT comment_id FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'	LIMIT 100" );
		
		load_plugin_textdomain( 'akismet' );

		foreach ( (array) $comment_errors as $comment_id ) {
			// if the comment no longer exists, or is too old, remove the meta entry from the queue to avoid getting stuck
			$comment = get_comment( $comment_id );
			if ( !$comment || strtotime( $comment->comment_date_gmt ) < strtotime( "-15 days" ) ) {
				delete_comment_meta( $comment_id, 'akismet_error' );
				delete_comment_meta( $comment_id, 'akismet_delayed_moderation_email' );
				continue;
			}

			add_comment_meta( $comment_id, 'akismet_rechecking', true );
			$status = self::check_db_comment( $comment_id, 'retry' );

			$msg = '';
			if ( $status == 'true' ) {
				$msg = __( 'Akismet caught this comment as spam during an automatic retry.' , 'akismet');
			} elseif ( $status == 'false' ) {
				$msg = __( 'Akismet cleared this comment during an automatic retry.' , 'akismet');
			}

			// If we got back a legit response then update the comment history
			// other wise just bail now and try again later.  No point in
			// re-trying all the comments once we hit one failure.
			if ( !empty( $msg ) ) {
				delete_comment_meta( $comment_id, 'akismet_error' );
				self::update_comment_history( $comment_id, $msg, 'cron-retry' );
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
				return;
			}
			delete_comment_meta( $comment_id, 'akismet_rechecking' );
		}

		$remaining = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'" );
		if ( $remaining && !wp_next_scheduled('akismet_schedule_cron_recheck') ) {
			wp_schedule_single_event( time() + 1200, 'akismet_schedule_cron_recheck' );
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
		}
	}

	public static function add_comment_nonce( $post_id ) {
		echo '<p style="display: none;">';
		wp_nonce_field( 'akismet_comment_nonce_' . $post_id, 'akismet_comment_nonce', FALSE );
		echo '</p>';
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
		
		return (
			   isset( $comment1['comment_post_ID'], $comment2['comment_post_ID'] )
			&& intval( $comment1['comment_post_ID'] ) == intval( $comment2['comment_post_ID'] )
			&& $comment1['comment_author'] == $comment2['comment_author']
			&& $comment1['comment_author_email'] == $comment2['comment_author_email']
		);
	}
	
	// Does the supplied comment match the details of the one most recently stored in self::$last_comment?
	public static function matches_last_comment( $comment ) {
		if ( is_object( $comment ) )
			$comment = (array) $comment;

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
		// Only do this if it's the correct comment
		if ( is_null(self::$last_comment_result) || ! self::matches_last_comment( $comment ) ) {
			self::log( "comment_is_spam mismatched comment, returning unaltered $approved" );
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

		$akismet_url = "http://{$http_host}/1.1/{$path}";
		$response = wp_remote_post( $akismet_url, $http_args );
		Akismet::log( compact( 'akismet_url', 'http_args', 'response' ) );
		if ( is_wp_error( $response ) )
			return array( '', '' );

		return array( $response['headers'], $response['body'] );
	}

	// given a response from an API call like check_key_status(), update the alert code options if an alert is present.
	private static function update_alert( $response ) {
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
		// WP < 3.3 can't enqueue a script this late in the game and still have it appear in the footer.
		// Once we drop support for everything pre-3.3, this can change back to a single enqueue call.
		wp_register_script( 'akismet-form', AKISMET__PLUGIN_URL . '_inc/form.js', array(), AKISMET_VERSION, true );
		add_action( 'wp_footer', array( 'Akismet', 'print_form_js' ) );
		add_action( 'admin_footer', array( 'Akismet', 'print_form_js' ) );
	}
	
	public static function print_form_js() {
		wp_print_scripts( 'akismet-form' );
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
			
			$message = '<strong>'.sprintf(esc_html__( 'Akismet %s requires WordPress %s or higher.' , 'akismet'), AKISMET_VERSION, AKISMET__MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'akismet'), 'https://codex.wordpress.org/Upgrading_WordPress', 'http://wordpress.org/extend/plugins/akismet/download/');

			Akismet::bail_on_activation( $message );
		}
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		//tidy up
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

	public static function log( $akismet_debug ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG )
			error_log( print_r( compact( 'akismet_debug' ), 1 ) ); //send message to debug.log when in debug mode
	}
}