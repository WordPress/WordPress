<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_Widget extends WP_Widget {

//Widget Setup
    public function __construct() {
        parent::__construct(__CLASS__, esc_html__('HUSKY - Products Filter Professional for WooCommerce', 'woocommerce-products-filter'), array(
            'classname' => __CLASS__,
            'description' => esc_html__('Products Filter for WooCommerce by realmag777', 'woocommerce-products-filter')
                )
        );
    }

//Widget view
    public function widget($args, $instance) {
        $args['instance'] = $instance;
        $args['sidebar_id'] = (isset($args['id'])) ? $args['id'] : 0;
        $args['sidebar_name'] = (isset($args['name'])) ? $args['name'] : "";
        //+++
        $price_filter = 0;
        if (isset(woof()->settings['by_price']['show'])) {
            $price_filter = intval(woof()->settings['by_price']['show']);
        }

        if (isset($args['before_widget'])) {
            echo  wp_kses_post(wp_unslash($args['before_widget']));
        }
        ?>
        <div class="widget widget-woof">
            <?php
            if (!empty($instance['title'])) {
                $instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
                if (isset($args['before_title'])) {
                    echo wp_kses_post(wp_unslash($args['before_title']));
                    esc_html_e($instance['title']);
                    echo wp_kses_post(wp_unslash($args['after_title']));
                } else {
                    ?>
                    <<?php echo esc_attr(apply_filters('woof_widget_title_tag', 'h3')) ?> class="widget-title"><?php esc_html_e($instance['title']) ?></<?php echo esc_attr(apply_filters('woof_widget_title_tag', 'h3')) ?>>
                    <?php
                }
            }
            ?>


            <?php
            if (isset($instance['additional_text_before'])) {
                echo do_shortcode(wp_kses_post(wp_unslash($instance['additional_text_before'])));
            }

            $redirect = '';
            if (isset($instance['redirect'])) {
                $redirect = sanitize_text_field($instance['redirect']);
            }

            //+++

            $woof_start_filtering_btn = 0;
            if (isset($instance['woof_start_filtering_btn'])) {
                $woof_start_filtering_btn = (int) $instance['woof_start_filtering_btn'];
            }

            //+++

            $ajax_redraw = '';
            if (isset($instance['ajax_redraw'])) {
                $ajax_redraw = (int) $instance['ajax_redraw'];
            }

            $dynamic_recount = -1;
            if (isset($instance['dynamic_recount'])) {
                $dynamic_recount = (int) $instance['dynamic_recount'];
            }

            $btn_position = 'b';
            if (isset($instance['btn_position'])) {
                $btn_position = sanitize_text_field($instance['btn_position']);
            }
            $autosubmit = -1;
            if (isset($instance['autosubmit'])) {
                $autosubmit = (int) $instance['autosubmit'];
            }
            $mobile_mode = 0;
            if (isset($instance['mobile_mode'])) {
                $mobile_mode = (int) $instance['mobile_mode'];
            }
            ?>

            <?php echo do_shortcode('[woof sid="widget" mobile_mode="' . esc_attr($mobile_mode) . '" autosubmit="' . esc_attr($autosubmit) . '" start_filtering_btn=' . esc_attr($woof_start_filtering_btn) . ' price_filter=' . esc_attr($price_filter) . ' redirect="' . esc_attr($redirect) . '" ajax_redraw="' . esc_attr($ajax_redraw) . '" btn_position="' . esc_attr($btn_position) . '" dynamic_recount="' . esc_attr($dynamic_recount) . '" ]'); ?>
        </div>
        <?php
        if (isset($args['after_widget'])) {
            echo wp_kses_post(wp_unslash($args['after_widget']));
        }
    }

//Update widget
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['additional_text_before'] = $new_instance['additional_text_before'];
        $instance['redirect'] = $new_instance['redirect'];
        $instance['woof_start_filtering_btn'] = $new_instance['woof_start_filtering_btn'];
        $instance['ajax_redraw'] = $new_instance['ajax_redraw'];
        $instance['btn_position'] = $new_instance['btn_position'];
        $instance['mobile_mode'] = isset($new_instance['mobile_mode'])?$new_instance['mobile_mode']:0;
        $instance['dynamic_recount'] = $new_instance['dynamic_recount'];
        $instance['autosubmit'] = $new_instance['autosubmit'];
        return $instance;
    }

//Widget form
    public function form($instance) {
//Defaults
        $defaults = array(
            'title' => esc_html__('HUSKY Filter', 'woocommerce-products-filter'),
            'additional_text_before' => '',
            'redirect' => '',
            'woof_start_filtering_btn' => 0,
            'ajax_redraw' => 0,
            'dynamic_recount' => -1,
            'btn_position' => 'b',
            'mobile_mode' => 0,
            'autosubmit' => -1
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $args = array();
        $args['instance'] = $instance;
        $args['widget'] = $this;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'woocommerce-products-filter') ?>:</label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']) ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('additional_text_before')); ?>"><?php esc_html_e('Additional text before', 'woocommerce-products-filter') ?>:</label>
            <textarea class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('additional_text_before')); ?>" name="<?php echo esc_attr($this->get_field_name('additional_text_before')); ?>"><?php echo wp_kses_post($instance['additional_text_before']) ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('redirect')); ?>"><?php esc_html_e('Redirect to', 'woocommerce-products-filter') ?>:</label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('redirect')); ?>" name="<?php echo esc_attr($this->get_field_name('redirect')); ?>" value="<?php echo esc_attr($instance['redirect']) ?>" /><br />
            <i><?php esc_html_e('Redirect to any page - use it by your own logic. Leave it empty for default behavior.', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('woof_start_filtering_btn')); ?>"><?php esc_html_e('Hide search form by default and show one button instead', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('woof_start_filtering_btn')) ?>" name="<?php echo esc_attr($this->get_field_name('woof_start_filtering_btn')) ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['woof_start_filtering_btn'], $k) ?> value="<?php echo esc_attr($k) ?>" class="level-0"><?php echo esc_html($val) ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php esc_html_e('User on the site front will have to press button like "Show products filter form" to load search form by ajax and start filtering. Good feature when search form is quite big and page loading takes more time because of it!', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('dynamic_recount')); ?>"><?php esc_html_e('Dynamic recount', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                -1 => esc_html__('Default', 'woocommerce-products-filter'),
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('dynamic_recount')) ?>" name="<?php echo esc_attr($this->get_field_name('dynamic_recount')) ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['dynamic_recount'], $k) ?> value="<?php echo esc_attr($k) ?>" class="level-0"><?php echo esc_html($val) ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php esc_html_e('Dynamic recount for current search form', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('autosubmit')); ?>"><?php esc_html_e('Autosubmit', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                -1 => esc_html__('Default', 'woocommerce-products-filter'),
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('autosubmit')) ?>" name="<?php echo esc_attr($this->get_field_name('autosubmit')) ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['autosubmit'], $k) ?> value="<?php echo esc_attr($k) ?>" class="level-0"><?php echo esc_html($val) ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php esc_html_e('Yes - filtering starts immediately if user changed any item in the search form. No - user can set search data and then should press Filter button', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('btn_position')); ?>"><?php esc_html_e('Submit button position', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                'b' => esc_html__('Bottom', 'woocommerce-products-filter'),
                't' => esc_html__('Top', 'woocommerce-products-filter'),
                'tb' => esc_html__('Top AND Bottom', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('btn_position')) ?>" name="<?php echo esc_attr($this->get_field_name('btn_position')) ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['btn_position'], $k) ?> value="<?php echo esc_attr($k) ?>" class="level-0"><?php echo esc_html($val) ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php esc_html_e('The submit and reset buttons position in current search form', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('ajax_redraw')); ?>"><?php esc_html_e('Form AJAX redrawing', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('ajax_redraw')) ?>" name="<?php echo esc_attr($this->get_field_name('ajax_redraw')) ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['ajax_redraw'], $k) ?> value="<?php echo esc_attr($k) ?>" class="level-0"><?php echo esc_html($val) ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php esc_html_e('Redraws search form by AJAX, and to start filtering "Filter" button should be pressed. Useful when uses hierarchical drop-down for example', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('mobile_mode')); ?>"><?php esc_html_e('Mobile mode', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('mobile_mode')) ?>" name="<?php echo esc_attr($this->get_field_name('mobile_mode')) ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['mobile_mode'], $k) ?> value="<?php echo esc_attr($k) ?>" class="level-0"><?php echo esc_html($val) ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php esc_html_e('Hide the widget on a mobile device and a button appears to show the filter', 'woocommerce-products-filter') ?></i>
        </p>		
        <?php
    }

}
