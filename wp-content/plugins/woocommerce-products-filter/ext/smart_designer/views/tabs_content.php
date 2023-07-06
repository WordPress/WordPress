<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-sd">

    <div class="woof-tabs woof-tabs-style-line">

        <div class="content-wrap">

            <section>


                <div class="woof-section-title">
                    <div class="col-title">

                        <h4 id="woof-sd-title"><?php esc_html_e('Smart Designer', 'woocommerce-products-filter') ?> <b class="woof__text-success">v.<?php esc_html_e($ext_version) ?></b><span></span></h4>
                        <p class="description"><?php esc_html_e('HUSKY Filter Elements Constructor', 'woocommerce-products-filter') ?></p>
                    </div>
                    <div class="col-button">
                        <a href="https://products-filter.com/smart-designer" target="_blank" class="button-primary"><span class="icon-info"></span></a><br />
                    </div>
                </div>

                <div class="woof-notice">
                    <?php printf(esc_html__('Notice: This extension works for now only with taxonomies and its functionality will be extended with new element types, and also with meta data elements. HUSKY SD has %s. If you have an idea about HTML element(s) you want to see in HUSKY filter elements constructor discuss please your suggestion using %s.', 'woocommerce-products-filter'), '<a href="https://demo-sd.products-filter.com/woof-sd-gallery/" target="_blank">' . esc_html__('gallery of custom-element types', 'woocommerce-products-filter') . '</a>', '<a href="https://pluginus.net/support/forum/woof-woocommerce-products-filter/" target="_blank">' . esc_html__('HUSKY support forum', 'woocommerce-products-filter') . '</a>') ?>
                </div>


                <div class="woof-control-section">

                    <?php if (isset($error)): ?>
                        <div class="woof__alert woof__alert-info2"><?php echo esc_html($error) ?></div>
                        <div class="woof__alert woof__alert-info2"><?php echo esc_html($last_error) ?> -> <code><?php echo esc_html($sql) ?></code></div>
                    <?php endif; ?>

                    <div id="sd-panel"></div>

                    <div class="sd" data-scene="0">
                        <div id="sd-scene"><div><?php esc_html_e('Loading', 'woocommerce-products-filter') ?> ...</div></div>
                        <div id="sd-visor"></div>
                    </div>


                </div><!--/ .woof-control-section-->

                <?php if (woof()->show_notes): ?>
                    <div class="woof__alert woof__alert-info2 woof_tomato">
                        <?php printf(esc_html__('In the free version of HUSKY you can operate with 1 element! If you want to create more elements you can make upgrade to the %s.', 'woocommerce-products-filter'), '<a href="https://codecanyon.pluginus.net/item/woof-woocommerce-products-filter/11498469" target="_blank">' . esc_html__('premium version of the plugin', 'woocommerce-products-filter') . '</a>') ?>
                    </div>
                <?php endif; ?>


            </section>

        </div>

    </div>
</section>

