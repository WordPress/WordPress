<?php
/**
 * Autoptimize SpeedUp; minify & cache each JS/ CSS separately
 * new in Autoptimize 2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeSpeedupper
{
    public function __construct()
    {
        $this->add_hooks();
    }

    public function add_hooks()
    {
        if ( apply_filters( 'autoptimize_js_do_minify', true ) ) {
            add_filter( 'autoptimize_js_individual_script', array( $this, 'js_snippetcacher' ), 10, 2 );
            add_filter( 'autoptimize_js_after_minify', array( $this, 'js_cleanup' ), 10, 1 );
        }
        if ( apply_filters( 'autoptimize_css_do_minify', true ) ) {
            add_filter( 'autoptimize_css_individual_style', array( $this, 'css_snippetcacher' ), 10, 2 );
            add_filter( 'autoptimize_css_after_minify', array( $this, 'css_cleanup' ), 10, 1 );
        }
    }

    public function js_snippetcacher( $jsin, $jsfilename )
    {
        $md5hash = 'snippet_' . md5( $jsin );
        $ccheck  = new autoptimizeCache( $md5hash, 'js' );
        if ( $ccheck->check() ) {
            $scriptsrc = $ccheck->retrieve();
        } else {
            if ( false === ( strpos( $jsfilename, 'min.js' ) ) && ( str_replace( apply_filters( 'autoptimize_filter_js_consider_minified', false ), '', $jsfilename ) === $jsfilename ) ) {
                $tmp_jscode = trim( JSMin::minify( $jsin ) );
                if ( ! empty( $tmp_jscode ) ) {
                    $scriptsrc = $tmp_jscode;
                    unset( $tmp_jscode );
                } else {
                    $scriptsrc = $jsin;
                }
            } else {
                // Removing comments, linebreaks and stuff!
                $scriptsrc = preg_replace( '#^\s*\/\/.*$#Um', '', $jsin );
                $scriptsrc = preg_replace( '#^\s*\/\*[^!].*\*\/\s?#Us', '', $scriptsrc );
                $scriptsrc = preg_replace( "#(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#", "\n", $scriptsrc );
            }

            $last_char = substr( $scriptsrc, -1, 1 );
            if ( ';' !== $last_char && '}' !== $last_char ) {
                $scriptsrc .= ';';
            }

            if ( ! empty( $jsfilename ) && str_replace( apply_filters( 'autoptimize_filter_js_speedup_cache', false ), '', $jsfilename ) === $jsfilename ) {
                // Don't cache inline CSS or if filter says no!
                $ccheck->cache( $scriptsrc, 'text/javascript' );
            }
        }
        unset( $ccheck );

        return $scriptsrc;
    }

    public function css_snippetcacher( $cssin, $cssfilename )
    {
        $md5hash = 'snippet_' . md5( $cssin );
        $ccheck  = new autoptimizeCache( $md5hash, 'css' );
        if ( $ccheck->check() ) {
            $stylesrc = $ccheck->retrieve();
        } else {
            if ( ( false === strpos( $cssfilename, 'min.css' ) ) && ( str_replace( apply_filters( 'autoptimize_filter_css_consider_minified', false ), '', $cssfilename ) === $cssfilename ) ) {
                $cssmin   = new autoptimizeCSSmin();
                $tmp_code = trim( $cssmin->run( $cssin ) );

                if ( ! empty( $tmp_code ) ) {
                    $stylesrc = $tmp_code;
                    unset( $tmp_code );
                } else {
                    $stylesrc = $cssin;
                }
            } else {
                // .min.css -> no heavy-lifting, just some cleanup!
                $stylesrc = preg_replace( '#^\s*\/\*[^!].*\*\/\s?#Us', '', $cssin );
                $stylesrc = preg_replace( "#(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#", "\n", $stylesrc );
                $stylesrc = autoptimizeStyles::fixurls( $cssfilename, $stylesrc );
            }
            if ( ! empty( $cssfilename ) && ( str_replace( apply_filters( 'autoptimize_filter_css_speedup_cache', false ), '', $cssfilename ) === $cssfilename ) ) {
                // Only caching CSS if it's not inline and is allowed by filter!
                $ccheck->cache( $stylesrc, 'text/css' );
            }
        }
        unset( $ccheck );

        return $stylesrc;
    }

    public function css_cleanup( $cssin )
    {
        // Speedupper results in aggregated CSS not being minified, so the filestart-marker AO adds when aggregating needs to be removed.
        return trim( str_replace( array( '/*FILESTART*/', '/*FILESTART2*/' ), '', $cssin ) );
    }

    public function js_cleanup( $jsin )
    {
        return trim( $jsin );
    }
}
