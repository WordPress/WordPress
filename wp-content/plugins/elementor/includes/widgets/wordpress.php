<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor WordPress widget.
 *
 * Elementor widget that displays all the WordPress widgets.
 *
 * @since 1.0.0
 */
class Widget_WordPress extends Widget_Base {

	/**
	 * WordPress widget name.
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $_widget_name = null;

	/**
	 * WordPress widget instance.
	 *
	 * @access private
	 *
	 * @var \WP_Widget
	 */
	private $_widget_instance = null;

	public function hide_on_search() {
		return true;
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve WordPress widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wp-widget-' . $this->get_widget_instance()->id_base;
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve WordPress widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return $this->get_widget_instance()->name;
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the WordPress widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories. Returns either a WordPress category.
	 */
	public function get_categories() {
		return [ 'wordpress' ];
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve WordPress widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon. Returns either a WordPress icon.
	 */
	public function get_icon() {
		return 'eicon-wordpress';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'wordpress', 'widget' ];
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 3.26.0
	 * @access public
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'e-swiper' ];
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 3.27.0
	 * @access public
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends(): array {
		return [ 'swiper' ];
	}

	public function get_help_url() {
		return '';
	}

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the reload preview is required.
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve WordPress widget form.
	 *
	 * Returns the WordPress widget form, to be used in Elementor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget form.
	 */
	public function get_form() {
		$instance = $this->get_widget_instance();

		ob_start();
		echo '<div class="widget-inside media-widget-control"><div class="form wp-core-ui">';
		echo '<input type="hidden" class="id_base" value="' . esc_attr( $instance->id_base ) . '" />';
		echo '<input type="hidden" class="widget-id" value="widget-' . esc_attr( $this->get_id() ) . '" />';
		echo '<div class="widget-content">';
		$widget_data = $this->get_settings( 'wp' );
		$instance->form( $widget_data );
		do_action( 'in_widget_form', $instance, null, $widget_data );
		echo '</div></div></div>';
		return ob_get_clean();
	}

	/**
	 * Retrieve WordPress widget instance.
	 *
	 * Returns an instance of WordPress widget, to be used in Elementor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return \WP_Widget
	 */
	public function get_widget_instance() {
		if ( is_null( $this->_widget_instance ) ) {
			global $wp_widget_factory;

			if ( isset( $wp_widget_factory->widgets[ $this->_widget_name ] ) ) {
				$this->_widget_instance = $wp_widget_factory->widgets[ $this->_widget_name ];
				$this->_widget_instance->_set( 'REPLACE_TO_ID' );
			} elseif ( class_exists( $this->_widget_name ) ) {
				$this->_widget_instance = new $this->_widget_name();
				$this->_widget_instance->_set( 'REPLACE_TO_ID' );
			}
		}
		return $this->_widget_instance;
	}

	/**
	 * Retrieve WordPress widget parsed settings.
	 *
	 * Returns the WordPress widget settings, to be used in Elementor.
	 *
	 * @access protected
	 * @since 2.3.0
	 *
	 * @return array Parsed settings.
	 */
	protected function get_init_settings() {
		$settings = parent::get_init_settings();

		if ( ! empty( $settings['wp'] ) ) {
			$widget = $this->get_widget_instance();
			$instance = $widget->update( $settings['wp'], [] );
			/** This filter is documented in wp-includes/class-wp-widget.php */
			$settings['wp'] = apply_filters( 'widget_update_callback', $instance, $settings['wp'], [], $widget );
		}

		return $settings;
	}

	/**
	 * Register WordPress widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->add_control(
			'wp',
			[
				'label' => esc_html__( 'Form', 'elementor' ),
				'type' => Controls_Manager::WP_WIDGET,
				'widget' => $this->get_name(),
				'id_base' => $this->get_widget_instance()->id_base,
			]
		);
	}

	/**
	 * Render WordPress widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$default_widget_args = [
			'widget_id' => $this->get_name(),
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<h5>',
			'after_title' => '</h5>',
		];

		/**
		 * WordPress widget args.
		 *
		 * Filters the WordPress widget arguments when they are rendered in Elementor panel.
		 *
		 * @since 1.0.0
		 *
		 * @param array            $default_widget_args Default widget arguments.
		 * @param Widget_WordPress $this                The WordPress widget.
		 */
		$default_widget_args = apply_filters( 'elementor/widgets/wordpress/widget_args', $default_widget_args, $this );
		$is_gallery_widget = 'wp-widget-media_gallery' === $this->get_name();

		if ( $is_gallery_widget ) {
			add_filter( 'wp_get_attachment_link', [ $this, 'add_lightbox_data_to_image_link' ], 10, 2 );
		}

		$this->get_widget_instance()->widget( $default_widget_args, $this->get_settings( 'wp' ) );

		if ( $is_gallery_widget ) {
			remove_filter( 'wp_get_attachment_link', [ $this, 'add_lightbox_data_to_image_link' ] );
		}
	}

	/**
	 * Render WordPress widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {}

	/**
	 * WordPress widget constructor.
	 *
	 * Used to run WordPress widget constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Widget data. Default is an empty array.
	 * @param array $args Widget arguments. Default is null.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->_widget_name = $args['widget_name'];

		parent::__construct( $data, $args );
	}

	/**
	 * Render WordPress widget as plain content.
	 *
	 * Override the default render behavior, don't render widget content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $instance Widget instance. Default is empty array.
	 */
	public function render_plain_content( $instance = [] ) {}
}
