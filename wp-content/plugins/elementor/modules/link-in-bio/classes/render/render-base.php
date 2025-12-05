<?php

namespace Elementor\Modules\LinkInBio\Classes\Render;

use Elementor\Core\Base\Providers\Social_Network_Provider;
use Elementor\Core\Base\Traits\Shared_Widget_Controls_Trait;
use Elementor\Icons_Manager;
use Elementor\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base;
use Elementor\Utils;

/**
 * Class Render_Base.
 *
 * This is the base class that will hold shared functionality that will be needed by all the various widget versions.
 *
 * @since 3.23.0
 */
abstract class Render_Base {

	use Shared_Widget_Controls_Trait;

	protected Widget_Link_In_Bio_Base $widget;

	protected array $settings;

	abstract public function render(): void;

	public function __construct( Widget_Link_In_Bio_Base $widget ) {
		$this->widget = $widget;
		$this->settings = $widget->get_settings_for_display();
	}

	protected function render_image_links(): void {
		$image_links_value_initial = $this->settings['image_links'] ?? [];
		$image_links_columns_value = $this->settings['image_links_per_row'] ?? 2;

		/**
		 * If empty returns a sub-array with all empty values
		 * Check for this here to avoid rendering container when empty
		 */
		$image_links_value = $this->clean_array( $image_links_value_initial );
		$has_image_links = ! empty( $image_links_value );

		if ( ! $has_image_links ) {
			return;
		}

		$image_links_classnames = 'e-link-in-bio__image-links';

		if ( ! empty( $image_links_columns_value ) ) {
			$image_links_classnames .= ' has-' . $image_links_columns_value . '-columns';
		}

		$this->widget->add_render_attribute( 'image-links', [
			'class' => $image_links_classnames,
		] );
		?>

		<div <?php echo $this->widget->get_render_attribute_string( 'image-links' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			foreach ( $image_links_value as $key => $image_link ) {
				$formatted_link = $image_link['image_links_url']['url'] ?? '';
				$image_link_image = $image_link['image_links_image'] ?? [];

				// Manage Link class variations

				$image_link_classnames = 'e-link-in-bio__image-links-link';

				// Manage Link attributes

				$url_attrs = [
					'class' => $image_link_classnames,
					'href' => esc_url( $formatted_link ),
				];

				$url_combined_attrs = $this->get_link_attributes(
					$image_link['image_links_url'],
					$url_attrs
				);

				foreach ( $url_combined_attrs as $attr_key => $attr_value ) {
					$this->widget->add_render_attribute( 'image-links-link' . $key, [
						$attr_key => $attr_value,
					] );
				}
				?>
				<a <?php echo $this->widget->get_render_attribute_string( 'image-links-link' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( ! empty( $image_link_image['id'] ) ) {
						echo wp_get_attachment_image( $image_link_image['id'], 'thumbnail', false, [
							'class' => 'e-link-in-bio__image-links-img',
						] );
					} else {
						$this->widget->add_render_attribute( 'image-links-img-' . $key, [
							'alt' => '',
							'class' => 'e-link-in-bio__image-links-img',
							'src' => esc_url( $image_link_image['url'] ),
						] );
						?>
						<img <?php echo $this->widget->get_render_attribute_string( 'image-links-img-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
					<?php } ?>
				</a>
			<?php } ?>
		</div>
		<?php
	}

	protected function render_ctas(): void {
		$ctas_props_corners = $this->settings['cta_links_corners'] ?? 'rounded';
		$ctas_props_show_border = $this->settings['cta_links_show_border'] ?? false;
		$ctas_props_type = $this->settings['cta_links_type'] ?? 'button';
		$ctas_value_initial = $this->settings['cta_link'] ?? [];

		/**
		 * $this->settings['cta_link'] if empty returns a sub-array with all empty values
		 * Check for this here to avoid rendering container when empty
		 */
		$ctas_value = $this->clean_array( $ctas_value_initial );
		$has_ctas = ! empty( $ctas_value );

		if ( ! $has_ctas ) {
			return;
		}

		$this->widget->add_render_attribute( 'ctas', [
			'class' => 'e-link-in-bio__ctas has-type-' . $ctas_props_type,
		] );
		?>

		<div <?php echo $this->widget->get_render_attribute_string( 'ctas' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			foreach ( $ctas_value as $key => $cta ) {
				$formatted_link = $this->get_formatted_link_based_on_type_for_cta( $cta );
				$cta_image = $cta['cta_link_image'] ?? [];
				$cta_has_image = ! empty( $cta_image ) &&
					( ! empty( $cta_image['url'] || ! empty( $cta_image['id'] ) ) ) &&
					'button' === $ctas_props_type;

				// Manage Link class variations

				$ctas_classnames = 'e-link-in-bio__cta is-type-' . $ctas_props_type;

				if ( 'button' === $ctas_props_type && $ctas_props_show_border ) {
					$ctas_classnames .= ' has-border';
				}

				if ( $cta_has_image ) {
					$ctas_classnames .= ' has-image';
				}

				if ( 'button' === $ctas_props_type ) {
					$ctas_classnames .= ' has-corners-' . $ctas_props_corners;
				}

				// Manage Link attributes

				$url_attrs = [
					'class' => $ctas_classnames,
					'href' => esc_url( $formatted_link ),
				];

				if (
					Social_Network_Provider::FILE_DOWNLOAD === $cta['cta_link_type'] ||
					Social_Network_Provider::VCF === $cta['cta_link_type']
				) {
					$url_attrs['download'] = 'download';
				}

				$cta_url = $cta['cta_link_url'];

				if ( Social_Network_Provider::WAZE == $cta['cta_link_type'] ) {
					$cta_url = $cta['cta_link_location'];
				}

				$url_combined_attrs = $this->get_link_attributes(
					$cta_url,
					$url_attrs
				);

				foreach ( $url_combined_attrs as $attr_key => $attr_value ) {
					$this->widget->add_render_attribute( 'cta-' . $key, [
						$attr_key => $attr_value,
					] );
				}
				?>
				<a <?php echo $this->widget->get_render_attribute_string( 'cta-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( $cta_has_image ) : ?>
						<span class="e-link-in-bio__cta-image">
							<?php if ( ! empty( $cta_image['id'] ) ) {
								echo wp_get_attachment_image( $cta_image['id'], 'thumbnail', false, [
									'class' => 'e-link-in-bio__cta-image-element',
								] );
							} else {
								$this->widget->add_render_attribute( 'cta-link-image' . $key, [
									'alt' => '',
									'class' => 'e-link-in-bio__cta-image-element',
									'src' => esc_url( $cta_image['url'] ),
								] );
								?>
								<img <?php echo $this->widget->get_render_attribute_string( 'cta-link-image' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
							<?php } ?>
						</span>
					<?php endif; ?>
					<span class="e-link-in-bio__cta-text">
						<?php echo esc_html( $cta['cta_link_text'] ); ?>
					</span>
				</a>
			<?php } ?>
		</div>
		<?php
	}

	protected function render_icons(): void {
		$icons_props_show_border = $this->settings['icons_border_show_border'] ?? false;
		$icons_props_size = $this->settings['icons_size'] ?? 'small';
		$icons_value = $this->settings['icon'] ?? [];

		$has_icons = ! empty( $icons_value );
		if ( ! $has_icons ) {
			return;
		}

		$this->widget->add_render_attribute( 'icons', [
			'class' => 'e-link-in-bio__icons has-size-' . $icons_props_size,
		] );
		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'icons' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			foreach ( $icons_value as $key => $icon ) {

				$formatted_link = $this->get_formatted_link_for_icon( $icon );

				$icon_class_names = 'e-link-in-bio__icon is-size-' . $icons_props_size;

				if ( $icons_props_show_border ) {
					$icon_class_names .= ' has-border';
				}

				$this->widget->add_render_attribute( 'icon-' . $key, [
					'class' => $icon_class_names,
				] );

				// Manage Link attributes

				$url_attrs = [
					'aria-label' => esc_attr( $icon['icon_platform'] ),
					'class' => 'e-link-in-bio__icon-link',
					'href' => esc_url( $formatted_link ),
				];

				$icon_url = $icon['icon_url'];

				if ( Social_Network_Provider::WAZE == $icon['icon_platform'] ) {
					$icon_url = $icon['icon_location'];
				}

				$url_combined_attrs = $this->get_link_attributes(
					$icon_url,
					$url_attrs
				);

				foreach ( $url_combined_attrs as $attr_key => $attr_value ) {
					$this->widget->add_render_attribute( 'icon-link-' . $key, [
						$attr_key => $attr_value,
					] );
				}
				?>
				<div <?php echo $this->widget->get_render_attribute_string( 'icon-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<a <?php echo $this->widget->get_render_attribute_string( 'icon-link-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<span class="e-link-in-bio__icon-svg">
							<?php
							$mapping = Social_Network_Provider::get_icon_mapping( $icon['icon_platform'] );
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
						</span>
						<?php if ( ! empty( $icon['icon_text'] ) ) : ?>
							<span class="e-link-in-bio__icon-label">
								<?php echo esc_html( $icon['icon_text'] ); ?>
							</span>
						<?php endif; ?>
					</a>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	protected function render_bio(): void {
		$bio_heading_props_tag = $this->settings['bio_heading_tag'] ?? 'h2';
		$bio_heading_value = $this->settings['bio_heading'] ?? '';

		$bio_title_props_tag = $this->settings['bio_title_tag'] ?? 'h2';
		$bio_title_value = $this->settings['bio_title'] ?? '';

		if ( 'top' === $this->widget->get_description_position() ) {
			$bio_about_heading_props_tag = $this->settings['bio_about_tag'] ?? 'h3';
			$bio_about_heading_value = $this->settings['bio_about'] ?? '';

			$bio_description_value = $this->settings['bio_description'] ?? '';
		}

		$has_bio_about_heading = ! empty( $bio_about_heading_value );
		$has_bio_description = ! empty( $bio_description_value );
		$has_bio_heading = ! empty( $bio_heading_value );
		$has_bio_title = ! empty( $bio_title_value );

		if ( $has_bio_heading || $has_bio_title || $has_bio_about_heading || $has_bio_description ) {
			?>
			<div class="e-link-in-bio__bio">
				<?php if ( $has_bio_heading ) {
					$this->widget->add_render_attribute( 'heading', 'class', 'e-link-in-bio__heading' );
					$bio_heading_output = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $bio_heading_props_tag ), $this->widget->get_render_attribute_string( 'heading' ), esc_html( $bio_heading_value ) );
					// Escaped above
					Utils::print_unescaped_internal_string( $bio_heading_output );
				} ?>
				<?php if ( $has_bio_title ) {
					$this->widget->add_render_attribute( 'title', 'class', 'e-link-in-bio__title' );
					$bio_title_output = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $bio_title_props_tag ), $this->widget->get_render_attribute_string( 'title' ), esc_html( $bio_title_value ) );
					// Escaped above
					Utils::print_unescaped_internal_string( $bio_title_output );
				} ?>
				<?php if ( $has_bio_about_heading ) {
					$this->widget->add_render_attribute( 'about-heading', 'class', 'e-link-in-bio__about-heading' );
					$bio_about_heading_output = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $bio_about_heading_props_tag ), $this->widget->get_render_attribute_string( 'about-heading' ), esc_html( $bio_about_heading_value ) );
					// Escaped above
					Utils::print_unescaped_internal_string( $bio_about_heading_output );
				} ?>
				<?php if ( $has_bio_description ) {
					$this->widget->add_render_attribute( 'description', 'class', 'e-link-in-bio__description' );
					$bio_description_output = sprintf( '<p %1$s>%2$s</p>', $this->widget->get_render_attribute_string( 'description' ), esc_html( $bio_description_value ) );
					// Escaped above
					Utils::print_unescaped_internal_string( $bio_description_output );
				} ?>
			</div>
			<?php
		}
	}

	protected function render_footer_bio(): void {
		if ( 'bottom' !== $this->widget->get_description_position() ) {
			return;
		}

		$bio_about_heading_props_tag = $this->settings['bio_about_tag'] ?? 'h3';
		$bio_about_heading_value = $this->settings['bio_about'] ?? '';

		$bio_description_value = $this->settings['bio_description'] ?? '';

		$has_bio_description = ! empty( $bio_description_value );
		$has_bio_about_heading = ! empty( $bio_about_heading_value );

		if ( $has_bio_about_heading || $has_bio_description ) {
			?>
			<div class="e-link-in-bio__bio e-link-in-bio__bio--footer">
				<?php if ( $has_bio_about_heading ) {
					$this->widget->add_render_attribute( 'about-heading', 'class', 'e-link-in-bio__about-heading' );
					$bio_about_heading_output = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $bio_about_heading_props_tag ), $this->widget->get_render_attribute_string( 'about-heading' ), esc_html( $bio_about_heading_value ) );
					// Escaped above
					Utils::print_unescaped_internal_string( $bio_about_heading_output );
				} ?>
				<?php if ( $has_bio_description ) {
					$this->widget->add_render_attribute( 'description', 'class', 'e-link-in-bio__description' );
					$bio_description_output = sprintf( '<p %1$s>%2$s</p>', $this->widget->get_render_attribute_string( 'description' ), esc_html( $bio_description_value ) );
					// Escaped above
					Utils::print_unescaped_internal_string( $bio_description_output );
				} ?>
			</div>
			<?php
		}
	}

	protected function render_identity_image(): void {
		/**
		 * Get base data for potential images
		 * Note order is important - secondary must render before primary
		*/
		$output_images = [
			'secondary_image' => [
				'props' => [],
				'should_render' => false,
				'value' => $this->settings['identity_image_cover'] ?? [],
			],
			'primary_image' => [
				'props' => [],
				'should_render' => false,
				'value' => $this->settings['identity_image'] ?? [],
			],
		];

		$output_images['primary_image']['should_render'] = ! empty( $output_images['primary_image']['value'] ) && ( ! empty( $output_images['primary_image']['value']['url'] || ! empty( $output_images['primary_image']['value']['id'] ) ) );
		$output_images['secondary_image']['should_render'] = ! empty( $output_images['secondary_image']['value'] ) && ( ! empty( $output_images['secondary_image']['value']['url'] || ! empty( $output_images['secondary_image']['value']['id'] ) ) );

		if ( ! $output_images['primary_image']['should_render'] && ! $output_images['secondary_image']['should_render'] ) {
			return;
		}

		$output_images = $this->set_primary_image_properties( $output_images );

		$output_images = $this->set_secondary_image_properties( $output_images );
		?>
		<div class="e-link-in-bio__identity">
			<?php
			foreach ( $output_images as $image_key => $image ) :
				if ( $image['should_render'] ) :
					$this->widget->add_render_attribute( 'identity_image_' . $image_key, [
						'class' => $this->get_image_classnames( $image ),
					] );
					?>
						<div <?php echo $this->widget->get_render_attribute_string( 'identity_image_' . $image_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php if ( ! empty( $image['value']['id'] ) ) {
								echo wp_get_attachment_image( $image['value']['id'], 'medium', false, [
									'class' => 'e-link-in-bio__identity-image-element',
								] );
							} else {
								$this->widget->add_render_attribute( 'identity_image_src' . $image_key, [
									'alt' => '',
									'class' => 'e-link-in-bio__identity-image-element',
									'src' => esc_url( $image['value']['url'] ),
								] );
								?>
								<img <?php echo $this->widget->get_render_attribute_string( 'identity_image_src' . $image_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />

							<?php } ?>
							<?php
							if ( ! empty( $image['props']['has_shape_divider'] ) ) {
								$this->print_shape_divider();
							}
							?>
						</div>
					<?php
				endif;
			endforeach;
			?>
		</div>
		<?php
	}

	protected function get_image_classnames( array $image ): string {
		$image_classnames = 'e-link-in-bio__identity-image e-link-in-bio__identity-image-' . $image['props']['style'];
		if ( ! empty( $image['props']['show_border'] ) || ! empty( $image['props']['show_bottom_border'] ) ) {
			$image_classnames .= ' has-border';
		}
		if ( ! empty( $image['props']['shape'] ) && 'profile' === $image['props']['style'] ) {
			$image_classnames .= ' has-style-' . $image['props']['shape'];
		}
		if ( ! empty( $image['props']['has_shape_divider'] ) ) {
			$image_classnames .= ' has-shape-divider';
		}
		return $image_classnames;
	}

	protected function get_formatted_link_based_on_type_for_cta( array $cta ): string {
		$formatted_link = $cta['cta_link_url']['url'] ?? '';

		// Ensure we clear the default link value if the matching type value is empty
		switch ( $cta['cta_link_type'] ) {
			case Social_Network_Provider::EMAIL:
				$formatted_link = Social_Network_Provider::build_email_link( $cta, 'cta_link' );
				break;
			case Social_Network_Provider::TELEPHONE:
				$formatted_link = ! empty( $cta['cta_link_number'] ) ? 'tel:' . $cta['cta_link_number'] : '';
				break;
			case Social_Network_Provider::MESSENGER:
				$formatted_link = ! empty( $cta['cta_link_username'] ) ?
					Social_Network_Provider::build_messenger_link( $cta['cta_link_username'] ) :
					'';
				break;
			case Social_Network_Provider::WAZE:
				$formatted_link = ! empty( $cta['cta_link_location']['url'] ) ? $cta['cta_link_location']['url'] : '';
				break;
			case Social_Network_Provider::WHATSAPP:
				$formatted_link = ! empty( $cta['cta_link_number'] ) ? 'https://wa.me/' . $cta['cta_link_number'] : '';
				break;
			case Social_Network_Provider::FILE_DOWNLOAD:
				$formatted_link = ! empty( $cta['cta_link_file']['url'] ) ? $cta['cta_link_file']['url'] : '';
				break;
			case Social_Network_Provider::VCF:
				$formatted_link = ! empty( $cta['cta_link_file']['url'] ) ? $cta['cta_link_file']['url'] : '';
				break;
			default:
				break;
		}

		return $formatted_link;
	}

	protected function get_formatted_link_for_icon( array $icon ): string {
		$formatted_link = $icon['icon_url']['url'] ?? '';

		// Ensure we clear the default link value if the matching type value is empty
		switch ( $icon['icon_platform'] ) {
			case Social_Network_Provider::EMAIL:
				$formatted_link = Social_Network_Provider::build_email_link( $icon, 'icon' );
				break;
			case Social_Network_Provider::TELEPHONE:
				$formatted_link = ! empty( $icon['icon_number'] ) ? 'tel:' . $icon['icon_number'] : '';
				break;
			case Social_Network_Provider::MESSENGER:
				$formatted_link = ! empty( $icon['icon_username'] ) ?
					Social_Network_Provider::build_messenger_link( $icon['icon_username'] ) :
					'';
				break;
			case Social_Network_Provider::WAZE:
				$formatted_link = ! empty( $icon['icon_location']['url'] ) ? $icon['icon_location']['url'] : '';
				break;
			case Social_Network_Provider::WHATSAPP:
				$formatted_link = ! empty( $icon['icon_number'] ) ? 'https://wa.me/' . $icon['icon_number'] : '';
				break;
			default:
				break;
		}

		return $formatted_link;
	}

	protected function build_layout_render_attribute(): void {
		$layout_props_full_height = $this->settings['advanced_layout_full_screen_height'] ?? '';
		$layout_props_full_height_controls = $this->settings['advanced_layout_full_screen_height_controls'] ?? '';
		$layout_props_full_width = $this->settings['advanced_layout_full_width_custom'] ?? '';
		$layout_props_show_border = $this->settings['background_show_border'] ?? '';
		$custom_classes = $this->settings['advanced_custom_css_classes'] ?? '';

		$layout_classnames = 'e-link-in-bio e-' . $this->widget->get_name();

		if ( 'yes' === $layout_props_show_border ) {
			$layout_classnames .= ' has-border';
		}

		if ( 'yes' === $layout_props_full_width ) {
			$layout_classnames .= ' is-full-width';
		}

		if ( 'yes' === $layout_props_full_height ) {
			$layout_classnames .= ' is-full-height';
		}

		if ( ! empty( $layout_props_full_height_controls ) ) {
			foreach ( $layout_props_full_height_controls as $breakpoint ) {
				$layout_classnames .= ' is-full-height-' . $breakpoint;
			}
		}

		if ( $custom_classes ) {
			$layout_classnames .= ' ' . $custom_classes;
		}

		$attrs = [
			'class' => $layout_classnames,
		];

		if ( ! empty( $this->settings['advanced_custom_css_id'] ) ) {
			$attrs['id'] = $this->settings['advanced_custom_css_id'];
		}

		$this->widget->add_render_attribute( 'layout', $attrs );
	}

	private function set_primary_image_properties( array $output_images ): array {
		if ( $output_images['primary_image']['should_render'] ) {
			$output_images['primary_image']['props']['shape'] = $this->settings['identity_image_shape'] ?? 'circle';
			$output_images['primary_image']['props']['style'] = $this->settings['identity_image_style'] ?? 'profile';
			$output_images['primary_image']['props']['show_border'] = $this->settings['identity_image_show_border'] ?? false;
			$output_images['primary_image']['props']['show_bottom_border'] = $this->settings['identity_image_bottom_show_border'] ?? false;
		}

		return $output_images;
	}

	private function set_secondary_image_properties( array $output_images ): array {
		if ( $output_images['secondary_image']['should_render'] ) {
			$output_images['secondary_image']['props']['style'] = 'cover';
			$output_images['secondary_image']['props']['show_bottom_border'] = $this->settings['identity_image_bottom_show_border'] ?? false;

			if ( ! empty( $this->settings['identity_section_style_cover_divider_bottom'] ) ) {
				$output_images['secondary_image']['props']['has_shape_divider'] = true;

				// Remove border if a shaped divider is applied
				$output_images['secondary_image']['props']['show_bottom_border'] = false;
			}

			$output_images['primary_image']['props']['style'] = 'profile';
		}

		return $output_images;
	}
}
