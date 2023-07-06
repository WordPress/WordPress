<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php  ?>
<div data-css-class="woof_author_search_container" class="woof_author_search_container woof_container woof_container_woof_author">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">

        <?php
        if (!isset($view) OR empty($view)) {
            $view = (isset(woof()->settings['by_author']['view'])) ? woof()->settings['by_author']['view'] : 'drop-down';
        }

        $args = array(
            'role' => '',
            'meta_key' => '',
            'meta_value' => '',
            'meta_compare' => '',
            'meta_query' => array(),
            'date_query' => array(),
            'include' => array(),
            'exclude' => array(),
            'orderby' => 'login',
            'order' => 'ASC',
            'offset' => '',
            'search' => '',
            'number' => '',
            'count_total' => false,
            'fields' => 'all',
            'who' => ''
        );

        if (isset($role) AND!empty($role)) {
            $args['role'] = $role;
        }
        $authors_title = array();
        $authors = get_users($args);
        $request = woof()->get_request_data();
        $woof_author = '';
        if (isset($request['woof_author'])) {
            $woof_author = $request['woof_author'];
        }
        //+++
        $p = esc_html__('Select a product author', 'woocommerce-products-filter');

        if (isset($placeholder) AND!empty($placeholder)) {
            $p = $placeholder;
        } else {
            if (isset(woof()->settings['by_author']['placeholder'])) {
                if (!empty(woof()->settings['by_author']['placeholder'])) {
                    $p = woof()->settings['by_author']['placeholder'];
                    $p = WOOF_HELPER::wpml_translate(null, $p);
                    $p = esc_html__($p, 'woocommerce-products-filter');
                }
            }
        }



        //***
        $unique_id = uniqid('woof_author_search_');


        switch ($view) {
            case 'checkbox':
                ?>
                <?php $woof_author = explode(",", $woof_author); ?>
                <<?php echo esc_attr(apply_filters('woof_title_tag', 'h4')); ?>><?php esc_html_e($p) ?></<?php echo esc_attr(apply_filters('woof_title_tag', 'h4')); ?>>
                <div data-css-class="woof_checkbox_authors_container" class="woof_checkbox_authors_container">
                    <div class="woof_container_overlay_item"></div>
                    <div class="woof_container_inner">
                        <ul class='woof_authors '>
                            <?php foreach ($authors as $user): ?>
                                <li>
                                    <input type="checkbox" class="woof_checkbox_author" id="woof_checkbox_author_<?php echo esc_attr($user->data->ID) ?>" name="woof_author[]" value="<?php echo esc_attr($user->data->ID) ?>" <?php checked(in_array($user->data->ID, $woof_author)) ?> />&nbsp;&nbsp;
									<label for="woof_checkbox_author_<?php echo esc_attr($user->data->ID) ?>"><?php esc_html_e($user->data->display_name) ?></label>
                                    <?php
                                    if (in_array($user->data->ID, $woof_author)) {
                                        $authors_title[] = $user;
                                    }
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php
                break;
            default :
                ?>
                <select name="woof_author" class="woof_select woof_show_author_search <?php echo esc_attr($unique_id) ?>" data-uid="<?php echo esc_attr($unique_id) ?>">
                    <option value="0"><?php esc_html_e($p) ?></option>
                    <?php if (!empty($authors)): ?>
                        <?php foreach ($authors as $user): ?>
                            <option <?php selected($woof_author, $user->data->ID); ?> value="<?php echo esc_html($user->data->ID) ?>"><?php esc_html_e($user->data->display_name) ?></option>
                            <?php
                            if ($user->data->ID == $woof_author) {
                                $authors_title[] = $user;
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php
                break;
        }
        if (!empty($authors_title)) {
            foreach ($authors_title as $user) {
                ?>
                <input type="hidden" value="<?php esc_html_e('Author:', 'woocommerce-products-filter') ?><?php esc_html_e($user->data->display_name) ?>" data-anchor="woof_n_woof_author_<?php echo esc_attr($user->data->ID) ?>" />
                <?php
            }
        }
        ?>               
    </div>
</div>
