<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th colspan="2">
        <span class="description"><?php _e('We recommend that you use <a href="http://docs.amazonwebservices.com/IAM/latest/UserGuide/AccessPolicyLanguage_KeyConcepts.html" target="_blank"><acronym title="AWS Identity and Access Management">IAM</acronym></a> to create a new policy for <acronym title="Amazon Web Services">AWS</acronym> services that have limited permissions. A helpful tool: <a href="http://awspolicygen.s3.amazonaws.com/policygen.html" target="_blank"><acronym title="Amazon Web Services">AWS</acronym> Policy Generator</a>', 'w3-total-cache'); ?></span>
    </th>
</tr>
<tr>
	<th style="width: 300px;"><label for="cdn_cf2_key"><?php _e('Access key ID:', 'w3-total-cache'); ?></label></th>
	<td>
		<input id="cdn_cf2_key" class="w3tc-ignore-change" type="text"
                       <?php $this->sealing_disabled('cdn') ?> name="cdn.cf2.key" value="<?php echo esc_attr($this->_config->get_string('cdn.cf2.key')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_cf2_secret"><?php _e('Secret key:', 'w3-total-cache'); ?></label></th>
	<td>
		<input id="cdn_cf2_secret" class="w3tc-ignore-change"
                       <?php $this->sealing_disabled('cdn') ?> type="password" name="cdn.cf2.secret" value="<?php echo esc_attr($this->_config->get_string('cdn.cf2.secret')); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><?php _e('Origin:', 'w3-total-cache'); ?></th>
	<td>
		<?php echo w3_get_host(); ?>
		<input id="cdn_create_container"
                       <?php $this->sealing_disabled('cdn') ?> class="button {type: 'cf2', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Create distribution', 'w3-total-cache'); ?>" />
        <span id="cdn_create_container_status" class="w3tc-status w3tc-process"></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_cf2_ssl"><?php _e('<acronym title="Secure Sockets Layer">SSL</acronym> support:', 'w3-total-cache'); ?></label></th>
	<td>
		<select id="cdn_cf2_ssl" name="cdn.cf2.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.cf2.ssl'), 'auto'); ?>><?php _e('Auto (determine connection type automatically)', 'w3-total-cache'); ?></option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.cf2.ssl'), 'enabled'); ?>><?php _e('Enabled (always use SSL)', 'w3-total-cache'); ?></option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.cf2.ssl'), 'disabled'); ?>><?php _e('Disabled (always use HTTP)', 'w3-total-cache'); ?></option>
		</select>
        <br /><span class="description"><?php _e('Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache'); ?></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_cf2_id"><?php _e('Replace site\'s hostname with:', 'w3-total-cache'); ?></label></th>
	<td>
		<input id="cdn_cf2_id" type="text" name="cdn.cf2.id"
                       <?php $this->sealing_disabled('cdn') ?> value="<?php echo esc_attr($this->_config->get_string('cdn.cf2.id')); ?>" size="18" style="text-align: right;" />.cloudfront.net or CNAME:
		<?php $cnames = $this->_config->get_array('cdn.cf2.cname'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
		<br /><span class="description"><?php _e('If you have already added a <a href="http://docs.amazonwebservices.com/AmazonCloudFront/latest/DeveloperGuide/index.html?CNAMEs.html" target="_blank">CNAME</a> to your <acronym title="Domain Name System">DNS</acronym> Zone, enter it here.', 'w3-total-cache'); ?></span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'cf2', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test CloudFront distribution', 'w3-total-cache'); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
