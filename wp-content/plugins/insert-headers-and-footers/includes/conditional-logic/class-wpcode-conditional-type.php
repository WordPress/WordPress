<?php
/**
 * Base class for types of conditional logic options.
 *
 * @package WPCode
 */

/**
 * Abstract class WPCode_Conditional_Type
 */
abstract class WPCode_Conditional_Type {

	/**
	 * An array of options for this type.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * The type label.
	 *
	 * @var string
	 */
	public $label;

	/**
	 * The type name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_type();
		$this->hooks();
	}

	/**
	 * Register conditional-logic hooks specific to each type (e.g. ajax callbacks).
	 *
	 * @return void
	 */
	public function hooks() {
	}

	/**
	 * Register this instance to the global auto-insert types.
	 *
	 * @return void
	 */
	private function register_type() {
		wpcode()->conditional_logic->register_type( $this );
	}

	/**
	 * Get the options for this type.
	 *
	 * @return array
	 */
	public function get_type_options() {
		if ( ! isset( $this->options ) ) {
			$this->load_type_options();
		}

		return $this->options;
	}

	/**
	 * Set the type label with a translatable string.
	 *
	 * @return void
	 */
	abstract protected function set_label();

	/**
	 * Load the options for this type of conditions.
	 *
	 * @return void
	 */
	abstract public function load_type_options();

	/**
	 * Get the label.
	 *
	 * @return string
	 */
	public function get_label() {
		if ( ! isset( $this->label ) ) {
			$this->set_label();
		}

		return $this->label;
	}

	/**
	 * Get the type name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Process a rule group specific to the conditions type.
	 *
	 * @param array $rule_group An array of rules with keys option,relation and value.
	 *
	 * @return bool
	 */
	public function evaluate_rule_row( $rule_group ) {
		return $this->evaluate_rule( $rule_group['option'], $rule_group['relation'], $rule_group['value'] );
	}

	/**
	 * This takes an option name from the list of options for the type
	 * and if it finds it, it executes the callback defined in the list of
	 * options and compares that value to the set value using the operator
	 * set in the settings.
	 *
	 * @param string $option The option to evaluate.
	 * @param string $relation The comparison relation.
	 * @param string $value The selected value for this condition.
	 *
	 * @return bool
	 */
	protected function evaluate_rule( $option, $relation, $value ) {
		$options = $this->get_type_options();
		if ( ! isset( $options [ $option ] ) ) {
			return true;
		}
		$option_details = $options[ $option ];

		if ( ! isset( $option_details['callback'] ) ) {
			return false;
		}
		$callback = $option_details['callback'];
		if ( ! is_callable( $callback ) ) {
			return false;
		}

		return $this->get_relation_comparison( $callback(), $value, $relation );
	}

	/**
	 * Takes 2 values and an operator and finds the appropriate function
	 * to evaluate the relation between them.
	 *
	 * @param mixed  $value1 This is the first value to compare with value 2.
	 * @param mixed  $value2 This is the 2nd value.
	 * @param string $operator This is the operator string.
	 *
	 * @return bool
	 */
	protected function get_relation_comparison( $value1, $value2, $operator ) {
		switch ( $operator ) {
			case '=':
				$result = $this->equals( $value1, $value2 );
				break;
			case '!=':
				$result = $this->does_not_equal( $value1, $value2 );
				break;
			case 'contains':
				$result = $this->contains( $value1, $value2 );
				break;
			case 'notcontains':
				$result = ! $this->contains( $value1, $value2 );
				break;
			default:
				$result = true;
				break;
		}

		return $result;
	}

	/**
	 * Does an equals comparison (not strict), also handles arrays to
	 * make it easier to compare things like user roles.
	 *
	 * @param mixed $value1 Value 1.
	 * @param mixed $value2 Value to compare value 1 to.
	 *
	 * @return bool
	 */
	private function equals( $value1, $value2 ) {
		if ( is_array( $value1 ) ) {
			if ( is_array( $value2 ) ) {
				return count( array_intersect( $value1, $value2 ) ) > 0;
			}

			return in_array( $value2, $value1 );
		}
		if ( is_array( $value2 ) ) {
			return in_array( $value1, $value2 );
		}

		return $value1 == $value2;
	}

	/**
	 * Does an does not equal comparison (not strict), also handles arrays to
	 * make it easier to compare things like user roles.
	 *
	 * @param mixed $value1 Value 1.
	 * @param mixed $value2 Value to compare value 1 to.
	 *
	 * @return bool
	 */
	private function does_not_equal( $value1, $value2 ) {
		if ( is_array( $value1 ) ) {
			if ( is_array( $value2 ) ) {
				return count( array_intersect( $value1, $value2 ) ) === 0;
			}

			return ! in_array( $value2, $value1 );
		}

		return $value1 != $value2;
	}

	/**
	 * Check if value1 contains value2.
	 *
	 * @param string $value1 Value in which to look for value 2.
	 * @param string $value2 The value to look for in value 1.
	 *
	 * @return bool
	 */
	private function contains( $value1, $value2 ) {
		return false !== strpos( $value1, $value2 );
	}
}
