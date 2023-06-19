<?php
/**
 * Class for CSS optimization.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeStyles extends autoptimizeBase
{
    const ASSETS_REGEX = '/url\s*\(\s*(?!["\']?data:)(?![\'|\"]?[\#|\%|])([^)]+)\s*\)([^;},\s]*)/i';

    /**
     * Font-face regex-fu from HamZa at: https://stackoverflow.com/a/21395083
     */
    const FONT_FACE_REGEX = '~@font-face\s*(\{(?:[^{}]+|(?1))*\})~xsi'; // added `i` flag for case-insensitivity.

    /**
     * Store CSS.
     *
     * @var array
     */
    private $css = array();

    /**
     * To store CSS code
     *
     * @var array
     */
    private $csscode = array();

    /**
     * To store urls
     *
     * @var array
     */
    private $url = array();

    /**
     * String to store rest of content (when old setting "only in head" is used)
     *
     * @var string
     */
    private $restofcontent = '';

    /**
     * Setting to change small images to inline CSS
     *
     * @var bool
     */
    private $datauris = false;

    /**
     * Array to store hashmap
     *
     * @var array
     */
    private $hashmap = array();

    /**
     * Flag to indicate if CSS is already minified
     *
     * @var bool
     */
    private $alreadyminified = false;

    /**
     * Setting if CSS should be aggregated
     *
     * @var bool
     */
    private $aggregate = true;

    /**
     * Setting if all CSS should be inlined
     *
     * @var bool
     */
    private $inline = false;

    /**
     * Setting if CSS should be deferred
     *
     * @var bool
     */
    private $defer = false;

    /**
     * Setting for to be inlined CSS.
     *
     * @var string
     */
    private $defer_inline = '';

    /**
     * Setting for allowlist of what should be aggregated.
     *
     * @var string
     */
    private $allowlist = '';

    /**
     * Setting (only filter) for size under which CSS should be inlined instead of linked.
     *
     * @var string
     */
    private $cssinlinesize = '';

    /**
     * Setting (only filter) of CSS that can be removed.
     *
     * @var array
     */
    private $cssremovables = array();

    /**
     * Setting: should inline CSS be aggregated.
     *
     * @var bool
     */
    private $include_inline = false;

    /**
     * Setting (only filter) if minified CSS can be injected after minificatoin of aggregated CSS.
     *
     * @var bool
     */
    private $inject_min_late = true;

    /**
     * Holds all exclusions.
     *
     * @var array
     */
    private $dontmove = array();

    /**
     * Holds all options.
     *
     * @var array
     */
    private $options = array();

    /**
     * Setting; should excluded CSS-files be minified.
     *
     * @var bool
     */
    private $minify_excluded = true;

    /**
     * Setting (filter only); should all media-attributes be forced to "all".
     *
     * @var bool
     */
    private $media_force_all = false;

    /**
     * Reads the page and collects style tags.
     *
     * @param array $options all options.
     */
    public function read( $options )
    {
        $noptimize_css = apply_filters( 'autoptimize_filter_css_noptimize', false, $this->content );
        if ( $noptimize_css || false === autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_css_optimize' ) ) {
            return false;
        }

        $allowlist_css = apply_filters( 'autoptimize_filter_css_allowlist', '', $this->content );
        if ( ! empty( $allowlist_css ) ) {
            $this->allowlist = array_filter( array_map( 'trim', explode( ',', $allowlist_css ) ) );
        }

        $removable_css = apply_filters( 'autoptimize_filter_css_removables', '' );
        if ( ! empty( $removable_css ) ) {
            $this->cssremovables = array_filter( array_map( 'trim', explode( ',', $removable_css ) ) );
        }

        $this->cssinlinesize = apply_filters( 'autoptimize_filter_css_inlinesize', 256 );

        // filter to "late inject minified CSS", default to true for now (it is faster).
        $this->inject_min_late = apply_filters( 'autoptimize_filter_css_inject_min_late', true );

        // Remove everything that's not the header.
        if ( apply_filters( 'autoptimize_filter_css_justhead', $options['justhead'] ) ) {
            $content             = explode( '</head>', $this->content, 2 );
            $this->content       = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }

        // Determine whether we're doing CSS-files aggregation or not.
        if ( isset( $options['aggregate'] ) && ! $options['aggregate'] ) {
            $this->aggregate = false;
        }
        // Returning true for "dontaggregate" turns off aggregation.
        if ( $this->aggregate && apply_filters( 'autoptimize_filter_css_dontaggregate', false ) ) {
            $this->aggregate = false;
        }
        // and the filter that should have been there to begin with.
        $this->aggregate = apply_filters( 'autoptimize_filter_css_aggregate', $this->aggregate );

        // include inline?
        if ( apply_filters( 'autoptimize_css_include_inline', $options['include_inline'] ) ) {
            $this->include_inline = true;
        }

        // List of CSS strings which are excluded from autoptimization.
        $exclude_css = apply_filters( 'autoptimize_filter_css_exclude', $options['css_exclude'], $this->content );
        if ( '' !== $exclude_css ) {
            $this->dontmove = array_filter( array_map( 'trim', explode( ',', $exclude_css ) ) );
        } else {
            $this->dontmove = array();
        }

        // forcefully exclude CSS with data-noptimize attrib.
        $this->dontmove[] = 'data-noptimize';

        // forcefully exclude inline CSS with ".wp-container-" which due to the random-ish nature busts AO's cache continuously.
        $this->dontmove[] = '.wp-container-';

        // Should we defer css?
        // value: true / false.
        $this->defer = $options['defer'];
        $this->defer = apply_filters( 'autoptimize_filter_css_defer', $this->defer, $this->content );

        // If page/ post check post_meta to see if optimize is off.
        if ( $this->defer && false === autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_ccss' ) ) {
             $this->defer = false;
        }

        // Should we inline while deferring?
        // value: inlined CSS.
        $this->defer_inline = apply_filters( 'autoptimize_filter_css_defer_inline', $this->sanitize_css( $options['defer_inline'] ), $this->content );

        // Should we inline?
        // value: true / false.
        $this->inline = $options['inline'];
        $this->inline = apply_filters( 'autoptimize_filter_css_inline', $this->inline, $this->content );

        // Store cdn url.
        $this->cdn_url = $options['cdn_url'];

        // Store data: URIs setting for later use.
        $this->datauris = $options['datauris'];

        // Determine whether excluded files should be minified if not yet so.
        if ( ! $options['minify_excluded'] && $options['aggregate'] ) {
            $this->minify_excluded = false;
        }
        $this->minify_excluded = apply_filters( 'autoptimize_filter_css_minify_excluded', $this->minify_excluded, '' );

        // should we force all media-attributes to all?
        $this->media_force_all = apply_filters( 'autoptimize_filter_css_tagmedia_forceall', false );

        // noptimize me.
        $this->content = $this->hide_noptimize( $this->content );

        // Exclude (no)script, as those may contain CSS which should be left as is.
        $this->content = $this->replace_contents_with_marker_if_exists(
            'SCRIPT',
            '<script',
            '#<(?:no)?script.*?<\/(?:no)?script>#is',
            $this->content
        );

        // Save IE hacks.
        $this->content = $this->hide_iehacks( $this->content );

        // Hide HTML comments.
        $this->content = $this->hide_comments( $this->content );

        // Get <style> and <link>.
        if ( preg_match_all( '#(<style[^>]*>.*</style>)|(<link[^>]*stylesheet[^>]*>)#Usmi', $this->content, $matches ) ) {

            foreach ( $matches[0] as $tag ) {
                if ( $this->isremovable( $tag, $this->cssremovables ) ) {
                    $this->content = str_replace( $tag, '', $this->content );
                } elseif ( $this->ismovable( $tag ) ) {
                    // Get the media.
                    if ( false !== strpos( $tag, 'media=' ) ) {
                        preg_match( '#media=(?:"|\')([^>]*)(?:"|\')#Ui', $tag, $medias );
                        if ( ! empty( $medias ) ) {
                            $medias = explode( ',', $medias[1] );
                            $media  = array();
                            foreach ( $medias as $elem ) {
                                if ( empty( $elem ) ) {
                                    $elem = 'all';
                                }

                                $media[] = $elem;
                            }
                        } else {
                            $media = array( 'all' );
                        }
                    } else {
                        // No media specified - applies to all.
                        $media = array( 'all' );
                    }

                    // forcing media attribute to all to merge all in one file.
                    if ( $this->media_force_all ) {
                        $media = array( 'all' );
                    }

                    $media = apply_filters( 'autoptimize_filter_css_tagmedia', $media, $tag );

                    if ( preg_match( '#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source ) ) {
                        // <link>.
                        $url  = current( explode( '?', $source[2], 2 ) );
                        $path = $this->getpath( $url );

                        if ( false !== $path && preg_match( '#\.css$#', $path ) ) {
                            // Good link.
                            $this->css[] = array( $media, $path );
                        } else {
                            // Link is dynamic (.php etc).
                            $new_tag = $this->optionally_defer_excluded( $tag, 'none' );
                            if ( '' !== $new_tag && $new_tag !== $tag ) {
                                $this->content = str_replace( $tag, $new_tag, $this->content );
                            }
                            $tag = '';
                        }
                    } else {
                        // Inline css in style tags can be wrapped in comment tags, so restore comments.
                        $tag = $this->restore_comments( $tag );
                        preg_match( '#<style.*>(.*)</style>#Usmi', $tag, $code );

                        // And re-hide them to be able to to the removal based on tag.
                        $tag = $this->hide_comments( $tag );

                        if ( $this->include_inline ) {
                            $code        = preg_replace( '#^.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*$#sm', '$1', $code[1] );
                            $this->css[] = array( $media, 'INLINE;' . $code );
                        } else {
                            $tag = '';
                        }
                    }

                    // Remove the original style tag.
                    $this->content = str_replace( $tag, '', $this->content );
                } else {
                    if ( preg_match( '#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source ) ) {
                        $exploded_url = explode( '?', $source[2], 2 );
                        $url          = $exploded_url[0];
                        $path         = $this->getpath( $url );
                        $new_tag      = $tag;

                        // Excluded CSS, minify that file:
                        // -> if aggregate is on and exclude minify is on
                        // -> if aggregate is off and the file is not in dontmove.
                        if ( $path && $this->minify_excluded ) {
                            $consider_minified_array = apply_filters( 'autoptimize_filter_css_consider_minified', false );
                            if ( ( false === $this->aggregate && str_replace( $this->dontmove, '', $path ) === $path ) || ( true === $this->aggregate && ( false === $consider_minified_array || str_replace( $consider_minified_array, '', $path ) === $path ) ) ) {
                                $minified_url = $this->minify_single( $path );
                                if ( ! empty( $minified_url ) ) {
                                    // Replace orig URL with cached minified URL.
                                    $new_tag = str_replace( $url, $minified_url, $tag );
                                } elseif ( apply_filters( 'autoptimize_filter_ccsjs_remove_empty_minified_url', false ) ) {
                                    // Remove the original style tag, because cache content is empty but only if
                                    // filter is true-ed because $minified_url is also false if file is minified already.
                                    $new_tag = '';
                                }
                            }
                        }

                        if ( '' !== $new_tag ) {
                            // Optionally defer (preload) non-aggregated CSS.
                            $new_tag = $this->optionally_defer_excluded( $new_tag, $url );

                            // Check if we still need to CDN (esp. for already minified resources).
                            if ( ! empty( $this->cdn_url ) || has_filter( 'autoptimize_filter_base_replace_cdn' ) ) {
                                $new_tag = str_replace( $url, $this->url_replace_cdn( $url ), $new_tag );
                            }
                        }

                        // And replace!
                        if ( ( '' !== $new_tag && $new_tag !== $tag ) || ( '' === $new_tag && apply_filters( 'autoptimize_filter_css_remove_empty_files', false ) ) ) {
                            $this->content = str_replace( $tag, $new_tag, $this->content );
                        }
                    }
                }
            }
            return true;
        }

        // Really, no styles?
        return false;
    }

    /**
     * Checks if non-optimized CSS is to be preloaded and if so return
     * the tag with preload code.
     *
     * @param string $tag (required).
     * @param string $url (optional).
     *
     * @return string $new_tag
     */
    private function optionally_defer_excluded( $tag, $url = '' )
    {
        // Defer single CSS if "inline & defer" is ON and there is inline CSS.
        if ( ! empty( $tag ) && false === strpos( $tag, ' onload=' ) && $this->defer && ! empty( $this->defer_inline ) && apply_filters( 'autoptimize_filter_css_defer_excluded', true, $tag ) ) {
            // get media attribute and based on that create onload JS attribute value.
            if ( false === strpos( $tag, 'media=' ) ) {
                $tag = str_replace( '<link', "<link media='all'", $tag );
            }

            preg_match( '#media=(?:"|\')([^>]*)(?:"|\')#Ui', $tag, $_medias );
            $_media          = $_medias[1];
            $_preload_onload = autoptimizeConfig::get_ao_css_preload_onload( $_media );

            if ( 'print' !== $_media ) {
                // If not media=print, adapt original <link> element for CSS to be preloaded and add <noscript>-version for fallback.
                $new_tag = '<noscript>' . autoptimizeUtils::remove_id_from_node( $tag ) . '</noscript>' . str_replace(
                    $_medias[0],
                    "media='print' onload=\"" . $_preload_onload . '"',
                    $tag
                );

                // Optionally (but default false) preload the (excluded) CSS-file.
                if ( apply_filters( 'autoptimize_fitler_css_preload_and_print', false ) && 'none' !== $url ) {
                    $new_tag = '<link rel="preload" as="stylesheet" href="' . $url . '"/>' . $new_tag;
                }
            } else {
                $new_tag = $tag;
            }

            return $new_tag;
        }

        // Return unchanged $tag.
        return $tag;
    }

    /**
     * Checks if the local file referenced by $path is a valid
     * candidate for being inlined into a data: URI
     *
     * @param string $path image path.
     * @return boolean
     */
    private function is_datauri_candidate( $path )
    {
        // Call only once since it's called from a loop.
        static $max_size = null;
        if ( null === $max_size ) {
            $max_size = $this->get_datauri_maxsize();
        }

        if ( $path && preg_match( '#\.(jpe?g|png|gif|webp|bmp)$#i', $path ) &&
            file_exists( $path ) && is_readable( $path ) && filesize( $path ) <= $max_size ) {

            // Seems we have a candidate.
            $is_candidate = true;
        } else {
            // Filter allows overriding default decision (which checks for local file existence).
            $is_candidate = apply_filters( 'autoptimize_filter_css_is_datauri_candidate', false, $path );
        }

        return $is_candidate;
    }

    /**
     * Returns the amount of bytes that shouldn't be exceeded if a file is to
     * be inlined into a data: URI. Defaults to 4096, passed through
     * `autoptimize_filter_css_datauri_maxsize` filter.
     *
     * @return mixed
     */
    private function get_datauri_maxsize()
    {
        static $max_size = null;

        /**
         * No need to apply the filter multiple times in case the
         * method itself is invoked multiple times during a single request.
         * This prevents some wild stuff like having different maxsizes
         * for different files/site-sections etc. But if you're into that sort
         * of thing you're probably better of building assets completely
         * outside of WordPress anyway.
         */
        if ( null === $max_size ) {
            $max_size = (int) apply_filters( 'autoptimize_filter_css_datauri_maxsize', 4096 );
        }

        return $max_size;
    }

    private function check_datauri_exclude_list( $url )
    {
        static $exclude_list = null;
        static $no_datauris  = array();

        // Again, skip doing certain stuff repeatedly when loop-called.
        if ( null === $exclude_list ) {
            $exclude_list = apply_filters( 'autoptimize_filter_css_datauri_exclude', '' );
            $no_datauris  = array_filter( array_map( 'trim', explode( ',', $exclude_list ) ) );
        }

        $matched = false;

        if ( ! empty( $exclude_list ) ) {
            foreach ( $no_datauris as $no_datauri ) {
                if ( false !== strpos( $url, $no_datauri ) ) {
                    $matched = true;
                    break;
                }
            }
        }

        return $matched;
    }

    private function build_or_get_datauri_image( $path )
    {
        /**
         * TODO/FIXME: document the required return array format, or better yet,
         * use a string, since we don't really need an array for this. That would, however,
         * require changing even more code, which is not happening right now...
         */

        // Allows short-circuiting datauri generation for an image.
        $result = apply_filters( 'autoptimize_filter_css_datauri_image', array(), $path );
        if ( ! empty( $result ) ) {
            if ( is_array( $result ) && isset( $result['full'] ) && isset( $result['base64data'] ) ) {
                return $result;
            }
        }

        $hash  = md5( $path );
        $check = new autoptimizeCache( $hash, 'img' );
        if ( $check->check() ) {
            // we have the base64 image in cache.
            $head_and_data = $check->retrieve();
            $_base64data   = explode( ';base64,', $head_and_data );
            $base64data    = $_base64data[1];
            unset( $_base64data );
        } else {
            // It's an image and we don't have it in cache, get the type by extension.
            $exploded_path = explode( '.', $path );
            $type          = end( $exploded_path );

            switch ( $type ) {
                case 'jpg':
                case 'jpeg':
                    $dataurihead = 'data:image/jpeg;base64,';
                    break;
                case 'gif':
                    $dataurihead = 'data:image/gif;base64,';
                    break;
                case 'png':
                    $dataurihead = 'data:image/png;base64,';
                    break;
                case 'bmp':
                    $dataurihead = 'data:image/bmp;base64,';
                    break;
                case 'webp':
                    $dataurihead = 'data:image/webp;base64,';
                    break;
                default:
                    $dataurihead = 'data:application/octet-stream;base64,';
            }

            // Encode the data.
            $base64data    = base64_encode( file_get_contents( $path ) );
            $head_and_data = $dataurihead . $base64data;

            // Save in cache.
            $check->cache( $head_and_data, 'text/plain' );
        }
        unset( $check );

        return array(
            'full'       => $head_and_data,
            'base64data' => $base64data,
        );
    }

    /**
     * Given an array of key/value pairs to replace in $string,
     * it does so by replacing the longest-matching strings first.
     *
     * @param string $string string in which to replace.
     * @param array  $replacements to be replaced strings and replacement.
     *
     * @return string
     */
    protected static function replace_longest_matches_first( $string, $replacements = array() )
    {
        if ( ! empty( $replacements ) ) {
            // Sort the replacements array by key length in desc order (so that the longest strings are replaced first).
            $keys = array_map( 'strlen', array_keys( $replacements ) );
            array_multisort( $keys, SORT_DESC, $replacements );
            $string = str_replace( array_keys( $replacements ), array_values( $replacements ), $string );
        }

        return $string;
    }

    /**
     * Rewrites/Replaces any ASSETS_REGEX-matching urls in a string.
     * Removes quotes/cruft around each one and passes it through to
     * `autoptimizeBase::url_replace_cdn()`.
     * Replacements are performed in a `longest-match-replaced-first` way.
     *
     * @param string $code CSS code.
     *
     * @return string
     */
    public function replace_urls( $code = '' )
    {
        $replacements = array();

        preg_match_all( self::ASSETS_REGEX, $code, $url_src_matches );
        if ( is_array( $url_src_matches ) && ! empty( $url_src_matches ) ) {
            foreach ( $url_src_matches[1] as $count => $original_url ) {
                // Removes quotes and other cruft.
                $url = trim( $original_url, " \t\n\r\0\x0B\"'" );

                /**
                 * TODO/FIXME: Add a way for other code / callable to be called here
                 * and provide it's own results for the $replacements array
                 * for the "current" key.
                 * If such a result is returned/provided, we sholud then avoid
                 * calling url_replace_cdn() here for the current iteration.
                 *
                 * This would maybe allow the inlining logic currently present
                 * in `autoptimizeStyles::rewrite_assets()` to be "pulled out"
                 * and given as a callable to this method or something... and
                 * then we could "just" call `replace_urls()` from within
                 * `autoptimizeStyles::rewrite_assets()` and avoid some
                 * (currently present) code/logic duplication.
                 */

                // Do CDN replacement if needed.
                if ( ! empty( $this->cdn_url ) ) {
                    $replacement_url = $this->url_replace_cdn( $url );
                    // Prepare replacements array.
                    $replacements[ $url_src_matches[1][ $count ] ] = str_replace(
                        $original_url,
                        $replacement_url,
                        $url_src_matches[1][ $count ]
                    );
                }
            }
        }

        $code = self::replace_longest_matches_first( $code, $replacements );

        return $code;
    }

    /**
     * "Hides" @font-face declarations by replacing them with `%%FONTFACE%%` markers.
     * Also does CDN replacement of any font-urls within those declarations if the `autoptimize_filter_css_fonts_cdn`
     * filter is used.
     *
     * @param string $code HTML being processed to hide fonts.
     * @return string
     */
    public function hide_fontface_and_maybe_cdn( $code )
    {
        // Proceed only if @font-face declarations exist within $code.
        preg_match_all( self::FONT_FACE_REGEX, $code, $fontfaces );
        if ( isset( $fontfaces[0] ) ) {
            // Check if we need to cdn fonts or not.
            $do_font_cdn = apply_filters( 'autoptimize_filter_css_fonts_cdn', false );

            foreach ( $fontfaces[0] as $full_match ) {
                // Keep original match so we can search/replace it.
                $match_search = $full_match;

                // Do font cdn if needed.
                if ( $do_font_cdn ) {
                    $full_match = $this->replace_urls( $full_match );
                }

                // Replace declaration with its base64 encoded string.
                $replacement = self::build_marker( 'FONTFACE', $full_match );
                $code        = str_replace( $match_search, $replacement, $code );
            }
        }

        return $code;
    }

    /**
     * Restores original @font-face declarations that have been "hidden"
     * using `hide_fontface_and_maybe_cdn()`.
     *
     * @param string $code HTML being processed to unhide fonts.
     * @return string
     */
    public function restore_fontface( $code )
    {
        return $this->restore_marked_content( 'FONTFACE', $code );
    }

    /**
     * Re-write (and/or inline) referenced assets.
     *
     * @param string $code HTML being processed rewrite assets.
     * @return string
     */
    public function rewrite_assets( $code )
    {
        // Handle @font-face rules by hiding and processing them separately.
        $code = $this->hide_fontface_and_maybe_cdn( $code );

        /**
         * TODO/FIXME:
         * Certain code parts below are kind-of repeated now in `replace_urls()`, which is not ideal.
         * There is maybe a way to separate/refactor things and then be able to keep
         * the ASSETS_REGEX rewriting/handling logic in a single place (along with removing quotes/cruft from matched urls).
         * See comments in `replace_urls()` regarding this. The idea is to extract the inlining
         * logic out (which is the only real difference between replace_urls() and the code below), but still
         * achieve identical results as before.
         */

        // Re-write (and/or inline) URLs to point them to the CDN host.
        $url_src_matches = array();
        $imgreplace      = array();

        // Matches and captures anything specified within the literal `url()` and excludes those containing data: URIs.
        preg_match_all( self::ASSETS_REGEX, $code, $url_src_matches );
        if ( is_array( $url_src_matches ) && ! empty( $url_src_matches ) ) {
            foreach ( $url_src_matches[1] as $count => $original_url ) {
                // Removes quotes and other cruft.
                $url = trim( $original_url, " \t\n\r\0\x0B\"'" );

                // If datauri inlining is turned on, do it.
                $inlined = false;
                if ( $this->datauris ) {
                    $iurl = $url;
                    if ( false !== strpos( $iurl, '?' ) ) {
                        $iurl = strtok( $iurl, '?' );
                    }

                    $ipath = $this->getpath( $iurl );

                    $excluded = $this->check_datauri_exclude_list( $ipath );
                    if ( ! $excluded ) {
                        $is_datauri_candidate = $this->is_datauri_candidate( $ipath );
                        if ( $is_datauri_candidate ) {
                            $datauri    = $this->build_or_get_datauri_image( $ipath );
                            $base64data = $datauri['base64data'];
                            // Add it to the list for replacement.
                            $imgreplace[ $url_src_matches[1][ $count ] ] = str_replace(
                                $original_url,
                                $datauri['full'],
                                $url_src_matches[1][ $count ]
                            );
                            $inlined                                     = true;
                        }
                    }
                }

                /**
                 * Doing CDN URL replacement for every found match (if CDN is
                 * specified). This way we make sure to do it even if
                 * inlining isn't turned on, or if a resource is skipped from
                 * being inlined for whatever reason above.
                 */
                if ( ! $inlined && ( ! empty( $this->cdn_url ) || has_filter( 'autoptimize_filter_base_replace_cdn' ) ) ) {
                    // Just do the "simple" CDN replacement.
                    $replacement_url                             = $this->url_replace_cdn( $url );
                    $imgreplace[ $url_src_matches[1][ $count ] ] = str_replace(
                        $original_url,
                        $replacement_url,
                        $url_src_matches[1][ $count ]
                    );
                }
            }
        }

        $code = self::replace_longest_matches_first( $code, $imgreplace );

        // Replace back font-face markers with actual font-face declarations.
        $code = $this->restore_fontface( $code );

        return $code;
    }

    /**
     * Joins and optimizes CSS.
     */
    public function minify()
    {
        foreach ( $this->css as $group ) {
            list( $media, $css ) = $group;
            if ( preg_match( '#^INLINE;#', $css ) ) {
                // <style>.
                $css      = preg_replace( '#^INLINE;#', '', $css );
                $css      = self::fixurls( ABSPATH . 'index.php', $css ); // ABSPATH already contains a trailing slash.
                $tmpstyle = apply_filters( 'autoptimize_css_individual_style', $css, '' );
                if ( has_filter( 'autoptimize_css_individual_style' ) && ! empty( $tmpstyle ) ) {
                    $css                   = $tmpstyle;
                    $this->alreadyminified = true;
                }
            } else {
                // <link>
                if ( false !== $css && file_exists( $css ) && is_readable( $css ) ) {
                    $css_path = $css;
                    $css      = self::fixurls( $css_path, file_get_contents( $css_path ) );
                    $css      = preg_replace( '/\x{EF}\x{BB}\x{BF}/', '', $css );
                    $tmpstyle = apply_filters( 'autoptimize_css_individual_style', $css, $css_path );
                    if ( has_filter( 'autoptimize_css_individual_style' ) && ! empty( $tmpstyle ) ) {
                        $css                   = $tmpstyle;
                        $this->alreadyminified = true;
                    } elseif ( $this->can_inject_late( $css_path, $css ) ) {
                        $css = self::build_injectlater_marker( $css_path, md5( $css ) );
                    }
                } else {
                    // Couldn't read CSS. Maybe getpath isn't working?
                    $css = '';
                }
            }

            foreach ( $media as $elem ) {
                if ( ! empty( $css ) ) {
                    if ( ! isset( $this->csscode[ $elem ] ) ) {
                        $this->csscode[ $elem ] = '';
                    }
                    $this->csscode[ $elem ] .= "\n/*FILESTART*/" . $css;
                }
            }
        }

        // Check for duplicate code.
        $md5list = array();
        $tmpcss  = $this->csscode;
        foreach ( $tmpcss as $media => $code ) {
            $md5sum    = md5( $code );
            $medianame = $media;
            foreach ( $md5list as $med => $sum ) {
                // If same code.
                if ( $sum === $md5sum ) {
                    // Add the merged code.
                    $medianame                   = $med . ', ' . $media;
                    $this->csscode[ $medianame ] = $code;
                    $md5list[ $medianame ]       = $md5list[ $med ];
                    unset( $this->csscode[ $med ], $this->csscode[ $media ], $md5list[ $med ] );
                }
            }
            $md5list[ $medianame ] = $md5sum;
        }
        unset( $tmpcss );

        // Manage @imports, while is for recursive import management.
        foreach ( $this->csscode as &$thiscss ) {
            // Flag to trigger import reconstitution and var to hold external imports.
            $fiximports       = false;
            $external_imports = '';

            // remove comments to avoid importing commented-out imports.
            $thiscss_nocomments = preg_replace( '#/\*.*\*/#Us', '', $thiscss );
            while ( preg_match_all( '#@import +(?:url)?(?:(?:\((["\']?)(?:[^"\')]+)\1\)|(["\'])(?:[^"\']+)\2)(?:[^,;"\']+(?:,[^,;"\']+)*)?)(?:;)#mi', $thiscss_nocomments, $matches ) ) {
                foreach ( $matches[0] as $import ) {
                    if ( $this->isremovable( $import, $this->cssremovables ) ) {
                        $thiscss   = str_replace( $import, '', $thiscss );
                        $import_ok = true;
                    } else {
                        $url       = trim( preg_replace( '#^.*((?:https?:|ftp:)?//.*\.css).*$#', '$1', trim( $import ) ), " \t\n\r\0\x0B\"'" );
                        $path      = $this->getpath( $url );
                        $import_ok = false;
                        if ( file_exists( $path ) && is_readable( $path ) ) {
                            $code     = addcslashes( self::fixurls( $path, file_get_contents( $path ) ), '\\' );
                            $code     = preg_replace( '/\x{EF}\x{BB}\x{BF}/', '', $code );
                            $tmpstyle = apply_filters( 'autoptimize_css_individual_style', $code, '' );
                            if ( has_filter( 'autoptimize_css_individual_style' ) && ! empty( $tmpstyle ) ) {
                                $code                  = $tmpstyle;
                                $this->alreadyminified = true;
                            } elseif ( $this->can_inject_late( $path, $code ) ) {
                                $code = self::build_injectlater_marker( $path, md5( $code ) );
                            }

                            if ( ! empty( $code ) ) {
                                $tmp_thiscss = str_replace( $import, stripcslashes( $code ), $thiscss );
                                if ( ! empty( $tmp_thiscss ) ) {
                                    $thiscss   = $tmp_thiscss;
                                    $import_ok = true;
                                    unset( $tmp_thiscss );
                                }
                            }
                            unset( $code );
                        }
                    }
                    if ( ! $import_ok ) {
                        // External imports and general fall-back.
                        $external_imports .= $import;

                        $thiscss    = str_replace( $import, '', $thiscss );
                        $fiximports = true;
                    }
                }
                $thiscss = preg_replace( '#/\*FILESTART\*/#', '', $thiscss );
                $thiscss = preg_replace( '#/\*FILESTART2\*/#', '/*FILESTART*/', $thiscss );

                // and update $thiscss_nocomments before going into next iteration in while loop.
                $thiscss_nocomments = preg_replace( '#/\*.*\*/#Us', '', $thiscss );
            }
            unset( $thiscss_nocomments );

            // Add external imports to top of aggregated CSS.
            if ( $fiximports ) {
                $thiscss = $external_imports . $thiscss;
            }
        }
        unset( $thiscss );

        // $this->csscode has all the uncompressed code now.
        foreach ( $this->csscode as &$code ) {
            // Check for already-minified code.
            $hash = md5( $code );
            do_action( 'autoptimize_action_css_hash', $hash );
            $ccheck = new autoptimizeCache( $hash, 'css' );
            if ( $ccheck->check() ) {
                $code                          = $ccheck->retrieve();
                $this->hashmap[ md5( $code ) ] = $hash;
                continue;
            }
            unset( $ccheck );

            // Rewrite and/or inline referenced assets.
            $code = $this->rewrite_assets( $code );

            // Minify.
            $code = $this->run_minifier_on( $code );

            // Bring back INJECTLATER stuff.
            $code = $this->inject_minified( $code );

            // Filter results.
            $tmp_code = apply_filters( 'autoptimize_css_after_minify', $code );
            if ( ! empty( $tmp_code ) ) {
                $code = $tmp_code;
                unset( $tmp_code );
            }

            $this->hashmap[ md5( $code ) ] = $hash;
        }

        unset( $code );
        return true;
    }

    public function run_minifier_on( $code )
    {
        if ( ! $this->alreadyminified ) {
            $do_minify = apply_filters( 'autoptimize_css_do_minify', true );

            if ( $do_minify ) {
                $cssmin   = new autoptimizeCSSmin();
                $tmp_code = trim( $cssmin->run( $code ) );

                if ( ! empty( $tmp_code ) ) {
                    $code = $tmp_code;
                    unset( $tmp_code );
                }
            }
        }

        return $code;
    }

    /**
     * Caches the CSS in uncompressed, deflated and gzipped form.
     */
    public function cache()
    {
        // CSS cache.
        foreach ( $this->csscode as $media => $code ) {
            if ( empty( $code ) ) {
                continue;
            }

            $md5   = $this->hashmap[ md5( $code ) ];
            $cache = new autoptimizeCache( $md5, 'css' );
            if ( ! $cache->check() ) {
                // Cache our code.
                $cache->cache( $code, 'text/css' );
            }
            $this->url[ $media ] = AUTOPTIMIZE_CACHE_URL . $cache->getname();
        }
    }

    /**
     * Returns the content.
     */
    public function getcontent()
    {
        // Restore the full content (only applies when "autoptimize_filter_css_justhead" filter is true).
        if ( ! empty( $this->restofcontent ) ) {
            $this->content      .= $this->restofcontent;
            $this->restofcontent = '';
        }

        // type is not added by default.
        $type_css = '';
        if ( apply_filters( 'autoptimize_filter_cssjs_addtype', false ) ) {
            $type_css = 'type="text/css" ';
        }

        // Inject the new stylesheets, if possible after SEO stuff, but we need to
        // already restore script to be able to inject before ld+json instead of title
        // this should be safe here as all has been extracted  already but behind a filter anyway.
        if ( $this->inline && true === apply_filters( 'autoptimize_filter_css_restore_js_early', true ) ) {
            $this->content = $this->restore_marked_content( 'SCRIPT', $this->content );
        }
        $_strpos_ldjson = strpos( $this->content, '<script type="application/ld+json"' );
        if ( false !== $_strpos_ldjson && $_strpos_ldjson < strpos( $this->content, '</head' ) ) {
            $replace_tag = array( '<script type="application/ld+json"', 'before' );            
        } else {
            $replace_tag = array( '<title', 'before' );
        }
        $replace_tag = apply_filters( 'autoptimize_filter_css_replacetag', $replace_tag, $this->content );

        if ( $this->inline ) {
            foreach ( $this->csscode as $media => $code ) {
                $this->inject_in_html( apply_filters( 'autoptimize_filter_css_bodyreplacementpayload', '<style ' . $type_css . 'media="' . $media . '">' . $code . '</style>' ), $replace_tag );
            }
        } else {
            if ( $this->defer ) {
                $preload_css_block  = '';
                $inlined_ccss_block = '';
                $noscript_css_block = '<noscript id="aonoscrcss">';

                $defer_inline_code = $this->defer_inline;
                if ( ! empty( $defer_inline_code ) ) {
                    if ( apply_filters( 'autoptimize_filter_css_critcss_minify', true ) ) {
                        $icss_hash  = md5( $defer_inline_code );
                        $icss_cache = new autoptimizeCache( $icss_hash, 'css' );
                        if ( $icss_cache->check() ) {
                            // we have the optimized inline CSS in cache.
                            $defer_inline_code = $icss_cache->retrieve();
                        } else {
                            $cssmin   = new autoptimizeCSSmin();
                            $tmp_code = trim( $cssmin->run( $defer_inline_code ) );

                            if ( ! empty( $tmp_code ) ) {
                                $defer_inline_code = $tmp_code;
                                $icss_cache->cache( $defer_inline_code, 'text/css' );
                                unset( $tmp_code );
                            }
                        }
                    }
                    // inlined critical css set here, but injected when full CSS is injected
                    // to avoid CSS containing SVG with <title tag receiving the full CSS link.
                    $inlined_ccss_block = '<style ' . $type_css . 'id="aoatfcss" media="all">' . $defer_inline_code . '</style>';
                }
            }

            foreach ( $this->url as $media => $url ) {
                $url = $this->url_replace_cdn( $url );

                // Add the stylesheet either deferred (import at bottom) or normal links in head.
                if ( $this->defer && 'print' !== $media ) {
                    $preload_onload = autoptimizeConfig::get_ao_css_preload_onload( $media );

                    $preload_css_block .= apply_filters( 'autoptimize_filter_css_single_deferred_link', '<link rel="stylesheet" media="print" href="' . $url . '" onload="' . $preload_onload . '">' );
                    if ( apply_filters( 'autoptimize_fitler_css_preload_and_print', false ) ) {
                        $preload_css_block = '<link rel="preload" as="stylesheet" href="' . $url . '"/>' . $preload_css_block;
                    }
                    $noscript_css_block .= '<link ' . $type_css . 'media="' . $media . '" href="' . $url . '" rel="stylesheet">';
                } else {
                    if ( strlen( $this->csscode[ $media ] ) > $this->cssinlinesize ) {
                        $this->inject_in_html( apply_filters( 'autoptimize_filter_css_bodyreplacementpayload', '<link ' . $type_css . 'media="' . $media . '" href="' . $url . '" rel="stylesheet">' ), $replace_tag );
                    } elseif ( strlen( $this->csscode[ $media ] ) > 0 ) {
                        $this->inject_in_html( apply_filters( 'autoptimize_filter_css_bodyreplacementpayload', '<style ' . $type_css . 'media="' . $media . '">' . $this->csscode[ $media ] . '</style>' ), $replace_tag );
                    }
                }
            }

            if ( $this->defer ) {
                $noscript_css_block .= '</noscript>';
                // Inject inline critical CSS, the preloaded full CSS and the noscript-CSS.
                $this->inject_in_html( apply_filters( 'autoptimize_filter_css_bodyreplacementpayload', $inlined_ccss_block . $preload_css_block . $noscript_css_block ), $replace_tag );
            }
        }

        // restore comments.
        $this->content = $this->restore_comments( $this->content );

        // restore IE hacks.
        $this->content = $this->restore_iehacks( $this->content );

        // restore (no)script.
        $this->content = $this->restore_marked_content( 'SCRIPT', $this->content );

        // Restore noptimize.
        $this->content = $this->restore_noptimize( $this->content );

        // Return the modified stylesheet.
        return $this->content;
    }

    /**
     * Make sure URL's are absolute iso relative to original CSS location.
     *
     * @param string $file filename of optimized CSS-file.
     * @param string $code CSS-code in which to fix URL's.
     */
    static function fixurls( $file, $code )
    {
        // Switch all imports to the url() syntax.
        $code = preg_replace( '#@import ("|\')(.+?)\.css.*?("|\')#', '@import url("${2}.css")', $code );

        if ( preg_match_all( self::ASSETS_REGEX, $code, $matches ) ) {
            $file = str_replace( WP_ROOT_DIR, '/', $file );
            /**
             * Rollback as per https://github.com/futtta/autoptimize/issues/94
             * $file = str_replace( AUTOPTIMIZE_WP_CONTENT_NAME, '', $file );
             */
            $dir = dirname( $file ); // Like /themes/expound/css.

            /**
             * $dir should not contain backslashes, since it's used to replace
             * urls, but it can contain them when running on Windows because
             * fixurls() is sometimes called with `ABSPATH . 'index.php'`
             */
            $dir = str_replace( '\\', '/', $dir );
            unset( $file ); // not used below at all.

            $replace = array();
            foreach ( $matches[1] as $k => $url ) {
                // Remove quotes.
                $url      = trim( $url, " \t\n\r\0\x0B\"'" );
                $no_q_url = trim( $url, "\"'" );
                if ( $url !== $no_q_url ) {
                    $removed_quotes = true;
                } else {
                    $removed_quotes = false;
                }

                if ( '' === $no_q_url ) {
                    continue;
                }

                $url = $no_q_url;
                if ( '/' === $url[0] || preg_match( '#^(https?://|ftp://|data:)#i', $url ) ) {
                    // URL is protocol-relative, host-relative or something we don't touch.
                    continue;
                } else { // Relative URL.

                    /*
                     * rollback as per https://github.com/futtta/autoptimize/issues/94
                     * $newurl = preg_replace( '/https?:/', '', str_replace( ' ', '%20', AUTOPTIMIZE_WP_CONTENT_URL . str_replace( '//', '/', $dir . '/' . $url ) ) );
                     */
                    $newurl = preg_replace( '/https?:/', '', str_replace( ' ', '%20', AUTOPTIMIZE_WP_ROOT_URL . str_replace( '//', '/', $dir . '/' . $url ) ) );
                    $newurl = apply_filters( 'autoptimize_filter_css_fixurl_newurl', $newurl );

                    /**
                     * Hash the url + whatever was behind potentially for replacement
                     * We must do this, or different css classes referencing the same bg image (but
                     * different parts of it, say, in sprites and such) loose their stuff...
                     */
                    $hash = md5( $url . $matches[2][ $k ] );
                    $code = str_replace( $matches[0][ $k ], $hash, $code );

                    if ( $removed_quotes ) {
                        $replace[ $hash ] = "url('" . $newurl . "')" . $matches[2][ $k ];
                    } else {
                        $replace[ $hash ] = 'url(' . $newurl . ')' . $matches[2][ $k ];
                    }
                }
            }

            $code = self::replace_longest_matches_first( $code, $replace );
        }

        return $code;
    }

    private function ismovable( $tag )
    {
        if ( ! $this->aggregate ) {
            return false;
        }

        if ( ! empty( $this->allowlist ) ) {
            foreach ( $this->allowlist as $match ) {
                if ( false !== strpos( $tag, $match ) ) {
                    return true;
                }
            }
            // no match with allowlist.
            return false;
        } else {
            if ( is_array( $this->dontmove ) && ! empty( $this->dontmove ) ) {
                foreach ( $this->dontmove as $match ) {
                    if ( false !== strpos( $tag, $match ) ) {
                        // Matched something.
                        return false;
                    }
                }
            }

            // If we're here it's safe to move.
            return true;
        }
    }

    private function can_inject_late( $css_path, $css )
    {
        $consider_minified_array = apply_filters( 'autoptimize_filter_css_consider_minified', false, $css_path );
        if ( true !== $this->inject_min_late ) {
            // late-inject turned off.
            return false;
        } elseif ( ( false === strpos( $css_path, 'min.css' ) ) && ( str_replace( $consider_minified_array, '', $css_path ) === $css_path ) ) {
            // file not minified based on filename & filter.
            return false;
        } elseif ( false !== strpos( $css, '@import' ) ) {
            // can't late-inject files with imports as those need to be aggregated.
            return false;
        } elseif ( ( false !== strpos( $css, '@font-face' ) ) && ( apply_filters( 'autoptimize_filter_css_fonts_cdn', false ) === true ) && ( ! empty( $this->cdn_url ) ) ) {
            // don't late-inject CSS with font-src's if fonts are set to be CDN'ed.
            return false;
        } elseif ( ( ( true == $this->datauris ) || ( ! empty( $this->cdn_url ) ) ) && preg_match( '#background[^;}]*url\(#Ui', $css ) ) {
            // don't late-inject CSS with images if CDN is set OR if image inlining is on.
            return false;
        } else {
            // phew, all is safe, we can late-inject.
            return true;
        }
    }

    /**
     * Minifies (and cdn-replaces) a single local css file
     * and returns its (cached) url.
     *
     * @param string $filepath Filepath.
     * @param bool   $cache_miss Optional. Force a cache miss. Default false.
     *
     * @return bool|string Url pointing to the minified css file or false.
     */
    public function minify_single( $filepath, $cache_miss = false )
    {
        $contents = $this->prepare_minify_single( $filepath );

        if ( empty( $contents ) ) {
            // if aggregate is off and CCSS is used but all files are minified already, then we
            // must make sure the autoptimize_action_css_hash action still fires for CCSS's sake.
            $ao_ccss_key = get_option( 'autoptimize_ccss_key', '' );
            if ( false === $this->aggregate && isset( $ao_ccss_key ) && ! empty( $ao_ccss_key ) ) {
                $hash = 'single_' . md5( file_get_contents( $filepath ) );
                do_action( 'autoptimize_action_css_hash', $hash );
            }
            return false;
        }

        // Check cache.
        $hash  = 'single_' . md5( $contents );
        $cache = new autoptimizeCache( $hash, 'css' );
        do_action( 'autoptimize_action_css_hash', $hash );

        // If not in cache already, minify...
        if ( ! $cache->check() || $cache_miss ) {
            // Fixurls...
            $contents = self::fixurls( $filepath, $contents );
            // CDN-replace any referenced assets if needed...
            $contents = $this->hide_fontface_and_maybe_cdn( $contents );
            $contents = $this->replace_urls( $contents );
            $contents = $this->restore_fontface( $contents );
            // Now minify...
            $cssmin   = new autoptimizeCSSmin();
            $contents = trim( $cssmin->run( $contents ) );

            // Check if minified cache content is empty.
            if ( empty( $contents ) ) {
                return false;
            }

            // Filter contents of excluded minified CSS.
            $contents = apply_filters( 'autoptimize_filter_css_single_after_minify', $contents );

            // Store in cache.
            $cache->cache( $contents, 'text/css' );
        }

        $url = $this->build_minify_single_url( $cache );

        return $url;
    }

    /**
     * Returns whether we're doing aggregation or not.
     *
     * @return bool
     */
    public function aggregating()
    {
        return $this->aggregate;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function replaceOptions( $options )
    {
        $this->options = $options;
    }

    public function setOption( $name, $value )
    {
        $this->options[ $name ] = $value;
        $this->$name            = $value;
    }

    public function getOption( $name )
    {
        return $this->options[ $name ];
    }

    /**
     * Sanitize user-provided CSS.
     *
     * For now just strip_tags (the WordPress way) and preg_replace to escape < in certain cases but might do full CSS escaping in the future, see:
     * https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html#rule-4-css-encode-and-strictly-validate-before-inserting-untrusted-data-into-html-style-property-values
     * https://github.com/twigphp/Twig/blob/3.x/src/Extension/EscaperExtension.php#L300-L319
     * https://github.com/laminas/laminas-escaper/blob/2.8.x/src/Escaper.php#L205-L221
     *
     * @param string $css the to be sanitized CSS.
     * @return string sanitized CSS.
     */
    public static function sanitize_css( $css )
    {
        $css = wp_strip_all_tags( $css );
        if ( strpos( $css, '<' ) !== false ) {
            $css = preg_replace( '#<(\/?\w+)#', '\00003C$1', $css );
        }
        return $css;
    }
}
