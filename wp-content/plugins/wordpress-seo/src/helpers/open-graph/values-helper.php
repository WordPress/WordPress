<?php

namespace Yoast\WP\SEO\Helpers\Open_Graph;

/**
 * A helper object for the filtering of values.
 */
class Values_Helper {

	/**
	 * Filters the Open Graph title.
	 *
	 * @param string $title          The default title.
	 * @param string $object_type    The object type.
	 * @param string $object_subtype The object subtype.
	 *
	 * @return string The open graph title.
	 */
	public function get_open_graph_title( $title, $object_type, $object_subtype ) {
		/**
		 * Allow changing the Open Graph title.
		 *
		 * @param string $title          The default title.
		 * @param string $object_subtype The object subtype.
		 */
		return \apply_filters( 'Yoast\WP\SEO\open_graph_title_' . $object_type, $title, $object_subtype );
	}

	/**
	 * Filters the Open Graph description.
	 *
	 * @param string $description    The default description.
	 * @param string $object_type    The object type.
	 * @param string $object_subtype The object subtype.
	 *
	 * @return string The open graph description.
	 */
	public function get_open_graph_description( $description, $object_type, $object_subtype ) {
		/**
		 * Allow changing the Open Graph description.
		 *
		 * @param string $description    The default description.
		 * @param string $object_subtype The object subtype.
		 */
		return \apply_filters( 'Yoast\WP\SEO\open_graph_description_' . $object_type, $description, $object_subtype );
	}

	/**
	 * Filters the Open Graph image ID.
	 *
	 * @param int    $image_id       The default image ID.
	 * @param string $object_type    The object type.
	 * @param string $object_subtype The object subtype.
	 *
	 * @return string The open graph image ID.
	 */
	public function get_open_graph_image_id( $image_id, $object_type, $object_subtype ) {
		/**
		 * Allow changing the Open Graph image ID.
		 *
		 * @param int    $image_id       The default image ID.
		 * @param string $object_subtype The object subtype.
		 */
		return \apply_filters( 'Yoast\WP\SEO\open_graph_image_id_' . $object_type, $image_id, $object_subtype );
	}

	/**
	 * Filters the Open Graph image URL.
	 *
	 * @param string $image          The default image URL.
	 * @param string $object_type    The object type.
	 * @param string $object_subtype The object subtype.
	 *
	 * @return string The open graph image URL.
	 */
	public function get_open_graph_image( $image, $object_type, $object_subtype ) {
		/**
		 * Allow changing the Open Graph image URL.
		 *
		 * @param string $image          The default image URL.
		 * @param string $object_subtype The object subtype.
		 */
		return \apply_filters( 'Yoast\WP\SEO\open_graph_image_' . $object_type, $image, $object_subtype );
	}
}
