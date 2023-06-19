<?php
/**
 * Critical CSS base file (initializes all ccss files).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeCriticalCSSBase {
    /**
     * Main plugin filepath.
     * Used for activation/deactivation/uninstall hooks.
     *
     * @var string
     */
    protected $filepath = null;

    /**
     * Critical CSS options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Core object
     *
     * @var object
     */
    protected $_core = null;

    /**
     * Cron object
     *
     * @var object
     */
    protected $_cron = null;

    /**
     * Settings object
     *
     * @var object
     */
    protected $_settings = null;

    /**
     * Enqueue object
     *
     * @var object
     */
    protected $_enqueue = null;

    public function __construct()
    {
        // define constant, but only once.
        if ( ! defined( 'AO_CCSS_DIR' ) ) {
            // Define a constant with the directory to store critical CSS in.
            if ( is_multisite() ) {
                $blog_id = get_current_blog_id();
                define( 'AO_CCSS_DIR', WP_CONTENT_DIR . '/uploads/ao_ccss/' . $blog_id . '/' );
            } else {
                define( 'AO_CCSS_DIR', WP_CONTENT_DIR . '/uploads/ao_ccss/' );
            }
        }
        if ( ! defined( 'AO_CCSS_VER' ) ) {
            // Define plugin version.
            define( 'AO_CCSS_VER', 'AO_' . AUTOPTIMIZE_PLUGIN_VERSION );

            // Define constants for criticalcss.com base path and API endpoints.
            // fixme: AO_CCSS_URL should be read from the autoptimize availability json stored as option.
            define( 'AO_CCSS_URL', 'https://criticalcss.com' );
            define( 'AO_CCSS_API', apply_filters( 'autoptimize_filter_ccss_service_url', AO_CCSS_URL . '/api/premium/' ) );
            define( 'AO_CCSS_SLEEP', 10 );
        }

        // Define support files locations, in case they are not already defined.
        if ( ! defined( 'AO_CCSS_LOCK' ) ) {
            define( 'AO_CCSS_LOCK', AO_CCSS_DIR . 'queue.lock' );
        }
        if ( ! defined( 'AO_CCSS_LOG' ) ) {
            define( 'AO_CCSS_LOG', AO_CCSS_DIR . 'queuelog.html' );
        }
        if ( ! defined( 'AO_CCSS_DEBUG' ) ) {
            define( 'AO_CCSS_DEBUG', AO_CCSS_DIR . 'queue.json' );
        }

        $this->filepath = __FILE__;

        // Add keychecker action for scheduled use.
        add_action( 'ao_ccss_keychecker', array( $this, 'ao_ccss_check_key' ) );
    }

    public function setup() {
        // check if we need to upgrade.
        $this->check_upgrade();

        // add/ remove scheduled jobs.
        if ( $this->is_api_active() ) {
            // make sure the 10 minutes cron schedule is added.
            add_filter( 'cron_schedules', array( $this, 'ao_ccss_interval' ) );

            if ( ! wp_next_scheduled( 'ao_ccss_queue' ) ) {
                // make sure ao_ccss_queue is scheduled OK if an API key is active.
                wp_schedule_event( time(), apply_filters( 'ao_ccss_queue_schedule', 'ao_ccss' ), 'ao_ccss_queue' );
            }

            if ( ! wp_next_scheduled( 'ao_ccss_maintenance' ) ) {
                // and schedule maintenance job.
                wp_schedule_event( time(), 'twicedaily', 'ao_ccss_maintenance' );
            }

            if ( wp_next_scheduled( 'ao_ccss_keychecker' ) ) {
                // api is active now, no need to check key as it is checked by using the API.
                wp_clear_scheduled_hook( 'ao_ccss_keychecker' );
            }
        } else {
            if ( wp_next_scheduled( 'ao_ccss_queue' ) ) {
                wp_clear_scheduled_hook( 'ao_ccss_queue' );
            }

            if ( wp_next_scheduled( 'ao_ccss_maintenance' ) ) {
                wp_clear_scheduled_hook( 'ao_ccss_maintenance' );
            }

            // add keychecker logic if api is not active but we have a key so maybe this is a temporary issue, check if key is OK daily.
            $ao_ccss_key   = $this->get_option( 'key' );
            if ( ! empty( $ao_ccss_key ) && ! wp_next_scheduled( 'ao_ccss_keychecker' ) ) {
                wp_schedule_event( time(), 'twicedaily', 'ao_ccss_keychecker' );
            } else if ( empty( $ao_ccss_key ) && wp_next_scheduled( 'ao_ccss_keychecker' ) ) {
                // edge case: we had a inactive key that was checked daily, but it is now removed, so remove keychecker from schedule.
                wp_clear_scheduled_hook( 'ao_ccss_keychecker' );
            }
        }

        // check/ create AO_CCSS_DIR.
        if ( ! file_exists( AO_CCSS_DIR ) ) {
            $this->create_ao_ccss_dir();
        }
    }

    public function load_requires() {
        // Required libs, core is always needed.
        $this->_core = new autoptimizeCriticalCSSCore();

        if ( ( defined( 'WP_CLI' ) || defined( 'DOING_CRON' ) || is_admin() ) && $this->is_api_active() ) {
            // cron only initiated when doing cron (or wp_cli or is_amdin) and when we have an active API key.
            $this->_cron = new autoptimizeCriticalCSSCron();
        }

        if ( is_admin() ) {
            $this->_settings = new autoptimizeCriticalCSSSettings();
        } else if ( $this->is_api_active() ) {
            // enqueuing only done when not wp-admin and when API is active.
            $this->_enqueue = new autoptimizeCriticalCSSEnqueue();
        }
    }

    /**
     * Log a message via CCSS Core object
     *
     * @param string $msg Message to log.
     * @param int    $lvl Loglevel.
     *
     * @return empty
     */
    public function log( $msg, $lvl ) {
        return $this->_core->ao_ccss_log( $msg, $lvl );
    }

    /**
     * Get viewport from CCSS Core object
     *
     * @return array
     */
    public function viewport() {
        return $this->_core->ao_ccss_viewport();
    }

    /**
     * Check CCSS contents from Core object
     *
     * @param string $ccss Critical CSS to be checked.
     *
     * @return bool
     */
    public function check_contents( $ccss ) {
        return $this->_core->ao_ccss_check_contents( $ccss );
    }

    /**
     * Get key status from Core object
     *
     * @param bool $render Indicates if key status is to be rendered.
     *
     * @return array
     */
    public function key_status( $render ) {
        return $this->_core->ao_ccss_key_status( $render );
    }

    /**
     * Return valid types from core object
     *
     * @return array
     */
    public function get_types() {
        return $this->_core->get_types();
    }

    /**
     * Run enqueue in CCSS Enqueue object
     *
     * @param string $hash Hash (default empty).
     * @param string $path Path (default empty).
     * @param string $type (default is_page).
     */
    public function enqueue( $hash = '', $path = '', $type = 'is_page' ) {
        // Enqueue is sometimes required on wp-admin requests, load it just-in-time.
        if ( is_null( $this->_enqueue ) && $this->is_api_active() ) {
            $this->_enqueue = new autoptimizeCriticalCSSEnqueue();
        }

        return $this->_enqueue->ao_ccss_enqueue( $hash, $path, $type );
    }

    /**
     * Check auto-rules in CCSS Settings object
     */
    public function has_autorules() {
        return $this->_settings->ao_ccss_has_autorules();
    }

    /**
     * Get a Critical CSS option
     *
     * @param string $name The option name.
     *
     * @return mixed
     */
    public function get_option( $name ) {
        if ( is_null( $this->_options ) ) {
            $this->fetch_options();
        }

        if ( isset( $this->_options[ $name ] ) ) {
            return $this->_options[ $name ];
        }

        return null;
    }

    public function flush_options() {
        $this->_options = null;
    }

    protected function fetch_options() {
        if ( ! is_null( $this->_options ) ) {
            return $this->_options;
        }

        $this->_options = array(
            'css_defer'         => autoptimizeOptionWrapper::get_option( 'autoptimize_css_defer' ),
            'css_defer_inline'  => autoptimizeOptionWrapper::get_option( 'autoptimize_css_defer_inline' ),
            'rules_raw'     => get_option( 'autoptimize_ccss_rules', false ),
            'additional'    => get_option( 'autoptimize_ccss_additional' ),
            'queue_raw'     => get_option( 'autoptimize_ccss_queue', false ),
            'viewport'      => get_option( 'autoptimize_ccss_viewport', false ),
            'finclude'      => get_option( 'autoptimize_ccss_finclude', false ),
            'rtimelimit'    => get_option( 'autoptimize_ccss_rtimelimit', '30' ),
            'noptimize'     => get_option( 'autoptimize_ccss_noptimize', false ),
            'debug'         => get_option( 'autoptimize_ccss_debug', false ),
            'key'           => apply_filters( 'autoptimize_filter_ccss_key', get_option( 'autoptimize_ccss_key' ) ),
            'keyst'         => get_option( 'autoptimize_ccss_keyst' ),
            'loggedin'      => get_option( 'autoptimize_ccss_loggedin', '1' ),
            'forcepath'     => get_option( 'autoptimize_ccss_forcepath', '1' ),
            'servicestatus' => get_option( 'autoptimize_service_availablity' ),
            'deferjquery'   => get_option( 'autoptimize_ccss_deferjquery', false ),
            'domain'        => get_option( 'autoptimize_ccss_domain' ),
            'unloadccss'    => get_option( 'autoptimize_ccss_unloadccss', false ),
        );

        if ( strpos( $this->_options['domain'], 'http' ) === false && strpos( $this->_options['domain'], 'uggc' ) === 0 ) {
            $this->_options['domain'] = str_rot13( $this->_options['domain'] );
        } elseif ( strpos( $this->_options['domain'], 'http' ) !== false ) {
            // not rot13'ed yet, do so now (goal; avoid migration plugins change the bound domain).
            update_option( 'autoptimize_ccss_domain', str_rot13( $this->_options['domain'] ) );
        }

        // Setup the rules array.
        if ( empty( $this->_options['rules_raw'] ) ) {
            $this->_options['rules'] = array(
                'paths' => array(),
                'types' => array(),
            );
        } else {
            $this->_options['rules'] = json_decode( $this->_options['rules_raw'], true );
        }

        // Setup the queue array.
        if ( empty( $this->_options['queue_raw'] ) ) {
            $this->_options['queue'] = array();
        } else {
            $this->_options['queue'] = json_decode( $this->_options['queue_raw'], true );
        }

        // Override API key if constant is defined.
        if ( defined( 'AUTOPTIMIZE_CRITICALCSS_API_KEY' ) ) {
            $this->_options['key'] = AUTOPTIMIZE_CRITICALCSS_API_KEY;
        }

        return $this->_options;
    }

    public function on_upgrade() {
        $key = $this->get_option( 'key' );

        // Create the cache directory if it doesn't exist already.
        if ( ! file_exists( AO_CCSS_DIR ) ) {
            $this->create_ao_ccss_dir();
        }

        // Create a scheduled event for the queue.
        if ( $this->is_api_active() && ! wp_next_scheduled( 'ao_ccss_queue' ) ) {
            wp_schedule_event( time(), apply_filters( 'ao_ccss_queue_schedule', 'ao_ccss' ), 'ao_ccss_queue' );
        }

        // Create a scheduled event for log maintenance.
        if ( $this->is_api_active() && ! wp_next_scheduled( 'ao_ccss_maintenance' ) ) {
            wp_schedule_event( time(), 'twicedaily', 'ao_ccss_maintenance' );
        }
    }

    public function check_upgrade() {
        $db_version = get_option( 'autoptimize_ccss_version', '' );
        if ( AO_CCSS_VER !== $db_version ) {
            // check schedules & re-schedule if needed.
            $this->on_upgrade();
            // and update db_version.
            update_option( 'autoptimize_ccss_version', AO_CCSS_VER );
        }
    }

    public function ao_ccss_interval( $schedules ) {
        // Let interval be configurable.
        if ( ! defined( 'AO_CCSS_DEBUG_INTERVAL' ) ) {
            $intsec = 600;
            $inttxt = '10 minutes';
        } else {
            $intsec = AO_CCSS_DEBUG_INTERVAL;
            if ( $intsec >= 120 ) {
                $inttxt = $intsec / 60 . ' minutes';
            } else {
                $inttxt = $intsec . ' second(s)';
            }
            $this->log( 'Using custom WP-Cron interval of ' . $inttxt, 3 );
        }

        // Attach interval to schedule.
        $schedules['ao_ccss'] = array(
            'interval' => $intsec,
            'display'  => sprintf( __( 'Every %s (Autoptimize Crit. CSS)', 'autoptimize' ), $inttxt ),
        );
        return $schedules;
    }

    public function create_ao_ccss_dir() {
        // Make sure dir to write ao_ccss exists and is writable.
        if ( ! is_dir( AO_CCSS_DIR ) ) {
            // TODO: use wp_mkdir_p() ?
            $mkdirresp = @mkdir( AO_CCSS_DIR, 0775, true ); // @codingStandardsIgnoreLine
        } else {
            $mkdirresp = true;
        }

        // Make sure our index.html is there.
        if ( ! is_file( AO_CCSS_DIR . 'index.html' ) ) {
            $fileresp = file_put_contents( AO_CCSS_DIR . 'index.html', '<html><head><meta name="robots" content="noindex, nofollow"></head><body>Generated by <a href="http://wordpress.org/extend/plugins/autoptimize/" rel="nofollow">Autoptimize</a></body></html>' );
        } else {
            $fileresp = true;
        }

        if ( true === $fileresp && true === $mkdirresp ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Helper function to determine if there is an active API key.
     *
     * @return bool
     */
    public function is_api_active() {
        // using options instead of more complex $this->key_status (which gave some dependancy issues ... ;-) ).
        $ao_ccss_key   = $this->get_option( 'key' );
        $ao_ccss_keyst = $this->get_option( 'keyst' );

        if ( ! empty( $ao_ccss_key ) && $ao_ccss_keyst && 2 == $ao_ccss_keyst ) {
            return true;
        }

        return false;
    }

    /**
     * Helper function to determine if a rule is MANUAL.
     *
     * @param array $rule Rule to check.
     *
     * @return bool
     */
    public function is_rule_manual( $rule ) {
        if ( is_array( $rule ) && false == $rule['hash'] && false != $rule['file'] ) {
            return true;
        }

        return false;
    }

    /**
     * Scheduled action to check an inactive key. Not part of autoptimizeCriticalCSSCron.php
     * to allow us to only load the main cron logic if we have an active key to begin with.
     */
    public function ao_ccss_check_key() {
        $ao_ccss_key = $this->get_option( 'key' );
        $_result = $this->_core->ao_ccss_key_validation( $ao_ccss_key );
        $_resmsg = ( true === $_result ) ? 'ok' : 'nok';
        $this->log( 'Inactive key checked, result was ' . $_resmsg, 3 );
    }
}
