<?php if (!defined('W3TC')) die(); ?>
<div id="w3tc-edge-mode">
    <div class="w3tc-overlay-logo"></div>
    <header>
    </header>
    <div class="content">
    <p><strong><?php _e('You can now keep up-to-date on all of the web performance optimization (WPO) techniques that will make your website as fast as possible without having to worry about new features breaking your website when you update!', 'w3-total-cache') ?></strong></p>
    <p><?php _e('Enable "edge mode" to opt-in to pre-release features or simply close this window to opt-in to bug fixes, security fixes and settings updates only.', 'w3-total-cache') ?></p>
    </div>
    <div class="footer">
        <?php
        $wp_url = w3_admin_url('admin.php?page='. $page .'&w3tc_edge_mode_enable');
        echo w3tc_action_button(__('Enable Edge Mode', 'w3-total-cache'), $wp_url, "btn w3tc-size image btn-default palette-turquoise") ?>
        <?php wp_nonce_field('w3tc') ?>
        <input type="button" class="button-cancel btn w3tc-size btn-default outset palette-light-grey" value="<?php _e('Cancel', 'w3-total-cache') ?>">
    </div>
</div>
