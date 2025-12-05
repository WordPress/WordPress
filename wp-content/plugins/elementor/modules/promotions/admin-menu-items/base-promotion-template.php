<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Settings;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_Promotion_Template implements Admin_Menu_Item_With_Page {

	abstract protected function get_promotion_title(): string;

	abstract protected function get_cta_url(): string;

	abstract protected function get_content_lines(): array;

	abstract protected function get_video_url(): string;

	public function is_visible() {
		return true;
	}

	public function get_parent_slug() {
		return Settings::PAGE_ID;
	}

	public function get_capability() {
		return 'manage_options';
	}

	protected function get_cta_text() {
		return esc_html__( 'Upgrade Now', 'elementor' );
	}

	/**
	 * Should the promotion have a side note.
	 *
	 * @return string
	 */
	protected function get_side_note(): string {
		return '';
	}

	private function get_lines() {
		ob_start();
		if ( ! empty( $this->get_content_lines() ) ) {
			?>
			<ul>
				<?php foreach ( $this->get_content_lines() as $item ) { ?>
					<li><?php Utils::print_unescaped_internal_string( $item ); ?></li>
				<?php } ?>
			</ul>
			<?php
		}

		return ob_get_clean();
	}

	public function render() {
		$promotion_data = $this->get_promotion_data();
		?>
			<div class="e-feature-promotion">
				<div class="e-feature-promotion_data">
					<h3><?php Utils::print_unescaped_internal_string( $promotion_data['promotion_title'] ); ?></h3>

					<?php Utils::print_unescaped_internal_string( $promotion_data['lines'] ); ?>

					<a class="elementor-button go-pro" href="<?php echo esc_url( $promotion_data['cta_url'] ); ?>" target="_blank">
						<?php Utils::print_unescaped_internal_string( $promotion_data['cta_text'] ); ?>
					</a>

					<?php if ( ! empty( $promotion_data['side_note'] ) ) { ?>
						<div class="side-note">
							<p><?php Utils::print_unescaped_internal_string( $promotion_data['side_note'] ); ?></p>
						</div>
					<?php } ?>

				</div>

				<iframe class="e-feature-promotion_iframe" src="<?php Utils::print_unescaped_internal_string( $promotion_data['video_url'] ); ?>&rel=0" title="Elementor" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
			</div>
		<?php
	}

	/**
	 * @return array|null
	 */
	private function get_promotion_data(): ?array {
		return Filtered_Promotions_Manager::get_filtered_promotion_data( $this->build_promotion_data_array(), 'elementor/' . $this->get_name() . '/custom_promotion', 'cta_url' );
	}

	/**
	 * @return array
	 */
	private function build_promotion_data_array(): array {
		return [
			'promotion_title' => $this->get_promotion_title(),
			'cta_url' => $this->get_cta_url(),
			'cta_text' => $this->get_cta_text(),
			'video_url' => $this->get_video_url(),
			'lines' => $this->get_lines(),
			'side_note' => $this->get_side_note(),
		];
	}
}
