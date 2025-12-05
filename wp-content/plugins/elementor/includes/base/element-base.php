<?php
namespace Elementor;

use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor element base.
 *
 * An abstract class to register new Elementor elements. It extended the
 * `Controls_Stack` class to inherit its properties.
 *
 * This abstract class must be extended in order to register new elements.
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Element_Base extends Controls_Stack {

	/**
	 * Child elements.
	 *
	 * Holds all the child elements of the element.
	 *
	 * @access private
	 *
	 * @var Element_Base[]
	 */
	private $children;

	/**
	 * Element default arguments.
	 *
	 * Holds all the default arguments of the element. Used to store additional
	 * data. For example WordPress widgets use this to store widget names.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $default_args = [];

	/**
	 * Is type instance.
	 *
	 * Whether the element is an instance of that type or not.
	 *
	 * @access private
	 *
	 * @var bool
	 */
	private $is_type_instance = true;

	/**
	 * Depended scripts.
	 *
	 * Holds all the element depended scripts to enqueue.
	 *
	 * @since 1.9.0
	 * @access private
	 *
	 * @var array
	 */
	private $depended_scripts = [];

	/**
	 * Depended styles.
	 *
	 * Holds all the element depended styles to enqueue.
	 *
	 * @since 1.9.0
	 * @access private
	 *
	 * @var array
	 */
	private $depended_styles = [];

	/**
	 * Add script depends.
	 *
	 * Register new script to enqueue by the handler.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @param string $handler Depend script handler.
	 */
	public function add_script_depends( $handler ) {
		$this->depended_scripts[] = $handler;
	}

	/**
	 * Add style depends.
	 *
	 * Register new style to enqueue by the handler.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @param string $handler Depend style handler.
	 */
	public function add_style_depends( $handler ) {
		$this->depended_styles[] = $handler;
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		return $this->depended_scripts;
	}

	public function get_global_scripts() {
		return [ 'elementor-frontend' ];
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the scripts defined as element dependencies and enqueues
	 * them. Use `get_script_depends()` method to add custom script dependencies.
	 *
	 * @since 1.3.0
	 * @access public
	 */
	final public function enqueue_scripts() {
		$deprecated_scripts = [
			// Insert here when you have a deprecated script.
		];

		foreach ( $this->get_script_depends() as $script ) {
			if ( isset( $deprecated_scripts[ $script ] ) ) {
				Utils::handle_deprecation( $script, $deprecated_scripts[ $script ]['version'], $deprecated_scripts[ $script ]['replacement'] );
			}

			wp_enqueue_script( $script );
		}

		foreach ( $this->get_global_scripts() as $script ) {
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return $this->depended_styles;
	}

	/**
	 * Enqueue styles.
	 *
	 * Registers all the styles defined as element dependencies and enqueues
	 * them. Use `get_style_depends()` method to add custom style dependencies.
	 *
	 * @since 1.9.0
	 * @access public
	 */
	final public function enqueue_styles() {
		foreach ( $this->get_style_depends() as $style ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * @since 1.0.0
	 * @deprecated 2.6.0
	 * @access public
	 * @static
	 */
	final public static function add_edit_tool() {}

	/**
	 * @since 2.2.0
	 * @deprecated 2.6.0
	 * @access public
	 * @static
	 */
	final public static function is_edit_buttons_enabled() {
		return get_option( 'elementor_edit_buttons' );
	}

	/**
	 * Get default child type.
	 *
	 * Retrieve the default child type based on element data.
	 *
	 * Note that not all elements support children.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @abstract
	 *
	 * @param array $element_data Element data.
	 *
	 * @return Element_Base
	 */
	abstract protected function _get_default_child_type( array $element_data );

	/**
	 * Before element rendering.
	 *
	 * Used to add stuff before the element.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function before_render() {}

	/**
	 * After element rendering.
	 *
	 * Used to add stuff after the element.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function after_render() {}

	/**
	 * Get element title.
	 *
	 * Retrieve the element title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Element title.
	 */
	public function get_title() {
		return '';
	}

	/**
	 * Get element icon.
	 *
	 * Retrieve the element icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Element icon.
	 */
	public function get_icon() {
		return 'eicon-columns';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/widget-' . $this->get_name();
	}

	public function get_custom_help_url() {
		return '';
	}

	/**
	 * Whether the reload preview is required.
	 *
	 * Used to determine whether the reload preview is required or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the reload preview is required.
	 */
	public function is_reload_preview_required() {
		return false;
	}

	/**
	 * @since 2.3.1
	 * @access protected
	 */
	protected function should_print_empty() {
		return true;
	}

	/**
	 * Whether the element returns dynamic content.
	 *
	 * Set to determine whether to cache the element output or not.
	 *
	 * @since 3.22.0
	 * @access protected
	 *
	 * @return bool Whether to cache the element output.
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get child elements.
	 *
	 * Retrieve all the child elements of this element.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Element_Base[] Child elements.
	 */
	public function get_children() {
		if ( null === $this->children ) {
			$this->init_children();
		}

		return $this->children;
	}

	/**
	 * Get default arguments.
	 *
	 * Retrieve the element default arguments. Used to return all the default
	 * arguments or a specific default argument, if one is set.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $item Optional. Default is null.
	 *
	 * @return array Default argument(s).
	 */
	public function get_default_args( $item = null ) {
		return self::get_items( $this->default_args, $item );
	}

	/**
	 * Get panel presets.
	 *
	 * Used for displaying the widget in the panel multiple times, but with different defaults values,
	 * icon, title etc.
	 *
	 * @since 3.16.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_panel_presets() {
		return [];
	}

	/**
	 * Add new child element.
	 *
	 * Register new child element to allow hierarchy.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $child_data Child element data.
	 * @param array $child_args Child element arguments.
	 *
	 * @return Element_Base|false Child element instance, or false if failed.
	 */
	public function add_child( array $child_data, array $child_args = [] ) {
		if ( null === $this->children ) {
			$this->init_children();
		}

		$child_type = $this->get_child_type( $child_data );

		if ( ! $child_type ) {
			return false;
		}

		$child = Plugin::$instance->elements_manager->create_element_instance( $child_data, $child_args, $child_type );

		if ( $child ) {
			$this->children[] = $child;
		}

		return $child;
	}

	/**
	 * Add link render attributes.
	 *
	 * Used to add link tag attributes to a specific HTML element.
	 *
	 * The HTML link tag is represented by the element parameter. The `url_control` parameter
	 * needs to be an array of link settings in the same format they are set by Elementor's URL control.
	 *
	 * Example usage:
	 *
	 * `$this->add_link_attributes( 'button', $settings['link'] );`
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array|string $element   The HTML element.
	 * @param array        $url_control      Array of link settings.
	 * @param bool         $overwrite         Optional. Whether to overwrite existing
	 *                                        attribute. Default is false, not to overwrite.
	 *
	 * @return Element_Base Current instance of the element.
	 */
	public function add_link_attributes( $element, array $url_control, $overwrite = false ) {
		$attributes = [];

		if ( ! empty( $url_control['url'] ) ) {
			$allowed_protocols = array_merge( wp_allowed_protocols(), [ 'skype', 'viber' ] );

			$attributes['href'] = esc_url( $url_control['url'], $allowed_protocols );
		}

		if ( ! empty( $url_control['is_external'] ) ) {
			$attributes['target'] = '_blank';
		}

		if ( ! empty( $url_control['nofollow'] ) ) {
			$attributes['rel'] = 'nofollow';
		}

		if ( ! empty( $url_control['custom_attributes'] ) ) {
			// Custom URL attributes should come as a string of comma-delimited key|value pairs.
			$attributes = array_merge( $attributes, Utils::parse_custom_attributes( $url_control['custom_attributes'] ) );
		}

		if ( $attributes ) {
			$this->add_render_attribute( $element, $attributes, null, $overwrite );
		}

		return $this;
	}

	/**
	 * Print element.
	 *
	 * Used to generate the element final HTML on the frontend and the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function print_element() {
		$element_type = $this->get_type();

		if ( $this->should_render_shortcode() ) {
			$unique_id = apply_filters( 'elementor/element_cache/unique_id', '' );

			echo '[elementor-element k="' . esc_attr( $unique_id ) . '" data="' . esc_attr( base64_encode( wp_json_encode( $this->get_raw_data() ) ) ) . '"]';
			return;
		}

		/**
		 * Before frontend element render.
		 *
		 * Fires before Elementor element is rendered in the frontend.
		 *
		 * @since 2.2.0
		 *
		 * @param Element_Base $this The element.
		 */
		do_action( 'elementor/frontend/before_render', $this );

		/**
		 * Before frontend element render.
		 *
		 * Fires before Elementor element is rendered in the frontend.
		 *
		 * The dynamic portion of the hook name, `$element_type`, refers to the element type.
		 *
		 * @since 1.0.0
		 *
		 * @param Element_Base $this The element.
		 */
		do_action( "elementor/frontend/{$element_type}/before_render", $this );

		ob_start();

		if ( $this->has_own_method( '_print_content', self::class ) ) {
			Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( '_print_content', '3.1.0', __CLASS__ . '::print_content()' );

			$this->_print_content();
		} else {
			$this->print_content();
		}

		$content = ob_get_clean();

		$should_render = ( ! empty( $content ) || $this->should_print_empty() );

		/**
		 * Should the element be rendered for frontend
		 *
		 * Filters if the element should be rendered on frontend.
		 *
		 * @since 2.3.3
		 *
		 * @param bool true The element.
		 * @param Element_Base $this The element.
		 */
		$should_render = apply_filters( "elementor/frontend/{$element_type}/should_render", $should_render, $this );

		if ( $should_render ) {
			if ( $this->has_own_method( '_add_render_attributes', self::class ) ) {
				Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( '_add_render_attributes', '3.1.0', __CLASS__ . '::add_render_attributes()' );

				$this->_add_render_attributes();
			} else {
				$this->add_render_attributes();
			}

			$this->before_render();
			// PHPCS - The content has already been escaped by the `render` method.
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$this->after_render();

			// TODO: Remove this in the future
			// Since version 3.24.0 page scripts/styles are handled by `page_assets`.
			$this->enqueue_scripts();
			$this->enqueue_styles();
		}

		/**
		 * After frontend element render.
		 *
		 * Fires after Elementor element is rendered in the frontend.
		 *
		 * The dynamic portion of the hook name, `$element_type`, refers to the element type.
		 *
		 * @since 1.0.0
		 *
		 * @param Element_Base $this The element.
		 */
		do_action( "elementor/frontend/{$element_type}/after_render", $this );

		/**
		 * After frontend element render.
		 *
		 * Fires after Elementor element is rendered in the frontend.
		 *
		 * @since 2.3.0
		 *
		 * @param Element_Base $this The element.
		 */
		do_action( 'elementor/frontend/after_render', $this );
	}

	protected function should_render_shortcode() {
		$should_render_shortcode = apply_filters( 'elementor/element/should_render_shortcode', false );

		if ( ! $should_render_shortcode ) {
			return false;
		}

		$raw_data = $this->get_raw_data();

		if ( ! empty( $raw_data['settings']['_element_cache'] ) ) {
			return 'yes' === $raw_data['settings']['_element_cache'];
		}

		if ( $this->is_dynamic_content() ) {
			return true;
		}

		$is_dynamic_content = apply_filters( 'elementor/element/is_dynamic_content', false, $raw_data, $this );

		$has_dynamic_tag = $this->has_element_dynamic_tag( $raw_data['settings'] );

		if ( $is_dynamic_content || $has_dynamic_tag ) {
			return true;
		}

		return false;
	}

	private function has_element_dynamic_tag( $element_settings ): bool {
		if ( is_array( $element_settings ) ) {
			if ( ! empty( $element_settings['__dynamic__'] ) ) {
				return true;
			}

			foreach ( $element_settings as $value ) {
				$has_dynamic = $this->has_element_dynamic_tag( $value );

				if ( $has_dynamic ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get the element raw data.
	 *
	 * Retrieve the raw element data, including the id, type, settings, child
	 * elements and whether it is an inner element.
	 *
	 * The data with the HTML used always to display the data, but the Elementor
	 * editor uses the raw data without the HTML in order not to render the data
	 * again.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $with_html_content Optional. Whether to return the data with
	 *                                HTML content or without. Used for caching.
	 *                                Default is false, without HTML.
	 *
	 * @return array Element raw data.
	 */
	public function get_raw_data( $with_html_content = false ) {
		$data = $this->get_data();

		$elements = [];

		foreach ( $this->get_children() as $child ) {
			$elements[] = $child->get_raw_data( $with_html_content );
		}

		$raw_data = [
			'id' => $this->get_id(),
			'elType' => $data['elType'],
			'settings' => $data['settings'],
			'elements' => $elements,
			'isInner' => $data['isInner'],
		];

		if ( ! empty( $data['isLocked'] ) ) {
			$raw_data['isLocked'] = $data['isLocked'];
		}

		return $raw_data;
	}

	public function get_data_for_save() {
		$data = $this->get_raw_data();

		$elements = [];

		foreach ( $this->get_children() as $child ) {
			$elements[] = $child->get_data_for_save();
		}

		if ( ! empty( $elements ) ) {
			$data['elements'] = $elements;
		}

		if ( ! empty( $data['settings'] ) ) {
			$data['settings'] = $this->on_save( $data['settings'] );
		}

		return $data;
	}

	/**
	 * Get unique selector.
	 *
	 * Retrieve the unique selector of the element. Used to set a unique HTML
	 * class for each HTML element. This way Elementor can set custom styles for
	 * each element.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Unique selector.
	 */
	public function get_unique_selector() {
		return '.elementor-element-' . $this->get_id();
	}

	/**
	 * Is type instance.
	 *
	 * Used to determine whether the element is an instance of that type or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the element is an instance of that type.
	 */
	public function is_type_instance() {
		return $this->is_type_instance;
	}

	/**
	 * On import update dynamic content (e.g. post and term IDs).
	 *
	 * @since 3.8.0
	 *
	 * @param array      $config   The config of the passed element.
	 * @param array      $data     The data that requires updating/replacement when imported.
	 * @param array|null $controls The available controls.
	 *
	 * @return array Element data.
	 */
	public static function on_import_update_dynamic_content( array $config, array $data, $controls = null ): array {
		$tags_manager = Plugin::$instance->dynamic_tags;

		if ( empty( $config['settings'][ $tags_manager::DYNAMIC_SETTING_KEY ] ) ) {
			return $config;
		}

		foreach ( $config['settings'][ $tags_manager::DYNAMIC_SETTING_KEY ] as $dynamic_name => $dynamic_value ) {
			$tag_config = $tags_manager->tag_text_to_tag_data( $dynamic_value );
			$tag_instance = $tags_manager->create_tag( $tag_config['id'], $tag_config['name'], $tag_config['settings'] );

			if ( is_null( $tag_instance ) ) {
				continue;
			}

			if ( $tag_instance->has_own_method( 'on_import_replace_dynamic_content' ) ) {
				// TODO: Remove this check in the future.
				$tag_config = $tag_instance->on_import_replace_dynamic_content( $tag_config, $data['post_ids'] );
			} else {
				$tag_config = $tag_instance->on_import_update_dynamic_content( $tag_config, $data, $tag_instance->get_controls() );
			}

			$config['settings'][ $tags_manager::DYNAMIC_SETTING_KEY ][ $dynamic_name ] = $tags_manager->tag_data_to_tag_text( $tag_config['id'], $tag_config['name'], $tag_config['settings'] );
		}

		return $config;
	}

	/**
	 * Add render attributes.
	 *
	 * Used to add attributes to the current element wrapper HTML tag.
	 *
	 * @since 1.3.0
	 * @access protected
	 * @deprecated 3.1.0 Use `add_render_attribute()` method instead.
	 */
	protected function _add_render_attributes() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.1.0', 'add_render_attributes()' );

		return $this->add_render_attributes();
	}

	/**
	 * Add render attributes.
	 *
	 * Used to add attributes to the current element wrapper HTML tag.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function add_render_attributes() {
		$id = $this->get_id();

		$settings = $this->get_settings_for_display();
		$frontend_settings = $this->get_frontend_settings();
		$controls = $this->get_controls();

		$this->add_render_attribute( '_wrapper', [
			'class' => [
				'elementor-element',
				'elementor-element-' . $id,
			],
			'data-id' => $id,
			'data-element_type' => $this->get_type(),
		] );

		$class_settings = [];

		foreach ( $settings as $setting_key => $setting ) {
			if ( isset( $controls[ $setting_key ]['prefix_class'] ) ) {
				if ( isset( $controls[ $setting_key ]['classes_dictionary'][ $setting ] ) ) {
					$value = $controls[ $setting_key ]['classes_dictionary'][ $setting ];
				} else {
					$value = $setting;
				}

				$class_settings[ $setting_key ] = $value;
			}
		}

		foreach ( $class_settings as $setting_key => $setting ) {
			if ( empty( $setting ) && '0' !== $setting ) {
				continue;
			}

			$this->add_render_attribute( '_wrapper', 'class', $controls[ $setting_key ]['prefix_class'] . $setting );
		}

		$_animation = ! empty( $settings['_animation'] );
		$animation = ! empty( $settings['animation'] );
		$has_animation = ( $_animation && 'none' !== $settings['_animation'] ) || ( $animation && 'none' !== $settings['animation'] );

		if ( $has_animation ) {
			$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

			if ( ! $is_static_render_mode ) {
				// Hide the element until the animation begins.
				$this->add_render_attribute( '_wrapper', 'class', 'elementor-invisible' );
			}
		}

		if ( ! empty( $settings['_element_id'] ) ) {
			$this->add_render_attribute( '_wrapper', 'id', trim( $settings['_element_id'] ) );
		}

		if ( $frontend_settings ) {
			$this->add_render_attribute( '_wrapper', 'data-settings', wp_json_encode( $frontend_settings ) );
		}

		/**
		 * After element attribute rendered.
		 *
		 * Fires after the attributes of the element HTML tag are rendered.
		 *
		 * @since 2.3.0
		 *
		 * @param Element_Base $this The element.
		 */
		do_action( 'elementor/element/after_add_attributes', $this );
	}

	/**
	 * Register the Transform controls in the advanced tab of the element.
	 *
	 * Previously registered under the Widget_Common class, but registered a more fundamental level now to enable access from other widgets.
	 *
	 * @param string $element_selector
	 * @param string $transform_selector_class
	 * @return void
	 * @since 3.9.0
	 * @access protected
	 */
	protected function register_transform_section( $element_selector = '', $transform_selector_class = ' > .elementor-widget-container' ) {
		$default_unit_values_deg = [];
		$default_unit_values_ms = [];

		// Set the default unit sizes for all active breakpoints.
		foreach ( Breakpoints_Manager::get_default_config() as $breakpoint_name => $breakpoint_config ) {
			$default_unit_values_deg[ $breakpoint_name ] = [
				'default' => [
					'unit' => 'deg',
				],
			];

			$default_unit_values_ms[ $breakpoint_name ] = [
				'default' => [
					'unit' => 'ms',
				],
			];
		}

		$this->start_controls_section(
			'_section_transform',
			[
				'label' => esc_html__( 'Transform', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->start_controls_tabs( '_tabs_positioning' );

		$transform_prefix_class = 'e-';
		$transform_return_value = 'transform';
		$transform_css_modifier = '';

		if ( 'con' === $element_selector ) {
			$transform_selector_class = '.e-' . $element_selector;
			$transform_css_modifier = $element_selector . '-';
		}

		foreach ( [ '', '_hover' ] as $tab ) {
			$state = '_hover' === $tab ? ':hover' : '';

			$this->start_controls_tab(
				"_tab_positioning{$tab}",
				[
					'label' => '' === $tab ? esc_html__( 'Normal', 'elementor' ) : esc_html__( 'Hover', 'elementor' ),
				]
			);

			$this->add_control(
				"_transform_rotate_popover{$tab}",
				[
					'label' => esc_html__( 'Rotate', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				"_transform_rotateZ_effect{$tab}",
				[
					'label' => esc_html__( 'Rotate', 'elementor' ) . ' (deg)',
					'type' => Controls_Manager::SLIDER,
					'device_args' => $default_unit_values_deg,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-rotateZ: {{SIZE}}deg',
					],
					'condition' => [
						"_transform_rotate_popover{$tab}!" => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				"_transform_rotate_3d{$tab}",
				[
					'label' => esc_html__( '3D Rotate', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'On', 'elementor' ),
					'label_off' => esc_html__( 'Off', 'elementor' ),
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-rotateX: 1{{UNIT}};  --e-' . $transform_css_modifier . 'transform-perspective: 20px;',
					],
					'condition' => [
						"_transform_rotate_popover{$tab}!" => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_rotateX_effect{$tab}",
				[
					'label' => esc_html__( 'Rotate X', 'elementor' ) . ' (deg)',
					'type' => Controls_Manager::SLIDER,
					'device_args' => $default_unit_values_deg,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_rotate_3d{$tab}!" => '',
						"_transform_rotate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-rotateX: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_rotateY_effect{$tab}",
				[
					'label' => esc_html__( 'Rotate Y', 'elementor' ) . ' (deg)',
					'type' => Controls_Manager::SLIDER,
					'device_args' => $default_unit_values_deg,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_rotate_3d{$tab}!" => '',
						"_transform_rotate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-rotateY: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_perspective_effect{$tab}",
				[
					'label' => esc_html__( 'Perspective', 'elementor' ) . ' (px)',
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1000,
						],
					],
					'condition' => [
						"_transform_rotate_popover{$tab}!" => '',
						"_transform_rotate_3d{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-perspective: {{SIZE}}px',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_translate_popover{$tab}",
				[
					'label' => esc_html__( 'Offset', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				"_transform_translateX_effect{$tab}",
				[
					'label' => esc_html__( 'Offset X', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'range' => [
						'%' => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'condition' => [
						"_transform_translate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-translateX: {{SIZE}}{{UNIT}};',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_translateY_effect{$tab}",
				[
					'label' => esc_html__( 'Offset Y', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
					'range' => [
						'%' => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'condition' => [
						"_transform_translate_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-translateY: {{SIZE}}{{UNIT}};',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_scale_popover{$tab}",
				[
					'label' => esc_html__( 'Scale', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_control(
				"_transform_keep_proportions{$tab}",
				[
					'label' => esc_html__( 'Keep Proportions', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'On', 'elementor' ),
					'label_off' => esc_html__( 'Off', 'elementor' ),
					'default' => 'yes',
				]
			);

			$this->add_responsive_control(
				"_transform_scale_effect{$tab}",
				[
					'label' => esc_html__( 'Scale', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 2,
							'step' => 0.1,
						],
					],
					'condition' => [
						"_transform_scale_popover{$tab}!" => '',
						"_transform_keep_proportions{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-scale: {{SIZE}};',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_scaleX_effect{$tab}",
				[
					'label' => esc_html__( 'Scale X', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 2,
							'step' => 0.1,
						],
					],
					'condition' => [
						"_transform_scale_popover{$tab}!" => '',
						"_transform_keep_proportions{$tab}" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-scaleX: {{SIZE}};',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_scaleY_effect{$tab}",
				[
					'label' => esc_html__( 'Scale Y', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 2,
							'step' => 0.1,
						],
					],
					'condition' => [
						"_transform_scale_popover{$tab}!" => '',
						"_transform_keep_proportions{$tab}" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-scaleY: {{SIZE}};',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_skew_popover{$tab}",
				[
					'label' => esc_html__( 'Skew', 'elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'prefix_class' => $transform_prefix_class,
					'return_value' => $transform_return_value,
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				"_transform_skewX_effect{$tab}",
				[
					'label' => esc_html__( 'Skew X', 'elementor' ) . ' (deg)',
					'type' => Controls_Manager::SLIDER,
					'device_args' => $default_unit_values_deg,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_skew_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-skewX: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				"_transform_skewY_effect{$tab}",
				[
					'label' => esc_html__( 'Skew Y', 'elementor' ) . ' (deg)',
					'type' => Controls_Manager::SLIDER,
					'device_args' => $default_unit_values_deg,
					'range' => [
						'px' => [
							'min' => -360,
							'max' => 360,
						],
					],
					'condition' => [
						"_transform_skew_popover{$tab}!" => '',
					],
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-skewY: {{SIZE}}deg;',
					],
					'frontend_available' => true,
				]
			);

			$this->end_popover();

			$this->add_control(
				"_transform_flipX_effect{$tab}",
				[
					'label' => esc_html__( 'Flip Horizontal', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'transform' => [
							'title' => esc_html__( 'Flip Horizontal', 'elementor' ),
							'icon' => 'eicon-flip eicon-tilted',
						],
					],
					'prefix_class' => $transform_prefix_class,
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-flipX: -1',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				"_transform_flipY_effect{$tab}",
				[
					'label' => esc_html__( 'Flip Vertical', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'transform' => [
							'title' => esc_html__( 'Flip Vertical', 'elementor' ),
							'icon' => 'eicon-flip',
						],
					],
					'prefix_class' => $transform_prefix_class,
					'selectors' => [
						"{{WRAPPER}}{$transform_selector_class}{$state}" => '--e-' . $transform_css_modifier . 'transform-flipY: -1',
					],
					'frontend_available' => true,
				]
			);

			if ( '_hover' === $tab ) {
				$this->add_control(
					'_transform_transition_hover',
					[
						'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'device_args' => $default_unit_values_ms,
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10000,
								'step' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}}' => '--e-' . $transform_css_modifier . 'transform-transition-duration: {{SIZE}}ms',
						],
					]
				);
			}

			${"transform_origin_conditions{$tab}"} = [
				[
					'name' => "_transform_scale_popover{$tab}",
					'operator' => '!=',
					'value' => '',
				],
				[
					'name' => "_transform_rotate_popover{$tab}",
					'operator' => '!=',
					'value' => '',
				],
				[
					'name' => "_transform_flipX_effect{$tab}",
					'operator' => '!=',
					'value' => '',
				],
				[
					'name' => "_transform_flipY_effect{$tab}",
					'operator' => '!=',
					'value' => '',
				],
			];

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$transform_origin_conditions = [
			'relation' => 'or',
			'terms' => array_merge( $transform_origin_conditions, $transform_origin_conditions_hover ),
		];

		// Will override motion effect transform-origin.
		$this->add_responsive_control(
			'motion_fx_transform_x_anchor_point',
			[
				'label' => esc_html__( 'X Anchor Point', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'conditions' => $transform_origin_conditions,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--e-' . $transform_css_modifier . 'transform-origin-x: {{VALUE}}',
				],
			]
		);

		// Will override motion effect transform-origin.
		$this->add_responsive_control(
			'motion_fx_transform_y_anchor_point',
			[
				'label' => esc_html__( 'Y Anchor Point', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'conditions' => $transform_origin_conditions,
				'selectors' => [
					'{{WRAPPER}}' => '--e-' . $transform_css_modifier . 'transform-origin-y: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add Hidden Device Controls
	 *
	 * Adds controls for hiding elements within certain devices' viewport widths. Adds a control for each active device.
	 *
	 * @since 3.4.0
	 * @access protected
	 */
	protected function add_hidden_device_controls() {
		// The 'Hide On X' controls are displayed from largest to smallest, while the method returns smallest to largest.
		$active_devices = Plugin::$instance->breakpoints->get_active_devices_list( [ 'reverse' => true ] );
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		foreach ( $active_devices as $breakpoint_key ) {
			$label = 'desktop' === $breakpoint_key ? esc_html__( 'Desktop', 'elementor' ) : $active_breakpoints[ $breakpoint_key ]->get_label();

			$this->add_control(
				'hide_' . $breakpoint_key,
				[
					'label' => sprintf(
						/* translators: %s: Device name. */
						esc_html__( 'Hide On %s', 'elementor' ),
						$label
					),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'prefix_class' => 'elementor-',
					'label_on' => esc_html__( 'Hide', 'elementor' ),
					'label_off' => esc_html__( 'Show', 'elementor' ),
					'return_value' => 'hidden-' . $breakpoint_key,
				]
			);
		}
	}

	/**
	 * Get default data.
	 *
	 * Retrieve the default element data. Used to reset the data on initialization.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Default data.
	 */
	protected function get_default_data() {
		$data = parent::get_default_data();

		return array_merge(
			$data, [
				'elements' => [],
				'isInner' => false,
			]
		);
	}

	/**
	 * Print element content.
	 *
	 * Output the element final HTML on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @deprecated 3.1.0 Use `print_content()` method instead.
	 */
	protected function _print_content() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.1.0', 'print_content()' );

		$this->print_content();
	}

	/**
	 * Print element content.
	 *
	 * Output the element final HTML on the frontend.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function print_content() {
		foreach ( $this->get_children() as $child ) {
			$child->print_element();
		}
	}

	/**
	 * Get initial config.
	 *
	 * Retrieve the current element initial configuration.
	 *
	 * Adds more configuration on top of the controls list and the tabs assigned
	 * to the control. This method also adds element name, type, icon and more.
	 *
	 * @since 2.9.0
	 * @access protected
	 *
	 * @return array The initial config.
	 */
	protected function get_initial_config() {
		$config = [
			'name' => $this->get_name(),
			'elType' => $this->get_type(),
			'title' => $this->get_title(),
			'icon' => $this->get_icon(),
			'reload_preview' => $this->is_reload_preview_required(),
		];

		if ( preg_match( '/^' . __NAMESPACE__ . '(Pro)?\\\\/', get_called_class() ) ) {
			$config['help_url'] = $this->get_help_url();
		} else {
			$config['help_url'] = $this->get_custom_help_url();
		}

		if ( ! $this->is_editable() ) {
			$config['editable'] = false;
		}

		return $config;
	}

	/**
	 * A Base method for sanitizing the settings before save.
	 * This method is meant to be overridden by the element.
	 *
	 * @param array $settings
	 * @return array
	 */
	protected function on_save( array $settings ) {
		return $settings;
	}

	/**
	 * Get child type.
	 *
	 * Retrieve the element child type based on element data.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param array $element_data Element ID.
	 *
	 * @return Element_Base|false Child type or false if type not found.
	 */
	private function get_child_type( $element_data ) {
		$child_type = $this->_get_default_child_type( $element_data );

		// If it's not a valid widget ( like a deactivated plugin ).
		if ( ! $child_type ) {
			return false;
		}

		/**
		 * Element child type.
		 *
		 * Filters the child type of the element.
		 *
		 * @since 1.0.0
		 *
		 * @param Element_Base $child_type   The child element.
		 * @param array        $element_data The original element ID.
		 * @param Element_Base $this         The original element.
		 */
		$child_type = apply_filters( 'elementor/element/get_child_type', $child_type, $element_data, $this );

		return $child_type;
	}

	/**
	 * Initialize children.
	 *
	 * Initializing the element child elements.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function init_children() {
		$this->children = [];

		$children_data = $this->get_data( 'elements' );

		if ( ! $children_data ) {
			return;
		}

		foreach ( $children_data as $child_data ) {
			if ( ! $child_data ) {
				continue;
			}

			$this->add_child( $child_data );
		}
	}

	public function has_widget_inner_wrapper(): bool {
		return true;
	}

	/**
	 * Element base constructor.
	 *
	 * Initializing the element base class using `$data` and `$args`.
	 *
	 * The `$data` parameter is required for a normal instance because of the
	 * way Elementor renders data when initializing elements.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array      $data Optional. Element data. Default is an empty array.
	 * @param array|null $args Optional. Element default arguments. Default is null.
	 **/
	public function __construct( array $data = [], ?array $args = null ) {
		if ( $data ) {
			$this->is_type_instance = false;
		} elseif ( $args ) {
			$this->default_args = $args;
		}

		parent::__construct( $data );
	}
}
