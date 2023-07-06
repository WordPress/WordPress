<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
$structure = WOOF_EXT_QUICK_TEXT::parse_template_structure($template_structure);
?>
<div class="woof_qs_result  woof_qs_list_<?php echo esc_attr($template_result) ?>   text_res_page_0">
    <div class=" woof_qs_table_<?php echo esc_attr($template_result) ?>_header"><?php echo wp_kses_post(wp_unslash($header_text)) ?></div>
    <?php echo WOOF_EXT_QUICK_TEXT::show_sort_html_select() ?>
    __PAGINATION__
    <div class="qs_cards woof_qs_container">
        <div class="qs_card woof_qs_item">
            <img __SRC__  alt="__TITLE__"> 
            <div class="card-title">
                <a href="#" class="qs_toggle-info qs_btn"><span class="left"></span><span class="right"></span></a>
                <h2 class="woof_qs_<?php echo esc_attr($template_result) ?>_title card__title" ><a class="woof_qs_link" href="__URL__" target="__TARGET__">__TITLE__</a></h2>   
                <?php if (isset($structure['price'])): ?>
                    <div class="woof_qs_<?php echo esc_attr($template_result) ?>_price" >__PRICE__</div>
                <?php endif; ?>
            </div>
            <div class="card-flap flap1">
                <div class="card-description">
                    <?php if (isset($structure['sku'])): ?>
                        <p class="card__text woof_qs_<?php echo esc_attr($template_result) ?>_sku" ><?php esc_html_e($structure['sku']['title']) ?>:__SKU__</p>
                    <?php endif; ?>
                    <?php if (isset($structure['key_words'])): ?>
                        <p class="card__text woof_qs_<?php echo esc_attr($template_result) ?>_key_words" ><?php esc_html_e($structure['key_words']['title']) ?>:__KEY_WORDS__</p>
                    <?php endif; ?>
                    <div class="card-flap flap2"> 
                        <div class="card-actions">
                            <a href="__URL__" target="__TARGET__"  class="woof_qs_link_btn"><?php esc_html_e('View product', 'woocommerce-products-filter') ?></a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="woof_qs_no_products_item">
            <div class='woof_qs_no_products woof_qs_no_products_<?php echo esc_attr($template_result) ?>'>
                <?php esc_html_e('Product not found', 'woocommerce-products-filter') ?>
            </div>
        </div>           
    </div>
    __PAGINATION__
</div>



