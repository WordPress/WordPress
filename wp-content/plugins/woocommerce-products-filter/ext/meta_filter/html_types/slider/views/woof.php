<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php


$skin = 'round';
if (isset(woof()->settings['ion_slider_skin'])) {
    $skin = woof()->settings['ion_slider_skin'];
}
$skin = WOOF_HELPER::check_new_ion_skin($skin);
if (isset($meta_settings['meta_slider_skin']) && $meta_settings['meta_slider_skin']) {
    $skin = $meta_settings['meta_slider_skin'];
}
$use_prettify = 'false';
if (isset($meta_settings['use_prettify']) && $meta_settings['use_prettify']) {
    $use_prettify = 'true';
}

$request = woof()->get_request_data();
$current_request_txt = "";
if (woof()->is_isset_in_request_data("slider_" . $meta_key)) {
    $current_request_txt = $request["slider_" . $meta_key];
    $current_request = explode('^', urldecode($current_request_txt));
} else {
    $current_request = array();
}

//***

$min = 0;
$max = 100;
$min_max = explode("^", $range, 2);
if (count($min_max) > 1) {
    $min = floatval($min_max[0]);
    $max = floatval($min_max[1]);
}

$min_value = $min;
$max_value = $max;
if (!empty($current_request)) {
    $min_value = floatval($current_request[0]);
    $max_value = floatval($current_request[1]);
    if ($min_value < $min) {
        $min_value = $min;
    }
    if ($max_value > $max) {
        $max_value = $max;
    }
}
//Check if slider has  products in current request
$count = 0;
$show = true;
$hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos', 0);
if (empty($current_request)) {
    if ($hide_dynamic_empty_pos) {
        $meta_field = array(
            'key' => $meta_key,
            'value' => array($min, $max),
        );
        $count_data = array();
        $count = woof()->dynamic_count(array(), 'slider', (WOOF_REQUEST::isset('additional_taxes')) ? WOOF_REQUEST::get('additional_taxes') : "", $meta_field);
    }
    //+++
    if ($hide_dynamic_empty_pos AND $count == 0) {
        $show = false;
    }
}

$show_title_label = (isset($meta_settings['show_title_label'])) ? $meta_settings['show_title_label'] : 1;
$css_classes = "woof_block_html_items";
$show_toggle = 0;
if (isset($meta_settings['show_toggle_button'])) {
    $show_toggle = (int) $meta_settings['show_toggle_button'];
}
//***
$block_is_closed = true;
if (!empty($current_request)) {
    $block_is_closed = false;
}
if ($show_toggle === 1 AND empty($current_request)) {
    $css_classes .= " woof_closed_block";
}

if ($show_toggle === 2 AND empty($current_request)) {
    $block_is_closed = false;
}
$tooltip_text = "";
if (isset($meta_settings['tooltip_text'])) {
    $tooltip_text = $meta_settings['tooltip_text'];
}
if (in_array($show_toggle, array(1, 2))) {
    $block_is_closed = apply_filters('woof_block_toggle_state', $block_is_closed);
    if ($block_is_closed) {
        $css_classes .= " woof_closed_block";
    } else {
        $css_classes = str_replace('woof_closed_block', '', $css_classes);
    }
}

if ($show):
    $top_panel_txt = "";
    $top_panel_txt = WOOF_HELPER::wpml_translate(null, $options['title']);
    $top_panel_txt .= sprintf(":%s %s %s", $meta_settings['prefix'], str_replace("^", "-", $current_request_txt), $meta_settings['postfix']);
    $slider_id = "woof_meta_slider_" . $meta_key;
    ?>
    <div data-css-class="woof_meta_slider_container" class="woof_meta_slider_container woof_container woof_container_<?php echo esc_attr("slider_" . $meta_key) ?>">
        <div class="woof_container_inner">
            <div class="woof_container_inner woof_container_inner_meta_slider">
                <?php if ($show_title_label) {
                    ?>
                    <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                    <?php echo esc_html(WOOF_HELPER::wpml_translate(null, $options['title'])) ?>
                    <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, $options['title']), $tooltip_text) ?>
                    <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                    </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                <?php }
                ?>
                <div class="<?php echo esc_attr($css_classes) ?>">
                    <?php
                    if (isset($meta_settings['show_inputs']) AND $meta_settings['show_inputs']) {
                        ?>
                        <div class="woof_metarange_slider_inputs">
                            <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($slider_id) ?>_from"><?php esc_html_e('From', 'woocommerce-products-filter') ?></label>
                            <input id="<?php echo esc_attr($slider_id) ?>_from" type="number" data-name="<?php echo esc_attr("slider_" . $meta_key) ?>" class="woof_metarange_slider_input_<?php echo esc_attr($meta_key) ?> woof_metarange_slider_from" placeholder="<?php echo esc_html($min) ?>" min="<?php echo esc_html($min) ?>" max="<?php echo esc_html($max) ?>"  value="<?php echo esc_html($min_value) ?>" />&nbsp;
                            <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($slider_id) ?>_to"><?php esc_html_e('To', 'woocommerce-products-filter') ?></label>
                            <input id="<?php echo esc_attr($slider_id) ?>_to" type="number" data-name="<?php echo esc_attr("slider_" . $meta_key) ?>" class="woof_metarange_slider_input_<?php echo esc_attr($meta_key) ?> woof_metarange_slider_to" placeholder="<?php echo esc_html($max) ?>" min="<?php echo esc_html($min) ?>" max="<?php echo esc_html($max) ?>"  value="<?php echo esc_html($max_value) ?>" />
                            <div class="woof_float_none"></div>
                        </div>
                    <?php } ?>

                    <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($slider_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate(null, $options['title'])) ?></label>
                    <input id="<?php echo esc_attr($slider_id) ?>" class="woof_metarange_slider" name="<?php echo esc_attr('slider_' . $meta_key) ?>" data-skin="<?php echo esc_attr($skin) ?>" data-min="<?php echo esc_html($min) ?>" data-max="<?php echo esc_html($max) ?>" data-min-now="<?php echo esc_html($min_value) ?>" data-max-now="<?php echo esc_html($max_value) ?>" data-step="<?php echo esc_html($meta_settings['step']) ?>" data-slider-prefix="<?php echo esc_html($meta_settings['prefix']) ?>" data-slider-postfix="<?php echo esc_html($meta_settings['postfix']) ?>"  data-prettify="<?php echo esc_html($use_prettify); ?>" value="" />
                </div>
                <input type="hidden" value="<?php echo esc_html($top_panel_txt) ?>" data-anchor="woof_n_<?php echo esc_attr('slider_' . $meta_key) ?>_<?php echo esc_attr($current_request_txt) ?>" />
            </div>
        </div>
    </div>
<?php endif; ?>