<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\Promotions\Controls\Promotion_Control;

/**
 * Elementor testimonial widget.
 *
 * Elementor widget that displays customer testimonials that show social proof.
 *
 * @since 1.0.0
 */
class Widget_Testimonial extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve testimonial widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'testimonial';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve testimonial widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Testimonial', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve testimonial widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial';
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
		return [ 'testimonial', 'blockquote' ];
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
		return [ 'widget-testimonial' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
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
			'description' => esc_html__( 'Use interesting masonry layouts and other overlay features with Elementor\'s Pro Gallery widget.', 'elementor' ),
			'upgrade_url' => esc_url( 'https://go.elementor.com/go-pro-testimonial-widget/' ),
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
		];
	}

	/**
	 * Register testimonial widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_testimonial',
			[
				'label' => esc_html__( 'Testimonial', 'elementor' ),
			]
		);

		$this->add_control(
			'testimonial_content',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => '10',
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
			]
		);

		$this->add_control(
			'testimonial_image',
			[
				'label' => esc_html__( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'testimonial_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `testimonial_image_size` and `testimonial_image_custom_dimension`.
				'default' => 'full',
			]
		);

		$this->add_control(
			'testimonial_name',
			[
				'label' => esc_html__( 'Name', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'default' => esc_html__( 'John Doe', 'elementor' ),
			]
		);

		$this->add_control(
			'testimonial_job',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'default' => esc_html__( 'Designer', 'elementor' ),
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
			]
		);

		$aside = is_rtl() ? 'right' : 'left';

		$this->add_control(
			'testimonial_image_position',
			[
				'label' => esc_html__( 'Image Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'aside',
				'options' => [
					'aside' => [
						'title' => esc_html__( 'Aside', 'elementor' ),
						'icon' => 'eicon-h-align-' . $aside,
					],
					'top' => [
						'title' => esc_html__( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
				],
				'toggle' => false,
				'condition' => [
					'testimonial_image[url]!' => '',
				],
				'separator' => 'before',
				'style_transfer' => true,
			]
		);

		$this->add_responsive_control(
			'testimonial_alignment',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
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
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-wrapper' => 'text-align: {{VALUE}}',
				],
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();

		// Content.
		$this->start_controls_section(
			'section_style_testimonial_content',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_content_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .elementor-testimonial-content',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'content_shadow',
				'selector' => '{{WRAPPER}} .elementor-testimonial-content',
			]
		);

		$this->end_controls_section();

		// Image.
		$this->start_controls_section(
			'section_style_testimonial_image',
			[
				'label' => esc_html__( 'Image', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'testimonial_image[url]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => esc_html__( 'Image Resolution', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-wrapper .elementor-testimonial-image img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .elementor-testimonial-wrapper .elementor-testimonial-image img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-wrapper .elementor-testimonial-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Name.
		$this->start_controls_section(
			'section_style_testimonial_name',
			[
				'label' => esc_html__( 'Name', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'name_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-testimonial-name',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_shadow',
				'selector' => '{{WRAPPER}} .elementor-testimonial-name',
			]
		);

		$this->end_controls_section();

		// Job.
		$this->start_controls_section(
			'section_style_testimonial_job',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'job_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-job' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'job_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .elementor-testimonial-job',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'job_shadow',
				'selector' => '{{WRAPPER}} .elementor-testimonial-job',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render testimonial widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$has_content = ! empty( $settings['testimonial_content'] );
		$has_image = ! empty( $settings['testimonial_image']['url'] );
		$has_name = ! empty( $settings['testimonial_name'] );
		$has_job = ! empty( $settings['testimonial_job'] );

		if ( ! $has_content && ! $has_image && ! $has_name && ! $has_job ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-testimonial-wrapper' );

		$this->add_render_attribute( 'meta', 'class', 'elementor-testimonial-meta' );

		if ( $settings['testimonial_image']['url'] ) {
			$this->add_render_attribute( 'meta', 'class', 'elementor-has-image' );
		}

		if ( $settings['testimonial_image_position'] ) {
			$this->add_render_attribute( 'meta', 'class', 'elementor-testimonial-image-position-' . $settings['testimonial_image_position'] );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( $has_content ) :
				$this->add_render_attribute( 'testimonial_content', 'class', 'elementor-testimonial-content' );
				$this->add_inline_editing_attributes( 'testimonial_content' );
				?>
				<div <?php $this->print_render_attribute_string( 'testimonial_content' ); ?>><?php echo wp_kses_post( $settings['testimonial_content'] ); ?></div>
			<?php endif; ?>

			<?php if ( $has_image || $has_name || $has_job ) : ?>
			<div <?php $this->print_render_attribute_string( 'meta' ); ?>>
				<div class="elementor-testimonial-meta-inner">
					<?php if ( $has_image ) : ?>
						<div class="elementor-testimonial-image">
							<?php
							$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'testimonial_image' );
							if ( ! empty( $settings['link']['url'] ) ) :
								$image_html = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $image_html . '</a>';
							endif;
							echo wp_kses_post( $image_html );
							?>
						</div>
					<?php endif; ?>

					<?php if ( $has_name || $has_job ) : ?>
					<div class="elementor-testimonial-details">
						<?php
						if ( $has_name ) :
							$this->add_render_attribute( 'testimonial_name', 'class', 'elementor-testimonial-name' );
							$this->add_inline_editing_attributes( 'testimonial_name', 'none' );

							if ( ! empty( $settings['link']['url'] ) ) :
								?>
								<a <?php $this->print_render_attribute_string( 'testimonial_name' ); ?> <?php $this->print_render_attribute_string( 'link' ); ?>><?php echo wp_kses_post( $settings['testimonial_name'] ); ?></a>
								<?php
							else :
								?>
								<div <?php $this->print_render_attribute_string( 'testimonial_name' ); ?>><?php echo wp_kses_post( $settings['testimonial_name'] ); ?></div>
								<?php
							endif;
						endif; ?>
						<?php
						if ( $has_job ) :
							$this->add_render_attribute( 'testimonial_job', 'class', 'elementor-testimonial-job' );

							$this->add_inline_editing_attributes( 'testimonial_job', 'none' );

							if ( ! empty( $settings['link']['url'] ) ) :
								?>
								<a <?php $this->print_render_attribute_string( 'testimonial_job' ); ?> <?php $this->print_render_attribute_string( 'link' ); ?>><?php echo wp_kses_post( $settings['testimonial_job'] ); ?></a>
								<?php
							else :
								?>
								<div <?php $this->print_render_attribute_string( 'testimonial_job' ); ?>><?php echo wp_kses_post( $settings['testimonial_job'] ); ?></div>
								<?php
							endif;
						endif; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render testimonial widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		if ( '' === settings.testimonial_content && '' === settings.testimonial_image.url && '' === settings.testimonial_name && '' === settings.testimonial_job ) {
			return;
		}

		var image = {
				id: settings.testimonial_image.id,
				url: settings.testimonial_image.url,
				size: settings.testimonial_image_size,
				dimension: settings.testimonial_image_custom_dimension,
				model: view.getEditModel()
			};
		var imageUrl = false, hasImage = '';

		if ( '' !== settings.testimonial_image.url ) {
			imageUrl = elementor.imagesManager.getImageUrl( image );
			hasImage = ' elementor-has-image';

			var imageHtml = '<img src="' + _.escape( imageUrl ) + '" alt="testimonial" />';
			if ( settings.link?.url ) {
				imageHtml = '<a href="' + elementor.helpers.sanitizeUrl( settings.link?.url ) + '">' + imageHtml + '</a>';
			}
		}

		var testimonial_image_position = settings.testimonial_image_position ? ' elementor-testimonial-image-position-' + settings.testimonial_image_position : '';
		#>
		<div class="elementor-testimonial-wrapper">
			<# if ( '' !== settings.testimonial_content ) {
				view.addRenderAttribute( 'testimonial_content', {
					'data-binding-type': 'content',
					'data-binding-setting': 'testimonial_content',
					'data-binding-config': JSON.stringify({
						'testimonial_content': {
							editType: "text"
						}
					})
				} );
				view.addRenderAttribute( 'testimonial_content', 'class', 'elementor-testimonial-content' );

				view.addInlineEditingAttributes( 'testimonial_content' );
				#>
				<div {{{ view.getRenderAttributeString( 'testimonial_content' ) }}}>{{ settings.testimonial_content }}</div>
			<# } #>
			<div class="elementor-testimonial-meta{{ hasImage }}{{ testimonial_image_position }}">
				<div class="elementor-testimonial-meta-inner">
					<# if ( imageUrl ) { #>
						<div class="elementor-testimonial-image">{{{ imageHtml }}}</div>
					<# } #>
					<div class="elementor-testimonial-details">
						<?php $this->render_testimonial_description(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_testimonial_description() {
		?>
		<#
		if ( '' !== settings.testimonial_name ) {
			view.addRenderAttribute( 'testimonial_name', 'class', 'elementor-testimonial-name' );

			view.addInlineEditingAttributes( 'testimonial_name', 'none' );

			if ( settings.link?.url ) {
				#>
				<a href="{{  elementor.helpers.sanitizeUrl( settings.link?.url ) }}" {{{ view.getRenderAttributeString( 'testimonial_name' ) }}}>{{ settings.testimonial_name }}</a>
				<#
			} else {
				#>
				<div {{{ view.getRenderAttributeString( 'testimonial_name' ) }}}>{{ settings.testimonial_name }}</div>
				<#
			}
		}

		if ( '' !== settings.testimonial_job ) {
			view.addRenderAttribute( 'testimonial_job', 'class', 'elementor-testimonial-job' );

			view.addInlineEditingAttributes( 'testimonial_job', 'none' );

			if ( settings.link?.url ) {
				#>
				<a href="{{  elementor.helpers.sanitizeUrl( settings.link?.url ) }}" {{{ view.getRenderAttributeString( 'testimonial_job' ) }}}>{{ settings.testimonial_job }}</a>
				<#
			} else {
				#>
				<div {{{ view.getRenderAttributeString( 'testimonial_job' ) }}}>{{ settings.testimonial_job }}</div>
				<#
			}
		}
		#>
		<?php
	}
}
