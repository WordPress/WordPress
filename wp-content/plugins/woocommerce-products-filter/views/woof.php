<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
//+++
$args = array();
$args['show_count'] = get_option('woof_show_count', 0);
if ($dynamic_recount == -1) {
    $args['show_count_dynamic'] = get_option('woof_show_count_dynamic', 0);
} else {
    $args['show_count_dynamic'] = $dynamic_recount;
}
$args['hide_dynamic_empty_pos'] = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos', 0);
$args['woof_autosubmit'] = $autosubmit;
//***

WOOF_REQUEST::set('tax_only', $tax_only);
WOOF_REQUEST::set('tax_exclude', $tax_exclude);
WOOF_REQUEST::set('by_only', $by_only);

if (!function_exists('woof_show_btn')) {

    function woof_show_btn($autosubmit = 1, $ajax_redraw = 0) {
        ?>
        <div class="woof_submit_search_form_container">
            <?php
            $is_searh_active = woof()->is_isset_in_request_data(woof()->get_swoof_search_slug());
            $request = woof()->get_request_data(true);

            if ($is_searh_active AND ($request AND is_array($request))) {
                $not_search_request = [woof()->get_swoof_search_slug(), 'paged', 'really_curr_tax'];
                $request = array_diff(array_keys($request), $not_search_request);

                if (!count($request)) {
                    $is_searh_active = false;
                }
            }

            if ($is_searh_active OR woof()->is_isset_in_request_data('min_price') OR ( class_exists("WOOF_EXT_TURBO_MODE") AND isset(woof()->settings["woof_turbo_mode"]["enable"]) AND woof()->settings["woof_turbo_mode"]["enable"] )):
                global $woof_link;
                ?>

                <?php
                $woof_reset_btn_txt = get_option('woof_reset_btn_txt', '');
                if (empty($woof_reset_btn_txt) OR woof()->show_notes) {
                    $woof_reset_btn_txt = esc_html__('Reset', 'woocommerce-products-filter');
                }
                $woof_reset_btn_txt = WOOF_HELPER::wpml_translate(null, $woof_reset_btn_txt);
                ?>

                <?php if ($woof_reset_btn_txt != 'none'): ?>
                    <button  class="button woof_reset_search_form" data-link="<?php echo esc_url_raw($woof_link ?: '') ?>"><?php esc_html_e($woof_reset_btn_txt) ?></button>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$autosubmit OR $ajax_redraw): ?>
                <?php
                $woof_filter_btn_txt = get_option('woof_filter_btn_txt', '');
                if (empty($woof_filter_btn_txt) OR woof()->show_notes) {
                    $woof_filter_btn_txt = esc_html__('Filter', 'woocommerce-products-filter');
                }

                $woof_filter_btn_txt = WOOF_HELPER::wpml_translate(null, $woof_filter_btn_txt);
                ?>
                <button class="button woof_submit_search_form"><?php esc_html_e($woof_filter_btn_txt) ?></button>
            <?php endif; ?>

        </div>
        <?php
    }

}

if (!function_exists('woof_only')) {

    function woof_only($key_slug, $type = 'taxonomy') {

        switch ($type) {
            case 'taxonomy':

                if (!empty(WOOF_REQUEST::get('tax_only'))) {
                    if (!in_array($key_slug, WOOF_REQUEST::get('tax_only'))) {
                        return FALSE;
                    }
                }

                if (!empty(WOOF_REQUEST::get('tax_exclude'))) {
                    if (in_array($key_slug, WOOF_REQUEST::get('tax_exclude'))) {
                        return FALSE;
                    }
                }

                break;

            case 'item':
                if (!empty(WOOF_REQUEST::get('by_only'))) {
                    if (!in_array($key_slug, WOOF_REQUEST::get('by_only'))) {
                        return FALSE;
                    }
                }
                if (!empty(WOOF_REQUEST::get('tax_exclude'))) {
                    if (in_array($key_slug, WOOF_REQUEST::get('tax_exclude'))) {
                        return FALSE;
                    }
                }
                break;
        }


        return TRUE;
    }

}

//Sort logic  for shortcode [woof] attr tax_only
if (!function_exists('woof_print_tax')) {

    function get_order_by_tax_only($t_order, $t_only) {
        $temp_array = array_intersect($t_order, $t_only);
        $i = 0;
        foreach ($temp_array as $key => $val) {
            $t_order[$key] = $t_only[$i];
            $i++;
        }
        return $t_order;
    }

}
//***
if (!function_exists('woof_print_tax')) {

    function woof_print_tax($taxonomies, $tax_slug, $terms, $exclude_tax_key, $taxonomies_info, $additional_taxes, $woof_settings, $args, $counter) {



        if ($exclude_tax_key == $tax_slug) {
            if (empty($terms)) {
                return;
            }
        }

        //***

        if (!woof_only($tax_slug, 'taxonomy')) {
            return;
        }

        //***


        $args['taxonomy_info'] = $taxonomies_info[$tax_slug];
        $args['tax_slug'] = $tax_slug;
        $args['terms'] = $terms;
        $args['all_terms_hierarchy'] = $taxonomies[$tax_slug];
        $args['additional_taxes'] = $additional_taxes;

        //***
        $woof_container_styles = "";
        if ($woof_settings['tax_type'][$tax_slug] == 'radio' OR $woof_settings['tax_type'][$tax_slug] == 'checkbox') {
            if (isset(woof()->settings['tax_block_height']) && woof()->settings['tax_block_height'][$tax_slug] > 0) {
                $woof_container_styles = "max-height:" . sanitize_text_field(woof()->settings['tax_block_height'][$tax_slug]) . "px; overflow-y: auto;";
            }
        }
        //***
        //https://wordpress.org/support/topic/adding-classes-woof_container-div
        $primax_class = sanitize_key(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug]));
        ?>
        <div data-css-class="woof_container_<?php echo esc_attr($tax_slug) ?>" class="woof_container woof_container_<?php echo esc_attr($woof_settings['tax_type'][$tax_slug]) ?> woof_container_<?php echo esc_attr($tax_slug) ?> woof_container_<?php echo esc_attr($counter) ?> woof_container_<?php echo esc_attr($primax_class) ?>">
            <div class="woof_container_overlay_item"></div>
            <div class="woof_container_inner woof_container_inner_<?php echo esc_attr($primax_class) ?>">
                <?php
                $css_classes = "woof_block_html_items";
                $show_toggle = 0;
                if (isset(woof()->settings['show_toggle_button'][$tax_slug])) {
                    $show_toggle = (int) woof()->settings['show_toggle_button'][$tax_slug];
                }
                $tooltip_text = "";
                if (isset(woof()->settings['tooltip_text'][$tax_slug])) {
                    $tooltip_text = woof()->settings['tooltip_text'][$tax_slug];
                }
                //***
                $search_query = woof()->get_request_data();
                $block_is_closed = true;
                if (in_array($tax_slug, array_keys($search_query))) {
                    $block_is_closed = false;
                }
                if ($show_toggle === 1 AND !in_array($tax_slug, array_keys($search_query))) {
                    $css_classes .= " woof_closed_block";
                }

                if ($show_toggle === 2 AND !in_array($tax_slug, array_keys($search_query))) {
                    $block_is_closed = false;
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
                switch ($woof_settings['tax_type'][$tax_slug]) {
                    case 'checkbox':
                        if (woof()->settings['show_title_label'][$tax_slug]) {
                            ?>
                            <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug])) ?>
                            <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug]), $tooltip_text) ?>
                            <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                            </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php
                        }

                        if (!empty($woof_container_styles)) {
                            $css_classes .= " woof_section_scrolled";
                        }
                        ?>
                        <div class="<?php echo esc_attr($css_classes) ?>" <?php if (!empty($woof_container_styles)): ?>style="<?php echo wp_kses_post(wp_unslash($woof_container_styles)) ?>"<?php endif; ?>>
                            <?php
                            woof()->render_html_e(apply_filters('woof_html_types_view_checkbox', WOOF_PATH . 'views/html_types/checkbox.php'), $args);
                            ?>
                        </div>
                        <?php
                        break;
                    case 'select':
                        if (woof()->settings['show_title_label'][$tax_slug]) {
                            ?>
                            <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug])) ?>
                            <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug]), $tooltip_text) ?>
                            <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                            </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php
                        }
                        ?>
                        <div class="<?php echo esc_html($css_classes) ?>">
                            <?php
                            woof()->render_html_e(apply_filters('woof_html_types_view_select', WOOF_PATH . 'views/html_types/select.php'), $args);
                            ?>
                        </div>
                        <?php
                        break;
                    case 'mselect':
                        if (woof()->settings['show_title_label'][$tax_slug]) {
                            ?>
                            <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug])) ?>
                            <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug]), $tooltip_text) ?>
                            <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                            </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php
                        }
                        ?>
                        <div class="<?php echo esc_html($css_classes) ?>">
                            <?php
                            woof()->render_html_e(apply_filters('woof_html_types_view_mselect', WOOF_PATH . 'views/html_types/mselect.php'), $args);
                            ?>
                        </div>
                        <?php
                        break;

                    default:
                        if (woof()->settings['show_title_label'][$tax_slug]) {
                            $title = WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug]);
                            $title = explode('^', $title); //for hierarchy drop-down and any future manipulations
                            if (isset($title[1])) {
                                $title = $title[1];
                            } else {
                                $title = $title[0];
                            }
                            ?>
                            <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php echo esc_html($title) ?>
                            <?php WOOF_HELPER::draw_tooltipe($title, $tooltip_text) ?>
                            <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                            </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php
                        }

                        if (!empty($woof_container_styles)) {
                            $css_classes .= " woof_section_scrolled";
                        }
                        ?>

                        <div class="<?php echo esc_attr($css_classes) ?>" <?php if (!empty($woof_container_styles)): ?>style="<?php echo wp_kses_post(wp_unslash($woof_container_styles)) ?>"<?php endif; ?>>

                            <?php
                            if (!empty(WOOF_EXT::$includes['taxonomy_type_objects'])) {
                                $is_custom = false;
                                foreach (WOOF_EXT::$includes['taxonomy_type_objects'] as $obj) {

                                    $is = $obj->html_type === $woof_settings['tax_type'][$tax_slug];

                                    $info = apply_filters('woof_taxonomy_type_objects_front_render',
                                            $is,
                                            $obj->html_type,
                                            $woof_settings['tax_type'][$tax_slug],
                                            $args);

                                    if (!empty($info) && isset($info['args'])) {
                                        $args = $info['args'];
                                        $is = $info['is'];
                                    }

                                    if ($is) {
                                        $is_custom = true;
                                        $args['woof_settings'] = $woof_settings;
                                        $args['taxonomies_info'] = $taxonomies_info;
                                        woof()->render_html_e($obj->get_html_type_view(), $args);
                                        break;
                                    }
                                }


                                if (!$is_custom) {
                                    woof()->render_html_e(apply_filters('woof_html_types_view_radio', WOOF_PATH . 'views/html_types/radio.php'), $args);
                                }
                            } else {
                                woof()->render_html_e(apply_filters('woof_html_types_view_radio', WOOF_PATH . 'views/html_types/radio.php'), $args);
                            }
                            ?>

                        </div>
                        <?php
                        break;
                }
                ?>

                <input type="hidden" name="woof_t_<?php echo esc_attr($tax_slug) ?>" value="<?php echo esc_html($taxonomies_info[$tax_slug]->labels->name) ?>" /><!-- for red button search nav panel -->

            </div>
        </div>
        <?php
    }

}

if (!function_exists('woof_print_item_by_key')) {

    function woof_print_item_by_key($key, $woof_settings, $additional_taxes) {

        if (!woof_only($key, 'item')) {
            return;
        }

        //***


        switch ($key) {
            case 'by_price':
                $price_filter = 0;
                if (isset(woof()->settings['by_price']['show'])) {
                    $price_filter = (int) woof()->settings['by_price']['show'];
                }
                $tooltip_text = "";
                if (isset(woof()->settings['by_price']['tooltip_text'])) {
                    $tooltip_text = woof()->settings['by_price']['tooltip_text'];
                }

                $min_pf = WOOF_HELPER::get_min_price($additional_taxes);
                $max_pf = WOOF_HELPER::get_max_price($additional_taxes);
                ?>

                <?php
                if ($price_filter == 1):
                    if ($min_pf == $max_pf) {
                        break;
                    }
                    ?>
                    <div data-css-class="woof_price_search_container" class="woof_price_search_container woof_container woof_price_filter">
                        <div class="woof_container_overlay_item"></div>
                        <div class="woof_container_inner">
                            <div class="woocommerce widget_price_filter">
                                <?php //the_widget('WC_Widget_Price_Filter', array('title' => ''));         ?>
                                <?php if (isset(woof()->settings['by_price']['title_text']) AND !empty(woof()->settings['by_price']['title_text'])): ?>
                                    <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                    <?php echo esc_html(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text'])); ?>
                                    <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text']), $tooltip_text) ?>
                                    </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php endif; ?>
                                <?php WOOF_HELPER::price_filter_e($additional_taxes, $min_pf, $max_pf); ?>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                <?php endif; ?>

                <?php if ($price_filter == 2): ?>
                    <div data-css-class="woof_price2_search_container" class="woof_price2_search_container woof_container woof_price_filter">
                        <div class="woof_container_overlay_item"></div>
                        <div class="woof_container_inner">
                            <?php if (isset(woof()->settings['by_price']['title_text']) AND !empty(woof()->settings['by_price']['title_text'])): ?>
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text'])); ?>
                                <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text']), $tooltip_text) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php endif; ?>

                            <?php echo do_shortcode('[woof_price_filter type="select" additional_taxes="' . esc_attr($additional_taxes) . '"]'); ?>

                        </div>
                    </div>
                <?php endif; ?>


                <?php
                if ($price_filter == 3):

                    if ($min_pf == $max_pf) {
                        break;
                    }
                    ?>
                    <div data-css-class="woof_price3_search_container" class="woof_price3_search_container woof_container woof_price_filter">
                        <div class="woof_container_overlay_item"></div>
                        <div class="woof_container_inner">
                            <?php if (isset(woof()->settings['by_price']['title_text']) AND !empty(woof()->settings['by_price']['title_text'])): ?>
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text'])); ?>
                                <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text']), $tooltip_text) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php endif; ?>

                            <?php echo do_shortcode('[woof_price_filter range_min=' . esc_attr($min_pf) . ' range_max=' . esc_attr($max_pf) . ' type="slider" additional_taxes="' . esc_attr($additional_taxes) . '"]'); ?>

                        </div>
                    </div>
                <?php endif; ?>


                <?php if ($price_filter == 4): ?>
                    <div data-css-class="woof_price4_search_container" class="woof_price4_search_container woof_container woof_price_filter">
                        <div class="woof_container_overlay_item"></div>
                        <div class="woof_container_inner">
                            <?php if (isset(woof()->settings['by_price']['title_text']) AND !empty(woof()->settings['by_price']['title_text'])): ?>
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text'])); ?>
                                <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text']), $tooltip_text) ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php endif; ?>

                            <?php echo do_shortcode('[woof_price_filter type="text" additional_taxes="' . esc_attr($additional_taxes) . '"]'); ?>

                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($price_filter == 5): ?>
                    <div data-css-class="woof_price5_search_container" class="woof_price5_search_container woof_container woof_price_filter">
                        <div class="woof_container_overlay_item"></div>
                        <div class="woof_container_inner">
                            <?php
                            $css_classes = "woof_block_html_items";
                            $show_toggle = 0;
                            if (isset(woof()->settings[$key]['show_toggle_button'])) {
                                $show_toggle = (int) woof()->settings[$key]['show_toggle_button'];
                            }
                            $tooltip_text = "";
                            if (isset(woof()->settings['tooltip_text'][$key])) {
                                $tooltip_text = woof()->settings['tooltip_text'][$key];
                            }
                            //***
                            $search_query = woof()->get_request_data();
                            $block_is_closed = true;
                            if (in_array("min_price", array_keys($search_query))) {
                                $block_is_closed = false;
                            }
                            if ($show_toggle === 1 AND !in_array("min_price", array_keys($search_query))) {
                                $css_classes .= " woof_closed_block";
                            }

                            if ($show_toggle === 2 AND !in_array("min_price", array_keys($search_query))) {
                                $block_is_closed = false;
                            }

                            if (in_array($show_toggle, array(1, 2))) {
                                $block_is_closed = apply_filters('woof_block_toggle_state', $block_is_closed);
                                if ($block_is_closed) {
                                    $css_classes .= " woof_closed_block";
                                } else {
                                    $css_classes = str_replace('woof_closed_block', '', $css_classes);
                                }
                            }
                            ?>
                            <?php if (isset(woof()->settings['by_price']['title_text']) AND !empty(woof()->settings['by_price']['title_text'])): ?>
                                <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, woof()->settings['by_price']['title_text'])); ?>
                                <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                                </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
                            <?php endif; ?>
                            <div class="<?php echo esc_attr($css_classes) ?>" <?php if (!empty($woof_container_styles)): ?>style="<?php echo wp_kses_post(wp_unslash($woof_container_styles)); ?>"<?php endif; ?>>
                                <?php echo do_shortcode('[woof_price_filter type="radio" additional_taxes="' . esc_attr($additional_taxes) . '"]'); ?>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

                <?php
                break;

            default:
                do_action('woof_print_html_type_' . $key);
                break;
        }
    }

}
?>


<?php if ($autohide): ?>
    <div class='woof_autohide_wrapper' >
        <?php
        //***
        $woof_auto_hide_button_txt = '';
        if (isset($this->settings['woof_auto_hide_button_txt'])) {
            $woof_auto_hide_button_txt = WOOF_HELPER::wpml_translate(null, $this->settings['woof_auto_hide_button_txt']);
        }
        ?>
        <a href="javascript:void(0);" class="woof_show_auto_form woof_btn_default <?php if (isset($this->settings['woof_auto_hide_button_img']) AND $this->settings['woof_auto_hide_button_img'] == 'none') echo esc_attr('woof_show_auto_form_txt'); ?>"><?php esc_html_e($woof_auto_hide_button_txt) ?></a><br />
        <!-------------------- inline css for js anim ----------------------->
        <div class="woof_auto_show woof_overflow_hidden" style="opacity: 0; height: 1px;">
            <div class="woof_auto_show_indent woof_overflow_hidden">
                <?php
            endif;

            $woof_class = "";
            if (wp_is_mobile() && (isset($mobile_mode) && $mobile_mode == 1) && isset($sid)) {
                $woof_class = 'woof_hide_filter';
            }
            ?>


            <div class="woof <?php if (!empty($sid)): ?>woof_sid woof_sid_<?php echo esc_attr($sid) ?><?php endif; ?> <?php echo esc_attr($woof_class) ?>" <?php if (!empty($sid)): ?>data-sid="<?php echo esc_attr($sid); ?>"<?php endif; ?> data-shortcode="<?php echo esc_html(WOOF_REQUEST::isset('woof_shortcode_txt') ? WOOF_REQUEST::get('woof_shortcode_txt') : 'woof') ?>" data-redirect="<?php echo esc_attr($redirect) ?>" data-autosubmit="<?php echo esc_attr($autosubmit) ?>" data-ajax-redraw="<?php echo esc_attr($ajax_redraw) ?>">
                <?php
                if (wp_is_mobile() && (isset($mobile_mode) && $mobile_mode) && isset($sid)) {
                    $image_mb_open = (isset($this->settings['image_mobile_behavior_open'])) ? $this->settings['image_mobile_behavior_open'] : '';
                    $image_mb_close = (isset($this->settings['image_mobile_behavior_close'])) ? $this->settings['image_mobile_behavior_close'] : '';
                    if ($image_mb_open != -1 && empty($image_mb_open)) {
                        $image_mb_open = WOOF_LINK . "img/open_filter.png";
                    }
                    if ($image_mb_close != -1 && empty($image_mb_close)) {
                        $image_mb_close = WOOF_LINK . "img/close_filter.png";
                    }
                    $text_mb_open = (isset($this->settings['text_mobile_behavior_open'])) ? $this->settings['text_mobile_behavior_open'] : esc_html__('Open filter', 'woocommerce-products-filter');
                    $text_mb_close = (isset($this->settings['text_mobile_behavior_close'])) ? $this->settings['text_mobile_behavior_close'] : esc_html__('Close filter', 'woocommerce-products-filter');
                    ?>
                    <div class="woof_show_mobile_filter" data-sid="<?php echo esc_attr($sid); ?>">
                        <?php if ($image_mb_open != -1) : ?>
                            <img src="<?php echo esc_url($image_mb_open); ?>" alt="">
                        <?php endif; ?>
                        <?php if ($text_mb_open != -1) : ?>
                            <span><?php echo esc_html(WOOF_HELPER::wpml_translate(null, $text_mb_open)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="woof_hide_mobile_filter" >
                        <?php if ($image_mb_close != -1) : ?>
                            <img src="<?php echo esc_url($image_mb_close); ?>" alt="">
                        <?php endif; ?>
                        <?php if ($text_mb_close != -1) : ?>
                            <span><?php echo esc_html(WOOF_HELPER::wpml_translate(null, $text_mb_close)); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
                <?php if ($show_woof_edit_view AND !empty($sid)): ?>
                    <a href="#" class="woof_edit_view" data-sid="<?php echo esc_attr($sid) ?>"><?php esc_html_e('show blocks helper', 'woocommerce-products-filter') ?></a>
                    <div></div>
                <?php endif; ?>

                <!--- here is possible to drop html code which is never redraws by AJAX ---->
                <?php echo wp_kses_post(wp_unslash(apply_filters('woof_print_content_before_redraw_zone', ''))) ?>

                <div class="woof_redraw_zone" data-woof-ver="<?php echo esc_attr(WOOF_VERSION) ?>">
                    <?php echo wp_kses_post(wp_unslash(apply_filters('woof_print_content_before_search_form', ''))) ?>
                    <?php
                    if (isset($start_filtering_btn) AND (int) $start_filtering_btn == 1) {
                        $start_filtering_btn = true;
                    } else {
                        $start_filtering_btn = false;
                    }

                    if (wp_doing_ajax()) {
                        $start_filtering_btn = false;
                    }

                    if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
                        $start_filtering_btn = false;
                    }
                    ?>

                    <?php if ($start_filtering_btn): ?>
                        <a href="#" class="<?php echo esc_attr(apply_filters('woof_button_css_classes', 'woof_button')) ?> woof_start_filtering_btn"><?php echo wp_kses_post($woof_start_filtering_btn_txt) ?></a>
                    <?php else: ?>
                        <?php
                        if ($btn_position == 't' OR $btn_position == 'tb' OR $btn_position == 'bt') {
                            woof_show_btn($autosubmit, $ajax_redraw);
                        }
                        global $wp_query;
                        //+++
                        {
                            $exclude_tax_key = '';
                            //code-bone for pages like
                            //http://dev.pluginus.net/product-category/clothing/ with GET params
                            //another way when GET is actual no possibility get current taxonomy
                            if ($this->is_really_current_term_exists()) {
                                $o = $this->get_really_current_term();
                                $exclude_tax_key = $o->taxonomy;
                            }
                            //***
                            if (!empty($wp_query->query)) {
                                if (isset($wp_query->query_vars['taxonomy']) AND in_array($wp_query->query_vars['taxonomy'], get_object_taxonomies('product'))) {
                                    $taxes = $wp_query->query;
                                    if (isset($taxes['paged'])) {
                                        unset($taxes['paged']);
                                    }

                                    foreach ($taxes as $key => $value) {
                                        if (in_array($key, array_keys($this->get_request_data()))) {
                                            unset($taxes[$key]);
                                        }
                                    }
                                    //***
                                    if (!empty($taxes)) {
                                        $t = array_keys($taxes);
                                        $v = array_values($taxes);
                                        //***
                                        $exclude_tax_key = $t[0];
                                        WOOF_REQUEST::set('WOOF_IS_TAX_PAGE', $exclude_tax_key);
                                    }
                                }
                            }

                            //***

                            $items_order = array();

                            $taxonomies_keys = array_keys($taxonomies);

                            if (isset($woof_settings['items_order']) AND !empty($woof_settings['items_order'])) {
                                $items_order = explode(',', $woof_settings['items_order']);
                            } else {
                                $items_order = array_merge($this->items_keys, $taxonomies_keys);
                            }

                            //*** lets check if we have new taxonomies added in woocommerce or new item
                            foreach (array_merge($this->items_keys, $taxonomies_keys) as $key) {
                                if (!in_array($key, $items_order)) {
                                    $items_order[] = $key;
                                }
                            }

                            //lets print our items and taxonomies
                            $counter = 0;

                            if (count($tax_only) > 0) {
                                $items_order = get_order_by_tax_only($items_order, $tax_only);
                            }

                            if (isset($by_step)) {
                                $new_items_order = explode(',', $by_step);
                                $items_order = array_map('trim', $new_items_order);
                            }

                            $items_order = apply_filters('woof_custom_filter_items_order', $items_order);
                            $tax_show = array();
                            if (isset($shortcode_atts['tax_only'])) {
                                $tax_show = explode(',', $shortcode_atts['tax_only']);
                            }

                            foreach ($items_order as $key) {
                                do_action('woof_before_draw_filter', $key, $shortcode_atts);

                                if (in_array($key, $this->items_keys)) {
                                    woof_print_item_by_key($key, $woof_settings, $additional_taxes);
                                } else {
                                    if (!isset($woof_settings['tax'][$key])) {
                                        continue;
                                    }

                                    woof_print_tax($taxonomies, $key, $taxonomies[$key], $exclude_tax_key, $taxonomies_info, $additional_taxes, $woof_settings, $args, $counter);
                                }
                                do_action('woof_after_draw_filter', $key, $shortcode_atts);
                                $counter++;
                            }
                        }
                        ?>


                        <?php
                        //submit form
                        if ($btn_position == 'b' OR $btn_position == 'tb' OR $btn_position == 'bt') {
                            woof_show_btn($autosubmit, $ajax_redraw);
                        }
                        ?>

                    <?php endif; ?>



                </div>

            </div>



            <?php if ($autohide): ?>
            </div>
        </div>

    </div>
<?php endif; ?>