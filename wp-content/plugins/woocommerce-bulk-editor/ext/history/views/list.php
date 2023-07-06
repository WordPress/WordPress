<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<?php if (!empty($history)): ?>
    <ul class="woobe_fields" id="woobe_history_list">
        <?php foreach ($history as $operation) : ?>
            <?php $is_solo = isset($operation['field_key']) ? true : false; ?>

            <li class="woobe_options_li <?php echo($is_solo ? 'solo_li' : 'bulk_li') ?> woobe_history_item woobe_history_li_show" id="woobe_history_<?php echo($is_solo ? $operation['id'] : $operation['bulk_key']) ?>">
                <?php
                $filds="";
                if($is_solo){
                    $filds=$operation['field_key'];
                }else{
                    if(!empty($operation['set_of_keys'])){
                       $keys = json_decode($operation['set_of_keys']); 
                       if($keys){
                           $filds=implode(",",$keys);
                       }
                    }
                }
                
                ?>
                <div class="woobe_history_data woobe_history_hidden" data-types="<?php echo($is_solo ? 1 : 2) ?>" data-author="<?php echo $operation['user_id'] ?>"  data-date="<?php echo ($is_solo)?$operation["mod_date"]:$operation['started']; ?>" data-fields="<?php echo  $filds ?>">
                </div>   
                <div class="col-lg-4">
                    <?php if ($is_solo): ?>
                        <h5 style="margin: 0;"><?php echo '#' . $operation['product_id'] . '. ' . get_the_title($operation['product_id']) ?></h5>
                        <h6 style="margin: 0;">[<?php echo $operation['field_key'] ?>]</h6>
                    <?php else: ?>
                        <h5 style="margin: 0;"><?php esc_html_e('Bulk operation', 'woocommerce-bulk-editor') ?></h5>
                        [<span style="color: <?php echo($operation['state'] == 'completed' ? 'green' : 'red') ?>;"><small><?php printf(esc_html__('state: %s', 'woocommerce-bulk-editor'), $operation['state']) ?></small></span>]
                    <?php endif; ?>
                </div>

                <div class="col-lg-3">
                    <small>
                        <?php
                        if ($is_solo) {
                            echo date(get_option('date_format') . ' ' . get_option('time_format'), $operation['mod_date']);
                        } else {
                            echo date(get_option('date_format') . ' ' . get_option('time_format'), $operation['started']);
                            if ($operation['state'] !== 'terminated') {
                                echo ' - ' . date(get_option('date_format') . ' ' . get_option('time_format'), $operation['finished']);
                            }
                        }
                        ?>
                    </small>
                </div>


                <div class="col-lg-3">
                    <small>
                        <?php
                        if ($is_solo) {
                            $show_val = in_array($settings_fields[$operation['field_key']]['edit_view'], array('textinput'));
                            $sanitize = '';
                            if (isset($settings_fields[$operation['field_key']]['sanitize'])) {
                                $sanitize = $settings_fields[$operation['field_key']]['sanitize'];
                            }
                            if ($show_val) {
                                printf(esc_html__('value been: %s', 'woocommerce-bulk-editor'), '<i>' . substr($products_obj->sanitize_answer_value($operation['field_key'], $sanitize, $operation['prev_val']), 0, 120) . '</i>');
                            } else {
                                echo '&nbsp;';
                            }
                        } else {
                            printf(esc_html__('products bulked: %s', 'woocommerce-bulk-editor'), '<b>' . intval($operation['products_count']) . '</b>');

                            if (!empty($operation['set_of_keys'])) {
                                $set_of_keys = json_decode($operation['set_of_keys']);
                                $names = array();
                                foreach ($set_of_keys as $kk) {
                                    $names[] = $settings_fields_full[$kk]['title'];
                                }
                                $names = implode(', ', $names);
                                echo '<br />';
                                printf(esc_html__('columns: %s', 'woocommerce-bulk-editor'), '<b>' . $names . '</b>');
                            }
                        }
                        ?>
                    </small>
                </div>


                <div class="col-lg-2 tar">
                    <?php if ($is_solo): ?>
                        <a href="javascript: woobe_history_revert_solo(<?php echo $operation['id'] ?>, <?php echo $operation['product_id'] ?>);void(0);" class="button button-primary woobe_history_btn woobe_history_revert" title="<?php esc_html_e('revert', 'woocommerce-bulk-editor') ?>"></a>
                        <a href="javascript: woobe_history_delete_solo(<?php echo $operation['id'] ?>);void(0);" class="button button-primary woobe_history_btn woobe_history_delete" title="<?php esc_html_e('delete', 'woocommerce-bulk-editor') ?>"></a><br />
                    <?php else: ?>

                        <a href="javascript: woobe_history_revert_bulk('<?php echo $operation['bulk_key'] ?>', <?php echo $operation['id'] ?>);void(0);" class="button button-primary woobe_history_btn woobe_history_revert" title="<?php esc_html_e('revert', 'woocommerce-bulk-editor') ?>"></a>
                        <a href="javascript: woobe_history_delete_bulk('<?php echo $operation['bulk_key'] ?>');void(0);" class="button button-primary woobe_history_btn woobe_history_delete" title="<?php esc_html_e('delete', 'woocommerce-bulk-editor') ?>"></a><br />

                        <div class="woobe_progress" style="display: none;">
                            <div class="woobe_progress_in" id="woobe_bulk_progress_<?php echo $operation['id'] ?>">0%</div>
                        </div>

                    <?php endif; ?>
                </div>

                <div class="clear"></div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>

    <h5><?php esc_html_e('History is empty!', 'woocommerce-bulk-editor') ?></h5>

<?php endif; ?>
