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

/**
 * ? FIX:
 * Do NOT re-require REST core classes.
 * WordPress loads all REST dependencies automatically in wp-settings.php.
 * Instead, register this controller when the REST API initializes.
 */
add_action( 'rest_api_init', function() {

    /**
     * REST controller that exposes cache management endpoints.
     *
     * @since 1.0.0
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
         * Registers all routes handled by this controller.
         *
         * @return void
         */
        public function register_routes() {
            // ?? Purge cache route
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

            // ? Schedule cache purge route
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

            // ?? Configuration routes
            register_rest_route(
                $this->namespace,
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
         * Check if the current user has permission to manage performance settings.
         *
         * @return bool
         */
        public function check_permissions() {
            return current_user_can( 'manage_options' );
        }

        /**
         * Purge caches based on request parameters.
         *
         * @param WP_REST_Request $request The REST request.
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
         * Schedule cache purges at a specific timestamp.
         *
         * @param WP_REST_Request $request The REST request.
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
         * Retrieve all scheduled cache purges.
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
         * Get the full configuration of performance settings.
         *
         * @return WP_REST_Response
         */
        public function get_configuration() {
            return new WP_REST_Response( wp_performance_get_all_settings() );
        }

        /**
         * Update performance settings configuration.
         *
         * @param WP_REST_Request $request The REST request.
         * @return WP_REST_Response
         */
        public function update_configuration( $request ) {
            $settings = (array) $request->get_param( 'settings' );

            $updated = wp_performance_update_all_settings( $settings );

            return new WP_REST_Response(
                array(
                    'updated'  => $updated,
                    'settings' => wp_performance_get_all_settings(),
                )
            );
        }
    }

    // ? Initialize and register controller routes
    ( new WP_REST_Performance_Controller() )->register_routes();

});
