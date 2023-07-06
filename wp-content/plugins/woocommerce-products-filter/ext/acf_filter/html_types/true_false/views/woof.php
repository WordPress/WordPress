<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');


$woof_meta_title = WOOF_HELPER::wpml_translate(null, $meta_title);
//***
if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) !== -1) {
    if (intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) === 1) {
        WOOF_REQUEST::set('hide_terms_count_txt', 1);
    } else {
        WOOF_REQUEST::set('hide_terms_count_txt', 0);
    }
}
//***
if (isset(woof()->settings[$meta_key]) AND woof()->settings[$meta_key]['show']) {
    $count_string = "";
    $count = 0;
    $show_count = get_option('woof_show_count', 0);
    $show_count_dynamic = get_option('woof_show_count_dynamic', 0);
    $hide_dynamic_empty_pos = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos', 0);
    $show = true;
    $disable = "";
    $additional_tax = (WOOF_REQUEST::isset('additional_taxes')) ? WOOF_REQUEST::get('additional_taxes') : "";
    if (!woof()->is_isset_in_request_data($meta_key)) {
        if ($show_count) {
            $value = 1;
            $type = 'checkbox';

            $meta_field = array(
                'key' => $meta_key,
                'value' => $value,
            );
            if ($show_count_dynamic) {
                $count_data = array();
                $count = woof()->dynamic_count(array(), $type, $additional_tax, $meta_field);
                $count_string = '(' . $count . ')';
                if ($count == 0) {
                    $disable = "disabled=''";
                }
            } else {
                // $count = $term['count'];
            }
        }
        //+++
        if ($hide_dynamic_empty_pos AND $count == 0) {
            $show = false;
        }
    }

    if (WOOF_REQUEST::get('hide_terms_count_txt')) {
        $count_string = "";
    }
    ?>
    <?php if ($show): ?>
        <div data-css-class="woof_acf_checkbox_container" class="woof_acf_checkbox_container woof_container woof_container_<?php echo esc_attr( $meta_key) ?>">
            <div class="woof_container_overlay_item"></div>
            <div class="woof_container_inner">
                <input type="checkbox" class="woof_acf_checkbox" <?php echo esc_html($disable); ?> id="woof_acf_checkbox_<?php echo esc_attr($meta_key) ?>" <?php ?>  name="<?php echo esc_attr($meta_key) ?>" value="0" <?php checked(1, woof()->is_isset_in_request_data($meta_key) ? 1 : '', true) ?> />&nbsp;&nbsp;
				<label for="woof_meta_checkbox_<?php echo esc_attr($meta_key) ?>"><?php echo esc_html($woof_meta_title) ?><?php echo wp_kses_post(wp_unslash($count_string)); ?></label><br />
				<input type="hidden" value="<?php esc_html_e('Yes', 'woocommerce-products-filter') ?>" data-anchor="woof_n_<?php echo esc_attr( $meta_key) ?>_1" />
			</div>
        </div>
    <?php endif; ?>
    <?php
}
