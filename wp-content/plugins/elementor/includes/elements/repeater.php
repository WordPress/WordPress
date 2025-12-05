<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor repeater element.
 *
 * Elementor repeater handler class is responsible for initializing the repeater.
 *
 * @since 1.0.0
 */
class Repeater extends Element_Base {

	/**
	 * Repeater counter.
	 *
	 * Holds the Repeater counter data. Default is `0`.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var int Repeater counter.
	 */
	private static $counter = 0;

	/**
	 * Holds the count of the CURRENT instance
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Repeater constructor.
	 *
	 * Initializing Elementor repeater element.
	 *
	 * @since 1.0.7
	 * @access public
	 *
	 * @param array      $data Optional. Element data. Default is an empty array.
	 * @param array|null $args Optional. Element default arguments. Default is null.
	 */
	public function __construct( array $data = [], ?array $args = null ) {
		++self::$counter;

		$this->id = self::$counter;

		parent::__construct( $data, $args );

		$this->add_control(
			'_id',
			[
				'type' => Controls_Manager::HIDDEN,
			]
		);
	}

	/**
	 * Get repeater name.
	 *
	 * Retrieve the repeater name.
	 *
	 * @since 1.0.7
	 * @access public
	 *
	 * @return string Repeater name.
	 */
	public function get_name() {
		return 'repeater-' . $this->id;
	}

	/**
	 * Get repeater type.
	 *
	 * Retrieve the repeater type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Repeater type.
	 */
	public static function get_type() {
		return 'repeater';
	}

	/**
	 * Add new repeater control to stack.
	 *
	 * Register a repeater control to allow the user to set/update data.
	 *
	 * This method should be used inside `register_controls()`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $id      Repeater control ID.
	 * @param array  $args    Repeater control arguments.
	 * @param array  $options Optional. Repeater control options. Default is an
	 *                        empty array.
	 *
	 * @return bool True if repeater control added, False otherwise.
	 */
	public function add_control( $id, array $args, $options = [] ) {
		$current_tab = $this->get_current_tab();

		if ( null !== $current_tab ) {
			$args = array_merge( $args, $current_tab );
		}

		return parent::add_control( $id, $args, $options );
	}

	/**
	 * Get repeater fields.
	 *
	 * Retrieve the fields from the current repeater control.
	 *
	 * @since 1.5.0
	 * @deprecated 2.1.0 Use `get_controls()` method instead.
	 * @access public
	 *
	 * @return array Repeater fields.
	 */
	public function get_fields() {
		_deprecated_function( __METHOD__, '2.1.0', 'get_controls()' );

		return array_values( $this->get_controls() );
	}

	/**
	 * Get default child type.
	 *
	 * Retrieve the repeater child type based on element data.
	 *
	 * Note that repeater does not support children, therefore it returns false.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $element_data Element ID.
	 *
	 * @return false Repeater default child type or False if type not found.
	 */
	protected function _get_default_child_type( array $element_data ) {
		return false;
	}

	protected function handle_control_position( array $args, $control_id, $overwrite ) {
		return $args;
	}
}
