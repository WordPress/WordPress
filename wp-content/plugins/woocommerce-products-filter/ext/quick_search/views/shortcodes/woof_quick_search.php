<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

?>
<div class="woof_qt">
    <div class="woof_quick_search_wraper <?php echo esc_attr($class) ?>">

        <input id="woof_quick_search_form" class="form-control woof_quick_search_wraper_textinput" data-text_group_logic="<?php echo esc_html($text_group_logic) ?>" data-term_logic="<?php echo esc_html($term_logic) ?>" data-tax_logic="<?php echo esc_html($tax_logic) ?>"  data-target-link="<?php echo esc_html($target) ?>" data-preload="<?php echo esc_html($preload) ?>" data-extended="<?php echo esc_html($extended_filter) ?>" placeholder="<?php echo esc_html($placeholder) ?>" >

        <?php
        if ($extended_filter) {
            if ($price_filter == 1) {

                wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion-rangeSlider/ion.rangeSlider.min.js', array('jquery'), WOOF_VERSION);
                wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css', array(), WOOF_VERSION);

                $skin = 'round';
                if (isset(woof()->settings['ion_slider_skin'])) {
                    $skin = woof()->settings['ion_slider_skin'];
                }
                $skin = WOOF_HELPER::check_new_ion_skin($skin);
                //***
                $additional_taxes = "";
                $min_price = $preset_min = WOOF_HELPER::get_min_price($additional_taxes);
                $max_price = $preset_max = WOOF_HELPER::get_max_price($additional_taxes);
                if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
                    $tax_classes = array_merge(array(''), WC_Tax::get_tax_classes());
                    $class_max = $max_price;
                    foreach ($tax_classes as $tax_class) {
                        if ($tax_rates = WC_Tax::get_rates($tax_class)) {
                            $class_max = ceil($max_price + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($max_price, $tax_rates)));
                        }
                    }

                    $max_price = $class_max;
                }

                if (class_exists('WOOCS')) {
                    $preset_min = apply_filters('woocs_exchange_value', $preset_min);
                    $preset_max = apply_filters('woocs_exchange_value', $preset_max);
                    $min_price = apply_filters('woocs_exchange_value', $min_price);
                    $max_price = apply_filters('woocs_exchange_value', $max_price);
                }
                //***
                $slider_step = 1;
                //***
                $slider_prefix = '';
                $slider_postfix = '';
                if (class_exists('WOOCS')) {
                    global $WOOCS;
                    $currencies = $WOOCS->get_currencies();
                    $currency_pos = 'left';
                    if (isset($currencies[$WOOCS->current_currency])) {
                        $currency_pos = $currencies[$WOOCS->current_currency]['position'];
                    }
                } else {
                    $currency_pos = get_option('woocommerce_currency_pos');
                }
                switch ($currency_pos) {
                    case 'left':
                        $slider_prefix = get_woocommerce_currency_symbol();
                        break;
                    case 'left_space':
                        $slider_prefix = get_woocommerce_currency_symbol() . ' ';
                        break;
                    case 'right':
                        $slider_postfix = get_woocommerce_currency_symbol();
                        break;
                    case 'right_space':
                        $slider_postfix = ' ' . get_woocommerce_currency_symbol();
                        break;

                    default:
                        break;
                }

                //***
                //https://wordpress.org/support/topic/results-found/
                if ($preset_max < $max_price) {
                    $max = $max_price;
                } else {
                    $max = $preset_max;
                }
                if ($preset_min > $min_price) {
                    $min = $min_price;
                } else {
                    $min = $preset_min;
                }
                ?>
                <div class="woof_qt_add_filter ">
                    <input class="woof_qt_price_slider" data-skin="<?php echo esc_attr($skin) ?>"  data-min="<?php echo esc_attr($min) ?>" data-max="<?php echo esc_attr($max) ?>" data-min-now="<?php echo esc_attr($min_price) ?>" data-max-now="<?php echo esc_attr($max_price) ?>" data-step="<?php echo esc_attr($slider_step) ?>" data-slider-prefix="<?php echo esc_attr($slider_prefix) ?>" data-slider-postfix="<?php echo esc_attr($slider_postfix) ?>" value="" />
                </div>
                <?php
            }
            if ($add_filters !== '') {
                $filter_items = array();
                $filter_items = explode(',', $add_filters);

                $filter_custom_title = array();
                $filter_title = explode(',', $filter_title);
                foreach ($filter_title as $title_itm) {
                    $temp_title = explode(':', $title_itm);
                    if (isset($temp_title[1])) {
                        $filter_custom_title[$temp_title[0]] = $temp_title[1];
                    }
                }
                $taxonomy_info = "";

                foreach ($filter_items as $item) {
                    $filter_struct = array();
                    $terms = array();
                    $filter_struct = explode(':', $item);
                    if (!isset($filter_struct[1])) {
                        continue;
                    }
                    if (class_exists('WOOF_META_FILTER')) {
                        $meta_fields = $this->settings['meta_filter'];
                        if (!empty($meta_fields)) {
                            if (in_array($filter_struct[1], array_keys($meta_fields))) {
                                $title = WOOF_HELPER::wpml_translate(null, (isset($filter_custom_title[$filter_struct[1]]) ? $filter_custom_title[$filter_struct[1]] : $meta_fields[$filter_struct[1]]['title']));
                                woof_get_meta_filter_html($meta_fields[$filter_struct[1]], $filter_struct[0], $title);
                                continue;
                            }
                        }
                    }
                    $args = array(
                        'taxonomy' => $filter_struct[1],
                        'hide_empty' => true,
                    );
                    if ($exclude_terms != '') {
                        $args['exclude'] = $exclude_terms;
                    }
                    $terms = get_terms($args);

                    if (!is_array($terms)) {
                        continue;
                    }

                    $taxonomy_info = WOOF_HELPER::wpml_translate(get_taxonomy($filter_struct[1]), (isset($filter_custom_title[$filter_struct[1]]) ? $filter_custom_title[$filter_struct[1]] : ""));
                    switch ($filter_struct[0]) {
                        case 'multi-drop-down':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_multiselect_<?php echo esc_attr($filter_struct[1]) ?>">
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html($taxonomy_info) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <select class="woof_qt_select tax_<?php echo esc_attr($filter_struct[1]) ?>" data-placeholder="<?php echo esc_html($taxonomy_info) ?>"  data-tax="<?php echo esc_attr($filter_struct[1]) ?>" multiple="multiple" >
                                    <?php
                                    foreach ($terms as $term) {
                                        ?>
                                        <option value="<?php echo esc_attr($term->term_id) ?>"><?php echo esc_html($term->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php
                            break;
                        case 'drop-down':
                            ?>

                            <div class="woof_qt_add_filter woof_qt_add_filter_select_<?php echo esc_attr($filter_struct[1]) ?>">
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html($taxonomy_info) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <select class="woof_qt_select tax_<?php echo esc_attr($filter_struct[1]) ?>" data-tax="<?php echo esc_attr($filter_struct[1]) ?>">
                                    <option value="-1"><?php esc_html_e('Any', 'woocommerce-products-filter') ?></option>
                                    <?php
                                    foreach ($terms as $term) {
                                        ?>
                                        <option value="<?php echo esc_attr($term->term_id) ?>"><?php echo esc_html($term->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php
                            break;
                        case 'checkbox':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_checkbox woof_qt_add_filter_checkbox_<?php echo esc_attr($filter_struct[1]) ?>">
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html($taxonomy_info) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php
                                foreach ($terms as $term) {
                                    ?>
                                    <div class="woof_qt_item_container">
                                        <input type="checkbox" id="term_<?php echo esc_attr($unique_id) ?>" name="woof_qt_check_<?php echo esc_attr($filter_struct[1]) ?>" class="woof_qt_checkbox tax_<?php echo esc_attr($filter_struct[1]) ?>" data-tax="<?php echo esc_attr($filter_struct[1]) ?>"value="<?php echo esc_attr($term->term_id) ?>" >
                                        <label class="woof_qt_checkbox_label" for="term_<?php echo esc_attr($unique_id) ?>"><?php echo esc_html($term->name) ?></label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            break;
                        case 'radio':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_radio woof_qt_add_filter_radio_<?php echo esc_attr($filter_struct[1]) ?>">
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html($taxonomy_info) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php
                                foreach ($terms as $term) {
                                    $unique_id = uniqid();
                                    ?>
                                    <div class="woof_qt_item_container">
                                        <input type="radio" id="term_<?php echo esc_attr($unique_id) ?>" name="woof_qt_radio_<?php echo esc_attr($filter_struct[1]) ?>" class="woof_qt_radio tax_<?php echo esc_attr($filter_struct[1]) ?>" data-tax="<?php echo esc_attr($filter_struct[1]) ?>" value="<?php echo esc_attr($term->term_id) ?>" >
                                        <label class="woof_qt_radio_label" for="term_<?php echo esc_attr($unique_id) ?>"><?php echo esc_html($term->name) ?>
                                            <span class="woof_qt_radio_reset tax_<?php echo esc_attr($filter_struct[1]) ?>_reset" data-tax="<?php echo esc_attr($filter_struct[1]) ?>" ><img src="<?php echo esc_url($this->settings['delete_image']) ?>" height="12" width="12" alt="" /></span>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            break;
                        case 'slider':
                            ?><p class="woof_notice"><a href="#"><?php esc_html_e('Please read documents', 'woocommerce-products-filter'); ?></a></p><?php
                            break;
                        default :
                            break;
                    }
                }
            }
            if ($reset_btn == 1) {
                ?>
                <div class="woof_qt_reset_filter_con">
                    <button class="woof_qt_reset_filter_btn"><?php esc_html_e($reset_text) ?></button>
                </div>
                <?php
            }
        }
        ?>
    </div>  
</div>       
<?php
if (!function_exists("woof_get_meta_filter_html")) {

    function woof_get_meta_filter_html($meta_item, $type, $title = "") {
        switch ($type) {
            case'drop-down':
                $meta_options = array();
                if (!isset($meta_item["options"]) OR!$meta_item["options"]) {
                    ?> <div class="woof_qt_add_filter "><p class="woof_notice"><a href="#"><?php esc_html_e('Error! Please read documents', 'woocommerce-products-filter'); ?></a></p></div><?php
                    break;
                } else {
                    $meta_options = explode(',', $meta_item["options"]);
                }
                ?>

                <div class="woof_qt_add_filter woof_qt_add_filter_select woof_qt_add_filter_select_<?php echo esc_attr($meta_item['meta_key']) ?>">
                    <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                    <?php echo esc_html($title) ?>
                    </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>   
                    <select class="woof_qt_select meta_<?php echo esc_attr($meta_item['meta_key']) ?>" data-meta="1" data-tax="<?php echo esc_attr($meta_item['meta_key']) ?>">
                        <option value="-1"><?php echo esc_html(WOOF_HELPER::wpml_translate(null, $meta_item['title'])) ?></option>
                        <?php foreach ($meta_options as $key => $option) : ?>
                            <?php
                            $option_title = $option;
                            $custom_title = explode('^', $option, 2);
                            if (count($custom_title) > 1) {
                                $option = $custom_title[1];
                                $option_title = $custom_title[0];
                            }
                            ?>   
                            <option value="<?php echo esc_html($option) ?>" >
                                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, $option_title)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php
                break;
            case'multi-drop-down':
                $meta_options = array();
                if (!isset($meta_item["options"]) OR!$meta_item["options"]) {
                    ?> <div class="woof_qt_add_filter "><p class="woof_notice"><a href="#"><?php esc_html_e('Error! Please read documents', 'woocommerce-products-filter'); ?></a></p></div><?php
                    break;
                } else {
                    $meta_options = explode(',', $meta_item["options"]);
                }
                ?>

                <div class="woof_qt_add_filter woof_qt_add_filter_select woof_qt_add_filter_select_<?php echo esc_attr($meta_item['meta_key']) ?>">
                    <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                    <?php echo esc_html($title) ?>
                    </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>   
                    <select class="woof_qt_select meta_<?php echo esc_attr($meta_item['meta_key']) ?>" data-meta="1" data-tax="<?php echo esc_attr($meta_item['meta_key']) ?>"multiple="multiple" >
                        <?php foreach ($meta_options as $key => $option) : ?>
                            <?php
                            $option_title = $option;
                            $custom_title = explode('^', $option, 2);
                            if (count($custom_title) > 1) {
                                $option = $custom_title[1];
                                $option_title = $custom_title[0];
                            }
                            ?>   
                            <option  value="<?php echo esc_html($option) ?>" >
                                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, $option_title)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php
                break;
            case'checkbox':
                
                $value = 1;
                if ($meta_item['search_view'] == 'checkbox') {
                    $meta_settings = woof()->settings[$meta_item['meta_key']];
                    if ($meta_settings['search_option'] == 0) {
                        $value = $meta_settings['search_value'];
                    } else {
                        $value = "meta_exist";
                    }
                }
                ?>
                <div class="woof_qt_add_filter woof_qt_add_filter_checkbox woof_qt_add_filter_checkbox_<?php echo esc_attr($meta_item['meta_key']) ?>">
                    <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                    <?php echo esc_html($title) ?>
                    </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>

                    <div class="woof_qt_item_container">
                        <input type="checkbox" name="woof_qt_check_<?php echo esc_attr($meta_item['meta_key']) ?>" class="woof_qt_checkbox meta_<?php echo esc_attr($meta_item['meta_key']) ?>" data-tax="<?php echo esc_attr($meta_item['meta_key']) ?>"value="<?php echo esc_html($value) ?>" >
                        <label class="woof_qt_checkbox_label"><?php echo esc_html($title) ?></label>
                    </div>
                </div><?php
                break;
            case'radio':
                ?> <div class="woof_qt_add_filter "><p class="woof_notice"><a href="#"><?php esc_html_e('Error! Please read documents', 'woocommerce-products-filter'); ?></a></p></div><?php
                break;
            case'slider':
                if ($meta_item['search_view'] != 'slider') {
                    ?> <div class="woof_qt_add_filter "><p class="woof_notice"><a href="#"><?php esc_html_e('Error! Please read documents', 'woocommerce-products-filter'); ?></a></p></div><?php
                            break;
                        }

                        
                        $meta_settings = woof()->settings[$meta_item['meta_key']];
                        wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion-rangeSlider/ion.rangeSlider.min.js', array('jquery'), WOOF_VERSION);
                        wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css', array(), WOOF_VERSION);
                        $skin = 'round';
                        if (isset(woof()->settings['ion_slider_skin'])) {
                            $skin = woof()->settings['ion_slider_skin'];
                        }
                        $skin = WOOF_HELPER::check_new_ion_skin($skin);
                        $min = 0;
                        $max = 100;
                        if (!isset($meta_settings['range'])) {
                            $meta_settings['range'] = "1-100";
                        }
                        if (!isset($meta_settings['step'])) {
                            $meta_settings['step'] = 1;
                        }
                        if (!isset($meta_settings['prefix']) OR!isset($meta_settings['postfix'])) {
                            $meta_settings['prefix'] = $meta_settings['postfix'] = "";
                        }
                        if (!isset($meta_settings['step'])) {
                            $meta_settings['step'] = 1;
                        }
                        $min_max = explode("-", $meta_settings['range'], 2);
                        if (count($min_max) > 1) {
                            $min = floatval($min_max[0]);
                            $max = floatval($min_max[1]);
                        }
                        ?>
                <div class="woof_qt_add_filter ">
                    <input class="woof_qt_meta_slider" data-skin="<?php echo esc_attr($skin) ?>" data-tax="<?php echo esc_attr($meta_item['meta_key']) ?>"  data-min="<?php echo esc_attr($min) ?>" data-max="<?php echo esc_attr($max) ?>" data-step="<?php echo esc_attr($meta_settings['step']) ?>" data-slider-prefix="<?php echo esc_html($meta_settings['prefix']) ?>" data-slider-postfix="<?php echo esc_html($meta_settings['postfix']) ?>" value="" />
                </div>
                <?php
                break;
            default :
                break;
        }
    }

}
