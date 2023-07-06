<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_URL_REQUEST extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'url_request'; //should be defined!!
    //+++
    public $enable = 0;
    public $url_parser = null;
    public $seo = null;

    public function __construct() {
        //return false;//for dev purposes
        parent::__construct();

        include_once $this->get_ext_path() . 'classes/url_parser.php';
        include_once $this->get_ext_path() . 'classes/seo.php';
        if (isset($this->woof_settings['woof_url_request']['enable'])) {
            $this->enable = $this->woof_settings['woof_url_request']['enable'];
        }
        $this->init();
        add_filter('woocommerce_product_query_tax_query', array($this, 'tax_query'), 10, 2);
        add_filter('wpseo_sitemap_index', array($this, 'sitemap_index'), 10);
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
        add_action('woof_print_applications_tabs_content_advanced', array($this, 'woof_print_applications_tabs_content'), 10, 99999);
        if ($this->enable) {
            $this->url_parser = new WOOF_URL_PARSER();
            add_action('wp_enqueue_scripts', array($this, 'init_js'), 1);

            //seo rules
            $seo_rules = array();
            $current_lang = get_locale();
            if (isset($this->woof_settings['woof_url_request']['seo_rules'][$current_lang]) && is_array($this->woof_settings['woof_url_request']['seo_rules'][$current_lang])) {
                $seo_rules = $this->woof_settings['woof_url_request']['seo_rules'][$current_lang];
            }
            $url = $this->url_parser->get_request_uri();
            $this->seo = new WOOF_SEO($seo_rules, $url);
        }

        add_action('wp_ajax_woof_get_seo_rule_html', array($this, 'get_seo_rule_html'));
    }

    public function init_js() {
        wp_enqueue_script('woof_url_parser', $this->get_ext_link() . 'js/url_parser.js', array('jquery', 'woof_front'), WOOF_VERSION);
        $all_data['filters'] = array_flip($this->url_parser->get_all_items());
        foreach ($this->url_parser->special_filters as $key => $data) {
            $all_data['special'][array_key_first($data)] = $key;
        }
        foreach ($all_data['filters'] as $f_real_key => $f_key) {
            if (isset($this->woof_settings['meta_filter']) AND isset($this->woof_settings['meta_filter'][$f_real_key])) {
                unset($all_data['filters'][$f_real_key]);
                $f_real_key = $this->woof_settings['meta_filter'][$f_real_key]['search_view'] . '_' . $f_real_key;
                $all_data['filters'][$f_real_key] = $f_key;
            }
        }

        wp_localize_script('woof_url_parser', 'url_parser_data', $all_data);
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-url-request">
                <span class="icon-link-outline"></span>
                <span><?php esc_html_e("SEO URL request", 'woocommerce-products-filter'); ?></span>
            </a>
        </li>
        <?php
    }

    public function get_seo_rule_html() {
        ob_start();
        $url = '/{any}/';
        if (WOOF_REQUEST::get('url')) {
            $url = sanitize_text_field(WOOF_REQUEST::get('url'));
        }
        $lang = get_locale();
        if (WOOF_REQUEST::get('lang')) {
            $lang = sanitize_text_field(WOOF_REQUEST::get('lang'));
        }
        $this->woof_draw_seo_rules_item('', $lang, $url);
        $seo_rule = ob_get_clean();
        die($seo_rule);
    }

    public function woof_print_applications_tabs_content() {
        wp_enqueue_script('woof_seo_admin', $this->get_ext_link() . 'js/admin.js', [], WOOF_VERSION);
        wp_enqueue_style('woof_seo_admin_css', $this->get_ext_link() . 'css/admin.css', [], WOOF_VERSION);
        //***
        
        $data = array();

        $data['woof_settings'] = $this->woof_settings;
        $data['seo_rule'] = $this;
        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function get_all_langs() {

        $langs = array();
        $langs[] = get_locale();
        if (class_exists('SitePress')) {
            $wpml_langs = icl_get_languages('skip_missing=0&orderby=code');
            foreach ($wpml_langs as $key => $lang) {

                if ($lang["default_locale"] != get_locale()) {
                    $langs[] = $lang["default_locale"];
                }
            }
        }

        return apply_filters('woof_seo_rules_langs', $langs);
    }

    public function woof_draw_seo_rules_item($ukey, $lang, $url = '', $title = '', $description = '', $h1 = '', $text = '') {
        if (!$ukey) {
            $ukey = uniqid("section");
        }
        ?>
        <li class="woof_seo_rules_item  woof_seo_rules_item_<?php echo esc_attr($lang); ?>" data-key='<?php echo esc_attr($ukey); ?>'>
            <div class='woof_seo_rule_url' >
                <label><?php esc_html_e("URL of the page", 'woocommerce-products-filter'); ?></label></br>
                <input type="text" placeholder="<?php esc_html_e("Example: /color-{any}/", 'woocommerce-products-filter'); ?>" name="woof_settings[woof_url_request][seo_rules][<?php echo esc_html($lang) ?>][<?php echo esc_attr($ukey); ?>][url]" value="<?php echo esc_attr($url); ?>">
            </div>
            <div class='woof_seo_rule_container' >
                <div class='woof_seo_rule_item_field woof_seo_rule_title' >
                    <label><?php esc_html_e("Meta title", 'woocommerce-products-filter'); ?></label>
                    <input type="text" name="woof_settings[woof_url_request][seo_rules][<?php echo esc_attr($lang) ?>][<?php echo esc_attr($ukey); ?>][title]" value="<?php echo esc_html($title); ?>">
                </div>
                <div class='woof_seo_rule_item_field woof_seo_rule_h1' >
                    <label><?php esc_html_e("H1  title", 'woocommerce-products-filter'); ?></label>
                    <input type="text" name="woof_settings[woof_url_request][seo_rules][<?php echo esc_attr($lang) ?>][<?php echo esc_attr($ukey); ?>][h1]" value="<?php echo esc_html($h1); ?>">
                </div>				
                <div class='woof_seo_rule_item_field woof_seo_rule_description' >
                    <label><?php esc_html_e("Meta description", 'woocommerce-products-filter'); ?></label>				
                    <textarea name="woof_settings[woof_url_request][seo_rules][<?php echo esc_attr($lang) ?>][<?php echo esc_attr($ukey); ?>][description]" ><?php echo esc_html($description); ?></textarea>
                </div>
                <div class='woof_seo_rule_item_field woof_seo_rule_description' >
                    <label><?php esc_html_e("SEO text", 'woocommerce-products-filter'); ?></label>				
                    <textarea name="woof_settings[woof_url_request][seo_rules][<?php echo esc_attr($lang) ?>][<?php echo esc_attr($ukey); ?>][text]" ><?php echo wp_kses_post(wp_unslash($text)); ?></textarea>
                </div>				

            </div>
            <div class='woof_seo_rule_delete' >                
                <a href="#" class="button button-primary woof_seo_rules_delete woof-button" data-key='<?php echo esc_attr($ukey); ?>' title="delete"><span class="dashicons dashicons-trash"></span></a>
            </div>				
        </li>
        <?php
    }

    public function tax_query($tax_query, $_this) {
        
        $tax_q = woof()->get_tax_query();
		if(isset($tax_q["relation"])){
			unset($tax_q["relation"]);
		}
        $tax_query = array_merge($tax_query, $tax_q);
        $tax_query = woof()->product_visibility_not_in($tax_query, woof()->generate_visibility_keys(woof()->is_isset_in_request_data(woof()->get_swoof_search_slug())));
        $tax_relations = apply_filters('woof_main_query_tax_relations', array());
        if (!empty($tax_relations)) {
            foreach ($tax_query as $key => $value) {
                if (isset($value['taxonomy'])) {
                    if (in_array($value['taxonomy'], array_keys($tax_relations))) {
                        if (count($tax_query[$key]['terms'])) {
                            $tax_query[$key]['operator'] = $tax_relations[$value['taxonomy']];
                            $tax_query[$key]['include_children'] = 0;
                        }
                    }
                }
            }
        }

        return $tax_query;
    }

    public function sitemap_index($appended_text = '') {
        $links = array();
        if (isset($this->woof_settings['woof_url_request']['yoast_sitemap'])) {
            $links = explode(PHP_EOL, trim($this->woof_settings['woof_url_request']['yoast_sitemap']));
        }
        foreach ($links as $l) {
            $appended_text .= "<sitemap><loc>{$l}</loc></sitemap>";
        }

        return $appended_text;
    }

}

WOOF_EXT::$includes['applications']['url_request'] = new WOOF_EXT_URL_REQUEST();
