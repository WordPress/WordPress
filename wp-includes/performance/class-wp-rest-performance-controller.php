<?php
/**
 * REST controller for the performance module.
 *
 * @package WordPress
 * @subpackage Performance
 */

if ( class_exists( 'WP_REST_Performance_Controller' ) ) {
    return;
}

if ( ! class_exists( 'WP_REST_Server' ) ) {
    require_once ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php';
}

if ( ! class_exists( 'WP_HTTP_Response' ) ) {
    require_once ABSPATH . WPINC . '/class-wp-http-response.php';
}

if ( ! class_exists( 'WP_REST_Response' ) ) {
    require_once ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php';
}

if ( ! class_exists( 'WP_REST_Request' ) ) {
    require_once ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php';
}

if ( ! class_exists( 'WP_REST_Controller' ) ) {
    require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-controller.php';
}

if ( ! function_exists( 'register_rest_route' ) ) {
    require_once ABSPATH . WPINC . '/rest-api.php';
}

/**
 * REST controller that exposes cache management endpoints.
 */
class WP_REST_Performance_Controller extends WP_REST_Controller {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->namespace = 'wp-performance/v1';
        $this->rest_base = 'cache';
    }

    /**
     * Registers routes handled by this controller.
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/purge',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'permission_callback' => array( $this, 'check_permissions' ),
                'callback'            => array( $this, 'purge_cache' ),
                'args'                => array(
                    'type' => array(
                        'type'    => 'string',
                        'default' => 'all',
                    ),
                    'targets' => array(
                        'type'    => 'array',
                        'default' => array(),
                    ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/schedule',
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => array( $this, 'check_permissions' ),
                    'callback'            => array( $this, 'schedule_purge' ),
                    'args'                => array(
                        'paths'     => array(
                            'type'     => 'array',
                            'required' => true,
                        ),
                        'timestamp' => array(
                            'type'    => 'integer',
                            'default' => 0,
                        ),
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'check_permissions' ),
                    'callback'            => array( $this, 'get_schedule' ),
                ),
            )
        );

        register_rest_route(
            'wp-performance/v1',
            '/config',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'check_permissions' ),
                    'callback'            => array( $this, 'get_configuration' ),
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'permission_callback' => array( $this, 'check_permissions' ),
                    'callback'            => array( $this, 'update_configuration' ),
                    'args'                => array(
                        'settings' => array(
                            'type'     => 'object',
                            'required' => true,
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * Permission check for controller.
     *
     * @return bool
     */
    public function check_permissions() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Purge the caches based on request arguments.
     *
     * @param WP_REST_Request $request Request instance.
     *
     * @return WP_REST_Response
     */
    public function purge_cache( $request ) {
        $type    = $request->get_param( 'type' );
        $targets = (array) $request->get_param( 'targets' );

        $result = wp_performance_purge_cache( $type, $targets );

        return new WP_REST_Response(
            array(
                'type'    => $type,
                'targets' => $targets,
                'result'  => $result,
            )
        );
    }

    /**
     * Schedule page cache purges at a given timestamp.
     *
     * @param WP_REST_Request $request Request instance.
     *
     * @return WP_REST_Response
     */
    public function schedule_purge( $request ) {
        $paths     = (array) $request->get_param( 'paths' );
        $timestamp = (int) $request->get_param( 'timestamp' );

        $scheduled = wp_performance_schedule_purge_list( $paths, $timestamp );

        return new WP_REST_Response(
            array(
                'paths'     => $paths,
                'timestamp' => $timestamp,
                'scheduled' => $scheduled,
            )
        );
    }

    /**
     * Returns the scheduled purge list.
     *
     * @return WP_REST_Response
     */
    public function get_schedule() {
        $settings = wp_performance_get_page_cache_settings();

        return new WP_REST_Response(
            array(
                'scheduled_purges' => $settings['scheduled_purges'],
            )
        );
    }

    /**
     * Returns the performance configuration for export.
     *
     * @return WP_REST_Response
     */
    public function get_configuration() {
        return new WP_REST_Response( wp_performance_get_all_settings() );
    }

    /**
     * Updates the performance configuration from an import payload.
     *
     * @param WP_REST_Request $request Request instance.
     *
     * @return WP_REST_Response
     */
    public function update_configuration( $request ) {
        $settings = (array) $request->get_param( 'settings' );

        $updated = wp_performance_update_all_settings( $settings );

        return new WP_REST_Response(
            array(
                'updated'   => $updated,
                'settings'  => wp_performance_get_all_settings(),
            )
        );
    }
}
