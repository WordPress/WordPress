<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Actions\Importing\Aioseo;

use wpdb;
use Yoast\WP\SEO\Actions\Importing\Abstract_Aioseo_Importing_Action;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Import_Cursor_Helper;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Indexable_To_Postmeta_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Sanitization_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Replacevar_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Provider_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Transformer_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Social_Images_Provider_Service;

/**
 * Importing action for AIOSEO post data.
 */
class Aioseo_Posts_Importing_Action extends Abstract_Aioseo_Importing_Action {

	/**
	 * The plugin of the action.
	 */
	public const PLUGIN = 'aioseo';

	/**
	 * The type of the action.
	 */
	public const TYPE = 'posts';

	/**
	 * The map of aioseo to yoast meta.
	 *
	 * @var array<string, array<string, string|bool|array<string, string|bool>>>
	 */
	protected $aioseo_to_yoast_map = [
		'title'               => [
			'yoast_name'       => 'title',
			'transform_method' => 'simple_import_post',
		],
		'description'         => [
			'yoast_name'       => 'description',
			'transform_method' => 'simple_import_post',
		],
		'og_title'            => [
			'yoast_name'       => 'open_graph_title',
			'transform_method' => 'simple_import_post',
		],
		'og_description'      => [
			'yoast_name'       => 'open_graph_description',
			'transform_method' => 'simple_import_post',
		],
		'twitter_title'       => [
			'yoast_name'       => 'twitter_title',
			'transform_method' => 'simple_import_post',
			'twitter_import'   => true,
		],
		'twitter_description' => [
			'yoast_name'       => 'twitter_description',
			'transform_method' => 'simple_import_post',
			'twitter_import'   => true,
		],
		'canonical_url'       => [
			'yoast_name'       => 'canonical',
			'transform_method' => 'url_import_post',
		],
		'keyphrases'          => [
			'yoast_name'       => 'primary_focus_keyword',
			'transform_method' => 'keyphrase_import',
		],
		'og_image_url'        => [
			'yoast_name'                   => 'open_graph_image',
			'social_image_import'          => true,
			'social_setting_prefix_aioseo' => 'og_',
			'social_setting_prefix_yoast'  => 'open_graph_',
			'transform_method'             => 'social_image_url_import',
		],
		'twitter_image_url'   => [
			'yoast_name'                   => 'twitter_image',
			'social_image_import'          => true,
			'social_setting_prefix_aioseo' => 'twitter_',
			'social_setting_prefix_yoast'  => 'twitter_',
			'transform_method'             => 'social_image_url_import',
		],
		'robots_noindex'      => [
			'yoast_name'       => 'is_robots_noindex',
			'transform_method' => 'post_robots_noindex_import',
			'robots_import'    => true,
		],
		'robots_nofollow'     => [
			'yoast_name'       => 'is_robots_nofollow',
			'transform_method' => 'post_general_robots_import',
			'robots_import'    => true,
			'robot_type'       => 'nofollow',
		],
		'robots_noarchive'    => [
			'yoast_name'       => 'is_robots_noarchive',
			'transform_method' => 'post_general_robots_import',
			'robots_import'    => true,
			'robot_type'       => 'noarchive',
		],
		'robots_nosnippet'    => [
			'yoast_name'       => 'is_robots_nosnippet',
			'transform_method' => 'post_general_robots_import',
			'robots_import'    => true,
			'robot_type'       => 'nosnippet',
		],
		'robots_noimageindex' => [
			'yoast_name'       => 'is_robots_noimageindex',
			'transform_method' => 'post_general_robots_import',
			'robots_import'    => true,
			'robot_type'       => 'noimageindex',
		],
	];

	/**
	 * Represents the indexables repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * The WordPress database instance.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image;

	/**
	 * The indexable_to_postmeta helper.
	 *
	 * @var Indexable_To_Postmeta_Helper
	 */
	protected $indexable_to_postmeta;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * The social images provider service.
	 *
	 * @var Aioseo_Social_Images_Provider_Service
	 */
	protected $social_images_provider;

	/**
	 * Class constructor.
	 *
	 * @param Indexable_Repository                  $indexable_repository   The indexables repository.
	 * @param wpdb                                  $wpdb                   The WordPress database instance.
	 * @param Import_Cursor_Helper                  $import_cursor          The import cursor helper.
	 * @param Indexable_Helper                      $indexable_helper       The indexable helper.
	 * @param Indexable_To_Postmeta_Helper          $indexable_to_postmeta  The indexable_to_postmeta helper.
	 * @param Options_Helper                        $options                The options helper.
	 * @param Image_Helper                          $image                  The image helper.
	 * @param Sanitization_Helper                   $sanitization           The sanitization helper.
	 * @param Aioseo_Replacevar_Service             $replacevar_handler     The replacevar handler.
	 * @param Aioseo_Robots_Provider_Service        $robots_provider        The robots provider service.
	 * @param Aioseo_Robots_Transformer_Service     $robots_transformer     The robots transfomer service.
	 * @param Aioseo_Social_Images_Provider_Service $social_images_provider The social images provider service.
	 */
	public function __construct(
		Indexable_Repository $indexable_repository,
		wpdb $wpdb,
		Import_Cursor_Helper $import_cursor,
		Indexable_Helper $indexable_helper,
		Indexable_To_Postmeta_Helper $indexable_to_postmeta,
		Options_Helper $options,
		Image_Helper $image,
		Sanitization_Helper $sanitization,
		Aioseo_Replacevar_Service $replacevar_handler,
		Aioseo_Robots_Provider_Service $robots_provider,
		Aioseo_Robots_Transformer_Service $robots_transformer,
		Aioseo_Social_Images_Provider_Service $social_images_provider
	) {
		parent::__construct( $import_cursor, $options, $sanitization, $replacevar_handler, $robots_provider, $robots_transformer );

		$this->indexable_repository   = $indexable_repository;
		$this->wpdb                   = $wpdb;
		$this->image                  = $image;
		$this->indexable_helper       = $indexable_helper;
		$this->indexable_to_postmeta  = $indexable_to_postmeta;
		$this->social_images_provider = $social_images_provider;
	}

	// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: They are already prepared.

	/**
	 * Returns the total number of unimported objects.
	 *
	 * @return int The total number of unimported objects.
	 */
	public function get_total_unindexed() {
		if ( ! $this->aioseo_helper->aioseo_exists() ) {
			return 0;
		}

		$limit                = false;
		$just_detect          = true;
		$indexables_to_create = $this->wpdb->get_col( $this->query( $limit, $just_detect ) );

		$number_of_indexables_to_create = \count( $indexables_to_create );
		$completed                      = $number_of_indexables_to_create === 0;
		$this->set_completed( $completed );

		return $number_of_indexables_to_create;
	}

	/**
	 * Returns the limited number of unimported objects.
	 *
	 * @param int $limit The maximum number of unimported objects to be returned.
	 *
	 * @return int|false The limited number of unindexed posts. False if the query fails.
	 */
	public function get_limited_unindexed_count( $limit ) {
		if ( ! $this->aioseo_helper->aioseo_exists() ) {
			return 0;
		}

		$just_detect          = true;
		$indexables_to_create = $this->wpdb->get_col( $this->query( $limit, $just_detect ) );

		$number_of_indexables_to_create = \count( $indexables_to_create );
		$completed                      = $number_of_indexables_to_create === 0;
		$this->set_completed( $completed );

		return $number_of_indexables_to_create;
	}

	/**
	 * Imports AIOSEO meta data and creates the respective Yoast indexables and postmeta.
	 *
	 * @return Indexable[]|false An array of created indexables or false if aioseo data was not found.
	 */
	public function index() {
		if ( ! $this->aioseo_helper->aioseo_exists() ) {
			return false;
		}

		$limit              = $this->get_limit();
		$aioseo_indexables  = $this->wpdb->get_results( $this->query( $limit ), \ARRAY_A );
		$created_indexables = [];

		$completed = \count( $aioseo_indexables ) === 0;
		$this->set_completed( $completed );

		// Let's build the list of fields to check their defaults, to identify whether we're gonna import AIOSEO data in the indexable or not.
		$check_defaults_fields = [];
		foreach ( $this->aioseo_to_yoast_map as $yoast_mapping ) {
			// We don't want to check all the imported fields.
			if ( ! \in_array( $yoast_mapping['yoast_name'], [ 'open_graph_image', 'twitter_image' ], true ) ) {
				$check_defaults_fields[] = $yoast_mapping['yoast_name'];
			}
		}

		$last_indexed_aioseo_id = 0;
		foreach ( $aioseo_indexables as $aioseo_indexable ) {
			$last_indexed_aioseo_id = $aioseo_indexable['id'];

			$indexable = $this->indexable_repository->find_by_id_and_type( $aioseo_indexable['post_id'], 'post' );

			// Let's ensure that the current post id represents something that we want to index (eg. *not* shop_order).
			if ( ! \is_a( $indexable, 'Yoast\WP\SEO\Models\Indexable' ) ) {
				continue;
			}

			if ( $this->indexable_helper->check_if_default_indexable( $indexable, $check_defaults_fields ) ) {
				$indexable = $this->map( $indexable, $aioseo_indexable );
				$this->indexable_helper->save_indexable( $indexable );

				// To ensure that indexables can be rebuild after a reset, we have to store the data in the postmeta table too.
				$this->indexable_to_postmeta->map_to_postmeta( $indexable );
			}

			$last_indexed_aioseo_id = $aioseo_indexable['id'];

			$created_indexables[] = $indexable;
		}

		$cursor_id = $this->get_cursor_id();
		$this->import_cursor->set_cursor( $cursor_id, $last_indexed_aioseo_id );

		return $created_indexables;
	}

	// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

	/**
	 * Maps AIOSEO meta data to Yoast meta data.
	 *
	 * @param Indexable $indexable        The Yoast indexable.
	 * @param array     $aioseo_indexable The AIOSEO indexable.
	 *
	 * @return Indexable The created indexables.
	 */
	public function map( $indexable, $aioseo_indexable ) {
		foreach ( $this->aioseo_to_yoast_map as $aioseo_key => $yoast_mapping ) {
			// For robots import.
			if ( isset( $yoast_mapping['robots_import'] ) && $yoast_mapping['robots_import'] ) {
				$yoast_mapping['subtype']                  = $indexable->object_sub_type;
				$indexable->{$yoast_mapping['yoast_name']} = $this->transform_import_data( $yoast_mapping['transform_method'], $aioseo_indexable, $aioseo_key, $yoast_mapping, $indexable );

				continue;
			}

			// For social images, like open graph and twitter image.
			if ( isset( $yoast_mapping['social_image_import'] ) && $yoast_mapping['social_image_import'] ) {
				$image_url = $this->transform_import_data( $yoast_mapping['transform_method'], $aioseo_indexable, $aioseo_key, $yoast_mapping, $indexable );

				// Update the indexable's social image only where there's actually a url to import, so as not to lose the social images that we came up with when we originally built the indexable.
				if ( ! empty( $image_url ) ) {
					$indexable->{$yoast_mapping['yoast_name']} = $image_url;

					$image_source_key             = $yoast_mapping['social_setting_prefix_yoast'] . 'image_source';
					$indexable->$image_source_key = 'imported';

					$image_id_key             = $yoast_mapping['social_setting_prefix_yoast'] . 'image_id';
					$indexable->$image_id_key = $this->image->get_attachment_by_url( $image_url );

					if ( $yoast_mapping['yoast_name'] === 'open_graph_image' ) {
						$indexable->open_graph_image_meta = null;
					}
				}
				continue;
			}

			// For twitter import, take the respective open graph data if the appropriate setting is enabled.
			if ( isset( $yoast_mapping['twitter_import'] ) && $yoast_mapping['twitter_import'] && $aioseo_indexable['twitter_use_og'] ) {
				$aioseo_indexable['twitter_title']       = $aioseo_indexable['og_title'];
				$aioseo_indexable['twitter_description'] = $aioseo_indexable['og_description'];
			}

			if ( ! empty( $aioseo_indexable[ $aioseo_key ] ) ) {
				$indexable->{$yoast_mapping['yoast_name']} = $this->transform_import_data( $yoast_mapping['transform_method'], $aioseo_indexable, $aioseo_key, $yoast_mapping, $indexable );
			}
		}

		return $indexable;
	}

	/**
	 * Transforms the data to be imported.
	 *
	 * @param string    $transform_method The method that is going to be used for transforming the data.
	 * @param array     $aioseo_indexable The data of the AIOSEO indexable data that is being imported.
	 * @param string    $aioseo_key       The name of the specific set of data that is going to be transformed.
	 * @param array     $yoast_mapping    Extra details for the import of the specific data that is going to be transformed.
	 * @param Indexable $indexable        The Yoast indexable that we are going to import the transformed data into.
	 *
	 * @return string|bool|null The transformed data to be imported.
	 */
	protected function transform_import_data( $transform_method, $aioseo_indexable, $aioseo_key, $yoast_mapping, $indexable ) {
		return \call_user_func( [ $this, $transform_method ], $aioseo_indexable, $aioseo_key, $yoast_mapping, $indexable );
	}

	/**
	 * Returns the number of objects that will be imported in a single importing pass.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		/**
		 * Filter 'wpseo_aioseo_post_indexation_limit' - Allow filtering the number of posts indexed during each indexing pass.
		 *
		 * @param int $max_posts The maximum number of posts indexed.
		 */
		$limit = \apply_filters( 'wpseo_aioseo_post_indexation_limit', 25 );

		if ( ! \is_int( $limit ) || $limit < 1 ) {
			$limit = 25;
		}

		return $limit;
	}

	/**
	 * Populates the needed data array based on which columns we use from the AIOSEO indexable table.
	 *
	 * @return array The needed data array that contains all the needed columns.
	 */
	public function get_needed_data() {
		$needed_data = \array_keys( $this->aioseo_to_yoast_map );
		\array_push( $needed_data, 'id', 'post_id', 'robots_default', 'og_image_custom_url', 'og_image_type', 'twitter_image_custom_url', 'twitter_image_type', 'twitter_use_og' );

		return $needed_data;
	}

	/**
	 * Populates the needed robot data array to be used in validating against its structure.
	 *
	 * @return array The needed data array that contains all the needed columns.
	 */
	public function get_needed_robot_data() {
		$needed_robot_data = [];

		foreach ( $this->aioseo_to_yoast_map as $yoast_mapping ) {
			if ( isset( $yoast_mapping['robot_type'] ) ) {
				$needed_robot_data[] = $yoast_mapping['robot_type'];
			}
		}

		return $needed_robot_data;
	}

	/**
	 * Creates a query for gathering AiOSEO data from the database.
	 *
	 * @param int|false $limit       The maximum number of unimported objects to be returned.
	 *                               False for "no limit".
	 * @param bool      $just_detect Whether we want to just detect if there are unimported objects. If false, we want to actually import them too.
	 *
	 * @return string The query to use for importing or counting the number of items to import.
	 */
	public function query( $limit = false, $just_detect = false ) {
		$table = $this->aioseo_helper->get_table();

		$select_statement = 'id';
		if ( ! $just_detect ) {
			// If we want to import too, we need the actual needed data from AIOSEO indexables.
			$needed_data = $this->get_needed_data();

			$select_statement = \implode( ', ', $needed_data );
		}

		$cursor_id = $this->get_cursor_id();
		$cursor    = $this->import_cursor->get_cursor( $cursor_id );

		/**
		 * Filter 'wpseo_aioseo_post_cursor' - Allow filtering the value of the aioseo post import cursor.
		 *
		 * @param int $import_cursor The value of the aioseo post import cursor.
		 */
		$cursor = \apply_filters( 'wpseo_aioseo_post_import_cursor', $cursor );

		$replacements = [ $cursor ];

		$limit_statement = '';
		if ( ! empty( $limit ) ) {
			$replacements[]  = $limit;
			$limit_statement = ' LIMIT %d';
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		return $this->wpdb->prepare(
			"SELECT {$select_statement} FROM {$table} WHERE id > %d ORDER BY id{$limit_statement}",
			$replacements
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Minimally transforms data to be imported.
	 *
	 * @param array  $aioseo_data All of the AIOSEO data to be imported.
	 * @param string $aioseo_key  The AIOSEO key that contains the setting we're working with.
	 *
	 * @return string The transformed meta data.
	 */
	public function simple_import_post( $aioseo_data, $aioseo_key ) {
		return $this->simple_import( $aioseo_data[ $aioseo_key ] );
	}

	/**
	 * Transforms URL to be imported.
	 *
	 * @param array  $aioseo_data All of the AIOSEO data to be imported.
	 * @param string $aioseo_key  The AIOSEO key that contains the setting we're working with.
	 *
	 * @return string The transformed URL.
	 */
	public function url_import_post( $aioseo_data, $aioseo_key ) {
		return $this->url_import( $aioseo_data[ $aioseo_key ] );
	}

	/**
	 * Plucks the keyphrase to be imported from the AIOSEO array of keyphrase meta data.
	 *
	 * @param array  $aioseo_data All of the AIOSEO data to be imported.
	 * @param string $aioseo_key  The AIOSEO key that contains the setting we're working with, aka keyphrases.
	 *
	 * @return string|null The plucked keyphrase.
	 */
	public function keyphrase_import( $aioseo_data, $aioseo_key ) {
		$meta_data = \json_decode( $aioseo_data[ $aioseo_key ], true );
		if ( ! isset( $meta_data['focus']['keyphrase'] ) ) {
			return null;
		}

		return $this->sanitization->sanitize_text_field( $meta_data['focus']['keyphrase'] );
	}

	/**
	 * Imports the post's noindex setting.
	 *
	 * @param bool $aioseo_robots_settings AIOSEO's set of robot settings for the post.
	 *
	 * @return bool|null The value of Yoast's noindex setting for the post.
	 */
	public function post_robots_noindex_import( $aioseo_robots_settings ) {
		// If robot settings defer to default settings, we have null in the is_robots_noindex field.
		if ( $aioseo_robots_settings['robots_default'] ) {
			return null;
		}

		return $aioseo_robots_settings['robots_noindex'];
	}

	/**
	 * Imports the post's robots setting.
	 *
	 * @param bool   $aioseo_robots_settings AIOSEO's set of robot settings for the post.
	 * @param string $aioseo_key             The AIOSEO key that contains the robot setting we're working with.
	 * @param array  $mapping                The mapping of the setting we're working with.
	 *
	 * @return bool|null The value of Yoast's noindex setting for the post.
	 */
	public function post_general_robots_import( $aioseo_robots_settings, $aioseo_key, $mapping ) {
		$mapping = $this->enhance_mapping( $mapping );

		if ( $aioseo_robots_settings['robots_default'] ) {
			// Let's first get the subtype's setting value and then transform it taking into consideration whether it defers to global defaults.
			$subtype_setting = $this->robots_provider->get_subtype_robot_setting( $mapping );
			return $this->robots_transformer->transform_robot_setting( $mapping['robot_type'], $subtype_setting, $mapping );
		}

		return $aioseo_robots_settings[ $aioseo_key ];
	}

	/**
	 * Enhances the mapping of the setting we're working with, with type and the option name, so that we can retrieve the settings for the object we're working with.
	 *
	 * @param array $mapping The mapping of the setting we're working with.
	 *
	 * @return array The enhanced mapping.
	 */
	public function enhance_mapping( $mapping = [] ) {
		$mapping['type']        = 'postTypes';
		$mapping['option_name'] = 'aioseo_options_dynamic';

		return $mapping;
	}

	/**
	 * Imports the og and twitter image url.
	 *
	 * @param bool      $aioseo_social_image_settings AIOSEO's set of social image settings for the post.
	 * @param string    $aioseo_key                   The AIOSEO key that contains the robot setting we're working with.
	 * @param array     $mapping                      The mapping of the setting we're working with.
	 * @param Indexable $indexable                    The Yoast indexable we're importing into.
	 *
	 * @return bool|null The url of the social image we're importing, null if there's none.
	 */
	public function social_image_url_import( $aioseo_social_image_settings, $aioseo_key, $mapping, $indexable ) {
		if ( $mapping['social_setting_prefix_aioseo'] === 'twitter_' && $aioseo_social_image_settings['twitter_use_og'] ) {
			$mapping['social_setting_prefix_aioseo'] = 'og_';
		}

		$social_setting = \rtrim( $mapping['social_setting_prefix_aioseo'], '_' );

		$image_type = $aioseo_social_image_settings[ $mapping['social_setting_prefix_aioseo'] . 'image_type' ];

		if ( $image_type === 'default' ) {
			$image_type = $this->social_images_provider->get_default_social_image_source( $social_setting );
		}

		switch ( $image_type ) {
			case 'attach':
				$image_url = $this->social_images_provider->get_first_attached_image( $indexable->object_id );
				break;
			case 'auto':
				if ( $this->social_images_provider->get_featured_image( $indexable->object_id ) ) {
					// If there's a featured image, lets not import it, as our indexable calculation has already set that as active social image. That way we achieve dynamicality.
					return null;
				}
				$image_url = $this->social_images_provider->get_auto_image( $indexable->object_id );
				break;
			case 'content':
				$image_url = $this->social_images_provider->get_first_image_in_content( $indexable->object_id );
				break;
			case 'custom_image':
				$image_url = $aioseo_social_image_settings[ $mapping['social_setting_prefix_aioseo'] . 'image_custom_url' ];
				break;
			case 'featured':
				return null; // Our auto-calculation when the indexable was built/updated has taken care of it, so it's not needed to transfer any data now.
			case 'author':
				return null;
			case 'custom':
				return null;
			case 'default':
				$image_url = $this->social_images_provider->get_default_custom_social_image( $social_setting );
				break;
			default:
				$image_url = $aioseo_social_image_settings[ $mapping['social_setting_prefix_aioseo'] . 'image_url' ];
				break;
		}

		if ( empty( $image_url ) ) {
			$image_url = $this->social_images_provider->get_default_custom_social_image( $social_setting );
		}

		if ( empty( $image_url ) ) {
			return null;
		}

		return $this->sanitization->sanitize_url( $image_url, null );
	}
}
