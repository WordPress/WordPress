<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Web_Stories_Conditional;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Integrations\Front_End_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Title_Presenter;

/**
 * Web Stories integration.
 */
class Web_Stories implements Integration_Interface {

	/**
	 * The front end integration.
	 *
	 * @var Front_End_Integration
	 */
	protected $front_end;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Web_Stories_Conditional::class ];
	}

	/**
	 * Constructs the Web Stories integration
	 *
	 * @param Front_End_Integration $front_end The front end integration.
	 */
	public function __construct( Front_End_Integration $front_end ) {
		$this->front_end = $front_end;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Disable default title and meta description output in the Web Stories plugin,
		// and force-add title & meta description presenter, regardless of theme support.
		\add_filter( 'web_stories_enable_document_title', '__return_false' );
		\add_filter( 'web_stories_enable_metadata', '__return_false' );
		\add_filter( 'wpseo_frontend_presenters', [ $this, 'filter_frontend_presenters' ], 10, 2 );

		\add_action( 'web_stories_enable_schemaorg_metadata', '__return_false' );
		\add_action( 'web_stories_enable_open_graph_metadata', '__return_false' );
		\add_action( 'web_stories_enable_twitter_metadata', '__return_false' );

		\add_action( 'web_stories_story_head', [ $this, 'web_stories_story_head' ], 1 );
		\add_filter( 'wpseo_schema_article_type', [ $this, 'filter_schema_article_type' ], 10, 2 );
		\add_filter( 'wpseo_metadesc', [ $this, 'filter_meta_description' ], 10, 2 );
	}

	/**
	 * Filter 'wpseo_frontend_presenters' - Allow filtering the presenter instances in or out of the request.
	 *
	 * @param array             $presenters The presenters.
	 * @param Meta_Tags_Context $context    The meta tags context for the current page.
	 * @return array Filtered presenters.
	 */
	public function filter_frontend_presenters( $presenters, $context ) {
		if ( $context->indexable->object_sub_type !== 'web-story' ) {
			return $presenters;
		}

		$has_title_presenter = false;

		foreach ( $presenters as $presenter ) {
			if ( $presenter instanceof Title_Presenter ) {
				$has_title_presenter = true;
			}
		}

		if ( ! $has_title_presenter ) {
			$presenters[] = new Title_Presenter();
		}

		return $presenters;
	}

	/**
	 * Hooks into web story <head> generation to modify output.
	 *
	 * @return void
	 */
	public function web_stories_story_head() {
		\remove_action( 'web_stories_story_head', 'rel_canonical' );
		\add_action( 'web_stories_story_head', [ $this->front_end, 'call_wpseo_head' ], 9 );
	}

	/**
	 * Filters the meta description for stories.
	 *
	 * @param string                 $description  The description sentence.
	 * @param Indexable_Presentation $presentation The presentation of an indexable.
	 * @return string The description sentence.
	 */
	public function filter_meta_description( $description, $presentation ) {
		if ( $description || $presentation->model->object_sub_type !== 'web-story' ) {
			return $description;
		}

		return \get_the_excerpt( $presentation->model->object_id );
	}

	/**
	 * Filters Article type for Web Stories.
	 *
	 * @param string|string[] $type      The Article type.
	 * @param Indexable       $indexable The indexable.
	 * @return string|string[] Article type.
	 */
	public function filter_schema_article_type( $type, $indexable ) {
		if ( $indexable->object_sub_type !== 'web-story' ) {
			return $type;
		}

		if ( \is_string( $type ) && $type === 'None' ) {
			return 'Article';
		}

		return $type;
	}
}
