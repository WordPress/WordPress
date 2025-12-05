<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast\WP\SEO\Models\Indexable;

/**
 * A helper object to map indexable data to postmeta.
 */
class Indexable_To_Postmeta_Helper {

	/**
	 * The Meta helper.
	 *
	 * @var Meta_Helper
	 */
	public $meta;

	/**
	 * The map of yoast to post meta.
	 *
	 * @var array
	 */
	protected $yoast_to_postmeta = [
		'title'                  => [
			'post_meta_key' => 'title',
			'map_method'    => 'simple_map',
		],
		'description'            => [
			'post_meta_key' => 'metadesc',
			'map_method'    => 'simple_map',
		],
		'open_graph_title'       => [
			'post_meta_key' => 'opengraph-title',
			'map_method'    => 'simple_map',
		],
		'open_graph_description' => [
			'post_meta_key' => 'opengraph-description',
			'map_method'    => 'simple_map',
		],
		'twitter_title'          => [
			'post_meta_key' => 'twitter-title',
			'map_method'    => 'simple_map',
		],
		'twitter_description'    => [
			'post_meta_key' => 'twitter-description',
			'map_method'    => 'simple_map',
		],
		'canonical'              => [
			'post_meta_key' => 'canonical',
			'map_method'    => 'simple_map',
		],
		'primary_focus_keyword'  => [
			'post_meta_key' => 'focuskw',
			'map_method'    => 'simple_map',
		],
		'open_graph_image'       => [
			'post_meta_key' => 'opengraph-image',
			'map_method'    => 'social_image_map',
		],
		'open_graph_image_id'    => [
			'post_meta_key' => 'opengraph-image-id',
			'map_method'    => 'social_image_map',
		],
		'twitter_image'          => [
			'post_meta_key' => 'twitter-image',
			'map_method'    => 'social_image_map',
		],
		'twitter_image_id'       => [
			'post_meta_key' => 'twitter-image-id',
			'map_method'    => 'social_image_map',
		],
		'is_robots_noindex'      => [
			'post_meta_key' => 'meta-robots-noindex',
			'map_method'    => 'noindex_map',
		],
		'is_robots_nofollow'     => [
			'post_meta_key' => 'meta-robots-nofollow',
			'map_method'    => 'nofollow_map',
		],
		'meta_robots_adv'        => [
			'post_meta_key' => 'meta-robots-adv',
			'map_method'    => 'robots_adv_map',
		],
	];

	/**
	 * Indexable_To_Postmeta_Helper constructor.
	 *
	 * @param Meta_Helper $meta The Meta helper.
	 */
	public function __construct( Meta_Helper $meta ) {
		$this->meta = $meta;
	}

	/**
	 * Creates postmeta from a Yoast indexable.
	 *
	 * @param Indexable $indexable The Yoast indexable.
	 *
	 * @return void
	 */
	public function map_to_postmeta( $indexable ) {
		foreach ( $this->yoast_to_postmeta as $indexable_column => $map_info ) {
			\call_user_func( [ $this, $map_info['map_method'] ], $indexable, $map_info['post_meta_key'], $indexable_column );
		}
	}

	/**
	 * Uses a simple set_value for non-empty data.
	 *
	 * @param Indexable $indexable        The Yoast indexable.
	 * @param string    $post_meta_key    The post_meta key that will be populated.
	 * @param string    $indexable_column The indexable data that will be mapped to post_meta.
	 *
	 * @return void
	 */
	public function simple_map( $indexable, $post_meta_key, $indexable_column ) {
		if ( empty( $indexable->{$indexable_column} ) ) {
			return;
		}

		$this->meta->set_value( $post_meta_key, $indexable->{$indexable_column}, $indexable->object_id );
	}

	/**
	 * Map social image data only if social image is explicitly set.
	 *
	 * @param Indexable $indexable        The Yoast indexable.
	 * @param string    $post_meta_key    The post_meta key that will be populated.
	 * @param string    $indexable_column The indexable data that will be mapped to post_meta.
	 *
	 * @return void
	 */
	public function social_image_map( $indexable, $post_meta_key, $indexable_column ) {
		if ( empty( $indexable->{$indexable_column} ) ) {
			return;
		}

		switch ( $indexable_column ) {
			case 'open_graph_image':
			case 'open_graph_image_id':
				$source = $indexable->open_graph_image_source;
				break;
			case 'twitter_image':
			case 'twitter_image_id':
				$source = $indexable->twitter_image_source;
				break;
		}

		// Map the social image data only when the social image is explicitly set.
		if ( $source === 'set-by-user' || $source === 'imported' ) {
			$value = (string) $indexable->{$indexable_column};

			$this->meta->set_value( $post_meta_key, $value, $indexable->object_id );
		}
	}

	/**
	 * Deletes the noindex post_meta key if no noindex in the indexable. Populates the post_meta key appropriately if there is noindex in the indexable.
	 *
	 * @param Indexable $indexable     The Yoast indexable.
	 * @param string    $post_meta_key The post_meta key that will be populated.
	 *
	 * @return void
	 */
	public function noindex_map( $indexable, $post_meta_key ) {
		if ( $indexable->is_robots_noindex === null ) {
			$this->meta->delete( $post_meta_key, $indexable->object_id );
			return;
		}

		if ( $indexable->is_robots_noindex === false ) {
			$this->meta->set_value( $post_meta_key, 2, $indexable->object_id );
		}

		if ( $indexable->is_robots_noindex === true ) {
			$this->meta->set_value( $post_meta_key, 1, $indexable->object_id );
		}
	}

	/**
	 * Deletes the nofollow post_meta key if no nofollow in the indexable or if nofollow is false. Populates the post_meta key appropriately if there is a true nofollow in the indexable.
	 *
	 * @param Indexable $indexable     The Yoast indexable.
	 * @param string    $post_meta_key The post_meta key that will be populated.
	 *
	 * @return void
	 */
	public function nofollow_map( $indexable, $post_meta_key ) {
		if ( $indexable->is_robots_nofollow === null || $indexable->is_robots_nofollow === false ) {
			$this->meta->delete( $post_meta_key, $indexable->object_id );
		}

		if ( $indexable->is_robots_nofollow === true ) {
			$this->meta->set_value( $post_meta_key, 1, $indexable->object_id );
		}
	}

	/**
	 * Deletes the nofollow post_meta key if no nofollow in the indexable or if nofollow is false. Populates the post_meta key appropriately if there is a true nofollow in the indexable.
	 *
	 * @param Indexable $indexable     The Yoast indexable.
	 * @param string    $post_meta_key The post_meta key that will be populated.
	 *
	 * @return void
	 */
	public function robots_adv_map( $indexable, $post_meta_key ) {
		$adv_settings_to_be_imported = [];
		$no_adv_settings             = true;

		if ( $indexable->is_robots_noimageindex === true ) {
			$adv_settings_to_be_imported[] = 'noimageindex';
			$no_adv_settings               = false;
		}
		if ( $indexable->is_robots_noarchive === true ) {
			$adv_settings_to_be_imported[] = 'noarchive';
			$no_adv_settings               = false;
		}
		if ( $indexable->is_robots_nosnippet === true ) {
			$adv_settings_to_be_imported[] = 'nosnippet';
			$no_adv_settings               = false;
		}

		if ( $no_adv_settings === true ) {
			$this->meta->delete( $post_meta_key, $indexable->object_id );
			return;
		}

		$this->meta->set_value( $post_meta_key, \implode( ',', $adv_settings_to_be_imported ), $indexable->object_id );
	}
}
