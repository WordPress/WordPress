<?php

namespace Yoast\WP\SEO\Surfaces;

use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Classes_Surface.
 *
 * Surface for the indexables.
 */
class Classes_Surface {

	/**
	 * The dependency injection container.
	 *
	 * @var ContainerInterface
	 */
	public $container;

	/**
	 * Loader constructor.
	 *
	 * @param ContainerInterface $container The dependency injection container.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * Returns the instance of a class. Handy for unhooking things.
	 *
	 * @param string $class_name The class to get the instance of.
	 *
	 * @return mixed The instance of the class.
	 */
	public function get( $class_name ) {
		return $this->container->get( $class_name );
	}
}
