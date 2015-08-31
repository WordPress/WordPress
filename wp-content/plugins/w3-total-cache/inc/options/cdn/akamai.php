<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label for="cdn_akamai_username"><?php _e('Username:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="cdn_akamai_username" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.akamai.username" value="<?php echo esc_attr($this->_config->get_string('cdn.akamai.username')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_password"><?php _e('Password:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="cdn_akamai_password" class="w3tc-ignore-change"
           <?php $this->sealing_disabled('cdn') ?> type="password" name="cdn.akamai.password" value="<?php echo esc_attr($this->_config->get_string('cdn.akamai.password')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_email_notification"><?php _e('Email notification:', 'w3-total-cache'); ?></label></th>
    <td>
        <textarea id="cdn_akamai_email_notification" name="cdn.akamai.email_notification"
            <?php $this->sealing_disabled('cdn') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('cdn.akamai.email_notification'))); ?></textarea>
        <br />
        <span class="description"><?php _e('Specify email addresses for completed removal notifications. One email per line.', 'w3-total-cache'); ?></span>
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_zone"><?php _e('Domain to purge:', 'w3-total-cache'); ?></label></th>
    <td>
        <select  id="cdn_akamai_zone" name="cdn.akamai.zone">
            <option value="production" <?php selected($this->_config->get_string('cdn.akamai.zone'), 'production'); ?>>Production</option>
            <option value="staging" <?php selected($this->_config->get_string('cdn.akamai.zone'), 'staging'); ?>>Staging</option>
        </select>
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_action"><?php _e('Purge action:', 'w3-total-cache'); ?></label></th>
    <td>
        <select  id="cdn_akamai_action" name="cdn.akamai.action">
            <option value="invalidate" <?php selected($this->_config->get_string('cdn.akamai.action'), 'invalidate'); ?>>Invalidate</option>
            <option value="remove" <?php selected($this->_config->get_string('cdn.akamai.action'), 'remove'); ?>>Remove</option>
        </select>
    </td>
</tr>
<tr>
	<th><label for="cdn_akamai_ssl"><?php _e('<acronym title="Secure Sockets Layer">SSL</acronym> support:</label>', 'w3-total-cache'); ?></th>
	<td>
		<select id="cdn_akamai_ssl" name="cdn.akamai.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.akamai.ssl'), 'auto'); ?>><?php _e('Auto (determine connection type automatically)', 'w3-total-cache'); ?></option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.akamai.ssl'), 'enabled'); ?>><?php _e('Enabled (always use SSL)', 'w3-total-cache'); ?></option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.akamai.ssl'), 'disabled'); ?>><?php _e('Disabled (always use HTTP)', 'w3-total-cache'); ?></option>
		</select>
        <br /><span class="description"><?php _e('Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache'); ?></span>
	</td>
</tr>
<tr>
    <th><?php _e('Replace site\'s hostname with:', 'w3-total-cache'); ?></th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.akamai.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description"><?php _e('Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache'); ?></span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'akamai', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test akamai', 'w3-total-cache'); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
