<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_IMAGE extends WOOF_EXT {

    public $type = 'html_type';
    public $html_type = 'image'; //your custom key here
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
        add_action('woof_print_tax_additional_options_image', array($this, 'print_additional_options'), 10, 1);
        add_action('woof_print_design_additional_options', array($this, 'woof_print_design_additional_options'), 10, 1);
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/html_types/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/html_types/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_image';

        add_action('admin_head', array($this, 'admin_head'), 50);

        $this->taxonomy_type_additional_options = array(
            'show_title' => array(
                'title' => esc_html__('Show image title', 'woocommerce-products-filter'),
                'tip' => esc_html__('Show image title below picture', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter'),
                )
            ),
            'as_radio' => array(
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
            wp_enqueue_style('woof_image', $this->get_ext_link() . 'css/admin.css', array(), WOOF_VERSION);
            wp_enqueue_script('woof_image', $this->get_ext_link() . 'js/html_types/plugin_options.js', array('jquery'), WOOF_VERSION);
        }
    }

    public function woof_add_html_types($types) {
        $types[$this->html_type] = esc_html__('Image', 'woocommerce-products-filter');
        return $types;
    }

    public function print_additional_options($key) {
        
        $woof_settings = woof()->settings;
        $terms = WOOF_HELPER::get_terms($key, 0, 0, 0, 0);
        if (!empty($terms)) {
            ?>
            <br /><a href="javascript:void(0);" class="button woof-button-outline-secondary woof_toggle_images"><?php esc_html_e('toggle image terms', 'woocommerce-products-filter') ?></a><br />
            <ul class="woof_image_list woof_hide_options">
                <?php
                foreach ($terms as $t) {
                    $term_key = 'images_term_' . $t['term_id'];
                    $image = '';
                    if (isset($woof_settings[$term_key]['image_url'])) {
                        $image = $woof_settings[$term_key]['image_url'];
                    }

                    if (!isset($woof_settings[$term_key]['image_styles'])) {
                        //init value
                        $woof_settings[$term_key]['image_styles'] = 'width: 100px;
height:50px;
margin: 0 3px 3px 0;
background-size: 100% 100%;
background-clip: content-box;
border: 2px solid #e2e6e7;
padding: 2px;
color: #292f38;
font-size: 0;
text-align: center;
cursor: pointer;
border-radius: 4px;
transition: border-color .35s ease;';
                    }
                    ?>
                    <li>
                        <table>
                            <tr>

                                <td style="padding-top: 0;">
                                    <input type="text" name="woof_settings[<?php echo esc_attr($term_key) ?>][image_url]" value="<?php echo esc_url($image) ?>" placeholder="<?php esc_html_e('set link to the image', 'woocommerce-products-filter') ?>" class="text" style="width: 600px;" />
                                    <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                                </td>
                                <td>
                                    <input type="button" value="&#xea49" data-key="<?php echo esc_attr($term_key) ?>" data-name="<?php printf(__('Image settings for term %s', 'woocommerce-products-filter'), $t['name']) ?>" class="woof-button js_woof_options js_woof_options_image icon-book">
                                    <input type="hidden" name="woof_settings[<?php echo esc_attr($term_key) ?>][image_styles]" value="<?php echo esc_textarea($woof_settings[$term_key]['image_styles']) ?>" />

                                    <div id="woof-modal-content-<?php echo esc_attr($term_key) ?>" style="display: none;">

                                        <div class="woof-form-element-container">

                                            <div class="woof-name-description woof_width_30p">
                                                <strong><?php esc_html_e('Image styles', 'woocommerce-products-filter') ?></strong>
                                                <span><?php esc_html_e('This option should be set', 'woocommerce-products-filter') ?></span>

                                                <b><?php esc_html_e('Example', 'woocommerce-products-filter') ?>:</b><br />
                                                <code>width: 100px;<br />
                                                    height:50px;<br />
                                                    margin: 0 3px 3px 0;<br />
                                                    background-size: 100% 100%;<br />
                                                    background-clip: content-box;<br />
                                                    border: 2px solid #e2e6e7;<br />
                                                    padding: 2px;<br />
                                                    color: #292f38;<br />
                                                    font-size: 0;<br />
                                                    text-align: center;<br />
                                                    cursor: pointer;<br />
                                                    border-radius: 4px;<br />
                                                    transition: border-color .35s ease;</code>
                                            </div>                                       


                                            <div class="woof-form-element woof_width_70p">
                                                <textarea class="woof_popup_option woof_fix11" data-option="image_styles"></textarea><br />

                                            </div>

                                        </div>



                                    </div>
                                </td>
                                <td class="woof_fix8">
                                    <p class="description"> [ <?php esc_html_e(WOOF_HELPER::strtolower($t['slug'])); ?> ]</p>
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

        if (!isset($woof_settings['checked_image'])) {
            $woof_settings['checked_image'] = '';
        }
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['image'] = new WOOF_EXT_IMAGE();
