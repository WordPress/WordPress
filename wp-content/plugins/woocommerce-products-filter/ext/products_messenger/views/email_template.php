<?php
$unsobscr_link = "";
$link = explode("?", $subscr['link']);
$unsobscr_link = $link[0] . "?id_user=" . $subscr['user_id'] . "&key=" . $subscr['key'] . "&woof_skey=" . $subscr['secret_key'];
if (count($link) == 1) {
    $subscr['link'] .= "?swoof=1";
}
$search_terms = $subscr['get'];
$products = new WP_Query(
        array('post_type' => 'product', 'post__in' => $products, 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => -1)
);
$product_count = count($products->posts);
$text_var = array($user->display_name, $user->user_nicename, $product_count);
$text_str = array('[DISPLAY_NAME]', '[USER_NICENAME]', '[PRODUCT_COUNT]');
$text_email = str_replace($text_str, $text_var, $text_email);
?>
<style>
    /******************* EMAIL TEMPLATE *********************************/
    .woof_mail{
        padding-left: 0px !important;
        list-style: none;
    }
    ul.woof_mail li{
        margin-left: 0px;
        width: 32%;
        display: inline-block;
        position: relative;
    }
    ul.woof_mail li a:nth-child(2){
        display: none;
    }
    ul.woof_mail li a h3{
        height: 80px;
    }
    ul.woof_mail li .onsale{
        display: none;
    }
    .woof_more_text p a:hover{
        background: blue;
    }
    .woof_terms{
        margin-left: 5px;
        padding: 3px;
        background: #E6E6E6;
    }
    .woof_author_name{
        margin-left: 5px;
        padding: 3px;
        background: #E6E6E6;
    }
</style>

<div class="woof_text_email" style="color: #4a4a4e; margin-bottom: 30px;" ><?php echo wp_kses_post(wp_unslash($text_email)) ?></div>
<div class="woof_search_terms"><p>
        <?php esc_html_e('Terms: ', 'woocommerce-products-filter') ?>
        <?php echo wp_kses_post(wp_unslash($search_terms)) ?>
    </p></div>
<?php if ($last_email) { ?>
    <div class="last_email"><?php esc_html_e('Attention! This is the last email. If you want to continue get such emails -> Go by next link and subscribe again', 'woocommerce-products-filter') ?>
        - <a href="<?php echo esc_attr($subscr['link']) . "&orderby=date&order=DESC" ?>"><?php esc_html_e('Subscribe ', 'woocommerce-products-filter'); ?></a>
    </div>
<?php } ?>
<div class="woof_subscr"><p><?php esc_html_e('If you want to Unsubscribe from this newsletter', 'woocommerce-products-filter') ?> - <a href="<?php echo esc_url_raw($unsobscr_link) ?>"><?php esc_html_e('unsubscribe', 'woocommerce-products-filter') ?></a> </p></div>
<ul class="products woof_mail" >
    <?php
    if ($products->have_posts()) {
        $i = 0;
        while ($products->have_posts()) : $products->the_post();
            wc_get_template_part('content', 'product');
            if (++$i >= 9) {
                break;
            }
        endwhile;

        if ($i < count($products->posts)) {
            ?>
            <div style="margin-top: 20px" class="woof_more_text" >
                <p style="text-align: center;font-size: 18px;" ><a style="padding: 4px;border: 2px solid #557DA1; text-decoration: none;" href="<?php echo esc_url_raw($subscr['link'] . "&orderby=date&order=DESC") ?>"><?php esc_html_e('See more new products...', 'woocommerce-products-filter') ?></a></p>
            </div> 
            <?php
        }
    } else {
        esc_html_e('No products found', 'woocommerce-products-filter');
    }
    wp_reset_postdata();
    ?>
</ul><!--/.products-->
<div class="woof_subscr"><p><?php esc_html_e('If you want to Unsubscribe from this newsletter', 'woocommerce-products-filter') ?> - <a href="<?php echo esc_url_raw($unsobscr_link) ?>"><?php esc_html_e('unsubscribe', 'woocommerce-products-filter') ?></a> </p></div>
