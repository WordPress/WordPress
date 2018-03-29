<?php

define( 'ET_BUILDER_AJAX_TEMPLATES_AMOUNT', apply_filters( 'et_pb_templates_loading_amount', 30 ) );

add_action( 'init', array( 'ET_Builder_Element', 'set_media_queries' ), 11 );

class ET_Builder_Element {
	public $name;
	public $slug;
	public $type;
	public $child_slug;
	public $decode_entities;
	public $fields = array();
	public $whitelisted_fields = array();
	public $fields_unprocessed = array();
	public $main_css_element;
	public $custom_css_options = array();
	public $child_title_var;
	public $child_title_fallback_var;
	public $shortcode_atts = array();
	public $shortcode_content;
	public $post_types = array();
	public $main_tabs = array();
	public $used_tabs = array();
	public $custom_css_tab;
	public $fb_support = false;
	public $dbl_quote_exception_options = array( 'et_pb_font_icon', 'et_pb_button_one_icon', 'et_pb_button_two_icon', 'et_pb_button_icon', 'et_pb_content_new' );
	public $options_toggles = array();

	public static $settings_migrations_initialized = false;
	public static $setting_advanced_styles = false;

	private static $_current_section_index = -1;
	private static $_current_row_index     = -1;
	private static $_current_column_index  = -1;
	private static $_current_module_index  = -1;

	// number of times shortcode_callback function has been executed
	private $_shortcode_callback_num;

	// number of times shortcode_callback function has been executed for the shop module
	// see the returned $object in the _shortcode_passthru_callback() method
	private static $_shop_shortcode_callback_num = 0;

	// priority number, applied to some CSS rules
	private $_style_priority;

	/**
	 * Holds module styles for the current request.
	 *
	 * @var array
	 */
	private static $styles = array();
	private static $internal_modules_styles = array();

	private static $prepare_internal_styles = false;
	private static $internal_modules_counter = 10000;
	private static $media_queries = array();
	private static $modules_order;
	private static $inner_modules_order;
	private static $parent_modules = array();
	private static $child_modules = array();
	private static $ab_tests_processed = array();
	private static $ab_tests_saved_id;
	private static $current_module_index = 0;
	private static $structure_modules = array();
	private static $structure_module_slugs = array();

	private static $loading_backbone_templates = false;

	public static $advanced_styles_manager  = null;
	public static $asm_post_id = 0;

	public static $can_reset_shortcode_indexes = true;

	const DEFAULT_PRIORITY = 10;
	const HIDE_ON_MOBILE   = 'et-hide-mobile';

	function __construct() {
		self::$current_module_index++;

		if ( ! self::$settings_migrations_initialized ) {
			self::$settings_migrations_initialized = true;

			require_once 'module/settings/Migration.php';
			ET_Builder_Module_Settings_Migration::init();

			add_filter( 'the_content', array( get_class( $this ), 'reset_shortcode_indexes' ), 9999 );
		}

		if ( self::$loading_backbone_templates || et_admin_backbone_templates_being_loaded() ) {
			if ( ! self::$loading_backbone_templates ) {
				self::$loading_backbone_templates = true;
			}

			$start_from = (int) sanitize_text_field( $_POST['et_templates_start_from'] );
			$post_type  = sanitize_text_field( $_POST['et_post_type'] );

			if ( 'layout' === $post_type ) {
				// need - 2 to include the et_pb_section and et_pb_row modules
				$start_from = ET_Builder_Element::get_modules_count( 'page' ) - 2;
			}

			$current_module_index = self::$current_module_index - 1;

			if ( ! ( $current_module_index >= $start_from && $current_module_index < ( ET_BUILDER_AJAX_TEMPLATES_AMOUNT + $start_from ) ) ) {
				return;
			}
		}

		if ( null === self::$advanced_styles_manager && ! is_admin() && ! et_fb_is_enabled() ) {
			self::_setup_advanced_styles_manager();
		}

		$this->init();
		$this->make_options_filterable();

		$this->process_whitelisted_fields();
		$this->set_fields();

		$this->_additional_fields_options = array();
		$this->_add_additional_fields();
		$this->_add_custom_css_fields();

		$this->_maybe_add_defaults();

		if ( ! isset( $this->main_css_element ) ) {
			$this->main_css_element = '%%order_class%%';
		}

		$this->_shortcode_callback_num = 0;

		$this->type = isset( $this->type ) ? $this->type : '';

		$this->decode_entities = isset( $this->decode_entities ) ? (bool) $this->decode_entities : false;

		$this->_style_priority = (int) self::DEFAULT_PRIORITY;
		if ( isset( $this->type ) && 'child' === $this->type ) {
			$this->_style_priority = $this->_style_priority + 1;
		} else {
			// add default toggles
			$default_general_toggles = array(
				'admin_label' => array(
					'title'    => esc_html__( 'Admin Label', 'et_builder' ),
					'priority' => 99,
				),
			);

			$default_advanced_toggles = array(
				'visibility' => array(
					'title'    => esc_html__( 'Visibility', 'et_builder' ),
					'priority' => 99,
				),
			);

			$this->_add_option_toggles( 'general', $default_general_toggles );
			$this->_add_option_toggles( 'custom_css', $default_advanced_toggles );
		}

		$this->main_tabs = $this->get_main_tabs();

		$this->custom_css_tab = isset( $this->custom_css_tab ) ? $this->custom_css_tab : true;

		$post_types = ! empty( $this->post_types ) ? $this->post_types : et_builder_get_builder_post_types();

		// all modules should be assigned for et_pb_layout post type to work in the library
		if ( ! in_array( 'et_pb_layout', $post_types ) ) {
			$post_types[] = 'et_pb_layout';
		}

		$this->post_types = apply_filters( 'et_builder_module_post_types', $post_types, $this->slug, $this->post_types );

		foreach ( $this->post_types as $post_type ) {
			if ( ! in_array( $post_type, $this->post_types ) ) {
				$this->register_post_type( $post_type );
			}

			if ( 'child' == $this->type ) {
				self::$child_modules[ $post_type ][ $this->slug ] = $this;
			} else {
				self::$parent_modules[ $post_type ][ $this->slug ] = $this;
			}
		}

		if ( ! isset( $this->no_shortcode_callback ) ) {
			$shortcode_slugs = array( $this->slug );

			if ( ! empty( $this->additional_shortcode_slugs ) ) {
				$shortcode_slugs = array_merge( $shortcode_slugs, $this->additional_shortcode_slugs );
			}

			foreach ( $shortcode_slugs as $shortcode_slug ) {
				add_shortcode( $shortcode_slug, array( $this, '_shortcode_callback' ) );
			}

			if ( isset( $this->additional_shortcode ) ) {
				add_shortcode( $this->additional_shortcode, array( $this, 'additional_shortcode_callback' ) );
			}
		}
	}

	/**
	 * Setup the advanced styles manager
	 *
	 * {@internal
	 *   Before the styles manager was implemented, the advanced styles were output inline in the footer.
	 *   That resulted in them being the last styles parsed by the browser, thus giving them higher
	 *   priority than other styles on the page. With the styles manager, the advanced styles are
	 *   enqueued at the very end of the <head>. This is for backwards compatibility (to maintain
	 *   the same priority for the styles as before).}}
	 */
	private static function _setup_advanced_styles_manager() {
		self::$asm_post_id = $post_id = et_core_page_resource_get_the_ID();

		$is_preview       = is_preview() || is_et_pb_preview();
		$forced_in_footer = et_builder_setting_is_on( 'et_pb_css_in_footer', $post_id );
		$forced_inline    = $is_preview || $forced_in_footer || et_builder_setting_is_off( 'et_pb_static_css_file', $post_id );
		$unified_styles   = ! $forced_inline && ! $forced_in_footer;

		$resource_owner = $unified_styles ? 'core' : 'builder';
		$resource_slug  = $unified_styles ? 'unified' : 'module-design';

		if ( $is_preview ) {
			// Don't let previews cause existing saved static css files to be modified.
			$resource_slug .= '-preview';
		}

		self::$advanced_styles_manager = et_core_page_resource_get( $resource_owner, $resource_slug, $post_id, 40 );

		if ( ! $forced_inline && ! $forced_in_footer && self::$advanced_styles_manager->has_file() ) {
			// This post currently has a fully configured styles manager.
			return;
		}

		self::$advanced_styles_manager->forced_inline       = $forced_inline;
		self::$advanced_styles_manager->write_file_location = 'footer';

		if ( $forced_in_footer || $forced_inline ) {
			// Restore legacy behavior--output inline styles in the footer.
			self::$advanced_styles_manager->set_output_location( 'footer' );
		}

		// Schedule callback to run in the footer so we can pass the module design styles to the page resource.
		add_action( 'wp_footer', array( 'ET_Builder_Element', 'set_advanced_styles' ), 19 );

		// Add filter for the resource data so we can prevent theme customizer css from being
		// included with the builder css inline on first-load (since its in the head already).
		add_filter( 'et_core_page_resource_get_data', array( 'ET_Builder_Element', 'filter_page_resource_data' ), 10, 3 );
	}

	/**
	 * Passes the module design styles for the current page to the advanced styles manager.
	 * {@see 'wp_footer' (9) Must run before the style manager's footer callback}
	 */
	public static function set_advanced_styles() {
		$styles = self::get_style() . self::get_style( true ) . et_pb_get_page_custom_css();

		// Pass styles to page resource which will handle their output
		self::$advanced_styles_manager->set_data( $styles, 40 );
	}

	/**
	 * Filters the unified page resource data. The data is an array of arrays of strings keyed by
	 * priority. The builder's styles are set with a priority of 40. Here we want to make sure
	 * only the builder's styles are output in the footer on first-page load so we aren't
	 * duplicating the customizer and custom css styles which are already in the <head>.
	 * {@see 'et_core_page_resource_get_data'}
	 */
	public static function filter_page_resource_data( $data, $context, $resource ) {
		global $wp_current_filter;

		if ( 'inline' !== $context || ! in_array( 'wp_footer', $wp_current_filter ) ) {
			return $data;
		}

		if ( false === strpos( $resource->slug, 'unified' ) ) {
			return $data;
		}

		return isset( $data[40] ) ? array( 40 => $data[40] ) : array();
	}

	function process_whitelisted_fields() {
		$fields = array();

		// Append _builder_version to all module
		$this->whitelisted_fields[] = '_builder_version';

		foreach ( $this->whitelisted_fields as $key ) {
			$fields[ $key ] = array();
		}

		$this->whitelisted_fields = $fields;
	}

	/**
	 * Set $this->fields_unprocessed property to all field settings on backend.
	 * Store only default settings for use in shortcode_callback() on frontend.
	 */
	function set_fields() {
		$fields_defaults = array();
		$module_defaults = isset( $this->fields_defaults ) && is_array( $this->fields_defaults )
			? $this->fields_defaults
			: array();

		if ( ! empty( $module_defaults ) ) {
			foreach ( $module_defaults as $key => $default_setting ) {
				$setting_fields = array();

				$default_value = $module_defaults[ $key ][0];

				$use_default_value = isset( $module_defaults[ $key ][1] ) && 'add_default_setting' === $module_defaults[ $key ][1];
				$use_only_default_value = isset( $module_defaults[ $key ][1] ) && 'only_default_setting' === $module_defaults[ $key ][1];

				/**
				 * If default value is set, it should be used for "shortcode_default",
				 * unless 'only_default_setting' is set
				 */
				if ( ! $use_only_default_value ) {
					$setting_fields['shortcode_default'] = $default_value;
				}

				/**
				 * Add "default" setting and set it to the default value,
				 * if 'add_default_setting' or 'only_default_setting' is provided
				 */
				if ( $use_default_value || $use_only_default_value ) {
					$setting_fields['default'] = $default_value;
				}

				$fields_defaults[ $key ] = $setting_fields;
			}
		}

		/**
		 * Only use whitelisted fields names on frontend.
		 * All fields settings are only needed in Page Builder.
		 *
		 * Logic summary:
		 * If is_admin(): do not load only whitelisted
		 * If !is_admin(): load only whitelisted if !et_fb_is_enabled()
		 */

		$only_whitelisted_fields = is_admin() ? false : ( et_fb_is_enabled() ? false : true );

		$fields = $only_whitelisted_fields ? $this->whitelisted_fields : $this->get_fields();

		# update settings with defaults
		foreach ( $fields as $key => $settings ) {
			if ( ! empty( $this->defaults ) && isset( $this->defaults[ $key ] ) ) {
				$fields[ $key ]['default'] = $this->defaults[ $key ];
				continue;
			}

			if ( ! isset( $fields_defaults[ $key ] ) ) {
				continue;
			}

			$settings = array_merge( $settings, $fields_defaults[ $key ] );

			$fields[ $key ] = $settings;
		}

		// Add _builder_version field to all modules
		$fields['_builder_version'] = array( 'type' => 'skip' );

		$this->fields_unprocessed = $fields;
	}

	private function register_post_type( $post_type ) {
		$this->post_types[] = $post_type;
		self::$parent_modules[ $post_type ] = array();
		self::$child_modules[ $post_type ] = array();
	}

	/**
	 * Double quote are saved as "%22" in shortcode attributes.
	 * Decode them back into "
	 *
	 * @return void
	 */
	private function _decode_double_quotes() {
		if ( ! isset( $this->shortcode_atts ) ) {
			return;
		}

		$shortcode_attributes = array();
		$font_icon_options = array( 'font_icon', 'button_icon', 'button_one_icon', 'button_two_icon' );

		foreach ( $this->shortcode_atts as $attribute_key => $attribute_value ) {
			$shortcode_attributes[ $attribute_key ] = in_array( $attribute_key, $font_icon_options ) || preg_match( "/^\%\%\d+\%\%$/i", $attribute_value ) ? $attribute_value : str_replace( array( '%22', '%92', '%91', '%93' ), array( '"', '\\', '&#91;', '&#93;' ), $attribute_value );
		}

		$this->shortcode_atts = $shortcode_attributes;
	}

	/**
	 * Provide a way for sub-class to access $this->_shortcode_callback_num without a chance to alter its value
	 *
	 * @return int
	 */
	protected function shortcode_callback_num() {
		return $this->_shortcode_callback_num;
	}

	/**
	 * check whether ab testing enabled for current module and calculate whether it should be displayed currently or not
	 *
	 * @return bool
	 */
	private function _is_display_module( $shortcode_atts ) {
		$ab_subject_id = isset( $shortcode_atts['ab_subject_id'] ) && '' !== $shortcode_atts['ab_subject_id'] ? $shortcode_atts['ab_subject_id'] : false;

		// return true if testing is disabled or current module has no subject id.
		if ( ! $ab_subject_id ) {
			return true;
		}

		return $this->_check_ab_test_subject( $ab_subject_id );
	}

	/**
	 * check whether the current module should be displayed or not
	 *
	 * @return bool
	 */
	private function _check_ab_test_subject( $ab_subject_id = false ) {
		if ( ! $ab_subject_id ) {
			return true;
		}

		$ab_subject_id = intval( $ab_subject_id );

		$test_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );

		$test_id = (int) $test_id;

		// return false if the current ab module was processed already
		if ( isset( $this->ab_tests_processed[ $test_id ] ) && $this->ab_tests_processed[ $test_id ] ) {
			return false;
		}

		$user_unique_id = et_pb_get_visitor_id();
		$saved_module_id = $this->_get_saved_ab_module_id( $test_id, $user_unique_id );

		$current_ab_module_id = et_pb_ab_get_current_ab_module_id( $test_id, $saved_module_id );

		// return false if current module is not the module which should be displayed this time
		if ( (int) $current_ab_module_id !== (int) $ab_subject_id ) {
			return false;
		}

		// If current loop is advanced styles being populated, skip it
		if ( ! self::$setting_advanced_styles ) {
			// mark current ab module as processed
			$this->ab_tests_processed[ $test_id ] = true;
		}

		if ( false === $saved_module_id ) {
			// log the view_page event right away
			et_pb_add_stats_record( array(
					'test_id'     => $test_id,
					'subject_id'  => $ab_subject_id,
					'record_type' => 'view_page',
				)
			);

			// increment the module id for the next time
			et_pb_ab_increment_current_ab_module_id( $test_id, $user_unique_id );
		}

		return true;
	}

	private function _get_saved_ab_module_id( $test_id, $client_id ) {
		if ( ! empty( $this->ab_tests_saved_id[ $test_id ] ) ) {
			return $this->ab_tests_saved_id[ $test_id ];
		}

		$saved_module_id = et_pb_ab_get_saved_ab_module_id( $test_id, $client_id );

		if ( false !== $saved_module_id ) {
			// cache the retrieved value
			$this->ab_tests_saved_id[ $test_id ] = $saved_module_id;
		}

		return $saved_module_id;
	}

	public static function reset_shortcode_indexes( $content = '' ) {
		if ( ! self::$can_reset_shortcode_indexes || ! is_main_query() ) {
			return $content;
		}

		if ( '' !== $content && false === strpos( $content, '[et_pb_' ) ) {
			// At least one builder section should be present.
			return $content;
		}

		global $wp_current_filter;

		if ( in_array( 'the_content', $wp_current_filter ) ) {
			$call_counts = array_count_values( $wp_current_filter );

			if ( $call_counts['the_content'] > 1 ) {
				// This is a nested call. We only want to reset indexes after the top-most call.
				return $content;
			}
		}

		self::$_current_section_index       = -1;
		self::$_current_row_index           = -1;
		self::$_current_column_index        = -1;
		self::$_current_module_index        = -1;

		return $content;
	}

	function _get_current_shortcode_address() {
		// Yuck! :-/
		if ( false !== strpos( $this->slug, '_section' ) ) {
			self::$_current_section_index++;
			self::$_current_row_index    = -1;
			self::$_current_column_index = -1;
			self::$_current_module_index = -1;
		} else if ( false !== strpos( $this->slug, '_row' ) ) {
			self::$_current_row_index++;
			self::$_current_column_index = -1;
			self::$_current_module_index = -1;
		} else if ( false !== strpos( $this->slug, '_column' ) ) {
			self::$_current_column_index++;
			self::$_current_module_index = -1;
		} else {
			self::$_current_module_index++;
		}

		$address = self::$_current_section_index;
		$parts   = array( self::$_current_row_index, self::$_current_column_index, self::$_current_module_index );

		foreach ( $parts as $part ) {
			if ( $part > -1 ) {
				$address .= ".{$part}";
			}
		}

		return $address;
	}

	function _shortcode_callback( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
		global $et_fb_processing_shortcode_object;

		$this->shortcode_atts = shortcode_atts( $this->get_shortcode_fields(), $atts );

		$this->_decode_double_quotes();

		$this->_maybe_remove_default_atts_values();

		$_address = $this->_get_current_shortcode_address();

		$this->shortcode_atts = apply_filters( 'et_pb_module_shortcode_attributes', $this->shortcode_atts, $atts, $this->slug, $_address );

		$global_shortcode_content = false;

		$ab_testing_enabled = et_is_ab_testing_active();

		$hide_subject_module = false;

		$post_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );

		$global_module_id = $this->shortcode_atts['global_module'];

		// If the section/row/module is disabled, hide it
		if ( isset( $this->shortcode_atts['disabled'] ) && 'on' === $this->shortcode_atts['disabled'] && ! $et_fb_processing_shortcode_object ) {
			return;
		}

		// need to perform additional check and some modifications in case AB testing enabled
		if ( $ab_testing_enabled ) {
			// check if ab testing enabled for this module and if it shouldn't be displayed currently
			if ( ! $et_fb_processing_shortcode_object && ! $this->_is_display_module( $this->shortcode_atts ) && ! et_pb_detect_cache_plugins() ) {
				return;
			}

			// add class to the AB testing subject if needed
			if ( isset( $this->shortcode_atts['ab_subject_id'] ) && '' !== $this->shortcode_atts['ab_subject_id'] ) {
				$subject_class = sprintf( ' et_pb_ab_subject et_pb_ab_subject_id-%1$s_%2$s',
					esc_attr( $post_id ),
					esc_attr( $this->shortcode_atts['ab_subject_id'] )
				);
				$this->shortcode_atts['module_class'] = isset( $this->shortcode_atts['module_class'] ) && '' !== $this->shortcode_atts['module_class'] ? $this->shortcode_atts['module_class'] . $subject_class : $subject_class;

				if ( et_pb_detect_cache_plugins() ) {
					$hide_subject_module = true;
				}
			}

			// add class to the AB testing goal if needed
			if ( isset( $this->shortcode_atts['ab_goal'] ) && 'on' === $this->shortcode_atts['ab_goal'] ) {
				$goal_class = sprintf( ' et_pb_ab_goal et_pb_ab_goal_id-%1$s', esc_attr( $post_id ) );
				$this->shortcode_atts['module_class'] = isset( $this->shortcode_atts['module_class'] ) && '' !== $this->shortcode_atts['module_class'] ? $this->shortcode_atts['module_class'] . $goal_class : $goal_class;
			}
		}

		//override module attributes for global module. Skip that step while processing Frontend Builder object
		if ( ! empty( $global_module_id ) && ! $et_fb_processing_shortcode_object ) {
			$global_content = et_pb_load_global_module( $global_module_id );

			if ( '' !== $global_content ) {
				$unsynced_global_attributes = get_post_meta( $global_module_id, '_et_pb_excluded_global_options' );
				$use_updated_global_sync_method = ! empty( $unsynced_global_attributes );

				$unsynced_options = ! empty( $unsynced_global_attributes[0] ) ? json_decode( $unsynced_global_attributes[0], true ) : array() ;
				$content_synced = $use_updated_global_sync_method && ! in_array( 'et_pb_content_field', $unsynced_options );

				// support legacy selective sync system
				if ( ! $use_updated_global_sync_method ) {
					$content_synced = ! isset( $this->shortcode_atts['saved_tabs'] ) || false !== strpos( $this->shortcode_atts['saved_tabs'], 'general' ) || 'all' === $this->shortcode_atts['saved_tabs'];
				}

				if ( $content_synced ) {
					$global_shortcode_content = et_pb_get_global_module_content( $global_content, $function_name );
				}

				// cleanup the shortcode string to avoid the attributes messing with content
				$global_content_processed = false !== $global_shortcode_content ? str_replace( $global_shortcode_content, '', $global_content ) : $global_content;
				$global_atts = shortcode_parse_atts( $global_content_processed );

				foreach( $this->shortcode_atts as $single_attr => $value ) {
					if ( isset( $global_atts[$single_attr] ) && ! in_array( $single_attr, $unsynced_options ) ) {
						// replace %22 with double quotes in options to make sure it's rendered correctly
						$this->shortcode_atts[$single_attr] = is_string( $global_atts[$single_attr] ) && ! in_array( $single_attr, $this->dbl_quote_exception_options ) ? str_replace( '%22', '"', $global_atts[$single_attr] ) : $global_atts[$single_attr];
					}
				}
			}
		}

		self::set_order_class( $function_name );

		$this->pre_shortcode_content();

		$content = false !== $global_shortcode_content ? $global_shortcode_content : $content;

		if ( $et_fb_processing_shortcode_object ) {
			$this->shortcode_content = et_pb_fix_shortcodes( $content, $this->decode_entities );
		} else {
			$this->shortcode_content = ! ( isset( $this->is_structure_element ) && $this->is_structure_element ) ? do_shortcode( et_pb_fix_shortcodes( $content, $this->decode_entities ) ) : '';
		}

		$this->shortcode_atts();

		$this->process_additional_options( $function_name );
		$this->process_custom_css_options( $function_name );

		// load inline fonts if needed
		if ( isset( $this->shortcode_atts['inline_fonts'] ) ) {
			$this->process_inline_fonts_option( $this->shortcode_atts['inline_fonts'] );
		}

		// Prepare shortcode for the frontend building if enabled.
		$shortcode_callback = $et_fb_processing_shortcode_object ? '_shortcode_passthru_callback' : 'shortcode_callback';

		$output = $this->{$shortcode_callback}( $atts, $content, $function_name, $parent_address, $global_parent, $global_parent_type );

		$this->_shortcode_callback_num++;

		// Hide module on specific screens if needed
		if ( isset( $this->shortcode_atts['disabled_on'] ) && '' !== $this->shortcode_atts['disabled_on'] ) {
			$disabled_on_array = explode( '|', $this->shortcode_atts['disabled_on'] );
			$i = 0;
			$current_media_query = 'max_width_767';

			foreach( $disabled_on_array as $value ) {
				if ( 'on' === $value ) {
					ET_Builder_Module::set_style( $function_name, array(
						'selector'    => '%%order_class%%',
						'declaration' => 'display: none !important;',
						'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
					) );
				}
				$i++;
				$current_media_query = 1 === $i ? '768_980' : 'min_width_981';
			}
		}

		if ( $hide_subject_module ) {
			$previous_subjects_cache = get_post_meta( $post_id, 'et_pb_subjects_cache', true );

			if ( empty( $previous_subjects_cache ) ) {
				$previous_subjects_cache = array();
			}

			if ( empty( $this->template_name ) ) {
				$previous_subjects_cache[ $this->shortcode_atts['ab_subject_id'] ] = $output;
			} else {
				$previous_subjects_cache[ $this->shortcode_atts['ab_subject_id'] ] = $this->shortcode_output();
			}

			// update the subjects cache in post meta to use it later
			update_post_meta( $post_id, 'et_pb_subjects_cache', $previous_subjects_cache );

			// generate the placeholder to output on front-end instead of actual content
			$subject_placeholder = sprintf( '<div class="et_pb_subject_placeholder et_pb_subject_placeholder_id_%1$s" style="display: none;"></div>', esc_attr( $this->shortcode_atts['ab_subject_id'] ) );

			return $subject_placeholder;
		}

		if ( empty( $this->template_name ) ) {
			return $output;
		}

		return $this->shortcode_output();
	}

	/**
	 * Delete default shortcode attribute values, defined in ET_Global_Settings class
	 * @return void
	 */
	private function _maybe_remove_default_atts_values() {
		$fields = $this->fields_unprocessed;

		// Non stylesheet attributes should've been passed
		$must_print_fields = apply_filters( $this->slug . '_must_print_attributes', array( 'text_orientation' ) );

		foreach ( $fields as $field_key => $field_settings ) {
			$global_setting_name  = $this->get_global_setting_name( $field_key );
			$global_setting_value = ET_Global_Settings::get_value( $global_setting_name );
			$shortcode_attr_value = ! empty( $this->shortcode_atts[ $field_key ] ) ? $this->shortcode_atts[ $field_key ] : '';

			// Don't do anything if there is no default or actual value for a setting
			// or shortcode attribute is no set
			if ( ! $global_setting_value || '' === $shortcode_attr_value ) {
				continue;
			}

			// Delete shortcode attribute value if it equals to the default global value
			if ( ! in_array( $field_key, $must_print_fields ) && $global_setting_value === $shortcode_attr_value ) {
				$this->shortcode_atts[ $field_key ] = '';
			}
		}
	}

	function shortcode_output() {
		$this->shortcode_atts['content'] = $this->shortcode_content;
		extract( $this->shortcode_atts );
		ob_start();
		require( locate_template( $this->template_name . '.php' ) );
		return ob_get_clean();
	}

	function shortcode_atts_to_data_atts( $atts = array() ) {
		if ( empty( $atts ) ) {
			return;
		}

		$output = array();
		foreach ( $atts as $attr ) {
			$output[] = 'data-' . esc_attr( $attr ) . '="' . esc_attr( $this->shortcode_atts[ $attr ] ) . '"';
		}

		return implode( ' ', $output );
	}

	// intended to be overridden as needed
	function shortcode_atts(){}

	// intended to be overridden as needed
	function pre_shortcode_content(){}

	// intended to be overridden as needed
	function shortcode_callback( $atts, $content = null, $function_name ){}

	function _shortcode_passthru_callback( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
		global $post;

		// this is called during pageload, but we want to ignore that round, as this data will be built and returned on separate ajax request instead
		if ( ! isset( $_POST['action'] ) ) {
			return false;
		}

		$attrs = array();
		$fields = $this->process_fields( $this->fields_unprocessed );
		$global_shortcode_content = false;
		$function_name_processed = et_fb_prepare_tag( $function_name );
		$unsynced_global_attributes = array();
		$use_updated_global_sync_method = false;
		$global_module_id = isset( $atts['global_module'] ) ? $atts['global_module'] : false;
		$is_global_template = false;

		// Add support of new selective sync feature for library modules in VB
		if ( isset( $_POST['et_post_type'], $_POST['et_post_id'], $_POST['et_layout_type'] ) && 'et_pb_layout' === $_POST['et_post_type'] && 'module' === $_POST['et_layout_type'] ) {
			$template_scope = wp_get_object_terms( $_POST['et_post_id'], 'scope' );
			$is_global_template = ! empty( $template_scope[0] ) && 'global' === $template_scope[0]->slug;

			if ( $is_global_template ) {
				$global_module_id = $_POST['et_post_id'];
			}
		}

		//override module attributes for global module
		if ( ! empty( $global_module_id ) ) {
			if ( ! in_array( $function_name, array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column', 'et_pb_column_inner' ) ) ) {
				$processing_global_module = $global_module_id;
				$unsynced_global_attributes = get_post_meta( $processing_global_module, '_et_pb_excluded_global_options' );
				$use_updated_global_sync_method = ! empty( $unsynced_global_attributes );
			}

			$global_content = et_pb_load_global_module( $global_module_id );

			if ( '' !== $global_content ) {
				$unsynced_options = ! empty( $unsynced_global_attributes[0] ) ? json_decode( $unsynced_global_attributes[0], true ) : array() ;
				$content_synced = $use_updated_global_sync_method && ! in_array( 'et_pb_content_field', $unsynced_options );
				$is_module_fully_global = $use_updated_global_sync_method && empty( $unsynced_options );
				$unsynced_legacy_options = array();

				// support legacy selective sync system
				if ( ! $use_updated_global_sync_method ) {
					$content_synced = ! isset( $atts['saved_tabs'] ) || false !== strpos( $atts['saved_tabs'], 'general' ) || 'all' === $atts['saved_tabs'];
					$is_module_fully_global = ! isset( $atts['saved_tabs'] ) || 'all' === $atts['saved_tabs'];
				}

				if ( $content_synced && ! $is_global_template ) {
					$global_shortcode_content = et_pb_get_global_module_content( $global_content, $function_name_processed );
				}

				// remove the shortcode content to avoid conflicts of parent attributes with similar attrs from child modules
				if ( false !== $global_shortcode_content ) {
					$global_content_processed = str_replace( $global_shortcode_content, '', $global_content );
				} else {
					$global_content_processed = $global_content;
				}

				// Ensuring that all possible attributes exist to avoid remaining child attributes being used by global parents' attributes
				// Do that only in case the module is fully global
				if ( $is_module_fully_global ) {
					$global_atts = wp_parse_args(
						shortcode_parse_atts( et_pb_remove_shortcode_content( $global_content_processed, $this->slug ) ),
						array_map( '__return_empty_string', $this->whitelisted_fields )
					);
				} else {
					$global_atts = shortcode_parse_atts( $global_content_processed );
				}

				foreach( $this->shortcode_atts as $single_attr => $value ) {
					if ( isset( $global_atts[$single_attr] ) && ! in_array( $single_attr, $unsynced_options ) ) {
						// replace %22 with double quotes in options to make sure it's rendered correctly
						if ( ! $is_global_template ) {
							$this->shortcode_atts[$single_attr] = is_string( $global_atts[$single_attr] ) && ! in_array( $single_attr, $this->dbl_quote_exception_options ) ? str_replace( '%22', '"', $global_atts[$single_attr] ) : $global_atts[$single_attr];
						}
					} else if ( ! $use_updated_global_sync_method ) {
						// prepare array of unsynced options to migrate the legacy modules to new system
						$unsynced_legacy_options[] = $single_attr;
					} else {
						$unsynced_global_attributes[0] = $unsynced_options;
					}
				}

				// migrate unsynced options to the new selective sync method
				if ( ! $use_updated_global_sync_method ) {
					$unsynced_global_attributes[0] = $unsynced_legacy_options;

					// check the content and add it into list if needed.
					if ( ! $content_synced ) {
						$unsynced_global_attributes[0][] = 'et_pb_content_field';
					}
				} else {
					$unsynced_global_attributes[0] = $unsynced_options;
				}
			} else {
				// remove global_module attr if it doesn't exist in DB
				$this->shortcode_atts['global_module'] = '';
				$global_parent = '';
			}
		}

		// Create secondary attribute for transparent_background in VB for precise comparison to avoid saving default value
		if ( et_is_builder_plugin_active() && 'et_pb_section' === $this->slug && isset( $this->shortcode_atts['transparent_background'] ) && '' !== $this->shortcode_atts['transparent_background'] ) {
			$attrs['transparent_background_fb'] = $this->shortcode_atts['transparent_background'];
		}

		foreach( $this->shortcode_atts as $shortcode_attr_key => $shortcode_attr_value ) {
			if ( isset( $fields[ $shortcode_attr_key ]['type'] ) && 'computed' === $fields[ $shortcode_attr_key ]['type'] ) {

				$field = $fields[ $shortcode_attr_key ];
				$depends_on = array();

				foreach ( $field['computed_depends_on'] as $depends_on_field ) {
					$dependency_value = $this->shortcode_atts[ $depends_on_field ];

					if ( '' === $dependency_value ) {
						if ( isset( $this->fields_unprocessed[ $depends_on_field]['default'] ) ) {
							$dependency_value = $this->fields_unprocessed[ $depends_on_field ]['default'];
						}

						if ( isset( $this->fields_unprocessed[ $depends_on_field]['shortcode_default'] ) ) {
							$dependency_value = $this->fields_unprocessed[ $depends_on_field ]['shortcode_default'];
						}
					}

					$depends_on[ $depends_on_field ] = $dependency_value;
				}

				if ( ! is_callable( $field['computed_callback'] ) ) {
					die( $shortcode_attr_key . ' Callback:' . $field['computed_callback'] . ' is not callable.... '); // TODO, fix this make it more graceful...
				}

				$value = call_user_func( $field['computed_callback'], $depends_on );
			} else {
				$value = $shortcode_attr_value;
			}

			// dont set the default, unless, lol, the value is literally 'default'
			// NOTE: bypass shortcode trimming for section's transparent background attribute in plugin, to preserve BB behaviour in VB
			// which is loading 'default' if no attribute found, then switch it accordinly to either on/off on settings modal saving process
			$is_plugin_section_transparent_background = et_is_builder_plugin_active() && 'et_pb_section' === $this->slug && 'transparent_background' === $shortcode_attr_key;
			if ( isset( $fields[ $shortcode_attr_key ]['default'] ) && $value === $fields[ $shortcode_attr_key ]['default'] && $value !== 'default' && ! $is_plugin_section_transparent_background ) {
				$value = '';
			}

			// dont set the default, unless, lol, the value is literally 'default'
			if ( isset( $fields[ $shortcode_attr_key ]['shortcode_default'] ) && $value === $fields[ $shortcode_attr_key ]['shortcode_default'] && $value !== 'default' ) {
				$value = '';
			}

			// dont set the default, unless, lol, the value is literally 'default'
			if ( isset( $this->fields_defaults[ $shortcode_attr_key ] ) && $value === $this->fields_defaults[ $shortcode_attr_key ][0] && $value !== 'default' ) {
				$value = '';
			}

			// generic override, disabled=off is an unspoken default
			if ( $shortcode_attr_key === 'disabled' && $shortcode_attr_value === 'off' ) {
				$value = '';
			}

			// this override is necessary becuase et_pb_column and et_pb_column_inner type default is 4_4 and will get stomped
			// above since its default, but we need it explicitly set anyways, so we force set it
			if ( in_array( $function_name, array( 'et_pb_column', 'et_pb_column_inner' ) ) && $shortcode_attr_key === 'type' ) {
				$value = $shortcode_attr_value;
			}

			if ( '' !== $value ) {
				$attrs[$shortcode_attr_key] = is_string($value) ? html_entity_decode($value) : $value;
			}
		}

		// Format FB component path
		// TODO, move this to class method and property, and allow both to be overridden
		$component_path = str_replace( 'et_pb_' , '', $function_name_processed );
		$component_path = str_replace( '_', '-', $component_path );
		$component_path = $component_path;

		$_i = isset( $atts['_i'] ) ? $atts['_i'] : 0;
		$address = isset( $atts['_address'] ) ? $atts['_address'] : '0';

		// set the global parent if exists
		if ( ( ! isset( $attrs['global_module'] ) || '' === $attrs['global_module'] ) && '' !== $global_parent ) {
			$attrs['global_parent'] = $global_parent;
		}

		if ( isset( $this->is_structure_element ) && $this->is_structure_element ) {
			$this->fb_support = true;
		}

		if ( ! $this->fb_support ) {
			global $et_fb_processing_shortcode_object;
			$et_fb_processing_shortcode_object = false;
			$raw_child_content = $content;
			$content = $this->_shortcode_callback( $atts, $content, $function_name_processed );
			$executed_shortcode = do_shortcode( $content );
			$processed_content = false !== $global_shortcode_content ? $global_shortcode_content : $this->shortcode_content;
			$attrs['content_new'] = array_key_exists( 'content_new', $this->whitelisted_fields ) ? $processed_content : et_fb_process_shortcode( $processed_content, $address, $global_parent, $global_parent_type );
			$et_fb_processing_shortcode_object = true;
		} else {
			$processed_content = false !== $global_shortcode_content ? $global_shortcode_content : $this->shortcode_content;
			$content = array_key_exists( 'content_new', $this->whitelisted_fields ) || 'et_pb_code' === $function_name_processed || 'et_pb_fullwidth_code' === $function_name_processed ? $processed_content : et_fb_process_shortcode( $processed_content, $address, $global_parent, $global_parent_type );
		}

		if ( is_array( $content ) ) {
			$prepared_content = $content;
		} else {
			if ( $this->fb_support && count( preg_split('/\r\n*\n/', trim( $content ), -1, PREG_SPLIT_NO_EMPTY ) ) > 1 ) {
				$prepared_content = wpautop( $content );
			} else {
				$prepared_content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');
			}
		}

		if ( empty( $attrs ) ) {
			// Visual Builder expects $attrs to be an object.
			// Associative array converted to an object by json_encode correctly, but empty array is not and it causes issues.
			$attrs = new stdClass();
		}

		$module_type = $this->type;

		// Ensuring that module which uses another module's template (i.e. accordion item uses toggle's
		// component) has correct $this->type value. This is covered on front-end, but it causes inheriting
		// module uses its template's value on _shortcode_passthru_callback()
		if ( $this->slug !== $function_name && isset( $_POST ) && isset( $_POST['et_post_type'] ) ) {
			$et_post_type = $_POST['et_post_type'];
			$parent_modules = self::get_parent_modules( $et_post_type);
			$function_module = false;

			if ( isset( $parent_modules[ $function_name ] ) ) {
				$function_module = $parent_modules[ $function_name ];
			} else {
				$child_modules = self::get_child_modules( $et_post_type );

				if ( isset( $child_modules[ $function_name] ) ) {
					$function_module = $child_modules[ $function_name ];
				}
			}

			if ( $function_module && isset( $function_module->type ) ) {
				$module_type = $function_module->type;
			}
		}

		// Get the current shortcode index
		$shortcode_index = $this->_shortcode_callback_num;

		// If this is a shop module use the Shop module shortcode index
		// Shop module creates a new class instance which resets the $_shortcode_callback_num value
		// ( see get_shop_html() method of ET_Builder_Module_Shop class in main-modules.php )
		// so we use a static property to track its proper shortcode index
		if ( 'et_pb_shop' === $function_name ) {
			$shortcode_index = self::$_shop_shortcode_callback_num;
			self::$_shop_shortcode_callback_num++;
		}

		// Build object.
		$object = array(
			'_i'                => $_i,
			'_order'            => $_i,
			// TODO make address be _address, its conflicting with 'address' prop in map module... (not sure how though, they are in diffent places...)
			'address'           => $address,
			'child_slug'        => $this->child_slug,
			'fb_support'        => $this->fb_support,
			'parent_address'    => $parent_address,
			'shortcode_index'   => $shortcode_index,
			'type'              => $function_name,
			'component_path'    => $component_path,
			'main_css_element'  => $this->main_css_element,
			'attrs'             => $attrs,
			'content'           => $prepared_content,
			'is_module_child'   => 'child' === $module_type,
			'prepared_styles'   => ! $this->fb_support ? ET_Builder_Element::get_style() : '',
		);

		if ( ! empty( $unsynced_global_attributes ) ) {
			$object['unsyncedGlobalSettings'] = $unsynced_global_attributes[0];
		}

		if ( $is_global_template ) {
			$object['libraryModuleScope'] = 'global';
		}

		if ( ! $this->fb_support ) {
			if ( !empty( $this->child_slug ) ) {
				$object['raw_child_content'] = $raw_child_content;
			}
		}

		return $object;
	}

	// intended to be overridden as needed
	function additional_shortcode_callback( $atts, $content = null, $function_name ){}

	// intended to be overridden as needed
	function predefined_child_modules(){}

	/**
	 * Generate global setting name
	 * @param  string $option_slug  Option slug
	 * @return string               Global setting name in the following format: "module_slug-option_slug"
	 */
	public function get_global_setting_name( $option_slug ) {
		$global_setting_name = sprintf(
			'%1$s-%2$s',
			isset( $this->global_settings_slug ) ? $this->global_settings_slug : $this->slug,
			$option_slug
		);

		return $global_setting_name;
	}

	/**
	 * Add global default values to all fields, if they don't have defaults set
	 *
	 * @return void
	 */
	private function _maybe_add_defaults() {
		// Don't add default settings to "child" modules
		if ( 'child' === $this->type ) {
			return;
		}

		$fields       = $this->fields_unprocessed;
		$ignored_keys = array(
			'custom_margin',
			'custom_padding',
		);

		// Font color settings have custom_color set to true, so add them to ingored keys array
		if ( ! empty( $this->advanced_options['fonts'] ) && is_array( $this->advanced_options['fonts'] ) ) {
			foreach ( $this->advanced_options['fonts'] as $font_key => $font_settings ) {
				$ignored_keys[] = sprintf( '%1$s_text_color', $font_key );
			}
		}

		$ignored_keys = apply_filters( 'et_builder_add_defaults_ignored_keys', $ignored_keys );

		foreach ( $fields as $field_key => $field_settings ) {
			if ( in_array( $field_key, $ignored_keys ) ) {
				continue;
			}

			$global_setting_name  = $this->get_global_setting_name( $field_key );
			$global_setting_value = ET_Global_Settings::get_value( $global_setting_name );

			if ( ! isset( $field_settings['default'] ) && $global_setting_value ) {
				$fields[ $field_key ]['default'] = $fields[ $field_key ]['shortcode_default'] = $global_setting_value;
			}
		}

		$this->fields_unprocessed = $fields;
	}

	private function _add_additional_fields() {
		if ( ! isset( $this->advanced_options ) ) {
			return false;
		}

		$this->_add_additional_font_fields();

		$this->_add_additional_background_fields();

		$this->_add_additional_border_fields();

		$this->_add_additional_custom_margin_padding_fields();

		$this->_add_additional_button_fields();

		if ( ! isset( $this->_additional_fields_options ) ) {
			return false;
		}

		$additional_options = $this->_additional_fields_options;

		if ( ! empty( $additional_options ) ) {
			// delete second level advanced options default values
			if ( isset( $this->type ) && 'child' === $this->type && apply_filters( 'et_pb_remove_child_module_defaults', true ) ) {
				$default_keys = array( 'default', 'shortcode_default' );

				foreach ( $additional_options as $name => $settings ) {
					foreach ( $default_keys as $default_key ) {
						if ( isset( $additional_options[ $name ][ $default_key ] ) && ! isset( $additional_options[ $name ]['default_on_child'] ) ) {
							$additional_options[ $name ][ $default_key ] = '';
						}
					}
				}
			}

			$this->fields_unprocessed = array_merge( $this->fields_unprocessed, $additional_options );
		}
	}

	private function _add_additional_font_fields() {
		if ( ! isset( $this->advanced_options['fonts'] ) ) {
			return;
		}

		$advanced_font_options = $this->advanced_options['fonts'];

		$additional_options = array();
		$defaults = array(
			'all_caps' => 'off',
		);

		foreach ( $advanced_font_options as $option_name => $option_settings ) {
			$advanced_font_options[ $option_name ]['defaults'] = $defaults;
		}

		$this->advanced_options['fonts'] = $advanced_font_options;
		$font_options_count = 0;

		foreach ( $advanced_font_options as $option_name => $option_settings ) {
			$font_options_count++;

			$option_settings = wp_parse_args( $option_settings, array(
				'label'          => '',
				'font_size'      => array(),
				'letter_spacing' => array(),
				'font'           => array(),
			) );

			$toggle_disabled = isset( $option_settings['disable_toggle'] ) && $option_settings['disable_toggle'];
			$tab_slug = isset( $option_settings['tab_slug'] ) ? $option_settings['tab_slug'] : 'advanced';
			$toggle_slug = '';

			if ( ! $toggle_disabled ) {
				$toggle_slug = isset( $option_settings['toggle_slug'] ) ? $option_settings['toggle_slug'] : $option_name;

				if ( ! isset( $option_settings['toggle_slug'] ) ) {
					$font_toggle = array(
						$option_name => array(
							'title'    => sprintf( '%1$s %2$s', esc_html( $option_settings['label'] ), esc_html__( 'Text', 'et_builder' ) ),
							'priority' => 50 + $font_options_count,
						),
					);

					$this->_add_option_toggles( $tab_slug, $font_toggle );
				}
			}

			if ( ! isset( $option_settings['hide_font'] ) || ! $option_settings['hide_font'] ) {
				$additional_options["{$option_name}_font"] = wp_parse_args( $option_settings['font'], array(
					'label'           => sprintf( esc_html__( '%1$s Font', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'font',
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $option_name,
				) );

				// add reference to the obsolete "all caps" option if needed
				if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] ) {
					$additional_options["{$option_name}_font"]['attributes'] = array( 'data-old-option-ref' => "{$option_name}_all_caps" );
				}

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_font"]['depends_show_if'] = $option_settings['depends_show_if'];
				}
			}

			if ( ! isset( $option_settings['hide_font_size'] ) || ! $option_settings['hide_font_size'] ) {
				$additional_options["{$option_name}_font_size"] = wp_parse_args( $option_settings['font_size'], array(
					'label'           => sprintf( esc_html__( '%1$s Font Size', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $option_name,
					'mobile_options'  => true,
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
				) );

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_font_size"]['depends_show_if'] = $option_settings['depends_show_if'];
				}

				$additional_options["{$option_name}_font_size_tablet"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
				$additional_options["{$option_name}_font_size_phone"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
				$additional_options["{$option_name}_font_size_last_edited"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
			}

			$additional_options["{$option_name}_text_color"] = array(
				'label'           => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'color-alpha',
				'option_category' => 'font_option',
				'custom_color'    => true,
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $option_name,
			);

			if ( ! isset( $option_settings['hide_text_color'] ) || ! $option_settings['hide_text_color'] ) {
				$additional_options["{$option_name}_text_color"] = array(
					'label'           => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'color-alpha',
					'option_category' => 'font_option',
					'custom_color'    => true,
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $option_name,
				);

				// add reference to the obsolete color option if needed
				if ( isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['old_option_ref'] ) ) {
					$additional_options["{$option_name}_text_color"]['attributes'] = array( 'data-old-option-ref' => "{$option_settings['text_color']['old_option_ref']}" );
				}

				// set default value if defined
				if ( isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['default'] ) ) {
					$additional_options["{$option_name}_text_color"]['default'] = $option_settings['text_color']['default'];
				}

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_text_color"]['depends_show_if'] = $option_settings['depends_show_if'];
				}
			}

			if ( ! isset( $option_settings['hide_letter_spacing'] ) || ! $option_settings['hide_letter_spacing'] ) {
				$additional_options["{$option_name}_letter_spacing"] = wp_parse_args( $option_settings['letter_spacing'], array(
					'label'           => sprintf( esc_html__( '%1$s Letter Spacing', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'mobile_options'  => true,
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $option_name,
					'default'         => '0px',
					'range_settings'  => array(
						'min'  => '0',
						'max'  => '100',
						'step' => '1',
					),
				) );

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_letter_spacing"]['depends_show_if'] = $option_settings['depends_show_if'];
				}

				$additional_options["{$option_name}_letter_spacing_tablet"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
				$additional_options["{$option_name}_letter_spacing_phone"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
				$additional_options["{$option_name}_letter_spacing_last_edited"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
			}

			if ( ! isset( $option_settings['hide_line_height'] ) || ! $option_settings['hide_line_height'] ) {
				$default_option_line_height = array(
					'label'           => sprintf( esc_html__( '%1$s Line Height', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'mobile_options'  => true,
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $option_name,
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '0.1',
					),
				);

				if ( isset( $option_settings['line_height'] ) ) {
					$additional_options["{$option_name}_line_height"] = wp_parse_args(
						$option_settings['line_height'],
						$default_option_line_height
					);
				} else {
					$additional_options["{$option_name}_line_height"] = $default_option_line_height;
				}

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_line_height"]['depends_show_if'] = $option_settings['depends_show_if'];
				}

				$additional_options["{$option_name}_line_height_tablet"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
				$additional_options["{$option_name}_line_height_phone"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
				$additional_options["{$option_name}_line_height_last_edited"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $option_name,
				);
			}

			// The below option is obsolete. This code is for backward compatibility
			if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] ) {
				$additional_options["{$option_name}_all_caps"] = array(
					'type'              => 'hidden',
					'shortcode_default' => '',
					'tab_slug'          => $tab_slug,
					'toggle_slug'       => $option_name,
				);
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_background_fields() {
		if ( ! isset( $this->advanced_options['background'] ) ) {
			return;
		}

		$toggle_disabled = isset( $this->advanced_options['background']['settings']['disable_toggle'] ) && $this->advanced_options['background']['settings']['disable_toggle'];
		$tab_slug = isset( $this->advanced_options['background']['settings']['tab_slug'] ) ? $this->advanced_options['background']['settings']['tab_slug'] : 'general';
		$toggle_slug = '';

		if ( ! $toggle_disabled ) {
			$toggle_slug = isset( $this->advanced_options['background']['settings']['tab_slug'] ) ? $this->advanced_options['background']['settings']['tab_slug'] : 'background';

			$background_toggle = array(
				'background' => array(
					'title'    => esc_html__( 'Background', 'et_builder' ),
					'priority' => 80,
				),
			);

			$this->_add_option_toggles( $tab_slug, $background_toggle );
		}

		$defaults = array(
			'use_background_color'  => true,
			'use_background_color_gradient' => true,
			'use_background_image' => true,
			'use_background_video' => true,
		);
		$this->advanced_options['background'] = wp_parse_args( $this->advanced_options['background'], $defaults );

		$additional_options = array();

		if ( $this->advanced_options['background']['use_background_color'] ) {
			$additional_options['background_color'] = array(
				'label'           => esc_html__( 'Background Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'option_category' => 'configuration',
				'custom_color'    => true,
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
			);
		}

		if ( $this->advanced_options['background']['use_background_color_gradient'] ) {
			$additional_options['use_background_color_gradient'] = array(
				'label'             => esc_html__( 'Use Background Color Gradient', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'shortcode_default' => 'off',
				'default_on_child'  => true,
				'affects'           => array(
					'background_color_gradient_start',
					'background_color_gradient_end',
					'background_color_gradient_start_position',
					'background_color_gradient_end_position',
					'background_color_gradient_type',
				),
				'description'       => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_start'] = array(
				'label'             => esc_html__( 'Gradient Start', 'et_builder' ),
				'type'              => 'color-alpha',
				'option_category'   => 'configuration',
				'description'       => '',
				'depends_show_if'   => 'on',
				'default'           => '#2b87da',
				'shortcode_default' => '#2b87da',
				'default_on_child'  => true,
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_end'] = array(
				'label'             => esc_html__( 'Gradient End', 'et_builder' ),
				'type'              => 'color-alpha',
				'option_category'   => 'configuration',
				'description'       => '',
				'depends_show_if'   => 'on',
				'default'           => '#29c4a9',
				'shortcode_default' => '#29c4a9',
				'default_on_child'  => true,
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_type'] = array(
				'label'             => esc_html__( 'Gradient Type', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'linear' => esc_html__( 'Linear', 'et_builder' ),
					'radial' => esc_html__( 'Radial', 'et_builder' ),
				),
				'affects'           => array(
					'background_color_gradient_direction',
					'background_color_gradient_direction_radial'
				),
				'default'           => 'linear',
				'shortcode_default' => 'linear',
				'default_on_child'  => true,
				'description'       => '',
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_direction'] = array(
				'label'             => esc_html__( 'Gradient Direction', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'configuration',
				'range_settings'    => array(
					'min'  => 1,
					'max'  => 360,
					'step' => 1,
				),
				'default'           => '180deg',
				'shortcode_default' => '180deg',
				'default_on_child'  => true,
				'validate_unit'     => true,
				'fixed_unit'        => 'deg',
				'fixed_range'       => true,
				'depends_show_if'   => 'linear',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_direction_radial'] = array(
				'label'             => esc_html__( 'Radial Direction', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'center'       => esc_html__( 'Center', 'et_builder' ),
					'top left'     => esc_html__( 'Top Left', 'et_builder' ),
					'top'          => esc_html__( 'Top', 'et_builder' ),
					'top right'    => esc_html__( 'Top Right', 'et_builder' ),
					'right'        => esc_html__( 'Right', 'et_builder' ),
					'bottom right' => esc_html__( 'Bottom Right', 'et_builder' ),
					'bottom'       => esc_html__( 'Bottom', 'et_builder' ),
					'bottom left'  => esc_html__( 'Bottom Left', 'et_builder' ),
					'left'         => esc_html__( 'Left', 'et_builder' ),
				),
				'default'           => 'center',
				'shortcode_default' => 'center',
				'default_on_child'  => true,
				'description'       => '',
				'depends_show_if'   => 'radial',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_start_position'] = array(
				'label'             => esc_html__( 'Start Position', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'configuration',
				'range_settings'    => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'           => '0%',
				'shortcode_default' => '0%',
				'default_on_child'  => true,
				'validate_unit'     => true,
				'fixed_unit'        => '%',
				'fixed_range'       => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_color_gradient_end_position'] = array(
				'label'             => esc_html__( 'End Position', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'configuration',
				'range_settings'    => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'           => '100%',
				'shortcode_default' => '100%',
				'default_on_child'  => true,
				'validate_unit'     => true,
				'fixed_unit'        => '%',
				'fixed_range'       => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);
		}

		if ( $this->advanced_options['background']['use_background_image'] ) {
			$additional_options['background_image'] = array(
				'label'              => esc_html__( 'Background Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background', 'et_builder' ),
				'tab_slug'           => $tab_slug,
				'toggle_slug'        => $toggle_slug,
			);

			$additional_options['parallax'] = array(
				'label'             => esc_html__( 'Use Parallax Effect', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'default_on_child'  => true,
				'affects'           => array(
					'parallax_method',
					'background_size',
					'background_position',
					'background_repeat',
					'background_blend',
				),
				'description'       => esc_html__( 'If enabled, your background image will stay fixed as your scroll, creating a fun parallax-like effect.', 'et_builder' ),
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['parallax_method'] = array(
				'label'             => esc_html__( 'Parallax Method', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'True Parallax', 'et_builder' ),
					'off' => esc_html__( 'CSS', 'et_builder' ),
				),
				'default'           => isset( $this->fields_defaults['parallax_method'][0] ) ? $this->fields_defaults['parallax_method'][0] : 'on',
				'default_on_child'  => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'et_builder' ),
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options['background_size'] = array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'         => 'cover',
				'default_on_child'=> true,
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			);

			$additional_options['background_position'] = array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'default_on_child'  => true,
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			);

			$additional_options['background_repeat'] = array(
				'label'           => esc_html__( 'Background Image Repeat', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'no-repeat' => esc_html__( 'No Repeat', 'et_builder' ),
					'repeat'    => esc_html__( 'Repeat', 'et_builder' ),
					'repeat-x'  => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
					'repeat-y'  => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
					'space'     => esc_html__( 'Space', 'et_builder' ),
					'round'     => esc_html__( 'Round', 'et_builder' ),
				),
				'default'          => 'no-repeat',
				'default_on_child' => true,
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'background',
			);

			$additional_options['background_blend'] = array(
				'label'           => esc_html__( 'Background Image Blend', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'normal'      => esc_html__( 'Normal', 'et_builder' ),
					'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
					'screen'      => esc_html__( 'Screen', 'et_builder' ),
					'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
					'darken'      => esc_html__( 'Darken', 'et_builder' ),
					'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
					'difference'  => esc_html__( 'Difference', 'et_builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
					'hue'         => esc_html__( 'Hue', 'et_builder' ),
					'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
					'color'       => esc_html__( 'Color', 'et_builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
				),
				'default'          => 'normal',
				'default_on_child' => true,
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'background',
			);
		}

		if ( $this->advanced_options['background']['use_background_video'] ) {
			$additional_options['background_video_mp4'] = array(
				'label'              => esc_html__( 'Background Video MP4', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
				'description'        => et_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .MP4 version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'et_builder' ) ),
				'tab_slug'           => $tab_slug,
				'toggle_slug'        => $toggle_slug,
				'computed_affects'   => array(
					'__video_background',
				),
			);

			$additional_options['background_video_webm'] = array(
				'label'              => esc_html__( 'Background Video Webm', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
				'description'        => et_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .WEBM version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'et_builder' ) ),
				'tab_slug'           => $tab_slug,
				'toggle_slug'        => $toggle_slug,
				'computed_affects'   => array(
					'__video_background',
				),
			);

			$additional_options['background_video_width'] = array(
				'label'            => esc_html__( 'Background Video Width', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'In order for videos to be sized correctly, you must input the exact width (in pixels) of your video here.', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'computed_affects' => array(
					'__video_background',
				),
			);

			$additional_options['background_video_height'] = array(
				'label'            => esc_html__( 'Background Video Height', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'In order for videos to be sized correctly, you must input the exact height (in pixels) of your video here.', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'computed_affects' => array(
					'__video_background',
				),
			);

			$additional_options['allow_player_pause'] = array(
				'label'           => esc_html__( 'Pause Video', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'          => 'off',
				'default_on_child' => true,
				'description'      => esc_html__( 'Allow video to be paused by other players when they begin playing', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
			);

			$additional_options['__video_background'] = array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Element', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_border_fields () {
		if ( ! isset( $this->advanced_options['border'] ) ) {
			return;
		}

		$additional_options = array();
		$toggle_disabled = isset( $this->advanced_options['border']['settings']['disable_toggle'] ) && $this->advanced_options['border']['settings']['disable_toggle'];
		$color_type = isset( $this->advanced_options['border']['settings']['color'] ) && 'alpha' === $this->advanced_options['border']['settings']['color'] ? 'color-alpha' : 'color';
		$tab_slug = isset( $this->advanced_options['border']['settings']['tab_slug'] ) ? $this->advanced_options['border']['settings']['tab_slug'] : 'advanced';
		$toggle_slug = '';

		if ( ! $toggle_disabled ) {
			$toggle_slug = isset( $this->advanced_options['border']['settings']['toggle_slug'] ) ? $this->advanced_options['border']['settings']['toggle_slug'] : 'border';

			$border_toggle = array(
				'border' => array(
					'title'    => esc_html__( 'Border', 'et_builder' ),
					'priority' => 60,
				),
			);

			$this->_add_option_toggles( $tab_slug, $border_toggle );
		}

		$additional_options['use_border_color'] = array(
			'label'             => esc_html__( 'Use Border', 'et_builder' ),
			'type'              => 'yes_no_button',
			'option_category'   => 'layout',
			'options'           => array(
				'off' => esc_html__( 'No', 'et_builder' ),
				'on'  => esc_html__( 'Yes', 'et_builder' ),
			),
			'default'           => 'off',
			'affects' => array(
				'border_color',
				'border_width',
				'border_style',
			),
			'shortcode_default' => 'off',
			'tab_slug'          => $tab_slug,
			'toggle_slug'       => $toggle_slug,
		);

		$additional_options['border_color'] = array(
			'label'             => esc_html__( 'Border Color', 'et_builder' ),
			'type'              => $color_type,
			'option_category'   => 'layout',
			'default'           => '#ffffff',
			'shortcode_default' => '#ffffff',
			'tab_slug'          => $tab_slug,
			'toggle_slug'       => $toggle_slug,
			'depends_default'   => true,
		);

		$additional_options['border_width'] = array(
			'label'             => esc_html__( 'Border Width', 'et_builder' ),
			'type'              => 'range',
			'option_category'   => 'layout',
			'default'           => '1px',
			'shortcode_default' => '1px',
			'tab_slug'          => $tab_slug,
			'toggle_slug'       => $toggle_slug,
			'depends_default'   => true,
		);

		$additional_options['border_style'] = array(
			'label'             => esc_html__( 'Border Style', 'et_builder' ),
			'type'              => 'select',
			'option_category'   => 'layout',
			'options'           => et_builder_get_border_styles(),
			'shortcode_default' => 'solid',
			'tab_slug'          => $tab_slug,
			'toggle_slug'       => $toggle_slug,
			'depends_default'   => true,
		);

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_custom_margin_padding_fields() {
		if ( ! isset( $this->advanced_options['custom_margin_padding'] ) ) {
			return;
		}

		$additional_options = array();

		$defaults = array(
			'use_margin'  => true,
			'use_padding' => true,
		);
		$this->advanced_options['custom_margin_padding'] = wp_parse_args( $this->advanced_options['custom_margin_padding'], $defaults );

		$tab_slug = isset( $this->advanced_options['custom_margin_padding']['tab_slug'] ) ? $this->advanced_options['custom_margin_padding']['tab_slug'] : 'advanced';
		$toggle_disabled = isset( $this->advanced_options['custom_margin_padding']['disable_toggle'] ) && $this->advanced_options['custom_margin_padding']['disable_toggle'];
		$toggle_slug = '';

		if ( ! $toggle_disabled ) {
			$toggle_slug = isset( $this->advanced_options['custom_margin_padding']['toggle_slug'] ) ? $this->advanced_options['custom_margin_padding']['toggle_slug'] : 'margin';

			$margin_toggle = array(
				'margin' => array(
					'title'    => esc_html__( 'Spacing', 'et_builder' ),
					'priority' => 70,
				),
			);

			$this->_add_option_toggles( $tab_slug, $margin_toggle );
		}

		if ( $this->advanced_options['custom_margin_padding']['use_margin'] ) {
			$additional_options['custom_margin'] = array(
				'label'           => esc_html__( 'Custom Margin', 'et_builder' ),
				'type'            => 'custom_margin',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
			);
			$additional_options['custom_margin_tablet'] = array(
				'type'     => 'skip',
				'tab_slug' => $tab_slug,
			);
			$additional_options['custom_margin_phone'] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			// make it possible to override/add options
			if ( ! empty( $this->advanced_options['custom_margin_padding']['custom_margin'] ) ) {
				$additional_options['custom_margin'] = array_merge( $additional_options['custom_margin'], $this->advanced_options['custom_margin_padding']['custom_margin'] );
			}

			$additional_options["custom_margin_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_1_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_2_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_3_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_4_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		if ( $this->advanced_options['custom_margin_padding']['use_padding'] ) {
			$additional_options['custom_padding'] = array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
			);
			$additional_options['custom_padding_tablet'] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options['custom_padding_phone'] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			// make it possible to override/add options
			if ( ! empty( $this->advanced_options['custom_margin_padding']['custom_padding'] ) ) {
				$additional_options['custom_padding'] = array_merge( $additional_options['custom_padding'], $this->advanced_options['custom_margin_padding']['custom_padding'] );
			}

			$additional_options["custom_padding_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_button_fields() {
		if ( ! isset( $this->advanced_options['button'] ) ) {
			return;
		}

		$additional_options = array();

		foreach ( $this->advanced_options['button'] as $option_name => $option_settings ) {
			$tab_slug = isset( $option_settings['tab_slug'] ) ? $option_settings['tab_slug'] : 'advanced';
			$toggle_disabled = isset( $option_settings['disable_toggle'] ) && $option_settings['disable_toggle'];
			$toggle_slug = '';

			if ( ! $toggle_disabled ) {
				$toggle_slug = isset( $option_settings['toggle_slug'] ) ? $option_settings['toggle_slug'] : $option_name;

				$button_toggle = array(
					$option_name => array(
						'title'    => esc_html( $option_settings['label'] ),
						'priority' => 80,
					),
				);

				$this->_add_option_toggles( $tab_slug, $button_toggle );
			}

			$additional_options["custom_{$option_name}"] = array(
				'label'             => sprintf( esc_html__( 'Use Custom Styles for %1$s ', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'yes_no_button',
				'option_category'   => 'button',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					"{$option_name}_text_color",
					"{$option_name}_text_size",
					"{$option_name}_border_width",
					"{$option_name}_border_radius",
					"{$option_name}_letter_spacing",
					"{$option_name}_spacing",
					"{$option_name}_bg_color",
					"{$option_name}_border_color",
					"{$option_name}_use_icon",
					"{$option_name}_font",
					"{$option_name}_text_color_hover",
					"{$option_name}_bg_color_hover",
					"{$option_name}_border_color_hover",
					"{$option_name}_border_radius_hover",
					"{$option_name}_letter_spacing_hover",
				),
				'shortcode_default' => 'off',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options["{$option_name}_text_size"] = array(
				'label'           => sprintf( esc_html__( '%1$s Text Size', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'range',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
				'option_category' => 'button',
				'default'         => ET_Global_Settings::get_value( 'all_buttons_font_size' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'mobile_options'  => true,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_text_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_bg_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Background Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => ET_Global_Settings::get_value( 'all_buttons_bg_color' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_width"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Width', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_width' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_radius"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Radius', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_radius' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_letter_spacing"] = array(
				'label'             => sprintf( esc_html__( '%1$s Letter Spacing', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_spacing' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'mobile_options'    => true,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_font"] = array(
				'label'           => sprintf( esc_html__( '%1$s Font', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'font',
				'option_category' => 'button',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_use_icon"] = array(
				'label'           => sprintf( esc_html__( 'Add %1$s Icon', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'select',
				'option_category' => 'button',
				'default'         => 'default',
				'options'         => array(
					'default' => esc_html__( 'Default', 'et_builder' ),
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					"{$option_name}_icon_color",
					"{$option_name}_icon_placement",
					"{$option_name}_on_hover",
					"{$option_name}_icon",
				),
				'shortcode_default' => 'default',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_icon"] = array(
				'label'               => sprintf( esc_html__( '%1$s Icon', 'et_builder' ), $option_settings['label'] ),
				'type'                => 'text',
				'option_category'     => 'button',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'default'             => '',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_icon_color"] = array(
				'label'               => sprintf( esc_html__( '%1$s Icon Color', 'et_builder' ), $option_settings['label'] ),
				'type'                => 'color-alpha',
				'option_category'     => 'button',
				'custom_color'        => true,
				'default'             => '',
				'shortcode_default'   => '',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_icon_placement"] = array(
				'label'           => sprintf( esc_html__( '%1$s Icon Placement', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'select',
				'option_category' => 'button',
				'options'         => array(
					'right'   => esc_html__( 'Right', 'et_builder' ),
					'left'    => esc_html__( 'Left', 'et_builder' ),
				),
				'shortcode_default'   => 'right',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_on_hover"] = array(
				'label'           => sprintf( esc_html__( 'Only Show Icon On Hover for %1$s', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'yes_no_button',
				'option_category' => 'button',
				'default'         => 'on',
				'options'         => array(
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'shortcode_default'   => 'on',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_text_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_bg_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Background Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Border Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_radius_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Border Radius', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_radius_hover' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_letter_spacing_hover"] = array(
				'label'           => sprintf( esc_html__( '%1$s Hover Letter Spacing', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'range',
				'option_category' => 'button',
				'default'         => ET_Global_Settings::get_value( 'all_buttons_spacing_hover' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'mobile_options'  => true,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_text_size_tablet"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_text_size_phone"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_tablet"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_phone"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_hover_tablet"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_hover_phone"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["{$option_name}_text_size_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_hover_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_custom_css_fields() {
		if ( isset( $this->custom_css_tab ) && ! $this->custom_css_tab ) {
			return;
		}

		$custom_css_fields = array();
		$custom_css_options = array();
		$current_module_unique_class = '.' . $this->slug . '_' . "<%= typeof( module_order ) !== 'undefined' ?  module_order : '<span class=\"et_pb_module_order_placeholder\"></span>' %>";
		$main_css_element_output = isset( $this->main_css_element ) ? $this->main_css_element : '%%order_class%%';
		$main_css_element_output = str_replace( '%%order_class%%', $current_module_unique_class, $main_css_element_output );

		$custom_css_default_options = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => ':before',
				'no_space_before_selector' => true,
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => ':after',
				'no_space_before_selector' => true,
			),
		);
		$custom_css_options = apply_filters( 'et_default_custom_css_options', $custom_css_default_options );

		if ( ! empty( $this->custom_css_options ) ) {
			$custom_css_options = array_merge( $custom_css_options, $this->custom_css_options );
		}

		$this->custom_css_options = apply_filters( 'et_custom_css_options_' . $this->slug, $custom_css_options );

		// optional settings names in custom css options
		$additional_option_slugs = array( 'description', 'priority' );

		foreach ( $custom_css_options as $slug => $option ) {
			$selector_value = isset( $option['selector'] ) ? $option['selector'] : '';
			$selector_contains_module_class = false !== strpos( $selector_value, '%%order_class%%' ) ? true : false;
			$selector_output = '' !== $selector_value ? str_replace( '%%order_class%%', $current_module_unique_class, $option['selector'] ) : '';
			$custom_css_fields[ "custom_css_{$slug}" ] = array(
				'label'    => sprintf(
					'%1$s:<span>%2$s%3$s%4$s</span>',
					$option['label'],
					! $selector_contains_module_class ? $main_css_element_output : '',
					! isset( $option['no_space_before_selector'] ) && isset( $option['selector'] ) ? ' ' : '',
					$selector_output
				),
				'type'        => 'custom_css',
				'tab_slug'    => 'custom_css',
				'toggle_slug' => 'custom_css',
				'no_colon' => true,
			);

			// update toggle slug for $this->custom_css_options
			$this->custom_css_options[ $slug ]['toggle_slug'] = 'custom_css';

			// add optional settings if needed
			foreach ( $additional_option_slugs as $option_slug ) {
				if ( isset( $option[ $option_slug ] ) ) {
					$custom_css_fields[ "custom_css_{$slug}" ][ $option_slug ] = $option[ $option_slug ];
				}
			}
		}

		if ( ! empty( $custom_css_fields ) ) {
			$this->fields_unprocessed = array_merge( $this->fields_unprocessed, $custom_css_fields );
		}

		$default_custom_css_toggles = array(
			'classes'    => esc_html__( 'CSS ID &amp; Classes', 'et_builder' ),
			'custom_css' => esc_html__( 'Custom CSS', 'et_builder' ),
		);

		$this->_add_option_toggles( 'custom_css', $default_custom_css_toggles );
	}

	private function _add_option_toggles( $tab_slug, $toggles_array ) {
		if ( ! isset( $this->options_toggles[ $tab_slug ] ) ) {
			$this->options_toggles[ $tab_slug ] = array();
		}

		if ( ! isset( $this->options_toggles[ $tab_slug ]['toggles'] ) ) {
			$this->options_toggles[ $tab_slug ]['toggles'] = array();
		}

		// get the only toggles which do not exist.
		$processed_toggles = array_diff_key( $toggles_array, $this->options_toggles[ $tab_slug ]['toggles'] );

		$this->options_toggles[ $tab_slug ]['toggles'] = array_merge( $this->options_toggles[ $tab_slug ]['toggles'], $processed_toggles );
	}

	private function _get_fields() {
		$this->fields = array();

		$this->fields = $this->fields_unprocessed;

		$this->fields = $this->process_fields( $this->fields );

		$this->fields = apply_filters( 'et_builder_module_fields_' . $this->slug, $this->fields );

		foreach ( $this->fields as $field_name => $field ) {
			$this->fields[ $field_name ] = apply_filters('et_builder_module_fields_' . $this->slug . '_field_' . $field_name, $field );
			$this->fields[ $field_name ]['name'] = $field_name;
		}

		return $this->fields;
	}

	// intended to be overridden as needed
	function process_fields( $fields ) {
		return apply_filters( 'et_pb_module_processed_fields', $fields, $this->slug );
	}

	/**
	 * Get the settings fields data for this element.
	 *
	 * @since 1.0
	 * @todo  Finish documenting return value's structure.
	 *
	 * @return array[] {
	 *     Settings Fields
	 *
	 *     @type mixed[] $setting_field_key {
	 *         Setting Field Data
	 *
	 *         @type string   $type                Setting field type.
	 *         @type string   $id                  CSS id for the setting.
	 *         @type string   $label               Text label for the setting. Translatable.
	 *         @type string   $description         Description for the settings. Translatable.
	 *         @type string   $class               Optional. Css class for the settings.
	 *         @type string[] $affects             Optional. The keys of all settings that depend on this setting.
	 *         @type string[] $depends_to          Optional. The keys of all settings that this setting depends on.
	 *         @type string   $depends_show_if     Optional. Only show this setting when the settings
	 *                                             on which it depends has a value equal to this.
	 *         @type string   $depends_show_if_not Optional. Only show this setting when the settings
	 *                                             on which it depends has a value that is not equal to this.
	 *         ...
	 *     }
	 *     ...
	 * }
	 */
	function get_fields() { return array(); }

	function hex2rgb( $color ) {
		if ( substr( $color, 0, 1 ) == '#' ) {
			$color = substr( $color, 1 );
		}

		if ( strlen( $color ) == 6 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return false;
		}

		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );

		return implode( ', ', array( $r, $g, $b ) );
	}

	function rgba_string_from_field_color_set( $color_set ) {
		if ( empty( $color_set ) || false === strpos($color_set, '|') ) {
			return false;
		}

		$color_set = explode('|', $color_set );

		$color_set_hex = $color_set[0];
		$color_set_rgb = $color_set[1];
		$color_set_alpha = $color_set[2];

		$color_set_rgba = 'rgba(' . $color_set_rgb . ', ' . $color_set_alpha . ')';
		return $color_set_rgba;
	}

	function get_post_type() {
		global $post, $et_builder_post_type;

		if ( is_admin() ) {
			return $post->post_type;
		} else {
			return $et_builder_post_type;
		}
	}

	function module_classes( $classes = array() ) {
		if ( ! empty( $classes ) ) {
			if ( ! is_array( $classes ) ) {
				if ( strpos( $classes, ' ' ) !== false ) {
					$classes = explode( ' ', $classes );
				} else {
					$classes = array( $classes );
				}
			}
		}

		$classes = apply_filters( 'et_builder_module_classes', $classes, $this->slug );
		$classes = apply_filters( 'et_builder_module_classes_' . $this->slug, $classes );

		$classes = array_map( 'trim', $classes );

		$_classes = array();
		foreach( $classes as $class ) {
			if ( ! empty( $class ) ) {
				$_classes[] = $class;
			}
		}

		return $_classes;
	}

	function wrap_settings_option( $option_output, $field ) {
		$depends = false;
		if ( isset( $field['depends_show_if'] ) || isset( $field['depends_show_if_not'] ) ) {
			$depends = true;
			if ( isset( $field['depends_show_if_not'] ) ) {
				$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $field['depends_show_if_not'] ) );
			} else {
				$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $field['depends_show_if'] ) );
			}
		}

		// Overriding background color's attribute, turning it into appropriate background attributes
		if ( isset( $field['type'] ) && isset( $field['name' ] ) && in_array( $field['name'], array( 'background_color', 'module_bg_color' ) ) ) {
			$field['type'] = 'background';

			// Removing depends default variable which hides background color for unified background field UI
			if ( isset( $field['depends_default'] ) ) {
				unset( $field['depends_default'] );
			}
		}

		$output = sprintf(
			'%6$s<div class="et-pb-option et-pb-option--%10$s%1$s%2$s%3$s%8$s%9$s%12$s"%4$s tabindex="-1" data-option_name="%11$s">%5$s</div> <!-- .et-pb-option -->%7$s',
			( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' et-pb-option-main-content' : '' ),
			( ( $depends || isset( $field['depends_default'] ) ) ? ' et-pb-depends' : '' ),
			( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' et_pb_hidden' : '' ),
			( $depends ? $depends_attr : '' ),
			"\n\t\t\t\t" . $option_output . "\n\t\t\t",
			"\t",
			"\n\n\t\t",
			( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' et-pb-option-%1$s', $field['name'] ) ) : '' ),
			( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' ),
			isset( $field['type'] ) ? esc_attr( $field['type'] ) : '',
			esc_attr( $field['name'] ),
			isset( $field['specialty_only'] ) && 'yes' === $field['specialty_only'] ? ' et-pb-specialty-only-option' : ''
		);

		return $output;
	}

	function wrap_settings_option_field( $field ) {
		$use_container_wrapper = isset( $field['use_container_wrapper'] ) && ! $field['use_container_wrapper'] ? false : true;

		if ( ! empty( $field['renderer'] ) ) {
			$renderer_options = isset( $field['renderer_options'] ) ? $field['renderer_options'] : $field;

			$field_el = is_callable( $field['renderer'] ) ? call_user_func( $field['renderer'], $renderer_options ) : $field['renderer'];

			if ( ! empty( $field['renderer_with_field'] ) && $field['renderer_with_field'] ) {
				$field_el .= $this->render_field( $field );
			}
		} else {
			$field_el = $this->render_field( $field );
		}

		$description = ! empty( $field['description'] ) ? sprintf( '%2$s<p class="description">%1$s</p>', $field['description'], "\n\t\t\t\t\t" ) : '';

		if ( '' === $description && ! $use_container_wrapper ) {
			$output = $field_el;
		} else {
			$output = sprintf(
				'%3$s<div class="et-pb-option-container et-pb-option-container--%6$s%5$s">
					%1$s
					%2$s
				%4$s</div> <!-- .et-pb-option-container -->',
				$field_el,
				$description,
				"\n\n\t\t\t\t",
				"\t",
				( isset( $field['type'] ) && 'custom_css' === $field['type'] ? ' et-pb-custom-css-option' : '' ),
				isset( $field['type'] ) ? esc_attr( $field['type'] ) : ''
			);
		}

		return $output;
	}

	function wrap_settings_option_label( $field ) {
		if ( ! empty( $field['label'] ) ) {
			$label = $field['label'];
		} else {
			return '';
		}

		$field_name = $this->get_field_name( $field );
		if ( isset( $field['type'] ) && 'font' === $field['type'] ) {
			$field_name .= '_select';
		}

		$required = ! empty( $field['required'] ) ? '<span class="required">*</span>' : '';
		$attributes = ! ( isset( $field['type'] ) && in_array( $field['type'], array( 'custom_margin', 'custom_padding' )  ) )
			? sprintf( ' for="%1$s"', esc_attr( $field_name ) )
			: ' class="et_custom_margin_label"';

		$label = sprintf(
			'<label%1$s>%2$s%4$s %3$s</label>',
			$attributes,
			$label,
			$required,
			isset( $field['no_colon'] ) && true === $field['no_colon'] ? '' : ':'
		);

		return $label;
	}

	/**
	 * Get svg icon as string
	 * @param string icon name
	 *
	 * @return string div-wrapped svg icon
	 */
	function get_icon( $icon_name ) {
		switch ( $icon_name ) {
			case 'add':
				$icon = '
					<g>
						<path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" />
					</g>
				';
				break;

			case 'delete':
				$icon = '
					<g>
						<path d="M19 9h-3V8a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v1H9a1 1 0 1 0 0 2h10a1 1 0 0 0 .004-2H19zM9 20c.021.543.457.979 1 1h8c.55-.004.996-.45 1-1v-7H9v7zm2.02-4.985h2v4h-2v-4zm4 0h2v4h-2v-4z" fillRule="evenodd" />
					</g>
				';
				break;

			case 'setting':
				$icon = '
					<g>
						<path d="M20.426 13.088l-1.383-.362a.874.874 0 0 1-.589-.514l-.043-.107a.871.871 0 0 1 .053-.779l.721-1.234a.766.766 0 0 0-.116-.917 6.682 6.682 0 0 0-.252-.253.768.768 0 0 0-.917-.116l-1.234.722a.877.877 0 0 1-.779.053l-.107-.044a.87.87 0 0 1-.513-.587l-.362-1.383a.767.767 0 0 0-.73-.567h-.358a.768.768 0 0 0-.73.567l-.362 1.383a.878.878 0 0 1-.513.589l-.107.044a.875.875 0 0 1-.778-.054l-1.234-.722a.769.769 0 0 0-.918.117c-.086.082-.17.166-.253.253a.766.766 0 0 0-.115.916l.721 1.234a.87.87 0 0 1 .053.779l-.043.106a.874.874 0 0 1-.589.514l-1.382.362a.766.766 0 0 0-.567.731v.357a.766.766 0 0 0 .567.731l1.383.362c.266.07.483.26.588.513l.043.107a.87.87 0 0 1-.053.779l-.721 1.233a.767.767 0 0 0 .115.917c.083.087.167.171.253.253a.77.77 0 0 0 .918.116l1.234-.721a.87.87 0 0 1 .779-.054l.107.044a.878.878 0 0 1 .513.589l.362 1.383a.77.77 0 0 0 .731.567h.356a.766.766 0 0 0 .73-.567l.362-1.383a.878.878 0 0 1 .515-.589l.107-.044a.875.875 0 0 1 .778.054l1.234.721c.297.17.672.123.917-.117.087-.082.171-.166.253-.253a.766.766 0 0 0 .116-.917l-.721-1.234a.874.874 0 0 1-.054-.779l.044-.107a.88.88 0 0 1 .589-.513l1.383-.362a.77.77 0 0 0 .567-.731v-.357a.772.772 0 0 0-.569-.724v-.005zm-6.43 3.9a2.986 2.986 0 1 1 2.985-2.986 3 3 0 0 1-2.985 2.987v-.001z" fillRule="evenodd" />
					</g>
				';
				break;

			case 'background-color':
				$icon = '
					<g>
						<path d="M19.4 14.6c0 0-1.5 3.1-1.5 4.4 0 0.9 0.7 1.6 1.5 1.6 0.8 0 1.5-0.7 1.5-1.6C20.9 17.6 19.4 14.6 19.4 14.6zM19.3 12.8l-4.8-4.8c-0.2-0.2-0.4-0.3-0.6-0.3 -0.3 0-0.5 0.1-0.7 0.3l-1.6 1.6L9.8 7.8c-0.4-0.4-1-0.4-1.4 0C8 8.1 8 8.8 8.4 9.1l1.8 1.8 -2.8 2.8c-0.4 0.4-0.4 1-0.1 1.4l4.6 4.6c0.2 0.2 0.4 0.3 0.6 0.3 0.3 0 0.5-0.1 0.7-0.3l6.1-6.1C19.5 13.4 19.5 13.1 19.3 12.8zM15.6 14.6c-1.7 1.7-4.5 1.7-6.2 0l2.1-2.1 1 1c0.4 0.4 1 0.4 1.4 0 0.4-0.4 0.4-1 0-1.4l-1-1 0.9-0.9 3.1 3.1L15.6 14.6z" fillRule="evenodd"/>
					</g>
				';
				break;

			case 'background-image':
				$icon = '
					<g>
						<path d="M22.9 7.5c-0.1-0.3-0.5-0.6-0.8-0.6H5.9c-0.4 0-0.7 0.2-0.8 0.6C5.1 7.6 5 7.7 5 7.9v12.2c0 0.1 0 0.2 0.1 0.4 0.1 0.3 0.5 0.5 0.8 0.6h16.2c0.4 0 0.7-0.2 0.8-0.6 0-0.1 0.1-0.2 0.1-0.4V7.9C23 7.7 23 7.6 22.9 7.5zM21 18.9H7v-10h14V18.9z" fillRule="evenodd"/>
						<circle cx="10.5" cy="12.4" r="1.5"/>
						<polygon points="15 16.9 13 13.9 11 16.9 "/>
						<polygon points="17 10.9 15 16.9 19 16.9 "/>
					</g>
				';
				break;

			case 'background-gradient':
				$icon = '
					<g>
						<path d="M22.9 7.5c-0.1-0.3-0.5-0.6-0.8-0.6H5.9c-0.4 0-0.7 0.2-0.8 0.6C5.1 7.6 5 7.7 5 7.9v12.2c0 0.1 0 0.2 0.1 0.4 0.1 0.3 0.5 0.5 0.8 0.6h16.2c0.4 0 0.7-0.2 0.8-0.6 0-0.1 0.1-0.2 0.1-0.4V7.9C23 7.7 23 7.6 22.9 7.5zM21 18.9L7 8.9h14V18.9z" fillRule="evenodd"/>
					</g>
				';
				break;

			case 'background-video':
				$icon = '
					<g>
						<path d="M22.9 7.5c-0.1-0.3-0.5-0.6-0.8-0.6H5.9c-0.4 0-0.7 0.2-0.8 0.6C5.1 7.6 5 7.7 5 7.9v12.2c0 0.1 0 0.2 0.1 0.4 0.1 0.3 0.5 0.5 0.8 0.6h16.2c0.4 0 0.7-0.2 0.8-0.6 0-0.1 0.1-0.2 0.1-0.4V7.9C23 7.7 23 7.6 22.9 7.5zM21 18.9H7v-10h14V18.9z" fillRule="evenodd"/>
						<polygon points="13 10.9 13 16.9 17 13.9 "/>
					</g>
				';
				break;

			case 'swap':
				$icon = '
					<g>
						<path d="M19 12h-3V9c0-0.5-0.5-1-1-1H8C7.5 8 7 8.5 7 9v7c0 0.5 0.5 1 1 1h3v3c0 0.5 0.5 1 1 1h7c0.5 0 1-0.5 1-1v-7C20 12.5 19.5 12 19 12zM18 19h-5v-2h2c0.5 0 1-0.5 1-1v-2h2V19z" fillRule="evenodd"/>
					</g>
				';
				break;

			default:
				$icon = '';
				break;
		}

		if ( '' === $icon ) {
			return '';
		}

		return '<div class="et-pb-icon">
			<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision">' . $icon . '</svg>
		</div>';
	}

	/**
	 * Get structure of background UI tabs
	 *
	 * @return array
	 */
	function get_background_fields_structure() {
		return array(
			'color' => array(
				'background_color',
				'module_bg_color', // Post Title
				'use_background_color',
				'transparent_background',
				'transparent_background_fb',
			),
			'gradient' => array(
				'background_color_gradient_start',
				'background_color_gradient_end',
				'use_background_color_gradient',
				'background_color_gradient_type',
				'background_color_gradient_direction',
				'background_color_gradient_direction_radial',
				'background_color_gradient_start_position',
				'background_color_gradient_end_position',
			),
			'image' => array(
				'background_image',
				'background_url',   // Fullwidth Header
				'bg_img',           // Column
				'parallax',
				'parallax_effect',  // Post Title
				'parallax_method',
				'background_size',
				'background_position',
				'background_repeat',
				'background_blend',
			),
			'video' => array(
				'background_video_mp4',
				'background_video_webm',
				'background_video_width',
				'background_video_height',
				'video_bg_mp4',    // Slider Item
				'video_bg_webm',   // Slider Item
				'video_bg_width',  // Slider Item
				'video_bg_height', // Slider Item
				'allow_player_pause',
			),
		);
	}

	/**
	 * Get list of background fields names in one dimensional array
	 *
	 * @return array
	 */
	function get_background_fields_names() {
		$background_structure = $this->get_background_fields_structure();
		$fields_names = array();

		foreach ( $background_structure as $tab_name ) {
			foreach ( $tab_name as $field_name ) {
				$fields_names[] = $field_name;
			}
		}

		return $fields_names;
	}

	/**
	 * Get / extract background fields from all modules fields
	 * @param array all modules fields
	 *
	 * @return array background fields multidimensional array grouped based on its tab
	 */
	function get_background_fields( $all_fields ) {
		$background_fields_structure = $this->get_background_fields_structure();
		$background_tab_names        = array_keys( $background_fields_structure );
		$background_fields           = array_fill_keys( $background_tab_names, array() );

		foreach ( $all_fields as $field_name => $field ) {
			// Multiple foreaches seem overkill. Use single foreach with little bit if conditions
			// redundancy to get background fields grouped into multi-dimensional tab-based array
			if ( in_array( $field_name, $background_fields_structure['color'] ) ) {
				$background_fields['color'][$field_name] = $field;
			}

			if ( in_array( $field_name, $background_fields_structure['gradient'] ) ) {
				$background_fields['gradient'][$field_name] = $field;
			}

			if ( in_array( $field_name, $background_fields_structure['image'] ) ) {
				$background_fields['image'][$field_name] = $field;
			}

			if ( in_array( $field_name, $background_fields_structure['video'] ) ) {
				$background_fields['video'][$field_name] = $field;
			}
		}

		return $background_fields;
	}

	/**
	 * Get string of background fields UI. Used in place of background_color fields UI
	 * @param array list of all module fields
	 *
	 * @return string background fields UI
	 */
	function wrap_settings_background_fields( $all_fields ) {
		$tab_structure     = $this->get_background_fields_structure();
		$tab_names         = array_keys( $tab_structure );
		$background_fields = $this->get_background_fields( $all_fields );

		// Concatenate background fields UI
		$background = '';

		// Label
		$background .= sprintf(
			'<label for="et_pb_background">%1$s</label>',
			esc_html__( 'Background:', 'et_builder' )
		);

		// Field wrapper
		$background .= '<div class="et-pb-option-container et-pb-option-container--background">';

		// Tab Nav
		$background .= '<ul class="et_pb_background-tab-navs">';

		foreach ( $tab_names as $tab_nav_name ) {
			$background .= sprintf(
				'<li><a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--%1$s" data-tab="%1$s" title="%1$s">%2$s</a></li>',
				esc_attr( $tab_nav_name ),
				$this->get_icon( 'background-' . $tab_nav_name )
			);
		}

		$background .= '</ul>';

		// Tabs
		foreach ( $tab_names as $tab_name ) {
			$background .= sprintf(
				'<div class="et_pb_background-tab et_pb_background-tab--%1$s" data-tab="%1$s">',
				esc_attr( $tab_name )
			);

			// Get tab's fields
			$tab_fields = $background_fields[ $tab_name ];

			// Render gradient tab's preview
			if ( 'gradient' === $tab_name ) {
				$background .= sprintf('<div class="et-pb-option-preview et-pb-option-preview--empty">
						<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
							%1$s
						</button>
						<button class="et-pb-option-preview-button et-pb-option-preview-button--swap">
							%2$s
						</button>
						<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
							%3$s
						</button>
					</div>',
					$this->get_icon( "add" ),
					$this->get_icon( "swap" ),
					$this->get_icon( "delete" )
				);
			}

			// Tab's fields
			foreach ( $tab_fields as $tab_field_name => $tab_field ) {

				if ( 'skip' === $tab_field['type'] ) {
					continue;
				}

				$preview_class = '';

				// Append preview class name
				if ( in_array( $tab_field['name'], array( 'background_color', 'module_bg_color', 'background_image', 'background_url', 'background_video_mp4', 'background_video_webm', 'video_bg_mp4', 'video_bg_webm' ) ) ) {
					$tab_field['has_preview'] = true;
					$preview_class = ' et-pb-option--has-preview';
				}

				// Prepare field list attribute
				$depends      = false;
				$depends_attr = '';
				if ( isset( $tab_field['depends_show_if'] ) || isset( $tab_field['depends_show_if_not'] ) ) {
					$depends = true;
					if ( isset( $tab_field['depends_show_if_not'] ) ) {
						$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $tab_field['depends_show_if_not'] ) );
					} else {
						$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $tab_field['depends_show_if'] ) );
					}
				}

				// Append fields UI
				$background .= sprintf(
					'<div class="et_pb_background-option et_pb_background-option--%1$s et-pb-option et-pb-option--%1$s%2$s"%3$s data-option_name="%4$s">',
					esc_attr( $tab_field_name ),
					esc_attr( $preview_class ),
					$depends_attr,
					esc_attr( $tab_field['name'] )
				);
				$background .= $this->wrap_settings_option_label( $tab_field );
				$background .= $this->wrap_settings_option_field( $tab_field );
				$background .= '</div>';
			}

			$background .= '</div>';
		}

		// End of field wrapper
		$background .= '</div> <!-- .et-pb-option-container -->';

		return $background;
	}

	function get_field_name( $field ) {
		// Don't add 'et_pb_' prefix to the "Admin Label" field
		if ( 'admin_label' === $field['name'] ) {
			return $field['name'];
		}

		return sprintf( 'et_pb_%s', $field['name'] );
	}

	function process_html_attributes( $field, &$attributes ) {
		if ( is_array( $field['attributes'] )  ) {
			foreach( $field['attributes'] as $attribute_key => $attribute_value ) {
				$attributes .= ' ' . esc_attr( $attribute_key ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		} else {
			$attributes = ' '.$field['attributes'];
		}
	}

	function render_field( $field ) {
		$classes = array();
		$hidden_field = '';
		$field_el = '';
		$is_custom_color = isset( $field['custom_color'] ) && $field['custom_color'];
		$reset_button_html = '<span class="et-pb-reset-setting"></span>';
		$need_mobile_options = isset( $field['mobile_options'] ) && $field['mobile_options'] ? true : false;
		$only_options = isset( $field['only_options'] ) ? $field['only_options'] : false;

		if ( $need_mobile_options ) {
			$mobile_settings_tabs = et_pb_generate_mobile_options_tabs();
		}

		if ( 0 !== strpos( $field['type'], 'select' ) ) {
			$classes = array( 'regular-text' );
		}

		foreach( $this->get_validation_class_rules() as $rule ) {
			if ( ! empty( $field[ $rule ] ) ) {
				$this->validation_in_use = true;
				$classes[] = $rule;
			}
		}

		if ( isset( $field['validate_unit'] ) && $field['validate_unit'] ) {
			$classes[] = 'et-pb-validate-unit';
		}

		if ( ! empty( $field['class'] ) ) {
			if ( is_string( $field['class'] ) ) {
				$field['class'] = array( $field['class'] );
			}

			$classes = array_merge( $classes, $field['class'] );
		}
		$field['class'] = implode(' ', $classes );

		$field_name = $this->get_field_name( $field );

		$field['id'] = ! empty( $field['id'] ) ? $field['id'] : $field_name;

		$field['name'] = $field_name;

		if ( isset( $this->type ) && 'child' === $this->type ) {
			$field_name = "data.{$field_name}";
		}

		$default = isset( $field['default'] ) ? $field['default'] : '';

		if ( 'font' === $field['type'] ) {
			$default = '' === $default ? '||||' : $default;
		}

		$field_value = esc_attr( $field_name ) . '.replace(/%91/g, "[").replace(/%93/g, "]").replace(/%22/g, "\"")';

		$value_html = ' value="<%%- typeof( %1$s ) !== \'undefined\' ?  %2$s : \'%3$s\' %%>" ';
		$value = sprintf(
			$value_html,
			esc_attr( $field_name ),
			$field_value,
			$default
		);

		$attributes = '';
		if ( ! empty( $field['attributes'] ) ) {
			$this->process_html_attributes( $field, $attributes );
		}

		if ( ! empty( $field['affects'] ) ) {
			$field['class'] .= ' et-pb-affects';
			$attributes .= sprintf( ' data-affects="%s"', esc_attr( implode( ', ', $field['affects'] ) ) );
		}

		if ( 'font' === $field['type'] ) {
			$field['class'] .= ' et-pb-font-select';
		}

		if ( in_array( $field['type'], array( 'font', 'hidden', 'multiple_checkboxes', 'select_with_option_groups' ) ) && ! $only_options ) {
			$hidden_field = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="et-pb-main-setting %3$s" data-default="%4$s" %5$s %6$s/>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $default ),
				$value,
				$attributes
			);

			if ( 'select_with_option_groups' === $field['type'] ) {
				// Since we are using a hidden field to manage the value, we need to clear the data-affects attribute so that
				// it doesn't appear on both the `$field` AND the hidden field. This should probably be done for all of these
				// field types but don't want to risk breaking anything :-/
				$attributes = preg_replace( '/data-affects="[\w\s-,]*"/', 'data-affects=""', $attributes );
			}
		}

		foreach ( $this->get_validation_attr_rules() as $rule ) {
			if ( ! empty( $field[ $rule ] ) ) {
				$this->validation_in_use = true;
				$attributes .= ' data-rule-' . esc_attr( $rule ). '="' . esc_attr( $field[ $rule ] ) . '"';
			}
		}

		if ( isset( $field['before'] ) && ! $only_options ) {
			$field_el .= $this->render_field_before_after_element( $field['before'] );
		}

		switch( $field['type'] ) {
			case 'warning':
				$field_el .= sprintf(
					'<div class="et-pb-option-warning" data-name="%2$s" data-display_if="%3$s">%1$s</div>',
					html_entity_decode( esc_html( $field['message'] ) ),
					esc_attr( $field['name'] ),
					esc_attr( $field['display_if'] )
				);
				break;
			case 'tiny_mce':
				if ( ! empty( $field['tiny_mce_html_mode'] ) ) {
					$field['class'] .= ' html_mode';
				}

				$main_content_property_name = $main_content_field_name = 'et_pb_content_new';

				if ( isset( $this->type ) && 'child' === $this->type ) {
					$main_content_property_name = "data.{$main_content_property_name}";
				}

				$field_el .= sprintf(
					'<div id="%1$s"><%%= typeof( %2$s ) !== \'undefined\' ? %2$s : \'\' %%></div>',
					esc_attr( $main_content_field_name ),
					esc_html( $main_content_property_name )
				);

				break;
			case 'textarea':
			case 'custom_css':
			case 'options_list':
				$field_custom_value = esc_html( $field_name );
				if ( in_array( $field['type'], array( 'custom_css', 'options_list' ) ) ) {
					$field_custom_value .= '.replace( /\|\|/g, "\n" ).replace( /%22/g, "&quot;" ).replace( /%92/g, "\\\" )';
					$field_custom_value .= '.replace( /%91/g, "&#91;" ).replace( /%93/g, "&#93;" )';
				}

				if ( in_array( $field_name, array( 'et_pb_raw_content', 'et_pb_custom_message' ) ) ) {
					$field_custom_value = sprintf( '_.unescape( %1$s )', $field_custom_value );
				}

				$field_el .= sprintf(
					'<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>',
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					esc_html( $field_name ),
					et_esc_previously( $field_custom_value )
				);

				if ( 'options_list' === $field['type'] ) {
					$radio_check = '';
					$row_class   = 'et_options_list_row';

					if ( isset( $field['radio'] ) && true === $field['radio'] ) {
						$radio_check = '<a href="#" class="et_options_list_check"></a>';
						$row_class   .= ' et_options_list_row_radio';
					}

					$field_el = sprintf(
						'<div class="et_options_list">
							<div class="%5$s">
								%6$s
								<input type="text" />
								<div class="et_options_list_actions">
									<a href="#" class="et_options_list_move"></a>
									<a href="#" class="et_options_list_copy"></a>
									<a href="#" class="et_options_list_remove"></a>
								</div>
							</div>
							<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>
							<a href="#" class="et-pb-add-sortable-option"><span>%7$s</span></a>
						</div>',
						esc_attr( $field['class'] ),
						esc_attr( $field['id'] ),
						esc_html( $field_name ),
						et_esc_previously( $field_custom_value ),
						esc_attr( $row_class ),
						$radio_check,
						esc_html__( 'Add New Item', 'et_builder' )
					);
				}
				break;
			case 'conditional_logic':
				$field_custom_value = esc_html( $field_name );
				$field_custom_value .= '.replace( /\|\|/g, "\n" ).replace( /%22/g, "&quot;" ).replace( /%92/g, "\\\" )';
				$field_custom_value .= '.replace( /%91/g, "&#91;" ).replace( /%93/g, "&#93;" )';

				$field_selects = sprintf(
					'<select class="et_conditional_logic_field"></select>
					<select class="et_conditional_logic_condition">
						<option value="is">%1$s</option>
						<option value="is not">%2$s</option>
						<option value="is greater">%3$s</option>
						<option value="is less">%4$s</option>
						<option value="contains">%5$s</option>
						<option value="does not contain">%6$s</option>
						<option value="is empty">%7$s</option>
						<option value="is not empty">%8$s</option>
					</select>',
					esc_html__( 'equals', 'et_builder' ),
					esc_html__( 'does not equal', 'et_builder' ),
					esc_html__( 'is greater than', 'et_builder' ),
					esc_html__( 'is less than', 'et_builder' ),
					esc_html__( 'contains', 'et_builder' ),
					esc_html__( 'does not contain', 'et_builder' ),
					esc_html__( 'is empty', 'et_builder' ),
					esc_html__( 'is not empty', 'et_builder' )
				);

				$field_el = sprintf(
					'<div class="et_options_list et_conditional_logic" data-checked="%6$s" data-unchecked="%7$s">
						<div class="et_options_list_row">
							%5$s
							<a href="#" class="et_options_list_remove"></a>
						</div>
						<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>
						<a href="#" class="et-pb-add-sortable-option"><span>%8$s</span></a>
					</div>',
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					esc_html( $field_name ),
					et_esc_previously( $field_custom_value ),
					$field_selects,
					esc_html__( 'checked', 'et_builder' ),
					esc_html__( 'not checked', 'et_builder' ),
					esc_html__( 'Add New Rule', 'et_builder' )
				);
				break;
			case 'select':
			case 'yes_no_button':
			case 'font':
			case 'select_with_option_groups':
				if ( 'font' === $field['type'] ) {
					$field['id']    .= '_select';
					$field_name     .= '_select';
					$field['class'] .= ' et-pb-helper-field';
					$field['options'] = array();
				}

				$button_options = array();

				if ( 'yes_no_button' === $field['type'] ) {
					$button_options = isset( $field['button_options'] ) ? $field['button_options'] : array();
				}

				if ( isset( $field['default'] ) ) {
					$attributes .= sprintf( ' data-default="%1$s"', esc_attr( $field['default'] ) );
				}

				$select = $this->render_select( $field_name, $field['options'], $field['id'], $field['class'], $attributes, $field['type'], $button_options, $default, $only_options );

				if ( $only_options ) {
					$field_el = $select;
				} else {
					$field_el .= $select;
				}

				if ( 'font' === $field['type'] ) {
					$font_style_button_html = sprintf(
						'<%%= window.et_builder.options_font_buttons_output(%1$s) %%>',
						json_encode( array( 'bold', 'italic', 'uppercase', 'underline' ) )
					);

					$field_el .= sprintf(
						'<div class="et_builder_font_styles mce-toolbar">
							%1$s
						</div> <!-- .et_builder_font_styles -->',
						$font_style_button_html
					);

					$field_el .= $hidden_field;
				}

				if ( 'select_with_option_groups' === $field['type'] ) {
					$field_el .= $hidden_field;
				}

				break;
			case 'color':
			case 'color-alpha':
				$field['default'] = ! empty( $field['default'] ) ? $field['default'] : '';

				if ( $is_custom_color && ( ! isset( $field['default'] ) || '' === $field['default'] ) ) {
					$field['default'] = '';
				}

				$default = ! empty( $field['default'] ) && ! $is_custom_color ? sprintf( ' data-default-color="%1$s" data-default="%1$s"', esc_attr( $field['default'] ) ) : '';

				$color_id = sprintf( ' id="%1$s"', esc_attr( $field['id'] ) );
				$color_value_html = '<%%- typeof( %1$s ) !== \'undefined\' && %1$s !== \'\' ? %1$s : \'%2$s\' %%>';
				$main_color_value = sprintf( $color_value_html, esc_attr( $field_name ), $field['default'] );
				$hidden_color_value = sprintf( $color_value_html, esc_attr( $field_name ), '' );
				$has_preview = isset( $field['has_preview'] ) && $field['has_preview'];

				$field_el = sprintf(
					'<input%1$s class="et-pb-color-picker-hex%5$s%8$s%10$s" type="text"%6$s%7$s placeholder="%9$s" data-selected-value="%2$s" value="%2$s"%3$s />
					%4$s',
					( ! $is_custom_color || $has_preview ? $color_id : '' ),
					$main_color_value,
					$default,
					( ! empty( $field['additional_code'] ) ? $field['additional_code'] : '' ),
					( 'color-alpha' === $field['type'] ? ' et-pb-color-picker-hex-alpha' : '' ),
					( 'color-alpha' === $field['type'] ? ' data-alpha="true"' : '' ),
					( 'color' === $field['type'] ? ' maxlength="7"' : '' ),
					( ! $is_custom_color ? ' et-pb-main-setting' : '' ),
					esc_attr__( 'Hex Value', 'et_builder' ),
					$has_preview ? esc_attr( ' et-pb-color-picker-hex-has-preview' ) : ''
				);

				if ( $is_custom_color && ! $has_preview ) {
					$field_el = sprintf(
						'<span class="et-pb-custom-color-button et-pb-choose-custom-color-button"><span>%1$s</span></span>
						<div class="et-pb-custom-color-container et_pb_hidden">
							%2$s
							<input%3$s class="et-pb-main-setting et-pb-custom-color-picker" type="hidden" value="%4$s" %6$s />
							%5$s
						</div> <!-- .et-pb-custom-color-container -->',
						esc_html__( 'Choose Custom Color', 'et_builder' ),
						$field_el,
						$color_id,
						$hidden_color_value,
						$reset_button_html,
						$attributes
					);
				}
				break;
			case 'upload':
				$field_data_type = ! empty( $field['data_type'] ) ? $field['data_type'] : 'image';
				$field['upload_button_text'] = ! empty( $field['upload_button_text'] ) ? $field['upload_button_text'] : esc_attr__( 'Upload', 'et_builder' );
				$field['choose_text'] = ! empty( $field['choose_text'] ) ? $field['choose_text'] : esc_attr__( 'Choose image', 'et_builder' );
				$field['update_text'] = ! empty( $field['update_text'] ) ? $field['update_text'] : esc_attr__( 'Set image', 'et_builder' );
				$field['class'] = ! empty( $field['class'] ) ? ' ' . $field['class'] : '';
				$field_additional_button = ! empty( $field['additional_button'] ) ? "\n\t\t\t\t\t" . $field['additional_button'] : '';
				$field_el .= sprintf(
					'<input id="%1$s" type="text" class="et-pb-main-setting regular-text et-pb-upload-field%8$s" value="<%%- typeof( %2$s ) !== \'undefined\' ? %2$s : \'\' %%>" %9$s />
					<input type="button" class="button button-upload et-pb-upload-button" value="%3$s" data-choose="%4$s" data-update="%5$s" data-type="%6$s" />%7$s',
					esc_attr( $field['id'] ),
					esc_attr( $field_name ),
					esc_attr( $field['upload_button_text'] ),
					esc_attr( $field['choose_text'] ),
					esc_attr( $field['update_text'] ),
					esc_attr( $field_data_type ),
					$field_additional_button,
					esc_attr( $field['class'] ),
					$attributes
				);
				break;
			case 'checkbox':
				$field_el .= sprintf(
					'<input type="checkbox" name="%1$s" id="%2$s" class="et-pb-main-setting" value="on" <%%- typeof( %1$s ) !==  \'undefined\' && %1$s == \'on\' ? checked="checked" : "" %%>>',
					esc_attr( $field['name'] ),
					esc_attr( $field['id'] )
				);
				break;
			case 'multiple_checkboxes' :
				$checkboxes_set = '<div class="et_pb_checkboxes_wrapper">';

				if ( ! empty( $field['options'] ) ) {
					foreach( $field['options'] as $option_value => $option_label ) {
						$checkboxes_set .= sprintf(
							'%3$s<label><input type="checkbox" class="et_pb_checkbox_%1$s" value="%1$s"> %2$s</label><br/>',
							esc_attr( $option_value ),
							esc_html( $option_label ),
							"\n\t\t\t\t\t"
						);
					}
				}

				// additional option for disable_on option for backward compatibility
				if ( isset( $field['additional_att'] ) && 'disable_on' === $field['additional_att'] ) {
					$et_pb_disabled_value = sprintf(
						$value_html,
						esc_attr( 'et_pb_disabled' ),
						esc_attr( 'et_pb_disabled' ),
						''
					);

					$checkboxes_set .= sprintf(
						'<input type="hidden" id="et_pb_disabled" class="et_pb_disabled_option"%1$s>',
						$et_pb_disabled_value
					);
				}

				$field_el .= $checkboxes_set . $hidden_field . '</div>';
				break;
			case 'hidden':
				$field_el .= $hidden_field;
				break;
			case 'custom_margin':
			case 'custom_padding':

				$custom_margin_class = "";

				// Fill the array of values for tablet and phone
				if ( $need_mobile_options ) {
					$mobile_values_array = array();
					$has_saved_value = array();
					$mobile_desktop_class = ' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active';
					$mobile_desktop_data = ' data-device="desktop"';

					foreach( array( 'tablet', 'phone' ) as $device ) {
						$mobile_values_array[] = sprintf(
							$value_html,
							esc_attr( $field_name . '_' . $device ),
							esc_attr( $field_name . '_' . $device ),
							$default
						);
						$has_saved_value[] = sprintf( ' data-has_saved_value="<%%- typeof( %1$s ) !== \'undefined\' ? \'yes\' : \'no\' %%>" ',
							esc_attr( $field_name . '_' . $device )
						);
					}

					$value_last_edited = sprintf(
						$value_html,
						esc_attr( $field_name . '_last_edited' ),
						esc_attr( $field_name . '_last_edited' ),
						''
					);
					// additional field to save the last edited field which will be opened automatically
					$additional_mobile_fields = sprintf( '<input id="%1$s" type="hidden" class="et_pb_mobile_last_edited_field"%2$s>',
						esc_attr( $field_name . '_last_edited' ),
						$value_last_edited
					);
				}

				// Add auto_important class to field which automatically append !important tag
				if ( isset( $this->advanced_options['custom_margin_padding']['css']['important'] ) ) {
					$custom_margin_class .= " auto_important";
				}

				$single_fields_settings = array(
					'side' => '',
					'label' => '',
					'need_mobile' => $need_mobile_options ? 'need_mobile' : '',
					'class' => esc_attr( $custom_margin_class ),
				);

				$field_el .= sprintf(
					'<div class="et_custom_margin_padding">
						%6$s
						%7$s
						%8$s
						%9$s
						<input type="hidden" name="%1$s" data-default="%5$s" id="%2$s" class="et_custom_margin_main et-pb-main-setting%11$s"%12$s %3$s %4$s/>
						%10$s
						%13$s
					</div> <!-- .et_custom_margin_padding -->',
					esc_attr( $field['name'] ),
					esc_attr( $field['id'] ),
					$value,
					$attributes,
					esc_attr( $default ), // #5
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'top', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_padding_output(%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'top',
								'label' => esc_html__( 'Top', 'et_builder' ),
							) ) )
						) : '',
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'right', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_padding_output(%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'right',
								'label' => esc_html__( 'Right', 'et_builder' ),
							) ) )
						) : '',
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'bottom', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_padding_output(%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'bottom',
								'label' => esc_html__( 'Bottom', 'et_builder' ),
							) ) )
						) : '',
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'left', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_padding_output(%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'left',
								'label' => esc_html__( 'Left', 'et_builder' ),
							) ) )
						) : '',
					$need_mobile_options ?
						sprintf(
							'<input type="hidden" name="%1$s_tablet" data-default="%4$s" id="%2$s_tablet" class="et-pb-main-setting et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet" %5$s %3$s %7$s/>
							<input type="hidden" name="%1$s_phone" data-default="%4$s" id="%2$s_phone" class="et-pb-main-setting et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone" %6$s %3$s %8$s/>',
							esc_attr( $field['name'] ),
							esc_attr( $field['id'] ),
							$attributes,
							esc_attr( $default ),
							$mobile_values_array[0],
							$mobile_values_array[1],
							$has_saved_value[0],
							$has_saved_value[1]
						)
						: '', // #10
					$need_mobile_options ? esc_attr( $mobile_desktop_class ) : '',
					$need_mobile_options ? $mobile_desktop_data : '',
					$need_mobile_options ? $additional_mobile_fields : '' // #13
				);
				break;
			case 'text':
			case 'number':
			case 'date_picker':
			case 'range':
			default:
				$validate_number = isset( $field['number_validation'] ) && $field['number_validation'] ? true : false;

				if ( 'date_picker' === $field['type'] ) {
					$field['class'] .= ' et-pb-date-time-picker';
				}

				$field['class'] .= 'range' === $field['type'] ? ' et-pb-range-input' : ' et-pb-main-setting';

				$type = in_array( $field['type'], array( 'text', 'number' ) ) ? $field['type'] : 'text';

				$field_el .= sprintf(
					'<input id="%1$s" type="%11$s" class="%2$s%5$s%9$s"%6$s%3$s%8$s%10$s %4$s/>%7$s',
					esc_attr( $field['id'] ),
					esc_attr( $field['class'] ),
					$value,
					$attributes,
					( $validate_number ? ' et-validate-number' : '' ),
					( $validate_number ? ' maxlength="3"' : '' ),
					( ! empty( $field['additional_button'] ) ? $field['additional_button'] : '' ),
					( '' !== $default
						? sprintf( ' data-default="%1$s"', esc_attr( $default ) )
						: ''
					),
					$need_mobile_options ? ' et_pb_setting_mobile et_pb_setting_mobile_active et_pb_setting_mobile_desktop' : '',
					$need_mobile_options ? ' data-device="desktop"' : '',
					$type
				);

				// generate additional fields for mobile settings switcher if needed
				if ( $need_mobile_options ) {
					$additional_fields = '';

					foreach( array( 'tablet', 'phone' ) as $device_type ) {
						$value_mobile = sprintf(
							$value_html,
							esc_attr( $field_name . '_' . $device_type ),
							esc_attr( $field_name . '_' . $device_type ),
							$default
						);
						// additional data attribute to handle default values for the responsive options
						$has_saved_value = sprintf( ' data-has_saved_value="<%%- typeof( %1$s ) !== \'undefined\' ? \'yes\' : \'no\' %%>" ',
							esc_attr( $field_name . '_' . $device_type )
						);

						$additional_fields .= sprintf( '<input id="%2$s" type="%11$s" class="%3$s%5$s et_pb_setting_mobile et_pb_setting_mobile_%9$s"%6$s%8$s%1$s data-device="%9$s" %4$s%10$s/>%7$s',
							$value_mobile,
							esc_attr( $field['id'] ) . '_' . $device_type,
							esc_attr( $field['class'] ),
							$attributes,
							( $validate_number ? ' et-validate-number' : '' ), // #5
							( $validate_number ? ' maxlength="3"' : '' ),
							( ! empty( $field['additional_button'] ) ? $field['additional_button'] : '' ),
							( '' !== $default
								? sprintf( ' data-default="%1$s"', esc_attr( $default ) )
								: ''
							),
							esc_attr( $device_type ),
							$has_saved_value, // #10,
							$type
						);
					}

					$value_last_edited = sprintf(
						$value_html,
						esc_attr( $field_name . '_last_edited' ),
						esc_attr( $field_name . '_last_edited' ),
						''
					);
					// additional field to save the last edited field which will be opened automatically
					$additional_fields .= sprintf( '<input id="%1$s" type="hidden" class="et_pb_mobile_last_edited_field"%2$s>',
						esc_attr( $field_name . '_last_edited' ),
						$value_last_edited
					);
				}

				if ( 'range' === $field['type'] ) {
					$value = sprintf(
						$value_html,
						esc_attr( $field_name ),
						esc_attr( sprintf( 'parseFloat( %1$s )', $field_name ) ),
						( '' !== $default ? floatval( $default ) : '' )
					);
					$fixed_range = isset($field['fixed_range']) && $field['fixed_range'];

					$range_settings_html = '';
					$range_properties = apply_filters( 'et_builder_range_properties', array( 'min', 'max', 'step' ) );
					foreach ( $range_properties as $property ) {
						if ( isset( $field['range_settings'][ $property ] ) ) {
							$range_settings_html .= sprintf( ' %2$s="%1$s"',
								esc_attr( $field['range_settings'][ $property ] ),
								esc_html( $property )
							);
						}
					}

					$range_el = sprintf(
						'<input type="range" class="et-pb-main-setting et-pb-range%4$s%6$s" data-default="%2$s"%1$s%3$s%5$s />',
						$value,
						esc_attr( $default ),
						$range_settings_html,
						$need_mobile_options ? ' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active' : '',
						$need_mobile_options ? ' data-device="desktop"' : '',
						$fixed_range ? ' et-pb-fixed-range' : ''
					);

					if ( $need_mobile_options ) {
						foreach( array( 'tablet', 'phone' ) as $device_type ) {
							// additional data attribute to handle default values for the responsive options
							$has_saved_value = sprintf( ' data-has_saved_value="<%%- typeof( %1$s ) !== \'undefined\' ? \'yes\' : \'no\' %%>" ',
								esc_attr( $field_name . '_' . $device_type )
							);
							$value_mobile_range = sprintf(
								$value_html,
								esc_attr( $field_name . '_' . $device_type ),
								esc_attr( sprintf( 'parseFloat( %1$s )', $field_name . '_' . $device_type ) ),
								( '' !== $default ? floatval( $default ) : '' )
							);
							$range_el .= sprintf(
								'<input type="range" class="et-pb-main-setting et-pb-range et_pb_setting_mobile et_pb_setting_mobile_%3$s%6$s" data-default="%1$s"%4$s%2$s data-device="%3$s"%5$s/>',
								esc_attr( $default ),
								$range_settings_html,
								esc_attr( $device_type ),
								$value_mobile_range,
								$has_saved_value,
								$fixed_range ? ' et-pb-fixed-range' : ''
							);
						}
					}

					$field_el = $range_el . "\n" . $field_el;
				}

				if ( $need_mobile_options ) {
					$field_el = $field_el . $additional_fields;
				}

				break;
		}

		if ( isset( $field['has_preview'] ) && $field['has_preview'] ) {
			$field_el = sprintf(
				'<div class="et-pb-option-preview et-pb-option-preview--empty">
					<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
						%1$s
					</button>
					<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
						%2$s
					</button>
					<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
						%3$s
					</button>
				</div>%4$s',
				$this->get_icon( 'add' ),
				$this->get_icon( 'setting' ),
				$this->get_icon( 'delete' ),
				$field_el
			);
		}

		if ( $need_mobile_options ) {
			$field_el = $mobile_settings_tabs . "\n" . $field_el;
			$field_el .= '<span class="et-pb-mobile-settings-toggle"></span>';
		}

		if ( isset( $field['type'] ) && isset( $field['tab_slug'] ) && 'advanced' === $field['tab_slug'] && ! $is_custom_color ) {
			$field_el .= $reset_button_html;
		}

		if ( isset( $field['after'] ) && ! $only_options ) {
			$field_el .= $this->render_field_before_after_element( $field['after'] );
		}

		return "\t" . $field_el;
	}

	public function render_field_before_after_element( $elements ) {
		$field_el = '';
		$elements = is_array( $elements ) ? $elements : array( $elements );

		foreach ( $elements as $element ) {
			$attributes = '';

			if ( ! empty( $element['attributes'] ) ) {
				$this->process_html_attributes( $element, $attributes );
			}

			switch ( $element['type'] ) {
				case 'button':
					$class     = isset( $element['class'] ) ? esc_attr( $element['class'] ) : '';
					$text      = isset( $element['text'] ) ? et_esc_previously( $element['text'] ) : '';
					$field_el .= sprintf( '<button class="button %1$s"%2$s>%3$s</button>', $class, $attributes, $text );

					break;
			}
		}

		return $field_el;
	}

	public function render_select_options( $select_name, $options, $default ) {
		$options_output = '';

		foreach ( (array) $options as $option_value => $option_label ) {
			$data = '';
			if ( is_array( $option_label ) ) {
				if ( isset( $option_label['data'] ) ) {
					$data_key_name = key( $option_label['data'] );
					$data = sprintf(
						' data-%1$s="%2$s"',
						esc_attr( $data_key_name ),
						esc_attr( $option_label['data'][ $data_key_name ] )
					);
				}
				$option_label = $option_label['value'];
			}
			$selected_attr = '<%- ( typeof( ' . esc_attr( $select_name ) . ' ) !== \'undefined\' && \'' . esc_attr( $option_value ) . '\' === ' . esc_attr( $select_name ) . ' ) || ( typeof( ' . esc_attr( $select_name ) . ' ) === \'undefined\' && \'' . $default . '\' !== \'\' && \'' . esc_attr( $option_value ) . '\' === \'' . $default .'\' ) ?  \' selected="selected"\' : \'\' %>';
			$options_output .= sprintf(
				'%4$s<option%5$s value="%1$s"%2$s>%3$s</option>',
				esc_attr( $option_value ),
				$selected_attr,
				esc_html( $option_label ),
				"\n\t\t\t\t\t\t",
				( '' !== $data ? $data : '' )
			);
		}

		return $options_output;
	}

	function render_select( $name, $options, $id = '', $class = '', $attributes = '', $field_type = '', $button_options = array(), $default = '', $only_options = false ) {
		$options_output = '';

		if ( 'font' === $field_type ) {
			$options_output = '<%= window.et_builder.fonts_template() %>';

		} else if ( 'select_with_option_groups' === $field_type ) {
			foreach ( $options as $option_group_name => $option_group ) {
				$option_group_name = esc_attr( $option_group_name );
				$options_output   .= '0' !== $option_group_name ? "<optgroup label='{$option_group_name}'>" : '';
				$options_output   .= $this->render_select_options( $name, $option_group, $default );
				$options_output   .= '0' !== $option_group_name ? '</optgroup>' : '';
			}

			$class = rtrim( $class );

			$name = $id = '';

		} else {
			$options_output .= $this->render_select_options( $name, $options, $default );
			$class           = rtrim( 'et-pb-main-setting ' . $class );
		}

		$output = sprintf(
			'%6$s
				<select name="%1$s"%2$s%3$s%4$s%8$s>%5$s</select>
			%7$s',
			esc_attr( $name ),
			( ! empty( $id ) ? sprintf(' id="%s"', esc_attr( $id ) ) : '' ),
			( ! empty( $class ) ? sprintf(' class="%s"', esc_attr( $class ) ) : '' ),
			( ! empty( $attributes ) ? $attributes : '' ),
			$options_output . "\n\t\t\t\t\t",
			'yes_no_button' === $field_type ?
				sprintf(
					'<div class="et_pb_yes_no_button_wrapper %2$s">
						%1$s',
					sprintf( '<%%= window.et_builder.options_yes_no_button_output(%1$s) %%>',
						json_encode( array(
							'on' => esc_html( $options['on'] ),
							'off' => esc_html( $options['off'] ),
						) )
					),
					( ! empty( $button_options['button_type'] ) && 'equal' === $button_options['button_type'] ? ' et_pb_button_equal_sides' : '' )
				) : '',
			'yes_no_button' === $field_type ? '</div>' : '',
			( 'et_pb_transparent_background' === $name ? '<%- typeof( ' . esc_html( $name ) . ' ) === \'undefined\' ?  \' data-default=default\' : \'\' %>' : '' )
		);
		return $only_options ? $options_output : $output;
	}

	function get_main_tabs() {
		$tabs = array(
			'general'    => esc_html__( 'Content', 'et_builder' ),
			'advanced'   => esc_html__( 'Design', 'et_builder' ),
			'custom_css' => esc_html__( 'Advanced', 'et_builder' ),
		);

		return apply_filters( 'et_builder_main_tabs', $tabs );
	}

	function get_validation_attr_rules() {
		return array(
			'minlength',
			'maxlength',
			'min',
			'max'
		);
	}

	function get_validation_class_rules() {
		return array(
			'required',
			'email',
			'url',
			'date',
			'dateISO',
			'number',
			'digits',
			'creditcard'
		);
	}

	function sort_fields( $fields ) {
		$tabs_fields   = array();
		$sorted_fields = array();
		$i = 0;

		// Sort fields array by tab name
		foreach ( $fields as $field_slug => $field_options ) {
			$field_options['_order_number'] = $i;

			$tab_slug = ! empty( $field_options['tab_slug'] ) ? $field_options['tab_slug'] : 'general';
			$tabs_fields[ $tab_slug ][ $field_slug ] = $field_options;

			$i++;
		}

		// Sort fields within tabs by priority
		foreach ( $tabs_fields as $tab_fields ) {
			uasort( $tab_fields, array( 'self', 'compare_by_priority' ) );
			$sorted_fields = array_merge( $sorted_fields, $tab_fields );
		}

		return $sorted_fields;
	}

	function get_options() {
		$output = '';
		$tab_output = '';
		$tab_slug = '';
		$toggle_slug = '';
		$toggle_all_options_slug = 'all_options';
		$toggles_used = isset( $this->options_toggles );
		$tabs_output = array( 'general' => array() );
		$all_fields = $this->sort_fields( $this->_get_fields() );
		$all_fields_keys = array_keys( $all_fields );
		$background_fields_names = $this->get_background_fields_names();
		$module_has_background_color_field = in_array( 'background_color', $all_fields_keys ) || in_array( 'module_bg_color', $all_fields_keys );

		foreach( $all_fields as $field_name => $field ) {
			if ( ! empty( $field['type'] ) && ( 'skip' === $field['type'] || 'computed' === $field['type'] ) ) {
				continue;
			}

			// add only options allowed for current user
			if (
				( ! et_pb_is_allowed( 'edit_colors' ) && ( ! empty( $field['type'] ) && in_array( $field['type'], array( 'color', 'color-alpha' ) ) || ( ! empty( $field['option_category'] ) && 'color_option' === $field['option_category'] ) ) )
				||
				( ! et_pb_is_allowed( 'edit_content' ) && ! empty( $field['option_category'] ) && 'basic_option' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_layout' ) && ! empty( $field['option_category'] ) && 'layout' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_configuration' ) && ! empty( $field['option_category'] ) && 'configuration' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_fonts' ) && ! empty( $field['option_category'] ) && ( 'font_option' === $field['option_category'] || ( 'button' === $field['option_category'] && ! empty( $field['type'] ) && 'font' === $field['type'] ) ) )
				||
				( ! et_pb_is_allowed( 'edit_buttons' ) && ! empty( $field['option_category'] ) && 'button' === $field['option_category'] )
			) {
				continue;
			}

			$option_output = '';

			if ( in_array( $field_name, array( 'background_color', 'module_bg_color' ) ) ) {
				// append background UI
				$option_output .= $this->wrap_settings_background_fields( $all_fields );
			} elseif ( $module_has_background_color_field && in_array( $field_name , $background_fields_names ) ) {
				// remove background-related fields from setting modals since it'll be printed by background UI
				continue;
			} else {
				// append normal fields
				$option_output .= $this->wrap_settings_option_label( $field );
				$option_output .= $this->wrap_settings_option_field( $field );
			}

			$tab_slug = ! empty( $field['tab_slug'] ) ? $field['tab_slug'] : 'general';
			$is_toggle_option = isset( $field['toggle_slug'] ) && $toggles_used && isset( $this->options_toggles[ $tab_slug ] );
			$toggle_slug = $is_toggle_option ? $field['toggle_slug'] : $toggle_all_options_slug;
			$sub_toggle_slug = 'all_options' !== $toggle_slug && isset( $field['sub_toggle'] ) && '' !== $field['sub_toggle'] ? $field['sub_toggle'] : 'main';
			$tabs_output[ $tab_slug ][ $toggle_slug ][ $sub_toggle_slug ][] = $this->wrap_settings_option( $option_output, $field );
		}

		// make sure that custom_css tab is the last item in array
		if ( isset( $tabs_output['custom_css'] ) ) {
			$custom_css_output = $tabs_output['custom_css'];
			unset( $tabs_output['custom_css'] );
			$tabs_output['custom_css'] = $custom_css_output;
		}

		foreach ( $tabs_output as $tab_slug => $tab_settings ) {
			$tab_output        = '';
			$this->used_tabs[] = $tab_slug;
			$i = 0;

			if ( isset( $tabs_output[ $tab_slug ] ) ) {
				if ( isset( $this->options_toggles[ $tab_slug ] ) ) {
					$this->options_toggles[ $tab_slug ]['toggles'] = self::et_pb_order_toggles_by_priority( $this->options_toggles[ $tab_slug ]['toggles'] );

					foreach ( $this->options_toggles[ $tab_slug ]['toggles'] as $toggle_slug => $toggle_data ) {
						$toggle_heading = is_array( $toggle_data ) ? $toggle_data['title'] : $toggle_data;
						if ( ! isset( $tabs_output[ $tab_slug ][ $toggle_slug ] ) ) {
							continue;
						}

						$i++;
						$toggle_output = '';
						$is_accordion_enabled = isset( $this->options_toggles[ $tab_slug ]['settings']['bb_toggles_enabeld'] ) && $this->options_toggles[ $tab_slug ]['settings']['bb_toggles_enabled'] ? true : false;

						if ( is_array( $toggle_data ) && ! empty( $toggle_data ) ) {
							if ( ! isset( $toggle_data['sub_toggles'] ) ) {
								$toggle_data['sub_toggles'] = array( 'main' => '' );
							}

							foreach( $toggle_data['sub_toggles'] as $sub_toggle_id => $sub_toggle_name ) {
								if ( ! isset( $tabs_output[ $tab_slug ][ $toggle_slug ][ $sub_toggle_id ] ) ) {
									continue;
								}

								foreach ( $tabs_output[ $tab_slug ][ $toggle_slug ][ $sub_toggle_id ] as $toggle_option_output ) {
									if ( 'main' === $sub_toggle_id ) {
										$toggle_output .= $toggle_option_output;
									} else {
										$toggle_output .= sprintf(
											'<div class="et_pb_subtoggle_section">
												%1$s
											</div>',
											$toggle_option_output
										);
									}
								}
							}
						} else {
							foreach ( $tabs_output[ $tab_slug ][ $toggle_slug ] as $toggle_option_id => $toggle_option_data ) {
								foreach( $toggle_option_data as $toggle_option_output ) {
									$toggle_output .= $toggle_option_output;
								}
							}
						}

						if ( '' === $toggle_output ) {
							continue;
						}

						$toggle_output = sprintf(
							'<div class="et-pb-options-toggle-container%3$s%4$s">
								<h3 class="et-pb-option-toggle-title">%1$s</h3>
								<div class="et-pb-option-toggle-content">
									%2$s
								</div> <!-- .et-pb-option-toggle-content -->
							</div> <!-- .et-pb-options-toggle-container -->',
							esc_html( $toggle_heading ),
							$toggle_output,
							( $is_accordion_enabled ? ' et-pb-options-toggle-enabled' : ' et-pb-options-toggle-disabled' ),
							( 1 === $i && $is_accordion_enabled ? ' et-pb-option-toggle-content-open' : '' )
						);

						$tab_output .= $toggle_output;
					}
				}

				if ( isset( $tabs_output[ $tab_slug ][ $toggle_all_options_slug ] ) ) {
					foreach ( $tabs_output[ $tab_slug ][ $toggle_all_options_slug ] as $no_toggle_option_data ) {
						foreach( $no_toggle_option_data as $subtoggle_id => $no_toggle_option_output ) {
							$tab_output .= $no_toggle_option_output;
						}
					}
				}
			}

			$output .= sprintf(
				'<div class="et-pb-options-tab et-pb-options-tab-%1$s">
					%3$s
					%2$s
				</div> <!-- .et-pb-options-tab_%1$s -->',
				esc_attr( $tab_slug ),
				$tab_output,
				( 'general' === $tab_slug ? $this->children_settings() : '' )
			);
		}

		// return error message if no tabs allowed for current user
		if ( '' === $output ) {
			$output = esc_html__( "You don't have sufficient permissions to access the settings", 'et_builder' );
		}

		return $output;
	}

	function children_settings() {
		$output = '';
		if ( ! empty( $this->child_slug ) ) {
			$output = sprintf(
			'%6$s<div class="et-pb-option-advanced-module-settings" data-module_type="%1$s">
				<ul class="et-pb-sortable-options">
				</ul>
				<a href="#" class="et-pb-add-sortable-option"><span>%2$s</span></a>
			</div> <!-- .et-pb-option -->

			<div class="et-pb-option et-pb-option-main-content et-pb-option-advanced-module">
				<label for="et_pb_content_new">%3$s</label>
				<div class="et-pb-option-container">
					<div id="et_pb_content_new"><%%= typeof( et_pb_content_new )!== \'undefined\' && \'\' !== et_pb_content_new.trim() ? et_pb_content_new : \'%7$s\' %%></div>
					<p class="description">%4$s</p>
				</div> <!-- .et-pb-option-container -->
			</div> <!-- .et-pb-option -->%5$s',
			esc_attr( $this->child_slug ),
			esc_html( $this->add_new_child_text() ),
			esc_html__( 'Content', 'et_builder' ),
			esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
			"\n\n",
			"\t",
			$this->predefined_child_modules()
			);
		}

		return $output;
	}

	function add_new_child_text() {
		$child_slug = ! empty( $this->child_item_text ) ? $this->child_item_text : '';

		$child_slug = '' === $child_slug ? esc_html__( 'Add New Item', 'et_builder' ) : sprintf( esc_html__( 'Add New %s', 'et_builder' ), $child_slug );

		return $child_slug;
	}

	function wrap_settings( $output ) {
		$tabs_output = '';
		$i = 0;
		$tabs = array();

		// General Settings Tab should be added to all modules if allowed
		if ( et_pb_is_allowed( 'general_settings' ) ) {
			$tabs['general'] = isset( $this->main_tabs['general'] ) ? $this->main_tabs['general'] : esc_html__( 'General Settings', 'et_builder' );
		}

		foreach ( $this->used_tabs as $tab_slug ) {
			if ( 'general' === $tab_slug ) {
				continue;
			}

			// Add only tabs allowed for current user
			if ( et_pb_is_allowed( $tab_slug . '_settings' ) ) {
				$tabs[ $tab_slug ] = $this->main_tabs[ $tab_slug ];
			}
		}

		$tabs_array = array();
		$tabs_json = '';

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$i++;

			$tabs_array[$i] = array(
				'slug' => $tab_slug,
				'label' => $tab_name,
			);

			$tabs_json = json_encode( $tabs_array );
		}

		$tabs_output = sprintf( '<%%= window.et_builder.options_tabs_output(%1$s) %%>', $tabs_json );
		$preview_tabs_output = '<%= window.et_builder.preview_tabs_output() %>';

		$output = sprintf(
			'%2$s
			%3$s
			<div class="et-pb-options-tabs">
				%1$s
			</div> <!-- .et-pb-options-tabs -->
			<div class="et-pb-preview-tab"></div> <!-- .et-pb-preview-tab -->
			',
			$output,
			$tabs_output,
			$preview_tabs_output
		);

		return sprintf(
			'%2$s<div class="et-pb-main-settings">%1$s</div> <!-- .et-pb-main-settings -->%3$s',
			"\n\t\t" . $output,
			"\n\t\t",
			"\n"
		);
	}

	function wrap_validation_form( $output ) {
		return '<form class="et-builder-main-settings-form validate">' . $output . '</form>';
	}

	function get_shortcode_fields() {
		$fields = array();

		foreach( $this->process_fields( $this->fields_unprocessed ) as $field_name => $field ) {
			$value = '';
			if ( isset( $field['shortcode_default'] ) ) {
				$value = $field['shortcode_default'];
			} else if( isset( $field['default'] ) ) {
				$value = $field['default'];
			}

			$fields[ $field_name ] = $value;
		}

		$fields['disabled'] = 'off';
		$fields['disabled_on'] = '';
		$fields['global_module'] = '';
		$fields['saved_tabs'] = '';
		$fields['ab_subject'] = '';
		$fields['ab_subject_id'] = '';
		$fields['ab_goal'] = '';
		$fields['locked'] = '';
		$fields['template_type'] = '';
		$fields['inline_fonts'] = '';
		$fields['collapsed'] = '';

		return $fields;
	}

	function get_module_data_attributes() {
		$attributes = apply_filters(
			"{$this->slug}_data_attributes",
			array(),
			$this->shortcode_atts,
			$this->shortcode_callback_num()
		);

		$data_attributes = '';

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $name => $value ) {
				$data_attributes .= sprintf(
					' data-%1$s="%2$s"',
					sanitize_title( $name ),
					esc_attr( $value )
				);
			}
		}

		return $data_attributes;
	}

	function build_microtemplate() {
		$this->validation_in_use = false;
		$template_output = '';

		if ( 'child' === $this->type ) {
			$id_attr = sprintf( 'et-builder-advanced-setting-%s', $this->slug );
		} else {
			$id_attr = sprintf( 'et-builder-%s-module-template', $this->slug );
		}

		if ( ! isset( $this->settings_text ) ) {
			$settings_text = sprintf(
				__( '%1$s %2$s Settings', 'et_builder' ),
				esc_html( $this->name ),
				'child' === $this->type ? esc_html__( 'Item', 'et_builder' ) : esc_html__( 'Module', 'et_builder' )
			);
		} else {
			$settings_text = $this->settings_text;
		}

		if ( file_exists( ET_BUILDER_DIR . 'microtemplates/' . $this->slug . '.php' ) ) {
			ob_start();
			include ET_BUILDER_DIR . 'microtemplates/' . $this->slug . '.php';
			$output = ob_get_clean();
		} else {
			$output = $this->get_options();
		}

		$output = $this->wrap_settings( $output );
		if ( $this->validation_in_use ) {
			$output = $this->wrap_validation_form( $output );
		}

		$template_output = sprintf(
			'<script type="text/template" id="%1$s">
				<h3 class="et-pb-settings-heading">%2$s</h3>
				%3$s
			</script> <!-- #%4$s -->%5$s',
			esc_attr( $id_attr ),
			esc_html( $settings_text ),
			$output,
			esc_html( $id_attr ),
			"\n"
		);

		if ( $this->type == 'child' ) {
			$title_var = esc_js( $this->child_title_var );
			$title_var = false === strpos( $title_var, 'et_pb_' ) ? 'et_pb_'. $title_var : $title_var;
			$title_fallback_var = esc_js( $this->child_title_fallback_var );
			$title_fallback_var = false === strpos( $title_fallback_var, 'et_pb_' ) ? 'et_pb_'. $title_fallback_var : $title_fallback_var;
			$add_new_text = isset( $this->advanced_setting_title_text ) ? $this->advanced_setting_title_text : $this->add_new_child_text();

			$template_output .= sprintf(
				'%6$s<script type="text/template" id="et-builder-advanced-setting-%1$s-title">
					<%% if ( typeof( %2$s ) !== \'undefined\' && typeof( %2$s ) === \'string\' && %2$s !== \'\' ) { %%>
						<%%- %2$s.replace( /%%91/g, "[" ).replace( /%%93/g, "]" ) %%>
					<%% } else if ( typeof( %3$s ) !== \'undefined\' && typeof( %3$s ) === \'string\' && %3$s !== \'\' ) { %%>
						<%%- %3$s.replace( /%%91/g, "[" ).replace( /%%93/g, "]" ) %%>
					<%% } else { %%>
						<%%- \'%4$s\' %%>
					<%% } %%>
				</script>%5$s',
				esc_attr( $this->slug ),
				esc_html( $title_var ),
				esc_html( $title_fallback_var ),
				esc_html( $add_new_text ),
				"\n\n",
				"\t"
			);
		}

		return $template_output;
	}

	function get_gradient( $args ) {
		$defaults = apply_filters( 'et_pb_default_gradient', array(
			'type'             => 'linear',
			'direction'        => '180deg',
			'radial_direction' => 'center',
			'color_start'      => '#2b87da',
			'color_end'        => '#29c4a9',
			'start_position'   => '0%',
			'end_position'     => '100%',
		) );

		$args           = wp_parse_args( array_filter( $args ), $defaults );
		$direction      = $args['type'] === 'linear' ? $args['direction'] : "circle at {$args['radial_direction']}";
		$start_position = et_sanitize_input_unit( $args['start_position'], false, '%' );
		$end_Position   = et_sanitize_input_unit( $args['end_position'], false, '%');

		return esc_html( "{$args['type']}-gradient(
			{$direction},
			{$args['color_start']} ${start_position},
			{$args['color_end']} ${end_Position}
		)" );
	}

	/**
	 * Remove suffix of a string
	 */
	function remove_suffix( $string, $separator = '_' ) {
		$stringAsArray = explode( $separator, $string );

		array_pop( $stringAsArray );

		return implode( $separator, $stringAsArray );
	}

	function process_additional_options( $function_name ) {
		if ( ! isset( $this->advanced_options ) ) {
			return false;
		}

		$this->process_advanced_fonts_options( $function_name );

		$this->process_advanced_background_options( $function_name );

		$this->process_advanced_border_options( $function_name );

		$this->process_advanced_custom_margin_options( $function_name );

		$this->process_advanced_button_options( $function_name );
	}

	function process_inline_fonts_option( $fonts_list ) {
		if ( '' === $fonts_list ) {
			return;
		}

		$fonts_list_array = explode( ',', $fonts_list );

		foreach( $fonts_list_array as $font_name ) {
			et_builder_enqueue_font( $font_name );
		}
	}

	function process_advanced_fonts_options( $function_name ) {
		if ( ! isset( $this->advanced_options['fonts'] ) ) {
			return;
		}

		$font_options = array();
		$slugs = array(
			'font',
			'font_size',
			'text_color',
			'letter_spacing',
			'line_height',
		);
		$mobile_options_slugs = array(
			'font_size_tablet',
			'font_size_phone',
			'line_height_tablet',
			'line_height_phone',
			'letter_spacing_tablet',
			'letter_spacing_phone',
		);

		$slugs = array_merge( $slugs, $mobile_options_slugs ); // merge all slugs into single array to define them in one place

		// Separetely defined and merged *_last_edited slugs. It needs to be merged as reference but shouldn't be looped for calling mobile attributes
		$mobile_options_last_edited_slugs = array(
			'font_size_last_edited',
			'line_height_last_edited',
			'letter_spacing_last_edited',
		);

		$slugs = array_merge( $slugs, $mobile_options_last_edited_slugs );

		foreach ( $this->advanced_options['fonts'] as $option_name => $option_settings ) {
			$style = '';
			$important_options = array();
			$is_important_set = isset( $option_settings['css']['important'] );
			$is_placeholder = isset( $option_settings['css']['placeholder'] );

			$use_global_important = $is_important_set && 'all' === $option_settings['css']['important'];

			if ( ! $use_global_important && $is_important_set && 'plugin_only' === $option_settings['css']['important'] && et_is_builder_plugin_active() ) {
				$use_global_important = true;
			}

			if ( $is_important_set && is_array( $option_settings['css']['important'] ) ) {
				$important_options = $option_settings['css']['important'];

				if ( et_is_builder_plugin_active() && in_array( 'plugin_all', $option_settings['css']['important'] ) ) {
					$use_global_important = true;
				}
			}

			foreach ( $slugs as $font_option_slug ) {
				if ( isset( $this->shortcode_atts["{$option_name}_{$font_option_slug}"] ) ) {
					$font_options["{$option_name}_{$font_option_slug}"] = $this->shortcode_atts["{$option_name}_{$font_option_slug}"];
				}
			}

			$field_key = "{$option_name}_{$slugs[0]}";
			$global_setting_name  = $this->get_global_setting_name( $field_key );
			$global_setting_value = ET_Global_Settings::get_value( $global_setting_name );

			if ( '' !== $font_options["{$option_name}_{$slugs[0]}"] || ! $global_setting_value ) {
				$important = in_array( 'font', $important_options ) || $use_global_important ? ' !important' : '';
				$font_styles = et_builder_set_element_font( $font_options["{$option_name}_{$slugs[0]}"], ( '' !== $important ), $global_setting_value );

				if ( isset( $option_settings['css']['font'] ) ) {
					self::set_style( $function_name, array(
						'selector'    => $option_settings['css']['font'],
						'declaration' => rtrim( $font_styles ),
						'priority'    => $this->_style_priority,
					) );
				} else {
					$style .= $font_styles;
				}
			}

			$size_option_name = "{$option_name}_{$slugs[1]}";
			$default_size     = isset( $this->fields_unprocessed[ $size_option_name ]['default'] ) ? $this->fields_unprocessed[ $size_option_name ]['default'] : '';

			if ( ! in_array( trim( $font_options[ $size_option_name ] ), array( '', 'px', $default_size ) ) ) {
				$important = in_array( 'size', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf(
					'font-size: %1$s%2$s; ',
					esc_html( et_builder_process_range_value( $font_options[ $size_option_name ] ) ),
					esc_html( $important )
				);
			}

			$text_color_option_name = "{$option_name}_{$slugs[2]}";

			if ( isset( $font_options[ $text_color_option_name ] ) && '' !== $font_options[ $text_color_option_name ] ) {
				// handle the value from old option
				$old_option_ref = isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['old_option_ref'] ) ? $option_settings['text_color']['old_option_ref'] : '';
				$old_option_val = '' !== $old_option_ref && isset( $this->shortcode_atts[ $old_option_ref ] ) ? $this->shortcode_atts[ $old_option_ref ] : '';
				$default_value = '' !== $old_option_val && isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['default'] ) ? $option_settings['text_color']['default'] : '';
				$important = ' !important';

				if ( $default_value !== $font_options[ $text_color_option_name ] ) {
					if ( isset( $option_settings['css']['color'] ) ) {
						self::set_style( $function_name, array(
							'selector'    => $option_settings['css']['color'],
							'declaration' => sprintf(
								'color: %1$s%2$s;',
								esc_html( $font_options[ $text_color_option_name ] ),
								esc_html( $important )
							),
							'priority'    => $this->_style_priority,
						) );
					} else {
						$style .= sprintf(
							'color: %1$s%2$s; ',
							esc_html( $font_options[ $text_color_option_name ] ),
							esc_html( $important )
						);
					}
				}
			}

			$letter_spacing_option_name = "{$option_name}_{$slugs[3]}";
			$default_letter_spacing     = isset( $this->fields_unprocessed[ $letter_spacing_option_name ]['default'] ) ? $this->fields_unprocessed[ $letter_spacing_option_name ]['default'] : '';

			if ( isset( $font_options[ $letter_spacing_option_name ] ) && ! in_array( trim( $font_options[ $letter_spacing_option_name ] ), array( '', 'px', $default_letter_spacing ) ) ) {
				$important = in_array( 'letter-spacing', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf(
					'letter-spacing: %1$s%2$s; ',
					esc_html( et_builder_process_range_value( $font_options[ $letter_spacing_option_name ] ) ),
					esc_html( $important )
				);

				if ( isset( $option_settings['css']['letter_spacing'] ) ) {
					self::set_style( $function_name, array(
						'selector'    => $option_settings['css']['letter_spacing'],
						'declaration' => sprintf(
							'letter-spacing: %1$s%2$s;',
							esc_html( et_builder_process_range_value( $font_options[ $letter_spacing_option_name ], 'letter_spacing' ) ),
							esc_html( $important )
						),
						'priority'    => $this->_style_priority,
					) );
				}
			}

			$line_height_option_name = "{$option_name}_{$slugs[4]}";

			if ( isset( $font_options[ $line_height_option_name ] ) ) {
				$default_line_height     = isset( $this->fields_unprocessed[ $line_height_option_name ]['default'] ) ? $this->fields_unprocessed[ $line_height_option_name ]['default'] : '';

				if ( ! in_array( trim( $font_options[ $line_height_option_name ] ), array( '', 'px', $default_line_height ) ) ) {
					$important = in_array( 'line-height', $important_options ) || $use_global_important ? ' !important' : '';

					$style .= sprintf(
						'line-height: %1$s%2$s; ',
						esc_html( et_builder_process_range_value( $font_options[ $line_height_option_name ], 'line_height' ) ),
						esc_html( $important )
					);

					if ( isset( $option_settings['css']['line_height'] ) ) {
						self::set_style( $function_name, array(
							'selector'    => $option_settings['css']['line_height'],
							'declaration' => sprintf(
								'line-height: %1$s%2$s;',
								esc_html( et_builder_process_range_value( $font_options[ $line_height_option_name ], 'line_height' ) ),
								esc_html( $important )
							),
							'priority'    => $this->_style_priority,
						) );
					}
				}
			}

			if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] && 'on' === $this->shortcode_atts["{$option_name}_all_caps"] ) {
				$important = in_array( 'all_caps', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf( 'text-transform: uppercase%1$s; ', esc_html( $important ) );
			}

			if ( '' !== $style ) {
				$css_element = ! empty( $option_settings['css']['main'] ) ? $option_settings['css']['main'] : $this->main_css_element;

				// use different selector for plugin if defined
				if ( et_is_builder_plugin_active() && ! empty( $option_settings['css']['plugin_main'] ) ) {
					$css_element = $option_settings['css']['plugin_main'];
				}

				// $css_element might be an array, for example to apply the css for placeholders
				if ( is_array( $css_element ) ) {
					foreach( $css_element as $selector ) {
						self::set_style( $function_name, array(
							'selector'    => $selector,
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );
					}
				} else {
					self::set_style( $function_name, array(
						'selector'    => $css_element,
						'declaration' => rtrim( $style ),
						'priority'    => $this->_style_priority,
					) );

					if ( $is_placeholder ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element . '::-webkit-input-placeholder',
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );

						self::set_style( $function_name, array(
							'selector'    => $css_element . '::-moz-placeholder',
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );

						self::set_style( $function_name, array(
							'selector'    => $css_element . '::-ms-input-placeholder',
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );
					}
				}
			}

			// process mobile options
			foreach( $mobile_options_slugs as $mobile_option ) {
				$current_option_name = "{$option_name}_{$mobile_option}";

				if ( isset( $font_options[ $current_option_name ] ) && '' !== $font_options[ $current_option_name ] ) {
					$current_desktop_option = $this->remove_suffix($mobile_option);
					$current_last_edited_slug = "{$option_name}_{$current_desktop_option}_last_edited";
					$current_last_edited = isset( $font_options[ $current_last_edited_slug ] ) ? $font_options[ $current_last_edited_slug ] : '';
					$current_responsive_status = et_pb_get_responsive_status( $current_last_edited );

					// Don't print mobile styles if responsive UI isn't toggled on
					if ( ! $current_responsive_status ) {
						continue;
					}

					$current_media_query = false === strpos( $mobile_option, 'phone' ) ? 'max_width_980' : 'max_width_767';
					$main_option_name = str_replace( array( '_tablet', '_phone' ), '', $mobile_option );
					$css_property = str_replace( '_', '-', $main_option_name );
					$css_option_name = 'font-size' === $css_property ? 'size' : $css_property;
					$important = in_array( $css_option_name, $important_options ) || $use_global_important ? ' !important' : '';

					// Allow specific selector tablet and mobile, simply add _tablet or _phone suffix
					if ( isset( $option_settings['css'][ $mobile_option ] ) && "" !== $option_settings['css'][ $mobile_option ] ) {
						$selector = $option_settings['css'][ $mobile_option ];
					} elseif ( isset( $option_settings['css'][ $main_option_name ] ) || isset( $option_settings['css']['main'] ) ) {
						$selector = isset( $option_settings['css'][ $main_option_name ] ) ? $option_settings['css'][ $main_option_name ] : $option_settings['css']['main'];
					} elseif ( et_is_builder_plugin_active() && ! empty( $option_settings['css']['plugin_main'] ) ) {
						$selector = $option_settings['css']['plugin_main'];
					} else {
						$selector = $this->main_css_element;
					}

					// $selector might be an array, for example to apply the css for placeholders
					if ( is_array( $selector ) ) {
						foreach( $selector as $selector_item ) {
							self::set_style( $function_name, array(
								'selector'    => $selector_item,
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );
						}
					} else {
						self::set_style( $function_name, array(
							'selector'    => $selector,
							'declaration' => sprintf(
								'%1$s: %2$s%3$s;',
								esc_html( $css_property ),
								esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
								esc_html( $important )
							),
							'priority'    => $this->_style_priority,
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );

						if ( $is_placeholder ) {
							self::set_style( $function_name, array(
								'selector'    => $selector . '::-webkit-input-placeholder',
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );

							self::set_style( $function_name, array(
								'selector'    => $selector . '::-moz-placeholder',
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );

							self::set_style( $function_name, array(
								'selector'    => $selector . '::-ms-input-placeholder',
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );
						}
					}
				}
			}
		}
	}

	function process_advanced_background_options( $function_name ) {
		if ( ! isset( $this->advanced_options['background'] ) ) {
			return;
		}

		$style = '';
		$settings = $this->advanced_options['background'];
		$important = isset( $settings['css']['important'] ) && $settings['css']['important'] ? ' !important' : '';

		$background_images = array();

		if ( $this->advanced_options['background']['use_background_color_gradient'] ) {
			$use_background_color_gradient              = $this->shortcode_atts['use_background_color_gradient'];
			$background_color_gradient_type             = $this->shortcode_atts['background_color_gradient_type'];
			$background_color_gradient_direction        = $this->shortcode_atts['background_color_gradient_direction'];
			$background_color_gradient_direction_radial = $this->shortcode_atts['background_color_gradient_direction_radial'];
			$background_color_gradient_start            = $this->shortcode_atts['background_color_gradient_start'];
			$background_color_gradient_end              = $this->shortcode_atts['background_color_gradient_end'];
			$background_color_gradient_start_position   = $this->shortcode_atts['background_color_gradient_start_position'];
			$background_color_gradient_end_position     = $this->shortcode_atts['background_color_gradient_end_position'];

			if ( 'on' === $use_background_color_gradient ) {
				$has_background_color_gradient = true;

				$background_images[] = $this->get_gradient( array(
					'type'             => $background_color_gradient_type,
					'direction'        => $background_color_gradient_direction,
					'radial_direction' => $background_color_gradient_direction_radial,
					'color_start'      => $background_color_gradient_start,
					'color_end'        => $background_color_gradient_end,
					'start_position'   => $background_color_gradient_start_position,
					'end_position'     => $background_color_gradient_end_position,
				) );
			}
		}

		if ( $this->advanced_options['background']['use_background_image'] ) {
			$background_image            = $this->shortcode_atts['background_image'];
			$background_size_default     = isset( $this->fields_unprocessed[ 'background_size' ]['default'] ) ? $this->fields_unprocessed[ 'background_size' ]['default'] : '';
			$background_size             = $this->shortcode_atts['background_size'];
			$background_position_default = isset( $this->fields_unprocessed[ 'background_position' ]['default'] ) ? $this->fields_unprocessed[ 'background_position' ]['default'] : '';
			$background_position         = $this->shortcode_atts['background_position'];
			$background_repeat_default   = isset( $this->fields_unprocessed[ 'background_repeat' ]['default'] ) ? $this->fields_unprocessed[ 'background_repeat' ]['default'] : '';
			$background_repeat           = $this->shortcode_atts['background_repeat'];
			$background_blend_default    = isset( $this->fields_unprocessed[ 'background_blend' ]['default'] ) ? $this->fields_unprocessed[ 'background_blend' ]['default'] : '';
			$background_blend            = $this->shortcode_atts['background_blend'];
			$parallax                    = $this->shortcode_atts['parallax'];

			if ( '' !== $background_image && 'on' !== $parallax ) {
				$has_background_image = true;

				$background_images[] = sprintf( 'url(%1$s)', esc_html( $background_image ) );

				if ( '' !== $background_size && $background_size_default !== $background_size ) {
					$style .= sprintf(
						'background-size: %1$s; ',
						esc_html( $background_size )
					);
				}

				if ( '' !== $background_position && $background_position_default !== $background_position ) {
					$style .= sprintf(
						'background-position: %1$s; ',
						esc_html( str_replace( '_', ' ', $background_position ) )
					);
				}

				if ( '' !== $background_repeat && $background_repeat_default !== $background_repeat ) {
					$style .= sprintf(
						'background-repeat: %1$s; ',
						esc_html( $background_repeat )
					);
				}

				if ( '' !== $background_blend && $background_blend_default !== $background_blend ) {
					$style .= sprintf(
						'background-blend-mode: %1$s; ',
						esc_html( $background_blend )
					);
				}
			}
		}

		if ( ! empty( $background_images ) ) {
			// The browsers stack the images in the opposite order to what you'd expect.
			$background_images = array_reverse( $background_images );

			$style .= sprintf(
				'background-image: %1$s%2$s;',
				esc_html( join( ', ', $background_images ) ),
				$important
			);
		}


		if ( $this->advanced_options['background']['use_background_color'] && ! isset( $has_background_color_gradient, $has_background_image ) ) {
			$background_color = $this->shortcode_atts['background_color'];

			if ( '' !== $background_color ) {
				$style .= sprintf(
					'background-color: %1$s%2$s; ',
					esc_html( $background_color ),
					esc_html( $important )
				);
			}
		} else if ( isset( $has_background_color_gradient, $has_background_image ) ) {
			// Force background-color: initial;
			$style .= sprintf( 'background-color: initial%1$s; ', esc_html( $important ) );
		}

		if ( '' !== $style ) {
			$css_element = ! empty( $settings['css']['main'] ) ? $settings['css']['main'] : $this->main_css_element;

			self::set_style( $function_name, array(
				'selector'    => $css_element,
				'declaration' => rtrim( $style ),
				'priority'    => $this->_style_priority,
			) );
		}
	}

	function process_advanced_border_options( $function_name ) {
		if ( ! isset( $this->advanced_options['border'] ) ) {
			return;
		}

		$style = '';
		$settings = $this->advanced_options['border'];

		$use_border_color = $this->shortcode_atts['use_border_color'];
		$border_style     = isset( $this->shortcode_atts['border_style'] ) && '' !== $this->shortcode_atts['border_style'] ? $this->shortcode_atts['border_style'] : 'solid';
		$border_color     =	'' !== $this->shortcode_atts['border_color'] ? $this->shortcode_atts['border_color'] : $this->fields_unprocessed['border_color']['default'];
		$border_width     = '' !== $this->shortcode_atts['border_width'] ? $this->shortcode_atts['border_width'] : $this->fields_unprocessed['border_width']['default'];
		$important        = '';

		if ( isset( $settings['css']['important'] ) ) {
			if ( 'plugin_only' === $settings['css']['important'] ) {
				$important = et_is_builder_plugin_active() ? '!important' : '';
			} else {
				$important = '!important';
			}
		}

		if ( 'on' === $use_border_color ) {
			$border_declaration_html = sprintf(
				'%1$s %3$s %2$s %4$s',
				esc_attr( et_builder_process_range_value( $border_width ) ),
				esc_attr( $border_color ),
				esc_attr( $border_style ),
				esc_attr( $important )
			);

			$style .= "border: {$border_declaration_html}; ";
		}

		if ( '' !== $style ) {
			$css_element = ! empty( $settings['css']['main'] ) ? $settings['css']['main'] : $this->main_css_element;

			self::set_style( $function_name, array(
				'selector'    => $css_element,
				'declaration' => rtrim( $style ),
				'priority'    => $this->_style_priority,
			) );

			if ( ! empty( $border_declaration_html ) && isset( $settings['additional_elements'] ) && is_array( $settings['additional_elements'] ) ) {
				foreach ( $settings['additional_elements'] as $selector => $border_type ) {
					$style = '';

					if ( ! is_array( $border_type ) ) {
						continue;
					}

					foreach ( $border_type as $direction ) {
						$style .= sprintf(
							'border-%1$s: %2$s; ',
							( 'all' !== $border_type ? esc_html( $direction ) : '' ),
							$border_declaration_html
						);
					}

					self::set_style( $function_name, array(
						'selector'    => $selector,
						'declaration' => rtrim( $style ),
						'priority'    => $this->_style_priority,
					) );
				}
			}
		}
	}

	function process_advanced_custom_margin_options( $function_name ) {
		if ( ! isset( $this->advanced_options['custom_margin_padding'] ) ) {
			return;
		}

		$style = '';
		$style_mobile = array();
		$important_options = array();
		$is_important_set = isset( $this->advanced_options['custom_margin_padding']['css']['important'] );
		$use_global_important = $is_important_set && 'all' === $this->advanced_options['custom_margin_padding']['css']['important'];
		if ( $is_important_set && is_array( $this->advanced_options['custom_margin_padding']['css']['important'] ) ) {
			$important_options = $this->advanced_options['custom_margin_padding']['css']['important'];
		}

		$custom_margin  = $this->advanced_options['custom_margin_padding']['use_margin'] ? $this->shortcode_atts['custom_margin'] : '';
		$custom_padding = $this->advanced_options['custom_margin_padding']['use_padding'] ? $this->shortcode_atts['custom_padding'] : '';

		$custom_margin_responsive_active = isset( $this->shortcode_atts['custom_margin_last_edited'] ) ? et_pb_get_responsive_status( $this->shortcode_atts['custom_margin_last_edited'] ) : false;
		$custom_margin_mobile = $custom_margin_responsive_active && $this->advanced_options['custom_margin_padding']['use_margin'] && ( isset( $this->shortcode_atts['custom_margin_tablet'] ) || isset( $this->shortcode_atts['custom_margin_phone'] ) )
			? array (
				'tablet' => isset( $this->shortcode_atts['custom_margin_tablet'] ) ? $this->shortcode_atts['custom_margin_tablet'] : '',
				'phone' => isset( $this->shortcode_atts['custom_margin_phone'] ) ? $this->shortcode_atts['custom_margin_phone'] : '',
			)
			: '';

		$custom_padding_responsive_active = isset( $this->shortcode_atts['custom_padding_last_edited'] ) ? et_pb_get_responsive_status( $this->shortcode_atts['custom_padding_last_edited'] ) : false;
		$custom_padding_mobile = $custom_padding_responsive_active && $this->advanced_options['custom_margin_padding']['use_padding'] && ( isset( $this->shortcode_atts['custom_padding_tablet'] ) || isset( $this->shortcode_atts['custom_padding_phone'] ) )
			? array (
				'tablet' => isset( $this->shortcode_atts['custom_padding_tablet'] ) ? $this->shortcode_atts['custom_padding_tablet'] : '',
				'phone' => isset( $this->shortcode_atts['custom_padding_phone'] ) ? $this->shortcode_atts['custom_padding_phone'] : '',
			)
			: '';

		if ( '' !== $custom_padding || ! empty( $custom_padding_mobile ) ) {
			$important = in_array( 'custom_padding', $important_options ) || $use_global_important ? true : false;
			$style .= '' !== $custom_padding ? et_builder_get_element_style_css( $custom_padding, 'padding', $important ) : '';

			if ( ! empty( $custom_padding_mobile ) ) {
				foreach ( $custom_padding_mobile as $device => $settings ) {
					$style_mobile[ $device ][] = '' !== $settings ? et_builder_get_element_style_css( $settings, 'padding', $important ) : '';
				}
			}
		}

		if ( '' !== $custom_margin || ! empty( $custom_margin_mobile ) ) {
			$important = in_array( 'custom_margin', $important_options ) || $use_global_important ? true : false;
			$style .= '' !== $custom_margin ? et_builder_get_element_style_css( $custom_margin, 'margin', $important ) : '';

			if ( ! empty( $custom_margin_mobile ) ) {
				foreach ( $custom_margin_mobile as $device => $settings ) {
					$style_mobile[ $device ][] = '' !== $settings ? et_builder_get_element_style_css( $settings, 'margin', $important ) : '';
				}
			}
		}

		if ( '' !== $style ) {
			$css_element = ! empty( $this->advanced_options['custom_margin_padding']['css']['main'] ) ? $this->advanced_options['custom_margin_padding']['css']['main'] : $this->main_css_element;

			self::set_style( $function_name, array(
				'selector'    => $css_element,
				'declaration' => rtrim( $style ),
				'priority'    => $this->_style_priority,
			) );
		}

		if ( ! empty( $style_mobile ) ) {
			$css_element = ! empty( $this->advanced_options['custom_margin_padding']['css']['main'] ) ? $this->advanced_options['custom_margin_padding']['css']['main'] : $this->main_css_element;

			foreach( $style_mobile as $device => $style ) {
				if ( ! empty( $style ) ) {
					$current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
					$current_media_css = '';
					foreach( $style as $css_code ) {
						$current_media_css .= $css_code;
					}
					if ( '' === $current_media_css ) {
						continue;
					}

					self::set_style( $function_name, array(
						'selector'    => $css_element,
						'declaration' => rtrim( $current_media_css ),
						'priority'    => $this->_style_priority,
						'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
					) );
				}
			}
		}
	}

	function process_advanced_button_options( $function_name ) {
		if ( ! isset( $this->advanced_options['button'] ) ) {
			return;
		}

		foreach ( $this->advanced_options['button'] as $option_name => $option_settings ) {
			$button_custom                      = $this->shortcode_atts["custom_{$option_name}"];
			$button_text_size                   = $this->shortcode_atts["{$option_name}_text_size"];
			$button_text_size_tablet            = $this->shortcode_atts["{$option_name}_text_size_tablet"];
			$button_text_size_phone             = $this->shortcode_atts["{$option_name}_text_size_phone"];
			$button_text_size_last_edited       = $this->shortcode_atts["{$option_name}_text_size_last_edited"];
			$button_text_color                  = $this->shortcode_atts["{$option_name}_text_color"];
			$button_bg_color                    = $this->shortcode_atts["{$option_name}_bg_color"];
			$button_border_width                = $this->shortcode_atts["{$option_name}_border_width"];
			$button_border_color                = $this->shortcode_atts["{$option_name}_border_color"];
			$button_border_radius               = $this->shortcode_atts["{$option_name}_border_radius"];
			$button_font                        = $this->shortcode_atts["{$option_name}_font"];
			$button_letter_spacing              = $this->shortcode_atts["{$option_name}_letter_spacing"];
			$button_letter_spacing_tablet       = $this->shortcode_atts["{$option_name}_letter_spacing_tablet"];
			$button_letter_spacing_phone        = $this->shortcode_atts["{$option_name}_letter_spacing_phone"];
			$button_letter_spacing_last_edited  = $this->shortcode_atts["{$option_name}_letter_spacing_last_edited"];
			$button_use_icon                    = $this->shortcode_atts["{$option_name}_use_icon"];
			$button_icon                        = $this->shortcode_atts["{$option_name}_icon"];
			$button_icon_color                  = $this->shortcode_atts["{$option_name}_icon_color"];
			$button_icon_placement              = $this->shortcode_atts["{$option_name}_icon_placement"];
			$button_on_hover                    = $this->shortcode_atts["{$option_name}_on_hover"];
			$button_text_color_hover            = $this->shortcode_atts["{$option_name}_text_color_hover"];
			$button_bg_color_hover              = $this->shortcode_atts["{$option_name}_bg_color_hover"];
			$button_border_color_hover          = $this->shortcode_atts["{$option_name}_border_color_hover"];
			$button_border_radius_hover         = $this->shortcode_atts["{$option_name}_border_radius_hover"];
			$button_letter_spacing_hover        = $this->shortcode_atts["{$option_name}_letter_spacing_hover"];
			$button_letter_spacing_hover_tablet = $this->shortcode_atts["{$option_name}_letter_spacing_hover_tablet"];
			$button_letter_spacing_hover_phone  = $this->shortcode_atts["{$option_name}_letter_spacing_hover_phone"];
			$button_letter_spacing_hover_last_edited  = $this->shortcode_atts["{$option_name}_letter_spacing_hover_last_edited"];

			$button_icon_pseudo_selector = $button_icon_placement === 'left' ? ':before' : ':after';

			if ( 'on' === $button_custom ) {
				$button_text_size = '' === $button_text_size || 'px' === $button_text_size ? '20px' : $button_text_size;
				$button_text_size = '' !== $button_text_size && false === strpos( $button_text_size, 'px' ) ? $button_text_size . 'px' : $button_text_size;
				$button_border_radius_processed = '' !== $button_border_radius && 'px' !== $button_border_radius ? et_builder_process_range_value( $button_border_radius ) : '';
				$button_border_radius_hover_processed = '' !== $button_border_radius_hover && 'px' !== $button_border_radius_hover ? et_builder_process_range_value( $button_border_radius_hover ) : '';
				$button_use_icon = '' === $button_use_icon ? 'default' : $button_use_icon;

				$css_element = ! empty( $option_settings['css']['main'] ) ? $option_settings['css']['main'] : $this->main_css_element . ' .et_pb_button';

				if ( et_is_builder_plugin_active() && ! empty( $option_settings['css']['plugin_main'] ) ) {
					$css_element = $option_settings['css']['plugin_main'];
				}

				$css_element_processed = et_is_builder_plugin_active() ? $css_element : 'body #page-container ' . $css_element;

				if ( et_is_builder_plugin_active() ) {
					$button_bg_color .= '' !== $button_bg_color ? ' !important' : '';
					$button_border_radius_processed .= '' !== $button_border_radius_processed ? ' !important' : '';
					$button_border_radius_hover_processed .= '' !== $button_border_radius_hover_processed ? ' !important' : '';
				}

				$main_element_styles_padding_important = 'no' === et_builder_option( 'all_buttons_icon' ) && 'off' !== $button_use_icon;

				$main_element_styles = sprintf(
					'%1$s
					%2$s
					%3$s
					%4$s
					%5$s
					%6$s
					%7$s
					%8$s
					%9$s',
					'' !== $button_text_color ? sprintf( 'color:%1$s !important;', $button_text_color ) : '',
					'' !== $button_bg_color ? sprintf( 'background:%1$s;', $button_bg_color ) : '',
					'' !== $button_border_width && 'px' !== $button_border_width ? sprintf( 'border-width:%1$s !important;', et_builder_process_range_value( $button_border_width ) ) : '',
					'' !== $button_border_color ? sprintf( 'border-color:%1$s;', $button_border_color ) : '',
					'' !== $button_border_radius_processed ? sprintf( 'border-radius:%1$s;', $button_border_radius_processed ) : '',
					'' !== $button_letter_spacing && 'px' !== $button_letter_spacing ? sprintf( 'letter-spacing:%1$s;', et_builder_process_range_value( $button_letter_spacing ) ) : '',
					'' !== $button_text_size && 'px' !== $button_text_size ? sprintf( 'font-size:%1$s;', et_builder_process_range_value( $button_text_size ) ) : '',
					'' !== $button_font ? et_builder_set_element_font( $button_font, true ) : '',
					'off' === $button_on_hover ?
						sprintf( 'padding-left:%1$s%3$s; padding-right: %2$s%3$s;',
							'left' === $button_icon_placement ? '2em' : '0.7em',
							'left' === $button_icon_placement ? '0.7em' : '2em',
							$main_element_styles_padding_important ? ' !important' : ''
						)
						: ''
				);

				self::set_style( $function_name, array(
					'selector'    => $css_element_processed,
					'declaration' => rtrim( $main_element_styles ),
				) );

				$main_element_styles_hover = sprintf(
					'%1$s
					%2$s
					%3$s
					%4$s
					%5$s
					%6$s',
					'' !== $button_text_color_hover ? sprintf( 'color:%1$s !important;', $button_text_color_hover ) : '',
					'' !== $button_bg_color_hover ? sprintf( 'background:%1$s !important;', $button_bg_color_hover ) : '',
					'' !== $button_border_color_hover ? sprintf( 'border-color:%1$s !important;', $button_border_color_hover ) : '',
					'' !== $button_border_radius_hover_processed ? sprintf( 'border-radius:%1$s;', $button_border_radius_hover_processed ) : '',
					'' !== $button_letter_spacing_hover && 'px' !== $button_letter_spacing_hover ? sprintf( 'letter-spacing:%1$s;', et_builder_process_range_value( $button_letter_spacing_hover ) ) : '',
					'off' === $button_on_hover ?
						''
						:
						sprintf( 'padding-left:%1$s%3$s; padding-right: %2$s%3$s;',
							'left' === $button_icon_placement ? '2em' : '0.7em',
							'left' === $button_icon_placement ? '0.7em' : '2em',
							$main_element_styles_padding_important ? ' !important' : ''
						)
				);

				self::set_style( $function_name, array(
					'selector'    => $css_element_processed . ':hover',
					'declaration' => rtrim( $main_element_styles_hover ),
				) );

				if ( 'off' === $button_use_icon ) {
					$main_element_styles_after = 'display:none !important;';
					$no_icon_styles = 'padding: 0.3em 1em !important;';

					$selector = sprintf( '%1$s:before, %1$s:after', $css_element_processed );

					self::set_style( $function_name, array(
						'selector'    => $css_element . ',' . $css_element . ':hover',
						'declaration' => rtrim( $no_icon_styles ),
					) );
				} else {
					$button_icon_code = '' !== $button_icon ? str_replace( ';', '', str_replace( '&#x', '', html_entity_decode( et_pb_process_font_icon( $button_icon ) ) ) ) : '';
					$int_font_size = intval( str_replace( 'px', '', $button_text_size ) );
					if ( '' !== $button_text_size ) {
						$button_icon_size = '35' !== $button_icon_code ? $button_text_size : ( $int_font_size * 1.6 ) . 'px';
					}

					$main_element_styles_after = sprintf(
						'%1$s
						%2$s
						%3$s
						%4$s
						%5$s
						%6$s
						%7$s',
						'' !== $button_icon_color ? sprintf( 'color:%1$s;', $button_icon_color ) : '',
						'' !== $button_icon_code ?
							sprintf( 'line-height:%1$s;', '35' !== $button_icon_code ? '1.7em' : '1em' )
							: '',
						'' !== $button_icon_code ? sprintf( 'font-size:%1$s !important;', $button_icon_size ) : '',
						sprintf( 'opacity:%1$s;', 'off' !== $button_on_hover ? '0' : '1' ),
						'off' !== $button_on_hover && '' !== $button_icon_code ?
							sprintf( 'margin-left: %1$s; %2$s: auto;',
								'left' === $button_icon_placement ? '-1.3em' : '-1em',
								'left' === $button_icon_placement ? 'right' : 'left'
							)
							: '',
						'off' === $button_on_hover ?
							sprintf( 'margin-left: %1$s; %2$s:auto;',
								'left' === $button_icon_placement ? '-1.3em' : '.3em',
								'left' === $button_icon_placement ? 'right' : 'left'
							)
							: '',
						( in_array( $button_use_icon , array( 'default', 'on' ) ) ? 'display: inline-block;' : '' )
					);

					// Reverse icon position
					if ( 'left' === $button_icon_placement ) {
						$button_icon_left_content = '' !== $button_icon_code ? 'content: attr(data-icon);' : '';

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':after',
							'declaration' => 'display: none;',
						) );

						if ( et_is_builder_plugin_active() ) {
							self::set_style( $function_name, array(
								'selector'    => '.et_pb_row ' . $css_element_processed . ':hover',
								'declaration' => 'padding-right: 1em; padding-left: 2em;',
							) );
						}

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':before',
							'declaration' => $button_icon_left_content . ' ; font-family: "ETmodules" !important;',
						) );
					}

					$hover_after_styles = sprintf(
						'%1$s
						%2$s
						%3$s',
						'' !== $button_icon_code ?
							sprintf( 'margin-left:%1$s;', '35' !== $button_icon_code ? '.3em' : '0' )
							: '',
							'' !== $button_icon_code ?
								sprintf( '%1$s: auto; margin-left: %2$s;',
									'left' === $button_icon_placement ? 'right' : 'left',
									'left' === $button_icon_placement ? '-1.3em' : '.3em'
								)
							: '',
						'off' !== $button_on_hover ? 'opacity: 1;' : ''
					);

					self::set_style( $function_name, array(
						'selector'    => $css_element_processed . ':hover' . $button_icon_pseudo_selector,
						'declaration' => rtrim( $hover_after_styles ),
					) );

					if ( '' === $button_icon ) {
						$default_icons_size = $int_font_size * 1.6 . 'px';
						$custom_icon_size = $button_text_size;

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . $button_icon_pseudo_selector,
							'declaration' => sprintf( 'font-size:%1$s;', $default_icons_size ),
						) );

						self::set_style( $function_name, array(
							'selector'    => 'body.et_button_custom_icon #page-container ' . $css_element . $button_icon_pseudo_selector,
							'declaration' => sprintf( 'font-size:%1$s;', $custom_icon_size ),
						) );
					}

					$selector = $css_element_processed . $button_icon_pseudo_selector;
				}

				foreach( array( 'tablet', 'phone' ) as $device ) {
					$current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
					$current_text_size = 'tablet' === $device ? et_builder_process_range_value( $button_text_size_tablet ) : et_builder_process_range_value( $button_text_size_phone );
					$current_letter_spacing = 'tablet' === $device ? et_builder_process_range_value( $button_letter_spacing_tablet ) : et_builder_process_range_value( $button_letter_spacing_phone );
					$current_letter_spacing_hover = 'tablet' === $device ? et_builder_process_range_value( $button_letter_spacing_hover_tablet ) : et_builder_process_range_value( $button_letter_spacing_hover_phone );

					$current_text_size_responsive_active = et_pb_get_responsive_status( $button_text_size_last_edited );
					$current_letter_spacing_responsive_active = et_pb_get_responsive_status( $button_letter_spacing_last_edited );
					$current_letter_spacing_hover_responsive_active = et_pb_get_responsive_status( $button_letter_spacing_hover_last_edited );

					if ( ( '' !== $current_text_size && '0px' !== $current_text_size ) || '' !== $current_letter_spacing ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ',' . $css_element_processed . $button_icon_pseudo_selector,
							'declaration' => sprintf(
								'%1$s
								%2$s',
								$current_text_size_responsive_active && '' !== $current_text_size && '0px' !== $current_text_size ? sprintf( 'font-size:%1$s !important;', $current_text_size ) : '',
								$current_letter_spacing_responsive_active && '' !== $current_letter_spacing ? sprintf( 'letter-spacing:%1$s;', $current_letter_spacing ) : ''
							),
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );
					}

					if ( $current_letter_spacing_hover_responsive_active && '' !== $current_letter_spacing_hover ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':hover',
							'declaration' => sprintf(
								'letter-spacing:%1$s;',
								$current_letter_spacing_hover
							),
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );
					}
				}

				self::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => rtrim( $main_element_styles_after ),
				) );
			}
		}
	}

	function process_custom_css_options( $function_name ) {
		if ( empty( $this->custom_css_options ) ) {
			return false;
		}

		foreach ( $this->custom_css_options as $slug => $option ) {
			$css      = $this->shortcode_atts["custom_css_{$slug}"];
			$order_class = isset( $this->main_css_element ) && count( explode( ' ', $this->main_css_element ) ) === 1 ? $selector = $this->main_css_element : '%%order_class%%';
			$selector = ! empty( $option['selector'] ) ? $option['selector'] : '';

			if ( false === strpos( $selector, '%%order_class%%' ) ) {
				if ( ! ( isset( $option['no_space_before_selector'] ) && $option['no_space_before_selector'] ) && '' !== $selector ) {
					$selector = " {$selector}";
				}

				$selector = "{$order_class}{$selector}";
			}

			if ( '' !== $css ) {
				self::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => trim( $css ),
				) );
			}
		}
	}

	function make_options_filterable() {
		if ( isset( $this->advanced_options ) ) {
			$this->advanced_options = apply_filters(
				"{$this->slug}_advanced_options",
				$this->advanced_options,
				$this->slug,
				$this->main_css_element
			);
		}

		if ( isset( $this->custom_css_options ) ) {
			$this->custom_css_options = apply_filters(
				"{$this->slug}_custom_css_options",
				$this->custom_css_options,
				$this->slug,
				$this->main_css_element
			);
		}

	}

	function disable_wptexturize( $shortcodes ) {
		$shortcodes[] = $this->slug;

		return $shortcodes;
	}

	static function compare_by_priority( $a, $b ) {
		$a_priority = ! empty( $a['priority'] ) ? (int) $a['priority'] : self::DEFAULT_PRIORITY;
		$b_priority = ! empty( $b['priority'] ) ? (int) $b['priority'] : self::DEFAULT_PRIORITY;

		if ( isset( $a['_order_number'], $b['_order_number'] ) && ( $a_priority === $b_priority ) ) {
			return $a['_order_number'] - $b['_order_number'];
		}

		return $a_priority - $b_priority;
	}
	/*
	 * Reorder toggles based on the priority with respect to manually ordered items with no priority
	 *
	 */
	static function et_pb_order_toggles_by_priority( $toggles_array ) {
		if ( empty( $toggles_array ) ) {
			return array();
		}

		$high_priority_toggles = array();
		$low_priority_toggles = array();
		$manually_ordered_toggles = array();

		// fill 3 arrays based on priority
		foreach ( $toggles_array as $toggle_id => $toggle_data ) {
			if ( isset( $toggle_data['priority'] ) ) {
				if ( $toggle_data['priority'] < 10 ) {
					$high_priority_toggles[ $toggle_id ] = $toggle_data;
				} else {
					$low_priority_toggles[ $toggle_id ] = $toggle_data;
				}
			} else {
				// keep the original order of options without priority defined
				$manually_ordered_toggles[ $toggle_id ] = $toggle_data;
			}
		}

		// order high and low priority toggles
		uasort( $high_priority_toggles, array( 'self', 'compare_by_priority' ) );
		uasort( $low_priority_toggles, array( 'self', 'compare_by_priority' ) );

		// merge 3 arrays to get the correct order of toggles.
		return array_merge( $high_priority_toggles, $manually_ordered_toggles, $low_priority_toggles );
	}

	static function compare_by_name( $a, $b ) {
		return strcasecmp( $a->name, $b->name );
	}

	static function get_modules_count( $post_type ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules = self::get_child_modules( $post_type );
		$overall_count = count( $parent_modules ) + count( $child_modules );

		return $overall_count;
	}

	static function get_modules_js_array( $post_type ) {
		$modules = array();

		$parent_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $parent_modules ) ) {
			/**
			 * Sort modules alphabetically by name.
			 */
			$sorted_modules = $parent_modules;

			uasort( $sorted_modules, array( 'self', 'compare_by_name' ) );

			foreach( $sorted_modules as $module ) {
				/**
				 * Replace single and double quotes with %% and || respectively
				 * to avoid js conflicts
				 */
				$module_name = str_replace( array( '"', '&quot;', '&#34;', '&#034;' ) , '%%', $module->name );
				$module_name = str_replace( array( "'", '&#039;', '&#39;' ) , '||', $module_name );

				$modules[] = sprintf(
					'{ "title" : "%1$s", "label" : "%2$s"%3$s}',
					esc_js( $module_name ),
					esc_js( $module->slug ),
					( isset( $module->fullwidth ) && $module->fullwidth ? ', "fullwidth_only" : "on"' : '' )
				);
			}
		}

		return '[' . implode( ',', $modules ) . ']';
	}

	static function get_shortcodes_with_children( $post_type ) {
		$shortcodes = array();
		if ( ! empty( self::$parent_modules[ $post_type ] ) ) {
			foreach( self::$parent_modules[ $post_type ] as $module ) {
				if ( ! empty( $module->child_slug ) ) {
					$shortcodes[] = sprintf(
						'"%1$s":"%2$s"',
						esc_js( $module->slug ),
						esc_js( $module->child_slug )
					);
				}
			}
		}

		return '{' . implode( ',', $shortcodes ) . '}';
	}

	static function get_modules_array( $post_type = '', $include_child = false, $fb_ignore_unsupported = false ) {
		$modules = array();

		if ( ! empty( $post_type ) ) {
			$parent_modules = self::get_parent_modules( $post_type );

			if ( $include_child ) {
				$parent_modules = array_merge( $parent_modules, self::get_child_modules( $post_type ));
			}

			if ( ! empty( $parent_modules ) ) {
				$sorted_modules = $parent_modules;
			}
		} else {
			$parent_modules = self::get_parent_modules();

			if ( $include_child ) {
				$parent_modules = array_merge( $parent_modules, self::get_child_modules());
			}

			if ( ! empty( $parent_modules ) ) {

				$all_modules = array();
				foreach( $parent_modules as $post_type => $post_type_modules ) {
					foreach ( $post_type_modules as $module_slug => $module ) {
						$all_modules[ $module_slug ] = $module;
					}
				}

				$sorted_modules = $all_modules;
			}
		}

		if ( ! empty( $sorted_modules ) ) {
			/**
			 * Sort modules alphabetically by name.
			 */
			uasort( $sorted_modules, array( 'self', 'compare_by_name' ) );

			foreach( $sorted_modules as $module ) {
				if ( $fb_ignore_unsupported && ( ! isset( $module->fb_support ) || ! $module->fb_support ) ) {
					continue;
				}

				/**
				 * Replace single and double quotes with %% and || respectively
				 * to avoid js conflicts
				 */
				$module_name = str_replace( '"', '%%', $module->name );
				$module_name = str_replace( "'", '||', $module_name );

				$_module = array(
					'title' => esc_attr( $module_name ),
					'label' => esc_attr( $module->slug ),
					'is_parent' => $module->type === 'child' ? 'off' : 'on',
					'fb_support' => isset( $module->fb_support ) && $module->fb_support ? 'on' : 'off',
				);

				if ( isset( $module->fullwidth ) && $module->fullwidth ) {
					$_module['fullwidth_only'] = 'on';
				}

				$modules[] = $_module;
			}
		}

		return $modules;
	}

	static function get_fb_unsupported_modules() {
		$parent_modules = self::get_parent_modules();
		$unsupported_modules_array = array();

		foreach( $parent_modules as $post_type => $post_type_modules ) {
			foreach ( $post_type_modules as $module_slug => $module ) {
				if ( ! isset( $module->fb_support ) || ! $module->fb_support ) {
					$unsupported_modules_array[] = $module_slug;
				}
			}
		}

		return array_unique( $unsupported_modules_array );
	}

	static function get_parent_shortcodes( $post_type ) {
		$shortcodes = array();
		$parent_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $parent_modules ) ) {
			foreach( $parent_modules as $module ) {
				$shortcodes[] = $module->slug;
			}
		}

		return implode( '|', $shortcodes );
	}

	static function get_child_shortcodes( $post_type ) {
		$shortcodes = array();
		$child_modules = self::get_child_modules( $post_type );
		if ( ! empty( $child_modules ) ) {
			foreach( $child_modules as $module ) {
				if ( ! empty( $module->slug ) ) {
					$shortcodes[] = $module->slug;
				}
			}
		}

		return implode( '|', $shortcodes );
	}

	static function get_child_slugs( $post_type ) {
		$child_slugs = array();
		$child_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $child_modules ) ) {
			foreach( $child_modules as $module ) {
				if ( ! empty( $module->child_slug ) ) {
					$child_slugs[ $module->slug ] = $module->child_slug;
				}
			}
		}

		return $child_slugs;
	}

	static function get_raw_content_shortcodes( $post_type ) {
		$shortcodes = array();

		$parent_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $parent_modules ) ) {
			foreach( $parent_modules as $module ) {
				if ( isset( $module->use_row_content ) && $module->use_row_content ) {
					$shortcodes[] = $module->slug;
				}
			}
		}

		$child_modules = self::get_child_modules( $post_type );
		if ( ! empty( $child_modules ) ) {
			foreach( $child_modules as $module ) {
				if ( isset( $module->use_row_content ) && $module->use_row_content ) {
					$shortcodes[] = $module->slug;
				}
			}
		}

		return implode( '|', $shortcodes );
	}

	static function get_modules_templates( $post_type, $slugs_array ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules = self::get_child_modules( $post_type );
		$all_modules = array_merge( $parent_modules, $child_modules );
		$templates_array = array();

		if ( empty( $slugs_array ) ) {
			return;
		}

		foreach ( $slugs_array as $slug ) {
			if ( ! isset( $all_modules[ $slug ] ) ) {
				return '';
			}

			$module = $all_modules[ $slug ];

			$templates_array[] = array(
				'slug'     => $slug,
				'template' => $module->build_microtemplate(),
			);
		}

		return $templates_array;
	}

	static function output_templates( $post_type = '', $start_from = 0, $amount = 999 ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules = self::get_child_modules( $post_type );
		$all_modules = array_merge( $parent_modules, $child_modules );

		$modules_names = array_keys( $all_modules );

		$output = array();
		$output['templates'] = array();

		if ( ! empty( $all_modules ) ) {
			for ( $i = 0; $i < ET_BUILDER_AJAX_TEMPLATES_AMOUNT; $i++ ) {
				if ( isset( $modules_names[ $i ] ) ) {
					$module = $all_modules[ $modules_names[ $i ] ];
					$output['templates'][ $module->slug ] = $module->build_microtemplate();
				} else {
					break;
				}
			}
		}

		return $output;
	}

	static function get_structure_module_slugs() {

		if ( ! empty( self::$structure_module_slugs ) ) {
			return self::$structure_module_slugs;
		}

		$structure_modules = self::get_structure_modules();
		self::$structure_module_slugs = array();
		foreach( $structure_modules as $structural_module ) {
			self::$structure_module_slugs[] = $structural_module->slug;
		}

		return self::$structure_module_slugs;
	}

	static function get_structure_modules() {
		if ( ! empty( self::$structure_modules ) ) {
			return self::$structure_modules;
		}

		$parent_modules = self::get_parent_modules( 'et_pb_layout' );
		self::$structure_modules = array();
		foreach ( $parent_modules as $parent_module ) {
			if ( isset( $parent_module->is_structure_element ) && $parent_module->is_structure_element ) {
				self::$structure_modules[] = $parent_module;
			}
		}

		return self::$structure_modules;
	}

	static function get_parent_modules( $post_type = '' ) {
		if ( ! empty( $post_type ) ) {
			$parent_modules = ! empty( self::$parent_modules[ $post_type ] ) ? self::$parent_modules[ $post_type ] : array();
		} else {
			$parent_modules = self::$parent_modules;
		}

		return apply_filters( 'et_builder_get_parent_modules', $parent_modules, $post_type );
	}

	static function get_child_modules( $post_type = '' ) {
		if ( ! empty( $post_type ) ) {
			$child_modules = ! empty( self::$child_modules[ $post_type ] ) ? self::$child_modules[ $post_type ] : array();
		} else {
			$child_modules = self::$child_modules;
		}

		return apply_filters( 'et_builder_get_child_modules', $child_modules, $post_type );
	}

	static function get_fields_defaults( $post_type = '', $mode = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields_defaults = array();
		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$module_fields_defaults[ $_module_slug ] = isset( $_module->fields_defaults ) ? $_module->fields_defaults : array();
		}

		return $module_fields_defaults;
	}

	static function get_defaults( $post_type = '', $mode = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_defaults = array();
		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$module_defaults[ $_module_slug ] = isset( $_module->defaults ) ? $_module->defaults : array();
		}

		return $module_defaults;
	}

	static function get_toggles( $post_type ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );
		$toggles_array = array();

		$_modules = array_merge( $parent_modules, $child_modules );

		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$toggles_array[ $_module_slug ] = isset( $_module->options_toggles ) ? $_module->options_toggles : array();

			foreach( array( 'general', 'advanced', 'custom_css' ) as $tab ) {
				// sort toggles by priority
				if ( isset( $toggles_array[ $_module_slug ][ $tab ]['toggles'] ) ) {
					$toggles_array[ $_module_slug ][ $tab ]['toggles'] = self::et_pb_order_toggles_by_priority( $toggles_array[ $_module_slug ][ $tab ]['toggles'] );
				}
			}
		}

		return $toggles_array;
	}

	static function get_general_fields( $post_type = '', $mode = 'all', $module_type = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields = array();

		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			// filter modules by slug if needed
			if ( 'all' !== $module_type && $module_type !== $_module_slug ) {
				continue;
			}

			$dependables = array();

			foreach ( $_module->fields_unprocessed as $field_key => $field ) {
				if ( isset( $field['affects'] ) ) {
					$dependables[ $field_key ] = $field['affects'];
				}

				if ( isset( $field['tab_slug'] ) && 'general' !== $field['tab_slug'] ) {
					continue;
				}

				$field['name'] = $field_key;
				$module_fields[ $_module_slug ][ $field_key ] = $field;
			}

			if ( ! empty( $dependables ) ) {
				foreach ( $dependables as $dependable_field => $affects ) {
					foreach ( $affects as $affect ) {
						// Avoid pushing depends_to attributte to non-existent module field data
						if ( ! isset( $module_fields[ $_module_slug ][ $affect ] ) ) {
							continue;
						}

						$module_fields[ $_module_slug ][ $affect ]['depends_to'][] = $dependable_field;
					}
				}
			}
		}

		if ( 'all' !== $module_type ) {
			return $module_fields[ $module_type ];
		}

		return $module_fields;
	}

	static function get_advanced_fields( $post_type = '', $mode = 'all', $module_type = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields = array();

		foreach( $_modules as $_module_slug => $_module ) {
			// filter modules by slug if needed
			if ( 'all' !== $module_type && $module_type !== $_module_slug ) {
				continue;
			}

			$dependables = array();

			foreach ( $_module->fields_unprocessed as $field_key => $field ) {
				// do not add the fields with 'skip' type. These fields used for rendering shortcode on Front End only
				if ( isset( $field['type'] ) && 'skip' === $field['type'] ) {
					continue;
				}

				if ( isset( $field['affects'] ) ) {
					$dependables[ $field_key ] = $field['affects'];
				}

				if ( ! isset( $field['tab_slug'] ) || 'advanced' !== $field['tab_slug'] ) {
					continue;
				}

				if ( isset( $field['default'] ) ) {
					$module_fields[ $_module_slug ]['advanced_defaults'][ $field_key ] = $field['default'];
				}

				$field['name'] = $field_key;
				$module_fields[ $_module_slug ][ $field_key ] = $field;
			}

			if ( ! empty( $dependables ) ) {
				foreach ( $dependables as $dependable_field => $affects ) {
					foreach ( $affects as $affect ) {
						if ( isset( $module_fields[ $_module_slug ][ $affect ] ) ) {
							$module_fields[ $_module_slug ][ $affect ]['depends_to'][] = $dependable_field;
						}
					}
				}
			}

			if ( ! empty( $_module->advanced_options ) ) {
				$module_fields[ $_module_slug ]['advanced_common'] = $_module->advanced_options;
			}
		}

		if ( 'all' !== $module_type ) {
			return $module_fields[ $module_type ];
		}

		return $module_fields;
	}

	static function get_custom_css_fields( $post_type = '', $mode = 'all', $module_type = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields = array();

		$custom_css_unwanted_types = array( 'custom_css', 'column_settings_css', 'column_settings_css_fields', 'column_settings_custom_css' );
		foreach( $_modules as $_module_slug => $_module ) {
			// filter modules by slug if needed
			if ( 'all' !== $module_type && $module_type !== $_module_slug ) {
				continue;
			}

			$dependables = array();

			$module_fields[ $_module_slug ] = $_module->custom_css_options;

			// Automatically added module ID and module class fields to setting modal's CSS tab
			if ( ! empty( $_module->fields_unprocessed ) ) {
				foreach ( $_module->fields_unprocessed as $field_unprocessed_key => $field_unprocessed ) {
					if ( isset( $field_unprocessed['tab_slug'] ) && 'custom_css' === $field_unprocessed['tab_slug'] &&
						 isset( $field_unprocessed['type'] ) && ! in_array( $field_unprocessed['type'], $custom_css_unwanted_types ) ) {
						$module_fields[ $_module_slug ][ $field_unprocessed_key ] = $field_unprocessed;

						if ( isset( $field_unprocessed['affects'] ) ) {
							$dependables[ $field_unprocessed_key ] = $field_unprocessed['affects'];
						}
					}
				}

				if ( ! empty( $dependables ) ) {
					foreach ( $dependables as $dependable_field => $affects ) {
						foreach ( $affects as $affect ) {
							if ( isset( $module_fields[ $_module_slug ][ $affect ] ) ) {
								$module_fields[ $_module_slug ][ $affect ]['depends_to'][] = $dependable_field;
							}
						}
					}
				}
			}
		}

		if ( 'all' !== $module_type ) {
			return $module_fields[ $module_type ];
		}

		return $module_fields;
	}

	static function get_module_fields( $post_type, $module ) {
		$_modules = array_merge( self::get_parent_modules( $post_type ), self::get_child_modules( $post_type ) );

		if ( ! empty( $_modules[ $module ] ) ) {
			return $_modules[ $module ]->fields_unprocessed;
		}
		return false;
	}

	static function get_parent_module_fields( $post_type, $module ) {
		if ( ! empty( self::$parent_modules[ $post_type ][ $module ] ) ) {
			return self::$parent_modules[ $post_type ][ $module ]->get_fields();
		}
		return false;
	}

	static function get_child_module_fields( $post_type, $module ) {
		if ( ! empty( self::$child_modules[ $post_type ][ $module ] ) ) {
			return self::$child_modules[ $post_type ][ $module ]->get_fields();
		}
		return false;
	}

	static function get_parent_module_field( $post_type, $module, $field ) {
		$fields = self::get_parent_module_fields( $post_type, $module );
		if ( ! empty( $fields[ $field ] ) ) {
			return $fields[ $field ];
		}
		return false;
	}

	static function get_font_icon_fields( $post_type = '' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );
		$_modules       = array_merge( $parent_modules, $child_modules );
		$module_fields  = array();

		foreach ( $_modules as $module_name => $module ) {
			foreach ($module->fields_unprocessed as $module_field_name => $module_field) {
				if ( isset( $module_field['renderer'] ) && 'et_pb_get_font_icon_list' === $module_field['renderer'] ) {
					$module_fields[ $module_name ][ $module_field_name ] = true;
				}
			}
		}

		return $module_fields;
	}

	static function get_media_quries( $for_js=false ) {
		$media_queries = array(
			'min_width_1405' => '@media only screen and ( min-width: 1405px )',
			'1100_1405'      => '@media only screen and ( min-width: 1100px ) and ( max-width: 1405px)',
			'981_1405'       => '@media only screen and ( min-width: 981px ) and ( max-width: 1405px)',
			'981_1100'       => '@media only screen and ( min-width: 981px ) and ( max-width: 1100px )',
			'min_width_981'  => '@media only screen and ( min-width: 981px )',
			'max_width_980'  => '@media only screen and ( max-width: 980px )',
			'768_980'        => '@media only screen and ( min-width: 768px ) and ( max-width: 980px )',
			'max_width_767'  => '@media only screen and ( max-width: 767px )',
			'max_width_479'  => '@media only screen and ( max-width: 479px )',
		);

		$media_queries['mobile'] = $media_queries['max_width_767'];

		$media_queries = apply_filters( 'et_builder_media_queries', $media_queries );

		if ( 'for_js' === $for_js ) {
			$processed_queries = array();

			foreach ( $media_queries as $key => $value ) {
				$processed_queries[] = array( $key, $value );
			}
		} else {
			$processed_queries = $media_queries;
		}

		return $processed_queries;
	}

	static function set_media_queries() {
		self::$media_queries = self::get_media_quries();
	}

	static function get_media_query( $name ) {
		if ( ! isset( self::$media_queries[ $name ] ) ) {
			return false;
		}

		return self::$media_queries[ $name ];
	}

	static function get_style( $internal = false ) {
		// use appropriate array depending on which styles we need
		$styles_array = $internal ? self::$internal_modules_styles : self::$styles;

		if ( empty( $styles_array ) ) {
			return '';
		}

		$output = '';

		$styles_by_media_queries = $styles_array;
		$styles_count            = (int) count( $styles_by_media_queries );
		$media_queries_order     = array_merge( array( 'general' ), array_values( self::$media_queries ) );

		// make sure styles in the array ordered by media query correctly from bigger to smaller screensize
		$styles_by_media_queries_sorted = array_merge( array_flip( $media_queries_order ), $styles_by_media_queries );

		foreach ( $styles_by_media_queries_sorted as $media_query => $styles ) {
			// skip wrong values which were added during the array sorting
			if ( ! is_array( $styles ) ) {
				continue;
			}

			$media_query_output    = '';
			$wrap_into_media_query = 'general' !== $media_query;

			// sort styles by priority
			uasort( $styles, array( 'self', 'compare_by_priority' ) );

			// get each rule in a media query
			foreach ( $styles as $selector => $settings ) {
				$media_query_output .= sprintf(
					'%3$s%4$s%1$s { %2$s }',
					$selector,
					$settings['declaration'],
					"\n",
					( $wrap_into_media_query ? "\t" : '' )
				);
			}

			// All css rules that don't use media queries are assigned to the "general" key.
			// Wrap all non-general settings into media query.
			if ( $wrap_into_media_query ) {
				$media_query_output = sprintf(
					'%3$s%3$s%1$s {%2$s%3$s}',
					$media_query,
					$media_query_output,
					"\n"
				);
			}

			$output .= $media_query_output;
		}

		return $output;
	}

	static function get_column_video_background( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		if ( empty( $args ) ) {
			return false;
		}

		$formatted_args = array();

		foreach ( $args as $key => $value) {
			$key_length = strlen( $key );
			$formatted_args[ substr( $key, 0, ( $key_length - 2 ) ) ] = $value;
		}

		return self::get_video_background( $formatted_args, $conditional_tags, $current_page );
	}

	static function get_video_background( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'background_video_mp4'    => '',
			'background_video_webm'   => '',
			'background_video_width'  => '',
			'background_video_height' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( '' === $args['background_video_mp4'] && '' === $args['background_video_webm'] ) {
			return false;
		}

		return do_shortcode( sprintf( '
			<video loop="loop"%3$s%4$s>
				%1$s
				%2$s
			</video>',
			( '' !== $args['background_video_mp4'] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args['background_video_mp4'] ) ) : '' ),
			( '' !== $args['background_video_webm'] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args['background_video_webm'] ) ) : '' ),
			( '' !== $args['background_video_width'] ? sprintf( ' width="%s"', esc_attr( intval( $args['background_video_width'] ) ) ) : '' ),
			( '' !== $args['background_video_height'] ? sprintf( ' height="%s"', esc_attr( intval( $args['background_video_height'] ) ) ) : '' )
		) );
	}

	static function clean_internal_modules_styles( $need_internal_styles = true ) {
		// clean the styles array
		self::$internal_modules_styles = array();
		// set the flag to make sure new styles will be saved to the correct place
		self::$prepare_internal_styles = $need_internal_styles;
		// generate unique number to make sure module classes will be unique if shortcode is generated via ajax
		self::$internal_modules_counter = rand( 10000, 99999 );
	}

	static function set_style( $function_name, $style ) {
		global $et_pb_rendering_column_content;

		// do not process all the styles if FB enabled. Only those for modules without fb support and styles for the internal modules from Blog/Slider
		if ( et_fb_is_enabled() && ! in_array( $function_name, self::get_fb_unsupported_modules() ) && ! $et_pb_rendering_column_content ) {
			return;
		}

		$order_class_name = self::get_module_order_class( $function_name );

		$selector    = str_replace( '%%order_class%%', ".{$order_class_name}", $style['selector'] );
		$selector    = str_replace( '%order_class%', ".{$order_class_name}", $selector );
		$selector    = apply_filters( 'et_pb_set_style_selector', $selector, $function_name );

		// Prepend .et_divi_builder class before all CSS rules in the Divi Builder plugin
		if ( et_is_builder_plugin_active() ) {
			$selector = ".et_divi_builder #et_builder_outer_content $selector";

			// add the prefix for all the selectors in a string.
			$selector = str_replace( ',', ',.et_divi_builder #et_builder_outer_content ', $selector );
		}

		$declaration = $style['declaration'];
		// New lines are saved as || in CSS Custom settings, remove them
		$declaration = preg_replace( '/(\|\|)/i', '', $declaration );

		$media_query = isset( $style[ 'media_query' ] ) ? $style[ 'media_query' ] : 'general';

		// prepare styles for internal content. Used in Blog/Slider modules if they contain Divi modules
		if ( $et_pb_rendering_column_content && self::$prepare_internal_styles ) {
			if ( isset( self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'] ) ) {
				self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'] = sprintf(
					'%1$s %2$s',
					self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'],
					$declaration
				);
			} else {
				self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'] = $declaration;
			}

			if ( isset( $style['priority'] ) ) {
				self::$internal_modules_styles[ $media_query ][ $selector ]['priority'] = (int) $style['priority'];
			}
		} else {
			if ( isset( self::$styles[ $media_query ][ $selector ]['declaration'] ) ) {
				self::$styles[ $media_query ][ $selector ]['declaration'] = sprintf(
					'%1$s %2$s',
					self::$styles[ $media_query ][ $selector ]['declaration'],
					$declaration
				);
			} else {
				self::$styles[ $media_query ][ $selector ]['declaration'] = $declaration;
			}

			if ( isset( $style['priority'] ) ) {
				self::$styles[ $media_query ][ $selector ]['priority'] = (int) $style['priority'];
			}
		}
	}

	static function get_module_order_class( $function_name ) {
		global $et_pb_rendering_column_content;

		// determine whether we need to get the internal module class or regular
		$get_inner_module_class = $et_pb_rendering_column_content;

		if ( $get_inner_module_class ) {
			if ( ! isset( self::$inner_modules_order[ $function_name ] ) ) {
				return false;
			}
		} else {
			if ( ! isset( self::$modules_order[ $function_name ] ) ) {
				return false;
			}
		}

		$shortcode_order_num = $get_inner_module_class ? self::$inner_modules_order[ $function_name ] : self::$modules_order[ $function_name ];

		$order_class_name = sprintf( '%1$s_%2$s', $function_name, $shortcode_order_num );


		return $order_class_name;
	}

	static function set_order_class( $function_name ) {
		global $et_pb_rendering_column_content;

		// determine whether we need to update the internal module class or regular
		$process_inner_module_class = $et_pb_rendering_column_content;

		if ( $process_inner_module_class ) {
			if ( ! isset( self::$inner_modules_order ) ) {
				self::$inner_modules_order = array();
			}

			self::$inner_modules_order[ $function_name ] = isset( self::$inner_modules_order[ $function_name ] ) ? (int) self::$inner_modules_order[ $function_name ] + 1 : self::$internal_modules_counter;
		} else {
			if ( ! isset( self::$modules_order ) ) {
				self::$modules_order = array();
			}

			self::$modules_order[ $function_name ] = isset( self::$modules_order[ $function_name ] ) ? (int) self::$modules_order[ $function_name ] + 1 : 0;
		}


	}

	static function add_module_order_class( $module_class, $function_name ) {
		$order_class_name = self::get_module_order_class( $function_name );

		return "{$module_class} {$order_class_name}";
	}

	function video_background( $args = array() ) {
		if ( ! empty( $args ) ) {
			$background_video = self::get_video_background( $args );

			$allow_player_pause = isset( $args['allow_player_pause' ] ) ? $args['allow_player_pause' ] : 'off';
		} else {
			$background_video = self::get_video_background( array(
				'background_video_mp4'    => $this->shortcode_atts['background_video_mp4'],
				'background_video_webm'   => $this->shortcode_atts['background_video_webm'],
				'background_video_width'  => $this->shortcode_atts['background_video_width'],
				'background_video_height' => $this->shortcode_atts['background_video_height'],
			) );

			$allow_player_pause = $this->shortcode_atts['allow_player_pause'];
		}

		$video_background = '';

		if ( $background_video ) {
			$video_background = sprintf(
				'<div class="et_pb_section_video_bg%2$s">
					%1$s
				</div>',
				$background_video,
				( 'on' === $allow_player_pause ? ' et_pb_allow_player_pause' : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_background;
	}

	function get_parallax_image_background() {
		$background_image    = $this->shortcode_atts['background_image'];
		$parallax            = $this->shortcode_atts['parallax'];
		$parallax_method     = $this->shortcode_atts['parallax_method'];
		$parallax_background = '';

		if ( '' !== $background_image && 'on' == $parallax ) {
			$parallax_classname = array( 'et_parallax_bg' );

			if ( 'off' === $parallax_method ) {
				$parallax_classname[] = 'et_pb_parallax_css';
			}

			$parallax_background = sprintf(
				'<div
					class="%1$s"
					style="background-image: url(%2$s);"
				></div>',
				esc_attr( implode( ' ', $parallax_classname ) ),
				esc_url( $background_image )
			);
		}

		return $parallax_background;
	}

	/**
	 * Convert smart quotes and &amp; entity to their applicable characters
	 * @param  string $text Input text
	 * @return string
	 */
	static function convert_smart_quotes_and_amp( $text ) {
		$smart_quotes = array(
			'&#8220;',
			'&#8221;',
			'&#8243;',
			'&#8216;',
			'&#8217;',
			'&#x27;',
			'&amp;',
		);

		$replacements = array(
			'&quot;',
			'&quot;',
			'&quot;',
			'&#39;',
			'&#39;',
			'&#39;',
			'&',
		);

		if ( 'fr_FR' === get_locale() ) {
			$french_smart_quotes = array(
				'&nbsp;&raquo;',
				'&Prime;&gt;',
			);

			$french_replacements = array(
				'&quot;',
				'&quot;&gt;',
			);

			$smart_quotes = array_merge( $smart_quotes, $french_smart_quotes );
			$replacements = array_merge( $replacements, $french_replacements );
		}

		$text = str_replace( $smart_quotes, $replacements, $text );

		return $text;
	}
}

do_action( 'et_pagebuilder_module_init' );

class ET_Builder_Module extends ET_Builder_Element {}

class ET_Builder_Structure_Element extends ET_Builder_Element {
	public $is_structure_element = true;

	function wrap_settings_option( $option_output, $field ) {
		$field_type = ! empty( $field['type'] ) ? $field['type'] : '';

		switch( $field_type ) {
			case 'column_settings_background' :
				$output = $this->generate_columns_settings_background();
				break;
			case 'column_settings_padding' :
				$output = $this->generate_columns_settings_padding();
				break;
			case 'column_settings_css_fields' :
				$output = $this->generate_columns_settings_css_fields();
				break;
			case 'column_settings_css' :
				$output = $this->generate_columns_settings_css();
				break;
			default:
				$depends = false;
				if ( isset( $field['depends_show_if'] ) || isset( $field['depends_show_if_not'] ) ) {
					$depends = true;
					if ( isset( $field['depends_show_if_not'] ) ) {
						$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $field['depends_show_if_not'] ) );
					} else {
						$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $field['depends_show_if'] ) );
					}
				}

				// Overriding background color's attribute, turning it into appropriate background attributes
				if ( isset( $field['type'] ) && isset( $field['name' ] ) && in_array( $field['name'], array( 'background_color' ) ) ) {

					$field['type'] = 'background';

					// Appending background class
					if ( isset( $field['option_class'] ) ) {
						$field['option_class'] .= ' et-pb-option--background';
					} else {
						$field['option_class'] = 'et-pb-option--background';
					}

					// Removing depends default variable which hides background color for unified background field UI
					$depends = false;

					if ( isset( $field['depends_default'] ) ) {
						unset( $field['depends_default'] );
					}
				}

				$output = sprintf(
					'%6$s<div class="et-pb-option et-pb-option--%11$s%1$s%2$s%3$s%8$s%9$s%10$s"%4$s>%5$s</div> <!-- .et-pb-option -->%7$s',
					( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' et-pb-option-main-content' : '' ),
					( ( $depends || isset( $field['depends_default'] ) ) ? ' et-pb-depends' : '' ),
					( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' et_pb_hidden' : '' ),
					( $depends ? $depends_attr : '' ),
					"\n\t\t\t\t" . $option_output . "\n\t\t\t",
					"\t",
					"\n\n\t\t",
					( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' et-pb-option-%1$s', $field['name'] ) ) : '' ),
					( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' ),
					isset( $field['specialty_only'] ) && 'yes' === $field['specialty_only'] ? ' et-pb-specialty-only-option' : '',
					isset( $field['type'] ) ? esc_attr( $field['type'] ) : ''
				);
				break;
		}

		return $output;
	}

	function generate_column_vars_css() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_module_id_value = typeof et_pb_module_id_%1$s !== \'undefined\' ? et_pb_module_id_%1$s : \'\',
					current_module_class_value = typeof et_pb_module_class_%1$s !== \'undefined\' ? et_pb_module_class_%1$s : \'\',
					current_custom_css_before_value = typeof et_pb_custom_css_before_%1$s !== \'undefined\' ? et_pb_custom_css_before_%1$s : \'\',
					current_custom_css_main_value = typeof et_pb_custom_css_main_%1$s !== \'undefined\' ? et_pb_custom_css_main_%1$s : \'\',
					current_custom_css_after_value = typeof et_pb_custom_css_after_%1$s !== \'undefined\' ? et_pb_custom_css_after_%1$s : \'\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_column_vars_bg() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_value_bg = typeof et_pb_background_color_%1$s !== \'undefined\' ? et_pb_background_color_%1$s : \'\',
					current_value_bg_img = typeof et_pb_bg_img_%1$s !== \'undefined\' ? et_pb_bg_img_%1$s : \'\';
					current_background_size_cover = typeof et_pb_background_size_%1$s !== \'undefined\' && et_pb_background_size_%1$s === \'cover\' ? \' selected="selected"\' : \'\';
					current_background_size_contain = typeof et_pb_background_size_%1$s !== \'undefined\' && et_pb_background_size_%1$s === \'contain\' ? \' selected="selected"\' : \'\';
					current_background_size_initial = typeof et_pb_background_size_%1$s !== \'undefined\' && et_pb_background_size_%1$s === \'initial\' ? \' selected="selected"\' : \'\';
					current_background_position_topleft = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'top_left\' ? \' selected="selected"\' : \'\';
					current_background_position_topcenter = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'top_center\' ? \' selected="selected"\' : \'\';
					current_background_position_topright = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'top_right\' ? \' selected="selected"\' : \'\';
					current_background_position_centerleft = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'center_left\' ? \' selected="selected"\' : \'\';
					current_background_position_center = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'center\' ? \' selected="selected"\' : \'\';
					current_background_position_centerright = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'center_right\' ? \' selected="selected"\' : \'\';
					current_background_position_bottomleft = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'bottom_left\' ? \' selected="selected"\' : \'\';
					current_background_position_bottomcenter = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'bottom_center\' ? \' selected="selected"\' : \'\';
					current_background_position_bottomright = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'bottom_right\' ? \' selected="selected"\' : \'\';
					current_background_repeat_repeat = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'repeat\' ? \' selected="selected"\' : \'\';
					current_background_repeat_repeatx = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'repeat-x\' ? \' selected="selected"\' : \'\';
					current_background_repeat_repeaty = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'repeat-y\' ? \' selected="selected"\' : \'\';
					current_background_repeat_space = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'space\' ? \' selected="selected"\' : \'\';
					current_background_repeat_round = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'round\' ? \' selected="selected"\' : \'\';
					current_background_repeat_norepeat = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'no-repeat\' ? \' selected="selected"\' : \'\';
					current_background_blend_normal = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'normal\' ? \' selected="selected"\' : \'\';
					current_background_blend_multiply = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'multiply\' ? \' selected="selected"\' : \'\';
					current_background_blend_screen = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'screen\' ? \' selected="selected"\' : \'\';
					current_background_blend_overlay = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'overlay\' ? \' selected="selected"\' : \'\';
					current_background_blend_darken = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'darken\' ? \' selected="selected"\' : \'\';
					current_background_blend_lighten = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'lighten\' ? \' selected="selected"\' : \'\';
					current_background_blend_colordodge = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'color-dodge\' ? \' selected="selected"\' : \'\';
					current_background_blend_colorburn = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'color-burn\' ? \' selected="selected"\' : \'\';
					current_background_blend_hardlight = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'hard-light\' ? \' selected="selected"\' : \'\';
					current_background_blend_softlight = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'soft-light\' ? \' selected="selected"\' : \'\';
					current_background_blend_difference = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'difference\' ? \' selected="selected"\' : \'\';
					current_background_blend_exclusion = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'hue\' ? \' selected="selected"\' : \'\';
					current_background_blend_hue = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'saturation\' ? \' selected="selected"\' : \'\';
					current_background_blend_saturation = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'color\' ? \' selected="selected"\' : \'\';
					current_background_blend_color = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'normal\' ? \' selected="selected"\' : \'\';
					current_background_blend_luminosity = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'luminosity\' ? \' selected="selected"\' : \'\';
					current_use_background_color_gradient = typeof et_pb_use_background_color_gradient_%1$s !== \'undefined\' && \'on\' === et_pb_use_background_color_gradient_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_start = typeof et_pb_background_color_gradient_start_%1$s !== \'undefined\' ? et_pb_background_color_gradient_start_%1$s : \'#2b87da\';
					current_background_color_gradient_end = typeof et_pb_background_color_gradient_end_%1$s !== \'undefined\' ? et_pb_background_color_gradient_end_%1$s : \'#29c4a9\';
					current_background_color_gradient_type = typeof et_pb_background_color_gradient_type_%1$s !== \'undefined\' && \'radial\' === et_pb_background_color_gradient_type_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction = typeof et_pb_background_color_gradient_direction_%1$s !== \'undefined\' ? et_pb_background_color_gradient_direction_%1$s : \'180deg\';
					current_background_color_gradient_direction_radial_center = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'center\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_top_left = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'top left\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_top = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'top\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_top_right = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'top right\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_right = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'right\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_bottom_right = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'bottom right\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_bottom = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'bottom\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_bottom_left = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'bottom left\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_left = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'left\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_start_position = typeof et_pb_background_color_gradient_start_position_%1$s !== \'undefined\' ? et_pb_background_color_gradient_start_position_%1$s : \'0%%\';
					current_background_color_gradient_end_position = typeof et_pb_background_color_gradient_end_position_%1$s !== \'undefined\' ? et_pb_background_color_gradient_end_position_%1$s : \'100%%\';
					current_background_video_mp4 = typeof et_pb_background_video_mp4_%1$s !== \'undefined\' ? et_pb_background_video_mp4_%1$s : \'\';
					current_background_video_webm = typeof et_pb_background_video_webm_%1$s !== \'undefined\' ? et_pb_background_video_webm_%1$s : \'\';
					current_background_video_width = typeof et_pb_background_video_width_%1$s !== \'undefined\' ? et_pb_background_video_width_%1$s : \'\';
					current_background_video_height = typeof et_pb_background_video_height_%1$s !== \'undefined\' ? et_pb_background_video_height_%1$s : \'\';
					current_allow_played_pause = typeof et_pb_allow_player_pause_%1$s !== \'undefined\' &&  \'on\' === et_pb_allow_player_pause_%1$s ? \' selected="selected"\' : \'\';
					current_value_parallax = typeof et_pb_parallax_%1$s !== \'undefined\' && \'on\' === et_pb_parallax_%1$s ? \' selected="selected"\' : \'\';
					current_value_parallax_method = typeof et_pb_parallax_method_%1$s !== \'undefined\' && \'on\' !== et_pb_parallax_method_%1$s ? \' selected="selected"\' : \'\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_column_vars_padding() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_value_pt = typeof et_pb_padding_top_%1$s !== \'undefined\' ? et_pb_padding_top_%1$s : \'\',
					current_value_pr = typeof et_pb_padding_right_%1$s !== \'undefined\' ? et_pb_padding_right_%1$s : \'\',
					current_value_pb = typeof et_pb_padding_bottom_%1$s !== \'undefined\' ? et_pb_padding_bottom_%1$s : \'\',
					current_value_pl = typeof et_pb_padding_left_%1$s !== \'undefined\' ? et_pb_padding_left_%1$s : \'\',
					current_value_padding_tablet = typeof et_pb_padding_%1$s_tablet !== \'undefined\' ? et_pb_padding_%1$s_tablet : \'\',
					current_value_padding_phone = typeof et_pb_padding_%1$s_phone !== \'undefined\' ? et_pb_padding_%1$s_phone : \'\',
					last_edited_padding_field = typeof et_pb_padding_%1$s_last_edited !== \'undefined\' ?  et_pb_padding_%1$s_last_edited : \'\',
					has_tablet_padding = typeof et_pb_padding_%1$s_tablet !== \'undefined\' ? \'yes\' : \'no\',
					has_phone_padding = typeof et_pb_padding_%1$s_phone !== \'undefined\' ? \'yes\' : \'no\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_columns_settings_background() {
		$output = sprintf(
			'<%% var columns = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter = 1;
				_.each( columns, function ( column_type ) {
					var current_value_bg,
						current_value_bg_img,
						current_value_parallax,
						current_value_parallax_method,
						current_background_size_cover,
						current_background_size_contain,
						current_background_size_initial,
						current_background_position_topleft,
						current_background_position_topcenter,
						current_background_position_topright,
						current_background_position_centerleft,
						current_background_position_center,
						current_background_position_centerright,
						current_background_position_bottomleft,
						current_background_position_bottomcenter,
						current_background_position_bottomright,
						current_background_repeat_repeat,
						current_background_repeat_repeatx,
						current_background_repeat_repeaty,
						current_background_repeat_space,
						current_background_repeat_round,
						current_background_repeat_norepeat,
						current_background_blend_normal,
						current_background_blend_multiply,
						current_background_blend_screen,
						current_background_blend_overlay,
						current_background_blend_darken,
						current_background_blend_lighten,
						current_background_blend_colordodge,
						current_background_blend_colorburn,
						current_background_blend_hardlight,
						current_background_blend_softlight,
						current_background_blend_difference,
						current_background_blend_exclusion,
						current_background_blend_hue,
						current_background_blend_saturation,
						current_background_blend_color,
						current_background_blend_luminosity,
						current_use_background_color_gradient,
						current_background_color_gradient_start,
						current_background_color_gradient_end,
						current_background_color_gradient_type,
						current_background_color_gradient_direction,
						current_background_color_gradient_direction_radial_center,
						current_background_color_gradient_direction_radial_top_left,
						current_background_color_gradient_direction_radial_top,
						current_background_color_gradient_direction_radial_top_right,
						current_background_color_gradient_direction_radial_right,
						current_background_color_gradient_direction_radial_bottom_right,
						current_background_color_gradient_direction_radial_bottom,
						current_background_color_gradient_direction_radial_bottom_left,
						current_background_color_gradient_direction_radial_left,
						current_background_color_gradient_start_position,
						current_background_color_gradient_end_position,
						current_background_video_mp4,
						current_background_video_webm,
						current_background_video_width,
						current_background_video_height,
						current_allow_played_pause;
					switch ( counter ) {
						%1$s
					}
			%%>',
			$this->generate_column_vars_bg()
		);

		$tab_navs = sprintf(
			'<ul class="et_pb_background-tab-navs">
				<li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--color" data-tab="color" title="%1$s">
						%5$s
					</a>
				</li><li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--gradient" data-tab="gradient" title="%2$s">
						%6$s
					</a>
				</li><li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--image" data-tab="image" title="%3$s">
						%7$s
					</a>
				</li><li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--video" data-tab="video" title="%4$s">
						%8$s
					</a>
				</li>
			</ul>',
			esc_html__( 'Color', 'et_builder' ),
			esc_html__( 'Gradient', 'et_builder' ),
			esc_html__( 'Image', 'et_builder' ),
			esc_html__( 'Video', 'et_builder' ),
			$this->get_icon( 'background-color' ),
			$this->get_icon( 'background-gradient' ),
			$this->get_icon( 'background-image' ),
			$this->get_icon( 'background-video' )
		);

		$tab_color = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--color" data-tab="color">
				<div class="et_pb_background-option et_pb_background-option--background_color et-pb-option et-pb-option--background_color et-pb-option--has-preview">
					<label for="et_pb_background_color">%1$s: </label>
					<div class="et-pb-option-container et-pb-option-container--color-alpha">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_background_color_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha et-pb-color-picker-hex-has-preview" type="text" data-alpha="true" placeholder="%5$s" data-selected-value="" value="<%%= current_value_bg %%>">
					</div>
					<!-- .et-pb-option-container -->
				</div>
			</div><!-- .et_pb_background-tab.et_pb_background-tab--color -->',
			esc_html__( 'Background Color', 'et_builder' ),
			$this->get_icon( 'add' ),
			$this->get_icon( 'setting' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Hex Value', 'et_builder' )
		);

		$tab_gradient = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--gradient" data-tab="gradient">
				<div class="et-pb-option-preview et-pb-option-preview--empty">
					<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
						%1$s
					</button>
					<button class="et-pb-option-preview-button et-pb-option-preview-button--swap">
						%2$s
					</button>
					<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
						%3$s
					</button>
				</div>
				<div class="et_pb_background-option et_pb_background-option--use_background_color_gradient et-pb-option et-pb-option--use_background_color_gradient">
					<label for="et_pb_use_background_color_gradient_<%%= counter %%>">%4$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%5$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%6$s</span>
							</div>
							<select name="et_pb_use_background_color_gradient_<%%= counter %%>" id="et_pb_use_background_color_gradient_<%%= counter %%>" class="et-pb-main-setting regular-text et-pb-affects" data-affects="background_color_gradient_start_<%%= counter %%>, background_color_gradient_end_<%%= counter %%>, background_color_gradient_start_position_<%%= counter %%>, background_color_gradient_end_position_<%%= counter %%>, background_color_gradient_type_<%%= counter %%>" data-default="off">
								<option value="off">%6$s</option>
								<option value="on" <%%= current_use_background_color_gradient %%>>%5$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_start et-pb-option et-pb-option--background_color_gradient_start" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_start_<%%= counter %%>">%7$s: </label>
					<div class="et-pb-option-container et-pb-option-container--color-alpha">
						<div class="wp-picker-container">
							<input id="et_pb_background_color_gradient_start_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha et-pb-main-setting" type="text" data-alpha="true" placeholder="%8$s" data-selected-value="<%%= current_background_color_gradient_start %%>" value="<%%= current_background_color_gradient_start %%>" data-default-color="#2b87da" data-default="#2b87da">
						</div>
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_end et-pb-option et-pb-option--background_color_gradient_end" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_end_<%%= counter %%>">%9$s: </label>
					<div class="et-pb-option-container et-pb-option-container--color-alpha">
						<div class="wp-picker-container">
							<input id="et_pb_background_color_gradient_end_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha et-pb-main-setting" type="text" data-alpha="true" placeholder="%8$s" data-selected-value="<%%= current_background_color_gradient_end %%>" value="<%%= current_background_color_gradient_end %%>" data-default-color="#29c4a9" data-default="#29c4a9">
						</div>
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_type et-pb-option et-pb-option--background_color_gradient_type" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_type_<%%= counter %%>">%10$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						<select name="et_pb_background_color_gradient_type_<%%= counter %%>" id="et_pb_background_color_gradient_type_<%%= counter %%>" class="et-pb-main-setting  et-pb-affects" data-affects="background_color_gradient_direction_<%%= counter %%>, background_color_gradient_direction_radial_<%%= counter %%>" data-default="linear">
							<option value="linear">%11$s</option>
							<option value="radial" <%%= current_background_color_gradient_type %%>>%12$s</option>
						</select>
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_direction et-pb-option et-pb-option--background_color_gradient_direction" data-depends_show_if="linear">
					<label for="et_pb_background_color_gradient_direction_<%%= counter %%>">%13$s: </label>
					<div class="et-pb-option-container et-pb-option-container--range">
						<input type="range" class="et-pb-main-setting et-pb-range et-pb-fixed-range" data-default="180" value="<%%= current_background_color_gradient_direction %%>" min="0" max="360" step="1">
						<input id="et_pb_background_color_gradient_direction_<%%= counter %%>" type="text" class="regular-text et-pb-validate-unit et-pb-range-input" value="<%%= current_background_color_gradient_direction %%>" data-default="180deg">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_direction_radial et-pb-option et-pb-option--background_color_gradient_direction_radial" data-depends_show_if="radial">
					<label for="et_pb_background_color_gradient_direction_radial_<%%= counter %%>">%14$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						<select name="et_pb_background_color_gradient_direction_radial_<%%= counter %%>" id="et_pb_background_color_gradient_direction_radial_<%%= counter %%>" class="et-pb-main-setting" data-default="center">
							<option value="center" <%%= current_background_color_gradient_direction_radial_center %%>>%15$s</option>
							<option value="top left" <%%= current_background_color_gradient_direction_radial_top_left %%>>%16$s</option>
							<option value="top" <%%= current_background_color_gradient_direction_radial_top %%>>%17$s</option>
							<option value="top right" <%%= current_background_color_gradient_direction_radial_top_right %%>>%18$s</option>
							<option value="right" <%%= current_background_color_gradient_direction_radial_right %%>>%19$s</option>
							<option value="bottom right" <%%= current_background_color_gradient_direction_radial_bottom_right %%>>%20$s</option>
							<option value="bottom" <%%= current_background_color_gradient_direction_radial_bottom %%>>%21$s</option>
							<option value="bottom left" <%%= current_background_color_gradient_direction_radial_bottom_left %%>>%22$s</option>
							<option value="left" <%%= current_background_color_gradient_direction_radial_left %%>>%23$s</option>
						</select>
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_start_position et-pb-option et-pb-option--background_color_gradient_start_position" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_start_position_<%%= counter %%>">%24$s: </label>
					<div class="et-pb-option-container et-pb-option-container--range">
						<input type="range" class="et-pb-main-setting et-pb-range et-pb-fixed-range" data-default="0" value="<%%= parseInt( current_background_color_gradient_start_position.trim() ) %%>" min="0" max="100" step="1">
						<input id="et_pb_background_color_gradient_start_position_<%%= counter %%>" type="text" class="regular-text et-pb-validate-unit et-pb-range-input" value="<%%= current_background_color_gradient_start_position %%>" data-default="0%%">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_end_position et-pb-option et-pb-option--background_color_gradient_end_position" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_end_position_<%%= counter %%>">%25$s: </label>
					<div class="et-pb-option-container et-pb-option-container--range">
						<input type="range" class="et-pb-main-setting et-pb-range et-pb-fixed-range" data-default="100" value="<%%= parseInt( current_background_color_gradient_end_position.trim() ) %%>" min="0" max="100" step="1">
						<input id="et_pb_background_color_gradient_end_position_<%%= counter %%>" type="text" class="regular-text et-pb-validate-unit et-pb-range-input" value="<%%= current_background_color_gradient_end_position %%>" data-default="100%%">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
			</div><!-- .et_pb_background-tab.et_pb_background-tab--gradient -->',
			$this->get_icon( 'add' ),
			$this->get_icon( 'swap' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Background Gradient', 'et_builder' ),
			esc_html__( 'On', 'et_builder' ), // #5
			esc_html__( 'Off', 'et_builder' ),
			esc_html__( 'Gradient Start', 'et_builder' ),
			esc_html__( 'Hex Value', 'et_builder' ),
			esc_html__( 'Gradient End', 'et_builder' ),
			esc_html__( 'Gradient Type', 'et_builder' ), // #10
			esc_html__( 'Linear', 'et_builder' ),
			esc_html__( 'Radial', 'et_builder' ),
			esc_html__( 'Gradient Direction', 'et_builder' ),
			esc_html__( 'Radial Direction', 'et_builder' ),
			esc_html__( 'Center', 'et_builder' ), // #15
			esc_html__( 'Top Left', 'et_builder' ),
			esc_html__( 'Top', 'et_builder' ),
			esc_html__( 'Top Right', 'et_builder' ),
			esc_html__( 'Right', 'et_builder' ),
			esc_html__( 'Bottom Right', 'et_builder' ), // #20
			esc_html__( 'Bottom', 'et_builder' ),
			esc_html__( 'Bottom Left', 'et_builder' ),
			esc_html__( 'Left', 'et_builder' ),
			esc_html__( 'Start Position', 'et_builder' ),
			esc_html__( 'End Position', 'et_builder' ) // #25
		);

		$select_background_size = sprintf(
			'<select name="et_pb_background_size_<%%= counter %%>" id="et_pb_background_size_<%%= counter %%>" class="et-pb-main-setting" data-default="cover">
				<option value="cover"<%%= current_background_size_cover %%>>%1$s</option>
				<option value="contain"<%%= current_background_size_contain %%>>%2$s</option>
				<option value="initial"<%%= current_background_size_initial %%>>%3$s</option>
			</select>',
			esc_html__( 'Cover', 'et_builder' ),
			esc_html__( 'Fit', 'et_builder' ),
			esc_html__( 'Actual Size', 'et_builder' )
		);

		$select_background_position = sprintf(
			'<select name="et_pb_background_position_<%%= counter %%>" id="et_pb_background_position_<%%= counter %%>" class="et-pb-main-setting" data-default="center">
				<option value="top_left"<%%= current_background_position_topleft %%>>%1$s</option>
				<option value="top_center"<%%= current_background_position_topcenter %%>>%2$s</option>
				<option value="top_right"<%%= current_background_position_topright %%>>%3$s</option>
				<option value="center_left"<%%= current_background_position_centerleft %%>>%4$s</option>
				<option value="center"<%%= current_background_position_center %%>>%5$s</option>
				<option value="center_right"<%%= current_background_position_centerright %%>>%6$s</option>
				<option value="bottom_left"<%%= current_background_position_bottomleft %%>>%7$s</option>
				<option value="bottom_center"<%%= current_background_position_bottomcenter %%>>%8$s</option>
				<option value="bottom_right"<%%= current_background_position_bottomright %%>>%9$s</option>
			</select>',
			esc_html__( 'Top Left', 'et_builder' ),
			esc_html__( 'Top Center', 'et_builder' ),
			esc_html__( 'Top Right', 'et_builder' ),
			esc_html__( 'Center Left', 'et_builder' ),
			esc_html__( 'Center', 'et_builder' ),
			esc_html__( 'Center Right', 'et_builder' ),
			esc_html__( 'Bottom Left', 'et_builder' ),
			esc_html__( 'Bottom Center', 'et_builder' ),
			esc_html__( 'Bottom Right', 'et_builder' )
		);

		$select_background_repeat = sprintf(
			'<select name="et_pb_background_repeat_<%%= counter %%>" id="et_pb_background_repeat_<%%= counter %%>" class="et-pb-main-setting" data-default="repeat">
				<option value="no-repeat"<%%= current_background_repeat_norepeat %%>>%1$s</option>
				<option value="repeat"<%%= current_background_repeat_repeat %%>>%2$s</option>
				<option value="repeat-x"<%%= current_background_repeat_repeatx %%>>%3$s</option>
				<option value="repeat-y"<%%= current_background_repeat_repeaty %%>>%4$s</option>
				<option value="space"<%%= current_background_repeat_space %%>>%5$s</option>
				<option value="round"<%%= current_background_repeat_round %%>>%6$s</option>
			</select>',
			esc_html__( 'No Repeat', 'et_builder' ),
			esc_html__( 'Repeat', 'et_builder' ),
			esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
			esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
			esc_html__( 'Space', 'et_builder' ),
			esc_html__( 'Round', 'et_builder' )
		);

		$select_background_blend = sprintf(
			'<select name="et_pb_background_blend_<%%= counter %%>" id="et_pb_background_blend_<%%= counter %%>" class="et-pb-main-setting" data-default="normal">
				<option value="normal"<%%= current_background_blend_normal %%>>%1$s</option>
				<option value="multiply"<%%= current_background_blend_multiply %%>>%2$s</option>
				<option value="screen"<%%= current_background_blend_screen %%>>%3$s</option>
				<option value="overlay"<%%= current_background_blend_overlay %%>>%4$s</option>
				<option value="darken"<%%= current_background_blend_darken %%>>%5$s</option>
				<option value="lighten"<%%= current_background_blend_lighten %%>>%6$s</option>
				<option value="color-dodge"<%%= current_background_blend_colordodge %%>>%7$s</option>
				<option value="color-burn"<%%= current_background_blend_colorburn %%>>%8$s</option>
				<option value="hard-light"<%%= current_background_blend_hardlight %%>>%9$s</option>
				<option value="soft-light"<%%= current_background_blend_softlight %%>>%10$s</option>
				<option value="difference"<%%= current_background_blend_difference %%>>%11$s</option>
				<option value="exclusion"<%%= current_background_blend_exclusion %%>>%12$s</option>
				<option value="hue"<%%= current_background_blend_hue %%>>%13$s</option>
				<option value="saturation"<%%= current_background_blend_saturation %%>>%14$s</option>
				<option value="color"<%%= current_background_blend_color %%>>%15$s</option>
				<option value="luminosity"<%%= current_background_blend_luminosity %%>>%16$s</option>
			</select>',
			esc_html__( 'Normal', 'et_builder' ),
			esc_html__( 'Multiply', 'et_builder' ),
			esc_html__( 'Screen', 'et_builder' ),
			esc_html__( 'Overlay', 'et_builder' ),
			esc_html__( 'Darken', 'et_builder' ),
			esc_html__( 'Lighten', 'et_builder' ),
			esc_html__( 'Color Dodge', 'et_builder' ),
			esc_html__( 'Color Burn', 'et_builder' ),
			esc_html__( 'Hard Light', 'et_builder' ),
			esc_html__( 'Soft Light', 'et_builder' ),
			esc_html__( 'Difference', 'et_builder' ),
			esc_html__( 'Exclusion', 'et_builder' ),
			esc_html__( 'Hue', 'et_builder' ),
			esc_html__( 'Saturation', 'et_builder' ),
			esc_html__( 'Color', 'et_builder' ),
			esc_html__( 'Luminosity', 'et_builder' )
		);

		$tab_image = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--image" data-tab="image">
				<div class="et_pb_background-option et_pb_background-option--background_image et-pb-option et-pb-option--background_image et-pb-option--has-preview">
					<label for="et_pb_bg_img_<%%= counter %%>">%1$s: </label>
					<div class="et-pb-option-container et-pb-option-container--upload">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_bg_img_<%%= counter %%>" type="text" class="et-pb-main-setting regular-text et-pb-upload-field" value="<%%= current_value_bg_img  %%>">
						<input type="button" class="button button-upload et-pb-upload-button" value="%5$s" data-choose="%6$s" data-update="%7$s" data-type="image">
						<span class="et-pb-reset-setting" style="display: none;"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--parallax et-pb-option et-pb-option--parallax">
					<label for="et_pb_parallax_<%%= counter %%>">%8$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%9$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%10$s</span>
							</div>
							<select name="et_pb_parallax_<%%= counter %%>" id="et_pb_parallax_<%%= counter %%>" class="et-pb-main-setting regular-text et-pb-affects" data-affects="parallax_method_<%%= counter %%>, background_size_<%%= counter %%>, background_position_<%%= counter %%>, background_repeat_<%%= counter %%>, background_blend_<%%= counter %%>" data-default="off">
								<option value="off">%10$s</option>
								<option value="on" <%%= current_value_parallax %%>>%9$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--parallax_method et-pb-option et-pb-option--parallax_method" data-depends_show_if="on">
					<label for="et_pb_parallax_method_<%%= counter %%>">%11$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						<select name="et_pb_parallax_method_<%%= counter %%>" id="et_pb_parallax_method_<%%= counter %%>" class="et-pb-main-setting" data-default="on">
							<option value="on">%12$s</option>
							<option value="off" <%%= current_value_parallax_method %%>>%13$s</option>
						</select>
						<span class="et-pb-reset-setting" style="display: none;"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_size et-pb-option et-pb-option--background_size" data-depends_show_if="off" data-option_name="background_size">
					<label for="et_pb_background_size">%14$s:</label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%15$s
					</div> <!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_position et-pb-option et-pb-option--background_position" data-depends_show_if="off" data-option_name="background_position">
					<label for="et_pb_background_position">%16$s:</label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%17$s
					</div> <!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_repeat et-pb-option et-pb-option--background_repeat" data-depends_show_if="off" data-option_name="background_repeat">
					<label for="et_pb_background_repeat">%18$s:</label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%19$s
					</div> <!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_blend et-pb-option et-pb-option--background_blend" data-depends_show_if="off" data-option_name="background_blend">
					<label for="et_pb_background_blend">%20$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%21$s
					</div> <!-- .et-pb-option-container -->
				</div>
			</div><!-- .et_pb_background-tab.et_pb_background-tab--image -->',
			esc_html__( 'Background Image', 'et_builder' ),
			$this->get_icon( 'add' ),
			$this->get_icon( 'setting' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Upload an image', 'et_builder' ), // #5
			esc_html__( 'Choose a Background Image', 'et_builder' ),
			esc_html__( 'Set As Background', 'et_builder' ),
			esc_html__( 'Use Parallax Effect', 'et_builder' ),
			esc_html__( 'On', 'et_builder' ),
			esc_html__( 'Off', 'et_builder' ), // #10
			esc_html__( 'Parallax Method', 'et_builder' ),
			esc_html__( 'True Parallax', 'et_builder' ),
			esc_html__( 'CSS', 'et_builder' ),
			esc_html__( 'Background Image Size', 'et_builder' ),
			$select_background_size, // #15
			esc_html__( 'Background Image Position', 'et_builder' ),
			$select_background_position,
			esc_html__( 'Background Image Repeat', 'et_builder' ),
			$select_background_repeat,
			esc_html__( 'Background Image Blend', 'et_builder' ), // #20
			$select_background_blend
		);

		$tab_video = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--video" data-tab="video">
				<div class="et_pb_background-option et_pb_background-option--background_video_mp4 et-pb-option et-pb-option--background_video_mp4 et-pb-option--has-preview">
					<label for="et_pb_background_video_mp4_<%%= counter %%>">%1$s: </label>
					<div class="et-pb-option-container et-pb-option-container--upload">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_background_video_mp4_<%%= counter %%>" type="text" class="et-pb-main-setting regular-text et-pb-upload-field" value="<%%= current_background_video_mp4 %%>">
						<input type="button" class="button button-upload et-pb-upload-button" value="%5$s" data-choose="%6$s" data-update="%7$s" data-type="video">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_webm et-pb-option et-pb-option--background_video_webm et-pb-option--has-preview">
					<label for="et_pb_background_video_webm_<%%= counter %%>">%8$s: </label>
					<div class="et-pb-option-container et-pb-option-container--upload">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_background_video_webm_<%%= counter %%>" type="text" class="et-pb-main-setting regular-text et-pb-upload-field" value="<%%= current_background_video_webm %%>">
						<input type="button" class="button button-upload et-pb-upload-button" value="%5$s" data-choose="%9$s" data-update="%7$s" data-type="video">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_width et-pb-option et-pb-option--background_video_width">
					<label for="et_pb_background_video_width_<%%= counter %%>">%10$s: </label>
					<div class="et-pb-option-container et-pb-option-container--text">
						<input id="et_pb_background_video_width_<%%= counter %%>" type="text" class="regular-text et-pb-main-setting" value="<%%= current_background_video_width %%>">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_height et-pb-option et-pb-option--background_video_height">
					<label for="et_pb_background_video_height_<%%= counter %%>">%11$s: </label>
					<div class="et-pb-option-container et-pb-option-container--text">
						<input id="et_pb_background_video_height_<%%= counter %%>" type="text" class="regular-text et-pb-main-setting" value="<%%= current_background_video_height %%>">
						<span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
				<div class="et_pb_background-option et_pb_background-option--allow_player_pause et-pb-option et-pb-option--allow_player_pause">
					<label for="et_pb_allow_player_pause_<%%= counter %%>">%12$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%13$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%14$s</span>
							</div>
							<select name="et_pb_allow_player_pause_<%%= counter %%>" id="et_pb_allow_player_pause_<%%= counter %%>" class="et-pb-main-setting regular-text" data-default="off">
								<option value="off">%14$s</option>
								<option value="on" <%%= current_allow_played_pause %%>>%13$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
					<!-- .et-pb-option-container -->
				</div>
			</div><!-- .et_pb_background-tab.et_pb_background-tab--video -->',
			esc_html__( 'Background Video MP4', 'et_builder' ),
			$this->get_icon( 'add' ),
			$this->get_icon( 'setting' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Upload a video', 'et_builder' ), // #5
			esc_html__( 'Choose a Background Video MP4 File', 'et_builder' ),
			esc_html__( 'Set As Background Video', 'et_builder' ),
			esc_html__( 'Background Video Webm', 'et_builder' ),
			esc_html__( 'Choose a Background Video WEBM File', 'et_builder' ),
			esc_html__( 'Background Video Width', 'et_builder' ), // #10
			esc_html__( 'Background Video Height', 'et_builder' ),
			esc_html__( 'Pause Video', 'et_builder' ),
			esc_html__( 'On', 'et_builder' ),
			esc_html__( 'Off', 'et_builder' )
		);

		$output .= sprintf(
			'<div class="et_pb_subtoggle_section">
				<div class="et-pb-option-toggle-content">
					<div class="et-pb-option et-pb-option--background">
						<label for="et_pb_background">
							%1$s
							<%% if ( "4_4" !== column_type ) { %%>
								<%%= counter + " " %%>
							<%% } %%>
							%2$s:
						</label>
						<div class="et-pb-option-container et-pb-option-container--background" data-column-index="<%%= counter %%>">
							%3$s

							%4$s

							%5$s

							%6$s

							%7$s
						</div>
						<!-- .et-pb-option-container -->
					</div><!-- .et-pb-option.et-pb-option--background -->
				</div>
			</div>
			<%% counter++;
			}); %%>',
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Background', 'et_builder' ),
			$tab_navs,
			$tab_color,
			$tab_gradient, // #5
			$tab_image,
			$tab_video
		);

		return $output;
	}

	function generate_columns_settings_padding() {
		$output = sprintf(
			'<%% var columns = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter = 1;
				_.each( columns, function ( column_type ) {
					var current_value_pt,
						current_value_pr,
						current_value_pb,
						current_value_pl,
						current_value_padding_tablet,
						current_value_padding_phone,
						has_tablet_padding,
						has_phone_padding;
					switch ( counter ) {
						%1$s
					}
			%%>',
			$this->generate_column_vars_padding()
		);

		$output .= sprintf(
			'<div class="et_pb_subtoggle_section">
				<div class="et-pb-option-toggle-content">
					<div class="et-pb-option">
						<label for="et_pb_padding_<%%= counter %%>">
							%1$s
							<%% if ( "4_4" !== column_type ) { %%>
								<%%= counter + " " %%>
							<%% } %%>
							%2$s:
						</label>
						<div class="et-pb-option-container">
						%7$s
							<div class="et_custom_margin_padding">
								<label>
									%3$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_top et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_top_<%%= counter %%>" name="et_pb_padding_top_<%%= counter %%>" value="<%%= current_value_pt %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_top et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_top et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<label>
									%4$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_right et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_right_<%%= counter %%>" name="et_pb_padding_right_<%%= counter %%>" value="<%%= current_value_pr %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_right et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_right et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<label>
									%5$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_bottom_<%%= counter %%>" name="et_pb_padding_bottom_<%%= counter %%>" value="<%%= current_value_pb %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<label>
									%6$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_left et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_left_<%%= counter %%>" name="et_pb_padding_left_<%%= counter %%>" value="<%%= current_value_pl %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_left et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_left et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<input type="hidden" class="et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_desktop et-pb-main-setting et_pb_setting_mobile_active" value="<%%= \'\' === current_value_pt && \'\' === current_value_pr && \'\' === current_value_pb && \'\' === current_value_pl ? \'\' : current_value_pt + \'|\' + current_value_pr + \'|\' + current_value_pb + \'|\' + current_value_pl %%>" data-device="desktop">
								<input type="hidden" class="et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_tablet et-pb-main-setting" id="et_pb_padding_<%%= counter %%>_tablet" name="et_pb_padding_<%%= counter %%>_tablet" value="<%%= current_value_padding_tablet %%>" data-device="tablet" data-has_saved_value="<%%= has_tablet_padding %%>">
								<input type="hidden" class="et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_phone et-pb-main-setting" id="et_pb_padding_<%%= counter %%>_phone" name="et_pb_padding_<%%= counter %%>_phone" value="<%%= current_value_padding_phone %%>" data-device="phone" data-has_saved_value="<%%= has_phone_padding %%>">
								<input id="et_pb_padding_<%%= counter %%>_last_edited" type="hidden" class="et_pb_mobile_last_edited_field" value="<%%= last_edited_padding_field %%>">
							</div> <!-- .et_custom_margin_padding -->
							<span class="et-pb-mobile-settings-toggle"></span>
							<span class="et-pb-reset-setting"></span>
						</div><!-- .et-pb-option-container -->
					</div><!-- .et-pb-option -->
				</div>
			</div>
			<%% counter++;
			}); %%>',
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Padding', 'et_builder' ),
			esc_html__( 'Top', 'et_builder' ),
			esc_html__( 'Right', 'et_builder' ),
			esc_html__( 'Bottom', 'et_builder' ), // #5
			esc_html__( 'Left', 'et_builder' ),
			et_pb_generate_mobile_options_tabs() // #7
		);

		return $output;
	}

	function generate_columns_settings_css() {
		$output = sprintf(
			'<%%
			var columns_css = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter_css = 1;

			_.each( columns_css, function ( column_type ) {
				var current_module_id_value,
					current_module_class_value,
					current_custom_css_before_value,
					current_custom_css_main_value,
					current_custom_css_after_value;
				switch ( counter_css ) {
					%1$s
				} %%>
				<div class="et_pb_subtoggle_section">
					<div class="et-pb-option-toggle-content">
						<div class="et-pb-option et-pb-option--custom_css">
							<label for="et_pb_custom_css_before_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%3$s:<span>.et_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%>:before</span>
							</label>

							<div class="et-pb-option-container et-pb-custom-css-option">
								<textarea id="et_pb_custom_css_before_<%%= counter_css %%>" class="et-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_before_value.replace( /\|\|/g, "\n" ) %%></textarea>
							</div><!-- .et-pb-option-container -->
						</div><!-- .et-pb-option -->

						<div class="et-pb-option et-pb-option--custom_css">
							<label for="et_pb_custom_css_main_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%4$s:<span>.et_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%></span>
							</label>

							<div class="et-pb-option-container et-pb-custom-css-option">
								<textarea id="et_pb_custom_css_main_<%%= counter_css %%>" class="et-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_main_value.replace( /\|\|/g, "\n" ) %%></textarea>
							</div><!-- .et-pb-option-container -->
						</div><!-- .et-pb-option -->

						<div class="et-pb-option et-pb-option--custom_css">
							<label for="et_pb_custom_css_after_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%5$s:<span>.et_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%>:after</span>
							</label>

							<div class="et-pb-option-container et-pb-custom-css-option">
								<textarea id="et_pb_custom_css_after_<%%= counter_css %%>" class="et-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_after_value.replace( /\|\|/g, "\n" ) %%></textarea>
							</div><!-- .et-pb-option-container -->
						</div><!-- .et-pb-option -->
					</div>
				</div>

			<%% counter_css++;
			}); %%>',
			$this->generate_column_vars_css(),
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Before', 'et_builder' ),
			esc_html__( 'Main Element', 'et_builder' ),
			esc_html__( 'After', 'et_builder' )
		);

		return $output;
	}

	function generate_columns_settings_css_fields() {
		$output = sprintf(
			'<%%
			var columns_css = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter_css = 1;

			_.each( columns_css, function ( column_type ) {
				var current_module_id_value,
					current_module_class_value;
				switch ( counter_css ) {
					%1$s
				} %%>
				<div class="et_pb_subtoggle_section">
					<div class="et-pb-option-toggle-content">
						<div class="et-pb-option et_pb_custom_css_regular">
							<label for="et_pb_module_id_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%3$s:
							</label>

							<div class="et-pb-option-container">
								<input id="et_pb_module_id_<%%= counter_css %%>" type="text" class="regular-text et_pb_custom_css_regular et-pb-main-setting" value="<%%= current_module_id_value %%>">
							</div><!-- .et-pb-option-container -->
						</div><!-- .et-pb-option -->

						<div class="et-pb-option et_pb_custom_css_regular">
							<label for="et_pb_module_class_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%4$s:
							</label>

							<div class="et-pb-option-container">
								<input id="et_pb_module_class_<%%= counter_css %%>" type="text" class="regular-text et_pb_custom_css_regular et-pb-main-setting" value="<%%= current_module_class_value %%>">
							</div><!-- .et-pb-option-container -->
						</div><!-- .et-pb-option -->
					</div>
				</div>
			<%% counter_css++;
			}); %%>',
			$this->generate_column_vars_css(),
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'CSS ID', 'et_builder' ),
			esc_html__( 'CSS Class', 'et_builder' )
		);

		return $output;
	}
}
