<?php
/**
 * Layout canvas integration for the block editor.
 *
 * @package WordPress
 * @subpackage Editor
 * @since 6.7.0
 */

if ( ! class_exists( 'WP_Layout_Canvas' ) ) {
        /**
         * Coordinates the Layout Designer canvas experience.
         */
        class WP_Layout_Canvas {
                /**
                 * Singleton instance.
                 *
                 * @var WP_Layout_Canvas|null
                 */
                protected static $instance = null;

                /**
                 * Collected responsive CSS rules keyed by breakpoint label.
                 *
                 * @var array
                 */
                protected $css_rules = array();

                /**
                 * Bootstraps the singleton.
                 *
                 * @return WP_Layout_Canvas
                 */
                public static function instance() {
                        if ( null === static::$instance ) {
                                static::$instance = new static();
                                static::$instance->register_hooks();
                        }

                        return static::$instance;
                }

                /**
                 * Registers core hooks used by the Layout Designer mode.
                 */
                public function register_hooks() {
                        add_action( 'init', array( $this, 'register_meta' ) );
                        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
                        add_filter( 'block_type_metadata', array( $this, 'inject_layout_attribute' ) );
                        add_filter( 'render_block', array( $this, 'filter_rendered_block' ), 10, 2 );
                        add_action( 'wp_footer', array( $this, 'output_collected_css' ) );
                        add_action( 'admin_footer', array( $this, 'output_collected_css' ) );
                }

                /**
                 * Registers the meta container used to persist canvas level options.
                 */
                public function register_meta() {
                        register_meta(
                                'post',
                                'wp_layout_canvas',
                                array(
                                        'single'        => true,
                                        'type'          => 'object',
                                        'default'       => array(),
                                        'auth_callback' => function() {
                                                return current_user_can( 'edit_posts' );
                                        },
                                        'show_in_rest'  => array(
                                                'schema' => array(
                                                        'type'                 => 'object',
                                                        'additionalProperties' => true,
                                                ),
                                        ),
                                )
                        );
                }

                /**
                 * Ensures every block understands the optional layoutCanvas attribute and support flags.
                 *
                 * @param array $metadata Original block metadata.
                 * @return array
                 */
                public function inject_layout_attribute( $metadata ) {
                        if ( empty( $metadata['attributes'] ) ) {
                                $metadata['attributes'] = array();
                        }

                        if ( empty( $metadata['attributes']['layoutCanvas'] ) ) {
                                $metadata['attributes']['layoutCanvas'] = array(
                                        'type'       => 'object',
                                        'default'    => null,
                                        'properties' => array(
                                                'x'           => array( 'type' => 'number' ),
                                                'y'           => array( 'type' => 'number' ),
                                                'width'       => array( 'type' => array( 'number', 'string' ) ),
                                                'height'      => array( 'type' => array( 'number', 'string' ) ),
                                                'zIndex'      => array( 'type' => 'integer' ),
                                                'breakpoints' => array( 'type' => 'object' ),
                                                'constraints' => array( 'type' => 'object' ),
                                                'handles'     => array( 'type' => 'array' ),
                                        ),
                                );
                        }

                        if ( empty( $metadata['attributes']['layoutId'] ) ) {
                                $metadata['attributes']['layoutId'] = array(
                                        'type'    => 'string',
                                        'default' => '',
                                );
                        }

                        if ( isset( $metadata['layoutHandles'] ) || isset( $metadata['layoutConstraints'] ) || isset( $metadata['layoutBreakpoints'] ) ) {
                                if ( empty( $metadata['supports'] ) ) {
                                        $metadata['supports'] = array();
                                }
                                $metadata['supports']['layoutCanvas'] = array_filter(
                                        array(
                                                'handles'     => isset( $metadata['layoutHandles'] ) ? $metadata['layoutHandles'] : array(),
                                                'constraints' => isset( $metadata['layoutConstraints'] ) ? $metadata['layoutConstraints'] : array(),
                                                'breakpoints' => isset( $metadata['layoutBreakpoints'] ) ? $metadata['layoutBreakpoints'] : array(),
                                        )
                                );
                        }

                        return $metadata;
                }

                /**
                 * Registers scripts and styles for the editor canvas experience.
                 */
                public function enqueue_editor_assets() {
                        $handle    = 'wp-layout-canvas';
                        $script    = ABSPATH . 'wp-admin/js/layout-canvas.js';
                        $style     = ABSPATH . 'wp-admin/css/layout-canvas.css';
                        $version   = file_exists( $script ) ? filemtime( $script ) : false;
                        $deps      = array(
                                'wp-block-editor',
                                'wp-components',
                                'wp-compose',
                                'wp-data',
                                'wp-edit-post',
                                'wp-element',
                                'wp-hooks',
                                'wp-i18n',
                                'wp-keyboard-shortcuts',
                                'wp-notices',
                                'wp-plugins',
                        );

                        wp_register_script(
                                $handle,
                                admin_url( 'js/layout-canvas.js' ),
                                $deps,
                                $version,
                                true
                        );

                        wp_set_script_translations( $handle, 'default' );

                        wp_enqueue_script( $handle );

                        if ( file_exists( $style ) ) {
                                wp_enqueue_style(
                                        $handle,
                                        admin_url( 'css/layout-canvas.css' ),
                                        array( 'wp-components' ),
                                        filemtime( $style )
                                );
                        }

                        wp_localize_script(
                                $handle,
                                'wpLayoutCanvasSettings',
                                array(
                                        'metaKey'        => 'wp_layout_canvas',
                                        'gridDefaults'   => array(
                                                'snap'     => true,
                                                'size'     => 10,
                                                'visible'  => true,
                                        ),
                                        'nonces'         => array(
                                                'wp_rest' => wp_create_nonce( 'wp_rest' ),
                                        ),
                                        'breakpoints'    => array( 'desktop', 'tablet', 'mobile' ),
                                        'onboardingFlag' => 'layout_canvas_onboarding_seen',
                                )
                        );
                }

                /**
                 * Filters rendered block markup to append wrappers and collect responsive CSS.
                 *
                 * @param string   $block_content The rendered block markup.
                 * @param WP_Block $block         Parsed block instance.
                 * @return string
                 */
                public function filter_rendered_block( $block_content, $block ) {
                        if ( empty( $block['attrs']['layoutCanvas'] ) || ! is_array( $block['attrs']['layoutCanvas'] ) ) {
                                return $block_content;
                        }

                        $layout = $block['attrs']['layoutCanvas'];
                        $inline     = $this->generate_inline_style( $layout );
                        $identifier = $this->resolve_identifier( $block );
                        $css        = $this->generate_responsive_css( $identifier, $layout );

                        if ( $css ) {
                                $this->css_rules[] = $css;
                        }

                        $wrapper_attributes = sprintf(
                                'class="wp-layout-canvas-item" data-layout-block="%s" style="%s"',
                                esc_attr( $identifier ),
                                esc_attr( $inline )
                        );

                        return sprintf( '<div %1$s>%2$s</div>', $wrapper_attributes, $block_content );
                }

                /**
                 * Turns collected CSS snippets into a single style tag printed once per request.
                 */
                public function output_collected_css() {
                        if ( empty( $this->css_rules ) ) {
                                return;
                        }

                        $css = implode( '\n', array_unique( $this->css_rules ) );
                        printf( "<style class='wp-layout-canvas-css'>%s</style>", wp_strip_all_tags( $css ) );
                        $this->css_rules = array();
                }

                /**
                 * Builds the inline CSS for the wrapper element.
                 *
                 * @param array $layout Layout description.
                 * @return string
                 */
                protected function generate_inline_style( $layout ) {
                        $defaults = array(
                                'x'      => 0,
                                'y'      => 0,
                                'width'  => 'auto',
                                'height' => 'auto',
                        );
                        $layout   = wp_parse_args( $layout, $defaults );

                        $style = array( 'display:block' );

                        if ( isset( $layout['absolute'] ) && $layout['absolute'] ) {
                                $style[] = 'position:absolute';
                                $style[] = 'top:' . $this->format_dimension( $layout['y'] );
                                $style[] = 'left:' . $this->format_dimension( $layout['x'] );
                        } else {
                                $style[] = 'position:relative';
                        }

                        if ( isset( $layout['width'] ) && '' !== $layout['width'] ) {
                                $style[] = 'width:' . $this->format_dimension( $layout['width'], 'auto' );
                        }

                        if ( isset( $layout['height'] ) && '' !== $layout['height'] ) {
                                $style[] = 'height:' . $this->format_dimension( $layout['height'], 'auto' );
                        }

                        if ( isset( $layout['zIndex'] ) ) {
                                $style[] = 'z-index:' . (int) $layout['zIndex'];
                        }

                        return implode( ';', array_filter( $style ) );
                }

                /**
                 * Generates responsive CSS rules for the provided layout definition.
                 *
                 * @param array $block  Parsed block.
                 * @param array $layout Layout attributes.
                 * @return string
                 */
                protected function generate_responsive_css( $identifier, $layout ) {
                        if ( empty( $layout['breakpoints'] ) || ! is_array( $layout['breakpoints'] ) ) {
                                return '';
                        }

                        $selector = '.wp-layout-canvas-item[data-layout-block="' . $identifier . '"]';
                        $rules    = array();

                        foreach ( $layout['breakpoints'] as $breakpoint => $values ) {
                                if ( empty( $values ) || ! is_array( $values ) ) {
                                        continue;
                                }

                                $prefix = $this->breakpoint_to_media_query( $breakpoint );
                                $styles = array();

                                foreach ( array( 'top' => 'y', 'left' => 'x', 'width' => 'width', 'height' => 'height', 'z-index' => 'zIndex' ) as $css_prop => $layout_key ) {
                                        if ( isset( $values[ $layout_key ] ) ) {
                                                $styles[] = $css_prop . ':' . $this->format_dimension( $values[ $layout_key ] );
                                        }
                                }

                                if ( empty( $styles ) ) {
                                        continue;
                                }

                                $rule = sprintf( '%1$s {%2$s %3$s {%4$s}}',
                                        $prefix,
                                        PHP_EOL,
                                        $selector,
                                        implode( ';', $styles )
                                );

                                $rules[] = $rule;
                        }

                        return implode( PHP_EOL, $rules );
                }

                /**
                 * Converts a breakpoint keyword to a media query wrapper.
                 *
                 * @param string $breakpoint Breakpoint label.
                 * @return string
                 */
                protected function breakpoint_to_media_query( $breakpoint ) {
                        switch ( $breakpoint ) {
                                case 'mobile':
                                        return '@media (max-width: 600px)';
                                case 'tablet':
                                        return '@media (max-width: 960px)';
                                case 'desktop':
                                default:
                                        return '@media (min-width: 961px)';
                        }
                }

                /**
                 * Formats dimensions supporting numeric pixel values or CSS units.
                 *
                 * @param mixed  $value   Value to format.
                 * @param string $default Default fallback.
                 * @return string
                 */
                protected function format_dimension( $value, $default = '0px' ) {
                        if ( null === $value || '' === $value ) {
                                return $default;
                        }

                        if ( is_numeric( $value ) ) {
                                return $value . 'px';
                        }

                        return $value;
                }

                /**
                 * Resolves a unique identifier for the rendered block.
                 *
                 * @param array $block Block being rendered.
                 * @return string
                 */
                protected function resolve_identifier( $block ) {
                        if ( ! empty( $block['attrs']['layoutId'] ) ) {
                                return sanitize_title_with_dashes( $block['attrs']['layoutId'] );
                        }

                        if ( ! empty( $block['attrs']['ref'] ) ) {
                                return sanitize_title_with_dashes( $block['attrs']['ref'] );
                        }

                        if ( ! empty( $block['blockName'] ) ) {
                                return sanitize_title_with_dashes( $block['blockName'] );
                        }

                        return uniqid( 'layout-canvas-' );
                }
        }
}

// Bootstrap the Layout Canvas singleton immediately after defining the class.
WP_Layout_Canvas::instance();
