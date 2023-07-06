<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOBE;
?>


<div class="notice notice-warning">
    <p>
        <?php printf(esc_html__('Bulk editing will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?>
    </p>
</div>

<form method="post" id="woobe_bulk_form">

    <div style="display: none;" id="woobe_show_variations_mode">
        <div class="notice notice-warning">
            <p>
                <?php esc_html_e('Bulk operations will be applied to the variations of the products, and their parent-products will be ignored!', 'woocommerce-bulk-editor') ?>
            </p>
        </div>


        <?php $combination_attributes = wc_get_attribute_taxonomies(); ?>

        <?php if (!empty($combination_attributes)): ?>
            <hr />

            <select id="woobe_bulk_combination_attributes" multiple="" class="chosen-select" style="width: 350px;" data-placeholder="<?php esc_html_e('select combination of attributes', 'woocommerce-bulk-editor') ?>">
                <?php foreach ($combination_attributes as $a) : ?>
                    <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                <?php endforeach; ?>
            </select>&nbsp;<a href="#" id="woobe_bulk_add_combination_to_apply" class="button button-primary button"><?php esc_html_e('Add attributes combination to apply on', 'woocommerce-bulk-editor') ?></a><br />

            <ul id="woobe_bulk_to_var_combinations_apply"></ul>

            <small><?php esc_html_e('Select combination(s) of attributes if you need it by your logic, another way changes will be applied to all variations of all/filtered/selected variable products. Order of the terms in combination(s) has no matter.', 'woocommerce-bulk-editor') ?></small><br />

            <br />
            <hr />
            <br />

        <?php else: ?>

            <strong><?php
                printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                            'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                            'title' => esc_html__('here', 'woocommerce-bulk-editor')
                )));
                ?></strong>

        <?php endif; ?>

    </div>


    <div class="woobe-tabs woobe-tabs-style-shape">
        <nav>
            <ul>
                <li class="tab-current">
                    <a href="#woobe-bulk-basic" onclick="return woobe_init_js_intab('tabs-woobe-bulk-basic')"><span class="icon-database"></span><?php esc_html_e('Basic', 'woocommerce-bulk-editor') ?></a>
                </li>
                <li>
                    <a href="#woobe-bulk-additional" onclick="return woobe_init_js_intab('tabs-woobe-bulk-additional')"><span class="icon-link"></span><?php esc_html_e('Additional', 'woocommerce-bulk-editor') ?></a>
                </li>
            </ul>
        </nav>
        <div class="content-wrap">
            <section id="woobe-bulk-basic" class="content-current">

                <div class="col-lg-12">
                    <ul class="woobe_filter_form_texts">
                        <li>
                            <small><?php esc_html_e('text', 'woocommerce-bulk-editor') ?></small><br />
                            <?php woobe_bulk_draw_text(apply_filters('woobe_bulk_text', $text_keys)); ?>
                        </li>
                    </ul>
                </div>



                <br />
                <hr class="clear" />

                <div class="col-lg-12">
                    <ul class="woobe_filter_form_texts">
                        <li>
                            <small><?php esc_html_e('numeric', 'woocommerce-bulk-editor') ?></small><br />
                            <?php woobe_bulk_draw_nums(apply_filters('woobe_bulk_number', $num_keys)); ?>
                        </li>
                    </ul>
                </div>

                <br />
                <hr class="clear" />

                <div class="col-lg-12">
                    <ul class="woobe_filter_form_texts">
                        <li>
                            <small><?php esc_html_e('statuses and types', 'woocommerce-bulk-editor') ?></small><br />
                            <?php woobe_bulk_draw_other(apply_filters('woobe_bulk_other', $other_keys)); ?>
                        </li>
                    </ul>
                </div>

                <br />
                <hr class="clear" />

                <div class="col-lg-12">
                    <ul class="woobe_filter_form_texts">
                        <li>
                            <small><?php esc_html_e('taxonomies', 'woocommerce-bulk-editor') ?></small><br />
                            <?php woobe_bulk_draw_taxonomies(); ?>
                        </li>
                    </ul>
                </div>

                <div class="clear"></div>

            </section>

            <section id="woobe-bulk-additional">
                <div class="col-lg-12">
                    <ul class="woobe_filter_form_texts">
                        <li><?php woobe_bulk_draw_add1($settings_fields); ?></li>
                    </ul>
                </div>

                <br />
                <hr class="clear" />
            </section>
        </div>
    </div>



</form>

<div class="clear"></div>
<br />
<div class="clear"></div>
<div class="woobe_progress" style="display: none;">
    <div class="woobe_progress_in" id="woobe_bulk_progress">0%</div>
</div>
<br />
<hr />


<div class="fl">
    <a href="#" class="button button-primary button-large" id="woobe_bulk_products_btn" style="display: none;"><?php esc_html_e('Do Bulk Edit', 'woocommerce-bulk-editor') ?></a>
    <a href="#" class="button button-primary woobe_bulk_terminate" title="<?php esc_html_e('terminate bulk operation', 'woocommerce-bulk-editor') ?>" style="display: none;"><?php esc_html_e('terminate bulk operation', 'woocommerce-bulk-editor') ?></a>
</div>


<div class="woobe_delete_wraper fr">




    <?php if ($WOOBE->show_notes): ?>
        <input type="checkbox" disabled="" value='0'>
        <label><?php esc_html_e('Bulk deleting', 'woocommerce-bulk-editor') ?>  (<small class="woobe-free-version"><?php esc_html_e('premium version', 'woocommerce-bulk-editor') ?></small>)</label><br />
        <a href="#" disabled='disabled' class="button button-primary button-large"><?php esc_html_e('Delete products!', 'woocommerce-bulk-editor') ?></a>
    <?php else: ?>
        <input type="checkbox" id="woobe_bulk_delete_products_btn_fuse" value='1'>
        <label for="woobe_bulk_delete_products_btn_fuse"><?php esc_html_e('Bulk deleting', 'woocommerce-bulk-editor') ?></label><br />
        <a href="#" disabled='disabled' class="button button-primary button-large" id="woobe_bulk_delete_products_btn" ><?php esc_html_e('Delete products!', 'woocommerce-bulk-editor') ?></a>
    <?php endif; ?>



</div>

<div class="clear"></div>

<hr />
<h4><?php esc_html_e('Notes', 'woocommerce-bulk-editor') ?>:</h4>
<ul>
    <?php if ($WOOBE->show_notes) : ?>
        <li class="woobe_set_attention">* <?php esc_html_e('In red containers wrapped fields which are not possible modify in bulk in FREE version of the plugin!', 'woocommerce-bulk-editor') ?><br /></li>
    <?php endif; ?>

    <li>* <?php esc_html_e('In the case of an aborted bulk-operation you can roll back changes in the tab History', 'woocommerce-bulk-editor') ?><br /></li>
    <li>* <?php
        printf(esc_html__('Time by time (one time per week for example) - make the backup of your site database. For example by %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(
                        array(
                            'href' => 'https://wordpress.org/plugins/wp-migrate-db/',
                            'title' => esc_html__('this plugin', 'woocommerce-bulk-editor'),
                            'target' => '_blank'
                        )
                ))
        ?><br /></li>
    <li>

        <a href="https://bulk-editor.com/document/woocommerce-products-bulk-edit/" target="_blank" class="button button-primary woobe_btn_order"><span class="icon-book"></span>&nbsp;<?php esc_html_e('Documentation', 'woocommerce-bulk-editor') ?></a>

    </li>
</ul><br />
<br />

<!-------------------------------------------------------------------->
<?php

function woobe_bulk_draw_text($bulk_fields) {
    global $WOOBE;
    $fields = $WOOBE->settings->get_fields();
    ?>
    <?php foreach ($bulk_fields as $field_key => $field) : ?>
        <?php
        if ($fields[$field_key]['edit_view'] == 'gallery_popup_editor') {
            continue;
        }
        ?>
        <?php
        if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
            if (intval($WOOBE->settings->get_shop_manager_visibility()[$field_key]) === 0) {
                continue;
            }
        }
        ?>

        <?php if ($fields[$field_key]['edit_view'] == 'meta_popup_editor'): ?>
            <div class="col-lg-4">
                <div class='filter-unit-wrap <?php echo $fields[$field_key]['css_classes'] ?> <?php if (!$fields[$field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                    <div class="col-lg-1">
                        <div class="height4"></div>
                        <?php if ($fields[$field_key]['direct']): ?>
                            <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $field_key ?>]" class="bulk_checker" data-field-key="<?php echo $field_key ?>" data-title="<?php echo $fields[$field_key]['title'] ?>" value="1" /><br />
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-11">

                        <div>
                            <?php echo WOOBE_HELPER::draw_meta_popup_editor_btn($field_key, 0, $fields[$field_key]['title']); ?>
                        </div>

                        <input type="hidden" name="woobe_bulk[<?php echo $field_key ?>][value]" value="" />
                        <input type="hidden" name="woobe_bulk[<?php echo $field_key ?>][behavior]" value="new" />
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-lg-4">
                <div class='filter-unit-wrap <?php echo $field['css_classes'] ?> <?php if (!$fields[$field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                    <div class="col-lg-1">
                        <div class="height4"></div>

                        <?php if ($fields[$field_key]['direct']): ?>
                            <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $field_key ?>]" class="bulk_checker" data-field-key="<?php echo $field_key ?>" data-title="<?php echo $field['title'] ?>" value="1" /><br />
                        <?php endif; ?>

                    </div>
                    <div class="col-lg-7">
                        <input type="text" class="woobe_bulk_value" disabled="" placeholder="<?php echo $field['title'] ?>" name="woobe_bulk[<?php echo $field_key ?>][value]" value="" />
                    </div>
                    <div class="col-lg-2">
                        <select class="woobe_bulk_add_special_key" disabled="">
                            <option value="-1"><?php esc_html_e('variable', 'woocommerce-bulk-editor') ?></option>
                            <option value="{TITLE}"><?php esc_html_e('TITLE', 'woocommerce-bulk-editor') ?></option>
                            <option value="{ID}">ID</option>
                            <option value="{SKU}"><?php esc_html_e('SKU', 'woocommerce-bulk-editor') ?></option>
                            <option value="{MENU_ORDER}"><?php esc_html_e('MENU ORDER', 'woocommerce-bulk-editor') ?></option>
                            <option value="{PARENT_ID}"><?php esc_html_e('PARENT ID', 'woocommerce-bulk-editor') ?></option>
                            <option value="{PARENT_TITLE}"><?php esc_html_e('PARENT TITLE', 'woocommerce-bulk-editor') ?></option>
                            <option value="{PARENT_SKU}"><?php esc_html_e('PARENT SKU', 'woocommerce-bulk-editor') ?></option>
                            <option value="{REGULAR_PRICE}"><?php esc_html_e('REGULAR PRICE', 'woocommerce-bulk-editor') ?></option>
                            <option value="{SALE_PRICE}"><?php esc_html_e('SALE PRICE', 'woocommerce-bulk-editor') ?></option>

                            <option value="{DO_STRING_UP}"><?php esc_html_e('STRING UP', 'woocommerce-bulk-editor') ?></option>
                            <option value="{DO_STRING_DOWN}"><?php esc_html_e('STRING DOWN', 'woocommerce-bulk-editor') ?></option>
                            <option value="{DO_STRING_TITLE}"><?php esc_html_e('STRING TITLE', 'woocommerce-bulk-editor') ?></option>
                            <option value="{DO_STRING_UP_FIRST}"><?php esc_html_e('STRING UP FIRST', 'woocommerce-bulk-editor') ?></option>


                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select name="woobe_bulk[<?php echo $field_key ?>][behavior]" disabled="" class="woobe_bulk_value_signs" data-key="<?php echo $field_key ?>">
                            <option value="append"><?php esc_html_e('append', 'woocommerce-bulk-editor') ?></option>
                            <option value="prepend"><?php esc_html_e('prepend', 'woocommerce-bulk-editor') ?></option>
                            <option value="new"><?php esc_html_e('new', 'woocommerce-bulk-editor') ?></option>
                            <option value="replace"><?php esc_html_e('replace', 'woocommerce-bulk-editor') ?></option>
                        </select>
                    </div>
                    <div class="clear"></div>

                    <div class='filter-unit-wrap woobe_bulk_replace_to_<?php echo $field_key ?>' style="display: none;">

                        <div class="col-lg-2">
                            <select name="woobe_bulk[<?php echo $field_key ?>][case]" disabled="">
                                <option value="same"><?php esc_html_e('same case', 'woocommerce-bulk-editor') ?></option>
                                <option value="ignore"><?php esc_html_e('ignore case', 'woocommerce-bulk-editor') ?></option>
                            </select>
                        </div>

                        <div class="col-lg-9">
                            <input type="text" class="woobe_bulk_value" disabled="" placeholder="<?php esc_html_e('replace to text', 'woocommerce-bulk-editor') ?>" name="woobe_bulk[<?php echo $field_key ?>][replace_to]" value="" />
                        </div>


                        <div class="col-lg-1">
                            <select class="woobe_bulk_add_special_key" disabled="">
                                <option value="-1"><?php esc_html_e('variable', 'woocommerce-bulk-editor') ?></option>
                                <option value="{TITLE}"><?php esc_html_e('TITLE', 'woocommerce-bulk-editor') ?></option>
                                <option value="{ID}">ID</option>
                                <option value="{SKU}"><?php esc_html_e('SKU', 'woocommerce-bulk-editor') ?></option>
                                <option value="{MENU_ORDER}"><?php esc_html_e('MENU ORDER', 'woocommerce-bulk-editor') ?></option>
                                <option value="{PARENT_ID}"><?php esc_html_e('PARENT ID', 'woocommerce-bulk-editor') ?></option>
                                <option value="{PARENT_TITLE}"><?php esc_html_e('PARENT TITLE', 'woocommerce-bulk-editor') ?></option>
                                <option value="{PARENT_SKU}"><?php esc_html_e('PARENT SKU', 'woocommerce-bulk-editor') ?></option>
                                <option value="{REGULAR_PRICE}"><?php esc_html_e('REGULAR PRICE', 'woocommerce-bulk-editor') ?></option>
                                <option value="{SALE_PRICE}"><?php esc_html_e('SALE PRICE', 'woocommerce-bulk-editor') ?></option>

                                <option value="{DO_STRING_UP}"><?php esc_html_e('STRING UP', 'woocommerce-bulk-editor') ?></option>
                                <option value="{DO_STRING_DOWN}"><?php esc_html_e('STRING DOWN', 'woocommerce-bulk-editor') ?></option>
                                <option value="{DO_STRING_TITLE}"><?php esc_html_e('STRING TITLE', 'woocommerce-bulk-editor') ?></option>
                                <option value="{DO_STRING_UP_FIRST}"><?php esc_html_e('STRING UP FIRST', 'woocommerce-bulk-editor') ?></option>


                            </select>
                        </div>



                        <div class="clear"></div>
                    </div>

                </div>
            </div>
        <?php endif; ?>



    <?php endforeach; ?>
    <div class="clear"></div>
    <?php
}

function woobe_bulk_draw_nums($filter_keys) {
    global $WOOBE;
    $fields = woobe_get_fields();
    ?>
    <?php foreach ($filter_keys as $field_key => $field) : ?>

        <?php
        if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
            if (intval($WOOBE->settings->get_shop_manager_visibility()[$field_key]) === 0) {
                continue;
            }
        }
        if ($fields[$field_key]['edit_view'] === 'calendar') {
            continue;
        }
        ?>

        <div class="col-lg-4">
            <div class='filter-unit-wrap <?php echo $field['css_classes'] ?> <?php if (!$field['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($field['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $field_key ?>]" class="bulk_checker" data-field-key="<?php echo $field_key ?>" data-title="<?php echo $field['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-7">
                    <input type="number" class="woobe_bulk_value" disabled="" placeholder="<?php echo $field['title'] ?>" name="woobe_bulk[<?php echo $field_key ?>][value]" value="" />
                </div>
                <div class="col-lg-4">
                    <select name="woobe_bulk[<?php echo $field_key ?>][behavior]" disabled="">
                        <?php foreach ($field['options'] as $key => $title) : ?>
                            <option value="<?php echo $key ?>"><?php echo $title ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="clear"></div>
    <br />
    <div class="col-lg-4">
        <?php WOOBE_HELPER::draw_rounding_drop_down() ?>
    </div>
    <div class="col-lg-1">
        <?php
        WOOBE_HELPER::draw_tooltip(sprintf(esc_html__('Select how to round float values fractions in the numeric fields. Works for prices and numeric meta fields. Read more %s.', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                            'title' => esc_html__('here', 'woocommerce-bulk-editor'),
                            'href' => 'https://bulk-editor.com/document/woocommerce-price-rounding/',
                            'target' => '_blank'
                        ))))
        ?>
    </div>
    <div class="col-lg-6 woobe_formula">
        <div class="col-lg-3">
            <p><?php esc_html_e('Selected num fields', 'woocommerce-bulk-editor'); ?></p>
        </div>
        <div class="col-lg-1">
            <select class="woobe_formula_action">
                <option value="+">+</option>
                <option value="-">-</option>
                <option value="*">*</option>
                <option value="/">/</option>
            </select>
        </div>
        <div class="col-lg-7">
            <select class="woobe_formula_value">
                <option value=-1></option>
                <?php foreach ($filter_keys as $field_key => $field) : ?>
                    <?php
                    if (!isset($fields[$field_key]['meta_key'])) {

                        if ($field_key == 'download_expiry' || $field_key == 'download_limit') {
                            $meta_key = '_' . $field_key;
                        } else {
                            continue;
                        }
                    } else {
                        $meta_key = $fields[$field_key]['meta_key'];
                    }
                    ?>

                    <option value="<?php echo $meta_key ?>"><?php echo $field['title']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-1">
            <?php
            WOOBE_HELPER::draw_tooltip(sprintf(esc_html__('You can select numeric field then make math operation with another numeric field using [+ - * /] value', 'woocommerce-bulk-editor')))
            ?>
        </div>
    </div>
    <div class="col-lg-12 woobe_random">
        <div class="col-lg-1">
            <span><?php esc_html_e('Random number', 'woocommerce-bulk-editor'); ?></span>
        </div>
        <div class="col-lg-1">
            <select id="woobe_random_action">
                <option value="+">+</option>
                <option value="-">-</option>
                <option value="*">*</option>
                <option value="/">/</option>
            </select>
        </div>
        <div class="col-lg-1">
            <label for="woobe_random_decimal"><?php esc_html_e('Decimal', 'woocommerce-bulk-editor'); ?></label>
        </div>
        <div class="col-lg-1">
            <select id="woobe_random_decimal">
                <option value="1">0</option>
                <option value="10">1</option>
                <option value="100">2</option>
                <option value="1000">3</option>
                <option value="10000">4</option>
            </select>
        </div>
        <div class="col-lg-1">
            <label for="woobe_random_from" ><?php esc_html_e('From:', 'woocommerce-bulk-editor'); ?></label>
        </div>		
        <div class="col-lg-2">			
            <input id="woobe_random_from" type="number" >		
        </div>	
        <div class="col-lg-1">
            <label for="woobe_random_to" ><?php esc_html_e('To:', 'woocommerce-bulk-editor'); ?></label>
        </div>		
        <div class="col-lg-2">
            <input id="woobe_random_to" type="number" >			
        </div>			
        <div class="col-lg-1">
            <?php
            WOOBE_HELPER::draw_tooltip(sprintf(esc_html__('You can create random numbers for test filling fields or for SEO tasks', 'woocommerce-bulk-editor')))
            ?>
        </div>
    </div>	

    <div class="clear"></div>
    <?php
}

function woobe_bulk_draw_taxonomies() {
    global $WOOBE;
    //get all products taxonomies
    $taxonomy_objects = get_object_taxonomies('product', 'objects');
    $taxonomy_objects = apply_filters('woobe_bulkedit_taxonomies', $taxonomy_objects);
    unset($taxonomy_objects['product_type']);
    unset($taxonomy_objects['product_visibility']);
    unset($taxonomy_objects['product_shipping_class']);

    //***

    if (!empty($taxonomy_objects)) {
        foreach ($taxonomy_objects as $t) {

            if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
                if (intval($WOOBE->settings->get_shop_manager_visibility()[$t->name]) === 0) {
                    continue;
                }
            }

            //***
			if (apply_filters('woobe_hide_bulk_by_attribute', false)) {
				if (substr($t->name, 0, 3) === 'pa_') {
					continue; //without attributes
				}
			}

            $terms_by_parents = array();
            $terms = get_terms(array(
                'taxonomy' => $t->name,
                'hide_empty' => false
            ));

            if (!empty($terms)) {
                foreach ($terms as $k => $term) {
                    if ($term->parent > 0) {
                        $terms_by_parents[$term->parent][] = $term;
                        unset($terms[$k]);
                    }
                }
            }
            ?>
            <div class="col-lg-4">
                <div class='filter-unit-wrap not-for-variations <?php if (!$WOOBE->settings->get_fields()[$t->name]['direct']): ?>woobe-direct-field<?php endif; ?>' style="overflow: visible;">

                    <div class="col-lg-1">
                        <div class="height4"></div>
                        <?php if ($WOOBE->settings->get_fields()[$t->name]['direct']): ?>
                            <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $t->name ?>]" class="bulk_checker" data-field-key="<?php echo $t->name ?>" data-title="<?php echo $t->label ?>" value="1" /><br />
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-9">
                        <select class="chosen-select woobe_filter_select" disabled="" multiple="" id="woobe_bulk_taxonomies_<?php echo $t->name ?>" name="woobe_bulk[<?php echo $t->name ?>][value][]" data-placeholder="<?php echo $t->label ?>">
                            <?php if (!empty($terms)): ?>
                                <?php foreach ($terms as $tt) : ?>
                                    <option value="<?php echo $tt->term_id ?>"><?php echo $tt->name ?></option>
                                    <?php draw_child_filter_terms($tt->term_id, $terms_by_parents, 1) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <div class='select-wrap' style="display: inline-block">
                            <select name="woobe_bulk[<?php echo $t->name ?>][behavior]" disabled="">
                                <option value="append"><?php esc_html_e('append', 'woocommerce-bulk-editor') ?></option>
                                <option value="replace"><?php esc_html_e('replace', 'woocommerce-bulk-editor') ?></option>
                                <option value="remove"><?php esc_html_e('remove', 'woocommerce-bulk-editor') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="clear"></div>
        <?php
    }
}

function woobe_bulk_draw_other($filter_keys) {
    global $WOOBE;
    ?>
    <?php foreach ($filter_keys as $field_key => $field) : ?>

        <?php
        if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
            if (intval($WOOBE->settings->get_shop_manager_visibility()[$field_key]) === 0) {
                continue;
            }
        }
        ?>

        <div class="col-lg-4">
            <div class='filter-unit-wrap <?php echo $field['css_classes'] ?> <?php if (!$field['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($field['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $field_key ?>]" class="bulk_checker" data-field-key="<?php echo $field_key ?>" data-title="<?php echo $field['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>

                <div class="col-lg-11">
                    <?php
                    $opt = array(-1 => sprintf(esc_html__('Set: %s', 'woocommerce-bulk-editor'), $field['title']));
                    $opt = array_merge($opt, $field['options']);

                    echo WOOBE_HELPER::draw_select(array(
                        'options' => $opt,
                        'field' => $field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $field_key . '][value]',
                        'disabled' => TRUE
                    ));
                    ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="clear"></div>
    <?php
}

/* * ****************************************** */

function woobe_bulk_draw_add1($fields) {
    global $WOOBE;

    //***

    $options1 = array(
        'new' => esc_html__('set new', 'woocommerce-bulk-editor'),
        'invalue' => esc_html__('increase by value', 'woocommerce-bulk-editor'),
        'devalue' => esc_html__('decrease by value', 'woocommerce-bulk-editor')
    );

    //***

    $current_field_key = '_thumbnail_id';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$fields[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($fields[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <img src="" alt="" width="30" id="woobe_bulk_select_thumb" />
                    <input type="hidden" class="woobe_bulk_value" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                </div>
                <div class="col-lg-7">
                    <a href="#" id="woobe_bulk_select_thumb_btn" class="button button-primary woobe_btn_order"><?php esc_html_e('select thumbnail', 'woocommerce-bulk-editor') ?></a>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $current_field_key = 'date_on_sale_from';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$fields[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($fields[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <div style="position: relative;">
                        <?php
                        echo WOOBE_HELPER::draw_calendar($current_field_key, $fields[$current_field_key]['title'], $current_field_key, '', 'woobe_bulk[' . $current_field_key . '][value]', true);
                        ?>
                        <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'date_on_sale_to';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$fields[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($fields[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <div style="position: relative;">
                        <?php
                        echo WOOBE_HELPER::draw_calendar($current_field_key, $fields[$current_field_key]['title'], $current_field_key, '', 'woobe_bulk[' . $current_field_key . '][value]', true);
                        ?>
                        <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>



    <?php
    $current_field_key = 'post_date';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$fields[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($fields[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <div style="position: relative;">
                        <?php
                        echo WOOBE_HELPER::draw_calendar($current_field_key, $fields[$current_field_key]['title'], $current_field_key, '', 'woobe_bulk[' . $current_field_key . '][value]', true, true);
                        ?>
                        <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $calendar_fields = [];

    foreach ($fields as $key => $value) {
        if ($value['edit_view'] === 'calendar' AND!in_array($key, array('date_on_sale_from', 'date_on_sale_to', 'post_date'))) {
            $calendar_fields[] = $key;
        }
    }

    if (!empty($calendar_fields)):
        foreach ($calendar_fields as $current_field_key) :
            $show_field = true;
            if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
                if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
                    $show_field = false;
                }
            }
            ?>

            <?php if ($show_field): ?>
                <div class="col-lg-3">
                    <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$fields[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                        <div class="col-lg-1">
                            <?php if ($fields[$current_field_key]['direct']): ?>
                                <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php esc_html_e($current_field_key) ?>" data-title="<?php esc_html_e($fields[$current_field_key]['title']) ?>" value="1" /><br />
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-11">
                            <div class="content-wrap">
                                <?php
                                echo WOOBE_HELPER::draw_calendar($current_field_key, $fields[$current_field_key]['title'], $current_field_key, '', 'woobe_bulk[' . $current_field_key . '][value]', true, true);
                                ?>
                                <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php
        endforeach;
    endif;
    ?>

    <div class="clear"></div>
    <br />
    <hr class="clear">
    <br />

    <?php $filter_keys2 = array('weight', 'length', 'width', 'height'); ?>

    <?php foreach ($filter_keys2 as $current_field_key) : ?>

        <?php
        if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
            if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
                continue;
            }
        }
        ?>

        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-7">
                    <input type="number" class="woobe_bulk_value" disabled="" placeholder="<?php echo $fields[$current_field_key]['title'] ?>" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                </div>
                <div class="col-lg-4">

                    <select name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" disabled="">
                        <?php foreach ($options1 as $key => $title) : ?>
                            <option value="<?php echo $key ?>"><?php echo $title ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>
                <div class="clear"></div>
            </div>
        </div>

    <?php endforeach; ?>


    <div class="clear"></div>
    <br />
    <hr class="clear">
    <br />

    <?php
    $current_field_key = 'manage_stock';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => array(
                            'yes' => esc_html__('Manage stock: Yes', 'woocommerce-bulk-editor'), //true
                            'no' => esc_html__('Manage stock: No', 'woocommerce-bulk-editor'), //false
                        ),
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'virtual';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => array(
                            'yes' => esc_html__('Virtual: yes', 'woocommerce-bulk-editor'), //true
                            'no' => esc_html__('Virtual: no', 'woocommerce-bulk-editor'), //false
                        ),
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'downloadable';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => array(
                            'yes' => esc_html__('Downloadable: yes', 'woocommerce-bulk-editor'), //true
                            'no' => esc_html__('Downloadable: no', 'woocommerce-bulk-editor'), //false
                        ),
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'purchase_note';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <input type="text" disabled="" placeholder="<?php echo $fields[$current_field_key]['title'] ?>" class="woobe_bulk_value" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $current_field_key = 'product_shipping_class';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => $fields[$current_field_key]['select_options'],
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'sold_individually';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => array(
                            'yes' => esc_html__('Sold individ.: Yes', 'woocommerce-bulk-editor'), //true
                            'no' => esc_html__('Sold individ.: No', 'woocommerce-bulk-editor'), //false
                        ),
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $current_field_key = 'backorders';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => $fields[$current_field_key]['select_options'],
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $current_field_key = 'tax_class';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => $fields[$current_field_key]['select_options'],
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $current_field_key = 'gallery';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field AND in_array($current_field_key, $WOOBE->settings->get_fields_keys())): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">

                    <div>
                        <?php echo WOOBE_HELPER::draw_gallery_popup_editor_btn($current_field_key, 0, array()); ?>
                    </div>

                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $current_field_key = 'download_files';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field AND in_array($current_field_key, $WOOBE->settings->get_fields_keys())): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">

                    <div>
                        <?php echo WOOBE_HELPER::draw_downloads_popup_editor_btn($current_field_key, 0, 0); ?>
                    </div>

                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'cross_sell_ids';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field AND in_array($current_field_key, $WOOBE->settings->get_fields_keys())): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">

                    <div>
                        <?php echo WOOBE_HELPER::draw_cross_sells_popup_editor_btn($current_field_key, 0, array()); ?>
                    </div>

                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'upsell_ids';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field AND in_array($current_field_key, $WOOBE->settings->get_fields_keys())): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">

                    <div>
                        <?php echo WOOBE_HELPER::draw_upsells_popup_editor_btn($current_field_key, 0, array()); ?>
                    </div>

                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'grouped_ids';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    ?>

    <?php if ($show_field AND in_array($current_field_key, $WOOBE->settings->get_fields_keys())): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11">

                    <div>
                        <?php echo WOOBE_HELPER::draw_grouped_popup_editor_btn($current_field_key, 0, array()); ?>
                    </div>

                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>
    <?php
    $current_field_key = 'post_author';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    $opt_auth = WOOBE_HELPER::get_users();
    ?>
    <?php if ($show_field): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap woobe_post_author_edit <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-11 ">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => $opt_auth,
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select chosen-select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value]'
                    ));
                    ?>
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = 'attribute_visibility';
    $show_field = true;
    if (!in_array($WOOBE->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
        if (intval($WOOBE->settings->get_shop_manager_visibility()[$current_field_key]) === 0) {
            $show_field = false;
        }
    }
    $attributes_opt = array();
    $attributes = wc_get_attribute_taxonomies();
    foreach ($attributes as $a) {
        $attributes_opt["pa_" . $a->attribute_name] = $a->attribute_label;
    }
    ?>
    <?php if (true): ?>
        <div class="col-lg-3">
            <div class='filter-unit-wrap woobe_post_author_edit <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
                <div class="col-lg-1">
                    <div class="height4"></div>
                    <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                        <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                    <?php endif; ?>
                </div>
                <div class="col-lg-7">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => $attributes_opt,
                        'field' => $current_field_key . "_b",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select chosen-select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][value][]'
                            ), true);
                    ?>
                    <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                </div>
                <div class="col-lg-3">
                    <?php
                    echo WOOBE_HELPER::draw_select(array(
                        'disabled' => 1,
                        'options' => array('1' => esc_html__('Visible', 'woocommerce-bulk-editor'), '0' => esc_html__('Hidden', 'woocommerce-bulk-editor')),
                        'field' => $current_field_key . "_b_o",
                        'product_id' => 0,
                        'class' => 'woobe_filter_select',
                        'name' => 'woobe_bulk[' . $current_field_key . '][visible]'
                    ));
                    ?>
                    <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="attribute_visibility" />
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $current_field_key = "product_url";
    ?>
    <div class="col-lg-3">
        <div class='filter-unit-wrap woobe_post_author_edit <?php echo $fields[$current_field_key]['css_classes'] ?> <?php if (!$WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>woobe-direct-field<?php endif; ?>'>
            <div class="col-lg-1">
                <div class="height4"></div>
                <?php if ($WOOBE->settings->get_fields()[$current_field_key]['direct']): ?>
                    <input type="checkbox" title='<?php esc_html_e('select it to use', 'woocommerce-bulk-editor') ?>' name="woobe_bulk[is][<?php echo $current_field_key ?>]" class="bulk_checker" data-field-key="<?php echo $current_field_key ?>" data-title="<?php echo $fields[$current_field_key]['title'] ?>" value="1" /><br />
                <?php endif; ?>
            </div>
            <div class="col-lg-11">
                <input type="text" class="woobe_bulk_value" disabled="" placeholder="<?php echo $fields[$current_field_key]['title'] ?>" name="woobe_bulk[<?php echo $current_field_key ?>][value]" value="" />
                <i class="fs8"><?php echo $fields[$current_field_key]['title'] ?></i>
                <input type="hidden" name="woobe_bulk[<?php echo $current_field_key ?>][behavior]" value="new" />
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="clear"></div>
    <?php
}
