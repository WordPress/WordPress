<?php

namespace Yoast\WP\SEO\Builders;

use WP_Error;
use WP_Post;
use Yoast\WP\SEO\Exceptions\Indexable\Post_Not_Built_Exception;
use Yoast\WP\SEO\Exceptions\Indexable\Post_Not_Found_Exception;
use Yoast\WP\SEO\Helpers\Meta_Helper;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Post Builder for the indexables.
 *
 * Formats the post meta to indexable format.
 */
class Indexable_Post_Builder {

	use Indexable_Social_Image_Trait;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Holds the Post_Helper instance.
	 *
	 * @var Post_Helper
	 */
	protected $post_helper;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * Knows the latest version of the Indexable post builder type.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * The meta helper.
	 *
	 * @var Meta_Helper
	 */
	protected $meta;

	/**
	 * Indexable_Post_Builder constructor.
	 *
	 * @param Post_Helper                $post_helper      The post helper.
	 * @param Post_Type_Helper           $post_type_helper The post type helper.
	 * @param Indexable_Builder_Versions $versions         The indexable builder versions.
	 * @param Meta_Helper                $meta             The meta helper.
	 */
	public function __construct(
		Post_Helper $post_helper,
		Post_Type_Helper $post_type_helper,
		Indexable_Builder_Versions $versions,
		Meta_Helper $meta
	) {
		$this->post_helper      = $post_helper;
		$this->post_type_helper = $post_type_helper;
		$this->version          = $versions->get_latest_version_for_type( 'post' );
		$this->meta             = $meta;
	}

	/**
	 * Sets the indexable repository. Done to avoid circular dependencies.
	 *
	 * @required
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 *
	 * @return void
	 */
	public function set_indexable_repository( Indexable_Repository $indexable_repository ) {
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Formats the data.
	 *
	 * @param int       $post_id   The post ID to use.
	 * @param Indexable $indexable The indexable to format.
	 *
	 * @return bool|Indexable The extended indexable. False when unable to build.
	 *
	 * @throws Post_Not_Found_Exception When the post could not be found.
	 * @throws Post_Not_Built_Exception When the post should not be indexed.
	 */
	public function build( $post_id, $indexable ) {
		if ( ! $this->post_helper->is_post_indexable( $post_id ) ) {
			throw Post_Not_Built_Exception::because_not_indexable( $post_id );
		}

		$post = $this->post_helper->get_post( $post_id );

		if ( $post === null ) {
			throw new Post_Not_Found_Exception();
		}

		if ( $this->should_exclude_post( $post ) ) {
			throw Post_Not_Built_Exception::because_post_type_excluded( $post_id );
		}

		$indexable->object_id       = $post_id;
		$indexable->object_type     = 'post';
		$indexable->object_sub_type = $post->post_type;
		$indexable->permalink       = $this->get_permalink( $post->post_type, $post_id );

		$indexable->primary_focus_keyword_score = $this->get_keyword_score(
			$this->meta->get_value( 'focuskw', $post_id ),
			(int) $this->meta->get_value( 'linkdex', $post_id )
		);

		$indexable->readability_score = (int) $this->meta->get_value( 'content_score', $post_id );

		$indexable->inclusive_language_score = (int) $this->meta->get_value( 'inclusive_language_score', $post_id );

		$indexable->is_cornerstone    = ( $this->meta->get_value( 'is_cornerstone', $post_id ) === '1' );
		$indexable->is_robots_noindex = $this->get_robots_noindex(
			(int) $this->meta->get_value( 'meta-robots-noindex', $post_id )
		);

		// Set additional meta-robots values.
		$indexable->is_robots_nofollow = ( $this->meta->get_value( 'meta-robots-nofollow', $post_id ) === '1' );
		$noindex_advanced              = $this->meta->get_value( 'meta-robots-adv', $post_id );
		$meta_robots                   = \explode( ',', $noindex_advanced );

		foreach ( $this->get_robots_options() as $meta_robots_option ) {
			$indexable->{'is_robots_' . $meta_robots_option} = \in_array( $meta_robots_option, $meta_robots, true ) ? 1 : null;
		}

		$this->reset_social_images( $indexable );

		foreach ( $this->get_indexable_lookup() as $meta_key => $indexable_key ) {
			$indexable->{$indexable_key} = $this->empty_string_to_null( $this->meta->get_value( $meta_key, $post_id ) );
		}

		if ( empty( $indexable->breadcrumb_title ) ) {
			$indexable->breadcrumb_title = \wp_strip_all_tags( \get_the_title( $post_id ), true );
		}

		$this->handle_social_images( $indexable );

		$indexable->author_id   = $post->post_author;
		$indexable->post_parent = $post->post_parent;

		$indexable->number_of_pages  = $this->get_number_of_pages_for_post( $post );
		$indexable->post_status      = $post->post_status;
		$indexable->is_protected     = $post->post_password !== '';
		$indexable->is_public        = $this->is_public( $indexable );
		$indexable->has_public_posts = $this->has_public_posts( $indexable );
		$indexable->blog_id          = \get_current_blog_id();

		$indexable->schema_page_type    = $this->empty_string_to_null( $this->meta->get_value( 'schema_page_type', $post_id ) );
		$indexable->schema_article_type = $this->empty_string_to_null( $this->meta->get_value( 'schema_article_type', $post_id ) );

		$indexable->object_last_modified = $post->post_modified_gmt;
		$indexable->object_published_at  = $post->post_date_gmt;

		$indexable->version = $this->version;

		return $indexable;
	}

	/**
	 * Retrieves the permalink for a post with the given post type and ID.
	 *
	 * @param string $post_type The post type.
	 * @param int    $post_id   The post ID.
	 *
	 * @return WP_Error|string|false The permalink.
	 */
	protected function get_permalink( $post_type, $post_id ) {
		if ( $post_type !== 'attachment' ) {
			return \get_permalink( $post_id );
		}

		return \wp_get_attachment_url( $post_id );
	}

	/**
	 * Determines the value of is_public.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return bool|null Whether or not the post type is public. Null if no override is set.
	 */
	protected function is_public( $indexable ) {
		if ( $indexable->is_protected === true ) {
			return false;
		}

		if ( $indexable->is_robots_noindex === true ) {
			return false;
		}

		// Attachments behave differently than the other post types, since they inherit from their parent.
		if ( $indexable->object_sub_type === 'attachment' ) {
			return $this->is_public_attachment( $indexable );
		}

		if ( ! \in_array( $indexable->post_status, $this->post_helper->get_public_post_statuses(), true ) ) {
			return false;
		}

		if ( $indexable->is_robots_noindex === false ) {
			return true;
		}

		return null;
	}

	/**
	 * Determines the value of is_public for attachments.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return bool|null False when it has no parent. Null when it has a parent.
	 */
	protected function is_public_attachment( $indexable ) {
		// If the attachment has no parent, it should not be public.
		if ( empty( $indexable->post_parent ) ) {
			return false;
		}

		// If the attachment has a parent, the is_public should be NULL.
		return null;
	}

	/**
	 * Determines the value of has_public_posts.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return bool|null Whether the attachment has a public parent, can be true, false and null. Null when it is not an attachment.
	 */
	protected function has_public_posts( $indexable ) {
		// Only attachments (and authors) have this value.
		if ( $indexable->object_sub_type !== 'attachment' ) {
			return null;
		}

		// The attachment should have a post parent.
		if ( empty( $indexable->post_parent ) ) {
			return false;
		}

		// The attachment should inherit the post status.
		if ( $indexable->post_status !== 'inherit' ) {
			return false;
		}

		// The post parent should be public.
		$post_parent_indexable = $this->indexable_repository->find_by_id_and_type( $indexable->post_parent, 'post' );
		if ( $post_parent_indexable !== false ) {
			return $post_parent_indexable->is_public;
		}

		return false;
	}

	/**
	 * Converts the meta robots noindex value to the indexable value.
	 *
	 * @param int $value Meta value to convert.
	 *
	 * @return bool|null True for noindex, false for index, null for default of parent/type.
	 */
	protected function get_robots_noindex( $value ) {
		$value = (int) $value;

		switch ( $value ) {
			case 1:
				return true;
			case 2:
				return false;
		}

		return null;
	}

	/**
	 * Retrieves the robot options to search for.
	 *
	 * @return array List of robots values.
	 */
	protected function get_robots_options() {
		return [ 'noimageindex', 'noarchive', 'nosnippet' ];
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
			'focuskw'                        => 'primary_focus_keyword',
			'canonical'                      => 'canonical',
			'title'                          => 'title',
			'metadesc'                       => 'description',
			'bctitle'                        => 'breadcrumb_title',
			'opengraph-title'                => 'open_graph_title',
			'opengraph-image'                => 'open_graph_image',
			'opengraph-image-id'             => 'open_graph_image_id',
			'opengraph-description'          => 'open_graph_description',
			'twitter-title'                  => 'twitter_title',
			'twitter-image'                  => 'twitter_image',
			'twitter-image-id'               => 'twitter_image_id',
			'twitter-description'            => 'twitter_description',
			'estimated-reading-time-minutes' => 'estimated_reading_time_minutes',
		];
	}

	/**
	 * Finds an alternative image for the social image.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return array|bool False when not found, array with data when found.
	 */
	protected function find_alternative_image( Indexable $indexable ) {
		if (
			$indexable->object_sub_type === 'attachment'
			&& $this->image->is_valid_attachment( $indexable->object_id )
		) {
			return [
				'image_id' => $indexable->object_id,
				'source'   => 'attachment-image',
			];
		}

		$featured_image_id = $this->image->get_featured_image_id( $indexable->object_id );
		if ( $featured_image_id ) {
			return [
				'image_id' => $featured_image_id,
				'source'   => 'featured-image',
			];
		}

		$gallery_image = $this->image->get_gallery_image( $indexable->object_id );
		if ( $gallery_image ) {
			return [
				'image'  => $gallery_image,
				'source' => 'gallery-image',
			];
		}

		$content_image = $this->image->get_post_content_image( $indexable->object_id );
		if ( $content_image ) {
			return [
				'image'  => $content_image,
				'source' => 'first-content-image',
			];
		}

		return false;
	}

	/**
	 * Gets the number of pages for a post.
	 *
	 * @param object $post The post object.
	 *
	 * @return int|null The number of pages or null if the post isn't paginated.
	 */
	protected function get_number_of_pages_for_post( $post ) {
		$number_of_pages = ( \substr_count( $post->post_content, '<!--nextpage-->' ) + 1 );

		if ( $number_of_pages <= 1 ) {
			return null;
		}

		return $number_of_pages;
	}

	/**
	 * Checks whether an indexable should be built for this post.
	 *
	 * @param WP_Post $post The post for which an indexable should be built.
	 *
	 * @return bool `true` if the post should be excluded from building, `false` if not.
	 */
	protected function should_exclude_post( $post ) {
		return $this->post_type_helper->is_excluded( $post->post_type );
	}

	/**
	 * Transforms an empty string into null. Leaves non-empty strings intact.
	 *
	 * @param string $text The string.
	 *
	 * @return string|null The input string or null.
	 */
	protected function empty_string_to_null( $text ) {
		if ( ! \is_string( $text ) || $text === '' ) {
			return null;
		}

		return $text;
	}
}
