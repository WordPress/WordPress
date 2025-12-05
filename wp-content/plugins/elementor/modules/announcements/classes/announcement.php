<?php

namespace Elementor\Modules\Announcements\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Announcement {

	/**
	 * @var array
	 */
	protected $raw_data;
	/**
	 * @var array
	 */
	protected $triggers;

	public function __construct( array $data ) {
		$this->raw_data = $data;
		$this->set_triggers();
	}

	/**
	 * @return array
	 */
	protected function get_triggers(): array {
		return $this->triggers;
	}

	protected function set_triggers() {
		$triggers = $this->raw_data['triggers'] ?? [];
		foreach ( $triggers as $trigger ) {
			$this->triggers[] = Utils::get_trigger_object( $trigger );
		}
	}

	/**
	 * Is Active is_active
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		$triggers = $this->get_triggers();

		if ( empty( $triggers ) ) {
			return true;
		}

		foreach ( $triggers as $trigger ) {
			if ( ! $trigger->is_active() ) {
				return false;
			}
		}

		return true;
	}

	public function after_triggered() {
		foreach ( $this->get_triggers() as $trigger ) {
			if ( $trigger->is_active() ) {
				$trigger->after_triggered();
			}
		}
	}

	/**
	 * @return array
	 */
	public function get_prepared_data(): array {
		$raw_data = $this->raw_data;
		unset( $raw_data['triggers'] );

		return $raw_data;
	}
}
