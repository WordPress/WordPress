<?php


class ET_Builder_Settings {

	/**
	 * @var array
	 */
	protected static $_BUILDER_SETTINGS_FIELDS;

	/**
	 * @var array
	 */
	protected static $_BUILDER_SETTINGS_VALUES;

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_FIELDS;

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_IS_DEFAULT;

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_VALUES;

	/**
	 * @var ET_Builder_Settings
	 */
	protected static $_instance;

	public function __construct() {
		if ( null !== self::$_instance ) {
			wp_die( get_class( $this ) . 'is a singleton class. You cannot create a another instance.' );
		}

		$this->_initialize();
		$this->_register_callbacks();
	}

	protected static function _get_ab_testing_fields() {
		return array(
			'et_pb_enable_ab_testing'         => array(
				'type'        => 'yes_no_button',
				'options'     => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'id'          => 'et_pb_enable_ab_testing',
				'label'       => esc_html__( 'Enable Split Testing', 'et_builder' ),
				'autoload'    => false,
				'class'       => 'et-pb-visible',
				'affects'     => array(
					'et_pb_ab_bounce_rate_limit',
					'et_pb_ab_refresh_interval',
					'et_pb_enable_shortcode_tracking',
				),
				'hide_on_fb'  => true,
				'tab_slug'    => 'content',
				'toggle_slug' => 'split_testing',
			),
			'et_pb_ab_bounce_rate_limit'      => array(
				'type'            => 'range',
				'id'              => 'et_pb_ab_bounce_rate_limit',
				'label'           => esc_html__( 'Bounce Rate Limit', 'et_builder' ),
				'default'         => 5,
				'range_settings'  => array(
					'step' => 1,
					'min'  => 3,
					'max'  => 60,
				),
				'depends_show_if' => 'on',
				'mobile_options'  => false,
				'unitless'        => true,
				'depends_to'      => array(
					'et_pb_enable_ab_testing',
				),
				'hide_on_fb'      => true,
				'tab_slug'        => 'content',
				'toggle_slug'     => 'split_testing',
			),
			'et_pb_ab_refresh_interval'       => array(
				'type'            => 'select',
				'id'              => 'et_pb_ab_refresh_interval',
				'label'           => esc_html__( 'Stats refresh interval', 'et_builder' ),
				'autoload'        => false,
				'depends_show_if' => 'on',
				'options'         => array(
					'hourly' => esc_html__( 'Hourly', 'et_builder' ),
					'daily'  => esc_html__( 'Daily', 'et_builder' ),
				),
				'depends_to'      => array(
					'et_pb_enable_ab_testing',
				),
				'hide_on_fb'      => true,
				'tab_slug'        => 'content',
				'toggle_slug'     => 'split_testing',
			),
			'et_pb_enable_shortcode_tracking' => array(
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'id'              => 'et_pb_enable_shortcode_tracking',
				'label'           => esc_html__( 'Shortcode Tracking', 'et_builder' ),
				'depends_show_if' => 'on',
				'affects'         => array(
					'et_pb_ab_current_shortcode',
				),
				'depends_to'      => array(
					'et_pb_enable_ab_testing',
				),
				'hide_on_fb'      => true,
				'tab_slug'        => 'content',
				'toggle_slug'     => 'split_testing',
			),
			'et_pb_ab_current_shortcode'      => array(
				'type'            => 'textarea',
				'id'              => 'et_pb_ab_current_shortcode',
				'label'           => esc_html__( 'Shortcode for Tracking:', 'et_builder' ),
				'autoload'        => false,
				'readonly'        => 'readonly',
				'depends_show_if' => 'on',
				'depends_to'      => array(
					'et_pb_enable_shortcode_tracking',
				),
				'hide_on_fb'      => true,
				'tab_slug'        => 'content',
				'toggle_slug'     => 'split_testing',
			),
			'et_pb_ab_subjects'               => array(
				'id'          => 'et_pb_ab_subjects',
				'type'        => 'hidden',
				'tab_slug'    => 'content',
				'toggle_slug' => 'split_testing',
				'autoload'    => false,
			),
		);
	}

	protected static function _get_builder_settings_fields() {
		return array(
			'et_pb_static_css_file' => self::_get_static_css_generation_field( 'builder' ),
			'et_pb_css_in_footer'   => array(
				'type'            => 'yes_no_button',
				'id'              => 'et_pb_css_in_footer',
				'index'           => -1,
				'label'           => esc_html__( 'Output Styles Inline', 'et_builder' ),
				'description'     => esc_html__( 'With previous versions of the builder, css styles for the modules\' design settings were output inline in the footer. Enable this option to restore that behavior.' ),
				'options'         => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'default'         => 'off',
				'validation_type' => 'simple_text',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'performance',
			),
		);
	}

	protected static function _get_builder_settings_in_epanel_format() {
		$tabs   = self::get_tabs( 'builder' );
		$fields = self::get_fields( 'builder' );
		$result = array();

		$result[]    = array( 'name' => 'wrap-builder', 'type' => 'contenttab-wrapstart' );
		$tab_content = array();
		$index       = 0;

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$index++;
			$tab_content_started = false;
			$tab_content         = array();

			$result[] = array( 'type' => 'subnavtab-start' );

			foreach ( $fields as $field_name => $field_info ) {
				if ( $field_info['tab_slug'] !== $tab_slug ) {
					continue;
				}

				if ( ! $tab_content_started ) {
					$result[]      = array( 'name' => "builder-{$index}", 'type' => 'subnav-tab', 'desc' => $tab_name );
					$tab_content[] = array( 'name' => "builder-{$index}", 'type' => 'subcontent-start' );

					$tab_content_started = true;
				}

				$field_type = $field_info['type'];

				if ( 'yes_no_button' === $field_type ) {
					$field_type = 'checkbox2';
				}

				$tab_content[] = array(
					'name'             => $field_info['label'],
					'id'               => $field_name,
					'type'             => $field_type,
					'std'              => $field_info['default'],
					'desc'             => $field_info['description'],
					'is_builder_field' => true,
				);
			}

			if ( $tab_content_started ) {
				$tab_content[] = array( 'name' => "builder-{$index}", 'type' => 'subcontent-end' );
			}
		}

		$result[] = array( 'type' => 'subnavtab-end' );
		$result   = array_merge( $result, $tab_content );
		$result[] = array( 'name' => 'wrap-builder', 'type' => 'contenttab-wrapend' );

		return $result;
	}

	protected static function _get_builder_settings_values() {
		return array(
			'et_pb_static_css_file' => et_get_option( 'et_pb_static_css_file', 'on' ),
			'et_pb_css_in_footer'   => et_get_option( 'et_pb_css_in_footer', 'off' ),
		);
	}

	protected static function _get_page_settings_fields() {
		$fields = array();

		if ( et_pb_is_allowed( 'ab_testing' ) ) {
			$fields = self::_get_ab_testing_fields();
		}

		$fields = array_merge( $fields, array(
			'et_pb_custom_css'                    => array(
				'type'        => 'textarea',
				'id'          => 'et_pb_custom_css',
				'label'       => esc_html__( 'Custom CSS', 'et_builder' ),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'custom_css',
			),
			'et_pb_color_palette'                 => array(
				'type'        => 'colorpalette',
				'id'          => 'et_pb_color_palette',
				'label'       => esc_html__( 'Color Picker Color Pallete', 'et_builder' ),
				'default'     => implode( '|', et_pb_get_default_color_palette() ),
				'tab_slug'    => 'design',
				'toggle_slug' => 'color_palette',
			),
			'et_pb_page_gutter_width'             => array(
				'type'           => 'range',
				'id'             => 'et_pb_page_gutter_width',
				'meta_key'       => '_et_pb_gutter_width',
				'label'          => esc_html__( 'Gutter Width', 'et_builder' ),
				'range_settings' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 4,
				),
				'default'        => et_get_option( 'gutter_width', 3 ),
				'mobile_options' => false,
				'validate_unit'  => false,
				'tab_slug'       => 'design',
				'toggle_slug'    => 'spacing',
			),
			'et_pb_light_text_color'              => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_light_text_color',
				'label'       => esc_html__( 'Light Text Color', 'et_builder' ),
				'default'     => '#ffffff',
				'tab_slug'    => 'design',
				'toggle_slug' => 'text',
			),
			'et_pb_dark_text_color'               => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_dark_text_color',
				'label'       => esc_html__( 'Dark Text Color', 'et_builder' ),
				'default'     => '#666666',
				'tab_slug'    => 'design',
				'toggle_slug' => 'text',
			),
			'et_pb_content_area_background_color' => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_content_area_background_color',
				'label'       => esc_html__( 'Content Area Background Color', 'et_builder' ),
				'default'     => 'rgba(255,255,255,0)',
				'tab_slug'    => 'content',
				'toggle_slug' => 'background',
			),
			'et_pb_section_background_color'      => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_section_background_color',
				'label'       => esc_html__( 'Section Background Color', 'et_builder' ),
				'default'     => '#ffffff',
				'tab_slug'    => 'content',
				'toggle_slug' => 'background',
			),
			'et_pb_static_css_file'               => self::_get_static_css_generation_field( 'page' ),
		) );

		return $fields;
	}

	protected static function _get_page_settings_values( $post_id ) {
		$post_id = $post_id ? $post_id : get_the_ID();

		if ( ! empty( self::$_PAGE_SETTINGS_VALUES[ $post_id ] ) ) {
			return self::$_PAGE_SETTINGS_VALUES[ $post_id ];
		}

		$is_default = array();

		// Page settings fields
		$fields = self::$_PAGE_SETTINGS_FIELDS;

		// Defaults
		$default_bounce_rate_limit = 5;

		// Get values
		$ab_bounce_rate_limit       = get_post_meta( $post_id, '_et_pb_ab_bounce_rate_limit', true );
		$et_pb_ab_bounce_rate_limit = '' !== $ab_bounce_rate_limit ? $ab_bounce_rate_limit : $default_bounce_rate_limit;
		$is_default[]               = $et_pb_ab_bounce_rate_limit === $default_bounce_rate_limit ? 'et_pb_ab_bounce_rate_limit' : '';

		$color_palette              = get_post_meta( $post_id, '_et_pb_color_palette', true );
		$default                    = $fields['et_pb_color_palette']['default'];
		$et_pb_color_palette        = '' !== $color_palette ? $color_palette : $default;
		$is_default[]               = $et_pb_color_palette === $default ? 'et_pb_color_palette' : '';

		$gutter_width               = get_post_meta( $post_id, '_et_pb_gutter_width', true );
		$default                    = $fields['et_pb_page_gutter_width']['default'];
		$et_pb_page_gutter_width    = '' !== $gutter_width ? $gutter_width : $default;
		$is_default[]               = $et_pb_page_gutter_width === $default ? 'et_pb_page_gutter_width' : '';

		$light_text_color           = get_post_meta( $post_id, '_et_pb_light_text_color', true );
		$default                    = $fields['et_pb_light_text_color']['default'];
		$et_pb_light_text_color     = '' !== $light_text_color ? $light_text_color : $default;
		$is_default[]               = strtolower( $et_pb_light_text_color ) === $default ? 'et_pb_light_text_color' : '';

		$dark_text_color            = get_post_meta( $post_id, '_et_pb_dark_text_color', true );
		$default                    = $fields['et_pb_dark_text_color']['default'];
		$et_pb_dark_text_color      = '' !== $dark_text_color ? $dark_text_color : $default;
		$is_default[]               = strtolower( $et_pb_dark_text_color ) === $default ? 'et_pb_dark_text_color' : '';

		$content_area_background_color       = get_post_meta( $post_id, '_et_pb_content_area_background_color', true );
		$default                             = $fields['et_pb_content_area_background_color']['default'];
		$et_pb_content_area_background_color = '' !== $content_area_background_color ? $content_area_background_color : $default;
		$is_default[]                        = strtolower( $et_pb_content_area_background_color ) === $default ? 'et_pb_content_area_background_color' : '';

		$section_background_color            = get_post_meta( $post_id, '_et_pb_section_background_color', true );
		$default                             = $fields['et_pb_section_background_color']['default'];
		$et_pb_section_background_color      = '' !== $section_background_color ? $section_background_color : $default;
		$is_default[]                        = strtolower( $et_pb_section_background_color ) === $default ? 'et_pb_section_background_color' : '';

		$static_css_file       = get_post_meta( $post_id, '_et_pb_static_css_file', true );
		$default               = $fields['et_pb_static_css_file']['default'];
		$et_pb_static_css_file = '' !== $static_css_file ? $static_css_file : $default;
		$is_default[]          = $et_pb_static_css_file === $default ? 'et_pb_static_css_file' : '';

		self::$_PAGE_SETTINGS_IS_DEFAULT = $is_default;

		$values = array(
			'et_pb_enable_ab_testing'             => et_is_ab_testing_active() ? 'on' : 'off',
			'et_pb_ab_bounce_rate_limit'          => $et_pb_ab_bounce_rate_limit,
			'et_pb_ab_refresh_interval'           => et_pb_ab_get_refresh_interval( $post_id ),
			'et_pb_ab_subjects'                   => et_pb_ab_get_subjects( $post_id ),
			'et_pb_enable_shortcode_tracking'     => get_post_meta( $post_id, '_et_pb_enable_shortcode_tracking', true ),
			'et_pb_ab_current_shortcode'          => '[et_pb_split_track id="' . $post_id . '" /]',
			'et_pb_custom_css'                    => get_post_meta( $post_id, '_et_pb_custom_css', true ),
			'et_pb_color_palette'                 => $et_pb_color_palette,
			'et_pb_page_gutter_width'             => $et_pb_page_gutter_width,
			'et_pb_light_text_color'              => strtolower( $et_pb_light_text_color ),
			'et_pb_dark_text_color'               => strtolower( $et_pb_dark_text_color ),
			'et_pb_content_area_background_color' => strtolower( $et_pb_content_area_background_color ),
			'et_pb_section_background_color'      => strtolower( $et_pb_section_background_color ),
			'et_pb_static_css_file'               => $et_pb_static_css_file,
		);

		/**
		 * Filters Divi Builder page settings values.
		 *
		 * @since 3.0.45
		 *
		 * @param mixed[] $builder_settings {
		 *     Builder Settings Values
		 *
		 *     @type string $setting_name Setting value.
		 *     ...
		 * }
		 * @param string|int $post_id
		 */
		$values = self::$_PAGE_SETTINGS_VALUES[ $post_id ] = apply_filters( 'et_builder_page_settings_values', $values, $post_id );

		/**
		 * Filters the Divi Builder's page settings values.
		 *
		 * @deprecated {@see 'et_builder_page_settings_values'}
		 *
		 * @since      2.7.0
		 * @since      3.0.45 Deprecation.
		 */
		return apply_filters( 'et_pb_get_builder_settings_values', $values, $post_id );
	}

	protected static function _get_static_css_generation_field( $scope ) {
		$description = array(
			'page'    => esc_html__( "When this option is enabled, the builder's inline CSS styles for this page will be cached and served as a static file. Enabling this option can help improve performance.", 'et_builder' ),
			'builder' => esc_html__( "When this option is enabled, the builder's inline CSS styles for all pages will be cached and served as static files. Enabling this option can help improve performance.", 'et_builder' ),
		);

		return array(
			'type'            => 'yes_no_button',
			'id'              => 'et_pb_static_css_file',
			'index'           => -1,
			'label'           => esc_html__( 'Static CSS File Generation', 'et_builder' ),
			'description'     => $description[ $scope ],
			'options'         => array(
				'on'  => __( 'On', 'et_builder' ),
				'off' => __( 'Off', 'et_builder' ),
			),
			'default'         => 'on',
			'validation_type' => 'simple_text',
			'after'           => array(
				'type'             => 'button',
				'link'             => '#',
				'class'            => 'et_builder_clear_static_css',
				'title'            => esc_html_x( 'Clear', 'clear static css files', 'et_builder' ),
				'authorize'        => false,
				'is_after_element' => true,
			),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'performance',
		);
	}

	protected function _initialize() {
		/**
		 * Filters Divi Builder settings field definitions.
		 *
		 * @since 3.0.45
		 */
		self::$_BUILDER_SETTINGS_FIELDS = apply_filters( 'et_builder_settings_definitions', self::_get_builder_settings_fields() );

		/**
		 * Filters Divi Builder settings values.
		 *
		 * @since 3.0.45
		 *
		 * @param mixed[] $builder_settings {
		 *     Builder Settings Values
		 *
		 *     @type string $setting_name Setting value.
		 *     ...
		 * }
		 */
		self::$_BUILDER_SETTINGS_VALUES = apply_filters( 'et_builder_settings_values', self::_get_builder_settings_values() );

		/**
		 * Filters Divi Builder page settings field definitions.
		 *
		 * @since 3.0.45
		 */
		self::$_PAGE_SETTINGS_FIELDS = apply_filters( 'et_builder_page_settings_definitions', self::_get_page_settings_fields() );

		/**
		 * Filters Divi Builder page settings field definitions.
		 *
		 * @deprecated {@see 'et_builder_page_settings_definitions'}
		 *
		 * @since      2.7.0
		 * @since      3.0.45 Deprecation.
		 */
		self::$_PAGE_SETTINGS_FIELDS = apply_filters( 'et_pb_get_builder_settings_configurations', self::$_PAGE_SETTINGS_FIELDS );

		self::$_PAGE_SETTINGS_VALUES = array();
	}

	protected static function _maybe_clear_cached_static_css_files( $setting, $setting_value ) {
		if ( in_array( $setting, array( 'et_pb_css_in_footer', 'et_pb_static_css_file' ) ) ) {
			 ET_Core_PageResource::remove_static_resources( 'all', 'all' );
		}
	}

	protected function _register_callbacks() {
		$class = get_class( $this );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'et_builder_settings_update_option', array( $class, 'update_option_cb'), 10, 3 );

		if ( et_is_builder_plugin_active() ) {
			add_filter( 'et_builder_plugin_dashboard_sections', array( $class, 'add_plugin_dashboard_sections' ) );
			add_filter( 'et_builder_plugin_dashboard_fields_data', array( $class, 'add_plugin_dashboard_fields_data' ) );
			add_action( 'et_pb_builder_after_save_options', array( $class, 'plugin_dashboard_option_saved_cb' ), 10, 4 );
			add_action( 'et_pb_builder_option_value', array( $class, 'plugin_dashboard_option_value_cb' ), 10, 2 );
		} else {
			add_filter( 'et_epanel_tab_names', array( $class, 'add_epanel_tab' ) );
			add_filter( 'et_epanel_layout_data', array( $class, 'add_epanel_tab_content' ) );
			add_action( 'et_epanel_update_option', array( $class, 'update_option_cb' ), 10, 3 );
		}
	}

	/**
	 * Adds a tab for the builder to ePanel's tabs array.
	 * {@see 'et_epanel_tab_names'}
	 *
	 * @param string[] $tabs
	 *
	 * @return string[] $tabs
	 */
	public static function add_epanel_tab( $tabs ) {
		$builder_tab = esc_html_x( 'Builder', 'Divi Builder', 'et_builder' );
		$keys        = array_keys( $tabs );
		$values      = array_values( $tabs );

		array_splice( $keys, 2, 0, 'builder' );
		array_splice( $values, 2, 0, $builder_tab );

		return array_combine( $keys, $values );
	}

	/**
	 * Adds builder settings fields data to the builder plugin's options dashboard.
	 * {@see 'et_builder_plugin_dashboard_fields_data'}
	 *
	 * @param array[] $dashboard_data
	 *
	 * @return array[] $dashboard_data
	 */
	public static function add_plugin_dashboard_fields_data( $dashboard_data ) {
		$tabs    = self::get_tabs( 'builder' );
		$fields  = self::get_fields( 'builder' );
		$toggles = self::get_toggles();

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$section                    = $tab_slug . '_main_options';
			$dashboard_data[ $section ] = array();

			$dashboard_data[ $section ][] = array( 'type' => 'main_title', 'title' => '' );

			foreach ( $toggles as $toggle_slug => $toggle ) {
				$section_started = false;

				foreach ( $fields as $field_slug => $field_info ) {
					if ( $tab_slug !== $field_info['tab_slug'] || $toggle_slug !== $field_info['toggle_slug'] ) {
						continue;
					}

					if ( 'et_pb_css_in_footer' === $field_info['id'] ) {
						continue;
					}

					if ( ! $section_started ) {
						$dashboard_data[ $section ][] = array( 'type'  => 'section_start', 'title' => $toggles[ $toggle_slug ] );
						$section_started              = true;
					}

					$field_info['hint_text'] = $field_info['description'];
					$field_info['name']      = $field_info['id'];
					$field_info['title']     = $field_info['label'];

					$dashboard_data[ $section ][] = $field_info;

					if ( isset( $field_info['after'] ) ) {
						$dashboard_data[ $section ][] = $field_info['after'];
					}
				}

				if ( $section_started ) {
					$dashboard_data[ $section ][] = array( 'type' => 'section_end' );
				}
			}
		}

		return $dashboard_data;
	}

	/**
	 * Adds tabs for builder settings to the builder plugin's options dashboard.
	 * {@see 'et_builder_plugin_dashboard_sections'}
	 *
	 * @param array[] $sections
	 *
	 * @return array[] $sections
	 */
	public static function add_plugin_dashboard_sections( $sections ) {
		$tabs = self::get_tabs( 'builder' );

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$sections[ $tab_slug ] = array(
				'title'    => et_esc_previously( $tab_name ),
				'contents' => array(
					'main' => esc_html__( 'Main', 'et_builder' ),
				),
			);
		}

		return $sections;
	}

	/**
	 * Adds builder settings to ePanel. {@see 'et_epanel_layout_data'}
	 *
	 * @param array $layout_data
	 *
	 * @return array $data
	 */
	public static function add_epanel_tab_content( $layout_data ) {
		$result = array();
		$done   = false;

		foreach ( $layout_data as $data ) {
			$result[] = $data;

			if ( $done || ! isset( $data['name'], $data['type'] ) ) {
				continue;
			}

			if ( 'wrap-navigation' === $data['name'] && 'contenttab-wrapend' === $data['type'] ) {
				$builder_options = self::_get_builder_settings_in_epanel_format();
				$result          = array_merge( $result, $builder_options );
				$done            = true;
			}
		}

		return $result;
	}

	public static function update_option_cb( $setting, $setting_value, $post_id = 'global' ) {
		self::_maybe_clear_cached_static_css_files( $setting, $setting_value );
	}

	/**
	 * Returns builder settings fields data for the provided settings scope.
	 *
	 * @param string $scope Get settings fields for scope (page|builder|all). Default 'page'.
	 *
	 * @return array[] See {@link ET_Builder_Element::get_fields()} for structure.
	 */
	public static function get_fields( $scope = 'page' ) {
		$fields = array();

		if ( 'builder' === $scope ) {
			$fields = self::$_BUILDER_SETTINGS_FIELDS;
		} else if ( 'page' === $scope ) {
			$fields = self::$_PAGE_SETTINGS_FIELDS;
		}

		return $fields;
	}

	/**
	 * @return ET_Builder_Settings
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new ET_Builder_Settings();
		}

		return self::$_instance;
	}

	/**
	 * Returns the localized tab names for the builder settings.
	 *
	 * @param string $scope
	 *
	 * @return string[] {
	 *     Localized Tab Names.
	 *
	 *     @type string $tab_slug Tab name
	 *     ...
	 * }
	 */
	public static function get_tabs( $scope = 'page' ) {
		$result   = array();
		$advanced = esc_html_x( 'Advanced', 'Design Settings', 'et_builder' );

		if ( 'page' === $scope ) {
			$result = array(
				'content'  => esc_html_x( 'Content', 'Content Settings', 'et_builder' ),
				'design'   => esc_html_x( 'Design', 'Design Settings', 'et_builder' ),
				'advanced' => $advanced,
			);
		} else if ( 'builder' === $scope ) {
			$result = array(
				'advanced' => $advanced,
			);
		}

		/**
		 * Filters the builder's settings tabs.
		 *
		 * @since 3.0.45
		 *
		 * @param string[] $tabs {
		 *     Localized Tab Names.
		 *
		 *     @type string $tab_slug Tab name
		 *     ...
		 * }
		 * @param string   $scope Accepts 'page', 'builder'.
		 */
		return apply_filters( 'et_builder_settings_tabs', $result, $scope );
	}

	/**
	 * Returns the localized title of the builder page settings modal.
	 *
	 * @return string
	 */
	public static function get_title() {
		global $post;

		$post_type     = isset( $post->post_type ) ? $post->post_type : 'page';
		$post_type_obj = get_post_type_object( $post_type );
		$settings      = esc_html_x( '%s Settings', 'Page, Post, Product, etc.', 'et_builder' );

		/**
		 * Filters the title of the builder's page settings modal.
		 *
		 * @since 3.0.45
		 *
		 * @param string $title
		 */
		return apply_filters( 'et_builder_page_settings_modal_title', sprintf( $settings, $post_type_obj->labels->singular_name ) );
	}

	/**
	 * Returns the localized toggle/group names for the builder page settings modal.
	 *
	 * @return string[] {
	 *     Localized Toggle Names
	 *
	 *     @type string $toggle_slug Toggle name
	 * }
	 */
	public static function get_toggles() {
		$toggles = array(
			'background'    => esc_html__( 'Background', 'et_builder' ),
			'color_palette' => esc_html__( 'Color Palette', 'et_builder' ),
			'custom_css'    => esc_html__( 'Custom CSS', 'et_builder' ),
			'performance'   => esc_html__( 'Performance', 'et_builder' ),
			'spacing'       => esc_html__( 'Spacing', 'et_builder' ),
			'split_testing' => esc_html__( 'Split Testing', 'et_builder' ),
			'text'          => esc_html__( 'Text', 'et_builder' ),
		);

		/**
		 * Filters the builder page settings modal's option group toggles.
		 *
		 * @since 3.0.45
		 *
		 * @param string[] $toggles {
		 *     Localized Toggle Names
		 *
		 *     @type string $toggle_slug Toggle name
		 *     ...
		 * }
		 */
		return apply_filters( 'et_builder_page_settings_modal_toggles', $toggles );
	}

	/**
	 * Returns the values of builder settings for the provided settings scope.
	 *
	 * @param string     $scope   Get values for scope (page|builder|all). Default 'page'.
	 * @param string|int $post_id Optional. If not provided, {@link get_the_ID()} will be used.
	 *
	 * @return mixed[] {
	 *     Settings Values
	 *
	 *     @type mixed $setting_key The value for the setting.
	 *     ...
	 * }
	 */
	public static function get_values( $scope = 'page', $post_id = null, $exclude_defaults = false ) {
		$result = array();

		if ( 'builder' === $scope ) {
			$result = self::$_BUILDER_SETTINGS_VALUES;
		} else if ( 'page' === $scope ) {
			$result = self::_get_page_settings_values( $post_id );
		} else if ( 'all' === $scope ) {
			$result = array (
				'page'    => self::_get_page_settings_values( $post_id ),
				'builder' => self::$_BUILDER_SETTINGS_VALUES,
			);
		}

		if ( $exclude_defaults ) {
			'all' === $scope || $result = array( $result );

			foreach ( $result as $key => $settings ) {
				$result[ $key ] = array_diff_key( $result[ $key ], array_flip( self::$_PAGE_SETTINGS_IS_DEFAULT ) );
			}

			'all' === $scope || $result = $result[0];
		}

		return $result;
	}

	public static function plugin_dashboard_option_saved_cb( $processed_options, $option_name, $field_info, $output ) {
		if ( ! isset( $field_info['id'] ) ) {
			return;
		}

		$setting       = $field_info['id'];
		$setting_value = $processed_options[ $option_name ];

		if ( ! isset( self::$_BUILDER_SETTINGS_FIELDS[ $setting ] ) ) {
			return;
		}

		et_update_option( $setting, $setting_value );

		self::_maybe_clear_cached_static_css_files( $setting, $setting_value );
	}

	public static function plugin_dashboard_option_value_cb( $option_value, $option ) {
		if ( ! isset( $option['id'] ) ) {
			return $option_value;
		}

		$setting = $option['id'];

		if ( ! isset( self::$_BUILDER_SETTINGS_VALUES[ $setting ] ) ) {
			return $option_value;
		}

		return self::$_BUILDER_SETTINGS_VALUES[ $setting ];
	}
}


if ( ! function_exists( 'et_builder_settings_init' ) ):
/**
 * Initializes the builder settings class if needed.
 * {@see 'current_screen'}
 *
 * @param WP_Screen $screen Optional. Default `null`.
 */
function et_builder_settings_init( $screen = null ) {
	$init_settings = et_builder_should_load_framework() || wp_doing_ajax();

	if ( ! $init_settings && is_a( $screen, 'WP_Screen' ) ) {
		$init_settings = 1 === preg_match( '/et_\w+_options/', $screen->base );
	}

	if ( $init_settings ) {
		ET_Builder_Settings::get_instance();
	}
}
add_action( 'current_screen', 'et_builder_settings_init' );
endif;


if ( ! function_exists( 'et_builder_settings_get' ) ):
/**
 * Get a builder setting value. Default and global setting values are considered when applicable.
 *
 * @param string     $setting Page setting name.
 * @param string|int $post_id Optional. The post id.
 *
 * @return mixed
 */
function et_builder_settings_get( $setting, $post_id = '' ) {
	$builder_fields = ET_Builder_Settings::get_fields( 'builder' );
	$builder_values = ET_Builder_Settings::get_values( 'builder' );

	$page_fields = ET_Builder_Settings::get_fields();
	$page_values = ET_Builder_Settings::get_values( 'page', $post_id );

	$has_page   = isset( $page_fields[ $setting ] );
	$has_global = isset( $builder_fields[ $setting ] );

	$value = '';

	if ( ! $has_page && ! $has_global ) {
		return $value;
	}

	if ( $has_global ) {
		$global_value       = $builder_values[ $setting ];
		$global_has_default = isset( $builder_fields[ $setting ]['default'] );
		$global_is_default  = $global_has_default && $global_value === $builder_fields[ $setting ]['default'];
		$value              = $global_value;
	}

	if ( $has_page ) {
		$page_value       = $page_values[ $setting ];
		$page_has_default = isset( $page_fields[ $setting ]['default'] );
		$page_is_default  = $page_has_default && $page_value === $page_fields[ $setting ]['default'];
		$value            = $page_value;
	}

	if ( ! $has_page || ( $page_is_default && ! $global_is_default ) ) {
		$value = $global_value;
	} else if ( ! $has_global || ! $page_is_default ) {
		$value = $page_value;
	}

	return $value;
}
endif;


if ( ! function_exists( 'et_builder_setting_is_off' ) ):
/**
 * Whether or not a builder setting is off. Default and global setting values are
 * considered when applicable.
 *
 * @param string     $setting Page setting name.
 * @param string|int $post_id Optional. The post id.
 *
 * @return bool
 */
function et_builder_setting_is_off( $setting, $post_id = '' ) {
	return 'off' === et_builder_settings_get( $setting, $post_id );
}
endif;


if ( ! function_exists( 'et_builder_setting_is_on' ) ):
/**
 * Whether or not a builder setting is on. Default and global setting values are
 * considered when applicable.
 *
 * @param string     $setting Page setting name.
 * @param string|int $post_id Optional. The post id.
 *
 * @return bool
 */
function et_builder_setting_is_on( $setting, $post_id = '' ) {
	return 'on' === et_builder_settings_get( $setting, $post_id );
}
endif;
