<?php
namespace Elementor\Data\Base;

abstract class Processor {

	/**
	 * Controller.
	 *
	 * @var \Elementor\Data\Base\Controller
	 */
	private $controller;

	/**
	 * Processor constructor.
	 *
	 * @param \Elementor\Data\Base\Controller $controller
	 */
	public function __construct( $controller ) {
		$this->controller = $controller;
	}

	/**
	 * Get processor command.
	 *
	 * @return string
	 */
	abstract public function get_command();
}
