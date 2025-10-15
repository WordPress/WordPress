<?php
/**
 * Performance settings registration.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers the Performance settings group and fields.
 */
function wp_performance_register_admin_settings() {
    register_setting(
        'performance',
        'wp_performance_persistent_cache',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'wp_performance_sanitize_persistent_cache',
            'default'           => array(),
        )
    );

    register_setting(
        'performance',
        'wp_performance_page_cache',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'wp_performance_sanitize_page_cache',
            'default'           => array(),
        )
    );

    register_setting(
        'performance',
        'wp_performance_asset_optimization',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'wp_performance_sanitize_asset_optimization',
            'default'           => array(),
        )
    );

    register_setting(
        'performance',
        'wp_performance_image_optimization',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'wp_performance_sanitize_image_optimization',
            'default'           => array(),
        )
    );
}
add_action( 'admin_init', 'wp_performance_register_admin_settings' );

if ( did_action( 'admin_init' ) ) {
    wp_performance_register_admin_settings();
}

/**
 * Sanitizes persistent cache settings.
 *
 * @param array $settings Raw settings.
 *
 * @return array
 */
function wp_performance_sanitize_persistent_cache( $settings ) {
    $settings = (array) $settings;

    $settings['backend']     = isset( $settings['backend'] ) ? sanitize_key( $settings['backend'] ) : 'none';
    $settings['host']        = isset( $settings['host'] ) ? sanitize_text_field( $settings['host'] ) : '127.0.0.1';
    $settings['port']        = isset( $settings['port'] ) ? absint( $settings['port'] ) : '';
    $settings['database']    = isset( $settings['database'] ) ? absint( $settings['database'] ) : '';
    $settings['password']    = isset( $settings['password'] ) ? sanitize_text_field( $settings['password'] ) : '';
    $settings['compression'] = ! empty( $settings['compression'] );
    $settings['opcode']      = isset( $settings['opcode'] ) ? array( 'engine' => sanitize_key( $settings['opcode'] ) ) : array();

    return $settings;
}

/**
 * Sanitizes page cache settings.
 *
 * @param array $settings Raw settings.
 *
 * @return array
 */
function wp_performance_sanitize_page_cache( $settings ) {
    $settings = (array) $settings;

    $settings['enabled']         = ! empty( $settings['enabled'] );
    $settings['mobile_segment']  = ! empty( $settings['mobile_segment'] );
    $settings['desktop_segment'] = ! empty( $settings['desktop_segment'] );
    $settings['ttl']             = isset( $settings['ttl'] ) ? absint( $settings['ttl'] ) : 0;
    $settings['rest_cache']      = ! empty( $settings['rest_cache'] );

    if ( isset( $settings['scheduled_purges'] ) && is_array( $settings['scheduled_purges'] ) ) {
        $clean = array();
        foreach ( $settings['scheduled_purges'] as $timestamp => $paths ) {
            $timestamp         = absint( $timestamp );
            $clean[ $timestamp ] = array_map( 'sanitize_text_field', (array) $paths );
        }
        $settings['scheduled_purges'] = $clean;
    } else {
        $settings['scheduled_purges'] = array();
    }

    return $settings;
}

/**
 * Sanitizes asset optimization settings.
 *
 * @param array $settings Raw settings.
 *
 * @return array
 */
function wp_performance_sanitize_asset_optimization( $settings ) {
    $settings = (array) $settings;

    $flags = array(
        'enable_pipeline',
        'minify_js',
        'minify_css',
        'minify_html',
        'combine_js',
        'combine_css',
        'async_js',
        'defer_js',
    );

    foreach ( $flags as $flag ) {
        $settings[ $flag ] = ! empty( $settings[ $flag ] );
    }

    $settings['critical_css_hook'] = isset( $settings['critical_css_hook'] ) ? sanitize_text_field( $settings['critical_css_hook'] ) : '';

    return $settings;
}

/**
 * Sanitizes image optimization settings.
 *
 * @param array $settings Raw settings.
 *
 * @return array
 */
function wp_performance_sanitize_image_optimization( $settings ) {
    $settings = (array) $settings;

    $settings['mode']             = isset( $settings['mode'] ) ? sanitize_key( $settings['mode'] ) : 'lossless';
    $settings['convert_webp']     = ! empty( $settings['convert_webp'] );
    $settings['convert_avif']     = ! empty( $settings['convert_avif'] );
    $settings['placeholders']     = ! empty( $settings['placeholders'] );
    $settings['lazy_loading']     = isset( $settings['lazy_loading'] ) ? sanitize_key( $settings['lazy_loading'] ) : 'default';
    $settings['quality']          = isset( $settings['quality'] ) ? min( 100, max( 10, absint( $settings['quality'] ) ) ) : 82;

    return $settings;
}
