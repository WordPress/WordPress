<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
    	<?php echo 
    		sprintf( __('Object caching via %1$s is currently %2$s', 'w3-total-cache'), 
    		'<strong>'.w3_get_engine_name($this->_config->get_string('objectcache.engine')).'</strong>', 
    		'<span class="w3tc-'.($objectcache_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>'
    		); 
    	?>
    </p>
    <p>
        To rebuild the object cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_objectcache" value="<?php _e('empty cache', 'w3-total-cache'); ?>"<?php if (! $objectcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        operation.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('Advanced', 'w3-total-cache'), '', 'advanced'); ?>
        <table class="form-table">
            <?php if ($this->_config->get_string('objectcache.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers"><?php w3_e_config_label('objectcache.memcached.servers') ?></label></th>
                <td>
                    <input id="memcached_servers" type="text"
                        <?php $this->sealing_disabled('objectcache') ?> name="objectcache.memcached.servers" value="<?php echo esc_attr(implode(',', $this->_config->get_array('objectcache.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test', 'w3-total-cache'); ?>" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description"><?php _e('Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="width: 250px;"><label for="objectcache_lifetime"><?php w3_e_config_label('objectcache.lifetime') ?></label></th>
                <td>
                    <input id="objectcache_lifetime" type="text"
                        <?php $this->sealing_disabled('objectcache') ?> name="objectcache.lifetime" value="<?php echo esc_attr($this->_config->get_integer('objectcache.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                    <br /><span class="description"><?php _e('Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="objectcache_file_gc"><?php w3_e_config_label('objectcache.file.gc') ?></label></th>
                <td>
                    <input id="objectcache_file_gc" type="text"
                        <?php $this->sealing_disabled('objectcache') ?> name="objectcache.file.gc" value="<?php echo esc_attr( $this->_config->get_integer('objectcache.file.gc')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                    <br /><span class="description"><?php _e('If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="objectcache_groups_global"><?php w3_e_config_label('objectcache.groups.global') ?></label></th>
                <td>
                    <textarea id="objectcache_groups_global"
                        <?php $this->sealing_disabled('objectcache') ?> name="objectcache.groups.global" cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('objectcache.groups.global'))); ?></textarea>
                    <br /><span class="description"><?php _e('Groups shared amongst sites in network mode.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="objectcache_groups_nonpersistent"><?php w3_e_config_label('objectcache.groups.nonpersistent') ?></label></th>
                <td>
                    <textarea id="objectcache_groups_nonpersistent"
                        <?php $this->sealing_disabled('objectcache') ?> name="objectcache.groups.nonpersistent" cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('objectcache.groups.nonpersistent'))); ?></textarea>
                    <br /><span class="description"><?php _e('Groups that should not be cached.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($this->_config->get_boolean('cluster.messagebus.enabled')): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('objectcache.purge.all') ?> <?php w3_e_config_label('objectcache.purge.all') ?></label>
                    <br /><span class="description"><?php _e('Enabling this option will increase load on server on certain actions but will guarantee that
                    the Object Cache is always clean and contains latest changes. <em>Enable if you are experiencing issues
                     with options displaying wrong value/state (checkboxes etc).</em>', 'w3-total-cache')?></span>
                </th>
            </tr>
            <?php endif ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>