<?php
/**
 * Handles autoptimizeExtra frontend features + admin options page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeExtra
{
    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    protected static $instance = null;

    /**
     * Creates an instance and calls run().
     *
     * @param array $options Optional. Allows overriding options without having to specify them via admin options page.
     */
    public function __construct( $options = array() )
    {
        if ( empty( $options ) ) {
            $options = self::fetch_options();
        }

        $this->options = $options;
    }

    /**
     * Helper for getting a singleton instance. While being an
     * anti-pattern generally, it comes in handy for now from a
     * readability/maintainability perspective, until we get some
     * proper dependency injection going.
     *
     * @return self
     */
    public static function instance()
    {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run()
    {
        if ( is_admin() ) {
            if ( is_multisite() && is_network_admin() && autoptimizeOptionWrapper::is_ao_active_for_network() ) {
                add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
            } else {
                add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            }
            add_filter( 'autoptimize_filter_settingsscreen_tabs', array( $this, 'add_extra_tab' ) );
        } else {
            add_action( 'wp', array( $this, 'run_on_frontend' ) );
        }
    }

    public function set_options( array $options )
    {
        $this->options = $options;

        return $this;
    }

    public static function fetch_options()
    {
        $value = autoptimizeOptionWrapper::get_option( 'autoptimize_extra_settings' );
        if ( empty( $value ) ) {
            // Fallback to returning defaults when no stored option exists yet.
            $value = autoptimizeConfig::get_ao_extra_default_options();
        }

        return $value;
    }

    public function disable_emojis()
    {
        // Removing all actions related to emojis!
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

        // Removes TinyMCE emojis.
        add_filter( 'tiny_mce_plugins', array( $this, 'filter_disable_emojis_tinymce' ) );

        // Removes emoji dns-preftech.
        add_filter( 'emoji_svg_url', '__return_false' );
    }

    public function filter_disable_emojis_tinymce( $plugins )
    {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        } else {
            return array();
        }
    }

    public function filter_remove_qs( $src )
    {
        if ( strpos( $src, '?ver=' ) ) {
            $src = remove_query_arg( 'ver', $src );
        } elseif ( strpos( $src, '?v=' ) ) {
            $src = remove_query_arg( 'v', $src );
        }

        return $src;
    }

    public function extra_async_js( $in )
    {
        $exclusions = array();
        if ( ! empty( $in ) ) {
            $exclusions = array_fill_keys( array_filter( array_map( 'trim', explode( ',', $in ) ) ), '' );
        }

        $settings = wp_strip_all_tags( $this->options['autoptimize_extra_text_field_3'] );
        $async    = array_fill_keys( array_filter( array_map( 'trim', explode( ',', $settings ) ) ), '' );
        $attr     = apply_filters( 'autoptimize_filter_extra_async', 'async' );
        foreach ( $async as $k => $v ) {
            $async[ $k ] = $attr;
        }

        // Merge exclusions & asyncs in one array and return to AO API.
        $merged = array_merge( $exclusions, $async );

        return $merged;
    }

    public function run_on_frontend()
    {
        // only run the Extra optimizations on frontend if general conditions
        // for optimizations are met, this to ensure e.g. removing querystrings
        // is not done when optimizing for logged in users is off, breaking
        // some pagebuilders (Divi & Elementor).
        if ( false === autoptimizeMain::should_buffer() ) {
            return;
        }

        $options = $this->options;

        // Disable emojis if specified.
        if ( ! empty( $options['autoptimize_extra_checkbox_field_1'] ) ) {
            $this->disable_emojis();
        }

        // Remove version query parameters.
        if ( ! empty( $options['autoptimize_extra_checkbox_field_0'] ) ) {
            add_filter( 'script_loader_src', array( $this, 'filter_remove_qs' ), 15, 1 );
            add_filter( 'style_loader_src', array( $this, 'filter_remove_qs' ), 15, 1 );
        }

        // Avoiding conflicts of interest when async-javascript plugin is active!
        $async_js_plugin_active = autoptimizeUtils::is_plugin_active( 'async-javascript/async-javascript.php' );
        if ( ! empty( $options['autoptimize_extra_text_field_3'] ) && ! $async_js_plugin_active ) {
            add_filter( 'autoptimize_filter_js_exclude', array( $this, 'extra_async_js' ), 10, 1 );
        }

        // Optimize google fonts!
        if ( ! empty( $options['autoptimize_extra_radio_field_4'] ) && ( '1' !== $options['autoptimize_extra_radio_field_4'] ) ) {
            add_filter( 'wp_resource_hints', array( $this, 'filter_remove_gfonts_dnsprefetch' ), 10, 2 );
            add_filter( 'autoptimize_html_after_minify', array( $this, 'filter_optimize_google_fonts' ), 10, 1 );
            add_filter( 'autoptimize_extra_filter_tobepreconn', array( $this, 'filter_preconnect_google_fonts' ), 10, 1 );

            if ( '2' === $options['autoptimize_extra_radio_field_4'] ) {
                // remove Google Fonts, adding filters to also remove Google Fonts from 3rd party themes/ plugins.
                // inspired by https://wordpress.org/plugins/disable-remove-google-fonts/.
                remove_action( 'wp_footer', 'et_builder_print_font' ); // Divi.
                remove_action( 'wp_footer', array( 'RevSliderFront', 'load_google_fonts' ) ); // Revslider.
                add_filter( 'elementor/frontend/print_google_fonts', '__return_false' ); // Elementor.
                add_filter( 'fl_builder_google_fonts_pre_enqueue', '__return_empty_array' ); // Beaver Builder.
            }
        }

        // Preconnect!
        if ( ! empty( $options['autoptimize_extra_text_field_2'] ) || has_filter( 'autoptimize_extra_filter_tobepreconn' ) ) {
            add_filter( 'wp_resource_hints', array( $this, 'filter_preconnect' ), 10, 2 );
        }

        // Preload!
        if ( ! empty( $options['autoptimize_extra_text_field_7'] ) || has_filter( 'autoptimize_filter_extra_tobepreloaded' ) || ! empty( autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_preload' ) ) ) {
            add_filter( 'autoptimize_html_after_minify', array( $this, 'filter_preload' ), 10, 2 );
        }

        // Remove global styles.
        if ( ! empty( $options['autoptimize_extra_checkbox_field_8'] ) ) {
            $this->disable_global_styles();
        }
    }

    public function filter_remove_gfonts_dnsprefetch( $urls, $relation_type )
    {
        return $this->filter_remove_dns_prefetch( $urls, $relation_type, 'fonts.googleapis.com' );
    }

    public function filter_remove_dns_prefetch( $urls, $relation_type, $url_to_remove )
    {
        $url_to_remove = (string) $url_to_remove;

        if ( ! empty( $url_to_remove ) && 'dns-prefetch' === $relation_type ) {
            $cnt = 0;
            foreach ( $urls as $url ) {
                if ( false !== strpos( $url, $url_to_remove ) ) {
                    unset( $urls[ $cnt ] );
                }
                $cnt++;
            }
        }

        return $urls;
    }

    public function filter_optimize_google_fonts( $in )
    {
        // Extract fonts, partly based on wp rocket's extraction code.
        $markup = preg_replace( '/<!--(.*)-->/Uis', '', $in );
        preg_match_all( '#<link(?:\s+(?:(?!href\s*=\s*)[^>])+)?(?:\s+href\s*=\s*([\'"])((?:https?:)?\/\/fonts\.googleapis\.com\/css(?:(?!\1).)+)\1)(?:\s+[^>]*)?>#iU', $markup, $matches );

        $fonts_collection = array();
        if ( ! $matches[2] ) {
            return $in;
        }

        // Store them in $fonts array.
        $i = 0;
        foreach ( $matches[2] as $font ) {
            if ( ! preg_match( '/rel=["\']dns-prefetch["\']/', $matches[0][ $i ] ) ) {
                // Get fonts name.
                $font = str_replace( array( '%7C', '%7c' ), '|', $font );
                if ( strpos( $font, 'fonts.googleapis.com/css2' ) !== false ) {
                    // (Somewhat) change Google Fonts APIv2 syntax back to v1.
                    // todo: support for 100..900
                    $font = rawurldecode( $font );
                    $font = str_replace( array( 'css2?', 'ital,wght@', 'wght@', 'ital@', '0,', '1,', ':1', ';', '&family=' ), array( 'css?', '', '', '', '', 'italic', ':italic', ',', '%7C' ), $font );
                }
                $font = explode( 'family=', $font );
                $font = ( isset( $font[1] ) ) ? explode( '&', $font[1] ) : array();
                // Add font to $fonts[$i] but make sure not to pollute with an empty family!
                $_thisfont = array_values( array_filter( explode( '|', reset( $font ) ) ) );
                if ( ! empty( $_thisfont ) ) {
                    $fonts_collection[ $i ]['fonts'] = $_thisfont;
                    // And add subset if any!
                    $subset = ( is_array( $font ) ) ? end( $font ) : '';
                    if ( false !== strpos( $subset, 'subset=' ) ) {
                        $subset                            = str_replace( array( '%2C', '%2c' ), ',', $subset );
                        $subset                            = explode( 'subset=', $subset );
                        $fonts_collection[ $i ]['subsets'] = explode( ',', $subset[1] );
                    }
                }
                // And remove Google Fonts.
                $in = str_replace( $matches[0][ $i ], '', $in );
            }
            $i++;
        }

        $options      = $this->options;
        $fonts_markup = '';
        if ( '2' === $options['autoptimize_extra_radio_field_4'] ) {
            // Remove Google Fonts.
            unset( $fonts_collection );
            return $in;
        } elseif ( '3' === $options['autoptimize_extra_radio_field_4'] || '5' === $options['autoptimize_extra_radio_field_4'] ) {
            // Aggregate & link!
            $fonts_string  = '';
            $subset_string = '';
            foreach ( $fonts_collection as $font ) {
                $fonts_string .= '|' . trim( implode( '|', $font['fonts'] ), '|' );
                if ( ! empty( $font['subsets'] ) ) {
                    $subset_string .= ',' . trim( implode( ',', $font['subsets'] ), ',' );
                }
            }

            if ( ! empty( $subset_string ) ) {
                $subset_string = str_replace( ',', '%2C', ltrim( $subset_string, ',' ) );
                $fonts_string  = $fonts_string . '&#038;subset=' . $subset_string;
            }

            $fonts_string = apply_filters( 'autoptimize_filter_extra_gfont_fontstring', str_replace( '|', '%7C', ltrim( $fonts_string, '|' ) ) );
            // only add display parameter if there is none in $fonts_string (by virtue of the filter).
            if ( strpos( $fonts_string, 'display=' ) === false ) {
                $fonts_string .= apply_filters( 'autoptimize_filter_extra_gfont_display', '&amp;display=swap' );
            }

            if ( ! empty( $fonts_string ) ) {
                if ( '5' === $options['autoptimize_extra_radio_field_4'] ) {
                    $rel_string = 'rel="stylesheet" media="print" onload="' . autoptimizeConfig::get_ao_css_preload_onload() . '"';
                } else {
                    $rel_string = 'rel="stylesheet"';
                }
                $fonts_markup = '<link ' . $rel_string . ' id="ao_optimized_gfonts" href="https://fonts.googleapis.com/css?family=' . $fonts_string . '">';
            }
        } elseif ( '4' === $options['autoptimize_extra_radio_field_4'] ) {
            // Aggregate & load async (webfont.js impl.)!
            $fonts_array = array();
            foreach ( $fonts_collection as $_fonts ) {
                if ( ! empty( $_fonts['subsets'] ) ) {
                    $_subset = implode( ',', $_fonts['subsets'] );
                    foreach ( $_fonts['fonts'] as $key => $_one_font ) {
                        $_one_font               = $_one_font . ':' . $_subset;
                        $_fonts['fonts'][ $key ] = $_one_font;
                    }
                }
                $fonts_array = array_merge( $fonts_array, $_fonts['fonts'] );
            }

            $fonts_array = array_map( 'urldecode', $fonts_array );
            $fonts_array = array_map(
                function( $_f ) {
                    return trim( $_f, ',' );
                },
                $fonts_array
            );

            // type attrib on <script not added by default.
            $type_js = '';
            if ( apply_filters( 'autoptimize_filter_cssjs_addtype', false ) ) {
                $type_js = 'type="text/javascript" ';
            }

            $fonts_markup         = '<script ' . $type_js . 'data-cfasync="false" id="ao_optimized_gfonts_config">WebFontConfig={google:{families:' . wp_json_encode( $fonts_array ) . ' },classes:false, events:false, timeout:1500};</script>';
            $fonts_library_markup = '<script ' . $type_js . 'data-cfasync="false" id="ao_optimized_gfonts_webfontloader">(function() {var wf = document.createElement(\'script\');wf.src=\'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js\';wf.type=\'text/javascript\';wf.async=\'true\';var s=document.getElementsByTagName(\'script\')[0];s.parentNode.insertBefore(wf, s);})();</script>';
            $in                   = substr_replace( $in, $fonts_library_markup . '</head>', strpos( $in, '</head>' ), strlen( '</head>' ) );
        }

        // Replace back in markup.
        $inject_point = apply_filters( 'autoptimize_filter_extra_gfont_injectpoint', '<link' );
        $out          = substr_replace( $in, $fonts_markup . $inject_point, strpos( $in, $inject_point ), strlen( $inject_point ) );
        unset( $fonts_collection );

        return $out;
    }

    public function filter_preconnect( $hints, $relation_type )
    {
        $options  = $this->options;
        $preconns = array();

        // Get settings and store in array.
        if ( array_key_exists( 'autoptimize_extra_text_field_2', $options ) ) {
            $preconns = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( $options['autoptimize_extra_text_field_2'] ) ) ) );
        }
        $preconns = apply_filters( 'autoptimize_extra_filter_tobepreconn', $preconns );

        // Walk array, extract domain and add to new array with crossorigin attribute.
        foreach ( $preconns as $preconn ) {
            $domain = '';
            $parsed = parse_url( $preconn );
            if ( is_array( $parsed ) && ! empty( $parsed['host'] ) && empty( $parsed['scheme'] ) ) {
                $domain = '//' . $parsed['host'];
            } elseif ( is_array( $parsed ) && ! empty( $parsed['host'] ) ) {
                $domain = $parsed['scheme'] . '://' . $parsed['host'];
            }

            if ( ! empty( $domain ) ) {
                $hint = array( 'href' => $domain );
                // Fonts don't get preconnected unless crossorigin flag is set, non-fonts don't get preconnected if origin flag is set
                // so hardcode fonts.gstatic.com to come with crossorigin and have filter to add other domains if needed.
                $crossorigins = apply_filters( 'autoptimize_extra_filter_preconn_crossorigin', array( 'https://fonts.gstatic.com' ) );
                if ( in_array( $domain, $crossorigins ) ) {
                    $hint['crossorigin'] = 'anonymous';
                }
                $new_hints[] = $hint;
            }
        }

        // Merge in WP's preconnect hints.
        if ( 'preconnect' === $relation_type && ! empty( $new_hints ) ) {
            $hints = array_merge( $hints, $new_hints );
        }

        return $hints;
    }

    public function filter_preconnect_google_fonts( $in )
    {
        if ( '2' !== $this->options['autoptimize_extra_radio_field_4'] ) {
            // Preconnect to fonts.gstatic.com unless we remove gfonts.
            $in[] = 'https://fonts.gstatic.com';
        }

        if ( '4' === $this->options['autoptimize_extra_radio_field_4'] ) {
            // Preconnect even more hosts for webfont.js!
            $in[] = 'https://ajax.googleapis.com';
            $in[] = 'https://fonts.googleapis.com';
        }

        return $in;
    }

    public function filter_preload( $in ) {
        // make array from comma separated list.
        $options  = $this->options;
        $preloads = array();
        if ( array_key_exists( 'autoptimize_extra_text_field_7', $options ) ) {
            $preloads = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( $options['autoptimize_extra_text_field_7'] ) ) ) );
        }

        if ( false === autoptimizeImages::imgopt_active() && false === autoptimizeImages::should_lazyload_wrapper() ) {
            // only do this here if imgopt/ lazyload are not active?
            $metabox_preloads = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_preload' ) ) ) ) );
            if ( ! empty( $metabox_preloads ) ) {
                $preloads = array_merge( $preloads, $metabox_preloads );
            }
        }

        $preloads = apply_filters( 'autoptimize_filter_extra_tobepreloaded', $preloads );

        // immediately return if nothing to be preloaded.
        if ( empty( $preloads ) ) {
            return $in;
        }

        // iterate through array and add preload link to tmp string.
        $preload_output = '';
        foreach ( $preloads as $preload ) {
            if ( filter_var( $preload, FILTER_VALIDATE_URL ) !== $preload ) {
                continue;
            }
            $preload     = esc_url_raw( $preload );
            $crossorigin = '';
            $preload_as  = '';
            $mime_type   = '';
            $_preload    = strtok( $preload, '?' );

            if ( autoptimizeUtils::str_ends_in( $_preload, '.css' ) ) {
                $preload_as = 'style';
            } elseif ( autoptimizeUtils::str_ends_in( $_preload, '.js' ) ) {
                $preload_as = 'script';
            } elseif ( autoptimizeUtils::str_ends_in( $_preload, '.woff' ) || autoptimizeUtils::str_ends_in( $_preload, '.woff2' ) || autoptimizeUtils::str_ends_in( $_preload, '.ttf' ) || autoptimizeUtils::str_ends_in( $_preload, '.eot' ) || autoptimizeUtils::str_ends_in( $_preload, '.otf' ) ) {
                $preload_as  = 'font';
                $crossorigin = ' crossorigin';
                $mime_type   = ' type="font/' . pathinfo( $_preload, PATHINFO_EXTENSION ) . '"';
                if ( ' type="font/eot"' === $mime_type ) {
                    $mime_type = 'application/vnd.ms-fontobject';
                }
            } elseif ( autoptimizeUtils::str_ends_in( $_preload, '.jpeg' ) || autoptimizeUtils::str_ends_in( $_preload, '.jpg' ) || autoptimizeUtils::str_ends_in( $_preload, '.webp' ) || autoptimizeUtils::str_ends_in( $_preload, '.png' ) || autoptimizeUtils::str_ends_in( $_preload, '.gif' ) || autoptimizeUtils::str_ends_in( $_preload, '.svg' ) ) {
                $preload_as = 'image';
            } else {
                $preload_as = 'other';
            }

            $preload_output .= '<link rel="preload" href="' . $preload . '" as="' . $preload_as . '"' . $mime_type . $crossorigin . '>';
        }
        $preload_output = apply_filters( 'autoptimize_filter_extra_preload_output', $preload_output );

        return $this->inject_preloads( $preload_output, $in );
    }

    public static function inject_preloads( $preloads, $html ) {
        // add string to head (before first link node by default).
        $preload_inject = apply_filters( 'autoptimize_filter_extra_preload_inject', '<link' );
        $position       = autoptimizeUtils::strpos( $html, $preload_inject );

        return autoptimizeUtils::substr_replace( $html, $preloads . $preload_inject, $position, strlen( $preload_inject ) );
    }

    public function disable_global_styles()
    {
        remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
        remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
        remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

        if ( true === apply_filters( 'autoptimize_filter_extra_global_styles_and_block_css', true ) ) {
            add_action(
                'wp_enqueue_scripts',
                function() {
                    wp_dequeue_style( 'wp-block-library' );
                    wp_dequeue_style( 'wp-block-library-theme' );
                }
            );
        }

        if ( true === apply_filters( 'autoptimize_filter_extra_remove_woocommerce_block_css', true ) ) {
            add_action(
                'wp_enqueue_scripts',
                function() {
                    wp_dequeue_style( 'wc-blocks-style' );
                }
            );
        }
    }

    public function admin_menu()
    {
        // no acces if multisite and not network admin and no site config allowed.
        if ( autoptimizeConfig::should_show_menu_tabs() ) {
            add_submenu_page(
                '',
                'autoptimize_extra',
                'autoptimize_extra',
                'manage_options',
                'autoptimize_extra',
                array( $this, 'options_page' )
            );
        }
        register_setting( 'autoptimize_extra_settings', 'autoptimize_extra_settings' );
    }

    public function add_extra_tab( $in )
    {
        if ( autoptimizeConfig::should_show_menu_tabs() ) {
            $in = array_merge( $in, array( 'autoptimize_extra' => __( 'Extra', 'autoptimize' ) ) );
        }

        return $in;
    }

    public function options_page()
    {
        // phpcs:disable Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace

        // Working with actual option values from the database here.
        // That way any saves are still processed as expected, but we can still
        // override behavior by using `new autoptimizeExtra($custom_options)` and not have that custom
        // behavior being persisted in the DB even if save is done here.
        $options = $this->fetch_options();
        $gfonts  = $options['autoptimize_extra_radio_field_4'];
        ?>
    <style>
        #ao_settings_form {background: white;border: 1px solid #ccc;padding: 1px 15px;margin: 15px 10px 10px 0;}
        #ao_settings_form .form-table th {font-weight: normal;}
        #autoptimize_extra_descr{font-size: 120%;}
    </style>
    <script>document.title = "Autoptimize: <?php _e( 'Extra', 'autoptimize' ); ?> " + document.title;</script>
    <div class="wrap">
    <h1><?php apply_filters( 'autoptimize_filter_settings_is_pro', false ) ? _e( 'Autoptimize Pro Settings', 'autoptimize' ) : _e( 'Autoptimize Settings', 'autoptimize' ); ?></h1>
        <?php echo autoptimizeConfig::ao_admin_tabs(); ?>
        <?php if ( 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_js' ) && 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_css' ) && 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_html' ) && ! autoptimizeImages::imgopt_active() ) { ?>
            <div class="notice-warning notice"><p>
            <?php _e( 'Most of below Extra optimizations require at least one of HTML, JS, CSS or Image autoptimizations being active.', 'autoptimize' ); ?>
            </p></div>
        <?php } ?>

    <form id='ao_settings_form' action='<?php echo admin_url( 'options.php' ); ?>' method='post'>
        <?php settings_fields( 'autoptimize_extra_settings' ); ?>
        <h2><?php _e( 'Extra Auto-Optimizations', 'autoptimize' ); ?></h2>
        <span id='autoptimize_extra_descr'><?php _e( 'The following settings can improve your site\'s performance even more.', 'autoptimize' ); ?></span>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Google Fonts', 'autoptimize' ); ?></th>
                <td>
                    <input type="radio" name="autoptimize_extra_settings[autoptimize_extra_radio_field_4]" value="1" <?php if ( ! in_array( $gfonts, array( 2, 3, 4, 5 ) ) ) { echo 'checked'; } ?> ><?php _e( 'Leave as is', 'autoptimize' ); ?><br/>
                    <input type="radio" name="autoptimize_extra_settings[autoptimize_extra_radio_field_4]" value="2" <?php checked( 2, $gfonts, true ); ?> ><?php _e( 'Remove Google Fonts', 'autoptimize' ); ?><br/>
                    <?php // translators: "display:swap" should remain untranslated, will be shown in code tags. ?>
                    <input type="radio" name="autoptimize_extra_settings[autoptimize_extra_radio_field_4]" value="3" <?php checked( 3, $gfonts, true ); ?> ><?php echo __( 'Combine and link in head (fonts load fast but are render-blocking)', 'autoptimize' ) . ', ' . sprintf( __( 'includes %1$sdisplay:swap%2$s.', 'autoptimize' ), '<code>', '</code>' ); ?><br/>
                    <?php // translators: "display:swap" should remain untranslated, will be shown in code tags. ?>
                    <input type="radio" name="autoptimize_extra_settings[autoptimize_extra_radio_field_4]" value="5" <?php checked( 5, $gfonts, true ); ?> ><?php echo __( 'Combine and link deferred in head (fonts load late, but are not render-blocking)', 'autoptimize' ) . ', ' . sprintf( __( 'includes %1$sdisplay:swap%2$s.', 'autoptimize' ), '<code>', '</code>' ); ?><br/>
                    <input type="radio" name="autoptimize_extra_settings[autoptimize_extra_radio_field_4]" value="4" <?php checked( 4, $gfonts, true ); ?> ><?php echo __( 'Combine and load fonts asynchronously with <a href="https://github.com/typekit/webfontloader#readme" target="_blank">webfont.js</a>', 'autoptimize' ) . ' ' . __( '(deprecated)', 'autoptimize' ); ?><br/>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Remove emojis', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='checkbox' name='autoptimize_extra_settings[autoptimize_extra_checkbox_field_1]' <?php if ( ! empty( $options['autoptimize_extra_checkbox_field_1'] ) && '1' === $options['autoptimize_extra_checkbox_field_1'] ) { echo 'checked="checked"'; } ?> value='1'><?php _e( 'Removes WordPress\' core emojis\' inline CSS, inline JavaScript, and an otherwise un-autoptimized JavaScript file.', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Remove query strings from static resources', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='checkbox' name='autoptimize_extra_settings[autoptimize_extra_checkbox_field_0]' <?php if ( ! empty( $options['autoptimize_extra_checkbox_field_0'] ) && '1' === $options['autoptimize_extra_checkbox_field_0'] ) { echo 'checked="checked"'; } ?> value='1'><?php _e( 'Removing query strings (or more specifically the <code>ver</code> parameter) will not improve load time, but might improve performance scores.', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Remove WordPress block CSS', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='checkbox' name='autoptimize_extra_settings[autoptimize_extra_checkbox_field_8]' <?php if ( ! empty( $options['autoptimize_extra_checkbox_field_8'] ) && '1' === $options['autoptimize_extra_checkbox_field_8'] ) { echo 'checked="checked"'; } ?> value='1'><?php _e( 'WordPress adds block CSS and global styles to improve easy styling of block-based sites, but which can add a significant amount of CSS and SVG. If you are sure your site can do without the block CSS and "global styles", you can disable them here.', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Preconnect to 3rd party domains <em>(advanced users)</em>', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='text' style='width:80%' name='autoptimize_extra_settings[autoptimize_extra_text_field_2]' value='<?php if ( array_key_exists( 'autoptimize_extra_text_field_2', $options ) ) { echo esc_attr( $options['autoptimize_extra_text_field_2'] ); } ?>'><br /><?php _e( 'Add 3rd party domains you want the browser to <a href="https://www.keycdn.com/support/preconnect/#primary" target="_blank">preconnect</a> to, separated by comma\'s. Make sure to include the correct protocol (HTTP or HTTPS).', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Preload specific requests <em>(advanced users)</em>', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='text' style='width:80%' name='autoptimize_extra_settings[autoptimize_extra_text_field_7]' value='<?php if ( array_key_exists( 'autoptimize_extra_text_field_7', $options ) ) { echo esc_attr( $options['autoptimize_extra_text_field_7'] ); } ?>'><br /><?php _e( 'Comma-separated list with full URL\'s of to to-be-preloaded resources. To be used sparingly!', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Async Javascript-files <em>(advanced users)</em>', 'autoptimize' ); ?></th>
                <td>
                    <?php
                    if ( autoptimizeUtils::is_plugin_active( 'async-javascript/async-javascript.php' ) ) {
                        // translators: link points Async Javascript settings page.
                        printf( __( 'You have "Async JavaScript" installed, %1$sconfiguration of async javascript is best done there%2$s.', 'autoptimize' ), '<a href="' . 'options-general.php?page=async-javascript' . '">', '</a>' );
                    } else {
                        ?>
                        <input type='text' style='width:80%' name='autoptimize_extra_settings[autoptimize_extra_text_field_3]' value='<?php if ( array_key_exists( 'autoptimize_extra_text_field_3', $options ) ) { echo esc_attr( $options['autoptimize_extra_text_field_3'] ); } ?>'>
                        <br />
                        <?php
                            _e( 'Comma-separated list of local or 3rd party JS-files that should loaded with the <code>async</code> flag. JS-files from your own site will be automatically excluded if added here. ', 'autoptimize' );
                            // translators: %s will be replaced by a link to the "async javascript" plugin.
                            echo sprintf( __( 'Configuration of async javascript is easier and more flexible using the %s plugin.', 'autoptimize' ), '"<a href="https://wordpress.org/plugins/async-javascript" target="_blank">Async Javascript</a>"' );
                            $asj_install_url = network_admin_url() . 'plugin-install.php?s=async+javascript&tab=search&type=term';
                            echo sprintf( ' <a href="' . $asj_install_url . '">%s</a>', __( 'Click here to install and activate it.', 'autoptimize' ) );
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Optimize YouTube videos', 'autoptimize' ); ?></th>
                <td>
                    <?php
                    if ( autoptimizeUtils::is_plugin_active( 'wp-youtube-lyte/wp-youtube-lyte.php' ) ) {
                        _e( 'Great, you have WP YouTube Lyte installed.', 'autoptimize' );
                        $lyte_config_url = 'options-general.php?page=lyte_settings_page';
                        echo sprintf( ' <a href="' . $lyte_config_url . '">%s</a>', __( 'Click here to configure it.', 'autoptimize' ) );
                    } else {
                        // translators: %s will be replaced by a link to "wp youtube lyte" plugin.
                        echo sprintf( __( '%s allows you to “lazy load” your videos, by inserting responsive “Lite YouTube Embeds". ', 'autoptimize' ), '<a href="https://wordpress.org/plugins/wp-youtube-lyte" target="_blank">WP YouTube Lyte</a>' );
                        $lyte_install_url = network_admin_url() . 'plugin-install.php?s=lyte&tab=search&type=term';
                        echo sprintf( ' <a href="' . $lyte_install_url . '">%s</a>', __( 'Click here to install and activate it.', 'autoptimize' ) );
                    }
                    ?>
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'autoptimize' ); ?>" /></p>
    </form>
        <?php
    }
}
