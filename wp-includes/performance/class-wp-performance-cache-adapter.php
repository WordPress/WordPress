<?php
/**
 * Base performance cache adapter.
 *
 * @package WordPress
 * @subpackage Performance
 */

if ( class_exists( 'WP_Performance_Cache_Adapter' ) ) {
    return;
}

/**
 * Abstract adapter that describes a cache backend.
 */
abstract class WP_Performance_Cache_Adapter {
    /**
     * Adapter identifier.
     *
     * @var string
     */
    protected $id;

    /**
     * Human readable label.
     *
     * @var string
     */
    protected $label;

    /**
     * Cached status data.
     *
     * @var array
     */
    protected $status = array();

    /**
     * Constructor.
     *
     * @param string $id    Adapter identifier.
     * @param string $label Human readable label.
     */
    public function __construct( $id, $label ) {
        $this->id    = $id;
        $this->label = $label;
    }

    /**
     * Returns the adapter identifier.
     *
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Returns the label for human display.
     *
     * @return string
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Returns a description of the adapter status.
     *
     * @return array {
     *     @type string $status  Status slug (good|recommended|critical).
     *     @type string $label   Display label.
     *     @type string $message Additional descriptive text.
     * }
     */
    public function get_status() {
        if ( empty( $this->status ) ) {
            $this->status = $this->generate_status();
        }

        return $this->status;
    }

    /**
     * Registers configuration for the adapter.
     *
     * @param array $settings Settings array from the UI.
     */
    public function configure( $settings ) {
        // Store settings for future use if needed.
        $this->status = array();
    }

    /**
     * Flushes adapter cache.
     */
    public function flush() {
        // Intentionally empty – to be implemented by children where applicable.
    }

    /**
     * Creates the status payload for this adapter.
     *
     * @return array
     */
    abstract protected function generate_status();
}
