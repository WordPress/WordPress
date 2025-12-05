<?php

namespace Elementor\Modules\FloatingButtons\Classes\Render;

/**
 * Class Contact_Buttons_Core_Render.
 *
 * This class handles the rendering of the Contact Buttons widget for the core version.
 *
 * @since 3.23.0
 */
class Contact_Buttons_Core_Render extends Contact_Buttons_Render_Base {

	public function render(): void {
		$this->build_layout_render_attribute();
		$this->add_content_wrapper_render_attribute();

		$content_classnames = 'e-contact-buttons__content';
		$animation_duration = $this->settings['style_chat_box_animation_duration'];

		if ( ! empty( $animation_duration ) ) {
			$content_classnames .= ' has-animation-duration-' . $animation_duration;
		}

		$this->widget->add_render_attribute( 'content', [
			'class' => $content_classnames,
		] );
		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'layout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div <?php echo $this->widget->get_render_attribute_string( 'content-wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div <?php echo $this->widget->get_render_attribute_string( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php
					$this->render_top_bar();
					$this->render_message_bubble();
					$this->render_send_button();
					?>
				</div>
			</div>
			<?php
			$this->render_chat_button();
			?>
		</div>
		<?php
	}

	protected function add_layout_render_attribute( $layout_classnames ) {
		$this->widget->add_render_attribute( 'layout', [
			'class' => $layout_classnames,
			'id' => $this->settings['advanced_custom_css_id'],
			'data-document-id' => get_the_ID(),
			'aria-role' => 'dialog',
		] );
	}

	protected function add_content_wrapper_render_attribute() {
		$this->widget->add_render_attribute( 'content-wrapper', [
			'aria-hidden' => 'true',
			'aria-label' => __( 'Links window', 'elementor' ),
			'class' => 'e-contact-buttons__content-wrapper hidden',
			'id' => 'e-contact-buttons__content-wrapper',
		] );
	}
}
