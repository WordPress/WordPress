<?php

namespace Elementor\Modules\Announcements\Triggers;

use Elementor\Modules\Announcements\Classes\Trigger_Base;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AiStarted extends Trigger_Base {
	/**
	 * @var string
	 */
	protected $name = 'ai-get-started-announcement';

	public function after_triggered() {
		User::set_introduction_viewed( [ 'introductionKey' => $this->name ] );
	}

	/**
	 * @return bool
	 */
	public function is_active(): bool {
		return ! User::get_introduction_meta( 'ai_get_started' ) && ! User::get_introduction_meta( $this->name );
	}
}
