<?php
namespace Elementor\Data\Base\Processor;

use Elementor\Data\Base\Processor;

abstract class After extends Processor {

	/**
	 * Get conditions for running processor.
	 *
	 * @param array $args
	 * @param mixed $result
	 *
	 * @return bool
	 */
	public function get_conditions( $args, $result ) {
		return true;
	}

	/**
	 * Apply processor.
	 *
	 * @param $args
	 * @param $result
	 *
	 * @return mixed
	 */
	abstract public function apply( $args, $result );
}
