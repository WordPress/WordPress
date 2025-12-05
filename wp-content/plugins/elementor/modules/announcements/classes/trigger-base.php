<?php

namespace Elementor\Modules\Announcements\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Trigger_Base {

	/**
	 * @var string
	 */
	protected $name = 'trigger-base';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function is_active(): bool {
		return true;
	}

	public function after_triggered() {
	}
}
