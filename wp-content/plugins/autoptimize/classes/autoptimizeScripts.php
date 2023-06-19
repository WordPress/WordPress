<?php
/**
 * Class for JS optimization.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeScripts extends autoptimizeBase
{

    /**
     * Stores founds scripts.
     *
     * @var array
     */
    private $scripts = array();

    /**
     * Stores to be moved JS.
     *
     * @var array
     */
    private $move = array(
        'first' => array(),
        'last'  => array(),
    );

    /**
     * List of not to be moved JS.
     *
     * @var array
     */
    private $dontmove = array(
        'document.write',
        'html5.js',
        'show_ads.js',
        'google_ad',
        'histats.com/js',
        'statcounter.com/counter/counter.js',
        'ws.amazon.com/widgets',
        'media.fastclick.net',
        '/ads/',
        'comment-form-quicktags/quicktags.php',
        'edToolbar',
        'intensedebate.com',
        'scripts.chitika.net/',
        '_gaq.push',
        'jotform.com/',
        'admin-bar.min.js',
        'GoogleAnalyticsObject',
        'plupload.full.min.js',
        'syntaxhighlighter',
        'adsbygoogle',
        'gist.github.com',
        '_stq',
        'nonce',
        'post_id',
        'data-noptimize',
        'data-cfasync',
        'data-pagespeed-no-defer',
        'logHuman',
        'amp-mobile-version-switcher',
        'data-rocketlazyloadscript',
        'rocket-browser-checker-js-after',
    );

    /**
     * List of to be moved JS.
     *
     * @var array
     */
    private $domove = array(
        'gaJsHost',
        'load_cmc',
        'jd.gallery.transitions.js',
        'swfobject.embedSWF(',
        'tiny_mce.js',
        'tinyMCEPreInit.go',
    );

    /**
     * List of JS that can be moved last (not used any more).
     *
     * @var array
     */
    private $domovelast = array(
        'addthis.com',
        '/afsonline/show_afs_search.js',
        'disqus.js',
        'networkedblogs.com/getnetworkwidget',
        'infolinks.com/js/',
        'jd.gallery.js.php',
        'jd.gallery.transitions.js',
        'swfobject.embedSWF(',
        'linkwithin.com/widget.js',
        'tiny_mce.js',
        'tinyMCEPreInit.go',
    );

    /**
     * Setting CDN base URL.
     *
     * @var string
     */
    public $cdn_url = '';

    /**
     * Setting; aggregate or not.
     *
     * @var bool
     */
    private $aggregate = true;

    /**
     * Setting; if not aggregated, should we defer?
     *
     * @var bool
     */
    private $defer_not_aggregate = false;

    /**
     * Setting; defer inline JS?
     *
     * @var bool
     */
    private $defer_inline = false;

    /**
     * Setting; try/catch wrapping or not.
     *
     * @var bool
     */
    private $trycatch = false;

    /**
     * State; is JS already minified.
     *
     * @var bool
     */
    private $alreadyminified = false;

    /**
     * Setting; force JS in head or not.
     *
     * @var bool
     */
    private $forcehead = true;

    /**
     * Setting; aggregate inline JS or not.
     *
     * @var bool
     */
    private $include_inline = false;

    /**
     * State; holds JS code.
     *
     * @var string
     */
    private $jscode = '';

    /**
     * State; holds URL of JS-file.
     *
     * @var string
     */
    private $url = '';

    /**
     * State; stores rest of HTML if (old) option "only in head" is on.
     *
     * @var string
     */
    private $restofcontent = '';

    /**
     * State; holds md5-hash.
     *
     * @var string
     */
    private $md5hash = '';

    /**
     * Setting (filter); allowlist of to be aggregated JS.
     *
     * @var string
     */
    private $allowlist = '';

    /**
     * Setting (filter); holds JS that should be removed.
     *
     * @var array
     */
    private $jsremovables = array();

    /**
     * Setting (filter); can we inject already minified files after the
     * unminified aggregate JS has been minified.
     *
     * @var bool
     */
    private $inject_min_late = true;

    /**
     * Setting; should excluded JS be minified (if not already).
     *
     * @var bool
     */
    private $minify_excluded = true;

    /**
     * Reads the page and collects script tags.
     *
     * @param array $options all options.
     */
    public function read( $options )
    {
        $noptimize_js = false;

        // If page/ post check post_meta to see if optimize is off.
        if ( false === autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_js_optimize' ) ) {
            $noptimize_js = true;
        }

        // And a filter to enforce JS noptimize.
        $noptimize_js = apply_filters( 'autoptimize_filter_js_noptimize', $noptimize_js, $this->content );

        // And finally bail if noptimize_js is true.
        if ( $noptimize_js ) {
            return false;
        }

        // only optimize known good JS?
        $allowlist_js = apply_filters( 'autoptimize_filter_js_allowlist', '', $this->content );
        if ( ! empty( $allowlist_js ) ) {
            $this->allowlist = array_filter( array_map( 'trim', explode( ',', $allowlist_js ) ) );
        }

        // is there JS we should simply remove?
        $removable_js = apply_filters( 'autoptimize_filter_js_removables', '', $this->content );
        if ( ! empty( $removable_js ) ) {
            $this->jsremovables = array_filter( array_map( 'trim', explode( ',', $removable_js ) ) );
        }

        // only header?
        if ( apply_filters( 'autoptimize_filter_js_justhead', $options['justhead'] ) ) {
            $content             = explode( '</head>', $this->content, 2 );
            $this->content       = $content[0] . '</head>';
            $this->restofcontent = $content[1];
        }

        // Determine whether we're doing JS-files aggregation or not.
        if ( ! $options['aggregate'] ) {
            $this->aggregate = false;
        }
        // Returning true for "dontaggregate" turns off aggregation.
        if ( $this->aggregate && apply_filters( 'autoptimize_filter_js_dontaggregate', false ) ) {
            $this->aggregate = false;
        }
        // and the filter that should have been there to begin with.
        $this->aggregate = apply_filters( 'autoptimize_filter_js_aggregate', $this->aggregate );

        // Defer when not aggregating.
        if ( false === $this->aggregate && apply_filters( 'autoptimize_filter_js_defer_not_aggregate', $options['defer_not_aggregate'] ) ) {
            $this->defer_not_aggregate = true;
        }

        // Defer inline JS?
        if ( ( true === $this->defer_not_aggregate && apply_filters( 'autoptimize_js_filter_defer_inline', $options['defer_inline'] ) ) || apply_filters( 'autoptimize_js_filter_force_defer_inline', false ) ) {
            $this->defer_inline = true;
        }

        // include inline?
        if ( apply_filters( 'autoptimize_js_include_inline', $options['include_inline'] ) ) {
            $this->include_inline = true;
        }

        // filter to "late inject minified JS", default to true for now (it is faster).
        $this->inject_min_late = apply_filters( 'autoptimize_filter_js_inject_min_late', true );

        // filters to override hardcoded do(nt)move(last) array contents (array in, array out!).
        $this->dontmove   = apply_filters( 'autoptimize_filter_js_dontmove', $this->dontmove );
        $this->domovelast = apply_filters( 'autoptimize_filter_js_movelast', $this->domovelast );
        $this->domove     = apply_filters( 'autoptimize_filter_js_domove', $this->domove );

        // Determine whether excluded files should be minified if not yet so.
        if ( ! $options['minify_excluded'] && $options['aggregate'] ) {
            $this->minify_excluded = false;
        }
        $this->minify_excluded = apply_filters( 'autoptimize_filter_js_minify_excluded', $this->minify_excluded, '' );

        // get extra exclusions settings or filter.
        $exclude_js = $options['js_exclude'];
        $exclude_js = apply_filters( 'autoptimize_filter_js_exclude', $exclude_js, $this->content );

        if ( '' !== $exclude_js ) {
            if ( is_array( $exclude_js ) ) {
                $remove_keys = array_keys( $exclude_js, 'remove' );
                if ( false !== $remove_keys ) {
                    foreach ( $remove_keys as $remove_key ) {
                        unset( $exclude_js[ $remove_key ] );
                        $this->jsremovables[] = $remove_key;
                    }
                }
                $excl_js_arr = array_keys( $exclude_js );
            } else {
                $excl_js_arr = array_filter( array_map( 'trim', explode( ',', $exclude_js ) ) );
            }
            $this->dontmove = array_merge( $excl_js_arr, $this->dontmove );
        }

        // Should we add try-catch?
        if ( $options['trycatch'] ) {
            $this->trycatch = true;
        }

        // force js in head?
        if ( $options['forcehead'] ) {
            $this->forcehead = true;
        } else {
            $this->forcehead = false;
        }

        $this->forcehead = apply_filters( 'autoptimize_filter_js_forcehead', $this->forcehead );

        // get cdn url.
        $this->cdn_url = $options['cdn_url'];

        // noptimize me.
        $this->content = $this->hide_noptimize( $this->content );

        // Save IE hacks.
        $this->content = $this->hide_iehacks( $this->content );

        // comments.
        $this->content = $this->hide_comments( $this->content );

        // Get script files.
        if ( preg_match_all( '#<script.*</script>#Usmi', $this->content, $matches ) ) {
            foreach ( $matches[0] as $tag ) {
                // only consider script aggregation for types allowlisted in should_aggregate-function.
                $should_aggregate = $this->should_aggregate( $tag );
                if ( ! $should_aggregate ) {
                    $tag = '';
                    continue;
                }

                if ( preg_match( '#<script[^>]*src=("|\')([^>]*)("|\')#Usmi', $tag, $source ) ) {
                    // non-inline script.
                    if ( $this->isremovable( $tag, $this->jsremovables ) ) {
                        $this->content = str_replace( $tag, '', $this->content );
                        continue;
                    }

                    $orig_tag = null;
                    $url      = current( explode( '?', $source[2], 2 ) );
                    $path     = $this->getpath( $url );
                    if ( false !== $path && preg_match( '#\.js$#', $path ) && $this->ismergeable( $tag ) ) {
                        // ok to optimize, add to array.
                        $this->scripts[] = $path;
                    } else {
                        $orig_tag = $tag;
                        $new_tag  = $tag;

                        // non-mergeable script (excluded or dynamic or external).
                        if ( is_array( $exclude_js ) ) {
                            // should we add flags?
                            foreach ( $exclude_js as $excl_tag => $excl_flags ) {
                                if ( false !== strpos( $orig_tag, $excl_tag ) && in_array( $excl_flags, array( 'async', 'defer' ) ) ) {
                                    $new_tag = str_replace( '<script ', '<script ' . $excl_flags . ' ', $new_tag );
                                }
                            }
                        }

                        // not aggregating but deferring?
                        if ( $this->defer_not_aggregate && false === $this->aggregate && ( str_replace( $this->dontmove, '', $path ) === $path || ( apply_filters( 'autoptimize_filter_js_defer_external', true ) && str_replace( $this->dontmove, '', $orig_tag ) === $orig_tag ) ) && strpos( $new_tag, ' defer' ) === false && strpos( $new_tag, ' async' ) === false ) {
                            $new_tag = str_replace( '<script ', '<script defer ', $new_tag );
                        }

                        // Should we minify the non-aggregated script?
                        // -> if aggregate is on and exclude minify is on
                        // -> if aggregate is off and the file is not in dontmove.
                        if ( $path && $this->minify_excluded ) {
                            $consider_minified_array = apply_filters( 'autoptimize_filter_js_consider_minified', false );
                            if ( ( false === $this->aggregate && str_replace( $this->dontmove, '', $path ) === $path ) || ( true === $this->aggregate && ( false === $consider_minified_array || str_replace( $consider_minified_array, '', $path ) === $path ) ) ) {
                                $minified_url = $this->minify_single( $path );
                                if ( ! empty( $minified_url ) ) {
                                    // Replace original URL with minified URL from cache.
                                    $new_tag = str_replace( $url, $minified_url, $new_tag );
                                } elseif ( apply_filters( 'autoptimize_filter_ccsjs_remove_empty_minified_url', false ) ) {
                                    // Remove the original script tag, because cache content is empty but only if filter
                                    // is trued because $minified_url is also false if original JS is minified already.
                                    $new_tag = '';
                                }
                            }
                        }

                        // Check if we still need to CDN (esp. for already minified resources).
                        if ( ! empty( $this->cdn_url ) || has_filter( 'autoptimize_filter_base_replace_cdn' ) ) {
                            $new_tag = str_replace( $url, $this->url_replace_cdn( $url ), $new_tag );
                        }

                        if ( $this->ismovable( $new_tag ) ) {
                            // can be moved, flags and all.
                            if ( $this->movetolast( $new_tag ) ) {
                                $this->move['last'][] = $new_tag;
                            } else {
                                $this->move['first'][] = $new_tag;
                            }
                        } else {
                            // cannot be moved, so if flag was added re-inject altered tag immediately.
                            if ( ( '' !== $new_tag && $orig_tag !== $new_tag ) || ( '' === $new_tag && apply_filters( 'autoptimize_filter_js_remove_empty_files', false ) ) ) {
                                $this->content = str_replace( $orig_tag, $new_tag, $this->content );
                                $orig_tag      = '';
                            }
                            // and forget about the $tag (not to be touched any more).
                            $tag = '';
                        }
                    }
                } else {
                    // Inline script.
                    if ( $this->isremovable( $tag, $this->jsremovables ) ) {
                        $this->content = str_replace( $tag, '', $this->content );
                        continue;
                    }

                    // unhide comments, as javascript may be wrapped in comment-tags for old times' sake.
                    $tag = $this->restore_comments( $tag );
                    if ( $this->ismergeable( $tag ) && $this->include_inline ) {
                        preg_match( '#<script.*>(.*)</script>#Usmi', $tag, $code );
                        $code            = preg_replace( '#.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*#sm', '$1', $code[1] );
                        $code            = preg_replace( '/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $code );
                        $this->scripts[] = 'INLINE;' . $code;
                    } else {
                        $_inline_deferable = apply_filters( 'autoptimize_filters_js_inline_deferable', array( 'nonce', 'post_id', 'syntaxhighlighter' ) );
                        $_inline_dontmove  = array_values( array_diff( $this->dontmove, $_inline_deferable ) );
                        if ( false === $this->defer_inline ) {
                            // Can we move this?
                            $autoptimize_js_moveable = apply_filters( 'autoptimize_js_moveable', '', $tag );
                            if ( $this->ismovable( $tag ) || '' !== $autoptimize_js_moveable ) {
                                if ( $this->movetolast( $tag ) || 'last' === $autoptimize_js_moveable ) {
                                    $this->move['last'][] = $tag;
                                } else {
                                    $this->move['first'][] = $tag;
                                }
                            } else {
                                $tag = '';
                            }
                        } else if ( str_replace( $_inline_dontmove, '', $tag ) === $tag && strlen( $tag ) < apply_filters( 'autoptimize_filter_script_defer_inline_maxsize', 200000 ) ) {
                            // defer inline JS by base64 encoding it but only if string is not ridiculously huge (to avoid issues with below regex mainly).
                            preg_match( '#<script(?:[^>](?!id=))*\s*(?:id=(["\'])([^"\']+)\1)*+[^>]*+>(.*?)<\/script>#is', $tag, $match );
                            if ( $match[2] ) {
                                $_id = 'id="' . $match[2] . '" ';
                            } else {
                                $_id = '';
                            }

                            $new_tag       = '<script defer ' . $_id . 'src="data:text/javascript;base64,' . base64_encode( $match[3] ) . '"></script>';
                            $this->content = str_replace( $tag, $new_tag, $this->content );
                            $tag           = '';
                        } else {
                            $tag = '';
                        }
                    }
                    // Re-hide comments to be able to do the removal based on tag from $this->content.
                    $tag = $this->hide_comments( $tag );
                }

                // Remove the original script tag.
                $this->content = str_replace( $tag, '', $this->content );
            }

            return true;
        }

        // No script files, great ;-) .
        return false;
    }

    /**
     * Determines wheter a certain `<script>` $tag should be aggregated or not.
     *
     * We consider these as "aggregation-safe" currently:
     * - script tags without a `type` attribute
     * - script tags with these `type` attribute values: `text/javascript`, `text/ecmascript`, `application/javascript`,
     * and `application/ecmascript`
     *
     * Everything else should return false.
     *
     * @link https://developer.mozilla.org/en/docs/Web/HTML/Element/script#attr-type
     *
     * @param string $tag Script node & child(ren).
     * @return bool
     */
    public static function should_aggregate( $tag )
    {
        if ( empty( $tag ) ) {
            return false;
        }

        // We're only interested in the type attribute of the <script> tag itself, not any possible
        // inline code that might just contain the 'type=' string...
        $tag_parts = array();
        preg_match( '#<(script[^>]*)>#i', $tag, $tag_parts );
        $tag_without_contents = null;
        if ( ! empty( $tag_parts[1] ) ) {
            $tag_without_contents = $tag_parts[1];
        }

        $has_type = ( strpos( $tag_without_contents, 'type' ) !== false );

        $type_valid = false;
        if ( $has_type ) {
            $type_valid = (bool) preg_match( '/type\s*=\s*[\'"]?(?:text|application)\/(?:javascript|ecmascript)[\'"]?/i', $tag_without_contents );
        }

        $should_aggregate = false;
        if ( ! $has_type || $type_valid ) {
            $should_aggregate = true;
        }

        return $should_aggregate;
    }

    /**
     * Joins and optimizes JS.
     */
    public function minify()
    {
        foreach ( $this->scripts as $script ) {
            if ( empty( $script ) ) {
                continue;
            }

            // TODO/FIXME: some duplicate code here, can be reduced/simplified.
            if ( preg_match( '#^INLINE;#', $script ) ) {
                // Inline script.
                $script = preg_replace( '#^INLINE;#', '', $script );
                $script = rtrim( $script, ";\n\t\r" ) . ';';
                // Add try-catch?
                if ( $this->trycatch ) {
                    $script = 'try{' . $script . '}catch(e){}';
                }
                $tmpscript = apply_filters( 'autoptimize_js_individual_script', $script, '' );
                if ( has_filter( 'autoptimize_js_individual_script' ) && ! empty( $tmpscript ) ) {
                    $script                = $tmpscript;
                    $this->alreadyminified = true;
                }
                $this->jscode .= "\n" . $script;
            } else {
                // External script.
                if ( false !== $script && file_exists( $script ) && is_readable( $script ) ) {
                    $scriptsrc = file_get_contents( $script );
                    $scriptsrc = preg_replace( '/\x{EF}\x{BB}\x{BF}/', '', $scriptsrc );
                    $scriptsrc = rtrim( $scriptsrc, ";\n\t\r" ) . ';';
                    // Add try-catch?
                    if ( $this->trycatch ) {
                        $scriptsrc = 'try{' . $scriptsrc . '}catch(e){}';
                    }
                    $tmpscriptsrc = apply_filters( 'autoptimize_js_individual_script', $scriptsrc, $script );
                    if ( has_filter( 'autoptimize_js_individual_script' ) && ! empty( $tmpscriptsrc ) ) {
                        $scriptsrc             = $tmpscriptsrc;
                        $this->alreadyminified = true;
                    } elseif ( $this->can_inject_late( $script ) ) {
                        $scriptsrc = self::build_injectlater_marker( $script, md5( $scriptsrc ) );
                    }
                    $this->jscode .= "\n" . $scriptsrc;
                }
            }
        }

        // Check for already-minified code.
        $this->md5hash = md5( $this->jscode );
        $ccheck        = new autoptimizeCache( $this->md5hash, 'js' );
        if ( $ccheck->check() ) {
            $this->jscode = $ccheck->retrieve();
            return true;
        }
        unset( $ccheck );

        // $this->jscode has all the uncompressed code now.
        if ( true !== $this->alreadyminified ) {
            if ( apply_filters( 'autoptimize_js_do_minify', true ) ) {
                $tmp_jscode = trim( JSMin::minify( $this->jscode ) );
                if ( ! empty( $tmp_jscode ) ) {
                    $this->jscode = $tmp_jscode;
                    unset( $tmp_jscode );
                }
                $this->jscode = $this->inject_minified( $this->jscode );
                $this->jscode = apply_filters( 'autoptimize_js_after_minify', $this->jscode );
                return true;
            } else {
                $this->jscode = $this->inject_minified( $this->jscode );
                return false;
            }
        }

        $this->jscode = apply_filters( 'autoptimize_js_after_minify', $this->jscode );
        return true;
    }

    /**
     * Caches the JS in uncompressed, deflated and gzipped form.
     */
    public function cache()
    {
        $cache = new autoptimizeCache( $this->md5hash, 'js' );
        if ( ! $cache->check() ) {
            // Cache our code.
            $cache->cache( $this->jscode, 'text/javascript' );
        }
        $this->url = AUTOPTIMIZE_CACHE_URL . $cache->getname();
        $this->url = $this->url_replace_cdn( $this->url );
    }

    /**
     * Returns the content.
     */
    public function getcontent()
    {
        // Restore the full content.
        if ( ! empty( $this->restofcontent ) ) {
            $this->content      .= $this->restofcontent;
            $this->restofcontent = '';
        }

        // Add the scripts taking forcehead/ deferred (default) into account.
        if ( $this->forcehead ) {
            $replace_tag = array( '</head>', 'before' );
            $defer       = '';
        } else {
            $replace_tag = array( '</body>', 'before' );
            $defer       = 'defer ';
        }

        $defer   = apply_filters( 'autoptimize_filter_js_defer', $defer );
        $type_js = '';
        if ( apply_filters( 'autoptimize_filter_cssjs_addtype', false ) ) {
            $type_js = 'type="text/javascript" ';
        }

        $bodyreplacementpayload = '<script ' . $type_js . $defer . 'src="' . $this->url . '"></script>';
        $bodyreplacementpayload = apply_filters( 'autoptimize_filter_js_bodyreplacementpayload', $bodyreplacementpayload );

        $bodyreplacement  = implode( '', $this->move['first'] );
        $bodyreplacement .= $bodyreplacementpayload;
        $bodyreplacement .= implode( '', $this->move['last'] );

        $replace_tag = apply_filters( 'autoptimize_filter_js_replacetag', $replace_tag );

        if ( strlen( $this->jscode ) > 0 ) {
            $this->inject_in_html( $bodyreplacement, $replace_tag );
        }

        // Restore comments.
        $this->content = $this->restore_comments( $this->content );

        // Restore IE hacks.
        $this->content = $this->restore_iehacks( $this->content );

        // Restore noptimize.
        $this->content = $this->restore_noptimize( $this->content );

        // Return the modified HTML.
        return $this->content;
    }

    /**
     * Checks against the allow- and blocklists.
     *
     * @param string $tag JS tag.
     */
    private function ismergeable( $tag )
    {
        if ( empty( $tag ) || ! $this->aggregate ) {
            return false;
        }

        if ( ! empty( $this->allowlist ) ) {
            foreach ( $this->allowlist as $match ) {
                if ( false !== strpos( $tag, $match ) ) {
                    return true;
                }
            }
            // No match with allowlist.
            return false;
        } else {
            foreach ( $this->domove as $match ) {
                if ( false !== strpos( $tag, $match ) ) {
                    // Matched something.
                    return false;
                }
            }

            if ( $this->movetolast( $tag ) ) {
                return false;
            }

            foreach ( $this->dontmove as $match ) {
                if ( false !== strpos( $tag, $match ) ) {
                    // Matched something.
                    return false;
                }
            }

            // If we're here it's safe to merge.
            return true;
        }
    }

    /**
     * Checks agains the blocklist.
     *
     * @param string $tag tag to check for blocklist (exclusions).
     */
    private function ismovable( $tag )
    {
        if ( empty( $tag ) || true !== $this->include_inline || apply_filters( 'autoptimize_filter_js_unmovable', true ) ) {
            return false;
        }

        foreach ( $this->domove as $match ) {
            if ( false !== strpos( $tag, $match ) ) {
                // Matched something.
                return true;
            }
        }

        if ( $this->movetolast( $tag ) ) {
            return true;
        }

        foreach ( $this->dontmove as $match ) {
            if ( false !== strpos( $tag, $match ) ) {
                // Matched something.
                return false;
            }
        }

        // If we're here it's safe to move.
        return true;
    }

    private function movetolast( $tag )
    {
        if ( empty( $tag ) ) {
            return false;
        }

        foreach ( $this->domovelast as $match ) {
            if ( false !== strpos( $tag, $match ) ) {
                // Matched, return true.
                return true;
            }
        }

        // Should be in 'first'.
        return false;
    }

    /**
     * Determines wheter a <script> $tag can be excluded from minification (as already minified) based on:
     * - inject_min_late being active
     * - filename ending in `min.js`
     * - filename matching one passed in the consider minified filter
     *
     * @param string $js_path Path to JS file.
     * @return bool
     */
    private function can_inject_late( $js_path ) {
        $consider_minified_array = apply_filters( 'autoptimize_filter_js_consider_minified', false );
        if ( true !== $this->inject_min_late ) {
            // late-inject turned off.
            return false;
        } elseif ( ( false === strpos( $js_path, 'min.js' ) ) && ( str_replace( $consider_minified_array, '', $js_path ) === $js_path ) ) {
            // file not minified based on filename & filter.
            return false;
        } else {
            // phew, all is safe, we can late-inject.
            return true;
        }
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

    /**
     * Minifies a single local js file and returns its (cached) url.
     *
     * @param string $filepath Filepath.
     * @param bool   $cache_miss Optional. Force a cache miss. Default false.
     *
     * @return bool|string Url pointing to the minified js file or false.
     */
    public function minify_single( $filepath, $cache_miss = false )
    {
        $contents = $this->prepare_minify_single( $filepath );

        if ( empty( $contents ) ) {
            return false;
        }

        // Check cache.
        $hash  = 'single_' . md5( $contents );
        $cache = new autoptimizeCache( $hash, 'js' );

        // If not in cache already, minify...
        if ( ! $cache->check() || $cache_miss ) {
            $contents = trim( JSMin::minify( $contents ) );

            // Check if minified cache content is empty.
            if ( empty( $contents ) ) {
                return false;
            }

            // Filter contents of excluded minified CSS.
            $contents = apply_filters( 'autoptimize_filter_js_single_after_minify', $contents );

            // Store in cache.
            $cache->cache( $contents, 'text/javascript' );
        }

        $url = $this->build_minify_single_url( $cache );

        return $url;
    }
}
