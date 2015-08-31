<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th style="width: 300px;"><label for="cdn_rscf_user"><?php _e('Username:', 'w3-total-cache'); ?></label></th>
	<td>
		<input id="cdn_rscf_user" class="w3tc-ignore-change" type="text"
                   <?php $this->sealing_disabled('cdn') ?> name="cdn.rscf.user" value="<?php echo esc_attr($this->_config->get_string('cdn.rscf.user')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_key"><?php _e('<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache'); ?></label></th>
	<td>
		<input id="cdn_rscf_key" class="w3tc-ignore-change" type="password"
                   <?php $this->sealing_disabled('cdn') ?> name="cdn.rscf.key" value="<?php echo esc_attr($this->_config->get_string('cdn.rscf.key')); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_location"><?php _e('Location:', 'w3-total-cache'); ?></label></th>
	<td>
        <select id="cdn_rscf_location" name="cdn.rscf.location" <?php $this->sealing_disabled('cdn') ?>>
            <option value="us"<?php echo selected($this->_config->get_string('cdn.rscf.location'), 'us'); ?>>US</option>
            <option value="uk"<?php echo selected($this->_config->get_string('cdn.rscf.location'), 'uk'); ?>>UK</option>
        </select>
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_container"><?php _e('Container:', 'w3-total-cache'); ?></label></th>
	<td>
		<input id="cdn_rscf_container" type="text" name="cdn.rscf.container"
                    <?php $this->sealing_disabled('cdn') ?> value="<?php echo esc_attr($this->_config->get_string('cdn.rscf.container')); ?>" size="30" />
		<input id="cdn_create_container"
                    <?php $this->sealing_disabled('cdn') ?> class="button {type: 'rscf', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Create container', 'w3-total-cache'); ?>" />
		<span id="cdn_create_container_status" class="w3tc-status w3tc-process"></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_ssl"><?php _e('<acronym title="Secure Sockets Layer">SSL</acronym> support:', 'w3-total-cache'); ?></label></th>
	<td>
		<select id="cdn_rscf_ssl" name="cdn.rscf.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.rscf.ssl'), 'auto'); ?>><?php _e('Auto (determine connection type automatically)', 'w3-total-cache'); ?></option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.rscf.ssl'), 'enabled'); ?>><?php _e('Enabled (always use SSL)', 'w3-total-cache'); ?></option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.rscf.ssl'), 'disabled'); ?>><?php _e('Disabled (always use HTTP)', 'w3-total-cache'); ?></option>
		</select>
        <br /><span class="description"><?php _e('Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache'); ?></span>
	</td>
</tr>
<tr>
    <th><?php _e('Replace site\'s hostname with:', 'w3-total-cache'); ?></th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.rscf.cname'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description"><?php _e('Enter the hostname provided by Rackspace Cloud Files, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache'); ?></span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'rscf', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test Cloud Files upload', 'w3-total-cache'); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
