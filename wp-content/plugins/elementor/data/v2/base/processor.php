<?php
namespace Elementor\Data\V2\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Processor is just typically HOOK, who called before or after a command runs.
 * It exist to simulate frontend ($e.data) like mechanism with commands and hooks, since each
 * controller or endpoint is reachable via command (get_format).
 * The `Elementor\Data\V2\Manager::run` is able to run them with the ability to reach the endpoint.
 */
abstract class Processor {

	/**
	 * Controller.
	 *
	 * @var \Elementor\Data\V2\Base\Controller
	 */
	private $controller;

	/**
	 * Get processor command.
	 *
	 * @return string
	 */
	abstract public function get_command();

	/**
	 * Processor constructor.
	 *
	 * @param \Elementor\Data\V2\Base\Controller $controller
	 */
	public function __construct( $controller ) {
		$this->controller = $controller;
	}
}
