<?php


function to_plugin_options()
    {
        $options = tto_get_settings();
        
        if (isset($_POST['to_form_submit']) &&  wp_verify_nonce($_POST['to_form_nonce'],'to_form_submit') )
            {
                    
                $options['capability'] = sanitize_key($_POST['capability']);
                
                $options['autosort']    = isset($_POST['autosort'])     ? intval($_POST['autosort'])    : '';
                $options['adminsort']   = isset($_POST['adminsort'])    ? intval($_POST['adminsort'])   : '';
                    
                ?><div class="updated fade"><p><?php _e('Settings Saved', 'taxonomy-terms-order') ?></p></div><?php

                update_option('tto_options', $options);   
            }
                        
                    ?>
                      <div class="wrap"> 
                        
                            <h2><?php _e( "General Settings", 'taxonomy-terms-order' ) ?></h2>
                            
                            <?php tto_info_box() ?>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle"><?php _e( "General", 'taxonomy-terms-order' ) ?></h2>                              
                                <table class="form-table">
                                    <tbody>
                            
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Minimum Level to use this plugin", 'taxonomy-terms-order' ) ?></label></th>
                                            <td>
                                                <select id="role" name="capability">
                                                    <option value="read" <?php if (isset($options['capability']) && $options['capability'] == "read") echo 'selected="selected"'?>><?php _e('Subscriber', 'taxonomy-terms-order') ?></option>
                                                    <option value="edit_posts" <?php if (isset($options['capability']) && $options['capability'] == "edit_posts") echo 'selected="selected"'?>><?php _e('Contributor', 'taxonomy-terms-order') ?></option>
                                                    <option value="publish_posts" <?php if (isset($options['capability']) && $options['capability'] == "publish_posts") echo 'selected="selected"'?>><?php _e('Author', 'taxonomy-terms-order') ?></option>
                                                    <option value="publish_pages" <?php if (isset($options['capability']) && $options['capability'] == "publish_pages") echo 'selected="selected"'?>><?php _e('Editor', 'taxonomy-terms-order') ?></option>
                                                    <option value="manage_options" <?php if (!isset($options['capability']) || empty($options['capability']) || (isset($options['capability']) && $options['capability'] == "manage_options")) echo 'selected="selected"'?>><?php _e('Administrator', 'taxonomy-terms-order') ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Auto Sort", 'taxonomy-terms-order' ) ?></label></th>
                                            <td>
                                                <select id="autosort" name="autosort">
                                                    <option value="0" <?php if ($options['autosort'] == "0") echo 'selected="selected"'?>><?php _e('OFF', 'taxonomy-terms-order') ?></option>
                                                    <option value="1" <?php if ($options['autosort'] == "1") echo 'selected="selected"'?>><?php _e('ON', 'taxonomy-terms-order') ?></option>
                                                </select>
                                                <label for="autosort"> *(<?php _e( "global setting", 'taxonomy-terms-order' ) ?>) <?php _e( "Additional description and details at ", 'taxonomy-terms-order' ) ?><a target="_blank" href="http://www.nsp-code.com/taxonomy-terms-order-and-auto-sort-admin-sort-description-an-usage/"><?php _e( "Auto Sort Description", 'taxonomy-terms-order' ) ?></a></label>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Admin Sort", 'taxonomy-terms-order' ) ?></label></th>
                                            <td>
                                                <select id="adminsort" name="adminsort">
                                                    <option value="0" <?php if ($options['adminsort'] == "0") echo 'selected="selected"'?>><?php _e('OFF', 'taxonomy-terms-order') ?></option>
                                                    <option value="1" <?php if ($options['adminsort'] == "1") echo 'selected="selected"'?>><?php _e('ON', 'taxonomy-terms-order') ?></option>
                                                </select>
                                                <label for="adminsort"><?php _e("This will change the order of terms within the admin interface", 'taxonomy-terms-order') ?>. <?php _e( "Additional description and details at ", 'taxonomy-terms-order' ) ?><a target="_blank" href="http://www.nsp-code.com/taxonomy-terms-order-and-auto-sort-admin-sort-description-an-usage/"><?php _e( "Auto Sort Description", 'taxonomy-terms-order' ) ?></a></label>
                                            </td>
                                        </tr>
                                        
                                        
                                        
                                    </tbody>
                                </table>
                                

                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'taxonomy-terms-order') ?>">
                               </p>
                            
                                <?php wp_nonce_field('to_form_submit','to_form_nonce'); ?>
                                <input type="hidden" name="to_form_submit" value="true" />
                                
                            </form>
                                                        
                    <?php  
            echo '</div>';   
        
        
    }

?>