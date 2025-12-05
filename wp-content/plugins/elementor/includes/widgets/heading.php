<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\ContentSanitizer\Interfaces\Sanitizable;
use Elementor\Core\Utils\Hints;
use Elementor\Core\Admin\Admin_Notices;
use Elementor\Modules\Promotions\Controls\Promotion_Control;

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Widget_Heading extends Widget_Base implements Sanitizable {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'heading';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Heading', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-t-letter';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
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
		return [ 'heading', 'title', 'text' ];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 3.24.0
	 * @access public
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'widget-heading' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Remove data attributes from the html.
	 *
	 * @param string $content Heading title.
	 * @return string
	 */
	public function sanitize( $content ): string {
		$allowed_tags = wp_kses_allowed_html( 'post' );
		$allowed_tags_for_heading = [];
		$non_allowed_tags = [ 'img' ];

		foreach ( $allowed_tags as $tag => $attributes ) {
			if ( in_array( $tag, $non_allowed_tags, true ) ) {
				continue;
			}

			$filtered_attributes = array_filter( $attributes, function( $attribute ) {
				return ! substr( $attribute, 0, 5 ) === 'data-';
			}, ARRAY_FILTER_USE_KEY );

			$allowed_tags_for_heading[ $tag ] = $filtered_attributes;
		}

		return wp_kses( $content, $allowed_tags_for_heading );
	}

	/**
	 * Get widget upsale data.
	 *
	 * Retrieve the widget promotion data.
	 *
	 * @since 3.18.0
	 * @access protected
	 *
	 * @return array Widget promotion data.
	 */
	protected function get_upsale_data() {
		return [
			'condition' => ! Utils::has_pro(),
			'image' => esc_url( ELEMENTOR_ASSETS_URL . 'images/go-pro.svg' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'description' => esc_html__( 'Create captivating headings that rotate with the Animated Headline Widget.', 'elementor' ),
			'upgrade_url' => esc_url( 'https://go.elementor.com/go-pro-heading-widget/' ),
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
		];
	}

	/**
	 * Register heading widget controls.
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
				'label' => esc_html__( 'Heading', 'elementor' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'ai' => [
					'type' => 'text',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your title', 'elementor' ),
				'default' => esc_html__( 'Add Your Heading Text Here', 'elementor' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'elementor' ),
					'small' => esc_html__( 'Small', 'elementor' ),
					'medium' => esc_html__( 'Medium', 'elementor' ),
					'large' => esc_html__( 'Large', 'elementor' ),
					'xl' => esc_html__( 'XL', 'elementor' ),
					'xxl' => esc_html__( 'XXL', 'elementor' ),
				],
				'default' => 'default',
				'condition' => [
					'size!' => 'default', // a workaround to hide the control, unless it's in use (not default).
				],
			]
		);

		$this->add_control(
			'header_size',
			[
				'label' => esc_html__( 'HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->maybe_add_ally_heading_hint();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Heading', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Normal', 'elementor' ),
					'multiply' => esc_html__( 'Multiply', 'elementor' ),
					'screen' => esc_html__( 'Screen', 'elementor' ),
					'overlay' => esc_html__( 'Overlay', 'elementor' ),
					'darken' => esc_html__( 'Darken', 'elementor' ),
					'lighten' => esc_html__( 'Lighten', 'elementor' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'elementor' ),
					'saturation' => esc_html__( 'Saturation', 'elementor' ),
					'color' => esc_html__( 'Color', 'elementor' ),
					'difference' => esc_html__( 'Difference', 'elementor' ),
					'exclusion' => esc_html__( 'Exclusion', 'elementor' ),
					'hue' => esc_html__( 'Hue', 'elementor' ),
					'luminosity' => esc_html__( 'Luminosity', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title' => 'mix-blend-mode: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'separator',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'title_colors' );

		$this->start_controls_tab(
			'title_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Link Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title a:hover, {{WRAPPER}} .elementor-heading-title a:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 's',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title a' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( '' === $settings['title'] ) {
			return;
		}

		$this->add_render_attribute( 'title', 'class', 'elementor-heading-title' );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-' . $settings['size'] );
		} else {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-default' );
		}

		$this->add_inline_editing_attributes( 'title' );

		$title = wp_kses_post( $settings['title'] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['link'] );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['header_size'] ), $this->get_render_attribute_string( 'title' ), $title );

		// PHPCS - the variable $title_html holds safe data.
		echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function maybe_add_ally_heading_hint() {
		$notice_id = 'ally_heading_notice';
		$plugin_slug = 'pojo-accessibility';
		if ( ! Hints::should_display_hint( $notice_id ) ) {
			return;
		}
		$notice_content = esc_html__( 'Make sure your page is structured with accessibility in mind. Ally helps detect and fix common issues across your site.', 'elementor' );

		$campaign_data = [
			'name' => 'elementor_ea11y_campaign',
			'campaign' => 'acc-scanner-plg-heading',
			'source' => 'editor-heading-widget',
			'medium' => 'editor',
		];

		$button_text = __( 'Install Plugin', 'elementor' );
		$action_url = Admin_Notices::add_plg_campaign_data( Hints::get_plugin_action_url( $plugin_slug ), $campaign_data );

		if ( Hints::is_plugin_installed( $plugin_slug ) && ! Hints::is_plugin_active( $plugin_slug ) ) {
			$button_text = __( 'Activate Plugin', 'elementor' );
		} elseif ( Hints::is_plugin_active( $plugin_slug ) && empty( get_option( 'ea11y_access_token' ) ) ) {
			$button_text = __( 'Connect to Ally', 'elementor' );
			$action_url = admin_url( 'admin.php?page=accessibility-settings' );
		}

		$this->add_control(
			$notice_id,
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => Hints::get_notice_template( [
					'display' => ! Hints::is_dismissed( $notice_id ),
					'heading' => esc_html__( 'Accessible structure matters', 'elementor' ),
					'type' => 'info',
					'content' => $notice_content,
					'icon' => true,
					'dismissible' => $notice_id,
					'button_text' => $button_text,
					'button_event' => $notice_id,
					'button_data' => [
						'action_url' => $action_url,
					],
				], true ),
			]
		);
	}

	/**
	 * Render heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		let title = elementor.helpers.sanitize( settings.title, { ALLOW_DATA_ATTR: false } );

		if ( '' !== settings.link?.url ) {
			title = '<a href="' + elementor.helpers.sanitizeUrl( settings.link?.url ) + '">' + title + '</a>';
		}

		view.addRenderAttribute( 'title', 'class', [ 'elementor-heading-title' ] );

		if ( '' !== settings.size ) {
			view.addRenderAttribute( 'title', 'class', [ 'elementor-size-' + settings.size ] );
		} else {
			view.addRenderAttribute( 'title', 'class', [ 'elementor-size-default' ] );
		}

		view.addInlineEditingAttributes( 'title' );

		var headerSizeTag = elementor.helpers.validateHTMLTag( settings.header_size ),
			title_html = '<' + headerSizeTag  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</' + headerSizeTag + '>';

		print( title_html );
		#>
		<?php
	}
}
