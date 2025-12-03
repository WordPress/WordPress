<?php

class Gutenberg_REST_Static_Templates_Controller extends WP_REST_Templates_Controller {
	public function __construct() {
		$this->rest_base = 'registered-templates';
		$this->namespace = 'wp/v2';
	}

	public function register_routes() {
		// Lists all templates.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// Lists/updates a single template based on the given id.
		register_rest_route(
			$this->namespace,
			// The route.
			sprintf(
				'/%s/(?P<id>%s%s)',
				$this->rest_base,
				/*
				 * Matches theme's directory: `/themes/<subdirectory>/<theme>/` or `/themes/<theme>/`.
				 * Excludes invalid directory name characters: `/:<>*?"|`.
				 */
				'([^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?)',
				// Matches the template name.
				'[\/\w%-]+'
			),
			array(
				'args'   => array(
					'id' => array(
						'description'       => __( 'The id of a template' ),
						'type'              => 'string',
						'sanitize_callback' => array( $this, '_sanitize_template_id' ),
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	public function get_item_schema() {
		$schema                            = parent::get_item_schema();
		$schema['properties']['is_custom'] = array(
			'description' => __( 'Whether a template is a custom template.' ),
			'type'        => 'bool',
			'context'     => array( 'embed', 'view', 'edit' ),
			'readonly'    => true,
		);
		$schema['properties']['plugin']    = array(
			'type'        => 'string',
			'description' => __( 'Plugin that registered the template.' ),
			'readonly'    => true,
			'context'     => array( 'view', 'edit', 'embed' ),
		);
		return $schema;
	}

	public function get_items( $request ) {
		$query = array();
		if ( isset( $request['area'] ) ) {
			$query['area'] = $request['area'];
		}
		if ( isset( $request['post_type'] ) ) {
			$query['post_type'] = $request['post_type'];
		}
		$query_result = gutenberg_get_registered_block_templates( $query );
		$templates    = array();
		foreach ( $query_result as $template ) {
			$item        = $this->prepare_item_for_response( $template, $request );
			$templates[] = $this->prepare_response_for_collection( $item );
		}

		return rest_ensure_response( $templates );
	}

	public function get_item( $request ) {
		$template = get_block_file_template( $request['id'], 'wp_template' );

		if ( ! $template ) {
			return new WP_Error( 'rest_template_not_found', __( 'No templates exist with that id.' ), array( 'status' => 404 ) );
		}

		$item = $this->prepare_item_for_response( $template, $request );
		return rest_ensure_response( $item );
	}
}
