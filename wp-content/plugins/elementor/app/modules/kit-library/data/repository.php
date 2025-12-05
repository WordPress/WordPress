<?php
namespace Elementor\App\Modules\KitLibrary\Data;

use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Core\Utils\Collection;
use Elementor\Data\V2\Base\Exceptions\Error_404;
use Elementor\Data\V2\Base\Exceptions\WP_Error_Exception;
use Elementor\Modules\Library\User_Favorites;
use Elementor\App\Modules\KitLibrary\Connect\Kit_Library;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Repository {
	/**
	 * There is no label for subscription plan with access_level=0 + it should not
	 * be translated.
	 */
	const SUBSCRIPTION_PLAN_FREE_TAG = 'Free';

	const TAXONOMIES_KEYS = [ 'tags', 'categories', 'main_category', 'third_category', 'features', 'types' ];

	const KITS_CACHE_KEY = 'elementor_remote_kits';
	const KITS_TAXONOMIES_CACHE_KEY = 'elementor_remote_kits_taxonomies';

	const KITS_CACHE_TTL_HOURS = 12;
	const KITS_TAXONOMIES_CACHE_TTL_HOURS = 12;

	/**
	 * @var Kit_Library
	 */
	protected $api;

	/**
	 * @var User_Favorites
	 */
	protected $user_favorites;

	/**
	 * @var Collection
	 */
	protected $subscription_plans;

	/**
	 * Get all kits.
	 *
	 * @param false $force_api_request
	 *
	 * @return Collection
	 */
	public function get_all( $force_api_request = false ) {
		return $this->get_kits_data( $force_api_request )
			->map( function ( $kit ) {
				return $this->transform_kit_api_response( $kit );
			} );
	}

	/**
	 * Get specific kit.
	 *
	 * @param       $id
	 * @param array $options
	 *
	 * @return array|null
	 *
	 * @throws WP_Error_Exception If kit is not found.
	 */
	public function find( $id, $options = [] ) {
		$options = wp_parse_args( $options, [
			'manifest_included' => true,
		] );

		$item = $this->get_kits_data()
			->find( function ( $kit ) use ( $id ) {
				return $kit->_id === $id;
			} );

		if ( ! $item ) {
			return null;
		}

		$manifest = null;

		if ( $options['manifest_included'] ) {
			$manifest = $this->api->get_manifest( $id );

			if ( is_wp_error( $manifest ) ) {
				throw new WP_Error_Exception( esc_html( $manifest ) );
			}
		}

		return $this->transform_kit_api_response( $item, $manifest );
	}

	/**
	 * @param false $force_api_request
	 *
	 * @return Collection
	 */
	public function get_taxonomies( $force_api_request = false ) {
		return $this->get_taxonomies_data( $force_api_request )
			->only( static::TAXONOMIES_KEYS )
			->reduce( function ( Collection $carry, $taxonomies, $type ) {
				return $carry->merge( array_map( function ( $taxonomy ) use ( $type ) {
					return [
						'text' => $taxonomy->name,
						'type' => $type,
					];
				}, $taxonomies ) );
			}, new Collection( [] ) )
			->merge(
				$this->subscription_plans->map( function ( $label ) {
					return [
						'text' => $label ? $label : self::SUBSCRIPTION_PLAN_FREE_TAG,
						'type' => 'subscription_plans',
					];
				} )
			)
			->unique( [ 'text', 'type' ] );
	}

	/**
	 * @param $id
	 *
	 * @return array
	 *
	 * @throws WP_Error_Exception If download link retrieval fails or API errors occur.
	 */
	public function get_download_link( $id ) {
		$response = $this->api->download_link( $id );

		if ( is_wp_error( $response ) ) {
			throw new WP_Error_Exception( esc_html( $response ) );
		}

		return [ 'download_link' => $response->download_link ];
	}

	/**
	 * @param $id
	 *
	 * @return array
	 *
	 * @throws Error_404 If kit is not found.
	 */
	public function add_to_favorites( $id ) {
		$kit = $this->find( $id, [ 'manifest_included' => false ] );

		if ( ! $kit ) {
			throw new Error_404( esc_html__( 'Kit not found', 'elementor' ), 'kit_not_found' );
		}

		$this->user_favorites->add( 'elementor', 'kits', $kit['id'] );

		$kit['is_favorite'] = true;

		return $kit;
	}

	/**
	 * @param $id
	 *
	 * @return array
	 *
	 * @throws Error_404 If kit is not found.
	 */
	public function remove_from_favorites( $id ) {
		$kit = $this->find( $id, [ 'manifest_included' => false ] );

		if ( ! $kit ) {
			throw new Error_404( esc_html__( 'Kit not found', 'elementor' ), 'kit_not_found' );
		}

		$this->user_favorites->remove( 'elementor', 'kits', $kit['id'] );

		$kit['is_favorite'] = false;

		return $kit;
	}

	/**
	 * @param bool $force_api_request
	 *
	 * @return Collection
	 *
	 * @throws WP_Error_Exception If kits data retrieval fails.
	 */
	private function get_kits_data( $force_api_request = false ) {
		$data = get_transient( static::KITS_CACHE_KEY );

		$experiments_manager = Plugin::$instance->experiments;
		$kits_editor_layout_type = $experiments_manager->is_feature_active( 'container' ) ? 'container_flexbox' : '';

		if ( ! $data || $force_api_request ) {
			$args = [
				'body' => [
					'editor_layout_type' => $kits_editor_layout_type,
				],
			];

			/**
			 * Filters arguments for the request to the Kits API.
			 *
			 * @since 3.11.0
			 *
			 * @param array[] $args Array of http arguments.
			 */
			$args = apply_filters( 'elementor/kit-library/get-kits-data/args', $args );

			$data = $this->api->get_all( $args );

			if ( is_wp_error( $data ) ) {
				throw new WP_Error_Exception( esc_html( $data ) );
			}

			set_transient( static::KITS_CACHE_KEY, $data, static::KITS_CACHE_TTL_HOURS * HOUR_IN_SECONDS );
		}

		return new Collection( $data );
	}

	/**
	 * @param bool $force_api_request
	 *
	 * @return Collection
	 *
	 * @throws WP_Error_Exception If taxonomies data retrieval fails.
	 */
	private function get_taxonomies_data( $force_api_request = false ) {
		$data = get_transient( static::KITS_TAXONOMIES_CACHE_KEY );

		if ( ! $data || $force_api_request ) {
			$data = $this->api->get_taxonomies();

			if ( is_wp_error( $data ) ) {
				throw new WP_Error_Exception( esc_html( $data ) );
			}

			set_transient( static::KITS_TAXONOMIES_CACHE_KEY, $data, static::KITS_TAXONOMIES_CACHE_TTL_HOURS * HOUR_IN_SECONDS );
		}

		return new Collection( (array) $data );
	}

	/**
	 * @param      $kit
	 * @param null $manifest
	 *
	 * @return array
	 */
	private function transform_kit_api_response( $kit, $manifest = null ) {
		// BC: Support legacy APIs that don't have access tiers.
		if ( isset( $kit->access_tier ) ) {
			$access_tier = $kit->access_tier;
		} else {
			$access_tier = 0 === $kit->access_level
				? ConnectModule::ACCESS_TIER_FREE
				: ConnectModule::ACCESS_TIER_ESSENTIAL;
		}

		$subscription_plan_tag = $this->subscription_plans->get( $access_tier );

		$taxonomies = ( new Collection( ( (array) $kit )['taxonomies'] ) )
			->filter( function ( $taxonomy ) {
				return in_array( $taxonomy->type, self::TAXONOMIES_KEYS );
			} )
			->flatten()
			->pluck( 'name' )
			->push( $subscription_plan_tag ? $subscription_plan_tag : self::SUBSCRIPTION_PLAN_FREE_TAG );

		return array_merge(
			[
				'id' => $kit->_id,
				'title' => $kit->title,
				'thumbnail_url' => $kit->thumbnail,
				'access_level' => $kit->access_level,
				'access_tier' => $access_tier,
				'keywords' => $kit->keywords,
				'taxonomies' => $taxonomies->values(),
				'is_favorite' => $this->user_favorites->exists( 'elementor', 'kits', $kit->_id ),
				// TODO: Remove all the isset when the API stable.
				'trend_index' => isset( $kit->trend_index ) ? $kit->trend_index : 0,
				'featured_index' => isset( $kit->featured_index ) ? $kit->featured_index : 0,
				'popularity_index' => isset( $kit->popularity_index ) ? $kit->popularity_index : 0,
				'created_at' => isset( $kit->created_at ) ? $kit->created_at : null,
				'updated_at' => isset( $kit->updated_at ) ? $kit->updated_at : null,
			],
			$manifest ? $this->transform_manifest_api_response( $manifest ) : []
		);
	}

	/**
	 * @param $manifest
	 *
	 * @return array
	 */
	private function transform_manifest_api_response( $manifest ) {
		$manifest_content = ( new Collection( (array) $manifest->content ) )
			->reduce( function ( $carry, $content, $type ) {
				$mapped_documents = array_map( function ( $document ) use ( $type ) {
					// TODO: Fix it!
					// Hack to override a bug when a document with type of 'wp-page' is declared as 'wp-post'.
					if ( 'page' === $type ) {
						$document->doc_type = 'wp-page';
					}

					return $document;
				}, (array) $content );

				return $carry + $mapped_documents;
			}, [] );

		$content = ( new Collection( (array) $manifest->templates ) )
			->union( $manifest_content )
			->map( function ( $manifest_item, $key ) {
				return [
					'id' => isset( $manifest_item->id ) ? $manifest_item->id : $key,
					'title' => $manifest_item->title,
					'doc_type' => $manifest_item->doc_type,
					'thumbnail_url' => $manifest_item->thumbnail,
					'preview_url' => isset( $manifest_item->url ) ? $manifest_item->url : null,
				];
			} );

		return [
			'description' => $manifest->description,
			'preview_url' => isset( $manifest->site ) ? $manifest->site : '',
			'documents' => $content->values(),
		];
	}

	/**
	 * @param Kit_Library    $kit_library
	 * @param User_Favorites $user_favorites
	 * @param Collection     $subscription_plans
	 */
	public function __construct( Kit_Library $kit_library, User_Favorites $user_favorites, Collection $subscription_plans ) {
		$this->api = $kit_library;
		$this->user_favorites = $user_favorites;
		$this->subscription_plans = $subscription_plans;
	}

	public static function clear_cache() {
		delete_transient( static::KITS_CACHE_KEY );
	}
}
