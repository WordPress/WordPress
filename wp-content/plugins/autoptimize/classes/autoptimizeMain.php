<?php
/**
 * Wraps base plugin logic/hooks and handles activation/deactivation/uninstall.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeMain
{
    const INIT_EARLIER_PRIORITY = -1;
    const DEFAULT_HOOK_PRIORITY = 2;

    /**
     * Version string.
     *
     * @var string
     */
    protected $version = null;

    /**
     * Main plugin filepath.
     * Used for activation/deactivation/uninstall hooks.
     *
     * @var string
     */
    protected $filepath = null;

    /**
     * Critical CSS base object
     *
     * @var object
     */
    protected $_criticalcss = null;

    /**
     * Constructor.
     *
     * @param string $version Version.
     * @param string $filepath Filepath. Needed for activation/deactivation/uninstall hooks.
     */
    public function __construct( $version, $filepath )
    {
        $this->version  = $version;
        $this->filepath = $filepath;
    }

    public function run()
    {
        $this->add_hooks();

        // Runs cache size checker.
        $checker = new autoptimizeCacheChecker();
        $checker->run();
    }

    protected function add_hooks()
    {
        if ( ! defined( 'AUTOPTIMIZE_SETUP_INITHOOK' ) ) {
            define( 'AUTOPTIMIZE_SETUP_INITHOOK', 'plugins_loaded' );
        }

        add_action( AUTOPTIMIZE_SETUP_INITHOOK, array( $this, 'setup' ) );
        add_action( AUTOPTIMIZE_SETUP_INITHOOK, array( $this, 'hook_page_cache_purge' ) );

        add_action( 'autoptimize_setup_done', array( $this, 'version_upgrades_check' ) );
        add_action( 'autoptimize_setup_done', array( $this, 'check_cache_and_run' ) );
        add_action( 'autoptimize_setup_done', array( $this, 'maybe_run_ao_compat' ), 10 );
        add_action( 'autoptimize_setup_done', array( $this, 'maybe_run_ao_extra' ), 15 );
        add_action( 'autoptimize_setup_done', array( $this, 'maybe_run_admin_only_trinkets' ), 20 );
        add_action( 'autoptimize_setup_done', array( $this, 'maybe_run_criticalcss' ), 11 );
        add_action( 'autoptimize_setup_done', array( $this, 'maybe_run_notfound_fallback' ), 10 );

        add_action( 'init', array( $this, 'load_textdomain' ) );

        if ( is_multisite() && is_admin() ) {
            // Only if multisite and if in admin we want to check if we need to save options on network level.
            add_action( 'init', 'autoptimizeOptionWrapper::check_multisite_on_saving_options' );
        }

        // register uninstall & deactivation hooks.
        register_uninstall_hook( $this->filepath, 'autoptimizeMain::on_uninstall' );
        register_deactivation_hook( $this->filepath, 'autoptimizeMain::on_deactivation' );
    }

    public function load_textdomain()
    {
        load_plugin_textdomain( 'autoptimize' );
    }

    public function setup()
    {
        // Do we gzip in php when caching or is the webserver doing it?
        define( 'AUTOPTIMIZE_CACHE_NOGZIP', (bool) autoptimizeOptionWrapper::get_option( 'autoptimize_cache_nogzip' ) );

        // These can be overridden by specifying them in wp-config.php or such.
        if ( ! defined( 'AUTOPTIMIZE_WP_CONTENT_NAME' ) ) {
            define( 'AUTOPTIMIZE_WP_CONTENT_NAME', '/' . wp_basename( WP_CONTENT_DIR ) );
        }
        if ( ! defined( 'AUTOPTIMIZE_CACHE_CHILD_DIR' ) ) {
            define( 'AUTOPTIMIZE_CACHE_CHILD_DIR', '/cache/autoptimize/' );
        }
        if ( ! defined( 'AUTOPTIMIZE_CACHEFILE_PREFIX' ) ) {
            define( 'AUTOPTIMIZE_CACHEFILE_PREFIX', 'autoptimize_' );
        }
        // Note: trailing slash is not optional!
        if ( ! defined( 'AUTOPTIMIZE_CACHE_DIR' ) ) {
            define( 'AUTOPTIMIZE_CACHE_DIR', autoptimizeCache::get_pathname() );
        }

        define( 'WP_ROOT_DIR', substr( WP_CONTENT_DIR, 0, strlen( WP_CONTENT_DIR ) - strlen( AUTOPTIMIZE_WP_CONTENT_NAME ) ) );

        if ( ! defined( 'AUTOPTIMIZE_WP_SITE_URL' ) ) {
            if ( function_exists( 'domain_mapping_siteurl' ) ) {
                define( 'AUTOPTIMIZE_WP_SITE_URL', domain_mapping_siteurl( get_current_blog_id() ) );
            } else {
                define( 'AUTOPTIMIZE_WP_SITE_URL', site_url() );
            }
        }
        if ( ! defined( 'AUTOPTIMIZE_WP_CONTENT_URL' ) ) {
            if ( function_exists( 'get_original_url' ) ) {
                define( 'AUTOPTIMIZE_WP_CONTENT_URL', str_replace( get_original_url( AUTOPTIMIZE_WP_SITE_URL ), AUTOPTIMIZE_WP_SITE_URL, content_url() ) );
            } else {
                define( 'AUTOPTIMIZE_WP_CONTENT_URL', content_url() );
            }
        }
        if ( ! defined( 'AUTOPTIMIZE_CACHE_URL' ) ) {
            if ( is_multisite() && apply_filters( 'autoptimize_separate_blog_caches', true ) ) {
                $blog_id = get_current_blog_id();
                define( 'AUTOPTIMIZE_CACHE_URL', AUTOPTIMIZE_WP_CONTENT_URL . AUTOPTIMIZE_CACHE_CHILD_DIR . $blog_id . '/' );
            } else {
                define( 'AUTOPTIMIZE_CACHE_URL', AUTOPTIMIZE_WP_CONTENT_URL . AUTOPTIMIZE_CACHE_CHILD_DIR );
            }
        }
        if ( ! defined( 'AUTOPTIMIZE_WP_ROOT_URL' ) ) {
            define( 'AUTOPTIMIZE_WP_ROOT_URL', str_replace( AUTOPTIMIZE_WP_CONTENT_NAME, '', AUTOPTIMIZE_WP_CONTENT_URL ) );
        }
        if ( ! defined( 'AUTOPTIMIZE_HASH' ) ) {
            define( 'AUTOPTIMIZE_HASH', wp_hash( AUTOPTIMIZE_CACHE_URL ) );
        }
        if ( ! defined( 'AUTOPTIMIZE_SITE_DOMAIN' ) ) {
            define( 'AUTOPTIMIZE_SITE_DOMAIN', parse_url( AUTOPTIMIZE_WP_SITE_URL, PHP_URL_HOST ) );
        }

        // Multibyte-capable string replacements are available with a filter.
        // Also requires 'mbstring' extension.
        $with_mbstring = apply_filters( 'autoptimize_filter_main_use_mbstring', false );
        if ( $with_mbstring ) {
            autoptimizeUtils::mbstring_available( \extension_loaded( 'mbstring' ) );
        } else {
            autoptimizeUtils::mbstring_available( false );
        }

        do_action( 'autoptimize_setup_done' );
    }

    /**
     * Checks if there's a need to upgrade/update options and whatnot,
     * in which case we might need to do stuff and flush the cache
     * to avoid old versions of aggregated files lingering around.
     */
    public function version_upgrades_check()
    {
        autoptimizeVersionUpdatesHandler::check_installed_and_update( $this->version );
    }

    public function check_cache_and_run()
    {
        if ( autoptimizeCache::cacheavail() ) {
            $conf = autoptimizeConfig::instance();
            if ( $conf->get( 'autoptimize_html' ) || $conf->get( 'autoptimize_js' ) || $conf->get( 'autoptimize_css' ) || autoptimizeImages::imgopt_active() || autoptimizeImages::should_lazyload_wrapper() ) {
                if ( ! defined( 'AUTOPTIMIZE_NOBUFFER_OPTIMIZE' ) ) {
                    // Hook into WordPress frontend.
                    if ( defined( 'AUTOPTIMIZE_INIT_EARLIER' ) ) {
                        add_action(
                            'init',
                            array( $this, 'start_buffering' ),
                            self::INIT_EARLIER_PRIORITY
                        );
                    } else {
                        if ( ! defined( 'AUTOPTIMIZE_HOOK_INTO' ) ) {
                            define( 'AUTOPTIMIZE_HOOK_INTO', 'template_redirect' );
                        }
                        add_action(
                            constant( 'AUTOPTIMIZE_HOOK_INTO' ),
                            array( $this, 'start_buffering' ),
                            self::DEFAULT_HOOK_PRIORITY
                        );
                    }
                }

                // And disable Jetpack's site accelerator if JS or CSS opt. are active.
                if ( class_exists( 'Jetpack' ) && apply_filters( 'autoptimize_filter_main_disable_jetpack_cdn', true ) && ( $conf->get( 'autoptimize_js' ) || $conf->get( 'autoptimize_css' ) ) ) {
                    add_filter( 'jetpack_force_disable_site_accelerator', '__return_true' );
                }

                // Add "no cache found" notice.
                add_action( 'admin_notices', 'autoptimizeMain::notice_nopagecache', 99 );
                add_action( 'admin_notices', 'autoptimizeMain::notice_potential_conflict', 99 );
            }
        } else {
            add_action( 'admin_notices', 'autoptimizeMain::notice_cache_unavailable' );
        }
    }

    public function maybe_run_ao_extra()
    {
        if ( apply_filters( 'autoptimize_filter_extra_activate', true ) ) {
            $ao_imgopt = new autoptimizeImages();
            $ao_imgopt->run();
            $ao_extra = new autoptimizeExtra();
            $ao_extra->run();

            // And show the imgopt notice.
            add_action( 'admin_notices', 'autoptimizeMain::notice_plug_imgopt' );
            add_action( 'admin_notices', 'autoptimizeMain::notice_imgopt_issue' );
        }
    }

    public function maybe_run_admin_only_trinkets()
    {
        // Loads partners tab and exit survey code if in admin (and not in admin-ajax.php)!
        if ( autoptimizeConfig::is_admin_and_not_ajax() ) {
            new autoptimizePartners();
            new autoptimizeExitSurvey();
        }
    }

    public function criticalcss()
    {
        if ( apply_filters( 'autoptimize_filter_criticalcss_active', true ) && ! autoptimizeUtils::is_plugin_active( 'autoptimize-criticalcss/ao_criticss_aas.php' ) ) {
            return $this->_criticalcss;
        } else {
            return false;
        }
    }

    public function maybe_run_criticalcss()
    {
        // Loads criticalcss if the filter returns true & old power-up is not active.
        if ( apply_filters( 'autoptimize_filter_criticalcss_active', true ) && ! autoptimizeUtils::is_plugin_active( 'autoptimize-criticalcss/ao_criticss_aas.php' ) ) {
            $this->_criticalcss = new autoptimizeCriticalCSSBase();
            $this->_criticalcss->setup();
            $this->_criticalcss->load_requires();
        }
    }

    public function maybe_run_notfound_fallback()
    {
        if ( autoptimizeCache::do_fallback() ) {
            add_action( 'template_redirect', array( 'autoptimizeCache', 'wordpress_notfound_fallback' ) );
        }
    }

    public function maybe_run_ao_compat()
    {
        // Condtionally loads the compatibility-class to ensure more out-of-the-box compatibility with big players.
        $_run_compat = true;

        if ( autoptimizeOptionWrapper::get_option( 'autoptimize_installed_before_compatibility', false ) ) {
            // If AO was already running before Compatibility logic was added, don't run compat by default
            // because it can be assumed everything works and we want to avoid (perf) regressions that
            // could occur due to compatibility code.
            $_run_compat = false;
        }

        if ( apply_filters( 'autoptimize_filter_init_compatibility', $_run_compat ) ) {
             new autoptimizeCompatibility();
        }
    }

    public function hook_page_cache_purge()
    {
        // hook into a collection of page cache purge actions if filter allows.
        if ( apply_filters( 'autoptimize_filter_main_hookpagecachepurge', true ) ) {
            $page_cache_purge_actions = array(
                'after_rocket_clean_domain', // exists.
                'hyper_cache_purged', // Stefano confirmed this will be added.
                'w3tc_flush_posts', // exits.
                'w3tc_flush_all', // exists.
                'ce_action_cache_cleared', // Sven confirmed this will be added.
                'aoce_action_cache_cleared', // Some other cache enabler.
                'comet_cache_wipe_cache', // still to be confirmed by Raam.
                'wp_cache_cleared', // cfr. https://github.com/Automattic/wp-super-cache/pull/537.
                'wpfc_delete_cache', // Emre confirmed this will be added this.
                'swift_performance_after_clear_all_cache', // swift perf. yeah!
                'wpo_cache_flush', // wp-optimize.
                'rt_nginx_helper_after_fastcgi_purge_all', // nginx helper.
            );
            $page_cache_purge_actions = apply_filters( 'autoptimize_filter_main_pagecachepurgeactions', $page_cache_purge_actions );
            foreach ( $page_cache_purge_actions as $purge_action ) {
                add_action( $purge_action, 'autoptimizeCache::clearall_actionless' );
            }
        }
    }

    /**
     * Setup output buffering if needed.
     *
     * @return void
     */
    public function start_buffering()
    {
        if ( $this->should_buffer() ) {

            // Load speedupper conditionally (true by default).
            if ( apply_filters( 'autoptimize_filter_speedupper', true ) ) {
                $ao_speedupper = new autoptimizeSpeedupper();
            }

            $conf = autoptimizeConfig::instance();

            if ( $conf->get( 'autoptimize_js' ) ) {
                if ( ! defined( 'CONCATENATE_SCRIPTS' ) ) {
                    define( 'CONCATENATE_SCRIPTS', false );
                }
                if ( ! defined( 'COMPRESS_SCRIPTS' ) ) {
                    define( 'COMPRESS_SCRIPTS', false );
                }
            }

            if ( $conf->get( 'autoptimize_css' ) ) {
                if ( ! defined( 'COMPRESS_CSS' ) ) {
                    define( 'COMPRESS_CSS', false );
                }
            }

            if ( apply_filters( 'autoptimize_filter_obkiller', false ) ) {
                while ( ob_get_level() > 0 ) {
                    ob_end_clean();
                }
            }

            // Now, start the real thing!
            ob_start( array( $this, 'end_buffering' ) );
        }
    }

    /**
     * Returns true if all the conditions to start output buffering are satisfied.
     *
     * @param bool $doing_tests Allows overriding the optimization of only
     *                          deciding once per request (for use in tests).
     * @return bool
     */
    public static function should_buffer( $doing_tests = false )
    {
        static $do_buffering = null;

        // Only check once in case we're called multiple times by others but
        // still allows multiple calls when doing tests.
        if ( null === $do_buffering || $doing_tests ) {

            $ao_noptimize = false;

            // Checking for DONOTMINIFY constant as used by e.g. WooCommerce POS.
            if ( defined( 'DONOTMINIFY' ) && ( constant( 'DONOTMINIFY' ) === true || constant( 'DONOTMINIFY' ) === 'true' ) ) {
                $ao_noptimize = true;
            }

            // Skip checking query strings if they're disabled.
            if ( apply_filters( 'autoptimize_filter_honor_qs_noptimize', true ) ) {
                // Check for `ao_noptimize` (and other) keys in the query string
                // to get non-optimized page for debugging.
                $keys = array(
                    'ao_noptimize',
                    'ao_noptirocket',
                );
                foreach ( $keys as $key ) {
                    if ( array_key_exists( $key, $_GET ) && '1' === $_GET[ $key ] ) {
                        $ao_noptimize = true;
                        break;
                    }
                }
            }

            // If setting says not to optimize logged in user and user is logged in...
            if ( false === $ao_noptimize && 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_optimize_logged', 'on' ) && is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
                $ao_noptimize = true;
            }

            // If setting says not to optimize cart/checkout.
            if ( false === $ao_noptimize && 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_optimize_checkout', 'off' ) ) {
                // Checking for woocommerce, easy digital downloads and wp ecommerce...
                foreach ( array( 'is_checkout', 'is_cart', 'is_account_page', 'edd_is_checkout', 'wpsc_is_cart', 'wpsc_is_checkout' ) as $func ) {
                    if ( function_exists( $func ) && $func() ) {
                        $ao_noptimize = true;
                        break;
                    }
                }
            }

            // Misc. querystring paramaters that will stop AO from doing optimizations (pagebuilders +
            // 2 generic parameters that could/ should become standard between optimization plugins?).
            if ( false === $ao_noptimize ) {
                $_qs_showstoppers = array( 'no_cache', 'no_optimize', 'tve', 'elementor-preview', 'fl_builder', 'vc_action', 'et_fb', 'bt-beaverbuildertheme', 'ct_builder', 'fb-edit', 'siteorigin_panels_live_editor', 'preview', 'td_action' );

                // doing Jonathan a quick favor to allow correct unused CSS generation ;-) .
                if ( apply_filters( 'autoptimize_filter_main_showstoppers_do_wp_rocket_a_favor', true ) ) {
                    $_qs_showstoppers[] = 'nowprocket';
                }

                foreach ( $_qs_showstoppers as $_showstopper ) {
                    if ( array_key_exists( $_showstopper, $_GET ) ) {
                        $ao_noptimize = true;
                        break;
                    }
                }
            }

            // Also honor PageSpeed=off parameter as used by mod_pagespeed, in use by some pagebuilders,
            // see https://www.modpagespeed.com/doc/experiment#ModPagespeed for info on that.
            if ( false === $ao_noptimize && array_key_exists( 'PageSpeed', $_GET ) && 'off' === $_GET['PageSpeed'] ) {
                $ao_noptimize = true;
            }

            // If page/ post check post_meta to see if optimize is off.
            if ( false === autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_optimize' ) ) {
                $ao_noptimize = true;
            }

            // And finally allows blocking of autoptimization on your own terms regardless of above decisions.
            $ao_noptimize = (bool) apply_filters( 'autoptimize_filter_noptimize', $ao_noptimize );

            // Check for site being previewed in the Customizer (available since WP 4.0).
            $is_customize_preview = false;
            if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
                $is_customize_preview = is_customize_preview();
            }
            
            // explicitly disable when is_login exists and is true but don't use it direclty because older versions of WordPress don't have that yet.
            $is_login = false;
            if ( function_exists( 'is_login' ) && true === is_login() ) {
                $is_login = true;
            }

            /**
             * We only buffer the frontend requests (and then only if not a feed
             * and not turned off explicitly and not when being previewed in Customizer)!
             * NOTE: Tests throw a notice here due to is_feed() being called
             * while the main query hasn't been ran yet. Thats why we use
             * AUTOPTIMIZE_INIT_EARLIER in tests.
             */
            $do_buffering = ( ! is_admin() && ! is_feed() && ! is_embed() && ! $is_login && ! $is_customize_preview && ! $ao_noptimize );
        }

        return $do_buffering;
    }

    /**
     * Returns true if given markup is considered valid/processable/optimizable.
     *
     * @param string $content Markup.
     *
     * @return bool
     */
    public function is_valid_buffer( $content )
    {
        // Defaults to true.
        $valid = true;

        $has_no_html_tag    = ( false === stripos( $content, '<html' ) );
        $has_xsl_stylesheet = ( false !== stripos( $content, '<xsl:stylesheet' ) || false !== stripos( $content, '<?xml-stylesheet' ) );
        $has_html5_doctype  = ( preg_match( '/^<!DOCTYPE.+html>/i', ltrim( $content ) ) > 0 );
        $has_noptimize_page = ( false !== stripos( $content, '<!-- noptimize-page -->' ) );

        if ( $has_no_html_tag ) {
            // Can't be valid amp markup without an html tag preceding it.
            $is_amp_markup = false;
        } else {
            $is_amp_markup = self::is_amp_markup( $content );
        }

        // If it's not html, or if it's amp or contains xsl stylesheets we don't touch it.
        if ( $has_no_html_tag && ! $has_html5_doctype || $is_amp_markup || $has_xsl_stylesheet || $has_noptimize_page ) {
            $valid = false;
        }

        return $valid;
    }

    /**
     * Returns true if given $content is considered to be AMP markup.
     * This is far from actual validation against AMP spec, but it'll do for now.
     *
     * @param string $content Markup to check.
     *
     * @return bool
     */
    public static function is_amp_markup( $content )
    {
        // Short-circuit if the page is already AMP from the start.
        if (
            preg_match(
                sprintf(
                    '#^(?:<!.*?>|\s+)*+<html(?=\s)[^>]*?\s(%1$s|%2$s|%3$s)(\s|=|>)#is',
                    'amp',
                    "\xE2\x9A\xA1", // From \AmpProject\Attribute::AMP_EMOJI.
                    "\xE2\x9A\xA1\xEF\xB8\x8F" // From \AmpProject\Attribute::AMP_EMOJI_ALT, per https://github.com/ampproject/amphtml/issues/25990.
                ),
                $content
            )
        ) {
            return true;
        }

        // Or else short-circuit if the AMP plugin will be processing the output to be an AMP page.
        if ( function_exists( 'amp_is_request' ) ) {
            return amp_is_request(); // For AMP plugin v2.0+.
        } elseif ( function_exists( 'is_amp_endpoint' ) ) {
            return is_amp_endpoint(); // For older/other AMP plugins (still supported in 2.0 as an alias).
        }

        return false;
    }

    /**
     * Processes/optimizes the output-buffered content and returns it.
     * If the content is not processable, it is returned unmodified.
     *
     * @param string $content Buffered content.
     *
     * @return string
     */
    public function end_buffering( $content )
    {
        // Bail early without modifying anything if we can't handle the content.
        if ( ! $this->is_valid_buffer( $content ) ) {
            return $content;
        }

        $conf = autoptimizeConfig::instance();

        // Determine what needs to be ran.
        $classes = array();
        if ( $conf->get( 'autoptimize_js' ) ) {
            $classes[] = 'autoptimizeScripts';
        }
        if ( $conf->get( 'autoptimize_css' ) ) {
            $classes[] = 'autoptimizeStyles';
        }
        if ( $conf->get( 'autoptimize_html' ) ) {
            $classes[] = 'autoptimizeHTML';
        }

        $classoptions = array(
            'autoptimizeScripts' => array(
                'aggregate'           => $conf->get( 'autoptimize_js_aggregate' ),
                'defer_not_aggregate' => $conf->get( 'autoptimize_js_defer_not_aggregate' ),
                'defer_inline'        => $conf->get( 'autoptimize_js_defer_inline' ),
                'justhead'            => $conf->get( 'autoptimize_js_justhead' ),
                'forcehead'           => $conf->get( 'autoptimize_js_forcehead' ),
                'trycatch'            => $conf->get( 'autoptimize_js_trycatch' ),
                'js_exclude'          => $conf->get( 'autoptimize_js_exclude' ),
                'cdn_url'             => $conf->get( 'autoptimize_cdn_url' ),
                'include_inline'      => $conf->get( 'autoptimize_js_include_inline' ),
                'minify_excluded'     => $conf->get( 'autoptimize_minify_excluded' ),
            ),
            'autoptimizeStyles'  => array(
                'aggregate'       => $conf->get( 'autoptimize_css_aggregate' ),
                'justhead'        => $conf->get( 'autoptimize_css_justhead' ),
                'datauris'        => $conf->get( 'autoptimize_css_datauris' ),
                'defer'           => $conf->get( 'autoptimize_css_defer' ),
                'defer_inline'    => $conf->get( 'autoptimize_css_defer_inline' ),
                'inline'          => $conf->get( 'autoptimize_css_inline' ),
                'css_exclude'     => $conf->get( 'autoptimize_css_exclude' ),
                'cdn_url'         => $conf->get( 'autoptimize_cdn_url' ),
                'include_inline'  => $conf->get( 'autoptimize_css_include_inline' ),
                'nogooglefont'    => $conf->get( 'autoptimize_css_nogooglefont' ),
                'minify_excluded' => $conf->get( 'autoptimize_minify_excluded' ),
            ),
            'autoptimizeHTML'    => array(
                'keepcomments'  => $conf->get( 'autoptimize_html_keepcomments' ),
                'minify_inline' => $conf->get( 'autoptimize_html_minify_inline' ),
            ),
        );

        $content = apply_filters( 'autoptimize_filter_html_before_minify', $content );

        // Run the classes!
        foreach ( $classes as $name ) {
            $instance = new $name( $content );
            if ( $instance->read( $classoptions[ $name ] ) ) {
                $instance->minify();
                $instance->cache();
                $content = $instance->getcontent();
            }
            unset( $instance );
        }

        $content = apply_filters( 'autoptimize_html_after_minify', $content );

        return $content;
    }

    public static function autoptimize_nobuffer_optimize( $html_in ) {
        $html_out = $html_in;

        if ( apply_filters( 'autoptimize_filter_speedupper', true ) ) {
            $ao_speedupper = new autoptimizeSpeedupper();
        }

        $self = new self( AUTOPTIMIZE_PLUGIN_VERSION, AUTOPTIMIZE_PLUGIN_FILE );
        if ( $self->should_buffer() ) {
            $html_out = $self->end_buffering( $html_in );
        }
        return $html_out;
    }

    public static function on_uninstall()
    {
        // clear the cache.
        autoptimizeCache::clearall();

        // remove postmeta if active.
        if ( autoptimizeConfig::is_ao_meta_settings_active() ) {
            delete_post_meta_by_key( 'ao_post_optimize' );
        }

        // remove all options.
        $delete_options = array(
            'autoptimize_cache_clean',
            'autoptimize_cache_nogzip',
            'autoptimize_css',
            'autoptimize_css_aggregate',
            'autoptimize_css_datauris',
            'autoptimize_css_justhead',
            'autoptimize_css_defer',
            'autoptimize_css_defer_inline',
            'autoptimize_css_inline',
            'autoptimize_css_exclude',
            'autoptimize_html',
            'autoptimize_html_keepcomments',
            'autoptimize_html_minify_inline',
            'autoptimize_enable_site_config',
            'autoptimize_enable_meta_ao_settings',
            'autoptimize_js',
            'autoptimize_js_aggregate',
            'autoptimize_js_defer_not_aggregate',
            'autoptimize_js_defer_inline',
            'autoptimize_js_exclude',
            'autoptimize_js_forcehead',
            'autoptimize_js_justhead',
            'autoptimize_js_trycatch',
            'autoptimize_version',
            'autoptimize_show_adv',
            'autoptimize_cdn_url',
            'autoptimize_cachesize_notice',
            'autoptimize_css_include_inline',
            'autoptimize_js_include_inline',
            'autoptimize_optimize_logged',
            'autoptimize_optimize_checkout',
            'autoptimize_extra_settings',
            'autoptimize_service_availablity',
            'autoptimize_imgopt_provider_stat',
            'autoptimize_imgopt_launched',
            'autoptimize_imgopt_settings',
            'autoptimize_minify_excluded',
            'autoptimize_cache_fallback',
            'autoptimize_ccss_rules',
            'autoptimize_ccss_additional',
            'autoptimize_ccss_queue',
            'autoptimize_ccss_viewport',
            'autoptimize_ccss_finclude',
            'autoptimize_ccss_rlimit',
            'autoptimize_ccss_rtimelimit',
            'autoptimize_ccss_noptimize',
            'autoptimize_ccss_debug',
            'autoptimize_ccss_key',
            'autoptimize_ccss_keyst',
            'autoptimize_ccss_version',
            'autoptimize_ccss_loggedin',
            'autoptimize_ccss_forcepath',
            'autoptimize_ccss_deferjquery',
            'autoptimize_ccss_domain',
            'autoptimize_ccss_unloadccss',
            'autoptimize_installed_before_compatibility',
        );

        if ( ! is_multisite() ) {
            foreach ( $delete_options as $del_opt ) {
                delete_option( $del_opt );
            }
            autoptimizeMain::remove_cronjobs();
        } else {
            global $wpdb;
            $blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            $original_blog_id = get_current_blog_id();
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                foreach ( $delete_options as $del_opt ) {
                    delete_option( $del_opt );
                }
                autoptimizeMain::remove_cronjobs();
            }
            switch_to_blog( $original_blog_id );
        }

        // Remove AO CCSS cached files and directory.
        $ao_ccss_dir = WP_CONTENT_DIR . '/uploads/ao_ccss/';
        if ( file_exists( $ao_ccss_dir ) && is_dir( $ao_ccss_dir ) ) {
            // fixme: should check for subdirs when in multisite and remove contents of those as well.
            array_map( 'unlink', glob( $ao_ccss_dir . '*.{css,html,json,log,zip,lock}', GLOB_BRACE ) );
            rmdir( $ao_ccss_dir );
        }

        // Remove 404-handler (although that should have been removed in clearall already).
        $_fallback_php = trailingslashit( WP_CONTENT_DIR ) . 'autoptimize_404_handler.php';
        if ( file_exists( $_fallback_php ) ) {
            unlink( $_fallback_php );
        }
    }

    public static function on_deactivation()
    {
        if ( is_multisite() && is_network_admin() ) {
            global $wpdb;
            $blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            $original_blog_id = get_current_blog_id();
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                autoptimizeMain::remove_cronjobs();
            }
            switch_to_blog( $original_blog_id );
        } else {
            autoptimizeMain::remove_cronjobs();
        }
        autoptimizeCache::clearall();
    }

    public static function remove_cronjobs() {
        // Remove scheduled events.
        foreach ( array( 'ao_cachechecker', 'ao_ccss_queue', 'ao_ccss_maintenance', 'ao_ccss_keychecker' ) as $_event ) {
            if ( wp_get_schedule( $_event ) ) {
                wp_clear_scheduled_hook( $_event );
            }
        }
    }

    public static function notice_cache_unavailable()
    {
        echo '<div class="error"><p>';
        // Translators: %s is the cache directory location.
        printf( __( 'Autoptimize cannot write to the cache directory (%s), please fix to enable CSS/ JS optimization!', 'autoptimize' ), AUTOPTIMIZE_CACHE_DIR );
        echo '</p></div>';
    }

    public static function notice_installed()
    {
        echo '<div class="updated"><p>';
        printf( __( 'Thank you for installing and activating Autoptimize. Please configure it under %1$sSettings -> Autoptimize%2$s to start improving your site\'s performance.', 'autoptimize' ), '<a href="options-general.php?page=autoptimize">', '</a>' );
        echo '</p></div>';
    }

    public static function notice_updated()
    {
        echo '<div class="updated"><p>';
        _e( 'Autoptimize has just been updated. Please <strong>test your site now</strong> and adapt Autoptimize config if needed.', 'autoptimize' );
        echo '</p></div>';
    }

    public static function notice_plug_imgopt()
    {
        // Translators: the URL added points to the Autopmize Extra settings.
        $_ao_imgopt_plug_notice      = sprintf( __( 'Did you know that Autoptimize offers on-the-fly image optimization (with support for WebP and AVIF) and CDN via ShortPixel? Check out the %1$sAutoptimize Image settings%2$s to enable this option.', 'autoptimize' ), '<a href="options-general.php?page=autoptimize_imgopt">', '</a>' );
        $_ao_imgopt_plug_notice      = apply_filters( 'autoptimize_filter_main_imgopt_plug_notice', $_ao_imgopt_plug_notice );
        $_ao_imgopt_launch_ok        = autoptimizeImages::launch_ok_wrapper();
        $_ao_imgopt_plug_dismissible = 'ao-img-opt-plug-123';
        $_ao_imgopt_active           = autoptimizeImages::imgopt_active();
        $_is_ao_settings_page        = autoptimizeUtils::is_ao_settings();

        if ( current_user_can( 'manage_options' ) && ! defined( 'AO_PRO_VERSION' ) && $_is_ao_settings_page && '' !== $_ao_imgopt_plug_notice && ! $_ao_imgopt_active && $_ao_imgopt_launch_ok && PAnD::is_admin_notice_active( $_ao_imgopt_plug_dismissible ) ) {
            echo '<div class="notice notice-info is-dismissible" data-dismissible="' . $_ao_imgopt_plug_dismissible . '"><p>';
            echo $_ao_imgopt_plug_notice;
            echo '</p></div>';
        }
    }

    public static function notice_imgopt_issue()
    {
        // Translators: the URL added points to the Autopmize Extra settings.
        $_ao_imgopt_issue_notice      = sprintf( __( 'Shortpixel reports it cannot always reach your site, which might mean some images are not optimized. You can %1$sread more about why this happens and how you can fix that problem here%2$s.', 'autoptimize' ), '<a href="https://shortpixel.com/knowledge-base/article/469-i-received-an-e-mail-that-says-some-of-my-images-are-not-accessible-what-should-i-do#fullarticle" target="_blank">', '</a>' );
        $_ao_imgopt_issue_notice      = apply_filters( 'autoptimize_filter_main_imgopt_issue_notice', $_ao_imgopt_issue_notice );
        $_ao_imgopt_issue_dismissible = 'ao-img-opt-issue-14';
        $_ao_imgopt_active            = autoptimizeImages::imgopt_active();
        $_ao_imgopt_status            = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_provider_stat', '' );

        if ( is_array( $_ao_imgopt_status ) && array_key_exists( 'TemporaryRedirectOrigin', $_ao_imgopt_status ) && ( $_ao_imgopt_status['TemporaryRedirectOrigin'] === "true" || $_ao_imgopt_status['TemporaryRedirectOrigin'] === true ) ) {
            $_ao_imgopt_status_redirect_warning = true;            
        } else {
            $_ao_imgopt_status_redirect_warning = false;
        }

        if ( current_user_can( 'manage_options' ) && $_ao_imgopt_active && $_ao_imgopt_status_redirect_warning && '' !== $_ao_imgopt_issue_notice && PAnD::is_admin_notice_active( $_ao_imgopt_issue_dismissible ) ) {
            echo '<div class="notice notice-info is-dismissible" data-dismissible="' . $_ao_imgopt_issue_dismissible . '"><p>';
            echo $_ao_imgopt_issue_notice;
            echo '</p></div>';
        }
    }


    public static function notice_nopagecache()
    {
        /*
         * Autoptimize does not do page caching (yet) but not everyone knows, so below logic tries to find out if page caching is available and if not show a notice on the AO Settings pages.
         *
         * uses helper function in autoptimizeUtils.php
         */
        $_ao_nopagecache_notice      = __( 'It looks like your site might not have <strong>page caching</strong> which is a <strong>must-have for performance</strong>. If you are sure you have a page cache, you can close this notice, but when in doubt check with your host if they offer this or install a page caching plugin like for example', 'autoptimize' );
        $_ao_pagecache_install_url   = network_admin_url() . 'plugin-install.php?tab=search&type=term&s=';
        $_ao_nopagecache_notice     .= ' <a href="' . $_ao_pagecache_install_url . 'wp+super+cache' . '">WP Super Cache</a>, <a href="' . $_ao_pagecache_install_url . 'keycdn+cache+enabler' . '">KeyCDN Cache Enabler</a>, ...';
        $_ao_nopagecache_dismissible = 'ao-nopagecache-forever'; // the notice is only shown once and will not re-appear when dismissed.
        $_is_ao_settings_page        = autoptimizeUtils::is_ao_settings();

        if ( current_user_can( 'manage_options' ) && $_is_ao_settings_page && PAnD::is_admin_notice_active( $_ao_nopagecache_dismissible ) && true === apply_filters( 'autoptimize_filter_main_show_pagecache_notice', true ) ) {
            if ( false === autoptimizeUtils::find_pagecache() ) {
                echo '<div class="notice notice-info is-dismissible" data-dismissible="' . $_ao_nopagecache_dismissible . '"><p>';
                echo $_ao_nopagecache_notice;
                echo '</p></div>';
            }
        }
    }

    public static function notice_potential_conflict()
    {
        /*
         * Using other plugins to do CSS/ JS optimization can cause unexpected and hard to troubleshoot issues, warn users who seem to be in that situation.
         */
        // Translators: the sentence will be finished with the name of the offending plugin and a final stop.
        $_ao_potential_conflict_notice      = __( 'It looks like you have <strong>another plugin also doing CSS and/ or JS optimization</strong>, which can result in hard to troubleshoot <strong>conflicts</strong>. For this reason it is recommended to disable this functionality in', 'autoptimize' ) . ' ';
        $_ao_potential_conflict_dismissible = 'ao-potential-conflict-forever'; // the notice is only shown once and will not re-appear when dismissed.
        $_is_ao_settings_page               = autoptimizeUtils::is_ao_settings();

        if ( current_user_can( 'manage_options' ) && $_is_ao_settings_page && PAnD::is_admin_notice_active( $_ao_potential_conflict_dismissible ) && true === apply_filters( 'autoptimize_filter_main_show_potential_conclict_notice', true ) ) {
            $_potential_conflicts = autoptimizeUtils::find_potential_conflicts();
            if ( false !== $_potential_conflicts ) {
                $_ao_potential_conflict_notice .= '<strong>' . $_potential_conflicts . '</strong>.';
                echo '<div class="notice notice-info is-dismissible" data-dismissible="' . $_ao_potential_conflict_dismissible . '"><p>';
                echo $_ao_potential_conflict_notice;
                echo '</p></div>';
            }
        }
    }
}
