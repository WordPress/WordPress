<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\User_Interface\Scores;

use Exception;
use WP_REST_Request;
use WP_REST_Response;
use WPSEO_Capability_Utils;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Dashboard\Application\Score_Results\Abstract_Score_Results_Repository;
use Yoast\WP\SEO\Dashboard\Application\Taxonomies\Taxonomies_Repository;
use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;
use Yoast\WP\SEO\Dashboard\Infrastructure\Content_Types\Content_Types_Collector;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Abstract scores route.
 */
abstract class Abstract_Scores_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * The namespace of the rout.
	 *
	 * @var string
	 */
	public const ROUTE_NAMESPACE = Main::API_V1_NAMESPACE;

	/**
	 * The prefix of the rout.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = null;

	/**
	 * The content types collector.
	 *
	 * @var Content_Types_Collector
	 */
	protected $content_types_collector;

	/**
	 * The taxonomies repository.
	 *
	 * @var Taxonomies_Repository
	 */
	protected $taxonomies_repository;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * The scores repository.
	 *
	 * @var Abstract_Score_Results_Repository
	 */
	protected $score_results_repository;

	/**
	 * Sets the collectors.
	 *
	 * @required
	 *
	 * @param Content_Types_Collector $content_types_collector The content type collector.
	 *
	 * @return void
	 */
	public function set_collectors( Content_Types_Collector $content_types_collector ) {
		$this->content_types_collector = $content_types_collector;
	}

	/**
	 * Sets the repositories.
	 *
	 * @required
	 *
	 * @param Taxonomies_Repository $taxonomies_repository The taxonomies repository.
	 * @param Indexable_Repository  $indexable_repository  The indexable repository.
	 *
	 * @return void
	 */
	public function set_repositories(
		Taxonomies_Repository $taxonomies_repository,
		Indexable_Repository $indexable_repository
	) {
		$this->taxonomies_repository = $taxonomies_repository;
		$this->indexable_repository  = $indexable_repository;
	}

	/**
	 * Returns the route prefix.
	 *
	 * @return string The route prefix.
	 *
	 * @throws Exception If the ROUTE_PREFIX constant is not set in the child class.
	 */
	public static function get_route_prefix() {
		$class  = static::class;
		$prefix = $class::ROUTE_PREFIX;

		if ( $prefix === null ) {
			throw new Exception( 'Score route without explicit prefix' );
		}

		return $prefix;
	}

	/**
	 * Registers routes for scores.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			self::ROUTE_NAMESPACE,
			$this->get_route_prefix(),
			[
				[
					'methods'             => 'GET',
					'callback'            => [ $this, 'get_scores' ],
					'permission_callback' => [ $this, 'permission_manage_options' ],
					'args'                => [
						'contentType' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'taxonomy' => [
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'term' => [
							'required'          => false,
							'type'              => 'integer',
							'default'           => null,
							'sanitize_callback' => static function ( $param ) {
								return \intval( $param );
							},
						],
						'troubleshooting' => [
							'required'          => false,
							'type'              => 'bool',
							'default'           => null,
							'sanitize_callback' => 'rest_sanitize_boolean',
						],
					],
				],
			]
		);
	}

	/**
	 * Gets the scores of a specific content type.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The success or failure response.
	 */
	public function get_scores( WP_REST_Request $request ) {
		try {
			$content_type = $this->get_content_type( $request['contentType'] );
			$taxonomy     = $this->get_taxonomy( $request['taxonomy'], $content_type );
			$term_id      = $this->get_validated_term_id( $request['term'], $taxonomy );

			$results = $this->score_results_repository->get_score_results( $content_type, $taxonomy, $term_id, $request['troubleshooting'] );
		} catch ( Exception $exception ) {
			return new WP_REST_Response(
				[
					'error' => $exception->getMessage(),
				],
				$exception->getCode()
			);
		}

		return new WP_REST_Response(
			$results,
			200
		);
	}

	/**
	 * Gets the content type object.
	 *
	 * @param string $content_type The content type.
	 *
	 * @return Content_Type|null The content type object.
	 *
	 * @throws Exception When the content type is invalid.
	 */
	protected function get_content_type( string $content_type ): ?Content_Type {
		$content_types = $this->content_types_collector->get_content_types()->get();

		if ( isset( $content_types[ $content_type ] ) && \is_a( $content_types[ $content_type ], Content_Type::class ) ) {
			return $content_types[ $content_type ];
		}

		throw new Exception( 'Invalid content type.', 400 );
	}

	/**
	 * Gets the taxonomy object.
	 *
	 * @param string       $taxonomy     The taxonomy.
	 * @param Content_Type $content_type The content type that the taxonomy is filtering.
	 *
	 * @return Taxonomy|null The taxonomy object.
	 *
	 * @throws Exception When the taxonomy is invalid.
	 */
	protected function get_taxonomy( string $taxonomy, Content_Type $content_type ): ?Taxonomy {
		if ( $taxonomy === '' ) {
			return null;
		}

		$valid_taxonomy = $this->taxonomies_repository->get_content_type_taxonomy( $content_type->get_name() );

		if ( $valid_taxonomy && $valid_taxonomy->get_name() === $taxonomy ) {
			return $valid_taxonomy;
		}

		throw new Exception( 'Invalid taxonomy.', 400 );
	}

	/**
	 * Gets the term ID validated against the given taxonomy.
	 *
	 * @param int|null      $term_id  The term ID to be validated.
	 * @param Taxonomy|null $taxonomy The taxonomy.
	 *
	 * @return int|null The validated term ID.
	 *
	 * @throws Exception When the term id is invalidated.
	 */
	protected function get_validated_term_id( ?int $term_id, ?Taxonomy $taxonomy ): ?int {
		if ( $term_id !== null && $taxonomy === null ) {
			throw new Exception( 'Term needs a provided taxonomy.', 400 );
		}

		if ( $term_id === null && $taxonomy !== null ) {
			throw new Exception( 'Taxonomy needs a provided term.', 400 );
		}

		if ( $term_id !== null ) {
			$term = \get_term( $term_id );
			if ( ! $term || \is_wp_error( $term ) ) {
				throw new Exception( 'Invalid term.', 400 );
			}

			if ( $taxonomy !== null && $term->taxonomy !== $taxonomy->get_name() ) {
				throw new Exception( 'Invalid term.', 400 );
			}
		}

		return $term_id;
	}

	/**
	 * Permission callback.
	 *
	 * @return bool True when user has the 'wpseo_manage_options' capability.
	 */
	public function permission_manage_options() {
		return WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' );
	}
}
