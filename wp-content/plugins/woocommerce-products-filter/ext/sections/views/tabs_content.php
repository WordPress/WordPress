<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');


$woof_settings = woof()->settings;
?>

<section id="tabs-sections">

    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>
                
                
                <div class="woof-section-title">
                    <div class="col-title">

                        <h4><?php esc_html_e('Sections', 'woocommerce-products-filter') ?></h4>

                    </div>
                    <div class="col-button">
                        <a href="https://products-filter.com/extencion/sections/" target="_blank" class="button-primary"><span class="icon-info"></span></a><br />


                    </div>
                </div>


                <p class="woof__alert woof__alert-info"><?php esc_html_e("Allows to wrap filter-elements into [close/open]-sections and make filter form more compact.", 'woocommerce-products-filter') ?></p>


                <div class="woof-control-section">

                    <h5><?php esc_html_e("Init sections", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $init_sections = array(
                                0 => esc_html__("No", 'woocommerce-products-filter'),
                                1 => esc_html__("Yes", 'woocommerce-products-filter'),
                            );
                            ?>
                            <?php
                            if (!isset($woof_settings['woof_init_sections']) OR empty($woof_settings['woof_init_sections'])) {
                                $woof_settings['woof_init_sections'] = 0;
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_init_sections]" class="chosen_select slideout_value" data-name="woof_init_sections">
                                    <?php foreach ($init_sections as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['woof_init_sections'] == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e("Init sections by default", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h5><?php esc_html_e("Sections behavior", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $sections_behavior = array(
                                'tabs_checkbox' => esc_html__("As checkbox", 'woocommerce-products-filter'),
                                'tabs_radio' => esc_html__("As radio", 'woocommerce-products-filter'),
                            );
                            ?>
                            <?php
                            if (!isset($woof_settings['sections_type']) OR empty($woof_settings['sections_type'])) {
                                $woof_settings['sections_type'] = 'tabs_checkbox';
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[sections_type]" class="chosen_select slideout_value" data-name="woof_sections_type">
                                    <?php foreach ($sections_behavior as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['sections_type'] == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e("Behavior of how the filter sections will open.", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
                <div class="woof-control-section">

                    <h4><?php esc_html_e('Sections', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container woof-control-section-sections">

                        <div class="woof-control-container ">
                            <div class="woof_sections_list_container">
                                <ul id='woof_sections_list'>
                                    <?php
                                    $sections = array();

                                    if (isset($woof_settings['sections']) && is_array($woof_settings['sections'])) {
                                        $sections = $woof_settings['sections'];
                                    }

                                    foreach ($sections as $key => $data) {
                                        $ext_sections->woof_draw_sctions_item($key, $data['title'], $data['from'], $data['to']);
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>

                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('Create new section. Don`t forget to click on the save button.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">
                    <input type="button" class="woof_add_sections woof-button" style="margin: 0;" value="<?php esc_html_e('Create section', 'woocommerce-products-filter') ?>">
                </div>

                <div class="woof-control-section">

                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="button" class="woof-button" id="woof_sections_generate" value="<?php esc_html_e("Generate attributes for shortcode [woof]", 'woocommerce-products-filter') ?>"><br>
                            <br>
                            <span class="woof_sections_shortcode_res"></span>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e("This button is just helper which allows to assemble data for [woof] shortcode.", 'woocommerce-products-filter') ?></p>
                            
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

            </section>

        </div>

    </div>
</section>
