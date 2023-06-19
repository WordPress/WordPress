<?php
/**
 * Critical CSS Cron logic:
 * processes the queue, submitting jobs to criticalcss.com and retrieving generated CSS from criticalcss.com and saving rules.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCriticalCSSCron {
    public function __construct() {
        $this->criticalcss = autoptimize()->criticalcss();

        // Add queue control to a registered event.
        add_action( 'ao_ccss_queue', array( $this, 'ao_ccss_queue_control' ) );
        // Add cleaning job to a registered event.
        add_action( 'ao_ccss_maintenance', array( $this, 'ao_ccss_cleaning' ) );
    }

    public function ao_ccss_queue_control() {
        // The queue execution backend.
        $key = $this->criticalcss->get_option( 'key' );

        if ( empty( $key ) ) {
            // no key set, not processing the queue!
            $this->criticalcss->log( 'No key set, so not processing queue.', 3 );
            return;
        }

        /**
         * Provide a debug facility for the queue
         *    This debug facility provides a way to easily force some queue behaviors useful for development and testing.
         *    To enable this feature, create the file AO_CCSS_DIR . 'queue.json' with a JSON object like the one bellow:
         *
         *    {"enable":bool,"htcode":int,"status":0|"str","resultStatus ":0|"str","validationStatus":0|"str"}
         *
         *    Where values are:
         *    - enable                    : 0 or 1, enable or disable this debug facility straight from the file
         *    - htcode                    : 0 or any HTTP reponse code (e.g. 2xx, 4xx, 5xx) to force API responses
         *    - status                    : 0 or a valid value for 'status' (see 'Generating critical css - Job Status Types' in spec docs)
         *    - resultStatus        : 0 or a valid value for 'resultStatus' (see 'Appendix - Result status types' in the spec docs)
         *    - validationStatus: 0 or a valid value for 'validationStatus' (see 'Appendix - Validation status types' in the spec docs)
         *
         *    When properly set, queue will always finish a job with the declared settings above regardless of the real API responses.
         */
        $queue_debug = false;
        if ( file_exists( AO_CCSS_DEBUG ) ) {
            $qdobj_raw = file_get_contents( AO_CCSS_DEBUG );
            $qdobj     = json_decode( $qdobj_raw, true );
            if ( $qdobj ) {
                if ( 1 === $qdobj['enable'] ) {
                    $queue_debug = true;
                    $this->criticalcss->log( 'Queue operating in debug mode with the following settings: <' . $qdobj_raw . '>', 3 );
                }
            }
        }

        // Set some default values for $qdobj to avoid function call warnings.
        if ( ! $queue_debug ) {
            $qdobj['htcode'] = false;
        }

        // Check if queue is already running.
        $queue_lock = false;
        if ( file_exists( AO_CCSS_LOCK ) ) {
            $queue_lock = true;
        }

        // Proceed with the queue if it's not already running.
        if ( ! $queue_lock ) {

            // Log queue start and create the lock file.
            $this->criticalcss->log( 'Queue control started', 3 );
            if ( touch( AO_CCSS_LOCK ) ) {
                $this->criticalcss->log( 'Queue control locked', 3 );
            }

            // Attach required variables.
            $queue = $this->criticalcss->get_option( 'queue' );
            $rtimelimit = $this->criticalcss->get_option( 'rtimelimit' );

            // Initialize counters.
            if ( 0 == $rtimelimit ) {
                // no time limit set, let's go with 1000 seconds.
                $rtimelimit = 1000;
            }
            $mt = time() + $rtimelimit; // maxtime queue processing can run.
            $jc = 1; // job count number.
            $jr = 1; // jobs requests number.
            $jt = count( $queue ); // number of jobs in queue.

            // Sort queue by ascending job status (e.g. ERROR, JOB_ONGOING, JOB_QUEUED, NEW...).
            array_multisort( array_column( $queue, 'jqstat' ), $queue ); // @codingStandardsIgnoreLine

            // Iterates over the entire queue.
            foreach ( $queue as $path => $jprops ) {
                // Prepare flags and target rule.
                $update      = false;
                $deljob      = false;
                $rule_update = false;
                $oldccssfile = false;
                $trule       = explode( '|', $jprops['rtarget'] );

                // Log job count.
                $this->criticalcss->log( 'Processing job ' . $jc . ' of ' . $jt . ' with id <' . $jprops['ljid'] . '> and status <' . $jprops['jqstat'] . '>', 3 );

                // Process NEW jobs.
                if ( 'NEW' == $jprops['jqstat'] ) {

                    // Log the new job.
                    $this->criticalcss->log( 'Found NEW job with local ID <' . $jprops['ljid'] . '>, starting its queue processing', 3 );

                    // Compare job and rule hashes (if any).
                    $hash = $this->ao_ccss_diff_hashes( $jprops['ljid'], $jprops['hash'], $jprops['hashes'], $jprops['rtarget'] );

                    // If job hash is new or different of a previous one.
                    if ( $hash ) {
                        if ( $jr > 2 ) {
                            // we already posted 2 jobs to criticalcss.com, don't post more this run
                            // but we can keep on processing the queue to keep it tidy.
                            $this->criticalcss->log( 'Holding off on generating request for job with local ID <' . $jprops['ljid'] . '>, maximum number of POSTS reached.', 3 );
                            continue;
                        }

                        // Set job hash.
                        $jprops['hash'] = $hash;

                        // Dispatch the job generate request and increment request count.
                        $apireq = $this->ao_ccss_api_generate( $path, $queue_debug, $qdobj['htcode'] );
                        $jr++;

                        // NOTE: All the following conditions maps to the ones in admin_settings_queue.js.php.
                        if ( empty( $apireq ) ) {
                            // ERROR: no response
                            // Update job properties.
                            $jprops['jqstat'] = 'NO_RESPONSE';
                            $jprops['jrstat'] = 'NONE';
                            $jprops['jvstat'] = 'NONE';
                            $jprops['jftime'] = microtime( true );
                            $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> request has no response, status now is <' . $jprops['jqstat'] . '>', 3 );
                        } elseif ( array_key_exists( 'errorCode', $apireq ) && 'INVALID_JWT_TOKEN' == $apireq['errorCode'] ) {
                            // ERROR: key validation
                            // Update job properties.
                            $jprops['jqstat'] = $apireq['errorCode'];
                            $jprops['jrstat'] = $apireq['error'];
                            $jprops['jvstat'] = 'NONE';
                            $jprops['jftime'] = microtime( true );
                            $this->criticalcss->log( 'API key validation error when processing job id <' . $jprops['ljid'] . '>, job status is now <' . $jprops['jqstat'] . '>', 3 );
                        } elseif ( array_key_exists( 'job', $apireq ) && array_key_exists( 'status', $apireq['job'] ) && 'JOB_QUEUED' == $apireq['job']['status'] || 'JOB_ONGOING' == $apireq['job']['status'] ) {
                            // SUCCESS: request has a valid result.
                            // Update job properties.
                            $jprops['jid']    = $apireq['job']['id'];
                            $jprops['jqstat'] = $apireq['job']['status'];
                            $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> generate request successful, remote id <' . $jprops['jid'] . '>, status now is <' . $jprops['jqstat'] . '>', 3 );
                        } elseif ( array_key_exists( 'job', $apireq ) && array_key_exists( 'status', $apireq['job'] ) && 'STATUS_JOB_BAD' == $apireq['job']['status'] ) {
                            // ERROR: concurrent requests
                            // Update job properties.
                            $jprops['jid']    = $apireq['job']['id'];
                            $jprops['jqstat'] = $apireq['job']['status'];
                            if ( $apireq['job']['error'] ) {
                                $jprops['jrstat'] = $apireq['job']['error'];
                            } else {
                                $jprops['jrstat'] = 'Baby did a bad bad thing';
                            }
                            $jprops['jvstat'] = 'NONE';
                            $jprops['jftime'] = microtime( true );
                            $this->criticalcss->log( 'Concurrent requests when processing job id <' . $jprops['ljid'] . '>, job status is now <' . $jprops['jqstat'] . '>', 3 );
                        } else {
                            // UNKNOWN: unhandled generate exception
                            // Update job properties.
                            $jprops['jqstat'] = 'JOB_UNKNOWN';
                            $jprops['jrstat'] = 'NONE';
                            $jprops['jvstat'] = 'NONE';
                            $jprops['jftime'] = microtime( true );
                            $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> generate request has an UNKNOWN condition, status now is <' . $jprops['jqstat'] . '>, check log messages above for more information', 2 );
                            $this->criticalcss->log( 'Job response was: ' . json_encode( $apireq ), 3 );
                        }
                    } else {
                        // SUCCESS: Job hash is equal to a previous one, so it's done
                        // Update job status and finish time.
                        $jprops['jqstat'] = 'JOB_DONE';
                        $jprops['jftime'] = microtime( true );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> requires no further processing, status now is <' . $jprops['jqstat'] . '>', 3 );
                    }

                    // Set queue update flag.
                    $update = true;

                } elseif ( 'JOB_QUEUED' == $jprops['jqstat'] || 'JOB_ONGOING' == $jprops['jqstat'] ) {
                    // Process QUEUED and ONGOING jobs
                    // Log the pending job.
                    $this->criticalcss->log( 'Found PENDING job with local ID <' . $jprops['ljid'] . '>, continuing its queue processing', 3 );

                    // Dispatch the job result request and increment request count.
                    $apireq = $this->ao_ccss_api_results( $jprops['jid'], $queue_debug, $qdobj['htcode'] );

                    // NOTE: All the following condigitons maps to the ones in admin_settings_queue.js.php
                    // Replace API response values if queue debugging is enabled and some value is set.
                    if ( $queue_debug ) {
                        if ( $qdobj['status'] ) {
                            $apireq['status'] = $qdobj['status'];
                        }
                        if ( $qdobj['resultStatus'] ) {
                            $apireq['resultStatus'] = $qdobj['resultStatus'];
                        }
                        if ( $qdobj['validationStatus'] ) {
                            $apireq['validationStatus'] = $qdobj['validationStatus'];
                        }
                    }

                    if ( empty( $apireq ) || ! is_array( $apireq ) ) {
                        // ERROR: no response
                        // Update job properties.
                        $jprops['jqstat'] = 'NO_RESPONSE';
                        $jprops['jrstat'] = 'NONE';
                        $jprops['jvstat'] = 'NONE';
                        $jprops['jftime'] = microtime( true );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> request has no response, status now is <' . $jprops['jqstat'] . '>', 3 );
                    } elseif ( array_key_exists( 'status', $apireq ) && ( 'JOB_QUEUED' == $apireq['status'] || 'JOB_ONGOING' == $apireq['status'] ) ) {
                        // SUCCESS: request has a valid result
                        // Process a PENDING job
                        // Update job properties.
                        $jprops['jqstat'] = $apireq['status'];
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful, remote id <' . $jprops['jid'] . '>, status <' . $jprops['jqstat'] . '> unchanged', 3 );
                    } elseif ( array_key_exists( 'status', $apireq ) && 'JOB_DONE' == $apireq['status'] ) {
                        // Process a DONE job
                        // New resultStatus from ccss.com "HTML_404", consider as "GOOD" for now.
                        if ( 'HTML_404' == $apireq['resultStatus'] ) {
                            $apireq['resultStatus'] = 'GOOD';
                        }

                        if ( 'GOOD' == $apireq['resultStatus'] && ( 'GOOD' == $apireq['validationStatus'] || 'WARN' == $apireq['validationStatus'] ) ) {
                            // SUCCESS: GOOD job with GOOD or WARN validation
                            // Update job properties.
                            $jprops['file']   = $this->ao_ccss_save_file( $apireq['css'], $trule, false );
                            $jprops['jqstat'] = $apireq['status'];
                            $jprops['jrstat'] = $apireq['resultStatus'];
                            $jprops['jvstat'] = $apireq['validationStatus'];
                            $jprops['jftime'] = microtime( true );
                            $rule_update      = true;
                            do_action( 'autoptimize_action_ccss_cron_rule_saved', $jprops['rtarget'], $jprops['file'] );
                            $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful, remote id <' . $jprops['jid'] . '>, status <' . $jprops['jqstat'] . '>, file saved <' . $jprops['file'] . '>', 3 );
                        } elseif ( 'GOOD' == $apireq['resultStatus'] && ( 'BAD' == $apireq['validationStatus'] || 'SCREENSHOT_WARN_BLANK' == $apireq['validationStatus'] ) ) {
                            // SUCCESS: GOOD job with BAD or SCREENSHOT_WARN_BLANK validation
                            // Update job properties.
                            $jprops['jqstat'] = $apireq['status'];
                            $jprops['jrstat'] = $apireq['resultStatus'];
                            $jprops['jvstat'] = $apireq['validationStatus'];
                            $jprops['jftime'] = microtime( true );
                            if ( apply_filters( 'autoptimize_filter_ccss_save_review_rules', true ) ) {
                                $jprops['file']   = $this->ao_ccss_save_file( $apireq['css'], $trule, true );
                                $rule_update      = true;
                                do_action( 'autoptimize_action_ccss_cron_rule_saved', $jprops['rtarget'], $jprops['file'] );
                                $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful, remote id <' . $jprops['jid'] . '>, status <' . $jprops['jqstat'] . ', file saved <' . $jprops['file'] . '> but requires REVIEW', 3 );
                            } else {
                                $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful, remote id <' . $jprops['jid'] . '>, status <' . $jprops['jqstat'] . ', file not saved because it required REVIEW.', 3 );
                            }
                        } elseif ( 'GOOD' != $apireq['resultStatus'] && ( 'GOOD' != $apireq['validationStatus'] || 'WARN' != $apireq['validationStatus'] || 'BAD' != $apireq['validationStatus'] || 'SCREENSHOT_WARN_BLANK' != $apireq['validationStatus'] ) ) {
                            // ERROR: no GOOD, WARN or BAD results
                            // Update job properties.
                            $jprops['jqstat'] = $apireq['status'];
                            $jprops['jrstat'] = $apireq['resultStatus'];
                            $jprops['jvstat'] = $apireq['validationStatus'];
                            $jprops['jftime'] = microtime( true );
                            $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful but job FAILED, status now is <' . $jprops['jqstat'] . '>', 3 );
                            $apireq['css'] = '/* critical css removed for DEBUG logging purposes */';
                            $this->criticalcss->log( 'Job response was: ' . json_encode( $apireq ), 3 );
                        } else {
                            // UNKNOWN: unhandled JOB_DONE exception
                            // Update job properties.
                            $jprops['jqstat'] = 'JOB_UNKNOWN';
                            $jprops['jrstat'] = $apireq['resultStatus'];
                            $jprops['jvstat'] = $apireq['validationStatus'];
                            $jprops['jftime'] = microtime( true );
                            $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful but job is UNKNOWN, status now is <' . $jprops['jqstat'] . '>', 2 );
                            $apireq['css'] = '/* critical css removed for DEBUG logging purposes */';
                            $this->criticalcss->log( 'Job response was: ' . json_encode( $apireq ), 3 );
                        }
                    } elseif ( array_key_exists( 'job', $apireq ) && is_array( $apireq['job'] ) && array_key_exists( 'status', $apireq['job'] ) && ( 'JOB_FAILED' == $apireq['job']['status'] || 'STATUS_JOB_BAD' == $apireq['job']['status'] ) ) {
                        // ERROR: failed job
                        // Update job properties.
                        $jprops['jqstat'] = $apireq['job']['status'];
                        if ( $apireq['job']['error'] ) {
                            $jprops['jrstat'] = $apireq['job']['error'];
                        } else {
                            $jprops['jrstat'] = 'Baby did a bad bad thing';
                        }
                        $jprops['jvstat'] = 'NONE';
                        $jprops['jftime'] = microtime( true );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful but job FAILED, status now is <' . $jprops['jqstat'] . '>', 3 );
                    } elseif ( array_key_exists( 'error', $apireq ) && 'This css no longer exists. Please re-generate it.' == $apireq['error'] ) {
                        // ERROR: CSS doesn't exist
                        // Update job properties.
                        $jprops['jqstat'] = 'NO_CSS';
                        $jprops['jrstat'] = $apireq['error'];
                        $jprops['jvstat'] = 'NONE';
                        $jprops['jftime'] = microtime( true );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request successful but job FAILED, status now is <' . $jprops['jqstat'] . '>', 3 );
                    } else {
                        // UNKNOWN: unhandled results exception
                        // Update job properties.
                        $jprops['jqstat'] = 'JOB_UNKNOWN';
                        $jprops['jrstat'] = 'NONE';
                        $jprops['jvstat'] = 'NONE';
                        $jprops['jftime'] = microtime( true );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> result request has an UNKNOWN condition, status now is <' . $jprops['jqstat'] . '>, check log messages above for more information', 2 );
                    }

                    // Set queue update flag.
                    $update = true;
                }

                // Mark DONE jobs for removal.
                if ( 'JOB_DONE' == $jprops['jqstat'] ) {
                    $update = true;
                    $deljob = true;
                }

                // Persist updated queue object.
                if ( $update ) {
                    if ( ! $deljob ) {
                        // Update properties of a NEW or PENDING job...
                        $queue[ $path ] = $jprops;
                    } else {
                        // ...or remove the DONE job.
                        unset( $queue[ $path ] );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> is DONE and was removed from the queue', 3 );
                    }

                    // Update queue object.
                    $queue_raw = json_encode( $queue );
                    update_option( 'autoptimize_ccss_queue', $queue_raw, false );
                    $this->criticalcss->log( 'Queue updated by job id <' . $jprops['ljid'] . '>', 3 );

                    // Update target rule.
                    if ( $rule_update ) {
                        $this->ao_ccss_rule_update( $jprops['ljid'], $jprops['rtarget'], $jprops['file'], $jprops['hash'] );
                        $this->criticalcss->log( 'Job id <' . $jprops['ljid'] . '> updated the target rule <' . $jprops['rtarget'] . '>', 3 );
                    }
                } else {
                    // Or log no queue action.
                    $this->criticalcss->log( 'Nothing to do on this job', 3 );
                }

                // Break the loop if request time limit is (almost exceeded).
                if ( time() > $mt ) {
                    $this->criticalcss->log( 'The time limit of ' . $rtimelimit . ' seconds was exceeded, queue control must finish now', 3 );
                    break;
                }

                // Increment job counter.
                $jc++;
            }

            // Remove the lock file and log the queue end.
            if ( file_exists( AO_CCSS_LOCK ) ) {
                unlink( AO_CCSS_LOCK );
                $this->criticalcss->log( 'Queue control unlocked', 3 );
            }
            $this->criticalcss->log( 'Queue control finished', 3 );

            // Log that queue is locked.
        } else {
            $this->criticalcss->log( 'Queue is already running, skipping the attempt to run it again', 3 );
        }
    }

    public function ao_ccss_diff_hashes( $ljid, $hash, $hashes, $rule ) {
        // Compare job hashes
        // STEP 1: update job hashes.
        if ( 1 == count( $hashes ) ) {
            // Job with a single hash
            // Set job hash.
            $hash = $hashes[0];
            $this->criticalcss->log( 'Job id <' . $ljid . '> updated with SINGLE hash <' . $hash . '>', 3 );
        } else {
            // Job with multiple hashes
            // Loop through hashes to concatenate them.
            $nhash = '';
            foreach ( $hashes as $shash ) {
                $nhash .= $shash;
            }

            // Set job hash.
            $hash = md5( $nhash );
            $this->criticalcss->log( 'Job id <' . $ljid . '> updated with a COMPOSITE hash <' . $hash . '>', 3 );
        }

        // STEP 2: compare job to existing jobs to prevent double submission for same type+hash.
        $queue = $this->criticalcss->get_option( 'queue' );

        foreach ( $queue as $queue_item ) {
            $this->criticalcss->log( 'Comparing <' . $rule . $hash . '> with <' . $queue_item['rtarget'] . $queue_item['hash'] . '>', 3 );
            if ( $queue_item['hash'] == $hash && $queue_item['rtarget'] == $rule && in_array( $queue_item['jqstat'], array( 'JOB_QUEUED', 'JOB_ONGOING', 'JOB_DONE' ) ) ) {
                $this->criticalcss->log( 'Job id <' . $ljid . '> matches the already pending job <' . $queue_item['ljid'] . '>', 3 );
                return false;
            }
        }

        // STEP 3: compare job and existing rule (if any) hashes
        // Attach required arrays.
        $rules = $this->criticalcss->get_option( 'rules' );

        // Prepare rule variables.
        $trule = explode( '|', $rule );
        if ( is_array( $trule ) && ! empty( $trule ) && array_key_exists( $trule[1], $rules[ $trule[0] ] ) ) {
            $srule = $rules[ $trule[0] ][ $trule[1] ];
        } else {
            $srule = '';
        }

        // If hash is empty, set it to now for a "forced job".
        if ( empty( $hash ) ) {
            $hash = 'new';
            $this->criticalcss->log( 'Job id <' . $ljid . '> had no hash, assuming forced job so setting hash to new', 3 );
        }

        // Check if a MANUAL rule exist and return false.
        if ( ! empty( $srule ) && ( 0 == $srule['hash'] && 0 != $srule['file'] ) ) {
            $this->criticalcss->log( 'Job id <' . $ljid . '> matches the MANUAL rule <' . $trule[0] . '|' . $trule[1] . '>', 3 );
            return false;
        } elseif ( ! empty( $srule ) ) {
            // Check if an AUTO rule exist.
            if ( $hash === $srule['hash'] && is_file( AO_CCSS_DIR . $srule['file'] ) && 0 != filesize( AO_CCSS_DIR . $srule['file'] ) ) {
                // Check if job hash matches rule, if the CCSS file exists said file is not empty and return FALSE is so.
                $this->criticalcss->log( 'Job id <' . $ljid . '> with hash <' . $hash . '> MATCH the one in rule <' . $trule[0] . '|' . $trule[1] . '>', 3 );
                return false;
            } else {
                // Or return the new hash if they differ.
                $this->criticalcss->log( 'Job id <' . $ljid . '> with hash <' . $hash . '> DOES NOT MATCH the one in rule <' . $trule[0] . '|' . $trule[1] . '> or rule\'s CCSS file was invalid.', 3 );
                return $hash;
            }
        } else {
            // Return the hash for a job that has no rule yet.
            $this->criticalcss->log( 'Job id <' . $ljid . '> with hash <' . $hash . '> has no rule yet', 3 );
            return $hash;
        }
    }

    public function ao_ccss_api_generate( $path, $debug, $dcode ) {
        // POST jobs to criticalcss.com and return responses
        // Get key and key status.
        $key = $this->criticalcss->get_option( 'key' );
        $key_status = $this->criticalcss->get_option( 'keyst' );
        $noptimize = $this->criticalcss->get_option( 'noptimize' );

        // Prepare full URL to request.
        $site_host = get_site_url();
        $site_path = parse_url( $site_host, PHP_URL_PATH );

        if ( ! empty( $site_path ) ) {
            $site_host = str_replace( $site_path, '', $site_host );
        }

        // Logic to bind to one domain to avoid site clones of sites would
        // automatically begin spawning requests to criticalcss.com which has
        // a per domain cost.
        $domain = $this->criticalcss->get_option( 'domain' );
        if ( empty( $domain ) ) {
            // first request being done, update option to allow future requests are only allowed if from same domain.
            update_option( 'autoptimize_ccss_domain', str_rot13( $site_host ) );
        } elseif ( trim( $domain, '\'"' ) !== 'none' && parse_url( $site_host, PHP_URL_HOST ) !== parse_url( $domain, PHP_URL_HOST ) && apply_filters( 'autoptimize_filter_ccss_bind_domain', true ) ) {
            // not the same domain, log as error and return without posting to criticalcss.com.
            $this->criticalcss->log( 'Request for domain ' . $site_host . ' does not match bound domain ' . $domain . ' so not proceeding.', 2 );
            return false;
        }

        $src_url = $site_host . $path;

        // Avoid AO optimizations if required by config or avoid lazyload if lazyload is active in AO.
        if ( ! empty( $noptimize ) ) {
            $src_url .= '?ao_noptirocket=1';
        } elseif ( ( class_exists( 'autoptimizeImages', false ) && autoptimizeImages::should_lazyload_wrapper() ) || apply_filters( 'autoptimize_filter_ccss_enforce_nolazy', false ) ) {
            $src_url .= '?ao_nolazy=1';
        }

        $src_url = apply_filters( 'autoptimize_filter_ccss_cron_srcurl', $src_url );

        if ( true !== autoptimizeUtils::is_local_server( parse_url( $src_url, PHP_URL_HOST ) ) ) {
            // Initialize request body.
            $body           = array();
            $body['url']    = $src_url;
            $body['aff']    = 1;
            $body['aocssv'] = AO_CCSS_VER;

            // Prepare and add viewport size to the body if available.
            $viewport = $this->criticalcss->viewport();
            if ( ! empty( $viewport['w'] ) && ! empty( $viewport['h'] ) ) {
                $body['width']  = $viewport['w'];
                $body['height'] = $viewport['h'];
            }

            // Prepare and add forceInclude to the body if available.
            $finclude = $this->criticalcss->get_option( 'finclude' );
            $finclude = $this->ao_ccss_finclude( $finclude );
            if ( ! empty( $finclude ) ) {
                $body['forceInclude'] = $finclude;
            }

            // Add filter to allow the body array to be altered (e.g. to add customPageHeaders).
            $body = apply_filters( 'autoptimize_ccss_cron_api_generate_body', $body );

            // Body must be json and log it.
            $body = json_encode( $body, JSON_UNESCAPED_SLASHES );
            $this->criticalcss->log( 'criticalcss.com: POST generate request body is ' . $body, 3 );

            // Prepare the request.
            $url  = esc_url_raw( AO_CCSS_API . 'generate?aover=' . AO_CCSS_VER );
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
                'body'    => $body,
            );

            // Dispatch the request and store its response code.
            $req  = wp_safe_remote_post( $url, $args );
            $code = wp_remote_retrieve_response_code( $req );
            $body = json_decode( wp_remote_retrieve_body( $req ), true );

            if ( $debug && $dcode ) {
                // If queue debug is active, change response code.
                $code = $dcode;
            }

            if ( 200 == $code ) {
                // Response code is OK.
                // Workaround criticalcss.com non-RESTful reponses.
                if ( 'JOB_QUEUED' == $body['job']['status'] || 'JOB_ONGOING' == $body['job']['status'] || 'STATUS_JOB_BAD' == $body['job']['status'] ) {
                    // Log successful and return encoded request body.
                    $this->criticalcss->log( 'criticalcss.com: POST generate request for path <' . $src_url . '> replied successfully', 3 );

                    // This code also means the key is valid, so cache key status for 24h if not already cached.
                    if ( ( ! $key_status || 2 != $key_status ) && $key ) {
                        update_option( 'autoptimize_ccss_keyst', 2 );
                        $this->criticalcss->log( 'criticalcss.com: API key is valid, updating key status', 3 );
                    }

                    // Return the request body.
                    return $body;
                } else {
                    // Log successful requests with invalid reponses.
                    $this->criticalcss->log( 'criticalcss.com: POST generate request for path <' . $src_url . '> replied with code <' . $code . '> and an UNKNOWN error condition, body follows...', 2 );
                    $this->criticalcss->log( print_r( $body, true ), 2 );
                    return $body;
                }
            } else {
                // Response code is anything else.
                // Log failed request with a valid response code and return body.
                if ( $code ) {
                    $this->criticalcss->log( 'criticalcss.com: POST generate request for path <' . $src_url . '> replied with error code <' . $code . '>, body follows...', 2 );
                    $this->criticalcss->log( print_r( $body, true ), 2 );

                    if ( 401 == $code ) {
                        // If request is unauthorized, also clear key status.
                        update_option( 'autoptimize_ccss_keyst', 1 );
                        $this->criticalcss->log( 'criticalcss.com: API key is invalid, updating key status', 3 );
                    }

                    // Return the request body.
                    return $body;
                } else {
                    // Log failed request with no response and return false.
                    $this->criticalcss->log( 'criticalcss.com: POST generate request for path <' . $src_url . '> has no response, this could be a service timeout', 2 );
                    if ( is_wp_error( $req ) ) {
                        $this->criticalcss->log( $req->get_error_message(), 2 );
                    }

                    return false;
                }
            }
        } else {
            // localhost/ private network server, no CCSS possible.
            $this->criticalcss->log( 'ccss cron: job not created at ccss.com as for local server', 3 );
            return false;
        }
    }

    public function ao_ccss_api_results( $jobid, $debug, $dcode ) {
        // GET jobs from criticalcss.com and return responses
        // Get key.
        $key = $this->criticalcss->get_option( 'key' );

        // Prepare the request.
        $url  = AO_CCSS_API . 'results?resultId=' . $jobid;
        $args = array(
            'headers' => apply_filters(
                'autoptimize_ccss_cron_api_generate_headers',
                array(
                    'User-Agent'    => 'Autoptimize CriticalCSS Power-Up v' . AO_CCSS_VER,
                    'Authorization' => 'JWT ' . $key,
                    'Connection'    => 'close',
                )
            ),
        );

        // Dispatch the request and store its response code.
        $req  = wp_safe_remote_get( $url, $args );
        $code = wp_remote_retrieve_response_code( $req );
        $body = json_decode( wp_remote_retrieve_body( $req ), true );

        if ( $debug && $dcode ) {
            // If queue debug is active, change response code.
            $code = $dcode;
        }

        if ( 200 == $code ) {
            // Response code is OK.
            if ( is_array( $body ) && ( array_key_exists( 'status', $body ) || array_key_exists( 'job', $body ) ) && ( 'JOB_QUEUED' == $body['status'] || 'JOB_ONGOING' == $body['status'] || 'JOB_DONE' == $body['status'] || 'JOB_FAILED' == $body['status'] || 'JOB_UNKNOWN' == $body['status'] || 'STATUS_JOB_BAD' == $body['job']['status'] ) ) {
                // Workaround criticalcss.com non-RESTful reponses
                // Log successful and return encoded request body.
                $this->criticalcss->log( 'criticalcss.com: GET results request for remote job id <' . $jobid . '> replied successfully', 3 );
                return $body;
            } elseif ( is_array( $body ) && ( array_key_exists( 'error', $body ) && 'This css no longer exists. Please re-generate it.' == $body['error'] ) ) {
                // Handle no CSS reply
                // Log no CSS error and return encoded request body.
                $this->criticalcss->log( 'criticalcss.com: GET results request for remote job id <' . $jobid . '> replied successfully but the CSS for it does not exist anymore', 3 );
                return $body;
            } else {
                // Log failed request and return false.
                $this->criticalcss->log( 'criticalcss.com: GET results request for remote job id <' . $jobid . '> replied with code <' . $code . '> and an UNKNOWN error condition, body follows...', 2 );
                $this->criticalcss->log( print_r( $body, true ), 2 );
                return false;
            }
        } else {
            // Response code is anything else
            // Log failed request with a valid response code and return body.
            if ( $code ) {
                $this->criticalcss->log( 'criticalcss.com: GET results request for remote job id <' . $jobid . '> replied with error code <' . $code . '>, body follows...', 2 );
                $this->criticalcss->log( print_r( $body, true ), 2 );
                if ( 401 == $code ) {
                    // If request is unauthorized, also clear key status.
                    update_option( 'autoptimize_ccss_keyst', 1 );
                    $this->criticalcss->log( 'criticalcss.com: API key is invalid, updating key status', 3 );
                }

                // Return the request body.
                return $body;
            } else {
                // Log failed request with no response and return false.
                $this->criticalcss->log( 'criticalcss.com: GET results request for remote job id <' . $jobid . '> has no response, this could be a service timeout', 2 );
                return false;
            }
        }
    }

    public function ao_ccss_save_file( $ccss, $target, $review ) {
        // Save critical CSS into the filesystem and return its filename
        // Prepare review mark.
        if ( $review ) {
            $rmark = '_R';
        } else {
            $rmark = '';
        }

        // Prepare target rule, filename and content.
        $filename = false;
        $content  = $ccss;

        if ( $this->criticalcss->check_contents( $content ) ) {
            // Sanitize content, set filename and try to save file.
            $file     = AO_CCSS_DIR . 'ccss_' . md5( $ccss . $target[1] ) . $rmark . '.css';
            $status   = file_put_contents( $file, $content, LOCK_EX );
            $filename = pathinfo( $file, PATHINFO_BASENAME );
            $this->criticalcss->log( 'Critical CSS file for the rule <' . $target[0] . '|' . $target[1] . '> was saved as <' . $filename . '>, size in bytes is <' . $status . '>', 3 );

            if ( ! $status ) {
                // If file has not been saved, reset filename.
                $this->criticalcss->log( 'Critical CSS file <' . $filename . '> could not be not saved', 2 );
                $filename = false;
                return $filename;
            }
        } else {
            $this->criticalcss->log( 'Critical CSS received did not pass content check', 2 );
            return $filename;
        }

        // Remove old critical CSS if a previous one existed in the rule and if that file exists in filesystem
        // Attach required arrays.
        $rules = $this->criticalcss->get_option( 'rules' );

        // Only proceed if the rule already existed.
        if ( array_key_exists( $target[1], $rules[ $target[0] ] ) ) {
            $srule   = $rules[ $target[0] ][ $target[1] ];
            $oldfile = $srule['file'];

            if ( $oldfile && $oldfile !== $filename ) {
                $delfile = AO_CCSS_DIR . $oldfile;
                if ( file_exists( $delfile ) ) {
                    $unlinkst = unlink( $delfile );
                    if ( $unlinkst ) {
                        $this->criticalcss->log( 'A previous critical CSS file <' . $oldfile . '> was removed for the rule <' . $target[0] . '|' . $target[1] . '>', 3 );
                    }
                }
            }
        }

        // Return filename or false.
        return $filename;
    }

    public function ao_ccss_rule_update( $ljid, $srule, $file, $hash ) {
        // Update or create a rule
        // Attach required arrays.
        $rules = $this->criticalcss->get_option( 'rules' );

        // Prepare rule variables.
        $trule  = explode( '|', $srule );
        if ( array_key_exists( $trule[1], $rules[ $trule[0] ] ) ) {
            $rule = $rules[ $trule[0] ][ $trule[1] ];
        } else {
            $rule = array();
        }
        $action = false;
        $rtype  = '';

        if ( is_array( $rule ) && array_key_exists( 'hash', $rule ) && 0 === $rule['hash'] && array_key_exists( 'file', $rule ) && 0 !== $rule['file'] ) {
            // manual rule, don't ever overwrite.
            $action = 'NOT UPDATED';
            $rtype  = 'MANUAL';
        } elseif ( is_array( $rule ) && array_key_exists( 'hash', $rule ) && 0 === $rule['hash'] && array_key_exists( 'file', $rule ) && 0 === $rule['file'] ) {
            // If this is an user created AUTO rule with no hash and file yet, update its hash and filename
            // Set rule hash, file and action flag.
            $rule['hash'] = $hash;
            $rule['file'] = $file;
            $action       = 'UPDATED';
            $rtype        = 'AUTO';
        } elseif ( is_array( $rule ) && array_key_exists( 'hash', $rule ) && 0 !== $rule['hash'] && ctype_alnum( $rule['hash'] ) ) {
            // If this is an genuine AUTO rule, update its hash and filename
            // Set rule hash, file and action flag.
            $rule['hash'] = $hash;
            $rule['file'] = $file;
            $action       = 'UPDATED';
            $rtype        = 'AUTO';
        } else {
            // If rule doesn't exist, create an AUTO rule
            // AUTO rules were only for types, but will now also work for paths.
            if ( ( 'types' == $trule[0] || 'paths' == $trule[0] ) && ! empty( $trule[1] ) ) {
                // Set rule hash and file and action flag.
                $rule['hash'] = $hash;
                $rule['file'] = $file;
                $action       = 'CREATED';
                $rtype        = 'AUTO';
            } else {
                // Log that no rule was created.
                $this->criticalcss->log( 'Exception, no AUTO rule created', 3 );
            }
        }

        if ( $action ) {
            // If a rule creation/update is required, persist updated rules object.
            $rules[ $trule[0] ][ $trule[1] ] = $rule;
            $rules_raw = json_encode( $rules );
            update_option( 'autoptimize_ccss_rules', $rules_raw );
            $this->criticalcss->flush_options();
            $this->criticalcss->log( 'Target rule <' . $srule . '> of type <' . $rtype . '> was ' . $action . ' for job id <' . $ljid . '>', 3 );
        } else {
            $this->criticalcss->log( 'No rule action required', 3 );
        }
    }

    function ao_ccss_finclude( $finclude_raw ) {
        // Prepare forceInclude object.
        if ( ! empty( $finclude_raw ) ) {
            // If there are any content
            // Convert raw string into arra and initialize the returning object.
            $fincludes = explode( ',', $finclude_raw );
            $finclude  = array();

            // Interacts over every rule.
            $i = 0;
            foreach ( $fincludes as $include ) {
                // Trim leading and trailing whitespaces.
                $include = trim( $include );

                if ( substr( $include, 0, 2 ) === '//' ) {
                    // Regex rule
                    // Format value as required.
                    $include = str_replace( '//', '/', $include );
                    $include = $include . '/i';

                    // Store regex object.
                    $finclude[ $i ]['type']  = 'RegExp';
                    $finclude[ $i ]['value'] = $include;
                } else {
                    // Simple value rule.
                    $finclude[ $i ]['value'] = $include;
                }

                $i++;
            }

            // Return forceInclude object.
            return $finclude;
        } else {
            // Or just return false if empty.
            return false;
        }
    }

    public function ao_ccss_cleaning() {
        // Perform plugin maintenance
        // Truncate log file >= 1MB .
        if ( file_exists( AO_CCSS_LOG ) ) {
            if ( filesize( AO_CCSS_LOG ) >= 1048576 ) {
                $logfile = fopen( AO_CCSS_LOG, 'w' );
                fclose( $logfile );
            }
        }

        // Remove lock file.
        if ( file_exists( AO_CCSS_LOCK ) ) {
            unlink( AO_CCSS_LOCK );
        }

        // Make sure queue processing is scheduled, recreate if not.
        if ( ! wp_next_scheduled( 'ao_ccss_queue' ) ) {
            wp_schedule_event( time(), apply_filters( 'ao_ccss_queue_schedule', 'ao_ccss' ), 'ao_ccss_queue' );
        }

        // Queue cleaning.
        $queue = $this->criticalcss->get_option( 'queue' );

        $queue_purge_threshold = 100;
        $queue_purge_age       = 24 * 60 * 60;
        $queue_length          = count( $queue );
        $timestamp_yesterday   = microtime( true ) - $queue_purge_age;
        $remove_old_new        = false;
        $queue_altered         = false;

        if ( $queue_length > $queue_purge_threshold ) {
            $remove_old_new = true;
        }

        foreach ( $queue as $path => $job ) {
            if ( ( $remove_old_new && 'NEW' == $job['jqstat'] && $job['jctime'] < $timestamp_yesterday ) || in_array( $job['jqstat'], array( 'JOB_FAILED', 'STATUS_JOB_BAD', 'NO_CSS', 'NO_RESPONSE' ) ) ) {
                unset( $queue[ $path ] );
                $queue_altered = true;
            }
        }

        // save queue to options!
        if ( $queue_altered ) {
            $queue_raw = json_encode( $queue );
            update_option( 'autoptimize_ccss_queue', $queue_raw, false );
            $this->criticalcss->log( 'Queue cleaning done.', 3 );
        }

        // re-check key if invalid.
        $keyst = $this->criticalcss->get_option( 'keyst' );
        if ( 1 == $keyst ) {
            $this->ao_ccss_api_generate( '', '', '' );
        }
    }
}
