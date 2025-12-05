<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Content_Types;

use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Types_List;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;

/**
 * Class that collects post types and relevant information.
 */
class Content_Types_Collector {

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type_helper;

	/**
	 * The constructor.
	 *
	 * @param Post_Type_Helper $post_type_helper The post type helper.
	 */
	public function __construct( Post_Type_Helper $post_type_helper ) {
		$this->post_type_helper = $post_type_helper;
	}

	/**
	 * Returns the content types in a list.
	 *
	 * @return Content_Types_List The content types in a list.
	 */
	public function get_content_types(): Content_Types_List {
		$content_types_list = new Content_Types_List();
		$post_types         = $this->post_type_helper->get_indexable_post_type_objects();

		foreach ( $post_types as $post_type_object ) {
			if ( $post_type_object->show_ui === false ) {
				continue;
			}

			$content_type = new Content_Type( $post_type_object->name, $post_type_object->label );
			$content_types_list->add( $content_type );
		}

		return $content_types_list;
	}
}
