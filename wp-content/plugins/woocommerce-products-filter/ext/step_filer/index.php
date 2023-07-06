<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_STEP_FILTER extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'step_filter'; //should be defined!!

    public function __construct() {
        parent::__construct();
        add_shortcode("woof_step", array($this, 'woof_step'));
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }
    public function get_ext_override_path()
    {
        return get_stylesheet_directory(). DIRECTORY_SEPARATOR ."woof". DIRECTORY_SEPARATOR ."ext". DIRECTORY_SEPARATOR .$this->folder_name. DIRECTORY_SEPARATOR;
    }
    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {

        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);
        self::$includes['css']['woof_' . $this->folder_name . '_html_items'] = $this->get_ext_link() . 'css/' . $this->folder_name . '.css';
        self::$includes['js']['woof_step_filter_html_items'] = $this->get_ext_link() . 'js/step_filter.js';
        self::$includes['js_init_functions'][$this->folder_name] = 'woof_step_filter_html_items';
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-stat">
                <span class="icon-cog-outline"></span>
                <span><?php esc_html_e("Step by step filter", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        $data = array();        
        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function woof_step($args) {
        $shortcode_txt = "";
        if (isset($args['taxonomies'])) {
            $shortcode_txt .= " taxonomies=" . $args['taxonomies'];
        }
        if (isset($args['tax_only'])) {
            $shortcode_txt .= " tax_only=" . $args['tax_only'];
        }

        if (isset($args['tax_exclude'])) {
            $shortcode_txt .= " tax_exclude=" . $args['tax_exclude'];
        }

        if (isset($args['by_only'])) {
            $shortcode_txt .= " by_only=" . $args['by_only'];
        }

        if (isset($args['autohide'])) {
            $shortcode_txt .= " autohide=" . $args['autohide'];
        }

        if (isset($args['redirect'])) {
            $shortcode_txt .= " redirect=" . $args['redirect'];
        }

        if (isset($args['sid'])) {
            $shortcode_txt .= " sid=" . $args['sid'];
        }
        if (isset($args['dynamic_recount'])) {
            $shortcode_txt .= " dynamic_recount=" . $args['dynamic_recount'];
        }
        if (isset($args["by_step"])) {
            $shortcode_txt .= " by_step=" . $args["by_step"];
        } else {
            $shortcode_txt .= " by_step=''";
        }

        $data["hide"] = 1;
        if (isset($args['hide'])) {
            $data["hide"] = $args['hide'];
        }

        $data["autosubmit"] = 0;
        if (isset($args['autosubmit'])) {
            $data["autosubmit"] = $args['autosubmit'];
        }


        $data["next_btn_txt"] = esc_html__('Next', 'woocommerce-products-filter');
        if (isset($args["next_btn_txt"])) {
            $data["next_btn_txt"] = $args["next_btn_txt"];
        }
        $data["prev_btn_txt"] = esc_html__('Back', 'woocommerce-products-filter');
        if (isset($args["prev_btn_txt"])) {
            $data["prev_btn_txt"] = $args["prev_btn_txt"];
        }
        $data["filter_type"] = 1;
        if (isset($args["filter_type"])) {
            $data["filter_type"] = intval($args["filter_type"]);
            if ($data["filter_type"] > 2) {
                $data["filter_type"] = 2;
            }
        }
        
        $data["selector"] = ".woof_step";
        if (isset($args["selector"])) {
            $data["selector"] = $args["selector"];
        } 
        $data["img_behavior"] = "append";
        if (isset($args["img_behavior"])) {
            $data["img_behavior"] = $args["img_behavior"];
        } 
        
        $data["images"] = array();
        if (isset($args['images'])) {
            $size=apply_filters("woof_step_filter_img_size",'thumbnail');
            $image_arr= explode(",",$args['images']);
            foreach($image_arr as $image_item){
                $image_item=explode(":",$image_item,2);
                if(count($image_item)==2){
                    $url=wp_get_attachment_image_url(intval($image_item[1]),$size);
                    if($url){
                        $data["images"][trim($image_item[0])]= '<img src="'.$url.'" class="woof_step_filter_image woof_step_filter_image_'.trim($image_item[0]).'" >';
                    }
                }
                
            }

        }


        $data["shortcode_woof"] = "[woof ajax_redraw=1 autosubmit=0  " . $shortcode_txt . "]";

        
        if(file_exists($this->get_ext_override_path(). 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'step_filter.php')){
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'step_filter.php', $data);
        }           
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcode' . DIRECTORY_SEPARATOR . 'step_filter.php', $data);
    }

}

WOOF_EXT::$includes['applications']['step_filter'] = new WOOF_EXT_STEP_FILTER();
