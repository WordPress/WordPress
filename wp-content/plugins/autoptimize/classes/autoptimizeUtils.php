<?php
/**
 * General helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeUtils
{
    /**
     * Returns true when mbstring is available.
     *
     * @param bool|null $override Allows overriding the decision.
     *
     * @return bool
     */
    public static function mbstring_available( $override = null )
    {
        static $available = null;

        if ( null === $available ) {
            $available = \extension_loaded( 'mbstring' );
        }

        if ( null !== $override ) {
            $available = $override;
        }

        return $available;
    }

    /**
     * Multibyte-capable strpos() if support is available on the server.
     * If not, it falls back to using \strpos().
     *
     * @param string      $haystack Haystack.
     * @param string      $needle   Needle.
     * @param int         $offset   Offset.
     * @param string|null $encoding Encoding. Default null.
     *
     * @return int|false
     */
    public static function strpos( $haystack, $needle, $offset = 0, $encoding = null )
    {
        if ( self::mbstring_available() ) {
            return ( null === $encoding ) ? \mb_strpos( $haystack, $needle, $offset ) : \mb_strpos( $haystack, $needle, $offset, $encoding );
        } else {
            return \strpos( $haystack, $needle, $offset );
        }
    }

    /**
     * Attempts to return the number of characters in the given $string if
     * mbstring is available. Returns the number of bytes
     * (instead of characters) as fallback.
     *
     * @param string      $string   String.
     * @param string|null $encoding Encoding.
     *
     * @return int Number of characters or bytes in given $string
     *             (characters if/when supported, bytes otherwise).
     */
    public static function strlen( $string, $encoding = null )
    {
        if ( self::mbstring_available() ) {
            return ( null === $encoding ) ? \mb_strlen( $string ) : \mb_strlen( $string, $encoding );
        } else {
            return \strlen( $string );
        }
    }

    /**
     * Our wrapper around implementations of \substr_replace()
     * that attempts to not break things horribly if at all possible.
     * Uses mbstring if available, before falling back to regular
     * substr_replace() (which works just fine in the majority of cases).
     *
     * @param string      $string      String.
     * @param string      $replacement Replacement.
     * @param int         $start       Start offset.
     * @param int|null    $length      Length.
     * @param string|null $encoding    Encoding.
     *
     * @return string
     */
    public static function substr_replace( $string, $replacement, $start, $length = null, $encoding = null )
    {
        if ( self::mbstring_available() ) {
            $strlen = self::strlen( $string, $encoding );

            if ( $start < 0 ) {
                if ( -$start < $strlen ) {
                    $start = $strlen + $start;
                } else {
                    $start = 0;
                }
            } elseif ( $start > $strlen ) {
                $start = $strlen;
            }

            if ( null === $length || '' === $length ) {
                $start2 = $strlen;
            } elseif ( $length < 0 ) {
                $start2 = $strlen + $length;
                if ( $start2 < $start ) {
                    $start2 = $start;
                }
            } else {
                $start2 = $start + $length;
            }

            if ( null === $encoding ) {
                $leader  = $start ? \mb_substr( $string, 0, $start ) : '';
                $trailer = ( $start2 < $strlen ) ? \mb_substr( $string, $start2, null ) : '';
            } else {
                $leader  = $start ? \mb_substr( $string, 0, $start, $encoding ) : '';
                $trailer = ( $start2 < $strlen ) ? \mb_substr( $string, $start2, null, $encoding ) : '';
            }

            return "{$leader}{$replacement}{$trailer}";
        }

        return ( null === $length ) ? \substr_replace( $string, $replacement, $start ) : \substr_replace( $string, $replacement, $start, $length );
    }

    /**
     * Decides whether this is a "subdirectory site" or not.
     *
     * @param bool $override Allows overriding the decision when needed.
     *
     * @return bool
     */
    public static function siteurl_not_root( $override = null )
    {
        static $subdir = null;

        if ( null === $subdir ) {
            $parts  = self::get_ao_wp_site_url_parts();
            $subdir = ( isset( $parts['path'] ) && ( '/' !== $parts['path'] ) );
        }

        if ( null !== $override ) {
            $subdir = $override;
        }

        return $subdir;
    }

    /**
     * Parse AUTOPTIMIZE_WP_SITE_URL into components using \parse_url(), but do
     * so only once per request/lifecycle.
     *
     * @return array
     */
    public static function get_ao_wp_site_url_parts()
    {
        static $parts = array();

        if ( empty( $parts ) ) {
            $parts = \parse_url( AUTOPTIMIZE_WP_SITE_URL );
        }

        return $parts;
    }

    /**
     * Modify given $cdn_url to include the site path when needed.
     *
     * @param string $cdn_url          CDN URL to tweak.
     * @param bool   $force_cache_miss Force a cache miss in order to be able
     *                                 to re-run the filter.
     *
     * @return string
     */
    public static function tweak_cdn_url_if_needed( $cdn_url, $force_cache_miss = false )
    {
        static $results = array();

        if ( ! isset( $results[ $cdn_url ] ) || $force_cache_miss ) {

            // In order to return unmodified input when there's no need to tweak.
            $results[ $cdn_url ] = $cdn_url;

            // Behind a default true filter for backcompat, and only for sites
            // in a subfolder/subdirectory, but still easily turned off if
            // not wanted/needed...
            if ( autoptimizeUtils::siteurl_not_root() ) {
                $check = apply_filters( 'autoptimize_filter_cdn_magic_path_check', true, $cdn_url );
                if ( $check ) {
                    $site_url_parts = autoptimizeUtils::get_ao_wp_site_url_parts();
                    $cdn_url_parts  = \parse_url( $cdn_url );
                    $schemeless     = self::is_protocol_relative( $cdn_url );
                    $cdn_url_parts  = self::maybe_replace_cdn_path( $site_url_parts, $cdn_url_parts );
                    if ( false !== $cdn_url_parts ) {
                        $results[ $cdn_url ] = self::assemble_parsed_url( $cdn_url_parts, $schemeless );
                    }
                }
            }
        }

        return $results[ $cdn_url ];
    }

    /**
     * When siteurl contains a path other than '/' and the CDN URL does not have
     * a path or it's path is '/', this will modify the CDN URL's path component
     * to match that of the siteurl.
     * This is to support "magic" CDN urls that worked that way before v2.4...
     *
     * @param array $site_url_parts Site URL components array.
     * @param array $cdn_url_parts  CDN URL components array.
     *
     * @return array|false
     */
    public static function maybe_replace_cdn_path( array $site_url_parts, array $cdn_url_parts )
    {
        if ( isset( $site_url_parts['path'] ) && '/' !== $site_url_parts['path'] ) {
            if ( ! isset( $cdn_url_parts['path'] ) || '/' === $cdn_url_parts['path'] ) {
                $cdn_url_parts['path'] = $site_url_parts['path'];
                return $cdn_url_parts;
            }
        }

        return false;
    }

    /**
     * Given an array or components returned from \parse_url(), assembles back
     * the complete URL.
     * If optional
     *
     * @param array $parsed_url URL components array.
     * @param bool  $schemeless Whether the assembled URL should be
     *                          protocol-relative (schemeless) or not.
     *
     * @return string
     */
    public static function assemble_parsed_url( array $parsed_url, $schemeless = false )
    {
        $scheme = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
        if ( $schemeless ) {
            $scheme = '//';
        }
        $host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
        $port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
        $user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
        $pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';
        $pass     = ( $user || $pass ) ? "$pass@" : '';
        $path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
        $query    = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
        $fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Returns true if given $url is protocol-relative.
     *
     * @param string $url URL to check.
     *
     * @return bool
     */
    public static function is_protocol_relative( $url )
    {
        $result = false;

        if ( ! empty( $url ) ) {
            $result = ( 0 === strpos( $url, '//' ) );
        }

        return $result;
    }

    /**
     * Canonicalizes the given path regardless of it existing or not.
     *
     * @param string $path Path to normalize.
     *
     * @return string
     */
    public static function path_canonicalize( $path )
    {
        $patterns     = array(
            '~/{2,}~',
            '~/(\./)+~',
            '~([^/\.]+/(?R)*\.{2,}/)~',
            '~\.\./~',
        );
        $replacements = array(
            '/',
            '/',
            '',
            '',
        );

        return preg_replace( $patterns, $replacements, $path );
    }

    /**
     * Checks to see if 3rd party services are available and stores result in option
     *
     * TODO This should be two separate methods.
     *
     * @param string $return_result should we return resulting service status array (default no).
     *
     * @return null|array Service status or null.
     */
    public static function check_service_availability( $return_result = false )
    {
        $service_availability_resp = wp_remote_get( 'https://misc.optimizingmatters.com/api/autoptimize_service_availablity.json?from=aomain&ver=' . AUTOPTIMIZE_PLUGIN_VERSION );
        if ( ! is_wp_error( $service_availability_resp ) ) {
            if ( '200' == wp_remote_retrieve_response_code( $service_availability_resp ) ) {
                $availabilities = json_decode( wp_remote_retrieve_body( $service_availability_resp ), true );
                if ( is_array( $availabilities ) ) {
                    autoptimizeOptionWrapper::update_option( 'autoptimize_service_availablity', $availabilities );
                    if ( $return_result ) {
                        return $availabilities;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Returns true if the string is a valid regex.
     *
     * @param string $string String, duh.
     *
     * @return bool
     */
    public static function str_is_valid_regex( $string )
    {
        set_error_handler( function() {}, E_WARNING );
        $is_regex = ( false !== preg_match( $string, '' ) );
        restore_error_handler();

        return $is_regex;
    }

    /**
     * Returns true if a certain WP plugin is active/loaded.
     *
     * @param string $plugin_file Main plugin file.
     *
     * @return bool
     */
    public static function is_plugin_active( $plugin_file )
    {
        static $ipa_exists = null;
        if ( null === $ipa_exists ) {
            if ( ! function_exists( '\is_plugin_active' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $ipa_exists = function_exists( '\is_plugin_active' );
        }

        return $ipa_exists && \is_plugin_active( $plugin_file );
    }

    /**
     * Returns a node without ID attrib for use in noscript tags
     *
     * @param string $node an html tag.
     *
     * @return string
     */
    public static function remove_id_from_node( $node ) {
        if ( strpos( $node, 'id=' ) === false || apply_filters( 'autoptimize_filter_utils_keep_ids', false ) ) {
            return $node;
        } else {
            return preg_replace( '#(.*) id=[\'|"].*[\'|"] (.*)#Um', '$1 $2', $node );
        }
    }

    /**
     * Returns true if given $str ends with given $test.
     *
     * @param string $str String to check.
     * @param string $test Ending to match.
     *
     * @return bool
     */
    public static function str_ends_in( $str, $test )
    {
        // @codingStandardsIgnoreStart
        // substr_compare() is bugged on 5.5.11: https://3v4l.org/qGYBH
        // return ( 0 === substr_compare( $str, $test, -strlen( $test ) ) );
        // @codingStandardsIgnoreEnd

        $length = strlen( $test );

        return ( substr( $str, -$length, $length ) === $test );
    }

    /**
     * Returns true if a pagecache is found, false if not.
     * Now used to show notice, might be used later on to (un)hide page caching in AO if no page cache found.
     *
     * @param bool $disregard_transient False by default, but can be used to ignore the transient and retest.
     *
     * @return bool
     */
    public static function find_pagecache( $disregard_transient = false ) {
        static $_found_pagecache = null;

        if ( null === $_found_pagecache ) {
            $_page_cache_constants = array(
                'NgInx' => 'NGINX_HELPER_BASENAME',
                'Kinsta' => 'KINSTA_CACHE_ZONE',
                'Presslabs' => 'PL_INSTANCE_REF',
                'Cache Enabler' => 'CACHE_ENABLER_VERSION',
                'Speed Booster Pack' => 'SBP_PLUGIN_NAME',
                'Servebolt' => 'SERVEBOLT_PLUGIN_FILE',
                'WP CloudFlare Super Page Cache' => 'SWCFPC_PLUGIN_PATH',
                'Cachify' => 'CACHIFY_CACHE_DIR',
                'WP Rocket' => 'WP_ROCKET_CACHE_PATH',
                'WP Optimize' => 'WPO_VERSION',
                'Autoptimize Pro' => 'AO_PRO_PAGECACHE_CACHE_DIR',
            );
            $_page_cache_classes = array(
                'Pressidium' => 'Ninukis_Plugin',
                'Swift Performance' => 'Swift_Performance_Cache',
                'WP Fastest Cache' => 'WpFastestCache',
                'Quick Cache' => 'c_ws_plugin__qcache_purging_routines',
                'ZenCache' => 'zencache',
                'Comet Cache' => 'comet_cache',
                'WP Engine' => 'WpeCommon',
                'Flywheel' => 'FlywheelNginxCompat',
                'Pagely' => 'PagelyCachePurge',
            );
            $_page_cache_functions = array(
                'WP Super Cache' => 'wp_cache_clear_cache',
                'W3 Total Cache' => 'w3tc_pgcache_flush',
                'WP Fast Cache' => 'wp_fast_cache_bulk_delete_all',
                'Rapidcache' => 'rapidcache_clear_cache',
                'Siteground' => 'sg_cachepress_purge_cache',
                'WP Super Cache' => 'prune_super_cache',
            );

            $_found_pagecache = false;
            if ( true !== $disregard_transient ) {
                $_ao_pagecache_transient = 'autoptimize_pagecache_check';
                $_found_pagecache        = get_transient( $_ao_pagecache_transient );
            }

            if ( current_user_can( 'manage_options' ) && false === $_found_pagecache ) {
                // loop through known pagecache constants.
                foreach ( $_page_cache_constants as $_name => $_constant ) {
                    if ( defined( $_constant ) ) {
                        $_found_pagecache = $_name;
                        break;
                    }
                }
                // and loop through known pagecache classes.
                if ( false === $_found_pagecache ) {
                    foreach ( $_page_cache_classes as $_name => $_class ) {
                        if ( class_exists( $_class ) ) {
                            $_found_pagecache = $_name;
                            break;
                        }
                    }
                }
                // and loop through known pagecache functions.
                if ( false === $_found_pagecache ) {
                    foreach ( $_page_cache_functions as $_name => $_function ) {
                        if ( function_exists( $_function ) ) {
                            $_found_pagecache = $_name;
                            break;
                        }
                    }
                }

                // store in transient for 1 week if pagecache found.
                if ( true === $_found_pagecache && true !== $disregard_transient ) {
                    set_transient( $_ao_pagecache_transient, true, WEEK_IN_SECONDS );
                }
            }
        }

        return $_found_pagecache;
    }

    /**
     * Returns true if on one of the AO settings tabs, false if not.
     * Used to limit notifications to AO settings pages.
     *
     * @return bool
     */
    public static function is_ao_settings() {
        $_is_ao_settings = ( str_replace( array( 'autoptimize', 'autoptimize_imgopt', 'ao_critcss', 'autoptimize_extra', 'ao_partners', 'ao_pro_boosters' ), '', $_SERVER['REQUEST_URI'] ) !== $_SERVER['REQUEST_URI'] ? true : false );
        return $_is_ao_settings;
    }

    /**
     * Returns false if no conflicting plugins are found, the name if the plugin if found.
     *
     * @return bool|string
     */
    public static function find_potential_conflicts() {
        if ( defined( 'WPFC_WP_CONTENT_BASENAME' ) ) {
            $_wpfc_options = json_decode( get_option( 'WpFastestCache' ) );
            foreach ( array( 'wpFastestCacheMinifyCss', 'wpFastestCacheCombineCss', 'wpFastestCacheCombineJs' ) as $_wpfc_conflicting ) {
                if ( isset( $_wpfc_options->$_wpfc_conflicting ) && 'on' === $_wpfc_options->$_wpfc_conflicting ) {
                    return 'WP Fastest Cache';
                }
            }
        } elseif ( defined( 'W3TC_VERSION' ) ) {
            $w3tc_config     = file_get_contents( WP_CONTENT_DIR . '/w3tc-config/master.php' );
            $w3tc_minify_on = strpos( $w3tc_config, '"minify.enabled": true' );
            if ( $w3tc_minify_on ) {
                return 'W3 Total Cache';
            }
        } elseif ( defined( 'SiteGround_Optimizer\VERSION' ) ) {
            if ( get_option( 'siteground_optimizer_optimize_css' ) == 1 || get_option( 'siteground_optimizer_optimize_javascript' ) == 1 || get_option( 'siteground_optimizer_combine_javascript' ) == 1 || get_option( 'siteground_optimizer_combine_css' ) == 1 ) {
                return 'Siteground Optimizer';
            }
        } elseif ( defined( 'WPO_VERSION' ) ) {
            $_wpo_options = get_site_option( 'wpo_minify_config' );
            if ( is_array( $_wpo_options ) && 1 == $_wpo_options['enabled'] && ( 1 == $_wpo_options['enable_css'] || 1 == $_wpo_options['enable_js'] ) ) {
                return 'WP Optimize';
            }
        } elseif ( defined( 'WPACU_PLUGIN_VERSION' ) || defined( 'WPACU_PRO_PLUGIN_VERSION' ) ) {
            $wpacu_settings_class = new \WpAssetCleanUp\Settings();
            $wpacu_settings      = $wpacu_settings_class->getAll();

            if ( $wpacu_settings['minify_loaded_css'] || $wpacu_settings['minify_loaded_js'] || $wpacu_settings['combine_loaded_js'] || $wpacu_settings['combine_loaded_css'] ) {
                return 'Asset Cleanup';
            }
        } elseif ( defined( 'WP_ROCKET_VERSION' ) && function_exists( 'get_rocket_option' ) ) {
            if ( get_rocket_option( 'minify_js' ) || get_rocket_option( 'minify_concatenate_js' ) || get_rocket_option( 'minify_css' ) || get_rocket_option( 'minify_concatenate_css' ) || get_rocket_option( 'async_css' ) ) {
                return 'WP Rocket';
            }
        } elseif ( function_exists( 'fvm_get_settings' ) ) {
            return 'Fast Velocity Minify';
        }

        return false;
    }
    
    /**
     * Returns true if false if on a local dev environment, true if not.
     * Used to disallow image opt/ critcss for local dev environments.
     *
     * @return bool
     */
    public static function is_local_server( $_domain = AUTOPTIMIZE_SITE_DOMAIN ) {
        static $_is_local_server = null;

        if ( null === $_is_local_server ) {
            if ( false === strpos( $_domain, '.' ) && false === strpos( $_domain, ':' ) ) {
                // no dots in domain or colon (ipv6 address), so impossible to reach, this also matches 'localhost' or any other single-word domain.
                $_is_local_server = true;
            } elseif ( in_array( $_domain, array( '127.0.0.1', '0000:0000:0000:0000:0000:0000:0000:0001', '0:0:0:0:0:0:0:1', '::1' ) ) ) {
                // localhost IPv4/ IPv6.
                $_is_local_server = true;
            } elseif ( 0 === strpos( $_domain, '127.' ) || 0 === strpos( $_domain, '192.168.' ) || 0 === strpos( $_domain, 'fd' ) ) {
                // private ranges so unreachable for imgopt/ CCSS.
                $_is_local_server = true;
            } elseif ( autoptimizeUtils::str_ends_in( $_domain, '.local') ) {
                // matches 'whatever.local'.
                $_is_local_server = true;
            } else {
                // likely OK.
                $_is_local_server = false;
            }
        }

        // filter to override result for testing purposes.
        return apply_filters( 'autoptimize_filter_utils_is_local_server', $_is_local_server );
    }

    public static function strip_tags_array( $array ) {
        // strip all tags in an array (use case: avoid XSS in CCSS rules both when importing and when outputting).
        // based on https://stackoverflow.com/a/44732196/237449 but heavily tweaked.
        if ( is_array( $array ) ) {
            $result = array();
            foreach ( $array as $key => $value ){
                if ( is_array( $value ) ) {
                    $result[$key] = autoptimizeUtils::strip_tags_array( $value );
                } else if ( is_string( $value ) ) {
                    $result[$key] = wp_strip_all_tags( $value );
                } else {
                    $result[$key] = $value;
                }
            }
        } else {
            $result = wp_strip_all_tags( $array );
        }
        return $result;
    }
}
