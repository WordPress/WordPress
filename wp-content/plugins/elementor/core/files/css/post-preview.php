<?php
namespace Elementor\Core\Files\CSS;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor post preview CSS file.
 *
 * Elementor CSS file handler class is responsible for generating the post
 * preview CSS file.
 *
 * @since 1.9.0
 */
class Post_Preview extends Post_Local_Cache {

	/**
	 * Preview ID.
	 *
	 * Holds the ID of the current post being previewed.
	 *
	 * @var int
	 */
	private $post_id_for_data;

	/**
	 * Post preview CSS file constructor.
	 *
	 * Initializing the CSS file of the post preview. Set the post ID and the
	 * parent ID and initiate the stylesheet.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id_for_data = $post_id;

		$parent_id = wp_get_post_parent_id( $post_id );

		parent::__construct( $parent_id );
	}

	protected function get_post_id_for_data() {
		return $this->post_id_for_data;
	}

	/**
	 * Get file handle ID.
	 *
	 * Retrieve the handle ID for the previewed post CSS file.
	 *
	 * @since 1.9.0
	 * @access protected
	 *
	 * @return string CSS file handle ID.
	 */
	protected function get_file_handle_id() {
		return 'elementor-preview-' . $this->get_post_id_for_data();
	}
}
