<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//17-12-2022
final class WOOF_SD extends WOOF_EXT {

    public $ext_version = '1.0.0';
    public $type = 'html_type'; //application, html_type
    public $html_type = 'woof_sd_'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi';
    public $folder_name = 'smart_designer';
    public $options = [];
    private $types = [];

    public function __construct() {
        parent::__construct();
        include_once $this->get_ext_path() . 'classes/presets.php';
        include_once $this->get_ext_path() . 'classes/color.php';

        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . 'woof_sd';
        $this->types = [
            'checkbox' => esc_html__('Checkbox', 'woocommerce-products-filter'),
            'radio' => esc_html__('Radio', 'woocommerce-products-filter'),
            'switcher' => esc_html__('Switcher', 'woocommerce-products-filter'),
            'color' => esc_html__('Color', 'woocommerce-products-filter')
        ];

        $this->init_outer_elements();
        $this->init();
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

    private function init_outer_elements() {
        $this->outer_templates = [];
        $this->outer_ext_dir = get_stylesheet_directory() . '/woof/ext/smart_designer/';
        $this->outer_templates_dir = $this->outer_ext_dir . 'elements/';
        $this->outer_ext_link = get_stylesheet_directory_uri() . '/woof/ext/smart_designer/';
        $this->outer_templates_link = get_stylesheet_directory_uri() . '/woof/ext/smart_designer/elements/';

        if (!is_dir($this->outer_templates_dir)) {
            return;
        }

        $dirs = [];
        $handle = opendir($this->outer_templates_dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $dirs[] = $entry;
            }
        }
        closedir($handle);

        //+++

        if (!empty($dirs)) {
            foreach ($dirs as $dir_name) {
                $path_to_data = $this->outer_templates_dir . $dir_name . '/data.php';

                if (!is_file($path_to_data)) {
                    continue;
                }

                $this->outer_templates[$dir_name] = [
                    'data' => include $path_to_data,
                    'script' => $this->outer_templates_link . $dir_name . '/ie.js', //interactive element
                    'styles' => $this->outer_templates_link . $dir_name . '/css/styles.css',
                    'templates' => $this->outer_templates_dir . $dir_name . '/templates/'
                ];

                self::$includes['css']["woof_sd_html_items_{$dir_name}"] = $this->outer_templates[$dir_name]['styles'];
                $this->types[$dir_name] = $this->outer_templates[$dir_name]['data']['title'];
            }

            //***
            $file = $this->outer_ext_dir . 'import.js';
            $create_import_js = false;
            $is_file = is_file($file);
            $check_string = "import IE from '{$this->get_ext_link()}js/ie/ie.js'";

            if ($is_file) {//file exists
                //check fot domain actuality (if site migrated)
                if (strpos(file_get_contents($file), $check_string) === false) {
                    $create_import_js = true;
                }
            } else {
                $create_import_js = true;
            }

            if ($create_import_js) {
                file_put_contents($file, "
                import IE from '{$this->get_ext_link()}js/ie/ie.js';
                import Helper from '{$this->get_ext_link()}js/helper.js';
                export {IE, Helper};
            ");
            }

            //***

            $file = $this->get_ext_path() . 'js/import-outer.js';
            $create_import_js = false;
            $is_file = is_file($file);
            $exts = implode(',', array_keys($this->outer_templates));
            $check_string = "/* ext link is: {$this->outer_templates_link}; exts: {$exts} */";

            if ($is_file) {//if file exists
                //check fot domain actuality (if site migrated)
                if (strpos(file_get_contents($file), $check_string) === false) {
                    $create_import_js = true;
                }
            } else {
                $create_import_js = true;
            }

            if ($create_import_js) {
                $js_import_code = $check_string . PHP_EOL;
                $js_import_code_obj = '';

                foreach ($dirs as $dir_name) {
                    if (isset($this->outer_templates[$dir_name])) {
                        $js_class = $this->outer_templates[$dir_name]['data']['js_class'];
                        $js_import_code .= "import {$js_class} from '{$this->outer_templates_link}{$dir_name}/js/ie.js';" . PHP_EOL;
                        $js_import_code_obj .= $dir_name . ': ' . $js_class . ',';
                    }
                }

                $js_import_code .= PHP_EOL;
                $js_import_code .= "let modules = {";
                $js_import_code .= trim($js_import_code_obj, ', ');
                $js_import_code .= "};";
                $js_import_code .= PHP_EOL;
                $js_import_code .= "export {modules};";

                file_put_contents($file, $js_import_code);
            }
        }
    }

    public function init() {
        add_action('woof_print_applications_tabs_' . $this->folder_name, [$this, 'woof_print_applications_tabs'], 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, [$this, 'woof_print_applications_tabs_content'], 10, 1);

        add_filter('woof_add_html_types', function ($types) {
            $elements = $this->get_elements();

            if (!empty($elements)) {
                foreach ($elements as $el) {
                    $types['woof_sd_' . $el['id']] = 'SD: ' . $el['title'];
                }
            }

            return $types;
        });

        $self = $this;
        add_action('woof_taxonomy_type_objects_front_render', function ($is, $html_type, $tax_slug, $args)use ($self) {
            if ($self->html_type && $html_type === $self->html_type) {
                if (substr($tax_slug, 0, strlen($self->html_type)) === $html_type) {
                    $is = true;
                    $args['woof_sd_id'] = $id = intval(str_replace('woof_sd_', '', $tax_slug));
                    $type = $this->get_element_type($id);

                    if (isset($this->outer_templates[$type])) {
                        $args['sd_data'] = $this->outer_templates[$type]['data'];
                    } else {
                        $file = $self->get_ext_path() . "data/{$type}.php";
                        if (is_file($file)) {
                            $args['sd_data'] = include $file;
                        }
                    }

                    $args['sd_data_prefix'] = $prefix = isset($args['sd_data']['prefix']) ? $args['sd_data']['prefix'] : '';
                    $args['sd_options'] = $self->get_element_options($id, $prefix);

                    if (!empty($args['sd_options']) AND!empty($args['sd_data']['sections'])) {
                        foreach ($args['sd_options'] as $el_key => $el_value) {
                            foreach ($args['sd_data']['sections'] as $section_num => $section) {
                                foreach ($section['table']['rows'] as $key => $value) {
                                    if ($key === $el_key) {
                                        $args['sd_data']['sections'][$section_num]['table']['rows'][$key][0]['value']['value'] = $el_value;
                                    }
                                }
                            }
                        }
                    }

                    $args['element_additional_data'] = $self->get_element_additional_data(array_keys($args['sd_options']), $type);
                    $template_num = $args['sd_template_num'] = $this->get_element_template_num($id);

                    if (!isset($args['sd_data']['templates'][$template_num])) {
                        //check if template for current element type exists
                        $template_num = $args['sd_template_num'] = 0;
                    }

                    if (isset($this->outer_templates[$type])) {
                        $file = $this->outer_templates[$type]['templates'] . "tpl.php";
                        if ($template_num > 0) {
                            $file = $this->outer_templates[$type]['templates'] . "tpl-{$template_num}.php";
                        }
                    } else {
                        $file = $this->get_ext_path() . "templates/{$type}.php";
                        if ($template_num > 0) {
                            $file = $this->get_ext_path() . "templates/{$type}-{$template_num}.php";
                        }
                    }

                    $args['sd_template'] = null;
                    if (is_file($file)) {
                        //$args['sd_template'] = file_get_contents($file);//PHP not executed
                        ob_start();
                        include($file);
                        $args['sd_template'] = ob_get_clean();
                    }

                    $args['sd_template_num'] = $template_num;
                    $args['sd_type'] = $type;
                }
            }

            return ['is' => $is, 'args' => $args];
        }, 10, 4);

        //+++

        add_action('wp_enqueue_scripts', array($this, 'wp_head'), 2);

        self::$includes['css']['woof_sd_html_items_checkbox'] = $this->get_ext_link() . 'css/elements/checkbox.css';
        self::$includes['css']['woof_sd_html_items_radio'] = $this->get_ext_link() . 'css/elements/radio.css';
        self::$includes['css']['woof_sd_html_items_switcher'] = $this->get_ext_link() . 'css/elements/switcher.css';
        self::$includes['css']['woof_sd_html_items_color'] = $this->get_ext_link() . 'css/elements/color.css';

        self::$includes['css']['woof_sd_html_items_tooltip'] = $this->get_ext_link() . 'css/tooltip.css';
        self::$includes['css']['woof_sd_html_items_front'] = $this->get_ext_link() . 'css/front.css';
        self::$includes['js']['woof_sd_html_items'] = $this->get_ext_link() . 'js/front.js';

        add_action('wp_ajax_woof_sd_boot', function () {
            $rows_raw = $this->get_elements();
            $rows = [];

            if (!empty($rows_raw)) {
                foreach ($rows_raw as $rr) {
                    if (!$rr['options']) {
                        $rr['options'] = [];
                    }

                    $rows[(string) $rr['id']] = [
                        'title' => ['value' => $rr['title']],
                        'type' => ['value' => $rr['type']],
                        //draw_row_actions is custom created function in js class Row
                        'actions' => ['value' => '', 'draw_content' => 'draw_row_actions', 'classes' => 'woof-sd-edit-row']
                    ];
                }
            }

            //list of elements
            $data = [
                'header' => [
                    ['value' => esc_html__('Title', 'woocommerce-products-filter'), 'width' => '45%', 'editable' => 1, 'key' => 'title', 'action' => 'woof_sd_change_title'],
                    ['value' => esc_html__('Type', 'woocommerce-products-filter'), 'width' => '45%', 'key' => 'type'],
                    ['value' => esc_html__('Actions', 'woocommerce-products-filter'), 'width' => '10%']
                ],
                'rows' => $rows,
            ];

            die(json_encode($data));
        });

        add_action('wp_ajax_woof_sd_get_options', function () {
            $type = esc_html($_REQUEST['type']);
            $id = intval($_REQUEST['id']);
            $data = [];

            if (isset($this->outer_templates[$type])) {
                $data = $this->outer_templates[$type]['data'];
            } else {
                $file = $this->get_ext_path() . "data/{$type}.php";
                if (is_file($file)) {
                    $data = include $file;
                }
            }

            if (empty($data)) {
                die('-1');
            }

            $prefix = isset($data['prefix']) ? $data['prefix'] : '';
            $element_options = $this->get_element_options($id, $prefix);

            //set data values from DB
            if (!empty($element_options) AND!empty($data['sections'])) {
                foreach ($element_options as $el_key => $el_value) {
                    foreach ($data['sections'] as $section_num => $section) {
                        foreach ($section['table']['rows'] as $key => $value) {
                            if ($key === $el_key) {
                                $data['sections'][$section_num]['table']['rows'][$key][0]['value']['value'] = $el_value;
                            }
                        }
                    }
                }
            }

            if ($_REQUEST['change_type']) {
                $this->db->update($this->table, array('type' => $type), array('id' => $id));
            }

            $data['types'] = $this->types;
            $data['template'] = $this->get_element_template_num($id);
            if (!isset($data['templates'][$data['template']])) {
                //check if template for current element type exists
                $data['template'] = 0; //fix for switcher which has only one template: 0
            }
            $data['selected_demo_taxonomy'] = $this->get_selected_demo_taxonomy($id);

            //demo terms
            $data['demo_taxonomies_terms'] = $this->get_terms($data['selected_demo_taxonomy'], intval($data['templates'][$data['template']]['use_subterms']));
            $data['demo_taxonomies'] = array_merge($data['demo_taxonomies'], $this->get_product_taxonomies());

            echo json_encode($data);
            exit;
        });

        add_action('wp_ajax_woof_sd_change_template', function () {
            $template = esc_html($_REQUEST['template']);
            $id = intval($_REQUEST['id']);
            $this->db->update($this->table, array('template' => $template), array('id' => $id));
        });

        add_action('wp_ajax_woof_sd_change_demo_taxonomy', function () {
            $taxonomy = esc_html($_REQUEST['taxonomy']);
            $id = intval($_REQUEST['id']);
            $this->db->update($this->table, array('demo_taxonomy' => $taxonomy), array('id' => $id));
            echo json_encode($this->get_terms($taxonomy));
            exit;
        });

        add_action('wp_ajax_woof_sd_create_element', function () {
            $this->db->insert($this->table, [
                'title' => esc_html($_REQUEST['title'])
            ]);

            $response = [
                'id' => $this->db->insert_id,
                'html_types' => apply_filters('woof_add_html_types', woof()->html_types)
            ];

            echo json_encode($response);
            exit;
        });

        add_action('wp_ajax_woof_sd_change_title', function () {
            $id = intval($_REQUEST['id']);
            $title = sanitize_text_field($_REQUEST['title']);
            $this->db->update($this->table, array('title' => $title), array('id' => $id));

            $response = [
                'id' => $id,
                'html_types' => apply_filters('woof_add_html_types', woof()->html_types)
            ];

            echo json_encode($response);
            exit;
        });

        add_action('wp_ajax_woof_sd_update_option', function () {
            $id = intval($_REQUEST['id']);
            $key = sanitize_text_field($_REQUEST['key']);
            $value = sanitize_text_field($_REQUEST['value']);

            $options = $this->get_element_options($id);
            $options[$key] = $value;
            $options = json_encode($options);

            $this->db->update($this->table, array('options' => $options), array('id' => $id));
            exit;
        });

        add_action('wp_ajax_woof_sd_reset', function () {
            $this->db->update($this->table, array('options' => NULL), array('id' => intval($_REQUEST['id'])));
            exit;
        });

        add_action('wp_ajax_woof_sd_delete_row', function () {
            $this->db->delete($this->table, array('id' => intval($_REQUEST['id'])));
            exit;
        });
    }

    public function wp_head() {
        wp_enqueue_style('woof-sd-switcher23', $this->get_ext_link() . 'css/elements/switcher.css', [], WOOF_VERSION);
    }

    public function get_elements() {
        $elements = $this->db->get_results("SELECT * FROM {$this->table} ORDER BY title asc", ARRAY_A);

        if (function_exists('woof') && woof() && woof()->show_notes) {
            if (!empty($elements)) {
                $elements = [$elements[0]];
            }
        }

        return $elements;
    }

    private function get_element_type($id) {
        return $this->db->get_var("SELECT type FROM {$this->table} WHERE id={$id}");
    }

    private function get_element_template_num($id) {
        return intval($this->db->get_var("SELECT template FROM {$this->table} WHERE id={$id}"));
    }

    private function get_selected_demo_taxonomy($id) {
        return $this->db->get_var("SELECT demo_taxonomy FROM {$this->table} WHERE id={$id}");
    }

    private function get_element_options($id, $prefix = '') {
        $row = $this->db->get_row("SELECT options FROM {$this->table} WHERE id={$id}", ARRAY_A);
        $options = [];

        if ($row) {
            $options = $row['options'];
            if (!$options) {
                $options = [];
            } else {
                $options = json_decode($options, true);
                //remove prefix
                if (!empty($prefix)) {
                    $tmp = [];
                    foreach ($options as $key => $value) {
                        $tmp[str_replace($prefix, '', $key)] = $value;
                    }
                    $options = $tmp;
                }
            }
        }

        return $options;
    }

    private function get_element_additional_data($keys, $type) {
        $additional_data = [];
        $data = [];

        if (isset($this->outer_templates[$type])) {
            $data = $this->outer_templates[$type]['data'];
        } else {
            $file = $this->get_ext_path() . "data/{$type}.php";
            if (is_file($file)) {
                $data = include $file;
            }
        }

        if (!empty($data)) {
            foreach ($data['sections'] as $section) {
                foreach ($section['table']['rows'] as $key => $value) {
                    $additional_data[$key]['measure'] = isset($value[0]['measure']) ? $value[0]['measure'] : '';
                    $additional_data[$key]['before'] = isset($value[0]['before']) ? $value[0]['before'] : '';
                    $additional_data[$key]['after'] = isset($value[0]['after']) ? $value[0]['after'] : '';
                }
            }
        }

        return $additional_data;
    }

    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-sd">
                <span class="icon-brush"></span>
                <span><?php esc_html_e('Smart Designer', 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        $data = [];
        $data['ext_version'] = $this->ext_version;
        $data['woof_settings'] = $this->woof_settings;

        if (!is_dir(get_stylesheet_directory() . '/woof/ext/smart_designer/elements/')) {
            //if user changed template, or removed woof folder from theme, 
            //or uploaded file in  plugin folder has data
            file_put_contents($this->get_ext_path() . 'js/import-outer.js', '');
        }

        //boot
        wp_enqueue_script('woof_sd_admin', $this->get_ext_link() . 'js/admin.js', [], WOOF_VERSION);
        wp_enqueue_script('woof_sd_boot', $this->get_ext_link() . 'js/boot.js', [], WOOF_VERSION);
        $this->script_loader_tag(); //!!

        wp_enqueue_style('woof_sd_checkbox', $this->get_ext_link() . 'css/elements/checkbox.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_radio', $this->get_ext_link() . 'css/elements/radio.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_switcher', $this->get_ext_link() . 'css/elements/switcher.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_color', $this->get_ext_link() . 'css/elements/color.css', [], WOOF_VERSION);

        if (!empty($this->outer_templates)) {
            foreach ($this->outer_templates as $key => $template) {
                wp_enqueue_style("woof_sd_{$key}", $template['styles'], [], WOOF_VERSION);
            }
        }

        wp_enqueue_style('woof_sd_growl', $this->get_ext_link() . 'css/growl.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_admin', $this->get_ext_link() . 'css/admin.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_ranger', $this->get_ext_link() . 'css/ranger.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_switcher23', $this->get_ext_link() . 'css/switcher.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_table', $this->get_ext_link() . 'css/table.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_popup', $this->get_ext_link() . 'css/popup.css', [], WOOF_VERSION);
        wp_enqueue_style('woof_sd_tooltip', $this->get_ext_link() . 'css/tooltip.css', [], WOOF_VERSION);

        wp_localize_script('woof_sd_admin', 'woof_sd', [
            'url' => "{$this->get_ext_link()}",
            'outer_elements_url' => "{$this->outer_templates_link}",
            'lang' => [
                'sure_apply_preset' => esc_html__('Are you sure you want to apply selected preset?', 'woocommerce-products-filter'),
                'sure_delete_preset' => esc_html__('Are you sure you want to delete selected preset?', 'woocommerce-products-filter'),
                'sure' => esc_html__('Are you sure?', 'woocommerce-products-filter'),
                'loading' => esc_html__('Loading', 'woocommerce-products-filter'),
                'loaded' => esc_html__('Loaded!', 'woocommerce-products-filter'),
                'saving' => esc_html__('Saving', 'woocommerce-products-filter'),
                'saved' => esc_html__('Saved', 'woocommerce-products-filter'),
                'create_new_el' => esc_html__('Create Element', 'woocommerce-products-filter'),
                'back' => esc_html__('Back', 'woocommerce-products-filter'),
                'new_el' => esc_html__('New element', 'woocommerce-products-filter'),
                'reset' => esc_html__('Reset', 'woocommerce-products-filter'),
                'presets' => esc_html__('Presets', 'woocommerce-products-filter'),
                'preset_code' => esc_html__('Preset code (copy to share)', 'woocommerce-products-filter'),
                'preset_import' => esc_html__('Import new preset code', 'woocommerce-products-filter'),
                'no_items' => esc_html__('No items', 'woocommerce-products-filter'),
                'preset_placeholder' => esc_html__('Enter title and press Enter keyboard to save options of the current element as preset', 'woocommerce-products-filter'),
                'creating' => esc_html__('creating', 'woocommerce-products-filter'),
                'set_terms_color' => esc_html__('Set terms color/image', 'woocommerce-products-filter'),
                'terms_color' => esc_html__('Terms color', 'woocommerce-products-filter'),
                'assign_terms_color' => esc_html__('To assign color value to terms select appropriate demo taxonomy here!', 'woocommerce-products-filter'),
                'select_image' => esc_html__('Select image', 'woocommerce-products-filter'),
                'term_image' => esc_html__('Select image for term', 'woocommerce-products-filter'),
                'about_presets' => esc_html__('Here you can save options for the current element to use for future purposes on elements of the same type!', 'woocommerce-products-filter'),
                'error1' => esc_html__('Such type doesn exists!', 'woocommerce-products-filter'),
                'import' => esc_html__('Import', 'woocommerce-products-filter')
            ]]
        );

        $charset_collate = '';
        if (method_exists($this->db, 'has_cap') AND $this->db->has_cap('collation')) {
            if (!empty($this->db->charset)) {
                $charset_collate = "DEFAULT CHARACTER SET {$this->db->charset}";
            }
            if (!empty($this->db->collate)) {
                $charset_collate .= " COLLATE {$this->db->collate}";
            }
        }
        //***
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `title` text,
                        `type` varchar(32) NOT NULL DEFAULT 'checkbox',
                        `template` int(2) DEFAULT 0,
                        `demo_taxonomy` varchar(96) DEFAULT '0',
                        `options` text,
                        PRIMARY KEY (`id`)
                      )  {$charset_collate};";

        if ($this->db->query($sql) === false) {
            $data['error'] = esc_html__("HUSKY cannot create database table for smart designer! Make sure that your mysql user has the CREATE privilege! Do it manually using your host panel and phpmyadmin!", 'woocommerce-products-filter');
            $data['last_error'] = $this->db->last_error;
            $data['sql'] = $sql;
        }

        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    private function script_loader_tag() {
        add_filter('script_loader_tag', function ($tag, $handle, $src) {

            if ('woof_sd_boot' === $handle) {
                $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
            }

            return $tag;
        }, 10, 3);
    }

    private function get_product_taxonomies() {
        $taxonomy_objects = get_object_taxonomies('product', 'objects');
        $taxonomies = [];
        $exclude_tax = ['product_type', 'product_visibility', 'product_shipping_class'];
        foreach ($taxonomy_objects as $key => $taxonomy) {
            if (in_array($key, $exclude_tax)) {
                continue;
            }

            $taxonomies[$key] = $taxonomy->label;
        }

        return $taxonomies;
    }

    private function get_terms($tax_slug, $use_subterms = true) {
        $terms = [
            23 => [
                'title' => 'XS',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ],
            11 => [
                'title' => 'S',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ],
            17 => [
                'title' => 'M',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ],
            2022 => [
                'title' => 'L',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ],
            777 => [
                'title' => 'XL',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ],
            14 => [
                'title' => 'XXL',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ],
            146 => [
                'title' => 'XXXL',
                'count' => random_int(1, 999),
                'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
                'image' => ''
            ]
        ];

        if ($tax_slug) {
            $terms = $this->assemble_terms(WOOF_HELPER::get_terms($tax_slug, false, $use_subterms));
        }

        return $terms;
    }

    //for admin part
    private function assemble_terms($data, $max_count = 30) {
        $terms = [];

        if (!empty($data)) {
            foreach ($data as $term_id => $term) {

                if (!$max_count) {
                    //avoid big heap of terms
                    break;
                }

                $terms[$term_id] = [
                    'title' => trim($term['name']),
                    'count' => trim($term['count']),
                    'color' => trim(apply_filters('get_woof_sd_term_color', $term_id)),
                    'image' => trim(apply_filters('get_woof_sd_term_color_image', $term_id))
                ];

                if (isset($term['childs'])) {
                    $terms[$term_id]['childs'] = $this->assemble_terms($term['childs']);
                }

                --$max_count;
            }
        }

        return $terms;
    }

}

//!!to show in main menu lets add it as also as application
WOOF_EXT::$includes['applications']['sd'] = WOOF_EXT::$includes['taxonomy_type_objects']['sd'] = new WOOF_SD();

