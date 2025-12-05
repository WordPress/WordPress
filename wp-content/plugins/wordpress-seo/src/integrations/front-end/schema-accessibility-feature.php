<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;
use Yoast\WP\SEO\Generators\Schema\Article;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Adds the table of contents accessibility feature to the article piece with a fallback to the webpage piece.
 */
class Schema_Accessibility_Feature implements Integration_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_schema_webpage', [ $this, 'maybe_add_accessibility_feature' ], 10, 4 );
		\add_filter( 'wpseo_schema_article', [ $this, 'add_accessibility_feature' ], 10, 2 );
	}

	/**
	 * Adds the accessibility feature to the webpage if there is no article.
	 *
	 * @param array                   $piece         The graph piece.
	 * @param Meta_Tags_Context       $context       The context.
	 * @param Abstract_Schema_Piece   $the_generator The current schema generator.
	 * @param Abstract_Schema_Piece[] $generators    The schema generators.
	 *
	 * @return array The graph piece.
	 */
	public function maybe_add_accessibility_feature( $piece, $context, $the_generator, $generators ) {
		foreach ( $generators as $generator ) {
			if ( \is_a( $generator, Article::class ) && $generator->is_needed() ) {
				return $piece;
			}
		}

		return $this->add_accessibility_feature( $piece, $context );
	}

	/**
	 * Adds the accessibility feature to a schema graph piece.
	 *
	 * @param array             $piece   The schema piece.
	 * @param Meta_Tags_Context $context The context.
	 *
	 * @return array The graph piece.
	 */
	public function add_accessibility_feature( $piece, $context ) {
		if ( empty( $context->blocks['yoast-seo/table-of-contents'] ) ) {
			return $piece;
		}

		if ( isset( $piece['accessibilityFeature'] ) ) {
			$piece['accessibilityFeature'][] = 'tableOfContents';
		}
		else {
			$piece['accessibilityFeature'] = [
				'tableOfContents',
			];
		}
		return $piece;
	}
}
