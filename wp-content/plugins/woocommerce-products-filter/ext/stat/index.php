<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_STAT extends WOOF_EXT {

    private $table_stat_buffer = 'woof_stat_buffer';
    private $table_stat_tmp = 'woof_stat_tmp';
    public $type = 'application';
    public $folder_name = 'stat'; //should be defined!!
    //***
    public $is_enabled = false;
    public $cache_folder = '';
    public $items_for_stat = array();
    public $user_max_requests = 10;
    public $request_max_deep = 5;
    public $pdo = NULL; //for stat asembling
    public $cron_system = 0; //wp by default
    public $cron = NULL;
    public $wp_cron_period = 'daily';
    public $max_items_per_graph = 10;
    public $updated_table = false;
    public $meta_keys = array();

    //***

    public function __construct() {
        parent::__construct();

        global $wpdb;
        $this->table_stat_buffer = $wpdb->prefix . $this->table_stat_buffer;
        $this->table_stat_tmp = $wpdb->prefix . $this->table_stat_tmp;
        //***
        if (isset($this->woof_settings['woof_stat']['is_enabled'])) {
            $this->is_enabled = (bool) $this->woof_settings['woof_stat']['is_enabled'];
        }
        //****
        $this->updated_table = get_option('woof_stat_updated_table', false);

        if (!$this->updated_table) {
            global $wpdb;
            $table_name = $this->table_stat_tmp;
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                update_option('woof_stat_updated_table', true);
            }
        }
        //***
        $cache_folder = '_woof_stat_cache';
        if (isset($this->woof_settings['woof_stat']['cache_folder']) AND!empty($this->woof_settings['woof_stat']['cache_folder'])) {
            $cache_folder = sanitize_title($this->woof_settings['woof_stat']['cache_folder']);
        }
        //***
        if (isset($this->woof_settings['woof_stat']['items_for_stat']) AND!empty($this->woof_settings['woof_stat']['items_for_stat'])) {
            $this->items_for_stat = (array) $this->woof_settings['woof_stat']['items_for_stat'];
        }
        //***
        if (isset($this->woof_settings['woof_stat']['user_max_requests']) AND!empty($this->woof_settings['woof_stat']['user_max_requests'])) {
            $this->user_max_requests = intval($this->woof_settings['woof_stat']['user_max_requests']);
            if ($this->user_max_requests <= 0) {
                $this->user_max_requests = 10;
            }
        }
        //***
        if (isset($this->woof_settings['woof_stat']['request_max_deep']) AND!empty($this->woof_settings['woof_stat']['request_max_deep'])) {
            $this->request_max_deep = intval($this->woof_settings['woof_stat']['request_max_deep']);
            if ($this->request_max_deep <= 0) {
                $this->request_max_deep = 5;
            }
        }
        //***
        if (isset($this->woof_settings['woof_stat']['cron_system']) AND!empty($this->woof_settings['woof_stat']['cron_system'])) {
            $this->cron_system = intval($this->woof_settings['woof_stat']['cron_system']);
        }
        //***
        if (isset($this->woof_settings['woof_stat']['wp_cron_period']) AND!empty($this->woof_settings['woof_stat']['wp_cron_period'])) {
            $this->wp_cron_period = $this->woof_settings['woof_stat']['wp_cron_period'];
        }
        //***
        if (isset($this->woof_settings['woof_stat']['max_items_per_graph']) AND!empty($this->woof_settings['woof_stat']['max_items_per_graph'])) {
            $max_items_per_graph = (int) $this->woof_settings['woof_stat']['max_items_per_graph'];
            if ($max_items_per_graph > 0) {
                $this->max_items_per_graph = $max_items_per_graph;
            }
        }
        //***
        $this->cache_folder = WP_CONTENT_DIR . '/' . $cache_folder . '/';
        add_filter('woof_get_request_data', array($this, 'woof_get_request_data'));
        $this->meta_keys = $this->get_meta_keys();
        //***
        $this->init();
        //***
        if ($this->is_enabled) {
            $this->cron = new PN_WP_CRON_WOOF('woof_stat_cron');
            $this->init_pdo();
            //***
            if ($this->cron_system === 1) {
                $this->woof_stat_wpcron_init(true);
                if (isset($_GET['woof_stat_collection'])) {
                    $cron_secret_key = 'woof_stat_updating';
                    if (isset($this->woof_settings['woof_stat']['cron_secret_key']) AND!empty($this->woof_settings['woof_stat']['cron_secret_key'])) {
                        $cron_secret_key = sanitize_title($this->woof_settings['woof_stat']['cron_secret_key']);
                    }
                    if ($_GET['woof_stat_collection'] === $cron_secret_key) {
                        $this->assemble_stat();
                        die('woof stat assemble done!');
                    }
                }
            } else {
                add_action('woof_stat_wpcron', array($this, 'assemble_stat'), 10);
                $this->woof_stat_wpcron_init();
            }
        }

        //***
        $this->get_stat_tables();
        add_action('wp_ajax_woof_write_stat', array($this, 'woof_write_stat'));
        add_action('wp_ajax_nopriv_woof_write_stat', array($this, 'woof_write_stat'));
        add_action('wp_ajax_woof_get_operative_tables', array($this, 'get_operative_tables'));
        add_action('wp_ajax_woof_get_stat_data', array($this, 'woof_get_stat_data'));
        add_action('wp_ajax_woof_get_top_terms', array($this, 'woof_get_top_terms'));
        add_action('wp_ajax_woof_stat_check_connection', array($this, 'woof_stat_check_connection'));
        add_action('wp_ajax_woof_stat_update_db', array($this, 'woof_stat_update_db'));
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->folder_name . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {
        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);
        self::$includes['js']['woof_stat_html_items'] = $this->get_ext_link() . 'js/stat.js';
    }

    //ajax
    public function woof_stat_check_connection() {
        $pdo_options = array();
        $pdo_options['host'] = sanitize_text_field(WOOF_REQUEST::get('woof_stat_host'));
        $pdo_options['host_db_name'] = sanitize_text_field(WOOF_REQUEST::get('woof_stat_name'));
        $pdo_options['host_user'] = sanitize_text_field(WOOF_REQUEST::get('woof_stat_user'));
        $pdo_options['host_pass'] = sanitize_text_field(WOOF_REQUEST::get('woof_stat_pswd'));

        try {
            $this->pdo = new PDO("mysql:host={$pdo_options['host']};dbname={$pdo_options['host_db_name']}", $pdo_options['host_user'], $pdo_options['host_pass']);
        } catch (PDOException $e) {
            die(esc_html__("Database not connected!    ERROR! ", 'woocommerce-products-filter'));
        }
        die(esc_html__("Database successfully connected!!!", 'woocommerce-products-filter'));
    }

    public function woof_stat_update_db() {
        global $wpdb;
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $this->table_stat_buffer . "' AND column_name = 'meta_value'");
        if (empty($row)) {
            $result = $wpdb->query("ALTER TABLE " . $this->table_stat_buffer . " ADD meta_value text COLLATE utf8_unicode_ci NOT NULL");
            if ($result === false) {
                die(esc_html__("Somthing wrong!", 'woocommerce-products-filter'));
            }
        }
        update_option('woof_stat_updated_table', true);
        die(esc_html__("Database successfully updated!!!", 'woocommerce-products-filter'));
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-stat">
                <span class="icon-chart-bar"></span>
                <span><?php esc_html_e("Statistic", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {

        //woof_stat_calendar_date_format, woof_stat_week_first_day
        wp_enqueue_script('woof_google_charts', 'https://www.gstatic.com/charts/loader.js');
        wp_enqueue_script('jquery-ui-core');
        //***

        $data = array();
        $data['stat_min_date'] = $this->get_stat_min_date_db();
        //***
        wp_register_script('woof_stat', $this->get_ext_link() . 'js/admin.js');
        wp_enqueue_style('woof_stat-css', $this->get_ext_link() . 'css/stat.css', array(), WOOF_VERSION);
        $localize_script = array(
            'calendar_date_format' => 'DD, d MM, yy',
            'week_first_day' => get_option('start_of_week'),
            'max_items_per_graph' => $this->max_items_per_graph,
            'woof_stat_leave_empty' => esc_html__('leave it empty to see all terms', 'woocommerce-products-filter'),
            'woof_stat_sel_date_range' => esc_html__('Select date range for statistic!', 'woocommerce-products-filter'),
            'woof_stat_calc' => esc_html__('Statistic calculation ...', 'woocommerce-products-filter'),
            'woof_stat_get_oper_tbls' => esc_html__('getting of operative tables ...', 'woocommerce-products-filter'),
            'woof_stat_oper_tbls_prep' => esc_html__('operative tables are prepared', 'woocommerce-products-filter'),
            'woof_stat_getting_dftbls' => esc_html__('getting the data from the table', 'woocommerce-products-filter'),
            'woof_stat_done' => esc_html__('done!', 'woocommerce-products-filter'),
            'woof_stat_no_data' => esc_html__('No data for the selected time period!', 'woocommerce-products-filter'),
            'woof_stat_google' => esc_html__('Google charts library not loaded! If site is on localhost just disable statistic extension in tab Extensions!', 'woocommerce-products-filter'),
            'woof_stat_graphs' => esc_html__('drawing graphs ...', 'woocommerce-products-filter'),
            'woof_stat_finished' => esc_html__('finished!', 'woocommerce-products-filter'),
            'woof_stat_troubles' => esc_html__('Looks like troubles with JavaScript!', 'woocommerce-products-filter')
        );

        if (isset($data['stat_min_date'][0])) {
            $localize_script['min_year'] = $data['stat_min_date'][0];
            $localize_script['min_month'] = $data['stat_min_date'][1];
        } else {
            $localize_script['min_year'] = date('Y');
            $localize_script['min_month'] = date('n');
        }

        wp_localize_script('woof_stat', 'woof_stat_vars', $localize_script);
        wp_enqueue_script('woof_stat', array('jquery', 'jquery-ui-core'), [], WOOF_VERSION);

        //***
        if (!extension_loaded('pdo_mysql')) {
            ?>
            <div class="error">
                <p class="description">
                    <?php
                    printf(__('PHP extension PDO_MYSQL is not enabled on your server, not possible to collect statistic data! Contact your hosting support to about enabling PDO_MYSQL.', 'woocommerce-products-filter'));
                    ?>
                </p>
            </div>
            <?php
        }

        $min_memory_mb = 268435456;
        $memory = wc_let_to_num(WP_MEMORY_LIMIT);
        if (function_exists('memory_get_usage')) {
            $system_memory = wc_let_to_num(@ini_get('memory_limit'));
            $memory = max($memory, $system_memory);
        }

        if (version_compare($memory, $min_memory_mb, '<')) {
            ?>
            <div class="error">
                <p class="description">
                    <?php
                    printf(__('Very recommend for the statistic not less than %s of the memory to avoid malfunctionality. Now is on the site %s', 'woocommerce-products-filter'), size_format($min_memory_mb), size_format($memory));
                    ?>
                </p>
            </div>
            <?php
        }
        //***

        $data['table_stat_buffer'] = $this->table_stat_buffer;
        $data['table_stat_tmp'] = $this->table_stat_tmp;
        $data['folder_name'] = $this->folder_name;
        $data['woof_settings'] = $this->woof_settings;
        $data['updated_table'] = $this->updated_table;

        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function init_pdo() {
        if (isset($this->woof_settings['woof_stat']['server_options'])) {
            if (extension_loaded('pdo_mysql')) {
                $pdo_options = $this->woof_settings['woof_stat']['server_options'];
                if (!empty($pdo_options['host']) AND!empty($pdo_options['host_db_name']) AND!empty($pdo_options['host_user']) AND!empty($pdo_options['host_pass'])) {
                    try {
                        $this->pdo = new PDO("mysql:host={$pdo_options['host']};dbname={$pdo_options['host_db_name']}", $pdo_options['host_user'], $pdo_options['host_pass']);
                    } catch (Exception $e) {
                        ?>
                        <div class="error">
                            <p class="description">
                                <?php
                                printf(__('Wrong data for "Server options for statistic stock" options', 'woocommerce-products-filter'));
                                ?>
                            </p>
                        </div>
                        <?php
                    }
                }
            }
        }
    }

    public function get_woof_cron_schedules($key = '') {
        $schedules = array(
            'hourly' => HOUR_IN_SECONDS,
            'twicedaily' => HOUR_IN_SECONDS * 12,
            'daily' => DAY_IN_SECONDS,
            'week' => WEEK_IN_SECONDS,
            'month' => WEEK_IN_SECONDS * 4,
            'min1' => MINUTE_IN_SECONDS,
        );

        if (!empty($key)) {
            return $schedules[$key];
        }

        return $schedules;
    }

    public function woof_stat_wpcron_init($reset = false) {
        $hook = 'woof_stat_wpcron';

        if ($reset) {
            $this->cron->remove($hook);
            return;
        }

        if ($this->cron_system === 0) {//wp cron
            if (!$this->cron->is_attached($hook, $this->get_woof_cron_schedules($this->wp_cron_period))) {
                $this->cron->attach($hook, time(), $this->get_woof_cron_schedules($this->wp_cron_period));
            }

            $this->cron->process();
        }
    }

    //ajax
    public function woof_write_stat() {
        WOOF_REQUEST::set('woof_products_doing', 1);
        $_GET = WOOF_REQUEST::get('woof_products_doing');
        if ($this->is_enabled AND!is_null($this->pdo)) {
            $this->woof_get_request_data(WOOF_REQUEST::get('woof_current_values'));
            $this->cron->process();
        } else {
            esc_html_e('stat is not activated or PDO not inited', 'woocommerce-products-filter');
        }
        exit;
    }

    //ajax
    public function get_operative_tables() {
        if (current_user_can('create_users')) {
            $calendar_from = intval(WOOF_REQUEST::get('calendar_from'));
            $calendar_from = mktime(0, 0, 0, date('n', $calendar_from), date('d', $calendar_from), date('y', $calendar_from));
            $calendar_to = intval(WOOF_REQUEST::get('calendar_to'));
            $calendar_to = mktime(23, 59, 59, date('n', $calendar_to), date('d', $calendar_to), date('y', $calendar_to));

            //+++

            $tables = $this->get_stat_tables();
            $request_tables = array();
            $start_year = date('Y', $calendar_from);
            $start_month = date('n', $calendar_from);
            $finish_year = date('Y', $calendar_to);
            $finish_month = date('n', $calendar_to);
            //***
            $current_y = $start_year;
            $current_m = $start_month;
            while (true) {
                $t = $current_y . '_' . $current_m;
                if (in_array($t, $tables)) {
                    $request_tables[] = $t;
                }

                if ($current_y >= $finish_year AND $current_m >= $finish_month) {
                    break;
                }

                $current_m++;
                if ($current_m > 12) {
                    $current_m = 1;
                    $current_y++;
                }
            }

            die(json_encode($request_tables));
        }

        die('not permitted');
    }

    //ajax
    public function woof_get_stat_data() {
        if (current_user_can('create_users')) {
            $table = sanitize_text_field(WOOF_REQUEST::get('table'));
            $calendar_from = intval(WOOF_REQUEST::get('calendar_from'));
            $calendar_from = mktime(0, 0, 0, date('n', $calendar_from), date('d', $calendar_from), date('y', $calendar_from));
            $calendar_to = intval(WOOF_REQUEST::get('calendar_to'));
            $calendar_to = mktime(23, 59, 59, date('n', $calendar_to), date('d', $calendar_to), date('y', $calendar_to));

            $taxonomies = array();
            foreach (woof()->get_taxonomies() as $slug => $t) {
                //missing taxonomies which are not selected in the stat options
                if (!in_array(urldecode($slug), $this->items_for_stat)) {
                    continue;
                }

                //+++

                $custom_name = '';
                if (isset(woof()->settings['custom_tax_label'][$slug])) {
                    $custom_name = WOOF_HELPER::wpml_translate(null, woof()->settings['custom_tax_label'][$slug], 0);
                }

                if (!empty($custom_name)) {
                    $taxonomies[urldecode($slug)] = $custom_name;
                } else {
                    $taxonomies[urldecode($slug)] = $t->labels->name;
                }
            }




            $search_template = (array) WOOF_REQUEST::get('request_snippets');
            $tax_array = array_keys($search_template);

            if (!empty($search_template)) {
                //this for under dev feature for working with dedicated terms, now not possible to set any terms for each taxonomy
                foreach ($search_template as &$value) {
                    $value = explode(',', $value);
                    if (count($value) == 1 AND empty($value[0])) {
                        $value = array();
                    }
                }
            }

            //+++
            if (!is_null($this->pdo)) {
                $sql = "SELECT taxonomy,value FROM {$table} WHERE time>=:calendar_from AND time<=:calendar_to";
                //+++
                if (!empty($tax_array)) {
                    $sql = "SELECT hash,taxonomy,value FROM {$table} WHERE time>=:calendar_from AND time<=:calendar_to";
                    $sql_tale = " AND (";
                    foreach ($tax_array as $k => $tax_slug) {
                        if ($k > 0) {
                            $sql_tale .= " OR ";
                        }

                        $sql_tale .= "taxonomy='" . $tax_slug . "'";
                    }
                    $sql_tale .= ')';
                    $sql .= $sql_tale;
                }
                //+++
                $sth = $this->pdo->prepare($sql);
                $sth->bindParam(':calendar_from', $calendar_from, PDO::PARAM_INT);
                $sth->bindParam(':calendar_to', $calendar_to, PDO::PARAM_INT);
                $sth->execute();

                $operative_data1 = $sth->fetchAll(PDO::FETCH_ASSOC);

                //if db table is empty
                if (empty($operative_data1)) {
                    die(json_encode(array(), JSON_UNESCAPED_UNICODE));
                }

                //+++

                if (empty($search_template)) {
                    //get top of terms
                    $operative_data2 = array();
                    if (!empty($operative_data1)) {
                        foreach ($operative_data1 as $block) {
                            if ($block['taxonomy'] == 'min_price' OR $block['taxonomy'] == 'max_price') {
                                continue;
                            }

                            //+++
                            $t = urldecode($block['taxonomy']);
                            $v = $block['value'];
                            //+++
                            if (!isset($operative_data2[$t])) {
                                $operative_data2[$t] = array();
                            }

                            if (!isset($operative_data2[$t][$v])) {
                                $operative_data2[$t][$v] = 0;
                            }

                            $operative_data2[$t][$v] += 1;
                        }

                        foreach ($operative_data2 as &$b) {
                            asort($b, SORT_NUMERIC);
                        }

                        die(json_encode($operative_data2, JSON_UNESCAPED_UNICODE));
                    }
                } else {

                    $operative_data2 = array(); //grouping by hash key
                    if (!empty($operative_data1)) {
                        foreach ($operative_data1 as $key => $value) {
                            if ($value['taxonomy'] == 'min_price' OR $value['taxonomy'] == 'max_price') {
                                unset($operative_data1[$key]);
                                continue;
                            }
                            $value['taxonomy'] = urldecode($value['taxonomy']);
                            if (in_array($value['taxonomy'], $tax_array)) {
                                $tmp = $value;
                                unset($tmp['hash']);
                                $operative_data2[$value['hash']][] = $tmp;
                                unset($operative_data1[$key]); //remove it from memory
                            }
                        }
                        //***

                        $operative_data3 = array();
                        $search_template_count = count($search_template);

                        //+++

                        foreach ($operative_data2 as $key => $value) {
                            if (count($value) === $search_template_count) {
                                $is = true;
                                $tax_should_be = array_flip($tax_array);
                                foreach ($value as &$item) {
                                    if ($item['value'] == 0) {
                                        $is = false;
                                        break;
                                    }

                                    if (class_exists('WOOF_META_FILTER') AND in_array($item['taxonomy'], $this->meta_keys)) {
                                        $item['tax_name'] = WOOF_META_FILTER::get_meta_filter_name($item['taxonomy']);
                                        $item['value_name'] = WOOF_META_FILTER::get_meta_filter_option_name($item['taxonomy'], $item['value']);
                                    } else {
                                        $item['tax_name'] = $taxonomies[$item['taxonomy']];
                                        $t = get_term_by('id', $item['value'], $item['taxonomy']);
                                        $item['value_name'] = $t->name;
                                    }

                                    unset($tax_should_be[$item['taxonomy']]);
                                }

                                //+++

                                if (!empty($tax_should_be)) {
                                    $is = false;
                                }

                                //+++
                                if ($is) {
                                    if (!empty($value)) {
                                        $tmp = array();
                                        foreach ($value as $it) {
                                            $tmp[$it['taxonomy']] = $it;
                                        }
                                        //+++
                                        ksort($tmp, SORT_STRING);
                                        $tmp2 = array();
                                        foreach ($tmp as $it) {
                                            $tmp2[] = $it;
                                        }

                                        $operative_data3[] = $tmp2;
                                    }
                                }
                            }
                        }
                    }

                    $operative_data4 = array();
                    if (!empty($operative_data3)) {
                        foreach ($operative_data3 as $v) {
                            $k4 = "";
                            $vn4 = "";
                            $tn4 = "";
                            foreach ($v as $kk => $vv) {

                                if ($kk > 0) {
                                    $k4 .= "_";
                                    $tn4 .= "+";
                                    $vn4 .= " - ";
                                }

                                $k4 .= $vv['value'];
                                $tn4 .= $vv['tax_name'];
                                $vn4 .= $vv['value_name'];
                            }
                            $operative_data4[$k4]['val'] += 1;
                            $operative_data4[$k4]['tname'] = $tn4;
                            $operative_data4[$k4]['vname'] = $vn4;
                        }
                    }
                    //***
                    usort($operative_data4, array($this, "cmp"));
                    die(json_encode($operative_data4, JSON_UNESCAPED_UNICODE));
                }
            }
        }

        die('PHP PDO ext is not activated!');
    }

    //ajax
    public function woof_get_top_terms() {
        if (current_user_can('create_users')) {

            $stat_data = WOOF_REQUEST::get('woof_stat_data');
            $taxonomies = array();
            foreach (woof()->get_taxonomies() as $slug => $t) {

                //missing taxonomies which are not selected in the stat options
                if (!in_array(urldecode($slug), $this->items_for_stat)) {
                    continue;
                }

                //+++

                $custom_name = '';

                if (isset(woof()->settings['custom_tax_label'][$slug])) {
                    $custom_name = WOOF_HELPER::wpml_translate(null, woof()->settings['custom_tax_label'][$slug], 0);
                }
                if (!empty($custom_name)) {
                    $taxonomies[urldecode($slug)] = $custom_name;
                } else {
                    $taxonomies[urldecode($slug)] = $t->labels->name;
                }
            }
            //meta filter
            if (class_exists('WOOF_META_FILTER')) {
                $meta_keys = array();
                $meta_fields = $this->woof_settings['meta_filter'];
                if (!empty($meta_fields)) {
                    foreach ($meta_fields as $key => $meta) {
                        if ($meta['meta_key'] == "__META_KEY__") {
                            continue;
                        }
                        $slug = $meta["search_view"] . "_" . $meta['meta_key'];
                        if (!in_array($slug, $this->items_for_stat)) {
                            continue;
                        }
                        $meta_keys[] = $slug;
                        $taxonomies[urldecode($slug)] = WOOF_HELPER::wpml_translate(null, $meta['title'], 0);
                    }
                }
            }

            //***
            if (!empty($stat_data)) {
                $operative_data1 = array();
                if (count($stat_data) > 1) {
                    $operative_data1 = $stat_data[0];
                    foreach ($stat_data as $i => $block) {
                        if ($i == 0) {
                            continue;
                        }

                        //+++

                        if (!empty($block)) {
                            foreach ($block as $tax_slug => $terms) {
                                if (!empty($terms)) {
                                    foreach ($terms as $term_id => $count) {
                                        if (!isset($operative_data1[$tax_slug])) {
                                            $operative_data1[$tax_slug] = array();
                                        }

                                        if (!isset($operative_data1[$tax_slug][$term_id])) {
                                            $operative_data1[$tax_slug][$term_id] = 0;
                                        }

                                        $operative_data1[$tax_slug][$term_id] += $count;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $operative_data1 = $stat_data[0];
                }

                $block_tax_diff = array();
                $block_tax_each = array();

                foreach ($operative_data1 as $tax_slug => $terms) {
                    if (isset($taxonomies[$tax_slug])) {
                        $tn = $taxonomies[$tax_slug];
                        foreach ($terms as $term_id => $count) {
                            if (!isset($block_tax_diff[$tn])) {
                                $block_tax_diff[$tn] = 0;
                            }
                            $block_tax_diff[$tn] += $count;
                        }
                    } else {
                        
                    }
                }

                asort($block_tax_diff, SORT_NUMERIC);
                $block_tax_diff = array_reverse($block_tax_diff, true);

                //+++

                foreach ($operative_data1 as $tax_slug => $block) {
                    asort($block, SORT_NUMERIC);
                    $block = array_reverse($block, true);
                    //$block = array_slice($block, 0, $this->max_items_per_graph, true);

                    if (isset($taxonomies[$tax_slug])) {
                        $block_tax_each[$tax_slug]['tax_name'] = $taxonomies[$tax_slug];
                        $block_tax_each[$tax_slug]['terms'] = array();
                        if (!empty($block)) {
                            if (class_exists('WOOF_META_FILTER')) {
                                if (in_array($tax_slug, $meta_keys)) {
                                    foreach ($block as $term_id => $count) {
                                        $val_name = WOOF_META_FILTER::get_meta_filter_option_name($tax_slug, $term_id);
                                        if ($val_name) {
                                            $block_tax_each[$tax_slug]['terms'][$val_name] = $count;
                                        } else {
                                            $block_tax_each[$tax_slug]['terms'][$term_id] = $count;
                                        }
                                    }
                                    continue;
                                }
                            }
                            foreach ($block as $term_id => $count) {
                                $t = get_term_by('id', $term_id, $tax_slug);
                                if (!empty($t->name)) {
                                    $block_tax_each[$tax_slug]['terms'][$t->name] = $count;
                                }
                            }
                        }
                    }
                }

                die(json_encode(array($block_tax_diff, $block_tax_each), JSON_UNESCAPED_UNICODE));
            }
        }
    }

    private function cmp($a, $b) {
        return $a["val"] < $b["val"];
    }

    //writing all search request from the customers
    public function woof_get_request_data($data) {
        if (!$this->is_enabled) {
            return $data;
        }

        //***


        global $wpdb;
        static $done = false;
        $do = true;
        if (defined('DOING_AJAX') && !WOOF_REQUEST::isset('woof_products_doing')) {
            $do = false;
        }
        if (!woof()->is_isset_in_request_data(woof()->get_swoof_search_slug(), FALSE)) {
            $do = false;
        }
        if ($done) {
            $do = false;
        }
        //***
        if ($do) {
            $user_ip = $this->get_the_user_ip();
            $type = 'shop';
            $tax_page = '';
            $tax_page_term_id = 0;
            if (woof()->is_really_current_term_exists()) {
                $t = woof()->get_really_current_term();
                $type = 'tax_page';
                if (isset($t->taxonomy)) {
                    $tax_page = $t->taxonomy;
                    $tax_page_term_id = $t->term_id;
                }
            }
            $request = $data;
            //disable unnecessary data
            unset($request[woof()->get_swoof_search_slug()]);
            unset($request['paged']);
            unset($request['page']);
            unset($request['orderby']);
            unset($request['min_rating']);
            unset($request['currency']);
            //***
            $this->items_for_stat[] = 'really_curr_tax';

            if (!empty($request)) {
                foreach ($request as $key => $value) {
                    $slug = urldecode($key);
                    if (!in_array($slug, $this->items_for_stat)) {
                        unset($request[$slug]);
                    }
                }
            }


            if (!empty($request) AND ( $this->get_user_requests_count($user_ip) < $this->user_max_requests)) {
                //lets control here max deep
                if (count($request) > $this->request_max_deep) {
                    $new_request = array();
                    $tmp_data = array();
                    if (isset($request['min_price']) AND in_array('min_price', $this->items_for_stat)) {
                        $tmp_data['min_price'] = $request['min_price'];
                    }
                    if (isset($request['max_price']) AND in_array('max_price', $this->items_for_stat)) {
                        $tmp_data['max_price'] = $request['max_price'];
                    }
                    //+++
                    $counter = 0;
                    foreach ($request as $key => $value) {
                        if ($counter > $this->request_max_deep) {
                            break;
                        }
                        $slug = urldecode($key);
                        $new_request[$slug] = urldecode($value);

                        if ($slug != 'min_price' AND $slug != 'max_price' AND $slug != 'really_curr_tax') {
                            $counter++;
                        }
                    }
                    $request = array_merge($new_request, $tmp_data);
                }

                //***
                $hash = md5(json_encode($request) . $user_ip . date('d-m-Y'));
                unset($request['really_curr_tax']);
                //lets check for the same request from the same user
                $data_sql = array(
                    array(
                        'type' => 'string',
                        'val' => $user_ip,
                    ),
                    array(
                        'type' => 'string',
                        'val' => $hash,
                    )
                );
                $the_same_hash = $wpdb->get_var(WOOF_HELPER::woof_prepare("SELECT hash FROM $this->table_stat_tmp WHERE user_ip = %s AND hash = %s", $data_sql));
                //***
                if (empty($the_same_hash)) {
                    $request = json_encode($request, JSON_UNESCAPED_UNICODE);
                    $time = time();
                    $data_sql = array(
                        array(
                            'type' => 'string',
                            'val' => $user_ip,
                        ),
                        array(
                            'type' => 'string',
                            'val' => $type,
                        ),
                        array(
                            'type' => 'string',
                            'val' => $request,
                        ),
                        array(
                            'type' => 'string',
                            'val' => $hash,
                        ),
                        array(
                            'type' => 'string',
                            'val' => $tax_page,
                        ),
                        array(
                            'type' => 'int',
                            'val' => $tax_page_term_id,
                        ),
                        array(
                            'type' => 'int',
                            'val' => $time,
                        ),
                    );
                    $insert = WOOF_HELPER::woof_prepare("(%s, %s, %s, %s, %s, %d, %d)", $data_sql);
                    $wpdb->query("INSERT INTO {$this->table_stat_tmp} (user_ip,page,request,hash,tax_page,tax_page_term_id,time) VALUES " . $insert);
                }
            }

            $done = true;
        }

        return $data;
    }

    //stat assembling by cron
    public function assemble_stat() {

        if (!$this->is_enabled) {
            return;
        }

        //***

        global $wpdb;
        $terms = array();
        $step_num = 0;
        $step = 100;
        $data_sql = array(
            array(
                'type' => 'int',
                'val' => 0,
            )
        );
        $count = $wpdb->get_var(WOOF_HELPER::woof_prepare("SELECT COUNT(*)
			FROM {$this->table_stat_tmp} WHERE is_collected = %d
			ORDER BY user_ip", $data_sql));

        while (true) {
            $next = $step * ($step_num + 1);
            $data_sql = array(
                array(
                    'type' => 'int',
                    'val' => 0,
                ),
                array(
                    'type' => 'int',
                    'val' => $step_num,
                ),
                array(
                    'type' => 'int',
                    'val' => $next,
                ),
            );
            $res = $wpdb->get_results(WOOF_HELPER::woof_prepare("SELECT *
			FROM {$this->table_stat_tmp} WHERE is_collected = %d
			LIMIT %d,%d", $data_sql), ARRAY_A);

            if (!empty($res)) {
                foreach ($res as $row) {
                    $data = json_decode($row['request'], true);

                    if (!empty($data)) {
                        foreach ($data as $taxonomy => $term_slugs) {
                            $terms = explode(',', $term_slugs);
                            foreach ($terms as $term_slug) {
                                $value = 0;
                                $meta_value = "";
                                $term_slug = urldecode($term_slug);
                                $taxonomy = urldecode($taxonomy);
                                $exclude = array('min_price', 'max_price');
                                $meta_key = array();
                                if (class_exists('WOOF_META_FILTER')) {

                                    $meta_fields = $this->woof_settings['meta_filter'];
                                    if (!empty($meta_fields)) {
                                        foreach ($meta_fields as $key => $meta) {
                                            if ($meta['meta_key'] == "__META_KEY__") {
                                                continue;
                                            }
                                            $slug = $meta["search_view"] . "_" . $meta['meta_key'];
                                            $meta_key[] = urldecode($slug);
                                            $exclude = array_merge($exclude, $meta_key);
                                        }
                                    }
                                }
                                if (!in_array($taxonomy, $exclude)) {
                                    if (!isset($terms[$taxonomy . '_' . $term_slug])) {
                                        $data_sql = array(
                                            array(
                                                'type' => 'string',
                                                'val' => urlencode($term_slug),
                                            ),
                                        );
                                        $value = (int) $wpdb->get_var(WOOF_HELPER::woof_prepare("SELECT term_id FROM {$wpdb->prefix}terms WHERE slug = %s", $data_sql));
                                        //terms caching
                                        $terms[$taxonomy . '_' . $term_slug] = $value;
                                    } else {
                                        $value = $terms[$taxonomy . '_' . $term_slug];
                                    }
                                } else {
                                    if (in_array($taxonomy, $meta_key)) {
                                        $meta_value = $term_slug; // do that
                                        $value = 0;
                                    } else {
                                        $value = $term_slug;
                                    }
                                }
                                $data_sql = array(
                                    array(
                                        'type' => 'string',
                                        'val' => $row['hash'],
                                    ),
                                    array(
                                        'type' => 'string',
                                        'val' => $row['user_ip'],
                                    ),
                                    array(
                                        'type' => 'string',
                                        'val' => $taxonomy,
                                    ),
                                    array(
                                        'type' => 'int',
                                        'val' => $value,
                                    ),
                                    array(
                                        'type' => 'string',
                                        'val' => $meta_value,
                                    ),
                                    array(
                                        'type' => 'string',
                                        'val' => $row['page'],
                                    ),
                                    array(
                                        'type' => 'int',
                                        'val' => $row['tax_page_term_id'],
                                    ),
                                    array(
                                        'type' => 'int',
                                        'val' => $row['time'],
                                    ),
                                );
                                $insert = WOOF_HELPER::woof_prepare("(%s, %s, %s, %d,%s,%s, %d, %d)", $data_sql);
                                $wpdb->query("INSERT INTO {$this->table_stat_buffer} (hash,user_ip,taxonomy,value,meta_value,page,tax_page_term_id,time) VALUES " . $insert);
                            }
                        }
                    }
                    $data_sql = array(
                        array(
                            'type' => 'int',
                            'val' => 1,
                        ),
                        array(
                            'type' => 'string',
                            'val' => $row['hash'],
                        ),
                    );
                    $wpdb->query(WOOF_HELPER::woof_prepare("UPDATE {$this->table_stat_tmp} SET is_collected = %d WHERE hash = %s", $data_sql));
                }
            }


//***
            if ($next > $count) {
                break;
            }
        }
//***
        $data_sql = array(
            array(
                'type' => 'int',
                'val' => 1,
            ),
        );
        $wpdb->query(WOOF_HELPER::woof_prepare("DELETE FROM {$this->table_stat_tmp} WHERE is_collected = %d", $data_sql));

        if ($this->place_statdata_into_db()) {//placing data into dedicated DB
            //remove data from woof_stat
            $wpdb->query("TRUNCATE TABLE {$this->table_stat_buffer}");
        }
//***

        return false;
    }

    public function place_statdata_into_db() {
        if (!$this->is_enabled) {
            return;
        }

        //***

        global $wpdb;
        try {
            //get all distinct hash keys to get data blocks. 1 block == 1 user search request
            $res = $wpdb->get_results("SELECT DISTINCT hash FROM {$this->table_stat_buffer}", ARRAY_N);

            $hash_array = array();
            if (!empty($res)) {
                foreach ($res as $value) {
                    $hash_array[] = $value[0];
                }
                //***
                foreach ($hash_array as $hash) {
                    $data_sql = array(
                        array(
                            'type' => 'string',
                            'val' => $hash,
                        ),
                    );

                    $res = $wpdb->get_results(WOOF_HELPER::woof_prepare("SELECT * FROM {$this->table_stat_buffer} WHERE hash = %s", $data_sql), ARRAY_A);
                    if (!empty($res)) {
                        $time = $res[0]['time'];

                        //PDO here
                        if (!is_null($this->pdo)) {
                            $table = date('Y', $time) . '_' . date('n', $time);

                            $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `hash` text COLLATE utf8_unicode_ci NOT NULL,
                            `user_ip` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'user IP',
                            `taxonomy` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'taxonomy,price, etc ...',
                            `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'value',
                            `page` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'page',
                            `tax_page_term_id` int(11) NOT NULL,
                            `time` int(11) NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
                          ";
                            $stmt = $this->pdo->prepare($sql);
                            $stmt->execute();
                            //***
                            $sql = "INSERT INTO {$table}(
                                            hash,
                                            user_ip,
                                            taxonomy,
                                            value,
                                            page,
                                            tax_page_term_id,
                                            time) VALUES (
                                            :hash,
                                            :user_ip,
                                            :taxonomy,
                                            :value,
                                            :page,
                                            :tax_page_term_id,
                                            :time)";

                            foreach ($res as $row) {
                                if (class_exists('WOOF_META_FILTER')) {
                                    if (in_array($row['taxonomy'], $this->meta_keys)) {
                                        if ($row['value'] == 0) {
                                            $row['value'] = $row['meta_value'];
                                        }
                                    }
                                }
                                $tax = urlencode($row['taxonomy']);
                                $stmt = $this->pdo->prepare($sql);
                                $stmt->bindParam(':hash', $row['hash'], PDO::PARAM_STR);
                                $stmt->bindParam(':user_ip', $row['user_ip'], PDO::PARAM_STR);
                                $stmt->bindParam(':taxonomy', $tax, PDO::PARAM_STR);
                                $stmt->bindParam(':value', $row['value'], PDO::PARAM_STR);
                                $stmt->bindParam(':page', $row['page'], PDO::PARAM_STR);
                                $stmt->bindParam(':tax_page_term_id', $row['tax_page_term_id'], PDO::PARAM_INT);
                                $stmt->bindParam(':time', $row['time'], PDO::PARAM_INT);

                                $stmt->execute();
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    public function get_stat_tables() {
        if ($this->pdo) {
            $sth = $this->pdo->prepare("SHOW TABLES");
            $sth->execute();
            return $sth->fetchAll(PDO::FETCH_COLUMN);
        }

        return array();
    }

    public function get_stat_min_date_db() {
        $res = get_option('woof_stat_start_data', 0);

        if (!$res) {
            $res = array();
            $tables = $this->get_stat_tables();
            natsort($tables);
            $tables = array_values($tables);
            if (!empty($tables)) {
                $res = explode('_', $tables[0]);
                update_option('woof_stat_start_data', $res);
            }
        }

        return $res;
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function place_statdata_into_files() {
        if (!$this->is_enabled) {
            return;
        }

        //***

        global $wpdb;
        $results = array();
        //***
        try {
            //get all distinct hash keys to get data blocks. 1 block == 1 user search request
            $res = $wpdb->get_results("SELECT DISTINCT hash FROM {$this->table_stat_buffer}", ARRAY_N);
            $hash_array = array();
            if (!empty($res)) {
                foreach ($res as $value) {
                    $hash_array[] = $value[0];
                }
                //***
                foreach ($hash_array as $hash) {
                    $data_sql = array(
                        array(
                            'type' => 'string',
                            'val' => $hash,
                        ),
                    );

                    $res = $wpdb->get_results(WOOF_HELPER::woof_prepare("SELECT * FROM {$this->table_stat_buffer} WHERE hash = %s", $data_sql), ARRAY_A);
                    if (!empty($res)) {
                        $time = $res[0]['time'];
                        if (!isset($results[date('Y', $time)])) {
                            $results[date('Y', $time)] = array();
                        }
                        if (!isset($results[date('Y', $time)][date('n', $time)])) {
                            $results[date('Y', $time)][date('n', $time)] = array();
                        }
                        if (!isset($results[date('Y', $time)][date('n', $time)][date('d', $time)])) {
                            $results[date('Y', $time)][date('n', $time)][date('d', $time)] = array();
                        }

                        //remove unnecessary field time
                        foreach ($res as $k => $v) {
                            unset($res[$k]['time']);
                        }

                        $results[date('Y', $time)][date('n', $time)][date('d', $time)][] = $res;
                    }
                }
                //***
                if (!empty($results)) {
                    foreach ($results as $year => $data1) {
                        foreach ($data1 as $month => $data2) {
                            foreach ($data2 as $day => $data3) {
                                if ($this->create_stat_file($year, $month, $day)) {
                                    $this->push_data_into_file($data3, $year, $month, $day);
                                }
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }

        return false;
    }

    private function push_data_into_file($data, $year, $month, $day) {
        $yf = $this->cache_folder . $year . '/';
        $mf = $yf . $month . '/';
        $file = $mf . $day . '.json';
        try {
            clearstatcache(true, $file);
        } catch (Exception $e) {
            
        }
        //***
        if ($fh = fopen($file, 'a+')) {
            if ($fh) {
                $contents = '';
                $file_size = filesize($file);
                if ($file_size > 0) {
                    $contents = fread($fh, $file_size);
                }
                //***
                if (!empty($contents)) {
                    $contents = json_decode(trim($contents), true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data = array_merge($contents, $data);
                    }
                }
                //***
                ftruncate($fh, 0);
                fwrite($fh, json_encode($data));
                fclose($fh);
                return true;
            }
        }

        return false;
    }

    private function create_stat_file($year, $month, $day) {
        $yf = $this->cache_folder . $year . '/';
        $mf = $yf . $month . '/';
        $file = $mf . $day . '.json';
//***
        WOOF_REQUEST::set('woof_stat_errors', []);

        if (!is_dir($yf)) {
            if (!mkdir($yf, 0777, true)) {
                WOOF_REQUEST::push('woof_stat_errors', sprintf(__('Its not possible to create folder: %s', 'woocommerce-products-filter'), $yf));
                return FALSE;
            }
        }

        if (!is_dir($mf)) {
            if (!mkdir($mf, 0777, true)) {
                WOOF_REQUEST::push('woof_stat_errors', sprintf(__('Its not possible to create folder: %s', 'woocommerce-products-filter'), $mf));
                return FALSE;
            }
        }

        if (!file_exists($file)) {
            if ($fh = fopen($file, 'w')) {
                if (!$fh) {
                    WOOF_REQUEST::push('woof_stat_errors', sprintf(__('Its not possible to create file: %s', 'woocommerce-products-filter'), $file));
                    return FALSE;
                } else {
                    fclose($fh);
                }
            }
        }

        return true;
    }

    //for file system
    public function get_stat_files_structure() {
        if (!is_dir($this->cache_folder)) {
            if (!mkdir($this->cache_folder)) {
                return array();
            }
        }

        $y_folders = glob($this->cache_folder . '*', GLOB_ONLYDIR);

        $data = array();
        $monthes = array();
        $days = array();
        if (!empty($y_folders)) {
            foreach ($y_folders as $key => $value) {
                $y = (int) str_replace($this->cache_folder, '', $value);
                $data[$y] = array();
                $m_folders = glob($this->cache_folder . $y . '/*', GLOB_ONLYDIR);
                if (!empty($m_folders)) {
                    foreach ($m_folders as $km => $m_path) {
                        $m = (int) str_replace($this->cache_folder . $y . '/', '', $m_path);
                        $data[$y][$m] = array();
                        $d_folders = glob($this->cache_folder . $y . '/' . $m . '/*');
                        foreach ($d_folders as $kd => $d_path) {
                            $data[$y][$m][] = (int) str_replace($this->cache_folder . $y . '/' . $m . '/', '', $d_path);
                        }
                    }
                }
            }
        }

        return $data;
    }

    //for file system
    public function get_stat_min_date() {
        $structure = $this->get_stat_files_structure();

        if (!empty($structure)) {
            $min_year = (int) min(array_keys($structure));
            $min_month = (int) min(array_keys($structure[$min_year]));
            $min_day = 0;
            $days = array_values($structure[$min_year][$min_month]);
            if (!empty($days)) {
                $min_day = (int) min($days);
            }
            //***
            while (true) {
                if ($min_day > 0) {
                    break;
                } else {
                    $min_month++;
                    if ($min_month > 12) {
                        $min_month = 1;
                        $min_year++;
                    }
                    $min_day = (int) min(array_values($structure[$min_year][$min_month]));
                }
            }

            return compact('min_day', 'min_month', 'min_year');
        }

        return array();
    }

    //for $this->user_max_requests
    private function get_user_requests_count($user_ip) {
        global $wpdb;
        $data_sql = array(
            array(
                'type' => 'string',
                'val' => $user_ip,
            ),
        );
        return (int) $wpdb->get_var(WOOF_HELPER::woof_prepare("SELECT COUNT(*) as count FROM $this->table_stat_tmp WHERE user_ip = %s", $data_sql));
    }

    public function get_the_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//*** check ip from share internet
            $ip = wc_clean(WOOF_HELPER::get_server_var('HTTP_CLIENT_IP'));
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//*** to check ip is pass from proxy
            $ip = wc_clean(WOOF_HELPER::get_server_var('HTTP_X_FORWARDED_FOR'));
        } else {
            $ip = wc_clean(WOOF_HELPER::get_server_var('REMOTE_ADDR'));
        }
        return $ip;
    }

    public function get_meta_keys() {
        if (class_exists('WOOF_META_FILTER')) {
            $meta_keys = array();
            $meta_fields = array();
            if (isset($this->woof_settings['meta_filter'])) {
                $meta_fields = $this->woof_settings['meta_filter'];
            }
            if (!empty($meta_fields)) {
                foreach ($meta_fields as $key => $meta) {
                    if ($meta['meta_key'] == "__META_KEY__") {
                        continue;
                    }
                    $slug = $meta["search_view"] . "_" . $meta['meta_key'];
                    if (!in_array($slug, $this->items_for_stat)) {
                        continue;
                    }
                    $meta_keys[] = $slug;
                }
            }
            return $meta_keys;
        } else {
            return array();
        }
    }

}

WOOF_EXT::$includes['applications']['stat'] = new WOOF_EXT_STAT();
