<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor HTML widget.
 *
 * Elementor widget that insert a custom HTML code into the page.
 */
class Widget_Read_More extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Read More widget name.
	 *
	 * @since 2.4.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'read-more';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Read More widget title.
	 *
	 * @since 2.4.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Read More', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Read More widget icon.
	 *
	 * @since 2.4.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-excerpt';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.4.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'read', 'more', 'tag', 'excerpt' ];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register HTML widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Read More', 'elementor' ),
			]
		);

		$default_link_text = esc_html__( 'Continue reading', 'elementor' );

		/**
		 * Read More widgets link text.
		 *
		 * Filters the link text in the "Read More" widget.
		 *
		 * This hook can be used to set different default text in the widget.
		 *
		 * @param string $default_link_text The link text in the "Read More" widget. Default is "Continue reading".
		 */
		$default_link_text = apply_filters( 'elementor/widgets/read_more/default_link_text', $default_link_text );

		$this->add_control(
			'theme_support',
			[
				'type' => Controls_Manager::ALERT,
				'alert_type' => 'warning',
				'content' => sprintf(
					/* translators: %s: The `the_content` function. */
					esc_html__( 'Note: This widget only affects themes that use `%s` in archive pages.', 'elementor' ),
					'the_content'
				),
			]
		);

		$this->add_control(
			'link_text',
			[
				'label' => esc_html__( 'Read More Text', 'elementor' ),
				'placeholder' => $default_link_text,
				'default' => $default_link_text,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Read More widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		printf( '<!--more %s-->', wp_kses_post( $this->get_settings_for_display( 'link_text' ) ) );
	}

	/**
	 * Render Read More widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<!--more {{ settings.link_text }}-->
		<?php
	}
}
