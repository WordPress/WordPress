<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOBE;
?>
<h4><?php esc_html_e('Help', 'woocommerce-bulk-editor') ?></h4>

<div class="woobe_alert">

    <?php
    printf(esc_html__('The plugin has %s, %s, %s list. Also if you have troubles you can %s!', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                'href' => 'https://bulk-editor.com/documentation/',
                'title' => esc_html__('documentation', 'woocommerce-bulk-editor'),
                'target' => '_blank'
            )), WOOBE_HELPER::draw_link(array(
                'href' => 'https://bulk-editor.com/how-to-list/',
                'title' => esc_html__('FAQ', 'woocommerce-bulk-editor'),
                'target' => '_blank'
            )), WOOBE_HELPER::draw_link(array(
                'href' => 'https://bulk-editor.com/translations/',
                'title' => esc_html__('translations', 'woocommerce-bulk-editor'),
                'target' => '_blank'
            )), WOOBE_HELPER::draw_link(array(
                'href' => 'https://pluginus.net/support/forum/woobe-woocommerce-bulk-editor-professional/',
                'title' => '<b style="color: #2eca8b;">' . esc_html__('ask for support here', 'woocommerce-bulk-editor') . '</b>',
                'style' => 'text-decoration: none;',
                'target' => '_blank'
    )));
    ?>
</div>

<?php if ($WOOBE->show_notes) : ?>
    <div style="height: 9px;"></div>
    <div class="woobe_set_attention woobe_alert"><?php
        printf(esc_html__('Current version of the plugin is FREE. See the difference between FREE and PREMIUM versions %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                    'href' => 'https://bulk-editor.com/downloads/',
                    'title' => esc_html__('here', 'woocommerce-bulk-editor'),
                    'target' => '_blank'
        )));
        ?></div>
<?php endif; ?>
</b>


<h4><?php esc_html_e('Some little hints', 'woocommerce-bulk-editor') ?>:</h4>

<ul>
    <li><span class="icon-right"></span>&nbsp;<?php esc_html_e('If to click on an empty space of the black wp-admin bar, it will get you back to the top of the page', 'woocommerce-bulk-editor') ?></li>


    <li><span class="icon-right"></span>&nbsp;<?php
        printf(esc_html__('Can I %s?', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                    'href' => 'https://bulk-editor.com/howto/can-i-select-products-and-add-15-to-their-regular-price/',
                    'title' => esc_html__('select products and add 15% to their regular price', 'woocommerce-bulk-editor'),
                    'target' => '_blank'
        )))
        ?>
    </li>

    <li><span class="icon-right"></span>&nbsp;<?php
        printf(esc_html__('How to %s by bulk operation', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                    'href' => 'https://bulk-editor.com/howto/how-to-remove-sale-prices-by-bulk-operation/',
                    'title' => esc_html__('remove sale prices', 'woocommerce-bulk-editor'),
                    'target' => '_blank',
                    'style' => 'color: red;'
        )))
        ?>
    </li>

    <li><span class="icon-right"></span>&nbsp;<?php
        printf(esc_html__('If your shop is on the Russian language you should install %s for the correct working of BEAR with Cyrillic', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                    'href' => 'https://ru.wordpress.org/plugins/cyr2lat/',
                    'title' => esc_html__('this plugin', 'woocommerce-bulk-editor'),
                    'target' => '_blank'
        )))
        ?>
    </li>


    <li><span class="icon-right"></span>&nbsp;<?php
        printf(esc_html__('How to set the same value for some products on the same time - %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                    'href' => 'https://bulk-editor.com/howto/how-to-set-the-same-value-for-some-products-on-the-same-time/',
                    'title' => esc_html__('binded editing', 'woocommerce-bulk-editor'),
                    'target' => '_blank'
        )))
        ?>
    </li>

    <li>
        <span class="icon-right"></span>&nbsp;<?php esc_html_e('Remember! "Sale price" can not be greater than "Regular price", never! So if "Regular price" is 0 - not possible to set "Sale price"!', 'woocommerce-bulk-editor') ?>
    </li>

    <li>
        <span class="icon-right"></span>&nbsp;<?php esc_html_e('Search by products slugs, which are in non-latin symbols does not work because in the data base they are keeps in the encoded format!', 'woocommerce-bulk-editor') ?>
    </li>


    <li>
        <span class="icon-right"></span>&nbsp;<?php esc_html_e('Click ID of the product in the products table to see it on the site front.', 'woocommerce-bulk-editor') ?>
    </li>


    <li>
        <span class="icon-right"></span>&nbsp;<?php esc_html_e('Use Enter keyboard button in the Products Editor for moving to the next products row cell with saving of changes while edit textinputs. Use arrow Up or arrow Down keyboard buttons in the Products Editor for moving to the next/previous products row without saving of changes.', 'woocommerce-bulk-editor') ?>
    </li>

    <li>
        <span class="icon-right"></span>&nbsp;<?php esc_html_e('To select range of products using checkboxes - select first product, press SHIFT key on your PC keyboard and click last product.', 'woocommerce-bulk-editor') ?>
    </li>

    <li><span class="icon-right"></span>&nbsp;<?php
        printf(esc_html__('If you have any ideas, you can suggest them on %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                    'href' => 'https://pluginus.net/support/forum/woobe-woocommerce-bulk-editor-professional/',
                    'title' => esc_html__('the plugin forum', 'woocommerce-bulk-editor'),
                    'target' => '_blank'
        )))
        ?>
    </li>
</ul>


<hr />
<div class="woobe_alert">
    <?php
    printf(esc_html__('If you like BEAR %s about what you liked and what you want to see in future versions of the plugin', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link([
                'href' => $WOOBE->show_notes ? 'https://wordpress.org/support/plugin/woo-bulk-editor/reviews/?filter=5#new-post' : 'https://codecanyon.net/downloads#item-21779835',
                'target' => '_blank',
                'title' => esc_html__('write us feedback please', 'woocommerce-bulk-editor'),
                'class' => ''
    ]));
    ?>
</div>

<h4><?php esc_html_e('Requirements', 'woocommerce-bulk-editor') ?>:</h4>
<ul>
    <li><span class="icon-right"></span>&nbsp;<?php esc_html_e('Recommended min RAM', 'woocommerce-bulk-editor') ?>: 1024 MB</li>
    <li><span class="icon-right"></span>&nbsp;<?php esc_html_e('Minimal PHP version is', 'woocommerce-bulk-editor') ?>: PHP 7.2</li>
    <li><span class="icon-right"></span>&nbsp;<?php esc_html_e('Recommended PHP version', 'woocommerce-bulk-editor') ?>: 8.xx</li>
</ul><br />



<hr />
<h4><?php esc_html_e('Some useful plugins for your e-shop', 'woocommerce-bulk-editor') ?></h4>


<div class="col-lg-12">
    <a href="https://products-filter.com/" title="WOOF - WooCommerce Products Filter" target="_blank">
        <img width="200" src="<?php echo WOOBE_LINK ?>assets/images/woof_banner.png" alt="WOOF - WooCommerce Products Filter" />
    </a>

    <a href="https://currency-switcher.com/" title="WOOCS - WooCommerce Currency Switcher" target="_blank">
        <img width="200" src="<?php echo WOOBE_LINK ?>assets/images/woocs_banner.png" alt="WOOCS - WooCommerce Currency Switcher" />
    </a>

    <a href="https://products-tables.com/" title="WOOT - WooCommerce Active Products Tables" target="_blank">
        <img width="200" src="<?php echo WOOBE_LINK ?>assets/images/woot_banner.png" alt="WOOT - WooCommerce Active Products Tables" />
    </a>

</div>

<div class="clear"></div>


