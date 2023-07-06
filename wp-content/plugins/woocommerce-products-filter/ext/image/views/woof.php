<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php

$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos', 0);
$woof_autosubmit = get_option('woof_autosubmit', 0);
$image_type = "checkbox";
if (isset(woof()->settings['as_radio'][$tax_slug]) AND woof()->settings['as_radio'][$tax_slug]) {
    $image_type = "radio";
}
//********************
$add_description = apply_filters('woof_image_allow_term_desc', true, $tax_slug);
?>

<ul class = "woof_list woof_list_image" data-type="<?php echo esc_attr($image_type) ?>">
    <?php
    $woof_tax_values = array();
    $current_request = array();
    $request = woof()->get_request_data();
    WOOF_REQUEST::set('additional_taxes', $additional_taxes);
    WOOF_REQUEST::set('hide_terms_count_txt', isset(woof()->settings['hide_terms_count_txt']) ? woof()->settings['hide_terms_count_txt'] : 0);
    //***
    if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) !== -1) {
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

    $terms = apply_filters('woof_sort_terms_before_out', $terms, 'image');
    $terms_count_printed = 0;
    $hide_next_term_li = false;
    ?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : $inique_id = uniqid(); ?>
            <?php
            //excluding hidden terms
            $inreverse = true;
            if (isset(woof()->settings['excluded_terms_reverse'][$tax_slug]) AND woof()->settings['excluded_terms_reverse'][$tax_slug]) {
                $inreverse = !$inreverse;
            }
            if (in_array($term['term_id'], $hidden_terms) == $inreverse) {
                continue;
            }

            //***

            $term_key = 'images_term_' . $term['term_id'];
            $images = isset($woof_settings[$term_key]) ? $woof_settings[$term_key] : array();

            $image = '';

            if (empty($image = apply_filters('woof_taxonomy_image', $image, $term))) {
                if (isset($images['image_url']) AND!empty($images['image_url'])) {
                    $image = $images['image_url'];
                } else {
                    continue;
                }

                if ($images['image_url'] == 'hide') {
                    continue;
                }
            }


            //***

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


            if ($add_description) {
                $term_desc = strip_tags(term_description($term['term_id'], $term['taxonomy']));
            }
            //***

            if (isset($images['image_styles'])) {
                $styles = trim($images['image_styles']);
            }

            //***

            if ($not_toggled_terms_count > 0 AND $terms_count_printed === $not_toggled_terms_count) {
                $hide_next_term_li = true;
            }
            ?>
            <li class="woof_image_term_li_<?php echo esc_attr($term['term_id']) ?> <?php if ($hide_next_term_li): ?>woof_hidden_term<?php endif; ?>">
                <p class="woof_tooltip"><span class="woof_tooltip_data"><?php echo esc_html($term['name']) ?> <?php echo wp_kses_post($count_string) ?><?php if(!empty($term_desc)): ?><br /><i><?php echo esc_html($term_desc) ?></i><?php endif; ?></span>

                    <?php $image_id = 'woof_tax_image_' . $term['slug']; ?>

                    <input id="<?php echo esc_attr($image_id) ?>" type="checkbox" data-styles="<?php echo esc_html($styles) ?>" <?php checked(in_array($term['slug'], $current_request)) ?> id="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>" class="woof_image_term woof_image_term_<?php echo esc_attr($term['term_id']) ?> <?php if (in_array($term['slug'], $current_request)): ?>checked<?php endif; ?>" data-image="<?php echo esc_url($image) ?>" data-tax="<?php echo esc_html(woof()->check_slug($tax_slug)) ?>" name="<?php echo esc_html($term['slug']) ?>" value="<?php echo esc_html($term['term_id']) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" <?php checked(in_array($term['slug'], $current_request)) ?> /></p>
                <input type="hidden" value="<?php echo esc_html($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>_<?php echo esc_attr($term['slug']) ?>" />
                <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($image_id) ?>"><?php echo esc_html($term['name']); ?></label>
                <?php if (isset(woof()->settings['show_title'][$tax_slug]) AND woof()->settings['show_title'][$tax_slug]): ?>
                    <p class="woof_image_text_term">
                        <?php echo esc_html($term['name']) ?> <?php echo wp_kses_post(wp_unslash($count_string)) ?>
                    </p>
                <?php endif; ?>
            </li>
            <?php
            $terms_count_printed++;
        endforeach;
        ?>


        <?php
        if ($not_toggled_terms_count > 0 AND $terms_count_printed > $not_toggled_terms_count):
            ?>
            <li class="woof_open_hidden_li"><?php WOOF_HELPER::draw_more_less_button('image') ?></li>
        <?php endif; ?>


    <?php endif; ?>
</ul>
<div class="clear clearfix"></div>
<?php
//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
WOOF_REQUEST::del('additional_taxes');
