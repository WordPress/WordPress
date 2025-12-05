<?php

namespace Yoast\WP\SEO\Actions\Wincher;

use Exception;
use WP_Post;
use WPSEO_Utils;
use Yoast\WP\SEO\Config\Wincher_Client;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Class Wincher_Keyphrases_Action
 */
class Wincher_Keyphrases_Action {

	/**
	 * The Wincher keyphrase URL for bulk addition.
	 *
	 * @var string
	 */
	public const KEYPHRASES_ADD_URL = 'https://api.wincher.com/beta/websites/%s/keywords/bulk';

	/**
	 * The Wincher tracked keyphrase retrieval URL.
	 *
	 * @var string
	 */
	public const KEYPHRASES_URL = 'https://api.wincher.com/beta/yoast/%s';

	/**
	 * The Wincher delete tracked keyphrase URL.
	 *
	 * @var string
	 */
	public const KEYPHRASE_DELETE_URL = 'https://api.wincher.com/beta/websites/%s/keywords/%s';

	/**
	 * The Wincher_Client instance.
	 *
	 * @var Wincher_Client
	 */
	protected $client;

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The Indexable_Repository instance.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Wincher_Keyphrases_Action constructor.
	 *
	 * @param Wincher_Client       $client               The API client.
	 * @param Options_Helper       $options_helper       The options helper.
	 * @param Indexable_Repository $indexable_repository The indexables repository.
	 */
	public function __construct(
		Wincher_Client $client,
		Options_Helper $options_helper,
		Indexable_Repository $indexable_repository
	) {
		$this->client               = $client;
		$this->options_helper       = $options_helper;
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Sends the tracking API request for one or more keyphrases.
	 *
	 * @param string|array $keyphrases One or more keyphrases that should be tracked.
	 * @param Object       $limits     The limits API call response data.
	 *
	 * @return Object The reponse object.
	 */
	public function track_keyphrases( $keyphrases, $limits ) {
		try {
			$endpoint = \sprintf(
				self::KEYPHRASES_ADD_URL,
				$this->options_helper->get( 'wincher_website_id' )
			);

			// Enforce arrrays to ensure a consistent way of preparing the request.
			if ( ! \is_array( $keyphrases ) ) {
				$keyphrases = [ $keyphrases ];
			}

			// Calculate if the user would exceed their limit.
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- To ensure JS code style, this can be ignored.
			if ( ! $limits->canTrack || $this->would_exceed_limits( $keyphrases, $limits ) ) {
				$response = [
					'limit'  => $limits->limit,
					'error'  => 'Account limit exceeded',
					'status' => 400,
				];

				return $this->to_result_object( $response );
			}

			$formatted_keyphrases = \array_values(
				\array_map(
					static function ( $keyphrase ) {
						return [
							'keyword' => $keyphrase,
							'groups'  => [],
						];
					},
					$keyphrases
				)
			);

			$results = $this->client->post( $endpoint, WPSEO_Utils::format_json_encode( $formatted_keyphrases ) );

			if ( ! \array_key_exists( 'data', $results ) ) {
				return $this->to_result_object( $results );
			}

			// The endpoint returns a lot of stuff that we don't want/need.
			$results['data'] = \array_map(
				static function ( $keyphrase ) {
					return [
						'id'         => $keyphrase['id'],
						'keyword'    => $keyphrase['keyword'],
					];
				},
				$results['data']
			);

			$results['data'] = \array_combine(
				\array_column( $results['data'], 'keyword' ),
				\array_values( $results['data'] )
			);

			return $this->to_result_object( $results );
		} catch ( Exception $e ) {
			return (object) [
				'error'  => $e->getMessage(),
				'status' => $e->getCode(),
			];
		}
	}

	/**
	 * Sends an untrack request for the passed keyword ID.
	 *
	 * @param int $keyphrase_id The ID of the keyphrase to untrack.
	 *
	 * @return object The response object.
	 */
	public function untrack_keyphrase( $keyphrase_id ) {
		try {
			$endpoint = \sprintf(
				self::KEYPHRASE_DELETE_URL,
				$this->options_helper->get( 'wincher_website_id' ),
				$keyphrase_id
			);

			$this->client->delete( $endpoint );

			return (object) [
				'status' => 200,
			];
		} catch ( Exception $e ) {
			return (object) [
				'error'  => $e->getMessage(),
				'status' => $e->getCode(),
			];
		}
	}

	/**
	 * Gets the keyphrase data for the passed keyphrases.
	 * Retrieves all available data if no keyphrases are provided.
	 *
	 * @param array|null  $used_keyphrases The currently used keyphrases. Optional.
	 * @param string|null $permalink       The current permalink. Optional.
	 * @param string|null $start_at        The position start date. Optional.
	 *
	 * @return object The keyphrase chart data.
	 */
	public function get_tracked_keyphrases( $used_keyphrases = null, $permalink = null, $start_at = null ) {
		try {
			if ( $used_keyphrases === null ) {
				$used_keyphrases = $this->collect_all_keyphrases();
			}

			// If we still have no keyphrases the API will return an error, so
			// don't even bother sending a request.
			if ( empty( $used_keyphrases ) ) {
				return $this->to_result_object(
					[
						'data'   => [],
						'status' => 200,
					]
				);
			}

			$endpoint = \sprintf(
				self::KEYPHRASES_URL,
				$this->options_helper->get( 'wincher_website_id' )
			);

			$results = $this->client->post(
				$endpoint,
				WPSEO_Utils::format_json_encode(
					[
						'keywords' => $used_keyphrases,
						'url'      => $permalink,
						'start_at' => $start_at,
					]
				),
				[
					'timeout' => 60,
				]
			);

			if ( ! \array_key_exists( 'data', $results ) ) {
				return $this->to_result_object( $results );
			}

			$results['data'] = $this->filter_results_by_used_keyphrases( $results['data'], $used_keyphrases );

			// Extract the positional data and assign it to the keyphrase.
			$results['data'] = \array_combine(
				\array_column( $results['data'], 'keyword' ),
				\array_values( $results['data'] )
			);

			return $this->to_result_object( $results );
		} catch ( Exception $e ) {
			return (object) [
				'error'  => $e->getMessage(),
				'status' => $e->getCode(),
			];
		}
	}

	/**
	 * Collects the keyphrases associated with the post.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return array The keyphrases.
	 */
	public function collect_keyphrases_from_post( $post ) {
		$keyphrases        = [];
		$primary_keyphrase = $this->indexable_repository
			->query()
			->select( 'primary_focus_keyword' )
			->where( 'object_id', $post->ID )
			->find_one();

		if ( $primary_keyphrase ) {
			$keyphrases[] = $primary_keyphrase->primary_focus_keyword;
		}

		/**
		 * Filters the keyphrases collected by the Wincher integration from the post.
		 *
		 * @param array $keyphrases The keyphrases array.
		 * @param int   $post_id    The ID of the post.
		 */
		return \apply_filters( 'wpseo_wincher_keyphrases_from_post', $keyphrases, $post->ID );
	}

	/**
	 * Collects all keyphrases known to Yoast.
	 *
	 * @return array
	 */
	protected function collect_all_keyphrases() {
		// Collect primary keyphrases first.
		$keyphrases = \array_column(
			$this->indexable_repository
				->query()
				->select( 'primary_focus_keyword' )
				->where_not_null( 'primary_focus_keyword' )
				->where( 'object_type', 'post' )
				->where_not_equal( 'post_status', 'trash' )
				->distinct()
				->find_array(),
			'primary_focus_keyword'
		);

		/**
		 * Filters the keyphrases collected by the Wincher integration from all the posts.
		 *
		 * @param array $keyphrases The keyphrases array.
		 */
		$keyphrases = \apply_filters( 'wpseo_wincher_all_keyphrases', $keyphrases );

		// Filter out empty entries.
		return \array_filter( $keyphrases );
	}

	/**
	 * Filters the results based on the passed keyphrases.
	 *
	 * @param array $results         The results to filter.
	 * @param array $used_keyphrases The used keyphrases.
	 *
	 * @return array The filtered results.
	 */
	protected function filter_results_by_used_keyphrases( $results, $used_keyphrases ) {
		return \array_filter(
			$results,
			static function ( $result ) use ( $used_keyphrases ) {
				return \in_array( $result['keyword'], \array_map( 'strtolower', $used_keyphrases ), true );
			}
		);
	}

	/**
	 * Determines whether the amount of keyphrases would mean the user exceeds their account limits.
	 *
	 * @param string|array $keyphrases The keyphrases to be added.
	 * @param object       $limits     The current account limits.
	 *
	 * @return bool Whether the limit is exceeded.
	 */
	protected function would_exceed_limits( $keyphrases, $limits ) {
		if ( ! \is_array( $keyphrases ) ) {
			$keyphrases = [ $keyphrases ];
		}

		if ( $limits->limit === null ) {
			return false;
		}

		return ( \count( $keyphrases ) + $limits->usage ) > $limits->limit;
	}

	/**
	 * Converts the passed dataset to an object.
	 *
	 * @param array $result The result dataset to convert to an object.
	 *
	 * @return object The result object.
	 */
	protected function to_result_object( $result ) {
		if ( \array_key_exists( 'data', $result ) ) {
			$result['results'] = (object) $result['data'];

			unset( $result['data'] );
		}

		if ( \array_key_exists( 'message', $result ) ) {
			$result['error'] = $result['message'];

			unset( $result['message'] );
		}

		return (object) $result;
	}
}
