<?php
/**
 * REST API: WP_REST_View_Config_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since      7.1.0
 */

/**
 * Controller which provides a REST endpoint for retrieving the default
 * view configuration for a given entity type.
 *
 * @since 7.1.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_View_Config_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 7.1.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'view-config';
	}

	/**
	 * Registers the routes for the controller.
	 *
	 * @since 7.1.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(
						'kind' => array(
							'description' => __( 'Entity kind.' ),
							'type'        => 'string',
							'required'    => true,
						),
						'name' => array(
							'description' => __( 'Entity name.' ),
							'type'        => 'string',
							'required'    => true,
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read view config.
	 *
	 * @since 7.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$kind = $request->get_param( 'kind' );
		$name = $request->get_param( 'name' );

		$capability = $this->get_required_capability( $kind, $name );

		if ( null === $capability ) {
			return new WP_Error(
				'rest_view_config_invalid_entity',
				__( 'Invalid entity kind or name.' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( $capability ) ) {
			return new WP_Error(
				'rest_cannot_read',
				__( 'Sorry, you are not allowed to read view config.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Resolves the capability required to read the view config for an entity.
	 *
	 * Known kinds map to the capability that gates managing that entity's list:
	 * post types use their own `edit_posts` capability (which honors custom
	 * `capability_type` registrations), taxonomies use `manage_terms`, and
	 * root-level entities use `manage_options`. A post type or taxonomy that is
	 * not registered, or not exposed to the REST API, resolves to `null` so the
	 * request is treated as referencing an unknown entity.
	 *
	 * Any other kind falls back to `edit_posts`. This keeps entities registered
	 * through the `get_entity_view_config_{$kind}_{$name}` filter readable behind
	 * a baseline capability.
	 *
	 * @since 7.1.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return string|null Capability required to read the config, or null if the
	 *                     entity is not registered.
	 */
	protected function get_required_capability( $kind, $name ) {
		switch ( $kind ) {
			case 'postType':
				$post_type = get_post_type_object( $name );
				if ( $post_type && $post_type->show_in_rest ) {
					return $post_type->cap->edit_posts;
				}
				return null;

			case 'taxonomy':
				$taxonomy = get_taxonomy( $name );
				if ( $taxonomy && $taxonomy->show_in_rest ) {
					return $taxonomy->cap->manage_terms;
				}
				return null;

			case 'root':
				return 'manage_options';
		}

		return 'edit_posts';
	}

	/**
	 * Returns the default view configuration for the given entity type.
	 *
	 * @since 7.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$kind = $request->get_param( 'kind' );
		$name = $request->get_param( 'name' );

		$config = wp_get_entity_view_config( $kind, $name );
		$schema = $this->get_item_schema();

		$response = array(
			'kind'            => $kind,
			'name'            => $name,
			'default_view'    => $this->cast_empty_objects( $config['default_view'], $schema['properties']['default_view'] ),
			'default_layouts' => $this->cast_empty_objects( $config['default_layouts'], $schema['properties']['default_layouts'] ),
			'view_list'       => $this->cast_empty_objects( $config['view_list'], $schema['properties']['view_list'] ),
			'form'            => $this->cast_empty_objects( $config['form'], $schema['properties']['form'] ),
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Recursively casts empty arrays to objects where the schema types them as
	 * objects.
	 *
	 * PHP cannot distinguish an empty associative array from an empty list, so
	 * `json_encode()` always serializes `array()` as a JSON array (`[]`). The
	 * REST schema, however, types several values as objects, which must encode
	 * as `{}`. This walks the value against its schema and casts any empty,
	 * object-typed array to an object. Non-empty associative arrays already
	 * encode as objects, so they are left as arrays and only recursed into to
	 * fix any nested empty objects.
	 *
	 * Union schemas (`oneOf`/`anyOf`) are handled only for the empty-array case:
	 * an empty value is cast to an object when any branch allows an object. Such
	 * values are not recursed into, which is sufficient for the form schema
	 * where they never contain empty nested objects.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $value  The value to normalize.
	 * @param array $schema The schema node describing the value.
	 * @return mixed The normalized value, with empty object-typed arrays cast to objects.
	 */
	protected function cast_empty_objects( $value, $schema ) {
		if ( ! is_array( $value ) || ! is_array( $schema ) ) {
			return $value;
		}

		if ( isset( $schema['oneOf'] ) || isset( $schema['anyOf'] ) ) {
			$branches = isset( $schema['oneOf'] ) ? $schema['oneOf'] : $schema['anyOf'];
			if ( array() === $value ) {
				foreach ( $branches as $branch ) {
					if ( is_array( $branch ) && in_array( 'object', (array) ( isset( $branch['type'] ) ? $branch['type'] : array() ), true ) ) {
						return (object) array();
					}
				}
			}
			return $value;
		}

		$types = (array) ( isset( $schema['type'] ) ? $schema['type'] : array() );

		if ( in_array( 'array', $types, true ) && isset( $schema['items'] ) ) {
			foreach ( $value as $index => $item ) {
				$value[ $index ] = $this->cast_empty_objects( $item, $schema['items'] );
			}
			return $value;
		}

		if ( in_array( 'object', $types, true ) ) {
			if ( isset( $schema['properties'] ) ) {
				foreach ( $schema['properties'] as $property => $property_schema ) {
					if ( array_key_exists( $property, $value ) ) {
						$value[ $property ] = $this->cast_empty_objects( $value[ $property ], $property_schema );
					}
				}
			}
			if ( isset( $schema['additionalProperties'] ) && is_array( $schema['additionalProperties'] ) ) {
				foreach ( $value as $key => $item ) {
					if ( isset( $schema['properties'][ $key ] ) ) {
						continue;
					}
					$value[ $key ] = $this->cast_empty_objects( $item, $schema['additionalProperties'] );
				}
			}

			// Empty object-typed arrays must serialize as {} to match the schema.
			if ( array() === $value ) {
				return (object) array();
			}
		}

		return $value;
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 7.1.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$view_base_properties = $this->get_view_base_schema();

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'view-config',
			'type'       => 'object',
			'properties' => array(
				'kind'            => array(
					'description' => __( 'Entity kind.' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'name'            => array(
					'description' => __( 'Entity name.' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'default_view'    => array(
					'description' => __( 'Default view configuration.' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array_merge(
						array(
							'type'   => array(
								'type' => 'string',
							),
							'layout' => $this->get_combined_layout_schema(),
						),
						$view_base_properties
					),
				),
				'default_layouts' => array(
					'description' => __( 'Default layout configurations.' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'table'       => array(
							'type'       => 'object',
							'properties' => array_merge(
								$view_base_properties,
								array(
									'layout' => $this->get_table_layout_schema(),
								)
							),
						),
						'list'        => array(
							'type'       => 'object',
							'properties' => array_merge(
								$view_base_properties,
								array(
									'layout' => $this->get_list_layout_schema(),
								)
							),
						),
						'grid'        => array(
							'type'       => 'object',
							'properties' => array_merge(
								$view_base_properties,
								array(
									'layout' => $this->get_grid_layout_schema(),
								)
							),
						),
						'activity'    => array(
							'type'       => 'object',
							'properties' => array_merge(
								$view_base_properties,
								array(
									'layout' => $this->get_list_layout_schema(),
								)
							),
						),
						'pickerGrid'  => array(
							'type'       => 'object',
							'properties' => array_merge(
								$view_base_properties,
								array(
									'layout' => $this->get_grid_layout_schema(),
								)
							),
						),
						'pickerTable' => array(
							'type'       => 'object',
							'properties' => array_merge(
								$view_base_properties,
								array(
									'layout' => $this->get_table_layout_schema(),
								)
							),
						),
					),
				),
				'view_list'       => array(
					'description' => __( 'List of default views.' ),
					'type'        => 'array',
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'title' => array(
								'type' => 'string',
							),
							'slug'  => array(
								'type' => 'string',
							),
							'view'  => array(
								'type'       => 'object',
								'properties' => array_merge(
									array(
										'type'   => array(
											'type' => 'string',
										),
										'layout' => $this->get_combined_layout_schema(),
									),
									$view_base_properties
								),
							),
						),
					),
				),
				'form'            => array(
					'description' => __( 'Default form configuration.' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => $this->get_form_schema(),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Returns the schema properties shared by all view types (ViewBase), excluding 'type'.
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema properties for the base view configuration.
	 */
	protected function get_view_base_schema() {
		return array(
			'search'                => array(
				'type' => 'string',
			),
			'filters'               => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'field'    => array(
							'type' => 'string',
						),
						'operator' => array(
							'type' => 'string',
							'enum' => array(
								'is',
								'isNot',
								'isAny',
								'isNone',
								'isAll',
								'isNotAll',
								'lessThan',
								'greaterThan',
								'lessThanOrEqual',
								'greaterThanOrEqual',
								'before',
								'after',
							),
						),
						'value'    => array(),
						'isLocked' => array(
							'type' => 'boolean',
						),
					),
				),
			),
			'sort'                  => array(
				'type'       => 'object',
				'properties' => array(
					'field'     => array(
						'type' => 'string',
					),
					'direction' => array(
						'type' => 'string',
						'enum' => array( 'asc', 'desc' ),
					),
				),
			),
			'page'                  => array(
				'type' => 'integer',
			),
			'perPage'               => array(
				'type' => 'integer',
			),
			'fields'                => array(
				'type'  => 'array',
				'items' => array(
					'type' => 'string',
				),
			),
			'titleField'            => array(
				'type' => 'string',
			),
			'mediaField'            => array(
				'type' => 'string',
			),
			'descriptionField'      => array(
				'type' => 'string',
			),
			'showTitle'             => array(
				'type' => 'boolean',
			),
			'showMedia'             => array(
				'type' => 'boolean',
			),
			'showDescription'       => array(
				'type' => 'boolean',
			),
			'showLevels'            => array(
				'type' => 'boolean',
			),
			'groupBy'               => array(
				'type'       => 'object',
				'properties' => array(
					'field'     => array(
						'type' => 'string',
					),
					'direction' => array(
						'type' => 'string',
						'enum' => array( 'asc', 'desc' ),
					),
					'showLabel' => array(
						'type'    => 'boolean',
						'default' => true,
					),
				),
			),
			'infiniteScrollEnabled' => array(
				'type' => 'boolean',
			),
		);
	}

	/**
	 * Returns the schema for the ColumnStyle type.
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a column style object.
	 */
	protected function get_column_style_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'width'    => array(
					'type' => array( 'string', 'number' ),
				),
				'maxWidth' => array(
					'type' => array( 'string', 'number' ),
				),
				'minWidth' => array(
					'type' => array( 'string', 'number' ),
				),
				'align'    => array(
					'type' => 'string',
					'enum' => array( 'start', 'center', 'end' ),
				),
			),
		);
	}

	/**
	 * Returns the layout schema for table-type views (ViewTable, ViewPickerTable).
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a table layout object.
	 */
	protected function get_table_layout_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'styles'       => array(
					'type'                 => 'object',
					'additionalProperties' => $this->get_column_style_schema(),
				),
				'density'      => array(
					'type' => 'string',
					'enum' => array( 'compact', 'balanced', 'comfortable' ),
				),
				'enableMoving' => array(
					'type' => 'boolean',
				),
			),
		);
	}

	/**
	 * Returns the layout schema for list-type views (ViewList, ViewActivity).
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a list layout object.
	 */
	protected function get_list_layout_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'density' => array(
					'type' => 'string',
					'enum' => array( 'compact', 'balanced', 'comfortable' ),
				),
			),
		);
	}

	/**
	 * Returns a combined layout schema that accepts properties from all view types.
	 *
	 * This is useful for contexts where the view type is not known ahead of time
	 * (e.g. the `view` override in a view list item), so all possible layout
	 * properties must be accepted.
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a combined layout object.
	 */
	protected function get_combined_layout_schema() {
		return array(
			'type'       => 'object',
			'properties' => array_merge(
				$this->get_table_layout_schema()['properties'],
				$this->get_grid_layout_schema()['properties'],
				$this->get_list_layout_schema()['properties']
			),
		);
	}

	/**
	 * Returns the layout schema for grid-type views (ViewGrid, ViewPickerGrid).
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a grid layout object.
	 */
	protected function get_grid_layout_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'badgeFields' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
				'previewSize' => array(
					'type' => 'number',
				),
				'density'     => array(
					'type' => 'string',
					'enum' => array( 'compact', 'balanced', 'comfortable' ),
				),
			),
		);
	}

	/**
	 * Returns the schema for a form layout object as a discriminated union.
	 *
	 * Each variant is discriminated by a single-value enum on its `type` property,
	 * matching the TypeScript Layout union in dataviews/src/types/dataform.ts.
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a form layout object.
	 */
	protected function get_form_layout_schema() {
		return array(
			'oneOf' => array(
				// RegularLayout.
				array(
					'type'       => 'object',
					'properties' => array(
						'type'          => array(
							'type' => 'string',
							'enum' => array( 'regular' ),
						),
						'labelPosition' => array(
							'type' => 'string',
							'enum' => array( 'top', 'side', 'none' ),
						),
					),
				),
				// PanelLayout.
				array(
					'type'       => 'object',
					'properties' => array(
						'type'           => array(
							'type' => 'string',
							'enum' => array( 'panel' ),
						),
						'labelPosition'  => array(
							'type' => 'string',
							'enum' => array( 'top', 'side', 'none' ),
						),
						'openAs'         => array(
							'oneOf' => array(
								array(
									'type' => 'string',
									'enum' => array( 'dropdown', 'modal' ),
								),
								array(
									'type'       => 'object',
									'properties' => array(
										'type'        => array(
											'type' => 'string',
											'enum' => array( 'dropdown', 'modal' ),
										),
										'applyLabel'  => array(
											'type' => 'string',
										),
										'cancelLabel' => array(
											'type' => 'string',
										),
									),
								),
							),
						),
						'summary'        => array(
							'oneOf' => array(
								array( 'type' => 'string' ),
								array(
									'type'  => 'array',
									'items' => array(
										'type' => 'string',
									),
								),
							),
						),
						'editVisibility' => array(
							'type' => 'string',
							'enum' => array( 'always', 'on-hover' ),
						),
					),
				),
				// CardLayout.
				array(
					'type'       => 'object',
					'properties' => array(
						'type'          => array(
							'type' => 'string',
							'enum' => array( 'card' ),
						),
						'withHeader'    => array(
							'type' => 'boolean',
						),
						'isOpened'      => array(
							'type' => 'boolean',
						),
						'isCollapsible' => array(
							'type' => 'boolean',
						),
						'summary'       => array(
							'oneOf' => array(
								array( 'type' => 'string' ),
								array(
									'type'  => 'array',
									'items' => array(
										'oneOf' => array(
											array( 'type' => 'string' ),
											array(
												'type' => 'object',
												'properties' => array(
													'id' => array(
														'type' => 'string',
													),
													'visibility' => array(
														'type' => 'string',
														'enum' => array( 'always', 'when-collapsed' ),
													),
												),
											),
										),
									),
								),
							),
						),
					),
				),
				// RowLayout.
				array(
					'type'       => 'object',
					'properties' => array(
						'type'      => array(
							'type' => 'string',
							'enum' => array( 'row' ),
						),
						'alignment' => array(
							'type' => 'string',
							'enum' => array( 'start', 'center', 'end' ),
						),
						'styles'    => array(
							'type'                 => 'object',
							'additionalProperties' => array(
								'type'       => 'object',
								'properties' => array(
									'flex' => array(
										'type' => array( 'string', 'number' ),
									),
								),
							),
						),
					),
				),
				// DetailsLayout.
				array(
					'type'       => 'object',
					'properties' => array(
						'type'    => array(
							'type' => 'string',
							'enum' => array( 'details' ),
						),
						'summary' => array(
							'type' => 'string',
						),
					),
				),
			),
		);
	}

	/**
	 * Returns the schema for a form field item (string or object).
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema for a form field.
	 */
	protected function get_form_field_schema() {
		return array(
			'oneOf' => array(
				array( 'type' => 'string' ),
				array(
					'type'       => 'object',
					'properties' => array(
						'id'          => array(
							'type' => 'string',
						),
						'label'       => array(
							'type' => 'string',
						),
						'description' => array(
							'type' => 'string',
						),
						'layout'      => $this->get_form_layout_schema(),
						'children'    => array(
							'type'  => 'array',
							'items' => array(
								'oneOf' => array(
									array( 'type' => 'string' ),
									// This object can have the shape of a form field itself,
									// allowing for recursive nesting of form fields.
									// There's no easy way to codify this recursion via the JSON Schema draft-04
									// supported by the REST API.
									array( 'type' => 'object' ),
								),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Returns the schema for the form configuration object.
	 *
	 * @since 7.1.0
	 *
	 * @return array Schema properties for the form configuration.
	 */
	protected function get_form_schema() {
		return array(
			'layout' => $this->get_form_layout_schema(),
			'fields' => array(
				'type'  => 'array',
				'items' => $this->get_form_field_schema(),
			),
		);
	}
}
