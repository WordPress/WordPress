<?php
/**
 * Autoptimize options handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * This class takes care of the set and get of option for standalone and multisite WordPress instances.
 */
class autoptimizeOptionWrapper {
    /**
     * Constructor, add filter on saving options.
     */
    public function __construct() {
    }

    /**
     * Ensure that is_plugin_active_for_network function is declared.
     */
    public static function maybe_include_plugin_functions() {
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
    }

    /**
     * Retrieves the option in standalone and multisite instances.
     *
     * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
     * @param mixed  $default Optional. Default value to return if the option does not exist.
     * @return mixed Value set for the option.
     */
    public static function get_option( $option, $default = false ) {
        if ( is_multisite() && self::is_ao_active_for_network() ) {
            static $configuration_per_site = null;
            if ( null === $configuration_per_site || defined( 'TEST_MULTISITE_FORCE_AO_ON_NETWORK' ) ) {
                $configuration_per_site = get_network_option( get_main_network_id(), 'autoptimize_enable_site_config', 'on' );
                if ( null === $configuration_per_site ) {
                    // Config per site is off, set as empty string to make sure the var it is not null any more so it can be cached.
                    $configuration_per_site = '';
                }
            }
        } else {
            // Kind of dummy value as when not on multisite or if AO not network enabled, config is always on site-level.
            $configuration_per_site = 'on';
        }

        // This is always a network setting, it is on by default to ensure settings are available at site level unless explicitly turned off.
        if ( 'autoptimize_enable_site_config' === $option ) {
            return $configuration_per_site;
        }

        // If the plugin is network activated and our per site setting is not on, use the network configuration.
        if ( is_multisite() && self::is_ao_active_for_network() && ( 'on' !== $configuration_per_site || is_network_admin() ) ) {
            return get_network_option( get_main_network_id(), $option, $default );
        }

        return get_option( $option, $default );
    }

    /**
     * Saves the option in standalone and multisite instances.
     *
     * @param string      $option   Option name. Expected to not be SQL-escaped.
     * @param mixed       $value    Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * @param string|bool $autoload Optional. Whether to load the option when WordPress starts up. For existing options,
     *                              `$autoload` can only be updated using `update_option()` if `$value` is also changed.
     *                              Accepts 'yes'|true to enable or 'no'|false to disable. For non-existent options,
     *                              the default value is 'yes'. Default null.
     * @return bool False if value was not updated and true if value was updated.
     */
    public static function update_option( $option, $value, $autoload = null ) {
        if ( self::is_ao_active_for_network() && is_network_admin() ) {
            return update_network_option( get_main_network_id(), $option, $value );
        } elseif ( 'autoptimize_enable_site_config' !== $option ) {
            return update_option( $option, $value, $autoload );
        }
    }

    /**
     * Use the pre_update_option filter to check if the option to be saved if from autoptimize and
     * in that case, take care of multisite case.
     */
    public static function check_multisite_on_saving_options() {
        if ( self::is_ao_active_for_network() ) {
            add_filter( 'pre_update_option', 'autoptimizeOptionWrapper::update_autoptimize_option_on_network', 10, 3 );
        }
    }

    /**
     * The actual magic to differentiate between network options and per-site options.
     *
     * @param mixed  $value     Option value.
     * @param string $option    Option name.
     * @param string $old_value Old value.
     */
    public static function update_autoptimize_option_on_network( $value, $option, $old_value ) {
        if ( strpos( $option, 'autoptimize_' ) === 0 && self::is_options_from_network_admin() ) {
            if ( self::is_ao_active_for_network() ) {
                update_network_option( get_main_network_id(), $option, $value );
                // Return old value, to stop update_option logic.
                return $old_value;
            }
            if ( apply_filters( 'autoptimize_filter_optionwrapper_wp_cache_delete', false ) ) {
                // in some (rare) cases options seem to get stuck in WP's Object cache, this should clear it there.
                wp_cache_delete( $option );
            }
        }
        return $value;
    }

    /**
     * As options are POST-ed to wp-admin/options.php checking is_network_admin() does not
     * work (yet). Instead we compare the network_admin_url with the _wp_http_referer
     * (which should always be available as part of a hidden form field).
     */
    public static function is_options_from_network_admin() {
        static $_really_is_network_admin = null;

        if ( null === $_really_is_network_admin ) {
            if ( array_key_exists( '_wp_http_referer', $_POST ) && strpos( network_admin_url( 'settings.php' ), strtok( $_POST['_wp_http_referer'], '?' ) ) !== false ) {
                $_really_is_network_admin = true;
            } else {
                $_really_is_network_admin = false;
            }
        }

        return $_really_is_network_admin;
    }

    /**
     * Function to check if AO (including beta) is active for network.
     */
    public static function is_ao_active_for_network() {
        static $_is_ao_active_for_network = null;
        if ( null === $_is_ao_active_for_network || defined( 'TEST_MULTISITE_FORCE_AO_ON_NETWORK' ) ) {
            self::maybe_include_plugin_functions();
            if ( is_plugin_active_for_network( 'autoptimize/autoptimize.php' ) || is_plugin_active_for_network( 'autoptimize-beta/autoptimize.php' ) || defined( 'TEST_MULTISITE_FORCE_AO_ON_NETWORK' ) ) {
                $_is_ao_active_for_network = true;
            } else {
                $_is_ao_active_for_network = false;
            }
        }
        return $_is_ao_active_for_network;
    }
}
new autoptimizeOptionWrapper();
