<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-quick-text">
    <div class="woof-tabs woof-tabs-style-line">

<?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>


                <div class="woof-section-title">
                    <div class="col-title">

                        <h4><?php esc_html_e('Quick search', 'woocommerce-products-filter') ?></h4>

                    </div>
                    <div class="col-button">
                        <a href="https://products-filter.com/extencion/quick-search/" target="_blank" class="button-primary"><span class="icon-info"></span></a><br />

                    </div>
                </div>


                <div class="woof-control-section" style="display:none;"  >

                    <h4><?php esc_html_e('How often assemble data file', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

<?php
$cron_systems = array(
    0 => esc_html__('WordPress Cron', 'woocommerce-products-filter'),
        //1 => esc_html__('External Cron', 'woocommerce-products-filter')
);

if (!isset($woof_settings['woof_quick_search']['cron_system'])) {
    $woof_settings['woof_quick_search']['cron_system'] = 0;
}
$cron_system = $woof_settings['woof_quick_search']['cron_system'];
$cron_system = -1; //hide  cron sys
?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_quick_search][cron_system]" class="chosen_select woof_cron_system">
<?php foreach ($cron_systems as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php selected($cron_system == $key) ?>><?php esc_html_e($value); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description">
<?php esc_html_e('Products assembling period in data file for quick search.', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_external_cron_option" style="display: <?php echo esc_attr($cron_system == 1 ? 'block' : 'none') ?>;">

                    <h4><?php esc_html_e('Secret key for external cron', 'woocommerce-products-filter') ?></h4>
<?php
if (!isset($woof_settings['woof_quick_search']['cron_secret_key']) OR empty($woof_settings['woof_quick_search']['cron_secret_key'])) {
    $woof_settings['woof_quick_search']['cron_secret_key'] = 'woof_stat_updating';
}
$cron_secret_key = sanitize_title($woof_settings['woof_quick_search']['cron_secret_key']);
?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_quick_search][cron_secret_key]" value="<?php echo esc_html($cron_secret_key) ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('Enter any random text in the field and use it in the external cron with link like: http://mysite.com/?woof_stat_collection=__YOUR_SECRET_KEY_HERE__', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_wp_cron_option" style="display: <?php echo esc_attr($cron_system == 0 ? 'block' : 'none') ?>;">

                    <h4><?php esc_html_e('WordPress Cron period', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

<?php
$wp_cron_periods = array(
    'daily' => esc_html__('daily', 'woocommerce-products-filter'),
    'week' => esc_html__('weekly', 'woocommerce-products-filter'),
    'twicemonthly' => esc_html__('twicemonthly', 'woocommerce-products-filter'),
    'month' => esc_html__('monthly', 'woocommerce-products-filter'),
);

if (!isset($woof_settings['woof_quick_search']['wp_cron_period'])) {
    $woof_settings['woof_quick_search']['wp_cron_period'] = 'weekly';
}
$wp_cron_period = $woof_settings['woof_quick_search']['wp_cron_period'];
?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_quick_search][wp_cron_period]" class="chosen_select">
<?php foreach ($wp_cron_periods as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($wp_cron_period == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
<?php esc_html_e('Weekly recommended', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_update_search_data_file">

                    <h4><?php esc_html_e('Reassemble data file', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">
                        <input type="hidden" id="woof_qs_update_nonce"  value="<?php echo wp_create_nonce('woof-qs-nonce'); ?>">
                        <div class="woof-control">
                            <a id="woof_quick_search_update" class="button" ><span class="icon-up"></span><?php esc_html_e('Update now!', 'woocommerce-products-filter') ?></a><span class="woof_qt_messange"></span><span class="woof_qt_product_count"></span>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('Reassemble data file for quick search manually now', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_file_data_option" >

<?php
if (!isset($woof_settings['woof_quick_search']['quick_search_tax_conditionals']) OR empty($woof_settings['woof_quick_search']['cron_secret_key'])) {
    $woof_settings['woof_quick_search']['quick_search_tax_conditionals'] = '';
}
$tax_conditionals = $woof_settings['woof_quick_search']['quick_search_tax_conditionals'];
?>
                    <div class="woof-control-container">

                        <div class="woof-control" style="display: none;">
                            <h5><?php esc_html_e('Taxonomy conditionals', 'woocommerce-products-filter') ?></h5>
                            <input type="text" name="woof_settings[woof_quick_search][quick_search_tax_conditionals]" value="<?php echo esc_html($tax_conditionals) ?>" />
                        </div>

                        <div class="woof-control">

<?php
$taxonomies = $this->get_taxonomies();
if (!empty($taxonomies)) {
    foreach ($taxonomies as $slug => $t) {
        $all_items[urldecode($slug)] = $t->labels->name;
    }
}

asort($all_items);
//***

if (!isset($woof_settings['woof_quick_search']['items_for_text_search']) OR empty($woof_settings['woof_quick_search']['items_for_text_search'])) {
    $woof_settings['woof_quick_search']['items_for_text_search'] = array();
}
$items_for_stat = (array) $woof_settings['woof_quick_search']['items_for_text_search'];
?>

                            <h5><?php esc_html_e('Additional search data for text search', 'woocommerce-products-filter') ?></h5>
                            <div class="select-wrap">

                                <select multiple="" name="woof_settings[woof_quick_search][items_for_text_search][]" class="chosen_select">
<?php foreach ($all_items as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php selected(in_array($key, $items_for_stat)) ?>><?php esc_html_e($value); ?></option>
                                    <?php endforeach; ?>
                                </select><br />

                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('Additional search data: which taxonomies terms titles should be included into TEXT search data file. So, your customers will be able to find with TEXT search input also products using your taxonomies terms titles! After selecting new taxonomies here do not forget press Save and then Reassemble data file!', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

            </section>

        </div>

    </div>
</section>


