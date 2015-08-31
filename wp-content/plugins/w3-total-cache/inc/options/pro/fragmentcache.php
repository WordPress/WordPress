<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
		<?php echo sprintf(__('Fragment caching via %s is currently %s.', 'w3-total-cache'), w3_get_engine_name($this->_config->get_string('fragmentcache.engine')) ,'<span class="w3tc-' . ($fragmentcache_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>'); ?>
    </p>
    <p>
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_fragmentcache" value="<?php _e('Empty the entire cache', 'w3-total-cache') ?>"<?php if (! $fragmentcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        <?php _e('if needed.', 'w3-total-cache') ?>
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('Overview', 'w3-total-cache'), '', 'overview'); ?>
        <table class="form-table">
        <tr>
            <th><?php _e('Registered fragment groups:', 'w3-total-cache'); ?></th>
            <td>
                <?php if ($registered_groups): ?>
                <ul>
                    <?php 
                    foreach ($registered_groups as $group => $descriptor)
                        echo '<li>', $group,
                            ' (', $descriptor['expiration'], ' secs): ',
                            implode(',', $descriptor['actions']), '</li>';
                    ?>
                </ul>
                <span class="description"><?php _e('The groups above will be flushed upon setting changes.', 'w3-total-cache'); ?></span>
                <?php else: ?>
                <span class="description"><?php _e('No groups have been registered.', 'w3-total-cache'); ?></span>
                <?php endif ?>
            </td>
        </tr>
        <?php if (w3_is_network()): ?>
            <tr>
                <th><?php _e('Registered site-wide fragment groups:', 'w3-total-cache'); ?></th>
                <td>
                    <?php if ($registered_global_groups): ?>
                    <ul>
                        <?php 
                        foreach ($registered_global_groups as $group => $descriptor)
                            echo '<li>', $group,
                                ' (', $descriptor['expiration'], ' secs): ',
                                implode(',', $descriptor['actions']), '</li>';
                        ?>
                    </ul>
                    <span class="description"><?php _e('The site-wide groups above will be purged upon setting changes.', 'w3-total-cache'); ?></span>
                    <?php else: ?>
                        <span class="description"><?php _e('No site-wide groups have been registered.', 'w3-total-cache'); ?></span>
                    <?php endif ?>
                </td>
            </tr>
        <?php endif ?>
        </table>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Advanced', 'w3-total-cache'), '', 'advanced'); ?>
        <table class="form-table">
            <?php if ($this->_config->get_string('fragmentcache.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers"><?php w3_e_config_label('fragmentcache.memcached.servers') ?></label></th>
                <td>
                    <input id="memcached_servers" type="text"
                        <?php $this->sealing_disabled('fragmentcache') ?> name="fragmentcache.memcached.servers" value="<?php echo esc_attr(implode(',', $this->_config->get_array('fragmentcache.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test', 'w3-total-cache'); ?>" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description"><?php _e('Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="width: 250px;"><label for="fragmentcache_lifetime"><?php w3_e_config_label('fragmentcache.lifetime') ?></label></th>
                <td>
                    <input id="fragmentcache_lifetime" type="text" <?php $this->sealing_disabled('fragmentcache') ?> name="fragmentcache.lifetime" value="<?php echo esc_attr($this->_config->get_integer('fragmentcache.lifetime')) ?>" size="8" /><?php _e('seconds', 'w3-total-cache') ?>
                    <br /><span class="description"><?php _e('Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="fragmentcache_file_gc"><?php w3_e_config_label('fragmentcache.file.gc')?></label></th>
                <td>
                    <input id="fragmentcache_file_gc" type="text" <?php $this->sealing_disabled('fragmentcache') ?> name="fragmentcache.file.gc" value="<?php echo esc_attr($this->_config->get_integer('fragmentcache.file.gc')) ?>" size="8" /> <?php _e('seconds', 'w3-total-cache') ?>
                    <br /><span class="description"><?php _e('If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="fragmentcache_groups"><?php w3_e_config_label('fragmentcache.groups') ?></label></th>
                <td>
                    <textarea id="fragmentcache_groups" name="fragmentcache.groups"
                        <?php $this->sealing_disabled('fragmentcache') ?>
                              cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('fragmentcache.groups'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify fragment groups that should be managed by W3 Total Cache. Enter one action per line comma delimited, e.g. (group, action1, action2). Include the prefix used for a transient by a theme or plugin.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
