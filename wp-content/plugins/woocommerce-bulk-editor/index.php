<?php
/*
  Plugin Name: BEAR – Bulk Editor and Products Manager Professional for WooCommerce
  Plugin URI: https://bulk-editor.com/
  Description: Tools for managing and bulk edit <strong>WooCommerce Products</strong> data in the reliable and flexible way! Be professionals with managing data of your e-shop!
  Requires at least: WP 4.9
  Tested up to: WP 6.2
  Author: realmag777
  Author URI: https://pluginus.net/
  Version: 2.1.3.3
  Requires PHP: 7.2
  Tags: woocommerce,woocommerce bulk edit,bulk edit,bulk,products editor
  Text Domain: woocommerce-bulk-editor
  Domain Path: /languages
  WC requires at least: 3.6
  WC tested up to: 7.7
  Forum URI: https://pluginus.net/support/forum/woobe-woocommerce-bulk-editor-professional/
 */

//update_option('woobe_options_' . get_current_user_id(), ''); //absolute reset of the plugin settings - be care
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//***
add_action('before_woocommerce_init', function () {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

define('WOOBE_PATH', plugin_dir_path(__FILE__));
define('WOOBE_LINK', plugin_dir_url(__FILE__));
define('WOOBE_ASSETS_LINK', WOOBE_LINK . 'assets/');
define('WOOBE_DATA_PATH', WOOBE_PATH . 'data/');
define('WOOBE_PLUGIN_NAME', plugin_basename(__FILE__));
define('WOOBE_VERSION', '2.1.3.3');
//define('WOOBE_VERSION', uniqid('woobe-'));//dev
define('WOOBE_MIN_WOOCOMMERCE_VERSION', '3.6');

woobe_init_translates(); //must be on top to init strings in folder 'data'
//libs
include WOOBE_PATH . 'lib/storage.php';

//data
include_once WOOBE_DATA_PATH . 'fields.php';
include_once WOOBE_DATA_PATH . 'settings.php';

//classes
include WOOBE_PATH . 'classes/helper.php';
include WOOBE_PATH . 'classes/models/profiles.php';
include WOOBE_PATH . 'classes/models/settings.php';
include WOOBE_PATH . 'classes/models/products.php';
include WOOBE_PATH . 'classes/ext.php';
include WOOBE_PATH . 'classes/alert.php';

//23-05-2023
final class WOOBE {

    public $storage = NULL;
    public $settings = NULL;
    public $products = NULL;
    public $profiles = NULL;
    private $ext = array('filters', 'bulk', 'export', 'meta', 'history', 'calculator', 'info', 'fprofiles', 'bulkoperations', 'vendor_area');
    public $show_notes = false;

    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        //fix for sheduled post
        add_filter('woobe_product_statuses', array($this, 'add_statuses'));
    }

    public function init() {

        if (!is_admin()) {
            return;
        }

        if (!class_exists('WooCommerce')) {
            return;
        }

        //no one operation is possible if user is not products administrator!!
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $this->ask_favour();

//***

        add_filter('plugin_action_links_' . WOOBE_PLUGIN_NAME, array($this, 'plugin_action_links'), 50);

//***

        if (isset($_GET['page']) AND $_GET['page'] == 'woobe') {
            //WOOCS compatibility
            if (class_exists('WOOCS')) {
                global $WOOCS;
                $WOOCS->reset_currency();
                remove_filter('woocommerce_product_get_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
                remove_filter('woocommerce_product_variation_get_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
                remove_filter('woocommerce_product_variation_get_regular_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
                remove_filter('woocommerce_product_variation_get_sale_price', array($WOOCS, 'raw_sale_price_filter'), 9999, 2);
                remove_filter('woocommerce_product_get_regular_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
                remove_filter('woocommerce_product_get_sale_price', array($WOOCS, 'raw_woocommerce_price_sale'), 9999, 2);
                remove_filter('woocommerce_get_variation_regular_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 4);
                remove_filter('woocommerce_get_variation_sale_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 4);
                remove_filter('woocommerce_variation_prices', array($WOOCS, 'woocommerce_variation_prices'), 9999, 3);
            }

            add_action('admin_notices', function () {
                $user_id = get_current_user_id();
                if (!get_user_meta($user_id, 'woobe_notice_dismissed')) {
                    echo '<div class="notice notice-warning"><p>' . sprintf(esc_html__('If you not familiar with the plugin, firstly %s please', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                'title' => esc_html__('visit this page', 'woocommerce-bulk-editor'),
                                'href' => 'https://bulk-editor.com/document/woocommerce-products-editor/',
                                'target' => '_blank',
                                'style' => 'line-height: 2em;'
                            ))) . '</p><a href="edit.php?post_type=product&page=woobe&woobe-notice-dismissed=1" class="notice-dismiss"></a></div>';
                }
            });
            add_action('admin_init', function () {
                $user_id = get_current_user_id();
                if (isset($_GET['woobe-notice-dismissed'])) {
                    add_user_meta($user_id, 'woobe_notice_dismissed', 'true', true);
                }
            });
        }

        //side bar menu
        add_action('admin_menu', function () {
            add_submenu_page('edit.php?post_type=product', 'BEAR ' . esc_html__('Bulk Editor', 'woocommerce-bulk-editor'), 'BEAR ' . esc_html__('Bulk Editor', 'woocommerce-bulk-editor'), 'manage_woocommerce', 'woobe', function () {
                $this->print_plugin_options();
            });
        }, 99);

        add_action('admin_bar_menu', function ($wp_admin_bar) {
            $opt = get_option('woobe_options_' . get_current_user_id()); //not beauty but we need it here
            $show = true;
            if (isset($opt['options']['show_admin_bar_menu_btn'])) {
                $show = intval($opt['options']['show_admin_bar_menu_btn']);
            }

            if ($show) {
                $args = array(
                    'id' => 'woobe-btn',
                    'title' => 'BEAR ' . esc_html__('Bulk Editor', 'woocommerce-bulk-editor'),
                    'href' => admin_url('edit.php?post_type=product&page=woobe'),
                    'meta' => array(
                        'class' => 'wp-admin-bar-woobe-btn',
                        'title' => 'BEAR - WooCommerce Bulk Editor'
                    )
                );
                $wp_admin_bar->add_node($args);
            }
            unset($opt);
        }, 250);

//do not init hooks and all other parts of the plugins as we not need it on all site pages
        if (!$this->is_should_init()) {
            return;
        }

//***
//include extensions and their hooks
        if (!empty($this->ext)) {
            foreach ($this->ext as $ext_slug) {
                include WOOBE_PATH . 'ext' . DIRECTORY_SEPARATOR . $ext_slug . DIRECTORY_SEPARATOR . $ext_slug . '.php';
                $class_name = 'WOOBE_' . strtoupper($ext_slug);
                $this->$ext_slug = new $class_name();
            }
        }

//woobe_ext - include extensions from wp-content folder
        $woobe_more_ext_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'woobe_ext';
        if (file_exists($woobe_more_ext_path)) {
            $dir = new DirectoryIterator($woobe_more_ext_path);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isDir() AND !$fileinfo->isDot()) {
                    $ext_slug = trim($fileinfo->getFilename());
                    include $woobe_more_ext_path . DIRECTORY_SEPARATOR . $ext_slug . DIRECTORY_SEPARATOR . $ext_slug . '.php';
                    $class_name = 'WOOBE_' . strtoupper($ext_slug);
                    $this->$ext_slug = new $class_name();
                    $this->ext[] = $ext_slug;
                }
            }
        }


//***
//init variables and hooks of the extensions will be applied, for example hook woobe_extend_fields
        $this->storage = new WOOBE_STORAGE();
        $this->settings = new WOOBE_SETTINGS();

        $this->profiles = new WOOBE_PROFILES($this->settings);
        $this->products = new WOOBE_PRODUCTS($this->settings, $this->storage);

        if (!empty($this->ext)) {
            foreach ($this->ext as $ext_slug) {
//we do it to allow ext hooks works everywhere (in the application and all its extensions)
                $this->$ext_slug->init_vars($this->storage, $this->profiles, $this->settings, $this->products);
            }
        }


//***
        //load_plugin_textdomain('woocommerce-bulk-editor', false, 'woocommerce-bulk-editor/languages');
//ajax
        add_action('wp_ajax_woobe_get_products', array($this, 'woobe_get_products'), 1);
        add_action('wp_ajax_woobe_update_page_field', array($this, 'woobe_update_page_field'), 1);
        add_action('wp_ajax_woobe_redraw_table_row', array($this, 'woobe_redraw_table_row'), 1);
        add_action('wp_ajax_woobe_get_post_field', array($this, 'get_post_field'), 1);
        add_action('wp_ajax_woobe_get_downloads', array($this, 'get_downloads'), 1);
        add_action('wp_ajax_woobe_get_gallery', array($this, 'woobe_get_gallery'), 1);
        add_action('wp_ajax_woobe_get_upsells', array($this, 'woobe_get_upsells'), 1);
        add_action('wp_ajax_woobe_get_cross_sells', array($this, 'woobe_get_cross_sells'), 1);
        add_action('wp_ajax_woobe_get_grouped', array($this, 'woobe_get_grouped'), 1);

        add_action('wp_ajax_woobe_create_new_product', array($this, 'woobe_create_new_product'), 1);
        add_action('wp_ajax_woobe_duplicate_products', array($this, 'woobe_duplicate_products'), 1);
        add_action('wp_ajax_woobe_delete_products', array($this, 'woobe_delete_products'), 1);

        add_action('wp_ajax_woobe_create_new_term', array($this, 'woobe_create_new_term'), 1);
        add_action('wp_ajax_woobe_update_tax_term', array($this, 'woobe_update_tax_term'), 1);
        add_action('wp_ajax_woobe_delete_tax_term', array($this, 'woobe_delete_tax_term'), 1);

//***
        add_action('wp_ajax_woobe_title_autocomplete', array($this, 'woobe_title_autocomplete'));
        add_action('wp_ajax_woobe_save_options', array($this, 'woobe_save_options'), 1);

//***
        add_post_type_support('product', 'author');

        $alert = new WOOBE_ADV();
        $alert->init();
    }

    /**
     * Show action links on the plugins page screen
     */
    public function plugin_action_links($links) {

        $buttons = array(
            '<a href="' . admin_url('edit.php?post_type=product&page=woobe') . '">' . esc_html__('Products Editor', 'woocommerce-bulk-editor') . '</a>',
            '<a target="_blank" href="https://bulk-editor.com/"><span class="icon-book"></span>&nbsp;' . esc_html__('Documentation', 'woocommerce-bulk-editor') . '</a>'
        );

        if ($this->show_notes) {
            $buttons[] = '<a target="_blank" style="color: red; font-weight: bold;" href="' . esc_url('https://pluginus.net/affiliate/woocommerce-bulk-editor') . '">' . esc_html__('Go Pro!', 'woocommerce-bulk-editor') . '</a>';
        }

        return array_merge($buttons, $links);
    }

    public function admin_enqueue_scripts() {
        if (isset($_GET['page']) AND $_GET['page'] == 'woobe') {
            ?>
            <script>
                var lang = {
                    move: "<?php echo esc_html__('move', 'woocommerce-bulk-editor') ?>",
                    search: "<?php echo esc_html__('Search', 'woocommerce-bulk-editor') ?>",
                    rest_failed: "<?php echo esc_html__('Failed', 'woocommerce-bulk-editor') ?>",
                    error: "<?php echo esc_html__('Error', 'woocommerce-bulk-editor') ?>",
                    delete: "<?php echo esc_html__('delete', 'woocommerce-bulk-editor') ?>",
                    ignore: "<?php echo esc_html__('ignore', 'woocommerce-bulk-editor') ?>",
                    no_deletable: "<?php echo esc_html__('This is not deletable!', 'woocommerce-bulk-editor') ?>",
                    no_items: "<?php echo esc_html__('no items', 'woocommerce-bulk-editor') ?>",
                    none: "<?php echo esc_html__('none', 'woocommerce-bulk-editor') ?>",
                    no_data: "<?php echo esc_html__('no data', 'woocommerce-bulk-editor') ?>",
                    loading: "<?php echo esc_html__('Loading', 'woocommerce-bulk-editor') ?> ...",
                    loaded: "<?php echo esc_html__('Loaded', 'woocommerce-bulk-editor') ?>.",
                    saved: "<?php echo esc_html__('Saved', 'woocommerce-bulk-editor') ?>.",
                    saving: "<?php echo esc_html__('Saving', 'woocommerce-bulk-editor') ?> ...",
                    apply: "<?php echo esc_html__('Apply', 'woocommerce-bulk-editor') ?>",
                    cancel: "<?php echo esc_html__('Cancel', 'woocommerce-bulk-editor') ?>",
                    canceled: "<?php echo esc_html__('Canceled', 'woocommerce-bulk-editor') ?>",
                    sure: "<?php echo esc_html__('Sure?', 'woocommerce-bulk-editor') ?>",
                    creating: "<?php echo esc_html__('Creating', 'woocommerce-bulk-editor') ?> ...",
                    created: "<?php echo esc_html__('Created!', 'woocommerce-bulk-editor') ?>",
                    duplicating: "<?php echo esc_html__('Duplicating', 'woocommerce-bulk-editor') ?> ...",
                    duplicated: "<?php echo esc_html__('Duplicated!', 'woocommerce-bulk-editor') ?>",
                    deleting: "<?php echo esc_html__('Deleting', 'woocommerce-bulk-editor') ?> ...",
                    deleted: "<?php echo esc_html__('Deleted!', 'woocommerce-bulk-editor') ?>",
                    reseting: "<?php echo esc_html__('Reseting', 'woocommerce-bulk-editor') ?> ...",
                    reseted: "<?php echo esc_html__('Reseted!', 'woocommerce-bulk-editor') ?>",
                    upload_image: "<?php echo esc_html__('Upload image', 'woocommerce-bulk-editor') ?>",
                    upload_images: "<?php echo esc_html__('Upload images', 'woocommerce-bulk-editor') ?>",
                    select_all: "<?php echo esc_html__('Select all', 'woocommerce-bulk-editor') ?>",
                    deselect_all: "<?php echo esc_html__('Deselect all', 'woocommerce-bulk-editor') ?>",
                    upload_file: "<?php echo esc_html__('Upload file', 'woocommerce-bulk-editor') ?>",
                    fill_up_data: "<?php echo esc_html__('Fill up the data please!', 'woocommerce-bulk-editor') ?>",
                    enter_duplicate_count: "<?php echo esc_html__('Enter how many time duplicate selected product(s)!', 'woocommerce-bulk-editor') ?>",
                    enter_new_count: "<?php echo esc_html__('Enter how many new product(s) to create!', 'woocommerce-bulk-editor') ?>",
                    search_input_placeholder: "<?php echo esc_html__('Text search by title or SKU', 'woocommerce-bulk-editor') ?>",
                    show_panel: "<?php esc_html_e('Show: Filters/Bulk Edit/Export', 'woocommerce-bulk-editor') ?>",
                    close_panel: "<?php esc_html_e('Hide: Filters/Bulk Edit/Export', 'woocommerce-bulk-editor') ?>",
                    per_page: "<?php esc_html_e('Per page', 'woocommerce-bulk-editor') ?>",
                    color_picker_col: "<?php esc_html_e('Select background color', 'woocommerce-bulk-editor') ?>",
                    color_picker_txt: "<?php esc_html_e('Select text color', 'woocommerce-bulk-editor') ?>",
                    sEmptyTable: "<?php esc_html_e('No data available in the table', 'woocommerce-bulk-editor') ?>",
                    sInfo: "<?php esc_html_e('Showing _START_ to _END_ of _TOTAL_ entries', 'woocommerce-bulk-editor') ?>",
                    sInfoEmpty: "<?php esc_html_e('Showing 0 to 0 of 0 entries', 'woocommerce-bulk-editor') ?>",
                    sInfoFiltered: "<?php esc_html_e('(filtered from _MAX_ total entries)', 'woocommerce-bulk-editor') ?>",
                    sLoadingRecords: "<?php esc_html_e('Loading...', 'woocommerce-bulk-editor') ?>",
                    sProcessing: "<?php esc_html_e('Processing...', 'woocommerce-bulk-editor') ?>",
                    sZeroRecords: "<?php esc_html_e('No matching records found', 'woocommerce-bulk-editor') ?>",
                    sFirst: "<?php esc_html_e('First', 'woocommerce-bulk-editor') ?>",
                    sLast: "<?php esc_html_e('Last', 'woocommerce-bulk-editor') ?>",
                    sNext: "<?php esc_html_e('Next', 'woocommerce-bulk-editor') ?>",
                    sPrevious: "<?php esc_html_e('Previous', 'woocommerce-bulk-editor') ?>",
                    action_state_1: "<?php esc_html_e('all the products on the site', 'woocommerce-bulk-editor') ?>",
                    action_state_2: "<?php esc_html_e('the filtered products. To remove the products filtering press reset button on the tools panel below', 'woocommerce-bulk-editor') ?>",
                    action_state_31: "<?php esc_html_e('the selected products (variations)', 'woocommerce-bulk-editor') ?>",
                    action_state_32: "<?php esc_html_e('You can reset selection of the products by its reset button on the panel of the editor OR uncheck them manually!', 'woocommerce-bulk-editor') ?>",
                    term_maybe_exist: "<?php esc_html_e('Maybe term(s) with such name(s) already exists!', 'woocommerce-bulk-editor') ?>",
                    free_ver_profiles: "<?php esc_html_e('In FREE version of the plugin you can create one profile only!', 'woocommerce-bulk-editor') ?>",
                    append_sub_item: "<?php esc_html_e('append sub item', 'woocommerce-bulk-editor') ?>",
                    is_deactivated_in_free: "<?php esc_html_e('This field is deactivated in FREE version for bulk edit!', 'woocommerce-bulk-editor') ?>",
                    checked_products: "<?php esc_html_e('Products checked', 'woocommerce-bulk-editor') ?>"
                };

                var woobe_settings = {
                    show_thumbnail_preview: <?php echo intval($this->settings->show_thumbnail_preview) ?>,
                    load_switchers: <?php echo intval($this->settings->load_switchers) ?>,
                    autocomplete_max_elem_count: <?php echo intval($this->settings->autocomplete_max_elem_count) ?>,
                    show_notes: <?php echo intval($this->show_notes) ?>
                };

                var woobe_assets_link = "<?php echo WOOBE_ASSETS_LINK ?>";
                var spinner = woobe_assets_link + "/images/spinner.gif";

                //***

            <?php
            if (class_exists('SitePress')) {
                add_filter('woobe_current_language', function () {
//WPML compatibility
//because if it will be selected 'all' language - will be shown default one
                    return ICL_LANGUAGE_CODE;
                });
            }
            ?>

                var woobe_lang = '<?php echo apply_filters('woobe_current_language', '') ?>';//for translating compatibilities



            </script>

            <?php
            wp_enqueue_style('open_sans_font', 'https://fonts.googleapis.com/css?family=Open+Sans');
            wp_enqueue_style('woobe-bootstrap-grid', WOOBE_ASSETS_LINK . 'css/bootstrap-grid.css', array(), WOOBE_VERSION);
            wp_enqueue_style('woobe', WOOBE_ASSETS_LINK . 'css/woobe.css', array(), WOOBE_VERSION);
            wp_enqueue_style('woobe_scrollbar', WOOBE_ASSETS_LINK . 'css/jquery.scrollbar.css', array(), WOOBE_VERSION);
            wp_enqueue_style('woobe_fontello', WOOBE_ASSETS_LINK . 'css/fontello.css', array(), WOOBE_VERSION);

//***

            wp_enqueue_media();
            wp_enqueue_script('media-upload');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');

            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');

            wp_enqueue_style('woobe_datatables', WOOBE_ASSETS_LINK . 'css/tables.css', array(), WOOBE_VERSION);
            wp_enqueue_script('woobe_datatables_net', WOOBE_ASSETS_LINK . 'js/jquery.dataTables.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_script('woobe_data_tables', WOOBE_ASSETS_LINK . 'js/data-tables.js', array('woobe_datatables_net'), WOOBE_VERSION);

            wp_enqueue_style('woobe_data_tables_fc', 'https://cdn.datatables.net/fixedcolumns/4.2.1/css/fixedColumns.dataTables.min.css');
            wp_enqueue_script('woobe_data_tables_fc', 'https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js');

            wp_enqueue_script('woobe_jquery_growl', WOOBE_ASSETS_LINK . 'js/jquery.growl.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_style('woobe_switchery', WOOBE_ASSETS_LINK . 'js/switchery/switchery.min.css', array(), WOOBE_VERSION);
            wp_enqueue_script('woobe_switchery', WOOBE_ASSETS_LINK . 'js/switchery/switchery.min.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_style('woobe_chosen', WOOBE_ASSETS_LINK . 'js/chosen/chosen.min.css', array(), WOOBE_VERSION);
            wp_enqueue_script('woobe_chosen', WOOBE_ASSETS_LINK . 'js/chosen/chosen.jquery.min.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_style('woobe_autocomplete', WOOBE_ASSETS_LINK . 'js/easy-autocomplete/easy-autocomplete.min.css', array(), WOOBE_VERSION);
            wp_enqueue_style('woobe_autocomplete_theme', WOOBE_ASSETS_LINK . 'js/easy-autocomplete/easy-autocomplete.themes.min.css', array(), WOOBE_VERSION);
            wp_enqueue_script('woobe_autocomplete', WOOBE_ASSETS_LINK . 'js/easy-autocomplete/jquery.easy-autocomplete.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_style('woobe_datetimepicker', WOOBE_ASSETS_LINK . 'js/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css', array(), WOOBE_VERSION);
            wp_enqueue_script('woobe_datetimepicker_moment', WOOBE_ASSETS_LINK . 'js/datepicker/moment-with-locales.min.js', array('jquery'), WOOBE_VERSION);
            wp_enqueue_script('woobe_datetimepicker', WOOBE_ASSETS_LINK . 'js/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_script('woobe_placeholder_label', WOOBE_ASSETS_LINK . 'js/jquery.placeholder.label.min.js', array('jquery'), WOOBE_VERSION);
            wp_enqueue_script('woobe_tooltip', WOOBE_ASSETS_LINK . 'js/tooltip.js', array('jquery'), WOOBE_VERSION);

            wp_enqueue_script('woobe_tabs', WOOBE_ASSETS_LINK . 'js/tabs.js', array(), WOOBE_VERSION);
            wp_enqueue_script('woobe_scrollbar', WOOBE_ASSETS_LINK . 'js/jquery.scrollbar.min.js', array(), WOOBE_VERSION);
//***
            wp_enqueue_script('woobe', WOOBE_ASSETS_LINK . 'js/woobe.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'woobe_tabs'), WOOBE_VERSION);
            do_action('woobe_ext_scripts'); //including extensions scripts
        }
    }

    public function print_plugin_options() {
        $args = array();
        $args['options'] = $this->settings->get_options();
        $args['total_settings'] = $this->settings->get_total_settings();
        $args['tax_keys'] = array();
        $args['attribute_keys'] = array();

        $args['is_popupeditor'] = FALSE;
        $args['is_downloads'] = FALSE;
        $args['is_gallery'] = FALSE;
        $args['is_upsells'] = FALSE;
        $args['is_cross_sells'] = FALSE;
        $args['is_grouped'] = FALSE;

        $args['meta_popup_editor'] = FALSE;

//to generate terms in popup taxonomies data
        if (!empty($this->settings->active_fields)) {
            foreach ($this->settings->active_fields as $k => $f) {
                if ($f['field_type'] == 'taxonomy' AND $f['edit_view'] == 'popup') {
                    $args['tax_keys'][] = $f['taxonomy'];
                }

                if ($f['field_type'] == 'attribute') {
                    $args['attribute_keys'][] = $k;
                }

//***

                if ($f['edit_view'] == 'popupeditor') {
                    $args['is_popupeditor'] = TRUE;
                }

                if ($f['edit_view'] == 'downloads_popup_editor') {
                    $args['is_downloads'] = TRUE;
                }

                if ($f['edit_view'] == 'gallery_popup_editor') {
                    $args['is_gallery'] = TRUE;
                }

                if ($f['edit_view'] == 'upsells_popup_editor') {
                    $args['is_upsells'] = TRUE;
                }

                if ($f['edit_view'] == 'cross_sells_popup_editor') {
                    $args['is_cross_sells'] = TRUE;
                }

                if ($f['edit_view'] == 'grouped_popup_editor') {
                    $args['is_grouped'] = TRUE;
                }

                if ($f['edit_view'] == 'meta_popup_editor') {
                    $args['meta_popup_editor'] = TRUE;
                }
            }
        }

        //***

        $args['active_fields'] = $this->settings->active_fields;
        $args['settings_fields'] = $this->settings->get_fields();
        $args['settings_fields_full'] = $this->settings->get_fields(false);
        $args['settings_fields_keys'] = $this->settings->get_fields_keys();
        $args['editable'] = $this->settings->editable;
        $args['default_sortby_col_num'] = $this->settings->get_default_sortby_col_num();
        $args['default_sort'] = $this->settings->default_sort;
        $args['no_order'] = $this->settings->no_order;
        $args['per_page'] = $this->settings->per_page;
        $args['extend_per_page'] = apply_filters('woobe_set_per_page_values', '');
        $args['show_notes'] = $this->show_notes;
        $args['current_user_role'] = $this->settings->current_user_role;
        $args['profiles'] = $this->profiles->get();

        //***

        echo WOOBE_HELPER::render_html(WOOBE_PATH . 'views/woobe.php', apply_filters('woobe_print_plugin_options', $args));
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//ajax
    public function woobe_get_products($args = array(), $return = false) {

        if (!current_user_can('manage_woocommerce')) {
            return;
        }

//***

        $res = array();
        $res['draw'] = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;
        $res['data'] = array();
        $fileds_keys = $this->settings->get_fields_keys();

        if (empty($args)) {
//for ajax only
            $args = array(
                'lang' => sanitize_key($_REQUEST['lang']),
                'per_page' => intval($_REQUEST['length']),
                'offset' => intval($_REQUEST['start']),
                'order_by' => $fileds_keys[intval($_REQUEST['order'][0]['column'])],
                'order' => sanitize_key($_REQUEST['order'][0]['dir']),
                'search' => sanitize_text_field($_REQUEST['search']['value'])
            );
        }

        $products = $this->products->gets($args);

        $res['recordsFiltered'] = $res['recordsTotal'] = $products->found_posts;
        if ($products->found_posts > 0) {
            $products_types = array();
            $products_titles = array();
            foreach ($products->posts as $p) {
                wp_cache_flush();
                $product_type = WC_Data_Store::load('product')->get_product_type($p->ID);
                $res['data'][] = $this->_pack_row($p, $product_type);
                $products_types[$p->ID] = $product_type;
                $products_titles[$p->ID] = str_replace('"', "", str_replace("'", "", $p->post_title));

//get variations if exists and requested
                if ($product_type == 'variable' AND ( isset($_REQUEST['woobe_show_variations']) AND intval($_REQUEST['woobe_show_variations']) > 0)) {

                    $variations = $this->products->gets(array('get_variations' => $p->ID));
                    if ($variations->found_posts > 0) {
                        foreach ($variations->posts as $var) {
                            $res['data'][] = $this->_pack_row($var, 'variation');
                            $products_types[$var->ID] = 'variation';
                            $products_titles[$var->ID] = str_replace('"', "", str_replace("'", "", $var->post_title));

//***

                            if ($this->settings->add_vars_to_var_title) {
                                $products_titles[$var->ID] = $this->products->generate_product_title($this->products->get_product($var->ID));
                            }
                        }
                    }
                }

//***
//data for javascript functionality on the front
                $res['products_types'] = $products_types;
                $res['products_titles'] = $products_titles;
                //$res['query']=$products->request;
            }
        }

//***
//echo get_num_queries() . ' + ';exit;
        if (!$return) {
//if requested by ajax
            die(json_encode($res));
        }

        return $res;
    }

//service
    private function _pack_row($p, $product_type) {
        //wp_cache_flush();
        $row = array();
        $p = (array) $p;

        foreach ($this->settings->get_fields_keys() as $key) {
            $row[] = $this->wrap_field_val($p, $key, $product_type);
        }

        if ($product_type !== 'variation') {
//buttons: edit + view
            $row[] = WOOBE_HELPER::draw_link(array(
                        'title' => '&#xea0b;',
                        'href' => get_permalink($p['ID']),
                        'target' => '_blank',
                        'class' => 'button button-primary button-small button-small-2',
                        'title_attr' => esc_html__('View the product on the site front', 'woocommerce-bulk-editor')
                    )) . '&nbsp;' . WOOBE_HELPER::draw_link(array(
                        'title' => '&#xea25;',
                        'href' => get_admin_url() . 'post.php?post=' . $p['ID'] . '&action=edit',
                        'target' => '_blank',
                        'class' => 'button button-primary button-small button-small-2',
                        'title_attr' => esc_html__('Editing of the product on its page', 'woocommerce-bulk-editor')
            ));
        } else {
            $row[] = '';
        }

        return $row;
    }

//ajax
    public function woobe_update_page_field() {

        $product_id = intval($_REQUEST['product_id']);

        if (!isset($_REQUEST['value']) || $_REQUEST['value'] == null) {
            $_REQUEST['value'] = array();
        }
        $field_key = sanitize_text_field(trim($_REQUEST['field'])); //if sanitize by sanitize_key not all meta keys works normally!!
        if ($product_id > 0 AND isset($_REQUEST['value'])) {
            if ($_REQUEST['value']) {
                if (is_array($_REQUEST['value'])) {
                    $value = $_REQUEST['value'];

                    $value = WOOBE_HELPER::sanitize_array((array) $_REQUEST['value']);
                } else {
                    $is_encoded = preg_match('~%[0-9A-F]{2}~i', $_REQUEST['value']);
                    $allowedpost = wp_kses_allowed_html('post');
                    if ('post_content' == $_REQUEST['field'] OR 'post_excerpt' == $_REQUEST['field'] OR $this->settings->active_fields[$field_key]['edit_view'] === 'popupeditor') {
                        $is_encoded = false;
                        $allowedpost['iframe'] = array(
                            'align' => true,
                            'frameborder' => true,
                            'height' => true,
                            'width' => true,
                            'sandbox' => true,
                            'seamless' => true,
                            'scrolling' => true,
                            'srcdoc' => true,
                            'src' => true,
                            'class' => true,
                            'id' => true,
                            'style' => true,
                            'border' => true,
                        );
                    }

                    if ($is_encoded) {
                        $data_array = [];

                        parse_str($_REQUEST['value'], $data_array);

                        $value = WOOBE_HELPER::sanitize_array($data_array);
                    } else {
                        $value = wp_kses($_REQUEST['value'], $allowedpost);
                    }
                }
            } else {
                $value = "";
            }

//***
            //normalize calendar date
            if ($this->settings->active_fields[$field_key]['edit_view'] === 'calendar') {
                if ($this->settings->active_fields[$field_key]["field_type"] AND $this->settings->active_fields[$field_key]["field_type"] == "meta") {
                    $value = strtotime($value);
                } else {
                    $value = $this->products->normalize_calendar_date($value, $field_key);
                }
            }

//***
            //uploated to
            if (isset($_REQUEST['uploaded_to']) AND $_REQUEST['uploaded_to'] == 1 AND $field_key == '_thumbnail_id') {
                $id_th = intval($value);
                if ($id_th) {
                    $my_post = array();
                    $my_post['ID'] = $id_th;
                    $my_post['post_parent'] = $product_id;

                    wp_update_post(wp_slash($my_post));
                }
            }

            $value = $this->products->string_replacer($value, $product_id);
            $value = $this->products->string_macros($value, $field_key, $product_id);

            echo $this->products->update_page_field($product_id, $field_key, $value);
            //die(json_encode($value));
        }

        exit;
    }

//ajax
    public function woobe_redraw_table_row() {
        if (is_array($_REQUEST['value'])) {
            $value = (array) $_REQUEST['value'];
        } else {
            $value = sanitize_text_field(trim($_REQUEST['value']));
        }

//***

        $product_id = intval($_REQUEST['product_id']);

        if ($product_id > 0) {
            if (isset($_REQUEST['field']) AND !empty($_REQUEST['field'])) {
                $field_key = sanitize_key($_REQUEST['field']);
                if (!empty($field_key)) {
                    $this->products->update_page_field($product_id, $field_key, $value);
                }
            }


//generate table row by $product_id
            $res = $this->woobe_get_products(array(
                'p' => $product_id,
                'post_type' => array('product', 'product_variation')
                    ), true);

            echo(json_encode($res['data'][0]));
        }
        exit;
    }

//ajax
    public function get_post_field() {
        $test = $this->products->get_post_field(intval($_REQUEST['product_id']), sanitize_key($_REQUEST['field']), (isset($_REQUEST['post_parent']) ? intval($_REQUEST['post_parent']) : 0));
        echo $test;
        exit;
    }

//ajax
    public function get_downloads() {

        $product_id = intval($_REQUEST['product_id']);

        if (!$product_id) {
            exit;
        }

        $product = $this->products->get_product($product_id);

        echo WOOBE_HELPER::render_html(WOOBE_PATH . 'views/parts/product-downloads.php', array(
            'downloadable_files' => $product->get_downloads('edit')
        ));

        exit;
    }

//ajax
    public function woobe_get_gallery() {

        $product_id = intval($_REQUEST['product_id']);

        if (!$product_id) {
            exit;
        }

        $product = $this->products->get_product($product_id);

        echo WOOBE_HELPER::render_html(WOOBE_PATH . 'views/parts/product-gallery.php', array(
            'images' => $product->get_gallery_image_ids('edit')
        ));

        exit;
    }

//ajax
    public function woobe_get_upsells() {

        $product_id = intval($_REQUEST['product_id']);

        if (!$product_id) {
            exit;
        }

        $product = $this->products->get_product($product_id);

        echo WOOBE_HELPER::render_html(WOOBE_PATH . 'views/parts/product-upsells.php', array(
            'products' => $product->get_upsell_ids('edit')
        ));

        exit;
    }

//ajax
    public function woobe_get_cross_sells() {

        $product_id = intval($_REQUEST['product_id']);

        if (!$product_id) {
            exit;
        }

        $product = $this->products->get_product($product_id);

        echo WOOBE_HELPER::render_html(WOOBE_PATH . 'views/parts/product-cross-sells.php', array(
            'products' => $product->get_cross_sell_ids('edit')
        ));

        exit;
    }

//ajax
    public function woobe_get_grouped() {

        $product_id = intval($_REQUEST['product_id']);

        if (!$product_id) {
            exit;
        }

        $product = $this->products->get_product($product_id);

        echo WOOBE_HELPER::render_html(WOOBE_PATH . 'views/parts/product-grouped.php', array(
            'products' => $product->get_children('edit')
        ));

        exit;
    }

//ajax
    public function woobe_save_options() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        $data = array();
        parse_str($_REQUEST['formdata'], $data);
        $data = WOOBE_HELPER::sanitize_array($data);

        if (isset($data['woobe_options'])) {
            if (is_array($data['woobe_options'])) {
                $this->settings->update_options($data['woobe_options']);
            }

            //***
            //save shop manager fields visibility
            if (in_array($this->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
                $shop_manager_visibility = array();

                foreach ($data['woobe_options']['fields'] as $key => $v) {
                    if (isset($v['shop_manager_visibility'])) {
                        $shop_manager_visibility[$key] = intval($v['shop_manager_visibility']);
                    }
                }

                update_option('woobe_shop_manager_visibility', $shop_manager_visibility);
            }
        }

        exit;
    }

//ajax
    public function woobe_title_autocomplete() {
        $results = array();
        $results[] = array(
            "name" => esc_html__("Products not found!", 'woocommerce-bulk-editor'),
            "id" => 0,
            "type" => "",
            "link" => "#",
            "icon" => WOOBE_ASSETS_LINK . 'images/not-found.jpg'
        );

//***

        if (!empty($_REQUEST['woobe_txt_search'])) {
            $args = array(
                'nopaging' => true,
                'post_type' => array('product', 'product_variation'),
                'post_status' => array_keys(apply_filters('woobe_product_statuses', get_post_statuses())),
                'order_by' => 'title',
                'order' => 'ASC',
                'per_page' => intval($_REQUEST['auto_res_count']) > 0 ? intval($_REQUEST['auto_res_count']) : 10,
                'max_num_pages' => intval($_REQUEST['auto_res_count']) > 0 ? intval($_REQUEST['auto_res_count']) : 10
            );

//***

            if (!empty($_REQUEST['exept_ids'])) {
                $exept_ids = array(); //which products exclude as they are on the list already
                parse_str($_REQUEST['exept_ids'], $exept_ids);

                if (isset($exept_ids['woobe_prod_ids'])) {
                    $args['post__not_in'] = array_map(function ($item) {
                        return intval($item); //sanitize intval
                    }, $exept_ids['woobe_prod_ids']);
                }
            }

//***
            $st = sanitize_text_field($_REQUEST['woobe_txt_search']);
            $_REQUEST['woobe_txt_search'] = array();
            $_REQUEST['woobe_txt_search_behavior'] = array();
            $_REQUEST['woobe_txt_search']['post_title'] = $st;
            $_REQUEST['woobe_txt_search_behavior']['post_title'] = 'like';
            $this->products->suppress_filters = true;
            add_filter('posts_where', array($this->filters, 'posts_txt_where'), 101);
            $query = $this->products->gets($args);

//+++
//http://easyautocomplete.com/guide
            if ($query->have_posts()) {
                $results = array();
                foreach ($query->posts as $p) {
                    $data = array(
                        "name" => $p->post_title . ' (#' . $p->ID . ')',
                        "id" => $p->ID,
                        "type" => "product"
                    );
                    if (has_post_thumbnail($p->ID)) {
                        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($p->ID), 'thumbnail');
                        $data['icon'] = $img_src[0];
                    } else {
                        $data['icon'] = WOOBE_ASSETS_LINK . 'images/not-found.jpg';
                    }
                    $data['link'] = get_post_permalink($p->ID);
                    $results[] = $data;
                }
            }
        }


        die(json_encode($results));
    }

    //ajax
    public function woobe_create_new_product() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }
        if (!isset($_REQUEST['woobe_nonce']) || !wp_verify_nonce($_REQUEST['woobe_nonce'], 'woobe_tools_panel_nonce')) {
            die('0');
        }

        //also: http://woocommerce.wp-a2z.org/oik_api/wc_api_productscreate_product/
        $wp_rest_request = new WP_REST_Request('POST');
        $wp_rest_request->set_body_params(array(
            'name' => esc_html__('New Product', 'woocommerce-bulk-editor'),
            'description' => '',
            'status' => apply_filters('woobe_new_product_status', 'draft')
        ));
        $products_controller = new WC_REST_Products_Controller();

        $to_create = intval($_REQUEST['to_create']);
        while ($to_create) {
            $products_controller->create_item($wp_rest_request);
            $to_create--;
        }

        exit;
    }

//ajax
    public function woobe_duplicate_products() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }
        if (!isset($_REQUEST['woobe_nonce']) || !wp_verify_nonce($_REQUEST['woobe_nonce'], 'woobe_tools_panel_nonce')) {
            die('0');
        }

        if (!empty($_REQUEST['products_ids'])) {
            if (!class_exists('WC_Admin_Duplicate_Product', false)) {
                include_once (plugin_dir_path('woocommerce/includes/admin/class-wc-admin-duplicate-product.php'));
            }
            $duplicator = new WC_Admin_Duplicate_Product();
            //$cached_products = $this->storage->get_val('woobe_cached_products');

            foreach ($_REQUEST['products_ids'] as $product_id) {
                $product_id = intval($product_id); //sanitizing

                $product = $this->products->get_product($product_id);

                //when duplication do some copies of the same product - just idea
                /*
                  if (isset($cached_products[$product_id])) {
                  $product = $cached_products[$product_id];
                  } else {
                  $product = $this->products->get_product($product_id);
                  $cached_products[$product_id] = $product;
                  $this->storage->set_val('woobe_cached_products', $cached_products);
                  }
                 */
                //duplication of variation is locked
                if ($product->get_type() === 'variation') {
                    continue;
                }

                $duplicate = $duplicator->product_duplicate($product);
                $duplicate->set_slug($duplicate->get_title()/* . '-'.$duplicate->get_id() */);
                $this->clone_custom_taxonomies($product_id, $duplicate->get_id());
                //delete rating
                //delete_post_meta($duplicate->get_id(), "_wc_average_rating");

                $duplicate->save();
                do_action('woocommerce_product_duplicate', $duplicate, $product);
//clean_post_cache($d->get_id());
            }

//wp_cache_flush();
        }

        die('done');
    }

//ajax
    public function woobe_delete_products() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }
        if (!isset($_REQUEST['woobe_nonce']) || !wp_verify_nonce($_REQUEST['woobe_nonce'], 'woobe_tools_panel_nonce')) {
            die('0');
        }

        if (!empty($_REQUEST['products_ids']) AND is_array($_REQUEST['products_ids'])) {
            foreach ($_REQUEST['products_ids'] as $product_id) {
                $product_id = intval($product_id);
                $product = $this->products->get_product(intval($product_id));
                $product->delete(false);
            }

            //wp_cache_flush(); - hint for possible compatibility
        }

        die('done');
    }

    public function wrap_field_val($post, $field_key, $product_type) {

        $res = NULL;

        $product = $this->products->get_product($post['ID']);
        $product_id = $product->get_id();

        if (isset($this->settings->active_fields[$field_key]['allow_product_types'])) {
            try {
                if (!in_array($product_type, $this->settings->active_fields[$field_key]['allow_product_types'])) {
                    return WOOBE_HELPER::draw_restricked();
                }
            } catch (Exception $e) {
//***
            }
        }

        if (isset($this->settings->active_fields[$field_key]['prohibit_product_types'])) {
            try {
                if (in_array($product_type, $this->settings->active_fields[$field_key]['prohibit_product_types'])) {
                    $additional_info = "";
                    if ($this->settings->active_fields[$field_key]['field_type'] == 'attribute' && $post['post_type'] === 'product_variation') {
                        $attr_name = $product->get_attribute($field_key);
                        if ($attr_name) {
                            $additional_info = '<i class="woobe_variation_attr_info" >[' . $attr_name . ']</i>';
                        }
                    }
                    return WOOBE_HELPER::draw_restricked() . $additional_info;
                }
            } catch (Exception $e) {
//***
            }
        }

//***
        $val = '';
        switch ($this->settings->active_fields[$field_key]['field_type']) {
            case 'meta':
            case 'prop':
            case 'attribute':
                $val = $this->products->get_post_field($product_id, $field_key);
                break;

            case 'taxonomy':
                $terms = $this->products->get_post_field($product_id, $field_key);

                $ids = array();
                $titles = array();

//***

                if (!empty($terms)) {
                    foreach ($terms as $t) {
                        $ids[] = $t->term_id;
                        $titles[] = $t->name;
                    }
                }

                if (!empty($ids)) {
                    $ids = array_map(function ($v) {
                        return intval($v);
                    }, $ids);
                }

//***

                if ($this->settings->active_fields[$field_key]['type'] === 'array') {
                    $val = array(
                        'terms_ids' => $ids,
                        'terms_titles' => $titles
                    );

//for drop-down view
                    if ($this->settings->active_fields[$field_key]['edit_view'] == 'select') {
                        $val['selected'] = $val['terms_ids'];
                    }
                } else {
//string, for example: product_type
                    $val = $titles[0];
                }
                if ($field_key == 'product_type') {
                    $val = $product->get_type();
                    //$val = $this->products->get_product_type($post['ID']);
                }
                break;

            default:
                if (isset($post[$field_key])) {
                    $val = $post[$field_key];
//for variations
                    if ($field_key === 'post_title' AND $post['post_type'] === 'product_variation') {
                        if ($this->settings->add_vars_to_var_title) {
                            $val = $this->products->generate_product_title($product);
                        }
                    }
                }
                break;
        }

//***

        switch ($this->settings->active_fields[$field_key]['edit_view']) {
            case 'select':

                $select_options = $this->settings->active_fields[$field_key]['select_options'];

                //fix for product variations statuses
                if ($field_key === 'post_status') {
                    if ($product->is_type('variation')) {
                        unset($select_options['draft']);
                        unset($select_options['pending']);
                    }
                }

                //***
                $res = WOOBE_HELPER::draw_select(array(
                            'field' => $field_key,
                            'product_id' => $product_id,
                            'class' => 'woobe_data_select ',
                            'options' => $select_options,
                            'selected' => (isset($val['selected']) ? $val['selected'] : $val),
                            'onchange' => 'woobe_act_select(this)',
                ));

                break;

            case 'multi_select':

                $res = WOOBE_HELPER::render_html(WOOBE_PATH . 'views/elements/multi_select.php', array(
                            'field_key' => $field_key,
                            'product_id' => $product_id,
                            'val' => $val,
                            'active_fields' => $this->settings->active_fields,
                            'post' => $post,
                ));
                break;
            case 'attr_visibility':

                $attributes = $this->products->get_attributes($product_id);
                $res = WOOBE_HELPER::render_html(WOOBE_PATH . 'views/elements/attribute_visibility.php', array(
                            'field_key' => $field_key,
                            'attributes' => $attributes,
                            'product_id' => $product_id,
                            'post' => $post,
                ));
                break;

            case 'popup':
                $res = WOOBE_HELPER::draw_taxonomy_popup_btn($val, $field_key, $post);

                break;

            case 'popupeditor':
                $res = WOOBE_HELPER::draw_popup_editor_btn($val, $field_key, $post);
                break;

            case 'downloads_popup_editor':
                $res = WOOBE_HELPER::draw_downloads_popup_editor_btn($field_key, $product_id);
                break;

            case 'gallery_popup_editor':

                $res = WOOBE_HELPER::draw_gallery_popup_editor_btn($field_key, $product_id);
                break;

            case 'upsells_popup_editor':
                $res = WOOBE_HELPER::draw_upsells_popup_editor_btn($field_key, $product_id);
                break;

            case 'cross_sells_popup_editor':
                $res = WOOBE_HELPER::draw_cross_sells_popup_editor_btn($field_key, $product_id);
                break;

            case 'grouped_popup_editor':
                $res = WOOBE_HELPER::draw_grouped_popup_editor_btn($field_key, $product_id);
                break;

            case 'meta_popup_editor':
                $res = WOOBE_HELPER::draw_meta_popup_editor_btn($field_key, $product_id);
                break;

            case 'thumbnail':
                $thumbnail = wp_get_attachment_image_src($val, 'thumbnail');
                $full = wp_get_attachment_image_src($val, 'full');

                if (!empty($thumbnail)) {
                    $thumbnail = $thumbnail[0];
                    $full = $full[0];
                } else {
                    $thumbnail = WOOBE_ASSETS_LINK . 'images/not-found.jpg';
                    $full = WOOBE_ASSETS_LINK . 'images/not-found.jpg';
                }

                $onmouseover = '';
                if ($this->settings->show_thumbnail_preview) {
                    $onmouseover = 'onmouseover="woobe_init_image_preview(this)"';
                }

                $res = '<a href="' . $full . '" onclick="return woobe_act_thumbnail(this)" ' . $onmouseover . ' title="' . $post['post_title'] . '"><img src="' . $thumbnail . '" class="attachment-thumbnail size-thumbnail" alt="" /></a>';
                break;

            case 'switcher':
                $labels = array_values($this->settings->active_fields[$field_key]['select_options']);
                $values = array_keys($this->settings->active_fields[$field_key]['select_options']);
                if ($val) {//do switcher
                    $val = WOOBE_HELPER::over_switcher_val_to_swicher($val, $field_key);
                }
                $res = WOOBE_HELPER::draw_advanced_switcher(($val == $values[0] ? TRUE : FALSE), $product_id . '_' . $field_key, $field_key, array('true' => $labels[0], 'false' => $labels[1]), array('true' => $values[0], 'false' => $values[1]), 'yes');
                break;

            case 'calendar':
                if ($this->settings->active_fields[$field_key]['type'] === 'timestamp') {
                    $val = strtotime($val);
                }
                $time = true;
                if (in_array($field_key, array('date_on_sale_from', 'date_on_sale_to'))) {
                    $time = false;
                    if ($val) {
                        $val = $val + get_option('gmt_offset') * 3600;
                    }
                }

                $post_title = $post['post_title'];

                if ($post['post_type'] === 'product_variation') {
                    if ($this->settings->add_vars_to_var_title) {
                        $post_title = $this->products->generate_product_title($this->products->get_product($product_id));
                    }
                }

                $res = WOOBE_HELPER::draw_calendar($product_id, $post_title . ' (' . $this->settings->active_fields[$field_key]['title'] . ')', $field_key, $val, "", false, $time);
                break;

            case 'checkbox':
//using for products selection
                $res = WOOBE_HELPER::draw_checkbox(array(
                            'class' => 'woobe_product_check',
                            'data-product-id' => $product_id
                ));
                break;

            default:
                //textinput
                $sanitize = '';
                if (isset($this->settings->active_fields[$field_key]['sanitize'])) {
                    $sanitize = $this->settings->active_fields[$field_key]['sanitize'];
                }

                $res = $this->products->sanitize_answer_value($field_key, $sanitize, $val);

                break;
        }

        //***
        //lets show product ID as LINK to the product
        if ($field_key === 'ID') {
            $class = 'woobe-id-permalink';
            $title = '';

            if ($product->get_type() === 'variable') {
                $class .= ' woobe-id-permalink-var';
                $title = esc_html__('see the product on the site OR select products variations if they are shown', 'woocommerce-bulk-editor');
            }

            $res = '<a href="' . get_permalink($res) . '" class="' . $class . '" title="' . $title . '" target="_blank">' . $res . '</a>';
        }

        //***
        /* for tests
          $woobe_operation_time = get_option('woobe_operation_time', 0);
          if (!$woobe_operation_time) {
          update_option('woobe_operation_time', time());
          }
         */
        //***

        return $res;
    }

//ajax
    public function woobe_delete_tax_term() {
        $term_id = (int) $_REQUEST['term_id'];
        $taxonomy = sanitize_text_field(trim($_REQUEST['tax_key']));
        if (!taxonomy_exists($taxonomy)) {
            die('Wrong taxonomy name.');
        }
        $result = wp_delete_term($term_id, $taxonomy);
        // check the result
        if (is_wp_error($result)) {

            die('error wp_update_term');
        } else {

            echo json_encode(WOOBE_HELPER::get_taxonomies_terms_hierarchy($taxonomy));
        }
        exit;
    }

    public function woobe_update_tax_term() {
        $term_id = (int) $_REQUEST['term_id'];
        $title = sanitize_textarea_field($_REQUEST['title']);
        $slug = sanitize_textarea_field($_REQUEST['slug']);
        $description = sanitize_textarea_field($_REQUEST['description']);
        $parent = (int) $_REQUEST['parent'];
        $taxonomy = sanitize_text_field(trim($_REQUEST['tax_key']));
        if (!taxonomy_exists($taxonomy)) {
            die('Wrong taxonomy name.');
        }

        $result = wp_update_term($term_id, $taxonomy, [
            'name' => $title,
            'slug' => $slug,
            'description' => $description,
            'parent' => $parent
                ]);

        // check the result
        if (is_wp_error($result)) {

            die('error wp_update_term');
        } else {

            echo json_encode(WOOBE_HELPER::get_taxonomies_terms_hierarchy($taxonomy));
        }
        exit;
    }

    public function woobe_create_new_term() {

        $titles = $_REQUEST['titles']; //sanitized in cycle
        $slugs = $_REQUEST['slugs']; //sanitized in cycle
        $description = sanitize_textarea_field($_REQUEST['description']);
        $taxonomy = sanitize_text_field(trim($_REQUEST['tax_key']));

        if (!taxonomy_exists($taxonomy)) {
            die('Wrong taxonomy name.');
        }

        //***

        if (!empty($titles)) {

            if (substr_count($titles, '|') > 0) {
                $titles = explode('|', $titles);
            } else {
                $titles = array($titles);
            }


            if (substr_count($slugs, '|') > 0) {
                $slugs = explode('|', $slugs);
            } else {
                $slugs = array($slugs);
            }

            //***

            $terms_ids = array();
            foreach ($titles as $k => $t) {
                $t = sanitize_text_field(trim($t));
                $sl = sanitize_title_with_dashes(trim($slugs[$k]));

                if (!term_exists($t, $taxonomy)) {
                    if (!empty($t)) {
                        $res = wp_insert_term($t, $taxonomy, array(
                            'parent' => intval($_REQUEST['parent']),
                            'slug' => (boolval($sl) ? $sl : ''),
                            'description' => $description,
                        ));
                        $terms_ids[] = $res['term_id'];
                    } else {
                        unset($titles[$k]);
                    }
                }
            }

            //***

            echo json_encode(array(
                'titles' => array_reverse($titles),
                'terms_ids' => array_reverse($terms_ids),
                'terms' => WOOBE_HELPER::get_taxonomies_terms_hierarchy($taxonomy)
            ));
        }
        exit;
    }

    public function clone_custom_taxonomies($origin, $clone) {
        $taxonomy_objects = get_object_taxonomies('product', 'objects');
        $taxonomies = array();
        $exclude_tax = array('product_type', 'product_visibility', 'product_shipping_class', 'product_cat', 'product_tag');
        foreach ($taxonomy_objects as $key => $taxonomy) {
            if (in_array($key, $exclude_tax)) {
                continue;
            }
            if (taxonomy_is_product_attribute($key)) {
                continue;
            }
            $terms = get_the_terms($origin, $key);
            if ($terms AND is_array($terms)) {
                $taxonomies = $taxonomies + $terms;
            }
        }

        foreach ($taxonomies as $term) {
            wp_set_post_terms($clone, $term->term_id, $term->taxonomy, true);
        }
    }

//do not init functionality on all site pages as it not nessesary
    private function is_should_init() {
//do not onit it exept of one woobe page and its ajax requests
        $init = (isset($_GET['page']) AND $_GET['page'] === 'woobe');

        if (defined('DOING_AJAX')) {
            if (strpos($_REQUEST['action'], 'woobe_') !== FALSE) {
                $init = true;
            }
        }

        return $init;
    }

    public function add_statuses($statuses) {
        return $statuses + [
            'future' => esc_html__('Scheduled', 'woocommerce-bulk-editor')
        ];
    }

    public function ask_favour() {

        if (intval(get_option('woobe_manage_rate_alert', 0)) === -2) {
            //old rate system mark for already set review users
            return;
        }

        $slug = strtolower(get_class($this));

        add_action("wp_ajax_{$slug}_dismiss_rate_alert", function () use ($slug) {
            update_option("{$slug}_dismiss_rate_alert", 2);
        });

        add_action("wp_ajax_{$slug}_later_rate_alert", function () use ($slug) {
            update_option("{$slug}_later_rate_alert", time() + 4 * 7 * 24 * 60 * 60); //4 weeks
        });

        //+++

        add_action('admin_notices', function () use ($slug) {

            if (!current_user_can('manage_options')) {
                return; //show to admin only
            }

            if (intval(get_option("{$slug}_dismiss_rate_alert", 0)) === 2) {
                return;
            }

            if (intval(get_option("{$slug}_later_rate_alert", 0)) === 0) {
                update_option("{$slug}_later_rate_alert", time() + 3 * 24 * 60 * 60); //3 days after install
                return;
            }

            if (intval(get_option("{$slug}_later_rate_alert", 0)) > time()) {
                return;
            }

            $link = 'https://codecanyon.net/downloads#item-21779835';
            $on = 'CodeCanyon';
            if ($this->show_notes) {
                $link = 'https://wordpress.org/support/plugin/woo-bulk-editor/reviews/?filter=5#new-post';
                $on = 'WordPress';
            }
            ?>
            <div class="notice notice-info" id="pn_<?php echo $slug ?>_ask_favour" style="position: relative;">
                <button onclick="javascript: pn_<?php echo $slug ?>_dismiss_review(1);
                                    void(0);" title="<?php esc_html_e('Later', 'woocommerce-bulk-editor'); ?>" class="notice-dismiss"></button>
                <div id="pn_<?php echo $slug ?>_review_suggestion">
                    <p><?php esc_html_e('Hi! Are you enjoying using BEAR - WooCommerce Bulk Editor and Products Manager Professional?', 'woocommerce-bulk-editor'); ?></p>
                    <p><a href="javascript: pn_<?php echo $slug ?>_set_review(1); void(0);"><?php esc_html_e('Yes, I love it', 'woocommerce-bulk-editor'); ?></a> 🙂 | <a href="javascript: pn_<?php echo $slug ?>_set_review(0); void(0);"><?php esc_html_e('Not really...', 'woocommerce-bulk-editor'); ?></a></p>
                </div>

                <div id="pn_<?php echo $slug ?>_review_yes" style="display: none;">
                    <p><?php printf(esc_html__('That\'s awesome! Could you please do us a BIG favor and give it a 5-star rating on %s to help us spread the word and boost our motivation?', 'woocommerce-bulk-editor'), $on) ?></p>
                    <p style="font-weight: bold;">~ PluginUs.NET developers team</p>
                    <p>
                        <a href="<?php echo $link ?>" style="display: inline-block; margin-right: 10px;" onclick="pn_<?php echo $slug ?>_dismiss_review(2)" target="_blank"><?php esc_html_e('Okay, you deserve it', 'woocommerce-bulk-editor'); ?></a>
                        <a href="javascript: pn_<?php echo $slug ?>_dismiss_review(1); void(0);" style="display: inline-block; margin-right: 10px;"><?php esc_html_e('Nope, maybe later', 'woocommerce-bulk-editor'); ?></a>
                        <a href="javascript: pn_<?php echo $slug ?>_dismiss_review(2); void(0);"><?php esc_html_e('I already did', 'woocommerce-bulk-editor'); ?></a>
                    </p>
                </div>

                <div id="pn_<?php echo $slug ?>_review_no" style="display: none;">
                    <p><?php esc_html_e('We are sorry to hear you aren\'t enjoying BEAR. We would love a chance to improve it. Could you take a minute and let us know what we can do better?', 'woocommerce-bulk-editor'); ?></p>
                    <p>
                        <a href="https://pluginus.net/contact-us/" onclick="pn_<?php echo $slug ?>_dismiss_review(2)" target="_blank"><?php esc_html_e('Give Feedback', 'woocommerce-bulk-editor'); ?></a>&nbsp;
                        |&nbsp;<a href="javascript: pn_<?php echo $slug ?>_dismiss_review(2); void(0);"><?php esc_html_e('No thanks', 'woocommerce-bulk-editor'); ?></a>
                    </p>
                </div>


                <script>
                    function pn_<?php echo $slug ?>_set_review(yes) {
                        document.getElementById('pn_<?php echo $slug ?>_review_suggestion').style.display = 'none';
                        if (yes) {
                            document.getElementById('pn_<?php echo $slug ?>_review_yes').style.display = 'block';
                        } else {
                            document.getElementById('pn_<?php echo $slug ?>_review_no').style.display = 'block';
                        }
                    }

                    function pn_<?php echo $slug ?>_dismiss_review(what = 1) {
                        //1 maybe later, 2 do not ask more
                        jQuery('#pn_<?php echo $slug ?>_ask_favour').fadeOut();

                        if (what === 1) {
                            jQuery.post(ajaxurl, {
                                action: '<?php echo $slug ?>_later_rate_alert'
                            });
                        } else {
                            jQuery.post(ajaxurl, {
                                action: '<?php echo $slug ?>_dismiss_rate_alert'
                            });
                        }

                        return true;
                    }
                </script>
            </div>
            <?php
        });
    }

}

//***

function woobe_init_translates() {
    try {
        $lang_domain = 'woocommerce-bulk-editor';
        $lang_dir = WP_CONTENT_DIR . '/languages/plugins/';
        $locale = get_locale();
        unload_textdomain($lang_domain);

        if (is_file("{$lang_dir}{$lang_domain}-{$locale}.mo")) {
            load_textdomain($lang_domain, "{$lang_dir}{$lang_domain}-{$locale}.mo");
        } else {
            if (is_file(WOOBE_PATH . "languages/{$lang_domain}-{$locale}.mo")) {
                load_textdomain($lang_domain, WOOBE_PATH . "languages/{$lang_domain}-{$locale}.mo");
            } else {
                load_plugin_textdomain($lang_domain, false, WOOBE_PATH . 'languages');
            }
        }
    } catch (Exception $e) {
        //+++
    }
}

//***


$WOOBE = new WOOBE();
$GLOBALS['WOOBE'] = $WOOBE;
add_action('init', array($WOOBE, 'init'), 9999);

