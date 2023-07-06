<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-stat">

    <br>
    <div class="woof-section-title">
        <div class="col-title">

            <h4><?php esc_html_e('Statistic', 'woocommerce-products-filter') ?></h4>

        </div>
        <div class="col-button">

            <a href="https://products-filter.com/extencion/statistic/" target="_blank" class="button-primary"><span class="icon-info"></span></a><br />

        </div>
    </div>

    <div class="woof-tabs woof-tabs-style-line">

        <nav>
            <ul>
                <li>
                    <a href="#woof-stat-1">
                        <span class="icon-chart-bar-outline"></span>
                        <span><?php esc_html_e("Statistic", 'woocommerce-products-filter') ?></span>
                    </a>
                </li>
                <li>
                    <a href="#woof-stat-2">
                        <span class="icon-cog-outline"></span>
                        <span><?php esc_html_e("Options", 'woocommerce-products-filter') ?></span>
                    </a>
                </li>
            </ul>
        </nav>

        <?php global $wp_locale; ?>

        <div class="content-wrap">
            <section id="woof-stat-1">



                <?php if (!$updated_table): ?>
                    <div class="woof-control-section">

                        <h4 class="woof_orange"><?php esc_html_e('Notice:', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">

                            <p class="description">
                                <?php esc_html_e('Please update database: ', 'woocommerce-products-filter') ?>
                                <button id="woof_update_db"><?php esc_html_e('Update', 'woocommerce-products-filter') ?></button>
                            </p>
                        </div>
                    </div><!--/ .woof-control-section-->
                <?php endif; ?>



                <div class="woof-control-section">

                    <h4 class="woof_fix3"><?php esc_html_e('Select period:', 'woocommerce-products-filter') ?></h4>
                    <?php if (!empty($stat_min_date)): ?>
                        <div class="woof_fix4"><?php printf(__('(Statistic collected from: %s %d)', 'woocommerce-products-filter'), $wp_locale->get_month($stat_min_date[1]), $stat_min_date[0]) ?></div>
                    <?php endif; ?>
                    <br />

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <input type="hidden" id="woof_stat_calendar_from" value="0" />
                            <input type="text" readonly="readonly" class="woof_stat_calendar woof_stat_calendar_from" placeholder="<?php esc_html_e('From', 'woocommerce-products-filter') ?>" />
                            &nbsp;
                            <input type="hidden" id="woof_stat_calendar_to" value="0" />
                            <input type="text" readonly="readonly" class="woof_stat_calendar woof_stat_calendar_to" placeholder="<?php esc_html_e('To', 'woocommerce-products-filter') ?>" /><br />

                            <br />

                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('Select the time period for which you want to see statistical data', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->




                <div class="woof-control-section">

                    <h4 class="woof_fix5"><?php esc_html_e('Statistical parameters:', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">


                            <?php
                            //prices will be reviwed in the next versions of stat
                            $all_items = array();
                            //***
                            $taxonomies = $this->get_taxonomies();
                            if (!empty($taxonomies)) {
                                foreach ($taxonomies as $slug => $t) {
                                    $all_items[urldecode($slug)] = $t->labels->name;
                                }
                            }
                            if (class_exists('WOOF_META_FILTER') AND $updated_table AND isset(woof()->settings['meta_filter'])) {
                                $all_meta_items = array();
                                //***
                                
                                $meta_fields = woof()->settings['meta_filter'];
                                if (!empty($meta_fields)) {
                                    foreach ($meta_fields as $key => $meta) {
                                        if ($meta['meta_key'] == "__META_KEY__" OR $meta["search_view"] == 'textinput') {
                                            continue;
                                        }
                                        $slug = $meta["search_view"] . "_" . $meta['meta_key'];
                                        $all_meta_items[urldecode($slug)] = $meta['title'];
                                    }
                                    $all_items = array_merge($all_items, $all_meta_items);
                                }
                            }
                            asort($all_items);
                            //***

                            if (!isset($woof_settings['woof_stat']['items_for_stat']) OR empty($woof_settings['woof_stat']['items_for_stat'])) {
                                $woof_settings['woof_stat']['items_for_stat'] = array();
                            }
                            $items_for_stat = (array) $woof_settings['woof_stat']['items_for_stat'];
                            ?>


                            <?php if (!empty($items_for_stat)): ?>

                                <div class="select-wrap">
                                    <select id="woof_stat_snippet" multiple="" class="chosen_select">
                                        <?php foreach ($all_items as $key => $value) : ?>
                                            <?php
                                            if (!in_array($key, $items_for_stat)) {
                                                continue;
                                            }
                                            ?>
                                            <option value="<?php echo esc_attr($key); ?>"><?php esc_html_e($value); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="display:none;">
                                    <ul id="woof_stat_snippets_tags"></ul>
                                </div>
                            <?php else: ?>
                                <p class="description woof_red">
                                    <?php esc_html_e('Select taxonomies in tab Options and press "Save changes"', 'woocommerce-products-filter') ?>
                                </p>
                            <?php endif; ?>

                            <br />

                            <a href="javascript: woof_stat_calculate();" class="button button-primary button-large"><?php esc_html_e('Calculate Statistics', 'woocommerce-products-filter') ?></a><br />


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('Select taxonomy, taxonomies combinations OR leave this field empty to see general data for all the most requested taxonomies', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->


                <div class="woof-control-section">

                    <h4><?php esc_html_e('Graphics', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof_width_100p">

                            <ul id="woof_stat_get_monitor"></ul>

                            <div id="woof_stat_charts_list">
                                <!------------------------- inline styles must ne here as it hidden zone, gmap fix ------------->
                                <div id="chart_div_1" style="width: 100%; height: 600px;"></div>
                                <div id="chart_div_1_set" style="width: 100%; height: auto;"></div>
                            </div>



                        </div>

                    </div>
                </div><!--/ .woof-control-section-->


            </section>

            <section id="woof-stat-2">
                <div class="woof-control-section">

                    <h4><?php esc_html_e('Statistics collection:', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $stat_activated_mode = array(
                                0 => esc_html__('Disabled', 'woocommerce-products-filter'),
                                1 => esc_html__('Enabled', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_stat']['is_enabled'])) {
                                $woof_settings['woof_stat']['is_enabled'] = 0;
                            }
                            $is_enabled = $woof_settings['woof_stat']['is_enabled'];
                            if (!$is_enabled) {
                                ?>
                                <div class="error">
                                    <p class="description">
                                        <?php
                                        printf(__('Statistic extension is activated but statistics collection is not enabled. Enable it on: tab Statistic -> tab Options -> "Statistics collection enabled"', 'woocommerce-products-filter'));
                                        ?>
                                    </p>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_stat][is_enabled]" class="chosen_select">
                                    <?php foreach ($stat_activated_mode as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($is_enabled == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('After installing all settings for statistics assembling - enable it here', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h4><?php esc_html_e('Server options for statistic stock', 'woocommerce-products-filter') ?>:</h4>
                    <?php
                    if (!isset($woof_settings['woof_stat']['server_options']) OR empty($woof_settings['woof_stat']['server_options'])) {
                        $woof_settings['woof_stat']['server_options'] = array(
                            'host' => '',
                            'host_db_name' => '',
                            'host_user' => '',
                            'host_pass' => '',
                        );
                    }

                    $server_options = $woof_settings['woof_stat']['server_options'];

                    if ((empty($server_options['host']) OR empty($server_options['host_user']) OR empty($server_options['host_db_name']) OR empty($server_options['host_pass'])) AND $woof_settings['woof_stat']['is_enabled']) {
                        ?>
                        <div class="error">
                            <p class="description">
                                <?php
                                printf(__('Statistic -> tab Options -> "Stat server options" inputs should be filled in by right data, another way not possible to collect statistical data!', 'woocommerce-products-filter'));
                                ?>
                            </p>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <label class="woof_fix6"><?php esc_html_e('Host', 'woocommerce-products-filter') ?></label>:
                            <input type="text" name="woof_settings[woof_stat][server_options][host]" value="<?php echo esc_html($server_options['host']) ?>" /><br />
                            <br />
                            <label class="woof_fix6"><?php esc_html_e('User', 'woocommerce-products-filter') ?></label>:
                            <input type="text" name="woof_settings[woof_stat][server_options][host_user]" value="<?php echo esc_html($server_options['host_user']) ?>" /><br />
                            <br />
                            <label class="woof_fix6"><?php esc_html_e('DB Name', 'woocommerce-products-filter') ?></label>:
                            <input type="text" name="woof_settings[woof_stat][server_options][host_db_name]" value="<?php echo esc_html($server_options['host_db_name']) ?>" /><br />
                            <br />
                            <label class="woof_fix6"><?php esc_html_e('Password', 'woocommerce-products-filter') ?></label>:
                            <input type="text" name="woof_settings[woof_stat][server_options][host_pass]" value="<?php echo esc_html($server_options['host_pass']) ?>" /><br />
                            <span id="woof_stat_connection"  class="button"><?php esc_html_e('Check DB connection', 'woocommerce-products-filter') ?></span>

                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('This data is very important for assembling statistics data, so please fill fields very responsibly. To collect statistical data uses a separate MySQL table.', 'woocommerce-products-filter') ?><br />
                            </p>


                        </div>
                    </div>

                </div><!--/ .woof-control-section-->



                <div class="woof-control-section">

                    <h4><?php esc_html_e('Statistic for:', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $all_items = [];
//***
                            $taxonomies = $this->get_taxonomies();
                            if (!empty($taxonomies)) {
                                foreach ($taxonomies as $slug => $t) {
                                    $all_items[urldecode($slug)] = $t->labels->name;
                                }
                            }
                            if (class_exists('WOOF_META_FILTER') AND $updated_table AND isset(woof()->settings['meta_filter'])) {
                                $all_meta_items = array();
                                //***
                                
                                $meta_fields = woof()->settings['meta_filter'];
                                if (!empty($meta_fields)) {
                                    foreach ($meta_fields as $key => $meta) {
                                        if ($meta['meta_key'] == "__META_KEY__" OR $meta["search_view"] == 'textinput') {
                                            continue;
                                        }
                                        $slug = $meta["search_view"] . "_" . $meta['meta_key'];
                                        $all_meta_items[urldecode($slug)] = $meta['title'];
                                    }
                                    $all_items = array_merge($all_items, $all_meta_items);
                                }
                            }
                            asort($all_items);
//***

                            if (!isset($woof_settings['woof_stat']['items_for_stat']) OR empty($woof_settings['woof_stat']['items_for_stat'])) {
                                $woof_settings['woof_stat']['items_for_stat'] = array();
                            }
                            $items_for_stat = (array) $woof_settings['woof_stat']['items_for_stat'];
                            ?>

                            <div class="select-wrap">
                                <select multiple="" name="woof_settings[woof_stat][items_for_stat][]" class="chosen_select">
                                    <?php foreach ($all_items as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php selected(in_array($key, $items_for_stat)) ?>><?php esc_html_e($value); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('Select taxonomies and meta keys which you want to track', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->





                <div class="woof-control-section">

                    <h4><?php esc_html_e('Max requests per unique user', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_stat']['user_max_requests']) OR empty($woof_settings['woof_stat']['user_max_requests'])) {
                        $woof_settings['woof_stat']['user_max_requests'] = 10;
                    }
                    $user_max_requests = intval($woof_settings['woof_stat']['user_max_requests']);
                    if ($user_max_requests <= 0) {
                        $user_max_requests = 10;
                    }
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_stat][user_max_requests]" value="<?php echo intval($user_max_requests) ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('How many search requests will be catched and written down into the statistical mySQL table per 1 unique user before cron will assemble the data', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->



                <div class="woof-control-section">

                    <h4><?php esc_html_e('Max deep of the search request', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_stat']['request_max_deep']) OR empty($woof_settings['woof_stat']['request_max_deep'])) {
                        $woof_settings['woof_stat']['request_max_deep'] = 5;
                    }
                    $request_max_deep = intval($woof_settings['woof_stat']['request_max_deep']);
                    if ($request_max_deep <= 0) {
                        $request_max_deep = 5;
                    }
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_stat][request_max_deep]" value="<?php echo intval($request_max_deep) ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('How many taxonomies per one search request will be written down into the statistical mySQL table for 1 unique user. The excess data will be truncated! Number 5 is recommended. More depth - more space in the DataBase will be occupied by the data', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->







                <div class="woof-control-section" style="display: none;">

                    <h4><?php esc_html_e('Cache folder', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_stat']['cache_folder']) OR empty($woof_settings['woof_stat']['cache_folder'])) {
                        $woof_settings['woof_stat']['cache_folder'] = '_woof_stat_cache';
                    }
                    $cache_folder = sanitize_title($woof_settings['woof_stat']['cache_folder']);
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_stat][cache_folder]" value="<?php echo esc_html($cache_folder) ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php echo esc_html(WP_CONTENT_DIR . '/' . $cache_folder) ?>/<br />
                                <?php esc_html_e('Select cron which you want to use for the statistic assembling. Better use WordPress cron, but on the server create external cron and set there period of site visiting.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->


                <div class="woof-control-section">

                    <h4><?php esc_html_e('How to assemble statistic', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $cron_systems = array(
                                0 => esc_html__('WordPress Cron', 'woocommerce-products-filter'),
                            );

                            if (!isset($woof_settings['woof_stat']['cron_system'])) {
                                $woof_settings['woof_stat']['cron_system'] = 0;
                            }
                            $cron_system = $woof_settings['woof_stat']['cron_system'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_stat][cron_system]" class="chosen_select woof_cron_system">
                                    <?php foreach ($cron_systems as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($cron_system == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('Use WordPress Cron if your site has a lot of traffic, and external cron if the site traffic is not big. External cron is more predictable with time of execution, but additional knowledge how to set it correctly is required', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->


                <div class="woof-control-section woof_external_cron_option" style="display: <?php echo esc_attr($cron_system == 1 ? 'block' : 'none') ?>;">

                    <h4><?php esc_html_e('Secret key for external cron', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_stat']['cron_secret_key']) OR empty($woof_settings['woof_stat']['cron_secret_key'])) {
                        $woof_settings['woof_stat']['cron_secret_key'] = 'woof_stat_updating';
                    }
                    $cron_secret_key = sanitize_title($woof_settings['woof_stat']['cron_secret_key']);
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_stat][cron_secret_key]" value="<?php echo esc_html($cron_secret_key) ?>" />
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
                                'hourly' => esc_html__('hourly', 'woocommerce-products-filter'),
                                'twicedaily' => esc_html__('twicedaily', 'woocommerce-products-filter'),
                                'daily' => esc_html__('daily', 'woocommerce-products-filter'),
                                'week' => esc_html__('weekly', 'woocommerce-products-filter'),
                                'month' => esc_html__('monthly', 'woocommerce-products-filter'),
                                'min1' => esc_html__('min1', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_stat']['wp_cron_period'])) {
                                $woof_settings['woof_stat']['wp_cron_period'] = 'daily';
                            }
                            $wp_cron_period = $woof_settings['woof_stat']['wp_cron_period'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_stat][wp_cron_period]" class="chosen_select">
                                    <?php foreach ($wp_cron_periods as $key => $value) : ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($wp_cron_period == $key) ?>><?php esc_html_e($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php esc_html_e('12 hours recommended', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->



                <div class="woof-control-section">

                    <h4><?php esc_html_e('Max terms or taxonomies per graph', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_stat']['max_items_per_graph']) OR empty($woof_settings['woof_stat']['max_items_per_graph'])) {
                        $woof_settings['woof_stat']['max_items_per_graph'] = 10;
                    }
                    $max_items_per_graph = intval($woof_settings['woof_stat']['max_items_per_graph']);
                    if ($max_items_per_graph <= 0) {
                        $max_items_per_graph = 10;
                    }
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_stat][max_items_per_graph]" value="<?php echo intval($max_items_per_graph) ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('How many taxonomies and terms to show on the graphs. Use no more than 10 to understand situation with statistical data', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->





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
                $sql = "CREATE TABLE IF NOT EXISTS `{$table_stat_buffer}` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `hash` text COLLATE utf8_unicode_ci NOT NULL,
                        `user_ip` text COLLATE utf8_unicode_ci NOT NULL,
                        `taxonomy` text COLLATE utf8_unicode_ci NOT NULL,
                        `value` int(11) NOT NULL,
                        `meta_value` text COLLATE utf8_unicode_ci NOT NULL,
                        `page` text COLLATE utf8_unicode_ci NOT NULL,
                        `tax_page_term_id` int(11) NOT NULL DEFAULT '0',
                        `time` int(11) NOT NULL,
                        PRIMARY KEY (`id`)
                      )  {$charset_collate};";

                if ($wpdb->query($sql) === false) {
                    ?>
                    <p class="description"><?php esc_html_e("HUSKY cannot create database table for statistic! Make sure that your mysql user has the CREATE privilege! Do it manually using your host panel&amp;phpmyadmin!", 'woocommerce-products-filter') ?></p>
                    <code><?php echo esc_html($sql) ?></code>
                    <?php
                    esc_html_e($wpdb->last_error);
                }

                //***
                $sql = "CREATE TABLE IF NOT EXISTS `{$table_stat_tmp}` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `user_ip` text COLLATE utf8_unicode_ci NOT NULL,
                        `page` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'shop',
                        `request` text COLLATE utf8_unicode_ci NOT NULL,
                        `hash` text COLLATE utf8_unicode_ci NOT NULL,
                        `tax_page` text COLLATE utf8_unicode_ci NOT NULL,
                        `tax_page_term_id` int(11) NOT NULL,
                        `time` int(11) NOT NULL,
                        `is_collected` int(1) NOT NULL DEFAULT '0',
                        PRIMARY KEY (`id`)
                      )  {$charset_collate};";

                if ($wpdb->query($sql) === false) {
                    ?>
                    <p class="description"><?php esc_html_e("HUSKY cannot create database table for statistic! Make sure that your mysql user has the CREATE privilege! Do it manually using your host panel&amp;phpmyadmin!", 'woocommerce-products-filter') ?></p>
                    <code><?php echo esc_html($sql) ?></code>
                    <?php
                    esc_html_e($wpdb->last_error);
                }
                ?>



            </section>

        </div>

    </div>
</section>


