<?php

namespace Elementor\Modules\FloatingButtons\Classes\Action;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Modules\FloatingButtons\Classes\Conditions\Conditions_Cache;
use Elementor\Modules\FloatingButtons\Documents\Floating_Buttons;
use Elementor\Modules\FloatingButtons\Module;

class Action_Handler {

	protected string $action;
	protected array $menu_args;
	protected Conditions_Cache $conditions_cache;

	public function __construct( string $action, array $menu_args ) {
		$this->action = $action;
		$this->menu_args = $menu_args;
		$this->conditions_cache = new Conditions_Cache();
	}

	public function process_action() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		switch ( $this->action ) {
			case 'remove_from_entire_site':
				$this->handle_remove_from_entire_site();
				break;
			case 'set_as_entire_site':
				$this->handle_set_as_entire_site();
				break;
			default:
				break;
		}
	}

	private function handle_remove_from_entire_site(): void {
		$post_id = filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );
		check_admin_referer( 'remove_from_entire_site_' . $post_id );
		delete_post_meta( $post_id, '_elementor_conditions' );
		$this->conditions_cache->remove_from_cache( $post_id );

		wp_redirect( $this->menu_args['menu_slug'] );
		exit;
	}

	private function handle_set_as_entire_site(): void {
		$post_id = filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );
		check_admin_referer( 'set_as_entire_site_' . $post_id );

		$posts = $this->get_published_floating_elements( $post_id );

		foreach ( $posts as $post_id_to_delete ) {
			delete_post_meta( $post_id_to_delete, '_elementor_conditions' );
			$this->conditions_cache->remove_from_cache( $post_id_to_delete );
		}

		update_post_meta( $post_id, '_elementor_conditions', [ 'include/general' ] );
		$this->conditions_cache->add_to_cache( $post_id );

		wp_redirect( $this->menu_args['menu_slug'] );
		exit;
	}

	private function get_published_floating_elements( int $post_id ): array {
		return get_posts( [
			'post_type'              => Module::CPT_FLOATING_BUTTONS,
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'meta_query'             => Floating_Buttons::get_meta_query_for_floating_buttons(
				Floating_Buttons::get_floating_element_type( $post_id )
			),
		] );
	}
}
