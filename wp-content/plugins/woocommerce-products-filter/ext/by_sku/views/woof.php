<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');


if (isset(woof()->settings['by_sku']) AND woof()->settings['by_sku']['show'])
{
    if (isset(woof()->settings['by_sku']['title']) AND ! empty(woof()->settings['by_sku']['title']))
    {
        ?>
        <!-- <<?php echo esc_attr(apply_filters('woof_title_tag', 'h4')); ?>><?php esc_html_e(woof()->settings['by_sku']['title']); ?></<?php echo esc_attr(apply_filters('woof_title_tag', 'h4')); ?>> -->
        <?php
    }
    echo do_shortcode('[woof_sku_filter]');
}

