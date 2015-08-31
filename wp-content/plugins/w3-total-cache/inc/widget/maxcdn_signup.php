<?php if (!defined('W3TC')) die(); ?>
<div id="maxcdn-widget" class="sign-up maxcdn-netdna-widget-base">
    <?php if ($error): ?>
    <?php w3_e_error_box('<p>' . sprintf(__('MaxCDN encountered an error trying to retrieve data, make sure your host support cURL and outgoing requests: %s', 'w3-total-cache'), $error) . '</p>') ?>
    <?php endif; ?>
    <?php if (!$authorized): ?>
    <p><?php _e('Add the MaxCDN content delivery network to increase website speeds dramatically in just a few minutes!', 'w3-total-cache')?></p>
    <h4><?php _e('New customers', 'w3-total-cache')?></h4>
    <p><?php _e('MaxCDN is a service that lets you speed up your site even more with W3 Total Cache.', 'w3-total-cache')?></p>
    <a class="button-primary" href="<?php echo wp_nonce_url(w3_admin_url('admin.php?page=w3tc_dashboard&w3tc_cdn_maxcdn_signup'), 'w3tc')?>" target="_blank"><?php _e('Sign Up Now and Save 25%', 'w3-total-cache')?></a>
    <p><span class="desc"><?php _e('100% Money Back Guarantee (30 Days)', 'w3-total-cache')?></span></p>
     <?php endif ?>
        <h4><?php _e('Current customers', 'w3-total-cache')?></h4>
        <p><?php _e("Once you've signed up or if you're an existing MaxCDN customer, to enable CDN:", 'w3-total-cache')?></p>
        <a class="button-primary" href="<?php echo wp_nonce_url(w3_admin_url('admin.php?page=w3tc_dashboard&w3tc_cdn_maxcdn_authorize'), 'w3tc')?>" target="_blank"><?php _e('Authorize', 'w3-total-cache')?></a>
    <form action="admin.php?page=w3tc_dashboard" method="post">
        <p>
            <label for="cdn_maxcdn_authorization_key"><?php _e('Authorization key', 'w3-total-cache')?>:</label>
            <input name="maxcdn" value="1" type="hidden" />
            <input id="cdn_maxcdn_authorization_key" class="w3tc-ignore-change" type="text" <?php echo $is_sealed? 'disabled="disabled"':'' ?> name="cdn.maxcdn.authorization_key"
                   value="<?php echo esc_attr($this->_config->get_string('cdn.maxcdn.authorization_key')); ?>" size="31"
                   onblur="w3tc_validate_cdn_key_result('maxcdn','<?php echo wp_create_nonce('w3tc'); ?>')" />
            <br />
            <input id="validate_cdn_key" type="button" class="button {type: 'maxcdn', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('Validate', 'w3-total-cache') ?>" /><span id="validate_cdn_key_result" style="margin-left:5px;padding:3px 8px 3px 20px;"></span>
            <span id="validate_cdn_key_result" style="margin-left:5px;padding:3px 8px 3px 20px;"></span>
        </p>
    </form>
        <p id="create_zone_area" style="<?php echo $authorized && (!$have_zone || is_null($zone_info)) ? '' : 'display: none' ?>"><input type="button" class="button" onclick="w3tc_create_zone('maxcdn','<?php echo wp_create_nonce('w3tc'); ?>')" value="<?php _e('Create Default Zone', 'w3-total-cache') ?>" />
            <span id="create_pull_zone_result" style="margin-left:15px;padding:3px 8px 3px 8px;"></span>
            <br /><span class="description"><?php _e('You have no zone connected with this site. Click button to create a default zone.', 'w3-total-cache') ?></span>
        </p>
        <p id="select_pull_zone" style="<?php echo !($authorized && $pull_zones) ? 'display:none' : '' ?>">
            <select id="cdn_maxcdn_zone_id" name="cdn.maxcdn.zone_id">
                <?php foreach($pull_zones as $zone):?>
                    <option value="<?php echo $zone['id'] ?>" <?php selected($zone['id'], $this->_config->get_integer('cdn.maxcdn.zone_id'))?>><?php echo $zone['name']?></option>
                <?php endforeach; ?>
            </select> <input id="use_poll_zone" type="button" class="button {type: 'maxcdn', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('Use Zone', 'w3-total-cache') ?>" /><span id="use_pull_zone_result" style="margin-left:15px;padding:3px 8px 3px 8px;"></span>
            <br />
            <span class="description"><?php _e('Select the pull zone to use with this site.', 'w3-total-cache')?></span>
        </p>
    <p id="cdn_result_message" class="description"></p>
    <?php  if($authorized && (!$have_zone || is_null($zone_info))): ?>
    <button id="netdna-maxcdn-create-pull-zone" class="button-primary {type: 'maxcdn', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}"><?php _e('Create Pull Zone', 'w3-total-cache')?></button>
    <?php endif ?>
</div>
