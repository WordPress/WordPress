<?php
/**
 * REST API: WP_REST_Menus_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.9.0
 */

/**
 * Core class used to managed menu terms associated via the REST API.
 *
 * @since 5.9.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Menus_Controller extends WP_REST_Terms_Controller {

	/**
	 * Checks if a request has access to read menus.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error True if the request has read access, otherwise false or WP_Error object.
	 */
	public function get_items_permissions_check( $request ) {
		$has_permission = parent::get_items_permissions_check( $request );

		if ( true !== $has_permission ) {
			return $has_permission;
		}

		return $this->check_has_read_only_access( $request );
	}

	/**
	 * Checks if a request has access to read or edit the specified menu.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error True if the request has read access for the item, otherwise false or WP_Error object.
	 */
	public function get_item_permissions_check( $request ) {
		$has_permission = parent::get_item_permissions_check( $request );

		if ( true !== $has_permission ) {
			return $has_permission;
		}

		return $this->check_has_read_only_access( $request );
	}

	/**
	 * Gets the term, if the ID is valid.
	 *
	 * @since 5.9.0
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Term|WP_Error Term object if ID is valid, WP_Error otherwise.
	 */
	protected function get_term( $id ) {
		$term = parent::get_term( $id );

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		$nav_term           = wp_get_nav_menu_object( $term );
		$nav_term->auto_add = $this->get_menu_auto_add( $nav_term->term_id );

		return $nav_term;
	}

	/**
	 * Checks whether the current user has read permission for the endpoint.
	 *
	 * This allows for any user that can `edit_theme_options` or edit any REST API available post type.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error Whether the current user has permission.
	 */
	protected function check_has_read_only_access( $request ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			return true;
		}

		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new WP_Error(
			'rest_cannot_view',
			__( 'Sorry, you are not allowed to view menus.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Prepares a single term output for response.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Term         $term    Term object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $term, $request ) {
		$nav_menu = wp_get_nav_menu_object( $term );
		$response = parent::prepare_item_for_response( $nav_menu, $request );

		$fields = $this->get_fields_for_response( $request );
		$data   = $response->get_data();

		if ( rest_is_field_included( 'locations', $fields ) ) {
			$data['locations'] = $this->get_menu_locations( $nav_menu->term_id );
		}

		if ( rest_is_field_included( 'auto_add', $fields ) ) {
			$data['auto_add'] = $this->get_menu_auto_add( $nav_menu->term_id );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $term ) );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php */
		return apply_filters( "rest_prepare_{$this->taxonomy}", $response, $term, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Term $term Term object.
	 * @return array Links for the given term.
	 */
	protected function prepare_links( $term ) {
		$links = parent::prepare_links( $term );

		$locations = $this->get_menu_locations( $term->term_id );
		foreach ( $locations as $location ) {
			$url = rest_url( sprintf( 'wp/v2/menu-locations/%s', $location ) );

			$links['https://api.w.org/menu-location'][] = array(
				'href'       => $url,
				'embeddable' => true,
			);
		}

		return $links;
	}

	/**
	 * Prepares a single term for create or update.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return object Prepared term data.
	 */
	public function prepare_item_for_database( $request ) {
		$prepared_term = parent::prepare_item_for_database( $request );

		$schema = $this->get_item_schema();

		if ( isset( $request['name'] ) && ! empty( $schema['properties']['name'] ) ) {
			$prepared_term->{'menu-name'} = $request['name'];
		}

		return $prepared_term;
	}

	/**
	 * Creates a single term in a taxonomy.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( isset( $request['parent'] ) ) {
			if ( ! is_taxonomy_hierarchical( $this->taxonomy ) ) {
				return new WP_Error( 'rest_taxonomy_not_hierarchical', __( 'Cannot set parent term, taxonomy is not hierarchical.' ), array( 'status' => 400 ) );
			}

			$parent = wp_get_nav_menu_object( (int) $request['parent'] );

			if ( ! $parent ) {
				return new WP_Error( 'rest_term_invalid', __( 'Parent term does not exist.' ), array( 'status' => 400 ) );
			}
		}

		$prepared_term = $this->prepare_item_for_database( $request );

		$term = wp_update_nav_menu_object( 0, wp_slash( (array) $prepared_term ) );

		if ( is_wp_error( $term ) ) {
			/*
			 * If we're going to inform the client that the term already exists,
			 * give them the identifier for future use.
			 */

			if ( in_array( 'menu_exists', $term->get_error_codes(), true ) ) {
				$existing_term = get_term_by( 'name', $prepared_term->{'menu-name'}, $this->taxonomy );
				$term->add_data( $existing_term->term_id, 'menu_exists' );
				$term->add_data(
					array(
						'status'  => 400,
						'term_id' => $existing_term->term_id,
					)
				);
			} else {
				$term->add_data( array( 'status' => 400 ) );
			}

			return $term;
		}

		$term = $this->get_term( $term );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php */
		do_action( "rest_insert_{$this->taxonomy}", $term, $request, true );

		$schema = $this->get_item_schema();
		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $term->term_id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$locations_update = $this->handle_locations( $term->term_id, $request );

		if ( is_wp_error( $locations_update ) ) {
			return $locations_update;
		}

		$this->handle_auto_add( $term->term_id, $request );

		$fields_update = $this->update_additional_fields_for_object( $term, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'view' );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php */
		do_action( "rest_after_insert_{$this->taxonomy}", $term, $request, true );

		$response = $this->prepare_item_for_response( $term, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( $this->namespace . '/' . $this->rest_base . '/' . $term->term_id ) );

		return $response;
	}

	/**
	 * Updates a single term from a taxonomy.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$term = $this->get_term( $request['id'] );
		if ( is_wp_error( $term ) ) {
			return $term;
		}

		if ( isset( $request['parent'] ) ) {
			if ( ! is_taxonomy_hierarchical( $this->taxonomy ) ) {
				return new WP_Error( 'rest_taxonomy_not_hierarchical', __( 'Cannot set parent term, taxonomy is not hierarchical.' ), array( 'status' => 400 ) );
			}

			$parent = get_term( (int) $request['parent'], $this->taxonomy );

			if ( ! $parent ) {
				return new WP_Error( 'rest_term_invalid', __( 'Parent term does not exist.' ), array( 'status' => 400 ) );
			}
		}

		$prepared_term = $this->prepare_item_for_database( $request );

		// Only update the term if we have something to update.
		if ( ! empty( $prepared_term ) ) {
			if ( ! isset( $prepared_term->{'menu-name'} ) ) {
				// wp_update_nav_menu_object() requires that the menu-name is always passed.
				$prepared_term->{'menu-name'} = $term->name;
			}

			$update = wp_update_nav_menu_object( $term->term_id, wp_slash( (array) $prepared_term ) );

			if ( is_wp_error( $update ) ) {
				return $update;
			}
		}

		$term = get_term( $term->term_id, $this->taxonomy );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php */
		do_action( "rest_insert_{$this->taxonomy}", $term, $request, false );

		$schema = $this->get_item_schema();
		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $term->term_id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$locations_update = $this->handle_locations( $term->term_id, $request );

		if ( is_wp_error( $locations_update ) ) {
			return $locations_update;
		}

		$this->handle_auto_add( $term->term_id, $request );

		$fields_update = $this->update_additional_fields_for_object( $term, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'view' );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php */
		do_action( "rest_after_insert_{$this->taxonomy}", $term, $request, false );

		$response = $this->prepare_item_for_response( $term, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Deletes a single term from a taxonomy.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$term = $this->get_term( $request['id'] );
		if ( is_wp_error( $term ) ) {
			return $term;
		}

		// We don't support trashing for terms.
		if ( ! $request['force'] ) {
			/* translators: %s: force=true */
			return new WP_Error( 'rest_trash_not_supported', sprintf( __( "Menus do not support trashing. Set '%s' to delete." ), 'force=true' ), array( 'status' => 501 ) );
		}

		$request->set_param( 'context', 'view' );

		$previous = $this->prepare_item_for_response( $term, $request );

		$result = wp_delete_nav_menu( $term );

		if ( ! $result || is_wp_error( $result ) ) {
			return new WP_Error( 'rest_cannot_delete', __( 'The menu cannot be deleted.' ), array( 'status' => 500 ) );
		}

		$response = new WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php */
		do_action( "rest_delete_{$this->taxonomy}", $term, $response, $request );

		return $response;
	}

	/**
	 * Returns the value of a menu's auto_add setting.
	 *
	 * @since 5.9.0
	 *
	 * @param int $menu_id The menu id to query.
	 * @return bool The value of auto_add.
	 */
	protected function get_menu_auto_add( $menu_id ) {
		$nav_menu_option = (array) get_option( 'nav_menu_options', array( 'auto_add' => array() ) );

		return in_array( $menu_id, $nav_menu_option['auto_add'], true );
	}

	/**
	 * Updates the menu's auto add from a REST request.
	 *
	 * @since 5.9.0
	 *
	 * @param int             $menu_id The menu id to update.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the auto add setting was successfully updated.
	 */
	protected function handle_auto_add( $menu_id, $request ) {
		if ( ! isset( $request['auto_add'] ) ) {
			return true;
		}

		$nav_menu_option = (array) get_option( 'nav_menu_options', array( 'auto_add' => array() ) );

		if ( ! isset( $nav_menu_option['auto_add'] ) ) {
			$nav_menu_option['auto_add'] = array();
		}

		$auto_add = $request['auto_add'];

		$i = array_search( $menu_id, $nav_menu_option['auto_add'], true );

		if ( $auto_add && false === $i ) {
			$nav_menu_option['auto_add'][] = $menu_id;
		} elseif ( ! $auto_add && false !== $i ) {
			array_splice( $nav_menu_option['auto_add'], $i, 1 );
		}

		$update = update_option( 'nav_menu_options', $nav_menu_option );

		/** This action is documented in wp-includes/nav-menu.php */
		do_action( 'wp_update_nav_menu', $menu_id );

		return $update;
	}

	/**
	 * Returns the names of the locations assigned to the menu.
	 *
	 * @since 5.9.0
	 *
	 * @param int $menu_id The menu id.
	 * @return string[] The locations assigned to the menu.
	 */
	protected function get_menu_locations( $menu_id ) {
		$locations      = get_nav_menu_locations();
		$menu_locations = array();

		foreach ( $locations as $location => $assigned_menu_id ) {
			if ( $menu_id === $assigned_menu_id ) {
				$menu_locations[] = $location;
			}
		}

		return $menu_locations;
	}

	/**
	 * Updates the menu's locations from a REST request.
	 *
	 * @since 5.9.0
	 *
	 * @param int             $menu_id The menu id to update.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True on success, a WP_Error on an error updating any of the locations.
	 */
	protected function handle_locations( $menu_id, $request ) {
		if ( ! isset( $request['locations'] ) ) {
			return true;
		}

		$menu_locations = get_registered_nav_menus();
		$menu_locations = array_keys( $menu_locations );
		$new_locations  = array();
		foreach ( $request['locations'] as $location ) {
			if ( ! in_array( $location, $menu_locations, true ) ) {
				return new WP_Error(
					'rest_invalid_menu_location',
					__( 'Invalid menu location.' ),
					array(
						'status'   => 400,
						'location' => $location,
					)
				);
			}
			$new_locations[ $location ] = $menu_id;
		}
		$assigned_menu = get_nav_menu_locations();
		foreach ( $assigned_menu as $location => $term_id ) {
			if ( $term_id === $menu_id ) {
				unset( $assigned_menu[ $location ] );
			}
		}
		$new_assignments = array_merge( $assigned_menu, $new_locations );
		set_theme_mod( 'nav_menu_locations', $new_assignments );

		return true;
	}

	/**
	 * Retrieves the term's schema, conforming to JSON Schema.
	 *
	 * @since 5.9.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();
		unset( $schema['properties']['count'], $schema['properties']['link'], $schema['properties']['taxonomy'] );

		$schema['properties']['locations'] = array(
			'description' => __( 'The locations assigned to the menu.' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'string',
			),
			'context'     => array( 'view', 'edit' ),
			'arg_options' => array(
				'validate_callback' => function ( $locations, $request, $param ) {
					$valid = rest_validate_request_arg( $locations, $request, $param );

					if ( true !== $valid ) {
						return $valid;
					}

					$locations = rest_sanitize_request_arg( $locations, $request, $param );

					foreach ( $locations as $location ) {
						if ( ! array_key_exists( $location, get_registered_nav_menus() ) ) {
							return new WP_Error(
								'rest_invalid_menu_location',
								__( 'Invalid menu location.' ),
								array(
									'location' => $location,
								)
							);
						}
					}

					return true;
				},
			),
		);

		$schema['properties']['auto_add'] = array(
			'description' => __( 'Whether to automatically add top level pages to this menu.' ),
			'context'     => array( 'view', 'edit' ),
			'type'        => 'boolean',
		);

		return $schema;
	}
}
