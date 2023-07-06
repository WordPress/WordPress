<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_CONDITIONALS extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'conditionals';
    public $html_type_dynamic_recount_behavior = 'none';
    public $search_key = array();
    public $options = array();

    public function __construct() {
        parent::__construct();
        $this->init();
        $this->search_key = array(
            'stock' => 'by_instock',
            'onsales' => 'by_onsales',
            'min_rating' => 'by_rating',
            "min_price" => 'by_price',
            'woof_author' => 'by_author',
            'woof_text' => 'by_text',
            'product_visibility' => 'by_featured',
            'woof_sku' => 'by_sku',
            'backorder' => 'by_backorder'
        );
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->folder_name . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {
        add_action('woof_print_applications_tabs_anvanced', array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_advanced', array($this, 'woof_print_applications_tabs_content'), 10, 1);

        add_filter('woof_filter_shortcode_args', array($this, 'check_conditionals'));

        $this->options = array();
    }

    public function wp_head() {
        
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-conditionals">
                <span class="icon-air"></span>
                <span><?php esc_html_e("Сonditionals", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function check_conditionals($attrs) {
        
        if (!isset($attrs['shortcode_atts']['conditionals']) AND (isset(woof()->settings['woof_conditionals']) AND woof()->settings['woof_conditionals'])) {
            if (!isset($attrs['shortcode_atts']) OR!is_array($attrs['shortcode_atts'])) {
                $attrs['shortcode_atts'] = array();
            }

            $attrs['shortcode_atts']['conditionals'] = woof()->settings['woof_conditionals'];
        }
        if (isset($attrs['shortcode_atts']['conditionals'])) {

            ///'tax_exclude'
            $terms = $this->generate_conditional_attr(preg_replace('/&(amp;)?#\d+;/', '', $attrs['shortcode_atts']['conditionals']));

            $attrs['tax_exclude'] = $this->get_all_keys($terms);
            $request = woof()->get_request_data();
            $request = array_keys($request);

            foreach ($request as $key_r => $item_r) {
                $request[$key_r] = str_replace("rev_", "", $item_r);
                if (isset($this->search_key[$item_r])) {
                    $request[$key_r] = $this->search_key[$item_r];
                }
                $index = array_search($request[$key_r], $attrs['tax_exclude']);
                if (false !== $index) {
                    unset($attrs['tax_exclude'][$index]);
                }
            }
            foreach ($terms as $terms_item) {
                foreach ($terms_item as $item) {
                    $res = array_intersect($request, $item);
                    $attrs['tax_exclude'] = array_diff($attrs['tax_exclude'], $item);
                    if (!count($res)) {
                        break;
                    }
                }
            }
        }

        return $attrs;
    }

    public function woof_print_applications_tabs_content() {
        //***
        
        $data = array();

        $data['woof_settings'] = $this->woof_settings;
        $data['search_key'] = $this->search_key;

        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function generate_conditional_attr($string) {
        $conditionals = array();

        $string = str_replace(PHP_EOL, "+", $string);

        $steps = explode('+', $string);
        foreach ($steps as $key => $step) {
            $step = explode('>', trim($step, "\x00..\x1F"));
            foreach ($step as $s_key => $items) {
                $conditionals[$key][$s_key] = explode(',', $items);
                array_map('trim', $conditionals[$key][$s_key]);
            }
        }

        return $conditionals;
    }

    public function get_all_keys($terms, $all_terms = false) {
        $names = array();
        foreach ($terms as $items) {
            foreach ($items as $key => $item) {
                if ($key == 0 AND!$all_terms) {
                    continue;
                }
                $names = array_merge($names, $item);
            }
        }

        return $names;
    }

}

WOOF_EXT::$includes['applications']['conditionals'] = new WOOF_CONDITIONALS();

