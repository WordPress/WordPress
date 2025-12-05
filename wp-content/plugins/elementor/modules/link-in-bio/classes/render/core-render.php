<?php

namespace Elementor\Modules\LinkInBio\Classes\Render;

/**
 * Class Core_Render.
 *
 * This class handles the rendering of the Link In Bio widget for the core version.
 *
 * @since 3.23.0
 */
class Core_Render extends Render_Base {

	public function render(): void {
		$this->build_layout_render_attribute();
		?>
		<div <?php echo $this->widget->get_render_attribute_string( 'layout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="e-link-in-bio__content">

				<?php
				$this->render_identity_image();

				$this->render_bio();

				$this->render_icons();

				$this->render_image_links();

				$this->render_ctas();

				$this->render_footer_bio();
				?>

			</div>
			<div class="e-link-in-bio__bg">
				<div class="e-link-in-bio__bg-overlay"></div>
			</div>
		</div>
		<?php
	}
}
