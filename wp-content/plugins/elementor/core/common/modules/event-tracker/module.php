<?php
namespace Elementor\Core\Common\Modules\EventTracker;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\EventTracker\Data\Controller;
use Elementor\Plugin;
use Elementor\Tracker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Event Tracker Module Class
 *
 * @since 3.6.0
 */
class Module extends BaseModule {

	public function get_name() {
		return 'event-tracker';
	}

	/**
	 * Get init settings.
	 *
	 * @since 3.6.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_init_settings() {
		return [
			'isUserDataShared' => Tracker::is_allow_track(),
		];
	}

	public function __construct() {
		// Initialize Events Database Table
		$this->add_component( 'events-db', new DB() );

		// Handle User Data Deletion/Export requests.
		new Personal_Data();

		Plugin::$instance->data_manager_v2->register_controller( new Controller() );
	}
}
