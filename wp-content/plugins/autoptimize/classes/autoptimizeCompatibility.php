<?php
/**
 * Multiple compatibility snippets to ensure important/ stubborn plugins work out of the box.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCompatibility
{
    /**
     * Options.
     *
     * @var array
     */
    protected $conf = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        if ( ! is_admin() && ! defined( 'DOING_CRON' ) ) {
            $this->conf = autoptimizeConfig::instance();
            $this->run();
        }
    }

    /**
     * Runs multiple compatibility snippets to ensure important plugins work out of the box.
     */
    public function run()
    {
        // Edit with Elementor in frontend admin menu (so for editors/ administrators) needs JS opt. disabled to appear & function.
        if ( defined( 'ELEMENTOR_VERSION' ) && is_user_logged_in() && current_user_can( 'edit_posts' ) && apply_filters( 'autoptimize_filter_compatibility_editelementor_active', true ) ) {
            add_filter( 'autoptimize_filter_js_noptimize', '__return_true' );
        }

        // Revslider; jQuery should not be deferred + exclude all revslider JS.
        if ( defined( 'RS_REVISION' ) && $this->conf->get( 'autoptimize_js' ) && true == $this->inline_js_config_checker() && apply_filters( 'autoptimize_filter_compatibility_revslider_active', true ) ) {
            add_filter(
                'autoptimize_filter_js_exclude',
                function( $js_excl = '', $html = '' ) {
                    $revslider_excl = 'revslider, setREVStartSize, window.RSIW, window.RS_MODULES, jquery.min.js';
                    if ( ! empty( $html ) && false !== strpos( $html, '<rs-slides' ) ) {
                        if ( is_array( $js_excl ) ) {
                            $js_excl = implode( ',', $js_excl );
                        }

                        $js_excl .= ',' . $revslider_excl;
                    }
                    return $js_excl;
                },
                11,
                2
            );
        }

        // Revslider; remove revslider JS if no slides in HTML for non-logged in users.
        if ( defined( 'RS_REVISION' ) && $this->conf->get( 'autoptimize_js' ) && false === is_user_logged_in() && apply_filters( 'autoptimize_filter_compatibility_revslider_remover_active', true ) ) {
            add_filter(
                'autoptimize_filter_js_removables',
                function( $to_remove = '', $html = '' ) {
                    if ( ! empty( $html ) && false === strpos( $html, '<rs-slides' ) ) {
                        $to_remove .= 'plugins/revslider, setREVStartSize, window.RSIW, window.RS_MODULES';
                    }

                    return $to_remove;
                },
                11,
                2
            );
        }

        // Exclude jQuery if inline JS is found that requires jQuery.
        if ( $this->inline_js_config_checker() && false === strpos( $this->conf->get( 'autoptimize_js_exclude' ), 'jquery.min.js' ) && apply_filters( 'autoptimize_filter_compatibility_inline_jquery', true ) ) {
            add_filter(
                'autoptimize_filter_js_exclude',
                function( $js_excl = '', $html = '' ) {
                    if ( ! empty( $html ) && preg_match( '/<script[^>]*>[^<]*(jQuery|\$)\([^<]*<\/script>/Usm', $html ) ) {
                        if ( is_array( $js_excl ) ) {
                            $js_excl = implode( ',', $js_excl );
                        }

                        if ( false === strpos( $js_excl, 'jquery.min.js' ) ) {
                            $js_excl .= ', jquery.min.js';
                        }

                        // also exclude jquery.js if for whatever reason that is still used.
                        if ( false === strpos( $js_excl, 'jquery.js' ) ) {
                            $js_excl .= ', jquery.js';
                        }
                    }
                    return $js_excl;
                },
                12,
                2
            );
        }

        // Make JS-based Gutenberg blocks work OOTB.
        if ( $this->inline_js_config_checker() && apply_filters( 'autoptimize_filter_compatibility_gutenberg_js', true ) ) {
            add_filter(
                'autoptimize_filter_js_exclude',
                function( $js_excl = '', $html = '' ) {
                    if ( ! empty( $html ) && false !== strpos( $html, 'wp.i18n' ) || false !== strpos( $html, 'wp.apiFetch' ) || false !== strpos( $html, 'window.lodash' ) ) {
                        if ( is_array( $js_excl ) ) {
                            $js_excl = implode( ',', $js_excl );
                        }

                        if ( false === strpos( $js_excl, 'jquery.min.js' ) ) {
                            $js_excl .= ', jquery.min.js';
                        }

                        if ( false === strpos( $js_excl, 'wp-includes/js/dist' ) ) {
                            $js_excl .= ', wp-includes/js/dist';
                        }
                    }
                    return $js_excl;
                },
                13,
                2
            );
        }
    }

    public function inline_js_config_checker() {
        static $inline_js_flagged = null;

        if ( null === $inline_js_flagged ) {
            if ( ( $this->conf->get( 'autoptimize_js_aggregate' ) || apply_filters( 'autoptimize_filter_js_dontaggregate', false ) ) && apply_filters( 'autoptimize_js_include_inline', $this->conf->get( 'autoptimize_js_include_inline' ) ) ) {
                // if all files and also inline JS are aggregated we don't have to worry about inline JS.
                $inline_js_flagged = false;
            } else if ( apply_filters( 'autoptimize_filter_js_defer_not_aggregate', $this->conf->get( 'autoptimize_js_defer_not_aggregate' ) ) && apply_filters( 'autoptimize_js_filter_defer_inline', $this->conf->get( 'autoptimize_js_defer_inline' ) ) ) {
                // and when not aggregating but deferring all including inline JS, then all is OK too.
                $inline_js_flagged = false;
            }

            // in all other cases we need to pay attention to inline JS requiring src'ed JS to be available.
            $inline_js_flagged = true;
        }

        return $inline_js_flagged;
    }
}
