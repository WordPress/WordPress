<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-url-request">
    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>

                <div class="woof-section-title">
                    <div class="col-title">

                        <h4><?php esc_html_e('SEO URL request', 'woocommerce-products-filter') ?></h4>

                    </div>
                    <div class="col-button">
                        <a href="https://products-filter.com/extencion/seo-url-request/" target="_blank" class="button-primary"><span class="icon-info"></span></a><br>
                    </div>
                </div>

                <?php if (woof()->show_notes): ?>
                    <div class="woof__alert woof__alert-info2 woof_tomato">
                        <?php esc_html_e('In FREE version it is possible to operate with 2 rules only!', 'woocommerce-products-filter') ?>
                    </div>
                <?php endif; ?>


                <div class="woof-control-section">

                    <h4><?php esc_html_e('Enable/Disable', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $enable_url = array(
                                0 => esc_html__('No', 'woocommerce-products-filter'),
                                1 => esc_html__('Yes', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_url_request']['enable'])) {
                                $woof_settings['woof_url_request']['enable'] = 0;
                            }
                            $enable = $woof_settings['woof_url_request']['enable'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_url_request][enable]" class="chosen_select">
                                    <?php foreach ($enable_url as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($enable == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('This option changes the search link. The search query becomes part of the URL.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h4><?php esc_html_e('Disable page indexing', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $page_index = array(
                                0 => esc_html__('No', 'woocommerce-products-filter'),
                                1 => esc_html__('Yes', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_url_request']['page_index'])) {
                                $woof_settings['woof_url_request']['page_index'] = 1;
                            }
                            $index = $woof_settings['woof_url_request']['page_index'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_url_request][page_index]" class="chosen_select">
                                    <?php foreach ($page_index as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($index == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('Disables page indexing when a seo search query exists.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
                <div class="woof-control-section">

                    <h4><?php esc_html_e('URL depth', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $index_deeps = array(
                                1 => 1,
                                2 => esc_html__('2 (recomended)', 'woocommerce-products-filter'),
                                3 => 3,
                                4 => 4,
                                5 => 5,
                                6 => 6,
                                7 => 7,
                                8 => 8,
                                9 => 9,
                                10 => 10
                            );

                            if (!isset($woof_settings['woof_url_request']['index_deep'])) {
                                $woof_settings['woof_url_request']['index_deep'] = 2;
                            }
                            $index_deep = $woof_settings['woof_url_request']['index_deep'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_url_request][index_deep]" class="chosen_select">
                                    <?php foreach ($index_deeps as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($index_deep == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('What is the maximum depth of the URL (how many filters can be in a request)', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->				
                <div class="woof-control-section">

                    <h4><?php esc_html_e('Rules', 'woocommerce-products-filter') ?>:</h4>

                    <div class="woof-control-container woof-control-container-add-seo-rule">
                        <div class="woof-control">
                            <input type='text' class='woof_seo_rule_url_add' placeholder="<?php esc_html_e('Create your products page URL here', 'woocommerce-products-filter') ?>" value="">
                            <input type="button" class="woof_add_seo_rule woof-button" style="margin: 0;" value="<?php esc_html_e('Add SEO rule', 'woocommerce-products-filter') ?>">
                        </div>						
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('You can insert key {any} into the link. An example: /color-{any}/. For fields title and description - you can insert names of the terms that should be in the search query. For example insert the name of the current color: {pa_color}. To show taxonomy title use literal key {pa_color_title}. Example: Current season clothes of {pa_color_title} {pa_color}. Rule like "{pa_color_title} {pa_color}" will generate in the text: "Color red". Additional values: {site_name},{current_tax_name}. Such rules can be set for each language with WPML plugin automatically,  for another plugins you can use hook woof_seo_rules_langs (read this extension documentation)', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                    <div class="woof-control-container woof-control-container-seo">

                        <div class="woof-control-seo">
                            <div class="woof_seo_rules_list_container">
                                <?php
                                $langs = $seo_rule->get_all_langs();
                                $add_class = 'woof_hide_options';
                                if (count($langs) > 1) {
                                    $add_class = '';
                                }
                                ?>

                                <div>
                                    <select class='woof_seo_current_lang <?php echo esc_attr($add_class) ?>'>
                                        <?php
                                        foreach ($langs as $lang) {
                                            ?>
                                            <option values='<?php echo esc_attr($lang) ?>'><?php esc_html_e($lang) ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                </div>

                                <ul id='woof_seo_rules_list' >
                                    <?php
                                    $seo_rules = array();
                                    foreach ($langs as $lang) {
                                        $seo_rules = array();
                                        if (isset($woof_settings['woof_url_request']['seo_rules'][$lang]) && is_array($woof_settings['woof_url_request']['seo_rules'][$lang])) {
                                            $seo_rules = $woof_settings['woof_url_request']['seo_rules'][$lang];
                                        }

                                        if (intval(WOOF_VERSION) === 1) {
                                            $seo_rules = array_slice($seo_rules, 0, 2);
                                        }

                                        foreach ($seo_rules as $key => $data) {
                                            $seo_rule->woof_draw_seo_rules_item($key, $lang, $data['url'], $data['title'], $data['description'], $data['h1'], (isset($data['text']) ? $data['text'] : ''));
                                        }
                                    }
                                    ?>
                                </ul>

                            </div>
                        </div>

                    </div>
                    <?php if (defined('WPSEO_VERSION')) { ?>

                        <hr>

                        <div class="woof-control-section">

                            <h5><?php esc_html_e('YOAST: add HUSKY SEO links to sitemap', 'woocommerce-products-filter') ?></h5>
                            <br>
                            <div class="woof-control-container">
                                <div class="woof-control">

                                    <?php
                                    if (!isset($woof_settings['woof_url_request']['yoast_sitemap'])) {
                                        $woof_settings['woof_url_request']['yoast_sitemap'] = '';
                                    }
                                    $yoast_sitemap = $woof_settings['woof_url_request']['yoast_sitemap'];
                                    ?>

                                    <textarea name="woof_settings[woof_url_request][yoast_sitemap]" rows="10" ><?php echo esc_textarea($yoast_sitemap) ?></textarea>

                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Each new link must be on a new line', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                        </div><!--/ .woof-control-section-->

                    <?php } ?>
                </div><!--/ .woof-control-section-->				
            </section>

        </div>

    </div>
</section>
