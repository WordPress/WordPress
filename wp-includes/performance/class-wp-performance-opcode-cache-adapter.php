<?php
/**
 * Opcode cache adapter implementation.
 *
 * @package WordPress
 * @subpackage Performance
 */

if ( class_exists( 'WP_Performance_Opcode_Cache_Adapter' ) ) {
    return;
}

/**
 * Adapter for opcode caches (OPcache, APCu, etc).
 */
class WP_Performance_Opcode_Cache_Adapter extends WP_Performance_Cache_Adapter {
    /**
     * Active engine slug.
     *
     * @var string
     */
    protected $engine = 'opcache';

    /**
     * Configures the adapter.
     *
     * @param array $settings Settings array.
     */
    public function configure( $settings ) {
        parent::configure( $settings );

        if ( isset( $settings['engine'] ) ) {
            $this->engine = $settings['engine'];
        }
    }

    /**
     * Flush opcode caches where possible.
     */
    public function flush() {
        if ( 'opcache' === $this->engine && function_exists( 'opcache_reset' ) ) {
            @opcache_reset();
        }

        if ( 'apcu' === $this->engine && function_exists( 'apcu_clear_cache' ) ) {
            @apcu_clear_cache();
        }
    }

    /**
     * Builds a status array that reports availability.
     *
     * @return array
     */
    protected function generate_status() {
        $available = false;
        $message   = '';

        switch ( $this->engine ) {
            case 'opcache':
                $available = function_exists( 'opcache_get_status' );
                $message   = $available ? __( 'OPcache is enabled and reporting status.' ) : __( 'OPcache is not available.' );
                break;
            case 'apcu':
                $available = function_exists( 'apcu_cache_info' );
                $message   = $available ? __( 'APCu opcode cache is enabled.' ) : __( 'APCu extension not available.' );
                break;
            default:
                $message = __( 'No opcode cache integration configured.' );
        }

        return array(
            'status'  => $available ? 'good' : 'recommended',
            'label'   => __( 'Opcode Cache' ),
            'message' => $message,
        );
    }
}
