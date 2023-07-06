<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');



//0 - radio, 1 - checkbox
$select_radio_check_type = (int) $this->settings['select_radio_check_type'][$tax_slug];
$select_radio_check_height = (int) $this->settings['select_radio_check_height'][$tax_slug];
?>

<dl class="woof_select_radio_check">

    <dt>
        <a href="javascript: void(0);" class="woof_select_radio_check_opener">
            <span class="woof_hida woof_hida_<?php echo esc_attr($tax_slug) ?>" data-title="<?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info)) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info)) ?></span>    
            <p class="woof_multiSel"></p>  
        </a>
    </dt>

    <dd>
        <div class="woof_mutliSelect woof_no_close_childs" data-height="<?php echo esc_attr($select_radio_check_height) ?>">
            <?php
            $args = array();
            $args['taxonomy_info'] = $taxonomies_info[$tax_slug];
            $args['tax_slug'] = $tax_slug;
            $args['terms'] = $terms;
            $args['additional_taxes'] = '';
            if (isset($additional_taxes)) {
                $args['additional_taxes'] = $additional_taxes;
            }
            //***

            $args['woof_settings'] = get_option('woof_settings', array());
            $args['show_count'] = get_option('woof_show_count', 0);
            $args['show_count_dynamic'] = get_option('woof_show_count_dynamic', 0);
            $args['hide_dynamic_empty_pos'] = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos', 0);

            if ($select_radio_check_type && !woof()->show_notes) {
                $this->render_html_e(apply_filters('woof_html_types_view_checkbox', WOOF_PATH . 'views/html_types/checkbox.php'), $args);
            } else {
                $this->render_html_e(apply_filters('woof_html_types_view_radio', WOOF_PATH . 'views/html_types/radio.php'), $args);
            }
            ?>            
        </div>
    </dd>
</dl>
