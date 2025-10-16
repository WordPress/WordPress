<?php
/**
 * Languages settings page.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

/**
 * Provides a Settings API wrapper for the Languages admin screen.
 */
class WP_Languages_Settings {

        /**
         * Bootstraps hooks.
         */
        public static function init() {
                add_action( 'admin_menu', array( __CLASS__, 'register_page' ) );
                add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        }

        /**
         * Registers the settings page under Settings > Languages.
         */
        public static function register_page() {
                add_options_page(
                        __( 'Languages', 'default' ),
                        __( 'Languages', 'default' ),
                        'manage_options',
                        'languages',
                        array( __CLASS__, 'render_page' )
                );
        }

        /**
         * Registers settings and fields.
         */
        public static function register_settings() {
                register_setting(
                        'languages',
                        'translation_available_locales',
                        array(
                                'type'              => 'string',
                                'sanitize_callback' => array( __CLASS__, 'sanitize_locales_field' ),
                                'default'           => '',
                        )
                );
                register_setting(
                        'languages',
                        'translation_default_locale',
                        array(
                                'type'              => 'string',
                                'sanitize_callback' => array( __CLASS__, 'sanitize_default_locale' ),
                                'default'           => '',
                        )
                );
                register_setting(
                        'languages',
                        'translation_locale_mode',
                        array(
                                'type'              => 'string',
                                'sanitize_callback' => array( __CLASS__, 'sanitize_locale_mode' ),
                                'default'           => 'path',
                        )
                );
                register_setting(
                        'languages',
                        'translation_domain_mapping',
                        array(
                                'type'              => 'string',
                                'sanitize_callback' => array( __CLASS__, 'sanitize_domain_mapping' ),
                                'default'           => '',
                        )
                );

                add_settings_section(
                        'translation_languages',
                        __( 'Locale Options', 'default' ),
                        '__return_false',
                        'languages'
                );

                add_settings_field(
                        'translation_available_locales',
                        __( 'Available locales', 'default' ),
                        array( __CLASS__, 'field_locales' ),
                        'languages',
                        'translation_languages'
                );

                add_settings_field(
                        'translation_default_locale',
                        __( 'Default locale', 'default' ),
                        array( __CLASS__, 'field_default_locale' ),
                        'languages',
                        'translation_languages'
                );

                add_settings_field(
                        'translation_locale_mode',
                        __( 'Routing mode', 'default' ),
                        array( __CLASS__, 'field_routing_mode' ),
                        'languages',
                        'translation_languages'
                );

                add_settings_field(
                        'translation_domain_mapping',
                        __( 'Domain mapping', 'default' ),
                        array( __CLASS__, 'field_domain_mapping' ),
                        'languages',
                        'translation_languages'
                );
        }

        /**
         * Renders the languages settings page.
         */
        public static function render_page() {
                if ( ! current_user_can( 'manage_options' ) ) {
                        return;
                }
                ?>
                <div class="wrap">
                        <h1><?php esc_html_e( 'Languages', 'default' ); ?></h1>
                        <form method="post" action="options.php">
                                <?php
                                settings_fields( 'languages' );
                                do_settings_sections( 'languages' );
                                submit_button();
                                ?>
                        </form>
                </div>
                <?php
        }

        /**
         * Field callback for available locales.
         */
        public static function field_locales() {
                $value = get_option( 'translation_available_locales', '' );
                ?>
                <textarea name="translation_available_locales" id="translation_available_locales" class="large-text code" rows="5"><?php echo esc_textarea( is_array( $value ) ? implode( "\n", $value ) : $value ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Enter one locale per line (for example en_US or de_DE).', 'default' ); ?></p>
                <?php
        }

        /**
         * Field callback for default locale.
         */
        public static function field_default_locale() {
                $value = get_option( 'translation_default_locale', '' );
                ?>
                <input name="translation_default_locale" id="translation_default_locale" type="text" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
                <p class="description"><?php esc_html_e( 'This locale is used when translations are missing.', 'default' ); ?></p>
                <?php
        }

        /**
         * Field callback for routing mode.
         */
        public static function field_routing_mode() {
                $value = get_option( 'translation_locale_mode', 'path' );
                ?>
                <select name="translation_locale_mode" id="translation_locale_mode">
                        <option value="path" <?php selected( $value, 'path' ); ?>><?php esc_html_e( 'URL prefix', 'default' ); ?></option>
                        <option value="domain" <?php selected( $value, 'domain' ); ?>><?php esc_html_e( 'Mapped domains', 'default' ); ?></option>
                </select>
                <p class="description"><?php esc_html_e( 'Choose how locales are represented in front-end URLs.', 'default' ); ?></p>
                <?php
        }

        /**
         * Field callback for domain mapping.
         */
        public static function field_domain_mapping() {
                $value = get_option( 'translation_domain_mapping', '' );
                ?>
                <textarea name="translation_domain_mapping" id="translation_domain_mapping" class="large-text code" rows="5"><?php echo esc_textarea( is_array( $value ) ? self::stringify_mapping( $value ) : $value ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Provide domain=locale pairs, one per line. Only used when using mapped domains.', 'default' ); ?></p>
                <?php
        }

        /**
         * Converts an array mapping into a newline separated string.
         *
         * @param array $mapping Mapping array.
         * @return string
         */
        protected static function stringify_mapping( $mapping ) {
                $output = array();

                foreach ( $mapping as $domain => $locale ) {
                        $output[] = $domain . '=' . $locale;
                }

                return implode( "\n", $output );
        }

        /**
         * Sanitizes the locales textarea field.
         *
         * @param string|array $value Raw field value.
         * @return string
         */
        public static function sanitize_locales_field( $value ) {
                if ( is_array( $value ) ) {
                        $value = implode( "\n", $value );
                }

                $locales   = preg_split( '/[\r\n]+/', (string) $value );
                $sanitized = array();

                foreach ( $locales as $locale ) {
                        $locale = trim( $locale );

                        if ( '' === $locale ) {
                                continue;
                        }

                        $sanitized[] = wp_translation_sanitize_locale( $locale );
                }

                return implode( "\n", array_unique( array_filter( $sanitized ) ) );
        }

        /**
         * Sanitizes the default locale option.
         *
         * @param string $value Raw value.
         * @return string
         */
        public static function sanitize_default_locale( $value ) {
                return wp_translation_sanitize_locale( $value );
        }

        /**
         * Sanitizes the routing mode option.
         *
         * @param string $value Raw value.
         * @return string
         */
        public static function sanitize_locale_mode( $value ) {
                return in_array( $value, array( 'path', 'domain' ), true ) ? $value : 'path';
        }

        /**
         * Sanitizes domain mapping entries.
         *
         * @param string|array $value Raw value.
         * @return string
         */
        public static function sanitize_domain_mapping( $value ) {
                if ( is_array( $value ) ) {
                        $value = self::stringify_mapping( $value );
                }

                $lines  = preg_split( '/[\r\n]+/', (string) $value );
                $output = array();

                foreach ( $lines as $line ) {
                        $line = trim( $line );

                        if ( '' === $line || false === strpos( $line, '=' ) ) {
                                continue;
                        }

                        list( $domain, $locale ) = array_map( 'trim', explode( '=', $line, 2 ) );

                        if ( '' === $domain || '' === $locale ) {
                                continue;
                        }

                        $output[] = strtolower( $domain ) . '=' . wp_translation_sanitize_locale( $locale );
                }

                return implode( "\n", array_unique( $output ) );
        }
}

WP_Languages_Settings::init();
