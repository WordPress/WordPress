<?php echo w3tc_postbox_header(__('Network Performance &amp; Security powered by CloudFlare', 'w3-total-cache'), '', 'cloudflare'); ?>
<p>
    <?php _e('CloudFlare protects and accelerates websites.', 'w3-total-cache') ?>
</p>

<table class="form-table">
    <tr>
        <th><?php w3_e_config_label('cloudflare.enabled', 'general') ?></th>
        <td>
            <label>
            <?php
            list($name, $id) = w3tc_get_name_and_id('cloudflare', 'enabled');
            w3_ui_element('checkbox', 'cloudflare.enabled', $name, w3tc_get_extension_config('cloudflare', 'enabled'), w3_extension_is_sealed('cloudflare')); ?>
                &nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
        </td>
    </tr>
    <tr>
        <?php
        list($name, $id) = w3tc_get_name_and_id('cloudflare', 'email'); ?>

        <th><label for="cloudflare_email"><?php w3_e_config_label('cloudflare.email', 'general') ?></label></th>
        <td>
            <?php w3_ui_element('textbox', 'cloudflare.email', $name, w3tc_get_extension_config('cloudflare', 'email'), w3_extension_is_sealed('cloudflare')); ?>
        </td>
    </tr>
    <tr>
        <?php
        list($name, $id) = w3tc_get_name_and_id('cloudflare', 'key'); ?>

        <th><label for="cloudflare_key"><?php w3_e_config_label('cloudflare.key', 'general') ?></label></th>
        <td>
            <?php w3_ui_element('password', 'cloudflare.key', $name, w3tc_get_extension_config('cloudflare', 'key'), w3_extension_is_sealed('cloudflare')); ?>
            (<a href="https://www.cloudflare.com/my-account.html"><?php _e('find it here', 'w3-total-cache'); ?></a>)
        </td>
    </tr>
    <tr>
        <?php
        list($name, $id) = w3tc_get_name_and_id('cloudflare', 'zone'); ?>
        <th><?php w3_e_config_label('cloudflare.zone', 'general') ?></th>
        <td>
            <?php w3_ui_element('textbox', 'cloudflare.zone', $name, w3tc_get_extension_config('cloudflare', 'zone'), w3_extension_is_sealed('cloudflare')); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Security level:', 'w3-total-cache'); ?></th>
        <td>
            <input type="hidden" name="cloudflare_sec_lvl_old" value="<?php echo $cloudflare_seclvl; ?>" />
            <select name="cloudflare_sec_lvl_new"
                    class="w3tc-ignore-change"
                <?php w3tc_sealing_disabled('cloudflare'); ?>>
                <?php foreach ($cloudflare_seclvls as $cloudflare_seclvl_key => $cloudflare_seclvl_label): ?>
                    <option value="<?php echo esc_attr($cloudflare_seclvl_key); ?>"<?php selected($cloudflare_seclvl, $cloudflare_seclvl_key); ?>><?php echo $cloudflare_seclvl_label; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e('Rocket Loader:', 'w3-total-cache'); ?></th>
        <td>
            <input type="hidden" name="cloudflare_async_old" value="<?php echo $cloudflare_rocket_loader; ?>" />
            <select name="cloudflare_async_new"
                    class="w3tc-ignore-change"
                <?php w3tc_sealing_disabled('cloudflare'); ?>>
                <?php foreach ($cloudflare_rocket_loaders as $cloudflare_rocket_loader_key => $cloudflare_rocket_loader_label): ?>
                    <option value="<?php echo $cloudflare_rocket_loader_key; ?>"<?php selected($cloudflare_rocket_loader, $cloudflare_rocket_loader_key); ?>><?php echo $cloudflare_rocket_loader_label; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e('Minification:', 'w3-total-cache'); ?></th>
        <td>
            <input type="hidden" name="cloudflare_minify_old" value="<?php echo $cloudflare_minify; ?>" />
            <select name="cloudflare_minify_new"
                    class="w3tc-ignore-change"
                <?php w3tc_sealing_disabled('cloudflare'); ?>>
                <?php foreach ($cloudflare_minifications as $cloudflare_minify_key => $cloudflare_minify_label): ?>
                    <option value="<?php echo esc_attr($cloudflare_minify_key); ?>"<?php selected($cloudflare_minify, $cloudflare_minify_key); ?>><?php echo $cloudflare_minify_label; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e('Development mode:', 'w3-total-cache'); ?></th>
        <td>
            <input type="hidden" name="cloudflare_devmode_old" value="<?php echo $cloudflare_devmode; ?>" />
            <select name="cloudflare_devmode_new"
                    class="w3tc-ignore-change"
                <?php w3tc_sealing_disabled('cloudflare'); ?>>
                <?php foreach ($cloudflare_devmodes as $cloudflare_devmode_key => $cloudflare_devmode_label): ?>
                    <option value="<?php echo esc_attr($cloudflare_devmode_key); ?>"<?php selected($cloudflare_devmode, $cloudflare_devmode_key); ?>><?php echo $cloudflare_devmode_label; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($cloudflare_devmode_expire): ?>
                <?php echo sprintf( __('Will automatically turn off at %s', 'w3-total-cache'), date('m/d/Y H:i:s', $cloudflare_devmode_expire) ); ?>>
            <?php endif; ?>
        </td>
    </tr>
    <?php if (is_network_admin() && !w3_force_master()): ?>
        <tr>
            <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
            <td>
                <?php w3tc_checkbox_admin('cloudflare.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
            </td>
        </tr>
    <?php endif; ?>
</table>

<p class="submit">
    <?php echo w3tc_nonce_field('w3tc'); ?>
    <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
    <input id="cloudflare_purge_cache" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Purge cache', 'w3-total-cache'); ?>"<?php if (! $cloudflare_enabled): ?> disabled="disabled"<?php endif; ?> />
</p>
<?php echo w3tc_postbox_footer(); ?>
