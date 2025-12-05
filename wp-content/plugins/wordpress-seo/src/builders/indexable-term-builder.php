<?php

namespace Yoast\WP\SEO\Builders;

use Yoast\WP\SEO\Exceptions\Indexable\Invalid_Term_Exception;
use Yoast\WP\SEO\Exceptions\Indexable\Term_Not_Built_Exception;
use Yoast\WP\SEO\Exceptions\Indexable\Term_Not_Found_Exception;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Term Builder for the indexables.
 *
 * Formats the term meta to indexable format.
 */
class Indexable_Term_Builder {

	use Indexable_Social_Image_Trait;

	/**
	 * Holds the taxonomy helper instance.
	 *
	 * @var Taxonomy_Helper
	 */
	protected $taxonomy_helper;

	/**
	 * The latest version of the Indexable_Term_Builder.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Holds the taxonomy helper instance.
	 *
	 * @var Post_Helper
	 */
	protected $post_helper;

	/**
	 * Indexable_Term_Builder constructor.
	 *
	 * @param Taxonomy_Helper            $taxonomy_helper The taxonomy helper.
	 * @param Indexable_Builder_Versions $versions        The latest version of each Indexable Builder.
	 * @param Post_Helper                $post_helper     The post helper.
	 */
	public function __construct(
		Taxonomy_Helper $taxonomy_helper,
		Indexable_Builder_Versions $versions,
		Post_Helper $post_helper
	) {
		$this->taxonomy_helper = $taxonomy_helper;
		$this->version         = $versions->get_latest_version_for_type( 'term' );
		$this->post_helper     = $post_helper;
	}

	/**
	 * Formats the data.
	 *
	 * @param int       $term_id   ID of the term to save data for.
	 * @param Indexable $indexable The indexable to format.
	 *
	 * @return bool|Indexable The extended indexable. False when unable to build.
	 *
	 * @throws Invalid_Term_Exception   When the term is invalid.
	 * @throws Term_Not_Built_Exception When the term is not viewable.
	 * @throws Term_Not_Found_Exception When the term is not found.
	 */
	public function build( $term_id, $indexable ) {
		$term = \get_term( $term_id );

		if ( $term === null ) {
			throw new Term_Not_Found_Exception();
		}

		if ( \is_wp_error( $term ) ) {
			throw new Invalid_Term_Exception( $term->get_error_message() );
		}

		$indexable_taxonomies = $this->taxonomy_helper->get_indexable_taxonomies();
		if ( ! \in_array( $term->taxonomy, $indexable_taxonomies, true ) ) {
			throw Term_Not_Built_Exception::because_not_indexable( $term_id );
		}

		$term_link = \get_term_link( $term, $term->taxonomy );

		if ( \is_wp_error( $term_link ) ) {
			throw new Invalid_Term_Exception( $term_link->get_error_message() );
		}

		$term_meta = $this->taxonomy_helper->get_term_meta( $term );

		$indexable->object_id       = $term_id;
		$indexable->object_type     = 'term';
		$indexable->object_sub_type = $term->taxonomy;
		$indexable->permalink       = $term_link;
		$indexable->blog_id         = \get_current_blog_id();

		$indexable->primary_focus_keyword_score = $this->get_keyword_score(
			$this->get_meta_value( 'wpseo_focuskw', $term_meta ),
			$this->get_meta_value( 'wpseo_linkdex', $term_meta )
		);

		$indexable->is_robots_noindex = $this->get_noindex_value( $this->get_meta_value( 'wpseo_noindex', $term_meta ) );
		$indexable->is_public         = ( $indexable->is_robots_noindex === null ) ? null : ! $indexable->is_robots_noindex;

		$this->reset_social_images( $indexable );

		foreach ( $this->get_indexable_lookup() as $meta_key => $indexable_key ) {
			$indexable->{$indexable_key} = $this->get_meta_value( $meta_key, $term_meta );
		}

		if ( empty( $indexable->breadcrumb_title ) ) {
			$indexable->breadcrumb_title = $term->name;
		}

		$this->handle_social_images( $indexable );

		$indexable->is_cornerstone = $this->get_meta_value( 'wpseo_is_cornerstone', $term_meta );

		// Not implemented yet.
		$indexable->is_robots_nofollow     = null;
		$indexable->is_robots_noarchive    = null;
		$indexable->is_robots_noimageindex = null;
		$indexable->is_robots_nosnippet    = null;

		$timestamps                      = $this->get_object_timestamps( $term_id, $term->taxonomy );
		$indexable->object_published_at  = $timestamps->published_at;
		$indexable->object_last_modified = $timestamps->last_modified;

		$indexable->version = $this->version;

		return $indexable;
	}

	/**
	 * Converts the meta noindex value to the indexable value.
	 *
	 * @param string $meta_value Term meta to base the value on.
	 *
	 * @return bool|null
	 */
	protected function get_noindex_value( $meta_value ) {
		if ( $meta_value === 'noindex' ) {
			return true;
		}

		if ( $meta_value === 'index' ) {
			return false;
		}

		return null;
	}

	/**
	 * Determines the focus keyword score.
	 *
	 * @param string $keyword The focus keyword that is set.
	 * @param int    $score   The score saved on the meta data.
	 *
	 * @return int|null Score to use.
	 */
	protected function get_keyword_score( $keyword, $score ) {
		if ( empty( $keyword ) ) {
			return null;
		}

		return $score;
	}

	/**
	 * Retrieves the lookup table.
	 *
	 * @return array Lookup table for the indexable fields.
	 */
	protected function get_indexable_lookup() {
		return [
			'wpseo_canonical'                => 'canonical',
			'wpseo_focuskw'                  => 'primary_focus_keyword',
			'wpseo_title'                    => 'title',
			'wpseo_desc'                     => 'description',
			'wpseo_content_score'            => 'readability_score',
			'wpseo_inclusive_language_score' => 'inclusive_language_score',
			'wpseo_bctitle'                  => 'breadcrumb_title',
			'wpseo_opengraph-title'          => 'open_graph_title',
			'wpseo_opengraph-description'    => 'open_graph_description',
			'wpseo_opengraph-image'          => 'open_graph_image',
			'wpseo_opengraph-image-id'       => 'open_graph_image_id',
			'wpseo_twitter-title'            => 'twitter_title',
			'wpseo_twitter-description'      => 'twitter_description',
			'wpseo_twitter-image'            => 'twitter_image',
			'wpseo_twitter-image-id'         => 'twitter_image_id',
		];
	}

	/**
	 * Retrieves a meta value from the given meta data.
	 *
	 * @param string $meta_key  The key to extract.
	 * @param array  $term_meta The meta data.
	 *
	 * @return string|null The meta value.
	 */
	protected function get_meta_value( $meta_key, $term_meta ) {
		if ( ! $term_meta || ! \array_key_exists( $meta_key, $term_meta ) ) {
			return null;
		}

		$value = $term_meta[ $meta_key ];
		if ( \is_string( $value ) && $value === '' ) {
			return null;
		}

		return $value;
	}

	/**
	 * Finds an alternative image for the social image.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return array|bool False when not found, array with data when found.
	 */
	protected function find_alternative_image( Indexable $indexable ) {
		$content_image = $this->image->get_term_content_image( $indexable->object_id );
		if ( $content_image ) {
			return [
				'image'  => $content_image,
				'source' => 'first-content-image',
			];
		}

		return false;
	}

	/**
	 * Returns the timestamps for a given term.
	 *
	 * @param int    $term_id  The term ID.
	 * @param string $taxonomy The taxonomy.
	 *
	 * @return object An object with last_modified and published_at timestamps.
	 */
	protected function get_object_timestamps( $term_id, $taxonomy ) {
		global $wpdb;
		$post_statuses = $this->post_helper->get_public_post_statuses();

		$replacements   = [];
		$replacements[] = 'post_modified_gmt';
		$replacements[] = 'post_date_gmt';
		$replacements[] = $wpdb->posts;
		$replacements[] = $wpdb->term_relationships;
		$replacements[] = 'object_id';
		$replacements[] = 'ID';
		$replacements[] = $wpdb->term_taxonomy;
		$replacements[] = 'term_taxonomy_id';
		$replacements[] = 'term_taxonomy_id';
		$replacements[] = 'taxonomy';
		$replacements[] = $taxonomy;
		$replacements[] = 'term_id';
		$replacements[] = $term_id;
		$replacements[] = 'post_status';
		$replacements   = \array_merge( $replacements, $post_statuses );
		$replacements[] = 'post_password';

		//phpcs:disable WordPress.DB.PreparedSQLPlaceholders -- %i placeholder is still not recognized.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		return $wpdb->get_row(
			$wpdb->prepare(
				'
			SELECT MAX(p.%i) AS last_modified, MIN(p.%i) AS published_at
			FROM %i AS p
			INNER JOIN %i AS term_rel
				ON		term_rel.%i = p.%i
			INNER JOIN %i AS term_tax
				ON		term_tax.%i = term_rel.%i
				AND		term_tax.%i = %s
				AND		term_tax.%i = %d
			WHERE	p.%i IN (' . \implode( ', ', \array_fill( 0, \count( $post_statuses ), '%s' ) ) . ")
				AND		p.%i = ''
			",
				$replacements
			)
		);
		//phpcs:enable
	}
}
