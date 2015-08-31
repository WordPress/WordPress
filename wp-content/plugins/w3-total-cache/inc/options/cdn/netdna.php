<?php
/**
 * @var bool $authorized
 * @var array $pull_zones
 */
?>
<?php if (!defined('W3TC')) die(); ?>
<?php if (!$authorized): ?>
    <tr>
        <th style="width: 300px;"><label><?php _e('Create account:', 'w3-total-cache')?></label></th>
        <td>
            <a href="<?php esc_attr_e(wp_nonce_url(w3_admin_url('admin.php?page=w3tc_dashboard&w3tc_cdn_maxcdn_signup'), 'w3tc'))?>" target="_blank" id="netdna-maxcdn-create-account" class="button-primary"><?php _e('Create Account', 'w3-total-cache') ?></a>
            <br />
            <span class="description"><?php _e('Are you a new customer? Click the Create Account button to receive a discount on a new MaxCDN account.', 'w3-total-cache')?></span>
        </td>
    </tr>
<?php endif ?>

<?php if ($authorized): ?>
<tr>
    <th style="width: 300px;"><label><?php !$pull_zones ? _e('Create pull zone:', 'w3-total-cache') : _e('Create new pull zone:', 'w3-total-cache')?></label></th>
    <td>
        <button id="netdna-maxcdn-create-pull-zone" <?php $this->sealing_disabled('cdn') ?> class="button-primary {type: 'netdna', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}"><?php !$pull_zones ? _e('Create pull zone', 'w3-total-cache') : _e('Create new pull zone', 'w3-total-cache')?></button>
        <br />
        <span class="description"><?php _e('Click the Create Pull Zone button above and create a pull zone manually for this site.', 'w3-total-cache')?></span>
    </td>
</tr>
<?php elseif(!$authorized): ?>
<tr>
    <th style="width: 300px;"><label><?php _e('Specify account credentials:', 'w3-total-cache')?></label></th>
    <td>
        <a id="cdn_netdna_oauth" class="button-primary" href="<?php echo wp_nonce_url(w3_admin_url('admin.php?page=w3tc_dashboard&w3tc_cdn_netdna_authorize'), 'w3tc')?>" target="_blank"><?php _e('Authorize', 'w3-total-cache')?></a>
        <br />
        <span class="description"><?php _e('Click the Authorize button above, log in, paste the key below and save settings.', 'w3-total-cache')?></span>
    </td>
</tr>
<?php endif ?>
<tr>
    <th style="width: 300px;"><label for="cdn_netdna_authorization_key"><?php _e('Authorization key', 'w3-total-cache')?>:</label></th>
    <td id="authorization_area">
        <input id="cdn_netdna_authorization_key" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.netdna.authorization_key" value="<?php echo esc_attr($this->_config->get_string('cdn.netdna.authorization_key')); ?>" size="60" onblur="w3tc_validate_cdn_key_result('netdna','<?php echo wp_create_nonce('w3tc'); ?>')" />
        <input id="validate_cdn_key" type="button" class="button {type: 'netdna', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('Validate', 'w3-total-cache') ?>" /><span id="validate_cdn_key_result" style="margin-left:15px;padding:3px 8px 3px 8px;"></span>
        <br /><span class="description"><?php _e('Consists of alias+key+secret . Example: bluewidgets+asd897asd98a7sd+798a7sd9 . If you use "Authorize" its already formatted correctly.', 'w3-total-cache')?></span>
    </td>
</tr>
<tr id="create_zone_area" style="<?php echo $authorized && (!$have_zone || is_null($zone_info)) ? '' : 'display: none' ?>">
    <th><?php _e('Create zone:', 'w3-total-cache') ?></th>
    <td><input type="button" class="button" onclick="w3tc_create_zone('netdna','<?php echo wp_create_nonce('w3tc'); ?>')" value="<?php _e('Create Default Zone', 'w3-total-cache') ?>" id="create_default_zone" />
        <span id="create_pull_zone_result" style="padding:3px 0 3px 20px;"></span>
        <br /><span class="description"><?php _e('You have no zone connected with this site. Click button to create a default zone automatically.', 'w3-total-cache') ?></span>
    </td>
</tr>
<tr id="select_pull_zone" style="<?php echo !($authorized && $pull_zones) ? 'display:none' : '' ?>">
    <th><label for="cdn_netdna_zone_id"><?php _e('Select pull zone:', 'w3-total-cache')?></label></th>
    <td>
        <select id="cdn_netdna_zone_id" name="cdn.netdna.zone_id" <?php $this->sealing_disabled('cdn') ?>>
            <?php foreach($pull_zones as $zone):?>
                <option value="<?php echo $zone['id'] ?>" <?php selected($zone['id'], $this->_config->get_integer('cdn.netdna.zone_id'))?>><?php echo $zone['name']?></option>
            <?php endforeach; ?>
        </select> <input id="use_poll_zone" type="button" class="button {type: 'netdna', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('Use Zone', 'w3-total-cache') ?>" /><span id="use_pull_zone_result" style="margin-left:15px;padding:3px 8px 3px 8px;"></span>
        <br />
        <span class="description"><?php _e('Select the pull zone to use with this site.', 'w3-total-cache')?></span>
    </td>
</tr>

<tr>
	<th><label for="cdn_netdna_ssl"><?php _e('<acronym title="Secure Sockets Layer">SSL</acronym> support', 'w3-total-cache')?>:</label></th>
	<td>
		<select id="cdn_netdna_ssl" name="cdn.netdna.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.netdna.ssl'), 'auto'); ?>><?php _e('Auto (determine connection type automatically)', 'w3-total-cache')?></option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.netdna.ssl'), 'enabled'); ?>><?php _e('Enabled (always use SSL)', 'w3-total-cache')?></option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.netdna.ssl'), 'disabled'); ?>><?php _e('Disabled (always use HTTP)', 'w3-total-cache')?></option>
		</select>
        <br /><span class="description"><?php _e('Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache')?></span>
	</td>
</tr>
<tr>
    <th><?php _e('Replace site\'s hostname with:', 'w3-total-cache')?></th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.netdna.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description"><?php _e('Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache')?></span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'netdna', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test NetDNA', 'w3-total-cache')?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
