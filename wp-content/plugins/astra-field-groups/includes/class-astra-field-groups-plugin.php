<?php
/**
 * Core plugin loader for Astra Field Groups.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Astra_Field_Groups_Plugin {
    const POST_TYPE       = 'wp_field_group';
    const META_SCHEMA_KEY = '_astra_field_group_schema';
    const NONCE_ACTION    = 'astra_field_group_save';

    /**
     * Singleton instance.
     *
     * @var Astra_Field_Groups_Plugin
     */
    protected static $instance = null;

    /**
     * Registered field type callbacks.
     *
     * @var array<string, callable>
     */
    protected $sanitizers = [];

    /**
     * Main plugin file reference.
     *
     * @var string
     */
    protected $plugin_file;

    /**
     * Retrieve singleton instance.
     *
     * @return Astra_Field_Groups_Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set up hooks.
     */
    private function __construct() {
        $this->plugin_file = dirname( __DIR__ ) . '/astra-field-groups.php';

        $this->register_default_sanitizers();

        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'add_meta_boxes', [ $this, 'register_group_metabox' ] );
        add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_group' ], 10, 2 );

        add_action( 'add_meta_boxes', [ $this, 'register_content_metaboxes' ], 20, 2 );
        add_action( 'save_post', [ $this, 'save_content_fields' ], 10, 3 );

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );

        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

        add_action( 'admin_menu', [ $this, 'register_tools_page' ] );

        add_filter( 'site_status_tests', [ $this, 'register_site_health_tests' ] );
    }

    /**
     * Register default sanitization callbacks for supported field types.
     */
    protected function register_default_sanitizers() {
        $this->sanitizers = [
            'text'     => 'sanitize_text_field',
            'textarea' => [ $this, 'sanitize_textarea' ],
            'number'   => function( $value ) {
                if ( '' === $value ) {
                    return '';
                }
                return is_numeric( $value ) ? 0 + $value : 0;
            },
            'boolean'  => function( $value ) {
                return (bool) $value;
            },
            'date'     => function( $value ) {
                $timestamp = strtotime( (string) $value );
                return $timestamp ? gmdate( 'Y-m-d', $timestamp ) : '';
            },
        ];
    }

    /**
     * Sanitize textarea content preserving limited HTML.
     *
     * @param string $value Value to sanitize.
     * @return string
     */
    public function sanitize_textarea( $value ) {
        return wp_kses_post( (string) $value );
    }

    /**
     * Register custom post type for field groups.
     */
    public function register_post_type() {
        $labels = [
            'name'               => __( 'Field Groups', 'astra-field-groups' ),
            'singular_name'      => __( 'Field Group', 'astra-field-groups' ),
            'menu_name'          => __( 'Field Groups', 'astra-field-groups' ),
            'add_new'            => __( 'Add New', 'astra-field-groups' ),
            'add_new_item'       => __( 'Add New Field Group', 'astra-field-groups' ),
            'edit_item'          => __( 'Edit Field Group', 'astra-field-groups' ),
            'new_item'           => __( 'New Field Group', 'astra-field-groups' ),
            'view_item'          => __( 'View Field Group', 'astra-field-groups' ),
            'search_items'       => __( 'Search Field Groups', 'astra-field-groups' ),
            'not_found'          => __( 'No field groups found', 'astra-field-groups' ),
            'not_found_in_trash' => __( 'No field groups found in Trash', 'astra-field-groups' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => false,
            'show_in_admin_bar'  => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'supports'           => [ 'title' ],
            'menu_position'      => 26,
        ];

        register_post_type( self::POST_TYPE, $args );
    }

    /**
     * Register meta box for field group editor.
     */
    public function register_group_metabox() {
        add_meta_box(
            'astra-field-group-config',
            __( 'Field Definitions', 'astra-field-groups' ),
            [ $this, 'render_group_metabox' ],
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Render the field group configuration meta box.
     *
     * @param WP_Post $post Post instance.
     */
    public function render_group_metabox( $post ) {
        wp_nonce_field( self::NONCE_ACTION, 'astra_field_group_nonce' );

        $schema = $this->get_group_schema( $post->ID );
        if ( empty( $schema ) ) {
            $schema = [];
        }

        $available_post_types = $this->get_editable_post_types();

        wp_enqueue_script( 'astra-field-group-editor' );
        wp_enqueue_style( 'astra-field-group-editor' );

        printf( '<div id="astra-field-group-editor" data-schema="%s" data-post-types="%s"></div>',
            esc_attr( wp_json_encode( $schema ) ),
            esc_attr( wp_json_encode( array_values( $available_post_types ) ) )
        );

        printf( '<input type="hidden" id="astra-field-group-schema" name="astra_field_group_schema" value="%s" />', esc_attr( wp_json_encode( $schema ) ) );
    }

    /**
     * Get editable public post types.
     *
     * @return array
     */
    protected function get_editable_post_types() {
        $types = get_post_types( [ 'show_ui' => true ], 'objects' );
        unset( $types[ self::POST_TYPE ] );

        return array_map(
            function( $type ) {
                return [
                    'name'  => $type->name,
                    'label' => $type->label,
                ];
            },
            $types
        );
    }

    /**
     * Save field group schema.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post Post object.
     */
    public function save_group( $post_id, $post ) {
        if ( ! isset( $_POST['astra_field_group_nonce'] ) ) {
            return;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST['astra_field_group_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $raw_schema = isset( $_POST['astra_field_group_schema'] ) ? wp_unslash( $_POST['astra_field_group_schema'] ) : '[]';
        $schema     = json_decode( $raw_schema, true );

        if ( ! is_array( $schema ) ) {
            $schema = [];
        }

        $schema = array_values( array_filter( array_map( [ $this, 'sanitize_field_definition' ], $schema ) ) );

        update_post_meta( $post_id, self::META_SCHEMA_KEY, $schema );
    }

    /**
     * Sanitize individual field definition entry.
     *
     * @param array $definition Field definition.
     * @return array|null
     */
    protected function sanitize_field_definition( $definition ) {
        if ( ! is_array( $definition ) ) {
            return null;
        }

        $key = sanitize_key( $definition['key'] ?? '' );
        if ( empty( $key ) ) {
            return null;
        }

        $label = sanitize_text_field( $definition['label'] ?? '' );
        $type  = sanitize_key( $definition['type'] ?? 'text' );
        if ( ! isset( $this->sanitizers[ $type ] ) ) {
            $type = 'text';
        }

        $required   = ! empty( $definition['required'] );
        $post_types = array_filter( array_map( 'sanitize_key', (array) ( $definition['post_types'] ?? [] ) ) );

        return [
            'key'        => $key,
            'label'      => $label,
            'type'       => $type,
            'required'   => $required,
            'post_types' => $post_types,
        ];
    }

    /**
     * Retrieve field group schema from storage.
     *
     * @param int $group_id Group ID.
     * @return array
     */
    public function get_group_schema( $group_id ) {
        $schema = get_post_meta( $group_id, self::META_SCHEMA_KEY, true );
        return is_array( $schema ) ? $schema : [];
    }

    /**
     * Register field meta boxes on content editing screens.
     *
     * @param string $post_type Post type being edited.
     * @param WP_Post $post Post instance.
     */
    public function register_content_metaboxes( $post_type, $post ) {
        if ( self::POST_TYPE === $post_type ) {
            return;
        }

        $groups = $this->get_field_groups_for_post_type( $post_type );

        foreach ( $groups as $group ) {
            $meta_box_id = 'astra-field-group-' . $group['id'];
            add_meta_box(
                $meta_box_id,
                sprintf( __( 'Field Group: %s', 'astra-field-groups' ), esc_html( $group['title'] ) ),
                function() use ( $group, $post ) {
                    $this->render_content_fields( $group, $post );
                },
                $post_type,
                'normal',
                'default'
            );
        }
    }

    /**
     * Render field controls for a given group on post editor.
     *
     * @param array   $group Group definition.
     * @param WP_Post $post Post object.
     */
    protected function render_content_fields( $group, $post ) {
        wp_nonce_field( self::NONCE_ACTION, 'astra_field_group_nonce_' . $group['id'] );

        $values = get_post_meta( $post->ID, '_astra_field_group_values_' . $group['id'], true );
        if ( ! is_array( $values ) ) {
            $values = [];
        }

        echo '<div class="astra-field-group-fields">';
        foreach ( $group['fields'] as $field ) {
            $this->render_field_control( $field, $values[ $field['key'] ] ?? null, $group['id'], $post );
        }
        echo '</div>';
    }

    /**
     * Render individual control.
     *
     * @param array   $field Field definition.
     * @param mixed   $value Existing value.
     * @param int     $group_id Group identifier.
     * @param WP_Post $post Post object.
     */
    protected function render_field_control( $field, $value, $group_id, $post ) {
        $input_name = sprintf( 'astra_field_group_values[%d][%s]', $group_id, $field['key'] );
        $label      = esc_html( $field['label'] );
        $required   = $field['required'] ? 'required' : '';

        echo '<div class="astra-field-group-control">';
        printf( '<label for="%1$s"><strong>%2$s</strong></label>', esc_attr( $input_name ), $label );

        switch ( $field['type'] ) {
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" %3$s rows="4" class="widefat">%2$s</textarea>',
                    esc_attr( $input_name ),
                    esc_textarea( (string) $value ),
                    $required
                );
                break;
            case 'number':
                printf( '<input type="number" class="widefat" name="%1$s" id="%1$s" value="%2$s" %3$s />',
                    esc_attr( $input_name ),
                    esc_attr( $value ),
                    $required
                );
                break;
            case 'boolean':
                $checked = $value ? 'checked' : '';
                printf( '<input type="checkbox" name="%1$s" id="%1$s" value="1" %2$s %3$s />',
                    esc_attr( $input_name ),
                    $checked,
                    $required
                );
                break;
            case 'date':
                printf( '<input type="date" class="widefat" name="%1$s" id="%1$s" value="%2$s" %3$s />',
                    esc_attr( $input_name ),
                    esc_attr( $value ),
                    $required
                );
                break;
            case 'text':
            default:
                printf( '<input type="text" class="widefat" name="%1$s" id="%1$s" value="%2$s" %3$s />',
                    esc_attr( $input_name ),
                    esc_attr( $value ),
                    $required
                );
                break;
        }

        if ( $field['required'] ) {
            printf( '<p class="description">%s</p>', esc_html__( 'Required field.', 'astra-field-groups' ) );
        }
        echo '</div>';
    }

    /**
     * Save field values for content post types.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post Post object.
     * @param bool    $update Whether updating existing post.
     */
    public function save_content_fields( $post_id, $post, $update ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( self::POST_TYPE === $post->post_type ) {
            return;
        }

        if ( ! isset( $_POST['astra_field_group_values'] ) || ! is_array( $_POST['astra_field_group_values'] ) ) {
            return;
        }

        $submitted_groups = wp_unslash( $_POST['astra_field_group_values'] );

        foreach ( $submitted_groups as $group_id => $group_values ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                continue;
            }

            $nonce = $_POST[ 'astra_field_group_nonce_' . $group_id ] ?? '';
            $nonce = sanitize_text_field( wp_unslash( $nonce ) );
            if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
                continue;
            }

            $schema = $this->get_group_schema( (int) $group_id );
            if ( empty( $schema ) ) {
                continue;
            }

            $schema = array_values( array_filter( $schema, function( $field ) use ( $post ) {
                return empty( $field['post_types'] ) || in_array( $post->post_type, (array) $field['post_types'], true );
            } ) );

            if ( empty( $schema ) ) {
                continue;
            }

            $group_values = wp_unslash( $group_values );
            $group_values = $this->sanitize_values_against_schema( $group_values, $schema );

            update_post_meta( $post_id, '_astra_field_group_values_' . $group_id, $group_values );
        }
    }

    /**
     * Sanitize values per schema.
     *
     * @param array $values Submitted values.
     * @param array $schema Schema definition.
     * @return array
     */
    protected function sanitize_values_against_schema( $values, $schema ) {
        $sanitized = [];
        foreach ( $schema as $field ) {
            $key = $field['key'];
            $raw = $values[ $key ] ?? ( $field['type'] === 'boolean' ? false : '' );

            $sanitized[ $key ] = $this->sanitize_value( $field, $raw );
        }

        return $sanitized;
    }

    /**
     * Sanitize individual value per field.
     *
     * @param array $field Field definition.
     * @param mixed $value Raw value.
     * @return mixed
     */
    protected function sanitize_value( $field, $value ) {
        $type = $field['type'];
        if ( isset( $this->sanitizers[ $type ] ) ) {
            $callback = $this->sanitizers[ $type ];
            $value    = call_user_func( $callback, $value );
        } else {
            $value = sanitize_text_field( (string) $value );
        }

        if ( $field['required'] ) {
            if ( ( 'boolean' === $type && ! $value ) || ( 'boolean' !== $type && '' === $value ) ) {
                add_filter( 'redirect_post_location', function( $location ) use ( $field ) {
                    return add_query_arg( 'astra_field_group_error', rawurlencode( $field['label'] ), $location );
                } );
            }
        }

        return $value;
    }

    /**
     * Enqueue admin assets for field group editor.
     *
     * @param string $hook Current admin hook.
     */
    public function enqueue_admin_assets( $hook ) {
        $screen = get_current_screen();
        $is_group_screen = $screen && self::POST_TYPE === $screen->post_type;

        if ( $is_group_screen ) {
            wp_register_script(
                'astra-field-group-editor',
                plugins_url( 'js/field-group-editor.js', $this->plugin_file ),
                [ 'wp-element', 'wp-components', 'wp-i18n' ],
                '0.1.0',
                true
            );

            wp_register_style(
                'astra-field-group-editor',
                plugins_url( 'css/field-group-editor.css', $this->plugin_file ),
                [ 'wp-components' ],
                '0.1.0'
            );
        }
    }

    /**
     * Enqueue block editor assets to expose field groups.
     */
    public function enqueue_block_editor_assets() {
        $screen = get_current_screen();
        if ( ! $screen || self::POST_TYPE === $screen->post_type ) {
            return;
        }

        $post_id   = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
        $post_type = $screen->post_type;
        $groups    = $this->get_field_groups_for_post_type( $post_type );
        $values    = [];

        foreach ( $groups as $group ) {
            $values[ $group['id'] ] = get_post_meta( $post_id, '_astra_field_group_values_' . $group['id'], true );
        }

        wp_register_script(
            'astra-field-group-block-editor',
            plugins_url( 'js/block-editor-panel.js', $this->plugin_file ),
            [ 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-element', 'wp-data', 'wp-api-fetch', 'wp-i18n' ],
            '0.1.0',
            true
        );

        wp_localize_script(
            'astra-field-group-block-editor',
            'AstraFieldGroupsEditorData',
            [
                'groups'     => $groups,
                'values'     => $values,
                'restNonce'  => wp_create_nonce( 'wp_rest' ),
                'postId'     => $post_id,
                'restNamespace' => '/astra/v1',
                'canEdit'    => current_user_can( 'edit_post', $post_id ),
            ]
        );

        wp_enqueue_script( 'astra-field-group-block-editor' );
    }

    /**
     * Get field groups applicable to post type.
     *
     * @param string $post_type Post type.
     * @return array
     */
    protected function get_field_groups_for_post_type( $post_type ) {
        $query = new WP_Query(
            [
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            ]
        );

        $groups = [];
        foreach ( $query->posts as $post ) {
            $schema = $this->get_group_schema( $post->ID );
            $fields = array_filter( $schema, function( $field ) use ( $post_type ) {
                return empty( $field['post_types'] ) || in_array( $post_type, $field['post_types'], true );
            } );

            if ( empty( $fields ) ) {
                continue;
            }

            $groups[] = [
                'id'     => $post->ID,
                'title'  => get_the_title( $post ),
                'fields' => array_values( $fields ),
            ];
        }

        wp_reset_postdata();

        return $groups;
    }

    /**
     * Register REST routes.
     */
    public function register_rest_routes() {
        register_rest_route(
            'astra/v1',
            '/field-groups',
            [
                'methods'  => WP_REST_Server::READABLE,
                'callback' => [ $this, 'rest_list_field_groups' ],
                'permission_callback' => function() {
                    return current_user_can( 'edit_posts' );
                },
            ]
        );

        register_rest_route(
            'astra/v1',
            '/field-groups/(?P<id>\d+)/values/(?P<post_id>\d+)',
            [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'rest_get_field_group_values' ],
                    'permission_callback' => function( $request ) {
                        $post_id = (int) $request['post_id'];
                        return current_user_can( 'edit_post', $post_id );
                    },
                ],
                [
                    'methods'  => WP_REST_Server::EDITABLE,
                    'callback' => [ $this, 'rest_update_field_group_values' ],
                    'permission_callback' => function( $request ) {
                        $post_id = (int) $request['post_id'];
                        return current_user_can( 'edit_post', $post_id );
                    },
                ],
            ]
        );
    }

    /**
     * REST callback: list field groups.
     *
     * @return WP_REST_Response
     */
    public function rest_list_field_groups( WP_REST_Request $request ) {
        $post_type = sanitize_key( $request->get_param( 'post_type' ) );

        if ( $post_type ) {
            return rest_ensure_response( $this->get_field_groups_for_post_type( $post_type ) );
        }

        return rest_ensure_response( $this->get_field_groups() );
    }

    /**
     * REST callback: get values for a group/post.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function rest_get_field_group_values( WP_REST_Request $request ) {
        $group_id = (int) $request['id'];
        $post_id  = (int) $request['post_id'];

        $schema = $this->get_group_schema( $group_id );
        if ( empty( $schema ) ) {
            return new WP_Error( 'astra_field_group_not_found', __( 'Field group not found.', 'astra-field-groups' ), [ 'status' => 404 ] );
        }

        $post_type = get_post_type( $post_id );
        if ( ! $post_type ) {
            return new WP_Error( 'astra_field_group_invalid_post', __( 'Post not found.', 'astra-field-groups' ), [ 'status' => 404 ] );
        }
        $schema    = array_values( array_filter( $schema, function( $field ) use ( $post_type ) {
            return empty( $field['post_types'] ) || in_array( $post_type, (array) $field['post_types'], true );
        } ) );

        if ( empty( $schema ) ) {
            return new WP_Error( 'astra_field_group_not_applicable', __( 'Field group does not apply to this post type.', 'astra-field-groups' ), [ 'status' => 400 ] );
        }

        $values = get_post_meta( $post_id, '_astra_field_group_values_' . $group_id, true );
        if ( ! is_array( $values ) ) {
            $values = [];
        }

        $allowed_keys = wp_list_pluck( $schema, 'key' );
        $values       = array_intersect_key( $values, array_flip( $allowed_keys ) );

        return rest_ensure_response( $values );
    }

    /**
     * REST callback: update values.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function rest_update_field_group_values( WP_REST_Request $request ) {
        $group_id = (int) $request['id'];
        $post_id  = (int) $request['post_id'];

        $schema = $this->get_group_schema( $group_id );
        if ( empty( $schema ) ) {
            return new WP_Error( 'astra_field_group_not_found', __( 'Field group not found.', 'astra-field-groups' ), [ 'status' => 404 ] );
        }

        $post_type = get_post_type( $post_id );
        if ( ! $post_type ) {
            return new WP_Error( 'astra_field_group_invalid_post', __( 'Post not found.', 'astra-field-groups' ), [ 'status' => 404 ] );
        }
        $schema    = array_values( array_filter( $schema, function( $field ) use ( $post_type ) {
            return empty( $field['post_types'] ) || in_array( $post_type, (array) $field['post_types'], true );
        } ) );

        if ( empty( $schema ) ) {
            return new WP_Error( 'astra_field_group_not_applicable', __( 'Field group does not apply to this post type.', 'astra-field-groups' ), [ 'status' => 400 ] );
        }

        $values = $request->get_json_params();
        if ( ! is_array( $values ) ) {
            $values = [];
        }

        $values = $this->sanitize_values_against_schema( $values, $schema );

        update_post_meta( $post_id, '_astra_field_group_values_' . $group_id, $values );

        return rest_ensure_response( $values );
    }

    /**
     * Register tools page for export/import.
     */
    public function register_tools_page() {
        add_management_page(
            __( 'Field Groups Export/Import', 'astra-field-groups' ),
            __( 'Field Groups', 'astra-field-groups' ),
            'manage_options',
            'astra-field-groups-tools',
            [ $this, 'render_tools_page' ]
        );
    }

    /**
     * Render export/import tools page.
     */
    public function render_tools_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['astra_field_groups_export'] ) && check_admin_referer( 'astra_field_groups_tools', 'astra_field_groups_tools_nonce' ) ) {
            $this->handle_export();
        }

        if ( isset( $_POST['astra_field_groups_import'] ) && check_admin_referer( 'astra_field_groups_tools', 'astra_field_groups_tools_nonce' ) ) {
            $this->handle_import();
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Field Groups Export/Import', 'astra-field-groups' ) . '</h1>';
        echo '<form method="post" enctype="multipart/form-data">';
        wp_nonce_field( 'astra_field_groups_tools', 'astra_field_groups_tools_nonce' );
        echo '<p>' . esc_html__( 'Export all field groups as JSON.', 'astra-field-groups' ) . '</p>';
        submit_button( __( 'Export Field Groups', 'astra-field-groups' ), 'primary', 'astra_field_groups_export', false );
        echo '</form>';

        echo '<hr />';
        echo '<form method="post" enctype="multipart/form-data">';
        wp_nonce_field( 'astra_field_groups_tools', 'astra_field_groups_tools_nonce' );
        echo '<p>' . esc_html__( 'Import field groups from a JSON file.', 'astra-field-groups' ) . '</p>';
        echo '<input type="file" name="astra_field_groups_import_file" accept="application/json" />';
        submit_button( __( 'Import Field Groups', 'astra-field-groups' ), 'primary', 'astra_field_groups_import', false );
        echo '</form>';
        settings_errors( 'astra_field_groups' );
        echo '</div>';
    }

    /**
     * Handle export submission.
     */
    protected function handle_export() {
        $query = new WP_Query(
            [
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'any',
                'posts_per_page' => -1,
            ]
        );

        $groups = [];
        foreach ( $query->posts as $post ) {
            $groups[] = [
                'post'   => [
                    'post_title'   => $post->post_title,
                    'post_status'  => $post->post_status,
                    'post_name'    => $post->post_name,
                    'post_excerpt' => $post->post_excerpt,
                ],
                'schema' => $this->get_group_schema( $post->ID ),
            ];
        }
        wp_reset_postdata();

        nocache_headers();
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="field-groups-' . gmdate( 'Y-m-d' ) . '.json"' );

        echo wp_json_encode( $groups );
        exit;
    }

    /**
     * Handle import submission.
     */
    protected function handle_import() {
        if ( empty( $_FILES['astra_field_groups_import_file']['tmp_name'] ) ) {
            add_settings_error( 'astra_field_groups', 'astra_field_groups_no_file', __( 'No file selected.', 'astra-field-groups' ) );
            settings_errors( 'astra_field_groups' );
            return;
        }

        $contents = file_get_contents( $_FILES['astra_field_groups_import_file']['tmp_name'] );
        if ( false === $contents ) {
            add_settings_error( 'astra_field_groups', 'astra_field_groups_read_error', __( 'Unable to read file.', 'astra-field-groups' ) );
            settings_errors( 'astra_field_groups' );
            return;
        }

        $data = json_decode( $contents, true );
        if ( ! is_array( $data ) ) {
            add_settings_error( 'astra_field_groups', 'astra_field_groups_invalid', __( 'Invalid file format.', 'astra-field-groups' ) );
            settings_errors( 'astra_field_groups' );
            return;
        }

        foreach ( $data as $group_data ) {
            $post_data = $group_data['post'] ?? [];
            $schema    = $group_data['schema'] ?? [];

            $postarr = [
                'post_type'   => self::POST_TYPE,
                'post_status' => $post_data['post_status'] ?? 'draft',
                'post_title'  => $post_data['post_title'] ?? __( 'Imported Field Group', 'astra-field-groups' ),
                'post_name'   => $post_data['post_name'] ?? '',
                'post_excerpt'=> $post_data['post_excerpt'] ?? '',
            ];

            $group_id = wp_insert_post( $postarr );
            if ( ! is_wp_error( $group_id ) ) {
                update_post_meta( $group_id, self::META_SCHEMA_KEY, array_map( [ $this, 'sanitize_field_definition' ], (array) $schema ) );
            }
        }

        add_settings_error( 'astra_field_groups', 'astra_field_groups_imported', __( 'Field groups imported.', 'astra-field-groups' ), 'updated' );
        settings_errors( 'astra_field_groups' );
    }

    /**
     * Register Site Health checks.
     *
     * @param array $tests Existing tests.
     * @return array
     */
    public function register_site_health_tests( $tests ) {
        $tests['direct']['astra_field_groups'] = [
            'label' => __( 'Field group configuration', 'astra-field-groups' ),
            'test'  => [ $this, 'run_site_health_test' ],
        ];

        return $tests;
    }

    /**
     * Execute Site Health test.
     *
     * @return array
     */
    public function run_site_health_test() {
        $issues     = [];
        $seen_keys  = [];
        $query      = new WP_Query(
            [
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'any',
                'posts_per_page' => -1,
            ]
        );

        foreach ( $query->posts as $post ) {
            $schema = $this->get_group_schema( $post->ID );
            foreach ( $schema as $field ) {
                $key = $field['key'];
                if ( isset( $seen_keys[ $key ] ) ) {
                    $issues[] = sprintf( __( 'Field key %1$s duplicated in group %2$s and %3$s.', 'astra-field-groups' ), $key, $seen_keys[ $key ], $post->post_title );
                } else {
                    $seen_keys[ $key ] = $post->post_title;
                }

                if ( empty( $field['label'] ) ) {
                    $issues[] = sprintf( __( 'Field %1$s in group %2$s is missing a label.', 'astra-field-groups' ), $key, $post->post_title );
                }

                if ( ! isset( $this->sanitizers[ $field['type'] ] ) ) {
                    $issues[] = sprintf( __( 'Field %1$s in group %2$s has invalid type %3$s.', 'astra-field-groups' ), $key, $post->post_title, $field['type'] );
                }
            }
        }

        wp_reset_postdata();

        if ( empty( $issues ) ) {
            return [
                'label'       => __( 'Field group configuration is valid.', 'astra-field-groups' ),
                'status'      => 'good',
                'badge'       => [ 'label' => __( 'Field Groups', 'astra-field-groups' ), 'color' => 'blue' ],
                'description' => __( 'All field group configurations passed validation checks.', 'astra-field-groups' ),
                'actions'     => [],
                'test'        => 'astra_field_groups',
            ];
        }

        return [
            'label'       => __( 'Field group configuration issues found.', 'astra-field-groups' ),
            'status'      => 'recommended',
            'badge'       => [ 'label' => __( 'Field Groups', 'astra-field-groups' ), 'color' => 'red' ],
            'description' => '<p>' . implode( '</p><p>', array_map( 'esc_html', $issues ) ) . '</p>',
            'actions'     => [],
            'test'        => 'astra_field_groups',
        ];
    }

    /**
     * Public API: Retrieve field groups.
     *
     * @param string $post_type Optional post type.
     * @return array
     */
    public function get_field_groups( $post_type = '' ) {
        if ( $post_type ) {
            return $this->get_field_groups_for_post_type( $post_type );
        }

        $query = new WP_Query(
            [
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            ]
        );

        $groups = [];
        foreach ( $query->posts as $post ) {
            $groups[] = [
                'id'     => $post->ID,
                'title'  => get_the_title( $post ),
                'fields' => $this->get_group_schema( $post->ID ),
            ];
        }
        wp_reset_postdata();

        return $groups;
    }

    /**
     * Public API: Get field values for post.
     *
     * @param int $post_id Post ID.
     * @return array
     */
    public function get_post_field_values( $post_id ) {
        $groups = $this->get_field_groups();
        $values = [];

        foreach ( $groups as $group ) {
            $values[ $group['id'] ] = get_post_meta( $post_id, '_astra_field_group_values_' . $group['id'], true );
        }

        return $values;
    }
}
