<?php

namespace Yoast\WP\SEO\Models;

use Yoast\WP\Lib\Model;

/**
 * Indexable table definition.
 *
 * @property int    $id
 * @property int    $object_id
 * @property string $object_type
 * @property string $object_sub_type
 *
 * @property int    $author_id
 * @property int    $post_parent
 *
 * @property string $created_at
 * @property string $updated_at
 *
 * @property string $permalink
 * @property string $permalink_hash
 * @property string $canonical
 *
 * @property bool   $is_robots_noindex
 * @property bool   $is_robots_nofollow
 * @property bool   $is_robots_noarchive
 * @property bool   $is_robots_noimageindex
 * @property bool   $is_robots_nosnippet
 *
 * @property string $title
 * @property string $description
 * @property string $breadcrumb_title
 *
 * @property bool   $is_cornerstone
 *
 * @property string $primary_focus_keyword
 * @property int    $primary_focus_keyword_score
 *
 * @property int    $readability_score
 *
 * @property int    $inclusive_language_score
 *
 * @property int    $link_count
 * @property int    $incoming_link_count
 * @property int    $number_of_pages
 *
 * @property string $open_graph_title
 * @property string $open_graph_description
 * @property string $open_graph_image
 * @property string $open_graph_image_id
 * @property string $open_graph_image_source
 * @property string $open_graph_image_meta
 *
 * @property string $twitter_title
 * @property string $twitter_description
 * @property string $twitter_image
 * @property string $twitter_image_id
 * @property string $twitter_image_source
 * @property string $twitter_card
 *
 * @property int    $prominent_words_version
 *
 * @property bool   $is_public
 * @property bool   $is_protected
 * @property string $post_status
 * @property bool   $has_public_posts
 *
 * @property int    $blog_id
 *
 * @property string $language
 * @property string $region
 *
 * @property string $schema_page_type
 * @property string $schema_article_type
 *
 * @property bool   $has_ancestors
 *
 * @property int    $estimated_reading_time_minutes
 *
 * @property string $object_last_modified
 * @property string $object_published_at
 *
 * @property int    $version
 */
class Indexable extends Model {

	/**
	 * Holds the ancestors.
	 *
	 * @var Indexable[]
	 */
	public $ancestors = [];

	/**
	 * Whether nor this model uses timestamps.
	 *
	 * @var bool
	 */
	protected $uses_timestamps = true;

	/**
	 * Which columns contain boolean values.
	 *
	 * @var array
	 */
	protected $boolean_columns = [
		'is_robots_noindex',
		'is_robots_nofollow',
		'is_robots_noarchive',
		'is_robots_noimageindex',
		'is_robots_nosnippet',
		'is_cornerstone',
		'is_public',
		'is_protected',
		'has_public_posts',
		'has_ancestors',
	];

	/**
	 * Which columns contain int values.
	 *
	 * @var array
	 */
	protected $int_columns = [
		'id',
		'object_id',
		'author_id',
		'post_parent',
		'primary_focus_keyword_score',
		'readability_score',
		'inclusive_language_score',
		'link_count',
		'incoming_link_count',
		'number_of_pages',
		'prominent_words_version',
		'blog_id',
		'estimated_reading_time_minutes',
		'version',
	];

	/**
	 * The loaded indexable extensions.
	 *
	 * @var Indexable_Extension[]
	 */
	protected $loaded_extensions = [];

	/**
	 * Returns an Indexable_Extension by its name.
	 *
	 * @param string $class_name The class name of the extension to load.
	 *
	 * @return Indexable_Extension|bool The extension.
	 */
	public function get_extension( $class_name ) {
		if ( ! $this->loaded_extensions[ $class_name ] ) {
			$this->loaded_extensions[ $class_name ] = $this->has_one( $class_name, 'indexable_id', 'id' )->find_one();
		}

		return $this->loaded_extensions[ $class_name ];
	}

	/**
	 * Enhances the save method.
	 *
	 * @return bool True on success.
	 */
	public function save() {
		if ( $this->permalink ) {
			$this->sanitize_permalink();
			$this->permalink_hash = \strlen( $this->permalink ) . ':' . \md5( $this->permalink );
		}
		if ( \is_string( $this->primary_focus_keyword ) && \mb_strlen( $this->primary_focus_keyword ) > 191 ) {
			$this->primary_focus_keyword = \mb_substr( $this->primary_focus_keyword, 0, 191, 'UTF-8' );
		}

		return parent::save();
	}

	/**
	 * Sanitizes the permalink.
	 *
	 * @return void
	 */
	protected function sanitize_permalink() {
		if ( $this->permalink === 'unindexed' ) {
			return;
		}

		$permalink_structure = \get_option( 'permalink_structure' );
		$permalink_parts     = \wp_parse_url( $this->permalink );

		if ( ! isset( $permalink_parts['path'] ) ) {
			$permalink_parts['path'] = '/';
		}
		if ( \substr( $permalink_structure, -1, 1 ) === '/' && \strpos( \substr( $permalink_parts['path'], -5 ), '.' ) === false ) {
			$permalink_parts['path'] = \trailingslashit( $permalink_parts['path'] );
		}

		$permalink = '';
		if ( isset( $permalink_parts['scheme'] ) ) {
			$permalink .= $permalink_parts['scheme'] . '://';
		}
		if ( isset( $permalink_parts['host'] ) ) {
			$permalink .= $permalink_parts['host'];
		}
		if ( isset( $permalink_parts['port'] ) ) {
			$permalink .= ':' . $permalink_parts['port'];
		}
		if ( isset( $permalink_parts['path'] ) ) {
			$permalink .= $permalink_parts['path'];
		}
		if ( isset( $permalink_parts['query'] ) ) {
			$permalink .= '?' . $permalink_parts['query'];
		}
		// We never set the fragment as the fragment is intended to be client-only.
		$this->permalink = $permalink;
	}
}
