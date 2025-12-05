<?php

namespace Elementor\Modules\Announcements\Triggers;

use Elementor\Modules\Announcements\Classes\Trigger_Base;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class IsFlexContainerInactive extends Trigger_Base {

	const USER_META_KEY = 'announcements_user_counter';
	/**
	 * @var string
	 */
	protected $name = 'is-flex-container-inactive';

	/**
	 * @return int
	 */
	protected function get_view_count(): int {
		$user_counter = $this->get_user_announcement_count();

		return ! empty( $user_counter ) ? (int) $user_counter : 0;
	}

	public function after_triggered() {
		$new_counter = $this->get_view_count() + 1;
		update_user_meta( get_current_user_id(), self::USER_META_KEY, $new_counter );
	}

	/**
	 * @return bool
	 */
	public function is_active(): bool {
		$is_feature_active = Plugin::$instance->experiments->is_feature_active( 'container' );
		$counter = $this->get_user_announcement_count();

		return ! $is_feature_active && (int) $counter < 1;
	}

	/**
	 * @return string
	 */
	private function get_user_announcement_count(): string {
		return get_user_meta( get_current_user_id(), self::USER_META_KEY, true );
	}
}
