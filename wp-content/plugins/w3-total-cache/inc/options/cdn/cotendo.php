<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label for="cdn_cotendo_username"><?php _e('Username:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="cdn_cotendo_username" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.cotendo.username" value="<?php echo esc_attr($this->_config->get_string('cdn.cotendo.username')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_cotendo_password"><?php _e('Password:', 'w3-total-cache'); ?></label></th>
    <td>
        <input id="cdn_cotendo_password" class="w3tc-ignore-change"
           <?php $this->sealing_disabled('cdn') ?> type="password" name="cdn.cotendo.password" value="<?php echo esc_attr($this->_config->get_string('cdn.cotendo.password')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_cotendo_zones"><?php _e('Zones to purge:', 'w3-total-cache'); ?></label></th>
    <td>
        <textarea id="cdn_cotendo_zones" name="cdn.cotendo.zones"
            <?php $this->sealing_disabled('cdn') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('cdn.cotendo.zones'))); ?></textarea>
    </td>
</tr>
<tr>
	<th><label for="cdn_cotendo_ssl"><?php _e('<acronym title="Secure Sockets Layer">SSL</acronym> support:', 'w3-total-cache'); ?></label></th>
	<td>
		<select id="cdn_cotendo_ssl" name="cdn.cotendo.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.cotendo.ssl'), 'auto'); ?>><?php _e('Auto (determine connection type automatically)', 'w3-total-cache'); ?></option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.cotendo.ssl'), 'enabled'); ?>><?php _e('Enabled (always use SSL)', 'w3-total-cache'); ?></option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.cotendo.ssl'), 'disabled'); ?>><?php _e('Disabled (always use HTTP)', 'w3-total-cache'); ?></option>
		</select>
        <br /><span class="description"><?php _e('Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache'); ?></span>
	</td>
</tr>
<tr>
    <th><?php _e('Replace site\'s hostname with:', 'w3-total-cache'); ?></th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.cotendo.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description"><?php _e('Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache'); ?></span>
    </td>
</tr>
<tr>
	<th colspan="2">
	<input id="cdn_test" class="button {type: 'cotendo', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test Cotendo', 'w3-total-cache'); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
