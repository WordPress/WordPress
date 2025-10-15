<?php
/**
 * Persistent object cache adapter implementations.
 *
 * @package WordPress
 * @subpackage Performance
 */

if ( class_exists( 'WP_Performance_Persistent_Cache_Adapter' ) ) {
    return;
}

/**
 * Adapter for persistent object caches such as Redis or Memcached.
 */
class WP_Performance_Persistent_Cache_Adapter extends WP_Performance_Cache_Adapter {
    /**
     * Selected backend key.
     *
     * @var string
     */
    protected $backend;

    /**
     * Configuration array.
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * Assigns configuration for the adapter.
     *
     * @param array $settings Array of settings from the UI.
     */
    public function configure( $settings ) {
        parent::configure( $settings );

        $defaults = array(
            'backend'     => 'none',
            'host'        => '127.0.0.1',
            'port'        => '',
            'database'    => '',
            'password'    => '',
            'compression' => false,
        );

        $this->configuration = wp_parse_args( $settings, $defaults );
        $this->backend       = $this->configuration['backend'];
    }

    /**
     * Attempts to flush the persistent cache.
     */
    public function flush() {
        if ( 'redis' === $this->backend && function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
            return;
        }

        if ( in_array( $this->backend, array( 'memcached', 'lsmcd' ), true ) && function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }
    }

    /**
     * Builds the status payload based on the backend and extensions.
     *
     * @return array
     */
    protected function generate_status() {
        $status  = 'recommended';
        $message = __( 'A persistent object cache can greatly improve database performance.' );

        switch ( $this->backend ) {
            case 'redis':
                if ( extension_loaded( 'redis' ) ) {
                    $status  = 'good';
                    $message = __( 'Redis extension detected and configured.' );
                } else {
                    $status  = 'critical';
                    $message = __( 'Redis backend selected but the PHP extension is not available.' );
                }
                break;
            case 'memcached':
                if ( extension_loaded( 'memcached' ) ) {
                    $status  = 'good';
                    $message = __( 'Memcached extension detected and configured.' );
                } else {
                    $status  = 'critical';
                    $message = __( 'Memcached backend selected but the PHP extension is not available.' );
                }
                break;
            case 'lsmcd':
                if ( extension_loaded( 'lsmemcache' ) || extension_loaded( 'memcached' ) ) {
                    $status  = 'good';
                    $message = __( 'LSMCD backend configuration detected.' );
                } else {
                    $status  = 'critical';
                    $message = __( 'LSMCD backend selected but the required PHP extension is not loaded.' );
                }
                break;
            default:
                $status  = 'recommended';
                $message = __( 'No persistent object cache backend selected.' );
                break;
        }

        return array(
            'status'  => $status,
            'label'   => sprintf( __( 'Persistent Object Cache (%s)' ), strtoupper( $this->backend ? $this->backend : __( 'None' ) ) ),
            'message' => $message,
        );
    }
}
