<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOBE;
?>
<h4 class="woobe-documentation"><a href="https://bulk-editor.com/document/history/" target="_blank" class="button button-primary"><span class="icon-book"></span></a>&nbsp;<?php esc_html_e('History', 'woocommerce-bulk-editor') ?></h4>
<div class="woobe_alert"><?php esc_html_e('Works for edit-operations and not work with delete-operations! Also does not work with all operations which are presented in "Variations Advanced Bulk Operations"', 'woocommerce-bulk-editor') ?></div>

<?php if ($WOOBE->show_notes) : ?>
    <div class="woobe_set_attention woobe_alert"><?php esc_html_e('In FREE version of the plugin it is possible to roll back 2 last operations.', 'woocommerce-bulk-editor') ?></div><br />
<?php endif; ?>


<div class="col-lg-6">
    <label for="woobe_history_pagination_number"><?php esc_html_e('Per page:', 'woocommerce-bulk-editor') ?></label>
    <select style="width: 50px;" id="woobe_history_pagination_number">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="-1"><?php esc_html_e('ALL', 'woocommerce-bulk-editor') ?></option>
    </select>
</div>
<div class="col-lg-6 tar">
    <a href="javascript: woobe_history_clear();void(0);" class="button button-primary"><?php esc_html_e('Clear the History', 'woocommerce-bulk-editor') ?></a>
</div>
<div class="clear"></div>
<div class="col-lg-12 woobe_history_pagination_cont">

    <div class="col-lg-12 woobe_history_filters">
        <div class="col-lg-2">
            <select id="woobe_history_show_types">
                <option value="0"><?php esc_html_e('all', 'woocommerce-bulk-editor') ?></option>
                <option value="1"><?php esc_html_e('solo operations', 'woocommerce-bulk-editor') ?></option>
                <option value="2"><?php esc_html_e('bulk operations', 'woocommerce-bulk-editor') ?></option>
            </select>
        </div>
        <div class="col-lg-2" >
            <?php
            $opt_auth = array();
            $opt_auth[-1] = esc_html__('by Author', 'woocommerce-bulk-editor');
            $opt_auth = $opt_auth + WOOBE_HELPER::get_users();
            ?>
            <?php
            echo WOOBE_HELPER::draw_select(array(
                'options' => $opt_auth,
                'field' => '',
                'product_id' => "author",
                'class' => 'woobe_history_filter_author chosen-select',
                'name' => '',
                'field' => 'woobe_history_filter'
            ));
            ?>
        </div>
        <div class="col-lg-2" >
            <input type="text" onmouseover="woobe_init_calendar(this)" data-title="<?php esc_html_e('by date from', 'woocommerce-bulk-editor') ?>" data-val-id="woobe_history_filter_date_from" value="" class="woobe_calendar" placeholder="<?php esc_html_e('by date from', 'woocommerce-bulk-editor') ?>" />
            <input type="hidden" data-key="from" data-product-id="" id="woobe_history_filter_date_from" value=""  />            
            <a href="#" class="woobe_calendar_clear" data-val-id="woobe_history_filter_date_from" ><?php echo esc_html__('clear', 'woocommerce-bulk-editor') ?></a>
        </div>
        <div class="col-lg-2" >
            <input type="text" onmouseover="woobe_init_calendar(this)" data-title="<?php esc_html_e('by date to', 'woocommerce-bulk-editor') ?>" data-val-id="woobe_history_filter_date_to" value="" class="woobe_calendar" placeholder="<?php esc_html_e('by date to', 'woocommerce-bulk-editor') ?>" />
            <input type="hidden" data-key="from" data-product-id="" id="woobe_history_filter_date_to" value=""  />
            <a href="#" class="woobe_calendar_clear" data-val-id="woobe_history_filter_date_to"><?php echo esc_html__('clear', 'woocommerce-bulk-editor') ?></a>
        </div>
        <div class="col-lg-2" >
            <input type="text" id="woobe_history_filter_field" placeholder="<?php esc_html_e('by fields, use comma', 'woocommerce-bulk-editor') ?>" >
        </div>     
        <div class="col-lg-2" >
            <input type="button" id="woobe_history_filter_submit" class="button button-primary" value="<?php esc_html_e('Filter', 'woocommerce-bulk-editor') ?>">&nbsp;<input type="button" class="button button-primary" id="woobe_history_filter_reset" value="<?php esc_html_e('Reset', 'woocommerce-bulk-editor') ?>">
        </div>


    </div>
</div>    
<div class="clear"></div>

<div id="woobe_history_list_container"></div>

<div id="woobe_history_pagination_container">
    <div>
        <a href="#"class="woobe_history_pagination_prev button button-primary"><span class="icon-left"></span><?php esc_html_e('Prev', 'woocommerce-bulk-editor') ?></a>
    </div>
    <div>
        <span class="woobe_history_pagination_current_count"></span><?php esc_html_e('of', 'woocommerce-bulk-editor') ?><span class="woobe_history_pagination_count"></span>
    </div>
    <div>
        <a href="#" class="woobe_history_pagination_next button button-primary"><?php esc_html_e('Next', 'woocommerce-bulk-editor') ?><span class="icon-right"></span></a>
    </div>
</div>

