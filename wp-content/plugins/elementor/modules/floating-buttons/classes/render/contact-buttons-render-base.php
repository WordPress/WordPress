<?php

namespace Elementor\Modules\FloatingButtons\Classes\Render;

use Elementor\Core\Base\Providers\Social_Network_Provider;
use Elementor\Icons_Manager;
use Elementor\Modules\FloatingButtons\Base\Widget_Contact_Button_Base;
use Elementor\Utils;

/**
 * Class Contact_Buttons_Render_Base.
 *
 * This is the base class that will hold shared functionality that will be needed by all the various widget versions.
 *
 * @since 3.23.0
 */
abstract class Contact_Buttons_Render_Base {

	protected Widget_Contact_Button_Base $widget;

	protected array $settings;


	abstract public function render(): void;

	public function __construct( Widget_Contact_Button_Base $widget ) {
		$this->widget = $widget;
		$this->settings = $widget->get_settings_for_display();
	}

	protected function render_chat_button_icon(): void {
		$platform = $this->settings['chat_button_platform'] ?? '';

		$mapping = Social_Network_Provider::get_icon_mapping( $platform );
		$icon_lib = explode( ' ', $mapping )[0];
		$library = 'fab' === $icon_lib ? 'fa-brands' : 'fa-solid';
		Icons_Manager::render_icon(
			[
				'library' => $library,
				'value' => $mapping,
			],
			[ 'aria-hidden' => 'true' ]
		);
	}

	protected function render_chat_button(): void {
		$platform = $this->settings['chat_button_platform'] ?? '';
		$display_dot = $this->settings['chat_button_show_dot'] ?? '';
		$button_size = $this->settings['style_chat_button_size'];
		$hover_animation = $this->settings['style_button_color_hover_animation'];
		$entrance_animation = $this->settings['style_chat_button_animation'];
		$entrance_animation_duration = $this->settings['style_chat_button_animation_duration'];
		$entrance_animation_delay = $this->settings['style_chat_button_animation_delay'];
		$accessible_name = $this->settings['chat_aria_label'];

		$button_classnames = 'e-contact-buttons__chat-button e-contact-buttons__chat-button-shadow';

		if ( ! empty( $button_size ) ) {
			$button_classnames .= ' has-size-' . $button_size;
		}

		if ( ! empty( $hover_animation ) ) {
			$button_classnames .= ' elementor-animation-' . $hover_animation;
		}

		if ( ! empty( $entrance_animation ) && 'none' != $entrance_animation ) {
			$button_classnames .= ' has-entrance-animation';
		}

		if ( ! empty( $entrance_animation_delay ) ) {
			$button_classnames .= ' has-entrance-animation-delay';
		}

		if ( ! empty( $entrance_animation_duration ) ) {
			$button_classnames .= ' has-entrance-animation-duration-' . $entrance_animation_duration;
		}

		if ( 'yes' === $display_dot ) {
			$button_classnames .= ' has-dot';
		}

		$this->widget->add_render_attribute( 'button', [
			'class' => $button_classnames,
			'aria-controls' => 'e-contact-buttons__content-wrapper',
			'aria-label' => sprintf(
				/* translators: %s: Accessible name. */
				esc_html__( 'Toggle %s', 'elementor' ),
				$accessible_name,
			),
			'type' => 'button',
		] );

		?>
		<div class="e-contact-buttons__chat-button-container">
			<button <?php echo $this->widget->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php
					$this->render_chat_button_icon();
				?>
			</button>
		</div>
		<?php
	}

	protected function render_close_button(): void {
		$accessible_name = $this->settings['chat_aria_label'];

		$this->widget->add_render_attribute( 'close-button', [
			'class' => 'e-contact-buttons__close-button',
			'aria-controls' => 'e-contact-buttons__content-wrapper',
			'aria-label' => sprintf(
				/* translators: %s: Accessible name. */
				esc_html__( 'Close %s', 'elementor' ),
				$accessible_name,
			),
			'type' => 'button',
		] );

		?>
			<button <?php echo $this->widget->get_render_attribute_string( 'close-button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<i class="eicon-close"></i>
			</button>
		<?php
	}

	protected function render_top_bar(): void {
		$profile_image_value = $this->settings['top_bar_image'] ?? [];
		$has_profile_image = ! empty( $profile_image_value ) && ( ! empty( $profile_image_value['url'] || ! empty( $profile_image_value['id'] ) ) );
		$profile_image_size = $this->settings['style_top_bar_image_size'];
		$display_profile_dot = $this->settings['top_bar_show_dot'];

		$profile_image_classnames = 'e-contact-buttons__profile-image';

		if ( ! empty( $profile_image_size ) ) {
			$profile_image_classnames .= ' has-size-' . $profile_image_size;
		}

		if ( 'yes' === $display_profile_dot ) {
			$profile_image_classnames .= ' has-dot';
		}

		$top_bar_title = $this->settings['top_bar_title'] ?? '';
		$top_bar_subtitle = $this->settings['top_bar_subtitle'] ?? '';

		$has_top_bar_title = ! empty( $top_bar_title );
		$has_top_bar_subtitle = ! empty( $top_bar_subtitle );

		$this->widget->add_render_attribute( 'profile-image', [
			'class' => $profile_image_classnames,
		] );
		?>
		<div class="e-contact-buttons__top-bar">
			<?php $this->render_close_button(); ?>
			<div <?php echo $this->widget->get_render_attribute_string( 'profile-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( ! empty( $profile_image_value['id'] ) ) {
					echo wp_get_attachment_image( $profile_image_value['id'], 'medium', false, [
						'class' => 'e-contact-buttons__profile-image-el',
					] );
				} else {
					$this->widget->add_render_attribute( 'profile-image-src', [
						'alt'   => '',
						'class' => 'e-contact-buttons__profile-image-el',
						'src'   => esc_url( $profile_image_value['url'] ),
					] );
					?>
					<img <?php echo $this->widget->get_render_attribute_string( 'profile-image-src' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
				<?php } ?>
			</div>

			<div class="e-contact-buttons__top-bar-details">
				<?php if ( $has_top_bar_title ) { ?>
					<p class="e-contact-buttons__top-bar-title"><?php echo esc_html( $top_bar_title ); ?></p>
				<?php } ?>
				<?php if ( $has_top_bar_subtitle ) { ?>
					<p class="e-contact-buttons__top-bar-subtitle"><?php echo esc_html( $top_bar_subtitle ); ?></p>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	protected function render_message_bubble_typing_animation(): void {
		$has_typing_animation = 'yes' === $this->settings['chat_button_show_animation'];
		?>
			<?php if ( $has_typing_animation ) { ?>
				<div class="e-contact-buttons__dots-container">
					<span class="e-contact-buttons__dot e-contact-buttons__dot-1"></span>
					<span class="e-contact-buttons__dot e-contact-buttons__dot-2"></span>
					<span class="e-contact-buttons__dot e-contact-buttons__dot-3"></span>
				</div>
			<?php } ?>
		<?php
	}

	protected function render_message_bubble_container(): void {
		$message_bubble_name = $this->settings['message_bubble_name'] ?? '';
		$message_bubble_body = $this->settings['message_bubble_body'] ?? '';
		$has_message_bubble_name = ! empty( $message_bubble_name );
		$has_message_bubble_body = ! empty( $message_bubble_body );
		$time_format = $this->settings['chat_button_time_format'];
		?>
			<div class="e-contact-buttons__bubble-container">
				<div class="e-contact-buttons__bubble">
					<?php if ( $has_message_bubble_name ) { ?>
						<p class="e-contact-buttons__message-bubble-name"><?php echo esc_html( $message_bubble_name ); ?></p>
					<?php } ?>
					<?php if ( $has_message_bubble_body ) { ?>
						<p class="e-contact-buttons__message-bubble-body"><?php echo esc_html( $message_bubble_body ); ?></p>
					<?php } ?>
					<p class="e-contact-buttons__message-bubble-time" data-time-format="<?php echo esc_attr( $time_format ); ?>"></p>
				</div>
			</div>
		<?php
	}

	protected function render_message_bubble_powered_by(): void {
		if ( Utils::has_pro() ) {
			return;
		}
		?>
			<div class="e-contact-buttons__powered-container">
				<p class="e-contact-buttons__powered-text">
					<?php echo esc_attr__( 'Powered by Elementor', 'elementor' ); ?>
				</p>
			</div>
		<?php
	}

	protected function render_message_bubble(): void {
		$message_bubble_classnames = 'e-contact-buttons__message-bubble';
		$show_animation = $this->settings['chat_button_show_animation'] ?? false;
		$has_typing_animation = $show_animation && 'yes' === $show_animation;

		if ( $has_typing_animation ) {
			$message_bubble_classnames .= ' has-typing-animation';
		}

		$this->widget->add_render_attribute( 'message-bubble', [
			'class' => $message_bubble_classnames,
		] );
		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'message-bubble' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
				$this->render_message_bubble_typing_animation();
				$this->render_message_bubble_container();
			?>
		</div>
		<?php
	}

	protected function render_contact_text(): void {
		$contact_cta_text = $this->settings['contact_cta_text'] ?? '';
		?>
			<?php if ( ! empty( $contact_cta_text ) ) { ?>
				<p class="e-contact-buttons__contact-text"><?php echo esc_html( $contact_cta_text ); ?></p>
			<?php } ?>
		<?php
	}

	protected function render_contact_links(): void {
		$contact_icons = $this->settings['contact_repeater'] ?? [];
		$icons_size = $this->settings['style_contact_button_size'] ?? 'small';
		$hover_animation = $this->settings['style_contact_button_hover_animation'];
		?>
			<div class="e-contact-buttons__contact-links">
				<?php
				foreach ( $contact_icons as $key => $icon ) {
					$icon_text_mapping = Social_Network_Provider::get_text_mapping( $icon['contact_icon_platform'] );
					$aria_label = sprintf(
						/* translators: %s: Platform name. */
						esc_html__( 'Open %s', 'elementor' ),
						$icon_text_mapping,
					);

					$link = [
						'platform' => $icon['contact_icon_platform'],
						'number' => $icon['contact_icon_number'] ?? '',
						'username' => $icon['contact_icon_username'] ?? '',
						'email_data' => [
							'contact_icon_mail' => $icon['contact_icon_mail'] ?? '',
							'contact_icon_mail_subject' => $icon['contact_icon_mail_subject'] ?? '',
							'contact_icon_mail_body' => $icon['contact_icon_mail_body'] ?? '',
						],
						'viber_action' => $icon['contact_icon_viber_action'] ?? '',
					];

					$formatted_link = $this->get_formatted_link( $link, 'contact_icon' );

					$icon_classnames = 'e-contact-buttons__contact-icon-link has-size-' . $icons_size;

					if ( ! empty( $hover_animation ) ) {
						$icon_classnames .= ' elementor-animation-' . $hover_animation;
					}

					$this->widget->add_render_attribute( 'icon-link-' . $key, [
						'aria-label' => $aria_label,
						'class' => $icon_classnames,
						'href' => $formatted_link,
						'rel' => 'noopener noreferrer',
						'target' => '_blank',
					] );

					?>

					<a <?php echo $this->widget->get_render_attribute_string( 'icon-link-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
							$mapping = Social_Network_Provider::get_icon_mapping( $icon['contact_icon_platform'] );
							$icon_lib = explode( ' ', $mapping )[0];
							$library = 'fab' === $icon_lib ? 'fa-brands' : 'fa-solid';
							Icons_Manager::render_icon(
								[
									'library' => $library,
									'value' => $mapping,
								],
								[ 'aria-hidden' => 'true' ]
							);
						?>
					</a>
				<?php } ?>
			</div>
		<?php
	}

	protected function render_contact_section(): void {
		?>
		<div class="e-contact-buttons__contact">
			<?php
				$this->render_contact_text();
				$this->render_contact_links();
			?>
		</div>
		<?php
	}

	protected function render_send_button(): void {
		$platform = $this->settings['chat_button_platform'] ?? '';
		$send_button_text = $this->settings['send_button_text'];
		$hover_animation = $this->settings['style_send_hover_animation'];
		$cta_classnames = 'e-contact-buttons__send-cta';

		$link = [
			'platform' => $platform,
			'number' => $this->settings['chat_button_number'] ?? '',
			'username' => $this->settings['chat_button_username'] ?? '',
			'email_data' => [
				'chat_button_mail' => $this->settings['chat_button_mail'],
				'chat_button_mail_subject' => $this->settings['chat_button_mail_subject'] ?? '',
				'chat_button_mail_body' => $this->settings['chat_button_mail_body'] ?? '',
			],
			'viber_action' => $this->settings['chat_button_viber_action'],
		];

		$formatted_link = $this->get_formatted_link( $link, 'chat_button' );

		if ( ! empty( $hover_animation ) ) {
			$cta_classnames .= ' elementor-animation-' . $hover_animation;
		}

		$this->widget->add_render_attribute( 'formatted-cta', [
			'class' => $cta_classnames,
			'href' => $formatted_link,
			'rel' => 'noopener noreferrer',
			'target' => '_blank',
		] );

		?>
		<div class="e-contact-buttons__send-button">
			<?php $this->render_message_bubble_powered_by(); ?>
			<div class="e-contact-buttons__send-button-container">
				<?php if ( $send_button_text ) { ?>
					<a <?php echo $this->widget->get_render_attribute_string( 'formatted-cta' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
							$mapping = Social_Network_Provider::get_icon_mapping( $platform );
							$icon_lib = explode( ' ', $mapping )[0];
							$library = 'fab' === $icon_lib ? 'fa-brands' : 'fa-solid';
							Icons_Manager::render_icon(
								[
									'library' => $library,
									'value' => $mapping,
								],
								[ 'aria-hidden' => 'true' ]
							);
						?>
						<?php echo esc_html( $send_button_text ); ?>
					</a>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	protected function get_formatted_link( array $link, string $prefix ): string {

		// Ensure we clear the default link value if the matching type value is empty
		switch ( $link['platform'] ) {
			case Social_Network_Provider::EMAIL:
				$formatted_link = Social_Network_Provider::build_email_link( $link['email_data'], $prefix );
				break;
			case Social_Network_Provider::SMS:
				$formatted_link = ! empty( $link['number'] ) ? 'sms:' . $link['number'] : '';
				break;
			case Social_Network_Provider::MESSENGER:
				$formatted_link = ! empty( $link['username'] ) ?
					Social_Network_Provider::build_messenger_link( $link['username'] ) :
					'';
				break;
			case Social_Network_Provider::WHATSAPP:
				$formatted_link = ! empty( $link['number'] ) ? 'https://wa.me/' . $link['number'] : '';
				break;
			case Social_Network_Provider::VIBER:
				$formatted_link = Social_Network_Provider::build_viber_link( $link['viber_action'], $link['number'] );
				break;
			case Social_Network_Provider::SKYPE:
				$formatted_link = ! empty( $link['username'] ) ? 'skype:' . $link['username'] . '?chat' : '';
				break;
			case Social_Network_Provider::TELEPHONE:
				$formatted_link = ! empty( $link['number'] ) ? 'tel:' . $link['number'] : '';
				break;
			default:
				break;
		}

		return esc_html( $formatted_link );
	}

	protected function is_url_link( string $platform ): bool {
		return Social_Network_Provider::URL === $platform || Social_Network_Provider::WAZE === $platform;
	}

	protected function render_link_attributes( array $link, string $key ) {
		switch ( $link['platform'] ) {
			case Social_Network_Provider::WAZE:
				if ( empty( $link['location']['url'] ) ) {
					$link['location']['url'] = '#';
				}

				$this->widget->add_link_attributes( $key, $link['location'] );
				break;
			case Social_Network_Provider::URL:
				if ( empty( $link['url']['url'] ) ) {
					$link['url']['url'] = '#';
				}

				$this->widget->add_link_attributes( $key, $link['url'] );
				break;
			default:
				break;
		}
	}

	protected function build_layout_render_attribute(): void {
		$layout_classnames = 'e-contact-buttons e-' . $this->widget->get_name();
		$platform = $this->settings['chat_button_platform'] ?? '';
		$border_radius = $this->settings['style_chat_box_corners'];
		$alignment_position_horizontal = $this->settings['advanced_horizontal_position'];
		$alignment_position_vertical = $this->settings['advanced_vertical_position'];
		$has_animations = ! empty( $this->settings['style_chat_box_exit_animation'] ) || ! empty( $this->settings['style_chat_box_entrance_animation'] );
		$custom_classes = $this->settings['advanced_custom_css_classes'] ?? '';

		$icon_name_mapping = Social_Network_Provider::get_name_mapping( $platform );

		if ( ! empty( $platform ) ) {
			$layout_classnames .= ' has-platform-' . $icon_name_mapping;
		}

		if ( ! empty( $border_radius ) ) {
			$layout_classnames .= ' has-corners-' . $border_radius;
		}

		if ( ! empty( $alignment_position_horizontal ) ) {
			$layout_classnames .= ' has-h-alignment-' . $alignment_position_horizontal;
		}

		if ( ! empty( $alignment_position_vertical ) ) {
			$layout_classnames .= ' has-v-alignment-' . $alignment_position_vertical;
		}

		if ( $has_animations ) {
			$layout_classnames .= ' has-animations';
		}

		if ( $custom_classes ) {
			$layout_classnames .= ' ' . $custom_classes;
		}

		$this->add_layout_render_attribute( $layout_classnames );
	}
}
