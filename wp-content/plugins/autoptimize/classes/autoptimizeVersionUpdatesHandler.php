<?php
/**
 * Handles version updates and should only be instantiated in autoptimize.php if/when needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizeVersionUpdatesHandler
{
    /**
     * The current major version string.
     *
     * @var string
     */
    protected $current_major_version = null;

    public function __construct( $current_version )
    {
        $this->current_major_version = substr( $current_version, 0, 3 );
    }

    /**
     * Runs all needed upgrade procedures (depending on the
     * current major version specified during class instantiation)
     */
    public function run_needed_major_upgrades()
    {
        $major_update = false;

        switch ( $this->current_major_version ) {
            case '1.6':
                $this->upgrade_from_1_6();
                $major_update = true;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '1.7':
                $this->upgrade_from_1_7();
                $major_update = true;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '1.9':
                $this->upgrade_from_1_9();
                $major_update = true;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '2.2':
                $this->upgrade_from_2_2();
                $major_update = true;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '2.4':
                if ( autoptimizeOptionWrapper::get_option( 'autoptimize_version', 'none' ) == '2.4.2' ) {
                    $this->upgrade_from_2_4_2();
                }
                $this->upgrade_from_2_4();
                $major_update = false;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '2.7':
                $this->upgrade_from_2_7();
                $major_update = true;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '2.8':
                // nothing.
            case '2.9':
                if ( version_compare( autoptimizeOptionWrapper::get_option( 'autoptimize_version', 'none' ), '2.9.999', 'lt' ) ) {
                    $this->upgrade_from_2_9_before_compatibility();
                }
                $major_update = false;
                // No break, intentionally, so all upgrades are ran during a single request...
            case '3.0':
                // nothing.
            case '3.1':
                $this->upgrade_from_3_1();
                $major_update = false;
        }

        if ( true === $major_update ) {
            $this->on_major_version_update();
        }
    }

    /**
     * Checks specified version against the one stored in the database under `autoptimize_version` and performs
     * any major upgrade routines if needed.
     * Updates the database version to the specified $target if it's different to the one currently stored there.
     *
     * @param string $target Target version to check against (ie., the currently running one).
     */
    public static function check_installed_and_update( $target )
    {
        $db_version = autoptimizeOptionWrapper::get_option( 'autoptimize_version', 'none' );
        if ( $db_version !== $target ) {
            if ( 'none' === $db_version ) {
                add_action( 'admin_notices', 'autoptimizeMain::notice_installed' );
            } else {
                $updater = new self( $db_version );
                $updater->run_needed_major_upgrades();
            }

            // Versions differed, upgrades happened if needed, store the new version.
            autoptimizeOptionWrapper::update_option( 'autoptimize_version', $target );
        }
    }

    /**
     * Called after any major version update (and it's accompanying upgrade procedure)
     * has happened. Clears cache and sets an admin notice.
     */
    protected function on_major_version_update()
    {
        // The transients guard here prevents stale object caches from busting the cache on every request.
        if ( false == get_transient( 'autoptimize_stale_option_buster' ) ) {
            set_transient( 'autoptimize_stale_option_buster', 'Mamsie & Liessie zehhe: ZWIJH!', HOUR_IN_SECONDS );
            autoptimizeCache::clearall();
            add_action( 'admin_notices', 'autoptimizeMain::notice_updated' );
        }
    }

    /**
     * From back in the days when I did not yet consider multisite.
     */
    private function upgrade_from_1_6()
    {
        // If user was on version 1.6.x, force advanced options to be shown by default.
        autoptimizeOptionWrapper::update_option( 'autoptimize_show_adv', '1' );

        // And remove old options.
        $to_delete_options = array(
            'autoptimize_cdn_css',
            'autoptimize_cdn_css_url',
            'autoptimize_cdn_js',
            'autoptimize_cdn_js_url',
            'autoptimize_cdn_img',
            'autoptimize_cdn_img_url',
            'autoptimize_css_yui',
        );
        foreach ( $to_delete_options as $del_opt ) {
            delete_option( $del_opt );
        }
    }

    /**
     * Forces WP 3.8 dashicons in CSS exclude options when upgrading from 1.7 to 1.8
     *
     * @global $wpdb
     */
    private function upgrade_from_1_7()
    {
        if ( ! is_multisite() ) {
            $css_exclude = autoptimizeOptionWrapper::get_option( 'autoptimize_css_exclude' );
            if ( empty( $css_exclude ) ) {
                $css_exclude = 'admin-bar.min.css, dashicons.min.css';
            } elseif ( false === strpos( $css_exclude, 'dashicons.min.css' ) ) {
                $css_exclude .= ', dashicons.min.css';
            }
            autoptimizeOptionWrapper::update_option( 'autoptimize_css_exclude', $css_exclude );
        } else {
            global $wpdb;
            $blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            $original_blog_id = get_current_blog_id();
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                $css_exclude = autoptimizeOptionWrapper::get_option( 'autoptimize_css_exclude' );
                if ( empty( $css_exclude ) ) {
                    $css_exclude = 'admin-bar.min.css, dashicons.min.css';
                } elseif ( false === strpos( $css_exclude, 'dashicons.min.css' ) ) {
                    $css_exclude .= ', dashicons.min.css';
                }
                autoptimizeOptionWrapper::update_option( 'autoptimize_css_exclude', $css_exclude );
            }
            switch_to_blog( $original_blog_id );
        }
    }

    /**
     * 2.0 will not aggregate inline CSS/JS by default, but we want users
     * upgrading from 1.9 to keep their inline code aggregated by default.
     *
     * @global $wpdb
     */
    private function upgrade_from_1_9()
    {
        if ( ! is_multisite() ) {
            autoptimizeOptionWrapper::update_option( 'autoptimize_css_include_inline', 'on' );
            autoptimizeOptionWrapper::update_option( 'autoptimize_js_include_inline', 'on' );
        } else {
            global $wpdb;
            $blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            $original_blog_id = get_current_blog_id();
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                autoptimizeOptionWrapper::update_option( 'autoptimize_css_include_inline', 'on' );
                autoptimizeOptionWrapper::update_option( 'autoptimize_js_include_inline', 'on' );
            }
            switch_to_blog( $original_blog_id );
        }
    }

    /**
     * 2.3 has no "remove google fonts" in main screen, moved to "extra"
     *
     * @global $wpdb
     */
    private function upgrade_from_2_2()
    {
        if ( ! is_multisite() ) {
            $this->do_2_2_settings_update();
        } else {
            global $wpdb;
            $blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            $original_blog_id = get_current_blog_id();
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                $this->do_2_2_settings_update();
            }
            switch_to_blog( $original_blog_id );
        }
    }

    /**
     * Helper for 2.2 autoptimize_extra_settings upgrade to avoid duplicate code
     */
    private function do_2_2_settings_update()
    {
        $nogooglefont    = autoptimizeOptionWrapper::get_option( 'autoptimize_css_nogooglefont', '' );
        $ao_extrasetting = autoptimizeOptionWrapper::get_option( 'autoptimize_extra_settings', '' );
        if ( ( $nogooglefont ) && ( empty( $ao_extrasetting ) ) ) {
            autoptimizeOptionWrapper::update_option( 'autoptimize_extra_settings', autoptimizeConfig::get_ao_extra_default_options() );
        }
        delete_option( 'autoptimize_css_nogooglefont' );
    }

    /**
     * 2.4.2 introduced too many cronned ao_cachecheckers, make this right
     */
    private function upgrade_from_2_4_2() {
        // below code by Thomas Sjolshagen (http://eighty20results.com/)
        // as found on https://www.paidmembershipspro.com/deleting-oldextra-cron-events/.
        $jobs = _get_cron_array();

        // Remove all ao_cachechecker cron jobs (for now).
        foreach ( $jobs as $when => $job ) {
            $name = key( $job );

            if ( false !== strpos( $name, 'ao_cachechecker' ) ) {
                unset( $jobs[ $when ] );
            }
        }

        // Save the data.
        _set_cron_array( $jobs );
    }

    /**
     * Migrate imgopt options from autoptimize_extra_settings to autoptimize_imgopt_settings
     */
    private function upgrade_from_2_4() {
        $extra_settings  = autoptimizeOptionWrapper::get_option( 'autoptimize_extra_settings', '' );
        $imgopt_settings = autoptimizeOptionWrapper::get_option( 'autoptimize_imgopt_settings', '' );
        if ( empty( $imgopt_settings ) && ! empty( $extra_settings ) ) {
            $imgopt_settings = autoptimizeConfig::get_ao_imgopt_default_options();
            if ( array_key_exists( 'autoptimize_extra_checkbox_field_5', $extra_settings ) ) {
                $imgopt_settings['autoptimize_imgopt_checkbox_field_1'] = $extra_settings['autoptimize_extra_checkbox_field_5'];
            }
            if ( array_key_exists( 'autoptimize_extra_select_field_6', $extra_settings ) ) {
                $imgopt_settings['autoptimize_imgopt_select_field_2'] = $extra_settings['autoptimize_extra_select_field_6'];
            }
            autoptimizeOptionWrapper::update_option( 'autoptimize_imgopt_settings', $imgopt_settings );
        }
    }

    /**
     * Remove CCSS request limit option + update jquery exclusion to include WordPress 5.6 jquery.min.js.
     */
    private function upgrade_from_2_7() {
        delete_option( 'autoptimize_ccss_rlimit' );
        $js_exclusions = get_option( 'autoptimize_js_exclude', '' );
        if ( strpos( $js_exclusions, 'js/jquery/jquery.js' ) !== false && strpos( $js_exclusions, 'js/jquery/jquery.min.js' ) === false ) {
            $js_exclusions .= ', js/jquery/jquery.min.js';
            autoptimizeOptionWrapper::update_option( 'autoptimize_js_exclude', $js_exclusions );
        }
    }

    /**
     * Set an option to indicate the AO installation predates the compatibility logic, this way we
     * can avoid adding compatibility code that is likely not needed and maybe not wanted as it
     * can introduce performance regressions.
     */
    private function upgrade_from_2_9_before_compatibility() {
        autoptimizeOptionWrapper::update_option( 'autoptimize_installed_before_compatibility', true );
    }

    /**
     * If the 404 handler is active, delete the current PHP-file so it can be re-created to fix the double underscore bug.
     */
    private function upgrade_from_3_1() {
        if ( autoptimizeCache::do_fallback() && version_compare( autoptimizeOptionWrapper::get_option( 'autoptimize_version', 'none' ), '3.1.2', 'lt' ) ) {
            $_fallback_php = trailingslashit( WP_CONTENT_DIR ) . 'autoptimize_404_handler.php';
            @unlink( $_fallback_php ); // @codingStandardsIgnoreLine
        }
    }
}
