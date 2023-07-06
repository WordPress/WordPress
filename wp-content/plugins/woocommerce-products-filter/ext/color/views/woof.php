<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php

//http://code.tutsplus.com/articles/how-to-use-wordpress-color-picker-api--wp-33067
$colors = isset($woof_settings['color'][$tax_slug]) ? $woof_settings['color'][$tax_slug] : array();
$colors_imgs = isset($woof_settings['color_img'][$tax_slug]) ? $woof_settings['color_img'][$tax_slug] : array();
$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos', 0);
$woof_autosubmit = get_option('woof_autosubmit', 0);
//********************
$show_tooltip = $this->settings['show_tooltip'][$tax_slug];
$color_type = "checkbox";
if (isset(woof()->settings['as_radio_color'][$tax_slug]) AND woof()->settings['as_radio_color'][$tax_slug]) {
    $color_type = "radio";
}

$show_title = 0;

if (isset($this->settings['show_title_column'][$tax_slug])) {
    $show_title = (int) $this->settings['show_title_column'][$tax_slug];
}

$show_title_class = "";
if ($show_title) {
    $show_title_class = "woof_color_title_col";
}
?>

<ul class = "woof_list woof_list_color <?php echo esc_attr($show_title_class) ?>" data-type="<?php echo esc_attr($color_type) ?>">
    <?php
    $woof_tax_values = array();
    $current_request = array();
    $request = woof()->get_request_data();
    WOOF_REQUEST::set('additional_taxes', $additional_taxes);
    WOOF_REQUEST::set('hide_terms_count_txt', isset(woof()->settings['hide_terms_count_txt']) ? woof()->settings['hide_terms_count_txt'] : 0);
    //***
    if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND WOOF_REQUEST::get('hide_terms_count_txt_short') != -1) {
        if (intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) === 1) {
            WOOF_REQUEST::set('hide_terms_count_txt', 1);
        } else {
            WOOF_REQUEST::set('hide_terms_count_txt', 0);
        }
    }
    //***
    if (woof()->is_isset_in_request_data(woof()->check_slug($tax_slug))) {
        $current_request = $request[woof()->check_slug($tax_slug)];
        $current_request = explode(',', urldecode($current_request));
    }
//excluding hidden terms
    $hidden_terms = array();
    if (!WOOF_REQUEST::isset('woof_shortcode_excluded_terms')) {
        if (isset(woof()->settings['excluded_terms'][$tax_slug])) {
            $hidden_terms = explode(',', woof()->settings['excluded_terms'][$tax_slug]);
        }
    } else {
        $hidden_terms = explode(',', WOOF_REQUEST::get('woof_shortcode_excluded_terms'));
    }


    //***

    $not_toggled_terms_count = 0;
    if (isset(woof()->settings['not_toggled_terms_count'][$tax_slug])) {
        $not_toggled_terms_count = intval(woof()->settings['not_toggled_terms_count'][$tax_slug]);
    }
    //***

    $terms = apply_filters('woof_sort_terms_before_out', $terms, 'color');
    $terms_count_printed = 0;
    $hide_next_term_li = false;
    ?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : $inique_id = uniqid(); ?>
            <?php
            $count_string = "";
            $count = 0;
            if (!in_array($term['slug'], $current_request)) {
                if ($show_count) {
                    if ($show_count_dynamic) {
                        $count = woof()->dynamic_count($term, 'multi', WOOF_REQUEST::get('additional_taxes'));
                    } else {
                        $count = $term['count'];
                    }
                    $count_string = '<span>(' . $count . ')</span>';
                }
                //+++
                if ($hide_dynamic_empty_pos AND $count == 0) {
                    continue;
                }
            }

            if (WOOF_REQUEST::get('hide_terms_count_txt')) {
                $count_string = "";
            }

            $color = '#000000';
            if (isset($colors[$term['slug']])) {
                $color = $colors[$term['slug']];
            }

            $color_img = '';
            if (isset($colors_imgs[$term['slug']]) AND!empty($colors_imgs[$term['slug']])) {
                $color_img = $colors_imgs[$term['slug']];
            }

            //excluding hidden terms
            $inreverse = true;
            if (isset(woof()->settings['excluded_terms_reverse'][$tax_slug]) AND woof()->settings['excluded_terms_reverse'][$tax_slug]) {
                $inreverse = !$inreverse;
            }
            if (in_array($term['term_id'], $hidden_terms) == $inreverse) {
                continue;
            }


            if ($not_toggled_terms_count > 0 AND $terms_count_printed === $not_toggled_terms_count) {
                $hide_next_term_li = true;
            }


            $term_desc = strip_tags(term_description($term['term_id'], $term['taxonomy']));
            ?>
            <li class="woof_color_term_<?php echo esc_attr($color) ?> woof_color_term_<?php echo esc_attr($term['term_id']) ?> <?php if ($hide_next_term_li): ?>woof_hidden_term<?php endif; ?>">

                <p class="woof_tooltip">
                    <?php if ($show_tooltip): ?>
                        <span class="woof_tooltip_data"><?php echo esc_html($term['name']) ?> 
                            <?php echo wp_kses_post(wp_unslash($count_string)) ?><?php if(!empty($term_desc)): ?><br /><i><?php echo esc_html($term_desc) ?></i><?php endif; ?>
                        </span>
                    <?php endif; ?>
                    <?php $color_id = 'woof_tax_color_' . sanitize_text_field($term['slug']); ?>
                    <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($color_id) ?>"><?php echo esc_html($term['name']); ?></label>	
                    <input id="<?php echo esc_attr($color_id) ?>" type="checkbox" <?php checked(in_array($term['slug'], $current_request)) ?> id="<?php echo esc_attr('woof_' . $term['term_id']) . '_' . esc_attr($inique_id) ?>" class="woof_color_term woof_color_term_<?php echo esc_attr($term['term_id']) ?> <?php if (in_array($term['slug'], $current_request)): ?>checked<?php endif; ?>" data-color="<?php echo esc_html($color) ?>" data-img="<?php echo esc_url($color_img) ?>" data-tax="<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>" name="<?php echo esc_attr($term['slug']) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" value="<?php echo esc_html($term['term_id']) ?>" <?php checked(in_array($term['slug'], $current_request)) ?> /></p>

                <input type="hidden" value="<?php echo esc_html($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>_<?php echo esc_attr($term['slug']) ?>" />

                <?php
                if ($show_title) {
                    ?>
                    <span class="woof_color_title <?php echo esc_attr(in_array($term['slug'], $current_request) ? "woof_checkbox_label_selected" : "") ?>"><?php echo esc_attr($term['name']) ?><?php echo wp_kses_post(wp_unslash($count_string)) ?></span>
                    <?php
                }
                ?>
            </li>
            <?php
            $terms_count_printed++;
        endforeach;
        ?>

        <?php
        if ($not_toggled_terms_count > 0 AND $terms_count_printed > $not_toggled_terms_count):
            ?>
            <li class="woof_open_hidden_li"><?php WOOF_HELPER::draw_more_less_button('color') ?></li>
        <?php endif; ?>
    <?php endif; ?>
</ul>
<div class="clear clearfix"></div>
<?php
//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
WOOF_REQUEST::del('additional_taxes');

