<?php

namespace Elementor\Core\Files\CSS;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Post_Local_Cache extends Post {

	/**
	 * Meta cache
	 *
	 * @var array
	 */
	private $meta_cache = [];

	abstract protected function get_post_id_for_data();

	public function is_update_required() {
		return true;
	}

	protected function load_meta() {
		return $this->meta_cache;
	}

	protected function delete_meta() {
		$this->meta_cache = [];
	}

	protected function update_meta( $meta ) {
		$this->meta_cache = $meta;
	}

	protected function get_data() {
		$document = Plugin::$instance->documents->get( $this->get_post_id_for_data() );

		return $document ? $document->get_elements_data() : [];
	}
}
