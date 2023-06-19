<?php
/**
 * WP-CLI commands for Autoptimize.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// This is a WP-CLI command, so bail if it's not available.
if ( ! defined( 'WP_CLI' ) ) {
    return;
}

class autoptimizeCLI extends \WP_CLI_Command
{
    /**
     * Clears the cache.
     *
     * @subcommand clear
     *
     * @param array $args Args.
     * @param array $args_assoc Associative args.
     *
     * @return void
     */
    public function clear( $args, $args_assoc ) {
        WP_CLI::line( esc_html__( 'Flushing the cache...', 'autoptimize' ) );
        autoptimizeCache::clearall();
        WP_CLI::success( esc_html__( 'Cache flushed.', 'autoptimize' ) );
    }
}

WP_CLI::add_command( 'autoptimize', 'autoptimizeCLI' );
