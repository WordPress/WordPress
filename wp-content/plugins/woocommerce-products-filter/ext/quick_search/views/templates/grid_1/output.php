<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
$structure = WOOF_EXT_QUICK_TEXT::parse_template_structure($template_structure);
?>
<div class="woof_qs_result  woof_qs_list_<?php echo esc_attr($template_result) ?>   text_res_page_0" >
    <div class=" woof_qs_table_<?php echo esc_attr($template_result) ?>_header"><?php echo wp_kses_post(wp_unslash($header_text)) ?></div>
    <?php echo WOOF_EXT_QUICK_TEXT::show_sort_html_select() ?>
    __PAGINATION__
    <ul class="cards table-hover woof_qs_container">
        <li class="cards__item woof_qs_item">
            <div class="card">
                <a href="__URL__" target="__TARGET__">
                    <div class="card__image card__image--fence" style="background-image: url(__IMG__)"></div>
                </a>
                <div class="card__content">
                    <?php
                    foreach ($structure as $item):
                        if ($item['key'] == 'img') {
                            continue;
                        } elseif ($item['key'] == 'title') {
                            ?>
                            <div class="woof_qs_<?php echo esc_attr($template_result) ?>_<?php echo esc_attr($item['key']) ?> card__title" >
                                <a href="__URL__" target="__TARGET__">__TITLE__</a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <p class="woof_qs_<?php echo esc_attr($template_result) ?>_<?php echo esc_attr($item['key']) ?>">
                                <?php echo esc_attr(($item['key'] == "price") ? "" : $item['title'] . ":") ?><?php echo esc_html($item['alias']) ?>
                            </p>
                            <?php
                        }
                    endforeach;
                    ?>  
                </div>
            </div>
        </li>
        <li class="woof_qs_no_products_item">
            <div class='woof_qs_no_products woof_qt_no_products_<?php echo esc_attr($template_result) ?>'>
                <?php esc_html_e('Product not found', 'woocommerce-products-filter') ?>
            </div>
        </li>
    </ul>
    __PAGINATION__
</div>
