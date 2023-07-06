<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if (!empty($meta_options)) {
    $meta_options = explode($options_separator, $meta_options);
} else {
    $meta_options = array();
}

$request = woof()->get_request_data();
$woof_value = "";
if (isset($request[$meta_key])) {
    $woof_value = $request[$meta_key];
}
$show_title_label = (isset($this->woof_settings[$this->meta_key]['show_title_label'])) ? $this->woof_settings[$this->meta_key]['show_title_label'] : 1;
$css_classes = "woof_block_html_items";
$show_toggle = 0;
$shown_options_tags = 0;
if (isset($this->woof_settings[$this->meta_key]["show_toggle_button"])) {
    $show_toggle = (int) $this->woof_settings[$this->meta_key]["show_toggle_button"];
}
//***
$block_is_closed = true;
if (!empty($woof_value)) {
    $block_is_closed = false;
}
if ($show_toggle === 1 AND empty($woof_value)) {
    $css_classes .= " woof_closed_block";
}

if ($show_toggle === 2 AND empty($woof_value)) {
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
//***
if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) !== -1) {
    if (intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) === 1) {
        WOOF_REQUEST::set('hide_terms_count_txt', 1);
    } else {
        WOOF_REQUEST::set('hide_terms_count_txt', 0);
    }
}
//***
//meta options
$all_options = array();
$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos', 0);

if (is_array($options)) {
    foreach ($options as $key => $option) {

        if (!$option) {
            continue;
        }

        $custom_title = explode(':', $option, 2);
		$option_val = $custom_title[0];
		$option_title = $custom_title[0];
        if (count($custom_title) > 1) {   
            $option_title = $custom_title[1];
        }
        $count_string = "";
        $count = 0;

        if ($woof_value != $key) {
            if ($show_count) {
                $meta_field = array(
                    'key' => $meta_key,
                    'value' => $option,
                );

                if ($show_count_dynamic) {
                    $count_data = array();
                    $count = woof()->dynamic_count(array(), 'select', (WOOF_REQUEST::isset('additional_taxes')) ? WOOF_REQUEST::get('additional_taxes') : "", $meta_field);
                    $count_string = '(' . $count . ')';
                } else {
                    $count = 1;
                    //$count = $term['count'];
                }
            }
            //+++
            if ($hide_dynamic_empty_pos AND $count == 0) {
                continue;
            }
        }

        if (WOOF_REQUEST::get('hide_terms_count_txt')) {
            $count_string = "";
        }
        $all_options[$key] = array(
            'name' => WOOF_HELPER::wpml_translate(null, $option_title) . $count_string,
            'count' => $count
        );
    }
}

if (!count($all_options)) {
    return "";
}
?>
<div data-css-class="woof_acf_radio_container" class="woof_acf_radio_container woof_container woof_container_<?php echo esc_attr($meta_key) ?>  woof_container_<?php echo esc_attr( $meta_key) ?>">
    <div class="woof_container_inner">
        <div class="woof_container_inner woof_container_inner_acf_radio">
            <?php if ($show_title_label) {
                ?>
                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, $meta_title)) ?>
                <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, $meta_title), $tooltip_text) ?>
                <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
            <?php }
            ?>
            <div class="<?php echo esc_attr($css_classes) ?>">
				
<ul class="woof_list woof_list_radio">
        <?php foreach ($all_options as $key => $option) : $inique_id = uniqid(); ?>

            <li class="woof_term_<?php echo esc_attr($key) ?> ">
                <input type="radio" <?php if (!$option['count'] AND $key != $woof_value AND $show_count): ?>disabled=""<?php endif; ?> id="<?php echo esc_attr('woof_' . $key . '_' . $inique_id) ?>" class="woof_radio_term woof_radio_term_<?php echo esc_attr($key) ?>" data-slug="<?php echo esc_attr($key) ?>" data-term-id="<?php echo esc_attr($key) ?>" name="<?php echo esc_attr($meta_key) ?>" value="<?php echo esc_attr($key) ?>" <?php checked($key == $woof_value) ?> />
                <label class="woof_radio_label <?php if ($key == $woof_value): ?>woof_radio_label_selected<?php endif; ?>" for="<?php echo esc_attr('woof_' . $key . '_' . $inique_id) ?>"><?php

            ?><?php echo wp_kses_post(wp_unslash($option['name'])) ?></label>

                <a href="#" data-name="<?php echo esc_attr($meta_key) ?>" data-term-id="<?php echo esc_attr($key) ?>" style="<?php if ($key != $woof_value): ?>display: none;<?php endif; ?>" class="woof_radio_term_reset  <?php if ($key == $woof_value): ?>woof_radio_term_reset_visible<?php endif; ?> woof_radio_term_reset_<?php echo esc_attr($key) ?>">
                    <img src="<?php echo esc_url(woof()->settings['delete_image']) ?>" height="12" width="12" alt="<?php esc_html_e("Delete", 'woocommerce-products-filter') ?>" />
                </a>

            </li>
            <?php
        endforeach;
        ?>
</ul>				
    <?php
                $curr_title = "";
                if (isset($options[$woof_value])) {
                    $op_title = explode(':', $options[$woof_value ], 2);
                    if (count($op_title) > 1) {
                        $curr_title = $op_title[1];
                    } else {
                        $curr_title = $options[$woof_value];
                    }
                }
                ?>   
                <input type="hidden" value="<?php echo esc_html(WOOF_HELPER::wpml_translate(null, $curr_title)); ?>" data-anchor="woof_n_<?php echo esc_attr( $meta_key) ?>_<?php echo esc_attr($woof_value) ?>" />				
				

            </div>    
        </div>        
    </div>
</div>


