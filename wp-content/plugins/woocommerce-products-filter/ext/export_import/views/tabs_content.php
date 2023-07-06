<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-export_import">
    <div class="woof-tabs woof-tabs-style-line">
        <div class="woof-control-section">

            <h5><?php esc_html_e('Import data', 'woocommerce-products-filter') ?></h5>

            <div class="woof-control-container">
                <div class="woof-control">
                    <textarea id="woof_import_settings" ></textarea><br />
					<input id="woof_import_settings_nc" type="hidden" value="<?php echo wp_create_nonce('woof_import_settings');?>">
                    <input type="button" id="woof_do_import" class="woof-button" value="<?php esc_html_e('Import placed data', 'woocommerce-products-filter') ?>">
                </div>
                <div class="woof-description">
                    <p class="description"><?php esc_html_e('WARNING: this action will overwrite all current HUSKY settings. Insert HUSKY settings data you need to import! Here you can import only HUSKY settings and nothing more! If your site don`t have taxonomies HUSKY will not create them (you should create taxonomies before).', 'woocommerce-products-filter') ?> </p>
                </div>
            </div>

        </div><!--/ .woof-control-section-->		
        <div class="woof-control-section">

            <h5><?php esc_html_e('Export data', 'woocommerce-products-filter') ?></h5>

            <div class="woof-control-container">
                <div class="woof-control">
                    <textarea readonly="readonly" id="woof_export_settings" ></textarea><br />
                    <input id="woof_export_settings_nc" type="hidden" value="<?php echo wp_create_nonce('woof_export_settings');?>">
					<input type="button" id="woof_get_export" class="woof-button" value="<?php esc_html_e('Generate data for export', 'woocommerce-products-filter') ?>">
                </div>
                <div class="woof-description">
                    <p class="description"><?php esc_html_e('Here is generated HUSKY settings data for export. You can only export HUSKY settings and not taxonomies or other site data. For taxonomies, products and other  use special third-party plugins.', 'woocommerce-products-filter') ?> </p>
                </div>
            </div>

        </div><!--/ .woof-control-section-->			

    </div>
</section>

