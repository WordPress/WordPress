<?php

class Akismet_Admin {
	const NONCE = 'akismet-update-key';

	private static $initiated = false;
	private static $notices = array();

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'enter-key' ) {
			self::enter_api_key();
		}
	}

	public static function init_hooks() {
		// The standalone stats page was removed in 3.0 for an all-in-one config and stats page.
		// Redirect any links that might have been bookmarked or in browser history.
		if ( isset( $_GET['page'] ) && 'akismet-stats-display' == $_GET['page'] ) {
			wp_safe_redirect( esc_url_raw( self::get_page_url( 'stats' ) ), 301 );
			die;
		}

		self::$initiated = true;

		add_action( 'admin_init', array( 'Akismet_Admin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'Akismet_Admin', 'admin_menu' ), 5 ); # Priority 5, so it's called before Jetpack's admin_menu.
		add_action( 'admin_notices', array( 'Akismet_Admin', 'display_notice' ) );
		add_action( 'admin_enqueue_scripts', array( 'Akismet_Admin', 'load_resources' ) );
		add_action( 'activity_box_end', array( 'Akismet_Admin', 'dashboard_stats' ) );
		add_action( 'rightnow_end', array( 'Akismet_Admin', 'rightnow_stats' ) );
		add_action( 'manage_comments_nav', array( 'Akismet_Admin', 'check_for_spam_button' ) );
		add_action( 'admin_action_akismet_recheck_queue', array( 'Akismet_Admin', 'recheck_queue' ) );
		add_action( 'wp_ajax_akismet_recheck_queue', array( 'Akismet_Admin', 'recheck_queue' ) );
		add_action( 'wp_ajax_comment_author_deurl', array( 'Akismet_Admin', 'remove_comment_author_url' ) );
		add_action( 'wp_ajax_comment_author_reurl', array( 'Akismet_Admin', 'add_comment_author_url' ) );

		add_filter( 'plugin_action_links', array( 'Akismet_Admin', 'plugin_action_links' ), 10, 2 );
		add_filter( 'comment_row_actions', array( 'Akismet_Admin', 'comment_row_action' ), 10, 2 );
		add_filter( 'comment_text', array( 'Akismet_Admin', 'text_add_link_class' ) );
		
		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'akismet.php'), array( 'Akismet_Admin', 'admin_plugin_settings_link' ) );
		
		add_filter( 'wxr_export_skip_commentmeta', array( 'Akismet_Admin', 'exclude_commentmeta_from_export' ), 10, 3 );
	}

	public static function admin_init() {
		load_plugin_textdomain( 'akismet' );
		add_meta_box( 'akismet-status', __('Comment History', 'akismet'), array( 'Akismet_Admin', 'comment_status_meta_box' ), 'comment', 'normal' );
	}

	public static function admin_menu() {
		if ( class_exists( 'Jetpack' ) )
			add_action( 'jetpack_admin_menu', array( 'Akismet_Admin', 'load_menu' ) );
		else
			self::load_menu();
	}

	public static function admin_head() {
		if ( !current_user_can( 'manage_options' ) )
			return;
	}
	
	public static function admin_plugin_settings_link( $links ) { 
  		$settings_link = '<a href="'.esc_url( self::get_page_url() ).'">'.__('Settings', 'akismet').'</a>';
  		array_unshift( $links, $settings_link ); 
  		return $links; 
	}

	public static function load_menu() {
		if ( class_exists( 'Jetpack' ) )
			$hook = add_submenu_page( 'jetpack', __( 'Akismet' , 'akismet'), __( 'Akismet' , 'akismet'), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ) );
		else
			$hook = add_options_page( __('Akismet', 'akismet'), __('Akismet', 'akismet'), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ) );

		if ( version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) ) {
			add_action( "load-$hook", array( 'Akismet_Admin', 'admin_help' ) );
		}
	}

	public static function load_resources() {
		global $hook_suffix;

		if ( in_array( $hook_suffix, array(
			'index.php', # dashboard
			'edit-comments.php',
			'comment.php',
			'post.php',
			'settings_page_akismet-key-config',
			'jetpack_page_akismet-key-config',
		) ) ) {
			wp_register_style( 'akismet.css', AKISMET__PLUGIN_URL . '_inc/akismet.css', array(), AKISMET_VERSION );
			wp_enqueue_style( 'akismet.css');

			wp_register_script( 'akismet.js', AKISMET__PLUGIN_URL . '_inc/akismet.js', array('jquery','postbox'), AKISMET_VERSION );
			wp_enqueue_script( 'akismet.js' );
			wp_localize_script( 'akismet.js', 'WPAkismet', array(
				'comment_author_url_nonce' => wp_create_nonce( 'comment_author_url_nonce' ),
				'strings' => array(
					'Remove this URL' => __( 'Remove this URL' , 'akismet'),
					'Removing...'     => __( 'Removing...' , 'akismet'),
					'URL removed'     => __( 'URL removed' , 'akismet'),
					'(undo)'          => __( '(undo)' , 'akismet'),
					'Re-adding...'    => __( 'Re-adding...' , 'akismet'),
				)
			) );
		}
	}

	/**
	 * Add help to the Akismet page
	 *
	 * @return false if not the Akismet page
	 */
	public static function admin_help() {
		$current_screen = get_current_screen();

		// Screen Content
		if ( current_user_can( 'manage_options' ) ) {
			if ( !Akismet::get_api_key() || ( isset( $_GET['view'] ) && $_GET['view'] == 'start' ) ) {
				//setup page
				$current_screen->add_help_tab(
					array(
						'id'		=> 'overview',
						'title'		=> __( 'Overview' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Setup' , 'akismet') . '</strong></p>' .
							'<p>' . esc_html__( 'Akismet filters out your comment and trackback spam for you, so you can focus on more important things.' , 'akismet') . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to setup the Akismet plugin.' , 'akismet') . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'setup-signup',
						'title'		=> __( 'New to Akismet' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Setup' , 'akismet') . '</strong></p>' .
							'<p>' . esc_html__( 'You need to enter an API key to activate the Akismet service on your site.' , 'akismet') . '</p>' .
							'<p>' . sprintf( __( 'Signup for an account on %s to get an API Key.' , 'akismet'), '<a href="https://akismet.com/plugin-signup/" target="_blank">Akismet.com</a>' ) . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'setup-manual',
						'title'		=> __( 'Enter an API Key' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Setup' , 'akismet') . '</strong></p>' .
							'<p>' . esc_html__( 'If you already have an API key' , 'akismet') . '</p>' .
							'<ol>' .
								'<li>' . esc_html__( 'Copy and paste the API key into the text field.' , 'akismet') . '</li>' .
								'<li>' . esc_html__( 'Click the Use this Key button.' , 'akismet') . '</li>' .
							'</ol>',
					)
				);
			}
			elseif ( isset( $_GET['view'] ) && $_GET['view'] == 'stats' ) {
				//stats page
				$current_screen->add_help_tab(
					array(
						'id'		=> 'overview',
						'title'		=> __( 'Overview' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Stats' , 'akismet') . '</strong></p>' .
							'<p>' . esc_html__( 'Akismet filters out your comment and trackback spam for you, so you can focus on more important things.' , 'akismet') . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to view stats on spam filtered on your site.' , 'akismet') . '</p>',
					)
				);
			}
			else {
				//configuration page
				$current_screen->add_help_tab(
					array(
						'id'		=> 'overview',
						'title'		=> __( 'Overview' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Configuration' , 'akismet') . '</strong></p>' .
							'<p>' . esc_html__( 'Akismet filters out your comment and trackback spam for you, so you can focus on more important things.' , 'akismet') . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to enter/remove an API key, view account information and view spam stats.' , 'akismet') . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'settings',
						'title'		=> __( 'Settings' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Configuration' , 'akismet') . '</strong></p>' .
							'<p><strong>' . esc_html__( 'API Key' , 'akismet') . '</strong> - ' . esc_html__( 'Enter/remove an API key.' , 'akismet') . '</p>' .
							'<p><strong>' . esc_html__( 'Comments' , 'akismet') . '</strong> - ' . esc_html__( 'Show the number of approved comments beside each comment author in the comments list page.' , 'akismet') . '</p>' .
							'<p><strong>' . esc_html__( 'Strictness' , 'akismet') . '</strong> - ' . esc_html__( 'Choose to either discard the worst spam automatically or to always put all spam in spam folder.' , 'akismet') . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'		=> 'account',
						'title'		=> __( 'Account' , 'akismet'),
						'content'	=>
							'<p><strong>' . esc_html__( 'Akismet Configuration' , 'akismet') . '</strong></p>' .
							'<p><strong>' . esc_html__( 'Subscription Type' , 'akismet') . '</strong> - ' . esc_html__( 'The Akismet subscription plan' , 'akismet') . '</p>' .
							'<p><strong>' . esc_html__( 'Status' , 'akismet') . '</strong> - ' . esc_html__( 'The subscription status - active, cancelled or suspended' , 'akismet') . '</p>',
					)
				);
			}
		}

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:' , 'akismet') . '</strong></p>' .
			'<p><a href="https://akismet.com/faq/" target="_blank">'     . esc_html__( 'Akismet FAQ' , 'akismet') . '</a></p>' .
			'<p><a href="https://akismet.com/support/" target="_blank">' . esc_html__( 'Akismet Support' , 'akismet') . '</a></p>'
		);
	}

	public static function enter_api_key() {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?', 'akismet'));

		if ( !wp_verify_nonce( $_POST['_wpnonce'], self::NONCE ) )
			return false;

		foreach( array( 'akismet_strictness', 'akismet_show_user_comments_approved' ) as $option ) {
			update_option( $option, isset( $_POST[$option] ) && (int) $_POST[$option] == 1 ? '1' : '0' );
		}

		if ( defined( 'WPCOM_API_KEY' ) )
			return false; //shouldn't have option to save key if already defined

		$new_key = preg_replace( '/[^a-h0-9]/i', '', $_POST['key'] );
		$old_key = Akismet::get_api_key();

		if ( empty( $new_key ) ) {
			if ( !empty( $old_key ) ) {
				delete_option( 'wordpress_api_key' );
				self::$notices[] = 'new-key-empty';
			}
		}
		elseif ( $new_key != $old_key ) {
			self::save_key( $new_key );
		}

		return true;
	}

	public static function save_key( $api_key ) {
		$key_status = Akismet::verify_key( $api_key );

		if ( $key_status == 'valid' ) {
			$akismet_user = self::get_akismet_user( $api_key );
			
			if ( $akismet_user ) {				
				if ( in_array( $akismet_user->status, array( 'active', 'active-dunning', 'no-sub' ) ) )
					update_option( 'wordpress_api_key', $api_key );
				
				if (  $akismet_user->status == 'active' )
					self::$notices['status'] = 'new-key-valid';
				else
					self::$notices['status'] = $akismet_user->status;
			}
			else
				self::$notices['status'] = 'new-key-invalid';
		}
		elseif ( in_array( $key_status, array( 'invalid', 'failed' ) ) )
			self::$notices['status'] = 'new-key-'.$key_status;
	}

	public static function dashboard_stats() {
		if ( !function_exists('did_action') || did_action( 'rightnow_end' ) )
			return; // We already displayed this info in the "Right Now" section

		if ( !$count = get_option('akismet_spam_count') )
			return;

		global $submenu;

		echo '<h3>' . esc_html( _x( 'Spam', 'comments' , 'akismet') ) . '</h3>';

		echo '<p>'.sprintf( _n(
				'<a href="%1$s">Akismet</a> has protected your site from <a href="%2$s">%3$s spam comment</a>.',
				'<a href="%1$s">Akismet</a> has protected your site from <a href="%2$s">%3$s spam comments</a>.',
				$count
			, 'akismet'), 'https://akismet.com/wordpress/', esc_url( add_query_arg( array( 'page' => 'akismet-admin' ), admin_url( isset( $submenu['edit-comments.php'] ) ? 'edit-comments.php' : 'edit.php' ) ) ), number_format_i18n($count) ).'</p>';
	}

	// WP 2.5+
	public static function rightnow_stats() {
		global $submenu, $wp_db_version;

		if ( 8645 < $wp_db_version  ) // 2.7
			$link = add_query_arg( array( 'comment_status' => 'spam' ), admin_url( 'edit-comments.php' ) );
		elseif ( isset( $submenu['edit-comments.php'] ) )
			$link = add_query_arg( array( 'page' => 'akismet-admin' ), admin_url( 'edit-comments.php' ) );
		else
			$link = add_query_arg( array( 'page' => 'akismet-admin' ), admin_url( 'edit.php' ) );

		if ( $count = get_option('akismet_spam_count') ) {
			$intro = sprintf( _n(
				'<a href="%1$s">Akismet</a> has protected your site from %2$s spam comment already. ',
				'<a href="%1$s">Akismet</a> has protected your site from %2$s spam comments already. ',
				$count
			, 'akismet'), 'https://akismet.com/wordpress/', number_format_i18n( $count ) );
		} else {
			$intro = sprintf( __('<a href="%s">Akismet</a> blocks spam from getting to your blog. ', 'akismet'), 'https://akismet.com/wordpress/' );
		}

		$link = function_exists( 'esc_url' ) ? esc_url( $link ) : clean_url( $link );
		if ( $queue_count = self::get_spam_count() ) {
			$queue_text = sprintf( _n(
				'There&#8217;s <a href="%2$s">%1$s comment</a> in your spam queue right now.',
				'There are <a href="%2$s">%1$s comments</a> in your spam queue right now.',
				$queue_count
			, 'akismet'), number_format_i18n( $queue_count ), $link );
		} else {
			$queue_text = sprintf( __( "There&#8217;s nothing in your <a href='%s'>spam queue</a> at the moment." , 'akismet'), $link );
		}

		$text = $intro . '<br />' . $queue_text;
		echo "<p class='akismet-right-now'>$text</p>\n";
	}

	public static function check_for_spam_button( $comment_status ) {
		if ( 'approved' == $comment_status )
			return;

		if ( function_exists('plugins_url') )
			$link = add_query_arg( array( 'action' => 'akismet_recheck_queue' ), admin_url( 'admin.php' ) );
		else
			$link = add_query_arg( array( 'page' => 'akismet-admin', 'recheckqueue' => 'true', 'noheader' => 'true' ), admin_url( 'edit-comments.php' ) );

		echo '</div><div class="alignleft"><a class="button-secondary checkforspam" href="' . esc_url( $link ) . '">' . esc_html__('Check for Spam', 'akismet') . '</a><span class="checkforspam-spinner"></span>';
	}

	public static function recheck_queue() {
		global $wpdb;

		Akismet::fix_scheduled_recheck();

		if ( ! ( isset( $_GET['recheckqueue'] ) || ( isset( $_REQUEST['action'] ) && 'akismet_recheck_queue' == $_REQUEST['action'] ) ) )
			return;

		$paginate = '';
		if ( isset( $_POST['limit'] ) && isset( $_POST['offset'] ) ) {
			$paginate = $wpdb->prepare( " LIMIT %d OFFSET %d", array( $_POST['limit'], $_POST['offset'] ) );
		}
		$moderation = $wpdb->get_results( "SELECT * FROM {$wpdb->comments} WHERE comment_approved = '0'{$paginate}", ARRAY_A );

		foreach ( (array) $moderation as $c ) {
			$c['user_ip']      = $c['comment_author_IP'];
			$c['user_agent']   = $c['comment_agent'];
			$c['referrer']     = '';
			$c['blog']         = get_bloginfo('url');
			$c['blog_lang']    = get_locale();
			$c['blog_charset'] = get_option('blog_charset');
			$c['permalink']    = get_permalink($c['comment_post_ID']);

			$c['user_role'] = '';
			if ( isset( $c['user_ID'] ) )
				$c['user_role'] = Akismet::get_user_roles($c['user_ID']);

			if ( Akismet::is_test_mode() )
				$c['is_test'] = 'true';

			add_comment_meta( $c['comment_ID'], 'akismet_rechecking', true );

			$response = Akismet::http_post( Akismet::build_query( $c ), 'comment-check' );
			
			if ( 'true' == $response[1] ) {
				wp_set_comment_status( $c['comment_ID'], 'spam' );
				update_comment_meta( $c['comment_ID'], 'akismet_result', 'true' );
				delete_comment_meta( $c['comment_ID'], 'akismet_error' );
				delete_comment_meta( $c['comment_ID'], 'akismet_delayed_moderation_email' );
				Akismet::update_comment_history( $c['comment_ID'], __('Akismet re-checked and caught this comment as spam', 'akismet'), 'check-spam' );

			} elseif ( 'false' == $response[1] ) {
				update_comment_meta( $c['comment_ID'], 'akismet_result', 'false' );
				delete_comment_meta( $c['comment_ID'], 'akismet_error' );
				delete_comment_meta( $c['comment_ID'], 'akismet_delayed_moderation_email' );
				Akismet::update_comment_history( $c['comment_ID'], __('Akismet re-checked and cleared this comment', 'akismet'), 'check-ham' );
			// abnormal result: error
			} else {
				update_comment_meta( $c['comment_ID'], 'akismet_result', 'error' );
				Akismet::update_comment_history( $c['comment_ID'], sprintf( __('Akismet was unable to re-check this comment (response: %s)', 'akismet'), substr($response[1], 0, 50)), 'check-error' );
			}

			delete_comment_meta( $c['comment_ID'], 'akismet_rechecking' );
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_send_json( array(
				'processed' => count((array) $moderation),
			));
		}
		else {
			$redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : admin_url( 'edit-comments.php' );
			wp_safe_redirect( $redirect_to );
			exit;
		}
	}

	// Adds an 'x' link next to author URLs, clicking will remove the author URL and show an undo link
	public static function remove_comment_author_url() {
		if ( !empty( $_POST['id'] ) && check_admin_referer( 'comment_author_url_nonce' ) ) {
			$comment = get_comment( intval( $_POST['id'] ), ARRAY_A );
			if ( $comment && current_user_can( 'edit_comment', $comment['comment_ID'] ) ) {
				$comment['comment_author_url'] = '';
				do_action( 'comment_remove_author_url' );
				print( wp_update_comment( $comment ) );
				die();
			}
		}
	}

	public static function add_comment_author_url() {
		if ( !empty( $_POST['id'] ) && !empty( $_POST['url'] ) && check_admin_referer( 'comment_author_url_nonce' ) ) {
			$comment = get_comment( intval( $_POST['id'] ), ARRAY_A );
			if ( $comment && current_user_can( 'edit_comment', $comment['comment_ID'] ) ) {
				$comment['comment_author_url'] = esc_url( $_POST['url'] );
				do_action( 'comment_add_author_url' );
				print( wp_update_comment( $comment ) );
				die();
			}
		}
	}

	public static function comment_row_action( $a, $comment ) {

		// failsafe for old WP versions
		if ( !function_exists('add_comment_meta') )
			return $a;

		$akismet_result = get_comment_meta( $comment->comment_ID, 'akismet_result', true );
		$akismet_error  = get_comment_meta( $comment->comment_ID, 'akismet_error', true );
		$user_result    = get_comment_meta( $comment->comment_ID, 'akismet_user_result', true);
		$comment_status = wp_get_comment_status( $comment->comment_ID );
		$desc = null;
		if ( $akismet_error ) {
			$desc = __( 'Awaiting spam check' , 'akismet');
		} elseif ( !$user_result || $user_result == $akismet_result ) {
			// Show the original Akismet result if the user hasn't overridden it, or if their decision was the same
			if ( $akismet_result == 'true' && $comment_status != 'spam' && $comment_status != 'trash' )
				$desc = __( 'Flagged as spam by Akismet' , 'akismet');
			elseif ( $akismet_result == 'false' && $comment_status == 'spam' )
				$desc = __( 'Cleared by Akismet' , 'akismet');
		} else {
			$who = get_comment_meta( $comment->comment_ID, 'akismet_user', true );
			if ( $user_result == 'true' )
				$desc = sprintf( __('Flagged as spam by %s', 'akismet'), $who );
			else
				$desc = sprintf( __('Un-spammed by %s', 'akismet'), $who );
		}

		// add a History item to the hover links, just after Edit
		if ( $akismet_result ) {
			$b = array();
			foreach ( $a as $k => $item ) {
				$b[ $k ] = $item;
				if (
					$k == 'edit'
					|| ( $k == 'unspam' && $GLOBALS['wp_version'] >= 3.4 )
				) {
					$b['history'] = '<a href="comment.php?action=editcomment&amp;c='.$comment->comment_ID.'#akismet-status" title="'. esc_attr__( 'View comment history' , 'akismet') . '"> '. esc_html__('History', 'akismet') . '</a>';
				}
			}

			$a = $b;
		}

		if ( $desc )
			echo '<span class="akismet-status" commentid="'.$comment->comment_ID.'"><a href="comment.php?action=editcomment&amp;c='.$comment->comment_ID.'#akismet-status" title="' . esc_attr__( 'View comment history' , 'akismet') . '">'.esc_html( $desc ).'</a></span>';

		$show_user_comments = apply_filters( 'akismet_show_user_comments_approved', get_option('akismet_show_user_comments_approved') );
		$show_user_comments = $show_user_comments === 'false' ? false : $show_user_comments; //option used to be saved as 'false' / 'true'
		
		if ( $show_user_comments ) {
			$comment_count = Akismet::get_user_comments_approved( $comment->user_id, $comment->comment_author_email, $comment->comment_author, $comment->comment_author_url );
			$comment_count = intval( $comment_count );
			echo '<span class="akismet-user-comment-count" commentid="'.$comment->comment_ID.'" style="display:none;"><br><span class="akismet-user-comment-counts">'. sprintf( esc_html( _n( '%s approved', '%s approved', $comment_count , 'akismet') ), number_format_i18n( $comment_count ) ) . '</span></span>';
		}

		return $a;
	}

	public static function comment_status_meta_box( $comment ) {
		$history = Akismet::get_comment_history( $comment->comment_ID );

		if ( $history ) {
			echo '<div class="akismet-history" style="margin: 13px;">';
			foreach ( $history as $row ) {
				$time = date( 'D d M Y @ h:i:m a', $row['time'] ) . ' GMT';
				echo '<div style="margin-bottom: 13px;"><span style="color: #999;" alt="' . $time . '" title="' . $time . '">' . sprintf( esc_html__('%s ago', 'akismet'), human_time_diff( $row['time'] ) ) . '</span> - ';
				echo esc_html( $row['message'] ) . '</div>';
			}
			echo '</div>';
		}
	}

	public static function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( AKISMET__PLUGIN_URL . '/akismet.php' ) ) {
			$links[] = '<a href="' . esc_url( self::get_page_url() ) . '">'.esc_html__( 'Settings' , 'akismet').'</a>';
		}

		return $links;
	}

	public static function text_add_link_callback( $m ) {
		// bare link?
		if ( $m[4] == $m[2] )
			return '<a '.$m[1].' href="'.$m[2].'" '.$m[3].' class="comment-link">'.$m[4].'</a>';
		else
			return '<span title="'.$m[2].'" class="comment-link"><a '.$m[1].' href="'.$m[2].'" '.$m[3].' class="comment-link">'.$m[4].'</a></span>';
	}

	public static function text_add_link_class( $comment_text ) {
		return preg_replace_callback( '#<a ([^>]*)href="([^"]+)"([^>]*)>(.*?)</a>#i', array( 'Akismet_Admin', 'text_add_link_callback' ), $comment_text );
	}

	// Total spam in queue
	// get_option( 'akismet_spam_count' ) is the total caught ever
	public static function get_spam_count( $type = false ) {
		global $wpdb;

		if ( !$type ) { // total
			$count = wp_cache_get( 'akismet_spam_count', 'widget' );
			if ( false === $count ) {
				if ( function_exists('wp_count_comments') ) {
					$count = wp_count_comments();
					$count = $count->spam;
				} else {
					$count = (int) $wpdb->get_var("SELECT COUNT(comment_ID) FROM {$wpdb->comments} WHERE comment_approved = 'spam'");
				}
				wp_cache_set( 'akismet_spam_count', $count, 'widget', 3600 );
			}
			return $count;
		} elseif ( 'comments' == $type || 'comment' == $type ) { // comments
			$type = '';
		}

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(comment_ID) FROM {$wpdb->comments} WHERE comment_approved = 'spam' AND comment_type = %s", $type ) );
	}

	// Check connectivity between the WordPress blog and Akismet's servers.
	// Returns an associative array of server IP addresses, where the key is the IP address, and value is true (available) or false (unable to connect).
	public static function check_server_ip_connectivity() {
		
		$servers = $ips = array();

		// Some web hosts may disable this function
		if ( function_exists('gethostbynamel') ) {	
			
			$ips = gethostbynamel( 'rest.akismet.com' );
			if ( $ips && is_array($ips) && count($ips) ) {
				$api_key = Akismet::get_api_key();
				
				foreach ( $ips as $ip ) {
					$response = Akismet::verify_key( $api_key, $ip );
					// even if the key is invalid, at least we know we have connectivity
					if ( $response == 'valid' || $response == 'invalid' )
						$servers[$ip] = 'connected';
					else
						$servers[$ip] = $response ? $response : 'unable to connect';
				}
			}
		}
		
		return $servers;
	}
	
	// Simpler connectivity check
	public static function check_server_connectivity($cache_timeout = 86400) {
		
		$debug = array();
		$debug[ 'PHP_VERSION' ]         = PHP_VERSION;
		$debug[ 'WORDPRESS_VERSION' ]   = $GLOBALS['wp_version'];
		$debug[ 'AKISMET_VERSION' ]     = AKISMET_VERSION;
		$debug[ 'AKISMET__PLUGIN_DIR' ] = AKISMET__PLUGIN_DIR;
		$debug[ 'SITE_URL' ]            = site_url();
		$debug[ 'HOME_URL' ]            = home_url();
		
		$servers = get_option('akismet_available_servers');
		if ( (time() - get_option('akismet_connectivity_time') < $cache_timeout) && $servers !== false ) {
			$servers = self::check_server_ip_connectivity();
			update_option('akismet_available_servers', $servers);
			update_option('akismet_connectivity_time', time());
		}
			
		$response = wp_remote_get( 'http://rest.akismet.com/1.1/test' );
		
		$debug[ 'gethostbynamel' ]  = function_exists('gethostbynamel') ? 'exists' : 'not here';
		$debug[ 'Servers' ]         = $servers;
		$debug[ 'Test Connection' ] = $response;
		
		Akismet::log( $debug );
		
		if ( $response && 'connected' == wp_remote_retrieve_body( $response ) )
			return true;
		
		return false;
	}

	// Check the server connectivity and store the available servers in an option. 
	public static function get_server_connectivity($cache_timeout = 86400) {
		return self::check_server_connectivity( $cache_timeout );
	}

	public static function get_number_spam_waiting() {
		global $wpdb;
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->commentmeta} WHERE meta_key = 'akismet_error'" );
	}

	public static function get_page_url( $page = 'config' ) {

		$args = array( 'page' => 'akismet-key-config' );

		if ( $page == 'stats' )
			$args = array( 'page' => 'akismet-key-config', 'view' => 'stats' );
		elseif ( $page == 'delete_key' )
			$args = array( 'page' => 'akismet-key-config', 'view' => 'start', 'action' => 'delete-key', '_wpnonce' => wp_create_nonce( self::NONCE ) );

		$url = add_query_arg( $args, class_exists( 'Jetpack' ) ? admin_url( 'admin.php' ) : admin_url( 'options-general.php' ) );

		return $url;
	}
	
	public static function get_akismet_user( $api_key ) {
		$akismet_user = Akismet::http_post( Akismet::build_query( array( 'key' => $api_key ) ), 'get-subscription' );

		if ( ! empty( $akismet_user[1] ) )
			$akismet_user = json_decode( $akismet_user[1] );
		else
			$akismet_user = false;
			
		return $akismet_user;
	}
	
	public static function get_stats( $api_key ) {
		$stat_totals = array();

		foreach( array( '6-months', 'all' ) as $interval ) {
			$response = Akismet::http_post( Akismet::build_query( array( 'blog' => urlencode( get_bloginfo('url') ), 'key' => $api_key, 'from' => $interval ) ), 'get-stats' );

			if ( ! empty( $response[1] ) ) {
				$stat_totals[$interval] = json_decode( $response[1] );
			}
		}
		return $stat_totals;
	}
	
	public static function verify_wpcom_key( $api_key, $user_id, $token = '' ) {
		$akismet_account = Akismet::http_post( Akismet::build_query( array(
			'user_id'          => $user_id,
			'api_key'          => $api_key,
			'token'            => $token,
			'get_account_type' => 'true'
		) ), 'verify-wpcom-key' );

		if ( ! empty( $akismet_account[1] ) )
			$akismet_account = json_decode( $akismet_account[1] );

		Akismet::log( compact( 'akismet_account' ) );
		
		return $akismet_account;
	}

	public static function display_alert() {
		Akismet::view( 'notice', array(
			'type' => 'alert',
			'code' => (int) get_option( 'akismet_alert_code' ),
			'msg'  => get_option( 'akismet_alert_msg' )
		) );
	}

	public static function display_spam_check_warning() {
		Akismet::fix_scheduled_recheck();

		if ( wp_next_scheduled('akismet_schedule_cron_recheck') > time() && self::get_number_spam_waiting() > 0 ) {
			$link_text = apply_filters( 'akismet_spam_check_warning_link_text', sprintf( __( 'Please check your <a href="%s">Akismet configuration</a> and contact your web host if problems persist.', 'akismet'), esc_url( self::get_page_url() ) ) );
			Akismet::view( 'notice', array( 'type' => 'spam-check', 'link_text' => $link_text ) );
		}
	}

	public static function display_invalid_version() {
		Akismet::view( 'notice', array( 'type' => 'version' ) );
	}

	public static function display_api_key_warning() {
		Akismet::view( 'notice', array( 'type' => 'plugin' ) );
	}

	public static function display_page() {
		if ( !Akismet::get_api_key() || ( isset( $_GET['view'] ) && $_GET['view'] == 'start' ) )
			self::display_start_page();
		elseif ( isset( $_GET['view'] ) && $_GET['view'] == 'stats' )
			self::display_stats_page();
		else
			self::display_configuration_page();
	}

	public static function display_start_page() {
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'delete-key' ) {
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], self::NONCE ) )
					delete_option( 'wordpress_api_key' );
			}
		}

		if ( $api_key = Akismet::get_api_key() ) {
			self::display_configuration_page();
			return;
		}
		
		//the user can choose to auto connect their API key by clicking a button on the akismet done page
		//if jetpack, get verified api key by using connected wpcom user id
		//if no jetpack, get verified api key by using an akismet token	
		
		$akismet_user = false;
		
		if ( isset( $_GET['token'] ) && preg_match('/^(\d+)-[0-9a-f]{20}$/', $_GET['token'] ) )
			$akismet_user = self::verify_wpcom_key( '', '', $_GET['token'] );
		elseif ( $jetpack_user = self::get_jetpack_user() )
			$akismet_user = self::verify_wpcom_key( $jetpack_user['api_key'], $jetpack_user['user_id'] );
			
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'save-key' ) {
				if ( is_object( $akismet_user ) ) {
					self::save_key( $akismet_user->api_key );
					self::display_notice();
					self::display_configuration_page();
					return;				
				}
			}
		}

		echo '<h2 class="ak-header">'.esc_html__('Akismet', 'akismet').'</h2>';

		self::display_status();

		Akismet::view( 'start', compact( 'akismet_user' ) );
	}

	public static function display_stats_page() {
		Akismet::view( 'stats' );
	}

	public static function display_configuration_page() {
		$api_key      = Akismet::get_api_key();
		$akismet_user = self::get_akismet_user( $api_key );
		$stat_totals  = self::get_stats( $api_key );
		
		// If unset, create the new strictness option using the old discard option to determine its default
       	if ( get_option( 'akismet_strictness' ) === false )
        	add_option( 'akismet_strictness', (get_option('akismet_discard_month') === 'true' ? '1' : '0') );

		if ( empty( self::$notices ) ) {
			//show status
			if ( ! empty( $stat_totals['all'] ) && isset( $stat_totals['all']->time_saved ) && $akismet_user->status == 'active' && $akismet_user->account_type == 'free-api-key' ) {

				$time_saved = false;

				if ( $stat_totals['all']->time_saved > 1800 ) {
					$total_in_minutes = round( $stat_totals['all']->time_saved / 60 );
					$total_in_hours   = round( $total_in_minutes / 60 );
					$total_in_days    = round( $total_in_hours / 8 );
					$cleaning_up      = __( 'Cleaning up spam takes time.' , 'akismet');

					if ( $total_in_days > 1 )
						$time_saved = $cleaning_up . ' ' . sprintf( __( 'Since you joined us, Akismet has saved you %s days!' , 'akismet'), number_format_i18n( $total_in_days ) );
					elseif ( $total_in_hours > 1 )
						$time_saved = $cleaning_up . ' ' . sprintf( __( 'Since you joined us, Akismet has saved you %d hours!' , 'akismet'), $total_in_hours );
					elseif ( $total_in_minutes >= 30 )
						$time_saved = $cleaning_up . ' ' . sprintf( __( 'Since you joined us, Akismet has saved you %d minutes!' , 'akismet'), $total_in_minutes );
				}

				Akismet::view( 'notice', array( 'type' => 'active-notice', 'time_saved' => $time_saved ) );
			}
			
			if ( !empty( $akismet_user->limit_reached ) && in_array( $akismet_user->limit_reached, array( 'yellow', 'red' ) ) ) {
				Akismet::view( 'notice', array( 'type' => 'limit-reached', 'level' => $akismet_user->limit_reached ) );
			}
		}
		
		if ( !isset( self::$notices['status'] ) && in_array( $akismet_user->status, array( 'cancelled', 'suspended', 'missing', 'no-sub' ) ) )	
			Akismet::view( 'notice', array( 'type' => $akismet_user->status ) );

		Akismet::log( compact( 'stat_totals', 'akismet_user' ) );
		Akismet::view( 'config', compact( 'api_key', 'akismet_user', 'stat_totals' ) );
	}

	public static function display_notice() {
		global $hook_suffix;

		if ( in_array( $hook_suffix, array( 'jetpack_page_akismet-key-config', 'settings_page_akismet-key-config', 'edit-comments.php' ) ) && (int) get_option( 'akismet_alert_code' ) > 0 ) {
			self::display_alert();
		}
		elseif ( $hook_suffix == 'plugins.php' && !Akismet::get_api_key() ) {
			self::display_api_key_warning();
		}
		elseif ( $hook_suffix == 'edit-comments.php' && wp_next_scheduled( 'akismet_schedule_cron_recheck' ) ) {
			self::display_spam_check_warning();
		}
		elseif ( in_array( $hook_suffix, array( 'jetpack_page_akismet-key-config', 'settings_page_akismet-key-config' ) ) && Akismet::get_api_key() ) {
			self::display_status();
		}
	}

	public static function display_status() {
		$type = '';

		if ( !self::get_server_connectivity() )
			$type = 'servers-be-down';

		if ( !empty( $type ) )
			Akismet::view( 'notice', compact( 'type' ) );
		elseif ( !empty( self::$notices ) ) {
			foreach ( self::$notices as $type )
				Akismet::view( 'notice', compact( 'type' ) );
		}
	}

	private static function get_jetpack_user() {
		if ( !class_exists('Jetpack') )
			return false;

		Jetpack::load_xml_rpc_client();
		$xml = new Jetpack_IXR_ClientMulticall( array( 'user_id' => get_current_user_id() ) );

		$xml->addCall( 'wpcom.getUserID' );
		$xml->addCall( 'akismet.getAPIKey' );
		$xml->query();

		Akismet::log( compact( 'xml' ) );

		if ( !$xml->isError() ) {
			$responses = $xml->getResponse();
			if ( count( $responses ) > 1 ) {
				$api_key = array_shift( $responses[0] );
				$user_id = (int) array_shift( $responses[1] );
				return compact( 'api_key', 'user_id' );
			}
		}
		return false;
	}
	
	/**
	 * Some commentmeta isn't useful in an export file. Suppress it (when supported).
	 *
	 * @param bool $exclude
	 * @param string $key The meta key
	 * @param object $meta The meta object
	 * @return bool Whether to exclude this meta entry from the export.
	 */
	public static function exclude_commentmeta_from_export( $exclude, $key, $meta ) {
		if ( in_array( $key, array( 'akismet_as_submitted', 'akismet_rechecking', 'akismet_delayed_moderation_email' ) ) ) {
			return true;
		}
		
		return $exclude;
	}
}