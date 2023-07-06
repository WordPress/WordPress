<?php
if (!defined('ABSPATH'))
{
    exit;
}
?>
<div id="message" class="updated woocommerce-message">
    <p>
        <strong class="woof_red">IMPORTANT</strong>: If you migrated from from v.2.1.2/1.1.2 and lower to 2.1.3/1.1.3 and higher read migration article please!
    </p>
    <p class="submit"><a class="button-primary" href="https://products-filter.com/migration-v-2-1-2-or-1-1-2-and-lower-to-2-1-3-or-1-1-3-and-higher/" target="_blank">Migration article</a> <a class="button-secondary skip" href="<?php echo wp_nonce_url(add_query_arg('woof_hide_notice', 'woof_notice_update_213')); ?>">Hide This Notice</a></p>
</div>

