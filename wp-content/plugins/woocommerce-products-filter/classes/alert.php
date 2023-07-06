<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_ADV {

    public $notices_list = array();

    public function __construct($alert_list = array()) {

        //fix to avoid disabling of 'Upload Theme' button action on /wp-admin/theme-install.php
        if (isset($_SERVER['REQUEST_URI'])) {
            if (substr_count(WOOF_HELPER::get_server_var('REQUEST_URI'), 'theme-install.php')) {
                return;
            }
        } else {
            if (isset($_SERVER['PHP_SELF'])) {
                if (substr_count($_SERVER['PHP_SELF'], 'theme-install.php')) {
                    return;
                }
            }
        }

        //***

        $this->notices_list = array(
            //'woocommerce_currency_switcher' => 'woocommerce-currency-switcher',
            'woocommerce_bulk_editor' => 'woo-bulk-editor',
                //'woot_products_tables' => 'profit-products-tables-for-woocommerce',
                //'func_name'=>'plugin_dir_name'
        );
        $this->notices_list = array_merge($this->notices_list, $alert_list);
    }

    public function init() {
        if (is_admin()) {
            //update_option('woof_alert', array());//reset, for dev purposes here
            if (get_option('woof_version') != WOOF_VERSION) { // if update plugin
                update_option('woof_version', WOOF_VERSION);

                $alert = (array) get_option('woof_alert', array());
                foreach ($this->notices_list as $key => $item) {
                    $alert[$key] = "";
                }

                add_option('woof_alert', $alert, '', 'no');
                update_option('woof_alert', $alert);
            }

            foreach ($this->notices_list as $key => $item) {
                if (file_exists(WP_PLUGIN_DIR . '/' . $item)) {
                    unset($this->notices_list[$key]);
                }
            }

            global $wp_version;
            if (version_compare($wp_version, '4.2', '>=') && current_user_can('install_plugins') && !empty($this->notices_list)) {
                $alert = (array) get_option('woof_alert', array());
                foreach ($this->notices_list as $key => $item) {
                    if (empty($alert[$key]) AND method_exists($this, 'alert_' . $key)) {
                        add_action('admin_notices', array($this, 'alert_' . $key));
                        add_action('network_admin_notices', array($this, 'alert_' . $key));
                    }
                }
                add_action('wp_ajax_woof_dismiss_alert', array($this, 'woof_dismiss_alert'));
                add_action('admin_enqueue_scripts', array($this, 'woof_alert_scripts'));
            }
        }
    }

    public function woof_dismiss_alert() {
        $alert = (array) get_option('woof_alert', array());
        $alert[sanitize_key($_POST['alert'])] = 1;

        add_option('woof_alert', $alert, '', 'no');
        update_option('woof_alert', $alert);

        exit;
    }

    public function woof_alert_scripts() {
        wp_enqueue_script('plugin-install');
        add_thickbox();
        wp_enqueue_script('updates');
    }

    //add functions
    public function alert_woocommerce_currency_switcher() {
        $screen = get_current_screen();
        ?>
        <div class="notice notice-info is-dismissible" id="woof_alert_woocs" style="display: none;">
            <p class="plugin-card-woocommerce-currency-switcher" <?php if ($screen->id != 'plugin-install'): ?>id="plugin-filter"<?php endif; ?>>
                <?php esc_html_e('For more marketing attraction of your woocommerce shop HUSKY team recommends you to install', 'woocommerce-products-filter') ?> <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=woocommerce-currency-switcher&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal" aria-label="HUSKY team recommends" data-title="WOOCS">WOOCS - WooCommerce Currency Switcher </a>.
                <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=woocommerce-currency-switcher&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal button" aria-label="More information about WOOCS" data-title="WOOCS" id="woof_alert_install_button"><?php esc_html_e('Install', 'woocommerce-products-filter') ?></a>
                <a class="install-now button" data-slug="woocommerce-currency-switcher" href="<?php echo network_admin_url('update.php?action=install-plugin') ?>&amp;plugin=woocommerce-currency-switcher&amp;_wpnonce=<?php echo wp_create_nonce('install-plugin-woocommerce-currency-switcher') ?>" aria-label="Install woocommerce currency switcher now" data-name="WOOCS - Woocommerce currency switcher" style="display:none"><?php esc_html_e('Install Now', 'woocommerce-products-filter') ?></a>
            </p>
        </div>
        <script>
            //DYNAMIC JAVASCRIPT
            jQuery('#woof_alert_woocs .open-plugin-details-modal').on('click', function () {
                jQuery('#woof_alert_install_button').hide().next().show();
                return true;
            });
            jQuery(function ($) {
                var alert_w = $('#woof_alert_woocs');
                alert_w.on('click', '.notice-dismiss', function (e) {
                    $.post(ajaxurl, {action: 'woof_dismiss_alert',
                        alert: 'woocommerce_currency_switcher',
                        sec: <?php echo json_encode(wp_create_nonce('woof_dissmiss_alert')) ?>
                    });
                });

        <?php if ($screen->id == 'plugin-install'): ?>
                    $('#plugin-filter').prepend(alert_w.css('margin-bottom', '10px').addClass('inline'));
        <?php endif ?>

                $(document).on('tb_unload', function () {
                    if (jQuery('#woof_alert_install_button').next().hasClass('updating-message'))
                        return;

                    jQuery('#woof_alert_install_button').show().next().hide();
                });
                $(document).on('credential-modal-cancel', function () {
                    jQuery('#woof_alert_install_button').show().next().hide();
                });
            });
        </script>
        <?php
        wp_print_request_filesystem_credentials_modal();
    }

    public function alert_woocommerce_bulk_editor() {
        $screen = get_current_screen();
        ?>
        <div class="notice notice-info is-dismissible" id="woof_alert_woobe" style="display: none;">
            <p class="plugin-card-woo-bulk-editor" <?php if ($screen->id != 'plugin-install'): ?>id="plugin-woobe"<?php endif; ?>>
                <?php esc_html_e('Try plugin for managing and BULK edit WooCommerce Products data in robust and flexible way', 'woocommerce-products-filter') ?>: <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=woo-bulk-editor&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal" aria-label="HUSKY team recommends" data-title="BEAR">BEAR - WooCommerce Bulk Editor Professional</a>.
                <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=woo-bulk-editor&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal button" aria-label="More information about BEAR" data-title="BEAR" id="woof_alert_install_button_woobe"><?php esc_html_e('Install', 'woocommerce-products-filter') ?></a>
                <a class="install-now button" data-slug="woo-bulk-editor" href="<?php echo network_admin_url('update.php?action=install-plugin') ?>&amp;plugin=woo-bulk-editor&amp;_wpnonce=<?php echo wp_create_nonce('install-plugin-woo-bulk-editor') ?>" aria-label="Install woocommerce bulk editor now" data-name="Woocommerce bulk editor" style="display:none"><?php esc_html_e('Install Now', 'woocommerce-products-filter') ?></a>
            </p>
        </div>
        <script>
            //DYNAMIC JAVASCRIPT
            jQuery('#woof_alert_woobe .open-plugin-details-modal').on('click', function () {
                jQuery('#woof_alert_install_button_woobe').hide().next().show();
                return true;
            });
            jQuery(function ($) {
                var alert_w = $('#woof_alert_woobe');
                alert_w.on('click', '.notice-dismiss', function (e) {
                    $.post(ajaxurl, {
                        action: 'woof_dismiss_alert',
                        alert: 'woocommerce_bulk_editor',
                        sec: <?php echo json_encode(wp_create_nonce('woof_dissmiss_alert')) ?>
                    });
                });

        <?php if ($screen->id == 'plugin-install'): ?>
                    $('#plugin-woobe').prepend(alert_w.css('margin-bottom', '10px').addClass('inline'));
        <?php endif ?>

                $(document).on('tb_unload', function () {
                    if (jQuery('#woof_alert_install_button_woobe').next().hasClass('updating-message'))
                        return;

                    jQuery('#woof_alert_install_button_woobe').show().next().hide();
                });
                $(document).on('credential-modal-cancel', function () {
                    jQuery('#woof_alert_install_button_woobe').show().next().hide();
                });
            });
        </script>
        <?php
        wp_print_request_filesystem_credentials_modal();
    }

    public function alert_woot_products_tables() {
        $screen = get_current_screen();
        ?>
        <div class="notice notice-info is-dismissible" id="woof_alert_woot" style="display: none;">
            <p class="plugin-card-profit-products-tables-for-woocommerce" <?php if ($screen->id != 'plugin-install'): ?>id="plugin-woot"<?php endif; ?>>
                <?php esc_html_e('Try HUSKY compatible plugin for displaying your WooCommerce shop products in table format', 'woocommerce-products-filter') ?>: <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=profit-products-tables-for-woocommerce&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal" aria-label="HUSKY team recommends" data-title="WOOT">WOOT - Active Products Tables for WooCommerce</a>.
                <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=profit-products-tables-for-woocommerce&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal button" aria-label="More information about WOOT" data-title="WOOT" id="woof_alert_install_button_woot"><?php esc_html_e('Install', 'woocommerce-products-filter') ?></a>
                <a class="install-now button" data-slug="woot-products-tables" href="<?php echo network_admin_url('update.php?action=install-plugin') ?>&amp;plugin=profit-products-tables-for-woocommerce&amp;_wpnonce=<?php echo wp_create_nonce('install-plugin-woot-products-tables') ?>" aria-label="Install woot now" data-name="Woocommerce woot" style="display:none"><?php esc_html_e('Install Now', 'woocommerce-products-filter') ?></a>
            </p>
        </div>
        <script>
            //DYNAMIC JAVASCRIPT
            jQuery('#woof_alert_woot .open-plugin-details-modal').on('click', function () {
                jQuery('#woof_alert_install_button_woot').hide().next().show();
                return true;
            });
            jQuery(function ($) {
                var alert_w = $('#woof_alert_woot');
                alert_w.on('click', '.notice-dismiss', function (e) {
                    $.post(ajaxurl, {
                        action: 'woof_dismiss_alert',
                        alert: 'woot_products_tables',
                        sec: <?php echo json_encode(wp_create_nonce('woof_dissmiss_alert')) ?>
                    });
                });

        <?php if ($screen->id == 'plugin-install'): ?>
                    $('#plugin-woot').prepend(alert_w.css('margin-bottom', '10px').addClass('inline'));
        <?php endif ?>

                $(document).on('tb_unload', function () {
                    if (jQuery('#woof_alert_install_button_woot').next().hasClass('updating-message'))
                        return;

                    jQuery('#woof_alert_install_button_woot').show().next().hide();
                });
                $(document).on('credential-modal-cancel', function () {
                    jQuery('#woof_alert_install_button_woot').show().next().hide();
                });
            });
        </script>
        <?php
        wp_print_request_filesystem_credentials_modal();
    }

}
