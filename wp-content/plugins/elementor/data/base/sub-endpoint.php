<?php
namespace Elementor\Data\Base;

/**
 * TODO: Add test.
 */
abstract class SubEndpoint extends Endpoint {

	/**
	 * @var Endpoint
	 */
	protected $parent_endpoint;

	/**
	 * @var string
	 */
	protected $parent_route = '';

	public function __construct( $parent_route, $parent_endpoint ) {
		$this->parent_endpoint = $parent_endpoint;
		$this->parent_route = $parent_route;

		parent::__construct( $this->parent_endpoint->controller );
	}

	/**
	 * Get parent route.
	 *
	 * @return \Elementor\Data\Base\Endpoint
	 */
	public function get_parent() {
		return $this->parent_endpoint;
	}

	public function get_base_route() {
		$controller_name = $this->controller->get_name();

		return $controller_name . '/' . $this->parent_route . $this->get_name();
	}
}
