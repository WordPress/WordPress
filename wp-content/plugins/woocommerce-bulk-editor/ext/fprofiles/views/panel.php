<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOBE;
?>

<!------------------ filter sets profiles popup --------------------------->
<div id="woobe_fprofile_popup" style="display: none;">
    <div class="woobe-modal woobe-modal2 woobe-style" style="z-index: 15002; width: 80%; height: 320px;">
        <div class="woobe-modal-inner">
            <div class="woobe-modal-inner-header">
                <h3 class="woobe-modal-title"><?php esc_html_e('Filters profiles', 'woocommerce-bulk-editor') ?></h3>
                <a href="javascript:void(0)" class="woobe-modal-close woobe-modal-close-fprofile"></a>
            </div>
            <div class="woobe-modal-inner-content">

                <div class="woobe-form-element-container">
                    <div class="woobe-name-description">
                        <strong><?php echo esc_html__('Profiles', 'woocommerce-bulk-editor') ?></strong>
                        <span><?php echo esc_html__('Here you can load previously saved filters profile. After pressing on the load button, products table data reloading will start immediately!', 'woocommerce-bulk-editor') ?></span>

                        <ul id="woobe_loaded_fprofile_data_info"></ul>

                    </div>
                    <div class="woobe-form-element">
                    <?php
                        $saved_prof = get_user_meta(get_current_user_id(),"woobe_fprofile_saved", true );
                    ?>
                        <select id="woobe_load_fprofile">
                            <option value="0"><?php esc_html_e('Select filter profile to load', 'woocommerce-bulk-editor') ?></option>
                            <?php if (!empty($fprofiles)): ?>
                                <?php foreach ($fprofiles as $pkey => $pvalue) : ?>
                            <option <?php echo ($saved_prof==$pkey)?"selected='selected'":""; ?> value="<?php echo $pkey ?>"><?php echo $pvalue['title'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>


                        <div style="display: none;" id="woobe_load_fprofile_actions">
                            <a href="javascript:void(0)" class="button button-primary button" id="woobe_load_fprofile_btn"><?php esc_html_e('load', 'woocommerce-bulk-editor') ?></a>&nbsp;
                            <?php
                            $checked="";
                            if($saved_prof ){
                              $checked="checked='chacked'";  
                            }
                            ?>
                            <input <?php echo $checked ?> value="1" type="checkbox" id="woobe_load_fprofile_save" ><label><?php esc_html_e('Use it constantly', 'woocommerce-bulk-editor') ?></label>&nbsp;&nbsp;&nbsp;
                            <a href="#" class="button button-primary button woobe_delete_fprofile"><?php esc_html_e('remove', 'woocommerce-bulk-editor') ?></a>
                        </div>

                    </div>
                </div>



                <div class="woobe-form-element-container woobe-new-fprofile-inputs">
                    <div class="woobe-name-description">
                        <strong><?php echo esc_html__('New Filter Profile', 'woocommerce-bulk-editor') ?></strong>
                        <span><?php echo esc_html__('Here you can type any title and save current filters set. Type here any title and then press Save button OR press Enter button on your keyboard!', 'woocommerce-bulk-editor') ?></span>
                    </div>
                    <div class="woobe-form-element">
                        <div class="products_search_container">
                            <input type="text" value="" id="woobe_new_fprofile" />
                        </div>
                    </div>
                </div>


                <div class="woobe-form-element-container woobe-new-fprofile-attention">

                    <div class="notice notice-info">
                        <p>
                            <?php esc_html_e('You can save filter profile only when you applying filters to the products.', 'woocommerce-bulk-editor') ?>    
                        </p>
                    </div>

                </div>

            </div>
            <div class="woobe-modal-inner-footer">
                <a href="javascript:void(0)" class="button button-primary button-large button-large-1"  id="woobe_new_fprofile_btn"><?php echo esc_html__('Save', 'woocommerce-bulk-editor') ?></a>
                <a href="javascript:void(0)" class="woobe-modal-close-fprofile button button-primary button-large button-large-2"><?php echo esc_html__('Close', 'woocommerce-bulk-editor') ?></a>
            </div>
        </div>
    </div>

    <div class="woobe-modal-backdrop" style="z-index: 15001;"></div>

</div>
