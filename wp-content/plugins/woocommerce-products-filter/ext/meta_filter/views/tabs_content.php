<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-meta-filter">
    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>

                <div class="woof-section-title">
                    <div class="col-title">

                        <h4><?php esc_html_e('Meta Fields', 'woocommerce-products-filter') ?></h4>

                        <?php if (woof()->show_notes) : ?>
                            <br>
                            <div class="woof__alert woof__alert-info2 woof_tomato">
                                <?php esc_html_e('In FREE version it is possible to operate by 2 meta fields only!', 'woocommerce-products-filter') ?>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="col-button">
                        <a href="https://products-filter.com/extencion/woocommerce-filter-by-meta-fields/" target="_blank" class="button-primary"><span class="icon-info"></span></a><br />


                    </div>
                </div>



                <div class="woof-control-section">

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <h5><?php esc_html_e('Add Custom key by hands', 'woocommerce-products-filter') ?>:</h5>
                            <input type="text" value="" class="woof_meta_key_input woof_width_75p">&nbsp;<a href="#" id="woof_meta_add_new_btn" class="button button-primary button-large"><?php esc_html_e('Add', 'woocommerce-products-filter') ?></a> 


                        </div>
                        <div class="woof-description">
                            <h5><?php esc_html_e('Get keys from any product by its ID', 'woocommerce-products-filter') ?>:</h5>
                            <input type="number" min="1" class="woof_meta_keys_get_input woof_width_75p" value="" placeholder="<?php esc_html_e('enter product ID', 'woocommerce-products-filter') ?>">&nbsp;<a href="#" id="woof_meta_get_btn" class="button button-primary button-large"><?php esc_html_e('Get', 'woocommerce-products-filter') ?></a>


                        </div>
                    </div>

                </div>




                <div class="clear"></div>

                <br />

                <div id="metaform" method="post" action="">
                    <input type="hidden" name="woof_meta_fields[]" value="" />
                    <ul id="woof_meta_list" class="ui-sortable woof_fields">

                        <?php
                        if (!empty($metas)) {
                            $counter = 0;
                            foreach ($metas as $m) {
                                if ($m['meta_key'] == "__META_KEY__") {
                                    continue;
                                }

                                if (intval(WOOF_VERSION) === 1) {
                                    if ($counter++ >= 2) {
                                        break;
                                    }
                                }

                                woof_meta_print_li($m, $meta_types);
                            }
                        }
                        ?>

                    </ul>


                    <br />


                </div>

                <div style="display: none;" id="woof_meta_li_tpl">
                    <?php
                    woof_meta_print_li(array(
                        'meta_key' => '__META_KEY__',
                        'title' => '__TITLE__',
                        'search_view' => '',
                        'type' => '',
                        'options' => ''
                            ), $meta_types);
                    ?>
                </div>

                <?php

                function woof_meta_print_li($m, $meta_types) {
                    ?>
                    <li class="woof_options_li">
                        <span class="icon-arrow-combo help_tip2 woof_drag_and_drope" data-tip2="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>

                        <div class="woof_options_item">
                            <input type="text" name="woof_settings[meta_filter][<?php echo esc_attr($m['meta_key']) ?>][meta_key]" value="<?php echo esc_attr($m['meta_key']) ?>" readonly="" class="woof_column_li_option" />
                        </div>
                        <div class="woof_options_item">
                            <input type="text" name="woof_settings[meta_filter][<?php echo esc_attr($m['meta_key']) ?>][title]" placeholder="<?php esc_html_e('enter title', 'woocommerce-products-filter') ?>" value="<?php echo esc_html($m['title']) ?>" class="woof_column_li_option woof_fix2" />

                        </div>
                        <div class="woof_options_item">
                            <div class="select-wrap">
                                <select name="woof_settings[meta_filter][<?php echo esc_attr($m['meta_key']) ?>][search_view]" class="woof_meta_view_selector woof_width_99p">
                                    <?php
                                    foreach ($meta_types as $key => $type):
                                        if (!is_array($type['hide_if'])) {
                                            $type['hide_if'] = array($type['hide_if']);
                                        }
                                        if ($m['search_view'] == $key AND in_array($m['type'], $type['hide_if'])) {
                                            $m['search_view'] = 'textinput';
                                        }
                                        ?> 
                                        <option  <?php selected($m['search_view'], $key) ?> value="<?php echo esc_html($key) ?>" data-show-options="<?php echo esc_attr(($type['show_options']) ? 'yes' : 'no') ?>" data-hideif="<?php echo esc_html(implode(',', $type['hide_if'])) ?>" <?php if (in_array($m['type'], $type['hide_if'])): ?>style="display:none;"<?php endif; ?>>
                                            <?php esc_html_e($type['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        $show_options = false;
                        if (isset($meta_types[$m['search_view']]['show_options'])) {
                            $show_options = $meta_types[$m['search_view']]['show_options'];
                        }
                        ?>
                        <div class="woof_options_item_options" <?php if (!$show_options): ?> style="display:none;" <?php endif; ?> >
                            <div class="textarea-wrap">
                                <textarea name="woof_settings[meta_filter][<?php echo esc_attr($m['meta_key']) ?>][options]" class="woof_column_li_option" ><?php echo esc_html(isset($m['options']) ? $m['options'] : "") ?></textarea>
                            </div>
                            <div class="woof-meta-description">
                                <p><i><?php esc_html_e('Use comma as in example: 1,2,3,4,5. If you want structure like title->value use next syntax example: France^1,Germany^2,USA^3. Countries are titles here.', 'woocommerce-products-filter') ?></i></p>
                            </div>
                        </div>
                        <div class="woof_options_item">
                            <div class="select-wrap" <?php if (in_array($m['search_view'], array('popupeditor', 'switcher'))): ?>style="display: none;"<?php endif; ?>>
                                <select name="woof_settings[meta_filter][<?php echo esc_attr($m['meta_key']) ?>][type]" class="woof_meta_type_selector">
                                    <option <?php selected($m['type'], 'NUMERIC') ?> value="NUMERIC"><?php esc_html_e('number', 'woocommerce-products-filter') ?></option>
                                    <option <?php selected($m['type'], 'string') ?> value="string"><?php esc_html_e('string', 'woocommerce-products-filter') ?></option>
                                    <option <?php selected($m['type'], 'DATE') ?> value="DATE"><?php esc_html_e('date', 'woocommerce-products-filter') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="woof_options_item">
                            <a href="#" class="button button-primary woof_meta_delete" title="<?php esc_html_e('delete', 'woocommerce-products-filter') ?>"><span class="dashicons dashicons-trash"></span></a>
                        </div>

                        <div class="clear clearfix"></div>
                    </li>
                    <?php
                }
                ?>
            </section>

        </div>

    </div>
</section>




