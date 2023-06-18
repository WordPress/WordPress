<?php
/**
 * Importer for Code Snippets.
 *
 * @package WPCode.
 */

/**
 * Class WPCode_Importer_Code_Snippets.
 */
class WPCode_Importer_Code_Snippets extends WPCode_Importer_Type {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	public $name = 'Code Snippets';

	/**
	 * Importer slug.
	 *
	 * @var string
	 */
	public $slug = 'code-snippets';

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	public $path = 'code-snippets/code-snippets.php';

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

		if ( ! function_exists( '\Code_Snippets\get_snippets' ) ) {
			return $snippets;
		}

		$code_snippets = \Code_Snippets\get_snippets();

		foreach ( $code_snippets as $code_snippet ) {
			/**
			 * The Code Snippets Snippet object.
			 *
			 * @var \Code_Snippets\Snippet $code_snippet
			 */
			$snippets[ $code_snippet->id ] = $code_snippet->name;
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

		if ( ! function_exists( '\Code_Snippets\get_snippets' ) ) {
			wp_send_json_error();
		}

		$id = isset( $_POST['snippet_id'] ) ? absint( $_POST['snippet_id'] ) : 0;

		// Grab a snippet from Code Snippets.
		$snippets = \Code_Snippets\get_snippets( array( $id ) );

		if ( empty( $snippets ) || empty( $snippets[0] ) ) {
			wp_send_json_error(
				array(
					'error' => true,
					'name'  => esc_html__( 'Unknown Snippet', 'insert-headers-and-footers' ),
					'msg'   => esc_html__( 'The snippet you are trying to import does not exist.', 'insert-headers-and-footers' ),
				)
			);
		}

		// If we got so far we have a snippet to process.
		$snippet = $snippets[0];

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
	 * Convert a "Code Snippets" snippet to the format for a WPCode snippet.
	 *
	 * @param \Code_Snippets\Snippet $snippet The snippet object.
	 *
	 * @return array
	 */
	public function get_snippet_data( $snippet ) {

		$code_type   = $this->get_snippet_type( $snippet );
		$auto_insert = 1;
		switch ( $snippet->scope ) {
			case 'admin':
				$location = 'admin_only';
				break;
			case 'front-end':
				$location = 'frontend_only';
				break;
			default:
				$location = 'everywhere';
		}
		if ( 'php' !== $code_type ) {
			$location    = '';
			$auto_insert = 0;
		}

		return array(
			'code'        => wp_slash( $snippet->code ),
			'note'        => $snippet->desc,
			'title'       => $snippet->name,
			'tags'        => $snippet->tags,
			'code_type'   => $code_type,
			'priority'    => $snippet->priority,
			'location'    => $location,
			'auto_insert' => $auto_insert,
		);
	}

	/**
	 * Get the snippet type from the scope as the method in \Code_Snippets\Snippet is private.
	 *
	 * @param \Code_Snippets\Snippet $snippet The snippet to check the scope for.
	 *
	 * @return string
	 */
	public function get_snippet_type( $snippet ) {
		if ( ! isset( $snippet->scope ) ) {
			return 'php';
		}

		if ( '-css' === substr( $snippet->scope, - 4 ) ) {
			return 'html';
		}

		if ( '-js' === substr( $snippet->scope, - 3 ) ) {
			return 'js';
		}

		if ( 'content' === substr( $snippet->scope, - 7 ) ) {
			return 'universal';
		}

		return 'php';
	}
}
