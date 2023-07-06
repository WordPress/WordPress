<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_COLOR extends WOOF_EXT {

    public $type = 'html_type';
    public $html_type = 'color'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi';

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->html_type . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {
        add_filter('woof_add_html_types', array($this, 'woof_add_html_types'));
        add_action('wp_enqueue_scripts', array($this, 'wp_head'), 9);
        add_action('woocommerce_settings_tabs_woof', array($this, 'woocommerce_settings_tabs_woof'), 51);
        add_action('woof_print_tax_additional_options_color', array($this, 'print_additional_options'), 10, 1);
        add_action('woof_print_design_additional_options', array($this, 'woof_print_design_additional_options'), 10, 1);
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/html_types/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/html_types/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_colors';

        add_action('admin_head', array($this, 'admin_head'), 50);

        $this->taxonomy_type_additional_options = array(
            'show_tooltip' => array(
                'title' => esc_html__('Tooltip text', 'woocommerce-products-filter'),
                'tip' => esc_html__('Enter tooltip text if necessary', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    1 => esc_html__('Yes', 'woocommerce-products-filter'),
                    0 => esc_html__('No', 'woocommerce-products-filter')
                )
            ),
            'show_title_column' => array(
                'title' => esc_html__('Show in one column', 'woocommerce-products-filter'),
                'tip' => esc_html__('Show in one column with title', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter'),
                )
            ),
            'as_radio_color' => array(
                'title' => esc_html__('Behavior as radio button', 'woocommerce-products-filter'),
                'tip' => esc_html__('Use image as radio button', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter'),
                )
            ),
        );
    }

    public function admin_head() {
        if (isset($_GET['tab']) AND $_GET['tab'] == 'woof') {
            wp_enqueue_style('woof_color', $this->get_ext_link() . 'css/admin.css', array(), WOOF_VERSION);
            wp_enqueue_script('woof_color', $this->get_ext_link() . 'js/html_types/plugin_options.js', array('jquery'), WOOF_VERSION);
        }
    }

    public function woof_add_html_types($types) {
        $types[$this->html_type] = esc_html__('Color', 'woocommerce-products-filter');
        return $types;
    }

    public function woocommerce_settings_tabs_woof() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    public function wp_head() {

        self::$includes['css_code_custom'][$this->index] = $this->get_style();
    }

    public function get_style() {
        
        ob_start();
        if (isset(woof()->settings['checked_color_img'])) {
            if (!empty(woof()->settings['checked_color_img'])) {
                ?>
                .checked .woof_color_checked{
                background: url(<?php echo esc_url(woof()->settings['checked_color_img']) ?>) !important;
                } 
                <?php
            }
        }
        return ob_get_clean();
    }

    public function print_additional_options($key) {
        
        $woof_settings = woof()->settings;
        $terms = WOOF_HELPER::get_terms($key, 0, 0, 0, 0);
        if (!empty($terms)) {
            ?>
            <br /><a href="javascript:void(0);" class="button woof-button-outline-secondary woof_toggle_colors"><?php esc_html_e('toggle color terms', 'woocommerce-products-filter') ?></a><br />
            <ul class="woof_color_list woof_hide_options">
                <?php
                foreach ($terms as $t) {
                    $color = '#000000';
                    if (isset($woof_settings['color'][$key][$t['slug']])) {
                        $color = $woof_settings['color'][$key][$t['slug']];
                    }

                    $color_img = '';
                    if (isset($woof_settings['color_img'][$key][$t['slug']])) {
                        $color_img = $woof_settings['color_img'][$key][$t['slug']];
                    }
                    ?>
                    <li>
                        <table>
                            <tr>
                                <td valign="top">
                                    <input type="text" name="woof_settings[color][<?php echo esc_attr($key) ?>][<?php echo esc_attr($t['slug']) ?>]" value="<?php echo esc_html($color) ?>" id="woof_color_picker_<?php echo esc_attr($t['slug']) ?>" class="woof-color-picker" >
                                </td>
                                <td>
                                    <input type="text" name="woof_settings[color_img][<?php echo esc_attr($key) ?>][<?php echo esc_attr($t['slug']) ?>]" value="<?php echo esc_url($color_img) ?>" placeholder="<?php esc_html_e('background image url 25x25', 'woocommerce-products-filter') ?>" class="text" style="width: 600px;" />
                                    <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                                </td>
                                <td class="woof_fix8">
                                    <p class="description"> [ <?php esc_html_e(WOOF_HELPER::strtolower($t['name'])) ?> ]</p>
                                </td>
                            </tr>
                        </table>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
    }

    public function woof_print_design_additional_options() {
        
        $woof_settings = woof()->settings;

        if (!isset($woof_settings['checked_color_img'])) {
            $woof_settings['checked_color_img'] = '';
        }
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['color'] = new WOOF_EXT_COLOR();
