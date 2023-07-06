<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<img src="<?php echo esc_url($thumbnail) ?>" class="woof_husky_txt-option-thumbnail" alt="" />
<div>
    test 1<br>

    <?php
    $labels_string = '<div class="woof_husky_txt-labels">';
    if (!empty($labels)) {
        foreach ($labels as $label) {
            $labels_string .= "<div>{$label}</div>";
        }
    }
    $labels_string .= '</div>';
    ?>
    <?php echo wp_kses_post(wp_unslash($labels_string)) ?>
    <div class="woof_husky_txt-option-title"><a href="<?php echo esc_url($permalink) ?>" target="<?php echo esc_attr($options['click_target']) ?>"><?php esc_html_e($title) ?></a></div>
    <div class="woof_husky_txt-option-text"><?php echo wp_kses_post(wp_unslash($excerpt))?></div>        
</div>

