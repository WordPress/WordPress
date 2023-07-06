<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<!--<div class="woof-admin-preloader"></div>-->
<div class="woof-admin-preloader">
    <div class="cssload-loader">
        <div class="cssload-inner cssload-one"></div>
        <div class="cssload-inner cssload-two"></div>
        <div class="cssload-inner cssload-three"></div>
    </div>
</div>

<div class="subsubsub_section <?php echo esc_attr($this->show_notes ? "woof_free" : "") ?>">

    <div class="woof_fix12"></div>

    <section class="woof-section">

        <?php if (isset($_GET['settings_saved'])): ?>
            <div class="woof-notice"><?php esc_html_e("Your settings have been saved.", 'woocommerce-products-filter') ?></div>
        <?php endif; ?>


        <div class="woof-header">
            <div>
                <h3 class="woof_plugin_name"><?php esc_html_e('HUSKY - Products Filter Professional for WooCommerce', 'woocommerce-products-filter') ?>&nbsp;<span class="woof__text-success">v.<?php echo esc_attr(WOOF_VERSION) ?></span>&nbsp;<span id="woof-head"><svg><use xlink:href="#svg-woof"></use></svg></span></h3>
                <i><?php printf(esc_html__('Actualized for WooCommerce v.%s.x', 'woocommerce-products-filter'), WOOCOMMERCE_VERSION) ?></i><br />


            </div>
             <div>
                <?php if ($this->show_notes && time() > 1688076000): ?>
                    <br><a href="https://codecanyon.pluginus.net/item/woof-woocommerce-products-filter/11498469" target="_blank" class="woof-button"><span class="icon-upload"></span><?php esc_html_e('Upgrade', 'woocommerce-products-filter') ?></a>
                <?php else: ?>
                    <br><a href="https://codecanyon.pluginus.net/item/woof-woocommerce-products-filter/11498469" style="padding: 7px 12px" target="_blank" class="woof-button"><span class="icon-upload"></span><?php esc_html_e("Upgrade to Premium with 33% OFF until 30th June 2023") ?></a>
                <?php endif; ?>
            </div>
        </div>

        <input type="hidden" name="woof_settings" value="" />
        <input type="hidden" name="woof_settings[items_order]" value="<?php echo esc_html(isset($woof_settings['items_order']) ? $woof_settings['items_order'] : '') ?>" />
        <input type="hidden" name="_wpnonce_woof" value="<?php echo wp_create_nonce('woof_save_option'); ?>">
        <?php if (version_compare(WOOCOMMERCE_VERSION, WOOF_MIN_WOOCOMMERCE_VERSION, '<')): ?>

            <div id="message" class="error fade"><p><strong><?php esc_html_e("ATTENTION! Your version of the woocommerce plugin is too obsolete. There is no warranty for working with HUSKY!!", 'woocommerce-products-filter') ?></strong></p></div>

        <?php endif; ?>

        <div id="tabs" class="woof-tabs">

            <nav>
                <ul>
                    <li class="tab-current">
                        <a href="#tabs-1">
                            <span class="icon-cubes"></span>
                            <span><?php esc_html_e("Structure", 'woocommerce-products-filter') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tabs-2">
                            <span class="icon-cog-outline"></span>
                            <span><?php esc_html_e("Options", 'woocommerce-products-filter') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tabs-3">
                            <span class="icon-picture"></span>
                            <span><?php esc_html_e("Design", 'woocommerce-products-filter') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tabs-4">
                            <span class="icon-steam"></span>
                            <span><?php esc_html_e("Advanced", 'woocommerce-products-filter') ?></span>
                        </a>
                    </li>


                    <?php
                    if (!empty(WOOF_EXT::$includes['applications'])) {
                        foreach (WOOF_EXT::$includes['applications'] as $obj) {
                            $dir1 = $this->get_custom_ext_path() . $obj->folder_name;
                            $dir2 = WOOF_EXT_PATH . $obj->folder_name;
                            $checked1 = WOOF_EXT::is_ext_activated($dir1);
                            $checked2 = WOOF_EXT::is_ext_activated($dir2);
                            if ($checked1 OR $checked2) {
                                do_action('woof_print_applications_tabs_' . $obj->folder_name);
                            }
                        }
                    }
                    ?>

                    <li>
                        <a href="#tabs-6">
                            <span class="icon-puzzle-outline"></span>
                            <span><?php esc_html_e("Extensions", 'woocommerce-products-filter') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tabs-7">
                            <span class="icon-info"></span>
                            <span><?php esc_html_e("Info", 'woocommerce-products-filter') ?></span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="content-wrap">

                <section id="tabs-1" class="content-current">

                    <ul id="woof_options">

                        <?php
                        $items_order = array();
                        $taxonomies = $this->get_taxonomies();
                        $taxonomies_keys = array_keys($taxonomies);
                        if (isset($woof_settings['items_order']) AND !empty($woof_settings['items_order'])) {
                            $items_order = explode(',', $woof_settings['items_order']);
                        } else {
                            $items_order = array_merge($this->items_keys, $taxonomies_keys);
                        }

//*** lets check if we have new taxonomies added in woocommerce or new item
                        foreach (array_merge($this->items_keys, $taxonomies_keys) as $key) {
                            if (!in_array($key, $items_order)) {
                                $items_order[] = $key;
                            }
                        }

//lets print our items and taxonomies
                        foreach ($items_order as $key) {

                            if (in_array($key, $this->items_keys)) {
                                woof_print_item_by_key($key, $woof_settings);
                            } else {
                                if (isset($taxonomies[$key])) {
                                    woof_print_tax($key, $taxonomies[$key], $woof_settings);
                                }
                            }
                        }
                        ?>
                    </ul>
                    <input type="button" class="button btn-warning woof_show_shortcode_generator" value="<?php esc_html_e('Custom filter form generator', 'woocommerce-products-filter') ?>" />

                    <input type="button" class="button btn-warning woof_reset_order" value="<?php esc_html_e('Reset items order', 'woocommerce-products-filter') ?>" />

                    <div class="clear"></div>

                </section>

                <section id="tabs-2">

                    <?php woocommerce_admin_fields($this->get_options()); ?>

                </section>

                <section id="tabs-3">

                    <?php
                    $skins = array(
                        'none' => array('none'),
                        'flat' => array(
                            'flat_aero',
                            'flat_blue',
                            'flat_flat',
                            'flat_green',
                            'flat_grey',
                            'flat_orange',
                            'flat_pink',
                            'flat_purple',
                            'flat_red',
                            'flat_yellow'
                        ),
                        'minimal' => array(
                            'minimal_aero',
                            'minimal_blue',
                            'minimal_green',
                            'minimal_grey',
                            'minimal_minimal',
                            'minimal_orange',
                            'minimal_pink',
                            'minimal_purple',
                            'minimal_red',
                            'minimal_yellow'
                        ),
                        'square' => array(
                            'square_aero',
                            'square_blue',
                            'square_green',
                            'square_grey',
                            'square_orange',
                            'square_pink',
                            'square_purple',
                            'square_red',
                            'square_yellow',
                            'square_square'
                        )
                    );
                    $skin = 'none';
                    if (isset($woof_settings['icheck_skin'])) {
                        $skin = $woof_settings['icheck_skin'];
                    }
                    ?>

                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Radio and checkboxes skin', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">

                                <select name="woof_settings[icheck_skin]" class="chosen_select">
                                    <?php foreach ($skins as $key => $schemes) : ?>
                                        <optgroup label="<?php echo esc_attr($key) ?>">
                                            <?php foreach ($schemes as $scheme) : ?>
                                                <option value="<?php echo esc_attr($scheme) ?>" <?php selected($skin == $scheme) ?>><?php echo esc_attr($scheme) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                            <div class="woof-description"></div>
                        </div>

                    </div><!--/ .woof-control-section-->

                    <?php
                    $skins = array(
                        'default' => esc_html__('Default', 'woocommerce-products-filter'),
                        'plainoverlay' => esc_html__('Plainoverlay - CSS', 'woocommerce-products-filter'),
                        'loading-balls' => esc_html__('Loading balls - SVG', 'woocommerce-products-filter'),
                        'loading-bars' => esc_html__('Loading bars - SVG', 'woocommerce-products-filter'),
                        'loading-bubbles' => esc_html__('Loading bubbles - SVG', 'woocommerce-products-filter'),
                        'loading-cubes' => esc_html__('Loading cubes - SVG', 'woocommerce-products-filter'),
                        'loading-cylon' => esc_html__('Loading cyclone - SVG', 'woocommerce-products-filter'),
                        'loading-spin' => esc_html__('Loading spin - SVG', 'woocommerce-products-filter'),
                        'loading-spinning-bubbles' => esc_html__('Loading spinning bubbles - SVG', 'woocommerce-products-filter'),
                        'loading-spokes' => esc_html__('Loading spokes - SVG', 'woocommerce-products-filter'),
                    );
                    if (!isset($woof_settings['overlay_skin'])) {
                        $woof_settings['overlay_skin'] = 'default';
                    }
                    $skin = $woof_settings['overlay_skin'];
                    ?>


                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Overlay skins', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">

                                <select name="woof_settings[overlay_skin]" class="chosen_select">
                                    <?php foreach ($skins as $scheme => $title) : ?>
                                        <option value="<?php echo esc_attr($scheme) ?>" <?php selected($skin == $scheme) ?>><?php esc_html_e($title) ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                            <div class="woof-description">

                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->

                    <?php
                    if (!isset($woof_settings['overlay_skin_bg_img'])) {
                        $woof_settings['overlay_skin_bg_img'] = '';
                    }
                    $overlay_skin_bg_img = $woof_settings['overlay_skin_bg_img'];
                    ?>


                    <div class="woof-control-section" <?php if ($skin == 'default'): ?>style="display: none;"<?php endif; ?>>

                        <h4><?php esc_html_e('Overlay image background', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">

                                <input type="text" name="woof_settings[overlay_skin_bg_img]" value="<?php echo esc_attr($overlay_skin_bg_img) ?>" />

                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a><br />

                                <div <?php if ($skin != 'plainoverlay'): ?>style="display: none;"<?php endif; ?>>
                                    <br />
                                    <?php
                                    if (!isset($woof_settings['plainoverlay_color'])) {
                                        $woof_settings['plainoverlay_color'] = '';
                                    }
                                    $plainoverlay_color = $woof_settings['plainoverlay_color'];
                                    ?>

                                    <h4><?php esc_html_e('Plainoverlay color', 'woocommerce-products-filter') ?></h4>
                                    <input type="text" name="woof_settings[plainoverlay_color]" value="<?php echo esc_attr($plainoverlay_color) ?>" id="woof_color_picker_plainoverlay_color" class="woof-color-picker" />

                                </div>

                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Example', 'woocommerce-products-filter') ?>: <?php echo esc_url(WOOF_LINK) ?>img/overlay_bg.png
                                </p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->


                    <div class="woof-control-section" <?php if ($skin != 'default'): ?>style="display: none;"<?php endif; ?>>

                        <h4><?php esc_html_e('Loading word', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                if (!isset($woof_settings['default_overlay_skin_word'])) {
                                    $woof_settings['default_overlay_skin_word'] = '';
                                }
                                $default_overlay_skin_word = $woof_settings['default_overlay_skin_word'];
                                ?>



                                <input type="text" name="woof_settings[default_overlay_skin_word]" value="<?php echo esc_attr($default_overlay_skin_word) ?>" />


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Word while searching is going on front when "Overlay skins" is default.', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>
                    </div><!--/ .woof-control-section-->

                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Select design', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                $select_designs = array(
                                    'native' => esc_html__('Native', 'woocommerce-products-filter'),
                                    'chosen' => esc_html__('Chosen', 'woocommerce-products-filter'),
                                    'selectwoo' => esc_html__('SelectWoo', 'woocommerce-products-filter'),
                                );

                                if (!isset($woof_settings['select_design'])) {
                                    $woof_settings['select_design'] = 'selectwoo';
                                    if (isset($woof_settings['use_chosen']) && $woof_settings['use_chosen']) {
                                        $woof_settings['select_design'] = 'chosen';
                                    }
                                }


                                $select_design = $woof_settings['select_design'];
                                ?>

                                <div class="select-wrap">
                                    <select name="woof_settings[select_design]" class="chosen_select">
                                        <?php foreach ($select_designs as $key => $value) : ?>
                                            <option value="<?php echo esc_attr($key) ?>" <?php selected($select_design == $key) ?>><?php esc_html_e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('JS library for select', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>
                    </div><!--/ .woof-control-section-->


                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Use beauty scroll', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                $use_beauty_scroll = array(
                                    0 => esc_html__('No', 'woocommerce-products-filter'),
                                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                                );

                                if (!isset($woof_settings['use_beauty_scroll'])) {
                                    $woof_settings['use_beauty_scroll'] = 0;
                                }
                                $use_scroll = $woof_settings['use_beauty_scroll'];
                                ?>

                                <div class="select-wrap">
                                    <select name="woof_settings[use_beauty_scroll]" class="chosen_select">
                                        <?php foreach ($use_beauty_scroll as $key => $value) : ?>
                                            <option value="<?php echo esc_attr($key) ?>" <?php selected($use_scroll == $key) ?>><?php esc_html_e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Use beauty scroll when you apply max height for taxonomy block on the front', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>
                    </div><!--/ .woof-control-section-->


                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Range-slider skin', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                $skins = array(
                                    'round' => 'Round',
                                    'flat' => 'skinFlat',
                                    'big' => 'skinHTML5',
                                    'modern' => 'skinModern',
                                    'sharp' => 'Sharp',
                                    'square' => 'Square',
                                );

                                if (!isset($woof_settings['ion_slider_skin'])) {
                                    $woof_settings['ion_slider_skin'] = 'round';
                                }

                                //comp  with old  ion slider
                                if (!isset($skins[$woof_settings['ion_slider_skin']])) {

                                    if (array_search($woof_settings['ion_slider_skin'], $skins) !== false) {
                                        $woof_settings['ion_slider_skin'] = array_search($woof_settings['ion_slider_skin'], $skins);
                                    } else {
                                        $woof_settings['ion_slider_skin'] = 'round';
                                    }
                                }


                                $skin = $woof_settings['ion_slider_skin'];
                                ?>

                                <div class="select-wrap">
                                    <select name="woof_settings[ion_slider_skin]" class="chosen_select">
                                        <?php foreach ($skins as $key => $value) : ?>
                                            <option value="<?php echo esc_attr($key) ?>" <?php selected($skin == $key) ?>><?php esc_html_e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Ion-Range slider js lib skin for range-sliders of the plugin', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>
                    </div><!--/ .woof-control-section-->

                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Use tooltip', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                $tooltip_selects = array(
                                    0 => esc_html__('No', 'woocommerce-products-filter'),
                                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                                );

                                if (!isset($woof_settings['use_tooltip'])) {
                                    $woof_settings['use_tooltip'] = 1;
                                }
                                $tooltip_select = $woof_settings['use_tooltip'];
                                ?>

                                <div class="select-wrap">
                                    <select name="woof_settings[use_tooltip]" class="chosen_select">
                                        <?php foreach ($tooltip_selects as $key => $value) : ?>
                                            <option value="<?php echo esc_attr($key) ?>" <?php selected($tooltip_select == $key) ?>><?php esc_html_e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Use tooltip library on the front of your site. Possible to disable it here if any scripts conflicts on the site front.', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>
                    </div><!--/ .woof-control-section-->

                    <?php if (get_option('woof_set_automatically')): ?>
                        <div class="woof-control-section">

                            <h4><?php esc_html_e('Hide auto filter by default', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">

                                    <?php
                                    $woof_auto_hide_button = array(
                                        0 => esc_html__('No', 'woocommerce-products-filter'),
                                        1 => esc_html__('Yes', 'woocommerce-products-filter')
                                    );
                                    if (!isset($woof_settings['woof_auto_hide_button'])) {
                                        $woof_settings['woof_auto_hide_button'] = 1;
                                    }
                                    $woof_auto_hide_button_val = $woof_settings['woof_auto_hide_button'];
                                    ?>

                                    <select name="woof_settings[woof_auto_hide_button]" class="chosen_select">
                                        <?php foreach ($woof_auto_hide_button as $v => $n) : ?>
                                            <option value="<?php echo esc_attr($v) ?>" <?php selected($woof_auto_hide_button_val == $v) ?>><?php esc_html_e($n) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('If in options tab option "Set filter automatically" is "Yes" you can hide filter and show hide/show button instead of it.', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                        </div><!--/ .woof-control-section-->

                        <div class="woof-control-section">

                            <h4><?php esc_html_e('Skins for the auto filter', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">

                                    <?php
                                    $woof_auto_filter_skins = array(
                                        '' => esc_html__('Default', 'woocommerce-products-filter'),
                                        'flat_white woof_auto_1_columns' => esc_html__('Flat white (1column)', 'woocommerce-products-filter'),
                                        'flat_grey woof_auto_1_columns' => esc_html__('Flat grey (1column)', 'woocommerce-products-filter'),
                                        'flat_dark woof_auto_1_columns' => esc_html__('Flat dark (1column)', 'woocommerce-products-filter'),
                                        'flat_grey woof_auto_2_columns' => esc_html__('Flat grey (2columns)', 'woocommerce-products-filter'),
                                        'flat_dark woof_auto_2_columns' => esc_html__('Flat dark (2columns)', 'woocommerce-products-filter'),
                                        'flat_grey woof_auto_3_columns' => esc_html__('Flat grey (3columns)', 'woocommerce-products-filter'),
                                        'flat_dark woof_auto_3_columns' => esc_html__('Flat dark (3columns)', 'woocommerce-products-filter'),
                                        'flat_grey woof_auto_4_columns' => esc_html__('Flat grey (4columns) without sidebar*', 'woocommerce-products-filter'),
                                        'flat_dark woof_auto_4_columns' => esc_html__('Flat dark (4columns) without sidebar*', 'woocommerce-products-filter'),
                                    );

                                    if (!array_key_exists('woof_auto_filter_skins', $woof_settings)) {
                                        $woof_settings['woof_auto_filter_skins'] = '';
                                    }

                                    $woof_auto_filter_skins_val = $woof_settings['woof_auto_filter_skins'];
                                    ?>

                                    <select name="woof_settings[woof_auto_filter_skins]" class="chosen_select">
                                        <?php foreach ($woof_auto_filter_skins as $v => $n) : ?>
                                            <option value="<?php echo esc_attr($v) ?>" <?php selected($woof_auto_filter_skins_val == $v) ?>><?php esc_html_e($n) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Skins for the auto-filter which appears on the shop page if in tab Options enabled Set filter automatically', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                        </div><!--/ .woof-control-section-->
                    <?php endif; ?>

                    <?php
                    if (!isset($woof_settings['woof_tooltip_img'])) {
                        $woof_settings['woof_tooltip_img'] = '';
                    }
                    ?>
                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Tooltip icon', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[woof_tooltip_img]" value="<?php echo esc_attr($woof_settings['woof_tooltip_img']) ?>" />
                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Image which displayed for tooltip', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->

                    <?php
                    if (!isset($woof_settings['woof_auto_hide_button_img'])) {
                        $woof_settings['woof_auto_hide_button_img'] = '';
                    }

                    if (!isset($woof_settings['woof_auto_hide_button_txt'])) {
                        $woof_settings['woof_auto_hide_button_txt'] = '';
                    }
                    ?>

                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Auto filter close/open image', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[woof_auto_hide_button_img]" value="<?php echo esc_attr($woof_settings['woof_auto_hide_button_img']) ?>" />
                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Image which displayed instead filter while it is closed if selected. Write "none" here if you want to use text only!', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->


                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Auto filter close/open text', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[woof_auto_hide_button_txt]" value="<?php echo esc_html($woof_settings['woof_auto_hide_button_txt']) ?>" />
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Text which displayed instead filter while it is closed if selected.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->

                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Image for subcategories open', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[woof_auto_subcats_plus_img]" value="<?php echo esc_attr(isset($woof_settings['woof_auto_subcats_plus_img']) ? $woof_settings['woof_auto_subcats_plus_img'] : '') ?>" />
                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Image when you select in tab Options "Hide childs in checkboxes and radio". By default it is green cross.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                        <h4><?php esc_html_e('Image for subcategories close', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[woof_auto_subcats_minus_img]" value="<?php echo esc_attr(isset($woof_settings['woof_auto_subcats_minus_img']) ? $woof_settings['woof_auto_subcats_minus_img'] : '') ?>" />
                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Image when you select in tab Options "Hide childs in checkboxes and radio". By default it is green minus.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->
                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Image for mobile filter button', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[image_mobile_behavior_open]" value="<?php echo esc_attr(isset($woof_settings['image_mobile_behavior_open']) ? $woof_settings['image_mobile_behavior_open'] : '') ?>" />
                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Image of button when activated mobile mode. Set -1 to disable.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>
                        <h4><?php esc_html_e('Text for mobile filter button', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <?php
                                if (!isset($woof_settings['text_mobile_behavior_open'])) {
                                    $woof_settings['text_mobile_behavior_open'] = esc_html__('Open filter', 'woocommerce-products-filter');
                                }
                                ?>
                                <input type="text" name="woof_settings[text_mobile_behavior_open]" value="<?php echo esc_html($woof_settings['text_mobile_behavior_open']) ?>" />
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Text for button when activated mobile mode. Set -1 to disable.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>


                        <h4><?php esc_html_e('Image to close mobile filter', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[image_mobile_behavior_close]" value="<?php echo esc_attr(isset($woof_settings['image_mobile_behavior_close']) ? $woof_settings['image_mobile_behavior_close'] : '') ?>" />
                                <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Image for close button when activated mobile mode. Set -1 to disable.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>
                        <h4><?php esc_html_e('Text to close mobile filter', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <?php
                                if (!isset($woof_settings['text_mobile_behavior_close'])) {
                                    $woof_settings['text_mobile_behavior_close'] = esc_html__('Close filter', 'woocommerce-products-filter');
                                }
                                ?>
                                <input type="text" name="woof_settings[text_mobile_behavior_close]" value="<?php echo esc_attr($woof_settings['text_mobile_behavior_close']) ?>" />
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('Text for close button when activated mobile mode. Set -1 to disable.', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->


                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Toggle block type', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                $toggle_types = array(
                                    'text' => esc_html__('Text', 'woocommerce-products-filter'),
                                    'image' => esc_html__('Images', 'woocommerce-products-filter')
                                );

                                if (!isset($woof_settings['toggle_type'])) {
                                    $woof_settings['toggle_type'] = 'text';
                                }
                                $toggle_type = $woof_settings['toggle_type'];
                                ?>

                                <div class="select-wrap">
                                    <select name="woof_settings[toggle_type]" class="chosen_select" id="toggle_type">
                                        <?php foreach ($toggle_types as $key => $value) : ?>
                                            <option value="<?php echo esc_attr($key) ?>" <?php selected($toggle_type == $key) ?>><?php esc_html_e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Type of the toggle on the front for block of html-items as: radio, checkbox .... Works only if the block title is not hidden!', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>

                        <div class="toggle_type_text" <?php if ($toggle_type == 'image'): ?>style="display: none;"<?php endif; ?>>

                            <h4><?php esc_html_e('Text for block toggle opened', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['toggle_opened_text'])) {
                                        $woof_settings['toggle_opened_text'] = '';
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[toggle_opened_text]" value="<?php echo esc_html($woof_settings['toggle_opened_text']) ?>" />
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Toggle text for opened html-items block. Example: close. By default applied sign minus "-"', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                            <h4><?php esc_html_e('Text for block toggle closed', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['toggle_closed_text'])) {
                                        $woof_settings['toggle_closed_text'] = '';
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[toggle_closed_text]" value="<?php echo esc_html($woof_settings['toggle_closed_text']) ?>" />
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Toggle text for closed html-items block. Example: open. By default applied sign plus "+"', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                        </div>


                        <div class="toggle_type_image" <?php if ($toggle_type == 'text'): ?>style="display: none;"<?php endif; ?>>
                            <h4><?php esc_html_e('Image for block toggle [opened]', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['toggle_opened_image'])) {
                                        $woof_settings['toggle_opened_image'] = '';
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[toggle_opened_image]" value="<?php echo esc_attr(isset($woof_settings['toggle_opened_image']) ? $woof_settings['toggle_opened_image'] : '') ?>" />
                                    <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Any image for opened html-items block 20x20', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>


                            <h4><?php esc_html_e('Image for block toggle closed', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['toggle_closed_image'])) {
                                        $woof_settings['toggle_closed_image'] = '';
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[toggle_closed_image]" value="<?php echo esc_attr(isset($woof_settings['toggle_closed_image']) ? $woof_settings['toggle_closed_image'] : '') ?>" />
                                    <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Any image for closed html-items block 20x20', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>
                        </div>



                    </div><!--/ .woof-control-section-->


                    <div class="woof-control-section">

                        <h4><?php esc_html_e('More/less button type', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <div class="woof-control">

                                <?php
                                $more_less_types = array(
                                    'text' => esc_html__('Text', 'woocommerce-products-filter'),
                                    'image' => esc_html__('Images', 'woocommerce-products-filter')
                                );

                                if (!isset($woof_settings['more_less_type'])) {
                                    $woof_settings['more_less_type'] = 'text';
                                }
                                $more_less_type = $woof_settings['more_less_type'];
                                ?>

                                <div class="select-wrap">
                                    <select name="woof_settings[more_less_type]" class="chosen_select" id="more_less_type">
                                        <?php foreach ($more_less_types as $key => $value) : ?>
                                            <option value="<?php echo esc_attr($key) ?>" <?php selected($more_less_type == $key) ?>><?php esc_html_e($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>
                            <div class="woof-description">
                                <p class="description">
                                    <?php esc_html_e('Uses for filter sections when option <Not toggled terms count> is enabled in additional options of a filter section', 'woocommerce-products-filter') ?>
                                </p>
                            </div>
                        </div>

                        <div class="more_less_type_text" <?php if ($more_less_type == 'image'): ?>style="display: none;"<?php endif; ?>>

                            <h4><?php esc_html_e('Text for block more/less opened', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['more_less_opened_text']) || empty($woof_settings['more_less_opened_text'])) {
                                        $woof_settings['more_less_opened_text'] = esc_html__('Show more', 'woocommerce-products-filter');
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[more_less_opened_text]" value="<?php echo esc_html($woof_settings['more_less_opened_text']) ?>" />
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Text for opened state', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                            <h4><?php esc_html_e('Text for block more/less closed', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['more_less_closed_text']) || empty($woof_settings['more_less_closed_text'])) {
                                        $woof_settings['more_less_closed_text'] = esc_html__('Show less', 'woocommerce-products-filter');
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[more_less_closed_text]" value="<?php echo esc_html($woof_settings['more_less_closed_text']) ?>" />
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Text for closed state', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>

                        </div>


                        <div class="more_less_type_image" <?php if ($more_less_type == 'text'): ?>style="display: none;"<?php endif; ?>>
                            <h4><?php esc_html_e('Image for block more/less [opened]', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['more_less_opened_image'])) {
                                        $woof_settings['more_less_opened_image'] = '';
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[more_less_opened_image]" value="<?php echo esc_attr(isset($woof_settings['more_less_opened_image']) ? $woof_settings['more_less_opened_image'] : '') ?>" />
                                    <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Image for opened state', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>


                            <h4><?php esc_html_e('Image for block more/less closed', 'woocommerce-products-filter') ?></h4>

                            <div class="woof-control-container">
                                <div class="woof-control">
                                    <?php
                                    if (!isset($woof_settings['more_less_closed_image'])) {
                                        $woof_settings['more_less_closed_image'] = '';
                                    }
                                    ?>
                                    <input type="text" name="woof_settings[more_less_closed_image]" value="<?php echo esc_attr(isset($woof_settings['more_less_closed_image']) ? $woof_settings['more_less_closed_image'] : '') ?>" />
                                    <a href="#" class="woof-button woof_select_image"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
                                </div>
                                <div class="woof-description">
                                    <p class="description"><?php esc_html_e('Image for closed state', 'woocommerce-products-filter') ?></p>
                                </div>
                            </div>
                        </div>



                    </div><!--/ .woof-control-section-->



                    <?php
                    if (!isset($woof_settings['custom_front_css'])) {
                        $woof_settings['custom_front_css'] = '';
                    }
                    ?>

                    <div class="woof-control-section">

                        <h4><?php esc_html_e('Custom front css styles file link', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">
                                <input type="text" name="woof_settings[custom_front_css]" value="<?php echo esc_attr($woof_settings['custom_front_css']) ?>" />
                            </div>
                            <div class="woof-description">
                                <p class="description"><?php esc_html_e('For developers who want to rewrite front css of the plugin front side. You are need to know CSS for this!', 'woocommerce-products-filter') ?></p>
                            </div>
                        </div>

                    </div><!--/ .woof-control-section-->

                    <?php do_action('woof_print_design_additional_options'); ?>

                </section>

                <section id="tabs-4">

                    <div class="woof-tabs woof-tabs-style-line">

                        <nav>
                            <ul>
                                <li>
                                    <a href="#tabs-41">
                                        <span class="icon-code"></span>
                                        <span><?php esc_html_e("Code", 'woocommerce-products-filter') ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tabs-42">
                                        <span class="icon-cog-outline"></span>
                                        <span><?php esc_html_e("Options", 'woocommerce-products-filter') ?></span>
                                    </a>
                                </li>
                                <?php do_action('woof_print_applications_tabs_anvanced'); ?>
                            </ul>
                        </nav>

                        <div class="content-wrap">

                            <section id="tabs-41">

                                <table class="form-table">

                                    <tr>
                                        <th scope="row"><label for="custom_css_code"><?php esc_html_e('Custom CSS code', 'woocommerce-products-filter') ?></label></th>

                                        <td>
                                            <textarea class="wide woof_custom_css" id="custom_css_code" name="woof_settings[custom_css_code]"><?php if (isset($this->settings['custom_css_code'])): ?><?php echo esc_textarea(stripcslashes($this->settings['custom_css_code'])) ?><?php endif; ?></textarea>
                                            <p class="description"><?php esc_html_e("If you are need to customize something and you don't want to lose your changes after update", 'woocommerce-products-filter') ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="js_after_ajax_done"><?php esc_html_e('JavaScript code after AJAX is done', 'woocommerce-products-filter') ?></label></th>
                                        <td>
                                            <textarea class="wide woof_custom_css" id="js_after_ajax_done" name="woof_settings[js_after_ajax_done]"><?php if (isset($this->settings['js_after_ajax_done'])): ?><?php echo stripcslashes(esc_js(str_replace('"', "'", $this->settings['js_after_ajax_done']))) ?><?php endif; ?></textarea>
                                            <p class="description"><?php esc_html_e('Use it when you are need additional action after AJAX redraw your products in shop page or in page with shortcode! For use when you need additional functionality after AJAX redraw of your products on the shop page or on pages with shortcodes.', 'woocommerce-products-filter') ?></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row"><label for="init_only_on"><?php esc_html_e('Init plugin on the next site pages only ', 'woocommerce-products-filter') ?></label></th>
                                        <td>
                                            <div class="woof-control-section">
                                                <div class="woof-control-container">
                                                    <div class="woof-control">

                                                        <?php
                                                        $init_only_on_r = array(
                                                            0 => esc_html__("Yes", 'woocommerce-products-filter'),
                                                            1 => esc_html__("No", 'woocommerce-products-filter')
                                                        );
                                                        ?>

                                                        <?php
                                                        if (!isset($woof_settings['init_only_on_reverse']) OR empty($woof_settings['init_only_on_reverse'])) {
                                                            $woof_settings['init_only_on_reverse'] = 0;
                                                        }
                                                        ?>
                                                        <div class="select-wrap">
                                                            <select name="woof_settings[init_only_on_reverse]">
                                                                <?php foreach ($init_only_on_r as $key => $value) : ?>
                                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['init_only_on_reverse'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                    </div>
                                                    <div class="woof-description woof_fix13">
                                                        <p class="description"><?php esc_html_e("Reverse: deactivate plugin on the next site pages only", 'woocommerce-products-filter') ?></p>
                                                    </div>
                                                </div>

                                            </div><!--/ .woof-control-section-->



                                            <?php
                                            if (!isset($this->settings['init_only_on'])) {
                                                $this->settings['init_only_on'] = '';
                                            }
                                            ?>
                                            <textarea class="wide woof_custom_css" id="init_only_on" name="woof_settings[init_only_on]"><?php echo stripcslashes(trim(esc_textarea($this->settings['init_only_on']))) ?></textarea>
                                            <p class="description"><?php esc_html_e('This option enables or disables initialization of the plugin on all pages of the site except links and link-masks in the textarea. One row - one link (or link-mask)! Example of link: http://site.com/ajaxed-search-7. Example of link-mask: product-category . Leave it empty to allow the plugin initialization on all pages of the site!', 'woocommerce-products-filter') ?></p>
                                            <p class="description"><?php esc_html_e('Use sign # before link to apply strict compliance. Example: #https://your_site.com/product-category/man/', 'woocommerce-products-filter') ?></p>
                                        </td>
                                    </tr>


                                    <?php if (class_exists('SitePress') OR class_exists('Polylang')): ?>
                                        <tr>
                                            <th scope="row"><label for="wpml_tax_labels">
                                                    <?php esc_html_e('WPML taxonomies labels translations', 'woocommerce-products-filter') ?> <img class="help_tip" data-tip="Syntax:
                                                         es:Locations^Ubicaciones
                                                         es:Size^Tamaño
                                                         de:Locations^Lage
                                                         de:Size^Größe" src="<?php echo esc_url(WOOF_LINK) ?>/img/help.png" height="16" width="16" />
                                                </label></th>
                                            <td>

                                                <?php
                                                $wpml_tax_labels = "";
                                                if (isset($woof_settings['wpml_tax_labels']) AND is_array($woof_settings['wpml_tax_labels'])) {
                                                    foreach ($woof_settings['wpml_tax_labels'] as $lang => $words) {
                                                        if (!empty($words) AND is_array($words)) {
                                                            foreach ($words as $key_word => $translation) {
                                                                $wpml_tax_labels .= $lang . ':' . $key_word . '^' . $translation . PHP_EOL;
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>

                                                <textarea class="wide woof_custom_css" id="wpml_tax_labels" name="woof_settings[wpml_tax_labels]"><?php echo esc_textarea($wpml_tax_labels) ?></textarea>
                                                <p class="description"><?php esc_html_e('Use it if you can not translate your custom taxonomies labels and attributes labels by another plugins.', 'woocommerce-products-filter') ?></p>

                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                </table>

                            </section>

                            <section id="tabs-42">

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Search slug', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            if (!isset($woof_settings['swoof_search_slug']) OR $this->show_notes) {
                                                $woof_settings['swoof_search_slug'] = '';
                                            }
                                            ?>

                                            <input placeholder="swoof" type="text" name="woof_settings[swoof_search_slug]" value="<?php echo esc_attr($woof_settings['swoof_search_slug']) ?>" id="swoof_search_slug" />

                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e('If you do not like search key "swoof" in the search link you can replace it by your own word. But be care to avoid conflicts with any themes and plugins, + never define it as symbol "s". Not understood? Simply do not touch it!', 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Products per page', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">
                                            <?php
                                            if (!isset($woof_settings['per_page'])) {
                                                $woof_settings['per_page'] = -1;
                                            }
                                            ?>

                                            <input type="text" name="woof_settings[per_page]" value="<?php echo intval($woof_settings['per_page']) ?>" id="per_page" />
                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e('Products per page when searching is going only. Set here -1 to prevent pagination managing from here!', 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e("Optimize loading of HUSKY JavaScript files", 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            $optimize_js_files = array(
                                                0 => esc_html__("No", 'woocommerce-products-filter'),
                                                1 => esc_html__("Yes", 'woocommerce-products-filter')
                                            );
                                            ?>

                                            <?php
                                            if (!isset($woof_settings['optimize_js_files']) OR empty($woof_settings['optimize_js_files'])) {
                                                $woof_settings['optimize_js_files'] = 0;
                                            }
                                            ?>

                                            <select name="woof_settings[optimize_js_files]">
                                                <?php foreach ($optimize_js_files as $key => $value) : ?>
                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['optimize_js_files'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                <?php endforeach; ?>
                                            </select>


                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e("This option place HUSKY JavaScript files on the site footer. Use it for page loading optimization. Be care with this option, and always after enabling of it test your site frontend!", 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Override no products found content', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            if (!isset($woof_settings['override_no_products']) OR $this->show_notes) {
                                                $woof_settings['override_no_products'] = '';
                                            }
                                            ?>

                                            <textarea name="woof_settings[override_no_products]" id="override_no_products" ><?php echo wp_kses_post(wp_unslash($woof_settings['override_no_products'])) ?></textarea>

                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e('Place in which you can paste text or/and any shortcodes which will be displayed when customer will not find any products by his search criterias. Example:', 'woocommerce-products-filter') ?> <i class="woof_orangered">&lt;center&gt;&lt;h2>Where are the products?&lt;/h2&gt;&lt;/center&gt;&lt;h4&gt;Perhaps you will like next products&lt;/h4&gt;[recent_products limit="3" columns="4" ]</i> (<?php esc_html_e('do not use shortcodes here in turbo mode', 'woocommerce-products-filter') ?>)</p>
                                        </div>
                                    </div>

                                </div>
                                <div class="woof-control-section woof_premium_only">
                                    <?php
                                    $show_images_by_attr = array(
                                        0 => esc_html__("No", 'woocommerce-products-filter'),
                                        1 => esc_html__("Yes", 'woocommerce-products-filter')
                                    );
                                    if (!isset($woof_settings['show_images_by_attr_show']) OR empty($woof_settings['show_images_by_attr_show']) && $this->show_notes) {
                                        $woof_settings['show_images_by_attr_show'] = 0;
                                    }
                                    ?>

                                    <h5><?php esc_html_e("Show image of variation", 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <select name="woof_settings[show_images_by_attr_show]">
                                                <?php foreach ($show_images_by_attr as $key => $value) : ?>
                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['show_images_by_attr_show'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                <?php endforeach; ?>
                                            </select>


                                            <?php
                                            $attributes = wc_get_attribute_taxonomies();
                                            ?>

                                            <?php
                                            if (!isset($woof_settings['show_images_by_attr']) OR empty($woof_settings['show_images_by_attr']) OR $this->show_notes) {
                                                $woof_settings['show_images_by_attr'] = array();
                                            }
                                            ?>
                                            <div class="select-wrap chosen_select" <?php if (!$woof_settings['show_images_by_attr_show']) : ?> style='display:none;' <?php endif; ?> >
                                                <select  class="chosen_select" multiple name="woof_settings[show_images_by_attr][]">
                                                    <?php foreach ($attributes as $attr) : ?>
                                                        <option value="pa_<?php echo esc_attr($attr->attribute_name) ?>" <?php selected(in_array('pa_' . $attr->attribute_name, $woof_settings['show_images_by_attr'])) ?>><?php esc_html_e($attr->attribute_label) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                        </div>


                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e("For variable products you can show an image depending on the current filter selection. For example you have variation with red color, and that varation has its own preview image - if on the site front user will select red color this imag will be shown. You can select attributes by which images will be selected", 'woocommerce-products-filter') ?></p>
                                        </div>

                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section woof_premium_only">

                                    <h5><?php esc_html_e("Hide terms count text", 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            $hide_terms_count_txt = array(
                                                0 => esc_html__("No", 'woocommerce-products-filter'),
                                                1 => esc_html__("Yes", 'woocommerce-products-filter')
                                            );

                                            if ($this->show_notes) {
                                                unset($hide_terms_count_txt[1]);
                                            }
                                            ?>

                                            <?php
                                            if (!isset($woof_settings['hide_terms_count_txt']) OR empty($woof_settings['hide_terms_count_txt']) OR $this->show_notes) {
                                                $woof_settings['hide_terms_count_txt'] = 0;
                                            }
                                            ?>

                                            <select name="woof_settings[hide_terms_count_txt]">
                                                <?php foreach ($hide_terms_count_txt as $key => $value) : ?>
                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['hide_terms_count_txt'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                <?php endforeach; ?>
                                            </select>


                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e("If you want show relevant tags on the categories pages you should activate show count, dynamic recount and hide empty terms in the tab Options. But if you do not want show count (number) text near each term - set Yes here.", 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e("Listen catalog visibility", 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            $listen_catalog_visibility = array(
                                                0 => esc_html__("No", 'woocommerce-products-filter'),
                                                1 => esc_html__("Yes", 'woocommerce-products-filter')
                                            );
                                            ?>

                                            <?php
                                            if (!isset($woof_settings['listen_catalog_visibility']) OR empty($woof_settings['listen_catalog_visibility'])) {
                                                $woof_settings['listen_catalog_visibility'] = 0;
                                            }
                                            ?>

                                            <select name="woof_settings[listen_catalog_visibility]">
                                                <?php foreach ($listen_catalog_visibility as $key => $value) : ?>
                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['listen_catalog_visibility'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                <?php endforeach; ?>
                                            </select>


                                        </div>
                                        <div class="woof-description">
                                            <p class="description">
                                                <?php esc_html_e("Listen catalog visibility - options in each product backend page in 'Publish' sidebar widget.", 'woocommerce-products-filter') ?><br />
                                                <a href="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/listen_catalog_visibility.png" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/listen_catalog_visibility.png" width="150" alt="" /></a>
                                            </p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->


                                <div class="woof-control-section">

                                    <h5><?php esc_html_e("Disable swoof influence", 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            $disable_swoof_influence = array(
                                                0 => esc_html__("No", 'woocommerce-products-filter'),
                                                1 => esc_html__("Yes", 'woocommerce-products-filter')
                                            );
                                            ?>

                                            <?php
                                            if (!isset($woof_settings['disable_swoof_influence']) OR empty($woof_settings['disable_swoof_influence'])) {
                                                $woof_settings['disable_swoof_influence'] = 0;
                                            }
                                            ?>

                                            <select name="woof_settings[disable_swoof_influence]">
                                                <?php foreach ($disable_swoof_influence as $key => $value) : ?>
                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['disable_swoof_influence'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                <?php endforeach; ?>
                                            </select>


                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e("Sometimes code 'wp_query->is_post_type_archive = true' does not necessary. Try to disable this and try woof-search on your site. If all is ok - leave its disabled. Disabled code by this option you can find in index.php by mark disable_swoof_influence.", 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <?php if (!isset($woof_settings['woof_turbo_mode']['enable']) OR $woof_settings['woof_turbo_mode']['enable'] != 1 OR !class_exists("WOOF_EXT_TURBO_MODE")) { ?>
                                    <div class="woof-control-section">

                                        <h5><?php esc_html_e("Cache dynamic recount number for each item in filter", 'woocommerce-products-filter') ?></h5>

                                        <div class="woof-control-container">
                                            <div class="woof-control">

                                                <?php
                                                $cache_count_data = array(
                                                    0 => esc_html__("No", 'woocommerce-products-filter'),
                                                    1 => esc_html__("Yes", 'woocommerce-products-filter')
                                                );
                                                ?>

                                                <?php
                                                if (!isset($woof_settings['cache_count_data']) OR empty($woof_settings['cache_count_data'])) {
                                                    $woof_settings['cache_count_data'] = 0;
                                                }
                                                ?>

                                                <select name="woof_settings[cache_count_data]">
                                                    <?php foreach ($cache_count_data as $key => $value) : ?>
                                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['cache_count_data'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                    <?php endforeach; ?>
                                                </select>


                                                <?php if ($woof_settings['cache_count_data']): ?>
                                                    <br />
                                                    <br /><a href="#" class="button js_cache_count_data_clear"><?php esc_html_e("clear cache", 'woocommerce-products-filter') ?></a>&nbsp;<span class="woof_green"></span><br />
                                                    <br />
                                                    <?php
                                                    $clean_period = 'days7';
                                                    if (isset($this->settings['cache_count_data_auto_clean'])) {
                                                        $clean_period = $this->settings['cache_count_data_auto_clean'];
                                                    }
                                                    $periods = array(
                                                        0 => esc_html__("do not clean cache automatically", 'woocommerce-products-filter'),
                                                        'hourly' => esc_html__("clean cache automatically hourly", 'woocommerce-products-filter'),
                                                        'twicedaily' => esc_html__("clean cache automatically twicedaily", 'woocommerce-products-filter'),
                                                        'daily' => esc_html__("clean cache automatically daily", 'woocommerce-products-filter'),
                                                        'days2' => esc_html__("clean cache automatically each 2 days", 'woocommerce-products-filter'),
                                                        'days3' => esc_html__("clean cache automatically each 3 days", 'woocommerce-products-filter'),
                                                        'days4' => esc_html__("clean cache automatically each 4 days", 'woocommerce-products-filter'),
                                                        'days5' => esc_html__("clean cache automatically each 5 days", 'woocommerce-products-filter'),
                                                        'days6' => esc_html__("clean cache automatically each 6 days", 'woocommerce-products-filter'),
                                                        'days7' => esc_html__("clean cache automatically each 7 days", 'woocommerce-products-filter')
                                                    );
                                                    ?>

                                                    <select name="woof_settings[cache_count_data_auto_clean]">
                                                        <?php foreach ($periods as $key => $txt): ?>
                                                            <option <?php selected($clean_period, $key) ?> value="<?php echo esc_attr($key) ?>"><?php esc_html_e($txt) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>


                                                <?php endif; ?>

                                            </div>
                                            <div class="woof-description">

                                                <?php
                                                global $wpdb;

                                                $charset_collate = '';
                                                if (method_exists($wpdb, 'has_cap') AND $wpdb->has_cap('collation')) {
                                                    if (!empty($wpdb->charset)) {
                                                        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                                                    }
                                                    if (!empty($wpdb->collate)) {
                                                        $charset_collate .= " COLLATE $wpdb->collate";
                                                    }
                                                }
                                                //***
                                                $sql = "CREATE TABLE IF NOT EXISTS `" . WOOF::$query_cache_table . "` (
                                                    `mkey` varchar(64) NOT NULL,
                                                    `mvalue` text NOT NULL,
                                                    PRIMARY KEY `mkey` (`mkey`)
                                                  ) {$charset_collate}";

                                                if ($wpdb->query($sql) === false) {
                                                    ?>
                                                    <p class="description"><?php esc_html_e("HUSKY cannot create the database table! Make sure that your mysql user has the CREATE privilege! Do it manually using your host panel&phpmyadmin!", 'woocommerce-products-filter') ?></p>
                                                    <code><?php echo esc_html($sql) ?></code>
                                                    <input type="hidden" name="woof_settings[cache_count_data]" value="0" />
                                                    <?php
                                                    esc_html_e($wpdb->last_error);
                                                }
                                                ?>

                                                <p class="description"><?php esc_html_e("Useful thing when you already set your site IN THE PRODUCTION MODE and use dynamic recount -> it make recount very fast! Of course if you added new products which have to be in search results you have to clean this cache OR you can set time period for auto cleaning!", 'woocommerce-products-filter') ?></p>
                                            </div>
                                        </div>

                                    </div><!--/ .woof-control-section-->



                                    <div class="woof-control-section">

                                        <h5><?php esc_html_e("Cache terms", 'woocommerce-products-filter') ?></h5>

                                        <div class="woof-control-container">
                                            <div class="woof-control">

                                                <?php
                                                $cache_terms = array(
                                                    0 => esc_html__("No", 'woocommerce-products-filter'),
                                                    1 => esc_html__("Yes", 'woocommerce-products-filter')
                                                );
                                                ?>

                                                <?php
                                                if (!isset($woof_settings['cache_terms']) OR empty($woof_settings['cache_terms'])) {
                                                    $woof_settings['cache_terms'] = 0;
                                                }
                                                ?>

                                                <select name="woof_settings[cache_terms]">
                                                    <?php foreach ($cache_terms as $key => $value) : ?>
                                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['cache_terms'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                    <?php endforeach; ?>
                                                </select>


                                                <?php if ($woof_settings['cache_terms']): ?>
                                                    <br />
                                                    <br /><a href="#" class="button js_cache_terms_clear"><?php esc_html_e("clear terms cache", 'woocommerce-products-filter') ?></a>&nbsp;<span class="woof_green"></span><br />
                                                    <br />
                                                    <?php
                                                    $clean_period = 'days7';
                                                    if (isset($this->settings['cache_terms_auto_clean'])) {
                                                        $clean_period = $this->settings['cache_terms_auto_clean'];
                                                    }
                                                    $periods = array(
                                                        0 => esc_html__("do not clean cache automatically", 'woocommerce-products-filter'),
                                                        'hourly' => esc_html__("clean cache automatically hourly", 'woocommerce-products-filter'),
                                                        'twicedaily' => esc_html__("clean cache automatically twicedaily", 'woocommerce-products-filter'),
                                                        'daily' => esc_html__("clean cache automatically daily", 'woocommerce-products-filter'),
                                                        'days2' => esc_html__("clean cache automatically each 2 days", 'woocommerce-products-filter'),
                                                        'days3' => esc_html__("clean cache automatically each 3 days", 'woocommerce-products-filter'),
                                                        'days4' => esc_html__("clean cache automatically each 4 days", 'woocommerce-products-filter'),
                                                        'days5' => esc_html__("clean cache automatically each 5 days", 'woocommerce-products-filter'),
                                                        'days6' => esc_html__("clean cache automatically each 6 days", 'woocommerce-products-filter'),
                                                        'days7' => esc_html__("clean cache automatically each 7 days", 'woocommerce-products-filter')
                                                    );
                                                    ?>
                                                    <div class="select-wrap">
                                                        <select name="woof_settings[cache_terms_auto_clean]">
                                                            <?php foreach ($periods as $key => $txt): ?>
                                                                <option <?php selected($clean_period, $key) ?> value="<?php echo esc_attr($key) ?>"><?php esc_html_e($txt) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                <?php endif; ?>

                                            </div>
                                            <div class="woof-description">
                                                <p class="description"><?php esc_html_e("Useful thing when you already set your site IN THE PRODUCTION MODE - its getting terms for filter faster without big MySQL queries! If you actively adds new terms every day or week you can set cron period for cleaning. Another way set: 'not clean cache automatically'!", 'woocommerce-products-filter') ?></p>
                                            </div>
                                        </div>

                                    </div><!--/ .woof-control-section-->

                                    <div class="woof-control-section">

                                        <h5><?php esc_html_e("Optimize price filter", 'woocommerce-products-filter') ?></h5>

                                        <div class="woof-control-container">
                                            <div class="woof-control">

                                                <?php
                                                $price_transient = array(
                                                    0 => esc_html__("No", 'woocommerce-products-filter'),
                                                    1 => esc_html__("Yes", 'woocommerce-products-filter')
                                                );
                                                ?>

                                                <?php
                                                if (!isset($woof_settings['price_transient']) OR empty($woof_settings['price_transient'])) {
                                                    $woof_settings['price_transient'] = 0;
                                                }
                                                ?>

                                                <select name="woof_settings[price_transient]">
                                                    <?php foreach ($price_transient as $key => $value) : ?>
                                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['price_transient'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                    <?php endforeach; ?>
                                                </select>


                                                <?php if ($woof_settings['price_transient']): ?>
                                                    <br />
                                                    <br /><a href="#" class="button js_price_transient_clear"><?php esc_html_e("clear", 'woocommerce-products-filter') ?></a>&nbsp;<span class="woof_green"></span><br />
                                                    <br />
                                                <?php endif; ?>

                                            </div>
                                            <div class="woof-description">
                                                <p class="description"><?php esc_html_e("Helps to more quickly find the minimum and maximum values for the filter by price on the site front and minimize server loading.", 'woocommerce-products-filter') ?></p>
                                            </div>
                                        </div>

                                    </div><!--/ .woof-control-section-->

                                <?php } ?>
                                <div class="woof-control-section">

                                    <h5><?php esc_html_e("Show blocks helper button", 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">

                                            <?php
                                            $show_woof_edit_view = array(
                                                0 => esc_html__("No", 'woocommerce-products-filter'),
                                                1 => esc_html__("Yes", 'woocommerce-products-filter')
                                            );
                                            ?>

                                            <?php
                                            if (!isset($woof_settings['show_woof_edit_view'])) {
                                                $woof_settings['show_woof_edit_view'] = 0;
                                            }
                                            ?>

                                            <select id="show_woof_edit_view" name="woof_settings[show_woof_edit_view]">
                                                <?php foreach ($show_woof_edit_view as $key => $value) : ?>
                                                    <option value="<?php echo esc_attr($key) ?>" <?php selected($woof_settings['show_woof_edit_view'] == $key) ?>><?php esc_html_e($value) ?></option>
                                                <?php endforeach; ?>
                                            </select>


                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e("Show helper button for shortcode [woof] on the front when 'Set filter automatically' is Yes", 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Custom extensions folder', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">
                                            <?php
                                            if (!isset($woof_settings['custom_extensions_path'])) {
                                                $woof_settings['custom_extensions_path'] = '';
                                            }
                                            ?>

                                            <input type="text" name="woof_settings[custom_extensions_path]" value="<?php echo esc_html($woof_settings['custom_extensions_path']) ?>" id="custom_extensions_path" placeholder="Example: my_woof_extensions" />
                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php printf(__('Custom extensions folder path relative to: %s', 'woocommerce-products-filter'), WP_CONTENT_DIR . DIRECTORY_SEPARATOR) ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Result count css selector', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">
                                            <?php
                                            if (!isset($woof_settings['result_count_redraw'])) {
                                                $woof_settings['result_count_redraw'] = "";
                                            }
                                            ?>

                                            <input type="text" name="woof_settings[result_count_redraw]" value="<?php echo esc_html($woof_settings['result_count_redraw']) ?>"  />
                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e('Css class of result-count container. Is needed for ajax compatibility with wp themes. If you do not understand, leave it blank.', 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Order dropdown css selector', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">
                                            <?php
                                            if (!isset($woof_settings['order_dropdown_redraw'])) {
                                                $woof_settings['order_dropdown_redraw'] = "";
                                            }
                                            ?>

                                            <input type="text" name="woof_settings[order_dropdown_redraw]" value="<?php echo esc_html($woof_settings['order_dropdown_redraw']) ?>"  />
                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e('Css class of ordering dropdown container. Is needed for ajax compatibility with wp themes. If you do not understand, leave it blank.', 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->
                                <div class="woof-control-section">

                                    <h5><?php esc_html_e('Per page css selector', 'woocommerce-products-filter') ?></h5>

                                    <div class="woof-control-container">
                                        <div class="woof-control">
                                            <?php
                                            if (!isset($woof_settings['per_page_redraw'])) {
                                                $woof_settings['per_page_redraw'] = "";
                                            }
                                            ?>

                                            <input type="text" name="woof_settings[per_page_redraw]" value="<?php echo esc_html($woof_settings['per_page_redraw']) ?>"  />
                                        </div>
                                        <div class="woof-description">
                                            <p class="description"><?php esc_html_e('Css class of per page dropdown container. Is needed for ajax compatibility with wp themes. If you do not understand, leave it blank.', 'woocommerce-products-filter') ?></p>
                                        </div>
                                    </div>

                                </div><!--/ .woof-control-section-->

                            </section>

                            <?php do_action('woof_print_applications_tabs_content_advanced'); ?>

                        </div>

                    </div>

                </section>



                <?php
                if (!empty(WOOF_EXT::$includes['applications'])) {
                    foreach (WOOF_EXT::$includes['applications'] as $obj) {
                        $dir1 = $this->get_custom_ext_path() . $obj->folder_name;
                        $dir2 = WOOF_EXT_PATH . $obj->folder_name;
                        $checked1 = WOOF_EXT::is_ext_activated($dir1);
                        $checked2 = WOOF_EXT::is_ext_activated($dir2);
                        if ($checked1 OR $checked2) {
                            do_action('woof_print_applications_tabs_content_' . $obj->folder_name);
                        }
                    }
                }
                ?>



                <section id="tabs-6">

                    <div class="woof-tabs woof-tabs-style-line">

                        <nav>
                            <ul>
                                <li>
                                    <a href="#tabs-61">
                                        <span class="icon-cog-outline"></span>
                                        <span><?php esc_html_e("Extensions", 'woocommerce-products-filter') ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tabs-62">
                                        <span class="icon-cog-outline"></span>
                                        <span><?php esc_html_e("Ext-Applications options", 'woocommerce-products-filter') ?></span>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <div class="content-wrap">


                            <section id="tabs-61">

                                <div class="select-wrap">
                                    <select id="woof_manipulate_with_ext">
                                        <option value="0"><?php esc_html_e('All', 'woocommerce-products-filter') ?></option>
                                        <option value="1"><?php esc_html_e('Enabled', 'woocommerce-products-filter') ?></option>
                                        <option value="2"><?php esc_html_e('Disabled', 'woocommerce-products-filter') ?></option>
                                    </select>
                                </div>

                                <input type="hidden" name="woof_settings[activated_extensions]" value="" />

                                <br><br>


                                <?php if (true): ?>


                                    <!-- ----------------------------------------- -->
                                    <?php if (isset($this->settings['custom_extensions_path']) AND !empty($this->settings['custom_extensions_path'])): ?>




                                        <div class="woof-section-title">
                                            <div class="col-title">

                                                <h4><?php esc_html_e('Custom extensions', 'woocommerce-products-filter') ?></h4>

                                            </div>
                                            <div class="col-button">

                                                <?php
                                                $is_custom_extensions = false;
                                                if (is_dir($this->get_custom_ext_path())) {
                                                    //$dir_writable = substr(sprintf('%o', fileperms($this->get_custom_ext_path())), -4) == "0774" ? true : false;
                                                    $dir_writable = is_writable($this->get_custom_ext_path());
                                                    if ($dir_writable) {
                                                        $is_custom_extensions = true;
                                                    }
                                                } else {
                                                    if (!empty($this->settings['custom_extensions_path'])) {
                                                        //ext dir auto creation
                                                        $dir = $this->get_custom_ext_path();
                                                        try {
                                                            mkdir($dir, 0777);
                                                            $dir_writable = is_writable($this->get_custom_ext_path());
                                                            if ($dir_writable) {
                                                                $is_custom_extensions = true;
                                                            }
                                                        } catch (Exception $e) {
                                                            //***
                                                        }
                                                    }
                                                }
                                                //***
                                                if ($is_custom_extensions):
                                                    ?>

                                                    <div id="errormsg" class="clearfix redtext"></div>

                                                    <div id="pic-progress-wrap" class="progress-wrap"></div>

                                                    <div id="picbox" class="clear"></div>

                                                <?php else: ?>
                                                    <span class="woof_orangered"><?php printf(__('Note for admin: Folder %s for extensions is not writable OR doesn exists! Ignore this message if you not planning using HUSKY custom extensions!', 'woocommerce-products-filter'), $this->get_custom_ext_path()) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($this->settings['custom_extensions_path'])): ?>
                                            <span class="woof_orangered"><?php esc_html_e('Note for admin: Create folder for custom extensions in wp-content folder: tab Advanced -> Options -> Custom extensions folder', 'woocommerce-products-filter') ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <!-- ----------------------------------------- -->

                                    <?php
                                    if (!isset($woof_settings['activated_extensions']) OR !is_array($woof_settings['activated_extensions'])) {
                                        $woof_settings['activated_extensions'] = array();
                                    }
                                    ?>
                                    <?php if (!empty($extensions) AND is_array($extensions)): ?>

                                        <input type="hidden" id="rm-ext-nonce" value="<?php echo wp_create_nonce('rm-ext-nonce') ?>">
                                        <ul class="woof_extensions woof_custom_extensions">

                                            <?php foreach ($extensions['custom'] as $dir): ?>
                                                <?php
                                                $checked = WOOF_EXT::is_ext_activated($dir);
                                                $idx = WOOF_EXT::get_ext_idx_new($dir);
                                                ?>
                                                <li class="woof_ext_li <?php echo esc_attr($checked ? 'is_enabled' : 'is_disabled'); ?>">
                                                    <?php
                                                    $info = array();
                                                    if (file_exists($dir . DIRECTORY_SEPARATOR . 'info.dat')) {
                                                        $info = WOOF_HELPER::parse_ext_data($dir . DIRECTORY_SEPARATOR . 'info.dat');
                                                    }
                                                    ?>
                                                    <div class="woof_ext-cell">
                                                        <label for="<?php echo esc_attr($idx) ?>">
                                                            <input type="checkbox" id="<?php echo esc_attr($idx) ?>" <?php if (isset($info['status']) AND $info['status'] == 'premium' AND $this->show_notes): ?>disabled="disabled"<?php endif; ?> <?php if ($checked): ?>checked=""<?php endif; ?> value="<?php echo esc_attr($idx) ?>" name="woof_settings[activated_extensions][]" />
                                                            <?php
                                                            echo '<h5>' . esc_html($info['title']) . '</h5>';
                                                            if (isset($info['link'])) {
                                                                echo '<a href="' . esc_attr($info['link']) . '" class="woof_ext_title" target="_blank"><span class="icon-link"></span></a>';
                                                            }
                                                            ?>
                                                            <span class="woof_ext_ver">
                                                                <?php
                                                                if (isset($info['version'])) {
                                                                    printf(__('<i>ver.</i> %s', 'woocommerce-products-filter'), $info['version']);
                                                                }
                                                                ?>
                                                            </span>
                                                        </label>

                                                        <?php
                                                        if (!empty($info)) {
                                                            if (!empty($info) AND is_array($info)) {
                                                                if (isset($info['description'])) {
                                                                    echo '<p class="description">' . wp_kses_post(wp_unslash($info['description'])) . '</p>';
                                                                }
                                                            } else {
                                                                echo esc_html($dir);
                                                                esc_html_e('You should write extension info in info.dat file!', 'woocommerce-products-filter');
                                                            }
                                                        } else {
                                                            printf(__('Looks like its not the HUSKY extension here %s!', 'woocommerce-products-filter'), $dir);
                                                        }
                                                        ?>
                                                    </div>

                                                    <div class="woof_ext-cell">
                                                        <a href="javascript:void(0)" class="woof_ext_remove" data-idx="<?php echo esc_attr($idx) ?>" title="<?php esc_html_e('remove extension', 'woocommerce-products-filter') ?>">
                                                            <span class="icon-plus-circle"></span>
                                                        </a>
                                                    </div>

                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <div class="clear clearfix"></div>
                                    <br />
                                    <hr />

                                    <?php if (!empty($extensions['default'])): ?>


                                        <div class="woof-section-title">
                                            <div class="col-title">

                                                <h4><?php esc_html_e('Default extensions', 'woocommerce-products-filter') ?></h4>

                                            </div>
                                            <div class="col-button">&nbsp;</div>
                                        </div>


                                        <ul class="woof_extensions">
                                            <?php foreach ($extensions['default'] as $dir): ?>
                                                <?php
                                                $checked = WOOF_EXT::is_ext_activated($dir);
                                                $idx = WOOF_EXT::get_ext_idx_new($dir);
                                                ?>
                                                <li class="woof_ext_li <?php echo esc_attr($checked ? 'is_enabled' : 'is_disabled'); ?>">
                                                    <?php
                                                    $info = array();
                                                    if (file_exists($dir . DIRECTORY_SEPARATOR . 'info.dat')) {
                                                        $info = WOOF_HELPER::parse_ext_data($dir . DIRECTORY_SEPARATOR . 'info.dat');
                                                    }
                                                    ?>
                                                    <div class="woof_ext-cell">
                                                        <?php
                                                        if (!empty($info)) {
                                                            $info = WOOF_HELPER::parse_ext_data($dir . DIRECTORY_SEPARATOR . 'info.dat');
                                                            if (!empty($info) AND is_array($info)) {
                                                                ?>
                                                                <label for="<?php echo esc_attr($idx) ?>">
                                                                    <input type="checkbox" id="<?php echo esc_attr($idx) ?>" <?php if (isset($info['status']) AND $info['status'] == 'premium'): ?>disabled="disabled"<?php endif; ?> <?php if ($checked): ?>checked=""<?php endif; ?> value="<?php echo esc_attr($idx) ?>" name="woof_settings[activated_extensions][]" />
                                                                    <?php
                                                                    echo '<h5>' . esc_html($info['title']) . '</h5>';
                                                                    if (isset($info['link'])) {
                                                                        echo '<a href="' . esc_attr($info['link']) . '" class="woof_ext_title" target="_blank"><span class="icon-link"></span></a>';
                                                                    }
                                                                    ?>
                                                                    <span class="woof_ext_ver"><?php
                                                                        if (isset($info['version'])) {
                                                                            printf(__('<i>ver.</i> %s', 'woocommerce-products-filter'), $info['version']);
                                                                        }
                                                                        ?>
                                                                    </span>
                                                                </label>
                                                                <?php
                                                                echo '<p class="description">' . wp_kses_post($info['description']);
                                                                if (isset($info['warning']) && $this->show_notes) {
                                                                    echo " <span class='woof-ext-warning'>" . esc_html($info['warning']) . "</span>";
                                                                }
                                                                echo '</p>';
                                                            } else {
                                                                echo esc_html($dir);
                                                                esc_html_e('You should write extension info in info.dat file!', 'woocommerce-products-filter');
                                                            }
                                                        } else {
                                                            echo esc_html($dir);
                                                        }
                                                        ?>
                                                    </div>

                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                <?php endif; ?>
                                <div class="clear"></div>


                            </section>


                            <section id="tabs-62">

                                <div class="woof-tabs woof-tabs-style-line">

                                    <nav class="woof_ext_nav">
                                        <ul>
                                            <?php
                                            $is_custom_extensions = false;
                                            if (is_dir($this->get_custom_ext_path())) {
                                                $dir_writable = is_writable($this->get_custom_ext_path());
                                                if ($dir_writable) {
                                                    $is_custom_extensions = true;
                                                }
                                            }

                                            if ($is_custom_extensions) {
                                                if (!empty(WOOF_EXT::$includes['applications'])) {
                                                    foreach (WOOF_EXT::$includes['applications'] as $obj) {

                                                        $dir = $this->get_custom_ext_path() . $obj->folder_name;
                                                        $checked = WOOF_EXT::is_ext_activated($dir);
                                                        if (!$checked) {
                                                            continue;
                                                        }
                                                        ?>
                                                        <li>

                                                            <?php
                                                            if (file_exists($dir . DIRECTORY_SEPARATOR . 'info.dat')) {
                                                                $info = WOOF_HELPER::parse_ext_data($dir . DIRECTORY_SEPARATOR . 'info.dat');
                                                                if (!empty($info) AND is_array($info)) {
                                                                    $name = $info['title'];
                                                                } else {
                                                                    $name = $obj->folder_name;
                                                                }
                                                            } else {
                                                                $name = $obj->folder_name;
                                                            }
                                                            ?>
                                                            <a href="#tabs-<?php echo esc_attr($obj->folder_name) ?>" title="<?php printf(esc_html__("%s", 'woocommerce-products-filter'), $name) ?>">
                                                                <span><?php printf(esc_html__("%s", 'woocommerce-products-filter'), $name) ?></span>
                                                            </a>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>


                                        </ul>
                                    </nav>


                                    <div class="content-wrap woof_ext_opt">

                                        <?php
                                        if ($is_custom_extensions) {
                                            if (!empty(WOOF_EXT::$includes['applications'])) {
                                                foreach (WOOF_EXT::$includes['applications'] as $obj) {

                                                    $dir = $this->get_custom_ext_path() . $obj->folder_name;
                                                    $checked = WOOF_EXT::is_ext_activated($dir);
                                                    if (!$checked) {
                                                        continue;
                                                    }
                                                    do_action('woof_print_applications_options_' . $obj->folder_name);
                                                }
                                            }
                                        }
                                        ?>

                                    </div>


                                    <div class="clear"></div>

                                </div>




                            </section>

                        </div>

                    </div>

                </section>



                <section id="tabs-7">

                    <div class="woof-p-4">

                        <div class="woof-card-holder woof__col-2">

                            <div class="woof-card-item">

                                <div class="woof-card woof-transition woof-text-center woof-rounded">
                                    <div class="woof-card-body">
                                        <a href="https://pluginus.net/support/forum/woof-woocommerce-products-filter/" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/support.svg" class="woof-avatar woof-avatar-small woof-mb-3" alt=""></a>
                                        <h5 class="woof-h5"><a href="https://pluginus.net/support/forum/woof-woocommerce-products-filter/" class="woof-text-dark" target="_blank"><?php esc_html_e("Tickets", 'woocommerce-products-filter') ?></a></h5>
                                        <p><?php esc_html_e("If you have questions about plugin functionality or found bug write us please", 'woocommerce-products-filter') ?></p>
                                    </div>
                                </div>

                            </div>

                            <div class="woof-card-item">

                                <div class="woof-card woof-transition woof-text-center woof-rounded">
                                    <div class="woof-card-body">
                                        <h5 class="woof-h5"><a href="https://products-filter.com/faq" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/faq.svg" class="woof-avatar woof-avatar-small woof-mb-3" alt=""></a>
                                            <h5 class="woof-h5"><a href="https://products-filter.com/faq" class="woof-text-dark" target="_blank"><?php esc_html_e("FAQ", 'woocommerce-products-filter') ?></a></h5>
                                            <p><?php esc_html_e("If you have questions check please already prepared answers", 'woocommerce-products-filter') ?></p>
                                    </div>
                                </div>

                            </div>

                            <div class="woof-card-item">

                                <div class="woof-card woof-transition woof-text-center woof-rounded">
                                    <div class="woof-card-body">
                                        <a href="https://products-filter.com/codex" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/codex.svg" class="woof-avatar woof-avatar-small woof-mb-3" alt=""></a>
                                        <h5 class="woof-h5"><a href="https://products-filter.com/codex" class="woof-text-dark" target="_blank"><?php esc_html_e("Codex", 'woocommerce-products-filter') ?></a></h5>
                                        <p><?php esc_html_e("For developers HUSKY has power bunch of functionality", 'woocommerce-products-filter') ?></p>
                                    </div>
                                </div>

                            </div>

                            <div class="woof-card-item">

                                <div class="woof-card woof-transition woof-text-center woof-rounded">
                                    <div class="woof-card-body">
                                        <a href="https://products-filter.com/video/" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/video.svg" class="woof-avatar woof-avatar-small woof-mb-3" alt=""></a>
                                        <h5 class="woof-h5"><a href="https://products-filter.com/video/" class="woof-text-dark" target="_blank"><?php esc_html_e("Video", 'woocommerce-products-filter') ?></a></h5>
                                        <p><?php esc_html_e("For the beginners there is videos are prepared", 'woocommerce-products-filter') ?></p>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="woof-row">

                            <div class="woof-col-lg-6 woof-mt-4">

                                <div class="woof-d-flex woof-p-4 woof-shadow woof-align-items-center woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-info woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://www.youtube.com/embed/jZPtdWgAxKk" target="_blank"><?php esc_html_e("Quick Introduction", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>

                            </div>

                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-p-4 woof-shadow woof-align-items-center woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-heart woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://pluginus.net/support/forum/woof-woocommerce-products-filter/" target="_blank"><?php esc_html_e("HUSKY Support", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-align-items-center woof-p-4 woof-shadow woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-language woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://products-filter.com/translations" target="_blank"><?php esc_html_e("Translations", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>


                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-align-items-center woof-p-4 woof-shadow woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-video woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://currency-switcher.com/video-tutorials/" target="_blank"><?php esc_html_e("Video tutorials", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>


                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-align-items-center woof-p-4 woof-shadow woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-globe woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://demo.products-filter.com/" target="_blank"><?php esc_html_e("Demo site", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>


                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-align-items-center woof-p-4 woof-shadow woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-globe woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://demo10k.products-filter.com/" target="_blank"><?php esc_html_e("Demo site 10K", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>


                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-align-items-center woof-p-4 woof-shadow woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-globe woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://turbo.products-filter.com/" target="_blank"><?php esc_html_e("Demo site Turbo", 'woocommerce-products-filter') ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>



                            <div class="woof-col-lg-6 woof-mt-4">
                                <div class="woof-d-flex woof-align-items-center woof-p-4 woof-shadow woof-features woof-rounded">
                                    <div class="woof-icons woof-text-primary woof-text-center">
                                        <span class="icon-euro woof-d-block woof-rounded"></span>
                                    </div>
                                    <div class="woof-ms-4">
                                        <h5 class="woof-h5 woof-mb-1">
                                            <a class="woof-text-dark" href="https://products-filter.com/gdpr/" target="_blank">GDPR</a>
                                        </h5>
                                    </div>
                                </div>
                            </div>



                        </div>

                        <div class="woof__alert woof__alert-info2" role="alert">
                            <h5 class="woof__alert-heading"><?php esc_html_e("Some questions", 'woocommerce-products-filter') ?>:</h5>
                            <ul class="woof-list-unstyled woof-text-muted woof-border-top woof-mb-0 woof-pt-3">
                                <li><a href="https://products-filter.com/can-i-override-any-extension-view-file-for-my-site-needs" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("Can I override any extension view file for my site needs?", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/create-custom-taxonomies-plugin" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("How to create custom taxonomies for the products", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/how-to-filter-woocommerce-products-by-meta-data" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("How to filter woocommerce products by meta data", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/manipulate-css-when-search-going" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("How to manipulate by CSS if search is going", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/how-to-show-or-hide-widget-only-on-selected-site-pages" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("How to show or hide widget only on selected site pages", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/searching-is-slow" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("Searching is slow OR the plugin make page loading slow", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/text-attributes-does-not-works-in-woof-as-they-are-not-supported-by-woocommerce" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("Text attributes does not works in HUSKY as they are not supported by WooCommerce", 'woocommerce-products-filter') ?></a></li>
                                <li><a href="https://products-filter.com/how-to-make-woof-more-seo-friendly/" target="_blank" class="woof-text-decoration-underline"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woof-fea woof-icon-sm woof-me-2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg><?php esc_html_e("How to make HUSKY more SEO friendly", 'woocommerce-products-filter') ?></a></li>
                            </ul>
                        </div>
                        <br>
                        <div class="woof__section-title woof-mb-3">
                            <h5><?php esc_html_e("Recommended plugins for your site flexibility and features", 'woocommerce-products-filter') ?>:</h5>
                        </div>

                        <ul class="woof__features-gallery woof__col-6">
                            <li><a target="_blank" href="https://pluginus.net/affiliate/woocommerce-products-filter"><img class="woof-rounded" width="300" src="<?php echo esc_url(WOOF_LINK) ?>/img/plugin_options/banners/woof.png"></a></li>
                            <li><a target="_blank" href="https://pluginus.net/affiliate/woocommerce-bulk-editor"><img class="woof-rounded" width="300" src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/bear.png"></a></li>
                            <li><a target="_blank" href="https://codecanyon.pluginus.net/item/woot-woocommerce-products-tables/27928580"><img class="woof-rounded" width="300" src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/woot.png"></a></li>
                            <li><a target="_blank" href="https://codecanyon.pluginus.net/item/wordpress-posts-bulk-editor-professional/24376112" title="WPBE - WordPress Posts Bulk Editor Professional"><img class="woof-rounded" src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/wpbe.png" alt="WPBE - WordPress Posts Bulk Editor Professional" width="300"></a></li>
                        </ul>

                    </div>

                </section>

            </div>

            <div class="woof_fix19">
                <a href="https://pluginus.net/" target="_blank" class="woof_powered_by">Powered by PluginUs.NET</a>
            </div>

            <div class="clearfix clear"></div>

        </div>


    </section><!--/ .woof-section-->

    <div class="clearfix"></div>

    <div id="woof-modal-content" style="display: none;">

        <div class="woof_option_container woof_option_all">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Show title label', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Show/Hide taxonomy block title on the front', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">

                    <div class="select-wrap">
                        <select class="woof_popup_option" data-option="show_title_label">
                            <option value="0"><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                            <option value="1"><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
                        </select>
                    </div>

                </div>

            </div>

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Show toggle button', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Show toggle button near the title on the front above the block of html-items', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">

                    <div class="select-wrap">
                        <select class="woof_popup_option" data-option="show_toggle_button">
                            <option value="0"><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                            <option value="1"><?php esc_html_e('Yes, show as closed', 'woocommerce-products-filter') ?></option>
                            <option value="2"><?php esc_html_e('Yes, show as opened', 'woocommerce-products-filter') ?></option>
                        </select>
                    </div>

                </div>

            </div>
            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Tooltip', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Show tooltip', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">

                    <div class="select-wrap">
                        <textarea class="woof_popup_option" data-option="tooltip_text" ></textarea>
                    </div>

                </div>

            </div>

        </div>
        <div class="woof_option_container woof_option_checkbox woof_option_woof_sd_all woof_option_radio woof_option_color woof_option_label">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Not toggled terms count', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Enter count of terms which should be visible to make all other collapsible. "Show more" button will be appeared. This feature is works with: radio, checkboxes, labels, colors.', 'woocommerce-products-filter') ?></span>
                    <span><?php printf(__('Advanced info is <a href="%s" target="_blank">here</a>', 'woocommerce-products-filter'), 'https://products-filter.com/hook/woof_get_more_less_button_xxxx/') ?></span>
                </div>

                <div class="woof-form-element">
                    <input type="text" class="woof_popup_option regular-text code" data-option="not_toggled_terms_count" placeholder="<?php esc_html_e('leave it empty to show all terms', 'woocommerce-products-filter') ?>" value="0" />
                </div>

            </div>

        </div>

        <div class="woof_option_container woof_option_all">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Taxonomy custom label', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('For example you want to show title of Product Categories as "My Products". Just for your convenience.', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">
                    <input type="text" class="woof_popup_option regular-text code" data-option="custom_tax_label" placeholder="<?php esc_html_e('leave it empty to use native taxonomy name', 'woocommerce-products-filter') ?>" value="0" />
                </div>

            </div>

        </div>

        <div class="woof_option_container woof_option_radio woof_option_checkbox woof_option_label">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Max height of the block', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Container max-height (px). 0 means no max-height.', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">
                    <input type="text" class="woof_popup_option regular-text code" data-option="tax_block_height" placeholder="<?php esc_html_e('Max height of  the block', 'woocommerce-products-filter') ?>" value="0" />
                </div>

            </div>

        </div>

        <div class="woof_option_container woof_option_radio woof_option_checkbox">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Display items in a row', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Works for radio and checkboxes only. Allows show radio/checkboxes in 1 row!', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">

                    <div class="select-wrap">
                        <select class="woof_popup_option" data-option="dispay_in_row">
                            <option value="0"><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                            <option value="1"><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
                        </select>
                    </div>

                </div>

            </div>

        </div>

        <div class="woof_option_container  woof_option_all">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Sort terms', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('How to sort terms inside of filter block', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">

                    <div class="select-wrap">
                        <select class="woof_popup_option" data-option="orderby">
                            <option value="-1"><?php esc_html_e('Default', 'woocommerce-products-filter') ?></option>
                            <option value="id"><?php esc_html_e('Id', 'woocommerce-products-filter') ?></option>
                            <option value="name"><?php esc_html_e('Title', 'woocommerce-products-filter') ?></option>
                            <option value="numeric"><?php esc_html_e('Numeric.', 'woocommerce-products-filter') ?></option>

                        </select>
                    </div>

                </div>

            </div>

        </div>
        <div class="woof_option_container  woof_option_all">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Sort terms direction', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Direction of terms sorted inside of filter block', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">

                    <div class="select-wrap">
                        <select class="woof_popup_option" data-option="order">
                            <option value="ASC"><?php esc_html_e('ASC', 'woocommerce-products-filter') ?></option>
                            <option value="DESC"><?php esc_html_e('DESC', 'woocommerce-products-filter') ?></option>
                        </select>
                    </div>

                </div>

            </div>

        </div>
        <?php //  woof_option_checkbox woof_option_mselect woof_option_image woof_option_color woof_option_label woof_option_select_radio_check      ?>
        <div class="woof_option_container woof_option_all ">

            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Logic of filtering', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('AND or OR: if to select AND and on the site front select 2 terms - will be found products which contains both terms on the same time.', 'woocommerce-products-filter') ?></span>
                    <span><?php esc_html_e('If to select NOT IN will be found items which not has selected terms!! Means vice versa to the the concept of including: excluding', 'woocommerce-products-filter') ?></span>
                </div>
                <div class="woof-form-element">

                    <div class="select-wrap">
                        <select class="woof_popup_option" data-option="comparison_logic">
                            <option value="OR"><?php esc_html_e('OR', 'woocommerce-products-filter') ?></option>
                            <option class="woof_option_container woof_option_checkbox woof_option_woof_sd_all woof_option_mselect woof_option_image woof_option_color woof_option_label woof_option_select_radio_check" value="AND" style="display: none;"><?php esc_html_e('AND', 'woocommerce-products-filter') ?></option>
                            <option value="NOT IN"><?php esc_html_e('NOT IN', 'woocommerce-products-filter') ?></option>
                        </select>
                    </div>

                </div>

            </div>

        </div>
        <!------------- options for extensions ------------------------>

        <?php
        if (!empty(WOOF_EXT::$includes['taxonomy_type_objects'])) {
            foreach (WOOF_EXT::$includes['taxonomy_type_objects'] as $obj) {
                if (!empty($obj->taxonomy_type_additional_options)) {
                    foreach ($obj->taxonomy_type_additional_options as $key => $option) {
                        switch ($option['type']) {
                            case 'select':
                                ?>
                                <div class="woof_option_container woof_option_<?php echo esc_attr($obj->html_type) ?>">

                                    <div class="woof-form-element-container">

                                        <div class="woof-name-description">
                                            <strong><?php esc_html_e($option['title']) ?></strong>
                                            <span><?php esc_html_e($option['tip']) ?></span>
                                        </div>

                                        <div class="woof-form-element">

                                            <div class="select-wrap">
                                                <select class="woof_popup_option" data-option="<?php echo esc_attr($key) ?>">
                                                    <?php foreach ($option['options'] as $val => $title): ?>
                                                        <option value="<?php echo esc_attr($val) ?>"><?php esc_html_e($title) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <?php
                                break;

                            case 'text':
                                ?>
                                <div class="woof_option_container woof_option_<?php echo esc_attr($obj->html_type) ?>">

                                    <div class="woof-form-element-container">

                                        <div class="woof-name-description">
                                            <strong><?php esc_html_e($option['title']) ?></strong>
                                            <span><?php echo wp_kses_post($option['tip']) ?></span>
                                        </div>

                                        <div class="woof-form-element">
                                            <input type="text" class="woof_popup_option regular-text code" data-option="<?php echo esc_attr($key) ?>" placeholder="<?php echo esc_html(isset($option['placeholder']) ? $option['placeholder'] : '') ?>" value="" />
                                        </div>

                                    </div>

                                </div>
                                <?php
                                break;

                            case 'image':
                                ?>
                                <div class="woof_option_container woof_option_<?php echo esc_attr($obj->html_type) ?>">

                                    <div class="woof-form-element-container">

                                        <div class="woof-name-description">
                                            <strong><?php esc_html_e($option['title']) ?></strong>
                                            <span><?php esc_html_e($option['tip']) ?></span>
                                        </div>

                                        <div class="woof-form-element">
                                            <input type="text" class="woof_popup_option regular-text code" data-option="<?php echo esc_attr($key) ?>" placeholder="<?php echo esc_html($option['placeholder']) ?>" value="" />
                                            <a href="#" class="button woof_select_image"><?php esc_html_e('select image', 'woocommerce-products-filter') ?></a>
                                        </div>

                                    </div>

                                </div>
                                <?php
                                break;

                            default:
                                break;
                        }
                    }
                }
            }
        }
        ?>

    </div>

    <div id="woof_ext_tpl" style="display: none;">
        <li class="woof_ext_li is_disabled">

            <table class="woof_width_100p">
                <tbody>
                    <tr>
                        <td class="woof_valign_top">
                            <img alt="ext cover" src="<?php echo esc_url(WOOF_LINK) ?>img/woof_ext_cover.png" width="85">
                        </td>
                        <td><div class="woof_width_5px"></div></td>
                        <td class="woof_fix16">
                            <a href="#" class="woof_ext_remove" data-title="__TITLE__" data-idx="__IDX__" title="<?php esc_html_e('remove extension', 'woocommerce-products-filter') ?>"><img src="<?php echo esc_url($this->settings['delete_image']) ?>" alt="<?php esc_html_e('remove extension', 'woocommerce-products-filter') ?>" /></a>
                            <label for="__IDX__">
                                <input type="checkbox" name="__NAME__" value="__IDX__" id="__IDX__">
                                __TITLE__
                            </label><br>
                            ver.: __VERSION__<br><p class="description">__DESCRIPTION__</p>
                        </td>
                    </tr>
                </tbody>
            </table>

        </li>
    </div>

    <div id="woof-modal-content-by_price" style="display: none;">

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Show button', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Show button for woocommerce filter by price inside woof search form when it is dispayed as woo range-slider', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">

                <?php
                $show_button = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_button">
                        <?php foreach ($show_button as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Title text', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Text before the price filter range slider. Leave it empty if you not need it!', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="title_text" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Show toggle button', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Show toggle button near the title on the front above the block of html-items', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_toggle_button">
                        <option value="0"><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                        <option value="1"><?php esc_html_e('Yes, show as closed', 'woocommerce-products-filter') ?></option>
                        <option value="2"><?php esc_html_e('Yes, show as opened', 'woocommerce-products-filter') ?></option>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Tooltip', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Show tooltip', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">

                <div class="select-wrap">
                    <textarea class="woof_popup_option" data-option="tooltip_text" ></textarea>
                </div>

            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <h3><?php esc_html_e('Drop-down OR radio', 'woocommerce-products-filter') ?></h3>
                <strong><?php esc_html_e('Drop-down OR radio price filter ranges', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Ranges for price filter.', 'woocommerce-products-filter') ?></span>
                <span><?php esc_html_e('Example: 0-50,51-100,101-i. Where "i" is infinity.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="ranges" placeholder="" value="" />
            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Drop-down price filter text', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Drop-down price filter first option text', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="first_option_text" placeholder="" value="" />
            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <h3><?php esc_html_e('Ion Range slider', 'woocommerce-products-filter') ?></h3>
                <strong><?php esc_html_e('Step', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('predifined step', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="ion_slider_step" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Show price text inputs', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('This works with ionSlider only', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_text_input">
                        <option value="0"><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                        <option value="1"><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <h3><?php esc_html_e('Taxes', 'woocommerce-products-filter') ?></h3>
                <strong><?php esc_html_e('Tax', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('It will be counted in the filter( Only for ion-slider )', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="price_tax" placeholder="" value="" />
            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Slider skin', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Skin only works for ion slider', 'woocommerce-products-filter') ?></span>
            </div>
            <?php
            $skins = array(
                0 => esc_html__('Default', 'woocommerce-products-filter'),
                'round' => 'Round',
                'flat' => 'skinFlat',
                'big' => 'skinHTML5',
                'modern' => 'skinModern',
                'sharp' => 'Sharp',
                'square' => 'Square',
            );
            ?>
            <div class="woof-form-element">
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="price_slider_skin">
                        <?php foreach ($skins as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>

    </div>



    <div id="woof_buffer" style="display: none;"></div>

    <div id="woof_html_buffer" class="woof_info_popup" style="display: none;"></div>




</div>

<div class="clearfix"></div>


<svg xmlns="http://www.w3.org/2000/svg" hidden>
<symbol id="svg-woof" viewBox="0 0 487 512">

    <g>
    <path d="M433.564 329.127c-.446 17.167-33.615 29.09-20.112 46.152c29.805 4.185 16.249 23.939 32.578 30.662c-28.865 47.002-75.448 52.873-119.742 23.44c-12.886-7.684-27.396-10.94-39.291-7.736c-6.776 10.84 3.305 12.118 9.732 13.688c31.15 6.201 62.553 54.347 167.968 8.487c14.046-5.912 11.916-23.308 5.834-36.985c11.683-4.978 12.384-17.96 14.106-34.646c11.46-37.616-26.181-34.365-51.073-43.062zm-165.728-81.688c-12.854.288-31.777 12.464-44.28 18.524c17.643 10.378 24.651 22.727 44.28 18.87c19.636-3.856 19.278-16.5 11.553-31.189c-2.423-4.607-6.523-6.317-11.553-6.205zM350.783.014c-23.986 1.1-44.619 65.358-69.083 110.904c-70.332 29.251-87.93-8.677-110.895-2.439C131.69 28.753 73.613-51.179 57.92 43.822c3.47 71.804-25.19 77.72-22.655 180.173c0 0-51.119 102.887-30.263 193.927C19.697 482.073 71.373 506.284 106.082 512c3.592-26.24 44.78-85.41 33.515-99.217c-28.108-34.451-19.29-118.846 51.948-127.569c9.732-70.992 58.03-57.629 53.089-78.164c-15.482-64.332 51.32-34.302 62.531-13.222c20.144 33.427 34.308 66.281 50.774 81.853c20.941 19.805 49.347 30.623 51.01 31.247c-5.968-3.73-57.183-47.055-72.168-81.854c-9.012-20.93-8.106-68.08-8.106-68.08c25.985-4.065 36.469 39.655 64.69 79.32c1.168 2.857.962 5.144 0 7.05c-3.893-2.053-5.246-7.202-9.918-7.82c-4.254 0-7.703 6.036-7.703 13.479s5.614 10.1 13.347 13.58c10.781 18.973 21.986 25.945 32.767 30.699L387.07 191.65c-1.29-63.662 5.444-127.327-31.442-190.99a13.923 13.923 0 0 0-4.845-.646zm.333 20.297c1.927-.033 3.707 1.267 5.282 4.227c19.253 36.195 19.963 121.15 19.831 151.184c-9.143-20.808-22.843-55.803-66.035-66.473c0 0 25.231-88.665 40.922-88.938zM92.392 28.385c8.777-.037 18.752 5.106 30.324 26.207c18.515 33.762 31.801 56.222 38.25 67.847c13.201 23.796-5.78 4.98-11.722 66.035c-1.174 12.056-5.726 17.854-14.65 23.973c-19.315-19.306-32.938-52.81-42.343-92.643c-18.44 27.387 11.933 69.735 12.473 105.95c-20.488 4.683-33.772 3.045-41.352.518c-9.505-4.752-12.637-62.723 4.994-107.725C83.052 81.063 76.071 33.4 82.6 31.323c4.492-1.43 4.525-2.916 9.792-2.938z"/>
    </g>

</symbol>


<symbol id="svg-woof" viewBox="0 0 487 512">

    <g>
    <path d="M433.564 329.127c-.446 17.167-33.615 29.09-20.112 46.152c29.805 4.185 16.249 23.939 32.578 30.662c-28.865 47.002-75.448 52.873-119.742 23.44c-12.886-7.684-27.396-10.94-39.291-7.736c-6.776 10.84 3.305 12.118 9.732 13.688c31.15 6.201 62.553 54.347 167.968 8.487c14.046-5.912 11.916-23.308 5.834-36.985c11.683-4.978 12.384-17.96 14.106-34.646c11.46-37.616-26.181-34.365-51.073-43.062zm-165.728-81.688c-12.854.288-31.777 12.464-44.28 18.524c17.643 10.378 24.651 22.727 44.28 18.87c19.636-3.856 19.278-16.5 11.553-31.189c-2.423-4.607-6.523-6.317-11.553-6.205zM350.783.014c-23.986 1.1-44.619 65.358-69.083 110.904c-70.332 29.251-87.93-8.677-110.895-2.439C131.69 28.753 73.613-51.179 57.92 43.822c3.47 71.804-25.19 77.72-22.655 180.173c0 0-51.119 102.887-30.263 193.927C19.697 482.073 71.373 506.284 106.082 512c3.592-26.24 44.78-85.41 33.515-99.217c-28.108-34.451-19.29-118.846 51.948-127.569c9.732-70.992 58.03-57.629 53.089-78.164c-15.482-64.332 51.32-34.302 62.531-13.222c20.144 33.427 34.308 66.281 50.774 81.853c20.941 19.805 49.347 30.623 51.01 31.247c-5.968-3.73-57.183-47.055-72.168-81.854c-9.012-20.93-8.106-68.08-8.106-68.08c25.985-4.065 36.469 39.655 64.69 79.32c1.168 2.857.962 5.144 0 7.05c-3.893-2.053-5.246-7.202-9.918-7.82c-4.254 0-7.703 6.036-7.703 13.479s5.614 10.1 13.347 13.58c10.781 18.973 21.986 25.945 32.767 30.699L387.07 191.65c-1.29-63.662 5.444-127.327-31.442-190.99a13.923 13.923 0 0 0-4.845-.646zm.333 20.297c1.927-.033 3.707 1.267 5.282 4.227c19.253 36.195 19.963 121.15 19.831 151.184c-9.143-20.808-22.843-55.803-66.035-66.473c0 0 25.231-88.665 40.922-88.938zM92.392 28.385c8.777-.037 18.752 5.106 30.324 26.207c18.515 33.762 31.801 56.222 38.25 67.847c13.201 23.796-5.78 4.98-11.722 66.035c-1.174 12.056-5.726 17.854-14.65 23.973c-19.315-19.306-32.938-52.81-42.343-92.643c-18.44 27.387 11.933 69.735 12.473 105.95c-20.488 4.683-33.772 3.045-41.352.518c-9.505-4.752-12.637-62.723 4.994-107.725C83.052 81.063 76.071 33.4 82.6 31.323c4.492-1.43 4.525-2.916 9.792-2.938z"/>
    </g>

</symbol>

</svg>


<?php if ($this->show_notes): ?>

    <br>
    <div class="woof__alert woof__alert-success">
        <table class="woof_settings_promotion woof_width_100p">
            <tbody>
                <tr>
                    <td>
                        <h3 class="woof_tomato"><?php esc_html_e("HUSKY FULL VERSION", 'woocommerce-products-filter') ?>:</h3>
                        <a href="https://pluginus.net/affiliate/woocommerce-products-filter" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/woof.png" alt="<?php esc_html_e("full version of the plugin", 'woocommerce-products-filter'); ?>"></a>
                    </td>

                    <td>
                        <h3><?php esc_html_e("WooCommerce Bulk Editor", 'woocommerce-products-filter') ?>:</h3>
                        <a href="https://pluginus.net/affiliate/woocommerce-bulk-editor" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/bear.png" alt="<?php esc_html_e("WOOBE", 'woocommerce-products-filter'); ?>" /></a>
                    </td>

                    <td>
                        <h3><?php esc_html_e("WooCommerce Currency Swither", 'woocommerce-products-filter') ?>:</h3>
                        <a href="https://pluginus.net/affiliate/woocommerce-currency-switcher" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/woocs.png" alt="<?php esc_html_e("WOOCS", 'woocommerce-products-filter'); ?>" /></a>
                    </td>

                    <td>
                        <h3><?php esc_html_e("WooCommerce Products Tables", 'woocommerce-products-filter') ?>:</h3>
                        <a href="https://codecanyon.pluginus.net/item/woot-woocommerce-products-tables/27928580" target="_blank"><img src="<?php echo esc_url(WOOF_LINK) ?>img/plugin_options/banners/woot.png" alt="<?php esc_html_e("WOOT", 'woocommerce-products-filter'); ?>" /></a>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>


<?php endif; ?>

<?php

function woof_print_tax($key, $tax, $woof_settings) {
    ?>
    <li data-key="<?php echo esc_attr($key) ?>" class="woof_options_li">
        <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>
        <?php
        $opt_group = array(
            'standard' => array(
                'g_name' => esc_html__("Standard filter types", 'woocommerce-products-filter'),
                'types' => array()
            ),
            'advanced' => array(
                'g_name' => esc_html__("Advanced types", 'woocommerce-products-filter'),
                'types' => array()
            ),
            'sd' => array(
                'g_name' => esc_html__("Smart designer", 'woocommerce-products-filter'),
                'types' => array()
            ),
        );
        foreach (woof()->html_types as $type => $type_text) {
            if (in_array($type, array('radio', 'checkbox', 'select', 'mselect'))) {
                $opt_group['standard']['types'][$type] = $type_text;
            } elseif (stripos($type, 'woof_sd_') !== false) {
                $opt_group['sd']['types'][$type] = $type_text;
            } else {
                $opt_group['advanced']['types'][$type] = $type_text;
            }
        }
        ?>
        <div class="select-wrap">
            <select name="woof_settings[tax_type][<?php echo esc_attr($key) ?>]" class="woof_select_tax_type">
                <?php foreach ($opt_group as $html_types) { ?>
                    <?php
                    if (empty($html_types['types'])) {
                        continue;
                    }
                    ?>
                    <optgroup label='<?php echo esc_html($html_types['g_name']); ?>'>
                        <?php foreach ($html_types['types'] as $type => $type_text) : ?>
                            <option value="<?php echo esc_html($type) ?>" <?php if (isset($woof_settings['tax_type'][$key])) selected($woof_settings['tax_type'][$key], $type) ?>><?php esc_html_e($type_text) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>

        <span class="icon-question help_tip" data-tip="<?php esc_html_e('View of the taxonomies terms on the front', 'woocommerce-products-filter') ?>"></span>

        <?php
        $excluded_terms = '';
        if (isset($woof_settings['excluded_terms'][$key])) {
            $excluded_terms = $woof_settings['excluded_terms'][$key];
        }

        $excluded_terms_reverse = 0;
        if (isset($woof_settings['excluded_terms_reverse'][$key])) {
            $excluded_terms_reverse = $woof_settings['excluded_terms_reverse'][$key];
        }
        ?>

        <input type="text" class="woof_excluded_terms" name="woof_settings[excluded_terms][<?php echo esc_attr($key) ?>]" placeholder="<?php esc_html_e('excluded terms ids', 'woocommerce-products-filter') ?>" value="<?php echo esc_html($excluded_terms) ?>" />
        <?php
        $rev_id = uniqid('re-');
        $rev_checked = false;
        if (isset(woof()->settings['excluded_terms_reverse']) && is_array(woof()->settings['excluded_terms_reverse']) && in_array($key, (array) array_keys(woof()->settings['excluded_terms_reverse']))) {
            $rev_checked = true;
        }
        ?>
        <input <?php checked($rev_checked) ?> type="checkbox" name="woof_settings[excluded_terms_reverse][<?php echo esc_attr($key) ?>]" id="<?php echo esc_attr($rev_id) ?>" value="1" />
        <label class="woof_fix17" for="<?php echo esc_attr($rev_id) ?>"><?php esc_html_e('Reverse', 'woocommerce-products-filter') ?></label>


        <span class="icon-question help_tip" data-tip="<?php esc_html_e('If you want to exclude some current taxonomies terms from the search form! Use Reverse if you want include only instead of exclude! Example: 11,23,77', 'woocommerce-products-filter') ?>"></span>
        <a href="#" data-taxonomy="<?php echo esc_attr($key) ?>" data-taxonomy-name="<?php echo esc_html($tax->labels->name) ?>" class="woof-button js_woof_add_options help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>

        <div style="display: none;">
            <?php
            $max_height = 0;
            if (isset($woof_settings['tax_block_height'][$key])) {
                $max_height = $woof_settings['tax_block_height'][$key];
            }
            ?>
            <input type="text" name="woof_settings[tax_block_height][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($max_height) ?>" />
            <?php
            $show_title_label = 0;
            if (isset($woof_settings['show_title_label'][$key])) {
                $show_title_label = $woof_settings['show_title_label'][$key];
            }
            ?>
            <input type="text" name="woof_settings[show_title_label][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($show_title_label) ?>" />


            <?php
            $show_toggle_button = 0;
            if (isset($woof_settings['show_toggle_button'][$key])) {
                $show_toggle_button = $woof_settings['show_toggle_button'][$key];
            }
            ?>
            <input type="text" name="woof_settings[show_toggle_button][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_attr($show_toggle_button) ?>" />


            <?php
            $tooltip_text = "";
            if (isset($woof_settings['tooltip_text'][$key])) {
                $tooltip_text = stripcslashes($woof_settings['tooltip_text'][$key]);
            }
            ?>
            <input type="text" name="woof_settings[tooltip_text][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($tooltip_text) ?>" />

            <?php
            $dispay_in_row = 0;
            if (isset($woof_settings['dispay_in_row'][$key])) {
                $dispay_in_row = $woof_settings['dispay_in_row'][$key];
            }
            ?>
            <input type="text" name="woof_settings[dispay_in_row][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($dispay_in_row) ?>" />


            <?php
            $orderby = '-1';
            if (isset($woof_settings['orderby'][$key])) {
                $orderby = $woof_settings['orderby'][$key];
            }
            ?>
            <input type="text" name="woof_settings[orderby][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($orderby) ?>" />

            <?php
            $order = 'ASC';
            if (isset($woof_settings['order'][$key])) {
                $order = $woof_settings['order'][$key];
            }
            ?>
            <input type="text" name="woof_settings[order][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($order) ?>" />
            <?php
            $comparison_logic = 'OR';
            $logic_restriction = array('checkbox', 'mselect', 'label', 'color', 'image', 'slider', 'select_hierarchy');

            if (isset($woof_settings['comparison_logic'][$key])) {
                $comparison_logic = $woof_settings['comparison_logic'][$key];
            }
            if (isset($woof_settings['tax_type'][$key]) AND (!in_array($woof_settings['tax_type'][$key], $logic_restriction) AND stripos($woof_settings['tax_type'][$key], 'woof_sd_') === false ) AND $comparison_logic == 'AND') {
                $comparison_logic = 'OR';
            }

            if ($comparison_logic == 'NOT IN' AND $woof_settings['tax_type'][$key] == 'select_hierarchy') {
                $comparison_logic = 'OR';
            }
            ?>
            <input type="text" name="woof_settings[comparison_logic][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($comparison_logic) ?>" />

            <?php
            $custom_tax_label = '';
            if (isset($woof_settings['custom_tax_label'][$key])) {
                $custom_tax_label = stripcslashes($woof_settings['custom_tax_label'][$key]);
            }
            ?>
            <input type="text" name="woof_settings[custom_tax_label][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo esc_html($custom_tax_label) ?>" />


            <?php
            $not_toggled_terms_count = '';
            if (isset($woof_settings['not_toggled_terms_count'][$key])) {
                $not_toggled_terms_count = $woof_settings['not_toggled_terms_count'][$key];
            }
            ?>
            <input type="text" name="woof_settings[not_toggled_terms_count][<?php echo esc_attr($key) ?>]" placeholder="" value="<?php echo intval($not_toggled_terms_count) ?>" />


            <!------------- options for extensions ------------------------>
            <?php
            if (!empty(WOOF_EXT::$includes['taxonomy_type_objects'])) {
                foreach (WOOF_EXT::$includes['taxonomy_type_objects'] as $obj) {
                    if (!empty($obj->taxonomy_type_additional_options)) {
                        foreach ($obj->taxonomy_type_additional_options as $option_key => $option) {
                            $option_val = 0;
                            if (isset($woof_settings[$option_key][$key])) {
                                $option_val = $woof_settings[$option_key][$key];
                            }
                            ?>
                            <input type="text" name="woof_settings[<?php echo esc_attr($option_key) ?>][<?php echo esc_attr($key) ?>]" value="<?php echo esc_html($option_val) ?>" />
                            <?php
                        }
                    }
                }
            }
            ?>




        </div>

        <?php
        $tax_checked = false;
        if (isset(woof()->settings['tax']) && is_array(woof()->settings['tax']) && in_array($key, (array) array_keys(woof()->settings['tax']))) {
            $tax_checked = true;
        }
        ?>

        <input <?php checked($tax_checked) ?> type="checkbox" name="woof_settings[tax][<?php echo esc_attr($key) ?>]" id="tax_<?php echo esc_attr(md5($key)) ?>" value="1" />
        <label for="tax_<?php echo esc_attr(md5($key)) ?>"><b><?php esc_html_e($tax->labels->name) ?></b></label>
        <?php
        if (isset($woof_settings['tax_type'][$key])) {
            do_action('woof_print_tax_additional_options_' . $woof_settings['tax_type'][$key], $key);
        }
        ?>
    </li>
    <?php
}

//***

function woof_print_item_by_key($key, $woof_settings) {

    switch ($key) {
        case 'by_price':

            if (!isset($woof_settings[$key])) {
                $woof_settings[$key] = [];
            }

            if (!is_array($woof_settings)) {
                break;
            }
            ?>
            <li data-key="<?php echo esc_attr($key) ?>" class="woof_options_li">

                <?php
                $show = 0;
                if (isset($woof_settings[$key]['show'])) {
                    $show = $woof_settings[$key]['show'];
                }
                ?>

                <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>


                <strong class="woof_fix1"><?php esc_html_e("Search by Price", 'woocommerce-products-filter'); ?>:</strong>

                <span class="icon-question help_tip" data-tip="<?php esc_html_e('Show woocommerce filter by price inside woof search form', 'woocommerce-products-filter') ?>"></span>


                <div class="select-wrap">
                    <select name="woof_settings[<?php echo esc_attr($key) ?>][show]" class="woof_setting_select">
                        <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
                        <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('As woo range-slider', 'woocommerce-products-filter') ?></option>
                        <option value="2" <?php selected($show, 2) ?>><?php esc_html_e('As drop-down', 'woocommerce-products-filter') ?></option>
                        <option value="5" <?php selected($show, 5) ?>><?php esc_html_e('As radio button', 'woocommerce-products-filter') ?></option>
                        <option value="4" <?php selected($show, 4) ?>><?php esc_html_e('As textinputs', 'woocommerce-products-filter') ?></option>
                        <option value="3" <?php selected($show, 3) ?>><?php esc_html_e('As ion range-slider', 'woocommerce-products-filter') ?></option>
                    </select>
                </div>

                <a href="#" data-key="<?php echo esc_attr($key) ?>" data-name="<?php esc_html_e("Search by Price", 'woocommerce-products-filter'); ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>

                <?php
                if (!isset($woof_settings[$key]['show_button'])) {
                    $woof_settings[$key]['show_button'] = 0;
                }

                if (!isset($woof_settings[$key]['title_text'])) {
                    $woof_settings[$key]['title_text'] = '';
                }

                if (!isset($woof_settings[$key]['show_toggle_button'])) {
                    $woof_settings[$key]['show_toggle_button'] = 0;
                }
                if (!isset($woof_settings[$key]['ranges'])) {
                    $woof_settings[$key]['ranges'] = '';
                }

                if (!isset($woof_settings[$key]['first_option_text'])) {
                    $woof_settings[$key]['first_option_text'] = '';
                }

                if (!isset($woof_settings[$key]['ion_slider_step'])) {
                    $woof_settings[$key]['ion_slider_step'] = 0;
                }
                if (!isset($woof_settings[$key]['price_tax'])) {
                    $woof_settings[$key]['price_tax'] = 0;
                }
                if (!isset($woof_settings[$key]['show_text_input'])) {
                    $woof_settings[$key]['show_text_input'] = 0;
                }

                if (!isset($woof_settings[$key]['tooltip_text'])) {
                    $woof_settings[$key]['tooltip_text'] = "";
                }

                if (!isset($woof_settings[$key]['price_slider_skin'])) {
                    $woof_settings[$key]['price_slider_skin'] = 0;
                }
                ?>
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][tooltip_text]" placeholder="" value="<?php echo stripcslashes(esc_html($woof_settings[$key]['tooltip_text'])) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_button]" value="<?php echo intval($woof_settings[$key]['show_button']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][title_text]" value="<?php echo esc_html($woof_settings[$key]['title_text']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_toggle_button]" value="<?php echo esc_html($woof_settings[$key]['show_toggle_button']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][ranges]" value="<?php echo esc_html($woof_settings[$key]['ranges']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][first_option_text]" value="<?php echo esc_html($woof_settings[$key]['first_option_text']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][ion_slider_step]" value="<?php echo floatval($woof_settings[$key]['ion_slider_step']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][price_tax]" value="<?php echo esc_html($woof_settings[$key]['price_tax']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_text_input]" value="<?php echo intval($woof_settings[$key]['show_text_input']) ?>" />
                <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][price_slider_skin]" value="<?php echo esc_html($woof_settings[$key]['price_slider_skin']) ?>" />
            </li>
            <?php
            break;

        default:
            //options for extensions

            do_action('woof_print_html_type_options_' . $key);
            break;
    }
}
