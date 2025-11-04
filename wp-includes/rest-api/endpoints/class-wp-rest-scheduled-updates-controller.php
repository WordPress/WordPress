<?php
/**
 * REST API: WP_REST_Scheduled_Updates_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 6.6.0
 */

/**
 * Core controller used to manage scheduled post updates via the REST API.
 *
 * @since 6.6.0
 */
class WP_REST_Scheduled_Updates_Controller extends WP_REST_Controller {

        /**
         * Constructor.
         *
         * @since 6.6.0
         */
        public function __construct() {
                $this->namespace = 'wp/v2';
                $this->rest_base = 'scheduled-updates';
        }

        /**
         * Registers the routes for the controller.
         *
         * @since 6.6.0
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
                                        'args'                => $this->get_collection_params(),
                                ),
                        )
                );

                register_rest_route(
                        $this->namespace,
                        '/' . $this->rest_base . '/(?P<id>[\d]+)',
                        array(
                                array(
                                        'methods'             => WP_REST_Server::READABLE,
                                        'callback'            => array( $this, 'get_item' ),
                                        'permission_callback' => array( $this, 'get_item_permissions_check' ),
                                        'args'                => array(
                                                'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                                        ),
                                ),
                                array(
                                        'methods'             => WP_REST_Server::EDITABLE,
                                        'callback'            => array( $this, 'update_item' ),
                                        'permission_callback' => array( $this, 'update_item_permissions_check' ),
                                        'args'                => array(
                                                'scheduled_for' => array(
                                                        'description' => __( 'The date the scheduled update should publish.' ),
                                                        'type'        => 'string',
                                                ),
                                                'title'         => array(
                                                        'description' => __( 'The title for the scheduled update.' ),
                                                        'type'        => 'string',
                                                ),
                                                'content'       => array(
                                                        'description' => __( 'The content for the scheduled update.' ),
                                                        'type'        => 'string',
                                                ),
                                                'excerpt'       => array(
                                                        'description' => __( 'The excerpt for the scheduled update.' ),
                                                        'type'        => 'string',
                                                ),
                                        ),
                                ),
                                array(
                                        'methods'             => WP_REST_Server::DELETABLE,
                                        'callback'            => array( $this, 'delete_item' ),
                                        'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                                ),
                        )
                );
        }

        /**
         * Checks permissions for listing scheduled updates.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return true|WP_Error True if the request has access, WP_Error otherwise.
         */
        public function get_items_permissions_check( $request ) {
                if ( ! current_user_can( 'edit_posts' ) ) {
                        return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to view scheduled updates.' ), array( 'status' => rest_authorization_required_code() ) );
                }

                return true;
        }

        /**
         * Retrieves a collection of scheduled updates.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_REST_Response Collection response.
         */
        public function get_items( $request ) {
                $per_page = (int) $request['per_page'];
                $page     = (int) $request['page'];

                if ( $per_page < 1 ) {
                        $per_page = 1;
                } elseif ( $per_page > 100 ) {
                        $per_page = 100;
                }

                $args = array(
                        'number' => $per_page,
                        'offset' => $per_page * ( max( 1, $page ) - 1 ),
                );

                if ( ! empty( $request['parent'] ) ) {
                        $args['parent'] = (int) $request['parent'];
                }

                if ( ! empty( $request['post_type'] ) ) {
                        $args['post_type'] = sanitize_key( $request['post_type'] );
                }

                $results = wp_get_scheduled_updates( $args );
                $data    = array();

                foreach ( $results['items'] as $item ) {
                        $response = $this->prepare_item_for_response( $item, $request );
                        $data[]   = $this->prepare_response_for_collection( $response );
                }

                $response = rest_ensure_response( $data );
                $response->header( 'X-WP-Total', (int) $results['total'] );
                $response->header( 'X-WP-TotalPages', (int) ceil( $results['total'] / $per_page ) );

                return $response;
        }

        /**
         * Checks permissions for retrieving a single scheduled update.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return true|WP_Error True if the request has access, WP_Error otherwise.
         */
        public function get_item_permissions_check( $request ) {
                $revision = $this->get_scheduled_update( (int) $request['id'] );

                if ( is_wp_error( $revision ) ) {
                        return $revision;
                }

                if ( ! current_user_can( 'edit_post', $revision->post_parent ) ) {
                        return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to view this scheduled update.' ), array( 'status' => rest_authorization_required_code() ) );
                }

                return true;
        }

        /**
         * Retrieves a scheduled update.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_REST_Response|WP_Error Response object on success, WP_Error otherwise.
         */
        public function get_item( $request ) {
                $revision = $this->get_scheduled_update( (int) $request['id'] );

                if ( is_wp_error( $revision ) ) {
                        return $revision;
                }

                $response = $this->prepare_item_for_response( $revision, $request );

                return rest_ensure_response( $response );
        }

        /**
         * Checks permissions for updating a scheduled update.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return true|WP_Error True if the request has access, WP_Error otherwise.
         */
        public function update_item_permissions_check( $request ) {
                $revision = $this->get_scheduled_update( (int) $request['id'] );

                if ( is_wp_error( $revision ) ) {
                        return $revision;
                }

                $parent_type = get_post_type_object( get_post_type( $revision->post_parent ) );

                if ( ! $parent_type ) {
                        return new WP_Error( 'rest_forbidden', __( 'Scheduled updates are not available for this post type.' ), array( 'status' => rest_authorization_required_code() ) );
                }

                if ( ! current_user_can( $parent_type->cap->publish_posts ) ) {
                        return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to modify this scheduled update.' ), array( 'status' => rest_authorization_required_code() ) );
                }

                return true;
        }

        /**
         * Updates a scheduled update revision.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_REST_Response|WP_Error Response object on success, WP_Error otherwise.
         */
        public function update_item( $request ) {
                $revision = $this->get_scheduled_update( (int) $request['id'] );

                if ( is_wp_error( $revision ) ) {
                        return $revision;
                }

                $prepared = array();

                if ( isset( $request['scheduled_for'] ) ) {
                        $timestamp = rest_parse_date( $request['scheduled_for'], true );

                        if ( false === $timestamp ) {
                                return new WP_Error( 'rest_invalid_date', __( 'Invalid schedule date.' ), array( 'status' => 400 ) );
                        }

                        $prepared['timestamp'] = $timestamp;
                }

                if ( isset( $request['title'] ) ) {
                        $prepared['post_title'] = $request['title'];
                }

                if ( isset( $request['content'] ) ) {
                        $prepared['post_content'] = $request['content'];
                }

                if ( isset( $request['excerpt'] ) ) {
                        $prepared['post_excerpt'] = $request['excerpt'];
                }

                if ( empty( $prepared ) ) {
                        return $this->prepare_item_for_response( $revision, $request );
                }

        $result = wp_update_scheduled_post_update( $revision->ID, $prepared );

                if ( is_wp_error( $result ) ) {
                        return $result;
                }

                $response = $this->prepare_item_for_response( $result, $request );

                return rest_ensure_response( $response );
        }

        /**
         * Checks permissions for deleting a scheduled update.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return true|WP_Error True if the request has access, WP_Error otherwise.
         */
        public function delete_item_permissions_check( $request ) {
                $revision = $this->get_scheduled_update( (int) $request['id'] );

                if ( is_wp_error( $revision ) ) {
                        return $revision;
                }

                if ( ! current_user_can( 'edit_post', $revision->post_parent ) ) {
                        return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to cancel this scheduled update.' ), array( 'status' => rest_authorization_required_code() ) );
                }

                return true;
        }

        /**
         * Cancels a scheduled update.
         *
         * @since 6.6.0
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_REST_Response|WP_Error Response on success, WP_Error otherwise.
         */
        public function delete_item( $request ) {
                $revision = $this->get_scheduled_update( (int) $request['id'] );

                if ( is_wp_error( $revision ) ) {
                        return $revision;
                }

        $previous      = $this->prepare_item_for_response( $revision, $request );
        $previous_data = $previous->get_data();

                $result = wp_cancel_scheduled_update( $revision->ID );

                if ( is_wp_error( $result ) ) {
                        return $result;
                }

                return rest_ensure_response(
                        array(
                                'deleted'  => true,
                                'previous' => $previous_data,
                        )
                );
        }

        /**
         * Prepares a scheduled update for response.
         *
         * @since 6.6.0
         *
         * @param WP_Post         $item    Scheduled update revision.
         * @param WP_REST_Request $request The REST request.
         * @return WP_REST_Response
         */
        public function prepare_item_for_response( $item, $request ) {
                $parent = get_post( $item->post_parent );

                $timestamp = (int) get_post_meta( $item->ID, '_scheduled_update_timestamp', true );
                if ( ! $timestamp ) {
                        $timestamp = strtotime( $item->post_date_gmt . ' GMT' );
                }

                $data = array(
                        'id'            => (int) $item->ID,
                        'parent'        => (int) $item->post_parent,
                        'parent_type'   => $parent ? $parent->post_type : null,
                        'scheduled_for' => $this->prepare_date_response( $timestamp ),
                        'status'        => $item->post_status,
                        'author'        => (int) $item->post_author,
                        'link'          => admin_url( 'revision.php?revision=' . $item->ID ),
                        'diff'          => admin_url( 'revision.php?revision=' . $item->ID ),
                );

                if ( 'edit' === $request['context'] ) {
                        $data['title']   = $item->post_title;
                        $data['content'] = $item->post_content;
                        $data['excerpt'] = $item->post_excerpt;
                }

                $response = rest_ensure_response( $data );
                $response->add_links( $this->prepare_links( $item, $parent ) );

                return $response;
        }

        /**
         * Prepares links for the scheduled update response.
         *
         * @since 6.6.0
         *
         * @param WP_Post      $item   Scheduled update revision.
         * @param WP_Post|null $parent Parent post object.
         * @return array Links for the response.
         */
        protected function prepare_links( $item, $parent ) {
        $links = array(
                'self'       => array(
                        'href' => rest_url( trailingslashit( $this->namespace ) . $this->rest_base . '/' . $item->ID ),
                ),
                'collection' => array(
                        'href' => rest_url( trailingslashit( $this->namespace ) . $this->rest_base ),
                ),
        );

        if ( $item->post_author ) {
                $links['author'] = array(
                        'href' => rest_url( 'wp/v2/users/' . $item->post_author ),
                );
        }

        if ( $parent && $parent->post_type ) {
                        $parent_type = get_post_type_object( $parent->post_type );

                        if ( $parent_type && $parent_type->show_in_rest ) {
                                $base = ! empty( $parent_type->rest_base ) ? $parent_type->rest_base : $parent_type->name;
                                $links['parent'] = array(
                                        'href' => rest_url( sprintf( 'wp/v2/%s/%d', $base, $parent->ID ) ),
                                );
                        }
                }

                return $links;
        }

        /**
         * Helper for formatting the date response.
         *
         * @since 6.6.0
         *
         * @param int $timestamp Timestamp in UTC.
         * @return string|null RFC3339 formatted date or null.
         */
        protected function prepare_date_response( $timestamp ) {
                if ( ! $timestamp ) {
                        return null;
                }

                return mysql_to_rfc3339( gmdate( 'Y-m-d H:i:s', $timestamp ) );
        }

        /**
         * Retrieves a scheduled update revision.
         *
         * @since 6.6.0
         *
         * @param int $revision_id Revision ID.
         * @return WP_Post|WP_Error Scheduled update on success, WP_Error on failure.
         */
        protected function get_scheduled_update( $revision_id ) {
                $revision = get_post( $revision_id );

                if ( ! $revision || 'revision' !== $revision->post_type || 'scheduled-update' !== $revision->post_status ) {
                        return new WP_Error( 'rest_scheduled_update_not_found', __( 'Scheduled update not found.' ), array( 'status' => 404 ) );
                }

                return $revision;
        }

        /**
         * Retrieves the collection parameters for scheduled updates.
         *
         * @since 6.6.0
         *
         * @return array Collection parameters.
         */
        public function get_collection_params() {
                $params = parent::get_collection_params();

                $params['context']['default'] = 'view';

                $params['page'] = array(
                        'description'       => __( 'Current page of the collection.' ),
                        'type'              => 'integer',
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                        'minimum'           => 1,
                );

                $params['per_page'] = array(
                        'description'       => __( 'Maximum number of items to be returned in result set.' ),
                        'type'              => 'integer',
                        'default'           => 10,
                        'sanitize_callback' => 'absint',
                        'minimum'           => 1,
                        'maximum'           => 100,
                );

                $params['parent'] = array(
                        'description'       => __( 'Limit results to a specific parent post.' ),
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                );

                $params['post_type'] = array(
                        'description'       => __( 'Limit results to a specific post type.' ),
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_key',
                );

                return $params;
        }

        /**
         * Retrieves the schema for scheduled updates.
         *
         * @since 6.6.0
         *
         * @return array Item schema data.
         */
        public function get_item_schema() {
                if ( $this->schema ) {
                        return $this->add_additional_fields_schema( $this->schema );
                }

                $schema = array(
                        '$schema'    => 'http://json-schema.org/draft-04/schema#',
                        'title'      => 'scheduled-update',
                        'type'       => 'object',
                        'properties' => array(
                                'id'            => array(
                                        'description' => __( 'Unique identifier for the scheduled update.' ),
                                        'type'        => 'integer',
                                        'context'     => array( 'view', 'edit', 'embed' ),
                                        'readonly'    => true,
                                ),
                                'parent'        => array(
                                        'description' => __( 'ID of the parent post.' ),
                                        'type'        => 'integer',
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                ),
                                'parent_type'   => array(
                                        'description' => __( 'The post type of the parent.' ),
                                        'type'        => array( 'string', 'null' ),
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                ),
                                'scheduled_for' => array(
                                        'description' => __( 'The date the scheduled update will publish.' ),
                                        'type'        => array( 'string', 'null' ),
                                        'format'      => 'date-time',
                                        'context'     => array( 'view', 'edit' ),
                                ),
                                'status'        => array(
                                        'description' => __( 'The status of the scheduled update.' ),
                                        'type'        => 'string',
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                ),
                                'author'        => array(
                                        'description' => __( 'The user ID that created the scheduled update.' ),
                                        'type'        => 'integer',
                                        'context'     => array( 'view', 'edit' ),
                                ),
                                'link'          => array(
                                        'description' => __( 'Link to the scheduled update in the editor.' ),
                                        'type'        => 'string',
                                        'format'      => 'uri',
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                ),
                                'diff'          => array(
                                        'description' => __( 'Link to preview differences with the live content.' ),
                                        'type'        => 'string',
                                        'format'      => 'uri',
                                        'context'     => array( 'view', 'edit' ),
                                        'readonly'    => true,
                                ),
                                'title'         => array(
                                        'description' => __( 'Title for the scheduled update.' ),
                                        'type'        => 'string',
                                        'context'     => array( 'edit' ),
                                ),
                                'content'       => array(
                                        'description' => __( 'Content for the scheduled update.' ),
                                        'type'        => 'string',
                                        'context'     => array( 'edit' ),
                                ),
                                'excerpt'       => array(
                                        'description' => __( 'Excerpt for the scheduled update.' ),
                                        'type'        => 'string',
                                        'context'     => array( 'edit' ),
                                ),
                        ),
                );

                $this->schema = $schema;

                return $this->add_additional_fields_schema( $this->schema );
        }
}
