<?php
/**
 * Critical CSS settings AJAX logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCriticalCSSSettingsAjax {
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
        // add filters.
        add_action( 'wp_ajax_fetch_critcss', array( $this, 'critcss_fetch_callback' ) );
        add_action( 'wp_ajax_save_critcss', array( $this, 'critcss_save_callback' ) );
        add_action( 'wp_ajax_rm_critcss', array( $this, 'critcss_rm_callback' ) );
        add_action( 'wp_ajax_rm_critcss_all', array( $this, 'critcss_rm_all_callback' ) );
        add_action( 'wp_ajax_ao_ccss_export', array( $this, 'ao_ccss_export_callback' ) );
        add_action( 'wp_ajax_ao_ccss_import', array( $this, 'ao_ccss_import_callback' ) );
        add_action( 'wp_ajax_ao_ccss_queuerunner', array( $this, 'ao_ccss_queuerunner_callback' ) );
        add_action( 'wp_ajax_ao_ccss_saverules', array( $this, 'ao_ccss_saverules_callback' ) );
    }

    public function critcss_fetch_callback() {
        // Ajax handler to obtain a critical CSS file from the filesystem.
        // Check referer.
        check_ajax_referer( 'fetch_critcss_nonce', 'critcss_fetch_nonce' );

        // Initialize error flag.
        $error = true;

        // Allow no content for MANUAL rules (as they may not exist just yet).
        if ( current_user_can( 'manage_options' ) && empty( $_POST['critcssfile'] ) ) {
            $content = '';
            $error   = false;
        } elseif ( current_user_can( 'manage_options' ) && $this->critcss_check_filename( $_POST['critcssfile'] ) ) {
            // Or check user permissios and filename.
            // Set file path and obtain its content.
            $critcssfile = AO_CCSS_DIR . strip_tags( $_POST['critcssfile'] );
            if ( file_exists( $critcssfile ) ) {
                $content = file_get_contents( $critcssfile );
                $error   = false;
            }
        }

        // Prepare response.
        if ( $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error reading file ' . $critcssfile . '.';
        } else {
            $response['code']   = '200';
            $response['string'] = $content;
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function critcss_save_callback() {
        $error    = false;
        $status   = false;
        $response = array();

        // Ajax handler to write a critical CSS to the filesystem
        // Check referer.
        check_ajax_referer( 'save_critcss_nonce', 'critcss_save_nonce' );

        // Allow empty contents for MANUAL rules (as they are fetched later).
        if ( current_user_can( 'manage_options' ) && empty( $_POST['critcssfile'] ) ) {
            $critcssfile = false;
            $status      = true;
        } elseif ( current_user_can( 'manage_options' ) && $this->critcss_check_filename( $_POST['critcssfile'] ) ) {
            // Or check user permissios and filename
            // Set critical CSS content.
            $critcsscontents = stripslashes( $_POST['critcsscontents'] );

            // If there is content and it's valid, write the file.
            if ( $critcsscontents && $this->criticalcss->check_contents( $critcsscontents ) ) {
                // Set file path and status.
                $critcssfile = AO_CCSS_DIR . strip_tags( $_POST['critcssfile'] );
                $status      = file_put_contents( $critcssfile, $critcsscontents, LOCK_EX );
                // Or set as error.
            } else {
                $error       = true;
                $critcssfile = 'CCSS content not acceptable.';
            }
            // Or just set an error.
        } else {
            $error       = true;
            $critcssfile = 'Not allowed or problem with CCSS filename.';
        }

        // Prepare response.
        if ( ! $status || $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error saving file ' . $critcssfile . '.';
        } else {
            $response['code'] = '200';
            if ( $critcssfile ) {
                $response['string'] = 'File ' . $critcssfile . ' saved.';
            } else {
                $response['string'] = 'Empty content does not need to be saved.';
            }
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function critcss_rm_callback() {
        // Ajax handler to delete a critical CSS from the filesystem
        // Check referer.
        check_ajax_referer( 'rm_critcss_nonce', 'critcss_rm_nonce' );

        // Initialize error and status flags.
        $error  = true;
        $status = false;

        // Allow no file for MANUAL rules (as they may not exist just yet).
        if ( current_user_can( 'manage_options' ) && empty( $_POST['critcssfile'] ) ) {
            $error = false;
        } elseif ( current_user_can( 'manage_options' ) && $this->critcss_check_filename( $_POST['critcssfile'] ) ) {
            // Or check user permissios and filename
            // Set file path and delete it.
            $critcssfile = AO_CCSS_DIR . strip_tags( $_POST['critcssfile'] );
            if ( file_exists( $critcssfile ) ) {
                $status = unlink( $critcssfile );
                $error  = false;
            }
        }

        // Prepare response.
        if ( $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error removing file ' . $critcssfile . '.';
        } else {
            $response['code'] = '200';
            if ( $status ) {
                $response['string'] = 'File ' . $critcssfile . ' removed.';
            } else {
                $response['string'] = 'No file to be removed.';
            }
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function critcss_rm_all_callback() {
        // Ajax handler to delete a critical CSS from the filesystem
        // Check referer.
        check_ajax_referer( 'rm_critcss_all_nonce', 'critcss_rm_all_nonce' );

        // Initialize error and status flags.
        $error  = true;
        $status = false;

        // Remove all ccss files on filesystem.
        if ( current_user_can( 'manage_options' ) ) {
            if ( file_exists( AO_CCSS_DIR ) && is_dir( AO_CCSS_DIR ) ) {
                array_map( 'unlink', glob( AO_CCSS_DIR . 'ccss_*.css', GLOB_BRACE ) );
                $error  = false;
                $status = true;
            }
        }

        // Prepare response.
        if ( $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error removing all critical CSS files.';
        } else {
            $response['code'] = '200';
            if ( $status ) {
                $response['string'] = 'Critical CSS Files removed.';
            } else {
                $response['string'] = 'No file removed.';
            }
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function ao_ccss_export_callback() {
        // Ajax handler export settings
        // Check referer.
        check_ajax_referer( 'ao_ccss_export_nonce', 'ao_ccss_export_nonce' );

        if ( ! class_exists( 'ZipArchive' ) ) {
            $response['code'] = '500';
            $response['msg']  = 'PHP ZipArchive not present, cannot create zipfile';
            echo json_encode( $response );
            wp_die();
        }

        // Init array, get options and prepare the raw object.
        $settings                        = array();

        // CCSS settings.
        $settings['ccss']['rules']       = get_option( 'autoptimize_ccss_rules' );
        $settings['ccss']['additional']  = get_option( 'autoptimize_ccss_additional' );
        $settings['ccss']['viewport']    = get_option( 'autoptimize_ccss_viewport' );
        $settings['ccss']['finclude']    = get_option( 'autoptimize_ccss_finclude' );
        $settings['ccss']['rtimelimit']  = get_option( 'autoptimize_ccss_rtimelimit' );
        $settings['ccss']['noptimize']   = get_option( 'autoptimize_ccss_noptimize' );
        $settings['ccss']['debug']       = get_option( 'autoptimize_ccss_debug' );
        $settings['ccss']['key']         = get_option( 'autoptimize_ccss_key' );
        $settings['ccss']['deferjquery'] = get_option( 'autoptimize_ccss_deferjquery' );
        $settings['ccss']['domain']      = get_option( 'autoptimize_ccss_domain' );
        $settings['ccss']['forcepath']   = get_option( 'autoptimize_ccss_forcepath' );
        $settings['ccss']['loggedin']    = get_option( 'autoptimize_ccss_loggedin' );
        $settings['ccss']['rlimit']      = get_option( 'autoptimize_ccss_rlimit' );
        $settings['ccss']['unloadccss']  = get_option( 'autoptimize_ccss_unloadccss' );

        // JS settings.
        $settings['js']['root']                = get_option( 'autoptimize_js' );
        $settings['js']['aggregate']           = get_option( 'autoptimize_js_aggregate' );
        $settings['js']['defer_not_aggregate'] = get_option( 'autoptimize_js_defer_not_aggregate' );
        $settings['js']['defer_inline']        = get_option( 'autoptimize_js_defer_inline' );
        $settings['js']['exclude']             = get_option( 'autoptimize_js_exclude' );
        $settings['js']['forcehead']           = get_option( 'autoptimize_js_forcehead' );
        $settings['js']['justhead']            = get_option( 'autoptimize_js_justhead' );
        $settings['js']['trycatch']            = get_option( 'autoptimize_js_trycatch' );
        $settings['js']['include_inline']      = get_option( 'autoptimize_js_include_inline' );

        // CSS settings.
        $settings['css']['root']           = get_option( 'autoptimize_css' );
        $settings['css']['aggregate']      = get_option( 'autoptimize_css_aggregate' );
        $settings['css']['datauris']       = get_option( 'autoptimize_css_datauris' );
        $settings['css']['justhead']       = get_option( 'autoptimize_css_justhead' );
        $settings['css']['defer']          = get_option( 'autoptimize_css_defer' );
        $settings['css']['defer_inline']   = get_option( 'autoptimize_css_defer_inline' );
        $settings['css']['inline']         = get_option( 'autoptimize_css_inline' );
        $settings['css']['exclude']        = get_option( 'autoptimize_css_exclude' );
        $settings['css']['include_inline'] = get_option( 'autoptimize_css_include_inline' );

        // Others.
        $settings['other']['autoptimize_imgopt_settings']         = get_option( 'autoptimize_imgopt_settings' );
        $settings['other']['autoptimize_extra_settings']          = get_option( 'autoptimize_extra_settings' );
        $settings['other']['autoptimize_cache_fallback']          = get_option( 'autoptimize_cache_fallback' );
        $settings['other']['autoptimize_cache_nogzip']            = get_option( 'autoptimize_cache_nogzip' );
        $settings['other']['autoptimize_cdn_url']                 = get_option( 'autoptimize_cdn_url' );
        $settings['other']['autoptimize_enable_meta_ao_settings'] = get_option( 'autoptimize_enable_meta_ao_settings' );
        $settings['other']['autoptimize_enable_site_config']      = get_option( 'autoptimize_enable_site_config' );
        $settings['other']['autoptimize_html']                    = get_option( 'autoptimize_html' );
        $settings['other']['autoptimize_html_keepcomments']       = get_option( 'autoptimize_html_keepcomments' );
        $settings['other']['autoptimize_minify_excluded']         = get_option( 'autoptimize_minify_excluded' );
        $settings['other']['autoptimize_optimize_checkout']       = get_option( 'autoptimize_optimize_checkout' );
        $settings['other']['autoptimize_optimize_logged']         = get_option( 'autoptimize_optimize_logged' );

        if ( defined( 'AO_PRO_VERSION' ) ) {
            $settings['pro']['boosters']  = get_option( 'autoptimize_pro_boosters' );
            $settings['pro']['pagecache'] = get_option( 'autoptimize_pro_pagecache' );
        }

        // Initialize error flag.
        $error = true;

        // Check user permissions.
        if ( current_user_can( 'manage_options' ) ) {
            // Prepare settings file path and content.
            $exportfile = AO_CCSS_DIR . 'settings.json';
            $contents   = json_encode( $settings );
            $status     = file_put_contents( $exportfile, $contents, LOCK_EX );
            $error      = false;
        }

        // Prepare archive.
        $zipfile = AO_CCSS_DIR . str_replace( array( '.', '/' ), '_', parse_url( AUTOPTIMIZE_WP_SITE_URL, PHP_URL_HOST ) ) . '_' . date( 'Ymd-H\hi' ) . '_ao_ccss_settings.zip'; // @codingStandardsIgnoreLine
        $file    = pathinfo( $zipfile, PATHINFO_BASENAME );
        $zip     = new ZipArchive();
        $ret     = $zip->open( $zipfile, ZipArchive::CREATE );
        if ( true !== $ret ) {
            $error = true;
        } else {
            $zip->addFile( AO_CCSS_DIR . 'settings.json', 'settings.json' );
            if ( file_exists( AO_CCSS_DIR . 'queue.json' ) ) {
                $zip->addFile( AO_CCSS_DIR . 'queue.json', 'queue.json' );
            }
            $options = array(
                'add_path'        => './',
                'remove_all_path' => true,
            );
            $zip->addGlob( AO_CCSS_DIR . '*.css', 0, $options );
            $zip->close();
        }

        // settings.json has been added to zipfile, so can be removed now.
        if ( file_exists( $exportfile ) ) {
            unlink( $exportfile );
        }

        // Prepare response.
        if ( ! $status || $error ) {
            $response['code'] = '500';
            $response['msg']  = 'Error saving file ' . $file . ', code: ' . $ret;
        } else {
            $response['code'] = '200';
            $response['msg']  = 'File ' . $file . ' saved.';
            $response['file'] = $file;
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function ao_ccss_import_callback() {
        // Ajax handler import settings
        // Check referer.
        check_ajax_referer( 'ao_ccss_import_nonce', 'ao_ccss_import_nonce' );

        // Initialize error flag.
        $error = false;

        // Process an uploaded file with no errors.
        if ( current_user_can( 'manage_options' ) && ! $_FILES['file']['error'] && $_FILES['file']['size'] < 500001 && strpos( $_FILES['file']['name'], '.zip' ) === strlen( $_FILES['file']['name'] ) - 4 ) {
            // create tmp dir with hard guess name in AO_CCSS_DIR.
            $_secret_dir     = wp_hash( uniqid( md5( AUTOPTIMIZE_CACHE_URL ), true ) );
            $_import_tmp_dir = trailingslashit( AO_CCSS_DIR . $_secret_dir );
            mkdir( $_import_tmp_dir, 0774, true );

            // Save file to that tmp directory but give it our own name to prevent directory traversal risks when using original name.
            $zipfile = $_import_tmp_dir . uniqid( 'import_settings-', true ) . '.zip';
            move_uploaded_file( $_FILES['file']['tmp_name'], $zipfile );

            // Extract archive in the tmp directory.
            $zip = new ZipArchive;
            if ( $zip->open( $zipfile ) === true ) {
                // loop through all files in the zipfile.
                for ( $i = 0; $i < $zip->numFiles; $i++ ) { // @codingStandardsIgnoreLine
                    // but only extract known good files.
                    if ( preg_match( '/^settings\.json$|^\.\/ccss_[a-z0-9]{32}\.css$/', $zip->getNameIndex( $i ) ) > 0 ) {
                        $zip->extractTo( AO_CCSS_DIR, $zip->getNameIndex( $i ) );
                    }
                }
                $zip->close();
            } else {
                $error = 'could not extract';
            }

            // and remove temp. dir with all contents (the import-zipfile).
            $this->rrmdir( $_import_tmp_dir );

            if ( ! $error ) {
                // Archive extraction ok, continue importing settings from AO_CCSS_DIR.
                // Settings file.
                $importfile = AO_CCSS_DIR . 'settings.json';

                if ( file_exists( $importfile ) ) {
                    // Get settings and turn them into an object.
                    $settings = json_decode( file_get_contents( $importfile ), true );

                    // Update options from settings, but only for known options.
                    // CCSS.
                    foreach ( array( 'rules', 'additional', 'viewport', 'finclude', 'rtimelimit', 'noptimize', 'debug', 'key', 'deferjquery', 'domain', 'forcepath', 'loggedin', 'rlimit', 'unloadccss' ) as $ccss_setting ) {
                        if ( false === array_key_exists( 'ccss', $settings ) || false === array_key_exists( $ccss_setting, $settings['ccss'] ) ) {
                            continue;
                        } else {
                            update_option( 'autoptimize_ccss_' . $ccss_setting, autoptimizeUtils::strip_tags_array( $settings['ccss'][ $ccss_setting ] ) );
                        }
                    }

                    // JS.
                    foreach ( array( 'root', 'aggregate', 'defer_not_aggregate', 'defer_inline', 'exclude', 'forcehead', 'trycatch', 'include_inline' ) as $js_setting ) {
                        if ( false === array_key_exists( 'js', $settings ) || false === array_key_exists( $js_setting, $settings['js'] ) ) {
                            continue;
                        } else if ( 'root' === $js_setting ) {
                            update_option( 'autoptimize_js', $settings['js']['root'] );
                        } else {
                            update_option( 'autoptimize_js_' . $js_setting, $settings['js'][ $js_setting ] );
                        }
                    }

                    // CSS.
                    foreach ( array( 'root', 'aggregate', 'datauris', 'justhead', 'defer', 'defer_inline', 'inline', 'exclude', 'include_inline' ) as $css_setting ) {
                        if ( false === array_key_exists( 'css', $settings ) || false === array_key_exists( $css_setting, $settings['css'] ) ) {
                            continue;
                        } else if ( 'root' === $css_setting ) {
                            update_option( 'autoptimize_css', $settings['css']['root'] );
                        } else {
                            update_option( 'autoptimize_css_' . $css_setting, $settings['css'][ $css_setting ] );
                        }
                    }

                    // Other.
                    foreach ( array( 'autoptimize_imgopt_settings', 'autoptimize_extra_settings', 'autoptimize_cache_fallback', 'autoptimize_cache_nogzip', 'autoptimize_cdn_url', 'autoptimize_enable_meta_ao_settings', 'autoptimize_enable_site_config', 'autoptimize_html', 'autoptimize_html_keepcomments', 'autoptimize_minify_excluded', 'autoptimize_optimize_checkout', 'autoptimize_optimize_logged' ) as $other_setting ) {
                        if ( false === array_key_exists( 'other', $settings ) || false === array_key_exists( $other_setting, $settings['other'] ) ) {
                            continue;
                        } else {
                            update_option( $other_setting, $settings['other'][ $other_setting ] );
                        }
                    }

                    // AO Pro.
                    if ( defined( 'AO_PRO_VERSION' ) && array_key_exists( 'pro', $settings ) ) {
                        update_option( 'autoptimize_pro_boosters', $settings['pro']['boosters'] );
                        update_option( 'autoptimize_pro_pagecache', $settings['pro']['pagecache'] );
                    }

                    // settings.json has been imported, so can be removed now.
                    if ( file_exists( $importfile ) ) {
                        unlink( $importfile );
                    }
                } else {
                    // Settings file doesn't exist, update error flag.
                    $error = 'settings file does not exist';
                }
            }
        } else {
            $error = 'file could not be saved';
        }

        // Prepare response.
        if ( $error ) {
            $response['code'] = '500';
            $response['msg']  = 'Error importing settings: ' . $error;
        } else {
            $response['code'] = '200';
            $response['msg']  = 'Settings imported successfully';
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function ao_ccss_queuerunner_callback() {
        check_ajax_referer( 'ao_ccss_queuerunner_nonce', 'ao_ccss_queuerunner_nonce' );

        // Process an uploaded file with no errors.
        if ( current_user_can( 'manage_options' ) ) {
            if ( ! file_exists( AO_CCSS_LOCK ) ) {
                $ccss_cron = new autoptimizeCriticalCSSCron();
                $ccss_cron->ao_ccss_queue_control();
                $response['code'] = '200';
                $response['msg']  = 'Queue processing done';
            } else {
                $response['code'] = '302';
                $response['msg']  = 'Lock file found';
            }
        } else {
            $response['code'] = '500';
            $response['msg']  = 'Not allowed';
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function ao_ccss_saverules_callback() {
        check_ajax_referer( 'ao_ccss_saverules_nonce', 'ao_ccss_saverules_nonce' );

        // save rules over AJAX, too many users forget to press "save changes".
        if ( current_user_can( 'manage_options' ) ) {
            if ( array_key_exists( 'critcssrules', $_POST ) ) {
                $rules = stripslashes( $_POST['critcssrules'] ); // ugly, but seems correct as per https://developer.wordpress.org/reference/functions/stripslashes_deep/#comment-1045 .
                if ( ! empty( $rules ) ) {
                    $_unsafe_rules_array = json_decode( wp_strip_all_tags( $rules ), true );
                    if ( ! empty( $_unsafe_rules_array ) && is_array( $_unsafe_rules_array ) ) {
                        $_safe_rules_array = array();
                        if ( array_key_exists( 'paths', $_unsafe_rules_array ) ) {
                            $_safe_rules_array['paths'] = $_unsafe_rules_array['paths'];
                        }
                        if ( array_key_exists( 'types', $_unsafe_rules_array ) ) {
                            $_safe_rules_array['types'] = $_unsafe_rules_array['types'];
                        }
                        $_safe_rules = json_encode( $_safe_rules_array, JSON_FORCE_OBJECT );
                        if ( ! empty( $_safe_rules ) ) {
                            update_option( 'autoptimize_ccss_rules', $_safe_rules );
                            $response['code'] = '200';
                            $response['msg']  = 'Rules saved';
                        } else {
                            $_error = 'Could not auto-save rules (safe rules empty)';
                        }
                    } else {
                        $_error = 'Could not auto-save rules (rules could not be json_decoded)';
                    }
                } else {
                    $_error = 'Could not auto-save rules (rules empty)';
                }
            } else {
                $_error = 'Could not auto-save rules (rules not in $_POST)';
            }
        } else {
            $_error = 'Not allowed';
        }

        if ( ! isset( $response ) && $_error ) {
            $response['code'] = '500';
            $response['msg']  = $_error;
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }

    public function critcss_check_filename( $filename ) {
        // Try to avoid directory traversal when reading/writing/deleting critical CSS files.
        if ( strpos( $filename, 'ccss_' ) !== 0 ) {
            return false;
        } elseif ( substr( $filename, -4, 4 ) !== '.css' ) {
            return false;
        } elseif ( sanitize_file_name( $filename ) !== $filename ) {
            // Use WordPress core's sanitize_file_name to see if anything fishy is going on.
            return false;
        } else {
            return true;
        }
    }

    public function rrmdir( $path ) {
        // recursively remove a directory as found on
        // https://andy-carter.com/blog/recursively-remove-a-directory-in-php.
        $files = glob( $path . '/*' );
        foreach ( $files as $file ) {
            is_dir( $file ) ? $this->rrmdir( $file ) : unlink( $file );
        }
        rmdir( $path );

        return;
    }
}
