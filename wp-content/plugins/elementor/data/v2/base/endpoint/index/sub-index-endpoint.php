<?php
namespace Elementor\Data\V2\Base\Endpoint\Index;

use Elementor\Data\V2\Base\Endpoint\Index;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class SubIndexEndpoint is default `Base\Endpoint\Index` of `SubController`,
 * it was created to handle base_route and format for child controller, index endpoint.
 * In case `SubController` were used and the default method of `Controller::register_index_endpoint` ain't overridden.
 * this class will give support to have such routes, eg: 'alpha/{id}/beta/{sub_id}' without using additional endpoints.
 */
final class Sub_Index_Endpoint extends Index {
	/***
	 * @var \Elementor\Data\V2\Base\Controller
	 */
	public $controller;

	public function get_format() {
		return $this->controller->get_parent()->get_name() . '/{id}/' . $this->controller->get_name() . '/{sub_id}';
	}

	public function get_base_route() {
		$parent_controller = $this->controller->get_parent();
		$parent_index_endpoint = $parent_controller->index_endpoint;
		$parent_controller_route = '';

		// In case `$parent_index_endpoint` is AllChildren, it cannot support id_arg_name.
		if ( ! $parent_index_endpoint instanceof AllChildren ) {
			$parent_controller_route = "(?P<{$parent_index_endpoint->id_arg_name}>[\w]+)";
		}

		return untrailingslashit('/' . implode( '/', array_filter( [
			trim( $parent_index_endpoint->get_base_route(), '/' ),
			$parent_controller_route,
			$this->controller->get_name(),
			$this->get_public_name(),
		] ) ) );
	}
}
