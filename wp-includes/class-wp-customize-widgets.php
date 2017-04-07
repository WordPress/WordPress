<?php
/**
 * WordPress Customize Widgets classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.9.0
 */

/**
 * Customize Widgets class.
 *
 * Implements widget management in the Customizer.
 *
 * @since 3.9.0
 *
 * @see WP_Customize_Manager
 */
final class WP_Customize_Widgets {

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 3.9.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * All id_bases for widgets defined in core.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $core_widget_id_bases = array(
		'archives', 'calendar', 'categories', 'links', 'meta',
		'nav_menu', 'pages', 'recent-comments', 'recent-posts',
		'rss', 'search', 'tag_cloud', 'text',
	);

	/**
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $rendered_sidebars = array();

	/**
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $rendered_widgets = array();

	/**
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $old_sidebars_widgets = array();

	/**
	 * Mapping of widget ID base to whether it supports selective refresh.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var array
	 */
	protected $selective_refreshable_widgets;

	/**
	 * Mapping of setting type to setting ID pattern.
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var array
	 */
	protected $setting_id_patterns = array(
		'widget_instance' => '/^widget_(?P<id_base>.+?)(?:\[(?P<widget_number>\d+)\])?$/',
		'sidebar_widgets' => '/^sidebars_widgets\[(?P<sidebar_id>.+?)\]$/',
	);

	/**
	 * Initial loader.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Customize manager bootstrap instance.
	 */
	public function __construct( $manager ) {
		$this->manager = $manager;

		// See https://github.com/xwp/wp-customize-snapshots/blob/962586659688a5b1fd9ae93618b7ce2d4e7a421c/php/class-customize-snapshot-manager.php#L420-L449
		add_filter( 'customize_dynamic_setting_args',          array( $this, 'filter_customize_dynamic_setting_args' ), 10, 2 );
		add_action( 'widgets_init',                            array( $this, 'register_settings' ), 95 );
		add_action( 'customize_register',                      array( $this, 'schedule_customize_register' ), 1 );

		// Skip remaining hooks when the user can't manage widgets anyway.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		add_action( 'wp_loaded',                               array( $this, 'override_sidebars_widgets_for_theme_switch' ) );
		add_action( 'customize_controls_init',                 array( $this, 'customize_controls_init' ) );
		add_action( 'customize_controls_enqueue_scripts',      array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_styles',         array( $this, 'print_styles' ) );
		add_action( 'customize_controls_print_scripts',        array( $this, 'print_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_footer_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'output_widget_control_templates' ) );
		add_action( 'customize_preview_init',                  array( $this, 'customize_preview_init' ) );
		add_filter( 'customize_refresh_nonces',                array( $this, 'refresh_nonces' ) );

		add_action( 'dynamic_sidebar',                         array( $this, 'tally_rendered_widgets' ) );
		add_filter( 'is_active_sidebar',                       array( $this, 'tally_sidebars_via_is_active_sidebar_calls' ), 10, 2 );
		add_filter( 'dynamic_sidebar_has_widgets',             array( $this, 'tally_sidebars_via_dynamic_sidebar_calls' ), 10, 2 );

		// Selective Refresh.
		add_filter( 'customize_dynamic_partial_args',          array( $this, 'customize_dynamic_partial_args' ), 10, 2 );
		add_action( 'customize_preview_init',                  array( $this, 'selective_refresh_init' ) );
	}

	/**
	 * List whether each registered widget can be use selective refresh.
	 *
	 * If the theme does not support the customize-selective-refresh-widgets feature,
	 * then this will always return an empty array.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @return array Mapping of id_base to support. If theme doesn't support
	 *               selective refresh, an empty array is returned.
	 */
	public function get_selective_refreshable_widgets() {
		global $wp_widget_factory;
		if ( ! current_theme_supports( 'customize-selective-refresh-widgets' ) ) {
			return array();
		}
		if ( ! isset( $this->selective_refreshable_widgets ) ) {
			$this->selective_refreshable_widgets = array();
			foreach ( $wp_widget_factory->widgets as $wp_widget ) {
				$this->selective_refreshable_widgets[ $wp_widget->id_base ] = ! empty( $wp_widget->widget_options['customize_selective_refresh'] );
			}
		}
		return $this->selective_refreshable_widgets;
	}

	/**
	 * Determines if a widget supports selective refresh.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param string $id_base Widget ID Base.
	 * @return bool Whether the widget can be selective refreshed.
	 */
	public function is_widget_selective_refreshable( $id_base ) {
		$selective_refreshable_widgets = $this->get_selective_refreshable_widgets();
		return ! empty( $selective_refreshable_widgets[ $id_base ] );
	}

	/**
	 * Retrieves the widget setting type given a setting ID.
	 *
	 * @since 4.2.0
	 * @access protected
	 *
	 * @staticvar array $cache
	 *
	 * @param string $setting_id Setting ID.
	 * @return string|void Setting type.
	 */
	protected function get_setting_type( $setting_id ) {
		static $cache = array();
		if ( isset( $cache[ $setting_id ] ) ) {
			return $cache[ $setting_id ];
		}
		foreach ( $this->setting_id_patterns as $type => $pattern ) {
			if ( preg_match( $pattern, $setting_id ) ) {
				$cache[ $setting_id ] = $type;
				return $type;
			}
		}
	}

	/**
	 * Inspects the incoming customized data for any widget settings, and dynamically adds
	 * them up-front so widgets will be initialized properly.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function register_settings() {
		$widget_setting_ids = array();
		$incoming_setting_ids = array_keys( $this->manager->unsanitized_post_values() );
		foreach ( $incoming_setting_ids as $setting_id ) {
			if ( ! is_null( $this->get_setting_type( $setting_id ) ) ) {
				$widget_setting_ids[] = $setting_id;
			}
		}
		if ( $this->manager->doing_ajax( 'update-widget' ) && isset( $_REQUEST['widget-id'] ) ) {
			$widget_setting_ids[] = $this->get_setting_id( wp_unslash( $_REQUEST['widget-id'] ) );
		}

		$settings = $this->manager->add_dynamic_settings( array_unique( $widget_setting_ids ) );

		/*
		 * Preview settings right away so that widgets and sidebars will get registered properly.
		 * But don't do this if a customize_save because this will cause WP to think there is nothing
		 * changed that needs to be saved.
		 */
		if ( ! $this->manager->doing_ajax( 'customize_save' ) ) {
			foreach ( $settings as $setting ) {
				$setting->preview();
			}
		}
	}

	/**
	 * Determines the arguments for a dynamically-created setting.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @param false|array $args       The arguments to the WP_Customize_Setting constructor.
	 * @param string      $setting_id ID for dynamic setting, usually coming from `$_POST['customized']`.
	 * @return false|array Setting arguments, false otherwise.
	 */
	public function filter_customize_dynamic_setting_args( $args, $setting_id ) {
		if ( $this->get_setting_type( $setting_id ) ) {
			$args = $this->get_setting_args( $setting_id );
		}
		return $args;
	}

	/**
	 * Retrieves an unslashed post value or return a default.
	 *
	 * @since 3.9.0
	 * @access protected
	 *
	 * @param string $name    Post value.
	 * @param mixed  $default Default post value.
	 * @return mixed Unslashed post value or default value.
	 */
	protected function get_post_value( $name, $default = null ) {
		if ( ! isset( $_POST[ $name ] ) ) {
			return $default;
		}

		return wp_unslash( $_POST[ $name ] );
	}

	/**
	 * Override sidebars_widgets for theme switch.
	 *
	 * When switching a theme via the Customizer, supply any previously-configured
	 * sidebars_widgets from the target theme as the initial sidebars_widgets
	 * setting. Also store the old theme's existing settings so that they can
	 * be passed along for storing in the sidebars_widgets theme_mod when the
	 * theme gets switched.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global array $sidebars_widgets
	 * @global array $_wp_sidebars_widgets
	 */
	public function override_sidebars_widgets_for_theme_switch() {
		global $sidebars_widgets;

		if ( $this->manager->doing_ajax() || $this->manager->is_theme_active() ) {
			return;
		}

		$this->old_sidebars_widgets = wp_get_sidebars_widgets();
		add_filter( 'customize_value_old_sidebars_widgets_data', array( $this, 'filter_customize_value_old_sidebars_widgets_data' ) );
		$this->manager->set_post_value( 'old_sidebars_widgets_data', $this->old_sidebars_widgets ); // Override any value cached in changeset.

		// retrieve_widgets() looks at the global $sidebars_widgets
		$sidebars_widgets = $this->old_sidebars_widgets;
		$sidebars_widgets = retrieve_widgets( 'customize' );
		add_filter( 'option_sidebars_widgets', array( $this, 'filter_option_sidebars_widgets_for_theme_switch' ), 1 );
		// reset global cache var used by wp_get_sidebars_widgets()
		unset( $GLOBALS['_wp_sidebars_widgets'] );
	}

	/**
	 * Filters old_sidebars_widgets_data Customizer setting.
	 *
	 * When switching themes, filter the Customizer setting old_sidebars_widgets_data
	 * to supply initial $sidebars_widgets before they were overridden by retrieve_widgets().
	 * The value for old_sidebars_widgets_data gets set in the old theme's sidebars_widgets
	 * theme_mod.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @see WP_Customize_Widgets::handle_theme_switch()
	 *
	 * @param array $old_sidebars_widgets
	 * @return array
	 */
	public function filter_customize_value_old_sidebars_widgets_data( $old_sidebars_widgets ) {
		return $this->old_sidebars_widgets;
	}

	/**
	 * Filters sidebars_widgets option for theme switch.
	 *
	 * When switching themes, the retrieve_widgets() function is run when the Customizer initializes,
	 * and then the new sidebars_widgets here get supplied as the default value for the sidebars_widgets
	 * option.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @see WP_Customize_Widgets::handle_theme_switch()
	 * @global array $sidebars_widgets
	 *
	 * @param array $sidebars_widgets
	 * @return array
	 */
	public function filter_option_sidebars_widgets_for_theme_switch( $sidebars_widgets ) {
		$sidebars_widgets = $GLOBALS['sidebars_widgets'];
		$sidebars_widgets['array_version'] = 3;
		return $sidebars_widgets;
	}

	/**
	 * Ensures all widgets get loaded into the Customizer.
	 *
	 * Note: these actions are also fired in wp_ajax_update_widget().
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function customize_controls_init() {
		/** This action is documented in wp-admin/includes/ajax-actions.php */
		do_action( 'load-widgets.php' );

		/** This action is documented in wp-admin/includes/ajax-actions.php */
		do_action( 'widgets.php' );

		/** This action is documented in wp-admin/widgets.php */
		do_action( 'sidebar_admin_setup' );
	}

	/**
	 * Ensures widgets are available for all types of previews.
	 *
	 * When in preview, hook to {@see 'customize_register'} for settings after WordPress is loaded
	 * so that all filters have been initialized (e.g. Widget Visibility).
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function schedule_customize_register() {
		if ( is_admin() ) {
			$this->customize_register();
		} else {
			add_action( 'wp', array( $this, 'customize_register' ) );
		}
	}

	/**
	 * Registers Customizer settings and controls for all sidebars and widgets.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global array $wp_registered_widgets
	 * @global array $wp_registered_widget_controls
	 * @global array $wp_registered_sidebars
	 */
	public function customize_register() {
		global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_sidebars;

		add_filter( 'sidebars_widgets', array( $this, 'preview_sidebars_widgets' ), 1 );

		$sidebars_widgets = array_merge(
			array( 'wp_inactive_widgets' => array() ),
			array_fill_keys( array_keys( $wp_registered_sidebars ), array() ),
			wp_get_sidebars_widgets()
		);

		$new_setting_ids = array();

		/*
		 * Register a setting for all widgets, including those which are active,
		 * inactive, and orphaned since a widget may get suppressed from a sidebar
		 * via a plugin (like Widget Visibility).
		 */
		foreach ( array_keys( $wp_registered_widgets ) as $widget_id ) {
			$setting_id   = $this->get_setting_id( $widget_id );
			$setting_args = $this->get_setting_args( $setting_id );
			if ( ! $this->manager->get_setting( $setting_id ) ) {
				$this->manager->add_setting( $setting_id, $setting_args );
			}
			$new_setting_ids[] = $setting_id;
		}

		/*
		 * Add a setting which will be supplied for the theme's sidebars_widgets
		 * theme_mod when the theme is switched.
		 */
		if ( ! $this->manager->is_theme_active() ) {
			$setting_id = 'old_sidebars_widgets_data';
			$setting_args = $this->get_setting_args( $setting_id, array(
				'type' => 'global_variable',
				'dirty' => true,
			) );
			$this->manager->add_setting( $setting_id, $setting_args );
		}

		$this->manager->add_panel( 'widgets', array(
			'type'            => 'widgets',
			'title'           => __( 'Widgets' ),
			'description'     => __( 'Widgets are independent sections of content that can be placed into widgetized areas provided by your theme (commonly called sidebars).' ),
			'priority'        => 110,
			'active_callback' => array( $this, 'is_panel_active' ),
			'auto_expand_sole_section' => true,
		) );

		foreach ( $sidebars_widgets as $sidebar_id => $sidebar_widget_ids ) {
			if ( empty( $sidebar_widget_ids ) ) {
				$sidebar_widget_ids = array();
			}

			$is_registered_sidebar = is_registered_sidebar( $sidebar_id );
			$is_inactive_widgets   = ( 'wp_inactive_widgets' === $sidebar_id );
			$is_active_sidebar     = ( $is_registered_sidebar && ! $is_inactive_widgets );

			// Add setting for managing the sidebar's widgets.
			if ( $is_registered_sidebar || $is_inactive_widgets ) {
				$setting_id   = sprintf( 'sidebars_widgets[%s]', $sidebar_id );
				$setting_args = $this->get_setting_args( $setting_id );
				if ( ! $this->manager->get_setting( $setting_id ) ) {
					if ( ! $this->manager->is_theme_active() ) {
						$setting_args['dirty'] = true;
					}
					$this->manager->add_setting( $setting_id, $setting_args );
				}
				$new_setting_ids[] = $setting_id;

				// Add section to contain controls.
				$section_id = sprintf( 'sidebar-widgets-%s', $sidebar_id );
				if ( $is_active_sidebar ) {

					$section_args = array(
						'title' => $wp_registered_sidebars[ $sidebar_id ]['name'],
						'description' => $wp_registered_sidebars[ $sidebar_id ]['description'],
						'priority' => array_search( $sidebar_id, array_keys( $wp_registered_sidebars ) ),
						'panel' => 'widgets',
						'sidebar_id' => $sidebar_id,
					);

					/**
					 * Filters Customizer widget section arguments for a given sidebar.
					 *
					 * @since 3.9.0
					 *
					 * @param array      $section_args Array of Customizer widget section arguments.
					 * @param string     $section_id   Customizer section ID.
					 * @param int|string $sidebar_id   Sidebar ID.
					 */
					$section_args = apply_filters( 'customizer_widgets_section_args', $section_args, $section_id, $sidebar_id );

					$section = new WP_Customize_Sidebar_Section( $this->manager, $section_id, $section_args );
					$this->manager->add_section( $section );

					$control = new WP_Widget_Area_Customize_Control( $this->manager, $setting_id, array(
						'section'    => $section_id,
						'sidebar_id' => $sidebar_id,
						'priority'   => count( $sidebar_widget_ids ), // place 'Add Widget' and 'Reorder' buttons at end.
					) );
					$new_setting_ids[] = $setting_id;

					$this->manager->add_control( $control );
				}
			}

			// Add a control for each active widget (located in a sidebar).
			foreach ( $sidebar_widget_ids as $i => $widget_id ) {

				// Skip widgets that may have gone away due to a plugin being deactivated.
				if ( ! $is_active_sidebar || ! isset( $wp_registered_widgets[$widget_id] ) ) {
					continue;
				}

				$registered_widget = $wp_registered_widgets[$widget_id];
				$setting_id        = $this->get_setting_id( $widget_id );
				$id_base           = $wp_registered_widget_controls[$widget_id]['id_base'];

				$control = new WP_Widget_Form_Customize_Control( $this->manager, $setting_id, array(
					'label'          => $registered_widget['name'],
					'section'        => $section_id,
					'sidebar_id'     => $sidebar_id,
					'widget_id'      => $widget_id,
					'widget_id_base' => $id_base,
					'priority'       => $i,
					'width'          => $wp_registered_widget_controls[$widget_id]['width'],
					'height'         => $wp_registered_widget_controls[$widget_id]['height'],
					'is_wide'        => $this->is_wide_widget( $widget_id ),
				) );
				$this->manager->add_control( $control );
			}
		}

		if ( ! $this->manager->doing_ajax( 'customize_save' ) ) {
			foreach ( $new_setting_ids as $new_setting_id ) {
				$this->manager->get_setting( $new_setting_id )->preview();
			}
		}
	}

	/**
	 * Determines whether the widgets panel is active, based on whether there are sidebars registered.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @see WP_Customize_Panel::$active_callback
	 *
	 * @global array $wp_registered_sidebars
	 * @return bool Active.
	 */
	public function is_panel_active() {
		global $wp_registered_sidebars;
		return ! empty( $wp_registered_sidebars );
	}

	/**
	 * Converts a widget_id into its corresponding Customizer setting ID (option name).
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $widget_id Widget ID.
	 * @return string Maybe-parsed widget ID.
	 */
	public function get_setting_id( $widget_id ) {
		$parsed_widget_id = $this->parse_widget_id( $widget_id );
		$setting_id       = sprintf( 'widget_%s', $parsed_widget_id['id_base'] );

		if ( ! is_null( $parsed_widget_id['number'] ) ) {
			$setting_id .= sprintf( '[%d]', $parsed_widget_id['number'] );
		}
		return $setting_id;
	}

	/**
	 * Determines whether the widget is considered "wide".
	 *
	 * Core widgets which may have controls wider than 250, but can still be shown
	 * in the narrow Customizer panel. The RSS and Text widgets in Core, for example,
	 * have widths of 400 and yet they still render fine in the Customizer panel.
	 *
	 * This method will return all Core widgets as being not wide, but this can be
	 * overridden with the {@see 'is_wide_widget_in_customizer'} filter.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global $wp_registered_widget_controls
	 *
	 * @param string $widget_id Widget ID.
	 * @return bool Whether or not the widget is a "wide" widget.
	 */
	public function is_wide_widget( $widget_id ) {
		global $wp_registered_widget_controls;

		$parsed_widget_id = $this->parse_widget_id( $widget_id );
		$width            = $wp_registered_widget_controls[$widget_id]['width'];
		$is_core          = in_array( $parsed_widget_id['id_base'], $this->core_widget_id_bases );
		$is_wide          = ( $width > 250 && ! $is_core );

		/**
		 * Filters whether the given widget is considered "wide".
		 *
		 * @since 3.9.0
		 *
		 * @param bool   $is_wide   Whether the widget is wide, Default false.
		 * @param string $widget_id Widget ID.
		 */
		return apply_filters( 'is_wide_widget_in_customizer', $is_wide, $widget_id );
	}

	/**
	 * Converts a widget ID into its id_base and number components.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $widget_id Widget ID.
	 * @return array Array containing a widget's id_base and number components.
	 */
	public function parse_widget_id( $widget_id ) {
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
	 * Converts a widget setting ID (option path) to its id_base and number components.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $setting_id Widget setting ID.
	 * @return WP_Error|array Array containing a widget's id_base and number components,
	 *                        or a WP_Error object.
	 */
	public function parse_widget_setting_id( $setting_id ) {
		if ( ! preg_match( '/^(widget_(.+?))(?:\[(\d+)\])?$/', $setting_id, $matches ) ) {
			return new WP_Error( 'widget_setting_invalid_id' );
		}

		$id_base = $matches[2];
		$number  = isset( $matches[3] ) ? intval( $matches[3] ) : null;

		return compact( 'id_base', 'number' );
	}

	/**
	 * Calls admin_print_styles-widgets.php and admin_print_styles hooks to
	 * allow custom styles from plugins.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function print_styles() {
		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_styles-widgets.php' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_styles' );
	}

	/**
	 * Calls admin_print_scripts-widgets.php and admin_print_scripts hooks to
	 * allow custom scripts from plugins.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function print_scripts() {
		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_scripts-widgets.php' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_scripts' );
	}

	/**
	 * Enqueues scripts and styles for Customizer panel and export data to JavaScript.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global WP_Scripts $wp_scripts
	 * @global array $wp_registered_sidebars
	 * @global array $wp_registered_widgets
	 */
	public function enqueue_scripts() {
		global $wp_scripts, $wp_registered_sidebars, $wp_registered_widgets;

		wp_enqueue_style( 'customize-widgets' );
		wp_enqueue_script( 'customize-widgets' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_enqueue_scripts', 'widgets.php' );

		/*
		 * Export available widgets with control_tpl removed from model
		 * since plugins need templates to be in the DOM.
		 */
		$available_widgets = array();

		foreach ( $this->get_available_widgets() as $available_widget ) {
			unset( $available_widget['control_tpl'] );
			$available_widgets[] = $available_widget;
		}

		$widget_reorder_nav_tpl = sprintf(
			'<div class="widget-reorder-nav"><span class="move-widget" tabindex="0">%1$s</span><span class="move-widget-down" tabindex="0">%2$s</span><span class="move-widget-up" tabindex="0">%3$s</span></div>',
			__( 'Move to another area&hellip;' ),
			__( 'Move down' ),
			__( 'Move up' )
		);

		$move_widget_area_tpl = str_replace(
			array( '{description}', '{btn}' ),
			array(
				__( 'Select an area to move this widget into:' ),
				_x( 'Move', 'Move widget' ),
			),
			'<div class="move-widget-area">
				<p class="description">{description}</p>
				<ul class="widget-area-select">
					<% _.each( sidebars, function ( sidebar ){ %>
						<li class="" data-id="<%- sidebar.id %>" title="<%- sidebar.description %>" tabindex="0"><%- sidebar.name %></li>
					<% }); %>
				</ul>
				<div class="move-widget-actions">
					<button class="move-widget-btn button" type="button">{btn}</button>
				</div>
			</div>'
		);

		/*
		 * Gather all strings in PHP that may be needed by JS on the client.
		 * Once JS i18n is implemented (in #20491), this can be removed.
		 */
		$some_non_rendered_areas_messages = array();
		$some_non_rendered_areas_messages[1] = html_entity_decode(
			/* translators: placeholder is the number of other widget areas registered but not rendered */
			__( 'Your theme has 1 other widget area, but this particular page doesn&#8217;t display it.' ),
			ENT_QUOTES,
			get_bloginfo( 'charset' )
		);
		$registered_sidebar_count = count( $wp_registered_sidebars );
		for ( $non_rendered_count = 2; $non_rendered_count < $registered_sidebar_count; $non_rendered_count++ ) {
			$some_non_rendered_areas_messages[ $non_rendered_count ] = html_entity_decode( sprintf(
				/* translators: placeholder is the number of other widget areas registered but not rendered */
				_n(
					'Your theme has %s other widget area, but this particular page doesn&#8217;t display it.',
					'Your theme has %s other widget areas, but this particular page doesn&#8217;t display them.',
					$non_rendered_count
				),
				number_format_i18n( $non_rendered_count )
			), ENT_QUOTES, get_bloginfo( 'charset' ) );
		}

		if ( 1 === $registered_sidebar_count ) {
			$no_areas_shown_message = html_entity_decode( sprintf(
				/* translators: placeholder is the total number of widget areas registered */
				__( 'Your theme has 1 widget area, but this particular page doesn&#8217;t display it.' )
			), ENT_QUOTES, get_bloginfo( 'charset' ) );
		} else {
			$no_areas_shown_message = html_entity_decode( sprintf(
				/* translators: placeholder is the total number of widget areas registered */
				_n(
					'Your theme has %s widget area, but this particular page doesn&#8217;t display it.',
					'Your theme has %s widget areas, but this particular page doesn&#8217;t display them.',
					$registered_sidebar_count
				),
				number_format_i18n( $registered_sidebar_count )
			), ENT_QUOTES, get_bloginfo( 'charset' ) );
		}

		$settings = array(
			'registeredSidebars'   => array_values( $wp_registered_sidebars ),
			'registeredWidgets'    => $wp_registered_widgets,
			'availableWidgets'     => $available_widgets, // @todo Merge this with registered_widgets
			'l10n' => array(
				'saveBtnLabel'     => __( 'Apply' ),
				'saveBtnTooltip'   => __( 'Save and preview changes before publishing them.' ),
				'removeBtnLabel'   => __( 'Remove' ),
				'removeBtnTooltip' => __( 'Trash widget by moving it to the inactive widgets sidebar.' ),
				'error'            => __( 'An error has occurred. Please reload the page and try again.' ),
				'widgetMovedUp'    => __( 'Widget moved up' ),
				'widgetMovedDown'  => __( 'Widget moved down' ),
				'navigatePreview'  => __( 'You can navigate to other pages on your site while using the Customizer to view and edit the widgets displayed on those pages.' ),
				'someAreasShown'   => $some_non_rendered_areas_messages,
				'noAreasShown'     => $no_areas_shown_message,
				'reorderModeOn'    => __( 'Reorder mode enabled' ),
				'reorderModeOff'   => __( 'Reorder mode closed' ),
				'reorderLabelOn'   => esc_attr__( 'Reorder widgets' ),
				/* translators: placeholder is the count for the number of widgets found */
				'widgetsFound'     => __( 'Number of widgets found: %d' ),
				'noWidgetsFound'   => __( 'No widgets found.' ),
			),
			'tpl' => array(
				'widgetReorderNav' => $widget_reorder_nav_tpl,
				'moveWidgetArea'   => $move_widget_area_tpl,
			),
			'selectiveRefreshableWidgets' => $this->get_selective_refreshable_widgets(),
		);

		foreach ( $settings['registeredWidgets'] as &$registered_widget ) {
			unset( $registered_widget['callback'] ); // may not be JSON-serializeable
		}

		$wp_scripts->add_data(
			'customize-widgets',
			'data',
			sprintf( 'var _wpCustomizeWidgetsSettings = %s;', wp_json_encode( $settings ) )
		);
	}

	/**
	 * Renders the widget form control templates into the DOM.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function output_widget_control_templates() {
		?>
		<div id="widgets-left"><!-- compatibility with JS which looks for widget templates here -->
		<div id="available-widgets">
			<div class="customize-section-title">
				<button class="customize-section-back" tabindex="-1">
					<span class="screen-reader-text"><?php _e( 'Back' ); ?></span>
				</button>
				<h3>
					<span class="customize-action"><?php
						/* translators: &#9656; is the unicode right-pointing triangle, and %s is the section title in the Customizer */
						echo sprintf( __( 'Customizing &#9656; %s' ), esc_html( $this->manager->get_panel( 'widgets' )->title ) );
					?></span>
					<?php _e( 'Add a Widget' ); ?>
				</h3>
			</div>
			<div id="available-widgets-filter">
				<label class="screen-reader-text" for="widgets-search"><?php _e( 'Search Widgets' ); ?></label>
				<input type="text" id="widgets-search" placeholder="<?php esc_attr_e( 'Search widgets&hellip;' ) ?>" aria-describedby="widgets-search-desc" />
				<div class="search-icon" aria-hidden="true"></div>
				<button type="button" class="clear-results"><span class="screen-reader-text"><?php _e( 'Clear Results' ); ?></span></button>
				<p class="screen-reader-text" id="widgets-search-desc"><?php _e( 'The search results will be updated as you type.' ); ?></p>
			</div>
			<div id="available-widgets-list">
			<?php foreach ( $this->get_available_widgets() as $available_widget ): ?>
				<div id="widget-tpl-<?php echo esc_attr( $available_widget['id'] ) ?>" data-widget-id="<?php echo esc_attr( $available_widget['id'] ) ?>" class="widget-tpl <?php echo esc_attr( $available_widget['id'] ) ?>" tabindex="0">
					<?php echo $available_widget['control_tpl']; ?>
				</div>
			<?php endforeach; ?>
			<p class="no-widgets-found-message"><?php _e( 'No widgets found.' ); ?></p>
			</div><!-- #available-widgets-list -->
		</div><!-- #available-widgets -->
		</div><!-- #widgets-left -->
		<?php
	}

	/**
	 * Calls admin_print_footer_scripts and admin_print_scripts hooks to
	 * allow custom scripts from plugins.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function print_footer_scripts() {
		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_print_footer_scripts-widgets.php' );

		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_print_footer_scripts' );

		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_footer-widgets.php' );
	}

	/**
	 * Retrieves common arguments to supply when constructing a Customizer setting.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $id        Widget setting ID.
	 * @param array  $overrides Array of setting overrides.
	 * @return array Possibly modified setting arguments.
	 */
	public function get_setting_args( $id, $overrides = array() ) {
		$args = array(
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'default'    => array(),
		);

		if ( preg_match( $this->setting_id_patterns['sidebar_widgets'], $id, $matches ) ) {
			$args['sanitize_callback'] = array( $this, 'sanitize_sidebar_widgets' );
			$args['sanitize_js_callback'] = array( $this, 'sanitize_sidebar_widgets_js_instance' );
			$args['transport'] = current_theme_supports( 'customize-selective-refresh-widgets' ) ? 'postMessage' : 'refresh';
		} elseif ( preg_match( $this->setting_id_patterns['widget_instance'], $id, $matches ) ) {
			$args['sanitize_callback'] = array( $this, 'sanitize_widget_instance' );
			$args['sanitize_js_callback'] = array( $this, 'sanitize_widget_js_instance' );
			$args['transport'] = $this->is_widget_selective_refreshable( $matches['id_base'] ) ? 'postMessage' : 'refresh';
		}

		$args = array_merge( $args, $overrides );

		/**
		 * Filters the common arguments supplied when constructing a Customizer setting.
		 *
		 * @since 3.9.0
		 *
		 * @see WP_Customize_Setting
		 *
		 * @param array  $args Array of Customizer setting arguments.
		 * @param string $id   Widget setting ID.
		 */
		return apply_filters( 'widget_customizer_setting_args', $args, $id );
	}

	/**
	 * Ensures sidebar widget arrays only ever contain widget IDS.
	 *
	 * Used as the 'sanitize_callback' for each $sidebars_widgets setting.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $widget_ids Array of widget IDs.
	 * @return array Array of sanitized widget IDs.
	 */
	public function sanitize_sidebar_widgets( $widget_ids ) {
		$widget_ids = array_map( 'strval', (array) $widget_ids );
		$sanitized_widget_ids = array();
		foreach ( $widget_ids as $widget_id ) {
			$sanitized_widget_ids[] = preg_replace( '/[^a-z0-9_\-]/', '', $widget_id );
		}
		return $sanitized_widget_ids;
	}

	/**
	 * Builds up an index of all available widgets for use in Backbone models.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global array $wp_registered_widgets
	 * @global array $wp_registered_widget_controls
	 * @staticvar array $available_widgets
	 *
	 * @see wp_list_widgets()
	 *
	 * @return array List of available widgets.
	 */
	public function get_available_widgets() {
		static $available_widgets = array();
		if ( ! empty( $available_widgets ) ) {
			return $available_widgets;
		}

		global $wp_registered_widgets, $wp_registered_widget_controls;
		require_once ABSPATH . '/wp-admin/includes/widgets.php'; // for next_widget_id_number()

		$sort = $wp_registered_widgets;
		usort( $sort, array( $this, '_sort_name_callback' ) );
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
				'widget_id'   => $widget['id'],
				'widget_name' => $widget['name'],
				'_display'    => 'template',
			);

			$is_disabled     = false;
			$is_multi_widget = ( isset( $wp_registered_widget_controls[$widget['id']]['id_base'] ) && isset( $widget['params'][0]['number'] ) );
			if ( $is_multi_widget ) {
				$id_base            = $wp_registered_widget_controls[$widget['id']]['id_base'];
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
			$control_tpl = $this->get_widget_control( $list_widget_controls_args );

			// The properties here are mapped to the Backbone Widget model.
			$available_widget = array_merge( $available_widget, array(
				'temp_id'      => isset( $args['_temp_id'] ) ? $args['_temp_id'] : null,
				'is_multi'     => $is_multi_widget,
				'control_tpl'  => $control_tpl,
				'multi_number' => ( $args['_add'] === 'multi' ) ? $args['_multi_num'] : false,
				'is_disabled'  => $is_disabled,
				'id_base'      => $id_base,
				'transport'    => $this->is_widget_selective_refreshable( $id_base ) ? 'postMessage' : 'refresh',
				'width'        => $wp_registered_widget_controls[$widget['id']]['width'],
				'height'       => $wp_registered_widget_controls[$widget['id']]['height'],
				'is_wide'      => $this->is_wide_widget( $widget['id'] ),
			) );

			$available_widgets[] = $available_widget;
		}

		return $available_widgets;
	}

	/**
	 * Naturally orders available widgets by name.
	 *
	 * @since 3.9.0
	 * @access protected
	 *
	 * @param array $widget_a The first widget to compare.
	 * @param array $widget_b The second widget to compare.
	 * @return int Reorder position for the current widget comparison.
	 */
	protected function _sort_name_callback( $widget_a, $widget_b ) {
		return strnatcasecmp( $widget_a['name'], $widget_b['name'] );
	}

	/**
	 * Retrieves the widget control markup.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $args Widget control arguments.
	 * @return string Widget control form HTML markup.
	 */
	public function get_widget_control( $args ) {
		$args[0]['before_form'] = '<div class="form">';
		$args[0]['after_form'] = '</div><!-- .form -->';
		$args[0]['before_widget_content'] = '<div class="widget-content">';
		$args[0]['after_widget_content'] = '</div><!-- .widget-content -->';
		ob_start();
		call_user_func_array( 'wp_widget_control', $args );
		$control_tpl = ob_get_clean();
		return $control_tpl;
	}

	/**
	 * Retrieves the widget control markup parts.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $args Widget control arguments.
	 * @return array {
	 *     @type string $control Markup for widget control wrapping form.
	 *     @type string $content The contents of the widget form itself.
	 * }
	 */
	public function get_widget_control_parts( $args ) {
		$args[0]['before_widget_content'] = '<div class="widget-content">';
		$args[0]['after_widget_content'] = '</div><!-- .widget-content -->';
		$control_markup = $this->get_widget_control( $args );

		$content_start_pos = strpos( $control_markup, $args[0]['before_widget_content'] );
		$content_end_pos = strrpos( $control_markup, $args[0]['after_widget_content'] );

		$control = substr( $control_markup, 0, $content_start_pos + strlen( $args[0]['before_widget_content'] ) );
		$control .= substr( $control_markup, $content_end_pos );
		$content = trim( substr(
			$control_markup,
			$content_start_pos + strlen( $args[0]['before_widget_content'] ),
			$content_end_pos - $content_start_pos - strlen( $args[0]['before_widget_content'] )
		) );

		return compact( 'control', 'content' );
	}

	/**
	 * Adds hooks for the Customizer preview.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function customize_preview_init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'customize_preview_enqueue' ) );
		add_action( 'wp_print_styles',    array( $this, 'print_preview_css' ), 1 );
		add_action( 'wp_footer',          array( $this, 'export_preview_data' ), 20 );
	}

	/**
	 * Refreshes the nonce for widget updates.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @param  array $nonces Array of nonces.
	 * @return array $nonces Array of nonces.
	 */
	public function refresh_nonces( $nonces ) {
		$nonces['update-widget'] = wp_create_nonce( 'update-widget' );
		return $nonces;
	}

	/**
	 * When previewing, ensures the proper previewing widgets are used.
	 *
	 * Because wp_get_sidebars_widgets() gets called early at {@see 'init' } (via
	 * wp_convert_widget_settings()) and can set global variable `$_wp_sidebars_widgets`
	 * to the value of `get_option( 'sidebars_widgets' )` before the Customizer preview
	 * filter is added, it has to be reset after the filter has been added.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $sidebars_widgets List of widgets for the current sidebar.
	 * @return array
	 */
	public function preview_sidebars_widgets( $sidebars_widgets ) {
		$sidebars_widgets = get_option( 'sidebars_widgets', array() );

		unset( $sidebars_widgets['array_version'] );
		return $sidebars_widgets;
	}

	/**
	 * Enqueues scripts for the Customizer preview.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function customize_preview_enqueue() {
		wp_enqueue_script( 'customize-preview-widgets' );
	}

	/**
	 * Inserts default style for highlighted widget at early point so theme
	 * stylesheet can override.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function print_preview_css() {
		?>
		<style>
		.widget-customizer-highlighted-widget {
			outline: none;
			-webkit-box-shadow: 0 0 2px rgba(30,140,190,0.8);
			box-shadow: 0 0 2px rgba(30,140,190,0.8);
			position: relative;
			z-index: 1;
		}
		</style>
		<?php
	}

	/**
	 * Communicates the sidebars that appeared on the page at the very end of the page,
	 * and at the very end of the wp_footer,
	 *
	 * @since 3.9.0
	 * @access public
     *
	 * @global array $wp_registered_sidebars
	 * @global array $wp_registered_widgets
	 */
	public function export_preview_data() {
		global $wp_registered_sidebars, $wp_registered_widgets;

		$switched_locale = switch_to_locale( get_user_locale() );
		$l10n = array(
			'widgetTooltip'  => __( 'Shift-click to edit this widget.' ),
		);
		if ( $switched_locale ) {
			restore_previous_locale();
		}

		// Prepare Customizer settings to pass to JavaScript.
		$settings = array(
			'renderedSidebars'   => array_fill_keys( array_unique( $this->rendered_sidebars ), true ),
			'renderedWidgets'    => array_fill_keys( array_keys( $this->rendered_widgets ), true ),
			'registeredSidebars' => array_values( $wp_registered_sidebars ),
			'registeredWidgets'  => $wp_registered_widgets,
			'l10n'               => $l10n,
			'selectiveRefreshableWidgets' => $this->get_selective_refreshable_widgets(),
		);
		foreach ( $settings['registeredWidgets'] as &$registered_widget ) {
			unset( $registered_widget['callback'] ); // may not be JSON-serializeable
		}

		?>
		<script type="text/javascript">
			var _wpWidgetCustomizerPreviewSettings = <?php echo wp_json_encode( $settings ); ?>;
		</script>
		<?php
	}

	/**
	 * Tracks the widgets that were rendered.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $widget Rendered widget to tally.
	 */
	public function tally_rendered_widgets( $widget ) {
		$this->rendered_widgets[ $widget['id'] ] = true;
	}

	/**
	 * Determine if a widget is rendered on the page.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $widget_id Widget ID to check.
	 * @return bool Whether the widget is rendered.
	 */
	public function is_widget_rendered( $widget_id ) {
		return in_array( $widget_id, $this->rendered_widgets );
	}

	/**
	 * Determines if a sidebar is rendered on the page.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $sidebar_id Sidebar ID to check.
	 * @return bool Whether the sidebar is rendered.
	 */
	public function is_sidebar_rendered( $sidebar_id ) {
		return in_array( $sidebar_id, $this->rendered_sidebars );
	}

	/**
	 * Tallies the sidebars rendered via is_active_sidebar().
	 *
	 * Keep track of the times that is_active_sidebar() is called in the template,
	 * and assume that this means that the sidebar would be rendered on the template
	 * if there were widgets populating it.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param bool   $is_active  Whether the sidebar is active.
	 * @param string $sidebar_id Sidebar ID.
	 * @return bool Whether the sidebar is active.
	 */
	public function tally_sidebars_via_is_active_sidebar_calls( $is_active, $sidebar_id ) {
		if ( is_registered_sidebar( $sidebar_id ) ) {
			$this->rendered_sidebars[] = $sidebar_id;
		}
		/*
		 * We may need to force this to true, and also force-true the value
		 * for 'dynamic_sidebar_has_widgets' if we want to ensure that there
		 * is an area to drop widgets into, if the sidebar is empty.
		 */
		return $is_active;
	}

	/**
	 * Tallies the sidebars rendered via dynamic_sidebar().
	 *
	 * Keep track of the times that dynamic_sidebar() is called in the template,
	 * and assume this means the sidebar would be rendered on the template if
	 * there were widgets populating it.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param bool   $has_widgets Whether the current sidebar has widgets.
	 * @param string $sidebar_id  Sidebar ID.
	 * @return bool Whether the current sidebar has widgets.
	 */
	public function tally_sidebars_via_dynamic_sidebar_calls( $has_widgets, $sidebar_id ) {
		if ( is_registered_sidebar( $sidebar_id ) ) {
			$this->rendered_sidebars[] = $sidebar_id;
		}

		/*
		 * We may need to force this to true, and also force-true the value
		 * for 'is_active_sidebar' if we want to ensure there is an area to
		 * drop widgets into, if the sidebar is empty.
		 */
		return $has_widgets;
	}

	/**
	 * Retrieves MAC for a serialized widget instance string.
	 *
	 * Allows values posted back from JS to be rejected if any tampering of the
	 * data has occurred.
	 *
	 * @since 3.9.0
	 * @access protected
	 *
	 * @param string $serialized_instance Widget instance.
	 * @return string MAC for serialized widget instance.
	 */
	protected function get_instance_hash_key( $serialized_instance ) {
		return wp_hash( $serialized_instance );
	}

	/**
	 * Sanitizes a widget instance.
	 *
	 * Unserialize the JS-instance for storing in the options. It's important that this filter
	 * only get applied to an instance *once*.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $value Widget instance to sanitize.
	 * @return array|void Sanitized widget instance.
	 */
	public function sanitize_widget_instance( $value ) {
		if ( $value === array() ) {
			return $value;
		}

		if ( empty( $value['is_widget_customizer_js_value'] )
			|| empty( $value['instance_hash_key'] )
			|| empty( $value['encoded_serialized_instance'] ) )
		{
			return;
		}

		$decoded = base64_decode( $value['encoded_serialized_instance'], true );
		if ( false === $decoded ) {
			return;
		}

		if ( ! hash_equals( $this->get_instance_hash_key( $decoded ), $value['instance_hash_key'] ) ) {
			return;
		}

		$instance = unserialize( $decoded );
		if ( false === $instance ) {
			return;
		}

		return $instance;
	}

	/**
	 * Converts a widget instance into JSON-representable format.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $value Widget instance to convert to JSON.
	 * @return array JSON-converted widget instance.
	 */
	public function sanitize_widget_js_instance( $value ) {
		if ( empty( $value['is_widget_customizer_js_value'] ) ) {
			$serialized = serialize( $value );

			$value = array(
				'encoded_serialized_instance'   => base64_encode( $serialized ),
				'title'                         => empty( $value['title'] ) ? '' : $value['title'],
				'is_widget_customizer_js_value' => true,
				'instance_hash_key'             => $this->get_instance_hash_key( $serialized ),
			);
		}
		return $value;
	}

	/**
	 * Strips out widget IDs for widgets which are no longer registered.
	 *
	 * One example where this might happen is when a plugin orphans a widget
	 * in a sidebar upon deactivation.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global array $wp_registered_widgets
	 *
	 * @param array $widget_ids List of widget IDs.
	 * @return array Parsed list of widget IDs.
	 */
	public function sanitize_sidebar_widgets_js_instance( $widget_ids ) {
		global $wp_registered_widgets;
		$widget_ids = array_values( array_intersect( $widget_ids, array_keys( $wp_registered_widgets ) ) );
		return $widget_ids;
	}

	/**
	 * Finds and invokes the widget update and control callbacks.
	 *
	 * Requires that `$_POST` be populated with the instance data.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @global array $wp_registered_widget_updates
	 * @global array $wp_registered_widget_controls
	 *
	 * @param  string $widget_id Widget ID.
	 * @return WP_Error|array Array containing the updated widget information.
	 *                        A WP_Error object, otherwise.
	 */
	public function call_widget_update( $widget_id ) {
		global $wp_registered_widget_updates, $wp_registered_widget_controls;

		$setting_id = $this->get_setting_id( $widget_id );

		/*
		 * Make sure that other setting changes have previewed since this widget
		 * may depend on them (e.g. Menus being present for Custom Menu widget).
		 */
		if ( ! did_action( 'customize_preview_init' ) ) {
			foreach ( $this->manager->settings() as $setting ) {
				if ( $setting->id !== $setting_id ) {
					$setting->preview();
				}
			}
		}

		$this->start_capturing_option_updates();
		$parsed_id   = $this->parse_widget_id( $widget_id );
		$option_name = 'widget_' . $parsed_id['id_base'];

		/*
		 * If a previously-sanitized instance is provided, populate the input vars
		 * with its values so that the widget update callback will read this instance
		 */
		$added_input_vars = array();
		if ( ! empty( $_POST['sanitized_widget_setting'] ) ) {
			$sanitized_widget_setting = json_decode( $this->get_post_value( 'sanitized_widget_setting' ), true );
			if ( false === $sanitized_widget_setting ) {
				$this->stop_capturing_option_updates();
				return new WP_Error( 'widget_setting_malformed' );
			}

			$instance = $this->sanitize_widget_instance( $sanitized_widget_setting );
			if ( is_null( $instance ) ) {
				$this->stop_capturing_option_updates();
				return new WP_Error( 'widget_setting_unsanitized' );
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

		// Invoke the widget update callback.
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
			unset( $_POST[ $key ] );
			unset( $_REQUEST[ $key ] );
		}

		// Make sure the expected option was updated.
		if ( 0 !== $this->count_captured_options() ) {
			if ( $this->count_captured_options() > 1 ) {
				$this->stop_capturing_option_updates();
				return new WP_Error( 'widget_setting_too_many_options' );
			}

			$updated_option_name = key( $this->get_captured_options() );
			if ( $updated_option_name !== $option_name ) {
				$this->stop_capturing_option_updates();
				return new WP_Error( 'widget_setting_unexpected_option' );
			}
		}

		// Obtain the widget instance.
		$option = $this->get_captured_option( $option_name );
		if ( null !== $parsed_id['number'] ) {
			$instance = $option[ $parsed_id['number'] ];
		} else {
			$instance = $option;
		}

		/*
		 * Override the incoming $_POST['customized'] for a newly-created widget's
		 * setting with the new $instance so that the preview filter currently
		 * in place from WP_Customize_Setting::preview() will use this value
		 * instead of the default widget instance value (an empty array).
		 */
		$this->manager->set_post_value( $setting_id, $this->sanitize_widget_js_instance( $instance ) );

		// Obtain the widget control with the updated instance in place.
		ob_start();
		$form = $wp_registered_widget_controls[ $widget_id ];
		if ( $form ) {
			call_user_func_array( $form['callback'], $form['params'] );
		}
		$form = ob_get_clean();

		$this->stop_capturing_option_updates();

		return compact( 'instance', 'form' );
	}

	/**
	 * Updates widget settings asynchronously.
	 *
	 * Allows the Customizer to update a widget using its form, but return the new
	 * instance info via Ajax instead of saving it to the options table.
	 *
	 * Most code here copied from wp_ajax_save_widget().
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @see wp_ajax_save_widget()
	 */
	public function wp_ajax_update_widget() {

		if ( ! is_user_logged_in() ) {
			wp_die( 0 );
		}

		check_ajax_referer( 'update-widget', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( -1 );
		}

		if ( empty( $_POST['widget-id'] ) ) {
			wp_send_json_error( 'missing_widget-id' );
		}

		/** This action is documented in wp-admin/includes/ajax-actions.php */
		do_action( 'load-widgets.php' );

		/** This action is documented in wp-admin/includes/ajax-actions.php */
		do_action( 'widgets.php' );

		/** This action is documented in wp-admin/widgets.php */
		do_action( 'sidebar_admin_setup' );

		$widget_id = $this->get_post_value( 'widget-id' );
		$parsed_id = $this->parse_widget_id( $widget_id );
		$id_base = $parsed_id['id_base'];

		$is_updating_widget_template = (
			isset( $_POST[ 'widget-' . $id_base ] )
			&&
			is_array( $_POST[ 'widget-' . $id_base ] )
			&&
			preg_match( '/__i__|%i%/', key( $_POST[ 'widget-' . $id_base ] ) )
		);
		if ( $is_updating_widget_template ) {
			wp_send_json_error( 'template_widget_not_updatable' );
		}

		$updated_widget = $this->call_widget_update( $widget_id ); // => {instance,form}
		if ( is_wp_error( $updated_widget ) ) {
			wp_send_json_error( $updated_widget->get_error_code() );
		}

		$form = $updated_widget['form'];
		$instance = $this->sanitize_widget_js_instance( $updated_widget['instance'] );

		wp_send_json_success( compact( 'form', 'instance' ) );
	}

	/*
	 * Selective Refresh Methods
	 */

	/**
	 * Filters arguments for dynamic widget partials.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param array|false $partial_args Partial arguments.
	 * @param string      $partial_id   Partial ID.
	 * @return array (Maybe) modified partial arguments.
	 */
	public function customize_dynamic_partial_args( $partial_args, $partial_id ) {
		if ( ! current_theme_supports( 'customize-selective-refresh-widgets' ) ) {
			return $partial_args;
		}

		if ( preg_match( '/^widget\[(?P<widget_id>.+)\]$/', $partial_id, $matches ) ) {
			if ( false === $partial_args ) {
				$partial_args = array();
			}
			$partial_args = array_merge(
				$partial_args,
				array(
					'type'                => 'widget',
					'render_callback'     => array( $this, 'render_widget_partial' ),
					'container_inclusive' => true,
					'settings'            => array( $this->get_setting_id( $matches['widget_id'] ) ),
					'capability'          => 'edit_theme_options',
				)
			);
		}

		return $partial_args;
	}

	/**
	 * Adds hooks for selective refresh.
	 *
	 * @since 4.5.0
	 * @access public
	 */
	public function selective_refresh_init() {
		if ( ! current_theme_supports( 'customize-selective-refresh-widgets' ) ) {
			return;
		}
		add_filter( 'dynamic_sidebar_params', array( $this, 'filter_dynamic_sidebar_params' ) );
		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_wp_kses_allowed_data_attributes' ) );
		add_action( 'dynamic_sidebar_before', array( $this, 'start_dynamic_sidebar' ) );
		add_action( 'dynamic_sidebar_after', array( $this, 'end_dynamic_sidebar' ) );
	}

	/**
	 * Inject selective refresh data attributes into widget container elements.
	 *
	 * @param array $params {
	 *     Dynamic sidebar params.
	 *
	 *     @type array $args        Sidebar args.
	 *     @type array $widget_args Widget args.
	 * }
	 * @see WP_Customize_Nav_Menus_Partial_Refresh::filter_wp_nav_menu_args()
	 *
	 * @return array Params.
	 */
	public function filter_dynamic_sidebar_params( $params ) {
		$sidebar_args = array_merge(
			array(
				'before_widget' => '',
				'after_widget' => '',
			),
			$params[0]
		);

		// Skip widgets not in a registered sidebar or ones which lack a proper wrapper element to attach the data-* attributes to.
		$matches = array();
		$is_valid = (
			isset( $sidebar_args['id'] )
			&&
			is_registered_sidebar( $sidebar_args['id'] )
			&&
			( isset( $this->current_dynamic_sidebar_id_stack[0] ) && $this->current_dynamic_sidebar_id_stack[0] === $sidebar_args['id'] )
			&&
			preg_match( '#^<(?P<tag_name>\w+)#', $sidebar_args['before_widget'], $matches )
		);
		if ( ! $is_valid ) {
			return $params;
		}
		$this->before_widget_tags_seen[ $matches['tag_name'] ] = true;

		$context = array(
			'sidebar_id' => $sidebar_args['id'],
		);
		if ( isset( $this->context_sidebar_instance_number ) ) {
			$context['sidebar_instance_number'] = $this->context_sidebar_instance_number;
		} else if ( isset( $sidebar_args['id'] ) && isset( $this->sidebar_instance_count[ $sidebar_args['id'] ] ) ) {
			$context['sidebar_instance_number'] = $this->sidebar_instance_count[ $sidebar_args['id'] ];
		}

		$attributes = sprintf( ' data-customize-partial-id="%s"', esc_attr( 'widget[' . $sidebar_args['widget_id'] . ']' ) );
		$attributes .= ' data-customize-partial-type="widget"';
		$attributes .= sprintf( ' data-customize-partial-placement-context="%s"', esc_attr( wp_json_encode( $context ) ) );
		$attributes .= sprintf( ' data-customize-widget-id="%s"', esc_attr( $sidebar_args['widget_id'] ) );
		$sidebar_args['before_widget'] = preg_replace( '#^(<\w+)#', '$1 ' . $attributes, $sidebar_args['before_widget'] );

		$params[0] = $sidebar_args;
		return $params;
	}

	/**
	 * List of the tag names seen for before_widget strings.
	 *
	 * This is used in the {@see 'filter_wp_kses_allowed_html'} filter to ensure that the
	 * data-* attributes can be whitelisted.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var array
	 */
	protected $before_widget_tags_seen = array();

	/**
	 * Ensures the HTML data-* attributes for selective refresh are allowed by kses.
	 *
	 * This is needed in case the `$before_widget` is run through wp_kses() when printed.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param array $allowed_html Allowed HTML.
	 * @return array (Maybe) modified allowed HTML.
	 */
	public function filter_wp_kses_allowed_data_attributes( $allowed_html ) {
		foreach ( array_keys( $this->before_widget_tags_seen ) as $tag_name ) {
			if ( ! isset( $allowed_html[ $tag_name ] ) ) {
				$allowed_html[ $tag_name ] = array();
			}
			$allowed_html[ $tag_name ] = array_merge(
				$allowed_html[ $tag_name ],
				array_fill_keys( array(
					'data-customize-partial-id',
					'data-customize-partial-type',
					'data-customize-partial-placement-context',
					'data-customize-partial-widget-id',
					'data-customize-partial-options',
				), true )
			);
		}
		return $allowed_html;
	}

	/**
	 * Keep track of the number of times that dynamic_sidebar() was called for a given sidebar index.
	 *
	 * This helps facilitate the uncommon scenario where a single sidebar is rendered multiple times on a template.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var array
	 */
	protected $sidebar_instance_count = array();

	/**
	 * The current request's sidebar_instance_number context.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var int
	 */
	protected $context_sidebar_instance_number;

	/**
	 * Current sidebar ID being rendered.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var array
	 */
	protected $current_dynamic_sidebar_id_stack = array();

	/**
	 * Begins keeping track of the current sidebar being rendered.
	 *
	 * Insert marker before widgets are rendered in a dynamic sidebar.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param int|string $index Index, name, or ID of the dynamic sidebar.
	 */
	public function start_dynamic_sidebar( $index ) {
		array_unshift( $this->current_dynamic_sidebar_id_stack, $index );
		if ( ! isset( $this->sidebar_instance_count[ $index ] ) ) {
			$this->sidebar_instance_count[ $index ] = 0;
		}
		$this->sidebar_instance_count[ $index ] += 1;
		if ( ! $this->manager->selective_refresh->is_render_partials_request() ) {
			printf( "\n<!--dynamic_sidebar_before:%s:%d-->\n", esc_html( $index ), intval( $this->sidebar_instance_count[ $index ] ) );
		}
	}

	/**
	 * Finishes keeping track of the current sidebar being rendered.
	 *
	 * Inserts a marker after widgets are rendered in a dynamic sidebar.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param int|string $index Index, name, or ID of the dynamic sidebar.
	 */
	public function end_dynamic_sidebar( $index ) {
		array_shift( $this->current_dynamic_sidebar_id_stack );
		if ( ! $this->manager->selective_refresh->is_render_partials_request() ) {
			printf( "\n<!--dynamic_sidebar_after:%s:%d-->\n", esc_html( $index ), intval( $this->sidebar_instance_count[ $index ] ) );
		}
	}

	/**
	 * Current sidebar being rendered.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var string
	 */
	protected $rendering_widget_id;

	/**
	 * Current widget being rendered.
	 *
	 * @since 4.5.0
	 * @access protected
	 * @var string
	 */
	protected $rendering_sidebar_id;

	/**
	 * Filters sidebars_widgets to ensure the currently-rendered widget is the only widget in the current sidebar.
	 *
	 * @since 4.5.0
	 * @access protected
	 *
	 * @param array $sidebars_widgets Sidebars widgets.
	 * @return array Filtered sidebars widgets.
	 */
	public function filter_sidebars_widgets_for_rendering_widget( $sidebars_widgets ) {
		$sidebars_widgets[ $this->rendering_sidebar_id ] = array( $this->rendering_widget_id );
		return $sidebars_widgets;
	}

	/**
	 * Renders a specific widget using the supplied sidebar arguments.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @see dynamic_sidebar()
	 *
	 * @param WP_Customize_Partial $partial Partial.
	 * @param array                $context {
	 *     Sidebar args supplied as container context.
	 *
	 *     @type string $sidebar_id              ID for sidebar for widget to render into.
	 *     @type int    $sidebar_instance_number Disambiguating instance number.
	 * }
	 * @return string|false
	 */
	public function render_widget_partial( $partial, $context ) {
		$id_data   = $partial->id_data();
		$widget_id = array_shift( $id_data['keys'] );

		if ( ! is_array( $context )
			|| empty( $context['sidebar_id'] )
			|| ! is_registered_sidebar( $context['sidebar_id'] )
		) {
			return false;
		}

		$this->rendering_sidebar_id = $context['sidebar_id'];

		if ( isset( $context['sidebar_instance_number'] ) ) {
			$this->context_sidebar_instance_number = intval( $context['sidebar_instance_number'] );
		}

		// Filter sidebars_widgets so that only the queried widget is in the sidebar.
		$this->rendering_widget_id = $widget_id;

		$filter_callback = array( $this, 'filter_sidebars_widgets_for_rendering_widget' );
		add_filter( 'sidebars_widgets', $filter_callback, 1000 );

		// Render the widget.
		ob_start();
		dynamic_sidebar( $this->rendering_sidebar_id = $context['sidebar_id'] );
		$container = ob_get_clean();

		// Reset variables for next partial render.
		remove_filter( 'sidebars_widgets', $filter_callback, 1000 );

		$this->context_sidebar_instance_number = null;
		$this->rendering_sidebar_id = null;
		$this->rendering_widget_id = null;

		return $container;
	}

	//
	// Option Update Capturing
	//

	/**
	 * List of captured widget option updates.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @var array $_captured_options Values updated while option capture is happening.
	 */
	protected $_captured_options = array();

	/**
	 * Whether option capture is currently happening.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @var bool $_is_current Whether option capture is currently happening or not.
	 */
	protected $_is_capturing_option_updates = false;

	/**
	 * Determines whether the captured option update should be ignored.
	 *
	 * @since 3.9.0
	 * @access protected
	 *
	 * @param string $option_name Option name.
	 * @return bool Whether the option capture is ignored.
	 */
	protected function is_option_capture_ignored( $option_name ) {
		return ( 0 === strpos( $option_name, '_transient_' ) );
	}

	/**
	 * Retrieves captured widget option updates.
	 *
	 * @since 3.9.0
	 * @access protected
	 *
	 * @return array Array of captured options.
	 */
	protected function get_captured_options() {
		return $this->_captured_options;
	}

	/**
	 * Retrieves the option that was captured from being saved.
	 *
	 * @since 4.2.0
	 * @access protected
	 *
	 * @param string $option_name Option name.
	 * @param mixed  $default     Optional. Default value to return if the option does not exist. Default false.
	 * @return mixed Value set for the option.
	 */
	protected function get_captured_option( $option_name, $default = false ) {
		if ( array_key_exists( $option_name, $this->_captured_options ) ) {
			$value = $this->_captured_options[ $option_name ];
		} else {
			$value = $default;
		}
		return $value;
	}

	/**
	 * Retrieves the number of captured widget option updates.
	 *
	 * @since 3.9.0
	 * @access protected
	 *
	 * @return int Number of updated options.
	 */
	protected function count_captured_options() {
		return count( $this->_captured_options );
	}

	/**
	 * Begins keeping track of changes to widget options, caching new values.
	 *
	 * @since 3.9.0
	 * @access protected
	 */
	protected function start_capturing_option_updates() {
		if ( $this->_is_capturing_option_updates ) {
			return;
		}

		$this->_is_capturing_option_updates = true;

		add_filter( 'pre_update_option', array( $this, 'capture_filter_pre_update_option' ), 10, 3 );
	}

	/**
	 * Pre-filters captured option values before updating.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param mixed  $new_value   The new option value.
	 * @param string $option_name Name of the option.
	 * @param mixed  $old_value   The old option value.
	 * @return mixed Filtered option value.
	 */
	public function capture_filter_pre_update_option( $new_value, $option_name, $old_value ) {
		if ( $this->is_option_capture_ignored( $option_name ) ) {
			return;
		}

		if ( ! isset( $this->_captured_options[ $option_name ] ) ) {
			add_filter( "pre_option_{$option_name}", array( $this, 'capture_filter_pre_get_option' ) );
		}

		$this->_captured_options[ $option_name ] = $new_value;

		return $old_value;
	}

	/**
	 * Pre-filters captured option values before retrieving.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param mixed $value Value to return instead of the option value.
	 * @return mixed Filtered option value.
	 */
	public function capture_filter_pre_get_option( $value ) {
		$option_name = preg_replace( '/^pre_option_/', '', current_filter() );

		if ( isset( $this->_captured_options[ $option_name ] ) ) {
			$value = $this->_captured_options[ $option_name ];

			/** This filter is documented in wp-includes/option.php */
			$value = apply_filters( 'option_' . $option_name, $value );
		}

		return $value;
	}

	/**
	 * Undoes any changes to the options since options capture began.
	 *
	 * @since 3.9.0
	 * @access protected
	 */
	protected function stop_capturing_option_updates() {
		if ( ! $this->_is_capturing_option_updates ) {
			return;
		}

		remove_filter( 'pre_update_option', array( $this, 'capture_filter_pre_update_option' ), 10 );

		foreach ( array_keys( $this->_captured_options ) as $option_name ) {
			remove_filter( "pre_option_{$option_name}", array( $this, 'capture_filter_pre_get_option' ) );
		}

		$this->_captured_options = array();
		$this->_is_capturing_option_updates = false;
	}

	/**
	 * {@internal Missing Summary}
	 *
	 * See the {@see 'customize_dynamic_setting_args'} filter.
	 *
	 * @since 3.9.0
	 * @deprecated 4.2.0 Deprecated in favor of the {@see 'customize_dynamic_setting_args'} filter.
	 */
	public function setup_widget_addition_previews() {
		_deprecated_function( __METHOD__, '4.2.0' );
	}

	/**
	 * {@internal Missing Summary}
	 *
	 * See the {@see 'customize_dynamic_setting_args'} filter.
	 *
	 * @since 3.9.0
	 * @deprecated 4.2.0 Deprecated in favor of the {@see 'customize_dynamic_setting_args'} filter.
	 */
	public function prepreview_added_sidebars_widgets() {
		_deprecated_function( __METHOD__, '4.2.0' );
	}

	/**
	 * {@internal Missing Summary}
	 *
	 * See the {@see 'customize_dynamic_setting_args'} filter.
	 *
	 * @since 3.9.0
	 * @deprecated 4.2.0 Deprecated in favor of the {@see 'customize_dynamic_setting_args'} filter.
	 */
	public function prepreview_added_widget_instance() {
		_deprecated_function( __METHOD__, '4.2.0' );
	}

	/**
	 * {@internal Missing Summary}
	 *
	 * See the {@see 'customize_dynamic_setting_args'} filter.
	 *
	 * @since 3.9.0
	 * @deprecated 4.2.0 Deprecated in favor of the {@see 'customize_dynamic_setting_args'} filter.
	 */
	public function remove_prepreview_filters() {
		_deprecated_function( __METHOD__, '4.2.0' );
	}
}
