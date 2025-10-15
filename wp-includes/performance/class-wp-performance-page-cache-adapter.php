<?php
/**
 * Page cache adapter implementation.
 *
 * @package WordPress
 * @subpackage Performance
 */

if ( class_exists( 'WP_Performance_Page_Cache_Adapter' ) ) {
    return;
}

/**
 * Adapter describing page cache settings and capabilities.
 */
class WP_Performance_Page_Cache_Adapter extends WP_Performance_Cache_Adapter {
    /**
     * Active rules configuration.
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * Assign configuration values.
     *
     * @param array $settings Settings array.
     */
    public function configure( $settings ) {
        parent::configure( $settings );

        $defaults = array(
            'enabled'          => false,
            'mobile_segment'   => false,
            'desktop_segment'  => false,
            'ttl'              => 0,
            'scheduled_purges' => array(),
            'rest_cache'       => false,
        );

        $this->configuration = wp_parse_args( $settings, $defaults );
    }

    /**
     * Flushes the page cache by triggering a hook so plugins can respond.
     */
    public function flush() {
        /**
         * Fires when the performance page cache is purged.
         */
        do_action( 'wp_performance_purge_page_cache' );
    }

    /**
     * Generates a status summary for Site Health.
     *
     * @return array
     */
    protected function generate_status() {
        if ( empty( $this->configuration['enabled'] ) ) {
            return array(
                'status'  => 'recommended',
                'label'   => __( 'Page Cache' ),
                'message' => __( 'Enable page caching to reduce time to first byte for visitors.' ),
            );
        }

        $message = __( 'Page cache is enabled.' );
        if ( ! empty( $this->configuration['mobile_segment'] ) || ! empty( $this->configuration['desktop_segment'] ) ) {
            $message .= ' ' . __( 'Segmentation rules are active.' );
        }

        if ( ! empty( $this->configuration['ttl'] ) ) {
            $message .= ' ' . sprintf( __( 'Entries expire after %d seconds.' ), (int) $this->configuration['ttl'] );
        }

        return array(
            'status'  => 'good',
            'label'   => __( 'Page Cache' ),
            'message' => $message,
        );
    }
}
