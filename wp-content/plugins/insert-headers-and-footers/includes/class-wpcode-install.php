<?php
/**
 * Logic to run on plugin install.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Install.
 */
class WPCode_Install {

	/**
	 * WPCode_Install constructor.
	 */
	public function __construct() {
		register_activation_hook( WPCODE_FILE, array( $this, 'activate' ) );
		add_action( 'admin_init', array( $this, 'maybe_run_install' ) );
	}

	/**
	 * Activation hook.
	 *
	 * @return void
	 */
	public function activate() {
		// Add capabilities on activation as deleting the plugin removes them
		// but the option used in the `maybe_run_install` method below is not
		// removed so the capabilities are not added back.
		WPCode_Capabilities::add_capabilities();

		// Use an action to have a single activation hook plugin-wide.
		do_action( 'wpcode_plugin_activation' );
	}

	/**
	 * Install routine to run on plugin activation.
	 * Runs on admin_init so that we also handle updates.
	 * The ihaf_activated option was used by IHAF 1.6 and the key "lite" is for the activation date
	 * of that version of the plugin. In the WPCode plugin we use the "wpcode" key, so we have the update date
	 * and install the demo data.
	 *
	 * @return void
	 */
	public function maybe_run_install() {
		if ( ! is_admin() ) {
			return;
		}

		$activated = get_option( 'ihaf_activated', array() );

		if ( ! is_array( $activated ) ) {
			$activated = array();
		}

		if ( empty( $activated['wpcode'] ) ) {
			$activated['wpcode'] = time();

			update_option( 'ihaf_activated', $activated );

			// Add custom capabilities.
			WPCode_Capabilities::add_capabilities();

			// The option was empty so let's add the demo data.
			$this->add_demo_data();

			if ( ! empty( $activated['lite'] ) ) {
				// If IHAF 1.6 has been running on the site, redirect to upgrade screen.
				set_transient( 'wpcode_upgrade_redirect', true, 30 );
			}

			do_action( 'wpcode_install' );
		}

		// Maybe run manually just one time.
		$install = get_option( 'wpcode_install', false );

		if ( ! empty( $install ) ) {
			$this->activate();
			delete_option( 'wpcode_install' );
		}

		// Let's run an upgrade routine.
		if ( empty( $activated['version'] ) ) {
			$this->update_2_1_0();
		}

		if ( isset( $activated['version'] ) && version_compare( $activated['version'], WPCODE_VERSION, '=' ) ) {
			// If the version is identical just skip.
			return;
		}

		// Give other plugins a chance to run an upgrade routine.
		do_action( 'wpcode_before_version_update', $activated );

		$activated['version'] = WPCODE_VERSION;
		update_option( 'ihaf_activated', $activated );
	}

	/**
	 * Upgrade routine for 2.1.0.
	 * Empty the library cache to get the new snippets.
	 *
	 * @return void
	 */
	public function update_2_1_0() {
		if ( isset( wpcode()->library ) ) {
			wpcode()->library->delete_cache();
		}
	}

	/**
	 * Add some example snippets in a new installation.
	 *
	 * @return void
	 */
	public function add_demo_data() {
		$snippets = array(
			array(
				'title'         => __( 'Display a message after the 1st paragraph of posts', 'insert-headers-and-footers' ),
				'code'          => 'Thank you for reading this post, don\'t forget to subscribe!',
				'code_type'     => 'text',
				'auto_insert'   => 1,
				'location'      => 'after_paragraph',
				'insert_number' => 1,
				'tags'          => array(
					'sample',
					'message',
				),
			),
			array(
				'title'       => __( 'Completely Disable Comments', 'insert-headers-and-footers' ),
				'code'        => "add_action('admin_init', function () {\r\n    \/\/ Redirect any user trying to access comments page\r\n    global \$pagenow;\r\n    \r\n    if (\$pagenow === 'edit-comments.php') {\r\n        wp_safe_redirect(admin_url());\r\n        exit;\r\n    }\r\n\r\n    \/\/ Remove comments metabox from dashboard\r\n    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');\r\n\r\n    \/\/ Disable support for comments and trackbacks in post types\r\n    foreach (get_post_types() as \$post_type) {\r\n        if (post_type_supports(\$post_type, 'comments')) {\r\n            remove_post_type_support(\$post_type, 'comments');\r\n            remove_post_type_support(\$post_type, 'trackbacks');\r\n        }\r\n    }\r\n});\r\n\r\n\/\/ Close comments on the front-end\r\nadd_filter('comments_open', '__return_false', 20, 2);\r\nadd_filter('pings_open', '__return_false', 20, 2);\r\n\r\n\/\/ Hide existing comments\r\nadd_filter('comments_array', '__return_empty_array', 10, 2);\r\n\r\n\/\/ Remove comments page in menu\r\nadd_action('admin_menu', function () {\r\n    remove_menu_page('edit-comments.php');\r\n});\r\n\r\n\/\/ Remove comments links from admin bar\r\nadd_action('init', function () {\r\n    if (is_admin_bar_showing()) {\r\n        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);\r\n    }\r\n});",
				'code_type'   => 'php',
				'auto_insert' => 1,
				'location'    => 'everywhere',
				'tags'        => array(
					'sample',
					'disable',
					'comments',
				),
				'library_id'  => 12,
			),
		);

		// The activation hook runs after `init` so our plugin's custom
		// post type and custom taxonomies didn't have a chance to be registered.
		wpcode_register_post_type();
		wpcode_register_taxonomies();

		foreach ( $snippets as $snippet ) {
			$new_snippet = new WPCode_Snippet( $snippet );
			$new_snippet->save();
		}
	}
}

new WPCode_Install();
