<?php

namespace Yoast\WP\SEO\Surfaces;

use Yoast\WP\SEO\Exceptions\Forbidden_Property_Mutation_Exception;
use Yoast\WP\SEO\Helpers\Schema;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Schema_Helpers_Surface.
 *
 * Surface for the indexables.
 *
 * @property Schema\Article_Helper $article
 * @property Schema\HTML_Helper $html
 * @property Schema\ID_Helper $id
 * @property Schema\Image_Helper $image
 * @property Schema\Language_Helper $language
 */
class Schema_Helpers_Surface {

	/**
	 * The DI container.
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * Helpers that should be fully capitalized.
	 *
	 * @var array
	 */
	private $capitalized_helpers = [ 'html', 'id' ];

	/**
	 * Loader constructor.
	 *
	 * @param ContainerInterface $container The dependency injection container.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * Magic getter for getting helper classes.
	 *
	 * @param string $helper The helper to get.
	 *
	 * @return mixed The helper class.
	 */
	public function __get( $helper ) {
		return $this->container->get( $this->get_helper_class( $helper ) );
	}

	/**
	 * Magic isset for ensuring helper exists.
	 *
	 * @param string $helper The helper to get.
	 *
	 * @return bool Whether the helper exists.
	 */
	public function __isset( $helper ) {
		return $this->container->has( $this->get_helper_class( $helper ) );
	}

	/**
	 * Prevents setting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name  The property name.
	 * @param mixed  $value The property value.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Set is never meant to be called.
	 */
	public function __set( $name, $value ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- __set must have a name and value - PHPCS #3715.
		throw Forbidden_Property_Mutation_Exception::cannot_set_because_property_is_immutable( $name );
	}

	/**
	 * Prevents unsetting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name The property name.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Unset is never meant to be called.
	 */
	public function __unset( $name ) {
		throw Forbidden_Property_Mutation_Exception::cannot_unset_because_property_is_immutable( $name );
	}

	/**
	 * Get the class name from a helper slug
	 *
	 * @param string $helper The name of the helper.
	 *
	 * @return string
	 */
	protected function get_helper_class( $helper ) {
		if ( \in_array( $helper, $this->capitalized_helpers, true ) ) {
			$helper = \strtoupper( $helper );
		}
		$helper = \implode( '_', \array_map( 'ucfirst', \explode( '_', $helper ) ) );
		return "Yoast\WP\SEO\Helpers\Schema\\{$helper}_Helper";
	}
}
