<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXP_IMP extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'export_import'; //should be defined!!

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {
        add_action('wp_ajax_woof_get_export_data', array($this, 'get_export_data'));
        add_action('wp_ajax_woof_do_import_data', array($this, 'do_import_data'));

        add_action('woof_print_applications_tabs_anvanced', array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_advanced', array($this, 'woof_print_applications_tabs_content'), 10, 1);
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-export_import">
                <span class="icon-export"></span>
                <span><?php esc_html_e("Export/Import", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        wp_register_script('woof_imp_exp', $this->get_ext_link() . 'js/admin.js');
        wp_localize_script('woof_imp_exp', 'woof_imp_exp_vars', array(
            'sure' => esc_html__("Are you sure? Settings will be overwritten!", 'woocommerce-products-filter'),
            'empty' => esc_html__("No data to import!", 'woocommerce-products-filter'),
        ));

        wp_enqueue_script('woof_imp_exp', array('jquery', 'jquery-ui-core'));

        $data['options'] = $this->get_all_options();
        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function get_all_options() {
        global $wpdb;

        $rows = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'woof_%'");

        $options = array();
        $exclude = array('woof_alert', 'woof_alert_rev', 'woof_version');
        foreach ($rows as $item) {
            if (!in_array($item->option_name, $exclude)) {

                $options[$item->option_name] = get_option($item->option_name, '');
            }
        }
        return json_encode($options);
    }

    public function get_export_data() {
        if (!wp_verify_nonce($_REQUEST['_nonce'], 'woof_export_settings')) {
            die(json_encode(array()));
        }

        die($this->get_all_options());
    }

    public function do_import_data() {

        if (!isset($_POST['import_value'])) {
            die(esc_html__("Error! No data", 'woocommerce-products-filter'));
        }
        if (!wp_verify_nonce($_REQUEST['_nonce'], 'woof_import_settings')) {
            die(esc_html__("Error! Security issue", 'woocommerce-products-filter'));
        }

        try {
            $options = wc_clean(json_decode(stripcslashes($_POST['import_value']), true));

            foreach ($options as $option_name => $option_data) {
                update_option($option_name, $option_data);
            }

            die(esc_html__("Settings imported successfully. Reload the page please.", 'woocommerce-products-filter'));
        } catch (Exception $e) {
            die(esc_html__("Error!", 'woocommerce-products-filter'));
        }
    }

}

WOOF_EXT::$includes['applications']['export_import'] = new WOOF_EXP_IMP();
