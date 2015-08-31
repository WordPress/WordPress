<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
		<?php echo sprintf(__('Database caching via %s is currently %s.', 'w3-total-cache'), w3_get_engine_name($this->_config->get_string('dbcache.engine')) ,'<span class="w3tc-' . ($dbcache_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>'); ?>
    </p>
    <p>
        <?php _e('To rebuild the database cache use the', 'w3-total-cache') ?>
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_dbcache" value="<?php _e('empty cache', 'w3-total-cache'); ?>"<?php if (! $dbcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
			<?php _e('operation.', 'w3-total-cache'); ?>
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('General', 'w3-total-cache'), '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th>
                    <?php $this->checkbox('dbcache.reject.logged') ?> <?php w3_e_config_label('dbcache.reject.logged') ?></label>
                    <br /><span class="description"><?php _e('Enabling this option is recommended to maintain default WordPress behavior.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Advanced', 'w3-total-cache'), '', 'advanced'); ?>
        <table class="form-table">
            <?php if ($this->_config->get_string('dbcache.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers"><?php w3_e_config_label('dbcache.memcached.servers') ?></label></th>
                <td>
                    <input id="memcached_servers" type="text"
                        <?php $this->sealing_disabled('dbcache') ?> name="dbcache.memcached.servers" value="<?php echo esc_attr(implode(',', $this->_config->get_array('dbcache.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Test', 'w3-total-cache'); ?>" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description"><?php _e('Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="width: 250px;"><label for="dbcache_lifetime"><?php w3_e_config_label('dbcache.lifetime') ?></label></th>
                <td>
                    <input id="dbcache_lifetime" type="text" name="dbcache.lifetime"
                        <?php $this->sealing_disabled('dbcache') ?>
                        value="<?php echo esc_attr($this->_config->get_integer('dbcache.lifetime')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                    <br /><span class="description"><?php _e('Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_file_gc"><?php w3_e_config_label('dbcache.file.gc') ?></label></th>
                <td>
                    <input id="dbcache_file_gc" type="text" name="dbcache.file.gc"
					<?php $this->sealing_disabled('dbcache') ?> value="<?php echo esc_attr($this->_config->get_integer('dbcache.file.gc')); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache'); ?>
                    <br /><span class="description"><?php _e('If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_uri"><?php w3_e_config_label('dbcache.reject.uri') ?></label></th>
                <td>
                    <textarea id="dbcache_reject_uri" name="dbcache.reject.uri"
                        <?php $this->sealing_disabled('dbcache') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('dbcache.reject.uri'))); ?></textarea><br />
						<span class="description">
							<?php echo sprintf( __('Always ignore the specified pages / directories. Supports regular expressions (See <a href="%s">FAQ</a>).', 'w3-total-cache'), network_admin_url('admin.php?page=w3tc_faq#q82') ); ?>
						</span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_sql"><?php w3_e_config_label('dbcache.reject.sql') ?></label></th>
                <td>
                    <textarea id="dbcache_reject_sql" name="dbcache.reject.sql"
                        <?php $this->sealing_disabled('dbcache') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('dbcache.reject.sql'))); ?></textarea><br />
                    <span class="description"><?php _e('Do not cache queries that contain these terms. Any entered prefix (set in wp-config.php) will be replaced with current database prefix (default: wp_). Query stems can be identified using debug mode.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="dbcache_reject_words"><?php w3_e_config_label('dbcache.reject.words') ?></label></th>
                <td>
                    <textarea id="dbcache_reject_words" name="dbcache.reject.words"
                        <?php $this->sealing_disabled('dbcache') ?> cols="40" rows="5"><?php echo esc_textarea(implode("\r\n", $this->_config->get_array('dbcache.reject.words'))); ?></textarea><br />
                    <span class="description"><?php _e('Do not cache queries that contain these words or regular expressions.', 'w3-total-cache'); ?></span>
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
