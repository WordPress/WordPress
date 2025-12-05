<?php

namespace Elementor\Modules\LinkInBio\Base;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Providers\Social_Network_Provider;
use Elementor\Core\Base\Traits\Shared_Widget_Controls_Trait;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Modules\LinkInBio\Classes\Render\Core_Render;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

abstract class Widget_Link_In_Bio_Base extends Widget_Base {

	use Shared_Widget_Controls_Trait;

	public function get_group_name(): string {
		return 'link-in-bio';
	}

	public function get_style_depends(): array {
		$widget_name = $this->get_name();

		$style_depends = Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' )
			? parent::get_style_depends()
			: [ 'elementor-icons-fa-solid', 'elementor-icons-fa-brands', 'elementor-icons-fa-regular' ];

		$style_depends[] = 'widget-link-in-bio-base';

		if ( 'link-in-bio' !== $widget_name ) {
			$style_depends[] = "widget-{$widget_name}";
		}

		return $style_depends;
	}

	public static function get_configuration() {
		return [
			'content' => [
				'identity_section' => [
					'identity_image_style' => [
						'default' => 'profile',
					],
					'has_heading_text' => false,
					'has_profile_image_controls' => false,
				],
				'bio_section' => [
					'title' => [
						'default' => esc_html__( 'Kitchen Chronicles', 'elementor' ),
					],
					'description' => [
						'default' => esc_html__( 'Join me on my journey to a healthier lifestyle', 'elementor' ),
					],
					'has_about_field' => false,
				],
				'icon_section' => [
					'has_text' => false,
					'platform' => [
						'group-1' => [
							Social_Network_Provider::EMAIL,
							Social_Network_Provider::TELEPHONE,
							Social_Network_Provider::MESSENGER,
							Social_Network_Provider::WAZE,
							Social_Network_Provider::WHATSAPP,
						],
						'limit' => 5,
					],
					'default' => [
						[
							'icon_platform' => Social_Network_Provider::FACEBOOK,
						],
						[
							'icon_platform' => Social_Network_Provider::INSTAGRAM,
						],
						[
							'icon_platform' => Social_Network_Provider::TIKTOK,
						],
					],
				],
				'cta_section' => [
					'cta_max' => 0,
					'cta_has_image' => false,
					'cta_repeater_defaults' => [
						[
							'cta_link_text' => esc_html__( 'Get Healthy', 'elementor' ),
						],
						[
							'cta_link_text' => esc_html__( 'Top 10 Recipes', 'elementor' ),
						],
						[
							'cta_link_text' => esc_html__( 'Meal Prep', 'elementor' ),
						],
						[
							'cta_link_text' => esc_html__( 'Healthy Living Resources', 'elementor' ),
						],
					],
				],
				'image_links_section' => false,
			],
			'style' => [
				'identity_section' => [
					'has_profile_image_shape' => true,
					'profile_image_max' => 115,
					'cover_image_max' => 1000,
				],
				'cta_section' => [
					'has_dividers' => false,
					'has_image_border' => false,
					'has_link_type' => [
						'default' => 'button',
					],
					'has_corners' => [
						'default' => 'rounded',
						'options' => [
							'round' => esc_html__( 'Round', 'elementor' ),
							'rounded' => esc_html__( 'Rounded', 'elementor' ),
							'sharp' => esc_html__( 'Sharp', 'elementor' ),
						],
					],
					'has_padding' => true,
					'has_background_control' => true,
					'has_cta_control_text' => false,
					'has_border_control' => [
						'prefix' => 'cta_links',
						'show_border_args' => [
							'condition' => [
								'cta_links_type' => 'button',
							],
						],
						'border_width_args' => [
							'condition' => [
								'cta_links_type' => 'button',
							],
							'selectors' => [
								'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-ctas-border-width: {{SIZE}}{{UNIT}}',
							],
						],
						'border_color_args' => [
							'condition' => [
								'cta_links_type' => 'button',
							],
							'selectors' => [
								'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-ctas-border-color: {{VALUE}}',
							],
						],
					],
				],
				'border_section' => [
					'field_options' => false,
					'overlay_field_options' => false,
				],
				'image_links_section' => false,
			],

		];
	}

	public function get_description_position() {
		return 'top';
	}

	public function get_icon(): string {
		return 'eicon-site-identity';
	}

	public function get_categories(): array {
		return [ 'link-in-bio' ];
	}

	public function get_keywords(): array {
		return [ 'buttons', 'bio', 'widget', 'link in bio' ];
	}

	public function get_image_position_options(): array {
		return [
			'' => esc_html__( 'Default', 'elementor' ),
			'center center' => esc_html__( 'Center Center', 'elementor' ),
			'center left' => esc_html__( 'Center Left', 'elementor' ),
			'center right' => esc_html__( 'Center Right', 'elementor' ),
			'top center' => esc_html__( 'Top Center', 'elementor' ),
			'top left' => esc_html__( 'Top Left', 'elementor' ),
			'top right' => esc_html__( 'Top Right', 'elementor' ),
			'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
			'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
			'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
		];
	}

	protected function register_controls(): void {

		$this->add_content_tab();

		$this->add_style_tab();
	}

	protected function render(): void {
		$render_strategy = new Core_Render( $this );

		$render_strategy->render();
	}

	protected function add_image_links_controls() {
		$config = static::get_configuration();

		if ( empty( $config['content']['image_links_section'] ) ) {
			return;
		}

		$this->start_controls_section(
			'image_links_section',
			[
				'label' => esc_html__( 'Image Links', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( ! empty( $config['content']['image_links_section']['images_max'] ) ) {
			$this->add_control(
				'image_links_alert',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => sprintf(
						/* translators: %s: Maximum number of images allowed. */
						esc_html__( 'Add up to %s Images', 'elementor' ),
						'<b>' . $config['content']['image_links_section']['images_max'] . '</b>'
					),
				]
			);
		}

		$this->add_icons_per_row_control(
			'image_links_per_row',
			[
				'1' => '1',
				'2' => '2',
				'3' => '3',
			],
			'2',
			esc_html__( 'Images Per Row', 'elementor' ),
			'--e-link-in-bio-image-links-columns',
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image_links_image',
			[
				'label' => esc_html__( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'image_links_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'autocomplete' => true,
				'label_block' => true,
				'placeholder' => esc_html__( 'Paste URL or type', 'elementor' ),
				'default' => [
					'is_external' => true,
				],
			],
		);

		$this->add_control(
			'image_links',
			[
				'type' => Controls_Manager::REPEATER,
				'max_items' => $config['content']['image_links_section']['images_max'] ?? 0,
				'fields' => $repeater->get_controls(),
				'prevent_empty' => true,
				'button_text' => esc_html__( 'Add item', 'elementor' ),
				'default' => $config['content']['image_links_section']['images_repeater_defaults'] ?? [],
			]
		);

		$this->end_controls_section();
	}

	protected function add_cta_controls() {
		$config = static::get_configuration();

		if ( empty( $config['content']['cta_section'] ) ) {
			return;
		}

		$this->start_controls_section(
			'cta_section',
			[
				'label' => esc_html__( 'CTA Link Buttons', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( ! empty( $config['content']['cta_section']['cta_max'] ) ) {
			$this->add_control(
				'cta_section_alert',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => sprintf(
						/* translators: %s: Maximum number of CTA links allowed. */
						esc_html__( 'Add up to %s CTA links', 'elementor' ),
						'<b>' . $config['content']['cta_section']['cta_max'] . '</b>'
					),
				]
			);
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'cta_link_text',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'default' => esc_html__( 'CTA link', 'elementor' ),
				'placeholder' => esc_html__( 'Enter link text', 'elementor' ),
			],
		);

		if ( $config['content']['cta_section']['cta_has_image'] ) {
			$repeater->add_control(
				'cta_link_image',
				[
					'label' => esc_html__( 'Choose Image', 'elementor' ),
					'type' => Controls_Manager::MEDIA,
					'label_block' => true,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);
		}

		$repeater->add_control(
			'cta_link_type',
			[
				'label' => esc_html__( 'Link Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => [

					[
						'label' => '',
						'options' => Social_Network_Provider::get_social_networks_text(
							[
								Social_Network_Provider::URL,
								Social_Network_Provider::FILE_DOWNLOAD,
							]
						),
					],
					[
						'label' => '   --',
						'options' => Social_Network_Provider::get_social_networks_text(
							[
								Social_Network_Provider::EMAIL,
								Social_Network_Provider::TELEPHONE,
								Social_Network_Provider::MESSENGER,
								Social_Network_Provider::WAZE,
								Social_Network_Provider::WHATSAPP,
							]
						),
					],
				],
				'default' => Social_Network_Provider::URL,
			],
		);

		$repeater->add_control(
			'cta_link_file',
			[
				'label' => esc_html__( 'Choose File', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'media_type' => [ 'application/pdf' ],
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::FILE_DOWNLOAD,
					],
				],
				'ai' => [
					'active' => false,
				],
			],
		);

		$repeater->add_control(
			'cta_link_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'autocomplete' => true,
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::URL,
					],
				],
				'placeholder' => esc_html__( 'Enter your link', 'elementor' ),
				'default' => [
					'is_external' => true,
				],
			],
		);

		$repeater->add_control(
			'cta_link_mail',
			[
				'label' => esc_html__( 'Mail', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::EMAIL,
					],
				],
				'placeholder' => esc_html__( 'Enter your email', 'elementor' ),
			],
		);

		$repeater->add_control(
			'cta_link_mail_subject',
			[
				'label' => esc_html__( 'Subject', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::EMAIL,
					],
				],
				'placeholder' => esc_html__( 'Subject', 'elementor' ),
			],
		);

		$repeater->add_control(
			'cta_link_mail_body',
			[
				'label' => esc_html__( 'Message', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::EMAIL,
					],
				],
				'placeholder' => esc_html__( 'Message', 'elementor' ),
			],
		);

		$repeater->add_control(
			'cta_link_number',
			[
				'label' => esc_html__( 'Number', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::TELEPHONE,
						Social_Network_Provider::WHATSAPP,
					],
				],
				'placeholder' => esc_html__( 'Enter your number', 'elementor' ),
			],
		);

		$repeater->add_control(
			'cta_link_location',
			[
				'label' => esc_html__( 'Location', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'is_external' => true,
				],
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::WAZE,
					],
				],
				'placeholder' => esc_html__( 'Paste Waze link', 'elementor' ),
			],
		);

		$repeater->add_control(
			'cta_link_username',
			[
				'label' => esc_html__( 'Username', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'cta_link_type' => [
						Social_Network_Provider::MESSENGER,
					],
				],
				'placeholder' => esc_html__( 'Enter your username', 'elementor' ),
			],
		);

		$this->add_control(
			'cta_link',
			[
				'type' => Controls_Manager::REPEATER,
				'max_items' => $config['content']['cta_section']['cta_max'] ?? 0,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ cta_link_text }}}',
				'button_text' => esc_html__( 'Add CTA Link', 'elementor' ),
				'default' => $config['content']['cta_section']['cta_repeater_defaults'],
			]
		);

		$this->end_controls_section();
	}

	protected function add_icons_controls(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'icons_section',
			[
				'label' => esc_html__( 'Icons', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( $config['content']['icon_section']['platform']['limit'] ) {
			$this->add_control(
				'custom_panel_alert',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => sprintf(
						/* translators: %s: Maximum number of icons allowed. */
						esc_html__( 'Add up to %s icons', 'elementor' ),
						'<b>' . $config['content']['icon_section']['platform']['limit'] . '</b>'
					),
				]
			);
		}

		$repeater = new Repeater();

		if ( $config['content']['icon_section']['has_text'] ) {
			$repeater->add_control(
				'icon_text',
				[
					'label' => esc_html__( 'Text', 'elementor' ),
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => esc_html__( 'Enter icon text', 'elementor' ),
				],
			);
		}

		$repeater->add_control(
			'icon_platform',
			[
				'label' => esc_html__( 'Platform', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => [

					[
						'label' => '',
						'options' => Social_Network_Provider::get_social_networks_text(
							$config['content']['icon_section']['platform']['group-1']
						),
					],
					[
						'label' => '   --',
						'options' => Social_Network_Provider::get_social_networks_text(
							[
								Social_Network_Provider::FACEBOOK,
								Social_Network_Provider::INSTAGRAM,
								Social_Network_Provider::LINKEDIN,
								Social_Network_Provider::PINTEREST,
								Social_Network_Provider::TIKTOK,
								Social_Network_Provider::TWITTER,
								Social_Network_Provider::YOUTUBE,
							]
						),
					],
					[
						'label' => '   --',
						'options' => Social_Network_Provider::get_social_networks_text(
							[
								Social_Network_Provider::APPLEMUSIC,
								Social_Network_Provider::BEHANCE,
								Social_Network_Provider::DRIBBBLE,
								Social_Network_Provider::SPOTIFY,
								Social_Network_Provider::SOUNDCLOUD,
								Social_Network_Provider::VIMEO,
							]
						),
					],
				],
				'default' => Social_Network_Provider::FACEBOOK,
			],
		);

		$repeater->add_control(
			'icon_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'autocomplete' => true,
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter your link', 'elementor' ),
				'default' => [
					'is_external' => true,
				],
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::VIMEO,
						Social_Network_Provider::FACEBOOK,
						Social_Network_Provider::SOUNDCLOUD,
						Social_Network_Provider::SPOTIFY,
						Social_Network_Provider::INSTAGRAM,
						Social_Network_Provider::LINKEDIN,
						Social_Network_Provider::PINTEREST,
						Social_Network_Provider::TIKTOK,
						Social_Network_Provider::TWITTER,
						Social_Network_Provider::YOUTUBE,
						Social_Network_Provider::APPLEMUSIC,
						Social_Network_Provider::BEHANCE,
						Social_Network_Provider::DRIBBBLE,
						Social_Network_Provider::SPOTIFY,
						Social_Network_Provider::SOUNDCLOUD,
						Social_Network_Provider::URL,
					],
				],
			],
		);

		$repeater->add_control(
			'icon_mail',
			[
				'label' => esc_html__( 'Email', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your email', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::EMAIL,
					],
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$repeater->add_control(
			'icon_mail_subject',
			[
				'label' => esc_html__( 'Subject', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Subject', 'elementor' ),
				'label_block' => true,
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::EMAIL,
					],
				],
			]
		);

		$repeater->add_control(
			'icon_mail_body',
			[
				'label' => esc_html__( 'Message', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Message', 'elementor' ),
				'label_block' => true,
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::EMAIL,
					],
				],
			]
		);

		$repeater->add_control(
			'icon_number',
			[
				'label' => esc_html__( 'Number', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( '+', 'elementor' ),
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::TELEPHONE,
						Social_Network_Provider::WHATSAPP,
					],
				],
				'ai' => [
					'active' => false,
				],
			],
		);

		$repeater->add_control(
			'icon_location',
			[
				'label' => esc_html__( 'Location', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'is_external' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'Paste Waze link', 'elementor' ),
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::WAZE,
					],
				],
				'ai' => [
					'active' => false,
				],
			],
		);

		$repeater->add_control(
			'icon_username',
			[
				'label' => esc_html__( 'Username', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter your username', 'elementor' ),
				'condition' => [
					'icon_platform' => [
						Social_Network_Provider::MESSENGER,
					],
				],
			],
		);

		$this->add_control(
			'icon',
			[
				'max_items' => $config['content']['icon_section']['platform']['limit'],
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => $this->get_icon_title_field(),
				'prevent_empty' => true,
				'button_text' => esc_html__( 'Add Icon', 'elementor' ),
				'default' => $config['content']['icon_section']['default'],
			]
		);

		$this->end_controls_section();
	}

	protected function get_icon_title_field(): string {
		$platform_icons_js = json_encode( Social_Network_Provider::get_social_networks_icons() );

		return <<<JS
	<#
	elementor.helpers.enqueueIconFonts( 'fa-solid' );
	elementor.helpers.enqueueIconFonts( 'fa-brands' );
	const mapping = {$platform_icons_js};
	#>
	<i class='{{{ mapping[icon_platform] }}}' ></i> {{{ icon_platform }}}
JS;
	}

	protected function add_style_tab(): void {

		$this->add_style_identity_controls();

		$this->add_style_bio_controls();

		$this->add_style_icons_controls();

		$this->add_style_cta_section();

		$this->add_style_image_links_controls();

		$this->add_style_background_controls();
	}

	protected function add_bio_section(): void {
		$config = static::get_configuration();
		$this->start_controls_section(
			'bio_section',
			[
				'label' => esc_html__( 'Bio', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'bio_heading',
			[
				'label' => esc_html__( 'Heading', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Heading', 'elementor' ),
				'default' => esc_html__( 'Sara Parker', 'elementor' ),
			]
		);

		$this->add_html_tag_control( 'bio_heading_tag', 'h2' );

		$this->add_control(
			'bio_title',
			[
				'label' => esc_html__( 'Title or Tagline', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Title', 'elementor' ),
				'default' => $config['content']['bio_section']['title']['default'],
			]
		);

		$this->add_html_tag_control( 'bio_title_tag', 'h3' );

		if ( $config['content']['bio_section']['has_about_field'] ) {
			$this->add_control(
				'bio_about',
				[
					'label' => esc_html__( 'About Heading', 'elementor' ),
					'type' => Controls_Manager::TEXTAREA,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => esc_html__( 'About', 'elementor' ),
					'default' => esc_html__( 'About Me', 'elementor' ),
				]
			);
			$this->add_html_tag_control( 'bio_about_tag', 'h3' );
		}

		$this->add_control(
			'bio_description',
			[
				'label' => esc_html__( 'Description', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Description', 'elementor' ),
				'default' => $config['content']['bio_section']['description']['default'],
			]
		);

		$this->end_controls_section();
	}

	protected function add_identity_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'identity_section',
			[
				'label' => esc_html__( 'Identity', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( $config['content']['identity_section']['has_profile_image_controls'] ) {
			$this->add_control(
				'identity_heading_cover',
				[
					'label' => esc_html__( 'Cover', 'elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'identity_image_cover',
				[
					'label' => esc_html__( 'Choose Image', 'elementor' ),
					'type' => Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_responsive_control(
				'identity_image_cover_position',
				[
					'label' => esc_html__( 'Position', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'desktop_default' => 'center center',
					'tablet_default' => 'center center',
					'mobile_default' => 'center center',
					'options' => $this->get_image_position_options(),
					'selectors' => [
						'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-cover-position: {{VALUE}}',
					],
					'condition' => [
						'identity_image_cover[url]!' => '',
					],
				]
			);
		}

		if ( $config['content']['identity_section']['has_heading_text'] ) {
			$this->add_control(
				'identity_heading',
				[
					'label' => $config['content']['identity_section']['has_heading_text'],
					'type' => Controls_Manager::HEADING,
				]
			);
		}

		if ( $config['content']['identity_section']['identity_image_style'] ) {
			$this->add_control(
				'identity_image_style',
				[
					'label' => esc_html__( 'Image style', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $config['content']['identity_section']['identity_image_style']['default'],
					'options' => [
						'profile' => esc_html__( 'Profile', 'elementor' ),
						'cover' => esc_html__( 'Cover', 'elementor' ),
					],
				]
			);
		}

		$this->add_control(
			'identity_image',
			[
				'label' => esc_html__( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_responsive_control(
			'identity_image_position',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'desktop_default' => 'center center',
				'tablet_default' => 'center center',
				'mobile_default' => 'center center',
				'options' => $this->get_image_position_options(),
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-profile-position: {{VALUE}}',
				],
				'condition' => [
					'identity_image[url]!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_image_links_controls(): void {
		$config = static::get_configuration();

		if ( empty( $config['style']['image_links_section'] ) ) {
			return;
		}

		$this->start_controls_section(
			'image_links_section_style',
			[
				'label' => esc_html__( 'Image Links', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_links_height',
			[
				'label' => esc_html__( 'Image Height', 'elementor' ) . ' (px)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-image-links-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		if ( $config['style']['image_links_section']['has_border_control'] ) {
			$this->add_borders_control(
				$config['style']['image_links_section']['has_border_control']['prefix'],
				$config['style']['image_links_section']['has_border_control']['show_border_args'],
				$config['style']['image_links_section']['has_border_control']['border_width_args'],
				$config['style']['image_links_section']['has_border_control']['border_color_args'],
			);
		}

		$this->end_controls_section();
	}

	protected function add_style_cta_section(): void {
		$config = static::get_configuration();

		if ( empty( $config['style']['cta_section'] ) ) {
			return;
		}

		$this->start_controls_section(
			'cta_links_section_style',
			[
				'label' => esc_html__( 'CTA Link Buttons', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['style']['cta_section']['has_cta_control_text'] ) {
			$this->add_control(
				'cta_links_heading',
				[
					'label' => $config['style']['cta_section']['has_cta_control_text'],
					'type' => Controls_Manager::HEADING,
				]
			);
		}

		if ( $config['style']['cta_section']['has_link_type'] ) {
			$this->add_control(
				'cta_links_type',
				[
					'label' => esc_html__( 'Type', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $config['style']['cta_section']['has_link_type']['default'],
					'options' => [
						'button' => esc_html__( 'Button', 'elementor' ),
						'link' => esc_html__( 'Link', 'elementor' ),
					],
				]
			);
		}

		$this->add_control(
			'cta_links_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-ctas-text-color: {{VALUE}}',
					'{{WRAPPER}} .e-link-in-bio__cta.is-type-link' => '--e-link-in-bio-ctas-text-color: {{VALUE}}',
				],
			]
		);

		$condition_if_has_links = [];
		if ( $config['style']['cta_section']['has_link_type'] ) {
			$condition_if_has_links = [
				'cta_links_type' => 'button',
			];
		}

		if ( $config['style']['cta_section']['has_background_control'] ) {
			$this->add_control(
				'cta_links_background_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'condition' => $condition_if_has_links,
					'selectors' => [
						'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-ctas-background-color: {{VALUE}}',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cta_links_typography',
				'selector' => '{{WRAPPER}} .e-link-in-bio__cta',
			]
		);

		if ( $config['style']['cta_section']['has_border_control'] ) {
			$this->add_borders_control(
				$config['style']['cta_section']['has_border_control']['prefix'],
				$config['style']['cta_section']['has_border_control']['show_border_args'],
				$config['style']['cta_section']['has_border_control']['border_width_args'],
				$config['style']['cta_section']['has_border_control']['border_color_args'],
			);
		}

		if ( $config['style']['cta_section']['has_corners'] ) {
			$this->add_control(
				'cta_links_corners',
				[
					'label' => esc_html__( 'Corners', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $config['style']['cta_section']['has_corners']['default'],
					'options' => $config['style']['cta_section']['has_corners']['options'],
					'condition' => $condition_if_has_links,
				]
			);
		}

		if ( $config['style']['cta_section']['has_padding'] ) {
			$this->add_control(
				'cta_links_hr',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);

			$this->add_responsive_control(
				'cta_links_padding',
				[
					'label' => esc_html__( 'Padding', 'elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'default' => [
						'unit' => 'px',
						'isLinked' => false,
					],
					'condition' => $condition_if_has_links,
					'selectors' => [
						'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-ctas-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-link-in-bio-ctas-padding-block-start: {{TOP}}{{UNIT}}; --e-link-in-bio-ctas-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-link-in-bio-ctas-padding-inline-start: {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		if ( $config['style']['cta_section']['has_dividers'] ) {
			$this->add_control(
				'cta_links_hr',
				[
					'type' => Controls_Manager::HEADING,
					'label' => esc_html__( 'Dividers', 'elementor' ),
					'separator' => 'before',
				]
			);

			$this->add_control(
				'cta_links_divider_color',
				[
					'label' => esc_html__( 'Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-link-in-bio__cta' => 'border-bottom-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'cta_links_divider_width',
				[
					'label' => esc_html__( 'Weight', 'elementor' ) . ' (px)',
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 10,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
					],
					'selectors' => [
						'{{WRAPPER}} .e-link-in-bio__cta' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
					],
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_style_identity_controls(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'identity_section_style',
			[
				'label' => esc_html__( 'Identity', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$condition = [];
		if ( $config['content']['identity_section']['identity_image_style'] ) {
			$condition = [
				'identity_image_style' => 'profile',
			];
		}

		$this->add_identity_image_profile_controls( $condition );

		$condition = [
			'identity_image_style' => 'cover',
		];

		$this->add_identity_image_cover_control( $condition );

		$this->end_controls_section();
	}

	protected function add_content_tab(): void {

		$this->add_identity_section();

		$this->add_bio_section();

		$this->add_icons_controls();

		$this->add_cta_controls();

		$this->add_image_links_controls();
	}

	protected function add_style_bio_controls(): void {
		$this->start_controls_section(
			'bio_section_style',
			[
				'label' => esc_html__( 'Bio', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bio_heading_heading',
			[
				'label' => esc_html__( 'Heading', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bio_heading_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-heading-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bio_heading_typography',
				'selector' => '{{WRAPPER}} .e-link-in-bio__heading',
			]
		);

		$this->add_control(
			'bio_title_heading',
			[
				'label' => esc_html__( 'Title or Tagline', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bio_title_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-title-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bio_title_typography',
				'selector' => '{{WRAPPER}} .e-link-in-bio__title',
			]
		);

		$this->add_control(
			'bio_description_heading',
			[
				'label' => esc_html__( 'Description', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'bio_description_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-description-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bio_description_typography',
				'selector' => '{{WRAPPER}} .e-link-in-bio__description',
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_icons_controls(): void {

		$this->start_controls_section(
			'icons_section_style',
			[
				'label' => esc_html__( 'Icons', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icons_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-icon-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icons_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => esc_html__( 'Small', 'elementor' ),
					'medium' => esc_html__( 'Medium', 'elementor' ),
					'large' => esc_html__( 'Large', 'elementor' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_background_controls(): void {
		$config = static::get_configuration();

		// Defaults for background image and overlay
		$bg_section_image_field_option_defaults = [
			'background' => [
				'default' => 'classic',
			],
			'position' => [
				'default' => 'center center',
			],
			'size' => [
				'default' => 'cover',
			],
		];

		// Background image
		$bg_image_field_options = $bg_section_image_field_option_defaults;

		if ( $config['style']['border_section']['field_options'] ) {
			$bg_image_field_options = array_merge(
				$bg_section_image_field_option_defaults,
				$config['style']['border_section']['field_options']
			);
		}

		// Background overlay
		$bg_overlay_image_field_options = $bg_section_image_field_option_defaults;

		if ( $config['style']['border_section']['overlay_field_options'] ) {
			$bg_overlay_image_field_options = array_merge(
				$bg_section_image_field_option_defaults,
				$config['style']['border_section']['overlay_field_options']
			);
		}

		$this->start_controls_section(
			'background_border_section_style',
			[
				'label' => esc_html__( 'Box', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_border_background',
			[
				'label' => esc_html__( 'Background', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_border_background_group',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .e-link-in-bio__bg',
				'fields_options' => $bg_image_field_options,
			]
		);

		$this->add_control(
			'background_border_background_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_border_background_overlay_group',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .e-link-in-bio__bg-overlay',
				'fields_options' => $bg_overlay_image_field_options,
			]
		);

		$this->add_responsive_control(
			'background_overlay_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0.5,
				],
				'condition' => [
					'background_border_background_overlay_group_background!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--background-overlay-opacity: {{SIZE}};',
				],
			]
		);

		$this->add_borders_control(
			'background',
			[
				'selectors' => [],
				'separator' => 'before',
			],
			[
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-border-width: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_dimensions',
			[
				'label' => esc_html__( 'Dimensions', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'advanced_layout_full_width_custom',
			[
				'label' => esc_html__( 'Full Width', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementor' ),
				'label_off' => esc_html__( 'No', 'elementor' ),
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'advanced_layout_width',
			[
				'label' => esc_html__( 'Layout Width', 'elementor' ) . ' (px)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'advanced_layout_full_width_custom' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-container-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'advanced_layout_content_width',
			[
				'label' => esc_html__( 'Content Width', 'elementor' ) . ' (px)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-content-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'advanced_layout_full_screen_height',
			[
				'label' => esc_html__( 'Full Screen Height', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementor' ),
				'label_off' => esc_html__( 'No', 'elementor' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'advanced_layout_full_width_custom' => 'yes',
				],
			],
		);

		$configured_breakpoints = $this->get_configured_breakpoints();

		$this->add_control(
			'advanced_layout_full_screen_height_controls',
			[
				'label' => esc_html__( 'Apply Full Screen Height on', 'elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $configured_breakpoints['devices_options'],
				'default' => $configured_breakpoints['active_devices'],
				'condition' => [
					'advanced_layout_full_width_custom' => 'yes',
					'advanced_layout_full_screen_height' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_identity_image_profile_controls( array $condition ): void {
		$config = static::get_configuration();

		$this->add_responsive_control(
			'identity_image_size',
			[
				'label' => esc_html__( 'Image Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => $config['style']['identity_section']['profile_image_max'] ?? 150,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'condition' => $condition,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-profile-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		if ( $config['style']['identity_section']['has_profile_image_shape'] ) {
			$this->add_control(
				'identity_image_shape',
				[
					'label' => esc_html__( 'Image Shape', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'circle',
					'options' => [
						'circle' => esc_html__( 'Circle', 'elementor' ),
						'square' => esc_html__( 'Square', 'elementor' ),
					],
					'condition' => $condition,
				]
			);
		}

		$this->add_borders_control(
			'identity_image',
			[
				'condition' => $condition,
			],
			[
				'condition' => $condition,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-profile-border-width: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'condition' => $condition,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-profile-border-color: {{VALUE}};',
				],
			]
		);
	}

	protected function add_identity_image_cover_control( array $condition ): void {
		$this->add_responsive_control(
			'identity_image_height',
			[
				'label' => esc_html__( 'Image Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => $config['style']['identity_section']['cover_image_max'] ?? 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
				],
				'condition' => $condition,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-cover-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_borders_control(
			'identity_image_bottom',
			[
				'condition' => $condition,
				'label' => esc_html__( 'Bottom Border', 'elementor' ),
			],
			[
				'condition' => $condition,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-cover-border-bottom-width: {{SIZE}}{{UNIT}};',
				],
			],
			[
				'condition' => $condition,
				'selectors' => [
					'{{WRAPPER}} .e-link-in-bio' => '--e-link-in-bio-identity-image-cover-border-color: {{VALUE}};',
				],
			]
		);
	}
}
