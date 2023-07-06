<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_EXPORT extends WOOBE_EXT {

    protected $slug = 'export'; //unique
    private $exlude_keys = array('__checker'); //do not export
    private $max_download_columns = 5; //we cant know how max downloads in one product
    private $csv_delimiter = ',';

    public function __construct() {
        add_action('woobe_ext_scripts', array($this, 'woobe_ext_scripts'), 1);

        //ajax
        add_action('wp_ajax_woobe_export_products_count', array($this, 'woobe_export_products_count'), 1);
        add_action('wp_ajax_woobe_export_products', array($this, 'woobe_export_products'), 1);
        add_action('wp_ajax_woobe_bulk_get_att_terms_export', array($this, 'woobe_bulk_get_att_terms_export'), 1);

        //tabs
        $this->add_tab($this->slug, 'top_panel', esc_html__('Export', 'woocommerce-bulk-editor'), 'export');
        add_action('woobe_ext_top_panel_' . $this->slug, array($this, 'woobe_ext_panel'), 1);
		
		$this->check_export_files();
    }
	
    public function woobe_ext_scripts() {
        wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js', array(), WOOBE_VERSION);
        wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css', array(), WOOBE_VERSION);
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            lang.<?php echo $this->slug ?>.want_to_export = '<?php esc_html_e('Should the export be started?', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.exporting = '<?php esc_html_e('Exporting', 'woocommerce-bulk-editor') ?> ...';
            lang.<?php echo $this->slug ?>.exported = '<?php esc_html_e('Exported', 'woocommerce-bulk-editor') ?> ...';
            lang.<?php echo $this->slug ?>.export_is_going = "<?php echo esc_html__('ATTENTION: Export operation is going!', 'woocommerce-bulk-editor') ?>";

        </script>
        <?php
    }

    public function woobe_ext_panel() {
        $data = array();
        //$data['download_link'] = $this->get_ext_link() . "__exported_files/woobe_exported.csv";
        //$data['download_link_xml'] = $this->get_ext_link() . "__exported_files/woobe_exported.xml";

        $data['download_link'] = $this->get_ext_link() . "__exported_files/";
        $data['active_fields'] = $this->get_active_fields();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

    //ajax
    public function woobe_export_products_count() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        //***
        $active_fields = $this->get_active_fields();

        //***

        if (isset($_REQUEST['download_files_count'])) {//doesn exists if downloads column is not actived
            $download_files_count = intval($_REQUEST['download_files_count']);
            if ($download_files_count > 0) {
                $this->max_download_columns = $download_files_count;
            }
        }

        $this->csv_delimiter = sanitize_text_field($_REQUEST['csv_delimiter']);
        $file_postfix = sanitize_text_field($_REQUEST['file_postfix']);

        //***

        $folder = $this->get_ext_path() . "__exported_files/";
        //clean folder
        array_map('unlink', array_filter((array) glob("{$folder}*")));


        switch ($_REQUEST['format']) {
            case 'csv':

                if (!empty($active_fields)) {
                    $file_path = $folder . "woobe_exported{$file_postfix}.csv";
                    $fp = fopen($file_path, "w");
                    $titles = array();
                    $attribute_index = 1; //for attributes columns

                    foreach ($active_fields as $field_key => $field) {
                        if (!in_array($field_key, $this->exlude_keys)) {

                            switch ($field['field_type']) {
                                case 'attribute':
                                    //making comapatibility with native woocommerce csv importer
                                    //wp-admin/edit.php?post_type=product&page=product_importer
                                    $titles[] = '"Attribute ' . $attribute_index . ' value(s)"';
                                    $titles[] = '"Attribute ' . $attribute_index . ' name"';
                                    $titles[] = '"Attribute ' . $attribute_index . ' visible"';
                                    $titles[] = '"Attribute ' . $attribute_index . ' global"';
                                    $attribute_index++;

                                    break;

                                case 'downloads':
                                    for ($i = 0; $i < $this->max_download_columns; $i++) {
                                        $titles[] = '"Download ' . ($i + 1) . ' name"';
                                        $titles[] = '"Download ' . ($i + 1) . ' URL"';
                                    }
                                    break;

                                case 'meta':
                                    $titles[] = '"Meta: ' . $field_key . '"';
                                    break;

                                default:
                                    $titles[] = '"' . $field['title'] . '"'; //head titles
                                    break;
                            }
                        }
                    }

                    //***

                    $titles = implode($this->csv_delimiter, $titles);
                    fputs($fp, $titles . $this->csv_delimiter . PHP_EOL);
                    fclose($fp);
                }


                break;
            case 'xml':
                //die(json_encode($active_fields));
                if (!empty($active_fields)) {
                    $file_path = $folder . "woobe_exported{$file_postfix}.xml";
                    $dom = new domDocument("1.0", "utf-8");

                    $rss = $dom->createElementNS('http://wordpress.org/export/1.2/excerpt/', 'rss');
                    $dom->appendChild($rss);
                    $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:wp', 'http://wordpress.org/export/1.2/');
                    $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
                    $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:wfw', 'http://wellformedweb.org/CommentAPI/');
                    $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:excerpt', 'http://wordpress.org/export/1.2/excerpt/');
                    $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dc', 'http://purl.org/dc/elements/1.1/');

                    $root = $dom->createElement("channel");
                    $rss->appendChild($root);
                    /**/
                    $title = $dom->createElement("title", get_bloginfo('name'));
                    $root->appendChild($title);
                    $link = $dom->createElement('link', get_bloginfo('url'));
                    $root->appendChild($link);
                    $description = $dom->createElement('description', get_bloginfo('description'));
                    $root->appendChild($description);
                    $pubDate = $dom->createElement('pubDate', date('r'));
                    $root->appendChild($pubDate);


                    $author = $dom->createElement("wp:author");
                    $root->appendChild($author);
                    $current_user = wp_get_current_user();
                    $author_id = $dom->createElement('wp:author_id', $current_user->ID);
                    $author->appendChild($author_id);
                    $author_login = $dom->createElement('wp:author_login', $current_user->user_login);
                    $author->appendChild($author_login);
                    $author_email = $dom->createElement('wp:author_email', $current_user->user_email);
                    $author->appendChild($author_email);

                    $author_cdata = $dom->createCDATASection($current_user->display_name);
                    $author_display_name = $dom->createElement('wp:author_display_name');
                    $author_element = $author->appendChild($author_display_name);
                    $author_element->appendChild($author_cdata);

                    $author_cdata = $dom->createCDATASection($current_user->user_firstname);
                    $author_first_name = $dom->createElement('wp:author_first_name');
                    $author_element = $author->appendChild($author_first_name);
                    $author_element->appendChild($author_cdata);

                    $author_cdata = $dom->createCDATASection($current_user->user_lastname);
                    $author_last_name = $dom->createElement('wp:author_last_name');
                    $author_element = $author->appendChild($author_last_name);
                    $author_element->appendChild($author_cdata);


                    $dom->save($file_path);
                }
                break;

            case 'excel':
                //todo
                break;
        }


        if (!isset($_REQUEST['no_filter'])) {
            //get count of filtered - doesn work if export is for checked products
            $products = $this->products->gets(array(
                'fields' => 'ids',
                'no_found_rows' => true
            ));
            echo json_encode($products->posts);
        }

        exit;
    }

    //ajax
    public function woobe_export_products() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        //***

        $behavior = 1;
        if (isset($_REQUEST['behavior']) AND intval($_REQUEST['behavior']) == 0) {
            $behavior = 0;
        }

        $download_files_count = intval($_REQUEST['download_files_count']);
        if ($download_files_count > 0) {
            $this->max_download_columns = $download_files_count;
        }

        $this->csv_delimiter = sanitize_text_field($_REQUEST['csv_delimiter']);
        $file_postfix = sanitize_text_field($_REQUEST['file_postfix']);

        $combination = array();
        if (isset($_REQUEST['combination'])) {
            $combination = WOOBE_HELPER::sanitize_array((array) $_REQUEST['combination']);
        }

        //***
//die(json_encode($combination ));
        if (!empty($_REQUEST['products_ids'])) {
            switch ($_REQUEST['format']) {
                case 'csv':
                    $file = $this->get_ext_path() . "__exported_files/woobe_exported{$file_postfix}.csv";
                    $fp = fopen($file, 'a+');
                    $products_ids = array();
                    //$variations_ids = array();
                    //add variations for var products
                    foreach ($_REQUEST['products_ids'] as $product_id) {
                        $product_id = intval($product_id);

                        $products_ids[] = $product_id;
                        $product = $this->products->get_product($product_id);
                        if ($product->is_type('variable')) {
                            $variations = $product->get_children();
                            if (!empty($variations)) {

                                if (!empty($combination) AND is_array($combination)) {
                                    $variations_var = $variations;
                                    $variations = array();
                                    foreach ($variations_var as $var_id) {
                                        $variation = $this->products->get_product($var_id);
                                        $attributes = $variation->get_attributes();

                                        //***

                                        $go = FALSE;
                                        if (!$behavior) {
                                            $go = TRUE;
                                        }
                                        //***

                                        if (!empty($attributes)) {
                                            foreach ($combination as $comb) {
                                                //lets look is $attributes the same set of attributes as in $comb
                                                $ak_att = array_keys($attributes);
                                                $ak_cv = array_keys($comb);

                                                //fix for non-latin symbols
                                                if (!empty($ak_att)) {
                                                    $ak_att = array_map('urldecode', $ak_att);
                                                }

                                                //fix for non-latin symbols
                                                if (!empty($ak_cv)) {
                                                    $ak_cv = array_map('urldecode', $ak_cv);
                                                }

                                                sort($ak_att);
                                                sort($ak_cv);

                                                if ($ak_att === $ak_cv) {
                                                    $av_att = array_values($attributes);
                                                    $av_cv = array_values($comb);

                                                    //fix for non-latin symbols
                                                    if (!empty($ak_att)) {
                                                        $av_att = array_map('urldecode', $av_att);
                                                    }

                                                    if (!empty($av_cv)) {
                                                        $av_cv = array_map('urldecode', $av_cv);
                                                    }

                                                    sort($av_att);
                                                    sort($av_cv);
                                                    if ($av_att === $av_cv) {
                                                        $go = TRUE;
                                                        if (!$behavior) {
                                                            $go = FALSE;
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        //***

                                        if ($go) {
                                            $variations[] = $var_id;
                                        }
                                    }
                                }

                                $products_ids = array_merge($products_ids, $variations);
                            }
                        }
                    }

                    //***

                    foreach ($products_ids as $product_id) {
                        $product_id = intval($product_id);
                        fputcsv($fp, $this->get_product_fields($product_id, $this->get_active_fields()), $this->csv_delimiter);
                    }

                    fclose($fp);
                    break;
                case 'xml':
                    $file = $this->get_ext_path() . "__exported_files/woobe_exported{$file_postfix}.xml";
                    $dom = new DOMDocument("1.0", "utf-8");
                    $dom->load($file);
                    $rss = $dom->firstChild;
                    $root = $rss->firstChild;
                    foreach ($_REQUEST['products_ids'] as $product_id) {
                        $product_id = intval($product_id);

                        $item = $dom->createElement("item");
                        $root->appendChild($item);
                        //die(json_encode($this->get_meta_for_xml($product_id, $this->get_active_fields())));
                        //wp data
                        //die(json_encode($this->get_category_for_xml($product_id, $this->get_active_fields())));


                        $wp_data = $this->get_post_data_for_xml($product_id, $this->get_active_fields());
                        foreach ($wp_data as $key => $val) {
                            if (in_array($key, array('content:encoded', 'excerpt:encoded'))) {
                                $wp_cdata = $dom->createCDATASection($val);
                                $data_item = $dom->createElement($key);
                                $wp_data = $item->appendChild($data_item);
                                $wp_data->appendChild($wp_cdata);
                            } else {
                                $data_item = $dom->createElement($key, $val);
                                $item->appendChild($data_item);
                            }
                        }
                        // tax data
                        $tax_data = $this->get_category_for_xml($product_id, $this->get_active_fields());
                        foreach ($tax_data as $key => $val) {
                            if (is_array($val)) {
                                foreach ($val as $term) {
                                    $tax_cdata = $dom->createCDATASection($term->name);
                                    $tax_item = $dom->createElement("category");
                                    $tax_data = $item->appendChild($tax_item);
                                    $tax_data->appendChild($tax_cdata);
                                    $domain = $dom->createAttribute('domain');
                                    $nicename = $dom->createAttribute('nicename');
                                    $domain->value = $key;
                                    $nicename->value = $term->slug;
                                    $tax_item->appendChild($domain);
                                    $tax_item->appendChild($nicename);
                                }
                            }
                        }

                        //meta data
                        $meta = $this->get_meta_for_xml($product_id, $this->get_active_fields());
                        foreach ($meta as $key => $val) {
                            if (is_array($val)) {
                                $val = serialize($val);
                            }
                            $meta_item = $dom->createElement("wp:postmeta");
                            $item->appendChild($meta_item);

                            $meta_key = $dom->createElement('wp:meta_key', $key);
                            $meta_item->appendChild($meta_key);

                            $meta_cdata = $dom->createCDATASection($val);
                            $meta_val = $dom->createElement('wp:meta_value');
                            $meta_data = $meta_item->appendChild($meta_val);
                            $meta_data->appendChild($meta_cdata);
                        }
                    }
                    $dom->save($file);
                    break;
                case 'excel':
                    //todo
                    break;

                default:
                    break;
            }
        }


        die('done');
    }

    private function get_meta_for_xml($product_id, $fields) {
        $data = array();
        foreach ($fields as $field_key => $field) {
            if (isset($field['meta_key']) AND!empty($field['meta_key'])) {
                $val = $this->products->get_post_field($product_id, $field_key);
                if (is_array($val)) {
                    $val = serialize($val);
                }
                $data[$field['meta_key']] = $val;
            }
        }
        $data['_product_attributes'] = get_post_meta($product_id, '_product_attributes', true);
        return $data;
    }

    private function get_post_data_for_xml($product_id, $fields) {

        $data = array(
            'wp:post_type' => "product",
            'dc:creator' => 'shopmanager',
            'wp:is_sticky' => 0,
            'description' => '',
            'wp:post_password' => '',
            'wp:post_parent' => 0,
        );
        $aloved_fields = array(
            'ID' => 'wp:post_id',
            'post_title' => 'title',
            'post_content' => 'content:encoded',
            'post_excerpt' => 'excerpt:encoded',
            'post_date' => 'wp:post_date',
            'post_name' => 'wp:post_name',
            'post_status' => 'wp:status',
            'menu_order' => 'wp:menu_order'
        );
        foreach ($fields as $field_key => $field) {
            if (isset($aloved_fields[$field_key])) {
                $val = $this->products->get_post_field($product_id, $field_key);
                if ($field_key == 'post_date') {
                    $data['pubDate'] = $val;
                }
                $data[$aloved_fields[$field_key]] = $val;
            }
        }

        return $data;
    }

    private function get_category_for_xml($product_id, $fields) {
        $data = array();
        foreach ($fields as $field_key => $field) {
            if (isset($field['taxonomy']) OR!empty($field['taxonomy'])) {
                $data[$field['taxonomy']] = $this->products->get_post_field($product_id, $field_key);
            }
            if (isset($field['attribute']) OR!empty($field['attribute'])) {
                $data[$field['attribute']] = $this->products->get_post_field($product_id, $field_key);
            }
            if ($field_key == 'catalog_visibility') {
                $data['product_visibility'] = wp_get_post_terms($product_id, 'product_visibility');
            }
        }
        return $data;
    }

    private function get_product_fields($product_id, $fields) {
        $answer = array();
        if (!empty($fields)) {
            global $wc_product_attributes;
            foreach ($fields as $field_key => $field) {
                if (!in_array($field_key, $this->exlude_keys)) {

                    $a = $this->filter_fields_vals($this->products->get_post_field($product_id, $field_key), $field_key, $field, $product_id);


                    switch ($field['field_type']) {
                        case 'attribute':

                            $answer[] = $a;

                            if (!empty($a)) {
                                //making comapatibility with native woocommerce csv importer
                                //wp-admin/edit.php?post_type=product&page=product_importer

                                $answer[] = $field['title'];
                                //$p = $this->products->get_product($product_id);
                                if (isset($wc_product_attributes[$field_key]) AND!$this->products->get_product($product_id)->is_type('variation')) {
                                    $answer[] = $wc_product_attributes[$field_key]->attribute_public; //visibility
                                } else {
                                    $answer[] = ''; //visibility
                                }

                                //wp-content\plugins\woocommerce\includes\export\class-wc-product-csv-exporter.php -> protected function prepare_attributes_for_export
                                $answer[] = intval(!empty($field['name'])); //global https://clip2net.com/s/3QWWy0a
                            } else {
                                $answer[] = '';
                                $answer[] = '';
                                $answer[] = '';
                            }

                            break;

                        case 'downloads':
                            if (!empty($a)) {
                                foreach ($a as $v) {
                                    $answer[] = $v;
                                }
                            } else {
                                //2 because there are 2 columns: name and URL
                                for ($i = 0; $i < 2 * $this->max_download_columns; $i++) {
                                    $answer[] = '';
                                }
                            }
                            break;

                        default:
                            $answer[] = $a;
                            break;
                    }
                }
            }
        }

        return $answer;
    }

    //values replaces to the human words
    private function filter_fields_vals($value, $field_key, $field, $product_id) {
        $words = array(
                /*
                  'draft' => esc_html__('draft', 'woocommerce-bulk-editor'),
                  'publish' => esc_html__('publish', 'woocommerce-bulk-editor'),
                 */
        ); //do not translate as it keys!!
        //***

        switch ($field['field_type']) {
            case 'taxonomy':

                if (is_array($value) AND!empty($value)) {
                    $tmp = array();
                    if (in_array($field['taxonomy'], array('product_type'/* , 'product_shipping_class' */))) {
                        foreach ($value as $term) {
                            $tmp[] = $term->slug;
                        }
                        $value = implode(',', $tmp);
                    } else {
                        foreach ($value as $term) {
                            $tmp[] = $term->term_id;
                        }
                        include_once( WC_ABSPATH . 'includes/export/class-wc-product-csv-exporter.php' );
                        $woo_csv_exporter = new WC_Product_CSV_Exporter();
                        //wp-content\plugins\woocommerce\includes\export\abstract-wc-csv-exporter.php -> public function format_term_ids
                        $value = $woo_csv_exporter->format_term_ids($tmp, $field['taxonomy']);
                    }
                } else {
                    $value = '';
                }

                //***
                //fix for product_type
                if ($field['taxonomy'] === 'product_type') {
                    $product = $this->products->get_product($product_id);

                    if ($product->is_type('variation')) {
                        $value = 'variation';
                    }

                    if ($product->is_downloadable()) {
                        $value .= ', downloadable';
                    }

                    if ($product->is_virtual()) {
                        $value .= ', virtual';
                    }
                }


                break;

            case 'attribute':

                if (is_array($value) AND!empty($value)) {
                    $tmp = array();
                    foreach ($value as $term_id) {
                        $tmp[] = get_term_field('name', $term_id);
                    }
                    $value = $this->implode_values($tmp);
                } else {
                    $value = '';
                }

                break;

            case 'gallery':
                if (!empty($value)) {
                    $tmp = array();
                    foreach ($value as $image_id) {
                        $image = wp_get_attachment_image_src($image_id, 'full');
                        if ($image) {
                            $tmp[] = $image[0];
                        }
                    }

                    $value = $this->implode_values($tmp);
                } else {
                    $value = '';
                }
                break;

            case 'meta':
                //just especially for thumbnail only
                if ($field['edit_view'] == 'thumbnail') {
                    $image = wp_get_attachment_image_src($value, 'full');
                    if ($image) {
                        $value = $image[0];
                    }
                }

                if ($field['edit_view'] == 'meta_popup_editor') {
                    if (!empty($value)) {
                        $value = json_encode($value, JSON_HEX_QUOT | JSON_HEX_TAG);
                    }
                }

                break;

            case 'downloads':
                $tmp = array();

                if (!empty($value)) {
                    foreach ($value as $f) {
                        $tmp[] = $f['name'];
                        $tmp[] = $f['file'];
                    }

                    //***

                    if (count($tmp) < $this->max_download_columns * 2) {
                        for ($i = count($tmp); $i < $this->max_download_columns * 2; $i++) {
                            $tmp[] = ''; //fill empty columns to avoid data shifting in csv
                        }
                    }
                }

                $value = $tmp;
                break;

            case 'upsells':
            case 'cross_sells':
            case 'grouped':
                if (!empty($value)) {
                    $tmp = array();
                    foreach ($value as $p_id) {
                        $product = $this->products->get_product($p_id);
                        $sku = '';
                        if (is_object($product) AND method_exists($product, 'get_sku')) {
                            $sku = $product->get_sku();
                            if (!empty($sku)) {
                                $tmp[] = $sku;
                            } else {
                                $tmp[] = 'id:' . $p_id;
                            }
                        }
                    }

                    $value = implode(',', $tmp);
                } else {
                    $value = '';
                }

                break;

            case 'prop':

                if ($field_key == 'backorders') {
                    switch ($value) {
                        case 'notify' :
                            $value = 'notify';
                            break;
                        default :
                            $value = wc_string_to_bool($value) ? 1 : 0;
                            break;
                    }
                }

                //***

                if ($field_key == 'stock_status') {
                    $value = ('instock' == $value ? 1 : 0);
                }

                //***

                if ($field['type'] == 'number') {
                    if ($field['sanitize'] == 'floatval') {
                        $value = floatval($value);
                    }

                    if ($field['sanitize'] == 'intval') {
                        $value = intval($value);
                    }
                }

                //***

                if ($field['type'] == 'timestamp') {
                    if (!empty($value)) {
                        $value = preg_replace('([+-](\d+):(\d+))', ' ', $value, 1);
                        $value = date('Y-m-d', strtotime($value)) . ' 0:00:00';
                    } else {
                        $value = '';
                    }
                }

                //***

                if ($field['edit_view'] == 'switcher') {
                    $value = intval($value);
                }

                break;

            case 'field':
                if ($field_key == 'post_status') {

                    if ($value == 'publish') {
                        $value = 1;
                    }

                    if ($value == 'draft') {
                        $value = -1;
                    }

                    if ($value == 'private') {
                        $value = 0;
                    }
                }

                if ($field_key == 'post_parent') {
                    $value = intval($value);
                    if ($value > 0) {
                        $value = 'id:' . $value;
                    }
                }
                break;
        }

        return $value;
    }

    //**********************************************************************

    private function implode_values($values) {
        $values_to_implode = array();

        foreach ($values as $value) {
            $value = (string) is_scalar($value) ? $value : '';
            $values_to_implode[] = str_replace(',', '\\,', $value);
        }

        return implode(', ', $values_to_implode);
    }

    public function get_active_fields() {
        static $fields_observed = array(); //cache

        if (empty($fields_observed)) {
            $fields_observed = $this->settings->active_fields;
            //Parent - post_parent - for variations is absolutely nessesary!!
            foreach ($fields_observed as $f) {
                if ($f['field_type'] == 'attribute' AND!isset($fields_observed['post_parent'])) {
                    $fields_observed['post_parent'] = woobe_get_fields()['post_parent'];
                    break;
                }
            }
        }


        return $fields_observed;
    }

    //ajax
    public function woobe_bulk_get_att_terms_export() {

        $drop_downs = '';
        if (!empty($_REQUEST['attributes'])) {
            foreach ($_REQUEST['attributes'] as $pa) {
                $pa = sanitize_text_field($pa);

                $terms = WOOBE_HELPER::get_taxonomies_terms_hierarchy($pa);
                if (!empty($terms)) {
                    $options = array();
                    $options[''] = esc_html__('not selected', 'woocommerce-bulk-editor');
                    foreach ($terms as $t) {
                        $options[$t['slug']] = $t['name'];
                    }

                    $drop_downs .= WOOBE_HELPER::draw_select(array(
                                'field' => 0,
                                'product_id' => 0,
                                'class' => '',
                                'options' => $options,
                                'name' => $pa
                    ));
                }
            }
        }

        die($drop_downs);
    }
	public function check_export_files() {
		$transient = 'woobe_time_last_check';
		$max_age = 3600 * 24 * 2;
		$last_check = get_transient($transient);
		if (!$last_check) {
			$last_check = 0;
		}

		$over_time = $last_check + $max_age;
		
		if ($over_time < time()) {
			$this->delete_old_export_files($max_age);
			$last_check = set_transient($transient, time());
			return;
		}
		
		
	}	
	public function delete_old_export_files($max_age) {
		$list = array();

		$limit = time() - $max_age;
		$dir = $this->get_ext_path() . "__exported_files/";
		$dir = realpath($dir);

		if (!is_dir($dir)) {
			return;
		}

		$dh = opendir($dir);
		if ($dh === false) {
			return;
		}

		while (($file = readdir($dh)) !== false) {
			$file = $dir . '/' . $file;
			if (!is_file($file)) {
				continue;
			}

			if (filemtime($file) < $limit) {
				$list[] = $file;
				unlink($file);
			}
		}
		closedir($dh);
	}

}
