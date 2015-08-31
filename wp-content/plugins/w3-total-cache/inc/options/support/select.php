<?php if (!defined('W3TC')) die(); ?>
<div class="metabox-holder">
    <?php echo $this->postbox_header(__('Choose Request Type', 'w3-total-cache')); ?>
    <table class="form-table">
        <tr>
            <th><label for="support_request_type"><?php _e('Request type:', 'w3-total-cache'); ?></label></th>
            <td>
                <select id="support_request_type" class="w3tc-ignore-change {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" name="request_type">
                    <option value=""><?php _e('-- Choose Type --', 'w3-total-cache'); ?></option>
                    <?php foreach ($this->_request_groups as $_request_group => $_request_types): ?>
                    <optgroup label="<?php echo esc_attr($_request_group); ?>:">
                    <?php foreach ($_request_types as $_request_type): ?>
                        <option value="<?php echo esc_attr($_request_type); ?>"><?php echo htmlspecialchars($this->_request_types[$_request_type]); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php echo $this->postbox_footer(); ?>
</div>
