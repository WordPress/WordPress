<?php

namespace Yoast\WP\Lib\Dependency_Injection;

use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Container_Registry class.
 */
class Container_Registry {

	/**
	 * The registered containers.
	 *
	 * @var ContainerInterface[]
	 */
	private static $containers = [];

	/**
	 * Register a container.
	 *
	 * @param string             $name      The name of the container.
	 * @param ContainerInterface $container The container.
	 *
	 * @return void
	 */
	public static function register( $name, ContainerInterface $container ) {
		self::$containers[ $name ] = $container;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Get an instance from a specific container.
	 *
	 * @param string $name              The name of the container.
	 * @param string $id                The ID of the service.
	 * @param int    $invalid_behaviour The behaviour when the service could not be found.
	 *
	 * @return object|null The service.
	 *
	 * @throws ServiceCircularReferenceException When a circular reference is detected.
	 * @throws ServiceNotFoundException          When the service is not defined.
	 */
	public static function get( $name, $id, $invalid_behaviour = 1 ) {
		if ( ! \array_key_exists( $name, self::$containers ) ) {
			if ( $invalid_behaviour === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE ) {
				throw new ServiceNotFoundException( $id );
			}
			return null;
		}
		return self::$containers[ $name ]->get( $id, $invalid_behaviour );
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

	/**
	 * Attempts to find a given service ID in all registered containers.
	 *
	 * @param string $id The service ID.
	 *
	 * @return string|null The name of the container if the service was found.
	 */
	public static function find( $id ) {
		foreach ( self::$containers as $name => $container ) {
			if ( $container->has( $id ) ) {
				return $name;
			}
		}
	}
}
