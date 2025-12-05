<?php

namespace Elementor\Modules\ElementorCounter;

use Elementor\Core\Isolation\Elementor_Counter_Adapter_Interface;
use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Wordpress_Adapter_Interface;
use Elementor\Core\Base\Module as BaseModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Module extends BaseModule implements Elementor_Counter_Adapter_Interface {
	const EDITOR_COUNTER_KEY = 'e_editor_counter';

	private ?Wordpress_Adapter_Interface $wordpress_adapter = null;
	private static $should_count_editor = true;

	public function get_name() {
		return 'elementor-counter';
	}

	public function __construct( ?Wordpress_Adapter_Interface $wordpress_adapter = null ) {
		parent::__construct();
		$this->wordpress_adapter = $wordpress_adapter ?? new Wordpress_Adapter();

		if ( self::$should_count_editor ) {
			add_action( 'elementor/editor/init', function () {
				$this->increment( self::EDITOR_COUNTER_KEY );
			}, 10 );

			self::$should_count_editor = false;
		}
	}

	/**
	 * @param self::EDITOR_COUNTER_KEY $key
	 *
	 * @return int | null
	 */
	public function get_count( $key ): ?int {
		return $this->is_key_allowed( $key )
			? (int) $this->wordpress_adapter->get_option( $key, 0 )
			: null;
	}

	/**
	 * @param self::EDITOR_COUNTER_KEY $key
	 * @param int                      $count
	 */
	public function set_count( $key, $count = 0 ): void {
		if ( ! $this->is_key_allowed( $key ) || ! is_int( $count ) ) {
			return;
		}

		$this->wordpress_adapter->update_option( $key, $count );
	}

	/**
	 * @param self::EDITOR_COUNTER_KEY $key
	 */
	public function increment( $key ): void {
		if ( ! $this->is_key_allowed( $key ) ) {
			return;
		}

		$count = $this->get_count( $key );

		$this->set_count( $key, $count + 1 );
	}

	public function is_key_allowed( $key ): bool {
		return in_array( $key, [ self::EDITOR_COUNTER_KEY ] );
	}
}
