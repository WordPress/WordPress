<?php

namespace Elementor\Modules\FloatingButtons\Base;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Providers\Social_Network_Provider;
use Elementor\Core\Base\Traits\Shared_Widget_Controls_Trait;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render;
use Elementor\Modules\FloatingButtons\Documents\Floating_Buttons;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

abstract class Widget_Contact_Button_Base extends Widget_Base {

	use Shared_Widget_Controls_Trait;

	const TAB_ADVANCED = 'advanced-tab-floating-buttons';

	public function show_in_panel(): bool {
		return false;
	}

	public function get_group_name(): string {
		return 'floating-buttons';
	}

	public function get_style_depends(): array {
		$widget_name = $this->get_name();

		$style_depends = Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' )
			? parent::get_style_depends()
			: [ 'elementor-icons-fa-solid', 'elementor-icons-fa-brands', 'elementor-icons-fa-regular' ];

		$style_depends[] = 'widget-contact-buttons-base';

		if ( 'contact-buttons' !== $widget_name ) {
			$style_depends[] = "widget-{$widget_name}";
		}

		return $style_depends;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function hide_on_search(): bool {
		return true;
	}

	protected function get_initial_config(): array {
		return array_merge( parent::get_initial_config(), [
			'commonMerged' => true,
		] );
	}

	public static function get_configuration() {
		return [
			'content' => [
				'chat_button_section' => [
					'section_name' => esc_html__( 'Chat Button', 'elementor' ),
					'has_platform' => true,
					'has_icon' => false,
					'icon_default' => [
						'value' => 'far fa-comment-dots',
						'library' => 'fa-regular',
					],
					'icons_recommended' => [
						'fa-regular' => [
							'comment',
							'comment-dots',
							'comment-alt',
						],
						'fa-solid' => [
							'ellipsis-v',
						],
					],
					'has_notification_dot' => true,
					'has_notification_dot_default_enabled' => true,
					'has_active_tab' => false,
					'has_display_text' => false,
					'display_text_label' => esc_html__( 'Call now', 'elementor' ),
					'has_display_text_select' => true,
					'platform' => [
						'group' => [
							Social_Network_Provider::EMAIL,
							Social_Network_Provider::SMS,
							Social_Network_Provider::WHATSAPP,
							Social_Network_Provider::SKYPE,
							Social_Network_Provider::MESSENGER,
							Social_Network_Provider::VIBER,
						],
						'default' => Social_Network_Provider::WHATSAPP,
					],
					'chat_aria_label' => Floating_Buttons::get_title(),
					'defaults' => [
						'mail' => null,
						'mail_subject' => null,
						'mail_body' => null,
						'number' => null,
						'username' => null,
						'location' => [
							'is_external' => true,
						],
						'url' => [
							'is_external' => true,
						],
					],
					'has_accessible_name' => true,
				],
				'top_bar_section' => [
					'section_name' => esc_html__( 'Top Bar', 'elementor' ),
					'has_image' => true,
					'has_active_dot' => true,
					'has_subtitle' => true,
					'title' => [
						'label' => esc_html__( 'Name', 'elementor' ),
						'default' => esc_html__( 'Rob Jones', 'elementor' ),
						'placeholder' => esc_html__( 'Type your name here', 'elementor' ),
						'dynamic' => false,
						'ai' => false,
						'label_block' => false,
					],
					'subtitle' => [
						'label' => esc_html__( 'Title', 'elementor' ),
						'default' => esc_html__( 'Store Manager', 'elementor' ),
						'placeholder' => esc_html__( 'Type your title here', 'elementor' ),
						'dynamic' => false,
						'ai' => false,
						'label_block' => false,
					],
				],
				'message_bubble_section' => [
					'has_typing_animation' => true,
				],
				'contact_section' => [
					'section_name' => esc_html__( 'Contact Buttons', 'elementor' ),
					'has_cta_text' => true,
					'repeater' => [
						'has_tooltip' => false,
						'tooltip_label' => esc_html__( 'Text', 'elementor' ),
						'tooltip_default' => esc_html__( 'Tooltip', 'elementor' ),
						'tooltip_placeholder' => esc_html__( 'Enter icon text', 'elementor' ),
						'has_title' => false,
						'has_description' => false,
					],
					'platform' => [
						'group-1' => [
							Social_Network_Provider::EMAIL,
							Social_Network_Provider::SMS,
							Social_Network_Provider::WHATSAPP,
							Social_Network_Provider::SKYPE,
							Social_Network_Provider::MESSENGER,
							Social_Network_Provider::VIBER,
						],
						'limit' => 5,
						'min_items' => 0,
					],
					'default' => [
						[
							'contact_icon_platform' => Social_Network_Provider::WHATSAPP,
						],
						[
							'contact_icon_platform' => Social_Network_Provider::EMAIL,
						],
						[
							'contact_icon_platform' => Social_Network_Provider::SMS,
						],
						[
							'contact_icon_platform' => Social_Network_Provider::VIBER,
						],
						[
							'contact_icon_platform' => Social_Network_Provider::MESSENGER,
						],
					],
					'has_accessible_name' => true,
				],
				'send_button_section' => [
					'section_name' => esc_html__( 'Send Button', 'elementor' ),
					'has_link' => false,
					'text' => [
						'default' => esc_html__( 'Click to start chat', 'elementor' ),
					],
				],
			],
			'style' => [
				'has_platform_colors' => true,
				'chat_button_section' => [
					'has_entrance_animation' => true,
					'has_box_shadow' => true,
					'has_drop_shadow' => false,
					'has_padding' => false,
					'has_button_size' => true,
					'button_size_default' => 'small',
					'has_typography' => false,
					'has_icon_position' => false,
					'has_icon_spacing' => false,
					'has_tabs' => true,
					'has_platform_color_controls' => false,
					'hover_animation_type' => 'default',
					'icon_color_label' => esc_html__( 'Icon Color', 'elementor' ),
				],
				'top_bar_section' => [
					'has_title_heading' => true,
					'title_heading_label' => esc_html__( 'Name', 'elementor' ),
					'subtitle_heading_label' => esc_html__( 'Title', 'elementor' ),
					'has_style_close_button' => true,
					'has_close_button_heading' => false,
					'has_background' => true,
					'has_background_heading' => false,
				],
				'message_bubble_section' => [
					'has_chat_background' => true,
				],
				'contact_section' => [
					'has_buttons_heading' => true,
					'buttons_heading_label' => esc_html__( 'Buttons', 'elementor' ),
					'has_buttons_size' => true,
					'has_box_shadow' => false,
					'has_buttons_spacing' => false,
					'has_hover_animation' => true,
					'has_chat_box_animation' => false,
					'has_icon_bg_color' => true,
					'has_button_bar' => false,
					'has_tabs' => true,
					'has_text_color' => false,
					'has_bg_color' => false,
					'has_padding' => false,
					'has_button_corners' => false,
					'has_typography' => false,
					'icon_color_label' => esc_html__( 'Icon Color', 'elementor' ),
					'has_hover_transition_duration' => false,
				],
				'send_button_section' => [
					'has_platform_colors' => true,
					'has_icon_color' => true,
					'has_background_color' => true,
					'has_text_color' => false,
					'has_typography' => true,
					'typography_selector' => '{{WRAPPER}} .e-contact-buttons__send-cta',
				],
				'chat_box_section' => [
					'section_name' => esc_html__( 'Chat Box', 'elementor' ),
					'has_width' => true,
					'has_padding' => false,
				],
			],
			'advanced' => [
				'has_layout_position' => true,
				'horizontal_position_default' => 'end',
				'has_mobile_full_width' => false,
				'has_vertical_offset' => true,
				'has_horizontal_offset' => true,
			],
		];
	}

	public function get_icon(): string {
		return 'eicon-commenting-o';
	}

	public function get_categories(): array {
		return [ 'general' ];
	}

	protected function register_controls(): void {

		$this->add_content_tab();

		$this->add_style_tab();

		$this->add_advanced_tab();
	}

	private function social_media_controls(): void {
		$config = static::get_configuration();

		$this->add_control(
			'chat_button_mail',
			[
				'label' => esc_html__( 'Email', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'placeholder' => esc_html__( '@', 'elementor' ),
				'default' => $config['content']['chat_button_section']['defaults']['mail'],
				'condition' => [
					'chat_button_platform' => Social_Network_Provider::EMAIL,
				],
			],
		);

		$this->add_control(
			'chat_button_mail_subject',
			[
				'label' => esc_html__( 'Subject', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'default' => $config['content']['chat_button_section']['defaults']['mail_subject'],
				'condition' => [
					'chat_button_platform' => Social_Network_Provider::EMAIL,
				],
			],
		);

		$this->add_control(
			'chat_button_mail_body',
			[
				'label' => esc_html__( 'Message', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => $config['content']['chat_button_section']['defaults']['mail_body'],
				'condition' => [
					'chat_button_platform' => Social_Network_Provider::EMAIL,
				],
			]
		);

		$this->add_control(
			'chat_button_number',
			[
				'label' => esc_html__( 'Number', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'placeholder' => esc_html__( '+', 'elementor' ),
				'default' => $config['content']['chat_button_section']['defaults']['number'],
				'condition' => [
					'chat_button_platform' => [
						Social_Network_Provider::SMS,
						Social_Network_Provider::WHATSAPP,
						Social_Network_Provider::VIBER,
						Social_Network_Provider::TELEPHONE,
					],
				],
			],
		);

		$this->add_control(
			'chat_button_username',
			[
				'label' => esc_html__( 'Username', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'default' => $config['content']['chat_button_section']['defaults']['username'],
				'condition' => [
					'chat_button_platform' => [
						Social_Network_Provider::SKYPE,
						Social_Network_Provider::MESSENGER,
					],
				],
			],
		);

		$this->add_control(
			'chat_button_viber_action',
			[
				'label' => esc_html__( 'Action', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'chat',
				'options' => [
					'chat' => 'Chat',
					'contact' => 'Contact',
				],
				'condition' => [
					'chat_button_platform' => Social_Network_Provider::VIBER,
				],
			]
		);

		$this->add_control(
			'chat_button_waze',
			[
				'label' => esc_html__( 'Location', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'Paste Waze link', 'elementor' ),
				'default' => $config['content']['chat_button_section']['defaults']['location'],
				'condition' => [
					'chat_button_platform' => [
						Social_Network_Provider::WAZE,
					],
				],
			],
		);

		$this->add_control(
			'chat_button_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'autocomplete' => true,
				'label_block' => true,
				'default' => $config['content']['chat_button_section']['defaults']['url'],
				'condition' => [
					'chat_button_platform' => [
						Social_Network_Provider::URL,
					],
				],
			],
		);
	}

	private function get_display_text_condition( $condition ) {
		$config = static::get_configuration();

		if ( true == $config['content']['chat_button_section']['has_display_text_select'] ) {
			return $condition;
		}

		return null;
	}

	protected function add_chat_button_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'chat_button_section',
			[
				'label' => $config['content']['chat_button_section']['section_name'],
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( $config['content']['chat_button_section']['has_accessible_name'] ) {
			$this->add_control(
				'chat_aria_label',
				[
					'label' => esc_html__( 'Accessible name', 'elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => $config['content']['chat_button_section']['chat_aria_label'],
					'placeholder' => esc_html__( 'Add accessible name', 'elementor' ),
					'dynamic' => [
						'active' => true,
					],
				],
			);
		}

		if ( $config['content']['chat_button_section']['has_platform'] ) {

			$this->add_control(
				'chat_button_platform',
				[
					'label' => esc_html__( 'Platform', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $config['content']['chat_button_section']['platform']['default'],
					'options' => Social_Network_Provider::get_social_networks_text(
						$config['content']['chat_button_section']['platform']['group']
					),
				]
			);

			$this->social_media_controls();
		}

		if ( $config['content']['chat_button_section']['has_icon'] ) {
			$this->add_control(
				'chat_button_icon',
				[
					'label' => esc_html__( 'Icon', 'elementor' ),
					'type' => Controls_Manager::ICONS,
					'default' => $config['content']['chat_button_section']['icon_default'],
					'recommended' => $config['content']['chat_button_section']['icons_recommended'],
				]
			);
		}

		if ( $config['content']['chat_button_section']['has_notification_dot'] ) {

			$notification_dot_return_value = 'yes';
			$notification_dot_default = $notification_dot_return_value;

			// Only clear if explicitly passed
			if ( false === $config['content']['chat_button_section']['has_notification_dot_default_enabled'] ) {
				$notification_dot_default = '';
			}

			$this->add_control(
				'chat_button_show_dot',
				[
					'label' => esc_html__( 'Notification Dot', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'elementor' ),
					'label_off' => esc_html__( 'Hide', 'elementor' ),
					'return_value' => $notification_dot_return_value,
					'default' => $notification_dot_default,
				]
			);
		}

		if ( $config['content']['chat_button_section']['has_display_text'] ) {

			if ( $config['content']['chat_button_section']['has_display_text_select'] ) {
				$this->add_control(
					'chat_button_display_text_select',
					[
						'label' => esc_html__( 'Display Text', 'elementor' ),
						'type'  => Controls_Manager::SELECT,
						'default' => 'details',
						'options' => [
							'details' => esc_html__( 'Contact Details', 'elementor' ),
							'cta' => esc_html__( 'Call to Action', 'elementor' ),
						],
					]
				);
			}

			$this->add_control(
				'chat_button_display_text',
				[
					'label' => esc_html__( 'Call to Action Text', 'elementor' ),
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'label_block' => true,
					'placeholder' => esc_html__( 'Enter the text', 'elementor' ),
					'default' => $config['content']['chat_button_section']['display_text_label'],
					'condition' => $this->get_display_text_condition([
						'chat_button_display_text_select' => 'cta',
					] ),
				],
			);
		}

		$this->end_controls_section();
	}

	protected function add_top_bar_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'top_bar_section',
			[
				'label' => $config['content']['top_bar_section']['section_name'],
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'top_bar_title',
			[
				'label' => $config['content']['top_bar_section']['title']['label'],
				'type' => Controls_Manager::TEXT,
				'default' => $config['content']['top_bar_section']['title']['default'],
				'placeholder' => $config['content']['top_bar_section']['title']['placeholder'],
				'dynamic' => [
					'active' => $config['content']['top_bar_section']['title']['dynamic'],
				],
				'ai' => [
					'active' => $config['content']['top_bar_section']['title']['ai'],
				],
				'label_block' => $config['content']['top_bar_section']['title']['label_block'],
			]
		);

		if ( $config['content']['top_bar_section']['has_subtitle'] ) {
			$this->add_control(
				'top_bar_subtitle',
				[
					'label' => $config['content']['top_bar_section']['subtitle']['label'],
					'type' => Controls_Manager::TEXT,
					'default' => $config['content']['top_bar_section']['subtitle']['default'],
					'placeholder' => $config['content']['top_bar_section']['subtitle']['placeholder'],
					$config['content']['top_bar_section']['subtitle']['dynamic'],
					'ai' => [
						'active' => $config['content']['top_bar_section']['subtitle']['ai'],
					],
					'label_block' => $config['content']['top_bar_section']['title']['label_block'],
				]
			);
		}

		if ( $config['content']['top_bar_section']['has_image'] ) {
			$this->add_control(
				'top_bar_image',
				[
					'label' => esc_html__( 'Profile Image', 'elementor' ),
					'type' => Controls_Manager::MEDIA,
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);
		}

		if ( $config['content']['top_bar_section']['has_active_dot'] ) {
			$this->add_control(
				'top_bar_show_dot',
				[
					'label' => esc_html__( 'Active Dot', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'elementor' ),
					'label_off' => esc_html__( 'Hide', 'elementor' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_message_bubble_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'message_bubble_section',
			[
				'label' => esc_html__( 'Message Bubble', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'message_bubble_name',
			[
				'label' => esc_html__( 'Name', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Rob', 'elementor' ),
				'placeholder' => esc_html__( 'Type your name here', 'elementor' ),
			]
		);

		$this->add_control(
			'message_bubble_body',
			[
				'label' => esc_html__( 'Message', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'default' => esc_html__( 'Hey, how can I help you today?', 'elementor' ),
				'placeholder' => esc_html__( 'Message', 'elementor' ),
			],
		);

		$this->add_control(
			'chat_button_time_format',
			[
				'label' => esc_html__( 'Time format', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '12h',
				'options' => [
					'12h' => esc_html__( '2:20 PM', 'elementor' ),
					'24h' => esc_html__( '14:20', 'elementor' ),
				],
			]
		);

		if ( $config['content']['message_bubble_section']['has_typing_animation'] ) {
			$this->add_control(
				'chat_button_show_animation',
				[
					'label' => esc_html__( 'Typing Animation', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'elementor' ),
					'label_off' => esc_html__( 'Hide', 'elementor' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_contact_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'contact_section',
			[
				'label' => $config['content']['contact_section']['section_name'],
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( $config['content']['contact_section']['has_accessible_name'] ) {
			$this->add_control(
				'contact_aria_label',
				[
					'label' => esc_html__( 'Accessible name', 'elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => $config['content']['chat_button_section']['chat_aria_label'],
					'placeholder' => esc_html__( 'Add accessible name', 'elementor' ),
					'dynamic' => [
						'active' => true,
					],
				],
			);
		}

		if ( $config['content']['contact_section']['has_cta_text'] ) {
			$this->add_control(
				'contact_cta_text',
				[
					'label' => esc_html__( 'Call to Action Text', 'elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Start conversation:', 'elementor' ),
					'placeholder' => esc_html__( 'Type your text here', 'elementor' ),
					'label_block' => true,
				]
			);
		}

		if ( $config['content']['contact_section']['platform']['limit'] ) {
			if ( $config['content']['contact_section']['platform']['min_items'] ) {
				$this->add_control(
					'contact_custom_panel_alert',
					[
						'type' => Controls_Manager::ALERT,
						'alert_type' => 'info',
						'content' => sprintf(
							/* translators: 1: Minimum items, 2: Items limit. */
							esc_html__( 'Add between %1$s to %2$s contact buttons', 'elementor' ),
							'<b>' . $config['content']['contact_section']['platform']['min_items'] . '</b>',
							'<b>' . $config['content']['contact_section']['platform']['limit'] . '</b>'
						),
					]
				);
			} else {
				$this->add_control(
					'contact_custom_panel_alert',
					[
						'type' => Controls_Manager::ALERT,
						'alert_type' => 'info',
						'content' => sprintf(
							/* translators: %s: Items limit. */
							esc_html__( 'Add up to %s contact buttons', 'elementor' ),
							'<b>' . $config['content']['contact_section']['platform']['limit'] . '</b>'
						),
					]
				);
			}
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'contact_icon_platform',
			[
				'label' => esc_html__( 'Platform', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => Social_Network_Provider::get_social_networks_text(
					$config['content']['contact_section']['platform']['group-1']
				),
				'default' => Social_Network_Provider::WHATSAPP,
			],
		);

		if ( $config['content']['contact_section']['repeater']['has_tooltip'] ) {
			$repeater->add_control(
				'contact_tooltip',
				[
					'label' => $config['content']['contact_section']['repeater']['tooltip_label'],
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => $config['content']['contact_section']['repeater']['tooltip_default'],
					'placeholder' => $config['content']['contact_section']['repeater']['tooltip_placeholder'],
				],
			);
		}

		if ( $config['content']['contact_section']['repeater']['has_title'] ) {
			$repeater->add_control(
				'contact_title',
				[
					'label' => 'Title',
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => 'Title',
					'placeholder' => esc_html__( 'Enter title', 'elementor' ),
				],
			);
		}

		if ( $config['content']['contact_section']['repeater']['has_description'] ) {
			$repeater->add_control(
				'contact_description',
				[
					'label' => 'Description',
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => 'Description',
					'placeholder' => esc_html__( 'Enter description', 'elementor' ),
				],
			);
		}

		$repeater->add_control(
			'contact_icon_mail',
			[
				'label' => esc_html__( 'Email', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your email', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::EMAIL,
					],
				],
			],
		);

		$repeater->add_control(
			'contact_icon_mail_subject',
			[
				'label' => esc_html__( 'Subject', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Subject', 'elementor' ),
				'label_block' => true,
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::EMAIL,
					],
				],
			]
		);

		$repeater->add_control(
			'contact_icon_mail_body',
			[
				'label' => esc_html__( 'Message', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Message', 'elementor' ),
				'label_block' => true,
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::EMAIL,
					],
				],
			]
		);

		$repeater->add_control(
			'contact_icon_number',
			[
				'label' => esc_html__( 'Number', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'placeholder' => esc_html__( '+', 'elementor' ),
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::SMS,
						Social_Network_Provider::WHATSAPP,
						Social_Network_Provider::VIBER,
						Social_Network_Provider::TELEPHONE,
					],
				],
			],
		);

		$repeater->add_control(
			'contact_icon_username',
			[
				'label' => esc_html__( 'Username', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter your username', 'elementor' ),
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::MESSENGER,
						Social_Network_Provider::SKYPE,
					],
				],
			],
		);

		$repeater->add_control(
			'contact_icon_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'autocomplete' => true,
				'label_block' => true,
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::URL,
					],
				],
				'default' => [
					'is_external' => true,
				],
				'placeholder' => esc_html__( 'Paste URL or type', 'elementor' ),
			],
		);

		$repeater->add_control(
			'contact_icon_waze',
			[
				'label' => esc_html__( 'Location', 'elementor' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => true,
				],
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'Paste Waze link', 'elementor' ),
				'condition' => [
					'contact_icon_platform' => [
						Social_Network_Provider::WAZE,
					],
				],
				'ai' => [
					'active' => false,
				],
			],
		);

		$repeater->add_control(
			'contact_icon_viber_action',
			[
				'label' => esc_html__( 'Action', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'chat',
				'dynamic' => [
					'active' => true,
				],
				'options' => [
					'chat' => 'Chat',
					'contact' => 'Contact',
				],
				'condition' => [
					'contact_icon_platform' => Social_Network_Provider::VIBER,
				],
			]
		);

		$this->add_control(
			'contact_repeater',
			[
				'max_items' => $config['content']['contact_section']['platform']['limit'],
				'min_items' => $config['content']['contact_section']['platform']['min_items'],
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => $this->get_icon_title_field(),
				'prevent_empty' => true,
				'button_text' => esc_html__( 'Add Item', 'elementor' ),
				'default' => $config['content']['contact_section']['default'],
			]
		);

		$this->end_controls_section();
	}

	protected function get_icon_title_field(): string {
		$platform_icons_js = json_encode( Social_Network_Provider::get_social_networks_icons() );
		$platform_text_js = json_encode( Social_Network_Provider::get_social_networks_text() );

		return <<<JS
	<#
	elementor.helpers.enqueueIconFonts( 'fa-solid' );
	elementor.helpers.enqueueIconFonts( 'fa-brands' );
	const mapping = {$platform_icons_js};
	const text_mapping = {$platform_text_js};
	#>
	<i class='{{{ mapping[contact_icon_platform] }}}' ></i> {{{ text_mapping[contact_icon_platform] }}}
JS;
	}

	protected function add_send_button_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'send_button_section',
			[
				'label' => $config['content']['send_button_section']['section_name'],
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'send_button_text',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => $config['content']['send_button_section']['text']['default'],
				'placeholder' => esc_html__( 'Type your text here', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		if ( $config['content']['send_button_section']['has_link'] ) {
			$this->add_control(
				'send_button_url',
				[
					'label' => esc_html__( 'Link', 'elementor' ),
					'type' => Controls_Manager::URL,
					'default' => [
						'is_external' => true,
					],
					'dynamic' => [
						'active' => true,
					],
					'ai' => [
						'active' => false,
					],
					'autocomplete' => true,
					'label_block' => true,
				],
			);
		}

		$this->end_controls_section();
	}

	protected function add_content_tab(): void {
		$this->add_chat_button_section();

		$this->add_top_bar_section();

		$this->add_message_bubble_section();

		$this->add_send_button_section();
	}

	private function get_platform_color_condition( $condition ) {
		$config = static::get_configuration();

		if ( true == $config['style']['has_platform_colors'] ) {
			return $condition;
		}

		return null;
	}

	protected function add_style_chat_button_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_chat_button',
			[
				'label' => $config['content']['chat_button_section']['section_name'],
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['style']['chat_button_section']['has_button_size'] ) {
			$this->add_control(
				'style_chat_button_size',
				[
					'label' => esc_html__( 'Size', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $config['style']['chat_button_section']['button_size_default'],
					'options' => [
						'small' => esc_html__( 'Small', 'elementor' ),
						'medium' => esc_html__( 'Medium', 'elementor' ),
						'large' => esc_html__( 'Large', 'elementor' ),
					],
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_icon_position'] ) {
			$this->add_responsive_control(
				'style_chat_button_horizontal_position',
				[
					'label' => esc_html__( 'Icon Position', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'start' => [
							'title' => esc_html__( 'Left', 'elementor' ),
							'icon' => 'eicon-h-align-left',
						],
						'end' => [
							'title' => esc_html__( 'Right', 'elementor' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons__chat-button svg' => 'order: {{VALUE}};',
					],
					'selectors_dictionary' => [
						'start' => '-1',
						'end' => '2',
					],
					'default' => 'start',
					'mobile_default' => 'start',
					'toggle' => true,
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_icon_spacing'] ) {
			$this->add_responsive_control(
				'style_chat_button_spacing',
				[
					'label' => esc_html__( 'Icon Spacing', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'%' => [
							'min' => 10,
							'max' => 100,
						],
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-chat-button-gap: {{SIZE}}{{UNIT}}',
					],
					'separator' => 'before',
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_typography'] ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'style_top_bar_title_typography',
					'selector' => '{{WRAPPER}} .e-contact-buttons__chat-button',
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_tabs'] ) {
			$this->start_controls_tabs(
				'style_button_color_tabs'
			);

			$this->start_controls_tab(
				'style_button_color_tabs_normal',
				[
					'label' => esc_html__( 'Normal', 'elementor' ),
				]
			);

			if ( $config['style']['has_platform_colors'] ) {
				$this->add_control(
					'style_button_color_select',
					[
						'label' => esc_html__( 'Colors', 'elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'default',
						'options' => [
							'default' => esc_html__( 'Default', 'elementor' ),
							'custom' => esc_html__( 'Custom', 'elementor' ),
						],
					]
				);
			}

			$this->add_control(
				'style_button_color_icon',
				[
					'label' => $config['style']['chat_button_section']['icon_color_label'],
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-icon: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_button_color_select' => 'custom',
					] ),
				]
			);

			$this->add_control(
				'style_button_color_background',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-bg: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_button_color_select' => 'custom',
					] ),
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'style_button_color_tabs_hover',
				[
					'label' => esc_html__( 'Hover', 'elementor' ),
				]
			);

			if ( $config['style']['has_platform_colors'] ) {
				$this->add_control(
					'style_button_color_select_hover',
					[
						'label' => esc_html__( 'Colors', 'elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'default',
						'options' => [
							'default' => esc_html__( 'Default', 'elementor' ),
							'custom' => esc_html__( 'Custom', 'elementor' ),
						],
					]
				);
			}

			$this->add_control(
				'style_button_color_icon_hover',
				[
					'label' => $config['style']['chat_button_section']['icon_color_label'],
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-icon-hover: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_button_color_select_hover' => 'custom',
					] ),
				]
			);

			$this->add_control(
				'style_button_color_background_hover',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-bg-hover: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_button_color_select_hover' => 'custom',
					] ),
				]
			);

			if ( 'default' == $config['style']['chat_button_section']['hover_animation_type'] ) {
				$this->add_hover_animation_control(
					'style_button_color_hover_animation',
				);
			}

			$this->end_controls_tab();

			if ( $config['content']['chat_button_section']['has_active_tab'] ) {
				$this->start_controls_tab(
					'style_button_color_tabs_active',
					[
						'label' => esc_html__( 'Active', 'elementor' ),
					]
				);

				$this->add_control(
					'style_button_color_icon_active',
					[
						'label' => esc_html__( 'Icon Color', 'elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-active-button-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'style_button_color_background_active',
					[
						'label' => esc_html__( 'Background Color', 'elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-active-button-bg: {{VALUE}}',
						],
					]
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();
		}

		if ( $config['style']['chat_button_section']['has_platform_color_controls'] ) {
			$this->add_control(
				'style_platform_control_select',
				[
					'label' => esc_html__( 'Colors', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'elementor' ),
						'custom' => esc_html__( 'Custom', 'elementor' ),
					],
					'separator' => 'before',
				]
			);

			$this->add_control(
				'style_button_color_icon',
				[
					'label' => $config['style']['chat_button_section']['icon_color_label'],
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-icon: {{VALUE}}',
					],
					'condition' => [
						'style_platform_control_select' => 'custom',
					],
				]
			);

			$this->add_control(
				'style_button_color_background',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-bg: {{VALUE}}',
					],
					'condition' => [
						'style_platform_control_select' => 'custom',
					],
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_entrance_animation'] ) {
			$this->add_responsive_control(
				'style_chat_button_animation',
				[
					'label' => esc_html__( 'Entrance Animation', 'elementor' ),
					'type' => Controls_Manager::ANIMATION,
					'frontend_available' => true,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'style_chat_button_animation_duration',
				[
					'label' => esc_html__( 'Animation Duration', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'normal',
					'options' => [
						'slow' => esc_html__( 'Slow', 'elementor' ),
						'normal' => esc_html__( 'Normal', 'elementor' ),
						'fast' => esc_html__( 'Fast', 'elementor' ),
					],
					'prefix_class' => 'animated-',
				]
			);

			$this->add_control(
				'style_chat_button_animation_delay',
				[
					'label' => esc_html__( 'Animation Delay', 'elementor' ) . ' (ms)',
					'type' => Controls_Manager::NUMBER,
					'min' => 0,
					'step' => 100,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-button-chat-button-animation-delay: {{SIZE}}ms;',
					],
					'render_type' => 'none',
					'frontend_available' => true,
					'separator' => 'after',
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_box_shadow'] ) {
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'style_chat_button_box_shadow',
					'selector' => '{{WRAPPER}} .e-contact-buttons__chat-button-shadow',
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_drop_shadow'] ) {
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'style_chat_button_drop_shadow',
					'fields_options' => [
						'box_shadow' => [
							'selectors' => [
								'{{WRAPPER}} .e-contact-buttons__chat-button-drop-shadow' => 'filter: drop-shadow({{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}});',
							],
						],
					],
				]
			);
		}

		if ( $config['style']['chat_button_section']['has_padding'] ) {
			$this->add_responsive_control(
				'style_chat_button_padding',
				[
					'label' => esc_html__( 'Padding', 'elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-chat-button-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-contact-buttons-chat-button-padding-block-start: {{TOP}}{{UNIT}}; --e-contact-buttons-chat-button-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-contact-buttons-chat-button-padding-inline-start: {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
		}

		if ( 'custom' == $config['style']['chat_button_section']['hover_animation_type'] ) {
			$this->add_control(
				'style_chat_button_custom_animation_heading',
				[
					'label' => esc_html__( 'Hover Animation', 'elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'style_chat_button_custom_animation_alert',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => __( 'Hover animation is <b>desktop only</b>', 'elementor' ),
				]
			);

			$this->add_control(
				'style_chat_button_custom_animation_transition',
				[
					'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (s)',
					'type' => Controls_Manager::SLIDER,
					'range' => [
						's' => [
							'min' => 0,
							'max' => 3,
							'step' => 0.1,
						],
					],
					'default' => [
						'unit' => 's',
						'size' => 0.3,
					],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-transition-duration: {{SIZE}}{{UNIT}}',
					],
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_style_top_bar_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_top_bar_section',
			[
				'label' => $config['content']['top_bar_section']['section_name'],
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['content']['top_bar_section']['has_image'] ) {
			$this->add_control(
				'style_top_bar_profile_heading',
				[
					'label' => esc_html__( 'Profile Image', 'elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'style_top_bar_image_size',
				[
					'label' => esc_html__( 'Size', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'medium',
					'options' => [
						'small' => esc_html__( 'Small', 'elementor' ),
						'medium' => esc_html__( 'Medium', 'elementor' ),
						'large' => esc_html__( 'Large', 'elementor' ),
					],
				]
			);
		}

		if ( $config['style']['has_platform_colors'] ) {
			$this->add_control(
				'style_top_bar_colors',
				[
					'label' => esc_html__( 'Colors', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'elementor' ),
						'custom' => esc_html__( 'Custom', 'elementor' ),
					],
					'separator' => 'before',
				]
			);
		}

		if ( $config['style']['top_bar_section']['has_title_heading'] ) {
			$this->add_control(
				'style_top_bar_title_heading',
				[
					'label' => $config['style']['top_bar_section']['title_heading_label'],
					'type' => Controls_Manager::HEADING,
					'separator' => ! $config['style']['has_platform_colors'] ? 'before' : false,
				]
			);
		}

		$this->add_control(
			'style_top_bar_title_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-top-bar-title: {{VALUE}}',
				],
				'condition' => $this->get_platform_color_condition( [
					'style_top_bar_colors' => 'custom',
				] ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_top_bar_title_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__top-bar-title',
			]
		);

		if ( $config['content']['top_bar_section']['has_subtitle'] ) {
			$this->add_control(
				'style_top_bar_subtitle_heading',
				[
					'label' => $config['style']['top_bar_section']['subtitle_heading_label'],
					'type' => Controls_Manager::HEADING,
					'separator' => false,
				]
			);

			$this->add_control(
				'style_top_bar_subtitle_color',
				[
					'label' => esc_html__( 'Text Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-top-bar-subtitle: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_top_bar_colors' => 'custom',
					] ),
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'style_top_bar_subtitle_typography',
					'selector' => '{{WRAPPER}} .e-contact-buttons__top-bar-subtitle',
				]
			);
		}

		$close_and_background_partial_divider = 'before';

		if ( $config['style']['top_bar_section']['has_style_close_button'] ) {

			if ( $config['style']['top_bar_section']['has_close_button_heading'] ) {
				$this->add_control(
					'style_top_bar_close_button_heading',
					[
						'label' => esc_html__( 'Close Button', 'elementor' ),
						'type' => Controls_Manager::HEADING,
						'separator' => $close_and_background_partial_divider,
						'condition' => $this->get_platform_color_condition( [
							'style_top_bar_colors' => 'custom',
						] ),
					]
				);
				$close_and_background_partial_divider = false;
			}

			$this->add_control(
				'style_top_bar_close_button_color',
				[
					'label' => esc_html__( 'Close Button Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-close-button-color: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_top_bar_colors' => 'custom',
					] ),
					'separator' => $close_and_background_partial_divider,
				]
			);

			$close_and_background_partial_divider = false;
		}

		if ( $config['style']['top_bar_section']['has_background'] ) {

			if ( $config['style']['top_bar_section']['has_background_heading'] ) {
				$this->add_control(
					'style_top_bar_background_heading',
					[
						'label' => esc_html__( 'Background', 'elementor' ),
						'type' => Controls_Manager::HEADING,
						'separator' => $close_and_background_partial_divider,
						'condition' => $this->get_platform_color_condition( [
							'style_top_bar_colors' => 'custom',
						] ),
					]
				);
				$close_and_background_partial_divider = false;
			}

			$this->add_control(
				'style_top_bar_background_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-top-bar-bg: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_top_bar_colors' => 'custom',
					] ),
					'separator' => $close_and_background_partial_divider,
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_style_message_bubble_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_bubble_section',
			[
				'label' => esc_html__( 'Message Bubble', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['style']['has_platform_colors'] ) {
			$this->add_control(
				'style_bubble_colors',
				[
					'label' => esc_html__( 'Colors', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'elementor' ),
						'custom' => esc_html__( 'Custom', 'elementor' ),
					],
				]
			);
		}

		$this->add_control(
			'style_bubble_name_heading',
			[
				'label' => esc_html__( 'Name', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => false,
			]
		);

		$this->add_control(
			'style_bubble_name_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-message-bubble-name: {{VALUE}}',
				],
				'condition' => $this->get_platform_color_condition( [
					'style_bubble_colors' => 'custom',
				] ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_bubble_name_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__message-bubble-name',
			]
		);

		$this->add_control(
			'style_bubble_message_heading',
			[
				'label' => esc_html__( 'Message', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => false,
			]
		);

		$this->add_control(
			'style_bubble_message_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-message-bubble-body: {{VALUE}}',
				],
				'condition' => $this->get_platform_color_condition( [
					'style_bubble_colors' => 'custom',
				] ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_bubble_message_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__message-bubble-body',
			]
		);

		$this->add_control(
			'style_bubble_time_heading',
			[
				'label' => esc_html__( 'Time', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => false,
			]
		);

		$this->add_control(
			'style_bubble_time_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-message-bubble-time: {{VALUE}}',
				],
				'condition' => $this->get_platform_color_condition( [
					'style_bubble_colors' => 'custom',
				] ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_bubble_time_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__message-bubble-time',
			]
		);

		$this->add_control(
			'style_bubble_background_color',
			[
				'label' => esc_html__( 'Bubble Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-message-bubble-bubble-bg: {{VALUE}}',
				],
				'condition' => $this->get_platform_color_condition( [
					'style_bubble_colors' => 'custom',
				] ),
				'separator' => 'before',
			]
		);

		if ( $config['style']['message_bubble_section']['has_chat_background'] ) {
			$this->add_control(
				'style_bubble_chat_color',
				[
					'label' => esc_html__( 'Chat Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-message-bubble-chat-bg: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_bubble_colors' => 'custom',
					] ),
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_style_contact_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_contact_section',
			[
				'label' => $config['content']['contact_section']['section_name'],
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['content']['contact_section']['has_cta_text'] ) {
			$this->add_control(
				'style_contact_text_heading',
				[
					'label' => esc_html__( 'Call to Action Text', 'elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => false,
					'condition' => $this->get_platform_color_condition( [
						'style_bubble_colors' => 'custom',
					] ),
				]
			);

			$this->add_control(
				'style_contact_text_color',
				[
					'label' => esc_html__( 'Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-text: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'style_contact_text_typography',
					'selector' => '{{WRAPPER}} .e-contact-buttons__contact-text',
				]
			);
		}

		if ( $config['style']['contact_section']['has_buttons_heading'] ) {
			$this->add_control(
				'style_contact_buttons_heading',
				[
					'label' => $config['style']['contact_section']['buttons_heading_label'],
					'type' => Controls_Manager::HEADING,
					'separator' => false,
					'condition' => $this->get_platform_color_condition( [
						'style_bubble_colors' => 'custom',
					] ),
				]
			);
		}

		if ( $config['style']['contact_section']['has_buttons_size'] ) {
			$this->add_control(
				'style_contact_button_size',
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
		}

		if ( $config['style']['contact_section']['has_text_color'] ) {
			$this->add_control(
				'style_contact_button_text_color',
				[
					'label' => $config['style']['contact_section']['icon_color_label'],
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-icon: {{VALUE}}',
					],
				]
			);
		}

		if ( $config['style']['contact_section']['has_typography'] ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'style_contact_typography',
					'selector' => '{{WRAPPER}} .e-contact-buttons__contact-icon-link',
				]
			);
		}

		if ( $config['style']['contact_section']['has_bg_color'] ) {
			$this->add_control(
				'style_contact_button_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-bg: {{VALUE}}',
					],
				]
			);
		}

		if ( $config['style']['contact_section']['has_tabs'] ) {
			$this->start_controls_tabs(
				'style_contact_button_color_tabs'
			);

			$this->start_controls_tab(
				'style_contact_button_color_tabs_normal',
				[
					'label' => esc_html__( 'Normal', 'elementor' ),
				]
			);

			$this->add_control(
				'style_contact_button_color_icon',
				[
					'label' => $config['style']['contact_section']['icon_color_label'],
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-icon: {{VALUE}}',
					],
				]
			);

			if ( $config['style']['contact_section']['has_icon_bg_color'] ) {
				$this->add_control(
					'style_contact_button_color_background',
					[
						'label' => esc_html__( 'Background Color', 'elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-bg: {{VALUE}}',
						],
					]
				);
			}

			$this->end_controls_tab();

			$this->start_controls_tab(
				'style_contact_button_color_tabs_hover',
				[
					'label' => esc_html__( 'Hover', 'elementor' ),
				]
			);

			$this->add_control(
				'style_contact_button_color_icon_hover',
				[
					'label' => esc_html__( 'Icon Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-icon-hover: {{VALUE}}',
					],
				]
			);

			if ( $config['style']['contact_section']['has_icon_bg_color'] ) {
				$this->add_control(
					'style_contact_button_color_background_hover',
					[
						'label' => esc_html__( 'Background Color', 'elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-bg-hover: {{VALUE}}',
						],
					]
				);
			}

			if ( $config['style']['contact_section']['has_hover_animation'] ) {
				$this->add_hover_animation_control(
					'style_contact_button_hover_animation',
				);
			}

			$this->end_controls_tab();

			$this->end_controls_tabs();
		}

		if ( $config['style']['contact_section']['has_buttons_spacing'] ) {

			$this->add_responsive_control(
				'style_contact_buttons_spacing',
				[
					'label' => esc_html__( 'Buttons Spacing', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'%' => [
							'min' => 10,
							'max' => 100,
						],
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-gap: {{SIZE}}{{UNIT}}',
					],
					'separator' => 'before',
				]
			);
		}

		if ( $config['style']['contact_section']['has_button_corners'] ) {
			$this->add_control(
				'style_contact_corners',
				[
					'label' => esc_html__( 'Corners', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'round',
					'options' => [
						'round' => esc_html__( 'Round', 'elementor' ),
						'rounded' => esc_html__( 'Rounded', 'elementor' ),
						'sharp' => esc_html__( 'Sharp', 'elementor' ),
					],
				]
			);
		}

		if ( $config['style']['contact_section']['has_box_shadow'] ) {
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'style_contact_icons_box_shadow',
					'selector' => '{{WRAPPER}} .e-contact-buttons__contact-box-shadow',
				]
			);
		}

		if ( $config['content']['contact_section']['repeater']['has_tooltip'] ) {
			$this->add_control(
				'style_contact_tooltip_heading',
				[
					'label' => esc_html__( 'Tooltips', 'elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'style_contact_tooltip_text_color',
				[
					'label' => esc_html__( 'Text Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-tooltip-text: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'style_contact_tooltip_typography',
					'selector' => '{{WRAPPER}} .e-contact-buttons__contact-tooltip',
				]
			);

			$this->add_control(
				'style_contact_tooltip_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-tooltip-bg: {{VALUE}}',
					],
				]
			);
		}

		if ( $config['style']['contact_section']['has_chat_box_animation'] ) {
			$this->chat_box_animation_controls();
		}

		if ( $config['style']['contact_section']['has_button_bar'] ) {
			$this->add_control(
				'style_contact_button_bar_heading',
				[
					'label' => esc_html__( 'Button Bar', 'elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'style_contact_button_bar_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-bar-bg: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'style_contact_button_bar_corners',
				[
					'label' => esc_html__( 'Corners', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'round',
					'options' => [
						'round' => esc_html__( 'Round', 'elementor' ),
						'rounded' => esc_html__( 'Rounded', 'elementor' ),
						'sharp' => esc_html__( 'Sharp', 'elementor' ),
					],
				]
			);

			$this->add_responsive_control(
				'style_contact_button_bar_padding',
				[
					'label' => esc_html__( 'Padding', 'elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-button-bar-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-contact-buttons-button-bar-padding-block-start: {{TOP}}{{UNIT}}; --e-contact-buttons-button-bar-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-contact-buttons-button-bar-padding-inline-start: {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
		}

		if ( $config['style']['contact_section']['has_padding'] ) {
			$this->add_responsive_control(
				'style_contact_padding',
				[
					'label' => esc_html__( 'Padding', 'elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-contact-buttons-contact-padding-block-start: {{TOP}}{{UNIT}}; --e-contact-buttons-contact-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-contact-buttons-contact-padding-inline-start: {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
		}

		if ( $config['style']['contact_section']['has_hover_transition_duration'] ) {
			$this->add_control(
				'style_contact_custom_animation_heading',
				[
					'label' => esc_html__( 'Animation', 'elementor' ),
					'type' => Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'style_contact_custom_animation_alert',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => __( 'Adjust transition duration to change the speed of the <b>hover animation on desktop</b> and the <b>click animation on touchscreen</b>.', 'elementor' ),
				]
			);

			$this->add_control(
				'style_contact_custom_animation_transition',
				[
					'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (s)',
					'type' => Controls_Manager::SLIDER,
					'range' => [
						's' => [
							'min' => 0,
							'max' => 3,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-transition-duration: {{SIZE}}{{UNIT}}',
					],
				]
			);
		}

		$this->end_controls_section();
	}

	protected function add_style_resource_links_section(): void {
		$this->start_controls_section(
			'style_resource_links_section',
			[
				'label' => esc_html__( 'Resource Links', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style_resource_links_icons_heading',
			[
				'label' => esc_html__( 'Icons', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => false,
			]
		);

		$this->add_control(
			'style_resource_links_button_size',
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

		$this->add_control(
			'style_resource_links_color_select',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'default' => esc_html__( 'Default', 'elementor' ),
					'custom'  => esc_html__( 'Custom', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'style_contact_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-icon: {{VALUE}}',
				],
				'condition' => [
					'style_resource_links_color_select' => 'custom',
				],
			]
		);

		$this->add_control(
			'style_resource_links_title_heading',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => false,
			]
		);

		$this->add_control(
			'style_resource_links_title_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-title-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_resource_links_title_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__contact-title',
			]
		);

		$this->add_control(
			'style_resource_links_description_heading',
			[
				'label' => esc_html__( 'Description', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => false,
			]
		);

		$this->add_control(
			'style_resource_links_description_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-description-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_resource_links_description_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__contact-description',
			]
		);

		$this->add_control(
			'style_resource_links_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-contact-button-bg: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_hover_animation_control(
			'style_resource_links_hover_animation',
		);

		$this->end_controls_section();
	}

	protected function add_style_info_links_section(): void {
		$this->start_controls_section(
			'style_info_links_section',
			[
				'label' => esc_html__( 'Info Links', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style_info_links_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'start',
				'toggle' => true,
			]
		);

		$this->add_control(
			'style_info_links_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-icon-link-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'style_info_links_link_spacing',
			[
				'label' => esc_html__( 'Link Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-icon-link-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_info_links_typography',
				'selector' => '{{WRAPPER}} .e-contact-buttons__contact-icon-link',
			]
		);

		$this->start_controls_tabs(
			'style_info_links_tabs'
		);

		$this->start_controls_tab(
			'style_info_links_tabs_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'style_info_links_normal_text_color',
			[
				'label' => esc_html__( 'Text and Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-icon-link-text-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_info_links_tabs_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'style_info_links_hover_text_color',
			[
				'label'     => esc_html__( 'Text and Icon Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-icon-link-text-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'style_info_links_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'style_info_links_dividers',
			[
				'label' => esc_html__( 'Dividers', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'style_info_links_divider_color',
			[
				'label'     => esc_html__( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-icon-link-divider-color: {{VALUE}}',
				],
				'condition' => [
					'style_info_links_dividers' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'style_info_links_divider_weight',
			[
				'label' => esc_html__( 'Weight', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-icon-link-divider-weight: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'style_info_links_dividers' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_send_button_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_send_section',
			[
				'label' => $config['content']['send_button_section']['section_name'],
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['style']['send_button_section']['has_typography'] ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'style_send_typography',
					'selector' => $config['style']['send_button_section']['typography_selector'],
				]
			);
		}

		$this->start_controls_tabs(
			'style_send_tabs'
		);

		$this->start_controls_tab(
			'style_send_tabs_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		if ( $config['style']['send_button_section']['has_platform_colors'] ) {
			$this->add_control(
				'style_send_normal_colors',
				[
					'label' => esc_html__( 'Colors', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'elementor' ),
						'custom'  => esc_html__( 'Custom', 'elementor' ),
					],
				]
			);
		}

		if ( $config['style']['send_button_section']['has_icon_color'] ) {
			$this->add_control(
				'style_send_normal_icon_color',
				[
					'label' => esc_html__( 'Icon Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-icon: {{VALUE}}',
					],
					'condition' => [
						'style_send_normal_colors' => 'custom',
					],
				]
			);
		}

		if ( $config['style']['send_button_section']['has_text_color'] ) {
			$this->add_control(
				'style_send_normal_text_color',
				[
					'label' => esc_html__( 'Text Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-text: {{VALUE}}',
					],
				]
			);
		}

		if ( $config['style']['send_button_section']['has_background_color'] ) {
			$this->add_control(
				'style_send_normal_background_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-bg: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_send_normal_colors' => 'custom',
					] ),
				]
			);
		}

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_send_tabs_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		if ( $config['style']['send_button_section']['has_platform_colors'] ) {
			$this->add_control(
				'style_send_hover_colors',
				[
					'label' => esc_html__( 'Colors', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'elementor' ),
						'custom' => esc_html__( 'Custom', 'elementor' ),
					],
				]
			);
		}

		if ( $config['style']['send_button_section']['has_icon_color'] ) {
			$this->add_control(
				'style_send_hover_icon_color',
				[
					'label' => esc_html__( 'Icon Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-icon-hover: {{VALUE}}',
					],
					'condition' => [
						'style_send_hover_colors' => 'custom',
					],
				]
			);
		}

		if ( $config['style']['send_button_section']['has_text_color'] ) {
			$this->add_control(
				'style_send_hover_text_color',
				[
					'label' => esc_html__( 'Text Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-text-hover: {{VALUE}}',
					],
				]
			);
		}

		if ( $config['style']['send_button_section']['has_background_color'] ) {
			$this->add_control(
				'style_send_hover_background_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-bg-hover: {{VALUE}}',
					],
					'condition' => $this->get_platform_color_condition( [
						'style_send_hover_colors' => 'custom',
					] ),
				]
			);
		}

		$this->add_hover_animation_control(
			'style_send_hover_animation',
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'style_chat_button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-send-button-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-contact-buttons-send-button-padding-block-start: {{TOP}}{{UNIT}}; --e-contact-buttons-send-button-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-contact-buttons-send-button-padding-inline-start: {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function chat_box_animation_controls(): void {
		$this->add_responsive_control(
			'style_chat_box_entrance_animation',
			[
				'label' => esc_html__( 'Open Animation', 'elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'style_chat_box_exit_animation',
			[
				'label' => esc_html__( 'Close Animation', 'elementor' ),
				'type' => Controls_Manager::EXIT_ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'style_chat_box_animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'slow' => esc_html__( 'Slow', 'elementor' ),
					'normal' => esc_html__( 'Normal', 'elementor' ),
					'fast' => esc_html__( 'Fast', 'elementor' ),
				],
				'prefix_class' => 'animated-',
			]
		);
	}

	protected function add_style_chat_box_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_chat_box_section',
			[
				'label' => $config['style']['chat_box_section']['section_name'],
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		if ( $config['style']['has_platform_colors'] ) {
			$this->add_control(
				'style_chat_box_bg_select',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'Default', 'elementor' ),
						'custom' => esc_html__( 'Custom', 'elementor' ),
					],
				]
			);
		}

		$this->add_control(
			'style_chat_box_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-chat-box-bg: {{VALUE}}',
				],
				'condition' => $this->get_platform_color_condition( [
					'style_chat_box_bg_select' => 'custom',
				] ),
			]
		);

		if ( $config['style']['chat_box_section']['has_width'] ) {
			$this->add_responsive_control(
				'style_chat_box_width',
				[
					'label' => esc_html__( 'Width', 'elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'%' => [
							'min' => 10,
							'max' => 100,
						],
						'px' => [
							'min' => 0,
							'max' => 400,
						],
					],
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-chat-box-width: {{SIZE}}{{UNIT}}',
					],
				]
			);
		}

		$this->add_control(
			'style_chat_box_corners',
			[
				'label' => esc_html__( 'Corners', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => [
					'round' => esc_html__( 'Round', 'elementor' ),
					'rounded' => esc_html__( 'Rounded', 'elementor' ),
					'sharp' => esc_html__( 'Sharp', 'elementor' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'style_chat_box_box_shadow',
				'selector' => '{{WRAPPER}} .e-contact-buttons__content',
			]
		);

		if ( $config['style']['chat_box_section']['has_padding'] ) {
			$this->add_responsive_control(
				'style_chat_box_padding',
				[
					'label' => esc_html__( 'Padding', 'elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors' => [
						'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-chat-box-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-contact-buttons-chat-box-padding-block-start: {{TOP}}{{UNIT}}; --e-contact-buttons-chat-box-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-contact-buttons-chat-box-padding-inline-start: {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$this->chat_box_animation_controls();

		$this->end_controls_section();
	}

	protected function add_style_tab(): void {
		$this->add_style_chat_button_section();

		$this->add_style_top_bar_section();

		$this->add_style_message_bubble_section();

		$this->add_style_send_button_section();

		$this->add_style_chat_box_section();
	}

	private function add_advanced_tab(): void {
		$config = static::get_configuration();

		Controls_Manager::add_tab(
			static::TAB_ADVANCED,
			esc_html__( 'Advanced', 'elementor' )
		);

		if ( $config['advanced']['has_layout_position'] ) {
			$this->start_controls_section(
				'advanced_layout_section',
				[
					'label' => esc_html__( 'Layout', 'elementor' ),
					'tab' => static::TAB_ADVANCED,
				]
			);

			$this->add_control(
				'advanced_horizontal_position',
				[
					'label' => esc_html__( 'Horizontal Position', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'start' => [
							'title' => esc_html__( 'Left', 'elementor' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'elementor' ),
							'icon' => 'eicon-h-align-center',
						],
						'end' => [
							'title' => esc_html__( 'Right', 'elementor' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => $config['advanced']['horizontal_position_default'],
					'toggle' => false,
				]
			);

			if ( $config['advanced']['has_horizontal_offset'] ) {
				$this->add_responsive_control(
					'advanced_horizontal_offset',
					[
						'label' => esc_html__( 'Offset', 'elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'%' => [
								'min' => 10,
								'max' => 100,
							],
							'px' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-horizontal-offset: {{SIZE}}{{UNIT}}',
						],
						'condition' => [
							'advanced_horizontal_position' => [
								'start',
								'end',
							],
						],
					]
				);
			}

			$this->add_control(
				'advanced_vertical_position',
				[
					'label' => esc_html__( 'Vertical Position', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'top' => [
							'title' => esc_html__( 'Top', 'elementor' ),
							'icon' => 'eicon-v-align-top',
						],
						'middle' => [
							'title' => esc_html__( 'Middle', 'elementor' ),
							'icon' => 'eicon-v-align-middle',
						],
						'bottom' => [
							'title' => esc_html__( 'Bottom', 'elementor' ),
							'icon' => 'eicon-v-align-bottom',
						],
					],
					'default' => 'bottom',
					'toggle' => false,
				]
			);

			if ( $config['advanced']['has_vertical_offset'] ) {
				$this->add_responsive_control(
					'advanced_vertical_offset',
					[
						'label' => esc_html__( 'Offset', 'elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'%' => [
								'min' => 10,
								'max' => 100,
							],
							'px' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
						'selectors' => [
							'{{WRAPPER}} .e-contact-buttons' => '--e-contact-buttons-vertical-offset: {{SIZE}}{{UNIT}}',
						],
						'condition' => [
							'advanced_vertical_position' => [
								'top',
								'bottom',
							],
						],
					]
				);
			}

			if ( $config['advanced']['has_mobile_full_width'] ) {
				$this->add_control(
					'advanced_mobile_full_width',
					[
						'label' => esc_html__( 'Full Width on Mobile', 'elementor' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Yes', 'elementor' ),
						'label_off' => esc_html__( 'No', 'elementor' ),
						'return_value' => 'yes',
						'default' => 'yes',
					]
				);
			}

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'advanced_responsive_section',
			[
				'label' => esc_html__( 'Responsive', 'elementor' ),
				'tab' => static::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'responsive_description',
			[
				'raw' => __( 'Responsive visibility will take effect only on preview mode or live page, and not while editing in Elementor.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_hidden_device_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_custom_controls_section',
			[
				'label' => esc_html__( 'CSS', 'elementor' ),
				'tab' => static::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'advanced_custom_css_id',
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
			]
		);

		$this->add_control(
			'advanced_custom_css_classes',
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
				'title' => esc_html__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor' ),
			]
		);

		$this->end_controls_section();

		Plugin::$instance->controls_manager->add_custom_css_controls( $this, static::TAB_ADVANCED );

		Plugin::$instance->controls_manager->add_custom_attributes_controls( $this, static::TAB_ADVANCED );
	}

	protected function render(): void {
		$render_strategy = new Contact_Buttons_Core_Render( $this );

		$render_strategy->render();
	}
}
