<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');



//***
if (is_user_logged_in() AND isset(woof()->settings['query_save'])) {

    $query_count = 2;
    $p = "";
    $adding_class = "";
    if ($in_filter != true) {
        $adding_class = "query_save_shortcode";
    }
    $show_notice = 0;
    if (isset(woof()->settings['query_save'])AND!empty(woof()->settings['query_save'])) {
        $query_count = woof()->settings['query_save']['search_count'];
        $p = woof()->settings['query_save']['label'];
        $placeholder = woof()->settings['query_save']['placeholder'];
        $btn_label = woof()->settings['query_save']['btn_label'];

        if (isset(woof()->settings['query_save']['show_notice'])AND!empty(woof()->settings['query_save']['show_notice'])) {
            $show_notice = woof()->settings['query_save']['show_notice'];
        }
    }

    $cur_user_id = get_current_user_id();
    $user_data_queries = get_user_meta($cur_user_id, 'woof_user_search_query', true);
    ?>
    <div data-css-class="woof_query_save_container" class="woof_query_save_container woof_container <?php echo esc_attr($adding_class) ?>">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
            <<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
            <?php
            echo esc_html(WOOF_HELPER::wpml_translate(null, $p));
            ?> 
            </<?php echo esc_html(apply_filters('woof_title_tag', 'h4')); ?>>
            <?php
            if (!is_array($user_data_queries)) {
                $user_data_queries = array();
            }
            ?>
            <div class="woof_query_save_list">
                <ul> 
                    <?php
                    $counter = 1;
                    foreach ($user_data_queries as $data) {

                        $data['show_notice'] = $show_notice;
                        $this->render_html_e(WOOF_EXT_PATH . 'query_save/views/item_list_query.php', $data);
                        $counter++;
                    }
                    ?>
                </ul>
            </div> 
            <?php
            $show_btn = false;
            $get_array = woof()->get_request_data();

            if (isset(woof()->settings['items_order'])) {
                $key_array = explode(',', woof()->settings['items_order']);
                $by_only_array = array('woof_text', 'stock', 'onsales', 'woof_sku', 'product_visibility');
                $tax_array = array_keys(woof()->settings['excluded_terms']);
                foreach ($tax_array as &$item) {
                    $item = woof()->check_slug($item);
                }
                $key_array = array_merge($by_only_array, $key_array, $tax_array);

                $real_query = array_intersect(array_keys($get_array), $key_array);
                if (count($real_query)) {
                    $show_btn = true;
                }

                $meta_filter = array();
                if (isset(woof()->settings['meta_filter']) AND is_array(woof()->settings['meta_filter'])) {
                    $meta_filter = woof()->settings['meta_filter'];
                }
                foreach ($meta_filter as $item) {
                    $key = $item['search_view'] . "_" . $item['meta_key'];
                    if (in_array($key, array_keys($get_array))) {
                        $show_btn = true;
                    }
                }
            }

            if ($show_btn OR class_exists("WOOF_EXT_TURBO_MODE")) { // hide btn without search query
                $visible = 'none';
                if ($query_count > count($user_data_queries)) {
                    $visible = 'block';
                }
                ?>
                <div class="woof_add_query_count" style="display: <?php echo esc_attr($visible) ?>" >
                    <input name="title_query_save"  type="text"  class="woof_save_query_title"  value="" placeholder="<?php esc_html_e($placeholder) ?>" >
                    <div class="woof_query_save_title_error"><?php esc_html_e('Please fill the title field.', 'woocommerce-products-filter') ?></div>
                    <input name="add_query_save" data-count="<?php echo esc_attr($query_count) ?>" type="button" class="woof_add_query_save" data-user="<?php echo esc_attr($cur_user_id) ?>" value="<?php echo esc_html($btn_label) ?>"  >
                </div>

            <?php } ?>
            <?php if (isset($this->settings['query_save']['notes_for_customer']) AND !empty($this->settings['query_save']['notes_for_customer'])): ?>
                <span class="woof_query_save_notes_for_customer"><?php echo do_shortcode(wp_kses_post(wp_unslash($this->settings['query_save']['notes_for_customer']))) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

