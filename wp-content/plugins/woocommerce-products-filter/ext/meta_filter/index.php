<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_META_FILTER extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'meta_filter'; //should be defined!!
    //***
    protected $excluded_meta = array();
    protected $meta_keys = array();
    //***
    public $meta_filters_obj = array();
    public $meta_filter_types = array();

    //***
    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->html_type . DIRECTORY_SEPARATOR;
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {

        require_once $this->get_ext_path() . 'classes/woof_type_meta_filter.php';

        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);
        add_action('wp_footer', array($this, 'wp_footer'), 12);
        //ajax
        add_action('wp_ajax_woof_meta_get_keys', array($this, 'woof_meta_get_keys'));

        // Create meta query
        add_filter('woof_get_meta_query', array($this, 'woof_get_meta_query'));

        //option to add filter type 
        $this->meta_filter_types = array(
            'slider' => array(
                'key' => 'slider',
                'title' => esc_html__('Slider', 'woocommerce-products-filter'),
                'hide_if' => array('string', 'DATE'),
                'show_options' => false,
            ),
            'textinput' => array(
                'key' => 'textinput',
                'title' => esc_html__('Search by text', 'woocommerce-products-filter'),
                'hide_if' => array('DATE'),
                'show_options' => false,
            ),
            'checkbox' => array(
                'key' => 'checkbox',
                'title' => esc_html__('Checkbox', 'woocommerce-products-filter'),
                'hide_if' => array('DATE'),
                'show_options' => false,
            ),
            'select' => array(
                'key' => 'select',
                'title' => esc_html__('Drop-down', 'woocommerce-products-filter'),
                'hide_if' => array('DATE'),
                'show_options' => true,
            ),
            'mselect' => array(
                'key' => 'mselect',
                'title' => esc_html__('Multi Drop-down', 'woocommerce-products-filter'),
                'hide_if' => array('DATE'),
                'show_options' => true,
            ),
            'datepicker' => array(
                'key' => 'datepicker',
                'title' => esc_html__('Datepicker', 'woocommerce-products-filter'),
                'hide_if' => array('string'),
                'show_options' => false,
            ),
        );

        $this->meta_filter_types = apply_filters('woof_meta_filter_add_types', $this->meta_filter_types);
        
        if (isset($this->woof_settings['meta_filter']) AND is_array($this->woof_settings['meta_filter'])) {
            $counter = 0;
            foreach ($this->woof_settings['meta_filter'] as $key => $val) {
                if ($key == "__META_KEY__") {
                    continue;
                }

                if (intval(WOOF_VERSION) === 1) {
                    if ($counter++ >= 2) {
                        break;
                    }
                }

                $this->meta_keys[] = $val['meta_key'];
                $this->conect_activate_meta_filter($key, $val);
            }
        }
        //add meta items to structure
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
    }

    public function conect_activate_meta_filter($key, $options) {
        $class_name = 'WOOF_META_FILTER_' . strtoupper($options['search_view']);
        require_once $this->get_ext_path() . 'html_types/' . $options['search_view'] . '/index.php';
        if (class_exists($class_name)) {
            $this->meta_filters_obj[$key] = new $class_name($key, $options, $this->woof_settings);
            self::$includes['js_init_functions']["meta_" . $options['search_view']] = $this->meta_filters_obj[$key]->get_js_func_name();
        }
    }

    public function wp_footer() {
        
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-meta-filter">
                <span class="icon-flow-cross"></span>
                <span><?php esc_html_e("Meta Data", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        require_once $this->get_ext_path() . 'classes/woof_pds_cpt.php';
        if (class_exists('WOOF_PDS_CPT', false)) {
            $pds_cpt = new WOOF_PDS_CPT();
            $this->excluded_meta = array_merge($pds_cpt->get_internal_meta_keys(), $this->excluded_meta);
        }
        wp_enqueue_script('woof_qs_admin', $this->get_ext_link() . 'js/admin.js', array(), WOOF_VERSION);
        //***
        
        $data = array();

        $data['woof_settings'] = $this->woof_settings;
        $data['meta_types'] = $this->meta_filter_types;
        $data['metas'] = (isset($data['woof_settings']['meta_filter'])) ? $data['woof_settings']['meta_filter'] : array();

        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    // +++
    //ajax
    public function woof_meta_get_keys() {
        $res = '';

        require_once $this->get_ext_path() . 'classes/woof_pds_cpt.php';
        if (class_exists('WOOF_PDS_CPT', false)) {
            $pds_cpt = new WOOF_PDS_CPT();
            $this->excluded_meta = array_merge($pds_cpt->get_internal_meta_keys(), $this->excluded_meta);
        }
        
        $product_id = intval(WOOF_REQUEST::get('product_id'));//data from AJAX request

        if ($product_id > 0) {
			$meta = get_post_meta($product_id, '');
			if(!is_array($meta)){
				$meta = array();
			}
            $a1 = array_keys($meta);
            $res = array_diff($a1, $this->excluded_meta);
        }

        die(json_encode(array_values($res)));
    }

    public function woof_add_items_keys($arr_keys) {
        if (!empty($this->meta_keys)) {
            $arr_keys = array_merge($arr_keys, $this->meta_keys);
        }
        return $arr_keys;
    }

    public function woof_print_html_type_options_meta() {//old
        $key = "";
        $key = str_replace('woof_print_html_type_options_', "", current_filter());
        
        ?>
        <li data-key="<?php echo esc_attr($key) ?>" class="woof_options_li">

            <?php
            $show = 0;
            if (isset($this->woof_settings[$key]['show'])) {
                $show = $this->woof_settings[$key]['show'];
            }
            ?>

            <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drop", 'woocommerce-products-filter'); ?>"></span>

            <strong class="woof_fix1"><?php echo esc_html($this->woof_settings['meta_filter'][$key]['title']) ?>:</strong>


            <span class="icon-question help_tip" data-tip="<?php esc_html_e('Meta filter', 'woocommerce-products-filter') ?>"></span>

            <div class="select-wrap">
                <select name="woof_settings[<?php echo esc_attr($key) ?>][show]" class="woof_setting_select">
                    <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                    <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
                </select>
            </div>
            <a href="#" data-key="<?php echo esc_attr($key) ?>" data-name="<?php echo esc_html($this->woof_settings['meta_filter'][$key]['title']) ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>
                <?php
                $data = array();
                $data['key'] = $key;
                $data['settings'] = $this->woof_settings;
                woof()->render_html_e($this->get_ext_path() . 'html_types/' . $this->woof_settings['meta_filter'][$key]['search_view'] . '/views/additional_options.php', $data);
                ?>      
        </li>
        <?php
    }

    public function woof_get_meta_query($meta_query) {
        $meta_filter_query = array();
        foreach ($this->meta_filters_obj as $obj) {
            $meta = $obj->create_meta_query();
            if ($meta) {
                $meta_filter_query[] = $meta;
            }
        }
        if (!empty($meta_filter_query)) {
            $meta_filter_query['relation'] = 'AND';
            $meta_query = array_merge($meta_query, $meta_filter_query);
        }

        return $meta_query;
    }

    //compatibility with other extensions
    public static function get_meta_filter_name($request_key) {
        
        foreach (woof()->settings['meta_filter'] as $item) {
            $key = $item['search_view'] . "_" . $item['meta_key'];
            if ($key == $request_key) {
                return WOOF_HELPER::wpml_translate(null, $item['title']);
            }
        }
        return false;
    }

    //compatibility with other extensions
    public static function get_meta_filter_option_name($request_key, $request_val) {
        
        $option_name = "";
        foreach (woof()->settings['meta_filter'] as $item) {
            $key = $item['search_view'] . "_" . $item['meta_key'];
            if ($key == $request_key) {
                $class_name = "WOOF_META_FILTER_" . strtoupper($item['search_view']);
                if (class_exists($class_name)) {
                    $option_name = $class_name::get_option_name($request_val, $request_key);
                    return WOOF_HELPER::wpml_translate(null, $option_name);
                }
            }
        }

        return false;
    }

    // get html for  messenger
    public static function get_meta_title_messenger($request_val, $request_key) {
        $html = "";
        $title = self::get_meta_filter_name($request_key);
        $option = self::get_meta_filter_option_name($request_key, $request_val);
        if (!$title) {
            return $html;
        }
        $html = $title;
        if ($option) {
            $html .= ":" . $option;
        }
        return "<span class='woof_terms'>" . $html . "</span><br />";
    }

}

WOOF_EXT::$includes['applications']['meta_filter'] = new WOOF_META_FILTER();
