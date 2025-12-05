<?php

namespace Elementor\App\Modules\ImportExport\Runners\Revert;

use Elementor\App\Modules\ImportExport\Utils as ImportExportUtils;
use Elementor\Plugin;

class Elementor_Content extends Revert_Runner_Base {
	private $show_page_on_front;

	private $page_on_front_id;

	public function __construct() {
		$this->init_page_on_front_data();
	}

	public static function get_name(): string {
		return 'elementor-content';
	}

	public function should_revert( array $data ): bool {
		return (
			isset( $data['runners'] ) &&
			array_key_exists( static::get_name(), $data['runners'] )
		);
	}

	public function revert( array $data ) {
		$elementor_post_types = ImportExportUtils::get_elementor_post_types();

		$query_args = [
			'post_type' => $elementor_post_types,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'meta_query' => [
				[
					'key' => static::META_KEY_ELEMENTOR_EDIT_MODE,
					'compare' => 'EXISTS',
				],
				[
					'key' => static::META_KEY_ELEMENTOR_IMPORT_SESSION_ID,
					'value' => $data['session_id'],
				],
			],
		];

		$query = new \WP_Query( $query_args );

		foreach ( $query->posts as $post ) {
			$post_type_document = Plugin::$instance->documents->get( $post->ID );
			$post_type_document->delete();

			// Deleting the post will reset the show_on_front option. We need to set it to false,
			// so we can set it back to what it was.
			if ( $post->ID === $this->page_on_front_id ) {
				$this->show_page_on_front = false;
			}
		}

		$this->restore_page_on_front( $data );
	}

	private function init_page_on_front_data() {
		$this->show_page_on_front = 'page' === get_option( 'show_on_front' );

		if ( $this->show_page_on_front ) {
			$this->page_on_front_id = (int) get_option( 'page_on_front' );
		}
	}

	private function restore_page_on_front( $data ) {
		if ( empty( $data['runners'][ static::get_name() ]['page_on_front'] ) ) {
			return;
		}

		$page_on_front = $data['runners'][ static::get_name() ]['page_on_front'];

		$document = Plugin::$instance->documents->get( $page_on_front );

		if ( ! $document ) {
			return;
		}

		$this->set_page_on_front( $document->get_main_id() );
	}

	private function set_page_on_front( $page_id ) {
		update_option( 'page_on_front', $page_id );

		if ( ! $this->show_page_on_front ) {
			update_option( 'show_on_front', 'page' );
		}
	}
}
