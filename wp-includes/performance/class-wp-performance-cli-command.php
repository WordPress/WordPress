<?php
/**
 * WP-CLI commands for the performance module.
 *
 * @package WordPress
 * @subpackage Performance
 */

if ( class_exists( 'WP_Performance_CLI_Command' ) ) {
    return;
}

if ( ! class_exists( 'WP_CLI_Command' ) ) {
    return;
}

/**
 * Provides performance related WP-CLI commands.
 */
class WP_Performance_CLI_Command extends WP_CLI_Command {
    /**
     * Flushes caches.
     *
     * ## OPTIONS
     *
     * [--type=<type>]
     * : Cache type to purge (object|page|opcode|all).
     *
     * [--target=<target>]
     * : Optional specific targets for purge.
     */
    public function cache_purge( $args, $assoc_args ) {
        $type    = isset( $assoc_args['type'] ) ? $assoc_args['type'] : 'all';
        $targets = array();
        if ( isset( $assoc_args['target'] ) ) {
            $targets = array_map( 'trim', explode( ',', $assoc_args['target'] ) );
        }

        $result = wp_performance_purge_cache( $type, $targets );

        WP_CLI::success( sprintf( 'Purged %s cache (%s).', $type, $result ? 'success' : 'partial' ) );
    }

    /**
     * Lists or schedules cache purges.
     *
     * ## OPTIONS
     *
     * [--paths=<paths>]
     * : Comma separated list of paths to purge. When omitted, the current schedule is output.
     *
     * [--timestamp=<timestamp>]
     * : Unix timestamp for the purge. Defaults to now.
     */
    public function cache_schedule( $args, $assoc_args ) {
        if ( empty( $assoc_args['paths'] ) ) {
            $settings = wp_performance_get_page_cache_settings();
            WP_CLI::print_value( $settings['scheduled_purges'], array( 'format' => 'json' ) );
            return;
        }

        $paths     = array_map( 'trim', explode( ',', $assoc_args['paths'] ) );
        $timestamp = isset( $assoc_args['timestamp'] ) ? (int) $assoc_args['timestamp'] : time();

        wp_performance_schedule_purge_list( $paths, $timestamp );

        WP_CLI::success( sprintf( 'Scheduled purge for %d paths at %s.', count( $paths ), date_i18n( 'c', $timestamp ) ) );
    }

    /**
     * Exports the configuration to STDOUT.
     */
    public function config_export() {
        $config = wp_performance_get_all_settings();
        WP_CLI::print_value( $config, array( 'format' => 'json' ) );
    }

    /**
     * Imports configuration from a JSON string.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to a JSON file containing configuration data.
     */
    public function config_import( $args ) {
        $file = isset( $args[0] ) ? $args[0] : '';

        if ( empty( $file ) || ! file_exists( $file ) ) {
            WP_CLI::error( 'Provide a valid configuration file.' );
        }

        $contents = file_get_contents( $file );
        $data     = json_decode( $contents, true );

        if ( empty( $data ) || ! is_array( $data ) ) {
            WP_CLI::error( 'File does not contain valid configuration data.' );
        }

        if ( wp_performance_update_all_settings( $data ) ) {
            WP_CLI::success( 'Configuration imported.' );
        } else {
            WP_CLI::warning( 'Configuration import completed with warnings.' );
        }
    }
}
