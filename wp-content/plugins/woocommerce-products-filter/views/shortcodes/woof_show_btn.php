<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

$btn_url = '';

if (isset($img_url) && $img_url) {
    $btn_url = $img_url;
}
$style = '';

if ($btn_url != 'none' && $btn_url) {
    $style = "background-image: url('" . sanitize_file_name($btn_url) . "');";
} elseif ($btn_url == 'none') {
    $style = "background-image: none ;";
}


//***
$woof_auto_hide_button_txt = '';
if (isset(woof()->settings['woof_auto_hide_button_txt'])) {
    $woof_auto_hide_button_txt = WOOF_HELPER::wpml_translate(null, woof()->settings['woof_auto_hide_button_txt']);
}
?>
<a href="javascript:void(0);" <?php if ($style): ?> style="<?php echo esc_attr($style) ?>"<?php endif; ?> class="woof_show_auto_form woof_btn <?php if ($btn_url == 'none') echo esc_attr('woof_show_auto_form_txt'); ?>">
    <?php esc_html_e($woof_auto_hide_button_txt) ?>
</a>

