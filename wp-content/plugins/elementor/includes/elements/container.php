<?php
namespace Elementor\Includes\Elements;

use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Element_Base;
use Elementor\Embed;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Flex_Container;
use Elementor\Group_Control_Flex_Item;
use Elementor\Group_Control_Grid_Container;
use Elementor\Plugin;
use Elementor\Shapes;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Container extends Element_Base {

	/**
	 * @var \Elementor\Core\Kits\Documents\Kit
	 */
	private $active_kit;

	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Container constructor.
	 *
	 * @param array      $data
	 * @param array|null $args
	 *
	 * @return void
	 */
	public function __construct( array $data = [], ?array $args = null ) {
		parent::__construct( $data, $args );

		$this->active_kit = Plugin::$instance->kits_manager->get_active_kit();
	}

	/**
	 * Get the element type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'container';
	}

	/**
	 * Get the element name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'container';
	}

	/**
	 * Get the element display name.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Container', 'elementor' );
	}

	/**
	 * Get the element display icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-container';
	}

	public function get_keywords() {
		return [ 'Container', 'Flex', 'Flexbox', 'Flexbox Container', 'Grid', 'Grid Container', 'CSS Grid', 'Layout' ];
	}

	public function get_panel_presets() {
		return [
			'container_grid' => [
				'replacements' => [
					'name' => 'container_grid',
					'controls' => [
						'container_type' => [ 'default' => 'grid' ],
					],
					'title' => esc_html__( 'Grid', 'elementor' ),
					'icon' => 'eicon-container-grid',
					'custom' => [
						'isPreset' => true,
						'originalWidget' => $this->get_name(),
						'presetWidget' => 'container_grid',
						'preset_settings' => [
							'container_type' => 'grid',
							'presetTitle' => esc_html__( 'Grid', 'elementor' ),
							'presetIcon' => 'eicon-container-grid',
						],
					],
				],
			],
		];
	}

	/**
	 * Override the render attributes to add a custom wrapper class.
	 *
	 * @return void
	 */
	protected function add_render_attributes() {
		parent::add_render_attributes();

		$is_nested_class_name = $this->get_data( 'isInner' ) ? 'e-child' : 'e-parent';

		$this->add_render_attribute( '_wrapper', [
			'class' => [
				'e-con',
				$is_nested_class_name,
			],
		] );
	}

	/**
	 * Override the initial element config to display the Container in the panel.
	 *
	 * @return array
	 */
	protected function get_initial_config() {
		$config = parent::get_initial_config();

		$config['controls'] = $this->get_controls();
		$config['tabs_controls'] = $this->get_tabs_controls();
		$config['show_in_panel'] = true;
		$config['categories'] = [ 'layout' ];
		$config['include_in_widgets_config'] = true;

		return $config;
	}

	/**
	 * Render the element JS template.
	 *
	 * @return void
	 */
	protected function content_template() {
		?>
		<# if ( 'boxed' === settings.content_width ) { #>
			<div class="e-con-inner">
		<#
		}
		if ( settings.background_video_link ) {
			let videoAttributes = 'autoplay muted playsinline';

			if ( ! settings.background_play_once ) {
				videoAttributes += ' loop';
			}

			view.addRenderAttribute(
				'background-video-container',
				{
					'class': 'elementor-background-video-container',
					'aria-hidden': 'true',
				}
			);

			if ( ! settings.background_play_on_mobile ) {
				view.addRenderAttribute( 'background-video-container', 'class', 'elementor-hidden-mobile' );
			}
			#>
			<div {{{ view.getRenderAttributeString( 'background-video-container' ) }}}>
				<div class="elementor-background-video-embed"></div>
				<video class="elementor-background-video-hosted" {{ videoAttributes }}></video>
			</div>
		<# } #>
		<div class="elementor-shape elementor-shape-top" aria-hidden="true"></div>
		<div class="elementor-shape elementor-shape-bottom" aria-hidden="true"></div>
		<# if ( 'boxed' === settings.content_width ) { #>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Render the video background markup.
	 *
	 * @return void
	 */
	protected function render_video_background() {
		$settings = $this->get_settings_for_display();

		if ( 'video' !== $settings['background_background'] ) {
			return;
		}

		if ( ! $settings['background_video_link'] ) {
			return;
		}

		$video_properties = Embed::get_video_properties( $settings['background_video_link'] );

		$this->add_render_attribute(
			'background-video-container',
			[
				'class' => 'elementor-background-video-container',
				'aria-hidden' => 'true',
			]
		);

		if ( ! $settings['background_play_on_mobile'] ) {
			$this->add_render_attribute( 'background-video-container', 'class', 'elementor-hidden-mobile' );
		}

		?><div <?php $this->print_render_attribute_string( 'background-video-container' ); ?>>
			<?php if ( $video_properties ) : ?>
				<div class="elementor-background-video-embed"></div>
				<?php
			else :
				$video_tag_attributes = 'autoplay muted playsinline';

				if ( 'yes' !== $settings['background_play_once'] ) {
					$video_tag_attributes .= ' loop';
				}
				?>
				<video class="elementor-background-video-hosted" <?php echo esc_attr( $video_tag_attributes ); ?>></video>
			<?php endif; ?>
		</div><?php
	}

	/**
	 * Render the Container's shape divider.
	 * TODO: Copied from `section.php`.
	 *
	 * Used to generate the shape dividers HTML.
	 *
	 * @param string $side - Shape divider side, used to set the shape key.
	 *
	 * @return void
	 */
	protected function render_shape_divider( $side ) {
		$settings = $this->get_active_settings();
		$base_setting_key = "shape_divider_$side";
		$negative = ! empty( $settings[ $base_setting_key . '_negative' ] );
		$shape_path = Shapes::get_shape_path( $settings[ $base_setting_key ], $negative );

		if ( ! is_file( $shape_path ) || ! is_readable( $shape_path ) ) {
			return;
		}
		?>
		<div class="elementor-shape elementor-shape-<?php echo esc_attr( $side ); ?>" aria-hidden="true" data-negative="<?php
			Utils::print_unescaped_internal_string( $negative ? 'true' : 'false' );
		?>">
			<?php
			// PHPCS - The file content is being read from a strict file path structure.
			echo Utils::file_get_contents( $shape_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<?php
	}

	/**
	 * Print safe HTML tag for the element based on the element settings.
	 *
	 * @return void
	 */
	protected function print_html_tag() {
		$html_tag = $this->get_settings( 'html_tag' );

		if ( empty( $html_tag ) ) {
			$html_tag = 'div';
		}

		Utils::print_validated_html_tag( $html_tag );
	}

	/**
	 * Before rendering the container content. (Print the opening tag, etc.)
	 *
	 * @return void
	 */
	public function before_render() {
		$settings = $this->get_settings_for_display();
		$link = $settings['link'];

		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( '_wrapper', $link );
		}

		?><<?php $this->print_html_tag(); ?> <?php $this->print_render_attribute_string( '_wrapper' ); ?>>
		<?php
		if ( $this->is_boxed_container( $settings ) ) { ?>
			<div class="e-con-inner">
		<?php }

		$this->render_video_background();

		if ( ! empty( $settings['shape_divider_top'] ) ) {
			$this->render_shape_divider( 'top' );
		}

		if ( ! empty( $settings['shape_divider_bottom'] ) ) {
			$this->render_shape_divider( 'bottom' );
		}
	}

	/**
	 * After rendering the Container content. (Print the closing tag, etc.)
	 *
	 * @return void
	 */
	public function after_render() {
		$settings = $this->get_settings_for_display();
		if ( $this->is_boxed_container( $settings ) ) { ?>
			</div>
		<?php } ?>
		</<?php $this->print_html_tag(); ?>>
		<?php
	}

	protected function is_boxed_container( array $settings ) {
		return ! empty( $settings['content_width'] ) && 'boxed' === $settings['content_width'];
	}

	/**
	 * Override the default child type to allow widgets & containers as children.
	 *
	 * @param array $element_data
	 *
	 * @return \Elementor\Element_Base|\Elementor\Widget_Base|null
	 */
	protected function _get_default_child_type( array $element_data ) {
		$el_types = array_keys( Plugin::$instance->elements_manager->get_element_types() );

		if ( in_array( $element_data['elType'], $el_types, true ) ) {
			return Plugin::$instance->elements_manager->get_element_types( $element_data['elType'] );
		}

		return Plugin::$instance->widgets_manager->get_widget_types( $element_data['widgetType'] );
	}

	/**
	 * Register the Container's layout controls.
	 *
	 * @return void
	 */
	protected function register_container_layout_controls() {
		$this->start_controls_section(
			'section_layout_container',
			[
				'label' => esc_html__( 'Container', 'elementor' ),
				'tab' => Controls_Manager::TAB_LAYOUT,
			]
		);

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		if ( array_key_exists( Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA, $active_breakpoints ) ) {
			$min_affected_device = Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA;
		} else {
			$min_affected_device = Breakpoints_Manager::BREAKPOINT_KEY_TABLET;
		}

		$this->add_control(
			'container_type',
			[
				'label' => esc_html__( 'Container Layout', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'flex',
				'prefix_class' => 'e-',
				'options' => [
					'flex' => esc_html__( 'Flexbox', 'elementor' ),
					'grid' => esc_html__( 'Grid', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--display: {{VALUE}}',
				],
				'separator' => 'after',
				'editor_available' => true,
			]
		);

		$this->add_control(
			'content_width',
			[
				'label' => esc_html__( 'Content Width', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'boxed',
				'options' => [
					'boxed' => esc_html__( 'Boxed', 'elementor' ),
					'full' => esc_html__( 'Full Width', 'elementor' ),
				],
				'render_type' => 'template',
				'prefix_class' => 'e-con-',
				'editor_available' => true,
			]
		);

		$width_control_settings = [
			'label' => esc_html__( 'Width', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
			'range' => [
				'px' => [
					'min' => 500,
					'max' => 1600,
				],
			],
			'default' => [
				'unit' => '%',
			],
			'min_affected_device' => [
				Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_LAPTOP => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_TABLET_EXTRA => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_TABLET => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA => $min_affected_device,
			],
		];

		$this->add_responsive_control(
			'width',
			array_merge( $width_control_settings, [
				'selectors' => [
					'{{WRAPPER}}' => '--width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_width' => 'full',
				],
				'device_args' => [
					Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP => [
						'placeholder' => [
							'size' => 100,
							'unit' => '%',
						],
					],
					Breakpoints_Manager::BREAKPOINT_KEY_MOBILE => [
						// The mobile width is not inherited from the higher breakpoint width controls.
						'placeholder' => [
							'size' => 100,
							'unit' => '%',
						],
					],
				],
			] )
		);

		$this->add_responsive_control(
			'boxed_width',
			array_merge( $width_control_settings, [
				'selectors' => [
					'{{WRAPPER}}' => '--content-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_width' => 'boxed',
				],
				'default' => [
					'unit' => 'px',
				],
				'device_args' => [
					Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP => [
						// Use the default width from the kit as a placeholder.
						'placeholder' => $this->active_kit->get_settings_for_display( 'container_width' ),
					],
					Breakpoints_Manager::BREAKPOINT_KEY_MOBILE => [
						// The mobile width is not inherited from the higher breakpoint width controls.
						'placeholder' => [
							'size' => 100,
							'unit' => '%',
						],
					],
				],
			] )
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label' => esc_html__( 'Min Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vh', 'custom' ],
				'range' => [
					'px' => [
						'max' => 1440,
					],
				],
				'description' => sprintf(
					/* translators: %s: 100vh. */
					esc_html__( 'To achieve full height Container use %s.', 'elementor' ),
					'100vh'
				),
				'selectors' => [
					'{{WRAPPER}}' => '--min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Flex_Container::get_type(),
			[
				'name' => 'flex',
				'selector' => '{{WRAPPER}}',
				'fields_options' => [
					'gap' => [
						'label' => esc_html__( 'Gaps', 'elementor' ),
						'device_args' => [
							Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP => [
								// Use the default gap from the kit as a placeholder.
								'placeholder' => $this->active_kit->get_settings_for_display( 'space_between_widgets' ),
							],
						],
					],
				],
				'condition' => [
					'container_type' => [ 'flex' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Grid_Container::get_type(),
			[
				'name' => 'grid',
				'selector' => '{{WRAPPER}}',
				'condition' => [
					'container_type' => [ 'grid' ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register the Container's items layout controls.
	 *
	 * @return void
	 */
	protected function register_items_layout_controls() {
		$this->start_controls_section(
			'section_layout_additional_options',
			[
				'label' => esc_html__( 'Additional Options', 'elementor' ),
				'tab' => Controls_Manager::TAB_LAYOUT,
			]
		);

		$this->add_control(
			'overflow',
			[
				'label' => esc_html__( 'Overflow', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'elementor' ),
					'hidden' => esc_html__( 'Hidden', 'elementor' ),
					'auto' => esc_html__( 'Auto', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--overflow: {{VALUE}}',
				],
			]
		);

		$possible_tags = [
			'div' => 'div',
			'header' => 'header',
			'footer' => 'footer',
			'main' => 'main',
			'article' => 'article',
			'section' => 'section',
			'aside' => 'aside',
			'nav' => 'nav',
			'a' => 'a ' . esc_html__( '(link)', 'elementor' ),
		];

		$options = [
			'' => esc_html__( 'Default', 'elementor' ),
		] + $possible_tags;

		$this->add_control(
			'html_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
			]
		);

		$this->add_control(
			'link_note',
			[
				'type' => Controls_Manager::ALERT,
				'alert_type' => 'warning',
				'content' => esc_html__( 'Don’t add links to elements nested in this container - it will break the layout.', 'elementor' ),
				'condition' => [
					'html_tag' => 'a',
				],
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
				'condition' => [
					'html_tag' => 'a',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register the Container's layout tab.
	 *
	 * @return void
	 */
	protected function register_layout_tab() {
		$this->register_container_layout_controls();

		$this->register_items_layout_controls();
	}

	/**
	 * Register the Container's background controls.
	 *
	 * @return void
	 */
	protected function register_background_controls() {
		$this->start_controls_section(
			'section_background',
			[
				'label' => esc_html__( 'Background', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_background' );

		/**
		 * Normal.
		 */
		$this->start_controls_tab(
			'tab_background_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'types' => [ 'classic', 'gradient', 'video', 'slideshow' ],
				'fields_options' => [
					'background' => [
						'frontend_available' => true,
					],
				],
			]
		);

		$this->add_control(
			'handle_slideshow_asset_loading',
			[
				'type' => Controls_Manager::HIDDEN,
				'assets' => [
					'styles' => [
						[
							'name' => 'e-swiper',
							'conditions' => [
								'terms' => [
									[
										'name' => 'background_background',
										'operator' => '===',
										'value' => 'slideshow',
									],
								],
							],
						],
					],
					'scripts' => [
						[
							'name' => 'swiper',
							'conditions' => [
								'terms' => [
									[
										'name' => 'background_background',
										'operator' => '===',
										'value' => 'slideshow',
									],
								],
							],
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		/**
		 * Hover.
		 */
		$this->start_controls_tab(
			'tab_background_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_hover',
				'selector' => '{{WRAPPER}}:hover',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (s)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'render_type' => 'ui',
				'separator' => 'before',
				'condition' => [
					'background_hover_background' => [ 'classic', 'gradient' ],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--background-transition: {{SIZE}}s;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register the Container's background overlay controls.
	 *
	 * @return void
	 */
	protected function register_background_overlay_controls() {
		$this->start_controls_section(
			'section_background_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_background_overlay' );

		/**
		 * Normal.
		 */
		$this->start_controls_tab(
			'tab_background_overlay',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$background_overlay_selector = '{{WRAPPER}}::before, {{WRAPPER}} > .elementor-background-video-container::before, {{WRAPPER}} > .e-con-inner > .elementor-background-video-container::before, {{WRAPPER}} > .elementor-background-slideshow::before, {{WRAPPER}} > .e-con-inner > .elementor-background-slideshow::before, {{WRAPPER}} > .elementor-motion-effects-container > .elementor-motion-effects-layer::before';

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay',
				'selector' => $background_overlay_selector,
				'fields_options' => [
					'background' => [
						'selectors' => [
							// Hack to set the `::before` content in order to render it only when there is a background overlay.
							$background_overlay_selector => '--background-overlay: \'\';',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'background_overlay_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--overlay-opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}}::before',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'background_overlay_image[url]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'background_overlay_color',
							'operator' => '!==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'overlay_blend_mode',
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
					'luminosity' => esc_html__( 'Luminosity', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--overlay-mix-blend-mode: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'background_overlay_image[url]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'background_overlay_color',
							'operator' => '!==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		/**
		 * Hover.
		 */
		$this->start_controls_tab(
			'tab_background_overlay_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$background_overlay_hover_selector = '{{WRAPPER}}:hover::before, {{WRAPPER}}:hover > .elementor-background-video-container::before, {{WRAPPER}}:hover > .e-con-inner > .elementor-background-video-container::before, {{WRAPPER}} > .elementor-background-slideshow:hover::before, {{WRAPPER}} > .e-con-inner > .elementor-background-slideshow:hover::before';

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay_hover',
				'selector' => $background_overlay_hover_selector,
				'fields_options' => [
					'background' => [
						'selectors' => [
							// Hack to set the `::before` content in order to render it only when there is a background overlay.
							$background_overlay_hover_selector => '--background-overlay: \'\';',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'background_overlay_hover_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}}:hover' => '--overlay-opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_hover_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->add_control(
			'background_overlay_hover_transition',
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
				'render_type' => 'ui',
				'separator' => 'before',
				'condition' => [
					'background_overlay_hover_background' => [ 'classic', 'gradient' ],
				],
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}}::before' => '--overlay-transition: {{SIZE}}s;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}}:hover::before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register the Container's border controls.
	 *
	 * @return void
	 */
	protected function register_border_controls() {
		$this->start_controls_section(
			'section_border',
			[
				'label' => esc_html__( 'Border', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_border' );

		/**
		 * Normal.
		 */
		$this->start_controls_tab(
			'tab_border',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}}',
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --border-top-width: {{TOP}}{{UNIT}}; --border-right-width: {{RIGHT}}{{UNIT}}; --border-bottom-width: {{BOTTOM}}{{UNIT}}; --border-left-width: {{LEFT}}{{UNIT}};',
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}; --border-color: {{VALUE}};',
						],
					],
					'border' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-style: {{VALUE}}; --border-style: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
			]
		);

		$this->end_controls_tab();

		/**
		 * Hover.
		 */
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_hover',
				'selector' => '{{WRAPPER}}:hover',
				'fields_options' => [
					'width' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --border-top-width: {{TOP}}{{UNIT}}; --border-right-width: {{RIGHT}}{{UNIT}}; --border-bottom-width: {{BOTTOM}}{{UNIT}}; --border-left-width: {{LEFT}}{{UNIT}};',
						],
					],
					'color' => [
						'selectors' => [
							'{{SELECTOR}}' => 'border-color: {{VALUE}}; --border-color: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}:hover' => '--border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --border-top-left-radius: {{TOP}}{{UNIT}}; --border-top-right-radius: {{RIGHT}}{{UNIT}}; --border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; --border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_hover',
				'selector' => '{{WRAPPER}}:hover',
			]
		);

		$this->add_control(
			'border_hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (s)',
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'border_hover_border',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'border_radius_hover[top]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'border_radius_hover[right]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'border_radius_hover[bottom]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'border_radius_hover[left]',
							'operator' => '!==',
							'value' => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}, {{WRAPPER}}::before' => '--border-transition: {{SIZE}}s;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register the Container's shape dividers controls.
	 * TODO: Copied from `section.php`.
	 *
	 * @return void
	 */
	protected function register_shape_dividers_controls() {
		$this->start_controls_section(
			'section_shape_divider',
			[
				'label' => esc_html__( 'Shape Divider', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_shape_dividers' );

		foreach ( [
			'top' => esc_html__( 'Top', 'elementor' ),
			'bottom' => esc_html__( 'Bottom', 'elementor' ),
		] as $side => $side_label ) {
			$base_control_key = "shape_divider_$side";

			$this->start_controls_tab(
				"tab_$base_control_key",
				[
					'label' => $side_label,
				]
			);

			$this->add_control(
				$base_control_key,
				[
					'label' => esc_html__( 'Type', 'elementor' ),
					'type' => Controls_Manager::VISUAL_CHOICE,
					'label_block' => true,
					'columns' => 2,
					'options' => Shapes::get_shapes(),
					'render_type' => 'none',
					'frontend_available' => true,
					'assets' => [
						'styles' => [
							[
								'name' => 'e-shapes',
								'conditions' => [
									'terms' => [
										[
											'name' => $base_control_key,
											'operator' => '!==',
											'value' => '',
										],
									],
								],
							],
						],
					],
				]
			);

			$this->add_control(
				$base_control_key . '_color',
				[
					'label' => esc_html__( 'Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'condition' => [
						"shape_divider_$side!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-shape-$side .elementor-shape-fill, {{WRAPPER}} > .e-con-inner > .elementor-shape-$side .elementor-shape-fill" => 'fill: {{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				$base_control_key . '_width',
				[
					'label' => esc_html__( 'Width', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'vw', 'custom' ],
					'default' => [
						'unit' => '%',
					],
					'tablet_default' => [
						'unit' => '%',
					],
					'mobile_default' => [
						'unit' => '%',
					],
					'range' => [
						'%' => [
							'min' => 100,
							'max' => 300,
						],
						'vw' => [
							'min' => 100,
							'max' => 300,
						],
					],
					'condition' => [
						"shape_divider_$side" => array_keys( Shapes::filter_shapes( 'height_only', Shapes::FILTER_EXCLUDE ) ),
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-shape-$side svg, {{WRAPPER}} > .e-con-inner > .elementor-shape-$side svg" => 'width: calc({{SIZE}}{{UNIT}} + 1.3px)',
					],
				]
			);

			$this->add_responsive_control(
				$base_control_key . '_height',
				[
					'label' => esc_html__( 'Height', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem', 'custom' ],
					'range' => [
						'px' => [
							'max' => 500,
						],
						'em' => [
							'max' => 50,
						],
						'rem' => [
							'max' => 50,
						],
					],
					'condition' => [
						"shape_divider_$side!" => '',
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-shape-$side svg, {{WRAPPER}} > .e-con-inner > .elementor-shape-$side svg" => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				$base_control_key . '_flip',
				[
					'label' => esc_html__( 'Flip', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'condition' => [
						"shape_divider_$side" => array_keys( Shapes::filter_shapes( 'has_flip' ) ),
					],
					'selectors' => [
						"{{WRAPPER}} > .elementor-shape-$side svg, {{WRAPPER}} > .e-con-inner > .elementor-shape-$side svg" => 'transform: translateX(-50%) rotateY(180deg)',
					],
				]
			);

			$this->add_control(
				$base_control_key . '_negative',
				[
					'label' => esc_html__( 'Invert', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'frontend_available' => true,
					'condition' => [
						"shape_divider_$side" => array_keys( Shapes::filter_shapes( 'has_negative' ) ),
					],
					'render_type' => 'none',
				]
			);

			$this->add_control(
				$base_control_key . '_above_content',
				[
					'label' => esc_html__( 'Bring to Front', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'selectors' => [
						"{{WRAPPER}} > .elementor-shape-$side, {{WRAPPER}} > .e-con-inner > .elementor-shape-$side" => 'z-index: 2; pointer-events: none',
					],
					'condition' => [
						"shape_divider_$side!" => '',
					],
				]
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	/**
	 * Register the Container's style tab.
	 *
	 * @return void
	 */
	protected function register_style_tab() {
		$this->register_background_controls();

		$this->register_background_overlay_controls();

		$this->register_border_controls();

		$this->register_shape_dividers_controls();
	}

	/**
	 * Register the Container's advanced style controls.
	 *
	 * @return void
	 */
	protected function register_advanced_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_responsive_control(
			'margin',
			[
				'label' => esc_html__( 'Margin', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--margin-top: {{TOP}}{{UNIT}}; --margin-bottom: {{BOTTOM}}{{UNIT}}; --margin-left: {{LEFT}}{{UNIT}}; --margin-right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--padding-top: {{TOP}}{{UNIT}}; --padding-bottom: {{BOTTOM}}{{UNIT}}; --padding-left: {{LEFT}}{{UNIT}}; --padding-right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_grid_item',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Grid Item', 'elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'grid_column',
			[
				'label' => esc_html__( 'Column Span', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => ' Default',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'custom' => 'Custom',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'grid-column: span {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_column_custom',
			[
				'label' => esc_html__( 'Custom', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'ai' => [
					'active' => false,
				],
				'selectors' => [
					'{{WRAPPER}}' => 'grid-column: {{VALUE}}',
				],
				'condition' => [
					'grid_column' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'grid_row',
			[
				'label' => esc_html__( 'Row Span', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => ' Default',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'custom' => 'Custom',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'grid-row: span {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_row_custom',
			[
				'label' => esc_html__( 'Custom', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'after',
				'ai' => [
					'active' => false,
				],
				'selectors' => [
					'{{WRAPPER}}' => 'grid-row: {{VALUE}}',
				],
				'condition' => [
					'grid_row' => 'custom',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Flex_Item::get_type(),
			[
				'name' => '_flex',
				'include' => [
					'align_self',
					'order',
					'order_custom',
					'size',
					'grow',
					'shrink',
				],
				'selector' => '{{WRAPPER}}.e-con', // Hack to increase specificity.
				'separator' => 'before',
			]
		);

		$this->add_control(
			'position_description',
			[
				'type' => Controls_Manager::ALERT,
				'alert_type' => 'warning',
				'heading' => esc_html__( 'Please note!', 'elementor' ),
				'content' => esc_html__( 'Custom positioning is not considered best practice for responsive web design and should not be used too frequently.', 'elementor' ),
				'render_type' => 'ui',
				'condition' => [
					'position!' => '',
				],
			]
		);

		// TODO: Copied from `common.php` - Extract to group control.
		$this->add_control(
			'position',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'elementor' ),
					'absolute' => esc_html__( 'Absolute', 'elementor' ),
					'fixed' => esc_html__( 'Fixed', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--position: {{VALUE}};',
				],
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$left = esc_html__( 'Left', 'elementor' );
		$right = esc_html__( 'Right', 'elementor' );

		$start = is_rtl() ? $right : $left;
		$end = ! is_rtl() ? $right : $left;

		$this->add_control(
			'_offset_orientation_h',
			[
				'label' => esc_html__( 'Horizontal Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => $start,
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => $end,
						'icon' => 'eicon-h-align-right',
					],
				],
				'classes' => 'elementor-control-start-end',
				'render_type' => 'ui',
				'condition' => [
					'position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_x',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 0,
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_h!' => 'end',
					'position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_x_end',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 0,
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_h' => 'end',
					'position!' => '',
				],
			]
		);

		$this->add_control(
			'_offset_orientation_v',
			[
				'label' => esc_html__( 'Vertical Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_y',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'vw', 'custom' ],
				'default' => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_v!' => 'end',
					'position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'_offset_y_end',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'vw', 'custom' ],
				'default' => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'_offset_orientation_v' => 'end',
					'position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'z_index',
			[
				'label' => esc_html__( 'Z-Index', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'selectors' => [
					'{{WRAPPER}}' => '--z-index: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_element_id',
			[
				'label' => esc_html__( 'CSS ID', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
				],
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor' ),
				'style_transfer' => false,
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->add_control(
			'css_classes',
			[
				'label' => esc_html__( 'CSS Classes', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
				],
				'prefix_class' => '',
				'title' => esc_html__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor' ),
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		Plugin::$instance->controls_manager->add_display_conditions_controls( $this );

		$this->end_controls_section();
	}

	/**
	 * Register the Container's motion effects controls.
	 *
	 * @return void
	 */
	protected function register_motion_effects_controls() {
		$this->start_controls_section(
			'section_effects',
			[
				'label' => esc_html__( 'Motion Effects', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		Plugin::$instance->controls_manager->add_motion_effects_promotion_control( $this );

		$this->add_responsive_control(
			'animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'slow' => esc_html__( 'Slow', 'elementor' ),
					'' => esc_html__( 'Normal', 'elementor' ),
					'fast' => esc_html__( 'Fast', 'elementor' ),
				],
				'prefix_class' => 'animated-',
				'condition' => [
					'animation!' => '',
				],
			]
		);

		$this->add_control(
			'animation_delay',
			[
				'label' => esc_html__( 'Animation Delay', 'elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'condition' => [
					'animation!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register the Container's responsive controls.
	 *
	 * @return void
	 */
	protected function register_responsive_controls() {
		$this->start_controls_section(
			'_section_responsive',
			[
				'label' => esc_html__( 'Responsive', 'elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'heading_visibility',
			[
				'label' => esc_html__( 'Visibility', 'elementor' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'responsive_description',
			[
				'raw' => sprintf(
					/* translators: 1: Link open tag, 2: Link close tag. */
					esc_html__( 'Responsive visibility will take effect only on %1$s preview mode %2$s or live page, and not while editing in Elementor.', 'elementor' ),
					'<a href="javascript: $e.run( \'panel/close\' )">',
					'</a>'
				),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_hidden_device_controls();

		$this->end_controls_section();
	}

	/**
	 * Register the Container's advanced tab.
	 *
	 * @return void
	 */
	protected function register_advanced_tab() {
		$this->register_advanced_controls();

		$this->register_motion_effects_controls();

		$this->hook_sticky_notice_into_transform_section();

		$this->register_transform_section( 'con' );

		$this->register_responsive_controls();

		Plugin::$instance->controls_manager->add_custom_attributes_controls( $this );

		Plugin::$instance->controls_manager->add_custom_css_controls( $this );
	}

	protected function hook_sticky_notice_into_transform_section() {
		add_action( 'elementor/element/container/_section_transform/after_section_start', function( Container $container ) {
			if ( ! empty( $container->get_controls( 'transform_sticky_notice' ) ) ) {
				return;
			}

			$container->add_control(
				'transform_sticky_notice',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'warning',
					'content' => esc_html__( 'Note: Avoid applying transform properties on sticky containers. Doing so might cause unexpected results.', 'elementor' ),
				]
			);
		} );
	}

	/**
	 * Register the Container's controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_layout_tab();
		$this->register_style_tab();
		$this->register_advanced_tab();
	}

	public function on_import( $element ) {
		return self::slider_to_gaps_converter( $element );
	}

	/**
	 * Convert slider to gaps control for the 3.16 upgrade script
	 *
	 * @param array $element
	 * @return array
	 */
	public static function slider_to_gaps_converter( $element ) {
		$breakpoints = array_keys( (array) Plugin::$instance->breakpoints->get_breakpoints() );
		$breakpoints[] = 'desktop';
		$control_name = 'flex_gap';

		foreach ( $breakpoints as $breakpoint ) {
			$control = 'desktop' !== $breakpoint
					? $control_name . '_' . $breakpoint
					: $control_name;

			if ( ! isset( $element['settings'][ $control ] ) ) {
				continue;
			}

			$already_using_gaps_control = isset( $element['settings'][ $control ]['isLinked'] ); // Slider control won't have the 'isLinked' property.

			if ( ! $already_using_gaps_control ) {
				$old_size = strval( $element['settings'][ $control ]['size'] );

				$element['settings'][ $control ]['column'] = $old_size;
				$element['settings'][ $control ]['row'] = $old_size;
				$element['settings'][ $control ]['isLinked'] = true;
			}
		}

		return $element;
	}
}
