<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

/**
 * Elementor star rating widget.
 *
 * Elementor widget that displays star rating.
 *
 * @since 2.3.0
 */
class Widget_Star_Rating extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve star rating widget name.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'star-rating';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve star rating widget title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Star Rating', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve star rating widget icon.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-rating';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'star', 'rating', 'rate', 'review' ];
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
		return [ 'widget-star-rating' ];
	}

	/**
	 * Hide widget from panel.
	 *
	 * Hide the star rating widget from the panel.
	 *
	 * @since 3.17.0
	 * @return bool
	 */
	public function show_in_panel(): bool {
		return false;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register star rating widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_rating',
			[
				'label' => esc_html__( 'Star Rating', 'elementor' ),
			]
		);

		if ( Plugin::$instance->widgets_manager->get_widget_types( 'rating' ) ) {
			$this->add_deprecation_message(
				'3.17.0',
				esc_html__(
					'You are currently editing a Star Rating widget in its old version. Drag a new Rating widget onto your page to use a newer version, providing better capabilities.',
					'elementor'
				),
				'rating'
			);
		}

		$this->add_control(
			'rating_scale',
			[
				'label' => esc_html__( 'Rating Scale', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'5' => '0-5',
					'10' => '0-10',
				],
				'default' => '5',
			]
		);

		$this->add_control(
			'rating',
			[
				'label' => esc_html__( 'Rating', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'step' => 0.1,
				'default' => 5,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'star_style',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'star_fontawesome' => 'Font Awesome',
					'star_unicode' => 'Unicode',
				],
				'default' => 'star_fontawesome',
				'render_type' => 'template',
				'prefix_class' => 'elementor--star-style-',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'unmarked_star_style',
			[
				'label' => esc_html__( 'Unmarked Style', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Solid', 'elementor' ),
						'icon' => 'eicon-star',
					],
					'outline' => [
						'title' => esc_html__( 'Outline', 'elementor' ),
						'icon' => 'eicon-star-o',
					],
				],
				'default' => 'solid',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$start = is_rtl() ? 'right' : 'left';
		$end = ! is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => "eicon-text-align-$start",
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => "eicon-text-align-$end",
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'classes_dictionary' => [
					'left' => is_rtl() ? 'end' : 'start',
					'right' => is_rtl() ? 'start' : 'end',
				],
				'selectors_dictionary' => [
					'left' => is_rtl() ? 'end' : 'start',
					'right' => is_rtl() ? 'start' : 'end',
				],
				'prefix_class' => 'elementor-star-rating%s--align-',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-star-rating__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'selector' => '{{WRAPPER}} .elementor-star-rating__title',
			]
		);

		$this->add_responsive_control(
			'title_gap',
			[
				'label' => esc_html__( 'Gap', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'min' => 0,
						'max' => 5,
					],
					'rem' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}}:not(.elementor-star-rating--align-justify) .elementor-star-rating__title' => 'margin-inline-end: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stars_style',
			[
				'label' => esc_html__( 'Stars', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'min' => 0,
						'max' => 5,
					],
					'rem' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-inline-end: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'stars_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating i:before' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'stars_unmarked_color',
			[
				'label' => esc_html__( 'Unmarked Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_rating() {
		$settings = $this->get_settings_for_display();
		$rating_scale = (int) $settings['rating_scale'];
		$rating = min( (float) $settings['rating'], $rating_scale );

		return [ $rating, $rating_scale ];
	}

	/**
	 * Print the actual stars and calculate their filling.
	 *
	 * Rating type is float to allow stars-count to be a fraction.
	 * Floored-rating type is int, to represent the rounded-down stars count.
	 * In the `for` loop, the index type is float to allow comparing with the rating value.
	 *
	 * @since 2.3.0
	 * @access protected
	 */
	protected function render_stars( $icon ) {
		$rating_data = $this->get_rating();
		$rating = (float) $rating_data[0];
		$floored_rating = floor( $rating );
		$stars_html = '';

		for ( $stars = 1.0; $stars <= $rating_data[1]; $stars++ ) {
			if ( $stars <= $floored_rating ) {
				$stars_html .= '<i class="elementor-star-full" aria-hidden="true">' . $icon . '</i>';
			} elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
				$stars_html .= '<i class="elementor-star-' . ( $rating - $floored_rating ) * 10 . '" aria-hidden="true">' . $icon . '</i>';
			} else {
				$stars_html .= '<i class="elementor-star-empty" aria-hidden="true">' . $icon . '</i>';
			}
		}

		Utils::print_unescaped_internal_string( $stars_html );
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$rating_data = $this->get_rating();
		$textual_rating = sprintf(
			/* translators: 1: Rating value. 2: Rating scale. */
			esc_html__( 'Rated %1$s out of %2$s', 'elementor' ),
			$rating_data[0],
			$rating_data[1]
		);
		$icon = '&#xE934;';

		if ( 'star_fontawesome' === $settings['star_style'] ) {
			if ( 'outline' === $settings['unmarked_star_style'] ) {
				$icon = '&#xE933;';
			}
		} elseif ( 'star_unicode' === $settings['star_style'] ) {
			$icon = '&#9733;';

			if ( 'outline' === $settings['unmarked_star_style'] ) {
				$icon = '&#9734;';
			}
		}

		$this->add_render_attribute( 'icon_wrapper', [
			'class' => 'elementor-star-rating',
			'itemtype' => 'http://schema.org/Rating',
			'itemscope' => '',
			'itemprop' => 'reviewRating',
		] );
		?>
		<div class="elementor-star-rating__wrapper">
			<?php if ( ! Utils::is_empty( $settings['title'] ) ) : ?>
				<div class="elementor-star-rating__title"><?php echo esc_html( $settings['title'] ); ?></div>
			<?php endif; ?>
			<div <?php $this->print_render_attribute_string( 'icon_wrapper' ); ?>>
				<?php $this->render_stars( $icon ); ?>
				<span itemprop="ratingValue" class="elementor-screen-only"><?php echo esc_html( $textual_rating ); ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var getRating = function() {
				var ratingScale = parseInt( settings.rating_scale, 10 ),
					rating = settings.rating > ratingScale ? ratingScale : settings.rating;

				return [ rating, ratingScale ];
			},
			ratingData = getRating(),
			rating = ratingData[0],
			textualRating = 'Rated ' + ratingData[0] + ' out of ' + ratingData[1],
			renderStars = function( icon ) {
				var starsHtml = '',
					flooredRating = Math.floor( rating );

				for ( var stars = 1; stars <= ratingData[1]; stars++ ) {
					if ( stars <= flooredRating  ) {
						starsHtml += '<i class="elementor-star-full" aria-hidden="true">' + icon + '</i>';
					} else if ( flooredRating + 1 === stars && rating !== flooredRating ) {
						starsHtml += '<i class="elementor-star-' + ( rating - flooredRating ).toFixed( 1 ) * 10 + '" aria-hidden="true">' + icon + '</i>';
					} else {
						starsHtml += '<i class="elementor-star-empty" aria-hidden="true">' + icon + '</i>';
					}
				}

				return starsHtml;
			},
			icon = '&#xE934;';

			if ( 'star_fontawesome' === settings.star_style ) {
				if ( 'outline' === settings.unmarked_star_style ) {
					icon = '&#xE933;';
				}
			} else if ( 'star_unicode' === settings.star_style ) {
				icon = '&#9733;';

				if ( 'outline' === settings.unmarked_star_style ) {
					icon = '&#9734;';
				}
			}

			view.addRenderAttribute( 'iconWrapper', 'class', 'elementor-star-rating' );
			view.addRenderAttribute( 'iconWrapper', 'itemtype', 'http://schema.org/Rating' );
			view.addRenderAttribute( 'iconWrapper', 'itemscope', '' );
			view.addRenderAttribute( 'iconWrapper', 'itemprop', 'reviewRating' );

			var stars = renderStars( icon );
		#>
		<div class="elementor-star-rating__wrapper">
			<# if ( ! _.isEmpty( settings.title ) ) { #>
				<div class="elementor-star-rating__title">{{ settings.title }}</div>
			<# } #>
			<div {{{ view.getRenderAttributeString( 'iconWrapper' ) }}} >
				{{{ stars }}}
				<span itemprop="ratingValue" class="elementor-screen-only">{{ textualRating }}</span>
			</div>
		</div>
		<?php
	}
}
