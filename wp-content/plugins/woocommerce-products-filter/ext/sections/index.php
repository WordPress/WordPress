<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_SECTIONS extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'sections'; //should be defined!!

    //https://refreshless.com/nouislider/
    //***

    public function __construct() {
        parent::__construct();
        $this->init();
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
        add_action('wp_ajax_woof_get_section_html', array($this, 'get_section_html'));

        add_action('woof_before_draw_filter', array($this, 'before_filter'), 99, 2);
        add_action('woof_after_draw_filter', array($this, 'after_filter'), 99, 2);
        self::$includes['js']['woof_sections_html_items'] = $this->get_ext_link() . 'js/sections.js';
        //woof_sections_html_items
        add_action('wp_head', array($this, 'wp_head'), 99);

        add_filter('woof_filter_shortcode_args', array($this, 'add_shortcode_attr'), 99);
    }

    public function wp_head() {
        wp_enqueue_style('woof_sections_style', $this->get_ext_link() . 'css/sections.css', [], WOOF_VERSION);
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-sections">
                <span class="icon-th"></span>
                <span><?php esc_html_e("Sections", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        wp_enqueue_script('woof_stections', $this->get_ext_link() . 'js/admin.js', [], WOOF_VERSION);
        
        $data = array();
        $data['ext_sections'] = $this;
        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function get_section_html() {
        ob_start();
        $this->woof_draw_sctions_item();
        $section = ob_get_clean();
        die($section);
    }

    public function woof_draw_sctions_item($ukey = "", $title = "", $from = -1, $to = -1) {
        if (!$ukey) {
            $ukey = uniqid("section");
        }
        
        $woof_settings = woof()->settings;

        $standard_filters = array(
            'by_price' => esc_html__("Search by Price", 'woocommerce-products-filter'),
            'by_rating' => esc_html__("By rating drop-down", 'woocommerce-products-filter'),
            'by_sku' => esc_html__("Search by SKU", 'woocommerce-products-filter'),
            'by_text' => esc_html__("Search by Text", 'woocommerce-products-filter'),
            'by_author' => esc_html__("Search by Author", 'woocommerce-products-filter'),
            'by_backorder' => esc_html__("Exclude products on backorder", 'woocommerce-products-filter'),
            'by_featured' => esc_html__("Featured checkbox", 'woocommerce-products-filter'),
            'by_instock' => esc_html__("In stock checkbox", 'woocommerce-products-filter'),
            'by_onsales' => esc_html__("On sale checkbox", 'woocommerce-products-filter'),
            'products_messenger' => esc_html__("Products Messenger", 'woocommerce-products-filter'),
            'query_save' => esc_html__("Save search query", 'woocommerce-products-filter'),
        );

        $options = array();
        $items_order = array();
        $taxonomies = woof()->get_taxonomies();
        $taxonomies_keys = array_keys($taxonomies);
        if (isset($woof_settings['items_order']) AND!empty($woof_settings['items_order'])) {
            $items_order = explode(',', $woof_settings['items_order']);
        } else {
            $items_order = array_merge(woof()->items_keys, $taxonomies_keys);
        }

//*** lets check if we have new taxonomies added in woocommerce or new item
        foreach (array_merge(woof()->items_keys, $taxonomies_keys) as $key) {
            if (!in_array($key, $items_order)) {
                $items_order[] = $key;
            }
        }

//lets print our items and taxonomies
        foreach ($items_order as $key) {
            if (in_array($key, woof()->items_keys)) {
                if (isset($woof_settings['meta_filter']) AND isset($woof_settings['meta_filter'][$key])) {
                    if (isset($woof_settings[$key]['show']) && $woof_settings[$key]['show'] != 0) {
                        $options[$key] = $woof_settings['meta_filter'][$key]['title'];
                    }
                } elseif (isset($standard_filters[$key])) {
                    if (isset($woof_settings[$key]['show']) && $woof_settings[$key]['show'] != 0) {
                        $options[$key] = $standard_filters[$key];
                    }
                } else {
                    if (isset($woof_settings[$key]['show']) && $woof_settings[$key]['show'] != 0) {
                        $options[$key] = $key;
                    }
                }
            } else {
                if (isset($taxonomies[$key])) {
                    if (isset($woof_settings['tax'][$key]) && $woof_settings['tax'][$key] != 0) {
                        $options[$key] = $taxonomies[$key]->label;
                    }
                }
            }
        }
        ?>
        <li class="woof_section_item" data-key='<?php echo esc_attr($ukey); ?>'>
            <input type="text" name="woof_settings[sections][<?php echo esc_attr($ukey); ?>][title]" value="<?php echo esc_html($title ? esc_html($title) : esc_html__("New section", 'woocommerce-products-filter')) ?>">
            <span><?php esc_html_e("from", 'woocommerce-products-filter'); ?></span>
            <select class="woof_section_from" name="woof_settings[sections][<?php echo esc_attr($ukey); ?>][from]">
                <?php foreach ($options as $type => $title) { ?>
                    <option <?php selected($type == $from, $type) ?> value="<?php echo esc_attr($type); ?>"><?php esc_html_e($title) ?></option>
                <?php } ?>
            </select>
            <span><?php esc_html_e("to", 'woocommerce-products-filter'); ?></span>
            <select class="woof_section_to" name="woof_settings[sections][<?php echo esc_attr($ukey); ?>][to]">
                <?php foreach ($options as $type => $title) { ?>
                    <option <?php selected($type == $to, $type) ?> value="<?php echo esc_attr($type); ?>"><?php esc_html_e($title) ?></option>
                <?php } ?>
            </select>	
            <input type="button" value="X" class="woof_sections_delete woof-button"data-key='<?php echo esc_attr($ukey); ?>'>
        </li>
        <?php
    }

    public function generate_shortcode_attr($sections) {
        $attr = array();
        foreach ($sections as $key => $item) {
            $attr[] = $item['from'] . '+' . $item['to'] . '^' . $item['title'];
        }
        return implode(',', $attr);
    }

    public function add_shortcode_attr($attr) {
        
        $settings = woof()->settings;
        if (!isset($attr['shortcode_atts']['sections']) && isset($settings['woof_init_sections']) && $settings['woof_init_sections'] == 1) {
            $attr['shortcode_atts'] = array();
            if (isset($settings['sections']) && is_array($settings['sections'])) {
                $attr['shortcode_atts']['sections'] = $this->generate_shortcode_attr($settings['sections']);
            }
            if (isset($settings['sections_type']) && $settings['sections_type']) {
                $attr['shortcode_atts']['sections_type'] = $settings['sections_type'];
            }
        }
        return $attr;
    }

    //https://codepen.io/milesmanners/pen/QEQPjw
    //https://codepen.io/RyanNHG/pen/XVJzVY
    //https://codepen.io/yo_i_am_cuban_b/pen/QWNvGxj

    public function before_filter($key, $shortcode_attr) {
        
        if (isset($shortcode_attr['sections']) && $shortcode_attr['sections']) {
            $sections = explode(',', $shortcode_attr['sections']);
            $type = 'tabs_checkbox';
            if (isset($shortcode_attr['sections_type'])) {
                $type = $shortcode_attr['sections_type'];
            }
            $all_sections = array();
            $count = 0;
            foreach ($sections as $item) {
                $explode = explode('+', $item);
                if (trim($explode[0]) == $key && isset($explode[1])) {
                    $explode_2 = explode('^', $explode[1]);
                    if (isset($explode_2[1]) && $explode_2[1]) {
                        $data['key'] = $explode[0];
                        $data['title'] = $explode_2[1];
                        $data['type'] = $type;
                        $data['checked'] = false;

                        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . 'start.php')) {
                            woof()->render_html_e($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . 'start.php', $data);
                        }
                        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . 'start.php', $data);
                    }
                }
                $count++;
            }
        }
    }

    public function after_filter($key, $shortcode_attr) {
        
        if (isset($shortcode_attr['sections']) && $shortcode_attr['sections']) {
            $sections = explode(',', $shortcode_attr['sections']);
            foreach ($sections as $item) {
                $explode = explode('+', $item);

                if (isset($explode[1])) {
                    $explode_2 = explode('^', $explode[1]);

                    if ($explode_2[0] == $key && isset($explode_2[1])) {
                        $data['key'] = $explode_2[0];
                        $data['title'] = $explode_2[1];
                        $data['type'] = 'tabs';
                        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . 'end.php')) {
                            woof()->render_html_e($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . 'end.php', $data);
                        }
                        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'sections' . DIRECTORY_SEPARATOR . 'end.php', $data);
                    }
                }
            }
        }
    }

}

WOOF_EXT::$includes['applications']['sections'] = new WOOF_EXT_SECTIONS();
