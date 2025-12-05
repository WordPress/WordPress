<?php

namespace Yoast\WP\SEO\Memoizers;

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The presentation memoizer.
 */
class Presentation_Memoizer {

	/**
	 * The service container.
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Cache with indexable presentations.
	 *
	 * @var Indexable_Presentation[]
	 */
	protected $cache = [];

	/**
	 * Presentation_Memoizer constructor.
	 *
	 * @param ContainerInterface $service_container The service container.
	 */
	public function __construct( ContainerInterface $service_container ) {
		$this->container = $service_container;
	}

	/**
	 * Gets the presentation of an indexable for a specific page type.
	 * This function is memoized by the indexable so every call with the same indexable will yield the same result.
	 *
	 * @param Indexable         $indexable The indexable to get a presentation of.
	 * @param Meta_Tags_Context $context   The current meta tags context.
	 * @param string            $page_type The page type.
	 *
	 * @return Indexable_Presentation The indexable presentation.
	 */
	public function get( Indexable $indexable, Meta_Tags_Context $context, $page_type ) {
		if ( ! isset( $this->cache[ $indexable->id ] ) ) {
			$presentation = $this->container->get( "Yoast\WP\SEO\Presentations\Indexable_{$page_type}_Presentation", ContainerInterface::NULL_ON_INVALID_REFERENCE );

			if ( ! $presentation ) {
				$presentation = $this->container->get( Indexable_Presentation::class );
			}

			$context->presentation = $presentation->of(
				[
					'model'   => $indexable,
					'context' => $context,
				]
			);

			$this->cache[ $indexable->id ] = $context->presentation;
		}

		return $this->cache[ $indexable->id ];
	}

	/**
	 * Clears the memoization of either a specific indexable or all indexables.
	 *
	 * @param Indexable|int|null $indexable Optional. The indexable or indexable id to clear the memoization of.
	 *
	 * @return void
	 */
	public function clear( $indexable = null ) {
		if ( $indexable instanceof Indexable ) {
			unset( $this->cache[ $indexable->id ] );
			return;
		}
		if ( \is_int( $indexable ) ) {
			unset( $this->cache[ $indexable ] );
			return;
		}
		if ( $indexable === null ) {
			$this->cache = [];
		}
	}
}
