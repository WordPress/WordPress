<?php
/**
 * Translation relationship helpers and APIs.
 *
 * @package WordPress
 * @subpackage i18n
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

/**
 * Determines whether translation helpers can safely access the database.
 *
 * @since 6.6.2
 * @access private
 *
 * @return bool True when WordPress is installed and options are accessible.
 */
function wp_translation_environment_is_ready() {
        if ( function_exists( 'wp_installing' ) && wp_installing() ) {
                return false;
        }

        if ( ! function_exists( 'get_option' ) ) {
                return false;
        }

        if ( function_exists( 'wp_cache_get' ) ) {
                $is_blog_installed = wp_cache_get( 'is_blog_installed' );

                if ( false === $is_blog_installed ) {
                        return false;
                }
        }

        return true;
}

/**
 * Retrieves the name of the translations table.
 *
 * @since 6.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return string Database table name.
 */
function wp_translation_table() {
        global $wpdb;

        return $wpdb->translations;
}

/**
 * Normalises an object type into a value stored in the translations table.
 *
 * @since 6.6.0
 *
 * @param string $object_type Raw object type.
 * @return string Normalised object type.
 */
function wp_translation_normalize_object_type( $object_type ) {
        switch ( $object_type ) {
                case 'nav_menu':
                        return 'term';
                case 'nav_menu_item':
                        return 'post';
        }

        return $object_type;
}

/**
 * Returns the configured locales for the installation.
 *
 * @since 6.6.0
 *
 * @return string[] List of locale codes.
 */
function wp_translation_get_configured_locales() {
        if ( ! wp_translation_environment_is_ready() ) {
                return array();
        }

        $configured = get_option( 'translation_available_locales', array() );

        if ( empty( $configured ) ) {
                return array();
        }

        if ( is_string( $configured ) ) {
                $configured = preg_split( '/[\r\n]+/', $configured );
        }

        $configured = array_filter( array_map( 'wp_translation_sanitize_locale', (array) $configured ) );

        return array_values( array_unique( $configured ) );
}

/**
 * Returns the default locale configured for fallbacks.
 *
 * @since 6.6.0
 *
 * @return string Locale code.
 */
function wp_translation_get_default_locale() {
        if ( ! wp_translation_environment_is_ready() ) {
                return 'en_US';
        }

        $default = get_option( 'translation_default_locale' );

        if ( ! $default ) {
                $default = get_option( 'WPLANG' );
        }

        if ( ! $default ) {
                $default = 'en_US';
        }

        return wp_translation_sanitize_locale( $default );
}

/**
 * Sanitizes a locale identifier.
 *
 * @since 6.6.0
 *
 * @param string $locale Raw locale string.
 * @return string Sanitized locale string.
 */
function wp_translation_sanitize_locale( $locale ) {
        return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $locale );
}

/**
 * Retrieves domain mappings for locale routing.
 *
 * @since 6.6.0
 *
 * @return array<string,string> Map of domain => locale.
 */
function wp_translation_get_domain_mapping() {
        if ( ! wp_translation_environment_is_ready() ) {
                return array();
        }

        $raw = get_option( 'translation_domain_mapping', array() );

        if ( empty( $raw ) ) {
                return array();
        }

        if ( is_string( $raw ) ) {
                $lines = preg_split( '/[\r\n]+/', $raw );
                $raw   = array();
                foreach ( $lines as $line ) {
                        if ( strpos( $line, '=' ) === false ) {
                                continue;
                        }

                        list( $domain, $locale ) = array_map( 'trim', explode( '=', $line, 2 ) );
                        if ( empty( $domain ) || empty( $locale ) ) {
                                continue;
                        }

                        $raw[ strtolower( $domain ) ] = wp_translation_sanitize_locale( $locale );
                }
        }

        $mapping = array();
        foreach ( (array) $raw as $domain => $locale ) {
                        $mapping[ strtolower( $domain ) ] = wp_translation_sanitize_locale( $locale );
        }

        return $mapping;
}

/**
 * Returns the routing mode.
 *
 * @since 6.6.0
 *
 * @return string Either 'path' or 'domain'.
 */
function wp_translation_get_locale_routing_mode() {
        if ( ! wp_translation_environment_is_ready() ) {
                return 'path';
        }

        $mode = get_option( 'translation_locale_mode', 'path' );

        return in_array( $mode, array( 'path', 'domain' ), true ) ? $mode : 'path';
}

/**
 * Retrieves the locale from the current request if one was detected.
 *
 * @since 6.6.0
 *
 * @return string|null Locale or null when not detected.
 */
function wp_translation_get_request_locale() {
        if ( isset( $GLOBALS['wp_translation_current_locale'] ) ) {
                return $GLOBALS['wp_translation_current_locale'];
        }

        return null;
}

/**
 * Sets the detected locale for the current request.
 *
 * @since 6.6.0
 *
 * @param string $locale Locale identifier.
 * @return void
 */
function wp_translation_set_request_locale( $locale ) {
        $GLOBALS['wp_translation_current_locale'] = wp_translation_sanitize_locale( $locale );
}

/**
 * Attempts to detect a locale based on the current request path or host.
 *
 * @since 6.6.0
 *
 * @return string|null Detected locale or null.
 */
function wp_translation_detect_request_locale() {
        if ( ! wp_translation_environment_is_ready() ) {
                return null;
        }

        $configured = wp_translation_get_configured_locales();

        if ( empty( $configured ) ) {
                return null;
        }

        $mode = wp_translation_get_locale_routing_mode();

        if ( 'domain' === $mode ) {
                $domain  = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
                if ( strpos( $domain, ':' ) !== false ) {
                        $domain = explode( ':', $domain, 2 )[0];
                }
                $mapping = wp_translation_get_domain_mapping();

                if ( isset( $mapping[ $domain ] ) ) {
                        return $mapping[ $domain ];
                }

                return null;
        }

        $path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
        $path = trim( (string) $path, '/' );

        if ( '' === $path ) {
                return null;
        }

        $segments = explode( '/', $path );
        $prefix   = wp_translation_sanitize_locale( reset( $segments ) );

        if ( in_array( $prefix, $configured, true ) ) {
                return $prefix;
        }

        return null;
}

/**
 * Returns a stack of fallback locales.
 *
 * @since 6.6.0
 *
 * @param string|null $requested Requested locale.
 * @return string[] List of locale codes ordered by preference.
 */
function wp_translation_get_locale_fallbacks( $requested = null ) {
        $fallbacks   = array();
        $requested   = $requested ? wp_translation_sanitize_locale( $requested ) : wp_translation_detect_request_locale();
        $configured  = wp_translation_get_configured_locales();
        $default     = wp_translation_get_default_locale();

        if ( $requested ) {
                $fallbacks[] = $requested;
        }

        if ( ! empty( $configured ) ) {
                foreach ( $configured as $locale ) {
                        if ( in_array( $locale, $fallbacks, true ) ) {
                                continue;
                        }

                        $fallbacks[] = $locale;
                }
        }

        if ( ! in_array( $default, $fallbacks, true ) ) {
                $fallbacks[] = $default;
        }

        return $fallbacks;
}

/**
 * Retrieves the translation group for an object.
 *
 * @since 6.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string   $object_type Object type.
 * @param int|null $object_id   Object ID.
 * @return string|null Translation group identifier or null if not assigned.
 */
function wp_get_translation_group( $object_type, $object_id ) {
        global $wpdb;

        $object_type = wp_translation_normalize_object_type( $object_type );
        $object_id   = (int) $object_id;

        if ( $object_id <= 0 ) {
                return null;
        }

        return $wpdb->get_var( $wpdb->prepare( 'SELECT translation_group FROM ' . wp_translation_table() . ' WHERE object_type = %s AND object_id = %d LIMIT 1', $object_type, $object_id ) );
}

/**
 * Retrieves a translation row for a specific locale.
 *
 * @since 6.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string      $object_type Object type.
 * @param int         $object_id   Object ID.
 * @param string|null $locale      Locale code. Defaults to current request locale.
 * @return object|null Database row or null when not found.
 */
function wp_get_translation( $object_type, $object_id, $locale = null ) {
        global $wpdb;

        $object_type = wp_translation_normalize_object_type( $object_type );
        $object_id   = (int) $object_id;
        $locale      = $locale ? wp_translation_sanitize_locale( $locale ) : wp_translation_detect_request_locale();

        if ( $object_id <= 0 || ! $locale ) {
                return null;
        }

        return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . wp_translation_table() . ' WHERE object_type = %s AND object_id = %d AND locale = %s LIMIT 1', $object_type, $object_id, $locale ) );
}

/**
 * Returns all translations that belong to the group of an object.
 *
 * @since 6.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $object_type Object type.
 * @param int    $object_id   Object ID.
 * @return array<int,object> Rows keyed by locale.
 */
function wp_get_translation_group_items( $object_type, $object_id ) {
        global $wpdb;

        $group = wp_get_translation_group( $object_type, $object_id );

        if ( ! $group ) {
                return array();
        }

        $rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . wp_translation_table() . ' WHERE translation_group = %s', $group ) );

        $items = array();
        foreach ( (array) $rows as $row ) {
                $items[ $row->locale ] = $row;
        }

        return $items;
}

/**
 * Assigns an object to a translation group.
 *
 * @since 6.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string      $object_type Object type.
 * @param int         $object_id   Object ID.
 * @param string      $locale      Locale code.
 * @param string|null $group       Optional. Translation group ID. Generated when omitted.
 * @param array       $args        Optional. Additional arguments. Accepts `status` and `synced_meta`.
 * @return string Translation group ID.
 */
function wp_set_translation( $object_type, $object_id, $locale, $group = null, $args = array() ) {
        global $wpdb;

        $object_type = wp_translation_normalize_object_type( $object_type );
        $object_id   = (int) $object_id;
        $locale      = wp_translation_sanitize_locale( $locale );

        if ( $object_id <= 0 || ! $locale ) {
                return $group;
        }

        if ( ! $group ) {
                $group = wp_generate_uuid4();
        }

        $defaults = array(
                'status'      => 'draft',
                'synced_meta' => array(),
        );
        $args     = wp_parse_args( $args, $defaults );

        $data = array(
                'translation_group' => $group,
                'object_type'       => $object_type,
                'object_id'         => $object_id,
                'locale'            => $locale,
                'status'            => sanitize_key( $args['status'] ),
                'synced_meta'       => maybe_serialize( $args['synced_meta'] ),
                'last_synced'       => current_time( 'mysql' ),
        );

        $existing = wp_get_translation( $object_type, $object_id, $locale );

        if ( $existing ) {
                $wpdb->update( wp_translation_table(), $data, array( 'translation_id' => (int) $existing->translation_id ) );
        } else {
                $wpdb->insert( wp_translation_table(), $data );
        }

        return $group;
}

/**
 * Returns translation data structured for REST output.
 *
 * @since 6.6.0
 *
 * @param string $object_type Object type.
 * @param int    $object_id   Object ID.
 * @return array<string,array>
 */
function wp_translation_prepare_rest_data( $object_type, $object_id ) {
        $items      = wp_get_translation_group_items( $object_type, $object_id );
        $configured = wp_translation_get_configured_locales();
        $default    = wp_translation_get_default_locale();

        $response = array();
        foreach ( $items as $locale => $item ) {
                $response[ $locale ] = array(
                        'object_id' => (int) $item->object_id,
                        'status'    => $item->status,
                        'synced'    => $item->last_synced,
                        'synced_meta' => maybe_unserialize( $item->synced_meta ),
                );
        }

        foreach ( $configured as $locale ) {
                if ( isset( $response[ $locale ] ) ) {
                        continue;
                }

                $response[ $locale ] = array(
                        'object_id' => 0,
                        'status'    => 'missing',
                        'synced'    => null,
                );
        }

        if ( $default && ! isset( $response[ $default ] ) ) {
                $response[ $default ] = array(
                        'object_id' => 0,
                        'status'    => 'missing',
                        'synced'    => null,
                );
        }

        ksort( $response );

        return $response;
}

/**
 * Clones a post into a new locale.
 *
 * @since 6.6.0
 *
 * @param int    $post_id      Post ID.
 * @param string $target_locale Locale to clone into.
 * @param array  $args          Optional. Additional arguments.
 * @return int|WP_Error New post ID or error.
 */
function wp_translation_clone_post( $post_id, $target_locale, $args = array() ) {
        $post = get_post( $post_id );

        if ( ! $post ) {
                return new WP_Error( 'translation_missing_source', __( 'Source post could not be found.' ) );
        }

        $target_locale = wp_translation_sanitize_locale( $target_locale );
        $group         = wp_get_translation_group( 'post', $post_id );

        if ( ! $group ) {
                $group = wp_generate_uuid4();
                wp_set_translation( 'post', $post_id, $args['source_locale'] ?? wp_translation_detect_request_locale() ?? wp_translation_get_default_locale(), $group, array( 'status' => 'source' ) );
        }

        $new_postarr = array(
                'post_type'    => $post->post_type,
                'post_status'  => 'draft',
                'post_title'   => $post->post_title,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_parent'  => $post->post_parent,
                'menu_order'   => $post->menu_order,
        );

        if ( ! empty( $args['post_author'] ) ) {
                $new_postarr['post_author'] = (int) $args['post_author'];
        }

        $new_post_id = wp_insert_post( wp_slash( $new_postarr ), true );

        if ( is_wp_error( $new_post_id ) ) {
                return $new_post_id;
        }

        $meta = get_post_meta( $post_id );
        foreach ( $meta as $key => $values ) {
                delete_post_meta( $new_post_id, $key );
                foreach ( $values as $value ) {
                        update_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
                }
        }

        wp_set_translation( 'post', $new_post_id, $target_locale, $group, array( 'status' => 'draft' ) );

        return $new_post_id;
}

/**
 * Attempts to retrieve a fallback translation when one is not available for the requested locale.
 *
 * @since 6.6.0
 *
 * @param string $object_type Object type.
 * @param int    $object_id   Object ID.
 * @param string $locale      Requested locale.
 * @return object|null Translation row or null.
 */
function wp_translation_get_fallback_translation( $object_type, $object_id, $locale ) {
        $group = wp_get_translation_group( $object_type, $object_id );

        if ( ! $group ) {
                return null;
        }

        $items     = wp_get_translation_group_items( $object_type, $object_id );
        $fallbacks = wp_translation_get_locale_fallbacks( $locale );

        foreach ( $fallbacks as $candidate ) {
                if ( isset( $items[ $candidate ] ) ) {
                        return $items[ $candidate ];
                }
        }

        return null;
}

/**
 * Filters nav menu arguments for locale specific menus.
 *
 * @since 6.6.0
 *
 * @param array $args Nav menu arguments.
 * @return array Filtered arguments.
 */
function wp_translation_filter_nav_menu_args( $args ) {
        $menu_id = 0;

        if ( ! empty( $args['menu'] ) ) {
                $menu_id = $args['menu'];
        } elseif ( ! empty( $args['theme_location'] ) ) {
                $locations = get_nav_menu_locations();
                if ( isset( $locations[ $args['theme_location'] ] ) ) {
                        $menu_id = $locations[ $args['theme_location'] ];
                }
        }

        if ( ! $menu_id ) {
                return $args;
        }

        $menu = wp_get_nav_menu_object( $menu_id );

        if ( ! $menu ) {
                return $args;
        }

        $locale = determine_locale();
        $row    = wp_translation_get_fallback_translation( 'term', $menu->term_id, $locale );

        if ( $row && (int) $row->object_id !== (int) $menu->term_id ) {
                $args['menu'] = (int) $row->object_id;
        }

        return $args;
}
add_filter( 'wp_nav_menu_args', 'wp_translation_filter_nav_menu_args' );

/**
 * Adjusts widgets that contain nav menus to use locale aware menus.
 *
 * @since 6.6.0
 *
 * @param array     $instance Settings for the current widget instance.
 * @param WP_Widget $widget   Current widget.
 * @param array     $args     Display arguments.
 * @return array|null Null to abort display, or settings array.
 */
function wp_translation_filter_widget_display( $instance, $widget, $args ) {
        if ( ! $instance ) {
                return $instance;
        }

        if ( 'WP_Nav_Menu_Widget' !== get_class( $widget ) || empty( $instance['nav_menu'] ) ) {
                return $instance;
        }

        $menu = wp_get_nav_menu_object( $instance['nav_menu'] );

        if ( ! $menu ) {
                return $instance;
        }

        $locale = determine_locale();
        $row    = wp_translation_get_fallback_translation( 'term', $menu->term_id, $locale );

        if ( $row && (int) $row->object_id !== (int) $menu->term_id ) {
                $instance['nav_menu'] = (int) $row->object_id;
        }

        return $instance;
}
add_filter( 'widget_display_callback', 'wp_translation_filter_widget_display', 10, 3 );

/**
 * Registers REST fields exposing translation information.
 *
 * @since 6.6.0
 *
 * @return void
 */
function wp_translation_register_rest_fields() {
        $post_types = get_post_types( array( 'show_in_rest' => true ) );

        foreach ( $post_types as $post_type ) {
                register_rest_field(
                        $post_type,
                        'translations',
                        array(
                                'get_callback'    => function( $object ) use ( $post_type ) {
                                        return wp_translation_prepare_rest_data( 'post', $object['id'] );
                                },
                                'update_callback' => function( $value, $object ) use ( $post_type ) {
                                        if ( ! is_array( $value ) ) {
                                                return;
                                        }

                                        foreach ( $value as $locale => $data ) {
                                                if ( empty( $data['object_id'] ) ) {
                                                        continue;
                                                }

                                                $status = isset( $data['status'] ) ? $data['status'] : 'draft';
                                                $meta   = isset( $data['synced_meta'] ) ? $data['synced_meta'] : array();

                                                wp_set_translation(
                                                        'post',
                                                        (int) $data['object_id'],
                                                        $locale,
                                                        null,
                                                        array(
                                                                'status'      => $status,
                                                                'synced_meta' => $meta,
                                                        )
                                                );
                                        }
                                },
                                'schema'          => array(
                                        'type'       => 'object',
                                        'context'    => array( 'view', 'edit' ),
                                        'properties' => array(),
                                ),
                        )
                );
        }

        foreach ( get_taxonomies( array( 'show_in_rest' => true ) ) as $taxonomy ) {
                register_rest_field(
                        $taxonomy,
                        'translations',
                        array(
                                'get_callback' => function( $object ) use ( $taxonomy ) {
                                        return wp_translation_prepare_rest_data( 'term', $object['id'] );
                                },
                                'schema'       => array(
                                        'type'    => 'object',
                                        'context' => array( 'view', 'edit' ),
                                ),
                        )
                );
        }
}
add_action( 'rest_api_init', 'wp_translation_register_rest_fields' );

/**
 * Injects translation data for the block editor.
 *
 * @since 6.6.0
 *
 * @param WP_Post $post Post being edited.
 * @return array
 */
function wp_translation_get_block_editor_settings( $post ) {
        if ( ! $post ) {
                return array();
        }

        $locales      = wp_translation_get_configured_locales();
        $translations = wp_translation_prepare_rest_data( 'post', $post->ID );

        return array(
                'availableLocales' => array_values( $locales ),
                'defaultLocale'    => wp_translation_get_default_locale(),
                'activeLocale'     => determine_locale(),
                'translations'     => $translations,
                'strings'          => array(
                        'statusPrefix' => __( 'Status:', 'default' ),
                ),
        );
}

/**
 * Renders the markup for the block editor language switcher.
 *
 * @since 6.6.0
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function wp_translation_render_block_editor_language_switcher( $post ) {
        if ( ! $post ) {
                return;
        }

        $locales = wp_translation_get_configured_locales();

        if ( empty( $locales ) ) {
                return;
        }

        $translations = wp_translation_prepare_rest_data( 'post', $post->ID );
        $active       = determine_locale();

        echo '<div class="wp-translation-language-switcher">';
        echo '<label for="wp-translation-language-select" class="screen-reader-text">' . esc_html__( 'Switch language', 'default' ) . '</label>';
        echo '<select id="wp-translation-language-select" class="wp-translation-language-select" data-post-id="' . esc_attr( $post->ID ) . '">';

        foreach ( $locales as $locale ) {
                $status   = isset( $translations[ $locale ] ) ? $translations[ $locale ]['status'] : 'missing';
                $selected = selected( $locale, $active, false );

                printf(
                        '<option value="%1$s" %3$s data-status="%4$s">%2$s</option>',
                        esc_attr( $locale ),
                        esc_html( strtoupper( $locale ) ),
                        $selected,
                        esc_attr( $status )
                );
        }

        echo '</select>';

        $nonce = wp_create_nonce( 'clone-post-' . $post->ID );
        echo '<button type="button" class="button button-secondary wp-translation-clone" data-post-id="' . esc_attr( $post->ID ) . '" data-nonce="' . esc_attr( $nonce ) . '">' . esc_html__( 'Clone to locale', 'default' ) . '</button>';
        echo '<span class="description wp-translation-status" aria-live="polite"></span>';
        echo '</div>';
}

/**
 * Filters permalinks to inject locale prefixes or domain routing.
 *
 * @since 6.6.0
 *
 * @param string   $permalink Generated permalink.
 * @param WP_Post  $post      Post object.
 * @param string[] $leavename Optional. Leave name.
 * @return string Filtered permalink.
 */
function wp_translation_filter_post_link( $permalink, $post, $leavename = false, $sample = false ) {
        $locale = wp_translation_detect_request_locale();

        if ( ! $locale ) {
                $row = wp_translation_get_fallback_translation( 'post', $post->ID, determine_locale() );
                if ( $row ) {
                        $locale = $row->locale;
                }
        }

        return wp_translation_apply_locale_to_url( $permalink, $locale );
}
add_filter( 'post_link', 'wp_translation_filter_post_link', 10, 3 );
add_filter( 'page_link', 'wp_translation_filter_post_link', 10, 4 );
add_filter( 'post_type_link', 'wp_translation_filter_post_link', 10, 3 );

/**
 * Filters term links to include locale specific routing.
 *
 * @since 6.6.0
 *
 * @param string  $termlink Term link.
 * @param WP_Term $term     Term object.
 * @param string  $taxonomy Taxonomy slug.
 * @return string Filtered link.
 */
function wp_translation_filter_term_link( $termlink, $term, $taxonomy ) {
        $locale = wp_translation_detect_request_locale();

        if ( ! $locale ) {
                $row = wp_translation_get_fallback_translation( 'term', $term->term_id, determine_locale() );
                if ( $row ) {
                        $locale = $row->locale;
                }
        }

        return wp_translation_apply_locale_to_url( $termlink, $locale );
}
add_filter( 'term_link', 'wp_translation_filter_term_link', 10, 3 );

/**
 * Applies locale routing to a URL.
 *
 * @since 6.6.0
 *
 * @param string      $url    Original URL.
 * @param string|null $locale Locale code.
 * @return string Filtered URL.
 */
function wp_translation_apply_locale_to_url( $url, $locale ) {
        $locale = $locale ? wp_translation_sanitize_locale( $locale ) : '';

        if ( ! $locale ) {
                return $url;
        }

        $mode = wp_translation_get_locale_routing_mode();

        if ( 'domain' === $mode ) {
                $mapping = array_flip( wp_translation_get_domain_mapping() );

                if ( isset( $mapping[ $locale ] ) ) {
                        $host  = $mapping[ $locale ];
                        $parts = wp_parse_url( $url );
                        $scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : ( is_ssl() ? 'https' : 'http' );
                        $path   = isset( $parts['path'] ) ? $parts['path'] : '/';
                        $query  = isset( $parts['query'] ) ? '?' . $parts['query'] : '';
                        $frag   = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';

                        return $scheme . '://' . $host . $path . $query . $frag;
                }

                return $url;
        }

        $home_url = trailingslashit( home_url() );

        if ( 0 === strpos( $url, $home_url ) ) {
                $path = substr( $url, strlen( $home_url ) );
                $path = ltrim( $path, '/' );

                return trailingslashit( $home_url . $locale ) . $path;
        }

        return $url;
}

/**
 * Captures request locale early in the request lifecycle.
 *
 * @since 6.6.0
 *
 * @param WP $wp Main WP instance.
 * @return void
 */
function wp_translation_parse_request_locale( $wp ) {
        $locale = wp_translation_detect_request_locale();

        if ( ! $locale ) {
                return;
        }

        wp_translation_set_request_locale( $locale );

        if ( isset( $wp->query_vars['translation_path'] ) ) {
                $path        = trim( (string) $wp->query_vars['translation_path'], '/' );
                $wp->request = $path;

                if ( '' !== $path ) {
                        if ( empty( $wp->query_vars['pagename'] ) ) {
                                $wp->query_vars['pagename'] = $path;
                        }

                        if ( empty( $wp->query_vars['name'] ) && false === strpos( $path, '/' ) ) {
                                $wp->query_vars['name'] = $path;
                        }
                }
        }

        if ( 'path' === wp_translation_get_locale_routing_mode() ) {
                $path = trim( $wp->request, '/' );
                if ( 0 === strpos( $path, $locale . '/' ) ) {
                        $new_path = substr( $path, strlen( $locale . '/' ) );
                        $wp->request = $new_path;
                } elseif ( $path === $locale ) {
                        $wp->request = '';
                }
        }

        $wp->set_query_var( 'locale', $locale );
        $wp->query_vars['locale'] = $locale;
}
add_action( 'parse_request', 'wp_translation_parse_request_locale', 1 );

/**
 * Uses the detected locale for determine_locale() when available.
 *
 * @since 6.6.0
 *
 * @param string|null $locale Short-circuit locale value.
 * @return string|null Filtered locale.
 */
function wp_translation_pre_determine_locale( $locale ) {
        if ( $locale ) {
                return $locale;
        }

        $detected = wp_translation_get_request_locale();

        if ( $detected ) {
                return $detected;
        }

        return null;
}
add_filter( 'pre_determine_locale', 'wp_translation_pre_determine_locale' );

/**
 * Registers translation synchronisation hooks for post updates.
 *
 * @since 6.6.0
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function wp_translation_track_post_update( $post_id, $post ) {
        $group = wp_get_translation_group( 'post', $post_id );

        if ( ! $group ) {
                return;
        }

        wp_set_translation( 'post', $post_id, determine_locale(), $group, array( 'status' => $post->post_status ) );
}
add_action( 'save_post', 'wp_translation_track_post_update', 10, 2 );

/**
 * Provides translation data to classic meta box contexts.
 *
 * @since 6.6.0
 *
 * @param WP_Post $post Post being edited.
 * @return void
 */
function wp_translation_render_status_meta_box( $post ) {
        $translations = wp_translation_prepare_rest_data( 'post', $post->ID );

        echo '<p>' . esc_html__( 'Translation status across locales:', 'default' ) . '</p>';
        echo '<ul class="wp-translation-status-list">';
        foreach ( $translations as $locale => $data ) {
                printf(
                        '<li><strong>%1$s:</strong> %2$s</li>',
                        esc_html( strtoupper( $locale ) ),
                        esc_html( $data['status'] )
                );
        }
        echo '</ul>';
}

/**
 * Registers the translation status meta box for block editor screens.
 *
 * @since 6.6.0
 *
 * @return void
 */
function wp_translation_register_status_meta_box() {
        $screen = get_current_screen();

        if ( ! $screen || ! post_type_supports( $screen->id, 'editor' ) ) {
                return;
        }

        add_meta_box( 'translation-status', __( 'Translations', 'default' ), 'wp_translation_render_status_meta_box', null, 'side' );
}
add_action( 'add_meta_boxes', 'wp_translation_register_status_meta_box' );

/**
 * Allows cloning posts into other locales through the admin action.
 *
 * @since 6.6.0
 *
 * @return void
 */
function wp_translation_handle_admin_clone_request() {
        if ( ! is_admin() || empty( $_GET['clone_post_to_locale'] ) || empty( $_GET['post'] ) ) {
                return;
        }

        if ( ! current_user_can( 'edit_post', (int) $_GET['post'] ) ) {
                return;
        }

        check_admin_referer( 'clone-post-' . (int) $_GET['post'] );

        $target_locale = wp_translation_sanitize_locale( wp_unslash( $_GET['clone_post_to_locale'] ) );
        $result        = wp_translation_clone_post( (int) $_GET['post'], $target_locale );

        if ( is_wp_error( $result ) ) {
                wp_die( $result );
        }

        wp_safe_redirect( get_edit_post_link( $result, 'url' ) );
        exit;
}
add_action( 'admin_init', 'wp_translation_handle_admin_clone_request' );

/**
 * Flush rewrite rules when locale routing options change.
 */
function wp_translation_maybe_flush_rewrite_rules( $old = null, $new = null, $option = null ) {
        if ( function_exists( 'flush_rewrite_rules' ) ) {
                flush_rewrite_rules();
        }
}
add_action( 'update_option_translation_locale_mode', 'wp_translation_maybe_flush_rewrite_rules', 10, 3 );
add_action( 'update_option_translation_available_locales', 'wp_translation_maybe_flush_rewrite_rules', 10, 3 );
add_action( 'update_option_translation_domain_mapping', 'wp_translation_maybe_flush_rewrite_rules', 10, 3 );
