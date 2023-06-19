<?php
/**
 * Base class other (more-specific) classes inherit from.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class autoptimizeBase
{
    /**
     * Holds content being processed (html, scripts, styles)
     *
     * @var string
     */
    protected $content = '';

    /**
     * Controls debug logging.
     *
     * @var bool
     */
    public $debug_log = false;

    /**
     * Initiated $cdn_url.
     *
     * @var string
     */
    public $cdn_url = '';

    public function __construct( $content )
    {
        $this->content = $content;
    }

    /**
     * Reads the page and collects tags.
     *
     * @param array $options Options.
     *
     * @return bool
     */
    abstract public function read( $options );

    /**
     * Joins and optimizes collected things.
     *
     * @return bool
     */
    abstract public function minify();

    /**
     * Caches the things.
     *
     * @return void
     */
    abstract public function cache();

    /**
     * Returns the content
     *
     * @return string
     */
    abstract public function getcontent();

    /**
     * Tranfsorms a given URL to a full local filepath if possible.
     * Returns local filepath or false.
     *
     * @param string $url URL to transform.
     *
     * @return bool|string
     */
    public function getpath( $url )
    {
        $url = apply_filters( 'autoptimize_filter_cssjs_alter_url', $url );

        if ( is_null( $url ) ) {
            return false;
        }

        if ( false !== strpos( $url, '%' ) ) {
            $url = urldecode( $url );
        }

        $site_host    = parse_url( AUTOPTIMIZE_WP_SITE_URL, PHP_URL_HOST );
        $content_host = parse_url( AUTOPTIMIZE_WP_ROOT_URL, PHP_URL_HOST );

        // Normalizing attempts...
        $double_slash_position = strpos( $url, '//' );
        if ( 0 === $double_slash_position ) {
            if ( is_ssl() ) {
                $url = 'https:' . $url;
            } else {
                $url = 'http:' . $url;
            }
        } elseif ( ( false === $double_slash_position ) && ( false === strpos( $url, $site_host ) ) ) {
            if ( AUTOPTIMIZE_WP_SITE_URL === $site_host ) {
                $url = AUTOPTIMIZE_WP_SITE_URL . $url;
            } elseif ( 0 === strpos( $url, '/' ) ) {
                $url = '//' . $site_host . autoptimizeUtils::path_canonicalize( $url );
            } else {
                $url = AUTOPTIMIZE_WP_SITE_URL . autoptimizeUtils::path_canonicalize( $url );
            }
        }

        if ( $site_host !== $content_host ) {
            $url = str_replace( AUTOPTIMIZE_WP_CONTENT_URL, AUTOPTIMIZE_WP_SITE_URL . AUTOPTIMIZE_WP_CONTENT_NAME, $url );
        }

        // First check; hostname wp site should be hostname of url!
        $url_host = @parse_url( $url, PHP_URL_HOST ); // @codingStandardsIgnoreLine
        if ( $url_host !== $site_host ) {
            /**
             * First try to get all domains from WPML (if available)
             * then explicitely declare $this->cdn_url as OK as well
             * then apply own filter autoptimize_filter_cssjs_multidomain takes an array of hostnames
             * each item in that array will be considered part of the same WP multisite installation
             */
            $multidomains = array();

            $multidomains_wpml = apply_filters( 'wpml_setting', array(), 'language_domains' );
            if ( ! empty( $multidomains_wpml ) ) {
                $multidomains = array_map( array( $this, 'get_url_hostname' ), $multidomains_wpml );
            }

            if ( ! empty( $this->cdn_url ) ) {
                $multidomains[] = parse_url( $this->cdn_url, PHP_URL_HOST );
            }

            $multidomains = apply_filters( 'autoptimize_filter_cssjs_multidomain', $multidomains );

            if ( ! empty( $multidomains ) ) {
                if ( in_array( $url_host, $multidomains ) ) {
                    $url = str_replace( $url_host, $site_host, $url );
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // Try to remove "wp root url" from url while not minding http<>https.
        $tmp_ao_root = preg_replace( '/https?:/', '', AUTOPTIMIZE_WP_ROOT_URL );

        if ( $site_host !== $content_host ) {
            // As we replaced the content-domain with the site-domain, we should match against that.
            $tmp_ao_root = preg_replace( '/https?:/', '', AUTOPTIMIZE_WP_SITE_URL );
        }

        if ( is_multisite() && ! is_main_site() && ! empty( $this->cdn_url ) && apply_filters( 'autoptimize_filter_base_getpage_multisite_cdn_juggling', true ) ) {
            // multisite child sites with CDN need the network_site_url as tmp_ao_root but only if directory-based multisite.
            $_network_site_url = network_site_url();
            if ( strpos( AUTOPTIMIZE_WP_SITE_URL, $_network_site_url ) !== false ) {
                $tmp_ao_root = preg_replace( '/https?:/', '', $_network_site_url );
            }
        }

        $tmp_url = preg_replace( '/https?:/', '', $url );
        $path    = str_replace( $tmp_ao_root, '', $tmp_url );

        // If path starts with :// or //, this is not a URL in the WP context and
        // we have to assume we can't aggregate.
        if ( preg_match( '#^:?//#', $path ) ) {
            // External script/css (adsense, etc).
            return false;
        }

        // Prepend with WP_ROOT_DIR to have full path to file.
        $path = str_replace( '//', '/', trailingslashit( WP_ROOT_DIR ) . $path );

        // Allow path to be altered, e.g. in the case of bedrock-like setups where
        // core, theme & plugins might be in different locations on the filesystem.
        $path = apply_filters( 'autoptimize_filter_base_getpath_path', $path, $url );

        // Final check: does file exist and is it readable?
        if ( file_exists( $path ) && is_file( $path ) && is_readable( $path ) ) {
            return $path;
        } else {
            return false;
        }
    }

    /**
     * Returns the hostname part of a given $url if we're able to parse it.
     * If not, it returns the original url (prefixed with http:// scheme in case
     * it was missing).
     * Used as callback for WPML multidomains filter.
     *
     * @param string $url URL.
     *
     * @return string
     */
    protected function get_url_hostname( $url )
    {
        // Checking that the url starts with something vaguely resembling a protocol.
        if ( ( 0 !== strpos( $url, 'http' ) ) && ( 0 !== strpos( $url, '//' ) ) ) {
            $url = 'http://' . $url;
        }

        // Grab the hostname.
        $hostname = parse_url( $url, PHP_URL_HOST );

        // Fallback when parse_url() fails.
        if ( empty( $hostname ) ) {
            $hostname = $url;
        }

        return $hostname;
    }

    /**
     * Hides everything between noptimize-comment tags.
     *
     * @param string $markup Markup to process.
     *
     * @return string
     */
    protected function hide_noptimize( $markup )
    {
        return $this->replace_contents_with_marker_if_exists(
            'NOPTIMIZE',
            '/<!--\s?noptimize\s?-->/',
            '#<!--\s?noptimize\s?-->.*?<!--\s?/\s?noptimize\s?-->#is',
            $markup
        );
    }

    /**
     * Unhide noptimize-tags.
     *
     * @param string $markup Markup to process.
     *
     * @return string
     */
    protected function restore_noptimize( $markup )
    {
        return $this->restore_marked_content( 'NOPTIMIZE', $markup );
    }

    /**
     * Hides "iehacks" content.
     *
     * @param string $markup Markup to process.
     *
     * @return string
     */
    protected function hide_iehacks( $markup )
    {
        return $this->replace_contents_with_marker_if_exists(
            'IEHACK', // Marker name...
            '<!--[if', // Invalid regex, will fallback to search using strpos()...
            '#<!--\[if.*?\[endif\]-->#is', // Replacement regex...
            $markup
        );
    }

    /**
     * Restores "hidden" iehacks content.
     *
     * @param string $markup Markup to process.
     *
     * @return string
     */
    protected function restore_iehacks( $markup )
    {
        return $this->restore_marked_content( 'IEHACK', $markup );
    }

    /**
     * "Hides" content within HTML comments using a regex-based replacement
     * if HTML comment markers are found.
     * `<!--example-->` becomes `%%COMMENTS%%ZXhhbXBsZQ==%%COMMENTS%%`
     *
     * @param string $markup Markup to process.
     *
     * @return string
     */
    protected function hide_comments( $markup )
    {
        return $this->replace_contents_with_marker_if_exists(
            'COMMENTS',
            '<!--',
            '#<!--.*?-->#is',
            $markup
        );
    }

    /**
     * Restores original HTML comment markers inside a string whose HTML
     * comments have been "hidden" by using `hide_comments()`.
     *
     * @param string $markup Markup to process.
     *
     * @return string
     */
    protected function restore_comments( $markup )
    {
        return $this->restore_marked_content( 'COMMENTS', $markup );
    }

    /**
     * Replaces the given URL with the CDN-version of it when CDN replacement
     * is supposed to be done.
     *
     * @param string $url URL to process.
     *
     * @return string
     */
    public function url_replace_cdn( $url )
    {
        // For 2.3 back-compat in which cdn-ing appeared to be automatically
        // including WP subfolder/subdirectory into account as part of cdn-ing,
        // even though it might've caused serious troubles in certain edge-cases.
        $cdn_url = autoptimizeUtils::tweak_cdn_url_if_needed( $this->cdn_url );

        // Allows API/filter to further tweak the cdn url...
        $cdn_url = apply_filters( 'autoptimize_filter_base_cdnurl', $cdn_url );
        if ( ! empty( $cdn_url ) && false === strpos( $url, $cdn_url ) && false !== apply_filters( 'autoptimize_filter_base_apply_cdn', true, $url ) ) {

            // Simple str_replace-based approach fails when $url is protocol-or-host-relative.
            $is_protocol_relative = autoptimizeUtils::is_protocol_relative( $url );
            $is_host_relative     = ( ! $is_protocol_relative && ( '/' === $url[0] ) );
            $cdn_url              = esc_url( rtrim( $cdn_url, '/' ) );

            if ( $is_host_relative ) {
                // Prepending host-relative urls with the cdn url.
                $url = $cdn_url . $url;
            } else {
                // Either a protocol-relative or "regular" url, replacing it either way.
                if ( $is_protocol_relative ) {
                    // Massage $site_url so that simple str_replace() still "works" by
                    // searching for the protocol-relative version of AUTOPTIMIZE_WP_SITE_URL.
                    $site_url = str_replace( array( 'http:', 'https:' ), '', AUTOPTIMIZE_WP_SITE_URL );
                } else {
                    $site_url = AUTOPTIMIZE_WP_SITE_URL;
                }
                $url = str_replace( $site_url, $cdn_url, $url );
            }
        }

        // Allow API filter to take further care of CDN replacement.
        $url = apply_filters( 'autoptimize_filter_base_replace_cdn', $url );

        return $url;
    }

    /**
     * Injects/replaces the given payload markup into `$this->content`
     * at the specified location.
     * If the specified tag cannot be found, the payload is appended into
     * $this->content along with a warning wrapped inside <!--noptimize--> tags.
     *
     * @param string $payload Markup to inject.
     * @param array  $where   Array specifying the tag name and method of injection.
     *                        Index 0 is the tag name (i.e., `</body>`).
     *                        Index 1 specifies ˛'before', 'after' or 'replace'. Defaults to 'before'.
     *
     * @return void
     */
    protected function inject_in_html( $payload, $where )
    {
        $warned   = false;
        $position = autoptimizeUtils::strpos( $this->content, $where[0] );
        if ( false !== $position ) {
            // Found the tag, setup content/injection as specified.
            if ( 'after' === $where[1] ) {
                $content = $where[0] . $payload;
            } elseif ( 'replace' === $where[1] ) {
                $content = $payload;
            } else {
                $content = $payload . $where[0];
            }
            // Place where specified.
            $this->content = autoptimizeUtils::substr_replace(
                $this->content,
                $content,
                $position,
                // Using plain strlen() should be safe here for now, since
                // we're not searching for multibyte chars here still...
                strlen( $where[0] )
            );
        } else {
            // Couldn't find what was specified, just append and add a warning.
            $this->content .= $payload;
            if ( ! $warned ) {
                $tag_display    = str_replace( array( '<', '>' ), '', $where[0] );
                $this->content .= '<!--noptimize--><!-- Autoptimize found a problem with the HTML in your Theme, tag `' . $tag_display . '` missing --><!--/noptimize-->';
                $warned         = true;
            }
        }
    }

    /**
     * Returns true if given `$tag` is found in the list of `$removables`.
     *
     * @param string $tag Tag to search for.
     * @param array  $removables List of things considered completely removable.
     *
     * @return bool
     */
    protected function isremovable( $tag, $removables )
    {
        foreach ( $removables as $match ) {
            if ( false !== strpos( $tag, $match ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Callback used in `self::inject_minified()`.
     *
     * @param array $matches Regex matches.
     *
     * @return string
     */
    public function inject_minified_callback( $matches )
    {
        static $conf = null;
        if ( null === $conf ) {
            $conf = autoptimizeConfig::instance();
        }

        /**
         * $matches[1] holds the whole match caught by regex in self::inject_minified(),
         * so we take that and split the string on `|`.
         * First element is the filepath, second is the md5 hash of contents
         * the filepath had when it was being processed.
         * If we don't have those, we'll bail out early.
        */
        $filepath = null;
        $filehash = null;

        // Grab the parts we need.
        $parts = explode( '|', $matches[1] );
        if ( ! empty( $parts ) ) {
            $filepath = isset( $parts[0] ) ? base64_decode( $parts[0] ) : null;
            $filehash = isset( $parts[1] ) ? $parts[1] : null;
        }

        // Bail early if something's not right...
        if ( ! $filepath || ! $filehash ) {
            return "\n";
        }

        $filecontent = file_get_contents( $filepath );

        // Some things are differently handled for css/js...
        $is_js_file = ( '.js' === substr( $filepath, -3, 3 ) );

        $is_css_file = false;
        if ( ! $is_js_file ) {
            $is_css_file = ( '.css' === substr( $filepath, -4, 4 ) );
        }

        // BOMs being nuked here unconditionally (regardless of where they are)!
        $filecontent = preg_replace( "#\x{EF}\x{BB}\x{BF}#", '', $filecontent );

        // Remove comments and blank lines.
        if ( $is_js_file ) {
            $filecontent = preg_replace( '#^\s*\/\/.*$#Um', '', $filecontent );
        }

        // Nuke un-important comments.
        $filecontent = preg_replace( '#^\s*\/\*[^!].*\*\/\s?#Um', '', $filecontent );

        // Normalize newlines.
        $filecontent = preg_replace( '#(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#', "\n", $filecontent );

        // JS specifics.
        if ( $is_js_file ) {
            // Append a semicolon at the end of js files if it's missing.
            $last_char = substr( $filecontent, -1, 1 );
            if ( ';' !== $last_char && '}' !== $last_char ) {
                $filecontent .= ';';
            }
            // Check if try/catch should be used.
            $opt_js_try_catch = $conf->get( 'autoptimize_js_trycatch' );
            if ( 'on' === $opt_js_try_catch ) {
                // It should, wrap in try/catch.
                $filecontent = 'try{' . $filecontent . '}catch(e){}';
            }
        } elseif ( $is_css_file ) {
            $filecontent = autoptimizeStyles::fixurls( $filepath, $filecontent );
        } else {
            $filecontent = '';
        }

        // Return modified (or empty!) code/content.
        return "\n" . $filecontent;
    }

    /**
     * Inject already minified code in optimized JS/CSS.
     *
     * @param string $in Markup.
     *
     * @return string
     */
    protected function inject_minified( $in )
    {
        $out = $in;
        if ( false !== strpos( $in, '%%INJECTLATER%%' ) ) {
            $out = preg_replace_callback(
                '#\/\*\!%%INJECTLATER' . AUTOPTIMIZE_HASH . '%%(.*?)%%INJECTLATER%%\*\/#is',
                array( $this, 'inject_minified_callback' ),
                $in
            );
        }

        return $out;
    }

    /**
     * Specialized method to create the INJECTLATER marker.
     * These are somewhat "special", in the sense that they're additionally wrapped
     * within an "exclamation mark style" comment, so that they're not stripped
     * out by minifiers.
     * They also currently contain the hash of the file's contents too (unlike other markers).
     *
     * @param string $filepath Filepath.
     * @param string $hash Hash.
     *
     * @return string
     */
    public static function build_injectlater_marker( $filepath, $hash )
    {
        $contents = '/*!' . self::build_marker( 'INJECTLATER', $filepath, $hash ) . '*/';

        return $contents;
    }

    /**
     * Creates and returns a `%%`-style named marker which holds
     * the base64 encoded `$data`.
     * If `$hash` is provided, it's appended to the base64 encoded string
     * using `|` as the separator (in order to support building the
     * somewhat special/different INJECTLATER marker).
     *
     * @param string      $name Marker name.
     * @param string      $data Marker data which will be base64-encoded.
     * @param string|null $hash Optional.
     *
     * @return string
     */
    public static function build_marker( $name, $data, $hash = null )
    {
        // Start the marker, add the data.
        $marker = '%%' . $name . AUTOPTIMIZE_HASH . '%%' . base64_encode( $data );

        // Add the hash if provided.
        if ( null !== $hash ) {
            $marker .= '|' . $hash;
        }

        // Close the marker.
        $marker .= '%%' . $name . '%%';

        return $marker;
    }

    /**
     * Searches for `$search` in `$content` (using either `preg_match()`
     * or `strpos()`, depending on whether `$search` is a valid regex pattern or not).
     * If something is found, it replaces `$content` using `$re_replace_pattern`,
     * effectively creating our named markers (`%%{$marker}%%`.
     * These are then at some point replaced back to their actual/original/modified
     * contents using `autoptimizeBase::restore_marked_content()`.
     *
     * @param string $marker Marker name (without percent characters).
     * @param string $search A string or full blown regex pattern to search for in $content. Uses `strpos()` or `preg_match()`.
     * @param string $re_replace_pattern Regex pattern to use when replacing contents.
     * @param string $content Content to work on.
     *
     * @return string
     */
    public static function replace_contents_with_marker_if_exists( $marker, $search, $re_replace_pattern, $content )
    {
        $found = false;

        $is_regex = autoptimizeUtils::str_is_valid_regex( $search );
        if ( $is_regex ) {
            $found = preg_match( $search, $content );
        } else {
            $found = ( false !== strpos( $content, $search ) );
        }

        if ( $found ) {
            $content = preg_replace_callback(
                $re_replace_pattern,
                function( $matches ) use ( $marker ) {
                    return autoptimizeBase::build_marker( $marker, $matches[0] );
                },
                $content
            );
        }

        return $content;
    }

    /**
     * Complements `autoptimizeBase::replace_contents_with_marker_if_exists()`.
     *
     * @param string $marker Marker.
     * @param string $content Markup.
     *
     * @return string
     */
    public static function restore_marked_content( $marker, $content )
    {
        if ( false !== strpos( $content, $marker ) ) {
            $content = preg_replace_callback(
                '#%%' . $marker . AUTOPTIMIZE_HASH . '%%(.*?)%%' . $marker . '%%#is',
                function ( $matches ) {
                    return base64_decode( $matches[1] );
                },
                $content
            );
        }

        return $content;
    }

    /**
     * Logs given `$data` for debugging purposes (when debug logging is on).
     *
     * @param mixed $data Data to log.
     *
     * @return void
     */
    protected function debug_log( $data )
    {
        if ( ! isset( $this->debug_log ) || ! $this->debug_log ) {
            return;
        }

        if ( ! is_string( $data ) && ! is_resource( $data ) ) {
            $data = var_export( $data, true );
        }

        error_log( $data );
    }

    /**
     * Checks if a single local css/js file can be minified and returns source if so.
     *
     * @param string $filepath Filepath.
     *
     * @return bool|string to be minified code or false.
     */
    protected function prepare_minify_single( $filepath )
    {
        // Decide what we're dealing with, return false if we don't know.
        if ( autoptimizeUtils::str_ends_in( $filepath, '.js' ) ) {
            $type = 'js';
        } elseif ( autoptimizeUtils::str_ends_in( $filepath, '.css' ) ) {
            $type = 'css';
        } else {
            return false;
        }

        // Bail if it looks like its already minifed (by having -min or .min in filename).
        $minified_variants = array(
            '-min.' . $type,
            '.min.' . $type,
        );
        foreach ( $minified_variants as $ending ) {
            if ( autoptimizeUtils::str_ends_in( $filepath, $ending ) && true === apply_filters( 'autoptimize_filter_base_prepare_exclude_minified', true ) ) {
                return false;
            }
        }

        // Get file contents, bail if empty.
        $contents = file_get_contents( $filepath );

        return $contents;
    }

    /**
     * Given an autoptimizeCache instance returns the (maybe cdn-ed) url of
     * the cached file.
     *
     * @param autoptimizeCache $cache autoptimizeCache instance.
     *
     * @return string
     */
    protected function build_minify_single_url( autoptimizeCache $cache )
    {
        $url = AUTOPTIMIZE_CACHE_URL . $cache->getname();

        // CDN-replace the resulting URL if needed...
        $url = $this->url_replace_cdn( $url );

        return $url;
    }
}
