<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_SLIDEOUT extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'slideout';
    public $html_type_dynamic_recount_behavior = 'none';
    public $options = array();

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
        add_action('wp_footer', array($this, 'wp_footer'), 10);
        add_shortcode("woof_slideout", array($this, "woof_slideout"));

        add_action('wp_ajax_woof_slideout_shortcode_gen', array($this, 'generate_shortcode_ajax'));

        $this->options = array(
            'slideout_img' => array(
                'type' => 'textinput',
                'default' => 'woo2',
                'title' => esc_html__('Image', 'woocommerce-products-filter'),
                'placeholder' => 'Select image',
                'description' => esc_html__('', 'woocommerce-products-filter')
            )
        );
    }

    public function wp_head() {
        
    }

    public function wp_footer() {
        wp_enqueue_script('woof-slideout-js', $this->get_ext_link() . 'js/jquery.tabSlideOut.js', array('jquery'), WOOF_VERSION);
        wp_enqueue_style('woof-slideout-tab-css', $this->get_ext_link() . 'css/jquery.tabSlideOut.css', [], WOOF_VERSION);
        wp_enqueue_style('woof-slideout-css', $this->get_ext_link() . 'css/slideout.css', [], WOOF_VERSION);
        wp_enqueue_script('woof-slideout-init', $this->get_ext_link() . 'js/slideout.js', array('jquery'), WOOF_VERSION);

        if (isset($this->woof_settings['woof_slideout_show']) AND $this->woof_settings['woof_slideout_show'] AND is_woocommerce()) {
            $this->woof_settings['woof_slideout_class'] = 'woof_slideout_default';
            if (!isset($this->woof_settings['woof_slideout_width']) OR!$this->woof_settings['woof_slideout_width']) {
                $this->woof_settings['woof_slideout_width'] = "350";
                $this->woof_settings['woof_slideout_width_t'] = "px";
            }
            $shortcode = $this->generate_shortcode($this->woof_settings, "[woof]");
            echo do_shortcode(wp_kses_post(wp_unslash($shortcode)));
        }
        $css_data = '';
        if (WOOF_REQUEST::get('woof_slideout_styles')) {


            if (isset(WOOF_REQUEST::get('woof_slideout_styles')['image_w']) AND isset(WOOF_REQUEST::get('woof_slideout_styles')['image_h'])) {
                $style = "background-size: " . intval(WOOF_REQUEST::get('woof_slideout_styles')['image_w']) . "px " . intval(WOOF_REQUEST::get('woof_slideout_styles')['image_h']) . "px !important;";
                $css_data = ".woof-slide-out-div .woof-handle{
					" . $style . "
				}";
            }

            if (isset(WOOF_REQUEST::get('woof_slideout_styles')['key']) AND isset(WOOF_REQUEST::get('woof_slideout_styles')['height']) AND isset(WOOF_REQUEST::get('woof_slideout_styles')['width'])) {
                $height = esc_attr(WOOF_REQUEST::get('woof_slideout_styles')['height']);
                $key = sanitize_key(WOOF_REQUEST::get('woof_slideout_styles')['key']);
                $width = esc_attr(WOOF_REQUEST::get('woof_slideout_styles')['width']);
                $style_cont = "";
                if ($height != "auto") {
                    $style_cont .= "height:" . $height . ";";
                }
                if ($width != "auto") {
                    $style_cont .= "width:" . $width . ";";
                }

                if ($style_cont) {
                    $css_data .= PHP_EOL . ".woof-slide-content.woof-slide-" . $key . "{
						" . $style_cont . "
					}";
                }
            }
        }
        if ($css_data) {
            wp_add_inline_style('woof-slideout-css', $css_data);
        }
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-slideout">
                <span class="icon-truck"></span>
                <span><?php esc_html_e("Slideout", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
//***
        
        $data = array();

        $data['woof_settings'] = $this->woof_settings;
        wp_enqueue_script('woof_slideout_admin', $this->get_ext_link() . 'js/admin.js', array(), WOOF_VERSION);
        wp_enqueue_style('woof_slideout_admin', $this->get_ext_link() . 'css/admin.css', array(), WOOF_VERSION);
        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function woof_slideout($atts, $content) {

        $image = $this->get_ext_link() . 'img' . DIRECTORY_SEPARATOR . 'filter.png';
        if (isset($this->woof_settings['woof_slideout_img']) AND $this->woof_settings['woof_slideout_img']) {
            $image = $this->woof_settings['woof_slideout_img'];
        }
		$offset =  '100px';
		if (isset($this->woof_settings['woof_slideout_offset_t']) &&  isset($this->woof_settings['woof_slideout_offset'])) {
			$offset = $this->woof_settings['woof_slideout_offset'] . $this->woof_settings['woof_slideout_offset_t'];
		}
		
		$width = 'auto';
		if (isset($this->woof_settings['woof_slideout_width']) &&  isset($this->woof_settings['woof_slideout_width_t'])) {
			$width = $this->woof_settings['woof_slideout_width'] . $this->woof_settings['woof_slideout_width_t'];
		}		
		
		$height = 'auto';
		if (isset($this->woof_settings['woof_slideout_height_t']) &&  isset($this->woof_settings['woof_slideout_height'])) {
			$height = $this->woof_settings['woof_slideout_height'] . $this->woof_settings['woof_slideout_height_t'];
		}		
		
        $atts = shortcode_atts(array(
            'image' => $image,
            'image_h' => (isset($this->woof_settings['woof_slideout_img_h'])) ? $this->woof_settings['woof_slideout_img_h'] : 50,
            'image_w' => (isset($this->woof_settings['woof_slideout_img_w'])) ? $this->woof_settings['woof_slideout_img_w'] : 50,
            'action' => (isset($this->woof_settings['woof_slideout_action'])) ? $this->woof_settings['woof_slideout_action'] : 'click',
            'location' => (isset($this->woof_settings['woof_slideout_position'])) ? $this->woof_settings['woof_slideout_position'] : 'right',
            'speed' => (isset($this->woof_settings['woof_slideout_speed'])) ? $this->woof_settings['woof_slideout_speed'] : '100',
            'offset' => $offset,
            'onloadslideout' => (isset($this->woof_settings['woof_slideout_open'])) ? $this->woof_settings['woof_slideout_open'] : true,
            'mobile_behavior' => (isset($this->woof_settings['woof_slideout_mobile'])) ? $this->woof_settings['woof_slideout_mobile'] : '0',
            'width' => $width,
            'height' => $height,
            'text' => (isset($this->woof_settings['woof_slideout_txt'])) ? $this->woof_settings['woof_slideout_txt'] : esc_html__('Filter', 'woocommerce-products-filter'),
            'class' => ""
                ), $atts);

        if (!empty($content)) {
            $atts['content'] = $content;
        } else {
            $atts['content'] = "[woof]";
        }

        
        $show = true;
        if ($atts['mobile_behavior'] == 1 AND!wp_is_mobile()) {
            $show = false;
        }
        if ($atts['mobile_behavior'] == 2 AND wp_is_mobile()) {
            $show = false;
        }

        $atts['key'] = uniqid("woof_");

        WOOF_REQUEST::set('woof_slideout_styles', array(
            'key' => $atts['key'],
            'image_w' => $atts['image_w'],
            'image_h' => $atts['image_h'],
            'height' => $atts['height'],
            'width' => $atts['width'],
        ));

        if ($show) {

            if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_slideout.php')) {
                return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_slideout.php', $atts);
            }
            return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_slideout.php', $atts);
        } else {
            return "";
        }
    }

    public function generate_shortcode($attr, $content = "") {


        $deff_attr = array(
            'woof_slideout_img' => "image=",
            'woof_slideout_img_h' => "image_h=",
            'woof_slideout_img_w' => "image_w=",
            'woof_slideout_position' => "location=",
            'woof_slideout_speed' => "speed=",
            'woof_slideout_action' => "action=",
            'woof_slideout_offset' => "offset=",
            'woof_slideout_open' => "onloadslideout=",
            'woof_slideout_mobile' => "mobile_behavior=",
            'woof_slideout_height' => "height=",
            'woof_slideout_width' => "width=",
            'woof_slideout_class' => "class="
        );

        if (isset($attr['woof_slideout_type_btn']) AND $attr['woof_slideout_type_btn'] == 1) {
            $attr['woof_slideout_img'] = 'null';
            $deff_attr['woof_slideout_txt'] = "text=";
        }

        foreach ($deff_attr as $key => $data) {
            if (isset($attr[$key]) AND!empty($attr[$key])) {
                $deff_attr[$key] .= $attr[$key];
                if ($key == "woof_slideout_offset") {
                    $type = "px";
                    if (isset($attr[$key . "_t"]) AND!empty($attr[$key] . "_t")) {
                        $type = $attr[$key . "_t"];
                    }
                    $deff_attr[$key] .= $type;
                }
                if ($key == "woof_slideout_width") {
                    $type = "px";
                    if (isset($attr[$key . "_t"]) AND!empty($attr[$key] . "_t")) {
                        $type = $attr[$key . "_t"];
                    }
                    $deff_attr[$key] .= $type;
                }
                if ($key == "woof_slideout_height") {
                    $type = "px";
                    if (isset($attr[$key . "_t"]) AND!empty($attr[$key] . "_t")) {
                        $type = $attr[$key . "_t"];
                    }
                    $deff_attr[$key] .= $type;
                }
            } else {
                unset($deff_attr[$key]);
            }
        }

        return "[woof_slideout " . implode(" ", $deff_attr) . " ]" . $content . "[/woof_slideout]";
    }

    public function generate_shortcode_ajax() {
        $shortcode = $this->generate_shortcode(wc_clean($_POST));
        die($shortcode);
    }

}

WOOF_EXT::$includes['applications']['slideout'] = new WOOF_SLIDEOUT();

