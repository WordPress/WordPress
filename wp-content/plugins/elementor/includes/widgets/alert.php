<?php
namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor alert widget.
 *
 * Elementor widget that displays a collapsible display of content in an toggle
 * style, allowing the user to open multiple items.
 *
 * @since 1.0.0
 */
class Widget_Alert extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve alert widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'alert';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve alert widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Alert', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve alert widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-alert';
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
		return [ 'alert', 'notice', 'message' ];
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
		return [ 'widget-alert' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register alert widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_alert',
			[
				'label' => esc_html__( 'Alert', 'elementor' ),
			]
		);

		$this->add_control(
			'alert_type',
			[
				'label' => esc_html__( 'Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'info',
				'options' => [
					'info' => esc_html__( 'Info', 'elementor' ),
					'success' => esc_html__( 'Success', 'elementor' ),
					'warning' => esc_html__( 'Warning', 'elementor' ),
					'danger' => esc_html__( 'Danger', 'elementor' ),
				],
				'prefix_class' => 'elementor-alert-',
			]
		);

		$this->add_control(
			'alert_title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your title', 'elementor' ),
				'default' => esc_html__( 'This is an Alert', 'elementor' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'alert_description',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your description', 'elementor' ),
				'default' => esc_html__( 'I am a description. Click the edit button to change this text.', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_dismiss',
			[
				'label' => esc_html__( 'Dismiss Icon', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'return_value' => 'show',
				'default' => 'show',
			]
		);

		$this->add_control(
			'dismiss_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'render_type' => 'template',
				'skin_settings' => [
					'inline' => [
						'none' => [
							'label' => 'Default',
							'icon' => 'eicon-close',
						],
						'icon' => [
							'icon' => 'eicon-star',
						],
					],
				],
				'recommended' => [
					'fa-regular' => [
						'times-circle',
					],
					'fa-solid' => [
						'times',
						'times-circle',
					],
				],
				'condition' => [
					'show_dismiss' => 'show',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_type',
			[
				'label' => esc_html__( 'Alert', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Side Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert' => 'border-inline-start-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_left-width',
			[
				'label' => esc_html__( 'Side Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-alert' => 'border-inline-start-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'alert_title',
				'selector' => '{{WRAPPER}} .elementor-alert-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'selector' => '{{WRAPPER}} .elementor-alert-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_description',
			[
				'label' => esc_html__( 'Description', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-alert-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'alert_description',
				'selector' => '{{WRAPPER}} .elementor-alert-description',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'description_shadow',
				'selector' => '{{WRAPPER}} .elementor-alert-description',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dismiss_icon',
			[
				'label' => esc_html__( 'Dismiss Icon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_dismiss' => 'show',
				],
			]
		);

		$this->add_responsive_control(
			'dismiss_icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--dismiss-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dismiss_icon_vertical_position',
			[
				'label' => esc_html__( 'Vertical Position', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--dismiss-icon-vertical-position: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dismiss_icon_horizontal_position',
			[
				'label' => esc_html__( 'Horizontal Position', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--dismiss-icon-horizontal-position: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'dismiss_icon_colors' );

		$this->start_controls_tab( 'dismiss_icon_normal_colors', [
			'label' => esc_html__( 'Normal', 'elementor' ),
		] );

		$this->add_control(
			'dismiss_icon_normal_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--dismiss-icon-normal-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'dismiss_icon_hover_colors', [
			'label' => esc_html__( 'Hover', 'elementor' ),
		] );

		$this->add_control(
			'dismiss_icon_hover_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--dismiss-icon-hover-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dismiss_icon_hover_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (s)',
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--dismiss-icon-hover-transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render alert widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( Utils::is_empty( $settings['alert_title'] ) && Utils::is_empty( $settings['alert_description'] ) ) {
			return;
		}

		$this->add_render_attribute( 'alert_wrapper', 'class', 'elementor-alert' );

		$this->add_render_attribute( 'alert_wrapper', 'role', 'alert' );

		$this->add_render_attribute( 'alert_title', 'class', 'elementor-alert-title' );

		$this->add_render_attribute( 'alert_description', 'class', 'elementor-alert-description' );

		$this->add_inline_editing_attributes( 'alert_title', 'none' );

		$this->add_inline_editing_attributes( 'alert_description' );
		?>
		<div <?php $this->print_render_attribute_string( 'alert_wrapper' ); ?>>

			<?php if ( ! Utils::is_empty( $settings['alert_title'] ) ) : ?>
			<span <?php $this->print_render_attribute_string( 'alert_title' ); ?>><?php echo wp_kses_post( $settings['alert_title'] ); ?></span>
			<?php endif; ?>

			<?php if ( ! Utils::is_empty( $settings['alert_description'] ) ) : ?>
			<span <?php $this->print_render_attribute_string( 'alert_description' ); ?>><?php echo wp_kses_post( $settings['alert_description'] ); ?></span>
			<?php endif; ?>

			<?php if ( 'show' === $settings['show_dismiss'] ) : ?>
			<button type="button" class="elementor-alert-dismiss" aria-label="<?php echo esc_attr__( 'Dismiss this alert.', 'elementor' ); ?>">
				<?php
				if ( ! empty( $settings['dismiss_icon']['value'] ) ) {
					Icons_Manager::render_icon( $settings['dismiss_icon'], [ 'aria-hidden' => 'true' ] );
				} else { ?>
					<span aria-hidden="true">&times;</span>
				<?php } ?>
			</button>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Render alert widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		if ( ! settings.alert_title && ! settings.alert_description ) {
			return;
		}

		view.addRenderAttribute( 'alert_wrapper', 'class', 'elementor-alert' );

		view.addRenderAttribute( 'alert_wrapper', 'role', 'alert' );

		view.addRenderAttribute( 'alert_title', 'class', 'elementor-alert-title' );

		view.addRenderAttribute( 'alert_description', 'class', 'elementor-alert-description' );

		view.addInlineEditingAttributes( 'alert_title', 'none' );

		view.addInlineEditingAttributes( 'alert_description' );

		var iconHTML = elementor.helpers.renderIcon( view, settings.dismiss_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			migrated = elementor.helpers.isIconMigrated( settings, 'dismiss_icon' );
		#>
		<div {{{ view.getRenderAttributeString( 'alert_wrapper' ) }}}>

			<# if ( settings.alert_title ) { #>
			<span {{{ view.getRenderAttributeString( 'alert_title' ) }}}>{{ settings.alert_title }}</span>
			<# } #>

			<# if ( settings.alert_description ) { #>
			<span {{{ view.getRenderAttributeString( 'alert_description' ) }}}>{{ settings.alert_description }}</span>
			<# } #>

			<# if ( 'show' === settings.show_dismiss ) { #>
			<button type="button" class="elementor-alert-dismiss" aria-label="<?php echo esc_attr__( 'Dismiss this alert.', 'elementor' ); ?>">
				<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
				{{{ iconHTML.value }}}
				<# } else { #>
					<span aria-hidden="true">&times;</span>
				<# } #>
			</button>
			<# } #>

		</div>
		<?php
	}
}
