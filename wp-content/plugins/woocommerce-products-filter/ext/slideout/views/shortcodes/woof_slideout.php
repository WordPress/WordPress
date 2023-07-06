<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<div class="woof-slide-out-div <?php echo esc_attr($class) ?>" style="position: absolute; right: 10000px;" data-key="<?php echo esc_attr($key) ?>"  data-image="<?php echo esc_attr($image) ?>"
     data-image_h="<?php echo esc_attr($image_h) ?>" data-image_w="<?php echo esc_attr($image_w) ?>"
     data-mobile="<?php echo esc_attr($mobile_behavior) ?>"  data-action="<?php echo esc_attr($action) ?>" data-location="<?php echo esc_attr($location) ?>"
     data-speed="<?php echo esc_attr($speed) ?>" data-toppos="<?php echo esc_attr($offset) ?>"  data-onloadslideout="<?php echo esc_attr($onloadslideout) ?>"
     data-height="<?php echo esc_attr($height) ?>" data-width="<?php echo esc_attr($width) ?>">
    <span class="woof-handle <?php echo esc_attr($key) ?>" style="" ><?php
        if ($image == "null") {
            esc_html_e($text);
        }
        ?></span>
    <div class="woof-slide-content woof-slide-<?php echo esc_attr($key) ?>">
        <?php echo do_shortcode(esc_attr($content)) ?>
    </div>
</div>

