<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div id="woof-modal-shortcode-generator" _style="display: none;">

    <?php
    //sid
    //autohide
    //autosubmit
    //is_ajax
    //taxonomies
    //ajax_redraw
    //tax_only
    //by_only
    //tax_exclude
    //redirect
    //start_filtering_btn
    //btn_position
    //dynamic_recount
    //hide_terms_count_txt
    //conditionals
    //mobile_mode
    ?>
    <input type="button" class="button btn-warning woof_show_shortcode_generator" value="<?php esc_html_e('Close shortcode generator', 'woocommerce-products-filter') ?>" />
    <div class="woof-form-shortcode-generator">

        <h2><?php printf(esc_html__('Generate your own custom filter by shortcode %s', 'woocommerce-products-filter'), '<a href="https://products-filter.com/shortcode/woof" target="_blank">[woof]</a>') ?></h2>

        <?php /* sid */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $sid_g = uniqid('generator_');
            ?>
            <h4>sid</h4>
            <input class="shortcode-generator-value" name="sid" value="<?php echo esc_html($sid_g); ?>">
            <p><?php esc_html_e('Shortcode identifier is used to generate a unique CSS class in the main container of the generated search form, creating a unique design.', 'woocommerce-products-filter') ?></p>
        </div>
        <?php /* redirect */ ?>
        <div class="woof-form-shortcode-item">
            <h4>redirect</h4>
            <input name="redirect" value="" class="shortcode-generator-value">
            <p><?php esc_html_e('This field is for the full URL. Allows showing results on any other page of the shop.', 'woocommerce-products-filter') ?></p>
        </div>
        <?php /* autohide */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $autohide_g = array(
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>autohide</h4>
            <select name="autohide" class="chosen_select shortcode-generator-value">
                <?php foreach ($autohide_g as $autohide_key => $autohide_val) { ?>
                    <option value="<?php echo esc_html($autohide_key); ?>"><?php echo esc_html($autohide_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('This setting determines whether the filter form will be hidden or shown after the page is loaded.', 'woocommerce-products-filter') ?></p>
        </div>
        <?php /* autosubmit */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $autosubmit_g = array(
                -1 => esc_html("Default", 'woocommerce-products-filter'),
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>autosubmit</h4>
            <select name="autosubmit" class="chosen_select shortcode-generator-value">
                <?php foreach ($autosubmit_g as $autosubmit_key => $autosubmit_val) { ?>
                    <option value="<?php echo esc_html($autosubmit_key); ?>"><?php echo esc_html($autosubmit_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Allows form auto submit of the search form even if its disabled on the plugin options page.', 'woocommerce-products-filter') ?></p>
        </div>
        <?php /* is_ajax */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $is_ajax_g = array(
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>is_ajax</h4>
            <select name="is_ajax" class="chosen_select shortcode-generator-value">
                <?php foreach ($is_ajax_g as $is_ajax_key => $is_ajax_val) { ?>
                    <option value="<?php echo esc_html($is_ajax_key); ?>"><?php echo esc_html($is_ajax_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('This enables the AJAX mode.', 'woocommerce-products-filter') ?></p>
        </div>
        <?php /* ajax_redraw */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $ajax_redraw_g = array(
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>ajax_redraw</h4>
            <select name="ajax_redraw" class="chosen_select shortcode-generator-value">
                <?php foreach ($ajax_redraw_g as $ajax_redraw_key => $ajax_redraw_val) { ?>
                    <option value="<?php echo esc_html($ajax_redraw_key); ?>"><?php echo esc_html($ajax_redraw_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Redraw the search form without submitting the search data, just a redrawing. Does not work with AJAX mode.', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* start_filtering_btn */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $start_filtering_btn_g = array(
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>start_filtering_btn</h4>
            <select name="start_filtering_btn" class="chosen_select shortcode-generator-value">
                <?php foreach ($start_filtering_btn_g as $start_filtering_btn_key => $start_filtering_btn_val) { ?>
                    <option value="<?php echo esc_html($start_filtering_btn_key); ?>"><?php echo esc_html($start_filtering_btn_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Filter as a button. If the form is hidden, you will see a simple button. After clicking the button, the search form will appear. Allows to hide big form improve page performance.', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* btn_position */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $btn_position_g = array(
                'b' => esc_html("Bottom", 'woocommerce-products-filter'),
                't' => esc_html("Top", 'woocommerce-products-filter'),
                'tb' => esc_html("Top&bottom", 'woocommerce-products-filter'),
            );
            ?>
            <h4>btn_position</h4>
            <select name="btn_position" class="chosen_select shortcode-generator-value">
                <?php foreach ($btn_position_g as $btn_position_key => $btn_position_val) { ?>
                    <option value="<?php echo esc_html($btn_position_key); ?>"><?php echo esc_html($btn_position_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Allows to set the Filter and Reset button on the: bottom, top, or both (top and bottom)', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* dynamic_recount */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $dynamic_recount_g = array(
                -1 => esc_html("Default", 'woocommerce-products-filter'),
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>dynamic_recount</h4>
            <select name="dynamic_recount" class="chosen_select shortcode-generator-value">
                <?php foreach ($dynamic_recount_g as $dynamic_recount_key => $dynamic_recount_val) { ?>
                    <option value="<?php echo esc_html($dynamic_recount_key); ?>"><?php echo esc_html($dynamic_recount_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Allows enabling or disabling dynamic recount for the current product search form.', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* hide_terms_count_txt */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $hide_terms_count_txt_g = array(
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>hide_terms_count_txt</h4>
            <select name="hide_terms_count_txt" class="chosen_select shortcode-generator-value">
                <?php foreach ($hide_terms_count_txt_g as $hide_terms_count_txt_key => $hide_terms_count_txt_val) { ?>
                    <option value="<?php echo esc_html($hide_terms_count_txt_key); ?>"><?php echo esc_html($hide_terms_count_txt_val); ?></option>
                <?php } ?>
            </select>
            <p>
                <?php esc_html_e('Hides the text with the count of variants', 'woocommerce-products-filter') ?>
                <?php if (woof()->show_notes): ?>
                    <span class="woof_red"><?php esc_html_e('enabled in premium version', 'woocommerce-products-filter') ?></span>
                <?php endif; ?>
            </p>
        </div>

        <?php /* mobile_mode */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $mobile_mode_g = array(
                0 => esc_html("No", 'woocommerce-products-filter'),
                1 => esc_html("Yes", 'woocommerce-products-filter'),
            );
            ?>
            <h4>mobile_mode</h4>
            <select name="mobile_mode" class="chosen_select shortcode-generator-value">
                <?php foreach ($mobile_mode_g as $mobile_mode_key => $mobile_mode_val) { ?>
                    <option value="<?php echo esc_html($mobile_mode_key); ?>"><?php echo esc_html($mobile_mode_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Hides the filter form on mobile devices and displays a button to show the filter.', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* tax_only */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $all_taxonomies_array = woof()->get_taxonomies();
            $all_taxonomies = array();
            foreach ($all_taxonomies_array as $tax_key => $tax_data) {
                $all_taxonomies[$tax_key] = $tax_data->label;
            }
            ?>
            <h4>tax_only</h4>
            <select multiple="multiple" name="tax_only" class="chosen_select shortcode-generator-value">
                <option value="none"><?php echo esc_html("None", 'woocommerce-products-filter'); ?></option>
                <?php foreach ($all_taxonomies as $all_taxonomy_key => $all_taxonomy_val) { ?>
                    <option value="<?php echo esc_html($all_taxonomy_key); ?>"><?php echo esc_html($all_taxonomy_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Use this for taxonomies filter sections which you want to see on the form', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* by_only */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $by_items_array = woof()->items_keys;
            $by_items = array();
            foreach ($by_items_array as $by_item) {
                $by_items[$by_item] = $by_item;
            }
            ?>
            <h4>by_only</h4>
            <select multiple="multiple" name="by_only" class="chosen_select shortcode-generator-value">
                <option value="none"><?php echo esc_html("None", 'woocommerce-products-filter'); ?></option>
                <?php foreach ($by_items as $by_item_key => $by_item_val) { ?>
                    <option value="<?php echo esc_html($by_item_key); ?>"><?php echo esc_html($by_item_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Use this for NOT taxonomies filter sections you want to see on the form', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* tax_exclude */ ?>
        <div class="woof-form-shortcode-item">
            <?php
            $all_filters = array_merge($all_taxonomies, $by_items);
            ?>
            <h4>tax_exclude</h4>
            <select multiple="multiple" name="tax_exclude" class="chosen_select shortcode-generator-value">
                <?php foreach ($all_filters as $all_filters_key => $all_filters_val) { ?>
                    <option value="<?php echo esc_html($all_filters_key); ?>"><?php echo esc_html($all_filters_val); ?></option>
                <?php } ?>
            </select>
            <p><?php esc_html_e('Exclude taxonomies from the filter (vice versa for param tax_only).', 'woocommerce-products-filter') ?></p>
        </div>

        <?php /* taxonomies */ ?>
        <div class="woof-form-shortcode-item  woof-form-shortcode-item-wide">
            <?php
            $all_taxonomies_array;
            ?>
            <h4>taxonomies</h4>
            <div class="woof_select_taxonomy_wrapper">
                <select  class="chosen_select woof_select_taxonomy">
                    <option value="-1"><?php echo esc_html("Select taxonomy", 'woocommerce-products-filter'); ?></option>
                    <?php foreach ($all_taxonomies_array as $all_filters_key => $all_filters_val) { ?>
                        <option value="<?php echo esc_html($all_filters_key); ?>"><?php echo esc_html($all_filters_val->label); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="woof_select_term_wrapper">
                <select multiple="multiple"  class="chosen_select woof_select_term">
                </select>
            </div>
            <button style="display: none;" class="button btn-warning woof_add_taxonomies_shortcode">
                <?php echo esc_html("Select Taxonomies Terms", 'woocommerce-products-filter'); ?>
                <span class="dashicons dashicons-migrate"></span>
            </button>
            <input name="taxonomies" class="shortcode-generator-value">
            <p><?php esc_html_e('uses to display relevant filter-items in generated search form if activated: show count+dynamic recount+hide empty options in the plugin settings. Just select taxonomy, terms and press button Select Taxonomies Terms', 'woocommerce-products-filter') ?></p>

        </div>
        
        <br>
        <hr>

        <div class="woof-form-shortcode-generate">
            <button  class="button btn-warning generate_woof_shortcode">
                <?php echo esc_html("Generate shortcode", 'woocommerce-products-filter'); ?>
                <span class="dashicons dashicons-shortcode"></span>
            </button>
            <button  style="display: none;"class="button btn-warning copy_woof_shortcode">
                <?php echo esc_html("Copy", 'woocommerce-products-filter'); ?>
                <span class="dashicons dashicons-admin-page"></span>
            </button>
            <span style="display: none;" class="copy_woof_shortcode_info"><?php echo esc_html_e("Copied!", 'woocommerce-products-filter'); ?></span>
            <textarea class="woof-form-shortcode-generate-result">
            </textarea>
        </div>

    </div>
</div>
