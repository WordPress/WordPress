<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<li data-key="<?php echo esc_attr($key) ?>" class="woof_options_li">

    <?php
    $show = 0;
    if (isset($woof_settings[$key]['show'])) {
        $show = $woof_settings[$key]['show'];
    }
    ?>

    <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>

    <strong class="woof_fix1"><?php esc_html_e("Search by Text", 'woocommerce-products-filter'); ?>:</strong>


    <span class="icon-question help_tip" data-tip="<?php esc_html_e('Show textinput for searching by products title', 'woocommerce-products-filter') ?>"></span>

    <div class="select-wrap">
        <select name="woof_settings[<?php echo esc_attr($key) ?>][show]" class="woof_setting_select">
            <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
            <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
        </select>
    </div>

    <a href="#" data-key="<?php echo esc_attr($key) ?>" data-name="<?php esc_html_e("Search by text", 'woocommerce-products-filter'); ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>

    <?php
    if (!isset($woof_settings[$key]['title'])) {
        $woof_settings[$key]['title'] = '';
    }

    if (!isset($woof_settings[$key]['placeholder'])) {
        $woof_settings[$key]['placeholder'] = '';
    }

    if (!isset($woof_settings[$key]['behavior'])) {
        $woof_settings[$key]['behavior'] = 'title';
    }
    if (!isset($woof_settings[$key]['search_by_full_word'])) {
        $woof_settings[$key]['search_by_full_word'] = 0;
    }
    if (!isset($woof_settings[$key]['search_desc_variant'])) {
        $woof_settings[$key]['search_desc_variant'] = 0;
    }
    if (!isset($woof_settings[$key]['sku_compatibility'])) {
        $woof_settings[$key]['sku_compatibility'] = 0;
    }
    if (!isset($woof_settings[$key]['autocomplete'])) {
        $woof_settings[$key]['autocomplete'] = 0;
    }

    if (!isset($woof_settings[$key]['post_links_in_autocomplete'])) {
        $woof_settings[$key]['post_links_in_autocomplete'] = 0;
    }

    if (!isset($woof_settings[$key]['how_to_open_links'])) {
        $woof_settings[$key]['how_to_open_links'] = 0;
    }


    if (!isset($woof_settings[$key]['image'])) {
        $woof_settings[$key]['image'] = '';
    }

    if (!isset($woof_settings[$key]['notes_for_customer'])) {
        $woof_settings[$key]['notes_for_customer'] = '';
    }
    ?>

    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][title]" value="<?php echo esc_html($woof_settings[$key]['title']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][placeholder]" value="<?php echo esc_html($woof_settings[$key]['placeholder']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][behavior]" value="<?php echo esc_html($woof_settings[$key]['behavior']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][search_by_full_word]" value="<?php echo intval($woof_settings[$key]['search_by_full_word']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][search_desc_variant]" value="<?php echo intval($woof_settings[$key]['search_desc_variant']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][autocomplete]" value="<?php echo intval($woof_settings[$key]['autocomplete']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][post_links_in_autocomplete]" value="<?php echo intval($woof_settings[$key]['post_links_in_autocomplete']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][how_to_open_links]" value="<?php echo intval($woof_settings[$key]['how_to_open_links']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][sku_compatibility]" value="<?php echo intval($woof_settings[$key]['sku_compatibility']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][notes_for_customer]" value="<?php echo stripcslashes(wp_kses_post(wp_unslash($woof_settings[$key]['notes_for_customer']))); ?>" />

    <div id="woof-modal-content-by_text_2" style="display: none;">

        <div style="display: none;">
            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Title text', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Leave it empty if you not need this', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">
                    <input type="text" class="woof_popup_option" data-option="title" placeholder="" value="" />
                </div>

            </div>
        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Placeholder text', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Leave it empty if you not need this', 'woocommerce-products-filter') ?></span>
                <span><?php esc_html_e('Set "none" to disable placeholder for this textinput', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="placeholder" placeholder="" value="" />
            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Behavior', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('behavior of the text searching', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">

                <?php
                $behavior = array(
                    'title' => esc_html__("Search by title", 'woocommerce-products-filter'),
                    'content' => esc_html__("Search by content", 'woocommerce-products-filter'),
                    'excerpt' => esc_html__("Search by excerpt", 'woocommerce-products-filter'),
                    'content_or_excerpt' => esc_html__("Search by content OR excerpt", 'woocommerce-products-filter'),
                    'title_or_content_or_excerpt' => esc_html__("Search by title OR content OR excerpt", 'woocommerce-products-filter'),
                    'title_or_content' => esc_html__("Search by title OR content", 'woocommerce-products-filter'),
                    'title_and_content' => esc_html__("Search by title AND content", 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="behavior">
                        <?php foreach ($behavior as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Search by full word only', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('The result is only with the full coincidence of words', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $autocomplete = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="search_by_full_word">
                        <?php foreach ($autocomplete as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Autocomplete', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Autocomplete relevant variants in by_text textinput', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $autocomplete = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="autocomplete">
                        <?php foreach ($autocomplete as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Links to posts in suggestion', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Direct links to posts in autocomplete suggestion', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $post_links_in_autocomplete = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="post_links_in_autocomplete">
                        <?php foreach ($post_links_in_autocomplete as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('How to open links with posts in suggestion', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('In the same window (_self) or in the new one (_blank)', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $how_to_open_links = array(
                    0 => esc_html__('new window', 'woocommerce-products-filter'),
                    1 => esc_html__('the same window', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="how_to_open_links">
                        <?php foreach ($how_to_open_links as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('+SKU ', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Activates the ability to search by SKU from the same text-input', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $autocomplete = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="sku_compatibility">
                        <?php foreach ($autocomplete as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Search by description in variations', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Ability to search by the description of the any variation in the variable product', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $desc_var = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="search_desc_variant">
                        <?php foreach ($desc_var as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Notes for customer', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Any notes for customer.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="notes_for_customer"></textarea>
            </div>

        </div>  
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Image', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Image for text search button which appears near input when users typing there any symbols. Better use png. Size is: 20x20 px.', 'woocommerce-products-filter') ?></span>
                <span><?php esc_html_e('Example', 'woocommerce-products-filter') ?>: <?php echo esc_url(WOOF_LINK) ?>img/eye-icon1.png</span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="image" placeholder="" value="" />
                <a href="#" class="woof-button woof_select_image woof_select_image_last"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
            </div>

        </div>		
    </div>

</li>
