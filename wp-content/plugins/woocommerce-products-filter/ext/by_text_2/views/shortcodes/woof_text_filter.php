<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div data-css-class="woof_text_search_container" class="woof_text_search_container woof_container woof_container_woof_text">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <?php
        
        $woof_text = '';
        $request = woof()->get_request_data();

        if (isset($request['woof_text'])) {
            $woof_text = stripslashes($request['woof_text']);
        }
        //+++
        if (!isset($placeholder)) {
            $p = esc_html__('enter a product title here ...', 'woocommerce-products-filter');
        }

        if (isset(woof()->settings['by_text_2']['placeholder']) AND!isset($placeholder)) {
            if (!empty(woof()->settings['by_text_2']['placeholder'])) {
                $p = woof()->settings['by_text_2']['placeholder'];
                $p = WOOF_HELPER::wpml_translate(null, $p);
                $p = esc_html__($p, 'woocommerce-products-filter');
            }


            if (woof()->settings['by_text_2']['placeholder'] == 'none') {
                $p = '';
            }
        }
        //***
        $unique_id = uniqid('woof_text_search_');
        ?>

        <div class="woof_show_text_search_container">
            <img width="36" class="woof_show_text_search_loader" style="display: none;" src="<?php echo esc_url($loader_img) ?>" alt="loader" />
            <a href="javascript:void(0);" data-uid="<?php echo esc_attr($unique_id) ?>" class="woof_text_search_go <?php echo esc_attr($unique_id) ?>"></a>
            <input type="search" class="woof_show_text_search <?php echo esc_attr($unique_id) ?>" id="<?php echo esc_attr($unique_id) ?>" data-uid="<?php echo esc_attr($unique_id) ?>" data-auto_res_count="<?php echo intval((isset($auto_res_count) ? $auto_res_count : 0)) ?>" data-auto_search_by="<?php echo esc_html(isset($auto_search_by) ? $auto_search_by : "") ?>" placeholder="<?php echo esc_html(isset($placeholder) ? $placeholder : $p) ?>" name="woof_text" value="<?php echo esc_html($woof_text) ?>" />

            <?php if (isset(woof()->settings['by_text_2']['notes_for_customer']) AND!empty(woof()->settings['by_text_2']['notes_for_customer'])): ?>
                <span class="woof_text_notes_for_customer"><?php echo stripcslashes(wp_kses_post(wp_unslash(woof()->settings['by_text_2']['notes_for_customer']))); ?></span>
            <?php endif; ?>        
        </div>


    </div>
</div>