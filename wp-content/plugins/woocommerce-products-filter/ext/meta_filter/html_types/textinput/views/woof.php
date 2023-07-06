<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div data-css-class="woof_textinput_container" class="woof_textinput_container woof_container  woof_container_<?php echo esc_attr("textinput_".$meta_key) ?>">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <?php
        
        $woof_text = '';
        $request = woof()->get_request_data();

        if (isset($request['textinput_'.$meta_key]))
        {
            $woof_text = $request['textinput_'.$meta_key];
        }
        //+++
        if (!isset($placeholder))
        {
            $p = esc_html__('enter a text here ...', 'woocommerce-products-filter');
        }

        if (isset($options['title']) AND ! isset($placeholder))
        {
            if (!empty($options['title']))
            {
                $p = $options['title'];
                $p = WOOF_HELPER::wpml_translate(null, $p);
                $p = esc_html__($p, 'woocommerce-products-filter');
            }

        }
        //***
        $unique_id = uniqid('woof_meta_filter_');
        ?>

        <div class="woof_show_textinput_container ">
            <img width="36" class="woof_show_text_search_loader" style="display: none;" src="<?php echo esc_url($loader_img) ?>" alt="loader" />
            <a href="javascript:void(0);" data-uid="<?php echo esc_attr($unique_id) ?>" class="woof_textinput_go <?php echo esc_attr($unique_id) ?>"></a>
            <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($unique_id)  ?>"><?php esc_html_e(isset($placeholder) ? $placeholder : $p); ?></label>
			<input type="search" class="woof_meta_filter_textinput <?php echo esc_attr($unique_id) ?>" id="<?php echo esc_attr($unique_id) ?>" data-uid="<?php echo esc_attr($unique_id) ?>" data-auto_res_count="<?php echo intval(isset($auto_res_count) ? $auto_res_count : 0) ?>" data-auto_search_by="<?php echo esc_html(isset($auto_search_by) ? $auto_search_by : "") ?>" placeholder="<?php esc_html_e(isset($placeholder) ? $placeholder : $p) ?>" name="textinput_<?php echo esc_attr($meta_key) ?>" value="<?php echo esc_attr($woof_text) ?>" />
        </div>

    </div>
</div>
