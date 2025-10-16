<?php
/**
 * Plugin Name: Astra Field Groups
 * Description: Provides custom field group editor, rendering, REST APIs, and tools.
 * Author: OpenAI Assistant
 * Version: 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/includes/class-astra-field-groups-plugin.php';

Astra_Field_Groups_Plugin::get_instance();

if ( ! function_exists( 'astra_field_groups_get' ) ) {
    /**
     * Retrieve field group definitions.
     *
     * @param string $post_type Optional post type to filter groups.
     * @return array
     */
    function astra_field_groups_get( $post_type = '' ) {
        return Astra_Field_Groups_Plugin::get_instance()->get_field_groups( $post_type );
    }
}

if ( ! function_exists( 'astra_field_groups_get_values' ) ) {
    /**
     * Retrieve field values for a post.
     *
     * @param int $post_id Post ID.
     * @return array
     */
    function astra_field_groups_get_values( $post_id ) {
        return Astra_Field_Groups_Plugin::get_instance()->get_post_field_values( $post_id );
    }
}
