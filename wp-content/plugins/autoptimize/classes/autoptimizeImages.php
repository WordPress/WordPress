<?php
/**
 * Handles optimizing images.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeImages
{
    /**
     * Options.
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
     * lazyload counter.
     * 
     * @var int
     */
    protected $lazyload_counter = 0;

    public function __construct( array $options = array() )
    {
        // If options are not provided, fetch them.
        if ( empty( $options ) ) {
            $options = $this->fetch_options();
        }

        $this->set_options( $options );
    }

    public function set_options( array $options )
    {
        $this->options = $options;

        return $this;
    }

    public static function fetch_options()
    {
        $value = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_settings' );
        if ( empty( $value ) ) {
            // Fallback to returning defaults when no stored option exists yet.
            $value = autoptimizeConfig::get_ao_imgopt_default_options();
        }

        // get service availability and add it to the options-array.
        $value['availabilities'] = autoptimizeOptionWrapper::get_option( 'autoptimize_service_availablity' );

        if ( empty( $value['availabilities'] ) || ! is_array( $value['availabilities'] ) ) {
            $value['availabilities'] = null;

            if ( true === autoptimizeImages::imgopt_active() ) {
                $value['availabilities'] = autoptimizeUtils::check_service_availability( true );
            }

            if ( null === $value['availabilities'] ) {
                // We can't seem to check service availability, use mock result with imgopt status UP.
                $_mock_settings = array(
                    'extra_imgopt' => array(
                        'status' => 'up',
                        'hosts' => array(
                            '1' => 'https://sp-ao.shortpixel.ai/',
                        ),
                    ),
                    'critcss' => array(
                        'status' => 'up',
                    ),
                );
                $value['availabilities'] = $_mock_settings;
            }
        }

        return $value;
    }

    public static function imgopt_active()
    {
        // function to quickly check if imgopt is active, used below but also in
        // autoptimizeMain.php to start ob_ even if no HTML, JS or CSS optimizing is done
        // and does not use/ request the availablity data (which could slow things down).
        static $imgopt_active = null;

        if ( null === $imgopt_active ) {
            $opts = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_settings', '' );
            if ( ! empty( $opts ) && is_array( $opts ) && array_key_exists( 'autoptimize_imgopt_checkbox_field_1', $opts ) && ! empty( $opts['autoptimize_imgopt_checkbox_field_1'] ) && '1' === $opts['autoptimize_imgopt_checkbox_field_1'] ) {
                $imgopt_active = true;
            } else {
                $imgopt_active = false;
            }
        }

        return $imgopt_active;
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
                add_action( 'network_admin_menu', array( $this, 'imgopt_admin_menu' ) );
            } else {
                add_action( 'admin_menu', array( $this, 'imgopt_admin_menu' ) );
            }
            add_filter( 'autoptimize_filter_settingsscreen_tabs', array( $this, 'add_imgopt_tab' ), 9 );
        } else {
            add_action( 'wp', array( $this, 'run_on_frontend' ) );
        }
    }

    public function run_on_frontend() {
        if ( ! $this->should_run() ) {
            if ( $this->should_lazyload() ) {
                add_filter(
                    'wp_lazy_loading_enabled',
                    array( $this, 'should_disable_core_lazyload' ),
                    10,
                    3
                );
                add_filter(
                    'autoptimize_html_after_minify',
                    array( $this, 'filter_lazyload_images' ),
                    10,
                    1
                );
                add_action(
                    'wp_footer',
                    array( $this, 'add_lazyload_js_footer' ),
                    10,
                    0
                );
            }
            return;
        }

        $active = false;

        if ( apply_filters( 'autoptimize_filter_imgopt_do', true ) ) {
            add_filter(
                'autoptimize_html_after_minify',
                array( $this, 'filter_optimize_images' ),
                10,
                1
            );
            $active = true;
        }

        if ( apply_filters( 'autoptimize_filter_imgopt_do_css', true ) ) {
            // fixme: also act on already minified CSS!
            add_filter(
                'autoptimize_filter_base_replace_cdn',
                array( $this, 'filter_optimize_css_images' ),
                10,
                1
            );

            add_filter(
                'autoptimize_html_after_minify',
                array( $this, 'filter_optimize_inline_css_images' ),
                10,
                1
            );

            $active = true;
        }

        if ( $active ) {
            add_filter(
                'autoptimize_extra_filter_tobepreconn',
                array( $this, 'filter_preconnect_imgopt_url' ),
                10,
                1
            );
        }

        if ( $this->should_lazyload() ) {
            add_filter(
                'wp_lazy_loading_enabled',
                array( $this, 'should_disable_core_lazyload' ),
                10,
                3
            );
            add_action(
                'wp_footer',
                array( $this, 'add_lazyload_js_footer' ),
                10,
                0
            );
        }
    }

    /**
     * Disables core's native lazyload for images, not for iframes.
     *
     * @param bool   $flag      Incoming flag (mostly true).
     * @param string $tag       Tag (img or iframe).
     * @param string $context   Full context.
     *
     * @return bool
     */
    public function should_disable_core_lazyload( $flag = true, $tag = '', $context = '' ) {
        if ( 'img' === $tag ) {
            return false;
        }
        return $flag;
    }

    /**
     * Basic checks before we can run.
     *
     * @return bool
     */
    protected function should_run()
    {
        $opts              = $this->options;
        $service_not_down  = ( 'down' !== $opts['availabilities']['extra_imgopt']['status'] );
        $not_launch_status = ( 'launch' !== $opts['availabilities']['extra_imgopt']['status'] );

        $do_cdn      = true;
        $_userstatus = $this->get_imgopt_provider_userstatus();
        if ( isset( $_userstatus['Status'] ) && ( -2 == $_userstatus['Status'] || -3 == $_userstatus['Status'] ) ) {
            // don't even attempt to put images on CDN if heavily exceeded threshold or if site not reachable.
            $do_cdn = false;
        }

        if (
            $this->imgopt_active()
            && $do_cdn
            && $service_not_down
            && ( $not_launch_status || $this->launch_ok() )
        ) {
            return true;
        }
        return false;
    }

    public function get_imgopt_host()
    {
        static $imgopt_host = null;

        if ( null === $imgopt_host ) {
            $imgopt_host  = 'https://sp-ao.shortpixel.ai/';
            $avail_imgopt = $this->options['availabilities']['extra_imgopt'];
            if ( ! empty( $avail_imgopt ) && array_key_exists( 'hosts', $avail_imgopt ) && is_array( $avail_imgopt['hosts'] ) ) {
                $imgopt_host = array_rand( array_flip( $avail_imgopt['hosts'] ) );
            }
            $imgopt_host = apply_filters( 'autoptimize_filter_imgopt_host', $imgopt_host );
        }

        return $imgopt_host;
    }

    public static function get_imgopt_host_wrapper()
    {
        // needed for CI tests.
        $self = new self();
        return $self->get_imgopt_host();
    }

    public static function get_service_url_suffix()
    {
        $suffix = '/af/U0ZIWMK109483/' . AUTOPTIMIZE_SITE_DOMAIN;

        return $suffix;
    }

    public function get_img_quality_string()
    {
        static $quality = null;

        if ( null === $quality ) {
            $q_array = $this->get_img_quality_array();
            $setting = $this->get_img_quality_setting();
            $quality = apply_filters(
                'autoptimize_filter_imgopt_quality',
                'q_' . $q_array[ $setting ]
            );
        }

        return $quality;
    }

    public function get_img_quality_array()
    {
        static $map = null;

        if ( null === $map ) {
            $map = array(
                '1' => 'lossy',
                '2' => 'glossy',
                '3' => 'lossless',
            );
            $map = apply_filters(
                'autoptimize_filter_imgopt_quality_array',
                $map
            );
        }

        return $map;
    }

    public function get_img_quality_setting()
    {
        static $q = null;

        if ( null === $q ) {
            if ( is_array( $this->options ) && array_key_exists( 'autoptimize_imgopt_select_field_2', $this->options ) ) {
                $setting = $this->options['autoptimize_imgopt_select_field_2'];
            }

            if ( ! isset( $setting ) || empty( $setting ) || ( '1' !== $setting && '3' !== $setting ) ) {
                // default image opt. value is 2 ("glossy").
                $q = '2';
            } else {
                $q = $setting;
            }
        }

        return $q;
    }

    public function filter_preconnect_imgopt_url( array $in )
    {
        $url_parts = parse_url( $this->get_imgopt_base_url() );
        $in[]      = $url_parts['scheme'] . '://' . $url_parts['host'];

        return $in;
    }

    /**
     * Makes sure given url contains the full scheme and hostname
     * in case they're not present already.
     *
     * @param string $in Image url to normalize.
     *
     * @return string
     */
    private function normalize_img_url( $in )
    {
        // Only parse the site url once.
        static $parsed_site_url = null;
        if ( null === $parsed_site_url ) {
            $parsed_site_url = parse_url( site_url() );
        }

        // get CDN domain once.
        static $cdn_domain = null;
        if ( is_null( $cdn_domain ) ) {
            $cdn_url = $this->get_cdn_url();
            if ( ! empty( $cdn_url ) ) {
                $cdn_domain = parse_url( $cdn_url, PHP_URL_HOST );
            } else {
                $cdn_domain = '';
            }
        }

        /**
         * This method gets called a lot, often for identical urls it seems.
         * `filter_optimize_css_images()` calls us, uses the resulting url and
         * gives it to `can_optimize_image()`, and if that returns trueish
         * then `build_imgopt_url()` is called (which, again, calls this method).
         * Until we dig deeper into whether this all must really happen that
         * way, having an internal cache here helps (to avoid doing repeated
         * identical string operations).
         */
        static $cache = null;
        if ( null === $cache ) {
            $cache = array();
        }

        // Do the work on cache miss only.
        if ( ! isset( $cache[ $in ] ) ) {
            // Default to (the trimmed version of) what was given to us.
            $result = trim( $in );

            // Some silly plugins wrap background images in html-encoded quotes, so remove those from the img url.
            $result = $this->fix_silly_bgimg_quotes( $result );

            if ( autoptimizeUtils::is_protocol_relative( $result ) ) {
                $result = $parsed_site_url['scheme'] . ':' . $result;
            } elseif ( 0 === strpos( $result, '/' ) ) {
                // Root-relative...
                $result = $parsed_site_url['scheme'] . '://' . $parsed_site_url['host'] . $result;
            } elseif ( ! empty( $cdn_domain ) && false === strpos( $this->get_imgopt_host(), $cdn_domain ) && strpos( $result, $cdn_domain ) !== 0 ) {
                // remove CDN except if it is the image optimization one.
                $result = str_replace( $cdn_domain, $parsed_site_url['host'], $result );
            }

            // filter (default off) to remove QS from image URL's to avoid eating away optimization credits.
            if ( apply_filters( 'autoptimize_filter_imgopt_no_querystring', false ) && strpos( $result, '?' ) !== false ) {
                $result = strtok( $result, '?' );
            }

            $result = apply_filters( 'autoptimize_filter_imgopt_normalized_url', $result );

            // Store in cache.
            $cache[ $in ] = $result;
        }

        return $cache[ $in ];
    }

    public function filter_optimize_css_images( $in )
    {
        $in = $this->normalize_img_url( $in );

        if ( $this->can_optimize_image( $in ) && false === strpos( $in, $this->get_imgopt_host() ) ) {
            return $this->build_imgopt_url( $in, '', '' );
        } else {
            return $in;
        }
    }
    
    public function filter_optimize_inline_css_images( $html ) {
        preg_match_all( '#<style[^>]*>([^<]*)</style>#Um', $html, $inline_css_blocks, PREG_SET_ORDER );
        foreach ( $inline_css_blocks as $inline_css_block ) {
            if ( false !== strpos( $inline_css_block[0], 'background' ) ) { 
                $inline_css_block_new = $this->replace_background_img_css( $inline_css_block[0] );
                if ( $inline_css_block_new !== $inline_css_block[0] ) {
                    $html = str_replace( $inline_css_block[0], $inline_css_block_new, $html );
                }
            }
        }
        return $html;
    }

    public static function replace_background_img_css( $css ) {
        // fixme; can/ should we cache these?
        preg_match_all( '#background[^;}]*url\((.*)\)#Ui', $css, $backgrounds, PREG_SET_ORDER );
        if ( is_array( $backgrounds ) && ! empty( $backgrounds ) ) {
            foreach ( $backgrounds as $background ) {
                if ( autoptimizeImages::can_optimize_image_wrapper( $background[1] ) ) {
                    $css = str_replace( $background[1], autoptimizeImages::build_imgopt_url_wrapper( $background[1] ), $css );
                }
            }
        }
        return $css;
    }

    private function get_imgopt_base_url()
    {
        static $imgopt_base_url = null;

        if ( null === $imgopt_base_url ) {
            $imgopt_host     = $this->get_imgopt_host();
            $quality         = $this->get_img_quality_string();
            $ret_val         = apply_filters( 'autoptimize_filter_imgopt_wait', 'ret_img' ); // values: ret_wait, ret_img, ret_json, ret_blank.
            if ( $this->should_ngimg() ) {
                $sp_to_string = 'to_auto';
            } else {
                $sp_to_string = 'to_webp';
            }
            $sp_to_string    = apply_filters( 'autoptimize_filter_imgopt_format', $sp_to_string ); // values: empty (= jpeg), to_webp (smart; webp or fallback), to_avif (avif or fallback) or to_auto (smart avif, webp or fallback).
            $imgopt_base_url = $imgopt_host . 'client/' . $sp_to_string . ',' . $quality . ',' . $ret_val;
            $imgopt_base_url = apply_filters( 'autoptimize_filter_imgopt_base_url', $imgopt_base_url );
        }

        return $imgopt_base_url;
    }

    public static function can_optimize_image_wrapper( $url, $tag = '', $testing = false ) {
        $self = new self();
        return $self->can_optimize_image( $url, $tag = '', $testing = false );
    }

    private function can_optimize_image( $url, $tag = '', $testing = false )
    {
        static $cdn_url      = null;
        static $nopti_images = null;

        if ( null === $cdn_url ) {
            $cdn_url = apply_filters(
                'autoptimize_filter_base_cdnurl',
                autoptimizeOptionWrapper::get_option( 'autoptimize_cdn_url', '' )
            );
        }

        if ( null === $nopti_images || $testing ) {
            if ( is_array( $this->options ) && array_key_exists( 'autoptimize_imgopt_text_field_6', $this->options ) ) {
                $nopti_images = $this->options['autoptimize_imgopt_text_field_6'];
            }
            $nopti_images = apply_filters( 'autoptimize_filter_imgopt_noptimize', $nopti_images );
        }

        $site_host  = AUTOPTIMIZE_SITE_DOMAIN;
        $url        = $this->normalize_img_url( $url );
        $url_parsed = parse_url( $url );

        if ( array_key_exists( 'host', $url_parsed ) && $url_parsed['host'] !== $site_host && empty( $cdn_url ) ) {
            return false;
        } elseif ( autoptimizeUtils::is_local_server() ) {
            return false;
        } elseif ( ! empty( $cdn_url ) && strpos( $url, $cdn_url ) === false && array_key_exists( 'host', $url_parsed ) && $url_parsed['host'] !== $site_host ) {
            return false;
        } elseif ( strpos( $url, '.php' ) !== false ) {
            return false;
        } elseif ( str_ireplace( array( '.png', '.gif', '.jpg', '.jpeg', '.webp', '.avif' ), '', $url_parsed['path'] ) === $url_parsed['path'] ) {
            // fixme: better check against end of string.
            return false;
        } elseif ( ! empty( $nopti_images ) ) {
            $nopti_images_array = array_filter( array_map( 'trim', explode( ',', $nopti_images ) ) );
            foreach ( $nopti_images_array as $nopti_image ) {
                if ( strpos( $url, $nopti_image ) !== false || ( ( '' !== $tag && strpos( $tag, $nopti_image ) !== false ) ) ) {
                    return false;
                }
            }
        }
        return true;
    }

    // wrapper for reuse in AOPro.
    public static function build_imgopt_url_wrapper( $orig_url, $width = 0, $height = 0 ) {
        $self = new self();
        return $self->build_imgopt_url( $orig_url, $width = 0, $height = 0 );
    }

    private function build_imgopt_url( $orig_url, $width = 0, $height = 0 )
    {
        // sanitize width and height.
        if ( strpos( $width, '%' ) !== false ) {
            $width = 0;
        }
        if ( strpos( $height, '%' ) !== false ) {
            $height = 0;
        }
        $width  = (int) $width;
        $height = (int) $height;

        $filtered_url = apply_filters(
            'autoptimize_filter_imgopt_build_url',
            $orig_url,
            $width,
            $height
        );

        // If filter modified the url, return that.
        if ( $filtered_url !== $orig_url ) {
            return $filtered_url;
        }

        $normalized_url = $this->normalize_img_url( $orig_url );

        // if the URL is ascii we check if we have a real URL with filter_var (which only works on ascii url's) and if not a real URL we return the original one.
        if ( apply_filters( 'autoptimize_filter_imgopt_check_normalized_url', true ) && ! preg_match( '/[^\x20-\x7e]/', $normalized_url ) && false === filter_var( $normalized_url, FILTER_VALIDATE_URL ) ) {
            return $orig_url;
        }

        $imgopt_base_url = $this->get_imgopt_base_url();
        $imgopt_size     = '';

        if ( $width && 0 !== $width ) {
            $imgopt_size = ',w_' . $width;
        }

        if ( $height && 0 !== $height ) {
            $imgopt_size .= ',h_' . $height;
        }

        $url = $imgopt_base_url . $imgopt_size . '/' . $normalized_url;

        return $url;
    }

    public function replace_data_thumbs( $matches )
    {
        return $this->replace_img_callback( $matches, 150, 150 );
    }

    public function replace_img_callback( $matches, $width = 0, $height = 0 )
    {
        $_normalized_img_url = $this->normalize_img_url( $matches[1] );
        if ( $this->can_optimize_image( $matches[1], $matches[0] ) ) {
            return str_replace( $matches[1], $this->build_imgopt_url( $_normalized_img_url, $width, $height ), $matches[0] );
        } else {
            return $matches[0];
        }
    }

    public function replace_icon_callback( $matches )
    {
        if ( array_key_exists( '2', $matches ) ) {
            $sizes  = explode( 'x', $matches[2] );
            $width  = $sizes[0];
            $height = $sizes[1];
        } else {
            $width  = 180;
            $height = 180;
        }

        // make sure we're not trying to optimize a *.ico file.
        if ( strpos( $matches[1], '.ico' ) === false ) {
            return $this->replace_img_callback( $matches, $width, $height );
        } else {
            return $matches[0];
        }
    }

    public function filter_optimize_images( $in, $testing = false )
    {
        /*
         * potential future functional improvements:
         *
         * filter for critical CSS.
         */
        $to_replace = array();
        $to_preload = '';

        // hide (no)script tags to avoid replacing (and potentially breaking) images in script tags.
        if ( apply_filters( 'autoptimize_filter_imgopt_hide_script', true ) || $this->should_lazyload() ) {
            $in = autoptimizeBase::replace_contents_with_marker_if_exists(
                'SCRIPT',
                '<script',
                '#<(?:no)?script.*?<\/(?:no)?script>#is',
                $in
            );
        }

        // get img preloads as set in post metabox, exploding ", " instead of "," because LCP preload 
        // could be a shortpixel URL, which has comma's and results in way too many preloads.
        $metabox_preloads = array_filter( array_map( 'trim', explode( ', ', wp_strip_all_tags( autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_preload' ) ) ) ) );
        $metabox_preloads = apply_filters( 'autoptimize_filter_images_metabox_preloads', $metabox_preloads );

        // extract img tags.
        if ( preg_match_all( '#<img[^>]*src[^>]*>#Usmi', $in, $matches ) ) {
            foreach ( $matches[0] as $tag ) {
                $tag = apply_filters( 'autoptimize_filter_imgopt_tag_preopt', $tag );

                $orig_tag = $tag;
                $imgopt_w = '';
                $imgopt_h = '';

                // first do (data-)srcsets.
                if ( preg_match_all( '#srcset=("|\')(.*)("|\')#Usmi', $tag, $allsrcsets, PREG_SET_ORDER ) ) {
                    foreach ( $allsrcsets as $srcset ) {
                        $srcset      = $srcset[2];
                        $orig_srcset = $srcset;
                        $srcsets     = explode( ',', $srcset );
                        foreach ( $srcsets as $indiv_srcset ) {
                            $indiv_srcset_parts = explode( ' ', trim( $indiv_srcset ) );
                            if ( isset( $indiv_srcset_parts[1] ) && rtrim( $indiv_srcset_parts[1], 'w' ) !== $indiv_srcset_parts[1] ) {
                                $imgopt_w = rtrim( $indiv_srcset_parts[1], 'w' );
                            }
                            if ( $this->can_optimize_image( $indiv_srcset_parts[0], $tag, $testing ) && false === apply_filters( 'autoptimize_filter_imgopt_do_spai', false ) ) {
                                $imgopt_url = $this->build_imgopt_url( $indiv_srcset_parts[0], $imgopt_w, '' );
                                $srcset     = str_replace( $indiv_srcset_parts[0], $imgopt_url, $srcset );
                            }
                        }
                        $tag = str_replace( $orig_srcset, $srcset, $tag );
                    }
                }

                // proceed with img src.
                // get width and height and add to $imgopt_size.
                $_get_size = $this->get_size_from_tag( $tag );
                $imgopt_w  = $_get_size['width'];
                $imgopt_h  = $_get_size['height'];

                // then start replacing images src.
                if ( preg_match_all( '#src=(?:"|\')(?!data)(.*)(?:"|\')#Usmi', $tag, $urls, PREG_SET_ORDER ) ) {
                    foreach ( $urls as $url ) {
                        $full_src_orig = $url[0];
                        $url           = $url[1];
                        if ( $this->can_optimize_image( $url, $tag, $testing ) && false === apply_filters( 'autoptimize_filter_imgopt_do_spai', false ) ) {
                            $imgopt_url      = $this->build_imgopt_url( $url, $imgopt_w, $imgopt_h );
                            $full_imgopt_src = str_replace( $url, $imgopt_url, $full_src_orig );
                            $tag             = str_replace( $full_src_orig, $full_imgopt_src, $tag );
                        }
                    }
                }

                // check if the image needs to be prelaoded.
                if ( ! empty( $metabox_preloads ) && is_array( $metabox_preloads ) && str_replace( $metabox_preloads, '', $tag ) !== $tag ) {
                    $to_preload .= $this->create_img_preload_tag( $tag );
                }

                // do lazyload stuff.
                if ( $this->should_lazyload( $in ) && ! empty( $url ) ) {
                    // first do lpiq placeholder logic.
                    if ( strpos( $url, $this->get_imgopt_host() ) === 0 ) {
                        // if all img src have been replaced during srcset, we have to extract the
                        // origin url from the imgopt one to be able to set a lqip placeholder.
                        $_url = substr( $url, strpos( $url, '/http' ) + 1 );
                    } else {
                        $_url = $url;
                    }

                    $_url = $this->normalize_img_url( $_url );

                    $placeholder = '';
                    if ( $this->can_optimize_image( $_url, $tag ) && apply_filters( 'autoptimize_filter_imgopt_lazyload_dolqip', false, $_url ) && false === apply_filters( 'autoptimize_filter_imgopt_do_spai', false ) ) {
                        $lqip_w = '';
                        $lqip_h = '';
                        if ( isset( $imgopt_w ) && ! empty( $imgopt_w ) ) {
                            $lqip_w = ',w_' . $imgopt_w;
                        }
                        if ( isset( $imgopt_h ) && ! empty( $imgopt_h ) ) {
                            $lqip_h = ',h_' . $imgopt_h;
                        }
                        $placeholder = $this->get_imgopt_host() . 'client/q_lqip,ret_wait' . $lqip_w . $lqip_h . '/' . $_url;
                    }
                    // then call add_lazyload-function with lpiq placeholder if set.
                    $tag = $this->add_lazyload( $tag, $placeholder );
                }

                // add decoding="async" behind filter, not sure if I'll make it default true yet.
                if ( true === apply_filters( 'autoptimize_filter_imgopt_add_decoding', true ) && false === strpos( $tag, ' decoding=' ) ) {
                    $tag = str_replace( '<img ', '<img decoding="async" ', $tag );
                }

                $tag = apply_filters( 'autoptimize_filter_imgopt_tag_postopt', $tag );

                // and add tag to array for later replacement.
                if ( $tag !== $orig_tag ) {
                    $to_replace[ $orig_tag ] = $tag;
                }
            }
        }

        // and replace all.
        $out = str_replace( array_keys( $to_replace ), array_values( $to_replace ), $in );

        // misc. node attributes that might hold image url's (incl. the previously separate data-thumb).
        $extra_attr_with_img = apply_filters( 'autoptimize_filter_imgopt_attr_with_img', array( array( 'div', 'data-thumb'), array( 'div', 'data-background' ), array( 'img', 'data-retina' ) ) );
        if ( ! empty( $extra_attr_with_img ) && is_array( $extra_attr_with_img ) ) {
            foreach ( $extra_attr_with_img as $candidate ) {
                if ( is_array( $candidate ) && strpos( $out, $candidate[1] ) !== false ) {
                    $_regex = '/\<' . $candidate[0] . '(?:[^>]*)?\s' . $candidate[1] . '=(?:"|\')(.+?)(?:"|\')(?:[^>]*)?>/s';
                    $out = preg_replace_callback(
                        $_regex,
                        array( $this, 'replace_img_callback' ),
                        $out
                    );
                }
            }
        }

        // background-image in inline style.
        if ( ( strpos( $out, 'background-image:' ) !== false || strpos( $out, 'background:' ) !== false ) && strpos( $out, 'url(' ) !== false && apply_filters( 'autoptimize_filter_imgopt_backgroundimages', true ) ) {
            $out = preg_replace_callback(
               '/style=(?:"|\')[^<>]*?background(?:-image)?:[^;"\'()>]*url\((?:"|\')?([^"\')]*)(?:"|\')?\)/',
                array( $this, 'replace_img_callback' ),
                $out
            );
        }

        // act on icon links.
        if ( ( strpos( $out, '<link rel="icon"' ) !== false || ( strpos( $out, "<link rel='icon'" ) !== false ) ) && apply_filters( 'autoptimize_filter_imgopt_linkicon', true ) ) {
            $out = preg_replace_callback(
                '/<link\srel=(?:"|\')(?:apple-touch-)?icon(?:"|\').*\shref=(?:"|\')(.*)(?:"|\')(?:\ssizes=(?:"|\')(\d*x\d*)(?:"|\'))?\s\/>/Um',
                array( $this, 'replace_icon_callback' ),
                $out
            );
        }

        // lazyload picture source tags and bgimage.
        if ( $this->should_lazyload() ) {
            $out = $this->process_picture_tag( $out, true, true );
            $out = $this->process_bgimage( $out );
        } else {
            $out = $this->process_picture_tag( $out, true, false );
        }

        // restore (no)script tags.
        if ( apply_filters( 'autoptimize_filter_imgopt_hide_script', true ) || $this->should_lazyload() ) {
            $out = autoptimizeBase::restore_marked_content(
                'SCRIPT',
                $out
            );
        }

        if ( ! empty( $metabox_preloads ) && is_array( $metabox_preloads ) && empty( $to_preload ) && false !== apply_filters( 'autoptimize_filter_imgopt_dopreloads', true ) ) {
            // the preload was not in an img tag, so adding a non-responsive preload instead.
            foreach ( $metabox_preloads as $img_preload ) {
                $to_preload .= '<link rel="preload" href="' . $img_preload . '" as="image">';
            }
        }

        if ( ! empty( $to_preload ) ) {
            $out = autoptimizeExtra::inject_preloads( $to_preload, $out );
        }

        return $out;
    }

    public static function get_size_from_tag( $tag ) {
        // reusable function to extract widht and height from an image tag
        // enforcing a filterable maximum width and height (default 4999X4999).
        $width  = '';
        $height = '';

        if ( preg_match( '#width=("|\')(.*)("|\')#Usmi', $tag, $_width ) ) {
            if ( strpos( $_width[2], '%' ) === false ) {
                $width = (int) $_width[2];
            }
        }
        if ( preg_match( '#height=("|\')(.*)("|\')#Usmi', $tag, $_height ) ) {
            if ( strpos( $_height[2], '%' ) === false ) {
                $height = (int) $_height[2];
            }
        }

        // check for and enforce (filterable) max sizes.
        $_max_width = apply_filters( 'autoptimize_filter_imgopt_max_width', 4999 );
        if ( $width > $_max_width ) {
            $_width = $_max_width;
            if ( ! empty( $height ) && is_int( $height ) ) {
                $height = $_width / $width * $height;
            }
            $width  = $_width;
        }
        $_max_height = apply_filters( 'autoptimize_filter_imgopt_max_height', 4999 );
        if ( $height > $_max_height ) {
            $_height = $_max_height;
            if ( ! empty( $width ) && is_int( $width ) ) {
                $width   = $_height / $height * $width;
            }
            $height  = $_height;
        }

        return array(
            'width'  => $width,
            'height' => $height,
        );
    }

    /**
     * Lazyload functions
     */
    public static function should_lazyload_wrapper( $no_meta = false ) {
        // needed in autoptimizeMain.php.
        $self = new self();
        return $self->should_lazyload( '', $no_meta );
    }

    public function should_lazyload( $context = '', $no_meta = false ) {
        if ( ! empty( $this->options['autoptimize_imgopt_checkbox_field_3'] ) && false === $this->check_nolazy() ) {
            $lazyload_return = true;
        } else {
            $lazyload_return = false;
        }

        // If page/ post check post_meta to see if lazyload is off for page.
        if ( false === $no_meta && false === autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_lazyload' ) ) {
              $lazyload_return = false;
        }

        $lazyload_return = apply_filters( 'autoptimize_filter_imgopt_should_lazyload', $lazyload_return, $context );

        return $lazyload_return;
    }

    public static function check_nolazy() {
        if ( array_key_exists( 'ao_nolazy', $_GET ) && '1' === $_GET['ao_nolazy'] ) {
            return true;
        } else {
            return false;
        }
    }

    public function filter_lazyload_images( $in )
    {
        // only used is image optimization is NOT active but lazyload is.
        $to_replace = array();
        $to_preload = '';

        // hide (no)script tags to avoid nesting noscript tags (as lazyloaded images add noscript).
        $out = autoptimizeBase::replace_contents_with_marker_if_exists(
            'SCRIPT',
            '<script',
            '#<(?:no)?script.*?<\/(?:no)?script>#is',
            $in
        );

        // get img preloads as set in post metabox.
        $metabox_preloads = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_preload' ) ) ) ) );

        // extract img tags and add lazyload attribs/ add preloads.
        if ( preg_match_all( '#<img[^>]*src[^>]*>#Usmi', $out, $matches ) ) {
            foreach ( $matches[0] as $tag ) {
                // check if image needs to be preloaded.
                if ( ! empty( $metabox_preloads ) && is_array( $metabox_preloads ) && str_replace( $metabox_preloads, '', $tag ) !== $tag ) {
                    $to_preload .= $this->create_img_preload_tag( $tag );
                }

                // and lazyloaded.
                if ( $this->should_lazyload( $out ) ) {
                    $to_replace[ $tag ] = $this->add_lazyload( $tag );
                }
            }
            $out = str_replace( array_keys( $to_replace ), array_values( $to_replace ), $out );
        }

        // and also lazyload picture tag.
        $out = $this->process_picture_tag( $out, false, true );

        // and inline style blocks with background-image.
        $out = $this->process_bgimage( $out );

        // restore noscript tags.
        $out = autoptimizeBase::restore_marked_content(
            'SCRIPT',
            $out
        );

        if ( ! empty( $metabox_preloads ) && is_array( $metabox_preloads ) && empty( $to_preload ) && false !== apply_filters( 'autoptimize_filter_imgopt_dopreloads', true ) ) {
            // the preload was not in an img tag, so adding a non-responsive preload instead.
            foreach ( $metabox_preloads as $img_preload ) {
                $to_preload .= '<link rel="preload" href="' . $img_preload . '" as="image">';
            }
        }

        if ( ! empty( $to_preload ) ) {
            $out = autoptimizeExtra::inject_preloads( $to_preload, $out );
        }

        return $out;
    }

    public function add_lazyload( $tag, $placeholder = '' ) {
        // adds actual lazyload-attributes to an image node.
        $this->lazyload_counter++;

        $_lazyload_from_nth = '';
        if ( array_key_exists( 'autoptimize_imgopt_number_field_7', $this->options ) ) {
            $_lazyload_from_nth = $this->options['autoptimize_imgopt_number_field_7'];
        }
        $_lazyload_from_nth = apply_filters( 'autoptimize_filter_imgopt_lazyload_from_nth', $_lazyload_from_nth );

        if ( str_ireplace( $this->get_lazyload_exclusions(), '', $tag ) === $tag && $this->lazyload_counter >= $_lazyload_from_nth ) {
            $tag = $this->maybe_fix_missing_quotes( $tag );

            // store original tag for use in noscript version.
            $noscript_tag = '<noscript>' . autoptimizeUtils::remove_id_from_node( $tag ) . '</noscript>';

            $lazyload_class = apply_filters( 'autoptimize_filter_imgopt_lazyload_class', 'lazyload' );

            // insert lazyload class.
            $tag = $this->inject_classes_in_tag( $tag, "$lazyload_class " );

            if ( ! $placeholder || empty( $placeholder ) ) {
                // get image width & heigth for placeholder fun (and to prevent content reflow).
                $_get_size = $this->get_size_from_tag( $tag );
                $width     = $_get_size['width'];
                $height    = $_get_size['height'];
                if ( false === $width || empty( $width ) ) {
                    $width = 210; // default width for SVG placeholder.
                }
                if ( false === $height || empty( $height ) ) {
                    $height = $width / 3 * 2; // if no height, base it on width using the 3/2 aspect ratio.
                }

                // insert the actual lazyload stuff.
                // see https://css-tricks.com/preventing-content-reflow-from-lazy-loaded-images/ for great read on why we're using empty svg's.
                $placeholder = apply_filters( 'autoptimize_filter_imgopt_lazyload_placeholder', $this->get_default_lazyload_placeholder( $width, $height ) );
            }

            $tag = preg_replace( '/(\s)src=/', ' src=\'' . $placeholder . '\' data-src=', $tag );
            $tag = preg_replace( '/(\s)srcset=/', ' data-srcset=', $tag );

            // move sizes to data-sizes unless filter says no.
            if ( apply_filters( 'autoptimize_filter_imgopt_lazyload_move_sizes', true ) ) {
                $tag = str_replace( ' sizes=', ' data-sizes=', $tag );
            }

            // add the noscript-tag from earlier.
            $tag = $noscript_tag . $tag;
            $tag = apply_filters( 'autoptimize_filter_imgopt_lazyloaded_img', $tag );
        } else {
            $tag = apply_filters( 'autoptimize_filter_imgopt_not_lazyloaded_img', $tag );
        }

        return $tag;
    }

    public function add_lazyload_js_footer() {
        if ( false === autoptimizeMain::should_buffer() || autoptimizeMain::is_amp_markup( '' ) ) {
            return;
        }

        // The JS will by default be excluded form autoptimization but this can be changed with a filter.
        $noptimize_flag = '';
        if ( apply_filters( 'autoptimize_filter_imgopt_lazyload_js_noptimize', true ) ) {
            $noptimize_flag = ' data-noptimize="1"';
        }

        $_extra = autoptimizeOptionWrapper::get_option( 'autoptimize_extra_settings', '' );
        if ( is_array( $_extra ) && array_key_exists( 'autoptimize_extra_checkbox_field_0', $_extra ) && ! empty( $_extra['autoptimize_extra_checkbox_field_0'] ) ) {
            // if "remove query strings" is active in "extra", then let's be consistant and not add one ourselves :-) ?
            $lazysizes_js = plugins_url( 'external/js/lazysizes.min.js', __FILE__ );
        } else {
            $lazysizes_js = plugins_url( 'external/js/lazysizes.min.js?ao_version=' . AUTOPTIMIZE_PLUGIN_VERSION, __FILE__ );
        }

        $cdn_url      = $this->get_cdn_url();
        if ( ! empty( $cdn_url ) ) {
            $cdn_url      = rtrim( $cdn_url, '/' );
            $lazysizes_js = str_replace( AUTOPTIMIZE_WP_SITE_URL, $cdn_url, $lazysizes_js );
        }

        $type_js = '';
        if ( apply_filters( 'autoptimize_filter_cssjs_addtype', false ) ) {
            $type_js = ' type="text/javascript"';
        }

        // Adds lazyload CSS & JS to footer, using echo because wp_enqueue_script seems not to support pushing attributes (async).
        echo apply_filters( 'autoptimize_filter_imgopt_lazyload_cssoutput', '<noscript><style>.lazyload{display:none;}</style></noscript>' );
        echo apply_filters( 'autoptimize_filter_imgopt_lazyload_jsconfig', '<script' . $type_js . $noptimize_flag . '>window.lazySizesConfig=window.lazySizesConfig||{};window.lazySizesConfig.loadMode=1;</script>' );
        echo apply_filters( 'autoptimize_filter_imgopt_lazyload_js', '<script async' . $type_js . $noptimize_flag . ' src=\'' . $lazysizes_js . '\'></script>' );
    }

    public static function create_img_preload_tag( $tag ) {
        if ( false === apply_filters( 'autoptimize_filter_imgopt_dopreloads', true ) ) {
            return '';
        }

        // clean up; remove tabs/ linebreaks/ spaces.
        $tag = preg_replace( '/\s+/', ' ', $tag );
        
        // remove noscript.
        if ( false !== strpos( $tag, '<noscript' ) ) {
            $tag = preg_replace( '/<noscript.*<\/noscript>/mU', '', $tag );
        }

        // rewrite img tag to link preload img.
        $_from = array( '<img ', ' src=', ' sizes=', ' srcset=' );
        $_to   = array( '<link rel="preload" as="image" ', ' href=', ' imagesizes=', ' imagesrcset=' );
        $tag   = str_replace( $_from, $_to, $tag );

        // and remove title, alt, class and id.
        $tag = preg_replace( '/ ((?:title|alt|class|id|loading|fetchpriority|decoding|data-no-lazy|width|height)=".*")/Um', '', $tag );
        if ( str_replace( array( ' title=', ' class=', ' alt=', ' id=', ' fetchpriority=', ' decoding=', ' data-no-lazy=' ), '', $tag ) !== $tag ) {
            // 2nd regex pass if still title/ class/ alt in case single quotes were used iso doubles.
            $tag = preg_replace( '/ ((?:title|alt|class|id|loading|fetchpriority|decoding|data-no-lazy)=\'.*\')/Um', '', $tag );
        }

        return $tag;
    }

    public static function get_cdn_url() {
        // getting CDN url here to avoid having to make bigger changes to autoptimizeBase.
        static $cdn_url = null;

        if ( null === $cdn_url ) {
            $cdn_url = autoptimizeOptionWrapper::get_option( 'autoptimize_cdn_url', '' );
            $cdn_url = autoptimizeUtils::tweak_cdn_url_if_needed( $cdn_url );
            $cdn_url = apply_filters( 'autoptimize_filter_base_cdnurl', $cdn_url );
        }

        return $cdn_url;
    }

    public function get_lazyload_exclusions() {
        // returns array of strings that if found in an <img tag will stop the img from being lazy-loaded.
        static $exclude_lazyload_array = null;

        if ( null === $exclude_lazyload_array ) {
            $options = $this->options;

            // set default exclusions.
            $exclude_lazyload_array = array( 'skip-lazy', 'data-no-lazy', 'notlazy', 'data-src', 'data-srcset', 'data:image/', 'data-lazyload', 'rev-slidebg', 'loading="eager"' );

            // add from setting.
            if ( array_key_exists( 'autoptimize_imgopt_text_field_5', $options ) ) {
                $exclude_lazyload_option = $options['autoptimize_imgopt_text_field_5'];
                if ( ! empty( $exclude_lazyload_option ) ) {
                    $exclude_lazyload_array = array_merge( $exclude_lazyload_array, array_filter( array_map( 'trim', explode( ',', $options['autoptimize_imgopt_text_field_5'] ) ) ) );
                }
            }

            // and filter for developer-initiated changes.
            $exclude_lazyload_array = apply_filters( 'autoptimize_filter_imgopt_lazyload_exclude_array', $exclude_lazyload_array );
        }

        return $exclude_lazyload_array;
    }

    public function inject_classes_in_tag( $tag, $target_class ) {
        if ( strpos( $tag, 'class=' ) !== false ) {
            $tag = preg_replace( '/(\sclass\s?=\s?("|\'))/', '$1' . $target_class, $tag );
        } else {
            $tag = preg_replace( '/(<[a-zA-Z]*)\s/', '$1 class="' . trim( $target_class ) . '" ', $tag );
        }

        return $tag;
    }

    public function get_default_lazyload_placeholder( $imgopt_w, $imgopt_h ) {
        return 'data:image/svg+xml,%3Csvg%20xmlns=%22http://www.w3.org/2000/svg%22%20viewBox=%220%200%20' . $imgopt_w . '%20' . $imgopt_h . '%22%3E%3C/svg%3E';
    }

    public function should_ngimg() {
        static $ngimg_return = null;

        if ( is_null( $ngimg_return ) ) {
            // nextgen img only works if imgopt is active.
            if ( ! empty( $this->options['autoptimize_imgopt_checkbox_field_4'] ) && $this->imgopt_active() ) {
                $ngimg_return = true;
            } else {
                $ngimg_return = false;
            }
        }

        return $ngimg_return;
    }

    public function process_picture_tag( $in, $imgopt = false, $lazy = false ) {
        // check if "<picture" is present and if filter allows us to process <picture>.
        if ( strpos( $in, '<picture' ) === false || apply_filters( 'autoptimize_filter_imgopt_dopicture', true ) === false ) {
            return $in;
        }

        $_exclusions     = $this->get_lazyload_exclusions();
        $to_replace_pict = array();

        // extract and process each picture-node.
        preg_match_all( '#<picture.*</picture>#Usmi', $in, $_pictures, PREG_SET_ORDER );
        foreach ( $_pictures as $_picture ) {
            $_picture = $this->maybe_fix_missing_quotes( $_picture );
            if ( strpos( $_picture[0], '<source ' ) !== false && preg_match_all( '#<source .*srcset=(?:"|\')(?!data)(.*)(?:"|\').*>#Usmi', $_picture[0], $_sources, PREG_SET_ORDER ) !== false ) {
                foreach ( $_sources as $_source ) {
                    $_picture_replacement = $_source[0];

                    // should we optimize the image?
                    if ( $imgopt && $this->can_optimize_image( $_source[1], $_picture[0] ) ) {
                        $_picture_replacement = str_replace( $_source[1], $this->build_imgopt_url( $_source[1] ), $_picture_replacement );
                    }
                    // should we lazy-load?
                    if ( $lazy && $this->should_lazyload() && str_ireplace( $_exclusions, '', $_picture_replacement ) === $_picture_replacement ) {
                        $_picture_replacement = str_replace( ' srcset=', ' data-srcset=', $_picture_replacement );
                    }
                    $to_replace_pict[ $_source[0] ] = $_picture_replacement;
                }
            }
        }

        // and return the fully procesed $in.
        $out = str_replace( array_keys( $to_replace_pict ), array_values( $to_replace_pict ), $in );

        return $out;
    }

    public function process_bgimage( $in ) {
        if ( strpos( $in, 'background-image:' ) !== false && apply_filters( 'autoptimize_filter_imgopt_lazyload_backgroundimages', true ) ) {
            $out = preg_replace_callback(
                '/(<(?:article|aside|body|div|footer|header|p|section|span|table)[^>]*)\sstyle=(?:"|\')[^<>]*?background-image:\s?url\((?:"|\')?([^"\')]*)(?:"|\')?\)[^>]*/',
                array( $this, 'lazyload_bgimg_callback' ),
                $in
            );
            return $out;
        }
        return $in;
    }

    public function lazyload_bgimg_callback( $matches ) {
        if ( str_ireplace( $this->get_lazyload_exclusions(), '', $matches[0] ) === $matches[0] ) {
            // get placeholder & lazyload class strings.
            $placeholder    = apply_filters( 'autoptimize_filter_imgopt_lazyload_placeholder', $this->get_default_lazyload_placeholder( 500, 300 ) );
            $lazyload_class = apply_filters( 'autoptimize_filter_imgopt_lazyload_class', 'lazyload' );
            // remove quotes from url() to be able to replace in next step.
            $out = str_replace( array( "url('" . $matches[2] . "')", 'url("' . $matches[2] . '")' ), 'url(' . $matches[2] . ')', $matches[0] );
            // replace background-image URL with SVG placeholder.
            $out = str_replace( 'url(' . $matches[2], 'url(' . $placeholder, $out );
            // sanitize bgimg src for quote sillyness.
            $bgimg_src = $this->fix_silly_bgimg_quotes( $matches[2] );
            // add data-bg attribute with real background-image URL for lazyload to pick up.
            $out = str_replace( $matches[1], $matches[1] . ' data-bg="' . $bgimg_src . '"', $out );
            // and finally add lazyload class to tag.
            $out = $this->inject_classes_in_tag( $out, "$lazyload_class " );
            return $out;
        }
        return $matches[0];
    }

    public function fix_silly_bgimg_quotes( $tag_in ) {
        // some themes/ pagebuilders wrap backgroundimages in HTML-encoded quotes (or linebreaks) which breaks imgopt/ lazyloading, this removes them.
        return trim( str_replace( array( "\r\n", '"', '&quot;', '&#034;', '&apos;', '&#039;' ), '', $tag_in ) );
    }

    public function maybe_fix_missing_quotes( $tag_in ) {
        // W3TC's Minify_HTML class removes quotes around attribute value, this re-adds them for the class and width/height attributes so we can lazyload properly.
        if ( file_exists( WP_PLUGIN_DIR . '/w3-total-cache/w3-total-cache.php' ) && class_exists( 'Minify_HTML' ) && apply_filters( 'autoptimize_filter_imgopt_fixquotes', true ) ) {
            $tag_out = preg_replace( '/class\s?=([^("|\')]*)(\s|>)/U', 'class=\'$1\'$2', $tag_in );
            $tag_out = preg_replace( '/\s(width|height)=(?:"|\')?([^\s"\'>]*)(?:"|\')?/', ' $1=\'$2\'', $tag_out );
            return $tag_out;
        } else {
            return $tag_in;
        }
    }

    /**
     * Admin page logic and related functions below.
     */
    public function imgopt_admin_menu()
    {
        // no acces if multisite and not network admin and no site config allowed.
        if ( autoptimizeConfig::should_show_menu_tabs() ) {
            add_submenu_page(
                '',
                'autoptimize_imgopt',
                'autoptimize_imgopt',
                'manage_options',
                'autoptimize_imgopt',
                array( $this, 'imgopt_options_page' )
            );
        }
        register_setting( 'autoptimize_imgopt_settings', 'autoptimize_imgopt_settings' );
    }

    public function add_imgopt_tab( $in )
    {
        if ( autoptimizeConfig::should_show_menu_tabs() ) {
            $in = array_merge( $in, array( 'autoptimize_imgopt' => apply_filters( 'autoptimize_filter_imgopt_tab_text', __( 'Images', 'autoptimize' ) ) ) );
        }

        return $in;
    }

    public function imgopt_options_page()
    {
        // phpcs:disable Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace
        // phpcs:disable Generic.Formatting.DisallowMultipleStatements.SameLine

        // Check querystring for "refreshCacheChecker" and call cachechecker if so.
        if ( array_key_exists( 'refreshImgProvStats', $_GET ) && 1 == $_GET['refreshImgProvStats'] ) {
            $this->query_img_provider_stats( true );
        }

        $options       = $this->fetch_options();
        $sp_url_suffix = $this->get_service_url_suffix();
        ?>
    <style>
        #ao_settings_form {background: white;border: 1px solid #ccc;padding: 1px 15px;margin: 15px 10px 10px 0;}
        #ao_settings_form .form-table th {font-weight: normal;}
        #autoptimize_imgopt_descr{font-size: 120%;}
    </style>
    <script>document.title = "Autoptimize: <?php _e( 'Images', 'autoptimize' ); ?> " + document.title;</script>
    <div class="wrap">
    <h1><?php apply_filters( 'autoptimize_filter_settings_is_pro', false ) ? _e( 'Autoptimize Pro Settings', 'autoptimize' ) : _e( 'Autoptimize Settings', 'autoptimize' ); ?></h1>
        <?php echo autoptimizeConfig::ao_admin_tabs(); ?>
        <?php if ( autoptimizeUtils::is_local_server() ) { ?>
            <div class="notice-warning notice"><p>
            <?php
            echo __( 'The image optimization service does not work on locally hosted sites or when the server is on a private network.', 'autoptimize' );
            ?>
            </p></div>
        <?php } ?>
        <?php if ( 'down' === $options['availabilities']['extra_imgopt']['status'] ) { ?>
            <div class="notice-warning notice"><p>
            <?php
            // translators: "Autoptimize support forum" will appear in a "a href".
            echo sprintf( __( 'The image optimization service is currently down, image optimization will be skipped until further notice. Check the %1$sAutoptimize support forum%2$s for more info.', 'autoptimize' ), '<a href="https://wordpress.org/support/plugin/autoptimize/" target="_blank">', '</a>' );
            ?>
            </p></div>
        <?php } ?>

        <?php if ( 'launch' === $options['availabilities']['extra_imgopt']['status'] && ! autoptimizeImages::instance()->launch_ok() ) { ?>
            <div class="notice-warning notice"><p>
            <?php _e( 'The image optimization service is launching, but not yet available for this domain, it should become available in the next couple of days.', 'autoptimize' ); ?>
            </p></div>
        <?php } ?>

        <?php if ( class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'get_active_modules' ) && in_array( 'photon', Jetpack::get_active_modules() ) ) { ?>
            <div class="notice-warning notice"><p>
            <?php
            // translators: "disable  Jetpack's site accelerator for images" will appear in a "a href" linking to the jetpack settings page.
            echo sprintf( __( 'Please %1$sdisable Jetpack\'s site accelerator for images%2$s to be able to use Autoptomize\'s advanced image optimization features below.', 'autoptimize' ), '<a href="admin.php?page=jetpack#/settings">', '</a>' );
            ?>
            </p></div>
        <?php } ?>
    <form id='ao_settings_form' action='<?php echo admin_url( 'options.php' ); ?>' method='post'>
        <?php settings_fields( 'autoptimize_imgopt_settings' ); ?>
        <h2><?php _e( 'Image optimization', 'autoptimize' ); ?></h2>
        <span id='autoptimize_imgopt_descr'><?php echo apply_filters( 'autoptimize_filter_imgopt_intro_copy', __( 'Make your site significantly faster by simply ticking a few boxes and start serving CDN powered, optimized images in next-get formats like WebP and AVIF! No additional plugins or services needed.', 'autoptimize' ) ); ?></span>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Image optimization & CDN', 'autoptimize' ); ?></th>
                <td>
                    <label><input id='autoptimize_imgopt_checkbox' type='checkbox' name='autoptimize_imgopt_settings[autoptimize_imgopt_checkbox_field_1]' <?php if ( ! empty( $options['autoptimize_imgopt_checkbox_field_1'] ) && '1' === $options['autoptimize_imgopt_checkbox_field_1'] ) { echo 'checked="checked"'; } ?> value='1'><?php echo apply_filters( 'autoptimize_filter_imgopt_main_setting_copy', __( 'On-the-fly image optimization and fast delivery via the Shortpixel global CDN.', 'autoptimize' ) ); ?></label>
                    <?php
                    // show shortpixel status.
                    $_notice = autoptimizeImages::instance()->get_imgopt_status_notice();
                    if ( $_notice ) {
                        switch ( $_notice['status'] ) {
                            case 2:
                                $_notice_color = 'green';
                                break;
                            case 1:
                                $_notice_color = 'orange';
                                break;
                            case -1:
                            case -2:
                            case -3:
                                $_notice_color = 'red';
                                break;
                            default:
                                $_notice_color = 'green';
                        }
                        echo apply_filters( 'autoptimize_filter_imgopt_settings_status', '<p><strong><span style="color:' . $_notice_color . ';">' . __( 'Shortpixel status: ', 'autoptimize' ) . '</span></strong>' . $_notice['notice'] . '</p>' );
                    } else {
                        // translators: link points to shortpixel.
                        $upsell_msg_1 = '<p>' . sprintf( __( 'Get more Google love by speeding up your website. Start serving on-the-fly optimized images (also in the "next-gen" <strong>WebP</strong> and <strong>AVIF</strong> image formats) by %1$sShortPixel%2$s. No additional image optimization plugins are needed: your images are optimized, cached and served from %3$sShortPixel\'s global CDN%2$s.', 'autoptimize' ), '<a href="https://misc.optimizingmatters.com/partners/?from=aofree&partner=shortpixelupsell" target="_blank">', '</a>', '<a href="https://help.shortpixel.com/article/62-where-does-the-cdn-has-pops" target="_blank">' );
                        if ( 'launch' === $options['availabilities']['extra_imgopt']['status'] ) {
                            $upsell_msg_2 = __( 'For a limited time only, this service is offered free for all Autoptimize users, <b>don\'t miss the chance to test it</b> and see how much it could improve your site\'s speed.', 'autoptimize' );
                        } else {
                            // translators: 1st link points to autoptimize.com.pro, 2nd to shortpixel.
                            $upsell_msg_2 = sprintf( __( 'For <strong>unlimited image optimizations %1$sbuy Autoptimize Pro%2$s</strong> which also includes Critical CSS and extra "booster" options or %3$ssign up at Shortpixel%4$s.', 'autoptimize' ), '<a href="https://autoptimize.com/pro/" target="_blank">', '</a>', '<a href="https://autoptimize.shortpixel.com/' . $sp_url_suffix . '" target="_blank">', '</a>' );
                        }
                        echo apply_filters( 'autoptimize_filter_imgopt_settings_copy', $upsell_msg_1 . ' ' . $upsell_msg_2 . '</p>' );
                    }
                    // translators: link points to shortpixel FAQ.
                    $faqcopy = sprintf( __( '<strong>Questions</strong>? Take a look at the %1$sAutoptimize + ShortPixel FAQ%2$s!', 'autoptimize' ), '<strong><a href="https://help.shortpixel.com/category/405-autoptimize" target="_blank">', '</strong></a>' );
                    $faqcopy = $faqcopy . ' ' . __( 'Only works for websites and images that are publicly available.', 'autoptimize' );
                    // translators: links points to shortpixel TOS & Privacy Policy.
                    $toscopy = sprintf( __( 'Usage of this feature is subject to Shortpixel\'s %1$sTerms of Use%2$s and %3$sPrivacy policy%4$s.', 'autoptimize' ), '<a href="https://shortpixel.com/tos' . $sp_url_suffix . '" target="_blank">', '</a>', '<a href="https://shortpixel.com/privacy' . $sp_url_suffix . '" target="_blank">', '</a>' );
                    echo apply_filters( 'autoptimize_filter_imgopt_settings_tos', '<p>' . $faqcopy . ' ' . $toscopy . '</p>' );
                    ?>
                </td>
            </tr>
            <tr id='autoptimize_imgopt_optimization_exclusions' <?php if ( ! array_key_exists( 'autoptimize_imgopt_checkbox_field_1', $options ) || ( isset( $options['autoptimize_imgopt_checkbox_field_1'] ) && '1' !== $options['autoptimize_imgopt_checkbox_field_1'] ) ) { echo 'class="hidden"'; } ?>>
                <th scope="row"><?php _e( 'Optimization exclusions', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='text' style='width:80%' id='autoptimize_imgopt_optimization_exclusions' name='autoptimize_imgopt_settings[autoptimize_imgopt_text_field_6]' value='<?php if ( ! empty( $options['autoptimize_imgopt_text_field_6'] ) ) { echo esc_attr( $options['autoptimize_imgopt_text_field_6'] ); } ?>'><br /><?php _e( 'Comma-separated list of image classes or filenames that should not be optimized.', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr id='autoptimize_imgopt_quality' <?php if ( ! array_key_exists( 'autoptimize_imgopt_checkbox_field_1', $options ) || ( isset( $options['autoptimize_imgopt_checkbox_field_1'] ) && '1' !== $options['autoptimize_imgopt_checkbox_field_1'] ) ) { echo 'class="hidden"'; } ?>>
                <th scope="row"><?php _e( 'Image Optimization quality', 'autoptimize' ); ?></th>
                <td>
                    <label>
                    <select name='autoptimize_imgopt_settings[autoptimize_imgopt_select_field_2]'>
                        <?php
                        $_imgopt_array = autoptimizeImages::instance()->get_img_quality_array();
                        $_imgopt_val   = autoptimizeImages::instance()->get_img_quality_setting();

                        foreach ( $_imgopt_array as $key => $value ) {
                            echo '<option value="' . $key . '"';
                            if ( $_imgopt_val == $key ) {
                                echo ' selected';
                            }
                            echo '>' . ucfirst( $value ) . '</option>';
                        }
                        echo "\n";
                        ?>
                    </select>
                    </label>
                    <p>
                        <?php
                            // translators: link points to shortpixel image test page.
                            echo apply_filters( 'autoptimize_filter_imgopt_quality_copy', sprintf( __( 'You can %1$stest compression levels here%2$s.', 'autoptimize' ), '<a href="https://shortpixel.com/online-image-compression' . $sp_url_suffix . '" target="_blank">', '</a>' ) );
                        ?>
                    </p>
                </td>
            </tr>
            <?php
            if ( apply_filters( 'autoptimize_filter_imgopt_settings_show_avif', true ) ) {
                ?>
                <tr id='autoptimize_imgopt_ngimg' <?php if ( ! array_key_exists( 'autoptimize_imgopt_checkbox_field_1', $options ) || ( isset( $options['autoptimize_imgopt_checkbox_field_1'] ) && '1' !== $options['autoptimize_imgopt_checkbox_field_1'] ) ) { echo 'class="hidden"'; } ?>>
                    <th scope="row"><?php _e( 'Load AVIF in supported browsers?', 'autoptimize' ); ?></th>
                    <td>
                        <label><input type='checkbox' id='autoptimize_imgopt_ngimg_checkbox' name='autoptimize_imgopt_settings[autoptimize_imgopt_checkbox_field_4]' <?php if ( ! empty( $options['autoptimize_imgopt_checkbox_field_4'] ) && '1' === $options['autoptimize_imgopt_checkbox_field_4'] ) { echo 'checked="checked"'; } ?> value='1'><?php _e( 'Automatically serve AVIF image format to any browser that supports it.', 'autoptimize' ); ?></label>
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <input type='hidden' id='autoptimize_imgopt_ngimg_checkbox' name='autoptimize_imgopt_settings[autoptimize_imgopt_checkbox_field_4]' value='0'>
                <?php
            }
            ?>
            <tr>
                <th scope="row"><?php _e( 'Lazy-load images?', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='checkbox' id='autoptimize_imgopt_lazyload_checkbox' name='autoptimize_imgopt_settings[autoptimize_imgopt_checkbox_field_3]' <?php if ( ! empty( $options['autoptimize_imgopt_checkbox_field_3'] ) && '1' === $options['autoptimize_imgopt_checkbox_field_3'] ) { echo 'checked="checked"'; } ?> value='1'><?php _e( 'Image lazy-loading will delay the loading of non-visible images to allow the browser to optimally load all resources for the "above the fold"-page first.', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr id='autoptimize_imgopt_lazyload_exclusions' <?php if ( ! array_key_exists( 'autoptimize_imgopt_checkbox_field_3', $options ) || ( isset( $options['autoptimize_imgopt_checkbox_field_3'] ) && '1' !== $options['autoptimize_imgopt_checkbox_field_3'] ) ) { echo 'class="autoptimize_lazyload_child hidden"'; } else { echo 'class="autoptimize_lazyload_child"'; } ?>>
                <th scope="row"><?php _e( 'Lazy-load exclusions', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='text' style='width:80%' id='autoptimize_imgopt_lazyload_exclusions_text' name='autoptimize_imgopt_settings[autoptimize_imgopt_text_field_5]' value='<?php if ( ! empty( $options['autoptimize_imgopt_text_field_5'] ) ) { echo esc_attr( $options['autoptimize_imgopt_text_field_5'] ); } ?>'><br /><?php _e( 'Comma-separated list of to be excluded image classes or filenames.', 'autoptimize' ); ?></label>
                </td>
            </tr>
            <tr id='autoptimize_imgopt_lazyload_from_nth_image' <?php if ( ! array_key_exists( 'autoptimize_imgopt_checkbox_field_3', $options ) || ( isset( $options['autoptimize_imgopt_checkbox_field_3'] ) && '1' !== $options['autoptimize_imgopt_checkbox_field_3'] ) ) { echo 'class="autoptimize_lazyload_child hidden"'; } else { echo 'class="autoptimize_lazyload_child"'; } ?>>
                <th scope="row"><?php _e( 'Lazy-load from nth image', 'autoptimize' ); ?></th>
                <td>
                    <label><input type='number' min='0' max='50' style='width:80%' id='autoptimize_imgopt_lazyload_from_nth_image_number' name='autoptimize_imgopt_settings[autoptimize_imgopt_number_field_7]' value='<?php if ( ! empty( $options['autoptimize_imgopt_number_field_7'] ) ) { echo esc_attr( $options['autoptimize_imgopt_number_field_7'] ); } else { echo '1'; } ?>'><br /><?php _e( 'Don\'t lazyload the first X images, \'1\' lazyloads all.', 'autoptimize' ); ?></label>
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'autoptimize' ); ?>" /></p>
    </form>
    <script>
        jQuery(document).ready(function() {
            jQuery("#autoptimize_imgopt_checkbox").change(function() {
                if (this.checked) {
                    jQuery("#autoptimize_imgopt_quality").show("slow");
                    jQuery("#autoptimize_imgopt_ngimg").show("slow");
                    jQuery("#autoptimize_imgopt_optimization_exclusions").show("slow");
                } else {
                    jQuery("#autoptimize_imgopt_quality").hide("slow");
                    jQuery("#autoptimize_imgopt_ngimg").hide("slow");
                    jQuery("#autoptimize_imgopt_optimization_exclusions").hide("slow");
                }
            });
            jQuery("#autoptimize_imgopt_lazyload_checkbox").change(function() {
                if (this.checked) {
                    jQuery(".autoptimize_lazyload_child").show("slow");
                } else {
                    jQuery(".autoptimize_lazyload_child").hide("slow");
                }
            });
        });
    </script>
        <?php
    }

    /**
     * Ïmg opt status as used on dashboard.
     */
    public function get_imgopt_status_notice() {
        if ( $this->imgopt_active() && apply_filters( 'autoptimize_filter_imgopt_status_shortpixel', true ) ) {
            $_imgopt_notice  = '';
            $_stat           = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_provider_stat', '' );
            $_site_host      = AUTOPTIMIZE_SITE_DOMAIN;
            $_imgopt_upsell  = 'https://misc.optimizingmatters.com/partners/?from=aofree&partner=shortpixelupsell';
            $_imgopt_assoc   = 'https://shortpixel.helpscoutdocs.com/article/94-how-to-associate-a-domain-to-my-account';
            $_imgopt_unreach = 'https://shortpixel.helpscoutdocs.com/article/148-why-are-my-images-redirected-from-cdn-shortpixel-ai';

            if ( is_array( $_stat ) ) {
                if ( 1 == $_stat['Status'] ) {
                    // translators: "add more credits" will appear in a "a href".
                    $_imgopt_notice = sprintf( __( 'Your ShortPixel image optimization and CDN quota is almost used, make sure you %1$sadd more credits%2$s to avoid slowing down your website <strong>or consider using %3$sAutoptimize Pro%2$s which comes with (nearly) unlimited image optimization</strong> but also automated critical CSS and extra booster options.', 'autoptimize' ), '<a href="' . $_imgopt_upsell . '" target="_blank">', '</a>', '<a href="https://autoptimize.com/pro/" target="_blank">' );
                } elseif ( -1 == $_stat['Status'] || -2 == $_stat['Status'] ) {
                    // translators: "add more credits" will appear in a "a href".
                    $_imgopt_notice = sprintf( __( 'Your ShortPixel image optimization and CDN quota has been exhausted, %1$sadd more credits%2$s to continue to quickly deliver optimized images on your website <strong>or consider using %3$sAutoptimize Pro%2$s which comes with (nearly) unlimited image optimization</strong> but also automated critical CSS and extra booster options.', 'autoptimize' ), '<a href="' . $_imgopt_upsell . '" target="_blank">', '</a>', '<a href="https://autoptimize.com/pro/" target="_blank">' );
                    // translators: "associate your domain" will appear in a "a href".
                    $_imgopt_notice = $_imgopt_notice . ' ' . sprintf( __( 'If you have enough CDN quota remaining, then you may need to %1$sassociate your domain%2$s to your Shortpixel account.', 'autoptimize' ), '<a rel="noopener noreferrer" href="' . $_imgopt_assoc . '" target="_blank">', '</a>' );
                } elseif ( -3 == $_stat['Status'] ) {
                    // translators: "check the documentation here" will appear in a "a href".
                    $_imgopt_notice = sprintf( __( 'It seems ShortPixel image optimization is not able to fetch images from your site, %1$scheck the documentation here%2$s for more information', 'autoptimize' ), '<a href="' . $_imgopt_unreach . '" target="_blank">', '</a>' );
                } else {
                    $_imgopt_upsell = 'https://misc.optimizingmatters.com/partners/?from=aofree&partner=shortpixelupsell';
                    // translators: "log in to check your account" will appear in a "a href".
                    $_imgopt_notice = sprintf( __( 'Your ShortPixel image optimization and CDN quota are in good shape, %1$slog in to check your account%2$s.', 'autoptimize' ), '<a href="' . $_imgopt_upsell . '" target="_blank">', '</a>' );
                }

                // add info on freshness + refresh link if status is not 2 (good shape).
                if ( 2 != $_stat['Status'] ) {
                    $_imgopt_stats_refresh_url = add_query_arg(
                        array(
                            'page'                => 'autoptimize_imgopt',
                            'refreshImgProvStats' => '1',
                        ),
                        admin_url( 'options-general.php' )
                    );
                    if ( $_stat && array_key_exists( 'timestamp', $_stat ) && ! empty( $_stat['timestamp'] ) ) {
                        $_imgopt_stats_last_run = __( 'based on status at ', 'autoptimize' ) . date_i18n( autoptimizeOptionWrapper::get_option( 'time_format' ), $_stat['timestamp'] );
                    } else {
                        $_imgopt_stats_last_run = __( 'based on previously fetched data', 'autoptimize' );
                    }
                    $_imgopt_notice .= ' (' . $_imgopt_stats_last_run . ', ';
                    // translators: "here to refresh" links to the Autoptimize Extra page and forces a refresh of the img opt stats.
                    $_imgopt_notice .= sprintf( __( 'you can click %1$shere to refresh your quota status%2$s', 'autoptimize' ), '<a href="' . $_imgopt_stats_refresh_url . '">', '</a>).' );
                }

                // and make the full notice filterable.
                $_imgopt_notice = apply_filters( 'autoptimize_filter_imgopt_notice', $_imgopt_notice );

                return array(
                    'status' => $_stat['Status'],
                    'notice' => $_imgopt_notice,
                );
            }
        }
        return false;
    }

    public static function get_imgopt_status_notice_wrapper() {
        // needed for notice being shown in autoptimizeCacheChecker.php.
        $self = new self();
        return $self->get_imgopt_status_notice();
    }

    /**
     * Get img provider stats (used to display notice).
     *
     * @param bool $_refresh Should the stats be forcefully refreshed or not.
     */
    public function query_img_provider_stats( $_refresh = false ) {
        if ( ! empty( $this->options['autoptimize_imgopt_checkbox_field_1'] ) && apply_filters( 'autoptimize_filter_imgopt_status_shortpixel', true ) ) {
            $url      = '';
            $stat_dom = 'https://no-cdn.shortpixel.ai/';
            $endpoint = $stat_dom . 'read-domain/';
            $domain   = AUTOPTIMIZE_SITE_DOMAIN;

            // make sure parse_url result makes sense, keeping $url empty if not.
            if ( $domain && ! empty( $domain ) ) {
                $url = $endpoint . $domain;
                if ( true === $_refresh ) {
                    $url = $url . '/refresh';
                }
            }

            $url = apply_filters(
                'autoptimize_filter_imgopt_stat_url',
                $url
            );

            // only do the remote call if $url is not empty to make sure no parse_url
            // weirdness results in useless calls.
            if ( ! empty( $url ) ) {
                $response = wp_remote_get( $url );
                if ( ! is_wp_error( $response ) ) {
                    if ( '200' == wp_remote_retrieve_response_code( $response ) ) {
                        $stats = json_decode( wp_remote_retrieve_body( $response ), true );
                        autoptimizeOptionWrapper::update_option( 'autoptimize_imgopt_provider_stat', $stats );
                    }
                }
            }
        }
    }

    public static function get_img_provider_stats()
    {
        // wrapper around query_img_provider_stats() so we can get to $this->options from cronjob() in autoptimizeCacheChecker.
        $self = new self();
        return $self->query_img_provider_stats();
    }

    /**
     * Determines and returns the service launch status.
     *
     * @return bool
     */
    public function launch_ok()
    {
        static $launch_status = null;

        if ( null === $launch_status ) {
            $avail_imgopt = '';
            if ( is_array( $this->options ) && array_key_exists( 'availabilities', $this->options ) && is_array( $this->options['availabilities'] ) && array_key_exists( 'extra_imgopt', $this->options['availabilities'] ) ) {
                $avail_imgopt = $this->options['availabilities']['extra_imgopt'];
            }

            $magic_number  = intval( substr( md5( parse_url( AUTOPTIMIZE_WP_SITE_URL, PHP_URL_HOST ) ), 0, 3 ), 16 );
            $has_launched  = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_launched', '' );
            $launch_status = false;
            if ( $has_launched || ( is_array( $avail_imgopt ) && array_key_exists( 'launch-threshold', $avail_imgopt ) && $magic_number < $avail_imgopt['launch-threshold'] ) ) {
                $launch_status = true;
                if ( ! $has_launched ) {
                    autoptimizeOptionWrapper::update_option( 'autoptimize_imgopt_launched', 'on' );
                }
            }
        }

        return $launch_status;
    }

    public static function launch_ok_wrapper() {
        // needed for "plug" notice in autoptimizeMain.php.
        $self = new self();
        return $self->launch_ok();
    }

    public function get_imgopt_provider_userstatus() {
        static $_provider_userstatus = null;

        if ( is_null( $_provider_userstatus ) ) {
            $_stat = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_provider_stat', '' );
            if ( is_array( $_stat ) ) {
                if ( array_key_exists( 'Status', $_stat ) ) {
                    $_provider_userstatus['Status'] = $_stat['Status'];
                } else {
                    // if no stats then we assume all is well.
                    $_provider_userstatus['Status'] = 2;
                }
                if ( array_key_exists( 'timestamp', $_stat ) ) {
                    $_provider_userstatus['timestamp'] = $_stat['timestamp'];
                } else {
                    // if no timestamp then we return "".
                    $_provider_userstatus['timestamp'] = '';
                }
            } else {
                // no provider_stat yet, assume/ return all OK.
                $_provider_userstatus['Status']    = 2;
                $_provider_userstatus['timestamp'] = '';
            }
        }

        return $_provider_userstatus;
    }
}
