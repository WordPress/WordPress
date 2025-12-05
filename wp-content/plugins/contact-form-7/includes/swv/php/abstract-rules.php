<?php

namespace Contactable\SWV;

use WP_Error;

/**
 * The base class of SWV rules.
 */
abstract class Rule {

	protected $properties = array();

	public function __construct( $properties = '' ) {
		$this->properties = wp_parse_args( $properties, array() );
	}


	/**
	 * Returns true if this rule matches the given context.
	 *
	 * @param array $context Context.
	 */
	public function matches( $context ) {
		$field = $this->get_property( 'field' );

		if ( ! empty( $context['field'] ) ) {
			if ( $field and ! in_array( $field, (array) $context['field'], true ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Validates with this rule's logic.
	 *
	 * @param array $context Context.
	 */
	public function validate( $context ) {
		return true;
	}


	/**
	 * Converts the properties to an array.
	 *
	 * @return array Array of properties.
	 */
	public function to_array() {
		$properties = (array) $this->properties;

		if ( defined( 'static::rule_name' ) and static::rule_name ) {
			$properties = array( 'rule' => static::rule_name ) + $properties;
		}

		return $properties;
	}


	/**
	 * Returns the property value specified by the given property name.
	 *
	 * @param string $name Property name.
	 * @return mixed Property value.
	 */
	public function get_property( $name ) {
		if ( isset( $this->properties[$name] ) ) {
			return $this->properties[$name];
		}
	}


	/**
	 * Returns the default user input value from $_POST.
	 *
	 * @return mixed Default user input value.
	 */
	public function get_default_input() {
		$field = $this->get_property( 'field' );

		if ( isset( $_POST[$field] ) ) {
			return wp_unslash( $_POST[$field] );
		}

		return '';
	}


	/**
	 * Returns the default user upload file from $_FILES.
	 *
	 * @return object Default user upload file.
	 */
	public function get_default_upload() {
		$field = $this->get_property( 'field' );

		if ( isset( $_FILES[$field] ) ) {
			return (object) wp_unslash( $_FILES[$field] );
		}

		return (object) array();
	}


	/**
	 * Creates an error object. Returns false if the error property is omitted.
	 */
	protected function create_error() {
		$error_code = defined( 'static::rule_name' )
			? sprintf( 'swv_%s', static::rule_name )
			: 'swv';

		return new WP_Error(
			$error_code,
			(string) $this->get_property( 'error' ),
			$this
		);
	}

}


/**
 * The base class of SWV composite rules.
 */
abstract class CompositeRule extends Rule {

	protected $rules = array();


	/**
	 * Adds a sub-rule to this composite rule.
	 *
	 * @param Rule $rule Sub-rule to be added.
	 */
	public function add_rule( $rule ) {
		if ( $rule instanceof Rule ) {
			$this->rules[] = $rule;
		}
	}


	/**
	 * Returns an iterator of sub-rules.
	 */
	public function rules() {
		foreach ( $this->rules as $rule ) {
			yield $rule;
		}
	}


	/**
	 * Returns true if this rule matches the given context.
	 *
	 * @param array $context Context.
	 */
	public function matches( $context ) {
		return true;
	}


	/**
	 * Validates with this rule's logic.
	 *
	 * @param array $context Context.
	 */
	public function validate( $context ) {
		foreach ( $this->rules() as $rule ) {
			if ( $rule->matches( $context ) ) {
				$result = $rule->validate( $context );

				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}

		return true;
	}


	/**
	 * Converts the properties to an array.
	 *
	 * @return array Array of properties.
	 */
	public function to_array() {
		$rules_arrays = array_map(
			static function ( $rule ) {
				return $rule->to_array();
			},
			$this->rules
		);

		return array_merge(
			parent::to_array(),
			array(
				'rules' => $rules_arrays,
			)
		);
	}

}
