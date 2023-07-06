<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

$combination_attributes = wc_get_attribute_taxonomies();
?>

<!------------------ bulkoperations popup --------------------------->

<div id="bulkoperations_popup" style="display: none;">                            

    <div class="woobe-modal woobe-modal2 woobe-style" style="z-index: 15002; width: 80%;">
        <div class="woobe-modal-inner">
            <div class="woobe-modal-inner-header">
                <h3 class="woobe-modal-title"><?php echo esc_html__('Variations Advanced Bulk Operations', 'woocommerce-bulk-editor') ?> - <a href="https://bulk-editor.com/document/variations-advanced-bulk-operations/" class="button button-primary woobe_btn_order" style="vertical-align: middle;" target="_blank"><span class="icon-book"></span>&nbsp;<?php echo esc_html__('Documentation', 'woocommerce-bulk-editor') ?></a></h3>
                <a href="javascript:void(0)" class="woobe-modal-close woobe-modal-close-bulkoperations"></a>
            </div>
            <div class="woobe-modal-inner-content">                

                <div class="woobe-tabs woobe-tabs-style-shape" style="overflow: visible;">
                    <nav>
                        <ul>
                            <li class="tab-current">
                                <a href="#woobe-bulkoperations-creating" onclick="return woobe_init_js_intab('tabs-bulkoperations-creating')"><?php esc_html_e('Creating', 'woocommerce-bulk-editor') ?></a>
                            </li>
                            <li>
                                <a href="#woobe-bulkoperations-default-values" onclick="return woobe_init_js_intab('tabs-bulkoperations-default-values')"><?php esc_html_e('Default combination', 'woocommerce-bulk-editor') ?></a>
                            </li>


                            <li>
                                <a href="#woobe-bulkoperations-ordering" onclick="return woobe_init_js_intab('tabs-woobe-bulkoperations-ordering')"><?php esc_html_e('Ordering', 'woocommerce-bulk-editor') ?></a>
                            </li>


                            <li>
                                <a href="#woobe-bulkoperations-attaching" onclick="return woobe_init_js_intab('tabs-woobe-bulkoperations-attaching')"><?php esc_html_e('Attaching', 'woocommerce-bulk-editor') ?></a>
                            </li>

                            <li>
                                <a href="#woobe-bulkoperations-visibility" onclick="return woobe_init_js_intab('tabs-woobe-bulkoperations-visibility')"><?php esc_html_e('Visibility', 'woocommerce-bulk-editor') ?></a>
                            </li>

                            <li>
                                <a href="#woobe-bulkoperations-swap" onclick="return woobe_init_js_intab('tabs-woobe-bulkoperations-swap')"><?php esc_html_e('Swap', 'woocommerce-bulk-editor') ?></a>
                            </li>


                            <li>
                                <a href="#woobe-bulkoperations-delete" onclick="return woobe_init_js_intab('tabs-woobe-bulkoperations-delete')"><?php esc_html_e('Deleting', 'woocommerce-bulk-editor') ?></a>
                            </li>

                        </ul>
                    </nav>

                    <div class="content-wrap">
                        <section id="woobe-bulkoperations-creating" class="content-current">

                            <div class="woobe-form-element-container" id="bulkoperations_step_1">
                                <div class="woobe-name-description">
                                    <strong><?php esc_html_e('Select attributes', 'woocommerce-bulk-editor') ?></strong>
                                    <span><?php esc_html_e('Select attributes which products variations you want to create', 'woocommerce-bulk-editor') ?></span>
                                </div>
                                <div class="woobe-form-element">
                                    <?php if (!empty($attributes)): ?>
                                        <select id="bulkoperations_attributes" multiple="" data-placeholder="<?php esc_html_e('Select attributes', 'woocommerce-bulk-editor') ?>">
                                            <?php foreach ($attributes as $a) : ?>
                                                <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <strong><?php
                                            printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                                        'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                                                        'title' => esc_html__('here', 'woocommerce-bulk-editor')
                                            )));
                                            ?></strong>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <div class="woobe-form-element-container" id="bulkoperations_step_2">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Variations Advanced Bulk Operation will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('Here you can set order of attributes on the single product page after loaded. Before attaching products variations select variable products by filter or select them by checkboxes! Selection by checkbox has higher priority!', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <a href="javascript: bulkoperations_generate_combinations();void(0);" class="button button-primary button-large bulkoperations_generate_combinations_btn" style="display: none;"><?php esc_html_e('Generate possible combinations', 'woocommerce-bulk-editor') ?></a>
                                </div>
                                <div class="woobe-form-element">
                                    <ul id="bulkoperations_attributes_terms"></ul>
                                </div>
                            </div>

                            <div class="woobe-form-element-container" id="bulkoperations_step_3" style="display: none;">
                                <div class="woobe-name-description">

                                    <a href="javascript: bulkoperations_generate_variations();void(0);" class="button button-primary button-large bulkoperations_generate_variations_btn"><?php esc_html_e('Start BULK Adding!', 'woocommerce-bulk-editor') ?></a><br />
                                    <br />
                                    <b><?php esc_html_e('Note', 'woocommerce-bulk-editor') ?></b>: <?php esc_html_e('If combinations of the variations in the products exists already - changings will be ignored, so do not worry, you will never got the same product variations in one product!', 'woocommerce-bulk-editor') ?><br />
                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress">0%</div>
                                    </div>
                                    <br />
                                    <a href="javascript: woobe_bulkoperations_terminate();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />


                                </div>
                                <div class="woobe-form-element">
                                    <h4 style="margin: 0;"><?php esc_html_e('Possible combinations', 'woocommerce-bulk-editor') ?> (<span>0</span>)</h4>
                                    <ul id="bulkoperations_attributes_combos" class="woobe_fields"></ul><br />
                                </div>

                            </div>

                        </section>

                        <section id="woobe-bulkoperations-default-values">

                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <strong><?php esc_html_e('Select attributes', 'woocommerce-bulk-editor') ?></strong>
                                    <span><?php esc_html_e('This operation is only for variable products and not for their variations. Select attributes which combination you want to apply as default one on its single page of variable a product. Select terms of the attributes and press button Apply combination. This combination will be pre-selected on the shop single page. If you will miss any attribute you can always can stop the operation, and then try again!', 'woocommerce-bulk-editor') ?></span>
                                </div>
                                <div class="woobe-form-element">
                                    <?php if (!empty($attributes)): ?>
                                        <select id="bulkoperations_attributes_default" multiple="" data-placeholder="<?php esc_html_e('Select attributes', 'woocommerce-bulk-editor') ?>">
                                            <?php foreach ($attributes as $a) : ?>
                                                <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <strong><?php
                                            printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                                        'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                                                        'title' => esc_html__('here', 'woocommerce-bulk-editor')
                                            )));
                                            ?></strong>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Default variations combination will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('This operation is only for variable products and not for their variations. Before attaching default combination of variations, select variation products by filter or by checkboxes! Selection by checkbox has higher priority!', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <a href="javascript: bulkoperations_apply_combination();void(0);" class="button button-primary button-large bulkoperations_apply_combination_btn" style="display: none;"><?php esc_html_e('Apply combination', 'woocommerce-bulk-editor') ?></a><br />
                                    <a href="javascript: woobe_bulkoperations_terminate2();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />

                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress_default">0%</div>
                                    </div>

                                </div>
                                <div class="woobe-form-element">
                                    <ul id="bulkoperations_attributes_terms_default"></ul>
                                </div>
                            </div>


                        </section>

                        <section id="woobe-bulkoperations-ordering">

                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">

                                    <strong><?php esc_html_e('Paste any variable product ID', 'woocommerce-bulk-editor') ?></strong>
                                    <span><?php esc_html_e('This operation is only for variable products and not for their variations. Paste in the textinput ID of any variable product, which variations you want to set in the custom order, change their order and then apply their new ordering to the another variative products which you filtered by the filter or selected them by checkboxes.', 'woocommerce-bulk-editor') ?></span>

                                </div>
                                <div class="woobe-form-element">
                                    <input type="number" value="" class="form-control input-sm" style="width: 100%;" id="woobe-bulkoperations-ordering-id" />

                                    <br /><br />
                                    <a href="javascript: void(0);" class="button button-primary button-large bulkoperations_get_product_variations_btn"><?php esc_html_e('Get the products variations', 'woocommerce-bulk-editor') ?></a><br />


                                </div>
                            </div>


                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Ordering of the products variations will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('Using mouse, set order of variations you need and press start button.', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <a href="javascript: bulkoperations_apply_4();void(0);" class="button button-primary button-large bulkoperations_apply_4_btn" style="display: none;"><?php esc_html_e('Start', 'woocommerce-bulk-editor') ?></a><br />




                                    <a href="javascript: woobe_bulkoperations_terminate4();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />

                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress_4">0%</div>
                                    </div>

                                </div>
                                <div class="woobe-form-element">
                                    <ul id="bulkoperations_attributes_var_order" class="woobe_fields"></ul>
                                </div>
                            </div>


                        </section>

                        <section id="woobe-bulkoperations-attaching">


                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Selected combination will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('This operation is only for variations of variable products and not their parents. Select a product attribute to attach and also its default term, then add attaching rules. Attaching usually uses when exists empty attribute in the variation, but it is also can change existed attribute term in the variation!', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <?php esc_html_e('IMPORTANT: if attached attribute is already selected in the product and not marked as [Used for variations] operation of attaching is not be done, so before attaching do [Visibility operation] for the products attributes.', 'woocommerce-bulk-editor') ?>
                                    <br /><br />
                                    <a href="javascript: bulkoperations_apply_6();void(0);" class="button button-primary button-large bulkoperations_apply_6_btn" style="display: none;"><?php esc_html_e('Attaching', 'woocommerce-bulk-editor') ?></a><br />
                                    <a href="javascript: woobe_bulkoperations_terminate_6();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />
                                    <br />
                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress_6">0%</div>
                                    </div>

                                </div>

                                <div class="woobe-form-element">

                                    <?php if (!empty($combination_attributes)): ?>

                                        <select id="bulkoperations_attaching_att" style="width: 200px;">
                                            <option value="-1"><?php esc_html_e('select attribute', 'woocommerce-bulk-editor') ?></option>
                                            <?php foreach ($combination_attributes as $a) : ?>
                                                <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                            <?php endforeach; ?>
                                        </select> - <span id="bulkoperations_attaching_defterms_container"><?php esc_html_e('attribute to select replaceable term', 'woocommerce-bulk-editor') ?></span><br />

                                        <hr />

                                        <input type="number" value="" placeholder="<?php esc_html_e('Paste any variable product ID which has combinations of the products variations you need', 'woocommerce-bulk-editor') ?>" class="form-control input-sm" style="width: 100%;" id="woobe-bulkoperations-attaching-id" /><br />
                                        <br />
                                        <a href="javascript: void(0);" class="button button-primary button-large bulkoperations_get_product_variations_btn_6"><?php esc_html_e('Get the existing products variations', 'woocommerce-bulk-editor') ?></a><br />
                                        <br />
                                        <ul id="bulkoperations_attributes_var_attaching" class="woobe_fields"></ul>

                                    <?php else: ?>

                                        <strong><?php
                                            printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                                        'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                                                        'title' => esc_html__('here', 'woocommerce-bulk-editor')
                                            )));
                                            ?></strong>

                                    <?php endif; ?>

                                </div>
                            </div>


                        </section>


                        <section id="woobe-bulkoperations-visibility">


                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Selected combination will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('This operation is only for attributes of variable products. Select attributes and their properties.', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <a href="javascript: bulkoperations_apply_7();void(0);" class="button button-primary button-large bulkoperations_apply_7_btn" style="display: none;"><?php esc_html_e('Set visibility', 'woocommerce-bulk-editor') ?></a><br />
                                    <a href="javascript: woobe_bulkoperations_terminate_7();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />
                                    <br />
                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress_7">0%</div>
                                    </div>

                                </div>

                                <div class="woobe-form-element">

                                    <?php if (!empty($combination_attributes)): ?>

                                        <a href="#" class="button button-primary button-large" id="bulkoperations_att_visibility_add"><?php esc_html_e('Add attribute', 'woocommerce-bulk-editor') ?></a><br />

                                        <div id="bulkoperations_visibility_att_tpl" style="display: none;">
                                            <select class="bulkoperations_visibility_att" style="width: 200px;">
                                                <option value="-1"><?php esc_html_e('select attribute', 'woocommerce-bulk-editor') ?></option>
                                                <?php foreach ($combination_attributes as $a) : ?>
                                                    <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                                <?php endforeach; ?>
                                            </select>&nbsp;<a href="#" class="button button-primary button-large bulkoperations_att_visibility_del">X</a><br />
                                            <input type="checkbox" value="1" checked="" id="__ID1__" />&nbsp;<label for="__ID1__"><?php esc_html_e('Visible on the product page', 'woocommerce-bulk-editor') ?></label>
                                            <br /><input type="checkbox" value="1" checked="" id="__ID2__" />&nbsp;<label for="__ID2__"><?php esc_html_e('Used for variations', 'woocommerce-bulk-editor') ?></label>
                                            <br />
                                            <br />
                                        </div>

                                        <ul id="bulkoperations_att_visibility"></ul>



                                    <?php else: ?>

                                        <strong><?php
                                            printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                                        'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                                                        'title' => esc_html__('here', 'woocommerce-bulk-editor')
                                            )));
                                            ?></strong>

                                    <?php endif; ?>

                                </div>
                            </div>


                        </section>

                        <section id="woobe-bulkoperations-swap">


                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Selected combination will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('This operation is only for variations of variable products and not their parents. Select which term is replaceable and which is substitute. If the terms has the same attribute, replaceable term will be replaced by substitute term. If terms has different attributes - substitute term will be attached as new to a product variation which has replaceable term, and replaceable term will not be replaced! Also if parent product has not the substitute attribute (tab Attributes on the product page) - it will be attached to the product automatically in the current process!', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <?php esc_html_e('IMPORTANT: if swap-attribute is already selected in the product and not marked as [Used for variations] operation of swapping is not be done, so before swapping do [Visibility operation] for the products attributes', 'woocommerce-bulk-editor') ?>
                                    <br />
                                    <a href="javascript: bulkoperations_apply_5();void(0);" class="button button-primary button-large bulkoperations_apply_5_btn"><?php esc_html_e('Swap', 'woocommerce-bulk-editor') ?></a><br />
                                    <a href="javascript: woobe_bulkoperations_terminate_5();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />
                                    <br /><br />
                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress_5">0%</div>
                                    </div>

                                </div>

                                <div class="woobe-form-element">

                                    <?php if (!empty($combination_attributes)): ?>

                                        <select id="bulkoperations_swap_att_from" style="width: 200px;">
                                            <option value="-1"><?php esc_html_e('select attribute', 'woocommerce-bulk-editor') ?></option>
                                            <?php foreach ($combination_attributes as $a) : ?>
                                                <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                            <?php endforeach; ?>
                                        </select> - <span id="bulkoperations_swap_terms_from_container"><?php esc_html_e('attribute to select replaceable term', 'woocommerce-bulk-editor') ?></span><br />


                                        <h4 style="margin-top: 0; margin-bottom: 7px;"><b><?php esc_html_e('replaceable term', 'woocommerce-bulk-editor') ?></b> <?php esc_html_e('swap to', 'woocommerce-bulk-editor') ?> <b><?php esc_html_e('substitute term', 'woocommerce-bulk-editor') ?></b>:</h4>


                                        <select id="bulkoperations_swap_att_to" style="width: 200px;">
                                            <option value="-1"><?php esc_html_e('select attribute', 'woocommerce-bulk-editor') ?></option>
                                            <?php foreach ($combination_attributes as $a) : ?>
                                                <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                            <?php endforeach; ?>
                                        </select> - <span id="bulkoperations_swap_terms_to_container"><?php esc_html_e('attribute to select substitute term', 'woocommerce-bulk-editor') ?></span><br />

                                    <?php else: ?>

                                        <strong><?php
                                            printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                                        'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                                                        'title' => esc_html__('here', 'woocommerce-bulk-editor')
                                            )));
                                            ?></strong>

                                    <?php endif; ?>

                                </div>
                            </div>


                        </section>

                        <section id="woobe-bulkoperations-delete">

                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <strong><?php esc_html_e('Select attributes', 'woocommerce-bulk-editor') ?></strong>
                                    <span><?php esc_html_e('This operation is only for variations of variable products and not their parents. Select attributes combination of which you want to delete. Remember: not possible to rollback delete-operations!', 'woocommerce-bulk-editor') ?></span>
                                </div>
                                <div class="woobe-form-element">
                                    <?php if (!empty($attributes)): ?>
                                        <div class="bulkoperations_attributes_delete_cont">
                                            <select id="bulkoperations_attributes_delete" multiple="" data-placeholder="<?php esc_html_e('Select attributes', 'woocommerce-bulk-editor') ?>">
                                                <?php foreach ($attributes as $a) : ?>
                                                    <option value="pa_<?php echo $a->attribute_name ?>"><?php echo $a->attribute_label ?></option>
                                                <?php endforeach; ?>
                                            </select><br />
                                        </div>
                                        <br />
                                        <select id="bulkoperations_attributes_delete_how">
                                            <option value="combo"><?php esc_html_e('Delete the products variations according to the combination of the attributes', 'woocommerce-bulk-editor') ?></option>
                                            <option value="all"><?php esc_html_e('Delete all products variations', 'woocommerce-bulk-editor') ?></option>
                                        </select>
                                    <?php else: ?>
                                        <strong><?php
                                            printf(esc_html__('No attributes created, you can do it %s', 'woocommerce-bulk-editor'), WOOBE_HELPER::draw_link(array(
                                                        'href' => admin_url('edit.php?post_type=product&page=product_attributes'),
                                                        'title' => esc_html__('here', 'woocommerce-bulk-editor')
                                            )));
                                            ?></strong>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <div class="woobe-form-element-container">
                                <div class="woobe-name-description">
                                    <b><?php printf(esc_html__('Combination, of the products variations for deleting, will be applied to: %s', 'woocommerce-bulk-editor'), '<span class="woobe_action_will_be_applied_to">' . esc_html__('all the products on the site', 'woocommerce-bulk-editor') . '</span>') ?></b><br />
                                    <?php esc_html_e('Order of the product variations no matter, has the sense only count of the attributes and their names. Will be deleted variations of the products only with the exact count of attributes and the exact names of attributes! For more convenience and more quick operation before deleting filter the products by their type [Variable].', 'woocommerce-bulk-editor') ?><br />
                                    <br />
                                    <a href="javascript: bulkoperations_apply_3();void(0);" class="button button-primary button-large bulkoperations_apply_3_btn" style="display: none;"><?php esc_html_e('Start deleting', 'woocommerce-bulk-editor') ?></a><br />
                                    <a href="javascript: woobe_bulkoperations_terminate3();void(0);" class="button button-primary button-large woobe_bulkoperations_terminate_btn" style="display: none;"><?php esc_html_e('Terminate operation!', 'woocommerce-bulk-editor') ?></a><br />

                                    <div class="woobe_progress" style="display: none;">
                                        <div class="woobe_progress_in" id="woobe_bulkoperations_progress_delete">0%</div>
                                    </div>

                                </div>
                                <div class="woobe-form-element">
                                    <ul id="bulkoperations_attributes_terms_delete"></ul>
                                </div>
                            </div>


                        </section>
                    </div>
                </div>

            </div>
            <div class="woobe-modal-inner-footer">
                <a href="javascript:void(0)" class="woobe-modal-close-bulkoperations button button-primary button-large button-large-2"><?php echo esc_html__('Close', 'woocommerce-bulk-editor') ?></a>
            </div>
        </div>
    </div>

    <div class="woobe-modal-backdrop" style="z-index: 15001;"></div>

</div>

<div style="display: none;" id="bulkoperations_attributes_combo_tpl">
    <li class="woobe_options_li">
        <a href="#" class="help_tip woobe_drag_and_drope" title="<?php esc_html_e('drag and drop', 'woocommerce-bulk-editor') ?>"><img src="<?php echo WOOBE_ASSETS_LINK ?>images/move.png" width="18" alt="" /></a>&nbsp;<input type="checkbox" data-terms="__DATA_TERMS__" value="1" checked="" id="__ID__" />&nbsp;<label for="__ID__">__LABEL__</label>
    </li>
</div>

<div style="display: none;" id="bulkoperations_attributes_order_tpl">
    <li class="woobe_options_li" data-var-id='__ID__' data-var-num='__NUM__'>
        <a href="#" class="help_tip woobe_drag_and_drope" title="<?php esc_html_e('drag and drop', 'woocommerce-bulk-editor') ?>"><img src="<?php echo WOOBE_ASSETS_LINK ?>images/move.png" width="18" alt="" /></a>&nbsp;<label>__LABEL__</label>
    </li>
</div>


<div style="display: none;" id="bulkoperations_attributes_attaching_tpl">
    <li class="woobe_options_li" data-var-id='__ID__' data-var-num='__NUM__'>
        <label>__LABEL__</label>&nbsp;<select style="width: 150px;" id="bulkoperations_attributes_attaching_sel___ID__"></select>
    </li>
</div>




