<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');


if (isset(woof()->settings['by_rating']) AND woof()->settings['by_rating']['show']) {
    $as_star = 0;
    if (isset(woof()->settings['by_rating']['use_star'])) {
        $as_star = woof()->settings['by_rating']['use_star'];
    }
    ?>
    <div data-css-class="woof_by_rating_container" class="woof_by_rating_container woof_container">
        <div class="woof_container_overlay_item"></div>
        <?php
        $request = woof()->get_request_data();
        $selected = woof()->is_isset_in_request_data('min_rating') ? $request['min_rating'] : 0;
        $select_id = "woof_select_range";
        ?>
        <div class="woof_container_inner <?php echo esc_attr($as_star ? "woof_star_selected" : "") ?>">
            <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($select_id) ?>"><?php esc_html_e('Filter by rating', 'woocommerce-products-filter') ?></label>
            <select id="<?php echo esc_attr($select_id) ?>" class="woof_by_rating_dropdown woof_select" name="min_rating">
                <?php
                $vals = array(
                    0 => esc_html__('Filter by rating', 'woocommerce-products-filter'),
                    4 => esc_html__('average rating between 4 to 5', 'woocommerce-products-filter'),
                    3 => esc_html__('average rating between 3 to 4-', 'woocommerce-products-filter'),
                    2 => esc_html__('average rating between 2 to 3-', 'woocommerce-products-filter'),
                    1 => esc_html__('average rating between 1 to 2-', 'woocommerce-products-filter')
                );
                if ($as_star) {
                    $vals = array(
                        0 => esc_html__('Filter by rating', 'woocommerce-products-filter'),
                        4 => esc_html__('SSSSS', 'woocommerce-products-filter'),
                        3 => esc_html__('SSSS', 'woocommerce-products-filter'),
                        2 => esc_html__('SSS', 'woocommerce-products-filter'),
                        1 => esc_html__('SS', 'woocommerce-products-filter')
                    );
                }
                ?>
                <?php foreach ($vals as $key => $value): ?>
                    <option <?php selected($selected, $key); ?> <?php echo (($key !== 0 AND $as_star) ? "class='woof_star_font'" : "") ?> value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" value="<?php esc_html_e('Min rating: ', 'woocommerce-products-filter') ?><?php echo esc_html($selected) ?>" data-anchor="woof_n_<?php echo esc_attr("min_rating") ?>_<?php echo esc_attr($selected) ?>" />
        </div>
    </div>
    <?php
}


