<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<p>
    <?php echo sprintf(__('The plugin is currently <span class="w3tc-%s">%s</span> in <strong>%s%s</strong> mode.', 'w3-total-cache')
                      , $enabled ? "enabled" : "disabled"
                      , $enabled ? __('enabled', 'w3-total-cache') : __('disabled', 'w3-total-cache')
                      , w3_w3tc_release_version($this->_config), (w3tc_edge_mode() ? __(' edge', 'w3-total-cache') : ''));
    ?>
</p>
<form id="w3tc_dashboard" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Perform a
        <input type="button" class="button button-self-test {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('compatibility check', 'w3-total-cache') ?>" />,
        <?php echo $this->nonce_field('w3tc'); ?>
        <input id="flush_all" class="button" type="submit" name="w3tc_flush_all" value="<?php _e('empty all caches', 'w3-total-cache') ?>"<?php if (! $can_empty_memcache && ! $can_empty_opcode && ! $can_empty_file && ! $can_empty_varnish): ?> disabled="disabled"<?php endif; ?> /> <?php _e('at once or', 'w3-total-cache') ?>
        <input class="button" type="submit" name="w3tc_flush_memcached" value="<?php _e('empty only the memcached cache(s)', 'w3-total-cache') ?>"<?php if (! $can_empty_memcache): ?> disabled="disabled"<?php endif; ?> /> <?php _e('or', 'w3-total-cache') ?>
        <input class="button" type="submit" name="w3tc_flush_opcode" value="<?php _e('empty only the opcode cache', 'w3-total-cache') ?>"<?php if (! $can_empty_opcode): ?> disabled="disabled"<?php endif; ?> /> <?php _e('or', 'w3-total-cache') ?>
        <?php if ($can_empty_apc_system): ?>
        <input class="button" type="submit" name="w3tc_flush_apc_system" value="<?php _e('empty only the APC system cache', 'w3-total-cache') ?>"<?php if (! $can_empty_apc_system): ?> disabled="disabled"<?php endif; ?> /> <?php _e('or', 'w3-total-cache') ?>
        <?php endif ?>
        <input class="button" type="submit" name="w3tc_flush_file" value="<?php _e('empty only the disk cache(s)', 'w3-total-cache') ?>"<?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> /> <?php _e('or', 'w3-total-cache') ?>
        <?php if ($cdn_mirror_purge && $cdn_enabled): ?>
        <input class="button" type="submit" name="w3tc_flush_cdn" value="<?php _e('purge CDN completely', 'w3-total-cache') ?>" /> <?php _e('or', 'w3-total-cache') ?>
        <?php endif; ?>
        <input type="submit" name="w3tc_flush_browser_cache" value="<?php _e('update Media Query String', 'w3-total-cache') ?>" <?php disabled(! ($browsercache_enabled && $browsercache_update_media_qs)) ?> class="button" />
        <?php
        $string = __('or', 'w3-total-cache');
        echo implode(" $string ", apply_filters('w3tc_dashboard_actions', array())) ?>.
    </p>
</form>

    <div id="w3tc-dashboard-widgets" class="clearfix widefat metabox-holder">
        <?php $screen = get_current_screen();
        ?>
        <div id="postbox-container-left" style="float: left;">
            <div class="content">
            <div id="dashboard-text" style="display:inline-block;">
                <h1><?php _e('Dashboard', 'w3-total-cache')?></h1>
                <p>Thanks for choosing W3TC as your Web Performance Optimization (<acronym title="Web Performance Optimization">WPO</acronym>) framework. Eventually, the dashboard will provide at-a-glance insight into key performance indicators for this WordPress installation. Please share <a href="admin.php?page=w3tc_support&amp;request_type=new_feature">your suggestions</a> about the statistics and reporting you would like to see here!</p>
            </div>
            <div id="widgets-container">
            <?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
            </div>
            </div>
        </div>
        <div id="postbox-container-right">
            <div id='postbox-container-3' class='postbox-container' style="width: 100%;">
                <?php do_meta_boxes( $screen->id, 'side', '' ); ?>
            </div>
        </div>
        <div style="clear:both"></div>

        <?php
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        ?>
    </div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
