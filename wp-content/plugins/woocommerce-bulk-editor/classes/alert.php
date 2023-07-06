<?php

class WOOBE_ADV {

    public $notices_list = array();

    public function __construct($alert_list = array()) {

        //fix to avoid disabling of 'Upload Theme' button action on /wp-admin/theme-install.php
        if (isset($_SERVER['REQUEST_URI'])) {
            if (substr_count($_SERVER['REQUEST_URI'], 'theme-install.php')) {
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
            'woot_products_tables' => 'profit-products-tables-for-woocommerce',
                //'func_name'=>'plugin_dir_name'
        );
        $this->notices_list = array_merge($this->notices_list, $alert_list);
    }

    public function init() {
        if (is_admin()) {
            //update_option('woobe_alert', array());//reset
            if (get_option('woobe_version') != WOOBE_VERSION) {// if update plugin
                update_option('woobe_version', WOOBE_VERSION);

                $alert = (array) get_option('woobe_alert', array());

                foreach ($this->notices_list as $key => $item) {
                    $alert[$key] = "";
                }

                add_option('woobe_alert', $alert, '', 'no');
                update_option('woobe_alert', $alert);
            }

            foreach ($this->notices_list as $key => $item) {
                if (file_exists(WP_PLUGIN_DIR . '/' . $item)) {
                    unset($this->notices_list[$key]);
                }
            }

            global $wp_version;
            if (version_compare($wp_version, '4.2', '>=') && current_user_can('install_plugins') && !empty($this->notices_list)) {
                $alert = (array) get_option('woobe_alert', array());
                foreach ($this->notices_list as $key => $item) {
                    if (empty($alert[$key]) AND method_exists($this, 'alert_' . $key)) {
                        add_action('admin_notices', array($this, 'alert_' . $key));
                        add_action('network_admin_notices', array($this, 'alert_' . $key));
                    }
                }
                add_action('wp_ajax_woobe_dismiss_alert', array($this, 'woobe_dismiss_alert'));
                add_action('admin_enqueue_scripts', array($this, 'woobe_alert_scripts'));

                //enqueue admin/js/updates.js
            }
        }
    }

    public function woobe_dismiss_alert() {
        // check_ajax_referer('woobe_dissmiss_alert', 'sec');

        $alert = (array) get_option('woobe_alert', array());
        $alert[$_POST['alert']] = 1;

        add_option('woobe_alert', $alert, '', 'no');
        update_option('woobe_alert', $alert);

        exit;
    }

    public function woobe_alert_scripts() {
        wp_enqueue_script('plugin-install');
        add_thickbox();
        wp_enqueue_script('updates');
    }

    public function alert_woot_products_tables() {
        if (isset($_GET['page']) AND $_GET['page'] === 'woobe') {
            $screen = get_current_screen();
            ?>
            <div class="notice notice-info is-dismissible" id="woobe_alert_woot">
                <p class="plugin-card-profit-products-tables-for-woocommerce"<?php if ($screen->id != 'plugin-install') echo ' id="plugin-woot"' ?>>
                    <?php esc_html_e("Try new BEAR compatible plugin for displaying WooCommerce shop products in table format", 'woocommerce-bulk-editor') ?>: <a href="https://products-tables.com/" target="_blank">WOOT - WooCommerce Active Products Tables</a> (ACTIVE).
                    <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=profit-products-tables-for-woocommerce&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal button" aria-label="More information about WOOT" data-title="WOOT" id="woobe_alert_install_button_woot"><?php esc_html_e("Install", 'woocommerce-bulk-editor') ?></a>
                    <a class="install-now button" data-slug="profit-products-tables-for-woocommerce" href="<?php echo network_admin_url('update.php?action=install-plugin') ?>&amp;plugin=profit-products-tables-for-woocommerce&amp;_wpnonce=<?php echo wp_create_nonce('install-profit-products-tables-for-woocommerce') ?>" aria-label="Install WOOT now" data-name="WOOT" style="display:none"><?php esc_html_e("Install Now", 'woocommerce-bulk-editor') ?></a>
                </p>
            </div>
            <script>
                jQuery('#woobe_alert_woot .open-plugin-details-modal').on('click', function () {
                    jQuery('#woobe_alert_install_button_woot').hide().next().show();
                    return true;
                });
                jQuery(function ($) {
                    var alert_w = $('#woobe_alert_woot');
                    alert_w.on('click', '.notice-dismiss', function (e) {
                        //e.preventDefault 

                        $.post(ajaxurl, {
                            action: 'woobe_dismiss_alert',
                            alert: 'woot_products_tables',
                            sec: <?php echo json_encode(wp_create_nonce('woobe_dissmiss_alert')) ?>
                        });
                    });

            <?php if ($screen->id == 'plugin-install'): ?>
                        $('#plugin-woot').prepend(alert_w.css('margin-bottom', '10px').addClass('inline'));
            <?php endif ?>

                    $(document).on('tb_unload', function () {
                        if (jQuery('#woobe_alert_install_button_woot').next().hasClass('updating-message'))
                            return;

                        jQuery('#woobe_alert_install_button_woot').show().next().hide();
                    });
                    $(document).on('credential-modal-cancel', function () {
                        jQuery('#woobe_alert_install_button_woot').show().next().hide();
                    });
                });
            </script>
            <?php
            wp_print_request_filesystem_credentials_modal();
        }
    }

    public function alert_bulk_editor() {
        $screen = get_current_screen();
        ?>
        <div class="notice notice-info is-dismissible" id="woobe_alert_wpbe">
            <p class="plugin-card-bulk-editor"<?php if ($screen->id != 'plugin-install') echo ' id="plugin-woobe"' ?>>
                If you need bulk editor plugin for your site posts and pages try <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=bulk-editor&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal" aria-label="BEAR team recommends" data-title="WPBE - WordPress Posts Bulk Editor Professional">WPBE - WordPress Posts Bulk Editor Professional</a> - plugin for managing and bulk edit WordPress posts, pages and custom post types data in robust and flexible way!
                <a href="<?php echo network_admin_url('plugin-install.php?tab=plugin-information') ?>&amp;plugin=bulk-editor&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal button" aria-label="More information about WPBE" data-title="WPBE" id="woobe_alert_install_button_wpbe"><?php esc_html_e("Install", 'woocommerce-bulk-editor') ?></a>
                <a class="install-now button" data-slug="bulk-editor" href="<?php echo network_admin_url('update.php?action=install-plugin') ?>&amp;plugin=bulk-editor&amp;_wpnonce=<?php echo wp_create_nonce('install-plugin-bulk-editor') ?>" aria-label="Install wordpress bulk editor now" data-name="Posts bulk editor" style="display:none"><?php esc_html_e("Install Now", 'woocommerce-bulk-editor') ?></a>
            </p>
        </div>
        <script>
            jQuery('#woobe_alert_wpbe .open-plugin-details-modal').on('click', function () {
                jQuery('#woobe_alert_install_button_wpbe').hide().next().show();
                return true;
            });
            jQuery(function ($) {
                var alert_w = $('#woobe_alert_wpbe');
                alert_w.on('click', '.notice-dismiss', function (e) {
                    //e.preventDefault 

                    $.post(ajaxurl, {
                        action: 'woobe_dismiss_alert',
                        alert: 'bulk_editor',
                        sec: <?php echo json_encode(wp_create_nonce('woobe_dissmiss_alert')) ?>
                    });
                });

        <?php if ($screen->id == 'plugin-install'): ?>
                    $('#plugin-woobe').prepend(alert_w.css('margin-bottom', '10px').addClass('inline'));
        <?php endif ?>

                $(document).on('tb_unload', function () {
                    if (jQuery('#woobe_alert_install_button_wpbe').next().hasClass('updating-message'))
                        return;

                    jQuery('#woobe_alert_install_button_wpbe').show().next().hide();
                });
                $(document).on('credential-modal-cancel', function () {
                    jQuery('#woobe_alert_install_button_wpbe').show().next().hide();
                });
            });
        </script>
        <?php
        wp_print_request_filesystem_credentials_modal();
    }

}
