<?php

namespace Elementor\Modules\FloatingButtons\Classes\Render;

use Elementor\Icons_Manager;

/**
 * Class Floating_Bars_Core_Render.
 *
 * This class handles the rendering of the Floating Bars widget for the core version.
 *
 * @since 3.23.0
 */
class Floating_Bars_Core_Render extends Floating_Bars_Render_Base {

	protected function render_announcement_icon(): void {
		$icon = $this->settings['announcement_icon'] ?? '';

		if ( '' !== $icon['value'] ) : ?>
			<span class="e-floating-bars__announcement-icon"><?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?></span>
		<?php endif;
	}

	protected function render_announcement_text(): void {
		$text = $this->settings['announcement_text'] ?? '';

		$this->widget->add_render_attribute( 'announcement_text', [
			'class' => 'e-floating-bars__announcement-text',
		] );

		if ( '' !== $text ) : ?>
			<p <?php $this->widget->print_render_attribute_string( 'announcement_text' ); ?>>
				<?php echo esc_html( $text ); ?>
			</p>
		<?php endif;
	}

	protected function render_cta_icon(): void {
		$icon = $this->settings['cta_icon'] ?? '';
		$icon_classnames = 'e-floating-bars__cta-icon';

		$this->widget->add_render_attribute( 'cta-icon', [
			'class' => $icon_classnames,
		] );

		if ( '' !== $icon['value'] ) : ?>
			<span <?php echo $this->widget->get_render_attribute_string( 'cta-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?></span>
		<?php endif;
	}

	protected function render_cta_button(): void {
		$link = $this->settings['cta_link'] ?? '';
		$text = $this->settings['cta_text'] ?? '';

		$hover_animation = $this->settings['style_cta_button_hover_animation'];
		$corners = $this->settings['style_cta_button_corners'];
		$link_type = $this->settings['style_cta_type'];
		$entrance_animation = $this->settings['style_cta_button_animation'];
		$has_border = $this->settings['style_cta_button_show_border'];

		$cta_classnames = 'e-floating-bars__cta-button';

		if ( ! empty( $hover_animation ) ) {
			$cta_classnames .= ' elementor-animation-' . $hover_animation;
		}

		if ( ! empty( $corners ) ) {
			$cta_classnames .= ' has-corners-' . $corners;
		}

		if ( ! empty( $link_type ) ) {
			$cta_classnames .= ' is-type-' . $link_type;
		}

		if ( ! empty( $entrance_animation ) && 'none' != $entrance_animation ) {
			$cta_classnames .= ' has-entrance-animation';
		}

		if ( 'yes' == $has_border ) {
			$cta_classnames .= ' has-border';
		}

		$this->widget->add_render_attribute( 'cta-button', [
			'class' => $cta_classnames,
		] );

		$this->widget->add_render_attribute( 'cta_text', [
			'class' => 'e-floating-bars__cta-text',
		] );

		if ( ! empty( $text ) ) {
			$this->widget->add_link_attributes( 'cta-button', $link );
			?>
				<div class="e-floating-bars__cta-button-container">
					<a <?php echo $this->widget->get_render_attribute_string( 'cta-button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php $this->render_cta_icon(); ?>
						<span <?php $this->widget->print_render_attribute_string( 'cta_text' ); ?>><?php echo esc_html( $text ); ?></span>
					</a>
				</div>
			<?php
		}
	}

	protected function render_close_button(): void {
		$accessible_name = $this->settings['accessible_name'];
		$close_button_classnames = 'e-floating-bars__close-button';

		$this->widget->add_render_attribute( 'close-button', [
			'class' => $close_button_classnames,
			'aria-label' => sprintf(
				/* translators: %s: Accessible name. */
				esc_html__( 'Close %s', 'elementor' ),
				$accessible_name,
			),
			'type' => 'button',
			'aria-controls' => 'e-floating-bars',
		] );

		?>
			<button <?php echo $this->widget->get_render_attribute_string( 'close-button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<i class="eicon-close"></i>
			</button>
		<?php
	}

	public function render(): void {
		$this->build_layout_render_attribute();
		$has_close_button = $this->settings['floating_bar_close_switch'];

		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'layout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			$this->render_announcement_text();

			$this->render_announcement_icon();

			$this->render_cta_button();

			if ( 'yes' === $has_close_button ) {
				$this->render_close_button();
			}
			?>
			<div class="e-floating-bars__overlay"></div>
		</div>
		<?php
	}
}
