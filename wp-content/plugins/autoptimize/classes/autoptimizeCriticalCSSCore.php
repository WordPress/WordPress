<?php
/**
 * Critical CSS Core logic:
 * gets called by AO core, checks the rules and if a matching rule is found returns the associated CCSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCriticalCSSCore {
    /**
     * Critical CSS page types.
     *
     * @var array
     */
    protected $_types = null;
    
    /**
     * Critical CSS object.
     *
     * @var object
     */
    protected $criticalcss;

    public function __construct() {
        $this->criticalcss = autoptimize()->criticalcss();
        $this->run();
    }

    public function run() {
        $css_defer   = $this->criticalcss->get_option( 'css_defer' );
        $deferjquery = $this->criticalcss->get_option( 'deferjquery' );
        $unloadccss  = $this->criticalcss->get_option( 'unloadccss' );

        if ( ! $css_defer ) {
            return;
        }

        // add all filters to do CCSS
        // Set AO behavior: disable minification to avoid double minifying and caching.
        add_filter( 'autoptimize_filter_css_critcss_minify', '__return_false' );
        add_filter( 'autoptimize_filter_css_defer_inline', array( $this, 'ao_ccss_frontend' ), 10, 1 );

        // Add the action to enqueue jobs for CriticalCSS cron.
        if ( $this->criticalcss->is_api_active() ) {
            add_action( 'autoptimize_action_css_hash', array( $this->criticalcss, 'enqueue' ), 10, 1 );
        }

        // conditionally add the filter to defer jquery and others but only if not done so in autoptimizeScripts.
        $_native_defer = false;
        if ( 'on' === autoptimizeOptionWrapper::get_option( 'autoptimize_js_defer_not_aggregate' ) && 'on' === autoptimizeOptionWrapper::get_option( 'autoptimize_js_defer_inline' ) ) {
            $_native_defer = true;
        }
        if ( $deferjquery && ! $_native_defer ) {
            add_filter( 'autoptimize_html_after_minify', array( $this, 'ao_ccss_defer_jquery' ), 11, 1 );
        }

        // conditionally add filter to unload the CCSS.
        if ( $unloadccss ) {
            add_filter( 'autoptimize_html_after_minify', array( $this, 'ao_ccss_unloadccss' ), 12, 1 );
        }

        // Order paths by length, as longest ones have greater priority in the rules.
        $rules = $this->criticalcss->get_option( 'rules' );
        if ( ! empty( $rules['paths'] ) ) {
            $keys = array_map( 'strlen', array_keys( $rules['paths'] ) );
            array_multisort( $keys, SORT_DESC, $rules['paths'] );
            // TODO: Not sure what we're doing here. Sorted the $keys,
            // but they don't seem to be used anywhere.
        }

        // Add an array with default WordPress's conditional tags
        // NOTE: these tags are sorted.
        $this->_types = $this->get_ao_ccss_core_types();

        // Extend conditional tags on plugin initalization.
        add_action( apply_filters( 'autoptimize_filter_ccss_extend_types_hook', 'init' ), array( $this, 'ao_ccss_extend_types' ) );

        // When autoptimize cache is cleared, also clear transient cache for page templates.
        add_action( 'autoptimize_action_cachepurged', array( $this, 'ao_ccss_clear_page_tpl_cache' ), 10, 0 );
    }

    public function ao_ccss_frontend( $inlined ) {
        // Apply CriticalCSS to frontend pages
        // Attach types and settings arrays.
        $rules      = $this->criticalcss->get_option( 'rules' );
        $additional = $this->criticalcss->get_option( 'additional' );
        $loggedin   = $this->criticalcss->get_option( 'loggedin' );
        $debug      = $this->criticalcss->get_option( 'debug' );
        $no_ccss    = '';
        $additional = autoptimizeStyles::sanitize_css( $additional );

        // Only if keystatus is OK and option to add CCSS for logged on users is on or user is not logged in.
        if ( $loggedin || ! is_user_logged_in() ) {
            // Check for a valid CriticalCSS based on path to return its contents.
            $req_path = strtok( $_SERVER['REQUEST_URI'], '?' );
            if ( ! empty( $rules['paths'] ) ) {
                foreach ( $rules['paths'] as $path => $rule ) {
                    // explicit match OR partial match if MANUAL rule.
                    if ( ( $this->criticalcss->is_api_active() || $this->criticalcss->is_rule_manual( $rule ) ) && ( $req_path == $path || urldecode( $req_path ) == $path || ( apply_filters( 'autoptimize_filter_ccss_core_path_partial_match', true ) && false == $rule['hash'] && false != $rule['file'] && strpos( $req_path, str_replace( site_url(), '', $path ) ) !== false ) ) ) {
                        if ( file_exists( AO_CCSS_DIR . $rule['file'] ) ) {
                            $_ccss_contents = file_get_contents( AO_CCSS_DIR . $rule['file'] );
                            if ( 'none' != $_ccss_contents ) {
                                if ( $debug ) {
                                    $_ccss_contents = '/* PATH: ' . $path . ' hash: ' . $rule['hash'] . ' file: ' . $rule['file'] . ' */ ' . $_ccss_contents;
                                }
                                return apply_filters( 'autoptimize_filter_ccss_core_ccss', $_ccss_contents . $additional );
                            } else {
                                if ( $debug ) {
                                    $this->criticalcss->log( 'Path based rule with value "none" found.', 3 );
                                }
                                $no_ccss = 'none';
                            }
                        }
                    }
                }
            }

            // Check for a valid CriticalCSS based on conditional tags to return its contents.
            if ( ! empty( $rules['types'] ) && 'none' !== $no_ccss ) {
                // order types-rules by the order of the original $ao_ccss_types array so as not to depend on the order in which rules were added.
                $rules['types'] = array_replace( array_intersect_key( array_flip( $this->_types ), $rules['types'] ), $rules['types'] );
                $is_front_page  = is_front_page();

                foreach ( $rules['types'] as $type => $rule ) {
                    if ( ( $this->criticalcss->is_api_active() || $this->criticalcss->is_rule_manual( $rule ) ) && in_array( $type, $this->_types ) && file_exists( AO_CCSS_DIR . $rule['file'] ) ) {
                        $_ccss_contents = file_get_contents( AO_CCSS_DIR . $rule['file'] );
                        if ( $is_front_page && 'is_front_page' == $type ) {
                            if ( 'none' != $_ccss_contents ) {
                                if ( $debug ) {
                                    $_ccss_contents = '/* TYPES: ' . $type . ' hash: ' . $rule['hash'] . ' file: ' . $rule['file'] . ' */ ' . $_ccss_contents;
                                }
                                return apply_filters( 'autoptimize_filter_ccss_core_ccss', $_ccss_contents . $additional );
                            } else {
                                if ( $debug ) {
                                    $this->criticalcss->log( 'Conditional rule for is_front_page with value "none" found.', 3 );
                                }
                                $no_ccss = 'none';
                            }
                        } elseif ( ( $this->criticalcss->is_api_active() || $this->criticalcss->is_rule_manual( $rule ) ) && strpos( $type, 'custom_post_' ) === 0 && ! $is_front_page ) {
                            if ( get_post_type( get_the_ID() ) === substr( $type, 12 ) ) {
                                if ( 'none' != $_ccss_contents ) {
                                    if ( $debug ) {
                                        $_ccss_contents = '/* TYPES: ' . $type . ' hash: ' . $rule['hash'] . ' file: ' . $rule['file'] . ' */ ' . $_ccss_contents;
                                    }
                                    return apply_filters( 'autoptimize_filter_ccss_core_ccss', $_ccss_contents . $additional );
                                } else {
                                    if ( $debug ) {
                                        $this->criticalcss->log( 'Conditional rule custom_post with value "none" found.', 3 );
                                    }
                                    $no_ccss = 'none';
                                }
                            }
                        } elseif ( ( $this->criticalcss->is_api_active() || $this->criticalcss->is_rule_manual( $rule ) ) && 0 === strpos( $type, 'template_' ) && ! $is_front_page ) {
                            if ( is_page_template( substr( $type, 9 ) ) ) {
                                if ( 'none' != $_ccss_contents ) {
                                    if ( $debug ) {
                                        $_ccss_contents = '/* TYPES: ' . $type . ' hash: ' . $rule['hash'] . ' file: ' . $rule['file'] . ' */ ' . $_ccss_contents;
                                    }
                                    return apply_filters( 'autoptimize_filter_ccss_core_ccss', $_ccss_contents . $additional );
                                } else {
                                    if ( $debug ) {
                                        $this->criticalcss->log( 'Conditional rule for template with value "none" found.', 3 );
                                    }
                                    $no_ccss = 'none';
                                }
                            }
                        } elseif ( ( $this->criticalcss->is_api_active() || $this->criticalcss->is_rule_manual( $rule ) ) && ! $is_front_page ) {
                            // all "normal" conditional tags, core + woo + buddypress + edd + bbpress
                            // but we have to remove the prefix for the non-core ones for them to function.
                            $type = str_replace( array( 'woo_', 'bp_', 'bbp_', 'edd_' ), '', $type );
                            if ( function_exists( $type ) && call_user_func( $type ) ) {
                                if ( 'none' != $_ccss_contents ) {
                                    if ( $debug ) {
                                        $_ccss_contents = '/* TYPES: ' . $type . ' hash: ' . $rule['hash'] . ' file: ' . $rule['file'] . ' */ ' . $_ccss_contents;
                                    }
                                    return apply_filters( 'autoptimize_filter_ccss_core_ccss', $_ccss_contents . $additional );
                                } else {
                                    if ( $debug ) {
                                        $this->criticalcss->log( 'Conditional rule for ' . $type . ' with value "none" found.', 3 );
                                    }
                                    $no_ccss = 'none';
                                }
                            }
                        }
                    }
                }
            }
        }

        // Finally, inline the default CriticalCSS if any or else the entire CSS for the page
        // This also applies to logged in users if the option to add CCSS for logged in users has been disabled.
        if ( ! empty( $inlined ) && 'none' !== $no_ccss ) {
            if ( $debug ) {
                $this->criticalcss->log( 'Using default "above the fold" CSS.', 3 );
            }
            return apply_filters( 'autoptimize_filter_ccss_core_ccss', $inlined . $additional );
        } else {
            if ( $debug ) {
                $this->criticalcss->log( 'No matching CCSS found, switching to inlining full CSS.', 3 );
            }
            add_filter( 'autoptimize_filter_css_inline', '__return_true' );
            return;
        }
    }

    public function ao_ccss_defer_jquery( $in ) {
        $loggedin = $this->criticalcss->get_option( 'loggedin' );

        // defer all linked and inline JS.
        if ( ( ! is_user_logged_in() || $loggedin ) && preg_match_all( '#<script.*>(.*)</script>#Usmi', $in, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                if ( str_replace( apply_filters( 'autoptimize_filter_ccss_core_defer_exclude', array( 'data-noptimize="1"', 'data-cfasync="false"', 'data-pagespeed-no-defer' ) ), '', $match[0] ) !== $match[0] ) {
                    // do not touch JS with noptimize/ cfasync/ pagespeed-no-defer flags.
                    continue;
                } elseif ( '' !== $match[1] && ( ! preg_match( '/<script.* type\s?=.*>/', $match[0] ) || preg_match( '/type\s*=\s*[\'"]?(?:text|application)\/(?:javascript|ecmascript)[\'"]?/i', $match[0] ) ) ) {
                    // base64-encode and defer all inline JS.
                    $base64_js = '<script defer src="data:text/javascript;base64,' . base64_encode( $match[1] ) . '"></script>';
                    $in        = str_replace( $match[0], $base64_js, $in );
                } elseif ( str_replace( array( ' defer', ' async' ), '', $match[0] ) === $match[0] ) {
                    // and defer linked JS unless already deferred or asynced.
                    $new_match = str_replace( '<script ', '<script defer ', $match[0] );
                    $in        = str_replace( $match[0], $new_match, $in );
                }
            }
        }
        return $in;
    }

    public function ao_ccss_unloadccss( $html_in ) {
        // set media attrib of inline CCSS to none at onLoad to avoid it impacting full CSS (rarely needed).
        $_unloadccss_js = apply_filters( 'autoptimize_filter_ccss_core_unloadccss_js', '<script>window.addEventListener("load", function(event) {var el = document.getElementById("aoatfcss"); if(el) el.media = "none";})</script>' );

        if ( false !== strpos( $html_in, $_unloadccss_js . '</body>' ) ) {
            return $html_in;
        }

        return str_replace( '</body>', $_unloadccss_js . '</body>', $html_in );
    }

    /**
     * Get the types array.
     *
     * @return array|null
     */
    public function get_types() {
        return $this->_types;
    }

    public function ao_ccss_extend_types() {
        // Extend contidional tags
        // Attach the conditional tags array.

        // in some cases $ao_ccss_types is empty and/or not an array, this should work around that problem.
        if ( empty( $this->_types ) || ! is_array( $this->_types ) ) {
            $this->_types = $this->get_ao_ccss_core_types();
            $this->ao_ccss_log( 'Empty types array in extend, refetching array with core conditionals.', 3 );
        }

        // Custom Post Types.
        $cpts = get_post_types(
            array(
                'public'   => true,
                '_builtin' => false,
            ),
            'names',
            'and'
        );
        foreach ( $cpts as $cpt ) {
            array_unshift( $this->_types, 'custom_post_' . $cpt );
        }

        // Templates.
        // Transient cache to avoid frequent disk reads.
        $templates = get_transient( 'autoptimize_ccss_page_templates' );
        if ( ! $templates ) {
            $templates = wp_get_theme()->get_page_templates();
            set_transient( 'autoptimize_ccss_page_templates', $templates, HOUR_IN_SECONDS );
        }
        foreach ( $templates as $tplfile => $tplname ) {
            array_unshift( $this->_types, 'template_' . $tplfile );
        }

        // bbPress tags.
        if ( function_exists( 'is_bbpress' ) ) {
            $this->_types = array_merge(
                array(
                    'bbp_is_bbpress',
                    'bbp_is_favorites',
                    'bbp_is_forum_archive',
                    'bbp_is_replies_created',
                    'bbp_is_reply_edit',
                    'bbp_is_reply_move',
                    'bbp_is_search',
                    'bbp_is_search_results',
                    'bbp_is_single_forum',
                    'bbp_is_single_reply',
                    'bbp_is_single_topic',
                    'bbp_is_single_user',
                    'bbp_is_single_user_edit',
                    'bbp_is_single_view',
                    'bbp_is_subscriptions',
                    'bbp_is_topic_archive',
                    'bbp_is_topic_edit',
                    'bbp_is_topic_merge',
                    'bbp_is_topic_split',
                    'bbp_is_topic_tag',
                    'bbp_is_topic_tag_edit',
                    'bbp_is_topics_created',
                    'bbp_is_user_home',
                    'bbp_is_user_home_edit',
                ),
                $this->_types
            );
        }

        // BuddyPress tags.
        if ( function_exists( 'is_buddypress' ) ) {
            $this->_types = array_merge(
                array(
                    'bp_is_activation_page',
                    'bp_is_activity',
                    'bp_is_blogs',
                    'bp_is_buddypress',
                    'bp_is_change_avatar',
                    'bp_is_create_blog',
                    'bp_is_friend_requests',
                    'bp_is_friends',
                    'bp_is_friends_activity',
                    'bp_is_friends_screen',
                    'bp_is_group_admin_page',
                    'bp_is_group_create',
                    'bp_is_group_forum',
                    'bp_is_group_forum_topic',
                    'bp_is_group_home',
                    'bp_is_group_invites',
                    'bp_is_group_leave',
                    'bp_is_group_members',
                    'bp_is_group_single',
                    'bp_is_groups',
                    'bp_is_messages',
                    'bp_is_messages_compose_screen',
                    'bp_is_messages_conversation',
                    'bp_is_messages_inbox',
                    'bp_is_messages_sentbox',
                    'bp_is_my_activity',
                    'bp_is_my_blogs',
                    'bp_is_notices',
                    'bp_is_profile_edit',
                    'bp_is_register_page',
                    'bp_is_settings_component',
                    'bp_is_user',
                    'bp_is_user_profile',
                    'bp_is_wire',
                ),
                $this->_types
            );
        }

        // Easy Digital Downloads (EDD) tags.
        if ( function_exists( 'edd_is_checkout' ) ) {
            $this->_types = array_merge(
                array(
                    'edd_is_checkout',
                    'edd_is_failed_transaction_page',
                    'edd_is_purchase_history_page',
                    'edd_is_success_page',
                ),
                $this->_types
            );
        }

        // WooCommerce tags.
        if ( class_exists( 'WooCommerce' ) ) {
            $this->_types = array_merge(
                array(
                    'woo_is_account_page',
                    'woo_is_cart',
                    'woo_is_checkout',
                    'woo_is_product',
                    'woo_is_product_category',
                    'woo_is_product_tag',
                    'woo_is_shop',
                    'woo_is_wc_endpoint_url',
                    'woo_is_woocommerce',
                ),
                $this->_types
            );
        }
    }

    public function get_ao_ccss_core_types() {
        return array(
            'is_404',
            'is_front_page',
            'is_home',
            'is_page',
            'is_single',
            'is_category',
            'is_author',
            'is_archive',
            'is_search',
            'is_attachment',
            'is_sticky',
            'is_paged',
        );
    }

    public function ao_ccss_key_status( $render ) {
        // Provide key status
        // Get key and key status.
        $key = $this->criticalcss->get_option( 'key' );
        $key_status = $this->criticalcss->get_option( 'keyst' );

        // Prepare returned variables.
        $key_return = array();
        $status     = false;

        if ( $key && 2 == $key_status ) {
            // Key exists and its status is valid.
            // Set valid key status.
            $status     = 'valid';
            $status_msg = __( 'Valid' );
            $color      = '#46b450'; // Green.
            $message    = null;
        } elseif ( $key && 1 == $key_status ) {
            // Key exists but its validation has failed.
            // Set invalid key status.
            $status     = 'invalid';
            $status_msg = __( 'Invalid' );
            $color      = '#dc3232'; // Red.
            $message    = __( 'Your API key is invalid. Please enter a valid <a href="https://criticalcss.com/?aff=1" target="_blank">criticalcss.com</a> key.', 'autoptimize' );
        } elseif ( $key && ! $key_status ) {
            // Key exists but it has no valid status yet
            // Perform key validation.
            $key_check = $this->ao_ccss_key_validation( $key );

            // Key is valid, set valid status.
            if ( $key_check ) {
                $status     = 'valid';
                $status_msg = __( 'Valid' );
                $color      = '#46b450'; // Green.
                $message    = null;
            } else {
                // Key is invalid, set invalid status.
                $status     = 'invalid';
                $status_msg = __( 'Invalid' );
                $color      = '#dc3232'; // Red.
                if ( get_option( 'autoptimize_ccss_keyst' ) == 1 ) {
                    $message = __( 'Your API key is invalid. Please enter a valid <a href="https://criticalcss.com/?aff=1" target="_blank">criticalcss.com</a> key.', 'autoptimize' );
                } else {
                    $message = __( 'Something went wrong when checking your API key, make sure you server can communicate with https://criticalcss.com and/ or try again later.', 'autoptimize' );
                }
            }
        } else {
            // No key nor status
            // Set no key status.
            $status     = 'nokey';
            $status_msg = __( 'None' );
            $color      = '#ffb900'; // Yellow.
            $message    = __( 'Please enter a valid <a href="https://criticalcss.com/?aff=1" target="_blank">criticalcss.com</a> API key to start.', 'autoptimize' );
        }

        // Fill returned values.
        $key_return['status'] = $status;
        // Provide rendering information if required.
        if ( $render ) {
            $key_return['stmsg'] = $status_msg;
            $key_return['color'] = $color;
            $key_return['msg']   = $message;
        }

        // Return key status.
        return $key_return;
    }

    public function ao_ccss_key_validation( $key ) {
        $noptimize = $this->criticalcss->get_option( 'noptimize' );

        // POST a dummy job to criticalcss.com to check for key validation
        // Prepare home URL for the request.
        $src_url = get_home_url();

        // Avoid AO optimizations if required by config or avoid lazyload if lazyload is active in AO.
        if ( ! empty( $noptimize ) ) {
            $src_url .= '/?ao_noptirocket=1';
        } elseif ( class_exists( 'autoptimizeImages', false ) && autoptimizeImages::should_lazyload_wrapper() ) {
            $src_url .= '/?ao_nolazy=1';
        }

        $src_url = apply_filters( 'autoptimize_filter_ccss_cron_srcurl', $src_url );

        if ( true !== autoptimizeUtils::is_local_server( parse_url( $src_url,  PHP_URL_HOST ) ) ) {
            // Prepare the request.
            $url  = esc_url_raw( AO_CCSS_API . 'generate' );
            $args = array(
                'headers' => apply_filters(
                    'autoptimize_ccss_cron_api_generate_headers',
                    array(
                        'User-Agent'    => 'Autoptimize v' . AO_CCSS_VER,
                        'Content-type'  => 'application/json; charset=utf-8',
                        'Authorization' => 'JWT ' . $key,
                        'Connection'    => 'close',
                    )
                ),
                // Body must be JSON.
                'body'    => json_encode(
                    apply_filters(
                        'autoptimize_ccss_cron_api_generate_body',
                        array(
                            'url'    => $src_url,
                            'aff'    => 1,
                            'aocssv' => AO_CCSS_VER,
                        )
                    ),
                    JSON_UNESCAPED_SLASHES
                ),
            );

            // Dispatch the request and store its response code.
            $req  = wp_safe_remote_post( $url, $args );
            $code = wp_remote_retrieve_response_code( $req );
            $body = json_decode( wp_remote_retrieve_body( $req ), true );

            if ( 200 == $code ) {
                // Response is OK.
                // Set key status as valid and log key check.
                update_option( 'autoptimize_ccss_keyst', 2 );
                $this->ao_ccss_log( 'criticalcss.com: API key is valid, updating key status', 3 );

                // extract job-id from $body and put it in the queue as a P job
                // but only if no jobs and no rules!
                $queue = $this->criticalcss->get_option( 'queue' );
                $rules = $this->criticalcss->get_option( 'rules' );

                if ( 0 == count( $queue ) && 0 == count( $rules['types'] ) && 0 == count( $rules['paths'] ) ) {
                    if ( 'JOB_QUEUED' == $body['job']['status'] || 'JOB_ONGOING' == $body['job']['status'] ) {
                        $jprops['ljid']     = 'firstrun';
                        $jprops['rtarget']  = 'types|is_front_page';
                        $jprops['ptype']    = 'is_front_page';
                        $jprops['hashes'][] = 'dummyhash';
                        $jprops['hash']     = 'dummyhash';
                        $jprops['file']     = null;
                        $jprops['jid']      = $body['job']['id'];
                        $jprops['jqstat']   = $body['job']['status'];
                        $jprops['jrstat']   = null;
                        $jprops['jvstat']   = null;
                        $jprops['jctime']   = microtime( true );
                        $jprops['jftime']   = null;
                        $queue['/'] = $jprops;
                        $queue_raw  = json_encode( $queue );
                        update_option( 'autoptimize_ccss_queue', $queue_raw, false );
                        $this->ao_ccss_log( 'Created P job for is_front_page based on API key check response.', 3 );
                    }
                }
                return true;
            } elseif ( 401 == $code ) {
                // Response is unauthorized
                // Set key status as invalid and log key check.
                update_option( 'autoptimize_ccss_keyst', 1 );
                $this->ao_ccss_log( 'criticalcss.com: API key is invalid, updating key status', 3 );
                return false;
            } else {
                // Response unkown
                // Log key check attempt.
                $this->ao_ccss_log( 'criticalcss.com: could not check API key status, this is a service error, body follows if any...', 2 );
                if ( ! empty( $body ) ) {
                    $this->ao_ccss_log( print_r( $body, true ), 2 );
                }
                if ( is_wp_error( $req ) ) {
                    $this->ao_ccss_log( $req->get_error_message(), 2 );
                }
                return false;
            }
        } else {
            // localhost/ private network server, no API check possible.
            return false;
        }
    }

    public function ao_ccss_viewport() {
        // Get viewport size
        // Attach viewport option.
        $viewport = $this->criticalcss->get_option( 'viewport' );

        return array(
            'w' => ! empty( $viewport['w'] ) ? $viewport['w'] : '',
            'h' => ! empty( $viewport['h'] ) ? $viewport['h'] : '',
        );
    }

    public function ao_ccss_check_contents( $ccss ) {
        // Perform basic exploit avoidance and CSS validation.
        if ( ! empty( $ccss ) ) {
            // Try to avoid code injection.
            $blocklist = array( '#!/', 'function(', '<script', '<?php', '</style', ' onload=', ' onerror=', ' onmouse', ' onscroll=', ' onclick=' );
            foreach ( $blocklist as $blocklisted ) {
                if ( stripos( $ccss, $blocklisted ) !== false ) {
                    $this->ao_ccss_log( 'Critical CSS received contained blocklisted content.', 2 );
                    return false;
                }
            }

            // Check for most basics CSS structures.
            $needlist = array( '{', '}', ':' );
            foreach ( $needlist as $needed ) {
                if ( false === strpos( $ccss, $needed ) && 'none' !== $ccss ) {
                    $this->ao_ccss_log( 'Critical CSS received did not seem to contain real CSS.', 2 );
                    return false;
                }
            }
        }

        // Return true if file critical CSS is sane.
        return true;
    }

    public function ao_ccss_log( $msg, $lvl ) {
        // Commom logging facility
        // Attach debug option.
        $debug = $this->criticalcss->get_option( 'debug' );

        // Prepare log levels, where accepted $lvl are:
        // 1: II (for info)
        // 2: EE (for error)
        // 3: DD (for debug)
        // Default: UU (for unkown).
        $level = false;
        if ( $debug ) {
            switch ( $lvl ) {
                case 1:
                    $level = 'II';
                    break;
                case 2:
                    $level = 'EE';
                    break;
                case 3:
                    $level = 'DD';
                    break;
                default:
                    $level = 'UU';
            }
        }

        // Prepare and write a log message if there's a valid level.
        if ( $level ) {

            // Prepare message.
            $message = date( 'c' ) . ' - [' . $level . '] ' . htmlentities( $msg ) . '<br>'; // @codingStandardsIgnoreLine

            // Write message to log file.
            error_log( $message, 3, AO_CCSS_LOG );
        }
    }

    public function ao_ccss_clear_page_tpl_cache() {
        // Clears transient cache for page templates.
        delete_transient( 'autoptimize_ccss_page_templates' );
    }
}
