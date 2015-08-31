<?php
/**
 * @package WPSEO\admin|google_search_console
 */

/**
 * Class WPSEO_GSC
 */
class WPSEO_GSC {

	/**
	 * The option where data will be stored
	 */
	const OPTION_WPSEO_GSC = 'wpseo-gsc';

	/**
	 * @var WPSEO_GSC_Service
	 */
	private $service;

	/**
	 * @var WPSEO_GSC_Category_Filters
	 */
	protected $category_filter;

	/**
	 * @var WPSEO_GSC_Issues
	 */
	protected $issue_fetch;

	/**
	 * @var string current platform
	 */
	private $platform;

	/**
	 * @var string current category
	 */
	private $category;

	/**
	 * Constructor for the page class. This will initialize all GSC related stuff
	 */
	public function __construct() {
		// Settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Setting the screen option.
		if ( filter_input( INPUT_GET, 'page' ) === 'wpseo_search_console' ) {

			if ( filter_input( INPUT_GET, 'tab' ) !== 'settings' && WPSEO_GSC_Settings::get_profile() === '' ) {
				wp_redirect( add_query_arg( 'tab', 'settings' ) );
				exit;
			}

			$this->set_hooks();
			$this->set_dependencies();
			$this->request_handler();
		}
	}

	/**
	 * Be sure the settings will be registered, so data can be stored
	 */
	public function register_settings() {
		register_setting( 'yoast_wpseo_gsc_options', self::OPTION_WPSEO_GSC );
	}

	/**
	 * Function that outputs the redirect page
	 */
	public function display() {
		require_once WPSEO_PATH . '/admin/google_search_console/views/gsc-display.php';
	}

	/**
	 * Display the table
	 */
	public function display_table() {
		// The list table.
		$list_table = new WPSEO_GSC_Table( $this->platform, $this->category, $this->issue_fetch->get_issues() );

		// Adding filter to display the category filters.
		add_filter( 'views_' . $list_table->get_screen_id(), array( $this->category_filter, 'as_array' ) );

		// Preparing and displaying the table.
		$list_table->prepare_items();
		$list_table->search_box( __( 'Search', 'wordpress-seo' ), 'wpseo-crawl-issues-search' );
		$list_table->display();
	}

	/**
	 * Load the admin redirects scripts
	 */
	public function page_scripts() {
		wp_enqueue_script( 'wp-seo-admin-gsc', plugin_dir_url( WPSEO_FILE ) . 'js/wp-seo-admin-gsc' . WPSEO_CSSJS_SUFFIX . '.js', array( 'jquery' ), WPSEO_VERSION );
		add_screen_option( 'per_page', array(
			'label'   => __( 'Crawl errors per page', 'wordpress-seo' ),
			'default' => 50,
			'option'  => 'errors_per_page',
		) );

		wp_enqueue_style( 'jquery-qtip.js', plugins_url( 'css/jquery.qtip' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
		wp_enqueue_style( 'metabox-tabs', plugins_url( 'css/metabox-tabs' . WPSEO_CSSJS_SUFFIX . '.css', WPSEO_FILE ), array(), WPSEO_VERSION );
		wp_enqueue_script( 'jquery-qtip', plugins_url( 'js/jquery.qtip.min.js', WPSEO_FILE ), array( 'jquery' ), WPSEO_VERSION, true );
	}

	/**
	 * Set the screen options
	 *
	 * @param string $status
	 * @param string $option
	 * @param string $value
	 *
	 * @return mixed
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( 'errors_per_page' == $option ) {
			return $value;
		}
	}

	/**
	 * Setting the hooks to be load on page request
	 */
	private function set_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'page_scripts' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 11, 3 );
	}

	/**
	 * Handles the POST and GET requests
	 */
	private function request_handler() {

		// List the table search post to a get.
		$this->list_table_search_post_to_get();

		// Catch the authorization code POST.
		$this->catch_authentication_post();

		// Is there a reset post than we will remove the posts and data.
		if ( filter_input( INPUT_GET, 'gsc_reset' ) ) {
			// Clear the google data.
			WPSEO_GSC_Settings::clear_data( $this->service );

			// Adding notification to the notification center.
			/* Translators: %1$s: expands to Google Search Console. */
			$this->add_notification( sprintf( __( 'The %1$s data has been removed. You will have to reauthenticate if you want to retrieve the data again.', 'wordpress-seo' ), 'Google Search Console' ), 'updated' );

			// Directly output the notifications.
			wp_redirect( remove_query_arg( 'gsc_reset' ) );
			exit;
		}

		// Reloads al the issues.
		if ( wp_verify_nonce( filter_input( INPUT_POST, 'reload-crawl-issues-nonce' ), 'reload-crawl-issues' ) && filter_input( INPUT_POST, 'reload-crawl-issues' ) ) {
			// Reloading all the issues.
			WPSEO_GSC_Settings::reload_issues();

			// Adding the notification.
			$this->add_notification( __( 'The issues have been successfully reloaded!', 'wordpress-seo' ), 'updated' );

			// Directly output the notifications.
			Yoast_Notification_Center::get()->display_notifications();
		}

		// Catch bulk action request.
		new WPSEO_GSC_Bulk_Action();
	}

	/**
	 * Catch the redirects search post and redirect it to a search get
	 */
	private function list_table_search_post_to_get() {
		if ( $search_string = filter_input( INPUT_POST, 's' ) ) {
			$url = add_query_arg( 's', $search_string );

			// Do the redirect.
			wp_redirect( $url );
			exit;
		}
	}

	/**
	 * Catch the authentication post
	 */
	private function catch_authentication_post() {
		$gsc_values = filter_input( INPUT_POST, 'gsc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		// Catch the authorization code POST.
		if ( ! empty( $gsc_values['authorization_code'] ) && wp_verify_nonce( $gsc_values['gsc_nonce'], 'wpseo-gsc_nonce' ) ) {
			if ( ! WPSEO_GSC_Settings::validate_authorization( trim( $gsc_values['authorization_code'] ), $this->service->get_client() ) ) {
				$this->add_notification( __( 'Incorrect Google Authorization Code.', 'wordpress-seo' ), 'error' );
			}

			// Redirect user to prevent a post resubmission which causes an oauth error.
			wp_redirect( admin_url( 'admin.php' ) . '?page=' . esc_attr( filter_input( INPUT_GET, 'page' ) ) . '&tab=settings' );
			exit;
		}
	}

	/**
	 * Adding notification to the yoast notification center
	 *
	 * @param string $message
	 * @param string $type
	 */
	private function add_notification( $message, $type ) {
		Yoast_Notification_Center::get()->add_notification(
			new Yoast_Notification( $message, array( 'type' => $type ) )
		);
	}

	/**
	 * Setting dependencies which will be used one this page
	 */
	private function set_dependencies() {
		// Setting the service object.
		$this->service         = new WPSEO_GSC_Service( WPSEO_GSC_Settings::get_profile() );

		// Setting the platform.
		$this->platform        = WPSEO_GSC_Mapper::get_current_platform( 'tab' );

		// Loading the issue counter.
		$issue_count           = new WPSEO_GSC_Count( $this->service );

		// Loading the category filters.
		$this->category_filter = new WPSEO_GSC_Category_Filters( $issue_count->get_platform_counts( $this->platform ) );

		// Setting the current category.
		$this->category        = $this->category_filter->get_category();

		// Listing the issues.
		$issue_count->list_issues( $this->platform, $this->category );

		// Fetching the issues.
		$this->issue_fetch = new WPSEO_GSC_Issues( $this->platform, $this->category, $issue_count->get_issues() );
	}

	/**
	 * Setting the tab help on top of the screen
	 */
	public function set_help() {
		$screen = get_current_screen();

		$screen->add_help_tab(
			array(
				'id'      => 'basic-help',
				'title'   => __( 'Issue categories', 'wordpress-seo' ),
				'content' => '<p><strong>' .__( 'Desktop', 'wordpress-seo' ) . '</strong><br />' . __( 'Errors that occurred when your site was crawled by Googlebot.', 'wordpress-seo' ) . '</p>'
							. '<p><strong>' .__( 'Smartphone', 'wordpress-seo' ) . '</strong><br />' . __( 'Errors that occurred only when your site was crawled by Googlebot-Mobile (errors didn\'t appear for desktop).', 'wordpress-seo' ) . '</p>'
							. '<p><strong>' .__( 'Feature phone', 'wordpress-seo' ) . '</strong><br />' . __( 'Errors that only occurred when your site was crawled by Googlebot for feature phones (errors didn\'t appear for desktop).', 'wordpress-seo' ) . '</p>',
			)
		);
	}

}
