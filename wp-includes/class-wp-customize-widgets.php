<?php
/**
 * Widget customizer manager class.
 */
class WP_Customize_Widgets {
	const UPDATE_WIDGET_AJAX_ACTION    = 'update-widget';
	const UPDATE_WIDGET_NONCE_POST_KEY = 'update-sidebar-widgets-nonce';

	/**
	 * All id_bases for widgets defined in core
	 *
	 * @var array
	 */
	protected static $core_widget_id_bases = array(
		'archives',
		'calendar',
		'categories',
		'links',
		'meta',
		'nav_menu',
		'pages',
		'recent-comments',
		'recent-posts',
		'rss',
		'search',
		'tag_cloud',
		'text',
	);

	/**
	 * Initial loader.
	 */
	static function setup() {
		add_action( 'after_setup_theme', array( __CLASS__, 'setup_widget_addition_previews' ) );
		add_action( 'customize_controls_init', array( __CLASS__, 'customize_controls_init' ) );
		add_action( 'customize_register', array( __CLASS__, 'schedule_customize_register' ), 1 );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'customize_controls_enqueue_deps' ) );
		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'output_widget_control_templates' ) );
		add_action( 'customize_preview_init', array( __CLASS__, 'customize_preview_init' ) );

		add_action( 'dynamic_sidebar', array( __CLASS__, 'tally_rendered_widgets' ) );
		add_action( 'dynamic_sidebar', array( __CLASS__, 'tally_sidebars_via_dynamic_sidebar_actions' ) );
		add_filter( 'temp_is_active_sidebar', array( __CLASS__, 'tally_sidebars_via_is_active_sidebar_calls' ), 10, 2 );
		add_filter( 'temp_dynamic_sidebar_has_widgets', array( __CLASS__, 'tally_sidebars_via_dynamic_sidebar_calls' ), 10, 2 );

		/**
		 * Special filter for Settings Revisions plugin until it can handle
		 * dynamically creating settings. Normally this should be handled by
		 * a setting's sanitize_js_callback, but when restoring an old revision
		 * it may include settings which do not currently exist, and so they
		 * do not have the opportunity to be sanitized as needed. Furthermore,
		 * we have to add this filter here because the customizer is not
		 * initialized in WP Ajax, which is where Settings Revisions currently
		 * needs to apply this filter at times.
		 */
		add_filter( 'temp_customize_sanitize_js', array( __CLASS__, 'temp_customize_sanitize_js' ), 10, 2 );
	}

	/**
	 * Get an unslashed post value, or return a default
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	static function get_post_value( $name, $default = null ) {
		if ( ! isset( $_POST[$name] ) ) {
			return $default;
		}
		return wp_unslash( $_POST[$name] );
	}

	protected static $_customized;
	protected static $_prepreview_added_filters = array();

	/**
	 * Since the widgets get registered (widgets_init) before the customizer settings are set up (customize_register),
	 * we have to filter the options similarly to how the setting previewer will filter the options later.
	 *
	 * @action after_setup_theme
	 */
	static function setup_widget_addition_previews() {
		global $wp_customize;
		$is_customize_preview = (
			( ! empty( $wp_customize ) )
			&&
			( ! is_admin() )
			&&
			( 'on' === self::get_post_value( 'wp_customize' ) )
			&&
			check_ajax_referer( 'preview-customize_' . $wp_customize->get_stylesheet(), 'nonce', false )
		);

		$is_ajax_widget_update = (
			( defined( 'DOING_AJAX' ) && DOING_AJAX )
			&&
			self::get_post_value( 'action' ) === self::UPDATE_WIDGET_AJAX_ACTION
			&&
			check_ajax_referer( self::UPDATE_WIDGET_AJAX_ACTION, self::UPDATE_WIDGET_NONCE_POST_KEY, false )
		);

		$is_ajax_customize_save = (
			( defined( 'DOING_AJAX' ) && DOING_AJAX )
			&&
			self::get_post_value( 'action' ) === 'customize_save'
			&&
			check_ajax_referer( 'save-customize_' . $wp_customize->get_stylesheet(), 'nonce' )
		);

		$is_valid_request = ( $is_ajax_widget_update || $is_customize_preview || $is_ajax_customize_save );
		if ( ! $is_valid_request ) {
			return;
		}

		// Input from customizer preview
		if ( isset( $_POST['customized'] ) ) {
			$customized = json_decode( self::get_post_value( 'customized' ), true );
		}
		// Input from ajax widget update request
		else {
			$customized    = array();
			$id_base       = self::get_post_value( 'id_base' );
			$widget_number = (int) self::get_post_value( 'widget_number' );
			$option_name   = 'widget_' . $id_base;
			$customized[$option_name] = array();
			if ( false !== $widget_number ) {
				$option_name .= '[' . $widget_number . ']';
				$customized[$option_name][$widget_number] = array();
			}
		}

		$function = array( __CLASS__, 'prepreview_added_sidebars_widgets' );

		$hook = 'option_sidebars_widgets';
		add_filter( $hook, $function );
		self::$_prepreview_added_filters[] = compact( 'hook', 'function' );

		$hook = 'default_option_sidebars_widgets';
		add_filter( $hook, $function );
		self::$_prepreview_added_filters[] = compact( 'hook', 'function' );

		foreach ( $customized as $setting_id => $value ) {
			if ( preg_match( '/^(widget_.+?)(\[(\d+)\])?$/', $setting_id, $matches ) ) {
				$body     = sprintf( 'return %s::prepreview_added_widget_instance( $value, %s );', __CLASS__, var_export( $setting_id, true ) );
				$function = create_function( '$value', $body );
				$option   = $matches[1];

				$hook = sprintf( 'option_%s', $option );
				add_filter( $hook, $function );
				self::$_prepreview_added_filters[] = compact( 'hook', 'function' );

				$hook = sprintf( 'default_option_%s', $option );
				add_filter( $hook, $function );
				self::$_prepreview_added_filters[] = compact( 'hook', 'function' );

				/**
				 * Make sure the option is registered so that the update_option won't fail due to
				 * the filters providing a default value, which causes the update_option() to get confused.
				 */
				add_option( $option, array() );
			}
		}

		self::$_customized = $customized;
	}

	/**
	 * Ensure that newly-added widgets will appear in the widgets_sidebars.
	 * This is necessary because the customizer's setting preview filters are added after the widgets_init action,
	 * which is too late for the widgets to be set up properly.
	 *
	 * @param array $sidebars_widgets
	 * @return array
	 */
	static function prepreview_added_sidebars_widgets( $sidebars_widgets ) {
		foreach ( self::$_customized as $setting_id => $value ) {
			if ( preg_match( '/^sidebars_widgets\[(.+?)\]$/', $setting_id, $matches ) ) {
				$sidebar_id = $matches[1];
				$sidebars_widgets[$sidebar_id] = $value;
			}
		}
		return $sidebars_widgets;
	}

	/**
	 * Ensure that newly-added widgets will have empty instances so that they will be recognized.
	 * This is necessary because the customizer's setting preview filters are added after the widgets_init action,
	 * which is too late for the widgets to be set up properly.
	 *
	 * @param array $instance
	 * @param string $setting_id
	 * @return array
	 */
	static function prepreview_added_widget_instance( $instance, $setting_id ) {
		if ( isset( self::$_customized[$setting_id] ) ) {
			$parsed_setting_id = self::parse_widget_setting_id( $setting_id );
			$widget_number     = $parsed_setting_id['number'];

			// Single widget
			if ( is_null( $widget_number ) ) {
				if ( false === $instance && empty( $value ) ) {
					$instance = array();
				}
			}
			// Multi widget
			else if ( false === $instance || ! isset( $instance[$widget_number] ) ) {
				if ( empty( $instance ) ) {
					$instance = array( '_multiwidget' => 1 );
				}
				if ( ! isset( $instance[$widget_number] ) ) {
					$instance[$widget_number] = array();
				}
			}
		}
		return $instance;
	}

	/**
	 * Remove filters added in setup_widget_addition_previews() which ensure that
	 * widgets are populating the options during widgets_init
	 *
	 * @action wp_loaded
	 */
	static function remove_prepreview_filters() {
		foreach ( self::$_prepreview_added_filters as $prepreview_added_filter ) {
			remove_filter( $prepreview_added_filter['hook'], $prepreview_added_filter['function'] );
		}
		self::$_prepreview_added_filters = array();
	}

	/**
	 * Make sure that all widgets get loaded into customizer; these actions are also done in the wp_ajax_save_widget()
	 *
	 * @see wp_ajax_save_widget()
	 * @action customize_controls_init
	 */
	static function customize_controls_init() {
		do_action( 'load-widgets.php' );
		do_action( 'widgets.php' );
		do_action( 'sidebar_admin_setup' );
	}

	/**
	 * When in preview, invoke customize_register for settings after WordPress is
	 * loaded so that all filters have been initialized (e.g. Widget Visibility)
	 */
	static function schedule_customize_register( $wp_customize ) {
		if ( is_admin() ) { // @todo for some reason, $wp_customize->is_preview() is true here?
			self::customize_register( $wp_customize );
		} else {
			add_action( 'wp', array( __CLASS__, 'customize_register' ) );
		}
	}

	/**
	 * Register customizer settings and controls for all sidebars and widgets
	 *
	 * @action customize_register
	 */
	static function customize_register( $wp_customize = null ) {
		global $wp_registered_widgets, $wp_registered_widget_controls;
		if ( ! ( $wp_customize instanceof WP_Customize_Manager ) ) {
			$wp_customize = $GLOBALS['wp_customize'];
		}

		$sidebars_widgets = array_merge(
			array( 'wp_inactive_widgets' => array() ),
			array_fill_keys( array_keys( $GLOBALS['wp_registered_sidebars'] ), array() ),
			wp_get_sidebars_widgets()
		);

		$new_setting_ids = array();

		/**
		 * Register a setting for all widgets, including those which are active, inactive, and orphaned
		 * since a widget may get suppressed from a sidebar via a plugin (like Widget Visibility).
		 */
		foreach ( array_keys( $wp_registered_widgets ) as $widget_id ) {
			$setting_id   = self::get_setting_id( $widget_id );
			$setting_args = self::get_setting_args( $setting_id );
			$setting_args['sanitize_callback']    = array( __CLASS__, 'sanitize_widget_instance' );
			$setting_args['sanitize_js_callback'] = array( __CLASS__, 'sanitize_widget_js_instance' );
			$wp_customize->add_setting( $setting_id, $setting_args );
			$new_setting_ids[] = $setting_id;
		}

		foreach ( $sidebars_widgets as $sidebar_id => $sidebar_widget_ids ) {
			if ( empty( $sidebar_widget_ids ) ) {
				$sidebar_widget_ids = array();
			}
			$is_registered_sidebar = isset( $GLOBALS['wp_registered_sidebars'][$sidebar_id] );
			$is_inactive_widgets   = ( 'wp_inactive_widgets' === $sidebar_id );
			$is_active_sidebar     = ( $is_registered_sidebar && ! $is_inactive_widgets );

			/**
			 * Add setting for managing the sidebar's widgets
			 */
			if ( $is_registered_sidebar || $is_inactive_widgets ) {
				$setting_id   = sprintf( 'sidebars_widgets[%s]', $sidebar_id );
				$setting_args = self::get_setting_args( $setting_id );
				$setting_args['sanitize_callback']    = array( __CLASS__, 'sanitize_sidebar_widgets' );
				$setting_args['sanitize_js_callback'] = array( __CLASS__, 'sanitize_sidebar_widgets_js_instance' );
				$wp_customize->add_setting( $setting_id, $setting_args );
				$new_setting_ids[] = $setting_id;

				/**
				 * Add section to contain controls
				 */
				$section_id = sprintf( 'sidebar-widgets-%s', $sidebar_id );
				if ( $is_active_sidebar ) {
					$section_args = array(
						'title' => sprintf( __( 'Widgets: %s' ), $GLOBALS['wp_registered_sidebars'][$sidebar_id]['name'] ),
						'description' => $GLOBALS['wp_registered_sidebars'][$sidebar_id]['description'],
					);
					$section_args = apply_filters( 'customizer_widgets_section_args', $section_args, $section_id, $sidebar_id );
					$wp_customize->add_section( $section_id, $section_args );

					$control = new WP_Widget_Area_Customize_Control(
						$wp_customize,
						$setting_id,
						array(
							'section' => $section_id,
							'sidebar_id' => $sidebar_id,
							//'priority' => 99, // so it appears at the end
						)
					);
					$new_setting_ids[] = $setting_id;
					$wp_customize->add_control( $control );
				}
			}

			/**
			 * Add a control for each active widget (located in a sidebar)
			 */
			foreach ( $sidebar_widget_ids as $i => $widget_id ) {
				// Skip widgets that may have gone away due to a plugin being deactivated
				if ( ! $is_active_sidebar || ! isset( $GLOBALS['wp_registered_widgets'][$widget_id] ) ) {
					continue;
				}
				$registered_widget = $GLOBALS['wp_registered_widgets'][$widget_id];
				$setting_id = self::get_setting_id( $widget_id );
				$id_base = $GLOBALS['wp_registered_widget_controls'][$widget_id]['id_base'];
				assert( false !== is_active_widget( $registered_widget['callback'], $registered_widget['id'], false, false ) );
				$control = new WP_Widget_Form_Customize_Control(
					$wp_customize,
					$setting_id,
					array(
						'label' => $registered_widget['name'],
						'section' => $section_id,
						'sidebar_id' => $sidebar_id,
						'widget_id' => $widget_id,
						'widget_id_base' => $id_base,
						'priority' => $i,
						'width' => $wp_registered_widget_controls[$widget_id]['width'],
						'height' => $wp_registered_widget_controls[$widget_id]['height'],
						'is_wide' => self::is_wide_widget( $widget_id ),
					)
				);
				$wp_customize->add_control( $control );
			}
		}

		/**
		 * We have to register these settings later than customize_preview_init so that other
		 * filters have had a chance to run.
		 * @see self::schedule_customize_register()
		 */
		if ( did_action( 'customize_preview_init' ) ) {
			foreach ( $new_setting_ids as $new_setting_id ) {
				$wp_customize->get_setting( $new_setting_id )->preview();
			}
		}

		self::remove_prepreview_filters();
	}

	/**
	 * Covert a widget_id into its corresponding customizer setting id (option name)
	 *
	 * @param string $widget_id
	 * @see _get_widget_id_base()
	 * @return string
	 */
	static function get_setting_id( $widget_id ) {
		$parsed_widget_id = self::parse_widget_id( $widget_id );
		$setting_id = sprintf( 'widget_%s', $parsed_widget_id['id_base'] );
		if ( ! is_null( $parsed_widget_id['number'] ) ) {
			$setting_id .= sprintf( '[%d]', $parsed_widget_id['number'] );
		}
		return $setting_id;
	}

	/**
	 * Core widgets which may have controls wider than 250, but can still be
	 * shown in the narrow customizer panel. The RSS and Text widgets in Core,
	 * for example, have widths of 400 and yet they still render fine in the
	 * customizer panel. This method will return all Core widgets as being
	 * not wide, but this can be overridden with the is_wide_widget_in_customizer
	 * filter.
	 *
	 * @param string $widget_id
	 * @return bool
	 */
	static function is_wide_widget( $widget_id ) {
		global $wp_registered_widget_controls;
		$parsed_widget_id = self::parse_widget_id( $widget_id );
		$width = $wp_registered_widget_controls[$widget_id]['width'];
		$is_core = in_array( $parsed_widget_id['id_base'], self::$core_widget_id_bases );
		$is_wide = ( $width > 250 && ! $is_core );
		$is_wide = apply_filters( 'is_wide_widget_in_customizer', $is_wide, $widget_id );
		return $is_wide;
	}

	/**
	 * Covert a widget ID into its id_base and number components
	 *
	 * @param string $widget_id
	 * @return array
	 */
	static function parse_widget_id( $widget_id ) {
		$parsed = array(
			'number' => null,
			'id_base' => null,
		);
		if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $matches ) ) {
			$parsed['id_base'] = $matches[1];
			$parsed['number']  = intval( $matches[2] );
		} else {
			// likely an old single widget
			$parsed['id_base'] = $widget_id;
		}
		return $parsed;
	}

	/**
	 * Convert a widget setting ID (option path) to its id_base and number components
	 *
	 * @throws Widget_Customizer_Exception
	 * @throws Exception
	 *
	 * @param string $setting_id
	 * @param array
	 * @return array
	 */
	static function parse_widget_setting_id( $setting_id ) {
		if ( ! preg_match( '/^(widget_(.+?))(?:\[(\d+)\])?$/', $setting_id, $matches ) ) {
			throw new Widget_Customizer_Exception( sprintf( 'Invalid widget setting ID: %s', $setting_id ) );
		}
		$id_base = $matches[2];
		$number  = isset( $matches[3] ) ? intval( $matches[3] ) : null;
		return compact( 'id_base', 'number' );
	}

	/**
	 * Enqueue scripts and styles for customizer panel and export data to JS
	 *
	 * @action customize_controls_enqueue_scripts
	 */
	static function customize_controls_enqueue_deps() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_style(
			'widget-customizer',
			admin_url( 'css/customize-widgets.css' )
		);
		wp_enqueue_script(
			'widget-customizer',
			admin_url( 'js/customize-widgets.js' ),
			array( 'jquery', 'wp-backbone', 'wp-util', 'customize-controls' )
		);

		// Export available widgets with control_tpl removed from model
		// since plugins need templates to be in the DOM
		$available_widgets = array();
		foreach ( self::get_available_widgets() as $available_widget ) {
			unset( $available_widget['control_tpl'] );
			$available_widgets[] = $available_widget;
		}

		$widget_reorder_nav_tpl = sprintf(
			'<div class="widget-reorder-nav"><span class="move-widget" tabindex="0" title="%1$s">%2$s</span><span class="move-widget-down" tabindex="0" title="%3$s">%4$s</span><span class="move-widget-up" tabindex="0" title="%5$s">%6$s</span></div>',
			esc_attr__( 'Move to another area...' ),
			esc_html__( 'Move to another area...' ),
			esc_attr__( 'Move down' ),
			esc_html__( 'Move down' ),
			esc_attr__( 'Move up' ),
			esc_html__( 'Move up' )
		);

		$move_widget_area_tpl = str_replace(
			array( '{description}', '{btn}' ),
			array(
				esc_html__( 'Select an area to move this widget into:' ),
				esc_html__( 'Move' ),
			),
			'
				<div class="move-widget-area">
					<p class="description">{description}</p>
					<ul class="widget-area-select">
						<% _.each( sidebars, function ( sidebar ){ %>
							<li class="" data-id="<%- sidebar.id %>" title="<%- sidebar.description %>" tabindex="0"><%- sidebar.name %></li>
						<% }); %>
					</ul>
					<div class="move-widget-actions">
						<button class="move-widget-btn button-secondary" type="button">{btn}</button>
					</div>
				</div>
			'
		);

		// Why not wp_localize_script? Because we're not localizing, and it forces values into strings
		global $wp_scripts;
		$exports = array(
			'update_widget_ajax_action' => self::UPDATE_WIDGET_AJAX_ACTION,
			'update_widget_nonce_value' => wp_create_nonce( self::UPDATE_WIDGET_AJAX_ACTION ),
			'update_widget_nonce_post_key' => self::UPDATE_WIDGET_NONCE_POST_KEY,
			'registered_sidebars' => array_values( $GLOBALS['wp_registered_sidebars'] ),
			'registered_widgets' => $GLOBALS['wp_registered_widgets'],
			'available_widgets' => $available_widgets, // @todo Merge this with registered_widgets
			'i18n' => array(
				'save_btn_label' => _x( 'Apply', 'button to save changes to a widget' ),
				'save_btn_tooltip' => _x( 'Save and preview changes before publishing them.', 'tooltip on the widget save button' ),
				'remove_btn_label' => _x( 'Remove', 'link to move a widget to the inactive widgets sidebar' ),
				'remove_btn_tooltip' => _x( 'Trash widget by moving it to the inactive widgets sidebar.', 'tooltip on btn a widget to move it to the inactive widgets sidebar' ),
			),
			'tpl' => array(
				'widget_reorder_nav' => $widget_reorder_nav_tpl,
				'move_widget_area' => $move_widget_area_tpl,
			),
		);
		foreach ( $exports['registered_widgets'] as &$registered_widget ) {
			unset( $registered_widget['callback'] ); // may not be JSON-serializeable
		}

		$wp_scripts->add_data(
			'widget-customizer',
			'data',
			sprintf( 'var WidgetCustomizer_exports = %s;', json_encode( $exports ) )
		);
	}

	/**
	 * Render the widget form control templates into the DOM so that plugin scripts can manipulate them
	 *
	 * @action customize_controls_print_footer_scripts
	 */
	static function output_widget_control_templates() {
		?>
		<div id="widgets-left"><!-- compatibility with JS which looks for widget templates here -->
		<div id="available-widgets">
			<div id="available-widgets-filter">
				<input type="search" placeholder="<?php esc_attr_e( 'Find widgets&hellip;' ) ?>">
			</div>
			<?php foreach ( self::get_available_widgets() as $available_widget ): ?>
				<div id="widget-tpl-<?php echo esc_attr( $available_widget['id'] ) ?>" data-widget-id="<?php echo esc_attr( $available_widget['id'] ) ?>" class="widget-tpl <?php echo esc_attr( $available_widget['id'] ) ?>" tabindex="0">
					<?php echo $available_widget['control_tpl']; // xss ok ?>
				</div>
			<?php endforeach; ?>
		</div><!-- #available-widgets -->
		</div><!-- #widgets-left -->
		<?php
	}

	/**
	 * Get common arguments to supply when constructing a customizer setting
	 *
	 * @param string $id
	 * @param array  [$overrides]
	 * @return array
	 */
	static function get_setting_args( $id, $overrides = array() ) {
		$args = array(
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'transport' => 'refresh',
			'default' => array(),
		);
		$args = array_merge( $args, $overrides );
		$args = apply_filters( 'widget_customizer_setting_args', $args, $id );
		return $args;
	}

	/**
	 * Make sure that a sidebars_widgets[x] only ever consists of actual widget IDs.
	 * Used as sanitize_callback for each sidebars_widgets setting.
	 *
	 * @param array $widget_ids
	 * @return array
	 */
	static function sanitize_sidebar_widgets( $widget_ids ) {
		global $wp_registered_widgets;
		$widget_ids = array_map( 'strval', (array) $widget_ids );
		$sanitized_widget_ids = array();
		foreach ( $widget_ids as $widget_id ) {
			if ( array_key_exists( $widget_id, $wp_registered_widgets ) ) {
				$sanitized_widget_ids[] = $widget_id;
			}
		}
		return $sanitized_widget_ids;
	}

	/**
	 * Special filter for Settings Revisions plugin until it can handle
	 * dynamically creating settings.
	 *
	 * @param mixed $value
	 * @param stdClass|WP_Customize_Setting $setting
	 * @return mixed
	 */
	static function temp_customize_sanitize_js( $value, $setting ) {
		if ( preg_match( '/^widget_/', $setting->id ) && $setting->type === 'option' ) {
			$value = self::sanitize_widget_js_instance( $value );
		}
		return $value;
	}

	/**
	 * Build up an index of all available widgets for use in Backbone models
	 *
	 * @see wp_list_widgets()
	 * @return array
	 */
	static function get_available_widgets() {
		static $available_widgets = array();
		if ( ! empty( $available_widgets ) ) {
			return $available_widgets;
		}

		global $wp_registered_widgets, $wp_registered_widget_controls;
		require_once ABSPATH . '/wp-admin/includes/widgets.php'; // for next_widget_id_number()

		$sort = $wp_registered_widgets;
		usort( $sort, array( __CLASS__, '_sort_name_callback' ) );
		$done = array();

		foreach ( $sort as $widget ) {
			if ( in_array( $widget['callback'], $done, true ) ) { // We already showed this multi-widget
				continue;
			}

			$sidebar = is_active_widget( $widget['callback'], $widget['id'], false, false );
			$done[]  = $widget['callback'];

			if ( ! isset( $widget['params'][0] ) ) {
				$widget['params'][0] = array();
			}

			$available_widget = $widget;
			unset( $available_widget['callback'] ); // not serializable to JSON

			$args = array(
				'widget_id' => $widget['id'],
				'widget_name' => $widget['name'],
				'_display' => 'template',
			);

			$is_disabled     = false;
			$is_multi_widget = (
				isset( $wp_registered_widget_controls[$widget['id']]['id_base'] )
				&&
				isset( $widget['params'][0]['number'] )
			);
			if ( $is_multi_widget ) {
				$id_base = $wp_registered_widget_controls[$widget['id']]['id_base'];
				$args['_temp_id']   = "$id_base-__i__";
				$args['_multi_num'] = next_widget_id_number( $id_base );
				$args['_add']       = 'multi';
			} else {
				$args['_add'] = 'single';
				if ( $sidebar && 'wp_inactive_widgets' !== $sidebar ) {
					$is_disabled = true;
				}
				$id_base = $widget['id'];
			}

			$list_widget_controls_args = wp_list_widget_controls_dynamic_sidebar( array( 0 => $args, 1 => $widget['params'][0] ) );
			$control_tpl = self::get_widget_control( $list_widget_controls_args );

			// The properties here are mapped to the Backbone Widget model
			$available_widget = array_merge(
				$available_widget,
				array(
					'temp_id' => isset( $args['_temp_id'] ) ? $args['_temp_id'] : null,
					'is_multi' => $is_multi_widget,
					'control_tpl' => $control_tpl,
					'multi_number' => ( $args['_add'] === 'multi' ) ? $args['_multi_num'] : false,
					'is_disabled' => $is_disabled,
					'id_base' => $id_base,
					'transport' => 'refresh',
					'width' => $wp_registered_widget_controls[$widget['id']]['width'],
					'height' => $wp_registered_widget_controls[$widget['id']]['height'],
					'is_wide' => self::is_wide_widget( $widget['id'] ),
				)
			);

			$available_widgets[] = $available_widget;
		}
		return $available_widgets;
	}

	/**
	 * Replace with inline closure once on PHP 5.3:
	 * sort( $array, function ( $a, $b ) { return strnatcasecmp( $a['name'], $b['name'] ); } );
	 *
	 * @access private
	 */
	static function _sort_name_callback( $a, $b ) {
		return strnatcasecmp( $a['name'], $b['name'] );
	}

	/**
	 * Invoke wp_widget_control() but capture the output buffer and transform the markup
	 * so that it can be used in the customizer.
	 *
	 * @see wp_widget_control()
	 * @param array $args
	 * @return string
	 */
	static function get_widget_control( $args ) {
		ob_start();
		call_user_func_array( 'wp_widget_control', $args );
		$replacements = array(
			'<form action="" method="post">' => '<div class="form">',
			'</form>' => '</div><!-- .form -->',
		);
		$control_tpl = ob_get_clean();
		$control_tpl = str_replace( array_keys( $replacements ), array_values( $replacements ), $control_tpl );
		return $control_tpl;
	}

	/**
	 * Add hooks for the customizer preview
	 *
	 * @action customize_preview_init
	 */
	static function customize_preview_init() {
		add_filter( 'sidebars_widgets', array( __CLASS__, 'preview_sidebars_widgets' ), 1 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customize_preview_enqueue_deps' ) );
		add_action( 'wp_footer', array( __CLASS__, 'export_preview_data' ), 9999 );
	}

	/**
	 * When previewing, make sure the proper previewing widgets are used. Because wp_get_sidebars_widgets()
	 * gets called early at init (via wp_convert_widget_settings()) and can set global variable
	 * $_wp_sidebars_widgets to the value of get_option( 'sidebars_widgets' ) before the customizer
	 * preview filter is added, we have to reset it after the filter has been added.
	 *
	 * @filter sidebars_widgets
	 */
	static function preview_sidebars_widgets( $sidebars_widgets ) {
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		unset( $sidebars_widgets['array_version'] );
		return $sidebars_widgets;
	}

	/**
	 * Enqueue scripts for the customizer preview
	 *
	 * @action wp_enqueue_scripts
	 */
	static function customize_preview_enqueue_deps() {
		wp_enqueue_script(
			'customize-preview-widgets',
			includes_url( 'js/customize-preview-widgets.js' ),
			array( 'jquery', 'wp-util', 'customize-preview' )
		);

		/*
		wp_enqueue_style(
			'widget-customizer-preview',
			'widget-customizer-preview.css'
		);
		*/

		// Why not wp_localize_script? Because we're not localizing, and it forces values into strings
		global $wp_scripts;
		$exports = array(
			'registered_sidebars' => array_values( $GLOBALS['wp_registered_sidebars'] ),
			'registered_widgets' => $GLOBALS['wp_registered_widgets'],
			'i18n' => array(
				'widget_tooltip' => __( 'Press shift and then click to edit widget in customizer...' ),
			),
			'request_uri' => wp_unslash( $_SERVER['REQUEST_URI'] ),
		);
		foreach ( $exports['registered_widgets'] as &$registered_widget ) {
			unset( $registered_widget['callback'] ); // may not be JSON-serializeable
		}
		$wp_scripts->add_data(
			'customize-preview-widgets',
			'data',
			sprintf( 'var WidgetCustomizerPreview_exports = %s;', json_encode( $exports ) )
		);
	}

	/**
	 * At the very end of the page, at the very end of the wp_footer, communicate the sidebars that appeared on the page
	 *
	 * @action wp_footer
	 */
	static function export_preview_data() {
		wp_print_scripts( array( 'customize-preview-widgets' ) );
		?>
		<script>
		(function () {
			/*global WidgetCustomizerPreview */
			WidgetCustomizerPreview.rendered_sidebars = <?php echo json_encode( array_fill_keys( array_unique( self::$rendered_sidebars ), true ) ) ?>;
			WidgetCustomizerPreview.rendered_widgets = <?php echo json_encode( array_fill_keys( array_keys( self::$rendered_widgets ), true ) ); ?>;
		}());
		</script>
		<?php
	}

	static protected $rendered_sidebars = array();
	static protected $rendered_widgets  = array();

	/**
	 * Keep track of the widgets that were rendered
	 *
	 * @action dynamic_sidebar
	 */
	static function tally_rendered_widgets( $widget ) {
		self::$rendered_widgets[$widget['id']] = true;
	}

	/**
	 * This is hacky. It is too bad that dynamic_sidebar is not just called once with the $sidebar_id supplied
	 * This does not get called for a sidebar which lacks widgets.
	 * See core patch which addresses the problem.
	 *
	 * @link http://core.trac.wordpress.org/ticket/25368
	 * @action dynamic_sidebar
	 */
	static function tally_sidebars_via_dynamic_sidebar_actions( $widget ) {
		global $sidebars_widgets;
		foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {
			if ( in_array( $sidebar_id, self::$rendered_sidebars ) ) {
				continue;
			}
			if ( isset( $GLOBALS['wp_registered_sidebars'][$sidebar_id] ) && is_array( $widget_ids ) && in_array( $widget['id'], $widget_ids ) ) {
				self::$rendered_sidebars[] = $sidebar_id;
			}
		}
	}

	/**
	 * Keep track of the times that is_active_sidebar() is called in the template, and assume that this
	 * means that the sidebar would be rendered on the template if there were widgets populating it.
	 *
	 * @see http://core.trac.wordpress.org/ticket/25368
	 * @filter temp_is_active_sidebar
	 */
	static function tally_sidebars_via_is_active_sidebar_calls( $is_active, $sidebar_id ) {
		if ( isset( $GLOBALS['wp_registered_sidebars'][$sidebar_id] ) ) {
			self::$rendered_sidebars[] = $sidebar_id;
		}
		// We may need to force this to true, and also force-true the value for temp_dynamic_sidebar_has_widgets
		// if we want to ensure that there is an area to drop widgets into, if the sidebar is empty.
		return $is_active;
	}

	/**
	 * Keep track of the times that dynamic_sidebar() is called in the template, and assume that this
	 * means that the sidebar would be rendered on the template if there were widgets populating it.
	 *
	 * @see http://core.trac.wordpress.org/ticket/25368
	 * @filter temp_dynamic_sidebar_has_widgets
	 */
	static function tally_sidebars_via_dynamic_sidebar_calls( $has_widgets, $sidebar_id ) {
		if ( isset( $GLOBALS['wp_registered_sidebars'][$sidebar_id] ) ) {
			self::$rendered_sidebars[] = $sidebar_id;
		}
		// We may need to force this to true, and also force-true the value for temp_is_active_sidebar
		// if we want to ensure that there is an area to drop widgets into, if the sidebar is empty.
		return $has_widgets;
	}

	/**
	 * Serialize an instance and hash it with the AUTH_KEY; when a JS value is
	 * posted back to save, this instance hash key is used to ensure that the
	 * serialized_instance was not tampered with, but that it had originated
	 * from WordPress and so is sanitized.
	 *
	 * @param array $instance
	 * @return string
	 */
	protected static function get_instance_hash_key( $instance ) {
		$hash = md5( AUTH_KEY . serialize( $instance ) );
		return $hash;
	}

	/**
	 * Unserialize the JS-instance for storing in the options. It's important
	 * that this filter only get applied to an instance once.
	 *
	 * @see Widget_Customizer::sanitize_widget_js_instance()
	 *
	 * @param array $value
	 * @return array
	 */
	static function sanitize_widget_instance( $value ) {
		if ( $value === array() ) {
			return $value;
		}
		$invalid = (
			empty( $value['is_widget_customizer_js_value'] )
			||
			empty( $value['instance_hash_key'] )
			||
			empty( $value['encoded_serialized_instance'] )
		);
		if ( $invalid ) {
			return null;
		}
		$decoded = base64_decode( $value['encoded_serialized_instance'], true );
		if ( false === $decoded ) {
			return null;
		}
		$instance = unserialize( $decoded );
		if ( false === $instance ) {
			return null;
		}
		if ( self::get_instance_hash_key( $instance ) !== $value['instance_hash_key'] ) {
			return null;
		}
		return $instance;
	}

	/**
	 * Convert widget instance into JSON-representable format
	 *
	 * @see Widget_Customizer::sanitize_widget_instance()
	 *
	 * @param array $value
	 * @return array
	 */
	static function sanitize_widget_js_instance( $value ) {
		if ( empty( $value['is_widget_customizer_js_value'] ) ) {
			$serialized = serialize( $value );
			$value = array(
				'encoded_serialized_instance' => base64_encode( $serialized ),
				'title' => empty( $value['title'] ) ? '' : $value['title'],
				'is_widget_customizer_js_value' => true,
				'instance_hash_key' => self::get_instance_hash_key( $value ),
			);
		}
		return $value;
	}

	/**
	 * Strip out widget IDs for widgets which are no longer registered, such
	 * as the case when a plugin orphans a widget in a sidebar when it is deactivated.
	 *
	 * @param array $widget_ids
	 * @return array
	 */
	static function sanitize_sidebar_widgets_js_instance( $widget_ids ) {
		global $wp_registered_widgets;
		$widget_ids = array_values( array_intersect( $widget_ids, array_keys( $wp_registered_widgets ) ) );
		return $widget_ids;
	}

	/**
	 * Find and invoke the widget update and control callbacks. Requires that
	 * $_POST be populated with the instance data.
	 *
	 * @throws Widget_Customizer_Exception
	 * @throws Exception
	 *
	 * @param string $widget_id
	 * @return array
	 */
	static function call_widget_update( $widget_id ) {
		global $wp_registered_widget_updates, $wp_registered_widget_controls;

		$options_transaction = new Options_Transaction();

		try {
			$options_transaction->start();
			$parsed_id   = self::parse_widget_id( $widget_id );
			$option_name = 'widget_' . $parsed_id['id_base'];

			/**
			 * If a previously-sanitized instance is provided, populate the input vars
			 * with its values so that the widget update callback will read this instance
			 */
			$added_input_vars = array();
			if ( ! empty( $_POST['sanitized_widget_setting'] ) ) {
				$sanitized_widget_setting = json_decode( self::get_post_value( 'sanitized_widget_setting' ), true );
				if ( empty( $sanitized_widget_setting ) ) {
					throw new Widget_Customizer_Exception( 'Malformed sanitized_widget_setting' );
				}
				$instance = self::sanitize_widget_instance( $sanitized_widget_setting );
				if ( is_null( $instance ) ) {
					throw new Widget_Customizer_Exception( 'Unsanitary sanitized_widget_setting' );
				}
				if ( ! is_null( $parsed_id['number'] ) ) {
					$value = array();
					$value[$parsed_id['number']] = $instance;
					$key = 'widget-' . $parsed_id['id_base'];
					$_REQUEST[$key] = $_POST[$key] = wp_slash( $value );
					$added_input_vars[] = $key;
				} else {
					foreach ( $instance as $key => $value ) {
						$_REQUEST[$key] = $_POST[$key] = wp_slash( $value );
						$added_input_vars[] = $key;
					}
				}
			}

			/**
			 * Invoke the widget update callback
			 */
			foreach ( (array) $wp_registered_widget_updates as $name => $control ) {
				if ( $name === $parsed_id['id_base'] && is_callable( $control['callback'] ) ) {
					ob_start();
					call_user_func_array( $control['callback'], $control['params'] );
					ob_end_clean();
					break;
				}
			}

			// Clean up any input vars that were manually added
			foreach ( $added_input_vars as $key ) {
				unset( $_POST[$key] );
				unset( $_REQUEST[$key] );
			}

			/**
			 * Make sure the expected option was updated
			 */
			if ( 0 !== $options_transaction->count() ) {
				if ( count( $options_transaction->options ) > 1 ) {
					throw new Widget_Customizer_Exception( sprintf( 'Widget %1$s unexpectedly updated more than one option.', $widget_id ) );
				}
				$updated_option_name = key( $options_transaction->options );
				if ( $updated_option_name !== $option_name ) {
					throw new Widget_Customizer_Exception( sprintf( 'Widget %1$s updated option "%2$s", but expected "%3$s".', $widget_id, $updated_option_name, $option_name ) );
				}
			}

			/**
			 * Obtain the widget control with the updated instance in place
			 */
			ob_start();
			$form = $wp_registered_widget_controls[$widget_id];
			if ( $form ) {
				call_user_func_array( $form['callback'], $form['params'] );
			}
			$form = ob_get_clean();

			/**
			 * Obtain the widget instance
			 */
			$option = get_option( $option_name );
			if ( null !== $parsed_id['number'] ) {
				$instance = $option[$parsed_id['number']];
			} else {
				$instance = $option;
			}

			$options_transaction->rollback();
			return compact( 'instance', 'form' );
		}
		catch ( Exception $e ) {
			$options_transaction->rollback();
			throw $e;
		}
	}

	/**
	 * Allow customizer to update a widget using its form, but return the new
	 * instance info via Ajax instead of saving it to the options table.
	 * Most code here copied from wp_ajax_save_widget()
	 *
	 * @see wp_ajax_save_widget
	 * @todo Reuse wp_ajax_save_widget now that we have option transactions?
	 * @action wp_ajax_update_widget
	 */
	static function wp_ajax_update_widget() {
		$generic_error = __( 'An error has occurred. Please reload the page and try again.' );

		try {
			if ( ! check_ajax_referer( self::UPDATE_WIDGET_AJAX_ACTION, self::UPDATE_WIDGET_NONCE_POST_KEY, false ) ) {
				throw new Widget_Customizer_Exception( __( 'Nonce check failed. Reload and try again?' ) );
			}
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				throw new Widget_Customizer_Exception( __( 'Current user cannot!' ) );
			}
			if ( ! isset( $_POST['widget-id'] ) ) {
				throw new Widget_Customizer_Exception( __( 'Incomplete request' ) );
			}

			unset( $_POST[self::UPDATE_WIDGET_NONCE_POST_KEY], $_POST['action'] );

			do_action( 'load-widgets.php' );
			do_action( 'widgets.php' );
			do_action( 'sidebar_admin_setup' );

			$widget_id = self::get_post_value( 'widget-id' );
			$parsed_id = self::parse_widget_id( $widget_id );
			$id_base   = $parsed_id['id_base'];

			if ( isset( $_POST['widget-' . $id_base] ) && is_array( $_POST['widget-' . $id_base] ) && preg_match( '/__i__|%i%/', key( $_POST['widget-' . $id_base] ) ) ) {
				throw new Widget_Customizer_Exception( 'Cannot pass widget templates to create new instances; apply template vars in JS' );
			}

			$updated_widget = self::call_widget_update( $widget_id ); // => {instance,form}
			$form = $updated_widget['form'];
			$instance = self::sanitize_widget_js_instance( $updated_widget['instance'] );

			wp_send_json_success( compact( 'form', 'instance' ) );
		}
		catch( Exception $e ) {
			if ( $e instanceof Widget_Customizer_Exception ) {
				$message = $e->getMessage();
			} else {
				error_log( sprintf( '%s in %s: %s', get_class( $e ), __FUNCTION__, $e->getMessage() ) );
				$message = $generic_error;
			}
			wp_send_json_error( compact( 'message' ) );
		}
	}
}

class Widget_Customizer_Exception extends Exception {}

class Options_Transaction {

	/**
	 * @var array $options values updated while transaction is open
	 */
	public $options = array();

	protected $_ignore_transients = true;
	protected $_is_current = false;
	protected $_operations = array();

	function __construct( $ignore_transients = true ) {
		$this->_ignore_transients = $ignore_transients;
	}

	/**
	 * Determine whether or not the transaction is open
	 * @return bool
	 */
	function is_current() {
		return $this->_is_current;
	}

	/**
	 * @param $option_name
	 * @return boolean
	 */
	function is_option_ignored( $option_name ) {
		return ( $this->_ignore_transients && 0 === strpos( $option_name, '_transient_' ) );
	}

	/**
	 * Get the number of operations performed in the transaction
	 * @return bool
	 */
	function count() {
		return count( $this->_operations );
	}

	/**
	 * Start keeping track of changes to options, and cache their new values
	 */
	function start() {
		$this->_is_current = true;
		add_action( 'added_option', array( $this, '_capture_added_option' ), 10, 2 );
		add_action( 'updated_option', array( $this, '_capture_updated_option' ), 10, 3 );
		add_action( 'delete_option', array( $this, '_capture_pre_deleted_option' ), 10, 1 );
		add_action( 'deleted_option', array( $this, '_capture_deleted_option' ), 10, 1 );
	}

	/**
	 * @action added_option
	 * @param $option_name
	 * @param $new_value
	 */
	function _capture_added_option( $option_name, $new_value ) {
		if ( $this->is_option_ignored( $option_name ) ) {
			return;
		}
		$this->options[$option_name] = $new_value;
		$operation = 'add';
		$this->_operations[] = compact( 'operation', 'option_name', 'new_value' );
	}

	/**
	 * @action updated_option
	 * @param string $option_name
	 * @param mixed $old_value
	 * @param mixed $new_value
	 */
	function _capture_updated_option( $option_name, $old_value, $new_value ) {
		if ( $this->is_option_ignored( $option_name ) ) {
			return;
		}
		$this->options[$option_name] = $new_value;
		$operation = 'update';
		$this->_operations[] = compact( 'operation', 'option_name', 'old_value', 'new_value' );
	}

	protected $_pending_delete_option_autoload;
	protected $_pending_delete_option_value;

	/**
	 * It's too bad the old_value and autoload aren't passed into the deleted_option action
	 * @action delete_option
	 * @param string $option_name
	 */
	function _capture_pre_deleted_option( $option_name ) {
		if ( $this->is_option_ignored( $option_name ) ) {
			return;
		}
		global $wpdb;
		$autoload = $wpdb->get_var( $wpdb->prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option_name ) ); // db call ok; no-cache ok
		$this->_pending_delete_option_autoload = $autoload;
		$this->_pending_delete_option_value    = get_option( $option_name );
	}

	/**
	 * @action deleted_option
	 * @param string $option_name
	 */
	function _capture_deleted_option( $option_name ) {
		if ( $this->is_option_ignored( $option_name ) ) {
			return;
		}
		unset( $this->options[$option_name] );
		$operation = 'delete';
		$old_value = $this->_pending_delete_option_value;
		$autoload  = $this->_pending_delete_option_autoload;
		$this->_operations[] = compact( 'operation', 'option_name', 'old_value', 'autoload' );
	}

	/**
	 * Undo any changes to the options since start() was called
	 */
	function rollback() {
		remove_action( 'updated_option', array( $this, '_capture_updated_option' ), 10, 3 );
		remove_action( 'added_option', array( $this, '_capture_added_option' ), 10, 2 );
		remove_action( 'delete_option', array( $this, '_capture_pre_deleted_option' ), 10, 1 );
		remove_action( 'deleted_option', array( $this, '_capture_deleted_option' ), 10, 1 );
		while ( 0 !== count( $this->_operations ) ) {
			$option_operation = array_pop( $this->_operations );
			if ( 'add' === $option_operation['operation'] ) {
				delete_option( $option_operation['option_name'] );
			}
			else if ( 'delete' === $option_operation['operation'] ) {
				add_option( $option_operation['option_name'], $option_operation['old_value'], null, $option_operation['autoload'] );
			}
			else if ( 'update' === $option_operation['operation'] ) {
				update_option( $option_operation['option_name'], $option_operation['old_value'] );
			}
			else {
				throw new Exception( 'Unexpected operation' );
			}
		}
		$this->_is_current = false;
	}
}
