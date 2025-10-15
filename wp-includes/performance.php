<?php
/**
 * Performance framework bootstrap.
 *
 * @package WordPress
 * @subpackage Performance
 */

require_once __DIR__ . '/performance/class-wp-performance-cache-adapter.php';
require_once __DIR__ . '/performance/class-wp-performance-persistent-cache-adapter.php';
require_once __DIR__ . '/performance/class-wp-performance-page-cache-adapter.php';
require_once __DIR__ . '/performance/class-wp-performance-opcode-cache-adapter.php';
require_once __DIR__ . '/performance/class-wp-rest-performance-controller.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once __DIR__ . '/performance/class-wp-performance-cli-command.php';
    WP_CLI::add_command( 'performance', 'WP_Performance_CLI_Command' );
}

/**
 * Returns the persistent cache settings.
 *
 * @return array
 */
function wp_performance_get_persistent_cache_settings() {
    $defaults = array(
        'backend'     => 'none',
        'host'        => '127.0.0.1',
        'port'        => '',
        'database'    => '',
        'password'    => '',
        'compression' => false,
        'opcode'      => array(),
    );

    $settings = get_option( 'wp_performance_persistent_cache', array() );

    return wp_parse_args( $settings, $defaults );
}

/**
 * Returns the page cache settings.
 *
 * @return array
 */
function wp_performance_get_page_cache_settings() {
    $defaults = array(
        'enabled'          => false,
        'mobile_segment'   => false,
        'desktop_segment'  => false,
        'ttl'              => 0,
        'scheduled_purges' => array(),
        'rest_cache'       => false,
    );

    $settings = get_option( 'wp_performance_page_cache', array() );

    $settings = wp_parse_args( $settings, $defaults );

    if ( ! is_array( $settings['scheduled_purges'] ) ) {
        $settings['scheduled_purges'] = array();
    }

    return $settings;
}

/**
 * Returns the asset optimization settings.
 *
 * @return array
 */
function wp_performance_get_asset_optimization_settings() {
    $defaults = array(
        'enable_pipeline'   => false,
        'minify_js'         => false,
        'minify_css'        => false,
        'minify_html'       => false,
        'combine_js'        => false,
        'combine_css'       => false,
        'async_js'          => false,
        'defer_js'          => false,
        'critical_css_hook' => '',
    );

    $settings = get_option( 'wp_performance_asset_optimization', array() );

    return wp_parse_args( $settings, $defaults );
}

/**
 * Returns the image optimization settings.
 *
 * @return array
 */
function wp_performance_get_image_optimization_settings() {
    $defaults = array(
        'mode'         => 'lossless',
        'convert_webp' => false,
        'convert_avif' => false,
        'placeholders' => false,
        'lazy_loading' => 'default',
        'quality'      => 82,
    );

    $settings = get_option( 'wp_performance_image_optimization', array() );

    return wp_parse_args( $settings, $defaults );
}

/**
 * Returns all registered performance settings.
 *
 * @return array
 */
function wp_performance_get_all_settings() {
    return array(
        'persistent_cache'   => wp_performance_get_persistent_cache_settings(),
        'page_cache'         => wp_performance_get_page_cache_settings(),
        'asset_optimization' => wp_performance_get_asset_optimization_settings(),
        'image_optimization' => wp_performance_get_image_optimization_settings(),
    );
}

/**
 * Updates all settings based on an import payload.
 *
 * @param array $settings Settings array keyed by module.
 *
 * @return bool True if any update succeeded.
 */
function wp_performance_update_all_settings( $settings ) {
    $updated = false;

    if ( isset( $settings['persistent_cache'] ) ) {
        $updated = update_option( 'wp_performance_persistent_cache', (array) $settings['persistent_cache'] ) || $updated;
    }

    if ( isset( $settings['page_cache'] ) ) {
        $updated = update_option( 'wp_performance_page_cache', (array) $settings['page_cache'] ) || $updated;
    }

    if ( isset( $settings['asset_optimization'] ) ) {
        $updated = update_option( 'wp_performance_asset_optimization', (array) $settings['asset_optimization'] ) || $updated;
    }

    if ( isset( $settings['image_optimization'] ) ) {
        $updated = update_option( 'wp_performance_image_optimization', (array) $settings['image_optimization'] ) || $updated;
    }

    return $updated;
}

/**
 * Registers Site Health tests for the configured adapters.
 *
 * @param array $tests Existing tests.
 *
 * @return array
 */
function wp_performance_register_site_health_tests( $tests ) {
    $adapters = wp_performance_get_cache_adapters();
    foreach ( $adapters as $adapter ) {
        $tests['direct']['performance_' . $adapter->get_id() ] = array(
            'label' => $adapter->get_label(),
            'test'  => function () use ( $adapter ) {
                $status = $adapter->get_status();

                return array(
                    'label'       => $status['label'],
                    'status'      => $status['status'],
                    'description' => $status['message'],
                );
            },
        );
    }

    return $tests;
}
add_filter( 'site_status_tests', 'wp_performance_register_site_health_tests' );

/**
 * Returns cache adapters for the configured settings.
 *
 * @return WP_Performance_Cache_Adapter[]
 */
function wp_performance_get_cache_adapters() {
    static $adapters = null;

    if ( null !== $adapters ) {
        return $adapters;
    }

    $persistent_settings = wp_performance_get_persistent_cache_settings();
    $page_settings       = wp_performance_get_page_cache_settings();
    $opcode_settings     = isset( $persistent_settings['opcode'] ) ? $persistent_settings['opcode'] : array();

    $persistent = new WP_Performance_Persistent_Cache_Adapter( 'persistent', __( 'Persistent Object Cache' ) );
    $persistent->configure( $persistent_settings );

    $page = new WP_Performance_Page_Cache_Adapter( 'page', __( 'Page Cache' ) );
    $page->configure( $page_settings );

    $opcode = new WP_Performance_Opcode_Cache_Adapter( 'opcode', __( 'Opcode Cache' ) );
    $opcode->configure( $opcode_settings );

    $adapters = array( $persistent, $page, $opcode );

    return $adapters;
}

/**
 * Flush caches for CLI and REST integrations.
 *
 * @param string $type    Cache type identifier (object|page|opcode|all).
 * @param array  $targets Optional list of targets for page cache.
 *
 * @return bool True on success.
 */
function wp_performance_purge_cache( $type = 'all', $targets = array() ) {
    $adapters = wp_performance_get_cache_adapters();
    $result   = true;

    foreach ( $adapters as $adapter ) {
        if ( 'all' !== $type && $adapter->get_id() !== $type ) {
            continue;
        }

        if ( 'page' === $adapter->get_id() && ! empty( $targets ) ) {
            do_action( 'wp_performance_purge_page_targets', $targets );
        }

        try {
            $adapter->flush();
        } catch ( Exception $exception ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
            $result = false;
        }
    }

    return $result;
}

/**
 * Register REST routes for performance settings.
 */
function wp_performance_register_rest_routes() {
    $controller = new WP_REST_Performance_Controller();
    $controller->register_routes();
}
add_action( 'rest_api_init', 'wp_performance_register_rest_routes' );

/**
 * Schedules cache purges.
 *
 * @param array $paths     Paths that should be purged.
 * @param int   $timestamp Optional timestamp; defaults to now.
 *
 * @return array Scheduled purge list.
 */
function wp_performance_schedule_purge_list( $paths, $timestamp = 0 ) {
    $timestamp = $timestamp ? $timestamp : time();
    $paths     = array_filter( array_map( 'sanitize_text_field', (array) $paths ) );

    $settings = wp_performance_get_page_cache_settings();

    if ( ! isset( $settings['scheduled_purges'] ) || ! is_array( $settings['scheduled_purges'] ) ) {
        $settings['scheduled_purges'] = array();
    }

    $settings['scheduled_purges'][ $timestamp ] = $paths;
    ksort( $settings['scheduled_purges'] );

    update_option( 'wp_performance_page_cache', $settings );

    if ( ! wp_next_scheduled( 'wp_performance_run_scheduled_purges' ) ) {
        wp_schedule_event( time() + MINUTE_IN_SECONDS, 'hourly', 'wp_performance_run_scheduled_purges' );
    }

    return $settings['scheduled_purges'];
}

/**
 * Runs scheduled purge events.
 */
function wp_performance_execute_scheduled_purges() {
    $settings = wp_performance_get_page_cache_settings();

    if ( empty( $settings['scheduled_purges'] ) ) {
        return;
    }

    $now       = time();
    $remaining = array();

    foreach ( $settings['scheduled_purges'] as $timestamp => $paths ) {
        if ( $timestamp <= $now ) {
            wp_performance_purge_cache( 'page', (array) $paths );
        } else {
            $remaining[ $timestamp ] = $paths;
        }
    }

    $settings['scheduled_purges'] = $remaining;
    update_option( 'wp_performance_page_cache', $settings );
}
add_action( 'wp_performance_run_scheduled_purges', 'wp_performance_execute_scheduled_purges' );

/**
 * Creates the cache directory used for combined assets.
 *
 * @return string|false The directory path or false if it cannot be created.
 */
function wp_performance_get_cache_directory() {
    $upload_dir = wp_upload_dir();

    if ( empty( $upload_dir['basedir'] ) ) {
        return false;
    }

    $path = trailingslashit( $upload_dir['basedir'] ) . 'performance-cache';

    if ( wp_mkdir_p( $path ) ) {
        return $path;
    }

    return false;
}

/**
 * Returns the base URL used for cached assets.
 *
 * @return string|false
 */
function wp_performance_get_cache_url() {
    $upload_dir = wp_upload_dir();

    if ( empty( $upload_dir['baseurl'] ) ) {
        return false;
    }

    return trailingslashit( $upload_dir['baseurl'] ) . 'performance-cache';
}

/**
 * Writes a cache file containing optimized assets.
 *
 * @param string $filename File name.
 * @param string $contents File contents.
 *
 * @return string|false Absolute path to file on success.
 */
function wp_performance_write_cache_file( $filename, $contents ) {
    $directory = wp_performance_get_cache_directory();

    if ( ! $directory ) {
        return false;
    }

    $path = trailingslashit( $directory ) . $filename;

    $written = file_put_contents( $path, $contents );

    if ( false === $written ) {
        return false;
    }

    return $path;
}

/**
 * Simple JS minification.
 *
 * @param string $content Script contents.
 *
 * @return string
 */
function wp_performance_minify_js( $content ) {
    $content = preg_replace( '#/\*.*?\*/#s', '', $content );
    $content = preg_replace( '/\s+/', ' ', $content );

    return trim( $content );
}

/**
 * Simple CSS minification.
 *
 * @param string $css CSS string.
 *
 * @return string
 */
function wp_performance_minify_css( $css ) {
    $css = preg_replace( '#/\*.*?\*/#s', '', $css );
    $css = preg_replace( '/\s*([{};:,])\s*/', '$1', $css );
    $css = preg_replace( '/\s+/', ' ', $css );

    return trim( $css );
}

/**
 * Simple HTML minification callback.
 *
 * @param string $html HTML output.
 *
 * @return string
 */
function wp_performance_minify_html( $html ) {
    $html = preg_replace( '/>\s+</', '><', $html );
    $html = preg_replace( '/\s+/', ' ', $html );

    return trim( $html );
}

/**
 * Stores REST API responses when caching is enabled.
 *
 * @param mixed           $result Result to send.
 * @param WP_REST_Server  $server REST server.
 * @param WP_REST_Request $request Request object.
 *
 * @return mixed
 */
function wp_performance_rest_pre_dispatch_cache( $result, $server, $request ) {
    $settings = wp_performance_get_page_cache_settings();

    if ( empty( $settings['rest_cache'] ) ) {
        return $result;
    }

    $key = wp_performance_rest_cache_key( $request );

    if ( false !== ( $cached = get_transient( $key ) ) ) {
        return $cached;
    }

    return $result;
}
add_filter( 'rest_pre_dispatch', 'wp_performance_rest_pre_dispatch_cache', 10, 3 );

/**
 * Stores REST API responses after dispatch.
 *
 * @param mixed           $response Response.
 * @param WP_REST_Server  $server   REST server.
 * @param WP_REST_Request $request  Request object.
 *
 * @return mixed
 */
function wp_performance_rest_post_dispatch_cache( $response, $server, $request ) {
    $settings = wp_performance_get_page_cache_settings();

    if ( empty( $settings['rest_cache'] ) ) {
        return $response;
    }

    $key = wp_performance_rest_cache_key( $request );

    if ( $response instanceof WP_REST_Response ) {
        $data = $response->get_data();
        set_transient( $key, $response, HOUR_IN_SECONDS );
        $response->set_data( $data );
    } else {
        set_transient( $key, $response, HOUR_IN_SECONDS );
    }

    return $response;
}
add_filter( 'rest_post_dispatch', 'wp_performance_rest_post_dispatch_cache', 10, 3 );

/**
 * Generates the cache key for REST requests taking segmentation into account.
 *
 * @param WP_REST_Request $request Request.
 *
 * @return string
 */
function wp_performance_rest_cache_key( $request ) {
    $settings = wp_performance_get_page_cache_settings();

    $segments = array();

    if ( ! empty( $settings['mobile_segment'] ) && wp_is_mobile() ) {
        $segments[] = 'mobile';
    }

    if ( ! empty( $settings['desktop_segment'] ) ) {
        $segments[] = 'desktop';
    }

    if ( empty( $segments ) ) {
        $segments[] = wp_is_mobile() ? 'mobile' : 'desktop';
    }

    $url     = $request->get_route();
    $params  = $request->get_query_params();
    $context = md5( $url . '|' . wp_json_encode( $params ) );

    return 'wp_performance_rest_' . implode( '_', $segments ) . '_' . $context;
}
