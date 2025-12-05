<?php

// We plan to gradually remove all of the disabled lint rules below.
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
// phpcs:disable Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

class Akismet_Admin {

	const NONCE = 'akismet-update-key';

	const NOTICE_EXISTING_KEY_INVALID = 'existing-key-invalid';

	private static $initiated = false;
	private static $notices   = array();
	private static $allowed   = array(
		'a'      => array(
			'href'  => true,
			'title' => true,
		),
		'b'      => array(),
		'code'   => array(),
		'del'    => array(
			'datetime' => true,
		),
		'em'     => array(),
		'i'      => array(),
		'q'      => array(
			'cite' => true,
		),
		'strike' => array(),
		'strong' => array(),
	);

	/**
	 * List of pages where activation banner should be displayed.
	 *
	 * @var array
	 */
	private static $activation_banner_pages = array(
		'edit-comments.php',
		'options-discussion.php',
		'plugins.php',
	);

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
		add_action( 'admin_menu', array( 'Akismet_Admin', 'admin_menu' ), 5 ); // Priority 5, so it's called before Jetpack's admin_menu.
		add_action( 'admin_notices', array( 'Akismet_Admin', 'display_notice' ) );
		add_action( 'admin_enqueue_scripts', array( 'Akismet_Admin', 'load_resources' ) );
		add_action( 'activity_box_end', array( 'Akismet_Admin', 'dashboard_stats' ) );
		add_action( 'rightnow_end', array( 'Akismet_Admin', 'rightnow_stats' ) );
		add_action( 'manage_comments_nav', array( 'Akismet_Admin', 'check_for_spam_button' ) );
		add_action( 'admin_action_akismet_recheck_queue', array( 'Akismet_Admin', 'recheck_queue' ) );
		add_action( 'wp_ajax_akismet_recheck_queue', array( 'Akismet_Admin', 'recheck_queue' ) );
		add_action( 'wp_ajax_comment_author_deurl', array( 'Akismet_Admin', 'remove_comment_author_url' ) );
		add_action( 'wp_ajax_comment_author_reurl', array( 'Akismet_Admin', 'add_comment_author_url' ) );
		add_action( 'jetpack_auto_activate_akismet', array( 'Akismet_Admin', 'connect_jetpack_user' ) );

		add_filter( 'plugin_action_links', array( 'Akismet_Admin', 'plugin_action_links' ), 10, 2 );
		add_filter( 'comment_row_actions', array( 'Akismet_Admin', 'comment_row_action' ), 10, 2 );

		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'akismet.php' ), array( 'Akismet_Admin', 'admin_plugin_settings_link' ) );

		add_filter( 'wxr_export_skip_commentmeta', array( 'Akismet_Admin', 'exclude_commentmeta_from_export' ), 10, 3 );

		add_filter( 'all_plugins', array( 'Akismet_Admin', 'modify_plugin_description' ) );

		// priority=1 because we need ours to run before core's comment anonymizer runs, and that's registered at priority=10
		add_filter( 'wp_privacy_personal_data_erasers', array( 'Akismet_Admin', 'register_personal_data_eraser' ), 1 );
	}

	public static function admin_init() {
		if ( get_option( 'Activated_Akismet' ) ) {
			delete_option( 'Activated_Akismet' );
			if ( ! headers_sent() ) {
				$admin_url = self::get_page_url( 'init' );
				wp_redirect( $admin_url );
			}
		}

		add_meta_box( 'akismet-status', __( 'Comment History', 'akismet' ), array( 'Akismet_Admin', 'comment_status_meta_box' ), 'comment', 'normal' );

		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content(
				__( 'Akismet', 'akismet' ),
				__( 'We collect information about visitors who comment on Sites that use our Akismet Anti-spam service. The information we collect depends on how the User sets up Akismet for the Site, but typically includes the commenter\'s IP address, user agent, referrer, and Site URL (along with other information directly provided by the commenter such as their name, username, email address, and the comment itself).', 'akismet' )
			);
		}
	}

	public static function admin_menu() {
		if ( class_exists( 'Jetpack' ) ) {
			add_action( 'jetpack_admin_menu', array( 'Akismet_Admin', 'load_menu' ) );
		} else {
			self::load_menu();
		}
	}

	public static function admin_head() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	}

	public static function admin_plugin_settings_link( $links ) {
		$settings_link = '<a href="' . esc_url( self::get_page_url() ) . '">' . __( 'Settings', 'akismet' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public static function load_menu() {
		if ( class_exists( 'Jetpack' ) ) {
			$hook = add_submenu_page( 'jetpack', __( 'Akismet Anti-spam', 'akismet' ), __( 'Akismet Anti-spam', 'akismet' ), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ) );
		} else {
			$hook = add_options_page( __( 'Akismet Anti-spam', 'akismet' ), __( 'Akismet Anti-spam', 'akismet' ), 'manage_options', 'akismet-key-config', array( 'Akismet_Admin', 'display_page' ) );
		}

		if ( $hook ) {
			add_action( "load-$hook", array( 'Akismet_Admin', 'admin_help' ) );
		}
	}

	public static function load_resources() {
		global $hook_suffix;

		if ( in_array(
			$hook_suffix,
			apply_filters(
				'akismet_admin_page_hook_suffixes',
				array_merge(
					array(
						'index.php', // dashboard
						'comment.php',
						'post.php',
						'settings_page_akismet-key-config',
						'jetpack_page_akismet-key-config',
					),
					self::$activation_banner_pages
				)
			)
		) ) {
			$akismet_css_path = is_rtl() ? '_inc/rtl/akismet-rtl.css' : '_inc/akismet.css';
			wp_register_style( 'akismet', plugin_dir_url( __FILE__ ) . $akismet_css_path, array(), self::get_asset_file_version( $akismet_css_path ) );
			wp_enqueue_style( 'akismet' );

			wp_register_style( 'akismet-font-inter', plugin_dir_url( __FILE__ ) . '_inc/fonts/inter.css', array(), self::get_asset_file_version( '_inc/fonts/inter.css' ) );
			wp_enqueue_style( 'akismet-font-inter' );

			$akismet_admin_css_path = is_rtl() ? '_inc/rtl/akismet-admin-rtl.css' : '_inc/akismet-admin.css';
			wp_register_style( 'akismet-admin', plugin_dir_url( __FILE__ ) . $akismet_admin_css_path, array(), self::get_asset_file_version( $akismet_admin_css_path ) );
			wp_enqueue_style( 'akismet-admin' );

			wp_add_inline_style( 'akismet-admin', self::get_inline_css() );

			wp_register_script( 'akismet.js', plugin_dir_url( __FILE__ ) . '_inc/akismet.js', array( 'jquery' ), self::get_asset_file_version( '_inc/akismet.js' ) );
			wp_enqueue_script( 'akismet.js' );

			wp_register_script( 'akismet-admin.js', plugin_dir_url( __FILE__ ) . '_inc/akismet-admin.js', array(), self::get_asset_file_version( '/_inc/akismet-admin.js' ) );
			wp_enqueue_script( 'akismet-admin.js' );

			$inline_js = array(
				'comment_author_url_nonce' => wp_create_nonce( 'comment_author_url_nonce' ),
				'strings'                  => array(
					'Remove this URL' => __( 'Remove this URL', 'akismet' ),
					'Removing...'     => __( 'Removing...', 'akismet' ),
					'URL removed'     => __( 'URL removed', 'akismet' ),
					'(undo)'          => __( '(undo)', 'akismet' ),
					'Re-adding...'    => __( 'Re-adding...', 'akismet' ),
				),
			);

			if ( isset( $_GET['akismet_recheck'] ) && wp_verify_nonce( $_GET['akismet_recheck'], 'akismet_recheck' ) ) {
				$inline_js['start_recheck'] = true;
			}

			if ( apply_filters( 'akismet_enable_mshots', true ) ) {
				$inline_js['enable_mshots'] = true;
			}

			wp_localize_script( 'akismet.js', 'WPAkismet', $inline_js );
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
			if ( ! Akismet::get_api_key() || ( isset( $_GET['view'] ) && $_GET['view'] == 'start' ) ) {
				// setup page
				$current_screen->add_help_tab(
					array(
						'id'      => 'overview',
						'title'   => __( 'Overview', 'akismet' ),
						'content' =>
							'<p><strong>' . esc_html__( 'Akismet Setup', 'akismet' ) . '</strong></p>' .
							'<p>' . esc_html__( 'Akismet filters out spam, so you can focus on more important things.', 'akismet' ) . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to set up the Akismet plugin.', 'akismet' ) . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'      => 'setup-signup',
						'title'   => __( 'New to Akismet', 'akismet' ),
						'content' =>
							'<p><strong>' . esc_html__( 'Akismet Setup', 'akismet' ) . '</strong></p>' .
							'<p>' . esc_html__( 'You need to enter an API key to activate the Akismet service on your site.', 'akismet' ) . '</p>' .
							/* translators: %s: a link to the signup page with the text 'Akismet.com'. */
							'<p>' . sprintf( __( 'Sign up for an account on %s to get an API Key.', 'akismet' ), '<a href="https://akismet.com/plugin-signup/" target="_blank">Akismet.com</a>' ) . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'      => 'setup-manual',
						'title'   => __( 'Enter an API Key', 'akismet' ),
						'content' =>
							'<p><strong>' . esc_html__( 'Akismet Setup', 'akismet' ) . '</strong></p>' .
							'<p>' . esc_html__( 'If you already have an API key', 'akismet' ) . '</p>' .
							'<ol>' .
							'<li>' . esc_html__( 'Copy and paste the API key into the text field.', 'akismet' ) . '</li>' .
							'<li>' . esc_html__( 'Click the Use this Key button.', 'akismet' ) . '</li>' .
							'</ol>',
					)
				);
			} elseif ( isset( $_GET['view'] ) && $_GET['view'] == 'stats' ) {
				// stats page
				$current_screen->add_help_tab(
					array(
						'id'      => 'overview',
						'title'   => __( 'Overview', 'akismet' ),
						'content' =>
							'<p><strong>' . esc_html__( 'Akismet Stats', 'akismet' ) . '</strong></p>' .
							'<p>' . esc_html__( 'Akismet filters out spam, so you can focus on more important things.', 'akismet' ) . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to view stats on spam filtered on your site.', 'akismet' ) . '</p>',
					)
				);
			} else {
				// configuration page
				$current_screen->add_help_tab(
					array(
						'id'      => 'overview',
						'title'   => __( 'Overview', 'akismet' ),
						'content' =>
							'<p><strong>' . esc_html__( 'Akismet Configuration', 'akismet' ) . '</strong></p>' .
							'<p>' . esc_html__( 'Akismet filters out spam, so you can focus on more important things.', 'akismet' ) . '</p>' .
							'<p>' . esc_html__( 'On this page, you are able to update your Akismet settings and view spam stats.', 'akismet' ) . '</p>',
					)
				);

				$current_screen->add_help_tab(
					array(
						'id'      => 'settings',
						'title'   => __( 'Settings', 'akismet' ),
						'content' =>
							'<p><strong>' . esc_html__( 'Akismet Configuration', 'akismet' ) . '</strong></p>' .
							( Akismet::predefined_api_key() ? '' : '<p><strong>' . esc_html__( 'API Key', 'akismet' ) . '</strong> - ' . esc_html__( 'Enter/remove an API key.', 'akismet' ) . '</p>' ) .
							'<p><strong>' . esc_html__( 'Comments', 'akismet' ) . '</strong> - ' . esc_html__( 'Show the number of approved comments beside each comment author in the comments list page.', 'akismet' ) . '</p>' .
							'<p><strong>' . esc_html__( 'Strictness', 'akismet' ) . '</strong> - ' . esc_html__( 'Choose to either discard the worst spam automatically or to always put all spam in spam folder.', 'akismet' ) . '</p>',
					)
				);

				if ( ! Akismet::predefined_api_key() ) {
					$current_screen->add_help_tab(
						array(
							'id'      => 'account',
							'title'   => __( 'Account', 'akismet' ),
							'content' =>
								'<p><strong>' . esc_html__( 'Akismet Configuration', 'akismet' ) . '</strong></p>' .
								'<p><strong>' . esc_html__( 'Subscription Type', 'akismet' ) . '</strong> - ' . esc_html__( 'The Akismet subscription plan', 'akismet' ) . '</p>' .
								'<p><strong>' . esc_html__( 'Status', 'akismet' ) . '</strong> - ' . esc_html__( 'The subscription status - active, cancelled or suspended', 'akismet' ) . '</p>',
						)
					);
				}
			}
		}

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:', 'akismet' ) . '</strong></p>' .
			'<p><a href="https://akismet.com/faq/" target="_blank">' . esc_html__( 'Akismet FAQ', 'akismet' ) . '</a></p>' .
			'<p><a href="https://akismet.com/support/" target="_blank">' . esc_html__( 'Akismet Support', 'akismet' ) . '</a></p>'
		);
	}

	public static function enter_api_key() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die( __( 'Cheatin&#8217; uh?', 'akismet' ) );
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], self::NONCE ) ) {
			return false;
		}

		foreach ( array( 'akismet_strictness', 'akismet_show_user_comments_approved' ) as $option ) {
			update_option( $option, isset( $_POST[ $option ] ) && (int) $_POST[ $option ] == 1 ? '1' : '0' );
		}

		if ( ! empty( $_POST['akismet_comment_form_privacy_notice'] ) ) {
			self::set_form_privacy_notice_option( $_POST['akismet_comment_form_privacy_notice'] );
		} else {
			self::set_form_privacy_notice_option( 'hide' );
		}

		if ( Akismet::predefined_api_key() ) {
			return false; // shouldn't have option to save key if already defined
		}

		$new_key = preg_replace( '/[^a-f0-9]/i', '', $_POST['key'] );
		$old_key = Akismet::get_api_key();

		if ( empty( $new_key ) ) {
			if ( ! empty( $old_key ) ) {
				delete_option( 'wordpress_api_key' );
				self::$notices[] = 'new-key-empty';
			}
		} elseif ( $new_key != $old_key ) {
			self::save_key( $new_key );
		}

		return true;
	}

	public static function save_key( $api_key ) {
		$key_status = Akismet::verify_key( $api_key );

		if ( $key_status == 'valid' ) {
			$akismet_user = self::get_akismet_user( $api_key );

			if ( $akismet_user ) {
				if ( in_array( $akismet_user->status, array( 'active', 'active-dunning', 'no-sub' ) ) ) {
					update_option( 'wordpress_api_key', $api_key );
				}

				if ( $akismet_user->status == 'active' ) {
					self::$notices['status'] = 'new-key-valid';
				} elseif ( $akismet_user->status == 'notice' ) {
					self::$notices['status'] = $akismet_user;
				} else {
					self::$notices['status'] = $akismet_user->status;
				}
			} else {
				self::$notices['status'] = 'new-key-invalid';
			}
		} elseif ( in_array( $key_status, array( 'invalid', 'failed' ) ) ) {
			self::$notices['status'] = 'new-key-' . $key_status;
		}
	}

	public static function dashboard_stats() {
		if ( did_action( 'rightnow_end' ) ) {
			return; // We already displayed this info in the "Right Now" section
		}

		if ( ! $count = get_option( 'akismet_spam_count' ) ) {
			return;
		}

		global $submenu;

		echo '<h3>' . esc_html( _x( 'Spam', 'comments', 'akismet' ) ) . '</h3>';

		echo '<p>' . sprintf(
			/* translators: 1: Akismet website URL, 2: Comments page URL, 3: Number of spam comments. */
			_n(
				'<a href="%1$s">Akismet</a> has protected your site from <a href="%2$s">%3$s spam comment</a>.',
				'<a href="%1$s">Akismet</a> has protected your site from <a href="%2$s">%3$s spam comments</a>.',
				$count,
				'akismet'
			),
			'https://akismet.com/wordpress/',
			esc_url( add_query_arg( array( 'page' => 'akismet-admin' ), admin_url( isset( $submenu['edit-comments.php'] ) ? 'edit-comments.php' : 'edit.php' ) ) ),
			number_format_i18n( $count )
		) . '</p>';
	}

	// WP 2.5+
	public static function rightnow_stats() {
		if ( $count = get_option( 'akismet_spam_count' ) ) {
			$intro = sprintf(
			/* translators: 1: Akismet website URL, 2: Number of spam comments. */
				_n(
					'<a href="%1$s">Akismet</a> has protected your site from %2$s spam comment already. ',
					'<a href="%1$s">Akismet</a> has protected your site from %2$s spam comments already. ',
					$count,
					'akismet'
				),
				'https://akismet.com/wordpress/',
				number_format_i18n( $count )
			);
		} else {
			/* translators: %s: Akismet website URL. */
			$intro = sprintf( __( '<a href="%s">Akismet</a> blocks spam from getting to your blog. ', 'akismet' ), 'https://akismet.com/wordpress/' );
		}

		$link = add_query_arg( array( 'comment_status' => 'spam' ), admin_url( 'edit-comments.php' ) );

		if ( $queue_count = self::get_spam_count() ) {
			$queue_text = sprintf(
			/* translators: 1: Number of comments, 2: Comments page URL. */
				_n(
					'There&#8217;s <a href="%2$s">%1$s comment</a> in your spam queue right now.',
					'There are <a href="%2$s">%1$s comments</a> in your spam queue right now.',
					$queue_count,
					'akismet'
				),
				number_format_i18n( $queue_count ),
				esc_url( $link )
			);
		} else {
			/* translators: %s: Comments page URL. */
			$queue_text = sprintf( __( "There&#8217;s nothing in your <a href='%s'>spam queue</a> at the moment.", 'akismet' ), esc_url( $link ) );
		}

		$text = $intro . '<br />' . $queue_text;
		echo "<p class='akismet-right-now'>$text</p>\n";
	}

	public static function check_for_spam_button( $comment_status ) {
		// The "Check for Spam" button should only appear when the page might be showing
		// a comment with comment_approved=0, which means an un-trashed, un-spammed,
		// not-yet-moderated comment.
		if ( 'all' != $comment_status && 'moderated' != $comment_status ) {
			return;
		}

		$link = '';

		$comments_count = wp_count_comments();

		echo '</div>';
		echo '<div class="alignleft actions">';

		$classes = array(
			'button-secondary',
			'checkforspam',
			'button-disabled',   // Disable button until the page is loaded
		);

		if ( $comments_count->moderated > 0 ) {
			$classes[] = 'enable-on-load';

			if ( ! Akismet::get_api_key() ) {
				$link      = self::get_page_url();
				$classes[] = 'ajax-disabled';
			}
		}

		echo '<a
				class="' . esc_attr( implode( ' ', $classes ) ) . '"' .
			( ! empty( $link ) ? ' href="' . esc_url( $link ) . '"' : '' ) .
			/* translators: The placeholder is for showing how much of the process has completed, as a percent. e.g., "Checking for Spam (40%)" */
			' data-progress-label="' . esc_attr( __( 'Checking for Spam (%1$s%)', 'akismet' ) ) . '"
				data-success-url="' . esc_attr(
				remove_query_arg(
					array( 'akismet_recheck', 'akismet_recheck_error' ),
					add_query_arg(
						array(
							'akismet_recheck_complete' => 1,
							'recheck_count'            => urlencode( '__recheck_count__' ),
							'spam_count'               => urlencode( '__spam_count__' ),
						)
					)
				)
			) . '"
				data-failure-url="' . esc_attr( remove_query_arg( array( 'akismet_recheck', 'akismet_recheck_complete' ), add_query_arg( array( 'akismet_recheck_error' => 1 ) ) ) ) . '"
				data-pending-comment-count="' . esc_attr( $comments_count->moderated ) . '"
				data-nonce="' . esc_attr( wp_create_nonce( 'akismet_check_for_spam' ) ) . '"
				' . ( ! in_array( 'ajax-disabled', $classes ) ? 'onclick="return false;"' : '' ) . '
				>' . esc_html__( 'Check for Spam', 'akismet' ) . '</a>';
		echo '<span class="checkforspam-spinner"></span>';
	}

	public static function recheck_queue() {
		global $wpdb;

		Akismet::fix_scheduled_recheck();

		if ( ! ( isset( $_GET['recheckqueue'] ) || ( isset( $_REQUEST['action'] ) && 'akismet_recheck_queue' == $_REQUEST['action'] ) ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'akismet_check_for_spam' ) ) {
			wp_send_json(
				array(
					'error' => __( 'You don&#8217;t have permission to do that.', 'akismet' ),
				)
			);
			return;
		}

		$result_counts = self::recheck_queue_portion( empty( $_POST['offset'] ) ? 0 : $_POST['offset'], empty( $_POST['limit'] ) ? 100 : $_POST['limit'] );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_send_json(
				array(
					'counts' => $result_counts,
				)
			);
		} else {
			$redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : admin_url( 'edit-comments.php' );
			wp_safe_redirect( $redirect_to );
			exit;
		}
	}

	public static function recheck_queue_portion( $start = 0, $limit = 100 ) {
		global $wpdb;

		$paginate = '';

		if ( $limit <= 0 ) {
			$limit = 100;
		}

		if ( $start < 0 ) {
			$start = 0;
		}

		$moderation = $wpdb->get_col( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_approved = '0' LIMIT %d OFFSET %d", $limit, $start ) );

		$result_counts = array(
			'processed' => is_countable( $moderation ) ? count( $moderation ) : 0,
			'spam'      => 0,
			'ham'       => 0,
			'error'     => 0,
		);

		foreach ( $moderation as $comment_id ) {
			$api_response = Akismet::recheck_comment( $comment_id, 'recheck_queue' );

			if ( 'true' === $api_response ) {
				++$result_counts['spam'];
			} elseif ( 'false' === $api_response ) {
				++$result_counts['ham'];
			} else {
				++$result_counts['error'];
			}
		}

		return $result_counts;
	}

	// Adds an 'x' link next to author URLs, clicking will remove the author URL and show an undo link
	public static function remove_comment_author_url() {
		if ( ! empty( $_POST['id'] ) && check_admin_referer( 'comment_author_url_nonce' ) ) {
			$comment_id = intval( $_POST['id'] );
			$comment    = get_comment( $comment_id, ARRAY_A );
			if ( $comment && current_user_can( 'edit_comment', $comment['comment_ID'] ) ) {
				$comment['comment_author_url'] = '';
				do_action( 'comment_remove_author_url' );
				print( wp_update_comment( $comment ) );
				die();
			}
		}
	}

	public static function add_comment_author_url() {
		if ( ! empty( $_POST['id'] ) && ! empty( $_POST['url'] ) && check_admin_referer( 'comment_author_url_nonce' ) ) {
			$comment_id = intval( $_POST['id'] );
			$comment    = get_comment( $comment_id, ARRAY_A );
			if ( $comment && current_user_can( 'edit_comment', $comment['comment_ID'] ) ) {
				$comment['comment_author_url'] = esc_url( $_POST['url'] );
				do_action( 'comment_add_author_url' );
				print( wp_update_comment( $comment ) );
				die();
			}
		}
	}

	public static function comment_row_action( $a, $comment ) {
		$akismet_result = get_comment_meta( $comment->comment_ID, 'akismet_result', true );
		if ( ! $akismet_result && get_comment_meta( $comment->comment_ID, 'akismet_skipped', true ) ) {
			$akismet_result = 'skipped'; // Akismet chose to skip the comment-check request.
		}

		$akismet_error  = get_comment_meta( $comment->comment_ID, 'akismet_error', true );
		$user_result    = get_comment_meta( $comment->comment_ID, 'akismet_user_result', true );
		$comment_status = wp_get_comment_status( $comment->comment_ID );
		$desc           = null;
		if ( $akismet_error ) {
			$desc = __( 'Awaiting spam check', 'akismet' );
		} elseif ( ! $user_result || $user_result == $akismet_result ) {
			// Show the original Akismet result if the user hasn't overridden it, or if their decision was the same
			if ( $akismet_result == 'true' && $comment_status != 'spam' && $comment_status != 'trash' ) {
				$desc = __( 'Flagged as spam by Akismet', 'akismet' );
			} elseif ( $akismet_result == 'false' && $comment_status == 'spam' ) {
				$desc = __( 'Cleared by Akismet', 'akismet' );
			}
		} else {
			$who = get_comment_meta( $comment->comment_ID, 'akismet_user', true );
			if ( $user_result == 'true' ) {
				/* translators: %s: Username. */
				$desc = sprintf( __( 'Flagged as spam by %s', 'akismet' ), $who );
			} else {
				/* translators: %s: Username. */
				$desc = sprintf( __( 'Un-spammed by %s', 'akismet' ), $who );
			}
		}

		// add a History item to the hover links, just after Edit
		if ( $akismet_result && is_array( $a ) ) {
			$b = array();
			foreach ( $a as $k => $item ) {
				$b[ $k ] = $item;
				if (
					$k == 'edit'
					|| $k == 'unspam'
				) {
					$b['history'] = '<a href="comment.php?action=editcomment&amp;c=' . $comment->comment_ID . '#akismet-status" title="' . esc_attr__( 'View comment history', 'akismet' ) . '"> ' . esc_html__( 'History', 'akismet' ) . '</a>';
				}
			}

			$a = $b;
		}

		if ( $desc ) {
			echo '<span class="akismet-status" commentid="' . $comment->comment_ID . '"><a href="comment.php?action=editcomment&amp;c=' . $comment->comment_ID . '#akismet-status" title="' . esc_attr__( 'View comment history', 'akismet' ) . '">' . esc_html( $desc ) . '</a></span>';
		}

		$show_user_comments_option = get_option( 'akismet_show_user_comments_approved' );

		if ( $show_user_comments_option === false ) {
			// Default to active if the user hasn't made a decision.
			$show_user_comments_option = '1';
		}

		$show_user_comments = apply_filters( 'akismet_show_user_comments_approved', $show_user_comments_option );
		$show_user_comments = $show_user_comments === 'false' ? false : $show_user_comments; // option used to be saved as 'false' / 'true'

		if ( $show_user_comments ) {
			$comment_count = Akismet::get_user_comments_approved( $comment->user_id, $comment->comment_author_email, $comment->comment_author, $comment->comment_author_url );
			$comment_count = intval( $comment_count );
			echo '<span class="akismet-user-comment-count" commentid="' . $comment->comment_ID . '" style="display:none;"><br><span class="akismet-user-comment-counts">';
			/* translators: %s: Number of comments. */
			echo sprintf( esc_html( _n( '%s approved', '%s approved', $comment_count, 'akismet' ) ), number_format_i18n( $comment_count ) ) . '</span></span>';
		}

		return $a;
	}

	public static function comment_status_meta_box( $comment ) {
		$history = Akismet::get_comment_history( $comment->comment_ID );

		if ( $history ) {
			foreach ( $history as $row ) {
				$message = '';

				if ( ! empty( $row['message'] ) ) {
					// Old versions of Akismet stored the message as a literal string in the commentmeta.
					// New versions don't do that for two reasons:
					// 1) Save space.
					// 2) The message can be translated into the current language of the blog, not stuck
					// in the language of the blog when the comment was made.
					$message = esc_html( $row['message'] );
				} elseif ( ! empty( $row['event'] ) ) {
					// If possible, use a current translation.
					switch ( $row['event'] ) {
						case 'recheck-spam':
							$message = esc_html( __( 'Akismet re-checked and caught this comment as spam.', 'akismet' ) );
							break;
						case 'check-spam':
							$message = esc_html( __( 'Akismet caught this comment as spam.', 'akismet' ) );
							break;
						case 'recheck-ham':
							$message = esc_html( __( 'Akismet re-checked and cleared this comment.', 'akismet' ) );
							break;
						case 'check-ham':
							$message = esc_html( __( 'Akismet cleared this comment.', 'akismet' ) );
							break;
						case 'check-ham-pending':
							$message = esc_html( __( 'Akismet provisionally cleared this comment.', 'akismet' ) );
							break;
						case 'wp-blacklisted':
						case 'wp-disallowed':
							$message = sprintf(
							/* translators: The placeholder is a WordPress PHP function name. */
								esc_html( __( 'Comment was caught by %s.', 'akismet' ) ),
								function_exists( 'wp_check_comment_disallowed_list' ) ? '<code>wp_check_comment_disallowed_list</code>' : '<code>wp_blacklist_check</code>'
							);
							break;
						case 'report-spam':
							if ( isset( $row['user'] ) ) {
								/* translators: The placeholder is a username. */
								$message = esc_html( sprintf( __( '%s reported this comment as spam.', 'akismet' ), $row['user'] ) );
							} elseif ( ! $message ) {
								$message = esc_html( __( 'This comment was reported as spam.', 'akismet' ) );
							}
							break;
						case 'report-ham':
							if ( isset( $row['user'] ) ) {
								/* translators: The placeholder is a username. */
								$message = esc_html( sprintf( __( '%s reported this comment as not spam.', 'akismet' ), $row['user'] ) );
							} elseif ( ! $message ) {
								$message = esc_html( __( 'This comment was reported as not spam.', 'akismet' ) );
							}
							break;
						case 'cron-retry-spam':
							$message = esc_html( __( 'Akismet caught this comment as spam during an automatic retry.', 'akismet' ) );
							break;
						case 'cron-retry-ham':
							$message = esc_html( __( 'Akismet cleared this comment during an automatic retry.', 'akismet' ) );
							break;
						case 'check-error':
							if ( isset( $row['meta'], $row['meta']['response'] ) ) {
								/* translators: The placeholder is an error response returned by the API server. */
								$message = sprintf( esc_html( __( 'Akismet was unable to check this comment (response: %s) but will automatically retry later.', 'akismet' ) ), '<code>' . esc_html( $row['meta']['response'] ) . '</code>' );
							} else {
								$message = esc_html( __( 'Akismet was unable to check this comment but will automatically retry later.', 'akismet' ) );
							}
							break;
						case 'recheck-error':
							if ( isset( $row['meta'], $row['meta']['response'] ) ) {
								/* translators: The placeholder is an error response returned by the API server. */
								$message = sprintf( esc_html( __( 'Akismet was unable to recheck this comment (response: %s).', 'akismet' ) ), '<code>' . esc_html( $row['meta']['response'] ) . '</code>' );
							} else {
								$message = esc_html( __( 'Akismet was unable to recheck this comment.', 'akismet' ) );
							}
							break;
						case 'webhook-spam':
							$message = esc_html( __( 'Akismet caught this comment as spam and updated its status via webhook.', 'akismet' ) );
							break;
						case 'webhook-ham':
							$message = esc_html( __( 'Akismet cleared this comment and updated its status via webhook.', 'akismet' ) );
							break;
						case 'webhook-spam-noaction':
							$message = esc_html( __( 'Akismet determined this comment was spam during a recheck. It did not update the comment status because it had already been modified by another user or plugin.', 'akismet' ) );
							break;
						case 'webhook-ham-noaction':
							$message = esc_html( __( 'Akismet cleared this comment during a recheck. It did not update the comment status because it had already been modified by another user or plugin.', 'akismet' ) );
							break;
						case 'akismet-skipped':
							$message = esc_html( __( 'This comment was not sent to Akismet when it was submitted because it was caught by something else.', 'akismet' ) );
							break;
						case 'akismet-skipped-disallowed':
							$message = esc_html( __( 'This comment was not sent to Akismet when it was submitted because it was caught by the comment disallowed list.', 'akismet' ) );
							break;
						default:
							if ( preg_match( '/^status-changed/', $row['event'] ) ) {
								// Half of these used to be saved without the dash after 'status-changed'.
								// See https://plugins.trac.wordpress.org/changeset/1150658/akismet/trunk
								$new_status = preg_replace( '/^status-changed-?/', '', $row['event'] );
								/* translators: The placeholder is a short string (like 'spam' or 'approved') denoting the new comment status. */
								$message = sprintf( esc_html( __( 'Comment status was changed to %s', 'akismet' ) ), '<code>' . esc_html( $new_status ) . '</code>' );
							} elseif ( preg_match( '/^status-/', $row['event'] ) ) {
								$new_status = preg_replace( '/^status-/', '', $row['event'] );

								if ( isset( $row['user'] ) ) {
									/* translators: %1$s is a username; %2$s is a short string (like 'spam' or 'approved') denoting the new comment status. */
									$message = sprintf( esc_html( __( '%1$s changed the comment status to %2$s.', 'akismet' ) ), $row['user'], '<code>' . esc_html( $new_status ) . '</code>' );
								}
							}
							break;
					}
				}

				if ( ! empty( $message ) ) {
					echo '<p>';

					if ( isset( $row['time'] ) ) {
						$time = gmdate( 'D d M Y @ h:i:s a', (int) $row['time'] ) . ' GMT';

						/* translators: The placeholder is an amount of time, like "7 seconds" or "3 days" returned by the function human_time_diff(). */
						$time_html = '<span style="color: #999;" alt="' . esc_attr( $time ) . '" title="' . esc_attr( $time ) . '">' . sprintf( esc_html__( '%s ago', 'akismet' ), human_time_diff( $row['time'] ) ) . '</span>';

						printf(
						/* translators: %1$s is a human-readable time difference, like "3 hours ago", and %2$s is an already-translated phrase describing how a comment's status changed, like "This comment was reported as spam." */
							esc_html( __( '%1$s - %2$s', 'akismet' ) ),
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$time_html,
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$message
						); // esc_html() is done above so that we can use HTML in $message.
					} else {
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $message; // esc_html() is done above so that we can use HTML in $message.
					}

					echo '</p>';
				}
			}
		} else {
			echo '<p>';
			echo esc_html( __( 'No comment history.', 'akismet' ) );
			echo '</p>';
		}
	}

	public static function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( plugin_dir_url( __FILE__ ) . '/akismet.php' ) ) {
			$links[] = '<a href="' . esc_url( self::get_page_url() ) . '">' . esc_html__( 'Settings', 'akismet' ) . '</a>';
		}

		return $links;
	}

	// Total spam in queue
	// get_option( 'akismet_spam_count' ) is the total caught ever
	public static function get_spam_count( $type = false ) {
		global $wpdb;

		if ( ! $type ) { // total
			$count = wp_cache_get( 'akismet_spam_count', 'widget' );
			if ( false === $count ) {
				$count = wp_count_comments();
				$count = $count->spam;
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
		if ( function_exists( 'gethostbynamel' ) ) {

			$ips = gethostbynamel( 'rest.akismet.com' );
			if ( $ips && is_array( $ips ) && count( $ips ) ) {
				$api_key = Akismet::get_api_key();

				foreach ( $ips as $ip ) {
					$response = Akismet::verify_key( $api_key, $ip );
					// even if the key is invalid, at least we know we have connectivity
					if ( $response == 'valid' || $response == 'invalid' ) {
						$servers[ $ip ] = 'connected';
					} else {
						$servers[ $ip ] = $response ? $response : 'unable to connect';
					}
				}
			}
		}

		return $servers;
	}

	// Simpler connectivity check
	public static function check_server_connectivity( $cache_timeout = 86400 ) {

		$debug                        = array();
		$debug['PHP_VERSION']         = PHP_VERSION;
		$debug['WORDPRESS_VERSION']   = $GLOBALS['wp_version'];
		$debug['AKISMET_VERSION']     = AKISMET_VERSION;
		$debug['AKISMET__PLUGIN_DIR'] = AKISMET__PLUGIN_DIR;
		$debug['SITE_URL']            = site_url();
		$debug['HOME_URL']            = home_url();

		$servers = get_option( 'akismet_available_servers' );
		if ( ( time() - get_option( 'akismet_connectivity_time' ) < $cache_timeout ) && $servers !== false ) {
			$servers = self::check_server_ip_connectivity();
			update_option( 'akismet_available_servers', $servers );
			update_option( 'akismet_connectivity_time', time() );
		}

		if ( wp_http_supports( array( 'ssl' ) ) ) {
			$response = wp_remote_get( 'https://rest.akismet.com/1.1/test' );
		} else {
			$response = wp_remote_get( 'http://rest.akismet.com/1.1/test' );
		}

		$debug['gethostbynamel']  = function_exists( 'gethostbynamel' ) ? 'exists' : 'not here';
		$debug['Servers']         = $servers;
		$debug['Test Connection'] = $response;

		Akismet::log( $debug );

		if ( $response && 'connected' == wp_remote_retrieve_body( $response ) ) {
			return true;
		}

		return false;
	}

	// Check the server connectivity and store the available servers in an option.
	public static function get_server_connectivity( $cache_timeout = 86400 ) {
		return self::check_server_connectivity( $cache_timeout );
	}

	/**
	 * Find out whether any comments in the Pending queue have not yet been checked by Akismet.
	 *
	 * @return bool
	 */
	public static function are_any_comments_waiting_to_be_checked() {
		return ! ! get_comments(
			array(
				// Exclude comments that are not pending. This would happen if someone manually approved or spammed a comment
				// that was waiting to be checked. The akismet_error meta entry will eventually be removed by the cron recheck job.
				'status'   => 'hold',

				// This is the commentmeta that is saved when a comment couldn't be checked.
				'meta_key' => 'akismet_error',

				// We only need to know whether at least one comment is waiting for a check.
				'number'   => 1,
			)
		);
	}

	public static function get_page_url( $page = 'config' ) {

		$args = array( 'page' => 'akismet-key-config' );

		if ( $page == 'stats' ) {
			$args = array(
				'page' => 'akismet-key-config',
				'view' => 'stats',
			);
		} elseif ( $page == 'delete_key' ) {
			$args = array(
				'page'     => 'akismet-key-config',
				'view'     => 'start',
				'action'   => 'delete-key',
				'_wpnonce' => wp_create_nonce( self::NONCE ),
			);
		} elseif ( $page === 'init' ) {
			$args = array(
				'page' => 'akismet-key-config',
				'view' => 'start',
			);
		}

		return add_query_arg( $args, menu_page_url( 'akismet-key-config', false ) );
	}

	public static function get_akismet_user( $api_key ) {
		$akismet_user = false;

		$request_args = array(
			'key'  => $api_key,
			'blog' => get_option( 'home' ),
		);

		$request_args = apply_filters( 'akismet_request_args', $request_args, 'get-subscription' );

		$subscription_verification = Akismet::http_post( Akismet::build_query( $request_args ), 'get-subscription' );

		if ( ! empty( $subscription_verification[1] ) ) {
			if ( 'invalid' !== $subscription_verification[1] ) {
				$akismet_user = json_decode( $subscription_verification[1] );
			}
		}

		return $akismet_user;
	}

	public static function get_stats( $api_key ) {
		$stat_totals = array();

		foreach ( array( '6-months', 'all' ) as $interval ) {
			$request_args = array(
				'blog' => get_option( 'home' ),
				'key'  => $api_key,
				'from' => $interval,
			);

			$request_args = apply_filters( 'akismet_request_args', $request_args, 'get-stats' );

			$response = Akismet::http_post( Akismet::build_query( $request_args ), 'get-stats' );

			if ( ! empty( $response[1] ) ) {
				$data = json_decode( $response[1] );
				/*
				 * The json decoded response should be an object. If it's not an object, something's wrong, and the data
				 * shouldn't be added to the stats_totals array.
				 */
				if ( is_object( $data ) ) {
					$stat_totals[ $interval ] = $data;
				}
			}
		}

		return $stat_totals;
	}

	public static function verify_wpcom_key( $api_key, $user_id, $extra = array() ) {
		$request_args = array_merge(
			array(
				'user_id'          => $user_id,
				'api_key'          => $api_key,
				'get_account_type' => 'true',
			),
			$extra
		);

		$request_args = apply_filters( 'akismet_request_args', $request_args, 'verify-wpcom-key' );

		$akismet_account = Akismet::http_post( Akismet::build_query( $request_args ), 'verify-wpcom-key' );

		if ( ! empty( $akismet_account[1] ) ) {
			$akismet_account = json_decode( $akismet_account[1] );
		}

		Akismet::log( compact( 'akismet_account' ) );

		return $akismet_account;
	}

	public static function connect_jetpack_user() {

		if ( $jetpack_user = self::get_jetpack_user() ) {
			if ( isset( $jetpack_user['user_id'] ) && isset( $jetpack_user['api_key'] ) ) {
				$akismet_user = self::verify_wpcom_key( $jetpack_user['api_key'], $jetpack_user['user_id'], array( 'action' => 'connect_jetpack_user' ) );

				if ( is_object( $akismet_user ) ) {
					self::save_key( $akismet_user->api_key );
					return in_array( $akismet_user->status, array( 'active', 'active-dunning', 'no-sub' ) );
				}
			}
		}

		return false;
	}

	public static function display_alert() {
		Akismet::view(
			'notice',
			array(
				'type' => 'alert',
				'code' => (int) get_option( 'akismet_alert_code' ),
				'msg'  => get_option( 'akismet_alert_msg' ),
			)
		);
	}

	public static function get_usage_limit_alert_data() {
		return array(
			'type'                => 'usage-limit',
			'code'                => (int) get_option( 'akismet_alert_code' ),
			'msg'                 => get_option( 'akismet_alert_msg' ),
			'api_calls'           => get_option( 'akismet_alert_api_calls' ),
			'usage_limit'         => get_option( 'akismet_alert_usage_limit' ),
			'upgrade_plan'        => get_option( 'akismet_alert_upgrade_plan' ),
			'upgrade_url'         => get_option( 'akismet_alert_upgrade_url' ),
			'upgrade_type'        => get_option( 'akismet_alert_upgrade_type' ),
			'upgrade_via_support' => get_option( 'akismet_alert_upgrade_via_support' ) === 'true',
		);
	}

	public static function display_usage_limit_alert() {
		Akismet::view( 'notice', self::get_usage_limit_alert_data() );
	}

	public static function display_spam_check_warning() {
		Akismet::fix_scheduled_recheck();

		if ( wp_next_scheduled( 'akismet_schedule_cron_recheck' ) > time() && self::are_any_comments_waiting_to_be_checked() ) {
			/*
			 * The 'akismet_display_cron_disabled_notice' filter can be used to control whether the WP-Cron disabled notice is displayed.
			 */
			if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON && apply_filters( 'akismet_display_cron_disabled_notice', true ) ) {
				Akismet::view( 'notice', array( 'type' => 'spam-check-cron-disabled' ) );
			} else {
				/* translators: The Akismet configuration page URL. */
				$link_text = apply_filters( 'akismet_spam_check_warning_link_text', sprintf( __( 'Please check your <a href="%s">Akismet configuration</a> and contact your web host if problems persist.', 'akismet' ), esc_url( self::get_page_url() ) ) );
				Akismet::view(
					'notice',
					array(
						'type'      => 'spam-check',
						'link_text' => $link_text,
					)
				);
			}
		}
	}

	public static function display_api_key_warning() {
		Akismet::view( 'notice', array( 'type' => 'plugin' ) );
	}

	public static function display_page() {
		if ( ! Akismet::get_api_key() || ( isset( $_GET['view'] ) && $_GET['view'] == 'start' ) ) {
			self::display_start_page();
		} elseif ( isset( $_GET['view'] ) && $_GET['view'] == 'stats' ) {
			self::display_stats_page();
		} else {
			self::display_configuration_page();
		}
	}

	public static function display_start_page() {
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'delete-key' ) {
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], self::NONCE ) ) {
					delete_option( 'wordpress_api_key' );
				}
			}
		}

		$api_key               = Akismet::get_api_key();
		$existing_key_is_valid = ! (
			self::get_notice_by_key( 'status' ) === self::NOTICE_EXISTING_KEY_INVALID
		);

		if ( $api_key && $existing_key_is_valid ) {
			self::display_configuration_page();
			return;
		}

		// the user can choose to auto connect their API key by clicking a button on the akismet done page
		// if jetpack, get verified api key by using connected wpcom user id
		// if no jetpack, get verified api key by using an akismet token

		$akismet_user = false;

		if ( isset( $_GET['token'] ) && preg_match( '/^(\d+)-[0-9a-f]{20}$/', $_GET['token'] ) ) {
			$akismet_user = self::verify_wpcom_key( '', '', array( 'token' => $_GET['token'] ) );
		}

		if ( false === $akismet_user ) {
			$jetpack_user = self::get_jetpack_user();

			if ( is_array( $jetpack_user ) ) {
				$akismet_user = self::verify_wpcom_key( $jetpack_user['api_key'], $jetpack_user['user_id'] );
			}
		}

		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'save-key' ) {
				if ( is_object( $akismet_user ) ) {
					self::save_key( $akismet_user->api_key );
					self::display_configuration_page();
					return;
				}
			}
		}

		Akismet::view( 'start', compact( 'akismet_user' ) );

		/*
		// To see all variants when testing.
		$akismet_user->status = 'no-sub';
		Akismet::view( 'start', compact( 'akismet_user' ) );
		$akismet_user->status = 'cancelled';
		Akismet::view( 'start', compact( 'akismet_user' ) );
		$akismet_user->status = 'suspended';
		Akismet::view( 'start', compact( 'akismet_user' ) );
		$akismet_user->status = 'other';
		Akismet::view( 'start', compact( 'akismet_user' ) );
		$akismet_user = false;
		*/
	}

	public static function display_stats_page() {
		Akismet::view( 'stats' );
	}

	public static function display_configuration_page() {
		$api_key      = Akismet::get_api_key();
		$akismet_user = self::get_akismet_user( $api_key );

		if ( ! $akismet_user ) {
			// This could happen if the user's key became invalid after it was previously valid and successfully set up.
			self::$notices['status'] = self::NOTICE_EXISTING_KEY_INVALID;
			self::display_start_page();
			return;
		}

		$stat_totals = self::get_stats( $api_key );

		// If unset, create the new strictness option using the old discard option to determine its default.
		// If the old option wasn't set, default to discarding the blatant spam.
		if ( get_option( 'akismet_strictness' ) === false ) {
			add_option( 'akismet_strictness', ( get_option( 'akismet_discard_month' ) === 'false' ? '0' : '1' ) );
		}

		// Sync the local "Total spam blocked" count with the authoritative count from the server.
		if ( isset( $stat_totals['all'], $stat_totals['all']->spam ) ) {
			update_option( 'akismet_spam_count', $stat_totals['all']->spam );
		}

		$notices = array();

		if ( empty( self::$notices ) ) {
			if ( ! empty( $stat_totals['all'] ) && isset( $stat_totals['all']->time_saved ) && $akismet_user->status == 'active' && $akismet_user->account_type == 'free-api-key' ) {

				$time_saved = false;

				if ( $stat_totals['all']->time_saved > 1800 ) {
					$total_in_minutes = round( $stat_totals['all']->time_saved / 60 );
					$total_in_hours   = round( $total_in_minutes / 60 );
					$total_in_days    = round( $total_in_hours / 8 );
					$cleaning_up      = __( 'Cleaning up spam takes time.', 'akismet' );

					if ( $total_in_days > 1 ) {
						/* translators: %s: Number of days. */
						$time_saved = $cleaning_up . ' ' . sprintf( _n( 'Akismet has saved you %s day!', 'Akismet has saved you %s days!', $total_in_days, 'akismet' ), number_format_i18n( $total_in_days ) );
					} elseif ( $total_in_hours > 1 ) {
						/* translators: %s: Number of hours. */
						$time_saved = $cleaning_up . ' ' . sprintf( _n( 'Akismet has saved you %d hour!', 'Akismet has saved you %d hours!', $total_in_hours, 'akismet' ), $total_in_hours );
					} elseif ( $total_in_minutes >= 30 ) {
						/* translators: %s: Number of minutes. */
						$time_saved = $cleaning_up . ' ' . sprintf( _n( 'Akismet has saved you %d minute!', 'Akismet has saved you %d minutes!', $total_in_minutes, 'akismet' ), $total_in_minutes );
					}
				}

				$notices[] = array(
					'type'       => 'active-notice',
					'time_saved' => $time_saved,
				);
			}
		}

		if ( ! Akismet::predefined_api_key() && ! isset( self::$notices['status'] ) && in_array( $akismet_user->status, array( 'cancelled', 'suspended', 'missing', 'no-sub' ) ) ) {
			$notices[] = array( 'type' => $akismet_user->status );
		}

		$alert_code = get_option( 'akismet_alert_code' );
		if ( isset( Akismet::$limit_notices[ $alert_code ] ) ) {
			$notices[] = self::get_usage_limit_alert_data();
		} elseif ( $alert_code > 0 ) {
			$notices[] = array(
				'type' => 'alert',
				'code' => (int) get_option( 'akismet_alert_code' ),
				'msg'  => get_option( 'akismet_alert_msg' ),
			);
		}

		/*
		 *  To see all variants when testing.
		 *
		 *  You may also want to comment out the akismet_view_arguments filter in Akismet::view()
		 *  to ensure that you can see all of the notices (e.g. suspended, active-notice).
		*/
		// $notices[] = array( 'type' => 'active-notice', 'time_saved' => 'Cleaning up spam takes time. Akismet has saved you 1 minute!' );
		// $notices[] = array( 'type' => 'plugin' );
		// $notices[] = array( 'type' => 'notice', 'notice_header' => 'This is the notice header.', 'notice_text' => 'This is the notice text.' );
		// $notices[] = array( 'type' => 'missing-functions' );
		// $notices[] = array( 'type' => 'servers-be-down' );
		// $notices[] = array( 'type' => 'active-dunning' );
		// $notices[] = array( 'type' => 'cancelled' );
		// $notices[] = array( 'type' => 'suspended' );
		// $notices[] = array( 'type' => 'missing' );
		// $notices[] = array( 'type' => 'no-sub' );
		// $notices[] = array( 'type' => 'new-key-valid' );
		// $notices[] = array( 'type' => 'new-key-invalid' );
		// $notices[] = array( 'type' => 'existing-key-invalid' );
		// $notices[] = array( 'type' => 'new-key-failed' );
		// $notices[] = array( 'type' => 'usage-limit', 'api_calls' => '15000', 'usage_limit' => '10000', 'upgrade_plan' => 'Enterprise', 'upgrade_url' => 'https://akismet.com/account/', 'code' => 10502 );
		// $notices[] = array( 'type' => 'spam-check', 'link_text' => 'Link text.' );
		// $notices[] = array( 'type' => 'spam-check-cron-disabled' );
		// $notices[] = array( 'type' => 'alert', 'code' => 123 );
		// $notices[] = array( 'type' => 'alert', 'code' => Akismet::ALERT_CODE_COMMERCIAL );

		Akismet::log( compact( 'stat_totals', 'akismet_user' ) );
		Akismet::view( 'config', compact( 'api_key', 'akismet_user', 'stat_totals', 'notices' ) );
	}

	public static function display_notice() {
		global $hook_suffix;

		if ( in_array( $hook_suffix, array( 'jetpack_page_akismet-key-config', 'settings_page_akismet-key-config' ) ) ) {
			// This page manages the notices and puts them inline where they make sense.
			return;
		}

		// To see notice variants while testing.
		// Akismet::view( 'notice', array( 'type' => 'spam-check-cron-disabled' ) );
		// Akismet::view( 'notice', array( 'type' => 'spam-check' ) );
		// Akismet::view( 'notice', array( 'type' => 'alert', 'code' => 123, 'msg' => 'Message' ) );

		if ( in_array( $hook_suffix, array( 'edit-comments.php' ) ) && (int) get_option( 'akismet_alert_code' ) > 0 ) {
			Akismet::verify_key( Akismet::get_api_key() ); // verify that the key is still in alert state

			$alert_code = get_option( 'akismet_alert_code' );
			if ( isset( Akismet::$limit_notices[ $alert_code ] ) ) {
				self::display_usage_limit_alert();
			} elseif ( $alert_code > 0 ) {
				self::display_alert();
			}
		} elseif ( in_array( $hook_suffix, self::$activation_banner_pages, true ) && ! Akismet::get_api_key() ) {
			// Show the "Set Up Akismet" banner on the comments and plugin pages if no API key has been set.
			self::display_api_key_warning();
		} elseif ( $hook_suffix == 'edit-comments.php' && wp_next_scheduled( 'akismet_schedule_cron_recheck' ) ) {
			self::display_spam_check_warning();
		}

		if ( isset( $_GET['akismet_recheck_complete'] ) ) {
			$recheck_count = (int) $_GET['recheck_count'];
			$spam_count    = (int) $_GET['spam_count'];

			if ( $recheck_count === 0 ) {
				$message = __( 'There were no comments to check. Akismet will only check comments awaiting moderation.', 'akismet' );
			} else {
				/* translators: %s: Number of comments. */
				$message  = sprintf( _n( 'Akismet checked %s comment.', 'Akismet checked %s comments.', $recheck_count, 'akismet' ), number_format( $recheck_count ) );
				$message .= ' ';

				if ( $spam_count === 0 ) {
					$message .= __( 'No comments were caught as spam.', 'akismet' );
				} else {
					/* translators: %s: Number of comments. */
					$message .= sprintf( _n( '%s comment was caught as spam.', '%s comments were caught as spam.', $spam_count, 'akismet' ), number_format( $spam_count ) );
				}
			}

			echo '<div class="notice notice-success"><p>' . esc_html( $message ) . '</p></div>';
		} elseif ( isset( $_GET['akismet_recheck_error'] ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( __( 'Akismet could not recheck your comments for spam.', 'akismet' ) ) . '</p></div>';
		}
	}

	public static function display_status() {
		if ( ! self::get_server_connectivity() ) {
			Akismet::view( 'notice', array( 'type' => 'servers-be-down' ) );
		} elseif ( ! empty( self::$notices ) ) {
			foreach ( self::$notices as $index => $type ) {
				if ( is_object( $type ) ) {
					$notice_header = $notice_text = '';

					if ( property_exists( $type, 'notice_header' ) ) {
						$notice_header = wp_kses( $type->notice_header, self::$allowed );
					}

					if ( property_exists( $type, 'notice_text' ) ) {
						$notice_text = wp_kses( $type->notice_text, self::$allowed );
					}

					if ( property_exists( $type, 'status' ) ) {
						$type = wp_kses( $type->status, self::$allowed );
						Akismet::view( 'notice', compact( 'type', 'notice_header', 'notice_text' ) );

						unset( self::$notices[ $index ] );
					}
				} else {
					Akismet::view( 'notice', compact( 'type' ) );

					unset( self::$notices[ $index ] );
				}
			}
		}
	}

	/**
	 * Gets a specific notice by key.
	 *
	 * @param $key
	 * @return mixed
	 */
	private static function get_notice_by_key( $key ) {
		return self::$notices[ $key ] ?? null;
	}

	/**
	 * Gets a Jetpack user.
	 *
	 * @return array|false
	 */
	private static function get_jetpack_user() {
		if ( ! class_exists( 'Jetpack' ) ) {
			return false;
		}

		if ( defined( 'JETPACK__VERSION' ) && version_compare( JETPACK__VERSION, '7.7', '<' ) ) {
			// For version of Jetpack prior to 7.7.
			Jetpack::load_xml_rpc_client();
		}

		$xml = new Jetpack_IXR_ClientMulticall( array( 'user_id' => get_current_user_id() ) );

		$xml->addCall( 'wpcom.getUserID' );
		$xml->addCall( 'akismet.getAPIKey' );
		$xml->query();

		Akismet::log( compact( 'xml' ) );

		if ( ! $xml->isError() ) {
			$responses = $xml->getResponse();
			if ( ( is_countable( $responses ) ? count( $responses ) : 0 ) > 1 ) {
				// Due to a quirk in how Jetpack does multi-calls, the response order
				// can't be trusted to match the call order. It's a good thing our
				// return values can be mostly differentiated from each other.
				$first_response_value  = array_shift( $responses[0] );
				$second_response_value = array_shift( $responses[1] );

				// If WPCOM ever reaches 100 billion users, this will fail. :-)
				if ( preg_match( '/^[a-f0-9]{12}$/i', $first_response_value ) ) {
					$api_key = $first_response_value;
					$user_id = (int) $second_response_value;
				} else {
					$api_key = $second_response_value;
					$user_id = (int) $first_response_value;
				}

				return compact( 'api_key', 'user_id' );
			}
		}
		return false;
	}

	/**
	 * Some commentmeta isn't useful in an export file. Suppress it (when supported).
	 *
	 * @param bool   $exclude
	 * @param string $key The meta key
	 * @param object $meta The meta object
	 * @return bool Whether to exclude this meta entry from the export.
	 */
	public static function exclude_commentmeta_from_export( $exclude, $key, $meta ) {
		if (
			in_array(
				$key,
				array(
					'akismet_as_submitted',
					'akismet_delay_moderation_email',
					'akismet_delayed_moderation_email',
					'akismet_rechecking',
					'akismet_schedule_approval_fallback',
					'akismet_schedule_email_fallback',
					'akismet_skipped_microtime',
				)
			)
		) {
			return true;
		}

		return $exclude;
	}

	/**
	 * When Akismet is active, remove the "Activate Akismet" step from the plugin description.
	 */
	public static function modify_plugin_description( $all_plugins ) {
		if ( isset( $all_plugins['akismet/akismet.php'] ) ) {
			if ( Akismet::get_api_key() ) {
				$all_plugins['akismet/akismet.php']['Description'] = __( 'Used by millions, Akismet is quite possibly the best way in the world to <strong>protect your blog from spam</strong>. Your site is fully configured and being protected, even while you sleep.', 'akismet' );
			} else {
				$all_plugins['akismet/akismet.php']['Description'] = __( 'Used by millions, Akismet is quite possibly the best way in the world to <strong>protect your blog from spam</strong>. It keeps your site protected even while you sleep. To get started, just go to <a href="admin.php?page=akismet-key-config">your Akismet Settings page</a> to set up your API key.', 'akismet' );
			}
		}

		return $all_plugins;
	}

	private static function set_form_privacy_notice_option( $state ) {
		if ( in_array( $state, array( 'display', 'hide' ) ) ) {
			update_option( 'akismet_comment_form_privacy_notice', $state );
		}
	}

	public static function register_personal_data_eraser( $erasers ) {
		$erasers['akismet'] = array(
			'eraser_friendly_name' => __( 'Akismet', 'akismet' ),
			'callback'             => array( 'Akismet_Admin', 'erase_personal_data' ),
		);

		return $erasers;
	}

	/**
	 * When a user requests that their personal data be removed, Akismet has a duty to discard
	 * any personal data we store outside of the comment itself. Right now, that is limited
	 * to the copy of the comment we store in the akismet_as_submitted commentmeta.
	 *
	 * FWIW, this information would be automatically deleted after 15 days.
	 *
	 * @param $email_address string The email address of the user who has requested erasure.
	 * @param $page int This function can (and will) be called multiple times to prevent timeouts,
	 *                  so this argument is used for pagination.
	 * @return array
	 * @see https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-eraser-to-your-plugin/
	 */
	public static function erase_personal_data( $email_address, $page = 1 ) {
		$items_removed = false;

		$number = 50;
		$page   = (int) $page;

		$comments = get_comments(
			array(
				'author_email' => $email_address,
				'number'       => $number,
				'paged'        => $page,
				'order_by'     => 'comment_ID',
				'order'        => 'ASC',
			)
		);

		foreach ( (array) $comments as $comment ) {
			$comment_as_submitted = get_comment_meta( $comment->comment_ID, 'akismet_as_submitted', true );

			if ( $comment_as_submitted ) {
				delete_comment_meta( $comment->comment_ID, 'akismet_as_submitted' );
				$items_removed = true;
			}
		}

		// Tell core if we have more comments to work on still
		$done = ( is_countable( $comments ) ? count( $comments ) : 0 ) < $number;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false, // always false in this example
			'messages'       => array(), // no messages in this example
			'done'           => $done,
		);
	}

	/**
	 * Return an array of HTML elements that are allowed in a notice.
	 *
	 * @return array
	 */
	public static function get_notice_kses_allowed_elements() {
		return self::$allowed;
	}

	/**
	 * Return a version to append to the URL of an asset file (e.g. CSS and images).
	 *
	 * @param string $relative_path Relative path to asset file
	 * @return string
	 */
	public static function get_asset_file_version( $relative_path ) {

		$full_path = AKISMET__PLUGIN_DIR . $relative_path;

		// If the AKISMET_VERSION contains a lower-case letter, it's a development version (e.g. 5.3.1a2).
		// Use the file modified time in development.
		if ( preg_match( '/[a-z]/', AKISMET_VERSION ) && file_exists( $full_path ) ) {
			return filemtime( $full_path );
		}

		// Otherwise, use the AKISMET_VERSION.
		return AKISMET_VERSION;
	}

	/**
	 * Return inline CSS for Akismet admin.
	 *
	 * @return string
	 */
	protected static function get_inline_css(): string {
		global $hook_suffix;

		// Hide excess compatible plugins when there are lots.
		$inline_css = '
			.akismet-compatible-plugins__card:nth-child(n+' . esc_attr( Akismet_Compatible_Plugins::DEFAULT_VISIBLE_PLUGIN_COUNT + 1 ) . ') {
				display: none;
			}

			.akismet-compatible-plugins__list.is-expanded .akismet-compatible-plugins__card:nth-child(n+' . esc_attr( Akismet_Compatible_Plugins::DEFAULT_VISIBLE_PLUGIN_COUNT + 1 ) . ') {
				display: flex;
			}
		';

		// Enqueue the Akismet activation banner background separately so we can
		// include the right path to the image. Shown on edit-comments.php and plugins.php.
		if ( in_array( $hook_suffix, self::$activation_banner_pages, true ) ) {
			$activation_banner_url = esc_url(
				plugin_dir_url( __FILE__ ) . '_inc/img/akismet-activation-banner-elements.png'
			);
			$inline_css           .= '.akismet-activate {' . PHP_EOL .
				'background-image: url(' . $activation_banner_url . ');' . PHP_EOL .
				'}';
		}

		return $inline_css;
	}
}
