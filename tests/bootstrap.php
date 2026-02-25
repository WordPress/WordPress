<?php
/**
 * Bootstrap for formatting.php tests.
 *
 * Provides minimal stubs for WordPress core functions so that
 * formatting.php can be loaded and tested in isolation.
 */

// Prevent double-loading.
if ( defined( 'FORMATTING_TESTS_LOADED' ) ) {
    return;
}
define( 'FORMATTING_TESTS_LOADED', true );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'WPINC', 'wp-includes' );

// Time constants used by human_time_diff() and others.
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
    define( 'MINUTE_IN_SECONDS', 60 );
}
if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
    define( 'HOUR_IN_SECONDS', 3600 );
}
if ( ! defined( 'DAY_IN_SECONDS' ) ) {
    define( 'DAY_IN_SECONDS', 86400 );
}
if ( ! defined( 'WEEK_IN_SECONDS' ) ) {
    define( 'WEEK_IN_SECONDS', 604800 );
}
if ( ! defined( 'MONTH_IN_SECONDS' ) ) {
    define( 'MONTH_IN_SECONDS', 2592000 );
}
if ( ! defined( 'YEAR_IN_SECONDS' ) ) {
    define( 'YEAR_IN_SECONDS', 31536000 );
}
if ( ! defined( 'SCRIPT_DEBUG' ) ) {
    define( 'SCRIPT_DEBUG', false );
}

// ── Minimal WordPress function stubs ──────────────────────────────

if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( $hook_name, $value, ...$args ) {
        return $value;
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
        return true;
    }
}

if ( ! function_exists( 'remove_filter' ) ) {
    function remove_filter( $hook_name, $callback, $priority = 10 ) {
        return true;
    }
}

if ( ! function_exists( 'add_action' ) ) {
    function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
        return add_filter( $hook_name, $callback, $priority, $accepted_args );
    }
}

if ( ! function_exists( 'remove_action' ) ) {
    function remove_action( $hook_name, $callback, $priority = 10 ) {
        return remove_filter( $hook_name, $callback, $priority );
    }
}

if ( ! function_exists( 'has_action' ) ) {
    function has_action( $hook_name, $callback = false ) {
        return false;
    }
}

if ( ! function_exists( 'did_action' ) ) {
    function did_action( $hook_name ) {
        return 0;
    }
}

if ( ! function_exists( 'current_filter' ) ) {
    function current_filter() {
        return '';
    }
}

if ( ! function_exists( '__' ) ) {
    function __( $text, $domain = 'default' ) {
        return $text;
    }
}

if ( ! function_exists( '_x' ) ) {
    function _x( $text, $context, $domain = 'default' ) {
        return $text;
    }
}

if ( ! function_exists( '_n' ) ) {
    function _n( $single, $plural, $number, $domain = 'default' ) {
        return ( $number == 1 ) ? $single : $plural;
    }
}

if ( ! function_exists( '_deprecated_function' ) ) {
    function _deprecated_function( $function_name, $version, $replacement = '' ) {
        // Silenced for tests.
    }
}

if ( ! function_exists( '_deprecated_argument' ) ) {
    function _deprecated_argument( $function_name, $version, $message = '' ) {
        // Silenced for tests.
    }
}

if ( ! function_exists( '_doing_it_wrong' ) ) {
    function _doing_it_wrong( $function_name, $message, $version ) {
        // Silenced for tests.
    }
}

if ( ! function_exists( 'wp_trigger_error' ) ) {
    function wp_trigger_error( $function_name, $message, $error_level = E_USER_NOTICE ) {
        // Silenced for tests.
    }
}

if ( ! function_exists( 'get_option' ) ) {
    function get_option( $option, $default_value = false ) {
        $options = array(
            'blog_charset'    => 'UTF-8',
            'use_balanceTags' => 0,
        );
        return $options[ $option ] ?? $default_value;
    }
}

if ( ! function_exists( 'is_utf8_charset' ) ) {
    function is_utf8_charset() {
        return true;
    }
}

if ( ! function_exists( 'wp_is_valid_utf8' ) ) {
    function wp_is_valid_utf8( $str ) {
        return (bool) preg_match( '//u', $str );
    }
}

if ( ! function_exists( 'wp_scrub_utf8' ) ) {
    function wp_scrub_utf8( $str ) {
        // Replace invalid UTF-8 sequences with the replacement character.
        return mb_convert_encoding( $str, 'UTF-8', 'UTF-8' );
    }
}

if ( ! function_exists( '_canonical_charset' ) ) {
    function _canonical_charset( $charset ) {
        if ( 'utf-8' === strtolower( $charset ) || 'utf8' === strtolower( $charset ) ) {
            return 'UTF-8';
        }
        return $charset;
    }
}

if ( ! function_exists( '_wp_can_use_pcre_u' ) ) {
    function _wp_can_use_pcre_u() {
        return (bool) @preg_match( '/^./u', 'a' );
    }
}

if ( ! function_exists( 'mbstring_binary_safe_encoding' ) ) {
    function mbstring_binary_safe_encoding( $reset = false ) {
        static $encodings = array();
        static $overloaded = null;

        if ( null === $overloaded ) {
            $overloaded = false;
        }

        if ( ! $overloaded ) {
            return;
        }

        if ( ! $reset ) {
            $encoding = mb_internal_encoding();
            array_push( $encodings, $encoding );
            mb_internal_encoding( 'ISO-8859-1' );
        }

        if ( $reset && $encodings ) {
            $encoding = array_pop( $encodings );
            mb_internal_encoding( $encoding );
        }
    }
}

if ( ! function_exists( 'reset_mbstring_encoding' ) ) {
    function reset_mbstring_encoding() {
        mbstring_binary_safe_encoding( true );
    }
}

if ( ! function_exists( 'wp_allowed_protocols' ) ) {
    function wp_allowed_protocols() {
        return array(
            'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'irc6',
            'ircs', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'sms',
            'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn',
        );
    }
}

if ( ! function_exists( 'wp_kses_normalize_entities' ) ) {
    function wp_kses_normalize_entities( $content, $context = 'html' ) {
        return $content;
    }
}

if ( ! function_exists( 'wp_kses_bad_protocol' ) ) {
    function wp_kses_bad_protocol( $content, $allowed_protocols ) {
        return $content;
    }
}

if ( ! function_exists( 'wp_parse_url' ) ) {
    function wp_parse_url( $url, $component = -1 ) {
        return parse_url( $url, $component );
    }
}

if ( ! function_exists( 'absint' ) ) {
    function absint( $maybeint ) {
        return abs( (int) $maybeint );
    }
}

if ( ! function_exists( 'is_admin' ) ) {
    function is_admin() {
        return false;
    }
}

if ( ! function_exists( 'wp_timezone' ) ) {
    function wp_timezone() {
        return new DateTimeZone( 'UTC' );
    }
}

if ( ! function_exists( 'wp_check_filetype' ) ) {
    function wp_check_filetype( $filename, $mimes = null ) {
        $ext = pathinfo( $filename, PATHINFO_EXTENSION );
        return array( 'ext' => $ext, 'type' => '' );
    }
}

if ( ! function_exists( 'wp_get_mime_types' ) ) {
    function wp_get_mime_types() {
        return array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif'          => 'image/gif',
            'png'          => 'image/png',
            'pdf'          => 'application/pdf',
            'doc'          => 'application/msword',
            'zip'          => 'application/zip',
            'mp3|m4a|m4b'  => 'audio/mpeg',
            'txt|asc|c|cc|h|srt' => 'text/plain',
            'css'          => 'text/css',
            'js'           => 'application/javascript',
            'htm|html'     => 'text/html',
            'xml'          => 'text/xml',
            'php'          => 'application/x-httpd-php',
            'exe'          => 'application/x-msdownload',
        );
    }
}

if ( ! function_exists( 'get_allowed_mime_types' ) ) {
    function get_allowed_mime_types( $user = null ) {
        return wp_get_mime_types();
    }
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
    // This is defined in formatting.php itself, but needed by sanitize_user
    // which may call it before the file is fully loaded. The real one will override.
}

if ( ! function_exists( 'str_contains' ) ) {
    function str_contains( string $haystack, string $needle ): bool {
        return '' === $needle || false !== strpos( $haystack, $needle );
    }
}

if ( ! function_exists( 'str_starts_with' ) ) {
    function str_starts_with( string $haystack, string $needle ): bool {
        return 0 === strncmp( $haystack, $needle, strlen( $needle ) );
    }
}

if ( ! function_exists( 'str_ends_with' ) ) {
    function str_ends_with( string $haystack, string $needle ): bool {
        return '' === $needle || substr( $haystack, -strlen( $needle ) ) === $needle;
    }
}

if ( ! function_exists( 'array_first' ) ) {
    function array_first( array $array ) {
        return reset( $array );
    }
}

if ( ! function_exists( 'filter_block_content' ) ) {
    function filter_block_content( $content, $allowed_html = 'post', $allowed_protocols = array() ) {
        return $content;
    }
}

if ( ! function_exists( 'is_wp_error' ) ) {
    function is_wp_error( $thing ) {
        return false;
    }
}

if ( ! function_exists( 'get_bloginfo' ) ) {
    function get_bloginfo( $show = '' ) {
        if ( 'version' === $show ) {
            return '6.9';
        }
        return '';
    }
}

if ( ! function_exists( 'includes_url' ) ) {
    function includes_url( $path = '' ) {
        return 'http://example.com/wp-includes/' . $path;
    }
}

// Stub WP_HTML_Tag_Processor used by get_url_in_content().
if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
    class WP_HTML_Tag_Processor {
        private $html;
        private $tags = array();
        private $index = -1;

        public function __construct( $html ) {
            $this->html = $html;
            // Simple regex to extract tags.
            preg_match_all( '/<a\s[^>]*>/i', $html, $matches );
            $this->tags = $matches[0] ?? array();
        }

        public function next_tag( $query = null ) {
            $this->index++;
            return isset( $this->tags[ $this->index ] );
        }

        public function get_attribute( $name ) {
            if ( ! isset( $this->tags[ $this->index ] ) ) {
                return null;
            }
            $tag = $this->tags[ $this->index ];
            if ( preg_match( '/' . preg_quote( $name, '/' ) . '=["\']([^"\']*)["\']/', $tag, $m ) ) {
                return $m[1];
            }
            return null;
        }
    }
}

// Stub WP_Http class.
if ( ! class_exists( 'WP_Http' ) ) {
    class WP_Http {
        public static function make_absolute_url( $maybe_relative_path, $url ) {
            if ( ! empty( $maybe_relative_path ) && '/' === $maybe_relative_path[0] ) {
                $parts = parse_url( $url );
                return ( $parts['scheme'] ?? 'http' ) . '://' . ( $parts['host'] ?? '' ) . $maybe_relative_path;
            }
            return $maybe_relative_path;
        }
    }
}

// Now load formatting.php.
require_once ABSPATH . WPINC . '/formatting.php';
