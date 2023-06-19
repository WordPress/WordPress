<?php
/**
 * Critical CSS Options page.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCriticalCSSSettings {
    /**
     * Options.
     *
     * @var bool
     */
    private $settings_screen_do_remote_http = true;

    /**
     * Critical CSS object.
     *
     * @var object
     */
    protected $criticalcss;

    public function __construct() {
        $this->criticalcss = autoptimize()->criticalcss();
        $this->settings_screen_do_remote_http = apply_filters( 'autoptimize_settingsscreen_remotehttp', $this->settings_screen_do_remote_http );
        $this->run();
    }

    protected function enabled()
    {
        return apply_filters( 'autoptimize_filter_show_criticalcss_tabs', true );
    }

    public function run()
    {
        if ( $this->enabled() ) {
            add_filter( 'autoptimize_filter_settingsscreen_tabs', array( $this, 'add_critcss_tabs' ), 10, 1 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

            if ( $this->is_multisite_network_admin() && autoptimizeOptionWrapper::is_ao_active_for_network() ) {
                add_action( 'network_admin_menu', array( $this, 'add_critcss_admin_menu' ) );
            } else {
                add_action( 'admin_menu', array( $this, 'add_critcss_admin_menu' ) );
            }

            $criticalcss_ajax = new autoptimizeCriticalCSSSettingsAjax();

            // if debug logging is off but the file is present, then remove the debug log file.
            if ( empty( $this->criticalcss->get_option( 'debug' ) ) && file_exists( AO_CCSS_LOG ) ) {
                unlink( AO_CCSS_LOG );
            }
        }
    }

    public function add_critcss_tabs( $in )
    {
        $in = array_merge( $in, array( 'ao_critcss' => apply_filters( 'autoptimize_filter_ccss_tab_text', '⚡ ' . __( 'Critical CSS', 'autoptimize' ) ) ) );

        return $in;
    }

    public function add_critcss_admin_menu()
    {
        // Register settings.
        register_setting( 'ao_ccss_options_group', 'autoptimize_css_defer_inline' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_rules' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_additional' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_queue' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_viewport' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_finclude' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_rtimelimit' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_noptimize' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_debug' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_key' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_keyst' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_loggedin' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_forcepath' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_deferjquery' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_domain' );
        register_setting( 'ao_ccss_options_group', 'autoptimize_ccss_unloadccss' );

        // And add submenu-page.
        add_submenu_page( '', 'Critical CSS', 'Critical CSS', 'manage_options', 'ao_critcss', array( $this, 'ao_criticalcsssettings_page' ) );
    }

    public function admin_assets( $hook ) {
        // Return if plugin is not hooked.
        if ( 'settings_page_ao_critcss' != $hook && 'admin_page_ao_critcss' != $hook ) {
            return;
        }

        // Stylesheets to add.
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'ao-tablesorter', plugins_url( 'critcss-inc/css/ao-tablesorter/style.css', __FILE__ ) );
        wp_enqueue_style( 'ao-ccss-admin-css', plugins_url( 'critcss-inc/css/admin_styles.css', __FILE__ ) );

        // Scripts to add.
        wp_enqueue_script( 'jquery-ui-dialog', array( 'jquery' ) );
        wp_enqueue_script( 'md5', plugins_url( 'critcss-inc/js/md5.min.js', __FILE__ ), null, null, true );
        wp_enqueue_script( 'tablesorter', plugins_url( 'critcss-inc/js/jquery.tablesorter.min.js', __FILE__ ), array( 'jquery' ), null, true );
        wp_enqueue_script( 'ao-ccss-admin-license', plugins_url( 'critcss-inc/js/admin_settings.js', __FILE__ ), array( 'jquery' ), null, true );
    }

    public function ao_criticalcsssettings_page()
    {
        // these are not OO yet, simply require for now.
        require_once( 'critcss-inc/admin_settings_rules.php' );
        require_once( 'critcss-inc/admin_settings_queue.php' );
        require_once( 'critcss-inc/admin_settings_key.php' );
        require_once( 'critcss-inc/admin_settings_adv.php' );
        require_once( 'critcss-inc/admin_settings_explain.php' );

        $ao_ccss_key           = $this->criticalcss->get_option( 'key' );
        $ao_ccss_keyst         = $this->criticalcss->get_option( 'keyst' );
        $ao_css_defer          = $this->criticalcss->get_option( 'css_defer' );
        $ao_ccss_deferjquery   = $this->criticalcss->get_option( 'deferjquery' );
        $ao_ccss_queue         = $this->criticalcss->get_option( 'queue' );
        $ao_ccss_rules         = $this->criticalcss->get_option( 'rules' );
        $ao_ccss_servicestatus = $this->criticalcss->get_option( 'servicestatus' );
        $ao_ccss_finclude      = $this->criticalcss->get_option( 'finclude' );
        $ao_ccss_rtimelimit    = $this->criticalcss->get_option( 'rtimelimit' );
        $ao_ccss_debug         = $this->criticalcss->get_option( 'debug' );
        $ao_ccss_noptimize     = $this->criticalcss->get_option( 'noptimize' );
        $ao_css_defer_inline   = $this->criticalcss->get_option( 'css_defer_inline' );
        $ao_ccss_loggedin      = $this->criticalcss->get_option( 'loggedin' );
        $ao_ccss_forcepath     = $this->criticalcss->get_option( 'forcepath' );
        $ao_ccss_domain        = $this->criticalcss->get_option( 'domain' );
        ?>
        <script>document.title = "Autoptimize: <?php _e( 'Critical CSS', 'autoptimize' ); ?> " + document.title;</script>
        <div class="wrap">
            <div id="autoptimize_main">
                <div id="ao_title_and_button">
                    <h1><?php apply_filters( 'autoptimize_filter_settings_is_pro', false ) ? _e( 'Autoptimize Pro Settings', 'autoptimize' ) : _e( 'Autoptimize Settings', 'autoptimize' ); ?></h1>
                </div>

                <?php
                // Print AO settings tabs.
                echo autoptimizeConfig::ao_admin_tabs();

                if ( autoptimizeUtils::is_local_server() && isset( $ao_ccss_key ) ) { ?>
                    <div class="notice-warning notice"><p>
                    <?php
                    echo __( 'The Critical CSS service does not work on locally hosted sites or when the server is on a private network.', 'autoptimize' );
                    ?>
                    </p></div>
                <?php }

                $mkdirresult = $this->criticalcss->create_ao_ccss_dir();

                // Warn if we could not create those files.
                if ( ( true !== $mkdirresult ) ) {
                    ?>
                    <div class="notice-error notice"><p>
                    <?php
                    _e( 'Could not create the required directory. Make sure the webserver can write to the wp-content/uploads directory.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // Check if CSS optimization is on.
                if ( 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_css' ) || 'on' !== autoptimizeOptionWrapper::get_option( 'autoptimize_css_defer' ) ) {
                    ?>
                    <div class="notice-info notice"><p>
                    <?php
                    _e( 'To be able to use Critical CSS you will have to enable CSS optimization and make sure "eliminate render-blocking CSS" is active on the main Autoptimize settings page.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // Check for "inline & defer CSS" being active in Autoptimize.
                if ( ! empty( $ao_ccss_key ) && ! $ao_css_defer && empty( $ao_ccss_keyst ) ) {
                        // no keystate so likely in activation-process of CCSS, let's enable "inline & defer CSS" immediately to make things easier!
                        autoptimizeOptionWrapper::update_option( 'autoptimize_css_defer', 'on' );
                    ?>
                        <div class="notice-info notice"><p>
                        <?php
                        _e( 'The "Eliminate render-blocking CSS" option was activated to allow critical CSS to be used.', 'autoptimize' );
                        ?>
                        </p></div>
                        <?php
                }

                // check if WordPress cron is disabled and warn if so.
                if ( ! empty( $ao_ccss_key ) && defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON && PAnD::is_admin_notice_active( 'i-know-about-disable-cron-forever' ) ) {
                    ?>
                    <div data-dismissible="i-know-about-disable-cron-forever" class="notice-warning notice is-dismissible"><p>
                    <?php
                    _e( 'WordPress cron (for task scheduling) seems to be disabled. Have a look at <a href="https://wordpress.org/plugins/autoptimize-criticalcss/faq/" target="_blank">the FAQ</a> or the info in the Job Queue instructions if all jobs remain in "N" status and no rules are created.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // check if defer jQuery is active and warn if so.
                if ( 1 == $ao_ccss_deferjquery && PAnD::is_admin_notice_active( 'i-know-about-defer-inline-forever' ) ) {
                    ?>
                    <div data-dismissible="i-know-about-defer-inline-forever" class="notice-warning notice is-dismissible"><p>
                    <?php
                    _e( 'You have "defer jQuery and other non-aggregated JS-files" active (under Advanced Settings), but that functionality is deprecated and will be removed in the next major version of Autoptimize. Consider using the new "Do not aggregate but defer" and "Also defer inline JS" options on the main settings page instead.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // warn if it looks as though the queue processing job looks isn't running
                // but store result in transient as to not to have to go through 2 arrays each and every time.
                $_warn_cron = get_transient( 'ao_ccss_cronwarning' );
                if ( ! empty( $ao_ccss_key ) && false === $_warn_cron ) {
                    $_jobs_all_new         = true;
                    $_oldest_job_timestamp = microtime( true ); // now.
                    $_jobs_too_old         = true;

                    // go over queue array.
                    if ( empty( $ao_ccss_queue ) ) {
                        // no jobs, then no warning.
                        $_jobs_all_new = false;
                    } else {
                        foreach ( $ao_ccss_queue as $job ) {
                            if ( $job['jctime'] < $_oldest_job_timestamp ) {
                                // we need to catch the oldest job's timestamp.
                                $_oldest_job_timestamp = $job['jctime'];
                            }

                            if ( 'NEW' !== $job['jqstat'] && 'firstrun' !== $job['ljid'] ) {
                                // we have a non-"NEW" job which is not our pending firstrun job either, break the loop.
                                $_jobs_all_new = false;
                                break;
                            }
                        }
                    }

                    // is the oldest job too old (4h)?
                    if ( $_oldest_job_timestamp > microtime( true ) - 60 * 60 * 4 ) {
                        $_jobs_too_old = false;
                    }

                    if ( $_jobs_all_new && ! $this->ao_ccss_has_autorules() && $_jobs_too_old ) {
                        $_warn_cron            = 'on';
                        $_transient_multiplier = 1; // store for 1 hour.
                    } else {
                        $_warn_cron            = 'off';
                        $_transient_multiplier = 4; // store for 4 hours.
                    }
                    // and set transient.
                    set_transient( 'ao_ccss_cronwarning', $_warn_cron, $_transient_multiplier * HOUR_IN_SECONDS );
                }

                if ( ! empty( $ao_ccss_key ) && 'on' == $_warn_cron && PAnD::is_admin_notice_active( 'i-know-about-cron-1' ) ) {
                    ?>
                    <div data-dismissible="i-know-about-cron-1" class="notice-warning notice is-dismissible"><p>
                    <?php
                    _e( 'It looks like there might be a problem with WordPress cron (task scheduling). Have a look at <a href="https://blog.futtta.be/2023/03/17/how-to-fix-autoptimize-critical-css-cron-issue/" target="_blank">the FAQ</a> or the info in the Job Queue instructions if all jobs remain in "N" status and no rules are created.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                } elseif ( ! empty( $ao_ccss_key ) && '2' == $ao_ccss_keyst && 'on' != $_warn_cron && ! $this->ao_ccss_has_autorules() ) {
                    ?>
                    <div class="notice-success notice"><p>
                    <?php
                    _e( 'Great, Autoptimize will now automatically start creating new critical CSS rules, you should see those appearing below in the next couple of hours.', 'autoptimize' );
                    echo ' ';
                    _e( 'In the meantime you might want to <strong>edit default rule CSS now</strong>, to avoid all CSS being inlined when no (applicable) rules are found.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // warn if service is down.
                if ( ! empty( $ao_ccss_key ) && ! empty( $ao_ccss_servicestatus ) && is_array( $ao_ccss_servicestatus ) && 'down' === $ao_ccss_servicestatus['critcss']['status'] ) {
                    ?>
                    <div class="notice-warning notice"><p>
                    <?php
                    _e( 'The critical CSS service has been reported to be down. Although no new rules will be created for now, this does not prevent existing rules from being applied.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // warn if too many rules (based on length of ao_ccss_rules option) as that might cause issues at e.g. wpengine
                // see https://wpengine.com/support/database-optimization-best-practices/#Autoloaded_Data .
                $_raw_rules_length = strlen( get_option( 'autoptimize_ccss_rules', '' ) );
                if ( $_raw_rules_length > apply_filters( 'autoptimize_ccss_rules_length_warning', 500000 ) ) {
                    ?>
                    <div class="notice-warning notice"><p>
                    <?php
                    _e( 'It looks like the amount of Critical CSS rules is very high, it is recommended to reconfigure Autoptimize (e.g. by manually creating broader rules) to ensure less rules are created.', 'autoptimize' );
                    ?>
                    </p></div>
                    <?php
                }

                // Settings Form.
                ?>
                <form id="settings" method="post" action="options.php">
                    <?php
                    settings_fields( 'ao_ccss_options_group' );

                    // Get API key status.
                    $key = $this->criticalcss->key_status( true );

                    if ( $this->is_multisite_network_admin() ) {
                        ?>
                        <ul id="key-panel">
                            <li class="itemDetail">
                            <?php
                                // translators: the placesholder is for a line of code in wp-config.php.
                                echo sprintf( __( '<p>Critical CSS settings cannot be set at network level as critical CSS is specific to each sub-site.</p><p>You can however provide the critical CSS API key for use by all sites by adding this your wp-config.php as %s</p>', 'autoptimize' ), '<br/><code>define(\'AUTOPTIMIZE_CRITICALCSS_API_KEY\', \'eyJhbGmorestringsherexHa7MkOQFtDFkZgLmBLe-LpcHx4\');</code>' );
                            ?>
                            </li>
                        </ul>
                        <?php
                    } else {
                        if ( 'valid' == $key['status'] || ( defined( 'AO_PRO_VERSION' ) && has_filter( 'autoptimize_filter_ccss_key' ) ) ) {
                            // If key status is valid, render other panels.
                            // Render rules section.
                            ao_ccss_render_rules();
                            // Render queue section.
                            ao_ccss_render_queue();
                            // Render advanced panel.
                            ao_ccss_render_adv();
                        } else {
                            if ( apply_filters( 'autoptimize_filter_ccss_rules_without_api', true ) ) {
                                // Render rules section for manual rules.
                                ao_ccss_render_rules();
                            } else {
                                echo "<input class='hidden' name='autoptimize_ccss_rules' value='" . json_encode( $ao_ccss_rules, JSON_FORCE_OBJECT ) . "'>";
                            }

                            // But if key is other than valid, add hidden fields to persist settings when submitting form
                            // Show explanation of why and how to get a API key.
                            ao_ccss_render_explain();

                            // Get viewport size.
                            $viewport = $this->criticalcss->viewport();

                            // Add hidden fields.
                            echo "<input class='hidden' name='autoptimize_ccss_queue' value='" . json_encode( $ao_ccss_queue, JSON_FORCE_OBJECT ) . "'>";
                            echo '<input class="hidden" name="autoptimize_ccss_viewport[w]" value="' . esc_attr( $viewport['w'] ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_viewport[h]" value="' . esc_attr( $viewport['h'] ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_finclude" value="' . esc_attr( $ao_ccss_finclude ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_rtimelimit" value="' . esc_attr( $ao_ccss_rtimelimit ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_debug" value="' . esc_attr( $ao_ccss_debug ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_noptimize" value="' . esc_attr( $ao_ccss_noptimize ) . '">';
                            echo '<input class="hidden" name="autoptimize_css_defer_inline" value="' . esc_attr( $ao_css_defer_inline ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_loggedin" value="' . esc_attr( $ao_ccss_loggedin ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_forcepath" value="' . esc_attr( $ao_ccss_forcepath ) . '">';
                            echo '<input class="hidden" name="autoptimize_ccss_domain" id="autoptimize_ccss_domain" value="' . esc_attr( $ao_ccss_domain ) . '">';
                        }
                        // Render key panel unconditionally.
                        ao_ccss_render_key( $ao_ccss_key, $key['status'], $key['stmsg'], $key['msg'], $key['color'] );
                        ?>
                        <p class="submit left">
                            <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'autoptimize' ); ?>" />
                        </p>
                        <?php
                    }
                    ?>
                </form>
                <script>
                jQuery("form#settings").submit(function(){
                    var input = jQuery("#autoptimize_ccss_domain");
                    input.val(rot(input.val(), 13));
                });
                // rot JS from http://stackoverflow.com/a/617685/987044 .
                function rot(domainstring, itype) {
                    return domainstring.toString().replace(/[a-zA-Z]/g, function (letter) {
                        return String.fromCharCode((letter <= 'Z' ? 90 : 122) >= (letter = letter.charCodeAt(0) + itype) ? letter : letter   - 26);
                    });
                }
                </script>
                <form id="importSettingsForm"<?php if ( $this->is_multisite_network_admin() ) { echo ' class="hidden"'; } // @codingStandardsIgnoreLine ?>>
                    <span id="exportSettings" class="button-secondary"><?php _e( 'Export Settings', 'autoptimize' ); ?></span>
                    <input class="button-secondary" id="importSettings" type="button" value="<?php _e( 'Import Settings', 'autoptimize' ); ?>" onclick="upload();return false;" />
                    <input class="button-secondary" id="settingsfile" name="settingsfile" type="file" />
                </form>
                <div id="importdialog"></div>
            </div><!-- /#autoptimize_main -->
        </div><!-- /#wrap -->
        <?php
        if ( ! $this->is_multisite_network_admin() ) {
            // Include debug panel if debug mode is enable.
            if ( $ao_ccss_debug ) {
                ?>
                <div id="debug">
                    <?php
                    // Include debug panel.
                    include( 'critcss-inc/admin_settings_debug.php' );
                    ?>
                </div><!-- /#debug -->
                <?php
            }
            echo '<script>';
            include( 'critcss-inc/admin_settings_rules.js.php' );
            include( 'critcss-inc/admin_settings_queue.js.php' );
            include( 'critcss-inc/admin_settings_impexp.js.php' );
            echo '</script>';
        }
    }

    public function ao_ccss_has_autorules() {
        static $_has_auto_rules = null;

        if ( null === $_has_auto_rules ) {
            $ao_ccss_rules = $this->criticalcss->get_option( 'rules' );
            $_has_auto_rules = false;
            if ( ! empty( $ao_ccss_rules ) ) {
                foreach ( array( 'types', 'paths' ) as $_typat ) {
                    foreach ( $ao_ccss_rules[ $_typat ] as $rule ) {
                        if ( ! empty( $rule['hash'] ) ) {
                            // we have at least one AUTO job, so all is fine.
                            $_has_auto_rules = true;
                            break;
                        }
                    }
                    if ( $_has_auto_rules ) {
                        break;
                    }
                }
            }
        }

        return $_has_auto_rules;
    }

    public static function is_multisite_network_admin() {
        static $_multisite_network_admin = null;

        if ( null === $_multisite_network_admin ) {
            if ( is_multisite() && is_network_admin() ) {
                $_multisite_network_admin = true;
            } else {
                $_multisite_network_admin = false;
            }
        }

        return $_multisite_network_admin;
    }
}
