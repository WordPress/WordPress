<div>
    Unlock more speed, <input type="button" class="button-primary button-buy-plugin {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('upgrade', 'w3-total-cache') ?>" /> now!
    <div id="w3tc-license-instruction" style="display: none;">
    <span class="description"><?php printf(__('Please enter the license key you received after successful checkout %s.', 'w3-total-cache'),
            '<a href="' . (is_network_admin() ?
                                network_admin_url('admin.php?page=w3tc_general#licensing') :
                                admin_url('admin.php?page=w3tc_general#licensing')) .'">' . __('here', 'w3-total-cache') . '</a>')
        ?></span>
    </div>
</div>
