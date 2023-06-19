<?php
/**
 * Handles minifying HTML markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeHTML extends autoptimizeBase
{
    /**
     * Whether HTML comments are kept.
     *
     * @var bool
     */
    private $keepcomments = false;

    /**
     * Whether inline CSS/ JS is minified.
     *
     * @var bool
     */
    private $minify_inline = false;

    /**
     * Whether to force xhtml compatibility.
     *
     * @var bool
     */
    private $forcexhtml = false;

    /**
     * Things to exclude from being minifed.
     *
     * @var array
     */
    private $exclude = array(
        '<!-- ngg_resource_manager_marker -->',
        '<!--noindex-->',
        '<!--/noindex-->',
    );

    public function read( $options )
    {
        // Remove the HTML comments?
        $this->keepcomments = (bool) $options['keepcomments'];

        // Filter to force xhtml.
        $this->forcexhtml = (bool) apply_filters( 'autoptimize_filter_html_forcexhtml', false );

        // minify inline JS/ CSS.
        $this->minify_inline = (bool) apply_filters( 'autoptimize_html_minify_inline_js_css', $options['minify_inline'] );

        // Filterable strings to be excluded from HTML minification.
        $exclude = apply_filters( 'autoptimize_filter_html_exclude', '' );
        if ( '' !== $exclude ) {
            $exclude_arr   = array_filter( array_map( 'trim', explode( ',', $exclude ) ) );
            $this->exclude = array_merge( $exclude_arr, $this->exclude );
        }

        // Nothing else for HTML!
        return true;
    }

    /**
     * Minifies HTML.
     *
     * @return bool
     */
    public function minify()
    {
        $noptimize = apply_filters( 'autoptimize_filter_html_noptimize', false, $this->content );
        if ( $noptimize ) {
            return false;
        }

        // Wrap the to-be-excluded strings in noptimize tags.
        foreach ( $this->exclude as $str ) {
            if ( false !== strpos( $this->content, $str ) ) {
                $replacement   = '<!--noptimize-->' . $str . '<!--/noptimize-->';
                $this->content = str_replace( $str, $replacement, $this->content );
            }
        }

        // Noptimize.
        $this->content = $this->hide_noptimize( $this->content );

        // Preparing options for Minify_HTML.
        $options = array( 'keepComments' => $this->keepcomments );
        if ( $this->forcexhtml ) {
            $options['xhtml'] = true;
        }

        // Optionally minify inline JS & CSS.
        if ( $this->minify_inline ) {
            if ( false != autoptimizeOptionWrapper::get_option( 'autoptimize_js' ) && false != autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_js_optimize' ) && true !== apply_filters( 'autoptimize_filter_js_noptimize', false, $this->content ) && false === strpos( $this->content, 'text/template' ) ) {
                $options['jsMinifier']  = 'JSMin::minify';
            }
            if ( false != autoptimizeOptionWrapper::get_option( 'autoptimize_css' ) && false != autoptimizeConfig::get_post_meta_ao_settings( 'ao_post_css_optimize' ) && true !== apply_filters( 'autoptimize_filter_css_noptimize', false, $this->content ) ) {
                $options['cssMinifier'] = 'autoptimizeCSSmin::minify';
            }
        }

        $tmp_content = AO_Minify_HTML::minify( $this->content, $options );
        if ( ! empty( $tmp_content ) ) {
            $this->content = $tmp_content;
            unset( $tmp_content );
        }

        // Restore noptimize.
        $this->content = $this->restore_noptimize( $this->content );

        // Remove the noptimize-wrapper from around the excluded strings.
        foreach ( $this->exclude as $str ) {
            $replacement = '<!--noptimize-->' . $str . '<!--/noptimize-->';
            if ( false !== strpos( $this->content, $replacement ) ) {
                $this->content = str_replace( $replacement, $str, $this->content );
            }
        }

        // Revslider data attribs somehow suffer from HTML optimization, this fixes that!
        if ( class_exists( 'RevSlider' ) && apply_filters( 'autoptimize_filter_html_dataattrib_cleanup', false ) ) {
            $this->content = preg_replace( '#\n(data-.*$)\n#Um', ' $1 ', $this->content );
            $this->content = preg_replace( '#<[^>]*(=\"[^"\'<>\s]*\")(\w)#', '$1 $2', $this->content );
        }

        return true;
    }

    /**
     * Doesn't do much in case of HTML (no cache in css/js sense there)
     *
     * @return true
     */
    public function cache()
    {
        return true;
    }

    /**
     * Returns the HTML markup.
     *
     * @return string
     */
    public function getcontent()
    {
        return $this->content;
    }
}
