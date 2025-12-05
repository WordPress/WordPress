<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast\WP\SEO\Models\Indexable;

/**
 * A helper object for the robots meta tag.
 */
class Robots_Helper {

	/**
	 * Holds the Post_Type_Helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * Holds the Taxonomy_Helper.
	 *
	 * @var Taxonomy_Helper
	 */
	protected $taxonomy_helper;

	/**
	 * Constructs a Score_Helper.
	 *
	 * @param Post_Type_Helper $post_type_helper The Post_Type_Helper.
	 * @param Taxonomy_Helper  $taxonomy_helper  The Taxonomy_Helper.
	 */
	public function __construct( Post_Type_Helper $post_type_helper, Taxonomy_Helper $taxonomy_helper ) {
		$this->post_type_helper = $post_type_helper;
		$this->taxonomy_helper  = $taxonomy_helper;
	}

	/**
	 * Retrieves whether the Indexable is indexable.
	 *
	 * @param Indexable $indexable The Indexable.
	 *
	 * @return bool Whether the Indexable is indexable.
	 */
	public function is_indexable( Indexable $indexable ) {
		if ( $indexable->is_robots_noindex === null ) {
			// No individual value set, check the global setting.
			switch ( $indexable->object_type ) {
				case 'post':
					return $this->post_type_helper->is_indexable( $indexable->object_sub_type );
				case 'term':
					return $this->taxonomy_helper->is_indexable( $indexable->object_sub_type );
			}
		}

		return $indexable->is_robots_noindex === false;
	}

	/**
	 * Sets the robots index to noindex.
	 *
	 * @param array $robots The current robots value.
	 *
	 * @return array The altered robots string.
	 */
	public function set_robots_no_index( $robots ) {
		if ( ! \is_array( $robots ) ) {
			\_deprecated_argument( __METHOD__, '14.1', '$robots has to be a key-value paired array.' );
			return $robots;
		}

		$robots['index'] = 'noindex';

		return $robots;
	}
}
