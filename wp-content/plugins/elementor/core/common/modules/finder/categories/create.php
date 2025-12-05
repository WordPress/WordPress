<?php
namespace Elementor\Core\Common\Modules\Finder\Categories;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Create Category
 *
 * Provides items related to creation of new posts/pages/templates etc.
 */
class Create extends Base_Category {

	/**
	 * Get title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Create', 'elementor' );
	}

	public function get_id() {
		return 'create';
	}

	/**
	 * Get category items.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_category_items( array $options = [] ) {
		$result = [];

		$registered_document_types = Plugin::$instance->documents->get_document_types();

		// TODO: Remove - Support 'post' backwards compatibility - See `Documents_Manager::register_default_types()`.
		unset( $registered_document_types['post'] );

		$elementor_supported_post_types = array_flip( get_post_types_by_support( 'elementor' ) );

		foreach ( $registered_document_types as $document_name => $document_class ) {
			$document_properties = $document_class::get_properties();

			if ( empty( $document_properties['show_in_finder'] ) ) {
				continue;
			}

			if ( ! empty( $document_properties['cpt'] ) ) {
				foreach ( $document_properties['cpt'] as $cpt ) {
					unset( $elementor_supported_post_types[ $cpt ] );
				}
			}

			$result[ $document_name ] = $this->create_item_url_by_document_class( $document_class );
		}

		foreach ( $elementor_supported_post_types as $post_type => $val ) {
			$result[ $post_type ] = $this->create_item_url_by_post_type( $post_type );
		}

		return $result;
	}

	private function create_item_url_by_post_type( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		// If there is an old post type from inactive plugins.
		if ( ! $post_type_object ) {
			return false;
		}

		return $this->get_create_new_template(
			sprintf(
				/* translators: %s: Post type singular name. */
				__( 'Add New %s', 'elementor' ),
				$post_type_object->labels->singular_name
			),
			Plugin::$instance->documents->get_create_new_post_url( $post_type )
		);
	}

	private function create_item_url_by_document_class( $document_class ) {
		$result = $this->get_create_new_template(
			$document_class::get_add_new_title(),
			$document_class::get_create_url()
		);

		$lock_behavior = $document_class::get_lock_behavior_v2();
		$is_locked = ! empty( $lock_behavior ) && $lock_behavior->is_locked();

		if ( $is_locked ) {
			$result['lock'] = $lock_behavior->get_config();
		}

		return $result;
	}

	private function get_create_new_template( $add_new_title, $url ) {
		return [
			'title' => $add_new_title,
			'icon' => 'plus-circle-o',
			'url' => $url,
			'keywords' => [ $add_new_title, 'post', 'page', 'template', 'new', 'create' ],
		];
	}
}
