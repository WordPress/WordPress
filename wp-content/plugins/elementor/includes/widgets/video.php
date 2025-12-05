<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Modules\Promotions\Controls\Promotion_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor video widget.
 *
 * Elementor widget that displays a video player.
 *
 * @since 1.0.0
 */
class Widget_Video extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve video widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'video';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve video widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Video', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve video widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-youtube';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the video widget belongs to.
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
		return [ 'video', 'player', 'embed', 'youtube', 'vimeo', 'dailymotion', 'videopress' ];
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
		return [ 'widget-video' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register video widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.19.0
	 * @access protected
	 *
	 * @return array Widget promotion data.
	 */
	protected function get_upsale_data() {
		return [
			'condition' => ! Utils::has_pro(),
			'image' => esc_url( ELEMENTOR_ASSETS_URL . 'images/go-pro.svg' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'title' => esc_html__( "Grab your visitors' attention", 'elementor' ),
			'description' => esc_html__( 'Get the Video Playlist widget and grow your toolbox with Elementor Pro.', 'elementor' ),
			'upgrade_url' => esc_url( 'https://go.elementor.com/go-pro-video-widget/' ),
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
		];
	}

	/**
	 * Register video widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_video',
			[
				'label' => esc_html__( 'Video', 'elementor' ),
			]
		);

		$this->add_control(
			'video_type',
			[
				'label' => esc_html__( 'Source', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => esc_html__( 'YouTube', 'elementor' ),
					'vimeo' => esc_html__( 'Vimeo', 'elementor' ),
					'dailymotion' => esc_html__( 'Dailymotion', 'elementor' ),
					'videopress' => esc_html__( 'VideoPress', 'elementor' ),
					'hosted' => esc_html__( 'Self Hosted', 'elementor' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'youtube_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', 'elementor' ) . ' (YouTube)',
				'default' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				'label_block' => true,
				'condition' => [
					'video_type' => 'youtube',
				],
				'ai' => [
					'active' => false,
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'vimeo_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', 'elementor' ) . ' (Vimeo)',
				'default' => 'https://vimeo.com/235215203',
				'label_block' => true,
				'condition' => [
					'video_type' => 'vimeo',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'dailymotion_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', 'elementor' ) . ' (Dailymotion)',
				'default' => 'https://www.dailymotion.com/video/x6tqhqb',
				'label_block' => true,
				'condition' => [
					'video_type' => 'dailymotion',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'insert_url',
			[
				'label' => esc_html__( 'External URL', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => [ 'hosted', 'videopress' ],
				],
			]
		);

		$this->add_control(
			'hosted_url',
			[
				'label' => esc_html__( 'Choose Video File', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_types' => [
					'video',
				],
				'condition' => [
					'video_type' => [ 'hosted', 'videopress' ],
					'insert_url' => '',
				],
			]
		);

		$this->add_control(
			'external_url',
			[
				'label' => esc_html__( 'URL', 'elementor' ),
				'type' => Controls_Manager::URL,
				'autocomplete' => false,
				'options' => false,
				'label_block' => true,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', 'elementor' ),
				'condition' => [
					'video_type' => 'hosted',
					'insert_url' => 'yes',
				],
			]
		);

		$this->add_control(
			'videopress_url',
			[
				'label' => esc_html__( 'URL', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'show_label' => false,
				'default' => 'https://videopress.com/v/ZCAOzTNk',
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'VideoPress URL', 'elementor' ),
				'ai' => [
					'active' => false,
				],
				'condition' => [
					'video_type' => 'videopress',
					'insert_url' => 'yes',
				],

			]
		);

		$this->add_control(
			'start',
			[
				'label' => esc_html__( 'Start Time', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Specify a start time (in seconds)', 'elementor' ),
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'end',
			[
				'label' => esc_html__( 'End Time', 'elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Specify an end time (in seconds)', 'elementor' ),
				'condition' => [
					'video_type' => [ 'youtube', 'hosted' ],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'video_options',
			[
				'label' => esc_html__( 'Video Options', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'elementor' ),
				'description' => sprintf(
					/* translators: 1: `<a>` opening tag, 2: `</a>` closing tag. */
					esc_html__( 'Note: Autoplay is affected by %1$s Google’s Autoplay policy %2$s on Chrome browsers.', 'elementor' ),
					'<a href="https://developers.google.com/web/updates/2017/09/autoplay-policy-changes" target="_blank">',
					'</a>'
				),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'play_on_mobile',
			[
				'label' => esc_html__( 'Play On Mobile', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'mute',
			[
				'label' => esc_html__( 'Mute', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type!' => 'dailymotion',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'controls',
			[
				'label' => esc_html__( 'Player Controls', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'video_type!' => 'vimeo',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'showinfo',
			[
				'label' => esc_html__( 'Video Info', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => [ 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'cc_load_policy',
			[
				'label' => esc_html__( 'Captions', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => [ 'youtube' ],
					'controls' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'logo',
			[
				'label' => esc_html__( 'Logo', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => [ 'dailymotion' ],
				],
			]
		);

		// YouTube.
		$this->add_control(
			'yt_privacy',
			[
				'label' => esc_html__( 'Privacy Mode', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When you turn on privacy mode, YouTube/Vimeo won\'t store information about visitors on your website unless they play the video.', 'elementor' ),
				'condition' => [
					'video_type' => [ 'youtube', 'vimeo' ],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lazy_load',
			[
				'label' => esc_html__( 'Lazy Load', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'video_type',
							'operator' => '===',
							'value' => 'youtube',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'show_image_overlay',
									'operator' => '===',
									'value' => 'yes',
								],
								[
									'name' => 'video_type',
									'operator' => '!==',
									'value' => 'hosted',
								],
							],
						],
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rel',
			[
				'label' => esc_html__( 'Suggested Videos', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Current Video Channel', 'elementor' ),
					'yes' => esc_html__( 'Any Video', 'elementor' ),
				],
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		// Vimeo.
		$this->add_control(
			'vimeo_title',
			[
				'label' => esc_html__( 'Intro Title', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_portrait',
			[
				'label' => esc_html__( 'Intro Portrait', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_byline',
			[
				'label' => esc_html__( 'Intro Byline', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Controls Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'video_type' => [ 'vimeo', 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'download_button',
			[
				'label' => esc_html__( 'Download Button', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'preload',
			[
				'label' => esc_html__( 'Preload', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'metadata' => esc_html__( 'Metadata', 'elementor' ),
					'auto' => esc_html__( 'Auto', 'elementor' ),
					'none' => esc_html__( 'None', 'elementor' ),
				],
				'description' => sprintf(
					'%1$s <a target="_blank" href="https://go.elementor.com/preload-video/">%2$s</a>',
					esc_html__( 'Preload attribute lets you specify how the video should be loaded when the page loads.', 'elementor' ),
					esc_html__( 'Learn more', 'elementor' ),
				),
				'default' => 'metadata',
				'condition' => [
					'video_type' => 'hosted',
					'autoplay' => '',
				],
			]
		);

		$this->add_control(
			'poster',
			[
				'label' => esc_html__( 'Poster', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_overlay',
			[
				'label' => esc_html__( 'Image Overlay', 'elementor' ),
			]
		);

		$this->add_control(
			'show_image_overlay',
			[
				'label' => esc_html__( 'Image Overlay', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => esc_html__( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_image_overlay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_overlay', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_overlay_size` and `image_overlay_custom_dimension`.
				'default' => 'full',
				'condition' => [
					'show_image_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_play_icon',
			[
				'label' => esc_html__( 'Play Icon', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'separator' => 'before',
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
			]
		);

		$this->add_control(
			'play_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'skin_settings' => [
					'inline' => [
						'none' => [
							'label' => 'Default',
							'icon' => 'eicon-play',
						],
						'icon' => [
							'icon' => 'eicon-star',
						],
					],
				],
				'recommended' => [
					'fa-regular' => [
						'play-circle',
					],
					'fa-solid' => [
						'play',
						'play-circle',
					],
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon!' => '',
				],
			]
		);

		$this->add_control(
			'lightbox',
			[
				'label' => esc_html__( 'Lightbox', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_off' => esc_html__( 'Off', 'elementor' ),
				'label_on' => esc_html__( 'On', 'elementor' ),
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_style',
			[
				'label' => esc_html__( 'Video', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Aspect Ratio', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'169' => '16:9',
					'219' => '21:9',
					'43' => '4:3',
					'32' => '3:2',
					'11' => '1:1',
					'916' => '9:16',
				],
				'selectors_dictionary' => [
					'169' => '1.77777', // 16 / 9
					'219' => '2.33333', // 21 / 9
					'43' => '1.33333', // 4 / 3
					'32' => '1.5', // 3 / 2
					'11' => '1', // 1 / 1
					'916' => '0.5625', // 9 / 16
				],
				'default' => '169',
				'selectors' => [
					'{{WRAPPER}} .elementor-wrapper' => '--video-aspect-ratio: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-wrapper',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_overlay_style',
			[
				'label' => esc_html__( 'Image Overlay', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'play_icon_title',
			[
				'label' => esc_html__( 'Play Icon', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'play_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-embed-play i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-custom-embed-play svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'play_icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'selectors' => [
					// Not using a CSS vars because the default size value is coming from a global scss file.
					'{{WRAPPER}} .elementor-custom-embed-play i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-custom-embed-play svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'play_icon_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-custom-embed-play i',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => esc_html__( 'Shadow', 'elementor' ),
					],
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
					'play_icon[library]!' => 'svg',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			[
				'label' => esc_html__( 'Lightbox', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
					'lightbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'lightbox_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#elementor-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color',
			[
				'label' => esc_html__( 'UI Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color_hover',
			[
				'label' => esc_html__( 'UI Hover Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_content_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'deprecation_warning',
			[
				'type' => Controls_Manager::ALERT,
				'alert_type' => 'danger',
				'content' => esc_html__( 'Note: These controls have been deprecated and are only visible if they were previously in use. The video’s width and position are now set based on its aspect ratio.', 'elementor' ),
				'separator' => 'before',
				'condition' => [
					'lightbox_video_width!' => '',
					'lightbox_content_position!' => '',
				],
			]
		);

		// Deprecated control. Visible only if it was previously in use.
		$this->add_control(
			'lightbox_video_width',
			[
				'label' => esc_html__( 'Content Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'default' => [
					'unit' => '%',
				],
				// 'selectors' => [
				// '(desktop+)#elementor-lightbox-{{ID}} .elementor-video-container' => 'width: {{SIZE}}{{UNIT}};',
				// ],
				'condition' => [
					'lightbox_video_width!' => '',
					'lightbox_content_position!' => '',
				],
			]
		);

		// Deprecated control. Visible only if it was previously in use.
		$this->add_control(
			'lightbox_content_position',
			[
				'label' => esc_html__( 'Content Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'' => esc_html__( 'Center', 'elementor' ),
					'top' => esc_html__( 'Top', 'elementor' ),
				],
				// 'selectors' => [
				// '#elementor-lightbox-{{ID}} .elementor-video-container' => '{{VALUE}}; transform: translateX(-50%);',
				// ],
				'selectors_dictionary' => [
					'top' => 'top: 60px',
				],
				'condition' => [
					'lightbox_video_width!' => '',
					'lightbox_content_position!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	public function print_a11y_text( $image_overlay ) {
		if ( empty( $image_overlay['alt'] ) ) {
			echo esc_html__( 'Play Video', 'elementor' );
		} else {
			echo esc_html__( 'Play Video about', 'elementor' ) . ' ' . esc_attr( $image_overlay['alt'] );
		}
	}

	/**
	 * Render video widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_url = $settings[ $settings['video_type'] . '_url' ];

		if ( 'hosted' === $settings['video_type'] ) {
			$video_url = $this->get_hosted_video_url();
		} else {
			if ( 'videopress' === $settings['video_type'] ) {
				$video_url = $this->get_videopress_video_url();
			}

			$embed_params = $this->get_embed_params();
			$embed_options = $this->get_embed_options();
		}

		if ( empty( $video_url ) ) {
			return;
		}

		if ( 'youtube' === $settings['video_type'] ) {
			$video_html = '<div class="elementor-video"></div>';
		}

		if ( 'hosted' === $settings['video_type'] ) {
			$this->add_render_attribute( 'video-wrapper', 'class', 'e-hosted-video' );

			ob_start();

			$this->render_hosted_video();

			$video_html = ob_get_clean();
		} else {
			$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();
			$post_id = get_queried_object_id();

			if ( $is_static_render_mode ) {
				$video_html = Embed::get_embed_thumbnail_html( $video_url, $post_id );
				// YouTube API requires a different markup which was set above.
			} elseif ( 'youtube' !== $settings['video_type'] ) {
				$video_html = Embed::get_embed_html( $video_url, $embed_params, $embed_options );
			}
		}

		if ( empty( $video_html ) ) {
			echo esc_url( $video_url );

			return;
		}

		$this->add_render_attribute( 'video-wrapper', 'class', 'elementor-wrapper' );

		$this->add_render_attribute( 'video-wrapper', 'class', 'elementor-open-' . ( $settings['lightbox'] ? 'lightbox' : 'inline' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'video-wrapper' ); ?>>
			<?php
			if ( ! $settings['lightbox'] ) {
				Utils::print_unescaped_internal_string( $video_html ); // XSS ok.
			}

			if ( $this->has_image_overlay() ) {
				$this->add_render_attribute( 'image-overlay', 'class', 'elementor-custom-embed-image-overlay' );

				if ( $settings['lightbox'] ) {
					if ( 'hosted' === $settings['video_type'] ) {
						$lightbox_url = $video_url;
					} else {
						$lightbox_url = Embed::get_embed_url( $video_url, $embed_params, $embed_options );
					}

					$lightbox_options = [
						'type' => 'video',
						'videoType' => $settings['video_type'],
						'url' => $lightbox_url,
						'autoplay' => $settings['autoplay'],
						'modalOptions' => [
							'id' => 'elementor-lightbox-' . $this->get_id(),
							'entranceAnimation' => $settings['lightbox_content_animation'],
							'entranceAnimation_tablet' => $settings['lightbox_content_animation_tablet'],
							'entranceAnimation_mobile' => $settings['lightbox_content_animation_mobile'],
							'videoAspectRatio' => $settings['aspect_ratio'] ?? '169',
						],
					];

					if ( 'hosted' === $settings['video_type'] ) {
						$lightbox_options['videoParams'] = $this->get_hosted_params();
					}

					$this->add_render_attribute( 'image-overlay', [
						'data-elementor-open-lightbox' => 'yes',
						'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
						'data-e-action-hash' => Plugin::instance()->frontend->create_action_hash( 'lightbox', $lightbox_options ),
					] );

					if ( Plugin::$instance->editor->is_edit_mode() ) {
						$this->add_render_attribute( 'image-overlay', [
							'class' => 'elementor-clickable',
						] );
					}
				} else {
					// When there is an image URL but no ID, it means the overlay image is the placeholder. In this case, get the placeholder URL.
					if ( empty( $settings['image_overlay']['id'] && ! empty( $settings['image_overlay']['url'] ) ) ) {
						$image_url = $settings['image_overlay']['url'];
					} else {
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings );
					}

					$this->add_render_attribute( 'image-overlay', 'style', 'background-image: url(' . $image_url . ');' );
				}
				?>
				<div <?php $this->print_render_attribute_string( 'image-overlay' ); ?>>
					<?php if ( $settings['lightbox'] ) : ?>
						<?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'image_overlay' ); ?>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['show_play_icon'] ) : ?>
						<div class="elementor-custom-embed-play" role="button" aria-label="<?php $this->print_a11y_text( $settings['image_overlay'] ); ?>" tabindex="0">
							<?php
							if ( empty( $settings['play_icon']['value'] ) ) {
								$settings['play_icon'] = [
									'library' => 'eicons',
									'value' => 'eicon-play',
								];
							}
							Icons_Manager::render_icon( $settings['play_icon'], [ 'aria-hidden' => 'true' ] );
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render video widget as plain content.
	 *
	 * Override the default behavior, by printing the video URL instead of rendering it.
	 *
	 * @since 1.4.5
	 * @access public
	 */
	public function render_plain_content() {
		$settings = $this->get_settings_for_display();

		if ( 'hosted' !== $settings['video_type'] ) {
			$url = $settings[ $settings['video_type'] . '_url' ];
		} else {
			$url = $this->get_hosted_video_url();
		}

		echo esc_url( $url );
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget embed parameters.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params() {
		$settings = $this->get_settings_for_display();

		$params = [];

		if ( $settings['autoplay'] && ! $this->has_image_overlay() ) {
			$params['autoplay'] = '1';

			if ( $settings['play_on_mobile'] ) {
				$params['playsinline'] = '1';
			}
		}

		$params_dictionary = [];

		if ( 'youtube' === $settings['video_type'] ) {
			$params_dictionary = [
				'loop',
				'controls',
				'mute',
				'rel',
				'cc_load_policy',
			];

			if ( $settings['loop'] ) {
				$video_properties = Embed::get_video_properties( $settings['youtube_url'] );

				$params['playlist'] = $video_properties['video_id'];
			}

			$params['start'] = $settings['start'];

			$params['end'] = $settings['end'];

			$params['wmode'] = 'opaque';
		} elseif ( 'vimeo' === $settings['video_type'] ) {
			$params_dictionary = [
				'loop',
				'mute' => 'muted',
				'vimeo_title' => 'title',
				'vimeo_portrait' => 'portrait',
				'vimeo_byline' => 'byline',
			];

			$params['color'] = str_replace( '#', '', $settings['color'] );

			$params['autopause'] = '0';

			if ( ! empty( $settings['yt_privacy'] ) ) {
				$params['dnt'] = 'true';
			}
		} elseif ( 'dailymotion' === $settings['video_type'] ) {
			$params_dictionary = [
				'controls',
				'mute',
				'showinfo' => 'ui-start-screen-info',
				'logo' => 'ui-logo',
			];

			$params['ui-highlight'] = str_replace( '#', '', $settings['color'] );

			$params['start'] = $settings['start'];

			$params['endscreen-enable'] = '0';
		} elseif ( 'videopress' === $settings['video_type'] ) {
			$params_dictionary = $this->get_params_dictionary_for_videopress();

			$params['at'] = $settings['start'];
		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = $settings[ $setting_name ] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}

	/**
	 * Whether the video widget has an overlay image or not.
	 *
	 * Used to determine whether an overlay image was set for the video.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return bool Whether an image overlay was set for the video.
	 */
	protected function has_image_overlay() {
		$settings = $this->get_settings_for_display();

		return ! empty( $settings['image_overlay']['url'] ) && 'yes' === $settings['show_image_overlay'];
	}

	/**
	 * @since 2.1.0
	 * @access private
	 */
	private function get_embed_options() {
		$settings = $this->get_settings_for_display();

		$embed_options = [];

		if ( 'youtube' === $settings['video_type'] ) {
			$embed_options['privacy'] = $settings['yt_privacy'];
		} elseif ( 'vimeo' === $settings['video_type'] ) {
			$embed_options['start'] = $settings['start'];
		}

		$embed_options['lazy_load'] = ! empty( $settings['lazy_load'] );

		return $embed_options;
	}

	/**
	 * @since 2.1.0
	 * @access private
	 */
	private function get_hosted_params() {
		$settings = $this->get_settings_for_display();

		$video_params = [];

		foreach ( [ 'autoplay', 'loop', 'controls' ] as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$video_params[ $option_name ] = '';
			}
		}

		if ( $settings['preload'] ) {
			$video_params['preload'] = $settings['preload'];
		}

		if ( $settings['mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( $settings['play_on_mobile'] ) {
			$video_params['playsinline'] = '';
		}

		if ( ! $settings['download_button'] ) {
			$video_params['controlsList'] = 'nodownload';
		}

		if ( $settings['poster']['url'] ) {
			$video_params['poster'] = $settings['poster']['url'];
		}

		return $video_params;
	}

	/**
	 *
	 * @return string
	 * @since 2.1.0
	 * @access private
	 */
	private function get_hosted_video_url() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['insert_url'] ) ) {
			$video_url = $settings['external_url']['url'];
		} else {
			$video_url = $settings['hosted_url']['url'];
		}

		if ( empty( $video_url ) ) {
			return '';
		}

		if ( $settings['start'] || $settings['end'] ) {
			$video_url .= '#t=';
		}

		if ( $settings['start'] ) {
			$video_url .= $settings['start'];
		}

		if ( $settings['end'] ) {
			$video_url .= ',' . $settings['end'];
		}

		return $video_url;
	}

	/**
	 * Get the VideoPress video URL from the current selected settings.
	 *
	 * @return string
	 */
	private function get_videopress_video_url() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['insert_url'] ) ) {
			return $settings['videopress_url'];
		}

		return $settings['hosted_url']['url'];
	}

	/**
	 * Get the params dictionary for VideoPress videos.
	 *
	 * @return array
	 */
	private function get_params_dictionary_for_videopress() {
		return [
			'controls',
			'autoplay' => 'autoPlay',
			'mute' => 'muted',
			'loop',
			'play_on_mobile' => 'playsinline',
		];
	}

	/**
	 *
	 * @since 2.1.0
	 * @access private
	 */
	private function render_hosted_video() {
		$video_url = $this->get_hosted_video_url();
		if ( empty( $video_url ) ) {
			return;
		}

		$video_params = $this->get_hosted_params();
		/* Sometimes the video url is base64, therefore we use `esc_attr` in `src`. */
		?>
		<video class="elementor-video" src="<?php echo esc_attr( $video_url ); ?>" <?php Utils::print_html_attributes( $video_params ); ?>></video>
		<?php
	}
}
