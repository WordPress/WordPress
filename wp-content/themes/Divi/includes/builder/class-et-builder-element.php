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

	// number of times shortcode_callback function has been executed
	private $_shortcode_callback_num;

	// priority number, applied to some CSS rules
	private $_style_priority;

	private static $styles = array();
	private static $media_queries = array();
	private static $modules_order;
	private static $parent_modules = array();
	private static $child_modules = array();
	private static $ab_tests_processed = array();
	private static $ab_tests_saved_id;
	private static $current_module_index = 0;
	private static $loading_backbone_templates = false;

	const DEFAULT_PRIORITY = 10;
	const HIDE_ON_MOBILE   = 'et-hide-mobile';

	function __construct() {
		self::$current_module_index++;

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

		$this->init();

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

	function process_whitelisted_fields() {
		$fields = array();

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
		 */
		$fields = ! is_admin() ? $this->whitelisted_fields : $this->get_fields();

		# update settings with defaults
		foreach ( $fields as $key => $settings ) {
			if ( ! isset( $fields_defaults[ $key ] ) ) {
				continue;
			}

			$settings = array_merge( $settings, $fields_defaults[ $key ] );

			$fields[ $key ] = $settings;
		}

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
			$shortcode_attributes[ $attribute_key ] = in_array( $attribute_key, $font_icon_options ) || preg_match( "/^\%\%\d+\%\%$/i", $attribute_value ) ? $attribute_value : str_replace( '%22', '"', $attribute_value );
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

		// mark current ab module as processed
		$this->ab_tests_processed[ $test_id ] = true;

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

	function _shortcode_callback( $atts, $content = null, $function_name ) {
		$this->shortcode_atts = shortcode_atts( $this->get_shortcode_fields(), $atts );

		$this->_decode_double_quotes();

		$this->_maybe_remove_default_atts_values();

		$global_shortcode_content = false;

		$ab_testing_enabled = et_is_ab_testing_active();

		$hide_subject_module = false;

		$post_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );

		// If the section/row/module is disabled, hide it
		if ( isset( $this->shortcode_atts['disabled'] ) && 'on' === $this->shortcode_atts['disabled'] ) {
			return;
		}

		// need to perform additional check and some modifications in case AB testing enabled
		if ( $ab_testing_enabled ) {
			// check if ab testing enabled for this module and if it shouldn't be displayed currently
			if ( ! $this->_is_display_module( $this->shortcode_atts ) && ! et_pb_detect_cache_plugins() ) {
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

		//override module attributes for global module
		if ( ! empty( $this->shortcode_atts['global_module'] ) ) {
			$global_content = et_pb_load_global_module( $this->shortcode_atts['global_module'] );

			if ( '' !== $global_content ) {
				$global_atts = shortcode_parse_atts( $global_content );

				foreach( $this->shortcode_atts as $single_attr => $value ) {
					if ( isset( $global_atts[$single_attr] ) ) {
						$this->shortcode_atts[$single_attr] = $global_atts[$single_attr];
					}
				}

				if ( false !== strpos( $this->shortcode_atts['saved_tabs'], 'general' ) || 'all' === $this->shortcode_atts['saved_tabs'] ) {
					$global_shortcode_content = et_pb_extract_shortcode_content( $global_content, $function_name );
				}
			}
		}

		self::set_order_class( $function_name );

		$this->pre_shortcode_content();

		$content = false !== $global_shortcode_content ? $global_shortcode_content : $content;

		$this->shortcode_content = ! ( isset( $this->is_structure_element ) && $this->is_structure_element ) ? do_shortcode( et_pb_fix_shortcodes( $content, $this->decode_entities ) ) : '';

		$this->shortcode_atts();

		$this->process_additional_options( $function_name );
		$this->process_custom_css_options( $function_name );

		$output = $this->shortcode_callback( $atts, $content, $function_name );

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
			if ( $global_setting_value === $shortcode_attr_value ) {
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
						if ( isset( $additional_options[ $name ][ $default_key ] ) ) {
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

		foreach ( $advanced_font_options as $option_name => $option_settings ) {
			$option_settings = wp_parse_args( $option_settings, array(
				'label'          => '',
				'font_size'      => array(),
				'letter_spacing' => array(),
				'font'           => array(),
			) );

			if ( ! isset( $option_settings['hide_font'] ) || ! $option_settings['hide_font'] ) {
				$additional_options["{$option_name}_font"] = wp_parse_args( $option_settings['font'], array(
					'label'           => sprintf( esc_html__( '%1$s Font', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'font',
					'option_category' => 'font_option',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => '',
				) );
			}

			if ( ! isset( $option_settings['hide_font_size'] ) || ! $option_settings['hide_font_size'] ) {
				$additional_options["{$option_name}_font_size"] = wp_parse_args( $option_settings['font_size'], array(
					'label'           => sprintf( esc_html__( '%1$s Font Size', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'option_category' => 'font_option',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => '',
					'mobile_options'  => true,
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
				) );

				$additional_options["{$option_name}_font_size_tablet"] = array(
					'type' => 'skip',
				);
				$additional_options["{$option_name}_font_size_phone"] = array(
					'type' => 'skip',
				);
			}

			$additional_options["{$option_name}_text_color"] = array(
				'label'           => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'color-alpha',
				'option_category' => 'font_option',
				'custom_color'    => true,
				'tab_slug'        => 'advanced',
			);

			if ( ! isset( $option_settings['hide_text_color'] ) || ! $option_settings['hide_text_color'] ) {
				$additional_options["{$option_name}_text_color"] = array(
					'label'           => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'color',
					'option_category' => 'font_option',
					'custom_color'    => true,
					'tab_slug'        => 'advanced',
				);
			}

			if ( ! isset( $option_settings['hide_letter_spacing'] ) || ! $option_settings['hide_letter_spacing'] ) {
				$additional_options["{$option_name}_letter_spacing"] = wp_parse_args( $option_settings['letter_spacing'], array(
					'label'           => sprintf( esc_html__( '%1$s Letter Spacing', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'mobile_options'  => true,
					'option_category' => 'font_option',
					'tab_slug'        => 'advanced',
					'default'         => '0px',
					'range_settings'  => array(
						'min'  => '0',
						'max'  => '100',
						'step' => '1',
					),
				) );

				$additional_options["{$option_name}_letter_spacing_tablet"] = array(
					'type' => 'skip',
				);
				$additional_options["{$option_name}_letter_spacing_phone"] = array(
					'type' => 'skip',
				);
			}

			if ( ! isset( $option_settings['hide_line_height'] ) || ! $option_settings['hide_line_height'] ) {
				$default_option_line_height = array(
					'label'           => sprintf( esc_html__( '%1$s Line Height', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'mobile_options'  => true,
					'option_category' => 'font_option',
					'tab_slug'        => 'advanced',
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

				$additional_options["{$option_name}_line_height_tablet"] = array(
					'type' => 'skip',
				);
				$additional_options["{$option_name}_line_height_phone"] = array(
					'type' => 'skip',
				);
			}

			if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] ) {
				$additional_options["{$option_name}_all_caps"] = array(
					'label'           => sprintf( esc_html__( '%1$s All Caps', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'yes_no_button',
					'option_category' => 'font_option',
					'options'         => array(
						'off' => esc_html__( 'Off', 'et_builder' ),
						'on'  => esc_html__( 'On', 'et_builder' ),
					),
					'shortcode_default' => $option_settings['defaults']['all_caps'],
					'tab_slug' => 'advanced',
				);
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_background_fields() {
		if ( ! isset( $this->advanced_options['background'] ) ) {
			return;
		}

		$additional_options = array();

		$color_type = isset( $this->advanced_options['background']['settings']['color'] ) && 'alpha' === $this->advanced_options['background']['settings']['color'] ? 'color-alpha' : 'color';
		$defaults = array(
			'use_background_color'  => true,
			'use_background_image' => true,
		);
		$this->advanced_options['background'] = wp_parse_args( $this->advanced_options['background'], $defaults );

		if ( $this->advanced_options['background']['use_background_color'] ) {
			$additional_options['background_color'] = array(
				'label'           => esc_html__( 'Background Color', 'et_builder' ),
				'type'            => $color_type,
				'option_category' => 'configuration',
				'custom_color'    => true,
				'tab_slug'        => 'advanced',
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
				'tab_slug'           => 'advanced',
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_border_fields () {
		if ( ! isset( $this->advanced_options['border'] ) ) {
			return;
		}

		$additional_options = array();

		$color_type = isset( $this->advanced_options['border']['settings']['color'] ) && 'alpha' === $this->advanced_options['border']['settings']['color'] ? 'color-alpha' : 'color';

		$additional_options['use_border_color'] = array(
			'label'           => esc_html__( 'Use Border', 'et_builder' ),
			'type'            => 'yes_no_button',
			'option_category' => 'layout',
			'options'         => array(
				'off' => esc_html__( 'No', 'et_builder' ),
				'on'  => esc_html__( 'Yes', 'et_builder' ),
			),
			'affects' => array(
				'#et_pb_border_color',
				'#et_pb_border_width',
				'#et_pb_border_style',
			),
			'shortcode_default' => 'off',
			'tab_slug'	       	=> 'advanced',
		);

		$additional_options['border_color'] = array(
			'label'             => esc_html__( 'Border Color', 'et_builder' ),
			'type'              => $color_type,
			'option_category'   => 'layout',
			'default'           => '#ffffff',
			'shortcode_default' => '#ffffff',
			'tab_slug'	       	=> 'advanced',
			'depends_default'   => true,
		);

		$additional_options['border_width'] = array(
			'label'             => esc_html__( 'Border Width', 'et_builder' ),
			'type'              => 'range',
			'option_category'   => 'layout',
			'default'           => '1px',
			'shortcode_default' => '1px',
			'tab_slug'          => 'advanced',
			'depends_default'   => true,
		);

		$additional_options['border_style'] = array(
			'label'             => esc_html__( 'Border Style', 'et_builder' ),
			'type'              => 'select',
			'option_category'   => 'layout',
			'options'           => et_builder_get_border_styles(),
			'shortcode_default' => 'solid',
			'tab_slug'          => 'advanced',
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

		if ( $this->advanced_options['custom_margin_padding']['use_margin'] ) {
			$additional_options['custom_margin'] = array(
				'label'           => esc_html__( 'Custom Margin', 'et_builder' ),
				'type'            => 'custom_margin',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
			);
			$additional_options['custom_margin_tablet'] = array(
				'type' => 'skip',
			);
			$additional_options['custom_margin_phone'] = array(
				'type' => 'skip',
			);

			// make it possible to override/add options
			if ( ! empty( $this->advanced_options['custom_margin_padding']['custom_margin'] ) ) {
				$additional_options['custom_margin'] = array_merge( $additional_options['custom_margin'], $this->advanced_options['custom_margin_padding']['custom_margin'] );
			}
		}

		if ( $this->advanced_options['custom_margin_padding']['use_padding'] ) {
			$additional_options['custom_padding'] = array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
			);
			$additional_options['custom_padding_tablet'] = array(
				'type' => 'skip',
			);
			$additional_options['custom_padding_phone'] = array(
				'type' => 'skip',
			);

			// make it possible to override/add options
			if ( ! empty( $this->advanced_options['custom_margin_padding']['custom_padding'] ) ) {
				$additional_options['custom_padding'] = array_merge( $additional_options['custom_padding'], $this->advanced_options['custom_margin_padding']['custom_padding'] );
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_button_fields() {
		if ( ! isset( $this->advanced_options['button'] ) ) {
			return;
		}

		$additional_options = array();

		foreach ( $this->advanced_options['button'] as $option_name => $option_settings ) {
			$additional_options["custom_{$option_name}"] = array(
				'label'           => sprintf( esc_html__( 'Use Custom Styles for %1$s ', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'yes_no_button',
				'option_category' => 'button',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects' => array(
					"#et_pb_{$option_name}_text_color",
					"#et_pb_{$option_name}_text_size",
					"#et_pb_{$option_name}_border_width",
					"#et_pb_{$option_name}_border_radius",
					"#et_pb_{$option_name}_letter_spacing",
					"#et_pb_{$option_name}_spacing",
					"#et_pb_{$option_name}_bg_color",
					"#et_pb_{$option_name}_border_color",
					"#et_pb_{$option_name}_use_icon",
					"#et_pb_{$option_name}_font",
					"#et_pb_{$option_name}_text_color_hover",
					"#et_pb_{$option_name}_bg_color_hover",
					"#et_pb_{$option_name}_border_color_hover",
					"#et_pb_{$option_name}_border_radius_hover",
					"#et_pb_{$option_name}_letter_spacing_hover",
				),
				'shortcode_default' => 'off',
				'tab_slug'	       	=> 'advanced',
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
				'tab_slug'        => 'advanced',
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
				'tab_slug'	       	=> 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_bg_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Background Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => ET_Global_Settings::get_value( 'all_buttons_bg_color' ),
				'shortcode_default' => '',
				'tab_slug'	       	=> 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_width"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Width', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_width' ),
				'shortcode_default' => '',
				'tab_slug'          => 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'	       	=> 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_radius"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Radius', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_radius' ),
				'shortcode_default' => '',
				'tab_slug'          => 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_letter_spacing"] = array(
				'label'             => sprintf( esc_html__( '%1$s Letter Spacing', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_spacing' ),
				'shortcode_default' => '',
				'tab_slug'          => 'advanced',
				'mobile_options'    => true,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_font"] = array(
				'label'           => sprintf( esc_html__( '%1$s Font', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'font',
				'option_category' => 'button',
				'tab_slug'        => 'advanced',
				'depends_default' => true,
			);

			$additional_options["{$option_name}_use_icon"] = array(
				'label'           => sprintf( esc_html__( 'Add %1$s Icon', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'select',
				'option_category' => 'button',
				'options'         => array(
					'default' => esc_html__( 'Default', 'et_builder' ),
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					"#et_pb_{$option_name}_icon_color",
					"#et_pb_{$option_name}_icon_placement",
					"#et_pb_{$option_name}_on_hover",
					"#et_pb_{$option_name}_icon",
				),
				'shortcode_default' => 'on',
				'tab_slug'	       	=> 'advanced',
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
				'tab_slug'            => 'advanced',
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_icon_color"] = array(
				'label'               => sprintf( esc_html__( '%1$s Icon Color', 'et_builder' ), $option_settings['label'] ),
				'type'                => 'color-alpha',
				'option_category'     => 'button',
				'custom_color'        => true,
				'default'             => '',
				'shortcode_default'   => '',
				'tab_slug'	       	  => 'advanced',
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
				'tab_slug'	       	  => 'advanced',
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_on_hover"] = array(
				'label'           => sprintf( esc_html__( 'Only Show Icon On Hover for %1$s', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'yes_no_button',
				'option_category' => 'button',
				'options'         => array(
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'shortcode_default'   => 'on',
				'tab_slug'	       	  => 'advanced',
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_text_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'	       	=> 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_bg_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Background Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'	       	=> 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Border Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'	       	=> 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_radius_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Border Radius', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_radius_hover' ),
				'shortcode_default' => '',
				'tab_slug'          => 'advanced',
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_letter_spacing_hover"] = array(
				'label'           => sprintf( esc_html__( '%1$s Hover Letter Spacing', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'range',
				'option_category' => 'button',
				'default'         => ET_Global_Settings::get_value( 'all_buttons_spacing_hover' ),
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_text_size_tablet"] = array(
				'type' => 'skip',
			);
			$additional_options["{$option_name}_text_size_phone"] = array(
				'type' => 'skip',
			);
			$additional_options["{$option_name}_letter_spacing_tablet"] = array(
				'type' => 'skip',
			);
			$additional_options["{$option_name}_letter_spacing_phone"] = array(
				'type' => 'skip',
			);
			$additional_options["{$option_name}_letter_spacing_hover_tablet"] = array(
				'type' => 'skip',
			);
			$additional_options["{$option_name}_letter_spacing_hover_phone"] = array(
				'type' => 'skip',
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
			$selector_output = isset( $option['selector'] ) ? str_replace( '%%order_class%%', $current_module_unique_class, $option['selector'] ) : '';
			$custom_css_fields[ "custom_css_{$slug}" ] = array(
				'label'    => sprintf(
					'%1$s:<span>%2$s%3$s%4$s</span>',
					$option['label'],
					$main_css_element_output,
					! isset( $option['no_space_before_selector'] ) && isset( $option['selector'] ) ? ' ' : '',
					$selector_output
				),
				'type'     => 'custom_css',
				'tab_slug' => 'custom_css',
				'no_colon' => true,
			);

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
	function process_fields( $fields ) { return $fields; }

	// intended to be overridden as needed
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

		$output = sprintf(
			'%6$s<div class="et-pb-option%1$s%2$s%3$s%8$s%9$s"%4$s tabindex="-1">%5$s</div> <!-- .et-pb-option -->%7$s',
			( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' et-pb-option-main-content' : '' ),
			( ( $depends || isset( $field['depends_default'] ) ) ? ' et-pb-depends' : '' ),
			( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' et_pb_hidden' : '' ),
			( $depends ? $depends_attr : '' ),
			"\n\t\t\t\t" . $option_output . "\n\t\t\t",
			"\t",
			"\n\n\t\t",
			( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' et-pb-option-%1$s', $field['name'] ) ) : '' ),
			( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' )
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
				'%3$s<div class="et-pb-option-container%5$s">
					%1$s
					%2$s
				%4$s</div> <!-- .et-pb-option-container -->',
				$field_el,
				$description,
				"\n\n\t\t\t\t",
				"\t",
				( isset( $field['type'] ) && 'custom_css' === $field['type'] ? ' et-pb-custom-css-option' : '' )
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

	function get_field_name( $field ) {
		// Don't add 'et_pb_' prefix to the "Admin Label" field
		if ( 'admin_label' === $field['name'] ) {
			return $field['name'];
		}

		return sprintf( 'et_pb_%s', $field['name'] );
	}

	function render_field( $field ) {
		$classes = array();
		$hidden_field = '';
		$is_custom_color = isset( $field['custom_color'] ) && $field['custom_color'];
		$reset_button_html = '<span class="et-pb-reset-setting"></span>';
		$need_mobile_options = isset( $field['mobile_options'] ) && $field['mobile_options'] ? true : false;

		if ( $need_mobile_options ) {
			$mobile_settings_tabs = et_pb_generate_mobile_options_tabs();
		}

		if ( 'select' !== $field['type'] ) {
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

		$value_html = ' value="<%%- typeof( %1$s ) !== \'undefined\' ?  %2$s : \'%3$s\' %%>" ';
		$value = sprintf(
			$value_html,
			esc_attr( $field_name ),
			esc_attr( $field_name ),
			$default
		);

		$attributes = '';
		if ( ! empty( $field['attributes'] ) ) {
			if ( is_array( $field['attributes'] )  ) {
				foreach( $field['attributes'] as $attribute_key => $attribute_value ) {
					$attributes .= ' ' . esc_attr( $attribute_key ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			} else {
				$attributes = ' '.$field['attributes'];
			}
		}

		if ( ! empty( $field['affects'] ) ) {
			$field['class'] .= ' et-pb-affects';
			$attributes .= sprintf( ' data-affects="%s"', esc_attr( implode( ', ', $field['affects'] ) ) );
		}

		if ( 'font' === $field['type'] ) {
			$field['class'] .= ' et-pb-font-select';
		}

		if ( in_array( $field['type'], array( 'font', 'hidden', 'multiple_checkboxes' ) ) ) {
			$hidden_field = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="et-pb-main-setting %3$s" data-default="%4$s" %5$s %6$s/>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $default ),
				$value,
				$attributes
			);
		}

		foreach ( $this->get_validation_attr_rules() as $rule ) {
			if ( ! empty( $field[ $rule ] ) ) {
				$this->validation_in_use = true;
				$attributes .= ' data-rule-' . esc_attr( $rule ). '="' . esc_attr( $field[ $rule ] ) . '"';
			}
		}

		switch( $field['type'] ) {
			case 'tiny_mce':
				if ( ! empty( $field['tiny_mce_html_mode'] ) ) {
					$field['class'] .= ' html_mode';
				}

				$main_content_property_name = $main_content_field_name = 'et_pb_content_new';

				if ( isset( $this->type ) && 'child' === $this->type ) {
					$main_content_property_name = "data.{$main_content_property_name}";
				}

				$field_el = sprintf(
					'<div id="%1$s"><%%= typeof( %2$s ) !== \'undefined\' ? %2$s : \'\' %%></div>',
					esc_attr( $main_content_field_name ),
					esc_html( $main_content_property_name )
				);

				break;
			case 'textarea':
			case 'custom_css':
				$field_custom_value = esc_html( $field_name );
				if ( 'custom_css' === $field['type'] ) {
					$field_custom_value .= '.replace( /\|\|/g, "\n" )';
				}

				if ( 'et_pb_raw_content' === $field_name ) {
					$field_custom_value = sprintf( '_.unescape( %1$s )', $field_custom_value );
				}

				$field_el = sprintf(
					'<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>',
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					esc_html( $field_name ),
					$field_custom_value
				);
				break;
			case 'select':
			case 'yes_no_button':
			case 'font':
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

				$field_el = $this->render_select( $field_name, $field['options'], $field['id'], $field['class'], $attributes, $field['type'], $button_options );

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
				break;
			case 'color':
			case 'color-alpha':
				$field['default'] = ! empty( $field['default'] ) ? $field['default'] : '';

				if ( $is_custom_color && ( ! isset( $field['default'] ) || '' === $field['default'] ) ) {
					$field['default'] = '';
				}

				$default = ! empty( $field['default'] ) && ! $is_custom_color ? sprintf( ' data-default-color="%s"', $field['default'] ) : '';

				$color_id = sprintf( ' id="%1$s"', esc_attr( $field['id'] ) );
				$color_value_html = '<%%- typeof( %1$s ) !== \'undefined\' && %1$s !== \'\' ? %1$s : \'%2$s\' %%>';
				$main_color_value = sprintf( $color_value_html, esc_attr( $field_name ), $field['default'] );
				$hidden_color_value = sprintf( $color_value_html, esc_attr( $field_name ), '' );

				$field_el = sprintf(
					'<input%1$s class="et-pb-color-picker-hex%5$s%8$s" type="text"%6$s%7$s placeholder="%9$s" data-selected-value="%2$s" value="%2$s"%3$s />
					%4$s',
					( ! $is_custom_color ? $color_id : '' ),
					$main_color_value,
					$default,
					( ! empty( $field['additional_code'] ) ? $field['additional_code'] : '' ),
					( 'color-alpha' === $field['type'] ? ' et-pb-color-picker-hex-alpha' : '' ),
					( 'color-alpha' === $field['type'] ? ' data-alpha="true"' : '' ),
					( 'color' === $field['type'] ? ' maxlength="7"' : '' ),
					( ! $is_custom_color ? ' et-pb-main-setting' : '' ),
					esc_attr__( 'Hex Value', 'et_builder' )
				);

				if ( $is_custom_color ) {
					$field_el = sprintf(
						'<span class="et-pb-custom-color-button et-pb-choose-custom-color-button"><span>%1$s</span></span>
						<div class="et-pb-custom-color-container et_pb_hidden">
							%2$s
							<input%3$s class="et-pb-main-setting et-pb-custom-color-picker" type="hidden" value="%4$s" />
							%5$s
						</div> <!-- .et-pb-custom-color-container -->',
						esc_html__( 'Choose Custom Color', 'et_builder' ),
						$field_el,
						$color_id,
						$hidden_color_value,
						$reset_button_html
					);
				}
				break;
			case 'upload':
				$field_data_type = ! empty( $field['data_type'] ) ? $field['data_type'] : 'image';
				$field['upload_button_text'] = ! empty( $field['upload_button_text'] ) ? $field['upload_button_text'] : esc_attr__( 'Upload', 'et_builder' );
				$field['choose_text'] = ! empty( $field['choose_text'] ) ? $field['choose_text'] : esc_attr__( 'Choose image', 'et_builder' );
				$field['update_text'] = ! empty( $field['update_text'] ) ? $field['update_text'] : esc_attr__( 'Set image', 'et_builder' );
				$field['classes'] = ! empty( $field['classes'] ) ? ' ' . $field['classes'] : '';
				$field_additional_button = ! empty( $field['additional_button'] ) ? "\n\t\t\t\t\t" . $field['additional_button'] : '';

				$field_el = sprintf(
					'<input id="%1$s" type="text" class="et-pb-main-setting regular-text et-pb-upload-field%8$s" value="<%%- typeof( %2$s ) !== \'undefined\' ? %2$s : \'\' %%>" />
					<input type="button" class="button button-upload et-pb-upload-button" value="%3$s" data-choose="%4$s" data-update="%5$s" data-type="%6$s" />%7$s',
					esc_attr( $field['id'] ),
					esc_attr( $field_name ),
					esc_attr( $field['upload_button_text'] ),
					esc_attr( $field['choose_text'] ),
					esc_attr( $field['update_text'] ),
					esc_attr( $field_data_type ),
					$field_additional_button,
					esc_attr( $field['classes'] )
				);
				break;
			case 'checkbox':
				$field_el = sprintf(
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

				$field_el = $checkboxes_set . $hidden_field . '</div>';
				break;
			case 'hidden':
				$field_el = $hidden_field;
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

				$field_el = sprintf(
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
			case 'date_picker':
			case 'range':
			default:
				$validate_number = isset( $field['number_validation'] ) && $field['number_validation'] ? true : false;

				if ( 'date_picker' === $field['type'] ) {
					$field['class'] .= ' et-pb-date-time-picker';
				}

				$field['class'] .= 'range' === $field['type'] ? ' et-pb-range-input' : ' et-pb-main-setting';

				$field_el = sprintf(
					'<input id="%1$s" type="text" class="%2$s%5$s%9$s"%6$s%3$s%8$s%10$s %4$s/>%7$s',
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
					$need_mobile_options ? ' data-device="desktop"' : ''
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

						$additional_fields .= sprintf( '<input id="%2$s" type="text" class="%3$s%5$s et_pb_setting_mobile et_pb_setting_mobile_%9$s"%6$s%8$s%1$s data-device="%9$s" %4$s%10$s/>%7$s',
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
							$has_saved_value // #10
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
						'<input type="range" class="et-pb-main-setting et-pb-range%4$s" data-default="%2$s"%1$s%3$s%5$s />',
						$value,
						esc_attr( $default ),
						$range_settings_html,
						$need_mobile_options ? ' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active' : '',
						$need_mobile_options ? ' data-device="desktop"' : ''
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
								'<input type="range" class="et-pb-main-setting et-pb-range et_pb_setting_mobile et_pb_setting_mobile_%3$s" data-default="%1$s"%4$s%2$s data-device="%3$s"%5$s/>',
								esc_attr( $default ),
								$range_settings_html,
								esc_attr( $device_type ),
								$value_mobile_range,
								$has_saved_value
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

		if ( $need_mobile_options ) {
			$field_el = $mobile_settings_tabs . "\n" . $field_el;
			$field_el .= '<span class="et-pb-mobile-settings-toggle"></span>';
		}

		if ( isset( $field['type'] ) && isset( $field['tab_slug'] ) && 'advanced' === $field['tab_slug'] && ! $is_custom_color ) {
			$field_el .= $reset_button_html;
		}

		return "\t" . $field_el;
	}

	function render_select( $name, $options, $id = '', $class = '', $attributes = '', $field_type = '', $button_options = array() ) {
		$options_output = '';

		if ( 'font' === $field_type ) {
			$options_output = '<%= window.et_builder.fonts_template() %>';
		} else {
			foreach ( $options as $option_value => $option_label ) {
				$data = '';
				if ( is_array( $option_label ) ) {
					if ( isset( $option_label['data'] ) ) {
						$data_key_name = key( $option_label['data'] );
						$data = sprintf(
							' data-%1$s="%2$s"',
							esc_html( $data_key_name ),
							esc_attr( $option_label['data'][ $data_key_name ] )
						);
					}
					$option_label = $option_label['value'];
				}
				$selected_attr = '<%- typeof( ' . esc_attr( $name ) . ' ) !== \'undefined\' && \'' . esc_attr( $option_value ) . '\' === ' . esc_attr( $name ) . ' ?  \' selected="selected"\' : \'\' %>';
				$options_output .= sprintf(
					'%4$s<option%5$s value="%1$s"%2$s>%3$s</option>',
					esc_attr( $option_value ),
					$selected_attr,
					esc_html( $option_label ),
					"\n\t\t\t\t\t\t",
					( '' !== $data ? $data : '' )
				);
			}
			$class = rtrim( 'et-pb-main-setting ' . $class );
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
		return $output;
	}

	function get_main_tabs() {
		$tabs = array(
			'general'    => esc_html__( 'General Settings', 'et_builder' ),
			'advanced'   => esc_html__( 'Advanced Design Settings', 'et_builder' ),
			'custom_css' => esc_html__( 'Custom CSS', 'et_builder' ),
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

		foreach( $all_fields as $field_name => $field ) {
			if ( ! empty( $field['type'] ) && 'skip' == $field['type'] ) {
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
				( ! et_pb_is_allowed( 'edit_fonts' ) && ! empty( $field['option_category'] ) && 'font_option' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_buttons' ) && ! empty( $field['option_category'] ) && 'button' === $field['option_category'] )
			) {
				continue;
			}

			$option_output = '';
			$option_output .= $this->wrap_settings_option_label( $field );
			$option_output .= $this->wrap_settings_option_field( $field );

			$tab_slug = ! empty( $field['tab_slug'] ) ? $field['tab_slug'] : 'general';
			$is_toggle_option = isset( $field['toggle_slug'] ) && $toggles_used && isset( $this->options_toggles[ $tab_slug ] );
			$toggle_slug = $is_toggle_option ? $field['toggle_slug'] : $toggle_all_options_slug;
			$tabs_output[ $tab_slug ][ $toggle_slug ][] = $this->wrap_settings_option( $option_output, $field );

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
					foreach ( $this->options_toggles[ $tab_slug ]['toggles'] as $toggle_slug => $toggle_heading ) {
						$i++;
						$toggle_output = '';
						$is_accordion_disabled = isset( $this->options_toggles[ $tab_slug ]['settings']['toggles_disabled'] ) && $this->options_toggles[ $tab_slug ]['settings']['toggles_disabled'] ? true : false;

						foreach ( $tabs_output[ $tab_slug ][ $toggle_slug ] as $toggle_option_output ) {
							$toggle_output .= $toggle_option_output;
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
							( $is_accordion_disabled ? ' et-pb-options-toggle-disabled' : ' et-pb-options-toggle-enabled' ),
							( 1 === $i && ! $is_accordion_disabled ? ' et-pb-option-toggle-content-open' : '' )
						);

						$tab_output .= $toggle_output;
					}
				}

				if ( isset( $tabs_output[ $tab_slug ][ $toggle_all_options_slug ] ) ) {
					foreach ( $tabs_output[ $tab_slug ][ $toggle_all_options_slug ] as $no_toggle_option_output ) {
						$tab_output .= $no_toggle_option_output;
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
					<div id="et_pb_content_new"><%%= typeof( et_pb_content_new )!== \'undefined\' && \'\' !== et_pb_content_new.trim() ? et_pb_content_new.replace( /%%22/g, \'||\' ) : \'%7$s\' %%></div>
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
		$fields['ab_subject_id'] = '';
		$fields['ab_goal'] = '';

		return $fields;
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
						<%%- %2$s %%>
					<%% } else if ( typeof( %3$s ) !== \'undefined\' && typeof( %3$s ) === \'string\' && %3$s !== \'\' ) { %%>
						<%%- %3$s %%>
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

		foreach ( $this->advanced_options['fonts'] as $option_name => $option_settings ) {
			$style = '';
			$important_options = array();
			$is_important_set = isset( $option_settings['css']['important'] );
			$is_placeholder = isset( $option_settings['css']['placeholder'] );

			$use_global_important = $is_important_set && 'all' === $option_settings['css']['important'];
			if ( $is_important_set && is_array( $option_settings['css']['important'] ) ) {
				$important_options = $option_settings['css']['important'];
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

				$style .= et_builder_set_element_font( $font_options["{$option_name}_{$slugs[0]}"], ( '' !== $important ), $global_setting_value );
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
				$important = ' !important';

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

			$letter_spacing_option_name = "{$option_name}_{$slugs[3]}";
			$default_letter_spacing     = isset( $this->fields_unprocessed[ $letter_spacing_option_name ]['default'] ) ? $this->fields_unprocessed[ $letter_spacing_option_name ]['default'] : '';

			if ( isset( $font_options[ $letter_spacing_option_name ] ) && ! in_array( trim( $font_options[ $letter_spacing_option_name ] ), array( '', 'px', $default_letter_spacing ) ) ) {
				$important = in_array( 'letter-spacing', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf(
					'letter-spacing: %1$s%2$s; ',
					esc_html( et_builder_process_range_value( $font_options[ $letter_spacing_option_name ] ) ),
					esc_html( $important )
				);
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
					$current_media_query = false === strpos( $mobile_option, 'phone' ) ? 'max_width_980' : 'max_width_767';
					$main_option_name = str_replace( array( '_tablet', '_phone' ), '', $mobile_option );
					$css_property = str_replace( '_', '-', $main_option_name );
					$important = in_array( $css_property, $important_options ) || $use_global_important ? ' !important' : '';

					// Allow specific selector tablet and mobile, simply add _tablet or _phone suffix
					if ( isset( $option_settings['css'][ $mobile_option ] ) && "" !== $option_settings['css'][ $mobile_option ] ) {
						$selector = $option_settings['css'][ $mobile_option ];
					} elseif ( isset( $option_settings['css'][ $main_option_name ] ) || isset( $option_settings['css']['main'] ) ) {
						$selector = isset( $option_settings['css'][ $main_option_name ] ) ? $option_settings['css'][ $main_option_name ] : $option_settings['css']['main'];
					} else {
						$selector = $this->main_css_element;
					}

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

	function process_advanced_background_options( $function_name ) {
		if ( ! isset( $this->advanced_options['background'] ) ) {
			return;
		}

		$style = '';
		$settings = $this->advanced_options['background'];
		$important = isset( $settings['css']['use_important'] ) && $settings['css']['use_important'] ? ' !important' : '';

		if ( $this->advanced_options['background']['use_background_color'] ) {
			$background_color = $this->shortcode_atts['background_color'];

			if ( '' !== $background_color ) {
				$style .= sprintf(
					'background-color: %1$s%2$s; ',
					esc_html( $background_color ),
					esc_html( $important )
				);
			}
		}

		if ( $this->advanced_options['background']['use_background_image'] ) {
			$background_image = $this->shortcode_atts['background_image'];

			if ( '' !== $background_image ) {
				$style .= sprintf(
					'background-image: url(%1$s)%2$s; ',
					esc_html( $background_image ),
					esc_html( $important )
				);
			}
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
		$border_style     = $this->shortcode_atts['border_style'];
		$border_color     =	'' !== $this->shortcode_atts['border_color'] ? $this->shortcode_atts['border_color'] : $this->fields_unprocessed['border_color']['default'];
		$border_width     = '' !== $this->shortcode_atts['border_width'] ? $this->shortcode_atts['border_width'] : $this->fields_unprocessed['border_width']['default'];
		$important        = isset( $settings['css']['important'] ) ? '!important' : '';

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
		$custom_margin_mobile = $this->advanced_options['custom_margin_padding']['use_margin'] && ( isset( $this->shortcode_atts['custom_margin_tablet'] ) || isset( $this->shortcode_atts['custom_margin_phone'] ) )
			? array (
				'tablet' => isset( $this->shortcode_atts['custom_margin_tablet'] ) ? $this->shortcode_atts['custom_margin_tablet'] : '',
				'phone' => isset( $this->shortcode_atts['custom_margin_phone'] ) ? $this->shortcode_atts['custom_margin_phone'] : '',
			)
			: '';
		$custom_padding_mobile = $this->advanced_options['custom_margin_padding']['use_padding'] && ( isset( $this->shortcode_atts['custom_padding_tablet'] ) || isset( $this->shortcode_atts['custom_padding_phone'] ) )
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
			$button_text_color                  = $this->shortcode_atts["{$option_name}_text_color"];
			$button_bg_color                    = $this->shortcode_atts["{$option_name}_bg_color"];
			$button_border_width                = $this->shortcode_atts["{$option_name}_border_width"];
			$button_border_color                = $this->shortcode_atts["{$option_name}_border_color"];
			$button_border_radius               = $this->shortcode_atts["{$option_name}_border_radius"];
			$button_font                        = $this->shortcode_atts["{$option_name}_font"];
			$button_letter_spacing              = $this->shortcode_atts["{$option_name}_letter_spacing"];
			$button_letter_spacing_tablet       = $this->shortcode_atts["{$option_name}_letter_spacing_tablet"];
			$button_letter_spacing_phone        = $this->shortcode_atts["{$option_name}_letter_spacing_phone"];
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

			if ( 'on' === $button_custom ) {
				$button_text_size = '' === $button_text_size || 'px' === $button_text_size ? '20px' : $button_text_size;
				$button_text_size = '' !== $button_text_size && false === strpos( $button_text_size, 'px' ) ? $button_text_size . 'px' : $button_text_size;

				$css_element = ! empty( $option_settings['css']['main'] ) ? $option_settings['css']['main'] : $this->main_css_element . ' .et_pb_button';
				$css_element_processed = et_is_builder_plugin_active() ? $css_element : 'body #page-container ' . $css_element;

				if ( '' !== $button_bg_color && et_is_builder_plugin_active() ) {
					$button_bg_color .= ' !important';
				}

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
					'' !== $button_border_radius && 'px' !== $button_border_radius ? sprintf( 'border-radius:%1$s;', et_builder_process_range_value( $button_border_radius ) ) : '',
					'' !== $button_letter_spacing && 'px' !== $button_letter_spacing ? sprintf( 'letter-spacing:%1$s;', et_builder_process_range_value( $button_letter_spacing ) ) : '',
					'' !== $button_text_size && 'px' !== $button_text_size ? sprintf( 'font-size:%1$s;', et_builder_process_range_value( $button_text_size ) ) : '',
					'' !== $button_font ? et_builder_set_element_font( $button_font, true ) : '',
					'off' === $button_on_hover ?
						sprintf( 'padding-left:%1$s; padding-right: %2$s;',
							'left' === $button_icon_placement ? '2em' : '0.7em',
							'left' === $button_icon_placement ? '0.7em' : '2em'
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
					'' !== $button_border_radius_hover ? sprintf( 'border-radius:%1$s;', et_builder_process_range_value( $button_border_radius_hover ) ) : '',
					'' !== $button_letter_spacing_hover ? sprintf( 'letter-spacing:%1$spx;', $button_letter_spacing_hover ) : '',
					'off' === $button_on_hover ?
						''
						:
						sprintf( 'padding-left:%1$s; padding-right: %2$s;',
							'left' === $button_icon_placement ? '2em' : '0.7em',
							'left' === $button_icon_placement ? '0.7em' : '2em'
						)
				);

				self::set_style( $function_name, array(
					'selector'    => $css_element_processed . ':hover',
					'declaration' => rtrim( $main_element_styles_hover ),
				) );

				if ( 'off' === $button_use_icon ) {
					$main_element_styles_after = 'display:none !important;';
					$no_icon_styles = 'padding: 0.3em 1em !important;';

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
						sprintf( 'opacity:%1$s;', 'on' === $button_on_hover ? '0' : '1' ),
						'off' !== $button_on_hover && '' !== $button_icon_code ?
							sprintf( 'margin-left:%1$s;left:%2$s;',
								'left' === $button_icon_placement ? '0' : '-1em',
								'left' === $button_icon_placement ? '1em' : 'auto'
							)
							: '',
						'off' === $button_on_hover ?
							sprintf( 'margin-left:%1$s;left:%2$s;',
								'left' === $button_icon_placement ? '0' : '.3em',
								'left' === $button_icon_placement ? '0.15em' : 'auto'
							)
							: '',
						'on' === $button_use_icon ? 'display: inline-block;' : ''

					);

					$hover_after_styles = sprintf(
						'%1$s
						%2$s
						%3$s',
						'' !== $button_icon_code ?
							sprintf( 'margin-left:%1$s;', '35' !== $button_icon_code ? '.3em' : '0' )
							: '',
							'' !== $button_icon_code ?
								sprintf( 'left:%1$s;margin-left:%2$s;',
									'left' === $button_icon_placement ? '0.15em' : 'auto',
									'35' !== $button_icon_code ? '.3em' : '0'
								)
							: '',
						'on' === $button_on_hover ? 'opacity: 1;' : ''
					);

					self::set_style( $function_name, array(
						'selector'    => $css_element_processed . ':hover:after',
						'declaration' => rtrim( $hover_after_styles ),
					) );

					if ( '' === $button_icon ) {
						$default_icons_size = $int_font_size * 1.6 . 'px';
						$custom_icon_size = $button_text_size;

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':after',
							'declaration' => sprintf( 'font-size:%1$s;', $default_icons_size ),
						) );

						self::set_style( $function_name, array(
							'selector'    => 'body.et_button_custom_icon #page-container ' . $css_element . ':after',
							'declaration' => sprintf( 'font-size:%1$s;', $custom_icon_size ),
						) );
					}
				}

				foreach( array( 'tablet', 'phone' ) as $device ) {
					$current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
					$current_text_size = 'tablet' === $device ? et_builder_process_range_value( $button_text_size_tablet ) : et_builder_process_range_value( $button_text_size_phone );
					$current_letter_spacing = 'tablet' === $device ? et_builder_process_range_value( $button_letter_spacing_tablet ) : et_builder_process_range_value( $button_letter_spacing_phone );
					$current_letter_spacing_hover = 'tablet' === $device ? et_builder_process_range_value( $button_letter_spacing_hover_tablet ) : et_builder_process_range_value( $button_letter_spacing_hover_phone );

					if ( ( '' !== $current_text_size && '0px' !== $current_text_size ) || '' !== $current_letter_spacing ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ',' . $css_element_processed . ':after',
							'declaration' => sprintf(
								'%1$s
								%2$s',
								'' !== $current_text_size && '0px' !== $current_text_size ? sprintf( 'font-size:%1$s !important;', $current_text_size ) : '',
								'' !== $current_letter_spacing ? sprintf( 'letter-spacing:%1$s;', $current_letter_spacing ) : ''
							),
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );
					}

					if ( '' !== $current_letter_spacing_hover ) {
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
					'selector'    => $css_element_processed . ':after',
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
			$selector = ! empty( $option['selector'] ) ? $option['selector'] : '';

			if ( false === strpos( $selector, '%%order_class%%' ) ) {
				if ( ! ( isset( $option['no_space_before_selector'] ) && $option['no_space_before_selector'] ) && '' !== $selector ) {
					$selector = " {$selector}";
				}

				$selector = "%%order_class%%{$selector}";
			}

			if ( '' !== $css ) {
				self::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => trim( $css ),
				) );
			}
		}
	}

	static function compare_by_priority( $a, $b ) {
		$a_priority = ! empty( $a['priority'] ) ? (int) $a['priority'] : self::DEFAULT_PRIORITY;
		$b_priority = ! empty( $b['priority'] ) ? (int) $b['priority'] : self::DEFAULT_PRIORITY;

		if ( isset( $a['_order_number'], $b['_order_number'] ) && ( $a_priority === $b_priority ) ) {
			return $a['_order_number'] - $b['_order_number'];
		}

		return $a_priority - $b_priority;
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

	static function get_modules_array( $post_type = '' ) {
		$modules = array();

		if ( ! empty( $post_type ) ) {
			$parent_modules = self::get_parent_modules( $post_type );
			if ( ! empty( $parent_modules ) ) {
				$sorted_modules = $parent_modules;
			}
		} else {
			$parent_modules = self::get_parent_modules();
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
				/**
				 * Replace single and double quotes with %% and || respectively
				 * to avoid js conflicts
				 */
				$module_name = str_replace( '"', '%%', $module->name );
				$module_name = str_replace( "'", '||', $module_name );

				$_module = array(
					'title' => esc_attr( $module_name ),
					'label' => esc_attr( $module->slug ),
				);

				if ( isset( $module->fullwidth ) && $module->fullwidth ) {
					$_module['fullwidth_only'] = 'on';
				}

				$modules[] = $_module;
			}
		}

		return $modules;
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

	static function set_media_queries() {
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

		self::$media_queries = apply_filters( 'et_builder_media_queries', $media_queries );
	}

	static function get_media_query( $name ) {
		if ( ! isset( self::$media_queries[ $name ] ) ) {
			return false;
		}

		return self::$media_queries[ $name ];
	}

	static function get_style() {
		if ( empty( self::$styles ) ) {
			return false;
		}

		$output = '';

		$styles_by_media_queries = self::$styles;
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

	static function set_style( $function_name, $style ) {
		$order_class_name = self::get_module_order_class( $function_name );

		// Prepend .et_divi_builder class before all CSS rules in the Divi Builder plugin
		if ( et_is_builder_plugin_active() ) {
			$order_class_name = "et_divi_builder #et_builder_outer_content .$order_class_name";
		}

		$selector    = str_replace( '%%order_class%%', ".{$order_class_name}", $style['selector'] );
		$selector    = str_replace( '%order_class%', ".{$order_class_name}", $selector );
		$selector    = apply_filters( 'et_pb_set_style_selector', $selector, $function_name );

		$declaration = $style['declaration'];
		// New lines are saved as || in CSS Custom settings, remove them
		$declaration = preg_replace( '/(\|\|)/i', '', $declaration );

		$media_query = isset( $style[ 'media_query' ] ) ? $style[ 'media_query' ] : 'general';

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

	static function get_module_order_class( $function_name ) {
		if ( ! isset( self::$modules_order[ $function_name ] ) ) {
			return false;
		}

		$shortcode_order_num = self::$modules_order[ $function_name ];

		$order_class_name = sprintf( '%1$s_%2$s', $function_name, $shortcode_order_num );

		return $order_class_name;
	}

	static function set_order_class( $function_name ) {
		if ( ! isset( self::$modules_order ) ) {
			self::$modules_order = array();
		}

		self::$modules_order[ $function_name ] = isset( self::$modules_order[ $function_name ] ) ? (int) self::$modules_order[ $function_name ] + 1 : 0;
	}

	static function add_module_order_class( $module_class, $function_name ) {
		$order_class_name = self::get_module_order_class( $function_name );

		return "{$module_class} {$order_class_name}";
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
		if ( ! empty( $field['type'] ) && 'column_settings' == $field['type'] ) {
			$output = $this->generate_columns_settings();
		} elseif ( ! empty( $field['type'] ) && 'column_settings_css_fields' == $field['type'] ) {
			$output = $this->generate_columns_settings_css_fields();
		} elseif ( ! empty( $field['type'] ) && 'column_settings_css' == $field['type'] ) {
			$output = $this->generate_columns_settings_css();
		} else {
			$depends = false;
			if ( isset( $field['depends_show_if'] ) || isset( $field['depends_show_if_not'] ) ) {
				$depends = true;
				if ( isset( $field['depends_show_if_not'] ) ) {
					$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $field['depends_show_if_not'] ) );
				} else {
					$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $field['depends_show_if'] ) );
				}
			}

			$output = sprintf(
				'%6$s<div class="et-pb-option%1$s%2$s%3$s%8$s%9$s"%4$s>%5$s</div> <!-- .et-pb-option -->%7$s',
				( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' et-pb-option-main-content' : '' ),
				( ( $depends || isset( $field['depends_default'] ) ) ? ' et-pb-depends' : '' ),
				( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' et_pb_hidden' : '' ),
				( $depends ? $depends_attr : '' ),
				"\n\t\t\t\t" . $option_output . "\n\t\t\t",
				"\t",
				"\n\n\t\t",
				( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' et-pb-option-%1$s', $field['name'] ) ) : '' ),
				( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' )
			);
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
					current_custom_css_after_value = typeof et_pb_custom_css_after_%1$s !== \'undefined\' ? et_pb_custom_css_after_%1$s : \'\'
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_column_vars() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_value_bg = typeof et_pb_background_color_%1$s !== \'undefined\' ? et_pb_background_color_%1$s : \'\',
					current_value_pt = typeof et_pb_padding_top_%1$s !== \'undefined\' ? et_pb_padding_top_%1$s : \'\',
					current_value_pr = typeof et_pb_padding_right_%1$s !== \'undefined\' ? et_pb_padding_right_%1$s : \'\',
					current_value_pb = typeof et_pb_padding_bottom_%1$s !== \'undefined\' ? et_pb_padding_bottom_%1$s : \'\',
					current_value_pl = typeof et_pb_padding_left_%1$s !== \'undefined\' ? et_pb_padding_left_%1$s : \'\',
					current_value_padding_tablet = typeof et_pb_padding_%1$s_tablet !== \'undefined\' ? et_pb_padding_%1$s_tablet : \'\',
					current_value_padding_phone = typeof et_pb_padding_%1$s_phone !== \'undefined\' ? et_pb_padding_%1$s_phone : \'\',
					last_edited_padding_field = typeof et_pb_padding_%1$s_last_edited !== \'undefined\' ?  et_pb_padding_%1$s_last_edited : \'\',
					has_tablet_padding = typeof et_pb_padding_%1$s_tablet !== \'undefined\' ? \'yes\' : \'no\',
					has_phone_padding = typeof et_pb_padding_%1$s_phone !== \'undefined\' ? \'yes\' : \'no\',
					current_value_bg_img = typeof et_pb_bg_img_%1$s !== \'undefined\' ? et_pb_bg_img_%1$s : \'\';
					current_value_parallax = typeof et_pb_parallax_%1$s !== \'undefined\' && \'on\' === et_pb_parallax_%1$s ? \' selected="selected"\' : \'\';
					current_value_parallax_method = typeof et_pb_parallax_method_%1$s !== \'undefined\' && \'on\' === et_pb_parallax_method_%1$s ? \' selected="selected"\' : \'\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_columns_settings() {
		$output = sprintf(
			'<%% var columns = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter = 1;
				_.each( columns, function ( column_type ) {
					var current_value_bg,
						current_value_pt,
						current_value_pr,
						current_value_pb,
						current_value_pl,
						current_value_padding_tablet,
						current_value_padding_phone,
						current_value_bg_img,
						current_value_parallax,
						current_value_parallax_method,
						has_tablet_padding,
						has_phone_padding;
					switch ( counter ) {
						%1$s
					}
			%%>',
			$this->generate_column_vars()
		);

		$output .= sprintf(
			'<div class="et-pb-option">
				<label for="et_pb_bg_img_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%2$s:
				</label>

				<div class=et-pb-option-container>
					<input id="et_pb_bg_img_<%%= counter %%>" type="text" class="regular-text et-pb-upload-field et-pb-main-setting" value="<%%= current_value_bg_img  %%>" />
					<input type="button" class="button button-upload et-pb-upload-button" value="%3$s" data-choose="%4$s" data-update="%5$s" data-type="image" />
					<span class="et-pb-reset-setting" style="display: none;"></span>
				</div>
			</div> <!-- .et-pb-option -->

			<div class="et-pb-option">
				<label for="et_pb_parallax_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%13$s:
				</label>

				<div class="et-pb-option-container">
					<div class="et_pb_yes_no_button_wrapper ">
						<div class="et_pb_yes_no_button et_pb_off_state">
							<span class="et_pb_value_text et_pb_on_value">%14$s</span>
							<span class="et_pb_button_slider"></span>
							<span class="et_pb_value_text et_pb_off_value">%15$s</span>
						</div>
						<select name="et_pb_parallax_<%%= counter %%>" id="et_pb_parallax_<%%= counter %%>" class="et-pb-main-setting regular-text et-pb-affects" data-affects="#et_pb_parallax_method_<%%= counter %%>">
							<option value="off">%15$s</option>
							<option value="on" <%%= current_value_parallax %%>>%14$s</option>
						</select>
					</div>
					<span class="et-pb-reset-setting" style="display: none;"></span>
				</div> <!-- .et-pb-option-container -->
			</div>

			<div class="et-pb-option et-pb-depends" data-depends_show_if="on">
				<label for="et_pb_parallax_method_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%16$s:
				</label>

				<div class="et-pb-option-container">
					<select name="et_pb_parallax_method_<%%= counter %%>" id="et_pb_parallax_method_<%%= counter %%>" class="et-pb-main-setting">
						<option value="off">%17$s</option>
						<option value="on" <%%= current_value_parallax_method %%>>%18$s</option>
					</select>
					<span class="et-pb-reset-setting" style="display: none;"></span>
				</div> <!-- .et-pb-option-container -->
			</div>

			<div class="et-pb-option">
				<label for="et_pb_background_color_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%6$s:
				</label>
				<div class="et-pb-option-container">
					<input id="et_pb_background_color_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha wp-color-picker et-pb-main-setting" type="text" data-alpha="true" placeholder="%7$s" value="<%%= current_value_bg %%>" />
					<span class="et-pb-reset-setting" style="display: none;"></span>
				</div> <!-- .et-pb-option-container -->
			</div> <!-- .et-pb-option -->

			<div class="et-pb-option">
				<label for="et_pb_padding_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%8$s:
				</label>
				<div class="et-pb-option-container">
				%19$s
					<div class="et_custom_margin_padding">
						<label>
							%9$s
							<input type="text" class="medium-text et_custom_margin et_custom_margin_top et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_top_<%%= counter %%>" name="et_pb_padding_top_<%%= counter %%>" value="<%%= current_value_pt %%>" data-device="desktop">
							<input type="text" class="medium-text et_custom_margin et_custom_margin_top et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text et_custom_margin et_custom_margin_top et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
						</label>
						<label>
							%10$s
							<input type="text" class="medium-text et_custom_margin et_custom_margin_right et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_right_<%%= counter %%>" name="et_pb_padding_right_<%%= counter %%>" value="<%%= current_value_pr %%>" data-device="desktop">
							<input type="text" class="medium-text et_custom_margin et_custom_margin_right et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text et_custom_margin et_custom_margin_right et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
						</label>
						<label>
							%11$s
							<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_bottom_<%%= counter %%>" name="et_pb_padding_bottom_<%%= counter %%>" value="<%%= current_value_pb %%>" data-device="desktop">
							<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
						</label>
						<label>
							%12$s
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

			<%% counter++;
			}); %%>',
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Background Image', 'et_builder' ),
			esc_html__( 'Upload an image', 'et_builder' ),
			esc_html__( 'Choose a Background Image', 'et_builder' ),
			esc_html__( 'Set As Background', 'et_builder' ), // #5
			esc_html__( 'Background Color', 'et_builder' ),
			esc_html__( 'Hex Value', 'et_builder' ),
			esc_html__( 'Padding', 'et_builder' ),
			esc_html__( 'Top', 'et_builder' ),
			esc_html__( 'Right', 'et_builder' ), // #10
			esc_html__( 'Bottom', 'et_builder' ),
			esc_html__( 'Left', 'et_builder' ),
			esc_html__( 'Parallax Effect', 'et_builder' ),
			esc_html__( 'Yes', 'et_builder' ),
			esc_html__( 'No', 'et_builder' ), // #15
			esc_html__( 'Parallax Method', 'et_builder' ),
			esc_html__( 'CSS', 'et_builder' ),
			esc_html__( 'True Parallax', 'et_builder' ),
			et_pb_generate_mobile_options_tabs() // #19
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

				<div class="et-pb-option">
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

				<div class="et-pb-option">
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

				<div class="et-pb-option">
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
