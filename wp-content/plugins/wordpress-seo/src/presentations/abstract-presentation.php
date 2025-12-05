<?php

namespace Yoast\WP\SEO\Presentations;

use AllowDynamicProperties;
use Exception;

/**
 * The abstract presentation class.
 */
#[AllowDynamicProperties]
class Abstract_Presentation {

	/**
	 * The model.
	 *
	 * @var mixed
	 */
	public $model;

	/**
	 * Whether or not there is a presentation prototype.
	 *
	 * @var bool
	 */
	private $is_prototype = true;

	/**
	 * Creates a model presentation.
	 *
	 * @param array $data The data that this is a presentation of.
	 *
	 * @return static A model presentation.
	 *
	 * @throws Exception If attempting to create a model presentation from another model presentation.
	 */
	public function of( $data ) {
		if ( ! $this->is_prototype() ) {
			throw new Exception( 'Attempting to create a model presentation from another model presentation. Use the prototype presentation gained from DI instead.' );
		}

		// Clone self to allow stateful services that do benefit from DI.
		$presentation = clone $this;
		foreach ( $data as $key => $value ) {
			$presentation->{$key} = $value;
		}
		$presentation->is_prototype = false;
		return $presentation;
	}

	/**
	 * Magic getter for lazy loading of generate functions.
	 *
	 * @param string $name The property to get.
	 *
	 * @return mixed The value if it could be generated.
	 *
	 * @throws Exception If there is no generator for the property.
	 */
	public function __get( $name ) {
		if ( $this->is_prototype() ) {
			throw new Exception( 'Attempting property access on prototype presentation. Use Presentation::of( $data ) to get a model presentation.' );
		}
		$generator = "generate_$name";
		if ( \method_exists( $this, $generator ) ) {
			$this->{$name} = $this->$generator();
			return $this->{$name};
		}
		throw new Exception( "Property $name has no generator. Expected function $generator." );
	}

	/**
	 * Magic isset for ensuring methods that have a generator are recognised.
	 *
	 * @codeCoverageIgnore Wrapper method.
	 *
	 * @param string $name The property to get.
	 *
	 * @return bool Whether or not there is a generator for the requested property.
	 */
	public function __isset( $name ) {
		return \method_exists( $this, "generate_$name" );
	}

	/**
	 * Returns `true` if this class is a prototype.
	 *
	 * @codeCoverageIgnore Wrapper method.
	 *
	 * @return bool If this class is a prototype or not.
	 */
	protected function is_prototype() {
		return $this->is_prototype;
	}
}
