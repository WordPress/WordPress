<?php
namespace Elementor\Core\Behaviors\Interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

interface Lock_Behavior {

	/**
	 * @return bool
	 */
	public function is_locked();

	/**
	 * @return array {
	 *
	 *    @type bool $is_locked
	 *
	 *    @type array $badge {
	 *         @type string $icon
	 *         @type string $text
	 *     }
	 *
	 *    @type array $content {
	 *         @type string $heading
	 *         @type string $description
	 *   }
	 *
	 *    @type array $button {
	 *         @type string $text
	 *         @type string $url
	 *   }
	 *
	 * }
	 */
	public function get_config();
}
