<?php
/**
 * Importer for Woody.
 *
 * @package WPCode.
 */

/**
 * Class WPCode_Importer_Woody.
 */
class WPCode_Importer_Woody extends WPCode_Importer_Type {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	public $name = 'Woody Code Snippets';

	/**
	 * Importer slug.
	 *
	 * @var string
	 */
	public $slug = 'woody';

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	public $path = 'insert-php/insert_php.php';

	/**
	 * Get an array of snippets for this plugin.
	 *
	 * @return array
	 */
	public function get_snippets() {

		$snippets = array();

		if ( ! $this->is_active() ) {
			return $snippets;
		}

		$snippets_posts = get_posts(
			array(
				'post_type'      => 'wbcr-snippets',
				'posts_per_page' => - 1,
				'post_status'    => 'any',
			)
		);

		foreach ( $snippets_posts as $post ) {
			$snippets[ $post->ID ] = $post->post_title;
		}

		return $snippets;

	}

	/**
	 * Import the snippet data.
	 *
	 * @return void
	 */
	public function import_snippet() {
		// Run a security check.
		check_ajax_referer( 'wpcode_admin' );

		if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
			wp_send_json_error();
		}

		if ( ! class_exists( 'WINP_Helper' ) ) {
			wp_send_json_error();
		}

		$id = isset( $_POST['snippet_id'] ) ? absint( $_POST['snippet_id'] ) : 0;

		// Grab a snippet from Code Snippets.
		$snippet = get_post( $id );

		if ( null === $snippet ) {
			wp_send_json_error(
				array(
					'error' => true,
					'name'  => esc_html__( 'Unknown Snippet', 'insert-headers-and-footers' ),
					'msg'   => esc_html__( 'The snippet you are trying to import does not exist.', 'insert-headers-and-footers' ),
				)
			);
		}

		// If we got so far we have a snippet to process.

		// Create a new snippet from the snippet data array.
		$new_snippet = new WPCode_Snippet( $this->get_snippet_data( $snippet ) );

		$new_snippet->save();

		if ( ! empty( $new_snippet->get_id() ) ) {
			wp_send_json_success(
				array(
					'name' => $new_snippet->get_title(),
					'edit' => esc_url_raw(
						add_query_arg(
							array(
								'page'       => 'wpcode-snippet-manager',
								'snippet_id' => $new_snippet->get_id(),
							),
							admin_url( 'admin.php' )
						)
					),
				)
			);
		}
	}


	/**
	 * Convert a "Woody" snippet to the format for a WPCode snippet.
	 *
	 * @param WP_Post $snippet The snippet post.
	 *
	 * @return array
	 */
	public function get_snippet_data( $snippet ) {

		$snippet_location = WINP_Helper::getMetaOption( $snippet->ID, 'snippet_location', '' );
		$scope            = WINP_Helper::getMetaOption( $snippet->ID, 'snippet_scope', '' );
		$auto_insert      = in_array(
			$scope,
			array(
				'auto',
				'evrywhere',
			),
			true
		) ? 1 : 0;

		switch ( $snippet_location ) {
			case 'header':
				$location = 'site_wide_header';
				break;
			case 'footer':
				$location = 'site_wide_footer';
				break;
			default:
				$location = $snippet_location;
		}

		return array(
			'code'        => wp_slash( WINP_Helper::get_snippet_code( $snippet ) ),
			'note'        => WINP_Helper::getMetaOption( $snippet->ID, 'snippet_description', '' ),
			'title'       => $snippet->post_title,
			'tags'        => wp_get_post_terms( $snippet->ID, WINP_SNIPPETS_TAXONOMY, array( 'fields' => 'slugs' ) ),
			'code_type'   => WINP_Helper::get_snippet_type( $snippet->ID ),
			'priority'    => intval( WINP_Helper::getMetaOption( $snippet->ID, 'snippet_priority', '' ) ),
			'location'    => $location,
			'auto_insert' => $auto_insert,
		);
	}
}
