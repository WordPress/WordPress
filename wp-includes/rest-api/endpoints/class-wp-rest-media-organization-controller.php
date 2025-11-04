<?php
/**
 * REST API: WP_REST_Media_Organization_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 */

class WP_REST_Media_Organization_Controller extends WP_REST_Controller {

        public function __construct() {
                $this->namespace = 'wp/v2';
                $this->rest_base = 'media-organization';
        }

        public function register_routes() {
                register_rest_route(
                        $this->namespace,
                        '/' . $this->rest_base . '/folders',
                        array(
                                array(
                                        'methods'             => WP_REST_Server::READABLE,
                                        'callback'            => array( $this, 'get_folders' ),
                                        'permission_callback' => array( $this, 'permissions_check' ),
                                        'args'                => array(
                                                'hide_empty' => array(
                                                        'description' => __( 'Whether to hide folders that do not contain attachments.' ),
                                                        'type'        => 'boolean',
                                                        'default'     => false,
                                                ),
                                        ),
                                ),
                        )
                );

                register_rest_route(
                        $this->namespace,
                        '/' . $this->rest_base . '/tags',
                        array(
                                array(
                                        'methods'             => WP_REST_Server::READABLE,
                                        'callback'            => array( $this, 'get_tags' ),
                                        'permission_callback' => array( $this, 'permissions_check' ),
                                        'args'                => array(
                                                'hide_empty' => array(
                                                        'description' => __( 'Whether to hide tags that do not contain attachments.' ),
                                                        'type'        => 'boolean',
                                                        'default'     => false,
                                                ),
                                        ),
                                ),
                        )
                );

                register_rest_route(
                        $this->namespace,
                        '/' . $this->rest_base . '/bulk',
                        array(
                                array(
                                        'methods'             => WP_REST_Server::CREATABLE,
                                        'callback'            => array( $this, 'bulk_update' ),
                                        'permission_callback' => array( $this, 'permissions_check' ),
                                        'args'                => array(
                                                'attachments' => array(
                                                        'description' => __( 'List of attachment IDs to update.' ),
                                                        'type'        => 'array',
                                                        'items'       => array(
                                                                'type' => 'integer',
                                                        ),
                                                        'required'    => true,
                                                ),
                                                'folder'      => array(
                                                        'description' => __( 'Media folder to assign. Use 0 to clear.' ),
                                                        'type'        => 'integer',
                                                        'default'     => 0,
                                                ),
                                                'tags'        => array(
                                                        'description' => __( 'Media tags to assign to attachments.' ),
                                                        'type'        => 'array',
                                                        'items'       => array(
                                                                'type' => 'integer',
                                                        ),
                                                        'default'     => array(),
                                                ),
                                                'tag_action'  => array(
                                                        'description' => __( 'How to apply the provided tag list.' ),
                                                        'type'        => 'string',
                                                        'enum'        => array( 'replace', 'append', 'remove' ),
                                                        'default'     => 'replace',
                                                ),
                                        ),
                                ),
                        )
                );

                register_rest_route(
                        $this->namespace,
                        '/' . $this->rest_base . '/export',
                        array(
                                array(
                                        'methods'             => WP_REST_Server::READABLE,
                                        'callback'            => array( $this, 'export' ),
                                        'permission_callback' => array( $this, 'permissions_check' ),
                                ),
                        )
                );

                register_rest_route(
                        $this->namespace,
                        '/' . $this->rest_base . '/import',
                        array(
                                array(
                                        'methods'             => WP_REST_Server::CREATABLE,
                                        'callback'            => array( $this, 'import' ),
                                        'permission_callback' => array( $this, 'permissions_check' ),
                                ),
                        )
                );
        }

        public function permissions_check( $request ) {
                if ( ! current_user_can( 'upload_files' ) ) {
                        return new WP_Error(
                                'rest_forbidden',
                                __( 'Sorry, you are not allowed to manage media organization on this site.' ),
                                array( 'status' => rest_authorization_required_code() )
                        );
                }

                return true;
        }

        public function get_folders( $request ) {
                $folders = wp_get_media_folder_tree(
                        array(
                                'hide_empty' => rest_sanitize_boolean( $request['hide_empty'] ),
                        )
                );

                return rest_ensure_response( $folders );
        }

        public function get_tags( $request ) {
                if ( ! taxonomy_exists( 'media_tag' ) ) {
                        return rest_ensure_response( array() );
                }

                $terms = get_terms(
                        array(
                                'taxonomy'   => 'media_tag',
                                'hide_empty' => rest_sanitize_boolean( $request['hide_empty'] ),
                                'orderby'    => 'name',
                                'order'      => 'ASC',
                        )
                );

                if ( is_wp_error( $terms ) ) {
                        return $terms;
                }

                $prepared = array();

                foreach ( $terms as $term ) {
                        $prepared[] = array(
                                'id'          => (int) $term->term_id,
                                'name'        => $term->name,
                                'slug'        => $term->slug,
                                'description' => $term->description,
                                'count'       => (int) $term->count,
                        );
                }

                return rest_ensure_response( $prepared );
        }

        public function bulk_update( $request ) {
                $attachment_ids = array_filter( array_map( 'absint', (array) $request['attachments'] ) );

                if ( empty( $attachment_ids ) ) {
                        return new WP_Error( 'rest_invalid_param', __( 'At least one attachment must be specified.' ), array( 'status' => 400 ) );
                }

                $folder_param = $request->has_param( 'folder' ) ? $request['folder'] : null;
                $folder_id    = null;

                if ( null !== $folder_param ) {
                        $folder_id = absint( $folder_param );

                        if ( $folder_id > 0 && ! get_term( $folder_id, 'media_folder' ) ) {
                                return new WP_Error( 'rest_media_folder_invalid', __( 'Invalid media folder supplied.' ), array( 'status' => 400 ) );
                        }
                }

                $tags       = array_filter( array_map( 'absint', (array) $request['tags'] ) );
                $tag_action = $request['tag_action'];

                $updated = array();
                $failed  = array();

                foreach ( $attachment_ids as $attachment_id ) {
                        if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
                                $failed[] = array(
                                        'id'      => $attachment_id,
                                        'message' => __( 'Sorry, you are not allowed to edit this attachment.' ),
                                );
                                continue;
                        }

                        $result = true;

                        if ( null !== $folder_param ) {
                                if ( $folder_id > 0 ) {
                                        $result = wp_set_object_terms( $attachment_id, array( $folder_id ), 'media_folder' );
                                } else {
                                        $result = wp_set_object_terms( $attachment_id, array(), 'media_folder' );
                                }

                                if ( is_wp_error( $result ) ) {
                                        $failed[] = array(
                                                'id'      => $attachment_id,
                                                'message' => $result->get_error_message(),
                                        );
                                        continue;
                                }
                        }

                        if ( $request->has_param( 'tags' ) ) {
                                $existing = wp_get_object_terms( $attachment_id, 'media_tag', array( 'fields' => 'ids' ) );

                                if ( is_wp_error( $existing ) ) {
                                        $existing = array();
                                }

                                if ( 'append' === $tag_action ) {
                                        $result = wp_set_object_terms( $attachment_id, array_unique( array_merge( $existing, $tags ) ), 'media_tag' );
                                } elseif ( 'remove' === $tag_action ) {
                                        $result = wp_set_object_terms( $attachment_id, array_diff( $existing, $tags ), 'media_tag' );
                                } else {
                                        $result = wp_set_object_terms( $attachment_id, $tags, 'media_tag' );
                                }

                                if ( is_wp_error( $result ) ) {
                                        $failed[] = array(
                                                'id'      => $attachment_id,
                                                'message' => $result->get_error_message(),
                                        );
                                        continue;
                                }
                        }

                        $updated[] = array( 'id' => $attachment_id );
                }

                return rest_ensure_response(
                        array(
                                'updated' => $updated,
                                'failed'  => $failed,
                        )
                );
        }

        public function export( $request ) {
                return rest_ensure_response( wp_get_media_organization_export_data() );
        }

        public function import( $request ) {
                $data = $request->get_json_params();

                if ( empty( $data ) && $request->has_param( 'data' ) ) {
                        $data = $request['data'];
                }

                if ( empty( $data ) ) {
                        return new WP_Error( 'rest_invalid_param', __( 'No media organization data supplied.' ), array( 'status' => 400 ) );
                }

                $result = wp_import_media_organization( $data );

                if ( is_wp_error( $result ) ) {
                        return $result;
                }

                return rest_ensure_response( array( 'success' => true ) );
        }
}
