<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

abstract class WOOF_META_FILTER_TYPE {

    protected $type_options = array();
    protected $woof_settings = array();
    protected $type = "";
    protected $meta_key = "";
    public $value_type = '';
    public $options_separator = ',';

    public function __construct($key, $options, $woof_settings) {
        $this->meta_key = $key;
        $this->type_options = $options;
        $this->woof_settings = $woof_settings;
        add_action('init', array($this, 'init_data'));
    }

    abstract public function init();

    abstract public function get_meta_filter_path();

    abstract public function get_meta_filter_link();

    abstract public function get_meta_filter_override_path();

    abstract public function create_meta_query();

    public function get_js_func_name() {
        return false;
    }

    public function init_data() {
        $this->options_separator = apply_filters('woof_meta_options_separator', $this->options_separator);
    }

    protected function draw_additional_options() {
        echo esc_html("");
    }

    public function draw_meta_filter_structure() {
        ?><li data-key="<?php echo esc_attr($this->meta_key) ?>" class="woof_options_li">
        <?php
        $show = 0;
        if (isset($this->woof_settings[$this->meta_key]['show'])) {
            $show = $this->woof_settings[$this->meta_key]['show'];
        }
        ?>
            <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>

            <strong class="woof_fix1"><?php echo esc_html($this->woof_settings['meta_filter'][$this->meta_key]['title']) ?>:</strong>


            <span class="icon-question help_tip" data-tip="<?php esc_html_e('Meta filter', 'woocommerce-products-filter') ?>"></span>

            <div class="select-wrap">
                <select name="woof_settings[<?php echo esc_attr($this->meta_key) ?>][show]" class="woof_setting_select">
                    <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                    <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
                </select>
            </div>
            <a href="#" data-key="<?php echo esc_attr($this->meta_key) ?>" data-name="<?php echo esc_html($this->woof_settings['meta_filter'][$this->meta_key]['title']) ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($this->meta_key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>
            <?php $this->draw_additional_options() ?></li><?php
    }

    public function woof_print_html_type_meta() {
        echo '<h1>' . esc_html($this->meta_key) . '</h1>';
    }

    public function render_html_e($pagepath, $data = array()) {
        if (isset($data['pagepath'])) {
            unset($data['pagepath']);
        }
        if (is_array($data) AND!empty($data)) {
            extract($data);
        }

        $pagepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath);
        $pagepath = realpath($pagepath);
        if (!$pagepath) {
            return;
        }
        include($pagepath);
    }

    public function render_html($pagepath, $data = array()) {
        if (isset($data['pagepath'])) {
            unset($data['pagepath']);
        }
        if (is_array($data) AND!empty($data)) {
            extract($data);
        }

        $pagepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath);
        $pagepath = realpath($pagepath);
        if (!$pagepath) {
            return "";
        }
        ob_start();
        include($pagepath);
        return ob_get_clean();
    }

}
