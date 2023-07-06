<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

$class = $match ? "woof_notise_match" : "woof_notise_not_match";
?>
<div class="woof_notice_result <?php echo esc_attr($class) ?>">
    <span class="dashicons <?php echo esc_attr($match ? "dashicons-yes-alt" : "dashicons-warning") ?>"></span>
    <?php echo wp_kses_post(wp_unslash($notice)) ?>  
</div>
<?php
