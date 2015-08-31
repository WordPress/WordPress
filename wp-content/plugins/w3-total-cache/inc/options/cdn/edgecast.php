<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label for="cdn_edgecast_account"><?php _e('Account #:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="cdn_edgecast_account" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.edgecast.account" value="<?php echo esc_attr($this->_config->get_string('cdn.edgecast.account')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_edgecast_token"><?php _e('Token:', 'w3-total-cache'); ?></th>
    <td>
        <input id="cdn_edgecast_token" class="w3tc-ignore-change" type="password"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.edgecast.token" value="<?php echo esc_attr($this->_config->get_string('cdn.edgecast.token')); ?>" size="60" />
    </td>
</tr>
<tr>
	<th><label for="cdn_edgecast_ssl"><?php _e('<acronym title="Secure Sockets Layer">SSL</acronym> support:', 'w3-total-cache'); ?></label></th>
	<td>
		<select id="cdn_edgecast_ssl" name="cdn.edgecast.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.edgecast.ssl'), 'auto'); ?>><?php _e('Auto (determine connection type automatically)', 'w3-total-cache'); ?></option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.edgecast.ssl'), 'enabled'); ?>><?php _e('Enabled (always use SSL)', 'w3-total-cache'); ?></option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.edgecast.ssl'), 'disabled'); ?>><?php _e('Disabled (always use HTTP)', 'w3-total-cache'); ?></option>
		</select>
        <br /><span class="description"><?php _e('Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache'); ?></span>
	</td>
</tr>
<tr>
    <th><?php _e('Replace site\'s hostname with:', 'w3-total-cache'); ?></th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.edgecast.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description"><?php _e('Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache'); ?></span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'edgecast', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test EdgeCast', 'w3-total-cache'); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
