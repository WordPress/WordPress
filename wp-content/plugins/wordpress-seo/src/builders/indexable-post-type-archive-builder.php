<?php

namespace Yoast\WP\SEO\Builders;

use Yoast\WP\SEO\Exceptions\Indexable\Post_Type_Not_Built_Exception;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Post type archive builder for the indexables.
 *
 * Formats the post type archive meta to indexable format.
 */
class Indexable_Post_Type_Archive_Builder {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * The latest version of the Indexable_Post_Type_Archive_Builder.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Holds the post helper instance.
	 *
	 * @var Post_Helper
	 */
	protected $post_helper;

	/**
	 * Holds the post type helper instance.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * Indexable_Post_Type_Archive_Builder constructor.
	 *
	 * @param Options_Helper             $options          The options helper.
	 * @param Indexable_Builder_Versions $versions         The latest version of each Indexable builder.
	 * @param Post_Helper                $post_helper      The post helper.
	 * @param Post_Type_Helper           $post_type_helper The post type helper.
	 */
	public function __construct(
		Options_Helper $options,
		Indexable_Builder_Versions $versions,
		Post_Helper $post_helper,
		Post_Type_Helper $post_type_helper
	) {
		$this->options          = $options;
		$this->version          = $versions->get_latest_version_for_type( 'post-type-archive' );
		$this->post_helper      = $post_helper;
		$this->post_type_helper = $post_type_helper;
	}

	/**
	 * Formats the data.
	 *
	 * @param string    $post_type The post type to build the indexable for.
	 * @param Indexable $indexable The indexable to format.
	 *
	 * @return Indexable The extended indexable.
	 * @throws Post_Type_Not_Built_Exception Throws exception if the post type is excluded.
	 */
	public function build( $post_type, Indexable $indexable ) {
		if ( ! $this->post_type_helper->is_post_type_archive_indexable( $post_type ) ) {
			throw Post_Type_Not_Built_Exception::because_not_indexable( $post_type );
		}

		$indexable->object_type       = 'post-type-archive';
		$indexable->object_sub_type   = $post_type;
		$indexable->title             = $this->options->get( 'title-ptarchive-' . $post_type );
		$indexable->description       = $this->options->get( 'metadesc-ptarchive-' . $post_type );
		$indexable->breadcrumb_title  = $this->get_breadcrumb_title( $post_type );
		$indexable->permalink         = \get_post_type_archive_link( $post_type );
		$indexable->is_robots_noindex = $this->options->get( 'noindex-ptarchive-' . $post_type );
		$indexable->is_public         = ( (int) $indexable->is_robots_noindex !== 1 );
		$indexable->blog_id           = \get_current_blog_id();
		$indexable->version           = $this->version;

		$timestamps                      = $this->get_object_timestamps( $post_type );
		$indexable->object_published_at  = $timestamps->published_at;
		$indexable->object_last_modified = $timestamps->last_modified;

		return $indexable;
	}

	/**
	 * Returns the fallback breadcrumb title for a given post.
	 *
	 * @param string $post_type The post type to get the fallback breadcrumb title for.
	 *
	 * @return string
	 */
	private function get_breadcrumb_title( $post_type ) {
		$options_breadcrumb_title = $this->options->get( 'bctitle-ptarchive-' . $post_type );

		if ( $options_breadcrumb_title !== '' ) {
			return $options_breadcrumb_title;
		}

		$post_type_obj = \get_post_type_object( $post_type );

		if ( ! \is_object( $post_type_obj ) ) {
			return '';
		}

		if ( isset( $post_type_obj->label ) && $post_type_obj->label !== '' ) {
			return $post_type_obj->label;
		}

		if ( isset( $post_type_obj->labels->menu_name ) && $post_type_obj->labels->menu_name !== '' ) {
			return $post_type_obj->labels->menu_name;
		}

		return $post_type_obj->name;
	}

	/**
	 * Returns the timestamps for a given post type.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return object An object with last_modified and published_at timestamps.
	 */
	protected function get_object_timestamps( $post_type ) {
		global $wpdb;
		$post_statuses = $this->post_helper->get_public_post_statuses();

		$replacements   = [];
		$replacements[] = 'post_modified_gmt';
		$replacements[] = 'post_date_gmt';
		$replacements[] = $wpdb->posts;
		$replacements[] = 'post_status';
		$replacements   = \array_merge( $replacements, $post_statuses );
		$replacements[] = 'post_password';
		$replacements[] = 'post_type';
		$replacements[] = $post_type;

		//phpcs:disable WordPress.DB.PreparedSQLPlaceholders -- %i placeholder is still not recognized.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need to use a direct query here.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		return $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT MAX(p.%i) AS last_modified, MIN(p.%i) AS published_at
				FROM %i AS p
				WHERE p.%i IN (' . \implode( ', ', \array_fill( 0, \count( $post_statuses ), '%s' ) ) . ")
					AND p.%i = ''
					AND p.%i = %s
				",
				$replacements
			)
		);
		//phpcs:enable
	}
}
