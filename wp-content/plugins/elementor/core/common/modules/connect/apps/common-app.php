<?php
namespace Elementor\Core\Common\Modules\Connect\Apps;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Common_App extends Base_User_App {
	const OPTION_CONNECT_COMMON_DATA_KEY = self::OPTION_NAME_PREFIX . 'common_data';

	protected static $common_data = null;

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function get_option_name() {
		return static::OPTION_NAME_PREFIX . 'common_data';
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function init_data() {
		if ( is_null( self::$common_data ) ) {
			self::$common_data = get_user_option( static::get_option_name() );

			if ( ! self::$common_data ) {
				self::$common_data = [];
			}
		}

		$this->data = & self::$common_data;
	}

	public function action_reset() {
		delete_user_option( get_current_user_id(), static::OPTION_CONNECT_COMMON_DATA_KEY );

		parent::action_reset();
	}
}
