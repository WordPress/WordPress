<?php
namespace Elementor\Core\Editor;

use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Promotion {

	/**
	 * @return array
	 */
	public function get_elements_promotion() {
		return Filtered_Promotions_Manager::get_filtered_promotion_data(
			$this->get_promotion_data(),
			'elementor/editor/promotion/get_elements_promotion',
			'action_button',
			'url'
		);
	}

	/**
	 * @return array
	 */
	private function get_action_button_content(): array {
		$has_pro = Utils::has_pro();
		return $has_pro ? [
			'text' => __( 'Connect & Activate', 'elementor' ),
			'url' => admin_url( 'admin.php?page=elementor-license' ),
		] : [
			'text' => __( 'Upgrade Now', 'elementor' ),
			'url' => 'https://go.elementor.com/go-pro-%s',
		];
	}

	/**
	 * @return string
	 */
	private function get_promotion_url(): string {
		return Utils::has_pro()
			? admin_url( 'admin.php?page=elementor-license' )
			: 'https://go.elementor.com/go-pro-%s';
	}

	/**
	 * @return array
	 */
	private function get_promotion_data(): array {
		return [
			/* translators: %s: Widget title. */
			'title' => __( '%s Widget', 'elementor' ),
			/* translators: %s: Widget title. */
			'content' => __(
				'Use %s widget and dozens more pro features to extend your toolbox and build sites faster and better.',
				'elementor'
			),
			'action_button' => $this->get_action_button_content(),
		];
	}
}
