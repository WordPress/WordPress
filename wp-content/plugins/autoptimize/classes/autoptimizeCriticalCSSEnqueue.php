<?php
/**
 * Critical CSS job enqueue logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCriticalCSSEnqueue {
    public function __construct() {
        $this->criticalcss = autoptimize()->criticalcss();
    }

    public function ao_ccss_enqueue( $hash = '', $path = '', $type = 'is_page' ) {
        // Get key status.
        $key = $this->criticalcss->key_status( false );

        // Queue is available to anyone...
        $enqueue = true;

        // ... which are not the ones below.
        if ( true === autoptimizeUtils::is_local_server() ) {
            $enqueue = false;
            $this->criticalcss->log('cant enqueue as local/ private', 3 );
        } elseif ( 'nokey' == $key['status'] || 'invalid' == $key['status'] ) {
            $enqueue = false;
            $this->criticalcss->log( 'Job queuing is not available: no valid API key found.', 3 );
        } elseif ( ! empty( $hash ) && ( is_user_logged_in() || is_feed() || is_404() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || $this->ao_ccss_ua() || false === apply_filters( 'autoptimize_filter_ccss_enqueue_should_enqueue', true ) ) ) {
            $enqueue = false;
            $this->criticalcss->log( 'Job queuing is not available for WordPress\'s logged in users, feeds, error pages, ajax calls or calls from criticalcss.com itself.', 3 );
        } elseif ( empty( $hash ) && empty( $path ) || ( ( 'is_single' !== $type ) && ( 'is_page' !== $type ) ) ) {
            $enqueue = false;
            $this->criticalcss->log( 'Forced job queuing failed, no path or not right type', 3 );
        }

        if ( ! $enqueue ) {
            return;
        }

        // Continue if queue is available
        // Attach required arrays/ vars.
        $rules = $this->criticalcss->get_option( 'rules' );
        $queue_raw = $this->criticalcss->get_option( 'queue_raw' );
        $queue = $this->criticalcss->get_option( 'queue' );
        $forcepath = $this->criticalcss->get_option( 'forcepath' );

        // Get request path and page type, and initialize the queue update flag.
        if ( ! empty( $hash ) ) {
            $req_orig = $_SERVER['REQUEST_URI'];
            $req_type = $this->ao_ccss_get_type();
        } elseif ( ! empty( $path ) ) {
            $req_orig = $path;
            if ( '/' === $path ) {
                $req_type = 'is_front_page';
            } else {
                $req_type = $type;
            }
        }
        $req_path = strtok( $req_orig, '?' );

        // Check if we have a lang param. we need to keep as WPML can switch languages based on that
        // and that includes RTL -> LTR so diff. structure, so rules would be RTL vs LTR
        // but this needs changes in the structur of the rule object so off by default for now
        // as now this will simply result in conditional rules being overwritten.
        if ( apply_filters( 'autoptimize_filter_ccss_coreenqueue_honor_lang', false ) && strpos( $req_orig, 'lang=' ) !== false ) {
            $req_params = strtok( '?' );
            parse_str( $req_params, $req_params_arr );
            if ( array_key_exists( 'lang', $req_params_arr ) && ! empty( $req_params_arr['lang'] ) ) {
                $req_path .= '?lang=' . $req_params_arr['lang'];
            }
        }

        $job_qualify     = false;
        $target_rule     = false;
        $rule_properties = false;
        $queue_update    = false;

        // Match for paths in rules.
        foreach ( $rules['paths'] as $path => $props ) {

            // Prepare rule target and log.
            $target_rule = 'paths|' . $path;
            $this->criticalcss->log( 'Qualifying path <' . $req_path . '> for job submission by rule <' . $target_rule . '>', 3 );

            // Path match
            // -> exact match needed for AUTO rules
            // -> partial match OK for MANUAL rules (which have empty hash and a file with CCSS).
            if ( $path === $req_path || ( false == $props['hash'] && false != $props['file'] && preg_match( '|' . $path . '|', $req_path ) ) ) {

                // There's a path match in the rule, so job QUALIFIES with a path rule match.
                $job_qualify     = true;
                $rule_properties = $props;
                $this->criticalcss->log( 'Path <' . $req_path . '> QUALIFIED for job submission by rule <' . $target_rule . '>', 3 );

                // Stop processing other path rules.
                break;
            }
        }

        // Match for types in rules if no path rule matches and if we're not enforcing paths.
        if ( '' !== $hash && ! $job_qualify && ( ! $forcepath || ! in_array( $req_type, apply_filters( 'autoptimize_filter_ccss_coreenqueue_forcepathfortype', array( 'is_page' ) ) ) || ! apply_filters( 'autoptimize_filter_ccss_coreenqueue_ignorealltypes', false ) ) ) {
            foreach ( $rules['types'] as $type => $props ) {

                // Prepare rule target and log.
                $target_rule = 'types|' . $type;
                $this->criticalcss->log( 'Qualifying page type <' . $req_type . '> on path <' . $req_path . '> for job submission by rule <' . $target_rule . '>', 3 );

                if ( $req_type == $type ) {
                    // Type match.
                    // There's a type match in the rule, so job QUALIFIES with a type rule match.
                    $job_qualify     = true;
                    $rule_properties = $props;
                    $this->criticalcss->log( 'Page type <' . $req_type . '> on path <' . $req_path . '> QUALIFIED for job submission by rule <' . $target_rule . '>', 3 );

                    // Stop processing other type rules.
                    break;
                }
            }
        }

        if ( $job_qualify && ( ( false == $rule_properties['hash'] && false != $rule_properties['file'] ) || strpos( $req_type, 'template_' ) !== false ) ) {
            // If job qualifies but rule hash is false and file isn't false (MANUAL rule) or if template, job does not qualify despite what previous evaluations says.
            $job_qualify = false;
            $this->criticalcss->log( 'Job submission DISQUALIFIED by MANUAL rule <' . $target_rule . '> with hash <' . $rule_properties['hash'] . '> and file <' . $rule_properties['file'] . '>', 3 );
        } elseif ( ! $job_qualify && empty( $rule_properties ) ) {
            // But if job does not qualify and rule properties are set, job qualifies as there is no matching rule for it yet
            // Fill-in the new target rule.
            $job_qualify = true;

            // Should we switch to path-base AUTO-rules? Conditions:
            // 1. forcepath option has to be enabled (off by default)
            // 2. request type should be (by default, but filterable) one of is_page (removed for now: woo_is_product or woo_is_product_category).
            if ( ( $forcepath && in_array( $req_type, apply_filters( 'autoptimize_filter_ccss_coreenqueue_forcepathfortype', array( 'is_page' ) ) ) ) || apply_filters( 'autoptimize_filter_ccss_coreenqueue_ignorealltypes', false ) || empty( $hash ) ) {
                if ( '/' !== $req_path ) {
                    $target_rule = 'paths|' . $req_path;
                } else {
                    // Exception; we don't want a path-based rule for "/" as that messes things up, hard-switch this to a type-based is_front_page rule.
                    $target_rule = 'types|' . 'is_front_page';
                }
            } else {
                $target_rule = 'types|' . $req_type;
            }
            $this->criticalcss->log( 'Job submission QUALIFIED by MISSING rule for page type <' . $req_type . '> on path <' . $req_path . '>, new rule target is <' . $target_rule . '>', 3 );
        } else {
            // Or just log a job qualified by a matching rule.
            $this->criticalcss->log( 'Job submission QUALIFIED by AUTO rule <' . $target_rule . '> with hash <' . $rule_properties['hash'] . '> and file <' . $rule_properties['file'] . '>', 3 );
        }

        // Submit job.
        if ( $job_qualify ) {
            if ( ! array_key_exists( $req_path, $queue ) ) {
                // This is a NEW job
                // Merge job into the queue.
                $queue[ $req_path ] = $this->ao_ccss_define_job(
                    $req_path,
                    $target_rule,
                    $req_type,
                    $hash,
                    null,
                    null,
                    null,
                    null,
                    true
                );
                // Set update flag.
                $queue_update = true;
            } else {
                // This is an existing job
                // The job is still NEW, most likely this is extra CSS file for the same page that needs a hash.
                if ( 'NEW' == $queue[ $req_path ]['jqstat'] ) {
                    // Add hash if it's not already in the job.
                    if ( ! in_array( $hash, $queue[ $req_path ]['hashes'] ) ) {
                        // Push new hash to its array and update flag.
                        $queue_update = array_push( $queue[ $req_path ]['hashes'], $hash );

                        // Log job update.
                        $this->criticalcss->log( 'Hashes UPDATED on local job id <' . $queue[ $req_path ]['ljid'] . '>, job status NEW, target rule <' . $queue[ $req_path ]['rtarget'] . '>, hash added: ' . $hash, 3 );

                        // Return from here as the hash array is already updated.
                        return true;
                    }
                } elseif ( 'NEW' != $queue[ $req_path ]['jqstat'] && 'JOB_QUEUED' != $queue[ $req_path ]['jqstat'] && 'JOB_ONGOING' != $queue[ $req_path ]['jqstat'] ) {
                    // Allow requeuing jobs that are not NEW, JOB_QUEUED or JOB_ONGOING
                    // Merge new job keeping some previous job values.
                    $queue[ $req_path ] = $this->ao_ccss_define_job(
                        $req_path,
                        $target_rule,
                        $req_type,
                        $hash,
                        $queue[ $req_path ]['file'],
                        $queue[ $req_path ]['jid'],
                        $queue[ $req_path ]['jrstat'],
                        $queue[ $req_path ]['jvstat'],
                        false
                    );
                    // Set update flag.
                    $queue_update = true;
                }
            }

            if ( $queue_update ) {
                // Persist the job to the queue and return.
                $queue_raw = json_encode( $queue );
                update_option( 'autoptimize_ccss_queue', $queue_raw, false );
                $this->criticalcss->flush_options();
                return true;
            } else {
                // Or just return false if no job was added.
                $this->criticalcss->log( 'A job for path <' . $req_path . '> already exist with NEW or PENDING status, skipping job creation', 3 );
                return false;
            }
        }
    }

    public function ao_ccss_get_type() {
        // Get the type of a page
        // Attach the conditional tags array.
        $types = $this->criticalcss->get_types();
        $forcepath = $this->criticalcss->get_option( 'forcepath' );

        // By default, a page type is false.
        $page_type = false;

        // Iterates over the array to match a type.
        foreach ( $types as $type ) {
            if ( is_404() ) {
                $page_type = 'is_404';
                break;
            } elseif ( is_front_page() ) {
                // identify frontpage immediately to avoid it also matching a CPT or template.
                $page_type = 'is_front_page';
                break;
            } elseif ( strpos( $type, 'custom_post_' ) !== false && ( ! $forcepath || ! is_page() ) && is_singular() )  {
                // Match custom post types and not page or page not forced to path-based.
                if ( get_post_type( get_the_ID() ) === substr( $type, 12 ) ) {
                    $page_type = $type;
                    break;
                }
            } elseif ( strpos( $type, 'template_' ) !== false && ( ! $forcepath || ! is_page() ) ) {
                // Match templates if not page or if page is not forced to path-based.
                if ( is_page_template( substr( $type, 9 ) ) ) {
                    $page_type = $type;
                    break;
                }
            } else {
                // Match all other existing types
                // but remove prefix to be able to check if the function exists & returns true.
                $_type = str_replace( array( 'woo_', 'bp_', 'bbp_', 'edd_' ), '', $type );
                if ( function_exists( $_type ) && call_user_func( $_type ) ) {
                    // Make sure we only return for one page, not for the "paged pages" (/page/2 ..).
                    if ( ! is_page() || ! is_paged() ) {
                        $page_type = $type;
                        break;
                    }
                }
            }
        }

        // Return the page type.
        return $page_type;
    }

    public function ao_ccss_define_job( $path, $target, $type, $hash, $file, $jid, $jrstat, $jvstat, $create ) {
        // Define a job entry to be created or updated
        // Define commom job properties.
        $path            = array();
        $path['ljid']    = $this->ao_ccss_job_id();
        $path['rtarget'] = $target;
        $path['ptype']   = $type;
        $path['hashes']  = array( $hash );
        $path['hash']    = $hash;
        $path['file']    = $file;
        $path['jid']     = $jid;
        $path['jqstat']  = 'NEW';
        $path['jrstat']  = $jrstat;
        $path['jvstat']  = $jvstat;
        $path['jctime']  = microtime( true );
        $path['jftime']  = null;

        // Set operation requested.
        if ( $create ) {
            $operation = 'CREATED';
        } else {
            $operation = 'UPDATED';
        }

        // Log job creation.
        $this->criticalcss->log( 'Job ' . $operation . ' with local job id <' . $path['ljid'] . '> for target rule <' . $target . '>', 3 );

        return $path;
    }

    public function ao_ccss_job_id( $length = 6 ) {
        // Generate random strings for the local job ID
        // Based on https://stackoverflow.com/a/4356295 .
        $characters        = '0123456789abcdefghijklmnopqrstuvwxyz';
        $characters_length = strlen( $characters );
        $random_string     = 'j-';
        for ( $i = 0; $i < $length; $i++ ) {
            $random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
        }
        return $random_string;
    }

    public function ao_ccss_ua() {
        // Check for criticalcss.com user agent.
        $agent = '';
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }

        // Check for UA and return TRUE when criticalcss.com is the detected UA, false when not.
        $rtn = strpos( $agent, AO_CCSS_URL );
        if ( 0 === $rtn ) {
            $rtn = true;
        } else {
            $rtn = false;
        }
        return ( $rtn );
    }
}
