<?php

require_once( ET_BUILDER_DIR . 'core.php' );

if ( wp_doing_ajax() && ! is_customize_preview() ) {
	define( 'WPE_HEARTBEAT_INTERVAL', et_builder_heartbeat_interval() );

	$_builder_load_actions = array(
		'et_pb_get_backbone_template',
		'et_pb_get_backbone_templates',
		'et_pb_process_computed_property',
		'et_fb_ajax_render_shortcode',
		'et_fb_ajax_save',
		'et_fb_ajax_drop_autosave',
		'et_fb_get_saved_layouts',
		'et_fb_save_layout',
		'et_fb_update_layout',
		'et_pb_execute_content_shortcodes',
		'et_pb_ab_builder_data',
		'et_pb_create_ab_tables',
		'et_pb_update_stats_table',
		'et_pb_ab_clear_cache',
		'et_pb_ab_clear_stats',
		'et_fb_prepare_shortcode',
		'et_fb_process_imported_content',
		'et_fb_get_saved_templates',
		'et_fb_retrieve_builder_data',
		'et_builder_email_add_account',     // email opt-in module
		'et_builder_email_remove_account',  // email opt-in module
		'et_builder_email_get_lists',       // email opt-in module
		'et_builder_save_settings',         // builder plugin dashboard (global builder settings)
		'save_epanel',                      // ePanel (global builder settings)
	);

	if ( class_exists( 'Easy_Digital_Downloads') ) {
		$_builder_load_actions[] = 'edd_load_gateway';
	}

	$builder_load_actions = apply_filters( 'et_builder_load_actions', array() );
	$builder_load_actions = array_merge( $builder_load_actions, $_builder_load_actions );

	$force_builder_load = isset( $_POST['et_load_builder_modules'] ) && '1' === $_POST['et_load_builder_modules'];

	if ( isset( $_REQUEST['action'] ) && 'heartbeat' == $_REQUEST['action'] ) {
		// if this is the heartbeat, and if its not packing our heartbeat data, then return
		if ( !isset( $_REQUEST['data'] ) || !isset( $_REQUEST['data']['et'] ) ) {
			return;
		}
	} else if ( ! $force_builder_load && ( ! isset( $_REQUEST['action'] ) || ! in_array( $_REQUEST['action'], $builder_load_actions ) ) ) {
		return;
	}

	if ( et_should_memory_limit_increase() ) {
		et_increase_memory_limit();
	}
}

function et_builder_load_global_functions_script() {
	wp_enqueue_script( 'et-builder-modules-global-functions-script', ET_BUILDER_URI . '/scripts/frontend-builder-global-functions.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'et_builder_load_global_functions_script', 7 );

function et_builder_load_modules_styles() {
	$current_page_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );
	$is_fb_enabled = function_exists( 'et_fb_enabled' ) ? et_fb_enabled() : false;

	wp_register_script( 'google-maps-api', esc_url( add_query_arg( array( 'key' => et_pb_get_google_api_key(), 'callback' => 'initMap' ), is_ssl() ? 'https://maps.googleapis.com/maps/api/js' : 'http://maps.googleapis.com/maps/api/js' ) ), array(), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'divi-fitvids', ET_BUILDER_URI . '/scripts/jquery.fitvids.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'waypoints', ET_BUILDER_URI . '/scripts/waypoints.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'magnific-popup', ET_BUILDER_URI . '/scripts/jquery.magnific-popup.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_register_script( 'hashchange', ET_BUILDER_URI . '/scripts/jquery.hashchange.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_register_script( 'salvattore', ET_BUILDER_URI . '/scripts/salvattore.min.js', array(), ET_BUILDER_VERSION, true );
	wp_register_script( 'easypiechart', ET_BUILDER_URI . '/scripts/jquery.easypiechart.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

	if ( et_is_builder_plugin_active() ) {
		wp_register_script( 'fittext', ET_BUILDER_URI . '/scripts/jquery.fittext.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	}

	// Load main styles CSS file only if the Builder plugin is active
	if ( et_is_builder_plugin_active() ) {
		wp_enqueue_style( 'et-builder-modules-style', ET_BUILDER_URI . '/styles/frontend-builder-plugin-style.css', array(), ET_BUILDER_VERSION );
	}

	// Load visible.min.js only if AB testing active on current page OR VB (because post settings is synced between VB and BB)
	if ( et_is_ab_testing_active() || $is_fb_enabled ) {
		wp_enqueue_script( 'et-jquery-visible-viewport', ET_BUILDER_URI . '/scripts/ext/jquery.visible.min.js', array( 'jquery', 'et-builder-modules-script' ), ET_BUILDER_VERSION, true );
	}

	wp_enqueue_style( 'magnific-popup', ET_BUILDER_URI . '/styles/magnific_popup.css', array(), ET_BUILDER_VERSION );

	wp_enqueue_script( 'et-jquery-touch-mobile', ET_BUILDER_URI . '/scripts/jquery.mobile.custom.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'et-builder-modules-script', ET_BUILDER_URI . '/scripts/frontend-builder-scripts.js', apply_filters( 'et_pb_frontend_builder_scripts_dependencies', array( 'jquery', 'et-jquery-touch-mobile' ) ), ET_BUILDER_VERSION, true );
	wp_localize_script( 'et-builder-modules-script', 'et_pb_custom', array(
		'ajaxurl'                => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
		'images_uri'             => get_template_directory_uri() . '/images',
		'builder_images_uri'     => ET_BUILDER_URI . '/images',
		'et_frontend_nonce'      => wp_create_nonce( 'et_frontend_nonce' ),
		'subscription_failed'    => esc_html__( 'Please, check the fields below to make sure you entered the correct information.', 'et_builder' ),
		'et_ab_log_nonce'        => wp_create_nonce( 'et_ab_testing_log_nonce' ),
		'fill_message'           => esc_html__( 'Please, fill in the following fields:', 'et_builder' ),
		'contact_error_message'  => esc_html__( 'Please, fix the following errors:', 'et_builder' ),
		'invalid'                => esc_html__( 'Invalid email', 'et_builder' ),
		'captcha'                => esc_html__( 'Captcha', 'et_builder' ),
		'prev'                   => esc_html__( 'Prev', 'et_builder' ),
		'previous'               => esc_html__( 'Previous', 'et_builder' ),
		'next'                   => esc_html__( 'Next', 'et_builder' ),
		'wrong_captcha'          => esc_html__( 'You entered the wrong number in captcha.', 'et_builder' ),
		'is_builder_plugin_used' => et_is_builder_plugin_active(),
		'ignore_waypoints'       => et_is_ignore_waypoints() ? 'yes' : 'no',
		'is_divi_theme_used'     => function_exists( 'et_divi_fonts_url' ),
		'widget_search_selector' => apply_filters( 'et_pb_widget_search_selector', '.widget_search' ),
		'is_ab_testing_active'   => et_is_ab_testing_active(),
		'page_id'                => $current_page_id,
		'unique_test_id'         => get_post_meta( $current_page_id, '_et_pb_ab_testing_id', true ),
		'ab_bounce_rate'         => '' !== get_post_meta( $current_page_id, '_et_pb_ab_bounce_rate_limit', true ) ? get_post_meta( $current_page_id, '_et_pb_ab_bounce_rate_limit', true ) : 5,
		'is_cache_plugin_active' => false === et_pb_detect_cache_plugins() ? 'no' : 'yes',
		'is_shortcode_tracking'  => get_post_meta( $current_page_id, '_et_pb_enable_shortcode_tracking', true ),
	) );

	/**
	 * Only load this during builder preview screen session
	 */
	if ( is_et_pb_preview() ) {
		// Set fixed protocol for preview URL to prevent cross origin issue
		$preview_scheme = is_ssl() ? 'https' : 'http';

		// Get home url, then parse it
		$preview_origin_component = parse_url( home_url( '', $preview_scheme ) );

		// Rebuild origin URL, strip sub-directory address if there's any (postMessage e.origin doesn't pass sub-directory address)
		$preview_origin = "";

		// Perform check, prevent unnecessary error
		if ( isset( $preview_origin_component['scheme'] ) && isset( $preview_origin_component['host'] ) ) {
			$preview_origin = "{$preview_origin_component['scheme']}://{$preview_origin_component['host']}";

			// Append port number if different port number is being used
			if ( isset( $preview_origin_component['port'] ) ) {
				$preview_origin = "{$preview_origin}:{$preview_origin_component['port']}";
			}
		}

		// Enqueue theme's style.css if it hasn't been enqueued (possibly being hardcoded by theme)
		if ( ! et_builder_has_theme_style_enqueued() && et_is_builder_plugin_active() ) {
			wp_enqueue_style( 'et-builder-theme-style-css', get_stylesheet_uri(), array() );
		}

		wp_enqueue_style( 'et-builder-preview-style', ET_BUILDER_URI . '/styles/preview.css', array(), ET_BUILDER_VERSION );
		wp_enqueue_script( 'et-builder-preview-script', ET_BUILDER_URI . '/scripts/frontend-builder-preview.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
		wp_localize_script( 'et-builder-preview-script', 'et_preview_params', array(
			'preview_origin' => esc_url( $preview_origin ),
			'alert_origin_not_matched' => sprintf(
				esc_html__( 'Unauthorized access. Preview cannot be accessed outside %1$s.', 'et_builder' ),
				esc_url( home_url( '', $preview_scheme ) )
			),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'et_builder_load_modules_styles', 11 );

/**
 * Determine whether current theme supports Waypoints or not
 * @return bool
 */
function et_is_ignore_waypoints() {
	// WPBakery Visual Composer plugin conflicts with waypoints
	if ( class_exists( 'Vc_Manager' ) ) {
		return true;
	}

	// always return false if not in divi plugin
	if ( ! et_is_builder_plugin_active() ) {
		return false;
	}

	$theme_data = wp_get_theme();

	if ( empty( $theme_data ) ) {
		return false;
	}

	// list of themes without Waypoints support
	$no_waypoints_themes = apply_filters( 'et_pb_no_waypoints_themes', array(
		'Avada'
	) );

	// return true if current theme doesn't support Waypoints
	if ( in_array( $theme_data->Name, $no_waypoints_themes, true ) ) {
		return true;
	}

	return false;
}

/**
 * Determine whether current page has enqueued theme's style.css or not
 * This is mainly used on preview screen to decide to enqueue theme's style nor not
 * @return bool
 */
function et_builder_has_theme_style_enqueued() {
	global $wp_styles;

	if ( ! empty( $wp_styles->queue  ) ) {
		$theme_style_uri = get_stylesheet_uri();

		foreach ( $wp_styles->queue as $handle) {
			if ( isset( $wp_styles->registered[$handle]->src ) && $theme_style_uri === $wp_styles->registered[$handle]->src ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Added specific body classes for builder related situation
 * This enables theme to adjust its case independently
 * @return array
 */
function et_builder_body_classes( $classes ) {
	if ( is_et_pb_preview() ) {
		$classes[] = 'et-pb-preview';
	}

	return $classes;
}
add_filter( 'body_class', 'et_builder_body_classes' );

if ( ! function_exists( 'et_builder_add_main_elements' ) ) :
function et_builder_add_main_elements() {
	require ET_BUILDER_DIR . 'main-structure-elements.php';
	require ET_BUILDER_DIR . 'main-modules.php';
	do_action( 'et_builder_ready' );
}
endif;

if ( ! function_exists( 'et_builder_load_framework' ) ) :
function et_builder_load_framework() {

	require ET_BUILDER_DIR . 'functions.php';
	require ET_BUILDER_DIR . 'compat/woocommerce.php';

	if ( is_admin() ) {
		global $pagenow, $et_current_memory_limit;

		if ( ! empty( $pagenow ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$et_current_memory_limit = intval( @ini_get( 'memory_limit' ) );
		}
	}

	// load builder files on front-end and on specific admin pages only.
	$action_hook = is_admin() ? 'wp_loaded' : 'wp';

	if ( et_builder_should_load_framework() ) {

		require ET_BUILDER_DIR . 'layouts.php';
		require ET_BUILDER_DIR . 'class-et-builder-element.php';
		require ET_BUILDER_DIR . 'class-et-builder-plugin-compat-base.php';
		require ET_BUILDER_DIR . 'class-et-builder-plugin-compat-loader.php';
		require ET_BUILDER_DIR . 'class-et-global-settings.php';
		require ET_BUILDER_DIR . 'ab-testing.php';
		require ET_BUILDER_DIR . 'class-et-builder-settings.php';

		$builder_settings_loaded = true;

		do_action( 'et_builder_framework_loaded' );

		add_action( $action_hook, 'et_builder_init_global_settings', 9 );
		add_action( $action_hook, 'et_builder_add_main_elements' );
	} else if ( is_admin() ) {
		require ET_BUILDER_DIR . 'class-et-builder-settings.php';
		$builder_settings_loaded = true;
	}

	if ( isset( $builder_settings_loaded ) ) {
		et_builder_settings_init();
	}

	add_action( $action_hook, 'et_builder_load_frontend_builder' );
}
endif;

function et_builder_load_frontend_builder() {
	// set the $et_current_memory_limit if FB is loading
	global $et_current_memory_limit;
	$et_current_memory_limit = intval( @ini_get( 'memory_limit' ) );

	// try to increase the memory limit to 128mb silently if it less than 128
	if ( ! empty( $et_current_memory_limit ) && intval( $et_current_memory_limit ) < 128 ) {
		if ( true !== strpos( ini_get( 'disable_functions' ), 'ini_set' ) ) {
			@ini_set( 'memory_limit', '128M' );
		}
	}

	require_once ET_BUILDER_DIR . 'frontend-builder/init.php';
}

if ( ! function_exists( 'et_pb_get_google_api_key' ) ) :
function et_pb_get_google_api_key() {
	$google_api_option = get_option( 'et_google_api_settings' );
	$google_api_key = isset( $google_api_option['api_key'] ) ? $google_api_option['api_key'] : '';

	return $google_api_key;
}
endif;

if ( ! function_exists( 'et_pb_enqueue_google_maps_script' ) ) :
function et_pb_enqueue_google_maps_script() {
	$google_api_option = get_option( 'et_google_api_settings' );
	$google_maps_script_enqueue = !$google_api_option || !isset( $google_api_option['enqueue_google_maps_script'] ) || (isset( $google_api_option['enqueue_google_maps_script'] ) && 'on' === $google_api_option['enqueue_google_maps_script']) ? true : false;

	return apply_filters(
		'et_pb_enqueue_google_maps_script',
		$google_maps_script_enqueue
	);
}
endif;

/**
 * Add pseudo-action via the_content to hook filter/action at the end of main content
 * @param string  content string
 * @return string content string
 */
function et_pb_content_main_query( $content ) {
	global $post, $et_pb_comments_print;

	// Perform filter on main query + if builder is used only
	if ( is_main_query() && et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		add_filter( 'comment_class', 'et_pb_add_non_builder_comment_class', 10, 5 );

		// Actual front-end only adjustment. has_shortcode() can't use passed $content since
		// Its shortcode has been parsed
		if ( false === $et_pb_comments_print && ! et_fb_is_enabled() && has_shortcode( $post->post_content, 'et_pb_comments' ) ) {
			add_filter( 'get_comments_number', '__return_zero' );
			add_filter( 'comments_open', '__return_false' );
			add_filter( 'comments_array', '__return_empty_array' );
		}
	}

	return $content;
}
add_filter( 'the_content', 'et_pb_content_main_query', 1500 );

/**
 * Added special class name for comment items that are placed outside builder
 *
 * See {@see 'comment_class'}.
 *
 * @param  array       $classes    classname
 * @param  string      $comment    comma separated list of additional classes
 * @param  int         $comment_ID comment ID
 * @param  WP_Comment  $comment    comment object
 * @param  int|WP_Post $post_id    post ID or WP_Post object
 *
 * @return array modified classname
 */
function et_pb_add_non_builder_comment_class( $classes, $class, $comment_ID, $comment, $post_id ) {

	$classes[] = 'et-pb-non-builder-comment';

	return $classes;
}

et_builder_load_framework();
