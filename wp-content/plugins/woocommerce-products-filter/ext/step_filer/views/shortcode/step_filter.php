<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

$classes = "";
if ($autosubmit) {
    $classes = "woof_autosubmit";
}
if ($filter_type) {
    $classes .= " woof_step_filter_" . $filter_type;
}

global $woof_link;
$woof_reset_btn_txt = get_option('woof_reset_btn_txt', '');
if (empty($woof_reset_btn_txt)) {
    $woof_reset_btn_txt = esc_html__('Reset', 'woocommerce-products-filter');
}
$woof_reset_btn_txt = WOOF_HELPER::wpml_translate(null, $woof_reset_btn_txt);
?>


<div class="woof_step <?php echo esc_attr($classes) ?>" data-filter_type="<?php echo esc_attr($filter_type) ?>"  data-autosubmit="<?php echo esc_attr($autosubmit) ?>" data-hide="<?php echo esc_attr($autosubmit) ?>">
    <?php
    echo do_shortcode(esc_attr($shortcode_woof));
    ?>
    <div class="woof_step_next_back_btn">
        <button  class="button woof_step_filter_prev"><?php esc_html_e($prev_btn_txt) ?></button>&nbsp;<?php if ($woof_reset_btn_txt != 'none'): ?><button  class="button woof_reset_search_form" data-link="<?php echo esc_url_raw($woof_link) ?>"><?php esc_html_e($woof_reset_btn_txt) ?></button><?php endif; ?>       
        <button class="button woof_step_filter_next"><?php esc_html_e($next_btn_txt) ?></button>
    </div>
    <?php if ($images): ?>
        <input class="woof_step_filter_images" type="hidden" value=<?php echo base64_encode(json_encode($images)) ?> data-selector="<?php echo esc_attr($selector) ?>" data-behavior="<?php echo esc_attr($img_behavior) ?>" >
    <?php endif; ?>
</div>

